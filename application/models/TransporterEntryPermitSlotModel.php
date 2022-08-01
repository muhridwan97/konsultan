<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransporterEntryPermitSlotModel extends MY_Model
{
    protected $table = 'transporter_entry_permit_slot';

    /**
     * TEP Customers Model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

     /**
     * Get active record query builder for all related tep data.
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = NULL)
    {
    	$baseQuery = parent::getBaseQuery();
        return $baseQuery;

    }
    function getSlot($filters = []){
        $branchId = get_active_branch('id');
        $baseQuery = $this->db->from('transporter_entry_permit_slot')
            ->select('SUM(slot) as total_slot')
            ->where($this->table.'.id_branch', $branchId)
            ->group_by($this->table.'.date');
        if (key_exists('tep_date', $filters) && !empty($filters['tep_date'])) {
            $baseQuery->where($this->table.'.date', $filters['tep_date']);
        }else{
            $baseQuery->where($this->table.'.date = date(now())');
        }
        return $baseQuery->get()->result_array();
    }
}
