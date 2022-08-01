<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OwnershipHistoryModel extends CI_Model
{
    private $table = 'ownership_histories';

    /**
     * OwnershipHistoryModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }
	
	/**
     * Set basic query for ownership histories
     * @param bool $withTrashed
     * @return array
     */
    private function getBasicOwnershipHistoryQuery()
    {
        $ownershiphistory = $this->db
            ->select('ownership_histories.*, delivery_orders.no_delivery_order, owner_to.name')
            ->from('ownership_histories')
			->join('delivery_orders', 'ownership_histories.id_delivery_order = delivery_orders.id', 'left')
			->join('ref_people AS owner_to', 'ownership_histories.id_owner_to = owner_to.id', 'left')
			->order_by('delivery_orders.no_delivery_order', 'ASC')
			->order_by('ownership_histories.change_date', 'ASC');
		return $ownershiphistory;
    }
	
    /**
     * Get all ownership histories with or without deleted records.
     * @param bool $withTrashed
     * @return array
     */
    public function getAllOwnershipHistories($withTrashed = false)
    {
		$ownershiphistories = $this->getBasicOwnershipHistoryQuery();
		
        if ($withTrashed) {
            return $ownershiphistories->get()->result_array();
        }

        $branches = $ownershiphistories
            ->where('ownership_histories.is_deleted', false)
            ->get();
        return $branches->result_array();
    }

    /**
     * Get ownership histories data by delivery order with or without deleted record.
     * @param integer $id
     * @param bool $withTrashed
     * @return array
     */
    public function getOwnershipHistoriesByDeliveryOrder($idDeliveryOrder, $withTrashed = false)
    {
		$ownershiphistories = $this->getBasicOwnershipHistoryQuery();
	
        if ($withTrashed) {
            return $ownershiphistories->where(['ownership_histories.id_delivery_order' => $idDeliveryOrder])->get->result_array();
        }

        $ownershiphistories = $ownershiphistories->where([
			'ownership_histories.id_delivery_order' => $idDeliveryOrder,
            'ownership_histories.is_deleted' => false
        ])->get();
        return $ownershiphistories->result_array();
    }
	
	/**
     * Get ownership history data by id with or without deleted record.
     * @param integer $id
     * @param bool $withTrashed
     * @return array
     */
    public function getOwnershipHistoryById($id, $withTrashed = false)
    {
		$ownershiphistories = $this->getBasicOwnershipHistoryQuery();
	
        if ($withTrashed) {
            return $ownershiphistories->where(['id' => $id])->get()->row_array();
        }

        $ownershiphistory = $ownershiphistories->where([
            'ownership_histories.id' => $id,
            'ownership_histories.is_deleted' => false
        ])->get();
        return $ownershiphistory->row_array();
    }

    /**
     * Create new ownership histories.
     * @param $data
     * @return bool
     */
    public function createOwnershipHistory($data)
    {
        return $this->db->insert($this->table, $data);
    }
	
	/**
     * Update ownership histories.
     * @param $data
     * @param $id
     */
	public function updateOwnershipHistory($data, $id)
    {
        return $this->db->update($this->table, $data, ['ownership_histories.id' => $id]);
    }

    /**
     * Delete ownership histories data.
     * @param integer $id
     * @param bool $softDelete
     */
    public function deleteOwnershipHistory($id, $softDelete = true)
    {
        if ($softDelete) {
            return $this->db->update($this->table, [
                'ownership_histories.is_deleted' => true,
                'ownership_histories.deleted_at' => date('Y-m-d H:i:s')
            ], ['id' => $id]);
        }
        return $this->db->delete($this->table, ['ownership_histories.id' => $id]);
    }

    /**
     * Delete ownership histories data.
     * @param integer $deliveryOrderId
     * @param bool $softDelete
     */
    public function deleteOwnershipByDO($deliveryOrderId, $softDelete = true)
    {
        if ($softDelete) {
            return $this->db->update($this->table, [
                'ownership_histories.is_deleted' => true,
                'ownership_histories.deleted_at' => date('Y-m-d H:i:s')
            ], ['id_delivery_order' => $deliveryOrderId]);
        }
        return $this->db->delete($this->table, ['ownership_histories.id_delivery_order' => $deliveryOrderId]);
    }

}