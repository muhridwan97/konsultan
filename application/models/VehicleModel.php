<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class VehicleModel extends MY_Model
{
    protected $table = 'ref_vehicles';

    /**
     * Get master vehicle base query.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return $this->db->select([
            'ref_vehicles.*',
            'ref_branches.branch'
        ])
            ->from($this->table)
            ->join('ref_branches', 'ref_branches.id = ref_vehicles.id_branch', 'left');
    }
}