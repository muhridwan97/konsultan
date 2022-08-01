<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WarehouseReceiptDetailModel extends MY_Model
{
    protected $table = 'warehouse_receipt_details';

    /**
     * Get active record query builder for all related warehouse data selection.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        if(empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $warehouseReceiptDetails = $this->db->select([
            'warehouse_receipt_details.*',
            'warehouse_receipts.no_warehouse_receipt',
            'ref_people.name AS customer_name',
            'ref_goods.no_goods AS no_goods',
            'ref_goods.name AS goods_name',
            'ref_units.unit',
            'ref_positions.position',
        ])
            ->from($this->table)
            ->join('warehouse_receipts', 'warehouse_receipts.id = warehouse_receipt_details.id_warehouse_receipt', 'left')
            ->join('ref_people', 'ref_people.id = warehouse_receipts.id_customer', 'left')
            ->join('ref_goods', 'warehouse_receipt_details.id_goods = ref_goods.id', 'left')
            ->join('ref_positions', 'warehouse_receipt_details.id_position = ref_positions.id', 'left')
            ->join('ref_units', 'warehouse_receipt_details.id_unit = ref_units.id', 'left');

        if (!empty($branchId)) {
            $warehouseReceiptDetails->where('warehouse_receipts.id_branch', $branchId);
        }

        return $warehouseReceiptDetails;
    }

    /**
     * Get summary detail by warehouse receipt.
     *
     * @param $warehouseReceiptId
     * @param bool $withTrashed
     * @return mixed
     */
    public function getSummaryByWarehouseReceipt($warehouseReceiptId, $withTrashed = false)
    {
        $warehouseReceiptDetails = $this->db
            ->select([
                'ref_branches.address', 'duration',
                'ref_goods.name AS goods_name', 'ref_goods.type_goods', 'ref_units.unit AS unit',
	            'SUM(quantity) AS quantity', 'SUM(tonnage) AS tonnage', 'SUM(tonnage_gross) AS tonnage_gross',
                'SUM(volume) AS volume', 'GROUP_CONCAT(no_pallet) AS no_pallets', 'GROUP_CONCAT(DISTINCT DATE(inbound_date)) AS inbound_dates'
            ])
            ->from('warehouse_receipt_details')
            ->join('warehouse_receipts', 'warehouse_receipts.id = warehouse_receipt_details.id_warehouse_receipt', 'left')
            ->join('ref_branches', 'ref_branches.id = warehouse_receipts.id_branch', 'left')
            ->join('ref_goods', 'ref_goods.id = warehouse_receipt_details.id_goods', 'left')
            ->join('ref_units', 'ref_units.id = warehouse_receipt_details.id_unit', 'left')
            ->where('warehouse_receipt_details.id_warehouse_receipt', $warehouseReceiptId)
            ->group_by('ref_branches.address, duration, id_warehouse_receipt, ref_goods.name');

        if(!$withTrashed) {
            $warehouseReceiptDetails->where('warehouse_receipts.is_deleted', false);
        }

        return $warehouseReceiptDetails->get()->result_array();
    }

}