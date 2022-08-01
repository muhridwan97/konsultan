<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class TransporterEntryPermitTrackingModel
 */
class TransporterEntryPermitTrackingModel extends MY_Model
{
    protected $table = 'transporter_entry_permit_trackings';

    const STATUS_LINKED = 'LINKED';
    const STATUS_SITE_TRANSIT = 'SITE TRANSIT';
    const STATUS_UNLOADED = 'UNLOADED';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        if (empty(env('DB_PHBID_DATABASE'))) {
            show_error("PHBID configuration is not set, you cannot access this feature.");
        }

        $baseQuery = parent::getBaseQuery()
            ->select([
                'transporter_entry_permits.tep_code',
                'transporter_entry_permits.checked_in_at',
                'transporter_entry_permits.checked_out_at',
                'order_containers.nomor_seal',
                'order_containers.tanggal_ambil_kontainer',
                'order_containers.tanggal_stuffing',
                'order_containers.tanggal_dooring',
                'order_containers.tanggal_kontainer_kembali_kedepo',
                'prv_users.name AS creator_name'
            ])
            ->join('transporter_entry_permits', 'transporter_entry_permits.id = transporter_entry_permit_trackings.id_tep', 'left')
            ->join(PhBidOrderContainerModel::$tableOrderContainer, 'order_containers.id = transporter_entry_permit_trackings.id_phbid_tracking', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = transporter_entry_permit_trackings.created_by', 'left');

        if (!empty($branchId)) {
            $baseQuery->where('transporter_entry_permits.id_branch', $branchId);
        }

        return $baseQuery;
    }

    /**
     * Get base query tep tracking.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQueryTracking($branchId = null)
    {
        $baseQuery = $this->db
            ->select([
                'transporter_entry_permit_trackings.id',
                'transporter_entry_permit_trackings.id_phbid_tracking',
                'transporter_entry_permit_trackings.phbid_no_vehicle',
                'transporter_entry_permit_trackings.phbid_no_order',
                'transporter_entry_permit_trackings.site_transit_actual_date',
                'transporter_entry_permit_trackings.unloading_actual_date',
                'IFNULL(transporter_entry_permit_trackings.status, "NOT LINKED") AS status',
                'transporter_entry_permits.id AS id_transporter_entry_permit',
                'transporter_entry_permits.tep_code',
                'transporter_entry_permits.checked_in_at',
                'transporter_entry_permits.checked_out_at',
                'transporter_entry_permits.receiver_no_police',
                'prv_users.name AS creator_name'
            ])
            ->from('transporter_entry_permits')
            ->join($this->table, 'transporter_entry_permit_trackings.id_tep = transporter_entry_permits.id', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = transporter_entry_permit_trackings.created_by', 'left')
            ->where([
                'transporter_entry_permits.tep_category' => 'OUTBOUND',
                'transporter_entry_permits.checked_in_at IS NOT NULL' => null,
                'transporter_entry_permits.created_at >=' => '2021-09-20',
            ]);

        if (!empty($branchId)) {
            $baseQuery->where('transporter_entry_permits.id_branch', $branchId);
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
        $baseQuery = $this->getBaseQueryTracking($branchId);

        if (!empty($search)) {
            $baseQuery
                ->group_start()
                ->like('transporter_entry_permits.tep_code', $search)
                ->or_like('transporter_entry_permits.receiver_no_police', $search)
                ->or_like('transporter_entry_permit_trackings.status', $search)
                ->or_like('transporter_entry_permit_trackings.description', $search)
                ->or_like('transporter_entry_permit_trackings.phbid_no_order', $search)
                ->or_like('transporter_entry_permit_trackings.phbid_no_vehicle', $search)
                ->group_end();
        }

        if (key_exists('status', $filters) && !empty($filters['status']) && $filters['status'] != 'ALL') {
            if ($filters['status'] == 'NOT LINKED') {
                $baseQuery->where('transporter_entry_permit_trackings.id IS NULL');
            } elseif ($filters['status'] == 'LINKED DATA') {
                $baseQuery->where('transporter_entry_permit_trackings.status IS NOT NULL');
            } else {
                $baseQuery->where('transporter_entry_permit_trackings.status', $filters['status']);
            }
        }

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $baseQuery->where('DATE(transporter_entry_permits.created_at)>=', format_date($filters['date_from']));
        }

        if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
            $baseQuery->where('DATE(transporter_entry_permits.created_at)<=', format_date($filters['date_to']));
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        if ($column == 'no') $column = $this->table . '.id';
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
