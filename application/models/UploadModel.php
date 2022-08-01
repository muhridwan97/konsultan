<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class UploadModel
 * @property StatusHistoryModel $statusHistory
 */
class UploadModel extends MY_Model
{
    protected $table = 'uploads';

    const STATUS_REJECTED = 'REJECTED';
    const STATUS_NEW = 'NEW';
    const STATUS_ON_PROCESS = 'ON PROCESS';
    const STATUS_HOLD = 'HOLD';
    const STATUS_RELEASED = 'RELEASED';
    const STATUS_BILLING = 'BILLING';
    const STATUS_PAID = 'PAID';
    const STATUS_AP = 'AP';
    const STATUS_CLEARANCE = 'CLEARANCE';

    /**
     * UploadModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('ReportStockModel', 'ReportStock');
        $this->load->model('BookingTypeModel', 'bookingTypeModel');
    }

    /**
     * Get auto number for upload.
     * @param string $type
     * @return string
     */
    public function getAutoNumberUpload($type = 'UP')
    {
        $orderData = $this->db->query("
            SELECT CAST(RIGHT(no_upload, 6) AS UNSIGNED) + 1 AS order_number 
            FROM uploads 
            WHERE MONTH(created_at) = MONTH(NOW()) 
			  AND YEAR(created_at) = YEAR(NOW())
			  AND SUBSTRING(no_upload, 1, 2) = '$type'
            ORDER BY id DESC LIMIT 1
			");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return $type . '/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }

    /**
     * Get active record query builder for all related upload data selection.
     *
     * @param null $branchId
     * @param null $userType
     * @param null $customerId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null, $userType = null, $customerId = null)
    {
        $uploads = $this->db
            ->select([
                'uploads.*',
                'ref_people.name',
                'ref_people.id_parent',
                'prv_users.name as uploader_name',
                'prv_users.id as uploader_id',
                'bookings.id AS id_booking',
                'bookings.no_booking',
                'ref_booking_types.booking_type',
                'ref_booking_types.type',
                'ref_booking_types.category',
                'ref_booking_types.id_document_type',
                'ref_branches.branch AS branch_name',
                'ref_branches.branch_type',
                'IFNULL(SUM(upload_documents.is_deleted = 0), 0) AS total_document',
                'IFNULL(SUM(IF(upload_documents.is_valid = -1 AND upload_documents.is_deleted = 0, 1, 0)), 0) AS total_rejected_document',
                'IFNULL(SUM(IF(upload_documents.is_valid = 1 AND upload_documents.is_deleted = 0, 1, 0)), 0) AS total_valid_document',
                'IFNULL(SUM(IF(upload_documents.is_valid = 0 AND upload_documents.is_deleted = 0, 1, 0)), 0) AS total_invalid_document',
                'IF(SUM(upload_documents.is_valid <= 0 AND upload_documents.is_deleted = 0), 0, 1) AS is_valid_all',
                'main_docs.id AS id_main_document',
                'main_docs.document_type AS main_docs_name',
                'IFNULL(SUM(DISTINCT IF(upload_item_photos.status = "VALIDATED" AND upload_item_photos.is_deleted = 0, 1, 0)), 0) AS total_valid_photo',
                'IF(SUM(DISTINCT upload_item_photos.status = "ON REVIEW" AND upload_item_photos.is_deleted = 0), 0, 1) AS is_valid_all_photo'                
            ])
            ->from('uploads')
            ->join('ref_people', 'uploads.id_person = ref_people.id', 'left')
            ->join('upload_documents', 'upload_documents.id_upload = uploads.id', 'left')
            ->join('(SELECT * FROM bookings WHERE is_deleted = false) AS bookings', 'bookings.id_upload = uploads.id', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join(UserModel::$tableUser, 'uploads.created_by = prv_users.id', 'left')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
            ->join('ref_document_types AS main_docs', 'main_docs.id = ref_booking_types.id_document_type', 'left')
            ->join('upload_item_photos', 'upload_item_photos.id_upload = uploads.id', 'left')
            ->group_by(['uploads.id', 'bookings.id', 'bookings.no_booking', 'ref_booking_types.booking_type']);

        if ($userType == 'EXTERNAL') {
            $uploads->where('uploads.id_person', $customerId);
        }

        if (!empty($branchId)) {
            $uploads->where('uploads.id_branch', $branchId);
        }

        return $uploads;
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
        $baseQuery = $this->db
            ->select([
                'uploads.id',
                'uploads.id_branch',
                'ref_branches.branch AS branch_name',
                'ref_branches.branch_type',
                'uploads.no_upload',
                'uploads.description',
                'uploads.is_hold',
                'uploads.status',
                'uploads.is_valid',
                'uploads.id_person',
                'ref_people.id_parent',
                'ref_people.name',
                'bookings.id AS id_booking',
                'bookings.no_booking',
                'ref_booking_types.id_document_type',
                'ref_booking_types.booking_type',
                'ref_booking_types.category',
                '(SELECT COUNT(id) FROM upload_documents WHERE id_upload = uploads.id AND is_deleted = FALSE) AS total_document',
                '(SELECT COUNT(id) FROM upload_documents WHERE id_upload = uploads.id AND is_deleted = FALSE AND is_valid = 1) AS total_valid_document',
                'IF(
                    (SELECT COUNT(id) FROM upload_documents WHERE id_upload = uploads.id AND is_deleted = FALSE AND is_valid <= 0) = 0, 
                    1, 0
                ) AS is_valid_all',
                "(SELECT COUNT(id) FROM upload_item_photos WHERE id_upload = uploads.id AND is_deleted = FALSE AND status = 'VALIDATED') AS total_valid_photo",
                "IF(
                    (SELECT COUNT(id) FROM upload_item_photos WHERE id_upload = uploads.id AND is_deleted = FALSE AND status = 'ON REVIEW') = 0, 
                    1, 0
                ) AS is_valid_all_photo",
                'uploads.created_at'
            ])
            ->from($this->table)
            ->join('ref_people', 'uploads.id_person = ref_people.id', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
            ->join('(SELECT * FROM bookings WHERE is_deleted = false) AS bookings', 'bookings.id_upload = uploads.id', 'left');

        if ($userType == 'EXTERNAL') {
            $baseQuery->where('uploads.id_person', $customerId);
        }

        if (!empty($branchId)) {
            $baseQuery->where('uploads.id_branch', $branchId);
        }

        return $baseQuery;
    }

    /**
     * Get all uploaded header data.
     *
     * @param array $filters
     * @param bool $withTrashed
     * @return array
     */
    public function getAll($filters = [], $withTrashed = false)
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');
        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $columns = [
            "uploads.id",
            "ref_booking_types.booking_type",
            "bookings.no_booking",
            "uploads.no_upload",
            "uploads.description",
            "ref_people.name",
            "is_valid",
            "is_valid_all",
            "uploads.id",
        ];
        $columnSort = $columns[$column];

        $this->db->start_cache();

        //$baseQuery = $this->getBaseQuery($branchId, $userType, $customerId);
        $baseQuery = $this->getBaseQueryIndex($branchId, $userType, $customerId);

        if (!empty($search)) {
            $baseQuery->group_start();
            $baseQuery
                ->or_like('ref_booking_types.booking_type', trim($search))
                ->or_like('bookings.no_booking', trim($search))
                ->or_like('uploads.no_upload', trim($search))
                ->or_like('uploads.description', trim($search))
                ->or_like('ref_people.name', trim($search))
                ->or_like('uploads.no_upload', trim($search))
                ->or_like('uploads.status', trim($search));
            if (!key_exists('document_type_query', $filters) || empty($filters['document_type_query'])) {
                // $baseQuery->or_like('upload_documents.no_document', trim($search));

                // optimized sub searching, compatible both getBaseQueryIndex() and getBaseQuery()
                $baseQuery->or_where("EXISTS (
                    SELECT id FROM upload_documents
                    WHERE no_document LIKE '%" . trim($search) . "%'
                        AND id_upload = uploads.id
                )");
            }
            $baseQuery->group_end();
        }

        if (!$withTrashed) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if (key_exists('dashboard_status', $filters)) {
            // put additional join to keep index data clean
            $baseQuery
                ->select([
                    'main_docs.id AS id_main_document',
                    'main_docs.document_type AS main_docs_name',
                ])
                ->join('ref_document_types AS main_docs', 'main_docs.id = ref_booking_types.id_document_type', 'left')
                ->where('ref_branches.dashboard_status', $filters['dashboard_status']);
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->where('uploads.id_person', $filters['customer']);
        }

        if (key_exists('booking_type', $filters) && !empty($filters['booking_type'])) {
            $baseQuery->where('uploads.id_booking_type', $filters['booking_type']);
        }

        if (key_exists('document_type', $filters) && !empty($filters['document_type'])) {
            // $baseQuery->where('upload_documents.id_document_type', $filters['document_type']);

            // optimized sub searching, compatible both getBaseQueryIndex() and getBaseQuery()
            $baseQuery->where("EXISTS (
                SELECT id FROM upload_documents
                WHERE id_upload = uploads.id AND id_document_type = '{$filters['document_type']}'
            )");
        }

        if (key_exists('document_type_query', $filters) && !empty($filters['document_type_query'])) {
            /*
            $baseQuery->group_start();
            $baseQuery
                ->or_like('upload_documents.no_document', trim($filters['document_type_query']))
                ->or_like('upload_documents.document_date', if_empty(format_date(trim($filters['document_type_query'])), trim($filters['document_type_query'])))
                ->or_like('upload_documents.description', trim($filters['document_type_query']));
            $baseQuery->group_end();
            */

            // optimized sub searching, compatible both getBaseQueryIndex() and getBaseQuery()
            $documentQuery = trim($filters['document_type_query']);
            $baseQuery->where("EXISTS (
                SELECT id FROM upload_documents
                WHERE id_upload = uploads.id
                    AND (
                        no_document LIKE '%{$documentQuery}%'
                        OR document_date LIKE '%" . if_empty(format_date($documentQuery), $documentQuery) . "%'
                        OR description LIKE '%{$documentQuery}%'
                    )
            )");
        }

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $baseQuery->where('DATE(uploads.created_at)>=', format_date($filters['date_from']));
        }

        if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
            $baseQuery->where('DATE(uploads.created_at)<=', format_date($filters['date_to']));
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
     * Get uploads by customer.
     * @param $bookingTypeId
     * @param null $customerId
     * @param bool $withTrashed
     * @return mixed
     */
    public function getUploadsInByCustomer($customerId, $withTrashed = false)
    {

        $branchId = get_active_branch('id');
        $uploads = $this->getBaseQuery($branchId)->where('ref_booking_types.category', "INBOUND")->where('uploads.id_person', $customerId);

        if (!$withTrashed) {
            $uploads->where('uploads.is_deleted', false);
        }

        return $uploads->get()->result_array();
    }

    /**
     * Get uploads by booking type.
     * @param $filters
     * @param bool $withTrashed
     * @return mixed
     */
    public function getUploadsByBookingType($filters, $withTrashed = false)
    {
        $bookingTypeId = get_if_exist($filters, 'id_booking_type');
        $withDo = get_if_exist($filters, 'with_do');
        $customerId = get_if_exist($filters, 'id_customer');
        $supplierId = get_if_exist($filters, 'id_supplier');
        $availableOnly = get_if_exist($filters, 'available_only', false);
        $except = get_if_exist($filters, 'except');
        $category = get_if_exist($filters, 'category');
        $branchId = get_active_branch('id');

        $uploads = $this->getBaseQuery($branchId);

        if (!empty($bookingTypeId)) {
            $uploads->where('uploads.id_booking_type', $bookingTypeId);
        }

        if (!empty($withDo)) {
            $this->load->model('DocumentTypeModel');
            $uploads->where('ref_booking_types.with_do', $withDo);
            $uploads->join('(
                SELECT id_upload, MAX(upload_documents.id) AS latest_do_id FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE document_type = "' . DocumentTypeModel::DOC_DO . '" AND upload_documents.is_deleted = false
                GROUP BY id_upload
            ) AS has_do', 'has_do.id_upload = uploads.id', 'left');
            $uploads->where('has_do.latest_do_id IS NULL');
            $uploads->where('DATE(uploads.created_at)>=', '2019-01-01');
        }

        if (!empty($customerId) && !empty($supplierId)) {
            $uploads->group_start();
            $uploads->where('uploads.id_person', $customerId)->or_where('uploads.id_person', $supplierId);
            $uploads->group_end();
        } else if (!empty($customerId) && empty($supplierId)) {
            $uploads->where('uploads.id_person', $customerId);
        } else if (empty($customerId) && !empty($supplierId)) {
            $uploads->where('uploads.id_person', $supplierId);
        }

        if (!empty($category)) {
            $uploads->where('ref_booking_types.category', $category);
        }

        if ($availableOnly) {
            $uploads->where('bookings.no_booking IS NULL');
        }

        if (!empty($except)) {
            $uploads->or_where('uploads.id', $except);
        }

        if (get_if_exist($filters, 'is_valid') && $filters['is_valid']) {
            $uploads->where('uploads.is_valid', $filters['is_valid']);
        }

        if (get_if_exist($filters, 'is_booking_ready') && $filters['is_booking_ready']) {
            $uploads
                ->group_start()
                ->where('uploads.is_valid', true)
                ->or_where_in('uploads.status', [
                    self::STATUS_PAID,
                    self::STATUS_AP,
                    self::STATUS_CLEARANCE,
                ])
                ->group_end();
        }

        if (!$withTrashed) {
            $uploads->where('uploads.is_deleted', false);
        }

        return $uploads->get()->result_array();
    }

    /**
     * Get uploads by booking type.
     * @param $getUploadsByBookingId
     * @param null $customerId
     * @param null $supplierId
     * @param bool $availableOnly for booking
     * @param null $except
     * @param bool $withTrashed
     * @return mixed
     */
    public function getUploadsByBookingId($bookingId, $customerId = null, $supplierId = null, $availableOnly = false, $except = null, $withTrashed = false)
    {
        $uploads = $this->getBaseQuery()->where('bookings.id', $bookingId);

        if (!empty($customerId) && !empty($supplierId)) {
            $uploads->group_start();
            $uploads->where('uploads.id_person', $customerId)->or_where('uploads.id_person', $supplierId);
            $uploads->group_end();
        } else if (!empty($customerId) && empty($supplierId)) {
            $uploads->where('uploads.id_person', $customerId);
        } else if (empty($customerId) && !empty($supplierId)) {
            $uploads->where('uploads.id_person', $supplierId);
        }

        if ($availableOnly) {
            $uploads->group_start();
            $uploads->where('bookings.no_booking IS NULL');
            $uploads->group_end();
        }

        if (!empty($except)) {
            $uploads->or_where('uploads.id', $except);
        }

        if (!$withTrashed) {
            $uploads->where('uploads.is_deleted', false);
        }

        return $uploads->get()->row_array();
    }

    /**
     * Get uploaded data by specific user.
     * @param $userId
     * @param null $bookingTypeId
     * @param bool $unAttachedBooking
     * @param bool $validOnly
     * @param bool $readyBooking
     * @param bool $withTrashed
     * @return array
     */
    public function getUploadsByUser($userId = null, $bookingTypeId = null, $unAttachedBooking = false, $validOnly = null, $readyBooking = false, $withTrashed = false)
    {
        $isAdmin = AuthorizationModel::hasRole(ROLE_ADMINISTRATOR) || AuthorizationModel::hasRole(ROLE_OPERATIONAL);

        if (UserModel::authenticatedUserData('id_person') <> $userId) {
            $isAdmin = false;
        }

        $uploads = $this->getBaseQuery();

        if (!is_null($userId) && !$isAdmin) {
            $uploads->where("uploads.id_person", $userId);
        }

        if ($readyBooking) {
            $uploads->where('uploads.is_valid = 1');
        }

        if (!is_null($bookingTypeId)) {
            $uploads->where(['uploads.id_booking_type' => $bookingTypeId]);
        }

        if ($unAttachedBooking) {
            //$uploads->where('bookings.id IS NULL OR (bookings.id IS NOT NULL AND bookings.is_deleted = 1)');

            $uploads->where('bookings.id IS NULL');
        }

        if (!is_null($validOnly)) {
            if ($validOnly) {
                $uploads->having('total_invalid_document', 0);
            } else {
                $uploads->having('total_invalid_document > 0', NULL);
            }
        }

        if (!$withTrashed) {
            $uploads->where('uploads.is_deleted', false);
        }

        return $uploads->get()->result_array();
    }

    /**
     * Get outstanding upload that not booked yet.
     *
     * @return array|array[]
     */
    public function getOutstandingBooking()
    {
        $baseQuery = $this->db
            ->select([
                'uploads.id AS id_upload',
                'ref_branches.id AS id_branch',
                'ref_branches.branch',
                'ref_customers.name AS customer_name',
                'uploads.no_upload',
                'uploads.description',
                'uploads.status AS upload_status',
                'ref_booking_types.category',
                'sppb_documents.no_document AS sppb_number',
                'sppb_documents.created_at AS sppb_date',
                'bookings.id AS id_booking',
                'bookings.no_booking',
                'bookings.no_reference',
                'bookings.status AS booking_status',
                'DATEDIFF(CURDATE(), IFNULL(sppb_documents.created_at, CURDATE())) AS age',
            ])
            ->from($this->table)
            ->join("(
                SELECT 
                    upload_documents.id_upload,
                    upload_documents.no_document,
                    upload_documents.document_date,
                    upload_documents.created_at             
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'SPPB' 
                    AND upload_documents.is_deleted = FALSE  
            ) AS sppb_documents", 'sppb_documents.id_upload = uploads.id', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch')
            ->join('ref_people AS ref_customers', 'ref_customers.id = uploads.id_person')
            ->join('(
                SELECT * FROM bookings WHERE is_deleted = FALSE
            ) AS bookings', 'bookings.id_upload = uploads.id', 'left')
            ->where([
                'uploads.is_deleted' => false,
                'uploads.created_at>=' => '2021-01-01 00:00:00',
                //'uploads.is_valid' => true,
                //'bookings.id IS NULL' => null,
                'ref_branches.tally_check_approval' => 1,
                //'sppb_documents.document_date>=' => '2021-10-01',
            ])
            ->group_start()
                ->where_not_in('bookings.status', ['APPROVED', 'COMPLETED'])
                ->or_where('bookings.id IS NULL', null)
            ->group_end()
            ->group_start()
                ->where('uploads.is_valid', true)
                ->or_where_in('uploads.status', [
                    self::STATUS_PAID,
                    self::STATUS_AP,
                    self::STATUS_CLEARANCE,
                ])
            ->group_end();

        return $baseQuery->get()->result_array();
    }

    /**
     * Search upload by type.
     *
     * @param $type
     * @param $query
     * @return array|null
     */
    public function trackingUploadByType($type, $query)
    {
        $branchId = get_active_branch_id();

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $baseQuery = $this->db
            ->select([
                'uploads.*',
                'ref_booking_types.id_document_type AS id_main_document',
                'MIN(required_documents.is_valid) AS is_valid'
            ])
            ->from($this->table)
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type')
            ->join('ref_booking_document_types', 'ref_booking_document_types.id_booking_type = ref_booking_types.id AND is_required = 1')
            ->join('upload_documents AS required_documents', 'required_documents.id_upload = uploads.id AND required_documents.id_document_type = ref_booking_document_types.id_document_type')
            ->where('ref_booking_types.category', 'INBOUND')
            ->group_by('uploads.id, ref_booking_types.id_document_type');

        if (!empty($branchId)) {
            $baseQuery->where('uploads.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $baseQuery->where('uploads.id_person', $customerId);
        }

        switch ($type) {
            case 'no_reference':
                return $baseQuery
                    ->join('upload_documents', 'upload_documents.id_upload = uploads.id AND upload_documents.id_document_type = ref_booking_types.id_document_type')
                    ->where('TRIM(upload_documents.no_document)', trim($query))
                    ->get()
                    ->row_array();
            case 'BL':
                return $baseQuery
                    ->join('upload_documents', 'upload_documents.id_upload = uploads.id')
                    ->join('ref_document_types', 'ref_document_types.id = upload_documents.id_document_type')
                    ->where('ref_document_types.document_type', 'Bill Of Loading')
                    ->where('TRIM(upload_documents.no_document)', trim($query))
                    ->get()
                    ->row_array();
            case 'invoice':
                return $baseQuery
                    ->join('upload_documents', 'upload_documents.id_upload = uploads.id')
                    ->join('ref_document_types', 'ref_document_types.id = upload_documents.id_document_type')
                    ->where('ref_document_types.document_type', 'Invoice')
                    ->where('TRIM(upload_documents.no_document)', trim($query))
                    ->get()
                    ->row_array();
        }

        return null;
    }

      /**
     * Get container by DO (UNLOAD).
     *
     * @return array
     */
    public function getEmptyContainerReturn($filters = [])
    {
        $branchId = get_active_branch('id');
        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');
        $report = $this->db
            ->select([
                'uploads.id as id_upload',
                'uploads.description as aju',
                'uploadDocuments.expired_date',
                'ref_people.id AS id_owner',
                'ref_people.name AS owner_name',
                'IFNULL(bookings.id_booking, bookings.id) AS id_booking',
                'booking_inbounds.no_reference',
                'ref_containers.id AS id_container',
                'ref_containers.no_container',
                'ref_containers.type AS container_type',
                'ref_containers.size AS container_size',
                'SUM(CAST(work_order_containers.quantity AS SIGNED) * multiplier_container) AS stock',
                'DATEDIFF(CURDATE(), MIN(completed_at)) AS age',
                'MIN(IFNULL(security_out_date, completed_at)) AS inbound_date',
                'MAX(completed_at) AS last_activity_date',
                'MAX(work_order_containers.id) AS id_work_order_container',
            ])
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('uploads', 'uploads.id = bookings.id_upload', 'left')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(bookings.id_booking, bookings.id)')
            ->join("(
                SELECT
                    upload_documents.id_upload, 
                    upload_documents.expired_date as expired_date
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'DO'
                GROUP BY id_upload              
            ) AS uploadDocuments", 'uploadDocuments.id_upload = bookings.id_upload', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container')
            ->join('ref_people', 'ref_people.id = work_order_containers.id_owner')
            ->where([
                'bookings.is_deleted' => false,
                'handlings.status' => 'APPROVED',
                'handlings.is_deleted' => false,
                'work_orders.is_deleted' => false,
                'work_order_containers.is_deleted' => false,
                'ref_handling_types.is_deleted' => false,
                'ref_people.is_deleted' => false,
                'work_orders.status' => 'COMPLETED',
                'uploadDocuments.expired_date !=' => null,
            ])
            ->group_by('ref_people.id, IFNULL(bookings.id_booking, bookings.id), ref_containers.id, ref_containers.type, ref_containers.size');

        $report->having('stock > 0');

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('ref_people.id', $customerId);
        }

        $baseStockQuery = $this->db->get_compiled_select();
        $report = $this->db->select([
            'stocks.*',
            'ref_positions.position',
            'position_blocks.position_blocks',
        ])
            ->from("({$baseStockQuery}) AS stocks")
            ->join("(
                SELECT 
                    id_work_order_container,
                    GROUP_CONCAT(ref_position_blocks.id) AS id_position_blocks,
                    GROUP_CONCAT(ref_position_blocks.position_block) AS position_blocks
                FROM ref_position_blocks
                INNER JOIN work_order_container_positions ON work_order_container_positions.id_position_block = ref_position_blocks.id
                GROUP BY work_order_container_positions.id_work_order_container              
            ) AS position_blocks", 'position_blocks.id_work_order_container = stocks.id_work_order_container', 'left')
            ->join('work_order_containers AS last_work_order_containers', 'last_work_order_containers.id = stocks.id_work_order_container', 'left')
            ->join('ref_positions', 'ref_positions.id = last_work_order_containers.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left')
            ->join('ref_branches', 'ref_branches.id = ref_warehouses.id_branch', 'left')
            ->order_by("expired_date", "ASC");

        $reportData = $report->get()->result_array();

        return $reportData;

    }

    /**
     * Get container without DO (UNLOAD).
     *
     * @return array
     */
    public function getContainerWithoutDO($withTrash = false)
    {
        $containers = $this->db->select([
            'uploads.id as id_upload',
            'uploads.description as aju',
            'work_orders.no_work_order',
            'ref_handling_types.handling_type',
            'ref_containers.no_container',
            'ref_containers.type',
            'ref_containers.size',
            'ref_positions.position',
            'ref_people.name AS owner_name',
            'upload_documents.expired_date',
            'GROUP_CONCAT(DISTINCT work_order_container_positions.id_position_block) AS id_position_blocks',
            'GROUP_CONCAT(DISTINCT ref_position_blocks.position_block) AS position_blocks'
        ])
            ->from($this->table)
            ->join('upload_documents', 'upload_documents.id_upload = uploads.id', 'left')
            ->join('ref_document_types AS main_docs', 'main_docs.id = upload_documents.id_document_type')
            ->join('bookings', 'uploads.id = bookings.id_upload', 'left')
            ->join('handlings', 'bookings.id = handlings.id_booking', 'left')
            ->join('work_orders', 'handlings.id = work_orders.id_handling', 'left')
            ->join('work_order_containers', 'work_orders.id = work_order_containers.id_work_order')
            ->join('work_order_container_positions', 'work_order_container_positions.id_work_order_container = work_order_containers.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container', 'left')
            ->join('ref_positions', 'ref_positions.id = work_order_containers.id_position', 'left')
            ->join('ref_people', 'ref_people.id = work_order_containers.id_owner', 'left')
            ->join('ref_position_blocks', 'ref_position_blocks.id = work_order_container_positions.id_position_block', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE document_type IN("DO")) AS ref_document_types', 'ref_document_types.id = upload_documents.id_document_type', 'left')
            ->where('ref_handling_types.handling_type', "UNLOAD")
            ->group_by('work_order_containers.id')
            ->having('COUNT(ref_document_types.id) = 0')
            ->order_by('work_orders.no_work_order', 'DESC');

        if (!$withTrash) {
            $containers->where('work_order_containers.is_deleted', false);
        }

        return $containers->get()->result_array();

    }

    /**
     * Get monitoring start to review inbound.
     *
     * @return array
     */
    public function getMonitoringStartToReview()
    {
        $uploads = $this->db
            ->select([
                'uploads.id',
                'uploads.no_upload',
                'ref_booking_types.booking_type',
                'ref_people.name AS customer',
                'ref_branches.branch',
                'uploads.description AS no_reference',
                'uploads.created_at AS upload_date',
                'DATEDIFF(CURDATE(), uploads.created_at) AS age'
            ])
            ->from($this->table)
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_document_types AS main_docs', 'main_docs.id = ref_booking_types.id_document_type', 'left')
            ->join('ref_people', 'ref_people.id = uploads.id_person', 'left')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
            ->join('(SELECT * FROM upload_documents WHERE upload_documents.is_deleted = false) AS upload_documents', 'upload_documents.id_upload = uploads.id', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE ref_document_types.document_type = "SPPD") AS data_sppd', 'data_sppd.id = upload_documents.id_document_type', 'left')
            ->join('(
                SELECT *
                FROM upload_documents
                WHERE upload_documents.is_check = false
                AND upload_documents.is_valid = false
                AND upload_documents.is_deleted = false
                GROUP BY id_upload
            ) document_files', 'document_files.id_upload = uploads.id', 'left')
            ->where('ref_booking_types.dashboard_status', true)
            ->where('ref_branches.dashboard_status', true)
            ->where('uploads.is_deleted', false)
            ->where('ref_booking_types.category', "INBOUND")
            ->where('uploads.created_at>=', '2019-05-01')
            ->group_by('no_upload, booking_type, name, branch, uploads.description, uploads.created_at')
            ->having('COUNT(data_sppd.id) = 0') //without sppd
            ->having('COUNT(document_files.id) > 0') // no check n no valid
            ->order_by('age', 'desc');

        return $uploads->get()->result_array();
    }

    /**
     * Get monitoring start to review outbound.
     *
     * @return array
     */
    public function getMonitoringStartToReviewOutbound()
    {
        $uploads = $this->db
            ->select([
                'uploads.no_upload',
                'ref_booking_types.booking_type',
                'ref_people.name AS customer',
                'ref_branches.branch',
                'uploads.description AS no_reference',
                'uploads.created_at AS upload_date',
                'DATEDIFF(CURDATE(), uploads.created_at) AS age'
            ])
            ->from($this->table)
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_document_types AS main_docs', 'main_docs.id = ref_booking_types.id_document_type', 'left')
            ->join('ref_people', 'ref_people.id = uploads.id_person', 'left')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
            ->join('(SELECT * FROM upload_documents WHERE upload_documents.is_deleted = false) AS upload_documents', 'upload_documents.id_upload = uploads.id', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE ref_document_types.document_type = "SPPD") AS data_sppd', 'data_sppd.id = upload_documents.id_document_type', 'left')
            ->join('(
                SELECT *
                FROM upload_documents
                WHERE upload_documents.is_check = false
                AND upload_documents.is_valid = false
                AND upload_documents.is_deleted = false
                GROUP BY id_upload
            ) document_files', 'document_files.id_upload = uploads.id', 'left')
            ->where('ref_booking_types.dashboard_status', true)
            ->where('ref_branches.dashboard_status', true)
            ->where('ref_booking_types.category', "OUTBOUND")
            ->where('uploads.created_at>=', '2019-05-01')
            ->where('uploads.is_deleted', false)
            ->group_by('no_upload, booking_type, name, branch, uploads.description, uploads.created_at')
            ->having('COUNT(data_sppd.id) = 0') //without sppd outbound
            ->having('COUNT(document_files.id) > 0') // no check n no valid
            ->order_by('age', 'desc');

        return $uploads->get()->result_array();
    }

    /**
     * Get monitoring on review to draft inbound.
     *
     * @return array
     */
    public function getMonitoringReviewToDraft()
    {
        $uploads = $this->db
            ->select([
                'uploads.no_upload',
                'ref_booking_types.booking_type',
                'ref_people.name AS customer',
                'ref_branches.branch',
                'uploads.description AS no_reference',
                'uploads.created_at AS upload_date',
                'DATEDIFF(CURDATE(), uploads.created_at) AS age'
            ])
            ->from($this->table)
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_document_types AS main_docs', 'main_docs.id = ref_booking_types.id_document_type', 'left')
            ->join('ref_people', 'ref_people.id = uploads.id_person', 'left')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
             ->join('(SELECT * FROM upload_documents WHERE upload_documents.is_deleted = false) AS upload_documents', 'upload_documents.id_upload = uploads.id', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE document_type IN("BC 1.6 Draft", "BC 2.8 Draft", "BC 2.7 Draft", "SPPD")) AS ref_document_types', 'ref_document_types.id = upload_documents.id_document_type', 'left')
            ->join('(
                SELECT *
                FROM upload_documents
                WHERE upload_documents.is_check = true
                AND upload_documents.is_valid = false
                AND upload_documents.is_deleted = false
                GROUP BY id_upload
            ) document_files', 'document_files.id_upload = uploads.id', 'left')
            ->where('ref_booking_types.dashboard_status', true)
            ->where('ref_branches.dashboard_status', true)
            ->where('uploads.is_deleted', false)
            ->where('ref_booking_types.category', "INBOUND")
            ->where('uploads.created_at>=', '2019-05-01')
            ->group_by('no_upload, booking_type, name, branch, uploads.description, uploads.created_at')
            ->having('COUNT(ref_document_types.id) = 0') //without dokumen draft n sppd
            ->having('COUNT(document_files.id) > 0') // checking n no valid
            ->order_by('age', 'desc');

        return $uploads->get()->result_array();
    }

    /**
     * Get monitoring on review to draft outbound.
     *
     * @return array
     */
    public function getMonitoringReviewToDraftOut()
    {
        $uploads = $this->db
            ->select([
                'uploads.no_upload',
                'ref_booking_types.booking_type',
                'ref_people.name AS customer',
                'ref_branches.branch',
                'uploads.description AS no_reference',
                'uploads.created_at AS upload_date',
                'DATEDIFF(CURDATE(), uploads.created_at) AS age'
            ])
            ->from($this->table)
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_document_types AS main_docs', 'main_docs.id = ref_booking_types.id_document_type', 'left')
            ->join('ref_people', 'ref_people.id = uploads.id_person', 'left')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
            ->join('(SELECT * FROM upload_documents WHERE upload_documents.is_deleted = false) AS upload_documents', 'upload_documents.id_upload = uploads.id', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE document_type IN("BC 1.6 Draft", "BC 2.8 Draft", "BC 2.7 Draft", "SPPD")) AS ref_document_types', 'ref_document_types.id = upload_documents.id_document_type', 'left')
            ->join('(
                SELECT *
                FROM upload_documents
                WHERE upload_documents.is_check = true
                AND upload_documents.is_valid = false
                AND upload_documents.is_deleted = false
                GROUP BY id_upload
            ) document_files', 'document_files.id_upload = uploads.id', 'left')
            ->where('ref_booking_types.dashboard_status', true)
            ->where('ref_branches.dashboard_status', true)
            ->where('uploads.is_deleted', false)
            ->where('uploads.id_upload !=', null)
            ->where('ref_booking_types.category', "OUTBOUND")
            ->where('uploads.created_at>=', '2019-05-01')
            ->group_by('no_upload, booking_type, name, branch, uploads.description, uploads.created_at')
            ->having('COUNT(ref_document_types.id) = 0')
            ->order_by('age', 'desc');

        return $uploads->get()->result_array();
    }

    /**
     * Get monitoring draft to confirm inbound.
     *
     * @return array
     */
    public function getMonitoringDraftToConfirm()
    {
        $uploads = $this->db
            ->select([
                'uploads.no_upload',
                'ref_booking_types.booking_type',
                'ref_booking_types.category',
                'ref_people.name AS customer',
                'ref_branches.branch',
                'uploads.description AS no_reference',
                'document_files.min_date AS draft_date',
                'document_files.revise_date',
                'IF(document_files.revise_date IS NULL, DATEDIFF(CURDATE(), document_files.min_date), DATEDIFF(document_files.revise_date, document_files.min_date)) AS age',
                'DATEDIFF(CURDATE(), document_files.min_date) AS age_from_first_draft',
                'document_files.total_files'
            ])
            ->from($this->table)
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_document_types AS main_docs', 'main_docs.id = ref_booking_types.id_document_type', 'left')
            ->join('ref_people', 'ref_people.id = uploads.id_person', 'left')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
            ->join('(SELECT * FROM upload_documents WHERE upload_documents.is_deleted = false) AS upload_documents', 'upload_documents.id_upload = uploads.id', 'left')
            ->join('upload_document_files', 'upload_document_files.id_upload_document = upload_documents.id', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE ref_document_types.document_type = "SPPD") AS data_sppd', 'data_sppd.id = upload_documents.id_document_type', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE ref_document_types.document_type IN ("BC 1.6 Draft", "BC 2.8 Draft", "BC 2.7 Draft")) AS data_draft', 'data_draft.id = upload_documents.id_document_type', 'left')
            ->join('(
                SELECT id_upload, MIN(upload_document_files.description_date) AS revise_date, MIN(upload_document_files.created_at) AS min_date, COUNT(DISTINCT upload_document_files.id) AS total_files 
                FROM upload_documents
                LEFT JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                LEFT JOIN upload_document_files ON upload_documents.id = upload_document_files.id_upload_document
                WHERE document_type IN("BC 1.6 Draft","BC 2.8 Draft","BC 2.7 Draft")
                AND upload_documents.is_deleted = false
                GROUP BY id_upload
            ) document_files', 'document_files.id_upload = uploads.id', 'left')
            ->join('(
                SELECT id_upload, COUNT(ref_document_types.id) AS has_confirmation 
                FROM upload_documents
                LEFT JOIN (
                    SELECT * FROM ref_document_types 
                    WHERE document_type IN("BC 1.6 Confirmation", "BC 2.8 Confirmation", "BC 2.7 Confirmation")
                ) AS ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE upload_documents.is_deleted = false
                GROUP BY id_upload
            ) AS confirmations', 'confirmations.id_upload = uploads.id AND confirmations.has_confirmation = 0')
            ->where('uploads.created_at>=', '2019-05-01')
            ->where('ref_booking_types.dashboard_status', true)
            ->where('ref_branches.dashboard_status', true)
            ->where('uploads.is_deleted', false)
            ->where('ref_booking_types.category', "INBOUND")
            ->group_by('no_upload, booking_type, name, branch, uploads.description, uploads.created_at')
            ->having('COUNT(data_sppd.id) = 0')
            ->having('COUNT(data_draft.id) > 0')
            ->order_by('no_upload');

        return $uploads->get()->result_array();
    }

