<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HeavyEquipmentBranchModel extends MY_Model
{
    protected $table = 'ref_heavy_equipment_branches';

    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery($branchId)
            ->select([
                'ref_branches.branch'
            ])
            ->join('ref_branches', 'ref_branches.id = ref_heavy_equipment_branches.id_branch');
    }
}