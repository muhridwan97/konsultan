<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class StorageUsageModel
 */
class StorageUsageModel extends MY_Model
{
    protected $table = 'storage_usages';

    const STATUS_PENDING = 'PENDING';
    const STATUS_PROCEED = 'PROCEED';

    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery($branchId)
            ->select([
                'COUNT(storage_usage_customers.id) AS total_customer_usage',
                'SUM(IF(storage_usage_customers.status != "PENDING", 1, 0)) AS total_proceed',
                'SUM(IF(storage_usage_customers.status = "PENDING", 1, 0)) AS total_pending',
                'SUM(IF(storage_usage_customers.status = "VALIDATED", 1, 0)) AS total_validated',
                'SUM(IF(storage_usage_customers.status = "SKIPPED", 1, 0)) AS total_skipped',
            ])
            ->join('storage_usage_customers', 'storage_usage_customers.id_storage_usage = storage_usages.id', 'left')
            ->group_by('storage_usages.id');
    }

    /**
     * Get all storage usage with or without deleted records.
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

        if (!$withTrashed) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if (!empty($search)) {
            $baseQuery
                ->group_start()
                ->like('storage_usages.date', $search)
                ->or_like('storage_usages.description', $search)
                ->or_like('storage_usages.status', $search)
                ->group_end();
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
     * Check if over space validation is confirm and not more then limit: 09:00
     *
     * @return bool
     */
    public function isOverSpaceValidationCompleted()
    {
        $outstandingStorageUsage = $this->db->from($this->table)->where([
            $this->table . '.id_branch' => get_active_branch_id(),
            $this->table . '.date<=' => date('Y-m-d'),
            $this->table . '.status!=' => StorageUsageModel::STATUS_PROCEED,
            $this->table . '.is_deleted' => false,
        ])
            ->order_by('date', 'desc')
            ->get()
            ->row_array();

        if (empty($outstandingStorageUsage)) {
            return true;
        }

        return date('Y-m-d H:i') < (date('Y-m-d') . ' 09:00');
    }
}
