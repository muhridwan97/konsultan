<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class InvoiceCalculatorModel
 * @property HandlingGoodsModel $handlingGoods
 * @property WorkOrderGoodsModel $workOrderGoods
 */
class InvoiceCalculatorModel extends CI_Model
{
    const PRICE_TYPES = [
        'STORAGE' => ['ACTIVITY', 'CONTAINER', 'GOODS'],
        'HANDLING' => ['ACTIVITY', 'CONTAINER', 'GOODS'],
        'COMPONENT' => ['ACTIVITY', 'CONTAINER', 'GOODS'],
        'INVOICE' => ['ACTIVITY'],
    ];

    const PRICE_RULES = [
        'ACTIVITY' => ['PER_ACTIVITY', 'PER_DAY'],
        'CONTAINER' => ['PER_CONTAINER', 'PER_DAY', 'PER_SIZE', 'PER_TYPE', 'PER_EMPTY', 'PER_DANGER', 'PER_CONDITION'],
        'GOODS' => ['PER_GOODS', 'PER_DAY', 'PER_DANGER', 'PER_CONDITION', 'PER_VOLUME', 'PER_TONNAGE', 'PER_UNIT']
    ];

    const RULE_MAPPING_DATA = [
        'PER_CONTAINER' => '',
        'PER_GOODS' => '',
        'PER_DAY' => '',
        'PER_TONNAGE' => '',
        'PER_VOLUME' => '',
        'PER_UNIT' => 'id_unit',
        'PER_SIZE' => 'container_size',
        'PER_TYPE' => 'container_type',
        'PER_DANGER' => 'status_danger',
        'PER_EMPTY' => 'is_empty',
        'PER_CONDITION' => 'status'
    ];

    const RULE_MAPPING_PRICE = [
        'PER_CONTAINER' => '',
        'PER_GOODS' => '',
        'PER_DAY' => '',
        'PER_TONNAGE' => '',
        'PER_VOLUME' => '',
        'PER_UNIT' => 'goods_unit',
        'PER_SIZE' => 'container_size',
        'PER_TYPE' => 'container_type',
        'PER_DANGER' => 'status_danger',
        'PER_EMPTY' => 'status_empty',
        'PER_CONDITION' => 'status_condition'
    ];

    private $tablePrices = [];

    private $ruleCollections = [];

    private $roundItemValue = 2;

    private $ignoreGoodsRule = '-1';

    public static $resultStockContainer = null;
    public static $resultStockGoods = null;

    /**
     * InvoiceCalculatorModel constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->tablePrices = $this->getBaseInvoicePrice()->get()->result_array();
    }

    /**
     * Get minimum inbound date for invoice.
     * @param $bookingId
     * @param bool $isDepo
     * @return string inbound date
     */
    public function getInboundDate($bookingId, $isDepo = false)
    {
        $booking = $this->booking->getBookingById($bookingId);
        $stockContainers = $this->getBaseStockContainers($booking, true, $isDepo);
        $stockGoods = $this->getBaseStockGoods($booking, true, $isDepo);

        $dateContainers = array_column($stockContainers, 'completed_at');
        $dateGoods = array_column($stockGoods, 'completed_at');

        $dates = array_merge($dateContainers, $dateGoods);
        $uniqueDates = array_unique($dates);

        usort($uniqueDates, function ($a, $b) {
            $dateTimestamp1 = strtotime($a);
            $dateTimestamp2 = strtotime($b);

            return $dateTimestamp1 < $dateTimestamp2 ? -1 : 1;
        });

        return key_exists('0', $uniqueDates) ? $uniqueDates[0] : date('Y-m-d');
    }

    /**
     * Get base last invoice price reference.
     * @param bool $isMultiValue
     * @return CI_DB_query_builder
     */
    public function getBaseInvoicePrice($isMultiValue = false)
    {
        $branchId = get_active_branch('id');

        if ($isMultiValue) {
            $prices = $this->db->select('ref_component_prices.*', false)->from("(
                SELECT ref_component_prices.*, (
                    SELECT next_prices.min_weight FROM ref_component_prices AS next_prices
                    WHERE next_prices.min_weight = (
                        SELECT MIN(all_prices.min_weight) FROM ref_component_prices AS all_prices 
                        WHERE all_prices.min_weight > ref_component_prices.min_weight
                            AND all_prices.id_branch = ref_component_prices.id_branch
                            AND all_prices.price_type = ref_component_prices.price_type
                            AND all_prices.price_subtype = ref_component_prices.price_subtype
                            AND all_prices.rule = ref_component_prices.rule
                            AND IF(ref_component_prices.id_component IS NULL, 1, all_prices.id_component = ref_component_prices.id_component)
                            AND IF(ref_component_prices.id_handling_type IS NULL, 1, all_prices.id_handling_type = ref_component_prices.id_handling_type)
                            AND IF(ref_component_prices.id_customer IS NULL, 1, all_prices.id_customer = ref_component_prices.id_customer)
                            AND (CONCAT(',', RTRIM(ref_component_prices.rule), ',') LIKE '%,PER_TONNAGE,%' OR CONCAT(',', RTRIM(ref_component_prices.rule), ',') LIKE '%,PER_VOLUME,%')
                            AND ref_component_prices.status = 'APPROVED'
                    ) LIMIT 1
                ) AS max_weight
                FROM ref_component_prices
                INNER JOIN (
                    SELECT MAX(id) AS id, MAX(effective_date) AS effective_date
                    FROM ref_component_prices 
                    WHERE is_void = false AND is_deleted = false
                    GROUP BY id_branch, id_customer, id_handling_type, id_component, price_type, price_subtype, rule, 
                            min_weight, goods_unit, container_type, container_size, status_danger, status_empty, status_condition, description
                ) AS last_prices ON ref_component_prices.id = last_prices.id
                WHERE (CONCAT(',', RTRIM(ref_component_prices.rule), ',') LIKE '%,PER_TONNAGE,%' OR CONCAT(',', RTRIM(ref_component_prices.rule), ',') LIKE '%,PER_VOLUME,%')
                AND ref_component_prices.status = 'APPROVED'
            ) AS ref_component_prices");
        } else {
            $priceBase = $this->db->select(['MAX(id) AS id', 'MAX(effective_date) AS effective_date'])
                ->from('ref_component_prices')
                ->where(['is_void' => false, 'is_deleted' => false, 'status' => 'APPROVED'])
                ->group_by('id_branch, id_customer, id_handling_type, id_component, price_type, price_subtype, rule, goods_unit, container_type, container_size, status_danger, status_empty, status_condition, description');

            if (!empty($branchId)) {
                $priceBase->where('ref_component_prices.id_branch', $branchId);
            }

            $basePriceQuery = $this->db->get_compiled_select();
            $prices = $this->db->select('ref_component_prices.*')
                ->from('ref_component_prices')
                ->join("({$basePriceQuery}) AS last_prices", 'ref_component_prices.id = last_prices.id');
        }

        if (!empty($branchId)) {
            $prices->where('ref_component_prices.id_branch', $branchId);
        }

        return $prices;
    }

    /**
     * Get base stock containers.
     * @param $booking
     * @param bool $returnResult
     * @param bool $isDepo
     * @return mixed
     */
    public function getBaseStockContainers($booking, $returnResult = false, $isDepo = false)
    {
        if ($returnResult && !is_null(self::$resultStockContainer)) {
            return self::$resultStockContainer;
        } else {
            $branchId = get_active_branch('id');
            $handlingOutbound = get_setting('default_outbound_handling');
            $handlingMoveInbound = get_setting('default_moving_in_handling');
            $handlingMoveOutbound = get_setting('default_moving_out_handling');

            $report = $this->db
                ->select([
                    'ref_people.name AS owner_name',
                    'IFNULL(bookings.id_booking, bookings.id) AS id_booking',
                    'id_container',
                    'no_container',
                    'ref_containers.type AS container_type',
                    'ref_containers.size AS container_size',
                    'SUM(CAST(quantity AS SIGNED) * multiplier_container) AS quantity',
                    'MIN(IFNULL(security_out_date, completed_at)) AS completed_at',
                    'MAX(work_order_containers.id) AS id_work_order_container',
                ])
                ->from('bookings')
                ->join('bookings AS booking_in', 'booking_in.id = bookings.id_booking', 'left')
                ->join('ref_people', 'ref_people.id = bookings.id_customer')
                ->join('handlings', 'handlings.id_booking = bookings.id')
                ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
                ->join('work_orders', 'work_orders.id_handling = handlings.id')
                ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
                ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id')
                ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container')
                ->where("IF(id_handling_type = '{$handlingOutbound}', security_in_date, 1) IS NOT NULL")
                ->where('(IFNULL(bookings.id_booking, bookings.id)) = ', $booking['id'])
                ->where([
                    'bookings.is_deleted' => false,
                    'handlings.status !=' => 'REJECTED',
                    'handlings.is_deleted' => false,
                    'work_orders.is_deleted' => false,
                    'work_order_containers.is_deleted' => false,
                    'ref_handling_types.is_deleted' => false,
                    'ref_people.is_deleted' => false,
                    'work_orders.status' => 'COMPLETED',
                ])
                ->group_start()
                ->where('id_handling_type !=', $handlingMoveInbound)
                ->where('id_handling_type !=', $handlingMoveOutbound)
                ->group_end()
                ->group_by('ref_people.name, IFNULL(bookings.id_booking, bookings.id), id_container');

            if (!$isDepo) {
                $report->having('quantity > 0');
            }

            if (!empty($branchId)) {
                $report->where('bookings.id_branch', $branchId);
            }

            $baseStockQuery = $this->db->get_compiled_select();
            $report = $this->db->select([
                'container_stocks.*',
                'work_order_containers.is_empty',
                'work_order_containers.status_danger',
                'work_order_containers.status',
            ])
                ->from('work_order_containers')
                ->join("({$baseStockQuery}) AS container_stocks", 'work_order_containers.id = container_stocks.id_work_order_container', 'right');

            if ($returnResult) {
                $result = $report->get()->result_array();
                self::$resultStockContainer = $result;
                return $result;
            }
            return $this->db->get_compiled_select();
        }
    }

    /**
     * Get base stock goods.
     * @param $booking
     * @param bool $returnResult
     * @param bool $isDepo
     * @return mixed
     */
    public function getBaseStockGoods($booking, $returnResult = false, $isDepo = false)
    {
        if ($returnResult && !is_null(self::$resultStockGoods)) {
            return self::$resultStockGoods;
        } else {
            $branchId = get_active_branch('id');
            $handlingOutbound = get_setting('default_outbound_handling');
            $handlingMoveInbound = get_setting('default_moving_in_handling');
            $handlingMoveOutbound = get_setting('default_moving_out_handling');

            $report = $this->db->select([
                'ref_people.name AS owner_name',
                'IFNULL(bookings.id_booking, bookings.id) AS id_booking',
                'id_goods',
                'no_goods',
                'ref_goods.name AS goods_name',
                'id_unit',
                'unit',
                '"" AS no_delivery_order',
                'SUM(quantity * multiplier_goods) AS quantity',
                'SUM(quantity * multiplier_goods) * ref_goods.unit_weight AS tonnage',
                'SUM(quantity * multiplier_goods) * ref_goods.unit_volume AS volume',
                'MIN(IFNULL(security_out_date, completed_at)) AS completed_at',
                'MAX(work_order_goods.id) AS id_work_order_goods',
            ])
                ->from('bookings')
                ->join('bookings AS booking_in', 'booking_in.id = bookings.id_booking', 'left')
                ->join('ref_people', 'ref_people.id = bookings.id_customer')
                ->join('handlings', 'handlings.id_booking = bookings.id')
                ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
                ->join('work_orders', 'work_orders.id_handling = handlings.id')
                ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
                ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
                ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods')
                ->join('ref_units', 'ref_units.id = work_order_goods.id_unit')
                ->where("IF(id_handling_type = '{$handlingOutbound}', security_in_date, 1) IS NOT NULL")
                ->where('(IFNULL(bookings.id_booking, bookings.id)) = ', $booking['id'])
                ->where([
                    'bookings.is_deleted' => false,
                    'handlings.status !=' => 'REJECTED',
                    'handlings.is_deleted' => false,
                    'work_orders.is_deleted' => false,
                    'work_order_goods.is_deleted' => false,
                    'ref_handling_types.is_deleted' => false,
                    'ref_people.is_deleted' => false,
                    'work_orders.status' => 'COMPLETED',
                ])
                ->group_start()
                ->where('id_handling_type !=', $handlingMoveInbound)
                ->where('id_handling_type !=', $handlingMoveOutbound)
                ->group_end()
                ->group_by('ref_people.name, IFNULL(bookings.id_booking, bookings.id), id_goods, id_unit');

            if (!$isDepo) {
                $report->having('quantity > 0');
            }

            if (!empty($branchId)) {
                $report->where('bookings.id_branch', $branchId);
            }

            $baseStockQuery = $this->db->get_compiled_select();
            $report = $this->db->select([
                'goods_stocks.*',
                'work_order_goods.status_danger',
                'work_order_goods.status',
            ])->distinct()
                ->from('work_order_goods')
                ->join("({$baseStockQuery}) AS goods_stocks", 'work_order_goods.id = goods_stocks.id_work_order_goods', 'right');

            if ($returnResult) {
                $result = $report->get()->result_array();
                self::$resultStockGoods = $result;
                return $result;
            }
            return $this->db->get_compiled_select();
        }
    }

    /**
     * Get base in out containers data.
     * @param $booking
     * @return string
     */
    public function getBaseInOutContainers($booking)
    {
        $handlingInbound = get_setting('default_inbound_handling');
        $handlingOutbound = get_setting('default_outbound_handling');

        $report = $this->db
            ->select([
                'inbounds.id_booking', 'inbounds.id_container', 'inbounds.no_container', 'inbounds.status',
                'inbounds.container_type', 'inbounds.container_size', 'inbounds.status_danger', 'inbounds.is_empty',
                'inbounds.quantity', 'DATE(IFNULL(inbounds.security_out_date, inbounds.completed_at)) AS inbound_date',
                'outbounds.id_booking_out', 'outbounds.quantity AS quantity_out', 'DATE(outbound_date) AS outbound_date'
            ])
            ->from('(
                SELECT handlings.id_booking, id_handling_type, id_container, no_container, work_order_containers.status, 
                    ref_containers.type AS container_type, ref_containers.size AS container_size, 
                    status_danger, is_empty, quantity, completed_at, security_out_date 
                FROM handlings
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                LEFT JOIN safe_conducts ON safe_conducts.id = work_orders.id_safe_conduct
                INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                INNER JOIN ref_containers ON ref_containers.id = work_order_containers.id_container
                WHERE work_orders.is_deleted = false AND work_orders.status = "COMPLETED" AND handlings.status != "REJECTED"
            ) AS inbounds')
            ->join('(
	            SELECT DISTINCT 
	                bookings.id AS id_booking_out, 
	                bookings.id_booking AS id_booking_in, 
	                id_container, 
	                quantity, 
	                security_in_date AS outbound_date
	            FROM bookings
	            INNER JOIN handlings ON handlings.id_booking = bookings.id
	            INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                LEFT JOIN safe_conducts ON safe_conducts.id = work_orders.id_safe_conduct
	            INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id	            
	            WHERE id_handling_type = "' . $handlingOutbound . '"
                ) AS outbounds', 'inbounds.id_booking = outbounds.id_booking_in 
                    AND inbounds.id_container = outbounds.id_container', 'left')
            ->where('inbounds.id_booking', $booking['id'])
            ->where('inbounds.id_handling_type', $handlingInbound);

        return $report->get_compiled_select();
    }

    /**
     * Get base in out goods data.
     * @param $booking
     * @return string
     */
    public function getBaseInOutGoods($booking)
    {
        $handlingInbound = get_setting('default_inbound_handling');
        $handlingOutbound = get_setting('default_outbound_handling');

        $report = $this->db
            ->select([
                'inbounds.id_booking', 'inbounds.id_goods', 'inbounds.no_goods', 'inbounds.goods_name', 'inbounds.quantity',
                'inbounds.tonnage', 'inbounds.volume', 'inbounds.id_unit', 'inbounds.unit',
                'inbounds.status_danger', 'inbounds.status', 'DATE(IFNULL(inbounds.security_out_date, inbounds.completed_at)) AS inbound_date',
                'outbounds.id_booking_out', 'outbounds.quantity AS quantity_out', 'outbounds.tonnage AS tonnage_out', 'outbounds.volume AS volume_out',
                'IFNULL(DATE(outbound_date), CURDATE()) AS outbound_date'
            ])
            ->from('(
                SELECT handlings.id_booking, id_handling_type, id_goods, no_goods, ref_goods.name AS goods_name,
                    quantity, (quantity * unit_weight) AS tonnage, (quantity * unit_volume) AS volume, id_unit, unit,
                    status_danger, work_order_goods.status, completed_at, security_out_date 
                FROM handlings
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                LEFT JOIN safe_conducts ON safe_conducts.id = work_orders.id_safe_conduct
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                INNER JOIN ref_goods ON ref_goods.id = work_order_goods.id_goods
                INNER JOIN ref_units ON ref_units.id = work_order_goods.id_unit
                WHERE work_orders.is_deleted = false AND work_orders.status = "COMPLETED" AND handlings.status != "REJECTED"
            ) AS inbounds')
            ->join('(
                SELECT DISTINCT 
	                bookings.id AS id_booking_out, 
	                bookings.id_booking AS id_booking_in,  
                    id_goods, 
                    SUM(quantity) AS quantity,
                    SUM(quantity) * unit_weight AS tonnage,
                    SUM(quantity) * unit_volume AS volume, 
                    MAX(IFNULL(security_in_date, completed_at)) AS outbound_date
                FROM bookings
	            INNER JOIN handlings ON handlings.id_booking = bookings.id
	            INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                LEFT JOIN safe_conducts ON safe_conducts.id = work_orders.id_safe_conduct
	            INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id	            
	            INNER JOIN ref_goods ON ref_goods.id = work_order_goods.id_goods	            
	            WHERE id_handling_type = "' . $handlingOutbound . '"
                GROUP BY bookings.id, bookings.id_booking, id_goods, completed_at
	            ) AS outbounds', 'inbounds.id_booking = outbounds.id_booking_in 
                    AND inbounds.id_goods = outbounds.id_goods', 'left')
            ->where('inbounds.id_booking', $booking['id'])
            ->where('inbounds.id_handling_type', $handlingInbound);

        return $report->get_compiled_select();
    }

