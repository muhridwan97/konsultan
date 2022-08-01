<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkOrderContainerModel extends MY_Model
{
    protected $table = 'work_order_containers';

    /**
     * WorkOrderContainerModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

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
                'bookings.no_reference',
                'booking_refs.no_reference AS no_booking_reference',
                'ref_containers.no_container',
                'ref_containers.type',
                'ref_containers.size',
                'ref_positions.position',
                'ref_people.name AS owner_name',
                'COUNT(work_order_goods.id) AS total_item',
                'GROUP_CONCAT(DISTINCT work_order_container_positions.id_position_block) AS id_position_blocks',
                'GROUP_CONCAT(DISTINCT ref_position_blocks.position_block) AS position_blocks',
                'ref_warehouses.type as type_warehouse'
            ])
            ->from($this->table)
            ->join('bookings AS booking_refs', 'booking_refs.id = work_order_containers.id_booking_reference', 'left')
            ->join('work_orders', 'work_orders.id = ' . $this->table . '.id_work_order')
            ->join('work_order_goods', 'work_order_goods.id_work_order_container = ' . $this->table . '.id', 'left')
            ->join('work_order_container_positions', 'work_order_container_positions.id_work_order_container = ' . $this->table . '.id', 'left')
            ->join('ref_position_blocks', 'ref_position_blocks.id = work_order_container_positions.id_position_block', 'left')
            ->join('ref_containers', 'ref_containers.id = ' . $this->table . '.id_container', 'left')
            ->join('ref_positions', 'ref_positions.id = ' . $this->table . '.id_position', 'left')
            ->join('ref_people', 'ref_people.id = ' . $this->table . '.id_owner', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling', 'left')
            ->join('bookings', 'bookings.id = handlings.id_booking', 'left')
            ->join('ref_warehouses', 'ref_positions.id_warehouse = ref_warehouses.id', 'left')
            ->group_by($this->table . '.id');
    }

    /**
     * Get all work orders by specific handling id.
     * @param $workOrderId
     * @param bool $nonContainer
     * @param bool $withTrash
     * @return array
     */
    public function getWorkOrderContainersByWorkOrder($workOrderId, $nonContainer = false, $withTrash = false)
    {
        $workOrderContainers = $this->getBaseQuery()
            ->where($this->table . '.id_work_order', $workOrderId);

        if ($nonContainer) {
            $workOrderContainers->where($this->table . '.id_work_order_container IS NULL');
        }

        if (!$withTrash) {
            $workOrderContainers->where($this->table . '.is_deleted', false);
        }

        return $workOrderContainers->get()->result_array();
    }


    /**
     * Get all work orders by specific handling id adn overtime status is 'NOT SET OR 0'.
     * @param $workOrderId
     * @param bool $nonContainer
     * @param bool $withTrash
     * @return array
     */
    public function getNotSetStatusOvertimeWorkOrderContainersByWorkOrder($workOrderId, $nonContainer = false, $withTrash = false)
    {
        $workOrderContainers = $this->getBaseQuery()
            ->where('work_order_containers.id_work_order', $workOrderId)->where('work_order_containers.overtime_status', '0');

        if ($nonContainer) {
            $workOrderContainers->where('work_order_containers.id_work_order_container IS NULL');
        }

        if (!$withTrash) {
            $workOrderContainers->where('work_order_containers.is_deleted', false);
        }

        return $workOrderContainers->get()->result_array();
    }

    /**
     * Get work order container by work order container.
     * @param $workOrderContainerId
     * @param bool $withTrash
     * @return array
     */
    public function getWorkOrderContainersByWorkOrderContainer($workOrderContainerId, $withTrash = false)
    {
        $workOrderContainers = $this->getBaseQuery()
            ->where('work_order_containers.id_work_order_container', $workOrderContainerId);

        if (!$withTrash) {
            $workOrderContainers->where('work_order_containers.is_deleted', false);
        }

        return $workOrderContainers->get()->result_array();
    }

    /**
     * Get work order container by id.
     * @param $workOrderContainerId
     * @param bool $withTrash
     * @return mixed
     */
    public function getWorkOrderContainerById($workOrderContainerId, $withTrash = false)
    {
        $workOrderContainer = $this->getBaseQuery()
            ->where('work_order_containers.id', $workOrderContainerId);

        if (!$withTrash) {
            $workOrderContainer->where('work_order_containers.is_deleted', false);
        }

        return $workOrderContainer->get()->row_array();
    }

    /**
     * TODO: get real stock
     * @param $bookingId
     * @return array
     */
    public function getContainerStocksByBooking($bookingId, $withTrash = false)
    {
        $containers = $this->getBaseQuery()->where('bookings.id', $bookingId);

        if (!$withTrash) {
            $containers->where('work_order_containers.is_deleted', false);
        }

        return $containers->get()->result_array();
    }

    /**
     * Insert single or batch data container.
     * @param $data
     * @return bool
     */
    public function insertWorkOrderContainer($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update work order container data.
     * @param $data
     * @param $id
     * @return bool
     */
    public function updateWorkOrderContainer($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete work order containers by specific work order.
     * @param $workOrderId
     * @param bool $softDelete
     * @return mixed
     */
    public function deleteContainersByWorkOrder($workOrderId, $softDelete = true)
    {
        if ($softDelete) {
            return $this->db->update($this->table, [
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => UserModel::authenticatedUserData('id')
            ], ['id_work_order' => $workOrderId]);
        }
        return $this->db->delete($this->table, ['id_work_order' => $workOrderId]);
    }

     /**
     * Delete work order containers by specific Id.
     * @param $Id
     * @param bool $softDelete
     * @return mixed
     */
    public function deleteContainersById($Id, $softDelete = true)
    {
        if ($softDelete) {
            return $this->db->update($this->table, [
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => UserModel::authenticatedUserData('id')
            ], ['id' => $Id]);
        }
        return $this->db->delete($this->table, ['id' => $Id]);
    }
}