    /**
     * Get monitoring draft to confirm outbound.
     *
     * @return array
     */
    public function getMonitoringDraftToConfirmOut()
    {
        $uploads = $this->db
            ->select([
                'uploads.no_upload',
                'ref_booking_types.booking_type',
                'ref_people.name AS customer',
                'ref_branches.branch',
                'uploads.description AS no_reference',
                'document_files.min_date AS draft_date',
                'document_files.revise_date',
                'IF(document_files.revise_date IS NULL, DATEDIFF(CURDATE(), document_files.min_date), DATEDIFF(document_files.revise_date,  document_files.min_date)) AS age',
                'DATEDIFF(CURDATE(), document_files.min_date) AS age_from_first_draft',
                'document_files.total_files'
            ])
            ->from($this->table)
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_document_types AS main_docs', 'main_docs.id = ref_booking_types.id_document_type', 'left')
            ->join('ref_people', 'ref_people.id = uploads.id_person', 'left')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
            ->join('(SELECT * FROM upload_documents WHERE upload_documents.is_deleted = false) AS upload_documents', 'upload_documents.id_upload = uploads.id', 'left')
            ->join('upload_document_files', 'upload_document_files.id_upload_document = upload_documents.id', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE ref_document_types.document_type = "SPPD") AS data_sppd', 'data_sppd.id = upload_documents.id_document_type', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE ref_document_types.document_type IN ("BC 1.6 Draft", "BC 2.8 Draft", "BC 2.7 Draft")) AS data_draft', 'data_draft.id = upload_documents.id_document_type', 'left')
            ->join('(
                SELECT id_upload, MIN(upload_document_files.description_date) AS revise_date, MIN(upload_document_files.created_at) AS min_date, COUNT(DISTINCT upload_document_files.id) AS total_files 
                FROM upload_documents
                LEFT JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                LEFT JOIN upload_document_files ON upload_documents.id = upload_document_files.id_upload_document
                WHERE document_type IN("BC 1.6 Draft","BC 2.8 Draft","BC 2.7 Draft")
                AND upload_documents.is_deleted = false
                GROUP BY id_upload
            ) document_files', 'document_files.id_upload = uploads.id', 'left')
            ->join('(
                SELECT id_upload, COUNT(ref_document_types.id) AS has_confirmation 
                FROM upload_documents
                LEFT JOIN (
                    SELECT * FROM ref_document_types 
                    WHERE document_type IN("BC 1.6 Confirmation", "BC 2.8 Confirmation", "BC 2.7 Confirmation")
                ) AS ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE upload_documents.is_deleted = false
                GROUP BY id_upload
            ) AS confirmations', 'confirmations.id_upload = uploads.id AND confirmations.has_confirmation = 0')
            ->where('ref_booking_types.dashboard_status', true)
            ->where('uploads.created_at>=', '2019-05-01')
            ->where('ref_branches.dashboard_status', true)
            ->where('ref_booking_types.category', "OUTBOUND")
            ->where('uploads.is_deleted', false)
            ->where('uploads.id_upload !=', null)
            ->group_by('no_upload, booking_type, name, branch, uploads.description, uploads.created_at')
            ->having('COUNT(data_sppd.id) = 0')
            ->having('COUNT(data_draft.id) > 0')
            ->order_by('age_from_first_draft DESC, no_upload, draft_date');

