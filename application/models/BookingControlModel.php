<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BookingControlModel extends MY_Model
{
    protected $table = 'bookings';

    const STATUS_PENDING = 'PENDING';
    const STATUS_CANCELED = 'CANCELED';
    const STATUS_DRAFT = 'DRAFT';
    const STATUS_DONE = 'DONE';
    const STATUS_CLEAR = 'CLEAR';

    /**
     * Get active record query builder for all related warehouse data selection.
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $baseQuery = $this->db->select([
            'bookings.id',
            'bookings.no_booking',
            'bookings.no_reference',
            'bookings.booking_date',
            'ref_customers.name AS customer_name',
            'booking_in.id AS id_booking_in',
            'booking_in.no_booking AS no_booking_in',
            'booking_in.no_reference AS no_reference_in',
            'GROUP_CONCAT(booking_out.no_booking) AS no_booking_out',
            'GROUP_CONCAT(booking_out.no_reference) AS no_reference_out',
            'ref_booking_types.booking_type',
            'ref_booking_types.category',
            'bookings.status',
            'bookings.status_control',
        ])
            ->from($this->table)
            ->join('bookings AS booking_in', 'bookings.id_booking = booking_in.id', 'left')
            ->join('bookings AS booking_out', 'booking_out.id_booking = bookings.id', 'left')
            ->join('ref_booking_types', 'bookings.id_booking_type = ref_booking_types.id', 'left')
            ->join('ref_people AS ref_customers', 'bookings.id_customer = ref_customers.id', 'left')
            ->group_by('bookings.id');;

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        return $baseQuery;
    }

    /**
     * Get all booking with or without deleted records.
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
            "bookings.id",
            "ref_customers.name",
            "ref_booking_types.booking_type",
            "bookings.no_booking",
            "bookings.booking_date",
            "bookings.status",
            "bookings.id",
        ];
        $columnSort = $columnOrder[$column];

        $branchId = get_active_branch('id');

        $this->db->start_cache();
        $query = $this->getBaseQuery($branchId)
            ->group_start()
            ->like('bookings.no_booking', trim($search))
            ->or_like('bookings.no_reference', trim($search))
            ->or_like('bookings.booking_date', trim($search))
            ->or_like('bookings.status', trim($search))
            ->or_like('bookings.status_control', trim($search))
            ->or_like('booking_in.no_booking', trim($search))
            ->or_like('booking_in.no_reference', trim($search))
            ->or_like('booking_out.no_booking', trim($search))
            ->or_like('booking_out.no_reference', trim($search))
            ->or_like('ref_booking_types.booking_type', trim($search))
            ->or_like('ref_booking_types.category', trim($search))
            ->group_end();

        if (!$withTrashed) {
            $query->where($this->table . '.is_deleted', false);
        }

        if (!empty($filters)) {
            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $query->where_in('bookings.id_customer', $filters['owner']);
                } else {
                    $query->where('bookings.id_customer', $filters['owner']);
                }
            }
            if (key_exists('status', $filters) && !empty($filters['status'])) {
                if (in_array('OUTSTANDING', $filters['status'])) {
                    $query->group_start();
                    $query->where_in('bookings.status_control', ['PENDING', 'DRAFT', 'DONE']);
                    foreach ($filters['status'] as $status) {
                        if ($status != 'OUTSTANDING') {
                            $query->or_where('bookings.status_control', $status);
                        }
                    }
                    $query->group_end();
                } else {
                    $query->where_in('bookings.status_control', $filters['status']);
                }
            }
            if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                $query->where('bookings.booking_date>=', sql_date_format($filters['date_from'], false));
            }
            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $query->where('bookings.booking_date<=', sql_date_format($filters['date_to'], false));
            }
        }
        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $query->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        $page = $query->order_by($columnSort, $sort)->limit($length, $start);
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