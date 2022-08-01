<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ServiceHourModel extends MY_Model
{
    protected $table = 'ref_service_hours';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @param bool $viewAll
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null, $viewAll = false)
    {
        $branchConditions = '';
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }
        if (!empty($branchId)) {
            $branchConditions = 'AND ref_service_hours.id_branch = ' . $branchId;
        }

        $baseQuery = parent::getBaseQuery($branchId)
            ->select([
                'prv_users.name AS creator_name'
            ])
            ->join(UserModel::$tableUser, 'prv_users.id = ref_service_hours.created_by', 'left');

        if (!$viewAll) {
            $baseQuery->join("(
                SELECT service_day, MAX(effective_date) AS effective_date
                FROM ref_service_hours 
                WHERE effective_date <= CURDATE() AND is_deleted = false {$branchConditions}
                GROUP BY service_day
            ) AS latest_service_hours", 'latest_service_hours.service_day = ref_service_hours.service_day 
                AND latest_service_hours.effective_date = ref_service_hours.effective_date');
        }

        return $baseQuery;
    }

    /**
     * Get all service hour or without deleted records.
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

        $baseQuery = $this->getBaseQuery($branchId, get_if_exist($filters, 'view_all', false))
            ->group_start()
            ->like('ref_service_hours.service_day', $search)
            ->or_like('ref_service_hours.effective_date', $search)
            ->or_like('ref_service_hours.description', $search)
            ->group_end();

        if (!$withTrashed) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if (key_exists('related_service_day', $filters) && !empty($filters['related_service_day'])) {
            $baseQuery->where('ref_service_hours.service_day', $filters['related_service_day']);
            $baseQuery->where('ref_service_hours.id!=', $filters['related_service_except']);
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        if ($column == 'no') $column = 'ref_service_hours.id';
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