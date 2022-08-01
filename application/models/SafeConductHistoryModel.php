<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SafeConductHistoryModel extends MY_Model
{
    protected $table = 'safe_conduct_histories';

    /**
     * TEP histories Model constructor.
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
        $baseQuery = parent::getBaseQuery()
        ->select([
            'prv_users.name AS author_name',
        ])
        ->join(UserModel::$tableUser, 'safe_conduct_histories.created_by = prv_users.id', 'left');
        
        return $baseQuery;

    }
}
