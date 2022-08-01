<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class ReportTppModel
 * @property BranchModel $branchModel
 */
class ReportTppModel extends CI_Model
{
    /**
     * ReportTppModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get report customs daily.
     * @param string $type
     * @param $filters
     * @return mixed
     */
    public function getReportCustomsDaily($type = 'INBOUND', $filters = null)
    {
        $branchId = get_active_branch('id');

        $inboundSettingId = get_setting('default_inbound_handling');
        $outboundSettingId = get_setting('default_outbound_handling');

        $report = $this->db->select([
            "'CONTAINER' AS item_type",
            'ref_containers.no_container',
            'ref_containers.size AS container_size',
            'work_order_containers.seal',
            'bookings.no_reference',
            'bookings.reference_date',
            'ref_booking_types.booking_type',
            'customers.name AS booking_customer_name',
            'work_orders.completed_at',
            'COALESCE(booking_containers.description, work_order_containers.description) AS goods_name',
            'container_tps.name AS tps_name'
        ])
            ->from('bookings')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer')
            ->join('handlings', 'handlings.id_booking = bookings.id')
            ->join('work_orders', 'work_orders.id_handling = handlings.id')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id')
            ->join('booking_containers', 'booking_containers.id_container = work_order_containers.id_container AND bookings.id = booking_containers.id_booking', 'left')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('ref_people AS container_tps', 'container_tps.id = safe_conducts.id_source_warehouse', 'left');

        if ($type == 'INBOUND') {
            $report->where('handlings.id_handling_type', $inboundSettingId);
        } else {
            $report->where('handlings.id_handling_type', $outboundSettingId);
        }

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        $baseOutContainerQuery = $this->db->get_compiled_select();

        $report = $this->db->select([
            "'GOODS' AS item_type",
            '"" AS no_container',
            '"" AS container_size',
            '"" AS seal',
            'bookings.no_reference',
            'bookings.reference_date',
            'ref_booking_types.booking_type',
            'customers.name AS booking_customer_name',
            'work_orders.completed_at',
            'ref_goods.name AS goods_name',
            'container_tps.name AS tps_name'
        ])
            ->from('bookings')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer')
            ->join('handlings', 'handlings.id_booking = bookings.id')
            ->join('work_orders', 'work_orders.id_handling = handlings.id')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('ref_people AS container_tps', 'container_tps.id = safe_conducts.id_source_warehouse', 'left');

        if ($type == 'INBOUND') {
            $report->where('handlings.id_handling_type', $inboundSettingId);
        } else {
            $report->where('handlings.id_handling_type', $outboundSettingId);
        }

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        $baseOutGoodsQuery = $this->db->get_compiled_select();

        $queryReport = "
          SELECT * FROM (
              SELECT * FROM ({$baseOutContainerQuery}) AS in_container 
              UNION 
              SELECT * FROM ({$baseOutGoodsQuery}) AS in_goods
          ) AS outbounds WHERE 1 = 1
        ";

        $queryReport .= ' AND DATE(completed_at)>="' . format_date($filters['date_from']) . '"';
        $queryReport .= ' AND DATE(completed_at)<="' . format_date($filters['date_to']) . '"';
        $queryReport .= ' ORDER BY completed_at DESC';

        $report = $this->db->query($queryReport);

        return $report->result_array();
    }

