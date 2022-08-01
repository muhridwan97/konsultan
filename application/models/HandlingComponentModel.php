<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HandlingComponentModel extends CI_Model
{
    private $table = 'handling_components';

    /**
     * HandlingComponentModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active record query builder for all related handling component selection.
     * @return CI_DB_query_builder
     */
    public function getBaseHandlingComponentQuery()
    {
        $componentTransactions = $this->db->select([
            'handling_components.*',
            'ref_units.unit',
            'ref_components.handling_component',
            'component_orders.no_transaction',
            'handlings.no_handling',
        ])
            ->from($this->table)
            ->join('ref_components', 'ref_components.id = handling_components.id_component', 'left')
            ->join('ref_units', 'ref_units.id = handling_components.id_unit', 'left')
            ->join('handlings', 'handlings.id = handling_components.id_handling', 'left')
            ->join('component_orders', 'component_orders.id = handling_components.id_component_order', 'left');

        return $componentTransactions;
    }

    /**
     * Get all handling component with or without deleted records.
     * @return array
     */
    public function getAllHandlingComponents()
    {
        $handlingComponents = $this->getBaseHandlingComponentQuery();

        return $handlingComponents->get()->result_array();
    }

    /**
     * Get single handling component data by id with or without deleted record.
     * @param integer $handlingId
     * @return array
     */
    public function getHandlingComponentsByHandling($handlingId)
    {
        $handlingComponents = $this->getBaseHandlingComponentQuery()
            ->where('handling_components.id_handling', $handlingId);

        return $handlingComponents->get()->result_array();
    }

    /**
     * Get single handling component data by id with or without deleted record.
     * @param integer $id
     * @return array
     */
    public function getHandlingComponentById($id)
    {
        $handlingComponent = $this->getBaseHandlingComponentQuery()
            ->where('handling_components.id', $id);

        return $handlingComponent->get()->row_array();
    }

    /**
     * Create new handling component.
     * @param $data
     * @return bool
     */
    public function createHandlingComponent($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update handling component.
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateHandlingComponent($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete handling component data.
     * @param $handlingId
     * @return bool
     */
    public function deleteHandlingComponentByHandling($handlingId)
    {
        return $this->db->delete($this->table, ['id_handling' => $handlingId]);
    }

    /**
     * Delete handling component data.
     * @param integer $id
     * @param bool $softDelete
     * @return bool
     */
    public function deleteHandlingComponent($id, $softDelete = true)
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