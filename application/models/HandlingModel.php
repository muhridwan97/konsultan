<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HandlingModel extends MY_Model
{
    protected $table = 'handlings';

    const STATUS_PENDING = 'PENDING';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';

    /**
     * HandlingModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get base query of handling
     * @param null $branchId
     * @param null $userType
     * @param null $customerId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null, $userType = null, $customerId = null)
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

        $handlings = $this->db->select([
            'handlings.*',
            'bookings.no_booking',
            'bookings.no_reference',
            'bookings.id_branch',
            'ref_branches.branch',
            'ref_handling_types.handling_type',
            'ref_handling_types.handling_code',
            'ref_handling_types.category AS handling_category',
            'DATEDIFF(handling_date, CURRENT_DATE()) AS handling_date_remaining',
            'DATEDIFF(CURRENT_DATE(), handlings.created_at) AS create_date_elapsed',
            'ref_people.name AS customer_name',
            'ref_people.email AS customer_email',
            'prv_users.name AS validator_name',
            'creators.name AS creator_name'
        ])
            ->from('handlings')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('ref_people', 'ref_people.id = handlings.id_customer')
            ->join(UserModel::$tableUser, 'prv_users.id = handlings.validated_by', 'left')
            ->join(UserModel::$tableUser . ' AS creators', 'creators.id = handlings.created_by', 'left');

        if (!empty($branchId)) {
            $handlings->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $handlings->where('bookings.id_customer', $customerId);
        }

        return $handlings;
    }

    /**
     * Get all handling data.
     * @param int $start
     * @param int $length
     * @param string $search
     * @param int $column
     * @param string $sort
     * @param bool $withTrashed
     * @return mixed
     */
    public function getAllHandlings($start = -1, $length = 10, $search = '', $column = 0, $sort = 'DESC', $withTrashed = false)
    {
        // alias column name by index for sorting data table library
        $columnOrder = [
            0 => "handlings.id",
            1 => "ref_people.name",
            2 => "handlings.no_handling",
            3 => "ref_handling_types.handling_type",
            4 => "handlings.handling_date",
            5 => "handlings.handling_date",
            6 => "handlings.status",
            7 => "handlings.id",
        ];
        $columnSort = $columnOrder[$column];

        $branchId = get_active_branch('id');
        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $this->db->start_cache();

        $handlings = $this->getBaseQuery($branchId, $userType, $customerId);

        if (!$withTrashed) {
            $handlings->where('handlings.is_deleted', false);
        }

        if ($start < 0) {
            $handlingData = $handlings->get()->result_array();
            $handlings->stop_cache();
            $handlings->flush_cache();
            return $handlingData;
        }

        $handlings
            ->group_start()
            ->like('handlings.no_handling', $search)
            ->or_like('ref_people.name', $search)
            ->or_like('ref_handling_types.handling_type', $search)
            ->or_like('bookings.no_booking', $search)
            ->or_like('bookings.no_reference', $search)
            ->or_like('ref_handling_types.category', $search)
            ->group_end()
            ->order_by($columnSort, $sort);

        $handlings->stop_cache();

        $handlingTotal = $handlings->count_all_results();

        $handlingsPage = $handlings->limit($length, $start);
        $handlingData = $handlingsPage->get()->result_array();

        $data = [
            "total" => count($handlingData),
            "result" => $handlingTotal,
            "data" => $handlingData,
        ];

        $this->db->flush_cache();

        return $data;
    }

    /**
     * Get handling by type.
     * @param $handlingTypeId
     * @param null $customerId
     * @param bool $withTrashed
     * @return mixed
     */
    public function getHandlingByType($handlingTypeId, $customerId = null, $withTrashed = false)
    {
        $handlings = $this->getBaseQuery()->where('handlings.id_handling_type', $handlingTypeId);

        if (!$withTrashed) {
            $handlings->where('handlings.is_deleted', false);
        }

        if (!empty($customerId)) {
            $handlings->where('handlings.id_customer', $customerId);
        }

        return $handlings->get()->result_array();
    }

    /**
     * Get handling by id.
     * @param $handlingId
     * @param bool $withTrashed
     * @return mixed
     */
    public function getHandlingById($handlingId, $withTrashed = false)
    {
        $handlingTypes = $this->getBaseQuery()->where('handlings.id', $handlingId);

        if (!$withTrashed) {
            $handlingTypes->where('handlings.is_deleted', false);
        }

        return $handlingTypes->get()->row_array();
    }

    /**
     * Get handling by no.
     * @param $handlingNo
     * @param bool $approvedOnly
     * @param null $page
     * @return mixed
     */
    public function getHandlingByNo($handlingNo, $approvedOnly = false, $page = null)
    {
        $branchId = get_active_branch('id');
        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $this->db->start_cache();

        $handlingTypes = $this->getBaseQuery($branchId, $userType, $customerId)
            ->where(['handlings.is_deleted' => false])
            ->like('handlings.no_handling', $handlingNo, 'both');

        if ($approvedOnly) {
            $handlingTypes->where('handlings.status', self::STATUS_APPROVED);
        }

        $this->db->stop_cache();

        if (!empty($page) || $page != 0) {
            $handlingTotal = $handlingTypes->count_all_results();
            $handlingPage = $handlingTypes->limit(10, 10 * ($page - 1));
            $handlingData = $handlingPage->get()->result_array();

            $this->db->flush_cache();

            return [
                'results' => $handlingData,
                'total_count' => $handlingTotal
            ];
        }

        $data = $handlingTypes->get()->row_array();

        $this->db->flush_cache();

        return $data;
    }

    /**
     * Get handlings by booking id.
     * @param $bookingId
     * @param bool $withTrash
     * @return mixed
     */
    public function getHandlingsByBooking($bookingId, $withTrash = false)
    {
        $handlings = $this->getBaseQuery()
            ->select([
                'work_orders.id AS id_work_order',
                'work_orders.no_work_order'
            ])
            ->join('work_orders', 'work_orders.id_handling = handlings.id')
            ->where('handlings.id_booking', $bookingId)
            ->order_by('handlings.id', 'desc');

        if (!$withTrash) {
            $handlings->where('handlings.is_deleted', false);
        }

        return $handlings->get()->result_array();
    }

    /**
     * Get handling by invoice status or data
     * @param $customerId
     * @return mixed
     */
    public function getHandlingsByUnpublishedInvoice($customerId)
    {
        $handlings = $this->getBaseQuery()->distinct()
            ->join('(SELECT * FROM invoices WHERE is_deleted = 0 AND status = "PUBLISHED") AS invoices', 'invoices.no_reference = handlings.no_handling', 'left')
            ->where('invoices.id IS NULL')
            ->where('handlings.status', 'APPROVED')
            ->where('handlings.id_customer', $customerId)
            ->order_by('handlings.id', 'desc');
        return $handlings->get()->result_array();
    }

    /**
     * Generate handling auto number.
     * @param string $type
     * @return string
     */
    public function getAutoNumberHandlingRequest($type = 'HR')
    {
        $orderData = $this->db->query("
            SELECT CAST(RIGHT(no_handling, 6) AS UNSIGNED) + 1 AS order_number 
            FROM handlings 
            WHERE MONTH(created_at) = MONTH(NOW()) 
              AND YEAR(created_at) = YEAR(NOW())
              AND SUBSTRING(no_handling, 1, 2) = '$type'
            ORDER BY CAST(RIGHT(no_handling, 6) AS UNSIGNED) DESC LIMIT 1
        ");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return $type . '/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }

    /**
     * Create new handling.
     * @param $data
     * @return bool
     */
    public function createHandling($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update handling data.
     * @param $data
     * @param $id
     * @return bool
     */
    public function updateHandling($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete handling data.
     * @param $id
     * @param bool $softDelete
     * @return bool
     */
    public function deleteHandling($id, $softDelete = true)
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
     * Search handling data by query string.
     * @param $query
     * @param int $limit
     * @param bool $withTrashed
     * @return mixed
     */
    public function search($query, $limit = 10, $withTrashed = false)
    {
        $handlings = $this->getBaseQuery()
            ->group_start()
            ->like('no_handling', $query)
            ->or_like('handling_type', $query)
            ->or_like('handlings.status', $query)
            ->or_like('handling_date', $query)
            ->or_like('ref_people.name', $query)
            ->group_end()
            ->limit($limit);

        if (!$withTrashed) {
            $handlings->where($this->table . '.is_deleted', false);
        }

        return $handlings->get()->result_array();
    }

}