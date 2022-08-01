<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class DashboardModel
 * @property ReportStockModel $reportStock
 */
class DashboardModel extends CI_Model
{
    /**
     * DocumentTypeModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getTotalStock()
    {
        $branchId = get_active_branch_id();

        $stockContainer = $this->db
            ->select([
                'booking_inbounds.id_customer',
                'booking_inbounds.id AS id_booking',
                'work_order_containers.id_container',
                'SUM(CAST(work_order_containers.quantity AS SIGNED) * multiplier_container) AS stock',
            ])
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('booking_references', 'booking_references.id_booking = bookings.id', 'left')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(booking_references.id_booking_reference, bookings.id)', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id AND IFNULL(work_order_containers.id_booking_reference, bookings.id) = booking_inbounds.id')
            ->where([
                'bookings.is_deleted' => false,
                'handlings.status' => 'APPROVED',
                'handlings.is_deleted' => false,
                'work_orders.is_deleted' => false,
                'work_order_containers.is_deleted' => false,
                'ref_handling_types.is_deleted' => false,
                'work_orders.status' => 'COMPLETED',
                'bookings.id_branch' => $branchId,
            ])
            ->group_by('booking_inbounds.id, booking_inbounds.id_customer, work_order_containers.id_container')
            ->having('stock > 0')
            ->count_all_results();

        $stockGoods = $this->db
            ->select([
                'booking_inbounds.id_customer',
                'booking_inbounds.id AS id_booking',
                'work_order_goods.id_goods',
                'work_order_goods.id_unit',
                'work_order_goods.ex_no_container',
                'SUM(CAST(work_order_goods.quantity AS SIGNED) * multiplier_container) AS stock',
            ])
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('booking_references', 'booking_references.id_booking = bookings.id', 'left')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(booking_references.id_booking_reference, bookings.id)', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id AND IFNULL(work_order_goods.id_booking_reference, bookings.id) = booking_inbounds.id')
            ->where([
                'bookings.is_deleted' => false,
                'handlings.status' => 'APPROVED',
                'handlings.is_deleted' => false,
                'work_orders.is_deleted' => false,
                'work_order_goods.is_deleted' => false,
                'ref_handling_types.is_deleted' => false,
                'work_orders.status' => 'COMPLETED',
                'bookings.id_branch' => $branchId,
            ])
            ->group_by('booking_inbounds.id, booking_inbounds.id_customer, work_order_goods.id_goods, work_order_goods.id_unit, work_order_goods.ex_no_container')
            ->having('stock > 0')
            ->count_all_results();

        return $stockContainer + $stockGoods;
    }

    /**
     * Get all statistic summary type
     * @param $customerId
     * @return mixed
     */
    public function getStatisticSummary($customerId)
    {
        $branchId = get_active_branch('id');
        $userType = UserModel::authenticatedUserData('user_type');

        // booking
        $bookings = $this->db->from('bookings');
        if ($userType == 'EXTERNAL') {
            $bookings->where('bookings.id_customer', $customerId);
        }
        if (!empty($branchId)) {
            $bookings->where('bookings.id_branch', $branchId);
        }
        $totalBooking = $bookings->count_all_results();

        // booking pending
        $bookingPending = $this->db->from('bookings')->where('status', BookingModel::STATUS_BOOKED);
        if ($userType == 'EXTERNAL') {
            $bookingPending->where('bookings.id_customer', $customerId);
        }
        if (!empty($branchId)) {
            $bookingPending->where('bookings.id_branch', $branchId);
        }
        $totalBookingPending = $bookingPending->count_all_results();

        // job
        $jobs = $this->db->from('handlings')
            ->join('bookings', 'handlings.id_booking = bookings.id')
            ->join('work_orders', 'handlings.id = work_orders.id_handling');
        if ($userType == 'EXTERNAL') {
            $jobs->where('handlings.id_customer', $customerId);
        }
        if (!empty($branchId)) {
            $jobs->where('bookings.id_branch', $branchId);
        }
        $totalJob = $jobs->count_all_results();

        // job pending
        $jobPending = $this->db->from('handlings')
            ->join('bookings', 'handlings.id_booking = bookings.id')
            ->join('work_orders', 'handlings.id = work_orders.id_handling')
            ->where('work_orders.completed_at', null);
        if ($userType == 'EXTERNAL') {
            $jobPending->where('bookings.id_customer', $customerId);
        }
        if (!empty($branchId)) {
            $jobPending->where('bookings.id_branch', $branchId);
        }
        $totalJobPending = $jobPending->count_all_results();

        // invoice
        $invoices = $this->db->from('invoices')->where('status', 'PUBLISHED');
        if ($userType == 'EXTERNAL') {
            $invoices->where('invoices.id_customer', $customerId);
        }
        if (!empty($branchId)) {
            $invoices->where('invoices.id_branch', $branchId);
        }
        $totalInvoice = $invoices->count_all_results();

        $totalStock = $this->getTotalStock();

        return [
            'bookingTotal' => $totalBooking,
            'bookingPendingTotal' => $totalBookingPending,
            'jobTotal' => $totalJob,
            'jobPendingTotal' => $totalJobPending,
            'invoiceTotal' => $totalInvoice,
            'stockTotal' => $totalStock
        ];
    }

