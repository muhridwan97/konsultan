<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Storage_usage
 * @property BranchModel $branch
 * @property StorageUsageModel $storageUsage
 * @property StorageUsageCustomerModel $storageUsageCustomer
 * @property CustomerStorageCapacityModel $customerStorageCapacity
 * @property PeopleModel $people
 * @property ReportStorageModel $reportStorage
 * @property StatusHistoryModel $statusHistory
 * @property NotificationModel $notification
 */
class Storage_usage extends MY_Controller
{
    /**
     * Storage_usage constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BranchModel', 'branch');
        $this->load->model('StorageUsageModel', 'storageUsage');
        $this->load->model('StorageUsageCustomerModel', 'storageUsageCustomer');
        $this->load->model('CustomerStorageCapacityModel', 'customerStorageCapacity');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('ReportStorageModel', 'reportStorage');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('NotificationModel', 'notification');

        $this->load->model('notifications/OverSpaceM2CustomerNotification');

        $this->setFilterMethods([
            'ajax_get_data' => 'GET',
            'skip_all' => 'POST|PUT',
            'approve_all' => 'POST|PUT',
        ]);
    }

    /**
     * Show storage usage data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_VIEW);

        $this->render('storage_usage/index', []);
    }

    /**
     * Get ajax datatable storage usage.
     */
    public function ajax_get_data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_VIEW);

        $filters = array_merge($_GET, [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);

        $data = $this->storageUsage->getAll($filters);

        $this->render_json($data);
    }

    /**
     * Show detail storage usage.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_VIEW);

        $storageUsage = $this->storageUsage->getById($id);
        $storageUsageCustomers = $this->storageUsageCustomer->getBy(['id_storage_usage' => $id]);

        $this->render('storage_usage/view', compact('storageUsage', 'storageUsageCustomers'));
    }

    /**
     * Show form create storage usage.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_CREATE);

        $this->render('storage_usage/create');
    }

    /**
     * Save new storage usage data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_CREATE);

        if ($this->validate()) {
            $branchId = if_empty($this->input->post('branch'), get_active_branch_id());
            $date = format_date($this->input->post('date'));
            $description = $this->input->post('description');

            $branch = $this->branch->getById($branchId);

            $customers = $this->customerStorageCapacity->getAll(['branch' => $branch['id']]);
            $customerIds = array_unique(array_column($customers, 'id_customer'));
            $customerStorages75 = [];
            foreach ($customerIds as $customerId) {
                $customer = $this->people->getById($customerId);
                if (empty($customer['id_parent'])) {
                    $balances = $this->reportStorage->getCustomerBalanceStorage([
                        'customer' => $customerId,
                        'customer_include_member' => true,
                        'data' => 'stock',
                        'stock_date' => $date
                    ]);

                    if (!empty($balances)) {
                        $currentBalance = $balances[0];
                        $warehousePercent = $currentBalance['used_warehouse_percent'] >= 75;
                        $yardPercent = $currentBalance['used_yard_percent'] >= 75;
                        $coveredYardPercent = $currentBalance['used_covered_yard_percent'] >= 75;
                        if ($warehousePercent || $yardPercent || $coveredYardPercent) {
                            $customerStorages75[] = $currentBalance;
                        }
                    }
                }
            }

            if (!empty($customerStorages75)) {
                $this->db->trans_start();

                $this->storageUsage->create([
                    'id_branch' => $branch['id'],
                    'date' => $date,
                    'status' => StorageUsageModel::STATUS_PENDING,
                    'description' => $description
                ]);
                $storageUsageId = $this->db->insert_id();

                $this->statusHistory->create([
                    'id_reference' => $storageUsageId,
                    'type' => StatusHistoryModel::TYPE_STORAGE_USAGE,
                    'status' => StorageUsageModel::STATUS_PENDING,
                    'description' => $description,
                ]);

                foreach ($customerStorages75 as $customerStorage) {
                    $this->storageUsageCustomer->create([
                        'id_storage_usage' => $storageUsageId,
                        'id_customer' => $customerStorage['id_customer'],
                        'warehouse_capacity' => $customerStorage['warehouse_capacity'],
                        'yard_capacity' => $customerStorage['yard_capacity'],
                        'covered_yard_capacity' => $customerStorage['covered_yard_capacity'],
                        'warehouse_usage' => $customerStorage['used_warehouse_storage'],
                        'yard_usage' => $customerStorage['used_yard_storage'],
                        'covered_yard_usage' => $customerStorage['used_covered_yard_storage'],
                        'status' => StorageUsageCustomerModel::STATUS_PENDING
                    ]);
                    $storageUsageCustomerId = $this->db->insert_id();

                    $this->statusHistory->create([
                        'id_reference' => $storageUsageCustomerId,
                        'type' => StatusHistoryModel::TYPE_STORAGE_USAGE_CUSTOMER,
                        'status' => StorageUsageCustomerModel::STATUS_PENDING,
                        'description' => 'Capture customer storage usage',
                    ]);
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Storage usage {$date} successfully created", 'storage-usage');
                } else {
                    flash('danger', 'Something is getting wrong, try again or contact administrator');
                }
            } else {
                flash('danger', "No customer use space more than or equal 75% at {$date}");
            }
        }
        $this->create();
    }

    /**
     * Perform deleting storage usage data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AUCTION_DELETE);

        $storageUsage = $this->storageUsage->getById($id);

        if ($this->storageUsage->delete($id, true)) {
            flash('warning', "Captured storage usage {$storageUsage['date']} is successfully deleted");
        } else {
            flash('danger', "Delete captured storage usage {$storageUsage['date']} failed");
        }
        redirect('storage-usage');
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    protected function _validation_rules(...$params)
    {
        $id = isset($params[0]) ? $params[0] : 0;

        return [
            'date' => [
                'required', 'max_length[20]', ['date_exists', function ($date) use ($id) {
                    $this->form_validation->set_message('date_exists', 'The %s is already exist before');
                    return empty($this->storageUsage->getBy([
                        'storage_usages.id_branch' => get_active_branch_id(),
                        'storage_usages.date' => format_date($date),
                        'storage_usages.id!=' => $id
                    ]));
                }]
            ],
            'description' => 'trim|max_length[500]',
        ];
    }

    /**
     * Validate all storage usage data.
     *
     * @param $id
     */
    public function approve_all($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_VALIDATE);

        $message = $this->input->post('message');

        $storageUsage = $this->storageUsage->getById($id);
        $storageUsageCustomers = $this->storageUsageCustomer->getBy(['id_storage_usage' => $id]);

        $this->db->trans_start();

        $this->proceedStorageUsage($id, $message);

        foreach ($storageUsageCustomers as $storageUsageCustomer) {
            if ($storageUsageCustomer['status'] != StorageUsageCustomerModel::STATUS_PENDING) continue;

            $this->storageUsageCustomer->update([
                'status' => StorageUsageCustomerModel::STATUS_VALIDATED,
            ], $storageUsageCustomer['id']);

            $this->statusHistory->create([
                'id_reference' => $storageUsageCustomer['id'],
                'type' => StatusHistoryModel::TYPE_STORAGE_USAGE_CUSTOMER,
                'status' => StorageUsageCustomerModel::STATUS_VALIDATED,
                'description' => if_empty($message, 'Customer storage usage is validated'),
            ]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            foreach ($storageUsageCustomers as $storageUsageCustomer) {
                $customer = $this->people->getById($storageUsageCustomer['id_customer']);
                $customerBranch = $this->people->getPeopleByIdCustomerIdBranch($customer['id'], if_empty(get_active_branch_id(), $customer['id_branch']));
                if (!empty($customerBranch['whatsapp_group'])) {
                    $customer['whatsapp_group'] = $customerBranch['whatsapp_group'];
                    $this->notification
                        ->via([Notify::CHAT_PUSH])
                        ->to($customer['whatsapp_group'])
                        ->send(new OverSpaceM2CustomerNotification($storageUsageCustomer));
                }
            }

            flash('success', "Storage usage date {$storageUsage['date']} is successfully validated");
        } else {
            flash('danger', 'Validating storage usage failed');
        }

        redirect('storage-usage/view/' . $id);
    }

    /**
     * Skip all storage usage data.
     *
     * @param $id
     */
    public function skip_all($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_VALIDATE);

        $message = $this->input->post('message');

        $storageUsage = $this->storageUsage->getById($id);
        $storageUsageCustomers = $this->storageUsageCustomer->getBy(['id_storage_usage' => $id]);

        $this->db->trans_start();

        $this->proceedStorageUsage($id, $message);

        foreach ($storageUsageCustomers as $storageUsageCustomer) {
            if ($storageUsageCustomer['status'] != StorageUsageCustomerModel::STATUS_PENDING) continue;

            $this->storageUsageCustomer->update([
                'status' => StorageUsageCustomerModel::STATUS_SKIPPED,
            ], $storageUsageCustomer['id']);

            $this->statusHistory->create([
                'id_reference' => $storageUsageCustomer['id'],
                'type' => StatusHistoryModel::TYPE_STORAGE_USAGE_CUSTOMER,
                'status' => StorageUsageCustomerModel::STATUS_SKIPPED,
                'description' => if_empty($message, 'Customer storage usage is skipped'),
            ]);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            flash('success', "Storage usage date {$storageUsage['date']} is successfully validated");
        } else {
            flash('danger', 'Validating storage usage failed');
        }

        redirect('storage-usage/view/' . $id);
    }

    /**
     * Update storage usage to proceed.
     *
     * @param $id
     * @param $message
     */
    private function proceedStorageUsage($id, $message)
    {
        $this->storageUsage->update([
            'status' => StorageUsageModel::STATUS_PROCEED
        ], $id);

        $this->statusHistory->create([
            'id_reference' => $id,
            'type' => StatusHistoryModel::TYPE_STORAGE_USAGE,
            'status' => StorageUsageModel::STATUS_PROCEED,
            'description' => if_empty($message, 'Storage usage is proceed all'),
        ]);
    }

}