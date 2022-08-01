<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportServiceTimeModel extends MY_Model
{

    /**
     * Get stock summary container.
     *
     * @param array $filters
     * @return array|int
     */
    public function getEsealTracking($filters = [])
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

        $report = $this->db
            ->select([
                'customers.id AS id_customer',
                'customers.name AS customer_name',
                'bookings.id AS id_booking',
                'bookings.no_reference',
                'safe_conducts.id AS id_safe_conduct',
                'safe_conducts.no_safe_conduct',
                'ref_eseals.no_eseal',
                'ref_eseals.id_device',
                'ref_eseals.device_name',
                'safe_conducts.driver',
                'safe_conducts.no_police',
                'safe_conducts.expedition_type',
                'safe_conducts.security_in_date AS security_start',
                'safe_conducts.security_out_date AS security_stop',
                'GROUP_CONCAT(DISTINCT sf_ref_containers.no_container SEPARATOR ", ") AS containers_load',
                'GROUP_CONCAT(DISTINCT sf_ref_goods.name SEPARATOR ", ") AS goods_load',
                'source_warehouses.name AS source_warehouse',
                'source_warehouses.address AS source_warehouse_address',
                'ref_branches.branch AS destination',
                'ref_branches.address AS destination_address',
                'safe_conducts.status_tracking',
                'safe_conduct_routes.total_route',
                'safe_conduct_routes.total_distance',
            ])
            ->from('safe_conducts')
            ->join('bookings', 'bookings.id = safe_conducts.id_booking', 'left')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->join('ref_eseals', 'ref_eseals.id = safe_conducts.id_eseal', 'left')
            ->join('ref_people AS source_warehouses', 'source_warehouses.id = safe_conducts.id_source_warehouse', 'left')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->join('safe_conduct_containers AS sf_containers', 'safe_conducts.id = sf_containers.id_safe_conduct', 'left')
            ->join('ref_containers AS sf_ref_containers', 'sf_ref_containers.id = sf_containers.id_container', 'left')
            ->join('safe_conduct_goods AS sf_goods', 'safe_conducts.id = sf_goods.id_safe_conduct', 'left')
            ->join('ref_goods AS sf_ref_goods', 'sf_ref_goods.id = sf_goods.id_goods', 'left')
            ->join("(
                SELECT 
                    id_safe_conduct, 
                    COUNT(id) AS total_route, 
                    SUM(distance) AS total_distance
                FROM safe_conduct_routes
                GROUP BY id_safe_conduct
            ) AS safe_conduct_routes", 'safe_conduct_routes.id_safe_conduct = safe_conducts.id', 'left')
            ->where([
                'bookings.is_deleted' => false,
                'safe_conducts.is_deleted' => false,
                'safe_conducts.security_in_date IS NOT NULL' => null,
                'safe_conducts.security_out_date IS NOT NULL' => null,
                'safe_conducts.type' => 'INBOUND',
            ])
            ->group_by('safe_conducts.id');

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('customers.id', $customerId);
        }

        if (!empty($search)) {
            $report
                ->group_start()
                ->or_like('bookings.no_reference', $search)
                ->or_like('safe_conducts.no_safe_conduct', $search)
                ->or_like('customers.name', $search)
                ->or_like('ref_eseals.no_eseal', $search)
                ->or_like('ref_eseals.device_name', $search)
                ->or_like('safe_conducts.driver', $search)
                ->or_like('safe_conducts.no_police', $search)
                ->or_like('safe_conducts.expedition_type', $search)
                ->or_like('source_warehouses.name', $search)
                ->or_like('source_warehouses.address', $search)
                ->or_like('ref_branches.branch', $search)
                ->or_like('ref_branches.address', $search)
                ->or_like('safe_conducts.status_tracking', $search)
                ->group_end();
        }

        if (!empty($filters)) {
            if (key_exists('booking', $filters) && !empty($filters['booking'])) {
                $report->where_in('bookings.id', $filters['booking']);
            }

            if (key_exists('customer', $filters) && !empty($filters['customer'])) {
                $report->where_in('customers.id', $filters['customer']);
            }

            if (key_exists('status_tracking', $filters) && !empty($filters['status_tracking'])) {
                $report->where_in('safe_conducts.status_tracking', $filters['status_tracking']);
            }

            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
                if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                    $report->where('DATE(' . $filters['date_type'] . ')>=', format_date($filters['date_from']));
                }

                if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                    $report->where('DATE(' . $filters['date_type'] . ')<=', format_date($filters['date_to']));
                }
            }
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $report->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $finalQuery = $this->db->get_compiled_select();
        $total = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$finalQuery}) AS CI_count_all_results")->row_array()['numrows'];

        if($column == 'no') $column = 'safe_conducts.id';
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

}
