<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkOrderDocumentModel extends MY_Model
{
    protected $table = 'work_order_documents';

    const STATUS_PENDING = 'PENDING';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';

    const TYPE_JOB_SUMMARY = 'JOB SUMMARY';

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

        $baseQuery = $this->db->select([
            'work_order_documents.*',
            'IFNULL(files.total_files, 0) AS total_files',
            'validator.name AS validator_name',
            'creator.name AS creator_name',
            'IF(IFNULL(jobs.total_validated, 0) = IFNULL(jobs.total_job, 0), "VALIDATED", "UNVALIDATED") AS status_job_validate',
            'IFNULL(jobs.total_job, 0) AS total_job',
            'IFNULL(jobs.total_validated, 0) AS total_validated',
            'IFNULL(jobs.total_unvalidated, 0) AS total_unvalidated',

        ])
            ->from($this->table)
            ->join(UserModel::$tableUser . ' AS validator', 'validator.id = work_order_documents.validated_by', 'left')
            ->join(UserModel::$tableUser . ' AS creator', 'creator.id = work_order_documents.created_by', 'left')
            ->join('(
                SELECT id_work_order_document, COUNT(id) AS total_files
                FROM work_order_document_files
                GROUP BY id_work_order_document
            ) AS files', 'files.id_work_order_document = work_order_documents.id', 'left');

        $branchCondition = '';
        if (!empty($branchId)) {
            $baseQuery->where('work_order_documents.id_branch', $branchId);
            $branchCondition = ' AND bookings.id_branch = ' . $branchId;
        }

        $baseQuery->join("(
            SELECT 
                DATE(completed_at) AS job_date,
                COUNT(work_orders.id) AS total_job, 
                SUM(IF(status_validation = 'VALIDATED', 1, 0)) AS total_validated, 
                SUM(CASE WHEN status_validation != 'VALIDATED' THEN 1 ELSE 0 END) AS total_unvalidated
            FROM work_orders
            INNER JOIN handlings ON handlings.id = work_orders.id_handling
            INNER JOIN bookings ON bookings.id = handlings.id_booking
            WHERE work_orders.status = 'COMPLETED' AND work_orders.is_deleted = 0 {$branchCondition}
            GROUP BY DATE(work_orders.completed_at)
          ) AS jobs", 'jobs.job_date = work_order_documents.date', 'left'
        );

        return $baseQuery;
    }

    /**
     * Get all document with or without deleted records.
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

        $columns = [
            "work_order_documents.id",
            "work_order_documents.date",
            "files.total_files",
            "work_order_documents.status",
            "status_job_validate",
            "jobs.total_validated",
            "validator.name",
            "work_order_documents.id",
        ];
        $columnSort = $columns[$column];

        $this->db->start_cache();

        $baseQuery = $this->getBaseQuery();

        if (!empty($search)) {
            $baseQuery->group_start();
            $baseQuery
                ->or_like('work_order_documents.date', trim($search))
                ->or_like('work_order_documents.status', trim($search))
                ->or_like('IF(IFNULL(jobs.total_validated, 0) = IFNULL(jobs.total_job, 0), "VALIDATED", "UNVALIDATED")', trim($search))
                ->or_like('jobs.total_validated', trim($search))
                ->or_like('validator.name', trim($search))
                ->or_like('files.total_files', trim($search));
            $baseQuery->group_end();
        }

        if (!$withTrashed) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        $page = $baseQuery->order_by($columnSort, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        $this->db->flush_cache();

        foreach ($data as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($data),
            "recordsFiltered" => $total,
            "data" => $data
        ];

        return $pageData;
    }

    /**
     * Get accumulate status summary.
     *
     * @param $filters
     * @return array
     */
    public function getStatusSummary($filters = null)
    {
        $branchId = null;
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $summary = $this->db->from($this->table)
            ->select([
                'COUNT(IFNULL(total_files, 0)) AS total',
                'IFNULL(status, "EMPTY") AS status'
            ])
            ->join("
            (
                SELECT id_work_order_document, COUNT(id) AS total_files
                FROM work_order_document_files
                GROUP BY id_work_order_document
            ) AS files", 'files.id_work_order_document = work_order_documents.id', 'left')
            ->group_by('status');

        if (!empty($branchId)) {
            $summary->where('work_order_documents.id_branch', $branchId);
        }

        if (!empty($filters)) {
            if (isset($filters['year'])) {
                $summary->where('YEAR(date)', $filters['year']);
            }

            if (isset($filters['month'])) {
                $summary->where('MONTH(date)', $filters['month']);
            }
        }

        return $summary->get()->result_array();
    }
}