    /**
     * Get dashboard handling summary.
     * @param $customerId
     * @return mixed
     */
    public function getBookingSummary($customerId)
    {
        $bookingData = $this->getBookingSummaryData($customerId, date('Y') . '-01-01', date('Y') . '-12-31');

        $bookings = [
            date('Y') . ' January' => [],
            date('Y') . ' February' => [],
            date('Y') . ' March' => [],
            date('Y') . ' April' => [],
            date('Y') . ' May' => [],
            date('Y') . ' June' => [],
            date('Y') . ' July' => [],
            date('Y') . ' August' => [],
            date('Y') . ' September' => [],
            date('Y') . ' October' => [],
            date('Y') . ' November' => [],
            date('Y') . ' December' => [],
        ];

        // put result value into year-month array
        foreach ($bookingData as $data) {
            $key = date('Y') . ' ' . format_date($data['year'] . '-' . $data['month'], 'F');
            if (!key_exists(strtolower($data['category']), $bookings[$key])) {
                $bookings[$key][strtolower($data['category'])] = $data['total_item'];
            } else {
                $bookings[$key][strtolower($data['category'])] += $data['total_item'];
            }

            if (!key_exists(strtolower($data['category'] . '_booking'), $bookings[$key])) {
                $bookings[$key][strtolower($data['category'] . '_booking')] = $data['total_booking'];
            } else {
                $bookings[$key][strtolower($data['category'] . '_booking')] += $data['total_booking'];
            }
        }

        // fill empty value with zero
        foreach ($bookings as &$booking) {
            if (!key_exists('inbound_booking', $booking)) {
                $booking['inbound_booking'] = 0;
            }
            if (!key_exists('outbound_booking', $booking)) {
                $booking['outbound_booking'] = 0;
            }
            if (!key_exists('inbound', $booking)) {
                $booking['inbound'] = 0;
            }
            if (!key_exists('outbound', $booking)) {
                $booking['outbound'] = 0;
            }
        }

        // adding movement inbound in percent
        $lastMonthValue = $this->getBookingSummaryData($customerId, (date('Y') - 1) . '-12-01', (date('Y') - 1) . '-12-31');
        $lastValue = 0;
        if (!empty($lastMonthValue)) {
            foreach ($lastMonthValue as $lastMonth) {
                if ($lastMonth['category'] == 'inbound') {
                    $lastValue = $lastMonth['total_item'];
                } else {
                    $lastValue = 0;
                }
            }
        }
        foreach ($bookings as &$booking) {
            $booking['movement'] = numerical($booking['inbound'] / if_empty($lastValue, 1) * 100, 2, true);
            if ($booking['inbound'] == $lastValue) {
                $booking['direction'] = 'left';
                $booking['label'] = 'yellow';
            } elseif ($booking['inbound'] > $lastValue) {
                $booking['label'] = 'green';
                $booking['direction'] = 'up';
            } else {
                $booking['label'] = 'red';
                $booking['direction'] = 'down';
            }
            $lastValue = $booking['inbound'];
        }

        return $bookings;
    }

