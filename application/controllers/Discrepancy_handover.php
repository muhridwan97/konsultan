<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Discrepancy_handover
 * @property DiscrepancyHandoverModel $discrepancyHandover
 * @property DiscrepancyHandoverGoodsModel $discrepancyHandoverGoods
 * @property NotificationModel $notification
 * @property StatusHistoryModel $statusHistory
 * @property PeopleModel $people
 * @property PeopleBranchModel $peopleBranch
 * @property PeopleUserModel $peopleUser
 * @property UserTokenModel $userToken
 * @property BookingModel $booking
 * @property BranchModel $branch
 * @property UploadDocumentModel $uploadDocument
 * @property UploadDocumentFileModel $uploadDocumentFile
 * @property Uploader $uploader
 * @property Exporter $exporter
 * @property Mailer $mailer
 */
class Discrepancy_handover extends MY_Controller
{
    /**
     * Discrepancy_handover constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('DiscrepancyHandoverModel', 'discrepancyHandover');
        $this->load->model('DiscrepancyHandoverGoodsModel', 'discrepancyHandoverGoods');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('NotificationModel', 'notification');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('PeopleBranchModel', 'peopleBranch');
        $this->load->model('PeopleUserModel', 'peopleUser');
        $this->load->model('UserTokenModel', 'userToken');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('BranchModel', 'branch');
        $this->load->model('UploadDocumentModel', 'uploadDocument');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
        $this->load->model('modules/Uploader', 'uploader');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('modules/Mailer', 'mailer');

        $this->setFilterMethods([
            'data' => 'GET',
            'upload' => 'POST|PUT',
            'resend_confirm_email' => 'POST|PUT',
            'not_use' => 'POST|PUT',
            'in_use' => 'POST|PUT',
            'cancel' => 'POST|PUT',
            'document' => 'POST|PUT',
            'physical' => 'POST|PUT',
            'print_discrepancy_handover' => 'GET',
        ]);
    }

    /**
     * Show discrepancy handover data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DISCREPANCY_HANDOVER_VIEW);

        if ($this->input->get('export')) {
            $reports = $this->discrepancyHandover->getAll($_GET);
            $this->exporter->exportLargeResourceFromArray('Discrepancy Handover', $reports);
        } else {
            $customer = $this->people->getById($this->input->get('customer'));

            $this->render('discrepancy_handover/index', compact('customer'));
        }
    }

    /**
     * Get ajax datatable.
     */
    public function data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DISCREPANCY_HANDOVER_VIEW);

        $filters = array_merge(get_url_param('filter') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);

        $discrepancyHandovers = $this->discrepancyHandover->getAll($filters);

        foreach ($discrepancyHandovers['data'] as &$datum) {
            $datum['attachment_url'] = empty($datum['attachment']) ? null : asset_url($datum['attachment']);
        }

        $this->render_json($discrepancyHandovers);
    }

    /**
     * Show view discrepancy handover form.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DISCREPANCY_HANDOVER_VIEW);

        $discrepancyHandover = $this->discrepancyHandover->getById($id);
        $discrepancyHandoverGoods = $this->discrepancyHandoverGoods->getBy(['id_discrepancy_handover' => $id]);
        $statusHistories = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_DISCREPANCY_HANDOVER,
            'status_histories.id_reference' => $id
        ]);

        $this->render('discrepancy_handover/view', compact('discrepancyHandover', 'discrepancyHandoverGoods', 'statusHistories'));
    }

    /**
     * Print discrepancy handover.
     *
     * @param $id
     */
    public function print_discrepancy_handover($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DISCREPANCY_HANDOVER_VIEW);

        $discrepancyHandover = $this->discrepancyHandover->getById($id);
        $discrepancyHandoverGoods = $this->discrepancyHandoverGoods->getBy(['id_discrepancy_handover' => $id]);

        $page = $this->load->view('discrepancy_handover/print', compact('discrepancyHandover', 'discrepancyHandoverGoods'), true);

        $this->exporter->exportToPdf('Discrepancy ' . url_title($discrepancyHandover['no_discrepancy']), $page);
    }

    /**
     * Upload discrepancy handover data.
     *
     * @param $id
     */
    public function upload($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DISCREPANCY_HANDOVER_EDIT);

        $discrepancyHandover = $this->discrepancyHandover->getById($id);

        $uploadFile = true;
        $uploadedFile = if_empty($discrepancyHandover['attachment'], null);
        if (!empty($_FILES['attachment']['name'])) {
            $uploadFile = $this->uploader->setDriver('s3')->uploadTo('attachment', [
                'destination' => 'discrepancy-handover/' . date('Y/m')
            ]);
            if ($uploadFile) {
                $uploadedData = $this->uploader->getUploadedData();
                $uploadedFile = $uploadedData['uploaded_path'];
                if (!empty($discrepancyHandover['attachment'])) {
                    //$this->uploader->setDriver('s3')->delete($discrepancyHandover['attachment']);
                }
            } else {
                flash('danger', $this->uploader->getDisplayErrors());
            }
        }

        if ($uploadFile) {
            $booking = $this->booking->getById($discrepancyHandover['id_booking']);
            $description = $this->input->post('description');

            $this->db->trans_start();

            $this->discrepancyHandover->update([
                'attachment' => $uploadedFile,
                'status' => $discrepancyHandover['status'] == 'PENDING' ? DiscrepancyHandoverModel::STATUS_UPLOADED : $discrepancyHandover['status']
            ], $id);

            $this->statusHistory->create([
                'id_reference' => $id,
                'type' => StatusHistoryModel::TYPE_DISCREPANCY_HANDOVER,
                'status' => DiscrepancyHandoverModel::STATUS_UPLOADED,
                'description' => if_empty($description, 'Handover uploaded'),
            ]);

            $existingUploadDocument = $this->uploadDocument->getBy([
                'upload_documents.id_upload' => $booking['id_upload'],
                'upload_documents.id_document_type' => 194,
            ], true);

            if (empty($existingUploadDocument)) {
                $this->uploadDocument->create([
                    'id_upload' => $booking['id_upload'],
                    'id_document_type' => 194, // discrepancy handover id
                    'no_document' => $discrepancyHandover['no_discrepancy'],
                    'document_date' => format_date($discrepancyHandover['created_at']),
                    'is_valid' => true,
                    'is_response' => true,
                    'validated_at' => date('Y-m-d'),
                    'validated_by' => UserModel::authenticatedUserData('id')
                ]);
                $uploadDocumentId = $this->db->insert_id();
            } else {
                $this->uploadDocument->update([
                    'no_document' => $discrepancyHandover['no_discrepancy'],
                    'document_date' => format_date($discrepancyHandover['created_at']),
                    'validated_at' => date('Y-m-d H:i:s'),
                    'validated_by' => UserModel::authenticatedUserData('id')
                ], $existingUploadDocument['id']);
                $uploadDocumentId = $existingUploadDocument['id'];
            }

            if ($discrepancyHandover['attachment'] != $uploadedFile) {
                $this->uploadDocumentFile->createUploadDocumentFile([
                    'id_upload_document' => $uploadDocumentId,
                    'source' => $uploadedFile,
                    'description' => $description,
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                $discrepancyHandover = $this->discrepancyHandover->getById($id);
                $fileName = url_title($discrepancyHandover['no_discrepancy']) . '.' . pathinfo($discrepancyHandover['attachment'], PATHINFO_EXTENSION);
                $caption = "Discrepancy Handover {$discrepancyHandover['no_reference']}: " . $fileName;

                $branch = $this->branch->getById($discrepancyHandover['id_branch']);
                $branchGroup = $branch['whatsapp_group'];

                // send to branch group
                $result = $this->notification->broadcast([
                    'url' => 'sendFile',
                    'method' => 'POST',
                    'payload' => [
                        'chatId' => detect_chat_id($branchGroup),
                        'body' => asset_url($discrepancyHandover['attachment']),
                        'filename' => $fileName,
                        'caption' => $caption
                    ]
                ], NotificationModel::TYPE_CHAT_PUSH);

                // send to customer group
                $peopleBranch = $this->peopleBranch->getBy([
                    'id_customer' => $discrepancyHandover['id_customer'],
                    'id_branch' => $discrepancyHandover['id_branch']
                ], true);
                if (!empty($peopleBranch['whatsapp_group'])) {
                    $customer = $this->people->getById($discrepancyHandover['id_customer']);
                    $confirmationEmail = $customer['email'];
                    if ($customer['confirm_email_source'] == 'USER') {
                        $peopleUsers = $this->peopleUser->getUserByPerson($customer['id']);
                        $confirmationEmail = implode(',', array_column($peopleUsers, 'email'));
                    }

                    // send message about handover and ask for confirmation
                    $chatMessage = "*DISCREPANCY IS [UPLOADED]*\n";
                    $chatMessage .= "————————————————\n";
                    $chatMessage .= "*Attachment:* {$fileName}\n";
                    $chatMessage .= "*Customer:* {$discrepancyHandover['customer_name']}\n";
                    $chatMessage .= "*No Reference:* {$discrepancyHandover['no_reference']}\n";
                    $chatMessage .= "*Status:* UPLOADED\n";
                    $chatMessage .= "*Total Item:* {$discrepancyHandover['total_discrepancy_item']} items\n";
                    $chatMessage .= "*Confirmation Email:* {$confirmationEmail}";
                    $result = $this->notification->broadcast([
                        'url' => 'sendMessage',
                        'method' => 'POST',
                        'payload' => [
                            'chatId' => detect_chat_id($peopleBranch['whatsapp_group']),
                            'body' => $chatMessage,
                        ]
                    ], NotificationModel::TYPE_CHAT_PUSH);

                    // send actual file handover
                    $this->notification->broadcast([
                        'url' => 'sendFile',
                        'method' => 'POST',
                        'payload' => [
                            'chatId' => detect_chat_id($peopleBranch['whatsapp_group']),
                            'body' => asset_url($discrepancyHandover['attachment']),
                            'filename' => $fileName,
                            'caption' => $caption
                        ]
                    ], NotificationModel::TYPE_CHAT_PUSH);
                }

                $this->resend_email($discrepancyHandover);

                if ($result && !($result['sent'] ?? false)) {
                    flash('warning', "Discrepancy handover {$discrepancyHandover['no_discrepancy']} successfully uploaded, but notification failed to be sent");
                } else {
                    flash('success', "Discrepancy handover {$discrepancyHandover['no_discrepancy']} successfully uploaded");
                }
            } else {
                flash('danger', "Upload discrepancy handover {$discrepancyHandover['no_discrepancy']} failed");
            }
        }
        redirect('discrepancy-handover');
    }

    /**
     * Resent confirmation email.
     *
     * @param $id
     */
    public function resend_confirm_email($id)
    {
        $discrepancyHandover = $this->discrepancyHandover->getById($id);

        if ($this->resend_email($discrepancyHandover)) {
            flash('success', "Discrepancy handover {$discrepancyHandover['no_discrepancy']} confirmation email successfully sent");
        } else {
            flash('warning', "Failed send discrepancy handover {$discrepancyHandover['no_discrepancy']} confirmation email, try again later");
        }
        redirect('discrepancy-handover');
    }

    /**
     * Send actual email handover confirmation.
     *
     * @param $discrepancyHandover
     * @return bool
     */
    private function resend_email($discrepancyHandover)
    {
        if (is_int($discrepancyHandover)) {
            $discrepancyHandover = $this->discrepancyHandover->getById($discrepancyHandover);
        }
        $customer = $this->people->getById($discrepancyHandover['id_customer']);
        if ($customer['confirm_email_source'] == 'USER') {
            $peopleUsers = $this->peopleUser->getUserByPerson($customer['id']);
            //$confirmationEmails = implode(',', array_column($peopleUsers, 'email'));
        } else {
            $peopleUsers = $this->peopleUser->getUserByPerson($customer['id']);
            if (!empty($peopleUsers)) {
                // consider for multiple user take only one because we will notify PROFILE email based on setup confirm_email_source
                $peopleUser = $peopleUsers[0];
                $peopleUser['email'] = $customer['email']; // replace email user from customer profile
                $peopleUsers = [$peopleUser];
            }
            //$confirmationEmails = $customer['email'];
        }

        $results = [];
        foreach ($peopleUsers as $user) {
            $token = $this->userToken->createToken($user['email'], UserTokenModel::$TOKEN_CONFIRMATION, 32, 1, null, false);
            $explainLink = site_url("discrepancy-handover-confirmation/explain/{$token}?id={$discrepancyHandover['id']}&email={$user['email']}");
            $explainLinkShorten = if_empty(get_tiny_url($explainLink), $explainLink);
            $confirmationLink = site_url("discrepancy-handover-confirmation/confirm/{$token}?id={$discrepancyHandover['id']}&email={$user['email']}");
            $confirmationLinkShorten = if_empty(get_tiny_url($confirmationLink), $confirmationLink);

            $emailTitle = "Discrepancy handover confirmation {$discrepancyHandover['no_discrepancy']} - {$discrepancyHandover['no_reference']}";
            $emailTemplate = 'emails/discrepancy_handover_confirmation';
            $emailData = [
                'email' => $user['email'],
                'discrepancyHandover' => $discrepancyHandover,
                'explainLink' => $explainLinkShorten,
                'confirmationLink' => $confirmationLinkShorten,
                'token' => $token,
            ];

            $emailOption = [
                'attachment' => [
                    ['source' => asset_url($discrepancyHandover['attachment'])]
                ],
            ];
            $results[] = $this->mailer->send($user['email'], $emailTitle, $emailTemplate, $emailData, $emailOption);
        }

        if (array_sum($results) == count($peopleUsers)) {
            return true;
        }

        return false;
    }

    /**
     * Set as not use discrepancy handover data.
     *
     * @param $id
     */
    public function not_use($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DISCREPANCY_HANDOVER_VALIDATE);

        $discrepancyHandover = $this->discrepancyHandover->getById($id);

        $message = $this->input->post('message');

        $this->db->trans_start();

        $this->discrepancyHandover->update([
            'status' => DiscrepancyHandoverModel::STATUS_NOT_USE
        ], $id);

        $this->statusHistory->create([
            'id_reference' => $id,
            'type' => StatusHistoryModel::TYPE_DISCREPANCY_HANDOVER,
            'status' => DiscrepancyHandoverModel::STATUS_NOT_USE,
            'description' => if_empty($message, 'Handover is not used'),
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            // send to branch group
            $branch = $this->branch->getById($discrepancyHandover['id_branch']);
            $chatMessage = "*DISCREPANCY SET AS [NOT USED]*\n";
            $chatMessage .= "———————————————————\n";
            $chatMessage .= "*No Discrepancy:* {$discrepancyHandover['no_discrepancy']}\n";
            $chatMessage .= "*Customer:* {$discrepancyHandover['customer_name']}\n";
            $chatMessage .= "*No Reference:* {$discrepancyHandover['no_reference']}\n";
            $chatMessage .= "*Status:* NOT USED\n";
            $chatMessage .= "*Note:* {$message}\n\n";
            $chatMessage .= "ℹ️ You can ignore or set other handover status as cancelled discrepancy document";
            $this->notification->broadcast([
                'url' => 'sendMessage',
                'method' => 'POST',
                'payload' => [
                    'chatId' => detect_chat_id($branch['whatsapp_group']),
                    'body' => $chatMessage,
                ]
            ], NotificationModel::TYPE_CHAT_PUSH);

            flash('warning', "Discrepancy handover {$discrepancyHandover['no_discrepancy']} successfully set as not used");
        } else {
            flash('danger', "Update discrepancy handover {$discrepancyHandover['no_discrepancy']} failed");
        }
        redirect('discrepancy-handover');
    }

    /**
     * Set as in use discrepancy handover data.
     *
     * @param $id
     */
    public function in_use($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DISCREPANCY_HANDOVER_VALIDATE);

        $discrepancyHandover = $this->discrepancyHandover->getById($id);

        $message = $this->input->post('message');

        $this->db->trans_start();

        $this->discrepancyHandover->update([
            'status' => DiscrepancyHandoverModel::STATUS_IN_USE
        ], $id);

        $this->statusHistory->create([
            'id_reference' => $id,
            'type' => StatusHistoryModel::TYPE_DISCREPANCY_HANDOVER,
            'status' => DiscrepancyHandoverModel::STATUS_IN_USE,
            'description' => if_empty($message, 'Handover is used'),
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            // send to branch group
            $branch = $this->branch->getById($discrepancyHandover['id_branch']);
            $chatMessage = "*DISCREPANCY SET AS [IN USE]*\n";
            $chatMessage .= "——————————————————\n";
            $chatMessage .= "*No Discrepancy:* {$discrepancyHandover['no_discrepancy']}\n";
            $chatMessage .= "*Customer:* {$discrepancyHandover['customer_name']}\n";
            $chatMessage .= "*No Reference:* {$discrepancyHandover['no_reference']}\n";
            $chatMessage .= "*Status:* IN USED\n";
            $chatMessage .= "*Note:* {$message}\n\n";
            $chatMessage .= "‼️ Please proceed the next step by upload signed discrepancy document!\n";
            $chatMessage .= site_url('discrepancy-handover/view/' . $discrepancyHandover['id']);
            $this->notification->broadcast([
                'url' => 'sendMessage',
                'method' => 'POST',
                'payload' => [
                    'chatId' => detect_chat_id($branch['whatsapp_group']),
                    'body' => $chatMessage,
                ]
            ], NotificationModel::TYPE_CHAT_PUSH);
            flash('success', "Discrepancy handover {$discrepancyHandover['no_discrepancy']} successfully set as in used");
        } else {
            flash('danger', "Update discrepancy handover {$discrepancyHandover['no_discrepancy']} failed");
        }
        redirect('discrepancy-handover');
    }

    /**
     * Set as document discrepancy handover data.
     *
     * @param $id
     */
    public function document($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DISCREPANCY_HANDOVER_PROCEED);

        $discrepancyHandover = $this->discrepancyHandover->getById($id);

        $message = $this->input->post('message');

        $this->db->trans_start();

        $this->discrepancyHandover->update([
            'status' => DiscrepancyHandoverModel::STATUS_DOCUMENT
        ], $id);

        $this->statusHistory->create([
            'id_reference' => $id,
            'type' => StatusHistoryModel::TYPE_DISCREPANCY_HANDOVER,
            'status' => DiscrepancyHandoverModel::STATUS_DOCUMENT,
            'description' => if_empty($message, 'Update as document'),
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            // send to branch group
            $branch = $this->branch->getById($discrepancyHandover['id_branch']);
            $chatMessage = "*DISCREPANCY SET AS [DOCUMENT]*\n";
            $chatMessage .= "————————————————————\n";
            $chatMessage .= "*No Discrepancy:* {$discrepancyHandover['no_discrepancy']}\n";
            $chatMessage .= "*Customer:* {$discrepancyHandover['customer_name']}\n";
            $chatMessage .= "*No Reference:* {$discrepancyHandover['no_reference']}\n";
            $chatMessage .= "*Status:* DOCUMENT\n";
            $chatMessage .= "*Note:* {$message}\n\n";
            $chatMessage .= "‼️ Please adjust inventory based on document item list!\n";
            $chatMessage .= site_url('discrepancy-handover/view/' . $discrepancyHandover['id']);
            $this->notification->broadcast([
                'url' => 'sendMessage',
                'method' => 'POST',
                'payload' => [
                    'chatId' => detect_chat_id($branch['whatsapp_group']),
                    'body' => $chatMessage,
                ]
            ], NotificationModel::TYPE_CHAT_PUSH);
            flash('success', "Discrepancy handover {$discrepancyHandover['no_discrepancy']} successfully set as document");
        } else {
            flash('danger', "Update discrepancy handover {$discrepancyHandover['no_discrepancy']} failed");
        }
        redirect('discrepancy-handover');
    }


    /**
     * Set as physical discrepancy handover data.
     *
     * @param $id
     */
    public function physical($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DISCREPANCY_HANDOVER_PROCEED);

        $discrepancyHandover = $this->discrepancyHandover->getById($id);

        $message = $this->input->post('message');

        $this->db->trans_start();

        $this->discrepancyHandover->update([
            'status' => DiscrepancyHandoverModel::STATUS_PHYSICAL
        ], $id);

        $this->statusHistory->create([
            'id_reference' => $id,
            'type' => StatusHistoryModel::TYPE_DISCREPANCY_HANDOVER,
            'status' => DiscrepancyHandoverModel::STATUS_PHYSICAL,
            'description' => if_empty($message, 'Update as physical'),
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            // send to branch group
            $branch = $this->branch->getById($discrepancyHandover['id_branch']);
            $chatMessage = "*DISCREPANCY SET AS [PHYSICAL]*\n";
            $chatMessage .= "————————————————————\n";
            $chatMessage .= "*No Discrepancy:* {$discrepancyHandover['no_discrepancy']}\n";
            $chatMessage .= "*Customer:* {$discrepancyHandover['customer_name']}\n";
            $chatMessage .= "*No Reference:* {$discrepancyHandover['no_reference']}\n";
            $chatMessage .= "*Status:* PHYSICAL\n";
            $chatMessage .= "*Note:* {$message}\n\n";
            $chatMessage .= "Leave inventory as it is!\n";
            $chatMessage .= site_url('discrepancy-handover/view/' . $discrepancyHandover['id']);
            $this->notification->broadcast([
                'url' => 'sendMessage',
                'method' => 'POST',
                'payload' => [
                    'chatId' => detect_chat_id($branch['whatsapp_group']),
                    'body' => $chatMessage,
                ]
            ], NotificationModel::TYPE_CHAT_PUSH);
            flash('success', "Discrepancy handover {$discrepancyHandover['no_discrepancy']} successfully set as physical");
        } else {
            flash('danger', "Update discrepancy handover {$discrepancyHandover['no_discrepancy']} failed");
        }
        redirect('discrepancy-handover');
    }

    /**
     * Cancel discrepancy handover data.
     *
     * @param $id
     */
    public function cancel($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DISCREPANCY_HANDOVER_DELETE);

        $discrepancyHandover = $this->discrepancyHandover->getById($id);

        $message = $this->input->post('message');

        $this->db->trans_start();

        $this->discrepancyHandover->update([
            'status' => DiscrepancyHandoverModel::STATUS_CANCELED
        ], $id);

        $this->statusHistory->create([
            'id_reference' => $id,
            'type' => StatusHistoryModel::TYPE_DISCREPANCY_HANDOVER,
            'status' => DiscrepancyHandoverModel::STATUS_CANCELED,
            'description' => if_empty($message, 'Handover is canceled'),
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            flash('warning', "Discrepancy handover {$discrepancyHandover['no_discrepancy']} successfully canceled");
        } else {
            flash('danger', "Cancel discrepancy handover {$discrepancyHandover['no_discrepancy']} failed");
        }
        redirect('discrepancy-handover');
    }

    /**
     * Perform deleting discrepancy handover data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DISCREPANCY_HANDOVER_DELETE);

        $discrepancyHandover = $this->discrepancyHandover->getById($id);

        if ($this->discrepancyHandover->delete($id)) {
            flash('warning', "Discrepancy handover {$discrepancyHandover['no_discrepancy']} successfully deleted");
        } else {
            flash('danger', "Delete discrepancy handover {$discrepancyHandover['no_discrepancy']} failed");
        }
        redirect('discrepancy-handover');
    }
}