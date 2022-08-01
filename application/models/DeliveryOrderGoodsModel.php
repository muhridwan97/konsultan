<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DeliveryOrderGoodsModel extends CI_Model
{
    private $table = 'delivery_order_goods';

    /**
     * DeliveryOrderGoodsModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active record query builder for all related DO goods data selection.
     * @return CI_DB_query_builder
     */
    private function getBasicDOGoodsQuery()
    {
        $deliveryGoods = $this->db
            ->select([
                'bookings.id AS id_booking',
                'delivery_orders.no_delivery_order',
                'delivery_order_goods.*',
                'ref_units.unit AS unit',
                'ref_goods.name AS goods_name
            '])
            ->from($this->table)
            ->join('delivery_orders', 'delivery_orders.id = delivery_order_goods.id_delivery_order', 'left')
            ->join('bookings', 'delivery_orders.id_booking = bookings.id', 'left')
            ->join('ref_units', 'delivery_order_goods.id_unit = ref_units.id', 'left')
            ->join('ref_goods', 'delivery_order_goods.id_goods = ref_goods.id', 'left');

        return $deliveryGoods;
    }

    /**
     * Get all delivery order goods with or without deleted records.
     * @param $deliveryOrderId
     * @param bool $withTrashed
     * @return array
     */
    public function getDeliveryOrderGoodsByDo($deliveryOrderId, $withTrashed = false)
    {
        $deliveryGoods = $this->getBasicDOGoodsQuery()->where('id_delivery_order', $deliveryOrderId);

        if (!$withTrashed) {
            $deliveryGoods->where('delivery_orders.is_deleted', false);
        }

        return $deliveryGoods->get()->result_array();
    }

    /**
     * Get delivery order goods by specific booking.
     * @param $bookingId
     * @param bool $withTrashed
     * @return mixed
     */
    public function getDeliveryOrderGoodsByBooking($bookingId, $withTrashed = false)
    {
        $deliveryOrderGoods = $this->getBasicDOGoodsQuery()->where('delivery_orders.id_booking', $bookingId);

        if (!$withTrashed) {
            $deliveryOrderGoods->where('delivery_orders.is_deleted', false);
        }

        return $deliveryOrderGoods->get()->result_array();
    }

    /**
     * Get booking goods available for create safe conduct.
     * @param integer $bookingId
     * @param null $exceptSafeConductId
     * @return array
     */
    public function getDeliveryOrderGoodsByBookingSafeConduct($bookingId, $exceptSafeConductId = null)
    {
        $exceptCondition = '';
        if (!empty($exceptSafeConductId)) {
            $exceptCondition = "AND safe_conduct_goods.id_safe_conduct != '" . $exceptSafeConductId . "'";
        }

        $goods = $this->getBasicDOGoodsQuery()
            ->select([
                'IFNULL((delivery_order_goods.quantity - SUM(safe_conduct_goods.quantity)), delivery_order_goods.quantity) AS left_quantity',
                'IFNULL((delivery_order_goods.tonnage - SUM(safe_conduct_goods.tonnage)), delivery_order_goods.tonnage) AS left_tonnage',
                'IFNULL((delivery_order_goods.volume - SUM(safe_conduct_goods.volume)), delivery_order_goods.volume) AS left_volume'
            ])
            ->join('(
                SELECT safe_conduct_goods.* 
                FROM safe_conduct_goods
	            LEFT JOIN safe_conducts ON safe_conduct_goods.id_safe_conduct = safe_conducts.id
	            WHERE safe_conducts.is_deleted = false ' . $exceptCondition . '
                ) AS safe_conduct_goods', 'delivery_order_goods.id = safe_conduct_goods.id_delivery_order_goods', 'left')
            ->where('bookings.id', $bookingId)
            ->group_by('delivery_order_goods.id')
            ->having('left_quantity > 0');

        return $goods->get()->result_array();
    }

    /**
     * Get booking goods available for create handling.
     * @param integer $bookingId
     * @param string $type
     * @return array
     */
    public function getDeliveryOrderGoodsByBookingHandling($bookingId, $type = 'INBOUND')
    {
        $handlingTypeId = get_setting('default_outbound_handling');
        if ($type == 'INBOUND') {
            $handlingTypeId = get_setting('default_inbound_handling');
        }

        $goods = $this->getBasicDOGoodsQuery()
            ->select([
                'IFNULL((delivery_order_goods.quantity - SUM(handling_goods.quantity)), delivery_order_goods.quantity) AS left_quantity',
                'IFNULL((delivery_order_goods.tonnage - SUM(handling_goods.tonnage)), delivery_order_goods.tonnage) AS left_tonnage',
                'IFNULL((delivery_order_goods.volume - SUM(handling_goods.volume)), delivery_order_goods.volume) AS left_volume'
            ])
            ->join('handlings', 'bookings.id = handlings.id_booking', 'left')
            ->join('handling_goods', 'handling_goods.id_handling = handlings.id AND delivery_order_goods.id_goods = handling_goods.id_goods', 'left')
            ->where('bookings.id', $bookingId)
            ->where('handlings.id_handling_type', $handlingTypeId)
            ->group_by('delivery_order_goods.id')
            ->having('left_quantity > 0');

        return $goods->get()->result_array();
    }

    /**
     * Get delivery order goods by specific booking.
     * @param $id
     * @param bool $withTrashed
     * @return mixed
     */
    public function getDeliveryOrderGoodsById($id, $withTrashed = false)
    {
        $deliveryOrderGood = $this->getBasicDOGoodsQuery()->where('delivery_order_goods.id', $id);

        if (!$withTrashed) {
            $deliveryOrderGood->where('delivery_orders.is_deleted', false);
        }

        return $deliveryOrderGood->get()->row_array();
    }

    /**
     * Create new delivery order.
     * @param $data
     * @return bool
     */
    public function createDeliveryOrderGoods($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Delete delivery order goods by delivery order.
     * @param $deliveryOrderId
     * @return mixed
     */
    public function deleteBookingGoodsByBooking($deliveryOrderId)
    {
        return $this->db->delete($this->table, ['id_delivery_order' => $deliveryOrderId]);
    }
}
