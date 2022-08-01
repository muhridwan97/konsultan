<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Response
 * @property UserModel $user
 * @property UserTokenModel $userToken
 * @property PeopleModel $people
 * @property UploadModel $uploadModel
 * @property UploadDocumentModel $uploadDocument
 * @property UploadDocumentFileModel $uploadDocumentFile
 * @property BookingTypeModel $bookingType
 * @property DocumentTypeModel $documentType
 * @property Uploader $uploader
 */
class Response extends CI_Controller
{
    /**
     * Account constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UploadModel', 'uploadModel');
        $this->load->model('UploadDocumentModel', 'uploadDocument');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
        $this->load->model('BookingTypeModel', 'bookingType');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('UserModel', 'user');
        $this->load->model('UserTokenModel', 'userToken');
        $this->load->helper(array('form', 'url'));
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('modules/Uploader', 'uploader');
        $this->load->model('NotificationModel', 'notification');
        $this->uploader->setDriver('s3');
    }

    public function index()
    {
        $this->load->view('response/approve_response');
    }

    /**
     * Approve document response Draft
     * @param $token
     */
    public function approve_response($token)
    {
        $emailToken = $this->userToken->verifyToken($token, UserTokenModel::$TOKEN_CONFIRMATION);
        $sourceEmail = get_url_param('email', $emailToken);

        if (!$emailToken) {
            flash('danger', 'Invalid or expired confirmation token key.', 'response/index');
        } else {
            $user = $this->user->getUserByEmail($emailToken);
            $document = $this->uploadDocument->getDocumentById($_GET['id']);
            $upload = $this->uploadModel->getById($document['id_upload']);
            $people = $this->people->getById($upload['id_person']);
            $uploadDocuments = $this->uploadDocument->getDocumentsByUpload($document['id_upload']);

            $explodeName = explode(' ', $document['document_type']);
            $lastNameDocType = array_pop($explodeName);
            $mainDocTypeName = implode(' ', $explodeName);
            $documentType = $this->documentType->getReservedDocumentType($mainDocTypeName . ' Confirmation');
            $checkDocument = in_array($documentType['id'], array_column($uploadDocuments, 'id_document_type')); // check duplicate document
            
            $ref = substr($upload['description'], -4);
            $default_document = $this->bookingType->getBookingTypeById($upload['id_booking_type']);
            $files = $this->uploadDocumentFile->getFilesByDocument($_GET['id']);

            // to approve description2 and description_date should empty (last revision from internal)
            $fileFilters = array_filter($files, function($item){
                return (!$item['description2']) && (!$item['description_date']);
            });

            if(empty($checkDocument)){
                if(!empty($fileFilters)){
                    $no_document = 'CONF_' . $document['no_document'];
                    $this->db->trans_start();
                    $this->uploadDocument->createUploadDocument([
                        'id_upload' => $document['id_upload'],
                        'id_document_type' => $documentType['id'],
                        'no_document' => $no_document,
                        'document_date' => date('Y-m-d'),
                        'description' => 'Confirmed from email ' . $sourceEmail,
                        'is_response' => 1,
                        'is_valid' => 1, // valid
                        'created_by' => $user['id'] ?? 0,
                    ]);
                    $uploadDocumentId = $this->db->insert_id();

                    $responseFiles = [];
                    foreach ($files as $file) {
                        if ((!$file['description2']) && (!$file['description_date'])) {
                            $explode = explode('_', $file['source']);
                            unset($explode[0]);
                            unset($explode[1]);
                            $filename = rand(100, 999) . '_' . time() . '_' . implode('_', $explode);

                            $this->uploadDocumentFile->updateUploadDocumentFile([
                                'description2' => "Valid",
                                'description_date' => date('Y-m-d H:i:s'),
                            ], $file['id']);

                            $fileData = $this->uploadDocumentFile->createUploadDocumentFile([
                                'id_upload_document' => $uploadDocumentId,
                                'source' => date('Y/m') . '/conf_' . basename($file['source']),
                                'created_by' => isset($people['id_user']) ? $people['id_user'] : 1,
                            ]);

                            if ($fileData) {
                                //$sourceFile = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $document['directory'] . DIRECTORY_SEPARATOR . $file['source'];
                                //$destFile = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $documentType['directory'] . DIRECTORY_SEPARATOR . $filename;
                                //$this->uploadDocumentFile->copyTo($sourceFile, $destFile);
                                //$responseFiles[] = $destFile;

                                $sourceFile = $document['directory'] . '/'. $file['source'];
                                $destFile = $documentType['directory'] . '/' . date('Y/m') . '/conf_' . basename($file['source']);
                                $this->uploader->copy($sourceFile, $destFile);
                                $responseFiles[] = asset_url($destFile);
                            }
                        }
                    }
                    $branch_type = get_active_branch('branch_type');
                    $branch_id = get_active_branch('id');
                    // send email to compliance
                    $compAdmin = get_setting('admin_compliance');

                    $emailTo = get_setting('email_compliance');
                    $emailTitle = $upload['no_upload'] . ' - ' . $people['name'] . ' ' . $documentType['document_type'] . ' ' . $ref . ' Confirmation';
                    $emailTemplate = 'emails/basic';
                    $emailData = [
                        'title' => 'Confirmation file ' . $document['document_type'],
                        'name' => $compAdmin,
                        'email' => $emailTo,
                        'content' => 'The customer have been approved your document draft with no upload <b>' . $upload['no_upload'] . '</b>. The customer recently upload document ' . $documentType['document_type'] . '(See Attachment). For more further information please contact our customer support',
                    ];
                    $attachments = [];
                    foreach ($responseFiles as $responseFile) {
                        $attachments[] = [
                            'source' => $responseFile,
                        ];
                    }
                    $emailOptions = [
                        'attachment' => $attachments,
                    ];

                    $this->db->trans_complete();

                    if ($this->db->trans_status()) {
                        $this->userToken->deleteToken($token);
                        if($branch_type!='TPP' && $branch_id != '4'){
                            //send email for approve document
                            $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
                        }
                        //send wa for document approve only
                        $data_whatsapp = "[CONFIRM] Draft {$default_document['default_document']} aju {$ref} telah dikonfirmasi oleh {$people['name']} from email {$emailToken}. ".$document['created_name']." akan segera melakukan proses SPPB dan proses selanjutnya";
                        $whatsapp_group = $people['whatsapp_group'];
                        $this->send_message($data_whatsapp,$whatsapp_group);
                        flash('success', "Approve for <strong>{$upload['no_upload']}</strong> document <strong>{$documentType['document_type']}</strong> successfully created,<br>Please login for check your document");
                    } else {
                        flash('danger', 'Invalid or expired reset token key.');
                    }
                }else{ // dokumen sudah di revisi semua atau dokumen telah valid
                    flash('warning', "Approve for <strong>{$upload['no_upload']}</strong> document <strong>{$documentType['document_type']}</strong> is not allowed,<br> document already revised or confirmed!");
                }
            }

            // send email to compliance
            $compAdmin = get_setting('admin_compliance');

            $emailTo = get_setting('email_compliance');
            $emailTitle = $upload['no_upload'] . ' - ' . $people['name'] . ' ' . $documentType['document_type'] . ' ' . $ref . ' Confirmation';
            $emailTemplate = 'emails/basic';
            $emailData = [
                'title' => 'Confirmation file ' . $document['document_type'],
                'name' => $compAdmin,
                'email' => $emailTo,
                'content' => 'The customer have been approved your document draft with no upload <b>' . $upload['no_upload'] . '</b>. The customer recently upload document ' . $documentType['document_type'] . '(See Attachment). For more further information please contact our customer support',
            ];
            $attachments = [];
            foreach ($responseFiles as $responseFile) {
                $attachments[] = [
                    'source' => $responseFile,
                ];
            }
            $emailOptions = [
                'attachment' => $attachments,
            ];

            $this->userToken->deleteToken($token);
            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                //send email for approve document
                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);

                //send wa for document approve only
                $data_whatsapp = "[CONFIRM] Draft {$default_document['default_document']} aju {$ref} telah dikonfirmasi oleh {$people['name']}. ".$document['created_name']." akan segera melakukan proses SPPB dan proses selanjutnya";
                $whatsapp_group = $people['whatsapp_group'];
                $this->send_message($data_whatsapp,$whatsapp_group);
                
                flash('success', "Approve for <strong>{$upload['no_upload']}</strong> document <strong>{$documentType['document_type']}</strong> successfully created, Please login for check your document");
            }else{
                flash('danger', 'Confirmation document is already exists !');
            }

