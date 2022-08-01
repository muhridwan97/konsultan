<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportModel extends CI_Model
{
    protected $tableHoliday = 'schedule_holidays';
    protected $tableScheduleDivision = 'schedule_division';
    protected $tableSchedule = 'schedules';
    protected $tableEmployees = 'ref_employees';
    protected $tablePositionSalaryComponent = 'position_salary_components';
    protected $tablePosition = 'ref_positions';
    protected $tableSalaryComponent = 'ref_salary_components';
    protected $tableLocation = 'ref_work_locations';

    /**
     * ReportModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        if ($this->config->item('sso_enable')) {
            $this->tableHoliday = env('DB_HR_DATABASE') . '.schedule_holidays';
            $this->tableScheduleDivision = env('DB_HR_DATABASE') . '.schedule_division';
            $this->tableSchedule = env('DB_HR_DATABASE') . '.schedules';
            $this->tableEmployees = env('DB_HR_DATABASE') . '.ref_employees';
            $this->tablePositionSalaryComponent = env('DB_HR_DATABASE') . '.position_salary_components';
            $this->tablePosition = env('DB_HR_DATABASE') . '.ref_positions';
            $this->tableSalaryComponent = env('DB_HR_DATABASE') . '.ref_salary_components';
            $this->tableLocation = env('DB_HR_DATABASE') . '.ref_work_locations';
        }
    }

    /**
     * Get activity container summary.
     *
     * @param $category
     * @param array $filters
     * @return array|null
     */
    public function getActivityContainerSummary($category, $filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $handlingTypeIdInbound = get_setting('default_inbound_handling');
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $baseQuery = $this->db
            ->select([
                'COUNT(work_order_containers.id) AS total_transaction',
                'SUM(work_order_containers.quantity) AS total_quantity',
                'SUM(IF(ref_containers.size = 20, 1, 0)) AS total_20',
                'SUM(IF(ref_containers.size = 40, 1, 0)) AS total_40',
                'SUM(IF(ref_containers.size = 45, 1, 0)) AS total_45',
            ])
            ->from('bookings')
            ->join('handlings', 'handlings.id_booking = bookings.id', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id', 'left')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id', 'left')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container', 'left');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $baseQuery->where('id_owner', $customerId);
        }

        if (key_exists('owner', $filters) && !empty($filters['owner'])) {
            $baseQuery->where_in('work_order_containers.id_owner', $filters['owner']);
        }

        if (key_exists('size', $filters) && !empty($filters['size'])) {
            $baseQuery->where_in('ref_containers.size', $filters['size']);
        }

        if ($category == 'INBOUND') {
            $baseQuery->where('id_handling_type', $handlingTypeIdInbound);
        } else {
            $baseQuery->where('id_handling_type', $handlingTypeIdOutbound);
        }

        if (key_exists('data', $filters) && !empty($filters['data'])) {
            if ($filters['data'] == 'realization') {
                $baseQuery->where('id_container IS NOT NULL');
            }
        } else {
            $baseQuery->where('id_container IS NOT NULL');
        }

        return $baseQuery->get()->row_array();
    }

    /**
     * Get activity goods summary.
     *
     * @param $category
     * @param array $filters
     * @return array|null
     */
    public function getActivityGoodsSummary($category, $filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $handlingTypeIdInbound = get_setting('default_inbound_handling');
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $baseQuery = $this->db
            ->select([
                'COUNT(work_order_goods.id) AS total_transaction',
                'SUM(work_order_goods.quantity) AS total_quantity',
                'SUM(work_order_goods.quantity * ref_goods.unit_weight) AS total_weight',
                'SUM(work_order_goods.quantity * ref_goods.unit_volume) AS total_volume',
            ])
            ->from('bookings')
            ->join('booking_references', 'booking_references.id_booking = bookings.id', 'left')
            ->join('bookings AS booking_in', 'booking_in.id = IFNULL(booking_references.id_booking_reference, bookings.id)', 'left')
            ->join('handlings', 'handlings.id_booking = bookings.id', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id', 'left')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id AND IFNULL(work_order_goods.id_booking_reference, bookings.id) = booking_in.id', 'left')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods', 'left');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $baseQuery->where('id_owner', $customerId);
        }

        if (key_exists('owner', $filters) && !empty($filters['owner'])) {
            $baseQuery->where_in('work_order_goods.id_owner', $filters['owner']);
        }

        if (key_exists('goods', $filters) && !empty($filters['goods'])) {
            $baseQuery->where_in('work_order_goods.id_goods', $filters['goods']);
        }

        if ($category == 'INBOUND') {
            $baseQuery->where('id_handling_type', $handlingTypeIdInbound);
        } else {
            $baseQuery->where('id_handling_type', $handlingTypeIdOutbound);
        }

        if (key_exists('data', $filters) && !empty($filters['data'])) {
            if ($filters['data'] == 'realization') {
                $baseQuery->where('id_goods IS NOT NULL');
            }
        } else {
            $baseQuery->where('id_goods IS NOT NULL');
        }

        return $baseQuery->get()->row_array();
    }

    /**
     * Get activity report container.
     *
     * @param string $category
     * @param array $filters
     * @param null $branch
     * @return array|int
     */
    public function getReportActivityContainer($category = 'INBOUND', $filters = [], $branch = null)
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : if_empty(get_active_branch('id'), $branch);

        $handlingTypeIdInbound = get_setting('default_inbound_handling');
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $this->db->start_cache();

        $report = $this->db->select([
            '(SELECT booking_extensions.value FROM booking_extensions 
              LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
              WHERE booking_extensions.id_booking = bookings.id AND ref_extension_fields.field_name = "nopen"
              LIMIT 1) AS no_registration',
            '(SELECT booking_extensions.value FROM booking_extensions 
              LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
              WHERE booking_extensions.id_booking = bookings.id AND ref_extension_fields.field_name = "tapen"
              LIMIT 1) AS registration_date',
            'invoice_documents.no_invoice',
            'bookings.id_booking',
            'bookings.no_booking',
            'bookings.no_reference',
            'bookings.reference_date',
            'ref_booking_types.booking_type',
            'bookings.booking_date',
            'bookings.vessel',
            'bookings.voyage',
            'bookings.id_customer AS id_owner',
            'customers.name AS owner_name',
            'safe_conducts.id AS id_safe_conduct',
            'safe_conducts.no_safe_conduct',
            'safe_conducts.driver',
            'safe_conducts.no_police',
            'safe_conducts.expedition',
            'safe_conducts.security_in_date',
            'safe_conducts.security_out_date',
            'source_warehouses.name AS source_warehouse',
            'source_warehouses.region AS source_warehouse_region',
            'handlings.id AS id_handling',
            'handlings.no_handling',
            'work_orders.id AS id_work_order',
            'work_orders.no_work_order',
            'work_orders.gate_in_date',
            'work_orders.gate_out_date',
            'work_orders.taken_at',
            'work_orders.completed_at',
            'work_order_containers.id_container',
            'ref_containers.no_container',
            'ref_containers.type AS container_type',
            'ref_containers.size AS container_size',
            'work_order_containers.seal',
            'ref_positions.position',
            'work_order_containers.is_empty',
            'work_order_containers.is_hold',
            'work_order_containers.status',
            'work_order_containers.status_danger',
            'work_order_containers.description AS container_description',
            'booking_in.id AS id_booking_in',
            'booking_in.no_booking AS no_booking_in',
            'booking_in.no_reference AS no_reference_in',
            'booking_in.reference_date AS reference_date_in',
            'booking_type_in.booking_type AS booking_type_in',
        ])
            ->from('bookings')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('booking_references', 'booking_references.id_booking = bookings.id', 'left')
            ->join('bookings AS booking_in', 'booking_in.id = IFNULL(booking_references.id_booking_reference, bookings.id)', 'left')
            //->join('bookings AS booking_in', 'booking_in.id = IFNULL(bookings.id_booking, bookings.id)', 'left')
            ->join('handlings', 'handlings.id_booking = bookings.id', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id', 'left')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id AND IFNULL(work_order_containers.id_booking_reference, bookings.id) = booking_in.id', 'left')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container', 'left')
            ->join('ref_positions', 'ref_positions.id = work_order_containers.id_position', 'left')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('ref_people AS source_warehouses', 'source_warehouses.id = safe_conducts.id_source_warehouse', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_booking_types AS booking_type_in', 'booking_type_in.id = booking_in.id_booking_type', 'left')
            ->join("(
                SELECT 
                    upload_documents.id_upload,
                    MAX(upload_documents.no_document) as no_invoice
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'Invoice'
                GROUP BY id_upload              
            ) AS invoice_documents", 'invoice_documents.id_upload = bookings.id_upload', 'left');

        if ($category == 'INBOUND') {
            $report->where('id_handling_type', $handlingTypeIdInbound);
        } else {
            $report->where('id_handling_type', $handlingTypeIdOutbound);
        }

        if ($start < 0) {
            $report->order_by('completed_at', 'desc');
        }

        if (empty($filters) || !key_exists('data', $filters)) {
            $report->where('id_container IS NOT NULL');
        } else {
            if (key_exists('data', $filters) && !empty($filters['data'])) {
                if ($filters['data'] == 'realization') {
                    $report->where('id_container IS NOT NULL');
                }
            } else {
                $report->where('id_container IS NOT NULL');
            }

            if (key_exists('branch_type', $filters) && !is_null($filters['branch_type'])) {
                $report->where('branch_type IS NOT NULL');
                $report->where('branch_type', $filters['branch_type']);
            }

            if (key_exists('branch', $filters) && !empty($filters['branch'])) {
                if (is_array($filters['branch'])) {
                    $report->where_in('bookings.id_branch', $filters['branch']);
                } else {
                    $report->where('bookings.id_branch', $filters['branch']);
                }
            }

            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $report->where_in('id_owner', $filters['owner']);
                } else {
                    $report->where('id_owner', $filters['owner']);
                }
            }

            if (key_exists('container', $filters) && !empty($filters['container'])) {
                if (is_array($filters['container'])) {
                    $report->where_in('id_container', $filters['container']);
                } else {
                    $report->where('id_container', $filters['container']);
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
            }

            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
                if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                    $report->where('DATE(' . $filters['date_type'] . ')>=', sql_date_format($filters['date_from'], false));
                }
                if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                    $report->where('DATE(' . $filters['date_type'] . ')<=', sql_date_format($filters['date_to'], false));
                }
            }
        }

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('id_owner', $customerId);
        }

        if (key_exists('total_size', $filters) && !empty($filters['total_size'])) {
            if ($filters['total_size'] != 'all') {
                $report->where('ref_containers.size', $filters['total_size']);
            }
            $this->db->stop_cache();
            $this->db->flush_cache();

            return $report->count_all_results();
        }

        //if (key_exists('search', $filters) && !empty($filters['search'])) {
        //    $search = (is_array($filters['search']) ? $search : $filters['search']);
        //}

        if (!empty($search)) {
            $report
                ->group_start()
                ->like('bookings.no_booking', $search)
                ->or_like('booking_in.no_booking', $search)
                ->or_like('bookings.no_reference', $search)
                ->or_like('booking_in.no_reference', $search)
                ->or_like('bookings.reference_date', $search)
                ->or_like('booking_in.reference_date', $search)
                ->or_like('bookings.booking_date', $search)
                ->or_like('booking_in.booking_date', $search)
                ->or_like('bookings.vessel', $search)
                ->or_like('bookings.voyage', $search)
                ->or_like('customers.name', $search)
                ->or_like('no_safe_conduct', $search)
                ->or_like('driver', $search)
                ->or_like('no_police', $search)
                ->or_like('safe_conducts.expedition', $search)
                ->or_like('security_in_date', $search)
                ->or_like('security_out_date', $search)
                ->or_like('no_handling', $search)
                ->or_like('no_work_order', $search)
                ->or_like('gate_in_date', $search)
                ->or_like('gate_out_date', $search)
                ->or_like('taken_at', $search)
                ->or_like('completed_at', $search)
                ->or_like('no_container', $search)
                ->or_like('ref_containers.type', $search)
                ->or_like('ref_containers.size', $search)
                ->or_like('seal', $search)
                ->or_like('position', $search)
                ->or_like('is_empty', $search)
                ->or_like('is_hold', $search)
                ->or_like('work_order_containers.status', $search)
                ->or_like('status_danger', $search)
                ->or_like('work_order_containers.description', $search)
                ->group_end();
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $report->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $reportTotal = $this->db->count_all_results();
        if ($column == 'no') $column = 'bookings.id';
        $page = $report->order_by($column, $sort)->limit($length, $start);
        $reportData = $page->get()->result_array();

        foreach ($reportData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => get_url_param('draw', $this->input->post('draw')),
            "recordsTotal" => count($reportData),
            "recordsFiltered" => $reportTotal,
            "data" => $reportData
        ];

        $this->db->flush_cache();

        return $pageData;
    }

    /**
     * Get report inbound.
     *
     * @param string $category
     * @param array $filters
     * @return array|int
     */
    public function getReportActivityGoods($category = 'INBOUND', $filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $handlingTypeIdInbound = get_setting('default_inbound_handling');
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $this->db->start_cache();

        $defaultSelect = [
            '(SELECT booking_extensions.value FROM booking_extensions 
              LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
              WHERE booking_extensions.id_booking = bookings.id AND ref_extension_fields.field_name = "nopen"
              LIMIT 1) AS no_registration',
            '(SELECT booking_extensions.value FROM booking_extensions 
              LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
              WHERE booking_extensions.id_booking = bookings.id AND ref_extension_fields.field_name = "tapen"
              LIMIT 1) AS registration_date',
            'invoice_documents.no_invoice',
            'bookings.id AS id_booking',
            'bookings.id_booking AS id_booking_ref',
            'bookings.no_booking',
            'bookings.no_reference',
            'bookings.reference_date',
            'ref_booking_types.booking_type',
            'bookings.booking_date',
            'bookings.vessel',
            'bookings.voyage',
            'bookings.id_customer AS id_owner',
            'customers.name AS owner_name',
            'safe_conducts.id AS id_safe_conduct',
            'safe_conducts.no_safe_conduct',
            'safe_conducts.driver',
            'safe_conducts.no_police',
            'safe_conducts.expedition',
            'safe_conducts.security_in_date',
            'safe_conducts.security_out_date',
            'source_warehouses.name AS source_warehouse',
            'source_warehouses.region AS source_warehouse_region',
            'handlings.id AS id_handling',
            'handlings.no_handling',
            'work_orders.id AS id_work_order',
            'work_orders.no_work_order',
            'work_orders.gate_in_date',
            'work_orders.gate_out_date',
            'work_orders.taken_at',
            'work_orders.completed_at',
            'work_order_goods.id_goods',
            'ref_goods.no_goods',
            'ref_goods.name AS goods_name',
            'work_order_goods.quantity',
            'ref_goods.unit_weight',
            '(ref_goods.unit_weight * work_order_goods.quantity) AS total_weight',
            'ref_goods.unit_gross_weight',
            '(ref_goods.unit_gross_weight * work_order_goods.quantity) AS total_gross_weight',
            'ref_goods.unit_volume',
            '(ref_goods.unit_volume * work_order_goods.quantity) AS total_volume',
            'ref_units.unit',
            'ref_positions.position',
            'work_order_goods.ex_no_container',
            'work_order_goods.no_pallet',
            'ref_goods.whey_number',
            'work_order_goods.is_hold',
            'work_order_goods.status',
            'work_order_goods.status_danger',
            'work_order_goods.description AS goods_description',
            'booking_in.id AS id_booking_in',
            'booking_in.no_booking AS no_booking_in',
            'booking_in.no_reference AS no_reference_in',
            'booking_in.reference_date AS reference_date_in',
            'booking_type_in.booking_type AS booking_type_in',
        ];

        if (key_exists('total_size', $filters) && !empty($filters['total_size'])) {
            if ($filters['total_size'] != 'all') {
                $countColumn = $filters['total_size'];
                if ($filters['total_size'] == 'unit_weight' || $filters['total_size'] == 'unit_volume') {
                    $countColumn = 'ref_goods.' . $filters['total_size'] . ' * quantity';
                }
                $defaultSelect = [
                    'SUM(' . $countColumn . ') AS ' . $filters['total_size'] . '_total'
                ];
            }
        }

        $report = $this->db->select($defaultSelect)
            ->from('bookings')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('booking_references', 'booking_references.id_booking = bookings.id', 'left')
            ->join('bookings AS booking_in', 'booking_in.id = IFNULL(booking_references.id_booking_reference, bookings.id)', 'left')
            //->join('bookings AS booking_in', 'booking_in.id = IFNULL(bookings.id_booking, bookings.id)', 'left')
            ->join('handlings', 'handlings.id_booking = bookings.id', 'left')
            ->join('work_orders', 'work_orders.id_handling = handlings.id', 'left')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id AND IFNULL(work_order_goods.id_booking_reference, bookings.id) = booking_in.id', 'left')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods', 'left')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit', 'left')
            ->join('ref_positions', 'ref_positions.id = work_order_goods.id_position', 'left')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('ref_people AS source_warehouses', 'source_warehouses.id = safe_conducts.id_source_warehouse', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_booking_types AS booking_type_in', 'booking_type_in.id = booking_in.id_booking_type', 'left')
            ->join("(
                SELECT 
                    upload_documents.id_upload,
                    MAX(upload_documents.no_document) as no_invoice
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'Invoice'
                GROUP BY id_upload              
            ) AS invoice_documents", 'invoice_documents.id_upload = bookings.id_upload', 'left');

        if ($category == 'INBOUND') {
            $report->where('id_handling_type', $handlingTypeIdInbound);
        } else {
            $report->where('id_handling_type', $handlingTypeIdOutbound);
        }

        if ($start < 0) {
            $report->order_by('completed_at', 'desc');
        }

        if (empty($filters) || !key_exists('data', $filters)) {
            $report->where('id_goods IS NOT NULL');
        } else {
            if (key_exists('data', $filters) && !empty($filters['data'])) {
                if ($filters['data'] == 'realization') {
                    $report->where('id_goods IS NOT NULL');
                }
            } else {
                $report->where('id_goods IS NOT NULL');
            }

            if (key_exists('branch_type', $filters) && !is_null($filters['branch_type'])) {
                $report->where('branch_type IS NOT NULL');
                $report->where('branch_type', $filters['branch_type']);
            }

            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $report->where_in('id_owner', $filters['owner']);
                } else {
                    $report->where('id_owner', $filters['owner']);
                }
            }

            if (key_exists('goods', $filters) && !empty($filters['goods'])) {
                if (is_array($filters['goods'])) {
                    $report->where_in('id_goods', $filters['goods']);
                } else {
                    $report->where('id_goods', $filters['goods']);
                }
            }

            if (key_exists('sort_by', $filters) && !empty($filters['sort_by'])) {
                $order = key_exists('order', $filters) ? $filters['order'] : 'asc';
                $report->order_by($filters['sort_by'], $order);
            }

            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
                if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                    $report->where('DATE(' . $filters['date_type'] . ')>=', sql_date_format($filters['date_from'], false));
                }
                if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                    $report->where('DATE(' . $filters['date_type'] . ')<=', sql_date_format($filters['date_to'], false));
                }
            }
        }

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('id_owner', $customerId);
        }

        if (key_exists('search', $filters) && !empty($filters['search'])) {
            $search = (is_array($filters['search']) ? $search : $filters['search']);
        }

        if (key_exists('total_size', $filters) && !empty($filters['total_size'])) {
            $this->db->stop_cache();
            $this->db->flush_cache();
            if ($filters['total_size'] == 'all') {
                return $report->count_all_results();
            } else {
                return $report->get()->row_array()[$filters['total_size'] . '_total'];
            }
        }

        if (!empty($search)) {
            $report
                ->group_start()
                ->like('bookings.no_booking', $search)
                ->or_like('booking_in.no_booking', $search)
                ->or_like('bookings.no_reference', $search)
                ->or_like('booking_in.no_reference', $search)
                ->or_like('bookings.reference_date', $search)
                ->or_like('booking_in.reference_date', $search)
                ->or_like('bookings.booking_date', $search)
                ->or_like('booking_in.booking_date', $search)
                ->or_like('bookings.vessel', $search)
                ->or_like('bookings.voyage', $search)
                ->or_like('customers.name', $search)
                ->or_like('no_safe_conduct', $search)
                ->or_like('driver', $search)
                ->or_like('no_police', $search)
                ->or_like('safe_conducts.expedition', $search)
                ->or_like('security_in_date', $search)
                ->or_like('security_out_date', $search)
                ->or_like('no_handling', $search)
                ->or_like('no_work_order', $search)
                ->or_like('gate_in_date', $search)
                ->or_like('gate_out_date', $search)
                ->or_like('taken_at', $search)
                ->or_like('completed_at', $search)
                ->or_like('no_goods', $search)
                ->or_like('ref_goods.name', $search)
                ->or_like('quantity', $search)
                ->or_like('ref_goods.unit_weight', $search)
                ->or_like('ref_goods.unit_gross_weight', $search)
                ->or_like('ref_goods.unit_volume', $search)
                ->or_like('unit', $search)
                ->or_like('position', $search)
                ->or_like('no_pallet', $search)
                ->or_like('is_hold', $search)
                ->or_like('work_order_goods.status', $search)
                ->or_like('status_danger', $search)
                ->or_like('work_order_goods.description', $search)
                ->group_end();
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $report->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $reportTotal = $this->db->count_all_results();
        if ($column == 'no') $column = 'bookings.id';
        $page = $report->order_by($column, $sort)->limit($length, $start);
        $reportData = $page->get()->result_array();

        foreach ($reportData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => get_url_param('draw', $this->input->post('draw')),
            "recordsTotal" => count($reportData),
            "recordsFiltered" => $reportTotal,
            "data" => $reportData
        ];

        $this->db->flush_cache();

        return $pageData;
    }

    /**
     * Get report upload progress.
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getReportUploadProgressInbound($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));

        $this->db->start_cache();

        $report = $this->db
            ->select([
                'uploads.id as id_upload',
                'ref_branches.id AS id_branch',
                'ref_branches.branch AS branch_name',
                'ref_people.id AS id_customer',
                'ref_people.name AS customer',
                'ref_booking_types.booking_type',
                'uploads.description AS no_reference',
                'no_registrations.value AS no_registration',
                'bill_of_loading_documents.no_bill_of_loading',
                'invoice_documents.no_invoice',
                'bookings.party AS party',
                'bookings.goods_name',
                'bookings.total_netto AS total_net_weight',
                'bookings.total_bruto AS total_gross_weight',
                get_ext_field('cif', 'bookings.id', 'cif'),
                'eta_documents.eta_date',
                'ata_documents.ata_date',
                'uploads.created_at AS upload_date',
                'draft_documents.draft_date',
                'confirmation_documents.confirmation_date',
                'do_documents.do_date',
                'do_documents.expired_do_date',
                'do_documents.freetime_do_date',
                'sppb_documents.sppb_date',
                'sppd_documents.sppd_date',
                'hardcopy_documents.hardcopy_date',
                'draft_documents.type AS type_parties',
                'draft_documents.party AS parties',
                'uploads.status',
            ])
            ->from('uploads')
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_document_types AS main_docs', 'main_docs.id = ref_booking_types.id_document_type', 'left')
            ->join('ref_people', 'ref_people.id = uploads.id_person', 'left')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
            ->join("(
                SELECT upload_documents.id_upload, upload_documents.no_document as no_bill_of_loading
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'Bill Of Loading' AND upload_documents.is_deleted = false
            ) AS bill_of_loading_documents", 'bill_of_loading_documents.id_upload = uploads.id', 'left')
            ->join("(
                SELECT upload_documents.id_upload, upload_documents.no_document as no_invoice
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'Invoice' AND upload_documents.is_deleted = false
            ) AS invoice_documents", 'invoice_documents.id_upload = uploads.id', 'left')
            ->join("(
                SELECT upload_documents.id_upload, upload_documents.created_at as draft_date,
                    GROUP_CONCAT(CONCAT(ROUND(upload_document_parties.party),'x', upload_document_parties.shape) separator ', ') AS party,
                    upload_document_parties.type
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                LEFT JOIN upload_document_parties ON upload_document_parties.id_upload_document = upload_documents.id
                WHERE ref_document_types.document_type LIKE '% Draft' AND upload_documents.is_deleted = false
                GROUP BY upload_documents.id
            ) AS draft_documents", 'draft_documents.id_upload = uploads.id', 'left')
            ->join("(
                SELECT upload_documents.id_upload, upload_documents.created_at as confirmation_date
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type LIKE '% Confirmation' AND upload_documents.is_deleted = false
            ) AS confirmation_documents", 'confirmation_documents.id_upload = uploads.id', 'left')
            ->join("(
                SELECT upload_documents.id_upload, upload_documents.document_date as eta_date
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'ETA' AND upload_documents.is_deleted = false
            ) AS eta_documents", 'eta_documents.id_upload = uploads.id', 'left')
            ->join("(
                SELECT upload_documents.id_upload, upload_documents.document_date as ata_date
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'ATA' AND upload_documents.is_deleted = false
            ) AS ata_documents", 'ata_documents.id_upload = uploads.id', 'left')
            ->join("(
                SELECT 
                    upload_documents.id_upload,
                    upload_documents.created_at as do_date,
                    upload_documents.document_date as do_document_date,
                    upload_documents.expired_date as expired_do_date,
                    upload_documents.freetime_date as freetime_do_date
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'DO' AND upload_documents.is_deleted = false
            ) AS do_documents", 'do_documents.id_upload = uploads.id', 'left')
            ->join("(
                SELECT upload_documents.id_upload, upload_documents.document_date as sppb_date             
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'SPPB' AND upload_documents.is_deleted = false
            ) AS sppb_documents", 'sppb_documents.id_upload = uploads.id', 'left')
            ->join("(
                SELECT upload_documents.id_upload, upload_documents.document_date as sppd_date             
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'SPPD' AND upload_documents.is_deleted = false
            ) AS sppd_documents", 'sppd_documents.id_upload = uploads.id', 'left')
            ->join("(
                SELECT upload_documents.id_upload, upload_documents.created_at as hardcopy_date             
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'Hardcopy' AND upload_documents.is_deleted = false
            ) AS hardcopy_documents", 'hardcopy_documents.id_upload = uploads.id', 'left')
            ->join("(
                SELECT 
                    bookings.id,
                    bookings.id_upload,
                    bookings.total_netto,
                    bookings.total_bruto,
                    IF(COUNT(booking_containers.id_booking) = 0, 'LCL', GROUP_CONCAT(booking_containers.party)) AS party,
                    SUM(booking_containers.total) AS total_container,
                    (SELECT COUNT(id) FROM booking_goods WHERE id_booking_container IS NULL AND id_booking = bookings.id) AS total_goods,
                    booking_goods.goods_name
                FROM bookings
                LEFT JOIN (
                    SELECT 
                        id_booking, 
                        ref_containers.size, 
                        COUNT(booking_containers.id) AS total, 
                        CONCAT(COUNT(booking_containers.id), 'x', ref_containers.size) AS party
                    FROM booking_containers
                    INNER JOIN ref_containers ON ref_containers.id = booking_containers.id_container
                    GROUP BY id_booking, size
                ) AS booking_containers ON booking_containers.id_booking = bookings.id
                LEFT JOIN (
                    SELECT id_booking, GROUP_CONCAT(ref_goods.name) AS goods_name 
                    FROM booking_goods
                    INNER JOIN ref_goods ON ref_goods.id = booking_goods.id_goods
                    GROUP BY id_booking
                ) AS booking_goods ON booking_goods.id_booking = bookings.id
                WHERE bookings.is_deleted = FALSE
                GROUP BY bookings.id, bookings.id_upload
            ) AS bookings", 'bookings.id_upload = uploads.id', 'left')
            ->join('(
                SELECT DISTINCT booking_extensions.id_booking, booking_extensions.value 
                FROM booking_extensions 
                LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
                WHERE ref_extension_fields.field_name = "nopen"
            ) AS no_registrations', 'no_registrations.id_booking = bookings.id', 'left')
            ->where('ref_booking_types.category', "INBOUND")
            ->where('uploads.is_deleted', false);

        if (!empty($branchId) && $branchId != 'all') {
            if (is_array($branchId)) {
                $report->where_in('uploads.id_branch', $branchId);
            } else {
                $report->where('uploads.id_branch', $branchId);
            }
        }

        if (key_exists('data', $filters) && !empty($filters['data'])) {
            if ($filters['data'] == 'not_sppd') {
                $report->where('sppd_date IS NULL');
            }
            if ($filters['data'] == 'sppd') {
                $report->where('sppd_date IS NOT NULL');
            }
        }

        if (key_exists('owner', $filters) && !empty($filters['owner'])) {
            if (is_array($filters['owner'])) {
                $report->where_in('ref_people.id', $filters['owner']);
            } else {
                $report->where('ref_people.id', $filters['owner']);
            }
        }

        if (key_exists('dashboard', $filters) && !empty($filters['dashboard'])) {
            if (is_array($filters['dashboard'])) {
                $report->where_in('ref_branches.dashboard_status', $filters['dashboard']);
            } else {
                $report->where('ref_branches.dashboard_status', $filters['dashboard']);
            }
        }

        if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
            if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                $report->where('DATE(' . $filters['date_type'] . ')>=', format_date($filters['date_from']));
            }
            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $report->where('DATE(' . $filters['date_type'] . ')<=', format_date($filters['date_to']));
            }
        }

        if (!empty($search)) {
            $report
                ->group_start()
                ->like('ref_people.name', $search)
                ->or_like('ref_branches.branch', $search)
                ->or_like('uploads.description', $search)
                ->or_like('uploads.no_upload', $search)
                ->or_like('uploads.status', $search)
                ->or_like('ref_booking_types.booking_type', $search)
                ->or_like('bill_of_loading_documents.no_bill_of_loading', $search)
                ->or_like('invoice_documents.no_invoice', $search)
                ->group_end();
        }

        $this->db->stop_cache();

        if ($column == 'no') $column = 'uploads.created_at';

        if ($start < 0) {
            $allData = $report->order_by($column, $sort)->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $total = $this->db->count_all_results();
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
     * Get report upload progress outbound.
     * @param array $filters
     * @return array|array[]
     */
    public function getReportUploadProgressOutbound($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));

        $this->db->start_cache();

        $report = $this->db
            ->select([
                'uploads.id as id_upload',
                'ref_branches.id AS id_branch',
                'ref_branches.branch AS branch_name',
                'ref_people.id AS id_customer',
                'ref_people.name AS customer',
                'ref_booking_types.booking_type',
                'invoice_documents.no_invoice',
                'no_registrations.value AS no_registration',
                'uploads.description AS no_reference',
                'uploads_in_data.description AS no_reference_inbound',
                'booking_goods.goods_name',
                'bookings.total_netto AS total_net_weight',
                'bookings.total_bruto AS total_gross_weight',
                get_ext_field('cif', 'bookings.id', 'cif'),
                'uploads.created_at AS upload_date',
                'IFNULL(draft_documents.updated_at_draft, draft_documents.draft_date) AS draft_date',
                'IFNULL(confirmation_documents.updated_at_confirmation, confirmation_documents.confirmation_date) AS confirmation_date',
                'IFNULL(billing_documents.billing_date, billing_documents.updated_at_billing) AS billing_date',
                'IFNULL(bpn_documents.bpn_date, bpn_documents.updated_at_bpn) AS bpn_date',
                'sppb_documents.sppb_date',
                'sppf_documents.sppf_date',
                'sppd_documents.sppd_date',
                'sppd_in_documents.sppd_in_date',
                'draft_documents.party AS parties',
                'draft_documents.type AS type_parties',
                'uploads.status',
                'uploads.is_hold',
                /*
                'DATE_FORMAT(uploads.created_at, "%d/%m/%Y %H:%i:%s") AS upload_date',
                'DATE_FORMAT(IFNULL(draft_documents.updated_at_draft, draft_documents.draft_date), "%d/%m/%Y %H:%i:%s") AS draft_date',
                'DATE_FORMAT(IFNULL(confirmation_documents.updated_at_confirmation, confirmation_documents.confirmation_date), "%d/%m/%Y %H:%i:%s") AS confirmation_date',
                'DATE_FORMAT(IFNULL(billing_documents.billing_date, billing_documents.updated_at_billing), "%d/%m/%Y %H:%i:%s") AS billing_date',
                'DATE_FORMAT(IFNULL(bpn_documents.bpn_date, bpn_documents.updated_at_bpn), "%d/%m/%Y %H:%i:%s") AS bpn_date',
                'DATE_FORMAT(IFNULL(sppb_documents.sppb_date, sppb_documents.updated_at_sppb), "%d/%m/%Y %H:%i:%s") AS sppb_date',
                'DATE_FORMAT(IFNULL(sppf_documents.sppf_date, sppf_documents.updated_at_sppf), "%d/%m/%Y %H:%i:%s") AS sppf_date',
                'DATE_FORMAT(IFNULL(sppd_documents.updated_at_sppd, sppd_documents.sppd_date), "%d/%m/%Y %H:%i:%s") AS sppd_date',
                'DATE_FORMAT(IFNULL(sppd_in_documents.sppd_in_date, sppd_in_documents.updated_at_sppd_in), "%d/%m/%Y %H:%i:%s") AS sppd_in_date',
                */
            ])
            ->from('uploads')
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_document_types AS main_docs', 'main_docs.id = ref_booking_types.id_document_type', 'left')
            ->join('ref_people', 'ref_people.id = uploads.id_person', 'left')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
            ->join('bookings', 'bookings.id_upload = uploads.id', 'left')
            ->join('(
                SELECT id_booking, GROUP_CONCAT(ref_goods.name) AS goods_name 
                FROM booking_goods
                INNER JOIN ref_goods ON ref_goods.id = booking_goods.id_goods
                GROUP BY id_booking
            ) AS booking_goods', 'booking_goods.id_booking = bookings.id', 'left')
            ->join("(
                SELECT 
                    upload_documents.id_upload,
                    upload_documents.no_document as no_invoice
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'Invoice' AND upload_documents.is_deleted = false
            ) AS invoice_documents", 'invoice_documents.id_upload = uploads.id', 'left')
            ->join("uploads AS uploads_in_data", 'uploads_in_data.id = uploads.id_upload', 'left')
            ->join("(
                SELECT 
                    upload_documents.id_upload,
                    upload_documents.created_at as draft_date,
                    upload_documents.updated_at as updated_at_draft,
                    GROUP_CONCAT(CONCAT(ROUND(upload_document_parties.party),'x', upload_document_parties.shape) separator ', ') AS party,
                    upload_document_parties.type
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                LEFT JOIN upload_document_parties ON upload_document_parties.id_upload_document = upload_documents.id
                WHERE ref_document_types.document_type LIKE '% Draft' AND upload_documents.is_deleted = false
                GROUP BY upload_documents.id
            ) AS draft_documents", 'draft_documents.id_upload = uploads.id', 'left')
            ->join("(
                SELECT 
                    upload_documents.id_upload,
                    upload_documents.created_at as confirmation_date,
                    upload_documents.updated_at as updated_at_confirmation
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type LIKE '% Confirmation' AND upload_documents.is_deleted = false
            ) AS confirmation_documents", 'confirmation_documents.id_upload = uploads.id', 'left')
            ->join("(
                SELECT 
                    upload_documents.id_upload,
                    upload_documents.created_at as billing_date,
                    upload_documents.updated_at as updated_at_billing             
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'E Billing' AND upload_documents.is_deleted = false
            ) AS billing_documents", 'billing_documents.id_upload = uploads.id', 'left')
            ->join("(
                SELECT 
                    upload_documents.id_upload,
                    upload_documents.created_at as bpn_date,
                    upload_documents.updated_at as updated_at_bpn
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'BPN (Bukti Penerimaan Negara)' AND upload_documents.is_deleted = false
            ) AS bpn_documents", 'bpn_documents.id_upload = uploads.id', 'left')
            ->join("(
                SELECT 
                    upload_documents.id_upload, 
                    upload_documents.document_date as sppf_date      
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'SPPF (PEMERIKSAAN BARANG)' AND upload_documents.is_deleted = false
            ) AS sppf_documents", 'sppf_documents.id_upload = uploads.id', 'left')
            ->join("(
                SELECT 
                    upload_documents.id_upload,
                    upload_documents.document_date as sppb_date         
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'SPPB' AND upload_documents.is_deleted = false
            ) AS sppb_documents", 'sppb_documents.id_upload = uploads.id', 'left')
            ->join("(
                SELECT 
                    upload_documents.id_upload,
                    upload_documents.document_date as sppd_in_date
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'SPPD' AND upload_documents.is_deleted = false
            ) AS sppd_in_documents", 'sppd_in_documents.id_upload = uploads.id_upload', 'left')
            ->join("(
                SELECT 
                    upload_documents.id_upload,
                    upload_documents.document_date as sppd_date
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'SPPD' AND upload_documents.is_deleted = false
            ) AS sppd_documents", 'sppd_documents.id_upload = uploads.id', 'left')
            ->join('(
                SELECT DISTINCT booking_extensions.id_booking, booking_extensions.value 
                FROM booking_extensions 
                LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
                WHERE ref_extension_fields.field_name = "nopen"
            ) AS no_registrations', 'no_registrations.id_booking = bookings.id', 'left')
            ->where('ref_booking_types.category', "OUTBOUND")
            ->where('uploads.is_deleted', false);

        if (!empty($branchId) && $branchId != 'all') {
            if (is_array($branchId)) {
                $report->where_in('uploads.id_branch', $branchId);
            } else {
                $report->where('uploads.id_branch', $branchId);
            }
        }

        if (key_exists('data', $filters) && !empty($filters['data'])) {
            if ($filters['data'] == 'not_sppd') {
                $report->where('sppd_documents.sppd_date IS NULL');
            }
            if ($filters['data'] == 'sppd') {
                $report->where('sppd_documents.sppd_date IS NOT NULL');
            }
        }

        if (key_exists('owner', $filters) && !empty($filters['owner'])) {
            if (is_array($filters['owner'])) {
                $report->where_in('ref_people.id', $filters['owner']);
            } else {
                $report->where('ref_people.id', $filters['owner']);
            }
        }

        if (key_exists('dashboard', $filters) && !empty($filters['dashboard'])) {
            if (is_array($filters['dashboard'])) {
                $report->where_in('ref_branches.dashboard_status', $filters['dashboard']);
            } else {
                $report->where('ref_branches.dashboard_status', $filters['dashboard']);
            }
        }

        if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
            if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                $report->where('DATE(' . $filters['date_type'] . ')>=', format_date($filters['date_from']));
            }
            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $report->where('DATE(' . $filters['date_type'] . ')<=', format_date($filters['date_to']));
            }
        }

        $report
            ->group_start()
            ->like('ref_people.name', $search)
            ->or_like('invoice_documents.no_invoice', $search)
            ->or_like('uploads_in_data.description', $search)
            ->or_like('uploads.description', $search)
            ->or_like('uploads.no_upload', $search)
            ->or_like('uploads.status', $search)
            ->or_like('uploads.created_at', $search)
            ->or_like('draft_documents.draft_date', $search)
            ->or_like('confirmation_documents.confirmation_date', $search)
            ->or_like('billing_documents.billing_date', $search)
            ->or_like('sppb_documents.sppb_date', $search)
            ->or_like('sppd_in_documents.sppd_in_date', $search)
            ->group_end();

        $this->db->stop_cache();

        if ($column == 'no') $column = 'uploads.created_at';

        if ($start < 0) {
            $allData = $report->order_by($column, $sort)->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $total = $this->db->count_all_results();
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
     * Get report activity external.
     *
     * @param string $category
     * @param array $filters
     * @return array|int
     */
    public function getReportActivityGoodsExternal($category = 'INBOUND', $filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $handlingTypeIdInbound = get_setting('default_inbound_handling');
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $this->db->start_cache();

        $defaultSelect = [
            'doc_invoice.no_document AS invoice_number',
            'bookings.no_reference',
            'work_order_goods.ex_no_container',
            'IFNULL(whey_number, no_goods) AS no_label',
            'ref_goods.name AS description',
            'work_order_goods.quantity',
            'ref_units.unit',
            'ref_goods.unit_weight',
            '(ref_goods.unit_weight * work_order_goods.quantity) AS total_weight',
            'ref_goods.unit_volume',
            '(ref_goods.unit_volume * work_order_goods.quantity) AS total_volume',
            'doc_bl.no_document AS bl_number',
            'safe_conducts.security_out_date AS security_out',
            'work_orders.gate_out_date AS gate_out',
            'safe_conducts.no_police AS no_trucking',
            'safe_conducts.driver AS driver_name'
        ];

        if (key_exists('total_size', $filters) && !empty($filters['total_size'])) {
            if ($filters['total_size'] != 'all') {
                $countColumn = $filters['total_size'];
                if ($filters['total_size'] == 'unit_weight' || $filters['total_size'] == 'unit_volume') {
                    $countColumn = 'ref_goods.' . $filters['total_size'] . ' * quantity';
                }
                $defaultSelect = [
                    'SUM(' . $countColumn . ') AS ' . $filters['total_size'] . '_total'
                ];
            }
        }

        $report = $this->db->select($defaultSelect)
            ->from('bookings')
            ->join('bookings AS booking_in', 'booking_in.id = IFNULL(bookings.id_booking, bookings.id)', 'left')
            ->join('safe_conducts', 'safe_conducts.id_booking = bookings.id', 'left')
            ->join('work_orders', 'work_orders.id_safe_conduct = safe_conducts.id', 'left')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id', 'left')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods', 'left')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling', 'left')
            ->join('(
                SELECT id_upload, no_document FROM upload_documents
                INNER JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                WHERE ref_document_types.document_type = "Invoice"
            ) AS doc_invoice', 'doc_invoice.id_upload = booking_in.id_upload', 'left')
            ->join('(
                SELECT id_upload, upload_documents.no_document FROM upload_documents
                INNER JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                WHERE ref_document_types.document_type = "Bill Of Loading"
            ) AS doc_bl', 'doc_bl.id_upload = booking_in.id_upload', 'left');

        if ($category == 'INBOUND') {
            $report->where('id_handling_type', $handlingTypeIdInbound);
        } else {
            $report->where('id_handling_type', $handlingTypeIdOutbound);
        }

        if ($start < 0) {
            $report->order_by('invoice_number', 'desc');
        }

        if (empty($filters) || !key_exists('data', $filters)) {
            $report->where('id_goods IS NOT NULL');
        } else {
            if (key_exists('data', $filters) && !empty($filters['data'])) {
                if ($filters['data'] == 'realization') {
                    $report->where('id_goods IS NOT NULL');
                }
            }

            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $report->where_in('id_owner', $filters['owner']);
                } else {
                    $report->where('id_owner', $filters['owner']);
                }
            }

            if (key_exists('goods', $filters) && !empty($filters['goods'])) {
                if (is_array($filters['goods'])) {
                    $report->where_in('id_goods', $filters['goods']);
                } else {
                    $report->where('id_goods', $filters['goods']);
                }
            }

            if (key_exists('sort_by', $filters) && !empty($filters['sort_by'])) {
                $order = key_exists('order', $filters) ? $filters['order'] : 'asc';
                $report->order_by($filters['sort_by'], $order);
            }

            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
                if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                    $report->where('DATE(' . $filters['date_type'] . ')>=', sql_date_format($filters['date_from'], false));
                }
                if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                    $report->where('DATE(' . $filters['date_type'] . ')<=', sql_date_format($filters['date_to'], false));
                }
            }
        }

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('id_owner', $customerId);
        }

        if (key_exists('search', $filters) && !empty($filters['search'])) {
            $search = (is_array($filters['search']) ? $search : $filters['search']);
        }

        if (key_exists('total_size', $filters) && !empty($filters['total_size'])) {
            $this->db->stop_cache();
            $this->db->flush_cache();
            if ($filters['total_size'] == 'all') {
                return $report->count_all_results();
            } else {
                return $report->get()->row_array()[$filters['total_size'] . '_total'];
            }
        }

        if (!empty($search)) {
            $report
                ->group_start()
                ->like('bookings.no_reference', $search)
                ->or_like('work_order_goods.ex_no_container', $search)
                ->or_like('bookings.no_reference', $search)
                ->or_like('ref_goods.whey_number', $search)
                ->or_like('ref_goods.no_goods', $search)
                ->or_like('ref_goods.name', $search)
                ->or_like('work_order_goods.quantity', $search)
                ->or_like('ref_units.unit', $search)
                ->or_like('ref_goods.unit_weight', $search)
                ->or_like('ref_goods.unit_volume', $search)
                ->or_like('work_orders.gate_out_date', $search)
                ->or_like('safe_conducts.no_police', $search)
                ->or_like('safe_conducts.driver', $search)
                ->or_like('doc_bl.no_document', $search)
                ->or_like('doc_invoice.no_document', $search)
                ->group_end();
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $report->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $reportTotal = $this->db->count_all_results();
        if ($column == 'no') $column = 'bookings.id';
        $page = $report->order_by($column, $sort)->limit($length, $start);
        $reportData = $page->get()->result_array();

        foreach ($reportData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($reportData),
            "recordsFiltered" => $reportTotal,
            "data" => $reportData
        ];

        $this->db->flush_cache();

        return $pageData;
    }

    /**
     * Get realization booking container report.
     *
     * @param array $filters
     * @return mixed
     */
    public function getReportRealizationContainer($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $handlingTypeIdInbound = get_setting('default_inbound_handling');
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $this->db->start_cache();

        $report = $this->db->select([
            'bookings.no_booking',
            'bookings.no_reference',
            'bookings.booking_date',
            'ref_people.name AS customer_name',
            'ref_booking_types.category',
            'ref_containers.no_container',
            'ref_containers.type',
            'ref_containers.size',
            'work_order_containers.position',
            'DATEDIFF(NOW(), bookings.booking_date) AS total_pending_day',
            'IF(work_order_containers.id_container IS NULL, "IN PROCESS", "REALIZED") AS status_realization',
        ])
            ->from('bookings')
            ->join('ref_people', 'bookings.id_customer = ref_people.id', 'left')
            ->join('ref_booking_types', 'bookings.id_booking_type = ref_booking_types.id', 'left')
            ->join('booking_containers', 'bookings.id = booking_containers.id_booking', 'left')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->join('ref_containers', 'booking_containers.id_container = ref_containers.id', 'left')
            ->join("(
                SELECT id_booking, id_handling_type, id_container, position
                FROM handlings
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                INNER JOIN ref_positions ON ref_positions.id = work_order_containers.id_position
                WHERE work_orders.is_deleted = false AND handlings.is_deleted = false
            ) AS work_order_containers", 'bookings.id = work_order_containers.id_booking 
                AND booking_containers.id_container = work_order_containers.id_container', 'left')
            ->group_start()
            ->where('id_handling_type', $handlingTypeIdInbound)
            ->or_where('id_handling_type', $handlingTypeIdOutbound)
            ->or_where('id_handling_type IS NULL')
            ->group_end()
            ->where('booking_containers.id_container IS NOT NULL');

        if (!empty($filters)) {
            if (key_exists('data', $filters) && !empty($filters['data'])) {
                if ($filters['data'] == 'pending') {
                    $report->where('work_order_containers.id_container IS NULL');
                }
            }

            if (key_exists('branch_type', $filters) && !is_null($filters['branch_type'])) {
                $report->where('branch_type IS NOT NULL');
                $report->where('branch_type', $filters['branch_type']);
            }

            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $report->where_in('id_owner', $filters['owner']);
                } else {
                    $report->where('id_owner', $filters['owner']);
                }
            }

            if (key_exists('booking', $filters) && !empty($filters['booking'])) {
                $report->where('id_booking', $filters['booking']);
            }

            if (key_exists('date_fetch', $filters) && !empty($filters['date_fetch'])) {
                $report->where('bookings.booking_date <=', sql_date_format($filters['date_fetch']));
            }
        }

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('id_owner', $customerId);
        }

        $report
            ->group_start()
            ->like('bookings.no_booking', $search)
            ->or_like('bookings.no_reference', $search)
            ->or_like('bookings.booking_date', $search)
            ->or_like('ref_people.name', $search)
            ->or_like('ref_booking_types.category', $search)
            ->or_like('ref_containers.no_container', $search)
            ->or_like('ref_containers.type', $search)
            ->or_like('ref_containers.size', $search)
            ->or_like('work_order_containers.position', $search)
            ->or_like('DATEDIFF(NOW(), bookings.booking_date)', $search)
            ->or_like('IF(work_order_containers.id_container IS NULL, "IN PROCESS", "REALIZED")', $search)
            ->group_end();

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $report->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $total = $this->db->count_all_results();
        if ($column == 'no') $column = 'bookings.id';
        $page = $report->order_by($column, $sort)->limit($length, $start);
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
     * Get realization booking goods report.
     *
     * @param array $filters
     * @return mixed
     */
    public function getReportRealizationGoods($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $handlingTypeIdInbound = get_setting('default_inbound_handling');
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $this->db->start_cache();

        $report = $this->db->select([
            'bookings.no_booking',
            'bookings.no_reference',
            'bookings.booking_date',
            'ref_people.name AS customer_name',
            'ref_booking_types.category',
            'ref_goods.no_goods',
            'ref_goods.name AS goods_name',
            'ref_units.unit',
            'booking_goods.quantity',
            'SUM(work_order_goods.quantity) AS quantity_realization',
            'work_order_goods.position',
            'work_order_goods.no_pallet',
            'DATEDIFF(NOW(), bookings.booking_date) AS total_pending_day',
            'IF(work_order_goods.id_goods IS NULL, "IN PROCESS", "REALIZED") AS status_realization',
        ])
            ->from('bookings')
            ->join('ref_people', 'bookings.id_customer = ref_people.id', 'left')
            ->join('ref_booking_types', 'bookings.id_booking_type = ref_booking_types.id', 'left')
            ->join('booking_goods', 'bookings.id = booking_goods.id_booking', 'left')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->join('ref_goods', 'booking_goods.id_goods = ref_goods.id', 'left')
            ->join('ref_units', 'booking_goods.id_unit = ref_units.id', 'left')
            ->join('(
                SELECT id_booking, id_handling_type, id_goods, id_unit, position, quantity, no_pallet
                FROM handlings
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                INNER JOIN ref_positions ON ref_positions.id = work_order_goods.id_position
                WHERE work_orders.is_deleted = false AND handlings.is_deleted = false
            ) AS work_order_goods', 'bookings.id = work_order_goods.id_booking 
                AND booking_goods.id_goods = work_order_goods.id_goods
                AND booking_goods.id_unit = work_order_goods.id_unit', 'left')
            ->group_start()
            ->where('id_handling_type', $handlingTypeIdInbound)
            ->or_where('id_handling_type', $handlingTypeIdOutbound)
            ->or_where('id_handling_type IS NULL')
            ->group_end()
            ->where('booking_goods.id_goods IS NOT NULL')
            ->group_by('booking_goods.id, position, no_pallet');

        if (!empty($filters)) {
            if (key_exists('data', $filters) && !empty($filters['data'])) {
                if ($filters['data'] == 'pending') {
                    $report->where('work_order_goods.id_goods IS NULL');
                }
            }

            if (key_exists('branch_type', $filters) && !is_null($filters['branch_type'])) {
                $report->where('branch_type IS NOT NULL');
                $report->where('branch_type', $filters['branch_type']);
            }

            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $report->where_in('id_owner', $filters['owner']);
                } else {
                    $report->where('id_owner', $filters['owner']);
                }
            }

            if (key_exists('booking', $filters) && !empty($filters['booking'])) {
                $report->where('id_booking', $filters['booking']);
            }

            if (key_exists('date_fetch', $filters) && !empty($filters['date_fetch'])) {
                $report->where('bookings.booking_date <=', sql_date_format($filters['date_fetch']));
            }
        }

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('id_owner', $customerId);
        }

        $report
            ->group_start()
            ->like('bookings.no_booking', $search)
            ->or_like('bookings.no_reference', $search)
            ->or_like('bookings.booking_date', $search)
            ->or_like('ref_people.name', $search)
            ->or_like('ref_booking_types.category', $search)
            ->or_like('ref_goods.no_goods', $search)
            ->or_like('ref_goods.name', $search)
            ->or_like('ref_units.unit', $search)
            ->or_like('booking_goods.quantity', $search)
            ->or_like('work_order_goods.position', $search)
            ->or_like('work_order_goods.no_pallet', $search)
            ->or_like('DATEDIFF(NOW(), bookings.booking_date)', $search)
            ->or_like('IF(work_order_goods.id_goods IS NULL, "IN PROCESS", "REALIZED")', $search)
            ->group_end();

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $report->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $total = $this->db->count_all_results();
        if ($column == 'no') $column = 'ref_people.name';
        $page = $report->order_by($column, $sort)->limit($length, $start);
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
     * Get report booking rating.
     *
     * @param $filters
     * @return array|int
     */
    public function getReportBookingRating($filters)
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;

        $columnOrder = [
            0 => "bookings.id",
            1 => "customer_name",
            2 => "no_reference",
            3 => "sppb_date",
            4 => "completed_date",
            5 => "category",
            6 => "rated_at",
            7 => "rating",
            8 => "rating_description",
        ];
        $columnSort = $columnOrder[$column];

        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));
        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $this->db->start_cache();

        $baseQuery = $this->db->select([
            'bookings.id',
            'ref_people.name AS customer_name',
            'bookings.no_booking',
            'bookings.no_reference',
            'doc_sppb.sppb_date',
            'complete_statuses.completed_date',
            'ref_booking_types.booking_type',
            'ref_booking_types.category',
            'bookings.rated_at',
            'IF(complete_statuses.completed_date IS NULL, "NOT YET", bookings.rating) AS rating',
            'bookings.rating_description'
        ])
            ->from('bookings')
            ->join('ref_booking_types', 'bookings.id_booking_type = ref_booking_types.id', 'left')
            ->join('ref_people', 'ref_people.id = bookings.id_customer', 'left')
            ->join('(
                    SELECT id_booking, MIN(booking_statuses.created_at) AS completed_date
                    FROM booking_statuses
                    WHERE booking_status = "COMPLETED"
                    GROUP BY id_booking
                ) AS complete_statuses', 'complete_statuses.id_booking = bookings.id', 'left')
            ->join('(
                SELECT uploads.id AS id_upload, upload_documents.created_at AS sppb_date
		        FROM uploads 
		        INNER JOIN upload_documents ON upload_documents.id_upload = uploads.id
                INNER JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                    AND ref_document_types.document_type = "SPPB"
              ) AS doc_sppb', 'doc_sppb.id_upload = bookings.id_upload', 'left')
            ->where('DATE(bookings.created_at)>=', '2019-09-01');

        if (!empty($filters)) {
            if (key_exists('search', $filters) && !empty($filters['search'])) {
                $filteredFields = [
                    'bookings.no_booking', 'bookings.no_reference', 'ref_people.name',
                    'ref_booking_types.category', 'bookings.rating', 'bookings.rated_at', 'bookings.rating_description',
                ];
                $baseQuery->group_start();
                foreach ($filteredFields as $filteredField) {
                    $baseQuery->or_like($filteredField, trim($filters['search']));
                }
                $baseQuery->group_end();
            }

            if (key_exists('is_completed', $filters)) {
                if ($filters['is_completed'] == 1) {
                    $baseQuery->where('complete_statuses.completed_date IS NOT NULL');
                }
                if ($filters['is_completed'] == 0) {
                    $baseQuery->where('complete_statuses.completed_date IS NULL');
                }
            }

            if (key_exists('is_sppb', $filters)) {
                if ($filters['is_sppb'] == 1) {
                    $baseQuery->where('doc_sppb.sppb_date IS NOT NULL');
                }
                if ($filters['is_sppb'] == 0) {
                    $baseQuery->where('doc_sppb.sppb_date IS NULL');
                }
            }

            if (key_exists('rating_outstanding', $filters) && $filters['rating_outstanding']) {
                $baseQuery->having('completed_date IS NOT NULL');
                $baseQuery->having('rating', 0);
            }

            if (key_exists('completed_outstanding', $filters) && $filters['completed_outstanding']) {
                $baseQuery->having('sppb_date IS NOT NULL');
                $baseQuery->having('completed_date IS NULL');
            }

            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                $baseQuery->where_in('ref_people.id', $filters['owner']);
            }

            if (key_exists('booking', $filters) && !empty($filters['booking'])) {
                $baseQuery->where_in('bookings.id', $filters['booking']);
            }

            if (key_exists('category', $filters) && !empty($filters['category'])) {
                $baseQuery->where_in('ref_booking_types.category', $filters['category']);
            }

            if (key_exists('star_from', $filters) && !empty($filters['star_from'])) {
                $baseQuery->where('bookings.rating>=', $filters['star_from']);
            }

            if (key_exists('star_to', $filters) && !empty($filters['star_to'])) {
                $baseQuery->where('bookings.rating<=', $filters['star_to']);
            }

            if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                $baseQuery->where('DATE(' . if_empty($filters['date_type'], 'bookings.rated_at') . ')>=', format_date($filters['date_from']));
            }

            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $baseQuery->where('DATE(' . if_empty($filters['date_type'], 'bookings.rated_at') . ')<=', format_date($filters['date_to']));
            }
        }

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $baseQuery->where('ref_people.id', $customerId);
        }

        $this->db->stop_cache();

        if (key_exists('get_total', $filters) && $filters['get_total']) {
            $totalCount = $this->db->count_all_results();

            $this->db->flush_cache();

            return $totalCount;
        }

        if ($start > -1) {
            $total = $this->db->count_all_results();
            $page = $baseQuery->order_by($columnSort, $sort)->limit($length, $start);
            $data = $page->get()->result_array();

            foreach ($data as &$row) {
                $row['no'] = ++$start;
            }

            $pageData = [
                "draw" => $this->input->get('draw'),
                "recordsTotal" => count($data),
                "recordsFiltered" => $total,
                "data" => $data
            ];
            $this->db->flush_cache();

            return $pageData;
        }

        if (key_exists('sort_by', $filters) && !empty($filters['sort_by'])) {
            if (key_exists('order_method', $filters) && !empty($filters['order_method'])) {
                $baseQuery->order_by($filters['sort_by'], $filters['order_method']);
            } else {
                $baseQuery->order_by($filters['sort_by'], 'asc');
            }
        } else {
            $baseQuery->order_by('bookings.created_at', 'desc');
        }

        $data = $baseQuery->get()->result_array();

        $this->db->flush_cache();

        return $data;
    }

    /**
     * @param $filters
     * @return array
     */
    public function getReportServiceTimeSummary($filters = [])
    {
        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            $branchId = $filters['branch'];
        } else {
            $branchId = get_active_branch('id');
        }

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;

        $columns = [
            'booking_date',
            'ref_people.name',
            'booking_type',
            'no_booking',
            'no_reference',
            'doc_invoice.no_document',
            'doc_bl.no_document',
            'booking_handlers.name',
            'doc_ata.created_at',
            'doc_do.created_at',
            'st_do',
            'doc_sppb.created_at',
            'doc_tila.created_at',
            'no_safe_conduct',
            'no_police',
            'driver',
            'expedition_type',
            'no_container',
            'security_in_date',
            'security_out_date',
            'st_inbound',
            'st_trucking'
        ];

        $this->db->start_cache();
        $baseQuery = $this->db->select([
            'bookings.booking_date',
            'ref_people.name AS customer_name',
            'ref_booking_types.booking_type',
            'bookings.no_booking',
            'bookings.no_reference',
            'doc_invoice.no_document AS no_invoice',
            'doc_bl.no_document AS no_bl',
            'booking_handlers.name AS assigned_do',
            'CONCAT(doc_ata.document_date, " 00:00:00") AS ata_date',
            'doc_do.created_at AS do_date',
            'TIMEDIFF(doc_do.created_at, TIMESTAMP(doc_ata.document_date)) AS st_do',
            'doc_sppb.created_at AS sppb_date',
            'doc_tila.created_at AS tila_date',
            'safe_conducts.no_safe_conduct',
            'safe_conducts.no_police AS police_number',
            'safe_conducts.driver',
            'safe_conducts.expedition_type',
            'ref_containers.no_container',
            'safe_conducts.security_in_date AS security_start',
            'safe_conducts.security_out_date AS security_stop',
            'IF(safe_conducts.expedition_type = "INTERNAL", TIMEDIFF(safe_conducts.security_out_date, TIMESTAMP(doc_ata.document_date)), TIMEDIFF(safe_conducts.security_in_date, TIMESTAMP(doc_ata.document_date))) AS st_inbound',
            'IF(safe_conducts.expedition_type = "INTERNAL", TIMEDIFF(safe_conducts.security_out_date, safe_conducts.security_in_date), TIMEDIFF(safe_conducts.security_in_date, doc_tila.created_at)) AS st_trucking'
        ])
            ->from('bookings')
            ->join('(
                SELECT uploads.id AS id_upload, upload_documents.no_document
		        FROM uploads 
		        INNER JOIN upload_documents ON upload_documents.id_upload = uploads.id
                INNER JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                    AND ref_document_types.document_type = "Invoice"
              ) AS doc_invoice', 'doc_invoice.id_upload = bookings.id_upload', 'left')
            ->join('(
                SELECT uploads.id AS id_upload, upload_documents.no_document
		        FROM uploads 
		        INNER JOIN upload_documents ON upload_documents.id_upload = uploads.id
                INNER JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                    AND ref_document_types.document_type = "Bill Of Loading"
              ) AS doc_bl', 'doc_bl.id_upload = bookings.id_upload', 'left')
            ->join('(
                SELECT uploads.id AS id_upload, upload_documents.document_date, upload_documents.created_at
		        FROM uploads 
		        INNER JOIN upload_documents ON upload_documents.id_upload = uploads.id
                INNER JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                    AND ref_document_types.document_type = "ATA"
              ) AS doc_ata', 'doc_ata.id_upload = bookings.id_upload', 'left')
            ->join('(
                SELECT uploads.id AS id_upload, upload_documents.created_at
		        FROM uploads 
		        INNER JOIN upload_documents ON upload_documents.id_upload = uploads.id
                INNER JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                    AND ref_document_types.document_type = "DO"
              ) AS doc_do', 'doc_do.id_upload = bookings.id_upload', 'left')
            ->join('(
                SELECT uploads.id AS id_upload, upload_documents.created_at
		        FROM uploads 
		        INNER JOIN upload_documents ON upload_documents.id_upload = uploads.id
                INNER JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                    AND ref_document_types.document_type = "SPPB"
              ) AS doc_sppb', 'doc_sppb.id_upload = bookings.id_upload', 'left')
            ->join('(
                SELECT uploads.id AS id_upload, upload_documents.created_at
		        FROM uploads 
		        INNER JOIN upload_documents ON upload_documents.id_upload = uploads.id
                INNER JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                    AND ref_document_types.document_type = "TILA"
              ) AS doc_tila', 'doc_tila.id_upload = bookings.id_upload', 'left')
            ->join('booking_assignments', 'booking_assignments.id_booking = bookings.id', 'left')
            ->join(UserModel::$tableUser . ' AS booking_handlers', 'booking_handlers.id = booking_assignments.id_user', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_people', 'ref_people.id = bookings.id_customer', 'left')
            ->join('safe_conducts', 'safe_conducts.id_booking = bookings.id', 'left')
            ->join('safe_conduct_containers', 'safe_conduct_containers.id_safe_conduct = safe_conducts.id', 'left')
            ->join('ref_containers', 'ref_containers.id = safe_conduct_containers.id_container', 'left')
            ->where([
                'bookings.is_deleted' => false,
                'bookings.id_branch' => $branchId,
                'ref_booking_types.category' => 'INBOUND'
            ]);

        if (!empty($search)) {
            $baseQuery->group_start();
            foreach ($columns as $filteredField) {
                if ($filteredField != 'st_do' && $filteredField != 'st_inbound' && $filteredField != 'st_trucking') {
                    $baseQuery->or_like($filteredField, trim($filters['search']));
                }
            }
            $baseQuery->group_end();
        }

        if ($userType == 'EXTERNAL') {
            $baseQuery->where('id_owner', $customerId);
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $total = $baseQuery->count_all_results();
        $page = $baseQuery->order_by($columns[$column], $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        foreach ($data as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($data),
            "recordsFiltered" => $total,
            "data" => $data
        ];
        $this->db->flush_cache();

        return $pageData;
    }

    /**
     * Get service time trucking.
     *
     * @param array $filters
     * @return array
     */
    public function getReportServiceTime($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branch = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        // alias column name by index for sorting data table library
        $columnOrder = [
            0 => "security_out_date",
            1 => "no_booking",
            2 => "no_reference",
            3 => "booking_category",
            4 => "no_safe_conduct",
            5 => "driver",
            6 => "no_police",
            7 => "expedition",
            8 => "owner_name",
            9 => "no_container",
            10 => "container_type",
            11 => "container_size",
            12 => "security_in_date",
            13 => "security_out_date",
            14 => "trucking_service_time",
            15 => "tally_name",
            16 => "no_work_order",
            17 => "queue_duration",
            18 => "taken_at",
            19 => "completed_at",
            20 => "tally_service_time",
            21 => "gate_in_date",
            22 => "gate_out_date",
            23 => "gate_service_time",
            24 => "booking_date",
            25 => "booking_service_time",
            26 => "booking_service_time_days",
        ];
        $columnSort = $columnOrder[$column];

        $this->db->start_cache();

        $defaultSelect = [
            'safe_conducts.security_out_date',
            'bookings.no_booking',
            'bookings.no_reference',
            'ref_booking_types.category AS booking_category',
            'safe_conducts.no_safe_conduct',
            'safe_conducts.driver',
            'safe_conducts.no_police',
            'safe_conducts.expedition',
            'ref_people.name AS owner_name',
            'ref_containers.no_container',
            'ref_containers.type AS container_type',
            'ref_containers.size AS container_size',
            'safe_conducts.security_in_date',
            'safe_conducts.security_out_date',
            'TIMEDIFF(security_out_date, security_in_date) AS trucking_service_time',
            'prv_users.name AS tally_name',
            'work_orders.no_work_order',
            'ref_branches.branch_type AS branch_type',
            'TIMEDIFF(taken_at, gate_in_date) AS queue_duration',
            'work_orders.taken_at',
            'work_orders.completed_at',
            'TIMEDIFF(completed_at, taken_at) AS tally_service_time',
            'work_orders.gate_in_date',
            'work_orders.gate_out_date',
            'TIMEDIFF(gate_out_date, gate_in_date) AS gate_service_time',
            'bookings.booking_date',
            'TIMEDIFF(gate_out_date, booking_date) AS booking_service_time',
            'DATEDIFF(gate_out_date, booking_date) AS booking_service_time_days',
        ];

        if (key_exists('average', $filters) && $filters['average']) {
            $defaultSelect = [
                'ref_booking_types.category AS booking_category',
                'TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(taken_at, gate_in_date)))), "%H:%i:%s") AS queue_duration',
                'TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(gate_out_date, gate_in_date)))), "%H:%i:%s") AS gate_service_time',
                'TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(completed_at, taken_at)))), "%H:%i:%s") AS tally_service_time',
                'TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(security_out_date, security_in_date)))), "%H:%i:%s") AS trucking_service_time',
                'TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(gate_out_date, booking_date)))), "%H:%i:%s") AS booking_service_time',
                'AVG(DATEDIFF(gate_out_date, booking_date)) AS booking_service_time_days'
            ];
        }

        $report = $this->db->select($defaultSelect)
            ->from('work_orders')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container')
            ->join('ref_people', 'ref_people.id = work_order_containers.id_owner')
            ->join(UserModel::$tableUser, 'prv_users.id = work_orders.taken_by')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type')
            ->where('work_orders.status', 'COMPLETED')
            ->where('work_orders.is_deleted', false)
            ->where('safe_conducts.is_deleted', false);

        if ($start < 0) {
            $report->order_by('completed_at', 'desc');
        }

        if (empty($filters) || !key_exists('data', $filters)) {
            //$report->where('id_safe_conduct IS NOT NULL');
            $report->where('id_work_order IS NOT NULL');
            $report->where('no_container IS NOT NULL');
            $report->where_in('handling_type', ['UNLOAD', 'LOAD', 'STRIPPING']);
        } else {
            if (key_exists('data', $filters) && !empty($filters['data'])) {
                if ($filters['data'] == 'realization') {
                    //$report->where('id_safe_conduct IS NOT NULL');
                    $report->where('id_work_order IS NOT NULL');
                    $report->where('no_container IS NOT NULL');
                    $report->where_in('handling_type', ['UNLOAD', 'LOAD', 'STRIPPING']);
                }
            }

            if (key_exists('branch_type', $filters) && !is_null($filters['branch_type'])) {
                $report->where('branch_type IS NOT NULL');
                $report->where('branch_type', $filters['branch_type']);
            }

            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $report->where_in('id_owner', $filters['owner']);
                } else {
                    $report->where('id_owner', $filters['owner']);
                }
            }

            if (key_exists('container', $filters) && !empty($filters['container'])) {
                if (is_array($filters['container'])) {
                    $report->where_in('work_order_containers.id_container', $filters['container']);
                } else {
                    $report->where('work_order_containers.id_container', $filters['container']);
                }
            }

            if (key_exists('sort_by', $filters) && !empty($filters['sort_by'])) {
                $order = key_exists('order', $filters) ? $filters['order'] : 'asc';
                $report->order_by($filters['sort_by'], $order);
            }

            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
                if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                    $report->where($filters['date_type'] . '>=', sql_date_format($filters['date_from']));
                }
                if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                    $report->where($filters['date_type'] . '<=', sql_date_format($filters['date_to']));
                }
            }
        }

        if (!empty($branch)) {
            $report->where('bookings.id_branch', $branch);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('id_owner', $customerId);
        }

        if (key_exists('average', $filters) && $filters['average']) {
            $report->group_by('ref_booking_types.category');
        }

        if (key_exists('search', $filters) && !empty($filters['search'])) {
            $search = (is_array($filters['search']) ? $search : $filters['search']);
        }

        $report
            ->group_start()
            ->like('no_booking', $search)
            ->or_like('no_reference', $search)
            ->or_like('ref_booking_types.category', $search)
            ->or_like('handling_type', $search)
            ->or_like('no_safe_conduct', $search)
            ->or_like('driver', $search)
            ->or_like('no_police', $search)
            ->or_like('safe_conducts.expedition', $search)
            ->or_like('ref_people.name', $search)
            ->or_like('no_container', $search)
            ->or_like('ref_containers.type', $search)
            ->or_like('ref_containers.size', $search)
            ->or_like('security_in_date', $search)
            ->or_like('security_out_date', $search)
            ->or_like('TIMEDIFF(security_out_date, security_in_date)', $search)
            ->or_like('prv_users.name', $search)
            ->or_like('no_work_order', $search)
            ->or_like('TIMEDIFF(taken_at, gate_in_date)', $search)
            ->or_like('taken_at', $search)
            ->or_like('completed_at', $search)
            ->or_like('TIMEDIFF(completed_at, taken_at)', $search)
            ->or_like('gate_in_date', $search)
            ->or_like('gate_out_date', $search)
            ->or_like('TIMEDIFF(gate_out_date, gate_in_date)', $search)
            ->or_like('booking_date', $search)
            ->or_like('TIMEDIFF(gate_out_date, booking_date)', $search)
            ->or_like('DATEDIFF(gate_out_date, booking_date)', $search)
            ->group_end();

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $report->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $reportTotal = $this->db->count_all_results();
        $report->order_by($columnSort, $sort);
        $reportData = $report->limit($length, $start)->get()->result_array();

        foreach ($reportData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($reportData),
            "recordsFiltered" => $reportTotal,
            "data" => $reportData
        ];

        $this->db->flush_cache();

        return $pageData;
    }

    /**
     * Get monthly service time.
     *
     * @param array $filters
     * @return mixed
     */
    public function getReportServiceTimeMonthly($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branch = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $handlingTypeIdInbound = get_setting('default_inbound_handling');
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        // alias column name by index for sorting data table library
        $columnOrder = [
            0 => "MAX(gate_in_date)",
            1 => "year",
            2 => "month_year",
            3 => "booking_category",
            4 => "trucking_service_time",
            5 => "queue_duration",
            6 => "tally_service_time",
            7 => "gate_service_time",
            8 => "booking_service_time",
            9 => "booking_service_time_days",
            10 => "no_container",
        ];
        $columnSort = $columnOrder[$column];

        $this->db->start_cache();

        $report = $this->db->select([
            'YEAR(gate_in_date) AS year',
            'MONTH(gate_in_date) AS month_year',
            'DATE_FORMAT(gate_in_date, "%M") AS month',
            'ref_booking_types.category AS booking_category',
            'TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(taken_at, gate_in_date)))), "%H:%i:%s") AS queue_duration',
            'TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(gate_out_date, gate_in_date)))), "%H:%i:%s") AS gate_service_time',
            'TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(completed_at, taken_at)))), "%H:%i:%s") AS tally_service_time',
            'TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(security_out_date, security_in_date)))), "%H:%i:%s") AS trucking_service_time',
            'TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(gate_out_date, booking_date)))), "%H:%i:%s") AS booking_service_time',
            'AVG(DATEDIFF(gate_out_date, booking_date)) AS booking_service_time_days',
        ])
            ->from('work_orders')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type')
            ->where('work_orders.status', 'COMPLETED')
            ->where('work_orders.is_deleted', false)
            ->where('safe_conducts.is_deleted', false)
            ->group_start()
            ->where('id_handling_type', $handlingTypeIdInbound)
            ->or_where('id_handling_type', $handlingTypeIdOutbound)
            ->group_end()
            ->group_by('year, month_year, month, category');

        if (!empty($filters)) {
            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $report->where_in('id_owner', $filters['owner']);
                } else {
                    $report->where('id_owner', $filters['owner']);
                }
            }
        }

        if (!empty($branch)) {
            $report->where('id_branch', $branch);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('id_owner', $customerId);
        }

        $report
            ->group_start()
            ->like('YEAR(gate_in_date)', $search)
            ->or_like('DATE_FORMAT(gate_in_date, "%M")', $search)
            ->or_like('category', $search)
            ->group_end()
            ->order_by($columnSort, $sort);

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $report->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $reportTotal = $this->db->count_all_results();
        $reportData = $report->limit($length, $start)->get()->result_array();

        foreach ($reportData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($reportData),
            "recordsFiltered" => $reportTotal,
            "data" => $reportData
        ];

        $this->db->flush_cache();

        return $pageData;
    }

    /**
     * Get monthly service time.
     * @param array $filters
     * @return mixed
     */
    public function getReportServiceTimeWeekly($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branch = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $handlingTypeIdInbound = get_setting('default_inbound_handling');
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        // alias column name by index for sorting data table library
        $columnOrder = [
            0 => "MAX(gate_in_date)",
            1 => "year",
            2 => "month_year",
            3 => "week",
            4 => "booking_category",
            5 => "trucking_service_time",
            6 => "queue_duration",
            7 => "tally_service_time",
            8 => "gate_service_time",
            9 => "booking_service_time",
            10 => "booking_service_time_days",
            11 => "no_container",
        ];
        $columnSort = $columnOrder[$column];

        $this->db->start_cache();

        $report = $this->db->select([
            'YEAR(gate_in_date) AS year',
            'MONTH(gate_in_date) AS month_year',
            'DATE_FORMAT(gate_in_date, "%M") AS month',
            'WEEK(gate_in_date) + 1 AS week',
            'ref_booking_types.category AS booking_category',
            'TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(taken_at, gate_in_date)))), "%H:%i:%s") AS queue_duration',
            'TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(gate_out_date, gate_in_date)))), "%H:%i:%s") AS gate_service_time',
            'TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(completed_at, taken_at)))), "%H:%i:%s") AS tally_service_time',
            'TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(security_out_date, security_in_date)))), "%H:%i:%s") AS trucking_service_time',
            'TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(gate_out_date, booking_date)))), "%H:%i:%s") AS booking_service_time',
            'AVG(DATEDIFF(gate_out_date, booking_date)) AS booking_service_time_days',
        ])
            ->from('work_orders')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type')
            ->where('work_orders.status', 'COMPLETED')
            ->where('work_orders.is_deleted', false)
            ->where('safe_conducts.is_deleted', false)
            ->group_start()
            ->where('id_handling_type', $handlingTypeIdInbound)
            ->or_where('id_handling_type', $handlingTypeIdOutbound)
            ->group_end()
            ->group_by('year, month_year, month, week, category');

        if (!empty($filters)) {
            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $report->where_in('id_owner', $filters['owner']);
                } else {
                    $report->where('id_owner', $filters['owner']);
                }
            }
        }

        if (!empty($branch)) {
            $report->where('id_branch', $branch);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('id_owner', $customerId);
        }

        $report
            ->group_start()
            ->like('YEAR(gate_in_date)', $search)
            ->or_like('DATE_FORMAT(gate_in_date, "%M")', $search)
            ->or_like('category', $search)
            ->or_like('(WEEK(gate_in_date) + 1)', $search)
            ->group_end()
            ->order_by($columnSort, $sort);

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $report->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $reportTotal = $this->db->count_all_results();
        $reportData = $report->limit($length, $start)->get()->result_array();

        foreach ($reportData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($reportData),
            "recordsFiltered" => $reportTotal,
            "data" => $reportData
        ];

        $this->db->flush_cache();

        return $pageData;
    }

    /**
     * Get driver service time.
     * @param null $filters
     * @return mixed
     */
    public function getReportServiceTimeDriver($filters = null)
    {
        $branchId = get_active_branch('id');

        $handlingTypeIdInbound = get_setting('default_inbound_handling');
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $report = $this->db->select([
            'driver',
            'TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(security_out_date, security_in_date)))), "%H:%i:%s") AS trucking_service_time',
        ])
            ->from('safe_conducts')
            ->join('work_orders', 'work_orders.id_safe_conduct = safe_conducts.id')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->where('safe_conducts.expedition_type', 'INTERNAL')
            ->where('driver IS NOT NULL')
            ->where('security_out_date != security_in_date')
            ->group_start()
            ->where('id_handling_type', $handlingTypeIdInbound)
            ->or_where('id_handling_type', $handlingTypeIdOutbound)
            ->group_end()
            ->group_by('driver');

        if (!empty($filters)) {
            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $report->where_in('id_owner', $filters['owner']);
                } else {
                    $report->where('id_owner', $filters['owner']);
                }
            }
        }

        if (!empty($branchId)) {
            $report->where('id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('id_owner', $customerId);
        }

        return $report->get()->result_array();
    }

    /**
     * Get tally service time.
     * @param string $type
     * @param null $filters
     * @return mixed
     */
    public function getReportServiceTimeTally($type = 'field', $filters = null)
    {
        $branchId = get_active_branch('id');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $report = $this->db->select([
            'prv_users.name AS tally_name',
            'TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(taken_at, gate_in_date)))), "%H:%i:%s") AS queue_duration',
            'TIME_FORMAT(SEC_TO_TIME(AVG(TIME_TO_SEC(TIMEDIFF(completed_at, taken_at)))), "%H:%i:%s") AS tally_service_time',
        ])
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join(UserModel::$tableUser, 'prv_users.id = work_orders.taken_by')
            ->where('work_orders.status', 'COMPLETED')
            ->where('work_orders.is_deleted', false)
            ->group_by('tally_name');

        if ($type == 'field') {
            $report->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id');
        } else {
            $report->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id');
        }

        if (!empty($filters)) {
            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $report->where_in('id_owner', $filters['owner']);
                } else {
                    $report->where('id_owner', $filters['owner']);
                }
            }
        }

        if (!empty($branchId)) {
            $report->where('id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('id_owner', $customerId);
        }

        return $report->get()->result_array();
    }

    /**
     * Get report service time control inbound.
     *
     * @param $filters
     * @return array
     */
    public function getReportServiceTimeControlInbound($filters)
    {
        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));

        $this->db->start_cache();

        $baseQuery = $this->db->select([
            'ref_branches.branch',
            'ref_booking_types.booking_type',
            'uploads.no_upload',
            'uploads.description AS no_reference',
            'bookings.no_reference AS booking_reference',
            'ref_people.id AS id_customer',
            'ref_people.name AS customer_name',
            'uploads.created_at AS upload_date',

            'strippings.id_booking AS id_booking_stripping',
            'last_unloads.id_booking AS id_booking_unload',
            'strippings.completed_at AS terakhir_stripping',
            'last_unloads.completed_at AS terakhir_unload',

            'IF(strippings.completed_at is NULL,last_unloads.completed_at,strippings.completed_at) AS last_bongkar_date',

            'IF(atas.ata_date IS NULL, 
                NULL, DATE(uploads.created_at) >= DATE(atas.ata_date)
            ) AS is_late_upload',
            'drafts.draft_date',
            // 'IF(drafts.draft_date IS NULL,
            //     NULL,
            //     IF(TIME(uploads.created_at) < "12:00:00", 
            //         drafts.draft_date > CONCAT(DATE(uploads.created_at), " 23:59:59"),
            //         drafts.draft_date > CONCAT(DATE_ADD(DATE(uploads.created_at), INTERVAL 1 DAY), " 12:00:00")
            //     )
            // ) AS is_late_draft',

            'IF(drafts.draft_date IS NULL,
                NULL,
                IF(DAYNAME(uploads.created_at)="Saturday" || DAYNAME(uploads.created_at)="Sunday",
                    IF(DAYNAME(uploads.created_at)="Saturday",
                        IF(get_holiday (DATE_ADD(DATE(uploads.created_at), INTERVAL 2 DAY))>0 ,
                            drafts.draft_date > CONCAT(DATE_ADD(DATE(uploads.created_at), INTERVAL (2+get_holiday (DATE_ADD(DATE(uploads.created_at), INTERVAL 2 DAY))) DAY), " 12:00:00"),
                            drafts.draft_date > CONCAT(DATE_ADD(DATE(uploads.created_at), INTERVAL 2 DAY), " 12:00:00")
                        ),
                        IF(get_holiday (DATE_ADD(DATE(uploads.created_at), INTERVAL 1 DAY))>0 ,
                            drafts.draft_date > CONCAT(DATE_ADD(DATE(uploads.created_at), INTERVAL (get_holiday (DATE_ADD(DATE(uploads.created_at), INTERVAL 1 DAY))) DAY), " 12:00:00"),
                            drafts.draft_date > CONCAT(DATE_ADD(DATE(uploads.created_at), INTERVAL 1 DAY), " 12:00:00")
                        )
                    ),
                    IF(get_holiday (DATE(uploads.created_at))>0,
                        drafts.draft_date > CONCAT(DATE_ADD(DATE(uploads.created_at), INTERVAL get_holiday (DATE(uploads.created_at)) DAY), " 12:00:00"),
                        IF(get_holiday (DATE_ADD(DATE(uploads.created_at), INTERVAL 1 DAY))>0,
                            IF(TIME(uploads.created_at) < "12:00:00", 
                                drafts.draft_date > CONCAT(DATE(uploads.created_at), " 23:59:59"), 
                                drafts.draft_date > CONCAT(DATE_ADD(DATE(uploads.created_at), INTERVAL (1+get_holiday (DATE_ADD(DATE(uploads.created_at), INTERVAL 1 DAY))) DAY), " 12:00:00")
                            ),
                            IF(TIME(uploads.created_at) < "12:00:00", 
                                drafts.draft_date > CONCAT(DATE(uploads.created_at), " 23:59:59"),
                                drafts.draft_date > CONCAT(DATE_ADD(DATE(uploads.created_at), INTERVAL 1 DAY), " 12:00:00")
                            )
                        )
                    )   
                ) 
            ) AS is_late_draft',
            'IF(draft_holidays.date is NULL, FALSE, TRUE) AS libur',

            'atas.ata_date',
            'confirmations.confirmation_date',

            'IF(drafts.draft_date IS NULL OR confirmations.confirmation_date IS NULL,
                NULL,
                IF(DAYNAME(drafts.draft_date)="Saturday" && DAYNAME(drafts.draft_date)="Sunday",
                    IF(DAYNAME(drafts.draft_date)="Saturday",
                    confirmations.confirmation_date > CONCAT(DATE_ADD(DATE(drafts.draft_date), INTERVAL 2 DAY), " 12:00:00"),
                    confirmations.confirmation_date > CONCAT(DATE_ADD(DATE(drafts.draft_date), INTERVAL 1 DAY), " 12:00:00")
                    ),
                    IF(TIME(drafts.draft_date) < "12:00:00", 
                    confirmations.confirmation_date > CONCAT(DATE(drafts.draft_date), " 23:59:59"),
                    confirmations.confirmation_date > CONCAT(DATE_ADD(DATE(drafts.draft_date), INTERVAL 1 DAY), " 12:00:00")
                    )
                )
            ) AS is_late_confirmation',

            'dos.do_date',
            '(dos.do_date > DATE_ADD(atas.ata_date, INTERVAL 1 DAY)) AS is_late_do',

            'sppbs.sppb_date',
            'IF(dos.do_date IS NULL OR sppbs.sppb_date IS NULL,
                NULL,
                IF(TIME(dos.do_date) < "12:00:00", 
                    sppbs.sppb_date > CONCAT(DATE(dos.do_date), " 23:59:59"),
                    sppbs.sppb_date > CONCAT(DATE_ADD(DATE(dos.do_date), INTERVAL 1 DAY), " 12:00:00")
                )
            ) AS is_late_sppb',

            'safe_conducts.security_out_date AS security_inbound',
            // 'IF(sppbs.sppb_date IS NULL OR safe_conducts.security_out_date IS NULL,
            //     NULL,
            //     IF(TIME(sppbs.sppb_date) < "12:00:00", 
            //         safe_conducts.security_out_date > CONCAT(DATE(sppbs.sppb_date), " 23:59:59"),
            //         safe_conducts.security_out_date > CONCAT(DATE_ADD(DATE(sppbs.sppb_date), INTERVAL 1 DAY), " 12:00:00")
            //     )
            // ) AS is_late_security_inbound',
            // 'IF(sppbs.sppb_date IS NULL OR safe_conducts.security_in_date IS NULL,
            //     NULL,
            //     IF(TIME(sppbs.sppb_date) < "12:00:00", 
            //         safe_conducts.security_in_date > CONCAT(DATE(sppbs.sppb_date), " 23:59:59"),
            //         safe_conducts.security_in_date > CONCAT(DATE_ADD(DATE(sppbs.sppb_date), INTERVAL 1 DAY), " 12:00:00")
            //     )
            // ) AS is_late_security_inbound_external',

            //disini ada pengecualian sppd_date yang lebih besar dari last tci
            'IF(safe_conducts.expedition_type="INTERNAL",
                IF(sppbs.sppb_date IS NULL OR safe_conducts.security_out_date IS NULL,
                NULL,
                IF(TIME(sppbs.sppb_date) < "12:00:00", 
                    safe_conducts.security_out_date > CONCAT(DATE(sppbs.sppb_date), " 23:59:59"),
                    IF(sppbs.sppb_date>safe_conducts.security_out_date,1,
                    safe_conducts.security_out_date > CONCAT(DATE_ADD(DATE(sppbs.sppb_date), INTERVAL 1 DAY), " 12:00:00"))
                )
            ),IF(sppbs.sppb_date IS NULL OR safe_conducts.security_in_date IS NULL,
                NULL,
                IF(TIME(sppbs.sppb_date) < "12:00:00", 
                    safe_conducts.security_in_date > CONCAT(DATE(sppbs.sppb_date), " 23:59:59"),
                    IF(sppbs.sppb_date>safe_conducts.security_out_date,1,
                    safe_conducts.security_in_date > CONCAT(DATE_ADD(DATE(sppbs.sppb_date), INTERVAL 1 DAY), " 12:00:00"))
                    
                )
            )
            ) AS is_late_security_inbound',


            'IF(safe_conducts.expedition_type="INTERNAL",safe_conducts.security_out_date,safe_conducts.security_in_date
            ) AS last_in_tci',

            'unloads.completed_at AS unload_date',
            'last_unloads.completed_at AS last_unload_date',
            'IF(unloads.gate_in_date IS NULL OR unloads.completed_at IS NULL,
                NULL,
                IF(TIME(unloads.gate_in_date) < "12:00:00", 
                    unloads.completed_at > CONCAT(DATE(unloads.gate_in_date), " 23:59:59"),
                    unloads.completed_at > CONCAT(DATE_ADD(DATE(unloads.gate_in_date), INTERVAL 1 DAY), " 12:00:00")
                )
            ) AS is_late_unload',

            'strippings.completed_at AS stripping_date',
            'last_strippings.completed_at AS last_stripping_date',
            'IF(strippings.completed_at IS NULL OR unloads.completed_at IS NULL,
                NULL,
                IF(TIME(strippings.gate_in_date) < "12:00:00", 
                    strippings.completed_at > CONCAT(DATE(strippings.gate_in_date), " 23:59:59"),
                    strippings.completed_at > CONCAT(DATE_ADD(DATE(strippings.gate_in_date), INTERVAL 1 DAY), " 12:00:00")
                )
            ) AS is_late_stripping',

            'IF(complete_statuses.completed_date IS NULL,
                NULL,
                IF(strippings.completed_at IS NULL, last_unloads.completed_at, strippings.completed_at)
            ) AS bongkar_date',
            'IF(safe_conducts.expedition_type="INTERNAL",
                IF(complete_statuses.completed_date IS NULL OR safe_conducts.security_out_date IS NULL,
                NULL,
                IF(TIME(safe_conducts.security_out_date) < "12:00:00", 
                    IF(strippings.completed_at IS NULL, last_unloads.completed_at, strippings.completed_at) > CONCAT(DATE(safe_conducts.security_out_date), " 23:59:59"),
                    IF(strippings.completed_at IS NULL, last_unloads.completed_at, strippings.completed_at) > CONCAT(DATE_ADD(DATE(safe_conducts.security_out_date), INTERVAL 1 DAY), " 12:00:00")
                )
            ),IF(complete_statuses.completed_date IS NULL OR safe_conducts.security_in_date IS NULL,
                NULL,
                IF(TIME(safe_conducts.security_in_date) < "12:00:00", 
                    IF(strippings.completed_at IS NULL, last_unloads.completed_at, strippings.completed_at) > CONCAT(DATE(safe_conducts.security_in_date), " 23:59:59"),
                    IF(strippings.completed_at IS NULL, last_unloads.completed_at, strippings.completed_at) > CONCAT(DATE_ADD(DATE(safe_conducts.security_in_date), INTERVAL 1 DAY), " 12:00:00")
                )
            )
            ) AS is_late_bongkar',

            'IF(strippings.completed_at IS NULL,0,1) AS is_stripping',
            'IF(safe_conducts.expedition_type="INTERNAL",
                IF(complete_statuses.completed_date IS NULL OR safe_conducts.security_out_date IS NULL,
                NULL,
                IF(TIME(safe_conducts.security_out_date) < "12:00:00",
                    IF(strippings.completed_at IS NULL,
                        last_unloads.completed_at > CONCAT(DATE(safe_conducts.security_out_date), " 23:59:59"),
                        strippings.completed_at > CONCAT(DATE(safe_conducts.security_out_date), " 23:59:59")
                    ),
                    IF(strippings.completed_at IS NULL,
                        last_unloads.completed_at > CONCAT(DATE_ADD(DATE(safe_conducts.security_out_date), INTERVAL 1 DAY), " 12:00:00"),
                        strippings.completed_at > CONCAT(DATE_ADD(DATE(safe_conducts.security_out_date), INTERVAL 1 DAY), " 12:00:00")
                    )   
                )
            ),IF(complete_statuses.completed_date IS NULL OR safe_conducts.security_in_date IS NULL,
                NULL,
                IF(TIME(safe_conducts.security_in_date) < "12:00:00", 
                    IF(strippings.completed_at IS NULL,
                        last_unloads.completed_at > CONCAT(DATE(safe_conducts.security_in_date), " 23:59:59"),
                        strippings.completed_at > CONCAT(DATE(safe_conducts.security_in_date), " 23:59:59")
                    ),
                    IF(strippings.completed_at IS NULL,
                        last_unloads.completed_at > CONCAT(DATE_ADD(DATE(safe_conducts.security_in_date), INTERVAL 1 DAY), " 12:00:00"),
                        strippings.completed_at > CONCAT(DATE_ADD(DATE(safe_conducts.security_in_date), INTERVAL 1 DAY), " 12:00:00")
                    ) 
                )
            )
            ) AS is_late_bongkar_x',

            'safe_conducts.expedition_type',
            'safe_conducts.security_out_date',
            'safe_conducts.security_in_date',
            'safe_conducts.no_safe_conduct',

            'complete_statuses.completed_date AS completed_date',
            'IF(last_strippings.completed_at IS NULL OR last_unloads.completed_at IS NULL,
                NULL,
                IF(TIME(IFNULL(last_strippings.completed_at, last_unloads.completed_at)) < "12:00:00", 
                    complete_statuses.completed_date >= CONCAT(DATE(IFNULL(last_strippings.completed_at, last_unloads.completed_at)), " 12:00:00"),
                    complete_statuses.completed_date >= CONCAT(DATE(IFNULL(last_strippings.completed_at, last_unloads.completed_at)), " 23:59:59")
                )
            ) AS is_late_completed',

            'sppds.sppd_date',
            'IF(sppds.sppd_date IS NULL OR complete_statuses.completed_date IS NULL,
                NULL,
                IF(TIME(complete_statuses.completed_date) < "12:00:00", 
                    sppds.sppd_date > CONCAT(DATE(complete_statuses.completed_date), " 23:59:59"),
                    sppds.sppd_date > CONCAT(DATE_ADD(DATE(complete_statuses.completed_date), INTERVAL 1 DAY), " 12:00:00")
                )
            ) AS is_late_sppd',

            'DATEDIFF(sppbs.sppb_date, IF(DATE(uploads.created_at) > atas.ata_date, atas.ata_date, DATE(uploads.created_at))) AS service_time_doc',

            'TIMEDIFF(safe_conducts.security_out_date, sppbs.sppb_date) AS service_time_inbound_delivery',
            'IF(sppbs.sppb_date IS NULL OR safe_conducts.security_out_date IS NULL,
                NULL,
                IF(TIME(sppbs.sppb_date) < "12:00:00", 
                    safe_conducts.security_out_date > CONCAT(DATE(sppbs.sppb_date), " 23:59:59"),
                    safe_conducts.security_out_date > CONCAT(DATE_ADD(DATE(sppbs.sppb_date), INTERVAL 1 DAY), " 12:00:00")
                )
            ) AS is_late_st_inbound_delivery',
            "CONCAT(
                FLOOR(TIMESTAMPDIFF(HOUR, sppbs.sppb_date, safe_conducts.security_out_date) / 24), ' days ',
                MOD(TIMESTAMPDIFF(HOUR, sppbs.sppb_date, safe_conducts.security_out_date), 24), ' hours ',
                MOD(TIMESTAMPDIFF(MINUTE, sppbs.sppb_date, safe_conducts.security_out_date), 60), ' minutes') AS service_time_inbound_delivery_label",

            'TIMEDIFF(complete_statuses.completed_date, safe_conducts.security_out_date) AS service_time_unload',
            'IF(complete_statuses.completed_date IS NULL OR safe_conducts.security_out_date IS NULL,
                NULL,
                IF(TIME(safe_conducts.security_out_date) < "12:00:00", 
                    complete_statuses.completed_date > CONCAT(DATE(safe_conducts.security_out_date), " 23:59:59"),
                    complete_statuses.completed_date > CONCAT(DATE_ADD(DATE(safe_conducts.security_out_date), INTERVAL 1 DAY), " 12:00:00")
                )
            ) AS is_late_st_unload',
            "CONCAT(
                FLOOR(TIMESTAMPDIFF(HOUR, safe_conducts.security_out_date, complete_statuses.completed_date) / 24), ' days ',
                MOD(TIMESTAMPDIFF(HOUR, safe_conducts.security_out_date, complete_statuses.completed_date), 24), ' hours ',
                MOD(TIMESTAMPDIFF(MINUTE, safe_conducts.security_out_date, complete_statuses.completed_date), 60), ' minutes') AS service_time_unload_label"
        ])
            ->from('uploads')
            ->join('ref_people', 'ref_people.id = uploads.id_person', 'left')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join("(
                    SELECT id_upload, upload_documents.created_at AS draft_date
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE document_type IN('BC 1.6 Draft', 'BC 2.8 Draft', 'BC 2.7 Draft')
                ) AS drafts", 'drafts.id_upload = uploads.id', 'left')
            ->join("(
                    SELECT id_upload, upload_documents.document_date AS ata_date
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE document_type = 'ATA'
                ) AS atas", 'atas.id_upload = uploads.id', 'left')
            ->join("(
                    SELECT id_upload, MAX(upload_documents.created_at) AS confirmation_date
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE document_type IN('BC 1.6 Confirmation', 'BC 2.8 Confirmation', 'BC 2.7 Confirmation')
                    GROUP BY id_upload
                ) AS confirmations", 'confirmations.id_upload = uploads.id', 'left')
            ->join("(
                    SELECT id_upload, upload_documents.created_at AS do_date
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE document_type = 'DO' AND upload_documents.`is_deleted`='0'
                ) AS dos", 'dos.id_upload = uploads.id', 'left')
            ->join("(
                    SELECT id_upload, upload_documents.created_at AS sppb_date
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE document_type = 'SPPB'
                ) AS sppbs", 'sppbs.id_upload = uploads.id', 'left')
            ->join('bookings', 'bookings.id_upload = uploads.id', 'left')
            ->join('(
                    SELECT work_orders.id, handlings.id_booking, id_safe_conduct, id_container, completed_at, gate_in_date FROM work_orders 
                    INNER JOIN handlings ON handlings.id = work_orders.id_handling
                    LEFT JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                    WHERE id_handling_type = 1 AND work_orders.is_deleted = FALSE
                ) AS unloads', 'unloads.id_booking = bookings.id', 'left')
            ->join('safe_conducts', 'safe_conducts.id = unloads.id_safe_conduct', 'left')
            ->join('(
                    SELECT bookings.id AS id_booking, MAX(completed_at) AS completed_at FROM work_orders 
                    INNER JOIN handlings ON handlings.id = work_orders.id_handling
                    INNER JOIN bookings ON bookings.id = handlings.id_booking
                    WHERE id_handling_type = 1 AND work_orders.is_deleted = FALSE
                    GROUP BY bookings.id
                ) AS last_unloads', 'last_unloads.id_booking = bookings.id', 'left')
            ->join('(
                    SELECT work_orders.id, id_booking, id_container, completed_at, gate_in_date FROM work_orders 
                    INNER JOIN handlings ON handlings.id = work_orders.id_handling
                    LEFT JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                    WHERE id_handling_type = 3 AND work_orders.is_deleted = FALSE
                ) AS strippings', 'strippings.id_booking = bookings.id AND strippings.id_container = unloads.id_container', 'left')
            ->join('(
                    SELECT bookings.id AS id_booking, MAX(completed_at) AS completed_at FROM work_orders 
                    INNER JOIN handlings ON handlings.id = work_orders.id_handling
                    INNER JOIN bookings ON bookings.id = handlings.id_booking
                    WHERE id_handling_type = 1 AND work_orders.is_deleted = FALSE
                    GROUP BY bookings.id
                ) AS last_strippings', 'last_strippings.id_booking = bookings.id', 'left')
            ->join('(
                    SELECT id_booking, MIN(booking_statuses.created_at) AS completed_date
                    FROM booking_statuses
                    WHERE booking_status = "COMPLETED"
                    GROUP BY id_booking
                ) AS complete_statuses', 'complete_statuses.id_booking = bookings.id', 'left')
            ->join('(
                    SELECT id_upload, upload_documents.created_at AS sppd_date
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE document_type = "SPPD"
                ) AS sppds', 'sppds.id_upload = uploads.id', 'left')
            ->join($this->tableHoliday . ' AS draft_holidays', 'draft_holidays.date = DATE(uploads.created_at)', 'left')
            ->where_not_in('ref_branches.branch', ['PDPLB - MEGASETIA'])
            ->where('ref_booking_types.category', 'INBOUND')
            ->where('uploads.is_deleted', false)
            ->where('uploads.id_branch', $branchId);

        if (!empty($filters)) {
            if (key_exists('search', $filters) && !empty($filters['search'])) {
                $filteredFields = [
                    'uploads.description', 'bookings.no_reference', 'ref_people.name'
                ];
                $baseQuery->group_start();
                foreach ($filteredFields as $filteredField) {
                    $baseQuery->or_like($filteredField, trim($filters['search']));
                }
                $baseQuery->group_end();
            }

            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                $baseQuery->where_in('ref_people.id', $filters['owner']);
            }

            if (key_exists('booking', $filters) && !empty($filters['booking'])) {
                $baseQuery->where_in('bookings.id', $filters['booking']);
            }

            if (key_exists('sppd_status', $filters) && !empty($filters['sppd_status'])) {
                if ($filters['sppd_status'] == 'DONE') {
                    $baseQuery->where('sppds.sppd_date IS NOT NULL');
                } else {
                    $baseQuery->where('sppds.sppd_date IS NULL');
                }
            }

            if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                $baseQuery->where('uploads.created_at>=', format_date($filters['date_from']));
            }

            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $baseQuery->where('uploads.created_at<=', format_date($filters['date_to']));
            }
        }
        $this->db->stop_cache();

        if (key_exists('per_page', $filters) && !empty($filters['per_page'])) {
            $perPage = $filters['per_page'];
        } else {
            $perPage = 25;
        }

        if (key_exists('page', $filters) && !empty($filters['page'])) {
            $currentPage = $filters['page'];

            $totalData = $this->db->count_all_results();

            if (key_exists('sort_by', $filters) && !empty($filters['sort_by'])) {
                if (key_exists('order_method', $filters) && !empty($filters['order_method'])) {
                    $baseQuery->order_by($filters['sort_by'], $filters['order_method']);
                } else {
                    $baseQuery->order_by($filters['sort_by'], 'asc');
                }
            } else {
                $baseQuery->order_by('uploads.created_at', 'desc');
                $baseQuery->order_by('last_in_tci', 'desc');
            }
            $pageData = $baseQuery->limit($perPage, ($currentPage - 1) * $perPage)->get()->result_array();

            $this->db->flush_cache();

            return [
                '_paging' => true,
                'total_data' => $totalData,
                'total_page_data' => count($pageData),
                'total_page' => ceil($totalData / $perPage),
                'per_page' => $perPage,
                'current_page' => $currentPage,
                'data' => $pageData
            ];
        }

        if (key_exists('sort_by', $filters) && !empty($filters['sort_by'])) {
            if (key_exists('order_method', $filters) && !empty($filters['order_method'])) {
                $baseQuery->order_by($filters['sort_by'], $filters['order_method']);
            } else {
                $baseQuery->order_by($filters['sort_by'], 'asc');
            }
        } else {
            $baseQuery->order_by('uploads.created_at', 'desc');
            $baseQuery->order_by('last_in_tci', 'desc');
        }

        $data = $baseQuery->get()->result_array();

        $this->db->flush_cache();

        return $data;
    }

    /**
     * Get report service time control outbound.
     *
     * @param $filters
     * @return array
     */
    public function getReportServiceTimeControlOutbound($filters)
    {
        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));

        $this->db->start_cache();

        $baseQuery = $this->db->select([
            'ref_branches.branch',
            'ref_booking_types.booking_type',
            'uploads.no_upload',
            'uploads.description AS no_reference',
            'bookings.no_reference AS booking_reference',
            'inbounds.no_reference AS booking_in_reference',
            'ref_people.id AS id_customer',
            'ref_people.name AS customer_name',
            'uploads.created_at AS upload_date',
            'drafts.draft_date',
            'IF(drafts.draft_date IS NULL,
                NULL,
                IF(TIME(uploads.created_at) < "12:00:00", 
                    drafts.draft_date > CONCAT(DATE(uploads.created_at), " 23:59:59"),
                    drafts.draft_date > CONCAT(DATE_ADD(DATE(uploads.created_at), INTERVAL 1 DAY), " 12:00:00")
                )
            ) AS is_late_draft',
            'confirmations.confirmation_date',
            'IF(drafts.draft_date IS NULL OR confirmations.confirmation_date IS NULL,
                NULL,
                IF(TIME(drafts.draft_date) < "12:00:00", 
                    confirmations.confirmation_date > CONCAT(DATE(drafts.draft_date), " 23:59:59"),
                    confirmations.confirmation_date > CONCAT(DATE_ADD(DATE(drafts.draft_date), INTERVAL 1 DAY), " 12:00:00")
                )
            ) AS is_late_confirmation',
            'billings.billing_date',
            'IF(billings.billing_date IS NULL OR confirmations.confirmation_date IS NULL,
                NULL,
                IF(TIME(confirmations.confirmation_date) < "12:00:00", 
                    billings.billing_date > CONCAT(DATE(confirmations.confirmation_date), " 23:59:59"),
                    billings.billing_date > CONCAT(DATE_ADD(DATE(confirmations.confirmation_date), INTERVAL 1 DAY), " 12:00:00")
                )
            ) AS is_late_billing',

            'bpns.bpn_date AS payment_date',
            'IF(billings.billing_date IS NULL OR bpns.bpn_date IS NULL,
                NULL,
                IF(TIME(billings.billing_date) < "12:00:00", 
                    bpns.bpn_date > CONCAT(DATE(billings.billing_date), " 23:59:59"),
                    bpns.bpn_date > CONCAT(DATE_ADD(DATE(billings.billing_date), INTERVAL 1 DAY), " 12:00:00")
                )
            ) AS is_late_payment',

            'bpns.bpn_date',
            '(bpns.bpn_date > DATE_ADD(billings.billing_date, INTERVAL 5 DAY)) AS is_late_bpn',

            'sppbs.sppb_date',
            'IF(bpns.bpn_date IS NULL OR sppbs.sppb_date IS NULL,
                NULL,
                IF(TIME(bpns.bpn_date) < "12:00:00", 
                    sppbs.sppb_date > CONCAT(DATE(bpns.bpn_date), " 23:59:59"),
                    sppbs.sppb_date > CONCAT(DATE_ADD(DATE(bpns.bpn_date), INTERVAL 1 DAY), " 12:00:00")
                )
            ) AS is_late_sppb',

            'transporter_entry_permits.checked_in_at AS tep_checked_in_date',
            'safe_conducts.security_in_date',
            'safe_conducts.security_out_date',

            'loads.completed_at AS load_date',
            'last_loads.completed_at AS last_load_date',
            'IF(safe_conducts.security_in_date IS NULL OR loads.completed_at IS NULL,
                NULL,
                IF(TIME(safe_conducts.security_in_date) < "12:00:00", 
                    loads.completed_at > CONCAT(DATE(safe_conducts.security_in_date), " 23:59:59"),
                    loads.completed_at > CONCAT(DATE_ADD(DATE(safe_conducts.security_in_date), INTERVAL 1 DAY), " 12:00:00")
                )
            ) AS is_late_load',
            'loads.gate_out_date',
            'IF(loads.gate_out_date IS NULL OR loads.completed_at IS NULL,
                NULL,
                IF(TIME(loads.gate_out_date) < "12:00:00", 
                    loads.completed_at > CONCAT(DATE(loads.gate_out_date), " 23:59:59"),
                    loads.completed_at > CONCAT(DATE_ADD(DATE(loads.gate_out_date), INTERVAL 1 DAY), " 12:00:00")
                )
            ) AS is_late_gate_out',

            'complete_statuses.completed_date AS booking_complete',
            'IF(safe_conducts.expedition_type="INTERNAL",
                IF(complete_statuses.completed_date IS NULL OR sppbs.sppb_date IS NULL,
                NULL,
                IF(TIME(sppbs.sppb_date) < "12:00:00", 
                    complete_statuses.completed_date > CONCAT(DATE(sppbs.sppb_date), " 23:59:59"),
                    complete_statuses.completed_date > CONCAT(DATE_ADD(DATE(sppbs.sppb_date), INTERVAL 1 DAY), " 12:00:00")
                )
            ),IF(complete_statuses.completed_date IS NULL OR safe_conducts.security_in_date IS NULL,
                NULL,
                IF(TIME(safe_conducts.security_in_date) < "12:00:00", 
                    complete_statuses.completed_date > CONCAT(DATE(safe_conducts.security_in_date), " 23:59:59"),
                    complete_statuses.completed_date > CONCAT(DATE_ADD(DATE(safe_conducts.security_in_date), INTERVAL 1 DAY), " 12:00:00")
                )
            )
            ) AS is_late_booking_complete',
            'IF(safe_conducts.expedition_type="INTERNAL",sppbs.sppb_date,safe_conducts.security_in_date
            ) AS sppb_sec_start_date',

            'TIMEDIFF(complete_statuses.completed_date, safe_conducts.security_in_date) AS service_time_load',
            'IF(complete_statuses.completed_date IS NULL OR safe_conducts.security_in_date IS NULL,
                NULL,
                IF(TIME(safe_conducts.security_in_date) < "12:00:00", 
                    complete_statuses.completed_date > CONCAT(DATE(safe_conducts.security_in_date), " 23:59:59"),
                    complete_statuses.completed_date > CONCAT(DATE_ADD(DATE(safe_conducts.security_in_date), INTERVAL 1 DAY), " 12:00:00")
                )
            ) AS is_late_st_load',

            'IF(safe_conducts.expedition_type="INTERNAL",sppbs.sppb_date,safe_conducts.security_in_date
            ) AS muat_internal_external',

            "CONCAT(
                FLOOR(TIMESTAMPDIFF(HOUR, safe_conducts.security_in_date, complete_statuses.completed_date) / 24), ' days ',
                MOD(TIMESTAMPDIFF(HOUR, safe_conducts.security_in_date, complete_statuses.completed_date), 24), ' hours ',
                MOD(TIMESTAMPDIFF(MINUTE, safe_conducts.security_in_date, complete_statuses.completed_date), 60), ' minutes') AS service_time_load_label"
        ])
            ->from('uploads')
            ->join('ref_people', 'ref_people.id = uploads.id_person', 'left')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join("(
                    SELECT id_upload, upload_documents.created_at AS draft_date
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE document_type IN('BC 1.6 Draft', 'BC 2.8 Draft', 'BC 2.7 Draft')
                ) AS drafts", 'drafts.id_upload = uploads.id', 'left')
            ->join("(
                    SELECT id_upload, upload_documents.created_at AS confirmation_date
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE document_type IN('BC 1.6 Confirmation', 'BC 2.8 Confirmation', 'BC 2.7 Confirmation')
                ) AS confirmations", 'confirmations.id_upload = uploads.id', 'left')
            ->join("(
                    SELECT id_upload, upload_documents.created_at AS billing_date
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE document_type = 'E Billing'
                ) AS billings", 'billings.id_upload = uploads.id', 'left')
            ->join("(
                    SELECT id_upload, upload_documents.created_at AS bpn_date
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE document_type = 'BPN (Bukti Penerimaan Negara)'
                ) AS bpns", 'bpns.id_upload = uploads.id', 'left')
            ->join("(
                    SELECT id_upload, upload_documents.created_at AS sppb_date
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE document_type = 'SPPB'
                ) AS sppbs", 'sppbs.id_upload = uploads.id', 'left')
            ->join('bookings', 'bookings.id_upload = uploads.id', 'left')
            ->join('bookings AS inbounds', 'inbounds.id = bookings.id_booking', 'left')
            ->join('safe_conducts', 'safe_conducts.id_booking = bookings.id', 'left')
            ->join('transporter_entry_permits', 'transporter_entry_permits.id = safe_conducts.id_transporter_entry_permit', 'left')
            ->join('(
                    SELECT work_orders.id, id_safe_conduct, id_container, completed_at, gate_out_date FROM work_orders 
                    INNER JOIN handlings ON handlings.id = work_orders.id_handling
                    LEFT JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                    WHERE id_handling_type = 2 AND work_orders.is_deleted = FALSE
                ) AS loads', 'loads.id_safe_conduct = safe_conducts.id', 'left')
            ->join('(
                    SELECT bookings.id AS id_booking, MAX(completed_at) AS completed_at FROM work_orders 
                    INNER JOIN handlings ON handlings.id = work_orders.id_handling
                    INNER JOIN bookings ON bookings.id = handlings.id_booking
                    WHERE id_handling_type = 2 AND work_orders.is_deleted = FALSE
                    GROUP BY bookings.id
                ) AS last_loads', 'last_loads.id_booking = bookings.id', 'left')
            ->join('(
                    SELECT id_booking, MIN(booking_statuses.created_at) AS completed_date
                    FROM booking_statuses
                    WHERE booking_status = "COMPLETED"
                    GROUP BY id_booking
                ) AS complete_statuses', 'complete_statuses.id_booking = bookings.id', 'left')
            ->join('(
                    SELECT id_upload, upload_documents.created_at AS sppd_date
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE document_type = "SPPD"
                ) AS sppds', 'sppds.id_upload = uploads.id', 'left')
            ->where_not_in('ref_branches.branch', ['PDPLB - MEGASETIA'])
            ->where('ref_booking_types.category', 'OUTBOUND')
            ->where('uploads.is_deleted', false)
            ->where('uploads.id_branch', $branchId);

        if (!empty($filters)) {
            if (key_exists('search', $filters) && !empty($filters['search'])) {
                $filteredFields = [
                    'uploads.description', 'bookings.no_reference', 'ref_people.name'
                ];
                $baseQuery->group_start();
                foreach ($filteredFields as $filteredField) {
                    $baseQuery->or_like($filteredField, trim($filters['search']));
                }
                $baseQuery->group_end();
            }

            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                $baseQuery->where_in('ref_people.id', $filters['owner']);
            }

            if (key_exists('booking', $filters) && !empty($filters['booking'])) {
                $baseQuery->where_in('bookings.id', $filters['booking']);
            }

            if (key_exists('sppd_status', $filters) && !empty($filters['sppd_status'])) {
                if ($filters['sppd_status'] == 'DONE') {
                    $baseQuery->where('sppds.sppd_date IS NOT NULL');
                } else {
                    $baseQuery->where('sppds.sppd_date IS NULL');
                }
            }

            if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                $baseQuery->where('uploads.created_at>=', format_date($filters['date_from']));
            }

            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $baseQuery->where('uploads.created_at<=', format_date($filters['date_to']));
            }
        }
        $this->db->stop_cache();

        if (key_exists('per_page', $filters) && !empty($filters['per_page'])) {
            $perPage = $filters['per_page'];
        } else {
            $perPage = 25;
        }

        if (key_exists('page', $filters) && !empty($filters['page'])) {
            $currentPage = $filters['page'];

            $totalData = $this->db->count_all_results();

            if (key_exists('sort_by', $filters) && !empty($filters['sort_by'])) {
                if (key_exists('order_method', $filters) && !empty($filters['order_method'])) {
                    $baseQuery->order_by($filters['sort_by'], $filters['order_method']);
                } else {
                    $baseQuery->order_by($filters['sort_by'], 'asc');
                }
            } else {
                $baseQuery->order_by('uploads.created_at', 'desc');
            }
            $pageData = $baseQuery->limit($perPage, ($currentPage - 1) * $perPage)->get()->result_array();

            $this->db->flush_cache();

            return [
                '_paging' => true,
                'total_data' => $totalData,
                'total_page_data' => count($pageData),
                'total_page' => ceil($totalData / $perPage),
                'per_page' => $perPage,
                'current_page' => $currentPage,
                'data' => $pageData
            ];
        }

        if (key_exists('sort_by', $filters) && !empty($filters['sort_by'])) {
            if (key_exists('order_method', $filters) && !empty($filters['order_method'])) {
                $baseQuery->order_by($filters['sort_by'], $filters['order_method']);
            } else {
                $baseQuery->order_by($filters['sort_by'], 'asc');
            }
        } else {
            $baseQuery->order_by('uploads.created_at', 'desc');
        }

        $data = $baseQuery->get()->result_array();

        $this->db->flush_cache();

        return $data;
    }

    /**
     * Stock summary goods for external user.
     *
     * @param array $filters
     * @return array
     */
    public function getReportSummaryGoodsExternal($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $reportStock = $this->db->select([
            'doc_invoice.no_document AS invoice_number',
            'booking_inbounds.no_reference',
            'work_order_goods.ex_no_container',
            'ref_goods.no_goods',
            'IFNULL(whey_number, no_goods) AS no_label',
            'ref_goods.name AS description',
            'SUM(work_order_goods.quantity * multiplier_goods) AS quantity',
            'ref_units.unit',
            'SUM(work_order_goods.quantity * multiplier_goods) * ref_goods.unit_weight AS weight',
            'SUM(work_order_goods.quantity * multiplier_goods) * ref_goods.unit_volume AS volume',
            'doc_bl.no_document AS bl_number',
            'MIN(completed_at) AS first_stock_date',
            'MAX(work_order_goods.id) AS id_work_order_goods',
            'work_order_goods.no_pallet',
            'ref_goods.id AS id_goods',
        ])
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
            ->join('(
                SELECT id_upload, no_document FROM upload_documents
                INNER JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                WHERE ref_document_types.document_type = "Invoice" AND upload_documents.is_deleted = FALSE
                ) AS doc_invoice', 'doc_invoice.id_upload = booking_inbounds.id_upload', 'left')
            ->join('(
                SELECT id_upload, upload_documents.no_document FROM upload_documents
                INNER JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                WHERE ref_document_types.document_type = "Bill Of Loading" AND upload_documents.is_deleted = FALSE
                ) AS doc_bl', 'doc_bl.id_upload = booking_inbounds.id_upload', 'left')
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
            ->group_by('doc_invoice.no_document, doc_bl.no_document, ref_people.id, booking_inbounds.id, booking_inbounds.no_reference, booking_inbounds.id_upload, ref_goods.id, ref_units.id, ref_units.unit, ex_no_container');

        if (empty($filters) || !key_exists('data', $filters)) {
            $reportStock->having('quantity > 0');
        } else {
            if (key_exists('data', $filters) && !empty($filters['data'])) {
                if ($filters['data'] == 'stock') {
                    $reportStock->having('quantity > 0');
                }
            }

            if (key_exists('branch_type', $filters) && !is_null($filters['branch_type'])) {
                $reportStock->where('branch_type IS NOT NULL');
                $reportStock->where('branch_type', $filters['branch_type']);
            }

            if (key_exists('booking', $filters) && !empty($filters['booking'])) {
                if (is_array($filters['booking'])) {
                    $reportStock->where_in('booking_inbounds.id', $filters['booking']);
                } else {
                    $reportStock->where('booking_inbounds.id', $filters['booking']);
                }
            }

            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $reportStock->where_in('ref_people.id', $filters['owner']);
                } else {
                    $reportStock->where('ref_people.id', $filters['owner']);
                }
            }

            if (key_exists('age', $filters) && !empty($filters['age']) && $filters['age'] != 'ALL') {
                $age = 'DATEDIFF(CURDATE(), completed_at)';
                switch (urldecode($filters['age'])) {
                    case 'GROWTH':
                        $reportStock->where($age . '>=', 0);
                        $reportStock->where($age . '<=', 365);
                        break;
                    case 'SLOW GROWTH':
                        $reportStock->where($age . '>=', 366);
                        $reportStock->where($age . '<=', 730);
                        break;
                    case 'NO GROWTH':
                        $reportStock->where($age . '>=', 731);
                        break;
                }
            }

            if (key_exists('stock_date', $filters) && !empty($filters['stock_date'])) {
                $reportStock->where('DATE(completed_at) <=', sql_date_format($filters['stock_date'], false));
            }
        }

        if (key_exists('no_pallet', $filters) && !empty($filters['no_pallet'])) {
            $reportStock->where('work_order_goods.no_pallet',$filters['no_pallet']);
        }

        if (!empty($branchId)) {
            $reportStock->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $reportStock->where('ref_people.id', $customerId);
        }

        $baseStockQuery = $this->db->get_compiled_select();

        $report = $this->db->select([
            'goods_stocks.*',
            'work_order_goods.status'
        ])
            ->distinct()
            ->from('work_order_goods')
            ->join("({$baseStockQuery}) AS goods_stocks", 'work_order_goods.id = goods_stocks.id_work_order_goods', 'right');

        $report
            ->group_start()
            ->or_like('goods_stocks.invoice_number', trim($search))
            ->or_like('goods_stocks.no_reference', trim($search))
            ->or_like('goods_stocks.ex_no_container', trim($search))
            ->or_like('goods_stocks.no_goods', trim($search))
            ->or_like('goods_stocks.no_label', trim($search))
            ->or_like('goods_stocks.description', trim($search))
            ->or_like('goods_stocks.quantity', trim($search))
            ->or_like('goods_stocks.unit', trim($search))
            ->or_like('goods_stocks.weight', trim($search))
            ->or_like('goods_stocks.volume', trim($search))
            ->or_like('goods_stocks.bl_number', trim($search))
            ->or_like('goods_stocks.first_stock_date', trim($search))
            ->or_like('work_order_goods.status', trim($search))
            ->group_end();

        // order
        if ($column == 'no') $column = 'invoice_number';
        $report->order_by($column, $sort);

        // get all for export data
        if ($start < 0) {
            return $report->order_by('invoice_number', 'desc')->get()->result_array();
        }

        $finalStockQuery = $this->db->get_compiled_select();

        $reportTotal = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$finalStockQuery}) AS CI_count_all_results")->row_array()['numrows'];
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
     * Stock summary goods simple format.
     *
     * @param $filters
     * @return array
     */
    public function getReportSummaryGoodsSimple($filters = [])
    {
        $reportStock = $this->db->select([
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
              AND ref_document_types.document_type = "Bill Of Loading"
              LIMIT 1) AS no_bl',
            'no_goods',
            'ref_goods.name AS goods_name',
            'unit',
            'ex_no_container',
            'MIN(completed_at) AS inbound_date',
            'SUM(quantity * multiplier_goods) AS stock_quantity',
            'SUM(quantity * multiplier_goods) * ref_goods.unit_weight AS stock_weight',
            'SUM(quantity * multiplier_goods) * ref_goods.unit_gross_weight AS stock_gross_weight',
            'SUM(quantity * multiplier_goods) * ref_goods.unit_volume AS stock_volume',
            'MAX(work_order_goods.id_work_order_goods) AS id_work_order_goods',
        ])
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(bookings.id_booking, bookings.id)')
            ->join('ref_branches', 'ref_branches.id = booking_inbounds.id_branch')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit', 'left')
            ->join('ref_people', 'ref_people.id = work_order_goods.id_owner')
            ->where([
                'bookings.is_deleted' => false,
                'handlings.status !=' => 'REJECTED',
                'handlings.is_deleted' => false,
                'work_orders.is_deleted' => false,
                'work_order_goods.is_deleted' => false,
                'ref_handling_types.is_deleted' => false,
                'ref_people.is_deleted' => false,
                'work_orders.status' => 'COMPLETED'
            ])
            ->group_by('ref_people.id, IFNULL(bookings.id_booking, bookings.id), booking_inbounds.id_upload, booking_inbounds.no_reference, ref_goods.id, ref_units.id, ref_units.unit, ex_no_container')
            ->having('stock_quantity > 0');

        if (!empty($filters)) {
            if (key_exists('stock_date', $filters) && !empty($filters['stock_date'])) {
                $reportStock->where('DATE(completed_at) <=', sql_date_format($filters['stock_date'], false));
            }

            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                $reportStock->where('id_owner', $filters['owner']);
            }

            if (key_exists('branch_type', $filters) && !is_null($filters['branch_type'])) {
                $reportStock->where('branch_type IS NOT NULL');
                $reportStock->where('branch_type', $filters['branch_type']);
            }

            if (key_exists('branch', $filters) && !empty($filters['branch'])) {
                $branchId = $filters['branch'];
            } else {
                if (!is_cli()) {
                    $branchId = get_active_branch('id');
                }
            }
            if (!empty($branchId)) {
                $reportStock->where('bookings.id_branch', $branchId);
            }
        }

        $baseStockQuery = $this->db->get_compiled_select();

        $report = $this->db->select([
            'goods_stocks.id_owner',
            'goods_stocks.owner_name',
            'goods_stocks.no_invoice',
            'goods_stocks.no_bl',
            'goods_stocks.no_reference',
            'goods_stocks.no_goods',
            'goods_stocks.goods_name',
            'goods_stocks.unit',
            'goods_stocks.inbound_date',
            'goods_stocks.stock_quantity',
            'goods_stocks.stock_weight',
            'goods_stocks.stock_gross_weight',
            'goods_stocks.stock_volume',
            'goods_stocks.ex_no_container',
            'work_order_goods.no_pallet',
        ])->distinct()
            ->from('work_order_goods')
            ->join("({$baseStockQuery}) AS goods_stocks", 'work_order_goods.id = goods_stocks.id_work_order_goods', 'right');

        return $report->get()->result_array();
    }

     /**
     * Get report complain.
     *
     * @param array $filters
     * @return array
     */
    public function getReportComplain($filters = [])
    {
        $branchId = get_active_branch('id');
        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $report = $this->db->select([
            'complains.no_complain',
            'complains.complain_date',
            'ref_people.name AS customer',
            'complains.department',
            'ref_complain_categories.category',
            'ref_complain_categories.value_type',
            'complains.complain',
            'complains.investigation_result',
            'complains.conclusion',
            'complains.close_date',
            'complains.ftkp'
        ])
            ->from('complains')
            ->join('ref_complain_categories', 'ref_complain_categories.id = complains.id_complain_category', 'left')
            ->join('ref_people', 'ref_people.id = complains.id_customer', 'left')
            ->group_by('complains.id');

        if (!empty($filters)) {
             if (key_exists('customer', $filters) && !empty($filters['customer'])) {
                if (is_array($filters['customer'])) {
                    $report->where_in('ref_people.id', $filters['customer']);
                } else {
                    $report->where('ref_people.id', $filters['customer']);
                }
            }

            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
                if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                    $report->where('DATE(' . $filters['date_type'] . ')>=', sql_date_format($filters['date_from'], false));
                }
                if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                    $report->where('DATE(' . $filters['date_type'] . ')<=', sql_date_format($filters['date_to'], false));
                }
            }

            // if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            //     $report->where('ref_branch.id', $filters['branch']);
            // }
        }


        $complain = $report->get()->result_array();

        return $complain;
    }

    /**
     * Get report stock status goods.
     *
     * @param array $filters
     * @return array
     */
    public function getReportStatusGoods($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branch = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $columnOrder = [
            0 => "goods_stocks.no_packing_list",
            1 => "goods_stocks.no_packing_list",
            2 => "goods_stocks.ex_no_container",
            3 => "goods_stocks.no_goods",
            4 => "goods_stocks.whey_number",
            5 => "goods_stocks.goods_name",
            6 => "goods_stocks.stock_quantity",
            7 => "goods_stocks.unit",
            8 => "goods_stocks.no_reference",
            9 => "goods_stocks.payment_status",
            10 => "goods_stocks.bcf_status",
            11 => "goods_stocks.no_packing_list",
        ];
        $columnSort = $columnOrder[$column];

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $report = $this->db->select([
            'work_order_goods.id_owner',
            'ref_people.name AS owner_name',
            'IFNULL(bookings.id_booking, bookings.id) AS id_booking',
            'booking_inbounds.no_reference',
            'booking_inbounds.payment_status',
            'booking_inbounds.bcf_status',
            '(SELECT upload_documents.no_document FROM upload_documents 
              LEFT JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
              WHERE upload_documents.id_upload = booking_inbounds.id_upload
              AND ref_document_types.document_type = "Packing List"
              LIMIT 1) AS no_packing_list',
            'ref_goods.no_goods',
            'IFNULL(whey_number, no_goods) AS whey_number',
            'ref_goods.name AS goods_name',
            'ref_units.unit',
            'work_order_goods.ex_no_container',
            'MIN(completed_at) AS inbound_date',
            'SUM(quantity * multiplier_goods) AS stock_quantity',
            'MAX(work_order_goods.id) AS id_work_order_goods',
        ])
            ->from('work_order_goods')
            ->join('ref_people', 'ref_people.id = work_order_goods.id_owner')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit', 'left')
            ->join('ref_positions', 'ref_positions.id = work_order_goods.id_position', 'left')
            ->join('work_orders', 'work_orders.id = work_order_goods.id_work_order')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(bookings.id_booking, bookings.id)')
            ->where('work_orders.status', 'COMPLETED')
            ->group_by('id_owner, IFNULL(bookings.id_booking, bookings.id), booking_inbounds.no_reference, booking_inbounds.payment_status, booking_inbounds.bcf_status, booking_inbounds.id_upload, id_goods, id_unit, ex_no_container');

        if (empty($filters)) {
            $report->having('stock_quantity > 0');
        } else {
            if (key_exists('data', $filters) && !empty($filters['data'])) {
                if ($filters['data'] == 'stock') {
                    $report->having('stock_quantity > 0');
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

        if (!empty($branch)) {
            $report->where('bookings.id_branch', $branch);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('id_owner', $customerId);
        }

        $baseStockQuery = $this->db->get_compiled_select();

        $report = $this->db->select([
            'goods_stocks.no_packing_list',
            'goods_stocks.ex_no_container',
            'goods_stocks.no_goods',
            'goods_stocks.whey_number',
            'goods_stocks.goods_name',
            'goods_stocks.stock_quantity',
            'goods_stocks.unit',
            'goods_stocks.no_reference',
            'goods_stocks.payment_status',
            'goods_stocks.bcf_status',
            '"" AS remark',
        ])
            ->distinct()
            ->from('work_order_goods')
            ->join("({$baseStockQuery}) AS goods_stocks", 'work_order_goods.id = goods_stocks.id_work_order_goods', 'right');

        $report->group_start();
        foreach ($columnOrder as $column) {
            $report->or_like($column, trim($search));
        }
        $report->group_end();
        $report->order_by($columnSort, $sort);

        if ($start < 0) {
            return $report->get()->result_array();
        }

        $finalStockQuery = $this->db->get_compiled_select();
        $reportTotal = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$finalStockQuery}) AS CI_count_all_results")->row_array()['numrows'];
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
     * Get stock mutation container.
     *
     * @param null $filters
     * @param bool $raw
     * @return array
     */
    public function getReportMutationContainer($filters = null, $raw = false)
    {
        $branchId = get_active_branch('id');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $report = $this->db
            ->select([
                'bookings.no_booking',
                'bookings.no_reference',
                'ref_people.name AS owner_name',
                'ref_handling_types.handling_type',
                'work_orders.id As id_work_order',
                'work_orders.completed_at',
                'work_order_containers.id AS id_work_order_container',
                'ref_containers.no_container',
                'ref_containers.type AS container_type',
                'ref_containers.size AS container_size',
                'work_order_containers.seal',
                'ref_positions.position',
                'work_order_containers.is_empty',
                'multiplier_container',
                '(multiplier_container * CAST(quantity AS SIGNED)) AS quantity',
                'IF(multiplier_container = 1, (multiplier_container * CAST(quantity AS SIGNED)), 0) AS quantity_debit',
                'IF(multiplier_container = -1, (multiplier_container * CAST(quantity AS SIGNED)), 0) AS quantity_credit',
            ])
            ->from('work_order_containers')
            ->join('ref_people', 'ref_people.id = work_order_containers.id_owner')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container')
            ->join('ref_positions', 'ref_positions.id = work_order_containers.id_position', 'left')
            ->join('work_orders', 'work_orders.id = work_order_containers.id_work_order')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
            ->where('work_orders.status', 'COMPLETED')
            ->order_by('no_container')
            ->order_by('completed_at');

        if (!empty($filters)) {
            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $report->where_in('id_owner', $filters['owner']);
                } else {
                    $report->where('id_owner', $filters['owner']);
                }
            }

            if (key_exists('bookings', $filters) && !empty($filters['bookings'])) {
                $report->where('(IFNULL(bookings.id_booking, bookings.id)) = ', $filters['bookings']);
            }

            if (key_exists('branch_type', $filters) && !is_null($filters['branch_type'])) {
                $report->where('branch_type IS NOT NULL');
                $report->where('branch_type', $filters['branch_type']);
            }

            if (key_exists('bookings', $filters) && !empty($filters['bookings'])) {
                $report->where('(IFNULL(bookings.id_booking, bookings.id)) = ', $filters['bookings']);
            }

            if (key_exists('container', $filters) && !empty($filters['container'])) {
                if (is_array($filters['container'])) {
                    $report->where_in('id_container', $filters['container']);
                } else {
                    $report->where('id_container', $filters['container']);
                }
            }

            if (key_exists('handling_type', $filters) && !empty($filters['handling_type'])) {
                if (is_array($filters['handling_type'])) {
                    $report->where_in('id_handling_type', $filters['handling_type']);
                } else {
                    $report->where('id_handling_type', $filters['handling_type']);
                }
            }

            if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                $report->where('completed_at >=', sql_date_format($filters['date_from']));
            }

            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $report->where('completed_at <=', sql_date_format($filters['date_to']));
            }
        }

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('id_owner', $customerId);
        }

        $mutations = $report->get()->result_array();

        if ($raw) {
            return $mutations;
        }

        $reportCards = [];

        $lastContainer = '';
        foreach ($mutations as $mutation) {
            if ($lastContainer != $mutation['no_container']) {
                $lastContainer = $mutation['no_container'];
            }
            $reportCards[$lastContainer][] = $mutation;
        }

        return $reportCards;
    }

    /**
     * Get stock mutation goods.
     *
     * @param null $filters
     * @param bool $raw
     * @return array
     */
    public function getReportMutationGoods($filters = null, $raw = false)
    {
        $branchId = get_active_branch('id');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $report = $this->db
            ->select([
                'bookings.no_booking',
                'bookings.no_reference',
                'ref_people.name AS owner_name',
                'ref_handling_types.handling_type',
                'work_orders.id As id_work_order',
                'work_orders.completed_at',
                'work_order_goods.id AS id_work_order_goods',
                'ref_goods.id AS id_goods',
                'ref_goods.no_goods',
                'ref_goods.name AS goods_name',
                'ref_units.unit',
                'ref_positions.position',
                'work_order_goods.no_pallet',
                'ex_no_container',
                'multiplier_goods',
                '(multiplier_goods * quantity) AS quantity',
                'IF(multiplier_goods = 1, (multiplier_goods * quantity), 0) AS quantity_debit',
                'IF(multiplier_goods = -1, (multiplier_goods * quantity), 0) AS quantity_credit',
                '(multiplier_goods * quantity) * ref_goods.unit_weight AS total_weight',
                'IF(multiplier_goods = 1, (multiplier_goods * quantity), 0) * ref_goods.unit_weight AS weight_debit',
                'IF(multiplier_goods = -1, (multiplier_goods * quantity), 0) * ref_goods.unit_weight AS weight_credit',
                '(multiplier_goods * quantity) * ref_goods.unit_gross_weight AS total_gross_weight',
                'IF(multiplier_goods = 1, (multiplier_goods * quantity), 0) * ref_goods.unit_gross_weight AS gross_weight_debit',
                'IF(multiplier_goods = -1, (multiplier_goods * quantity), 0) * ref_goods.unit_gross_weight AS gross_weight_credit',
                '(multiplier_goods * quantity) * ref_goods.unit_volume AS total_volume',
                'IF(multiplier_goods = 1, (multiplier_goods * quantity), 0) * ref_goods.unit_volume AS volume_debit',
                'IF(multiplier_goods = -1, (multiplier_goods * quantity), 0) * ref_goods.unit_volume AS volume_credit',
            ])
            ->from('work_order_goods')
            ->join('ref_people', 'ref_people.id = work_order_goods.id_owner')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit', 'left')
            ->join('ref_positions', 'ref_positions.id = work_order_goods.id_position', 'left')
            ->join('work_orders', 'work_orders.id = work_order_goods.id_work_order')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->where('work_orders.status', 'COMPLETED')
            ->order_by('no_goods')
            ->order_by('completed_at');

        if (!empty($filters)) {
            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $report->where_in('id_owner', $filters['owner']);
                } else {
                    $report->where('id_owner', $filters['owner']);
                }
            }

            if (key_exists('bookings', $filters) && !empty($filters['bookings'])) {
                $report->where('(IFNULL(bookings.id_booking, bookings.id)) = ', $filters['bookings']);
            }

            if (key_exists('branch_type', $filters) && !is_null($filters['branch_type'])) {
                $report->where('branch_type IS NOT NULL');
                $report->where('branch_type', $filters['branch_type']);
            }

            if (key_exists('goods', $filters) && !empty($filters['goods'])) {
                if (is_array($filters['goods'])) {
                    $report->where_in('id_goods', $filters['goods']);
                } else {
                    $report->where('id_goods', $filters['goods']);
                }
            }

            if (key_exists('handling_type', $filters) && !empty($filters['handling_type'])) {
                if (is_array($filters['handling_type'])) {
                    $report->where_in('id_handling_type', $filters['handling_type']);
                } else {
                    $report->where('id_handling_type', $filters['handling_type']);
                }
            }

            if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                $report->where('DATE(completed_at) >=', format_date($filters['date_from']));
            }

            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $report->where('DATE(completed_at) <=', format_date($filters['date_to']));
            }
        }

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('id_owner', $customerId);
        }

        $mutations = $report->get()->result_array();

        if ($raw) {
            return $mutations;
        }

        $reportCards = [];

        $lastGoods = '';
        foreach ($mutations as $mutation) {
            if ($lastGoods != $mutation['id_goods']) {
                $lastGoods = $mutation['id_goods'];
            }
            $reportCards[$lastGoods][] = $mutation;
        }

        return $reportCards;
    }

    /**
     * Get report aging container.
     *
     * @param array $filters
     * @return array
     */
    public function getReportAgingContainer($filters = [])
    {
        $branch = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $branchCondition = '';
        if (!empty($branch)) {
            $branchCondition = 'AND id_branch = ' . $branch;
        }

        $branchTypeCondition = '';
        if (key_exists('branch_type', $filters) && !is_null($filters['branch_type'])) {
            $branchTypeCondition = 'AND branch_type = "' . $filters['branch_type'] . '"';
        }

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $ownerCondition = '';
        if ($userType == 'EXTERNAL') {
            $ownerCondition = 'AND id_owner = ' . $customerId;
        }

        if (!empty($filters)) {
            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $ownerIds = implode(',', $filters['owner']);
                    $ownerCondition = 'AND id_owner IN(' . $ownerIds . ')';
                } else {
                    $ownerCondition = 'AND id_owner = ' . $filters['owner'];
                }
            }
        }

        $report = $this->db->select([
            'container_size',
            'SUM(IF(DATEDIFF(CURDATE(), transaction_date) = 0, quantity, 0)) AS age_today',
            'SUM(IF(DATEDIFF(CURDATE(), transaction_date) BETWEEN 1 AND 30, quantity, 0)) AS age_1_30',
            'SUM(IF(DATEDIFF(CURDATE(), transaction_date) BETWEEN 31 AND 60, quantity, 0)) AS age_31_60',
            'SUM(IF(DATEDIFF(CURDATE(), transaction_date) BETWEEN 61 AND 90, quantity, 0)) AS age_61_90',
            'SUM(IF(DATEDIFF(CURDATE(), transaction_date) > 90, quantity, 0)) AS age_more_than_90'
        ])
            ->from('
              (SELECT 
                ref_containers.size AS container_size,
                SUM(multiplier_container * CAST(quantity AS SIGNED)) AS quantity,
                MIN(completed_at) AS transaction_date
              FROM work_order_containers
              INNER JOIN ref_containers ON ref_containers.id = work_order_containers.id_container
              INNER JOIN work_orders ON work_orders.id = work_order_containers.id_work_order
              INNER JOIN handlings ON handlings.id = work_orders.id_handling
              INNER JOIN bookings ON bookings.id = handlings.id_booking
              INNER JOIN ref_branches ON ref_branches.id = bookings.id_branch
              INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
              WHERE id_container IS NOT NULL ' . $branchCondition . ' ' . $branchTypeCondition . ' ' . $ownerCondition . ' 
              GROUP BY ref_containers.size) AS stocks
            ')
            ->where('stocks.quantity > 0')
            ->group_by('container_size');

        if (!empty($filters)) {
            $dayFilterFrom = key_exists('day_from', $filters) && !empty($filters['day_from']);
            $dayFilterTo = key_exists('day_to', $filters) && !empty($filters['day_to']);
            if ($dayFilterFrom && $dayFilterTo) {
                $report->select('SUM(IF(DATEDIFF(CURDATE(), transaction_date) BETWEEN "' . $filters['day_from'] . '" AND "' . $filters['day_to'] . '", quantity, 0)) AS age_filter');
            } else {
                if ($dayFilterFrom) {
                    $report->select('SUM(IF(DATEDIFF(CURDATE(), transaction_date) >= "' . $filters['day_from'] . '", quantity, 0)) AS age_filter');
                } else if ($dayFilterTo) {
                    $report->select('SUM(IF(DATEDIFF(CURDATE(), transaction_date) <= "' . $filters['day_to'] . '", quantity, 0)) AS age_filter');
                }
            }
        }

        return $report->get()->result_array();
    }

    /**
     * Get report aging goods
     * @param array $filters
     * @return array
     */
    public function getReportAgingGoods($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branch = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $branchCondition = '';
        if (!empty($branch)) {
            $branchCondition = 'AND bookings.id_branch = ' . $branch;
        }

        $branchTypeCondition = '';
        if (key_exists('branch_type', $filters) && !is_null($filters['branch_type'])) {
            $branchTypeCondition = 'AND branch_type = "' . $filters['branch_type'] . '"';
        }

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $ownerCondition = '';
        if ($userType == 'EXTERNAL') {
            $ownerCondition = 'AND id_owner = ' . $customerId;
        }

        $this->db->start_cache();

        $report = $this->db->select([
            'owner_name',
            'id_goods',
            'no_goods',
            'goods_name',
            'unit',
            'SUM(IF(DATEDIFF(CURDATE(), transaction_date) = 0, quantity, 0)) AS age_today',
            'SUM(IF(DATEDIFF(CURDATE(), transaction_date) BETWEEN 1 AND 30, quantity, 0)) AS age_1_30',
            'SUM(IF(DATEDIFF(CURDATE(), transaction_date) BETWEEN 31 AND 60, quantity, 0)) AS age_31_60',
            'SUM(IF(DATEDIFF(CURDATE(), transaction_date) BETWEEN 61 AND 90, quantity, 0)) AS age_61_90',
            'SUM(IF(DATEDIFF(CURDATE(), transaction_date) > 90, quantity, 0)) AS age_more_than_90'
        ])
            ->from('
              (SELECT 
                id_owner,
                ref_people.name AS owner_name,
                id_goods,
                no_goods,
                ref_goods.name AS goods_name,
                unit,
                branch_type,
                SUM(multiplier_goods * quantity) AS quantity,
                SUM(multiplier_goods * quantity) * ref_goods.unit_weight AS total_weight,
                SUM(multiplier_goods * quantity) * ref_goods.unit_gross_weight AS total_gross_weight,
                SUM(multiplier_goods * quantity) * ref_goods.unit_volume AS total_volume,
                MIN(completed_at) AS transaction_date
              FROM work_order_goods
              INNER JOIN ref_people ON ref_people.id = work_order_goods.id_owner
              INNER JOIN ref_goods ON ref_goods.id = work_order_goods.id_goods
              INNER JOIN ref_units ON ref_units.id = work_order_goods.id_unit
              INNER JOIN work_orders ON work_orders.id = work_order_goods.id_work_order
              INNER JOIN handlings ON handlings.id = work_orders.id_handling
              INNER JOIN bookings ON bookings.id = handlings.id_booking
              INNER JOIN ref_branches ON ref_branches.id = bookings.id_branch
              INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
              WHERE id_goods IS NOT NULL ' . $branchCondition . ' ' . $branchTypeCondition . ' ' . $ownerCondition . '
              GROUP BY id_owner, id_goods, id_unit) AS stocks
            ')
            ->group_by('owner_name, id_goods, no_goods, goods_name, unit');

        if (!empty($filters)) {
            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $report->where_in('id_owner', $filters['owner']);
                } else {
                    $report->where('id_owner', $filters['owner']);
                }
            }

            $dayFilterFrom = key_exists('day_from', $filters) && !empty($filters['day_from']);
            $dayFilterTo = key_exists('day_to', $filters) && !empty($filters['day_to']);
            if ($dayFilterFrom && $dayFilterTo) {
                $report->select('SUM(IF(DATEDIFF(CURDATE(), transaction_date) BETWEEN "' . $filters['day_from'] . '" AND "' . $filters['day_to'] . '", quantity, 0)) AS age_filter');
            } else {
                if ($dayFilterFrom) {
                    $report->select('SUM(IF(DATEDIFF(CURDATE(), transaction_date) >= "' . $filters['day_from'] . '", quantity, 0)) AS age_filter');
                } else if ($dayFilterTo) {
                    $report->select('SUM(IF(DATEDIFF(CURDATE(), transaction_date) <= "' . $filters['day_to'] . '", quantity, 0)) AS age_filter');
                }
                $report->select("'-' AS age_filter");
            }
        } else {
            $report->select("'-' AS age_filter");
        }

        $report
            ->group_start()
            ->like('owner_name', $search)
            ->or_like('no_goods', $search)
            ->or_like('goods_name', $search)
            ->or_like('unit', $search)
            ->or_like('quantity', $search)
            ->group_end();

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $report->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $reportTotal = $this->db->count_all_results();
        if($column == 'no') $column = 'goods_name';
        $page = $report->order_by($column, $sort)->limit($length, $start);
        $reportData = $page->get()->result_array();

        foreach ($reportData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($reportData),
            "recordsFiltered" => $reportTotal,
            "data" => $reportData
        ];
        $this->db->flush_cache();

        return $pageData;
    }

    /**
     * Fetch report booking summary.
     *
     * @param array $filters
     * @return mixed
     */
    public function getReportBookingSummary($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));

        $handlingTypeIdInbound = get_setting('default_inbound_handling');
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $this->db->start_cache();

        $reports = $this->db->from("(
            SELECT 
              bookings.id, 
              bookings.id_branch, 
              ref_people.name AS owner_name,
              bookings.no_reference AS no_reference_inbound, 
              bookings.booking_date AS booking_date_inbound, 
              booking_container_inbounds.total_booking_container_inbound, 
              booking_goods_inbounds.total_booking_goods_inbound,
              inbound_dates.first_date_inbound,
              inbound_dates.last_date_inbound,
              container_inbounds.total_container_inbound,
              goods_inbounds.total_goods_inbound,
              
              GROUP_CONCAT(DISTINCT booking_outbounds.no_reference) AS no_reference_outbound,
              GROUP_CONCAT(DISTINCT booking_outbounds.booking_date) AS booking_date_outbound,
              SUM(booking_container_outbounds.total_booking_container_outbound) AS total_booking_container_outbound, 
              SUM(booking_goods_outbounds.total_booking_goods_outbound) AS total_booking_goods_outbound,
              MIN(outbound_dates.first_date_outbound) AS first_date_outbound,
              MAX(outbound_dates.last_date_outbound) AS last_date_outbound,
              SUM(container_outbounds.total_container_outbound) AS total_container_outbound,
              SUM(goods_outbounds.total_goods_outbound) AS total_goods_outbound
              
            FROM (
                SELECT bookings.* FROM bookings 
                LEFT JOIN ref_booking_types ON bookings.id_booking_type = ref_booking_types.id
                WHERE ref_booking_types.category = 'INBOUND'
            ) AS bookings
            LEFT JOIN ref_people ON ref_people.id = bookings.id_customer
            LEFT JOIN (
              SELECT id_booking, SUM(quantity) AS total_booking_container_inbound
              FROM booking_containers 
              GROUP BY id_booking
              ) AS booking_container_inbounds ON bookings.id = booking_container_inbounds.id_booking
            LEFT JOIN (
              SELECT id_booking, SUM(quantity) AS total_booking_goods_inbound
              FROM booking_goods 
              GROUP BY id_booking
              ) AS booking_goods_inbounds ON bookings.id = booking_goods_inbounds.id_booking
            LEFT JOIN (
              SELECT id_booking, MIN(completed_at) AS first_date_inbound, MAX(completed_at) AS last_date_inbound
              FROM handlings
              INNER JOIN work_orders ON work_orders.id_handling = handlings.id
              WHERE id_handling_type = '{$handlingTypeIdInbound}'
              GROUP BY id_booking
            ) AS inbound_dates ON bookings.id = inbound_dates.id_booking
            LEFT JOIN (
              SELECT id_booking, SUM(quantity) AS total_container_inbound
              FROM handlings
              INNER JOIN work_orders ON work_orders.id_handling = handlings.id
              INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
              WHERE id_handling_type = '{$handlingTypeIdInbound}'
                AND work_orders.is_deleted = false
                AND handlings.is_deleted = false
              GROUP BY id_booking
            ) AS container_inbounds ON bookings.id = container_inbounds.id_booking
            LEFT JOIN (
              SELECT id_booking, SUM(quantity) AS total_goods_inbound
              FROM handlings
              INNER JOIN work_orders ON work_orders.id_handling = handlings.id
              INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
              WHERE id_handling_type = '{$handlingTypeIdInbound}'
                AND work_orders.is_deleted = false
                AND handlings.is_deleted = false
              GROUP BY id_booking
            ) AS goods_inbounds ON bookings.id = goods_inbounds.id_booking
            
            LEFT JOIN bookings AS booking_outbounds
                ON bookings.id = booking_outbounds.id_booking
            LEFT JOIN (
              SELECT id_booking, SUM(quantity) AS total_booking_container_outbound
              FROM booking_containers 
              GROUP BY id_booking
              ) AS booking_container_outbounds ON booking_outbounds.id = booking_container_outbounds.id_booking
            LEFT JOIN (
              SELECT id_booking, SUM(quantity) AS total_booking_goods_outbound
              FROM booking_goods 
              GROUP BY id_booking
              ) AS booking_goods_outbounds ON booking_outbounds.id = booking_goods_outbounds.id_booking
            LEFT JOIN (
              SELECT id_booking, MIN(completed_at) AS first_date_outbound, MAX(completed_at) AS last_date_outbound
              FROM handlings
              INNER JOIN work_orders ON work_orders.id_handling = handlings.id
              WHERE id_handling_type = '{$handlingTypeIdOutbound}'
              GROUP BY id_booking
            ) AS outbound_dates ON booking_outbounds.id = outbound_dates.id_booking
            LEFT JOIN (
              SELECT id_booking, SUM(quantity) AS total_container_outbound
              FROM handlings
              INNER JOIN work_orders ON work_orders.id_handling = handlings.id
              INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
              WHERE id_handling_type = '{$handlingTypeIdOutbound}'
                AND work_orders.is_deleted = false
                AND handlings.is_deleted = false
              GROUP BY id_booking
            ) AS container_outbounds ON booking_outbounds.id = container_outbounds.id_booking
            LEFT JOIN (
              SELECT id_booking, SUM(quantity) AS total_goods_outbound
              FROM handlings
              INNER JOIN work_orders ON work_orders.id_handling = handlings.id
              INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
              WHERE id_handling_type = '{$handlingTypeIdOutbound}'
                AND work_orders.is_deleted = false
                AND handlings.is_deleted = false
              GROUP BY id_booking
            ) AS goods_outbounds ON booking_outbounds.id = goods_outbounds.id_booking
            GROUP BY bookings.id) AS bookings")
            ->join('(
                SELECT 
                    IFNULL(bookings.id_booking, bookings.id) AS id_booking,
                    SUM(CAST(quantity AS SIGNED) * multiplier_container) AS stock_container
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                WHERE work_orders.is_deleted = false
                    AND handlings.is_deleted = false
                GROUP BY IFNULL(bookings.id_booking, bookings.id)
            ) AS stock_containers', 'stock_containers.id_booking=bookings.id', 'left')
            ->join('(
                SELECT 
                    IFNULL(bookings.id_booking, bookings.id) AS id_booking,
                    SUM(quantity * multiplier_goods) AS stock_goods
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE work_orders.is_deleted = false
                    AND handlings.is_deleted = false
                GROUP BY IFNULL(bookings.id_booking, bookings.id)
            ) AS stock_goods', 'stock_goods.id_booking=bookings.id', 'left');

        if (!empty($branchId)) {
            $reports->where('bookings.id_branch', $branchId);
        }

        if (!empty($filters)) {
            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $reports->where_in('owner_name', $filters['owner']);
                } else {
                    $reports->where('owner_name', $filters['owner']);
                }
            }

            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
                if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                    $reports->where($filters['date_type'] . '>=', sql_date_format($filters['date_from']));
                }
                if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                    $reports->where($filters['date_type'] . '<=', sql_date_format($filters['date_to']));
                }
            }
        }

        $reports
            ->group_start()
            ->like('owner_name', $search)
            ->or_like('no_reference_inbound', $search)
            ->or_like('booking_date_inbound', $search)
            ->or_like('total_booking_container_inbound', $search)
            ->or_like('total_booking_goods_inbound', $search)
            ->or_like('first_date_inbound', $search)
            ->or_like('last_date_inbound', $search)
            ->or_like('total_container_inbound', $search)
            ->or_like('total_goods_inbound', $search)
            ->or_like('no_reference_outbound', $search)
            ->or_like('booking_date_outbound', $search)
            ->or_like('total_booking_container_outbound', $search)
            ->or_like('total_booking_goods_outbound', $search)
            ->or_like('first_date_outbound', $search)
            ->or_like('last_date_outbound', $search)
            ->or_like('total_container_outbound', $search)
            ->or_like('total_goods_outbound', $search)
            ->group_end();

        $this->db->stop_cache();

        if ($start < 0) {
            $data = $reports->get()->result_array();
            $this->db->flush_cache();
            return $data;
        }

        $total = $this->db->count_all_results();
        if($column == 'no') $column = 'bookings.id';
        $page = $reports->order_by($column, $sort)->limit($length, $start);
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
     * Get container stock move.
     *
     * @param array $filters
     * @return array
     */
    public function getContainerStockMove($filters = [])
    {
        $bookingId = get_if_exist($filters, 'booking');
        $multiplier = get_if_exist($filters, 'multiplier');
        $date_from = get_if_exist($filters, 'date_from');
        $date_to = get_if_exist($filters, 'date_to');
        $container = get_if_exist($filters, 'containers');
        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));

        $containers = $this->db
            ->select([
                'bookings.id AS id_booking',
                'bookings.id_branch',
                'bookings.no_booking',
                'bookings.no_reference',
                'IFNULL(bookings.id_booking, bookings.id) AS id_booking_in',
                'work_orders.id AS id_work_order',
                'work_orders.no_work_order',
                'ref_handling_types.handling_type',
                'ref_containers.id AS id_container',
                'ref_containers.no_container',
                'ref_containers.size',
                'ref_containers.type',
                'ref_positions.position',
                'work_order_containers.seal',
                'work_order_containers.status_danger',
                'work_orders.completed_at',
            ])
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container')
            ->join('ref_positions', 'ref_positions.id = work_order_containers.id_position', 'left')
            ->where([
                'bookings.is_deleted' => false,
                'ref_handling_types.multiplier_container' => $multiplier,
                'handlings.status' => 'APPROVED',
                'handlings.is_deleted' => false,
                'work_orders.status' => 'COMPLETED',
                'work_orders.is_deleted' => false,
                'work_order_containers.is_deleted' => false,
            ]);

        if (!empty($branchId)) {
            $containers->where('bookings.id_branch', $branchId);
        }

        if (!empty($container)) {
            $containers->where_in('ref_containers.id', $container);
        }

        if (!empty($bookingId)) {
            $containers->where('handlings.id_booking', $bookingId);
        }

        if (!empty($date_from) && !empty($date_to)) {
            $containers->where('DATE(completed_at) >=', $date_from)->where('DATE(completed_at) <=', $date_to);
        }

        return $containers->get()->result_array();
    }

    /**
     * Get container stock move.
     *
     * @param $filters
     * @return array
     */
    public function getGoodsStockMove($filters = [])
    {
        $bookingId = get_if_exist($filters, 'booking');
        $date_from = get_if_exist($filters, 'date_from');
        $date_to = get_if_exist($filters, 'date_to');
        $item = get_if_exist($filters, 'items');
        $multiplier = get_if_exist($filters, 'multiplier');
        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));

        $goods = $this->db
            ->select([
                'bookings.id AS id_booking',
                'bookings.id_branch',
                'bookings.no_booking',
                'bookings.no_reference',
                'IFNULL(bookings.id_booking, bookings.id) AS id_booking_in',
                'work_orders.id AS id_work_order',
                'work_orders.no_work_order',
                'ref_handling_types.handling_type',
                'ref_goods.id AS id_goods',
                'ref_goods.no_goods',
                'ref_goods.name AS goods_name',
                'ref_positions.position',
                'ref_units.id AS id_unit',
                'ref_units.unit',
                'work_order_goods.quantity',
                'ref_goods.unit_weight',
                '(ref_goods.unit_weight * work_order_goods.quantity) AS total_weight',
                'ref_goods.unit_gross_weight',
                '(ref_goods.unit_gross_weight * work_order_goods.quantity) AS total_gross_weight',
                'ref_goods.unit_volume',
                '(ref_goods.unit_volume * work_order_goods.quantity) AS total_volume',
                'work_order_goods.status_danger',
                'work_order_goods.ex_no_container',
                'work_orders.completed_at',
            ])
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods')
            ->join('ref_positions', 'ref_positions.id = work_order_goods.id_position', 'left')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit', 'left')
            ->where([
                'bookings.is_deleted' => false,
                'ref_handling_types.multiplier_goods' => $multiplier,
                'handlings.status' => 'APPROVED',
                'handlings.is_deleted' => false,
                'work_orders.status' => 'COMPLETED',
                'work_orders.is_deleted' => false,
                'work_order_goods.is_deleted' => false,
            ]);

        if (!empty($branchId)) {
            $goods->where('bookings.id_branch', $branchId);
        }

        if (!empty($item)) {
            $goods->where_in('ref_goods.id', $item);
        }

        if (!empty($bookingId)) {
            $goods->where('handlings.id_booking', $bookingId);
        }

        if (!empty($date_from) && !empty($date_to)) {
            $goods->where('DATE(completed_at) >=', $date_from)->where('DATE(completed_at) <=', $date_to);
        }

        return $goods->get()->result_array();
    }

    /**
     * Get booking list that still available in stock.
     * Fetch all and sum activity container and goods, check branch, owner, and type of item
     * if necessary then put them in altogether.
     * @param string $type
     * @param null $filters
     * @return mixed
     */
    public function getAvailableStockBookingList($type = 'all', $filters = null)
    {
        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $branchId = get_active_branch('id');
        $branchCondition = '';
        if (!empty($branchId)) {
            $branchCondition = 'AND bookings.id_branch = ' . $branchId;
        }

        // cache available stock booking
        $this->load->driver('cache', ['adapter' => 'file']);
        $cacheKey = 'available-stock-booking-list-' . md5(json_encode([$userType, $customerId, $type, if_empty($filters, 'no-filter')]));
        $cachedStockList = $this->cache->get($cacheKey);
        if ($cachedStockList !== false) {
            return $cachedStockList;
        }

        $ownerCondition = '';
        if ($userType == 'EXTERNAL') {
            $ownerCondition = 'AND id_owner = ' . $customerId;
        }

        $containerCondition = 'AND TRUE';
        $goodsCondition = 'AND TRUE';
        if ($type == 'container') {
            $goodsCondition = 'AND FALSE';
        } else if ($type == 'goods') {
            $containerCondition = 'AND FALSE';
        }

        // we need include whether there is transaction (eg. payment feature proceed before booking)
        $transactionExistCondition = '';
        $limitAllowEmptyStock = '';
        if (!empty($filters)) {
            if (key_exists('transaction_exist', $filters)) {
                if ($filters['transaction_exist'] == false) {
                    $transactionExistCondition = 'OR total_transaction <= 0';
                }
            }

            if (key_exists('allow_empty_stock', $filters) && key_exists('allow_empty_limit', $filters) && $filters['allow_empty_stock']) {
                $limitAllowEmptyStock = 'OR last_transaction >= "' . $filters['allow_empty_limit'] . '"';
            }
        }

        $bookings = $this->db->query("
            SELECT DISTINCT id_owner, owner_name, stocks.id_booking, bookings.no_booking, bookings.no_reference, ref_people.name AS customer_name,
              SUM(stock) AS stock, SUM(total_transaction) AS total_transaction, MAX(last_transaction) AS last_transaction  
            FROM (
                SELECT bookings.id_customer AS id_owner, ref_people.name AS owner_name, IFNULL(bookings.id_booking, bookings.id) AS id_booking,
                  SUM(CAST(IFNULL(quantity, 0) AS SIGNED) * multiplier_container) AS stock,
                  SUM(IFNULL(quantity, 0)) AS total_transaction, MAX(work_orders.completed_at) last_transaction
                FROM bookings
                LEFT JOIN handlings ON handlings.id_booking = bookings.id
                LEFT JOIN (SELECT * FROM work_orders WHERE status = 'COMPLETED' AND is_deleted = false) AS work_orders ON work_orders.id_handling = handlings.id
                LEFT JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                LEFT JOIN ref_people ON ref_people.id = bookings.id_customer
                LEFT JOIN ref_branches ON ref_branches.id = bookings.id_branch
                LEFT JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                WHERE bookings.is_deleted = false {$containerCondition} {$branchCondition} {$ownerCondition}
                GROUP BY bookings.id_customer, owner_name, branch_type, IFNULL(bookings.id_booking, bookings.id)
                
                UNION
                
                SELECT bookings.id_customer AS id_owner, ref_people.name AS owner_name, IFNULL(bookings.id_booking, bookings.id) AS id_booking,
                  SUM(CAST(IFNULL(quantity, 0) AS SIGNED) * multiplier_goods) AS stock,
                  SUM(IFNULL(quantity, 0)) AS total_transaction, MAX(work_orders.completed_at) last_transaction 
                FROM bookings
                LEFT JOIN handlings ON handlings.id_booking = bookings.id
                LEFT JOIN (SELECT * FROM work_orders WHERE status = 'COMPLETED' AND is_deleted = false) AS work_orders ON work_orders.id_handling = handlings.id
                LEFT JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                LEFT JOIN ref_people ON ref_people.id = bookings.id_customer
                LEFT JOIN ref_branches ON ref_branches.id = bookings.id_branch
                LEFT JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                WHERE bookings.is_deleted = false {$goodsCondition} {$branchCondition} {$ownerCondition}
                GROUP BY bookings.id_customer, owner_name, branch_type, IFNULL(bookings.id_booking, bookings.id)
            ) AS stocks
            LEFT JOIN bookings ON bookings.id = stocks.id_booking
            LEFT JOIN ref_people ON bookings.id_customer = ref_people.id
            GROUP BY id_owner, owner_name, stocks.id_booking
            HAVING stock > 0 {$limitAllowEmptyStock} {$transactionExistCondition}
        ");

        $bookings = $bookings->result_array();
        if (!empty($bookings)) {
            for ($i = count($bookings) - 1; $i > 0; $i--) {
                for ($j = count($bookings) - 1; $j > 0; $j--) {
                    if ($i != $j) {
                        if ($bookings[$i]['no_booking'] == $bookings[$j]['no_booking']) {
                            if (empty($bookings[$i]['id_owner'])) {
                                array_splice($bookings, $i, 1);
                            }
                        }
                    }
                }
            }
        }

        $this->cache->save($cacheKey, $bookings, 60);

        return $bookings;
    }

    /**
     * Get booking goods comparator against job.
     *
     * @param $filters
     * @return array
     */
    public function getBookingGoodsComparator($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));

        $query = "
            SELECT 
                ref_people.name AS owner_name,
                ref_booking_types.category,
                ref_booking_types.booking_type,
                bookings.no_reference, 
                bookings.status,
                gate_in.first_gate_in_date,
                ref_goods.no_goods, 
                ref_goods.name AS goods_name, 
                booking_goods.quantity AS booking_quantity, 
                work_order_goods.quantity AS work_order_quantity,
                IF(booking_goods.id_unit = items.id_unit, ref_units.unit, NULL) AS booking_unit, 
                IF(work_order_goods.id_unit = items.id_unit, ref_units.unit, NULL) AS work_order_unit,
                booking_goods.ex_no_container AS booking_ex_container, 
                ref_goods.unit_weight AS work_order_weight,
                (ref_goods.unit_weight * work_order_goods.quantity) AS work_order_total_weight,
                ref_goods.unit_gross_weight AS work_order_gross_weight,
                (ref_goods.unit_gross_weight * work_order_goods.quantity) AS work_order_total_gross_weight,
                ref_goods.unit_volume AS work_order_volume,
                (ref_goods.unit_volume * work_order_goods.quantity) AS work_order_total_volume,
                work_order_goods.ex_no_container AS work_order_ex_container
            FROM bookings
            INNER JOIN ref_booking_types ON ref_booking_types.id = bookings.id_booking_type
            INNER JOIN ref_people ON ref_people.id = bookings.id_customer
            INNER JOIN
            (
                SELECT booking_goods.id_booking, booking_goods.id_goods, booking_goods.id_unit, IFNULL(ref_containers.no_container, booking_goods.ex_no_container) AS ex_no_container 
                FROM bookings
                INNER JOIN booking_goods ON booking_goods.id_booking = bookings.id
                LEFT JOIN booking_containers ON booking_containers.id = booking_goods.id_booking_container
                LEFT JOIN ref_containers ON ref_containers.id = booking_containers.id_container
                WHERE bookings.id_branch = '{$branchId}'
                
                UNION
                
                SELECT bookings.id AS id_booking, work_order_goods.id_goods, work_order_goods.id_unit, work_order_goods.ex_no_container 
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
	            INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
	            WHERE ref_handling_types.multiplier_goods <> 0 AND bookings.id_branch = '{$branchId}'
            ) AS items ON items.id_booking = bookings.id
            INNER JOIN ref_goods ON ref_goods.id = items.id_goods
            LEFT JOIN ref_units ON ref_units.id = items.id_unit
            LEFT JOIN (
                SELECT booking_goods.id_booking, id_goods, id_unit, IFNULL(ref_containers.no_container, booking_goods.ex_no_container) AS ex_no_container, SUM(booking_goods.quantity) AS quantity 
                FROM booking_goods 
                INNER JOIN bookings ON bookings.id = booking_goods.id_booking
                LEFT JOIN booking_containers ON booking_containers.id = booking_goods.id_booking_container
                LEFT JOIN ref_containers ON ref_containers.id = booking_containers.id_container
                WHERE bookings.id_branch = '{$branchId}'
                GROUP BY booking_goods.id_booking, id_goods, id_unit, IFNULL(ref_containers.no_container, booking_goods.ex_no_container)
            ) AS booking_goods 
                ON booking_goods.id_booking = bookings.id 
                    AND booking_goods.id_goods = items.id_goods
                    AND booking_goods.id_unit = items.id_unit
                    AND IFNULL(booking_goods.ex_no_container, '') = IFNULL(items.ex_no_container, '')
            LEFT JOIN (
                SELECT bookings.id AS id_booking, id_goods, id_unit, ex_no_container, SUM(quantity) AS quantity
                FROM bookings
                INNER JOIN ref_booking_types ON ref_booking_types.id = bookings.id_booking_type
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                INNER JOIN work_orders AS work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                WHERE work_orders.status = 'COMPLETED' 
                    AND work_orders.is_deleted = 0 
                    AND handlings.status = 'APPROVED'
                    AND handlings.is_deleted = 0
                    AND ref_handling_types.multiplier_goods <> 0 
                    AND bookings.id_branch = '{$branchId}'
                GROUP BY bookings.id, work_order_goods.id_goods, work_order_goods.id_unit, ex_no_container
            ) AS work_order_goods 
                ON work_order_goods.id_booking = bookings.id 
                    AND work_order_goods.id_goods = items.id_goods
                    AND work_order_goods.id_unit = items.id_unit
                    AND IFNULL(work_order_goods.ex_no_container, '') = IFNULL(items.ex_no_container, '')
            LEFT JOIN (
                SELECT 
                    handlings.id_booking, 
                    MIN(work_orders.gate_in_date) AS first_gate_in_date
                FROM handlings
                INNER JOIN bookings ON bookings.id = handlings.id_booking
                INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                WHERE handlings.is_deleted = FALSE 
                    AND handlings.status = 'APPROVED'
                    AND (ref_handling_types.handling_type = 'UNLOAD' OR ref_handling_types.handling_type = 'LOAD')
                    AND work_orders.is_deleted = FALSE
                GROUP BY id_booking
            ) AS gate_in ON gate_in.id_booking = bookings.id
            WHERE bookings.is_deleted = FALSE
        ";

        if (!empty($branchId)) {
            $query .= ' AND bookings.id_branch = ' . $branchId;
        }

        if (!empty($search)) {
            $search = addslashes($search);
            $query .= " AND (ref_people.name LIKE '%{$search}%'
                OR ref_booking_types.category LIKE '%{$search}%'
                OR ref_booking_types.booking_type LIKE '%{$search}%'
                OR bookings.no_reference LIKE '%{$search}%'
                OR bookings.status LIKE '%{$search}%'
                OR ref_goods.no_goods LIKE '%{$search}%'
                OR ref_goods.name LIKE '%{$search}%'
                OR booking_goods.quantity LIKE '%{$search}%'
                OR ref_units.unit LIKE '%{$search}%'
                OR booking_goods.ex_no_container LIKE '%{$search}%'
                OR work_order_goods.quantity LIKE '%{$search}%'
                OR ref_goods.unit_weight LIKE '%{$search}%'
                OR ref_goods.unit_gross_weight LIKE '%{$search}%'
                OR ref_goods.unit_volume LIKE '%{$search}%'
                OR work_order_goods.ex_no_container LIKE '%{$search}%'
            )";
        }

        if (key_exists('category', $filters) && !empty($filters['category'])) {
            $query .= " AND ref_booking_types.category = '{$filters['category']}'";
        }

        if (key_exists('status', $filters) && !empty($filters['status'])) {
            $query .= " AND bookings.status = '{$filters['status']}'";
        }

        if (key_exists('first_gate_in_from', $filters) && !empty($filters['first_gate_in_from'])) {
            $query .= " AND DATE(gate_in.first_gate_in_date) >= '" . format_date($filters['first_gate_in_from']) . "'";
        }

        if (key_exists('first_gate_in_to', $filters) && !empty($filters['first_gate_in_to'])) {
            $query .= " AND DATE(gate_in.first_gate_in_date) <= '" . format_date($filters['first_gate_in_to']) . "'";
        }

        if (!empty($sort)) {
            if($column == 'no') $column = 'bookings.id';
            $query .= ' ORDER BY ' . $column . ' ' . $sort;
        } else {
            $query .= ' ORDER BY bookings.id DESC';
        }

        if ($start < 0) {
            return $this->db->query($query)->result_array();
        }

        $reportTotal = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$query}) AS CI_count_all_results")
            ->row_array()['numrows'];

        $reportData = $this->db->query($query . " LIMIT {$start}, {$length}")->result_array();

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
     * Get work order summary container.
     *
     * @param array $filters
     * @return array
     */
    public function getWorkOrderContainerSummary($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branch = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $this->db->start_cache();

        $baseQuery = $this->db
            ->select([
                'bookings.id AS id_booking',
                'ref_people.name AS customer_name',
                'bookings.no_reference',
                'bookings.no_booking',
                'handlings.no_handling',
                'ref_handling_types.handling_type',
                'handlings.status AS handling_status',
                'safe_conducts.no_safe_conduct',
                'safe_conducts.no_police',
                'safe_conducts.security_in_date',
                'safe_conducts.security_out_date',
                'transporter_entry_permits.tep_code',
                'transporter_entry_permits.checked_in_at',
                'transporter_entry_permits.checked_out_at',
                'work_orders.no_work_order',
                'work_orders.taken_at',
                'work_orders.completed_at',
                'prv_users.name AS taken_by',
                'ref_containers.no_container',
                'ref_containers.type',
                'ref_containers.size',
                'work_order_containers.seal',
                'work_order_containers.is_empty',
                'work_order_containers.is_hold',
                'work_order_containers.status AS status_condition',
                'work_order_containers.status_danger',
                'work_order_containers.description',
                'work_orders.created_at',
            ])
            ->from('bookings')
            ->join('ref_people', 'ref_people.id = bookings.id_customer')
            ->join('handlings', 'handlings.id_booking = bookings.id')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('work_orders', 'work_orders.id_handling = handlings.id')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('transporter_entry_permits', 'transporter_entry_permits.id = work_orders.id_transporter_entry_permit', 'left')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container')
            ->join(UserModel::$tableUser, 'prv_users.id = work_orders.taken_by', 'left')
            ->where([
                'bookings.is_deleted' => false,
                'handlings.is_deleted' => false,
                'work_orders.is_deleted' => false,
            ]);

        if (!empty($branch)) {
            $baseQuery->where('bookings.id_branch', $branch);
        }

        if ($userType == 'EXTERNAL') {
            $baseQuery->where('bookings.id_customer', $customerId);
        }

        if (!empty($search)) {
            $baseQuery->group_start();
            $baseQuery->or_like('ref_people.name', trim($search));
            $baseQuery->or_like('no_reference', trim($search));
            $baseQuery->or_like('no_booking', trim($search));
            $baseQuery->or_like('no_handling', trim($search));
            $baseQuery->or_like('handling_type', trim($search));
            $baseQuery->or_like('no_work_order', trim($search));
            $baseQuery->or_like('no_container', trim($search));
            $baseQuery->or_like('ref_containers.type', trim($search));
            $baseQuery->or_like('size', trim($search));
            $baseQuery->or_like('seal', trim($search));
            $baseQuery->or_like('work_order_containers.status', trim($search));
            $baseQuery->or_like('work_order_containers.status_danger', trim($search));
            $baseQuery->or_like('work_order_containers.description', trim($search));
            $baseQuery->or_like('safe_conducts.no_safe_conduct', trim($search));
            $baseQuery->or_like('safe_conducts.no_police', trim($search));
            $baseQuery->group_end();
        }

        if (!empty($filters)) {
            if (key_exists('customers', $filters) && !empty($filters['customers'])) {
                if (is_array($filters['customers'])) {
                    $baseQuery->where_in('bookings.id_customer', $filters['customers']);
                } else {
                    $baseQuery->where('bookings.id_customer', $filters['customers']);
                }
            }

            if (key_exists('bookings', $filters) && !empty($filters['bookings'])) {
                if (is_array($filters['bookings'])) {
                    $baseQuery->where_in('bookings.id', $filters['bookings']);
                } else {
                    $baseQuery->where('bookings.id', $filters['bookings']);
                }
            }

            if (key_exists('handling_types', $filters) && !empty($filters['handling_types'])) {
                if (is_array($filters['handling_types'])) {
                    $baseQuery->where_in('ref_handling_types.id', $filters['handling_types']);
                } else {
                    $baseQuery->where('ref_handling_types.id', $filters['handling_types']);
                }
            }

            if (key_exists('containers', $filters) && !empty($filters['containers'])) {
                if (is_array($filters['containers'])) {
                    $baseQuery->where_in('ref_containers.id', $filters['containers']);
                } else {
                    $baseQuery->where('ref_containers.id', $filters['containers']);
                }
            }

            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
                if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                    $baseQuery->having('DATE(' . $filters['date_type'] . ')>=', format_date($filters['date_from']));
                }

                if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                    $baseQuery->having('DATE(' . $filters['date_type'] . ')<=', format_date($filters['date_to']));
                }
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

        if($column == 'no') $column = 'bookings.id';
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
     * Get work order summary goods.
     *
     * @param array $filters
     * @return array
     */
    public function getWorkOrderGoodsSummary($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branch = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $this->db->start_cache();

        $baseQuery = $this->db
            ->select([
                'bookings.id AS id_booking',
                'ref_people.name AS customer_name',
                'bookings.no_reference',
                'bookings.no_booking',
                'handlings.no_handling',
                'ref_handling_types.handling_type',
                'handlings.status AS handling_status',
                'safe_conducts.no_safe_conduct',
                'safe_conducts.no_police',
                'safe_conducts.security_in_date',
                'safe_conducts.security_out_date',
                'transporter_entry_permits.tep_code',
                'transporter_entry_permits.checked_in_at',
                'transporter_entry_permits.checked_out_at',
                'work_orders.no_work_order',
                'work_orders.taken_at',
                'work_orders.completed_at',
                'prv_users.name AS taken_by',
                'ref_goods.no_goods',
                'ref_goods.name AS goods_name',
                'ref_goods.no_hs',
                'ref_goods.whey_number',
                'work_order_goods.quantity',
                'ref_goods.unit_weight',
                '(ref_goods.unit_weight * work_order_goods.quantity) AS total_weight',
                'ref_goods.unit_gross_weight',
                '(ref_goods.unit_gross_weight * work_order_goods.quantity) AS total_gross_weight',
                'ref_goods.unit_volume',
                '(ref_goods.unit_volume * work_order_goods.quantity) AS total_volume',
                'ref_goods.unit_length',
                'ref_goods.unit_width',
                'ref_goods.unit_height',
                'work_order_goods.no_pallet',
                'work_order_goods.ex_no_container',
                'work_order_goods.is_hold',
                'work_order_goods.status AS status_condition',
                'work_order_goods.status_danger',
                'work_order_goods.description',
                'work_orders.created_at',
                'jadwal_spv.*',
            ])
            ->from('bookings')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
            ->join('ref_people', 'ref_people.id = bookings.id_customer')
            ->join('handlings', 'handlings.id_booking = bookings.id')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('work_orders', 'work_orders.id_handling = handlings.id')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('transporter_entry_permits', 'transporter_entry_permits.id = work_orders.id_transporter_entry_permit', 'left')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods')
            ->join('ref_units', 'ref_units.id = work_order_goods.id_unit')
            ->join(UserModel::$tableUser, 'prv_users.id = work_orders.taken_by', 'left')
            ->join('(SELECT UPPER('.$this->tableEmployees.'.`name`) AS name_spv,'.$this->tablePosition.'.position AS position_spv,'.$this->tableEmployees.'.`id_user` AS is_user_spv, GROUP_CONCAT(DISTINCT UPPER('.UserModel::$tableUser.'.`username`)) as user_spv,
            '.$this->tableLocation.'.`id` AS id_location,
            CONCAT('.$this->tableScheduleDivision.'.`date`," ",'.$this->tableSchedule.'.`start`) AS mulai, 
            CONCAT('.$this->tableScheduleDivision.'.`date`," ",'.$this->tableSchedule.'.`end`) AS selesai FROM '.$this->tableEmployees.'
            LEFT JOIN '.UserModel::$tableUser.' ON '.UserModel::$tableUser.'.`id` = '.$this->tableEmployees.'.`id_user`
            JOIN '.$this->tableLocation.' ON '.$this->tableLocation.'.`id` = '.$this->tableEmployees.'.`work_location`
            LEFT JOIN  '.$this->tablePosition.' ON '.$this->tablePosition.'.`id` = '.$this->tableEmployees.'.`id_position`
            LEFT JOIN '.$this->tablePositionSalaryComponent.' ON '.$this->tablePositionSalaryComponent.'.`id_position` = '.$this->tablePosition.'.`id`
            LEFT JOIN '.$this->tableSalaryComponent.' ON '.$this->tableSalaryComponent.'.id = '.$this->tablePositionSalaryComponent.'.`id_component`
            LEFT JOIN '.$this->tableScheduleDivision.' ON '.$this->tableScheduleDivision.'.id_employee = '.$this->tableEmployees.'.`id`
            JOIN '.$this->tableSchedule.' ON '.$this->tableSchedule.'.id = '.$this->tableScheduleDivision.'.`id_schedule`
            WHERE '.$this->tableSalaryComponent.'.`id` = "2" GROUP BY mulai HAVING mulai IS NOT NULL) AS jadwal_spv','jadwal_spv.id_location = ref_branches.`id_branch_hr` AND work_orders.completed_at BETWEEN jadwal_spv.mulai AND jadwal_spv.selesai','left')
            ->where([
                'bookings.is_deleted' => false,
                'handlings.is_deleted' => false,
                'work_orders.is_deleted' => false,
            ]);

        if (!empty($branch)) {
            $baseQuery->where('bookings.id_branch', $branch);
        }

        if ($userType == 'EXTERNAL') {
            $baseQuery->where('bookings.id_customer', $customerId);
        }

        if (!empty($search)) {
            $baseQuery->group_start();
            $baseQuery->or_like('ref_people.name', trim($search));
            $baseQuery->or_like('no_reference', trim($search));
            $baseQuery->or_like('no_booking', trim($search));
            $baseQuery->or_like('no_handling', trim($search));
            $baseQuery->or_like('handling_type', trim($search));
            $baseQuery->or_like('no_work_order', trim($search));
            $baseQuery->or_like('no_goods', trim($search));
            $baseQuery->or_like('ref_goods.name', trim($search));
            $baseQuery->or_like('no_hs', trim($search));
            $baseQuery->or_like('whey_number', trim($search));
            $baseQuery->or_like('no_pallet', trim($search));
            $baseQuery->or_like('ex_no_container', trim($search));
            $baseQuery->or_like('work_order_goods.status', trim($search));
            $baseQuery->or_like('work_order_goods.status_danger', trim($search));
            $baseQuery->or_like('work_order_goods.description', trim($search));
            $baseQuery->or_like('safe_conducts.no_safe_conduct', trim($search));
            $baseQuery->or_like('safe_conducts.no_police', trim($search));
            $baseQuery->group_end();
        }

        if (!empty($filters)) {
            if (key_exists('customers', $filters) && !empty($filters['customers'])) {
                if (is_array($filters['customers'])) {
                    $baseQuery->where_in('bookings.id_customer', $filters['customers']);
                } else {
                    $baseQuery->where('bookings.id_customer', $filters['customers']);
                }
            }

            if (key_exists('bookings', $filters) && !empty($filters['bookings'])) {
                if (is_array($filters['bookings'])) {
                    $baseQuery->where_in('bookings.id', $filters['bookings']);
                } else {
                    $baseQuery->where('bookings.id', $filters['bookings']);
                }
            }

            if (key_exists('handling_types', $filters) && !empty($filters['handling_types'])) {
                if (is_array($filters['handling_types'])) {
                    $baseQuery->where_in('ref_handling_types.id', $filters['handling_types']);
                } else {
                    $baseQuery->where('ref_handling_types.id', $filters['handling_types']);
                }
            }

            if (key_exists('goods', $filters) && !empty($filters['goods'])) {
                if (is_array($filters['goods'])) {
                    $baseQuery->where_in('ref_goods.id', $filters['goods']);
                } else {
                    $baseQuery->where('ref_goods.id', $filters['goods']);
                }
            }

            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
                if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                    $baseQuery->having('DATE(' . $filters['date_type'] . ')>=', format_date($filters['date_from']));
                }

                if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                    $baseQuery->having('DATE(' . $filters['date_type'] . ')<=', format_date($filters['date_to']));
                }
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

        if($column == 'no') $column = 'bookings.id';
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
	 * Get performance inbound outbound report.
	 *
	 * @param array $filters
	 * @return array|array[]
	 */
	public function getPerformanceInOutReport($filters = [])
	{
        if (key_exists('year', $filters) && !empty($filters['year'])) {
			$first_date = "{$filters['year']}-01-01";
            $first_year_week = "{$filters['year']}01";
		}else{
            $first_date = "DATE_FORMAT(NOW(), '%Y-01-01')";
            $first_year_week = "DATE_FORMAT(NOW(), '%Y01')";
        }
        if (key_exists('customers', $filters) && !empty($filters['customers'])) {
            $customersWhere = "AND bookings.id_customer IN (";
            $customersWhere .= implode(",",$filters['customers']);
            $customersWhere .= ")";
        } else {
            $customersWhere = "";
        }
        
        $sppb_date = "SELECT 
                        DATE(MAX(upload_documents.document_date)) AS sppb_date             
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE ref_document_types.document_type = 'SPPB'
                        AND upload_documents.is_deleted = FALSE
                        AND upload_documents.id_upload = bookings.id_upload
                    GROUP BY upload_documents.id_upload";

		$filterMonthIn = "AND (
            {$sppb_date}
        ) >= '{$first_date}'";
        $filterMonthLclCon = "AND (
            work_orders.completed_at
        ) >= '{$first_date}'";
        $filterMonthOut = "AND safe_conducts.security_out_date >= '{$first_date}'";
		if (key_exists('month', $filters) && !empty($filters['month'])) {
			$filterMonthIn = "AND MONTH((
                {$sppb_date}
            )) = '{$filters['month']}'";
            $filterMonthLclCon = "AND MONTH((
                work_orders.completed_at
            )) = '{$filters['month']}'";
            $filterMonthOut = "AND MONTH(safe_conducts.security_out_date) = '{$filters['month']}'";
		}
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

		$baseQuery = $this->db
			->select([
				'result.year_week',
                'result.year',
                'result.week',
                'ROUND(SUM(IFNULL(result.sppb_complete,0)),2) AS sppb_complete',
                'ROUND(SUM(IFNULL(result.total_container,0)),2) AS total_container',
                'ROUND(SUM(IFNULL(result.total_lcl,0)),2) AS total_lcl',
                'ROUND(SUM(IFNULL(result.st_sppb_req,0)),2) AS st_sppb_req',
                'ROUND(SUM(IFNULL(result.st_req_complete,0)),2) AS st_req_complete',
                'ROUND(SUM(IFNULL(result.total_fleet,0)),2) AS total_fleet',
                '(ROUND(SUM(IFNULL(result.total_container,0)),2) + ROUND(SUM(IFNULL(result.total_lcl,0)),2)) AS total_in',
                '(ROUND(SUM(IFNULL(result.total_container,0)),2) + ROUND(SUM(IFNULL(result.total_lcl,0)),2) + ROUND(SUM(IFNULL(result.total_fleet,0)),2)) AS total_all',
			])
			// to optimize query use "year_week only" to join (remove year and week)
			->from("(
                SELECT
                    YEARWEEK(sppb_date, 2) AS year_week,
                    LEFT(YEARWEEK(sppb_date, 2), LENGTH(YEARWEEK(sppb_date, 2)) - 2) AS YEAR,
                    WEEK(sppb_date, 2) AS WEEK,
                    AVG(DATEDIFF(DATE(completed_at), DATE(sppb_date))) AS sppb_complete,
                    '' AS total_container,
                    '' AS total_lcl,
                    '' AS st_sppb_req,
                    '' AS st_req_complete,
                    '' AS total_fleet
                FROM (
                    SELECT
                        YEARWEEK((
                            SELECT 
                                DATE(MAX(upload_documents.document_date)) AS sppb_date             
                            FROM upload_documents
                            INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                            WHERE ref_document_types.document_type = 'SPPB'
                                AND upload_documents.is_deleted = FALSE
                                AND upload_documents.id_upload = bookings.id_upload
                            GROUP BY upload_documents.id_upload
                        ), 2) AS year_week,
                        bookings.no_reference,
                        (
                            {$sppb_date}
                        ) AS sppb_date,
                        IFNULL(safe_conduct_inbounds.security_out_date, safe_conducts.security_out_date) AS security_out_date,
                        IFNULL(safe_conduct_inbounds.no_police, safe_conducts.no_police) AS no_police,
                        work_orders.no_work_order,
                        MAX(work_orders.completed_at) AS completed_at,
                        IFNULL(ref_containers.no_container, 'LCL') AS no_container,
                        COUNT(DISTINCT CASE WHEN IFNULL(ref_containers.no_container, 'LCL') != 'LCL' THEN IFNULL(safe_conduct_inbounds.security_out_date, safe_conducts.security_out_date) END) AS tot_container,
                        COUNT(DISTINCT CASE WHEN IFNULL(ref_containers.no_container, 'LCL') = 'LCL' THEN IFNULL(safe_conduct_inbounds.security_out_date, safe_conducts.security_out_date) END) AS tot_lcl,
                        IFNULL(ref_containers.size, 'LCL') AS container_size
                    FROM bookings
                    INNER JOIN handlings ON handlings.id_booking = bookings.id
                    INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                    LEFT JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                    LEFT JOIN ref_containers ON ref_containers.id = work_order_containers.id_container
                    LEFT JOIN (
                        SELECT safe_conducts.id, 
                            safe_conducts.id_booking, 
                            safe_conducts.security_in_date, 
                            safe_conducts.security_out_date, 
                            safe_conducts.no_police, 
                            safe_conduct_containers.id_container
                        FROM safe_conducts
                        INNER JOIN safe_conduct_containers ON safe_conduct_containers.id_safe_conduct = safe_conducts.id
                        WHERE safe_conducts.is_deleted = FALSE AND safe_conducts.type = 'INBOUND'
                    ) AS safe_conduct_inbounds ON safe_conduct_inbounds.id_booking = bookings.id 
                        AND safe_conduct_inbounds.id_container = work_order_containers.id_container
                    LEFT JOIN safe_conducts ON safe_conducts.id = work_orders.id_safe_conduct
                    WHERE bookings.id_branch = '{$branchId}'
                        {$customersWhere}
                        AND work_orders.status = 'COMPLETED'
                        AND id_handling_type IN(1, 3)
                        AND !(handlings.id_handling_type = 1 AND work_order_containers.id IS NOT NULL)
                        {$filterMonthIn}
                    GROUP BY no_reference
                ) AS summaries
                GROUP BY year_week
                UNION
                SELECT
                    YEARWEEK(completed_at, 2) AS year_week,
                    LEFT(YEARWEEK(completed_at, 2), LENGTH(YEARWEEK(completed_at, 2)) - 2) AS YEAR,
                    WEEK(completed_at, 2) AS WEEK,
                    '' AS sppb_complete,
                    SUM(tot_container) AS total_container,
                    SUM(tot_lcl) AS total_lcl,
                    '' AS st_sppb_req,
                    '' AS st_req_complete,
                    '' AS total_fleet
                FROM (
                    SELECT
                        time_week.year_week,
                        bookings.no_reference,
                        (
                            {$sppb_date}
                        ) AS sppb_date,
                        IFNULL(safe_conduct_inbounds.security_out_date, safe_conducts.security_out_date) AS security_out_date,
                        IFNULL(safe_conduct_inbounds.no_police, safe_conducts.no_police) AS no_police,
                        work_orders.no_work_order,
                        MAX(work_orders.completed_at) AS completed_at,
                        IFNULL(ref_containers.no_container, 'LCL') AS no_container,
                        COUNT(DISTINCT CASE WHEN IFNULL(ref_containers.no_container, 'LCL') != 'LCL' THEN IFNULL(safe_conduct_inbounds.security_out_date, safe_conducts.security_out_date) END) AS tot_container,
                        COUNT(DISTINCT CASE WHEN IFNULL(ref_containers.no_container, 'LCL') = 'LCL' THEN IFNULL(safe_conduct_inbounds.security_out_date, safe_conducts.security_out_date) END) AS tot_lcl,
                        IFNULL(ref_containers.size, 'LCL') AS container_size
                    FROM bookings
                    INNER JOIN handlings ON handlings.id_booking = bookings.id
                    INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                    INNER JOIN (SELECT YEARWEEK(DATE, 2) AS year_week,min_year_week.min_date,max_year_week.max_date,
                        get_min_week_cut_off(YEARWEEK(DATE, 2), '{$branchId}') AS min_date_time,
                        get_max_week_cut_off(YEARWEEK(DATE, 2), '{$branchId}') AS max_date_time
                        FROM ref_dates
                        JOIN (SELECT MIN(DATE) AS min_date,YEARWEEK(ref_dates.date,2) AS year_week FROM ref_dates 
                            GROUP BY YEARWEEK(ref_dates.date,2)) AS min_year_week ON min_year_week.year_week = YEARWEEK(ref_dates.date, 2)
                        JOIN (SELECT MAX(DATE) AS max_date,YEARWEEK(ref_dates.date,2) AS year_week FROM ref_dates 
                            GROUP BY YEARWEEK(ref_dates.date,2)) AS max_year_week ON max_year_week.year_week = YEARWEEK(ref_dates.date, 2)
                        WHERE YEARWEEK(DATE, 2) >= '{$first_year_week}'
                        GROUP BY YEARWEEK(DATE, 2)) AS time_week 
                        ON time_week.min_date_time <= work_orders.completed_at 
                            AND time_week.max_date_time >= work_orders.completed_at
                    LEFT JOIN work_order_containers ON work_order_containers.id_work_order = work_orders.id
                    LEFT JOIN ref_containers ON ref_containers.id = work_order_containers.id_container
                    LEFT JOIN (
                        SELECT safe_conducts.id, 
                            safe_conducts.id_booking, 
                            safe_conducts.security_in_date, 
                            safe_conducts.security_out_date, 
                            safe_conducts.no_police, 
                            safe_conduct_containers.id_container
                        FROM safe_conducts
                        INNER JOIN safe_conduct_containers ON safe_conduct_containers.id_safe_conduct = safe_conducts.id
                        WHERE safe_conducts.is_deleted = FALSE AND safe_conducts.type = 'INBOUND'
                    ) AS safe_conduct_inbounds ON safe_conduct_inbounds.id_booking = bookings.id 
                        AND safe_conduct_inbounds.id_container = work_order_containers.id_container
                    LEFT JOIN safe_conducts ON safe_conducts.id = work_orders.id_safe_conduct
                    WHERE bookings.id_branch = '{$branchId}'
                        {$customersWhere}
                        AND work_orders.status = 'COMPLETED'
                        AND id_handling_type IN(1, 3)
                        AND !(handlings.id_handling_type = 1 AND work_order_containers.id IS NOT NULL)
                        {$filterMonthLclCon}
                    GROUP BY work_orders.completed_at
                ) AS summaries
                GROUP BY year_week
                UNION
                SELECT
                    YEARWEEK(security_out_date, 2) AS year_week,
                    LEFT(YEARWEEK(security_out_date, 2), LENGTH(YEARWEEK(security_out_date, 2)) - 2) AS YEAR,
                    WEEK(security_out_date, 2) AS WEEK,                    
                    '' AS sppb_complete,
                    '' AS total_container,
                    '' AS total_lcl,
                    AVG(DATEDIFF(DATE(request_date), DATE(sppb_date))) AS st_sppb_req,
                    AVG(DATEDIFF(DATE(completed_at), DATE(request_date))) AS st_req_complete,
                    '' AS total_fleet
                FROM (
                    SELECT
                        YEARWEEK(security_out_date, 2) AS year_week,
                        ref_goods.no_goods,
                        bookings.no_reference,
                        (
                            SELECT 
                                DATE(MAX(upload_documents.document_date)) AS sppb_date             
                            FROM upload_documents
                            INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                            WHERE ref_document_types.document_type = 'SPPB'
                                AND upload_documents.is_deleted = FALSE
                                AND upload_documents.id_upload = bookings.id_upload
                            GROUP BY upload_documents.id_upload
                        ) AS sppb_date,
                        requests.request_date,
                        safe_conducts.security_out_date,
                        safe_conducts.no_police,
                        safe_conducts.id_transporter_entry_permit,
                        work_orders.no_work_order,
                        work_orders.completed_at
                    FROM bookings
                    INNER JOIN handlings ON handlings.id_booking = bookings.id
                    INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                    INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                    INNER JOIN ref_goods ON ref_goods.id = work_order_goods.id_goods
                    INNER JOIN safe_conducts ON safe_conducts.id = work_orders.id_safe_conduct
                    INNER JOIN (
                        SELECT
                            MAX(tep_requests.created_at) AS request_date,
                            tep_request_uploads.id_upload,
                            tep_request_uploads.id_goods,
                            tep_request_uploads.id_unit,
                            IFNULL(tep_request_uploads.ex_no_container, '') AS ex_no_container
                        FROM transporter_entry_permit_requests AS tep_requests
                        INNER JOIN transporter_entry_permit_request_uploads AS tep_request_uploads 
                            ON tep_request_uploads.id_request = tep_requests.id
                        GROUP BY tep_request_uploads.id_upload, 
                            tep_request_uploads.id_goods, 
                            tep_request_uploads.id_unit, 
                            tep_request_uploads.ex_no_container
                    ) AS requests 
                        ON requests.id_upload = bookings.id_upload
                            AND requests.id_goods = work_order_goods.id_goods
                            AND requests.id_unit = work_order_goods.id_unit
                            AND IFNULL(requests.ex_no_container, '') = IFNULL(work_order_goods.ex_no_container, '')
                    WHERE bookings.id_branch = '{$branchId}'
                        {$customersWhere}
                        AND work_orders.status = 'COMPLETED'
                        AND id_handling_type IN(2)
                        {$filterMonthOut}
                ) AS summaries
                GROUP BY year_week
                UNION
                SELECT
                    YEARWEEK(security_out_date, 2) AS year_week,
                    LEFT(YEARWEEK(security_out_date, 2), LENGTH(YEARWEEK(security_out_date, 2)) - 2) AS YEAR,
                    WEEK(security_out_date, 2) AS WEEK,                    
                    '' AS sppb_complete,
                    '' AS total_container,
                    '' AS total_lcl,
                    '' AS st_sppb_req,
                    '' AS st_req_complete,
                    COUNT(DISTINCT id_transporter_entry_permit) AS total_fleet
                FROM (
                    SELECT
                        YEARWEEK(security_out_date, 2) AS year_week,
                        ref_goods.no_goods,
                        bookings.no_reference,
                        (
                            SELECT 
                                DATE(MAX(upload_documents.document_date)) AS sppb_date             
                            FROM upload_documents
                            INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                            WHERE ref_document_types.document_type = 'SPPB'
                                AND upload_documents.is_deleted = FALSE
                                AND upload_documents.id_upload = bookings.id_upload
                            GROUP BY upload_documents.id_upload
                        ) AS sppb_date,
                        safe_conducts.security_out_date,
                        safe_conducts.no_police,
                        safe_conducts.id_transporter_entry_permit,
                        work_orders.no_work_order,
                        work_orders.completed_at
                    FROM bookings
                    INNER JOIN handlings ON handlings.id_booking = bookings.id
                    INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                    INNER JOIN work_order_goods ON work_order_goods.id_work_order = work_orders.id
                    INNER JOIN ref_goods ON ref_goods.id = work_order_goods.id_goods
                    INNER JOIN safe_conducts ON safe_conducts.id = work_orders.id_safe_conduct
                    WHERE bookings.id_branch = '{$branchId}'
                        {$customersWhere}
                        AND work_orders.status = 'COMPLETED'
                        AND id_handling_type IN(2)
                        {$filterMonthOut}
                ) AS summaries
                GROUP BY year_week) AS result")
			// when use MAX use to find latest approved 'APPROVED%'
            ->group_by('year_week')
			->order_by('year_week', 'desc');

		if (key_exists('year', $filters) && !empty($filters['year'])) {
			$baseQuery->having('year', $filters['year']);
		}

		$reports = $baseQuery->get()->result_array();

		// fill the gap week
		if (!empty($reports) && empty(get_url_param('month'))) {
			for ($i = 1; $i < $reports[0]['week']; $i++) {
				$notFound = true;
				foreach ($reports as $index => $report) {
					if ($report['week'] == $i) {
						$notFound = false;
						break;
					}
				}
				if ($notFound) {
					array_splice($reports, count($reports) - $i + 1, 0, [
						[
                            'year_week' => $filters['year'].$i,
							'year' => $filters['year'],
							'week' => $i,
							'sppb_complete' => '0',
							'total_container' => '0',
							'total_lcl' => '0',
                            'st_sppb_req' => '0',
                            'st_req_complete' => '0',
                            'total_fleet' => '0',
                            'total_in' => '0',
                            'total_all' => '0',
						]
					]);
				}
			}
		}

		return $reports;
	}

    /**
	 * Get performance inbound outbound report.
	 *
	 * @param array $filters
	 * @return array|array[]
	 */
	public function getPerformanceInOutReportDetailSppbComplete($filters = [])
	{
        $sppb_date = "SELECT 
                        DATE(MAX(upload_documents.document_date)) AS sppb_date             
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE ref_document_types.document_type = 'SPPB'
                        AND upload_documents.is_deleted = FALSE
                        AND upload_documents.id_upload = bookings.id_upload
                    GROUP BY upload_documents.id_upload";
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();
        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $customersWhere = "AND bookings.id_customer IN (";
            $customersWhere .= implode(",",$filters['customer']);
            $customersWhere .= ")";
        } else {
            $customersWhere = "";
        }

		$baseQuery = $this->db
			->select([
				'YEARWEEK((
                    SELECT 
                        DATE(MAX(upload_documents.document_date)) AS sppb_date             
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE ref_document_types.document_type = "SPPB"
                        AND upload_documents.is_deleted = FALSE
                        AND upload_documents.id_upload = bookings.id_upload
                    GROUP BY upload_documents.id_upload
                ), 2) AS year_week',
                'bookings.no_reference',
                "(
                    {$sppb_date}
                ) AS sppb_date",
                'IFNULL(safe_conduct_inbounds.security_out_date, safe_conducts.security_out_date) AS security_out_date',
                'IFNULL(safe_conduct_inbounds.no_police, safe_conducts.no_police) AS no_police',
                'work_orders.no_work_order',
                'MAX(work_orders.completed_at) AS completed_at',
                'IFNULL(ref_containers.no_container, "LCL") AS no_container',
                'IFNULL(ref_containers.size, "LCL") AS container_size',
                'ref_people.name AS customer_name',
                "DATEDIFF(MAX(work_orders.completed_at), ({$sppb_date})) AS diff_date"
			])
			// to optimize query use "year_week only" to join (remove year and week)
			->from("bookings")
            ->join("handlings", "handlings.id_booking = bookings.id", "INNER")
            ->join("work_orders", "work_orders.id_handling = handlings.id", "INNER")
            ->join("work_order_containers", "work_order_containers.id_work_order = work_orders.id", "LEFT")
            ->join("ref_containers", "ref_containers.id = work_order_containers.id_container", "LEFT")
            ->join("(
                         SELECT safe_conducts.id, 
                             safe_conducts.id_booking, 
                             safe_conducts.security_in_date, 
                             safe_conducts.security_out_date, 
                             safe_conducts.no_police, 
                             safe_conduct_containers.id_container
                         FROM safe_conducts
                         INNER JOIN safe_conduct_containers ON safe_conduct_containers.id_safe_conduct = safe_conducts.id
                         WHERE safe_conducts.is_deleted = FALSE AND safe_conducts.type = 'INBOUND'
                     ) AS safe_conduct_inbounds", "safe_conduct_inbounds.id_booking = bookings.id 
                         AND safe_conduct_inbounds.id_container = work_order_containers.id_container", "LEFT")
            ->join("safe_conducts", "safe_conducts.id = work_orders.id_safe_conduct", "LEFT")
            ->join("ref_people", "ref_people.id = bookings.id_customer", "LEFT")
            ->where("bookings.id_branch = '{$branchId}'
                {$customersWhere}
                AND work_orders.status = 'COMPLETED'
                AND id_handling_type IN(1, 3)
                AND !(handlings.id_handling_type = 1 AND work_order_containers.id IS NOT NULL)
                AND YEARWEEK((
                    {$sppb_date}
                ), 2) = {$filters['year_week']}")
            ->group_by("no_reference");

		$reports = $baseQuery->get()->result_array();

		return $reports;
	}
    /**
	 * Get performance inbound outbound report detail total lcl container.
	 *
	 * @param array $filters
	 * @return array|array[]
	 */
	public function getPerformanceInOutReportDetailTotalLclContainer($filters = [])
	{
        $sppb_date = "SELECT 
                        DATE(MAX(upload_documents.document_date)) AS sppb_date             
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE ref_document_types.document_type = 'SPPB'
                        AND upload_documents.is_deleted = FALSE
                        AND upload_documents.id_upload = bookings.id_upload
                    GROUP BY upload_documents.id_upload";
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();
        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $customersWhere = "AND bookings.id_customer IN (";
            $customersWhere .= implode(",",$filters['customer']);
            $customersWhere .= ")";
        } else {
            $customersWhere = "";
        }

		$baseQuery = $this->db
			->select([
				'YEARWEEK((
                    work_orders.completed_at
                ), 2) AS year_week',
                'bookings.no_reference',
                "(
                    {$sppb_date}
                ) AS sppb_date",
                'IFNULL(safe_conduct_inbounds.security_out_date, safe_conducts.security_out_date) AS security_out_date',
                'IFNULL(safe_conduct_inbounds.no_police, safe_conducts.no_police) AS no_police',
                'work_orders.no_work_order',
                'work_orders.completed_at',
                'IFNULL(ref_containers.no_container, "LCL") AS no_container',
                'IFNULL(ref_containers.size, "LCL") AS container_size',
                'ref_people.name AS customer_name',
			])
			// to optimize query use "year_week only" to join (remove year and week)
			->from("bookings")
            ->join("handlings", "handlings.id_booking = bookings.id", "INNER")
            ->join("work_orders", "work_orders.id_handling = handlings.id", "INNER")
            ->join("work_order_containers", "work_order_containers.id_work_order = work_orders.id", "LEFT")
            ->join("ref_containers", "ref_containers.id = work_order_containers.id_container", "LEFT")
            ->join("(
                         SELECT safe_conducts.id, 
                             safe_conducts.id_booking, 
                             safe_conducts.security_in_date, 
                             safe_conducts.security_out_date, 
                             safe_conducts.no_police, 
                             safe_conduct_containers.id_container
                         FROM safe_conducts
                         INNER JOIN safe_conduct_containers ON safe_conduct_containers.id_safe_conduct = safe_conducts.id
                         WHERE safe_conducts.is_deleted = FALSE AND safe_conducts.type = 'INBOUND'
                     ) AS safe_conduct_inbounds", "safe_conduct_inbounds.id_booking = bookings.id 
                         AND safe_conduct_inbounds.id_container = work_order_containers.id_container", "LEFT")
            ->join("safe_conducts", "safe_conducts.id = work_orders.id_safe_conduct", "LEFT")
            ->join("ref_people", "ref_people.id = bookings.id_customer", "LEFT")
            ->where("bookings.id_branch = '{$branchId}'
                {$customersWhere}
                AND work_orders.status = 'COMPLETED'
                AND id_handling_type IN(1, 3)
                AND !(handlings.id_handling_type = 1 AND work_order_containers.id IS NOT NULL)
                AND work_orders.completed_at >= 
                    (SELECT MIN(CONCAT(get_min_week({$filters['year_week']}), ' ',cut_off.`start`)) AS START
                    FROM ref_operation_cut_offs cut_off WHERE id_branch = '{$branchId}' AND STATUS = 'ACTIVE'
                    GROUP BY id_branch)
                AND work_orders.completed_at <= 
                    (SELECT 
                    MAX(IF(CONCAT(get_max_week({$filters['year_week']}), ' ',cut_off.`start`) <= CONCAT(get_max_week({$filters['year_week']}), ' ',cut_off.`end`),
                    CONCAT(get_max_week({$filters['year_week']}), ' ',cut_off.`end`),
                    CONCAT(get_max_week({$filters['year_week']}) + INTERVAL 1 DAY, ' ',cut_off.`end`))) AS END FROM ref_operation_cut_offs cut_off
                    WHERE id_branch = '{$branchId}' AND STATUS = 'ACTIVE'
                    GROUP BY id_branch)");

		$reports = $baseQuery->get()->result_array();

		return $reports;
	}

    /**
	 * Get performance inbound outbound report detail st sppb request.
	 *
	 * @param array $filters
	 * @return array|array[]
	 */
	public function getPerformanceInOutReportDetailStSppbRequest($filters = [])
	{
        $sppb_date = "SELECT 
                        DATE(MAX(upload_documents.document_date)) AS sppb_date             
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE ref_document_types.document_type = 'SPPB'
                        AND upload_documents.is_deleted = FALSE
                        AND upload_documents.id_upload = bookings.id_upload
                    GROUP BY upload_documents.id_upload";
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();
        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $customersWhere = "AND bookings.id_customer IN (";
            $customersWhere .= implode(",",$filters['customer']);
            $customersWhere .= ")";
        } else {
            $customersWhere = "";
        }

		$baseQuery = $this->db
			->select([
				'YEARWEEK(security_out_date, 2) AS year_week',
                'ref_goods.no_goods',
                'ref_goods.name AS goods_name',
                'bookings.no_reference',
                "(
                    {$sppb_date}
                ) AS sppb_date",
                'requests.request_date AS request_date',
                'requests.no_request',
                'safe_conducts.security_out_date',
                'safe_conducts.no_police',
                'safe_conducts.id_transporter_entry_permit',
                'work_orders.no_work_order',
                'work_orders.completed_at AS completed_at',
                'ref_people.name AS customer_name',
                "DATEDIFF(requests.request_date, ({$sppb_date})) AS diff_date",
                "DATEDIFF(work_orders.completed_at, requests.request_date) AS diff_date_complete",
			])
			// to optimize query use "year_week only" to join (remove year and week)
			->from("bookings")
            ->join("handlings", "handlings.id_booking = bookings.id", "INNER")
            ->join("work_orders", "work_orders.id_handling = handlings.id", "INNER")
            ->join("work_order_goods", "work_order_goods.id_work_order = work_orders.id", "INNER")
            ->join("ref_goods", "ref_goods.id = work_order_goods.id_goods", "INNER")
            ->join("safe_conducts", "safe_conducts.id = work_orders.id_safe_conduct", "INNER")
            ->join("(
                    SELECT
                        MAX(tep_requests.created_at) AS request_date,
                        tep_request_uploads.id_upload,
                        tep_request_uploads.id_goods,
                        tep_request_uploads.id_unit,
                        IFNULL(tep_request_uploads.ex_no_container, '') AS ex_no_container,
                        MAX(tep_requests.no_request) AS no_request
                    FROM transporter_entry_permit_requests AS tep_requests
                    INNER JOIN transporter_entry_permit_request_uploads AS tep_request_uploads 
                        ON tep_request_uploads.id_request = tep_requests.id
                    GROUP BY tep_request_uploads.id_upload, 
                        tep_request_uploads.id_goods, 
                        tep_request_uploads.id_unit, 
                        tep_request_uploads.ex_no_container
                ) AS requests", 'requests.id_upload = bookings.id_upload
                    AND requests.id_goods = work_order_goods.id_goods
                    AND requests.id_unit = work_order_goods.id_unit
                    AND IFNULL(requests.ex_no_container, "") = IFNULL(work_order_goods.ex_no_container, "")', "INNER")
            ->join('ref_people','ref_people.id = bookings.id_customer', 'left')                    
            ->where("bookings.id_branch = '{$branchId}'
                {$customersWhere}
                AND work_orders.status = 'COMPLETED'
                AND id_handling_type IN(2)
                AND YEARWEEK(safe_conducts.security_out_date,2) = '{$filters['year_week']}'");

		$reports = $baseQuery->get()->result_array();

		return $reports;
	}

    /**
	 * Get performance inbound outbound report detail total fleet.
	 *
	 * @param array $filters
	 * @return array|array[]
	 */
	public function getPerformanceInOutReportDetailTotalFleet($filters = [])
	{
        $sppb_date = "SELECT 
                        DATE(MAX(upload_documents.document_date)) AS sppb_date             
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE ref_document_types.document_type = 'SPPB'
                        AND upload_documents.is_deleted = FALSE
                        AND upload_documents.id_upload = bookings.id_upload
                    GROUP BY upload_documents.id_upload";
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();
        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $customersWhere = "AND bookings.id_customer IN (";
            $customersWhere .= implode(",",$filters['customer']);
            $customersWhere .= ")";
        } else {
            $customersWhere = "";
        }

		$baseQuery = $this->db
			->select([
				'YEARWEEK(security_out_date, 2) AS year_week',
                'ref_goods.no_goods',
                'bookings.no_reference',
                "(
                    {$sppb_date}
                ) AS sppb_date",
                'safe_conducts.security_out_date',
                'safe_conducts.no_police',
                'safe_conducts.id_transporter_entry_permit',
                'safe_conducts.vehicle_type',
                'safe_conducts.driver',
                'safe_conducts.no_safe_conduct',
                'work_orders.no_work_order',
                'work_orders.completed_at',
                'ref_people.name AS customer_name',
			])
			// to optimize query use "year_week only" to join (remove year and week)
			->from("bookings")
            ->join("handlings", "handlings.id_booking = bookings.id", "INNER")
            ->join("work_orders", "work_orders.id_handling = handlings.id", "INNER")
            ->join("work_order_goods", "work_order_goods.id_work_order = work_orders.id", "INNER")
            ->join("ref_goods", "ref_goods.id = work_order_goods.id_goods", "INNER")
            ->join("safe_conducts", "safe_conducts.id = work_orders.id_safe_conduct", "INNER")
            ->join('ref_people','ref_people.id = bookings.id_customer', 'left')                    
            ->where("bookings.id_branch = '{$branchId}'
                {$customersWhere}
                AND work_orders.status = 'COMPLETED'
                AND id_handling_type IN(2)
                AND YEARWEEK(safe_conducts.security_out_date,2) = '{$filters['year_week']}'")
            ->group_by("safe_conducts.id_transporter_entry_permit");

		$reports = $baseQuery->get()->result_array();

		return $reports;
	}
}