<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DiscrepancyHandoverModel extends MY_Model
{
    protected $table = 'discrepancy_handovers';

    const STATUS_PENDING = 'PENDING';
    const STATUS_UPLOADED = 'UPLOADED';
    const STATUS_CONFIRMED = 'CONFIRMED';
    const STATUS_EXPLAINED = 'EXPLAINED';
    const STATUS_CANCELED = 'CANCELED';
    const STATUS_NOT_USE = 'NOT USE';
    const STATUS_IN_USE = 'IN USE';
    const STATUS_DOCUMENT = 'DOCUMENT';
    const STATUS_PHYSICAL = 'PHYSICAL';

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

        $baseQuery = parent::getBaseQuery($branchId)
            ->select([
                'bookings.no_reference',
                'bookings.id_branch',
                'ref_branches.branch',
                'bookings.id_customer',
                'bookings.id_upload',
                'ref_people.name AS customer_name',
                '(
                    SELECT COUNT(*) FROM discrepancy_handover_goods 
                    WHERE id_discrepancy_handover = discrepancy_handovers.id
                ) AS total_discrepancy_item'
            ])
            ->join('bookings', 'bookings.id = discrepancy_handovers.id_booking')
            ->join('ref_people', 'ref_people.id = bookings.id_customer')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        return $baseQuery;
    }

    /**
     * Generate auto number.
     *
     * @return string
     */
    public function getAutoNumber()
    {
        $orderData = $this->db->query("
            SELECT CAST(RIGHT(no_discrepancy, 6) AS UNSIGNED) + 1 AS order_number 
            FROM discrepancy_handovers 
            WHERE MONTH(created_at) = MONTH(NOW()) 
              AND YEAR(created_at) = YEAR(NOW())
            ORDER BY CAST(RIGHT(no_discrepancy, 6) AS UNSIGNED) DESC LIMIT 1
        ");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return 'DC/' . date('y') . '/' . date('m') . '/' . $orderPad;
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

        if (!empty($search)) {
            $baseQuery->group_start()
                ->like('bookings.no_booking', $search)
                ->or_like('bookings.no_reference', $search)
                ->or_like('ref_people.name', $search)
                ->group_end();
        }

        if (!$withTrashed) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->where('bookings.id_customer', $filters['customer']);
        }

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $baseQuery->where('DATE(discrepancy_handovers.created_at)>=', format_date($filters['date_from']));
        }

        if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
            $baseQuery->where('DATE(discrepancy_handovers.created_at)<=', format_date($filters['date_to']));
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        if ($column == 'no') $column = $this->table . '.id';
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
