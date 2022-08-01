<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DeliveryInspectionModel extends MY_Model
{
    protected $table = 'delivery_inspections';

    const STATUS_PENDING = 'PENDING';
    const STATUS_CONFIRMED = 'CONFIRMED';

    /**
     * DeliveryInspectionModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all delivery with or without deleted records.
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

        $baseQuery = $this->getBaseQuery($branchId)
            ->group_start()
            ->like('delivery_inspections.date', $search)
            ->or_like('delivery_inspections.description', $search)
            ->or_like('delivery_inspections.pic_tci', $search)
            ->or_like('delivery_inspections.pic_khaisan', $search)
            ->or_like('delivery_inspections.pic_smgp', $search)
            ->or_like('delivery_inspections.location', $search)
            ->group_end();

        if (!$withTrashed) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $baseQuery->where('DATE(delivery_inspections.date)>=', format_date($filters['date_from']));
        }

        if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
            $baseQuery->where('DATE(delivery_inspections.date)<=', format_date($filters['date_to']));
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        if ($column == 'no') $column = 'delivery_inspections.id';
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
