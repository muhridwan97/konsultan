<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ChangeOwnershipModel extends CI_Model
{
    private $table = 'change_ownerships';

    /**
     * ChangeOwnershipModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Set basic query for change ownerships.
     * @return CI_DB_query_builder
     */
    private function getBasicChangeOwnershipsQuery()
    {
        $changeOwnerships = $this->db
            ->select([
                'change_ownerships.*', 'owner_from.name AS owner_from', 'owner_to.name AS owner_to'
            ])
            ->from('change_ownerships')
            ->join('ref_people AS owner_from', 'change_ownerships.id_owner_from = owner_from.id', 'left')
            ->join('ref_people AS owner_to', 'change_ownerships.id_owner_to = owner_to.id', 'left')
            ->order_by('change_ownerships.change_date', 'ASC');
        return $changeOwnerships;
    }

    /**
     * Get all change ownerships with or without deleted records.
     * @param bool $withTrashed
     * @return array
     */
    public function getAllChangeOwnerships($withTrashed = false)
    {
        $changeOwnerships = $this->getBasicChangeOwnershipsQuery();

        if ($withTrashed) {
            return $changeOwnerships->get()->result_array();
        }

        $changeOwnerships = $changeOwnerships->where('change_ownerships.is_deleted', false)->get();

        return $changeOwnerships->result_array();
    }

    /**
     * Auto Number Change Ownership
     * @param string $type
     * @return string
     */
    public function getAutoNumberChangeOwnership($type = 'CM')
    {
        $orderData = $this->db->query("
            SELECT CAST(RIGHT(no_change_ownership, 6) AS UNSIGNED) + 1 AS order_number 
            FROM change_ownerships 
            WHERE MONTH(created_at) = MONTH(NOW()) 
			AND YEAR(created_at) = YEAR(NOW())
			AND SUBSTRING(no_change_ownership, 1, 2) = '$type'
            ORDER BY id DESC LIMIT 1
			");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return $type . '/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }

    /**
     * Get change ownership data by delivery order with or without deleted record.
     * @param $idDeliveryOrder
     * @param bool $withTrashed
     * @return array
     */
    public function getOwnershipHistoriesByDeliveryOrder($idDeliveryOrder, $withTrashed = false)
    {
        $ownershiphistories = $this->getBasicChangeOwnershipsQuery();

        if ($withTrashed) {
            return $ownershiphistories->where(['change_ownerships.id_delivery_order' => $idDeliveryOrder])->get()->result_array();
        }

        $ownershiphistories = $ownershiphistories->where([
            'change_ownerships.id_delivery_order' => $idDeliveryOrder,
            'change_ownerships.is_deleted' => false
        ])->get();

        return $ownershiphistories->result_array();
    }

    /**
     * Get change ownership data by id with or without deleted record.
     * @param integer $id
     * @param bool $withTrashed
     * @return array
     */
    public function getChangeOwnershipById($id, $withTrashed = false)
    {
        $changeOwnership = $this->getBasicChangeOwnershipsQuery();

        if ($withTrashed) {
            return $changeOwnership->where(['id' => $id])->get()->row_array();
        }

        $changeOwnership = $changeOwnership->where([
            'change_ownerships.id' => $id,
            'change_ownerships.is_deleted' => false
        ])->get();
        return $changeOwnership->row_array();
    }

    /**
     * Create new change ownership.
     * @param $data
     * @return bool
     */
    public function createChangeOwnership($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update change ownership.
     * @param $data
     * @param $id
     * @return bool
     */
    public function updateOwnershipHistory($data, $id)
    {
        return $this->db->update($this->table, $data, ['change_ownerships.id' => $id]);
    }

    /**
     * Delete change ownership data.
     * @param integer $id
     * @param bool $softDelete
     * @return bool|mixed
     */
    public function deleteChangeOwnership($id, $softDelete = true)
    {
        if ($softDelete) {
            return $this->db->update($this->table, [
                'change_ownerships.is_deleted' => true,
                'change_ownerships.deleted_at' => date('Y-m-d H:i:s')
            ], ['id' => $id]);
        }
        return $this->db->delete($this->table, ['change_ownerships.id' => $id]);
    }

    /**
     * Delete change ownership data.
     * @param integer $deliveryOrderId
     * @param bool $softDelete
     * @return bool|mixed
     */
    public function deleteOwnershipByDO($deliveryOrderId, $softDelete = true)
    {
        if ($softDelete) {
            return $this->db->update($this->table, [
                'change_ownerships.is_deleted' => true,
                'change_ownerships.deleted_at' => date('Y-m-d H:i:s')
            ], ['id_delivery_order' => $deliveryOrderId]);
        }
        return $this->db->delete($this->table, ['change_ownerships.id_delivery_order' => $deliveryOrderId]);
    }

}