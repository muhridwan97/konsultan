<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class BookingModel
 * @property ReportModel $reportModel
 * @property ReportStockModel $reportStock
 * @property BookingModel $booking
 */
class BookingModel extends MY_Model
{
    protected $table = 'bookings';

    const NO_INBOUND = 'BI';
    const NO_OUTBOUND = 'BO';
    const NO_CHANGEOWNER_IN = 'CI';
    const NO_CHANGEOWNER_OUT = 'CO';

    const STATUS_IMPORTED = 'IMPORTED';
    const STATUS_BOOKED = 'BOOKED';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_COMPLETED = 'COMPLETED';

    const PAYMENT_NOT_STARTED = 'Not Started';
    const PAYMENT_SKB_ON_PROGRESS = 'SKB On-Progress';
    const PAYMENT_BILLING_ON_PROGRESS = 'Code Billing On-Progress';
    const PAYMENT_SKB_DONE = 'SKB Done';
    const PAYMENT_SKB_BILLING_DONE = 'Code Billing Done';

    const PAYMENT_STATUSES = [
        self::PAYMENT_NOT_STARTED, self::PAYMENT_SKB_ON_PROGRESS, self::PAYMENT_BILLING_ON_PROGRESS,
        self::PAYMENT_SKB_DONE, self::PAYMENT_SKB_BILLING_DONE
    ];

    const BCF_NOT_STARTED = 'Not Started';
    const BCF_SPPD_ON_PROGRESS = 'SPPD On-Progress';
    const BCF_SPPF_ON_PROGRESS = 'SPPF On-Progress';
    const BCF_SPPD_DONE = 'SPPD Done';
    const BCF_SPPF_DONE = 'SPPF Done';

    const BCF_STATUSES = [
        self::BCF_NOT_STARTED, self::BCF_SPPD_ON_PROGRESS, self::BCF_SPPF_ON_PROGRESS,
        self::BCF_SPPD_DONE, self::BCF_SPPF_DONE
    ];

    const DOCUMENT_STATUSES = [
        'BTD', 'BDN', 'BMN', 'BTN AJU BMPDRI', 'BTD KEP LIMIT', 'BTD LELANG 1', 'BTN LELANG 2', 'BTN AJU MUSNAH',
        'BTN AJU CIKARANG', 'BTD CIKARANG', 'BTD STATUS LAINNYA', 'BMN AJU APPRAISAL', 'BMN KEP LIMIT',
        'BMN LELANG 1', 'BMN LELANG 2', 'BMN LELANG 3', 'BMN LELANG 4', 'BMN LELANG 5', 'BMN AJU MUSNAH', 'BMN AJU CIKARANG',
        'BMN CIKARANG', 'BMN STATUS LAINNYA', 'SPPB BELUM KELUAR', 'TEGAHAN', 'BTD TURUN KEP', 'BTD LELANG 2',
        'BTD AJU MUSNAH', 'BTD AJU CIKARANG', 'BTD MUSNAH', 'BMN MUSNAH', 'BTD AJU BMPDRI', 'LAIN - LAIN'
    ];

    /**
     * BookingModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get total booking records.
     * @return mixed
     */
    public function getTotalBookings()
    {
        return $this->db->count_all($this->table);
    }

