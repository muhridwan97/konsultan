<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WarehouseModel extends MY_Model
{
    protected $table = 'ref_warehouses';

    /**
     * WarehouseModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active record query builder for all related warehouse data selection.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch_id();
        }

        $warehouses = $this->db
            ->select([
                'ref_warehouses.*',
                'ref_branches.branch',
                'IFNULL(total_position, 0) AS total_position'
            ])
            ->from('ref_warehouses')
            ->join('ref_branches', 'ref_branches.id = ref_warehouses.id_branch', 'left')
            ->join('(
                    SELECT id_warehouse, COUNT(id) AS total_position 
                    FROM ref_positions 
                    WHERE is_deleted = false
                    GROUP BY id_warehouse
                ) AS warehouse_positions', 'warehouse_positions.id_warehouse = ref_warehouses.id', 'left');

        if (!empty($branchId)) {
            $warehouses->where('ref_warehouses.id_branch', $branchId);
        }

        return $warehouses;
    }

    /**
     * Get warehouse data by name with or without deleted record.
     *
     * @param null $name
     * @param $page
     * @param bool $withTrashed
     * @return array
     */
    public function getByName($name, $page = null, $withTrashed = false)
    {
        $branchId = get_active_branch('id');

        $this->db->start_cache();

        $warehouses = $this->db->select('ref_warehouses.*')->from($this->table);

        if (is_array($name)) {
            $warehouses->where_in('ref_warehouses.warehouse', $name);
        } else {
            $warehouses->like('ref_warehouses.warehouse', $name, 'both');
        }

        if (!$withTrashed) {
            $warehouses->where('ref_warehouses.is_deleted', false);
        }

        if(!empty($branchId)) {
            $warehouses->where('ref_warehouses.id_branch', $branchId);
        }

        $this->db->stop_cache();

        if (!empty($page) || $page != 0) {
            $total = $warehouses->count_all_results();
            $page = $warehouses->limit(10, 10 * ($page - 1));
            $data = $page->get()->result_array();

            return [
                'results' => $data,
                'total_count' => $total
            ];
        }

        $warehouseData = $warehouses->get()->result_array();

        $this->db->flush_cache();

        return $warehouseData;
    }

}