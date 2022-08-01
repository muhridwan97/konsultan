<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DeliveryOrderModel extends CI_Model
{
    private $table = 'delivery_orders';

    /**
     * DeliveryOrderModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getBookingETAStatus()
    {
        $validator = AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_VALIDATE);
        $userId = UserModel::authenticatedUserData('id');

        $branchId = get_active_branch('id');
        $bookings = $this->db->select([
            'bookings.id',
            'bookings.id_upload',
            'bookings.no_booking',
            'bookings.no_reference',
            'bookings.id_upload',
            'uploads.no_upload',
            'customers.name AS customer_name',
            'prv_users.name AS assigned_to',
            'bookings.created_at',
            'booking_extensions.value AS eta',
            'IFNULL(DATEDIFF(DATE(booking_extensions.value), CURDATE()), 0) AS diff_eta',
            "(SELECT COUNT(*) FROM upload_documents 
              INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
              WHERE upload_documents.id_upload = bookings.id_upload 
                AND ref_document_types.document_type = '" . DocumentTypeModel::DOC_DO . "'
              LIMIT 1) AS has_do",
            "(SELECT COUNT(*) FROM upload_documents 
              INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
              WHERE upload_documents.id_upload = bookings.id_upload 
                AND ref_document_types.document_type = '" . DocumentTypeModel::DOC_ATA . "'
              LIMIT 1) AS has_ata",
            "(SELECT COUNT(*) FROM upload_documents 
              INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
              WHERE upload_documents.id_upload = bookings.id_upload 
                AND ref_document_types.document_type = '" . DocumentTypeModel::DOC_SPPB . "'
              LIMIT 1) AS has_sppb",
            "(SELECT COUNT(*) FROM upload_documents 
              INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
              WHERE upload_documents.id_upload = bookings.id_upload 
                AND ref_document_types.document_type = '" . DocumentTypeModel::DOC_TILA . "'
              LIMIT 1) AS has_tila",
        ])
            ->from('bookings')
            ->join('uploads', 'uploads.id = bookings.id_upload', 'left')
            ->join('booking_extensions', 'booking_extensions.id_booking = bookings.id', 'left')
            ->join('ref_extension_fields', 'booking_extensions.id_extension_field = ref_extension_fields.id', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('booking_assignments', 'booking_assignments.id_booking = bookings.id', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = booking_assignments.id_user', 'left')
            ->where('ref_booking_types.category', 'INBOUND')
            ->where('ref_extension_fields.field_name', 'ETA')
            ->where_in('bookings.status', [BookingModel::STATUS_APPROVED, BookingModel::STATUS_COMPLETED])
            ->where('bookings.id_upload IS NOT NULL')
            ->where('bookings.id_upload != 0')
            ->where('ref_booking_types.with_do', true)
            ->order_by('bookings.created_at', 'desc');

        $bookings->where('bookings.id_branch', $branchId);

        if(!$validator) {
            $bookings->where('booking_assignments.id_user', $userId);
        }

        return $bookings->get()->result_array();
    }

    /**
     * Get auto number for delivery order.
     * @param $deliveryType
     * @return string
     */
    public function getAutoNumberDelivery($deliveryType)
    {
        $orderData = $this->db->query("
            SELECT CAST(RIGHT(no_delivery_order, 6) AS UNSIGNED) + 1 AS order_number 
            FROM delivery_orders 
            WHERE MONTH(created_at) = MONTH(NOW()) 
              AND YEAR(created_at) = YEAR(NOW())
            ORDER BY id DESC LIMIT 1
        ");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return $deliveryType . '/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }

    /**
     * Get basic query data delivery order
     * @return CI_DB_query_builder
     */
    private function getDeliveryOrderBaseQuery()
    {
        $branchId = get_active_branch('id');
        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $deliveryOrders = $this->db
            ->select([
                'delivery_orders.*',
                'bookings.no_booking',
                'bookings.no_reference',
                'bookings.status',
                'bookings.booking_date',
                'ref_booking_types.booking_type',
                'ref_owner.name AS owner_name',
                'ref_customer.name AS customer_name',
                'ref_supplier.name AS supplier_name'
            ])
            ->from('delivery_orders')
            ->join('bookings', 'delivery_orders.id_booking = bookings.id', 'left')
            ->join('ref_booking_types', 'bookings.id_booking_type = ref_booking_types.id', 'left')
            ->join('ref_people AS ref_owner', 'delivery_orders.id_owner = ref_owner.id', 'left')
            ->join('ref_people AS ref_customer', 'bookings.id_customer = ref_customer.id', 'left')
            ->join('ref_people AS ref_supplier', 'bookings.id_supplier = ref_supplier.id', 'left')
            ->order_by('id', 'DESC');

        if (!empty($branchId)) {
            $deliveryOrders->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $deliveryOrders->where('bookings.id_customer', $customerId);
        }

        return $deliveryOrders;
    }

    /**
     * @param null $bookingId
     * @return mixed
     */
    public function getGoodUnitByBooking($bookingId = null)
    {
        $bookings = $this->db->query("SELECT DISTINCT a.id AS `id_goods`, ru.id AS `id_unit`, ru.`unit` 
            FROM (
                SELECT bg.`id_booking`, g.* FROM booking_goods bg LEFT JOIN ref_goods g ON bg.`id_goods` = g.id
            ) a LEFT JOIN view_conversions vc ON a.id = vc.id_goods LEFT JOIN ref_units ru ON vc.`id_unit_to` = ru.`id` 
            WHERE id_booking = {$bookingId}");

        return $bookings->result_array();
    }

    /**
     * Get all delivery orders with or without deleted records.
     * @param bool $withTrashed
     * @return array
     */
    public function getAllDeliveryOrders($withTrashed = false)
    {
        $deliveryOrders = $this->getDeliveryOrderBaseQuery();

        if (!$withTrashed) {
            $deliveryOrders->where('delivery_orders.is_deleted', false);
        }

        return $deliveryOrders->get()->result_array();
    }

    /**
     * Get all delivery orders with or without deleted records.
     * @param $id
     * @param bool $withTrashed
     * @return array
     */
    public function getDeliveryOrderById($id, $withTrashed = false)
    {
        $deliveryOrder = $this->getDeliveryOrderBaseQuery()->where('delivery_orders.id', $id);

        if (!$withTrashed) {
            $deliveryOrder->where('delivery_orders.is_deleted', false);
        }

        return $deliveryOrder->get()->row_array();
    }

    /**
     * Get all delivery orders with or without deleted records.
     * @param $noDeliveryOrder
     * @param bool $withTrashed
     * @return array
     */
    public function getDeliveryOrderByNo($noDeliveryOrder, $withTrashed = false)
    {
        $deliveryOrder = $this->getDeliveryOrderBaseQuery()->where('no_delivery_order', $noDeliveryOrder);

        if (!$withTrashed) {
            $deliveryOrder->where('delivery_orders.is_deleted', false);
        }

        return $deliveryOrder->get()->row_array();
    }

    /**
     * Get delivery orders by booking with or without deleted records.
     * @param $bookingId
     * @param bool $withTrashed
     * @return array
     */
    public function getDeliveryOrdersByBooking($bookingId, $withTrashed = false)
    {
        $deliveryOrders = $this->getDeliveryOrderBaseQuery()->where('bookings.id', $bookingId);

        if (!$withTrashed) {
            $deliveryOrders->where('delivery_orders.is_deleted', false);
        }

        return $deliveryOrders->get()->result_array();
    }

    /**
     * Get delivery order.
     * @param $ownerId
     * @param null $deliveryOrderId
     * @param bool $isAvailableOut
     * @return array
     */
    public function getDeliveryOrdersByOwner($ownerId = null, $deliveryOrderId = null, $isAvailableOut = false)
    {
        $deliveryOrders = $this->db->select([
            'delivery_orders.id',
            'delivery_orders.no_delivery_order',
            'delivery_orders.id_booking',
            'bookings.no_booking',
            'COUNT(work_order_goods.id) AS total_pallet'
        ])->from('bookings')
            ->join('delivery_orders', 'delivery_orders.id_booking = bookings.id')
            ->join('delivery_order_pallets', 'delivery_orders.id = delivery_order_pallets.id_delivery_order')
            ->join('work_order_goods', 'delivery_order_pallets.no_pallet = work_order_goods.no_pallet')
            ->group_by('delivery_orders.id');

        if (!is_null($ownerId)) {
            $deliveryOrders->where('delivery_orders.id_owner', $ownerId);
        }

        if ($isAvailableOut) {
            $deliveryOrders
                ->join('delivery_orders d2', 'delivery_orders.id = d2.id_delivery_order_in', 'left')
                ->where('d2.id_delivery_order_in IS NULL', NULL);
        }

        if (!is_null($deliveryOrderId)) {
            $deliveryOrders->where('delivery_orders.id', $deliveryOrderId);
            return $deliveryOrders->get()->row_array();
        }

        return $deliveryOrders->get()->result_array();
    }

    /**
     * Get delivery order.
     * @param $supplierId
     * @param null $deliveryOrderId
     * @param bool $isAvailableOut
     * @return array
     */
    public function getDeliveryOrdersBySupplier($supplierId = null, $deliveryOrderId = null, $isAvailableOut = false)
    {
        $deliveryOrders = $this->db->select([
            'delivery_orders.id',
            'delivery_orders.no_delivery_order',
            'delivery_orders.id_booking',
            'bookings.no_booking',
            'COUNT(work_order_goods.id) AS total_pallet'
        ])->from('bookings')
            ->join('delivery_orders', 'delivery_orders.id_booking = bookings.id')
            ->join('delivery_order_pallets', 'delivery_orders.id = delivery_order_pallets.id_delivery_order')
            ->join('work_order_goods', 'delivery_order_pallets.no_pallet = work_order_goods.no_pallet')
            ->join('ownership_histories', 'delivery_orders.id = ownership_histories.id_delivery_order')
            ->group_by('delivery_orders.id');

        if (!is_null($supplierId)) {
            $deliveryOrders->where('ownership_histories.id_owner_to', $supplierId);
        }

        if ($isAvailableOut) {
            $deliveryOrders
                ->join('delivery_orders d2', 'delivery_orders.id = d2.id_delivery_order_in', 'left')
                ->where('d2.id_delivery_order_in IS NULL', NULL);
        }

        if (!is_null($deliveryOrderId)) {
            $deliveryOrders->where('delivery_orders.id', $deliveryOrderId);
            return $deliveryOrders->get()->row_array();
        }

        return $deliveryOrders->get()->result_array();
    }

    /**
     * Get delivery order that not booked out.
     * @param $ownerId
     * @param null $deliveryOrderId
     * @param bool $isAvailable
     * @return array
     */
    public function getDeliveryOrdersHasNotBookedOut($ownerId = null, $deliveryOrderId = null, $isAvailable = false)
    {
        $deliveryOrders = $this->db->select([
            'delivery_orders.id',
            'delivery_orders.no_delivery_order',
            'delivery_orders.id_booking',
            'bookings.no_booking',
            'COUNT(work_order_goods.id) AS total_pallet'
        ])->from('bookings')
            ->join('delivery_orders', 'delivery_orders.id_booking = bookings.id')
            ->join('delivery_order_pallets', 'delivery_orders.id = delivery_order_pallets.id_delivery_order')
            ->join('work_order_goods', 'delivery_order_pallets.no_pallet = work_order_goods.no_pallet')
            ->group_by('delivery_orders.id');

        if (!is_null($ownerId)) {
            $deliveryOrders->where('delivery_orders.id_owner', $ownerId);
        }

        if ($isAvailable) {
            $deliveryOrders
                ->join('delivery_orders d2', 'delivery_orders.id = d2.id_delivery_order_in', 'left')
                ->where('d2.id_delivery_order_in IS NULL', NULL);
        }

        if (!is_null($deliveryOrderId)) {
            $deliveryOrders->where('delivery_orders.id', $deliveryOrderId);
            return $deliveryOrders->get()->row_array();
        }

        return $deliveryOrders->get()->result_array();
    }

    /**
     * Create new delivery order.
     * @param $data
     * @return bool
     */
    public function createDeliveryOrder($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update delivery order.
     * @param $data
     * @param $id
     * @return bool
     */
    public function updateDeliveryOrder($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete delivery data.
     * @param integer $id
     * @param bool $softDelete
     * @return bool
     */
    public function deleteDeliveryOrder($id, $softDelete = true)
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
     * @param $bookingId
     * @return mixed
     */
    public function deleteDeliveryOrderByBooking($bookingId)
    {
        return $this->db->delete($this->table, ['id_booking' => $bookingId]);
    }
}
