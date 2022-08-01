<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DiscrepancyHandoverGoodsModel extends MY_Model
{
    protected $table = 'discrepancy_handover_goods';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        return parent::getBaseQuery($branchId)
            ->select([
                'discrepancy_handovers.no_discrepancy',
                'ref_goods.no_goods',
                'ref_goods.name AS goods_name',
                'ref_units.unit',
            ])
            ->join('discrepancy_handovers', 'discrepancy_handovers.id = discrepancy_handover_goods.id_discrepancy_handover')
            ->join('ref_goods', 'ref_goods.id = discrepancy_handover_goods.id_goods')
            ->join('ref_units', 'ref_units.id = discrepancy_handover_goods.id_unit', 'left');
    }
}
