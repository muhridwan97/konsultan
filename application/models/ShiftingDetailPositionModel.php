<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ShiftingDetailPositionModel extends MY_Model
{
    protected $table = 'shifting_detail_positions';


      /**
     * Get active record query builder for all related shifting detail position data selection.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        $shiftingDetailPosition = $this->db
            ->select([
                'shifting_detail_positions.*',
                'ref_position_blocks.*',
            ])
            ->from('shifting_detail_positions')
            ->join('shifting_details', 'shifting_details.id = shifting_detail_positions.id_shifting_detail', 'left')
            ->join('ref_position_blocks', 'ref_position_blocks.id = shifting_detail_positions.id_position_block', 'left');

        return $shiftingDetailPosition;
    }

    public function getShiftingDetailPositionByDetailshiftingId($idShitingDetail)
    {
        $shifting = $this->getBaseQuery();
        $shifting->where('shifting_detail_positions.id_shifting_detail', $idShitingDetail);

        return $shifting->get()->result_array();
    }

}