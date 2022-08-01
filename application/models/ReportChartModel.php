<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportChartModel extends CI_Model
{
    /**
     * ReportChartModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get activity report chart container.
     * @param $filters
     * @return array
     */
    public function getReportChartActivityContainer($filters)
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();
        $handlingTypeIdInbound = get_setting('default_inbound_handling');
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $filterDate = 'WHERE 1 = 1';
        if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
            if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                $filterDate .= ' AND ' . $filters['date_type'] . '>="' . sql_date_format($filters['date_from']) . '"';
            }
            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $filterDate .= ' AND ' . $filters['date_type'] . '<="' . sql_date_format($filters['date_to']) . '"';
            }
        }

        $report = $this->db->select([
            'year',
            'month',
            'SUM(IF(id_handling_type = ' . $handlingTypeIdInbound . ', 1, 0)) AS total_in',
            'SUM(IF(id_handling_type = ' . $handlingTypeIdOutbound . ', 1, 0)) AS total_out',
        ])
            ->from('bookings')
            ->join('handlings', 'handlings.id_booking = bookings.id')
            ->join("(
                SELECT work_orders.id, work_orders.id_handling, YEAR(completed_at) AS year, DATE_FORMAT(completed_at, '%M') AS month 
                FROM work_orders
                LEFT JOIN safe_conducts ON safe_conducts.id = work_orders.id_safe_conduct
                INNER JOIN handlings ON handlings.id = work_orders.id_handling
                INNER JOIN bookings ON bookings.id = handlings.id_booking
                {$filterDate}
            ) AS work_orders", 'work_orders.id_handling = handlings.id')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container')
            ->where('year = YEAR(NOW())')
            ->group_by('year, month');

        if (empty($filters) || !key_exists('data', $filters)) {
            $report->where('id_container IS NOT NULL');
        } else {
            if (key_exists('data', $filters) && !empty($filters['data'])) {
                if ($filters['data'] == 'realization') {
                    $report->where('id_container IS NOT NULL');
                }
            }

            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                $report->where_in('id_owner', $filters['owner']);
            }

            if (key_exists('container', $filters) && !empty($filters['container'])) {
                $report->where_in('id_container', $filters['container']);
            }

            if (key_exists('size', $filters) && !empty($filters['size'])) {
                $report->where_in('ref_containers.size', $filters['size']);
            }
        }

        if (!empty($branchId)) {
            $report->where('id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('id_owner', $customerId);
        }

        $report->group_by('year, month')
            ->order_by('year')
            ->order_by('month');

        $report->limit(12);

        return $report->get()->result_array();
    }

    /**
     * Get report inbound.
     * @param $filters
     * @return array
     */
    public function getReportChartActivityGoods($filters = null)
    {
        $branchId = get_active_branch('id');
        $handlingTypeIdInbound = get_setting('default_inbound_handling');
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $filterDate = 'WHERE 1 = 1';
        if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
            if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                $filterDate .= ' AND ' . $filters['date_type'] . '>="' . sql_date_format($filters['date_from']) . '"';
            }
            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $filterDate .= ' AND ' . $filters['date_type'] . '<="' . sql_date_format($filters['date_to']) . '"';
            }
        }

        $report = $this->db->select([
            'year',
            'month',
            'SUM(IF(id_handling_type = ' . $handlingTypeIdInbound . ', 1, 0)) AS total_in',
            'SUM(IF(id_handling_type = ' . $handlingTypeIdOutbound . ', 1, 0)) AS total_out',
        ])
            ->from('bookings')
            ->join('handlings', 'handlings.id_booking = bookings.id')
            ->join("(
                SELECT work_orders.id, work_orders.no_work_order, work_orders.id_handling, YEAR(completed_at) AS year, DATE_FORMAT(completed_at, '%M') AS month 
                FROM work_orders
                LEFT JOIN safe_conducts ON safe_conducts.id = work_orders.id_safe_conduct
                INNER JOIN handlings ON handlings.id = work_orders.id_handling
                INNER JOIN bookings ON bookings.id = handlings.id_booking
                {$filterDate}
            ) AS work_orders", 'work_orders.id_handling = handlings.id')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join("(
                SELECT ref_goods.id, ref_goods.no_goods, ref_goods.name as goods_name 
                FROM ref_goods) AS ref_goods", 'ref_goods.id = work_order_goods.id_goods', 'left')
            ->join("(
                SELECT ref_people.id, ref_people.name as owner_name
                FROM ref_people) AS customer", 'customer.id = bookings.id_customer', 'left')
            ->where('year = YEAR(NOW())')
            ->group_by('year, month');

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

            if (key_exists('search', $filters) && !empty($filters['search'])) {
                $report->group_start();
                $report->like('no_booking', $filters['search'])
                    ->or_like('no_handling', $filters['search'])
                    ->or_like('no_work_order', $filters['search'])
                    ->or_like('no_goods', $filters['search'])
                    ->or_like('goods_name', $filters['search'])
                    ->or_like('owner_name', $filters['search']);
                $report->group_end();
            }
        }

        if (!empty($branchId)) {
            $report->where('id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('id_owner', $customerId);
        }

        $report->group_by('year, month')
            ->order_by('year')
            ->order_by('month');

        $report->limit(12);

        return $report->get()->result_array();
    }

    /**
     * Get report chart data.
     * @param string $type
     * @param null $filters
     * @return mixed
     */
    public function getReportChartServiceTime($type = 'INBOUND', $filters = null)
    {
        $branchId = get_active_branch('id');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $report = $this->db->select([
            'YEAR(gate_in_date) AS year',
            'CONCAT(WEEK(gate_in_date) + 1, " ", DATE_FORMAT(gate_in_date, "%M")) AS week',
            'AVG(TIME_TO_SEC(TIMEDIFF(taken_at, gate_in_date))) AS queue_duration',
            'AVG(TIME_TO_SEC(TIMEDIFF(gate_out_date, gate_in_date))) AS gate_service_time',
            'AVG(TIME_TO_SEC(TIMEDIFF(completed_at, taken_at))) AS tally_service_time',
            'AVG(TIME_TO_SEC(TIMEDIFF(security_out_date, security_in_date))) AS trucking_service_time',
            'AVG(TIME_TO_SEC(TIMEDIFF(gate_out_date, booking_date))) AS booking_service_time',
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
            ->where('ref_booking_types.category', $type)
            ->where('YEAR(gate_in_date) = YEAR(NOW())')
            ->group_by('year, week')
            ->order_by('year')
            ->order_by('week');

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
}