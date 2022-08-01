<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HandlingTypeComponentModel extends CI_Model
{
    private $table = 'ref_handling_type_components';

    /**
     * HandlingTypeComponentModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get basic query handling type components.
     * @return CI_DB_query_builder
     */
    public function getBasicQueryHandlingTypeComponents()
    {
        $query = $this->db->select([
            'ref_handling_type_components.*',
            'ref_handling_types.handling_type',
            'ref_handling_types.handling_code',
            'ref_handling_types.category AS handling_category',
            'ref_components.handling_component',
            'ref_components.component_category',
        ])
            ->from('ref_handling_type_components')
            ->join('ref_handling_types', 'ref_handling_types.id = ref_handling_type_components.id_handling_type')
            ->join('ref_components', 'ref_components.id = ref_handling_type_components.id_component')
            ->order_by('ref_handling_type_components.id', 'desc');
        return $query;
    }

    /**
     * Insert single or batch data handling type components.
     * @param $data
     * @return bool
     */
    public function insertHandlingTypeComponents($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update handling type component.
     * @param $data
     * @param $id
     * @return mixed
     */
    public function updateHandlingTypeComponent($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete handling type component by specific handling type.
     * @param $handlingTypeId
     * @param bool $softDelete
     * @return mixed
     */
    public function deleteHandlingTypeComponentByHandlingType($handlingTypeId, $softDelete = true)
    {
        $branchId = get_active_branch('id');
        $deleteCondition = [
            'id_handling_type' => $handlingTypeId
        ];

        if (!empty($branchId)) {
            $deleteCondition['id_branch'] = $branchId;
        }

        if ($softDelete) {
            return $this->db->update($this->table, [
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => UserModel::authenticatedUserData('id')
            ], $deleteCondition);
        }
        return $this->db->delete($this->table, $deleteCondition);
    }

    /**
     * Delete handling type component.
     * @param integer $id
     * @param bool $softDelete
     * @return bool
     */
    public function deleteHandlingTypeComponents($id, $softDelete = true)
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