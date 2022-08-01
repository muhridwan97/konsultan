<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CycleCountContainerModel extends MY_Model
{
    protected $table = 'cycle_count_containers';

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

        $cycleCountContainers = $this->db->select([
            'cycle_count.no_cycle_count',
            'cycle_count.cycle_count_date',
            'ref_branches.branch',
            'cycle_count_containers.*',
            'ref_people.name',
            'bookings.no_booking',
            'bookings.no_reference',
            'ref_containers.no_container',
            'ref_positions.position',
        ])
            ->from($this->table)
            ->join('cycle_count', 'cycle_count.id = cycle_count_containers.id_cycle_count', 'left')
            ->join('ref_branches', 'ref_branches.id = cycle_count.id_branch', 'left')
            ->join('ref_people', 'ref_people.id = cycle_count_containers.id_owner', 'left')
            ->join('bookings', 'bookings.id = cycle_count_containers.id_booking', 'left')
            ->join('ref_containers', 'ref_containers.id = cycle_count_containers.id_container', 'left')
            ->join('ref_positions', 'ref_positions.id = cycle_count_containers.id_position', 'left'); 

        if (!empty($branchId)) {
            $cycleCountContainers->where('cycle_count.id_branch', $branchId);
        }

        return $cycleCountContainers;
    }
}