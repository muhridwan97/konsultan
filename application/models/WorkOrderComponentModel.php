<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkOrderComponentModel extends CI_Model
{
    private $table = 'work_order_components';

    /**
     * WorkOrderComponentModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active record query builder for all related work order component selection.
     * @return mixed
     */
    public function getBaseWorkOrderComponentQuery()
    {
        $workOrderComponents = $this->db->select([
            'work_order_components.*',
            'ref_units.unit',
            'ref_components.handling_component',
            'component_orders.no_transaction',
            'work_orders.no_work_order',
        ])
            ->from($this->table)
            ->join('ref_components', 'ref_components.id = work_order_components.id_component', 'left')
            ->join('ref_units', 'ref_units.id = work_order_components.id_unit', 'left')
            ->join('work_orders', 'work_orders.id = work_order_components.id_work_order', 'left')
            ->join('component_orders', 'component_orders.id = work_order_components.id_component_order', 'left');

        return $workOrderComponents;
    }

    /**
     * Get all work order component with or without deleted records.
     * @return array
     */
    public function getAllWorkOrderComponents()
    {
        $workOrderComponents = $this->getBaseWorkOrderComponentQuery();

        return $workOrderComponents->get()->result_array();
    }

    /**
     * Get single work order component data by id with or without deleted record.
     * @param integer $workOrderId
     * @return array
     */
    public function getWorkOrderComponentsByWorkOrder($workOrderId)
    {
        $workOrderComponents = $this->getBaseWorkOrderComponentQuery()
            ->where('work_order_components.id_work_order', $workOrderId);

        return $workOrderComponents->get()->result_array();
    }

    /**
     * Get single work order component data by id with or without deleted record.
     * @param integer $id
     * @return array
     */
    public function getWorkOrderComponentById($id)
    {
        $workOrderComponent = $this->getBaseWorkOrderComponentQuery()
            ->where('work_order_components.id', $id);

        return $workOrderComponent->get()->row_array();
    }

    /**
     * Create new work order component.
     * @param $data
     * @return bool
     */
    public function createWorkOrderComponent($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            $hasCreatedBy = $this->db->field_exists('created_by', $this->table);
            $hasCreatedAt = $this->db->field_exists('created_at', $this->table);
            foreach ($data as &$datum) {
                if ($hasCreatedBy) {
                    $datum['created_by'] = UserModel::authenticatedUserData('id', 0);
                }
                if ($hasCreatedAt) {
                    $datum['created_at'] = date('Y-m-d H:i:s');
                }
            }
            return $this->db->insert_batch($this->table, $data);
        }
        if ($this->db->field_exists('created_by', $this->table) && !key_exists('created_by', $data)) {
            $data['created_by'] = UserModel::authenticatedUserData('id', 0);
        }
        if ($this->db->field_exists('created_at', $this->table) && !key_exists('created_at', $data)) {
            $data['created_at'] = date('Y-m-d H:i:s');
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update work order component.
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateWorkOrderComponent($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete work order component data.
     * @param integer $id
     * @param bool $softDelete
     * @return bool
     */
    public function deleteWorkOrderComponent($id, $softDelete = true)
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
     * Delete work order component data by id_work_order.
     * @param integer $id
     * @param bool $softDelete
     * @return bool
     */
    public function deleteByWorkOrderId($id_work_order)
    {
        return $this->db->delete($this->table, ['id_work_order' => $id_work_order]);
    }

    /**
	 * Get data by custom condition.
	 *
	 * @param $conditions
	 * @param bool $resultRow
	 * @param bool $withTrashed
	 * @return array|int
	 */
	public function getBy($conditions, $resultRow = false, $withTrashed = false)
	{
		$baseQuery = $this->getBaseWorkOrderComponentQuery()->order_by($this->table . '.id', 'asc');

		if (is_array($conditions)) {
			foreach ($conditions as $key => $condition) {
				if (is_array($condition)) {
					if (!empty($condition)) {
						$baseQuery->where_in($key, $condition);
					}
				} else {
					$baseQuery->where($key, $condition);
				}
			}
		} else {
			$baseQuery->where($conditions);
		}

		if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
			$baseQuery->where($this->table . '.is_deleted', false);
		}

		if($resultRow === 'COUNT') {
			return $baseQuery->count_all_results();
		} else if ($resultRow) {
			return $baseQuery->get()->row_array();
		}

		return $baseQuery->get()->result_array();
	}

}