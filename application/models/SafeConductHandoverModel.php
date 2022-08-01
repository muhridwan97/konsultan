<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class SafeConductHandoverModel
 */
class SafeConductHandoverModel extends MY_Model
{
    protected $table = 'safe_conduct_handovers';

    const STATUS_PENDING = 'PENDING';
    const STATUS_RECEIVED = 'RECEIVED';
    const STATUS_HANDOVER = 'HANDOVER';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        $baseQuery = parent::getBaseQuery()
            ->select([
                'transporter_entry_permits.tep_code',
                'safe_conducts.no_safe_conduct',
                'safe_conducts.no_police',
                'safe_conducts.vehicle_type',
                'safe_conducts.id_safe_conduct_group',
                'safe_conduct_groups.no_safe_conduct_group',
                'bookings.id AS id_booking',
                'bookings.no_booking',
                'bookings.no_reference',
                'transporter_entry_permit_trackings.id AS id_tep_tracking',
                'transporter_entry_permit_trackings.site_transit_actual_date',
                'transporter_entry_permit_trackings.unloading_actual_date',
                'transporter_entry_permits.id AS id_tep',
                'prv_users.name AS creator_name'
            ])
            ->join('safe_conducts', 'safe_conducts.id = safe_conduct_handovers.id_safe_conduct', 'left')
            ->join('safe_conduct_groups', 'safe_conduct_groups.id = safe_conducts.id_safe_conduct_group', 'left')
            ->join('bookings', 'bookings.id = safe_conducts.id_booking', 'left')
            ->join('transporter_entry_permits', 'transporter_entry_permits.id = safe_conducts.id_transporter_entry_permit', 'left')
            ->join('transporter_entry_permit_trackings', 'transporter_entry_permit_trackings.id_tep = transporter_entry_permits.id', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = transporter_entry_permit_trackings.created_by', 'left');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        return $baseQuery;
    }

    /**
     * Get base query tep tracking.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQueryHandover($branchId = null)
    {
        $this->load->model('TransporterEntryPermitTrackingModel');

        $baseQuery = $this->db
            ->select([
                'safe_conduct_handovers.id',
                'safe_conduct_handovers.received_date',
                'safe_conduct_handovers.driver_handover_date',
                'safe_conducts.id AS id_safe_conduct', // if safe conduct group container MAX(id)
                'safe_conducts.id_safe_conduct_group',
                'safe_conducts.no_safe_conduct',
                'transporter_entry_permits.receiver_no_police AS no_police',
                'transporter_entry_permits.receiver_vehicle AS vehicle_type',
                'transporter_entry_permits.receiver_name AS driver',
                'safe_conducts.no_reference',
                'transporter_entry_permits.tep_code',
                'transporter_entry_permit_trackings.id AS id_tep_tracking',
                'transporter_entry_permit_trackings.phbid_no_vehicle',
                'transporter_entry_permit_trackings.phbid_no_order',
                'transporter_entry_permit_trackings.site_transit_actual_date',
                'transporter_entry_permit_trackings.unloading_actual_date',
                'IF(safe_conduct_handovers.id IS NULL, "' . self::STATUS_PENDING . '", safe_conduct_handovers.status) AS status',
            ])
            ->from("(
                SELECT  
                    bookings.id_branch,
                    GROUP_CONCAT(bookings.no_reference) AS no_reference,
                    safe_conducts.id_transporter_entry_permit,
                    MAX(safe_conducts.id) AS id,   
                    MAX(safe_conducts.id_safe_conduct_group) AS id_safe_conduct_group,   
                    IFNULL(no_safe_conduct_group, no_safe_conduct) AS no_safe_conduct
                FROM safe_conducts
                INNER JOIN bookings ON bookings.id = safe_conducts.id_booking
                LEFT JOIN safe_conduct_groups ON safe_conduct_groups.id = safe_conducts.id_safe_conduct_group
                WHERE safe_conducts.type = 'OUTBOUND'
                GROUP BY id_branch, id_transporter_entry_permit, IFNULL(no_safe_conduct_group, no_safe_conduct)
            ) AS safe_conducts")
            ->join('transporter_entry_permits', 'transporter_entry_permits.id = safe_conducts.id_transporter_entry_permit')
            ->join('transporter_entry_permit_trackings', 'transporter_entry_permit_trackings.id_tep = transporter_entry_permits.id')
            ->join('safe_conduct_handovers', 'safe_conduct_handovers.id_safe_conduct = safe_conducts.id', 'left')
            ->where(['transporter_entry_permit_trackings.status' => TransporterEntryPermitTrackingModel::STATUS_UNLOADED]);

        if (!empty($branchId)) {
            $baseQuery->where('safe_conducts.id_branch', $branchId);
        }

        return $baseQuery;
    }

    /**
     * Get all tep tracking with or without deleted records.
     *
     * @param array $filters
     * @param bool $withTrashed
     * @return mixed
     */
    public function getAll($filters = [], $withTrashed = false)
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $this->db->start_cache();

        //$baseQuery = $this->getBaseQuery($branchId);
        $baseQuery = $this->getBaseQueryHandover($branchId);

        if (!empty($search)) {
            $baseQuery
                ->group_start()
                ->like('safe_conducts.no_safe_conduct', $search)
                ->or_like('safe_conducts.no_police', $search)
                ->or_like('safe_conducts.driver', $search)
                ->or_like('safe_conducts.no_reference', $search)
                ->or_like('transporter_entry_permits.tep_code', $search)
                ->or_like('transporter_entry_permit_trackings.phbid_no_order', $search)
                ->or_like('transporter_entry_permit_trackings.phbid_no_vehicle', $search)
                ->group_end();
        }

        if (key_exists('status', $filters) && !empty($filters['status'])) {
            $baseQuery->having('status', $filters['status']);
        }

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $baseQuery->where('DATE(safe_conducts.created_at)>=', format_date($filters['date_from']));
        }

        if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
            $baseQuery->where('DATE(safe_conducts.created_at)<=', format_date($filters['date_to']));
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        //$total = $this->db->count_all_results();
        $finalQuery = $this->db->get_compiled_select();
        $total = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$finalQuery}) AS CI_count_all_results")->row_array()['numrows'];

        if ($column == 'no') $column = 'safe_conducts.id';
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
