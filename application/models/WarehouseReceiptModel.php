<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WarehouseReceiptModel extends MY_Model
{
    protected $table = 'warehouse_receipts';

    const STATUS_PENDING = 'PENDING';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_EXPIRED = 'EXPIRED';

    const DURATIONS = [
        'MAKSIMAL SATU TAHUN' => 'MAXIMUM ONE YEAR',
        'MAKSIMAL DUA TAHUN' => 'MAXIMUM TWO YEARS',
        'MAKSIMAL TIGA TAHUN' => 'MAXIMUM THREE YEARS',
        'MAKSIMAL EMPAT TAHUN' => 'MAXIMUM FOUR YEARS',
        'MAKSIMAL LIMA TAHUN' => 'MAXIMUM FIVE YEARS',
    ];

    /**
     * Get active record query builder for all related warehouse data selection.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $warehouseReceipts = $this->db->select([
            'warehouse_receipts.*',
            'ref_branches.branch',
            'ref_branches.address AS location',
            'ref_people.name AS customer_name',
            'validators.username AS validator_name',
            'warehouse_receipt_details.total_tonnage'
        ])
            ->from($this->table)
            ->join('ref_branches', 'ref_branches.id = warehouse_receipts.id_branch', 'left')
            ->join('ref_people', 'ref_people.id = warehouse_receipts.id_customer', 'left')
            ->join(UserModel::$tableUser . ' AS validators', 'validators.id = warehouse_receipts.validated_by', 'left')
            ->join('(
                SELECT id_warehouse_receipt, SUM(tonnage) AS total_tonnage 
                FROM warehouse_receipt_details
                GROUP BY id_warehouse_receipt) AS warehouse_receipt_details',
                'warehouse_receipt_details.id_warehouse_receipt = warehouse_receipts.id', 'left');

        if (!empty($branchId)) {
            $warehouseReceipts->where('warehouse_receipts.id_branch', $branchId);
        }

        return $warehouseReceipts;
    }

    /**
     * Get all warehouse receipt with or without deleted records.
     *
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
            0 => "warehouse_receipts.id",
            1 => "warehouse_receipts.no_warehouse_receipt",
            2 => "warehouse_receipts.no_batch",
            3 => "ref_people.name",
            4 => "warehouse_receipts.issuance_date",
            5 => "warehouse_receipts.duration",
            6 => "warehouse_receipt_details.total_tonnage",
            7 => "warehouse_receipts.status",
            8 => "warehouse_receipts.id",
        ];
        $columnSort = $columnOrder[$column];

        $branchId = get_active_branch('id');

        $this->db->start_cache();
        $warehouseReceipts = $this->getBaseQuery($branchId)
            ->group_start()
            ->like('warehouse_receipts.no_warehouse_receipt', $search)
            ->or_like('warehouse_receipts.no_batch', $search)
            ->or_like('warehouse_receipts.issuance_date', $search)
            ->or_like('warehouse_receipts.duration', $search)
            ->or_like('warehouse_receipts.status', $search)
            ->or_like('warehouse_receipt_details.total_tonnage', $search)
            ->or_like('warehouse_receipts.description', $search)
            ->or_like('ref_people.name', $search)
            ->group_end();

        if (!$withTrashed) {
            $warehouseReceipts->where($this->table . '.is_deleted', false);
        }

        $this->db->stop_cache();

        if ($getAllData) {
            $allData = $warehouseReceipts->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        $page = $warehouseReceipts->order_by($columnSort, $sort)->limit($length, $start);
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
     * Get auto number for warehouse receipt.
     *
     * @return string
     */
    public function getAutoNumberWarehouseReceipt()
    {
        $branchId = get_active_branch('id');
        if (empty($branchId)) {
            $branchId = '0';
        }

        $orderData = $this->db->query("
            SELECT IFNULL(CAST(RIGHT(no_warehouse_receipt, 5) AS UNSIGNED), 0) + 1 AS order_number 
            FROM warehouse_receipts 
            WHERE SUBSTR(no_warehouse_receipt, 14, 2) = MONTH(NOW()) 
			AND SUBSTR(no_warehouse_receipt, 12, 2) = DATE_FORMAT(NOW(), '%y')
            ORDER BY SUBSTR(no_warehouse_receipt FROM 4) DESC LIMIT 1
        ");
        $orderPad = '00001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 5, '0', STR_PAD_LEFT);
        }
        $branchPad = str_pad($branchId, 2, '0', STR_PAD_LEFT);
        return 'TCI.' . $branchPad . '.WRC.' . date('y') . date('m') . $orderPad;
    }

    /**
     * Get next batch number.
     *
     * @return int
     */
    public function getNextBatch()
    {
        $nextBatch = $this->db->select('(IFNULL(MAX(no_batch), 0) + 1) AS batch')
            ->from($this->table)
            ->order_by('no_batch', 'desc')
            ->get()->row_array();

        if ($nextBatch) {
            return $nextBatch['batch'];
        }

        return 1;
    }
}