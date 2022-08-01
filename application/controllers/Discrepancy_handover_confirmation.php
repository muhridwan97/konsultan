<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Discrepancy_handover_confirmation
 * @property DiscrepancyHandoverModel $discrepancyHandover
 * @property DiscrepancyHandoverGoodsModel $discrepancyHandoverGoods
 * @property UploadDocumentFileModel $uploadDocumentFile
 * @property NotificationModel $notification
 * @property StatusHistoryModel $statusHistory
 * @property PeopleModel $people
 * @property PeopleBranchModel $peopleBranch
 * @property UserTokenModel $userToken
 * @property Uploader $uploader
 * @property Exporter $exporter
 * @property Mailer $mailer
 */
class Discrepancy_handover_confirmation extends MY_Controller
{
    /**
     * Discrepancy_handover_confirmation constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('DiscrepancyHandoverModel', 'discrepancyHandover');
        $this->load->model('DiscrepancyHandoverGoodsModel', 'discrepancyHandoverGoods');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('NotificationModel', 'notification');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('PeopleBranchModel', 'peopleBranch');
        $this->load->model('UserTokenModel', 'userToken');
        $this->load->model('modules/Uploader', 'uploader');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('modules/Mailer', 'mailer');

        $this->setFilterMethods([
            'confirm' => 'GET|POST|PUT',
            'explain' => 'GET',
            'save_explanation' => 'GET|POST|PUT',
        ]);
    }

    /**
     * Show index response
     */
    public function index()
    {
        $this->load->view('response/approve_response');
    }

