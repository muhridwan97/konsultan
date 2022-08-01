<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkOrderHistoryLockModel extends MY_Model
{
    protected $table = 'work_order_history_locks';

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
                'user.name',
                'work_order_history_locks.*',
            ])
            ->from('work_order_history_locks')
            ->join(env('DB_SSO_DATABASE') . '.prv_users AS user', 'user.id = work_order_history_locks.created_by', 'left');


        return $history;
    }
    function getHistoryLockByWorkOrderId($id){
        $query = $this->getBaseQuery()
            ->where('work_order_history_locks.id_work_order', $id);
        
        return $query->get()->result_array();
    }
}