    /**
     * Get booking data.
     * @param $customerId
     * @param $from
     * @param $to
     * @return mixed
     */
    private function getBookingSummaryData($customerId, $from, $to)
    {
        $branchId = get_active_branch('id');
        $userType = UserModel::authenticatedUserData('user_type');

        $bookings = $this->db
            ->select([
                'YEAR(bookings.booking_date) AS year',
                'MONTH(bookings.booking_date) AS month',
                'ref_booking_types.category',
                'IFNULL(COUNT(bookings.id), 0) AS total_booking',
                'IFNULL(SUM(booking_containers.total_container), 0) AS total_container',
                'IFNULL(SUM(booking_goods.total_goods), 0) AS total_goods',
                'IFNULL(SUM(booking_containers.total_container), 0) + IFNULL(SUM(booking_goods.total_goods), 0) AS total_item',
            ])
            ->from('bookings')
            ->join('ref_booking_types', 'bookings.id_booking_type = ref_booking_types.id', 'left')
            ->join('(
                    SELECT id_booking, COUNT(DISTINCT id_container) AS total_container 
                    FROM booking_containers
                    GROUP BY id_booking
                ) AS booking_containers', 'booking_containers.id_booking = bookings.id', 'left')
            ->join('(
                    SELECT id_booking, COUNT(DISTINCT id_goods) AS total_goods 
                    FROM booking_goods
                    GROUP BY id_booking
                ) AS booking_goods', 'booking_goods.id_booking = bookings.id', 'left')
            ->where('bookings.booking_date >= ', $from)
            ->where('bookings.booking_date <= ', $to)
            ->group_by('YEAR(booking_date), MONTH(booking_date), ref_booking_types.category');

        if ($userType == 'EXTERNAL') {
            $bookings->where('bookings.id_customer', $customerId);
        }

        if (!empty($branchId)) {
            $bookings->where('bookings.id_branch', $branchId);
        }

        return $bookings->get()->result_array();
    }

    /**
     * Get dashboard handling summary.
     * @param $customerId
     * @return mixed
     */
    public function getHandlingSummary($customerId)
    {
        $branchId = get_active_branch('id');
        $userType = UserModel::authenticatedUserData('user_type');

        $handlings = $this->db
            ->select([
                'handlings.id_handling_type',
                'ref_handling_types.handling_code',
                'ref_handling_types.handling_type',
                'COUNT(handlings.id) AS total_activity',
                'IFNULL(SUM(handling_containers.total_container), 0) AS total_container',
                'IFNULL(SUM(handling_goods.total_goods), 0) AS total_goods',
                'IFNULL(SUM(handling_containers.total_container), 0) + IFNULL(SUM(handling_goods.total_goods), 0) AS total_item',
            ])
            ->from('handlings')
            ->join('bookings', 'handlings.id_booking = bookings.id', 'left')
            ->join('ref_handling_types', 'handlings.id_handling_type = ref_handling_types.id', 'left')
            ->join('(
                    SELECT id_handling, COUNT(DISTINCT id_container) AS total_container 
                    FROM handling_containers WHERE is_deleted = false
                    GROUP BY id_handling
                ) AS handling_containers', 'handling_containers.id_handling = handlings.id', 'left')
            ->join('(
                    SELECT id_handling, COUNT(DISTINCT id_goods) AS total_goods 
                    FROM handling_goods WHERE is_deleted = false
                    GROUP BY id_handling
                ) AS handling_goods', 'handling_goods.id_handling = handlings.id', 'left')
            ->where('handlings.created_at >= ', date('Y') . '-01-01')
            ->where('handlings.created_at <= ', date('Y') . '-12-31')
            ->where('handlings.is_deleted', false)
            ->group_by('id_handling_type, handling_code, handling_type');

        if ($userType == 'EXTERNAL') {
            $handlings->where('handlings.id_customer', $customerId);
        }
        if (!empty($branchId)) {
            $handlings->where('bookings.id_branch', $branchId);
        }

        return $handlings->get()->result_array();
    }

