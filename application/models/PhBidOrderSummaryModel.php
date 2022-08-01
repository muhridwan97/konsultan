<?php

class PhBidOrderSummaryModel extends MY_Model
{
    protected $table = 'order_summaries';
    public static $tableOrder = 'order_summaries';

    const RESERVED_AUCTION = [
        '01.OUT/T/PL-TCI MDN/VI/2021',
        '02.OUT/T/PL-TCI MDN/VI/2021',
        '10.BI/SLIDING BED /PL.KIM-PAD C/VII/2021',
        '01.OUT/T/PL-TCI MDN/2022',
        '02.OUT/T/PL-TCI MDN/2022',
    ];

    public function __construct()
    {
        parent::__construct();

        $this->table = env('DB_PHBID_DATABASE') . '.order_summaries';
        self::$tableOrder = env('DB_PHBID_DATABASE') . '.order_summaries';

        $this->load->model('PhBidOrderContainerModel');
    }

    /**
     * Get outbound progress data.
     *
     * @param array $filters
     * @return array
     */
    public function getOutboundProgressData($filters = [])
    {
        $containers = $this->getOrderContainerStats($filters)->get()->result_array();

        $data = [];
        foreach ($containers as $container) {
            $data['outstanding_order'][$container['vehicle']] = $container['outstanding_order'];
            $data['waiting_stuffing'][$container['vehicle']] = $container['waiting_stuffing'] - $container['waiting_stuffing_tep_checked_in'] - $container['waiting_stuffing_tep_checked_out'];
            $data['waiting_stuffing_tep_checked_in'][$container['vehicle']] = $container['waiting_stuffing_tep_checked_in'];
            $data['waiting_stuffing_tep_checked_out_yesterday'][$container['vehicle']] = $container['waiting_stuffing_tep_checked_out_yesterday'];
            $data['waiting_stuffing_tep_checked_out'][$container['vehicle']] = $container['waiting_stuffing_tep_checked_out'];
            $data['rm_kolam'][$container['vehicle']] = $container['rm_kolam'];
            $data['site_transit'][$container['vehicle']] = $container['site_transit'];
            $data['site_unloaded'][$container['vehicle']] = $container['site_unloaded'];
        }
        return $data;
    }

    /**
     * Get outbound progress data.
     *
     * @param array $filters
     * @return array
     */
    public function getOutboundProgressTotal($filters = [])
    {
        $orderContainerQuery = $this->getOrderContainerStats($filters)->get_compiled_select();

        $baseQuery = $this->db
            ->select([
                "SUM(outstanding_order) AS outstanding_order",
                "SUM(waiting_stuffing) AS waiting_stuffing",
                "SUM(waiting_stuffing_tep_checked_in) AS waiting_stuffing_tep_checked_in",
                "SUM(waiting_stuffing_tep_checked_out_yesterday) AS waiting_stuffing_tep_checked_out_yesterday",
                "SUM(waiting_stuffing_tep_checked_out) AS waiting_stuffing_tep_checked_out",
                "SUM(rm_kolam) AS rm_kolam",
                "SUM(site_transit) AS site_transit",
                "SUM(site_unloaded) AS site_unloaded",
            ])
            ->from("({$orderContainerQuery}) AS summary");

        return $baseQuery->get()->row_array();
    }

