<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransporterEntryPermitGoodsModel extends CI_Model
{
    private $table = 'transporter_entry_permit_goods';

    /**
     * TEPGoodsModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get TEP goods query.
     * @return mixed
     */
    private function getBaseQuery()
    {
        $getBaseQuery = $this->db
            ->select([
                'transporter_entry_permit_goods.*',
                'delivery_orders.id AS id_delivery_order',
                'delivery_orders.no_delivery_order',
                'ref_goods.no_goods',
                'ref_goods.whey_number',
                'ref_goods.name AS goods_name',
                'ref_units.unit',
                'ref_positions.position',
                'ref_containers.no_container',
                'ref_containers.size',
                'ref_containers.type'
            ])
            ->from($this->table)
            ->join('delivery_order_goods', 'transporter_entry_permit_goods.id_delivery_order_goods = delivery_order_goods.id', 'left')
            ->join('delivery_orders', 'delivery_order_goods.id_delivery_order = delivery_orders.id', 'left')
            ->join('ref_goods', 'transporter_entry_permit_goods.id_goods = ref_goods.id', 'left')
            ->join('ref_units', 'transporter_entry_permit_goods.id_unit = ref_units.id', 'left')
            ->join('ref_positions', 'transporter_entry_permit_goods.id_position = ref_positions.id', 'left')
            ->join('transporter_entry_permit_containers', 'transporter_entry_permit_containers.id = transporter_entry_permit_goods.id_tep_container', 'left')
            ->join('ref_containers', 'ref_containers.id = transporter_entry_permit_containers.id_container', 'left');

        return $getBaseQuery;
    }

    /**
     * Get TEP goods by TEP Id.
     * @param integer $TEP
     * @param bool $nonContainer
     * @return array
     */
    public function getTEPGoodsByTEP($TEP, $nonContainer = false)
    {
        $goods = $this->getBaseQuery()
            ->where('transporter_entry_permit_goods.id_tep', $TEP);

        if($nonContainer) {
            $goods->where('transporter_entry_permit_goods.id_tep_container IS NULL');
        }

        return $goods->get()->result_array();
    }

    /**
     * Get TEP goods by TEP id.
     * @param integer $tepContainerId
     * @return array
     */
    public function getTepGoodsByTepContainer($tepContainerId)
    {
        $goods = $this->getBaseQuery()
            ->where('transporter_entry_permit_goods.id_tep_container', $tepContainerId);

        return $goods->get()->result_array();
    }

    /**
     * Insert single or batch tep goods.
     * @param $data
     */
    public function createTepGoods($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update or replace tep goods.
     * @param $data
     * @param $id
     */
    public function updateTepGoods($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * delete TEP goods.
     * @param $id
     */
    public function deleteTepGoods($id)
    {
        return $this->db->delete($this->table, $id);
    }

    /**
     * Delete tep goods by tep id.
     * @param $tepId
     * @return mixed
     */
    public function deleteTepByTep($tepId)
    {
        return $this->db->delete($this->table, ['id_tep' => $tepId]);
    }
}