    /**
     * Get report customs stock.
     *
     * @param array $filters
     * @return mixed
     */
    public function getReportCustomsStock($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        // collect default handling type
        $handlingTypeIdInbound = get_setting('default_inbound_handling');
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');
        $handlingTypeIdStrippingTPP = 16; // TODO: must dynamic from application preferences
        $handlingTypeIdStuffingTPP = 17;

        // get data FCL
        $report = $this->db->select([
            'ref_booking_types.booking_type',
            'bookings.no_reference',
            'bookings.reference_date',
            get_ext_field('BA_SERAH_NO', 'bookings.id', 'no_ba_serah'),
            get_ext_field('BA_SERAH_TGL', 'bookings.id', 'ba_serah_date'),
            get_ext_field('BA_SEGEL_NO', 'bookings.id', 'no_ba_seal'),
            get_ext_field('BA_SEGEL_TGL', 'bookings.id', 'ba_seal_date'),
            'work_orders.completed_at AS inbound_date',
            '"FCL" as cargo_type',
            'ref_containers.no_container',
            '"" AS ex_container',
            'ref_containers.size AS container_size',
            'customers.name AS owner_name',
            'container_tps.name AS tps_name',
            'container_tps.region AS tps_region',
            'COALESCE(
                NULLIF(work_order_goods.goods_name, ""),
                booking_containers.description
            ) AS goods_name',
            'work_order_containers.quantity',
            '"CARGO" AS unit',
            get_ext_field('NHP_NO', 'bookings.id', 'no_nhp'),
            get_ext_field('NHP_TGL', 'bookings.id', 'nhp_date'),
            '(SELECT booking_statuses.document_status FROM booking_statuses 
              INNER JOIN (
                SELECT id_booking, MAX(booking_statuses.created_at) created_at
                FROM booking_statuses GROUP BY id_booking
              ) b2 ON booking_statuses.id_booking = b2.id_booking AND booking_statuses.created_at = b2.created_at
              WHERE booking_statuses.id_booking = bookings.id 
              LIMIT 1) AS "document_status"',
            'last_stripping_dates.stripping_date',
            'last_stuffing_dates.stuffing_date',
            'last_outbound_dates.outbound_date',
            get_ext_field('DOK_NO', 'bookings.id', 'no_doc_kep'),
            get_ext_field('DOK_TGL', 'bookings.id', 'doc_kep_date'),
            'outbounds.no_reference AS no_reference_out',
            'outbounds.reference_date AS reference_out_date',
            '"" AS description',
            'work_order_containers.quantity AS total_in_container',
            '0 AS total_in_goods',
            'IF(last_outbound_dates.outbound_date IS NULL, 0, 1) AS total_out_container',
            '0 AS total_out_goods',
        ])
            ->from('bookings')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('handlings', 'handlings.id_booking = bookings.id', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container', 'left')
            ->join("(
                SELECT bookings.id_booking AS id_booking_ref, id_container, MAX(completed_at) AS outbound_date 
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdOutbound}'
                GROUP BY bookings.id_booking, id_container
            ) AS last_outbound_dates", 'bookings.id = last_outbound_dates.id_booking_ref
                AND work_order_containers.id_container = last_outbound_dates.id_container', 'left')
            ->join("(
                SELECT bookings.id AS id_booking, id_container, MAX(completed_at) AS stripping_date 
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdStrippingTPP}'
                GROUP BY bookings.id, id_container
            ) AS last_stripping_dates", 'bookings.id = last_stripping_dates.id_booking
                AND work_order_containers.id_container = last_outbound_dates.id_container', 'left')
            ->join("(
                SELECT bookings.id AS id_booking, id_container, MAX(completed_at) AS stuffing_date 
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdStuffingTPP}'
                GROUP BY bookings.id, id_container
            ) AS last_stuffing_dates", 'bookings.id = last_stuffing_dates.id_booking
                AND ref_containers.id = last_outbound_dates.id_container', 'left')
            ->join('booking_containers', 'bookings.id = booking_containers.id_booking
                AND ref_containers.id = booking_containers.id_container', 'left')
            ->join("
                (SELECT id_work_order, id_work_order_container,
                    GROUP_CONCAT(ref_goods.name SEPARATOR '^^') AS goods_name,
                    GROUP_CONCAT(work_order_goods.quantity SEPARATOR '^^') AS quantity,
                    GROUP_CONCAT(ref_units.unit SEPARATOR '^^') AS unit
                    FROM work_order_goods
                    LEFT JOIN ref_goods ON work_order_goods.id_goods = ref_goods.id
                    LEFT JOIN ref_units ON work_order_goods.id_unit = ref_units.id
                    GROUP BY id_work_order, id_work_order_container
                ) AS work_order_goods",
                'work_order_goods.id_work_order_container = work_order_containers.id
                    AND work_order_goods.id_work_order = work_order_containers.id_work_order', 'left')
            ->join('(
                SELECT safe_conducts.*, id_container AS id_container_sf FROM safe_conducts
                LEFT JOIN safe_conduct_containers
                    ON safe_conducts.id = safe_conduct_containers.id_safe_conduct
                WHERE safe_conducts.is_deleted = FALSE
                ) AS safe_conducts',
                'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('ref_people AS container_tps', 'container_tps.id = safe_conducts.id_source_warehouse', 'left')
            ->join('bookings AS outbounds', 'outbounds.id_booking = bookings.id', 'left')
            ->where('handlings.id_handling_type', $handlingTypeIdInbound)
            ->where('work_order_containers.id_container IS NOT NULL');

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }
        if ($userType == 'EXTERNAL') {
            $report->where('work_order_containers.id_owner', $customerId);
        }
        $baseInContainerQuery = $this->db->get_compiled_select();

        // get data LCL
        $report = $this->db->select([
            'ref_booking_types.booking_type',
            'bookings.no_reference',
            'bookings.reference_date',
            get_ext_field('BA_SERAH_NO', 'bookings.id', 'no_ba_serah'),
            get_ext_field('BA_SERAH_TGL', 'bookings.id', 'ba_serah_date'),
            get_ext_field('BA_SEGEL_NO', 'bookings.id', 'no_ba_seal'),
            get_ext_field('BA_SEGEL_TGL', 'bookings.id', 'ba_seal_date'),
            'work_orders.completed_at AS inbound_date',
            '"LCL" as cargo_type',
            '"" AS no_container',
            'work_order_goods.ex_no_container AS ex_container',
            '"" AS container_size',
            'customers.name AS owner_name',
            'container_tps.name AS tps_name',
            'container_tps.region AS tps_region',
            'ref_goods.name AS goods_name',
            'work_order_goods.quantity',
            'ref_units.unit',
            get_ext_field('NHP_NO', 'bookings.id', 'no_nhp'),
            get_ext_field('NHP_TGL', 'bookings.id', 'nhp_date'),
            '(SELECT booking_statuses.document_status FROM booking_statuses 
              INNER JOIN (
                SELECT id_booking, MAX(booking_statuses.created_at) created_at
                FROM booking_statuses GROUP BY id_booking
              ) b2 ON booking_statuses.id_booking = b2.id_booking AND booking_statuses.created_at = b2.created_at
              WHERE booking_statuses.id_booking = bookings.id 
              LIMIT 1) AS "document_status"',
            'last_stripping_dates.stripping_date',
            'last_stuffing_dates.stuffing_date',
            'last_outbound_dates.outbound_date',
            get_ext_field('DOK_NO', 'bookings.id', 'no_doc_kep'),
            get_ext_field('DOK_TGL', 'bookings.id', 'doc_kep_date'),
            'outbounds.no_reference AS no_reference_out',
            'outbounds.reference_date AS reference_out_date',
            '"" AS description',
            '0 AS total_in_container',
            'work_order_goods.quantity AS total_in_goods',
            '0 AS total_out_container',
            'outbound_goods.total AS total_out_goods',
        ])
            ->from('bookings')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('handlings', 'handlings.id_booking = bookings.id', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods', 'left')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit', 'left')
            ->join("(
                SELECT bookings.id_booking AS id_booking_ref, id_goods, MAX(completed_at) AS outbound_date 
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdOutbound}'
                GROUP BY bookings.id_booking, id_goods
            ) AS last_outbound_dates", 'bookings.id = last_outbound_dates.id_booking_ref
                AND work_order_goods.id_goods = last_outbound_dates.id_goods', 'left')
            ->join("(
                SELECT bookings.id_booking AS id_booking_ref, id_goods, SUM(quantity) AS total 
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdOutbound}'
                GROUP BY bookings.id_booking, id_goods
            ) AS outbound_goods", 'bookings.id = outbound_goods.id_booking_ref
                AND work_order_goods.id_goods = outbound_goods.id_goods', 'left')
            ->join("(
                SELECT bookings.id AS id_booking, id_goods, MAX(completed_at) AS stripping_date 
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdStrippingTPP}'
                GROUP BY bookings.id, id_goods
            ) AS last_stripping_dates", 'bookings.id = last_stripping_dates.id_booking
                AND work_order_goods.id_goods = last_outbound_dates.id_goods', 'left')
            ->join("(
                SELECT bookings.id AS id_booking, id_goods, MAX(completed_at) AS stuffing_date 
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdStuffingTPP}'
                GROUP BY bookings.id, id_goods
            ) AS last_stuffing_dates", 'bookings.id = last_stuffing_dates.id_booking
                AND work_order_goods.id_goods = last_outbound_dates.id_goods', 'left')
            ->join('(
                SELECT safe_conducts.*, id_goods FROM safe_conducts
                LEFT JOIN safe_conduct_goods
                    ON safe_conducts.id = safe_conduct_goods.id_safe_conduct
                WHERE safe_conducts.is_deleted = FALSE
                ) AS safe_conducts',
                'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('ref_people AS container_tps', 'container_tps.id = safe_conducts.id_source_warehouse', 'left')
            ->join('bookings AS outbounds', 'outbounds.id_booking = bookings.id', 'left')
            ->where('handlings.id_handling_type', $handlingTypeIdInbound);

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }
        if ($userType == 'EXTERNAL') {
            $report->where('work_order_goods.id_owner', $customerId);
        }
        $baseInGoodsQuery = $this->db->get_compiled_select();

        $report = $this->db->from("(
              SELECT * FROM ({$baseInContainerQuery}) AS in_containers 
              UNION 
              SELECT * FROM ({$baseInGoodsQuery}) AS in_goods
          ) AS data");

        if (!empty($filters)) {
            if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                $report->where('DATE(inbound_date)>=', sql_date_format($filters['date_from'], false));
            }
            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $report->where('DATE(inbound_date)<=', sql_date_format($filters['date_to'], false));
            }
        }

        $report
            ->group_start()
            ->like('booking_type', $search)
            ->or_like('no_reference', $search)
            ->or_like('reference_date', $search)
            ->or_like('inbound_date', $search)
            ->or_like('no_container', $search)
            ->or_like('container_size', $search)
            ->or_like('owner_name', $search)
            ->or_like('tps_name', $search)
            ->or_like('tps_region', $search)
            ->or_like('quantity', $search)
            ->or_like('stuffing_date', $search)
            ->or_like('outbound_date', $search)
            ->group_end();

        if ($start < 0) {
            return $report->get()->result_array();
        }

        $finalStockQuery = $this->db->get_compiled_select();

        $reportTotal = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$finalStockQuery}) AS CI_count_all_results")->row_array()['numrows'];

        if (!empty($column)) {
            if ($column == 'no') $column = 'inbound_date';
            $finalStockQuery .= ' ORDER BY `' . $column . '` ' . $sort;
        } else {
            $finalStockQuery .= ' ORDER BY inbound_date DESC';
        }
        $reportData = $this->db->query($finalStockQuery . " LIMIT {$start}, {$length}")->result_array();

        foreach ($reportData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => get_url_param('draw', $this->input->post('draw')),
            "recordsTotal" => count($reportData),
            "recordsFiltered" => $reportTotal,
            "data" => $reportData
        ];

        return $pageData;
    }

    /**
     * Get report event summary.
     * @param string $type
     * @param $filters
     * @return mixed
     */
    public function getReportEventSummary($filters = null, $type = null)
    {
        $report = $this->db->select([
            'booking_news.no_booking_news',
            'booking_news.type',
            'booking_news.booking_news_date',
            'booking_news.no_sprint',
            'booking_news.tps',
            'bookings.no_reference',
            'bookings.reference_date',
            '(SELECT booking_extensions.value FROM booking_extensions 
              LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
              WHERE booking_extensions.id_booking = bookings.id AND ref_extension_fields.field_name = "NO_BC11"
              LIMIT 1) AS no_bc11',
            '(SELECT booking_extensions.value FROM booking_extensions 
              LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
              WHERE booking_extensions.id_booking = bookings.id AND ref_extension_fields.field_name = "TGL_BC11"
              LIMIT 1) AS bc11_date'
        ])
            ->from('booking_news')
            ->join('booking_news_details', 'booking_news.id = booking_news_details.id_booking_news', 'left')
            ->join('bookings', 'booking_news_details.id_booking = bookings.id', 'left');

        if (!empty($type)) {
            $report->where('booking_news.type', $type);
        }

        if (!empty($filters)) {
            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
                $dayFilterFrom = key_exists('date_from', $filters) && !empty($filters['date_from']);
                $dayFilterTo = key_exists('date_to', $filters) && !empty($filters['date_to']);
                if ($dayFilterFrom && $dayFilterTo) {
                    $report->having("DATE({$filters['date_type']}) >= DATE('" . sql_date_format($filters['date_from'], false) . "')");
                    $report->having("DATE({$filters['date_type']}) <= DATE('" . sql_date_format($filters['date_to'], false) . "')");
                }
            }
        }

        if (!empty($branchId)) {
            $report->where('id_branch', $branchId);
        }

        return $report->get()->result_array();
    }

    /**
     * Get report shipping line stock.
     *
     * @param array $filters
     * @return mixed
     */
    public function getReportShippingLineStock($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        // exclude moving in so minimum completed at will be inbound
        $movingInSetting = get_setting('default_moving_in_handling');

        $report = $this->db
            ->select([
                'customers.name AS owner_name',
                'IFNULL(bookings.id_booking, bookings.id) AS id_booking',
                'booking_in.vessel',
                'booking_in.voyage',
                'id_container',
                'ref_containers.no_container',
                'ref_containers.type AS container_type',
                'ref_containers.size AS container_size',
                'SUM(CAST(quantity AS SIGNED) * multiplier_container) AS stock',
                'MIN(completed_at) AS completed_at',
                'DATEDIFF(CURDATE(), MIN(completed_at)) AS age',
                'MAX(work_order_containers.id) AS id_work_order_container',
            ])
            ->from('bookings')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('bookings AS booking_in', 'booking_in.id = IFNULL(bookings.id_booking, bookings.id)', 'left')
            ->join('safe_conducts', 'safe_conducts.id_booking = bookings.id', 'left')
            ->join('work_orders', 'work_orders.id_safe_conduct = safe_conducts.id', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id', 'left')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container', 'left')
            ->where('id_handling_type !=', $movingInSetting)
            ->group_by('customers.name, IFNULL(bookings.id_booking, bookings.id), booking_in.vessel, booking_in.voyage, id_container');

        if (key_exists('shipping_line', $filters) && !empty($filters['shipping_line'])) {
            if (is_array($filters['shipping_line'])) {
                $report->where_in('id_shipping_line', $filters['shipping_line']);
            } else {
                $report->where('id_shipping_line', $filters['shipping_line']);
            }
        }

        if (key_exists('stock_date', $filters) && !empty($filters['stock_date'])) {
            $report->where('completed_at <=', sql_date_format($filters['stock_date']));
        }

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('id_owner', $customerId);
        }

        $baseStockQuery = $this->db->get_compiled_select();
        $report = $this->db->select([
            'container_stocks.*',
            'work_order_containers.id_position',
            'ref_positions.position',
            'work_order_containers.seal',
            'work_order_containers.is_empty',
            'work_order_containers.status',
            'ref_positions.id_warehouse',
            'ref_warehouses.warehouse',
            'work_order_containers.description',
            'COALESCE(
                NULLIF(work_order_goods.goods_name, ""),
                booking_containers.description
            ) AS goods_name',
            'work_order_goods.quantity',
            'work_order_goods.unit',
            'ref_containers.id_shipping_line',
            'shipping_lines.name AS shipping_line_name',
            'container_tps.id AS id_tps',
            'container_tps.name AS tps_name',
            '(SELECT booking_statuses.document_status FROM booking_statuses 
              INNER JOIN (SELECT id_booking, MAX(booking_statuses.created_at) created_at
              FROM booking_statuses GROUP BY id_booking) b2 ON booking_statuses.id_booking = b2.id_booking
              AND booking_statuses.created_at = b2.created_at
              WHERE booking_statuses.id_booking = container_stocks.id_booking 
              LIMIT 1) AS "document_status"',
            '(SELECT booking_statuses.created_at FROM booking_statuses 
              INNER JOIN (SELECT id_booking, MAX(booking_statuses.created_at) created_at
              FROM booking_statuses GROUP BY id_booking) b2 ON booking_statuses.id_booking = b2.id_booking
              AND booking_statuses.created_at = b2.created_at
              WHERE booking_statuses.id_booking = container_stocks.id_booking 
              LIMIT 1) AS "document_status_date"',
            '(SELECT booking_extensions.value FROM booking_extensions 
              LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
              WHERE booking_extensions.id_booking = container_stocks.id_booking 
              AND ref_extension_fields.field_name = "NO_BC11"
              LIMIT 1) AS "no_bc11"',
            '(SELECT booking_extensions.value FROM booking_extensions 
              LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
              WHERE booking_extensions.id_booking = container_stocks.id_booking 
              AND ref_extension_fields.field_name = "TGL_BC11"
              LIMIT 1) AS "bc11_date"',
            '(SELECT booking_extensions.value FROM booking_extensions 
              LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
              WHERE booking_extensions.id_booking = container_stocks.id_booking  
              AND ref_extension_fields.field_name = "POS"
              LIMIT 1) AS "pos"',
            '(SELECT booking_extensions.value FROM booking_extensions 
              LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
              WHERE booking_extensions.id_booking = container_stocks.id_booking  
              AND ref_extension_fields.field_name = "NO_BL"
              LIMIT 1) AS "no_bl"',
            get_ext_field('NHP_NO', 'container_stocks.id_booking', 'no_nhp'),
            get_ext_field('NHP_TGL', 'container_stocks.id_booking', 'nhp_date'),
            get_ext_field('DOK_NO', 'container_stocks.id_booking', 'no_doc_kep'),
            get_ext_field('DOK_TGL', 'container_stocks.id_booking', 'doc_kep_date'),
            'outbounds.no_reference AS no_reference_out',
            'outbounds.reference_date AS reference_out_date',
        ])
            ->from('work_order_containers')
            ->join('work_orders', 'work_orders.id = work_order_containers.id_work_order')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('ref_positions', 'ref_positions.id = work_order_containers.id_position')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container')
            ->join('ref_people AS shipping_lines', 'shipping_lines.id = ref_containers.id_shipping_line')
            ->join('booking_containers', 'handlings.id_booking = booking_containers.id_booking
                AND work_order_containers.id_container = booking_containers.id_container', 'left')
            ->join("({$baseStockQuery}) AS container_stocks", 'work_order_containers.id = container_stocks.id_work_order_container', 'right')
            ->join("
                (SELECT id_work_order, id_work_order_container,
                    GROUP_CONCAT(ref_goods.name SEPARATOR '^^') AS goods_name,
                    GROUP_CONCAT(work_order_goods.quantity SEPARATOR '^^') AS quantity,
                    GROUP_CONCAT(ref_units.unit SEPARATOR '^^') AS unit
                    FROM work_order_goods
                    LEFT JOIN ref_goods ON work_order_goods.id_goods = ref_goods.id
                    LEFT JOIN ref_units ON work_order_goods.id_unit = ref_units.id
                    GROUP BY id_work_order, id_work_order_container
                ) AS work_order_goods",
                'work_order_goods.id_work_order_container = work_order_containers.id
                    AND work_order_goods.id_work_order = work_order_containers.id_work_order', 'left')
            ->join('
                (SELECT safe_conducts.*, id_container AS id_container_sf FROM safe_conducts
                LEFT JOIN safe_conduct_containers
                    ON safe_conducts.id = safe_conduct_containers.id_safe_conduct
                WHERE safe_conducts.is_deleted = FALSE
                ) AS safe_conducts',
                'safe_conducts.id_booking = container_stocks.id_booking
                    AND safe_conducts.id_container_sf = container_stocks.id_container', 'left')
            ->join('ref_people AS container_tps', 'container_tps.id = safe_conducts.id_source_warehouse', 'left')
            ->join('(SELECT id_booking, no_reference, reference_date FROM bookings) AS outbounds', 'outbounds.id_booking = container_stocks.id_booking', 'left');

        $report
            ->group_start()
            ->like('container_stocks.no_container', $search)
            ->or_like('container_stocks.container_size', $search)
            ->or_like('container_stocks.container_type', $search)
            ->or_like('vessel', $search)
            ->or_like('voyage', $search)
            ->or_like('container_stocks.owner_name', $search)
            ->or_like('position', $search)
            ->or_like('container_stocks.completed_at', $search)
            ->or_like('work_order_containers.seal', $search)
            ->or_like('goods_name', $search)
            ->or_like('work_order_goods.quantity', $search)
            ->or_like('unit', $search)
            ->or_like('shipping_lines.name', $search)
            ->or_like('container_tps.name', $search)
            ->group_end();

        if ($start < 0) {
            return $report->get()->result_array();
        }

        $finalStockQuery = $this->db->get_compiled_select();

        $reportTotal = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$finalStockQuery}) AS CI_count_all_results")->row_array()['numrows'];

        if (!empty($column)) {
            if ($column == 'no') $column = 'completed_at';
            $finalStockQuery .= ' ORDER BY `' . $column . '` ' . $sort;
        } else {
            $finalStockQuery .= ' ORDER BY completed_at DESC';
        }
        $reportData = $this->db->query($finalStockQuery . " LIMIT {$start}, {$length}")->result_array();

        foreach ($reportData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => get_url_param('draw', $this->input->post('draw')),
            "recordsTotal" => count($reportData),
            "recordsFiltered" => $reportTotal,
            "data" => $reportData
        ];

        return $pageData;
    }

    /**
     * Get base query tpp container activity.
     *
     * @param array $filters
     * @param bool $with_ext
     * @return mixed
     */
    public function getTppContainerActivity($filters = [], $with_ext = true)
    {
        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            $branchId = $filters['branch'];
        } else {
            $branchId = get_active_branch('id');
        }

        $handlingTypeId = null;
        $bookingFieldExtension = 'bookings.id';
        if (key_exists('category', $filters) && !empty($filters['category'])) {
            if ($filters['category'] == 'INBOUND') {
                $handlingTypeId = get_setting('default_inbound_handling');
                $bookingFieldExtension = 'bookings.id';
            } else {
                $handlingTypeId = get_setting('default_outbound_handling');
                $bookingFieldExtension = 'bookings.id_booking';
            }
        }

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $report = $this->db->select([
            'bookings.no_reference',
            'bookings.reference_date',
        ]);
        if ($with_ext) {
            $report->select(get_ext_field('NO_BC11', $bookingFieldExtension, 'no_bc11'));
            $report->select(get_ext_field('TGL_BC11', $bookingFieldExtension, 'bc11_date'));
        }
        $report->select([
            'bookings.vessel',
            'bookings.voyage',
        ]);
        if ($with_ext) {
            $report->select(get_ext_field('PELBASAL', $bookingFieldExtension, 'port_of_origin'));
            $report->select(get_ext_field('PELBTUJUAN', $bookingFieldExtension, 'port_of_destination'));
            $report->select(get_ext_field('ETA', $bookingFieldExtension, 'eta'));
            $report->select(get_ext_field('NO_BL', $bookingFieldExtension, 'bl'));
            $report->select(get_ext_field('BL_DATE', $bookingFieldExtension, 'bl_date'));
            $report->select(get_ext_field('NHP_NO', $bookingFieldExtension, 'no_nhp'));
            $report->select(get_ext_field('NHP_TGL', $bookingFieldExtension, 'nhp_date'));
            $report->select(get_ext_field('DOK_NO', $bookingFieldExtension, 'no_doc'));
            $report->select(get_ext_field('DOK_TGL', $bookingFieldExtension, 'doc_date'));
        }
        $report->select([
            'work_orders.completed_at AS inbound_date',
            'COALESCE("BCF") AS inbound_status',
            'ref_containers.no_container',
            'ref_containers.size AS container_size',
            'ref_containers.type AS container_type',
            'work_order_containers.status_danger AS cargo_class',
            'work_order_containers.seal',
            'work_order_containers.description AS goods_name',
            'customers.name AS consignee_name',
            'container_tps.id AS id_tps',
            'container_tps.no_person AS no_tps',
            'container_tps.name AS tps_name',
            'container_sl.id AS id_shipping_line',
            'container_sl.no_person AS no_shipping_line',
            'container_sl.name AS shipping_line_name',
            'booking_news.no_booking_news AS ba',
            'booking_news.booking_news_date AS ba_date',
            'IF(ref_booking_types.category = "OUTBOUND", work_orders.completed_at, "") AS outbound_date',
            'IF(ref_booking_types.category = "OUTBOUND", bookings.document_status, "") AS outbound_status',
            'booking_in.document_status'
        ])
            ->from('bookings')
            ->join('bookings AS booking_in', 'IFNULL(bookings.id_booking, bookings.id) = booking_in.id', 'left')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('booking_news_details', 'booking_news_details.id_booking = booking_in.id', 'left')
            ->join('booking_news', 'booking_news.id=booking_news_details.id_booking_news', 'left')
            ->join('safe_conducts', 'booking_in.id = safe_conducts.id_booking', 'left')
            ->join('work_orders', 'work_orders.id_safe_conduct = safe_conducts.id', 'left')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container', 'left')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('ref_people AS container_tps', 'container_tps.id = safe_conducts.id_source_warehouse', 'left')
            ->join('ref_people AS container_sl', 'container_sl.id = ref_containers.id_shipping_line', 'left')
            ->where('work_order_containers.id_container IS NOT NULL')
            ->group_start()
                ->where('ref_handling_types.multiplier_container!=', 0)
                ->or_where('ref_handling_types.multiplier_goods!=', 0)
            ->group_end();

        if (empty($filters)) {
            $report->order_by('completed_at', 'desc');
        } else {
            if (key_exists('category', $filters) && !empty($filters['category'])) {
                $report->where('handlings.id_handling_type', $handlingTypeId);
            }

            if (key_exists('branch_type', $filters) && !is_null($filters['branch_type'])) {
                $report->where('branch_type IS NOT NULL');
                $report->where('branch_type', $filters['branch_type']);
            }

            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $report->where_in('customers.id', $filters['owner']);
                } else {
                    $report->where('customers.id', $filters['owner']);
                }
            }

            if (key_exists('container', $filters) && !empty($filters['container'])) {
                if (is_array($filters['container'])) {
                    $report->where_in('work_order_containers.id_container', $filters['container']);
                } else {
                    $report->where('work_order_containers.id_container', $filters['container']);
                }
            }

            if (key_exists('size', $filters) && !empty($filters['size'])) {
                if (is_array($filters['size'])) {
                    $report->where_in('ref_containers.size', $filters['size']);
                } else {
                    $report->where('ref_containers.size', $filters['size']);
                }
            }

            if (key_exists('sort_by', $filters) && !empty($filters['sort_by'])) {
                $order = key_exists('order', $filters) ? $filters['order'] : 'asc';
                $report->order_by($filters['sort_by'], $order);
            } else {
                $report->order_by('work_orders.completed_at', 'desc');
            }

            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
                if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                    $report->where('DATE(' . $filters['date_type'] . ')>=', sql_date_format($filters['date_from'], false));
                }
                if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                    $report->where('DATE(' . $filters['date_type'] . ')<=', sql_date_format($filters['date_to'], false));
                }
            }

            if (key_exists('shipping_line_id', $filters) && !empty($filters['shipping_line_id'])) {
                $report->where('ref_containers.id_shipping_line', $filters['shipping_line_id']);
            }

            if (key_exists('tps_id', $filters) && !empty($filters['tps_id'])) {
                $report->where('safe_conducts.id_source_warehouse', $filters['tps_id']);
            }
        }

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        };

        if ($userType == 'EXTERNAL') {
            $report->where('customers.id', $customerId);
        }

        return $this->db->get()->result_array();
    }

    /**
     * Get shipping line stock.
     * @param null $shippingLineId
     * @param null $branchId
     * @param null $branchType
     * @return array
     */
    public function getShippingLineStock($shippingLineId = null, $branchId = null, $branchType = null)
    {
        $report = $this->db
            ->select([
                'IFNULL(bookings.id_booking, bookings.id) AS id_booking',
                'booking_in.no_reference',
                'booking_in.reference_date',
                get_ext_field('NO_BC11', 'booking_in.id', 'no_bc11'),
                get_ext_field('TGL_BC11', 'booking_in.id', 'bc11_date'),
                'booking_in.vessel',
                'booking_in.voyage',
                get_ext_field('PELBASAL', 'booking_in.id', 'port_of_origin'),
                get_ext_field('PELBTUJUAN', 'booking_in.id', 'port_of_destination'),
                get_ext_field('ETA', 'booking_in.id', 'eta'),
                get_ext_field('NO_BL', 'booking_in.id', 'bl'),
                get_ext_field('BL_DATE', 'booking_in.id', 'bl_date'),
                get_ext_field('NHP_NO', 'booking_in.id', 'no_nhp'),
                get_ext_field('NHP_TGL', 'booking_in.id', 'nhp_date'),
                get_ext_field('DOK_NO', 'booking_in.id', 'no_doc'),
                get_ext_field('DOK_TGL', 'booking_in.id', 'doc_date'),
                'MIN(work_orders.completed_at) AS inbound_date',
                'COALESCE("BCF") AS inbound_status',
                'customers.name AS consignee_name',
                'container_tps.id AS id_tps',
                'container_tps.no_person AS no_tps',
                'container_tps.name AS tps_name',
                'container_sl.id AS id_shipping_line',
                'container_sl.no_person AS no_shipping_line',
                'container_sl.name AS shipping_line_name',
                'booking_news.no_booking_news AS ba',
                'booking_news.booking_news_date AS ba_date',
                'booking_in.document_status',
                'ref_containers.no_container',
                'ref_containers.type AS container_type',
                'ref_containers.size AS container_size',
                'SUM(CAST(work_order_containers.quantity AS SIGNED) * multiplier_container) AS stock',
                'DATEDIFF(CURDATE(), MIN(work_orders.completed_at)) AS age',
                'MAX(work_order_containers.id) AS id_work_order_container'
            ])
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('bookings AS booking_in', 'booking_in.id = IFNULL(bookings.id_booking, bookings.id)')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container')
            ->join('ref_people AS customers', 'customers.id = work_order_containers.id_owner', 'left')
            ->join('ref_branches as branches', 'branches.id = bookings.id_branch')
            ->join('booking_news_details', 'booking_news_details.id_booking = booking_in.id', 'left')
            ->join('booking_news', 'booking_news.id = booking_news_details.id_booking_news', 'left')
            ->join('ref_people AS container_tps', 'container_tps.id = safe_conducts.id_source_warehouse', 'left')
            ->join('ref_people AS container_sl', 'container_sl.id = ref_containers.id_shipping_line', 'left')
            ->where([
                'bookings.is_deleted' => false,
                'handlings.status !=' => 'REJECTED',
                'handlings.is_deleted' => false,
                'work_orders.is_deleted' => false,
                'work_order_containers.is_deleted' => false,
                'ref_handling_types.is_deleted' => false,
                'customers.is_deleted' => false,
                'work_orders.status' => 'COMPLETED',
            ])
            ->group_by('customers.id, IFNULL(bookings.id_booking, bookings.id), booking_in.id, booking_in.no_reference, booking_in.reference_date, ref_containers.id') // , container_tps.id, booking_news.no_booking_news, booking_news.booking_news_date
            ->having('stock > 0');

        if (is_array($shippingLineId)) {
            if (isset($shippingLineId['tps']) && !empty($shippingLineId['tps'])) {
                $report->where('container_tps.id', $shippingLineId['tps']);
            }
        } else if (!empty($shippingLineId)) {
            $report->where('ref_containers.id_shipping_line', $shippingLineId);
        }

        if (!empty($branchId)) {
            $report->where('booking_in.id_branch', $branchId);
        }

        if (!empty($branchType) && !is_null($branchType)) {
            $report->where('branch_type IS NOT NULL');
            $report->where('branch_type', $branchType);
        }

        $baseStockQuery = $this->db->get_compiled_select();
        $report = $this->db->select([
            'container_stocks.*',
            'work_order_containers.description AS goods_name',
            'ref_positions.position',
            'work_order_containers.seal',
            'work_order_containers.is_empty',
            'work_order_containers.is_hold',
            'work_order_containers.status',
            'work_order_containers.status_danger AS cargo_class',
        ])
            ->from('work_order_containers')
            ->join('ref_positions', 'ref_positions.id = work_order_containers.id_position')
            ->join("({$baseStockQuery}) AS container_stocks", 'work_order_containers.id = container_stocks.id_work_order_container', 'right');

        return $report->get()->result_array();
    }

    /**
     * Get container growth.
     * @param null $branchId
     * @return array
     */
    public function getGrowthContainer($branchId = null)
    {
        $branchCondition = '';
        if (!empty($branchId)) {
            $branchCondition = 'AND bookings.id_branch = ' . $branchId;
        } else {
            $branchId = get_active_branch('id');
            if (!empty($branchId)) {
                $branchCondition = 'AND bookings.id_branch = ' . $branchId;
            }
        }

        $report = $this->db->select([
            'SUM(IF(DATEDIFF(CURDATE(), transaction_date) <= 365, quantity, 0)) AS growth',
            'SUM(IF(DATEDIFF(CURDATE(), transaction_date) BETWEEN 366 AND 731, quantity, 0)) AS slow_growth',
            'SUM(IF(DATEDIFF(CURDATE(), transaction_date) >= 731, quantity, 0)) AS no_growth',
            'SUM(IF(DATEDIFF(CURDATE(), transaction_date) <= 365, TIMESTAMPDIFF(DAY, transaction_date, NOW()), 0)) AS time_growth',
            'SUM(IF(DATEDIFF(CURDATE(), transaction_date) BETWEEN 366 AND 731, TIMESTAMPDIFF(DAY, transaction_date, NOW()), 0)) AS time_slow_growth',
            'SUM(IF(DATEDIFF(CURDATE(), transaction_date) >= 731, TIMESTAMPDIFF(DAY, transaction_date, NOW()), 0)) AS time_no_growth'
        ])
            ->from('(
                SELECT 
                    ref_people.name AS owner_name,
                    no_container,
                    ref_containers.type AS container_type,
                    ref_containers.size AS container_size,
                    SUM(multiplier_container * CAST(quantity AS SIGNED)) AS quantity,
                    MIN(completed_at) AS transaction_date
                FROM bookings
                INNER JOIN ref_people ON ref_people.id = bookings.id_customer
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                INNER JOIN ref_containers ON ref_containers.id = work_order_containers.id_container
                WHERE id_container IS NOT NULL AND work_orders.is_deleted = FALSE AND handlings.status = "APPROVED" ' . $branchCondition . '
                GROUP BY owner_name, no_container
                ) AS stocks
            ')
            ->where('stocks.quantity > 0');

        return $report->get()->row_array();
    }

    /**
     * Get report customs stock.
     * @param array $filters
     * @return mixed
     */
    public function getReportBTD($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        // collect default handling type
        $handlingTypeIdInbound = get_setting('default_inbound_handling');
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $handlingTypeIdPencacahanFCL = 14;
        $handlingTypeIdPencacahanFCL2 = 21;
        $handlingTypeIdPencacahanLCL = 20;

        // get data FCL
        $report = $this->db->select([
            'bookings.no_reference',
            'bookings.reference_date',
            'ref_containers.size AS container_size',
            'ref_containers.no_container',
            'customers.name AS owner_name',
            'COALESCE(
                NULLIF(work_order_goods.goods_name, ""), 
                booking_containers.description,
                NULLIF(work_order_containers.description, "")
            ) AS goods_name',
            'booking_news.no_sprint',
            'booking_news.sprint_date',
            'work_orders.completed_at AS inbound_date',
            'last_pencacahan_dates.pencacahan_date',
            'last_outbound_dates.outbound_date',
            'outbounds.no_reference AS no_reference_out',
            'outbounds.reference_date AS reference_out_date',
            'bookings.booking_date',
            'status_btd_lelang.no_doc AS kep_btd_lelang',
            'status_btd_lelang.doc_date AS kep_btd_date',
            'status_bmn.no_doc AS kep_bmn_lelang',
            'status_bmn.doc_date AS kep_bmn_date',
            'first_lelang.lelang_1 AS tanggal_lelang_1',
            'second_lelang.lelang_2 AS tanggal_lelang_2',
            '"" AS description',
        ])
            ->from('bookings')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('safe_conducts', 'safe_conducts.id_booking = bookings.id', 'left')
            ->join('work_orders', 'work_orders.id_safe_conduct = safe_conducts.id', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id', 'left')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container', 'left')
            ->join("booking_news_details", 'booking_news_details.id_booking = bookings.id', 'left')
            ->join("(SELECT * FROM booking_news WHERE type = 'WITHDRAWAL' ORDER BY booking_news_date DESC LIMIT 1) AS booking_news", 'booking_news.id = booking_news_details.id_booking_news', 'left')
            ->join("(
                SELECT bookings.id_booking AS id_booking_ref, id_container, MAX(completed_at) AS outbound_date 
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdOutbound}'
                GROUP BY bookings.id_booking, id_container
            ) AS last_outbound_dates", 'bookings.id = last_outbound_dates.id_booking_ref
                AND work_order_containers.id_container = last_outbound_dates.id_container', 'left')
            ->join("(
                SELECT bookings.id AS id_booking, id_container, MAX(completed_at) AS pencacahan_date 
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdPencacahanFCL}' OR id_handling_type = '{$handlingTypeIdPencacahanFCL2}'
                GROUP BY bookings.id, id_container
            ) AS last_pencacahan_dates", 'bookings.id = last_pencacahan_dates.id_booking
                AND work_order_containers.id_container = last_pencacahan_dates.id_container', 'left')
            ->join("
                (SELECT id_work_order, id_work_order_container,
                    GROUP_CONCAT(ref_goods.name SEPARATOR '^^') AS goods_name,
                    GROUP_CONCAT(work_order_goods.quantity SEPARATOR '^^') AS quantity,
                    GROUP_CONCAT(ref_units.unit SEPARATOR '^^') AS unit
                    FROM work_order_goods
                    LEFT JOIN ref_goods ON work_order_goods.id_goods = ref_goods.id
                    LEFT JOIN ref_units ON work_order_goods.id_unit = ref_units.id
                    GROUP BY id_work_order, id_work_order_container
                ) AS work_order_goods",
                'work_order_goods.id_work_order_container = work_order_containers.id
                    AND work_order_goods.id_work_order = work_orders.id', 'left')
            ->join('booking_containers', 'bookings.id = booking_containers.id_booking
                AND work_order_containers.id_container = booking_containers.id_container', 'left')
            ->join('bookings AS outbounds', 'outbounds.id_booking = bookings.id', 'left')
            ->join('(
	            SELECT DISTINCT id_booking, document_status, no_doc, doc_date 
	            FROM booking_statuses WHERE document_status = "BTD"
                ) AS status_btd', 'status_btd.id_booking = bookings.id')
            ->join('(
	            SELECT DISTINCT id_booking, document_status, no_doc, doc_date 
	            FROM booking_statuses WHERE document_status = "BTD LELANG 1"
                ) AS status_btd_lelang', 'status_btd_lelang.id_booking = bookings.id', 'left')
            ->join('(
	            SELECT DISTINCT id_booking, document_status, no_doc, doc_date 
	            FROM booking_statuses WHERE document_status = "BMN"
                ) AS status_bmn', 'status_bmn.id_booking = bookings.id', 'left')
            ->join('(
                SELECT id_booking, MIN(doc_date) AS lelang_1 FROM auctions 
                INNER JOIN auction_details ON auctions.id = auction_details.id_auction
                WHERE auctions.status = "APPROVED"
                GROUP BY id_booking
                ) AS first_lelang', 'first_lelang.id_booking = bookings.id', 'left')
            ->join('(
                SELECT id_booking, MAX(doc_date) AS lelang_2 FROM (
                    SELECT id_booking, doc_date FROM auctions 
                    INNER JOIN auction_details ON auctions.id = auction_details.id_auction
                    WHERE auctions.status = "APPROVED"
                    GROUP BY id_booking, id_auction
                    ORDER BY id_booking, doc_date DESC
                    ) AS lelang 
                GROUP BY id_booking
                ) AS second_lelang', 'second_lelang.id_booking = bookings.id AND second_lelang.lelang_2 > first_lelang.lelang_1', 'left')
            ->where('handlings.id_handling_type', $handlingTypeIdInbound)
            ->where('work_order_containers.id_container IS NOT NULL');

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }
        if ($userType == 'EXTERNAL') {
            $report->where('bookings.id_customer', $customerId);
        }
        $baseInContainerQuery = $this->db->get_compiled_select();

        // get data LCL
        $report = $this->db->select([
            'bookings.no_reference',
            'bookings.reference_date',
            '"LCL" AS container_size',
            '"" AS no_container',
            'customers.name AS owner_name',
            'CONCAT(ref_goods.name, " ", FORMAT(work_order_goods.quantity, 2, "id_ID"), " ", ref_units.unit) AS goods_name',
            'booking_news.no_sprint',
            'booking_news.sprint_date',
            'work_orders.completed_at AS inbound_date',
            'last_pencacahan_dates.pencacahan_date',
            'last_outbound_dates.outbound_date',
            'outbounds.no_reference AS no_reference_out',
            'outbounds.reference_date AS reference_out_date',
            'bookings.booking_date',
            'status_btd_lelang.no_doc AS kep_btd_lelang',
            'status_btd_lelang.doc_date AS kep_btd_date',
            'status_bmn.no_doc AS kep_bmn_lelang',
            'status_bmn.doc_date AS kep_bmn_date',
            'first_lelang.lelang_1 AS tanggal_lelang_1',
            'second_lelang.lelang_2 AS tanggal_lelang_2',
            '"" AS description',
        ])
            ->from('bookings')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('handlings', 'handlings.id_booking = bookings.id', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods', 'left')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit', 'left')
            ->join("booking_news_details", 'booking_news_details.id_booking = bookings.id', 'left')
            ->join("(SELECT * FROM booking_news WHERE type = 'WITHDRAWAL' ORDER BY booking_news_date DESC LIMIT 1) AS booking_news", 'booking_news.id = booking_news_details.id_booking_news', 'left')
            ->join("(
                SELECT bookings.id_booking AS id_booking_ref, id_goods, MAX(completed_at) AS outbound_date  
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdOutbound}'
                GROUP BY bookings.id_booking, id_goods
            ) AS last_outbound_dates", 'bookings.id = last_outbound_dates.id_booking_ref
                AND work_order_goods.id_goods = last_outbound_dates.id_goods', 'left')
            ->join("(
                SELECT bookings.id_booking AS id_booking_ref, id_goods, MAX(completed_at) AS pencacahan_date  
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdPencacahanLCL}'
                GROUP BY bookings.id_booking, id_goods
            ) AS last_pencacahan_dates", 'bookings.id = last_pencacahan_dates.id_booking_ref
                AND work_order_goods.id_goods = last_pencacahan_dates.id_goods', 'left')
            ->join('bookings AS outbounds', 'outbounds.id_booking = bookings.id', 'left')
            ->join('(
	            SELECT DISTINCT id_booking, document_status, no_doc, doc_date 
	            FROM booking_statuses WHERE document_status = "BTD"
                ) AS status_btd', 'status_btd.id_booking = bookings.id')
            ->join('(
	            SELECT DISTINCT id_booking, document_status, no_doc, doc_date 
	            FROM booking_statuses WHERE document_status = "BTD LELANG 1"
                ) AS status_btd_lelang', 'status_btd_lelang.id_booking = bookings.id', 'left')
            ->join('(
	            SELECT DISTINCT id_booking, document_status, no_doc, doc_date 
	            FROM booking_statuses WHERE document_status = "BMN"
                ) AS status_bmn', 'status_bmn.id_booking = bookings.id', 'left')
            ->join('(
                SELECT id_booking, MIN(doc_date) AS lelang_1 FROM auctions 
                INNER JOIN auction_details ON auctions.id = auction_details.id_auction
                GROUP BY id_booking
                ) AS first_lelang', 'first_lelang.id_booking = bookings.id', 'left')
            ->join('(
                SELECT id_booking, MAX(doc_date) AS lelang_2 FROM (
                    SELECT id_booking, doc_date FROM auctions 
                    INNER JOIN auction_details ON auctions.id = auction_details.id_auction
                    GROUP BY id_booking, id_auction
                    ORDER BY id_booking, doc_date DESC
                    ) AS lelang 
                GROUP BY id_booking
                ) AS second_lelang', 'second_lelang.id_booking = bookings.id AND second_lelang.lelang_2 > first_lelang.lelang_1', 'left')
            ->where('handlings.id_handling_type', $handlingTypeIdInbound)
            ->where('work_order_goods.id_goods IS NOT NULL');

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }
        if ($userType == 'EXTERNAL') {
            $report->where('bookings.id_customer', $customerId);
        }
        $baseInGoodsQuery = $this->db->get_compiled_select();

        $query = "SELECT * FROM (
              $baseInContainerQuery 
              UNION
              $baseInGoodsQuery
              ) AS data WHERE 1 = 1";

        if (!empty($filters)) {
            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $query .= ' AND DATE(inbound_date)<= "' . sql_date_format($filters['date_to'], false) . '"';
            }
        }

        $query .= " AND (
            no_reference LIKE '%{$search}%'
            OR reference_date LIKE '%{$search}%'
            OR inbound_date LIKE '%{$search}%'
            OR no_container LIKE '%{$search}%'
            OR container_size LIKE '%{$search}%'
            OR owner_name LIKE '%{$search}%'
            OR goods_name LIKE '%{$search}%'
            OR no_sprint LIKE '%{$search}%'
            OR inbound_date LIKE '%{$search}%'
            OR outbound_date LIKE '%{$search}%'
            OR pencacahan_date LIKE '%{$search}%'
            OR no_reference_out LIKE '%{$search}%'
            OR booking_date LIKE '%{$search}%'
        )";

        if ($start < 0) {
            return $this->db->query($query)->result_array();
        }

        $reportTotal = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$query}) AS CI_count_all_results")->row_array()['numrows'];

        if (!empty($column)) {
            if ($column == 'no') $column = 'inbound_date';
            $query .= ' ORDER BY `' . $column . '` ' . $sort;
        } else {
            $query .= ' ORDER BY inbound_date DESC';
        }
        $reportData = $this->db->query($query . " LIMIT {$start}, {$length}")->result_array();

        foreach ($reportData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => get_url_param('draw', $this->input->post('draw')),
            "recordsTotal" => count($reportData),
            "recordsFiltered" => $reportTotal,
            "data" => $reportData
        ];

        return $pageData;
    }

    /**
     * Get report customs stock.
     * @param array $filters
     * @return mixed
     */
    public function getReportBMN($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        // collect default handling type
        $handlingTypeIdInbound = get_setting('default_inbound_handling');
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $handlingTypeIdPencacahanFCL = 14;
        $handlingTypeIdPencacahanFCL2 = 21;
        $handlingTypeIdPencacahanLCL = 20;

        // get data FCL
        $report = $this->db->select([
            'status_bmn.no_doc AS no_bmn',
            'status_bmn.doc_date AS date_bmn',
            'ref_containers.size AS container_size',
            'ref_containers.no_container',
            'customers.name AS owner_name',
            'COALESCE(
                NULLIF(work_order_goods.goods_name, ""), 
                booking_containers.description,
                NULLIF(work_order_containers.description, "")
            ) AS goods_name',
            'booking_news.no_sprint',
            'booking_news.sprint_date',
            'work_orders.completed_at AS inbound_date',
            'last_pencacahan_dates.pencacahan_date',
            'last_outbound_dates.outbound_date',
            'outbounds.no_reference AS no_reference_out',
            'outbounds.reference_date AS reference_out_date',
            get_ext_field('DJKN_NO', 'bookings.id', 'no_djkn'),
            get_ext_field('DJKN_DATE', 'bookings.id', 'date_djkn'),
            'first_lelang.lelang_1 AS tanggal_lelang_1',
            'second_lelang.lelang_2 AS tanggal_lelang_2',
            '"" AS description',
        ])
            ->from('bookings')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('safe_conducts', 'safe_conducts.id_booking = bookings.id', 'left')
            ->join('work_orders', 'work_orders.id_safe_conduct = safe_conducts.id', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id', 'left')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container', 'left')
            ->join("booking_news_details", 'booking_news_details.id_booking = bookings.id', 'left')
            ->join("(SELECT * FROM booking_news WHERE type = 'WITHDRAWAL' ORDER BY booking_news_date DESC LIMIT 1) AS booking_news", 'booking_news.id = booking_news_details.id_booking_news', 'left')
            ->join("(
                SELECT bookings.id_booking AS id_booking_ref, id_container, MAX(completed_at) AS outbound_date 
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdOutbound}'
                GROUP BY bookings.id_booking, id_container
            ) AS last_outbound_dates", 'bookings.id = last_outbound_dates.id_booking_ref
                AND work_order_containers.id_container = last_outbound_dates.id_container', 'left')
            ->join("(
                SELECT bookings.id AS id_booking, id_container, MAX(completed_at) AS pencacahan_date 
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdPencacahanFCL}' OR id_handling_type = '{$handlingTypeIdPencacahanFCL2}'
                GROUP BY bookings.id, id_container
            ) AS last_pencacahan_dates", 'bookings.id = last_pencacahan_dates.id_booking
                AND work_order_containers.id_container = last_pencacahan_dates.id_container', 'left')
            ->join("
                (SELECT id_work_order, id_work_order_container,
                    GROUP_CONCAT(ref_goods.name SEPARATOR '^^') AS goods_name,
                    GROUP_CONCAT(work_order_goods.quantity SEPARATOR '^^') AS quantity,
                    GROUP_CONCAT(ref_units.unit SEPARATOR '^^') AS unit
                    FROM work_order_goods
                    LEFT JOIN ref_goods ON work_order_goods.id_goods = ref_goods.id
                    LEFT JOIN ref_units ON work_order_goods.id_unit = ref_units.id
                    GROUP BY id_work_order, id_work_order_container
                ) AS work_order_goods",
                'work_order_goods.id_work_order_container = work_order_containers.id
                    AND work_order_goods.id_work_order = work_orders.id', 'left')
            ->join('booking_containers', 'bookings.id = booking_containers.id_booking
                AND work_order_containers.id_container = booking_containers.id_container', 'left')
            ->join('bookings AS outbounds', 'outbounds.id_booking = bookings.id', 'left')
            ->join('(
	            SELECT DISTINCT id_booking, document_status, no_doc, doc_date 
	            FROM booking_statuses WHERE document_status = "BMN"
                ) AS status_bmn', 'status_bmn.id_booking = bookings.id')
            ->join('(
                SELECT id_booking, MIN(doc_date) AS lelang_1 FROM auctions 
                INNER JOIN auction_details ON auctions.id = auction_details.id_auction
                GROUP BY id_booking
                ) AS first_lelang', 'first_lelang.id_booking = bookings.id', 'left')
            ->join('(
                SELECT id_booking, MAX(doc_date) AS lelang_2 FROM (
                    SELECT id_booking, doc_date FROM auctions 
                    INNER JOIN auction_details ON auctions.id = auction_details.id_auction
                    GROUP BY id_booking, id_auction
                    ORDER BY id_booking, doc_date DESC
                    ) AS lelang 
                GROUP BY id_booking
                ) AS second_lelang', 'second_lelang.id_booking = bookings.id AND second_lelang.lelang_2 > first_lelang.lelang_1', 'left')
            ->where('handlings.id_handling_type', $handlingTypeIdInbound)
            ->where('work_order_containers.id_container IS NOT NULL');

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }
        if ($userType == 'EXTERNAL') {
            $report->where('customer.id_owner', $customerId);
        }
        $baseInContainerQuery = $this->db->get_compiled_select();

        // get data LCL
        $report = $this->db->select([
            'status_bmn.no_doc AS no_bmn',
            'status_bmn.doc_date AS date_bmn',
            '"LCL" AS container_size',
            '"" AS no_container',
            'customers.name AS owner_name',
            'CONCAT(ref_goods.name, " ", FORMAT(work_order_goods.quantity, 2, "id_ID"), " ", ref_units.unit) AS goods_name',
            'booking_news.no_sprint',
            'booking_news.sprint_date',
            'work_orders.completed_at AS inbound_date',
            'last_pencacahan_dates.pencacahan_date',
            'last_outbound_dates.outbound_date',
            'outbounds.no_reference AS no_reference_out',
            'outbounds.reference_date AS reference_out_date',
            get_ext_field('DJKN_NO', 'bookings.id', 'no_djkn'),
            get_ext_field('DJKN_DATE', 'bookings.id', 'date_djkn'),
            'first_lelang.lelang_1 AS tanggal_lelang_1',
            'second_lelang.lelang_2 AS tanggal_lelang_2',
            '"" AS description',
        ])
            ->from('bookings')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('handlings', 'handlings.id_booking = bookings.id', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods', 'left')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit', 'left')
            ->join("booking_news_details", 'booking_news_details.id_booking = bookings.id', 'left')
            ->join("(SELECT * FROM booking_news WHERE type = 'WITHDRAWAL' ORDER BY booking_news_date DESC LIMIT 1) AS booking_news", 'booking_news.id = booking_news_details.id_booking_news', 'left')
            ->join("(
                SELECT bookings.id_booking AS id_booking_ref, id_goods, MAX(completed_at) AS outbound_date  
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdOutbound}'
                GROUP BY bookings.id_booking, id_goods
            ) AS last_outbound_dates", 'bookings.id = last_outbound_dates.id_booking_ref
                AND work_order_goods.id_goods = last_outbound_dates.id_goods', 'left')
            ->join("(
                SELECT bookings.id_booking AS id_booking_ref, id_goods, MAX(completed_at) AS pencacahan_date  
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdPencacahanLCL}'
                GROUP BY bookings.id_booking, id_goods
            ) AS last_pencacahan_dates", 'bookings.id = last_pencacahan_dates.id_booking_ref
                AND work_order_goods.id_goods = last_pencacahan_dates.id_goods', 'left')
            ->join('bookings AS outbounds', 'outbounds.id_booking = bookings.id', 'left')
            ->join('(
	            SELECT DISTINCT id_booking, document_status, no_doc, doc_date 
	            FROM booking_statuses WHERE document_status = "BMN"
                ) AS status_bmn', 'status_bmn.id_booking = bookings.id')
            ->join('(
                SELECT id_booking, MIN(doc_date) AS lelang_1 FROM auctions 
                INNER JOIN auction_details ON auctions.id = auction_details.id_auction
                GROUP BY id_booking
                ) AS first_lelang', 'first_lelang.id_booking = bookings.id', 'left')
            ->join('(
                SELECT id_booking, MAX(doc_date) AS lelang_2 FROM (
                    SELECT id_booking, doc_date FROM auctions 
                    INNER JOIN auction_details ON auctions.id = auction_details.id_auction
                    GROUP BY id_booking, id_auction
                    ORDER BY id_booking, doc_date DESC
                    ) AS lelang 
                GROUP BY id_booking
                ) AS second_lelang', 'second_lelang.id_booking = bookings.id AND second_lelang.lelang_2 > first_lelang.lelang_1', 'left')
            ->where('handlings.id_handling_type', $handlingTypeIdInbound)
            ->where('work_order_goods.id_goods IS NOT NULL');

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }
        if ($userType == 'EXTERNAL') {
            $report->where('customers.id', $customerId);
        }
        $baseInGoodsQuery = $this->db->get_compiled_select();

        $query = "SELECT * FROM (
              $baseInContainerQuery 
              UNION
              $baseInGoodsQuery
              ) AS data WHERE 1 = 1";

        if (!empty($filters)) {
            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $query .= ' AND DATE(inbound_date)<= "' . sql_date_format($filters['date_to'], false) . '"';
            }
        }

        $query .= " AND (
            no_bmn LIKE '%{$search}%'
            OR date_bmn LIKE '%{$search}%'
            OR container_size LIKE '%{$search}%'
            OR no_container LIKE '%{$search}%'
            OR owner_name LIKE '%{$search}%'
            OR goods_name LIKE '%{$search}%'
            OR no_sprint LIKE '%{$search}%'
            OR inbound_date LIKE '%{$search}%'
            OR outbound_date LIKE '%{$search}%'
            OR pencacahan_date LIKE '%{$search}%'
            OR outbound_date LIKE '%{$search}%'
            OR no_reference_out LIKE '%{$search}%'
            OR no_djkn LIKE '%{$search}%'
            OR date_djkn LIKE '%{$search}%'
            OR tanggal_lelang_1 LIKE '%{$search}%'
            OR tanggal_lelang_2 LIKE '%{$search}%'
            OR description LIKE '%{$search}%'
        )";

        if ($start < 0) {
            return $this->db->query($query)->result_array();
        }

        $reportTotal = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$query}) AS CI_count_all_results")->row_array()['numrows'];

        if (!empty($column)) {
            if ($column == 'no') $column = 'inbound_date';
            $query .= ' ORDER BY `' . $column . '` ' . $sort;
        } else {
            $query .= ' ORDER BY inbound_date DESC';
        }
        $reportData = $this->db->query($query . " LIMIT {$start}, {$length}")->result_array();

        foreach ($reportData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => get_url_param('draw', $this->input->post('draw')),
            "recordsTotal" => count($reportData),
            "recordsFiltered" => $reportTotal,
            "data" => $reportData
        ];

        return $pageData;
    }

    /**
     * Get report customs stock.
     * @param array $filters
     * @return mixed
     */
    public function getReportBDN($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        // collect default handling type
        $handlingTypeIdInbound = get_setting('default_inbound_handling');
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $handlingTypeIdPencacahanFCL = 14;
        $handlingTypeIdPencacahanFCL2 = 21;
        $handlingTypeIdPencacahanLCL = 20;

        // get data FCL
        $report = $this->db->select([
            'status_bdn.no_doc AS no_bdn',
            'status_bdn.doc_date AS date_bdn',
            'ref_containers.size AS container_size',
            'ref_containers.no_container',
            'customers.name AS owner_name',
            'COALESCE(
                NULLIF(work_order_goods.goods_name, ""), 
                booking_containers.description,
                NULLIF(work_order_containers.description, "")
            ) AS goods_name',
            'booking_news.no_sprint',
            'booking_news.sprint_date',
            'work_orders.completed_at AS inbound_date',
            'last_pencacahan_dates.pencacahan_date',
            'last_outbound_dates.outbound_date',
            'outbounds.no_reference AS no_reference_out',
            'status_bmn.no_doc AS no_bmn',
            'status_bmn.doc_date AS date_bmn',
            '"" AS other_clearance_doc',
            '"" AS description',
        ])
            ->from('bookings')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('safe_conducts', 'safe_conducts.id_booking = bookings.id', 'left')
            ->join('work_orders', 'work_orders.id_safe_conduct = safe_conducts.id', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id', 'left')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container', 'left')
            ->join("booking_news_details", 'booking_news_details.id_booking = bookings.id', 'left')
            ->join("(SELECT * FROM booking_news WHERE type = 'WITHDRAWAL' ORDER BY booking_news_date DESC LIMIT 1) AS booking_news", 'booking_news.id = booking_news_details.id_booking_news', 'left')
            ->join("(
                SELECT bookings.id_booking AS id_booking_ref, id_container, MAX(completed_at) AS outbound_date 
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdOutbound}'
                GROUP BY bookings.id_booking, id_container
            ) AS last_outbound_dates", 'bookings.id = last_outbound_dates.id_booking_ref
                AND work_order_containers.id_container = last_outbound_dates.id_container', 'left')
            ->join("(
                SELECT bookings.id AS id_booking, id_container, MAX(completed_at) AS pencacahan_date 
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdPencacahanFCL}' OR id_handling_type = '{$handlingTypeIdPencacahanFCL2}'
                GROUP BY bookings.id, id_container
            ) AS last_pencacahan_dates", 'bookings.id = last_pencacahan_dates.id_booking
                AND work_order_containers.id_container = last_pencacahan_dates.id_container', 'left')
            ->join("
                (SELECT id_work_order, id_work_order_container,
                    GROUP_CONCAT(ref_goods.name SEPARATOR '^^') AS goods_name,
                    GROUP_CONCAT(work_order_goods.quantity SEPARATOR '^^') AS quantity,
                    GROUP_CONCAT(ref_units.unit SEPARATOR '^^') AS unit
                    FROM work_order_goods
                    LEFT JOIN ref_goods ON work_order_goods.id_goods = ref_goods.id
                    LEFT JOIN ref_units ON work_order_goods.id_unit = ref_units.id
                    GROUP BY id_work_order, id_work_order_container
                ) AS work_order_goods",
                'work_order_goods.id_work_order_container = work_order_containers.id_work_order_container
                    AND work_order_goods.id_work_order = work_orders.id', 'left')
            ->join('booking_containers', 'bookings.id = booking_containers.id_booking
                AND work_order_containers.id_container = booking_containers.id_container', 'left')
            ->join('bookings AS outbounds', 'outbounds.id_booking = bookings.id', 'left')
            ->join('(
	            SELECT DISTINCT id_booking, document_status, no_doc, doc_date 
	            FROM booking_statuses WHERE document_status = "BDN"
                ) AS status_bdn', 'status_bdn.id_booking = bookings.id')
            ->join('(
	            SELECT DISTINCT id_booking, document_status, no_doc, doc_date 
	            FROM booking_statuses WHERE document_status = "BMN"
                ) AS status_bmn', 'status_bmn.id_booking = bookings.id', 'left')
            ->where('handlings.id_handling_type', $handlingTypeIdInbound)
            ->where('work_order_containers.id_container IS NOT NULL');

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }
        if ($userType == 'EXTERNAL') {
            $report->where('bookings.id_customer', $customerId);
        }
        $baseInContainerQuery = $this->db->get_compiled_select();

        // get data LCL
        $report = $this->db->select([
            'status_bdn.no_doc AS no_bdn',
            'status_bdn.doc_date AS date_bdn',
            '"LCL" AS container_size',
            '"" AS no_container',
            'customers.name AS owner_name',
            'CONCAT(ref_goods.name, " ", FORMAT(work_order_goods.quantity, 2, "id_ID"), " ", ref_units.unit) AS goods_name',
            'booking_news.no_sprint',
            'booking_news.sprint_date',
            'work_orders.completed_at AS inbound_date',
            'last_pencacahan_dates.pencacahan_date',
            'last_outbound_dates.outbound_date',
            'outbounds.no_reference AS no_reference_out',
            'status_bmn.no_doc AS no_bmn',
            'status_bmn.doc_date AS date_bmn',
            '"" AS other_clearance_doc',
            '"" AS description',
        ])
            ->from('bookings')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('handlings', 'handlings.id_booking = bookings.id', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods', 'left')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit', 'left')
            ->join("booking_news_details", 'booking_news_details.id_booking = bookings.id', 'left')
            ->join("(SELECT * FROM booking_news WHERE type = 'WITHDRAWAL' ORDER BY booking_news_date DESC LIMIT 1) AS booking_news", 'booking_news.id = booking_news_details.id_booking_news', 'left')
            ->join("(
                SELECT bookings.id_booking AS id_booking_ref, id_goods, MAX(completed_at) AS outbound_date  
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdOutbound}'
                GROUP BY bookings.id_booking, id_goods
            ) AS last_outbound_dates", 'bookings.id = last_outbound_dates.id_booking_ref
                AND work_order_goods.id_goods = last_outbound_dates.id_goods', 'left')
            ->join("(
                SELECT bookings.id_booking AS id_booking_ref, id_goods, MAX(completed_at) AS pencacahan_date  
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdPencacahanLCL}'
                GROUP BY bookings.id_booking, id_goods
            ) AS last_pencacahan_dates", 'bookings.id = last_pencacahan_dates.id_booking_ref
                AND work_order_goods.id_goods = last_pencacahan_dates.id_goods', 'left')
            ->join('bookings AS outbounds', 'outbounds.id_booking = bookings.id', 'left')
            ->join('(
	            SELECT DISTINCT id_booking, document_status, no_doc, doc_date 
	            FROM booking_statuses WHERE document_status = "BDN"
                ) AS status_bdn', 'status_bdn.id_booking = bookings.id')
            ->join('(
	            SELECT DISTINCT id_booking, document_status, no_doc, doc_date 
	            FROM booking_statuses WHERE document_status = "BMN"
                ) AS status_bmn', 'status_bmn.id_booking = bookings.id', 'left')
            ->where('handlings.id_handling_type', $handlingTypeIdInbound)
            ->where('work_order_goods.id_goods IS NOT NULL');

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }
        if ($userType == 'EXTERNAL') {
            $report->where('customers.id', $customerId);
        }
        $baseInGoodsQuery = $this->db->get_compiled_select();

        $report = $this->db->from("(
              SELECT * FROM ({$baseInContainerQuery}) AS in_containers 
              UNION
              SELECT * FROM ({$baseInGoodsQuery}) AS in_goods
          ) AS data");

        if (!empty($filters)) {
            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $report->where('DATE(inbound_date)<=', sql_date_format($filters['date_to'], false));
            }
        }

        $report
            ->group_start()
            ->or_like('no_bdn', $search)
            ->or_like('date_bdn', $search)
            ->or_like('container_size', $search)
            ->or_like('no_container', $search)
            ->or_like('owner_name', $search)
            ->or_like('goods_name', $search)
            ->or_like('no_sprint', $search)
            ->or_like('sprint_date', $search)
            ->or_like('inbound_date', $search)
            ->or_like('pencacahan_date', $search)
            ->or_like('outbound_date', $search)
            ->or_like('no_reference_out', $search)
            ->or_like('no_bmn', $search)
            ->or_like('date_bmn', $search)
            ->or_like('other_clearance_doc', $search)
            ->or_like('description', $search)
            ->group_end();

        if ($start < 0) {
            return $report->get()->result_array();
        }

        $finalStockQuery = $this->db->get_compiled_select();

        $reportTotal = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$finalStockQuery}) AS CI_count_all_results")->row_array()['numrows'];

        if (!empty($column)) {
            if ($column == 'no') $column = 'inbound_date';
            $finalStockQuery .= ' ORDER BY `' . $column . '` ' . $sort;
        } else {
            $finalStockQuery .= ' ORDER BY inbound_date DESC';
        }
        $reportData = $this->db->query($finalStockQuery . " LIMIT {$start}, {$length}")->result_array();

        foreach ($reportData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => get_url_param('draw', $this->input->post('draw')),
            "recordsTotal" => count($reportData),
            "recordsFiltered" => $reportTotal,
            "data" => $reportData
        ];

        return $pageData;
    }


    /**
     * Get report customs stock.
     * @param array $filters
     * @return mixed
     */
    public function getReportTegahan($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        // collect default handling type
        $handlingTypeIdInbound = get_setting('default_inbound_handling');
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        // get data FCL
        $report = $this->db->select([
            'ref_booking_types.booking_type',
            'bookings.no_reference',
            'bookings.reference_date',
            get_ext_field('BA_SERAH_NO', 'bookings.id', 'no_ba_serah'),
            get_ext_field('BA_SERAH_TGL', 'bookings.id', 'ba_serah_date'),
            get_ext_field('BA_SEGEL_NO', 'bookings.id', 'no_ba_seal'),
            get_ext_field('BA_SEGEL_TGL', 'bookings.id', 'ba_seal_date'),
            'work_orders.completed_at AS inbound_date',
            '"FCL" as cargo_type',
            'ref_containers.size AS container_size',
            'ref_containers.no_container',
            'customers.name AS owner_name',
            'container_tps.name AS tps_name',
            'container_tps.region AS tps_region',
            'COALESCE(
                NULLIF(work_order_goods.goods_name, ""), 
                booking_containers.description,
                NULLIF(work_order_containers.description, "")
            ) AS goods_name',
            'work_order_containers.quantity',
            '"CARGO" AS unit',
            '(SELECT booking_statuses.document_status FROM booking_statuses 
              INNER JOIN (
                SELECT id_booking, MAX(booking_statuses.created_at) created_at
                FROM booking_statuses GROUP BY id_booking
              ) b2 ON booking_statuses.id_booking = b2.id_booking AND booking_statuses.created_at = b2.created_at
              WHERE booking_statuses.id_booking = bookings.id 
              LIMIT 1) AS "document_status"',
            'last_outbound_dates.outbound_date',
            get_ext_field('DOK_NO', 'bookings.id', 'no_doc_kep'),
            get_ext_field('DOK_TGL', 'bookings.id', 'doc_kep_date')
        ])
            ->from('bookings')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('handlings', 'handlings.id_booking = bookings.id', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id', 'left')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container', 'left')
            ->join("(
                SELECT bookings.id_booking AS id_booking_ref, id_container, MAX(completed_at) AS outbound_date  
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdOutbound}'
                GROUP BY bookings.id_booking, id_container
            ) AS last_outbound_dates", 'bookings.id = last_outbound_dates.id_booking_ref
                AND work_order_containers.id_container = last_outbound_dates.id_container', 'left')
            ->join('booking_containers', 'bookings.id = booking_containers.id_booking
                AND work_order_containers.id_container = booking_containers.id_container', 'left')
            ->join("
                (SELECT id_work_order, id_work_order_container,
                    GROUP_CONCAT(ref_goods.name SEPARATOR '^^') AS goods_name,
                    GROUP_CONCAT(work_order_goods.quantity SEPARATOR '^^') AS quantity,
                    GROUP_CONCAT(ref_units.unit SEPARATOR '^^') AS unit
                    FROM work_order_goods
                    LEFT JOIN ref_goods ON work_order_goods.id_goods = ref_goods.id
                    LEFT JOIN ref_units ON work_order_goods.id_unit = ref_units.id
                    GROUP BY id_work_order, id_work_order_container
                ) AS work_order_goods",
                'work_order_goods.id_work_order_container = work_order_containers.id
                    AND work_order_goods.id_work_order = work_orders.id', 'left')
            ->join('(
                SELECT safe_conducts.*, id_container AS id_container_sf FROM safe_conducts
                LEFT JOIN safe_conduct_containers
                    ON safe_conducts.id = safe_conduct_containers.id_safe_conduct
                WHERE safe_conducts.is_deleted = FALSE
                ) AS safe_conducts',
                'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('ref_people AS container_tps', 'container_tps.id = safe_conducts.id_source_warehouse', 'left')
            ->join('bookings AS outbounds', 'outbounds.id_booking = bookings.id', 'left')
            ->where('handlings.id_handling_type', $handlingTypeIdInbound)
            ->where('ref_booking_types.booking_type', 'TEGAHAN')
            ->where('work_order_containers.id_container IS NOT NULL');

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }
        if ($userType == 'EXTERNAL') {
            $report->where('customers.id', $customerId);
        }
        $baseInContainerQuery = $this->db->get_compiled_select();

        // get data LCL
        $report = $this->db->select([
            'ref_booking_types.booking_type',
            'bookings.no_reference',
            'bookings.reference_date',
            get_ext_field('BA_SERAH_NO', 'bookings.id', 'no_ba_serah'),
            get_ext_field('BA_SERAH_TGL', 'bookings.id', 'ba_serah_date'),
            get_ext_field('BA_SEGEL_NO', 'bookings.id', 'no_ba_seal'),
            get_ext_field('BA_SEGEL_TGL', 'bookings.id', 'ba_seal_date'),
            'work_orders.completed_at AS inbound_date',
            '"LCL" as cargo_type',
            '"" AS no_container',
            '"" AS container_size',
            'customers.name AS owner_name',
            'container_tps.name AS tps_name',
            'container_tps.region AS tps_region',
            'ref_goods.name AS goods_name',
            'work_order_goods.quantity',
            'ref_units.unit',
            '(SELECT booking_statuses.document_status FROM booking_statuses 
              INNER JOIN (
                SELECT id_booking, MAX(booking_statuses.created_at) created_at
                FROM booking_statuses GROUP BY id_booking
              ) b2 ON booking_statuses.id_booking = b2.id_booking AND booking_statuses.created_at = b2.created_at
              WHERE booking_statuses.id_booking = bookings.id 
              LIMIT 1) AS "document_status"',
            'last_outbound_dates.outbound_date',
            get_ext_field('DOK_NO', 'bookings.id', 'no_doc_kep'),
            get_ext_field('DOK_TGL', 'bookings.id', 'doc_kep_date'),
        ])
            ->from('bookings')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('handlings', 'handlings.id_booking = bookings.id', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods', 'left')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit', 'left')
            ->join("(
                SELECT bookings.id_booking AS id_booking_ref, id_goods, MAX(completed_at) AS outbound_date  
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdOutbound}'
                GROUP BY bookings.id_booking, id_goods
            ) AS last_outbound_dates", 'bookings.id = last_outbound_dates.id_booking_ref
                AND work_order_goods.id_goods = last_outbound_dates.id_goods', 'left')
            ->join("(
                SELECT bookings.id_booking AS id_booking_ref, id_goods, SUM(quantity) AS total 
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE id_handling_type = '{$handlingTypeIdOutbound}'
                GROUP BY bookings.id_booking, id_goods
            ) AS outbound_goods", 'bookings.id = outbound_goods.id_booking_ref
                AND work_order_goods.id_goods = outbound_goods.id_goods', 'left')
            ->join('(
                SELECT safe_conducts.*, id_goods FROM safe_conducts
                LEFT JOIN safe_conduct_goods
                    ON safe_conducts.id = safe_conduct_goods.id_safe_conduct
                WHERE safe_conducts.is_deleted = FALSE
                ) AS safe_conducts',
                'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('ref_people AS container_tps', 'container_tps.id = safe_conducts.id_source_warehouse', 'left')
            ->join('bookings AS outbounds', 'outbounds.id_booking = bookings.id', 'left')
            ->where('handlings.id_handling_type', $handlingTypeIdInbound)
            ->where('ref_booking_types.booking_type', 'TEGAHAN')
            ->where('work_order_goods.id_goods IS NOT NULL');

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }
        if ($userType == 'EXTERNAL') {
            $report->where('customers.id', $customerId);
        }
        $baseInGoodsQuery = $this->db->get_compiled_select();

        $report = $this->db->from("(
              SELECT * FROM ({$baseInContainerQuery}) AS in_containers 
              UNION 
              SELECT * FROM ({$baseInGoodsQuery}) AS in_goods
          ) AS data");

        if (!empty($filters)) {
            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $report->where('DATE(inbound_date)<=', sql_date_format($filters['date_to'], false));
            }
        }

        $report
            ->group_start()
            ->like('booking_type', $search)
            ->or_like('no_reference', $search)
            ->or_like('reference_date', $search)
            ->or_like('inbound_date', $search)
            ->or_like('no_container', $search)
            ->or_like('container_size', $search)
            ->or_like('owner_name', $search)
            ->or_like('tps_name', $search)
            ->or_like('tps_region', $search)
            ->or_like('quantity', $search)
            ->or_like('outbound_date', $search)
            ->group_end();

        if ($start < 0) {
            return $report->get()->result_array();
        }

        $finalStockQuery = $this->db->get_compiled_select();

        $reportTotal = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$finalStockQuery}) AS CI_count_all_results")->row_array()['numrows'];

        if (!empty($column)) {
            if ($column == 'no') $column = 'inbound_date';
            $finalStockQuery .= ' ORDER BY `' . $column . '` ' . $sort;
        } else {
            $finalStockQuery .= ' ORDER BY inbound_date DESC';
        }
        $reportData = $this->db->query($finalStockQuery . " LIMIT {$start}, {$length}")->result_array();

        foreach ($reportData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => get_url_param('draw', $this->input->post('draw')),
            "recordsTotal" => count($reportData),
            "recordsFiltered" => $reportTotal,
            "data" => $reportData
        ];

        return $pageData;
    }


    /**
     * Get stock summary container.
     *
     * @param array $filters
     * @return array|int
     */
    public function getStockContainers($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $columnOrder = [
            "stocks.owner_name",
            "stocks.owner_name",
            "stocks.no_reference",
            "stocks.reference_date",
            "stocks.no_nhp",
            "stocks.nhp_date",
            "stocks.no_bc11",
            "stocks.bc11_date",
            "stocks.no_ba_segel",
            "stocks.ba_segel_date",
            "stocks.no_kep",
            "stocks.kep_date",
            "stocks.no_bl",
            "stocks.bl_date",
            "stocks.document_status",
            "stocks.vessel",
            "stocks.voyage",
            "stocks.no_container",
            "stocks.container_type",
            "stocks.container_size",
            "stocks.stock",
            "ref_positions.position",
            "ref_warehouses.warehouse",
            "last_work_order_containers.seal",
            "last_work_order_containers.status",
            "last_work_order_containers.status_danger",
            "last_work_order_containers.is_empty",
            "last_work_order_containers.is_hold",
            "stocks.age",
            "stocks.inbound_date",
            "stocks.outbound_date",
            "stocks.lelang_date_1",
            "stocks.lelang_date_2",
            "stocks.lelang_date_3",
            "last_work_order_containers.description",
        ];
        $columnSort = $columnOrder[$column];

        $report = $this->db
            ->select([
                'ref_people.id AS id_owner',
                'ref_people.name AS owner_name',
                'IFNULL(bookings.id_booking, bookings.id) AS id_booking',
                'booking_inbounds.no_reference',
                'booking_inbounds.reference_date',
                'ref_containers.id AS id_container',
                get_ext_field('NHP_NO', 'booking_inbounds.id', 'no_nhp'),
                get_ext_field('NHP_TGL', 'booking_inbounds.id', 'nhp_date'),
                get_ext_field('NO_BC11', 'booking_inbounds.id', 'no_bc11'),
                get_ext_field('TGL_BC11', 'booking_inbounds.id', 'bc11_date'),
                get_ext_field('BA_SEGEL_NO', 'booking_inbounds.id', 'no_ba_segel'),
                get_ext_field('BA_SEGEL_TGL', 'booking_inbounds.id', 'ba_segel_date'),
                get_ext_field('DOK_NO', 'booking_inbounds.id', 'no_kep'),
                get_ext_field('DOK_TGL', 'booking_inbounds.id', 'kep_date'),
                get_ext_field('NO_BL', 'booking_inbounds.id', 'no_bl'),
                get_ext_field('TGL_BL', 'booking_inbounds.id', 'bl_date'),
                'booking_inbounds.document_status',
                'booking_inbounds.vessel',
                'booking_inbounds.voyage',
                'shipping_lines.name AS shipping_line',
                'ref_containers.no_container',
                'ref_containers.type AS container_type',
                'ref_containers.size AS container_size',
                'SUM(CAST(work_order_containers.quantity AS SIGNED) * multiplier_container) AS stock',
                'DATEDIFF(CURDATE(), MIN(completed_at)) AS age',
                'MIN(IFNULL(security_out_date, completed_at)) AS inbound_date',
                'MAX(completed_at) AS last_activity_date',
                'MAX(IF(ref_handling_types.multiplier_container = -1, completed_at, null)) AS outbound_date',
                'MAX(work_order_containers.id) AS id_work_order_container',
                "(SELECT booking_statuses.doc_date FROM booking_statuses 
                  WHERE booking_statuses.id_booking = booking_inbounds.id
                  AND (document_status = 'BTD LELANG 1' OR document_status = 'BMN LELANG 1')
                  LIMIT 1) AS 'lelang_date_1'",
                "(SELECT booking_statuses.doc_date FROM booking_statuses 
                  WHERE booking_statuses.id_booking = booking_inbounds.id
                  AND (document_status = 'BTN LELANG 2' OR document_status = 'BMN LELANG 2')
                  LIMIT 1) AS 'lelang_date_2'",
                "(SELECT booking_statuses.doc_date FROM booking_statuses 
                  WHERE booking_statuses.id_booking = booking_inbounds.id
                  AND document_status = 'BMN LELANG 3'
                  LIMIT 1) AS 'lelang_date_3'"
            ])
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(bookings.id_booking, bookings.id)')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container')
            ->join('ref_people', 'ref_people.id = work_order_containers.id_owner')
            ->join('ref_people AS shipping_lines', 'shipping_lines.id = ref_containers.id_shipping_line')
            ->where([
                'bookings.is_deleted' => false,
                'handlings.status !=' => 'REJECTED',
                'handlings.is_deleted' => false,
                'work_orders.is_deleted' => false,
                'work_order_containers.is_deleted' => false,
                'ref_handling_types.is_deleted' => false,
                'ref_people.is_deleted' => false,
            ])
            ->group_by('ref_people.id, IFNULL(bookings.id_booking, bookings.id), booking_inbounds.id, booking_inbounds.no_reference, booking_inbounds.reference_date, ref_containers.id, ref_containers.type, ref_containers.size');

        if (empty($filters) || !key_exists('data', $filters)) {
            $report->having('stock > 0');
        } else {
            if (key_exists('data', $filters) && !empty($filters['data'])) {
                if ($filters['data'] == 'stock') {
                    $report->having('stock > 0');
                }
            }

            if (key_exists('booking', $filters) && !empty($filters['booking'])) {
                if (is_array($filters['booking'])) {
                    $report->where_in('(IFNULL(bookings.id_booking, bookings.id))', $filters['booking']);
                } else {
                    $report->where('(IFNULL(bookings.id_booking, bookings.id))=', $filters['booking']);
                }
            }

            if (key_exists('container_size', $filters) && !empty($filters['container_size'])) {
                if (is_array($filters['container_size'])) {
                    $report->where_in('ref_containers.size', $filters['container_size']);
                } else {
                    $report->where('ref_containers.size', $filters['container_size']);
                }
            }

            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $report->where_in('ref_people.id', $filters['owner']);
                } else {
                    $report->where('ref_people.id', $filters['owner']);
                }
            }

            if (key_exists('age', $filters) && !empty($filters['age']) && $filters['age'] != 'ALL') {
                switch (urldecode($filters['age'])) {
                    case 'GROWTH':
                        $report->having('age>=', 0);
                        $report->having('age<=', 365);
                        break;
                    case 'SLOW GROWTH':
                        $report->having('age>=', 366);
                        $report->having('age<=', 730);
                        break;
                    case 'NO GROWTH':
                        $report->having('age>=', 731);
                        break;
                }
            }

            if (key_exists('stock_date', $filters) && !empty($filters['stock_date'])) {
                $report->where('DATE(completed_at) <=', sql_date_format($filters['stock_date'], false));
            }

            if (key_exists('out_date', $filters) && !empty($filters['out_date'])) {
                $report
                    ->having('stock > 0')
                    ->or_having('(outbound_date!=', null)
                    ->having('outbound_date>="' . sql_date_format($filters['out_date'], false) . '")');
            }
        }

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('ref_people.id', $customerId);
        }

        if (key_exists('total_size', $filters) && !empty($filters['total_size'])) {
            if ($filters['total_size'] != 'all') {
                $report->where('ref_containers.size', $filters['total_size']);
            }
            return $report->count_all_results();
        }

        $baseStockQuery = $this->db->get_compiled_select();
        $report = $this->db->select([
            'stocks.*',
            'ref_positions.id AS id_position',
            'ref_positions.position',
            'ref_warehouses.id AS id_warehouse',
            'ref_warehouses.warehouse',
            'ref_branches.id AS id_branch',
            'ref_branches.branch',
            'last_work_order_containers.seal',
            'last_work_order_containers.is_empty',
            'last_work_order_containers.is_hold',
            'last_work_order_containers.status',
            'last_work_order_containers.status_danger',
            'booking_containers.description',
        ])
            ->from("({$baseStockQuery}) AS stocks")
            ->join('booking_containers', 'booking_containers.id_booking = stocks.id_booking AND booking_containers.id_container = stocks.id_container', 'left')
            ->join('work_order_containers AS last_work_order_containers', 'last_work_order_containers.id = stocks.id_work_order_container', 'left')
            ->join('ref_positions', 'ref_positions.id = last_work_order_containers.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left')
            ->join('ref_branches', 'ref_branches.id = ref_warehouses.id_branch', 'left');

        if (key_exists('warehouse', $filters) && !empty($filters['warehouse'])) {
            if (is_array($filters['warehouse'])) {
                $report->where_in('ref_warehouses.id', $filters['warehouse']);
            } else {
                $report->where('ref_warehouses.id', $filters['warehouse']);
            }
        }

        $report
            ->group_start()
            ->or_like('stocks.owner_name', trim($search))
            ->or_like('stocks.no_reference', trim($search))
            ->or_like('stocks.no_container', trim($search))
            ->or_like('stocks.container_type', trim($search))
            ->or_like('stocks.container_size', trim($search))
            ->or_like('stocks.stock', trim($search))
            ->or_like('stocks.age', trim($search))
            ->or_like('stocks.inbound_date', trim($search))
            ->or_like('ref_positions.position', trim($search))
            ->or_like('ref_warehouses.warehouse', trim($search))
            ->or_like('ref_branches.branch', trim($search))
            ->or_like('last_work_order_containers.seal', trim($search))
            ->or_like('last_work_order_containers.status', trim($search))
            ->or_like('last_work_order_containers.status_danger', trim($search))
            ->or_like('last_work_order_containers.is_empty', trim($search))
            ->or_like('last_work_order_containers.is_hold', trim($search))
            ->or_like('last_work_order_containers.description', trim($search))
            ->group_end()
            ->order_by($columnSort, $sort);

        if ($start < 0) {
            return $report->get()->result_array();
        }

        $finalStockQuery = $this->db->get_compiled_select();

        $reportTotal = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$finalStockQuery}) AS CI_count_all_results")
            ->row_array()['numrows'];

        $reportData = $this->db->query($finalStockQuery . " LIMIT {$start}, {$length}")->result_array();

        foreach ($reportData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($reportData),
            "recordsFiltered" => $reportTotal,
            "data" => $reportData
        ];

        return $pageData;
    }
}
