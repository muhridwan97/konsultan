<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HandlingGoodsModel extends MY_Model
{
    protected $table = 'handling_goods';

    /**
     * HandlingGoodsModel constructor.
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
        return $this->db
            ->select([
                'handling_goods.*',
                'handlings.id_booking',
                'bookings.no_reference',
                'booking_refs.no_reference AS no_booking_reference',
                'ref_goods.no_goods',
                'ref_goods.name AS goods_name',
                'ref_goods.whey_number',
                'handling_goods.unit_weight',
                '(handling_goods.quantity * handling_goods.unit_weight) AS total_weight',
                'handling_goods.unit_gross_weight',
                '(handling_goods.quantity * handling_goods.unit_gross_weight) AS total_gross_weight',
                'handling_goods.unit_length',
                'handling_goods.unit_width',
                'handling_goods.unit_height',
                'handling_goods.unit_volume',
                '(handling_goods.quantity * handling_goods.unit_volume) AS total_volume',
                'ref_units.unit',
                'ref_positions.position',
                'ref_people.name AS owner_name',
                'GROUP_CONCAT(DISTINCT handling_goods_positions.id_position_block) AS id_position_blocks',
                'GROUP_CONCAT(DISTINCT ref_position_blocks.position_block) AS position_blocks'
            ])
            ->from($this->table)
            ->join('handlings', 'handlings.id = handling_goods.id_handling', 'left')
            ->join('bookings', 'bookings.id = handlings.id_booking', 'left')
            ->join('bookings AS booking_refs', 'booking_refs.id = handling_goods.id_booking_reference', 'left')
            ->join('handling_goods_positions', 'handling_goods_positions.id_handling_goods = handling_goods.id', 'left')
            ->join('ref_position_blocks', 'ref_position_blocks.id = handling_goods_positions.id_position_block', 'left')
            ->join('ref_goods', 'handling_goods.id_goods = ref_goods.id', 'left')
            ->join('ref_units', 'handling_goods.id_unit = ref_units.id', 'left')
            ->join('ref_positions', 'handling_goods.id_position = ref_positions.id', 'left')
            ->join('ref_people', 'handling_goods.id_owner = ref_people.id', 'left')
            ->group_by('handling_goods.id');
    }
    
    /**
     * Get single data handling goods.
     * @param $id
     * @return mixed
     */
    public function getHandlingGoodsById($id)
    {
        $goods = $this->getBaseQuery()->where('handling_goods.id', $id);
        
        return $goods->get()->row_array();
    }
    
    /**
     * Get handling goods by handling id.
     * @param integer $handlingId
     * @param bool $nonContainer
     * @return array
     */
    public function getHandlingGoodsByHandling($handlingId, $nonContainer = false)
    {
        $goods = $this->getBaseQuery()->where('handling_goods.id_handling', $handlingId);

        if($nonContainer) {
            $goods->where('handling_goods.id_handling_container IS NULL');
        }

        return $goods->get()->result_array();
    }

    /**
     * Get handling goods by handling container
     * @param $handlingContainerId
     * @return mixed
     */
    public function getHandlingGoodsByHandlingContainer($handlingContainerId)
    {
        $goods = $this->getBaseQuery()->where('handling_goods.id_handling_container', $handlingContainerId);

        return $goods->get()->result_array();
    }

    /**
     * Insert single or batch handling goods.
     * @param $data
     * @return bool
     */
    public function createHandlingGoods($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update or replace handling goods.
     * @param $data
     * @param $id
     * @return bool
     */
    public function updateHandlingGoods($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete handling goods by handling id.
     * @param $handlingId
     * @return mixed
     */
    public function deleteHandlingGoodsByHandling($handlingId)
    {
        return $this->db->delete($this->table, ['id_handling' => $handlingId]);
    }
}
