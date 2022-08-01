<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CustomerModel extends MY_Model
{
    protected $table = 'ref_customers';

    public static $GENDER_NONE = 'NONE';
    public static $GENDER_MALE = 'MALE';
    public static $GENDER_FEMALE = 'FEMALE';

    /**
     * CustomerModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }


    /**
     * Get all customer with or without deleted records.
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
            $customer = $this->getBaseQuery();
            if (!$withTrashed) {
                $customer->where('ref_customers.is_deleted', false);
            }
            if (!empty($filters['contract']) && !is_null($filters['contract'])) {
                $customer->where('ref_customers_branches.contract is not null');
                $customer->where('ref_customers.outbound_type','ACCOUNT RECEIVABLE');
            }
            return $customer->get()->result_array();
        }

        // alias column name by index for sorting data table library
        $columnOrder = ["id", "identity_number", "name", "contact", "email", "id"];
        $columnSort = $columnOrder[$column];

        $this->db->start_cache();
        $customer = $this->getBaseQuery()
            ->group_start()
            ->like('ref_customers.identity_number', trim($search))
            ->or_like('ref_customers.name', trim($search))
            ->or_like('ref_customers.contact', trim($search))
            ->or_like('ref_customers.email', trim($search))
            ->group_end();

        if (!$withTrashed) {
            $customer->where('ref_customers.is_deleted', false);
        }
        $this->db->stop_cache();

        $customerTotal = $this->db->count_all_results();
        $customerPage = $customer->limit($length, $start)->order_by($columnSort, $sort);
        $customerData = $customerPage->get()->result_array();

        foreach ($customerData as &$row) {
            $row['no'] = ++$start;
        }

        return [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($customerData),
            "recordsFiltered" => $customerTotal,
            "data" => $customerData
        ];
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

        $customer = $this->getBaseQuery($branchId)->order_by('name');

        if (is_array($name)) {
            $customer->where_in('ref_customers.name', $name);
        } else {
            $customer->like('ref_customers.name', trim($name));
        }

        if (!$withTrashed) {
            $customer->where('ref_customers.is_deleted', false);
        }

        if (!empty($type)) {
            $customer->where('ref_customers.type', $type);
        }

        $this->db->stop_cache();

        if (!empty($page) || $page != 0) {
            $customerTotal = $customer->count_all_results();
            $customerPage = $customer->limit(10, 10 * ($page - 1));
            $customerData = $customerPage->get()->result_array();

            return [
                'results' => $customerData,
                'total_count' => $customerTotal
            ];
        }

        $customerData = $customer->get()->result_array();

        $this->db->flush_cache();

        return $customerData;
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

        $customer = $this->getBaseQuery()->order_by('name');

        if (is_array($name)) {
            $customer->where_in('ref_customers.name', $name);
        } else {
            $customer->like('ref_customers.name', trim($name));
        }

        if (!$withTrashed) {
            $customer->where('ref_customers.is_deleted', false);
        }

        if (!empty($type)) {
            $customer->where('ref_customers.type', $type);
        }

        $this->db->stop_cache();

        if (!empty($page) || $page != 0) {
            $customerTotal = $customer->count_all_results();
            $customerPage = $customer->limit(10, 10 * ($page - 1));
            $customerData = $customerPage->get()->result_array();

            return [
                'results' => $customerData,
                'total_count' => $customerTotal
            ];
        }

        $customerData = $customer->get()->result_array();

        $this->db->flush_cache();

        return $customerData;
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

        $customer = $this->getBaseQuery($branchId)->order_by('name');

        if (is_array($name)) {
            $customer->where_in('ref_customers.name', $name);
        } else {
            $customer->like('ref_customers.name', trim($name));
        }

        if (!$withTrashed) {
            $customer->where('ref_customers.is_deleted', false);
        }

        if (!empty($type)) {
            $customer->where('ref_customers.type', $type);
        }
        
        $this->db->stop_cache();

        if (!empty($page) || $page != 0) {
            $customerTotal = $customer->count_all_results();
            $customerPage = $customer->limit(10, 10 * ($page - 1));
            $customerData = $customerPage->get()->result_array();

            return [
                'results' => $customerData,
                'total_count' => $customerTotal
            ];
        }

        $customerData = $customer->get()->result_array();

        $this->db->flush_cache();

        return $customerData;
    }

    /**
     * Get auto number for customer.
     * @param string $type
     * @return string
     */
    public function getAutoNumberPerson($type)
    {
        $orderData = $this->db->query("
            SELECT CAST(RIGHT(no_person, 3) AS UNSIGNED) + 1 AS order_number 
            FROM ref_customers
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

    public function getCustomerByIdCustomerIdBranch($customerId,$branchId){
        $customer = $this->db->select('ref_customers.*, ref_branches.branch, ref_customers_users.id_user, ref_customers_branches.whatsapp_group,ref_customers_branches.contract')
            ->distinct()
            ->from($this->table)
            ->join('(select * from ref_customers_branches where ref_customers_branches.id_branch="'.$branchId.'") AS ref_customers_branches', 'ref_customers_branches.id_customer = ref_customers.id', 'left')
            ->join('ref_branches', 'ref_customers_branches.id_branch = ref_branches.id', 'left')
            ->join('ref_customers_users', 'ref_customers_users.id_customer = ref_customers.id', 'left')
            ->join('prv_user_roles', 'prv_user_roles.id_user = ref_customers_users.id_user', 'left')
            ->join('ref_branches AS branches', 'prv_user_roles.id_branch = branches.id', 'left')
            ->group_by('ref_customers.id, ref_customers_branches.whatsapp_group,ref_customers_branches.contract')
            ->where('ref_customers.id',$customerId);
        return $customer->get()->row_array();
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

        $baseStorageCapacityQuery = $this->db->from('ref_customers_storage_capacities')->where('is_deleted', false);

        if (key_exists('effective_date_active', $filters) && !empty($filters['effective_date_active'])) {
            $baseStorageCapacityQuery->where('ref_customers_storage_capacities.effective_date<=', format_date($filters['effective_date_active']));
        }
        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseStorageCapacityQuery->where_in('ref_customers_storage_capacities.id_customer', $filters['customer']);
        }
        $baseStorageCapacityQuery = $baseStorageCapacityQuery->get_compiled_select();

        $baseQuery = $this->db
            ->select([
                'ref_customers.id',
                'ref_customers.name',
                'ref_customers.id_parent',
                'IF(ref_customers_storage_capacities.effective_date > CURDATE(), 
                    "PENDING", 
                    IF((ref_customers_storage_capacities.effective_date <= CURDATE() AND MIN(next_storage_capacities.effective_date) > CURDATE()) OR MIN(next_storage_capacities.effective_date) IS NULL, 
                        IF(ref_customers_storage_capacities.expired_date < CURDATE(), "EXPIRED", "ACTIVE"), 
                        "EXPIRED"
                    )
                ) AS status_storage_capacity',
                'ref_customers_storage_capacities.id_branch',
                'ref_customers_storage_capacities.effective_date',
                'ref_customers_storage_capacities.expired_date',
                'ref_customers_storage_capacities.warehouse_capacity',
                '(ref_customers_storage_capacities.warehouse_capacity /100 * 6) AS warehouse_capacity_teus',
                'ref_customers_storage_capacities.yard_capacity',
                '(ref_customers_storage_capacities.yard_capacity /100 * 6) AS yard_capacity_teus',
                'ref_customers_storage_capacities.covered_yard_capacity',
                '(ref_customers_storage_capacities.covered_yard_capacity /100 * 6) AS covered_yard_capacity_teus',
            ])
            ->distinct()
            ->from($this->table)
            ->join("({$baseStorageCapacityQuery}) AS ref_customers_storage_capacities", 'ref_customers_storage_capacities.id_customer = ' . $this->table . '.id')
            ->join("({$baseStorageCapacityQuery}) AS next_storage_capacities", 'next_storage_capacities.id_customer = ref_customers.id AND next_storage_capacities.effective_date > ref_customers_storage_capacities.effective_date', 'left')
            ->where([
                'ref_customers.type' => 'CUSTOMER',
                //'ref_customers.id_parent IS NULL' => null
            ])
            ->group_by($this->table . '.id, ref_customers_storage_capacities.id_branch, ref_customers_storage_capacities.effective_date, ref_customers_storage_capacities.expired_date, warehouse_capacity, yard_capacity, covered_yard_capacity')
            ->having('status_storage_capacity', 'ACTIVE');

        if ($filters['customer_storage_self'] ?? false) {
            // allow to get its customer storage without parent
        } else {
            $baseQuery->where('ref_customers.id_parent IS NULL');
        }

        if (!empty($branchId)) {
            $baseQuery->where('ref_customers_storage_capacities.id_branch', $branchId);
        }

        if (key_exists('query', $filters) && $filters['query']) {
            return $baseQuery;
        }

        return $baseQuery->get()->result_array();
    }
}
