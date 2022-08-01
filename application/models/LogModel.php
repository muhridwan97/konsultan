<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class LogModel extends MY_Model
{
    protected $table = 'logs';

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
            'logs.*',
            'prv_users.name',
            'ref_branches.branch',
        ])
            ->from($this->table)
            ->join(UserModel::$tableUser, 'prv_users.id = logs.created_by', 'left')
            ->join('ref_branches', 'ref_branches.id = logs.id_branch', 'left');

        if (!empty($branchId)) {
            $pallets->where('logs.id_branch', $branchId);
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
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;

        $columnOrder = ["logs.id", "logs.type", "logs.data", "prv_users.name", "logs.created_at", "logs.id"];
        $columnSort = $columnOrder[$column];

        $branchId = get_active_branch('id');

        $this->db->start_cache();

        // manage base query
        $baseQuery = $this->getBaseQuery($branchId);

        // search field
        $baseQuery->group_start();
        foreach ($columnOrder as $field) {
            $baseQuery->or_like($field, trim($search));
        }
        $baseQuery->group_end();

        $this->db->stop_cache();

        // get all data if necessary
        if ($start < 0) {
            $allData = $baseQuery
                ->order_by('logs.id', 'desc')
                ->get()
                ->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        // count total result and get data
        $total = $this->db->count_all_results();
        $page = $baseQuery->order_by($columnSort, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        // append number field
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
     * Create log data.
     *
     * @param $data
     * @return bool
     */
    public function create($data)
    {
        $userId = UserModel::authenticatedUserData('id');
        $log = $this->db->from($this->table)
            ->where('created_by', $userId)
            ->order_by('id', 'desc')
            ->get()
            ->row_array();

        $createLog = false;
        if (!empty($log)) {
            $logData = json_decode($log['data']);
            $logType = json_decode($log['type']);
            $timeDiff = strtotime('now') - strtotime($log['created_at']);
            if ( ($logData->access != $data['data']['access']) || ($log['type'] == $data['type'] && $timeDiff > 300) || ($logData->access == $data['data']['access'] && $log['type'] != $data['type']) ) {
                $createLog = true;
            }
        } else {
            $createLog = true;
        }

        if ($createLog) {
            $data['data'] = json_encode($data['data']);
            return parent::create($data);
        }
        return true;
    }

}