    /**
     * Generate matched rules into rule collections.
     * @param $type string either 'STORAGE', 'HANDLING', 'COMPONENT' or INVOICE'
     * @param $subtype string either 'ACTIVITY', 'CONTAINER', or 'GOODS'
     * @return array|mixed
     */
    public function getMatchedRules($type, $subtype)
    {
        if (key_exists($type, $this->ruleCollections) && key_exists($subtype, $this->ruleCollections[$type])) {
            return $this->ruleCollections[$type][$subtype];
        } else {
            $baseRule = self::PRICE_RULES[$subtype];
            $allCombinationRules = power_set($baseRule);

            // build unique combination of rules (true and false rule)
            // [
            //   [0] => [PER_ACTIVITY,NOT-PER_DAY]  ---> look, we add NOT- condition in combination for query builder purpose latter
            //   [1] => [PER_ACTIVITY,PER_DAY]
            //   [2] => [PER_DAY,NOT-PER_ACTIVITY]
            // }
            //
            $allCombinationWithNegations = $allCombinationRules;
            foreach ($allCombinationWithNegations as &$combinationWithNegation) {
                $notConditions = array_diff($baseRule, $combinationWithNegation);
                foreach ($notConditions as $notCondition) {
                    $combinationWithNegation[] = 'NOT-' . $notCondition;
                }
            }

            // find only matched to table price for optimization,
            // we do not need to try querying rules combination that never exist in table price
            foreach ($allCombinationRules as $index => $conditionRules) {
                $isFound = false;
                foreach ($this->tablePrices as $tablePrice) {
                    $rules = explode(',', $tablePrice['rule']);

                    // we narrow down our search because [PER_CONTAINER,PER_SIZE] may exist on another price_type and price_subtype
                    if ($tablePrice['price_type'] == $type && $tablePrice['price_subtype'] == $subtype) {

                        $totalMatch = 0;

                        // loop through condition from combination generator above against rule on table price
                        // we cannot compare string-to-string because rules may in random position
                        // PER_ACTIVITY,PER_DAY (rule combination above) = PER_DAY,PER_ACTIVITY (table price)
                        // so we count all matches rule and must be have same total rule items in table price.
                        foreach ($conditionRules as $conditionRule) {
                            // find exactly same rules from combination above that exist on the table price
                            foreach ($rules as $rule) {
                                if ($rule == $conditionRule) {
                                    $totalMatch++;
                                }
                            }
                        }

                        // Uhu.. we know this combination exist on database, go ahead query it.
                        if ($totalMatch == count($conditionRules) && $totalMatch == count($rules)) {
                            $isFound = true;
                        }
                    }
                }

                if ($isFound) {
                    $this->ruleCollections[$type][$subtype][] = $allCombinationWithNegations[$index];
                }
            }

            if (key_exists($type, $this->ruleCollections) && key_exists($subtype, $this->ruleCollections[$type])) {
                return $this->ruleCollections[$type][$subtype];
            }
        }

        return [];
    }

    /**
     * Replace rules to be appended to invoice unit type.
     * @param $invoicePrice
     * @return string
     */
    private function buildRuleLabel($invoicePrice)
    {
        if (empty($invoicePrice['rule'])) {
            return '';
        }

        $rules = explode(',', $invoicePrice['rule']);
        if ($rules[0] == 'PER_ACTIVITY' || $rules[0] == 'PER_CONTAINER' || $rules[0] == 'PER_GOODS') {
            array_shift($rules);
        }

        return strtolower(preg_replace('/,?PER_/', '/', implode(',', $rules)));
    }

    /**
     * Check if quantity need to fetched from total of attributes.
     * @param $invoicePrice
     * @return mixed
     */
    private function buildQuantityLabel($invoicePrice)
    {
        $quantityLabel = 1;

        if ($invoicePrice['price_subtype'] == 'CONTAINER') {
            if (key_exists('total_container', $invoicePrice)) {
                $quantityLabel = $invoicePrice['total_container'];
            }
        } elseif ($invoicePrice['price_subtype'] == 'GOODS') {
            $quantityLabel = $invoicePrice['total_goods_quantity'];
            if (preg_match('/PER_VOLUME/', $invoicePrice['rule'])) {
                if (key_exists('total_goods_volume', $invoicePrice)) {
                    $quantityLabel = $invoicePrice['total_goods_volume'];
                }
            } elseif (preg_match('/PER_TONNAGE/', $invoicePrice['rule'])) {
                if (key_exists('total_goods_tonnage', $invoicePrice)) {
                    $quantityLabel = $invoicePrice['total_goods_tonnage'];
                }
            }
        }
        return $quantityLabel;
    }

    /**
     * Select and combine item name depends on existing attributes
     * @param $invoicePrice
     * @return string
     */
    private function buildItemSummaryLabel($invoicePrice)
    {
        $itemSummary = '';
        if (key_exists('no_containers', $invoicePrice)) {
            $itemSummary = $invoicePrice['no_containers'];
        }

        if (key_exists('goods_names', $invoicePrice)) {
            if (!empty($invoicePrice['goods_names'])) {
                if (empty($itemSummary)) {
                    $itemSummary = $invoicePrice['goods_names'];
                } else {
                    $itemSummary .= '|' . $invoicePrice['goods_names'];
                }
            }
        }

        return $itemSummary;
    }

    /**
     * Create label item storage if need to be appended with danger or empty status
     * @param $storagePrice
     * @return string
     */
    private function buildStorageLabel($storagePrice)
    {
        $storageLabel = '';
        if (preg_match('/PER_EMPTY/', $storagePrice['rule'])) {
            if (key_exists('is_empty', $storagePrice)) {
                $storageLabel .= ($storagePrice['is_empty'] ? ' EMPTY' : ' FULL');
            }
        }

        if (preg_match('/PER_DANGER/', $storagePrice['rule'])) {
            if (key_exists('status_danger', $storagePrice)) {
                if ($storagePrice['status_danger'] == 'DANGER TYPE 1') {
                    $storageLabel .= ' WITH LABEL IMDG CODE';
                } else if ($storagePrice['status_danger'] == 'DANGER TYPE 2') {
                    $storageLabel .= ' WITHOUT LABEL IMDG CODE';
                }
            }
        }
        if (preg_match('/PER_CONDITION/', $storagePrice['rule'])) {
            if (key_exists('status', $storagePrice)) {
                if ($storagePrice['status'] != 'GOOD') {
                    $storageLabel .= ' ' . $storagePrice['status'];
                }
            }
        }
        return $storageLabel;
    }

    /**
     * Decide which attribute used as price multiplier
     * @param $invoicePrice
     * @return int
     */
    private function buildMultiplierValue($invoicePrice)
    {
        $multiplier = 1;
        if (preg_match('/PER_DAY/', $invoicePrice['rule'])) {
            $multiplier = $invoicePrice['total_day'];
        }
        return $multiplier;
    }

    /**
     * Check if we need to modify description by dates or another label
     * @param $storagePrice
     * @return string
     */
    private function buildDescriptionLabel($storagePrice)
    {
        $description = '';
        if (preg_match('/PER_DAY/', $storagePrice['rule'])) {
            $fromStorage = format_date($storagePrice['from_date'], 'd/m/Y');
            $untilStorage = format_date($storagePrice['until_date'], 'd/m/Y');
            $description = $fromStorage . ' - ' . $untilStorage;
        }
        return $description;
    }

    /**
     * Get storage price
     * @param $booking
     * @param bool $isExtension
     * @param bool $isDepo
     * @return array
     */
    public function getStorageTypePrice($booking, $isExtension = false, $isDepo = false)
    {
        $invoicePrice = [];

        foreach (self::PRICE_TYPES['STORAGE'] as $type) {
            $rules = $this->getMatchedRules('STORAGE', $type);
            foreach ($rules as $rule) {
                if ($type == 'ACTIVITY') {
                    $storageActivities = $this->queryStorageActivity($rule, $booking, $isExtension);
                    if (!empty($storageActivities)) {
                        $invoicePrice = array_merge($invoicePrice, $storageActivities);
                    }
                } elseif ($type == 'CONTAINER') {
                    $storageContainers = $this->queryStorageContainer($rule, $booking, $isExtension, $isDepo);
                    if (!empty($storageContainers)) {
                        $invoicePrice = array_merge($invoicePrice, $storageContainers);
                    }
                } elseif ($type == 'GOODS') {
                    $storageGoods = $this->queryStorageGoods($rule, $booking, $isExtension, $isDepo);
                    if (!empty($storageGoods)) {
                        $invoicePrice = array_merge($invoicePrice, $storageGoods);
                    }
                }
            }
        }

        return $invoicePrice;
    }

    /**
     * Query storage by activity type of combination rule.
     * @param $conditionRules
     * @param $booking
     * @param $isExtension
     * @return array
     */
    private function queryStorageActivity($conditionRules, $booking, $isExtension)
    {
        $queryStockContainers = $this->getBaseStockContainers($booking);
        $queryStockGoods = $this->getBaseStockGoods($booking);

        if ($isExtension) {
            $booking['inbound_date'] = date('Y-m-d H:i:s', strtotime("+1 day", strtotime($booking['inbound_date'])));
        }

        $prices = $this->getBaseInvoicePrice()->select([
            'booking_containers.no_containers',
            'booking_containers.total_container',
            'booking_goods.goods_names',
            'booking_goods.total_goods',
            "'{$booking['inbound_date']}' AS from_date",
            "'{$booking['outbound_date']}' AS until_date",
            "DATEDIFF('{$booking['outbound_date']}', '{$booking['inbound_date']}') + 1 AS total_day",
        ])
            ->join("(
                SELECT 
                    GROUP_CONCAT(CONCAT(no_container, ' (', container_type, '-', container_size, '-', status_danger, ')') SEPARATOR ',') AS no_containers, 
                    COUNT(id_container) AS total_container
                FROM ($queryStockContainers) AS stock_containers 
                WHERE id_booking = '{$booking['id']}'
            ) AS booking_containers", '1 = 1')
            ->join("(
                SELECT 
                    GROUP_CONCAT(CONCAT(goods_name, ' (', quantity, unit, '-', tonnage, 'Kg-', volume, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names, 
                    SUM(quantity) AS total_goods
                FROM ($queryStockGoods) AS stock_goods 
                WHERE id_booking = '{$booking['id']}'
            ) AS booking_goods", '1 = 1')
            ->where([
                'ref_component_prices.id_branch' => $booking['id_branch'],
                'ref_component_prices.id_customer' => $booking['id_customer'],
                'ref_component_prices.price_type' => 'STORAGE',
                'ref_component_prices.price_subtype' => 'ACTIVITY',
            ]);

        if (!empty($conditionRules)) {
            $prices->group_start();
            foreach ($conditionRules as $conditionRule) {
                if (preg_match('/^NOT-/', $conditionRule)) {
                    $condNegationExtract = preg_replace('/^NOT-/', '', $conditionRule);
                    $prices->not_like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$condNegationExtract},");
                } else {
                    $prices->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$conditionRule},");
                }
            }
            $prices->group_end();
        }

        $storagePrices = $prices->get()->result_array();

        $storageActivities = [];
        foreach ($storagePrices as $storagePrice) {
            $multiplier = $this->buildMultiplierValue($storagePrice);
            $description = $this->buildDescriptionLabel($storagePrice);
            $rule = $this->buildRuleLabel($storagePrice);
            $itemSummary = $this->buildItemSummaryLabel($storagePrice);

            $storageActivities[] = [
                'item_name' => 'STORAGE',
                'unit' => $storagePrice['price_subtype'] . $rule,
                'type' => $storagePrice['price_type'],
                'quantity' => 1,
                'unit_price' => $storagePrice['price'],
                'unit_multiplier' => $multiplier,
                'total' => $multiplier * $storagePrice['price'],
                'description' => $description,
                'item_summary' => $itemSummary,
            ];
        }

        return $storageActivities;
    }

