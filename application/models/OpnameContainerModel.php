<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OpnameContainerModel extends MY_Model
{
    protected $table = 'opname_containers';

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
            'opname_containers.*',
            'ref_people.name',
            'bookings.no_booking',
            'bookings.no_reference',
            'ref_containers.no_container',
            'ref_positions.position',
            'IF(LOWER(TRIM(ref_positions.position)) != LOWER(TRIM(opname_containers.position_check)), 0, 1) AS is_location_match',
            'IF(opname_containers.quantity <> opname_containers.quantity_check, 0, 1) AS is_quantity_match',
        ])
            ->from($this->table)
            ->join('opnames', 'opnames.id = opname_containers.id_opname', 'left')
            ->join('ref_branches', 'ref_branches.id = opnames.id_branch', 'left')
            ->join('ref_people', 'ref_people.id = opname_containers.id_owner', 'left')
            ->join('bookings', 'bookings.id = opname_containers.id_booking', 'left')
            ->join('ref_containers', 'ref_containers.id = opname_containers.id_container', 'left')
            ->join('ref_positions', 'ref_positions.id = opname_containers.id_position', 'left'); 

        if (!empty($branchId)) {
            $opnames->where('opnames.id_branch', $branchId);
        }

        return $opnames;
    }
}