    /**
     * Get basic query for booking data
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $bookings = $this->db->select([
            'bookings.*',
            'uploads.no_upload',
            'uploads.description as aju',
            'booking_in.id AS id_booking_in',
            'booking_in.no_booking AS no_booking_in',
            'booking_in.no_reference AS no_reference_in',
            'ref_booking_types.booking_type',
            'ref_booking_types.type',
            'ref_booking_types.with_do',
            'ref_booking_types.category',
            'ref_suppliers.name AS supplier_name',
            'ref_customers.name AS customer_name',
            'ref_customers.outbound_type',
            'ref_branches.branch',
            'GROUP_CONCAT(ref_document_types.document_type SEPARATOR ";") AS document_type',
            'GROUP_CONCAT(upload_documents.no_document SEPARATOR ",") AS booking_document',
            'GROUP_CONCAT(upload_documents.id SEPARATOR ",") AS upload_document',
            'prv_users.name AS creator_name',
            get_ext_field('ETA', 'bookings.id', 'eta'),
            'ref_branches.branch_type',
        ])
            ->from($this->table)
            ->join('uploads', 'bookings.id_upload = uploads.id', 'left')
            ->join('(
                SELECT * FROM upload_documents WHERE is_deleted = false AND is_valid = 1
            ) AS upload_documents', 'uploads.id = upload_documents.id_upload AND upload_documents.is_deleted = 0', 'left')
            ->join('ref_document_types', 'upload_documents.id_document_type = ref_document_types.id', 'left')
            ->join('ref_booking_types', 'bookings.id_booking_type = ref_booking_types.id', 'left')
            ->join('ref_people AS ref_suppliers', 'bookings.id_supplier = ref_suppliers.id', 'left')
            ->join('ref_people AS ref_customers', 'bookings.id_customer = ref_customers.id', 'left')
            ->join('bookings AS booking_in', 'bookings.id_booking = booking_in.id', 'left')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = bookings.created_by', 'left')
            ->group_by('bookings.id');

        if (!empty($branchId)) {
            $bookings->where('bookings.id_branch', $branchId);
        }

        return $bookings;
    }

    /**
     * Optimized version of base query, select only what index page needs.
     *
     * @param null $branchId
     * @return CI_DB_mysql_driver|CI_DB_query_builder
     */
    public function getBaseQueryIndex($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $baseQuery = $this->db
            ->select([
                'bookings.id',
                'bookings.no_booking',
                'bookings.no_reference',
                'bookings.booking_date',
                'ref_customers.name AS customer_name',
                'ref_customers.outbound_type',
                'ref_booking_types.booking_type',
                'ref_booking_types.type',
                'ref_booking_types.category',
                'ref_branches.branch',
                'ref_branches.branch_type',
                'bookings.status',
                'bookings.status_payout',
                'uploads.no_upload',
                '(
                    SELECT GROUP_CONCAT(booking_refs.no_reference SEPARATOR ",") AS no_reference_in 
                    FROM booking_references
                    INNER JOIN bookings AS booking_refs ON booking_refs.id = booking_references.id_booking_reference
                    WHERE booking_references.id_booking = bookings.id
                ) AS no_reference_in',
                '(
                    SELECT GROUP_CONCAT(ref_document_types.document_type SEPARATOR ";") AS document_type 
                    FROM upload_documents
                    INNER JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                    WHERE upload_documents.id_upload = uploads.id 
                        AND upload_documents.is_valid = 1
                        AND upload_documents.is_deleted = 0
                ) AS document_type',
                '(
                    SELECT GROUP_CONCAT(upload_documents.no_document SEPARATOR ",") AS no_document 
                    FROM upload_documents
                    WHERE upload_documents.id_upload = uploads.id 
                        AND upload_documents.is_valid = 1
                        AND upload_documents.is_deleted = 0
                ) AS booking_document',
                '(
                    SELECT GROUP_CONCAT(upload_documents.id SEPARATOR ",") AS id_upload_document 
                    FROM upload_documents
                    WHERE upload_documents.id_upload = uploads.id 
                        AND upload_documents.is_valid = 1
                        AND upload_documents.is_deleted = 0
                ) AS upload_document',
            ])
            ->from($this->table)
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type')
            ->join('ref_people AS ref_customers', 'ref_customers.id = bookings.id_customer', 'left')
            ->join('uploads', 'bookings.id_upload = uploads.id', 'left');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        return $baseQuery;
    }

    /**
     * Get all bookings with or without deleted records.
     *
     * @param null $filters
     * @param bool $withTrashed
     * @return array
     */
    public function getAllBookings($filters = null, $withTrashed = false)
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        // alias column name by index for sorting data table library
        $columnOrder = [
            0 => "bookings.id",
            1 => "ref_customers.name",
            2 => "bookings.no_booking",
            3 => "bookings.booking_date",
            4 => "ref_booking_types.category",
            5 => "GROUP_CONCAT(upload_documents.no_document SEPARATOR ',')",
            6 => "bookings.status",
            7 => "bookings.id",
        ];
        $columnSort = $columnOrder[$column];

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $this->db->start_cache();

        if ($start < 0) {
            $bookings = $this->getBaseQuery($branchId);
        } else {
            $bookings = $this->getBaseQueryIndex($branchId);
        }

        if (!empty($search)) {
            $bookings
                ->group_start()
                ->like('ref_customers.name', trim($search))
                ->or_like('ref_customers.outbound_type', trim($search))
                ->or_like('bookings.no_booking', trim($search))
                ->or_like('bookings.booking_date', trim($search))
                ->or_like('ref_booking_types.category', trim($search))
                ->or_like('bookings.no_reference', trim($search))
                ->or_like('bookings.status', trim($search))
                ->or_like('bookings.status_payout', trim($search))
                ->or_where("EXISTS (
                    SELECT booking_refs.id FROM booking_references
                    INNER JOIN bookings AS booking_refs ON booking_refs.id = booking_references.id_booking_reference
                    WHERE booking_references.id_booking = bookings.id
                        AND (booking_refs.no_booking = '" . trim($search) . "' OR booking_refs.no_reference = '" . trim($search) . "')
                )") // search inbound -> found related outbound
                ->or_where("EXISTS (
                    SELECT booking_refs.id FROM booking_references
                    INNER JOIN bookings AS booking_refs ON booking_refs.id = booking_references.id_booking
                    WHERE booking_references.id_booking_reference = bookings.id
                        AND (booking_refs.no_booking = '" . trim($search) . "' OR booking_refs.no_reference = '" . trim($search) . "')
                )") // search outbound -> found related inbound
                ->group_end();
        }

        if ($userType == 'EXTERNAL') {
            $bookings->where('bookings.id_customer', $customerId);
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $bookings->where('bookings.id_customer', $filters['customer']);
        }

        if (key_exists('status', $filters) && !empty($filters['status'])) {
            $bookings->where_in('bookings.status', $filters['status']);
        }

        if (key_exists('category', $filters) && !empty($filters['category'])) {
            $bookings->where('ref_booking_types.category', $filters['category']);
        }

        if (key_exists('booking_type', $filters) && !empty($filters['booking_type'])) {
            $bookings->where('ref_booking_types.id', $filters['booking_type']);
        }

        if (key_exists('ob_tps_type', $filters) && !empty($filters['ob_tps_type'])) {
            // $bookings->where('(SELECT COUNT(id) FROM payments WHERE id_booking = bookings.id AND payment_type = "' . $filters['ob_tps_type'] . '") >', 0);
            $bookings->where('EXISTS (SELECT id FROM payments WHERE is_deleted = false AND id_booking = bookings.id AND payment_type = "' . $filters['ob_tps_type'] . '")');
        }

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $bookings->where('DATE(bookings.created_at)>=', format_date($filters['date_from']));
        }

        if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
            $bookings->where('DATE(bookings.created_at)<=', format_date($filters['date_to']));
        }

        if (!$withTrashed) {
            $bookings->where('bookings.is_deleted', false);
        }

        $this->db->stop_cache();

        if ($start < 0) {
            if ($filters['unbuffered'] ?? false) {
                $allData = $bookings->get()->unbuffered_row('array');
            } else {
                $allData = $bookings->get()->result_array();
            }

            $this->db->flush_cache();

            return $allData;
        }

        // counting result is slow, use simple pagination or cache to share result set like below
        $distinctQueryParams = filter_data_by_keys($filters, ['customer', 'booking_type', 'category', 'ob_tps_type', 'date_from', 'date_to', 'search']);
        $cacheIdxKey = 'bo-idx-count-' . $branchId . '-' . md5(json_encode($distinctQueryParams));
        $bookingsTotal = cache_remember($cacheIdxKey, 300, function() use ($bookings) {
            return $bookings->count_all_results();
        });

        //$bookingsTotal = $this->db->count_all_results();
        $bookingPage = $bookings->order_by($columnSort, $sort)->limit($length, $start);
        $bookingData = $bookingPage->get()->result_array();

        foreach ($bookingData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($bookingData),
            "recordsFiltered" => $bookingsTotal,
            "data" => $bookingData
        ];
        $this->db->flush_cache();

        return $pageData;
    }

    /**
     * Get outstanding rating data.
     *
     * @param array $filters
     * @return array
     */
    public static function getOutstandingRating($filters = [])
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

        $bookings = get_instance()->db
            ->select([
                'bookings.id',
                'bookings.no_booking',
                'bookings.no_reference',
                'bookings.booking_date',
                'bookings.rating',
                'bookings.rating_description',
                'bookings.rated_at',
                'complete_statuses.completed_date'
            ])
            ->from('bookings')
            ->join('(
                    SELECT id_booking, MIN(booking_statuses.created_at) AS completed_date
                    FROM booking_statuses
                    WHERE booking_status = "COMPLETED"
                    GROUP BY id_booking
                ) AS complete_statuses', 'complete_statuses.id_booking = bookings.id', 'left')
            ->group_start()
            ->where('bookings.rating', 0)
            ->where('DATE(bookings.created_at)>=', '2019-09-01');

        if (!empty($branchId)) {
            $bookings->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $bookings->where('bookings.id_customer', $customerId);
        }

        $bookings->group_end();

        if (!empty($filters)) {
            if (key_exists('outstanding', $filters) && $filters['outstanding']) {
                $bookings->or_where('bookings.rated_at>', date('Y-m-d H:i:s', strtotime('-1 day')));
            }
        }

        $bookings->having('complete_statuses.completed_date IS NOT NULL');

        return $bookings->get()->result_array();
    }

    /**
     * Get bookings by booking type.
     * @param $bookingTypeId
     * @param bool $withTrashed
     * @return mixed
     */
    public function getBookingsByBookingType($bookingTypeId, $withTrashed = false)
    {
        $bookings = $this->getBaseQuery()->where('bookings.id_booking_type', $bookingTypeId);

        if (!$withTrashed) {
            $bookings->where('bookings.is_deleted', false);
        }

        return $bookings->get()->result_array();
    }

    /**
     * Get bookings by upload Id.
     * @param $uploadId
     * @param bool $withTrashed
     * @return mixed
     */
    public function getBookingsByUploadId($uploadId, $withTrashed = false)
    {
        $bookings = $this->getBaseQuery()->where('bookings.id_upload', $uploadId);

        if (!$withTrashed) {
            $bookings->where('bookings.is_deleted', false);
        }

        return $bookings->get()->row_array();
    }

    /**
     * Get booking out by booking in.
     * @param $bookingInId
     * @param bool $withTrashed
     * @return array
     */
    public function getBookingOutByBookingIn($bookingInId, $withTrashed = false)
    {
        $bookings = $this->getBaseQuery()
            ->join('booking_references', 'booking_references.id_booking = bookings.id')
            ->where('booking_references.id_booking_reference', $bookingInId);

        if (!$withTrashed) {
            $bookings->where('bookings.is_deleted', false);
        }

        return $bookings->get()->result_array();
    }

    /**
     * Get booking extension
     * @param $fieldName
     * @param $value
     * @param null $containerId
     * @param bool $withTrashed
     * @return mixed
     */
    public function getBookingsByExtensionField($fieldName = '', $value = '', $containerId = null, $withTrashed = false)
    {
        $bookings = $this->getBaseQuery()
            ->select('booking_extensions.value AS extension_value')
            ->join('booking_extensions', 'booking_extensions.id_booking = bookings.id')
            ->join('ref_extension_fields', 'booking_extensions.id_extension_field = ref_extension_fields.id')
            ->group_by('booking_extensions.value');

        if (!empty($fieldName) && !empty($value)) {
            $bookings
                ->where('ref_extension_fields.field_name', $fieldName)
                ->where('booking_extensions.value', $value);
        }

        if (!empty($containerId)) {
            $bookings->join('booking_containers', 'bookings.id = booking_containers.id_booking')
                ->where('booking_containers.id_container', $containerId);
        }

        if (!$withTrashed) {
            $bookings->where('bookings.is_deleted', false);
        }

        return $bookings->get()->result_array();
    }

    /**
     * Get single booking data by id with or without deleted record.
     * @param $id
     * @param bool $withTrashed
     * @return array
     */
    public function getBookingById($id, $withTrashed = false)
    {
        $booking = $this->getBaseQuery();

        if (!$withTrashed) {
            $booking = $booking->where('bookings.is_deleted', false);
        }

        if (is_array($id)) {
            $booking->where_in('bookings.id', $id);
            return $booking->get()->result_array();
        } else {
            $booking->where('bookings.id', $id);
            return $booking->get()->row_array();
        }
    }

    /**
     * Get booking data by booking number.
     * @param $bookingNo
     * @param bool $withTrashed
     * @return mixed
     */
    public function getBookingByNo($bookingNo, $withTrashed = false)
    {
        $booking = $this->getBaseQuery();

        if (is_array($bookingNo)) {
            $booking->where_in('bookings.no_booking', $bookingNo);
        } else {
            $booking->where('bookings.no_booking', $bookingNo);
        }

        if (!$withTrashed) {
            $booking = $booking->where('bookings.is_deleted', false);
        }

        return is_array($bookingNo) ? $booking->get()->result_array() : $booking->get()->row_array();
    }

    /**
     * Get booking by supplier
     * @param integer $customerId
     * @param null $bookingCategory
     * @param bool $withTrashed
     * @return array
     */
    public function getBookingsByCustomer($customerId, $bookingCategory = null, $withTrashed = false)
    {
        $bookings = $this->getBaseQuery()->where('bookings.id_customer', $customerId);

        if (!$withTrashed) {
            $bookings->where('bookings.is_deleted', false);
        }

        if (!empty($bookingCategory)) {
            $bookings->where('ref_booking_types.category', $bookingCategory);
        }

        return $bookings->get()->result_array();
    }

    /**
     * Get booking by invoice status or data
     * @param $customerId
     * @param $type
     * @param bool $withTrashed
     * @return mixed
     */
    public function getBookingsByUnpublishedInvoice($customerId, $type, $withTrashed = false)
    {
        $bookings = $this->getBaseQuery()->distinct()
            ->join('(SELECT * FROM invoices WHERE is_deleted = 0 AND status = "PUBLISHED") AS invoices', 'invoices.no_reference = bookings.no_booking', 'left')
            ->where('invoices.id IS NULL')
            ->where([
                'bookings.id_customer' => $customerId,
                'ref_booking_types.category' => $type
            ]);

        if (!$withTrashed) {
            $bookings->where('bookings.is_deleted', false);
        }

        return $bookings->get()->result_array();
    }

    /**
     * Get booking by conditions.
     * @param $conditions
     * @return mixed
     */
    public function getBookingsByConditions($conditions)
    {
        $bookings = $this->getBaseQuery();

        foreach ($conditions as $key => $value) {
            if (is_array($value)) {
                $bookings->where_in($key, $value);
            } else {
                $bookings->where($key, $value);
            }
        }

        return $bookings->where('bookings.is_deleted', false)->get()->result_array();
    }

    /**
     * Get outstanding outbound SPPB.
     *
     * @param bool $stockBookingExist
     * @return array
     */
    public function getOutstandingOutboundSPPB($stockBookingExist = true)
    {
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $baseQuery = $this->db
            ->select([
                'bookings.id',
                'bookings.id_customer',
                'ref_people.name AS customer_name',
                'bookings.no_booking',
                'bookings.no_reference',
                'ref_branches.branch',
                'ref_branches.id AS id_branch',
                'doc_sppb.sppb_upload_date',
                'IFNULL(booking_containers.stock_booking_containers, 0) AS stock_booking_containers',
                'IFNULL(booking_goods.stock_booking_goods, 0) AS stock_booking_goods',
            ])
            ->from('bookings')
            ->join('ref_people', 'ref_people.id = bookings.id_customer')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
            ->join('ref_booking_types', 'bookings.id_booking_type = ref_booking_types.id')
            ->join('(
                SELECT uploads.id AS id_upload, upload_documents.created_at AS sppb_upload_date
		        FROM uploads 
		        INNER JOIN upload_documents ON upload_documents.id_upload = uploads.id
                INNER JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                    AND ref_document_types.document_type = "SPPB"
              ) AS doc_sppb', 'doc_sppb.id_upload = bookings.id_upload', 'left')
            ->join("(
                SELECT booking_containers.id_booking, IFNULL(SUM(booking_containers.quantity - IFNULL(work_order_containers.quantity, 0)), 0) AS stock_booking_containers
                FROM (SELECT id_booking, id_container, 1 AS quantity FROM booking_containers) AS booking_containers
                LEFT JOIN (
                    SELECT DISTINCT id_booking, work_order_containers.id_container, 1 AS quantity
                    FROM (SELECT * FROM handlings WHERE id_handling_type = '{$handlingTypeIdOutbound}' AND STATUS != 'REJECT' AND is_deleted = 0) AS handlings
                    INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                    INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                    WHERE work_orders.completed_at IS NOT NULL
                ) AS work_order_containers ON work_order_containers.id_booking = booking_containers.id_booking 
                    AND work_order_containers.id_container = booking_containers.id_container
                GROUP BY booking_containers.id_booking
            ) AS booking_containers", 'booking_containers.id_booking = bookings.id', 'left')
            ->join("(
                SELECT booking_goods.id_booking, IFNULL(SUM(booking_goods.quantity - IFNULL(work_order_goods.quantity, 0)), 0) AS stock_booking_goods  
                FROM (SELECT id_booking, id_goods, id_unit, ex_no_container, quantity FROM booking_goods) AS booking_goods
                LEFT JOIN (
                    SELECT id_booking, work_order_goods.id_goods, work_order_goods.id_unit, ex_no_container, SUM(quantity) AS quantity
                    FROM (SELECT * FROM handlings WHERE id_handling_type = '{$handlingTypeIdOutbound}' AND status != 'REJECT' AND is_deleted = 0) AS handlings
                    INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                    INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                    WHERE work_orders.completed_at IS NOT NULL
                    GROUP BY id_booking, id_goods, id_unit, ex_no_container
                ) AS work_order_goods ON work_order_goods.id_booking = booking_goods.id_booking 
                    AND work_order_goods.id_goods = booking_goods.id_goods
                        AND work_order_goods.id_unit = booking_goods.id_unit
                            AND IFNULL(work_order_goods.ex_no_container, '') = IFNULL(booking_goods.ex_no_container, '')
                GROUP BY booking_goods.id_booking
            ) AS booking_goods", 'booking_goods.id_booking = bookings.id', 'left')
            ->where([
                'ref_booking_types.category' => 'OUTBOUND',
                '(sppb_upload_date IS NOT NULL OR sppb_upload_date = "0000-00-00")' => null,
                'bookings.status !=' => 'COMPLETED',
                'bookings.is_deleted' => false,
                'NOT EXISTS (
                    SELECT id, id_upload, no_document, document_date 
                    FROM upload_documents 
                    WHERE id_document_type = 47 
                    AND is_deleted = FALSE
                    AND upload_documents.id_upload = bookings.id_upload
                )' => null, // not have SPPD
            ])
            //->having('DATE(sppb_upload_date) > "' . date('Y-m-d', strtotime('-30 day')) . '"')
            ->group_by('bookings.id, doc_sppb.sppb_upload_date')
            ->having('sppb_upload_date>="2020-01-01"');

        if ($stockBookingExist) {
            $baseQuery->having('(stock_booking_containers > 0 OR stock_booking_goods > 0)');
        } else {
            $baseQuery->having('DATE(sppb_upload_date) < "' . date('Y-m-d', strtotime('-14 day')) . '"');
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get difference booking data with job detail.
     *
     * @param $filters
     * @return array
     */
    public function getDifferenceBookingWorkOrderContainer($filters = [])
    {
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $baseQuery = $this->db
            ->select([
                'booking_containers.id_booking',
                'bookings.no_reference',
                'ref_containers.no_container',
                'ref_containers.size',
                'ref_containers.type',
                '(booking_containers.quantity - IFNULL(work_order_containers.quantity, 0)) AS quantity',
            ])
            ->from('bookings')
            ->join('(
                SELECT id_booking, id_container, 1 AS quantity 
                FROM booking_containers
            ) AS booking_containers', 'bookings.id = booking_containers.id_booking')
            ->join('ref_containers', 'ref_containers.id = booking_containers.id_container')
            ->join("(
                SELECT DISTINCT id_booking, work_order_containers.id_container, 1 AS quantity
                FROM (SELECT * FROM handlings WHERE id_handling_type = '{$handlingTypeIdOutbound}' AND STATUS != 'REJECT' AND is_deleted = 0) AS handlings
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                WHERE work_orders.completed_at IS NOT NULL
            ) AS work_order_containers", 'work_order_containers.id_booking = booking_containers.id_booking 
                AND work_order_containers.id_container = booking_containers.id_container', 'left')
            ->group_by('booking_containers.id_booking, booking_containers.id_container')
            ->having('quantity > 0');

        if(!empty($filters)) {
            if (key_exists('bookings', $filters) && !empty($filters['bookings'])) {
                $baseQuery->where_in('bookings.id', $filters['bookings']);
            }

            if (key_exists('customers', $filters) && !empty($filters['customers'])) {
                $baseQuery->where_in('bookings.id_customer', $filters['customers']);
            }
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get difference booking data with job detail.
     *
     * @param array $filters
     * @return array
     */
    public function getDifferenceBookingWorkOrderGoods($filters = [])
    {
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $baseQuery = $this->db
            ->select([
                'booking_goods.id_booking',
                'bookings.no_reference',
                'ref_goods.name AS goods_name',
                'ref_goods.no_goods',
                '(booking_goods.quantity - IFNULL(work_order_goods.quantity, 0)) AS quantity',
                'ref_units.unit',
            ])
            ->from('bookings')
            ->join('booking_goods', 'bookings.id = booking_goods.id_booking')
            ->join('ref_goods', 'ref_goods.id = booking_goods.id_goods')
            ->join('ref_units', 'ref_units.id = booking_goods.id_unit')
            ->join("(
                SELECT id_booking, work_order_goods.id_goods, work_order_goods.id_unit, SUM(quantity) AS quantity
                FROM (SELECT * FROM handlings WHERE id_handling_type = '{$handlingTypeIdOutbound}' AND STATUS != 'REJECT' AND is_deleted = 0) AS handlings
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE work_orders.completed_at IS NOT NULL
                GROUP BY id_booking, id_goods, id_unit
            ) AS work_order_goods", 'work_order_goods.id_booking = booking_goods.id_booking 
                AND work_order_goods.id_goods = booking_goods.id_goods
                    AND work_order_goods.id_unit = booking_goods.id_unit', 'left')
            ->group_by('booking_goods.id_booking, booking_goods.id_goods, booking_goods.id_unit')
            ->having('quantity > 0');

        if(!empty($filters)) {
            if (key_exists('bookings', $filters) && !empty($filters['bookings'])) {
                $baseQuery->where_in('bookings.id', $filters['bookings']);
            }

            if (key_exists('customers', $filters) && !empty($filters['customers'])) {
                $baseQuery->where_in('bookings.id_customer', $filters['customers']);
            }
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get bookings that available in stock.
     * @param $customerId
     * @param null $bookingInId
     * @return array
     */
    public function getBookingStocksByCustomer($customerId, $bookingInId = null)
    {
        $this->load->model('ReportModel', 'reportModel');
        $this->load->model('ReportStockModel', 'reportStock');
        $this->load->model('BookingModel', 'booking');

        $reportContainers = $this->reportStock->getStockContainers([
            'data' => 'stock',
            'owner' => $customerId,
            'booking' => $bookingInId,
        ]);

        $reportGoods = $this->reportStock->getStockGoods([
            'data' => 'stock',
            'owner' => $customerId,
            'booking' => $bookingInId,
        ]);

        $cBooking = [];
        if (!empty($reportContainers)) {
            $cBooking = array_column($reportContainers, 'id_booking');
        }

        $gBooking = [];
        if (!empty($reportGoods)) {
            $gBooking = array_column($reportGoods, 'id_booking');
        }

        $bookings = array_merge($cBooking, $gBooking);
        $bookingsUniqueId = array_unique($bookings);
        if (!empty($bookingsUniqueId)) {
            $bookings = $this->booking->getBookingsByConditions([
                'bookings.id' => $bookingsUniqueId,
            ]);
            return $bookings;
        }
        return [];
    }

    /**
     * Get diff data booking to job.
     *
     * @param $bookingId
     * @return array
     */
    public function getDifferenceBookingToWorkOrder($bookingId)
    {
        $booking = $this->getBookingById($bookingId);

        $baseQuery = $this->db
            ->select([
                'bookings.id',
                'bookings.id_customer',
                'ref_people.name AS customer_name',
                'bookings.no_booking',
                'bookings.no_reference',
                'ref_branches.branch',
                'IFNULL(booking_containers.stock_booking_containers, 0) AS stock_booking_containers',
                'IFNULL(booking_goods.stock_booking_goods, 0) AS stock_booking_goods',
            ])
            ->from('bookings')
            ->join('ref_people', 'ref_people.id = bookings.id_customer')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
            ->join('ref_booking_types', 'bookings.id_booking_type = ref_booking_types.id')
            ->join("(
                SELECT booking_containers.id_booking, IFNULL(SUM(booking_containers.quantity - IFNULL(work_order_containers.quantity, 0)), 0) AS stock_booking_containers 
                FROM (SELECT id_booking, id_container, 1 AS quantity FROM booking_containers WHERE id_booking = {$bookingId}) AS booking_containers
                LEFT JOIN (
                    SELECT handlings.id_booking, work_order_containers.id_container, 1 AS quantity 
                    FROM handlings 
                    LEFT JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                    LEFT JOIN work_orders ON work_orders.id_handling = handlings.id
                    LEFT JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id 
                    WHERE ref_handling_types.multiplier_container = " . ($booking['category'] == 'INBOUND' ? '1' : '-1') . "
                        AND handlings.status = 'APPROVED' 
                        AND handlings.is_deleted = 0 
                        AND handlings.id_booking = {$bookingId} 
                        AND work_orders.is_deleted = 0
                ) AS work_order_containers 
                    ON work_order_containers.id_booking = booking_containers.id_booking 
                        AND work_order_containers.id_container = booking_containers.id_container
                GROUP BY booking_containers.id_booking
            ) AS booking_containers", 'booking_containers.id_booking = bookings.id', 'left')
            ->join("(
                SELECT booking_goods.id_booking, IFNULL(SUM(booking_goods.quantity - IFNULL(work_order_goods.quantity, 0)), 0) AS stock_booking_goods  
                FROM (SELECT id_booking, id_goods, id_unit, quantity FROM booking_goods WHERE id_booking = {$bookingId}) AS booking_goods
                LEFT JOIN (
                    SELECT handlings.id_booking, work_order_goods.id_goods, work_order_goods.id_unit, work_order_goods.quantity 
                    FROM handlings 
                    LEFT JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                    LEFT JOIN work_orders ON work_orders.id_handling = handlings.id
                    LEFT JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id 
                    WHERE ref_handling_types.multiplier_goods = " . ($booking['category'] == 'INBOUND' ? '1' : '-1') . "
                        AND handlings.status = 'APPROVED' 
                        AND handlings.is_deleted = 0
                        AND handlings.id_booking = {$bookingId} 
                        AND work_orders.is_deleted = 0
                ) AS work_order_goods 
                    ON work_order_goods.id_booking = booking_goods.id_booking
                        AND work_order_goods.id_goods = booking_goods.id_goods
                            AND work_order_goods.id_unit = booking_goods.id_unit
                GROUP BY booking_goods.id_booking
            ) AS booking_goods", 'booking_goods.id_booking = bookings.id', 'left')
            ->where('bookings.id', $bookingId)
            ->group_by('bookings.id');

        return $baseQuery->get()->row_array();
    }

    /**
     * Get booking data by keyword.
     *
     * @param $type
     * @param $search
     * @param null $page
     * @param string $owner
     * @param bool $withTrashed
     * @return mixed
     */
    public function getBookingByKeyword($type, $search, $page = null, $owner = '', $withTrashed = false)
    {
        $branchId = get_active_branch_id();
        $personId = UserModel::authenticatedUserData('id_person', '0');

        $this->db->start_cache();

        $booking = $this->db->select('bookings.*, ref_people.name AS customer_name')->distinct()
            ->from($this->table)
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type')
            ->join('ref_people', 'ref_people.id = bookings.id_customer');

        if (!empty($type) && $type != '_all') {
            $booking->where('ref_booking_types.category', $type);
        }

        if (!empty($owner)) {
            if (strtolower($owner) == 'tps') {
                $booking
                    ->join('safe_conducts', 'safe_conducts.id_booking = bookings.id')
                    ->where('safe_conducts.id_source_warehouse', $personId);
            }
            if (strtolower($owner) == 'customer') {
                $booking->where('bookings.id_customer', $personId);
            }
        }

        if (!empty($branchId) && $type != '_all') {
            $booking->where('bookings.id_branch', $branchId);
        }

        $booking->group_start();
        if (is_array($search)) {
            $booking->where_in('no_booking', $search);
        } else {
            $booking->like('no_booking', trim($search))
                ->or_like('ref_people.name', trim($search))
                ->or_like('no_reference', trim($search));
        }
        $booking->group_end();

        if (!$withTrashed) {
            $booking->where($this->table . '.is_deleted', false);
        }

        $this->db->stop_cache();

        if (!empty($page) || $page != 0) {
            $bookingTotal = $booking->distinct()->count_all_results();
            $bookingPage = $booking->limit(10, 10 * ($page - 1));
            $bookingData = $bookingPage->distinct()->get()->result_array();

            return [
                'results' => $bookingData,
                'total_count' => $bookingTotal
            ];
        }

        $bookingData = $booking->distinct()->get()->result_array();

        $this->db->flush_cache();

        return $bookingData;
    }

    /**
     * Get auto number for booking.
     * @param string $type
     * @return string
     */
    public function getAutoNumberBooking($type = 'BI')
    {
        $orderData = $this->db->query("
            SELECT CAST(RIGHT(no_booking, 6) AS UNSIGNED) + 1 AS order_number 
            FROM bookings 
            WHERE MONTH(created_at) = MONTH(NOW()) 
			AND YEAR(created_at) = YEAR(NOW())
			AND SUBSTRING(no_booking, 1, 2) = '$type'
            ORDER BY CAST(RIGHT(no_booking, 6) AS UNSIGNED) DESC LIMIT 1
			");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return $type . '/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }

    /**
     * Create new booking with details.
     * @param $data
     * @return bool
     */
    public function createBooking($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update booking data.
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateBooking($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete booking data.
     * @param $id
     * @param bool $softDelete
     * @return bool
     */
    public function deleteBooking($id, $softDelete = true)
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
     * Get difference booking data with job detail.
     *
     * @param array $filters
     * @return array
     */
    public function getGoodsInContainer($filters = [])
    {
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $baseQuery = $this->db
            ->select([
                'booking_goods.id AS id',
                'booking_goods.id_booking',
                'booking_goods.quantity',
                'booking_goods.ex_no_container',
                'booking_goods.unit_weight AS weight',
                'booking_goods.unit_gross_weight',
                'booking_goods.id_unit',
                'booking_goods.unit_length',
                'booking_goods.unit_width',
                'booking_goods.unit_height',
                'booking_goods.unit_volume',
                'booking_goods.unit_width',
                'booking_goods.is_hold',
                'booking_goods.status',
                'booking_goods.status_danger',
                'booking_goods.no_pallet',
                'booking_goods.description',
                'booking_goods.id_goods',
                'ref_positions.id AS id_position',
                'ref_positions.position',
                'bookings.no_reference',
                'ref_goods.name AS goods_name',
                'ref_goods.no_goods',
                '(booking_goods.quantity - IFNULL(work_order_goods.quantity, 0)) AS quantity',
                'ref_units.unit',
            ])
            ->from('bookings')
            ->join('booking_goods', 'bookings.id = booking_goods.id_booking')
            ->join('ref_goods', 'ref_goods.id = booking_goods.id_goods')
            ->join('ref_units', 'ref_units.id = booking_goods.id_unit')
            ->join('ref_positions', 'ref_positions.id = booking_goods.id_position','left')
            ->join('booking_containers', 'booking_containers.id = booking_goods.id_booking_container')
            ->join("(
                SELECT id_booking, work_order_goods.id_goods, work_order_goods.id_unit, SUM(quantity) AS quantity
                FROM (SELECT * FROM handlings WHERE id_handling_type = '{$handlingTypeIdOutbound}' AND STATUS != 'REJECT' AND is_deleted = 0) AS handlings
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE work_orders.completed_at IS NOT NULL
                GROUP BY id_booking, id_goods, id_unit
            ) AS work_order_goods", 'work_order_goods.id_booking = booking_goods.id_booking 
                AND work_order_goods.id_goods = booking_goods.id_goods
                    AND work_order_goods.id_unit = booking_goods.id_unit', 'left')
            ->group_by('booking_goods.id_booking, booking_goods.id_goods, booking_goods.id_unit')
            ->having('quantity > 0');

        if(!empty($filters)) {
            if (key_exists('bookings', $filters) && !empty($filters['bookings'])) {
                $baseQuery->where_in('bookings.id', $filters['bookings']);
            }

            if (key_exists('id_container', $filters) && !empty($filters['id_container'])) {
                $baseQuery->where_in('booking_containers.id_container', $filters['id_container']);
            }
        }

        return $baseQuery->get()->result_array();
    }
    /**
     * Get booking data by booking number.
     * @param $bookingNo
     * @param bool $withTrashed
     * @return mixed
     */
    public function getNoBookingInByBookingId($bookingId, $withTrashed = false)
    {
        $booking = $this->db->select([
            'booking_in.no_booking AS no_booking_in',
            'bookings.no_booking'
        ])
            ->from('bookings')
            ->join('bookings AS booking_in','booking_in.id=bookings.id_booking','left');
        
        if (is_array($bookingId)) {
            $booking->where_in('bookings.id', $bookingId);
        } else {
            $booking->where('bookings.id', $bookingId);
        }

        if (!$withTrashed) {
            $booking = $booking->where('bookings.is_deleted', false);
        }

        return is_array($bookingId) ? $booking->get()->result_array() : $booking->get()->row_array();
    }

    /**
     * Get outstanding outbound SPPB.
     *
     * @param bool $stockBookingExist
     * @return array
     */
    public function getOutstandingInbound($branchId)
    {
        $baseQuery = $this->db
            ->select([
                'bookings.id',
                'bookings.id_customer',
                'ref_people.name AS customer_name',
                'bookings.no_booking',
                'bookings.no_reference',
                'ref_branches.branch',
                'ref_branches.id AS id_branch',
                'doc_sppb.sppb_upload_date',
                'draft_documents.draft_date',
                'draft_documents.type AS type_parties',
                'draft_documents.party AS parties',
                'safe_conducts.min_time_in AS first_in',
                'bookings_party.party AS party',
            ])
            ->from('bookings')
            ->join('ref_people', 'ref_people.id = bookings.id_customer')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
            ->join('ref_booking_types', 'bookings.id_booking_type = ref_booking_types.id')
            ->join('(
                SELECT uploads.id AS id_upload, upload_documents.created_at AS sppb_upload_date
		        FROM uploads 
		        INNER JOIN upload_documents ON upload_documents.id_upload = uploads.id
                INNER JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                    AND ref_document_types.document_type = "SPPB"
              ) AS doc_sppb', 'doc_sppb.id_upload = bookings.id_upload', 'left')
            ->join("(
                SELECT upload_documents.id_upload, upload_documents.created_at as draft_date,
                    GROUP_CONCAT(CONCAT(ROUND(upload_document_parties.party),'x', upload_document_parties.shape) separator ', ') AS party,
                    upload_document_parties.type
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                LEFT JOIN upload_document_parties ON upload_document_parties.id_upload_document = upload_documents.id
                WHERE ref_document_types.document_type LIKE '% Draft' AND upload_documents.is_deleted = false
                GROUP BY upload_documents.id
            ) AS draft_documents", 'draft_documents.id_upload = bookings.id_upload', 'left')
            ->join('(
                SELECT *, MIN(safe_conducts.time_in) AS min_time_in  FROM
                    (SELECT safe_conducts.id_booking, safe_conducts.expedition_type, IF(expedition_type="INTERNAL",  safe_conducts.security_in_date,safe_conducts.security_out_date)AS time_in, safe_conducts.security_in_date, safe_conducts.security_out_date
		        FROM safe_conducts
                WHERE safe_conducts.`is_deleted` = 0) AS safe_conducts
                GROUP BY safe_conducts.id_booking
              ) AS safe_conducts', 'safe_conducts.id_booking = bookings.id', 'left')
            ->join("(
                SELECT 
                    bookings.id,
                    bookings.id_upload,
                    bookings.total_netto,
                    bookings.total_bruto,
                    IF(COUNT(booking_containers.id_booking) = 0, 'LCL', GROUP_CONCAT(booking_containers.party)) AS party,
                    SUM(booking_containers.total) AS total_container,
                    (SELECT COUNT(id) FROM booking_goods WHERE id_booking_container IS NULL AND id_booking = bookings.id) AS total_goods,
                    booking_goods.goods_name
                FROM bookings
                LEFT JOIN (
                    SELECT 
                        id_booking, 
                        ref_containers.size, 
                        COUNT(booking_containers.id) AS total, 
                        CONCAT(COUNT(booking_containers.id), 'x', ref_containers.size) AS party
                    FROM booking_containers
                    INNER JOIN ref_containers ON ref_containers.id = booking_containers.id_container
                    GROUP BY id_booking, size
                ) AS booking_containers ON booking_containers.id_booking = bookings.id
                LEFT JOIN (
                    SELECT id_booking, GROUP_CONCAT(ref_goods.name) AS goods_name 
                    FROM booking_goods
                    INNER JOIN ref_goods ON ref_goods.id = booking_goods.id_goods
                    GROUP BY id_booking
                ) AS booking_goods ON booking_goods.id_booking = bookings.id
                WHERE bookings.is_deleted = FALSE
                GROUP BY bookings.id, bookings.id_upload
            ) AS bookings_party", 'bookings_party.id = bookings.id', 'left')
            ->where([
                'ref_booking_types.category' => 'INBOUND',
                '(sppb_upload_date IS NOT NULL OR sppb_upload_date = "0000-00-00")' => null,
                'bookings.status' => 'APPROVED',
                'bookings.created_at >=' => '2021-01-01',
                'bookings.is_deleted' => false,
                'bookings.id_branch' => $branchId,
            ])
            //->having('DATE(sppb_upload_date) > "' . date('Y-m-d', strtotime('-30 day')) . '"')
            ->group_by('bookings.id, doc_sppb.sppb_upload_date')
            ->having('safe_conducts.min_time_in <= NOW() - INTERVAL 0 DAY');

        return $baseQuery->get()->result_array();
    }


    /**
     * Get outstanding outbound complete.
     *
     * @param array $filters
     * @return array
     */
    public function getOutstandingOutboundComplete($filters = [])
    {
        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $baseQuery = $this->db
            ->select([
                'bookings.id',
                'bookings.id_customer',
                'ref_people.name AS customer_name',
                'bookings.no_booking',
                'bookings.no_reference',
                'ref_branches.branch',
                'ref_branches.id AS id_branch',
                'doc_sppb.sppb_upload_date',
                'IFNULL(booking_containers.stock_booking_containers, 0) AS stock_booking_containers',
                'IFNULL(booking_goods.stock_booking_goods, 0) AS stock_booking_goods',
                '(
                    SELECT MAX(IF(expedition_type="INTERNAL", security_in_date, security_out_date)) AS last_outbound
                    FROM safe_conducts
                    WHERE safe_conducts.id_booking = bookings.id		
                ) AS last_activity_date'
            ])
            ->from('bookings')
            ->join('ref_people', 'ref_people.id = bookings.id_customer')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
            ->join('ref_booking_types', 'bookings.id_booking_type = ref_booking_types.id')
            ->join('(
                SELECT uploads.id AS id_upload, upload_documents.created_at AS sppb_upload_date
		        FROM uploads 
		        INNER JOIN upload_documents ON upload_documents.id_upload = uploads.id
                INNER JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                    AND ref_document_types.document_type = "SPPB"
                    AND upload_documents.is_deleted = false
              ) AS doc_sppb', 'doc_sppb.id_upload = bookings.id_upload', 'left')
            ->join("(
                SELECT booking_containers.id_booking, IFNULL(SUM(booking_containers.quantity - IFNULL(work_order_containers.quantity, 0)), 0) AS stock_booking_containers
                FROM (SELECT DISTINCT id_booking, id_container, 1 AS quantity FROM booking_containers) AS booking_containers
                INNER JOIN bookings ON bookings.id = booking_containers.id_booking
                INNER JOIN ref_booking_types ON ref_booking_types.id = bookings .id_booking_type
                LEFT JOIN (
                    SELECT DISTINCT id_booking, work_order_containers.id_container, 1 AS quantity
                    FROM (SELECT * FROM handlings WHERE id_handling_type = '{$handlingTypeIdOutbound}' AND status = 'APPROVED' AND is_deleted = 0) AS handlings
                    INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                    INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                    WHERE work_orders.status = 'COMPLETED' AND work_orders.is_deleted = false
                ) AS work_order_containers ON work_order_containers.id_booking = booking_containers.id_booking 
                    AND work_order_containers.id_container = booking_containers.id_container
                WHERE ref_booking_types.category = 'OUTBOUND' 
                    AND bookings.status != 'COMPLETED'
                    " . (!empty($branchId) ? "AND bookings.id_branch = {$branchId}" : "") . "
                GROUP BY booking_containers.id_booking
            ) AS booking_containers", 'booking_containers.id_booking = bookings.id', 'left')
            ->join("(
                SELECT booking_goods.id_booking, IFNULL(SUM(booking_goods.quantity - IFNULL(work_order_goods.quantity, 0)), 0) AS stock_booking_goods  
                FROM (SELECT id_booking, id_goods, id_unit, ex_no_container, quantity FROM booking_goods) AS booking_goods
                INNER JOIN bookings ON bookings.id = booking_goods.id_booking
                INNER JOIN ref_booking_types ON ref_booking_types.id = bookings .id_booking_type
                LEFT JOIN (
                    SELECT id_booking, work_order_goods.id_goods, work_order_goods.id_unit, ex_no_container, SUM(quantity) AS quantity
                    FROM (SELECT * FROM handlings WHERE id_handling_type = '{$handlingTypeIdOutbound}' AND status = 'APPROVED' AND is_deleted = 0) AS handlings
                    INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                    INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                    WHERE work_orders.status = 'COMPLETED' AND work_orders.is_deleted = false
                    GROUP BY id_booking, id_goods, id_unit, ex_no_container
                ) AS work_order_goods ON work_order_goods.id_booking = booking_goods.id_booking 
                    AND work_order_goods.id_goods = booking_goods.id_goods
                        AND work_order_goods.id_unit = booking_goods.id_unit
                            AND IFNULL(work_order_goods.ex_no_container, '') = IFNULL(booking_goods.ex_no_container, '')
                WHERE ref_booking_types.category = 'OUTBOUND' 
                    AND bookings.status != 'COMPLETED'
                    " . (!empty($branchId) ? "AND bookings.id_branch = {$branchId}" : "") . "
                GROUP BY booking_goods.id_booking
            ) AS booking_goods", 'booking_goods.id_booking = bookings.id', 'left')
            ->where([
                'ref_booking_types.category' => 'OUTBOUND',
                //'(sppb_upload_date IS NOT NULL OR sppb_upload_date = "0000-00-00")' => null,
                'bookings.status !=' => 'COMPLETED',
                'bookings.created_at >=' => '2021-01-01',
                'bookings.is_deleted' => false,
                'IFNULL(stock_booking_containers, 0)=' => 0,
                'IFNULL(stock_booking_goods, 0)=' => 0,
                '(
                    SELECT MAX(IF(expedition_type="INTERNAL", security_in_date, security_out_date)) AS last_outbound
                    FROM safe_conducts
                    WHERE safe_conducts.id_booking = bookings.id		
                ) <= (NOW() - INTERVAL 2 DAY)' => null
            ]);

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        return $baseQuery->get()->result_array();
    }
}
