<?php

class OpnameSpaceUploadHistoryModel extends MY_Model
{
    protected $table = 'opname_space_upload_histories';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery()
            ->select([
                'prv_users.name AS creator_name'
            ])
            ->join(UserModel::$tableUser, 'prv_users.id = opname_space_upload_histories.created_by', 'left');
    }
}