    /**
     * Query storage by container combination rules.
     * @param $conditionRules
     * @param $booking
     * @param $isExtension
     * @param $isDepo
     * @return array
     */
    private function queryStorageContainer($conditionRules, $booking, $isExtension, $isDepo)
    {
        $columns = [];
        $columnJoin = '1=1';
        foreach ($conditionRules as $conditionRule) {
            if (!preg_match('/^NOT-/', $conditionRule)) {
                if (key_exists($conditionRule, self::RULE_MAPPING_DATA)) {
                    if (!empty(self::RULE_MAPPING_DATA[$conditionRule])) {
                        $columns[] = self::RULE_MAPPING_DATA[$conditionRule];
                        $columnJoin .= ' AND booking_containers.' . self::RULE_MAPPING_DATA[$conditionRule] . '=ref_component_prices.' . self::RULE_MAPPING_PRICE[$conditionRule];
                    }
                }
            }
        }
        $columnGroup = if_empty(implode(',', $columns), '', ',');

        $fromDate = 'DATE(completed_at)';
        if ($isExtension) {
            $fromDate = "'" . date('Y-m-d H:i:s', strtotime("+1 day", strtotime($booking['inbound_date']))) . "'";
        }

        $untilDate = "'{$booking['outbound_date']}'";
        if ($isDepo) {
            $fromDate = 'inbound_date';
            $untilDate = 'outbound_date';
            $queryStockContainers = $this->getBaseInOutContainers($booking);
        } else {
            $queryStockContainers = $this->getBaseStockContainers($booking);
        }

        $prices = $this->getBaseInvoicePrice()->select([
            'booking_containers.no_containers',
            'booking_containers.total_container',
            'booking_containers.from_date',
            'booking_containers.until_date',
            'DATEDIFF(booking_containers.until_date, booking_containers.from_date) + 1 AS total_day',
        ]);

        foreach ($columns as $column) {
            $prices->select('booking_containers.' . $column);
        }

        $prices = $prices->join("(
                SELECT 
                    GROUP_CONCAT(CONCAT(no_container, ' (', container_type, '-', container_size, '-', status_danger, ')') SEPARATOR ',') AS no_containers,
                    COUNT(id_container) AS total_container,
                    {$fromDate} AS from_date,
                    {$untilDate} AS until_date
                    {$columnGroup}
                FROM ($queryStockContainers) AS stock_containers 
                WHERE id_booking = '{$booking['id']}'
                GROUP BY {$fromDate}, {$untilDate} 
                {$columnGroup}
            ) AS booking_containers", $columnJoin)
            ->where([
                'ref_component_prices.id_branch' => $booking['id_branch'],
                'ref_component_prices.id_customer' => $booking['id_customer'],
                'ref_component_prices.price_type' => 'STORAGE',
                'ref_component_prices.price_subtype' => 'CONTAINER',
            ]);

        $prices->group_start();
        foreach ($conditionRules as $conditionRule) {
            if (preg_match('/^NOT-/', $conditionRule)) {
                $condNegationExtract = preg_replace('/^NOT-/', '', $conditionRule);
                $prices->not_like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$condNegationExtract},");
            } else {
                $prices->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$conditionRule},");
            }
        }
        $prices->group_end();

        $storagePrices = $prices->get()->result_array();

        $storageContainer = [];
        foreach ($storagePrices as $storagePrice) {
            $label = $this->buildStorageLabel($storagePrice);
            $multiplier = $this->buildMultiplierValue($storagePrice);
            $description = $this->buildDescriptionLabel($storagePrice);
            $rule = $this->buildRuleLabel($storagePrice);
            $itemSummary = $this->buildItemSummaryLabel($storagePrice);
            $storageContainer[] = [
                'item_name' => 'STORAGE' . $label,
                'unit' => $storagePrice['price_subtype'] . $rule,
                'type' => $storagePrice['price_type'],
                'quantity' => $storagePrice['total_container'],
                'unit_price' => $storagePrice['price'],
                'unit_multiplier' => $multiplier,
                'total' => $storagePrice['price'] * $storagePrice['total_container'] * $multiplier,
                'description' => $description,
                'item_summary' => $itemSummary,
            ];
        }

        return $storageContainer;
    }

    /**
     * Query storage by goods combination rules.
     * @param $conditionRules
     * @param $booking
     * @param $isExtension
     * @param $isDepo
     * @return array
     */
    private function queryStorageGoods($conditionRules, $booking, $isExtension, $isDepo)
    {
        // used for PER_TONNAGE and PER_VOLUME, for optimization we build different query
        // to find maximum value of min_weight (this is could be min_volume as well TODO: rename column `min_weight` to `min_value`)
        // let's say we have min_weight = 0 (>0) and min_weight = 5 (>5), we need to find maximum value of each see getBaseInvoicePrice()
        // this query little bit heavy (consider add column max_weight/max_value in database so we do not need to querying it)
        // we use as necessary so we must find out if current rule contain PER_TONNAGE or PER_VOLUME (we have special conditions as well for this)
        $isMultiValue = false;
        $multiTypeColumn = '';

        $columns = [];
        $columnJoin = '1=1';
        foreach ($conditionRules as $conditionRule) {
            if (!preg_match('/^NOT-/', $conditionRule)) {
                if (key_exists($conditionRule, self::RULE_MAPPING_DATA)) {
                    if (!empty(self::RULE_MAPPING_DATA[$conditionRule])) {
                        $columns[] = self::RULE_MAPPING_DATA[$conditionRule];
                        $columnJoin .= ' AND booking_goods.' . self::RULE_MAPPING_DATA[$conditionRule] . '=ref_component_prices.' . self::RULE_MAPPING_PRICE[$conditionRule];
                    }

                    if ($conditionRule == 'PER_TONNAGE') {
                        $isMultiValue = true;
                        $multiTypeColumn = 'booking_goods.total_goods_tonnage';
                    }
                    if ($conditionRule == 'PER_VOLUME') {
                        $isMultiValue = true;
                        $multiTypeColumn = 'booking_goods.total_goods_volume';
                    }
                }
            }
        }
        $columnGroup = if_empty(implode(',', $columns), '', ',');

        $fromDate = 'DATE(completed_at)';
        if ($isExtension) {
            $fromDate = "'" . date('Y-m-d H:i:s', strtotime("+1 day", strtotime($booking['inbound_date']))) . "'";
        }

        $untilDate = "'{$booking['outbound_date']}'";
        if ($isDepo) {
            $fromDate = 'inbound_date';
            $untilDate = 'outbound_date';
            $queryStockGoods = $this->getBaseInOutGoods($booking);
        } else {
            $queryStockGoods = $this->getBaseStockGoods($booking);
        }

        $prices = $this->getBaseInvoicePrice($isMultiValue)->select([
            'booking_goods.goods_names',
            'booking_goods.total_goods',
            'booking_goods.total_goods_quantity',
            'booking_goods.total_goods_volume',
            'booking_goods.total_goods_tonnage',
            'booking_goods.from_date',
            'booking_goods.until_date',
            'DATEDIFF(booking_goods.until_date, booking_goods.from_date) + 1 AS total_day',
        ]);

        foreach ($columns as $column) {
            $prices->select('booking_goods.' . $column);
        }

        $prices = $prices->join("(
                SELECT 
                    GROUP_CONCAT(CONCAT(goods_name, ' (', quantity, unit, '-', tonnage, 'Kg-', volume, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names, 
                    COUNT(DISTINCT id_goods) AS total_goods,
                    SUM(quantity) AS total_goods_quantity,
                    IF(SUM(volume) < {$this->roundItemValue}, {$this->roundItemValue}, SUM(volume)) AS total_goods_volume,
                    IF(SUM(tonnage / 1000) < {$this->roundItemValue}, {$this->roundItemValue}, SUM(tonnage / 1000)) AS total_goods_tonnage,
                    {$fromDate} AS from_date,
                    {$untilDate} AS until_date
                    {$columnGroup}
                FROM ($queryStockGoods) AS stock_goods 
                WHERE id_booking = '{$booking['id']}'
                GROUP BY {$fromDate}, {$untilDate} 
                {$columnGroup}
            ) AS booking_goods", $columnJoin)
            ->where([
                'ref_component_prices.id_branch' => $booking['id_branch'],
                'ref_component_prices.id_customer' => $booking['id_customer'],
                'ref_component_prices.price_type' => 'STORAGE',
                'ref_component_prices.price_subtype' => 'GOODS',
            ]);

        if ($isMultiValue) {
            // if max_weight (max_value) is null then we assume that maximum number is infinity,
            // like >5 then by default we do not to limit by maximum number, but >0 we need to limit maximum if number is not null which is 5
            if (!empty($multiTypeColumn)) {
                $prices->where("{$multiTypeColumn} > min_weight")
                    ->where("IF((max_weight IS NULL OR max_weight = 0), TRUE, ({$multiTypeColumn} <= max_weight))");
            }
        }

        $prices->group_start();
        foreach ($conditionRules as $conditionRule) {
            if (preg_match('/^NOT-/', $conditionRule)) {
                $condNegationExtract = preg_replace('/^NOT-/', '', $conditionRule);
                $prices->not_like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$condNegationExtract},");
            } else {
                $prices->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$conditionRule},");
            }
        }
        $prices->group_end();

        $storagePrices = $prices->get()->result_array();

        $storageGoods = [];
        foreach ($storagePrices as $storagePrice) {
            // ignore rule by selecting either volume or tonnage
            if (!preg_match("/{$this->ignoreGoodsRule}/", $storagePrice['rule'])) {
                $label = $this->buildStorageLabel($storagePrice);
                $multiplier = $this->buildMultiplierValue($storagePrice);
                $description = $this->buildDescriptionLabel($storagePrice);
                $rule = $this->buildRuleLabel($storagePrice);
                $quantity = $this->buildQuantityLabel($storagePrice);
                $itemSummary = $this->buildItemSummaryLabel($storagePrice);
                $storageGoods[] = [
                    'item_name' => 'STORAGE' . $label,
                    'unit' => $storagePrice['price_subtype'] . $rule,
                    'type' => $storagePrice['price_type'],
                    'quantity' => $quantity,
                    'unit_price' => $storagePrice['price'],
                    'unit_multiplier' => $multiplier,
                    'total' => $storagePrice['price'] * $quantity * $multiplier,
                    'description' => $description,
                    'item_summary' => $itemSummary,
                ];
            }
        }

        return $storageGoods;
    }


    /**
     * Get handling price
     * @param $booking
     * @param null $handling
     * @param bool $isDepo
     * @return array
     */
    public function getHandlingTypePrice($booking, $handling = null, $isDepo = false)
    {
        $invoicePrice = [];

        foreach (self::PRICE_TYPES['HANDLING'] as $type) {
            $rules = $this->getMatchedRules('HANDLING', $type);
            foreach ($rules as $rule) {
                if ($type == 'ACTIVITY') {
                    $handlingActivities = $this->queryHandlingActivity($rule, $booking, $handling, $isDepo);
                    if (!empty($handlingActivities)) {
                        $invoicePrice = array_merge($invoicePrice, $handlingActivities);
                    }
                } elseif ($type == 'CONTAINER') {
                    $handlingContainers = $this->queryHandlingContainer($rule, $booking, $handling, $isDepo);
                    if (!empty($handlingContainers)) {
                        $invoicePrice = array_merge($invoicePrice, $handlingContainers);
                    }
                } elseif ($type == 'GOODS') {
                    $handlingGoods = $this->queryHandlingGoods($rule, $booking, $handling, $isDepo);
                    if (!empty($handlingGoods)) {
                        $invoicePrice = array_merge($invoicePrice, $handlingGoods);
                    }
                }
            }
        }

        return $invoicePrice;
    }

    /**
     * Query handling by activity combination rules.
     * @param $conditionRules
     * @param $booking
     * @param null $handling
     * @param bool $isDepo
     * @return array
     */
    private function queryHandlingActivity($conditionRules, $booking, $handling = null, $isDepo = false)
    {
        $handlingTypeInbound = get_setting('default_inbound_handling');
        $handlingTypeOutbound = get_setting('default_outbound_handling');

        $queryStockContainers = $this->getBaseStockContainers($booking, true);
        $queryStockGoods = $this->getBaseStockGoods($booking, true);

        $containerIds = '-1';
        $goodsIds = '-1';
        if (!empty($queryStockContainers)) {
            $containerIds = implode(',', array_unique(array_column($queryStockContainers, 'id_container')));
        }
        if (!empty($queryStockGoods)) {
            $goodsIds = implode(',', array_unique(array_column($queryStockGoods, 'id_goods')));
        }

        $handlingCondition = ' AND handlings.status = "APPROVED"';
        if (!empty($handling)) {
            $handlingCondition = " AND handlings.id = {$handling['id']}";
        }

        $prices = $this->getBaseInvoicePrice()->select([
            'ref_handling_types.handling_type',
            'handlings.no_handlings',
            'handlings.total_activity',
            'handlings.no_containers',
            'handlings.goods_names',
        ])
            ->join("(
                SELECT 
                    handlings.id_handling_type, 
                    COUNT(handlings.id) AS total_activity, 
                    GROUP_CONCAT(DISTINCT no_handling) AS no_handlings,
                    no_containers, goods_names
                FROM (
                    SELECT handlings.id, no_handling, id_handling_type FROM handlings 
                    LEFT JOIN (
                        SELECT id, no_reference FROM invoices 
                        WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                    ) AS invoice_handlings ON invoice_handlings.no_reference = handlings.no_handling
                    WHERE invoice_handlings.id IS NULL AND id_booking = '{$booking['id']}' {$handlingCondition}
                ) AS handlings
                LEFT JOIN (
                    SELECT id_handling_type, 
                    GROUP_CONCAT(DISTINCT CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers
                    FROM (
                        SELECT handlings.id, id_handling_type FROM handlings 
                        LEFT JOIN (
                            SELECT id, no_reference FROM invoices 
                            WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                        ) AS invoice_handlings ON invoice_handlings.no_reference = handlings.no_handling
                        WHERE invoice_handlings.id IS NULL AND id_booking = '{$booking['id']}' {$handlingCondition}
                    ) AS handlings
                    LEFT JOIN handling_containers ON handlings.id = handling_containers.id_handling 
                    LEFT JOIN ref_containers ON ref_containers.id = handling_containers.id_container
                    WHERE id_container IN({$containerIds})
                    GROUP BY id_handling_type
                ) AS handling_containers ON handling_containers.id_handling_type = handlings.id_handling_type
                LEFT JOIN (
                    SELECT id_handling_type, 
                    GROUP_CONCAT(DISTINCT CONCAT(ref_goods.name, ' (', handling_goods.quantity, unit, '-', ref_goods.unit_weight * handling_goods.quantity, 'Kg-', ref_goods.unit_volume * handling_goods.quantity, 'M3-', handling_goods.status_danger, ')') SEPARATOR ',') AS goods_names
                    FROM (
                        SELECT handlings.id, id_handling_type FROM handlings 
                        LEFT JOIN (
                            SELECT id, no_reference FROM invoices 
                            WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                        ) AS invoice_handlings ON invoice_handlings.no_reference = handlings.no_handling
                        WHERE invoice_handlings.id IS NULL AND id_booking = '{$booking['id']}' {$handlingCondition}
                    ) AS handlings
                    LEFT JOIN handling_goods ON handlings.id = handling_goods.id_handling 
                    LEFT JOIN ref_goods ON ref_goods.id = handling_goods.id_goods
                    LEFT JOIN ref_units ON ref_units.id = handling_goods.id_unit
                    WHERE id_goods IN({$goodsIds})
                    GROUP BY id_handling_type
                ) AS handling_goods ON handling_goods.id_handling_type = handlings.id_handling_type
                GROUP BY id_handling_type
            ) AS handlings", 'handlings.id_handling_type = ref_component_prices.id_handling_type')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->where([
                'ref_component_prices.id_branch' => $booking['id_branch'],
                'ref_component_prices.id_customer' => $booking['id_customer'],
                'ref_component_prices.price_type' => 'HANDLING',
                'ref_component_prices.price_subtype' => 'ACTIVITY',
            ]);

        if (!empty($conditionRules)) {
            $prices->group_start();
            foreach ($conditionRules as $conditionRule) {
                if (preg_match('/^NOT-/', $conditionRule)) {
                    $condNegationExtract = preg_replace('/^NOT-/', '', $conditionRule);
                    $prices->not_like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$condNegationExtract},");
                } else {
                    $prices->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$conditionRule},");
                }
            }
            $prices->group_end();
        }

        $handlingPrices = $prices->get()->result_array();

        $handlingActivities = [];
        foreach ($handlingPrices as $handlingPrice) {
            $multiplier = $this->buildMultiplierValue($handlingPrice);
            $description = $this->buildDescriptionLabel($handlingPrice);
            $rule = $this->buildRuleLabel($handlingPrice);
            $itemSummary = $this->buildItemSummaryLabel($handlingPrice);

            if ($isDepo) {
                if ($handlingPrice['id_handling_type'] == $handlingTypeInbound) {
                    $handlingPrice['handling_type'] = 'INBOUND';
                } else if ($handlingPrice['id_handling_type'] == $handlingTypeOutbound) {
                    $handlingPrice['handling_type'] = 'LIFT ON';
                }
                $handlingPrice['unit_multiplier'] = 1;
            } else {
                if ($handlingPrice['id_handling_type'] == $handlingTypeInbound) {
                    $handlingPrice['unit_multiplier'] = 2;
                    $handlingPrice['handling_type'] = 'LIFT ON/OFF';
                } else {
                    $handlingPrice['unit_multiplier'] = 1;
                }
            }

            $handlingActivities[] = [
                'item_name' => $handlingPrice['handling_type'],
                'unit' => $handlingPrice['price_subtype'] . $rule,
                'type' => $handlingPrice['price_type'],
                'quantity' => $handlingPrice['total_activity'],
                'unit_price' => $handlingPrice['price'],
                'unit_multiplier' => $handlingPrice['unit_multiplier'] * $multiplier,
                'total' => $multiplier * $handlingPrice['price'] * $handlingPrice['total_activity'] * $handlingPrice['unit_multiplier'] * $multiplier,
                'description' => $handlingPrice['no_handlings'] . if_empty($description, '', ' '),
                'item_summary' => $itemSummary,
            ];
        }

        return $handlingActivities;
    }

    /**
     * Query handling by container combination rules.
     * @param $conditionRules
     * @param $booking
     * @param null $handling
     * @param bool $isDepo
     * @return array
     */
    private function queryHandlingContainer($conditionRules, $booking, $handling = null, $isDepo = false)
    {
        $handlingTypeInbound = get_setting('default_inbound_handling');
        $handlingTypeOutbound = get_setting('default_outbound_handling');

        $containerCondition = 'WHERE id_container IN(-1)';
        if ($isDepo) {
            $containerCondition = '';
        } else {
            $queryStockContainers = $this->getBaseStockContainers($booking, true);
            if (!empty($queryStockContainers)) {
                $containerIds = implode(',', array_unique(array_column($queryStockContainers, 'id_container')));
                $containerCondition = "WHERE id_container IN({$containerIds})";
            }
        }

        $handlingCondition = ' AND handlings.status = "APPROVED"';
        if (!empty($handling)) {
            $handlingCondition = " AND handlings.id = {$handling['id']}";
        }

        $ruleMappingData = [
            'PER_CONTAINER' => '',
            'PER_DAY' => '',
            'PER_SIZE' => 'size', // this column name different because data from master, not stock_containers
            'PER_TYPE' => 'type', // this is too
            'PER_DANGER' => 'status_danger',
            'PER_EMPTY' => 'is_empty',
            'PER_CONDITION' => 'status'
        ];

        $columns = [];
        $columnJoin = 'handlings.id_handling_type = ref_component_prices.id_handling_type';
        foreach ($conditionRules as $conditionRule) {
            if (!preg_match('/^NOT-/', $conditionRule)) {
                if (key_exists($conditionRule, $ruleMappingData)) {
                    if (!empty($ruleMappingData[$conditionRule])) {
                        $columns[] = $ruleMappingData[$conditionRule];
                        $columnJoin .= ' AND handlings.' . $ruleMappingData[$conditionRule] . '=ref_component_prices.' . self::RULE_MAPPING_PRICE[$conditionRule];
                    }
                }
            }
        }
        $columnGroup = if_empty(implode(',', $columns), '', ',');

        $prices = $this->getBaseInvoicePrice()->select([
            'ref_handling_types.handling_type',
            'handlings.no_handlings',
            'handlings.total_activity',
            'handlings.no_containers',
            'handlings.total_container',
            'handlings.total_activity',
        ]);

        foreach ($columns as $column) {
            $prices->select('handlings.' . $column);
        }

        $prices = $prices->join("(
                SELECT 
                    handlings.id_handling_type, 
                    COUNT(DISTINCT handlings.id) AS total_activity, 
                    GROUP_CONCAT(DISTINCT no_handling) AS no_handlings,
                    GROUP_CONCAT(DISTINCT CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers, 
                    COUNT(handling_containers.id) AS total_container
                    {$columnGroup}
                FROM (
                    SELECT handlings.id, no_handling, id_handling_type FROM handlings 
                    LEFT JOIN (
                        SELECT id, no_reference FROM invoices 
                        WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                    ) AS invoice_handlings ON invoice_handlings.no_reference = handlings.no_handling
                    WHERE invoice_handlings.id IS NULL AND id_booking = '{$booking['id']}' {$handlingCondition}
                ) AS handlings
                LEFT JOIN handling_containers ON handlings.id = handling_containers.id_handling 
                LEFT JOIN ref_containers ON ref_containers.id = handling_containers.id_container
                {$containerCondition}
                GROUP BY id_handling_type {$columnGroup}
            ) AS handlings", $columnJoin)
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->where([
                'ref_component_prices.id_branch' => $booking['id_branch'],
                'ref_component_prices.id_customer' => $booking['id_customer'],
                'ref_component_prices.price_type' => 'HANDLING',
                'ref_component_prices.price_subtype' => 'CONTAINER',
            ]);

        $prices->group_start();
        foreach ($conditionRules as $conditionRule) {
            if (preg_match('/^NOT-/', $conditionRule)) {
                $condNegationExtract = preg_replace('/^NOT-/', '', $conditionRule);
                $prices->not_like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$condNegationExtract},");
            } else {
                $prices->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$conditionRule},");
            }
        }
        $prices->group_end();

        $handlingPrices = $prices->get()->result_array();

        $handlingContainer = [];
        foreach ($handlingPrices as $handlingPrice) {
            $multiplier = $this->buildMultiplierValue($handlingPrice);
            $label = $this->buildStorageLabel($handlingPrice);
            $rule = $this->buildRuleLabel($handlingPrice);
            $itemSummary = $this->buildItemSummaryLabel($handlingPrice);

            if ($isDepo) {
                if ($handlingPrice['id_handling_type'] == $handlingTypeInbound) {
                    $handlingPrice['handling_type'] = 'INBOUND';
                } else if ($handlingPrice['id_handling_type'] == $handlingTypeOutbound) {
                    $handlingPrice['handling_type'] = 'LIFT ON';
                }
                $handlingPrice['unit_multiplier'] = 1;
            } else {
                if ($handlingPrice['id_handling_type'] == $handlingTypeInbound) {
                    $handlingPrice['unit_multiplier'] = 2;
                    $handlingPrice['handling_type'] = 'LIFT ON/OFF';
                } else {
                    $handlingPrice['unit_multiplier'] = 1;
                }
            }

            $handlingContainer[] = [
                'item_name' => $handlingPrice['handling_type'] . $label,
                'unit' => $handlingPrice['price_subtype'] . $rule,
                'type' => $handlingPrice['price_type'],
                'quantity' => $handlingPrice['total_container'],
                'unit_price' => $handlingPrice['price'],
                'unit_multiplier' => $handlingPrice['unit_multiplier'] * $multiplier,
                'total' => $handlingPrice['price'] * $handlingPrice['total_container'] * $handlingPrice['unit_multiplier'] * $multiplier,
                'description' => $handlingPrice['no_handlings'],
                'item_summary' => $itemSummary,
            ];
        }

        return $handlingContainer;
    }

    /**
     * Query handling by goods combination rules.
     * @param $conditionRules
     * @param $booking
     * @param null $handling
     * @param bool $isDepo
     * @return array
     */
    private function queryHandlingGoods($conditionRules, $booking, $handling = null, $isDepo = false)
    {
        $handlingTypeInbound = get_setting('default_inbound_handling');

        $goodsCondition = 'WHERE id_goods IN(-1)';
        if ($isDepo) {
            $goodsCondition = '';
        } else {
            $queryStockGoods = $this->getBaseStockGoods($booking, true);
            if (!empty($queryStockGoods)) {
                $goodsIds = implode(',', array_unique(array_column($queryStockGoods, 'id_goods')));
                $goodsCondition = "WHERE id_goods IN({$goodsIds})";
            }
        }

        $handlingCondition = ' AND handlings.status = "APPROVED"';
        if (!empty($handling)) {
            $handlingCondition = " AND handlings.id = {$handling['id']}";
        }

        $isMultiValue = false;
        $multiTypeColumn = '';

        $columns = [];
        $columnJoin = 'handlings.id_handling_type = ref_component_prices.id_handling_type';
        foreach ($conditionRules as $conditionRule) {
            if (!preg_match('/^NOT-/', $conditionRule)) {
                if (key_exists($conditionRule, self::RULE_MAPPING_DATA)) {
                    if (!empty(self::RULE_MAPPING_DATA[$conditionRule])) {
                        $columns[] = self::RULE_MAPPING_DATA[$conditionRule];
                        $columnJoin .= ' AND handlings.' . self::RULE_MAPPING_DATA[$conditionRule] . '=ref_component_prices.' . self::RULE_MAPPING_PRICE[$conditionRule];
                    }

                    if ($conditionRule == 'PER_TONNAGE') {
                        $isMultiValue = true;
                        $multiTypeColumn = 'handlings.total_goods_tonnage';
                    }
                    if ($conditionRule == 'PER_VOLUME') {
                        $isMultiValue = true;
                        $multiTypeColumn = 'handlings.total_goods_volume';
                    }
                }
            }
        }
        $columnGroup = if_empty(implode(',', $columns), '', ',');

        $prices = $this->getBaseInvoicePrice($isMultiValue)->select([
            'ref_handling_types.handling_type',
            'handlings.no_handlings',
            'handlings.total_activity',
            'handlings.goods_names',
            'handlings.total_goods',
            'handlings.total_goods_quantity',
            'handlings.total_goods_volume',
            'handlings.total_goods_tonnage',
            'handlings.total_activity',
        ]);

        foreach ($columns as $column) {
            $prices->select('handlings.' . $column);
        }

        $prices = $prices->join("(
                SELECT 
                    handlings.id_handling_type, 
                    COUNT(DISTINCT handlings.id) AS total_activity, 
                    GROUP_CONCAT(DISTINCT no_handling) AS no_handlings,
                    GROUP_CONCAT(CONCAT(name, ' (', quantity, unit, '-', ref_goods.unit_weight * quantity, 'Kg-', ref_goods.unit_volume * quantity, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names,
                    COUNT(handling_goods.id) AS total_goods,
                    SUM(quantity) AS total_goods_quantity,
                    IF(SUM(ref_goods.unit_volume * quantity) < {$this->roundItemValue}, {$this->roundItemValue}, SUM(ref_goods.unit_volume * quantity)) AS total_goods_volume,
                    IF(SUM(ref_goods.unit_weight * quantity / 1000) < {$this->roundItemValue}, {$this->roundItemValue}, SUM(ref_goods.unit_weight * quantity / 1000)) AS total_goods_tonnage                   
                    {$columnGroup}
                FROM (
                    SELECT handlings.id, no_handling, id_handling_type FROM handlings 
                    LEFT JOIN (
                        SELECT id, no_reference FROM invoices 
                        WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                    ) AS invoice_handlings ON invoice_handlings.no_reference = handlings.no_handling
                    WHERE invoice_handlings.id IS NULL AND id_booking = '{$booking['id']}' {$handlingCondition}
                ) AS handlings
                LEFT JOIN handling_goods ON handlings.id = handling_goods.id_handling 
                LEFT JOIN ref_goods ON ref_goods.id = handling_goods.id_goods
                LEFT JOIN ref_units ON ref_units.id = handling_goods.id_unit
                {$goodsCondition}
                GROUP BY id_handling_type {$columnGroup}
            ) AS handlings", $columnJoin)
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->where([
                'ref_component_prices.id_branch' => $booking['id_branch'],
                'ref_component_prices.id_customer' => $booking['id_customer'],
                'ref_component_prices.price_type' => 'HANDLING',
                'ref_component_prices.price_subtype' => 'GOODS',
            ]);

        if ($isMultiValue) {
            // if max_weight (max_value) is null then we assume that maximum number is infinity,
            // like >5 then by default we do not to limit by maximum number, but >0 we need to limit maximum if number is not null which is 5
            if (!empty($multiTypeColumn)) {
                $prices->where("{$multiTypeColumn} > min_weight")
                    ->where("IF((max_weight IS NULL OR max_weight = 0), TRUE, ({$multiTypeColumn} <= max_weight))");
            }
        }

        $prices->group_start();
        foreach ($conditionRules as $conditionRule) {
            if (preg_match('/^NOT-/', $conditionRule)) {
                $condNegationExtract = preg_replace('/^NOT-/', '', $conditionRule);
                $prices->not_like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$condNegationExtract},");
            } else {
                $prices->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$conditionRule},");
            }
        }
        $prices->group_end();

        $handlingPrices = $prices->get()->result_array();

        $handlingGoods = [];
        foreach ($handlingPrices as $handlingPrice) {
            // ignore rule by selecting either volume or tonnage
            if (!preg_match("/{$this->ignoreGoodsRule}/", $handlingPrice['rule'])) {
                $multiplier = $this->buildMultiplierValue($handlingPrice);
                $label = $this->buildStorageLabel($handlingPrice);
                $rule = $this->buildRuleLabel($handlingPrice);
                $quantity = $this->buildQuantityLabel($handlingPrice);
                $itemSummary = $this->buildItemSummaryLabel($handlingPrice);

                if ($handlingPrice['id_handling_type'] == $handlingTypeInbound) {
                    $handlingPrice['unit_multiplier'] = 1; // we could set 2
                    $handlingPrice['handling_type'] = 'RECEIVING';
                } else {
                    $handlingPrice['unit_multiplier'] = 1;
                }

                $handlingGoods[] = [
                    'item_name' => $handlingPrice['handling_type'] . $label,
                    'unit' => $handlingPrice['price_subtype'] . $rule,
                    'type' => $handlingPrice['price_type'],
                    'quantity' => $quantity,
                    'unit_price' => $handlingPrice['price'],
                    'unit_multiplier' => $handlingPrice['unit_multiplier'] * $multiplier,
                    'total' => $handlingPrice['price'] * $quantity * $handlingPrice['unit_multiplier'] * $multiplier,
                    'description' => $handlingPrice['no_handlings'],
                    'item_summary' => $itemSummary,
                ];
            }
        }

        return $handlingGoods;
    }

    /**
     * Get handling component price.
     * @param $booking
     * @param $handling
     * @param bool $isDepo
     * @return array
     */
    public function getHandlingComponentTypePrice($booking, $handling = null, $isDepo = false)
    {
        $invoicePrice = [];

        foreach (self::PRICE_TYPES['COMPONENT'] as $type) {
            $rules = $this->getMatchedRules('COMPONENT', $type);
            foreach ($rules as $rule) {
                if ($type == 'ACTIVITY') {
                    $componentActivities = $this->queryHandlingComponentActivity($rule, $booking, $handling);
                    if (!empty($componentActivities)) {
                        $invoicePrice = array_merge($invoicePrice, $componentActivities);
                    }
                } elseif ($type == 'CONTAINER') {
                    $componentContainers = $this->queryHandlingComponentContainer($rule, $booking, $handling, $isDepo);
                    if (!empty($componentContainers)) {
                        $invoicePrice = array_merge($invoicePrice, $componentContainers);
                    }
                } elseif ($type == 'GOODS') {
                    $componentGoods = $this->queryHandlingComponentGoods($rule, $booking, $handling, $isDepo);
                    if (!empty($componentGoods)) {
                        $invoicePrice = array_merge($invoicePrice, $componentGoods);
                    }
                }
            }
        }

        return $invoicePrice;
    }

    /**
     * Query component by activity combination rules.
     * @param $conditionRules
     * @param $booking
     * @param null $handling
     * @return array
     */
    private function queryHandlingComponentActivity($conditionRules, $booking, $handling = null)
    {
        $queryStockContainers = $this->getBaseStockContainers($booking, true);
        $queryStockGoods = $this->getBaseStockGoods($booking, true);

        $containerIds = '-1';
        $goodsIds = '-1';
        if (!empty($queryStockContainers)) {
            $containerIds = implode(',', array_unique(array_column($queryStockContainers, 'id_container')));
        }
        if (!empty($queryStockGoods)) {
            $goodsIds = implode(',', array_unique(array_column($queryStockGoods, 'id_goods')));
        }

        $handlingCondition = ' AND handlings.status = "APPROVED"';
        if (!empty($handling)) {
            $handlingCondition = " AND handlings.id = {$handling['id']}";
        }

        $prices = $this->getBaseInvoicePrice()->select([
            'ref_handling_types.handling_type',
            'ref_components.handling_component',
            'handlings.no_handlings',
            'handlings.total_activity',
            'handlings.no_containers',
            'handlings.goods_names',
        ])
            ->join("(
                SELECT 
                    handlings.id_handling_type,
                    handling_components.id_component, 
                    COUNT(DISTINCT handlings.id) AS total_activity, 
                    GROUP_CONCAT(DISTINCT no_handling) AS no_handlings,
                    no_containers, goods_names
                FROM (
                    SELECT handlings.id, no_handling, id_handling_type FROM handlings 
                    LEFT JOIN (
                        SELECT id, no_reference FROM invoices 
                        WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                    ) AS invoice_handlings ON invoice_handlings.no_reference = handlings.no_handling
                    WHERE invoice_handlings.id IS NULL AND id_booking = '{$booking['id']}' {$handlingCondition}
                ) AS handlings
                LEFT JOIN (
                    SELECT id_handling_type, 
                    GROUP_CONCAT(DISTINCT CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers
                    FROM (
                        SELECT handlings.id, id_handling_type FROM handlings 
                        LEFT JOIN (
                            SELECT id, no_reference FROM invoices 
                            WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                        ) AS invoice_handlings ON invoice_handlings.no_reference = handlings.no_handling
                        WHERE invoice_handlings.id IS NULL AND id_booking = '{$booking['id']}' {$handlingCondition}
                    ) AS handlings
                    LEFT JOIN handling_containers ON handlings.id = handling_containers.id_handling 
                    LEFT JOIN ref_containers ON ref_containers.id = handling_containers.id_container
                    WHERE id_container IN({$containerIds})
                    GROUP BY id_handling_type
                ) AS handling_containers ON handling_containers.id_handling_type = handlings.id_handling_type
                LEFT JOIN (
                    SELECT id_handling_type, 
                    GROUP_CONCAT(DISTINCT CONCAT(ref_goods.name, ' (', handling_goods.quantity, unit, '-', ref_goods.unit_weight * handling_goods.quantity, 'Kg-', ref_goods.unit_volume * handling_goods.quantity, 'M3-', handling_goods.status_danger, ')') SEPARATOR ',') AS goods_names
                    FROM (
                        SELECT handlings.id, id_handling_type FROM handlings 
                        LEFT JOIN (
                            SELECT id, no_reference FROM invoices 
                            WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                        ) AS invoice_handlings ON invoice_handlings.no_reference = handlings.no_handling
                        WHERE invoice_handlings.id IS NULL AND id_booking = '{$booking['id']}' {$handlingCondition}
                    ) AS handlings
                    LEFT JOIN handling_goods ON handlings.id = handling_goods.id_handling 
                    LEFT JOIN ref_goods ON ref_goods.id = handling_goods.id_goods
                    LEFT JOIN ref_units ON ref_units.id = handling_goods.id_unit
                    WHERE id_goods IN({$goodsIds})
                    GROUP BY id_handling_type
                ) AS handling_goods ON handling_goods.id_handling_type = handlings.id_handling_type
                INNER JOIN handling_components ON handlings.id = handling_components.id_handling
                GROUP BY handlings.id_handling_type, handling_components.id_component
            ) AS handlings",
                'handlings.id_handling_type = ref_component_prices.id_handling_type 
                    AND handlings.id_component = ref_component_prices.id_component')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('ref_components', 'ref_components.id = handlings.id_component')
            ->where([
                'ref_component_prices.id_branch' => $booking['id_branch'],
                'ref_component_prices.id_customer' => $booking['id_customer'],
                'ref_component_prices.price_type' => 'COMPONENT',
                'ref_component_prices.price_subtype' => 'ACTIVITY',
            ]);

        if (!empty($conditionRules)) {
            $prices->group_start();
            foreach ($conditionRules as $conditionRule) {
                if (preg_match('/^NOT-/', $conditionRule)) {
                    $condNegationExtract = preg_replace('/^NOT-/', '', $conditionRule);
                    $prices->not_like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$condNegationExtract},");
                } else {
                    $prices->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$conditionRule},");
                }
            }
            $prices->group_end();
        }

        $componentPrices = $prices->get()->result_array();

        $componentActivities = [];
        foreach ($componentPrices as $componentPrice) {
            $multiplier = $this->buildMultiplierValue($componentPrice);
            $description = $this->buildDescriptionLabel($componentPrice);
            $rule = $this->buildRuleLabel($componentPrice);
            $itemSummary = $this->buildItemSummaryLabel($componentPrice);

            $componentActivities[] = [
                'item_name' => $componentPrice['handling_component'] . ' (' . $componentPrice['handling_type'] . ')',
                'unit' => $componentPrice['price_subtype'] . $rule,
                'type' => $componentPrice['price_type'],
                'quantity' => $componentPrice['total_activity'],
                'unit_price' => $componentPrice['price'],
                'unit_multiplier' => $multiplier,
                'total' => $multiplier * $componentPrice['price'] * $componentPrice['total_activity'],
                'description' => $componentPrice['no_handlings'] . if_empty($description, '', ' '),
                'item_summary' => $itemSummary,
            ];
        }

        return $componentActivities;
    }

    /**
     * Query component by container combination rules.
     * @param $conditionRules
     * @param $booking
     * @param null $handling
     * @param bool $isDepo
     * @return array
     */
    private function queryHandlingComponentContainer($conditionRules, $booking, $handling = null, $isDepo = false)
    {
        $containerCondition = 'WHERE id_container IN(-1)';
        if ($isDepo) {
            $containerCondition = '';
        } else {
            $queryStockContainers = $this->getBaseStockContainers($booking, true);
            if (!empty($queryStockContainers)) {
                $containerIds = implode(',', array_unique(array_column($queryStockContainers, 'id_container')));
                $containerCondition = "WHERE id_container IN({$containerIds})";
            }
        }

        $handlingCondition = ' AND handlings.status = "APPROVED"';
        if (!empty($handling)) {
            $handlingCondition = " AND handlings.id = {$handling['id']}";
        }

        $ruleMappingData = [
            'PER_CONTAINER' => '',
            'PER_DAY' => '',
            'PER_SIZE' => 'size', // this column name different because data from master, not stock_containers
            'PER_TYPE' => 'type', // this is too
            'PER_DANGER' => 'status_danger',
            'PER_EMPTY' => 'is_empty',
            'PER_CONDITION' => 'status'
        ];

        $columns = [];
        $columnJoin = 'handlings.id_handling_type = ref_component_prices.id_handling_type AND handlings.id_component = ref_component_prices.id_component';
        foreach ($conditionRules as $conditionRule) {
            if (!preg_match('/^NOT-/', $conditionRule)) {
                if (key_exists($conditionRule, $ruleMappingData)) {
                    if (!empty($ruleMappingData[$conditionRule])) {
                        $columns[] = $ruleMappingData[$conditionRule];
                        $columnJoin .= ' AND handlings.' . $ruleMappingData[$conditionRule] . '=ref_component_prices.' . self::RULE_MAPPING_PRICE[$conditionRule];
                    }
                }
            }
        }
        $columnGroup = if_empty(implode(',', $columns), '', ',');

        $prices = $this->getBaseInvoicePrice()->select([
            'ref_handling_types.handling_type',
            'ref_components.handling_component',
            'handlings.quantity_component',
            'handlings.total_component',
            'handlings.no_handlings',
            'handlings.total_activity',
            'handlings.no_containers',
            'handlings.total_container',
        ]);

        foreach ($columns as $column) {
            $prices->select('handlings.' . $column);
        }

        $prices = $prices->join("(
                SELECT 
                    handlings.id_handling_type, 
                    handling_components.id_component, 
                    handling_components.quantity AS quantity_component,
                    SUM(handling_components.quantity) AS total_component,
                    COUNT(DISTINCT handlings.id) AS total_activity, 
                    GROUP_CONCAT(DISTINCT no_handling) AS no_handlings,
                    no_containers,
                    total_container
                    {$columnGroup}
                FROM (
                    SELECT handlings.id, no_handling, id_handling_type FROM handlings 
                    LEFT JOIN (
                        SELECT id, no_reference FROM invoices 
                        WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                    ) AS invoice_handlings ON invoice_handlings.no_reference = handlings.no_handling
                    WHERE invoice_handlings.id IS NULL AND id_booking = '{$booking['id']}' {$handlingCondition}
                ) AS handlings
                INNER JOIN handling_components ON handlings.id = handling_components.id_handling
                LEFT JOIN (
                    SELECT id_handling_type, handling_components.id_component, handling_components.quantity,
                    COUNT(handling_containers.id) AS total_container,
                    GROUP_CONCAT(DISTINCT CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers
                    {$columnGroup}
                    FROM (
                        SELECT handlings.id, id_handling_type FROM handlings 
                        LEFT JOIN (
                            SELECT id, no_reference FROM invoices 
                            WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                        ) AS invoice_handlings ON invoice_handlings.no_reference = handlings.no_handling
                        WHERE invoice_handlings.id IS NULL AND id_booking = '{$booking['id']}' {$handlingCondition}
                    ) AS handlings
                    LEFT JOIN handling_containers ON handlings.id = handling_containers.id_handling 
                    LEFT JOIN ref_containers ON ref_containers.id = handling_containers.id_container
                    INNER JOIN handling_components ON handlings.id = handling_components.id_handling
                    {$containerCondition}
                    GROUP BY id_handling_type, handling_components.id_component, handling_components.quantity {$columnGroup}
                ) AS handling_containers ON handling_containers.id_handling_type = handlings.id_handling_type
                    AND handling_containers.id_component = handling_components.id_component
                        AND handling_containers.quantity = handling_components.quantity
                GROUP BY handlings.id_handling_type, handling_components.id_component, handling_components.quantity, no_containers, total_container 
                {$columnGroup}
            ) AS handlings", $columnJoin)
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('ref_components', 'ref_components.id = handlings.id_component')
            ->where([
                'ref_component_prices.id_branch' => $booking['id_branch'],
                'ref_component_prices.id_customer' => $booking['id_customer'],
                'ref_component_prices.price_type' => 'COMPONENT',
                'ref_component_prices.price_subtype' => 'CONTAINER',
            ]);

        $prices->group_start();
        foreach ($conditionRules as $conditionRule) {
            if (preg_match('/^NOT-/', $conditionRule)) {
                $condNegationExtract = preg_replace('/^NOT-/', '', $conditionRule);
                $prices->not_like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$condNegationExtract},");
            } else {
                $prices->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$conditionRule},");
            }
        }
        $prices->group_end();

        $componentPrices = $prices->get()->result_array();

        $componentContainer = [];
        foreach ($componentPrices as $componentPrice) {
            $multiplier = $this->buildMultiplierValue($componentPrice);
            $rule = $this->buildRuleLabel($componentPrice);
            $description = $this->buildDescriptionLabel($componentPrice);
            $itemSummary = $this->buildItemSummaryLabel($componentPrice);

            $componentContainer[] = [
                'item_name' => $componentPrice['handling_component'] . ' QTY: ' . numerical($componentPrice['total_component'], 3, true) . ' (' . $componentPrice['handling_type'] . ')',
                'unit' => $componentPrice['price_subtype'] . $rule,
                'type' => $componentPrice['price_type'],
                'quantity' => $componentPrice['total_container'],
                'unit_price' => $componentPrice['price'],
                'unit_multiplier' => $componentPrice['quantity_component'] * $multiplier,
                'total' => $componentPrice['price'] * $componentPrice['quantity_component'] * $componentPrice['total_container'] * $multiplier,
                'description' => $componentPrice['no_handlings'] . if_empty($description, '', ' '),
                'item_summary' => $itemSummary,
            ];
        }

        return $componentContainer;
    }

    /**
     * Query component by goods combination rules.
     * @param $conditionRules
     * @param $booking
     * @param null $handling
     * @param bool $isDepo
     * @return array
     */
    private function queryHandlingComponentGoods($conditionRules, $booking, $handling = null, $isDepo = false)
    {
        $goodsCondition = 'WHERE id_goods IN(-1)';
        if ($isDepo) {
            $goodsCondition = '';
        } else {
            $queryStockGoods = $this->getBaseStockGoods($booking, true);
            if (!empty($queryStockGoods)) {
                $goodsIds = implode(',', array_unique(array_column($queryStockGoods, 'id_goods')));
                $goodsCondition = "WHERE id_goods IN({$goodsIds})";
            }
        }

        $handlingCondition = ' AND handlings.status = "APPROVED"';
        if (!empty($handling)) {
            $handlingCondition = " AND handlings.id = {$handling['id']}";
        }

        $isMultiValue = false;
        $multiTypeColumn = '';

        $columns = [];
        $columnJoin = 'handlings.id_handling_type = ref_component_prices.id_handling_type AND handlings.id_component = ref_component_prices.id_component';
        foreach ($conditionRules as $conditionRule) {
            if (!preg_match('/^NOT-/', $conditionRule)) {
                if (key_exists($conditionRule, self::RULE_MAPPING_DATA)) {
                    if (!empty(self::RULE_MAPPING_DATA[$conditionRule])) {
                        $columns[] = self::RULE_MAPPING_DATA[$conditionRule];
                        $columnJoin .= ' AND handlings.' . self::RULE_MAPPING_DATA[$conditionRule] . '=ref_component_prices.' . self::RULE_MAPPING_PRICE[$conditionRule];
                    }

                    if ($conditionRule == 'PER_TONNAGE') {
                        $isMultiValue = true;
                        $multiTypeColumn = 'handlings.total_goods_tonnage';
                    }
                    if ($conditionRule == 'PER_VOLUME') {
                        $isMultiValue = true;
                        $multiTypeColumn = 'handlings.total_goods_volume';
                    }
                }
            }
        }
        $columnGroup = if_empty(implode(',', $columns), '', ',');

        $prices = $this->getBaseInvoicePrice($isMultiValue)->select([
            'ref_handling_types.handling_type',
            'ref_components.handling_component',
            'handlings.quantity_component',
            'handlings.total_component',
            'handlings.no_handlings',
            'handlings.total_activity',
            'handlings.goods_names',
            'handlings.total_goods',
            'handlings.total_goods_quantity',
            'handlings.total_goods_volume',
            'handlings.total_goods_tonnage',
        ]);

        foreach ($columns as $column) {
            $prices->select('handlings.' . $column);
        }

        $prices = $prices->join("(
                SELECT 
                    handlings.id_handling_type, 
                    handling_components.id_component, 
                    handling_components.quantity AS quantity_component,
                    SUM(handling_components.quantity) AS total_component,
                    COUNT(DISTINCT handlings.id) AS total_activity, 
                    GROUP_CONCAT(DISTINCT no_handling) AS no_handlings,
                    goods_names,
                    total_goods, 
                    total_goods_quantity,
                    total_goods_volume,
                    total_goods_tonnage
                    {$columnGroup}
                FROM (
                    SELECT handlings.id, no_handling, id_handling_type FROM handlings 
                    LEFT JOIN (
                        SELECT id, no_reference FROM invoices 
                        WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                    ) AS invoice_handlings ON invoice_handlings.no_reference = handlings.no_handling
                    WHERE invoice_handlings.id IS NULL AND id_booking = '{$booking['id']}' {$handlingCondition}
                ) AS handlings
                INNER JOIN (SELECT id_handling, id_component, quantity FROM handling_components) AS handling_components 
                    ON handlings.id = handling_components.id_handling
                INNER JOIN (
                    SELECT id_handling_type, handling_components.id_component, handling_components.quantity AS quantity_component,
                    GROUP_CONCAT(CONCAT(name, ' (', handling_goods.quantity, unit, '-', ref_goods.unit_weight * handling_goods.quantity, 'Kg-', ref_goods.unit_volume * handling_goods.quantity, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names,
                    COUNT(handling_goods.id) AS total_goods,
                    SUM(handling_goods.quantity) AS total_goods_quantity,
                    IF(SUM(ref_goods.unit_volume * handling_goods.quantity) < {$this->roundItemValue}, {$this->roundItemValue}, SUM(ref_goods.unit_volume * handling_goods.quantity)) AS total_goods_volume,
                    IF(SUM(ref_goods.unit_weight * handling_goods.quantity / 1000) < {$this->roundItemValue}, {$this->roundItemValue}, SUM(ref_goods.unit_weight * handling_goods.quantity / 1000)) AS total_goods_tonnage
                    {$columnGroup}
                    FROM (
                        SELECT handlings.id, id_handling_type FROM handlings 
                        LEFT JOIN (
                            SELECT id, no_reference FROM invoices 
                            WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                        ) AS invoice_handlings ON invoice_handlings.no_reference = handlings.no_handling
                        WHERE invoice_handlings.id IS NULL AND id_booking = '{$booking['id']}' {$handlingCondition}
                    ) AS handlings
                    LEFT JOIN handling_goods ON handlings.id = handling_goods.id_handling
                    LEFT JOIN ref_goods ON ref_goods.id = handling_goods.id_goods
                    LEFT JOIN ref_units ON ref_units.id = handling_goods.id_unit
                    INNER JOIN (SELECT id_handling, id_component, quantity FROM handling_components) AS handling_components 
                        ON handlings.id = handling_components.id_handling
                    {$goodsCondition}
                    GROUP BY id_handling_type, handling_components.id_component, handling_components.quantity {$columnGroup}
                ) AS handling_goods ON handling_goods.id_handling_type = handlings.id_handling_type
                    AND handling_goods.id_component = handling_components.id_component
                        AND handling_goods.quantity_component = handling_components.quantity
                GROUP BY handlings.id_handling_type, handling_components.id_component, handling_components.quantity, 
                    goods_names, total_goods, total_goods_quantity, total_goods_volume, total_goods_tonnage 
                    {$columnGroup}
            ) AS handlings", $columnJoin)
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('ref_components', 'ref_components.id = handlings.id_component')
            ->where([
                'ref_component_prices.id_branch' => $booking['id_branch'],
                'ref_component_prices.id_customer' => $booking['id_customer'],
                'ref_component_prices.price_type' => 'COMPONENT',
                'ref_component_prices.price_subtype' => 'GOODS',
            ]);

        if ($isMultiValue) {
            // if max_weight (max_value) is null then we assume that maximum number is infinity,
            // like >5 then by default we do not to limit by maximum number, but >0 we need to limit maximum if number is not null which is 5
            if (!empty($multiTypeColumn)) {
                $prices->where("{$multiTypeColumn} > min_weight")
                    ->where("IF((max_weight IS NULL OR max_weight = 0), TRUE, ({$multiTypeColumn} <= max_weight))");
            }
        }

        $prices->group_start();
        foreach ($conditionRules as $conditionRule) {
            if (preg_match('/^NOT-/', $conditionRule)) {
                $condNegationExtract = preg_replace('/^NOT-/', '', $conditionRule);
                $prices->not_like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$condNegationExtract},");
            } else {
                $prices->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$conditionRule},");
            }
        }
        $prices->group_end();

        $componentPrices = $prices->get()->result_array();

        $componentGoods = [];
        foreach ($componentPrices as $componentPrice) {
            // ignore rule by selecting either volume or tonnage
            if (!preg_match("/{$this->ignoreGoodsRule}/", $componentPrice['rule'])) {
                $rule = $this->buildRuleLabel($componentPrice);
                $quantity = $this->buildQuantityLabel($componentPrice);
                $multiplier = $this->buildMultiplierValue($componentPrice);
                $description = $this->buildDescriptionLabel($componentPrice);
                $itemSummary = $this->buildItemSummaryLabel($componentPrice);

                $componentGoods[] = [
                    'item_name' => $componentPrice['handling_component'] . ' QTY: ' . numerical($componentPrice['total_component'], 3, true) . ' (' . $componentPrice['handling_type'] . ')',
                    'unit' => $componentPrice['price_subtype'] . $rule,
                    'type' => $componentPrice['price_type'],
                    'quantity' => $quantity,
                    'unit_price' => $componentPrice['price'],
                    'unit_multiplier' => $componentPrice['quantity_component'] * $multiplier,
                    'total' => $componentPrice['price'] * $componentPrice['quantity_component'] * $quantity * $multiplier,
                    'description' => $componentPrice['no_handlings'] . if_empty($description, '', ' '),
                    'item_summary' => $itemSummary,
                ];
            }
        }

        return $componentGoods;
    }

    /**
     * Get work order price
     * @param $booking
     * @param null $workOrder
     * @return array
     */
    public function getWorkOrderTypePrice($booking, $workOrder = null)
    {
        $invoicePrice = [];

        foreach (self::PRICE_TYPES['HANDLING'] as $type) {
            $rules = $this->getMatchedRules('HANDLING', $type);
            foreach ($rules as $rule) {
                if ($type == 'ACTIVITY') {
                    $workOrderActivities = $this->queryWorkOrderActivity($rule, $booking, $workOrder);
                    if (!empty($workOrderActivities)) {
                        $invoicePrice = array_merge($invoicePrice, $workOrderActivities);
                    }
                } elseif ($type == 'CONTAINER') {
                    $workOrderContainers = $this->queryWorkOrderContainer($rule, $booking, $workOrder);
                    if (!empty($workOrderContainers)) {
                        $invoicePrice = array_merge($invoicePrice, $workOrderContainers);
                    }
                } elseif ($type == 'GOODS') {
                    $workOrderGoods = $this->queryWorkOrderGoods($rule, $booking, $workOrder);
                    if (!empty($workOrderGoods)) {
                        $invoicePrice = array_merge($invoicePrice, $workOrderGoods);
                    }
                }
            }
        }

        return $invoicePrice;
    }

    /**
     * Query work order by activity combination rules.
     * @param $conditionRules
     * @param $booking
     * @param null $workOrder
     * @return array
     */
    private function queryWorkOrderActivity($conditionRules, $booking, $workOrder = null)
    {
        $handlingTypeInbound = get_setting('default_inbound_handling');

        $queryStockContainers = $this->getBaseStockContainers($booking, true);
        $queryStockGoods = $this->getBaseStockGoods($booking, true);

        $containerIds = '-1';
        $goodsIds = '-1';
        if (!empty($queryStockContainers)) {
            $containerIds = implode(',', array_unique(array_column($queryStockContainers, 'id_container')));
        }
        if (!empty($queryStockGoods)) {
            $goodsIds = implode(',', array_unique(array_column($queryStockGoods, 'id_goods')));
        }

        $workOrderCondition = '';
        if (!empty($workOrder)) {
            $workOrderCondition = "AND work_orders.id = {$workOrder['id']}";
        }

        $prices = $this->getBaseInvoicePrice()->select([
            'ref_handling_types.handling_type',
            'work_orders.no_work_orders',
            'work_orders.total_activity',
            'work_orders.no_containers',
            'work_orders.goods_names',
        ])
            ->join("(
                SELECT 
                    work_orders.id_handling_type, 
                    COUNT(work_orders.id) AS total_activity, 
                    GROUP_CONCAT(DISTINCT no_work_order) AS no_work_orders,
                    no_containers, goods_names
                FROM (
                    SELECT work_orders.id, no_work_order, id_handling_type FROM work_orders 
                    INNER JOIN handlings ON handlings.id = work_orders.id_handling
                    LEFT JOIN (
                        SELECT id, no_reference FROM invoices 
                        WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                    ) AS invoice_work_orders ON invoice_work_orders.no_reference = work_orders.no_work_order
                    WHERE invoice_work_orders.id IS NULL AND id_booking = '{$booking['id']}' {$workOrderCondition}
                ) AS work_orders
                LEFT JOIN (
                    SELECT id_handling_type, 
                    GROUP_CONCAT(DISTINCT CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers
                    FROM (
                        SELECT work_orders.id, no_work_order, id_handling_type FROM work_orders 
                        INNER JOIN handlings ON handlings.id = work_orders.id_handling
                        LEFT JOIN (
                            SELECT id, no_reference FROM invoices 
                            WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                        ) AS invoice_work_orders ON invoice_work_orders.no_reference = work_orders.no_work_order
                        WHERE invoice_work_orders.id IS NULL AND id_booking = '{$booking['id']}' {$workOrderCondition}
                    ) AS work_orders
                    LEFT JOIN work_order_containers ON work_orders.id = work_order_containers.id_work_order 
                    LEFT JOIN ref_containers ON ref_containers.id = work_order_containers.id_container
                    WHERE id_container IN({$containerIds})
                    GROUP BY id_handling_type
                ) AS work_order_containers ON work_order_containers.id_handling_type = work_orders.id_handling_type
                LEFT JOIN (
                    SELECT id_handling_type, 
                    GROUP_CONCAT(DISTINCT CONCAT(ref_goods.name, ' (', work_order_goods.quantity, unit, '-', ref_goods.unit_weight * work_order_goods.quantity, 'Kg-', ref_goods.unit_volume * work_order_goods.quantity, 'M3-', work_order_goods.status_danger, ')') SEPARATOR ',') AS goods_names
                    FROM (
                        SELECT work_orders.id, no_work_order, id_handling_type FROM work_orders 
                        INNER JOIN handlings ON handlings.id = work_orders.id_handling
                        LEFT JOIN (
                            SELECT id, no_reference FROM invoices 
                            WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                        ) AS invoice_work_orders ON invoice_work_orders.no_reference = work_orders.no_work_order
                        WHERE invoice_work_orders.id IS NULL AND id_booking = '{$booking['id']}' {$workOrderCondition}
                    ) AS work_orders
                    LEFT JOIN work_order_goods ON work_orders.id = work_order_goods.id_work_order 
                    LEFT JOIN ref_goods ON ref_goods.id = work_order_goods.id_goods
                    LEFT JOIN ref_units ON ref_units.id = work_order_goods.id_unit
                    WHERE id_goods IN({$goodsIds})
                    GROUP BY id_handling_type
                ) AS work_order_goods ON work_order_goods.id_handling_type = work_orders.id_handling_type
                GROUP BY id_handling_type
            ) AS work_orders", 'work_orders.id_handling_type = ref_component_prices.id_handling_type')
            ->join('ref_handling_types', 'ref_handling_types.id = work_orders.id_handling_type')
            ->where([
                'ref_component_prices.id_branch' => $booking['id_branch'],
                'ref_component_prices.id_customer' => $booking['id_customer'],
                'ref_component_prices.price_type' => 'HANDLING',
                'ref_component_prices.price_subtype' => 'ACTIVITY',
            ]);

        if (!empty($conditionRules)) {
            $prices->group_start();
            foreach ($conditionRules as $conditionRule) {
                if (preg_match('/^NOT-/', $conditionRule)) {
                    $condNegationExtract = preg_replace('/^NOT-/', '', $conditionRule);
                    $prices->not_like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$condNegationExtract},");
                } else {
                    $prices->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$conditionRule},");
                }
            }
            $prices->group_end();
        }

        $workOrderPrices = $prices->get()->result_array();

        $workOrderActivities = [];
        foreach ($workOrderPrices as $workOrderPrice) {
            $multiplier = $this->buildMultiplierValue($workOrderPrice);
            $description = $this->buildDescriptionLabel($workOrderPrice);
            $rule = $this->buildRuleLabel($workOrderPrice);
            $itemSummary = $this->buildItemSummaryLabel($workOrderPrice);

            if ($workOrderPrice['id_handling_type'] == $handlingTypeInbound) {
                $workOrderPrice['unit_multiplier'] = 2;
                $workOrderPrice['handling_type'] = 'LIFT ON/OFF';
            } else {
                $workOrderPrice['unit_multiplier'] = 1;
            }

            $workOrderActivities[] = [
                'item_name' => $workOrderPrice['handling_type'],
                'unit' => $workOrderPrice['price_subtype'] . $rule,
                'type' => $workOrderPrice['price_type'],
                'quantity' => $workOrderPrice['total_activity'],
                'unit_price' => $workOrderPrice['price'],
                'unit_multiplier' => $workOrderPrice['unit_multiplier'] * $multiplier,
                'total' => $multiplier * $workOrderPrice['price'] * $workOrderPrice['total_activity'] * $workOrderPrice['unit_multiplier'] * $multiplier,
                'description' => $workOrderPrice['no_work_orders'] . if_empty($description, '', ' '),
                'item_summary' => $itemSummary,
            ];
        }

        return $workOrderActivities;
    }

    /**
     * Query work order by container combination rules.
     * @param $conditionRules
     * @param $booking
     * @param null $workOrder
     * @return array
     */
    private function queryWorkOrderContainer($conditionRules, $booking, $workOrder = null)
    {
        $handlingTypeInbound = get_setting('default_inbound_handling');

        $containerIds = '-1';
        $queryStockContainers = $this->getBaseStockContainers($booking, true);
        if (!empty($queryStockContainers)) {
            $containerIds = implode(',', array_unique(array_column($queryStockContainers, 'id_container')));
        }

        $workOrderCondition = '';
        if (!empty($workOrder)) {
            $workOrderCondition = "AND work_orders.id = {$workOrder['id']}";
        }

        $ruleMappingData = [
            'PER_CONTAINER' => '',
            'PER_DAY' => '',
            'PER_SIZE' => 'size', // this column name different because data from master, not stock_containers
            'PER_TYPE' => 'type', // this is too
            'PER_DANGER' => 'status_danger',
            'PER_EMPTY' => 'is_empty',
            'PER_CONDITION' => 'status'
        ];

        $columns = [];
        $columnJoin = 'work_orders.id_handling_type = ref_component_prices.id_handling_type';
        foreach ($conditionRules as $conditionRule) {
            if (!preg_match('/^NOT-/', $conditionRule)) {
                if (key_exists($conditionRule, $ruleMappingData)) {
                    if (!empty($ruleMappingData[$conditionRule])) {
                        $columns[] = $ruleMappingData[$conditionRule];
                        $columnJoin .= ' AND work_orders.' . $ruleMappingData[$conditionRule] . '=ref_component_prices.' . self::RULE_MAPPING_PRICE[$conditionRule];
                    }
                }
            }
        }
        $columnGroup = if_empty(implode(',', $columns), '', ',');

        $prices = $this->getBaseInvoicePrice()->select([
            'ref_handling_types.handling_type',
            'work_orders.no_work_orders',
            'work_orders.total_activity',
            'work_orders.no_containers',
            'work_orders.total_container',
            'work_orders.total_activity',
        ]);

        foreach ($columns as $column) {
            $prices->select('work_orders.' . $column);
        }

        $prices = $prices->join("(
                SELECT 
                    work_orders.id_handling_type, 
                    COUNT(DISTINCT work_orders.id) AS total_activity, 
                    GROUP_CONCAT(DISTINCT no_work_order) AS no_work_orders,
                    GROUP_CONCAT(DISTINCT CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers, 
                    COUNT(work_order_containers.id) AS total_container
                    {$columnGroup}
                FROM (
                    SELECT work_orders.id, no_work_order, id_handling_type FROM work_orders 
                    INNER JOIN handlings ON handlings.id = work_orders.id_handling
                    LEFT JOIN (
                        SELECT id, no_reference FROM invoices 
                        WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                    ) AS invoice_work_orders ON invoice_work_orders.no_reference = work_orders.no_work_order
                    WHERE invoice_work_orders.id IS NULL AND id_booking = '{$booking['id']}' {$workOrderCondition}
                ) AS work_orders
                LEFT JOIN work_order_containers ON work_orders.id = work_order_containers.id_work_order 
                LEFT JOIN ref_containers ON ref_containers.id = work_order_containers.id_container
                WHERE id_container IN({$containerIds})
                GROUP BY id_handling_type {$columnGroup}
            ) AS work_orders", $columnJoin)
            ->join('ref_handling_types', 'ref_handling_types.id = work_orders.id_handling_type')
            ->where([
                'ref_component_prices.id_branch' => $booking['id_branch'],
                'ref_component_prices.id_customer' => $booking['id_customer'],
                'ref_component_prices.price_type' => 'HANDLING',
                'ref_component_prices.price_subtype' => 'CONTAINER',
            ]);

        $prices->group_start();
        foreach ($conditionRules as $conditionRule) {
            if (preg_match('/^NOT-/', $conditionRule)) {
                $condNegationExtract = preg_replace('/^NOT-/', '', $conditionRule);
                $prices->not_like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$condNegationExtract},");
            } else {
                $prices->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$conditionRule},");
            }
        }
        $prices->group_end();

        $workOrderPrices = $prices->get()->result_array();

        $workOrderContainer = [];
        foreach ($workOrderPrices as $workOrderPrice) {
            $multiplier = $this->buildMultiplierValue($workOrderPrice);
            $label = $this->buildStorageLabel($workOrderPrice);
            $rule = $this->buildRuleLabel($workOrderPrice);
            $itemSummary = $this->buildItemSummaryLabel($workOrderPrice);

            if ($workOrderPrice['id_handling_type'] == $handlingTypeInbound) {
                $workOrderPrice['unit_multiplier'] = 2;
                $workOrderPrice['handling_type'] = 'LIFT ON/OFF';
            } else {
                $workOrderPrice['unit_multiplier'] = 1;
            }

            $workOrderContainer[] = [
                'item_name' => $workOrderPrice['handling_type'] . $label,
                'unit' => $workOrderPrice['price_subtype'] . $rule,
                'type' => $workOrderPrice['price_type'],
                'quantity' => $workOrderPrice['total_container'],
                'unit_price' => $workOrderPrice['price'],
                'unit_multiplier' => $workOrderPrice['unit_multiplier'] * $multiplier,
                'total' => $workOrderPrice['price'] * $workOrderPrice['total_container'] * $workOrderPrice['unit_multiplier'] * $multiplier,
                'description' => $workOrderPrice['no_work_orders'],
                'item_summary' => $itemSummary,
            ];
        }

        return $workOrderContainer;
    }

    /**
     * Query work orders by goods combination rules.
     * @param $conditionRules
     * @param $booking
     * @param null $workOrder
     * @return array
     */
    private function queryWorkOrderGoods($conditionRules, $booking, $workOrder = null)
    {
        $handlingTypeInbound = get_setting('default_inbound_handling');

        $goodsIds = '-1';
        $queryStockGoods = $this->getBaseStockGoods($booking, true);
        if (!empty($queryStockGoods)) {
            $goodsIds = implode(',', array_unique(array_column($queryStockGoods, 'id_goods')));
        }

        $workOrderCondition = '';
        if (!empty($workOrder)) {
            $workOrderCondition = "AND work_orders.id = {$workOrder['id']}";
        }

        $isMultiValue = false;
        $multiTypeColumn = '';

        $columns = [];
        $columnJoin = 'work_orders.id_handling_type = ref_component_prices.id_handling_type';
        foreach ($conditionRules as $conditionRule) {
            if (!preg_match('/^NOT-/', $conditionRule)) {
                if (key_exists($conditionRule, self::RULE_MAPPING_DATA)) {
                    if (!empty(self::RULE_MAPPING_DATA[$conditionRule])) {
                        $columns[] = self::RULE_MAPPING_DATA[$conditionRule];
                        $columnJoin .= ' AND work_orders.' . self::RULE_MAPPING_DATA[$conditionRule] . '=ref_component_prices.' . self::RULE_MAPPING_PRICE[$conditionRule];
                    }

                    if ($conditionRule == 'PER_TONNAGE') {
                        $isMultiValue = true;
                        $multiTypeColumn = 'work_orders.total_goods_tonnage';
                    }
                    if ($conditionRule == 'PER_VOLUME') {
                        $isMultiValue = true;
                        $multiTypeColumn = 'work_orders.total_goods_volume';
                    }
                }
            }
        }
        $columnGroup = if_empty(implode(',', $columns), '', ',');

        $prices = $this->getBaseInvoicePrice($isMultiValue)->select([
            'ref_handling_types.handling_type',
            'work_orders.no_work_orders',
            'work_orders.total_activity',
            'work_orders.goods_names',
            'work_orders.total_goods',
            'work_orders.total_goods_quantity',
            'work_orders.total_goods_volume',
            'work_orders.total_goods_tonnage',
            'work_orders.total_activity',
        ]);

        foreach ($columns as $column) {
            $prices->select('work_orders.' . $column);
        }

        $prices = $prices->join("(
                SELECT 
                    work_orders.id_handling_type, 
                    COUNT(DISTINCT work_orders.id) AS total_activity, 
                    GROUP_CONCAT(DISTINCT no_work_order) AS no_work_orders,
                    GROUP_CONCAT(CONCAT(name, ' (', quantity, unit, '-', ref_goods.unit_weight * quantity, 'Kg-', ref_goods.unit_volume * quantity, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names,
                    COUNT(work_order_goods.id) AS total_goods,
                    SUM(quantity) AS total_goods_quantity,
                    IF(SUM(ref_goods.unit_volume * quantity) < {$this->roundItemValue}, {$this->roundItemValue}, SUM(ref_goods.unit_volume * quantity)) AS total_goods_volume,
                    IF(SUM(ref_goods.unit_weight * quantity / 1000) < {$this->roundItemValue}, {$this->roundItemValue}, SUM(ref_goods.unit_weight * quantity / 1000)) AS total_goods_tonnage                  
                    {$columnGroup}
                FROM (
                    SELECT work_orders.id, no_work_order, id_handling_type FROM work_orders 
                    INNER JOIN handlings ON handlings.id = work_orders.id_handling
                    LEFT JOIN (
                        SELECT id, no_reference FROM invoices 
                        WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                    ) AS invoice_work_orders ON invoice_work_orders.no_reference = work_orders.no_work_order
                    WHERE invoice_work_orders.id IS NULL AND id_booking = '{$booking['id']}' {$workOrderCondition}
                ) AS work_orders
                LEFT JOIN work_order_goods ON work_orders.id = work_order_goods.id_work_order 
                LEFT JOIN ref_goods ON ref_goods.id = work_order_goods.id_goods
                LEFT JOIN ref_units ON ref_units.id = work_order_goods.id_unit
                WHERE id_goods IN({$goodsIds})
                GROUP BY id_handling_type {$columnGroup}
            ) AS work_orders", $columnJoin)
            ->join('ref_handling_types', 'ref_handling_types.id = work_orders.id_handling_type')
            ->where([
                'ref_component_prices.id_branch' => $booking['id_branch'],
                'ref_component_prices.id_customer' => $booking['id_customer'],
                'ref_component_prices.price_type' => 'HANDLING',
                'ref_component_prices.price_subtype' => 'GOODS',
            ]);

        if ($isMultiValue) {
            // if max_weight (max_value) is null then we assume that maximum number is infinity,
            // like >5 then by default we do not to limit by maximum number, but >0 we need to limit maximum if number is not null which is 5
            if (!empty($multiTypeColumn)) {
                $prices->where("{$multiTypeColumn} > min_weight")
                    ->where("IF((max_weight IS NULL OR max_weight = 0), TRUE, ({$multiTypeColumn} <= max_weight))");
            }
        }

        $prices->group_start();
        foreach ($conditionRules as $conditionRule) {
            if (preg_match('/^NOT-/', $conditionRule)) {
                $condNegationExtract = preg_replace('/^NOT-/', '', $conditionRule);
                $prices->not_like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$condNegationExtract},");
            } else {
                $prices->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$conditionRule},");
            }
        }
        $prices->group_end();

        $workOrderPrices = $prices->get()->result_array();

        $workOrderGoods = [];
        foreach ($workOrderPrices as $workOrderPrice) {
            // ignore rule by selecting either volume or tonnage
            if (!preg_match("/{$this->ignoreGoodsRule}/", $workOrderPrice['rule'])) {
                $multiplier = $this->buildMultiplierValue($workOrderPrice);
                $label = $this->buildStorageLabel($workOrderPrice);
                $rule = $this->buildRuleLabel($workOrderPrice);
                $quantity = $this->buildQuantityLabel($workOrderPrice);
                $itemSummary = $this->buildItemSummaryLabel($workOrderPrice);

                if ($workOrderPrice['id_handling_type'] == $handlingTypeInbound) {
                    $workOrderPrice['unit_multiplier'] = 1; // we could set 2
                    $workOrderPrice['handling_type'] = 'RECEIVING';
                } else {
                    $workOrderPrice['unit_multiplier'] = 1;
                }

                $workOrderGoods[] = [
                    'item_name' => $workOrderPrice['handling_type'] . $label,
                    'unit' => $workOrderPrice['price_subtype'] . $rule,
                    'type' => $workOrderPrice['price_type'],
                    'quantity' => $quantity,
                    'unit_price' => $workOrderPrice['price'],
                    'unit_multiplier' => $workOrderPrice['unit_multiplier'] * $multiplier,
                    'total' => $workOrderPrice['price'] * $quantity * $workOrderPrice['unit_multiplier'] * $multiplier,
                    'description' => $workOrderPrice['no_work_orders'],
                    'item_summary' => $itemSummary,
                ];
            }
        }

        return $workOrderGoods;
    }

    /**
     * Get work order component price.
     * @param $booking
     * @param $workOrder
     * @return array
     */
    public function getWorkOrderComponentTypePrice($booking, $workOrder = null)
    {
        $invoicePrice = [];

        foreach (self::PRICE_TYPES['COMPONENT'] as $type) {
            $rules = $this->getMatchedRules('COMPONENT', $type);
            foreach ($rules as $rule) {
                if ($type == 'ACTIVITY') {
                    $componentActivities = $this->queryWorkOrderComponentActivity($rule, $booking, $workOrder);
                    if (!empty($componentActivities)) {
                        $invoicePrice = array_merge($invoicePrice, $componentActivities);
                    }
                } elseif ($type == 'CONTAINER') {
                    $componentContainers = $this->queryWorkOrderComponentContainer($rule, $booking, $workOrder);
                    if (!empty($componentContainers)) {
                        $invoicePrice = array_merge($invoicePrice, $componentContainers);
                    }
                } elseif ($type == 'GOODS') {
                    $componentGoods = $this->queryWorkOrderComponentGoods($rule, $booking, $workOrder);
                    if (!empty($componentGoods)) {
                        $invoicePrice = array_merge($invoicePrice, $componentGoods);
                    }
                }
            }
        }

        return $invoicePrice;
    }

    /**
     * Query work order component by activity combination rules.
     * @param $conditionRules
     * @param $booking
     * @param null $workOrder
     * @return array
     */
    private function queryWorkOrderComponentActivity($conditionRules, $booking, $workOrder = null)
    {
        $queryStockContainers = $this->getBaseStockContainers($booking, true);
        $queryStockGoods = $this->getBaseStockGoods($booking, true);

        $containerIds = '-1';
        $goodsIds = '-1';
        if (!empty($queryStockContainers)) {
            $containerIds = implode(',', array_unique(array_column($queryStockContainers, 'id_container')));
        }
        if (!empty($queryStockGoods)) {
            $goodsIds = implode(',', array_unique(array_column($queryStockGoods, 'id_goods')));
        }

        $workOrderCondition = '';
        if (!empty($workOrder)) {
            $workOrderCondition = "AND work_orders.id = {$workOrder['id']}";
        }

        $prices = $this->getBaseInvoicePrice()->select([
            'ref_handling_types.handling_type',
            'ref_components.handling_component',
            'work_orders.no_work_orders',
            'work_orders.total_activity',
            'work_orders.no_containers',
            'work_orders.goods_names',
        ])
            ->join("(
                SELECT 
                    work_orders.id_handling_type,
                    work_order_components.id_component, 
                    COUNT(DISTINCT work_orders.id) AS total_activity, 
                    GROUP_CONCAT(DISTINCT no_work_order) AS no_work_orders,
                    no_containers, goods_names
                FROM (
                    SELECT work_orders.id, no_work_order, id_handling_type FROM work_orders 
                    INNER JOIN handlings ON handlings.id = work_orders.id_handling
                    LEFT JOIN (
                        SELECT id, no_reference FROM invoices 
                        WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                    ) AS invoice_work_orders ON invoice_work_orders.no_reference = work_orders.no_work_order
                    WHERE invoice_work_orders.id IS NULL AND id_booking = '{$booking['id']}' {$workOrderCondition}
                ) AS work_orders
                LEFT JOIN (
                    SELECT id_handling_type, 
                    GROUP_CONCAT(DISTINCT CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers
                    FROM (
                        SELECT work_orders.id, no_work_order, id_handling_type FROM work_orders 
                        INNER JOIN handlings ON handlings.id = work_orders.id_handling
                        LEFT JOIN (
                            SELECT id, no_reference FROM invoices 
                            WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                        ) AS invoice_work_orders ON invoice_work_orders.no_reference = work_orders.no_work_order
                        WHERE invoice_work_orders.id IS NULL AND id_booking = '{$booking['id']}' {$workOrderCondition}
                    ) AS work_orders
                    LEFT JOIN work_order_containers ON work_orders.id = work_order_containers.id_work_order 
                    LEFT JOIN ref_containers ON ref_containers.id = work_order_containers.id_container
                    WHERE id_container IN({$containerIds})
                    GROUP BY id_handling_type
                ) AS work_order_containers ON work_order_containers.id_handling_type = work_orders.id_handling_type
                LEFT JOIN (
                    SELECT id_handling_type, 
                    GROUP_CONCAT(DISTINCT CONCAT(ref_goods.name, ' (', work_order_goods.quantity, unit, '-', ref_goods.unit_weight * work_order_goods.quantity, 'Kg-', ref_goods.unit_volume * work_order_goods.quantity, 'M3-', work_order_goods.status_danger, ')') SEPARATOR ',') AS goods_names
                    FROM (
                        SELECT work_orders.id, no_work_order, id_handling_type FROM work_orders 
                        INNER JOIN handlings ON handlings.id = work_orders.id_handling
                        LEFT JOIN (
                            SELECT id, no_reference FROM invoices 
                            WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                        ) AS invoice_work_orders ON invoice_work_orders.no_reference = work_orders.no_work_order
                        WHERE invoice_work_orders.id IS NULL AND id_booking = '{$booking['id']}' {$workOrderCondition}
                    ) AS work_orders
                    LEFT JOIN work_order_goods ON work_orders.id = work_order_goods.id_work_order 
                    LEFT JOIN ref_goods ON ref_goods.id = work_order_goods.id_goods
                    LEFT JOIN ref_units ON ref_units.id = work_order_goods.id_unit
                    WHERE id_goods IN({$goodsIds})
                    GROUP BY id_handling_type
                ) AS work_order_goods ON work_order_goods.id_handling_type = work_orders.id_handling_type
                INNER JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                GROUP BY work_orders.id_handling_type, work_order_components.id_component
            ) AS work_orders",
                'work_orders.id_handling_type = ref_component_prices.id_handling_type 
                    AND work_orders.id_component = ref_component_prices.id_component')
            ->join('ref_handling_types', 'ref_handling_types.id = work_orders.id_handling_type')
            ->join('ref_components', 'ref_components.id = work_orders.id_component')
            ->where([
                'ref_component_prices.id_branch' => $booking['id_branch'],
                'ref_component_prices.id_customer' => $booking['id_customer'],
                'ref_component_prices.price_type' => 'COMPONENT',
                'ref_component_prices.price_subtype' => 'ACTIVITY',
            ]);

        if (!empty($conditionRules)) {
            $prices->group_start();
            foreach ($conditionRules as $conditionRule) {
                if (preg_match('/^NOT-/', $conditionRule)) {
                    $condNegationExtract = preg_replace('/^NOT-/', '', $conditionRule);
                    $prices->not_like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$condNegationExtract},");
                } else {
                    $prices->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$conditionRule},");
                }
            }
            $prices->group_end();
        }

        $componentPrices = $prices->get()->result_array();

        $componentActivities = [];
        foreach ($componentPrices as $componentPrice) {
            $multiplier = $this->buildMultiplierValue($componentPrice);
            $description = $this->buildDescriptionLabel($componentPrice);
            $rule = $this->buildRuleLabel($componentPrice);
            $itemSummary = $this->buildItemSummaryLabel($componentPrice);

            $componentActivities[] = [
                'item_name' => $componentPrice['handling_component'] . ' (' . $componentPrice['handling_type'] . ')',
                'unit' => $componentPrice['price_subtype'] . $rule,
                'type' => $componentPrice['price_type'],
                'quantity' => $componentPrice['total_activity'],
                'unit_price' => $componentPrice['price'],
                'unit_multiplier' => $multiplier,
                'total' => $multiplier * $componentPrice['price'] * $componentPrice['total_activity'],
                'description' => $componentPrice['no_work_orders'] . if_empty($description, '', ' '),
                'item_summary' => $itemSummary,
            ];
        }

        return $componentActivities;
    }

    /**
     * Query work order component by container combination rules.
     * @param $conditionRules
     * @param $booking
     * @param null $workOrder
     * @return array
     */
    private function queryWorkOrderComponentContainer($conditionRules, $booking, $workOrder = null)
    {
        $containerIds = '-1';
        $queryStockContainers = $this->getBaseStockContainers($booking, true);
        if (!empty($queryStockContainers)) {
            $containerIds = implode(',', array_unique(array_column($queryStockContainers, 'id_container')));
        }

        $workOrderCondition = '';
        if (!empty($workOrder)) {
            $workOrderCondition = "AND work_orders.id = {$workOrder['id']}";
        }

        $ruleMappingData = [
            'PER_CONTAINER' => '',
            'PER_DAY' => '',
            'PER_SIZE' => 'size', // this column name different because data from master, not stock_containers
            'PER_TYPE' => 'type', // this is too
            'PER_DANGER' => 'status_danger',
            'PER_EMPTY' => 'is_empty',
            'PER_CONDITION' => 'status'
        ];

        $columns = [];
        $columnJoin = 'work_orders.id_handling_type = ref_component_prices.id_handling_type AND work_orders.id_component = ref_component_prices.id_component';
        foreach ($conditionRules as $conditionRule) {
            if (!preg_match('/^NOT-/', $conditionRule)) {
                if (key_exists($conditionRule, $ruleMappingData)) {
                    if (!empty($ruleMappingData[$conditionRule])) {
                        $columns[] = $ruleMappingData[$conditionRule];
                        $columnJoin .= ' AND work_orders.' . $ruleMappingData[$conditionRule] . '=ref_component_prices.' . self::RULE_MAPPING_PRICE[$conditionRule];
                    }
                }
            }
        }
        $columnGroup = if_empty(implode(',', $columns), '', ',');

        $prices = $this->getBaseInvoicePrice()->select([
            'ref_handling_types.handling_type',
            'ref_components.handling_component',
            'work_orders.quantity_component',
            'work_orders.total_component',
            'work_orders.no_work_orders',
            'work_orders.total_activity',
            'work_orders.no_containers',
            'work_orders.total_container',
        ]);

        foreach ($columns as $column) {
            $prices->select('work_orders.' . $column);
        }

        $prices = $prices->join("(
                SELECT 
                    work_orders.id_handling_type, 
                    work_order_components.id_component, 
                    work_order_components.quantity AS quantity_component,
                    SUM(work_order_components.quantity) AS total_component,
                    COUNT(DISTINCT work_orders.id) AS total_activity, 
                    GROUP_CONCAT(DISTINCT no_work_order) AS no_work_orders,
                    no_containers,
                    total_container
                    {$columnGroup}
                FROM (
                    SELECT work_orders.id, no_work_order, id_handling_type FROM work_orders 
                    INNER JOIN handlings ON handlings.id = work_orders.id_handling
                    LEFT JOIN (
                        SELECT id, no_reference FROM invoices 
                        WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                    ) AS invoice_work_orders ON invoice_work_orders.no_reference = work_orders.no_work_order
                    WHERE invoice_work_orders.id IS NULL AND id_booking = '{$booking['id']}' {$workOrderCondition}
                ) AS work_orders
                INNER JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                LEFT JOIN (
                    SELECT id_handling_type, work_order_components.id_component, work_order_components.quantity,
                    COUNT(work_order_containers.id) AS total_container,
                    GROUP_CONCAT(DISTINCT CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers
                    {$columnGroup}
                    FROM (
                        SELECT work_orders.id, no_work_order, id_handling_type FROM work_orders 
                        INNER JOIN handlings ON handlings.id = work_orders.id_handling
                        LEFT JOIN (
                            SELECT id, no_reference FROM invoices 
                            WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                        ) AS invoice_work_orders ON invoice_work_orders.no_reference = work_orders.no_work_order
                        WHERE invoice_work_orders.id IS NULL AND id_booking = '{$booking['id']}' {$workOrderCondition}
                    ) AS work_orders
                    LEFT JOIN work_order_containers ON work_orders.id = work_order_containers.id_work_order 
                    LEFT JOIN ref_containers ON ref_containers.id = work_order_containers.id_container
                    INNER JOIN work_order_components ON work_orders.id = work_order_components.id_work_order
                    WHERE id_container IN({$containerIds})
                    GROUP BY id_handling_type, work_order_components.id_component, work_order_components.quantity {$columnGroup}
                ) AS work_order_containers ON work_order_containers.id_handling_type = work_orders.id_handling_type
                    AND work_order_containers.id_component = work_order_components.id_component
                        AND work_order_containers.quantity = work_order_components.quantity
                GROUP BY work_orders.id_handling_type, work_order_components.id_component, work_order_components.quantity, no_containers, total_container 
                {$columnGroup}
            ) AS work_orders", $columnJoin)
            ->join('ref_handling_types', 'ref_handling_types.id = work_orders.id_handling_type')
            ->join('ref_components', 'ref_components.id = work_orders.id_component')
            ->where([
                'ref_component_prices.id_branch' => $booking['id_branch'],
                'ref_component_prices.id_customer' => $booking['id_customer'],
                'ref_component_prices.price_type' => 'COMPONENT',
                'ref_component_prices.price_subtype' => 'CONTAINER',
            ]);

        $prices->group_start();
        foreach ($conditionRules as $conditionRule) {
            if (preg_match('/^NOT-/', $conditionRule)) {
                $condNegationExtract = preg_replace('/^NOT-/', '', $conditionRule);
                $prices->not_like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$condNegationExtract},");
            } else {
                $prices->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$conditionRule},");
            }
        }
        $prices->group_end();

        $componentPrices = $prices->get()->result_array();

        $componentContainer = [];
        foreach ($componentPrices as $componentPrice) {
            $multiplier = $this->buildMultiplierValue($componentPrice);
            $rule = $this->buildRuleLabel($componentPrice);
            $description = $this->buildDescriptionLabel($componentPrice);
            $itemSummary = $this->buildItemSummaryLabel($componentPrice);

            $componentContainer[] = [
                'item_name' => $componentPrice['handling_component'] . ' QTY: ' . numerical($componentPrice['total_component'], 3, true) . ' (' . $componentPrice['handling_type'] . ')',
                'unit' => $componentPrice['price_subtype'] . $rule,
                'type' => $componentPrice['price_type'],
                'quantity' => $componentPrice['total_container'],
                'unit_price' => $componentPrice['price'],
                'unit_multiplier' => $componentPrice['quantity_component'] * $multiplier,
                'total' => $componentPrice['price'] * $componentPrice['quantity_component'] * $componentPrice['total_container'] * $multiplier,
                'description' => $componentPrice['no_work_orders'] . if_empty($description, '', ' '),
                'item_summary' => $itemSummary,
            ];
        }

        return $componentContainer;
    }

    /**
     * Query work order component by goods combination rules.
     * @param $conditionRules
     * @param $booking
     * @param null $workOrder
     * @return array
     */
    private function queryWorkOrderComponentGoods($conditionRules, $booking, $workOrder = null)
    {
        $goodsIds = '-1';
        $queryStockGoods = $this->getBaseStockGoods($booking, true);
        if (!empty($queryStockGoods)) {
            $goodsIds = implode(',', array_unique(array_column($queryStockGoods, 'id_goods')));
        }

        $workOrderCondition = '';
        if (!empty($workOrder)) {
            $workOrderCondition = "AND handlings.id = {$workOrder['id']}";
        }

        $isMultiValue = false;
        $multiTypeColumn = '';

        $columns = [];
        $columnJoin = 'work_orders.id_handling_type = ref_component_prices.id_handling_type AND work_orders.id_component = ref_component_prices.id_component';
        foreach ($conditionRules as $conditionRule) {
            if (!preg_match('/^NOT-/', $conditionRule)) {
                if (key_exists($conditionRule, self::RULE_MAPPING_DATA)) {
                    if (!empty(self::RULE_MAPPING_DATA[$conditionRule])) {
                        $columns[] = self::RULE_MAPPING_DATA[$conditionRule];
                        $columnJoin .= ' AND work_orders.' . self::RULE_MAPPING_DATA[$conditionRule] . '=ref_component_prices.' . self::RULE_MAPPING_PRICE[$conditionRule];
                    }

                    if ($conditionRule == 'PER_TONNAGE') {
                        $isMultiValue = true;
                        $multiTypeColumn = 'work_orders.total_goods_tonnage';
                    }
                    if ($conditionRule == 'PER_VOLUME') {
                        $isMultiValue = true;
                        $multiTypeColumn = 'work_orders.total_goods_volume';
                    }
                }
            }
        }
        $columnGroup = if_empty(implode(',', $columns), '', ',');

        $prices = $this->getBaseInvoicePrice($isMultiValue)->select([
            'ref_handling_types.handling_type',
            'ref_components.handling_component',
            'work_orders.quantity_component',
            'work_orders.total_component',
            'work_orders.no_work_orders',
            'work_orders.total_activity',
            'work_orders.goods_names',
            'work_orders.total_goods',
            'work_orders.total_goods_quantity',
            'work_orders.total_goods_volume',
            'work_orders.total_goods_tonnage',
        ]);

        foreach ($columns as $column) {
            $prices->select('handlings.' . $column);
        }

        $prices = $prices->join("(
                SELECT 
                    work_orders.id_handling_type, 
                    work_order_components.id_component, 
                    work_order_components.quantity AS quantity_component,
                    SUM(work_order_components.quantity) AS total_component,
                    COUNT(DISTINCT work_orders.id) AS total_activity, 
                    GROUP_CONCAT(DISTINCT no_work_order) AS no_work_orders,
                    goods_names,
                    total_goods, 
                    total_goods_quantity,
                    total_goods_volume,
                    total_goods_tonnage
                    {$columnGroup}
                FROM (
                    SELECT work_orders.id, no_work_order, id_handling_type FROM work_orders 
                    INNER JOIN handlings ON handlings.id = work_orders.id_handling
                    LEFT JOIN (
                        SELECT id, no_reference FROM invoices 
                        WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                    ) AS invoice_work_orders ON invoice_work_orders.no_reference = work_orders.no_work_order
                    WHERE invoice_work_orders.id IS NULL AND id_booking = '{$booking['id']}' {$workOrderCondition}
                ) AS work_orders
                INNER JOIN (SELECT id_work_order, id_component, quantity FROM work_order_components) AS work_order_components 
                    ON work_orders.id = work_order_components.id_work_order
                INNER JOIN (
                    SELECT id_handling_type, work_order_components.id_component, work_order_components.quantity AS quantity_component,
                    GROUP_CONCAT(CONCAT(name, ' (', work_order_goods.quantity, unit, '-', ref_goods.unit_weight * work_order_goods.quantity, 'Kg-', ref_goods.unit_volume * work_order_goods.quantity, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names,
                    COUNT(work_order_goods.id) AS total_goods,
                    SUM(work_order_goods.quantity) AS total_goods_quantity,
                    IF(SUM(ref_goods.unit_volume * work_order_goods.quantity) < {$this->roundItemValue}, {$this->roundItemValue}, SUM(ref_goods.unit_volume * work_order_goods.quantity)) AS total_goods_volume,
                    IF(SUM(ref_goods.unit_weight * work_order_goods.quantity / 1000) < {$this->roundItemValue}, {$this->roundItemValue}, SUM(ref_goods.unit_weight * work_order_goods.quantity / 1000)) AS total_goods_tonnage
                    {$columnGroup}
                    FROM (
                        SELECT work_orders.id, no_work_order, id_handling_type FROM work_orders 
                        INNER JOIN handlings ON handlings.id = work_orders.id_handling
                        LEFT JOIN (
                            SELECT id, no_reference FROM invoices 
                            WHERE is_deleted = 0 AND is_void = 0 AND STATUS = 'PUBLISHED'
                        ) AS invoice_work_orders ON invoice_work_orders.no_reference = work_orders.no_work_order
                        WHERE invoice_work_orders.id IS NULL AND id_booking = '{$booking['id']}' {$workOrderCondition}
                    ) AS work_orders
                    LEFT JOIN work_order_goods ON work_orders.id = work_order_goods.id_work_order
                    LEFT JOIN ref_goods ON ref_goods.id = work_order_goods.id_goods
                    LEFT JOIN ref_units ON ref_units.id = work_order_goods.id_unit
                    INNER JOIN (SELECT id_work_order, id_component, quantity FROM work_order_components) AS work_order_components 
                        ON work_orders.id = work_order_components.id_work_order
                    WHERE id_goods IN({$goodsIds})
                    GROUP BY id_handling_type, work_order_components.id_component, work_order_components.quantity {$columnGroup}
                ) AS work_order_goods ON work_order_goods.id_handling_type = work_orders.id_handling_type
                    AND work_order_goods.id_component = work_order_components.id_component
                        AND work_order_goods.quantity_component = work_order_components.quantity
                GROUP BY work_orders.id_handling_type, work_order_components.id_component, work_order_components.quantity, 
                    goods_names, total_goods, total_goods_quantity, total_goods_volume, total_goods_tonnage 
                    {$columnGroup}
            ) AS work_orders", $columnJoin)
            ->join('ref_handling_types', 'ref_handling_types.id = work_orders.id_handling_type')
            ->join('ref_components', 'ref_components.id = work_orders.id_component')
            ->where([
                'ref_component_prices.id_branch' => $booking['id_branch'],
                'ref_component_prices.id_customer' => $booking['id_customer'],
                'ref_component_prices.price_type' => 'COMPONENT',
                'ref_component_prices.price_subtype' => 'GOODS',
            ]);

        if ($isMultiValue) {
            // if max_weight (max_value) is null then we assume that maximum number is infinity,
            // like >5 then by default we do not to limit by maximum number, but >0 we need to limit maximum if number is not null which is 5
            if (!empty($multiTypeColumn)) {
                $prices->where("{$multiTypeColumn} > min_weight")
                    ->where("IF((max_weight IS NULL OR max_weight = 0), TRUE, ({$multiTypeColumn} <= max_weight))");
            }
        }

        $prices->group_start();
        foreach ($conditionRules as $conditionRule) {
            if (preg_match('/^NOT-/', $conditionRule)) {
                $condNegationExtract = preg_replace('/^NOT-/', '', $conditionRule);
                $prices->not_like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$condNegationExtract},");
            } else {
                $prices->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ",{$conditionRule},");
            }
        }
        $prices->group_end();

        $componentPrices = $prices->get()->result_array();

        $componentGoods = [];
        foreach ($componentPrices as $componentPrice) {
            // ignore rule by selecting either volume or tonnage
            if (!preg_match("/{$this->ignoreGoodsRule}/", $componentPrice['rule'])) {
                $rule = $this->buildRuleLabel($componentPrice);
                $quantity = $this->buildQuantityLabel($componentPrice);
                $multiplier = $this->buildMultiplierValue($componentPrice);
                $description = $this->buildDescriptionLabel($componentPrice);
                $itemSummary = $this->buildItemSummaryLabel($componentPrice);

                $componentGoods[] = [
                    'item_name' => $componentPrice['handling_component'] . ' QTY: ' . numerical($componentPrice['total_component'], 3, true) . ' (' . $componentPrice['handling_type'] . ')',
                    'unit' => $componentPrice['price_subtype'] . $rule,
                    'type' => $componentPrice['price_type'],
                    'quantity' => $quantity,
                    'unit_price' => $componentPrice['price'],
                    'unit_multiplier' => $componentPrice['quantity_component'] * $multiplier,
                    'total' => $componentPrice['price'] * $componentPrice['quantity_component'] * $quantity * $multiplier,
                    'description' => $componentPrice['no_work_orders'] . if_empty($description, '', ' '),
                    'item_summary' => $itemSummary,
                ];
            }
        }

        return $componentGoods;
    }

    /**
     * Get invoice level price.
     * @param $booking
     * @return array
     */
    public function getInvoiceTypePrice($booking)
    {
        $invoicePrices = [];

        // customer, branch level price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery->where([
            'ref_component_prices.id_branch' => $booking['id_branch'],
            'ref_component_prices.id_customer' => $booking['id_customer'],
            'ref_component_prices.id_handling_type' => NULL,
            'ref_component_prices.id_component' => NULL,
            'ref_component_prices.price_type' => 'INVOICE',
            'ref_component_prices.price_subtype' => 'ACTIVITY',
            'ref_component_prices.rule' => 'PER_ACTIVITY',
        ]);
        $invoices = $prices->get()->result_array();

        $activityPrices = [];
        foreach ($invoices as $price) {
            $activityPrices[] = [
                'item_name' => $price['description'],
                'unit' => $price['price_type'],
                'type' => $price['price_type'],
                'quantity' => 1,
                'unit_price' => $price['price'],
                'unit_multiplier' => 1,
                'total' => $price['price'],
                'description' => '',
                'item_summary' => '',
            ];
        }
        $invoicePrices = array_merge($invoicePrices, $activityPrices);

        return $invoicePrices;
    }

    /**
     * Get booking expense from payment table.
     * @param $booking
     * @param string $position
     * @return array
     */
    public function getPaymentBilling($booking, $position = PaymentModel::CHARGE_BEFORE_TAX)
    {
        $payments = $this->db->from('payments')
            ->join('uploads', 'uploads.id = payments.id_upload', 'left')
            ->join('bookings AS upload_bookings', 'upload_bookings.id_upload = uploads.id', 'left')
            ->join("(
                SELECT invoice_details.* FROM invoices 
                INNER JOIN invoice_details ON invoices.id = invoice_details.id_invoice
                WHERE invoices.is_deleted = 0 AND invoices.is_void = 0 AND invoices.status = 'PUBLISHED'
                ) AS invoice_details", 'payments.no_payment = invoice_details.description', 'left')
            ->where([
                'invoice_details.id' => NULL,
                'IFNULL(payments.id_booking, upload_bookings.id) = ' . $booking['id'] => null,
                'payments.status' => 'APPROVED',
                'payments.payment_category' => PaymentModel::PAYMENT_BILLING,
                'payments.charge_position' => $position,
                'payments.is_deleted' => false,
                'payments.is_realized' => true,
            ])->get()->result_array();

        $invoicePayments = [];
        foreach ($payments as $payment) {
            $multiplier = $payment['payment_type'] == 'DISCOUNT' ? -1 : 1;
            $invoicePayments[] = [
                'item_name' => $payment['payment_type'],
                'unit' => 'ACTIVITY',
                'type' => 'PAYMENT',
                'quantity' => 1,
                'unit_price' => $payment['amount'],
                'unit_multiplier' => $multiplier,
                'total' => $payment['amount'] * $multiplier,
                'description' => $payment['no_payment'],
                'item_summary' => ''
            ];
        }

        return $invoicePayments;
    }

    /**
     * Add tax and stamp value.
     * @param $invoices
     */
    public function addTaxAndStamp(&$invoices)
    {
        $totalPrice = 0;
        foreach ($invoices as $price) {
            $totalPrice += $price['total'];
        }
        $taxValue = floor(0.1 * $totalPrice);

        $invoices[] = [
            'item_name' => 'PPN (10%)',
            'unit' => 'OTHER',
            'type' => 'OTHER',
            'quantity' => 1,
            'unit_price' => $taxValue,
            'unit_multiplier' => 1,
            'total' => $taxValue,
            'description' => '',
            'item_summary' => '',
        ];

        $totalPrice += $taxValue;

        $stamp = 0;
        if ($totalPrice > 5000000) {
            $stamp = 10000;
        }/* else if ($totalPrice > 1000000) {
            $stamp = 6000;
        } else if ($totalPrice >= 250000) {
            $stamp = 3000;
        }*/

        if ($stamp > 0) {
            $invoices[] = [
                'item_name' => 'Materai',
                'unit' => 'OTHER',
                'type' => 'OTHER',
                'quantity' => 1,
                'unit_price' => $stamp,
                'unit_multiplier' => 1,
                'total' => $stamp,
                'description' => '',
                'item_summary' => '',
            ];
        }
    }

    /**
     * Flooring invoice unit price.
     * @param $invoices
     */
    public function roundFloorCurrency(&$invoices)
    {
        foreach ($invoices as &$invoice) {
            $invoice['total'] = round($invoice['total'], 0, PHP_ROUND_HALF_DOWN);
        }
    }

    /**
     * Replace admin fee price if < 5 ton or m<sup>3</sup>
     * @param $invoices
     */
    public function replaceAdminFeeCharge(&$invoices)
    {
        // replace admin fee to half when volume or tonnage bellow
        $belowMinimumValue = true;
        foreach ($invoices as $invoice) {
            $hasPerTonnage = preg_match("/tonnage/", $invoice['unit']);
            $hasPerVolume = preg_match("/volume/", $invoice['unit']);
            if ($hasPerTonnage || $hasPerVolume) {
                if ($invoice['quantity'] >= 5) {
                    $belowMinimumValue = false;
                    break;
                }
            }
        }

        if ($belowMinimumValue) {
            foreach ($invoices as &$invoice) {
                $guessed1 = preg_match("/ADMINISTRASI FEE/", trim($invoice['item_name']));
                $guessed2 = preg_match("/ADMIN FEE/", trim($invoice['item_name']));
                if ($guessed1 || $guessed2) {
                    $invoice['unit_price'] = $invoice['unit_price'] * 0.5;
                    $invoice['total'] = $invoice['unit_price'] * $invoice['quantity'] * $invoice['unit_multiplier'];
                }
            }
        }
    }

    /**
     * Get invoice data booking price.
     * @param $booking
     * @param bool $customerSetting
     * @param bool $isExtension
     * @param bool $isDepo
     * @return array
     */
    public function getInvoiceBooking($booking, $customerSetting = false, $isExtension = false, $isDepo = false)
    {
        // use general customer setting price
        if (!$customerSetting) {
            $booking['id_customer'] = NULL;
        }

        // special condition we need to determine which one to be selected, volume or weight rule
        // because we charge only the largest value.
        $stockGoods = $this->getBaseStockGoods($booking, true);
        $totalVolume = 0;
        $totalTonnage = 0;
        foreach ($stockGoods as $stockGood) {
            $totalVolume += $stockGood['volume'];
            $totalTonnage += ($stockGood['tonnage'] / 1000);
        }
        if ($totalVolume > $totalTonnage) {
            $this->ignoreGoodsRule = 'PER_TONNAGE';
        } else {
            $this->ignoreGoodsRule = 'PER_VOLUME';
        }

        $storage = $this->getStorageTypePrice($booking, $isExtension, $isDepo);
        $handling = $this->getHandlingTypePrice($booking, null, $isDepo);
        $component = $this->getHandlingComponentTypePrice($booking, null, $isDepo);

        // include booking on when booking depo
        if ($isDepo) {
            $bookingOuts = $this->booking->getBookingOutByBookingIn($booking['id']);
            foreach ($bookingOuts as $bookingOut) {
                $handlingOut = $this->getHandlingTypePrice($bookingOut, null, $isDepo);
                $handling = array_merge($handling, $handlingOut);

                $componentOut = $this->getHandlingComponentTypePrice($bookingOut, null, $isDepo);
                $component = array_merge($component, $componentOut);
            }
        }

        $expense = $this->getPaymentBilling($booking, PaymentModel::CHARGE_BEFORE_TAX);
        $expenseAfterTax = $this->getPaymentBilling($booking, PaymentModel::CHARGE_AFTER_TAX);
        $invoice = $this->getInvoiceTypePrice($booking);

        if ($isExtension) {
            $invoices = array_merge($storage, $expense, $invoice);
        } else {
            $invoices = array_merge($storage, $handling, $component, $expense, $invoice);
        }

        // FORCE REMOVE HANDLING 'PENCACAHAN SEBAGIAN', 'PENCACAHAN FULL' and its components
        $index = 0;
        foreach ($invoices as $invoice) {
            if ($invoice['type'] == 'HANDLING' || $invoice['type'] == 'COMPONENT') {
                // handling
                if (strcasecmp($invoice['item_name'], 'PENCACAHAN FCL BEHANDLE') == 0 || strcasecmp($invoice['item_name'], 'PENCACAHAN FCL PRIORITAS') == 0) {
                    unset($invoices[$index]);
                }
                // handling component
                if (strcasecmp($invoice['description'], 'PENCACAHAN FCL BEHANDLE') == 0 || strcasecmp($invoice['description'], 'PENCACAHAN FCL PRIORITAS') == 0) {
                    unset($invoices[$index]);
                }
            }
            $index++;
        }
        $invoices = array_values($invoices);

        $this->replaceAdminFeeCharge($invoices);
        $this->roundFloorCurrency($invoices);
        $this->addTaxAndStamp($invoices);

        if (!empty($invoices)) {
            $invoices = array_merge($invoices, $expenseAfterTax);
        }

        return $invoices;
    }

    /**
     * Get invoice handling data.
     * @param $booking
     * @param $handling
     * @param bool $customerSetting
     * @return array
     */
    public function getInvoiceHandling($booking, $handling, $customerSetting = false)
    {
        if (!$customerSetting) {
            $booking['id_customer'] = NULL;
            $handling['id_customer'] = NULL;
        }

        // special condition we need to determine which one to be selected, volume or weight rule
        // because we charge only the largest value.
        $handlingGoods = $this->handlingGoods->getHandlingGoodsByHandling($handling['id']);
        $totalVolume = 0;
        $totalTonnage = 0;
        foreach ($handlingGoods as $handlingGood) {
            $totalVolume += $handlingGood['total_volume'];
            $totalTonnage += ($handlingGood['total_weight'] / 1000);
        }
        if ($totalVolume > $totalTonnage) {
            $this->ignoreGoodsRule = 'PER_TONNAGE';
        } else {
            $this->ignoreGoodsRule = 'PER_VOLUME';
        }

        $items = $this->getHandlingTypePrice($booking, $handling);
        $component = $this->getHandlingComponentTypePrice($booking, $handling);

        $invoices = array_merge($items, $component);

        $this->roundFloorCurrency($invoices);
        $this->addTaxAndStamp($invoices);

        return $invoices;
    }

    /**
     * Get invoice work order data.
     * @param $booking
     * @param $workOrder
     * @param bool $customerSetting
     * @return array
     */
    public function getInvoiceWorkOrder($booking, $workOrder, $customerSetting = false)
    {
        if (!$customerSetting) {
            $booking['id_customer'] = NULL;
            $workOrder['id_customer'] = NULL;
        }

        // special condition we need to determine which one to be selected, volume or weight rule
        // because we charge only the largest value.
        $workOrderGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrder['id']);
        $totalVolume = 0;
        $totalTonnage = 0;
        foreach ($workOrderGoods as $workOrderGood) {
            $totalVolume += $workOrderGood['total_volume'];
            $totalTonnage += ($workOrderGood['total_weight'] / 1000);
        }
        if ($totalVolume > $totalTonnage) {
            $this->ignoreGoodsRule = 'PER_TONNAGE';
        } else {
            $this->ignoreGoodsRule = 'PER_VOLUME';
        }

        $items = $this->getWorkOrderTypePrice($booking, $workOrder);
        $component = $this->getWorkOrderComponentTypePrice($booking, $workOrder);

        $invoices = array_merge($items, $component);

        $this->roundFloorCurrency($invoices);
        $this->addTaxAndStamp($invoices);

        return $invoices;
    }
}
