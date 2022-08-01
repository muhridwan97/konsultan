<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PlanRealizationModel extends MY_Model
{
    protected $table = 'plan_realizations';

    const STATUS_OPEN = 'OPEN';
    const STATUS_CLOSED = 'CLOSED';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        return parent::getBaseQuery($branchId)
            ->select([
                'ref_branches.branch',
                'COUNT(DISTINCT plan_inbounds.id) AS total_inbound',
                'COUNT(DISTINCT plan_outbounds.id) AS total_outbound',
            ])
            ->join('ref_branches', 'ref_branches.id = plan_realizations.id_branch', 'left')
            ->join('plan_inbounds', 'plan_inbounds.id_plan_realization = plan_realizations.id', 'left')
            ->join('plan_outbounds', 'plan_outbounds.id_plan_realization = plan_realizations.id', 'left')
            ->group_by('plan_realizations.id');
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
            ->like('plan_realizations.date', $search)
            ->or_like('plan_realizations.description', $search)
            ->or_like('plan_realizations.analysis', $search)
            ->group_end();

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
        if ($column == 'no') $column = 'plan_realizations.id';
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
