<?php

class PositionModel extends MY_Model
{
    protected $table = 'ref_positions';

    /**
     * PositionModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

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

        $positions = $this->db->select([
            'ref_positions.id',
            'ref_positions.id_position_type',
            'ref_position_types.position_type',
            'ref_position_types.is_usable',
            'ref_position_types.color',
            'ref_positions.id_warehouse',
            'ref_warehouses.warehouse',
            'ref_positions.id_customer',
            'ref_people.name',
            'ref_positions.position',
            'ref_positions.description',
            'ref_positions.created_at',
            'ref_positions.updated_at',
        ])
            ->from($this->table)
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left')
            ->join('ref_position_types', 'ref_position_types.id = ref_positions.id_position_type', 'left')
            ->join('ref_people', 'ref_people.id = ref_positions.id_customer', 'left');

        if (!empty($branchId)) {
            $positions->where('ref_warehouses.id_branch', $branchId);
        }

        return $positions;
    }

    /**
     * Get all position with or without deleted records.
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

        $columnOrder = [
            "ref_positions.id",
            "ref_warehouses.warehouse",
            "ref_positions.position",
            "ref_positions.description",
            "ref_positions.id",
        ];
        $columnSort = $columnOrder[$column];

        $branchId = get_active_branch('id');

        $this->db->start_cache();

        $positions = $this->getBaseQuery($branchId)
            ->group_start()
            ->like('ref_warehouses.warehouse', trim($search))
            ->or_like('ref_positions.position', trim($search))
            ->or_like('ref_positions.description', trim($search))
            ->or_like('ref_people.name', trim($search))
            ->group_end();

        if (!$withTrashed) {
            $positions->where('ref_positions.is_deleted', false);
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $positions->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        $page = $positions->order_by($columnSort, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        foreach ($data as &$row) $row['no'] = ++$start;

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
     * Get position data by name
     * @param null $positionName
     * @param $page
     * @param bool $withTrashed
     * @return array
     */
    public function getByName($positionName = null, $page, $withTrashed = false)
    {
        $this->db->start_cache();

        $positions = $this->getBaseQuery()->where('ref_position_types.is_usable', 1);

        if (!empty($positionName)) {
            $positions->like('ref_positions.position', $positionName);
        }

        if (!$withTrashed) {
            $positions->where('ref_positions.is_deleted', false);
        }

        $this->db->stop_cache();

        if (!empty($page) || $page != 0) {
            $totalData = $positions->count_all_results();
            $dataPerPage = $positions->limit(10, 10 * ($page - 1));
            $data = $dataPerPage->get()->result_array();

            return [
                'results' => $data,
                'total_count' => $totalData
            ];
        }

        return $positions->get()->result_array();
    }

}