<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PositionBlockModel extends MY_Model
{
    protected $table = 'ref_position_blocks';

    /**
     * Get base query position block.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery($branchId)
            ->select([
                'ref_positions.id_warehouse',
                'ref_positions.position',
                'ref_position_types.color',
                'ref_position_types.position_type',
                'ref_position_types.is_usable',
            ])
            ->join('(SELECT * FROM ref_positions WHERE is_deleted = 0) AS ref_positions', 'ref_positions.id = ref_position_blocks.id_position')
            ->join('ref_position_types', 'ref_position_types.id = ref_positions.id_position_type', 'left');
    }
}