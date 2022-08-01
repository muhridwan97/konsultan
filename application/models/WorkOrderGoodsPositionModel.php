<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkOrderGoodsPositionModel extends MY_Model
{
    protected $table = 'work_order_goods_positions';

     /**
     * Delete work order goods position by specific work order workOrdergoodsId.
     * @param $workOrdergoodsId
     * @return mixed
     */
    public function deleteWorkOrderGoodsPositionByworkOrdergoodsId($workOrdergoodsId)
    {
        return $this->db->delete($this->table, ['id_work_order_goods' => $workOrdergoodsId]);
    }
}