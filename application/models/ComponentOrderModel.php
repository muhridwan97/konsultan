<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ComponentOrderModel extends CI_Model
{
    private $table = 'component_orders';

    /**
     * ComponentOrderModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active record query builder for all related handling component data selection.
     * @return CI_DB_query_builder
     */
    public function getBaseComponentOrderQuery()
    {
        $branchId = get_active_branch('id');

        $componentTransactions = $this->db->select([
            'component_orders.*',
            'ref_branches.branch',
            'ref_components.handling_component',
            'ref_components.component_category'
        ])
            ->from($this->table)
            ->join('ref_components', 'ref_components.id = component_orders.id_component', 'left')
            ->join('ref_branches', 'ref_branches.id = component_orders.id_branch', 'left')
            ->order_by('component_orders.id', 'desc');

        if (!empty($branchId)) {
            $componentTransactions->where('component_orders.id_branch', $branchId);
        }

        return $componentTransactions;
    }

    /**
     * Get all handling component transaction with or without deleted records.
     * @param bool $withTrashed
     * @return array
     */
    public function getAllComponentOrders($withTrashed = false)
    {
        $componentTransactions = $this->getBaseComponentOrderQuery();

        if (!$withTrashed) {
            $componentTransactions->where('component_orders.is_deleted', false);
        }

        return $componentTransactions->get()->result_array();
    }

    /**
     * Get single handling component data by id with or without deleted record.
     * @param integer $id
     * @param bool $withTrashed
     * @return array
     */
    public function getComponentOrderById($id, $withTrashed = false)
    {
        $componentTransaction = $this->getBaseComponentOrderQuery()
            ->where('component_orders.id', $id);

        if (!$withTrashed) {
            $componentTransaction->where('component_orders.is_deleted', false);
        }

        return $componentTransaction->get()->row_array();
    }

    /**
     * Get handling component transaction by handling component.
     * @param $handlingComponentId
     * @param bool $activeOnly
     * @param bool $withTrashed
     * @return mixed
     */
    public function getComponentOrdersByHandlingComponent($handlingComponentId, $activeOnly = false, $withTrashed = false)
    {
        $componentTransactions = $this->getBaseComponentOrderQuery()
            ->where('component_orders.id_component', $handlingComponentId);

        if (!$withTrashed) {
            $componentTransactions->where('component_orders.is_deleted', false);
        }

        if($activeOnly) {
            $componentTransactions
                ->where('component_orders.is_void', false)
                ->where('component_orders.status', 'APPROVED');
        }

        return $componentTransactions->get()->result_array();
    }

    /**
     * Create new handling component transaction.
     * @param $data
     * @return bool
     */
    public function createComponentOrder($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update handling component transaction.
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateComponentOrder($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete handling component transaction data.
     * @param integer $id
     * @param bool $softDelete
     * @return bool
     */
    public function deleteComponentOrder($id, $softDelete = true)
    {
        if ($softDelete) {
            return $this->db->update($this->table, [
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => UserModel::authenticatedUserData('id')
            ], ['id' => $id]);
        }
        return $this->db->delete($this->table, ['id' => $id]);
    }

}