    /**
     * Get report stock summary
     * @param $customerId
     * @return array
     */
    public function getStockSummary($customerId)
    {
        $filter = [
            'data' => 'stock',
        ];

        $stocks = [
            date('Y') . ' January' => [],
            date('Y') . ' February' => [],
            date('Y') . ' March' => [],
            date('Y') . ' April' => [],
            date('Y') . ' May' => [],
            date('Y') . ' June' => [],
            date('Y') . ' July' => [],
            date('Y') . ' August' => [],
            date('Y') . ' September' => [],
            date('Y') . ' October' => [],
            date('Y') . ' November' => [],
            date('Y') . ' December' => [],
        ];

        foreach ($stocks as $key => &$stock) {
            $month = format_date($key, 'm');
            $year = format_date($key, 'Y');
            $lastDateEachMonth = cal_days_in_month(CAL_GREGORIAN, $month, $year);
            $date = $year . '-' . $month . '-' . $lastDateEachMonth;

            $stockData['container20'] = 0;
            $stockData['container40'] = 0;
            $stockData['container45'] = 0;
            $stockData['containerAll'] = 0;
            $stockData['goodsQty'] = 0;
            $stockData['goodsTonnage'] = 0;
            $stockData['goodsVolume'] = 0;
            if (strtotime($date) <= strtotime('+1 month', strtotime('now'))) {
                $filter['stock_date'] = $date;
                $filter['total_size'] = 20;
                $container20 = $this->reportStock->getStockContainers($filter);
                $filter['total_size'] = 40;
                $container40 = $this->reportStock->getStockContainers($filter);
                $filter['total_size'] = 45;
                $container45 = $this->reportStock->getStockContainers($filter);

                $filter['total_size'] = 'quantity';
                $goodsQuantity = $this->reportStock->getStockGoods($filter);
                $filter['total_size'] = 'unit_weight';
                $goodsTonnage = $this->reportStock->getStockGoods($filter);
                $filter['total_size'] = 'unit_volume';
                $goodsVolume = $this->reportStock->getStockGoods($filter);

                $stockData['container20'] = $container20;
                $stockData['container40'] = $container40;
                $stockData['container45'] = $container45;
                $stockData['containerAll'] = $container20 + $container40 + $container45;
                $stockData['goodsQty'] = if_empty($goodsQuantity, 0);
                $stockData['goodsTonnage'] = if_empty($goodsTonnage, 0);
                $stockData['goodsVolume'] = if_empty($goodsVolume, 0);
            }
            $stock = $stockData;
        }

        // adding movement inbound in percent
        $filter['stock_date'] = (date('Y') - 1) . '-12-31';
        $filter['total_size'] = 'all';
        $lastMonthContainer = $this->reportStock->getStockContainers($filter);
        $filter['total_size'] = 'quantity';
        $lastMonthGoods = $this->reportStock->getStockGoods($filter);

        $lastContainerStock = if_empty($lastMonthContainer, 0);
        $lastGoodsStock = if_empty($lastMonthGoods, 0);
        foreach ($stocks as &$stock) {
            $totalContainer = $stock['containerAll'];
            $stock['containerMovement'] = numerical($totalContainer / if_empty($lastContainerStock, 1) * 100, 2, true);
            if ($totalContainer == $lastContainerStock) {
                $stock['containerDirection'] = 'left';
                $stock['containerLabel'] = 'yellow';
            } elseif ($totalContainer > $lastContainerStock) {
                $stock['containerLabel'] = 'green';
                $stock['containerDirection'] = 'up';
            } else {
                $stock['containerLabel'] = 'red';
                $stock['containerDirection'] = 'down';
            }
            $lastContainerStock = $totalContainer;

            $totalGoods = $stock['goodsQty'];
            $stock['goodsMovement'] = numerical($totalGoods / if_empty($lastGoodsStock, 1) * 100, 2, true);
            if ($totalGoods == $lastGoodsStock) {
                $stock['goodsDirection'] = 'left';
                $stock['goodsLabel'] = 'yellow';
            } elseif ($totalGoods > $lastGoodsStock) {
                $stock['goodsLabel'] = 'green';
                $stock['goodsDirection'] = 'up';
            } else {
                $stock['goodsLabel'] = 'red';
                $stock['goodsDirection'] = 'down';
            }
            $lastGoodsStock = $totalGoods;
        }

        return $stocks;
    }

