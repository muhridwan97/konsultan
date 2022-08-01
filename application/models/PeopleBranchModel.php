<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PeopleBranchModel extends CI_Model
{
    private $table = 'ref_people_branches';

    /**
     * PeopleBranchModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create people branch.
     * @param $data
     * @return bool|int
     */
    public function createPeopleBranch($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Delete person.
     * @param $customerId
     * @return mixed
     */
    public function deletePeopleBranchByCustomer($customerId)
    {
        return $this->db->delete($this->table, ['id_customer' => $customerId]);
    }

    public function getIdByIdCustomerIdBranch($customerId,$branchId){
        return $this->db->select('id')
        ->from($this->table)
        ->where('id_customer',$customerId)
        ->where('id_branch',$branchId)
        ->get()->result_array();
         
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
        $this->db->where('id',$id);
        $this->db->update($this->table, $data);
        return ($this->db->affected_rows() > 0);
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
        $baseQuery = $this->db->select('*')->from($this->table);

        foreach ($conditions as $key => $condition) {
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

        if ($resultRow === 'COUNT') {
            return $baseQuery->count_all_results();
        } else if ($resultRow) {
            return $baseQuery->get()->row_array();
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get multiple people branch data by id customer  with or without deleted record.
     * @param $id
     * @param bool $withTrashed
     * @return array
     */
    public function getPeopleBranchByCustomer($customerId)
    {
        $getPeopleBranchByCustomer =  $this->db->select('*')
                                ->from($this->table)
                                ->where('id_customer',$customerId);

        return $getPeopleBranchByCustomer->get()->result_array();
    }

}