<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HandlingTypeModel extends CI_Model
{
    private $table = 'ref_handling_types';

    const CATEGORY_WAREHOUSE = 'WAREHOUSE';
    const CATEGORY_NON_WAREHOUSE = 'NON WAREHOUSE';

    /**
     * HandlingTypeModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get base query handling types.
     * @return CI_DB_query_builder
     */
    public function getBaseQueryHandlingTypes()
    {
        $branchId = get_active_branch('id');
        $branchCondition = '';
        if (!empty($branchId)) {
            $branchCondition = 'AND id_branch = "' . $branchId . '"';
        }

        $query = $this->db->select([
            'ref_handling_types.*',
            'IFNULL(total_component, 0) AS total_component'
        ])
            ->from('ref_handling_types')
            ->join("(
                    SELECT id_handling_type, COUNT(id) AS total_component 
                    FROM ref_handling_type_components 
                    WHERE is_deleted = false {$branchCondition}
                    GROUP BY id_handling_type
                ) AS handling_type_components",
                'handling_type_components.id_handling_type = ref_handling_types.id', 'left')
            ->order_by('ref_handling_types.id', 'desc');
        return $query;
    }

    /**
     * Get all handling types with or without deleted records.
     * @param bool $withTrashed
     * @return array
     */
    public function getAllHandlingTypes($withTrashed = false)
    {
        $handlingTypes = $this->getBaseQueryHandlingTypes();

        if (!$withTrashed) {
            $handlingTypes->where('ref_handling_types.is_deleted', false);
        }

        return $handlingTypes->get()->result_array();
    }

    /**
     * Get single handling type data by id with or without deleted record.
     * @param integer $id
     * @param bool $withTrashed
     * @return array
     */
    public function getHandlingTypeById($id, $withTrashed = false)
    {
        $handlingTypes = $this->getBaseQueryHandlingTypes();

        if (!$withTrashed) {
            $handlingTypes->where('ref_handling_types.is_deleted', false);
        }

        if (is_array($id)) {
            $handlingTypes->where_in('ref_handling_types.id', $id);
        }else{
            $handlingTypes->where('ref_handling_types.id', $id);
        }

        if(is_array($id)) {
            return $handlingTypes->get()->result_array();
        }

        return $handlingTypes->get()->row_array();
    }


    /**
     * Get single handling type data by id with or without deleted record.
     * @param string $name
     * @param bool $withTrashed
     * @return array
     */
    public function getHandlingTypeByTypeName($name, $withTrashed = false)
    {
        $handlingTypes = $this->getBaseQueryHandlingTypes()->where('handling_type', $name);

        if (!$withTrashed) {
            $handlingTypes->where('is_deleted', false);
        }

        return $handlingTypes->get()->row_array();
    }

    /**
     * Get allocated handling type by customer.
     * @param $customerId
     * @return mixed
     */
    public function getHandlingTypesByCustomer($customerId)
    {
        $handlingTypes = $this->getBaseQueryHandlingTypes()
            ->join('ref_people_handling_types', 'ref_handling_types.id = ref_people_handling_types.id_handling_type', 'left')
            ->where([
                'ref_people_handling_types.id_customer' => $customerId,
                'ref_handling_types.is_deleted' => false
            ]);

        return $handlingTypes->get()->result_array();
    }

    /**
     * Get handling multiplier.
     *
     * @param $multiplier
     * @return array
     */
    public function getHandlingTypesByMultiplier($multiplier)
    {
        $handlingTypes = $this->getBaseQueryHandlingTypes()
            ->where('is_deleted', false)
            ->group_start()
            ->where('ref_handling_types.multiplier_container', $multiplier)
            ->or_where('ref_handling_types.multiplier_goods', $multiplier)
            ->group_end();

        return $handlingTypes->get()->result_array();
    }

    /**
     * Create new handling type.
     * @param $data
     * @return bool
     */
    public function createHandlingType($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update handling type.
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateHandlingType($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete handling type data.
     * @param integer $id
     * @param bool $softDelete
     */
    public function deleteHandlingType($id, $softDelete = true)
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

      /**
     * Get single handling type data by id with or without deleted record.
     * @param integer $id
     * @param bool $withTrashed
     * @return array
     */
    public function getAjaxHandlingTypeById($handlingTypeName, $page, $withTrashed = false)
    {
        $this->db->start_cache();
        $handlingTypes = $this->getBaseQueryHandlingTypes();

        if (!$withTrashed) {
            $handlingTypes->where('ref_handling_types.is_deleted', false);
        }

        if (!empty($handlingTypeName)) {
            if (is_array($handlingTypeName)) {
                $handlingTypes->where_in('ref_handling_types.handling_type', $handlingTypeName);
            } else {
                $handlingTypes->like('ref_handling_types.handling_type', trim($handlingTypeName));
            }
        }

        $this->db->stop_cache();

        if (!empty($page) || $page != 0) {
            $handlingTypesTotal = $handlingTypes->count_all_results();
            $handlingTypesPage = $handlingTypes->limit(10, 10 * ($page - 1));
            $dataHandlingTypes = $handlingTypesPage->get()->result_array();

            return [
                'results' => $dataHandlingTypes,
                'total_count' => $handlingTypesTotal
            ];
        }

        return $handlingTypes->get()->result_array();
    }

}