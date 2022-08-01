<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CycleCountGoodsModel extends MY_Model
{
    protected $table = 'cycle_count_goods';

   /**
     * Get active record query builder for all related warehouse data selection.
     *
     * @param null $ChecklistTypeId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($ChecklistTypeId = null)
    {
        $cycleCounts = $this->db
            ->select([
                'cycle_count.*',
                'cycle_count_goods.*',
                'ref_people.name',
                'bookings.no_booking',
                'bookings.no_reference',
                'ref_goods.no_goods',
                'ref_goods.name as goods_name',
                'ref_units.unit',
                'ref_positions.position',
            ])
            ->from('cycle_count_goods')
            ->join('cycle_count', 'cycle_count.id = cycle_count_goods.id_cycle_count', 'left')
            ->join('ref_people', 'ref_people.id = cycle_count_goods.id_owner', 'left')
            ->join('bookings', 'bookings.id = cycle_count_goods.id_booking', 'left')
            ->join('ref_goods', 'ref_goods.id = cycle_count_goods.id_goods', 'left')
            ->join('ref_units', 'ref_units.id = cycle_count_goods.id_unit', 'left')
            ->join('ref_positions', 'ref_positions.id = cycle_count_goods.id_position', 'left'); 


        return $cycleCounts;
    }

    public function getCycleCountGoodsById($id)
    {

        $cycleCounts = $this->getBaseQuery()->where('cycle_count_goods.id_cycle_count', $id);

        return $cycleCounts->get()->result_array();
    }

    /**
     * Update cycle count.
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateCycleCountGoods($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }
  
}