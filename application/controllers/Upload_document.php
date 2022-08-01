<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Upload_document
 * @property UploadModel $upload
 * @property UploadDocumentModel $uploadDocument
 * @property UploadDocumentFileModel $uploadDocumentFile
 * @property UploadDocumentPartyModel $uploadDocumentParty
 * @property DocumentTypeModel $documentType
 * @property BookingTypeModel $bookingType
 * @property PeopleModel $people
 * @property UserTokenModel $userToken
 * @property PeopleContactModel $peopleContact
 * @property PeopleBranchMentionModel $peopleBranchMention
 * @property PeopleUserModel $peopleUser
 * @property NotificationModel $notification
 * @property UserModel $user
 * @property Mailer $mailer
 * @property Uploader $uploader
 */
class Upload_document extends CI_Controller
{
    /**
     * Upload constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('zip');

        $this->load->model('UploadModel', 'upload');
        $this->load->model('UploadDocumentModel', 'uploadDocument');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
        $this->load->model('UploadDocumentPartyModel', 'uploadDocumentParty');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('BookingTypeModel', 'bookingType');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('PeopleUserModel', 'peopleUser');
        $this->load->model('UserTokenModel', 'userToken');
        $this->load->model('UserModel', 'user');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('modules/Uploader', 'uploader');
        $this->load->model('NotificationModel', 'notification');
        $this->load->model('PeopleContactModel', 'peopleContact');
        $this->load->model('PeopleBranchMentionModel', 'peopleBranchMention');
        $this->uploader->setDriver('s3');
    }

    /**
     * Show document and its files.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_VIEW);

        $files = $this->uploadDocumentFile->getFilesByDocument($id);
        $document = $this->uploadDocument->getDocumentById($id);
        $parties = $this->uploadDocumentParty->getPartyByUploadDocument($id);

        $data = [
            'title' => "Upload",
            'subtitle' => "View upload",
            'page' => "upload_document/view",
            'files' => $files,
            'document' => $document,
            'id' => $id,
            'parties' => $parties,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Download document.
     * @param $id
     */

    public function download($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_VIEW);

        $documentFiles = $this->uploadDocumentFile->getFilesByDocument($id);
        $document = $this->uploadDocument->getDocumentById($id);
        $upload = $this->upload->getById($document['id_upload']);

