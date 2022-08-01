<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class StorageOverSpaceCustomerModel
 * @property StorageOverSpaceModel $storageOverSpace
 * @property StorageOverSpaceCustomerModel $storageOverSpaceCustomer
 * @property StorageOverSpaceActivityModel $storageOverSpaceActivity
 */
class StorageOverSpaceCustomerModel extends MY_Model
{
    protected $table = 'storage_over_space_customers';

    const STATUS_PENDING = 'PENDING';
    const STATUS_SKIPPED = 'SKIPPED';
    const STATUS_VALIDATED = 'VALIDATED';

    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery($branchId)
            ->select([
                'storage_over_spaces.type',
                'storage_over_spaces.date_from',
                'storage_over_spaces.date_to',
                'customers.name AS customer_name',
                'customers.email AS customer_email',
                'customer_parents.name AS customer_parent_name',
                'IF(SUM(IF(storage_over_space_activities.warehouse_type = "WAREHOUSE", storage_over_space_activities.over_space, 0)) > 0, 1, 0) AS is_warehouse_over_space',
                'IF(SUM(IF(storage_over_space_activities.warehouse_type = "YARD", storage_over_space_activities.over_space, 0)) > 0, 1, 0) AS is_yard_over_space',
                'IF(SUM(IF(storage_over_space_activities.warehouse_type = "COVERED YARD", storage_over_space_activities.over_space, 0)) > 0, 1, 0) AS is_covered_yard_over_space',
            ])
            ->join('storage_over_space_activities', 'storage_over_space_activities.id_storage_over_space_customer = storage_over_space_customers.id', 'left')
            ->join('storage_over_spaces', 'storage_over_spaces.id = storage_over_space_customers.id_storage_over_space', 'left')
            ->join('ref_people AS customers', 'customers.id = storage_over_space_customers.id_customer', 'left')
            ->join('ref_people AS customer_parents', 'customer_parents.id = storage_over_space_customers.id_parent', 'left')
            ->group_by('storage_over_space_customers.id');
    }

    /**
     * Get last storage over space.
     *
     * @param $customerId
     * @param $branchId
     * @param null $bellowDate
     * @return array|null
     */
    public function getLastStorageOverSpace($customerId, $branchId, $bellowDate = null)
    {
        $baseQuery = $this->getBaseQuery()
            ->where([
                'storage_over_space_customers.id_customer' => $customerId,
                'storage_over_spaces.id_branch' => $branchId,
                'storage_over_spaces.is_deleted' => false,
                'storage_over_space_customers.status' => self::STATUS_VALIDATED,
                //'storage_over_space_customers.status!=' => self::STATUS_PENDING,
            ])
            ->order_by('storage_over_spaces.date_to', 'desc')
            ->limit(1);

        if (!empty($bellowDate)) {
            $baseQuery->where('storage_over_spaces.date_to<', $bellowDate);
        }

        return $baseQuery->get()->row_array();
    }

    /**
     * Get over space last storage.
     *
     * @param $id
     * @return mixed
     */
    public function getOverSpaceCustomerWithLastStorage($id)
    {
        $storageOverSpaceCustomers = $this->storageOverSpaceCustomer->getBy(['id_storage_over_space' => $id]);
        foreach ($storageOverSpaceCustomers as &$customer) {
            $storageWarehouse = $this->storageOverSpaceActivity->getBy([
                'id_storage_over_space_customer' => $customer['id'],
                'warehouse_type' => 'WAREHOUSE',
            ]);
            $storageYard = $this->storageOverSpaceActivity->getBy([
                'id_storage_over_space_customer' => $customer['id'],
                'warehouse_type' => 'YARD',
            ]);
            $storageCoveredYard = $this->storageOverSpaceActivity->getBy([
                'id_storage_over_space_customer' => $customer['id'],
                'warehouse_type' => 'COVERED YARD',
            ]);
            $customer['last_storages'] = [
                'warehouse_capacity' => end($storageWarehouse)['capacity'] ?? 0,
                'warehouse_usage' => end($storageWarehouse)['used_storage'] ?? 0,
                'yard_capacity' => end($storageYard)['capacity'] ?? 0,
                'yard_usage' => end($storageYard)['used_storage'] ?? 0,
                'covered_yard_capacity' => end($storageCoveredYard)['capacity'] ?? 0,
                'covered_yard_usage' => end($storageCoveredYard)['used_storage'] ?? 0,
            ];
        }
        return $storageOverSpaceCustomers;
    }
}
