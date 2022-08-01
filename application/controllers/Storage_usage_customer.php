<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Storage_usage_customer
 * @property StorageUsageModel $storageUsage
 * @property StorageUsageCustomerModel $storageUsageCustomer
 * @property CustomerStorageCapacityModel $customerStorageCapacity
 * @property PeopleModel $people
 * @property ReportStorageModel $reportStorage
 * @property ReportStockModel $reportStock
 * @property StatusHistoryModel $statusHistory
 * @property NotificationModel $notification
 */
class Storage_usage_customer extends MY_Controller
{
    /**
     * Storage_usage_customer constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('StorageUsageModel', 'storageUsage');
        $this->load->model('StorageUsageCustomerModel', 'storageUsageCustomer');
        $this->load->model('CustomerStorageCapacityModel', 'customerStorageCapacity');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('ReportStorageModel', 'reportStorage');
        $this->load->model('ReportStockModel', 'reportStock');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('NotificationModel', 'notification');

        $this->load->model('notifications/OverSpaceM2CustomerNotification');

        $this->setFilterMethods([
            'approve' => 'POST|PUT',
            'skip' => 'POST|PUT',
        ]);
    }

    /**
     * Show detail storage usage.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_VIEW);

        $storageUsageCustomer = $this->storageUsageCustomer->getById($id);
        $statusHistories = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_STORAGE_USAGE_CUSTOMER,
            'status_histories.id_reference' => $id
        ]);

        $this->render('storage_usage_customer/view', compact('storageUsageCustomer', 'statusHistories'));
    }

    /**
     * Validate storage usage data.
     *
     * @param $id
     */
    public function approve($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_VALIDATE);

        $message = $this->input->post('message');

        $storageUsageCustomer = $this->storageUsageCustomer->getById($id);

        $this->db->trans_start();

        $this->storageUsageCustomer->update([
            'status' => StorageUsageCustomerModel::STATUS_VALIDATED,
        ], $id);

        $this->statusHistory->create([
            'id_reference' => $id,
            'type' => StatusHistoryModel::TYPE_STORAGE_USAGE_CUSTOMER,
            'status' => StorageUsageCustomerModel::STATUS_VALIDATED,
            'description' => if_empty($message, 'Customer storage usage is validated'),
        ]);

        $this->checkStorageUsageStatus($storageUsageCustomer['id_storage_usage']);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            // for group member notify only the member that has stock
            $customerMembers = $this->people->getBy(['ref_people.id_parent' => $storageUsageCustomer['id_customer']]);
            if (empty($customerMembers)) {
                $notifiedCustomerIds = [$storageUsageCustomer['id_customer']];
            } else {
                $notifiedCustomerIds = array_column($customerMembers, 'id');
            }
            foreach ($notifiedCustomerIds as $customerId) {
                $customer = $this->people->getById($customerId);
                $customerBranch = $this->people->getPeopleByIdCustomerIdBranch($customer['id'], if_empty(get_active_branch_id(), $customer['id_branch']));
                $availableStocks = $this->reportStock->getStockGoods([
                    'data' => 'stock',
                    'owner' => $customerId
                ]);
                if (!empty($customerBranch['whatsapp_group']) && !empty($availableStocks)) {
                    $customer['whatsapp_group'] = $customerBranch['whatsapp_group'];
                    if (get_active_branch_id() == 8) {
                        $customer['whatsapp_group'] = get_setting('whatsapp_group_management');
                    }
                    $this->notification
                        ->via([Notify::CHAT_PUSH])
                        ->to($customer)
                        ->send(new OverSpaceM2CustomerNotification($storageUsageCustomer));
                }
            }

            flash('success', "Storage usage {$storageUsageCustomer['customer_name']} is successfully validated");
        } else {
            flash('danger', 'Validating storage usage failed');
        }

        redirect('storage-usage/view/' . $storageUsageCustomer['id_storage_usage']);
    }

    /**
     * Skip storage usage data.
     *
     * @param $id
     */
    public function skip($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_VALIDATE);

        $message = $this->input->post('message');

        $storageUsageCustomer = $this->storageUsageCustomer->getById($id);

        $this->db->trans_start();

        $this->storageUsageCustomer->update([
            'status' => StorageUsageCustomerModel::STATUS_SKIPPED,
        ], $id);

        $this->statusHistory->create([
            'id_reference' => $id,
            'type' => StatusHistoryModel::TYPE_STORAGE_USAGE_CUSTOMER,
            'status' => StorageUsageCustomerModel::STATUS_SKIPPED,
            'description' => if_empty($message, 'Customer storage usage is skipped'),
        ]);

        $this->checkStorageUsageStatus($storageUsageCustomer['id_storage_usage']);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            flash('success', "Storage usage {$storageUsageCustomer['customer_name']} is successfully skipped");
        } else {
            flash('danger', 'Validating storage usage failed');
        }

        redirect('storage-usage/view/' . $storageUsageCustomer['id_storage_usage']);
    }

    /**
     * Check if all storage usage customer is validate.
     *
     * @param $id
     */
    private function checkStorageUsageStatus($id)
    {
        $storageUsageCustomers = $this->storageUsageCustomer->getBy(['id_storage_usage' => $id]);

        $isPending = false;
        foreach ($storageUsageCustomers as $storageUsageCustomer) {
            if ($storageUsageCustomer['status'] == StorageUsageCustomerModel::STATUS_PENDING) {
                $isPending = true;
                break;
            }
        }

        if (!$isPending) {
            $this->storageUsage->update([
                'status' => StorageUsageModel::STATUS_PROCEED
            ], $id);

            $this->statusHistory->create([
                'id_reference' => $id,
                'type' => StatusHistoryModel::TYPE_STORAGE_USAGE,
                'status' => StorageUsageModel::STATUS_PROCEED,
                'description' => 'Storage usage is proceed',
            ]);
        }
    }
}