    public function getOverspaces($filters = []){
        $customerId = $filters['owner'];
        $branchId = get_active_branch('id');
        $overSpaces = $this->db->select([
                    'work_order_goods.*',
                    'ref_containers.no_container',
                    'ref_containers.size',
                    'ref_containers_direct.no_container AS no_containerx',
                    'ref_containers_direct.size AS sizex',
                    'bookings.no_reference',
                    'ref_goods.name AS goods_name'
                    ])
                ->from('work_orders')
                ->join('handlings', 'handlings.id = work_orders.id_handling')
                ->join('bookings', 'bookings.id = handlings.id_booking')
                ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
                ->join('ref_people', 'ref_people.id = work_order_goods.id_owner')
                ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
                ->join('work_order_containers', 'work_order_containers.id = work_order_goods.id_work_order_container', 'left')
                ->join('ref_containers as ref_containers_direct', 'ref_containers_direct.no_container = replace(work_order_goods.ex_no_container , " ","")', 'left')
                ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container', 'left')
                ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type')                
                ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods','left')
                ->where([
                    'bookings.id_customer'=>$customerId,
                    'bookings.is_deleted' => false,
                    'handlings.status' => 'APPROVED',
                    'handlings.is_deleted' => false,
                    'work_orders.is_deleted' => false,
                    'work_order_goods.is_deleted' => false,
                    'ref_handling_types.is_deleted' => false,
                    'ref_people.is_deleted' => false,
                    'work_orders.status' => 'COMPLETED',
                    ])
                ->group_by('bookings.id, work_order_goods.id');
        
        if (!empty($branchId)) {
            $overSpaces->where('bookings.id_branch', $branchId);
        }
        if (key_exists('container', $filters) && !empty($filters['container'])) {
            if ($filters['container']=='yes') {
                $overSpaces->where('work_order_goods.ex_no_container is not null');
                // $overSpaces->where('ref_containers.size is not null');
            } else {
                $overSpaces->where('work_order_goods.ex_no_container is null');
            }
        }
        if (key_exists('category', $filters) && !empty($filters['category'])) {
            $overSpaces->where('ref_booking_types.category',$filters['category']);
        }
        if (key_exists('multiplier', $filters) && !empty($filters['multiplier'])) {
            $overSpaces->where('ref_handling_types.multiplier_goods', $filters['multiplier']);
        }
        if (key_exists('order_by', $filters) && !empty($filters['order_by'])) {
            $overSpaces->order_by( $filters['order_by']);
        }
        
        return $overSpaces->get()->result_array();
    }