    /**
     * Get outbound progress stats summary.
     *
     * @param array $filters
     * @return array|null
     */
    public function getOutboundProgressStats($filters = [])
    {
        $orderContainerQuery = $this->getOrderContainerStats($filters)->get_compiled_select();

        $baseQuery = $this->db
            ->select([
                "GROUP_CONCAT(IF(outstanding_order > 0, CONCAT(vehicle, ' ', outstanding_order), NULL) SEPARATOR ' & ') AS outstanding_order",
                "GROUP_CONCAT(IF(waiting_stuffing > 0, CONCAT(vehicle, ' ', waiting_stuffing), NULL) SEPARATOR ' & ') AS waiting_stuffing",
                "GROUP_CONCAT(IF(waiting_stuffing_tep_checked_in > 0, CONCAT(vehicle, ' ', waiting_stuffing_tep_checked_in), NULL) SEPARATOR ' & ') AS waiting_stuffing_tep_checked_in",
                "GROUP_CONCAT(IF(waiting_stuffing_tep_checked_out_yesterday > 0, CONCAT(vehicle, ' ', waiting_stuffing_tep_checked_out_yesterday), NULL) SEPARATOR ' & ') AS waiting_stuffing_tep_checked_out_yesterday",
                "GROUP_CONCAT(IF(waiting_stuffing_tep_checked_out > 0, CONCAT(vehicle, ' ', waiting_stuffing_tep_checked_out), NULL) SEPARATOR ' & ') AS waiting_stuffing_tep_checked_out",
                "GROUP_CONCAT(IF(rm_kolam > 0, CONCAT(vehicle, ' ', rm_kolam), NULL) SEPARATOR ' & ') AS rm_kolam",
                "GROUP_CONCAT(IF(site_transit > 0, CONCAT(vehicle, ' ', site_transit), NULL) SEPARATOR ' & ') AS site_transit",
                "GROUP_CONCAT(IF(site_unloaded > 0, CONCAT(vehicle, ' ', site_unloaded), NULL) SEPARATOR ' & ') AS site_unloaded"
            ])
            ->from("({$orderContainerQuery}) AS summary");

        return $baseQuery->get()->row_array();
    }

