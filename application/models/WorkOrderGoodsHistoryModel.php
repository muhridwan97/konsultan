<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class WorkOrderGoodsHistoryModel
 * @property WorkOrderGoodsModel $WorkOrderGoodsModel
 */
class WorkOrderGoodsHistoryModel extends WorkOrderGoodsModel
{
    protected $table = 'work_order_goods_histories';

    /**
     * WorkOrderGoodsHistoryModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get basic query work order.
     */
    public function getWorkOrderGoodsBaseQuery()
    {
        $workOrderGoods = $this->db
            ->select([
                $this->table . '.*',
                '(' . $this->table . '.quantity * ' . $this->table . '.unit_weight) AS total_weight',
                '(' . $this->table . '.quantity * ' . $this->table . '.unit_gross_weight) AS total_gross_weight',
                '(' . $this->table . '.quantity * ' . $this->table . '.unit_volume) AS total_volume',
                'work_orders.no_work_order',
                'work_orders.completed_at',
                'handlings.no_handling',
                'bookings.id AS id_booking',
                'bookings.no_booking',
                'bookings.no_reference',
                'ref_booking_types.booking_type',
                'safe_conducts.no_safe_conduct',
                'safe_conducts.no_police',
                'ref_containers.no_container',
                'ref_goods.name AS goods_name',
                'ref_goods.no_goods',
                'ref_goods.whey_number',
                'ref_units.unit',
                'ref_positions.position',
                'ref_people.name AS owner_name',
                'doc_invoice.no_document AS invoice_number',
                '"" AS id_position_blocks',
                '"" AS position_blocks',
                'ref_warehouses.type as type_warehouse'
            ])
            ->from($this->table)
            ->join('work_order_histories AS work_orders', 'work_orders.id = ' . $this->table . '.id_work_order')
            ->join('work_order_container_histories AS work_order_containers', $this->table . '.id_work_order_container = work_order_containers.id', 'left')
            //->join('work_order_goods_positions', 'work_order_goods_positions.id_work_order_goods = ' . $this->table . '.id', 'left')
            //->join('ref_position_blocks', 'ref_position_blocks.id = work_order_goods_positions.id_position_block', 'left')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container', 'left')
            ->join('ref_positions', $this->table . '.id_position = ref_positions.id', 'left')
            ->join('ref_goods', $this->table . '.id_goods = ref_goods.id', 'left')
            ->join('ref_units', $this->table . '.id_unit = ref_units.id', 'left')
            ->join('ref_people', 'ref_people.id = ' . $this->table . '.id_owner', 'left')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling', 'left')
            ->join('bookings', 'bookings.id = handlings.id_booking', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('(
                SELECT id_upload, no_document FROM upload_documents
                INNER JOIN ref_document_types ON upload_documents.id_document_type = ref_document_types.id
                WHERE ref_document_types.document_type = "Invoice" AND upload_documents.is_deleted = FALSE
                ) AS doc_invoice', 'doc_invoice.id_upload = bookings.id_upload', 'left')
            ->join('ref_warehouses', 'ref_positions.id_warehouse = ref_warehouses.id', 'left')
            ->group_by($this->table . '.id, doc_invoice.no_document');

        return $workOrderGoods;
    }
}