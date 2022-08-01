<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class ReportPlanRealizationModel
 * @property HeavyEquipmentModel $heavyEquipment
 */
class ReportPlanRealizationModel extends MY_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('HeavyEquipmentModel', 'heavyEquipment');
    }

    /**
     * Get labor data.
     *
     * @param array $filters
     * @return array
     */
    private function getLabor($filters = [])
    {
        $date = key_exists('date', $filters) ? ("'" . $filters['date'] . "'") : 'CURDATE()';
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $baseQuery = $this->db
            ->select([
                'ref_branches.branch',
                'IFNULL(SUM(IF(guest.checkout IS NULL, 1, 0)), 0) AS labor_total',
                'IFNULL(SUM(IF(guest.checkin IS NOT NULL AND guest.checkout IS NULL, 1, 0)), 0) AS labor_realization_total',
            ])
            ->from(env('DB_VMS_DATABASE') . '.guest')
            ->join('ref_branches', 'ref_branches.id_branch_vms = guest.id_branch')
            ->where([
                'guest.laborers_type' => 'BURUH',
                'DATE(guest.janjian) = ' . $date => null
            ]);
        if (!empty($branchId)) {
            $baseQuery->where('ref_branches.id', $branchId);
        }
        $labor = $baseQuery->get()->row_array();

        // get detail transactions
        $baseQuery = $this->db
            ->select([
                'guest.id',
                'guest.uniqid AS no_reference',
                'guest.nama_visitor AS visitor',
                '"EXTERNAL" AS type'
            ])
            ->from(env('DB_VMS_DATABASE') . '.guest')
            ->join('ref_branches', 'ref_branches.id_branch_vms = guest.id_branch')
            ->where([
                'guest.laborers_type' => 'BURUH',
                'DATE(guest.janjian) = ' . $date => null,
                'ref_branches.id' => $branchId
            ]);
        $laborTransactions = $baseQuery->get()->result_array();

        return [
            'resource' => 'Labor',
            'plan' => get_if_exist($labor, 'labor_total', 0),
            'unit' => 'People',
            'realization' => get_if_exist($labor, 'labor_realization_total', 0),
            'description' => 'VMS Labor appointment today that not checked out compared by checked in',
            'transactions' => $laborTransactions
        ];
    }

    /**
     * Get heavy equipments.
     *
     * @param array $filters
     * @return array
     */
    private function getHeavyEquipments($filters = [])
    {
        $date = key_exists('date', $filters) ? ("'" . $filters['date'] . "'") : 'CURDATE()';
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        // get external data
        $baseQueryExternal = $this->db
            ->select([
                'requisitions.id',
                'requisitions.no_requisition',
                'requisitions.request_title',
                'ref_item_categories.item_name',
                'ref_employees.name',
                'ref_branches.branch',
                'requisition_items.quantity',
                'IFNULL(realizations.total, 0) AS total_used'
            ])
            ->from(env('DB_PURCHASE_DATABASE') . '.requisitions')
            ->join("(
                SELECT id_requisition, SUM(quantity) AS quantity
                FROM " . env('DB_PURCHASE_DATABASE') . '.requisition_items' . "
                GROUP BY id_requisition
            ) AS requisition_items", 'requisition_items.id_requisition = requisitions.id')
            ->join(env('DB_PURCHASE_DATABASE') . '.ref_item_categories', 'ref_item_categories.id = requisitions.id_item_category')
            ->join('(
                SELECT DISTINCT id_requisition, id_branch 
                FROM heavy_equipment_entry_permits
            ) AS hep_branches', 'hep_branches.id_requisition = requisitions.id')
            ->join(EmployeeModel::$tableEmployee, 'ref_employees.id = requisitions.id_employee', 'left')
            ->join('ref_branches', 'ref_branches.id = hep_branches.id_branch', 'left')
            ->join("(
                SELECT id_requisition, SUM(IF(total_used_in_work_order > 0, 1, 0)) AS total
                FROM (
                    SELECT
                      id_requisition,
                      heavy_equipment_entry_permits.id AS id_heep,
                      COUNT(work_order_component_heeps.id) AS total_used_in_work_order
                    FROM heavy_equipment_entry_permits
                    LEFT JOIN work_order_component_heeps ON work_order_component_heeps.id_heep = heavy_equipment_entry_permits.id
                    GROUP BY id_requisition, heavy_equipment_entry_permits.id
                    HAVING total_used_in_work_order > 0
                ) AS heep GROUP BY id_requisition
            ) AS realizations", 'realizations.id_requisition = requisitions.id', 'left')
            ->where([
                'DATE(requisitions.deadline) = ' . $date => null,
                'ref_branches.id' => $branchId
            ])
            ->where_in('ref_item_categories.item_name', ['FORKLIFT', 'CRANE']);
        $baseExternalHeavyEquipments = $baseQueryExternal->get()->result_array();

        // get internal data
        $baseQueryExternal = $this->db
            ->select([
                'ref_heavy_equipments.id',
                'ref_heavy_equipment_branches.id_branch',
                'ref_heavy_equipments.type',
                'ref_heavy_equipments.name',
                '1 AS quantity',
                'IFNULL(realizations.total, 0) AS total_used'
            ])
            ->from('ref_heavy_equipments')
            ->join('ref_heavy_equipment_branches', 'ref_heavy_equipment_branches.id_heavy_equipment = ref_heavy_equipments.id')
            ->join("(
                SELECT
                  ref_heavy_equipments.id,
                  IF(COUNT(work_order_component_heavy_equipments.id) > 0, 1, 0) AS total
                FROM ref_heavy_equipments
                LEFT JOIN work_order_component_heavy_equipments ON work_order_component_heavy_equipments.id_heavy_equipment = ref_heavy_equipments.id
                WHERE DATE(work_order_component_heavy_equipments.created_at) = CURDATE()
                GROUP BY ref_heavy_equipments.id
            ) AS realizations", 'realizations.id = ref_heavy_equipments.id', 'left')
            ->where([
                'ref_heavy_equipments.is_deleted' => false,
                'ref_heavy_equipment_branches.id_branch' => $branchId
            ])
            ->where_in('ref_heavy_equipments.type', ['FORKLIFT', 'CRANE']);
        $baseInternalHeavyEquipments = $baseQueryExternal->get()->result_array();

        // simplify for transaction details
        $externalHeavyEquipments = array_map(function ($heavyEquipment) {
            return [
                'id' => $heavyEquipment['id'],
                'type' => "EXTERNAL",
                'no_reference' => $heavyEquipment['no_requisition'],
                'item_name' => strtoupper(trim($heavyEquipment['item_name'])),
                'request_title' => $heavyEquipment['request_title'],
                'plan' => $heavyEquipment['quantity'],
                'realization' => $heavyEquipment['total_used'],
            ];
        }, $baseExternalHeavyEquipments);

        $internalHeavyEquipments = array_map(function ($heavyEquipment) {
            return [
                'id' => $heavyEquipment['id'],
                'type' => "INTERNAL",
                'no_reference' => $heavyEquipment['id'],
                'item_name' => strtoupper(trim($heavyEquipment['type'])),
                'request_title' => $heavyEquipment['name'],
                'plan' => $heavyEquipment['quantity'],
                'realization' => $heavyEquipment['total_used'],
            ];
        }, $baseInternalHeavyEquipments);

        $heavyEquipmentTransactions = array_merge($externalHeavyEquipments, $internalHeavyEquipments);

        // split by categories
        $forklifts = array_filter($heavyEquipmentTransactions, function ($heavyEquipment) {
            return strtolower($heavyEquipment['item_name']) == 'forklift';
        });
        $cranes = array_filter($heavyEquipmentTransactions, function ($heavyEquipment) {
            return strtolower($heavyEquipment['item_name']) == 'crane';
        });

        // group into resource array
        $resources = [];
        $resourceForklift = [
            'resource' => 'Forklift',
            'plan' => 0,
            'unit' => 'Unit',
            'realization' => 0,
            'description' => 'Total ordered forklift deadline today + internal compare job usage',
            'transactions' => []
        ];
        if (!empty($forklifts)) {
            $resourceForklift['plan'] = array_sum(array_column($forklifts, 'plan'));
            $resourceForklift['realization'] = array_sum(array_column($forklifts, 'realization'));
            $resourceForklift['transactions'] = $forklifts;
        }
        $resources[] = $resourceForklift;

        $resourceCrane = [
            'resource' => 'Crane',
            'plan' => 0,
            'unit' => 'Unit',
            'realization' => 0,
            'description' => 'Total ordered crane deadline today + internal compare job usage',
            'transactions' => []
        ];
        if (!empty($cranes)) {
            $resourceCrane['plan'] = array_sum(array_column($cranes, 'plan'));
            $resourceCrane['realization'] = array_sum(array_column($cranes, 'realization'));
            $resourceCrane['transactions'] = $cranes;
        }
        $resources[] = $resourceCrane;

        return $resources;
    }

    /**
     * Get resource plan realization.
     *
     * @param array $filters
     * @return array
     */
    public function getResource($filters = [])
    {
        $labor = $this->getLabor($filters);
        $heavyEquipments = $this->getHeavyEquipments($filters);

        return array_merge([$labor], $heavyEquipments);
    }

    /**
     * Get resource plan realization.
     *
     * @param array $filters
     * @return array
     */
    private function getResourceWillBeDeletedSoon($filters = [])
    {
        $date = key_exists('date', $filters) ? ("'" . $filters['date'] . "'") : 'CURDATE()';
        $transaction = key_exists('transaction', $filters) ? $filters['transaction'] : false;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        //$this->load->database('vms', FALSE, TRUE);
        // get labor
        $baseQuery = $this->db
            ->select([
                'ref_branches.branch',
                'IFNULL(SUM(IF(guest.checkout IS NULL, 1, 0)), 0) AS labor_total',
                'IFNULL(SUM(IF(guest.checkin IS NOT NULL AND guest.checkout IS NULL, 1, 0)), 0) AS labor_realization_total',
            ])
            ->from(env('DB_VMS_DATABASE') . '.guest')
            ->join('ref_branches', 'ref_branches.id_branch_vms = guest.id_branch')
            ->where([
                'guest.laborers_type' => 'BURUH',
                'DATE(guest.janjian) = ' . $date => null
            ]);
        if (!empty($branchId)) {
            $baseQuery->where('ref_branches.id', $branchId);
        }
        $labor = $baseQuery->get()->row_array();

        // get detail transactions
        $laborTransactions = [];
        if ($transaction) {
            $baseQuery = $this->db
                ->select([
                    'guest.id',
                    'guest.uniqid AS no_reference',
                    'guest.nama_visitor AS visitor',
                ])
                ->from(env('DB_VMS_DATABASE') . '.guest')
                ->join('ref_branches', 'ref_branches.id_branch_vms = guest.id_branch')
                ->where([
                    'guest.laborers_type' => 'BURUH',
                    'DATE(guest.janjian) = ' . $date => null
                ]);
            if (!empty($branchId)) {
                $baseQuery->where('ref_branches.id', $branchId);
            }
            $laborTransactions = $baseQuery->get()->result_array();
        }

        // get heavy equipments
        $baseQuery = $this->db
            ->select([
                'requisitions.id',
                'requisitions.no_requisition',
                'requisitions.request_title',
                'ref_item_categories.item_name',
                'ref_employees.name',
                'ref_branches.branch',
                'requisition_items.quantity',
                'IFNULL(realizations.total, 0) AS total_used'
            ])
            ->from(env('DB_PURCHASE_DATABASE') . '.requisitions')
            ->join("(
                SELECT id_requisition, SUM(quantity) AS quantity
                FROM " . env('DB_PURCHASE_DATABASE') . '.requisition_items' . "
                GROUP BY id_requisition
            ) AS requisition_items", 'requisition_items.id_requisition = requisitions.id')
            ->join(env('DB_PURCHASE_DATABASE') . '.ref_item_categories', 'ref_item_categories.id = requisitions.id_item_category')
            ->join('(
                SELECT DISTINCT id_requisition, id_branch 
                FROM heavy_equipment_entry_permits
            ) AS hep_branches', 'hep_branches.id_requisition = requisitions.id')
            ->join(EmployeeModel::$tableEmployee, 'ref_employees.id = requisitions.id_employee', 'left')
            //->join(env('DB_PURCHASE_DATABASE') . '.purchase_offers', 'purchase_offers.id_requisition = requisitions.id', 'left')
            //->join(UserModel::$tableUser, 'prv_users.id = ref_employees.id_user', 'left')
            //->join('(SELECT DISTINCT id_user, id_branch FROM prv_user_roles) AS prv_user_roles', 'prv_user_roles.id_user = prv_users.id', 'left')
            ->join('ref_branches', 'ref_branches.id = hep_branches.id_branch', 'left')
            ->join("(
                SELECT id_requisition, SUM(IF(total_used_in_work_order > 0, 1, 0)) AS total
                FROM (
                    SELECT
                      id_requisition,
                      heavy_equipment_entry_permits.id AS id_heep,
                      COUNT(work_order_component_heeps.id) AS total_used_in_work_order
                    FROM heavy_equipment_entry_permits
                    LEFT JOIN work_order_component_heeps ON work_order_component_heeps.id_heep = heavy_equipment_entry_permits.id
                    GROUP BY id_requisition, heavy_equipment_entry_permits.id
                    HAVING total_used_in_work_order > 0
                ) AS heep GROUP BY id_requisition
            ) AS realizations", 'realizations.id_requisition = requisitions.id', 'left')
            ->where([
                //'purchase_offers.status!=' => "COMPLETED",
                //'requisitions.status!=' => "DONE",
                'DATE(requisitions.deadline) = ' . $date => null
            ])
            ->where_in('ref_item_categories.item_name', ['FORKLIFT', 'CRANE']);

        if (!empty($branchId)) {
            $baseQuery->where('ref_branches.id', $branchId);
        }

        $resourceQuery = $baseQuery->get_compiled_select();

        $heavyEquipmentTransactions = [];
        if ($transaction) {
            $heavyEquipmentTransactions = $this->db
                ->select([
                    'requisitions.id',
                    'requisitions.no_requisition AS no_reference',
                    'requisitions.item_name',
                    'requisitions.request_title',
                ])
                ->from("({$resourceQuery}) AS requisitions")
                ->get()
                ->result_array();
        }

        $baseQuery = $this->db
            ->select([
                'requisitions.item_name AS resource',
                '"Unit" AS unit',
                'requisitions.quantity AS plan',
                'requisitions.total_used AS realization'
            ])
            ->from("({$resourceQuery}) AS requisitions")
            ->group_by('item_name');

        $heaveEquipments = $baseQuery->get()->result_array();

        $resources = [
            [
                'resource' => 'Labor',
                'plan' => get_if_exist($labor, 'labor_total', 0),
                'unit' => 'People',
                'realization' => get_if_exist($labor, 'labor_realization_total', 0),
                'description' => 'VMS Labor appointment today that not checked out compared by checked in',
                'transactions' => $laborTransactions
            ]
        ];

        $forklift = array_filter($heaveEquipments, function ($heavyEquipment) {
            if (strtolower($heavyEquipment['resource']) == 'forklift') {
                return true;
            }
            return false;
        });
        if (!empty($forklift)) {
            $resources[] = [
                'resource' => 'Forklift',
                'plan' => end($forklift)['plan'],
                'unit' => 'Unit',
                'realization' => end($forklift)['realization'],
                'description' => 'Total ordered forklift deadline today compare job usage',
                'transactions' => array_filter($heavyEquipmentTransactions, function ($forklift) {
                    return strtolower($forklift['item_name']) == 'forklift';
                })
            ];
        } else {
            $resources[] = [
                'resource' => 'Forklift',
                'plan' => 0,
                'unit' => 'Unit',
                'realization' => 0,
                'description' => 'Total ordered forklift deadline today compare job usage',
                'transactions' => []
            ];
        }

        $crane = array_filter($heaveEquipments, function ($heavyEquipment) {
            if (strtolower($heavyEquipment['resource']) == 'crane') {
                return true;
            }
            return false;
        });
        if (!empty($crane)) {
            $resources[] = [
                'resource' => 'Crane',
                'plan' => end($crane)['plan'],
                'unit' => 'Unit',
                'realization' => end($crane)['realization'],
                'description' => 'Total ordered crane deadline today compare job usage',
                'transactions' => array_filter($heavyEquipmentTransactions, function ($forklift) {
                    return strtolower($forklift['item_name']) == 'crane';
                })
            ];
        } else {
            $resources[] = [
                'resource' => 'Crane',
                'plan' => 0,
                'unit' => 'Unit',
                'realization' => 0,
                'description' => 'Total ordered crane deadline today compare job usage',
                'transactions' => []
            ];
        }

        return $resources;
    }

    /**
     * Get realization inbounds.
     *
     * @param $filters
     * @return array
     */
    public function getInbound($filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        // TODO: remove DISTINCT to increase query performance
        $baseQuery = $this->db
            ->select([
                'ref_people.id AS id_customer',
                'ref_people.name AS customer_name',
                'bookings.id AS id_booking',
                'bookings.no_reference',
                'goods_lists.goods_name AS item_name',
                //'work_order_positions.position AS unloading_location',
                //'work_order_components.tools AS special_equipment',
                'sppb_documents.sppb_date',
                'DATE(last_inbounds.date) AS inbound_date',

                'IFNULL(booking_containers.party_20, 0) AS party_20',
                'IFNULL(booking_containers.party_40, 0) AS party_40',
                'IFNULL(booking_goods.party_lcl, 0) AS party_lcl',

                'IFNULL(booking_containers.realization_20, 0) AS realization_20',
                'IFNULL(booking_containers.realization_40, 0) AS realization_40',
                'IFNULL(booking_goods.realization_lcl, 0) AS realization_lcl',

                'IFNULL((booking_containers.party_20 - booking_containers.realization_20), 0) AS left_20',
                'IFNULL((booking_containers.party_40 - booking_containers.realization_40), 0) AS left_40',
                'IFNULL((booking_goods.party_lcl - booking_goods.realization_lcl), 0) AS left_lcl',

                '(IFNULL(booking_containers.realization_20, 0) + IFNULL(booking_containers.realization_40, 0) + IFNULL(booking_goods.realization_lcl, 0)) / NULLIF((IFNULL(booking_containers.realization_20, 0) + IFNULL(booking_containers.realization_40, 0) + IFNULL(booking_goods.realization_lcl, 0)), 0) * 100 AS achievement',
                '"" AS description'
            ])
            ->from('bookings')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type')
            ->join('ref_people', 'ref_people.id = bookings.id_customer')
            ->join('(
                SELECT
                    booking_containers.id_booking, 
                    SUM(IF(ref_containers.size = 20, 1, 0)) AS party_20,
                    SUM(IF(ref_containers.size = 40 OR ref_containers.size = 45, 1, 0)) AS party_40,
                    SUM(IF(ref_containers.size = 20 AND work_order_containers.id_container IS NOT NULL, 1, 0)) AS realization_20,
                    SUM(IF((ref_containers.size = 40 OR ref_containers.size = 45) AND work_order_containers.id_container IS NOT NULL, 1, 0)) AS realization_40
                FROM bookings
                INNER JOIN booking_containers ON booking_containers.id_booking = bookings.id
                INNER JOIN ref_containers ON ref_containers.id = booking_containers.id_container
                LEFT JOIN (
                    SELECT 
                        handlings.id_booking, 
                        work_order_containers.id_container 
                    FROM handlings
                    INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                    INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                    INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                    WHERE handling_type = "STRIPPING" 
                        AND work_orders.status = "COMPLETED"
                        AND work_orders.is_deleted = FALSE
                ) AS work_order_containers ON work_order_containers.id_booking = booking_containers.id_booking
                    AND work_order_containers.id_container = booking_containers.id_container
                WHERE bookings.status = "APPROVED" 
                    AND bookings.id_branch = "' . $branchId . '"
                GROUP BY id_booking
            ) AS booking_containers', 'booking_containers.id_booking = bookings.id', 'left')
            ->join('(
                SELECT
                    booking_goods.id_booking,
                    IF(booking_containers.total_container > 0, 0, SUM(booking_goods.quantity)) AS party_lcl,
                    IF(booking_containers.total_container > 0, 0, work_order_goods.quantity) AS realization_lcl
                FROM bookings
                INNER JOIN booking_goods ON booking_goods.id_booking = bookings.id
                LEFT JOIN (
                    SELECT id_booking, COUNT(id) AS total_container 
                    FROM booking_containers
                    GROUP BY id_booking
                ) AS booking_containers ON booking_containers.id_booking = booking_goods.id_booking
                LEFT JOIN (
                    SELECT
                        handlings.id_booking, 
                        IFNULL(SUM(work_order_goods.quantity), 0) AS quantity 
                    FROM handlings
                    INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                    INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                    INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                    WHERE handling_type = "UNLOAD" 
                        AND work_orders.status = "COMPLETED"
                        AND work_orders.is_deleted = FALSE
                    GROUP BY handlings.id_booking
                ) AS work_order_goods ON work_order_goods.id_booking = booking_goods.id_booking
                WHERE booking_goods.id_booking_container IS NULL 
                    AND bookings.status = "APPROVED"
                    AND bookings.id_branch = "' . $branchId . '"
                GROUP BY id_booking, work_order_goods.quantity
            ) AS booking_goods', 'booking_goods.id_booking = bookings.id', 'left')
            ->join('(
                SELECT id_booking, GROUP_CONCAT(DISTINCT ref_goods.name) AS goods_name
                FROM booking_goods
                INNER JOIN ref_goods ON ref_goods.id = booking_goods.id_goods
                GROUP BY id_booking
            ) AS goods_lists', 'goods_lists.id_booking = bookings.id', 'left')
            ->join("(
                SELECT 
                    upload_documents.id_upload,
                    MAX(upload_documents.document_date) AS sppb_date
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'SPPB'
                GROUP BY id_upload             
            ) AS sppb_documents", 'sppb_documents.id_upload = bookings.id_upload', 'left')
            ->join('(
                SELECT id_booking, MAX(date) AS date FROM (
                    SELECT id_booking, security_in_date AS date
                    FROM safe_conducts
                    UNION ALL
                    SELECT id_booking, security_out_date AS date
                    FROM safe_conducts
                ) AS safe_conducts
                GROUP BY id_booking
            ) AS last_inbounds', 'last_inbounds.id_booking = bookings.id', 'left')
            /*->join('(
                SELECT
                    handlings.id_booking,
                    GROUP_CONCAT(DISTINCT CONCAT(handling_component, " ", CAST(quantity AS UNSIGNED), IFNULL(ref_units.unit, ""))) AS tools
                FROM handlings
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_components ON work_order_components.id_work_order = work_orders.id
                LEFT JOIN ref_units ON ref_units.id = work_order_components.id_unit
                INNER JOIN ref_components ON ref_components.id = work_order_components.id_component
                GROUP BY id_booking
            ) AS work_order_components', 'work_order_components.id_booking = bookings.id', 'left')
            ->join('(
                SELECT
                    handlings.id_booking,
                    GROUP_CONCAT(DISTINCT ref_positions.position) AS position
                FROM handlings
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                INNER JOIN ref_positions ON ref_positions.id = work_order_containers.id_position
                    OR ref_positions.id = work_order_goods.id_position
                GROUP BY id_booking
            ) AS work_order_positions', 'work_order_positions.id_booking = bookings.id', 'left')*/
            ->where([
                'ref_booking_types.category' => 'INBOUND',
                'bookings.is_deleted' => false,
                'bookings.booking_date>=' => '2021-01-01',
            ])
            ->group_by('bookings.id, last_inbounds.date')
            ->order_by('bookings.id', 'desc');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if (key_exists('bookings', $filters) && !empty($filters['bookings'])) {
            $baseQuery->where_in('bookings.id', $filters['bookings']);
        }

        if (key_exists('statuses', $filters) && !empty($filters['statuses'])) {
            $baseQuery->where_in('bookings.status', $filters['statuses']);
        } else {
            $baseQuery->where('bookings.status', 'APPROVED');
        }

        return $baseQuery->get()->result_array();
    }

    public function getOutbound($filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        if (key_exists('plan_date', $filters) && !empty($filters['plan_date'])) {
            $planDate = "'" . format_date($filters['plan_date']) . "'";
        } else {
            $planDate = 'CURDATE()';
        }

        $baseQuery = $this->db
            ->select([
                'ref_people.id AS id_customer',
                'ref_people.name AS customer_name',
                'bookings.id AS id_booking',
                'bookings.no_reference',
                'booking_inbounds.no_reference AS no_reference_inbound',
                'goods_lists.goods_name AS item_name',
                "IF(goods_lists.goods_name IS NULL, 'Yard', 'Warehouse') AS loading_location",
                //'work_order_components.tools AS special_equipment',
                'sppb_documents.sppb_date',
                'first_handlings.date AS instruction_date',

                'IFNULL(booking_containers.party_20, 0) AS party_20',
                'IFNULL(booking_containers.party_40, 0) AS party_40',
                'IFNULL(booking_goods.party_lcl, 0) AS party_lcl',

                'IFNULL(booking_containers.realization_20, 0) AS realization_20',
                'IFNULL(booking_containers.realization_40, 0) AS realization_40',
                'IFNULL(booking_goods.realization_lcl, 0) AS realization_lcl',

                'IFNULL((booking_containers.party_20 - booking_containers.realization_20), 0) AS left_20',
                'IFNULL((booking_containers.party_40 - booking_containers.realization_40), 0) AS left_40',
                'IFNULL((booking_goods.party_lcl - booking_goods.realization_lcl), 0) AS left_lcl',

                '(IFNULL(booking_containers.realization_20, 0) + IFNULL(booking_containers.realization_40, 0) + IFNULL(booking_goods.realization_lcl, 0)) / NULLIF((IFNULL(booking_containers.realization_20, 0) + IFNULL(booking_containers.realization_40, 0) + IFNULL(booking_goods.realization_lcl, 0)), 0) * 100 AS achievement',
                '"" AS description'
            ])
            ->from('bookings')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = bookings.id_booking')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type')
            ->join('ref_people', 'ref_people.id = bookings.id_customer')
            ->join('(
                SELECT
                    handlings.id_booking, 
                    SUM(IF(ref_containers.size = 20, 1, 0)) AS party_20,
                    SUM(IF(ref_containers.size = 40 OR ref_containers.size = 45, 1, 0)) AS party_40,
                    SUM(IF(ref_containers.size = 20 AND work_order_containers.id_container IS NOT NULL, 1, 0)) AS realization_20,
                    SUM(IF((ref_containers.size = 40 OR ref_containers.size = 45) AND work_order_containers.id_container IS NOT NULL, 1, 0)) AS realization_40
                FROM handlings
                INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                INNER JOIN handling_containers ON handling_containers.id_handling = handlings.id
                INNER JOIN ref_containers ON ref_containers.id = handling_containers.id_container
                LEFT JOIN (
                    SELECT 
                        handlings.id_booking, 
                        work_order_containers.id_container 
                    FROM handlings
                    INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                    INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                    INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                    WHERE handling_type = "LOAD"
                        AND work_orders.is_deleted = FALSE
                        AND work_orders.status = "COMPLETED"
                        AND DATE(handlings.handling_date) = ' . $planDate . '
                ) AS work_order_containers ON work_order_containers.id_booking = handlings.id_booking
                    AND work_order_containers.id_container = handling_containers.id_container
                WHERE ref_handling_types.handling_type = "LOAD"
                    AND DATE(handlings.handling_date) = ' . $planDate . '
                GROUP BY id_booking
            ) AS booking_containers', 'booking_containers.id_booking = bookings.id', 'left')
            ->join('(
                SELECT
                    handlings.id_booking,
                    SUM(handling_goods.quantity) AS party_lcl,
                    work_order_goods.quantity AS realization_lcl
                FROM handlings
                INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                INNER JOIN handling_goods ON handling_goods.id_handling = handlings.id
                LEFT JOIN (
                    SELECT
                        handlings.id_booking, 
                        IFNULL(SUM(work_order_goods.quantity), 0) AS quantity
                    FROM handlings
                    INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                    INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                    INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                    WHERE handling_type = "LOAD"
                        AND work_orders.is_deleted = FALSE
                        AND work_orders.status = "COMPLETED"
                        AND DATE(handlings.handling_date) = ' . $planDate . '
                    GROUP BY handlings.id_booking
                ) AS work_order_goods ON work_order_goods.id_booking = handlings.id_booking
                WHERE handling_goods.id_handling_container IS NULL
                    AND ref_handling_types.handling_type = "LOAD"
                    AND DATE(handlings.handling_date) = ' . $planDate . '
                GROUP BY id_booking, work_order_goods.quantity
            ) AS booking_goods', 'booking_goods.id_booking = bookings.id', 'left')
            ->join('(
                SELECT id_booking, GROUP_CONCAT(DISTINCT ref_goods.name) AS goods_name
                FROM booking_goods
                INNER JOIN ref_goods ON ref_goods.id = booking_goods.id_goods
                GROUP BY id_booking
            ) AS goods_lists', 'goods_lists.id_booking = bookings.id', 'left')
            ->join("(
                SELECT 
                    upload_documents.id_upload,
                    MAX(upload_documents.document_date) AS sppb_date
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'SPPB' AND upload_documents.is_deleted = FALSE
                GROUP BY id_upload             
            ) AS sppb_documents", 'sppb_documents.id_upload = bookings.id_upload', 'left')
            ->join('(
                SELECT id_booking, MIN(handlings.handling_date) AS date 
                FROM handlings
                INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                WHERE handling_type IN("LOAD")
                    AND handlings.is_deleted = FALSE 
                    AND handlings.status != "REJECTED"
                GROUP BY id_booking
            ) AS first_handlings', 'first_handlings.id_booking = bookings.id', 'left')
            /*->join('(
                SELECT
                    handlings.id_booking,
                    GROUP_CONCAT(DISTINCT CONCAT(handling_component, " ", CAST(quantity AS UNSIGNED), IFNULL(ref_units.unit, ""))) AS tools
                FROM handlings 
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_components ON work_order_components.id_work_order = work_orders.id
                LEFT JOIN ref_units ON ref_units.id = work_order_components.id_unit 
                INNER JOIN ref_components ON ref_components.id = work_order_components.id_component
                GROUP BY id_booking
            ) AS work_order_components', 'work_order_components.id_booking = bookings.id', 'left')*/
            ->where([
                'ref_booking_types.category' => 'OUTBOUND',
                'bookings.is_deleted' => false,
                'bookings.created_at >=' => '2021-01-01',
            ])
            ->group_by('bookings.id, first_handlings.date')
            ->order_by('bookings.id', 'desc');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if (key_exists('bookings', $filters) && !empty($filters['bookings'])) {
            $baseQuery->where_in('bookings.id', $filters['bookings']);
        }

        if (key_exists('statuses', $filters) && !empty($filters['statuses'])) {
            $baseQuery->where_in('bookings.status', $filters['statuses']);
        } else {
            $baseQuery->where('bookings.status', 'APPROVED');
        }

        return $baseQuery->get()->result_array();
    }
}
