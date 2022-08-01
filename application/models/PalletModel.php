<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PalletModel extends MY_Model
{
    protected $table = 'pallets';

    /**
     * Get active record query builder for all related position data selection.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $pallets = $this->db->select([
            'pallets.*',
            'bookings.no_booking',
            'bookings.no_reference',
            'ref_people.name AS customer_name',
        ])
            ->from($this->table)
            ->join('bookings', 'bookings.id = pallets.id_booking', 'left')
            ->join('ref_people', 'ref_people.id = bookings.id_customer', 'left');

        if (!empty($branchId)) {
            $pallets->where('pallets.id_branch', $branchId);
        }

        return $pallets;
    }

    /**
     * Get all pallets with or without deleted records.
     *
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
            0 => "pallets.id",
            1 => "pallets.no_pallet",
            2 => "pallets.batch",
            3 => "pallets.description",
            4 => "bookings.no_booking",
            5 => "pallets.created_at",
            6 => "pallets.id",
        ];
        $columnSort = $columnOrder[$column];

        $branchId = get_active_branch('id');

        $this->db->start_cache();
        $pallets = $this->getBaseQuery($branchId)
            ->group_start()
            ->like('pallets.no_pallet', $search)
            ->or_like('pallets.batch', $search)
            ->or_like('pallets.description', $search)
            ->or_like('pallets.created_at', $search)
            ->or_like('bookings.no_booking', $search)
            ->or_like('bookings.no_reference', $search)
            ->group_end();

        if (!$withTrashed) {
            $pallets->where($this->table . '.is_deleted', false);
        }

        $this->db->stop_cache();

        if ($getAllData) {
            $allData = $pallets->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        $page = $pallets->order_by($columnSort, $sort)->limit($length, $start);
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
     * Get auto number for pallet.
     *
     * @return string
     */
    public function getAutoNumberPallet()
    {
        $orderData = $this->db->query("
            SELECT IFNULL(CAST(RIGHT(no_pallet, 6) AS UNSIGNED), 0) + 1 AS order_number 
            FROM pallets 
            WHERE SUBSTR(no_pallet, 7, 2) = MONTH(NOW()) 
			AND SUBSTR(no_pallet, 4, 2) = DATE_FORMAT(NOW(), '%y')
            ORDER BY SUBSTR(no_pallet FROM 4) DESC LIMIT 1
        ");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return 'PL/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }

    /**
     * Get next batch number.
     *
     * @return int
     */
    public function getNextBatch()
    {
        $nextBatch = $this->db->select('(IFNULL(MAX(batch), 0) + 1) AS batch')
            ->from($this->table)
            ->order_by('batch', 'desc')
            ->get()->row_array();

        if ($nextBatch) {
            return $nextBatch['batch'];
        }

        return 1;
    }

}