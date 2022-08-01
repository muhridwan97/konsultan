<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SafeConductAttachmentModel extends MY_Model
{
    protected $table = 'safe_conduct_attachments';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery($branchId)
            ->select(['prv_users.name AS creator_name'])
            ->join(UserModel::$tableUser, 'prv_users.id = safe_conduct_attachments.created_by', 'left');
    }
}
