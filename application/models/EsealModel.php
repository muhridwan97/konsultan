<?php

use GuzzleHttp\Exception\GuzzleException;

defined('BASEPATH') OR exit('No direct script access allowed');

class EsealModel extends MY_Model
{
    protected $table = 'ref_eseals';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    public function getBaseQuery($branchId = null)
    {
        $branchId = get_active_branch_id();

        $baseQuery = parent::getBaseQuery()
            ->select([
                'ref_branches.branch',
                'ref_branches.address',
            ])
            ->join('ref_branches', 'ref_branches.id = ref_eseals.id_branch', 'left');

        if (!empty($branchId)) {
            $baseQuery
                ->group_start()
                ->where('ref_branches.id', $branchId)
                ->or_where('ref_branches.id IS NULL')
                ->group_end();
        }

        return $baseQuery;
    }

    /**
     * Get all eseal device
     * @param bool $unattached
     * @param null $exceptId
     * @return array
     */
    public function getAllDevices($unattached = false, $exceptId = null)
    {
        try {
            $client = new GuzzleHttp\Client([
                'base_uri' => env('ESEAL_API_URL'),
            ]);
            $response = $client->request('get', 'applications/' . env('ESEAL_APP_ID') . '/users', [
                'headers' => [
                    'Authorization' => env('ESEAL_API_TOKEN')
                ]
            ]);
            $users = json_decode($response->getBody(), true);
            $devices = [];
            $connectedEseals = $this->getBy(['(id_device IS NOT NULL OR id_device = "")']);
            foreach ($users as $index => $user) {
                if (empty($user['devices'])) {
                    unset($users[$index]);
                } else {
                    $isAttached = false;
                    if ($unattached) {
                        foreach ($connectedEseals as $eseal) {
                            if ($user['id'] == $eseal['id_device'] && $user['id'] != $exceptId) {
                                $isAttached = true;
                                break;
                            }
                        }
                    }

                    if (!$isAttached) {
                        $devices[] = [
                            'id' => $user['id'],
                            'name' => $user['name'],
                            'description' => get_if_exist($user, 'description'),
                            'deviceActivity' => $user['deviceActivity'],
                            'position' => $user['trackPoint']['position']
                        ];
                    }
                }
            }

            return $devices;
        } catch (GuzzleException $e) {
            show_error($e->getMessage(), 500);
        }
    }

    /**
     * Get device by id.
     *
     * @param $id
     * @return array
     */
    public function getDeviceById($id)
    {
        try {
            $client = new GuzzleHttp\Client([
                'base_uri' => env('ESEAL_API_URL'),
            ]);
            $response = $client->request('get', 'applications/' . env('ESEAL_APP_ID') . '/users/' . $id, [
                'query' => ['Identifier', 'UserId'],
                'headers' => [
                    'Authorization' => env('ESEAL_API_TOKEN')
                ]
            ]);
            $user = json_decode($response->getBody(), true);
            return [
                'id' => $user['id'],
                'name' => $user['name'],
                'description' => get_if_exist($user, 'description'),
                'deviceActivity' => $user['deviceActivity'],
                'position' => $user['trackPoint']['position']
            ];
        } catch (GuzzleException $e) {
            show_error($e->getMessage(), 500);
        }
    }

