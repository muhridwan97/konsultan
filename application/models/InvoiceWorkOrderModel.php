<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class InvoiceWorkOrderModel extends CI_Model
{
    /**
     * InvoiceWorkOrderModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get base last invoice price reference.
     * @param bool $isMultiValue
     * @return CI_DB_query_builder
     */
    public function getBaseInvoicePrice($isMultiValue = false)
    {
        if($isMultiValue) {
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
                            AND (ref_component_prices.rule = 'PER_TONNAGE' OR ref_component_prices.rule = 'PER_TONNAGE,PER_DAY'
                                OR ref_component_prices.rule = 'PER_VOLUME' OR ref_component_prices.rule = 'PER_VOLUME,PER_DAY')
                    ) LIMIT 1
                ) AS max_weight
                FROM ref_component_prices
                INNER JOIN (
                    SELECT MAX(id) AS id, MAX(effective_date) AS effective_date
                    FROM ref_component_prices 
                    WHERE is_void = false AND is_deleted = false
                    GROUP BY id_branch, id_customer, id_handling_type, id_component, price_type, price_subtype, rule, 
                            min_weight, goods_unit, container_type, container_size, description
                ) AS last_prices ON ref_component_prices.id = last_prices.id
                WHERE (ref_component_prices.rule = 'PER_TONNAGE' OR ref_component_prices.rule = 'PER_TONNAGE,PER_DAY'
                        OR ref_component_prices.rule = 'PER_VOLUME' OR ref_component_prices.rule = 'PER_VOLUME,PER_DAY')
            ) AS ref_component_prices");
        } else {
            $this->db->select(['MAX(id) AS id', 'MAX(effective_date) AS effective_date'])
                ->from('ref_component_prices')
                ->where(['is_void' => false, 'is_deleted' => false])
                ->group_by('id_branch, id_customer, id_handling_type, id_component, price_type, price_subtype, rule, goods_unit, container_type, container_size, description');
            $basePriceQuery = $this->db->get_compiled_select();
            $prices = $this->db->select('ref_component_prices.*')
                ->from('ref_component_prices')
                ->join("({$basePriceQuery}) AS last_prices", 'ref_component_prices.id = last_prices.id');
        }

        return $prices;
    }

    /**
     * Get work order component price
     * @param $workOrder
     * @return array
     */
    public function getWorkOrderComponentPrice($workOrder)
    {
        $workOrderComponentPrices = [];

        // component per activity
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'ref_handling_types.handling_type',
                'work_order_components.handling_component',
                'work_order_components.quantity AS component_quantity',
                'work_order_components.unit AS component_unit',
                'work_order_components.description AS component_description',
                'work_order_containers.no_containers',
                'work_order_goods.goods_names',
            ])
            ->join("(
                SELECT work_order_components.*, ref_components.handling_component, ref_units.unit
                FROM work_order_components 
                INNER JOIN ref_components ON ref_components.id = work_order_components.id_component
                LEFT JOIN ref_units ON ref_units.id = work_order_components.id_unit
                WHERE id_work_order = {$workOrder['id']}) AS work_order_components",
                'work_order_components.id_component = ref_component_prices.id_component')
            ->join('work_orders', 'work_orders.id = work_order_components.id_work_order', 'left')
            ->join('handlings', 'work_orders.id_handling = handlings.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join("(
                SELECT id_work_order, GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers
                FROM work_order_containers
                INNER JOIN ref_containers ON ref_containers.id = work_order_containers.id_container
                WHERE work_order_containers.id_work_order = {$workOrder['id']} AND id_work_order_container IS NULL
                ) AS work_order_containers", 'work_orders.id = work_order_containers.id_work_order')
            ->join("(
                SELECT id_work_order, GROUP_CONCAT(CONCAT(name, ' (', quantity, unit, '-', tonnage, 'Kg-', volume, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names
                FROM work_order_goods
                INNER JOIN ref_goods ON ref_goods.id = work_order_goods.id_goods
                INNER JOIN ref_units ON ref_units.id = work_order_goods.id_unit
                WHERE work_order_goods.id_work_order = {$workOrder['id']} AND id_work_order_container IS NULL AND id_work_order_goods IS NULL
                ) AS work_order_goods", 'work_orders.id = work_order_goods.id_work_order')
            ->where([
                'ref_component_prices.id_branch' => $workOrder['id_branch'],
                'ref_component_prices.id_customer' => $workOrder['id_customer'],
                'ref_component_prices.id_handling_type' => $workOrder['id_handling_type'],
                'ref_component_prices.price_type' => 'COMPONENT',
                'ref_component_prices.price_subtype' => 'ACTIVITY',
                'ref_component_prices.rule' => 'PER_ACTIVITY'
            ]);
        $components = $prices->get()->result_array();

        $componentPrices = [];
        foreach ($components as $component) {
            $componentPrices[] = [
                'item_name' => $component['handling_component'],
                'unit' => $component['price_subtype'],
                'type' => $component['price_type'],
                'quantity' => $component['component_quantity'],
                'unit_price' => $component['price'],
                'unit_multiplier' => 1,
                'total' => $component['component_quantity'] * $component['price'],
                'description' => $component['handling_type'],
                'item_summary' => $component['no_containers'] . '|' . $component['goods_names'],
            ];
        }
        $workOrderComponentPrices = array_merge($workOrderComponentPrices, $componentPrices);

        // per container price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'work_order_components.handling_component',
                'work_order_components.quantity AS component_quantity',
                'work_order_components.unit AS component_unit',
                'work_order_components.description AS component_description',
                'work_order_components.no_containers',
                'work_order_components.total_container',
            ])
            ->join("(
                SELECT work_order_components.*, ref_components.handling_component, ref_units.unit, 
                    work_order_containers.no_containers, work_order_containers.total_container
                FROM work_order_components 
                INNER JOIN ref_components ON ref_components.id = work_order_components.id_component
                LEFT JOIN ref_units ON ref_units.id = work_order_components.id_unit
                INNER JOIN (
                    SELECT id_work_order, GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers,
                        COUNT(work_order_containers.id) AS total_container 
                    FROM work_order_containers
                    INNER JOIN ref_containers ON ref_containers.id = work_order_containers.id_container
                    WHERE id_work_order_container IS NULL
                    GROUP BY id_work_order
                    ) AS work_order_containers ON work_order_containers.id_work_order = work_order_components.id_work_order
                WHERE work_order_components.id_work_order = {$workOrder['id']}) AS work_order_components",
                'work_order_components.id_component = ref_component_prices.id_component')
            ->where([
                'ref_component_prices.id_branch' => $workOrder['id_branch'],
                'ref_component_prices.id_customer' => $workOrder['id_customer'],
                'ref_component_prices.id_handling_type' => $workOrder['id_handling_type'],
                'ref_component_prices.price_type' => 'COMPONENT',
                'ref_component_prices.price_subtype' => 'CONTAINER',
                'ref_component_prices.rule' => 'PER_CONTAINER'
            ]);
        $components = $prices->get()->result_array();

        $componentContainerPrices = [];
        foreach ($components as $component) {
            $componentContainerPrices[] = [
                'item_name' => $component['handling_component'],
                'unit' => $component['price_subtype'],
                'type' => $component['price_type'],
                'quantity' => $component['component_quantity'],
                'unit_price' => $component['price'],
                'unit_multiplier' => $component['total_container'],
                'total' => $component['component_quantity'] * $component['price'] * $component['total_container'],
                'description' => $workOrder['handling_type'],
                'item_summary' => $component['no_containers'],
            ];
        }
        $workOrderComponentPrices = array_merge($workOrderComponentPrices, $componentContainerPrices);

        // per container, size price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'work_order_components.handling_component',
                'work_order_components.quantity AS component_quantity',
                'work_order_components.unit AS component_unit',
                'work_order_components.description AS component_description',
                'work_order_components.no_containers',
                'work_order_components.size',
                'work_order_components.total_container',
            ])
            ->join("(
                SELECT work_order_components.*, ref_components.handling_component, ref_units.unit, 
                    work_order_containers.no_containers, work_order_containers.size, work_order_containers.total_container
                FROM work_order_components 
                INNER JOIN ref_components ON ref_components.id = work_order_components.id_component
                INNER JOIN ref_units ON ref_units.id = work_order_components.id_unit
                INNER JOIN (
                    SELECT id_work_order, ref_containers.size,
                        GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers,
                        COUNT(work_order_containers.id) AS total_container 
                    FROM work_order_containers
                    INNER JOIN ref_containers ON ref_containers.id = work_order_containers.id_container
                    WHERE id_work_order_container IS NULL
                    GROUP BY id_work_order, ref_containers.size
                    ) AS work_order_containers ON work_order_containers.id_work_order = work_order_components.id_work_order
                WHERE work_order_components.id_work_order = {$workOrder['id']}) AS work_order_components",
                'work_order_components.id_component = ref_component_prices.id_component
                    AND ref_component_prices.container_size = work_order_components.size'
            )
            ->where([
                'ref_component_prices.id_branch' => $workOrder['id_branch'],
                'ref_component_prices.id_customer' => $workOrder['id_customer'],
                'ref_component_prices.id_handling_type' => $workOrder['id_handling_type'],
                'ref_component_prices.price_type' => 'COMPONENT',
                'ref_component_prices.price_subtype' => 'CONTAINER',
            ])
            ->group_start()
            ->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ',PER_CONTAINER,')
            ->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ',PER_SIZE,')
            ->not_like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ',PER_TYPE,')
            ->group_end();
        $components = $prices->get()->result_array();

        $componentContainerSizePrices = [];
        foreach ($components as $component) {
            $componentContainerSizePrices[] = [
                'item_name' => $component['handling_component'],
                'unit' => $component['price_subtype'] . '/size',
                'type' => $component['price_type'],
                'quantity' => $component['component_quantity'],
                'unit_price' => $component['price'],
                'unit_multiplier' => $component['total_container'],
                'total' => $component['component_quantity'] * $component['price'] * $component['total_container'],
                'description' => $workOrder['handling_type'],
                'item_summary' => $component['no_containers'],
            ];
        }
        $workOrderComponentPrices = array_merge($workOrderComponentPrices, $componentContainerSizePrices);

        // per container, type price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'work_order_components.handling_component',
                'work_order_components.quantity AS component_quantity',
                'work_order_components.unit AS component_unit',
                'work_order_components.description AS component_description',
                'work_order_components.no_containers',
                'work_order_components.type',
                'work_order_components.total_container',
            ])
            ->join("(
                SELECT work_order_components.*, ref_components.handling_component, ref_units.unit, 
                    work_order_containers.no_containers, work_order_containers.type, work_order_containers.total_container
                FROM work_order_components 
                INNER JOIN ref_components ON ref_components.id = work_order_components.id_component
                INNER JOIN ref_units ON ref_units.id = work_order_components.id_unit
                INNER JOIN (
                    SELECT id_work_order, ref_containers.type,
                        GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers,
                        COUNT(work_order_containers.id) AS total_container 
                    FROM work_order_containers
                    INNER JOIN ref_containers ON ref_containers.id = work_order_containers.id_container
                    WHERE id_work_order_container IS NULL
                    GROUP BY id_work_order, ref_containers.type
                    ) AS work_order_containers ON work_order_containers.id_work_order = work_order_components.id_work_order
                WHERE work_order_components.id_work_order = {$workOrder['id']}) AS work_order_components",
                'work_order_components.id_component = ref_component_prices.id_component
                    AND ref_component_prices.container_type = work_order_components.type'
            )
            ->where([
                'ref_component_prices.id_branch' => $workOrder['id_branch'],
                'ref_component_prices.id_customer' => $workOrder['id_customer'],
                'ref_component_prices.id_handling_type' => $workOrder['id_handling_type'],
                'ref_component_prices.price_type' => 'COMPONENT',
                'ref_component_prices.price_subtype' => 'CONTAINER',
            ])
            ->group_start()
            ->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ',PER_CONTAINER,')
            ->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ',PER_TYPE,')
            ->not_like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ',PER_SIZE,')
            ->group_end();
        $components = $prices->get()->result_array();

        $componentContainerTypePrices = [];
        foreach ($components as $component) {
            $componentContainerTypePrices[] = [
                'item_name' => $component['handling_component'],
                'unit' => $component['price_subtype'] . '/type',
                'type' => $component['price_type'],
                'quantity' => $component['component_quantity'],
                'unit_price' => $component['price'],
                'unit_multiplier' => $component['total_container'],
                'total' => $component['component_quantity'] * $component['price'] * $component['total_container'],
                'description' => $workOrder['handling_type'],
                'item_summary' => $component['no_containers'],
            ];
        }
        $workOrderComponentPrices = array_merge($workOrderComponentPrices, $componentContainerTypePrices);

        // per container, size, type price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'work_order_components.handling_component',
                'work_order_components.quantity AS component_quantity',
                'work_order_components.unit AS component_unit',
                'work_order_components.description AS component_description',
                'work_order_components.no_containers',
                'work_order_components.type',
                'work_order_components.size',
                'work_order_components.total_container',
            ])
            ->join("(
                SELECT work_order_components.*, ref_components.handling_component, ref_units.unit, 
                    work_order_containers.no_containers, work_order_containers.type, work_order_containers.size, work_order_containers.total_container
                FROM work_order_components 
                INNER JOIN ref_components ON ref_components.id = work_order_components.id_component
                INNER JOIN ref_units ON ref_units.id = work_order_components.id_unit
                INNER JOIN (
                    SELECT id_work_order, ref_containers.type, ref_containers.size,
                        GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers,
                        COUNT(work_order_containers.id) AS total_container 
                    FROM work_order_containers
                    INNER JOIN ref_containers ON ref_containers.id = work_order_containers.id_container
                    WHERE id_work_order_container IS NULL
                    GROUP BY id_work_order, ref_containers.type, ref_containers.size
                    ) AS work_order_containers 
                    ON work_order_containers.id_work_order = work_order_components.id_work_order
                WHERE work_order_components.id_work_order = {$workOrder['id']}) AS work_order_components",
                'work_order_components.id_component = ref_component_prices.id_component
                    AND ref_component_prices.container_type = work_order_components.type
                        AND ref_component_prices.container_size = work_order_components.size'
            )
            ->where([
                'ref_component_prices.id_branch' => $workOrder['id_branch'],
                'ref_component_prices.id_customer' => $workOrder['id_customer'],
                'ref_component_prices.id_handling_type' => $workOrder['id_handling_type'],
                'ref_component_prices.price_type' => 'COMPONENT',
                'ref_component_prices.price_subtype' => 'CONTAINER',
            ])
            ->group_start()
            ->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ',PER_CONTAINER,')
            ->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ',PER_TYPE,')
            ->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ',PER_SIZE,')
            ->group_end();
        $components = $prices->get()->result_array();

        $componentContainerTypeSizePrices = [];
        foreach ($components as $component) {
            $componentContainerTypeSizePrices[] = [
                'item_name' => $component['handling_component'],
                'unit' => $component['component_unit'] . '/type/size',
                'type' => $component['price_type'],
                'quantity' => $component['component_quantity'],
                'unit_price' => $component['price'],
                'unit_multiplier' => $component['total_container'],
                'total' => $component['component_quantity'] * $component['price'] * $component['total_container'],
                'description' => $workOrder['handling_type'],
                'item_summary' => $component['no_containers'],
            ];
        }
        $workOrderComponentPrices = array_merge($workOrderComponentPrices, $componentContainerTypeSizePrices);

        // per goods unit price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'work_order_components.handling_component',
                'work_order_components.quantity AS component_quantity',
                'work_order_components.unit AS component_unit',
                'work_order_components.description AS component_description',
                'work_order_components.goods_names',
                'work_order_components.goods_unit',
                'work_order_components.total_goods_quantity',
            ])
            ->join("(
                SELECT work_order_components.*, ref_components.handling_component, ref_units.unit, 
                    work_order_goods.goods_names, work_order_goods.id_goods_unit, work_order_goods.unit AS goods_unit, work_order_goods.total_goods_quantity
                FROM work_order_components 
                INNER JOIN ref_components ON ref_components.id = work_order_components.id_component
                LEFT JOIN ref_units ON ref_units.id = work_order_components.id_unit
                INNER JOIN (
                    SELECT id_work_order, id_unit AS id_goods_unit, ref_units.unit,
                        GROUP_CONCAT(CONCAT(name, ' (', quantity, unit, '-', tonnage, 'Kg-', volume, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names,
                        SUM(work_order_goods.quantity) AS total_goods_quantity 
                    FROM work_order_goods
                    INNER JOIN ref_goods ON ref_goods.id = work_order_goods.id_goods
                    INNER JOIN ref_units ON ref_units.id = work_order_goods.id_unit
                    WHERE id_work_order_container IS NULL AND id_work_order_goods IS NULL
                    GROUP BY id_work_order, id_unit, ref_units.unit
                    ) AS work_order_goods ON work_order_goods.id_work_order = work_order_components.id_work_order
                WHERE work_order_components.id_work_order = {$workOrder['id']}) AS work_order_components",
                'work_order_components.id_component = ref_component_prices.id_component
                    AND ref_component_prices.goods_unit = work_order_components.id_goods_unit'
            )
            ->where([
                'ref_component_prices.id_branch' => $workOrder['id_branch'],
                'ref_component_prices.id_customer' => $workOrder['id_customer'],
                'ref_component_prices.id_handling_type' => $workOrder['id_handling_type'],
                'ref_component_prices.price_type' => 'COMPONENT',
                'ref_component_prices.price_subtype' => 'GOODS',
                'ref_component_prices.rule' => 'PER_UNIT',
            ]);
        $components = $prices->get()->result_array();

        $componentGoodsUnitPrices = [];
        foreach ($components as $component) {
            $componentGoodsUnitPrices[] = [
                'item_name' => $component['handling_component'],
                'unit' => $component['price_subtype'] . '/unit',
                'type' => $component['price_type'],
                'quantity' => $component['component_quantity'],
                'unit_price' => $component['price'],
                'unit_multiplier' => $component['total_goods_quantity'],
                'total' => $component['component_quantity'] * $component['price'] * $component['total_goods_quantity'],
                'description' => $workOrder['handling_type'],
                'item_summary' => $component['goods_names'],
            ];
        }
        $workOrderComponentPrices = array_merge($workOrderComponentPrices, $componentGoodsUnitPrices);

        // per goods tonnage
        $baseQuery = $this->getBaseInvoicePrice(true);
        $prices = $baseQuery
            ->select([
                'work_order_components.handling_component',
                'work_order_components.quantity AS component_quantity',
                'work_order_components.unit AS component_unit',
                'work_order_components.description AS component_description',
                'work_order_components.goods_names',
                'work_order_components.total_goods_tonnage',
            ])
            ->join("(
                SELECT work_order_components.*, ref_components.handling_component, ref_units.unit, 
                    work_order_goods.goods_names, work_order_goods.total_goods_tonnage
                FROM work_order_components 
                INNER JOIN ref_components ON ref_components.id = work_order_components.id_component
                LEFT JOIN ref_units ON ref_units.id = work_order_components.id_unit
                INNER JOIN (
                    SELECT id_work_order,
                        GROUP_CONCAT(CONCAT(name, ' (', quantity, unit, '-', tonnage, 'Kg-', volume, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names,
                        SUM(work_order_goods.tonnage) AS total_goods_tonnage 
                    FROM work_order_goods
                    INNER JOIN ref_goods ON ref_goods.id = work_order_goods.id_goods
                    INNER JOIN ref_units ON ref_units.id = work_order_goods.id_unit
                    WHERE id_work_order_container IS NULL AND id_work_order_goods IS NULL
                    GROUP BY id_work_order
                    ) AS work_order_goods ON work_order_goods.id_work_order = work_order_components.id_work_order
                WHERE work_order_components.id_work_order = {$workOrder['id']}) AS work_order_components",
                'work_order_components.id_component = ref_component_prices.id_component'
            )
            ->where([
                'ref_component_prices.id_branch' => $workOrder['id_branch'],
                'ref_component_prices.id_customer' => $workOrder['id_customer'],
                'ref_component_prices.id_handling_type' => $workOrder['id_handling_type'],
                'ref_component_prices.price_type' => 'COMPONENT',
                'ref_component_prices.price_subtype' => 'GOODS',
                'ref_component_prices.rule' => 'PER_TONNAGE',
            ])
            ->where("work_order_components.total_goods_tonnage > min_weight")
            ->where("IF((max_weight IS NULL OR max_weight = 0), TRUE, (work_order_components.total_goods_tonnage <= max_weight))");
        $components = $prices->get()->result_array();

        $componentGoodsTonnagePrices = [];
        foreach ($components as $component) {
            $tonnageRound = ($component['total_goods_tonnage'] > 0 && $component['total_goods_tonnage'] < 2000) ? 2000 : $component['total_goods_tonnage'];
            $componentGoodsTonnagePrices[] = [
                'item_name' => $component['handling_component'],
                'unit' => $component['price_subtype'] . '/tonnage (Ton)',
                'type' => $component['price_type'],
                'quantity' => $component['component_quantity'],
                'unit_price' => $component['price'],
                'unit_multiplier' => ($tonnageRound / 1000),
                'total' => $component['component_quantity'] * $component['price'] * ($tonnageRound / 1000),
                'description' => $workOrder['handling_type'],
                'item_summary' => $component['goods_names'],
            ];
        }
        $workOrderComponentPrices = array_merge($workOrderComponentPrices, $componentGoodsTonnagePrices);

        // per goods volume price
        $baseQuery = $this->getBaseInvoicePrice(true);
        $prices = $baseQuery
            ->select([
                'work_order_components.handling_component',
                'work_order_components.quantity AS component_quantity',
                'work_order_components.unit AS component_unit',
                'work_order_components.description AS component_description',
                'work_order_components.goods_names',
                'work_order_components.total_goods_volume',
            ])
            ->join("(
                SELECT work_order_components.*, ref_components.handling_component, ref_units.unit, 
                    work_order_goods.goods_names, work_order_goods.total_goods_volume
                FROM work_order_components 
                INNER JOIN ref_components ON ref_components.id = work_order_components.id_component
                LEFT JOIN ref_units ON ref_units.id = work_order_components.id_unit
                INNER JOIN (
                    SELECT id_work_order,
                        GROUP_CONCAT(CONCAT(name, ' (', quantity, unit, '-', tonnage, 'Kg-', volume, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names,
                        SUM(work_order_goods.volume) AS total_goods_volume
                    FROM work_order_goods
                    INNER JOIN ref_goods ON ref_goods.id = work_order_goods.id_goods
                    INNER JOIN ref_units ON ref_units.id = work_order_goods.id_unit
                    WHERE id_work_order_container IS NULL AND id_work_order_goods IS NULL
                    GROUP BY id_work_order
                    ) AS work_order_goods ON work_order_goods.id_work_order = work_order_components.id_work_order
                WHERE work_order_components.id_work_order = {$workOrder['id']}) AS work_order_components",
                'work_order_components.id_component = ref_component_prices.id_component'
            )
            ->where([
                'ref_component_prices.id_branch' => $workOrder['id_branch'],
                'ref_component_prices.id_customer' => $workOrder['id_customer'],
                'ref_component_prices.id_handling_type' => $workOrder['id_handling_type'],
                'ref_component_prices.price_type' => 'COMPONENT',
                'ref_component_prices.price_subtype' => 'GOODS',
                'ref_component_prices.rule' => 'PER_VOLUME',
            ])
            ->where("work_order_components.total_goods_volume > min_weight")
            ->where("IF((max_weight IS NULL OR max_weight = 0), TRUE, (work_order_components.total_goods_volume <= max_weight))");
        $components = $prices->get()->result_array();

        $componentGoodsVolumePrices = [];
        foreach ($components as $component) {
            $componentGoodsVolumePrices[] = [
                'item_name' => $component['handling_component'],
                'unit' => $component['price_subtype'] . '/volume (M3)',
                'type' => $component['price_type'],
                'quantity' => $component['component_quantity'],
                'unit_price' => $component['price'],
                'unit_multiplier' => $component['total_goods_volume'],
                'total' => $component['component_quantity'] * $component['price'] * $component['total_goods_volume'],
                'description' => $workOrder['handling_type'],
                'item_summary' => $component['goods_names'],
            ];
        }
        $workOrderComponentPrices = array_merge($workOrderComponentPrices, $componentGoodsVolumePrices);

        return $workOrderComponentPrices;
    }

    /**
     * Get work order detail item price.
     * @param $workOrder
     * @return array
     */
    public function getWorkOrderItemPrice($workOrder)
    {
        $workOrderItemPrices = [];

        // per activity
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'ref_handling_types.handling_type',
                'work_order_containers.no_containers',
                'work_order_goods.goods_names',
            ])
            ->join('handlings', 'handlings.id_handling_type = ref_component_prices.id_handling_type')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id', 'left')
            ->join("(
                SELECT id_work_order, GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers
                FROM work_order_containers
                INNER JOIN ref_containers ON ref_containers.id = work_order_containers.id_container
                WHERE work_order_containers.id_work_order = {$workOrder['id']} AND id_work_order_container IS NULL
                ) AS work_order_containers", 'work_orders.id = work_order_containers.id_work_order', 'left')
            ->join("(
                SELECT id_work_order, GROUP_CONCAT(CONCAT(name, ' (', quantity, unit, '-', tonnage, 'Kg-', volume, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names
                FROM work_order_goods
                INNER JOIN ref_goods ON ref_goods.id = work_order_goods.id_goods
                INNER JOIN ref_units ON ref_units.id = work_order_goods.id_unit
                WHERE work_order_goods.id_work_order = {$workOrder['id']} AND id_work_order_container IS NULL AND id_work_order_goods IS NULL
                ) AS work_order_goods", 'work_orders.id = work_order_goods.id_work_order', 'left')
            ->where([
                'work_orders.id' => $workOrder['id'],
                'ref_component_prices.id_branch' => $workOrder['id_branch'],
                'ref_component_prices.id_customer' => $workOrder['id_customer'],
                'ref_component_prices.id_handling_type' => $workOrder['id_handling_type'],
                'ref_component_prices.price_type' => 'HANDLING',
                'ref_component_prices.price_subtype' => 'ACTIVITY',
                'ref_component_prices.rule' => 'PER_ACTIVITY'
            ]);

        $activities = $prices->get()->result_array();

        $handlingActivities = [];
        foreach ($activities as $activity) {
            $handlingActivities[] = [
                'item_name' => $activity['handling_type'],
                'unit' => $activity['price_subtype'],
                'type' => $activity['price_type'],
                'quantity' => 1,
                'unit_price' => $activity['price'],
                'unit_multiplier' => 1,
                'total' => $activity['price'],
                'description' => '',
                'item_summary' => $activity['no_containers'] . '|' . $activity['goods_names'],
            ];
        }
        $workOrderItemPrices = array_merge($workOrderItemPrices, $handlingActivities);

        // per container price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'work_order_containers.no_containers',
                'work_order_containers.total_container',
            ])
            ->join("(
                SELECT id_work_order, 
                    GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers,
                    COUNT(work_order_containers.id) AS total_container 
                FROM work_order_containers
                INNER JOIN ref_containers ON ref_containers.id = work_order_containers.id_container
                WHERE work_order_containers.id_work_order = {$workOrder['id']}
                AND id_work_order_container IS NULL
                ) AS work_order_containers", '1 = 1')
            ->where([
                'ref_component_prices.id_branch' => $workOrder['id_branch'],
                'ref_component_prices.id_customer' => $workOrder['id_customer'],
                'ref_component_prices.id_handling_type' => $workOrder['id_handling_type'],
                'ref_component_prices.price_type' => 'HANDLING',
                'ref_component_prices.price_subtype' => 'CONTAINER',
                'ref_component_prices.rule' => 'PER_CONTAINER',
            ]);
        $container = $prices->get()->row_array();

        if (!empty($container)) {
            $containerPrices[] = [
                'item_name' => $workOrder['handling_type'],
                'unit' => $container['price_subtype'],
                'type' => $container['price_type'],
                'quantity' => $container['total_container'],
                'unit_price' => $container['price'],
                'unit_multiplier' => 1,
                'total' => $container['total_container'] * $container['price'],
                'description' => '',
                'item_summary' => $container['no_containers'],
            ];
            $workOrderItemPrices = array_merge($workOrderItemPrices, $containerPrices);
        }

        // per container, size price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'work_order_containers.no_containers',
                'work_order_containers.size',
                'work_order_containers.total_container',
            ])
            ->join("(
                SELECT id_work_order, ref_containers.size,
                    GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers,
                    COUNT(work_order_containers.id) AS total_container 
                FROM work_order_containers
                INNER JOIN ref_containers ON ref_containers.id = work_order_containers.id_container
                WHERE work_order_containers.id_work_order = {$workOrder['id']}
                    AND id_work_order_container IS NULL
                GROUP BY id_work_order, ref_containers.size
                ) AS work_order_containers",
                'ref_component_prices.container_size = work_order_containers.size')
            ->where([
                'ref_component_prices.id_branch' => $workOrder['id_branch'],
                'ref_component_prices.id_customer' => $workOrder['id_customer'],
                'ref_component_prices.id_handling_type' => $workOrder['id_handling_type'],
                'ref_component_prices.price_type' => 'HANDLING',
                'ref_component_prices.price_subtype' => 'CONTAINER',
            ])
            ->group_start()
            ->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ',PER_CONTAINER,')
            ->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ',PER_SIZE,')
            ->not_like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ',PER_TYPE,')
            ->group_end();
        $containers = $prices->get()->result_array();

        $containerSizePrices = [];
        foreach ($containers as $container) {
            $containerSizePrices[] = [
                'item_name' => $workOrder['handling_type'],
                'unit' => $container['price_subtype'] . '/size',
                'type' => $container['price_type'],
                'quantity' => $container['total_container'],
                'unit_price' => $container['price'],
                'unit_multiplier' => 1,
                'total' => $container['total_container'] * $container['price'],
                'description' => '',
                'item_summary' => $container['no_containers'],
            ];
        }
        $workOrderItemPrices = array_merge($workOrderItemPrices, $containerSizePrices);

        // per container, type price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'work_order_containers.no_containers',
                'work_order_containers.type',
                'work_order_containers.total_container',
            ])
            ->join("(
                SELECT id_work_order, ref_containers.type,
                    GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers,
                    COUNT(work_order_containers.id) AS total_container 
                FROM work_order_containers
                INNER JOIN ref_containers ON ref_containers.id = work_order_containers.id_container
                WHERE work_order_containers.id_work_order = {$workOrder['id']}
                    AND id_work_order_container IS NULL
                GROUP BY id_work_order, ref_containers.type
                ) AS work_order_containers",
                'ref_component_prices.container_type = work_order_containers.type')
            ->where([
                'ref_component_prices.id_branch' => $workOrder['id_branch'],
                'ref_component_prices.id_customer' => $workOrder['id_customer'],
                'ref_component_prices.id_handling_type' => $workOrder['id_handling_type'],
                'ref_component_prices.price_type' => 'HANDLING',
                'ref_component_prices.price_subtype' => 'CONTAINER',
            ])
            ->group_start()
            ->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ',PER_CONTAINER,')
            ->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ',PER_TYPE,')
            ->not_like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ',PER_SIZE,')
            ->group_end();
        $containers = $prices->get()->result_array();

        $containerSizePrices = [];
        foreach ($containers as $container) {
            $containerSizePrices[] = [
                'item_name' => $workOrder['handling_type'],
                'unit' => $container['price_subtype'] . '/type',
                'type' => $container['price_type'],
                'quantity' => $container['total_container'],
                'unit_price' => $container['price'],
                'unit_multiplier' => 1,
                'total' => $container['total_container'] * $container['price'],
                'description' => '',
                'item_summary' => $container['no_containers'],
            ];
        }
        $workOrderItemPrices = array_merge($workOrderItemPrices, $containerSizePrices);

        // per container, type, size price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'work_order_containers.no_containers',
                'work_order_containers.type',
                'work_order_containers.size',
                'work_order_containers.total_container',
            ])
            ->join("(
                SELECT id_work_order, ref_containers.type, ref_containers.size,
                    GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers,
                    COUNT(work_order_containers.id) AS total_container 
                FROM work_order_containers
                INNER JOIN ref_containers ON ref_containers.id = work_order_containers.id_container
                WHERE work_order_containers.id_work_order = {$workOrder['id']}
                    AND id_work_order_container IS NULL
                GROUP BY id_work_order, ref_containers.type, ref_containers.size
                ) AS work_order_containers",
                'ref_component_prices.container_type = work_order_containers.type
                    AND ref_component_prices.container_size = work_order_containers.size')
            ->where([
                'ref_component_prices.id_branch' => $workOrder['id_branch'],
                'ref_component_prices.id_customer' => $workOrder['id_customer'],
                'ref_component_prices.id_handling_type' => $workOrder['id_handling_type'],
                'ref_component_prices.price_type' => 'HANDLING',
                'ref_component_prices.price_subtype' => 'CONTAINER',
            ])
            ->group_start()
            ->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ',PER_CONTAINER,')
            ->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ',PER_TYPE,')
            ->like("CONCAT(',', RTRIM(ref_component_prices.rule), ',')", ',PER_SIZE,')
            ->group_end();
        $containers = $prices->get()->result_array();

        $containerSizePrices = [];
        foreach ($containers as $container) {
            $containerSizePrices[] = [
                'item_name' => $workOrder['handling_type'],
                'unit' => $container['price_subtype'] . '/type/size',
                'type' => $container['price_type'],
                'quantity' => $container['total_container'],
                'unit_price' => $container['price'],
                'unit_multiplier' => 1,
                'total' => $container['total_container'] * $container['price'],
                'description' => '',
                'item_summary' => $container['no_containers'],
            ];
        }
        $workOrderItemPrices = array_merge($workOrderItemPrices, $containerSizePrices);

        // per goods unit price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'work_order_goods.goods_names',
                'work_order_goods.goods_unit',
                'work_order_goods.total_goods_quantity',
            ])
            ->join("(
                SELECT id_work_order, id_unit AS id_goods_unit, ref_units.unit AS goods_unit,
                    GROUP_CONCAT(CONCAT(name, ' (', quantity, unit, '-', tonnage, 'Kg-', volume, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names,
                    SUM(work_order_goods.quantity) AS total_goods_quantity 
                FROM work_order_goods
                INNER JOIN ref_goods ON ref_goods.id = work_order_goods.id_goods
                INNER JOIN ref_units ON ref_units.id = work_order_goods.id_unit
                WHERE work_order_goods.id_work_order = {$workOrder['id']}
                    AND id_work_order_container IS NULL AND id_work_order_goods IS NULL
                GROUP BY id_work_order, id_unit, ref_units.unit
                ) AS work_order_goods",
                'ref_component_prices.goods_unit = work_order_goods.id_goods_unit')
            ->where([
                'ref_component_prices.id_branch' => $workOrder['id_branch'],
                'ref_component_prices.id_customer' => $workOrder['id_customer'],
                'ref_component_prices.id_handling_type' => $workOrder['id_handling_type'],
                'ref_component_prices.price_type' => 'HANDLING',
                'ref_component_prices.price_subtype' => 'GOODS',
                'ref_component_prices.rule' => 'PER_UNIT',
            ]);
        $goods = $prices->get()->result_array();

        $goodsUnitPrices = [];
        foreach ($goods as $item) {
            $goodsUnitPrices[] = [
                'item_name' => $workOrder['handling_type'],
                'unit' => $item['price_subtype'] . '/' . $item['goods_unit'],
                'type' => $item['price_type'],
                'quantity' => $item['total_goods_quantity'],
                'unit_price' => $item['price'],
                'unit_multiplier' => 1,
                'total' => $item['total_goods_quantity'] * $item['price'],
                'description' => '',
                'item_summary' => $item['goods_names'],
            ];
        }
        $workOrderItemPrices = array_merge($workOrderItemPrices, $goodsUnitPrices);

        // per goods tonnage price
        $baseQuery = $this->getBaseInvoicePrice(true);
        $prices = $baseQuery
            ->select([
                'work_order_goods.goods_names',
                'work_order_goods.total_goods_tonnage',
            ])
            ->join("(
                SELECT id_work_order,
                    GROUP_CONCAT(CONCAT(name, ' (', quantity, unit, '-', tonnage, 'Kg-', volume, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names,
                    SUM(work_order_goods.tonnage) AS total_goods_tonnage
                FROM work_order_goods
                INNER JOIN ref_goods ON ref_goods.id = work_order_goods.id_goods
                INNER JOIN ref_units ON ref_units.id = work_order_goods.id_unit
                WHERE work_order_goods.id_work_order = {$workOrder['id']}
                    AND id_work_order_container IS NULL AND id_work_order_goods IS NULL
                GROUP BY id_work_order
                ) AS work_order_goods", '1 = 1')
            ->where([
                'ref_component_prices.id_branch' => $workOrder['id_branch'],
                'ref_component_prices.id_customer' => $workOrder['id_customer'],
                'ref_component_prices.id_handling_type' => $workOrder['id_handling_type'],
                'ref_component_prices.price_type' => 'HANDLING',
                'ref_component_prices.price_subtype' => 'GOODS',
                'ref_component_prices.rule' => 'PER_TONNAGE',
            ])
            ->where("work_order_goods.total_goods_tonnage > min_weight")
            ->where("IF((max_weight IS NULL OR max_weight = 0), TRUE, (work_order_goods.total_goods_tonnage <= max_weight))");
        $goods = $prices->get()->result_array();

        $goodsTonnagePrices = [];
        foreach ($goods as $item) {
            $tonnageRound = ($item['total_goods_tonnage'] > 0 && $item['total_goods_tonnage'] < 2000) ? 2000 : $item['total_goods_tonnage'];
            $goodsTonnagePrices[] = [
                'item_name' => $workOrder['handling_type'],
                'unit' => $item['price_subtype'] . '/' . 'tonnage (Ton)',
                'type' => $item['price_type'],
                'quantity' => ($tonnageRound / 1000),
                'unit_price' => $item['price'],
                'unit_multiplier' => 1,
                'total' => ($tonnageRound / 1000) * $item['price'],
                'description' => '',
                'item_summary' => $item['goods_names'],
            ];
        }
        $workOrderItemPrices = array_merge($workOrderItemPrices, $goodsTonnagePrices);

        // per goods volume price
        $baseQuery = $this->getBaseInvoicePrice(true);
        $prices = $baseQuery
            ->select([
                'work_order_goods.goods_names',
                'work_order_goods.total_goods_volume',
            ])
            ->join("(
                SELECT id_work_order,
                    GROUP_CONCAT(CONCAT(name, ' (', quantity, unit, '-', tonnage, 'Kg-', volume, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names, 
                    SUM(work_order_goods.volume) AS total_goods_volume
                FROM work_order_goods
                INNER JOIN ref_goods ON ref_goods.id = work_order_goods.id_goods
                INNER JOIN ref_units ON ref_units.id = work_order_goods.id_unit
                WHERE work_order_goods.id_work_order = {$workOrder['id']}
                    AND id_work_order_container IS NULL AND id_work_order_goods IS NULL
                GROUP BY id_work_order
                ) AS work_order_goods", '1 = 1')
            ->where([
                'ref_component_prices.id_branch' => $workOrder['id_branch'],
                'ref_component_prices.id_customer' => $workOrder['id_customer'],
                'ref_component_prices.id_handling_type' => $workOrder['id_handling_type'],
                'ref_component_prices.price_type' => 'HANDLING',
                'ref_component_prices.price_subtype' => 'GOODS',
                'ref_component_prices.rule' => 'PER_VOLUME',
            ])
            ->where("work_order_goods.total_goods_volume > min_weight")
            ->where("IF((max_weight IS NULL OR max_weight = 0), TRUE, (work_order_goods.total_goods_volume <= max_weight))");
        $goods = $prices->get()->result_array();

        $goodsVolumePrices = [];
        foreach ($goods as $item) {
            $goodsVolumePrices[] = [
                'item_name' => $workOrder['handling_type'],
                'unit' => $item['price_subtype'] . '/' . 'volume (M3)',
                'type' => $item['price_type'],
                'quantity' => $item['total_goods_volume'],
                'unit_price' => $item['price'],
                'unit_multiplier' => 1,
                'total' => $item['total_goods_volume'] * $item['price'],
                'description' => '',
                'item_summary' => $item['goods_names'],
            ];
        }
        $workOrderItemPrices = array_merge($workOrderItemPrices, $goodsVolumePrices);

        return $workOrderItemPrices;
    }

    /**
     * Get invoice data work order price.
     * @param $workOrder
     * @param bool $customerSetting
     * @return array
     */
    public function getInvoiceWorkOrderPrices($workOrder, $customerSetting = true)
    {
        if (!$customerSetting) {
            $workOrder['id_customer'] = NULL;
        }

        $component = $this->getWorkOrderComponentPrice($workOrder);
        $items = $this->getWorkOrderItemPrice($workOrder);
        //$invoice = $this->invoiceHandling->getHandlingInvoicePrice($workOrder);

        $invoices = array_merge($items, $component);

        $this->invoiceHandling->addTaxAndStamp($invoices);
        $this->invoiceHandling->roundFloorCurrency($invoices);

        return $invoices;
    }
}