    /**
     * Get storage capacity (enhanced from over space function).
     *
     * Terms:
     *   TEUS = twenty foot equivalent unit = 1x20ft containers
     *   20ft = 1 teu/s
     *   40ft/45ft = considered as 2 teu/s
     *
     * 1. Stripped container (FCL) teus calculation
     *   ---------------------------------------
     *   CONTAINER  =  ALL quantity goods inside
     *   ---------------------------------------
     * @example: Inbound container 40ft with 3 items with total 100 quantity,
     *             then outbound 2 items with total 20 quantity
     *
     *   - INBOUNDS
     *     TCLU9342342 - 40ft = - Computers 30 PCS
     *                          - Mouse 50 BOX
     *                          - Keyboards 20 BOX
     *                          ------------------ +
     *                            100 QTY items
     *
     *     TCLU9342342 - 40ft = 100 QTY items
     *                 2 teus = 100 QTY items
     *
     *   - OUTBOUND
     *     - 10 PCS Computers
     *     - 10 BOX Mouse
     *     ------------------ +
     *       20 QTY items  --> teus?
     *
     *     2 teus     100 QTY
     *     ------  =  ------- = 2t x 20q = 100q x Nt
     *     N teus     20 QTY         40  = 100N
     *                               N   = 40/100
     *                               N   = 0.4 teus
     *   - RESULT
     *     - available teus  = 2 (because 40ft container consider filled by all items inside)
     *     - outbound teus   = 0.4 (cross calculation by items content)
     *     - used teus       = 2 - 0.4 (available teus - outbound teus)
     *                       = 1.6 teus
     *
     *  2. Goods without ex container (LCL)
     *     - available teus  = p x l x qty(in) / 17
     *     - outbound teus   = p x l x qty(out) / 17
     *     - used teus       = p x l x qty(in - out) / 17
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getGoodsStorageCapacityUsage($filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $baseQuery = $this->db
            ->select([
                'booking_inbounds.id_customer',
                'customers.name AS customer_name',
                'customers.id_parent',
                'booking_inbounds.id_branch',
                'booking_inbounds.no_reference',
                'IF(ref_containers.no_container IS NULL, ref_goods.id, GROUP_CONCAT(ref_goods.id)) AS id_goods',
                'MIN(ref_goods.name) AS goods_name',
                'MIN(ref_goods.unit_length) AS unit_length',
                'MIN(ref_goods.unit_width) AS unit_width',
                'ref_containers.no_container',
                'ref_containers.size',
                'MIN(ref_warehouses.type) AS warehouse_type',
                'SUM(IF(ref_handling_types.multiplier_goods = 1, work_order_goods.quantity, 0)) AS inbound',
                'SUM(IF(ref_handling_types.multiplier_goods = -1, work_order_goods.quantity, 0)) AS outbound',
                'SUM(work_order_goods.quantity * ref_handling_types.multiplier_goods) AS stock',
                'IF(ref_containers.no_container IS NULL, 
                    SUM(ref_goods.unit_length) * SUM(ref_goods.unit_width) * SUM(IF(ref_handling_types.multiplier_goods = 1, work_order_goods.quantity, 0)) / 17, 
                    IF(ref_containers.size = 20, 1, 2)
                ) AS available_teus',
                'IF(ref_containers.no_container IS NULL,
                    SUM(ref_goods.unit_length) * SUM(ref_goods.unit_width) * SUM(IF(ref_handling_types.multiplier_goods = -1, work_order_goods.quantity, 0)) / 17,
                    SUM(IF(ref_handling_types.multiplier_goods = -1, work_order_goods.quantity, 0)) * IF(ref_containers.size = 20, 1, 2) / SUM(IF(ref_handling_types.multiplier_goods = 1, work_order_goods.quantity, 0))
                ) AS teus_outbound',
                'IF(ref_containers.no_container IS NULL, 
                    ref_goods.unit_length * ref_goods.unit_width * (SUM(IF(ref_handling_types.multiplier_goods = 1, work_order_goods.quantity, 0)) - SUM(IF(ref_handling_types.multiplier_goods = -1, work_order_goods.quantity, 0))) / 17, 
                    IF(ref_containers.size = 20, 1, 2) - SUM(IF(ref_handling_types.multiplier_goods = -1, work_order_goods.quantity, 0)) * IF(ref_containers.size = 20, 1, 2) / SUM(IF(ref_handling_types.multiplier_goods = 1, work_order_goods.quantity, 0))
                ) AS teus_used'
            ])
            ->from('bookings')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(bookings.id_booking, bookings.id)', 'left')
            ->join('ref_people AS customers', 'customers.id = booking_inbounds.id_customer')
            ->join('handlings', 'handlings.id_booking = bookings.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id', 'left')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit')
            ->join('ref_containers', 'ref_containers.no_container = TRIM(work_order_goods.ex_no_container)', 'left')
            ->join('ref_positions', 'ref_positions.id = work_order_goods.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left')
            ->where([
                'work_orders.status' => "COMPLETED",
                'handlings.status' => "APPROVED",
                'work_orders.is_deleted' => false,
                'handlings.is_deleted' => false,
                'bookings.is_deleted' => false,
            ])
            ->group_by('booking_inbounds.id_customer, booking_inbounds.no_reference, id_branch, no_container, IF(ref_containers.no_container IS NULL, id_goods, 1)')
            ->order_by('no_container');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if (key_exists('data', $filters) && !empty($filters['data'])) {
            switch ($filters['data']) {
                case 'all-data':
                case 'all':
                    break;
                case 'stocked':
                case 'stock-only':
                case 'stock':
                default:
                    $baseQuery->having('stock>', 0);
                    break;
                case 'empty-stock':
                    $baseQuery->having('stock', 0);
                    break;
                case 'negative-stock':
                    $baseQuery->having('stock<', 0);
                    break;
                case 'inactive-stock':
                    $baseQuery->having('stock<=', 0);
                    break;
            }
        } else {
            $baseQuery->having('stock>', 0);
        }

        if (key_exists('space_date', $filters) && !empty($filters['space_date'])) {
            $baseQuery->where('work_orders.completed_at<=', format_date($filters['space_date']));
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->group_start();
            $baseQuery->where('customers.id', $filters['customer']);
            if (key_exists('customer_include_member', $filters) && $filters['customer_include_member']) {
                $baseQuery->or_where('customers.id_parent', $filters['customer']);
            }
            $baseQuery->group_end();
        }

        if (key_exists('warehouse_type', $filters) && !empty($filters['warehouse_type'])) {
            $baseQuery->having('warehouse_type', $filters['warehouse_type']);
        }

        if (key_exists('branch_type', $filters) && !empty($filters['branch_type'])) {
            $baseQuery
                ->join('ref_branches', 'ref_branches.id = booking_inbounds.id_branch')
                ->where('ref_branches.branch_type', $filters['branch_type']);
        }

        if (key_exists('outbound_type', $filters) && !empty($filters['outbound_type'])) {
            $baseQuery->group_start();
            $baseQuery->where('customers.outbound_type', $filters['outbound_type']);
            if ($filters['outbound_type'] == 'CASH AND CARRY') {
                $baseQuery->or_where('customers.outbound_type IS NULL');
            }
            $baseQuery->group_end();
        }

        if (key_exists('have_whatsapp_group', $filters) && $filters['have_whatsapp_group']) {
            $baseQuery
                ->select([
                    'ref_people_branches.whatsapp_group'
                ])
                ->join('ref_people_branches', 'ref_people_branches.id_customer = customers.id AND booking_inbounds.id_branch = ref_people_branches.id_branch', 'left')
                ->where([
                    'ref_people_branches.whatsapp_group IS NOT NULL' => null,
                    'ref_people_branches.whatsapp_group!=' => '',
                ]);
        }

        if (key_exists('load_type', $filters)) {
            if (strtolower($filters['load_type']) == 'fcl') {
                $baseQuery->where('work_order_goods.ex_no_container IS NOT NULL');
            } else {
                $baseQuery
                    ->group_start()
                    ->where('work_order_goods.ex_no_container IS NULL')
                    ->or_where('TRIM(work_order_goods.ex_no_container)', '')
                    ->group_end();
            }
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get storage capacity of containers.
     *
     * Terms:
     *   TEUS = twenty foot equivalent unit = 1x20ft containers
     *   20ft = 1 teu/s
     *   40ft/45ft = considered as 2 teu/s
     *
     * - available teus  = by container size
     * - outbound teus   = teus container * outbound quantity
     * - used teus       = available teus - outbound teus
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getContainersStorageCapacityUsage($filters = [])
    {

        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $baseQuery = $this->db
            ->select([
                'booking_inbounds.id_customer',
                'customers.name AS customer_name',
                'customers.id_parent',
                'booking_inbounds.id_branch',
                'booking_inbounds.no_reference',
                'ref_containers.no_container',
                'ref_containers.size',
                'MIN(ref_warehouses.type) AS warehouse_type',
                'SUM(IF(ref_handling_types.multiplier_container = 1, work_order_containers.quantity, 0)) AS inbound',
                'SUM(IF(ref_handling_types.multiplier_container = -1, work_order_containers.quantity, 0)) AS outbound',
                'SUM(CAST(work_order_containers.quantity AS SIGNED) * ref_handling_types.multiplier_container) AS stock',
                'IF(ref_containers.size = 20, 1, 2) AS available_teus',
                'IF(ref_containers.size = 20, 1, 2) * SUM(IF(ref_handling_types.multiplier_container = -1, work_order_containers.quantity, 0)) AS teus_outbound',
                'IF(ref_containers.size = 20, 1, 2) - (IF(ref_containers.size = 20, 1, 2) * SUM(IF(ref_handling_types.multiplier_container = -1, work_order_containers.quantity, 0))) AS teus_used'
            ])
            ->from('bookings')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(bookings.id_booking, bookings.id)', 'left')
            ->join('ref_people AS customers', 'customers.id = booking_inbounds.id_customer')
            ->join('handlings', 'handlings.id_booking = bookings.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id', 'left')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container')
            ->join('ref_positions', 'ref_positions.id = work_order_containers.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left')
            ->where([
                'work_orders.status' => "COMPLETED",
                'handlings.status' => "APPROVED",
                'work_orders.is_deleted' => false,
                'handlings.is_deleted' => false,
                'bookings.is_deleted' => false,
            ])
            ->group_by('booking_inbounds.id_customer, booking_inbounds.no_reference, id_branch, id_container');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if (key_exists('data', $filters) && !empty($filters['data'])) {
            switch ($filters['data']) {
                case 'all-data':
                case 'all':
                    break;
                case 'stocked':
                case 'stock-only':
                case 'stock':
                default:
                    $baseQuery->having('stock>', 0);
                    break;
                case 'empty-stock':
                    $baseQuery->having('stock', 0);
                    break;
                case 'negative-stock':
                    $baseQuery->having('stock<', 0);
                    break;
                case 'inactive-stock':
                    $baseQuery->having('stock<=', 0);
                    break;
            }
        } else {
            $baseQuery->having('stock>', 0);
        }

        if (key_exists('space_date', $filters) && !empty($filters['space_date'])) {
            $baseQuery->where('work_orders.completed_at<=', format_date($filters['space_date']));
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->group_start();
            $baseQuery->where('customers.id', $filters['customer']);
            if (key_exists('customer_include_member', $filters) && $filters['customer_include_member']) {
                $baseQuery->or_where('customers.id_parent', $filters['customer']);
            }
            $baseQuery->group_end();
        }

        if (key_exists('warehouse_type', $filters) && !empty($filters['warehouse_type'])) {
            $baseQuery->having('warehouse_type', $filters['warehouse_type']);
        }

        if (key_exists('branch_type', $filters) && !empty($filters['branch_type'])) {
            $baseQuery
                ->join('ref_branches', 'ref_branches.id = booking_inbounds.id_branch')
                ->where('ref_branches.branch_type', $filters['branch_type']);
        }

        if (key_exists('outbound_type', $filters) && !empty($filters['outbound_type'])) {
            $baseQuery->group_start();
            $baseQuery->where('customers.outbound_type', $filters['outbound_type']);
            if ($filters['outbound_type'] == 'CASH AND CARRY') {
                $baseQuery->or_where('customers.outbound_type IS NULL');
            }
            $baseQuery->group_end();
        }

        if (key_exists('have_whatsapp_group', $filters) && $filters['have_whatsapp_group']) {
            $baseQuery
                ->select([
                    'ref_people_branches.whatsapp_group'
                ])
                ->join('ref_people_branches', 'ref_people_branches.id_customer = customers.id AND booking_inbounds.id_branch = ref_people_branches.id_branch', 'left')
                ->where([
                    'ref_people_branches.whatsapp_group IS NOT NULL' => null,
                    'ref_people_branches.whatsapp_group!=' => '',
                ]);
        }

        return $baseQuery->get()->result_array();
    }

}