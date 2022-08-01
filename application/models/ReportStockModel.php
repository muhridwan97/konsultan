<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportStockModel extends MY_Model
{
    /**
     * Get stock container summary.
     *
     * @param array $filters
     * @return array|null
     */
    public function getStockContainerSummary($filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $baseQuery = $this->db
            ->select([
                'SUM(work_order_containers.quantity * multiplier_container) AS total_all',
                'SUM(IF(ref_containers.size = 20, work_order_containers.quantity * multiplier_container, 0)) AS total_20',
                'SUM(IF(ref_containers.size = 40, work_order_containers.quantity * multiplier_container, 0)) AS total_40',
                'SUM(IF(ref_containers.size = 45, work_order_containers.quantity * multiplier_container, 0)) AS total_45',
            ])
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(work_order_containers.id_booking_reference, bookings.id)', 'left')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container')
            ->join('ref_people', 'ref_people.id = work_order_containers.id_owner')
            ->where([
                'bookings.is_deleted' => false,
                'handlings.status' => 'APPROVED',
                'handlings.is_deleted' => false,
                'ref_handling_types.multiplier_container <>' => 0,
                'work_orders.status' => 'COMPLETED',
                'work_orders.is_deleted' => false,
                'work_order_containers.is_deleted' => false,
                'ref_people.is_deleted' => false,
            ]);

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $baseQuery->where('id_owner', $customerId);
        }

        if (key_exists('owner', $filters) && !empty($filters['owner'])) {
            $baseQuery->where_in('id_owner', $filters['owner']);
        }

        if (key_exists('booking', $filters) && !empty($filters['booking'])) {
            $baseQuery->where_in('booking_inbounds.id', $filters['booking']);
        }

        if (key_exists('container_size', $filters) && !empty($filters['container_size'])) {
            $baseQuery->where_in('ref_containers.size', $filters['container_size']);
        }

        if (key_exists('stock_date', $filters) && !empty($filters['stock_date'])) {
            $baseQuery->where('completed_at <=', format_date($filters['stock_date'], 'Y-m-d') . ' 23:59:59');
        }

        return $baseQuery->get()->row_array();
    }

    /**
     * Get stock goods summary.
     *
     * @param array $filters
     * @return array|null
     */
    public function getStockGoodsSummary($filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $baseQuery = $this->db
            ->select([
                'SUM(work_order_goods.quantity * multiplier_goods) AS total_quantity',
                'SUM(work_order_goods.quantity * multiplier_goods * ref_goods.unit_weight) AS total_weight',
                'SUM(work_order_goods.quantity * multiplier_goods * ref_goods.unit_gross_weight) AS total_gross_weight',
                'SUM(work_order_goods.quantity * multiplier_goods * ref_goods.unit_volume) AS total_volume',
            ])
            ->from('work_orders')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(work_order_goods.id_booking_reference, bookings.id)')
            ->where([
                'bookings.is_deleted' => false,
                'booking_inbounds.is_deleted' => false,
                'handlings.status' => 'APPROVED',
                'handlings.is_deleted' => false,
                'work_orders.is_deleted' => false,
                'work_order_goods.is_deleted' => false,
                'work_orders.status' => 'COMPLETED',
            ]);

        if (!empty($branchId)) {
            $baseQuery->where('booking_inbounds.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $baseQuery->where('booking_inbounds.id_customer', $customerId);
        }

        if (key_exists('owner', $filters) && !empty($filters['owner'])) {
            $baseQuery->where_in('booking_inbounds.id_customer', $filters['owner']);
        }

        if (key_exists('booking', $filters) && !empty($filters['booking'])) {
            $baseQuery->where_in('booking_inbounds.id', $filters['booking']);
        }

        if (key_exists('stock_date', $filters) && !empty($filters['stock_date'])) {
            $baseQuery->where('completed_at <=', format_date($filters['stock_date'], 'Y-m-d') . ' 23:59:59');
        }

        return $baseQuery->get()->row_array();
    }

    public function getStockContainersOptimized($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = get_if_exist($filters, 'branch', get_active_branch_id());

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $baseStockQuery = $this->db
            ->select([
                'booking_inbounds.id_branch',
                'booking_inbounds.id_customer AS id_owner',
                'booking_inbounds.id AS id_booking',
                'booking_inbounds.id AS id_booking_reference',
                'work_order_containers.id_container',
                'SUM(work_order_containers.quantity * multiplier_container) AS stock',
                'DATEDIFF(CURDATE(), MIN(completed_at)) AS age',
                'MIN(IFNULL(security_out_date, completed_at)) AS inbound_date',
                'MAX(completed_at) AS last_activity_date',
                'MAX(work_order_containers.id) AS id_work_order_container'
            ])
            ->from('work_orders')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(work_order_containers.id_booking_reference, bookings.id)')
            ->where([
                'booking_inbounds.is_deleted' => false,
                'handlings.status' => 'APPROVED',
                'handlings.is_deleted' => false,
                'work_orders.is_deleted' => false,
                'work_order_containers.is_deleted' => false,
                'work_orders.status' => 'COMPLETED',
            ])
            ->group_start()
                ->where('ref_handling_types.multiplier_container <>', 0)
                ->or_where('handling_type', 'SHIFTING')
            ->group_end()
            ->group_by('work_order_containers.id_owner, booking_inbounds.id, work_order_containers.id_container');

        if (!empty($branchId)) {
            $baseStockQuery->where('booking_inbounds.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $baseStockQuery->where('booking_inbounds.id_customer', $customerId);
        }

        if (key_exists('data', $filters) && !empty($filters['data'])) {
            if ($filters['data'] == 'stock') {
                $baseStockQuery->having('stock > 0');
            }
        } else {
            $baseStockQuery->having('stock > 0');
        }

        if (key_exists('booking', $filters) && !empty($filters['booking'])) {
            if (is_array($filters['booking'])) {
                $baseStockQuery->where_in('booking_inbounds.id', $filters['booking']);
            } else {
                $baseStockQuery->where('booking_inbounds.id', $filters['booking']);
            }
        }

        if (key_exists('owner', $filters) && !empty($filters['owner'])) {
            if (is_array($filters['owner'])) {
                $baseStockQuery->where_in('booking_inbounds.id_customer', $filters['owner']);
            } else {
                $baseStockQuery->where('booking_inbounds.id_customer', $filters['owner']);
            }
        }

        if (key_exists('age', $filters) && !empty($filters['age']) && $filters['age'] != 'ALL') {
            switch (urldecode($filters['age'])) {
                case 'GROWTH':
                    $baseStockQuery->having('age>=', 0);
                    $baseStockQuery->having('age<=', 365);
                    break;
                case 'SLOW GROWTH':
                    $baseStockQuery->having('age>=', 366);
                    $baseStockQuery->having('age<=', 730);
                    break;
                case 'NO GROWTH':
                    $baseStockQuery->having('age>=', 731);
                    break;
            }
        }

        if (key_exists('stock_date', $filters) && !empty($filters['stock_date'])) {
            $baseStockQuery->where('completed_at <=', format_date($filters['stock_date']) . ' 23:59:59');
        }

        if (key_exists('except_work_order', $filters) && !empty($filters['except_work_order'])) {
            $baseStockQuery->where('work_orders.id !=', $filters['except_work_order']);
        }

        $compiledStockQuery = $this->db->get_compiled_select();

        $this->db->start_cache();

        $report = $this->db
            ->select([
                'stocks.*',
	            'ref_people.name AS owner_name',
	            'bookings.no_reference',
                'bookings.document_status',
                "(
                    SELECT upload_documents.subtype
                    FROM upload_documents
                    WHERE id_document_type = 22
                        AND upload_documents.is_deleted = FALSE
                        AND upload_documents.id_upload = bookings.id_upload
                    LIMIT 1
                ) AS do_subtype",
                "(
                    SELECT upload_documents.expired_date
                    FROM upload_documents
                    WHERE id_document_type = 22
                        AND upload_documents.is_deleted = FALSE
                        AND upload_documents.id_upload = bookings.id_upload
                    LIMIT 1
                ) AS expired_do_date",
                "(
                    SELECT upload_documents.freetime_date
                    FROM upload_documents
                    WHERE id_document_type = 22 
                        AND upload_documents.is_deleted = FALSE
                        AND upload_documents.id_upload = bookings.id_upload
                    LIMIT 1
                ) AS freetime_do_date",
                "(
                    SELECT source_warehouses.name FROM safe_conducts
                    INNER JOIN safe_conduct_containers ON safe_conduct_containers.id_safe_conduct = safe_conducts.id
                    INNER JOIN ref_people AS source_warehouses ON source_warehouses.id = safe_conducts.id_source_warehouse
                    WHERE safe_conducts.type = 'INBOUND' 
                        AND safe_conducts.id_booking = stocks.id_booking
                        AND safe_conduct_containers.id_container = stocks.id_container
                    LIMIT 1
                ) AS source_warehouse",
                'ref_containers.no_container',
                'ref_containers.size AS container_size',
                'ref_containers.type AS container_type',
                'ref_branches.branch',
                'ref_positions.id AS id_position',
                'ref_positions.position',
                '(
                    SELECT GROUP_CONCAT(DISTINCT work_order_container_positions.id_position_block) 
                    FROM work_order_container_positions
                    WHERE id_work_order_container = stocks.id_work_order_container
                ) AS id_position_blocks',
                '(
                    SELECT GROUP_CONCAT(DISTINCT ref_position_blocks.position_block) 
                    FROM work_order_container_positions
                    INNER JOIN ref_position_blocks ON ref_position_blocks.id = work_order_container_positions.id_position_block
                    WHERE id_work_order_container = stocks.id_work_order_container
                ) AS position_blocks',
                'ref_warehouses.id AS id_warehouse',
                'ref_warehouses.warehouse',
                'last_work_order_containers.seal',
                'last_work_order_containers.is_empty',
                'last_work_order_containers.is_hold',
                'last_work_order_containers.status',
                'last_work_order_containers.status_danger',
                'last_work_order_containers.description'
            ])
            ->from("({$compiledStockQuery}) AS stocks")
            ->join('work_order_containers AS last_work_order_containers', 'last_work_order_containers.id = stocks.id_work_order_container')
            ->join('ref_containers', 'ref_containers.id = stocks.id_container')
            ->join('ref_branches', 'ref_branches.id = stocks.id_branch')
            ->join('bookings', 'bookings.id = stocks.id_booking')
            ->join('ref_people', 'ref_people.id = stocks.id_owner')
            ->join('ref_positions', 'ref_positions.id = last_work_order_containers.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left');

        if (key_exists('container_size', $filters) && !empty($filters['container_size'])) {
            $report->where_in('ref_containers.size', $filters['container_size']);
        }

        if (key_exists('branch_type', $filters) && !is_null($filters['branch_type'])) {
            $report->where('branches.branch_type', $filters['branch_type']);
        }

        if (key_exists('warehouse', $filters) && !empty($filters['warehouse'])) {
            $report->where_in('ref_warehouses.id', $filters['warehouse']);
        }

        if (key_exists('source_warehouses', $filters) && !empty($filters['source_warehouses'])) {
            if (!is_array($filters['source_warehouses'])) {
                $filters['source_warehouses'] = [$filters['source_warehouses']];
            }
            $sourceWarehouseIds = implode(',', $filters['source_warehouses']);
            $report->where("EXISTS (
                SELECT safe_conducts.id FROM safe_conducts
                INNER JOIN safe_conduct_containers ON safe_conduct_containers.id_safe_conduct = safe_conducts.id
                WHERE safe_conducts.type = 'INBOUND' 
                    AND safe_conducts.id_source_warehouse IN ({$sourceWarehouseIds})
                    AND safe_conducts.id_booking = stocks.id_booking
                    AND safe_conduct_containers.id_container = stocks.id_container
            )");
        }

        if (!empty($search)) {
            $report
                ->group_start()
                ->or_like('ref_people.name', $search)
                ->or_like('bookings.no_reference', $search)
                ->or_like('bookings.no_booking', $search)
                ->or_like('bookings.document_status', $search)
                ->or_like('ref_containers.no_container', $search)
                ->or_like('ref_containers.size', $search)
                ->or_like('ref_containers.type', $search)
                ->or_like('ref_positions.position', $search)
                ->or_like('ref_warehouses.warehouse', $search)
                ->or_like('last_work_order_containers.seal', $search)
                ->or_like('last_work_order_containers.status', $search)
                ->or_like('last_work_order_containers.status_danger', $search)
                ->group_end();
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $report->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        if ($column == 'no') $column = 'bookings.no_reference';

        // counting result is slow, use simple pagination or cache to share result set like below
        $distinctQueryParams = filter_data_by_keys($filters, ['owner', 'booking', 'source_warehouse', 'warehouse', 'container_size', 'stock_date', 'age', 'search']);
        $cacheIdxKey = 'stock-container-page-count-' . $branchId . '-' . md5(json_encode($distinctQueryParams));
        $total = cache_remember($cacheIdxKey, 60 * 15, function() {
            return $this->db->count_all_results();
        });

        //$total = $this->db->count_all_results();
        $page = $report->order_by($column, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        $this->db->flush_cache();

        foreach ($data as &$row) {
            $row['no'] = ++$start;
        }

        return [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($data),
            "recordsFiltered" => $total,
            "data" => $data
        ];
    }

    public function getStockGoodsOptimized($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = get_if_exist($filters, 'branch', get_active_branch_id());

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $baseStockQuery = $this->db
            ->select([
                'booking_inbounds.id_branch',
                'booking_inbounds.id_customer AS id_owner',
                'booking_inbounds.id AS id_booking',
                'booking_inbounds.id AS id_booking_reference',
                'work_order_goods.id_goods',
                'work_order_goods.id_unit',
                'work_order_goods.ex_no_container',
                'SUM(work_order_goods.quantity * multiplier_goods) AS stock_quantity',
                'DATEDIFF(CURDATE(), MIN(completed_at)) AS age',
                'MIN(IFNULL(security_out_date, completed_at)) AS inbound_date',
                'MAX(completed_at) AS last_activity_date',
                'MAX(work_order_goods.id) AS id_work_order_goods'
            ])
            ->from('work_orders')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(work_order_goods.id_booking_reference, bookings.id)')
            ->where([
                'bookings.is_deleted' => false,
                'booking_inbounds.is_deleted' => false,
                'handlings.status' => 'APPROVED',
                'handlings.is_deleted' => false,
                'work_orders.is_deleted' => false,
                'work_order_goods.is_deleted' => false,
                'work_orders.status' => 'COMPLETED',
            ])
            ->group_start()
            ->where('ref_handling_types.multiplier_goods <>', 0)
            ->or_where('handling_type', 'SHIFTING')
            ->group_end()
            ->group_by('booking_inbounds.id, booking_inbounds.id_customer, id_goods, id_unit, ex_no_container');

        if (!empty($branchId)) {
            $baseStockQuery->where('booking_inbounds.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $baseStockQuery->where('booking_inbounds.id_customer', $customerId);
        }

        if (key_exists('data', $filters) && !empty($filters['data'])) {
            if ($filters['data'] == 'stock') {
                $baseStockQuery->having('stock_quantity > 0');
            }
        } else {
            $baseStockQuery->having('stock_quantity > 0');
        }

        if (key_exists('booking', $filters) && !empty($filters['booking'])) {
            if (is_array($filters['booking'])) {
                $baseStockQuery->where_in('booking_inbounds.id', $filters['booking']);
            } else {
                $baseStockQuery->where('booking_inbounds.id', $filters['booking']);
            }
        }

        if (key_exists('owner', $filters) && !empty($filters['owner'])) {
            if (is_array($filters['owner'])) {
                $baseStockQuery->where_in('booking_inbounds.id_customer', $filters['owner']);
            } else {
                $baseStockQuery->where('booking_inbounds.id_customer', $filters['owner']);
            }
        }

        if (key_exists('age', $filters) && !empty($filters['age']) && $filters['age'] != 'ALL') {
            switch (urldecode($filters['age'])) {
                case 'GROWTH':
                    $baseStockQuery->having('age>=', 0);
                    $baseStockQuery->having('age<=', 365);
                    break;
                case 'SLOW GROWTH':
                    $baseStockQuery->having('age>=', 366);
                    $baseStockQuery->having('age<=', 730);
                    break;
                case 'NO GROWTH':
                    $baseStockQuery->having('age>=', 731);
                    break;
            }
        }

        if (key_exists('stock_date', $filters) && !empty($filters['stock_date'])) {
            $baseStockQuery->where('completed_at <=', format_date($filters['stock_date']) . ' 23:59:59');
        }

        if (key_exists('except_work_order', $filters) && !empty($filters['except_work_order'])) {
            $baseStockQuery->where('work_orders.id !=', $filters['except_work_order']);
        }

        $compiledStockQuery = $this->db->get_compiled_select();

        $this->db->start_cache();

        $report = $this->db
            ->select([
                'stocks.*',
                'ref_people.name AS owner_name',
                'bookings.no_reference',
                'bookings.document_status',
                "(
                    SELECT upload_documents.no_document
                    FROM upload_documents
                    WHERE id_document_type = 3
                        AND upload_documents.is_deleted = FALSE
                        AND upload_documents.id_upload = bookings.id_upload
                    LIMIT 1
                ) AS no_invoice",
                "(
                    SELECT upload_documents.no_document
                    FROM upload_documents
                    WHERE id_document_type = 6
                        AND upload_documents.is_deleted = FALSE
                        AND upload_documents.id_upload = bookings.id_upload
                    LIMIT 1
                ) AS no_bl",
                'ref_goods.no_goods',
                'ref_goods.name AS goods_name',
                'ref_goods.id_assembly',
                "ref_assemblies.no_assembly",
                'ref_goods.whey_number',
                'ref_units.id AS id_unit',
                'ref_units.unit',
                'ref_goods.unit_weight',
                '(stocks.stock_quantity * ref_goods.unit_weight) AS stock_weight',
                'ref_goods.unit_gross_weight',
                '(stocks.stock_quantity * ref_goods.unit_gross_weight) AS stock_gross_weight',
                'ref_goods.unit_length',
                'ref_goods.unit_width',
                'ref_goods.unit_height',
                'ref_goods.unit_volume',
                '(stocks.stock_quantity * ref_goods.unit_volume) AS stock_volume',
                'ref_branches.branch',
                'ref_positions.id AS id_position',
                'ref_positions.position',
                '(
                    SELECT GROUP_CONCAT(DISTINCT work_order_goods_positions.id_position_block) 
                    FROM work_order_goods_positions
                    WHERE id_work_order_goods = stocks.id_work_order_goods
                ) AS id_position_blocks',
                '(
                    SELECT GROUP_CONCAT(DISTINCT ref_position_blocks.position_block) 
                    FROM work_order_goods_positions
                    INNER JOIN ref_position_blocks ON ref_position_blocks.id = work_order_goods_positions.id_position_block
                    WHERE id_work_order_goods = stocks.id_work_order_goods
                ) AS position_blocks',
                'ref_warehouses.id AS id_warehouse',
                'ref_warehouses.warehouse',
                'last_work_order_goods.no_pallet',
                'last_work_order_goods.is_hold',
                'last_work_order_goods.status',
                'last_work_order_goods.status_danger',
                'last_work_order_goods.description'
            ])
            ->from("({$compiledStockQuery}) AS stocks")
            ->join('work_order_goods AS last_work_order_goods', 'last_work_order_goods.id = stocks.id_work_order_goods')
            ->join('ref_goods', 'ref_goods.id = stocks.id_goods')
            ->join('ref_assemblies', 'ref_assemblies.id = ref_goods.id_assembly', 'left')
            ->join('ref_units', 'ref_units.id = stocks.id_unit')
            ->join('ref_branches', 'ref_branches.id = stocks.id_branch')
            ->join('bookings', 'bookings.id = stocks.id_booking')
            ->join('ref_people', 'ref_people.id = stocks.id_owner')
            ->join('ref_positions', 'ref_positions.id = last_work_order_goods.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left');

        if (key_exists('branch_type', $filters) && !is_null($filters['branch_type'])) {
            $report->where('branches.branch_type', $filters['branch_type']);
        }

        if (key_exists('warehouse', $filters) && !empty($filters['warehouse'])) {
            $report->where_in('ref_warehouses.id', $filters['warehouse']);
        }

        if (!empty($search)) {
            $report
                ->group_start()
                ->or_like('stocks.ex_no_container', $search)
                ->or_like('ref_people.name', $search)
                ->or_like('bookings.no_reference', $search)
                ->or_like('bookings.no_booking', $search)
                ->or_like('bookings.document_status', $search)
                ->or_like('ref_goods.name', $search)
                ->or_like('ref_goods.no_goods', $search)
                ->or_like('ref_goods.whey_number', $search)
                ->or_like('ref_positions.position', $search)
                ->or_like('ref_warehouses.warehouse', $search)
                ->or_like('last_work_order_goods.status', $search)
                ->or_like('last_work_order_goods.status_danger', $search)
                ->or_like('last_work_order_goods.no_pallet', $search)
                ->group_end();
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $report->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        if ($column == 'no') $column = 'ref_people.name';

        // counting result is slow, use simple pagination or cache to share result set like below
        $distinctQueryParams = filter_data_by_keys($filters, ['owner', 'booking', 'warehouse', 'stock_date', 'age', 'search']);
        $cacheIdxKey = 'stock-goods-page-count-' . $branchId . '-' . md5(json_encode($distinctQueryParams));
        $total = cache_remember($cacheIdxKey, 60 * 15, function() {
            return $this->db->count_all_results();
        });

        //$total = $this->db->count_all_results();
        $page = $report->order_by($column, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        $this->db->flush_cache();

        foreach ($data as &$row) {
            $row['no'] = ++$start;
        }

        return [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($data),
            "recordsFiltered" => $total,
            "data" => $data
        ];
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
        $quantity = key_exists('quantity', $filters) ? $filters['quantity'] : '';

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $columnOrder = [
            "stocks.owner_name",
            "stocks.owner_name",
            "stocks.no_reference",
            "stocks.no_container",
            "stocks.container_type",
            "stocks.container_size",
            "stocks.stock",
            "stocks.do_subtype",
            "stocks.expired_do_date",
            "stocks.freetime_do_date",
            "ref_positions.position",
            "ref_warehouses.warehouse",
            "source_warehouses.source_warehouse",
            "stocks.document_status",
            "last_work_order_containers.seal",
            "last_work_order_containers.status",
            "last_work_order_containers.status_danger",
            "last_work_order_containers.is_empty",
            "last_work_order_containers.is_hold",
            "stocks.age",
            "stocks.inbound_date",
            "last_work_order_containers.description",
        ];
        $columnSort = $columnOrder[$column];

        $report = $this->db
            ->select([
                'ref_people.id AS id_owner',
                'ref_people.name AS owner_name',
                //'IFNULL(bookings.id_booking, bookings.id) AS id_booking',
                'booking_inbounds.id AS id_booking',
                'booking_inbounds.id AS id_booking_reference',
                'booking_inbounds.no_reference',
                'booking_inbounds.document_status',
                'ref_containers.id AS id_container',
                'ref_containers.no_container',
                'ref_containers.type AS container_type',
                'ref_containers.size AS container_size',
                'do_documents.do_subtype',
                'do_documents.expired_do_date',
                'do_documents.freetime_do_date',
                'SUM(CAST(work_order_containers.quantity AS SIGNED) * multiplier_container) AS stock',
                'DATEDIFF(CURDATE(), MIN(completed_at)) AS age',
                'MIN(IFNULL(security_out_date, completed_at)) AS inbound_date',
                'MAX(completed_at) AS last_activity_date',
                'MAX(work_order_containers.id) AS id_work_order_container',
            ])
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(work_order_containers.id_booking_reference, bookings.id)', 'left')
            ->join('uploads', 'uploads.id = booking_inbounds.id_upload', 'left')
            ->join("(
                    SELECT 
                        upload_documents.id_upload,
                        upload_documents.subtype as do_subtype,                            
                        upload_documents.expired_date as expired_do_date,
                        upload_documents.freetime_date as freetime_do_date
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE ref_document_types.document_type = 'DO'
                ) AS do_documents", 'do_documents.id_upload = uploads.id', 'left')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container')
            ->join('ref_people', 'ref_people.id = work_order_containers.id_owner')
            ->join('ref_branches as branches', 'branches.id = bookings.id_branch')
            ->where([
                'bookings.is_deleted' => false,
                'handlings.status' => 'APPROVED',
                'handlings.is_deleted' => false,
                'ref_handling_types.multiplier_container <>' => 0,
                'work_orders.is_deleted' => false,
                'work_order_containers.is_deleted' => false,
                'ref_people.is_deleted' => false,
                'work_orders.status' => 'COMPLETED',
            ])
            ->group_by('ref_people.id, booking_inbounds.id, booking_inbounds.no_reference, booking_inbounds.document_status, do_documents.do_subtype, do_documents.expired_do_date, do_documents.freetime_do_date, ref_containers.id, ref_containers.type, ref_containers.size');

        if (empty($filters) || !key_exists('data', $filters)) {
            $report->having('stock > 0');
        } else {
            if (key_exists('data', $filters) && !empty($filters['data'])) {
                if ($filters['data'] == 'stock') {
                    $report->having('stock > 0');
                } else if ($filters['data'] == 'all' && !empty($quantity)) {
                    $report->having('stock != 0');
                }
            }

            if (key_exists('branch_type', $filters) && !is_null($filters['branch_type'])) {
                $report->where('branches.branch_type IS NOT NULL');
                $report->where('branches.branch_type', $filters['branch_type']);
            }

            if (key_exists('booking', $filters) && !empty($filters['booking'])) {
                if (is_array($filters['booking'])) {
                    $report->where_in('booking_inbounds.id', $filters['booking']);
                } else {
                    $report->where('booking_inbounds.id', $filters['booking']);
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

            if (key_exists('except_work_order', $filters) && !empty($filters['except_work_order'])) {
                $report->where('work_orders.id !=', $filters['except_work_order']);
            }
        }

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('ref_people.id', $customerId);
        }

        $baseStockQuery = $this->db->get_compiled_select();
        $report = $this->db->select([
            'stocks.*',
            'ref_positions.id AS id_position',
            'ref_positions.position',
            'position_blocks.id_position_blocks',
            'position_blocks.position_blocks',
            'ref_warehouses.id AS id_warehouse',
            'ref_warehouses.warehouse',
            'ref_branches.id AS id_branch',
            'ref_branches.branch',
            'source_warehouses.source_warehouse',
            'last_work_order_containers.seal',
            'last_work_order_containers.is_empty',
            'last_work_order_containers.is_hold',
            'last_work_order_containers.status',
            'last_work_order_containers.status_danger',
            'last_work_order_containers.description',
        ])
            ->from("({$baseStockQuery}) AS stocks")
            ->join("(
                SELECT 
                    id_work_order_container,
                    GROUP_CONCAT(ref_position_blocks.id) AS id_position_blocks,
                    GROUP_CONCAT(ref_position_blocks.position_block) AS position_blocks
                FROM ref_position_blocks
                INNER JOIN work_order_container_positions ON work_order_container_positions.id_position_block = ref_position_blocks.id
                GROUP BY work_order_container_positions.id_work_order_container              
            ) AS position_blocks", 'position_blocks.id_work_order_container = stocks.id_work_order_container', 'left')
            ->join('work_order_containers AS last_work_order_containers', 'last_work_order_containers.id = stocks.id_work_order_container', 'left')
            ->join('(
                SELECT DISTINCT source_warehouses.name AS source_warehouse, id_source_warehouse, id_container, id_booking FROM safe_conducts
                INNER JOIN ref_people AS source_warehouses ON source_warehouses.id = safe_conducts.id_source_warehouse
                INNER JOIN safe_conduct_containers ON safe_conduct_containers.id_safe_conduct = safe_conducts.id
                WHERE safe_conducts.type = "INBOUND"
            ) AS source_warehouses', 'source_warehouses.id_container = stocks.id_container AND source_warehouses.id_booking = stocks.id_booking', 'left')
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

        if (key_exists('source_warehouses', $filters) && !empty($filters['source_warehouses'])) {
            if (is_array($filters['source_warehouses'])) {
                $report->where_in('source_warehouses.id_source_warehouse', $filters['source_warehouses']);
            } else {
                $report->where('source_warehouses.id_source_warehouse', $filters['source_warehouses']);
            }
        }

        if (key_exists('total_size', $filters) && !empty($filters['total_size'])) {
            if ($filters['total_size'] != 'all') {
                $report->where('container_size', $filters['total_size']);
            }
            return $report->count_all_results();
        }

        if (!empty($search)) {
            $report
                ->group_start()
                ->or_like('stocks.owner_name', trim($search))
                ->or_like('stocks.no_reference', trim($search))
                ->or_like('stocks.no_container', trim($search))
                ->or_like('stocks.container_type', trim($search))
                ->or_like('stocks.container_size', trim($search))
                ->or_like('stocks.stock', trim($search))
                ->or_like('stocks.do_subtype', trim($search))
                ->or_like('stocks.document_status', trim($search))
                ->or_like('stocks.expired_do_date', trim($search))
                ->or_like('stocks.freetime_do_date', trim($search))
                ->or_like('stocks.age', trim($search))
                ->or_like('stocks.inbound_date', trim($search))
                ->or_like('ref_positions.position', trim($search))
                ->or_like('ref_warehouses.warehouse', trim($search))
                ->or_like('source_warehouses.source_warehouse', trim($search))
                ->or_like('ref_branches.branch', trim($search))
                ->or_like('last_work_order_containers.seal', trim($search))
                ->or_like('last_work_order_containers.status', trim($search))
                ->or_like('last_work_order_containers.status_danger', trim($search))
                ->or_like('last_work_order_containers.is_empty', trim($search))
                ->or_like('last_work_order_containers.is_hold', trim($search))
                ->or_like('last_work_order_containers.description', trim($search))
                ->group_end();
        }

        $report->order_by($columnSort, $sort);

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

    /**
     * Get stock summary goods.
     *
     * @param array $filters
     * @return array|int
     */
    public function getStockGoods($filters = [])
    {
        $column = get_if_exist($filters, 'order_by', 0);
        $sort = get_if_exist($filters, 'order_method', 'desc');
        $search = get_if_exist($filters, 'search', '');
        $length = get_if_exist($filters, 'length', 10);
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));
        $quantity = key_exists('quantity', $filters) ? $filters['quantity'] : '';

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $columnOrder = [
            "stocks.owner_name",
            "stocks.owner_name",
            "stocks.no_reference",
            "stocks.no_invoice",
            "stocks.no_bl",
            "stocks.no_goods",
            "stocks.goods_name",
            "ref_assemblies.no_assembly",
            "stocks.whey_number",
            "ref_positions.position",
            "ref_warehouses.warehouse",
            "last_work_order_goods.no_pallet",
            "stocks.stock_quantity",
            "stocks.unit",
            "stocks.unit_weight",
            "stocks.stock_weight",
            "stocks.unit_gross_weight",
            "stocks.stock_gross_weight",
            "stocks.unit_length",
            "stocks.unit_width",
            "stocks.unit_height",
            "stocks.unit_volume",
            "stocks.stock_volume",
            "last_work_order_goods.status",
            "last_work_order_goods.status_danger",
            "last_work_order_goods.is_hold",
            "stocks.ex_no_container",
            "stocks.age",
            "stocks.inbound_date",
            "last_work_order_goods.description",
        ];
        $columnSort = $columnOrder[$column];

        $defaultSelect = [
            'ref_people.id AS id_owner',
            'ref_people.name AS owner_name',
            //'IFNULL(bookings.id_booking, bookings.id) AS id_booking',
            'booking_inbounds.id AS id_booking',
            'booking_inbounds.id AS id_booking_reference',
            'booking_inbounds.no_reference',
            '(SELECT upload_documents.no_document FROM upload_documents 
              LEFT JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
              WHERE upload_documents.id_upload = booking_inbounds.id_upload
              AND ref_document_types.document_type = "Invoice"
              LIMIT 1) AS no_invoice',
            '(SELECT upload_documents.no_document FROM upload_documents 
              LEFT JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
              WHERE upload_documents.id_upload = booking_inbounds.id_upload
              AND ref_document_types.document_type = "Packing List"
              LIMIT 1) AS no_packing_list',
            '(SELECT upload_documents.no_document FROM upload_documents 
              LEFT JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
              WHERE upload_documents.id_upload = booking_inbounds.id_upload
              AND ref_document_types.document_type = "Bill Of Loading"
              LIMIT 1) AS no_bl',
            'ref_goods.id AS id_goods',
            'ref_goods.no_goods',
            'ref_goods.name AS goods_name',
            'ref_goods.id_assembly',
            'ref_goods.whey_number',
            'ref_units.id AS id_unit',
            'ref_units.unit',
            'SUM(quantity * multiplier_goods) AS stock_quantity',
            'ref_goods.unit_weight',
            'SUM(quantity * multiplier_goods) * ref_goods.unit_weight AS stock_weight',
            'ref_goods.unit_gross_weight',
            'SUM(quantity * multiplier_goods) * ref_goods.unit_gross_weight AS stock_gross_weight',
            'ref_goods.unit_length',
            'ref_goods.unit_width',
            'ref_goods.unit_height',
            'ref_goods.unit_volume',
            'SUM(quantity * multiplier_goods) * ref_goods.unit_volume AS stock_volume',
            'work_order_goods.ex_no_container',
            'DATEDIFF(CURDATE(), MIN(IFNULL(security_out_date, completed_at))) AS age',
            'MIN(IFNULL(security_out_date, completed_at)) AS inbound_date',
            'MAX(completed_at) AS last_activity_date',
            'MAX(work_order_goods.id) AS id_work_order_goods',
        ];

        $defaultGroup = 'ref_people.id, booking_inbounds.id, booking_inbounds.no_reference, booking_inbounds.id_upload, ref_goods.id, ref_units.id, ref_units.unit, ex_no_container';

        $defaultHaving = 'stock_quantity';
        if (key_exists('total_size', $filters) && !empty($filters['total_size'])) {
            if ($filters['total_size'] != 'all') {
                $countColumn = $filters['total_size'];
                if ($filters['total_size'] == 'unit_weight' || $filters['total_size'] == 'unit_volume') {
                    $countColumn = 'ref_goods.' . $filters['total_size'] . ' * quantity';
                }

                $defaultSelect = [
                    'SUM(' . $countColumn . ' * multiplier_goods) AS ' . $filters['total_size'] . '_total'
                ];
                $defaultGroup = '';
                $defaultHaving = $filters['total_size'] . '_total';
            }
        }

        $report = $this->db->select($defaultSelect)
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('booking_references', 'booking_references.id_booking = bookings.id', 'left')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(booking_references.id_booking_reference, bookings.id)', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id AND IFNULL(work_order_goods.id_booking_reference, bookings.id) = booking_inbounds.id')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit', 'left')
            ->join('ref_people', 'ref_people.id = work_order_goods.id_owner')
            ->join('ref_branches as branches', 'branches.id = bookings.id_branch')
            ->where([
                'bookings.is_deleted' => false,
                'handlings.status' => 'APPROVED',
                'handlings.is_deleted' => false,
                'work_orders.is_deleted' => false,
                'work_order_goods.is_deleted' => false,
                'ref_handling_types.is_deleted' => false,
                'ref_people.is_deleted' => false,
                'work_orders.status' => 'COMPLETED'
            ])
            ->group_by($defaultGroup);

        if (empty($filters) || !key_exists('data', $filters)) {
            $report->having($defaultHaving . ' > 0');
        } else {
            if (key_exists('data', $filters) && !empty($filters['data'])) {
                if ($filters['data'] == 'stock') {
                    $report->having($defaultHaving . ' > 0');
                } else if ($filters['data'] == 'all' && !empty($quantity)) {
                    $report->having($defaultHaving . ' != 0');
                }
            }

            if (key_exists('branch_type', $filters) && !is_null($filters['branch_type'])) {
                $report->where('branches.branch_type IS NOT NULL');
                $report->where('branches.branch_type', $filters['branch_type']);
            }

            if (key_exists('booking', $filters) && !empty($filters['booking'])) {
                if (is_array($filters['booking'])) {
                    $report->where_in('booking_inbounds.id', $filters['booking']);
                } else {
                    $report->where('booking_inbounds.id', $filters['booking']);
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

            if (key_exists('except_work_order', $filters) && !empty($filters['except_work_order'])) {
                $report->where('work_orders.id !=', $filters['except_work_order']);
            }
        }

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('ref_people.id', $customerId);
        }

        if (key_exists('total_size', $filters) && !empty($filters['total_size'])) {
            if ($filters['total_size'] == 'all') {
                return $report->count_all_results();
            } else {
                return $report->get()->row_array()[$filters['total_size'] . '_total'];
            }
        }
        if (key_exists('no_pallet', $filters) && !empty($filters['no_pallet'])) {
            $report->where('work_order_goods.no_pallet', $filters['no_pallet']);
        }

        $baseStockQuery = $this->db->get_compiled_select();
        $report = $this->db->select([
            'stocks.*',
            'last_work_order_goods.no_pallet',
            'ref_positions.id AS id_position',
            'ref_positions.position',
            'position_blocks.id_position_blocks',
            'position_blocks.position_blocks',
            'ref_assemblies.no_assembly',
            'ref_warehouses.id AS id_warehouse',
            'ref_warehouses.warehouse',
            'ref_branches.id AS id_branch',
            'ref_branches.branch',
            'last_work_order_goods.is_hold',
            'last_work_order_goods.status',
            'last_work_order_goods.status_danger',
            'last_work_order_goods.description',
        ])
            ->from("({$baseStockQuery}) AS stocks")
            ->join("(
                SELECT 
                    id_work_order_goods,
                    GROUP_CONCAT(ref_position_blocks.id) AS id_position_blocks,
                    GROUP_CONCAT(ref_position_blocks.position_block) AS position_blocks
                FROM ref_position_blocks
                INNER JOIN work_order_goods_positions ON work_order_goods_positions.id_position_block = ref_position_blocks.id
                GROUP BY work_order_goods_positions.id_work_order_goods              
            ) AS position_blocks", 'position_blocks.id_work_order_goods = stocks.id_work_order_goods', 'left')
            ->join('work_order_goods AS last_work_order_goods', 'last_work_order_goods.id = stocks.id_work_order_goods', 'left')
            ->join('ref_assemblies', 'ref_assemblies.id = stocks.id_assembly', 'left')
            ->join('ref_positions', 'ref_positions.id = last_work_order_goods.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left')
            ->join('ref_branches', 'ref_branches.id = ref_warehouses.id_branch', 'left');

        if (key_exists('warehouse', $filters) && !empty($filters['warehouse'])) {
            if (is_array($filters['warehouse'])) {
                $report->where_in('ref_warehouses.id', $filters['warehouse']);
            } else {
                $report->where('ref_warehouses.id', $filters['warehouse']);
            }
        }
        if (key_exists('position', $filters) && !empty($filters['position'])) {
            $report->where_in('ref_positions.id', $filters['position']);
        }

        if (!empty($search)) {
            $report
                ->group_start()
                ->or_like('stocks.owner_name', trim($search))
                ->or_like('stocks.goods_name', trim($search))
                ->or_like('stocks.no_reference', trim($search))
                ->or_like('stocks.no_packing_list', trim($search))
                ->or_like('stocks.no_invoice', trim($search))
                ->or_like('stocks.no_bl', trim($search))
                ->or_like('stocks.no_goods', trim($search))
                ->or_like('stocks.goods_name', trim($search))
                ->or_like('stocks.whey_number', trim($search))
                ->or_like('stocks.unit', trim($search))
                ->or_like('stocks.age', trim($search))
                ->or_like('stocks.inbound_date', trim($search))
                ->or_like('last_work_order_goods.no_pallet', trim($search))
                ->or_like('stocks.ex_no_container', trim($search))
                ->or_like('ref_positions.position', trim($search))
                ->or_like('ref_warehouses.warehouse', trim($search))
                ->or_like('ref_branches.branch', trim($search))
                ->or_like('stocks.unit', $search)
                ->or_like('stocks.stock_quantity', $search)
                ->or_like('stocks.unit_length', $search)
                ->or_like('stocks.unit_width', $search)
                ->or_like('stocks.unit_height', $search)
                ->or_like('stocks.unit_volume', $search)
                ->or_like('stocks.unit_weight', $search)
                ->or_like('stocks.unit_gross_weight', $search)
                ->or_like('last_work_order_goods.status', $search)
                ->or_like('last_work_order_goods.status_danger', $search)
                ->or_like('last_work_order_goods.description', $search)
                ->group_end();
        }

        $report->order_by($columnSort, $sort);

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

    /**
     * Get stock goods base query.
     *
     * @param array $filters
     * @return CI_DB_query_builder
     */
    public function getStockGoodsBaseQuery($filters = [])
    {
        $branchId = get_if_exist($filters, 'branch', get_active_branch_id());

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $baseQuery = $this->db
            ->select([
                'booking_inbounds.id_branch',
                'booking_inbounds.id AS id_booking',
                'work_order_goods.id_owner',
                'work_order_goods.id_goods',
                'work_order_goods.id_unit',
                'work_order_goods.ex_no_container',
                'SUM(work_order_goods.quantity * ref_handling_types.multiplier_goods) AS stock_quantity',
                'MAX(completed_at) AS last_activity_date',
                'MAX(work_order_goods.id) AS id_work_order_goods'
            ])
            ->from('work_orders')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(work_order_goods.id_booking_reference, bookings.id)')
            ->join('ref_people', 'ref_people.id = work_order_goods.id_owner')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods')
            ->where([
                'bookings.is_deleted' => false,
                'handlings.status' => 'APPROVED',
                'handlings.is_deleted' => false,
                'work_orders.status' => 'COMPLETED',
                'work_orders.is_deleted' => false,
                'work_order_goods.is_deleted' => false,
            ])
            ->group_by('booking_inbounds.id, id_owner, id_goods, id_unit, ex_no_container');

        if (key_exists('data', $filters) && !empty($filters['data'])) {
            switch ($filters['data']) {
                case 'all-data':
                case 'all':
                    break;
                case 'stocked':
                case 'stock-only':
                case 'stock':
                default:
                    $baseQuery->having('stock_quantity>', 0);
                    break;
                case 'empty-stock':
                    $baseQuery->having('stock_quantity', 0);
                    break;
                case 'negative-stock':
                    $baseQuery->having('stock_quantity<', 0);
                    break;
                case 'inactive-stock':
                    $baseQuery->having('stock_quantity<=', 0);
                    break;
            }
        } else {
            $baseQuery->having('stock_quantity>', 0);
        }

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $baseQuery->where('ref_people.id', $customerId);
        }

        if (key_exists('stock_date', $filters) && !empty($filters['stock_date'])) {
            if (strpos($filters['stock_date'], ':') === false) {
                $filters['stock_date'] = $filters['stock_date'] . ' 23:59:59';
            }
            $baseQuery->where('work_orders.completed_at<=', $filters['stock_date']);
        }

        if (key_exists('booking', $filters) && !empty($filters['booking'])) {
            $baseQuery->where_in('booking_inbounds.id', $filters['booking']);
        }

        if (key_exists('owner', $filters) && !empty($filters['owner'])) {
            $baseQuery->where_in('work_order_goods.id_owner', $filters['owner']);
        }

        if (key_exists('search', $filters) && !empty($filters['search'])) {
            $search = trim($filters['search']);
            $baseQuery
                ->group_start()
                ->or_like('ref_people.name', $search)
                ->or_like('ref_goods.name', $search)
                ->or_like('ref_goods.no_goods', $search)
                ->or_like('ref_goods.whey_number', $search)
                ->or_like('work_order_goods.ex_no_container', $search)
                ->or_like('work_order_goods.no_pallet', $search)
                ->or_like('booking_inbounds.no_reference', $search)
                ->or_like('booking_inbounds.no_booking', $search)
                ->group_end();
        }

        return $baseQuery;
    }

    /**
     * @param $bookingId
     * @param bool $stockOnly
     * @return array|array[]
     */
    public function getStockGoodsBooking($bookingId, $stockOnly = true)
    {
        $baseQuery = $this->db
            ->select([
                'bookings.id_branch',
                'ref_branches.branch',
                'bookings.id AS id_booking',
                'bookings.id_customer',
                'ref_people.name AS customer_name',
                'work_order_goods.id_goods',
                'ref_goods.no_goods',
                'ref_goods.whey_number',
                'ref_goods.name AS goods_name',
                'ref_goods.id_goods_parent',
                'packages.name AS package_name',
                'work_order_goods.id_unit',
                'ref_units.unit',
                //'work_order_goods.ex_no_container',
                'SUM(work_order_goods.quantity * ref_handling_types.multiplier_goods) AS stock_quantity',
                'ref_goods.unit_weight',
                'SUM(work_order_goods.quantity * ref_handling_types.multiplier_goods) * ref_goods.unit_gross_weight AS total_weight',
                'ref_goods.unit_gross_weight',
                'SUM(work_order_goods.quantity * ref_handling_types.multiplier_goods) * ref_goods.unit_gross_weight AS total_gross_weight',
                'ref_goods.unit_length',
                'ref_goods.unit_width',
                'ref_goods.unit_height',
                'ref_goods.unit_volume',
            ])
            ->from('work_orders')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
            ->join('ref_people', 'ref_people.id = bookings.id_customer')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods')
            ->join('ref_goods AS packages', 'packages.id = ref_goods.id_goods_parent', 'left')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit')
            ->where([
                'bookings.id' => $bookingId,
                'bookings.is_deleted' => false,
                'handlings.status' => 'APPROVED',
                'handlings.is_deleted' => false,
                'work_orders.status' => 'COMPLETED',
                'work_orders.is_deleted' => false,
                'work_order_goods.is_deleted' => false,
            ])
            //->group_by('bookings.id, work_order_goods.id_goods, work_order_goods.id_unit, work_order_goods.ex_no_container')
            ->group_by('bookings.id, work_order_goods.id_goods, work_order_goods.id_unit');

        if ($stockOnly) {
            $baseQuery->having('stock_quantity>', 0);
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get stock goods booking simple.
     *
     * @param $bookingId
     * @param bool $stockOnly
     * @return array|array[]
     */
    public function getStockGoodsBookingSimple($bookingId, $stockOnly = true)
    {
        $baseQuery = $this->db
            ->select([
                'stocks.id_booking',
                'bookings.no_reference',
                'stocks.id_goods',
                'ref_goods.no_goods',
                'ref_goods.name As goods_name',
                'stocks.id_unit',
                'ref_units.unit',
                'stocks.ex_no_container',
                'SUM(stocks.quantity) AS stock_quantity'
            ])
            ->from("(
                SELECT
                    work_order_goods.id_booking_reference AS id_booking,
                    id_goods,
                    id_unit,
                    ex_no_container,
                    (ref_handling_types.multiplier_goods * quantity) AS quantity
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE work_orders.status = 'COMPLETED'
                    AND handlings.status = 'APPROVED'
                    AND work_orders.is_deleted = FALSE
                    AND handlings.is_deleted = FALSE
                    AND work_order_goods.id_booking_reference = {$bookingId}
            
                UNION ALL
            
                SELECT
                    bookings.id AS id_booking,
                    id_goods,
                    id_unit,
                    ex_no_container,
                    (ref_handling_types.multiplier_goods * quantity) AS quantity
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE work_orders.status = 'COMPLETED'
                    AND handlings.status = 'APPROVED'
                    AND work_orders.is_deleted = FALSE
                    AND handlings.is_deleted = FALSE
                    AND bookings.id = {$bookingId}
            ) AS stocks")
            ->join('bookings', 'bookings.id = stocks.id_booking')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer')
            ->join('ref_goods', 'ref_goods.id = stocks.id_goods')
            ->join('ref_units', 'ref_units.id = stocks.id_unit')
            ->group_by('id_booking, id_goods, id_unit, ex_no_container');

        if ($stockOnly) {
            $baseQuery->having('stock_quantity>', 0);
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get minimal stock position.
     *
     * @param array $filters
     * @return array|array[]|mixed|object
     */
    public function getStockPosition($filters = [])
    {
        $baseStockQuery = $this->getStockGoodsBaseQuery($filters)->get_compiled_select();
        $baseQuery = $this->db->select([
            'SUM(stocks.stock_quantity) stock_quantity',
            'ref_positions.id AS id_position',
            'ref_positions.position',
            'ref_warehouses.id AS id_warehouse',
            'ref_warehouses.warehouse',
        ])
            ->from("({$baseStockQuery}) AS stocks")
            ->join('work_order_goods AS last_work_order_goods', 'last_work_order_goods.id = stocks.id_work_order_goods', 'left')
            ->join('ref_positions', 'ref_positions.id = last_work_order_goods.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left')
            ->where('ref_positions.id IS NOT NULL')
            ->group_by('ref_positions.id');

        if (key_exists('warehouse', $filters) && !empty($filters['warehouse'])) {
            $baseQuery->where_in('ref_warehouses.id', $filters['warehouse']);
        }


        if (key_exists('_result', $filters) && $filters['_result']) {
            return $baseQuery->get()->unbuffered_row();
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get stock summary goods.
     *
     * @param array $filters
     * @return array|int
     */
    public function getStockAssemblyGoods($filters = [])
    {
        $column = get_if_exist($filters, 'order_by', 0);
        $sort = get_if_exist($filters, 'order_method', 'desc');
        $search = get_if_exist($filters, 'search', '');
        $length = get_if_exist($filters, 'length', 10);
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));
        $quantity = key_exists('quantity', $filters) ? $filters['quantity'] : '';

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $columnOrder = [
            "stocks.owner_name",
            "stocks.owner_name",
            "stocks.no_reference",
            "stocks.no_invoice",
            "stocks.no_bl",
            "stocks.no_goods",
            "stocks.goods_name",
            "stocks.no_assembly",
            "stocks.whey_number",
            "ref_positions.position",
            "ref_warehouses.warehouse",
            "stocks.no_pallet",
            "stocks.no_delivery_order",
            "stocks.stock_quantity",
            "stocks.unit",
            "stocks.stock_tonnage",
            "stocks.stock_tonnage_gross",
            "stocks.stock_length",
            "stocks.stock_width",
            "stocks.stock_height",
            "stocks.stock_volume",
            "last_work_order_goods.status",
            "last_work_order_goods.status_danger",
            "last_work_order_goods.is_hold",
            "stocks.no_container",
            "stocks.age",
            "stocks.inbound_date",
            "last_work_order_goods.description",
        ];
        $columnSort = $columnOrder[$column];

        $defaultSelect = [
            'ref_people.id AS id_owner',
            'ref_people.name AS owner_name',
            'IFNULL(bookings.id_booking, bookings.id) AS id_booking',
            'booking_inbounds.no_reference',
            '(SELECT upload_documents.no_document FROM upload_documents 
              LEFT JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
              WHERE upload_documents.id_upload = booking_inbounds.id_upload
              AND ref_document_types.document_type = "Invoice"
              LIMIT 1) AS no_invoice',
            '(SELECT upload_documents.no_document FROM upload_documents 
              LEFT JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
              WHERE upload_documents.id_upload = booking_inbounds.id_upload
              AND ref_document_types.document_type = "Packing List"
              LIMIT 1) AS no_packing_list',
            '(SELECT upload_documents.no_document FROM upload_documents 
              LEFT JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
              WHERE upload_documents.id_upload = booking_inbounds.id_upload
              AND ref_document_types.document_type = "Bill Of Loading"
              LIMIT 1) AS no_bl',
            'ref_goods.id AS id_goods',
            'ref_goods.no_goods',
            'ref_goods.name AS goods_name',
            'ref_assemblies.no_assembly',
            'ref_goods.whey_number',
            'ref_units.id AS id_unit',
            'ref_units.unit',
            'SUM(quantity * multiplier_goods) AS stock_quantity',
            'SUM(tonnage * multiplier_goods) AS stock_tonnage',
            'SUM(tonnage_gross * multiplier_goods) AS stock_tonnage_gross',
            'SUM(work_order_goods.length * multiplier_goods) AS stock_length',
            'work_order_goods.width AS stock_width',
            'work_order_goods.height AS stock_height',
            'SUM(work_order_goods.volume * multiplier_goods) AS stock_volume',
            'work_order_goods.no_pallet',
            'work_order_goods.ex_no_container',
            'DATEDIFF(CURDATE(), MIN(completed_at)) AS age',
            'MIN(IFNULL(security_out_date, completed_at)) AS inbound_date',
            'MAX(completed_at) AS last_activity_date',
            '"" AS no_delivery_order',
            'MAX(work_order_goods.id) AS id_work_order_goods',
        ];

        $defaultGroup = 'ref_people.id, IFNULL(bookings.id_booking, bookings.id), ref_goods.id, ref_units.unit, width, height, IFNULL(ex_no_container, "")';
        $defaultHaving = 'stock_quantity';
        if (key_exists('total_size', $filters) && !empty($filters['total_size'])) {
            if ($filters['total_size'] != 'all') {
                $defaultSelect = [
                    'SUM(' . $filters['total_size'] . ' * multiplier_goods) AS ' . $filters['total_size'] . '_total'
                ];
                $defaultGroup = '';
                $defaultHaving = $filters['total_size'] . '_total';
            }
        }

        $report = $this->db->select($defaultSelect)
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(bookings.id_booking, bookings.id)')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods')
            ->join('ref_assemblies', 'ref_assemblies.id = ref_goods.id_assembly')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit', 'left')
            ->join('ref_people', 'ref_people.id = work_order_goods.id_owner')
            ->where([
                'bookings.is_deleted' => false,
                'handlings.status' => 'APPROVED',
                'handlings.is_deleted' => false,
                'work_orders.is_deleted' => false,
                'work_order_goods.is_deleted' => false,
                'ref_handling_types.is_deleted' => false,
                'ref_people.is_deleted' => false,
                'work_orders.status' => 'COMPLETED'
            ])
            ->group_by($defaultGroup);

        if (empty($filters) || !key_exists('data', $filters)) {
            $report->having($defaultHaving . ' > 0');
        } else {
            if (key_exists('data', $filters) && !empty($filters['data'])) {
                if ($filters['data'] == 'stock') {
                    $report->having($defaultHaving . ' > 0');
                } else if ($filters['data'] == 'all' && !empty($quantity)) {
                    $report->having($defaultHaving . ' != 0');
                }
            }

            if (key_exists('booking', $filters) && !empty($filters['booking'])) {
                if (is_array($filters['booking'])) {
                    $report->where_in('(IFNULL(bookings.id_booking, bookings.id))', $filters['booking']);
                } else {
                    $report->where('(IFNULL(bookings.id_booking, bookings.id))=', $filters['booking']);
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
                $report->where('DATE(completed_at) <=', format_date($filters['stock_date']));
            }
        }

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('ref_people.id', $customerId);
        }

        if (key_exists('total_size', $filters) && !empty($filters['total_size'])) {
            if ($filters['total_size'] == 'all') {
                $report->having($defaultHaving . ' > 0');
                return $report->count_all_results();
            } else {
                return $report->get()->row_array()[$filters['total_size'] . '_total'];
            }
        }

        $baseStockQuery = $this->db->get_compiled_select();
        $report = $this->db->select([
            'stocks.*',
            'ref_positions.id AS id_position',
            'ref_positions.position',
            'position_blocks.id_position_blocks',
            'position_blocks.position_blocks',
            'ref_warehouses.id AS id_warehouse',
            'ref_warehouses.warehouse',
            'ref_branches.id AS id_branch',
            'ref_branches.branch',
            'last_work_order_goods.is_hold',
            'last_work_order_goods.status',
            'last_work_order_goods.status_danger',
            'last_work_order_goods.description',
        ])
            ->from("({$baseStockQuery}) AS stocks")
            ->join("(
                SELECT 
                    id_work_order_goods,
                    GROUP_CONCAT(ref_position_blocks.id) AS id_position_blocks,
                    GROUP_CONCAT(ref_position_blocks.position_block) AS position_blocks
                FROM ref_position_blocks
                INNER JOIN work_order_goods_positions ON work_order_goods_positions.id_position_block = ref_position_blocks.id
                GROUP BY work_order_goods_positions.id_work_order_goods              
            ) AS position_blocks", 'position_blocks.id_work_order_goods = stocks.id_work_order_goods', 'left')
            ->join('work_order_goods AS last_work_order_goods', 'last_work_order_goods.id = stocks.id_work_order_goods', 'left')
            ->join('ref_positions', 'ref_positions.id = last_work_order_goods.id_position', 'left')
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
            ->or_like('stocks.goods_name', trim($search))
            ->or_like('stocks.no_reference', trim($search))
            ->or_like('stocks.no_packing_list', trim($search))
            ->or_like('stocks.no_invoice', trim($search))
            ->or_like('stocks.no_bl', trim($search))
            ->or_like('stocks.no_goods', trim($search))
            ->or_like('stocks.goods_name', trim($search))
            ->or_like('stocks.whey_number', trim($search))
            ->or_like('stocks.unit', trim($search))
            ->or_like('stocks.age', trim($search))
            ->or_like('stocks.inbound_date', trim($search))
            ->or_like('stocks.no_pallet', trim($search))
            ->or_like('stocks.no_container', trim($search))
            ->or_like('ref_positions.position', trim($search))
            ->or_like('ref_warehouses.warehouse', trim($search))
            ->or_like('ref_branches.branch', trim($search))
            ->or_like('stocks.unit', $search)
            ->or_like('stocks.stock_quantity', $search)
            ->or_like('stocks.stock_tonnage', $search)
            ->or_like('stocks.stock_length', $search)
            ->or_like('stocks.stock_width', $search)
            ->or_like('stocks.stock_height', $search)
            ->or_like('stocks.stock_volume', $search)
            ->or_like('last_work_order_goods.status', $search)
            ->or_like('last_work_order_goods.status_danger', $search)
            ->or_like('last_work_order_goods.is_hold', $search)
            ->or_like('last_work_order_goods.description', $search)
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
    /**
     * Get stock summary goods.
     *
     * @param array $filters
     * @return array|int
     */
    public function getStockGoodsRemaining($filters = [],$id_goods,$id_unit,$ex_container)
    {
        $column = get_if_exist($filters, 'order_by', 0);
        $sort = get_if_exist($filters, 'order_method', 'desc');
        $search = get_if_exist($filters, 'search', '');
        $length = get_if_exist($filters, 'length', 10);
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));
        $quantity = key_exists('quantity', $filters) ? $filters['quantity'] : '';

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $columnOrder = [
            "stocks.owner_name",
            "stocks.owner_name",
            "stocks.no_reference",
            "stocks.no_invoice",
            "stocks.no_bl",
            "stocks.no_goods",
            "stocks.goods_name",
            "ref_assemblies.no_assembly",
            "stocks.whey_number",
            "ref_positions.position",
            "ref_warehouses.warehouse",
            "last_work_order_goods.no_pallet",
            "stocks.stock_quantity",
            "stocks.unit",
            "stocks.unit_weight",
            "stocks.stock_weight",
            "stocks.unit_gross_weight",
            "stocks.stock_gross_weight",
            "stocks.unit_length",
            "stocks.unit_width",
            "stocks.unit_height",
            "stocks.unit_volume",
            "stocks.stock_volume",
            "last_work_order_goods.status",
            "last_work_order_goods.status_danger",
            "last_work_order_goods.is_hold",
            "stocks.ex_no_container",
            "stocks.age",
            "stocks.inbound_date",
            "last_work_order_goods.description",
        ];
        $columnSort = $columnOrder[$column];

        $defaultSelect = [
            'ref_people.id AS id_owner',
            'ref_people.name AS owner_name',
            'booking_inbounds.id AS id_booking',
            'booking_inbounds.id AS id_booking_reference',
            'booking_inbounds.no_reference',
            '(SELECT upload_documents.no_document FROM upload_documents 
              LEFT JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
              WHERE upload_documents.id_upload = booking_inbounds.id_upload
              AND ref_document_types.document_type = "Invoice"
              LIMIT 1) AS no_invoice',
            '(SELECT upload_documents.no_document FROM upload_documents 
              LEFT JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
              WHERE upload_documents.id_upload = booking_inbounds.id_upload
              AND ref_document_types.document_type = "Packing List"
              LIMIT 1) AS no_packing_list',
            '(SELECT upload_documents.no_document FROM upload_documents 
              LEFT JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
              WHERE upload_documents.id_upload = booking_inbounds.id_upload
              AND ref_document_types.document_type = "Bill Of Loading"
              LIMIT 1) AS no_bl',
            'ref_goods.id AS id_goods',
            'ref_goods.no_goods',
            'ref_goods.name AS goods_name',
            'ref_goods.id_assembly',
            'ref_goods.whey_number',
            'ref_units.id AS id_unit',
            'ref_units.unit',
            'SUM(quantity * multiplier_goods) AS stock_quantity',
            'ref_goods.unit_weight',
            'SUM(quantity * multiplier_goods) * ref_goods.unit_weight AS stock_weight',
            'ref_goods.unit_gross_weight',
            'SUM(quantity * multiplier_goods) * ref_goods.unit_gross_weight AS stock_gross_weight',
            'ref_goods.unit_length',
            'ref_goods.unit_width',
            'ref_goods.unit_height',
            'ref_goods.unit_volume',
            'SUM(quantity * multiplier_goods) * ref_goods.unit_volume AS stock_volume',
            'work_order_goods.ex_no_container',
            'DATEDIFF(CURDATE(), MIN(IFNULL(security_out_date, completed_at))) AS age',
            'MIN(IFNULL(security_out_date, completed_at)) AS inbound_date',
            'MAX(completed_at) AS last_activity_date',
            'MAX(work_order_goods.id) AS id_work_order_goods',
        ];

        $defaultGroup = 'ref_people.id, booking_inbounds.id, booking_inbounds.no_reference, booking_inbounds.id_upload, ref_goods.id, ref_units.id, ref_units.unit, ex_no_container';

        $defaultHaving = 'stock_quantity';
        if (key_exists('total_size', $filters) && !empty($filters['total_size'])) {
            if ($filters['total_size'] != 'all') {
                $countColumn = $filters['total_size'];
                if ($filters['total_size'] == 'unit_weight' || $filters['total_size'] == 'unit_volume') {
                    $countColumn = 'ref_goods.' . $filters['total_size'] . ' * quantity';
                }

                $defaultSelect = [
                    'SUM(' . $countColumn . ' * multiplier_goods) AS ' . $filters['total_size'] . '_total'
                ];
                $defaultGroup = '';
                $defaultHaving = $filters['total_size'] . '_total';
            }
        }

        $report = $this->db->select($defaultSelect)
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('booking_references', 'booking_references.id_booking = bookings.id', 'left')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(booking_references.id_booking_reference, bookings.id)', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id AND IFNULL(work_order_goods.id_booking_reference, bookings.id) = booking_inbounds.id')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit', 'left')
            ->join('ref_people', 'ref_people.id = work_order_goods.id_owner')
            ->join('ref_branches as branches', 'branches.id = bookings.id_branch')
            ->where([
                'bookings.is_deleted' => false,
                'handlings.status' => 'APPROVED',
                'handlings.is_deleted' => false,
                'work_orders.is_deleted' => false,
                'work_order_goods.is_deleted' => false,
                'ref_handling_types.is_deleted' => false,
                'ref_people.is_deleted' => false,
                'work_orders.status' => 'COMPLETED',
                'ref_goods.id' => $id_goods,
                'ref_units.id' => $id_unit,
                'work_order_goods.ex_no_container' => $ex_container,
            ])
            ->group_by($defaultGroup);

        if (empty($filters) || !key_exists('data', $filters)) {
            $report->having($defaultHaving . ' > 0');
        } else {
            if (key_exists('data', $filters) && !empty($filters['data'])) {
                if ($filters['data'] == 'stock') {
                    $report->having($defaultHaving . ' > 0');
                } else if ($filters['data'] == 'all' && !empty($quantity)) {
                    $report->having($defaultHaving . ' != 0');
                }
            }

            if (key_exists('branch_type', $filters) && !is_null($filters['branch_type'])) {
                $report->where('branches.branch_type IS NOT NULL');
                $report->where('branches.branch_type', $filters['branch_type']);
            }

            if (key_exists('booking', $filters) && !empty($filters['booking'])) {
                if (is_array($filters['booking'])) {
                    $report->where_in('(IFNULL(bookings.id_booking, bookings.id))', $filters['booking']);
                } else {
                    $report->where('(IFNULL(bookings.id_booking, bookings.id))=', $filters['booking']);
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
        }

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('ref_people.id', $customerId);
        }

        if (key_exists('total_size', $filters) && !empty($filters['total_size'])) {
            if ($filters['total_size'] == 'all') {
                return $report->count_all_results();
            } else {
                return $report->get()->row_array()[$filters['total_size'] . '_total'];
            }
        }

        $baseStockQuery = $this->db->get_compiled_select();
        $report = $this->db->select([
            'stocks.*',
            'last_work_order_goods.no_pallet',
            'ref_positions.id AS id_position',
            'ref_positions.position',
            'position_blocks.id_position_blocks',
            'position_blocks.position_blocks',
            'ref_assemblies.no_assembly',
            'ref_warehouses.id AS id_warehouse',
            'ref_warehouses.warehouse',
            'ref_branches.id AS id_branch',
            'ref_branches.branch',
            'last_work_order_goods.is_hold',
            'last_work_order_goods.status',
            'last_work_order_goods.status_danger',
            'last_work_order_goods.description',
        ])
            ->from("({$baseStockQuery}) AS stocks")
            ->join("(
                SELECT 
                    id_work_order_goods,
                    GROUP_CONCAT(ref_position_blocks.id) AS id_position_blocks,
                    GROUP_CONCAT(ref_position_blocks.position_block) AS position_blocks
                FROM ref_position_blocks
                INNER JOIN work_order_goods_positions ON work_order_goods_positions.id_position_block = ref_position_blocks.id
                GROUP BY work_order_goods_positions.id_work_order_goods              
            ) AS position_blocks", 'position_blocks.id_work_order_goods = stocks.id_work_order_goods', 'left')
            ->join('work_order_goods AS last_work_order_goods', 'last_work_order_goods.id = stocks.id_work_order_goods', 'left')
            ->join('ref_assemblies', 'ref_assemblies.id = stocks.id_assembly', 'left')
            ->join('ref_positions', 'ref_positions.id = last_work_order_goods.id_position', 'left')
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
            ->or_like('stocks.goods_name', trim($search))
            ->or_like('stocks.no_reference', trim($search))
            ->or_like('stocks.no_packing_list', trim($search))
            ->or_like('stocks.no_invoice', trim($search))
            ->or_like('stocks.no_bl', trim($search))
            ->or_like('stocks.no_goods', trim($search))
            ->or_like('stocks.goods_name', trim($search))
            ->or_like('stocks.whey_number', trim($search))
            ->or_like('stocks.unit', trim($search))
            ->or_like('stocks.age', trim($search))
            ->or_like('stocks.inbound_date', trim($search))
            ->or_like('last_work_order_goods.no_pallet', trim($search))
            ->or_like('stocks.ex_no_container', trim($search))
            ->or_like('ref_positions.position', trim($search))
            ->or_like('ref_warehouses.warehouse', trim($search))
            ->or_like('ref_branches.branch', trim($search))
            ->or_like('stocks.unit', $search)
            ->or_like('stocks.stock_quantity', $search)
            ->or_like('stocks.unit_length', $search)
            ->or_like('stocks.unit_width', $search)
            ->or_like('stocks.unit_height', $search)
            ->or_like('stocks.unit_volume', $search)
            ->or_like('stocks.unit_weight', $search)
            ->or_like('stocks.unit_gross_weight', $search)
            ->or_like('last_work_order_goods.status', $search)
            ->or_like('last_work_order_goods.status_danger', $search)
            ->or_like('last_work_order_goods.is_hold', $search)
            ->or_like('last_work_order_goods.description', $search)
            ->group_end()
            ->order_by($columnSort, $sort);

        
        return $report->get()->row_array();
    }

    /**
     * Get comparison outbound to job.
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getStockOutboundWithRequest($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $stockDate = '';
        if (key_exists('stock_date', $filters) && !empty($filters['stock_date'])) {
            $stockDate = format_date($filters['stock_date']);
        }
        $bookingOutboundFilterId = $filters['booking_outbound'] ?? '';

        $this->db->start_cache();

        $baseQuery = $this->db
            ->select([
                'ref_people.id AS id_customer',
                'ref_people.name AS customer_name',
                'booking_inbounds.id AS id_booking_inbound',
                'booking_inbounds.no_reference AS no_reference_inbound',
                'bookings.id_upload AS id_upload_outbound',
                'bookings.id AS id_booking_outbound',
                'bookings.no_reference AS no_reference_outbound',
		        'bookings.booking_date',
                'ref_goods.id AS id_goods',
                'ref_goods.no_goods',
                'ref_goods.whey_number',
                'ref_goods.name AS goods_name',
                'ref_units.id AS id_unit',
                'ref_units.unit',
                'booking_goods.ex_no_container',
                'booking_goods.quantity AS booking_outbound_quantity',
                'IFNULL(
                    IF(outbound_requests.quantity > booking_goods.quantity, 
                        booking_goods.quantity, 
                        outbound_requests.quantity
                    ), 
                0) AS request_quantity',
                'IFNULL(outbound_requests.hold_statuses, "NOT REQUESTED") AS hold_statuses',
                'IF(outbound_requests.quantity > 0, IFNULL(outbound_requests.unload_locations, "NOT SET"), "NOT REQUESTED") AS unload_locations',
                'IF(outbound_requests.quantity > 0, IFNULL(outbound_requests.priorities, "NOT SET"), "NOT REQUESTED") AS priorities',
                'IF(outbound_requests.quantity > 0, IFNULL(outbound_requests.priority_descriptions, "NOT SET"), "NOT REQUESTED") AS priority_descriptions',
                'IFNULL(work_order_goods.quantity, 0) AS work_order_quantity',
                'booking_goods.quantity - IFNULL(work_order_goods.quantity, 0) AS stock_outbound',
                'IFNULL(DATEDIFF(CURDATE(), work_order_inbounds.completed_at), 0) AS age_inbound',
                'IFNULL(DATEDIFF(CURDATE(), booking_goods.created_at), 0) AS age_outbound'
            ])
            ->from('bookings')
            ->join('uploads', 'uploads.id = bookings.id_upload')
            ->join('ref_people', 'ref_people.id = bookings.id_customer')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type')
            ->join('booking_goods', 'booking_goods.id_booking = bookings.id')
            ->join('ref_goods', 'ref_goods.id = booking_goods.id_goods')
            ->join('ref_units', 'ref_units.id = booking_goods.id_unit')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = booking_goods.id_booking_reference')
            ->join("(
                SELECT 
                    handlings.id_booking,
                    work_order_goods.id_goods,
                    work_order_goods.id_unit,
                    work_order_goods.ex_no_container,
                    MAX(work_orders.completed_at) AS completed_at
                FROM bookings
	            INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id 
                WHERE handlings.status = 'APPROVED' 
                    AND work_orders.status = 'COMPLETED'
                    AND work_orders.is_deleted = FALSE
                    AND ref_handling_types.multiplier_goods = 1
                    " . (!empty($branchId) ? "AND bookings.id_branch = {$branchId}" : '') . "
                GROUP BY id_booking, id_goods, id_unit, ex_no_container
            ) AS work_order_inbounds", "work_order_inbounds.id_booking = booking_goods.id_booking_reference
                AND work_order_inbounds.id_goods = booking_goods.id_goods
                AND work_order_inbounds.id_unit = booking_goods.id_unit
                AND work_order_inbounds.ex_no_container <=> booking_goods.ex_no_container", 'left', false)
            ->join("(
                SELECT
                    bookings.id AS id_booking, 
                    bookings.id_upload, 
                    id_goods, 
                    id_unit, 
                    IF(ex_no_container = '', null, ex_no_container) AS ex_no_container,
                    GROUP_CONCAT(DISTINCT IF(hold_status = 'OK', 'RELEASED', hold_status)) AS hold_statuses,
                    GROUP_CONCAT(DISTINCT unload_location) AS unload_locations,
                    GROUP_CONCAT(DISTINCT priority) AS priorities,
                    GROUP_CONCAT(DISTINCT priority_description) AS priority_descriptions,
                    GROUP_CONCAT(transporter_entry_permit_request_uploads.id) AS id_request_uploads,
	                GROUP_CONCAT(DISTINCT transporter_entry_permit_requests.id) AS id_requests,
                    GROUP_CONCAT(DISTINCT transporter_entry_permit_requests.no_request) AS no_requests,
                    SUM(IF(armada = 'TCI', quantity, 0)) AS quantity
                FROM transporter_entry_permit_request_uploads
                INNER JOIN transporter_entry_permit_requests ON transporter_entry_permit_requests.id = transporter_entry_permit_request_uploads.id_request
                INNER JOIN uploads ON uploads.id = transporter_entry_permit_request_uploads.id_upload
                INNER JOIN bookings ON bookings.id_upload = uploads.id
                WHERE quantity > 0 AND armada = 'TCI'
                    " . (!empty($branchId) ? "AND bookings.id_branch = {$branchId}" : '') . "
                    " . (!empty($bookingOutboundFilterId) ? "AND bookings.id = {$bookingOutboundFilterId}" : '') . "
                GROUP BY bookings.id, id_goods, id_unit, IF(ex_no_container = '', NULL, ex_no_container)
            ) AS outbound_requests", "outbound_requests.id_booking = booking_goods.id_booking
                AND outbound_requests.id_goods = booking_goods.id_goods
                AND outbound_requests.id_unit = booking_goods.id_unit
                AND outbound_requests.ex_no_container <=> booking_goods.ex_no_container", 'left', false)
            ->join("(
                SELECT 
                    handlings.id_booking,
                    work_order_goods.id_booking_reference,
                    work_order_goods.id_goods,
                    work_order_goods.id_unit,
                    work_order_goods.ex_no_container,
                    SUM(work_order_goods.quantity) AS quantity
                FROM bookings
	            INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id 
                WHERE handlings.status = 'APPROVED' 
                    AND work_orders.status = 'COMPLETED'
                    AND work_orders.is_deleted = FALSE
                    AND ref_handling_types.multiplier_goods = -1
                    " . (!empty($branchId) ? "AND bookings.id_branch = {$branchId}" : '') . "
                    " . (!empty($bookingOutboundFilterId) ? "AND bookings.id = {$bookingOutboundFilterId}" : '') . "
                    " . (!empty($stockDate) ? "AND DATE(work_orders.completed_at) <= '{$stockDate}'" : '') . "
                GROUP BY id_booking, id_booking_reference, id_goods, id_unit, ex_no_container
            ) AS work_order_goods", "work_order_goods.id_booking = booking_goods.id_booking
                AND work_order_goods.id_booking_reference = booking_goods.id_booking_reference
                AND work_order_goods.id_goods = booking_goods.id_goods
                AND work_order_goods.id_unit = booking_goods.id_unit
                AND work_order_goods.ex_no_container <=> booking_goods.ex_no_container", 'left', false)
            ->where([
                'ref_booking_types.category' => 'OUTBOUND',
                'bookings.is_deleted' => false,
            ])
            ->where_in('uploads.status', ['AP', 'CLEARANCE']);

        if (!empty($search)) {
            $search = trim($search);
            $baseQuery
                ->group_start()
                ->or_like('ref_people.name', $search)
                ->or_like('bookings.no_reference', $search)
                ->or_like('booking_inbounds.no_reference', $search)
                ->or_like('ref_goods.name', $search)
                ->or_like('ref_goods.no_goods', $search)
                ->or_like('ref_goods.whey_number', $search)
                ->or_like('ref_units.unit', $search)
                ->or_like('booking_goods.ex_no_container', $search)
                ->group_end();
        }

        if ($userType == 'EXTERNAL') {
            $baseQuery->where('bookings.id_customer', $customerId);
        }

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if (!empty($stockDate)) {
            $baseQuery->where('DATE(bookings.created_at) <=', $stockDate);
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->where('bookings.id_customer', $filters['customer']);
        }

        if (key_exists('booking_outbound', $filters) && !empty($filters['booking_outbound'])) {
            $baseQuery->where('bookings.id', $filters['booking_outbound']);
        }

        if (key_exists('goods', $filters) && !empty($filters['goods'])) {
            $baseQuery->where('booking_goods.id_goods', $filters['goods']);
        }

        if (key_exists('unit', $filters) && !empty($filters['unit'])) {
            $baseQuery->where('booking_goods.id_unit', $filters['unit']);
        }

        if (key_exists('ex_no_container', $filters)) {
            $baseQuery->where('booking_goods.ex_no_container', if_empty($filters['ex_no_container'], null));
        }

        if (key_exists('priority', $filters) && !empty($filters['priority'])) {
            if ($filters['priority'] == 'NOT REQUESTED') {
                $baseQuery
                    ->where('outbound_requests.quantity <=', 0)
                    ->where('outbound_requests.priorities IS NULL');
            } else if ($filters['priority'] == 'NOT SET') {
                $baseQuery
                    ->where('outbound_requests.quantity >', 0)
                    ->where('outbound_requests.priorities IS NULL');
            } else {
                $baseQuery->where('outbound_requests.priorities', $filters['priority']);
            }
        }

        if (key_exists('hold_status', $filters) && !empty($filters['hold_status'])) {
            if ($filters['hold_status'] == 'NOT REQUESTED') {
                $baseQuery->where('outbound_requests.hold_statuses IS NULL');
            } else {
                $baseQuery->like('outbound_requests.hold_statuses', $filters['hold_status']);
            }
        }

        if (key_exists('request_status', $filters) && !empty($filters['request_status'])) {
            switch ($filters['request_status']) {
                case 'REQUESTED':
                    $baseQuery->where('outbound_requests.quantity IS NOT NULL', null);
                    //$baseQuery->having('request_quantity>', 0);
                    break;
                case 'UNREQUESTED':
                    //$baseQuery->having('request_quantity', 0);
                    $baseQuery->where('outbound_requests.quantity IS NULL', null);
                    break;
            }
        }

        if (key_exists('outstanding_request', $filters) && $filters['outstanding_request']) {
            $baseQuery
                ->select([
                    'outbound_requests.id_booking',
                    'outbound_requests.id_upload',
                    'outbound_requests.id_request_uploads',
                    'outbound_requests.id_requests',
                    'outbound_requests.no_requests',
                ])
                ->where([
                    "IF(outbound_requests.quantity > booking_goods.quantity, 
                        booking_goods.quantity, 
                        outbound_requests.quantity
                    ) >" => 0,
                    "IF(outbound_requests.quantity > booking_goods.quantity, 
                        booking_goods.quantity, 
                        outbound_requests.quantity
                    ) > IFNULL(work_order_goods.quantity, 0)" => null,
                ]);
        }

        if ((key_exists('data', $filters) && $filters['data'] == 'stock') || !key_exists('data', $filters)) {
            $baseQuery
                ->where([
                    'NOT EXISTS (
                        SELECT id, id_upload, no_document, document_date 
                        FROM upload_documents 
                        WHERE id_document_type = 47 
                        AND is_deleted = FALSE
                        AND upload_documents.id_upload = bookings.id_upload
                    )' => null, // not have SPPD
                ])
                ->having('stock_outbound > 0');
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $finalQuery = $this->db->get_compiled_select();
        //$total = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$finalQuery}) AS CI_count_all_results")->row_array()['numrows'];

        // counting result is slow, use simple pagination or cache to share result set like below
        $distinctQueryParams = filter_data_by_keys($filters, ['customer', 'stock_date', 'request_status', 'priority', 'hold_status', 'booking_outbound', 'search']);
        $cacheIdxKey = 'stock-outbound-idx-count-' . $branchId . '-' . md5(json_encode($distinctQueryParams));
        $total = cache_remember($cacheIdxKey, 120, function() use ($finalQuery) {
            return $this->db->query("SELECT COUNT(*) AS numrows FROM ({$finalQuery}) AS CI_count_all_results")->row_array()['numrows'];
        });

        if($column == 'no') $column = 'bookings.no_reference';
        $page = $baseQuery->order_by($column, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        $this->db->flush_cache();

        foreach ($data as &$row) {
            $row['no'] = ++$start;
        }

        return [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($data),
            "recordsFiltered" => $total,
            "data" => $data
        ];
    }

    /**
     * Get comparison outbound to job.
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getStockOutbound($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $stockDate = '';
        if (key_exists('stock_date', $filters) && !empty($filters['stock_date'])) {
            $stockDate = format_date($filters['stock_date']);
        }

        $this->db->start_cache();

        $baseQuery = $this->db
            ->select([
                'booking_outbounds.customer_name',
                'booking_outbounds.no_reference_inbound',
                'booking_outbounds.no_reference_outbound',
                'booking_outbounds.no_goods',
                'booking_outbounds.goods_name',
                'booking_outbounds.unit',
                'booking_outbounds.ex_no_container',
                'booking_outbounds.quantity AS booking_outbound_quantity',
                'IFNULL(SUM(work_order_goods.quantity), 0) AS work_order_quantity',
                'booking_outbounds.quantity - IFNULL(SUM(work_order_goods.quantity), 0) AS stock_outbound',
                'IFNULL(DATEDIFF(CURDATE(), last_completed_at), 0) AS age_inbound',
                'IFNULL(DATEDIFF(CURDATE(), booking_outbounds.created_at), 0) AS age_outbound',
            ])
            ->from("(
                SELECT 
                    bookings.id AS id_booking,
                    bookings.id_customer,
                    ref_people.name AS customer_name,
                    booking_inbounds.no_reference AS no_reference_inbound,
                    bookings.no_reference AS no_reference_outbound,
                    ref_goods.id AS id_goods,
                    ref_goods.no_goods,
                    ref_goods.name AS goods_name,
                    ref_units.id AS id_unit,
                    ref_units.unit,
                    booking_goods.id_booking_reference,
                    booking_goods.ex_no_container,
                    booking_goods.quantity AS quantity,
                    MAX(booking_goods.created_at) AS created_at,
                    MAX(work_order_inbounds.completed_at) AS last_completed_at
                FROM bookings
                INNER JOIN ref_booking_types ON ref_booking_types.id = bookings.id_booking_type
                INNER JOIN (
                    SELECT  
                        id_booking, IFNULL(id_booking_reference, id_booking) AS id_booking_reference, id_goods, id_unit, IFNULL(booking_goods.ex_no_container, '') AS ex_no_container, SUM(quantity) AS quantity, MAX(booking_goods.created_at) created_at
                    FROM booking_goods
                    GROUP BY id_booking, IFNULL(id_booking_reference, id_booking), id_goods, id_unit, IFNULL(booking_goods.ex_no_container, '')
                ) AS booking_goods ON booking_goods.id_booking = bookings.id
                LEFT JOIN bookings AS booking_inbounds ON booking_inbounds.id = booking_goods.id_booking_reference
                INNER JOIN ref_people ON ref_people.id = bookings.id_customer
                INNER JOIN ref_goods ON ref_goods.id = booking_goods.id_goods
                LEFT JOIN ref_units ON ref_units.id = booking_goods.id_unit
                LEFT JOIN (
                    SELECT 
                        handlings.id_booking,
                        work_orders.completed_at,
                        IFNULL(work_order_goods.id_booking_reference, handlings.id_booking) AS id_booking_reference,
                        work_order_goods.id_goods,
                        work_order_goods.id_unit,
                        work_order_goods.ex_no_container
                    FROM handlings
                    INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                    INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                    INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                    WHERE handlings.status = 'APPROVED' 
                        AND work_orders.status = 'COMPLETED'
                        AND work_orders.is_deleted = FALSE
                        AND ref_handling_types.multiplier_goods > 0
                ) AS work_order_inbounds ON work_order_inbounds.id_booking = booking_inbounds.id
                AND work_order_inbounds.id_booking_reference = booking_goods.id_booking_reference
                    AND work_order_inbounds.id_goods = booking_goods.id_goods
                        AND work_order_inbounds.id_unit = booking_goods.id_unit
                            AND IFNULL(work_order_inbounds.ex_no_container, '') = IFNULL(booking_goods.ex_no_container, '')
                WHERE bookings.is_deleted = FALSE AND ref_booking_types.category = 'OUTBOUND'
                    AND bookings.id_branch = '{$branchId}'
                    " . ($userType == 'EXTERNAL' ? "AND bookings.id_customer = '{$customerId}'" : '') . "
                    " . (!empty($stockDate) ? "AND DATE(bookings.created_at) <= '{$stockDate}'" : '') . "
                GROUP BY bookings.id, booking_goods.id_goods, booking_goods.id_unit, IFNULL(booking_goods.ex_no_container, '')
            ) AS booking_outbounds")
            ->join("(
                SELECT 
                    handlings.id_booking,
                    work_orders.no_work_order,
                    IFNULL(work_order_goods.id_booking_reference, handlings.id_booking) AS id_booking_reference,
                    work_order_goods.id_unit,
                    work_order_goods.id_goods,
                    work_order_goods.ex_no_container,
                    work_order_goods.quantity		
                FROM handlings
                INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id 
                WHERE handlings.status = 'APPROVED' 
                    AND work_orders.status = 'COMPLETED'
                    AND work_orders.is_deleted = FALSE
                    AND (ref_handling_types.multiplier_goods = 1 OR ref_handling_types.multiplier_goods = -1)
                    " . (!empty($stockDate) ? "AND DATE(work_orders.completed_at) <= '{$stockDate}'" : '') . "
            ) AS work_order_goods", "work_order_goods.id_booking = booking_outbounds.id_booking
            AND work_order_goods.id_booking_reference = booking_outbounds.id_booking_reference
                AND work_order_goods.id_goods = booking_outbounds.id_goods
                    AND work_order_goods.id_unit = booking_outbounds.id_unit
                        AND IFNULL(work_order_goods.ex_no_container, '') = IFNULL(booking_outbounds.ex_no_container, '')", 'left')
            ->group_by("booking_outbounds.id_booking, booking_outbounds.id_goods, booking_outbounds.id_unit, IFNULL(booking_outbounds.ex_no_container, '')");

        if ($userType == 'EXTERNAL') {
            $baseQuery->where('booking_outbounds.id_customer', $customerId);
        }

        if (!empty($search)) {
            $baseQuery
                ->group_start()
                ->or_like('booking_outbounds.customer_name', trim($search))
                ->or_like('booking_outbounds.no_reference_inbound', trim($search))
                ->or_like('booking_outbounds.no_reference_outbound', trim($search))
                ->or_like('booking_outbounds.goods_name', trim($search))
                ->or_like('booking_outbounds.no_goods', trim($search))
                ->or_like('booking_outbounds.unit', trim($search))
                ->or_like('booking_outbounds.ex_no_container', trim($search))
                ->group_end();
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->where('booking_outbounds.id_customer', $filters['customer']);
        }

        if (key_exists('booking_outbound', $filters) && !empty($filters['booking_outbound'])) {
            $baseQuery->where('booking_outbounds.id_booking', $filters['booking_outbound']);
        }

        if (key_exists('data', $filters) && !empty($filters['data'])) {
            if ($filters['data'] == 'stock') {
                $baseQuery->having('stock_outbound > 0');
            }
        } else {
            $baseQuery->having('stock_outbound > 0');
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $finalQuery = $this->db->get_compiled_select();
        $total = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$finalQuery}) AS CI_count_all_results")->row_array()['numrows'];

        if($column == 'no') $column = 'booking_outbounds.no_reference_outbound';
        $page = $baseQuery->order_by($column, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        $this->db->flush_cache();

        foreach ($data as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($data),
            "recordsFiltered" => $total,
            "data" => $data
        ];

        return $pageData;
    }

    /**
     * Get stock request TEP.
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getStockRequest($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $this->db->start_cache();

        $baseQuery = $this->db
            ->distinct()
            ->select([
                'booking_outbounds.customer_name',
                'booking_outbounds.no_reference_inbound',
                'booking_outbounds.no_reference_outbound',
                'booking_outbounds.id_booking AS id_booking_outbound',
                'booking_outbounds.no_goods',
                'booking_outbounds.goods_name',
                'booking_outbounds.unit',
                'booking_outbounds.ex_no_container',
                'booking_outbounds.quantity AS booking_outbound_quantity',
                'booking_outbounds.whey_number',
                'booking_outbounds.id_goods',
                'booking_outbounds.id_unit',
                'booking_outbounds.id_upload',
                'IFNULL(tep_goods.req_quantity,0) AS req_quantity',
                // 'IFNULL(SUM(tep_goods.quantity), 0) AS req_quantity',
                'IFNULL(SUM(work_order_goods.quantity), 0) AS work_order_quantity',
                'booking_outbounds.quantity - IFNULL(SUM(work_order_goods.quantity), 0) - IFNULL(tep_goods.req_quantity,0) AS stock_outbound',
                'booking_outbounds.quantity - IFNULL(SUM(work_order_goods.quantity), 0) AS stock_outbound_job',
                '(SELECT upload_documents.no_document FROM upload_documents 
                LEFT JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                WHERE upload_documents.id_upload = booking_in.id_upload
                AND ref_document_types.document_type = "Invoice"
                LIMIT 1) AS no_invoice',
                '(SELECT upload_documents.no_document FROM upload_documents 
                LEFT JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                WHERE upload_documents.id_upload = booking_in.id_upload
                AND ref_document_types.document_type = "Bill Of Loading"
                LIMIT 1) AS no_bl'
            ])
            ->from("(
                SELECT 
                    bookings.id AS id_booking,
                    bookings.id_customer,
                    ref_people.name AS customer_name,
                    booking_inbounds.no_reference AS no_reference_inbound,
                    bookings.no_reference AS no_reference_outbound,
                    ref_goods.id AS id_goods,
                    ref_goods.no_goods,
                    ref_goods.name AS goods_name,
                    ref_goods.whey_number,
                    ref_units.id AS id_unit,
                    ref_units.unit,
                    booking_goods.id_booking_reference,
                    booking_goods.ex_no_container,
                    booking_goods.quantity AS quantity,
                    bookings.id_upload,
                    booking_inbounds.id AS id_booking_in
                FROM bookings
                INNER JOIN ref_booking_types ON ref_booking_types.id = bookings.id_booking_type
                INNER JOIN (
                    SELECT  
                        id_booking, IFNULL(id_booking_reference, id_booking) AS id_booking_reference, id_goods, id_unit, IFNULL(booking_goods.ex_no_container, '') AS ex_no_container, SUM(quantity) AS quantity, MAX(booking_goods.created_at) created_at
                    FROM booking_goods
                    GROUP BY id_booking, IFNULL(id_booking_reference, id_booking), id_goods, id_unit, IFNULL(booking_goods.ex_no_container, '')
                ) AS booking_goods ON booking_goods.id_booking = bookings.id
                LEFT JOIN bookings AS booking_inbounds ON booking_inbounds.id = booking_goods.id_booking_reference
                INNER JOIN ref_people ON ref_people.id = bookings.id_customer
                INNER JOIN ref_goods ON ref_goods.id = booking_goods.id_goods
                LEFT JOIN ref_units ON ref_units.id = booking_goods.id_unit
                LEFT JOIN (
                    SELECT 
                        handlings.id_booking,
                        work_orders.completed_at,
                        IFNULL(work_order_goods.id_booking_reference, handlings.id_booking) AS id_booking_reference,
                        work_order_goods.id_goods,
                        work_order_goods.id_unit,
                        work_order_goods.ex_no_container
                    FROM handlings
                    INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                    INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                    INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                    WHERE handlings.status = 'APPROVED' 
                        AND work_orders.status = 'COMPLETED'
                        AND work_orders.is_deleted = FALSE
                        AND ref_handling_types.multiplier_goods > 0
                ) AS work_order_inbounds ON work_order_inbounds.id_booking = booking_inbounds.id
                AND work_order_inbounds.id_booking_reference = booking_goods.id_booking_reference
                    AND work_order_inbounds.id_goods = booking_goods.id_goods
                        AND work_order_inbounds.id_unit = booking_goods.id_unit
                            AND IFNULL(work_order_inbounds.ex_no_container, '') = IFNULL(booking_goods.ex_no_container, '')
                WHERE bookings.is_deleted = FALSE AND ref_booking_types.category = 'OUTBOUND'
                    AND bookings.id_branch = '{$branchId}'
                    " . ($userType == 'EXTERNAL' ? "AND bookings.id_customer = '{$customerId}'" : '') . "
                GROUP BY bookings.id, booking_goods.id_goods, booking_goods.id_unit, IFNULL(booking_goods.ex_no_container, '')
            ) AS booking_outbounds")
            ->join("(
                SELECT 
                    handlings.id_booking,
                    work_orders.no_work_order,
                    IFNULL(work_order_goods.id_booking_reference, handlings.id_booking) AS id_booking_reference,
                    work_order_goods.id_unit,
                    work_order_goods.id_goods,
                    work_order_goods.ex_no_container,
                    work_order_goods.quantity		
                FROM handlings
                INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id 
                WHERE handlings.status = 'APPROVED' 
                    AND work_orders.status = 'COMPLETED'
                    AND work_orders.is_deleted = FALSE
                    AND (ref_handling_types.multiplier_goods = 1 OR ref_handling_types.multiplier_goods = -1)
            ) AS work_order_goods", "work_order_goods.id_booking = booking_outbounds.id_booking
            AND work_order_goods.id_booking_reference = booking_outbounds.id_booking_reference
                AND work_order_goods.id_goods = booking_outbounds.id_goods
                    AND work_order_goods.id_unit = booking_outbounds.id_unit
                        AND IFNULL(work_order_goods.ex_no_container, '') = IFNULL(booking_outbounds.ex_no_container, '')", 'left')
            ->join("(
                SELECT 
                    tep_req_upload.*,bookings.id AS id_booking, SUM(tep_req_upload.quantity) AS req_quantity
                FROM transporter_entry_permit_request_uploads AS tep_req_upload
                INNER JOIN bookings ON bookings.`id_upload` = tep_req_upload.id_upload
                INNER JOIN transporter_entry_permit_requests AS tep_req ON tep_req.id = tep_req_upload.id_request
                LEFT JOIN transporter_entry_permit_request_tep AS tep_req_tep ON tep_req_tep.`id_request` = tep_req.id
                LEFT JOIN transporter_entry_permits AS tep ON tep.id = tep_req_tep.id_tep
                WHERE tep_req.status != 'SKIP' AND tep_req.armada != 'CUSTOMER'
                    AND (((tep_req_tep.id_tep IS NULL 
                            AND tep_req.tep_date >= DATE(NOW()))
                                OR (tep_req_tep.id_tep IS NULL 
                                AND tep_req.tep_date IS NULL
                                ))
                        OR (tep.expired_at >= NOW() 
                            AND (tep.checked_out_at is null 
                                OR tep.checked_out_at >= NOW())))
                GROUP BY id_booking,id_goods,id_unit,IFNULL(ex_no_container, '')
            ) AS tep_goods","tep_goods.id_booking = booking_outbounds.id_booking
                AND tep_goods.id_goods = booking_outbounds.id_goods
                    AND tep_goods.id_unit = booking_outbounds.id_unit
                        AND IFNULL(tep_goods.ex_no_container, '') = IFNULL(booking_outbounds.ex_no_container, '')", 'left')
            ->join('(SELECT id,id_upload FROM bookings) AS booking_in','booking_in.id = booking_outbounds.id_booking_in','left')
            ->group_by("booking_outbounds.id_booking, booking_outbounds.id_goods, booking_outbounds.id_unit, IFNULL(booking_outbounds.ex_no_container, ''), tep_goods.id")
            ->where([
                'NOT EXISTS (
                    SELECT id, id_upload, no_document, document_date 
                    FROM upload_documents 
                    WHERE id_document_type = 47 
                    AND is_deleted = FALSE
                    AND upload_documents.id_upload = booking_outbounds.id_upload
                )' => null, // not have SPPD
            ]);

        if ($userType == 'EXTERNAL') {
            $baseQuery->where('booking_outbounds.id_customer', $customerId);
        }

        if (!empty($search)) {
            $baseQuery
                ->group_start()
                ->or_like('booking_outbounds.customer_name', trim($search))
                ->or_like('booking_outbounds.no_reference_inbound', trim($search))
                ->or_like('booking_outbounds.no_reference_outbound', trim($search))
                ->or_like('booking_outbounds.goods_name', trim($search))
                ->or_like('booking_outbounds.unit', trim($search))
                ->or_like('booking_outbounds.ex_no_container', trim($search))
                ->group_end();
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->where('booking_outbounds.id_customer', $filters['customer']);
        }

        if (key_exists('booking_outbound', $filters) && !empty($filters['booking_outbound'])) {
            $baseQuery->where('booking_outbounds.id_booking', $filters['booking_outbound']);
        }

        if (key_exists('id_upload', $filters) && !empty($filters['id_upload'])) {
            $baseQuery->where('booking_outbounds.id_upload', $filters['id_upload']);
        }

        if (key_exists('ex_no_container', $filters) && !empty($filters['ex_no_container'])) {
            $baseQuery->where('booking_outbounds.ex_no_container', $filters['ex_no_container']);
        }
        if (key_exists('id_goods', $filters) && !empty($filters['id_goods'])) {
            $baseQuery->where('booking_outbounds.id_goods', $filters['id_goods']);
        }
        if (key_exists('id_unit', $filters) && !empty($filters['id_unit'])) {
            $baseQuery->where('booking_outbounds.id_unit', $filters['id_unit']);
        }

        if (key_exists('data', $filters) && !empty($filters['data'])) {
            if ($filters['data'] == 'stock') {
                $baseQuery->having('stock_outbound > 0');
            }
        } else {
            $baseQuery->having('stock_outbound > 0');
        }

        if (key_exists('stock_outbound_job', $filters) && !empty($filters['stock_outbound_job'])) {
            if ($filters['stock_outbound_job'] == 'stock') {
                $baseQuery->having('stock_outbound_job > 0');
            }
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $finalQuery = $this->db->get_compiled_select();
        $total = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$finalQuery}) AS CI_count_all_results")->row_array()['numrows'];

        if($column == 'no') $column = 'booking_outbounds.no_reference_outbound';
        $page = $baseQuery->order_by($column, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        $this->db->flush_cache();

        foreach ($data as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($data),
            "recordsFiltered" => $total,
            "data" => $data
        ];

        return $pageData;
    }

}
