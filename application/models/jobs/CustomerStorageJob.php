<?php

/**
 * Class CustomerStorageJob
 *
 * @property BranchModel $branch
 * @property PeopleModel $people
 * @property WorkOrderModel $workOrder
 * @property CustomerStorageCapacityModel $customerStorageCapacity
 * @property ReportStorageModel $reportStorage
 * @property StorageUsageModel $storageUsage
 * @property StorageUsageCustomerModel $storageUsageCustomer
 * @property StorageOverSpaceModel $storageOverSpace
 * @property StorageOverSpaceCustomerModel $storageOverSpaceCustomer
 * @property StorageOverSpaceActivityModel $storageOverSpaceActivity
 * @property StatusHistoryModel $statusHistory
 * @property NotificationModel $notification
 */
class CustomerStorageJob extends CI_Model
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BranchModel', 'branch');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('NotificationModel', 'notification');
    }

    /**
     * Generate over space summary.
     *
     * @param $type
     * @param $dateFrom
     * @param $dateTo
     */
    public function generateOverSpaceSummary($type, $dateFrom, $dateTo)
    {
        $this->load->model('ReportStorageModel', 'customerStorageCapacity');
        $this->load->model('CustomerStorageCapacityModel', 'reportStorage');
        $this->load->model('StorageOverSpaceModel', 'storageOverSpace');
        $this->load->model('StorageOverSpaceCustomerModel', 'storageOverSpaceCustomer');
        $this->load->model('StorageOverSpaceActivityModel', 'storageOverSpaceActivity');
        $this->load->model('notifications/OverSpaceM2SummaryNotification');

        $branches = $this->branch->getBy([
            'ref_branches.dashboard_status' => 1
        ]);

        $this->db->trans_start();

        // loop through all branch
        foreach ($branches as $branch) {
            // check if storage over space data already generated or not
            $currentStorageOverSpace = $this->storageOverSpace->getBy([
                'storage_over_spaces.type' => $type,
                'storage_over_spaces.date_from' => $dateFrom,
                'storage_over_spaces.date_to' => $dateTo,
                'storage_over_spaces.id_branch' => $branch['id']
            ]);

            // if empty, then generate one
            if (empty($currentStorageOverSpace)) {
                // get all customer of this branch that have storage capacity setting
                $customers = $this->customerStorageCapacity->getAll(['branch' => $branch['id']]);
                $customerIds = array_unique(array_column($customers, 'id_customer'));

                // loop through the customers,
                // and check if it's only parent (not the child) because parent already covered their children data
                $overSpaces = [];
                foreach ($customerIds as $customerId) {
                    $customer = $this->people->getById($customerId);
                    if (empty($customer['id_parent'])) {
                        $filters = [
                            'branch' => $branch['id'],
                            'customer' => $customerId,
                            'date_from' => $dateFrom,
                            'date_to' => $dateTo,
                            'customer_include_member' => true
                        ];
                        /*$filters = [
                            'customer' => '9644',
                            'date_from' => '14 September 2019',
                            'date_to' => '26 September 2019',
                            'customer_include_member' => true
                        ];*/

                        $filters['warehouse_type'] = 'WAREHOUSE';
                        $warehouseActivities = $this->reportStorage->getStorageActivityUsage($filters);

                        $filters['warehouse_type'] = 'YARD';
                        $yardActivities = $this->reportStorage->getStorageActivityUsage($filters);

                        $filters['warehouse_type'] = 'COVERED YARD';
                        $coveredYardActivities = $this->reportStorage->getStorageActivityUsage($filters);

                        // find out if the range of the transaction $dateFrom - $dateTo there is activity
                        // that produce over space, the mark it
                        $isOverSpace = false;
                        foreach ($warehouseActivities as $warehouseActivity) {
                            if ($warehouseActivity['over_space'] > 0) {
                                $isOverSpace = true;
                                break;
                            }
                        }
                        foreach ($yardActivities as $yardActivity) {
                            if ($yardActivity['over_space'] > 0) {
                                $isOverSpace = true;
                                break;
                            }
                        }
                        foreach ($coveredYardActivities as $coveredYardActivity) {
                            if ($coveredYardActivity['over_space'] > 0) {
                                $isOverSpace = true;
                                break;
                            }
                        }

                        if ($isOverSpace) {
                            // set initial data, and by default data need to be validated
                            $overSpaceData = [
                                'id_customer' => $customerId,
                                'customer_name' => $customer['name'],
                                'customer_email' => $customer['email'],
                                'data' => [
                                    'WAREHOUSE' => $warehouseActivities,
                                    'YARD' => $yardActivities,
                                    'COVERED YARD' => $coveredYardActivities
                                ],
                                'need_revalidate' => true
                            ];

                            // important!: later we need to decide if current data we should send immediately OR need to be validated first
                            // IF last transaction IS VALIDATED already and there are NO TRANSACTION after, we send data immediately
                            // IF NOT then create the storage over space data, and let it to be validated first
                            $lastTransaction = $this->storageOverSpaceCustomer->getLastStorageOverSpace($customerId, $branch['id'], $dateTo);
                            if (!empty($lastTransaction)) {
                                // get job transaction from last validated over space data until $dateTo (this day)
                                $transaction = $this->workOrder->getBy([
                                    'bookings.id_customer' => $lastTransaction['id_customer'],
                                    'work_orders.completed_at>=' => $lastTransaction['date_to'] . ' 00:00:00',
                                    'work_orders.completed_at<=' => $dateTo . ' 00:00:00',
                                    'ref_handling_types.multiplier_goods!=' => 0,
                                ]);

                                // if no transaction from last generated storage over space data then no need revalidate,
                                // just send to customer by then
                                if (empty($transaction)) {
                                    $overSpaceData['need_revalidate'] = false;
                                    $overSpaceData['storage_over_space_customer'] = $lastTransaction;
                                }
                            }

                            $overSpaces[] = $overSpaceData;
                        }
                    }
                }

                // after we find out which is what need to be validated or send immediately,
                // then split into 2 groups
                $customerStorageNeedRevalidates = array_filter($overSpaces, function ($item) {
                    return $item['need_revalidate'];
                });
                $customerStorageSendImmediately = array_filter($overSpaces, function ($item) {
                    return !$item['need_revalidate'];
                });

                // if need validation, then create new data
                if (!empty($customerStorageNeedRevalidates)) {
                    $this->storageOverSpace->create([
                        'id_branch' => $branch['id'],
                        'type' => $type,
                        'date_from' => $dateFrom,
                        'date_to' => $dateTo,
                        'status' => StorageOverSpaceModel::STATUS_PENDING,
                        'description' => 'Auto generated'
                    ]);
                    $storageOverSpaceId = $this->db->insert_id();

                    $this->statusHistory->create([
                        'id_reference' => $storageOverSpaceId,
                        'type' => StatusHistoryModel::TYPE_STORAGE_OVER_SPACE,
                        'status' => StorageOverSpaceModel::STATUS_PENDING,
                        'description' => 'Auto generate weekly over space',
                    ]);

                    foreach ($customerStorageNeedRevalidates as $overSpace) {
                        $this->storageOverSpaceCustomer->create([
                            'id_storage_over_space' => $storageOverSpaceId,
                            'id_customer' => $overSpace['id_customer'],
                            'status' => StorageOverSpaceCustomerModel::STATUS_PENDING
                        ]);
                        $storageOverSpaceCustomerId = $this->db->insert_id();

                        $this->statusHistory->create([
                            'id_reference' => $storageOverSpaceCustomerId,
                            'type' => StatusHistoryModel::TYPE_STORAGE_OVER_SPACE_CUSTOMER,
                            'status' => StorageOverspaceCustomerModel::STATUS_PENDING,
                            'description' => 'Auto generate customer storage over space',
                        ]);

                        foreach ($overSpace['data'] as $warehouseType => $warehouseActivities) {
                            foreach ($warehouseActivities as $warehouseActivity) {
                                $capacity = $warehouseActivity['capacity'] ?? 0;

                                if (!isset($warehouseActivity['capacity'])) {
                                    switch ($warehouseActivity['warehouse_type']) {
                                        case 'WAREHOUSE':
                                            $capacity = $warehouseActivity['warehouse_capacity'];
                                            break;
                                        case 'YARD':
                                            $capacity = $warehouseActivity['yard_capacity'];
                                            break;
                                        case 'COVERED YARD':
                                            $capacity = $warehouseActivity['covered_yard_capacity'];
                                            break;
                                    }
                                }

                                $data = [
                                    'id_storage_over_space_customer' => $storageOverSpaceCustomerId,
                                    'warehouse_type' => $warehouseType,
                                    'capacity' => $capacity,
                                    'inbound_storage' => $warehouseActivity['inbound_storage'],
                                    'outbound_storage' => $warehouseActivity['outbound_storage'],
                                    'left_storage' => $warehouseActivity['left_storage'],
                                    'used_storage' => $warehouseActivity['used_storage'],
                                    'over_space' => $warehouseActivity['over_space'],
                                    'row_type' => $warehouseActivity['row_type'],
                                ];
                                switch ($warehouseActivity['row_type']) {
                                    case 'beginning-balance':
                                        $data['id_customer'] = $overSpace['id_customer'];
                                        $data['stock_date'] = $warehouseActivity['stock_date'];
                                        break;
                                    case 'transaction':
                                        $data = array_merge($data, [
                                            'id_customer' => $warehouseActivity['id_customer'],
                                            'id_parent' => $warehouseActivity['id_parent'],
                                            'no_reference' => $warehouseActivity['no_reference'],
                                            'no_reference_inbound' => $warehouseActivity['no_reference_inbound'],
                                            'date_activity' => $warehouseActivity['date_activity'],
                                            'activity_type' => $warehouseActivity['activity_type'],
                                            'handling_type' => $warehouseActivity['handling_type'],
                                            'id_work_order' => $warehouseActivity['id_work_order'],
                                            'no_work_order' => $warehouseActivity['no_work_order'],
                                            'warehouse_type' => $warehouseActivity['warehouse_type'],
                                            'id_goods' => $warehouseActivity['id_goods'],
                                            'goods_name' => $warehouseActivity['goods_name'],
                                            'unit' => $warehouseActivity['unit'],
                                            'no_container' => $warehouseActivity['no_container'],
                                            'size' => $warehouseActivity['size'],
                                            'total_goods_loaded_quantity' => $warehouseActivity['total_goods_loaded_quantity'],
                                            'quantity' => $warehouseActivity['quantity'],
                                            'effective_date_capacity' => $warehouseActivity['effective_date_capacity'],
                                        ]);
                                        break;
                                    case 'change-capacity':
                                        $data['id_customer'] = $overSpace['id_customer'];
                                        $data['effective_date_capacity'] = $warehouseActivity['effective_date_capacity'];
                                        break;
                                }
                                $this->storageOverSpaceActivity->create($data);
                            }
                        }
                    }
                }

                // send immediately for no need-validation data
                if (!empty($customerStorageSendImmediately)) {
                    $emailOperational = explode(",", $branch['email_operational']);
                    $emailCommercial = explode(",", $branch['email_support']);

                    foreach ($customerStorageSendImmediately as $customerStorage) {
                        $to = $customerStorage['customer_email'];
                        $emailFinance = ['fin2@transcon-indonesia.com', 'findata@transcon-id.com'];
                        if (empty($to)) {
                            $to = 'acc_mgr@transcon-indonesia.com';
                        } else {
                            $emailFinance[] = 'acc_mgr@transcon-indonesia.com';
                        }

                        $emailOption = ['cc' => array_merge($emailFinance, $emailOperational, $emailCommercial)];
                        $this->notification
                            ->via([Notify::MAIL_PUSH])
                            ->to($to)
                            ->send(new OverSpaceM2SummaryNotification(
                                $customerStorage['storage_over_space_customer'] ?? [],
                                $customerStorage['data'],
                                $emailOption
                            ));
                    }
                }
            } else {
                echo "Storage over space data branch {$branch['branch']} already exist" . PHP_EOL;
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            echo "Space over from {$dateFrom} to {$dateTo} generated or resend" . PHP_EOL;
        } else {
            echo "Generate space over from {$dateFrom} - {$dateTo} failed";
        }
    }

}