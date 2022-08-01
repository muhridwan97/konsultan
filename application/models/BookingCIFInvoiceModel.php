<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BookingCIFInvoiceModel extends MY_Model
{
    protected $table = 'booking_cif_invoices';

    /**
     * Get active record query builder for all related warehouse data selection.
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch_id();
        }

        $query = $this->db->select([
            'booking_cif_invoices.*',
            'bookings.no_booking',
            'bookings.no_reference',
            'ref_people.name AS customer_name',
            'ref_booking_types.category',
            'ref_booking_types.booking_type',
            'COUNT(DISTINCT booking_cif_invoice_details.id) AS total_item',
            'SUM(booking_cif_invoice_details.quantity) AS total_item_quantity',
            'SUM(booking_cif_invoice_details.quantity * booking_cif_invoice_details.price) AS subtotal',
            'SUM(booking_cif_invoice_details.quantity * booking_cif_invoice_details.price) + discount + freight + insurance + handling + other AS total_price',
            'SUM(booking_cif_invoice_details.total_item_value) AS total_item_value',
            'SUM(booking_cif_invoice_details.quantity * booking_cif_invoice_details.price) + SUM(booking_cif_invoice_details.total_item_value) AS total_price_value',
        ])
            ->from($this->table)
            ->join('bookings', 'bookings.id = booking_cif_invoices.id_booking')
            ->join('ref_people', 'ref_people.id = bookings.id_customer')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type')
            ->join('(
                SELECT 
                    booking_cif_invoice_details.*,
                    IF(inbound_details.id IS NOT NULL, 
                        booking_cif_invoice_details.quantity / inbound_details.quantity * total_item_value_inbound, 
                        booking_cif_invoice_details.quantity / booking_cif_invoices.total_item_quantity * booking_cif_invoices.total_distributed_cost
                    ) AS total_item_value
                FROM booking_cif_invoice_details
                INNER JOIN (
                    SELECT 
                        booking_cif_invoices.*,
                        SUM(booking_cif_invoice_details.quantity) AS total_item_quantity
                    FROM (
                        SELECT booking_cif_invoices.*, (discount + freight + insurance + handling + other) AS total_distributed_cost 
                        FROM booking_cif_invoices
                    ) AS booking_cif_invoices
                    LEFT JOIN booking_cif_invoice_details ON booking_cif_invoice_details.id_booking_cif_invoice = booking_cif_invoices.id
                    GROUP BY booking_cif_invoices.id
                ) AS booking_cif_invoices ON booking_cif_invoices.id = booking_cif_invoice_details.id_booking_cif_invoice
                LEFT JOIN (                
                    SELECT 
                        booking_cif_invoice_details.id,
                        booking_cif_invoice_details.quantity,
                        (quantity / total_item_quantity * booking_cif_invoices.total_distributed_cost) AS total_item_value_inbound 
                    FROM booking_cif_invoice_details
                    INNER JOIN (
                        SELECT 
                            booking_cif_invoices.*,
                            SUM(booking_cif_invoice_details.quantity) AS total_item_quantity
                        FROM (
                            SELECT booking_cif_invoices.*, (discount + freight + insurance + handling + other) AS total_distributed_cost 
                            FROM booking_cif_invoices
                        ) AS booking_cif_invoices
                        LEFT JOIN booking_cif_invoice_details ON booking_cif_invoice_details.id_booking_cif_invoice = booking_cif_invoices.id
                        GROUP BY booking_cif_invoices.id
                    ) AS booking_cif_invoices ON booking_cif_invoices.id = booking_cif_invoice_details.id_booking_cif_invoice                
                ) AS inbound_details ON inbound_details.id = booking_cif_invoice_details.id_booking_cif_invoice_detail
            ) AS booking_cif_invoice_details', 'booking_cif_invoice_details.id_booking_cif_invoice = booking_cif_invoices.id', 'left')
            ->group_by('booking_cif_invoices.id, bookings.no_booking, ref_people.name, ref_booking_types.category');

        if (!empty($branchId)) {
            $query->where('bookings.id_branch', $branchId);
        }

        return $query;
    }

    /**
     * Get all booking cif with or without deleted records.
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
        $branch = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $this->db->start_cache();
        $baseQuery = $this->getBaseQuery($branch)
            ->having('no_booking LIKE', '%' . $search . '%')
            ->or_having('no_reference LIKE', '%' . $search . '%')
            ->or_having('category LIKE', '%' . $search . '%')
            ->or_having('booking_type LIKE', '%' . $search . '%')
            ->or_having('total_item LIKE', '%' . $search . '%')
            ->or_having('total_item_quantity LIKE', '%' . $search . '%')
            ->or_having('subtotal LIKE', '%' . $search . '%')
            ->or_having('total_price LIKE', '%' . $search . '%');

        if (!$withTrashed) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }
        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $total = $this->db->count_all_results();

        if ($column == 'no') $column = 'booking_cif_invoices.id';
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
     * Get available bookings.
     *
     * @param string $search
     * @param null $page
     * @return array
     */
    public function getAvailableBooking($search = '', $page = null)
    {
        $branchId = get_active_branch_id();

        $this->db->start_cache();

        $query = $this->db->select([
            'bookings.id',
            'bookings.id_booking',
            'ref_people.name AS customer_name',
            'bookings.no_booking',
            'bookings.no_reference',
            'booking_inbounds.no_reference AS no_reference_inbound',
            'ref_booking_types.category',
            'ref_booking_types.booking_type',
            'booking_cif_invoices.id AS id_booking_cif_invoice'
        ])
            ->from('bookings')
            ->join('bookings AS booking_inbounds', 'booking_inbounds.id = bookings.id_booking', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('(SELECT * FROM booking_cif_invoices WHERE is_deleted = false) AS booking_cif_invoices', 'booking_cif_invoices.id_booking = bookings.id', 'left')
            ->join('ref_people', 'ref_people.id = bookings.id_customer', 'left')
            ->where('bookings.is_deleted', false)
            ->having('id_booking_cif_invoice IS NULL');

        if (!empty($branchId)) {
            $query->where('bookings.id_branch', $branchId);
        }

        if (!empty($search)) {
            $query->like('bookings.no_booking', trim($search))
                ->or_like('ref_people.name', trim($search))
                ->or_like('bookings.no_reference', trim($search));
        }

        $this->db->stop_cache();

        if (!empty($page) || $page != 0) {
            $bookingTotal = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$query->get_compiled_select()}) AS CI_count_all_results")->row_array()['numrows'];
            $bookingPage = $query->limit(10, 10 * ($page - 1));
            $bookingData = $bookingPage->get()->result_array();

            return [
                'results' => $bookingData,
                'total_count' => $bookingTotal
            ];
        }

        $bookingData = $query->get()->result_array();

        $this->db->flush_cache();

        return $bookingData;
    }

    /**
     * Get stock booking data.
     * @param array $filters
     * @return array
     */
    public function getAvailableGoodsStock($filters = [])
    {
        $query = $this->db->select([
            'inbounds.id AS id_booking_cif_invoice_detail',
            'inbounds.goods_name',
            'quantity',
            'weight',
            'gross_weight',
            'volume',
            '(quantity - IFNULL(taken_quantity, 0)) AS stock_quantity',
            '((quantity - IFNULL(taken_quantity, 0)) * inbounds.weight) / quantity AS stock_weight',
            '((quantity - IFNULL(taken_quantity, 0)) * inbounds.gross_weight) / quantity AS stock_gross_weight',
            '((quantity - IFNULL(taken_quantity, 0)) * inbounds.volume) / quantity AS stock_volume',
            'price',
            '(quantity * price) AS total_price',
            '(quantity / total_item_quantity * booking_cif_invoices.total_distributed_cost) AS total_item_value',
        ])
            ->from('(
                SELECT 
                    booking_cif_invoices.*,
                    SUM(booking_cif_invoice_details.quantity) AS total_item_quantity
                FROM (
                    SELECT booking_cif_invoices.*, (discount + freight + insurance + handling + other) AS total_distributed_cost 
                    FROM booking_cif_invoices
                ) AS booking_cif_invoices
                LEFT JOIN booking_cif_invoice_details ON booking_cif_invoice_details.id_booking_cif_invoice = booking_cif_invoices.id
                GROUP BY booking_cif_invoices.id
            ) AS booking_cif_invoices')
            ->join('booking_cif_invoice_details AS inbounds', 'inbounds.id_booking_cif_invoice = booking_cif_invoices.id')
            ->join('(
                SELECT id_booking_cif_invoice_detail, SUM(quantity) AS taken_quantity 
                FROM booking_cif_invoice_details
                WHERE id_booking_cif_invoice_detail IS NOT NULL
                GROUP BY id_booking_cif_invoice_detail
            ) AS outbounds', 'outbounds.id_booking_cif_invoice_detail = inbounds.id', 'left');

        if (!empty($filters)) {
            if (key_exists('booking', $filters) && !empty($filters['booking'])) {
                if (is_array($filters['booking'])) {
                    $query->where_in('booking_cif_invoices.id_booking', $filters['booking']);
                } else {
                    $query->where('booking_cif_invoices.id_booking', $filters['booking']);
                }
            }
        }

        return $query->get()->result_array();
    }

    /**
     * @return mixed
     */
    public function getCurrencies()
    {
        $client = new GuzzleHttp\Client([
            'base_uri' => 'https://api.exchangeratesapi.io'
        ]);
        try {
            $response = $client->request('GET', '/latest', [
                'query' => ['base' => 'USD'],
            ]);
            $result = json_decode($response->getBody(), true);
            return get_if_exist($result, 'rates', []);
        } catch (\GuzzleHttp\Exception\GuzzleException $e) {
            return [];
        }
    }
}