<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SafeConductGroupModel extends MY_Model
{
    protected $table = 'safe_conduct_groups';

    /**
     * Get auto number for group.
     *
     * @return string
     */
    public function getAutoNumber()
    {
        $orderData = $this->db->query("
            SELECT CAST(RIGHT(no_safe_conduct_group, 6) AS UNSIGNED) + 1 AS order_number 
            FROM safe_conduct_groups 
            WHERE MONTH(created_at) = MONTH(NOW()) 
              AND YEAR(created_at) = YEAR(NOW())
            ORDER BY id DESC LIMIT 1
        ");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return 'SG/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch_id();
        }
        return parent::getBaseQuery($branchId)
            ->select([
                'COUNT(safe_conducts.id) AS total_safe_conduct',
                'GROUP_CONCAT(safe_conducts.no_safe_conduct) AS no_safe_conducts'
            ])
            ->join('safe_conducts', 'safe_conducts.id_safe_conduct_group = safe_conduct_groups.id')
            ->group_by('safe_conduct_groups.id');
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
            ->like('safe_conduct_groups.no_safe_conduct_group', $search)
            ->or_like('safe_conducts.no_safe_conduct', $search)
            ->group_end();

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        if ($column == 'no') $column = 'safe_conduct_groups.id';
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
