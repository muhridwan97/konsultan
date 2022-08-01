<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SafeConductGoodsModel extends MY_Model
{
    protected $table = 'safe_conduct_goods';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        $bookings = $this->db
            ->select([
                'safe_conduct_goods.*',
                'safe_conducts.no_safe_conduct',
                'safe_conducts.id_booking',
                'bookings.no_reference',
                'booking_refs.no_reference AS no_booking_reference',
                'ref_goods.no_goods',
                'ref_goods.whey_number',
                'ref_goods.name AS goods_name',
                'safe_conduct_goods.unit_weight',
                '(safe_conduct_goods.quantity * safe_conduct_goods.unit_weight) AS total_weight',
                'safe_conduct_goods.unit_gross_weight',
                '(safe_conduct_goods.quantity * safe_conduct_goods.unit_gross_weight) AS total_gross_weight',
                'safe_conduct_goods.unit_length',
                'safe_conduct_goods.unit_width',
                'safe_conduct_goods.unit_height',
                'safe_conduct_goods.unit_volume',
                '(safe_conduct_goods.quantity * safe_conduct_goods.unit_volume) AS total_volume',
                'ref_units.unit',
                'ref_positions.position',
                'ref_containers.no_container',
                'ref_containers.size',
                'ref_containers.type'
            ])
            ->from($this->table)
            ->join('safe_conducts', 'safe_conducts.id = safe_conduct_goods.id_safe_conduct', 'left')
            ->join('bookings', 'bookings.id = safe_conducts.id_booking', 'left')
            ->join('bookings AS booking_refs', 'booking_refs.id = safe_conduct_goods.id_booking_reference', 'left')
            ->join('ref_goods', 'safe_conduct_goods.id_goods = ref_goods.id', 'left')
            ->join('ref_units', 'safe_conduct_goods.id_unit = ref_units.id', 'left')
            ->join('ref_positions', 'safe_conduct_goods.id_position = ref_positions.id', 'left')
            ->join('safe_conduct_containers', 'safe_conduct_containers.id = safe_conduct_goods.id_safe_conduct_container', 'left')
            ->join('ref_containers', 'ref_containers.id = safe_conduct_containers.id_container', 'left');

        return $bookings;
    }

    /**
     * Get safe conduct goods by safe conduct id.
     * @param integer $safeConduct
     * @param bool $nonContainer
     * @return array
     */
    public function getSafeConductGoodsBySafeConduct($safeConduct, $nonContainer = false)
    {
        $goods = $this->getBaseQuery()
            ->where('safe_conduct_goods.id_safe_conduct', $safeConduct);

        if($nonContainer) {
            $goods->where('safe_conduct_goods.id_safe_conduct_container IS NULL');
        }

        return $goods->get()->result_array();
    }

    /**
     * Get safe conduct goods by safe conduct id.
     * @param integer $safeConductContainerId
     * @return array
     */
    public function getSafeConductGoodsBySafeConductContainer($safeConductContainerId)
    {
        $goods = $this->getBaseQuery()
            ->where('safe_conduct_goods.id_safe_conduct_container', $safeConductContainerId);

        return $goods->get()->result_array();
    }

    /**
     * Insert single or batch safe conduct goods.
     * @param $data
     */
    public function createSafeConductGoods($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update or replace safe conduct goods.
     * @param $data
     * @param $id
     */
    public function updateSafeConductGoods($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * delete safe conduct goods.
     * @param $id
     */
    public function deleteSafeConductGoods($id)
    {
        return $this->db->delete($this->table, $id);
    }

    /**
     * Delete safe conduct goods by safe conduct id.
     * @param $safeConductId
     * @return mixed
     */
    public function deleteSafeConductGoodsBySafeConduct($safeConductId)
    {
        return $this->db->delete($this->table, ['id_safe_conduct' => $safeConductId]);
    }
}
