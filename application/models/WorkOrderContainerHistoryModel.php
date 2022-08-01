<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class WorkOrderContainerHistoryModel
 * @property WorkOrderContainerModel $WorkOrderContainerModel
 */
class WorkOrderContainerHistoryModel extends WorkOrderContainerModel
{
    protected $table = 'work_order_container_histories';

    /**
     * Get basic query work order container.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return $this->db
            ->select([
                $this->table . '.*',
                'work_orders.no_work_order',
                'work_orders.completed_at',
                'handlings.no_handling',
                'bookings.id AS id_booking',
                'bookings.no_booking',
                'ref_containers.no_container',
                'ref_containers.type',
                'ref_containers.size',
                'ref_positions.position',
                'ref_people.name AS owner_name',
                'COUNT(work_order_goods.id) AS total_item',
                '"" AS id_position_blocks',
                '"" AS position_blocks',
                'ref_warehouses.type as type_warehouse'
            ])
            ->from($this->table)
            ->join('work_order_histories AS work_orders', 'work_orders.id = ' . $this->table . '.id_work_order')
            ->join('work_order_goods_histories AS work_order_goods', 'work_order_goods.id_work_order_container = ' . $this->table . '.id', 'left')
            //->join('work_order_container_positions', 'work_order_container_positions.id_work_order_container = ' . $this->table . '.id', 'left')
            //->join('ref_position_blocks', 'ref_position_blocks.id = work_order_container_positions.id_position_block', 'left')
            ->join('ref_containers', 'ref_containers.id = ' . $this->table . '.id_container', 'left')
            ->join('ref_positions', 'ref_positions.id = ' . $this->table . '.id_position', 'left')
            ->join('ref_people', 'ref_people.id = ' . $this->table . '.id_owner', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling', 'left')
            ->join('bookings', 'bookings.id = handlings.id_booking', 'left')
            ->join('ref_warehouses', 'ref_positions.id_warehouse = ref_warehouses.id', 'left')
            ->group_by($this->table . '.id');
    }

    /**
     * WorkOrderContainerHistoryModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }
  
}