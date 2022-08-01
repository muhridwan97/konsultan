<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PeopleModel extends MY_Model
{
    protected $table = 'ref_people';

    public static $TYPE_CUSTOMER = 'CUSTOMER';
    public static $TYPE_SUPPLIER = 'SUPPLIER';
    public static $TYPE_EMPLOYEE = 'EMPLOYEE';
    public static $TYPE_DRIVER = 'DRIVER';
    public static $TYPE_EXPEDITION = 'EXPEDITION';
    public static $TYPE_TPS = 'TPS';
    public static $TYPE_SHIPPING_LINE = 'SHIPPING LINE';
    public static $TYPE_CUSTOMS = 'CUSTOMS';

    public static $GENDER_NONE = 'NONE';
    public static $GENDER_MALE = 'MALE';
    public static $GENDER_FEMALE = 'FEMALE';

    const OUTBOUND_ACCOUNT_RECEIVABLE = 'ACCOUNT RECEIVABLE';
    const OUTBOUND_CASH_AND_CARRY = 'CASH AND CARRY';

    const POSITION_LEVEL_MANAGER = 'MANAGER';
    const POSITION_LEVEL_STAFF = 'STAFF'; 
    const POSITION_LEVEL_SUPERVISOR = 'SUPERVISOR';

    /**
     * PeopleModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

     /**
     * Get people base query.
     *
     * @param null $branchId
     * @param bool $withTrashed
     * @return CI_DB_query_builder
     */
    public function getBaseQuery($branchId = null, $withTrashed = false)
    {
        $branchJoin = if_empty(get_active_branch('id'), $branchId);
        $BaseQuery = $this->db->select('ref_people.*, MAX(ref_people_users.id_user) AS id_user, parent.name AS parent_name, ref_people_branches.whatsapp_group,ref_people_branches.contract, ref_people_branches.id AS id_person_branch')
            ->from($this->table)
            ->join('ref_people AS parent', 'parent.id = ref_people.id_parent', 'left')
            ->join('(select * from ref_people_branches where ref_people_branches.id_branch="'.$branchJoin.'") AS ref_people_branches', 'ref_people_branches.id_customer = ref_people.id', 'left')
            ->join('ref_branches', 'ref_people_branches.id_branch = ref_branches.id', 'left')
            ->join('ref_people_users', 'ref_people_users.id_people = ref_people.id', 'left')
            ->join('prv_user_roles', 'prv_user_roles.id_user = ref_people_users.id_user', 'left')
            ->join('ref_branches AS branches', 'prv_user_roles.id_branch = branches.id', 'left')
            ->group_by('ref_people.id, ref_people_branches.whatsapp_group,ref_people_branches.contract');

        if(!empty($branchId)) {
            $BaseQuery
                ->group_start()
                    ->group_start()
                    ->where('ref_people.type_user', "NON USER")
                    ->where('ref_people_branches.id_branch', $branchId)
                    ->where('ref_people.type', self::$TYPE_CUSTOMER)
                    ->group_end()
                    ->or_group_start()
                        ->where('ref_people.type_user', "USER")
                        ->where('prv_user_roles.id_branch', $branchId)
                        ->where('ref_people.type', self::$TYPE_CUSTOMER)
                    ->group_end()
                    ->or_where('ref_people.type!=', self::$TYPE_CUSTOMER)
                ->group_end();
        }

        if (!$withTrashed) {
            $BaseQuery->where('ref_people.is_deleted', false);
        }

        return $BaseQuery;
    }

    /**
     * Get people base query.
     *
     * @param null $branchId
     * @param bool $withTrashed
     * @return CI_DB_query_builder
     */
    public function getBaseQueryCustomer($branchId = null, $withTrashed = false)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $BaseQuery = $this->db->select([
                        'ref_people.*',
                        'ref_people_users.id_user',
                    ])
                    ->from($this->table)
                    ->join('ref_people_branches', 'ref_people_branches.id_customer = ref_people.id', 'left')
                    ->join('ref_branches', 'ref_people_branches.id_branch = ref_branches.id', 'left')
                    ->join('ref_people_users', 'ref_people_users.id_people = ref_people.id', 'left')
                    ->join('prv_user_roles', 'prv_user_roles.id_user = ref_people_users.id_user', 'left')
                    ->join('ref_branches AS branches', 'prv_user_roles.id_branch = branches.id', 'left')
                    ->group_by('ref_people.id');

        if(!empty($branchId)) {
            $BaseQuery
                ->group_start()
                    ->group_start()
                    ->where('ref_people.type_user', "NON USER")
                    ->where('ref_people_branches.id_branch', $branchId)
                    ->where('ref_people.type', self::$TYPE_CUSTOMER)
                    ->group_end()
                    ->or_group_start()
                        ->where('ref_people.type_user', "USER")
                        ->where('prv_user_roles.id_branch', $branchId)
                        ->where('ref_people.type', self::$TYPE_CUSTOMER)
                    ->group_end()
                ->group_end();
        }

        if (!$withTrashed) {
            $BaseQuery->where('ref_people.is_deleted', false);
        }

        return $BaseQuery;
    }

    /**
     * Get all people with or without deleted records.
     *
     * @param array $filters
     * @param bool $withTrashed
     * @return mixed
     */
    public function getAll($filters = [], $withTrashed = false)
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $viewCustomer = AuthorizationModel::isAuthorized(PERMISSION_CUSTOMER_VIEW);
        $viewSupplier = AuthorizationModel::isAuthorized(PERMISSION_SUPPLIER_VIEW);

        if ($start < 0) {
            $people = $this->getBaseQuery($branchId);
            if (!$withTrashed) {
                $people->where('ref_people.is_deleted', false);
            }
            if (!empty($filters['contract']) && !is_null($filters['contract'])) {
                $people->where('ref_people_branches.contract is not null');
                $people->where('ref_people.outbound_type','ACCOUNT RECEIVABLE');
            }
            return $people->get()->result_array();
        }

        // alias column name by index for sorting data table library
        $columnOrder = ["id", "type", "no_person", "name", "contact", "email", "id"];
        $columnSort = $columnOrder[$column];

        $this->db->start_cache();
        $people = $this->getBaseQuery($branchId)
            ->group_start()
            ->like('ref_people.type', trim($search))
            ->or_like('ref_people.no_person', trim($search))
            ->or_like('ref_people.name', trim($search))
            ->or_like('ref_people.contact', trim($search))
            ->or_like('ref_people.email', trim($search))
            ->group_end();

        if (!$viewCustomer) {
            $people->where('ref_people.type!=', self::$TYPE_CUSTOMER);
        }

        if (!$viewSupplier) {
            $people->where('ref_people.type!=', self::$TYPE_SUPPLIER);
        }

        if (!$withTrashed) {
            $people->where('ref_people.is_deleted', false);
        }
        $this->db->stop_cache();

        $peopleTotal = $this->db->count_all_results();
        $peoplePage = $people->limit($length, $start)->order_by($columnSort, $sort);
        $peopleData = $peoplePage->get()->result_array();

        foreach ($peopleData as &$row) {
            $row['no'] = ++$start;
        }

        return [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($peopleData),
            "recordsFiltered" => $peopleTotal,
            "data" => $peopleData
        ];
    }

    /**
     * Get people by type.
     *
     * @param $type
     * @param bool $withTrashed
     * @return mixed
     */
    public function getByType($type = null, $withTrashed = false)
    {
        $branchId = get_active_branch('id');

        $people = $this->getBaseQuery($branchId);

        if (!empty($type)) {
            if (is_array($type)) {
                $people->where_in('ref_people.type', $type);
            } else {
                $people->where('ref_people.type', $type);
            }
        }

        if (!$withTrashed) {
            $people->where('ref_people.is_deleted', false);
        }

        return $people->get()->result_array();
    }

    /**
     * Get people by type and branch.
     *
     * @param null $type
     * @param null $branch
     * @param bool $withTrashed
     * @param null $branchType
     * @return mixed
     */
    public function getByTypeBranch($type = null, $branch = null, $withTrashed = false, $branchType = null)
    {
        $people = $this->getBaseQuery($branch);

        if (!empty($type)) {
            if (is_array($type)) {
                $people->where_in('ref_people.type', $type);
            } else {
                $people->where('ref_people.type', $type);
            }
        }

        if (!$withTrashed) {
            $people->where('ref_people.is_deleted', false);
        }

        if (!empty($branchType) && !is_null($branchType)) {
            $people->where('ref_branches.branch_type', $branchType);
        }

        return $people->get()->result_array();
    }

    /**
     * Get person data by name.
     *
     * @param null $name
     * @param null $type
     * @param null $page
     * @param bool $withTrashed
     * @return mixed
     */
    public function getPersonByName($name, $type = null, $page = null, $withTrashed = false)
    {
        $branchId = get_active_branch('id');

        $this->db->start_cache();

        $people = $this->getBaseQuery($branchId)->order_by('name');

        if (is_array($name)) {
            $people->where_in('ref_people.name', $name);
        } else {
            $people->like('ref_people.name', trim($name));
        }

        if (!$withTrashed) {
            $people->where('ref_people.is_deleted', false);
        }

        if (!empty($type)) {
            $people->where('ref_people.type', $type);
        }

        $this->db->stop_cache();

        if (!empty($page) || $page != 0) {
            $peopleTotal = $people->count_all_results();
            $peoplePage = $people->limit(10, 10 * ($page - 1));
            $peopleData = $peoplePage->get()->result_array();

            return [
                'results' => $peopleData,
                'total_count' => $peopleTotal
            ];
        }

        $peopleData = $people->get()->result_array();

        $this->db->flush_cache();

        return $peopleData;
    }


    /**
     * Get person data by name.
     *
     * @param null $name
     * @param null $type
     * @param null $page
     * @param bool $withTrashed
     * @return mixed
     */
    public function getPersonByNameAllBranch($name, $type = null, $page = null, $withTrashed = false)
    {
        $this->db->start_cache();

        $people = $this->getBaseQuery()->order_by('name');

        if (is_array($name)) {
            $people->where_in('ref_people.name', $name);
        } else {
            $people->like('ref_people.name', trim($name));
        }

        if (!$withTrashed) {
            $people->where('ref_people.is_deleted', false);
        }

        if (!empty($type)) {
            $people->where('ref_people.type', $type);
        }

        $this->db->stop_cache();

        if (!empty($page) || $page != 0) {
            $peopleTotal = $people->count_all_results();
            $peoplePage = $people->limit(10, 10 * ($page - 1));
            $peopleData = $peoplePage->get()->result_array();

            return [
                'results' => $peopleData,
                'total_count' => $peopleTotal
            ];
        }

        $peopleData = $people->get()->result_array();

        $this->db->flush_cache();

        return $peopleData;
    }

     /**
     * Get person data by name.
     *
     * @param null $name
     * @param null $type
     * @param null $page
     * @param bool $withTrashed
     * @return mixed
     */
    public function getPersonByNamePerBranch($name, $type = null, $page = null, $withTrashed = false)
    {

        $branchId = get_active_branch('id');

        $this->db->start_cache();

        $people = $this->getBaseQuery($branchId)->order_by('name');

        if (is_array($name)) {
            $people->where_in('ref_people.name', $name);
        } else {
            $people->like('ref_people.name', trim($name));
        }

        if (!$withTrashed) {
            $people->where('ref_people.is_deleted', false);
        }

        if (!empty($type)) {
            $people->where('ref_people.type', $type);
        }
        
        $this->db->stop_cache();

        if (!empty($page) || $page != 0) {
            $peopleTotal = $people->count_all_results();
            $peoplePage = $people->limit(10, 10 * ($page - 1));
            $peopleData = $peoplePage->get()->result_array();

            return [
                'results' => $peopleData,
                'total_count' => $peopleTotal
            ];
        }

        $peopleData = $people->get()->result_array();

        $this->db->flush_cache();

        return $peopleData;
    }

    /**
     * Get auto number for people.
     * @param string $type
     * @return string
     */
    public function getAutoNumberPerson($type)
    {
        $orderData = $this->db->query("
            SELECT CAST(RIGHT(no_person, 3) AS UNSIGNED) + 1 AS order_number 
            FROM ref_people
            WHERE SUBSTRING(no_person, 1, 3) = '$type'
            ORDER BY id DESC LIMIT 1
            ");
        $orderPad = '001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 3, '0', STR_PAD_LEFT);
        }
        return $orderPad;
    }

    public function getPeopleByIdCustomerIdBranch($customerId,$branchId){
        $people = $this->db->select('ref_people.*, ref_branches.branch, ref_people_users.id_user, ref_people_branches.whatsapp_group,ref_people_branches.contract')
            ->distinct()
            ->from($this->table)
            ->join('(select * from ref_people_branches where ref_people_branches.id_branch="'.$branchId.'") AS ref_people_branches', 'ref_people_branches.id_customer = ref_people.id', 'left')
            ->join('ref_branches', 'ref_people_branches.id_branch = ref_branches.id', 'left')
            ->join('ref_people_users', 'ref_people_users.id_people = ref_people.id', 'left')
            ->join('prv_user_roles', 'prv_user_roles.id_user = ref_people_users.id_user', 'left')
            ->join('ref_branches AS branches', 'prv_user_roles.id_branch = branches.id', 'left')
            ->group_by('ref_people.id, ref_people_branches.whatsapp_group,ref_people_branches.contract')
            ->where('ref_people.id',$customerId);
        return $people->get()->row_array();
    }

    /**
     * Get customers by active storage.
     *
     * @param array $filters
     * @return array|array[]|CI_DB_query_builder
     */
    public function getCustomersByActiveStorage($filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $baseStorageCapacityQuery = $this->db->from('ref_customer_storage_capacities')->where('is_deleted', false);

        if (key_exists('effective_date_active', $filters) && !empty($filters['effective_date_active'])) {
            $baseStorageCapacityQuery->where('ref_customer_storage_capacities.effective_date<=', format_date($filters['effective_date_active']));
        }
        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseStorageCapacityQuery->where_in('ref_customer_storage_capacities.id_customer', $filters['customer']);
        }
        $baseStorageCapacityQuery = $baseStorageCapacityQuery->get_compiled_select();

        $baseQuery = $this->db
            ->select([
                'ref_people.id',
                'ref_people.name',
                'ref_people.id_parent',
                'IF(ref_customer_storage_capacities.effective_date > CURDATE(), 
                    "PENDING", 
                    IF((ref_customer_storage_capacities.effective_date <= CURDATE() AND MIN(next_storage_capacities.effective_date) > CURDATE()) OR MIN(next_storage_capacities.effective_date) IS NULL, 
                        IF(ref_customer_storage_capacities.expired_date < CURDATE(), "EXPIRED", "ACTIVE"), 
                        "EXPIRED"
                    )
                ) AS status_storage_capacity',
                'ref_customer_storage_capacities.id_branch',
                'ref_customer_storage_capacities.effective_date',
                'ref_customer_storage_capacities.expired_date',
                'ref_customer_storage_capacities.warehouse_capacity',
                '(ref_customer_storage_capacities.warehouse_capacity /100 * 6) AS warehouse_capacity_teus',
                'ref_customer_storage_capacities.yard_capacity',
                '(ref_customer_storage_capacities.yard_capacity /100 * 6) AS yard_capacity_teus',
                'ref_customer_storage_capacities.covered_yard_capacity',
                '(ref_customer_storage_capacities.covered_yard_capacity /100 * 6) AS covered_yard_capacity_teus',
            ])
            ->distinct()
            ->from($this->table)
            ->join("({$baseStorageCapacityQuery}) AS ref_customer_storage_capacities", 'ref_customer_storage_capacities.id_customer = ' . $this->table . '.id')
            ->join("({$baseStorageCapacityQuery}) AS next_storage_capacities", 'next_storage_capacities.id_customer = ref_people.id AND next_storage_capacities.effective_date > ref_customer_storage_capacities.effective_date', 'left')
            ->where([
                'ref_people.type' => 'CUSTOMER',
                //'ref_people.id_parent IS NULL' => null
            ])
            ->group_by($this->table . '.id, ref_customer_storage_capacities.id_branch, ref_customer_storage_capacities.effective_date, ref_customer_storage_capacities.expired_date, warehouse_capacity, yard_capacity, covered_yard_capacity')
            ->having('status_storage_capacity', 'ACTIVE');

        if ($filters['customer_storage_self'] ?? false) {
            // allow to get its customer storage without parent
        } else {
            $baseQuery->where('ref_people.id_parent IS NULL');
        }

        if (!empty($branchId)) {
            $baseQuery->where('ref_customer_storage_capacities.id_branch', $branchId);
        }

        if (key_exists('query', $filters) && $filters['query']) {
            return $baseQuery;
        }

        return $baseQuery->get()->result_array();
    }
}
