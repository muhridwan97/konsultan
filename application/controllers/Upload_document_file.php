<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Upload_document_file
 * @property UploadDocumentModel $uploadDocument
 * @property UploadDocumentFileModel $uploadDocumentFile
 * @property UploadModel $uploadModel
 * @property PeopleModel $people
 * @property BookingTypeModel $bookingType
 * @property DocumentTypeModel $documentType
 * @property NotificationModel $notification
 * @property Uploader $uploader
 * @property Mailer $mailer
 */
class Upload_document_file extends CI_Controller
{
    /**
     * Upload_document_file constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('PeopleModel', 'people');
        $this->load->model('UploadModel', 'uploadModel');
        $this->load->model('UploadDocumentModel', 'uploadDocument');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
        $this->load->model('BookingTypeModel', 'bookingType');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('NotificationModel', 'notification');
        $this->load->model('UserModel', 'user');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('modules/Uploader', 'uploader');
        $this->uploader->setDriver('s3');
    }

    /**
     * Show upload files by specific document type.
     * @param $id
     */
    public function create($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_CREATE);

        $document = $this->uploadDocument->getDocumentById($id);
        $data = [
            'title' => "Upload File",
            'subtitle' => "Add file document " . $document['document_type'],
            'page' => "upload_document_file/create",
            'document' => $document
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Send document notification.
     *
     * @param $document
     * @param bool $isUpdate
     * @return array|bool
     */
    private function sppb_notification($document, $isUpdate = false)
    {
        if (!get_active_branch('dashboard_status')) return true;

        return $this->notification->broadcast([
            'url' => 'sendMessage',
            'method' => 'POST',
            'payload' => [
                'chatId' => detect_chat_id(get_setting('whatsapp_group_admin')),
                'body' => ($isUpdate ? "📝 [UPDATED]" : "📄") . " Document *{$document['document_type']} ({$document['no_document']}) is " . ($isUpdate ? "updated" : "uploaded") . "*, related upload number {$document['no_upload']} ({$document['upload_description']} - {$document['category']}) by {$document['customer_name']}. Please validate right now! ✅",
            ]
        ], NotificationModel::TYPE_CHAT_PUSH);
    }

    /**
     * Save new uploaded files.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_CREATE);

        $uploadDocumentId = $this->input->post('id_upload_document');
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id_upload_document', 'Document data', 'trim|required');
            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $this->db->trans_start();

                $documentType = $this->uploadDocument->getDocumentById($uploadDocumentId);
                $documentTypeMaster = $this->documentType->getById($documentType['id_document_type']);

                $dataUpload = $this->uploadModel->getById($documentType['id_upload']);
                $aju = substr($dataUpload['description'], -4);
                $customer = $this->people->getById($dataUpload['id_person']);
                $createdBy = UserModel::authenticatedUserData('name');
                
                $branch_type = get_active_branch('branch_type');
                $branch_id = get_active_branch('id');

                $this->uploadDocument->updateUploadDocument([
                    'is_valid' => 0,
                    'validated_by' => null,
                    'validated_at' => null,
                    'is_check' => 0, //check document
                    'checked_by' => null,
                    'checked_at' => null,
                ], $uploadDocumentId);

                $uploadedPaths = [];
                $files = $this->input->post('doc_file_' . $uploadDocumentId . '_name');

                foreach ($files as $file) {
                    $fileData = $this->uploadDocumentFile->createUploadDocumentFile([
                        'id_upload_document' => $uploadDocumentId,
                        'source' => date('Y/m') . '/' . $file,
                        'created_by' => UserModel::authenticatedUserData('id')
                    ]);
                    if ($fileData) {
                        //$sourceFile = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . $file;
                        //$destFile = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $documentType['directory'] . DIRECTORY_SEPARATOR . $file;
                        //$this->uploadDocumentFile->moveTo($sourceFile, $destFile);
                        //$uploadedPaths[] = $destFile;

                        $sourceFile = 'temp/' . $file;
                        $destFile = $documentType['directory'] . '/' . date('Y/m') . '/' . $file;
                        $this->uploader->move($sourceFile, $destFile);
                        $uploadedPaths[] = asset_url($destFile);
                    }
                }
                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    if (!empty($files)) {
                    
                        $emailTo = get_setting('email_compliance');
                        $emailTitle = $dataUpload['no_upload'] . ' - ' . $customer['name'] . ' ' . $documentType['document_type'] . ' ' . $aju . ' Add Document';
                        $emailTemplate = 'emails/basic';
                        $emailData = [
                            'title' => 'Add Document ' . $documentType['document_type'],
                            'name' => get_setting('admin_compliance'),
                            'email' => $emailTo,
                            'content' => 'The customer recently upload document with title <b>' . $dataUpload['description'] . ' (' . $dataUpload['no_upload'] . ')</b>.
                                 The document is ' . $documentType['document_type'] . '. Please check and respond immediately.',
                        ];

                        $attachments = [];
                        foreach ($uploadedPaths as $uploadedPath) {
                            $attachments[] = [
                                'source' => $uploadedPath,
                            ];
                        }

                        $emailOption = [
                            'attachment' => $attachments,
                        ];
                        if($branch_type!='TPP' && $branch_id != '4' && $documentTypeMaster['is_email_notification']){
                            $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOption);
                        }
                        if (!empty($customer['email'])) {
                            $emailCustomer = $customer['email'];
                            $emailCustData = [
                                'title' => 'Add Document ' . $documentType['document_type'],
                                'name' => $customer['name'],
                                'email' => $customer['email'],
                                'content' => ucfirst($createdBy).' recently upload document with title <b>' . $dataUpload['description'] . ' (' . $dataUpload['no_upload'] . ')</b>.
                                    Document details of : ' . $documentType['document_type'] . '. Please wait until our employee is finished checking and validate your document.',
                            ];
                            if($documentTypeMaster['is_email_notification']){
                                $this->mailer->send($emailCustomer, $emailTitle, $emailTemplate, $emailCustData, $emailOption);
                            }
                        }
                    }
                    if (strpos($documentType['document_type'], 'SPPB') !== false) {
                        $this->sppb_notification($documentType, true);
                    }
                    flash('success', "Files <strong>{$documentType['document_type']}</strong> successfully added");

                    redirect('upload_document/view/' . $uploadDocumentId);
                } else {
                    flash('danger', "Upload files <strong>{$documentType['document_type']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->create($uploadDocumentId);
    }

    /**
     * Delete file record and data.
     */
    public function delete()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_DELETE);

