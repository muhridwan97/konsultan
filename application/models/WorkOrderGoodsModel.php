<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkOrderGoodsModel extends MY_Model
{
    protected $table = 'work_order_goods';

    /**
     * WorkOrderGoodsModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get basic query work order.
     */
    public function getWorkOrderGoodsBaseQuery()
    {
        $workOrderGoods = $this->db
            ->select([
                $this->table . '.*',
                'ref_goods.id_goods_parent',
                'ref_goods.unit_weight',
                '(' . $this->table . '.quantity * ref_goods.unit_weight) AS total_weight',
                'ref_goods.unit_gross_weight',
                '(' . $this->table . '.quantity * ref_goods.unit_gross_weight) AS total_gross_weight',
                'ref_goods.unit_length',
                'ref_goods.unit_width',
                'ref_goods.unit_height',
                'ref_goods.unit_volume',
                '(' . $this->table . '.quantity * ref_goods.unit_volume) AS total_volume',
                'work_orders.no_work_order',
                'work_orders.completed_at',
                'handlings.no_handling',
                'bookings.id AS id_booking',
                'bookings.no_booking',
                'bookings.no_reference',
                'booking_refs.no_reference AS no_booking_reference',
                'ref_booking_types.booking_type',
                'safe_conducts.no_safe_conduct',
                'safe_conducts.no_police',
                'ref_containers.no_container',
                'ref_goods.name AS goods_name',
                'ref_goods.no_goods',
                'ref_goods.whey_number',
                'ref_units.unit',
                'ref_positions.position',
                'ref_people.name AS owner_name',
                'doc_invoice.no_document AS invoice_number',
                'GROUP_CONCAT(DISTINCT work_order_goods_positions.id_position_block) AS id_position_blocks',
                'GROUP_CONCAT(DISTINCT ref_position_blocks.position_block) AS position_blocks',
                'ref_warehouses.type as type_warehouse',
                'COUNT(work_order_goods_photos.id) AS total_photo'
            ])
            ->from($this->table)
            ->join('bookings AS booking_refs', 'booking_refs.id = work_order_goods.id_booking_reference', 'left')
            ->join('work_orders', 'work_orders.id = ' . $this->table . '.id_work_order')
            ->join('work_order_containers', $this->table . '.id_work_order_container = work_order_containers.id', 'left')
            ->join('work_order_goods_positions', 'work_order_goods_positions.id_work_order_goods = ' . $this->table . '.id', 'left')
            ->join('work_order_goods_photos', 'work_order_goods_photos.id_work_order_goods = ' . $this->table . '.id', 'left')
            ->join('ref_position_blocks', 'ref_position_blocks.id = work_order_goods_positions.id_position_block', 'left')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container', 'left')
            ->join('ref_positions', $this->table . '.id_position = ref_positions.id', 'left')
            ->join('ref_goods', $this->table . '.id_goods = ref_goods.id', 'left')
            ->join('ref_units', $this->table . '.id_unit = ref_units.id', 'left')
            ->join('ref_people', 'ref_people.id = ' . $this->table . '.id_owner', 'left')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling', 'left')
            ->join('bookings', 'bookings.id = handlings.id_booking', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('(
                SELECT id_upload, no_document FROM upload_documents
                INNER JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                WHERE ref_document_types.document_type = "Invoice" AND upload_documents.is_deleted = FALSE
                ) AS doc_invoice', 'doc_invoice.id_upload = bookings.id_upload', 'left')
            ->join('ref_warehouses', 'ref_positions.id_warehouse = ref_warehouses.id', 'left')
            ->group_by($this->table . '.id, doc_invoice.no_document');

        return $workOrderGoods;
    }

    /**
     * Get goods by work order data.
     * @param $workOrderId
     * @param bool $nonContainer
     * @param bool $nonGoods
     * @param bool $withTrash
     * @return mixed
     */
    public function getWorkOrderGoodsByWorkOrder($workOrderId, $nonContainer = false, $nonGoods = false, $withTrash = false)
    {
        $workOrderGoods = $this->getWorkOrderGoodsBaseQuery()
            ->select([
                $this->table . '.unit_weight AS unit_weight_item',
                '(' . $this->table . '.quantity * ' . $this->table . '.unit_weight) AS total_weight_item',
                $this->table . '.unit_gross_weight AS unit_gross_weight_item',
                '(' . $this->table . '.quantity * ' . $this->table . '.unit_gross_weight) AS total_gross_weight_item',
                $this->table . '.unit_length AS unit_length_item',
                $this->table . '.unit_width AS unit_width_item',
                $this->table . '.unit_height AS unit_height_item',
                $this->table . '.unit_volume AS unit_volume_item',
                '(' . $this->table . '.quantity * ' . $this->table . '.unit_volume) AS total_volume_item',
            ])
            ->where($this->table . '.id_work_order', $workOrderId);

        if ($nonContainer) {
            $workOrderGoods->where($this->table . '.id_work_order_container IS NULL');
        }

        if ($nonGoods) {
            $workOrderGoods->where($this->table . '.id_work_order_goods IS NULL');
        }

        if (!$withTrash) {
            $workOrderGoods->where($this->table . '.is_deleted', false);
        }

        return $workOrderGoods->get()->result_array();
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
        $baseQuery = $this->getWorkOrderGoodsBaseQuery();

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
     * Get goods by work order data.
     * @param $workOrderId
     * @param bool $nonContainer
     * @param bool $nonGoods
     * @param bool $withTrash
     * @return mixed
     */
    public function getNotSetStatusOvertimeWorkOrderGoodsByWorkOrder($workOrderId, $nonContainer = false, $nonGoods = false, $withTrash = false)
    {
        $workOrderGoods = $this->getWorkOrderGoodsBaseQuery()
            ->where('work_order_goods.id_work_order', $workOrderId)->where('work_order_containers.overtime_status', '0');

        if ($nonContainer) {
            $workOrderGoods->where('work_order_goods.id_work_order_container IS NULL');
        }

        if ($nonGoods) {
            $workOrderGoods->where('work_order_goods.id_work_order_goods IS NULL');
        }

        if (!$withTrash) {
            $workOrderGoods->where('work_order_goods.is_deleted', false);
        }

        return $workOrderGoods->get()->result_array();
    }

    /**
     * Get work order goods by work order container.
     * @param $workOrderContainerId
     * @param bool $withTrash
     * @return array
     */
    public function getWorkOrderGoodsByWorkOrderContainer($workOrderContainerId, $withTrash = false)
    {
        $workOrderGoods = $this->getWorkOrderGoodsBaseQuery()
            ->where($this->table . '.id_work_order_container', $workOrderContainerId);

        if (!$withTrash) {
            $workOrderGoods->where($this->table . '.is_deleted', false);
        }

        return $workOrderGoods->get()->result_array();
    }

    /**
     * TODO: get real stock
     * Get booking stock.
     * @param $bookingId
     * @return array
     */
    public function getGoodsStocksByBooking($bookingId, $withTrash = false)
    {
        $goods = $this->getWorkOrderGoodsBaseQuery()->where('bookings.id', $bookingId);

        if (!$withTrash) {
            $goods->where('work_order_goods.is_deleted', false);
        }

        return $goods->get()->result_array();
    }

    /**
     * Get work order goods by work order container.
     * @param $workOrderGoodsId
     * @param bool $withTrash
     * @return array
     */
    public function getWorkOrderGoodsByWorkOrderGoods($workOrderGoodsId, $withTrash = false)
    {
        $workOrderGoods = $this->getWorkOrderGoodsBaseQuery()
            ->where('work_order_goods.id_work_order_goods', $workOrderGoodsId);

        if (!$withTrash) {
            $workOrderGoods->where('work_order_goods.is_deleted', false);
        }

        return $workOrderGoods->get()->result_array();
    }

    /**
     * Get work order goods by id.
     * @param $workOrderGoodsId
     * @param bool $withTrash
     * @return array
     */
    public function getWorkOrderGoodsById($workOrderGoodsId, $withTrash = false)
    {
        $workOrderItem = $this->getWorkOrderGoodsBaseQuery()
            ->where('work_order_goods.id', $workOrderGoodsId);

        if (!$withTrash) {
            $workOrderItem->where('work_order_goods.is_deleted', false);
        }

        return $workOrderItem->get()->row_array();
    }
    /**
     * Get work order goods by booking id.
     * @param $bookingId
     * @param bool $withTrash
     * @return array
     */
    public function getWorkOrderGoodsByBookingId($bookingId, $withTrash = false)
    {
        $workOrderItem = $this->getWorkOrderGoodsBaseQuery()
            ->where('bookings.id', $bookingId);

        if (!$withTrash) {
            $workOrderItem->where('work_order_goods.is_deleted', false);
        }

        return $workOrderItem->get()->result_array();
    }

    /**
     * Get invalid work order goods.
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getInvalidWorkOrderGoods($filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();
        $outboundHandlingId = get_setting('default_outbound_handling');

        $baseQuery = $this->db
            ->select([
                'bookings.id AS id_booking',
                'bookings.no_booking',
                'bookings.no_reference',
                'GROUP_CONCAT(DISTINCT booking_inbounds.no_reference) AS no_reference_inbounds',
                'ref_people.name AS customer_name',
                'work_orders.id AS id_work_order',
                'work_orders.no_work_order',
                'work_orders.status',
                'work_orders.completed_at',
                'ref_goods.no_goods',
                'ref_goods.name AS goods_name',
                'work_order_goods.no_pallet',
                'work_order_goods.quantity',
            ])
            ->from($this->table)
            ->join('work_orders', 'work_orders.id = ' . $this->table . '.id_work_order')
            ->join('ref_goods', $this->table . '.id_goods = ref_goods.id', 'left')
            ->join('ref_units', $this->table . '.id_unit = ref_units.id', 'left')
            ->join('ref_people', 'ref_people.id = ' . $this->table . '.id_owner', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling', 'left')
            ->join('bookings', 'bookings.id = handlings.id_booking', 'left')
            ->join('booking_references', 'booking_references.id_booking = bookings.id', 'left')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = booking_references.id_booking_reference', 'left')
            ->where([
                'bookings.is_deleted' => false,
                'handlings.is_deleted' => false,
                'work_orders.is_deleted' => false,
                'work_order_goods.is_deleted' => false,
                'handlings.id_handling_type' => $outboundHandlingId,
                'work_order_goods.id_booking_reference IS NULL' => null,
                'work_orders.completed_at>=' => '2021-01-01'
            ])
            ->group_by('bookings.id');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get work order goods by range time on work order completed INBOUND.
     * @param $workOrderGoodsId
     * @param bool $withTrash
     * @return array
     */
    public function getWorkOrderGoodsByRangeTime($start, $end, $branchId = null, $withTrash = false)
    {
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $branchId = !empty($branchId) ? $branchId : get_active_branch_id();
        $workOrderItem = $this->getWorkOrderGoodsBaseQuery()
            ->join('status_histories',"status_histories.id_reference = work_orders.id AND status_histories.type = '".StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION."'",'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->where('ref_handling_types.multiplier_goods', 1)
            ->where('status_histories.created_at >= ', $start)
            ->where('status_histories.created_at <= ', $end)
            ->where('status_histories.status', 'APPROVED')
            ->where('ref_booking_types.category', 'INBOUND')
            ->where('bookings.id_branch', $branchId);

        if (!$withTrash) {
            $workOrderItem->where('work_order_goods.is_deleted', false);
        }

        return $workOrderItem->get()->result_array();
    }

    /**
     * Get unpackage quantity.
     *
     * @param $goodsId
     * @param null $bookingId
     * @return array|array[]
     */
    public function getWorkOrderUnpackageQuantity($goodsId, $bookingId = null)
    {
        $baseQuery = $this->db
            ->select(['SUM(work_order_goods.quantity) AS total'])
            ->from('handlings')
            ->join('work_orders', 'work_orders.id_handling = handlings.id')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->where('ref_handling_types.id', 27)
            ->where("EXISTS(
                SELECT id FROM handling_goods AS related_goods
                WHERE related_goods.id_goods = '{$goodsId}'
                    AND related_goods.id_handling = handlings.id
            )");

        if (!empty($bookingId)) {
            $baseQuery->where('handlings.id_booking', $bookingId);
        }

        return $baseQuery->get()->row_array();
    }

    /**
     * Get auto number for pallet.
     * @return string
     */
    public function getAutoNumberPallet()
    {
        $orderData = $this->db->query("
            SELECT IFNULL(CAST(RIGHT(no_pallet, 6) AS UNSIGNED), 0) + 1 AS order_number 
            FROM work_order_goods 
            WHERE SUBSTR(no_pallet, 7, 2) = MONTH(NOW()) 
			AND SUBSTR(no_pallet, 4, 2) = DATE_FORMAT(NOW(), '%y')
            ORDER BY SUBSTR(no_pallet FROM 4) DESC LIMIT 1
        ");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return 'PL/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }

    /**
     * Insert single or batch data goods.
     * @param $data
     * @return bool|int
     */
    public function insertWorkOrderGoods($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update work order goods data.
     * @param $data
     * @param $id
     * @return bool
     */
    public function updateWorkOrderGoods($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete work order goods by specific work order.
     * @param $workOrderId
     * @param bool $softDelete
     * @return mixed
     */
    public function deleteGoodsByWorkOrder($workOrderId, $softDelete = true)
    {
        if ($softDelete) {
            return $this->db->update($this->table, [
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => UserModel::authenticatedUserData('id')
            ], ['id_work_order' => $workOrderId]);
        }
        return $this->db->delete($this->table, ['id_work_order' => $workOrderId]);
    }

    /**
     * Delete work order goods by specific Id.
     * @param $Id
     * @param bool $softDelete
     * @return mixed
     */
    public function deleteGoodsById($Id, $softDelete = true)
    {
        if ($softDelete) {
            return $this->db->update($this->table, [
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => UserModel::authenticatedUserData('id')
            ], ['id' => $Id]);
        }
        return $this->db->delete($this->table, ['id' => $Id]);
    }
}
