<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class StorageOverSpaceActivityModel
 */
class StorageOverSpaceActivityModel extends MY_Model
{
    protected $table = 'storage_over_space_activities';

    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery($branchId)
            ->select([
                'customers.name AS customer_name',
            ])
            ->join('ref_people AS customers', 'customers.id = storage_over_space_activities.id_customer', 'left');
    }
}
