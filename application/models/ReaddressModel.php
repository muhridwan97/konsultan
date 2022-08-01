<?php

class ReaddressModel extends MY_Model
{
    protected $table = 'readdress_histories';

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

        $readdresses = $this->db->select([
            'readdress_histories.*',
            'bookings.no_booking',
            'bookings.no_reference',
            'customer_from.name AS customer_from',
            'customer_to.name AS customer_to',
            'validators.username AS validator_name',
        ])
            ->from($this->table)
            ->join('bookings', 'bookings.id = readdress_histories.id_booking', 'left')
            ->join('ref_people AS customer_from', 'customer_from.id = readdress_histories.id_customer_from', 'left')
            ->join('ref_people AS customer_to', 'customer_to.id = readdress_histories.id_customer_to', 'left')
            ->join('prv_users AS validators', 'validators.id = readdress_histories.validated_by', 'left');

        if (!empty($branchId)) {
            $readdresses->where('bookings.id_branch', $branchId);
        }

        return $readdresses;
    }

    /**
     * Get all readdress with or without deleted records.
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
            0 => "readdress_histories.id",
            1 => "bookings.no_booking",
            2 => "bookings.no_reference",
            3 => "customer_from.name",
            4 => "customer_to.name",
            5 => "readdress_histories.created_at",
            6 => "readdress_histories.status",
            7 => "readdress_histories.id",
        ];
        $columnSort = $columnOrder[$column];

        $branchId = get_active_branch('id');

        $this->db->start_cache();
        $readresses = $this->getBaseQuery($branchId)
            ->group_start()
            ->like('bookings.no_booking', $search)
            ->or_like('bookings.no_reference', $search)
            ->or_like('customer_from.name', $search)
            ->or_like('customer_to.name', $search)
            ->or_like('readdress_histories.created_at', $search)
            ->or_like('readdress_histories.status', $search)
            ->or_like('readdress_histories.description', $search)
            ->group_end();

        if (!$withTrashed) {
            $readresses->where($this->table . '.is_deleted', false);
        }
        $this->db->stop_cache();

        if ($getAllData) {
            $allData = $readresses->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        $page = $readresses->order_by($columnSort, $sort)->limit($length, $start);
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