<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class CustomerStorageCapacityModel
 * @property DashboardModel $dashboard
 */
class CustomerStorageCapacityModel extends MY_Model
{
    protected $table = 'ref_customer_storage_capacities';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_PENDING = 'PENDING';
    const STATUS_EXPIRED = 'EXPIRED';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        return parent::getBaseQuery($branchId)
            ->select([
                'ref_people.name AS customer_name',
                'IF(ref_customer_storage_capacities.effective_date > CURDATE(), 
                    "PENDING", 
                    IF((ref_customer_storage_capacities.effective_date <= CURDATE() AND MIN(next_storage_capacities.effective_date) > CURDATE()) OR MIN(next_storage_capacities.effective_date) IS NULL, 
                        IF(ref_customer_storage_capacities.expired_date < CURDATE(), "EXPIRED", "ACTIVE"), 
                        "EXPIRED"
                    )
                ) AS status'
            ])
            ->join('ref_people', 'ref_people.id = ref_customer_storage_capacities.id_customer')
            ->join('(
                SELECT * FROM ref_customer_storage_capacities
                WHERE is_deleted = false
            ) AS next_storage_capacities', 'next_storage_capacities.id_customer = ref_people.id AND next_storage_capacities.effective_date > ref_customer_storage_capacities.effective_date', 'left')
            ->group_by('ref_customer_storage_capacities.id');
    }


    /**
     * Get all delivery with or without deleted records.
     *
     * @param array $filters
     * @param bool $withTrashed
     * @return mixed
     */
    public function getAll($filters = [], $withTrashed = false)
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $this->db->start_cache();

        $baseQuery = $this->getBaseQuery($branchId);

        if (!$withTrashed) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if (!empty($search)) {
            $baseQuery
                ->group_start()
                ->or_like('ref_customer_storage_capacities.description', $search)
                ->or_like('ref_customer_storage_capacities.warehouse_capacity', $search)
                ->or_like('ref_customer_storage_capacities.yard_capacity', $search)
                ->or_like('ref_customer_storage_capacities.covered_yard_capacity', $search)
                ->or_like('ref_people.name', $search)
                ->group_end();
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->where('ref_customer_storage_capacities.id_customer', $filters['customer']);
        }

        if (key_exists('status', $filters) && !empty($filters['status'])) {
            $baseQuery->having('status', $filters['status']);
        }

        if (key_exists('effective_date_from', $filters) && !empty($filters['effective_date_from'])) {
            $baseQuery->where('ref_customer_storage_capacities.effective_date>=', format_date($filters['effective_date_from']));
        }

        if (key_exists('effective_date_to', $filters) && !empty($filters['effective_date_to'])) {
            $baseQuery->where('ref_customer_storage_capacities.effective_date<=', format_date($filters['effective_date_to']));
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        if ($column == 'no') $column = 'ref_customer_storage_capacities.id';
        $page = $baseQuery->order_by($column, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        foreach ($data as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($data),
            "recordsFiltered" => $total,
            "data" => $data
        ];
        $this->db->flush_cache();

        return $pageData;
    }

    /**
     * Get data by custom condition.
     *
     * @param $conditions
     * @param bool $resultRow
     * @param bool $withTrashed
     * @return array|int
     */
    public function getBy($conditions, $resultRow = false, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery();

        foreach ($conditions as $key => $condition) {
            if (is_array($condition)) {
                if (!empty($condition)) {
                    if (strtolower($key) == 'status') {
                        $baseQuery->group_start();
                        foreach ($condition as $itemCondition) {
                            $baseQuery->or_having('status', $itemCondition);
                        }
                        $baseQuery->group_end();
                    } else {
                        $baseQuery->where_in($key, $condition);
                    }
                }
            } else {
                if (strtolower($key) == 'status') {
                    $baseQuery->having('status', $condition);
                } else {
                    $baseQuery->where($key, $condition);
                }
            }
        }

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if ($resultRow === 'COUNT') {
            return $baseQuery->count_all_results();
        } else if ($resultRow) {
            return $baseQuery->get()->row_array();
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get customer storage summary.
     *
     * @param $customer array|int return of customer or id customer
     * @param null $spaceDate
     * @param null $goodsStorages
     * @return mixed
     */
    public function getCustomerStorageSummary($customer, $spaceDate = null, $goodsStorages = null)
    {
        $this->load->model('DashboardModel', 'dashboard');

        if (is_int($customer)) {
            $customer = $this->db->from('ref_people')->get_where(['id' => $customer])->row_array();
        }

        if (is_null($goodsStorages)) {
            $goodsStorages = $this->dashboard->getGoodsStorageCapacityUsage([
                'customer' => $customer['id'],
                'customer_include_member' => true,
                'space_date' => $spaceDate
            ]);

            $containerStorages = $this->dashboard->getContainersStorageCapacityUsage([
                'customer' => $customer['id'],
                'customer_include_member' => true,
                'space_date' => $spaceDate
            ]);
            $containerStorages = array_map(function($containerItem) {
                $containerItem['goods_name'] = 'CONTAINER';
                $containerItem['id_goods'] = 0;
                $containerItem['unit_length'] = 0;
                $containerItem['unit_width'] = 0;
                return $containerItem;
            }, $containerStorages);
            $goodsStorages = array_merge($goodsStorages, $containerStorages);
        }

        $customer['warehouse_storages']['fcl'] = [];
        $customer['warehouse_storages']['lcl'] = [];
        $customer['yard_storages']['fcl'] = [];
        $customer['yard_storages']['lcl'] = [];
        $customer['covered_yard_storages']['fcl'] = [];
        $customer['covered_yard_storages']['lcl'] = [];

        $totalUsedWarehouse = 0;
        $totalUsedYard = 0;
        $totalUsedCoveredYard = 0;
        foreach ($goodsStorages as $itemStorage) {
            if ($itemStorage['warehouse_type'] == 'WAREHOUSE') {
                if (!empty($itemStorage['no_container'])) {
                    $customer['warehouse_storages']['fcl'][] = $itemStorage;
                } else {
                    $customer['warehouse_storages']['lcl'][] = $itemStorage;
                }
                $totalUsedWarehouse += $itemStorage['teus_used'];
            }
            if (in_array($itemStorage['warehouse_type'], ['YARD', 'FIELD'])) {
                if (!empty($itemStorage['no_container'])) {
                    $customer['yard_storages']['fcl'][] = $itemStorage;
                } else {
                    $customer['yard_storages']['lcl'][] = $itemStorage;
                }
                $totalUsedYard += $itemStorage['teus_used'];
            }
            if ($itemStorage['warehouse_type'] == 'COVERED YARD') {
                if (!empty($itemStorage['no_container'])) {
                    $customer['covered_yard_storages']['fcl'][] = $itemStorage;
                } else {
                    $customer['covered_yard_storages']['lcl'][] = $itemStorage;
                }
                $totalUsedCoveredYard += $itemStorage['teus_used'];
            }
        }
        // summary per storage type
        $customer['warehouse_storages']['total_used_fcl'] = array_sum(array_column($customer['warehouse_storages']['fcl'], 'teus_used'));
        $customer['warehouse_storages']['total_used_lcl'] = array_sum(array_column($customer['warehouse_storages']['lcl'], 'teus_used'));
        $customer['warehouse_storages']['total_used'] = $totalUsedWarehouse;
        $customer['warehouse_storages']['total_used_percent'] = empty((float)$customer['warehouse_capacity_teus']) ? 0 : ($totalUsedWarehouse / $customer['warehouse_capacity_teus'] * 100);

        $customer['yard_storages']['total_used_fcl'] = array_sum(array_column($customer['yard_storages']['fcl'], 'teus_used'));
        $customer['yard_storages']['total_used_lcl'] = array_sum(array_column($customer['yard_storages']['lcl'], 'teus_used'));
        $customer['yard_storages']['total_used'] = $totalUsedYard;
        $customer['yard_storages']['total_used_percent'] = empty((float)$customer['yard_capacity_teus']) ? 0 : ($totalUsedYard / $customer['yard_capacity_teus'] * 100);

        $customer['covered_yard_storages']['total_used_fcl'] = array_sum(array_column($customer['covered_yard_storages']['fcl'], 'teus_used'));
        $customer['covered_yard_storages']['total_used_lcl'] = array_sum(array_column($customer['covered_yard_storages']['lcl'], 'teus_used'));
        $customer['covered_yard_storages']['total_used'] = $totalUsedCoveredYard;
        $customer['covered_yard_storages']['total_used_percent'] = empty((float)$customer['covered_yard_capacity_teus']) ? 0 : ($totalUsedCoveredYard / $customer['covered_yard_capacity_teus'] * 100);

        // summary total customer
        $customer['warehouse_capacity_teus_left'] = $customer['warehouse_capacity_teus'] - $totalUsedWarehouse;
        $customer['yard_capacity_teus_left'] = $customer['yard_capacity_teus'] - $totalUsedYard;
        $customer['covered_yard_capacity_teus_left'] = $customer['covered_yard_capacity_teus'] - $totalUsedCoveredYard;

        $customer['warehouse_capacity_teus_left_percent'] = empty((float)$customer['warehouse_capacity_teus']) ? 0 : ($customer['warehouse_capacity_teus_left'] / $customer['warehouse_capacity_teus'] * 100);
        $customer['yard_capacity_teus_left_percent'] = empty((float)$customer['yard_capacity_teus']) ? 0 : ($customer['yard_capacity_teus_left'] / $customer['yard_capacity_teus'] * 100);
        $customer['covered_yard_capacity_teus_left_percent'] = empty((float)$customer['covered_yard_capacity_teus']) ? 0 : ($customer['covered_yard_capacity_teus_left'] / $customer['covered_yard_capacity_teus'] * 100);

        $customer['warehouse_capacity_status'] = $customer['warehouse_capacity_teus_left'] <= 0 ? 'OVER' : ($customer['warehouse_capacity_teus_left'] < 2 ? 'HIGH' : 'NORMAL');
        $customer['yard_capacity_status'] = $customer['yard_capacity_teus_left'] <= 0 ? 'OVER' : ($customer['yard_capacity_teus_left'] < 2 ? 'HIGH' : 'NORMAL');
        $customer['covered_yard_capacity_status'] = $customer['covered_yard_capacity_teus_left'] <= 0 ? 'OVER' : ($customer['covered_yard_capacity_teus_left'] < 2 ? 'HIGH' : 'NORMAL');

        return $customer;
    }

}