            redirect('response/index');
        }
    }

    /**
     * view revise document response Draft.
     *
     * @param $token
     */
    public function revise_response($token)
    {
        $emailToken = $this->userToken->verifyToken($token, UserTokenModel::$TOKEN_CONFIRMATION);

        if (!$emailToken) {
            flash('danger', 'Invalid or expired reset token key.', 'response/index');
        } else {
            $allFiles = $this->uploadDocumentFile->getFilesByDocument($_GET['id']);
            $document = $this->uploadDocument->getDocumentById($_GET['id']);
            $upload = $this->uploadModel->getById($document['id_upload']);
            $tokenEmail = $token;
            $files = array_filter($allFiles, function($item){
                return (!$item['description2']) && (!$item['description_date']);
            });

            $this->load->view('response/revise_response', compact('upload', 'files', 'allFiles', 'document', 'tokenEmail'));
        }
    }

    /**
     * submit revise document response Draft.
     *
     * @param $token
     */
    public function revise($token)
    {
        $emailToken = $this->userToken->verifyToken($token, UserTokenModel::$TOKEN_CONFIRMATION);

        if (!$emailToken) {
            flash('danger', 'Invalid or expired reset token key.', 'response/index');
        }

        $id_upload_document_files = $this->input->post('id_upload_document_files');
        $id_upload_document = $this->input->post('id_upload_document');
        $revise = $this->input->post('revise');
        $id_upload = $this->input->post('id_upload');
        $document_type = $this->input->post('document_type');
        $upload = $this->uploadModel->getById($id_upload);
        $people = $this->people->getById($upload['id_person']);
        
        $ref = substr($upload['description'], -4);
        $upload = $this->uploadModel->getById($id_upload);
        $customer = $this->people->getById($upload['id_person']);
        $documentTypes = $this->documentType->getReservedDocumentType($document_type);
        $default_document = $this->bookingType->getBookingTypeById($upload['id_booking_type']);
        $uploadDocument = $this->uploadDocument->getDocumentsByUploadByDocumentType($id_upload, $documentTypes['id']);

        $uploadPassed = true;
        $this->db->trans_start();

        foreach ($id_upload_document_files as $key => $id_upload_document_file) {

            $fileName = '';
            $doc_attachment = $_FILES['attachment']['name'][$key];
            $getFilesByDocument = $this->uploadDocumentFile->getFileById($id_upload_document_file);

            $data = [];
            if (!empty($doc_attachment)) {
                /*
                $fileName = 'DesAttach_' . time() . '_' . rand(100, 999);
                $saveTo = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'description_attachments';
                if ($this->documentType->makeFolder('description_attachments')) {
                    $config['upload_path'] = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'description_attachments';
                    $config['allowed_types'] = 'gif|jpg|jpeg|png|pdf|xls|xlsx|doc|docx|ppt|pptx|txt|zip|rar';
                    $config['max_size'] = 3000;
                    $config['max_width'] = 5000;
                    $config['max_height'] = 5000;
                    $config['file_ext_tolower'] = true;
                    if (!empty($fileName)) {
                        $config['file_name'] = $fileName;
                    }

                    $this->load->library('upload', $config);

                    $_FILES['attachment' . '_multiple']['name'] = $_FILES['attachment']['name'][$key];
                    $_FILES['attachment' . '_multiple']['type'] = $_FILES['attachment']['type'][$key];
                    $_FILES['attachment' . '_multiple']['tmp_name'] = $_FILES['attachment']['tmp_name'][$key];
                    $_FILES['attachment' . '_multiple']['error'] = $_FILES['attachment']['error'][$key];
                    $_FILES['attachment' . '_multiple']['size'] = $_FILES['attachment']['size'][$key];

                    $dataupload = $this->upload->do_upload('attachment' . '_multiple');

                    if ($dataupload) {
                        $data[$key] = $this->upload->data();
                    } else {
                        $uploadPassed = false;
                        $this->session->set_flashdata([
                            'status' => 'danger',
                            'message' => $this->upload->display_errors(),
                        ]);
                        break;
                    }
                } else {
                    $uploadPassed = false;
                    $this->session->set_flashdata([
                        'status' => 'warning',
                        'message' => 'Making folder upload failed, try again',
                    ]);
                    break;
                }
                */

                $_FILES['attachment_' . $key]['name'] = $_FILES['attachment']['name'][$key];
                $_FILES['attachment_' . $key]['type'] = $_FILES['attachment']['type'][$key];
                $_FILES['attachment_' . $key]['tmp_name'] = $_FILES['attachment']['tmp_name'][$key];
                $_FILES['attachment_' . $key]['error'] = $_FILES['attachment']['error'][$key];
                $_FILES['attachment_' . $key]['size'] = $_FILES['attachment']['size'][$key];
                $uploadPassed = $this->uploader->uploadTo('attachment_' . $key, ['destination' => 'description_attachments/' . date('Y/m')]);
                if ($uploadPassed) {
                    $data = $this->uploader->getUploadedData();
                } else {
                    flash('danger', $this->uploader->getDisplayErrors());
                    break;
                }
            }

            //$name_file = !empty($data) ? $data[$key]['file_name'] : null;
            $name_file = !empty($data) ? $data['uploaded_path'] : null;

            if ($uploadPassed) {
                $this->uploadDocumentFile->updateUploadDocumentFile([
                    'description2' => $revise[$key],
                    'description_date' => date('Y-m-d H:i:s'),
                    'description_attachment' => $name_file,
                ], $id_upload_document_file);
            }

        }
        $this->db->trans_complete();

        if ($this->db->trans_status() && $uploadPassed) {
            $this->userToken->deleteToken($token);

            //send wa for document revise only
            if(isset($uploadDocument) && $uploadDocument['total_file'] > 1){
                $number = $uploadDocument['total_file'];

                $data_whatsapp = "[REVISE] Revisi Draft {$default_document['default_document']} ke {$number} aju {$ref} telah dilakukan oleh {$customer['name']}. ".$uploadDocument['created_name']." akan segera lakukan perbaikan dan segera upload draft kembali.";
                $whatsapp_group = $customer['whatsapp_group'];
                $this->send_message($data_whatsapp,$whatsapp_group);
            }

            if(isset($uploadDocument) && $uploadDocument['total_file'] == 1){
                $data_whatsapp = "[REVISE] Revisi Draft {$default_document['default_document']} aju {$ref} telah dilakukan oleh {$customer['name']}. ".$uploadDocument['created_name']." akan segera lakukan perbaikan dan segera upload draft kembali.";
                $whatsapp_group = $customer['whatsapp_group'];
                $this->send_message($data_whatsapp,$whatsapp_group);
            }
            flash('success', "Revise for <strong>{$upload['no_upload']}</strong> document <strong>{$document_type}</strong> successfully created,<br>Please login for check your document", 'response/index');
        }

        $this->revise_response($token);
    }

     /**
     * Send a message to a new or existing chat.
     * 6281333377368-1557128212@g.us
     */
    public function send_message($data,$whatsapp_group)
    {
        $data = [
            'url' => 'sendMessage',
            'method' => 'POST',
            'payload' => [
                'chatId' => detect_chat_id($whatsapp_group),
                'body' => $data,
            ]
        ];

        $result = $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);
    }
}
