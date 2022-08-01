<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class TransporterEntryPermitChassisModel
 */
class TransporterEntryPermitChassisModel extends MY_Model
{
    protected $table = 'transporter_entry_permit_chassis';

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
                'transporter_entry_permits.receiver_no_police',
                'work_orders.id AS id_work_order',
                'work_orders.no_work_order',
            ])
            ->join('transporter_entry_permits', 'transporter_entry_permits.id = transporter_entry_permit_chassis.id_tep')
            ->join('work_orders', 'work_orders.id_tep_chassis = transporter_entry_permit_chassis.id', 'left');

        if (!empty($branchId)) {
            $baseQuery->where('transporter_entry_permits.id_branch', $branchId);
        }

        return $baseQuery;
    }

    /**
     * Get all tep chassis.
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

        $baseQuery = $this->getBaseQuery($branchId);

        if (!empty($search)) {
            $baseQuery
                ->group_start()
                ->like('transporter_entry_permit_chassis.no_chassis', $search)
                ->or_like('transporter_entry_permits.tep_code', $search)
                ->group_end();
        }

        $dateType = 'transporter_entry_permit_chassis.created_at';
        if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
            $dateType = $filters['date_type'];
        }

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $baseQuery->where("DATE({$dateType})>=", format_date($filters['date_from']));
        }

        if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
            $baseQuery->where("DATE({$dateType})<=", format_date($filters['date_to']));
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

    /**
     * @param array $filters
     * @return array|array[]
     */
    public function getOutstandingChassis($filters = [])
    {
        if (empty($filters)) {
            return [];
        }
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $baseQuery = $this->getBaseQuery($branchId);

        if (key_exists('should_checked_in', $filters) && $filters['should_checked_in']) {
            $baseQuery->where('transporter_entry_permit_chassis.checked_in_at IS NOT NULL', null);
        }

        if (key_exists('outstanding_checked_out', $filters) && $filters['outstanding_checked_out']) {
            $baseQuery->where('transporter_entry_permit_chassis.checked_out_at IS NULL', null);
        }

        if (key_exists('id_except_chassis', $filters) && !empty($filters['id_except_chassis'])) {
            $baseQuery->or_where('transporter_entry_permit_chassis.id', $filters['id_except_chassis']);
        }

        if (key_exists('id', $filters) && $filters['id']) {
            $baseQuery->where('transporter_entry_permit_chassis.id', $filters['id']);
        }

        return $baseQuery->get()->result_array();
    }
}