    /**
     * Get route data by device id.
     *
     * @param $deviceId
     * @param $date
     * @param array $filters
     * @return array
     */
    public function getDeviceTrackingData($deviceId, $date, $filters = [])
    {
        try {
            $client = new GuzzleHttp\Client([
                'base_uri' => env('ESEAL_API_URL'),
            ]);

            if(empty($deviceId) || empty($date)) {
                return [];
            }

            // populate condition or filters
            $isDistinct = get_if_exist($filters, 'distinct', false);
            $isLockStatus = key_exists('lock_status', $filters) ? $filters['lock_status'] : null;
            $fromTime = get_if_exist($filters, 'from_time');
            $toTime = get_if_exist($filters, 'to_time');
            $unit = 'M';

            // profile and variable
            $responseProfile = $client->request('get', 'applications/' . env('ESEAL_APP_ID') . '/users/' . $deviceId . '/status', [
                'headers' => ['Authorization' => env('ESEAL_API_TOKEN')]
            ]);
            $profile = json_decode($responseProfile->getBody(), true);

            // tracking routes
            $query = ['Date' => $date, 'FromIndex' => 0, 'PageSize' => 2000];
            if (!empty($fromTime) && !empty($toTime)) {
                $query['From'] = $fromTime;
                $query['Until'] = $toTime;
            }
            $response = $client->request('get', 'applications/' . env('ESEAL_APP_ID') . '/users/' . $deviceId . '/tracks', [
                'query' => $query,
                'headers' => ['Authorization' => env('ESEAL_API_TOKEN')]
            ]);
            $tracks = json_decode($response->getBody(), true);

            $lastDataLong = '';
            $lastDataLat = '';
            $trackHistories = [];
            $totalDistance = 0;

            // loop through the tracking data and filter by condition if exist
            foreach ($tracks as $index => $track) {
                $lat = $track['position']['latitude'];
                $long = $track['position']['longitude'];
                $distinctData = $isDistinct ? ($long != $lastDataLong || $lat  != $lastDataLat) : true;
                $lockedStatus = $isLockStatus == null ? true : ($track['variables']['lockStatus'] == $isLockStatus);
                if ($distinctData && $lockedStatus) {
                    $lastDataLong = $long;
                    $lastDataLat = $lat;

                    $fromLat = $tracks[($index > 0 ? $index : 1) - 1]['position']['latitude'];
                    $fromLong = $tracks[($index > 0 ? $index : 1) - 1]['position']['longitude'];
                    $toLat = $track['position']['latitude'];
                    $toLong = $track['position']['longitude'];
                    $distance = $this->calculateDistance($fromLat, $fromLong, $toLat, $toLong, $unit);

                    $trackData = [
                        'time' => date("Y-m-d H:i:s", strtotime($track['utc'])),
                        'position' => $track['position'],
                        'address' => $this->getRevertedGeocode($lat, $long),
                        'distance' => is_nan($distance) ? 0 : $distance,
                        'distance_unit' => $unit,
                        'velocity' => $track['velocity'],
                        'variables' => $track['variables'],
                    ];
                    $trackHistories[] = $trackData;

                    $totalDistance += $distance;
                }
            }

            return [
                'profile' => $profile,
                'tracking_distance' => $totalDistance,
                'tracking_distance_unit' => $unit,
                'tracking' => $trackHistories
            ];
        } catch (GuzzleException $e) {
            return null;
            //show_error($e->getMessage(), 500);
        }
    }

    /**
     * @param $lat
     * @param $long
     * @return
     * @throws GuzzleException
     */
    private function getRevertedGeocode($lat, $long)
    {
        $client = new GuzzleHttp\Client([
            'base_uri' => env('ESEAL_API_URL'),
        ]);
        $locationRequest = $client->request('get', 'applications/' . env('ESEAL_APP_ID') . '/reversegeocode', [
            'query' => [
                'lon' => $long,
                'lat' => $lat
            ],
            'headers' => [
                'Authorization' => env('ESEAL_API_TOKEN')
            ]
        ]);
        $location = json_decode($locationRequest->getBody(), true);
        return $location['location']['address'];
    }

    /**
     * Calculate distance from lat and long.
     *
     * @param $lat1 float from latitude
     * @param $lon1 float from longitude
     * @param $lat2 float to latitude
     * @param $lon2 float to longitude
     * @param $unit string reserved unit (Miles|Kilometers|Meters|Nautical Miles)
     * @return float|int
     */
    public function calculateDistance($lat1, $lon1, $lat2, $lon2, $unit = 'default')
    {
        if (($lat1 == $lat2) && ($lon1 == $lon2)) {
            return 0;
        } else {
            $theta = $lon1 - $lon2;
            $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
            $dist = acos($dist);
            $dist = rad2deg($dist);
            $miles = $dist * 60 * 1.1515;
            $unit = strtoupper($unit);

            if ($unit == "K") {
                return ($miles * 1.609344);
            } else if ($unit == "M") {
                return ($miles * 1.609344) * 1000;
            } else if ($unit == "N") {
                return ($miles * 0.8684);
            } else {
                return $miles;
            }
        }
    }
}
