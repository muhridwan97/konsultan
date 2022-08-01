<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DeliveryTrackingDetailModel extends MY_Model
{
    protected $table = 'delivery_tracking_details';

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
            ->join(UserModel::$tableUser, 'prv_users.id = delivery_tracking_details.created_by', 'left');
    }
}
