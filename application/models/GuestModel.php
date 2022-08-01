<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GuestModel extends MY_Model
{
    protected $table = 'guest';
    
    const STATUS_SCHEDULED = 'SCHEDULED';
    const STATUS_ARRIVED = 'ARRIVED';
    const STATUS_CHECKED_IN = 'CHECKED IN';
    const STATUS_CHECKED_OUT = 'CHECKED OUT';
    const STATUS_CANCELLED = 'CANCELLED';
    const STATUS_AREA_VISITED = 'AREA VISITED';
    const STATUS_UPDATED = 'UPDATED';

    public function __construct()
	{
		parent::__construct();
		if ($this->config->item('sso_enable')) {
        $this->table = env('DB_VMS_DATABASE') . '.guest';
		}
    }
    /**
     * Generate unique code number
     * @param int $branchId
     * @return array
     */
    public function getGuestIn($branchId = null)
    {
        if(empty($branchId)) {
            $branchId = get_active_branch('id_branch_vms');
        }
        $varCheckin = "CHECKED IN";
        $baseQuery = parent::getBaseQuery()
            ->select('prv_users.name AS check_in_name')
            ->where('id_branch', $branchId)
            ->where('checkin is not null', null)
            ->where('checkin >=', "2019-11-23 00:00:00")
            ->where('checkout is null', null)
            ->join('(select * from '.env('DB_VMS_DATABASE') .'.status_histories where status="'.$varCheckin.'") AS status_histories','status_histories.id_reference='.$this->table.'.id','left')
            ->join(env('DB_SSO_DATABASE') .'.prv_users AS prv_users','prv_users.id=status_histories.created_by','left')
            //->group_by('uniqid')
            ->order_by('checkin','desc');
        return $baseQuery->get()->result_array();
    }
    /**
     * Generate unique code number
     * @param int $branchId
     * @param boolean $all
     * @return array
     */
    public function getLabours($branchId = null,$all=false)
    {
        if(empty($branchId)) {
            $branchId = get_active_branch('id_branch_vms');
        }
        $baseQuery = parent::getBaseQuery()
            ->where('id_branch', $branchId)
            ->where('checkin is not null', null)
            ->where('laborers_type', 'BURUH')
            ->group_by('uniqid')
            ->order_by('checkin','desc');
        if(!$all){
            $baseQuery->where('checkout is null', null);
        }
        return $baseQuery->get()->result_array();
    }
}