        foreach ($documentFiles as $value) {
            //$path[""] = 'uploads/' . $value['directory'] . '/' . urlencode($value['source']);
            //$this->zip->read_file($path[""]);
            $fileUrl = asset_url(rawurlencode(if_empty($value['directory'], '', '', '/')) . $value['source']);
            $this->zip->add_data(basename($value['source']), file_get_contents($fileUrl));
        }
        $this->zip->download($upload['no_upload'] . '_' . $document['directory'] . '.zip');
    }

    /**
     * Download file.
     * @param null $id
     */
    public function download_file($id = null)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_VIEW);

        $id_doc = $this->input->post('id_doc');
        $no_upload = $this->input->post('no_upload');

        if (empty($id_doc)) {
            $documents = $this->uploadDocument->getDocumentsByUpload($id);
            foreach ($documents as $document) {
                $documentFiles = $this->uploadDocumentFile->getFilesByDocument($document['id']);
                foreach ($documentFiles as $value) {
                    //$curDir = 'uploads/' . $value['directory'] . '/' . urlencode($value['source']);
                    //$newDir = $document['document_type'] . '/' . urlencode($value['source']);
                    //$this->zip->read_file($curDir, $newDir);

                    $fileUrl = asset_url(rawurlencode(if_empty($value['directory'], '', '', '/')) . $value['source']);
                    $this->zip->add_data($document['document_type'] . '/' . basename($value['source']), file_get_contents($fileUrl));
                }
            }
        } else {
            foreach ($id_doc as $id) {
                $document = $this->uploadDocument->getDocumentById($id);
                $documentFiles = $this->uploadDocumentFile->getFilesByDocument($id);
                foreach ($documentFiles as $value) {
                    //$curDir = 'uploads/' . $value['directory'] . '/' . urlencode($value['source']);
                    //$newDir = $document['document_type'] . '/' . urlencode($value['source']);
                    //$this->zip->read_file($curDir, $newDir);

                    $fileUrl = asset_url(rawurlencode(if_empty($value['directory'], '', '', '/')) . $value['source']);
                    $this->zip->add_data($document['document_type'] . '/' . basename($value['source']), file_get_contents($fileUrl));
                }
            }
        }

        if (empty($no_upload)) {
            $upload = $this->upload->getById($id);
            if (empty($upload)) {
                $no_upload = 'document';
            } else {
                $no_upload = $upload['no_upload'];
            }
        }

        $this->zip->download($no_upload . '.zip');
    }

    /**
     * Upload document file.
     * @param $uploadId
     */
    public function create($uploadId)
    {
        $upload = $this->upload->getById($uploadId);
        $documents = $this->uploadDocument->getDocumentsByUpload($uploadId, false);
        $documentTypes = $this->documentType->getByBookingType($upload['id_booking_type'], $upload['id']);

        $data = [
            'title' => "Upload Document",
            'subtitle' => "Add document",
            'page' => "upload_document/create",
            'upload' => $upload,
            'documents' => $documents,
            'documentTypes' => $documentTypes
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
     * Save new upload.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_CREATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Upload data', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $uploadId = $this->input->post('id');
                $upload = $this->upload->getById($uploadId);
                $customer = $this->people->getById($upload['id_person']);
                $createdBy =$this->user->getById(UserModel::authenticatedUserData('id'));

                $this->db->trans_start();

                $documentTypes = $this->documentType->getByBookingType($upload['id_booking_type'], $uploadId);

                $sppbDocument = null;
                $sppfDocument = null;
                $uploadedPaths = [];
                $documentTitles = [];
                $countDocSendMail = 0;
                foreach ($documentTypes as $documentType) {
                    $documentNumber = $this->input->post('doc_no_' . $documentType['id']);
                    $documentDate = format_date($this->input->post('doc_date_' . $documentType['id']));
                    $documentSubtype = $this->input->post('doc_subtype_' . $documentType['id']);
                    $documentExpiredDate = !empty($this->input->post('doc_expired_date_' . $documentType['id'])) ? format_date($this->input->post('doc_expired_date_' . $documentType['id'])) : null;
                    $documentFreetimeDate = !empty($this->input->post('doc_freetime_date_' . $documentType['id'])) ? format_date($this->input->post('doc_freetime_date_' . $documentType['id'])) : null;
                    if (!empty($documentNumber)) {
                        $documentTitles[] = $documentType['document_type'];
                        $this->uploadDocument->createUploadDocument([
                            'id_upload' => $uploadId,
                            'id_document_type' => $documentType['id'],
                            'no_document' => $documentNumber,
                            'document_date' => $documentDate,
                            'subtype' => $documentSubtype,
                            'expired_date' => $documentExpiredDate,
                            'freetime_date' => $documentFreetimeDate,
                            'created_by' => UserModel::authenticatedUserData('id')
                        ]);
                        $uploadDocumentId = $this->db->insert_id();
                        $uploadDocument = $this->uploadDocument->getDocumentById($uploadDocumentId);
                        if (strpos($documentType['document_type'], 'SPPF') !== false) {
                            $sppfDocument = $uploadDocument;
                        }
                        if (strpos($documentType['document_type'], 'SPPB') !== false) {
                            $sppbDocument = $uploadDocument;
                        }
                        $statusReleaseDocuments = ['E Billing', 'BPN (Bukti Penerimaan Negara)', 'SPPB', 'SURAT KETERANGAN BEBAS PAJAK', 'MASTERLIST'];
                        if ($upload['is_hold'] && in_array($documentType['document_type'], $statusReleaseDocuments)) {
                            flash('danger', "Upload {$upload['no_upload']} is HOLD, please release first before upload 'E Billing', 'BPN', 'SPPB', 'SKB', 'MASTERLIST'!", '_back', 'upload/view/' . $uploadId);
                        }

                        //$upload['status'] = $this->upload->updateUploadStatus($upload, $uploadDocument);

                        $files = $this->input->post('doc_file_' . $documentType['id'] . '_name');
                        if(!empty($files)) {
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

                                    if($documentType['is_email_notification']){
                                        $countDocSendMail++;
                                    }
                                }
                            }
                        }
                    }
                }

                if (!empty($sppfDocument)) {
                    $uploadReference = $this->upload->getById($upload['id_upload']);
                    $sppfFiles = $this->uploadDocumentFile->getFilesByDocument($sppfDocument['id']);
                    $attachments = [];
                    foreach ($sppfFiles as $sppfFile) {
                        $attachments[] = [
                            //'source' => FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $sppfFile['directory'] . DIRECTORY_SEPARATOR . $sppfFile['source'],
                            'source' => asset_url($sppfFile['directory'] . '/' . $sppfFile['source']),
                        ];
                    }

                    $emailTo = 'acc_mgr@transcon-indonesia.com';
                    $emailTitle = "Document SPPF is uploaded for reference " . $upload['description'] . ' with customer ' . $upload['name'];
                    $emailTemplate = 'emails/basic';
                    $emailData = [
                        'name' => 'Finance',
                        'email' => $emailTo,
                        'content' => "
                            Recently document SPPF {$sppfDocument['no_document']} 
                            ({$sppfDocument['document_date']}) of customer {$upload['name']} is uploaded:
                            <br><br>
                            <b>No Outbound: " . ($upload['category'] == 'OUTBOUND' ? $upload['description'] : '-') . "</b><br>
                            <b>No Inbound: " . ($uploadReference['category'] == 'INBOUND' ? $uploadReference['description'] : '-') . "</b><br><br>
                            This email may includes attachments.
                        "
                    ];
                    $emailOptions = [
                        'cc' => ['fin2@transcon-indonesia.com', 'findata@transcon-id.com'],
                        'attachment' => $attachments
                    ];

                    $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
                }

                $branch_type = get_active_branch('branch_type');
                $branch_id = get_active_branch('id');
                if (!empty($documentTitles)) {
                    if (count($documentTitles) == 1) {
                        $allDocumentTypes = 'The Document is ' . implode(', ', $documentTitles);
                    } else if (count($documentTitles) == 2) {
                        $lastDocumentType = array_pop($documentTitles);
                        $allDocumentTypes = 'Document details of : ' . implode(', ', $documentTitles);
                        $allDocumentTypes .= ' and ' . $lastDocumentType;
                    } else {
                        $lastDocumentType = array_pop($documentTitles);
                        $allDocumentTypes = 'Document details of : ' . implode(', ', $documentTitles);
                        $allDocumentTypes .= ', and ' . $lastDocumentType;
                    }

                    $compAdmin = get_setting('admin_compliance');
                    $aju = substr($upload['description'], -3);

                    $attachments = [];
                    foreach ($uploadedPaths as $uploadedPath) {
                        $attachments[] = [
                            'source' => $uploadedPath,
                        ];
                    }
                    $emailTo = get_setting('email_compliance');
                    $emailTitle = $upload['no_upload'] . ' - ' . $customer['name'] . ' ' . $aju . ' Add Document Upload';
                    $emailTemplate = 'emails/basic';
                    $emailData = [
                        'title' => 'Add Document Upload',
                        'name' => $compAdmin,
                        'email' => get_setting('email_compliance'),
                        'content' => 'The customer recently upload document with title <b>' . $upload['description'] . ' (' . $upload['no_upload'] . ')</b>. ' . $allDocumentTypes . '. Please check and respond immediately.',
                    ];
                    $emailOption = [
                        'attachment' => $attachments,
                    ];

                    if($branch_type!='TPP' && $branch_id != '4' && $countDocSendMail > 0){
                        $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOption);
                    }
                    if (!empty($customer['email'])) {
                        $emailCustomer = $customer['email'];
                        $emailCustData = [
                            'title' => 'Add Document Upload',
                            'name' => $customer['name'],
                            'email' => $customer['email'],
                            'content' => ucfirst($createdBy['name']).' recently upload document with title <b>' . $upload['description'] . ' (' . $upload['no_upload'] . ')</b>. ' . $allDocumentTypes . '. Please wait until our employee is finished checking and validate your document.',
                        ];
                        if($countDocSendMail > 0){
                            $this->mailer->send($emailCustomer, $emailTitle, $emailTemplate, $emailCustData, $emailOption);
                        }
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    if (!empty($sppbDocument)) {
                       $this->sppb_notification($sppbDocument);
                    }
                    flash('success', "Additional document for upload no <strong>{$upload['no_upload']}</strong> successfully added", 'upload');
                } else {
                    flash('danger', "Add document for upload no <strong>{$upload['no_upload']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->create($this->input->post('id'));
    }

    /**
     * Edit uploaded document.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_EDIT);

        $document = $this->uploadDocument->getDocumentById($id);
        $created_by = $this->user->getById($document['created_by']);
        $files = $this->uploadDocumentFile->getFilesByDocument($id);

        $explodeName = explode(' ', $document['document_type']);
        $lastNameDocType = array_pop($explodeName);
        $mainDocType = implode(' ', $explodeName);
        $getMainDocType = $this->documentType->getReservedDocumentType($mainDocType);

        $data = [
            'title' => "Upload Document",
            'subtitle' => "Edit document " . $document['document_type'],
            'page' => "upload_document/edit",
            'document' => $document,
            'files' => $files,
            'getMainDocType' => $getMainDocType,
            'lastNameDocType' => $lastNameDocType,
            'created_by' => $created_by,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Update document data.
     * @param $id
     * @throws Exception
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_EDIT);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Document data', 'trim|required');
            $this->form_validation->set_rules('doc_no', 'Document number', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('doc_date', 'Document date', 'trim|required|alpha_numeric_spaces');
            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $this->db->trans_start();

                $document = $this->uploadDocument->getDocumentById($id);
                $documentNumber = $this->input->post('doc_no');
                $documentDate = (new DateTime($this->input->post('doc_date')))->format('Y-m-d H:i:s');
                $documentSubtype = $this->input->post('document_subtype');
                $documentExpDate = $this->input->post('expired_date');
                $documentFreetimeDate = $this->input->post('freetime_date');
                $documentTotalItem = $this->input->post('total_item'); 
                $documentFiles = $this->input->post('doc_file_' . $id . '_name');
                $dataUpload = $this->upload->getById($document['id_upload']);
                $customer = $this->people->getById($dataUpload['id_person']);
                $documentType = $this->documentType->getById($document['id_document_type']);
                $createdBy = $this->user->getById(UserModel::authenticatedUserData('id'));

                if($documentSubtype == "SOC"){
                    $documentExpDate = date('Y-m-d H:i:s', strtotime($documentExpDate));
                    $documentFreetimeDate = null;
                }else if($documentSubtype == "COC"){
                    $documentFreetimeDate = date('Y-m-d H:i:s', strtotime($documentFreetimeDate));
                    $documentExpDate = date('Y-m-d H:i:s', strtotime($documentExpDate));
                }else if($documentSubtype == "LCL"){
                    $documentFreetimeDate = null;
                    $documentExpDate = null;
                }else{
                    $documentSubtype = null;
                    $documentFreetimeDate = null;
                    $documentExpDate = null;
                }

                // update data document only
                $this->uploadDocument->updateUploadDocument([
                    'no_document' => $documentNumber,
                    'document_date' => $documentDate,
                    'subtype' => $documentSubtype,
                    'expired_date' => $documentExpDate,
                    'freetime_date' => $documentFreetimeDate,
                    'total_item' => $documentTotalItem,
                    'is_valid' => 0,
                    'validated_by' => null,
                    'validated_at' => null,
                    'is_check' => 0, //check document
                    'checked_by' => null,
                    'checked_at' => null,
                    'updated_by' => UserModel::authenticatedUserData('id'),
                    'updated_at' => date('Y-m-d H:i:s')
                ], $id);

                // replace only if new document file uploaded.
                if (!empty($documentFiles)) {
                    // delete all files before
                    $files = $this->uploadDocumentFile->getFilesByDocument($id);
                    foreach ($files as $file) {
                        $delete = $this->uploadDocumentFile->deleteUploadDocumentFile($file['id']);
                        if ($delete) {
                            //$filePath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $file['directory'];
                            //$this->uploadDocumentFile->deleteFile($file['source'], $filePath);
                            $this->uploader->delete($file['directory'] . '/' . $file['source']);
                        }
                    }

                    // move uploaded file from temp
                    $responseFiles = [];
                    foreach ($documentFiles as $file) {
                        $fileData = $this->uploadDocumentFile->createUploadDocumentFile([
                            'id_upload_document' => $id,
                            'source' => date('Y/m') . '/' . $file,
                            'created_by' => UserModel::authenticatedUserData('id')
                        ]);
                        if ($fileData) {
                            //$sourceFile = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . $file;
                            //$destFile = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $document['directory'] . DIRECTORY_SEPARATOR . $file;
                            //$this->uploadDocumentFile->moveTo($sourceFile, $destFile);
                            //$responseFiles[] = $destFile;

                            $sourceFile = 'temp/' . $file;
                            $destFile = $document['directory'] . '/' . date('Y/m') . '/' . $file;
                            $this->uploader->move($sourceFile, $destFile);
                            $responseFiles[] = asset_url($destFile);
                        }
                    }

                    $aju = substr($dataUpload['description'], -3);
                    $branch_type = get_active_branch('branch_type');
                    $branch_id = get_active_branch('id');

                    if ($document['is_response'] == 1) {
                        $attachments = [];
                        foreach ($responseFiles as $responseFile) {
                            $attachments[] = [
                                'source' => $responseFile,
                            ];
                        }

                        // email notification for customer
                        $this->load->library('email');
                        if (!empty($customer['email'])) {
                           
                            $emailTo = $customer['email'];
                            $emailTitle =  $dataUpload['no_upload'] . ' - ' . $customer['name'] . ' ' . $document['document_type'] . ' ' . $aju . ' Re Upload Document';
                            $emailTemplate = 'emails/basic';
                            $emailData = [
                                'title' => 'Re Upload ' . $document['document_type'],
                                'name' => $customer['name'],
                                'email' => $emailTo,
                                'content' => 'Your documents with no upload ' . $dataUpload['no_upload'] . ' receive new response ' . $document['document_type'] . ' with no document: ' . $document['no_document'] . ' at ' . $document['document_date'] . ' (See Attachment). For more further information please contact our customer support.',
                            ];
                            $emailOption = [
                                'attachment' => $attachments,
                            ];

                            if(!empty($emailTo) && $documentType['is_email_notification']){
                                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOption);
                            }
                        }
                        if($branch_type!='TPP' && $branch_id != '4'){
                            //email for compliance
                            $emailCompliance = get_setting('email_compliance');
                            $emailTitle =  $dataUpload['no_upload'] . ' - ' . $customer['name'] . ' ' . $document['document_type'] . ' ' . $aju . ' Re Upload Document';
                            $emailTemplate = 'emails/basic';
                            $emailData = [
                                'title' => 'Re Upload ' . $document['document_type'],
                                'name' => get_setting('admin_compliance'),
                                'email' => $emailCompliance,
                                'content' => ucfirst($createdBy['name']).' recently re-upload document response with no upload <b>' . $dataUpload['no_upload'] . '</b> document ' . $document['document_type'] . ' (See Attachment).  Please wait until your customer finished checking and responding immediately.',
                            ];
                            $emailOption = [
                                'attachment' => $attachments,
                            ];

                            if($documentType['is_email_notification']){
                                $this->mailer->send($emailCompliance, $emailTitle, $emailTemplate, $emailData, $emailOption);
                            }
                        }
                    } else {

                        $attachments = [];
                        foreach ($responseFiles as $responseFile) {
                            $attachments[] = [
                                'source' => $responseFile,
                            ];
                        }

                        // email notification for customer
                        $this->load->library('email');
                        if (!empty($customer['email'])) {

                            $emailTo = $customer['email'];
                            $emailTitle =  $dataUpload['no_upload'] . ' - ' . $customer['name'] . ' ' . $document['document_type'] . ' ' . $aju . ' Re Upload Document';
                            $emailTemplate = 'emails/basic';
                            $emailData = [
                                'title' => 'Re Upload ' . $document['document_type'],
                                'name' => $customer['name'],
                                'email' => $emailTo,
                                'content' => ucfirst($createdBy['name']).' recently re-upload document response with no upload <b>' . $dataUpload['no_upload'] . '</b> document ' . $document['document_type'] . ' (See Attachment).  Please wait until our employee finished checking and responding immediately.',
                            ];
                            $emailOption = [
                                'attachment' => $attachments,
                            ];

                            if(!empty($emailTo) && $documentType['is_email_notification']){
                                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOption);
                            }
                        }

                        //email notification for compliance
                        $emailCompliance = get_setting('email_compliance');
                        $emailTitle =  $dataUpload['no_upload'] . ' - ' . $customer['name'] . ' ' . $document['document_type'] . ' ' . $aju . ' Re Upload Document';
                        $emailTemplate = 'emails/basic';
                        $emailData = [
                            'title' => 'Re Upload ' . $document['document_type'],
                            'name' => get_setting('admin_compliance'),
                            'email' => $emailCompliance,
                            'content' => 'The customer recently upload document with title <b>' . $dataUpload['description'] . ' (' . $dataUpload['no_upload'] . ')</b>.
                                 The document is ' . $document['document_type'] . '. Please check and respond immediately.',
                        ];
                        $emailOption = [
                            'attachment' => $attachments,
                        ];
                        if($branch_type!='TPP' && $branch_id != '4' && $documentType['is_email_notification']){
                            $this->mailer->send($emailCompliance, $emailTitle, $emailTemplate, $emailData, $emailOption);
                        }
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    if (strpos($document['document_type'], 'SPPB') !== false) {
                        $this->sppb_notification($document, true);
                    }
                    flash('success', "Document <strong>{$document['document_type']}</strong> successfully updated");

                    if (!empty(get_url_param('redirect'))) {
                        redirect(get_url_param('redirect'), false);
                    } else {
                        redirect('upload/view/' . $document['id_upload']);
                    }
                } else {
                    flash('danger', "Update document <strong>{$document['document_type']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->edit($id);
    }

    /**
     * Get document files.
     */
    public function ajax_get_document_files()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $documentId = $this->input->get('id_document');
            $files = $this->uploadDocumentFile->getFilesByDocument($documentId);
            foreach ($files as &$file) {
                $file['file_url'] = asset_url($file['directory'] . '/' . $file['source']);
            }
            header('Content-Type: application/json');
            echo json_encode($files);
        }
    }

    /**
     * Get documents by upload id.
     */
    public function ajax_upload_user_document()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $uploadId = $this->input->get('id_upload');
            $bookingTypeId = $this->input->get('id_booking_type');
            $documents = $this->uploadDocument->getDocumentsByUpload($uploadId);
            if (!empty($bookingTypeId)) {
                $bookingType = $this->bookingType->getById($bookingTypeId);
                foreach ($documents as &$document) {
                    if ($document['id_document_type'] == $bookingType['id_document_type']) {
                        $document['is_main_document'] = true;
                    } else {
                        $document['is_main_document'] = false;
                    }
                }
            }
            header('Content-Type: application/json');
            echo json_encode($documents);
        }
    }

    /**
     * Validate document (approve/reject)
     */
    public function validate()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_VALIDATE);

        $documentId = $this->input->post('id');
        $statusValidation = $this->input->post('status');
        $document = $this->uploadDocument->getDocumentById($documentId);
        $upload = $this->upload->getById($document['id_upload']);
        $bookingType = $this->bookingType->getBookingTypeById($upload['id_booking_type']);
        $documentType = $this->documentType->getById($document['id_document_type']);

        $explodeName = explode(' ', $document['document_type']);
        $lastNameDocType = array_pop($explodeName);
        $mainDocType = implode(' ', $explodeName);
        $getMainDocType = $this->documentType->getReservedDocumentType($mainDocType);
        $uploadedDocumentResponse = $this->uploadDocument->getDocumentsByUploadByDocumentType($upload['id'], $document['id_document_type']);        

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Document Data', 'trim|required|integer');
            $this->form_validation->set_rules('status', 'Document Status', 'trim|required|integer|in_list[0,1,-1]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $message = $this->input->post('message');
                $time = new DateTime($document['created_at']);
                $interval = $time->diff(new DateTime());
                $hours = $interval->d * 24 + $interval->h;

                $uploadDocument = $this->uploadDocument->getById($documentId);
                $upload = $this->upload->getById($uploadDocument['id_upload']);

                $this->db->trans_start();

                $this->uploadDocument->updateUploadDocument([
                    'is_valid' => $statusValidation,
                    'validated_by' => UserModel::authenticatedUserData('id'),
                    'validated_at' => sql_date_format('now'),
                    'service_time_document' => $hours . ":" . $interval->format('%i') . ":" . $interval->format('%s'),
                ], $documentId);

                if ($statusValidation == 1) {
                    $this->upload->updateUploadStatus($upload, $uploadDocument);
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    $statusText = 'rejected';
                    if ($statusValidation == 1) {
                        $statusText = 'approved';
                    }

                    $responseFiles = [];
                    $upload = $this->upload->getById($document['id_upload']);
                    $person = $this->people->getById($upload['id_person']);
                    $personContacts = $this->peopleContact->getContactByPerson($upload['id_person']);
                    $mailContact=[];
                    foreach ($personContacts as $personContact) {
                        array_push($mailContact,$personContact['email']);
                    }
                    $mailContact= implode(",",$mailContact);
                    $aju = substr($upload['description'], -4);
                    $files = $this->uploadDocumentFile->getFilesByDocument($document['id']);

                    foreach ($files as $file) {
                        if (empty($file['description2']) && (empty($file['description_date']))) {
                            //$destFile = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $document['directory'] . DIRECTORY_SEPARATOR . $file['source'];
                            $destFile = asset_url($document['directory'] . '/' . $file['source']);
                            $responseFiles[] = $destFile;
                        }
                    }

                    $isDraftDocument = false;
                    $isResponse = $document['is_response'];
                    $isDocumentNeedConfirm = $getMainDocType['is_confirm'];
                    $isDraftMainDocument = $bookingType['default_document'] == $getMainDocType['document_type'] && $lastNameDocType == 'Draft';
                    if ($isResponse && $isDocumentNeedConfirm && $isDraftMainDocument && $statusValidation) {
                        $isDraftDocument = true;

                        // email confirmation for customer users
                        if ($person['confirm_email_source'] == 'USER') {
                            $peopleUsers = $this->peopleUser->getUserByPerson($person['id']);
                            $draftConfirmEmails = implode(',', array_column($peopleUsers, 'email'));
                        } else {
                            $peopleUsers = $this->peopleUser->getUserByPerson($person['id']);
                            if (!empty($peopleUsers)) {
                                // consider for multiple user take only one because we will notify PROFILE email based on setup confirm_email_source
                                $peopleUser = $peopleUsers[0];
                                $peopleUser['email'] = $person['email'];
                                $peopleUsers = [$peopleUser];
                            }
                            $draftConfirmEmails = $person['email'];
                        }
                        foreach ($peopleUsers as $user) {
                            $token = $this->userToken->createToken($user['email'], UserTokenModel::$TOKEN_CONFIRMATION, 32, 1, null, false);
                            if ($token && !empty($user['email'])) {
                                $emailTo = $user['email'];
                                $emailTitle =  "[NEED ACTION] {$upload['no_upload']} - {$person['name']} {$document['document_type']} {$aju} Document Confirmation";
                                $emailTemplate = 'emails/response_confirmation';
                                $emailData = [
                                    'title' => 'Document Confirmation ' . $document['document_type'],
                                    'uploadDocumentId' => $documentId,
                                    'user' => $user,
                                    'token' => $token,
                                    'content' => "Your documents with no upload {$upload['no_upload']} receive new response {$document['document_type']} with no document: {$document['no_document']} is <b>{$statusText}</b> at {$document['document_date']} (See Attachment). Response {$document['document_type']} description : {$message}."
                                ];

                                $attachments = [];
                                foreach ($responseFiles as $responseFile) {
                                    $attachments[] = [
                                        'source' => $responseFile,
                                    ];
                                }

                                $emailOption = [
                                    'attachment' => $attachments,
                                ];
                                if ($documentType['is_email_notification']){
                                    $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOption);
                                }
                            }
                        }

                        // send email to people contacts
                        if (!empty($mailContact)) {
                            $emailTo = $mailContact;
                            $emailTitle = "{$person['name']} {$document['document_type']} {$aju} Document Uploaded";
                            $emailTemplate = 'emails/basic';
                            $emailData = [
                                'title' => 'Document Uploaded',
                                'name' => $person['name'],
                                'email' => $person['email'],
                                'content' => "The draft {$upload['main_docs_name']} document with reference number 
                                    {$upload['description']} has been uploaded by {$document['created_name']}. 
                                    A confirmation or revision email has been sent to the email address:"
                                    . $draftConfirmEmails
                            ];

                            if (!empty($emailTo) && $documentType['is_email_notification']) {
                                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
                            }
                        }

                        // send wa notification to customer group
                        $number = $uploadedDocumentResponse['total_file'];
                        $uploader = UserModel::authenticatedUserData('name');
                        $dataWhatsapp = "[DRAFT] Draft {$bookingType['default_document']} " . ($uploadedDocumentResponse['total_file'] > 1 ? "ke {$number}" : "") . " aju {$aju} telah divalidasi oleh {$uploader}, silahkan periksa email " . (str_replace(',', ', ', $draftConfirmEmails)) . " untuk segera melakukan konfirmasi atau revisi";
                        $this->send_message($dataWhatsapp, $person['whatsapp_group']);

                        flash($statusValidation == 1 ? 'success' : 'warning', "Documents of <strong>{$document['document_type']}</strong> successfully <strong>{$statusText}</strong>");
                    }

                    // add notification other than draft confirmation
                    $branchType = get_active_branch('branch_type');
                    $branchId = get_active_branch('id');
                    if ($branchType != 'TPP' && $branchId != '4') {
                        $emailTo = get_setting('email_compliance');
                        $emailTitle = "{$upload['no_upload']} {$person['name']} {$document['document_type']} {$aju} Document Validation ({$statusText})";
                        if (!$isDraftDocument) {
                            $emailTo = (if_empty($emailTo, '', '', ",") . $person['email']);
                            $emailTitle = "[NO ACTION NEEDED] {$upload['no_upload']} {$person['name']} {$document['document_type']} {$aju} Document Validation ({$statusText})";
                        }
                        $emailTemplate = 'emails/basic';
                        $emailData = [
                            'title' => 'Document Validation',
                            'name' => "User",
                            'email' => "User",
                            'content' => "Recently we review your document that was uploaded before. 
                                The document {$document['document_type']} with number {$document['no_document']} 
                                is <b>{$statusText}</b>.<br><b>{$document['document_type']} Description :</b> "
                                . if_empty($message, 'No description')
                        ];

                        if (!empty($emailTo) && $documentType['is_email_notification']) {
                            $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
                        }
                    }

                    // email reminder payout for booking out
                    $customer = $this->people->getById($upload['id_person']);
                    if($customer['outbound_type'] == 'CASH AND CARRY' && $upload['category'] == 'OUTBOUND' && $document['document_type'] == 'SPPB') {
                        $emailTitle =  "Payout reminder " . $upload['name'] . ' no upload ' . $upload['no_upload'];
                        $emailTemplate = 'emails/basic';
                        $emailData = [
                            'title' => 'Booking Payout',
                            'name' => $customer['name'],
                            'email' => $customer['email'],
                            'content' => "Recently outbound customer {$upload['name']} is uploaded with no {$upload['no_upload']} ({$upload['description']}), please get ready for outbound payout.",
                        ];
                        if(!empty($customer['email']) && $documentType['is_email_notification']){
                            $this->mailer->send($customer['email'], $emailTitle, $emailTemplate, $emailData);
                        }
                    }

                    flash($statusValidation == 1 ? 'success' : 'warning', "Documents of <strong>{$document['document_type']}</strong> successfully <strong>{$statusText}</strong>");

                    // additional notification
                    $data = [
                        'url' => 'dialog',
                        'method' => 'GET',
                        'payload' => [
                            'chatId' => detect_chat_id($person['whatsapp_group']),
                        ]
                    ];
                    $results = $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);
                    $participants = $results['metadata']['participants'];
                    if(!($document['user_type'] == 'EXTERNAL' && $document['document_type'] == 'E Billing') && $statusValidation && ($document['document_type'] == 'SPPB' || $document['document_type'] == 'E Billing')){
                        if($document['user_type'] == 'EXTERNAL'){
                            $mentions = $this->peopleBranchMention->getMentionByPersonBranch($person['id_person_branch'], ['operational','compliance']);
                        }else{
                            $mentions = $this->peopleBranchMention->getMentionByPersonBranch($person['id_person_branch'], ['operational','external']);
                        }                        
                        $tag = '';
                        $tagCus = '';
                        $tagOps = '';
                        $newMentions = [];
                        $newMentionCus = [];
                        $newMentionOps = [];
                        foreach($mentions as $mention){
                            $cekWa = true;
                            foreach ($participants as $key => $participant) {
                                if($participant==$mention['whatsapp']){
                                    $cekWa = false;
                                    break;
                                }
                            }
                            //for check whatsapp inside on group
                            if($cekWa){
                                continue;
                            }
                            if($mention['type'] == 'external' || $mention['type'] == 'compliance'){
                                $tagCus .= " @".invert_chat_id($mention['whatsapp'], true);
                                $newMentionCus[] = invert_chat_id($mention['whatsapp'], true);
                            }
                            if($mention['type'] == 'operational'){
                                $tagOps .= " @".invert_chat_id($mention['whatsapp'], true);
                                $newMentionOps[] = invert_chat_id($mention['whatsapp'], true);
                            }
                            $newMentions[] = invert_chat_id($mention['whatsapp'], true);
                            $tag .= " @".invert_chat_id($mention['whatsapp'], true);
                        }
                        $fixMention = [];
                        if($document['document_type'] == 'SPPB'){
                            if($document['user_type'] == 'EXTERNAL'){
                                $fixMention = $newMentionOps;
                                $message = " [".$upload['category']."] *SPPB* ".$upload['main_docs_name']." aju ".$aju." telah diupload, Tim ops".$tagOps." mohon dibantu untuk proses selanjutnya";
                            }else{
                                $fixMention = $newMentions;
                                $message = $tagCus." [".$upload['category']."] *SPPB* ".$upload['main_docs_name']." aju ".$aju." telah diupload oleh ".UserModel::authenticatedUserData('name').", Tim ops".$tagOps." mohon dibantu untuk proses selanjutnya";
                            }
                        }else{
                            if($document['user_type'] == 'EXTERNAL'){
                                $message = "*".$document['document_type']."* ".$upload['main_docs_name']." aju ".$aju." telah diupload, silahkan untuk segera melakukan pengecekan dan proses selanjutnya";
                            }else{
                                $fixMention = $newMentionCus;
                                $message = "*".$document['document_type']."* ".$upload['main_docs_name']." aju ".$aju." telah diupload oleh ".UserModel::authenticatedUserData('name').",".$tagCus." silahkan periksa email ".$person['email']." untuk segera melakukan pengecekan dan proses selanjutnya";
                            }
                        }
                        $this->notification->broadcast([
                            'url' => 'sendMessage',
                            'method' => 'POST',
                            'payload' => [
                                'chatId' => detect_chat_id($person['whatsapp_group']),
                                'mentionedPhones' => $fixMention,
                                'body' => $message,
                            ]
                        ], NotificationModel::TYPE_CHAT_PUSH);
                    }

                } else {
                    flash('danger', "Validating documents <strong>{$document['document_type']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        if (!empty(get_url_param('redirect'))) {
            redirect(get_url_param('redirect'), false);
        } else {
            redirect('upload/view/' . $document['id_upload']);
        }
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

    /**
     * Delete uploaded data and related document and files
     */
    public function delete()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_DELETE);

        $uploadId = $this->input->post('id_upload');
        $documentId = $this->input->post('id');
        $document = $this->uploadDocument->getDocumentById($documentId);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Document Data', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {

                $delete = $this->uploadDocument->deleteUploadDocument($documentId);

                if ($delete) {
                    flash('warning', "Documents of <strong>{$document['document_type']}</strong> successfully deleted");
                } else {
                    flash('danger', "Delete documents <strong>{$document['document_type']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('upload/view/' . $uploadId);
    }

    /**
     * Check document
     */
    public function check()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_VALIDATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Upload Data', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $uploadDocumentId = $this->input->post('id');
                $uploadId = $this->input->post('id_upload');

                $document = $this->uploadDocument->getById($uploadDocumentId);

                $status = $this->uploadDocument->updateUploadDocument([
                    'is_check' => 1, //check document
                    'checked_by' => UserModel::authenticatedUserData('id'),
                    'checked_at' => date('Y-m-d H:i:s'),
                ], $uploadDocumentId);

                if ($status) {
                    flash('success', "Documents of <strong>{$document['document_type']}</strong> successfully checked.");
                } else {
                    flash('danger', "Checking documents <strong>{$document['document_type']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('upload/view/' . $this->input->post('id_upload'));
    }
}
