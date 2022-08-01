<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OpnameGoodsModel extends MY_Model
{
    protected $table = 'opname_goods';

    /**
     * Get active record query builder for all related data selection.
     * @param null $branchId
     * @return mixed
     */
    protected function getBaseQuery($branchId = null)
    {
        if(empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $opnames = $this->db->select([
            'opnames.no_opname',
            'opnames.opname_date',
            'ref_branches.branch',
            'opname_goods.*',
            'ref_people.name',
            'bookings.no_booking',
            'bookings.no_reference',
            'ref_goods.no_goods',
            'ref_goods.name as name_goods',
            'ref_units.unit',
            'ref_positions.position',
            'IF(LOWER(TRIM(ref_positions.position)) != LOWER(TRIM(opname_goods.position_check)), 0, 1) AS is_location_match',
            'IF(opname_goods.quantity <> opname_goods.quantity_check, 0, 1) AS is_quantity_match',
        ])
            ->from($this->table)
            ->join('opnames', 'opnames.id = opname_goods.id_opname', 'left')
            ->join('ref_branches', 'ref_branches.id = opnames.id_branch', 'left')
            ->join('ref_people', 'ref_people.id = opname_goods.id_owner', 'left')
            ->join('bookings', 'bookings.id = opname_goods.id_booking', 'left')
            ->join('ref_goods', 'ref_goods.id = opname_goods.id_goods', 'left')
            ->join('ref_units', 'ref_units.id = opname_goods.id_unit', 'left')
            ->join('ref_positions', 'ref_positions.id = opname_goods.id_position', 'left'); 

        if (!empty($branchId)) {
            $opnames->where('opnames.id_branch', $branchId);
        }

        return $opnames;
    }
}