<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BookingAssignmentModel extends MY_Model
{
    protected $table = 'booking_assignments';

    /**
     * Get base query.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery($branchId)
            ->select([
                'bookings.no_booking',
                'bookings.no_reference',
                'prv_users.name',
                'assigners.name AS assigner_name',
            ])
            ->join('bookings', 'bookings.id = booking_assignments.id_booking')
            ->join(UserModel::$tableUser, 'prv_users.id = booking_assignments.id_user')
            ->join(UserModel::$tableUser . ' AS assigners', 'assigners.id = booking_assignments.created_by')
            ->where('bookings.id_upload IS NOT NULL')
            ->where('bookings.id_upload != 0');
    }

    /**
     * Get all assignment with or without deleted records.
     *
     * @param array $filters
     * @param bool $withTrashed
     * @return mixed
     */
    public function getAll($filters = [], $withTrashed = false)
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;

        $columnOrder = [
            "booking_assignments.id",
            "bookings.no_booking",
            "prv_users.name",
            "booking_assignments.created_at",
            "booking_assignments.id",
        ];
        $columnSort = $columnOrder[$column];

        $branchId = get_active_branch('id');

        $this->db->start_cache();
        $assignments = $this->getBaseQuery($branchId)
            ->group_start()
            ->like('bookings.no_booking', trim($search))
            ->or_like('bookings.no_reference', trim($search))
            ->or_like('booking_assignments.created_at', trim($search))
            ->group_end();
        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $assignments->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        $page = $assignments->order_by($columnSort, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        foreach ($data as &$row) $row['no'] = ++$start;

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
     * Get assigned booking.
     *
     * @param $userId
     * @return array
     */
    public function getAssignedBookings($userId)
    {
        return $this->getBaseQuery()->where('prv_users.id', $userId)
            ->get()
            ->result_array();
    }

    /**
     * Get unassigned booking.
     *
     * @return array
     */
    public function getUnassignedBooking()
    {
        $branchId = null;
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $bookings =  $this->db->select([
            'bookings.id',
            'bookings.no_booking',
            'bookings.no_reference',
            'ref_people.name AS customer_name',
        ])
            ->from('bookings')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_people', 'ref_people.id = bookings.id_customer', 'left')
            ->join('booking_assignments', 'booking_assignments.id_booking = bookings.id', 'left')
            ->where('booking_assignments.id_booking IS NULL')
            ->where('ref_booking_types.category', 'INBOUND')
            ->where('bookings.id_upload IS NOT NULL')
            ->where('bookings.id_upload != 0')
            ->where('bookings.status', 'APPROVED');

        if(!empty($branchId)) {
            $bookings->where('bookings.id_branch', $branchId);
        }

        return $bookings->get()->result_array();
    }
}