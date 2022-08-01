<?php

class DangerReplacementModel extends MY_Model
{
    protected $table = 'danger_histories';

    const STATUS_PENDING = 'PENDING';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';

    /**
     * Get active record query builder for all related warehouse data selection.
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $dangerHistories = $this->db->select([
            'danger_histories.*',
            'bookings.no_booking',
            'bookings.no_reference',
            'validators.username AS validator_name',
        ])
            ->from($this->table)
            ->join('bookings', 'bookings.id = danger_histories.id_booking', 'left')
            ->join(UserModel::$tableUser . ' AS validators', 'validators.id = danger_histories.validated_by', 'left');

        if (!empty($branchId)) {
            $dangerHistories->where('bookings.id_branch', $branchId);
        }

        return $dangerHistories;
    }

    /**
     * Get all danger status with or without deleted records.
     * @param array $filters
     * @param bool $withTrashed
     * @return mixed
     */
    public function getAll($filters = [], $withTrashed = false)
    {
        $getAllData = empty($filters);
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;

        $columnOrder = [
            0 => "danger_histories.id",
            1 => "bookings.no_booking",
            2 => "bookings.no_reference",
            3 => "danger_histories.status_danger",
            4 => "danger_histories.created_at",
            5 => "danger_histories.status",
            6 => "danger_histories.id",
        ];
        $columnSort = $columnOrder[$column];

        $branchId = get_active_branch('id');

        $this->db->start_cache();
        $dangerHistories = $this->getBaseQuery($branchId)
            ->group_start()
            ->like('bookings.no_booking', $search)
            ->or_like('bookings.no_reference', $search)
            ->or_like('danger_histories.status_danger', $search)
            ->or_like('danger_histories.created_at', $search)
            ->or_like('danger_histories.status', $search)
            ->or_like('danger_histories.description', $search)
            ->group_end();

        if (!$withTrashed) {
            $dangerHistories->where($this->table . '.is_deleted', false);
        }
        $this->db->stop_cache();

        if ($getAllData) {
            $allData = $dangerHistories->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        $page = $dangerHistories->order_by($columnSort, $sort)->limit($length, $start);
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