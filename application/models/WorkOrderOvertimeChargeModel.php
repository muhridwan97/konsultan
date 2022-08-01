<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkOrderOvertimeChargeModel extends MY_Model
{
    protected $table = 'work_order_overtime_charges';

    /**
     * WorkOrderOvertimeChargeModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $branchCondition = '';
        if (!empty($branchId)) {
            $branchCondition = 'AND ref_service_hours.id_branch = "' . $branchId . '"';
        }

        $baseQuery = $this->db
            ->select([
                'ref_branches.branch',
                'bookings.id AS id_booking',
                'booking_inbounds.no_reference AS no_reference_in',
                'bookings.no_reference',
                'work_orders.id AS id_work_order',
                'work_orders.no_work_order',
                'ref_people.id AS id_customer',
                'ref_people.name AS customer_name',
                'work_orders.taken_at',
                'work_orders.completed_at',
                'ref_service_hours.id AS id_service_hour',
                'ref_service_hours.service_day',
                'ref_service_hours.service_time_start',
                'ref_service_hours.service_time_end',
                'ref_service_hours.effective_date',
                "IF(TIME(completed_at) > TIME(service_time_end), TIMEDIFF(TIME(completed_at), TIME(service_time_end)), 0) AS total_overtime",
                "IF(TIME(completed_at) > TIME(service_time_end), CEIL(TIME_TO_SEC(TIMEDIFF(TIME(completed_at), TIME(service_time_end))) / 3600), 0) AS total_overtime_hour",
                "IF(TIME(completed_at) > TIME(service_time_end), FLOOR(TIME_TO_SEC(TIMEDIFF(TIME(completed_at), TIME(service_time_end))) / 60), 0) AS total_overtime_minute",
                'work_order_overtime_charges.id',
                'IF(TIME(completed_at) > TIME(service_time_end), "OVERTIME", "NORMAL") AS status',
                'work_order_overtime_charges.overtime_charged_to',
                'work_order_overtime_charges.overtime_attachment',
                'work_order_overtime_charges.reason',
                'work_order_overtime_charges.description',
                'work_order_overtime_charges.created_at',
                'prv_users.name AS validator_name',
            ])
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = bookings.id_booking')
            ->join('ref_people', 'ref_people.id = bookings.id_customer')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
            ->join('work_order_overtime_charges', 'work_order_overtime_charges.id_work_order = work_orders.id', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = work_order_overtime_charges.created_by', 'left')
            ->join("(
                SELECT work_orders.id AS id_work_order, service_day, MAX(effective_date) AS effective_date 
                FROM ref_service_hours
                INNER JOIN work_orders ON ref_service_hours.effective_date <= DATE(work_orders.completed_at)
                    AND ref_service_hours.service_day = DAYNAME(work_orders.completed_at)
                WHERE ref_service_hours.is_deleted = false {$branchCondition}
                GROUP BY id_work_order, service_day
            ) AS latest_service_hours", 'latest_service_hours.id_work_order = work_orders.id', 'left')
            ->join("(SELECT * FROM ref_service_hours WHERE is_deleted = false {$branchCondition}) AS ref_service_hours", 'ref_service_hours.service_day = latest_service_hours.service_day 
                AND ref_service_hours.effective_date = latest_service_hours.effective_date', 'left')
            ->where('work_orders.status', 'COMPLETED');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        return $baseQuery;
    }


    /**
     * Get all delivery with or without deleted records.
     *
     * @param array $filters
     * @param bool $withTrashed
     * @return mixed
     */
    public function getAll($filters = [], $withTrashed = false)
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $this->db->start_cache();

        $baseQuery = $this->getBaseQuery($branchId)
            ->where('handlings.id_handling_type', $handlingTypeIdOutbound); // specific load handling;

        if (!$withTrashed) {
            $baseQuery->where('work_orders.is_deleted', false);
        }

        if (!empty($search)) {
            $baseQuery
                ->group_start()
                ->like('work_orders.no_work_order', $search)
                ->or_like('work_orders.taken_at', $search)
                ->or_like('work_orders.completed_at', $search)
                ->or_like('ref_people.name', $search)
                ->or_like('bookings.no_booking', $search)
                ->or_like('bookings.no_reference', $search)
                ->or_like('booking_inbounds.no_reference', $search)
                ->or_like('ref_service_hours.service_day', $search)
                ->or_like('ref_service_hours.effective_date', $search)
                ->or_like('work_order_overtime_charges.reason', $search)
                ->or_like('work_order_overtime_charges.overtime_charged_to', $search)
                ->or_like('work_order_overtime_charges.overtime_attachment', $search)
                ->group_end();
        }

        if (key_exists('related_service_day', $filters) && !empty($filters['related_service_day'])) {
            $baseQuery->where('ref_service_hours.service_day', $filters['related_service_day']);
            $baseQuery->where('ref_service_hours.id!=', $filters['related_service_except']);
        }

        if (key_exists('status', $filters) && !empty($filters['status'])) {
            if ($filters['status'] == 'PENDING') {
                $baseQuery->where('work_order_overtime_charges.id IS NULL');
            } else {
                $baseQuery->where('work_order_overtime_charges.id IS NOT NULL');
            }
        }

        if (key_exists('charged_to', $filters) && !empty($filters['charged_to'])) {
            if ($filters['charged_to'] == 'PENDING') {
                $baseQuery->where('work_order_overtime_charges.id IS NULL');
            } else {
                $baseQuery->where('work_order_overtime_charges.overtime_charged_to', $filters['charged_to']);
            }
        }

        if (key_exists('customers', $filters) && !empty($filters['customers'])) {
            $baseQuery->where_in('bookings.id_customer', $filters['customers']);
        }

        if (key_exists('bookings', $filters) && !empty($filters['bookings'])) {
            $baseQuery->group_start();
            $baseQuery->where_in('booking_inbounds.id', $filters['bookings']);
            $baseQuery->or_where_in('bookings.id', $filters['bookings']);
            $baseQuery->group_end();
        }

        if (key_exists('job_data', $filters)) {
            if ($filters['job_data'] == 'OVERTIME') {
                $baseQuery->having('total_overtime_hour > 0');
            } elseif ($filters['job_data'] == 'NON OVERTIME') {
                $baseQuery->having('total_overtime_hour = 0');
            }
        } else {
            $baseQuery->having('total_overtime_hour > 0');
        }

        if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
            if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                $baseQuery->where('DATE(' . $filters['date_type'] . ')>=', format_date($filters['date_from']));
            }

            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $baseQuery->where('DATE(' . $filters['date_type'] . ')<=', format_date($filters['date_to']));
            }
        }

        $this->db->stop_cache();

        if ($start < 0) {
            if ($column == 'no') $column = 'ref_service_hours.id';
            $allData = $baseQuery->order_by($column, $sort)->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $query = $this->db->get_compiled_select();
        $total = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$query}) AS CI_count_all_results")->row_array()['numrows'];

        if ($column == 'no') $column = 'work_orders.completed_at';
        $page = $baseQuery->order_by($column, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        foreach ($data as &$row) {
            $row['no'] = ++$start;
            $row['has_attachment'] = empty($row['overtime_attachment']) ? 'NO' : 'YES';
            $row['overtime_attachment_url'] = empty($row['overtime_attachment']) ? '' : asset_url($row['overtime_attachment']);
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
     * Check validation overtime.
     *
     * @param int $dayTolerant
     * @return bool
     */
    public function isOvertimeValidationCompleted($dayTolerant = 2)
    {
        $outstandingOvertimes = $this->getAll([
            'job_data' => 'OVERTIME',
            'status' => 'PENDING',
            'date_type' => 'work_orders.completed_at',
            'date_to' => date('Y-m-d', strtotime("-" . $dayTolerant . " days")),
        ]);

        if (empty($outstandingOvertimes)) {
            return true;
        }
        return false;

        /*
        foreach ($outstandingOvertimes as $outstandingOvertime) {
            $diffFromToday = difference_date(format_date($outstandingOvertime['completed_at']), date('Y-m-d'));
            if ($diffFromToday > $dayTolerant) {
                return false;
            }
        }

        return true;
        */
    }
}
