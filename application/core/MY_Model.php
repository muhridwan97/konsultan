<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class MY_Model extends CI_Model
{
    protected $table = '';
    protected $id = 'id';
    protected $filteredFields = ['*'];
    protected $filteredMaps = [];

    /**
     * Set field to filtered list.
     *
     * @param $fields
     */
    protected function addFilteredField($fields)
    {
        if (is_array($fields)) {
            foreach ($fields as $field) {
                if (!in_array($field, $this->filteredFields)) {
                    $this->filteredFields[] = $field;
                }
            }
        } else {
            if (!in_array($fields, $this->filteredFields)) {
                $this->filteredFields[] = $fields;
            }
        }
    }

    /**
     * Set field to map as filter list.
     *
     * @param $key
     * @param $field
     */
    protected function addFilteredMap($key, $field)
    {
        $this->filteredMaps[$key] = $field;
    }

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        $baseQuery = $this->db->select([$this->table . '.*'])->from($this->table);

        if ($this->db->field_exists('id_branch', $this->table)) {
            if (!empty($branchId)) {
                $baseQuery->where($this->table . '.id_branch', $branchId);
            }
        }

        return $baseQuery;
    }

    /**
     * Get all data model.
     *
     * @param array $filters
     * @param bool $withTrashed
     * @return mixed
     */
    public function getAll($filters = [], $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery();

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if (!empty($filters)) {
            if (key_exists('query', $filters) && $filters['query']) {
                return $baseQuery;
            }

            if (!empty($this->filteredMaps)) {
                foreach ($this->filteredMaps as $filterKey => $filterField) {
                    if (is_callable($filterField)) {
                        $filterField($baseQuery, $filters);
                    } elseif (key_exists($filterKey, $filters) && !empty($filters[$filterKey])) {
                        if (is_array($filters[$filterKey])) {
                            $baseQuery->where_in($filterField, $filters[$filterKey]);
                        } else {
                            $baseQuery->where($filterField, $filters[$filterKey]);
                        }
                    }
                }
            }
        } else {
            $baseQuery->order_by($this->table . '.' . $this->id, 'desc');
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get single model data by id with or without deleted record.
     *
     * @param $modelId
     * @param bool $withTrashed
     * @return mixed
     */
    public function getById($modelId, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery();

        if(is_array($modelId)) {
            $baseQuery->where_in($this->table . '.' . $this->id, $modelId);
        } else {
            $baseQuery->where($this->table . '.' . $this->id, $modelId);
        }

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if(is_array($modelId)) {
            return $baseQuery->get()->result_array();
        }

        return $baseQuery->get()->row_array();
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
        $baseQuery = $this->getBaseQuery(get_if_exist($conditions, '_id_branch', null));

        foreach ($conditions as $key => $condition) {
            if ($key == '_id_branch') continue;

            if (is_array($condition)) {
                if (!empty($condition)) {
                    $baseQuery->where_in($key, $condition);
                }
            } else {
                $baseQuery->where($key, $condition);
            }
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

    /**
     * Ser
     * @param $keyword
     * @param int $limit
     * @param bool $withTrashed
     * @return array
     */
    public function search($keyword, $limit = 10, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery();

        foreach ($this->filteredFields as $filteredField) {
            if ($filteredField == '*') {
                $fields = $this->db->list_fields($this->table);
                foreach ($fields as $field) {
                    $baseQuery->or_having($this->table . '.' . $field . ' LIKE', '%' . trim($keyword) . '%');
                }
            } else {
                $baseQuery->or_having($filteredField . ' LIKE', '%' . trim($keyword) . '%');
            }
        }

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if (!empty($limit)) {
            $baseQuery->limit($limit);
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get total model data.
     *
     * @param bool $withTrashed
     * @return int
     */
    public function getTotal($withTrashed = false)
    {
        $query = $this->db->from($this->table);

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $query->where($this->table . '.is_deleted', false);
        }

        return $query->count_all_results();
    }

    /**
     * Create new model.
     *
     * @param $data
     * @return bool
     */
    public function create($data)
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
     * Update model.
     *
     * @param $data
     * @param $id
     * @return bool
     */
    public function update($data, $id)
    {
        $condition = is_null($id) ? null : [$this->id => $id];
        if (is_array($id)) {
            $condition = $id;
        }
        if ($this->db->field_exists('updated_at', $this->table) && !key_exists('updated_by', $data)) {
            $data['updated_by'] = UserModel::authenticatedUserData('id', 0);
        }
        if ($this->db->field_exists('updated_at', $this->table) && !key_exists('updated_at', $data)) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        return $this->db->update($this->table, $data, $condition);
    }

    /**
     * Delete model data.
     *
     * @param int|array $id
     * @param bool $softDelete
     * @return bool
     */
    public function delete($id, $softDelete = true)
    {
        if ($softDelete && $this->db->field_exists('is_deleted', $this->table)) {
            return $this->db->update($this->table, [
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => UserModel::authenticatedUserData('id')
            ], (is_array($id) ? $id : [$this->id => $id]));
        }
        return $this->db->delete($this->table, (is_array($id) ? $id : [$this->id => $id]));
    }

}
