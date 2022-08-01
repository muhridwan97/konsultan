<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Storage_over_space_customer
 * @property StorageOverSpaceModel $storageOverSpace
 * @property StorageOverSpaceCustomerModel $storageOverSpaceCustomer
 * @property StorageOverSpaceActivityModel $storageOverSpaceActivity
 * @property PeopleModel $people
 * @property ReportStorageModel $reportStorage
 * @property StatusHistoryModel $statusHistory
 * @property NotificationModel $notification
 * @property Exporter $exporter
 */
class Storage_over_space_customer extends MY_Controller
{
    /**
     * Storage_over_space_customer constructor.
     */
    public function __construct()
    {
        parent::__construct();

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
            'approve' => 'POST|PUT',
            'skip' => 'POST|PUT',
        ]);
    }

    /**
     * Show detail storage over space.
     *
     * @param $id
     * @param string $type
     */
    public function view($id, $type = 'WAREHOUSE')
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_VIEW);

        $storageOverSpaceCustomer = $this->storageOverSpaceCustomer->getById($id);
        $storageOverSpaceActivities = $this->storageOverSpaceActivity->getBy([
            'id_storage_over_space_customer' => $id,
            'warehouse_type' => urldecode($type),
        ]);

        if (get_url_param('export')) {
            $this->reportStorage->exportCustomerStorageActivity($storageOverSpaceActivities);
        } else {
            $statusHistories = $this->statusHistory->getBy([
                'status_histories.type' => StatusHistoryModel::TYPE_STORAGE_OVER_SPACE_CUSTOMER,
                'status_histories.id_reference' => $id
            ]);

            $this->render('storage_over_space_customer/view', compact('storageOverSpaceCustomer', 'storageOverSpaceActivities', 'statusHistories'));
        }
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

        $storageOverSpaceCustomer = $this->storageOverSpaceCustomer->getById($id);

        $this->db->trans_start();

        $this->storageOverSpaceCustomer->update([
            'status' => StorageOverSpaceCustomerModel::STATUS_VALIDATED,
        ], $id);

        $this->statusHistory->create([
            'id_reference' => $id,
            'type' => StatusHistoryModel::TYPE_STORAGE_OVER_SPACE_CUSTOMER,
            'status' => StorageOverSpaceCustomerModel::STATUS_VALIDATED,
            'description' => if_empty($message, 'Customer storage over space is validated'),
        ]);

        $this->checkStorageOverSpaceStatus($storageOverSpaceCustomer['id_storage_over_space']);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $this->sendOverSpaceEmail(true, $storageOverSpaceCustomer);
            $this->sendOverSpaceInternalNotification($storageOverSpaceCustomer['id_storage_over_space']);

            flash('success', "Storage over space {$storageOverSpaceCustomer['customer_name']} is successfully validated");
        } else {
            flash('danger', 'Validating storage over space failed');
        }

        redirect('storage-over-space/view/' . $storageOverSpaceCustomer['id_storage_over_space']);
    }

    /**
     * Skip storage over space data.
     *
     * @param $id
     */
    public function skip($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_VALIDATE);

        $message = $this->input->post('message');

        $storageOverSpaceCustomer = $this->storageOverSpaceCustomer->getById($id);

        $this->db->trans_start();

        $this->storageOverSpaceCustomer->update([
            'status' => StorageOverSpaceCustomerModel::STATUS_SKIPPED,
        ], $id);

        $this->statusHistory->create([
            'id_reference' => $id,
            'type' => StatusHistoryModel::TYPE_STORAGE_OVER_SPACE_CUSTOMER,
            'status' => StorageOverSpaceCustomerModel::STATUS_SKIPPED,
            'description' => if_empty($message, 'Customer storage over space is skipped'),
        ]);

        $this->checkStorageOverSpaceStatus($storageOverSpaceCustomer['id_storage_over_space']);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $this->sendOverSpaceEmail(false, $storageOverSpaceCustomer);
            $this->sendOverSpaceInternalNotification($storageOverSpaceCustomer['id_storage_over_space']);

            flash('success', "Storage over space {$storageOverSpaceCustomer['customer_name']} is successfully skipped");
        } else {
            flash('danger', 'Validating storage over space failed');
        }

        redirect('storage-over-space/view/' . $storageOverSpaceCustomer['id_storage_over_space']);
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
     * Check if all storage over space customer is validate.
     *
     * @param $id
     */
    private function checkStorageOverSpaceStatus($id)
    {
        $storageOverSpaceCustomers = $this->storageOverSpaceCustomer->getBy(['id_storage_over_space' => $id]);

        $isPending = false;
        foreach ($storageOverSpaceCustomers as $storageOverSpaceCustomer) {
            if ($storageOverSpaceCustomer['status'] == StorageOverSpaceModel::STATUS_PENDING) {
                $isPending = true;
                break;
            }
        }

        if (!$isPending) {
            $this->storageOverSpace->update([
                'status' => StorageOverSpaceModel::STATUS_PROCEED
            ], $id);

            $this->statusHistory->create([
                'id_reference' => $id,
                'type' => StatusHistoryModel::TYPE_STORAGE_OVER_SPACE,
                'status' => StorageOverSpaceModel::STATUS_PROCEED,
                'description' => 'Storage over space is proceed',
            ]);
        }
    }

}