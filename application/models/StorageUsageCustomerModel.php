<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class StorageUsageCustomerModel
 */
class StorageUsageCustomerModel extends MY_Model
{
    protected $table = 'storage_usage_customers';

    const STATUS_PENDING = 'PENDING';
    const STATUS_SKIPPED = 'SKIPPED';
    const STATUS_VALIDATED = 'VALIDATED';

    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery($branchId)
            ->select([
                'storage_usages.date',
                'customers.name AS customer_name',
                'customer_parents.name AS customer_parent_name',
                'IF(warehouse_usage = 0, 0, IF(warehouse_capacity > 0, (warehouse_usage / warehouse_capacity * 100), 100)) AS used_warehouse_percent',
                'IF(yard_usage = 0, 0, IF(yard_capacity > 0, (yard_usage / yard_capacity * 100), 100)) AS used_yard_percent',
                'IF(covered_yard_usage = 0, 0, IF(covered_yard_capacity > 0, (covered_yard_usage / covered_yard_capacity * 100), 100)) AS used_covered_yard_percent',
            ])
            ->join('storage_usages', 'storage_usages.id = storage_usage_customers.id_storage_usage', 'left')
            ->join('ref_people AS customers', 'customers.id = storage_usage_customers.id_customer', 'left')
            ->join('ref_people AS customer_parents', 'customer_parents.id = storage_usage_customers.id_parent', 'left');
    }

    /**
     * Get last storage usage.
     *
     * @param $customerId
     * @param $branchId
     * @param null $bellowDate
     * @return array|null
     */
    public function getLastStorageUsage($customerId, $branchId, $bellowDate = null)
    {
        $baseQuery = $this->getBaseQuery()
            ->where([
                'storage_usage_customers.id_customer' => $customerId,
                'storage_usages.id_branch' => $branchId,
                'storage_usages.is_deleted' => false,
                'storage_usage_customers.status' => self::STATUS_VALIDATED,
                //'storage_usage_customers.status!=' => self::STATUS_PENDING,
            ])
            ->order_by('storage_usages.date', 'desc')
            ->limit(1);

        if (!empty($bellowDate)) {
            $baseQuery->where('storage_usages.date<', $bellowDate);
        }

        return $baseQuery->get()->row_array();
    }
}