        return $uploads->get()->result_array();
    }

    /**
     * Get monitoring confirm to DO Inbound.
     *
     * @return array
     */
    public function getMonitoringConfirmToDO()
    {
        $uploads = $this->db
            ->select([
                'uploads.no_upload',
                'ref_booking_types.booking_type',
                'ref_people.name AS customer',
                'ref_branches.branch',
                'uploads.description AS no_reference',
                'document_files.min_date AS confirm_date',
                'DATEDIFF(CURDATE(), document_files.min_date) AS age'
            ])
            ->from($this->table)
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_document_types AS main_docs', 'main_docs.id = ref_booking_types.id_document_type', 'left')
            ->join('ref_people', 'ref_people.id = uploads.id_person', 'left')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
            ->join('(SELECT * FROM upload_documents WHERE upload_documents.is_deleted = false) AS upload_documents', 'upload_documents.id_upload = uploads.id', 'left')
            ->join('upload_document_files', 'upload_document_files.id_upload_document = upload_documents.id', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE ref_document_types.document_type = "SPPD") AS data_sppd', 'data_sppd.id = upload_documents.id_document_type', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE ref_document_types.document_type IN ("BC 1.6 Confirmation", "BC 2.8 Confirmation", "BC 2.7 Confirmation")) AS data_confirmation', 'data_confirmation.id = upload_documents.id_document_type', 'left')
             ->join('(
                SELECT id_upload, MIN(upload_document_files.created_at) AS min_date
                FROM upload_documents
                LEFT JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                LEFT JOIN upload_document_files ON upload_documents.id = upload_document_files.id_upload_document
                WHERE document_type IN("BC 1.6 Confirmation", "BC 2.8 Confirmation", "BC 2.7 Confirmation")
                AND upload_documents.is_deleted = false
                GROUP BY id_upload
            ) document_files', 'document_files.id_upload = uploads.id', 'left')
            ->join('(
                SELECT id_upload, COUNT(ref_document_types.id) AS has_delv_order 
                FROM upload_documents
                LEFT JOIN (SELECT * FROM ref_document_types WHERE document_type = "DO") AS ref_document_types 
                    ON ref_document_types.id = upload_documents.id_document_type
                WHERE upload_documents.is_deleted = false
                GROUP BY id_upload
            ) AS deliv_order', 'deliv_order.id_upload = uploads.id AND deliv_order.has_delv_order = 0')
            ->where('ref_booking_types.dashboard_status', true)
            ->where('ref_branches.dashboard_status', true)
            ->where('uploads.is_deleted', false)
            ->where('uploads.created_at>=', '2019-05-01')
            ->where('ref_booking_types.category', "INBOUND")
            ->group_by('no_upload, booking_type, name, branch, uploads.description, uploads.created_at')
            ->having('COUNT(data_sppd.id) = 0')
            ->having('COUNT(data_confirmation.id) > 0')
            ->order_by('age', 'desc');
        
        return $uploads->get()->result_array();
    }

    /**
     * Get monitoring Outbound (confirm to SPPD Inbound).
     *
     * @return array
     */
    public function getMonitoringConfirmToSppdOut()
    {
        $uploads = $this->db
            ->select([
                'uploads.no_upload',
                'ref_booking_types.booking_type',
                'ref_people.name AS customer',
                'ref_branches.branch',
                'uploads.description AS no_reference',
                'document_files.min_date AS confirm_date',
                'DATEDIFF(CURDATE(), document_files.min_date) AS age'
            ])
            ->from($this->table)
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_document_types AS main_docs', 'main_docs.id = ref_booking_types.id_document_type', 'left')
            ->join('ref_people', 'ref_people.id = uploads.id_person', 'left')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
            ->join('(SELECT * FROM upload_documents WHERE upload_documents.is_deleted = false) AS upload_documents', 'upload_documents.id_upload = uploads.id', 'left')
            ->join('upload_document_files', 'upload_document_files.id_upload_document = upload_documents.id', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE ref_document_types.document_type = "SPPD") AS data_sppd', 'data_sppd.id = upload_documents.id_document_type', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE ref_document_types.document_type IN ("BC 1.6 Confirmation", "BC 2.8 Confirmation", "BC 2.7 Confirmation")) AS data_confirmation', 'data_confirmation.id = upload_documents.id_document_type', 'left')
            ->join('(
                SELECT id_upload, MIN(upload_document_files.created_at) AS min_date
                FROM upload_documents
                LEFT JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                LEFT JOIN upload_document_files ON upload_documents.id = upload_document_files.id_upload_document
                WHERE document_type IN("BC 1.6 Confirmation", "BC 2.8 Confirmation", "BC 2.7 Confirmation")
                AND upload_documents.is_deleted = false
                GROUP BY id_upload
            ) document_files', 'document_files.id_upload = uploads.id', 'left')
             ->join('(
                SELECT id_upload, COUNT(ref_document_types.id) AS has_sppd_in 
                FROM upload_documents
                LEFT JOIN (SELECT * FROM ref_document_types WHERE document_type = "SPPD") AS ref_document_types 
                    ON ref_document_types.id = upload_documents.id_document_type
                WHERE upload_documents.is_deleted = false
                GROUP BY id_upload
            ) AS sppd_inbound', 'sppd_inbound.id_upload = uploads.id_upload AND sppd_inbound.has_sppd_in = 0')
            ->where('ref_booking_types.dashboard_status', true)
            ->where('uploads.created_at>=', '2019-05-01')
            ->where('ref_branches.dashboard_status', true)
            ->where('ref_booking_types.category', "OUTBOUND")
            ->where('uploads.is_deleted', false)
            ->where('uploads.id_upload !=', null)
            ->group_by('no_upload, booking_type, name, branch, uploads.description, uploads.created_at')
            ->having('COUNT(data_sppd.id) = 0')
            ->having('COUNT(data_confirmation.id) > 0')
            ->order_by('age', 'desc');

        return $uploads->get()->result_array();
    }

    /**
     * Get monitoring confirm or DO to SPPB INBOUND.
     *
     * @return array
     */
    public function getMonitoringConfirmDoToSppb()
    {
        $uploads = $this->db
            ->select([
                'uploads.no_upload',
                'ref_booking_types.booking_type',
                'ref_people.name AS customer',
                'ref_branches.branch',
                'uploads.description AS no_reference',
                'do_confirm.do_confirm_date AS file_date',
                'DATEDIFF(CURDATE(), do_confirm.do_confirm_date) AS age'
            ])
            ->from($this->table)
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_document_types AS main_docs', 'main_docs.id = ref_booking_types.id_document_type', 'left')
            ->join('ref_people', 'ref_people.id = uploads.id_person', 'left')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
            ->join('(SELECT * FROM upload_documents WHERE upload_documents.is_deleted = false) AS upload_documents', 'upload_documents.id_upload = uploads.id', 'left')
            ->join('upload_document_files', 'upload_document_files.id_upload_document = upload_documents.id', 'left')
             ->join('(
                SELECT id_upload, MAX(upload_document_files.created_at) AS do_confirm_date
                FROM upload_documents
                LEFT JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                LEFT JOIN upload_document_files ON upload_documents.id = upload_document_files.id_upload_document
                WHERE document_type IN ("BC 1.6 Confirmation", "BC 2.8 Confirmation", "BC 2.7 Confirmation", "DO")
                AND upload_documents.is_deleted = false
                GROUP BY id_upload
            ) AS do_confirm', 'do_confirm.id_upload = uploads.id', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE ref_document_types.document_type = "SPPD") AS data_sppd', 'data_sppd.id = upload_documents.id_document_type', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE ref_document_types.document_type IN ("BC 1.6 Confirmation", "BC 2.8 Confirmation", "BC 2.7 Confirmation", "DO")) AS data_do_confirm', 'data_do_confirm.id = upload_documents.id_document_type', 'left')
            ->join('(
                SELECT id_upload, COUNT(ref_document_types.id) AS has_confirmation 
                FROM upload_documents
                LEFT JOIN (SELECT * FROM ref_document_types WHERE document_type = "SPPB") AS ref_document_types 
                    ON ref_document_types.id = upload_documents.id_document_type
                WHERE upload_documents.is_deleted = false
                GROUP BY id_upload
            ) AS confirmations', 'confirmations.id_upload = uploads.id AND confirmations.has_confirmation = 0')
            ->where('ref_booking_types.dashboard_status', true)
            ->where('ref_branches.dashboard_status', true)
            ->where('uploads.is_deleted', false)
            ->where('ref_booking_types.category', "INBOUND")
            ->where('uploads.created_at>=', '2019-05-01')
            ->group_by('no_upload, booking_type, name, branch, uploads.description, uploads.created_at')
            ->having('COUNT(data_sppd.id) = 0')
            ->having('COUNT(data_do_confirm.id) > 0')
            ->order_by('uploads.created_at', 'desc');

        return $uploads->get()->result_array();
    }

    /**
     * Get monitoring sppb to sppd inbound.
     *
     * @return array
     */
    public function getMonitoringSppbToSppd()
    {
        $uploads = $this->db
            ->select([
                'uploads.no_upload',
                'ref_booking_types.booking_type',
                'ref_people.name AS customer',
                'ref_branches.branch',
                'uploads.description AS no_reference',
                'document_files.min_date AS sppb_date',
                'DATEDIFF(CURDATE(), document_files.min_date) AS age'
            ])
            ->from($this->table)
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_document_types AS main_docs', 'main_docs.id = ref_booking_types.id_document_type', 'left')
            ->join('ref_people', 'ref_people.id = uploads.id_person', 'left')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
            ->join('(SELECT * FROM upload_documents WHERE upload_documents.is_deleted = false) AS upload_documents', 'upload_documents.id_upload = uploads.id', 'left')
            ->join('upload_document_files', 'upload_document_files.id_upload_document = upload_documents.id', 'left')
            ->join('ref_document_types', 'ref_document_types.id = upload_documents.id_document_type', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE ref_document_types.document_type = "SPPD") AS data_sppd', 'data_sppd.id = upload_documents.id_document_type', 'left')
            ->join('(
                SELECT id_upload, COUNT(ref_document_types.id) AS has_sppb
                FROM upload_documents
                LEFT JOIN (SELECT * FROM ref_document_types WHERE document_type = "SPPD") AS ref_document_types 
                    ON ref_document_types.id = upload_documents.id_document_type
                AND upload_documents.is_deleted = false
                GROUP BY id_upload
            ) AS doc_sppb', 'doc_sppb.id_upload = uploads.id AND doc_sppb.has_sppb = 0')
            ->join('(
                SELECT id_upload, MIN(upload_document_files.created_at) AS min_date
                FROM upload_documents
                LEFT JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                LEFT JOIN upload_document_files ON upload_documents.id = upload_document_files.id_upload_document
                WHERE document_type = "SPPB"
                AND upload_documents.is_deleted = false
                GROUP BY id_upload
            ) document_files', 'document_files.id_upload = uploads.id', 'left')
            ->where('ref_booking_types.dashboard_status', true)
            ->where('ref_booking_types.category', "INBOUND")
            ->where('ref_document_types.document_type', 'SPPB')
            ->where('uploads.created_at>=', '2019-05-01')
            ->where('ref_branches.dashboard_status', true)
            ->where('uploads.is_deleted', false)
            ->group_by('no_upload, booking_type, name, branch, uploads.description, uploads.created_at')
            ->having('COUNT(data_sppd.id) = 0')
            ->order_by('age', 'desc');

        return $uploads->get()->result_array();
    }

    /**
     * Get monitoring sppd (INBOUND) to billing outbound.
     *
     * @return array
     */
    public function getMonitoringSppdToBillingOut() // SPPD IN NOT NULL BUT SPPD OUT NULL
    {
        $uploads = $this->db
            ->select([
                'uploads.no_upload',
                'ref_booking_types.booking_type',
                'ref_people.name AS customer',
                'ref_branches.branch',
                'uploads.description AS no_reference',
                'upload_in.sppd_date',
                'DATEDIFF(CURDATE(), upload_in.sppd_date) AS age'
            ])
            ->from($this->table)
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_document_types AS main_docs', 'main_docs.id = ref_booking_types.id_document_type', 'left')
            ->join('ref_people', 'ref_people.id = uploads.id_person', 'left')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
            ->join('(SELECT * FROM upload_documents WHERE upload_documents.is_deleted = false) AS upload_documents', 'upload_documents.id_upload = uploads.id', 'left')
            ->join('upload_document_files', 'upload_document_files.id_upload_document = upload_documents.id', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE ref_document_types.document_type = "SPPD") AS data_sppd', 'data_sppd.id = upload_documents.id_document_type', 'left')
            ->join('(
                SELECT id_upload, COUNT(ref_document_types.id) AS has_confirmation 
                FROM upload_documents
                LEFT JOIN (SELECT * FROM ref_document_types WHERE document_type = "E Billing") AS ref_document_types 
                    ON ref_document_types.id = upload_documents.id_document_type
                WHERE upload_documents.is_deleted = false
                GROUP BY id_upload
            ) AS confirmations', 'confirmations.id_upload = uploads.id AND confirmations.has_confirmation = 0')
            ->join("(
                SELECT
                    upload_documents.id_upload AS id_uploadIn, 
                    upload_documents.created_at as sppd_date
                FROM upload_documents
                LEFT JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'SPPD'
                AND upload_documents.is_deleted = false
                GROUP BY id_uploadIn              
            ) AS upload_in", 'upload_in.id_uploadIn = uploads.id_upload', 'left')
            ->where('ref_booking_types.dashboard_status', true)
            ->where('ref_branches.dashboard_status', true)
            ->where('uploads.is_deleted', false)
            ->where('ref_booking_types.category', "OUTBOUND")
            ->where('uploads.created_at>=', '2019-05-01')
            ->where('uploads.id_upload !=', null)
            ->where('upload_in.id_uploadIn !=', null)
            ->where('ref_booking_types.booking_type !=', "BC 27 FROM 1.6")
            ->where('ref_booking_types.booking_type !=', "PPB (OUTBOUND)")
            ->group_by('no_upload, booking_type, name, branch, uploads.description, uploads.created_at')
            ->having('COUNT(data_sppd.id) = 0')
            ->order_by('age', 'desc');

        return $uploads->get()->result_array();
    }


    /**
     * Get monitoring billing to Bpn outbound.
     *
     * @return array
     */
    public function getMonitoringBillingToBpnOut()
    {
        $uploads = $this->db
            ->select([
                'uploads.no_upload',
                'ref_booking_types.booking_type',
                'ref_people.name AS customer',
                'ref_branches.branch',
                'uploads.description AS no_reference',
                'document_files.min_date AS billing_date',
                'DATEDIFF(CURDATE(), document_files.min_date) AS age'
            ])
            ->from($this->table)
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_document_types AS main_docs', 'main_docs.id = ref_booking_types.id_document_type', 'left')
            ->join('ref_people', 'ref_people.id = uploads.id_person', 'left')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
            ->join('(SELECT * FROM upload_documents WHERE upload_documents.is_deleted = false) AS upload_documents', 'upload_documents.id_upload = uploads.id', 'left')
            ->join('upload_document_files', 'upload_document_files.id_upload_document = upload_documents.id', 'left')
            ->join('ref_document_types', 'ref_document_types.id = upload_documents.id_document_type', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE ref_document_types.document_type = "SPPD") AS data_sppd', 'data_sppd.id = upload_documents.id_document_type', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE ref_document_types.document_type = "E Billing") AS data_billing', 'data_billing.id = upload_documents.id_document_type', 'left')
            ->join('(
                SELECT id_upload, COUNT(ref_document_types.id) AS has_confirmation 
                FROM upload_documents
                LEFT JOIN (SELECT * FROM ref_document_types WHERE document_type = "BPN (Bukti Penerimaan Negara)") AS ref_document_types 
                    ON ref_document_types.id = upload_documents.id_document_type
                WHERE upload_documents.is_deleted = false
                GROUP BY id_upload
            ) AS confirmations', 'confirmations.id_upload = uploads.id AND confirmations.has_confirmation = 0')
            ->join('(
                SELECT id_upload, MIN(upload_document_files.created_at) AS min_date
                FROM upload_documents
                LEFT JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                LEFT JOIN upload_document_files ON upload_documents.id = upload_document_files.id_upload_document
                WHERE document_type = "E Billing"
                AND upload_documents.is_deleted = false
                GROUP BY id_upload
            ) document_files', 'document_files.id_upload = uploads.id', 'left')
            ->where('ref_booking_types.dashboard_status', true)
            ->where('ref_branches.dashboard_status', true)
            ->where('uploads.is_deleted', false)
            ->where('uploads.id_upload !=', null)
            ->where('ref_booking_types.category', "OUTBOUND")
            ->where('uploads.created_at>=', '2019-05-01')
            ->where('ref_booking_types.booking_type !=', "BC 27 FROM 1.6")
            ->where('ref_booking_types.booking_type !=', "PPB (OUTBOUND)")
            ->group_by('no_upload, booking_type, name, branch, uploads.description, uploads.created_at')
            ->having('COUNT(data_sppd.id) = 0')
            ->having('COUNT(data_billing.id) > 0')
            ->order_by('age', 'desc');

        return $uploads->get()->result_array();
    }

    /**
     * Get monitoring BPN to SPPB outbound.
     *
     * @return array
     */
    public function getMonitoringBpnToSppb()
    {
        $uploads = $this->db
            ->select([
                'uploads.no_upload',
                'ref_booking_types.booking_type',
                'ref_people.name AS customer',
                'ref_branches.branch',
                'uploads.description AS no_reference',
                'document_files.min_date AS bpn_date',
                'DATEDIFF(CURDATE(), document_files.min_date) AS age'
            ])
            ->from($this->table)
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_document_types AS main_docs', 'main_docs.id = ref_booking_types.id_document_type', 'left')
            ->join('ref_people', 'ref_people.id = uploads.id_person', 'left')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
            ->join('(SELECT * FROM upload_documents WHERE upload_documents.is_deleted = false) AS upload_documents', 'upload_documents.id_upload = uploads.id', 'left')
            ->join('upload_document_files', 'upload_document_files.id_upload_document = upload_documents.id', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE ref_document_types.document_type = "SPPD") AS data_sppd', 'data_sppd.id = upload_documents.id_document_type', 'left')
            ->join('(SELECT * FROM ref_document_types WHERE ref_document_types.document_type = "BPN (Bukti Penerimaan Negara)") AS data_bpn', 'data_bpn.id = upload_documents.id_document_type', 'left')
            ->join('(
                SELECT id_upload, COUNT(ref_document_types.id) AS has_confirmation 
                FROM upload_documents
                LEFT JOIN (SELECT * FROM ref_document_types WHERE document_type = "SPPB") AS ref_document_types 
                    ON ref_document_types.id = upload_documents.id_document_type
                WHERE upload_documents.is_deleted = false
                GROUP BY id_upload
            ) AS confirmations', 'confirmations.id_upload = uploads.id AND confirmations.has_confirmation = 0')
            ->join('(
                SELECT id_upload, MIN(upload_document_files.created_at) AS min_date
                FROM upload_documents
                LEFT JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                LEFT JOIN upload_document_files ON upload_documents.id = upload_document_files.id_upload_document
                WHERE document_type = "BPN (Bukti Penerimaan Negara)"
                AND upload_documents.is_deleted = false
                GROUP BY id_upload
            ) document_files', 'document_files.id_upload = uploads.id', 'left')
            ->where('ref_booking_types.dashboard_status', true)
            ->where('ref_branches.dashboard_status', true)
            ->where('uploads.is_deleted', false)
            ->where('uploads.created_at>=', '2019-05-01')
            ->where('ref_booking_types.category', "OUTBOUND")
            ->where('ref_booking_types.booking_type !=', "BC 27 FROM 1.6")
            ->where('ref_booking_types.booking_type !=', "PPB (OUTBOUND)")
            ->group_by('no_upload, booking_type, name, branch, uploads.description, uploads.created_at')
            ->having('COUNT(data_sppd.id) = 0')
            ->having('COUNT(data_bpn.id) > 0')
            ->order_by('age', 'desc');

        return $uploads->get()->result_array();
    }

    /**
     * Get kpi draft revision.
     *
     * @param array $filters
     * @return array
     */
    public function getKpiDraftRevision($filters = [])
    {
        $uploads = $this->db
            ->select([
                'author',
                'uploads.no_upload',
                'ref_branches.branch',
                'uploads.created_at AS upload_date',
                'prev_rev_date',
                'draft_date',
                'revise_date',
                'IF(TIME(IFNULL(prev_rev_date, uploads.created_at)) < "12:00:00", 
                    draft_date <= CONCAT(DATE(IFNULL(prev_rev_date, uploads.created_at)), " 23:59:59"),
                    draft_date <= CONCAT(DATE_ADD(DATE(IFNULL(prev_rev_date, uploads.created_at)), INTERVAL 1 DAY), " 12:00:00")) AS score'
            ])
            ->from($this->table)
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch')
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type')
            ->join('(
                SELECT upload_documents.id_upload, 
                    upload_document_files.created_at AS draft_date, 
                    upload_document_files.description_date AS revise_date,
                    MAX(prev_drafts.prev_rev_date) AS prev_rev_date,
                    prv_users.name AS author
                FROM upload_documents 
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type 
                INNER JOIN upload_document_files ON upload_document_files.id_upload_document = upload_documents.id
                LEFT JOIN ' . UserModel::$tableUser . ' ON prv_users.id = upload_document_files.created_by
                LEFT JOIN (
                    SELECT next_documents.id, id_upload_document, next_documents.description_date AS prev_rev_date 
                    FROM upload_documents AS next_upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = next_upload_documents.id_document_type
                    INNER JOIN upload_document_files AS next_documents ON next_documents.id_upload_document = next_upload_documents.id
                    WHERE document_type IN("BC 1.6 Draft", "BC 2.8 Draft", "BC 2.7 Draft")
                ) AS prev_drafts ON prev_drafts.id < upload_document_files.id AND prev_drafts.id_upload_document = upload_documents.id
                WHERE document_type IN("BC 1.6 Draft", "BC 2.8 Draft", "BC 2.7 Draft")
                GROUP BY upload_document_files.id
            ) AS drafts', 'drafts.id_upload = uploads.id', 'left')
            ->where('ref_booking_types.dashboard_status', true)
            ->where('drafts.draft_date IS NOT NULL')
            ->where('uploads.created_at>=', '2019-05-01')
            ->where('ref_branches.dashboard_status', true)
            ->where('uploads.is_deleted', false);

        $uploads->where('author IS NOT NULL');
        if (key_exists('author', $filters) && !empty($filters['author'])) {
            $uploads->having('author', $filters['author']);
        }
        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            $uploads->having('branch', $filters['branch']);
        }

        if (key_exists('booking_category', $filters) && !empty($filters['booking_category'])) {
            $uploads->where('ref_booking_types.category', $filters['booking_category']);
        }

        if (key_exists('year', $filters) && !empty($filters['year'])) {
            $uploads->where('YEAR(uploads.created_at)', $filters['year']);
        }

        if (key_exists('month', $filters) && !empty($filters['month'])) {
            $uploads->where('MONTH(uploads.created_at)', $filters['month']);
        }

        if (key_exists('summary', $filters) && !empty($filters['summary'])) {
            $baseQuery = $uploads->get_compiled_select();
            return $this->db->query("
                SELECT {$filters['summary_type']}, 
                       IFNULL(SUM(score), 0) AS draft_revision_score, 
                       COUNT(score) AS draft_revision_docs, 
                       (IFNULL(SUM(score), 0) / COUNT(score) * 100) AS draft_revision_percent
                FROM ({$baseQuery}) AS kpi
                GROUP BY {$filters['summary_type']}
            ")->result_array();
        }

        return $uploads->get()->result_array();
    }

    /**
     * Get kpi confirm to sppd
     * @param array $filters
     * @return array
     */
    public function getKpiConfirmSppb($filters = [])
    {
        $uploads = $this->db
            ->select([
                'author',
                'uploads.no_upload',
                'ref_branches.branch',
                'upload_documents.created_at AS confirm_date',
                'confirms.do_date',
                'confirms.max_confirm_do_date',
                'sppb_date',
                'IF(TIME(confirms.max_confirm_do_date) < "12:00:00", 
                    sppb_date <= CONCAT(DATE(confirms.max_confirm_do_date), " 23:59:59"),
                    sppb_date <= CONCAT(DATE_ADD(DATE(confirms.max_confirm_do_date), INTERVAL 1 DAY), " 12:00:00")) AS score'
            ])
            ->from($this->table)
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch')
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type')
            ->join('upload_documents', 'upload_documents.id_upload = uploads.id')
            ->join('ref_document_types', 'ref_document_types.id = upload_documents.id_document_type')
            ->join('upload_document_files', 'upload_document_files.id_upload_document = upload_documents.id')
            ->join('(
                SELECT id_upload, MIN(upload_document_files.created_at) AS sppb_date, prv_users.name AS author
                FROM upload_documents
                LEFT JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                LEFT JOIN upload_document_files ON upload_documents.id = upload_document_files.id_upload_document
                LEFT JOIN ' . UserModel::$tableUser . ' ON prv_users.id = upload_document_files.created_by
                WHERE document_type = "SPPB"
                GROUP BY id_upload
            ) AS sppbs', 'sppbs.id_upload = uploads.id', 'left')
            ->join('(
                SELECT 
                    upload_documents.id_upload, 
                    do_documents.do_date AS do_date,
                    IF(UNIX_TIMESTAMP(upload_documents.created_at) > IF(do_documents.do_date is not null, UNIX_TIMESTAMP(do_documents.do_date), 0), upload_documents.created_at, do_documents.do_date) AS max_confirm_do_date
                FROM upload_documents
                LEFT JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type 
                LEFT JOIN upload_document_files ON upload_document_files.id_upload_document = upload_documents.id
                LEFT JOIN ' . UserModel::$tableUser . ' ON prv_users.id = upload_document_files.created_by
                LEFT JOIN (
                    SELECT 
                        do_upload_documents.id_upload, 
                        do_upload_documents.created_at AS do_date 
                    FROM upload_documents AS do_upload_documents
                    LEFT JOIN ref_document_types ON ref_document_types.id = do_upload_documents.id_document_type
                    WHERE document_type = "DO"
                    GROUP BY do_upload_documents.id_upload 
                ) AS do_documents ON do_documents.id_upload = upload_documents.id_upload
                WHERE document_type IN("BC 1.6 Confirmation", "BC 2.8 Confirmation", "BC 2.7 Confirmation")
                GROUP BY upload_documents.id_upload
            ) AS confirms', 'confirms.id_upload = uploads.id', 'left')
            ->where('ref_booking_types.dashboard_status', true)
            ->where_in('ref_document_types.document_type', ['BC 1.6 Confirmation', 'BC 2.8 Confirmation', 'BC 2.7 Confirmation'])
            ->where('sppbs.sppb_date IS NOT NULL')
            ->where('uploads.created_at>=', '2019-05-01')
            ->where('ref_branches.dashboard_status', true)
            ->where('uploads.is_deleted', false);

        $uploads->where('author IS NOT NULL');
        if (key_exists('author', $filters) && !empty($filters['author'])) {
            $uploads->having('author', $filters['author']);
        }

        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            $uploads->having('branch', $filters['branch']);
        }

        if (key_exists('booking_category', $filters) && !empty($filters['booking_category'])) {
            $uploads->where('ref_booking_types.category', $filters['booking_category']);
        }

        if (key_exists('year', $filters) && !empty($filters['year'])) {
            $uploads->where('YEAR(uploads.created_at)', $filters['year']);
        }

        if (key_exists('month', $filters) && !empty($filters['month'])) {
            $uploads->where('MONTH(uploads.created_at)', $filters['month']);
        }

        if (key_exists('year_confirm', $filters) && !empty($filters['year_confirm'])) {
            $uploads->where('YEAR(upload_documents.created_at)', $filters['year_confirm']);
        }

        if (key_exists('month_confirm', $filters) && !empty($filters['month_confirm'])) {
            $uploads->where('MONTH(upload_documents.created_at)', $filters['month_confirm']);
        }


        if (key_exists('summary', $filters) && !empty($filters['summary'])) {
            $baseQuery = $uploads->get_compiled_select();
            return $this->db->query("
                SELECT {$filters['summary_type']}, 
                       IFNULL(SUM(score), 0) AS confirm_sppb_score, 
                       COUNT(score) AS confirm_sppb_docs, 
                       (IFNULL(SUM(score), 0) / COUNT(score) * 100) AS confirm_sppb_percent
                FROM ({$baseQuery}) AS kpi
                GROUP BY {$filters['summary_type']}
            ")->result_array();
        }

        return $uploads->get()->result_array();
    }

    /**
     * Get kpi sppb to coo receipt
     * @param array $filters
     * @return array
     */
    public function getKpiSppbCooReceipt($filters = [])
    {
        $uploads = $this->db
            ->select([
                'author',
                'uploads.no_upload',
                'ref_branches.branch',
                'upload_document_files.created_at AS sppb_date',
                'coo_receipt_date',
                '(coo_receipt_date <= DATE_ADD(upload_document_files.created_at, INTERVAL 3 DAY)) AS score'
            ])
            ->from($this->table)
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch')
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type')
            ->join('upload_documents', 'upload_documents.id_upload = uploads.id')
            ->join('ref_document_types', 'ref_document_types.id = upload_documents.id_document_type')
            ->join('upload_document_files', 'upload_document_files.id_upload_document = upload_documents.id')
            ->join('(
                SELECT id_upload, MIN(upload_document_files.created_at) AS coo_receipt_date, prv_users.name AS author
                FROM upload_documents
                LEFT JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                LEFT JOIN upload_document_files ON upload_documents.id = upload_document_files.id_upload_document
                LEFT JOIN ' . UserModel::$tableUser . ' ON prv_users.id = upload_document_files.created_by
                WHERE document_type = "COO Receipt"
                GROUP BY id_upload
            ) AS coo_receipts', 'coo_receipts.id_upload = uploads.id', 'left')
            ->where('ref_booking_types.dashboard_status', true)
            ->where_in('ref_document_types.document_type', 'SPPB')
            ->where('coo_receipts.coo_receipt_date IS NOT NULL')
            ->where('uploads.created_at>=', '2019-05-01')
            ->where('ref_branches.dashboard_status', true)
            ->where('uploads.is_deleted', false);

        $uploads->where('author IS NOT NULL');
        if (key_exists('author', $filters) && !empty($filters['author'])) {
            $uploads->having('author', $filters['author']);
        }

        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            $uploads->having('branch', $filters['branch']);
        }

        if (key_exists('booking_category', $filters) && !empty($filters['booking_category'])) {
            $uploads->where('ref_booking_types.category', $filters['booking_category']);
        }

        if (key_exists('year', $filters) && !empty($filters['year'])) {
            $uploads->where('YEAR(uploads.created_at)', $filters['year']);
        }

        if (key_exists('month', $filters) && !empty($filters['month'])) {
            $uploads->where('MONTH(uploads.created_at)', $filters['month']);
        }

        if (key_exists('year_sppb', $filters) && !empty($filters['year_sppb'])) {
            $uploads->where('YEAR(upload_document_files.created_at)', $filters['year_sppb']);
        }

        if (key_exists('month_sppb', $filters) && !empty($filters['month_sppb'])) {
            $uploads->where('MONTH(upload_document_files.created_at)', $filters['month_sppb']);
        }

        if (key_exists('summary', $filters) && !empty($filters['summary'])) {
            $baseQuery = $uploads->get_compiled_select();
            return $this->db->query("
                SELECT {$filters['summary_type']}, 
                       IFNULL(SUM(score), 0) AS sppb_coo_receipt_score, 
                       COUNT(score) AS sppb_coo_receipt_docs, 
                       (IFNULL(SUM(score), 0) / COUNT(score) * 100) AS sppb_coo_receipt_percent
                FROM ({$baseQuery}) AS kpi
                GROUP BY {$filters['summary_type']}
            ")->result_array();
        }

        return $uploads->get()->result_array();
    }

    /**
     * Get kpi completed to coo sppd
     * @param array $filters
     * @return array
     */
    public function getKpiCompleteSppd($filters = [])
    {
        $uploads = $this->db
            ->select([
                'author',
                'uploads.no_upload',
                'ref_branches.branch',
                'MIN(booking_statuses.created_at) AS completed_date',
                'sppd_date',
                'IF(TIME(MIN(booking_statuses.created_at)) < "12:00:00", 
                    sppd_date <= CONCAT(DATE(MIN(booking_statuses.created_at)), " 23:59:59"),
                    sppd_date <= CONCAT(DATE_ADD(DATE(MIN(booking_statuses.created_at)), INTERVAL 1 DAY), " 12:00:00")) AS score'
            ])
            ->from($this->table)
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type')
            ->join('bookings', 'bookings.id_upload = uploads.id')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
            ->join('booking_statuses', 'booking_statuses.id_booking = bookings.id AND booking_status = "COMPLETED"')
            ->join('(
                SELECT id_upload, MIN(upload_document_files.created_at) AS sppd_date, prv_users.name AS author
                FROM upload_documents
                LEFT JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                LEFT JOIN upload_document_files ON upload_documents.id = upload_document_files.id_upload_document
                LEFT JOIN ' . UserModel::$tableUser . ' ON prv_users.id = upload_document_files.created_by
                WHERE document_type = "SPPD"
                GROUP BY id_upload
            ) AS coo_receipts', 'coo_receipts.id_upload = uploads.id', 'left')
            ->where('ref_booking_types.dashboard_status', true)
            ->where('booking_statuses.created_at>=', '2019-05-01')
            ->where('ref_branches.dashboard_status', true)
            ->where('uploads.is_deleted', false)
            ->where('bookings.is_deleted', false)
            ->group_by('uploads.id, bookings.id');

        $uploads->where('author IS NOT NULL');
        if (key_exists('author', $filters) && !empty($filters['author'])) {
            $uploads->having('author', $filters['author']);
        }

        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            $uploads->having('branch', $filters['branch']);
        }

        if (key_exists('booking_category', $filters) && !empty($filters['booking_category'])) {
            $uploads->where('ref_booking_types.category', $filters['booking_category']);
        }

        if (key_exists('year', $filters) && !empty($filters['year'])) {
            $uploads->where('YEAR(uploads.created_at)', $filters['year']);
        }

        if (key_exists('month', $filters) && !empty($filters['month'])) {
            $uploads->where('MONTH(uploads.created_at)', $filters['month']);
        }

        if (key_exists('year_completed', $filters) && !empty($filters['year_completed'])) {
            $uploads->where('YEAR(booking_statuses.created_at)', $filters['year_completed']);
        }

        if (key_exists('month_completed', $filters) && !empty($filters['month_completed'])) {
            $uploads->where('MONTH(booking_statuses.created_at)', $filters['month_completed']);
        }

        if (key_exists('summary', $filters) && !empty($filters['summary'])) {
            $baseQuery = $uploads->get_compiled_select();
            return $this->db->query("
                SELECT {$filters['summary_type']}, 
                       IFNULL(SUM(score), 0) AS complete_sppd_score, 
                       COUNT(score) AS complete_sppd_docs, 
                       (IFNULL(SUM(score), 0) / COUNT(score) * 100) AS complete_sppd_percent
                FROM ({$baseQuery}) AS kpi
                GROUP BY {$filters['summary_type']}
            ")->result_array();
        }

        return $uploads->get()->result_array();
    }

    /**
     * Get kpi confirm/sppd in to billing
     * @param array $filters
     * @return array
     */
    public function getKpiSppdInBilling($filters = [])
    {
        $uploads = $this->db
            ->select([
                'author',
                'uploads.no_upload',
                'ref_branches.branch',
                '(upload_documents.created_at) AS confirm_date',
                '(confirms.sppd_in_date) AS sppd_in_date',
                '(confirms.max_confirm_sppd_in_date) AS max_confirm_sppd_in_date',
                '(billing_date) AS billing_date',
                'IF(TIME(confirms.max_confirm_sppd_in_date) < "12:00:00", 
                    billing_date <= CONCAT(DATE(confirms.max_confirm_sppd_in_date), " 23:59:59"),
                    billing_date <= CONCAT(DATE_ADD(DATE(confirms.max_confirm_sppd_in_date), INTERVAL 1 DAY), " 12:00:00")) AS score'
            ])
            ->from($this->table)
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch')
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type')
            ->join('upload_documents', 'upload_documents.id_upload = uploads.id')
            ->join('ref_document_types', 'ref_document_types.id = upload_documents.id_document_type')
            ->join('upload_document_files', 'upload_document_files.id_upload_document = upload_documents.id')
            ->join('(
                SELECT id_upload, (upload_document_files.created_at) AS billing_date, prv_users.name AS author
                FROM upload_documents
                LEFT JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                LEFT JOIN upload_document_files ON upload_documents.id = upload_document_files.id_upload_document
                LEFT JOIN ' . UserModel::$tableUser . ' ON prv_users.id = upload_document_files.created_by
                WHERE document_type = "E Billing"
                GROUP BY id_upload
            ) AS billings', 'billings.id_upload = uploads.id', 'left')
            ->join('(
                SELECT
                    upload_documents.id_upload, 
                    uploads.id_upload as id_upload_in,
                    sppd_in_documents.sppd_in_date,
                    IF(UNIX_TIMESTAMP(upload_documents.created_at) > IF(sppd_in_documents.sppd_in_date is not null, UNIX_TIMESTAMP(sppd_in_documents.sppd_in_date), 0), upload_documents.created_at, sppd_in_documents.sppd_in_date) AS max_confirm_sppd_in_date
                FROM upload_documents
                LEFT JOIN uploads ON upload_documents.id_upload = uploads.id 
                LEFT JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type 
                LEFT JOIN upload_document_files ON upload_document_files.id_upload_document = upload_documents.id
                LEFT JOIN ' . UserModel::$tableUser . ' ON prv_users.id = upload_document_files.created_by
                LEFT JOIN (
                    SELECT 
                        sppd_in_upload_documents.id_upload, 
                        sppd_in_upload_documents.created_at AS sppd_in_date 
                    FROM upload_documents AS sppd_in_upload_documents
                    LEFT JOIN ref_document_types ON ref_document_types.id = sppd_in_upload_documents.id_document_type
                    WHERE document_type = "SPPD"
                    GROUP BY sppd_in_upload_documents.id_upload 
                ) AS sppd_in_documents ON sppd_in_documents.id_upload = uploads.id_upload
                WHERE document_type IN("BC 1.6 Confirmation", "BC 2.8 Confirmation", "BC 2.7 Confirmation")
                GROUP BY upload_documents.id_upload
            ) AS confirms', 'confirms.id_upload = uploads.id', 'left')
            ->where('ref_booking_types.dashboard_status', true)
            ->where_in('ref_document_types.document_type', ['BC 1.6 Confirmation', 'BC 2.8 Confirmation', 'BC 2.7 Confirmation'])
            ->where('billings.billing_date IS NOT NULL')
            ->where('uploads.created_at>=', '2019-05-01')
            ->where('ref_branches.dashboard_status', true)
            ->where('ref_booking_types.category', bookingTypeModel::CATEGORY_OUTBOUND)
            ->where('uploads.is_deleted', false);

        $uploads->where('author IS NOT NULL');
        if (key_exists('author', $filters) && !empty($filters['author'])) {
            $uploads->having('author', $filters['author']);
        }

        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            $uploads->having('branch', $filters['branch']);
        }

        if (key_exists('booking_category', $filters) && !empty($filters['booking_category'])) {
            $uploads->where('ref_booking_types.category', $filters['booking_category']);
        }

        if (key_exists('year', $filters) && !empty($filters['year'])) {
            $uploads->where('YEAR(uploads.created_at)', $filters['year']);
        }

        if (key_exists('month', $filters) && !empty($filters['month'])) {
            $uploads->where('MONTH(uploads.created_at)', $filters['month']);
        }

        if (key_exists('year_confirm', $filters) && !empty($filters['year_confirm'])) {
            $uploads->where('YEAR(upload_documents.created_at)', $filters['year_confirm']);
        }

        if (key_exists('month_confirm', $filters) && !empty($filters['month_confirm'])) {
            $uploads->where('MONTH(upload_documents.created_at)', $filters['month_confirm']);
        }

        if (key_exists('summary', $filters) && !empty($filters['summary'])) {
            $baseQuery = $uploads->get_compiled_select();
            return $this->db->query("
                SELECT {$filters['summary_type']}, 
                       IFNULL(SUM(score), 0) AS sppdIn_billing_score, 
                       COUNT(score) AS sppdIn_billing_docs, 
                       (IFNULL(SUM(score), 0) / COUNT(score) * 100) AS sppdIn_billing_percent
                FROM ({$baseQuery}) AS kpi
                GROUP BY {$filters['summary_type']}
            ")->result_array();
        }

        return $uploads->get()->result_array();
    }

    /**
     * Get kpi bpn to sppn
     * @param array $filters
     * @return array
     */
    public function getKpiBpnSppb($filters = [])
    {
        $uploads = $this->db
            ->select([
                'author',
                'uploads.no_upload',
                'ref_branches.branch',
                '(upload_documents.created_at) AS bpn_date',
                'sppb_date',
                'IF(TIME((upload_documents.created_at)) < "12:00:00", 
                    sppb_date <= CONCAT(DATE((upload_documents.created_at)), " 23:59:59"),
                    sppb_date <= CONCAT(DATE_ADD((upload_documents.created_at), INTERVAL 1 DAY), " 12:00:00")) AS score'
            ])
            ->from($this->table)
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch')
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type')
            ->join('upload_documents', 'upload_documents.id_upload = uploads.id')
            ->join('ref_document_types', 'ref_document_types.id = upload_documents.id_document_type')
            ->join('upload_document_files', 'upload_document_files.id_upload_document = upload_documents.id')
            ->join('(
                SELECT id_upload, (upload_document_files.created_at) AS sppb_date, prv_users.name AS author
                FROM upload_documents
                LEFT JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                LEFT JOIN upload_document_files ON upload_documents.id = upload_document_files.id_upload_document
                LEFT JOIN ' . UserModel::$tableUser . ' ON prv_users.id = upload_document_files.created_by
                WHERE document_type = "SPPB"
                GROUP BY id_upload
            ) AS sppbs', 'sppbs.id_upload = uploads.id', 'left')
            ->where('ref_booking_types.dashboard_status', true)
            ->where('ref_document_types.document_type', "BPN (Bukti Penerimaan Negara)")
            ->where('sppbs.sppb_date IS NOT NULL')
            ->where('uploads.created_at>=', '2019-05-01')
            ->where('ref_branches.dashboard_status', true)
            ->where('uploads.is_deleted', false);
            
        $uploads->where('author IS NOT NULL');
        if (key_exists('author', $filters) && !empty($filters['author'])) {
            $uploads->having('author', $filters['author']);
        }

        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            $uploads->having('branch', $filters['branch']);
        }

        if (key_exists('booking_category', $filters) && !empty($filters['booking_category'])) {
            $uploads->where('ref_booking_types.category', $filters['booking_category']);
        }

        if (key_exists('year', $filters) && !empty($filters['year'])) {
            $uploads->where('YEAR(uploads.created_at)', $filters['year']);
        }

        if (key_exists('month', $filters) && !empty($filters['month'])) {
            $uploads->where('MONTH(uploads.created_at)', $filters['month']);
        }


        if (key_exists('year_bpn', $filters) && !empty($filters['year_bpn'])) {
            $uploads->where('YEAR(upload_documents.created_at)', $filters['year_bpn']);
        }

        if (key_exists('month_bpn', $filters) && !empty($filters['month_bpn'])) {
            $uploads->where('MONTH(upload_documents.created_at)', $filters['month_bpn']);
        }

        if (key_exists('summary', $filters) && !empty($filters['summary'])) {
            $baseQuery = $uploads->get_compiled_select();
            return $this->db->query("
                SELECT {$filters['summary_type']}, 
                       IFNULL(SUM(score), 0) AS bpn_sppb_score, 
                       COUNT(score) AS bpn_sppb_docs, 
                       (IFNULL(SUM(score), 0) / COUNT(score) * 100) AS bpn_sppb_percent
                FROM ({$baseQuery}) AS kpi
                GROUP BY {$filters['summary_type']}
            ")->result_array();
        }

        return $uploads->get()->result_array();
    }


    /**
     * Get statistic and data operation progress.
     *
     * @param array $filters
     * @return array
     */
    public function getSppbOperationProgress($filters = [])
    {
        $handlingTypeIdMoveIn = get_setting('default_moving_in_handling');

        $baseQuery = $this->db->select([
            'ref_branches.branch',
            'uploads.description',
            'ref_people.name AS customer_name',
            'sppb_documents.sppb_uploaded_at',
            'booking_statuses.booking_completed_at',
            'COUNT(moving_in_work_orders.id) AS total_moving_in'
        ])
            ->from('uploads')
            ->join('ref_people', 'ref_people.id = uploads.id_person')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch')
            ->join('(
                SELECT id_upload, upload_documents.created_at AS sppb_uploaded_at 
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE document_type = "SPPB"
            ) AS sppb_documents', 'sppb_documents.id_upload = uploads.id')
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('bookings', 'bookings.id_upload = sppb_documents.id_upload', 'left')
            ->join('(
                SELECT id_booking, MAX(created_at) AS booking_completed_at
                FROM booking_statuses 
                WHERE booking_status = "COMPLETED"
                GROUP BY id_booking
            ) AS booking_statuses', 'booking_statuses.id_booking = bookings.id', 'left')
            ->join("(
                SELECT handlings.id, handlings.id_booking, work_orders.no_work_order 
                FROM handlings
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                WHERE id_handling_type = {$handlingTypeIdMoveIn}
            ) AS moving_in_work_orders", 'moving_in_work_orders.id_booking = bookings.id', 'left')
            ->where('ref_booking_types.category', 'INBOUND')
            ->where('uploads.created_at>=', '2019-08-01')
            ->group_by('uploads.id');

        if (!empty($filters)) {

            if (key_exists('total_moving_in_from', $filters)) {
                $baseQuery->having('total_moving_in>=', $filters['total_moving_in_from']);
            }
            if (key_exists('total_moving_in_to', $filters)) {
                $baseQuery->having('total_moving_in<=', $filters['total_moving_in_to']);
            }

            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
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
     * Get realization document
     * @param array $filters
     * @return array
     */
    public function getRealizationDocument($filters = [])
    {

        $uploads = $this->db
            ->select([
                'uploads.*',
                'ref_branches.dashboard_status',
                'bookings.id AS id_booking',
                'upload_documents.id AS id_document',
                'default_document.document_type AS default_document',
                'document_draft.min_validated_at_draft',
                'document_draft_by_created.total_item',
                'document_draft_by_created.min_created_at_draft',
                'document_required.min_validated_at',
                'document_draft.validated_by',
                '(SELECT prv_users.name FROM '.UserModel::$tableUser.' WHERE prv_users.id = document_draft.validated_by) AS validated_name'

            ])
            ->from($this->table)
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
            ->join('upload_documents', 'upload_documents.id_upload = uploads.id', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_document_types', 'ref_document_types.id = upload_documents.id_document_type', 'left')
            ->join('ref_document_types AS default_document', 'ref_booking_types.id_document_type = default_document.id', 'left')
            ->join('ref_booking_document_types', 'ref_document_types.id = ref_booking_document_types.id_document_type', 'left')
            ->join('(SELECT * FROM bookings WHERE is_deleted = false) AS bookings', 'bookings.id_upload = uploads.id', 'left')
            ->join('(
                SELECT 
                    id_upload,
                    upload_documents.validated_at, 
                    upload_documents.validated_by,
                    MIN(upload_documents.validated_at) AS min_validated_at_draft
                FROM upload_documents
                    LEFT JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE document_type IN("BC 1.6 Draft","BC 2.8 Draft","BC 2.7 Draft")
                GROUP BY id_upload
            ) document_draft', 'document_draft.id_upload = uploads.id', 'left')
            ->join('(
                SELECT 
                    id_upload,
                    total_item,
                    MIN(upload_documents.created_at) AS min_created_at_draft
                FROM upload_documents
                    LEFT JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE document_type IN("BC 1.6 Draft","BC 2.8 Draft","BC 2.7 Draft")
                GROUP BY upload_documents.created_at
            ) document_draft_by_created', 'document_draft_by_created.id_upload = uploads.id', 'left')
            ->join('(
                SELECT
                    upload_documents.id_upload,
                    upload_documents.validated_at, 
                    upload_documents.validated_by,
                    MIN(upload_documents.validated_at) AS min_validated_at,
                    ref_booking_document_types.id_booking_type
                FROM upload_documents
                    LEFT JOIN uploads AS main_uploads ON main_uploads.id = upload_documents.id_upload
                    LEFT JOIN ref_booking_document_types ON upload_documents.id_document_type = ref_booking_document_types.id_document_type
                WHERE ref_booking_document_types.is_required = "1" AND ref_booking_document_types.id_booking_type = main_uploads.id_booking_type
                GROUP BY upload_documents.id_upload
            ) document_required', 'document_required.id_upload = uploads.id', 'left')
            ->where('uploads.is_deleted', false)
            ->where('ref_branches.dashboard_status', true)
            ->where('document_draft_by_created.min_created_at_draft IS NOT NULL')
            ->group_by('uploads.id');

        if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
            if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                $uploads->where('DATE(' . $filters['date_type'] . ')>=', sql_date_format($filters['date_from'], false));
            }
            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $uploads->where('DATE(' . $filters['date_type'] . ')<=', sql_date_format($filters['date_to'], false));
            }
        }

        if (key_exists('id_upload', $filters) && !empty($filters['id_upload'])) {
             $uploads->where('uploads.id', $filters['id_upload']);
        }

        if (key_exists('id_booking_type', $filters) && !empty($filters['id_booking_type'])) {
             $uploads->where('ref_booking_document_types.id_booking_type', $filters['id_booking_type']);
        }

        if (key_exists('category', $filters) && !empty($filters['category'])) {
             $uploads->where('ref_booking_types.category', $filters['category']);
        }
        return $uploads->get()->result_array();
    }

    /**
     * Get data by custom condition.
     *
     * @param $conditions
     * @param bool $resultRow
     * @param bool $withTrashed
     * @return array|int
     */
    public function getUploadSppbByCondition($conditions, $resultRow = false, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery();
        $baseQuery->SELECT([
            'doc_type.document_type AS jenis_doc',
            'upload_documents.no_document AS no_aju',
            ])
        ->join('ref_document_types as doc_type', 'doc_type.id = upload_documents.id_document_type','left')
        ->join('(SELECT ref_document_types.`document_type`,upload_documents.* FROM upload_documents 
        JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
        WHERE ref_document_types.`document_type` = "SPPD" AND upload_documents.`is_deleted`!=1) AS doc_type_sppd','`doc_type_sppd`.`id_upload` = `uploads`.`id`', 'left');

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
     * Get uploads by Item Compliance.
     * @param null $itemComplianceId
     * @param bool $withTrashed
     * @return mixed
     */
    public function getUploadsByItemCompliance($itemComplianceId, $withTrashed = false)
    {

        $uploads = $this->getBaseQuery(0)
                ->where('upload_item_photos.id_item', $itemComplianceId);

        if (!$withTrashed) {
            $uploads->where('uploads.is_deleted', false);
        }

        return $uploads->get()->result_array();
    }

    /**
     * @param $upload
     * @param $document
     * @return mixed|string
     */
    public function updateUploadStatus($upload, $document)
    {
        $this->load->model('StatusHistoryModel', 'statusHistory');

        $uploadStatus = $upload['status'];
        $uploadStatusHistory = $upload['status'];
        $uploadIsValid = $upload['is_valid'];

        if (strpos(strtolower($document['document_type']), 'draft') !== false) {
            $uploadStatusHistory = UploadModel::STATUS_ON_PROCESS;
            if ($uploadStatus == UploadModel::STATUS_NEW) {
                $uploadStatus = $uploadStatusHistory;
            }
        }

        if (strpos($document['document_type'], 'E Billing') !== false) {
            $uploadStatusHistory = UploadModel::STATUS_BILLING;
            if (in_array($uploadStatus, [UploadModel::STATUS_NEW, UploadModel::STATUS_ON_PROCESS])) {
                $uploadStatus = $uploadStatusHistory;
            }
        }

        if (strpos($document['document_type'], 'BPN') !== false) {
            $uploadStatusHistory = UploadModel::STATUS_PAID;
            if (in_array($uploadStatus, [UploadModel::STATUS_NEW, UploadModel::STATUS_ON_PROCESS, UploadModel::STATUS_BILLING])) {
                $uploadStatus = $uploadStatusHistory;
            }
        }

        if (strpos($document['document_type'], 'SPPB') !== false) {
            $uploadStatusHistory = $uploadStatus = UploadModel::STATUS_CLEARANCE;
            $uploadIsValid = 1;
        }

        if ($uploadStatus != $upload['status'] || $uploadStatus == UploadModel::STATUS_CLEARANCE) {
            $this->update([
                'status' => $uploadStatus,
                'is_valid' => $uploadIsValid,
            ], $upload['id']);
        }

        if ($uploadStatusHistory != $upload['status']) {
            $this->statusHistory->create([
                'id_reference' => $upload['id'],
                'type' => StatusHistoryModel::TYPE_UPLOAD,
                'status' => $uploadStatusHistory,
                'description' => "{$document['document_type']} is uploaded",
                'data' => json_encode([
                    'document_type' => $document['document_type'],
                    'no_document' => $document['no_document'],
                    'document_date' => $document['document_date'],
                ])
            ]);
        }

        return $uploadStatus;
    }
}
