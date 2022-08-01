<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Storage_over_space
 * @property BranchModel $branch
 * @property StorageOverSpaceModel $storageOverSpace
 * @property StorageOverSpaceCustomerModel $storageOverSpaceCustomer
 * @property StorageOverSpaceActivityModel $storageOverSpaceActivity
 * @property PeopleModel $people
 * @property ReportStorageModel $reportStorage
 * @property StatusHistoryModel $statusHistory
 * @property NotificationModel $notification
 * @property Exporter $exporter
 */
class Storage_over_space extends MY_Controller
{
    /**
     * Storage_usage constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BranchModel', 'branch');
        $this->load->model('StorageOverSpaceModel', 'storageOverSpace');
        $this->load->model('StorageOverSpaceCustomerModel', 'storageOverSpaceCustomer');
        $this->load->model('StorageOverSpaceActivityModel', 'storageOverSpaceActivity');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('ReportStorageModel', 'reportStorage');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('NotificationModel', 'notification');
        $this->load->model('notifications/OverSpaceM2SummaryNotification');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'weekly' => 'GET',
            'monthly' => 'GET',
            'ajax_get_data' => 'GET',
            'print_over_space' => 'GET',
            'skip_all' => 'POST|PUT',
            'approve_all' => 'POST|PUT',
        ]);
    }

    /**
     * Show storage over space data list.
     */
    public function weekly()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_VIEW);

        $this->render('storage_over_space/weekly', []);
    }

    /**
     * Show storage over space data list.
     */
    public function monthly()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_VIEW);

        $this->render('storage_over_space/monthly', []);
    }

    /**
     * Get ajax datatable storage over space.
     *
     * @param $type
     */
    public function ajax_get_data($type)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_VIEW);

        $filters = array_merge($_GET, [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir'],
            'type' => $type
        ]);

        $data = $this->storageOverSpace->getAll($filters);

        $this->render_json($data);
    }

    /**
     * Show detail storage over space.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_VIEW);

        $storageOverSpace = $this->storageOverSpace->getById($id);
        $storageOverSpaceCustomers = $this->storageOverSpaceCustomer->getBy(['id_storage_over_space' => $id]);
        $statusHistories = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_STORAGE_OVER_SPACE,
            'status_histories.id_reference' => $id
        ]);

        $this->render('storage_over_space/view', compact('storageOverSpace', 'storageOverSpaceCustomers', 'statusHistories'));
    }

    /**
     * Perform deleting storage over space data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_DELETE);

        $storageOverSpace = $this->storageOverSpace->getById($id);

        if ($this->storageOverSpace->delete($id, true)) {
            flash('warning', "Captured storage over space {$storageOverSpace['date_from']} {$storageOverSpace['date_to']} is successfully deleted", '_back');
        } else {
            flash('danger', "Delete captured storage over space {$storageOverSpace['date_from']} {$storageOverSpace['date_to']} failed", '_back');
        }
    }

    public function print_over_space($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_VIEW);

        $storageOverSpace = $this->storageOverSpace->getById($id);
        $storageOverSpaceCustomers = $this->storageOverSpaceCustomer->getOverSpaceCustomerWithLastStorage($id);
        $page = $this->load->view('storage_over_space/print', compact('storageOverSpace', 'storageOverSpaceCustomers'), true);

        $fileName = 'Storage over space ' . $storageOverSpace['type'] . ' ' . $storageOverSpace['date_from'] . '-' . $storageOverSpace['date_to'];
        $this->exporter->exportToPdf($fileName, $page);
    }

    /**
     * Validate all storage over space data.
     *
     * @param $id
     */
    public function approve_all($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_VALIDATE);

        $message = $this->input->post('message');

        $storageOverSpace = $this->storageOverSpace->getById($id);
        $storageOverSpaceCustomers = $this->storageOverSpaceCustomer->getBy(['id_storage_over_space' => $id]);

        $this->db->trans_start();

        $this->proceedStorageOverSpace($id, $message);

        $notifiedOverSpaceCustomers = [];
        foreach ($storageOverSpaceCustomers as $storageOverSpaceCustomer) {
            if ($storageOverSpaceCustomer['status'] != StorageOverSpaceCustomerModel::STATUS_PENDING) continue;

            $this->storageOverSpaceCustomer->update([
                'status' => StorageOverSpaceCustomerModel::STATUS_VALIDATED,
            ], $storageOverSpaceCustomer['id']);

            $this->statusHistory->create([
                'id_reference' => $storageOverSpaceCustomer['id'],
                'type' => StatusHistoryModel::TYPE_STORAGE_OVER_SPACE_CUSTOMER,
                'status' => StorageOverSpaceCustomerModel::STATUS_VALIDATED,
                'description' => if_empty($message, 'Customer storage usage is validated'),
            ]);

            $notifiedOverSpaceCustomers[] = $storageOverSpaceCustomer;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            foreach ($notifiedOverSpaceCustomers as $storageOverSpaceCustomer) {
                $this->sendOverSpaceEmail(true, $storageOverSpaceCustomer);
            }
            $this->sendOverSpaceInternalNotification($id);
            flash('success', "Storage usage date {$storageOverSpace['date']} is successfully validated");
        } else {
            flash('danger', 'Validating storage usage failed');
        }

        redirect('storage-over-space/view/' . $id);
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

        $storageOverSpace = $this->storageOverSpace->getById($id);
        $storageOverSpaceCustomers = $this->storageOverSpaceCustomer->getBy(['id_storage_over_space' => $id]);

        $this->db->trans_start();

        $this->proceedStorageOverSpace($id, $message);

        $notifiedOverSpaceCustomers = [];
        foreach ($storageOverSpaceCustomers as $storageOverSpaceCustomer) {
            if ($storageOverSpaceCustomer['status'] != StorageOverSpaceCustomerModel::STATUS_PENDING) continue;

            $this->storageOverSpaceCustomer->update([
                'status' => StorageOverSpaceCustomerModel::STATUS_SKIPPED,
            ], $storageOverSpaceCustomer['id']);

            $this->statusHistory->create([
                'id_reference' => $storageOverSpaceCustomer['id'],
                'type' => StatusHistoryModel::TYPE_STORAGE_OVER_SPACE_CUSTOMER,
                'status' => StorageOverSpaceCustomerModel::STATUS_SKIPPED,
                'description' => if_empty($message, 'Customer storage usage is skipped'),
            ]);

            $notifiedOverSpaceCustomers[] = $storageOverSpaceCustomer;
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            foreach ($notifiedOverSpaceCustomers as $storageOverSpaceCustomer) {
                $this->sendOverSpaceEmail(false, $storageOverSpaceCustomer);
            }
            $this->sendOverSpaceInternalNotification($id);
            flash('success', "Storage usage date {$storageOverSpace['date']} is successfully validated");
        } else {
            flash('danger', 'Validating storage usage failed');
        }

        redirect('storage-over-space/view/' . $id);
    }

    /**
     * Send email to customer and internal each validation.
     *
     * @param $withCustomer
     * @param $storageOverSpaceCustomer
     */
    private function sendOverSpaceEmail($withCustomer, $storageOverSpaceCustomer)
    {
        $storageData = ['WAREHOUSE' => [], 'YARD' => [], 'COVERED YARD' => []];
        foreach ($storageData as $type => &$data) {
            $data = $this->storageOverSpaceActivity->getBy([
                'id_storage_over_space_customer' => $storageOverSpaceCustomer['id'],
                'warehouse_type' => $type,
            ]);
        }

        $branch = get_active_branch();
        $emailOperational = explode(",", $branch['email_operational']);
        $emailCommercial = explode(",", $branch['email_support']);

        // default set to customer, fallback to internal email
        $to = $storageOverSpaceCustomer['customer_email'];
        $emailFinance = ['fin2@transcon-indonesia.com', 'findata@transcon-id.com'];
        if (empty($to)) {
            $to = 'acc_mgr@transcon-indonesia.com';
        } else {
            $emailFinance[] = 'acc_mgr@transcon-indonesia.com';
        }

        // without customer try to send internal only
        if (!$withCustomer) {
            $to = 'acc_mgr@transcon-indonesia.com';
        }

        $emailOption = ['cc' => array_merge($emailFinance, $emailOperational, $emailCommercial)];
        $this->notification
            ->via([Notify::MAIL_PUSH])
            ->to($to)
            ->send(new OverSpaceM2SummaryNotification($storageOverSpaceCustomer, $storageData, $emailOption));
    }

    /**
     * Send chat notification with pdf report end of validation.
     *
     * @param $storageOverSpaceId
     */
    public function sendOverSpaceInternalNotification($storageOverSpaceId)
    {
        $storageOverSpace = $this->storageOverSpace->getById($storageOverSpaceId);
        if ($storageOverSpace['status'] == StorageOverSpaceModel::STATUS_PROCEED) {
            $managementGroup = get_setting('whatsapp_group_management');

            $storageOverSpaceCustomers = $this->storageOverSpaceCustomer->getOverSpaceCustomerWithLastStorage($storageOverSpaceId);
            $page = $this->load->view('storage_over_space/print', compact('storageOverSpace', 'storageOverSpaceCustomers'), true);
            $fileName = 'Storage over space ' . $storageOverSpace['type'] . ' ' . $storageOverSpace['date_from'] . '-' . $storageOverSpace['date_to'];
            $pdf = $this->exporter->exportToPdf($fileName, $page, ['buffer' => true]);
            $pdfFileName = url_title($fileName) . '.pdf';
            file_put_contents(FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . $pdfFileName, $pdf);

            if (!empty($managementGroup)) {
                $message = "*Over Space " . $storageOverSpace['type'] . " branch " . $storageOverSpace['branch'] . " ({$storageOverSpace['date_from']} - {$storageOverSpace['date_to']})*\n";
                $this->notification->broadcast([
                    'url' => 'sendFile',
                    'method' => 'POST',
                    'payload' => [
                        'chatId' => detect_chat_id($managementGroup),
                        'body' => base_url('uploads/temp/' . $pdfFileName),
                        'filename' => "Over Space {$storageOverSpace['type']}.pdf",
                        'caption' => $message
                    ]
                ], NotificationModel::TYPE_CHAT_PUSH);
            }
        }
    }

    /**
     * Update storage over space to proceed.
     *
     * @param $id
     * @param $message
     */
    private function proceedStorageOverSpace($id, $message)
    {
        $this->storageOverSpace->update([
            'status' => StorageOverSpaceModel::STATUS_PROCEED
        ], $id);

        $this->statusHistory->create([
            'id_reference' => $id,
            'type' => StatusHistoryModel::TYPE_STORAGE_OVER_SPACE,
            'status' => StorageOverSpaceModel::STATUS_PROCEED,
            'description' => if_empty($message, 'Storage over space is proceed all'),
        ]);
    }

}