    /**
     * Cancel discrepancy handover data.
     *
     * @param $token
     */
    public function confirm($token)
    {
        $id = $this->input->get('id');

        $emailToken = $this->userToken->verifyToken($token, UserTokenModel::$TOKEN_CONFIRMATION);
        $sourceEmail = get_url_param('email', $emailToken);

        $discrepancyHandover = $this->discrepancyHandover->getById($id);

        if (!$emailToken) {
            flash('danger', 'Invalid or expired confirmation token key.');
        } else if (empty($discrepancyHandover)) {
            flash('danger', "Discrepancy handover data is not found");
        } else {
            $this->db->trans_start();

            $this->discrepancyHandover->update([
                'status' => DiscrepancyHandoverModel::STATUS_CONFIRMED
            ], $id);

            $this->statusHistory->create([
                'id_reference' => $id,
                'type' => StatusHistoryModel::TYPE_DISCREPANCY_HANDOVER,
                'status' => DiscrepancyHandoverModel::STATUS_CONFIRMED,
                'description' => 'Handover is confirmed from email: ' . $sourceEmail,
            ]);

            $existingUploadDocumentFile = $this->uploadDocumentFile->getBy([
                'upload_documents.id_upload' => $discrepancyHandover['id_upload'],
                'upload_documents.id_document_type' => 194,
                'upload_document_files.source' => $discrepancyHandover['attachment'],
            ], true);
            if (!empty($existingUploadDocumentFile)) {
                $this->uploadDocumentFile->updateUploadDocumentFile([
                    'description2' => 'Confirmed from email: ' . $sourceEmail,
                    'description_date' => date('Y-m-d H:i:s'),
                ], $existingUploadDocumentFile['id']);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {

                // send message about handover explained status
                $chatMessage = "*DISCREPANCY IS CONFIRMED*\n";
                $chatMessage .= "——————————————————\n";
                $chatMessage .= "*No Handover:* {$discrepancyHandover['no_discrepancy']}\n";
                $chatMessage .= "*Customer:* {$discrepancyHandover['customer_name']}\n";
                $chatMessage .= "*No Reference:* {$discrepancyHandover['no_reference']}\n";
                $chatMessage .= "*Status:* CONFIRMED";

                $complianceGroup = get_setting('whatsapp_group_admin');
                $this->notification->broadcast([
                    'url' => 'sendMessage',
                    'method' => 'POST',
                    'payload' => [
                        'chatId' => detect_chat_id($complianceGroup),
                        'body' => $chatMessage,
                    ]
                ], NotificationModel::TYPE_CHAT_PUSH);

                $this->userToken->deleteToken($token);

                flash('success', "Discrepancy handover {$discrepancyHandover['no_discrepancy']} successfully confirmed");
            } else {
                flash('danger', "Cancel discrepancy handover {$discrepancyHandover['no_discrepancy']} failed");
            }
        }

        redirect('response/index');
    }

    /**
     * View explanation form.
     *
     * @param $token
     */
    public function explain($token)
    {
        $id = $this->input->get('id');
        $emailToken = $this->userToken->verifyToken($token, UserTokenModel::$TOKEN_CONFIRMATION);

        $discrepancyHandover = $this->discrepancyHandover->getById($id);
        $discrepancyHandoverGoods = $this->discrepancyHandoverGoods->getBy(['id_discrepancy_handover' => $id]);

        if (!$emailToken) {
            flash('danger', 'Invalid or expired confirmation token key.', 'response/index');
        } else if (empty($discrepancyHandover)) {
            flash('danger', "Discrepancy handover data is not found", 'response/index');
        } else {
            $this->load->view('discrepancy_handover/explain', compact('discrepancyHandover', 'discrepancyHandoverGoods', 'token'));
        }
    }

    /**
     * View save explanation form.
     *
     * @param $token
     */
    public function save_explanation($token)
    {
        $id = $this->input->get('id');
        $emailToken = $this->userToken->verifyToken($token, UserTokenModel::$TOKEN_CONFIRMATION);
        $sourceEmail = get_url_param('email', $emailToken);

        $discrepancyHandover = $this->discrepancyHandover->getById($id);

        if (!$emailToken) {
            flash('danger', 'Invalid or expired confirmation token key.');
        } else if (empty($discrepancyHandover)) {
            flash('danger', "Discrepancy handover data is not found");
        } else {
            $uploadFile = true;
            $uploadedFile = if_empty($discrepancyHandover['explanation_attachment'], null);
            if (!empty($_FILES['attachment']['name'])) {
                $uploadFile = $this->uploader->setDriver('s3')->uploadTo('attachment', [
                    'destination' => 'discrepancy-handover/' . date('Y/m')
                ]);
                if ($uploadFile) {
                    $uploadedData = $this->uploader->getUploadedData();
                    $uploadedFile = $uploadedData['uploaded_path'];
                    if (!empty($discrepancyHandover['attachment'])) {
                        $this->uploader->setDriver('s3')->delete($discrepancyHandover['attachment']);
                    }
                } else {
                    flash('danger', $this->uploader->getDisplayErrors());
                }
            }

            if ($uploadFile) {
                $explanation = $this->input->post('explanation');

                $this->db->trans_start();

                $this->discrepancyHandover->update([
                    'explanation' => $explanation,
                    'explanation_attachment' => $uploadedFile,
                    'status' => DiscrepancyHandoverModel::STATUS_EXPLAINED
                ], $id);

                $this->statusHistory->create([
                    'id_reference' => $id,
                    'type' => StatusHistoryModel::TYPE_DISCREPANCY_HANDOVER,
                    'status' => DiscrepancyHandoverModel::STATUS_EXPLAINED,
                    'description' => 'Handover is explained from email: ' . $sourceEmail,
                ]);

                if (!empty($uploadedFile)) {
                    $existingUploadDocumentFile = $this->uploadDocumentFile->getBy([
                        'upload_documents.id_upload' => $discrepancyHandover['id_upload'],
                        'upload_documents.id_document_type' => 194,
                        'upload_document_files.source' => $discrepancyHandover['attachment'],
                    ], true);
                    if (!empty($existingUploadDocumentFile)) {
                        $this->uploadDocumentFile->updateUploadDocumentFile([
                            'description2' => $explanation . " (from email: {$sourceEmail})",
                            'description_date' => date('Y-m-d H:i:s'),
                            'description_attachment' => $uploadedFile
                        ], $existingUploadDocumentFile['id']);
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {

                    // send message about handover explained status
                    $chatMessage = "*DISCREPANCY IS [EXPLAINED]*\n";
                    $chatMessage .= "—————————————————\n";
                    $chatMessage .= "*No Handover:* {$discrepancyHandover['no_discrepancy']}\n";
                    $chatMessage .= "*Customer:* {$discrepancyHandover['customer_name']}\n";
                    $chatMessage .= "*No Reference:* {$discrepancyHandover['no_reference']}\n";
                    $chatMessage .= "*Status:* EXPLAINED\n";
                    $chatMessage .= "*Explanation:* {$explanation}\n";
                    $chatMessage .= "*Attachment:* " . (empty($uploadedFile) ? '-' : basename($uploadedFile));

                    $complianceGroup = get_setting('whatsapp_group_admin');
                    $this->notification->broadcast([
                        'url' => 'sendMessage',
                        'method' => 'POST',
                        'payload' => [
                            'chatId' => detect_chat_id($complianceGroup),
                            'body' => $chatMessage,
                        ]
                    ], NotificationModel::TYPE_CHAT_PUSH);

                    // send explanation file explanation
                    if (!empty($uploadedFile)) {
                        $this->notification->broadcast([
                            'url' => 'sendFile',
                            'method' => 'POST',
                            'payload' => [
                                'chatId' => detect_chat_id($complianceGroup),
                                'body' => asset_url($uploadedFile),
                                'filename' => basename($uploadedFile),
                                'caption' => 'Explanation attachment'
                            ]
                        ], NotificationModel::TYPE_CHAT_PUSH);
                    }

                    $this->userToken->deleteToken($token);

                    flash('success', "Discrepancy handover explanation {$discrepancyHandover['no_discrepancy']} successfully submitted", 'response/index');
                } else {
                    flash('danger', 'Submit discrepancy handover explanation failed, try again later');
                }
            }
        }

        $this->explain($token);
    }
}