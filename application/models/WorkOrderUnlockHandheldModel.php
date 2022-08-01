<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkOrderUnlockHandheldModel extends MY_Model
{
    protected $table = 'work_order_unlock_handhelds';

    const STATUS_LOCKED = 'LOCKED';
    const STATUS_UNLOCKED = 'UNLOCKED';

    /**
     * WorkOrderHandheldUnlockModel constructor.
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

        $baseQuery = parent::getBaseQuery()
            ->select([
                'ref_branches.branch',
                'bookings.id AS id_booking',
                'bookings.no_reference',
                'ref_people.id AS id_customer',
                'ref_people.name AS customer_name',
                'work_orders.no_work_order',
                'prv_users.name AS creator_name',
                'IF(CURDATE() > unlocked_until, "LOCKED", "UNLOCKED") AS status'
            ])
            ->join('work_orders', 'work_orders.id = work_order_unlock_handhelds.id_work_order')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('ref_people', 'ref_people.id = bookings.id_customer')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
            ->join(UserModel::$tableUser, 'prv_users.id = work_order_unlock_handhelds.created_by', 'left');

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

        $this->db->start_cache();

        $baseQuery = $this->getBaseQuery($branchId);

        if (!$withTrashed) {
            $baseQuery->where('work_orders.is_deleted', false);
        }

        if (!empty($search)) {
            $baseQuery
                ->group_start()
                ->like('work_orders.no_work_order', $search)
                ->or_like('ref_people.name', $search)
                ->or_like('bookings.no_booking', $search)
                ->or_like('bookings.no_reference', $search)
                ->group_end();
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

        if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
            if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                $baseQuery->having('DATE(' . $filters['date_type'] . ')>=', format_date($filters['date_from']));
            }

            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $baseQuery->having('DATE(' . $filters['date_type'] . ')<=', format_date($filters['date_to']));
            }
        }

        $this->db->stop_cache();

        if ($start < 0) {
            if ($column == 'no') $column = 'work_order_unlock_handhelds.id';
            $allData = $baseQuery->order_by($column, $sort)->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $query = $this->db->get_compiled_select();
        $total = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$query}) AS CI_count_all_results")->row_array()['numrows'];

        if ($column == 'no') $column = 'work_order_unlock_handhelds.id';
        $page = $baseQuery->order_by($column, $sort)->limit($length, $start);
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
}
