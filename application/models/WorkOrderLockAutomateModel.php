<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkOrderLockAutomateModel extends MY_Model
{
    protected $table = 'work_order_lock_automate';

    /**
     * WorkOrderHistoryUpdateModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active record query builder for all related warehouse data selection.
     *
     * @param null $ChecklistTypeId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($ChecklistTypeId = null)
    {
        $history = $this->db
            ->select([
                'work_orders.is_locked',
                'work_orders.no_work_order',
                'user.name',
                'work_order_lock_automate.*',
            ])
            ->from('work_order_lock_automate')
            ->join(env('DB_SSO_DATABASE') . '.prv_users AS user', 'user.id = work_order_lock_automate.request_by', 'left')
            ->join('work_orders','work_orders.id=work_order_lock_automate.id_work_order','left')
            ->order_by('work_order_lock_automate.id','desc');

        return $history;
    }

    function getRequestUnlockById($id){
        $branch = get_active_branch();

        $query = $this->getBaseQuery()
            ->where('work_order_lock_automate.request_by', $id)
            ->where('work_order_lock_automate.id_branch', $branch['id']);
        
        return $query->get()->result_array();
    }
    function getAllRequestUnlock(){
        $branch = get_active_branch();
        $query = $this->getBaseQuery()
            ->where('work_order_lock_automate.id_branch', $branch['id']);
        
        return $query->get()->result_array();
    }
    function getAllApprove(){
        $query = $this->getBaseQuery()
            ->where('work_order_lock_automate.status', 'APPROVE');
        
        return $query->get()->result_array();
    }
}