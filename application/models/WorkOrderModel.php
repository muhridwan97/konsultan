<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkOrderModel extends MY_Model
{
    protected $table = 'work_orders';
    protected $tableRequisition = 'requisitions';
    protected $tableScheduleHoliday = 'schedule_holidays';
    protected $tableEmployee = 'ref_employees';
    protected $tableEmployeePositionHistory = 'employee_position_histories';
    protected $tableEmployeeLocationHistory = 'employee_location_histories';
    protected $tableScheduleDivision = 'schedule_division';

    const STATUS_QUEUED = 'QUEUED';
    const STATUS_TAKEN = 'TAKEN';
    const STATUS_COMPLETED = 'COMPLETED';

    const STATUS_VALIDATION_VALIDATED = 'VALIDATED';
    const STATUS_VALIDATION_FIXED = 'FIXED';
    const STATUS_VALIDATION_REJECT = 'REJECTED';
    const STATUS_VALIDATION_ON_REVIEW = 'ON REVIEW';
    const STATUS_VALIDATION_PENDING = 'PENDING';
    const STATUS_VALIDATION_APPROVED = 'APPROVED';
    const STATUS_VALIDATION_CHECKED = 'CHECKED';
    const STATUS_VALIDATION_HANDOVER_RELEASED = 'HANDOVER RELEASED';
    const STATUS_VALIDATION_HANDOVER_APPROVED = 'HANDOVER APPROVED';
    const STATUS_VALIDATION_HANDOVER_TAKEN = 'HANDOVER TAKEN';
    const STATUS_VALIDATION_HANDOVER_COMPLETED = 'HANDOVER COMPLETED';

    /**
     * WorkOrderModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        if ($this->config->item('sso_enable')) {
            $this->tableRequisition = env('DB_PURCHASE_DATABASE') . '.requisitions';
            $this->tableScheduleHoliday = env('DB_HR_DATABASE') . '.schedule_holidays';
            $this->tableEmployee = env('DB_HR_DATABASE') . '.ref_employees';
            $this->tableEmployeePositionHistory = env('DB_HR_DATABASE') . '.employee_position_histories';
            $this->tableEmployeeLocationHistory = env('DB_HR_DATABASE') . '.employee_location_histories';
            $this->tableScheduleDivision = env('DB_HR_DATABASE') . '.schedule_division';
        }
    }

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return $this->getWorkOrderBaseQuery($branchId);
    }

    /**
     * Get basic query work order.
     * @param null $branchId
     * @param null $userType
     * @param null $customerId
     * @return CI_DB_query_builder
     */
    public function getWorkOrderBaseQuery($branchId = null, $userType = null, $customerId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        if (empty($userType)) {
            $userType = UserModel::authenticatedUserData('user_type');
        }

        if (empty($customerId)) {
            $customerId = UserModel::authenticatedUserData('id_person');
        }
        $workOrders = $this->db
            ->select([
                $this->table . '.*',
                'TIMEDIFF(gate_out_date, gate_in_date) AS service_time',
                'TIMEDIFF(completed_at, taken_at) AS service_time_tally',
                'bookings.id AS id_booking',
                'bookings.no_booking',
                //'bookings.id_booking AS id_booking_in',
                'GROUP_CONCAT(booking_in.id) AS id_booking_in',
                'bookings.id_booking_type',
                'bookings.no_reference',
                'bookings.vessel',
                'bookings.voyage',
                'bookings.mode AS booking_mode',
                'ref_branches.id AS id_branch',
                'ref_branches.branch',
                'ref_warehouses.warehouse',
                'safe_conducts.no_safe_conduct',
                'safe_conducts.driver',
                'safe_conducts.no_police',
                'safe_conducts.vehicle_type',
                'handlings.no_handling',
                'handlings.handling_date',
                'ref_handling_types.id AS id_handling_type',
                'ref_handling_types.handling_type',
                'ref_handling_types.category AS handling_category',
                'ref_handling_types.description AS handling_type_description',
                'ref_handling_types.multiplier_goods',
                'ref_handling_types.multiplier_container',
                'ref_people.name AS customer_name',
                'ref_people.email AS customer_email',
                'ref_people.id AS id_customer',
                'tally.name AS tally_name',
                'completed.name AS completed_name',
                'creators.name AS creator_name',
                'updaters.name AS updater_name',
                'ref_booking_types.category AS category_booking',
                'ref_booking_types.type AS type_booking',
                'ref_armada.jenis_armada',
                'lockers.name AS locker_name',
                'GROUP_CONCAT(booking_in.no_booking) AS no_booking_in',
                'IF('.$this->table.'.id_transporter_entry_permit is not null,transporter_entry_permits.receiver_no_police, ref_vehicles.no_plate) as no_plate_take',
            ])
            ->from($this->table)
            ->join('handlings', $this->table . '.id_handling = handlings.id')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('safe_conducts', $this->table . '.id_safe_conduct = safe_conducts.id', 'left')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->join('ref_warehouses', $this->table . '.id_warehouse = ref_warehouses.id', 'left')
            ->join('ref_people', 'ref_people.id = handlings.id_customer')
            ->join(UserModel::$tableUser . ' AS tally', 'tally.id = ' . $this->table . '.taken_by', 'left')
            ->join(UserModel::$tableUser . ' AS creators', 'creators.id = ' . $this->table . '.created_by', 'left')
            ->join(UserModel::$tableUser . ' AS updaters', 'updaters.id = ' . $this->table . '.updated_by', 'left')
            ->join(UserModel::$tableUser . ' AS lockers', 'lockers.id = ' . $this->table . '.locked_by', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_armada', 'ref_armada.id = ' . $this->table . '.id_armada', 'left')
            ->join(UserModel::$tableUser . ' AS completed', 'completed.id = ' . $this->table . '.completed_by', 'left')
            ->join('booking_references','booking_references.id_booking=bookings.id','left')
            ->join('bookings AS booking_in','booking_in.id=booking_references.id_booking_reference','left')
            //->join('bookings AS booking_in','booking_in.id=bookings.id_booking','left')
            ->join('transporter_entry_permits','transporter_entry_permits.id='.$this->table.'.id_transporter_entry_permit','left')
            ->join('ref_vehicles','ref_vehicles.id='.$this->table.'.id_vehicle','left')
            ->group_by($this->table . '.id');

        if (!empty($branchId)) {
            $workOrders->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $workOrders->where('bookings.id_customer', $customerId);
        }

        return $workOrders;
    }

    /**
     * Get auto number for work order.
     * @param string $type
     * @return string
     */
    public function getAutoNumberWorkOrder($type = 'WO')
    {
        $orderData = $this->db->query("
            SELECT CAST(RIGHT(no_work_order, 6) AS UNSIGNED) + 1 AS order_number 
            FROM work_orders 
            WHERE MONTH(created_at) = MONTH(NOW()) 
			AND YEAR(created_at) = YEAR(NOW())
            ORDER BY SUBSTR(no_work_order FROM 4) DESC LIMIT 1
			");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return $type . '/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }

    /**
     * Get queue number for work order.
     * @return string
     */
    public function getQueueNumberWorkOrder()
    {
        $orderData = $this->db->query("
            SELECT queue + 1 AS order_queue
            FROM work_orders 
            WHERE DATE(created_at) = DATE(NOW()) 
			AND MONTH(created_at) = MONTH(NOW()) 
			AND YEAR(created_at) = YEAR(NOW())
            ORDER BY id DESC LIMIT 1
			");
        $queue = 1;
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            return $lastOrder['order_queue'];
        }
        return $queue;
    }


     /**
     * Get over capacity.
     *
     * @param array $filters
     * @return array
     */
    public function getOverCapacity($filters = [])
    {

        $workOrders = $this->getWorkOrderBaseQuery()
                    ->select(["ROUND(IF(`ref_handling_types`.`multiplier_goods`='-1',SUM(work_order2.new_space),SUM(work_order2.new_space)),2) AS sum_space",
                            "work_order2.no_reference_inbound"])
                    ->join("(SELECT `work_orders`.id,
                                ROUND(IF(`ref_handling_types`.`multiplier_goods`='-1',work_orders.`space`*-1,work_orders.`space`*1),2) AS new_space,
                                IF(booking_in.no_reference IS NOT NULL,booking_in.no_reference,bookings.no_reference) AS no_reference_inbound
                                FROM `work_orders`
                                JOIN `handlings` ON `work_orders`.`id_handling` = `handlings`.`id`
                                JOIN `ref_handling_types` ON `ref_handling_types`.`id` = `handlings`.`id_handling_type`
                                JOIN `bookings` ON `bookings`.`id` = `handlings`.`id_booking`
                                LEFT JOIN `bookings` AS `booking_in` ON `booking_in`.`id`=`bookings`.`id_booking`
                                WHERE  `ref_handling_types`.`multiplier_goods` IN('1', '-1')
                                AND `work_orders`.`is_deleted` =0) AS work_order2","work_order2.id = work_orders.id","left")
                    ->join("(SELECT work_orders.id, work_order_goods.id AS has_goods FROM work_orders
                                LEFT JOIN work_order_goods ON work_order_goods.`id_work_order` = work_orders.`id`
                                WHERE work_order_goods.id IS NOT NULL GROUP BY work_orders.id) AS unload ","unload.id  = `work_orders`.`id`")
                    ->where_in('ref_handling_types.multiplier_goods ', ['1','-1'])
                    ->where('work_orders.is_deleted', false)
                    ->group_by('no_reference_inbound');

        if (!empty($filters)) {
            if (key_exists('customer', $filters) && !empty($filters['customer'])) {
                if (is_array($filters['customer'])) {
                    $workOrders->where_in('ref_people.id', $filters['customer']);
                } else {
                    $workOrders->where('ref_people.id', $filters['customer']);
                }
            }
            if (key_exists('sum_space_minus', $filters) && !empty($filters['sum_space_minus'])) {
                $workOrders->having('sum_space <= 0 OR sum_space is null');
            }
        }
         return $workOrders->get()->result_array();
    }

    /**
     * Get over capacity.
     *
     * @param array $filters
     * @return array
     */
    public function getOverCapacityDetail($filters = [])
    {

        $workOrders = $this->getWorkOrderBaseQuery()
                        ->select(["ROUND(work_order2.new_space,2) AS new_space",
                        "work_order2.no_reference_inbound"])
                    ->join("(SELECT `work_orders`.id,
                            ROUND(IF(`ref_handling_types`.`multiplier_goods`='-1',work_orders.`space`*-1,work_orders.`space`*1),2) AS new_space,
                            IF(booking_in.no_reference IS NOT NULL,booking_in.no_reference,bookings.no_reference) AS no_reference_inbound
                            FROM `work_orders`
                            JOIN `handlings` ON `work_orders`.`id_handling` = `handlings`.`id`
                            JOIN `ref_handling_types` ON `ref_handling_types`.`id` = `handlings`.`id_handling_type`
                            JOIN `bookings` ON `bookings`.`id` = `handlings`.`id_booking`
                            LEFT JOIN `bookings` AS `booking_in` ON `booking_in`.`id`=`bookings`.`id_booking`
                            WHERE  `ref_handling_types`.`multiplier_goods` IN('1', '-1')
                            AND `work_orders`.`is_deleted` =0) AS work_order2","work_order2.id = work_orders.id","left")
                    ->join("(SELECT work_orders.id, work_order_goods.id AS has_goods FROM work_orders
                            LEFT JOIN work_order_goods ON work_order_goods.`id_work_order` = work_orders.`id`
                            WHERE work_order_goods.id IS NOT NULL GROUP BY work_orders.id) AS unload ","unload.id  = `work_orders`.`id`")
                    ->where_in('ref_handling_types.multiplier_goods ', ['1','-1'])
                    ->where('work_orders.is_deleted', false);

        if (!empty($filters)) {
            if (key_exists('customer', $filters) && !empty($filters['customer'])) {
                if (is_array($filters['customer'])) {
                    $workOrders->where_in('ref_people.id', $filters['customer']);
                } else {
                    $workOrders->where('ref_people.id', $filters['customer']);
                }
            }
            if (key_exists('no_reference', $filters) && !empty($filters['no_reference'])) {
                    $workOrders->where('work_order2.no_reference_inbound', $filters['no_reference']);
            }
            if (key_exists('booking', $filters) && !empty($filters['booking'])) {
                if (is_array($filters['booking'])) {
                    $workOrders->where_in('IFNULL(`bookings`.`id_booking`,`bookings`.`id`)', $filters['booking']);
                }else{
                    $workOrders->where('IFNULL(`bookings`.`id_booking`,`bookings`.`id`)', $filters['booking']);
                }
            }
        }

        return $workOrders->get()->result_array();
    }

     /**
     * Get over capacity.
     *
     * @param array $filters
     * @return array
     */
    public function getDataOpnameSpace($filters = [])
    {
        if(!key_exists('completed', $filters) || empty($filters['completed'])){
            $filters['completed'] = "2018-07-18";
        }

        $workOrders = $this->getWorkOrderBaseQuery()
                    ->select(["ROUND(IF(`ref_handling_types`.`multiplier_goods`='-1',SUM(work_order2.new_space),SUM(work_order2.new_space)),2) AS sum_space",
                            "work_order2.no_reference_inbound"])
                    ->join("(SELECT `work_orders`.id,
                                ROUND(IF(`ref_handling_types`.`multiplier_goods`='-1',work_orders.`space`*-1,work_orders.`space`*1),2) AS new_space,
                                IF(booking_in.no_reference IS NOT NULL,booking_in.no_reference,bookings.no_reference) AS no_reference_inbound
                                FROM `work_orders`
                                JOIN `handlings` ON `work_orders`.`id_handling` = `handlings`.`id`
                                JOIN `ref_handling_types` ON `ref_handling_types`.`id` = `handlings`.`id_handling_type`
                                JOIN `bookings` ON `bookings`.`id` = `handlings`.`id_booking`
                                LEFT JOIN `bookings` AS `booking_in` ON `booking_in`.`id`=`bookings`.`id_booking`
                                WHERE  `ref_handling_types`.`multiplier_goods` IN('1', '-1')
                                AND `work_orders`.`is_deleted` =0) AS work_order2","work_order2.id = work_orders.id","left")
                    ->join("(SELECT IFNULL(`bookings`.`id_booking`,`bookings`.`id`) AS id_booking,
                                COUNT(`work_orders`.id) AS total_job,
                                MAX(work_orders.`completed_at`) AS last_completed
                                FROM `work_orders`
                                JOIN `handlings` ON `work_orders`.`id_handling` = `handlings`.`id`
                                JOIN `ref_handling_types` ON `ref_handling_types`.`id` = `handlings`.`id_handling_type`
                                JOIN `bookings` ON `bookings`.`id` = `handlings`.`id_booking`
                                WHERE  `ref_handling_types`.`multiplier_goods` IN('1', '-1')
                                AND `work_orders`.`is_deleted` =0
                            GROUP BY IFNULL(`bookings`.`id_booking`,`bookings`.`id`)) AS job_opname","job_opname.id_booking = IFNULL(`bookings`.`id_booking`,`bookings`.`id`)","left")
                    ->join("(SELECT work_orders.id, work_order_goods.id AS has_goods FROM work_orders
                                LEFT JOIN work_order_goods ON work_order_goods.`id_work_order` = work_orders.`id`
                                WHERE work_order_goods.id IS NOT NULL GROUP BY work_orders.id) AS unload ","unload.id  = `work_orders`.`id`")
                    ->where_in('ref_handling_types.multiplier_goods ', ['1','-1'])
                    ->where('work_orders.is_deleted', false)
                    ->where('job_opname.total_job>1')
                    ->group_by('no_reference_inbound');

        if (!empty($filters)) {
            if (key_exists('customer', $filters) && !empty($filters['customer'])) {
                if (is_array($filters['customer'])) {
                    $workOrders->where_in('ref_people.id', $filters['customer']);
                } else {
                    $workOrders->where('ref_people.id', $filters['customer']);
                }
            }
            if (key_exists('branch', $filters) && !empty($filters['branch'])) {
                $workOrders->where('bookings.id_branch', $filters['branch']);
            }
            if (key_exists('booking', $filters) && !empty($filters['booking'])) {
                if (is_array($filters['booking'])) {
                    $workOrders->where_in('IFNULL(`bookings`.`id_booking`,`bookings`.`id`)', $filters['booking']);
                }else{
                    $workOrders->where('IFNULL(`bookings`.`id_booking`,`bookings`.`id`)', $filters['booking']);
                }
            }
            if (key_exists('completed', $filters) && !empty($filters['completed'])) {
                if (key_exists('or_booking', $filters) && !empty($filters['or_booking'])) {
                    $workOrders->group_start();
                    if (is_array($filters['or_booking'])) {
                        $workOrders->where('DATE(job_opname.last_completed)>=',$filters['completed']);
                        $workOrders->or_where_in('IFNULL(`bookings`.`id_booking`,`bookings`.`id`)', $filters['or_booking']);
                    }else{
                        $workOrders->where('DATE(job_opname.last_completed)>=',$filters['completed']);
                        $workOrders->or_where('IFNULL(`bookings`.`id_booking`,`bookings`.`id`)', $filters['or_booking']);
                    }
                    $workOrders->group_end();
                } else {
                    $workOrders->where('DATE(job_opname.last_completed)>=',$filters['completed']);
                }
                
            }
        }

         return $workOrders->get()->result_array();
    }

    /**
     * Get work order queue list data.
     * @return mixed
     */
    public function getQueueListWorkOrderHandling()
    {
        $this->load->model('HandlingTypeModel');
        $workOrders = $this->getWorkOrderBaseQuery()
            ->select([
                'ref_handling_types.photo AS photo',
                'bookings.id_upload',
            ])
            ->join('(
                SELECT work_order_goods.`id_work_order`, COUNT(work_order_goods.id) AS sum_goods
                FROM work_order_goods
                GROUP BY work_order_goods.`id_work_order`
                ) AS work_order_goods' , 'work_order_goods.`id_work_order` = work_orders.`id`','left')
            ->where('handlings.status', 'APPROVED')
            ->where('work_orders.gate_out_date IS NULL')
            ->where('work_orders.status !=', self::STATUS_COMPLETED)
            ->where('work_orders.is_deleted', false)
            ->where('ref_handling_types.category', HandlingTypeModel::CATEGORY_WAREHOUSE)
            ->where('work_orders.gate_in_date IS NOT NULL')
            ->or_group_start() //this will start grouping
                ->where('bookings.id_branch', get_active_branch('id'))
                // ->where('work_orders.gate_out_date IS NULL')
                ->where('work_orders.status', self::STATUS_COMPLETED)
                ->where_in('work_orders.status_validation', [self::STATUS_VALIDATION_PENDING,self::STATUS_VALIDATION_CHECKED,self::STATUS_VALIDATION_HANDOVER_COMPLETED])
                ->where_in('ref_handling_types.multiplier_goods ', ['1','-1'])//->where_in('ref_handling_types.handling_type ', ['UNLOAD','LOAD','STRIPPING','STUFFING'])
                ->where('work_orders.is_deleted', false)
                ->where('ref_handling_types.category', HandlingTypeModel::CATEGORY_WAREHOUSE)
                ->where('work_orders.gate_in_date IS NOT NULL')
                ->where('DATE(work_orders.gate_in_date) >=','2019-12-26')
                ->where('ref_branches.tally_check_approval=1')
                ->where('work_order_goods.sum_goods IS NOT NULL')
            ->group_end() //this will end grouping
            ->order_by('work_orders.gate_in_date', 'ASC')
            ->order_by('work_orders.queue', 'ASC')
            ->get();
        return $workOrders->result_array();
    }

    public function getBaseQueryIndex($branchId = null, $userType = null, $customerId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        if (empty($userType)) {
            $userType = UserModel::authenticatedUserData('user_type');
        }

        if (empty($customerId)) {
            $customerId = UserModel::authenticatedUserData('id_person');
        }

        $baseQuery = $this->db
            ->select([
                'work_orders.id',
                'work_orders.no_work_order',
		        'work_orders.id_handling',
                'handlings.no_handling',
                'ref_people.name AS customer_name',
                'bookings.id AS id_booking',
                'bookings.no_reference',
                'ref_handling_types.handling_type',
                'ref_handling_types.multiplier_goods',
                'ref_handling_types.multiplier_container',
                'work_orders.gate_in_date',
                'work_orders.gate_out_date',
                'work_orders.completed_at',
                'work_orders.locked_at',
                'work_orders.is_locked',
                'lockers.name AS locker_name',
                'work_orders.status',
                'work_orders.status_validation',
                'work_orders.pallet_status',
                'work_orders.attachment',
                'work_orders.print_total',
                'work_orders.print_max',
                'work_orders.description',
            ])
            ->from($this->table)
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('ref_people', 'ref_people.id = handlings.id_customer')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join(UserModel::$tableUser . ' AS lockers', 'lockers.id = ' . $this->table . '.locked_by', 'left');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $baseQuery->where('bookings.id_customer', $customerId);
        }

        return $baseQuery;
    }

    /**
     * Get all work orders with or without deleted records.
     * @param array $filters
     * @param bool $withTrashed
     * @return array
     */
    public function getAllWorkOrders($filters = [], $withTrashed = false)
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        // alias column name by index for sorting data table library
        $columnOrder = [
            "work_orders.id",
            "ref_people.name",
            "work_orders.no_work_order",
            "ref_handling_types.handling_type",
            "work_orders.gate_in_date",
            "work_orders.completed_at",
            "work_orders.updated_at",
            "work_orders.locked_at",
            "work_orders.status",
            "work_orders.id",
        ];
        $columnSort = $columnOrder[$column];

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $this->db->start_cache();

        //$workOrders = $this->getWorkOrderBaseQuery($branchId, $userType, $customerId);
        $workOrders = $this->getBaseQueryIndex($branchId, $userType, $customerId);

        if (!$withTrashed) {
            $workOrders->where('work_orders.is_deleted', false);
        }

        if (!empty($filters)) {
            if (key_exists('customer', $filters) && !empty($filters['customer'])) {
                if (is_array($filters['customer'])) {
                    $workOrders->where_in('ref_people.id', $filters['customer']);
                } else {
                    $workOrders->where('ref_people.id', $filters['customer']);
                }
            }

            if (key_exists('handling_type', $filters) && !empty($filters['handling_type'])) {
                if (is_array($filters['handling_type'])) {
                    $workOrders->where_in('ref_handling_types.id', $filters['handling_type']);
                } else {
                    $workOrders->where('ref_handling_types.id', $filters['handling_type']);
                }
            }

            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
                if ($filters['date_type'] == "job_date") {
                    if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                        $workOrders->where('DATE(work_orders.completed_at)>=', sql_date_format($filters['date_from'], false));
                    }
                    if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                        $workOrders->where('DATE(work_orders.completed_at)<=', sql_date_format($filters['date_to'], false));
                    }
                }

                if ($filters['date_type'] == "update_job_date") {
                    if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                        $workOrders->where('DATE(work_orders.updated_at)>=', sql_date_format($filters['date_from'], false));
                    }
                    if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                        $workOrders->where('DATE(work_orders.updated_at)<=', sql_date_format($filters['date_to'], false));
                    }
                }
            }

            if (key_exists('people', $filters) && !empty($filters['people'])) {
                if (is_array($filters['people'])) {
                    $workOrders->where_in('work_orders.updated_by', $filters['people']);
                } else {
                    $workOrders->where('work_orders.updated_by', $filters['people']);
                }
            }

            if (key_exists('gate_status', $filters) && !empty($filters['gate_status']) && $filters['gate_status'] == 'checkout') {
                $workOrders->where('work_orders.status', WorkOrderModel::STATUS_COMPLETED)
                        ->where('work_orders.gate_out_date' , null);
            }
        }

        if (!empty($search)) {
            $workOrders
                ->group_start()
                ->like('work_orders.no_work_order', trim($search))
                ->or_like('ref_people.name', trim($search))
                ->or_like('bookings.no_booking', trim($search))
                ->or_like('bookings.no_reference', trim($search))
                ->or_like('handlings.no_handling', trim($search))
                ->or_like('work_orders.gate_in_date', trim($search))
                ->or_like('work_orders.gate_out_date', trim($search))
                ->or_like('work_orders.completed_at', trim($search))
                ->or_like('work_orders.status', trim($search))
                ->or_like('work_orders.description', trim($search))
                ->or_like('ref_handling_types.handling_type', trim($search))
                ->group_end();
        }

        $workOrders->stop_cache();

        if ($start < 0) {
            $workOrderData = $workOrders->get()->result_array();
            $workOrders->flush_cache();
            return $workOrderData;
        }

        // counting result is slow, use simple pagination or cache to share result set like below
        $distinctQueryParams = filter_data_by_keys($filters, ['customer', 'handling_type', 'date_type', 'date_from', 'date_to', 'gate_status', 'people', 'search']);
        $cacheIdxKey = 'wo-idx-count-' . $branchId . '-' . md5(json_encode($distinctQueryParams));
        $workOrderTotal = cache_remember($cacheIdxKey, 60, function() use ($workOrders) {
            return $workOrders->count_all_results();
        });

        //$workOrderTotal = $workOrders->count_all_results();
        $workOrdersPage = $workOrders->order_by($columnSort, $sort)->limit($length, $start);
        $workOrderData = $workOrdersPage->get()->result_array();

        foreach ($workOrderData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($workOrderData),
            "recordsFiltered" => $workOrderTotal,
            "data" => $workOrderData
        ];
        $this->db->flush_cache();

        return $pageData;
    }

    /**
     * Get work order by specific filters.
     * @param $filters
     * @return array
     */
    public function getWorkOrderSummary($filters)
    {
        $workOrders = $this->getWorkOrderBaseQuery()
            ->select([
                'doc_invoice.no_document AS invoice_number',
                'validators.name AS validator_name',
                'validators.created_at AS validated_at',
            ])
            ->join('(
                SELECT status_histories.id_reference, prv_users.name, status_histories.created_at
                FROM status_histories
                INNER JOIN (
                    SELECT MAX(status_histories.id) AS id
                    FROM status_histories
                    WHERE status_histories.type = "work-order-validation"
                    GROUP BY id_reference
                ) AS latest_validators ON latest_validators.id = status_histories.id
                INNER JOIN ' . UserModel::$tableUser . ' ON prv_users.id = status_histories.created_by
            ) AS validators', 'validators.id_reference = ' . $this->table . '.id', 'left')
            ->join('(
                SELECT id_upload, no_document FROM upload_documents
                INNER JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                WHERE ref_document_types.document_type = "Invoice" AND upload_documents.is_deleted = 0
                ) AS doc_invoice', 'doc_invoice.id_upload = bookings.id_upload', 'left');

        if (key_exists('from_date', $filters)) {
            if (empty($filters['from_date'])) {
                $filters['from_date'] = sql_date_format('now', false);
            } else {
                $filters['from_date'] = sql_date_format($filters['from_date'], false);
            }
            $workOrders->where('DATE(work_orders.completed_at)>=', $filters['from_date']);
        } else {
            $workOrders->where('DATE(work_orders.completed_at)>=', date('Y-m-d'));
        }

        if (key_exists('to_date', $filters)) {
            if (empty($filters['to_date'])) {
                $filters['to_date'] = sql_date_format('now', false);
            } else {
                $filters['to_date'] = sql_date_format($filters['to_date'], false);
            }
            $workOrders->where('DATE(work_orders.completed_at)<=', $filters['to_date']);
        } else {
            $workOrders->where('DATE(work_orders.completed_at)<=', date('Y-m-d'));
        }

        $workOrders->where('work_orders.is_deleted', false);

        return $workOrders->get()->result_array();
    }

    /**
     * Get work order by no pallet.
     *
     * @param $noPallet
     * @return array|array[]
     */
    public function getWorkOrderByNoPallet($noPallet, $customerId = null)
    {
        $baseQuery = $this->db
            ->select([
                'work_orders.*',
                'ref_handling_types.handling_type',
            ])
            ->distinct()
            ->from('work_orders')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->where('work_order_goods.no_pallet', $noPallet);

        if (!empty($customerId)) {
            $baseQuery->where('id_owner', $customerId);
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get work order data by id.
     * @param $workOrderId
     * @param bool $withTrash
     * @return mixed
     */
    public function getWorkOrderById($workOrderId, $withTrash = false)
    {
        $workOrder = $this->getWorkOrderBaseQuery()
            ->where($this->table . '.id', $workOrderId);

        if (!$withTrash) {
            $workOrder->where($this->table . '.id', $workOrderId);
        }

        return $workOrder->get()->row_array();
    }

    /**
     * Get work order data by no work order.
     * @param $noWorkOrder
     * @param bool $withTrash
     * @return mixed
     */
    public function getWorkOrderByNo($noWorkOrder, $withTrash = false)
    {
        $workOrder = $this->getWorkOrderBaseQuery()
            ->where('work_orders.no_work_order', $noWorkOrder);

        if (!$withTrash) {
            $workOrder->where('work_orders.is_deleted', false);
        }

        return $workOrder->get()->row_array();
    }

    /**
     * Get all work orders by specific handling id.
     * @param $handlingId
     * @param bool $withTrash
     * @return mixed
     */
    public function getWorkOrdersByHandling($handlingId, $withTrash = false)
    {
        $workOrders = $this->getWorkOrderBaseQuery()
            ->where('work_orders.id_handling', $handlingId)
            ->order_by('id', 'DESC');

        if (!$withTrash) {
            $workOrders->where('work_orders.is_deleted', false);
        }

        return $workOrders->get()->result_array();
    }

    /**
     * Get handling by invoice status or data
     * @param $customerId
     * @return mixed
     */
    public function getWorkOrdersByUnpublishedInvoice($customerId)
    {
        $workOrders = $this->getWorkOrderBaseQuery()
            ->join('(SELECT * FROM invoices WHERE is_deleted = 0 AND status = "PUBLISHED") AS invoices', 'invoices.no_reference = work_orders.no_work_order', 'left')
            ->where('invoices.id IS NULL')
            ->where('handlings.id_customer', $customerId)
            ->order_by('id', 'DESC');

        $workOrders->where('work_orders.is_deleted', false);

        return $workOrders->get()->result_array();
    }

    /**
     * Get work orders that don't have safe conduct
     * @param null $exceptWorkOrderId
     * @param bool $withTrash
     * @return mixed
     */
    public function getWorkOrdersByEmptySafeConduct($exceptWorkOrderId = null, $withTrash = false)
    {
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');
        $workOrders = $this->getWorkOrderBaseQuery()
            ->join('(
                SELECT work_order_goods.`id_work_order`, COUNT(work_order_goods.id) AS sum_goods
                FROM work_order_goods
                GROUP BY work_order_goods.`id_work_order`
                ) AS work_order_goods' , 'work_order_goods.`id_work_order` = work_orders.`id`','left')
            ->order_by('id', 'DESC')
            ->where('work_orders.status', 'COMPLETED')
            ->where('ref_handling_types.category', 'WAREHOUSE')
            ->where_in('ref_handling_types.handling_type', ['LOAD', 'EMPTY CONTAINER'])
            ->group_start()
                ->where_in('work_orders.status_validation', [self::STATUS_VALIDATION_APPROVED,self::STATUS_VALIDATION_ON_REVIEW,self::STATUS_VALIDATION_VALIDATED])
                // ->or_where('ref_handling_types.id!=',$handlingTypeIdOutbound)
                ->or_where('ref_handling_types.multiplier_goods', 0)
                ->or_where('ref_branches.tally_check_approval=0')
                ->or_where('work_order_goods.sum_goods IS NULL')
            ->group_end()
            ->group_start()
                ->group_start()
                    ->where('bookings.status_payout', 'APPROVED')
                    ->where('DATE(NOW()) <= IFNULL(bookings.payout_until_date, DATE(NOW()))')
                ->group_end()
                ->or_where('ref_booking_types.category', 'INBOUND')
                ->or_where('ref_people.outbound_type', 'ACCOUNT RECEIVABLE')
                ->or_where('ref_people.outbound_type IS NULL')
            ->group_end()
            //->group_start()
            //    ->where('ref_handling_types.multiplier_container', -1)
            //    ->or_where('ref_handling_types.multiplier_goods', -1)
            //->group_end()
            ->group_start()
                ->where('work_orders.id_safe_conduct IS NULL')
                ->or_where('work_orders.id_safe_conduct', 0);
        if (!empty($exceptWorkOrderId)) {
            $workOrders->or_where('work_orders.id', $exceptWorkOrderId);
        }
        $workOrders->group_end();

        if (!$withTrash) {
            $workOrders->where('work_orders.is_deleted', false);
        }

        return $workOrders->get()->result_array();
    }

    /**
     * Get work orders by related safe conduct.
     * @param $safeConductId
     * @param bool $withTrash
     * @return mixed
     */
    public function getWorkOrdersBySafeConduct($safeConductId, $withTrash = false)
    {
        $workOrders = $this->getWorkOrderBaseQuery()
            ->where('work_orders.id_safe_conduct', $safeConductId)
            ->order_by('id', 'DESC');

        if (!$withTrash) {
            $workOrders->where('work_orders.is_deleted', false);
        }

        return $workOrders->get()->result_array();
    }

    /**
     * Get work orders by related safe conduct.
     * @param $bookingId
     * @param bool $withTrash
     * @return mixed
     */
    public function getWorkOrdersByBooking($bookingId, $withTrash = false)
    {
        $workOrders = $this->getWorkOrderBaseQuery()->where('bookings.id', $bookingId)
            ->order_by('handling_type', 'DESC')
            ->order_by('id', 'DESC');

        if (!$withTrash) {
            $workOrders->where('work_orders.is_deleted', false);
        }

        return $workOrders->get()->result_array();
    }

    /**
     * Get job by handling type.
     *
     * @param $handlingTypeId
     * @param null $bookingId
     * @param bool $withTrash
     * @return array
     */
    public function getWorkOrdersByHandlingType($handlingTypeId = null, $bookingId = null, $withTrash = false)
    {
        $workOrders = $this->getWorkOrderBaseQuery()
            ->order_by('work_orders.completed_at', 'DESC');

        if (!empty($handlingTypeId)) {
            if (is_array($handlingTypeId)) {
                $workOrders->where_in('ref_handling_types.id', $handlingTypeId);
            } else {
                $workOrders->where('ref_handling_types.id', $handlingTypeId);
            }
        }

        if (!empty($bookingId)) {
            if (is_array($bookingId)) {
                $workOrders->where_in('bookings.id', $bookingId);
            } else {
                $workOrders->where('bookings.id', $bookingId);
            }
        }

        if (!$withTrash) {
            $workOrders->where('work_orders.is_deleted', false);
        }

        return $workOrders->get()->result_array();
    }

    /**
     * Get work order data by query.
     *
     * @param string|null $query
     * @param null $page
     * @param bool $withTrashed
     * @return mixed
     */
    public function getWorkOrdersByQuery($query, $page = null, $withTrashed = false)
    {
        $branchId = get_active_branch_id();

        $this->db->start_cache();

        $baseQuery = $this->db
            ->select([
                'work_orders.id',
                'work_orders.no_work_order',
                'handlings.no_handling',
                'bookings.no_booking',
                'bookings.no_reference',
                'ref_people.name AS customer_name',
            ])
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('ref_people', 'ref_people.id = bookings.id_customer');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if (is_array($query)) {
            $baseQuery->where_in('work_orders.no_work_order', $query);
        } else {
            $baseQuery
                ->group_start()
                ->or_like('work_orders.no_work_order', trim($query))
                ->or_like('handlings.no_handling', trim($query))
                ->or_like('bookings.no_booking', trim($query))
                ->or_like('bookings.no_reference', trim($query))
                ->group_end();
        }

        if (!$withTrashed) {
            $baseQuery->where('work_orders.is_deleted', false);
        }

        $this->db->stop_cache();

        if (!empty($page) || $page != 0) {
            $total = $baseQuery->count_all_results();
            $page = $baseQuery->limit(10, 10 * ($page - 1));
            $data = $page->get()->result_array();

            return [
                'results' => $data,
                'total_count' => $total
            ];
        }

        $peopleData = $baseQuery->get()->result_array();

        $this->db->flush_cache();

        return $peopleData;
    }

    /**
     * Create new work order.
     * @param $data
     * @return bool
     */
    public function createWorkOrder($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update work order data.
     * @param $data
     * @param $id
     * @return bool
     */
    public function updateWorkOrder($data, $id)
    {
        $condition = is_null($id) ? null : ['id' => $id];
        if (is_array($id)) {
            $condition = $id;
        }
        if ($this->db->field_exists('updated_by', $this->table) && !key_exists('updated_by', $data)) {
            $data['updated_by'] = UserModel::authenticatedUserData('id', 0);
        }
        if ($this->db->field_exists('updated_at', $this->table) && !key_exists('updated_at', $data)) {
            $data['updated_at'] = date('Y-m-d H:i:s');
        }
        return $this->db->update($this->table, $data, $condition);
    }

    /**
     * Delete work order data.
     * @param $id
     * @param bool $softDelete
     * @return bool
     */
    public function deleteWorkOrder($id, $softDelete = true)
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
     * Search work order data by query string.
     * @param $query
     * @param int $limit
     * @param bool $withTrashed
     * @return array
     */
    public function search($query, $limit = 10, $withTrashed = false)
    {
        $handlings = $this->getWorkOrderBaseQuery()
            ->group_start()
            ->like('no_work_order', $query)
            ->or_like('work_orders.description', $query)
            ->or_like('work_orders.status', $query)
            ->or_like('gate_in_date', $query)
            ->or_like('gate_out_date', $query)
            ->or_like('tools', $query)
            ->or_like('materials', $query)
            ->or_like('handling_type', $query)
            ->or_like('ref_people.name', $query)
            ->group_end()
            ->limit($limit)
            ->get();

        return $handlings->result_array();
    }

    /**
     * Get work order validation status summary.
     *
     * @param $filters
     * @return array
     */
    public function getValidationSummary($filters = null)
    {
        $branchId = null;
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $summary = $this->db->select([
            'COUNT(*) AS total_job',
            'SUM(IF(status_validation = "VALIDATED", 1, 0)) AS total_validated',
            'SUM(IF(status_validation != "VALIDATED" AND (DATEDIFF(NOW(), completed_at) < 2), 1, 0)) AS total_outstanding',
            'SUM(IF(status_validation != "VALIDATED" AND (DATEDIFF(NOW(), completed_at) >= 2), 1, 0)) AS total_outdated'
        ])
            ->from($this->table)
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking');

        if (!empty($branchId)) {
            $summary->where('bookings.id_branch', $branchId);
        }

        if (!empty($filters)) {
            if (isset($filters['year'])) {
                $summary->where('YEAR(completed_at)', $filters['year']);
            }

            if (isset($filters['month'])) {
                $summary->where('MONTH(completed_at)', $filters['month']);
            }
        }

        return $summary->get()->row_array();
    }


    /**
     * Get work order validation status summary.
     *
     * @param $date
     * @return array
     */
    public function getOutstandingSummary($date)
    {
        $branchId = null;
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $summary = $this->db->select([
            'IFNULL(COUNT(*), 0) AS total_all_job',
            "IFNULL(SUM(IF(status_validation != 'VALIDATED', 1, 0)), 0) AS total_outstanding"
        ])
            ->from($this->table)
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->where("DATE(completed_at)>", "DATE_SUB('{$date}', INTERVAL 2 DAY)", false)
            ->where("DATE(completed_at)<=", $date)
            ->where('work_orders.is_deleted', false);

        if (!empty($branchId)) {
            $summary->where('bookings.id_branch', $branchId);
        }

        return $summary->get()->row_array();
    }

    public function setLocked($data, $where)
    {
        $query = "UPDATE work_orders 
            INNER JOIN handlings ON handlings.id = work_orders.id_handling
            INNER JOIN bookings ON bookings.id = handlings.id_booking
            SET work_orders.is_locked = " . $data['is_locked'] . ",
            work_orders.locked_by = " . $data['locked_by'] . ",
            work_orders.locked_at = " . $data['locked_at'] . "
            WHERE work_orders.is_locked IS NOT NULL 
            AND bookings.id_branch =" . $where['id_branch'];
        if (!empty($where['date_from'])) {
            $query .= " AND work_orders.completed_at >= '" . $where['date_from'] . "'";
            $query .= " AND work_orders.completed_at <= '" . $where['date_to'] . "'";
        }
        if (!empty($where['id_customers'])) {
            $query .= " AND bookings.id_customer IN (" . $where['id_customers'] . ")";
        }
        if (!empty($where['id_handling_type'])) {
            $query .= " AND handlings.id_handling_type IN (" . $where['id_handling_type'] . ")";
        }
        if (!empty($where['no_work_order'])) {
            $query .= " AND work_orders.id IN (" . $where['no_work_order'] . ")";
        }
        if (!empty($where['id_work_order'])) {
            $query .= " AND work_orders.id IN (" . $where['id_work_order'] . ")";
        }
        return $this->db->query($query);
    }

    public function getIdWorkOrderByLockedParam($data)
    {
        $query = "SELECT work_orders.id FROM work_orders
            INNER JOIN handlings ON handlings.id = work_orders.id_handling
            INNER JOIN bookings ON bookings.id = handlings.id_booking
            WHERE work_orders.is_locked IS NOT NULL
            AND bookings.id_branch =" . $data['id_branch'];
        if (!empty($data['date_from'])) {
            $query .= " AND work_orders.completed_at >= '" . $data['date_from'] . "'";
            $query .= " AND work_orders.completed_at <= '" . $data['date_to'] . "'";
        }
        if (!empty($data['id_customers'])) {
            $query .= " AND bookings.id_customer IN (" . $data['id_customers'] . ")";
        }
        if (!empty($data['id_handling_type'])) {
            $query .= " AND handlings.id_handling_type IN (" . $data['id_handling_type'] . ")";
        }
        if (!empty($data['no_work_order'])) {
            $query .= " AND work_orders.id IN (" . $data['no_work_order'] . ")";
        }
        $hasil = $this->db->query($query);
        return $hasil->result();
    }

    public function getNoWorkOrder($name, $page = null, $withTrashed = false)
    {
        $branchId = get_active_branch('id');
        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');
        $this->db->start_cache();
        $workOrders = $this->db
            ->select([
                'work_orders.*'
            ])
            ->from($this->table)
            ->join('handlings', 'work_orders.id_handling = handlings.id')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('bookings', 'bookings.id = handlings.id_booking');

        if (!empty($branchId)) {
            $workOrders->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $workOrders->where('bookings.id_customer', $customerId);
        }
        $workOrders->order_by("work_orders.id", 'DESC');

        if (!$withTrashed) {
            $workOrders->where('work_orders.is_deleted', false);
        }

        if (!empty($name)) {
            if (is_array($name)) {
                $workOrders->where_in('work_orders.no_work_order', $name);
            } else {
                $workOrders->like('work_orders.no_work_order', trim($name));
            }
        }
        $this->db->stop_cache();
        if (!empty($page) || $page != 0) {
            $workOrdersTotal = $workOrders->count_all_results();
            $workOrdersPage = $workOrders->limit(10, 10 * ($page - 1));
            $dataWorkOrders = $workOrdersPage->get()->result_array();

            return [
                'results' => $dataWorkOrders,
                'total_count' => $workOrdersTotal
            ];
        }

        return $workOrders->get()->result_array();
    }

    /**
     * Get total space used by branch.
     *
     * @return array
     */
    public function getUsedSpace($id_customer){
        $branchId = get_active_branch('id');
        $workOrders = $this->db
            ->select([
                'SUM(work_orders.real_space) as total_space'
            ])
            ->from("(SELECT work_orders.*, IF(ref_booking_types.`category`='INBOUND',(work_orders.space*-1),work_orders.`space`) AS real_space FROM work_orders
            LEFT JOIN handlings ON handlings.`id` = work_orders.`id_handling`
            LEFT JOIN bookings ON bookings.`id` = handlings.`id_booking`
            LEFT JOIN ref_booking_types ON ref_booking_types.`id` = bookings.`id_booking_type`
            ) AS work_orders")
            ->join('handlings', 'work_orders.id_handling = handlings.id')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->where('bookings.id_branch', $branchId)
            ->where('bookings.id_customer', $id_customer)
            ->where('work_orders.is_deleted', false);

        return $workOrders->get()->row_array();
    }

    /**
     * Get total stock pallet used by branch.
     *
     * @return array
     */
    public function getUsedPallet(){
        $branchId = get_active_branch('id');
        $workOrders = $this->db
            ->select([
                'SUM(work_orders.real_stock_pallet) as total_pallet'
            ])
            ->from("(SELECT work_orders.*, IF(ref_booking_types.`category`='INBOUND',(work_orders.stock_pallet*-1),work_orders.`stock_pallet`) AS real_stock_pallet FROM work_orders
            LEFT JOIN handlings ON handlings.`id` = work_orders.`id_handling`
            LEFT JOIN bookings ON bookings.`id` = handlings.`id_booking`
            LEFT JOIN ref_booking_types ON ref_booking_types.`id` = bookings.`id_booking_type`
            ) AS work_orders")
            ->join('handlings', 'work_orders.id_handling = handlings.id')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->where('bookings.id_branch', $branchId)
            ->where('work_orders.is_deleted', false);

        return $workOrders->get()->row_array();
    }

    /**
     * Get work order pallet by branch.
     *
     * @return array
     */
    public function getWorkOrderPallet($filters = []){
        $branchId = get_active_branch('id');
        $workOrders = $this->db
            ->select([
                'work_orders.*',
                'upload_documents.no_document',
                'upload_document_in.no_document AS no_document_in',
                'bookings.no_reference',
                'booking_in.no_reference AS no_reference_in',
                'ref_containers.no_container',
                'ref_containers.size',
                'work_orders.`category`',
                'ROUND(work_order_qty.qty,0) AS qty',
            ])
            ->from("(SELECT work_orders.*,ref_booking_types.`category`
            , IF(ref_booking_types.`category`='INBOUND',(work_orders.space*-1),work_orders.`space`) AS real_space
            , IF(ref_booking_types.`category`='INBOUND',(work_orders.stock_pallet*-1),work_orders.`stock_pallet`) AS real_stock_pallet FROM work_orders
            LEFT JOIN handlings ON handlings.`id` = work_orders.`id_handling`
            LEFT JOIN bookings ON bookings.`id` = handlings.`id_booking`
            LEFT JOIN ref_booking_types ON ref_booking_types.`id` = bookings.`id_booking_type`
            ) AS work_orders")
            ->join('handlings', 'work_orders.id_handling = handlings.id')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('bookings AS booking_in', 'booking_in.id = bookings.id_booking','left')
            ->join('uploads', 'uploads.id = bookings.id_upload','left')
            ->join('upload_documents', 'upload_documents.id_upload = uploads.id','left')
            ->join('uploads AS upload_in', 'upload_in.id = uploads.id_upload','left')
            ->join('upload_documents AS upload_document_in', 'upload_document_in.id_upload = upload_in.id','left')
            ->join('work_order_containers', '`work_order_containers`.`id_work_order` = `work_orders`.`id`','left')
            ->join('ref_containers', '`ref_containers`.`id` = `work_order_containers`.`id_container`','left')
            ->join("(SELECT work_orders.id,  SUM(work_order_goods.`quantity`) AS qty FROM work_orders
                LEFT JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                GROUP BY work_orders.id) AS work_order_qty","work_order_qty.id = work_orders.id",'left')
            ->where('bookings.id_branch', $branchId)
            ->where('work_orders.stock_pallet is not null', null)
            ->where('work_orders.stock_pallet != 0', null)
            ->where('work_orders.is_deleted', false)
            ->order_by('work_orders.completed_at')
            ->group_by('work_orders.id');

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $workOrders->where("DATE(".$this->table . '.completed_at)>=', format_date($filters['date_from']));
        }
        if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
            $workOrders->where("DATE(".$this->table . '.completed_at)<=', format_date($filters['date_to']));
        }

        return $workOrders->get()->result_array();
    }
    
    /**
     * Get report forklift.
     *
     * @return array
     */
    public function getWorkOrderForklift($filtersDb){
        $workOrders = $this->db
            ->select([
                'total.tahun',
                'total.minggu',
                'ROUND(frt_all_jkt,2) AS all_frt_bln_jkt',
                'ROUND(frt_own_jkt,2) AS own_frt_bln_jkt',
                'ROUND((ROUND(frt_own_jkt,2)/IF(target_forklift_jkt.target IS NULL, ref_targets.target ,target_forklift_jkt.target))*100,0) AS target_jkt',
                'ROUND(frt_all_mdn,2) AS all_frt_bln_mdn',
                'ROUND(frt_own_mdn,2) AS own_frt_bln_mdn',
                'ROUND((ROUND(frt_own_mdn,2)/IF(target_forklift_mdn.target IS NULL, ref_targets.target ,target_forklift_mdn.target))*100,0) AS target_mdn',
                'ROUND(frt_own_sby,2) AS own_frt_bln_sby',
                'ROUND(frt_all_sby,2) AS all_frt_bln_sby',
                'ROUND((ROUND(frt_own_sby,2)/IF(target_forklift_sby.target IS NULL, ref_targets.target ,target_forklift_sby.target))*100,0) AS target_sby',
                'ROUND((IFNULL(ROUND(frt_all_jkt,2),0)+IFNULL(ROUND(frt_all_mdn,2),0)+IFNULL(ROUND(frt_all_sby,2),0))/3,2) AS avg_all_frt',
                'ROUND((IFNULL(ROUND(frt_own_jkt,2),0)+IFNULL(ROUND(frt_own_mdn,2),0)+IFNULL(ROUND(frt_own_sby,2),0))/3,2) AS avg_frt',
            ])
            ->from("(SELECT  YEAR(ref_dates.date)AS tahun, WEEK(ref_dates.date) AS minggu FROM 
                        ref_dates
                        WHERE ref_dates.date BETWEEN '".$filtersDb['date_from']."' AND '".$filtersDb['date_to']."'
                        GROUP BY  YEAR(ref_dates.date), WEEK(ref_dates.date) ) AS total")
            ->join("(SELECT jkt.tahun, jkt.minggu, jkt.frt_minggu AS frt_all_jkt, jkt2.frt_minggu AS frt_own_jkt
                        FROM (SELECT job_minggu.tahun, job_minggu.minggu, AVG(job_minggu.frt_hari) AS frt_minggu FROM
                        (SELECT YEAR(hari_kerja.selected_date)AS tahun, WEEK(hari_kerja.selected_date) AS minggu, DAY(hari_kerja.selected_date) AS hari, IFNULL(SUM(job.hasil_frt),0) AS frt_hari FROM
                                    (SELECT ref_dates.date AS selected_date FROM ref_dates
                                        WHERE ref_dates.date BETWEEN '".$filtersDb['date_from']."' AND '".$filtersDb['date_to']."'
                                        AND ref_dates.date NOT IN 
                                        (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday." AS schedule_holidays WHERE schedule_holidays.date NOT IN ('2020-12-28', '2020-12-29', '2020-12-30')) AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja
                                    LEFT JOIN 
                                    (SELECT IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),0) AS forklift_own, table_frt.frt AS frt_own, (table_frt.frt/IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),1))AS hasil_frt, work_orders.`id`, work_orders.`completed_at` FROM 
                                                        (SELECT IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt, work_order_goods.`id_work_order` FROM work_order_goods
                                                        LEFT JOIN ref_goods ON ref_goods.`id` = work_order_goods.`id_goods`
                                        GROUP BY work_order_goods.`id_work_order`) AS table_frt
                                                    LEFT JOIN work_orders ON work_orders.id = table_frt.id_work_order
                                                    LEFT JOIN handlings ON handlings.id = work_orders.id_handling
                                                    LEFT JOIN ref_handling_types ON ref_handling_types.`id` = handlings.`id_handling_type`
                                                    LEFT JOIN bookings ON bookings.id = handlings.id_booking
                                                    LEFT JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                                                    LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                                                    LEFT JOIN ref_branches ON ref_branches.id = bookings.id_branch
                                                    WHERE ref_branches.`id_branch_vms` = '1' AND ref_handling_types.`multiplier_goods`!=0 
                                    GROUP BY work_orders.`id`) AS job
                                    ON DATE(job.`completed_at`) = hari_kerja.selected_date
                                GROUP BY YEAR(hari_kerja.selected_date), WEEK(hari_kerja.selected_date), DAY(hari_kerja.selected_date)
                                UNION
                                SELECT YEAR(job.`completed_at`)AS tahun, WEEK(job.`completed_at`) AS minggu, DAY(job.`completed_at`) AS hari, IFNULL(SUM(job.hasil_frt),0) AS frt_hari FROM
                                    (SELECT IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),0) AS forklift_own, table_frt.frt AS frt_own, (table_frt.frt/IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),1))AS hasil_frt, work_orders.`id`, work_orders.`completed_at` FROM 
                                                        (SELECT IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt, work_order_goods.`id_work_order` FROM work_order_goods
                                                        LEFT JOIN ref_goods ON ref_goods.`id` = work_order_goods.`id_goods`
                                        GROUP BY work_order_goods.`id_work_order`) AS table_frt
                                                    LEFT JOIN work_orders ON work_orders.id = table_frt.id_work_order
                                                    LEFT JOIN handlings ON handlings.id = work_orders.id_handling
                                                    LEFT JOIN ref_handling_types ON ref_handling_types.`id` = handlings.`id_handling_type`
                                                    LEFT JOIN bookings ON bookings.id = handlings.id_booking
                                                    LEFT JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                                                    LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                                                    LEFT JOIN ref_branches ON ref_branches.id = bookings.id_branch
                                                    WHERE ref_branches.`id_branch_vms` = '1' AND ref_handling_types.`multiplier_goods`!=0 
                                    GROUP BY work_orders.`id`) AS job
                                GROUP BY YEAR(job.`completed_at`), WEEK(job.`completed_at`), DAY(job.`completed_at`)) AS job_minggu         
                            GROUP BY tahun,minggu) AS jkt       
                        LEFT JOIN (SELECT job_minggu.tahun, job_minggu.minggu, AVG(job_minggu.frt_hari) AS frt_minggu FROM
                                (SELECT YEAR(hari_kerja.selected_date)AS tahun, WEEK(hari_kerja.selected_date) AS minggu, DAY(hari_kerja.selected_date) AS hari, IFNULL(SUM(job.hasil_frt),0) AS frt_hari FROM
                                    (SELECT ref_dates.date AS selected_date FROM ref_dates
                                        WHERE ref_dates.date BETWEEN '".$filtersDb['date_from']."' AND '".$filtersDb['date_to']."'
                                        AND ref_dates.date NOT IN 
                                        (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday." AS schedule_holidays WHERE schedule_holidays.date NOT IN ('2020-12-28', '2020-12-29', '2020-12-30')) AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja
                                    LEFT JOIN 
                                    (SELECT IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,count_alat_berat.count_heavy_equipment) AS forklift_own, table_frt.frt AS frt_own, (table_frt.frt/IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,count_alat_berat.count_heavy_equipment))AS hasil_frt, work_orders.`id`, work_orders.`completed_at` FROM 
                                                        (SELECT IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt, work_order_goods.`id_work_order` FROM work_order_goods
                                                        LEFT JOIN ref_goods ON ref_goods.`id` = work_order_goods.`id_goods`
                                        GROUP BY work_order_goods.`id_work_order`) AS table_frt
                                                    LEFT JOIN work_orders ON work_orders.id = table_frt.id_work_order
                                                    LEFT JOIN handlings ON handlings.id = work_orders.id_handling
                                                    LEFT JOIN bookings ON bookings.id = handlings.id_booking
                                                    LEFT JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                                                    LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                                                    LEFT JOIN ref_branches ON ref_branches.id = bookings.id_branch
                                                    LEFT JOIN ref_heavy_equipments ON ref_heavy_equipments.id = work_order_component_heavy_equipments.id_heavy_equipment
                                                    LEFT JOIN (SELECT work_order_components.`id_work_order`,COUNT(work_order_component_heavy_equipments.`id`) AS count_heavy_equipment FROM work_order_component_heavy_equipments
                                                        JOIN work_order_components ON work_order_components.id = work_order_component_heavy_equipments.`id_work_order_component`
                                                        GROUP BY work_order_components.`id_work_order`) AS count_alat_berat ON count_alat_berat.id_work_order = work_orders.id
                                                    WHERE ref_branches.`id_branch_vms` = '1' AND work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED'
                                                    AND ref_heavy_equipments.type = 'FORKLIFT'                                                     
                                    GROUP BY work_orders.`id`) AS job
                                    ON DATE(job.`completed_at`) = hari_kerja.selected_date
                                GROUP BY YEAR(hari_kerja.selected_date), WEEK(hari_kerja.selected_date), DAY(hari_kerja.selected_date)
                                UNION
                                SELECT YEAR(job.`completed_at`)AS tahun, WEEK(job.`completed_at`) AS minggu, DAY(job.`completed_at`) AS hari, IFNULL(SUM(job.hasil_frt),0) AS frt_hari FROM
                                    (SELECT IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,count_alat_berat.count_heavy_equipment) AS forklift_own, table_frt.frt AS frt_own, (table_frt.frt/IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,count_alat_berat.count_heavy_equipment))AS hasil_frt, work_orders.`id`, work_orders.`completed_at` FROM 
                                                        (SELECT IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt, work_order_goods.`id_work_order` FROM work_order_goods
                                                        LEFT JOIN ref_goods ON ref_goods.`id` = work_order_goods.`id_goods`
                                        GROUP BY work_order_goods.`id_work_order`) AS table_frt
                                                    LEFT JOIN work_orders ON work_orders.id = table_frt.id_work_order
                                                    LEFT JOIN handlings ON handlings.id = work_orders.id_handling
                                                    LEFT JOIN bookings ON bookings.id = handlings.id_booking
                                                    LEFT JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                                                    LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                                                    LEFT JOIN ref_branches ON ref_branches.id = bookings.id_branch
                                                    LEFT JOIN ref_heavy_equipments ON ref_heavy_equipments.id = work_order_component_heavy_equipments.id_heavy_equipment
                                                    LEFT JOIN (SELECT work_order_components.`id_work_order`,COUNT(work_order_component_heavy_equipments.`id`) AS count_heavy_equipment FROM work_order_component_heavy_equipments
                                                        JOIN work_order_components ON work_order_components.id = work_order_component_heavy_equipments.`id_work_order_component`
                                                        GROUP BY work_order_components.`id_work_order`) AS count_alat_berat ON count_alat_berat.id_work_order = work_orders.id
                                                    WHERE ref_branches.`id_branch_vms` = '1' AND work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED'
                                                    AND ref_heavy_equipments.type = 'FORKLIFT'
                                    GROUP BY work_orders.`id`) AS job
                                GROUP BY YEAR(job.`completed_at`), WEEK(job.`completed_at`), DAY(job.`completed_at`)) AS job_minggu         
                            GROUP BY tahun,minggu) AS jkt2 
                        ON jkt.tahun = jkt2.tahun AND jkt.minggu = jkt2.minggu) AS jkt","jkt.tahun = total.tahun AND jkt.minggu = total.minggu",'left')

            ->join("(SELECT jkt.tahun, jkt.minggu, jkt.frt_minggu AS frt_all_mdn, jkt2.frt_minggu AS frt_own_mdn
                        FROM (SELECT job_minggu.tahun, job_minggu.minggu, AVG(job_minggu.frt_hari) AS frt_minggu FROM
                        (SELECT YEAR(hari_kerja.selected_date)AS tahun, WEEK(hari_kerja.selected_date) AS minggu, DAY(hari_kerja.selected_date) AS hari, IFNULL(SUM(job.hasil_frt),0) AS frt_hari FROM
                                    (SELECT ref_dates.date AS selected_date FROM ref_dates
                                        WHERE ref_dates.date BETWEEN '".$filtersDb['date_from']."' AND '".$filtersDb['date_to']."'
                                        AND ref_dates.date NOT IN 
                                        (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday." AS schedule_holidays WHERE schedule_holidays.date NOT IN ('2020-12-28', '2020-12-29', '2020-12-30')) AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja
                                    LEFT JOIN 
                                    (SELECT IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),0) AS forklift_own, table_frt.frt AS frt_own, (table_frt.frt/IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),1))AS hasil_frt, work_orders.`id`, work_orders.`completed_at` FROM 
                                                        (SELECT IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt, work_order_goods.`id_work_order` FROM work_order_goods
                                                        LEFT JOIN ref_goods ON ref_goods.`id` = work_order_goods.`id_goods`
                                        GROUP BY work_order_goods.`id_work_order`) AS table_frt
                                                    LEFT JOIN work_orders ON work_orders.id = table_frt.id_work_order
                                                    LEFT JOIN handlings ON handlings.id = work_orders.id_handling
                                                    LEFT JOIN ref_handling_types ON ref_handling_types.`id` = handlings.`id_handling_type`
                                                    LEFT JOIN bookings ON bookings.id = handlings.id_booking
                                                    LEFT JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                                                    LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                                                    WHERE bookings.`id_branch` = '2' AND ref_handling_types.`multiplier_goods`!=0 
                                    GROUP BY work_orders.`id`) AS job
                                    ON DATE(job.`completed_at`) = hari_kerja.selected_date
                                GROUP BY YEAR(hari_kerja.selected_date), WEEK(hari_kerja.selected_date), DAY(hari_kerja.selected_date)
                                UNION
                                SELECT YEAR(job.`completed_at`)AS tahun, WEEK(job.`completed_at`) AS minggu, DAY(job.`completed_at`) AS hari, IFNULL(SUM(job.hasil_frt),0) AS frt_hari FROM
                                    (SELECT IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),0) AS forklift_own, table_frt.frt AS frt_own, (table_frt.frt/IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),1))AS hasil_frt, work_orders.`id`, work_orders.`completed_at` FROM 
                                                        (SELECT IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt, work_order_goods.`id_work_order` FROM work_order_goods
                                                        LEFT JOIN ref_goods ON ref_goods.`id` = work_order_goods.`id_goods`
                                        GROUP BY work_order_goods.`id_work_order`) AS table_frt
                                                    LEFT JOIN work_orders ON work_orders.id = table_frt.id_work_order
                                                    LEFT JOIN handlings ON handlings.id = work_orders.id_handling
                                                    LEFT JOIN ref_handling_types ON ref_handling_types.`id` = handlings.`id_handling_type`
                                                    LEFT JOIN bookings ON bookings.id = handlings.id_booking
                                                    LEFT JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                                                    LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                                                    WHERE bookings.`id_branch` = '2' AND ref_handling_types.`multiplier_goods`!=0 
                                    GROUP BY work_orders.`id`) AS job
                                GROUP BY YEAR(job.`completed_at`), WEEK(job.`completed_at`), DAY(job.`completed_at`)) AS job_minggu         
                            GROUP BY tahun,minggu) AS jkt       
                        LEFT JOIN (SELECT job_minggu.tahun, job_minggu.minggu, AVG(job_minggu.frt_hari) AS frt_minggu FROM
                                (SELECT YEAR(hari_kerja.selected_date)AS tahun, WEEK(hari_kerja.selected_date) AS minggu, DAY(hari_kerja.selected_date) AS hari, IFNULL(SUM(job.hasil_frt),0) AS frt_hari FROM
                                    (SELECT ref_dates.date AS selected_date FROM ref_dates
                                        WHERE ref_dates.date BETWEEN '".$filtersDb['date_from']."' AND '".$filtersDb['date_to']."'
                                        AND ref_dates.date NOT IN 
                                        (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday." AS schedule_holidays WHERE schedule_holidays.date NOT IN ('2020-12-28', '2020-12-29', '2020-12-30')) AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja
                                    LEFT JOIN 
                                    (SELECT IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,count_alat_berat.count_heavy_equipment) AS forklift_own, table_frt.frt AS frt_own, (table_frt.frt/IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,count_alat_berat.count_heavy_equipment))AS hasil_frt, work_orders.`id`, work_orders.`completed_at` FROM 
                                                        (SELECT IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt, work_order_goods.`id_work_order` FROM work_order_goods
                                                        LEFT JOIN ref_goods ON ref_goods.`id` = work_order_goods.`id_goods`
                                        GROUP BY work_order_goods.`id_work_order`) AS table_frt
                                                    LEFT JOIN work_orders ON work_orders.id = table_frt.id_work_order
                                                    LEFT JOIN handlings ON handlings.id = work_orders.id_handling
                                                    LEFT JOIN bookings ON bookings.id = handlings.id_booking
                                                    LEFT JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                                                    LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                                                    LEFT JOIN ref_heavy_equipments ON ref_heavy_equipments.id = work_order_component_heavy_equipments.id_heavy_equipment
                                                    LEFT JOIN (SELECT work_order_components.`id_work_order`,COUNT(work_order_component_heavy_equipments.`id`) AS count_heavy_equipment FROM work_order_component_heavy_equipments
                                                        JOIN work_order_components ON work_order_components.id = work_order_component_heavy_equipments.`id_work_order_component`
                                                        GROUP BY work_order_components.`id_work_order`) AS count_alat_berat ON count_alat_berat.id_work_order = work_orders.id
                                                    WHERE bookings.`id_branch` = '2' AND work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED'
                                                    AND ref_heavy_equipments.type = 'FORKLIFT'
                                    GROUP BY work_orders.`id`) AS job
                                    ON DATE(job.`completed_at`) = hari_kerja.selected_date
                                GROUP BY YEAR(hari_kerja.selected_date), WEEK(hari_kerja.selected_date), DAY(hari_kerja.selected_date)
                                UNION
                                SELECT YEAR(job.`completed_at`)AS tahun, WEEK(job.`completed_at`) AS minggu, DAY(job.`completed_at`) AS hari, IFNULL(SUM(job.hasil_frt),0) AS frt_hari FROM
                                    (SELECT IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,count_alat_berat.count_heavy_equipment) AS forklift_own, table_frt.frt AS frt_own, (table_frt.frt/IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,count_alat_berat.count_heavy_equipment))AS hasil_frt, work_orders.`id`, work_orders.`completed_at` FROM 
                                                        (SELECT IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt, work_order_goods.`id_work_order` FROM work_order_goods
                                                        LEFT JOIN ref_goods ON ref_goods.`id` = work_order_goods.`id_goods`
                                        GROUP BY work_order_goods.`id_work_order`) AS table_frt
                                                    LEFT JOIN work_orders ON work_orders.id = table_frt.id_work_order
                                                    LEFT JOIN handlings ON handlings.id = work_orders.id_handling
                                                    LEFT JOIN bookings ON bookings.id = handlings.id_booking
                                                    LEFT JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                                                    LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                                                    LEFT JOIN ref_heavy_equipments ON ref_heavy_equipments.id = work_order_component_heavy_equipments.id_heavy_equipment
                                                    LEFT JOIN (SELECT work_order_components.`id_work_order`,COUNT(work_order_component_heavy_equipments.`id`) AS count_heavy_equipment FROM work_order_component_heavy_equipments
                                                        JOIN work_order_components ON work_order_components.id = work_order_component_heavy_equipments.`id_work_order_component`
                                                        GROUP BY work_order_components.`id_work_order`) AS count_alat_berat ON count_alat_berat.id_work_order = work_orders.id
                                                    WHERE bookings.`id_branch` = '2' AND work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED'
                                                    AND ref_heavy_equipments.type = 'FORKLIFT'
                                    GROUP BY work_orders.`id`) AS job
                                GROUP BY YEAR(job.`completed_at`), WEEK(job.`completed_at`), DAY(job.`completed_at`)) AS job_minggu         
                            GROUP BY tahun,minggu) AS jkt2 
                        ON jkt.tahun = jkt2.tahun AND jkt.minggu = jkt2.minggu) AS mdn", 'total.tahun = mdn.tahun AND total.minggu = mdn.minggu','left')

            ->join("(SELECT jkt.tahun, jkt.minggu, jkt.frt_minggu AS frt_all_sby, jkt2.frt_minggu AS frt_own_sby
                        FROM (SELECT job_minggu.tahun, job_minggu.minggu, AVG(job_minggu.frt_hari) AS frt_minggu FROM
                        (SELECT YEAR(hari_kerja.selected_date)AS tahun, WEEK(hari_kerja.selected_date) AS minggu, DAY(hari_kerja.selected_date) AS hari, IFNULL(SUM(job.hasil_frt),0) AS frt_hari FROM
                                    (SELECT ref_dates.date AS selected_date FROM ref_dates
                                        WHERE ref_dates.date BETWEEN '".$filtersDb['date_from']."' AND '".$filtersDb['date_to']."'
                                        AND ref_dates.date NOT IN 
                                        (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday." AS schedule_holidays WHERE schedule_holidays.date NOT IN ('2020-12-28', '2020-12-29', '2020-12-30')) AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja
                                    LEFT JOIN 
                                    (SELECT IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),0) AS forklift_own, table_frt.frt AS frt_own, (table_frt.frt/IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),1))AS hasil_frt, work_orders.`id`, work_orders.`completed_at` FROM 
                                                        (SELECT IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt, work_order_goods.`id_work_order` FROM work_order_goods
                                                        LEFT JOIN ref_goods ON ref_goods.`id` = work_order_goods.`id_goods`
                                        GROUP BY work_order_goods.`id_work_order`) AS table_frt
                                                    LEFT JOIN work_orders ON work_orders.id = table_frt.id_work_order
                                                    LEFT JOIN handlings ON handlings.id = work_orders.id_handling
                                                    LEFT JOIN ref_handling_types ON ref_handling_types.`id` = handlings.`id_handling_type`
                                                    LEFT JOIN bookings ON bookings.id = handlings.id_booking
                                                    LEFT JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                                                    LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                                                    WHERE bookings.`id_branch` = '5' AND ref_handling_types.`multiplier_goods`!=0 
                                    GROUP BY work_orders.`id`) AS job
                                    ON DATE(job.`completed_at`) = hari_kerja.selected_date
                                GROUP BY YEAR(hari_kerja.selected_date), WEEK(hari_kerja.selected_date), DAY(hari_kerja.selected_date)
                                UNION
                                SELECT YEAR(job.`completed_at`)AS tahun, WEEK(job.`completed_at`) AS minggu, DAY(job.`completed_at`) AS hari, IFNULL(SUM(job.hasil_frt),0) AS frt_hari FROM
                                    (SELECT IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),0) AS forklift_own, table_frt.frt AS frt_own, (table_frt.frt/IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),1))AS hasil_frt, work_orders.`id`, work_orders.`completed_at` FROM 
                                                        (SELECT IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt, work_order_goods.`id_work_order` FROM work_order_goods
                                                        LEFT JOIN ref_goods ON ref_goods.`id` = work_order_goods.`id_goods`
                                        GROUP BY work_order_goods.`id_work_order`) AS table_frt
                                                    LEFT JOIN work_orders ON work_orders.id = table_frt.id_work_order
                                                    LEFT JOIN handlings ON handlings.id = work_orders.id_handling
                                                    LEFT JOIN ref_handling_types ON ref_handling_types.`id` = handlings.`id_handling_type`
                                                    LEFT JOIN bookings ON bookings.id = handlings.id_booking
                                                    LEFT JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                                                    LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                                                    WHERE bookings.`id_branch` = '5' AND ref_handling_types.`multiplier_goods`!=0 
                                    GROUP BY work_orders.`id`) AS job
                                GROUP BY YEAR(job.`completed_at`), WEEK(job.`completed_at`), DAY(job.`completed_at`)) AS job_minggu         
                            GROUP BY tahun,minggu) AS jkt       
                        LEFT JOIN (SELECT job_minggu.tahun, job_minggu.minggu, AVG(job_minggu.frt_hari) AS frt_minggu FROM
                                (SELECT YEAR(hari_kerja.selected_date)AS tahun, WEEK(hari_kerja.selected_date) AS minggu, DAY(hari_kerja.selected_date) AS hari, IFNULL(SUM(job.hasil_frt),0) AS frt_hari FROM
                                    (SELECT ref_dates.date AS selected_date FROM ref_dates
                                        WHERE ref_dates.date BETWEEN '".$filtersDb['date_from']."' AND '".$filtersDb['date_to']."'
                                        AND ref_dates.date NOT IN 
                                        (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday." AS schedule_holidays WHERE schedule_holidays.date NOT IN ('2020-12-28', '2020-12-29', '2020-12-30')) AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja
                                    LEFT JOIN 
                                    (SELECT IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,count_alat_berat.count_heavy_equipment) AS forklift_own, table_frt.frt AS frt_own, (table_frt.frt/IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,count_alat_berat.count_heavy_equipment))AS hasil_frt, work_orders.`id`, work_orders.`completed_at` FROM 
                                                        (SELECT IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt, work_order_goods.`id_work_order` FROM work_order_goods
                                                        LEFT JOIN ref_goods ON ref_goods.`id` = work_order_goods.`id_goods`
                                        GROUP BY work_order_goods.`id_work_order`) AS table_frt
                                                    LEFT JOIN work_orders ON work_orders.id = table_frt.id_work_order
                                                    LEFT JOIN handlings ON handlings.id = work_orders.id_handling
                                                    LEFT JOIN bookings ON bookings.id = handlings.id_booking
                                                    LEFT JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                                                    LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                                                    LEFT JOIN ref_heavy_equipments ON ref_heavy_equipments.id = work_order_component_heavy_equipments.id_heavy_equipment
                                                    LEFT JOIN (SELECT work_order_components.`id_work_order`,COUNT(work_order_component_heavy_equipments.`id`) AS count_heavy_equipment FROM work_order_component_heavy_equipments
                                                        JOIN work_order_components ON work_order_components.id = work_order_component_heavy_equipments.`id_work_order_component`
                                                        GROUP BY work_order_components.`id_work_order`) AS count_alat_berat ON count_alat_berat.id_work_order = work_orders.id
                                                    WHERE bookings.`id_branch` = '5' AND work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED'
                                                    AND ref_heavy_equipments.type = 'FORKLIFT'
                                    GROUP BY work_orders.`id`) AS job
                                    ON DATE(job.`completed_at`) = hari_kerja.selected_date
                                GROUP BY YEAR(hari_kerja.selected_date), WEEK(hari_kerja.selected_date), DAY(hari_kerja.selected_date)
                                UNION
                                SELECT YEAR(job.`completed_at`)AS tahun, WEEK(job.`completed_at`) AS minggu, DAY(job.`completed_at`) AS hari, IFNULL(SUM(job.hasil_frt),0) AS frt_hari FROM
                                    (SELECT IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,count_alat_berat.count_heavy_equipment) AS forklift_own, table_frt.frt AS frt_own, (table_frt.frt/IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,count_alat_berat.count_heavy_equipment))AS hasil_frt, work_orders.`id`, work_orders.`completed_at` FROM 
                                                        (SELECT IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt, work_order_goods.`id_work_order` FROM work_order_goods
                                                        LEFT JOIN ref_goods ON ref_goods.`id` = work_order_goods.`id_goods`
                                        GROUP BY work_order_goods.`id_work_order`) AS table_frt
                                                    LEFT JOIN work_orders ON work_orders.id = table_frt.id_work_order
                                                    LEFT JOIN handlings ON handlings.id = work_orders.id_handling
                                                    LEFT JOIN bookings ON bookings.id = handlings.id_booking
                                                    LEFT JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                                                    LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                                                    LEFT JOIN ref_heavy_equipments ON ref_heavy_equipments.id = work_order_component_heavy_equipments.id_heavy_equipment
                                                    LEFT JOIN (SELECT work_order_components.`id_work_order`,COUNT(work_order_component_heavy_equipments.`id`) AS count_heavy_equipment FROM work_order_component_heavy_equipments
                                                        JOIN work_order_components ON work_order_components.id = work_order_component_heavy_equipments.`id_work_order_component`
                                                        GROUP BY work_order_components.`id_work_order`) AS count_alat_berat ON count_alat_berat.id_work_order = work_orders.id
                                                    WHERE bookings.`id_branch` = '5' AND work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED'
                                                    AND ref_heavy_equipments.type = 'FORKLIFT'
                                    GROUP BY work_orders.`id`) AS job
                                GROUP BY YEAR(job.`completed_at`), WEEK(job.`completed_at`), DAY(job.`completed_at`)) AS job_minggu         
                            GROUP BY tahun,minggu) AS jkt2 
                        ON jkt.tahun = jkt2.tahun AND jkt.minggu = jkt2.minggu) AS sby", 'total.tahun = sby.tahun AND total.minggu = sby.minggu','left')
            ->join("(SELECT ref_targets.id, ref_targets.target AS target_all, ref_target_branches.target, ref_target_branches.id_branch FROM
                        ref_targets
                        LEFT JOIN ref_target_branches ON ref_target_branches.id_target = ref_targets.id
                        WHERE ref_target_branches.id_branch = 1) AS target_forklift_jkt",
                        "target_forklift_jkt.id = 2","left")
            ->join("(SELECT ref_targets.id, ref_targets.target AS target_all, ref_target_branches.target, ref_target_branches.id_branch FROM
                        ref_targets
                        LEFT JOIN ref_target_branches ON ref_target_branches.id_target = ref_targets.id
                        WHERE ref_target_branches.id_branch = 2) AS target_forklift_mdn",
                        "target_forklift_mdn.id = 2","left")      
            ->join("(SELECT ref_targets.id, ref_targets.target AS target_all, ref_target_branches.target, ref_target_branches.id_branch FROM
                        ref_targets
                        LEFT JOIN ref_target_branches ON ref_target_branches.id_target = ref_targets.id
                        WHERE ref_target_branches.id_branch = 5) AS target_forklift_sby",
                        "target_forklift_sby.id = 2","left")
            ->join("ref_targets","ref_targets.id = 2","left");


        return $workOrders->get()->result_array();
    }

    /**
     * Get report forklift.
     *
     * @return array
     */
    public function getWorkOrderForkliftByCondition($filters = []){
        $workOrders = $this->db->select([
            'work_orders.id',
            'work_orders.no_work_order',
            'ref_people.name AS customer_name',
            'ROUND(table_frt.frt,2) AS frt_own',
            'work_orders.`completed_at`',
            'table_frt.satuan',
            'bookings.id_branch',
        ])
            ->from("(SELECT IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt,IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), 'TON', 'METER KUBIK') AS satuan, work_order_goods.`id_work_order` FROM work_order_goods
                    LEFT JOIN ref_goods ON ref_goods.`id` = work_order_goods.`id_goods`
                    GROUP BY work_order_goods.`id_work_order`) AS table_frt")
            ->join("work_orders","work_orders.id = table_frt.id_work_order","left")
            ->join("handlings","handlings.id = work_orders.id_handling","left")
            ->join('ref_people', 'ref_people.id = handlings.id_customer',"left")
            ->join("bookings","bookings.id = handlings.id_booking","left")
            ->join("work_order_components","work_orders.id = work_order_components.id_work_order","left")
            ->group_by("work_orders.`id`")
            ->order_by("work_orders.completed_at");
        if (key_exists('ownership', $filters) && !empty($filters['ownership'])) {
            if($filters['ownership']=='owned'){
                $workOrders->select(['ROUND(IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`, count_alat_berat.count_heavy_equipment),0) AS forklift',
                                'ROUND(table_frt.frt/IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`, count_alat_berat.count_heavy_equipment),2) AS frt',
                                'alat_berat.forklift_name'])
                        ->join("work_order_component_heavy_equipments","work_order_component_heavy_equipments.id_work_order_component = work_order_components.id","left")
                        ->join("(SELECT GROUP_CONCAT(ref_heavy_equipments.`name` SEPARATOR ', ') AS forklift_name,work_order_components.id FROM ref_heavy_equipments
                            JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.`id_heavy_equipment`=ref_heavy_equipments.id
                            JOIN work_order_components ON work_order_components.id = work_order_component_heavy_equipments.`id_work_order_component`
                            WHERE ref_heavy_equipments.type = 'FORKLIFT'
                            GROUP BY work_order_components.id) AS alat_berat","alat_berat.id = work_order_components.id","left")
                        ->join("(SELECT work_order_components.`id_work_order`,COUNT(work_order_component_heavy_equipments.`id`) AS count_heavy_equipment FROM work_order_component_heavy_equipments
                        JOIN work_order_components ON work_order_components.id = work_order_component_heavy_equipments.`id_work_order_component`
                        GROUP BY work_order_components.`id_work_order`) AS count_alat_berat","count_alat_berat.id_work_order = work_orders.id","left")
                        ->join("ref_heavy_equipments","ref_heavy_equipments.id = work_order_component_heavy_equipments.id_heavy_equipment","left")
                        ->where("work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED' AND ref_heavy_equipments.type = 'FORKLIFT'");
            }else if($filters['ownership']=='all'){
                $workOrders->select(["ROUND(IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),0),0) AS forklift",
                            "ROUND(table_frt.frt/IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),1),2) AS frt",
                            'alat_berat.forklift_name'])
                    ->join("work_order_component_heavy_equipments","work_order_component_heavy_equipments.id_work_order_component = work_order_components.id","left")
                    ->join("ref_handling_types","ref_handling_types.id = handlings.`id_handling_type`","left")
                    ->join("(SELECT GROUP_CONCAT(alat_berat.forklift_name SEPARATOR ', ') AS forklift_name,alat_berat.id FROM (
                            SELECT GROUP_CONCAT(ref_heavy_equipments.`name` SEPARATOR ', ') AS forklift_name,work_order_components.id FROM ref_heavy_equipments
                            JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.`id_heavy_equipment`=ref_heavy_equipments.id
                            JOIN work_order_components ON work_order_components.id = work_order_component_heavy_equipments.`id_work_order_component`
                            GROUP BY work_order_components.id
                            UNION
                            SELECT GROUP_CONCAT(heavy_equipment_entry_permits.`heep_code` SEPARATOR ', ') AS forklift_name,work_order_components.id FROM heavy_equipment_entry_permits
                            JOIN work_order_component_heeps ON work_order_component_heeps.`id_heep`=heavy_equipment_entry_permits.id
                            JOIN work_order_components ON work_order_components.id = work_order_component_heeps.`id_work_order_component`
                            GROUP BY work_order_components.id) AS alat_berat
                        GROUP BY alat_berat.id) AS alat_berat","alat_berat.id = work_order_components.id","left")
                    ->where("ref_handling_types.`multiplier_goods`!=0 ");
            }else{//for leas
                $workOrders->select(['ROUND(IF(COUNT(work_order_component_heeps.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heeps.`id`)),0) AS forklift',
                                'ROUND(table_frt.frt/IF(COUNT(work_order_component_heeps.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heeps.`id`)),2) AS frt',])
                        ->join("work_order_component_heeps","work_order_component_heeps.id_work_order_component = work_order_components.id","left")
                        ->join("heavy_equipment_entry_permits","heavy_equipment_entry_permits.id = work_order_component_heeps.`id_heep`","left")
                        ->join($this->tableRequisition." AS requisitions","requisitions.id = heavy_equipment_entry_permits.`id_requisition`","left")
                        ->where("work_order_components.`id_component` = '1' AND requisitions.`approved_type` = 'INTERNAL'");
            }
        }
        if (key_exists('week', $filters) && !empty($filters['week'])) {
            $workOrders->where("WEEK(work_orders.`completed_at`)", $filters['week'])
                    ->where("YEAR(work_orders.`completed_at`)",$filters['year']);
        }
        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            if($filters['branch']==1){
                $workOrders->join("ref_branches","ref_branches.id = bookings.`id_branch`", "left");
                $workOrders->where("ref_branches.`id_branch_vms`", $filters['branch']);
            }else{
                $workOrders->where("bookings.`id_branch`", $filters['branch']);
            }            
        }
        return $workOrders->get()->result_array();
    }

    /**
     * Get report performance.
     *
     * @return array
     */
    public function getWorkOrderPerformance($filtersDb){
        $workOrders = $this->db
            ->select([
                'jkt.tahun',
                'jkt.minggu',
                'ROUND((jkt.avg_frt+mdn.avg_frt+sby.avg_frt)/3,2) AS avg_frt_tot', 
                'ROUND((jkt.avg_ops+mdn.avg_ops+sby.avg_ops)/3,2) AS avg_ops_tot',
                'jkt.avg_frt AS avg_frt_jkt', 
                'jkt.avg_ops AS avg_ops_jkt', 
                'ROUND(jkt.avg_frt/jkt.avg_ops,2) AS frt_ops_jkt',
                'ROUND((ROUND(jkt.avg_frt/jkt.avg_ops,2)/IF(target_forklift_jkt.target IS NULL, ref_targets.target ,target_forklift_jkt.target))*100,0) AS target_jkt',
                'jkt.avg_ops_core AS avg_ops_core_jkt',
                'ROUND(jkt.avg_frt/jkt.avg_ops_core,2) AS frt_ops_core_jkt',
                'ROUND((ROUND(jkt.avg_frt/jkt.avg_ops_core,2)/IF(target_forklift_jkt.target IS NULL, ref_targets.target ,target_forklift_jkt.target))*100,0) AS target_jkt_core',
                'mdn.avg_frt AS avg_frt_mdn', 
                'mdn.avg_ops AS avg_ops_mdn', 
                'ROUND(mdn.avg_frt/mdn.avg_ops,2) AS frt_ops_mdn',
                'ROUND((ROUND(mdn.avg_frt/mdn.avg_ops,2)/IF(target_forklift_mdn.target IS NULL, ref_targets.target ,target_forklift_mdn.target))*100,0) AS target_mdn',
                'mdn.avg_ops_core AS avg_ops_core_mdn',
                'ROUND(mdn.avg_frt/mdn.avg_ops_core,2) AS frt_ops_core_mdn',
                'ROUND((ROUND(mdn.avg_frt/mdn.avg_ops_core,2)/IF(target_forklift_mdn.target IS NULL, ref_targets.target ,target_forklift_mdn.target))*100,0) AS target_mdn_core',
                'sby.avg_frt AS avg_frt_sby', 
                'sby.avg_ops AS avg_ops_sby',
                'ROUND(sby.avg_frt/sby.avg_ops,2) AS frt_ops_sby',
                'ROUND((ROUND(sby.avg_frt/sby.avg_ops,2)/IF(target_forklift_sby.target IS NULL, ref_targets.target ,target_forklift_sby.target))*100,0) AS target_sby',
                'sby.avg_ops_core AS avg_ops_core_sby',
                'ROUND(sby.avg_frt/sby.avg_ops_core,2) AS frt_ops_core_sby',
                'ROUND((ROUND(sby.avg_frt/sby.avg_ops_core,2)/IF(target_forklift_sby.target IS NULL, ref_targets.target ,target_forklift_sby.target))*100,0) AS target_sby_core',
            ])
            // ->from("(SELECT  YEAR(selected_date)AS tahun, WEEK(selected_date) AS minggu FROM 
                        // (SELECT ADDDATE('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date FROM
                        // (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
                        // (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
                        // (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
                        // (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
                        // (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4) v
                        // WHERE selected_date BETWEEN '".$filtersDb['date_from']."' AND '".$filtersDb['date_to']."'
                        // GROUP BY  YEAR(selected_date), WEEK(selected_date) ) AS total")
            ->from("(SELECT frt_jkt.tahun, frt_jkt.minggu, ROUND(frt_jkt.frt_minggu,2) AS avg_frt,ops_jkt.avg_ops,ops_core_jkt.avg_ops_core FROM
                        (SELECT job_minggu.tahun, job_minggu.minggu, AVG(job_minggu.frt_hari) AS frt_minggu FROM
                        (SELECT YEAR(selected_date)AS tahun, WEEK(selected_date) AS minggu, DAY(selected_date) AS hari, IFNULL(SUM(job.hasil_frt),0) AS frt_hari FROM
                                    (SELECT ref_dates.date AS selected_date FROM ref_dates
                                        WHERE ref_dates.date BETWEEN '".$filtersDb['date_from']."' AND '".$filtersDb['date_to']."' 
                                        AND ref_dates.date NOT IN 
                                        (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday." AS schedule_holidays WHERE schedule_holidays.date NOT IN ('2020-12-28', '2020-12-29', '2020-12-30')) AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja
                                    LEFT JOIN 
                                    (SELECT IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),0) AS forklift_own, table_frt.frt AS frt_own, (table_frt.frt/IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),1))AS hasil_frt, work_orders.`id`, work_orders.`completed_at` FROM 
                                                        (SELECT IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt, work_order_goods.`id_work_order` FROM work_order_goods
                                                        LEFT JOIN ref_goods ON ref_goods.`id` = work_order_goods.`id_goods`
                                        GROUP BY work_order_goods.`id_work_order`) AS table_frt
                                                    LEFT JOIN work_orders ON work_orders.id = table_frt.id_work_order
                                                    LEFT JOIN handlings ON handlings.id = work_orders.id_handling
                                                    LEFT JOIN ref_handling_types ON ref_handling_types.`id` = handlings.`id_handling_type`
                                                    LEFT JOIN bookings ON bookings.id = handlings.id_booking
                                                    LEFT JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                                                    LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                                                    LEFT JOIN ref_branches ON ref_branches.id = bookings.id_branch
                                                    WHERE ref_branches.`id_branch_vms` = '1' AND ref_handling_types.`multiplier_goods`!=0 AND work_orders.`completed_at`> '".$filtersDb['date_from']."' AND work_orders.`completed_at`<='".$filtersDb['date_to']."'
                                    GROUP BY work_orders.`id`) AS job
                                    ON DATE(job.`completed_at`) = hari_kerja.selected_date
                                GROUP BY YEAR(selected_date), WEEK(selected_date), DAY(selected_date)
                                UNION
                                SELECT YEAR(job.`completed_at`)AS tahun, WEEK(job.`completed_at`) AS minggu, DAY(job.`completed_at`) AS hari, IFNULL(SUM(job.hasil_frt),0) AS frt_hari FROM
                                    (SELECT IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),0) AS forklift_own, table_frt.frt AS frt_own, (table_frt.frt/IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),1))AS hasil_frt, work_orders.`id`, work_orders.`completed_at` FROM 
                                                        (SELECT IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt, work_order_goods.`id_work_order` FROM work_order_goods
                                                        LEFT JOIN ref_goods ON ref_goods.`id` = work_order_goods.`id_goods`
                                        GROUP BY work_order_goods.`id_work_order`) AS table_frt
                                                    LEFT JOIN work_orders ON work_orders.id = table_frt.id_work_order
                                                    LEFT JOIN handlings ON handlings.id = work_orders.id_handling
                                                    LEFT JOIN ref_handling_types ON ref_handling_types.`id` = handlings.`id_handling_type`
                                                    LEFT JOIN bookings ON bookings.id = handlings.id_booking
                                                    LEFT JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                                                    LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                                                    LEFT JOIN ref_branches ON ref_branches.id = bookings.id_branch
                                                    WHERE ref_branches.`id_branch_vms` = '1' AND ref_handling_types.`multiplier_goods`!=0 AND work_orders.`completed_at`> '".$filtersDb['date_from']."' AND work_orders.`completed_at`<='".$filtersDb['date_to']."'
                                    GROUP BY work_orders.`id`) AS job
                                GROUP BY YEAR(job.`completed_at`), WEEK(job.`completed_at`), DAY(job.`completed_at`)) AS job_minggu         
                            GROUP BY tahun,minggu) AS frt_jkt
                    LEFT JOIN
                        (SELECT tahun,minggu, ROUND(AVG(count_ops),2) AS avg_ops FROM 
                            (SELECT YEAR(selected_date) AS tahun, WEEK(selected_date) AS minggu, COUNT(ref_employees.`id`) AS count_ops FROM 
                            (SELECT ref_dates.date AS selected_date FROM ref_dates
                                WHERE ref_dates.date BETWEEN '".$filtersDb['date_from']."' AND '".$filtersDb['date_to']."' 
                                AND ref_dates.date NOT IN 
                                (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday." AS schedule_holidays WHERE schedule_holidays.date NOT IN ('2020-12-28', '2020-12-29', '2020-12-30')) AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja
                            INNER JOIN ".$this->tableEmployee." AS ref_employees ON ref_employees.`enter_date` <= hari_kerja.selected_date AND (ref_employees.`quit_date` IS NULL OR ref_employees.`quit_date`>=hari_kerja.selected_date)
                            INNER JOIN ".$this->tableEmployeeLocationHistory." AS location_history ON location_history.id_employee = ref_employees.id AND ((location_history.start_date <= hari_kerja.selected_date AND location_history.end_date > hari_kerja.selected_date) OR (location_history.start_date <= hari_kerja.selected_date AND location_history.end_date IS NULL))
                            INNER JOIN ref_branches ON ref_branches.`id_branch_hr` = location_history.id_location
                            INNER JOIN ".$this->tableScheduleDivision." AS schedule_division ON schedule_division.id_employee = ref_employees.id AND schedule_division.date = hari_kerja.selected_date
                            WHERE ref_employees.id_department = '1' AND ref_branches.`id` = '1' AND schedule_division.id_schedule != '1'
                            GROUP BY YEAR(selected_date), WEEK(selected_date), DAY(selected_date)) AS table_ops
                            GROUP BY tahun,minggu) AS ops_jkt
                    ON ops_jkt.tahun = frt_jkt.tahun AND ops_jkt.minggu = frt_jkt.minggu
                    LEFT JOIN
                        (SELECT tahun,minggu, ROUND(AVG(count_ops),2) AS avg_ops_core FROM 
                            (SELECT YEAR(selected_date) AS tahun, WEEK(selected_date) AS minggu, COUNT(ref_employees.`id`) AS count_ops FROM 
                            (SELECT ref_dates.date AS selected_date FROM ref_dates
                                WHERE ref_dates.date BETWEEN '".$filtersDb['date_from']."' AND '".$filtersDb['date_to']."' 
                                AND ref_dates.date NOT IN 
                                (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday." AS schedule_holidays WHERE schedule_holidays.date NOT IN ('2020-12-28', '2020-12-29', '2020-12-30')) AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja
                            INNER JOIN ".$this->tableEmployee." AS ref_employees ON ref_employees.`enter_date` <= hari_kerja.selected_date AND (ref_employees.`quit_date` IS NULL OR ref_employees.`quit_date`>=hari_kerja.selected_date)
                            INNER JOIN ".$this->tableEmployeeLocationHistory." AS location_history ON location_history.id_employee = ref_employees.id AND ((location_history.start_date <= hari_kerja.selected_date AND location_history.end_date > hari_kerja.selected_date) OR (location_history.start_date <= hari_kerja.selected_date AND location_history.end_date IS NULL))
                            INNER JOIN ref_branches ON ref_branches.`id_branch_hr` = location_history.id_location
                            INNER JOIN ".$this->tableEmployeePositionHistory." AS position_history ON position_history.id_employee = ref_employees.id AND ((position_history.start_date <= hari_kerja.selected_date AND position_history.end_date > hari_kerja.selected_date) OR (position_history.start_date <= hari_kerja.selected_date AND position_history.end_date IS NULL))
                            INNER JOIN core_positions ON core_positions.id_position = position_history.id_position
                            LEFT JOIN ".$this->tableScheduleDivision." AS schedule_division ON schedule_division.id_employee = ref_employees.id AND schedule_division.date = hari_kerja.selected_date
                            WHERE ref_employees.id_department = '1' AND ref_branches.`id` = '1' AND schedule_division.id_schedule != '1'
                            GROUP BY YEAR(selected_date), WEEK(selected_date), DAY(selected_date)) AS table_ops
                            GROUP BY tahun,minggu) AS ops_core_jkt
                    ON ops_core_jkt.tahun = frt_jkt.tahun AND ops_core_jkt.minggu = frt_jkt.minggu) AS jkt")
            ->join("(SELECT frt_jkt.tahun, frt_jkt.minggu, ROUND(frt_jkt.frt_minggu,2) AS avg_frt,ops_jkt.avg_ops, ops_core_jkt.avg_ops_core FROM
                        (SELECT job_minggu.tahun, job_minggu.minggu, AVG(job_minggu.frt_hari) AS frt_minggu FROM
                        (SELECT YEAR(selected_date)AS tahun, WEEK(selected_date) AS minggu, DAY(selected_date) AS hari, IFNULL(SUM(job.hasil_frt),0) AS frt_hari FROM
                                    (SELECT ref_dates.date AS selected_date FROM ref_dates
                                        WHERE ref_dates.date BETWEEN '".$filtersDb['date_from']."' AND '".$filtersDb['date_to']."' 
                                        AND ref_dates.date NOT IN 
                                        (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday." AS schedule_holidays WHERE schedule_holidays.date NOT IN ('2020-12-28', '2020-12-29', '2020-12-30')) AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja
                                    LEFT JOIN 
                                    (SELECT IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),0) AS forklift_own, table_frt.frt AS frt_own, (table_frt.frt/IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),1))AS hasil_frt, work_orders.`id`, work_orders.`completed_at` FROM 
                                                        (SELECT IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt, work_order_goods.`id_work_order` FROM work_order_goods
                                                        LEFT JOIN ref_goods ON ref_goods.`id` = work_order_goods.`id_goods`
                                        GROUP BY work_order_goods.`id_work_order`) AS table_frt
                                                    LEFT JOIN work_orders ON work_orders.id = table_frt.id_work_order
                                                    LEFT JOIN handlings ON handlings.id = work_orders.id_handling
                                                    LEFT JOIN ref_handling_types ON ref_handling_types.`id` = handlings.`id_handling_type`
                                                    LEFT JOIN bookings ON bookings.id = handlings.id_booking
                                                    LEFT JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                                                    LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                                                    WHERE bookings.`id_branch` = '2' AND ref_handling_types.`multiplier_goods`!=0 AND work_orders.`completed_at`> '".$filtersDb['date_from']."' AND work_orders.`completed_at`<='".$filtersDb['date_to']."'
                                    GROUP BY work_orders.`id`) AS job
                                    ON DATE(job.`completed_at`) = hari_kerja.selected_date
                                GROUP BY YEAR(selected_date), WEEK(selected_date), DAY(selected_date)
                                UNION
                                SELECT YEAR(job.`completed_at`)AS tahun, WEEK(job.`completed_at`) AS minggu, DAY(job.`completed_at`) AS hari, IFNULL(SUM(job.hasil_frt),0) AS frt_hari FROM
                                    (SELECT IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),0) AS forklift_own, table_frt.frt AS frt_own, (table_frt.frt/IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),1))AS hasil_frt, work_orders.`id`, work_orders.`completed_at` FROM 
                                                        (SELECT IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt, work_order_goods.`id_work_order` FROM work_order_goods
                                                        LEFT JOIN ref_goods ON ref_goods.`id` = work_order_goods.`id_goods`
                                        GROUP BY work_order_goods.`id_work_order`) AS table_frt
                                                    LEFT JOIN work_orders ON work_orders.id = table_frt.id_work_order
                                                    LEFT JOIN handlings ON handlings.id = work_orders.id_handling
                                                    LEFT JOIN ref_handling_types ON ref_handling_types.`id` = handlings.`id_handling_type`
                                                    LEFT JOIN bookings ON bookings.id = handlings.id_booking
                                                    LEFT JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                                                    LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                                                    WHERE bookings.`id_branch` = '2' AND ref_handling_types.`multiplier_goods`!=0 AND work_orders.`completed_at`> '".$filtersDb['date_from']."' AND work_orders.`completed_at`<='".$filtersDb['date_to']."'
                                    GROUP BY work_orders.`id`) AS job
                                GROUP BY YEAR(job.`completed_at`), WEEK(job.`completed_at`), DAY(job.`completed_at`)) AS job_minggu         
                            GROUP BY tahun,minggu) AS frt_jkt
                    LEFT JOIN
                        (SELECT tahun,minggu, ROUND(AVG(count_ops),2) AS avg_ops FROM 
                            (SELECT YEAR(selected_date) AS tahun, WEEK(selected_date) AS minggu, COUNT(ref_employees.`id`) AS count_ops FROM 
                            (SELECT ref_dates.date AS selected_date FROM ref_dates
                                WHERE ref_dates.date BETWEEN '".$filtersDb['date_from']."' AND '".$filtersDb['date_to']."' 
                                AND ref_dates.date NOT IN 
                                (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday." AS schedule_holidays WHERE schedule_holidays.date NOT IN ('2020-12-28', '2020-12-29', '2020-12-30')) AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja
                            INNER JOIN ".$this->tableEmployee." AS ref_employees ON ref_employees.`enter_date` <= hari_kerja.selected_date AND (ref_employees.`quit_date` IS NULL OR ref_employees.`quit_date`>=hari_kerja.selected_date)
                            INNER JOIN ".$this->tableEmployeeLocationHistory." AS location_history ON location_history.id_employee = ref_employees.id AND ((location_history.start_date <= hari_kerja.selected_date AND location_history.end_date > hari_kerja.selected_date) OR (location_history.start_date <= hari_kerja.selected_date AND location_history.end_date IS NULL))
                            INNER JOIN ref_branches ON ref_branches.`id_branch_hr` = location_history.id_location
                            LEFT JOIN ".$this->tableScheduleDivision." AS schedule_division ON schedule_division.id_employee = ref_employees.id AND schedule_division.date = hari_kerja.selected_date
                            WHERE ref_employees.id_department = '1' AND ref_branches.`id` = '2' AND schedule_division.id_schedule != '1'
                            GROUP BY YEAR(selected_date), WEEK(selected_date), DAY(selected_date)) AS table_ops
                            GROUP BY tahun,minggu) AS ops_jkt
                    ON ops_jkt.tahun = frt_jkt.tahun AND ops_jkt.minggu = frt_jkt.minggu
                    LEFT JOIN
                        (SELECT tahun,minggu, ROUND(AVG(count_ops),2) AS avg_ops_core FROM 
                            (SELECT YEAR(selected_date) AS tahun, WEEK(selected_date) AS minggu, COUNT(ref_employees.`id`) AS count_ops FROM 
                            (SELECT ref_dates.date AS selected_date FROM ref_dates
                                WHERE ref_dates.date BETWEEN '".$filtersDb['date_from']."' AND '".$filtersDb['date_to']."' 
                                AND ref_dates.date NOT IN 
                                (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday." AS schedule_holidays WHERE schedule_holidays.date NOT IN ('2020-12-28', '2020-12-29', '2020-12-30')) AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja
                            INNER JOIN ".$this->tableEmployee." AS ref_employees ON ref_employees.`enter_date` <= hari_kerja.selected_date AND (ref_employees.`quit_date` IS NULL OR ref_employees.`quit_date`>=hari_kerja.selected_date)
                            INNER JOIN ".$this->tableEmployeeLocationHistory." AS location_history ON location_history.id_employee = ref_employees.id AND ((location_history.start_date <= hari_kerja.selected_date AND location_history.end_date > hari_kerja.selected_date) OR (location_history.start_date <= hari_kerja.selected_date AND location_history.end_date IS NULL))
                            INNER JOIN ref_branches ON ref_branches.`id_branch_hr` = location_history.id_location
                            INNER JOIN ".$this->tableEmployeePositionHistory." AS position_history ON position_history.id_employee = ref_employees.id AND ((position_history.start_date <= hari_kerja.selected_date AND position_history.end_date > hari_kerja.selected_date) OR (position_history.start_date <= hari_kerja.selected_date AND position_history.end_date IS NULL))
                            INNER JOIN core_positions ON core_positions.id_position = position_history.id_position
                            LEFT JOIN ".$this->tableScheduleDivision." AS schedule_division ON schedule_division.id_employee = ref_employees.id AND schedule_division.date = hari_kerja.selected_date
                            WHERE ref_employees.id_department = '1' AND ref_branches.`id` = '2' AND schedule_division.id_schedule != '1'
                            GROUP BY YEAR(selected_date), WEEK(selected_date), DAY(selected_date)) AS table_ops
                            GROUP BY tahun,minggu) AS ops_core_jkt
                    ON ops_core_jkt.tahun = frt_jkt.tahun AND ops_core_jkt.minggu = frt_jkt.minggu) AS mdn", 'jkt.tahun = mdn.tahun AND jkt.minggu = mdn.minggu','left')
            ->join("(SELECT frt_jkt.tahun, frt_jkt.minggu, ROUND(frt_jkt.frt_minggu,2) AS avg_frt, ops_jkt.avg_ops,ops_core_jkt.avg_ops_core FROM
                        (SELECT job_minggu.tahun, job_minggu.minggu, AVG(job_minggu.frt_hari) AS frt_minggu FROM
                        (SELECT YEAR(selected_date)AS tahun, WEEK(selected_date) AS minggu, DAY(selected_date) AS hari, IFNULL(SUM(job.hasil_frt),0) AS frt_hari FROM
                                    (SELECT ref_dates.date AS selected_date FROM ref_dates
                                        WHERE ref_dates.date BETWEEN '".$filtersDb['date_from']."' AND '".$filtersDb['date_to']."' 
                                        AND ref_dates.date NOT IN 
                                        (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday." AS schedule_holidays WHERE schedule_holidays.date NOT IN ('2020-12-28', '2020-12-29', '2020-12-30')) AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja
                                    LEFT JOIN 
                                    (SELECT IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),0) AS forklift_own, table_frt.frt AS frt_own, (table_frt.frt/IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),1))AS hasil_frt, work_orders.`id`, work_orders.`completed_at` FROM 
                                                        (SELECT IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt, work_order_goods.`id_work_order` FROM work_order_goods
                                                        LEFT JOIN ref_goods ON ref_goods.`id` = work_order_goods.`id_goods`
                                        GROUP BY work_order_goods.`id_work_order`) AS table_frt
                                                    LEFT JOIN work_orders ON work_orders.id = table_frt.id_work_order
                                                    LEFT JOIN handlings ON handlings.id = work_orders.id_handling
                                                    LEFT JOIN ref_handling_types ON ref_handling_types.`id` = handlings.`id_handling_type`
                                                    LEFT JOIN bookings ON bookings.id = handlings.id_booking
                                                    LEFT JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                                                    LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                                                    WHERE bookings.`id_branch` = '5' AND ref_handling_types.`multiplier_goods`!=0 AND work_orders.`completed_at`> '".$filtersDb['date_from']."' AND work_orders.`completed_at`<='".$filtersDb['date_to']."' 
                                    GROUP BY work_orders.`id`) AS job
                                    ON DATE(job.`completed_at`) = hari_kerja.selected_date
                                GROUP BY YEAR(selected_date), WEEK(selected_date), DAY(selected_date)
                                UNION
                                SELECT YEAR(job.`completed_at`)AS tahun, WEEK(job.`completed_at`) AS minggu, DAY(job.`completed_at`) AS hari, IFNULL(SUM(job.hasil_frt),0) AS frt_hari FROM
                                    (SELECT IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),0) AS forklift_own, table_frt.frt AS frt_own, (table_frt.frt/IF(work_order_components.`id_component` = '1' AND work_order_components.`is_owned` = 'OWNED',IF(COUNT(work_order_component_heavy_equipments.`id`)=0,work_order_components.`quantity`,COUNT(work_order_component_heavy_equipments.`id`)),1))AS hasil_frt, work_orders.`id`, work_orders.`completed_at` FROM 
                                                        (SELECT IF(IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)>IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0), IFNULL(SUM((ref_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), IFNULL(SUM(ref_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt, work_order_goods.`id_work_order` FROM work_order_goods
                                                        LEFT JOIN ref_goods ON ref_goods.`id` = work_order_goods.`id_goods`
                                        GROUP BY work_order_goods.`id_work_order`) AS table_frt
                                                    LEFT JOIN work_orders ON work_orders.id = table_frt.id_work_order
                                                    LEFT JOIN handlings ON handlings.id = work_orders.id_handling
                                                    LEFT JOIN ref_handling_types ON ref_handling_types.`id` = handlings.`id_handling_type`
                                                    LEFT JOIN bookings ON bookings.id = handlings.id_booking
                                                    LEFT JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                                                    LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_work_order_component = work_order_components.id
                                                    WHERE bookings.`id_branch` = '5' AND ref_handling_types.`multiplier_goods`!=0 AND work_orders.`completed_at`> '".$filtersDb['date_from']."' AND work_orders.`completed_at`<='".$filtersDb['date_to']."' 
                                    GROUP BY work_orders.`id`) AS job
                                GROUP BY YEAR(job.`completed_at`), WEEK(job.`completed_at`), DAY(job.`completed_at`)) AS job_minggu         
                            GROUP BY tahun,minggu) AS frt_jkt
                    LEFT JOIN
                        (SELECT tahun,minggu, ROUND(AVG(count_ops),2) AS avg_ops FROM 
                            (SELECT YEAR(selected_date) AS tahun, WEEK(selected_date) AS minggu, COUNT(ref_employees.`id`) AS count_ops FROM 
                            (SELECT ref_dates.date AS selected_date FROM ref_dates
                                WHERE ref_dates.date BETWEEN '".$filtersDb['date_from']."' AND '".$filtersDb['date_to']."' 
                                AND ref_dates.date NOT IN 
                                (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday." AS schedule_holidays WHERE schedule_holidays.date NOT IN ('2020-12-28', '2020-12-29', '2020-12-30')) AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja
                            INNER JOIN ".$this->tableEmployee." AS ref_employees ON ref_employees.`enter_date` <= hari_kerja.selected_date AND (ref_employees.`quit_date` IS NULL OR ref_employees.`quit_date`>=hari_kerja.selected_date)
                            INNER JOIN ".$this->tableEmployeeLocationHistory." AS location_history ON location_history.id_employee = ref_employees.id AND ((location_history.start_date <= hari_kerja.selected_date AND location_history.end_date > hari_kerja.selected_date) OR (location_history.start_date <= hari_kerja.selected_date AND location_history.end_date IS NULL))
                            INNER JOIN ref_branches ON ref_branches.`id_branch_hr` = location_history.id_location
                            LEFT JOIN ".$this->tableScheduleDivision." AS schedule_division ON schedule_division.id_employee = ref_employees.id AND schedule_division.date = hari_kerja.selected_date
                            WHERE ref_employees.id_department = '1' AND ref_branches.`id` = '5' AND schedule_division.id_schedule != '1'
                            GROUP BY YEAR(selected_date), WEEK(selected_date), DAY(selected_date)) AS table_ops
                            GROUP BY tahun,minggu) AS ops_jkt
                    ON ops_jkt.tahun = frt_jkt.tahun AND ops_jkt.minggu = frt_jkt.minggu
                    LEFT JOIN
                        (SELECT tahun,minggu, ROUND(AVG(count_ops),2) AS avg_ops_core FROM 
                            (SELECT YEAR(selected_date) AS tahun, WEEK(selected_date) AS minggu, COUNT(ref_employees.`id`) AS count_ops FROM 
                            (SELECT ref_dates.date AS selected_date FROM ref_dates
                                WHERE ref_dates.date BETWEEN '".$filtersDb['date_from']."' AND '".$filtersDb['date_to']."' 
                                AND ref_dates.date NOT IN 
                                (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday." AS schedule_holidays WHERE schedule_holidays.date NOT IN ('2020-12-28', '2020-12-29', '2020-12-30')) AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja
                            INNER JOIN ".$this->tableEmployee." AS ref_employees ON ref_employees.`enter_date` <= hari_kerja.selected_date AND (ref_employees.`quit_date` IS NULL OR ref_employees.`quit_date`>=hari_kerja.selected_date)
                            INNER JOIN ".$this->tableEmployeeLocationHistory." AS location_history ON location_history.id_employee = ref_employees.id AND ((location_history.start_date <= hari_kerja.selected_date AND location_history.end_date > hari_kerja.selected_date) OR (location_history.start_date <= hari_kerja.selected_date AND location_history.end_date IS NULL))
                            INNER JOIN ref_branches ON ref_branches.`id_branch_hr` = location_history.id_location
                            INNER JOIN ".$this->tableEmployeePositionHistory." AS position_history ON position_history.id_employee = ref_employees.id AND ((position_history.start_date <= hari_kerja.selected_date AND position_history.end_date > hari_kerja.selected_date) OR (position_history.start_date <= hari_kerja.selected_date AND position_history.end_date IS NULL))
                            INNER JOIN core_positions ON core_positions.id_position = position_history.id_position
                            LEFT JOIN ".$this->tableScheduleDivision." AS schedule_division ON schedule_division.id_employee = ref_employees.id AND schedule_division.date = hari_kerja.selected_date
                            WHERE ref_employees.id_department = '1' AND ref_branches.`id` = '5' AND schedule_division.id_schedule != '1'
                            GROUP BY YEAR(selected_date), WEEK(selected_date), DAY(selected_date)) AS table_ops
                            GROUP BY tahun,minggu) AS ops_core_jkt
                    ON ops_core_jkt.tahun = frt_jkt.tahun AND ops_core_jkt.minggu = frt_jkt.minggu) AS sby", 'jkt.tahun = sby.tahun AND jkt.minggu = sby.minggu','left')
            ->join("(SELECT ref_targets.id, ref_targets.target AS target_all, ref_target_branches.target, ref_target_branches.id_branch FROM
                    ref_targets
                    LEFT JOIN ref_target_branches ON ref_target_branches.id_target = ref_targets.id
                    WHERE ref_target_branches.id_branch = 1) AS target_forklift_jkt",
                    "target_forklift_jkt.id = 1","left")
            ->join("(SELECT ref_targets.id, ref_targets.target AS target_all, ref_target_branches.target, ref_target_branches.id_branch FROM
                    ref_targets
                    LEFT JOIN ref_target_branches ON ref_target_branches.id_target = ref_targets.id
                    WHERE ref_target_branches.id_branch = 2) AS target_forklift_mdn",
                    "target_forklift_mdn.id = 1","left")      
            ->join("(SELECT ref_targets.id, ref_targets.target AS target_all, ref_target_branches.target, ref_target_branches.id_branch FROM
                    ref_targets
                    LEFT JOIN ref_target_branches ON ref_target_branches.id_target = ref_targets.id
                    WHERE ref_target_branches.id_branch = 5) AS target_forklift_sby",
                    "target_forklift_sby.id = 1","left")
            ->join("ref_targets","ref_targets.id = 1","left");

        return $workOrders->get()->result_array();
    }

    /**
     * Get report performance.
     *
     * @return array
     */
    public function getOpsByCondition($filters = []){
        $workOrders = $this->db->select([
            'selected_date',
            'DAYNAME(selected_date) AS nama_hari',
            'COUNT(ref_employees.name) AS count_ops',
            "GROUP_CONCAT(ref_employees.name ORDER BY ref_employees.id SEPARATOR ', ') AS name_ops",
        ])
            ->from("(SELECT * FROM 
                        (SELECT ADDDATE('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date FROM
                        (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
                        (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
                        (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
                        (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
                        (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4) v
                        WHERE selected_date BETWEEN '".$filters['year']."-01-01' AND CURRENT_DATE
                        AND selected_date NOT IN 
                        (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday." AS schedule_holidays WHERE schedule_holidays.date NOT IN ('2020-12-28', '2020-12-29', '2020-12-30')) AND DAYOFWEEK(selected_date) != 1) AS hari_kerja")
            ->join($this->tableEmployee." AS ref_employees", "ref_employees.`enter_date` <= hari_kerja.selected_date AND (ref_employees.`quit_date` IS NULL OR ref_employees.`quit_date`>=hari_kerja.selected_date)","left")
            ->join($this->tableScheduleDivision." AS schedule_division", "schedule_division.id_employee = ref_employees.id AND schedule_division.date = hari_kerja.selected_date","left")
            ->join($this->tableEmployeeLocationHistory." AS location_history","location_history.id_employee = ref_employees.id AND ((location_history.start_date <= hari_kerja.selected_date AND location_history.end_date > hari_kerja.selected_date) OR (location_history.start_date <= hari_kerja.selected_date AND location_history.end_date IS NULL))","inner")
            ->join("ref_branches","ref_branches.`id_branch_hr` = location_history.id_location","left")
            ->where("ref_employees.id_department = '1'")
            ->where("schedule_division.id_schedule != '1'")
            ->group_by("YEAR(hari_kerja.selected_date), WEEK(hari_kerja.selected_date), DAY(hari_kerja.selected_date)");
        
        if (key_exists('week', $filters) && !empty($filters['week'])) {
            $workOrders->where("WEEK(hari_kerja.selected_date)", $filters['week'])
                    ->where("YEAR(hari_kerja.selected_date)",$filters['year']);
        }
        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            $workOrders->where("ref_branches.`id`", $filters['branch']);
        }
        if (key_exists('is_core', $filters) && !empty($filters['is_core'])) {
            if($filters['is_core']){
                $workOrders->join($this->tableEmployeePositionHistory." AS position_history","position_history.id_employee = ref_employees.id AND ((position_history.start_date <= hari_kerja.selected_date AND position_history.end_date > hari_kerja.selected_date) OR (position_history.start_date <= hari_kerja.selected_date AND position_history.end_date IS NULL))","inner");
                $workOrders->join("core_positions","core_positions.id_position = position_history.id_position","inner");
            }
        }
        return $workOrders->get()->result_array();
    }

     /**
     * Get heavy equipment internal report.
     *
     * @param array $filters
     * @return array
     */
    public function getHeavyEquipmentInternal($filters = [])
    {
        if (empty($filters['branch'])) {
            $filters['branch'] = get_active_branch('id');
        }
        $workOrders = $this->db
                    ->select([
                        'selected_date',
                        'ref_heavy_equipments.name AS name_heavy_equipment',
                        'ref_heavy_equipments.type AS type_heavy_equipment',
                        'DATE(work_orders.`completed_at`) AS tgl',
                        'TIME(MIN(work_orders.`taken_at`)) AS start_job',
                        'TIME(MAX(work_orders.`completed_at`)) AS finish_job',
                        'ref_service_hours.service_day AS day_name',
                        'ref_service_hours.service_time_start',
                        'ref_service_hours.service_time_end',
                        // "CONCAT(ref_people.name,' (',GROUP_CONCAT(DISTINCT ref_handling_types.handling_type SEPARATOR ', '), ');') AS handling_type",
                        "GROUP_CONCAT(
                            DISTINCT CONCAT(ref_people.name,',',ref_handling_types.handling_type,',',bookings.no_reference)
                            ORDER BY ref_people.name
                            SEPARATOR '; ') AS keterangan",
                        "GROUP_CONCAT(DISTINCT ref_handling_types.handling_type SEPARATOR ', ') AS handling_type",
                        'IF(TIME(MAX(work_orders.`completed_at`)) > ref_service_hours.service_time_end,TIMESTAMPDIFF(HOUR,ref_service_hours.service_time_end,TIME(MAX(work_orders.`completed_at`))) + 1,0) AS lembur',
                        'IF(ref_heavy_equipments.name is NOT NULL,ROUND(TIMESTAMPDIFF(MINUTE,TIME(MIN(work_orders.`taken_at`)),TIME(MAX(work_orders.`completed_at`)))/60,2),0) AS jam',
                        "GROUP_CONCAT(DISTINCT ref_people.name SEPARATOR ', ') AS customer",
                    ])
                    ->from("(SELECT * FROM 
                                (SELECT ADDDATE('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date FROM
                                (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
                                (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
                                (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
                                (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
                                (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4) v
                                WHERE selected_date BETWEEN DATE_FORMAT(NOW() ,'%Y-01-01') AND CURRENT_DATE
                                AND selected_date NOT IN 
                                (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday." AS schedule_holidays WHERE schedule_holidays.date NOT IN ('2020-12-28', '2020-12-29', '2020-12-30')) AND DAYOFWEEK(selected_date) != 1) AS hari_kerja")
                    ->join('work_orders','DATE(work_orders.`completed_at`) = hari_kerja.selected_date  AND DATE(work_orders.`taken_at`) = hari_kerja.selected_date','left')
                    ->join('handlings','handlings.id = work_orders.id_handling','left')
                    ->join('ref_people', 'ref_people.id = handlings.id_customer',"left")
                    ->join('bookings','bookings.id = handlings.id_booking','left')
                    ->join('ref_handling_types','ref_handling_types.id = handlings.id_handling_type','left')
                    ->join('ref_service_hours','DAYNAME(hari_kerja.selected_date) = ref_service_hours.service_day AND ref_service_hours.id_branch = 1','left') // id branch 1 krn smua branch sama
                    ->join('work_order_components','work_order_components.id_work_order = work_orders.id','left')
                    ->join('work_order_component_heavy_equipments','work_order_component_heavy_equipments.id_work_order_component = work_order_components.id','left')
                    ->group_by('hari_kerja.selected_date, ref_heavy_equipments.id')
                    ->order_by('`ref_heavy_equipments`.`id` DESC');

        if (!empty($filters)) {
            if (key_exists('heavy_equipment', $filters) && !empty($filters['heavy_equipment'])) {
                if (is_array($filters['heavy_equipment'])) {
                    $arrayString = implode(', ',$filters['heavy_equipment']);
                    $workOrders->join('ref_heavy_equipments','ref_heavy_equipments.id = work_order_component_heavy_equipments.id_heavy_equipment AND ref_heavy_equipments.id IN ('.$arrayString.')','left');
                } else {
                    $workOrders->join('ref_heavy_equipments','ref_heavy_equipments.id = work_order_component_heavy_equipments.id_heavy_equipment AND ref_heavy_equipments.id = '.$filters['heavy_equipment'],'left');
                }
            }
            if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                $workOrders->where('hari_kerja.selected_date>=', sql_date_format($filters['date_from'], false));
            }
            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $workOrders->where('hari_kerja.selected_date<=', sql_date_format($filters['date_to'], false));
            }
            if (key_exists('customer', $filters) && !empty($filters['customer'])) {
                if (is_array($filters['customer'])) {
                    $arrayString = implode(', ',$filters['customer']);
                    $workOrders->where("IF(ref_heavy_equipments.name is null,1,ref_people.id in (".$arrayString."))");
                } else {
                    $workOrders->where("IF(ref_heavy_equipments.name is null,1,ref_people.id=".$filters['customer'].")");
                }
            }
        }
        $baseQuery = $this->db->get_compiled_select();
        $report = $this->db->select([
            'reports.*',
        ])
            ->from("({$baseQuery}) AS reports")
            ->group_by('reports.selected_date');
        return $report->get()->result_array();
    }

     /**
     * Get heavy equipment external report.
     *
     * @param array $filters
     * @return array
     */
    public function getHeavyEquipmentExternal($filters = [])
    {
        if (empty($filters['branch'])) {
            $filters['branch'] = get_active_branch('id');
        }
        $workOrders = $this->db
                    ->select([
                        'selected_date',
                        'heavy_equipment_entry_permits.heep_code AS name_heavy_equipment',
                        'heavy_equipment_entry_permits.no_heep AS type_heavy_equipment',
                        'DATE(work_orders.`completed_at`) AS tgl',
                        'TIME(MIN(work_orders.`taken_at`)) AS start_job',
                        'TIME(MAX(work_orders.`completed_at`)) AS finish_job',
                        'ref_service_hours.service_day AS day_name',
                        'ref_service_hours.service_time_start',
                        'ref_service_hours.service_time_end',
                        // "GROUP_CONCAT(DISTINCT ref_handling_types.handling_type SEPARATOR ', ') AS handling_type",
                        "GROUP_CONCAT(
                            DISTINCT CONCAT(ref_people.name,',',ref_handling_types.handling_type,',',bookings.no_reference)
                            ORDER BY ref_people.name
                            SEPARATOR '; ') AS keterangan",
                        "GROUP_CONCAT(DISTINCT ref_handling_types.handling_type SEPARATOR ', ') AS handling_type",
                        'IF(TIME(MAX(work_orders.`completed_at`)) > ref_service_hours.service_time_end,TIMESTAMPDIFF(HOUR,ref_service_hours.service_time_end,TIME(MAX(work_orders.`completed_at`))) + 1,0) AS lembur',
                        'DATE(heavy_equipment_entry_permits.checked_out_at) AS date_checkout',
                        'IF(heavy_equipment_entry_permits.heep_code is NOT NULL,ROUND(TIMESTAMPDIFF(MINUTE,TIME(MIN(work_orders.`taken_at`)),TIME(MAX(work_orders.`completed_at`)))/60,2), 0) AS jam',
                        'DATE(heep.checked_in_at) AS date_checked_in',
                        'DATE(heep.checked_out_at) AS date_checked_out',
                        "GROUP_CONCAT(DISTINCT ref_people.name SEPARATOR ', ') AS customer",
                    ])
                    ->from("(SELECT * FROM 
                                (SELECT ADDDATE('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date FROM
                                (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
                                (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
                                (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
                                (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
                                (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4) v
                                WHERE selected_date BETWEEN DATE_FORMAT(NOW() ,'%Y-01-01') AND CURRENT_DATE
                                AND selected_date NOT IN 
                                (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday." AS schedule_holidays WHERE schedule_holidays.date NOT IN ('2020-12-28', '2020-12-29', '2020-12-30')) AND DAYOFWEEK(selected_date) != 1) AS hari_kerja")
                    ->join('work_orders','DATE(work_orders.`completed_at`) = hari_kerja.selected_date  AND DATE(work_orders.`taken_at`) = hari_kerja.selected_date','left')
                    ->join('handlings','handlings.id = work_orders.id_handling','left')
                    ->join('ref_people', 'ref_people.id = handlings.id_customer',"left")
                    ->join('bookings','bookings.id = handlings.id_booking','left')
                    ->join('ref_handling_types','ref_handling_types.id = handlings.id_handling_type','left')
                    ->join('ref_service_hours','DAYNAME(hari_kerja.selected_date) = ref_service_hours.service_day AND ref_service_hours.id_branch = 1','left') // id branch 1 krn smua branch sama
                    ->join('work_order_components','work_order_components.id_work_order = work_orders.id','left')
                    ->join('work_order_component_heeps','work_order_component_heeps.id_work_order_component = work_order_components.id','left')
                    ->group_by('hari_kerja.selected_date, heavy_equipment_entry_permits.id')
                    ->order_by('`heavy_equipment_entry_permits`.`id` DESC');

        if (!empty($filters)) {
            if (key_exists('heavy_equipment', $filters) && !empty($filters['heavy_equipment'])) {
                if (is_array($filters['heavy_equipment'])) {
                    $arrayString = implode(', ',$filters['heavy_equipment']);
                    $workOrders->join('heavy_equipment_entry_permits','heavy_equipment_entry_permits.id = work_order_component_heeps.id_heep AND heavy_equipment_entry_permits.id IN ('.$arrayString.')','left');
                    $workOrders->join('heavy_equipment_entry_permits AS heep','heep.id IN ('.$arrayString.')','left');
                } else {
                    $workOrders->join('heavy_equipment_entry_permits','heavy_equipment_entry_permits.id = work_order_component_heeps.id_heep AND heavy_equipment_entry_permits.id = '.$filters['heavy_equipment'],'left');
                    $workOrders->join('heavy_equipment_entry_permits AS heep','heep.id = '.$filters['heavy_equipment'],'left');
                }
            }
            if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                $workOrders->where('hari_kerja.selected_date>=', sql_date_format($filters['date_from'], false));
            }
            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $workOrders->where('hari_kerja.selected_date<=', sql_date_format($filters['date_to'], false));
            }
            if (key_exists('customer', $filters) && !empty($filters['customer'])) {
                if (is_array($filters['customer'])) {
                    $arrayString = implode(', ',$filters['customer']);
                    $workOrders->where("IF(`heavy_equipment_entry_permits`.`heep_code` is null,1,ref_people.id in (".$arrayString."))");
                } else {
                    $workOrders->where("IF(`heavy_equipment_entry_permits`.`heep_code` is null,1,ref_people.id=".$filters['customer'].")");
                }
            }
        }
        $baseQuery = $this->db->get_compiled_select();
        $report = $this->db->select([
            'reports.*',
        ])
            ->from("({$baseQuery}) AS reports")
            ->group_by('reports.selected_date');
        return $report->get()->result_array();
    }

    /**
     * Get all work orders with or without deleted records.
     * @param array $filters
     * @param bool $withTrashed
     * @return array
     */
    public function getAllWorkOrderPhoto($filters = [], $withTrashed = false)
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        // alias column name by index for sorting data table library
        $columnOrder = [
            "work_orders.id",
            "ref_people.name",
            "work_orders.no_work_order",
            "ref_handling_types.handling_type",
            "work_orders.gate_in_date",
            "work_orders.completed_at",
            "work_orders.updated_at",
            "work_orders.locked_at",
            "work_orders.status",
            "work_orders.id",
        ];
        $columnSort = $columnOrder[$column];

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $this->db->start_cache();

        //$workOrders = $this->getWorkOrderBaseQuery($branchId, $userType, $customerId);
        $workOrders = $this->getBaseQueryIndex($branchId, $userType, $customerId)
                    ->join('ref_booking_types', 'ref_booking_types.`id` = bookings.`id_booking_type`')
                    ->join('work_order_goods', 'work_order_goods.`id_work_order` = work_orders.`id`', 'left')
                    ->group_by('work_orders.id');
        $workOrders->where('work_orders.status', WorkOrderModel::STATUS_COMPLETED);
        $workOrders->where('ref_handling_types.photo', true);
        $workOrders->where('ref_booking_types.category', 'INBOUND');

        if (!$withTrashed) {
            $workOrders->where('work_orders.is_deleted', false);
        }

        if (!empty($filters)) {
            if (key_exists('customer', $filters) && !empty($filters['customer'])) {
                if (is_array($filters['customer'])) {
                    $workOrders->where_in('ref_people.id', $filters['customer']);
                } else {
                    $workOrders->where('ref_people.id', $filters['customer']);
                }
            }

            if (key_exists('goods', $filters) && !empty($filters['goods'])) {
                if (is_array($filters['goods'])) {
                    $workOrders->where_in('work_order_goods.id_goods', $filters['goods']);
                } else {
                    $workOrders->where('work_order_goods.id_goods', $filters['goods']);
                }
            }

            if (key_exists('handling_type', $filters) && !empty($filters['handling_type'])) {
                if (is_array($filters['handling_type'])) {
                    $workOrders->where_in('ref_handling_types.id', $filters['handling_type']);
                } else {
                    $workOrders->where('ref_handling_types.id', $filters['handling_type']);
                }
            }

            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
                if ($filters['date_type'] == "job_date") {
                    if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                        $workOrders->where('DATE(work_orders.completed_at)>=', sql_date_format($filters['date_from'], false));
                    }
                    if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                        $workOrders->where('DATE(work_orders.completed_at)<=', sql_date_format($filters['date_to'], false));
                    }
                }

                if ($filters['date_type'] == "update_job_date") {
                    if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                        $workOrders->where('DATE(work_orders.updated_at)>=', sql_date_format($filters['date_from'], false));
                    }
                    if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                        $workOrders->where('DATE(work_orders.updated_at)<=', sql_date_format($filters['date_to'], false));
                    }
                }
            }

            if (key_exists('people', $filters) && !empty($filters['people'])) {
                if (is_array($filters['people'])) {
                    $workOrders->where_in('work_orders.updated_by', $filters['people']);
                } else {
                    $workOrders->where('work_orders.updated_by', $filters['people']);
                }
            }

            if (key_exists('gate_status', $filters) && !empty($filters['gate_status']) && $filters['gate_status'] == 'checkout') {
                $workOrders->where('work_orders.status', WorkOrderModel::STATUS_COMPLETED)
                        ->where('work_orders.gate_out_date' , null);
            }
        }

        if (!empty($search)) {
            $workOrders
                ->group_start()
                ->like('work_orders.no_work_order', trim($search))
                ->or_like('ref_people.name', trim($search))
                ->or_like('bookings.no_booking', trim($search))
                ->or_like('bookings.no_reference', trim($search))
                ->or_like('handlings.no_handling', trim($search))
                ->or_like('work_orders.gate_in_date', trim($search))
                ->or_like('work_orders.gate_out_date', trim($search))
                ->or_like('work_orders.completed_at', trim($search))
                ->or_like('work_orders.status', trim($search))
                ->or_like('work_orders.description', trim($search))
                ->or_like('ref_handling_types.handling_type', trim($search))
                ->group_end();
        }

        $workOrders->stop_cache();

        if ($start < 0) {
            $workOrderData = $workOrders->get()->result_array();
            $workOrders->flush_cache();
            return $workOrderData;
        }

        // counting result is slow, use simple pagination or cache to share result set like below
        $distinctQueryParams = filter_data_by_keys($filters, ['customer', 'handling_type', 'date_type', 'date_from', 'date_to', 'gate_status', 'people', 'search']);
        $cacheIdxKey = 'wo-idx-count-' . $branchId . '-' . md5(json_encode($distinctQueryParams));
        $workOrderTotal = cache_remember($cacheIdxKey, 60, function() use ($workOrders) {
            return $workOrders->count_all_results();
        });

        //$workOrderTotal = $workOrders->count_all_results();
        $workOrdersPage = $workOrders->order_by($columnSort, $sort)->limit($length, $start);
        $workOrderData = $workOrdersPage->get()->result_array();

        foreach ($workOrderData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($workOrderData),
            "recordsFiltered" => $workOrderTotal,
            "data" => $workOrderData
        ];
        $this->db->flush_cache();

        return $pageData;
    }
}
