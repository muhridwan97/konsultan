<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BranchModel extends MY_Model
{
    protected $table = 'ref_branches';

    const BRANCH_TYPE_PLB = 'PLB';  
    const BRANCH_TYPE_TPP = 'TPP';
    const BRANCH_TYPE_GU = 'GU';

    /**
     * BranchModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('PeopleModel', 'people');
    }

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        $branches = $this->db
            ->select('ref_branches.*, IFNULL(total_warehouse, 0) AS total_warehouse', false)
            ->from('ref_branches')
            ->join('(
                    SELECT id_branch, COUNT(id) AS total_warehouse 
                    FROM ref_warehouses 
                    WHERE is_deleted = false
                    GROUP BY id_branch
                ) AS branch_warehouses', 'branch_warehouses.id_branch = ref_branches.id', 'left');
        return $branches;
    }

    /**
     * Get branch data by customer with or without deleted record.
     * @param integer $idCustomer
     * @param bool $withTrashed
     * @return array
     */
    public function getByCustomer($idCustomer, $withTrashed = false)
    {
        $people = $this->people->getById($idCustomer);
        // print_debug($people);
        if($people['type_user'] == "NON USER" && $people['type'] == 'CUSTOMER'){
            $branches = $this->getBaseQuery()
            ->join('ref_people_branches', 'ref_branches.id = ref_people_branches.id_branch')
            ->where('ref_people_branches.id_customer', $idCustomer);

        }else{
            $branches = $this->getBaseQuery()
            ->distinct()  
            ->join('prv_user_roles', 'prv_user_roles.id_branch = ref_branches.id')
            ->join('ref_people_users', 'ref_people_users.id_user = prv_user_roles.id_user', 'left')
            ->join('ref_people', 'ref_people.id = ref_people_users.id_people')
            ->where('ref_people.id', $idCustomer);
        }

        if (!$withTrashed) {
            $branches->where('ref_branches.is_deleted', false);
        }

        return $branches->get()->result_array();
    }

    /**
     * Get branch data by user with or without deleted record.
     * @param integer $idUser
     * @param bool $withTrashed
     * @return array
     */
    public function getByUser($idUser, $withTrashed = false)
    {
        $branches = $this->getBaseQuery()
        ->distinct()  
        ->join('prv_user_roles', 'prv_user_roles.id_branch = ref_branches.id')
        ->where('prv_user_roles.id_user', $idUser);
        

        if (!$withTrashed) {
            $branches->where('ref_branches.is_deleted', false);
        }

        return $branches->get()->result_array();
    }

}