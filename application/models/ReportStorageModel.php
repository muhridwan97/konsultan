<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class ReportStorageModel
 * @property PeopleModel $people
 */
class ReportStorageModel extends MY_Model
{

    /**
     * ReportStorageModel constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('PeopleModel', 'people');
    }

    /**
     * Get query container loading.
     *
     * @param array $filters
     * @return CI_DB_query_builder
     */
    private function getContainerLoadingQuery($filters = [])
    {
        $baseQueryContainerLoading = $this->db
            ->select([
                'handlings.id_booking',
                'work_orders.id AS id_work_order',
                'ref_containers.no_container',
                'SUM(work_order_goods.quantity) AS total_goods_loaded_quantity',
            ])
            ->from('handlings')
            ->join('ref_people AS customers', 'customers.id = handlings.id_customer')
            ->join('work_orders', 'work_orders.id_handling = handlings.id')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container')
            // some stripping does not have reference to direct work order container
            //->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('work_order_goods', 'work_order_goods.id_work_order_container = work_order_containers.id')
            //->join('work_order_goods', 'work_order_goods.id_work_order_container = work_order_containers.id
            //    OR (work_order_goods.id_work_order_container IS NULL AND work_order_goods.id_work_order = work_orders.id)', 'inner', false)
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->where([
                'ref_handling_types.multiplier_goods !=' => 0
            ])
            ->group_by('work_orders.id, no_container');

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQueryContainerLoading->group_start();
            if (is_array($filters['customer'])) {
                $baseQueryContainerLoading->where_in('customers.id', $filters['customer']);
                if (key_exists('customer_include_member', $filters) && $filters['customer_include_member']) {
                    $baseQueryContainerLoading->or_where_in('customers.id_parent', $filters['customer']);
                }
            } else {
                $baseQueryContainerLoading->where('customers.id', $filters['customer']);
                if (key_exists('customer_include_member', $filters) && $filters['customer_include_member']) {
                    $baseQueryContainerLoading->or_where('customers.id_parent', $filters['customer']);
                }
            }
            $baseQueryContainerLoading->group_end();
        }

        $baseQueryContainerLoading = $baseQueryContainerLoading->get_compiled_select();

        return $this->db
            ->select([
                'base_work_orders.*',
                'work_order_goods.id_goods',
                'work_order_goods.id_unit'
            ])
            ->from("({$baseQueryContainerLoading}) AS base_work_orders")
            ->join('work_order_goods', 'work_order_goods.id_work_order = base_work_orders.id_work_order 
                AND work_order_goods.ex_no_container <=> base_work_orders.no_container', 'left', false);
    }

