<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class BookingGoodsModel
 * @property GoodsModel $goods
 * @property ReportStockModel $reportStock
 * @property AssemblyGoodsModel $assemblyGoods
 */
class BookingGoodsModel extends MY_Model
{
    protected $table = 'booking_goods';

    /**
     * BookingGoodsModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        $bookings = $this->db
            ->select([
                'booking_goods.*',
                'bookings.no_reference',
                'booking_refs.no_reference AS no_booking_reference',
                'booking_goods.unit_weight',
                '(booking_goods.quantity * booking_goods.unit_weight) AS total_weight',
                'booking_goods.unit_gross_weight',
                '(booking_goods.quantity * booking_goods.unit_gross_weight) AS total_gross_weight',
                'booking_goods.unit_length',
                'booking_goods.unit_width',
                'booking_goods.unit_height',
                'booking_goods.unit_volume',
                '(booking_goods.quantity * booking_goods.unit_volume) AS total_volume',
                'ref_goods.no_goods',
                'ref_goods.name AS goods_name',
                'ref_goods.whey_number',
                'ref_units.unit',
                'ref_positions.position',
                'ref_containers.no_container AS booking_no_container'
            ])
            ->from($this->table)
            ->join('bookings', 'bookings.id = booking_goods.id_booking', 'left')
            ->join('bookings AS booking_refs', 'booking_refs.id = booking_goods.id_booking_reference', 'left')
            ->join('booking_containers', 'booking_containers.id = booking_goods.id_booking_container', 'left')
            ->join('ref_containers', 'ref_containers.id = booking_containers.id_container', 'left')
            ->join('ref_goods', 'booking_goods.id_goods = ref_goods.id', 'left')
            ->join('ref_units', 'booking_goods.id_unit = ref_units.id', 'left')
            ->join('ref_positions', 'booking_goods.id_position = ref_positions.id', 'left');

        return $bookings;
    }

    /**
     * Get single data booking goods.
     * @param $id
     * @return mixed
     */
    public function getBookingGoodsById($id)
    {
        $goods = $this->getBaseQuery()->where('booking_goods.id', $id);

        return $goods->get()->row_array();
    }

    /**
     * Get booking goods by booking id.
     * @param integer $bookingId
     * @param bool $nonContainer
     * @return array
     */
    public function getBookingGoodsByBooking($bookingId, $nonContainer = false)
    {
        $goods = $this->getBaseQuery()->where('booking_goods.id_booking', $bookingId);

        if ($nonContainer) {
            $goods->where('booking_goods.id_booking_container IS NULL');
        }

        return $goods->get()->result_array();
    }

