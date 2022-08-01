<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportBCModel extends CI_Model
{
    /**
     * ReportBCModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get report BC inbound.
     *
     * @param array $filters
     * @return mixed
     */
    public function getReportInbound($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $handlingTypeIdInbound = get_setting('default_inbound_handling');
        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $report = $this->db->select([
            '(SELECT booking_extensions.value FROM booking_extensions 
              LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
              WHERE booking_extensions.id_booking = bookings.id AND ref_extension_fields.field_name = "nopen"
              LIMIT 1) AS no_registration',
            '(SELECT booking_extensions.value FROM booking_extensions 
              LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
              WHERE booking_extensions.id_booking = bookings.id AND ref_extension_fields.field_name = "tapen"
              LIMIT 1) AS registration_date',
            'ref_branches.branch',
            'ref_branches.branch_type',
            'ref_warehouses.warehouse',
            'bookings.no_reference AS "bc_doc_/_reference_no"',
            'bookings.reference_date AS "bc_doc_/_reference_date"',
            'ref_booking_types.booking_type AS "bc_doc_/_booking_type"',
            'bookings.no_booking AS booking_no',
            'bookings.booking_date',
            'suppliers.name AS supplier',
            'bookings.vessel',
            'bookings.voyage',
            'work_order_containers.id_owner',
            'customers.name AS owner',
            'safe_conducts.no_safe_conduct AS safe_conduct_no',
            'safe_conducts.created_at AS safe_conduct_date',
            'safe_conducts.vehicle_type',
            'safe_conducts.driver',
            'safe_conducts.no_police AS police_no',
            'safe_conducts.expedition',
            'ref_eseals.no_eseal AS eseal_no',
            'work_orders.no_work_order AS job_no',
            'work_orders.completed_at AS transaction_date',
            '"CONTAINER" AS item_category',
            'ref_containers.no_container AS item_no',
            'ref_containers.no_container AS item_name',
            'work_order_containers.quantity AS quantity',
            '"Cargo" AS unit',
            '"" AS tonnage',
            '"" AS volume',
            'ref_positions.position',
            'ref_containers.type AS container_type',
            'ref_containers.size AS container_size',
            'work_order_containers.seal AS container_seal',
            'IF(work_order_containers.is_empty = 0, "FULL", "EMPTY") AS container_status',
            'work_order_containers.status AS item_condition',
            'work_order_containers.status_danger',
            '"" AS no_ex_container',
            '"" AS pallet_no',
            'work_order_containers.description',
            'admins.name AS admin_name',
            'tally.name AS tally_name',
        ])
            ->from('bookings')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer')
            ->join('ref_people AS suppliers', 'suppliers.id = bookings.id_supplier', 'left')
            ->join('bookings AS booking_in', 'booking_in.id = bookings.id_booking', 'left')
            ->join('safe_conducts', 'safe_conducts.id_booking = bookings.id', 'left')
            ->join('ref_eseals', 'ref_eseals.id = safe_conducts.id_eseal', 'left')
            ->join('work_orders', 'work_orders.id_safe_conduct = safe_conducts.id', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling', 'left')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container', 'left')
            ->join('ref_positions', 'ref_positions.id = work_order_containers.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join(UserModel::$tableUser . ' AS admins', 'admins.id = work_orders.created_by', 'left')
            ->join(UserModel::$tableUser . ' AS tally', 'tally.id = work_orders.taken_by', 'left')
            ->where('handlings.id_handling_type', $handlingTypeIdInbound);

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('work_order_containers.id_owner', $customerId);
        }

        $baseInContainerQuery = $this->db->get_compiled_select();

        $report = $this->db->select([
            '(SELECT booking_extensions.value FROM booking_extensions 
              LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
              WHERE booking_extensions.id_booking = bookings.id AND ref_extension_fields.field_name = "nopen"
              LIMIT 1) AS no_registration',
            '(SELECT booking_extensions.value FROM booking_extensions 
              LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
              WHERE booking_extensions.id_booking = bookings.id AND ref_extension_fields.field_name = "tapen"
              LIMIT 1) AS registration_date',
            'ref_branches.branch',
            'ref_branches.branch_type',
            'ref_warehouses.warehouse',
            'bookings.no_reference AS "bc_doc_/_reference_no"',
            'bookings.reference_date AS "bc_doc_/_reference_date"',
            'ref_booking_types.booking_type AS "bc_doc_/_booking_type"',
            'bookings.no_booking AS booking_no',
            'bookings.booking_date',
            'suppliers.name AS supplier',
            'bookings.vessel',
            'bookings.voyage',
            'work_order_goods.id_owner',
            'customers.name AS owner',
            'safe_conducts.no_safe_conduct AS safe_conduct_no',
            'safe_conducts.created_at AS safe_conduct_date',
            'safe_conducts.vehicle_type',
            'safe_conducts.driver',
            'safe_conducts.no_police AS police_no',
            'safe_conducts.expedition',
            'ref_eseals.no_eseal AS eseal_no',
            'work_orders.no_work_order AS job_no',
            'work_orders.completed_at AS transaction_date',
            '"GOODS" AS item_category',
            'ref_goods.no_goods AS item_no',
            'ref_goods.name AS item_name',
            'work_order_goods.quantity AS quantity',
            'ref_units.unit',
            '(ref_goods.unit_weight * work_order_goods.quantity) AS tonnage',
            '(ref_goods.unit_volume * work_order_goods.quantity) AS volume',
            'ref_positions.position',
            '"" AS container_type',
            '"" AS container_size',
            '"" AS container_seal',
            '"" AS container_status',
            'work_order_goods.status AS item_condition',
            'work_order_goods.status_danger',
            'work_order_goods.ex_no_container',
            'work_order_goods.no_pallet AS pallet_no',
            'work_order_goods.description',
            'admins.name AS admin_name',
            'tally.name AS tally_name',
        ])
            ->from('bookings')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer')
            ->join('ref_people AS suppliers', 'suppliers.id = bookings.id_supplier', 'left')
            ->join('bookings AS booking_in', 'booking_in.id = bookings.id_booking', 'left')
            ->join('safe_conducts', 'safe_conducts.id_booking = bookings.id', 'left')
            ->join('ref_eseals', 'ref_eseals.id = safe_conducts.id_eseal', 'left')
            ->join('work_orders', 'work_orders.id_safe_conduct = safe_conducts.id', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling', 'left')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods', 'left')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit', 'left')
            ->join('ref_positions', 'ref_positions.id = work_order_goods.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join(UserModel::$tableUser . ' AS admins', 'admins.id = work_orders.created_by', 'left')
            ->join(UserModel::$tableUser . ' AS tally', 'tally.id = work_orders.taken_by', 'left')
            ->where('handlings.id_handling_type', $handlingTypeIdInbound);

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('work_order_goods.id_owner', $customerId);
        }

        $baseInGoodsQuery = $this->db->get_compiled_select();

        $queryReport = "
          SELECT * FROM (
              SELECT * FROM ({$baseInContainerQuery}) AS in_container 
              UNION 
              SELECT * FROM ({$baseInGoodsQuery}) AS in_goods
          ) AS inbounds WHERE 1 = 1
        ";

        if (empty($filters) || !key_exists('data', $filters)) {
            $queryReport .= ' AND item_name IS NOT NULL';
        } else {
            if (key_exists('data', $filters) && !empty($filters['data'])) {
                if ($filters['data'] == 'realization') {
                    $queryReport .= ' AND item_name IS NOT NULL';
                }
            }

            if (key_exists('branch_type', $filters) && !is_null($filters['branch_type'])) {
                $queryReport .= ' AND branch_type IS NOT NULL';
                $queryReport .= ' AND branch_type = "' . $filters['branch_type'] . '"';
            }

            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $queryReport .= ' AND owner IN("' . implode('","', $filters['owner']) . '")';
                } else {
                    $queryReport .= ' AND owner = "' . $filters['owner'] . '"';
                }
            }

            if (key_exists('item', $filters) && !empty($filters['item'])) {
                if (is_array($filters['item'])) {
                    $queryReport .= ' AND item_name IN("' . implode('","', $filters['item']) . '")';
                } else {
                    $queryReport .= ' AND item_name = "' . $filters['item'] . '"';
                }
            }

            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
                if($filters['date_type'] == 'reference_date') {
                    $filters['date_type'] = '`bc_doc_/_reference_date`';
                }
                if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                    $queryReport .= ' AND DATE(' . $filters['date_type'] . ')>="' . sql_date_format($filters['date_from']) . '"';
                }
                if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                    $queryReport .= ' AND DATE(' . $filters['date_type'] . ')<="' . sql_date_format($filters['date_to']) . '"';
                }
            }

            if (!key_exists('q', $filters) || empty($filters['q'])) {
                $filters['q'] = $search;
            }

            $searchString = $filters['q'];
            $queryReport .= ' AND (
                booking_no LIKE "%' . $searchString . '%" 
                OR `bc_doc_/_reference_no` LIKE "%' . $searchString . '%" 
                OR job_no LIKE "%' . $searchString . '%" 
                OR safe_conduct_no LIKE "%' . $searchString . '%"
                OR item_name LIKE "%' . $searchString . '%"
                OR owner LIKE "%' . $searchString . '%"
            )';
        }

        if ($start < 0) {
            return $this->db->query($queryReport)->result_array();
        }

        $reportTotalQuery = $this->db->query("SELECT COUNT(*) AS total_record FROM ({$queryReport}) AS report");
        $reportTotals = $reportTotalQuery->row_array();
        if (!empty($reportTotals)) {
            $reportTotal = $reportTotals['total_record'];
        } else {
            $reportTotal = 0;
        }

        if (!empty($column)) {
            if($column == 'no') $column = 'transaction_date';
            $queryReport .= ' ORDER BY `' . $column . '` ' . $sort;
        } else {
            $queryReport .= ' ORDER BY transaction_date DESC';
        }
        $queryReport .= ' LIMIT ' . $start . ', ' . $length;

        $reportData = $this->db->query($queryReport)->result_array();

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
     * Get report BC outbound.
     *
     * @param array $filters
     * @return mixed
     */
    public function getReportOutbound($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $handlingTypeIdOutbound = get_setting('default_outbound_handling');
        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $report = $this->db->select([
            '(SELECT booking_extensions.value FROM booking_extensions 
              LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
              WHERE booking_extensions.id_booking = bookings.id_booking AND ref_extension_fields.field_name = "nopen"
              LIMIT 1) AS no_registration',
            '(SELECT booking_extensions.value FROM booking_extensions 
              LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
              WHERE booking_extensions.id_booking = bookings.id_booking AND ref_extension_fields.field_name = "tapen"
              LIMIT 1) AS registration_date',
            'ref_branches.branch',
            'ref_branches.branch_type',
            'ref_warehouses.warehouse',
            'booking_in.no_reference AS "bc_doc_/_reference_no_in"',
            'booking_in.reference_date AS "bc_doc_/_reference_date_in"',
            'booking_type_in.booking_type AS "bc_doc_/_booking_type_in"',
            'booking_in.no_booking AS booking_no_in',
            'booking_in.booking_date AS booking_date_in',
            'bookings.no_reference AS "bc_doc_/_reference_no"',
            'bookings.reference_date AS "bc_doc_/_reference_date"',
            'ref_booking_types.booking_type AS "bc_doc_/_booking_type"',
            'bookings.no_booking AS booking_no',
            'bookings.booking_date',
            'suppliers.name AS supplier',
            'bookings.vessel',
            'bookings.voyage',
            'work_order_containers.id_owner',
            'customers.name AS owner',
            'safe_conducts.no_safe_conduct AS safe_conduct_no',
            'safe_conducts.created_at AS safe_conduct_date',
            'safe_conducts.vehicle_type',
            'safe_conducts.driver',
            'safe_conducts.no_police AS police_no',
            'safe_conducts.expedition',
            'ref_eseals.no_eseal AS eseal_no',
            'work_orders.no_work_order AS job_no',
            'work_orders.completed_at AS transaction_date',
            '"CONTAINER" AS item_category',
            'ref_containers.no_container AS item_no',
            'ref_containers.no_container AS item_name',
            'work_order_containers.quantity AS quantity',
            '"Cargo" AS unit',
            '"" AS tonnage',
            '"" AS volume',
            'ref_positions.position',
            'ref_containers.type AS container_type',
            'ref_containers.size AS container_size',
            'work_order_containers.seal AS container_seal',
            'IF(work_order_containers.is_empty = 0, "FULL", "EMPTY") AS container_status',
            'work_order_containers.status AS item_condition',
            'work_order_containers.status_danger',
            '"" AS no_ex_container',
            '"" AS pallet_no',
            'work_order_containers.description',
            'admins.name AS admin_name',
            'tally.name AS tally_name',
        ])
            ->from('bookings')
            ->join('booking_references', 'booking_references.id_booking = bookings.id', 'left')
            ->join('bookings AS booking_in', 'booking_in.id = IFNULL(booking_references.id_booking_reference, bookings.id)', 'left')
            //->join('bookings AS booking_in', 'booking_in.id = bookings.id_booking', 'left')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer')
            ->join('ref_people AS suppliers', 'suppliers.id = bookings.id_supplier', 'left')
            ->join('safe_conducts', 'safe_conducts.id_booking = bookings.id', 'left')
            ->join('ref_eseals', 'ref_eseals.id = safe_conducts.id_eseal', 'left')
            ->join('work_orders', 'work_orders.id_safe_conduct = safe_conducts.id', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling', 'left')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id AND IFNULL(work_order_containers.id_booking_reference, bookings.id) = booking_in.id')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container', 'left')
            ->join('ref_positions', 'ref_positions.id = work_order_containers.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_booking_types AS booking_type_in', 'booking_type_in.id = booking_in.id_booking_type', 'left')
            ->join(UserModel::$tableUser . ' AS admins', 'admins.id = work_orders.created_by', 'left')
            ->join(UserModel::$tableUser . ' AS tally', 'tally.id = work_orders.taken_by', 'left')
            ->where('handlings.id_handling_type', $handlingTypeIdOutbound);

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('work_order_containers.id_owner', $customerId);
        }

        $baseOutContainerQuery = $this->db->get_compiled_select();

        $report = $this->db->select([
            '(SELECT booking_extensions.value FROM booking_extensions 
              LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
              WHERE booking_extensions.id_booking = bookings.id AND ref_extension_fields.field_name = "nopen"
              LIMIT 1) AS no_registration',
            '(SELECT booking_extensions.value FROM booking_extensions 
              LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
              WHERE booking_extensions.id_booking = bookings.id AND ref_extension_fields.field_name = "tapen"
              LIMIT 1) AS registration_date',
            'ref_branches.branch',
            'ref_branches.branch_type',
            'ref_warehouses.warehouse',
            'booking_in.no_reference AS "bc_doc_/_reference_no_in"',
            'booking_in.reference_date AS "bc_doc_/_reference_date_in"',
            'booking_type_in.booking_type AS "bc_doc_/_booking_type_in"',
            'booking_in.no_booking AS booking_no_in',
            'booking_in.booking_date AS booking_date_in',
            'bookings.no_reference AS "bc_doc_/_reference_no"',
            'bookings.reference_date AS "bc_doc_/_reference_date"',
            'ref_booking_types.booking_type AS "bc_doc_/_booking_type"',
            'bookings.no_booking AS booking_no',
            'bookings.booking_date',
            'suppliers.name AS supplier',
            'bookings.vessel',
            'bookings.voyage',
            'work_order_goods.id_owner',
            'customers.name AS owner',
            'safe_conducts.no_safe_conduct AS safe_conduct_no',
            'safe_conducts.created_at AS safe_conduct_date',
            'safe_conducts.vehicle_type',
            'safe_conducts.driver',
            'safe_conducts.no_police AS police_no',
            'safe_conducts.expedition',
            'ref_eseals.no_eseal AS eseal_no',
            'work_orders.no_work_order AS job_no',
            'work_orders.completed_at AS transaction_date',
            '"GOODS" AS item_category',
            'ref_goods.no_goods AS item_no',
            'ref_goods.name AS item_name',
            'work_order_goods.quantity AS quantity',
            'ref_units.unit',
            '(ref_goods.unit_weight * work_order_goods.quantity) AS tonnage',
            '(ref_goods.unit_volume * work_order_goods.quantity) AS volume',
            'ref_positions.position',
            '"" AS container_type',
            '"" AS container_size',
            '"" AS container_seal',
            '"" AS container_status',
            'work_order_goods.status AS item_condition',
            'work_order_goods.status_danger',
            'work_order_goods.ex_no_container',
            'work_order_goods.no_pallet AS pallet_no',
            'work_order_goods.description',
            'admins.name AS admin_name',
            'tally.name AS tally_name',
        ])
            ->from('bookings')
            ->join('booking_references', 'booking_references.id_booking = bookings.id', 'left')
            ->join('bookings AS booking_in', 'booking_in.id = IFNULL(booking_references.id_booking_reference, bookings.id)', 'left')
            //->join('bookings AS booking_in', 'booking_in.id = bookings.id_booking', 'left')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer')
            ->join('ref_people AS suppliers', 'suppliers.id = bookings.id_supplier', 'left')
            ->join('safe_conducts', 'safe_conducts.id_booking = bookings.id', 'left')
            ->join('ref_eseals', 'ref_eseals.id = safe_conducts.id_eseal', 'left')
            ->join('work_orders', 'work_orders.id_safe_conduct = safe_conducts.id', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling', 'left')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id AND IFNULL(work_order_goods.id_booking_reference, bookings.id) = booking_in.id')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods', 'left')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit', 'left')
            ->join('ref_positions', 'ref_positions.id = work_order_goods.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_booking_types AS booking_type_in', 'booking_type_in.id = booking_in.id_booking_type', 'left')
            ->join(UserModel::$tableUser . ' AS admins', 'admins.id = work_orders.created_by', 'left')
            ->join(UserModel::$tableUser . ' AS tally', 'tally.id = work_orders.taken_by', 'left')
            ->where('handlings.id_handling_type', $handlingTypeIdOutbound);

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('work_order_goods.id_owner', $customerId);
        }

        $baseOutGoodsQuery = $this->db->get_compiled_select();

        $queryReport = "
          SELECT * FROM (
              SELECT * FROM ({$baseOutContainerQuery}) AS in_container 
              UNION
              SELECT * FROM ({$baseOutGoodsQuery}) AS in_goods
          ) AS outbounds WHERE 1 = 1
        ";

        if (empty($filters) || !key_exists('data', $filters)) {
            $queryReport .= ' AND item_name IS NOT NULL';
        } else {
            if (key_exists('data', $filters) && !empty($filters['data'])) {
                if ($filters['data'] == 'realization') {
                    $queryReport .= ' AND item_name IS NOT NULL';
                }
            }

            if (key_exists('branch_type', $filters) && !is_null($filters['branch_type'])) {
                $queryReport .= ' AND branch_type IS NOT NULL';
                $queryReport .= ' AND branch_type = "' . $filters['branch_type'] . '"';
            }

            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $queryReport .= ' AND owner IN("' . implode('","', $filters['owner']) . '")';
                } else {
                    $queryReport .= ' AND owner = "' . $filters['owner'] . '"';
                }
            }

            if (key_exists('item', $filters) && !empty($filters['item'])) {
                if (is_array($filters['item'])) {
                    $queryReport .= ' AND item_name IN("' . implode('","', $filters['item']) . '")';
                } else {
                    $queryReport .= ' AND item_name = "' . $filters['item'] . '"';
                }
            }

            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
                if($filters['date_type'] == 'reference_date') {
                    $filters['date_type'] = '`bc_doc_/_reference_date`';
                }
                if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                    $queryReport .= ' AND DATE(' . $filters['date_type'] . ')>="' . sql_date_format($filters['date_from']) . '"';
                }
                if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                    $queryReport .= ' AND DATE(' . $filters['date_type'] . ')<="' . sql_date_format($filters['date_to']) . '"';
                }
            }

            if (!key_exists('q', $filters) || empty($filters['q'])) {
                $filters['q'] = $search;
            }

            $searchString = $filters['q'];
            $queryReport .= ' AND (
                booking_no LIKE "%' . $searchString . '%" 
                OR `bc_doc_/_reference_no_in` LIKE "%' . $searchString . '%" 
                OR `bc_doc_/_reference_no` LIKE "%' . $searchString . '%" 
                OR job_no LIKE "%' . $searchString . '%" 
                OR safe_conduct_no LIKE "%' . $searchString . '%"
                OR item_name LIKE "%' . $searchString . '%"
                OR owner LIKE "%' . $searchString . '%"
            )';
        }

        if ($start < 0) {
            return $this->db->query($queryReport)->result_array();
        }

        $reportTotalQuery = $this->db->query("SELECT COUNT(*) AS total_record FROM ({$queryReport}) AS report");
        $reportTotals = $reportTotalQuery->row_array();
        if (!empty($reportTotals)) {
            $reportTotal = $reportTotals['total_record'];
        } else {
            $reportTotal = 0;
        }

        if (!empty($column)) {
            if($column == 'no') $column = 'transaction_date';
            $queryReport .= ' ORDER BY `' . $column . '` ' . $sort;
        } else {
            $queryReport .= ' ORDER BY transaction_date DESC';
        }
        $queryReport .= ' LIMIT ' . $start . ', ' . $length;

        $reportData = $this->db->query($queryReport)->result_array();

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
     * Get stock mutations.
     *
     * @param null $filters
     * @param bool $collapseData
     * @return array
     */
    public function getReportMutation($filters = null, $collapseData = false)
    {
        $branchId = get_active_branch('id');

        $handlingTypeIdMoveIn = get_setting('default_moving_in_handling');
        $handlingTypeIdMoveOut = get_setting('default_moving_out_handling');
        $handlingTypeIdInbound = get_setting('default_inbound_handling');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $report = $this->db
            ->select([
                'ref_branches.branch',
                '(IFNULL(bookings.id_booking, bookings.id)) id_booking_ref',
                '(IFNULL(booking_in.no_reference, bookings.no_reference)) AS "bc_doc_/_reference_no_in"',
                '(IFNULL(booking_in.reference_date, bookings.reference_date)) AS "bc_doc_/_reference_date_in"',
                '(IFNULL(booking_type_in.booking_type, ref_booking_types.booking_type)) AS "bc_doc_/_booking_type_in"',
                '"NO DO" AS do_no',
                'customers.name AS owner',
                'bookings.id AS id_booking',
                'bookings.no_reference AS "bc_doc_/_reference_no"',
                'bookings.reference_date AS "bc_doc_/_reference_date"',
                'ref_booking_types.booking_type AS "bc_doc_/_booking_type"',
                'id_handling_type',
                'handling_type',
                'work_orders.no_work_order AS job_no',
                'work_orders.completed_at AS transaction_date',
                'work_orders.description AS activity_description',
                'admins.name AS admin_name',
                'tally.name AS tally_name',
                '"CONTAINER" AS item_type',
                'ref_containers.no_container AS item_no',
                'ref_containers.no_container AS item_name',
                'ref_warehouses.warehouse',
                'ref_positions.position',
                '"" AS tonnage',
                '"" AS volume',
                '"Cargo" AS unit',
                '0 AS beginning_balance',
                '0 AS final_balance',
                '(multiplier_container * CAST(quantity AS SIGNED)) AS quantity',
                'IF(multiplier_container = 1, (multiplier_container * CAST(quantity AS SIGNED)), 0) AS quantity_debit',
                'IF(multiplier_container = -1, (multiplier_container * CAST(quantity AS SIGNED)), 0) AS quantity_credit',
            ])
            ->from('bookings')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer')
            ->join('bookings AS booking_in', 'booking_in.id = bookings.id_booking', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_booking_types AS booking_type_in', 'booking_in.id_booking_type = booking_type_in.id', 'left')
            ->join('handlings', 'handlings.id_booking = bookings.id')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('work_orders', 'work_orders.id_handling = handlings.id')
            ->join('work_order_containers', 'work_order_containers.id = work_orders.id')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container')
            ->join('ref_positions', 'ref_positions.id = work_order_containers.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left')
            ->join(UserModel::$tableUser . ' AS admins', 'admins.id = work_orders.created_by', 'left')
            ->join(UserModel::$tableUser . ' AS tally', 'tally.id = work_orders.taken_by', 'left')
            ->where('handlings.id_handling_type !=', $handlingTypeIdMoveIn)
            ->where('handlings.id_handling_type !=', $handlingTypeIdMoveOut)
            ->where('handlings.status !=', 'REJECTED')
            ->order_by('work_orders.completed_at');

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('work_order_containers.id_owner', $customerId);
        }

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $report->where('DATE(work_orders.completed_at)>=', sql_date_format($filters['date_from'], false));
        }

        if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
            $report->where('DATE(work_orders.completed_at)<=', sql_date_format($filters['date_to'], false));
        }

        $baseContainerQuery = $this->db->get_compiled_select();

        $report = $this->db
            ->select([
                'ref_branches.branch',
                '(IFNULL(bookings.id_booking, bookings.id)) id_booking_ref',
                '(IFNULL(booking_in.no_reference, bookings.no_reference)) AS "bc_doc_/_reference_no_in"',
                '(IFNULL(booking_in.reference_date, bookings.reference_date)) AS "bc_doc_/_reference_date_in"',
                '(IFNULL(booking_type_in.booking_type, ref_booking_types.booking_type)) AS "bc_doc_/_booking_type_in"',
                '"NO DO" AS do_no',
                'customers.name AS owner',
                'bookings.id AS id_booking',
                'bookings.no_reference AS "bc_doc_/_reference_no"',
                'bookings.reference_date AS "bc_doc_/_reference_date"',
                'ref_booking_types.booking_type AS "bc_doc_/_booking_type"',
                'id_handling_type',
                'handling_type',
                'work_orders.no_work_order AS job_no',
                'work_orders.completed_at AS transaction_date',
                'work_orders.description AS activity_description',
                'admins.name AS admin_name',
                'tally.name AS tally_name',
                '"ITEM" AS item_type',
                'no_goods AS item_no',
                'ref_goods.name AS item_name',
                'ref_warehouses.warehouse',
                'ref_positions.position',
                '(ref_goods.unit_weight * work_order_goods.quantity) AS tonnage',
                '(ref_goods.unit_volume * work_order_goods.quantity) AS volume',
                'unit',
                '0 AS beginning_balance',
                '0 AS final_balance',
                '(multiplier_goods * CAST(quantity AS SIGNED)) AS quantity',
                'IF(multiplier_goods = 1, (multiplier_goods * CAST(quantity AS SIGNED)), 0) AS quantity_debit',
                'IF(multiplier_goods = -1, (multiplier_goods * CAST(quantity AS SIGNED)), 0) AS quantity_credit',
            ])
            ->from('bookings')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer')
            ->join('bookings AS booking_in', 'booking_in.id = bookings.id_booking', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_booking_types AS booking_type_in', 'booking_in.id_booking_type = booking_type_in.id', 'left')
            ->join('handlings', 'handlings.id_booking = bookings.id')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('work_orders', 'work_orders.id_handling = handlings.id')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit', 'left')
            ->join('ref_positions', 'ref_positions.id = work_order_goods.id_position', 'left')
            ->join('ref_warehouses', 'ref_warehouses.id = ref_positions.id_warehouse', 'left')
            ->join(UserModel::$tableUser . ' AS admins', 'admins.id = work_orders.created_by', 'left')
            ->join(UserModel::$tableUser . ' AS tally', 'tally.id = work_orders.taken_by', 'left')
            ->where('handlings.id_handling_type !=', $handlingTypeIdMoveIn)
            ->where('handlings.id_handling_type !=', $handlingTypeIdMoveOut)
            ->where('handlings.status !=', 'REJECTED')
            ->order_by('work_orders.completed_at');

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('work_order_goods.id_owner', $customerId);
        }

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $report->where('DATE(work_orders.completed_at)>=', sql_date_format($filters['date_from'], false));
        }

        if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
            $report->where('DATE(work_orders.completed_at)<=', sql_date_format($filters['date_to'], false));
        }

        $baseGoodsQuery = $this->db->get_compiled_select();

        $queryReport = "
          SELECT * FROM (
              SELECT * FROM ({$baseContainerQuery}) AS containers 
              UNION ALL
              SELECT * FROM ({$baseGoodsQuery}) AS goods
          ) AS mutations WHERE 1 = 1
        ";

        if (!empty($filters)) {
            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $queryReport .= ' AND owner IN("' . implode('","', $filters['owner']) . '")';
                } else {
                    $queryReport .= ' AND owner = "' . $filters['owner'] . '"';
                }
            }

            if (key_exists('item', $filters) && !empty($filters['item'])) {
                if (is_array($filters['item'])) {
                    foreach ($filters['item'] as &$item) $item = addslashes($item['item']);
                    $queryReport .= ' AND item_name IN("' . implode('","', $filters['item']) . '")';
                } else {
                    $queryReport .= ' AND item_name = "' . addslashes($filters['item']) . '"';
                }
            }

            if (key_exists('do', $filters) && !empty($filters['do'])) {
                if (is_array($filters['do'])) {
                    $queryReport .= ' AND do_no IN("' . implode('","', $filters['do']) . '")';
                } else {
                    $queryReport .= ' AND do_no = "' . $filters['do'] . '"';
                }
            }

            if (key_exists('booking', $filters) && !empty($filters['booking'])) {
                if (is_array($filters['booking'])) {
                    $queryReport .= ' AND id_booking_ref IN("' . implode('","', $filters['booking']) . '")';
                } else {
                    $queryReport .= ' AND id_booking_ref = "' . $filters['booking'] . '"';
                }
            }

            /*
            if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                $queryReport .= ' AND DATE(transaction_date)>="' . sql_date_format($filters['date_from'], false) . '"';
            }

            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $queryReport .= ' AND DATE(transaction_date)<="' . sql_date_format($filters['date_to'], false) . '"';
            }
            */
        }
        $queryReport .= ' ORDER BY do_no, transaction_date';

        // raw handling transaction order by transaction date
        // (UNLOAD handling must precedent of other handling in minutes, second precise within unfiltered data)
        // IF NOT THEN THE DATA MUST BE WRONG! NOT THE CODE!
        $mutations = $this->db->query($queryReport)->result_array();

        /**
         * group per no reference, per item
         * [
         *      16021601068320171220000039 => [
         *          DO NUMBER => [
         *          HDMU6326362 => [
         *              [
         *                  [owner_name] => PT. JAPFA COMFEED INDONESIA TBK
         *                  [handling_type] => UNLOAD -------------------------> SHOULD BE FIRST (Beginning balance start from zero)
         *                  [...] => [...]
         *              ],
         *              [
         *                  [owner_name] => PT. JAPFA COMFEED INDONESIA TBK
         *                  [handling_type] => STRIPPING ---------------------> MAY BE FIRST IF FILTERED (Find out beginning balance)
         *                  [...] => [...]
         *              ],
         *              [
         *                  [owner_name] => PT. JAPFA COMFEED INDONESIA TBK
         *                  [handling_type] => LOAD -------------------------> MAY BE FIRST IF FILTERED (Find out beginning balance)
         *                  [...] => [...]
         *              ]
         *          ],
         *          ... => [...]
         *          ]
         *      ]
         * ]
         */
        $reportCards = [];
        foreach ($mutations as &$mutation) {
            $beginningBalance = 0;
            // skip for UNLOAD handling (it always start from 0)
            if ($mutation['id_handling_type'] != $handlingTypeIdInbound) {
                // try to find last balance if exist in mutation list by booking, by item
                // rather than calculate from previous transactions, WE JUST NEED beginning balance per booking, per item.
                $isFoundLastMutation = false;
                if (key_exists($mutation['id_booking_ref'], $reportCards)) {
                    if (key_exists($mutation['do_no'], $reportCards[$mutation['id_booking_ref']])) {
                        if (key_exists($mutation['item_name'], $reportCards[$mutation['id_booking_ref']][$mutation['do_no']])) {
                            $lastMutation = end($reportCards[$mutation['id_booking_ref']][$mutation['do_no']][$mutation['item_name']]);
                            $beginningBalance = $lastMutation['final_balance'];
                            $isFoundLastMutation = true;
                        }
                    }
                }

                // if not found we need to find out beginning balance before filtered transaction.
                if (!$isFoundLastMutation) {
                    if (!empty($filters)) {
                        // call recursively this method by turning "date_from" as input of "date_to"
                        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                            $transactionBefore = $this->getReportMutation([
                                'booking' => $mutation['id_booking_ref'],
                                'do' => $mutation['do_no'],
                                'item' => $mutation['item_name'],
                                'date_to' => date('Y-m-d', strtotime('-1 days', strtotime($filters['date_from'])))
                            ]);
                            if (!empty($transactionBefore)) {
                                $references = end($transactionBefore);
                                if (!empty($references)) {
                                    $dos = end($references);
                                    if (!empty($dos)) {
                                        $items = end($dos);
                                        if (!empty($items)) {
                                            $item = end($items);
                                            $beginningBalance = $item['final_balance'];
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            // push new mutation
            $mutation['beginning_balance'] = $beginningBalance;
            $mutation['final_balance'] = $mutation['beginning_balance'] + $mutation['quantity_debit'] + $mutation['quantity_credit'];
            $reportCards[$mutation['id_booking_ref']][$mutation['do_no']][$mutation['item_name']][] = $mutation;
        }

        if ($collapseData) {
            $reportFlats = [];
            foreach ($reportCards as $references) {
                foreach ($references as $dos) {
                    foreach ($dos as $items) {
                        foreach ($items as $item) {
                            $reportFlats[] = $item;
                        }
                    }
                }
            }
            return $reportFlats;
        }

        return $reportCards;
    }

    /**
     * Get container data by its number
     * @param null $itemName
     * @param $page
     * @return mixed
     */
    public function getItemsByName($itemName, $page = null)
    {
        $unionQuery = "
          SELECT id, 'CONTAINER' AS type, '' AS no_item, no_container AS item_name 
          FROM ref_containers WHERE ref_containers.is_deleted = FALSE
          UNION
          SELECT id, 'GOODS' AS type, no_goods AS no_item, name AS item_name 
          FROM ref_goods WHERE ref_goods.is_deleted = FALSE";
        $query = "SELECT * FROM ($unionQuery) AS ref_items";

        if (is_array($itemName)) {
            $itemList = "'" . implode("','", $itemName) . "'";
            $query .= " WHERE item_name IN({$itemList})";
        } else {
            $query .= " WHERE item_name LIKE '%{$itemName}%'";
        }

        if (!empty($page) || $page != 0) {
            $totalQuery = $this->db->query("SELECT COUNT(*) AS total_record FROM ({$query}) AS item_data");
            $reportTotals = $totalQuery->row_array();
            if (!empty($reportTotals)) {
                $totalData = $reportTotals['total_record'];
            } else {
                $totalData = 0;
            }

            $query .= ' LIMIT ' . (10 * ($page - 1)) . ', ' . 10;
            $data = $this->db->query($query)->result_array();

            return [
                'results' => $data,
                'total_count' => $totalData
            ];
        }

        return $this->db->query($query)->result_array();
    }
}