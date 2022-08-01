<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransporterEntryPermitHistoryModel extends MY_Model
{
    protected $table = 'transporter_entry_permit_histories';

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
        ->join(UserModel::$tableUser, 'transporter_entry_permit_histories.created_by = prv_users.id', 'left');
        
        return $baseQuery;

    }
}