    /**
     * Get booking goods available for create safe conduct.
     * @param integer $bookingId
     * @param bool $nonContainer
     * @param null $exceptSafeConductId
     * @return array
     */
    public function getBookingGoodsByBookingSafeConduct($bookingId, $nonContainer = false, $exceptSafeConductId = null)
    {
        $exceptCondition = '';
        if (!empty($exceptSafeConductId)) {
            $exceptCondition = "AND safe_conducts.id != '" . $exceptSafeConductId . "'";
        }

        $goods = $this->getBaseQuery()
            ->select([
                'IFNULL(booking_goods.quantity - SUM(safe_conduct_goods.quantity), booking_goods.quantity) AS left_quantity',
                'IFNULL(booking_goods.quantity - SUM(safe_conduct_goods.quantity), booking_goods.quantity) * ref_goods.unit_weight AS left_weight',
                'IFNULL(booking_goods.quantity - SUM(safe_conduct_goods.quantity), booking_goods.quantity) * ref_goods.unit_gross_weight AS left_gross_weight',
                'IFNULL(booking_goods.quantity - SUM(safe_conduct_goods.quantity), booking_goods.quantity) * ref_goods.unit_volume AS left_volume',
            ])
            ->join('(
                SELECT * FROM safe_conducts 
                WHERE safe_conducts.is_deleted = false ' . $exceptCondition . '
            ) AS safe_conducts', 'safe_conducts.id_booking = booking_goods.id_booking', 'left')
            ->join('safe_conduct_goods', 'safe_conduct_goods.id_safe_conduct = safe_conducts.id 
                AND booking_goods.id_goods = safe_conduct_goods.id_goods 
                    AND booking_goods.id_unit = safe_conduct_goods.id_unit
                        AND booking_goods.no_pallet = safe_conduct_goods.no_pallet 
                                AND IFNULL(booking_goods.ex_no_container, "") = IFNULL(safe_conduct_goods.ex_no_container, "")', 'left')
            ->where('booking_goods.id_booking', $bookingId)
            ->group_by('booking_goods.id')
            ->having('left_quantity > 0');

        if ($nonContainer) {
            $goods->where('booking_goods.id_booking_container IS NULL');
        }

        return $goods->get()->result_array();
    }

    /**
     * Get booking goods available for create handling.
     * @param integer $bookingId
     * @param bool $nonContainer
     * @param string $type
     * @return array
     */
    public function getBookingGoodsByBookingHandling($bookingId, $nonContainer = false, $type = 'INBOUND')
    {
        $handlingTypeId = get_setting('default_outbound_handling');
        if ($type == 'INBOUND') {
            $handlingTypeId = get_setting('default_inbound_handling');
        }
        /*
        $goods = $this->getBaseQuery()
            ->select([
                'IFNULL(booking_goods.quantity - SUM(handling_goods.quantity), booking_goods.quantity) AS left_quantity',
                'IFNULL(booking_goods.quantity - SUM(handling_goods.quantity), booking_goods.quantity) * ref_goods.unit_weight AS left_weight',
                'IFNULL(booking_goods.quantity - SUM(handling_goods.quantity), booking_goods.quantity) * ref_goods.unit_gross_weight AS left_gross_weight',
                'IFNULL(booking_goods.quantity - SUM(handling_goods.quantity), booking_goods.quantity) * ref_goods.unit_volume AS left_volume',
            ])
            ->join('(
                SELECT * FROM handlings 
                WHERE status = "APPROVED" 
                AND id_handling_type = "' . $handlingTypeId . '"
            ) AS handlings', 'booking_goods.id_booking = handlings.id_booking', 'left')
            ->join('(
                SELECT DISTINCT
                    handling_goods.id_handling,
                    handling_goods.id_goods,
                    handling_goods.id_unit,
                    handling_goods.no_pallet,
                    handling_goods.ex_no_container,
                    IFNULL(work_order_goods.quantity, handling_goods.quantity) AS quantity
                FROM handling_goods
                LEFT JOIN (
                    SELECT work_orders.* FROM work_orders 
                    INNER JOIN handlings ON handlings.id = work_orders.id_handling
                    INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                    WHERE multiplier_goods = -1 AND work_orders.is_deleted = false AND handlings.status = "APPROVED") AS work_orders ON work_orders.id_handling = handling_goods.id_handling
                LEFT JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id 
                    AND work_order_goods.id_goods = handling_goods.id_goods 
                        AND work_order_goods.id_unit = handling_goods.id_unit
                            AND work_order_goods.no_pallet = handling_goods.no_pallet 
                                AND IFNULL(work_order_goods.ex_no_container, "") = IFNULL(handling_goods.ex_no_container, "")
            ) AS handling_goods', 'handling_goods.id_handling = handlings.id 
                AND booking_goods.id_goods = handling_goods.id_goods 
                    AND booking_goods.id_unit = handling_goods.id_unit
                        AND booking_goods.no_pallet = handling_goods.no_pallet 
                                AND IFNULL(booking_goods.ex_no_container, "") = IFNULL(handling_goods.ex_no_container, "")', 'left')
            ->where('booking_goods.id_booking', $bookingId)
            ->group_by('booking_goods.id')
            ->having('left_quantity > 0');
        */

        $goods = $this->getBaseQuery()
            ->select([
                'IFNULL(booking_goods.quantity - SUM(handling_goods.quantity), booking_goods.quantity) AS left_quantity',
                'IFNULL(booking_goods.quantity - SUM(handling_goods.quantity), booking_goods.quantity) * ref_goods.unit_weight AS left_weight',
                'IFNULL(booking_goods.quantity - SUM(handling_goods.quantity), booking_goods.quantity) * ref_goods.unit_gross_weight AS left_gross_weight',
                'IFNULL(booking_goods.quantity - SUM(handling_goods.quantity), booking_goods.quantity) * ref_goods.unit_volume AS left_volume',
            ])
            ->join('(
                SELECT * FROM handlings 
                WHERE status = "APPROVED" 
                AND id_handling_type = "' . $handlingTypeId . '" AND is_deleted = false
            ) AS handlings', 'booking_goods.id_booking = handlings.id_booking', 'left')
            // job goods quantity [UNION] ALL handling goods have not work order yet
            ->join('(
                SELECT handlings.id AS id_handling, id_goods, id_unit, no_pallet, ex_no_container, quantity 
                FROM handlings
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE handlings.id_booking = "' . $bookingId . '"
                
                UNION ALL
                
                SELECT id_handling, id_goods, id_unit, no_pallet, ex_no_container, quantity 
                FROM (
                    SELECT handling_goods.*, work_order_goods.id AS id_detail 
                    FROM handlings
                    INNER JOIN handling_goods ON handling_goods.id_handling = handlings.id
                    LEFT JOIN work_orders ON work_orders.id_handling = handlings.id
                    LEFT JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                    WHERE handlings.id_booking = "' . $bookingId . '"
                    HAVING id_detail IS NULL
                ) AS handling_goods
            ) AS handling_goods', 'handling_goods.id_handling = handlings.id 
                AND booking_goods.id_goods = handling_goods.id_goods 
                    AND booking_goods.id_unit = handling_goods.id_unit
                        AND booking_goods.no_pallet = handling_goods.no_pallet 
                                AND IFNULL(booking_goods.ex_no_container, "") = IFNULL(handling_goods.ex_no_container, "")', 'left')
            ->where('booking_goods.id_booking', $bookingId)
            ->group_by('booking_goods.id')
            ->having('left_quantity > 0');

        if ($nonContainer) {
            $goods->where('booking_goods.id_booking_container IS NULL');
        }

        return $goods->get()->result_array();
    }

    /**
     * Get booking goods available for create handling.
     * @param integer $bookingId
     * @param bool $nonContainer
     * @param string $type
     * @return array
     */
    public function getBookingGoodsByBookingHandlingWorkOrderUncompleted($bookingId, $type = 'INBOUND')
    {
        $handlingTypeId = get_setting('default_outbound_handling');
        if ($type == 'INBOUND') {
            $handlingTypeId = get_setting('default_inbound_handling');
        }

        $goods = $this->db
            ->select([
                'booking_goods.*',
                'booking_goods.unit_weight',
                '(booking_goods.quantity * booking_goods.unit_weight) AS total_weight',
                'booking_goods.unit_gross_weight',
                '(booking_goods.quantity * booking_goods.unit_gross_weight) AS total_gross_weight',
                'booking_goods.unit_length',
                'booking_goods.unit_width',
                'booking_goods.unit_height',
                'booking_goods.unit_volume',
                '(booking_goods.quantity * booking_goods.unit_volume) AS total_volume',
                'ref_goods.no_goods',
                'ref_goods.name AS goods_name',
                'ref_goods.whey_number',
            ])
            ->from($this->table)
            ->join('ref_goods', 'booking_goods.id_goods = ref_goods.id', 'left')
            ->select([
                'IFNULL(booking_goods.quantity - SUM(handling_goods.quantity), booking_goods.quantity) AS left_quantity',
            ])
            ->join('(
                SELECT * FROM handlings 
                WHERE status = "APPROVED" 
                AND id_handling_type = "' . $handlingTypeId . '" AND is_deleted = false
            ) AS handlings', 'booking_goods.id_booking = handlings.id_booking', 'left')
            // job goods quantity [UNION] ALL handling goods have not work order yet
            ->join('(
                SELECT handlings.id AS id_handling, id_goods, id_unit, no_pallet, ex_no_container, quantity 
                FROM handlings
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE work_orders.status = "COMPLETED"
                
                UNION ALL
                
                SELECT id_handling, id_goods, id_unit, no_pallet, ex_no_container, quantity 
                FROM (
                    SELECT handling_goods.*, work_order_goods.id AS id_detail 
                    FROM handlings
                    INNER JOIN handling_goods ON handling_goods.id_handling = handlings.id
                    LEFT JOIN work_orders ON work_orders.id_handling = handlings.id
                    LEFT JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                    WHERE work_orders.status = "COMPLETED"
                    HAVING id_detail IS NULL
                ) AS handling_goods
            ) AS handling_goods', 'handling_goods.id_handling = handlings.id 
                AND booking_goods.id_goods = handling_goods.id_goods 
                    AND booking_goods.id_unit = handling_goods.id_unit
                        AND booking_goods.no_pallet = handling_goods.no_pallet 
                                AND IFNULL(booking_goods.ex_no_container, "") = IFNULL(handling_goods.ex_no_container, "")', 'left')
            ->where('booking_goods.id_booking', $bookingId)
            ->group_by('booking_goods.id')
            ->having('left_quantity > 0');

        return $goods->get()->result_array();
    }

    /**
     * @param $bookingId
     * @return array
     */
    public function getStockDiscrepancy($bookingId)
    {
        $this->load->model('ReportStockModel', 'reportStock');
        $this->load->model('GoodsModel', 'goods');
        $this->load->model('AssemblyGoodsModel', 'assemblyGoods');

        // check if booking is discrepancy with stock
        $itemCollections = [];
        $bookingGoods = $this->getBookingGoodsByBooking($bookingId);
        foreach ($bookingGoods as $bookingItem) {
            $goods = $this->goods->getById($bookingItem['id_goods']);
            if (empty($goods['id_assembly'])) {
                $itemCollections[] = [
                    'assembly_type' => 'NON ASSEMBLY',
                    'id_goods' => $bookingItem['id_goods'],
                    'no_goods' => $bookingItem['no_goods'],
                    'id_unit' => $bookingItem['id_unit'],
                    'unit' => $bookingItem['unit'],
                    'goods_name' => $bookingItem['goods_name'],
                    'quantity' => $bookingItem['quantity']
                ];
            } else {
                $assemblyGoods = $this->assemblyGoods->getBy([
                    'ref_assembly_goods.id_assembly' => $goods['id_assembly']
                ]);
                if (count($assemblyGoods) > 1) {
                    foreach ($assemblyGoods as $assemblyItem) {
                        $itemCollections[] = [
                            'assembly_type' => 'ASSEMBLY',
                            'id_goods' => $assemblyItem['id_goods'],
                            'no_goods' => $assemblyItem['no_goods'],
                            'goods_name' => $assemblyItem['goods_name'],
                            'id_unit' => $assemblyItem['id_unit'],
                            'unit' => $assemblyItem['unit'],
                            'quantity' => $assemblyItem['quantity']
                        ];
                    }
                } else {
                    // if single conversion unit package, then calculate tha package itself rather than the item assemblies
                    $itemCollections[] = [
                        'assembly_type' => 'PACKAGE',
                        'id_goods' => $bookingItem['id_goods'],
                        'no_goods' => $bookingItem['no_goods'],
                        'goods_name' => $bookingItem['goods_name'],
                        'id_unit' => $bookingItem['id_unit'],
                        'unit' => $bookingItem['unit'],
                        'quantity' => $bookingItem['quantity']
                    ];
                }
            }
        }

        $stocks = $this->reportStock->getStockGoodsBooking($bookingId, true);

        // check all items in booking in already in stock
        $discrepancyLists = [];
        foreach ($itemCollections as $bookingItem) {
            $itemFound = false;
            foreach ($stocks as $stock) {
                if ($stock['id_goods'] == $bookingItem['id_goods'] && $stock['id_unit'] == $bookingItem['id_unit']) {
                    $itemFound = true;
                    if ($stock['stock_quantity'] != $bookingItem['quantity']) {
                        $discrepancyLists[] = [
                            'source' => 'BOOKING',
                            'id_goods' => $bookingItem['id_goods'],
                            'no_goods' => $bookingItem['no_goods'],
                            'goods_name' => $bookingItem['goods_name'],
                            'id_unit' => $bookingItem['id_unit'],
                            'unit' => $bookingItem['unit'],
                            'assembly_type' => $bookingItem['assembly_type'],
                            'stock_exist' => true,
                            'quantity_booking' => $bookingItem['quantity'],
                            'quantity_stock' => $stock['stock_quantity'],
                            'quantity_difference' => $stock['stock_quantity'] - $bookingItem['quantity']
                        ];
                    }
                    break;
                }
            }
            if (!$itemFound) {
                $discrepancyLists[] = [
                    'source' => 'BOOKING',
                    'id_goods' => $bookingItem['id_goods'],
                    'no_goods' => $bookingItem['no_goods'],
                    'goods_name' => $bookingItem['goods_name'],
                    'id_unit' => $bookingItem['id_unit'],
                    'unit' => $bookingItem['unit'],
                    'assembly_type' => $bookingItem['assembly_type'],
                    'stock_exist' => false,
                    'quantity_booking' => $bookingItem['quantity'],
                    'quantity_stock' => 0,
                    'quantity_difference' => $bookingItem['quantity']
                ];
            }
        }

        // check all stock goods that exists over booking
        foreach ($stocks as $stock) {
            $isFound = false;
            foreach ($itemCollections as $bookingItem) {
                if ($stock['id_goods'] == $bookingItem['id_goods'] && $stock['id_unit'] == $bookingItem['id_unit']) {
                    $isFound = true;
                    break;
                }
            }
            if (!$isFound) {
                $goods = $this->goods->getById($stock['id_goods']);
                $discrepancyLists[] = [
                    'source' => 'STOCK',
                    'id_goods' => $stock['id_goods'],
                    'no_goods' => $stock['no_goods'],
                    'goods_name' => $stock['goods_name'],
                    'id_unit' => $stock['id_unit'],
                    'unit' => $stock['unit'],
                    'assembly_type' => empty($goods['id_assembly']) ? 'NON ASSEMBLY' : 'PACKAGE',
                    'stock_exist' => true,
                    'quantity_booking' => 0,
                    'quantity_stock' => $stock['stock_quantity'],
                    'quantity_difference' => $stock['stock_quantity'],
                ];
            }
        }

        return $discrepancyLists;
    }

    /**
     * Get booking goods by booking container
     * @param $bookingContainerId
     * @return mixed
     */
    public function getBookingGoodsByBookingContainer($bookingContainerId)
    {
        $goods = $this->getBaseQuery()->where('booking_goods.id_booking_container', $bookingContainerId);

        return $goods->get()->result_array();
    }

    /**
     * Insert single or batch booking goods.
     * @param $data
     * @return bool
     */
    public function createBookingGoods($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update or replace booking goods.
     * @param $data
     * @param $id
     * @return bool
     */
    public function updateBookingGoods($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete booking goods by booking id.
     * @param $bookingId
     * @return mixed
     */
    public function deleteBookingGoodsByBooking($bookingId)
    {
        return $this->db->delete($this->table, ['id_booking' => $bookingId]);
    }
}
