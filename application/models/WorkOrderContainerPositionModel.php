<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkOrderContainerPositionModel extends MY_Model
{
    protected $table = 'work_order_container_positions';

    /**
     * Delete work order goods position by specific work order workOrdergoodsId.
     * @param $workOrdergoodsId
     * @return mixed
     */
    public function deleteWorkOrderContainerPositionByworkOrdercontainerId($workOrdercontainerId)
    {
        return $this->db->delete($this->table, ['id_work_order_container' => $workOrdercontainerId]);
    }
}