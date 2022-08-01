<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ShiftingDetailModel extends MY_Model
{
    protected $table = 'shifting_details';

    /**
     * Get active record query builder for all related shifting detail position data selection.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return $this->db
            ->select([
                'shifting_details.*',
                'bookings.no_reference',
                'ref_containers.no_container',
                'ref_positions.position',
                'ref_goods.name AS goods_name',
                'ref_goods.unit_length',
                'ref_goods.unit_width',
                'ref_goods.unit_height',
                'ref_goods.unit_volume',
                'ref_goods.unit_weight',
                'ref_goods.unit_gross_weight',
                'GROUP_CONCAT(DISTINCT shifting_detail_positions.id_position_block) AS id_position_blocks',
                'GROUP_CONCAT(DISTINCT ref_position_blocks.position_block) AS position_blocks',
            ])
            ->from($this->table)
            ->join('bookings', 'bookings.id = shifting_details.id_booking', 'left')
            ->join('ref_containers', 'shifting_details.id_container = ref_containers.id', 'left')
            ->join('shifting_detail_positions', 'shifting_detail_positions.id_shifting_detail = shifting_details.id', 'left')
            ->join('ref_position_blocks', 'ref_position_blocks.id = shifting_detail_positions.id_position_block', 'left')
            ->join('ref_goods', 'shifting_details.id_goods = ref_goods.id', 'left')
            ->join('ref_positions', 'shifting_details.id_position = ref_positions.id', 'left')
            ->group_by('shifting_details.id');
    }
}
