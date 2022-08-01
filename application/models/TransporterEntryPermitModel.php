<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransporterEntryPermitModel extends MY_Model
{
    protected $table = 'transporter_entry_permits';

    const TEP_CODE = 'TP';

    /**
     * Get active record query builder for all related warehouse data selection.
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        if(empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $baseQuery = parent::getBaseQuery()
            ->select([
                'ref_branches.branch',
                // 'ref_people.id AS id_customer_booking', // id cust for tep inbound (OLD DATA)                
                // 'ref_people.name AS customer_name', // cust name for tep inbound (OLD DATA)
                // 'bookings.no_booking', // no booking for tep inbound (OLD DATA)
                // 'bookings.no_reference', // no aju for tep inbound (OLD DATA)
                // 'ref_booking_types.booking_type', //booking type for tep inbound (OLD DATA)
                // 'ref_booking_types.category', //booking category for tep inbound (OLD DATA)
                'GROUP_CONCAT(DISTINCT people_tep.id) AS id_customer_in', //id cust for tep inbound (NEW DATA)
                'GROUP_CONCAT(DISTINCT people_tep.name) AS customer_name_in', //cust name for tep inbound (NEW DATA)
                'GROUP_CONCAT(DISTINCT booking_tep.no_booking) AS no_booking_tep_in', //no booking for tep inbound (NEW DATA)
                'GROUP_CONCAT(DISTINCT booking_tep.no_reference) AS no_reference_tep_in', //no reference for tep inbound (NEW DATA)
                'GROUP_CONCAT(DISTINCT tep_booking_types.booking_type) AS tep_booking_type', //booking type for tep inbound (NEW DATA)
                'GROUP_CONCAT(DISTINCT tep_booking_types.category) AS tep_booking_category', //booking category for tep inbound (NEW DATA)
                // 'GROUP_CONCAT(DISTINCT booking_safe_coduct.no_booking) AS no_booking_safe_conduct', // no booking for tep outbound
                'checkers.name AS checker_name', //general
                'checker_outs.name AS checker_out_name', //general
                'creator.name AS creator_name', //general
                'GROUP_CONCAT(DISTINCT safe_conducts.id) AS id_safe_conduct', //general
                'GROUP_CONCAT(DISTINCT safe_conducts.no_safe_conduct) AS no_safe_conduct', //general
                'MAX(safe_conducts.expedition_type) AS expedition_type', //general
                'MAX(safe_conducts.security_in_date) AS security_in_date', //general
                'MAX(safe_conducts.security_out_date) AS security_out_date', //general
                'tep_reference.tep_code AS tep_code_reference',
                'GROUP_CONCAT(DISTINCT people_customers.id) AS id_customer_out',
                'GROUP_CONCAT(DISTINCT people_customers.name) AS customer_name_out',
                // 'GROUP_CONCAT(DISTINCT people_ec.id) AS id_customer_out_ec',//from empty container
                // 'GROUP_CONCAT(DISTINCT people_ec.name) AS customer_name_out_ec',//from empty container
                'GROUP_CONCAT(DISTINCT people_customers_ec.id) AS id_customer_out_ecs',//from empty container
                'GROUP_CONCAT(DISTINCT people_customers_ec.name) AS customer_name_out_ecs',//from empty container
                'GROUP_CONCAT(DISTINCT booking_safe_coduct_customers_ec.no_booking) AS no_booking_safe_conduct_ec', // from empty container
                'IF(transporter_entry_permit_histories.id is null,1,0) AS can_edit',
                'booking_tep.id AS id_booking'
            ])
            ->join(UserModel::$tableUser . ' AS checkers', 'checkers.id = transporter_entry_permits.checked_in_by', 'left')
            ->join(UserModel::$tableUser . ' AS checker_outs', 'checker_outs.id = transporter_entry_permits.checked_out_by', 'left')
            ->join(UserModel::$tableUser . ' AS creator', 'creator.id = transporter_entry_permits.created_by', 'left')
            ->join('ref_branches', 'ref_branches.id = transporter_entry_permits.id_branch', 'left')
            ->join('safe_conducts', 'safe_conducts.id_transporter_entry_permit = transporter_entry_permits.id', 'left')
            ->join('transporter_entry_permits AS tep_reference', 'tep_reference.id = transporter_entry_permits.id_tep_reference', 'left')
            ->join('transporter_entry_permit_histories', 'transporter_entry_permit_histories.id_tep = transporter_entry_permits.id AND transporter_entry_permit_histories.created_by = transporter_entry_permits.checked_in_by', 'left')
            ->where_in('transporter_entry_permits.id_branch', $branchId)
            ->group_start()
                // ->group_start()
                //     ->where('transporter_entry_permits.tep_category="INBOUND" OR transporter_entry_permits.tep_category IS NULL')
                //     ->join('bookings', 'bookings.id = transporter_entry_permits.id_booking', 'left')
                //     ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
                //     ->join('ref_people', 'ref_people.id = bookings.id_customer', 'left')
                // ->group_end()
                ->or_group_start()
                    ->where('transporter_entry_permits.tep_category', "INBOUND")
                    ->join('transporter_entry_permit_bookings', 'transporter_entry_permit_bookings.id_tep = transporter_entry_permits.id', 'left')
                    ->join('bookings AS booking_tep', 'booking_tep.id = transporter_entry_permit_bookings.id_booking', 'left')
                    ->join('ref_booking_types AS tep_booking_types', 'tep_booking_types.id = booking_tep.id_booking_type', 'left')
                    ->join('ref_people AS people_tep', 'people_tep.id = booking_tep.id_customer', 'left')
                ->group_end()
                ->or_group_start()
                     ->where('transporter_entry_permits.tep_category', "OUTBOUND")
                    //  ->where('transporter_entry_permits.id_customer', null)
                     ->join('transporter_entry_permit_customers', 'transporter_entry_permit_customers.id_tep = transporter_entry_permits.id', 'left')
                     ->join('safe_conducts as safe_conduct_bookings_customers', 'safe_conduct_bookings_customers.id_transporter_entry_permit = transporter_entry_permits.id', 'left')
                     ->join('bookings as booking_safe_coduct_customers', 'booking_safe_coduct_customers.id = safe_conduct_bookings_customers.id_booking', 'left')
                     ->join('ref_people AS people_customers', 'people_customers.id = transporter_entry_permit_customers.id_customer', 'left')
                ->group_end()
                // ->or_group_start()
                //      ->where('transporter_entry_permits.tep_category', "EMPTY CONTAINER")
                //      ->where('transporter_entry_permits.id_customer !=', null)
                //      ->join('safe_conducts as safe_conduct_bookings_ec', 'safe_conduct_bookings_ec.id_transporter_entry_permit = transporter_entry_permits.id', 'left')
                //      ->join('bookings as booking_safe_coduct_ec', 'booking_safe_coduct_ec.id = safe_conduct_bookings_ec.id_booking', 'left')
                //      ->join('ref_people AS people_ec', 'people_ec.id = transporter_entry_permits.id_customer', 'left')
                // ->group_end()
                ->or_group_start()
                    ->where('safe_conducts.no_safe_conduct', null)
                    ->where('transporter_entry_permits.checked_out_by is not null', null)
                    ->join('transporter_entry_permits as tep2','tep2.id_tep_reference = transporter_entry_permits.id', 'left')
                    ->join('safe_conducts as safe_conduct_tep2', 'safe_conduct_tep2.id_transporter_entry_permit = tep2.id', 'left')
                    ->join('bookings as booking_safe_coduct_tep2', 'booking_safe_coduct_tep2.id = safe_conduct_tep2.id_booking', 'left')
                ->group_end()
                ->or_group_start()
                     ->where('transporter_entry_permits.tep_category', "EMPTY CONTAINER")
                    //  ->where('transporter_entry_permits.id_customer', null)
                     ->join('transporter_entry_permit_customers AS transporter_entry_permit_customers_ec', 'transporter_entry_permit_customers_ec.id_tep = transporter_entry_permits.id', 'left')
                     ->join('safe_conducts as safe_conduct_bookings_customers_ec', 'safe_conduct_bookings_customers_ec.id_transporter_entry_permit = transporter_entry_permits.id', 'left')
                     ->join('bookings as booking_safe_coduct_customers_ec', 'booking_safe_coduct_customers_ec.id = safe_conduct_bookings_customers_ec.id_booking', 'left')
                     ->join('ref_people AS people_customers_ec', 'people_customers_ec.id = transporter_entry_permit_customers_ec.id_customer', 'left')
                ->group_end()
            ->group_end()
            ->group_by('transporter_entry_permits.id');

        return $baseQuery;
    }

    /**
     * Optimized version of query index.
     *
     * @param null $branchId
     * @return CI_DB_mysql_driver|CI_DB_query_builder
     */
    public function getBaseQueryIndex($branchId = null)
    {
        $baseQuery = $this->db
            ->select([
                'transporter_entry_permits.id',
                'transporter_entry_permits.tep_code',
                'transporter_entry_permits.tep_category',
                'transporter_entry_permits.receiver_no_police',
                'transporter_entry_permits.receiver_vehicle',
                'transporter_entry_permits.receiver_name',
                'transporter_entry_permits.checked_in_at',
                'transporter_entry_permits.checked_out_at',
                'transporter_entry_permits.expired_at',
                'checkers.name AS checker_name',
                'checker_outs.name AS checker_out_name',
                '(
                    SELECT GROUP_CONCAT(DISTINCT no_safe_conduct) AS no_safe_conduct
                    FROM safe_conducts 
                    WHERE safe_conducts.id_transporter_entry_permit = transporter_entry_permits.id
                ) AS no_safe_conduct',
                'COALESCE( 
                    (
                        SELECT GROUP_CONCAT(DISTINCT ref_people.name) AS customer_name 
                        FROM transporter_entry_permit_customers
                        INNER JOIN ref_people ON ref_people.id = transporter_entry_permit_customers.id_customer
                        WHERE transporter_entry_permit_customers.id_tep = transporter_entry_permits.id
                    ),
                    (
                        SELECT GROUP_CONCAT(DISTINCT ref_people.name) AS customer_name 
                        FROM transporter_entry_permit_bookings
                        INNER JOIN bookings ON bookings.id = transporter_entry_permit_bookings.id_booking
                        INNER JOIN ref_people ON ref_people.id = bookings.id_customer
                        WHERE transporter_entry_permit_bookings.id_tep = transporter_entry_permits.id
                    ),
                    (
                        SELECT ref_people.name AS customer_name 
                        FROM ref_people
                        WHERE bookings.id_customer = ref_people.id
                    ),
                    ref_people.name
                ) AS customer_name',
                'COALESCE(
                    (
                        SELECT GROUP_CONCAT(DISTINCT bookings.no_booking) AS no_booking 
                        FROM transporter_entry_permit_uploads
                        INNER JOIN uploads ON uploads.id = transporter_entry_permit_uploads.id_upload
                        INNER JOIN bookings ON bookings.id_upload = uploads.id
                        WHERE transporter_entry_permit_uploads.id_tep = transporter_entry_permits.id
                    ),
                    (
                        SELECT GROUP_CONCAT(DISTINCT bookings.no_booking) AS no_booking 
                        FROM transporter_entry_permit_bookings
                        INNER JOIN bookings ON bookings.id = transporter_entry_permit_bookings.id_booking
                        WHERE transporter_entry_permit_bookings.id_tep = transporter_entry_permits.id
                    ),
                    bookings.no_booking,
                    (
                        SELECT GROUP_CONCAT(DISTINCT bookings.no_booking) AS no_booking 
                        FROM bookings
                        INNER JOIN safe_conducts ON safe_conducts.id_booking = bookings.id
                        WHERE safe_conducts.id_transporter_entry_permit = transporter_entry_permits.id
                    )
                ) AS no_booking',
                'IF((
                    SELECT MAX(id) FROM transporter_entry_permit_histories
                    WHERE transporter_entry_permit_histories.created_by = transporter_entry_permits.checked_in_by
                        AND transporter_entry_permit_histories.id_tep = transporter_entry_permits.id
                ) IS NULL, 1, 0) AS can_edit'
            ])
            ->from($this->table)
            ->join(UserModel::$tableUser . ' AS checkers', 'checkers.id = transporter_entry_permits.checked_in_by', 'left')
            ->join(UserModel::$tableUser . ' AS checker_outs', 'checker_outs.id = transporter_entry_permits.checked_out_by', 'left')
            ->join('ref_people', 'ref_people.id = transporter_entry_permits.id_customer', 'left')
            ->join('bookings', 'bookings.id = transporter_entry_permits.id_booking', 'left');

        if (!empty($branchId)) {
            $baseQuery->where('transporter_entry_permits.id_branch', $branchId);
        }

        return $baseQuery;
    }

    /**
     * Get all transporter entry data.
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
        $branch = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $this->db->start_cache();

        if ($start < 0) {
            $baseQuery = $this->getBaseQuery($branch);
        } else {
            $baseQuery = $this->getBaseQueryIndex($branch);
        }

        if (!empty($search)) {
            $search = trim($search);
            $baseQuery->group_start()
                ->or_like('transporter_entry_permits.tep_code', $search)
                ->or_like('transporter_entry_permits.tep_category', $search)
                ->or_like('transporter_entry_permits.receiver_no_police', $search)
                ->or_like('transporter_entry_permits.receiver_vehicle', $search)
                ->or_like('transporter_entry_permits.receiver_name', $search)
                ->or_like('bookings.no_booking', $search)
                ->or_like('ref_people.name', $search)
                ->or_like('checkers.name', $search)
                ->or_like('checker_outs.name', $search);
            $baseQuery->or_where("EXISTS (
                SELECT safe_conducts.id FROM safe_conducts
                INNER JOIN bookings ON bookings.id = safe_conducts.id_booking
                WHERE (safe_conducts.no_safe_conduct LIKE '%" . $search . "%' OR bookings.no_booking LIKE '%" . $search . "%')
                    AND transporter_entry_permits.id = safe_conducts.id_transporter_entry_permit
            )");
            $baseQuery->or_where("EXISTS (
                SELECT bookings.id FROM bookings
                INNER JOIN transporter_entry_permit_bookings ON transporter_entry_permit_bookings.id_booking = bookings.id
                WHERE bookings.no_booking LIKE '%" . $search . "%'
                    AND transporter_entry_permits.id = transporter_entry_permit_bookings.id_tep
            )");
            $baseQuery->or_where("EXISTS (
                SELECT bookings.id FROM bookings
                INNER JOIN uploads ON uploads.id = bookings.id_upload
                INNER JOIN transporter_entry_permit_uploads ON transporter_entry_permit_uploads.id_upload = uploads.id
                WHERE bookings.no_booking LIKE '%" . $search . "%'
                    AND transporter_entry_permits.id = transporter_entry_permit_uploads.id_tep
            )");
            $baseQuery->or_where("EXISTS (
                SELECT ref_people.id FROM ref_people
                INNER JOIN transporter_entry_permit_customers ON transporter_entry_permit_customers.id_customer = ref_people.id
                WHERE ref_people.name LIKE '%" . $search . "%'
                    AND transporter_entry_permits.id = transporter_entry_permit_customers.id_tep
            )");
            $baseQuery->or_where("EXISTS (
                SELECT ref_people.id FROM ref_people 
                INNER JOIN bookings ON bookings.id_customer = ref_people.id
                INNER JOIN transporter_entry_permit_bookings ON transporter_entry_permit_bookings.id_booking = bookings.id
                WHERE ref_people.name LIKE '%" . $search . "%'
                    AND transporter_entry_permits.id = transporter_entry_permit_bookings.id_tep
            )");
            $baseQuery->or_where("EXISTS (
                SELECT ref_people.id FROM ref_people
                WHERE ref_people.name LIKE '%" . $search . "%'
                    AND bookings.id_customer = ref_people.id
            )");
            $baseQuery->group_end();
        }

        if (key_exists('tep', $filters) && !empty($filters['tep'])) {
            $baseQuery->where('transporter_entry_permits.id', $filters['tep']);
        }

        if (key_exists('linked_tep', $filters) && !empty($filters['linked_tep'])) {
            $baseQuery->where('transporter_entry_permits.id_linked_tep', $filters['linked_tep']);
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        if ($column == 'no') $column = $this->table . '.id';
        $page = $baseQuery->order_by($column, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        $this->db->flush_cache();

        foreach ($data as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($data),
            "recordsFiltered" => $total,
            "data" => $data
        ];

        return $pageData;
    }

    /**
     * Generate unique code number
     * @param int $maxTrial
     * @return string
     */
    public function generateCode($maxTrial = 10)
    {
        $this->load->helper('string');

        $code = self::TEP_CODE . '/' . strtoupper(random_string('alnum', 6));

        if (!empty($this->getBy(['transporter_entry_permits.tep_code' => $code, 'transporter_entry_permits.expired_at>=NOW()' => null]))) {
            if ($maxTrial > 0) {
                return $this->generateCode($maxTrial - 1);
            }
        }

        return $code;
    }

    /**
     * Generate unique code number
     * @param int $branchId
     * @return array
     */
    public function getTransporterIn($branchId = null)
    {
        if(empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $baseQuery = parent::getBaseQuery()
            ->select('prv_users.name AS check_in_name')
            ->select(['GROUP_CONCAT(DISTINCT safe_conducts.id) AS id_safe_conduct', 
                'GROUP_CONCAT(DISTINCT safe_conducts.no_safe_conduct) AS no_safe_conduct'])
            ->select([
                'GROUP_CONCAT(DISTINCT people_tep.name) AS customer_name_in',
                'GROUP_CONCAT(DISTINCT people_customers.name) AS customer_name_out',
                'IF(transporter_entry_permit_histories.id is null,1,0) AS can_edit',
                'transporter_entry_permit_chassis.no_chassis'])
            ->where('transporter_entry_permits.id_branch', $branchId)
            ->where('transporter_entry_permits.checked_in_at is not null', null)
            ->where('transporter_entry_permits.checked_in_at >=', "2019-11-23 00:00:00")
            ->where('transporter_entry_permits.checked_out_at is null', null)
            ->join(env('DB_SSO_DATABASE') .'.prv_users AS prv_users','prv_users.id=checked_in_by','left')
            ->join('safe_conducts', 'safe_conducts.id_transporter_entry_permit = transporter_entry_permits.id', 'left')
            // ->join('ref_people', 'ref_people.id = transporter_entry_permits.id_customer', 'left')
            ->join('transporter_entry_permit_histories', 'transporter_entry_permit_histories.id_tep = transporter_entry_permits.id AND transporter_entry_permit_histories.created_by = transporter_entry_permits.checked_in_by', 'left')
            ->join('transporter_entry_permit_chassis', 'transporter_entry_permit_chassis.id_tep = transporter_entry_permits.id', 'left')
            ->group_start()
                ->group_start()
                    ->where('transporter_entry_permits.tep_category="INBOUND" OR transporter_entry_permits.tep_category IS NULL')
                    // ->where('transporter_entry_permits.id_booking !=', null)
                    // ->where('transporter_entry_permits.id_customer', null)
                    ->join('transporter_entry_permit_bookings', 'transporter_entry_permit_bookings.id_tep = transporter_entry_permits.id', 'left')
                    ->join('bookings', 'bookings.id = transporter_entry_permit_bookings.id_booking', 'left')
                    ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
                    ->join('ref_people as rp', 'rp.id = bookings.id_customer', 'left')
                ->group_end()
                ->or_group_start()
                    ->where('transporter_entry_permits.tep_category', "INBOUND")
                    // ->where('transporter_entry_permits.id_booking', null)
                    // ->where('transporter_entry_permits.id_customer', null)
                    ->join('transporter_entry_permit_bookings', 'transporter_entry_permit_bookings.id_tep = transporter_entry_permits.id', 'left')
                    ->join('bookings AS booking_tep', 'booking_tep.id = transporter_entry_permit_bookings.id_booking', 'left')
                    ->join('ref_booking_types AS tep_booking_types', 'tep_booking_types.id = booking_tep.id_booking_type', 'left')
                    ->join('ref_people AS people_tep', 'people_tep.id = booking_tep.id_customer', 'left')
                ->group_end()
                ->or_group_start()
                     ->where('transporter_entry_permits.tep_category', "OUTBOUND")
                     ->or_where('transporter_entry_permits.tep_category', "EMPTY CONTAINER")
                    //  ->where('transporter_entry_permits.id_customer !=', null)
                     ->join('safe_conducts as safe_conduct_bookings', 'safe_conduct_bookings.id_transporter_entry_permit = transporter_entry_permits.id', 'left')
                     ->join('bookings as booking_safe_coduct', 'booking_safe_coduct.id = safe_conduct_bookings.id_booking', 'left')
                    //  ->join('ref_people AS people', 'people.id = transporter_entry_permits.id_customer', 'left')
                ->group_end()
                ->or_group_start()
                     ->where('transporter_entry_permits.tep_category', "OUTBOUND")
                     ->or_where('transporter_entry_permits.tep_category', "EMPTY CONTAINER")
                    //  ->where('transporter_entry_permits.id_customer', null)
                     ->join('transporter_entry_permit_customers', 'transporter_entry_permit_customers.id_tep = transporter_entry_permits.id', 'left')
                     ->join('safe_conducts as safe_conduct_bookings_customers', 'safe_conduct_bookings_customers.id_transporter_entry_permit = transporter_entry_permits.id', 'left')
                     ->join('bookings as booking_safe_coduct_customers', 'booking_safe_coduct_customers.id = safe_conduct_bookings_customers.id_booking', 'left')
                     ->join('ref_people AS people_customers', 'people_customers.id = transporter_entry_permit_customers.id_customer', 'left')
                ->group_end()
            ->group_end()
            ->group_by('receiver_no_police')
            ->order_by('transporter_entry_permits.checked_in_at','desc');
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
        $baseQuery = $this->getBaseQuery()
            ->select(['transporter_entry_permit_requests.queue_time',
            'transporter_entry_permit_requests.slot',
            'uploads.id as id_upload',
            'uploads.description as no_aju',
            'GROUP_CONCAT(DISTINCT upload_multi.id) AS id_upload_multi',
            'GROUP_CONCAT(DISTINCT upload_multi.description) AS no_aju_multi',
            ])
            ->join("transporter_entry_permit_uploads","transporter_entry_permit_uploads.id_tep = transporter_entry_permits.id","left")
            ->join('transporter_entry_permit_request_tep','transporter_entry_permit_request_tep.id_tep = transporter_entry_permits.id', 'left')
            ->join('transporter_entry_permit_requests','transporter_entry_permit_requests.id = transporter_entry_permit_request_tep.id_request', 'left')
            ->join('uploads','uploads.id = transporter_entry_permit_requests.id_upload', 'left')
            ->join('uploads AS upload_multi','upload_multi.id = transporter_entry_permit_uploads.id_upload', 'left');


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
     * Get all transporter entry data.
     *
     * @param array $filters
     * @param bool $withTrashed
     * @return mixed
     */
    public function getQueueTep($filters = [], $withTrashed = false)
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branch = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $columns = [
            "transporter_entry_permits.id",
            "ref_people.name",
            "bookings.no_booking",
            "transporter_entry_permits.tep_category",
            "transporter_entry_permits.tep_code",
            "transporter_entry_permits.checked_in_at",
            "transporter_entry_permits.checked_in_by",
            "transporter_entry_permits.checked_out_at",
            "transporter_entry_permits.checked_out_by",
            "transporter_entry_permits.receiver_name",
            "safe_conducts.no_safe_conduct",
            "transporter_entry_permits.id",
        ];
        $columnSort = $columns[$column];

        $this->db->start_cache();

        $baseQuery = $this->getBaseQuery($branch)
            ->select(['transporter_entry_permit_requests.queue_time',
            'uploads.id as id_upload',
            'uploads.description as no_aju',
            'GROUP_CONCAT(DISTINCT upload_multi.id) AS id_upload_multi',
            'GROUP_CONCAT(DISTINCT upload_multi.description) AS no_aju_multi',
            'transporter_entry_permit_requests.description AS description_req',
            'transporter_entry_permit_requests.id AS id_tep_req',
            'GROUP_CONCAT(DISTINCT transporter_entry_permit_requests.id) AS id_tep_req_multi',
            ])
            
            ->join("transporter_entry_permit_uploads","transporter_entry_permit_uploads.id_tep = transporter_entry_permits.id","left")
            ->join('transporter_entry_permit_request_tep','transporter_entry_permit_request_tep.id_tep = transporter_entry_permits.id', 'left')
            ->join('transporter_entry_permit_requests','transporter_entry_permit_requests.id = transporter_entry_permit_request_tep.id_request', 'left')
            ->join('uploads','uploads.id = transporter_entry_permit_requests.id_upload', 'left')
            ->join('uploads AS upload_multi','upload_multi.id = transporter_entry_permit_uploads.id_upload', 'left')
            ->where('transporter_entry_permit_uploads.id IS NOT NULL');

        if (!empty($search)) {
            $baseQuery->group_start();
            foreach ($columns as $field) {
                $baseQuery->or_like($field, trim($search));
            }
            $baseQuery->or_like('bookings.no_reference', trim($search));
            $baseQuery->or_like('ref_booking_types.category', trim($search));
            $baseQuery->or_like('people.name', trim($search));
            $baseQuery->group_end();
        }
        if (key_exists('tep_category', $filters) && !empty($filters['tep_category'])) {
            $baseQuery->where('transporter_entry_permits.tep_category', $filters['tep_category']);
        }
        if (key_exists('expired_date', $filters) && !empty($filters['expired_date'])) {
            $baseQuery->where('DATE(transporter_entry_permits.expired_at)', $filters['expired_date']);
        }
        if (key_exists('id_customer', $filters) && !empty($filters['id_customer'])) {
            $baseQuery->where_in('transporter_entry_permit_customers.id_customer', $filters['id_customer']);
        }
        if (key_exists('sort_by', $filters) && !empty($filters['sort_by'])) {
            if (key_exists('order_method', $filters) && !empty($filters['order_method'])) {
                $baseQuery->order_by($filters['sort_by'], $filters['order_method']);
            } else {
                $baseQuery->order_by($filters['sort_by'], 'desc');
            }
        }
        if (key_exists('armada', $filters) && !empty($filters['armada'])) {
            $baseQuery->where('transporter_entry_permit_requests.armada', $filters['armada']);
            if($filters['armada'] == 'TCI' && key_exists('id_request', $filters) && !empty($filters['id_request'])){
                $baseQuery->having('id_tep_req_multi NOT LIKE "%'.$filters['id_request'].'%"', null);
            }
        }
        if (key_exists('has_queue', $filters) && !empty($filters['has_queue'])) {
            $baseQuery->where('transporter_entry_permit_requests.queue_time IS NOT NULL', null);
        }
        if (key_exists('not_checkout', $filters) && !empty($filters['not_checkout'])) {
            $baseQuery->where('transporter_entry_permits.checked_out_at IS NULL', null);
        }
        if (key_exists('not_expired', $filters) && !empty($filters['not_expired'])) {
            $baseQuery->where('DATE(transporter_entry_permits.expired_at) >=', $filters['not_expired']);
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        $page = $baseQuery->order_by($columnSort, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        $this->db->flush_cache();

        foreach ($data as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($data),
            "recordsFiltered" => $total,
            "data" => $data
        ];

        return $pageData;
    }

    /**
     * Get outstanding tep for safe conduct.
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getOutstandingTep($filters = [])
    {
        if (empty($filters)) {
            return [];
        }
        $branch = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $baseQuery = $this->db
            ->select('transporter_entry_permits.*')
            ->distinct()
            ->from($this->table)
            ->join('transporter_entry_permit_bookings', 'transporter_entry_permit_bookings.id_tep = transporter_entry_permits.id', 'left')
            ->join('transporter_entry_permit_customers', 'transporter_entry_permit_customers.id_tep = transporter_entry_permits.id', 'left');

        if (key_exists('category', $filters) && !empty($filters['category'])) {
            $categoryValue = is_array($filters['category']) ? $filters['category'] : explode(',', $filters['category']);
            $baseQuery->where_in('transporter_entry_permits.tep_category', $categoryValue);
        }

        // available for outbound and empty container only
        if (key_exists('id_customer', $filters) && !empty($filters['id_customer'])) {
            $customerValue = is_array($filters['id_customer']) ? $filters['id_customer'] : explode(',', $filters['id_customer']);
            $baseQuery
                ->select(['tep_customers.name AS customer_name'])
                ->join('ref_people AS tep_customers', 'tep_customers.id = transporter_entry_permit_customers.id_customer', 'left')
                ->where_in('transporter_entry_permit_customers.id_customer', $customerValue);
        }

        // join related information inside filter to prevent producing multiple rows (inbound)
        if (key_exists('id_booking', $filters) && !empty($filters['id_booking'])) {
            $bookingValue = is_array($filters['id_booking']) ? $filters['id_booking'] : explode(',', $filters['id_booking']);
            $baseQuery
                ->select([
                    'IFNULL(booking_customers.name, booking_upload_customers.name) AS customer_name',
                    'IFNULL(bookings.no_reference, booking_uploads.no_reference) AS no_reference'
                ])
                ->join('bookings', 'bookings.id = transporter_entry_permit_bookings.id_booking', 'left')
                ->join('ref_people AS booking_customers', 'booking_customers.id = bookings.id_customer', 'left')
                // upload reference, can cause multiple rows when tep_bookings and tep_uploads filled data together
                ->join('transporter_entry_permit_uploads', 'transporter_entry_permit_uploads.id_tep = transporter_entry_permits.id', 'left')
                ->join('uploads', 'uploads.id = transporter_entry_permit_uploads.id_upload', 'left')
                ->join('bookings AS booking_uploads', 'booking_uploads.id_upload = uploads.id', 'left')
                ->join('ref_people AS booking_upload_customers', 'booking_upload_customers.id = booking_uploads.id_customer', 'left')
                ->group_start()
                    ->where_in('transporter_entry_permit_bookings.id_booking', $bookingValue)
                    ->or_where_in('booking_uploads.id', $bookingValue)
                ->group_end();
        }

        if (!empty($branch)) {
            $baseQuery->where('transporter_entry_permits.id_branch', $branch);
        }

        if (key_exists('should_checked_in', $filters) && $filters['should_checked_in']) {
            $baseQuery->where('transporter_entry_permits.checked_in_at IS NOT NULL', null);
        }

        if (key_exists('outstanding_checked_out', $filters) && $filters['outstanding_checked_out']) {
            $baseQuery->where('transporter_entry_permits.checked_out_at IS NULL', null);
        }

        if (key_exists('outstanding_tracking_link', $filters) && $filters['outstanding_tracking_link']) {
            $baseQuery
                ->select([
                    'GROUP_CONCAT(DISTINCT safe_conducts.no_safe_conduct) AS no_safe_conduct',
                ])
                ->join('safe_conducts', 'safe_conducts.id_transporter_entry_permit = transporter_entry_permits.id', 'left')
                ->where("NOT EXISTS(
                    SELECT id FROM transporter_entry_permit_trackings
                    WHERE id_tep = transporter_entry_permits.id
                )", null)
                ->where('transporter_entry_permits.created_at >=', '2021-09-20')
                ->group_by('transporter_entry_permits.id');
        }

        if (key_exists('active_days', $filters) && !empty($filters['active_days'])) {
            $dateLimit = date('Y-m-d', strtotime("-{$filters['active_days']} day"));
            $baseQuery->where('DATE(transporter_entry_permits.created_at)>=', $dateLimit);
        }

        if (key_exists('id_except_safe_conduct', $filters) && !empty($filters['id_except_safe_conduct'])) {
            $baseQuery
                ->join('safe_conducts', 'safe_conducts.id_transporter_entry_permit = transporter_entry_permits.id')
                ->or_where('safe_conducts.id', $filters['id_except_safe_conduct']);
        }

        if (key_exists('id_except_tep', $filters) && !empty($filters['id_except_tep'])) {
            $baseQuery->or_where('transporter_entry_permits.id', $filters['id_except_tep']);
        }

        if (key_exists('id', $filters) && $filters['id']) {
            $baseQuery->where('transporter_entry_permits.id', $filters['id']);
        }

        if (key_exists('id_upload', $filters) && !empty($filters['id_upload'])) {
            $baseQuery
                ->join("transporter_entry_permit_uploads","transporter_entry_permit_uploads.id_tep = transporter_entry_permits.id","left")
                ->join('transporter_entry_permit_request_tep','transporter_entry_permit_request_tep.id_tep = transporter_entry_permits.id', 'left')
                ->where('IF(transporter_entry_permit_request_tep.id is NULL, true, (transporter_entry_permit_uploads.id_upload = "'.$filters['id_upload'].'"))');
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get total outbound stats.
     *
     * @param array $filters
     * @return int|mixed
     */
    public function getTotalOutbound($filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $baseQuery = $this->db
            ->select('COUNT(id) AS total_outbound')
            ->from($this->table)
            ->where('tep_category', 'OUTBOUND');

        if (!empty($branchId)) {
            $baseQuery->where('id_branch', $branchId);
        }

        if (key_exists('checked_out_at', $filters) && !empty($filters['checked_out_at'])) {
            $baseQuery->where("DATE({$this->table}.checked_out_at)", format_date($filters['checked_out_at']));
        }

        return $baseQuery->get()->row_array()['total_outbound'] ?? 0;
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
        $baseQuery = $this->getBaseQuery()
                ->select([
                    'SUM(IF(transporter_entry_permit_checklists.type = "CHECK IN" AND transporter_entry_permit_checklists.id_container IS NULL, 1, 0)) as total_check_in', //general
                    'sum(IF(transporter_entry_permit_checklists.type = "CHECK OUT" AND transporter_entry_permit_checklists.id_container IS NULL, 1, 0)) as total_check_out', //general
                    'booking_uploads.no_reference AS no_reference_in_req'
                ])
                ->join('transporter_entry_permit_checklists', 'transporter_entry_permits.id = transporter_entry_permit_checklists.id_tep', 'left')
                ->join("transporter_entry_permit_uploads","transporter_entry_permit_uploads.id_tep = transporter_entry_permits.id","left")
                ->join("bookings AS booking_uploads","booking_uploads.id_upload = transporter_entry_permit_uploads.id_upload","left")
                ->join('transporter_entry_permit_request_tep','transporter_entry_permit_request_tep.id_tep = transporter_entry_permits.id', 'left')
                ->join('transporter_entry_permit_requests','transporter_entry_permit_requests.id = transporter_entry_permit_request_tep.id_request', 'left');

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
     * Get ALL tep.
     *
     * @param $conditions
     * @param bool $resultRow
     * @param bool $withTrashed
     * @return array|int
     */
    public function getAllTep($conditions, $resultRow = false, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery([1,2,3,4,5,6,7,8,9,10])//all branch
                ->select(['transporter_entry_permit_requests.queue_time',
                    'uploads.id as id_upload',
                    'uploads.description as no_aju',
                    'GROUP_CONCAT(DISTINCT upload_multi.id) AS id_upload_multi',
                    'GROUP_CONCAT(DISTINCT upload_multi.description) AS no_aju_multi',
                    ])
                ->join("transporter_entry_permit_uploads","transporter_entry_permit_uploads.id_tep = transporter_entry_permits.id","left")
                ->join('transporter_entry_permit_request_tep','transporter_entry_permit_request_tep.id_tep = transporter_entry_permits.id', 'left')
                ->join('transporter_entry_permit_requests','transporter_entry_permit_requests.id = transporter_entry_permit_request_tep.id_request', 'left')
                ->join('uploads','uploads.id = transporter_entry_permit_requests.id_upload', 'left')
                ->join('uploads AS upload_multi','upload_multi.id = transporter_entry_permit_uploads.id_upload', 'left');

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
     * Get single model data by id with or without deleted record.
     *
     * @param $modelId
     * @param bool $withTrashed
     * @return mixed
     */
    public function getByIdNonBase($modelId, $withTrashed = false)
    {
        $baseQuery = 
            $this->db
            ->select('transporter_entry_permits.*')
            ->distinct()
            ->from($this->table)
            ->select(['transporter_entry_permit_requests.queue_time',
            'transporter_entry_permit_requests.slot',
            'uploads.id as id_upload',
            'uploads.description as no_aju',
            'GROUP_CONCAT(DISTINCT upload_multi.id) AS id_upload_multi',
            'GROUP_CONCAT(DISTINCT upload_multi.description) AS no_aju_multi',
            ])
            ->join("transporter_entry_permit_uploads","transporter_entry_permit_uploads.id_tep = transporter_entry_permits.id","left")
            ->join('transporter_entry_permit_request_tep','transporter_entry_permit_request_tep.id_tep = transporter_entry_permits.id', 'left')
            ->join('transporter_entry_permit_requests','transporter_entry_permit_requests.id = transporter_entry_permit_request_tep.id_request', 'left')
            ->join('uploads','uploads.id = transporter_entry_permit_requests.id_upload', 'left')
            ->join('uploads AS upload_multi','upload_multi.id = transporter_entry_permit_uploads.id_upload', 'left')
            ->group_by('transporter_entry_permits.id');


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
}