    /**
     * Get query goods loading.
     *
     * @param array $filters
     * @return CI_DB_query_builder
     */
    private function getGoodsLoadingQuery($filters = [])
    {
        $baseQueryGoodsLoading = $this->db
            ->select([
                'handlings.id_booking',
                'work_orders.id AS id_work_order',
                'work_order_goods.ex_no_container',
                'IFNULL(work_orders.space, 0) AS used_space',
                'SUM(work_order_goods.quantity) AS total_goods_loaded_quantity',
            ])
            ->from('handlings')
            ->join('ref_people AS customers', 'customers.id = handlings.id_customer')
            ->join('work_orders', 'work_orders.id_handling = handlings.id')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join("(
                SELECT id_work_order, COUNT(id) AS total_container 
                FROM work_order_containers 
                GROUP BY id_work_order
            ) AS total_containers", 'total_containers.id_work_order = work_orders.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->where([
                'ref_handling_types.multiplier_goods' => 1
            ])
            ->group_start()
                ->group_start()
                    ->where('work_order_goods.ex_no_container IS NULL')
                    ->or_where('work_order_goods.ex_no_container', "")
                ->group_end()
                // job that has ex no container but has not container data treat as LCL
                ->or_group_start()
                    ->where('work_order_goods.ex_no_container IS NOT NULL')
                    ->where('work_order_goods.ex_no_container !=', "")
                    ->where('IFNULL(total_container, 0) =', 0) //  to exclude stripping
                ->group_end()
            ->group_end()
            ->group_by('work_orders.id, work_order_goods.ex_no_container');

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQueryGoodsLoading->group_start();
            if (is_array($filters['customer'])) {
                $baseQueryGoodsLoading->where_in('customers.id', $filters['customer']);
                if (key_exists('customer_include_member', $filters) && $filters['customer_include_member']) {
                    $baseQueryGoodsLoading->or_where_in('customers.id_parent', $filters['customer']);
                }
            } else {
                $baseQueryGoodsLoading->where('customers.id', $filters['customer']);
                if (key_exists('customer_include_member', $filters) && $filters['customer_include_member']) {
                    $baseQueryGoodsLoading->or_where('customers.id_parent', $filters['customer']);
                }
            }
            $baseQueryGoodsLoading->group_end();
        }
        $baseQueryGoodsLoading = $baseQueryGoodsLoading->get_compiled_select();

        /**
         * TODO: the limitation if inbound with same booking id contains multiple job with same goods
         * BI001
         * UL001 -> space 20m2
         *  - A 5PCS -> 10m2
         *  - B 5PCS -> 10m2
         * UL002 -> space 30m2
         *  - A 5PCS -> 30m2
         * LO001 x
         *  - A 3PCS -> ? -> 3PCS from where? UL001 or UL002 or combined, cannot find reference job space
         *
         * CHEAT:
         * find max used_space and total item (will work if the job NOT contain any other goods like only A)
         * in example above UL001 will fail the calculation of B
         *
         * OTHER OPTION:
         * LCL track the inbound by adding "ex no container", so outbound will not mixed
         *
         * REVERT:
         * to revert the tricks, remove MAX(used_space), MAX(total_goods_loaded_quantity),
         * and remove the group by id_booking, id_goods, id_unit
         */
        return $this->db
            ->select([
                'base_work_orders.id_booking',
                'base_work_orders.id_work_order',
                'MAX(base_work_orders.used_space) AS used_space',
                'MAX(base_work_orders.total_goods_loaded_quantity) AS total_goods_loaded_quantity',
                'work_order_goods.id_goods',
                'work_order_goods.id_unit',
                'work_order_goods.ex_no_container'
            ])
            ->from("({$baseQueryGoodsLoading}) AS base_work_orders")
            ->join('work_order_goods', 'work_order_goods.id_work_order = base_work_orders.id_work_order AND work_order_goods.ex_no_container <=> base_work_orders.ex_no_container', 'left', false)
            ->group_by('id_booking, id_goods, id_unit, ex_no_container');
    }

    /**
     * Get work order complete date compared with active storage query.
     *
     * @param array $filters
     * @return CI_DB_query_builder
     */
    private function getWorkOrderActiveStorageCapacityQuery($filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $customerKey = 'customers.id';
        $useGroupCapacity = !($filters['customer_storage_self'] ?? false);
        if (key_exists('customer_include_member', $filters) && $filters['customer_include_member'] && $useGroupCapacity) {
            $customerKey = 'IFNULL(customers.id_parent, customers.id)';
        }

        // TODO: should compatible with multiple customers (find out work order should match with which storage capacity)
        if (!empty($filters['customer'] ?? '')) {
            if (!is_array($filters['customer'])) {
                $checkCustomer = $this->people->getById($filters['customer']);
                if (empty($checkCustomer['id_parent'])) {
                    $customerKey = 'IFNULL(customers.id_parent, customers.id)';
                }
            }
        }

        $baseQuery = $this->db
            ->select([
                'work_orders.id',
                'ref_customer_storage_capacities.effective_date',
                'IFNULL(ref_customer_storage_capacities.warehouse_capacity, 0) AS warehouse_capacity',
                'IFNULL(ref_customer_storage_capacities.yard_capacity, 0) AS yard_capacity',
                'IFNULL(ref_customer_storage_capacities.covered_yard_capacity, 0) AS covered_yard_capacity',
                "IF(ref_customer_storage_capacities.effective_date > DATE(work_orders.completed_at), 
                    'PENDING', 
                    IF((ref_customer_storage_capacities.effective_date <= DATE(work_orders.completed_at)
                            AND MIN(next_storage_capacities.effective_date) > DATE(work_orders.completed_at)) 
                            OR MIN(next_storage_capacities.effective_date) IS NULL, 
                        IF(ref_customer_storage_capacities.expired_date < CURDATE(), 'EXPIRED', 'ACTIVE'), 
                        'EXPIRED'
                    )
                ) AS status_storage_capacity"
            ])
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer')
            ->join('(SELECT * FROM ref_customer_storage_capacities WHERE is_deleted = false' . (!empty($branchId) ? " AND id_branch = {$branchId}" : '') . ') AS ref_customer_storage_capacities', "
                ref_customer_storage_capacities.id_customer = {$customerKey}
                    AND ref_customer_storage_capacities.effective_date <= DATE(work_orders.completed_at)
            ", 'left')
            ->join('(SELECT * FROM ref_customer_storage_capacities WHERE is_deleted = false' . (!empty($branchId) ? " AND id_branch = {$branchId}" : '') . ') AS next_storage_capacities', "
                next_storage_capacities.id_customer = {$customerKey}
                    AND next_storage_capacities.effective_date > ref_customer_storage_capacities.effective_date
            ", 'left')
            ->group_by('work_orders.id, 
                ref_customer_storage_capacities.effective_date, 
                ref_customer_storage_capacities.warehouse_capacity,
                ref_customer_storage_capacities.yard_capacity,
                ref_customer_storage_capacities.covered_yard_capacity')
            ->having('status_storage_capacity', 'ACTIVE');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        return $baseQuery;
    }

    /**
     * Get LCL goods stock usage.
     * -----------------------------------------------
     * customer     warehouse   yard    covered yard
     * -----------------------------------------------
     * PT Customer  56          0       23
     * PT Arya      0           100     250,2
     * PT Wijaya    100         230     50
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getGoodsStockUsage($filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $baseQueryGoodsLoading = $this->getGoodsLoadingQuery($filters)->get_compiled_select();

        $customerKey = 'customers.id';
        if (key_exists('customer_include_member', $filters) && $filters['customer_include_member']) {
            $customerKey = 'IFNULL(customers.id_parent, customers.id)';
        }

        $ruleUsage = "
            ref_handling_types.multiplier_goods 
            * (
                work_order_goods.quantity / work_order_good_loads.total_goods_loaded_quantity 
                * work_order_good_loads.used_space
            )
        ";

        $baseQuery = $this->db
            ->select([
                $customerKey . ' AS id_customer',
                'customer_data.name AS customer_name',
                "SUM(IF(ref_warehouses.type = 'WAREHOUSE', {$ruleUsage}, 0)) AS used_warehouse_storage",
                "SUM(IF(ref_warehouses.type = 'YARD' OR ref_warehouses.type = 'FIELD', {$ruleUsage}, 0)) AS used_yard_storage",
                "SUM(IF(ref_warehouses.type = 'COVERED YARD', {$ruleUsage}, 0)) AS used_covered_yard_storage",
            ])
            ->from('bookings')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(bookings.id_booking, bookings.id)', 'left')
            ->join('ref_people AS customers', 'customers.id = booking_inbounds.id_customer')
            ->join('ref_people AS customer_data', 'customer_data.id = ' . $customerKey)
            ->join('handlings', 'handlings.id_booking = bookings.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id', 'left')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id', 'left')
            // TODO: LCL outbound need to find out which job they are come from, this join potentially produce invalid multiple rows
            ->join("({$baseQueryGoodsLoading}) AS work_order_good_loads", 'work_order_good_loads.id_booking = booking_inbounds.id
                AND work_order_good_loads.id_goods = work_order_goods.id_goods
                AND work_order_good_loads.id_unit = work_order_goods.id_unit
                AND work_order_good_loads.ex_no_container <=> work_order_goods.ex_no_container', 'inner', false)
            ->join("(
                SELECT id_work_order, COUNT(id) AS total_container 
                FROM work_order_containers 
                GROUP BY id_work_order
            ) AS total_containers", 'total_containers.id_work_order = work_orders.id', 'left')
            ->join('ref_positions', 'ref_positions.id = work_order_goods.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left')
            ->where([
                'work_orders.status' => "COMPLETED",
                'handlings.status' => "APPROVED",
                'work_orders.is_deleted' => false,
                'handlings.is_deleted' => false,
                'bookings.is_deleted' => false,
                'ref_handling_types.multiplier_goods !=' => 0,
                'ref_warehouses.id IS NOT NULL' => null,
            ])
            // check LCL
            ->group_start()
                ->group_start()
                    ->where('work_order_goods.ex_no_container IS NULL')
                    ->or_where('work_order_goods.ex_no_container', "")
                ->group_end()
                // job that has ex no container but has not container data treat as LCL
                ->or_group_start()
                    ->where('work_order_goods.ex_no_container IS NOT NULL')
                    ->where('work_order_goods.ex_no_container !=', "")
                    ->where('IFNULL(total_container, 0) =', 0)  //  to exclude stripping
                ->group_end()
            ->group_end()
            ->group_by($customerKey);

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if (key_exists('data', $filters) && !empty($filters['data'])) {
            switch ($filters['data']) {
                case 'all-data':
                case 'all':
                    break;
                case 'stocked':
                case 'stock-only':
                case 'stock':
                default:
                    $baseQuery
                        ->having('used_warehouse_storage>', 0)
                        ->or_having('used_yard_storage>', 0)
                        ->or_having('used_covered_yard_storage>', 0);
                    break;
                case 'empty-stock':
                    $baseQuery
                        ->having('used_warehouse_storage', 0)
                        ->having('used_yard_storage', 0)
                        ->having('used_covered_yard_storage', 0);
                    break;
                case 'negative-stock':
                    $baseQuery
                        ->having('used_warehouse_storage<', 0)
                        ->or_having('used_yard_storage<', 0)
                        ->or_having('used_covered_yard_storage<', 0);
                    break;
            }
        }

        if (key_exists('stock_date', $filters) && !empty($filters['stock_date'])) {
            $baseQuery->where('DATE(work_orders.completed_at)<=', format_date($filters['stock_date']));
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->group_start();
            $baseQuery->where_in('customers.id', $filters['customer']);
            if (key_exists('customer_include_member', $filters) && $filters['customer_include_member']) {
                $baseQuery->or_where_in('customers.id_parent', $filters['customer']);
            }
            $baseQuery->group_end();
        }

        if (key_exists('warehouse_type', $filters) && !empty($filters['warehouse_type'])) {
            $baseQuery->where('ref_warehouses.type', $filters['warehouse_type']);
        }

        if (key_exists('branch_type', $filters) && !empty($filters['branch_type'])) {
            $baseQuery
                ->join('ref_branches', 'ref_branches.id = booking_inbounds.id_branch')
                ->where('ref_branches.branch_type', $filters['branch_type']);
        }

        if (key_exists('outbound_type', $filters) && !empty($filters['outbound_type'])) {
            $baseQuery->group_start();
            $baseQuery->where('customer_data.outbound_type', $filters['outbound_type']);
            if ($filters['outbound_type'] == 'CASH AND CARRY') {
                $baseQuery->or_where('customer_data.outbound_type IS NULL');
            }
            $baseQuery->group_end();
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get containerized goods stock usage.
     * -----------------------------------------------
     * customer     warehouse   yard    covered yard
     * -----------------------------------------------
     * PT Customer  56          0       23
     * PT Arya      0           100     250,2
     * PT Wijaya    100         230     50
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getContainerizedGoodsStockUsage($filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $baseQueryContainerLoading = $this->getContainerLoadingQuery($filters)->get_compiled_select();

        $customerKey = 'customers.id';
        if (key_exists('customer_include_member', $filters) && $filters['customer_include_member']) {
            $customerKey = 'IFNULL(customers.id_parent, customers.id)';
        }

        $ruleUsage = "
            ref_handling_types.multiplier_goods 
            * (
                work_order_goods.quantity / work_order_containers.total_goods_loaded_quantity 
                * IF(ref_containers.size = 20, 17, IF(ref_containers.type = 'HC', 34, 32))
            )
        ";

        $baseQuery = $this->db
            ->select([
                $customerKey . ' AS id_customer',
                'customer_data.name AS customer_name',
                "SUM(IF(ref_warehouses.type = 'WAREHOUSE', {$ruleUsage}, 0)) AS used_warehouse_storage",
                "SUM(IF(ref_warehouses.type = 'YARD' OR ref_warehouses.type = 'FIELD', {$ruleUsage}, 0)) AS used_yard_storage",
                "SUM(IF(ref_warehouses.type = 'COVERED YARD', {$ruleUsage}, 0)) AS used_covered_yard_storage",
            ])
            ->from('bookings')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(bookings.id_booking, bookings.id)', 'left')
            ->join('ref_people AS customers', 'customers.id = booking_inbounds.id_customer')
            ->join('ref_people AS customer_data', 'customer_data.id = ' . $customerKey)
            ->join('handlings', 'handlings.id_booking = bookings.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id', 'left')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id', 'left')
            ->join("({$baseQueryContainerLoading}) AS work_order_containers", 'work_order_containers.id_booking = booking_inbounds.id 
                    AND work_order_containers.no_container <=> work_order_goods.ex_no_container
                    AND work_order_containers.id_goods = work_order_goods.id_goods
                    AND work_order_containers.id_unit = work_order_goods.id_unit
                    AND work_order_containers.id_unit = work_order_goods.id_unit', 'inner', false)
            ->join("(
                SELECT id_work_order, COUNT(id) AS total_container 
                FROM work_order_containers 
                GROUP BY id_work_order
            ) AS total_containers", 'total_containers.id_work_order = work_orders.id', 'left')
            ->join('ref_containers', 'ref_containers.no_container = TRIM(work_order_goods.ex_no_container)', 'left')
            ->join('ref_positions', 'ref_positions.id = work_order_goods.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left')
            ->where([
                'work_orders.status' => "COMPLETED",
                'handlings.status' => "APPROVED",
                'work_orders.is_deleted' => false,
                'handlings.is_deleted' => false,
                'bookings.is_deleted' => false,
                //'ref_handling_types.multiplier_goods !=' => 0,
                'ref_warehouses.id IS NOT NULL' => null,
                'work_order_goods.ex_no_container IS NOT NULL' => null,
            ])
            // inbound from containerized goods or outbound only, any other inbound job will be treat as LCL
            ->group_start()
                ->where('ref_handling_types.multiplier_goods', -1)
                ->or_group_start()
                    ->where('ref_handling_types.multiplier_goods', 1)
                    ->where('IFNULL(total_container, 0) >', 0)
                ->group_end()
            ->group_end()
            ->group_by($customerKey);

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if (key_exists('data', $filters) && !empty($filters['data'])) {
            switch ($filters['data']) {
                case 'all-data':
                case 'all':
                    break;
                case 'stocked':
                case 'stock-only':
                case 'stock':
                default:
                    $baseQuery
                        ->having('used_warehouse_storage>', 0)
                        ->or_having('used_yard_storage>', 0)
                        ->or_having('used_covered_yard_storage>', 0);
                    break;
                case 'empty-stock':
                    $baseQuery
                        ->having('used_warehouse_storage', 0)
                        ->having('used_yard_storage', 0)
                        ->having('used_covered_yard_storage', 0);
                    break;
                case 'negative-stock':
                    $baseQuery
                        ->having('used_warehouse_storage<', 0)
                        ->or_having('used_yard_storage<', 0)
                        ->or_having('used_covered_yard_storage<', 0);
                    break;
            }
        }

        if (key_exists('stock_date', $filters) && !empty($filters['stock_date'])) {
            $baseQuery->where('DATE(work_orders.completed_at)<=', format_date($filters['stock_date']));
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->group_start();
            $baseQuery->where_in('customers.id', $filters['customer']);
            if (key_exists('customer_include_member', $filters) && $filters['customer_include_member']) {
                $baseQuery->or_where_in('customers.id_parent', $filters['customer']);
            }
            $baseQuery->group_end();
        }

        if (key_exists('warehouse_type', $filters) && !empty($filters['warehouse_type'])) {
            $baseQuery->where('ref_warehouses.type', $filters['warehouse_type']);
        }

        if (key_exists('branch_type', $filters) && !empty($filters['branch_type'])) {
            $baseQuery
                ->join('ref_branches', 'ref_branches.id = booking_inbounds.id_branch')
                ->where('ref_branches.branch_type', $filters['branch_type']);
        }

        if (key_exists('outbound_type', $filters) && !empty($filters['outbound_type'])) {
            $baseQuery->group_start();
            $baseQuery->where('customer_data.outbound_type', $filters['outbound_type']);
            if ($filters['outbound_type'] == 'CASH AND CARRY') {
                $baseQuery->or_where('customer_data.outbound_type IS NULL');
            }
            $baseQuery->group_end();
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get customer stock balance.
     * Note: We separate function LCL and containerized to distinguish how to calculate source of spaces.
     * -----------------------------------------------
     * customer     warehouse   yard    covered yard
     * -----------------------------------------------
     * PT Customer  56          0       23
     * PT Arya      0           100     250,2
     * PT Wijaya    100         230     50
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getCustomerBalanceStorage($filters = [])
    {
        // get storage capacity
        $customerActiveStorage = array_merge($filters, ['query' => true, 'effective_date_active' => get_if_exist($filters, 'stock_date')]);
        $baseStorageCapacityQuery = $this->people->getCustomersByActiveStorage($customerActiveStorage)->get_compiled_select();

        $customerKey = 'customers.id';
        $useGroupCapacity = !($filters['customer_storage_self'] ?? false);
        if (key_exists('customer_include_member', $filters) && $filters['customer_include_member'] && $useGroupCapacity) {
            $customerKey = 'IFNULL(customers.id_parent, customers.id)';
        }

        $goodsStockUsage = $this->getGoodsStockUsage($filters);
        $containerizedStockUsage = $this->getContainerizedGoodsStockUsage($filters);

        $baseQuery = $this->db
            ->select([
                $customerKey . ' AS id_customer',
                'customer_data.name AS customer_name',
                'customer_active_capacities.effective_date',
                'customer_active_capacities.expired_date',
                'IFNULL(customer_active_capacities.warehouse_capacity, 0) AS warehouse_capacity',
                'IFNULL(customer_active_capacities.yard_capacity, 0) AS yard_capacity',
                'IFNULL(customer_active_capacities.covered_yard_capacity, 0) AS covered_yard_capacity',
            ])
            ->from('ref_people AS customers')
            ->join('ref_people AS customer_data', 'customer_data.id = ' . $customerKey)
            ->join("({$baseStorageCapacityQuery}) AS customer_active_capacities", 'customer_active_capacities.id = ' . $customerKey, 'left')
            ->group_by($customerKey . ', warehouse_capacity, yard_capacity, covered_yard_capacity');

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->group_start();
            if (is_array($filters['customer'])) {
                $baseQuery->where_in('customers.id', $filters['customer']);
                if (key_exists('customer_include_member', $filters) && $filters['customer_include_member'] && $useGroupCapacity) {
                    $baseQuery->or_where_in('customers.id_parent', $filters['customer']);
                }
            } else {
                $baseQuery->where('customers.id', $filters['customer']);
                if (key_exists('customer_include_member', $filters) && $filters['customer_include_member'] && $useGroupCapacity) {
                    $baseQuery->or_where('customers.id_parent', $filters['customer']);
                }
            }
            $baseQuery->group_end();
        }

        $storageUsages = $baseQuery->get()->result_array();

        foreach ($storageUsages as &$storageUsage) {
            $storageUsage['used_warehouse_storage'] = 0;
            $storageUsage['used_yard_storage'] = 0;
            $storageUsage['used_covered_yard_storage'] = 0;
            foreach ($goodsStockUsage as $itemUsage) {
                if ($storageUsage['id_customer'] == $itemUsage['id_customer']) {
                    $storageUsage['used_warehouse_storage'] += $itemUsage['used_warehouse_storage'];
                    $storageUsage['used_yard_storage'] += $itemUsage['used_yard_storage'];
                    $storageUsage['used_covered_yard_storage'] += $itemUsage['used_covered_yard_storage'];
                }
            }
            foreach ($containerizedStockUsage as $containerizedItemUsage) {
                if ($storageUsage['id_customer'] == $containerizedItemUsage['id_customer']) {
                    $storageUsage['used_warehouse_storage'] += $containerizedItemUsage['used_warehouse_storage'];
                    $storageUsage['used_yard_storage'] += $containerizedItemUsage['used_yard_storage'];
                    $storageUsage['used_covered_yard_storage'] += $containerizedItemUsage['used_covered_yard_storage'];
                }
            }

            $storageUsage['balance_warehouse_capacity'] = $storageUsage['warehouse_capacity'] - $storageUsage['used_warehouse_storage'];
            $storageUsage['balance_yard_capacity'] = $storageUsage['yard_capacity'] - $storageUsage['used_yard_storage'];
            $storageUsage['balance_covered_yard_capacity'] = $storageUsage['covered_yard_capacity'] - $storageUsage['used_covered_yard_storage'];
            $storageUsage['used_warehouse_percent'] = $storageUsage['warehouse_capacity'] == 0 ? (round($storageUsage['used_warehouse_storage'], 3) > 0 ? 100 : 0) : $storageUsage['used_warehouse_storage'] / $storageUsage['warehouse_capacity'] * 100;
            $storageUsage['used_yard_percent'] = $storageUsage['yard_capacity'] == 0 ? (round($storageUsage['used_yard_storage'], 3) > 0 ? 100 : 0) : $storageUsage['used_yard_storage'] / $storageUsage['yard_capacity'] * 100;
            $storageUsage['used_covered_yard_percent'] = $storageUsage['covered_yard_capacity'] == 0 ? (round($storageUsage['used_covered_yard_storage'], 3) > 0 ? 100 : 0) : $storageUsage['used_covered_yard_storage'] / $storageUsage['covered_yard_capacity'] * 100;

            // round to minimal amount, if usage very small < 0.00499999 consider to 0
            $storageUsage['used_warehouse_storage'] = round($storageUsage['used_warehouse_storage'], 3);
            $storageUsage['used_yard_storage'] = round($storageUsage['used_yard_storage'], 3);
            $storageUsage['used_covered_yard_storage'] = round($storageUsage['used_covered_yard_storage'], 3);
        }

        return $storageUsages;
    }

    /**
     * Get LCL goods storage by activity.
     * -------------------------------------------------------------------------
     * customer     date        type        goods       qty  ex         in  out
     * -------------------------------------------------------------------------
     * PT Customer  2021-01-01  WAREHOUSE   Computer    10   LCL        10  0
     * PT Customer  2021-01-02  WAREHOUSE   Mouse       5    LCL        0   2
     *
     * @param array $filters
     * @return array
     */
    public function getGoodsStorageActivityUsage($filters = [])
    {
        // querying transaction
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();
        $filters['branch'] = $branchId;

        $this->load->driver('cache', ['adapter' => 'file']);
        $cacheKey = 'storage-lcl-activity-' . md5(json_encode($filters));
        $cachedTransactions = $this->cache->get($cacheKey);
        if ($cachedTransactions !== false) {
            return $cachedTransactions;
        }

        $baseQueryGoodsLoading = $this->getGoodsLoadingQuery($filters)->get_compiled_select();
        $baseQueryActiveStorage = $this->getWorkOrderActiveStorageCapacityQuery($filters)->get_compiled_select();

        $baseQuery = $this->db
            ->select([
                'booking_inbounds.id_customer',
                'customers.name AS customer_name',
                'customers.id_parent',
                'booking_inbounds.id_branch',
                'bookings.no_reference',
                'booking_inbounds.no_reference AS no_reference_inbound',
                'IF(ref_handling_types.multiplier_goods = 1, "INBOUND", "OUTBOUND") AS activity_type',
                'ref_handling_types.handling_type',
                'work_orders.id AS id_work_order',
                'work_orders.no_work_order',
                'DATE(work_orders.completed_at) AS date_activity',
                'ref_warehouses.type AS warehouse_type',
                'ref_goods.id AS id_goods',
                'ref_goods.name AS goods_name',
                'ref_units.unit',
                '1 AS is_lcl',
                'IF(work_order_goods.ex_no_container IS NULL OR work_order_goods.ex_no_container = "", "LCL", work_order_goods.ex_no_container) AS no_container',
                '"" AS size',
                'work_order_good_loads.total_goods_loaded_quantity',
                'work_order_goods.quantity',
                "work_order_good_loads.used_space AS container_capacity",
                'ref_customer_storage_capacities.effective_date AS effective_date_capacity',
                'ref_customer_storage_capacities.warehouse_capacity',
                'ref_customer_storage_capacities.yard_capacity',
                'ref_customer_storage_capacities.covered_yard_capacity',
                "IF(ref_handling_types.multiplier_goods = 1, work_order_goods.quantity / work_order_good_loads.total_goods_loaded_quantity * work_order_good_loads.used_space, 0) AS inbound_storage",
                "IF(ref_handling_types.multiplier_goods = -1, work_order_goods.quantity / work_order_good_loads.total_goods_loaded_quantity * work_order_good_loads.used_space, 0) AS outbound_storage",
                "ref_handling_types.multiplier_goods * (work_order_goods.quantity / work_order_good_loads.total_goods_loaded_quantity * work_order_good_loads.used_space) AS used_storage",
            ])
            ->from('bookings')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(bookings.id_booking, bookings.id)', 'left')
            ->join('ref_people AS customers', 'customers.id = booking_inbounds.id_customer')
            ->join('handlings', 'handlings.id_booking = bookings.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id', 'left')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            // TODO: LCL outbound need to find out which job they are come from, this join potentially produce invalid multiple rows
            ->join("({$baseQueryGoodsLoading}) AS work_order_good_loads", 'work_order_good_loads.id_booking = booking_inbounds.id
                AND work_order_good_loads.id_goods = work_order_goods.id_goods
	            AND work_order_good_loads.id_unit = work_order_goods.id_unit
	            AND work_order_good_loads.ex_no_container <=> work_order_goods.ex_no_container', 'inner', false)
            ->join("(
                SELECT id_work_order, COUNT(id) AS total_container 
                FROM work_order_containers 
                GROUP BY id_work_order
            ) AS total_containers", 'total_containers.id_work_order = work_orders.id', 'left')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit')
            ->join('ref_positions', 'ref_positions.id = work_order_goods.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left')
            ->join("(({$baseQueryActiveStorage})) AS ref_customer_storage_capacities", 'ref_customer_storage_capacities.id = work_orders.id', 'left')
            ->where([
                'work_orders.status' => "COMPLETED",
                'handlings.status' => "APPROVED",
                'work_orders.is_deleted' => false,
                'handlings.is_deleted' => false,
                'bookings.is_deleted' => false,
                'ref_handling_types.multiplier_goods !=' => 0,
            ])
            // check LCL
            ->group_start()
                ->group_start()
                    ->where('work_order_goods.ex_no_container IS NULL')
                    ->or_where('work_order_goods.ex_no_container', "")
                ->group_end()
                // job that has ex no container but has not container data treat as LCL
                ->or_group_start()
                    ->where('work_order_goods.ex_no_container IS NOT NULL')
                    ->where('work_order_goods.ex_no_container !=', "")
                    ->where('IFNULL(total_container, 0) =', 0) //  to exclude stripping
                ->group_end()
            ->group_end()
            ->order_by('work_orders.completed_at');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $baseQuery->where('DATE(work_orders.completed_at)>=', format_date($filters['date_from']));
        }

        if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
            $baseQuery->where('DATE(work_orders.completed_at)<=', format_date($filters['date_to']));
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->group_start();
            if (is_array($filters['customer'])) {
                $baseQuery->where_in('customers.id', $filters['customer']);
                if (key_exists('customer_include_member', $filters) && $filters['customer_include_member']) {
                    $baseQuery->or_where_in('customers.id_parent', $filters['customer']);
                }
            } else {
                $baseQuery->where('customers.id', $filters['customer']);
                if (key_exists('customer_include_member', $filters) && $filters['customer_include_member']) {
                    $baseQuery->or_where('customers.id_parent', $filters['customer']);
                }
            }
            $baseQuery->group_end();
        }

        if (key_exists('booking', $filters) && !empty($filters['booking'])) {
            $baseQuery->where('booking_inbounds.id', $filters['booking']);
        }

        if (key_exists('goods', $filters) && !empty($filters['goods'])) {
            $baseQuery->where('ref_goods.id', $filters['goods']);
        }

        if (key_exists('unit', $filters) && !empty($filters['unit'])) {
            $baseQuery->where('ref_units.id', $filters['unit']);
        }

        if (key_exists('warehouse_type', $filters) && !empty($filters['warehouse_type'])) {
            $baseQuery->where('ref_warehouses.type', $filters['warehouse_type']);
        }

        if (key_exists('branch_type', $filters) && !empty($filters['branch_type'])) {
            $baseQuery
                ->join('ref_branches', 'ref_branches.id = booking_inbounds.id_branch')
                ->where('ref_branches.branch_type', $filters['branch_type']);
        }

        if (key_exists('outbound_type', $filters) && !empty($filters['outbound_type'])) {
            $baseQuery->group_start();
            $baseQuery->where('customers.outbound_type', $filters['outbound_type']);
            if ($filters['outbound_type'] == 'CASH AND CARRY') {
                $baseQuery->or_where('customers.outbound_type IS NULL');
            }
            $baseQuery->group_end();
        }

        $goodsStorages = $baseQuery->get()->result_array();

        if (key_exists('transaction_only', $filters) && $filters['transaction_only']) {
            $this->cache->save($cacheKey, $goodsStorages, 60);
            return $goodsStorages;
        }

        // get beginning balance
        $beginningBalance = 0;
        $beginningBalanceUsed = 0;
        $lastCapacity = 0;
        $capacityKey = '';
        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            // get last balance
            $filters['stock_date'] = date('d F Y', strtotime($filters['date_from'] . ' -1 day'));
            $lastBalance = $this->getCustomerBalanceStorage($filters);
            if (!empty($lastBalance)) $lastBalance = $lastBalance[0];

            if (key_exists('warehouse_type', $filters) && !empty($filters['warehouse_type'])) {
                if ($filters['warehouse_type'] == 'WAREHOUSE') {
                    $capacityKey = 'warehouse_capacity';
                    $lastCapacity = $lastBalance['warehouse_capacity'] ?? 0;
                    $beginningBalance = $lastBalance['balance_warehouse_capacity'] ?? 0;
                    $beginningBalanceUsed = $lastBalance['used_warehouse_storage'] ?? 0;
                }
                if ($filters['warehouse_type'] == 'YARD') {
                    $capacityKey = 'yard_capacity';
                    $lastCapacity = $lastBalance['yard_capacity'] ?? 0;
                    $beginningBalance = $lastBalance['balance_yard_capacity'] ?? 0;
                    $beginningBalanceUsed = $lastBalance['used_yard_storage'] ?? 0;
                }
                if ($filters['warehouse_type'] == 'COVERED YARD') {
                    $capacityKey = 'covered_yard_capacity';
                    $lastCapacity = $lastBalance['covered_yard_capacity'] ?? 0;
                    $beginningBalance = $lastBalance['balance_covered_yard_capacity'] ?? 0;
                    $beginningBalanceUsed = $lastBalance['used_covered_yard_storage'] ?? 0;
                }
            }
        }

        $transactions = [];

        $transactions[] = [
            'capacity' => $lastCapacity,
            'stock_date' => get_if_exist($filters, 'stock_date', ''),
            'inbound_storage' => $lastCapacity > 0 ? 0 : $lastCapacity,
            'outbound_storage' => $lastCapacity > 0 ? $lastCapacity : 0,
            'left_storage' => $beginningBalance,
            'used_storage' => $beginningBalanceUsed,
            'row_type' => 'beginning-balance',
        ];

        foreach ($goodsStorages as $itemStorage) {
            // change storage in the middle
            if ($itemStorage[$capacityKey] != $lastCapacity) {
                $additionalInbound = $itemStorage[$capacityKey] < $lastCapacity ? ($lastCapacity - $itemStorage[$capacityKey]) : 0;
                $additionalOutbound = $lastCapacity < $itemStorage[$capacityKey] ? ($itemStorage[$capacityKey] - $lastCapacity) : 0;
                $beginningBalance += $additionalOutbound - $additionalInbound;
                // changing capacity not affecting storage usage
                // $beginningBalanceUsed += $additionalInbound - $additionalOutbound;
                $capacity = get_if_exist($itemStorage, $capacityKey, 0);
                $transactions[] = [
                    'capacity' => $capacity,
                    'effective_date_capacity' => $itemStorage['effective_date_capacity'],
                    'inbound_storage' => $additionalInbound,
                    'outbound_storage' => $additionalOutbound,
                    'left_storage' => $beginningBalance,
                    'used_storage' => $beginningBalanceUsed,
                    'row_type' => 'change-capacity',
                ];
                $lastCapacity = $itemStorage[$capacityKey];
            }
            $beginningBalance += $itemStorage['outbound_storage'] - $itemStorage['inbound_storage'];
            $beginningBalanceUsed += $itemStorage['inbound_storage'] - $itemStorage['outbound_storage'];
            $itemStorage['left_storage'] = $beginningBalance;
            $itemStorage['used_storage'] = $beginningBalanceUsed;
            $itemStorage['row_type'] = 'transaction';

            $transactions[] = $itemStorage;
        }

        $this->cache->save($cacheKey, $transactions, 60);

        return $transactions;
    }

    /**
     * Get containerized goods storage by activity.
     * -------------------------------------------------------------------------
     * customer     date        type        goods       qty  ex         in  out
     * -------------------------------------------------------------------------
     * PT Customer  2021-01-01  WAREHOUSE   Computer    10   LCL        10  0
     * PT Customer  2021-01-02  WAREHOUSE   Mouse       5    LCL        0   2
     *
     * @param array $filters
     * @return array
     */
    public function getContainerizedGoodsStorageActivityUsage($filters = [])
    {
        // querying transaction
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();
        $filters['branch'] = $branchId;

        $this->load->driver('cache', ['adapter' => 'file']);
        $cacheKey = 'storage-containerized-activity-' . md5(json_encode($filters));
        $cachedTransactions = $this->cache->get($cacheKey);
        if ($cachedTransactions !== false) {
            return $cachedTransactions;
        }

        $baseQueryContainerLoading = $this->getContainerLoadingQuery($filters)->get_compiled_select();
        $baseQueryActiveStorage = $this->getWorkOrderActiveStorageCapacityQuery($filters)->get_compiled_select();

        $baseQuery = $this->db
            ->select([
                'booking_inbounds.id_customer',
                'customers.name AS customer_name',
                'customers.id_parent',
                'booking_inbounds.id_branch',
                'bookings.no_reference',
                'booking_inbounds.no_reference AS no_reference_inbound',
                'IF(ref_handling_types.multiplier_goods = 1, "INBOUND", "OUTBOUND") AS activity_type',
                'ref_handling_types.handling_type',
                'work_orders.id AS id_work_order',
                'work_orders.no_work_order',
                'DATE(work_orders.completed_at) AS date_activity',
                'ref_warehouses.type AS warehouse_type',
                'ref_goods.id AS id_goods',
                'ref_goods.name AS goods_name',
                'ref_units.unit',
                '0 AS is_lcl',
                'ref_containers.no_container',
                'ref_containers.size',
                'work_order_containers.total_goods_loaded_quantity',
                'work_order_goods.quantity',
                "IF(ref_containers.size = 20, 17, IF(ref_containers.type = 'HC', 34, 32)) AS container_capacity",
                'ref_customer_storage_capacities.effective_date AS effective_date_capacity',
                'ref_customer_storage_capacities.warehouse_capacity',
                'ref_customer_storage_capacities.yard_capacity',
                'ref_customer_storage_capacities.covered_yard_capacity',
                "IF(ref_handling_types.multiplier_goods = 1, work_order_goods.quantity / work_order_containers.total_goods_loaded_quantity * IF(ref_containers.size = 20, 17, IF(ref_containers.type = 'HC', 34, 32)), 0) AS inbound_storage",
                "IF(ref_handling_types.multiplier_goods = -1, work_order_goods.quantity / work_order_containers.total_goods_loaded_quantity * IF(ref_containers.size = 20, 17, IF(ref_containers.type = 'HC', 34, 32)), 0) AS outbound_storage",
                "ref_handling_types.multiplier_goods * work_order_goods.quantity / work_order_containers.total_goods_loaded_quantity * IF(ref_containers.size = 20, 17, IF(ref_containers.type = 'HC', 34, 32)) AS used_storage",
            ])
            ->from('bookings')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(bookings.id_booking, bookings.id)', 'left')
            ->join('ref_people AS customers', 'customers.id = booking_inbounds.id_customer')
            ->join('handlings', 'handlings.id_booking = bookings.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id', 'left')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join("({$baseQueryContainerLoading}) AS work_order_containers", 'work_order_containers.id_booking = booking_inbounds.id 
                    AND work_order_containers.no_container <=> work_order_goods.ex_no_container
                    AND work_order_containers.id_goods = work_order_goods.id_goods
                    AND work_order_containers.id_unit = work_order_goods.id_unit', 'inner', false)
            ->join("(
                SELECT id_work_order, COUNT(id) AS total_container 
                FROM work_order_containers 
                GROUP BY id_work_order
            ) AS total_containers", 'total_containers.id_work_order = work_orders.id', 'left')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit')
            ->join('ref_containers', 'ref_containers.no_container = TRIM(work_order_goods.ex_no_container)', 'left')
            ->join('ref_positions', 'ref_positions.id = work_order_goods.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left')
            ->join("(({$baseQueryActiveStorage})) AS ref_customer_storage_capacities", 'ref_customer_storage_capacities.id = work_orders.id', 'left')
            ->where([
                'work_orders.status' => "COMPLETED",
                'handlings.status' => "APPROVED",
                'work_orders.is_deleted' => false,
                'handlings.is_deleted' => false,
                'bookings.is_deleted' => false,
                //'ref_handling_types.multiplier_goods !=' => 0,
            ])
            ->group_start()
                ->where('ref_handling_types.multiplier_goods', -1)
                ->or_group_start()
                    ->where('ref_handling_types.multiplier_goods', 1)
                    ->where('IFNULL(total_container, 0) >', 0)
                ->group_end()
            ->group_end()
            ->order_by('work_orders.completed_at');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $baseQuery->where('DATE(work_orders.completed_at)>=', format_date($filters['date_from']));
        }

        if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
            $baseQuery->where('DATE(work_orders.completed_at)<=', format_date($filters['date_to']));
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->group_start();
            if (is_array($filters['customer'])) {
                $baseQuery->where_in('customers.id', $filters['customer']);
                if (key_exists('customer_include_member', $filters) && $filters['customer_include_member']) {
                    $baseQuery->or_where_in('customers.id_parent', $filters['customer']);
                }
            } else {
                $baseQuery->where('customers.id', $filters['customer']);
                if (key_exists('customer_include_member', $filters) && $filters['customer_include_member']) {
                    $baseQuery->or_where('customers.id_parent', $filters['customer']);
                }
            }
            $baseQuery->group_end();
        }

        if (key_exists('goods', $filters) && !empty($filters['goods'])) {
            $baseQuery->where('ref_goods.id', $filters['goods']);
        }

        if (key_exists('unit', $filters) && !empty($filters['unit'])) {
            $baseQuery->where('ref_units.id', $filters['unit']);
        }

        if (key_exists('ex_no_container', $filters) && !empty($filters['ex_no_container'])) {
            $baseQuery->where('ref_containers.no_container', $filters['ex_no_container']);
        }

        if (key_exists('warehouse_type', $filters) && !empty($filters['warehouse_type'])) {
            $baseQuery->where('ref_warehouses.type', $filters['warehouse_type']);
        }

        if (key_exists('branch_type', $filters) && !empty($filters['branch_type'])) {
            $baseQuery
                ->join('ref_branches', 'ref_branches.id = booking_inbounds.id_branch')
                ->where('ref_branches.branch_type', $filters['branch_type']);
        }

        if (key_exists('outbound_type', $filters) && !empty($filters['outbound_type'])) {
            $baseQuery->group_start();
            $baseQuery->where('customers.outbound_type', $filters['outbound_type']);
            if ($filters['outbound_type'] == 'CASH AND CARRY') {
                $baseQuery->or_where('customers.outbound_type IS NULL');
            }
            $baseQuery->group_end();
        }

        $goodsStorages = $baseQuery->get()->result_array();

        if (key_exists('transaction_only', $filters) && $filters['transaction_only']) {
            $this->cache->save($cacheKey, $goodsStorages, 60);
            return $goodsStorages;
        }

        // get beginning balance
        $beginningBalance = 0;
        $beginningBalanceUsed = 0;
        $lastCapacity = 0;
        $capacityKey = '';
        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            // get last balance
            $filters['stock_date'] = date('d F Y', strtotime($filters['date_from'] . ' -1 day'));
            $lastBalance = $this->getCustomerBalanceStorage($filters);
            if (!empty($lastBalance)) $lastBalance = $lastBalance[0];

            if (key_exists('warehouse_type', $filters) && !empty($filters['warehouse_type'])) {
                if ($filters['warehouse_type'] == 'WAREHOUSE') {
                    $capacityKey = 'warehouse_capacity';
                    $lastCapacity = $lastBalance['warehouse_capacity'] ?? 0;
                    $beginningBalance = $lastBalance['balance_warehouse_capacity'] ?? 0;
                    $beginningBalanceUsed = $lastBalance['used_warehouse_storage'] ?? 0;
                }
                if ($filters['warehouse_type'] == 'YARD') {
                    $capacityKey = 'yard_capacity';
                    $lastCapacity = $lastBalance['yard_capacity'] ?? 0;
                    $beginningBalance = $lastBalance['balance_yard_capacity'] ?? 0;
                    $beginningBalanceUsed = $lastBalance['used_yard_storage'] ?? 0;
                }
                if ($filters['warehouse_type'] == 'COVERED YARD') {
                    $capacityKey = 'covered_yard_capacity';
                    $lastCapacity = $lastBalance['covered_yard_capacity'] ?? 0;
                    $beginningBalance = $lastBalance['balance_covered_yard_capacity'] ?? 0;
                    $beginningBalanceUsed = $lastBalance['used_covered_yard_storage'] ?? 0;
                }
            }
        }

        $transactions = [];

        $transactions[] = [
            'capacity' => $lastCapacity,
            'stock_date' => get_if_exist($filters, 'stock_date', ''),
            'inbound_storage' => $lastCapacity > 0 ? 0 : $lastCapacity,
            'outbound_storage' => $lastCapacity > 0 ? $lastCapacity : 0,
            'left_storage' => $beginningBalance,
            'used_storage' => $beginningBalanceUsed,
            'row_type' => 'beginning-balance',
        ];

        foreach ($goodsStorages as $itemStorage) {
            // change storage in the middle
            if ($itemStorage[$capacityKey] != $lastCapacity) {
                $additionalInbound = $itemStorage[$capacityKey] < $lastCapacity ? ($lastCapacity - $itemStorage[$capacityKey]) : 0;
                $additionalOutbound = $lastCapacity < $itemStorage[$capacityKey] ? ($itemStorage[$capacityKey] - $lastCapacity) : 0;
                $beginningBalance += $additionalOutbound - $additionalInbound;
                // changing capacity not affecting storage usage
                // $beginningBalanceUsed += $additionalInbound - $additionalOutbound;
                $capacity = get_if_exist($itemStorage, $capacityKey, 0);
                $transactions[] = [
                    'capacity' => $capacity,
                    'effective_date_capacity' => $itemStorage['effective_date_capacity'],
                    'inbound_storage' => $additionalInbound,
                    'outbound_storage' => $additionalOutbound,
                    'left_storage' => $beginningBalance,
                    'used_storage' => $beginningBalanceUsed,
                    'row_type' => 'change-capacity',
                ];
                $lastCapacity = $itemStorage[$capacityKey];
            }
            $beginningBalance += $itemStorage['outbound_storage'] - $itemStorage['inbound_storage'];
            $beginningBalanceUsed += $itemStorage['inbound_storage'] - $itemStorage['outbound_storage'];
            $itemStorage['left_storage'] = $beginningBalance;
            $itemStorage['used_storage'] = $beginningBalanceUsed;
            $itemStorage['row_type'] = 'transaction';

            $transactions[] = $itemStorage;
        }

        $this->cache->save($cacheKey, $transactions, 60);

        return $transactions;
    }

    /**
     * Get joined containerized and LCL storage movement.
     * Note: We separate function LCL and containerized to distinguish how to calculate source of spaces.
     * --------------------------------------------------------------------------------------
     * customer     date        type        goods       qty  ex         in  out balance used
     * --------------------------------------------------------------------------------------
     * Balance      2020-12-31                                                  100
     * PT Customer  2021-01-01  WAREHOUSE   Computer    10   LCL        10  0   110     110
     * PT Customer  2021-01-02  WAREHOUSE   Mouse       5    TCLU34283  0   2   108     108
     *
     * @param array $filters
     * @return array|mixed
     */
    public function getStorageActivityUsage($filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();
        $filters['branch'] = $branchId;

        $this->load->driver('cache', ['adapter' => 'file']);
        $cacheKey = 'storage-activity-' . md5(json_encode($filters));
        $cachedTransactions = $this->cache->get($cacheKey);
        if ($cachedTransactions !== false) {
            return $cachedTransactions;
        }

        $beginningBalance = 0;
        $beginningBalanceUsed = 0;
        $lastCapacity = 0;
        $capacityKey = '';
        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            // get last balance
            $filters['stock_date'] = date('d F Y', strtotime($filters['date_from'] . ' -1 day'));
            $lastBalance = $this->getCustomerBalanceStorage($filters);
            if (!empty($lastBalance)) $lastBalance = $lastBalance[0];

            if (key_exists('warehouse_type', $filters) && !empty($filters['warehouse_type'])) {
                if ($filters['warehouse_type'] == 'WAREHOUSE') {
                    $capacityKey = 'warehouse_capacity';
                    $lastCapacity = $lastBalance['warehouse_capacity'] ?? 0;
                    $beginningBalance = $lastBalance['balance_warehouse_capacity'] ?? 0;
                    $beginningBalanceUsed = $lastBalance['used_warehouse_storage'] ?? 0;
                }
                if ($filters['warehouse_type'] == 'YARD') {
                    $capacityKey = 'yard_capacity';
                    $lastCapacity = $lastBalance['yard_capacity'] ?? 0;
                    $beginningBalance = $lastBalance['balance_yard_capacity'] ?? 0;
                    $beginningBalanceUsed = $lastBalance['used_yard_storage'] ?? 0;
                }
                if ($filters['warehouse_type'] == 'COVERED YARD') {
                    $capacityKey = 'covered_yard_capacity';
                    $lastCapacity = $lastBalance['covered_yard_capacity'] ?? 0;
                    $beginningBalance = $lastBalance['balance_covered_yard_capacity'] ?? 0;
                    $beginningBalanceUsed = $lastBalance['used_covered_yard_storage'] ?? 0;
                }
            }
        }

        $filters['transaction_only'] = true;

        $lcl = $this->getGoodsStorageActivityUsage($filters);
        $containerized = $this->getContainerizedGoodsStorageActivityUsage($filters);

        $goodsStorages = array_merge($lcl, $containerized);
        usort($goodsStorages, function($a, $b) {
            return $a['date_activity'] <=> $b['date_activity'];
        });

        $transactions = [];

        $transactions[] = [
            'capacity' => $lastCapacity,
            'stock_date' => format_date(get_if_exist($filters, 'stock_date', '')),
            'inbound_storage' => $lastCapacity > 0 ? 0 : $lastCapacity,
            'outbound_storage' => $lastCapacity > 0 ? $lastCapacity : 0,
            'left_storage' => $beginningBalance,
            'used_storage' => $beginningBalanceUsed,
            'over_space' => $beginningBalanceUsed > $lastCapacity ? ($beginningBalanceUsed - $lastCapacity) : 0,
            'row_type' => 'beginning-balance',
        ];

        foreach ($goodsStorages as $itemStorage) {
            // change storage in the middle
            if ($itemStorage[$capacityKey] != $lastCapacity) {
                $additionalInbound = $itemStorage[$capacityKey] < $lastCapacity ? ($lastCapacity - $itemStorage[$capacityKey]) : 0;
                $additionalOutbound = $lastCapacity < $itemStorage[$capacityKey] ? ($itemStorage[$capacityKey] - $lastCapacity) : 0;
                $beginningBalance += $additionalOutbound - $additionalInbound;
                // changing capacity not affecting storage usage
                // $beginningBalanceUsed += $additionalInbound - $additionalOutbound;
                $capacity = get_if_exist($itemStorage, $capacityKey, 0);
                $transactions[] = [
                    'capacity' => $capacity,
                    'effective_date_capacity' => $itemStorage['effective_date_capacity'],
                    'inbound_storage' => $additionalInbound,
                    'outbound_storage' => $additionalOutbound,
                    'left_storage' => $beginningBalance,
                    'used_storage' => $beginningBalanceUsed,
                    'over_space' => $beginningBalanceUsed > $lastCapacity ? ($beginningBalanceUsed - $lastCapacity) : 0,
                    'row_type' => 'change-capacity',
                ];
                $lastCapacity = $itemStorage[$capacityKey];
            }
            $beginningBalance += $itemStorage['outbound_storage'] - $itemStorage['inbound_storage'];
            $beginningBalanceUsed += $itemStorage['inbound_storage'] - $itemStorage['outbound_storage'];
            $itemStorage['left_storage'] = $beginningBalance;
            $itemStorage['used_storage'] = $beginningBalanceUsed;
            $itemStorage['over_space'] = $beginningBalanceUsed > $lastCapacity ? ($beginningBalanceUsed - $lastCapacity) : 0;
            $itemStorage['row_type'] = 'transaction';

            $transactions[] = $itemStorage;
        }

        $this->cache->save($cacheKey, $transactions, 60);

        return $transactions;
    }

    /**
     * Get detail goods stock usage.
     * ------------------------------------------------------------------
     * customer     type        goods       ex      date            used
     * ------------------------------------------------------------------
     * PT Customer  WAREHOUSE   Computer    LCL     2021-01-01      10
     * PT Arya      WAREHOUSE   Mouse       LCL     2021-01-02      5
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getDetailGoodsStock($filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $baseQueryGoodsLoading = $this->getGoodsLoadingQuery($filters)->get_compiled_select();

        $ruleUsage = "
            ref_handling_types.multiplier_goods 
            * (
                work_order_goods.quantity / work_order_good_loads.total_goods_loaded_quantity 
                * work_order_good_loads.used_space
            )
        ";

        $baseQuery = $this->db
            ->select([
                'customers.id AS id_customer',
                'customers.name AS customer_name',
                'booking_inbounds.id AS id_booking',
                'booking_inbounds.no_reference',
                'ref_warehouses.type AS warehouse_type',
                'work_order_goods.id_goods',
                'ref_goods.name AS goods_name',
                'ref_units.id AS id_unit',
                'ref_units.unit',
                'IF(work_order_goods.ex_no_container IS NULL OR work_order_goods.ex_no_container = "", "LCL", work_order_goods.ex_no_container) AS ex_no_container',
                '1 AS is_lcl',
                'MAX(work_orders.completed_at) AS inbound_date',
                "IFNULL(SUM({$ruleUsage}), 0) AS used_storage",
            ])
            ->from('bookings')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(bookings.id_booking, bookings.id)', 'left')
            ->join('ref_people AS customers', 'customers.id = booking_inbounds.id_customer')
            ->join('handlings', 'handlings.id_booking = bookings.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id', 'left')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id', 'left')
            ->join("({$baseQueryGoodsLoading}) AS work_order_good_loads", 'work_order_good_loads.id_booking = booking_inbounds.id
                AND work_order_good_loads.id_goods = work_order_goods.id_goods
                AND work_order_good_loads.id_unit = work_order_goods.id_unit
                AND work_order_good_loads.ex_no_container <=> work_order_goods.ex_no_container', 'inner', false)
            ->join("(
                SELECT id_work_order, COUNT(id) AS total_container 
                FROM work_order_containers 
                GROUP BY id_work_order
            ) AS total_containers", 'total_containers.id_work_order = work_orders.id', 'left')
            ->join('ref_containers', 'ref_containers.no_container = TRIM(work_order_goods.ex_no_container)', 'left')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods', 'left')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit', 'left')
            ->join('ref_positions', 'ref_positions.id = work_order_goods.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left')
            ->where([
                'work_orders.status' => "COMPLETED",
                'handlings.status' => "APPROVED",
                'work_orders.is_deleted' => false,
                'handlings.is_deleted' => false,
                'bookings.is_deleted' => false,
                'ref_handling_types.multiplier_goods !=' => 0,
                'ref_warehouses.id IS NOT NULL' => null,
            ])
            // check LCL
            ->group_start()
                ->group_start()
                    ->where('work_order_goods.ex_no_container IS NULL')
                    ->or_where('work_order_goods.ex_no_container', "")
                ->group_end()
                // job that has ex no container but has not container data treat as LCL
                ->or_group_start()
                    ->where('work_order_goods.ex_no_container IS NOT NULL')
                    ->where('work_order_goods.ex_no_container !=', "")
                    ->where('IFNULL(total_container, 0) =', 0)  //  to exclude stripping
                ->group_end()
            ->group_end()
            ->group_by('customers.id, booking_inbounds.id, ref_warehouses.type, ref_goods.id, ref_units.id');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if (key_exists('data', $filters) && !empty($filters['data'])) {
            switch ($filters['data']) {
                case 'all-data':
                case 'all':
                    break;
                case 'stocked':
                case 'stock-only':
                case 'stock':
                default:
                    $baseQuery->having('used_storage>', 0);
                    break;
                case 'empty-stock':
                    $baseQuery->having('used_storage', 0);
                    break;
                case 'negative-stock':
                    $baseQuery->having('used_storage<', 0);
                    break;
                case 'active':
                    $baseQuery->having('used_storage<>', 0);
                    break;
                case 'inactive-stock':
                    $baseQuery->having('used_storage<=', 0);
                    break;
            }
        }

        if (key_exists('stock_date', $filters) && !empty($filters['stock_date'])) {
            $baseQuery->where('DATE(work_orders.completed_at)<=', format_date($filters['stock_date']));
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->group_start();
            $baseQuery->where_in('customers.id', $filters['customer']);
            if (key_exists('customer_include_member', $filters) && $filters['customer_include_member']) {
                $baseQuery->or_where_in('customers.id_parent', $filters['customer']);
            }
            $baseQuery->group_end();
        }

        if (key_exists('warehouse_type', $filters) && !empty($filters['warehouse_type'])) {
            $baseQuery->where('ref_warehouses.type', $filters['warehouse_type']);
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get detail containerized goods usage.
     * ----------------------------------------------------------------------
     * customer     type        goods       ex          date            used
     * ----------------------------------------------------------------------
     * PT Customer  WAREHOUSE   Computer    TCLU342343  2021-01-01      25
     * PT Arya      WAREHOUSE   Mouse       MKLU234488  2021-01-02      14
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getDetailContainerizedGoodsStock($filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $baseQueryContainerLoading = $this->getContainerLoadingQuery($filters)->get_compiled_select();

        $ruleUsage = "
            ref_handling_types.multiplier_goods 
            * (
                work_order_goods.quantity / work_order_containers.total_goods_loaded_quantity 
                * IF(ref_containers.size = 20, 17, IF(ref_containers.type = 'HC', 34, 32))
            )
        ";

        $baseQuery = $this->db
            ->select([
                'customers.id AS id_customer',
                'customers.name AS customer_name',
                'booking_inbounds.id AS id_booking',
                'booking_inbounds.no_reference',
                'ref_warehouses.type AS warehouse_type',
                'work_order_goods.id_goods',
                'ref_goods.name AS goods_name',
                'ref_units.id AS id_unit',
                'ref_units.unit',
                'work_order_goods.ex_no_container',
                '0 AS is_lcl',
                'MAX(work_orders.completed_at) AS inbound_date',
                "IFNULL(SUM({$ruleUsage}), 0) AS used_storage",
            ])
            ->from('bookings')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(bookings.id_booking, bookings.id)', 'left')
            ->join('ref_people AS customers', 'customers.id = booking_inbounds.id_customer')
            ->join('handlings', 'handlings.id_booking = bookings.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id', 'left')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id', 'left')
            ->join("({$baseQueryContainerLoading}) AS work_order_containers", 'work_order_containers.id_booking = booking_inbounds.id 
                    AND work_order_containers.no_container <=> work_order_goods.ex_no_container
                    AND work_order_containers.id_goods = work_order_goods.id_goods
                    AND work_order_containers.id_unit = work_order_goods.id_unit', 'inner', false)
            ->join("(
                SELECT id_work_order, COUNT(id) AS total_container 
                FROM work_order_containers 
                GROUP BY id_work_order
            ) AS total_containers", 'total_containers.id_work_order = work_orders.id', 'left')
            ->join('ref_containers', 'ref_containers.no_container = TRIM(work_order_goods.ex_no_container)', 'left')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods', 'left')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit', 'left')
            ->join('ref_positions', 'ref_positions.id = work_order_goods.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left')
            ->where('EXISTS(
                SELECT * FROM work_orders work_order_goods
            )', null)
            ->where([
                'work_orders.status' => "COMPLETED",
                'handlings.status' => "APPROVED",
                'work_orders.is_deleted' => false,
                'handlings.is_deleted' => false,
                'bookings.is_deleted' => false,
                //'ref_handling_types.multiplier_goods !=' => 0,
                'ref_warehouses.id IS NOT NULL' => null,
                'work_order_goods.ex_no_container IS NOT NULL' => null,
            ])
            // inbound from containerized goods or outbound only, any other inbound job will be treat as LCL
            ->group_start()
                ->where('ref_handling_types.multiplier_goods', -1)
                ->or_group_start()
                    ->where('ref_handling_types.multiplier_goods', 1)
                    ->where('IFNULL(total_container, 0) >', 0)
                ->group_end()
            ->group_end()
            ->group_by('customers.id, booking_inbounds.id, ref_warehouses.type, ref_goods.id, ref_units.id, work_order_goods.ex_no_container');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if (key_exists('data', $filters) && !empty($filters['data'])) {
            switch ($filters['data']) {
                case 'all-data':
                case 'all':
                    break;
                case 'stocked':
                case 'stock-only':
                case 'stock':
                default:
                    $baseQuery->having('used_storage>', 0);
                    break;
                case 'empty-stock':
                    $baseQuery->having('used_storage', 0);
                    break;
                case 'negative-stock':
                    $baseQuery->having('used_storage<', 0);
                    break;
                case 'active':
                    $baseQuery->having('used_storage<>', 0);
                    break;
                case 'inactive-stock':
                    $baseQuery->having('used_storage<=', 0);
                    break;
            }
        }

        if (key_exists('stock_date', $filters) && !empty($filters['stock_date'])) {
            $baseQuery->where('DATE(work_orders.completed_at)<=', format_date($filters['stock_date']));
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->group_start();
            $baseQuery->where_in('customers.id', $filters['customer']);
            if (key_exists('customer_include_member', $filters) && $filters['customer_include_member']) {
                $baseQuery->or_where_in('customers.id_parent', $filters['customer']);
            }
            $baseQuery->group_end();
        }

        if (key_exists('warehouse_type', $filters) && !empty($filters['warehouse_type'])) {
            $baseQuery->where('ref_warehouses.type', $filters['warehouse_type']);
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get customer storage detail. (merging data from 2 function above)
     * Note: We separate function LCL and containerized to distinguish how to calculate source of spaces.
     *
     * - Containerized: from EX Container size and type
     * - LCL: from job space
     *
     * ----------------------------------------------------------------------
     * customer     type        goods       ex          date            used
     * ----------------------------------------------------------------------
     * PT Customer  WAREHOUSE   Computer    LCL         2021-01-01      18
     * PT Arya      WAREHOUSE   Mouse       MKLU234488  2021-01-02      14
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getCustomerStorageDetail($filters = [])
    {
        $lcl = $this->getDetailGoodsStock($filters);
        $containerized = $this->getDetailContainerizedGoodsStock($filters);

        $goodsStorageDetails = array_merge($lcl, $containerized);
        usort($goodsStorageDetails, function($a, $b) {
            return $a['inbound_date'] <=> $b['inbound_date'];
        });

        return $goodsStorageDetails;
    }

    /**
     * Export data activity space usage to excel.
     *
     * @param $data
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function exportCustomerStorageActivity($data)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator($this->config->item('app_name'))
            ->setLastModifiedBy($this->config->item('app_name'))
            ->setTitle('Customer Storage Activity')
            ->setSubject('Customer Storage')
            ->setDescription('Data export generated by: ' . $this->config->item('app_name'));
        $sheet = $spreadsheet->getActiveSheet();

        $sheet->setCellValue('A1', 'ACTIVITY');
        $sheet->setCellValue('B1', 'DATE ACTIVITY');
        $sheet->setCellValue('C1', 'CUSTOMER NAME');
        $sheet->setCellValue('D1', 'NO REFERENCE');
        $sheet->setCellValue('E1', 'NO REFERENCE INBOUND');
        $sheet->setCellValue('F1', 'NO CONTAINER');
        $sheet->setCellValue('G1', 'QTY');
        $sheet->setCellValue('H1', 'UNIT');
        $sheet->setCellValue('I1', 'INBOUND M2');
        $sheet->setCellValue('J1', 'OUTBOUND M2');
        $sheet->setCellValue('K1', 'LEFT STORAGE M2');
        $sheet->setCellValue('L1', 'USED STORAGE M2');
        $sheet->setCellValue('M1', 'REMARK');

        $sheet
            ->getStyle('A1:M1')
            ->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '000000']
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ]
                ]
            );

        $row = 2;
        foreach ($data as $index => $item) {
            if ($item['row_type'] == 'beginning-balance') {
                $sheet->setCellValue('A' . $row, "Beginning balance stock at {$item['stock_date']} (Capacity {$item['capacity']} M2)");
                $sheet->mergeCells("A{$row}:H{$row}");
                $sheet->setCellValue('I' . $row, $item['inbound_storage']);
                $sheet->setCellValue('J' . $row, $item['outbound_storage']);
                $sheet->setCellValue('K' . $row, $item['left_storage']);
                $sheet->setCellValue('L' . $row, $item['used_storage']);
                $sheet->setCellValue('M' . $row, '-');

                $sheet
                    ->getStyle("A{$row}:M{$row}")
                    ->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'color' => ['rgb' => '92D050']
                            ],
                            'font' => [
                                'bold' => true,
                                'color' => ['rgb' => '000000']
                            ]
                        ]
                    );
            } else if ($item['row_type'] == 'transaction') {
                $sheet->setCellValue('A' . $row, $item['activity_type']);
                $sheet->setCellValue('B' . $row, $item['date_activity']);
                $sheet->setCellValue('C' . $row, $item['customer_name']);
                $sheet->setCellValue('D' . $row, $item['no_reference']);
                $sheet->setCellValue('E' . $row, $item['no_reference_inbound']);
                $sheet->setCellValue('F' . $row, $item['no_container']);
                $sheet->setCellValue('G' . $row, $item['quantity']);
                $sheet->setCellValue('H' . $row, $item['unit']);
                $sheet->setCellValue('I' . $row, $item['inbound_storage']);
                $sheet->setCellValue('J' . $row, $item['outbound_storage']);
                $sheet->setCellValue('K' . $row, $item['left_storage']);
                $sheet->setCellValue('L' . $row, $item['used_storage']);
                $sheet->setCellValue('M' . $row, '-');
            } else if ($item['row_type'] == 'change-capacity') {
                $sheet->setCellValue('A' . $row, "New capacity effective at {$item['effective_date_capacity']} (Capacity {$item['capacity']} M2)");
                $sheet->mergeCells("A{$row}:H{$row}");
                $sheet->setCellValue('I' . $row, $item['inbound_storage']);
                $sheet->setCellValue('J' . $row, $item['outbound_storage']);
                $sheet->setCellValue('K' . $row, $item['left_storage']);
                $sheet->setCellValue('L' . $row, $item['used_storage']);
                $sheet->setCellValue('M' . $row, '-');

                $sheet
                    ->getStyle("A{$row}:M{$row}")
                    ->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'color' => ['rgb' => 'FFC000']
                            ],
                            'font' => [
                                'bold' => true,
                                'color' => ['rgb' => '000000']
                            ]
                        ]
                    );
            }
            $row++;
        }

        $columnIterator = $sheet->getColumnIterator();
        foreach ($columnIterator as $column) {
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Customer Storage.xlsx"');
        $writer->save('php://output');
    }

    /**
     * Export data all warehouse type (WAREHOUSE, YARD, COVERED YARD).
     *
     * @param $contents
     * @param bool $download
     * @return string
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportCustomerStorageActivities($contents, $download = true)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator($this->config->item('app_name'))
            ->setLastModifiedBy($this->config->item('app_name'))
            ->setTitle('Customer Storage Activity')
            ->setSubject('Customer Storage')
            ->setDescription('Data export generated by: ' . $this->config->item('app_name'));

        $spreadsheet->removeSheetByIndex(0);

        foreach ($contents as $title => $data) {
            $myWorkSheet = new Worksheet($spreadsheet, $title);
            $spreadsheet->addSheet($myWorkSheet);

            $sheet = $spreadsheet->getSheetByName($title);

            $sheet->setCellValue('A1', 'ACTIVITY');
            $sheet->setCellValue('B1', 'DATE ACTIVITY');
            $sheet->setCellValue('C1', 'CUSTOMER NAME');
            $sheet->setCellValue('D1', 'NO REFERENCE');
            $sheet->setCellValue('E1', 'NO REFERENCE INBOUND');
            $sheet->setCellValue('F1', 'NO CONTAINER');
            $sheet->setCellValue('G1', 'QTY');
            $sheet->setCellValue('H1', 'USED STORAGE M2');
            $sheet->setCellValue('I1', 'OVER SPACE');

            $sheet
                ->getStyle('A1:I1')
                ->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['rgb' => '000000']
                        ],
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => 'FFFFFF']
                        ]
                    ]
                );

            $row = 2;
            foreach ($data as $index => $item) {
                if ($item['row_type'] == 'beginning-balance') {
                    $sheet->setCellValue('A' . $row, "Beginning balance stock at {$item['stock_date']} (Capacity {$item['capacity']} M2)");
                    $sheet->mergeCells("A{$row}:G{$row}");
                    $sheet->setCellValue('H' . $row, $item['used_storage']);
                    $sheet->setCellValue('I' . $row, $item['over_space']);

                    $sheet
                        ->getStyle("A{$row}:I{$row}")
                        ->applyFromArray([
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'color' => ['rgb' => '92D050']
                                ],
                                'font' => [
                                    'bold' => true,
                                    'color' => ['rgb' => '000000']
                                ]
                            ]
                        );
                } else if ($item['row_type'] == 'transaction') {
                    $sheet->setCellValue('A' . $row, $item['activity_type']);
                    $sheet->setCellValue('B' . $row, $item['date_activity']);
                    $sheet->setCellValue('C' . $row, $item['customer_name']);
                    $sheet->setCellValue('D' . $row, $item['no_reference']);
                    $sheet->setCellValue('E' . $row, $item['no_reference_inbound']);
                    $sheet->setCellValue('F' . $row, $item['no_container']);
                    $sheet->setCellValue('G' . $row, $item['quantity']);
                    $sheet->setCellValue('H' . $row, $item['used_storage']);
                    $sheet->setCellValue('I' . $row, $item['over_space']);
                } else if ($item['row_type'] == 'change-capacity') {
                    $sheet->setCellValue('A' . $row, "New capacity effective at {$item['effective_date_capacity']} (Capacity {$item['capacity']} M2)");
                    $sheet->mergeCells("A{$row}:I{$row}");

                    $sheet
                        ->getStyle("A{$row}:I{$row}")
                        ->applyFromArray([
                                'fill' => [
                                    'fillType' => Fill::FILL_SOLID,
                                    'color' => ['rgb' => 'FFC000']
                                ],
                                'font' => [
                                    'bold' => true,
                                    'color' => ['rgb' => '000000']
                                ]
                            ]
                        );
                }
                $row++;
            }

            $columnIterator = $sheet->getColumnIterator();
            foreach ($columnIterator as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }
        }

        $spreadsheet->setActiveSheetIndex(0);

        $writer = new Xlsx($spreadsheet);

        if ($download) {
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Customer Storage.xlsx"');
            $writer->save('php://output');
        } else {
            if (empty($storeTo)) {
                $storeTo = './uploads/temp/storage-usage-' . uniqid() . '.xlsx';
            }
            $writer->save($storeTo);
            return $storeTo;
        }
    }
}