        $documentId = $this->input->post('id_upload_document');
        $fileId = $this->input->post('id');
        $file = $this->uploadDocumentFile->getFileById($fileId);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Document Data', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $delete = $this->uploadDocumentFile->deleteUploadDocumentFile($fileId);

                if ($delete) {
                    //$filePath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $file['directory'];
                    //$this->uploadDocumentFile->deleteFile($file['source'], $filePath);
                    $this->uploader->delete($file['directory'] . '/' . $file['source']);
                    flash('warning', "File <strong>{$file['source']}</strong> successfully deleted");
                } else {
                    flash('danger', "Delete file <strong>{$file['source']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('upload_document/view/' . $documentId);
    }

    /**
     * Store upload data.
     */
    public function upload_s3()
    {
        $result = [];
        foreach ($_FILES as $file => $data) {
            $fileTitle = url_title(pathinfo($data['name'], PATHINFO_FILENAME));
            $extension = pathinfo($data['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '_' . $fileTitle . '.' . $extension;
            $upload = $this->uploader->setDriver('s3')->uploadTo($file, ['file_name' => $fileName]);
            $result[$file] = [
                'status' => $upload,
                'errors' => $this->uploader->getDisplayErrors(),
                'data' => $this->uploader->getUploadedData()
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    /**
     * Delete temporary upload.
     */
    public function delete_temp_s3()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $fileTemp = 'temp/' . $this->input->post('file');
            $delete = $this->uploader->setDriver('s3')->delete($fileTemp);
            header('Content-Type: application/json');
            echo json_encode(['status' => $delete !== false]);
        }
    }

    /**
     * Store upload data.
     */
    public function upload()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_CREATE);
        $result = [];
        foreach ($_FILES as $file => $data) {
            $fileName = rand(100, 999) . '_' . time() . '_' . url_title(pathinfo($data['name'], PATHINFO_FILENAME)) . '.' . pathinfo($data['name'], PATHINFO_EXTENSION);
            $upload = $this->uploadDocumentFile->uploadTo($file, $fileName);
            if($upload['status']) {
                $upload['data']['file_url'] = base_url('uploads/temp/' . $upload['data']['file_name']);
            }
            $result[$file] = $upload;
        }
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    /**
     * Delete temporary upload
     */
    public function delete_temp_upload()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_CREATE);
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $fileTemp = $this->input->post('file');
            $delete = $this->uploadDocumentFile->deleteFile($fileTemp);
            header('Content-Type: application/json');
            echo json_encode(['status' => $delete]);
        }
    }

}