    /**
     * Get base query order container stats
     * @param array $filters
     * @return CI_DB_mysql_driver|CI_DB_query_builder
     */
    public function getOrderContainerStats($filters = [])
    {
        $orderQuery = $this->getOrderContainerTransactions($filters)->get_compiled_select();

        return $this->db
            ->select([
                'vehicle',
                'SUM(IF(tanggal_ambil_kontainer IS NULL, 1, 0)) AS outstanding_order',
                'SUM(
                    IF(
                        tanggal_ambil_kontainer IS NOT NULL 
                        AND tanggal_stuffing IS NULL
                        AND checked_in_at IS NULL, 
                    1, 0)
                ) AS waiting_stuffing',
                'SUM(
                    IF(
                        tanggal_ambil_kontainer IS NOT NULL 
                        AND tanggal_stuffing IS NULL 
                        AND tep_code IS NOT NULL 
                        AND checked_in_at IS NOT NULL 
                        AND checked_out_at IS NULL, 
                    1, 0)
                ) AS waiting_stuffing_tep_checked_in',
                'SUM(
                    IF(
                        tanggal_ambil_kontainer IS NOT NULL 
                        AND tanggal_stuffing IS NULL 
                        AND tep_code IS NOT NULL 
                        AND checked_in_at IS NOT NULL 
                        AND checked_out_at IS NOT NULL
                        AND checked_out_at >= CONCAT(DATE_ADD(CURDATE(), INTERVAL -1 DAY), " 07:01:00")
                        AND checked_out_at <= CONCAT(CURDATE(), " 07:00:00"),
                    1, 0)
                ) AS waiting_stuffing_tep_checked_out_yesterday',
                'SUM(
                    IF(
                        tanggal_ambil_kontainer IS NOT NULL 
                        AND tanggal_stuffing IS NULL 
                        AND tep_code IS NOT NULL 
                        AND checked_in_at IS NOT NULL 
                        AND checked_out_at IS NOT NULL
                        AND tanggal_dooring IS NULL, 
                    1, 0)
                ) AS waiting_stuffing_tep_checked_out', // "AND tanggal_dooring IS NULL" make if "rm kolam" not set but "site transit" filled then assume is already passed
                //'SUM(IF(tanggal_stuffing IS NOT NULL AND tanggal_dooring IS NULL, 1, 0)) AS rm_kolam',
                //'SUM(IF(tanggal_dooring IS NOT NULL AND tanggal_kontainer_kembali_kedepo IS NULL, 1, 0)) AS site_transit',
                //'SUM(IF(tanggal_kontainer_kembali_kedepo IS NOT NULL AND notified_at IS NULL, 1, 0)) AS site_unloaded'
                'SUM(IF(tanggal_stuffing IS NOT NULL AND tanggal_dooring IS NULL, 1, 0)) AS rm_kolam',
                'SUM(IF(tanggal_dooring IS NOT NULL AND tanggal_kontainer_kembali_kedepo IS NULL, 1, 0)) AS site_transit',
                'SUM(IF(tanggal_kontainer_kembali_kedepo IS NOT NULL AND notified_at IS NULL, 1, 0)) AS site_unloaded'
            ])
            ->from("({$orderQuery}) AS orders")
            ->group_by('vehicle');
    }

    /**
     * Get order detail container.
     *
     * @param array $filters
     * @return array|CI_DB_mysql_driver|CI_DB_query_builder|null
     */
    public function getOrderContainerTransactions($filters = [])
    {
        if (empty(env('DB_PHBID_DATABASE'))) {
            show_error("PHBID configuration is not set, you cannot access this feature.");
        }

        $baseQuery = $this->db
            ->select([
                'order_containers.id',
                'order_containers.id_reference',
                'order_summaries.nomor_lelang',
                'order_summaries.nomor_order',
                'order_summaries.tanggal_order',
                "UPPER(REPLACE(REPLACE(REPLACE(REPLACE(REPLACE(unit, '0', ''), '1', ''), '2', ''), '4', ''), ' ', '')) AS vehicle",
                'order_containers.id_order_summary',
                'order_containers.nomor_kontainer',
                'order_containers.tanggal_ambil_kontainer',
                'order_containers.tanggal_stuffing',
                //'order_containers.tanggal_dooring',
                //'order_containers.tanggal_kontainer_kembali_kedepo',
                'transporter_entry_permit_trackings.site_transit_actual_date AS tanggal_dooring',
                'transporter_entry_permit_trackings.unloading_actual_date AS tanggal_kontainer_kembali_kedepo',
                'order_containers.notified_at',
                'transporter_entry_permits.id AS id_tep',
                'transporter_entry_permits.tep_code',
                'transporter_entry_permits.checked_in_at',
                'transporter_entry_permits.checked_out_at',
            ])
            ->from($this->table)
            ->join(PhBidOrderContainerModel::$tableOrderContainer, 'order_containers.id_order_summary = order_summaries.id', 'left')
            ->join('transporter_entry_permit_trackings', 'transporter_entry_permit_trackings.id_phbid_tracking = order_containers.id', 'left')
            ->join('transporter_entry_permits', 'transporter_entry_permits.id = transporter_entry_permit_trackings.id_tep', 'left')
            ->where([
                'order_summaries.status_order!=' => 'DITOLAK'
            ]);

        if (key_exists('auction_number', $filters) && !empty($filters['auction_number'])) {
            $baseQuery->where_in("order_summaries.nomor_lelang", $filters['auction_number']);
        } else {
            $baseQuery->where_in("order_summaries.nomor_lelang", self::RESERVED_AUCTION);
        }

        if (key_exists('order_date_from', $filters) && !empty($filters['order_date_from'])) {
            $baseQuery->where("order_summaries.tanggal_order>=", format_date($filters['order_date_from']));
        } else {
            $baseQuery->where("order_summaries.tanggal_order>=", '2021-09-19');
        }

        if (key_exists('status', $filters) && !empty($filters['status'])) {
            switch ($filters['status']) {
                case 'outstanding-order':
                    $baseQuery->where([
                        'tanggal_ambil_kontainer IS NULL' => null,
                    ]);
                    break;
                case 'waiting-stuffing':
                    $baseQuery->where([
                        'tanggal_ambil_kontainer IS NOT NULL' => null,
                        'tanggal_stuffing IS NULL' => null,
                        'checked_in_at IS NULL' => null, // exclude linked tep that already checked in
                    ]);
                    break;
                case 'waiting-stuffing-tep-checked-in':
                    $baseQuery->where([
                        'tanggal_ambil_kontainer IS NOT NULL' => null,
                        'tanggal_stuffing IS NULL' => null,
                        'tep_code IS NOT NULL' => null,
                        'checked_in_at IS NOT NULL' => null,
                        'checked_out_at IS NULL' => null,
                    ]);
                    break;
                case 'waiting-stuffing-tep-checked-out-yesterday':
                    $baseQuery->where([
                        'tanggal_ambil_kontainer IS NOT NULL' => null,
                        'tanggal_stuffing IS NULL' => null,
                        'tep_code IS NOT NULL' => null,
                        'checked_in_at IS NOT NULL' => null,
                        'checked_out_at IS NOT NULL' => null,
                        'checked_out_at >= CONCAT(DATE_ADD(CURDATE(), INTERVAL -1 DAY), " 07:01:00")' => null,
                        'checked_out_at <= CONCAT(CURDATE(), " 07:00:00")' => null,
                    ]);
                    break;
                case 'waiting-stuffing-tep-checked-out':
                    $baseQuery->where([
                        'tanggal_ambil_kontainer IS NOT NULL' => null,
                        'tanggal_stuffing IS NULL' => null,
                        'tep_code IS NOT NULL' => null,
                        'checked_in_at IS NOT NULL' => null,
                        'checked_out_at IS NOT NULL' => null,
                        'site_transit_actual_date IS NULL' => null
                    ]);
                    break;
                case 'rm-kolam':
                    $baseQuery->where([
                        'tanggal_stuffing IS NOT NULL' => null,
                        //'tanggal_dooring IS NULL' => null
                        'site_transit_actual_date IS NULL' => null
                    ]);
                    break;
                case 'site-transit':
                    $baseQuery->where([
                        //'tanggal_dooring IS NOT NULL' => null,
                        //'tanggal_kontainer_kembali_kedepo IS NULL' => null
                        'site_transit_actual_date IS NOT NULL' => null,
                        'unloading_actual_date IS NULL' => null,
                    ]);
                    break;
                case 'unloading':
                    $baseQuery->where([
                        //'tanggal_kontainer_kembali_kedepo IS NOT NULL' => null,
                        'unloading_actual_date IS NOT NULL' => null,
                        'notified_at IS NULL' => null
                    ]);
                    break;
            }
        }

        if (key_exists('no_plat_matching', $filters) && !empty($filters['no_plat_matching'])) {
            $baseQuery->where("TRIM(REPLACE(nomor_kontainer, ' ', '')) REGEXP '^" . $filters['no_plat_matching'] . "' = 1", null);
        }

        if (key_exists('checked_in_matching', $filters) && !empty($filters['checked_in_matching'])) {
            $baseQuery->where([
                "tanggal_order>=" => (new DateTime($filters['checked_in_matching']))->sub(new DateInterval('P2D'))->format('Y-m-d'),
                "tanggal_order<=" => format_date($filters['checked_in_matching'], 'Y-m-d'),
            ]);
        }

        if (key_exists('outstanding_tracking_link', $filters) && $filters['outstanding_tracking_link']) {
            $baseQuery
                ->where("NOT EXISTS(
                    SELECT id FROM transporter_entry_permit_trackings
                    WHERE id_phbid_tracking = order_containers.id
                )", null);
        }

        if (key_exists('id', $filters) && !empty($filters['id'])) {
            $baseQuery->where_in("order_containers.id", $filters['id']);

            return $baseQuery->get()->row_array();
        }

        return $baseQuery;
    }

    /**
     * Get all order container with paging.
     *
     * @param array $filters
     * @return mixed
     */
    public function getOrderContainerPaging($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;

        $this->db->start_cache();

        $baseQuery = $this->getOrderContainerTransactions($filters);

        if (!empty($search)) {
            $baseQuery
                ->group_start()
                ->like('tep_code', $search)
                ->or_like('nomor_order', $search)
                ->or_like('nomor_lelang', $search)
                ->or_like('nomor_kontainer', $search)
                ->or_like('unit', $search)
                ->group_end();
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        if ($column == 'no') $column = 'tanggal_ambil_kontainer';
        $page = $baseQuery->order_by($column, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        foreach ($data as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($data),
            "recordsFiltered" => $total,
            "data" => $data
        ];
        $this->db->flush_cache();

        return $pageData;
    }
}