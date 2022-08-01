<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuctionModel extends MY_Model
{
    protected $table = 'auctions';

    const STATUS_PENDING = 'PENDING';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';

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

        $auctions = $this->db->select([
            'auctions.*',
        ])
            ->from($this->table);

        if (!empty($branchId)) {
            $auctions->where('auctions.id_branch', $branchId);
        }

        return $auctions;
    }

    /**
     * Get all auction with or without deleted records.
     * @param array $filters
     * @param bool $withTrashed
     * @return mixed
     */
    public function getAll($filters = [], $withTrashed = false)
    {
        $getAllData = empty($filters);
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;

        $columnOrder = [
            0 => "auctions.id",
            1 => "auctions.no_auction",
            2 => "auctions.no_doc",
            3 => "auctions.doc_date",
            4 => "auctions.auction_date",
            5 => "auctions.status",
            6 => "auctions.id",
        ];
        $columnSort = $columnOrder[$column];

        $branchId = get_active_branch('id');

        $this->db->start_cache();
        $auctions = $this->getBaseQuery($branchId)
            ->group_start()
            ->like('auctions.no_auction', $search)
            ->or_like('auctions.no_doc', $search)
            ->or_like('auctions.doc_date', $search)
            ->or_like('auctions.auction_date', $search)
            ->or_like('auctions.status', $search)
            ->or_like('auctions.description', $search)
            ->group_end();

        if (!$withTrashed) {
            $auctions->where($this->table . '.is_deleted', false);
        }
        $this->db->stop_cache();

        if ($getAllData) {
            $allData = $auctions->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        $page = $auctions->order_by($columnSort, $sort)->limit($length, $start);
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
     * Get auto number for auction.
     *
     * @return string
     */
    public function getAutoNumber()
    {
        $orderData = $this->db->query("
            SELECT CAST(RIGHT(no_auction, 6) AS UNSIGNED) + 1 AS order_number 
            FROM auctions
            ORDER BY CAST(RIGHT(no_auction, 6) AS UNSIGNED) DESC LIMIT 1
			");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return 'AU/' . $orderPad;
    }
}