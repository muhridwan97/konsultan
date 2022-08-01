<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DeliveryTrackingAssignmentModel extends MY_Model
{
    protected $table = 'delivery_tracking_assignments';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery($branchId)
            ->select([
                'delivery_trackings.no_delivery_tracking',
                'ref_employees.name AS employee_name',
                'ref_employees.contact_mobile',
                'creators.name AS creator_name'
            ])
            ->join('delivery_trackings', 'delivery_trackings.id = delivery_tracking_assignments.id_delivery_tracking', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = delivery_trackings.id_user', 'left')
            ->join(EmployeeModel::$tableEmployee, 'ref_employees.id_user = prv_users.id', 'left')
            ->join(UserModel::$tableUser . ' AS creators', 'creators.id = delivery_tracking_assignments.created_by', 'left');
    }
}
