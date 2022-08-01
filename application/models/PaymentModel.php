<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PaymentModel extends MY_Model
{
    protected $table = 'payments';
    protected $filteredMaps = [
        'users' => 'payments.created_by',
        'status_checks' => 'payments.status_check',
        'customers' => 'IFNULL(customers.id, upload_customers.id)',
        'payment_category' => 'payments.payment_category',
        'payment_type' => 'payments.payment_type',
    ];

    const PAYMENT_BILLING = 'BILLING';
    const PAYMENT_NON_BILLING = 'NON BILLING';

    const TYPE_OB_TPS = 'OB TPS';
    const TYPE_OB_TPS_PERFORMA = 'OB TPS PERFORMA';
    const TYPE_DISCOUNT = 'DISCOUNT';
    const TYPE_DO = 'DO';
    const TYPE_EMPTY_CONTAINER_REPAIR = 'EMPTY CONTAINER REPAIR';
    const TYPE_DRIVER = 'DRIVER';
    const TYPE_DISPOSITION_AND_TPS_OPERATIONAL = 'DISPOSITION AND TPS OPERATIONAL';
    const TYPE_AS_PER_BILL = 'AS PER BILL';
    const TYPE_TOOLS_AND_EQUIPMENTS = 'TOOLS AND EQUIPMENTS';
    const TYPE_MISC = 'MISC';

    const STATUS_DRAFT = 'DRAFT';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_SUBMISSION_REJECTED = 'SUBMISSION REJECTED';
    const STATUS_SUBMITTED = 'SUBMITTED';
    const STATUS_REALIZED = 'REALIZED';
    const STATUS_PENDING = 'PENDING';
    const STATUS_ASK_APPROVAL = 'ASK APPROVAL';

    const CHARGE_BEFORE_TAX = 'BEFORE TAX';
    const CHARGE_AFTER_TAX = 'AFTER TAX';

    /**
     * PaymentModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function getBaseQuery($branchId = null)
    {
        $this->load->model('BankAccountModel');
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $payments = $this->db->select([
            'IFNULL(bookings.no_booking, upload_bookings.no_booking) AS no_booking',
            'IFNULL(bookings.no_reference, upload_bookings.no_reference) AS no_reference',
            'uploads.no_upload',
            'uploads.description AS upload_description',
            'ref_branches.branch',
            'IFNULL(customers.name, upload_customers.name) AS customer_name',
            'applicants.username AS applicant_name',
            'validator.username AS validator_name',
            'pic.username AS pic_name',
            'payments.*',
            'DATEDIFF(IFNULL(DATE(payments.realized_at), CURDATE()), DATE(IFNULL(payments.approved_at, payments.created_at))) AS elapsed_day_until_realized',
            'ref_bank_accounts.bank',
            'ref_bank_accounts.account_number',
            'ref_bank_accounts.bank_type',
        ])
            ->from($this->table)
            ->join('bookings', 'bookings.id = payments.id_booking', 'left')
            ->join('uploads', 'uploads.id = payments.id_upload', 'left')
            ->join('bookings AS upload_bookings', 'upload_bookings.id_upload = uploads.id', 'left')
            ->join('ref_people AS upload_customers', 'upload_customers.id = uploads.id_person', 'left')
            ->join('ref_branches', 'ref_branches.id = IFNULL(bookings.id_branch, uploads.id_branch)', 'left')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join(UserModel::$tableUser . ' AS applicants', 'applicants.id = payments.created_by', 'left')
            ->join(UserModel::$tableUser . ' AS validator', 'validator.id = payments.approved_by', 'left')
            ->join(UserModel::$tableUser . ' AS pic', 'pic.id = payments.user_pic', 'left')
            ->join(BankAccountModel::$tableBankAccount, 'ref_bank_accounts.id = payments.id_bank_account', 'left');

        if (!empty($branchId)) {
            $payments->group_start()
                ->where('bookings.id_branch', $branchId)
                ->or_where('uploads.id_branch', $branchId)
                ->group_end();
        }

        return $payments;
    }

    /**
     * Get auto number for booking.
     * @return string
     */
    public function getAutoNumberPayment()
    {
        $orderData = $this->db->query("
            SELECT IFNULL(CAST(RIGHT(no_payment, 6) AS UNSIGNED), 0) + 1 AS order_number 
            FROM payments 
            WHERE MONTH(created_at) = MONTH(NOW()) 
			AND YEAR(created_at) = YEAR(NOW())
            ORDER BY SUBSTR(no_payment FROM 4) DESC LIMIT 1
			");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return 'PY/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }

    /**
     * Get all payments with or without deleted records.
     * @param $filters
     * @param bool $withTrashed
     * @return array
     */
    public function getAllPayments($filters, $withTrashed = false)
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branch = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $this->db->start_cache();

        $payments = $this->getBaseQuery($branch)
            ->group_start()
            ->like('bookings.no_booking', trim($search))
            ->or_like('bookings.no_reference', trim($search))
            ->or_like('uploads.no_upload', trim($search))
            ->or_like('uploads.description', trim($search))
            ->or_like('payments.no_payment', trim($search))
            ->or_like('payments.payment_type', trim($search))
            ->or_like('payments.payment_date', trim($search))
            ->or_like('payments.amount_request', trim($search))
            ->or_like('payments.amount', trim($search))
            ->or_like('payments.status', trim($search))
            ->or_like('payments.status_check', trim($search))
            ->or_like('payments.is_realized', trim($search))
            ->or_like('payments.description', trim($search))
            ->or_like('applicants.username', trim($search))
            ->group_end();

        if (!$withTrashed) {
            $payments->where('payments.is_deleted', false);
        }

        if (key_exists('user_pic', $filters) && !empty($filters['user_pic'])) {
            $payments->group_start()
                ->where_in('payments.created_by', $filters['user_pic'])
                ->or_where_in('payments.user_pic', $filters['user_pic'])
                ->group_end();
        }

        if (key_exists('status', $filters) && !empty($filters['status'])) {
            if ($filters['status'] == self::STATUS_REALIZED) {
                $payments->where('payments.is_realized', 1);
            } else {
                $payments->where_in('payments.status', $filters['status']);
            }
        }

        if (key_exists('tps_payment', $filters) && !empty($filters['tps_payment'])) {
            if ($filters['tps_payment'] == 'Paid By Customer') {
                $payments
                    ->where('payments.payment_type', self::TYPE_OB_TPS_PERFORMA)
                    ->where('payments.amount_request<=', 0);
            }
            if ($filters['tps_payment'] == 'Outstanding') {
                $payments
                    ->where('payments.payment_type', self::TYPE_OB_TPS_PERFORMA)
                    ->where('payments.amount_request>', 0)
                    ->where('payments.tps_invoice_payment_date IS NULL', null);
            }
            if ($filters['tps_payment'] == 'Realized') {
                $payments
                    ->where('payments.payment_type', self::TYPE_OB_TPS_PERFORMA)
                    ->where('payments.amount_request>', 0)
                    ->where('payments.tps_invoice_payment_date IS NOT NULL', null);
            }
        }

        if (!empty($this->filteredMaps)) {
            foreach ($this->filteredMaps as $filterKey => $filterField) {
                if (key_exists($filterKey, $filters) && !empty($filters[$filterKey])) {
                    $payments->where_in($filterField, $filters[$filterKey]);
                }
            }
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $payments->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $paymentTotal = $this->db->count_all_results();

        $columnOrder = [
            0 => "payments.id",
            1 => "bookings.no_booking",
            2 => "payments.no_payment",
            3 => "payments.payment_type",
            4 => "payments.payment_date",
            5 => "payments.amount_request",
            6 => "elapsed_day_until_realized",
            7 => "payments.status",
            8 => "payments.is_realized",
            9 => "payments.id",
        ];
        $columnSort = $columnOrder[$column];
        if ($columnSort == 'elapsed_day_until_realized') {
            $payments->order_by('payments.is_realized', 'asc');
        }
        $paymentPage = $payments->order_by($columnSort, $sort)->limit($length, $start);
        $paymentData = $paymentPage->get()->result_array();

        $this->db->flush_cache();

        foreach ($paymentData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($paymentData),
            "recordsFiltered" => $paymentTotal,
            "data" => $paymentData
        ];

        return $pageData;
    }

    /**
     * Get all payments with or without deleted records for report menu.
     * @param $filters
     * @param bool $withTrashed
     * @return array
     */
    public function getAllReportPayments($filters, $withTrashed = false)
    {
        $this->load->model('StatusHistoryModel', 'statusHistory');

        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branch = key_exists('branch', $filters) ? $filters['branch'] : NULL;
        
        $this->db->start_cache();
        $this->load->model('BankAccountModel');

        $payments = $this->db->select([
            'IFNULL(bookings.no_booking, upload_bookings.no_booking) AS no_booking',
            'IFNULL(bookings.no_reference, upload_bookings.no_reference) AS no_reference',
            'uploads.no_upload',
            'uploads.description AS upload_description',
            'ref_branches.id AS id_branch',
            'ref_branches.branch',
            'IFNULL(customers.name, upload_customers.name) AS customer_name',
            'applicants.username AS applicant_name',
            'validator.username AS validator_name',
            'payments.*',
            'DATEDIFF(IFNULL(DATE(payments.realized_at), CURDATE()), DATE(IFNULL(payments.approved_at, payments.created_at))) AS elapsed_day_until_realized',
            'ref_bank_accounts.bank',
            'ref_bank_accounts.account_number',
            'ref_bank_accounts.bank_type',
        ])
            ->from($this->table)
            ->join('bookings', 'bookings.id = payments.id_booking', 'left')
            ->join('uploads', 'uploads.id = payments.id_upload', 'left')
            ->join('bookings AS upload_bookings', 'upload_bookings.id_upload = uploads.id', 'left')
            ->join('ref_people AS upload_customers', 'upload_customers.id = uploads.id_person', 'left')
            ->join('ref_branches', 'ref_branches.id = IFNULL(bookings.id_branch, uploads.id_branch)', 'left')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join(UserModel::$tableUser . ' AS applicants', 'applicants.id = payments.created_by', 'left')
            ->join(UserModel::$tableUser . ' AS validator', 'validator.id = payments.approved_by', 'left')
            ->join(BankAccountModel::$tableBankAccount, 'ref_bank_accounts.id = payments.id_bank_account', 'left')

            ->select([
                'status_submitted.created_at AS date_submitted',
                'TIMESTAMPDIFF(HOUR, IFNULL(payments.approved_at, payments.created_at), IFNULL(payments.realized_at, NOW())) AS elapsed_hour_until_realized'
            ])
            ->join('(SELECT *
                    FROM status_histories
                    WHERE status = "'.PaymentModel::STATUS_SUBMITTED.'"
                    AND type = "'.StatusHistoryModel::TYPE_BOOKING_PAYMENT.'"
                    GROUP BY id_reference
                    ORDER BY id_reference DESC) 
                    AS status_submitted', 'status_submitted.id_reference = payments.id ' , 'left')
            ->group_start()
            ->like('bookings.no_booking', trim($search))
            ->or_like('bookings.no_reference', trim($search))
            ->or_like('uploads.no_upload', trim($search))
            ->or_like('uploads.description', trim($search))
            ->or_like('payments.no_payment', trim($search))
            ->or_like('payments.payment_type', trim($search))
            ->or_like('payments.payment_date', trim($search))
            ->or_like('payments.amount_request', trim($search))
            ->or_like('payments.amount', trim($search))
            ->or_like('payments.status', trim($search))
            ->or_like('payments.status_check', trim($search))
            ->or_like('payments.is_realized', trim($search))
            ->or_like('applicants.username', trim($search))
            ->group_end()
            ->order_by('payments.id','desc');

        if (!empty($branch)) {
            $payments->group_start()
                ->where_in('bookings.id_branch', $branch)
                ->or_where_in('uploads.id_branch', $branch)
                ->group_end();
        }

        if (!$withTrashed) {
            $payments->where('payments.is_deleted', false);
        }

        if (key_exists('status', $filters) && !empty($filters['status'])) {
            if ($filters['status'] == self::STATUS_REALIZED) {
                $payments->where('payments.is_realized', 1);
            } else {
                $payments->where_in('payments.status', $filters['status']);
            }
        }

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $payments->where('DATE(payments.payment_date)>=', sql_date_format($filters['date_from'], false));
        }
        if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
            $payments->where('DATE(payments.payment_date)<=', sql_date_format($filters['date_to'], false));
        }

        if (key_exists('tps_payment', $filters) && !empty($filters['tps_payment'])) {
            if ($filters['tps_payment'] == 'Paid By Customer') {
                $payments
                    ->where('payments.payment_type', self::TYPE_OB_TPS_PERFORMA)
                    ->where('payments.amount_request<=', 0);
            }
            if ($filters['tps_payment'] == 'Outstanding') {
                $payments
                    ->where('payments.payment_type', self::TYPE_OB_TPS_PERFORMA)
                    ->where('payments.amount_request>', 0)
                    ->where('payments.tps_invoice_payment_date IS NULL', null);
            }
            if ($filters['tps_payment'] == 'Realized') {
                $payments
                    ->where('payments.payment_type', self::TYPE_OB_TPS_PERFORMA)
                    ->where('payments.amount_request>', 0)
                    ->where('payments.tps_invoice_payment_date IS NOT NULL', null);
            }
        }

        if (!empty($this->filteredMaps)) {
            foreach ($this->filteredMaps as $filterKey => $filterField) {
                if (key_exists($filterKey, $filters) && !empty($filters[$filterKey])) {
                    $payments->where_in($filterField, $filters[$filterKey]);
                }
            }
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $payments->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $paymentTotal = $this->db->count_all_results();

        $columnOrder = [
            0 => "payments.id",
            1 => "bookings.no_booking",
            2 => "payments.no_payment",
            3 => "payments.payment_type",
            4 => "payments.payment_date",
            5 => "payments.amount_request",
            6 => "elapsed_day_until_realized",
            7 => "payments.status",
            8 => "payments.is_realized",
            9 => "payments.id",
        ];
        $columnSort = $columnOrder[$column];
        if ($columnSort == 'elapsed_day_until_realized') {
            $payments->order_by('payments.is_realized', 'asc');
        }
        
        $paymentPage = $payments->order_by($columnSort, $sort)->limit($length, $start);
        $paymentData = $paymentPage->get()->result_array();

        $this->db->flush_cache();

        foreach ($paymentData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($paymentData),
            "recordsFiltered" => $paymentTotal,
            "data" => $paymentData
        ];

        return $pageData;
    }

    /**
     * Get payment data.
     *
     * @param array $filters
     * @param bool $withTrashed
     * @return array
     */
    public function getPayments($filters = [], $withTrashed = false)
    {
        $payments = $this->db->select([
            'bookings.no_booking',
            'bookings.no_reference',
            'ref_branches.branch',
            'customers.name AS customer_name',
            'applicants.username AS applicant_name',
            'validator.username AS validator_name',
            'payments.*'
        ])
            ->from($this->table)
            ->join('bookings', 'bookings.id = payments.id_booking', 'left')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join(UserModel::$tableUser . ' AS applicants', 'applicants.id = payments.created_by', 'left')
            ->join(UserModel::$tableUser . ' AS validator', 'validator.id = payments.updated_by', 'left');


        if (!$withTrashed) {
            $payments->where('payments.is_deleted', false);
        }

        if (key_exists('from_date', $filters) && !empty($filters['from_date'])) {
            $payments->where('DATE(payments.payment_date)>=', format_date($filters['from_date']));
        }

        if (key_exists('to_date', $filters) && !empty($filters['to_date'])) {
            $payments->where('DATE(payments.payment_date)<=', format_date($filters['to_date']));
        }

        if (key_exists('is_realized', $filters)) {
            $payments->where('is_realized', $filters['is_realized']);
        }

        if (key_exists('booking', $filters)) {
            $payments->where('payments.id_booking', $filters['booking']);
        }

        if (key_exists('non_performa', $filters) && $filters['non_performa']) {
            $payments->where('payments.payment_type!=', 'OB TPS PERFORMA');
        }

        if (key_exists('type', $filters) && !empty($filters['type'])) {
            if (is_array($filters['type'])) {
                $payments->where_in('payment_type', $filters['type']);
            } else {
                $payments->where('payment_type', $filters['type']);
            }
        }

        return $payments->get()->result_array();
    }

    /**
     * Get single payment data by id with or without deleted record.
     * @param integer $id
     * @param bool $withTrashed
     * @return array
     */
    public function getPaymentById($id, $withTrashed = false)
    {
        $payment = $this->getBaseQuery()->where('payments.id', $id);

        if (!$withTrashed) {
            $payment->where('payments.is_deleted', false);
        }

        return $payment->get()->row_array();
    }

    /**
     * Get payment by booking.
     * @param $bookingId
     * @param null $paymentCategory
     * @param bool $withTrashed
     * @return mixed
     */
    public function getPaymentsByBooking($bookingId, $paymentCategory = null, $withTrashed = false)
    {
        $payments = $this->getBaseQuery()->where('IFNULL(payments.id_booking, upload_bookings.id) = ' . $bookingId);

        if (!$withTrashed) {
            $payments->where('payments.is_deleted', false);
        }

        if (!empty($paymentCategory)) {
            $payments->where('payments.payment_category', $paymentCategory);
        }

        return $payments->get()->result_array();
    }

    /**
     * Create new payment.
     * @param $data
     * @return bool
     */
    public function createPayment($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update payment.
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updatePayment($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete payment data.
     * @param integer $id
     * @param bool $softDelete
     * @return bool
     */
    public function deletePayment($id, $softDelete = true)
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

}
