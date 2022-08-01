<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SafeConductModel extends CI_Model
{
    private $table = 'safe_conducts';

    const TYPE_CODE_IN = 'SI';
    const TYPE_CODE_OUT = 'SO';
    const TYPE_CODE_GROUP = 'SG';

    const TYPE_INBOUND = 'INBOUND';
    const TYPE_OUTBOUND = 'OUTBOUND';

    const TRACKING_NO_ESEAL = 'NO ESEAL';
    const TRACKING_EMPTY_ROUTE = 'EMPTY ROUTES';
    const TRACKING_SUSPECTED = 'SUSPECTED';
    const TRACKING_START_ONLY = 'NORMAL START ONLY';
    const TRACKING_STOP_ONLY = 'NORMAL STOP ONLY';
    const TRACKING_NORMAL = 'NORMAL';

    /**
     * SafeConductModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get auto number for safe conduct.
     * @param $type
     * @return string
     */
    public function getAutoNumberSafeConduct($type)
    {
        $orderData = $this->db->query("
            SELECT CAST(RIGHT(no_safe_conduct, 6) AS UNSIGNED) + 1 AS order_number 
            FROM safe_conducts 
            WHERE MONTH(created_at) = MONTH(NOW()) 
              AND YEAR(created_at) = YEAR(NOW())
            ORDER BY SUBSTR(no_safe_conduct FROM 4) DESC LIMIT 1
        ");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return $type . '/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }

    /**
     * Get base query safe conduct.
     * @param null $branchId
     * @param null $userType
     * @param null $customerId
     * @return CI_DB_query_builder
     */
    public function getSafeConductBaseQuery($branchId = null, $userType = null, $customerId = null)
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

        $safeConducts = $this->db->select([
            'safe_conducts.*',
            '(SELECT upload_documents.no_document FROM upload_documents 
              LEFT JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
              WHERE upload_documents.id_upload = IFNULL(booking_in.id_upload, bookings.id_upload)
              AND ref_document_types.document_type = "Invoice"
              LIMIT 1) AS no_invoice',
            'TIMEDIFF(security_out_date, security_in_date) AS service_time',
            'ref_people.name AS source_warehouse',
            'ref_eseals.no_eseal',
            'ref_eseals.device_name',
            'bookings.no_booking',
            'bookings.no_reference',
            'bookings.id_customer',
            'customers.name AS customer_name',
            'ref_branches.id AS id_branch',
            'ref_branches.branch',
            'ref_branches.branch_type',
            'handlings.no_handling',
            'ref_handling_types.handling_type',
            'ref_handling_types.handling_code',
            'MAX(work_orders.id) AS id_work_order',
            'GROUP_CONCAT(DISTINCT sf_ref_containers.no_container SEPARATOR ", ") AS containers_load',
            'GROUP_CONCAT(DISTINCT sf_ref_goods.name SEPARATOR ", ") AS goods_load',
            'transporter_entry_permits.tep_code',
            'transporter_entry_permits.checked_in_at AS tep_in_date',
            'transporter_entry_permits.checked_out_at AS tep_out_date',
            'prv_users.name AS creator_name',
            'SUM(IF(safe_conduct_checklists.type = "CHECK IN" AND safe_conduct_checklists.id_container IS NULL, 1, 0)) as total_check_in',
            'sum(IF(safe_conduct_checklists.type = "CHECK OUT" AND safe_conduct_checklists.id_container IS NULL, 1, 0)) as total_check_out',
            'safe_conduct_groups.no_safe_conduct_group'

        ])->from('safe_conducts')
            ->join('bookings', 'safe_conducts.id_booking = bookings.id', 'left')
            ->join('bookings AS booking_in', 'booking_in.id = bookings.id_booking', 'left')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->join('handlings', 'safe_conducts.id_handling = handlings.id', 'left')
            ->join('ref_handling_types', 'handlings.id_handling_type = ref_handling_types.id', 'left')
            ->join('work_orders', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('ref_eseals', 'ref_eseals.id = safe_conducts.id_eseal', 'left')
            ->join('ref_people', 'ref_people.id = safe_conducts.id_source_warehouse', 'left')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('safe_conduct_containers AS sf_containers', 'safe_conducts.id = sf_containers.id_safe_conduct', 'left')
            ->join('ref_containers AS sf_ref_containers', 'sf_ref_containers.id = sf_containers.id_container', 'left')
            ->join('safe_conduct_goods AS sf_goods', 'safe_conducts.id = sf_goods.id_safe_conduct', 'left')
            ->join('ref_goods AS sf_ref_goods', 'sf_ref_goods.id = sf_goods.id_goods', 'left')
            ->join('transporter_entry_permits', 'transporter_entry_permits.id = safe_conducts.id_transporter_entry_permit', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = safe_conducts.created_by', 'left')
            ->join('safe_conduct_checklists', 'safe_conducts.id = safe_conduct_checklists.id_safe_conduct', 'left')
            ->join('safe_conduct_groups', 'safe_conduct_groups.id = safe_conducts.id_safe_conduct_group', 'left')
            ->group_by('safe_conducts.id');

        if (!empty($branchId)) {
            $safeConducts->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $safeConducts->where('bookings.id_customer', $customerId);
        }

        return $safeConducts;
    }

    /**
     * Optimized version of base query, select only what index page needs.
     *
     * @param null $branchId
     * @param null $userType
     * @param null $customerId
     * @return CI_DB_mysql_driver|CI_DB_query_builder
     */
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
                'safe_conducts.id',
                'safe_conducts.no_safe_conduct',
                'safe_conduct_groups.no_safe_conduct_group',
                'safe_conducts.type',
                'safe_conducts.expedition_type',
                'safe_conducts.no_police',
                'safe_conducts.driver',
                'transporter_entry_permits.tep_code',
                'safe_conducts.id_transporter_entry_permit',
                'safe_conducts.security_in_date',
                'safe_conducts.security_out_date',
                'bookings.no_booking',
                'bookings.no_reference',
                '(
                    SELECT GROUP_CONCAT(no_container SEPARATOR ", ") AS no_container 
                    FROM safe_conduct_containers
                    INNER JOIN ref_containers ON ref_containers.id = safe_conduct_containers.id_container
                    WHERE safe_conduct_containers.id_safe_conduct = safe_conducts.id
                ) AS containers_load',
                '(
                    SELECT GROUP_CONCAT(ref_goods.name SEPARATOR ", ") AS no_container 
                    FROM safe_conduct_goods
                    INNER JOIN ref_goods ON ref_goods.id = safe_conduct_goods.id_goods
                    WHERE safe_conduct_goods.id_safe_conduct = safe_conducts.id
                ) AS goods_load',
                'safe_conducts.print_total',
                'safe_conducts.print_max',
                '(
                    SELECT MAX(id) AS id FROM work_orders
                    WHERE id_safe_conduct = safe_conducts.id
                ) AS id_work_order',
                'safe_conducts.created_at',
            ])
            ->from($this->table)
            ->join('bookings', 'safe_conducts.id_booking = bookings.id')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('transporter_entry_permits', 'transporter_entry_permits.id = safe_conducts.id_transporter_entry_permit', 'left')
            ->join('safe_conduct_groups', 'safe_conduct_groups.id = safe_conducts.id_safe_conduct_group', 'left');

        if ($userType == 'EXTERNAL') {
            $baseQuery->where('bookings.id_customer', $customerId);
        }

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        return $baseQuery;
    }

    /**
     * Get vehicle.
     * @return mixed
     */
    public function getVehicles($police, $date1, $date2, $branch = null)
    {

        $vehicles =  $this->db->select([
            'safe_conducts.*',
            'IFNULL(safe_conducts.id_safe_conduct_group, safe_conducts.id) as unq_safe_conduct_group',
            ])->from('safe_conducts')
            ->join('safe_conduct_groups', 'safe_conduct_groups.id = safe_conducts.id_safe_conduct_group', 'left')
            ->join('bookings', 'bookings.id = safe_conducts.id_booking', 'left')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->join('work_orders', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('handlings', 'work_orders.id_handling = handlings.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->group_start()
                ->where_in('ref_handling_types.multiplier_goods', ['1','-1'])
                ->or_where_in('ref_handling_types.multiplier_container', ['1','-1'])
            ->group_end()
            ->where('expedition_type', "INTERNAL")
            ->where('no_police', $police)
            ->where('DATE(safe_conducts.created_at) >=', $date1)
            ->where('DATE(safe_conducts.created_at) <=', $date2)
            ->where('safe_conducts.is_deleted', false)
            ->group_by('unq_safe_conduct_group');

        if(!empty($branch)){
            $vehicles->where('ref_branches.id', $branch);
        }

        return $vehicles->get()->result_array();
    }

    /**
     * Get vehicle detail.
     * @return mixed
     */
    public function getDetailVehicles($police, $date1, $date2, $branch = null)
    {

        $vehicles =  $this->db->select([
            'safe_conducts.*',
            'IFNULL(safe_conducts.id_safe_conduct_group, safe_conducts.id) as unq_safe_conduct_group',
            'ref_branches.id AS id_branch',
            'ref_branches.branch',
            'bookings.id AS id_booking',
            'bookings.id_customer',
            'bookings.no_reference',
            'customers.name AS customer_name',
            'work_orders.id AS id_work_order',
            'work_orders.no_work_order'
            ])->from('safe_conducts')
            ->join('bookings', 'bookings.id = safe_conducts.id_booking', 'left')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('work_orders', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('handlings', 'work_orders.id_handling = handlings.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->join('ref_vehicles', 'ref_vehicles.no_plate = safe_conducts.no_police', 'left')
            ->join('safe_conduct_groups', 'safe_conduct_groups.id = safe_conducts.id_safe_conduct_group', 'left')
             ->group_start()
                ->where_in('ref_handling_types.multiplier_goods', ['1','-1'])
                ->or_where_in('ref_handling_types.multiplier_container', ['1','-1'])
            ->group_end()
            ->where('expedition_type', "INTERNAL")
            ->where('no_police', $police)
            ->where('DATE(safe_conducts.created_at) >=', $date1)
            ->where('DATE(safe_conducts.created_at) <=', $date2)
            ->where('safe_conducts.is_deleted', false)
            ->group_by('safe_conducts.id');

        if(!empty($branch)){
            $vehicles->where('ref_branches.id', $branch);
        }

        return $vehicles->get()->result_array();
    }

    /**
     * Get all safe conducts.
     * @param null $type
     * @param int $start
     * @param int $length
     * @param string $search
     * @param int $column
     * @param string $sort
     * @param bool $withTrashed
     * @return array
     */
    public function getAllSafeConducts($type = null, $start = -1, $length = 10, $search = '', $column = 0, $sort = 'DESC', $withTrashed = false)
    {
        if ($start < 0) {
            $safeConducts = $this->getSafeConductBaseQuery();
            if (!empty($type)) {
                $safeConducts->where('safe_conducts.type', $type);
            }
            if (!$withTrashed) {
                $safeConducts->where('safe_conducts.is_deleted', false);
            }
            return $safeConducts->get()->result_array();
        }

        // alias column name by index for sorting data table library
        $columnOrder = [
            0 => "safe_conducts.id",
            1 => "safe_conducts.no_safe_conduct",
            2 => "safe_conducts.type",
            3 => "safe_conducts.no_police",
            4 => "safe_conducts.driver",
            5 => "safe_conducts.expedition",
            6 => "safe_conducts.id",
            7 => "safe_conducts.security_in_date",
            8 => "safe_conducts.security_out_date",
            9 => "safe_conducts.id",
        ];
        $columnSort = $columnOrder[$column];

        $branchId = get_active_branch('id');
        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $this->db->start_cache();

        $safeConducts = $this->getBaseQueryIndex($branchId, $userType, $customerId);

        if (!empty($search)) {
            $safeConducts->group_start();
            $safeConducts
                ->like('safe_conducts.no_safe_conduct', $search)
                ->or_like('safe_conduct_groups.no_safe_conduct_group', $search)
                ->or_like('bookings.no_booking', $search)
                ->or_like('safe_conducts.type', $search)
                ->or_like('safe_conducts.no_police', $search)
                ->or_like('safe_conducts.driver', $search)
                ->or_like('safe_conducts.expedition', $search)
                ->or_like('safe_conducts.expedition_type', $search)
                ->or_like('safe_conducts.security_in_date', $search)
                ->or_like('safe_conducts.security_out_date', $search);
            $safeConducts->or_where("EXISTS (
                SELECT safe_conduct_containers.id FROM safe_conduct_containers
                INNER JOIN ref_containers ON ref_containers.id = safe_conduct_containers.id_container
                WHERE ref_containers.no_container LIKE '%" . trim($search) . "%'
                    AND safe_conduct_containers.id_safe_conduct = safe_conducts.id
            )");
            $safeConducts->group_end();
        }

        if (!empty($type)) {
            $safeConducts->where('safe_conducts.type', $type);
        }
        if (!$withTrashed) {
            $safeConducts->where('safe_conducts.is_deleted', false);
        }
        $this->db->stop_cache();

        // counting result is slow, use simple pagination or cache to share result set like below
        $distinctQueryParams = filter_data_by_keys($_GET, ['type', 'search']);
        $cacheIdxKey = 'sf-idx-count-' . $branchId . '-' . md5(json_encode($distinctQueryParams));
        $safeConductsTotal = cache_remember($cacheIdxKey, 300, function() use ($safeConducts) {
            return $safeConducts->count_all_results();
        });

        //$safeConductsTotal = $this->db->count_all_results();
        $safeConductPage = $safeConducts->order_by($columnSort, $sort)->limit($length, $start);
        $safeConductData = $safeConductPage->get()->result_array();

        $this->db->flush_cache();

        return [
            "total" => count($safeConductData),
            "result" => $safeConductsTotal,
            "data" => $safeConductData,
        ];
    }

    /**
     * Get safe conduct simple data.
     *
     * @param null $filters
     * @param bool $withTrashed
     * @return array
     */
    public function getSafeConductData($filters = null, $withTrashed = false)
    {
        $branchId = get_active_branch('id');
        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $safeConducts = $this->db->select([
            'safe_conducts.id',
            'safe_conducts.no_safe_conduct',
            'safe_conducts.vehicle_type',
            'safe_conducts.no_police',
            'safe_conducts.type',
            'safe_conducts.driver',
            'safe_conducts.expedition',
            'warehouse_origins.name AS warehouse_origin',
            'safe_conducts.description',
            'safe_conducts.created_at',
            'safe_conducts.security_in_date AS security_start',
            'safe_conducts.security_out_date AS security_end',
            '(SELECT upload_documents.no_document FROM upload_documents 
              LEFT JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
              WHERE upload_documents.id_upload = IFNULL(booking_in.id_upload, bookings.id_upload)
              AND ref_document_types.document_type = "Invoice"
              LIMIT 1) AS no_invoice',
            'IFNULL(booking_in.no_reference, bookings.no_reference) AS no_bc_in',
            'IF(booking_in.no_reference IS NOT NULL, bookings.no_reference, "") AS no_bc_out',
            'customers.no_person AS no_customer',
            'customers.name AS customer_name',
        ])->from('safe_conducts')
            ->join('bookings', 'safe_conducts.id_booking = bookings.id', 'left')
            ->join('bookings AS booking_in', 'booking_in.id = bookings.id_booking', 'left')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('ref_people AS warehouse_origins', 'warehouse_origins.id = safe_conducts.id_source_warehouse', 'left');

        if (!empty($branchId)) {
            $safeConducts->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $safeConducts->where('bookings.id_customer', $customerId);
        }

        if (!empty($filters['type'])) {
            $safeConducts->where('safe_conducts.type', $filters['type']);
        }

        if (!empty($filters['min_id'])) {
            $safeConducts->where('safe_conducts.id>=', $filters['min_id']);
        }

        if (!empty($filters['max_id'])) {
            $safeConducts->where('safe_conducts.id<=', $filters['max_id']);
        }

        if (!empty($filters['manifest_number'])) {
            $safeConducts->where('safe_conducts.no_safe_conduct', $filters['manifest_number']);
        }

        if (!empty($filters['min_date'])) {
            $safeConducts->where('DATE(safe_conducts.created_at)>=', $filters['min_date']);
        }

        if (!$withTrashed) {
            $safeConducts->where('safe_conducts.is_deleted', false);
        }

        return $safeConducts->get()->result_array();
    }

    /**
     * Get single safe conduct data.
     * @param $id
     * @param bool $withTrash
     * @return array
     */
    public function getSafeConductById($id, $withTrash = false)
    {
        $safeConduct = $this->getSafeConductBaseQuery()->where('safe_conducts.id', $id);

        if (!$withTrash) {
            $safeConduct->where('safe_conducts.is_deleted', false);
        }

        return $safeConduct->get()->row_array();
    }

    /**
     * Get specific safe conduct by unique no.
     * @param $noSafeConduct
     * @param bool $withTrash
     * @return array
     */
    public function getSafeConductByNo($noSafeConduct, $withTrash = false)
    {
        $safeConduct = $this->getSafeConductBaseQuery()->where('safe_conducts.no_safe_conduct', $noSafeConduct);

        if (!$withTrash) {
            $safeConduct->where('safe_conducts.is_deleted', false);
        }

        return $safeConduct->get()->row_array();
    }

    /**
     * Get safe conducts by booking.
     * @param $bookingId
     * @param bool $withTrash
     * @return array
     */
    public function getSafeConductsByBooking($bookingId, $withTrash = false)
    {
        $safeConducts = $this->getSafeConductBaseQuery()->where('safe_conducts.id_booking', $bookingId);

        if (!$withTrash) {
            $safeConducts->where('safe_conducts.is_deleted', false);
        }

        return $safeConducts->get()->result_array();
    }

     /**
     * Get safe conducts by tep Id.
     * @param $tepId
     * @param bool $withTrash
     * @return array
     */
    public function getSafeConductsByTepId($tepId, $withTrash = false)
    {
        $safeConducts = $this->getSafeConductBaseQuery()->where('safe_conducts.id_transporter_entry_permit', $tepId);

        if (!$withTrash) {
            $safeConducts->where('safe_conducts.is_deleted', false);
        }

        return $safeConducts->get()->result_array();
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
        $baseQuery = $this->getSafeConductBaseQuery();

        foreach ($conditions as $key => $condition) {
            if(is_array($condition)) {
                if(!empty($condition)) {
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
     * Get inbound in progress.
     *
     * @param array $filters
     * @return array
     */
    public function getInboundProgress($filters = [])
    {
        $baseQuery = $this->db->select([
            'ref_branches.branch',
            'bookings.no_reference',
            'ref_people.name AS customer_name',
            'ref_containers.no_container',
            'safe_conducts.security_out_date AS security_inbound_date',
            'stripping_work_orders.completed_at AS completed_stripping_date',
            'load_work_orders.completed_at AS completed_load_date'
        ])
        ->from('safe_conducts')
        ->join('bookings', 'bookings.id = safe_conducts.id_booking')
        ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type')
        ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
        ->join('ref_people', 'ref_people.id = bookings.id_customer')
        ->join('safe_conduct_containers', 'safe_conduct_containers.id_safe_conduct = safe_conducts.id')
        ->join('ref_containers', 'ref_containers.id = safe_conduct_containers.id_container')
        ->join('(
            SELECT DISTINCT 
                handlings.id_booking, handling_containers.id_container, 
                work_orders.taken_at, work_orders.completed_at
            FROM handlings
            INNER JOIN handling_containers ON handling_containers.id_handling = handlings.id
            INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
            INNER JOIN work_orders ON work_orders.id_handling = handlings.id
            WHERE ref_handling_types.handling_type = "STRIPPING"
        ) AS stripping_work_orders', 'stripping_work_orders.id_booking = bookings.id AND stripping_work_orders.id_container = safe_conduct_containers.id_container', 'left')
        ->join('(
            SELECT DISTINCT bookings.id_booking, work_order_containers.id_container, work_orders.completed_at
            FROM bookings
            INNER JOIN handlings ON handlings.id_booking = bookings.id
            INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
            INNER JOIN work_orders ON work_orders.id_handling = handlings.id
            INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
            WHERE ref_handling_types.handling_type = "LOAD" AND work_orders.completed_at IS NOT NULL
        ) AS load_work_orders', 'load_work_orders.id_booking = bookings.id AND load_work_orders.id_container = safe_conduct_containers.id_container', 'left')
        ->where('ref_booking_types.category', 'INBOUND')
        ->where('safe_conducts.is_deleted', false)
        ->where('safe_conducts.security_out_date IS NOT NULL')
        ->where('safe_conducts.created_at>=', '2019-08-01')
        ->where('load_work_orders.completed_at IS NULL');

        if (!empty($filters)) {

            if (key_exists('stripping_completed_at', $filters)) {
                if(is_null($filters['stripping_completed_at'])) {
                    $baseQuery->where('stripping_work_orders.completed_at IS NULL');
                } else {
                    $baseQuery->where('stripping_work_orders.completed_at', $filters['stripping_date']);
                }
            }

            if (key_exists('stripping_taken_at', $filters)) {
                if(is_null($filters['stripping_taken_at'])) {
                    $baseQuery->where('stripping_work_orders.taken_at IS NULL');
                } else if($filters['stripping_taken_at'] == 'in progress') {
                    $baseQuery->where('stripping_work_orders.taken_at IS NOT NULL');
                } else {
                    $baseQuery->where('stripping_work_orders.taken_at', $filters['taken_date']);
                }
            }

            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
                if (key_exists('date', $filters)) {
                    if (is_null($filters['date'])) {
                        $baseQuery->where('DATE(' . $filters['date_type'] . ') IS NULL');
                    } else {
                        $baseQuery->where('DATE(' . $filters['date_type'] . ')', $filters['date']);
                    }
                }

                if (key_exists('date', $filters)) {
                    if (is_null($filters['date'])) {
                        $baseQuery->where('DATE(' . $filters['date_type'] . ') IS NULL');
                    } else {
                        $baseQuery->where('DATE(' . $filters['date_type'] . ')', $filters['date']);
                    }
                }
                if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                    $baseQuery->where('DATE(' . $filters['date_type'] . ')>=', format_date($filters['date_from']));
                }

                if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                    $baseQuery->where('DATE(' . $filters['date_type'] . ')<=', format_date($filters['date_to']));
                }
            }
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Create new safe conduct.
     * @param $data
     * @return bool
     */
    public function createSafeConduct($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update safe conduct.
     * @param $data
     * @param $id
     * @return bool
     */
    public function updateSafeConduct($data, $id)
    {
        $condition = ['id' => $id];
        if (is_array($id)) {
            $condition = $id;
        }
        return $this->db->update($this->table, $data, $condition);
    }

    /**
     * Delete delivery data.
     * @param integer $id
     * @param bool $softDelete
     * @return bool
     */
    public function deleteSafeConduct($id, $softDelete = true)
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
     * Get safe conduct data by keyword.
     *
     * @param array $filters
     * @param $search
     * @param null $page
     * @param bool $withTrashed
     * @return mixed
     */
    public function getByKeyword($filters = [], $search = '', $page = null, $withTrashed = false)
    {
        $branchId = get_active_branch('id');

        $this->db->start_cache();

        $baseQuery = $this->db->select([
            'safe_conducts.id',
            'safe_conducts.no_safe_conduct',
            'safe_conducts.type',
            'safe_conducts.expedition_type',
            'ref_people.name AS customer_name',
            'safe_conduct_groups.no_safe_conduct_group',
            'bookings.no_reference'
        ])
            ->from($this->table)
            ->join('bookings', 'bookings.id = safe_conducts.id_booking', 'left')
            ->join('ref_people', 'ref_people.id = bookings.id_customer', 'left')
            ->join('safe_conduct_groups', 'safe_conduct_groups.id = safe_conducts.id_safe_conduct_group', 'left')
            ->order_by('safe_conducts.id', 'desc');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if (key_exists('except', $filters) && !empty($filters['except'])) {
            $baseQuery->where('safe_conducts.id !=', $filters['except']);
        }

        if (key_exists('check_in', $filters)) {
            if ($filters['check_in']) {
                $baseQuery->where('safe_conducts.security_in_date IS NOT NULL', null);
            } else {
                $baseQuery->where('safe_conducts.security_in_date IS NULL', null);
            }
        }

        if (key_exists('check_out', $filters)) {
            if ($filters['check_out']) {
                $baseQuery->where('safe_conducts.security_out_date IS NOT NULL', null);
            } else {
                $baseQuery->where('safe_conducts.security_out_date IS NULL', null);
            }
        }

        if (key_exists('type', $filters) && !empty($filters['type'])) {
            $baseQuery->where('safe_conducts.type', $filters['type']);
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->where('bookings.id_customer', $filters['customer']);
        }

        $baseQuery->group_start();
        if (is_array($search)) {
            $baseQuery->where_in('no_safe_conduct', $search);
        } else {
            $baseQuery->like('no_safe_conduct', trim($search));
        }
        $baseQuery->group_end();

        if (!$withTrashed) {
            $baseQuery->where($this->table . '.is_deleted', false);
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

        $data = $baseQuery->get()->result_array();

        $this->db->flush_cache();

        return $data;
    }
}
