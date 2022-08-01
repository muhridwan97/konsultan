<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class InvoiceHandlingModel extends CI_Model
{
    /**
     * InvoiceHandlingModel constructor.
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
     * Get handling component price
     * @param $handling
     * @return array
     */
    public function getHandlingComponentPrice($handling)
    {
        $handlingComponentPrices = [];

        // component per activity
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'ref_handling_types.handling_type',
                'handling_components.handling_component',
                'handling_components.quantity AS component_quantity',
                'handling_components.unit AS component_unit',
                'handling_components.description AS component_description',
                'handling_containers.no_containers',
                'handling_goods.goods_names',
            ])
            ->join("(
                SELECT handling_components.*, ref_components.handling_component, ref_units.unit
                FROM handling_components 
                INNER JOIN ref_components ON ref_components.id = handling_components.id_component
                LEFT JOIN ref_units ON ref_units.id = handling_components.id_unit
                WHERE id_handling = {$handling['id']}) AS handling_components",
                'handling_components.id_component = ref_component_prices.id_component')
            ->join('handlings', 'handlings.id = handling_components.id_handling', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join("(
                SELECT id_handling, GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers
                FROM handling_containers
                INNER JOIN ref_containers ON ref_containers.id = handling_containers.id_container
                WHERE handling_containers.id_handling = {$handling['id']} AND id_handling_container IS NULL
                ) AS handling_containers", 'handling_containers.id_handling = handlings.id', 'left')
            ->join("(
                SELECT id_handling, GROUP_CONCAT(CONCAT(name, ' (', quantity, unit, '-', tonnage, 'Kg-', volume, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names
                FROM handling_goods
                INNER JOIN ref_goods ON ref_goods.id = handling_goods.id_goods
                INNER JOIN ref_units ON ref_units.id = handling_goods.id_unit
                WHERE handling_goods.id_handling = {$handling['id']} AND id_handling_container IS NULL AND id_handling_goods IS NULL
                ) AS handling_goods", 'handling_goods.id_handling = handlings.id', 'left')
            ->where([
                'ref_component_prices.id_branch' => $handling['id_branch'],
                'ref_component_prices.id_customer' => $handling['id_customer'],
                'ref_component_prices.id_handling_type' => $handling['id_handling_type'],
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
        $handlingComponentPrices = array_merge($handlingComponentPrices, $componentPrices);

        // per container price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'ref_handling_types.handling_type',
                'handling_components.handling_component',
                'handling_components.quantity AS component_quantity',
                'handling_components.unit AS component_unit',
                'handling_components.description AS component_description',
                'handling_components.no_containers',
                'handling_components.total_container',
            ])
            ->join("(
                SELECT handling_components.*, ref_components.handling_component, ref_units.unit, 
                    handling_containers.no_containers, handling_containers.total_container
                FROM handling_components 
                INNER JOIN ref_components ON ref_components.id = handling_components.id_component
                LEFT JOIN ref_units ON ref_units.id = handling_components.id_unit
                INNER JOIN (
                    SELECT id_handling, GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers,
                        COUNT(handling_containers.id) AS total_container 
                    FROM handling_containers
                    INNER JOIN ref_containers ON ref_containers.id = handling_containers.id_container
                    WHERE id_handling_container IS NULL
                    GROUP BY id_handling
                    ) AS handling_containers ON handling_containers.id_handling = handling_components.id_handling
                WHERE handling_components.id_handling = {$handling['id']}) AS handling_components",
                'handling_components.id_component = ref_component_prices.id_component')
            ->join('handlings', 'handlings.id = handling_components.id_handling', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->where([
                'ref_component_prices.id_branch' => $handling['id_branch'],
                'ref_component_prices.id_customer' => $handling['id_customer'],
                'ref_component_prices.id_handling_type' => $handling['id_handling_type'],
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
                'description' => $component['handling_type'],
                'item_summary' => $component['no_containers'],
            ];
        }
        $handlingComponentPrices = array_merge($handlingComponentPrices, $componentContainerPrices);

        // per container, size price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'ref_handling_types.handling_type',
                'handling_components.handling_component',
                'handling_components.quantity AS component_quantity',
                'handling_components.unit AS component_unit',
                'handling_components.description AS component_description',
                'handling_components.no_containers',
                'handling_components.size',
                'handling_components.total_container',
            ])
            ->join("(
                SELECT handling_components.*, ref_components.handling_component, ref_units.unit, 
                    handling_containers.no_containers, handling_containers.size, handling_containers.total_container
                FROM handling_components 
                INNER JOIN ref_components ON ref_components.id = handling_components.id_component
                LEFT JOIN ref_units ON ref_units.id = handling_components.id_unit
                INNER JOIN (
                    SELECT id_handling, ref_containers.size,
                        GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers,
                        COUNT(handling_containers.id) AS total_container 
                    FROM handling_containers
                    INNER JOIN ref_containers ON ref_containers.id = handling_containers.id_container
                    WHERE id_handling_container IS NULL
                    GROUP BY id_handling, ref_containers.size
                    ) AS handling_containers ON handling_containers.id_handling = handling_components.id_handling
                WHERE handling_components.id_handling = {$handling['id']}) AS handling_components",
                'handling_components.id_component = ref_component_prices.id_component
                    AND ref_component_prices.container_size = handling_components.size'
            )
            ->join('handlings', 'handlings.id = handling_components.id_handling', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->where([
                'ref_component_prices.id_branch' => $handling['id_branch'],
                'ref_component_prices.id_customer' => $handling['id_customer'],
                'ref_component_prices.id_handling_type' => $handling['id_handling_type'],
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
                'description' => $component['handling_type'],
                'item_summary' => $component['no_containers'],
            ];
        }
        $handlingComponentPrices = array_merge($handlingComponentPrices, $componentContainerSizePrices);

        // per container, type price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'ref_handling_types.handling_type',
                'handling_components.handling_component',
                'handling_components.quantity AS component_quantity',
                'handling_components.unit AS component_unit',
                'handling_components.description AS component_description',
                'handling_components.no_containers',
                'handling_components.type',
                'handling_components.total_container',
            ])
            ->join("(
                SELECT handling_components.*, ref_components.handling_component, ref_units.unit, 
                    handling_containers.no_containers, handling_containers.type, handling_containers.total_container
                FROM handling_components 
                INNER JOIN ref_components ON ref_components.id = handling_components.id_component
                LEFT JOIN ref_units ON ref_units.id = handling_components.id_unit
                INNER JOIN (
                    SELECT id_handling, ref_containers.type,
                        GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers,
                        COUNT(handling_containers.id) AS total_container 
                    FROM handling_containers
                    INNER JOIN ref_containers ON ref_containers.id = handling_containers.id_container
                    WHERE id_handling_container IS NULL
                    GROUP BY id_handling, ref_containers.type
                    ) AS handling_containers ON handling_containers.id_handling = handling_components.id_handling
                WHERE handling_components.id_handling = {$handling['id']}) AS handling_components",
                'handling_components.id_component = ref_component_prices.id_component
                    AND ref_component_prices.container_type = handling_components.type'
            )
            ->join('handlings', 'handlings.id = handling_components.id_handling', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->where([
                'ref_component_prices.id_branch' => $handling['id_branch'],
                'ref_component_prices.id_customer' => $handling['id_customer'],
                'ref_component_prices.id_handling_type' => $handling['id_handling_type'],
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
                'description' => $component['handling_type'],
                'item_summary' => $component['no_containers'],
            ];
        }
        $handlingComponentPrices = array_merge($handlingComponentPrices, $componentContainerTypePrices);

        // per container, size, type price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'ref_handling_types.handling_type',
                'handling_components.handling_component',
                'handling_components.quantity AS component_quantity',
                'handling_components.unit AS component_unit',
                'handling_components.description AS component_description',
                'handling_components.no_containers',
                'handling_components.type',
                'handling_components.size',
                'handling_components.total_container',
            ])
            ->join("(
                SELECT handling_components.*, ref_components.handling_component, ref_units.unit, 
                    handling_containers.no_containers, handling_containers.type, handling_containers.size, handling_containers.total_container
                FROM handling_components 
                INNER JOIN ref_components ON ref_components.id = handling_components.id_component
                LEFT JOIN ref_units ON ref_units.id = handling_components.id_unit
                INNER JOIN (
                    SELECT id_handling, ref_containers.type, ref_containers.size,
                        GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers,
                        COUNT(handling_containers.id) AS total_container 
                    FROM handling_containers
                    INNER JOIN ref_containers ON ref_containers.id = handling_containers.id_container
                    WHERE id_handling_container IS NULL
                    GROUP BY id_handling, ref_containers.type, ref_containers.size
                    ) AS handling_containers 
                    ON handling_containers.id_handling = handling_components.id_handling
                WHERE handling_components.id_handling = {$handling['id']}) AS handling_components",
                'handling_components.id_component = ref_component_prices.id_component
                    AND ref_component_prices.container_type = handling_components.type
                        AND ref_component_prices.container_size = handling_components.size'
            )
            ->join('handlings', 'handlings.id = handling_components.id_handling', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->where([
                'ref_component_prices.id_branch' => $handling['id_branch'],
                'ref_component_prices.id_customer' => $handling['id_customer'],
                'ref_component_prices.id_handling_type' => $handling['id_handling_type'],
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
                'unit' => $component['price_subtype'] . '/type/size',
                'type' => $component['price_type'],
                'quantity' => $component['component_quantity'],
                'unit_price' => $component['price'],
                'unit_multiplier' => $component['total_container'],
                'total' => $component['component_quantity'] * $component['price'] * $component['total_container'],
                'description' => $component['handling_type'],
                'item_summary' => $component['no_containers'],
            ];
        }
        $handlingComponentPrices = array_merge($handlingComponentPrices, $componentContainerTypeSizePrices);

        // per goods unit price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'ref_handling_types.handling_type',
                'handling_components.handling_component',
                'handling_components.quantity AS component_quantity',
                'handling_components.unit AS component_unit',
                'handling_components.description AS component_description',
                'handling_components.goods_names',
                'handling_components.goods_unit',
                'handling_components.total_goods_quantity',
            ])
            ->join("(
                SELECT handling_components.*, ref_components.handling_component, ref_units.unit, 
                    handling_goods.goods_names, handling_goods.id_goods_unit, handling_goods.unit AS goods_unit, handling_goods.total_goods_quantity
                FROM handling_components 
                INNER JOIN ref_components ON ref_components.id = handling_components.id_component
                LEFT JOIN ref_units ON ref_units.id = handling_components.id_unit
                INNER JOIN (
                    SELECT id_handling, id_unit AS id_goods_unit, ref_units.unit,
                        GROUP_CONCAT(CONCAT(name, ' (', quantity, unit, '-', tonnage, 'Kg-', volume, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names,
                        SUM(handling_goods.quantity) AS total_goods_quantity 
                    FROM handling_goods
                    INNER JOIN ref_goods ON ref_goods.id = handling_goods.id_goods
                    INNER JOIN ref_units ON ref_units.id = handling_goods.id_unit
                    WHERE id_handling_container IS NULL AND id_handling_goods IS NULL
                    GROUP BY id_handling, id_unit, ref_units.unit
                    ) AS handling_goods ON handling_goods.id_handling = handling_components.id_handling
                WHERE handling_components.id_handling = {$handling['id']}) AS handling_components",
                'handling_components.id_component = ref_component_prices.id_component
                    AND ref_component_prices.goods_unit = handling_components.id_goods_unit'
            )
            ->join('handlings', 'handlings.id = handling_components.id_handling', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->where([
                'ref_component_prices.id_branch' => $handling['id_branch'],
                'ref_component_prices.id_customer' => $handling['id_customer'],
                'ref_component_prices.id_handling_type' => $handling['id_handling_type'],
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
                'description' => $component['handling_type'],
                'item_summary' => $component['goods_names'],
            ];
        }
        $handlingComponentPrices = array_merge($handlingComponentPrices, $componentGoodsUnitPrices);

        // per goods tonnage
        $baseQuery = $this->getBaseInvoicePrice(true);
        $prices = $baseQuery
            ->select([
                'ref_handling_types.handling_type',
                'handling_components.handling_component',
                'handling_components.quantity AS component_quantity',
                'handling_components.unit AS component_unit',
                'handling_components.description AS component_description',
                'handling_components.goods_names',
                'handling_components.total_goods_tonnage',
            ])
            ->join("(
                SELECT handling_components.*, ref_components.handling_component, ref_units.unit, 
                    handling_goods.goods_names, handling_goods.total_goods_tonnage
                FROM handling_components 
                INNER JOIN ref_components ON ref_components.id = handling_components.id_component
                LEFT JOIN ref_units ON ref_units.id = handling_components.id_unit
                INNER JOIN (
                    SELECT id_handling,
                        GROUP_CONCAT(CONCAT(name, ' (', quantity, unit, '-', tonnage, 'Kg-', volume, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names,
                        SUM(handling_goods.tonnage) AS total_goods_tonnage 
                    FROM handling_goods
                    INNER JOIN ref_goods ON ref_goods.id = handling_goods.id_goods
                    INNER JOIN ref_units ON ref_units.id = handling_goods.id_unit
                    WHERE id_handling_container IS NULL AND id_handling_goods IS NULL
                    GROUP BY id_handling
                    ) AS handling_goods ON handling_goods.id_handling = handling_components.id_handling
                WHERE handling_components.id_handling = {$handling['id']}) AS handling_components",
                'handling_components.id_component = ref_component_prices.id_component'
            )
            ->join('handlings', 'handlings.id = handling_components.id_handling', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->where([
                'ref_component_prices.id_branch' => $handling['id_branch'],
                'ref_component_prices.id_customer' => $handling['id_customer'],
                'ref_component_prices.id_handling_type' => $handling['id_handling_type'],
                'ref_component_prices.price_type' => 'COMPONENT',
                'ref_component_prices.price_subtype' => 'GOODS',
                'ref_component_prices.rule' => 'PER_TONNAGE',
            ])
            ->where("handling_components.total_goods_tonnage > min_weight")
            ->where("IF((max_weight IS NULL OR max_weight = 0), TRUE, (handling_components.total_goods_tonnage <= max_weight))");
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
                'description' => $component['handling_type'],
                'item_summary' => $component['goods_names'],
            ];
        }
        $handlingComponentPrices = array_merge($handlingComponentPrices, $componentGoodsTonnagePrices);

        // per volume price
        $baseQuery = $this->getBaseInvoicePrice(true);
        $prices = $baseQuery
            ->select([
                'ref_handling_types.handling_type',
                'handling_components.handling_component',
                'handling_components.quantity AS component_quantity',
                'handling_components.unit AS component_unit',
                'handling_components.description AS component_description',
                'handling_components.goods_names',
                'handling_components.total_goods_volume',
            ])
            ->join("(
                SELECT handling_components.*, ref_components.handling_component, ref_units.unit, 
                    handling_goods.goods_names, handling_goods.total_goods_volume
                FROM handling_components 
                INNER JOIN ref_components ON ref_components.id = handling_components.id_component
                LEFT JOIN ref_units ON ref_units.id = handling_components.id_unit
                INNER JOIN (
                    SELECT id_handling,
                        GROUP_CONCAT(CONCAT(name, ' (', quantity, unit, '-', tonnage, 'Kg-', volume, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names,
                        SUM(handling_goods.volume) AS total_goods_volume
                    FROM handling_goods
                    INNER JOIN ref_goods ON ref_goods.id = handling_goods.id_goods
                    INNER JOIN ref_units ON ref_units.id = handling_goods.id_unit
                    WHERE id_handling_container IS NULL AND id_handling_goods IS NULL
                    GROUP BY id_handling
                    ) AS handling_goods ON handling_goods.id_handling = handling_components.id_handling
                WHERE handling_components.id_handling = {$handling['id']}) AS handling_components",
                'handling_components.id_component = ref_component_prices.id_component'
            )
            ->join('handlings', 'handlings.id = handling_components.id_handling', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->where([
                'ref_component_prices.id_branch' => $handling['id_branch'],
                'ref_component_prices.id_customer' => $handling['id_customer'],
                'ref_component_prices.id_handling_type' => $handling['id_handling_type'],
                'ref_component_prices.price_type' => 'COMPONENT',
                'ref_component_prices.price_subtype' => 'GOODS',
                'ref_component_prices.rule' => 'PER_VOLUME',
            ])
            ->where("handling_components.total_goods_volume > min_weight")
            ->where("IF((max_weight IS NULL OR max_weight = 0), TRUE, (handling_components.total_goods_volume <= max_weight))");
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
                'description' => $component['handling_type'],
                'item_summary' => $component['goods_names'],
            ];
        }
        $handlingComponentPrices = array_merge($handlingComponentPrices, $componentGoodsVolumePrices);

        return $handlingComponentPrices;
    }

    /**
     * Get handling detail item price.
     * @param $handling
     * @return array
     */
    public function getHandlingItemPrice($handling)
    {
        $handlingItemPrices = [];

        // per activity
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'ref_handling_types.handling_type',
                'handling_containers.no_containers',
                'handling_goods.goods_names',
            ])
            ->join('handlings', 'handlings.id_handling_type = ref_component_prices.id_handling_type')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join("(
                SELECT id_handling, GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers
                FROM handling_containers
                INNER JOIN ref_containers ON ref_containers.id = handling_containers.id_container
                WHERE handling_containers.id_handling = {$handling['id']} AND id_handling_container IS NULL
                ) AS handling_containers", 'handlings.id = handling_containers.id_handling', 'left')
            ->join("(
                SELECT id_handling, GROUP_CONCAT(CONCAT(name, ' (', quantity, unit, '-', tonnage, 'Kg-', volume, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names
                FROM handling_goods
                INNER JOIN ref_goods ON ref_goods.id = handling_goods.id_goods
                INNER JOIN ref_units ON ref_units.id = handling_goods.id_unit
                WHERE handling_goods.id_handling = {$handling['id']}
                    AND id_handling_container IS NULL AND id_handling_goods IS NULL
                ) AS handling_goods", 'handlings.id = handling_goods.id_handling', 'left')
            ->where([
                'handlings.id' => $handling['id'],
                'ref_component_prices.id_branch' => $handling['id_branch'],
                'ref_component_prices.id_customer' => $handling['id_customer'],
                'ref_component_prices.id_handling_type' => $handling['id_handling_type'],
                'ref_component_prices.price_type' => 'HANDLING',
                'ref_component_prices.price_subtype' => 'ACTIVITY',
                'ref_component_prices.rule' => 'PER_ACTIVITY'
            ]);

        $activities = $prices->get()->result_array();

        $handlingActivities = [];
        foreach ($activities as $activity) {
            $handlingActivities[] = [
                'item_name' => $handling['handling_type'],
                'unit' => $activity['price_subtype'],
                'type' => $activity['price_type'],
                'quantity' => '1',
                'unit_price' => $activity['price'],
                'unit_multiplier' => 1,
                'total' => $activity['price'],
                'description' => '',
                'item_summary' => $activity['no_containers'] . '|' . $activity['goods_names'],
            ];
        }
        $handlingItemPrices = array_merge($handlingItemPrices, $handlingActivities);

        // per container price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'handling_containers.no_containers',
                'handling_containers.total_container',
            ])
            ->join("(
                SELECT id_handling, 
                    GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers,
                    COUNT(handling_containers.id) AS total_container 
                FROM handling_containers
                INNER JOIN ref_containers ON ref_containers.id = handling_containers.id_container
                WHERE handling_containers.id_handling = {$handling['id']}
                AND id_handling_container IS NULL
                ) AS handling_containers", '1 = 1')
            ->where([
                'ref_component_prices.id_branch' => $handling['id_branch'],
                'ref_component_prices.id_customer' => $handling['id_customer'],
                'ref_component_prices.id_handling_type' => $handling['id_handling_type'],
                'ref_component_prices.price_type' => 'HANDLING',
                'ref_component_prices.price_subtype' => 'CONTAINER',
                'ref_component_prices.rule' => 'PER_CONTAINER',
            ]);
        $container = $prices->get()->row_array();

        if (!empty($container)) {
            $containerPrices[] = [
                'item_name' => $handling['handling_type'],
                'unit' => $container['price_subtype'],
                'type' => $container['price_type'],
                'quantity' => $container['total_container'],
                'unit_price' => $container['price'],
                'unit_multiplier' => 1,
                'total' => $container['total_container'] * $container['price'],
                'description' => '',
                'item_summary' => $container['no_containers'],
            ];
            $handlingItemPrices = array_merge($handlingItemPrices, $containerPrices);
        }

        // per container, size price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'handling_containers.no_containers',
                'handling_containers.size',
                'handling_containers.total_container',
            ])
            ->join("(
                SELECT id_handling, ref_containers.size,
                    GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers,
                    COUNT(handling_containers.id) AS total_container 
                FROM handling_containers
                INNER JOIN ref_containers ON ref_containers.id = handling_containers.id_container
                WHERE handling_containers.id_handling = {$handling['id']}
                    AND id_handling_container IS NULL
                GROUP BY id_handling, ref_containers.size
                ) AS handling_containers",
                'ref_component_prices.container_size = handling_containers.size')
            ->where([
                'ref_component_prices.id_branch' => $handling['id_branch'],
                'ref_component_prices.id_customer' => $handling['id_customer'],
                'ref_component_prices.id_handling_type' => $handling['id_handling_type'],
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
                'item_name' => $handling['handling_type'],
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
        $handlingItemPrices = array_merge($handlingItemPrices, $containerSizePrices);

        // per container, type price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'handling_containers.no_containers',
                'handling_containers.type',
                'handling_containers.total_container',
            ])
            ->join("(
                SELECT id_handling, ref_containers.type,
                    GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers,
                    COUNT(handling_containers.id) AS total_container 
                FROM handling_containers
                INNER JOIN ref_containers ON ref_containers.id = handling_containers.id_container
                WHERE handling_containers.id_handling = {$handling['id']}
                    AND id_handling_container IS NULL
                GROUP BY id_handling, ref_containers.type
                ) AS handling_containers",
                'ref_component_prices.container_type = handling_containers.type')
            ->where([
                'ref_component_prices.id_branch' => $handling['id_branch'],
                'ref_component_prices.id_customer' => $handling['id_customer'],
                'ref_component_prices.id_handling_type' => $handling['id_handling_type'],
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
                'item_name' => $handling['handling_type'],
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
        $handlingItemPrices = array_merge($handlingItemPrices, $containerSizePrices);

        // per container, type, size price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'handling_containers.no_containers',
                'handling_containers.type',
                'handling_containers.size',
                'handling_containers.total_container',
            ])
            ->join("(
                SELECT id_handling, ref_containers.type, ref_containers.size,
                    GROUP_CONCAT(CONCAT(no_container, ' (', type, '-', size, '-', status_danger, ')') SEPARATOR ',') AS no_containers,
                    COUNT(handling_containers.id) AS total_container 
                FROM handling_containers
                INNER JOIN ref_containers ON ref_containers.id = handling_containers.id_container
                WHERE handling_containers.id_handling = {$handling['id']}
                    AND id_handling_container IS NULL
                GROUP BY id_handling, ref_containers.type, ref_containers.size
                ) AS handling_containers",
                'ref_component_prices.container_type = handling_containers.type
                    AND ref_component_prices.container_size = handling_containers.size')
            ->where([
                'ref_component_prices.id_branch' => $handling['id_branch'],
                'ref_component_prices.id_customer' => $handling['id_customer'],
                'ref_component_prices.id_handling_type' => $handling['id_handling_type'],
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
                'item_name' => $handling['handling_type'],
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
        $handlingItemPrices = array_merge($handlingItemPrices, $containerSizePrices);

        // per goods unit price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery
            ->select([
                'handling_goods.goods_names',
                'handling_goods.goods_unit',
                'handling_goods.total_goods_quantity',
            ])
            ->join("(
                SELECT id_handling, id_unit AS id_goods_unit, ref_units.unit AS goods_unit,
                    GROUP_CONCAT(CONCAT(name, ' (', quantity, unit, '-', tonnage, 'Kg-', volume, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names,
                    SUM(handling_goods.quantity) AS total_goods_quantity 
                FROM handling_goods
                INNER JOIN ref_goods ON ref_goods.id = handling_goods.id_goods
                INNER JOIN ref_units ON ref_units.id = handling_goods.id_unit
                WHERE handling_goods.id_handling = {$handling['id']}
                    AND id_handling_container IS NULL AND id_handling_goods IS NULL
                GROUP BY id_handling, id_unit, ref_units.unit
                ) AS handling_goods", 'ref_component_prices.goods_unit = handling_goods.id_goods_unit')
            ->where([
                'ref_component_prices.id_branch' => $handling['id_branch'],
                'ref_component_prices.id_customer' => $handling['id_customer'],
                'ref_component_prices.id_handling_type' => $handling['id_handling_type'],
                'ref_component_prices.price_type' => 'HANDLING',
                'ref_component_prices.price_subtype' => 'GOODS',
                'ref_component_prices.rule' => 'PER_UNIT',
            ]);
        $goods = $prices->get()->result_array();

        $goodsUnitPrices = [];
        foreach ($goods as $item) {
            $goodsUnitPrices[] = [
                'item_name' => $handling['handling_type'],
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
        $handlingItemPrices = array_merge($handlingItemPrices, $goodsUnitPrices);

        // per goods tonnage price
        $baseQuery = $this->getBaseInvoicePrice(true);
        $prices = $baseQuery
            ->select([
                'handling_goods.goods_names',
                'handling_goods.total_goods_tonnage',
            ])
            ->join("(
                SELECT id_handling,
                    GROUP_CONCAT(CONCAT(name, ' (', quantity, unit, '-', tonnage, 'Kg-', volume, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names,
                    SUM(handling_goods.tonnage) AS total_goods_tonnage
                FROM handling_goods
                INNER JOIN ref_goods ON ref_goods.id = handling_goods.id_goods
                INNER JOIN ref_units ON ref_units.id = handling_goods.id_unit
                WHERE handling_goods.id_handling = {$handling['id']}
                    AND id_handling_container IS NULL AND id_handling_goods IS NULL
                GROUP BY id_handling) AS handling_goods", '1 = 1')
            ->where([
                'ref_component_prices.id_branch' => $handling['id_branch'],
                'ref_component_prices.id_customer' => $handling['id_customer'],
                'ref_component_prices.id_handling_type' => $handling['id_handling_type'],
                'ref_component_prices.price_type' => 'HANDLING',
                'ref_component_prices.price_subtype' => 'GOODS',
                'ref_component_prices.rule' => 'PER_TONNAGE',
            ])
            ->where("handling_goods.total_goods_tonnage > min_weight")
            ->where("IF((max_weight IS NULL OR max_weight = 0), TRUE, (handling_goods.total_goods_tonnage <= max_weight))");
        $goods = $prices->get()->result_array();

        $goodsTonnagePrices = [];
        foreach ($goods as $item) {
            $tonnageRound = ($item['total_goods_tonnage'] > 0 && $item['total_goods_tonnage'] < 2000) ? 2000 : $item['total_goods_tonnage'];
            $goodsTonnagePrices[] = [
                'item_name' => $handling['handling_type'],
                'unit' => $item['price_subtype'] . '/tonnage (Ton)',
                'type' => $item['price_type'],
                'quantity' => ($tonnageRound / 1000),
                'unit_price' => $item['price'],
                'unit_multiplier' => 1,
                'total' => ($tonnageRound / 1000) * $item['price'],
                'description' => '',
                'item_summary' => $item['goods_names'],
            ];
        }
        $handlingItemPrices = array_merge($handlingItemPrices, $goodsTonnagePrices);

        // per goods volume price
        $baseQuery = $this->getBaseInvoicePrice(true);
        $prices = $baseQuery
            ->select([
                'handling_goods.goods_names',
                'handling_goods.total_goods_volume',
            ])
            ->join("(
                SELECT id_handling,
                    GROUP_CONCAT(CONCAT(name, ' (', quantity, unit, '-', tonnage, 'Kg-', volume, 'M3-', status_danger, ')') SEPARATOR ',') AS goods_names,
                    SUM(handling_goods.volume) AS total_goods_volume
                FROM handling_goods
                INNER JOIN ref_goods ON ref_goods.id = handling_goods.id_goods
                INNER JOIN ref_units ON ref_units.id = handling_goods.id_unit
                WHERE handling_goods.id_handling = {$handling['id']}
                    AND id_handling_container IS NULL AND id_handling_goods IS NULL
                GROUP BY id_handling) AS handling_goods", '1 = 1')
            ->where([
                'ref_component_prices.id_branch' => $handling['id_branch'],
                'ref_component_prices.id_customer' => $handling['id_customer'],
                'ref_component_prices.id_handling_type' => $handling['id_handling_type'],
                'ref_component_prices.price_type' => 'HANDLING',
                'ref_component_prices.price_subtype' => 'GOODS',
                'ref_component_prices.rule' => 'PER_VOLUME',
            ])
            ->where("handling_goods.total_goods_volume > min_weight")
            ->where("IF((max_weight IS NULL OR max_weight = 0), TRUE, (handling_goods.total_goods_volume <= max_weight))");
        $goods = $prices->get()->result_array();

        $goodsVolumePrices = [];
        foreach ($goods as $item) {
            $goodsVolumePrices[] = [
                'item_name' => $handling['handling_type'],
                'unit' => $item['price_subtype'] . '/volume (M3)',
                'type' => $item['price_type'],
                'quantity' => $item['total_goods_volume'],
                'unit_price' => $item['price'],
                'unit_multiplier' => 1,
                'total' => $item['total_goods_volume'] * $item['price'],
                'description' => '',
                'item_summary' => $item['goods_names'],
            ];
        }
        $handlingItemPrices = array_merge($handlingItemPrices, $goodsVolumePrices);

        return $handlingItemPrices;
    }

    /**
     * Get invoice level price.
     * @param $handling
     * @return array
     */
    public function getHandlingInvoicePrice($handling)
    {
        $handlingInvoicePrices = [];

        // customer, branch level price
        $baseQuery = $this->getBaseInvoicePrice();
        $prices = $baseQuery->where([
            'ref_component_prices.id_branch' => $handling['id_branch'],
            'ref_component_prices.id_customer' => $handling['id_customer'],
            'ref_component_prices.id_handling_type' => NULL,
            'ref_component_prices.price_type' => 'INVOICE',
            'ref_component_prices.price_subtype' => 'ACTIVITY',
            'ref_component_prices.rule' => 'PER_ACTIVITY',
        ]);
        $customerBranchPrices = $prices->get()->result_array();

        $invoiceCustomerBranchPrices = [];
        foreach ($customerBranchPrices as $price) {
            $invoiceCustomerBranchPrices[] = [
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
        $handlingInvoicePrices = array_merge($handlingInvoicePrices, $invoiceCustomerBranchPrices);

        return $handlingInvoicePrices;
    }

    /**
     * Add tax and stamp value.
     * @param $invoices
     */
    public function addTaxAndStamp(&$invoices)
    {
        $totalPrice = 0;
        foreach ($invoices as $price) {
            $totalPrice += floor($price['total']);
        }
        $invoices[] = [
            'item_name' => 'PPN (10%)',
            'unit' => 'OTHER',
            'type' => 'OTHER',
            'quantity' => 1,
            'unit_price' => 0.1 * $totalPrice,
            'unit_multiplier' => 1,
            'total' => 0.1 * $totalPrice,
            'description' => '',
            'item_summary' => '',
        ];

        $stamp = 0;
        if ($totalPrice > 1000000) {
            $stamp = 6000;
        } else if ($totalPrice >= 250000) {
            $stamp = 3000;
        }

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
     * @param $invoices
     */
    public function roundFloorCurrency(&$invoices)
    {
        foreach ($invoices as &$invoice) {
            $invoice['unit_price'] = round($invoice['unit_price'], 0, PHP_ROUND_HALF_DOWN);
            $invoice['total'] = $invoice['unit_price'] * $invoice['quantity'] * $invoice['unit_multiplier'];
        }
    }

    /**
     * Get invoice data handling price.
     * @param $handling
     * @param bool $customerSetting
     * @return array
     */
    public function getInvoiceHandlingPrices($handling, $customerSetting = true)
    {
        if (!$customerSetting) {
            $handling['id_customer'] = NULL;
        }

        $items = $this->getHandlingItemPrice($handling);
        $component = $this->getHandlingComponentPrice($handling);
        //$invoice = $this->getHandlingInvoicePrice($handling);

        $invoices = array_merge($items, $component);

        $this->addTaxAndStamp($invoices);
        $this->roundFloorCurrency($invoices);

        return $invoices;
    }
}