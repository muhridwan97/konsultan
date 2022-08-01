<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Upload
 * @property UploadModel $upload
 * @property UploadDocumentModel $uploadDocument
 * @property UploadDocumentFileModel $uploadDocumentFile
 * @property UploadReferenceModel $uploadReference
 * @property UploadItemPhotoModel $uploadItemPhoto
 * @property UploadItemPhotoFileModel $uploadItemPhotoFile
 * @property UploadUploadDocumentPartyModel $uploadUploadDocumentParty
 * @property BookingTypeModel $bookingType
 * @property DocumentTypeModel $documentType
 * @property StatusHistoryModel $statusHistory
 * @property PeopleModel $people
 * @property NotificationModel $notification
 * @property UserModel $user
 * @property BranchModel $branch
 * @property Uploader $uploader
 * @property Mailer $mailer
 * @property Exporter $exporter
 */
class Upload extends CI_Controller
{
    /**
     * Upload constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('UploadModel', 'upload');
        $this->load->model('UploadDocumentModel', 'uploadDocument');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
        $this->load->model('UploadReferenceModel', 'uploadReference');
        $this->load->model('UploadItemPhotoModel', 'uploadItemPhoto');
        $this->load->model('UploadItemPhotoFileModel', 'uploadItemPhotoFile');
        $this->load->model('UploadDocumentPartyModel', 'uploadDocumentParty');
        $this->load->model('BookingTypeModel', 'bookingType');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('UserModel', 'user');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('NotificationModel', 'notification');
        $this->load->model('BranchModel', 'branch');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('modules/Uploader', 'uploader');
        $this->uploader->setDriver('s3');
    }

    /**
     * Show list upload data.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_VIEW);

        $customer = $this->people->getById($this->input->get('customer'));
        $bookingTypes = $this->bookingType->getAll();
        $documentTypes = $this->documentType->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportLargeResourceFromArray("Uploads", $this->upload->getAll($_GET));
        } else {
            $data = [
                'title' => "Upload",
                'subtitle' => "Data upload",
                'page' => "upload/index",
                'customer' => $customer,
                'bookingTypes' => $bookingTypes,
                'documentTypes' => $documentTypes,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax paging data job document.
     */
    public function ajax_get_data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_VIEW);

        $filters = array_merge(get_url_param('filter') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);

        $data = $this->upload->getAll($filters);

        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($data));
    }

    /**
     * Show view upload and documents.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_VIEW);

        $upload = $this->upload->getById($id);
        $uploadReferences = $this->uploadReference->getBy(['upload_references.id_upload' => $id]);
        $uploadIn = !empty($upload['id_upload']) ? $this->upload->getById($upload['id_upload']) : null;
        $documents = $this->uploadDocument->getDocumentsByUpload($id);
        $photos = $this->uploadItemPhoto->getPhotosByUpload($id , 1);
        $itemCompliances = $this->uploadItemPhoto->getPhotosByUpload($id , 0);
        $statusHistories = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_UPLOAD,
            'status_histories.id_reference' => $id
        ]);
        $allowSetAP = !empty(array_filter($documents, function($doc) {
            return $doc['is_valid'] && in_array($doc['id_document_type'], [52, 191, 192]); // bpn, skb, masterlist
        }));

        $data = [
            'title' => "Upload",
            'subtitle' => "View upload",
            'page' => "upload/view",
            'upload' => $upload,
            'uploadReferences' => $uploadReferences,
            'documents' => $documents,
            'uploadIn' => $uploadIn,
            'id' => $id,
            'photos' => $photos,
            'itemCompliances' => $itemCompliances,
            'statusHistories' => $statusHistories,
            'allowSetAP' => $allowSetAP
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show create upload form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_CREATE);

        $data = [
            'title' => "Upload",
            'subtitle' => "Create Upload",
            'page' => "upload/create",
            'types' => $this->bookingType->getBookingTypesByCustomer(UserModel::authenticatedUserData('id_person')),
            'customers' => $this->people->getByType(PeopleModel::$TYPE_CUSTOMER)
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
            $this->form_validation->set_rules('customer', 'Customer or supplier', 'trim|required');
            $this->form_validation->set_rules('type', 'Type upload', 'trim|required');
            $this->form_validation->set_rules('description', 'Upload Description', 'trim|required|max_length[500]');
            
            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $branch = $this->input->post('branch');
                $customerId = $this->input->post('customer');
                $bookingTypeId = $this->input->post('type');
                $description = $this->input->post('description');
                $isHold = $this->input->post('is_hold');
                $holdDescription = $this->input->post('hold_description');
                $createdBy = $this->user->getById(UserModel::authenticatedUserData('id'));
                $uploadInId = $this->input->post('upload_in');
                $photos = $this->input->post('photos');

                $bookingType = $this->bookingType->getById($bookingTypeId);
                if ($bookingType['category'] == 'OUTBOUND') {
                    if ($bookingType['type'] == BookingTypeModel::TYPE_IMPORT) {
                        $this->form_validation->set_rules('upload_in', 'Upload in', 'trim|required');
                    } else {
                        $this->form_validation->set_rules('upload_in[]', 'Upload in', 'trim|required');
                    }
                    if ($this->form_validation->run() == FALSE) {
                        flash('warning', 'Form inputs are invalid, Upload in must be filled', 'upload/create');
                    }
                }
                $branchDetail = $this->branch->getById($branch);
                $documentTypes = $this->documentType->getByBookingType($bookingTypeId);
                $is_required = array_column($documentTypes, 'is_required');

                $this->db->trans_start();

                $uploadInReference = null;
                if ($bookingType['category'] == BookingTypeModel::CATEGORY_OUTBOUND && $bookingType['type'] == BookingTypeModel::TYPE_IMPORT) {
                    if (is_array($uploadInId)) {
                        $uploadInReference = if_empty($uploadInId[0], null);
                    } else {
                        $uploadInReference = if_empty($uploadInId, null);
                    }
                }

                $customer = $this->people->getById($customerId);
                $noUpload = $this->upload->getAutoNumberUpload();
                $this->upload->create([
                    'no_upload' => $noUpload,
                    'id_branch' => $branch,
                    'id_upload' => $uploadInReference,
                    'id_person' => $customerId,
                    'id_booking_type' => $bookingTypeId,
                    'description' => $description,
                    'is_hold' => $isHold,
                    'status' => UploadModel::STATUS_NEW
                ]);
                $uploadId = $this->db->insert_id();
                $upload = $this->upload->getById($uploadId);

                // set booking reference
                if ($bookingType['category'] == BookingTypeModel::CATEGORY_OUTBOUND) {
                    if (empty($uploadInReference)) {
                        log_message('error', json_encode($_POST));
                    }
                    if (!empty($uploadInId) && !is_array($uploadInId)) {
                        $uploadInId = [$uploadInId];
                    }
                    foreach ($uploadInId as $uploadRefId) {
                        $this->uploadReference->create([
                            'id_upload' => $uploadId,
                            'id_upload_reference' => $uploadRefId
                        ]);
                    }
                }

                $this->statusHistory->create([
                    'id_reference' => $uploadId,
                    'type' => StatusHistoryModel::TYPE_UPLOAD,
                    'status' => UploadModel::STATUS_NEW,
                    'description' => 'Create new upload document',
                    'data' => json_encode([
                        'no_upload' => $upload['no_upload'],
                        'description' => $upload['description'],
                        'category' => $upload['category'],
                        'booking_type' => $upload['booking_type'],
                        'customer' => $upload['name'],
                    ])
                ]);

                if ($isHold) {
                    $this->statusHistory->create([
                        'id_reference' => $uploadId,
                        'type' => StatusHistoryModel::TYPE_UPLOAD,
                        'status' => UploadModel::STATUS_HOLD,
                        'description' => if_empty(trim($holdDescription), 'Document is hold'),
                    ]);
                }

                $sppbDocument = null;
                $sppfDocument = null;
                $draftDocument = null;
                $billingDocument = null;
                $bpnDocument = null;

                $uploadedPaths = [];
                $documentTitles = [];
                $dataDocumentType = [];
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
                        if (strpos($documentType['document_type'], 'SPPF') !== false) {
                            $sppfDocument = $this->uploadDocument->getDocumentById($uploadDocumentId);
                        }
                        if (strpos($documentType['document_type'], 'SPPB') !== false) {
                            $sppbDocument = $this->uploadDocument->getDocumentById($uploadDocumentId);
                        }
                        if (strpos(strtolower($documentType['document_type']), 'draft') !== false) {
                            $draftDocument = $this->uploadDocument->getDocumentById($uploadDocumentId);
                        }
                        if (strpos($documentType['document_type'], 'E Billing') !== false) {
                            $billingDocument = $this->uploadDocument->getDocumentById($uploadDocumentId);
                        }
                        if (strpos($documentType['document_type'], 'BPN') !== false) {
                            $bpnDocument = $this->uploadDocument->getDocumentById($uploadDocumentId);
                        }

                        $files = $this->input->post('doc_file_' . $documentType['id'] . '_name');
                        if (!empty($files)) {
                            foreach ($files as $file) {
                                $fileData = $this->uploadDocumentFile->createUploadDocumentFile([
                                    'id_upload_document' => $uploadDocumentId,
                                    'source' => $file,
                                    'created_by' => UserModel::authenticatedUserData('id')
                                ]);
                                if ($fileData) {
                                    //$sourceFile = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . $file;
                                    //$destFile = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $documentType['directory'] . DIRECTORY_SEPARATOR . $file;
                                    //$this->uploadDocumentFile->moveTo($sourceFile, $destFile);
                                    //$uploadedPaths[] = $destFile;
                                    $sourceFile = 'temp/' . $file;
                                    $destFile = $documentType['directory'] . '/' . $file;
                                    $this->uploader->move($sourceFile, $destFile);
                                    $uploadedPaths[] = asset_url($destFile);
                                }
                            }
                        }
                    }

                    if (!empty($documentNumber)) {
                        $dataDocumentType[] = $documentNumber;
                    }
                }

                /*
                if (!empty($draftDocument)) {
                    $upload['status'] = $this->upload->updateUploadStatus($upload, $draftDocument);
                }

                if (!empty($billingDocument)) {
                    $upload['status'] = $this->upload->updateUploadStatus($upload, $billingDocument);
                }

                if (!empty($bpnDocument)) {
                    $upload['status'] = $this->upload->updateUploadStatus($upload, $bpnDocument);
                }

                if (!empty($sppbDocument)) {
                    $upload['status'] = $this->upload->updateUploadStatus($upload, $sppbDocument);
                }
                */

                // upload photos
                foreach ($photos as $key => $photo) {
                    $photoFiles = $this->input->post('file_photo_' . $key . '_name');
                    if (!empty($photoFiles)) {
                        $this->uploadItemPhoto->create([
                            'id_upload' => $uploadId,
                            'item_name' => $photo['item_name'],
                            'no_hs' => $photo['no_hs'],
                        ]);
                        $uploadPhotoId = $this->db->insert_id();
                        foreach ($photoFiles as $key => $photoFile) {
                            $sourceFile = 'temp/' . $photoFile;
                            $destFile = 'upload-item-photo/' . format_date('now', 'Y/m/') . $photoFile;
                            if ($this->uploader->setDriver('s3')->move($sourceFile, $destFile)) {
                                $this->uploadItemPhotoFile->create([
                                    'id_item_photo' => $uploadPhotoId,
                                    'photo' => $photoFile,
                                    'src' => $destFile,
                                    'url' => $this->uploader->setDriver('s3')->getUrl($destFile),
                                ]);
                                $uploadedPaths[] = $this->uploader->setDriver('s3')->getUrl($destFile);
                            }
                        }
                    }
                }

                if ((empty($dataDocumentType) == true) && (in_array(1, $is_required) == true)) {
                    flash('danger', "Upload no <strong>{$noUpload}</strong> failed, please add your document!", 'upload');
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    if (!empty($documentTitles)) {
                        if (count($documentTitles) > 2) {
                            $lastDocumentType = array_pop($documentTitles);
                            $allDocumentTypes = implode(', ', $documentTitles);
                            $allDocumentTypes .= ', and ' . $lastDocumentType;
                            $stsText = 'Document details of : ';
                        } else if (count($documentTitles) == 2) {
                            $lastDocumentType = array_pop($documentTitles);
                            $allDocumentTypes = implode(', ', $documentTitles);
                            $allDocumentTypes .= ' and ' . $lastDocumentType;
                            $stsText = 'Document details of : ';
                        } else {
                            $lastDocumentType = array_pop($documentTitles);
                            $allDocumentTypes = $lastDocumentType;
                            $stsText = 'Document is ';
                        }
                    } else {
                        $stsText = 'No Document detail';
                        $allDocumentTypes = '';
                    }

                    $compAdmin = get_setting('admin_compliance');
                    $ref = substr($description, -3);

                    $attachments = [];
                    foreach ($uploadedPaths as $uploadedPath) {
                        $attachments[] = [
                            'source' => $uploadedPath,
                        ];
                    }

                    $emailOptions = [
                        'attachment' => $attachments,
                    ];

                    $emailTo = get_setting('email_compliance');
                    $emailTitle = $noUpload . ' ' . $customer['name'] . ' ' . $ref . " Upload New Document";
                    $emailTemplate = 'emails/basic';
                    $emailData = [
                        'title' => 'Upload New Document',
                        'name' => $compAdmin,
                        'email' => $emailTo,
                        'content' => 'The customer recently upload document with title <b>' . $description . '</b>. ' . $stsText . $allDocumentTypes . '. Please check and respond immediately.',
                    ];
                    if($branchDetail['branch_type']=='PLB' && $branchDetail['id']!='4'){
                        $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
                    }
                    $emailCustomer = $customer['email'];
                    $emailDataCustomer = [
                        'title' => 'Upload New Document',
                        'name' => $customer['name'],
                        'email' => $customer['email'],
                        'content' => ucfirst($createdBy['name']). ' recently upload document with title <b>' . $description . '</b>. ' . $stsText . $allDocumentTypes . '. Please wait until our employee is finished checking and validate your document.',
                    ];
                    $this->mailer->send($emailCustomer, $emailTitle, $emailTemplate, $emailDataCustomer, $emailOptions);

                    //notif wa
                    if($branchDetail['branch_type']=='PLB' && $branchDetail['id']!='4'){
                        $waCompAdmin = get_setting('whatsapp_group_admin');
                        $textWa = '[NEW DOCUMENT UPLOADED] *'.$bookingType['category'].'* document *'.$bookingType['booking_type'].'* - *'.$customer['name'].'* - '.$branchDetail['branch'].' - '.date('d/m/Y H:i', strtotime($upload['created_at']));
                        $this->send_message($textWa,$waCompAdmin);
                    }

                    if (!empty($sppfDocument)) {
                        $this->sppf_notification($upload, $sppfDocument);
                    }
                    if (!empty($sppbDocument)) {
                        $this->sppb_notification($sppbDocument);
                    }
                    flash('success', "Upload no <strong>{$noUpload}</strong> successfully created", 'upload');
                } else {
                    flash('danger', "Upload no <strong>{$noUpload}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->create();
    }

    private function sppf_notification($upload, $sppfDocument) {
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

    /**
     * Get uploaded user data by booking type.
     */
    public function ajax_get_uploads_by_booking_type()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $bookingTypeId = $this->input->get('id_booking_type');
            $customerId = $this->input->get('id_customer');
            $supplierId = $this->input->get('id_supplier');
            $except = $this->input->get('except');
            $category = $this->input->get('category');
            $uploadData = $this->upload->getUploadsByBookingType([
                'id_booking_type' => $bookingTypeId,
                'id_customer' => $customerId,
                'id_supplier' => $supplierId,
                'available_only' => true,
                'except' => $except,
                'category' => $category,
                //'is_valid' => true,
                'is_booking_ready' => true,
            ]);
            $bookingDocuments = $this->documentType->getByBookingType($bookingTypeId);

            $uploads = [];
            foreach ($uploadData as $data) {
                $documents = $this->uploadDocument->getDocumentsByUpload($data['id']);
                $result = false;
                if(empty($bookingDocuments)) {
                    $bookingDocuments = $this->documentType->getByBookingType($data['id_booking_type']);
                }
                foreach ($bookingDocuments as $bookingDocument) {
                    if (($bookingDocument['is_required'] == 2) && (in_array($bookingDocument['id'], array_column($documents, 'id_document_type')) == false)) {
                        $result = true;
                        break;
                    }
                }

                if ($result == false) {
                    $uploads[] = $data;
                }

            }

            header('Content-Type: application/json');
            echo json_encode($uploads);
        }
    }

    /**
     * Show edit description form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_EDIT);

        $upload = $this->upload->getById($id);
        $data = [
            'title' => "Upload",
            'subtitle' => "Edit upload",
            'page' => "upload/edit",
            'upload' => $upload
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Update data upload by id.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_EDIT);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('description', 'Upload description', 'trim|required|max_length[500]');
            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $description = $this->input->post('description');

                $update = $this->upload->update([
                    'description' => $description
                ], $id);

                $upload = $this->upload->getById($id);

                if ($update) {
                    flash('success', "Upload <strong>{$upload['no_upload']}</strong> successfully updated", 'upload');
                } else {
                    flash('danger', "Update upload description <strong>{$upload['no_upload']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->edit($id);
    }

    /**
     * Show edit upload inbound form.
     * @param $id
     */
    public function edit_upload_in($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_EDIT_UPLOAD_IN);

        $upload = $this->upload->getById($id);
        if(!empty($upload['id_booking'])){
            flash('warning', "Cannot edit <strong>{$upload['no_upload']}</strong> because booking already created", 'upload');
        }
        $uploadIn = $this->upload->getUploadsInByCustomer($upload['id_person']);
        $uploadReferences = $this->uploadReference->getBy(['upload_references.id_upload' => $id]);
        $bookingType = $this->bookingType->getById($upload['id_booking_type']);
        
        if ($bookingType['category'] == 'INBOUND') {
            flash('warning', "Cannot edit <strong>{$upload['no_upload']}</strong> because booking inbound", 'upload');
        }
        $data = [
            'title' => "Upload",
            'subtitle' => "Edit upload in",
            'page' => "upload/edit_upload_in",
            'upload' => $upload,
            'uploadIn' => $uploadIn,
            'uploadReferences' => $uploadReferences,
            'bookingType' => $bookingType,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Update upload data upload in by id.
     * @param $id
     */
    public function update_upload_in($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_EDIT_UPLOAD_IN);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $upload = $this->upload->getById($id);
            $bookingType = $this->bookingType->getById($upload['id_booking_type']);
            if ($bookingType['type'] == BookingTypeModel::TYPE_IMPORT) {
                $this->form_validation->set_rules('upload_in', 'Upload in', 'trim|required');
            } else {
                $this->form_validation->set_rules('upload_in[]', 'Upload in', 'trim|required');
            }
            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $uploadInId = $this->input->post('upload_in');
                $this->db->trans_start();
                if ($bookingType['type'] == BookingTypeModel::TYPE_IMPORT) {
                    $this->upload->update([
                        'id_upload' => $uploadInId,
                    ], $id);
                }else{
                    $this->uploadReference->deleteUploadReferenceByUpload($id);
                    foreach ($uploadInId as $uploadRefId) {
                        $this->uploadReference->create([
                            'id_upload' => $id,
                            'id_upload_reference' => $uploadRefId
                        ]);
                    }
                }

                $upload = $this->upload->getById($id);
                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Upload <strong>{$upload['no_upload']}</strong> successfully updated", 'upload');
                } else {
                    flash('danger', "Update upload reference <strong>{$upload['no_upload']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->edit_upload_in($id);
    }

    /**
     * Delete uploaded data and related document and files
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_DELETE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $upload = $this->upload->getById($id);
            $delete = $this->upload->delete($id);

            if ($delete) {
                flash('warning', "Documents of <strong>{$upload['no_upload']}</strong> successfully deleted");
            } else {
                flash('danger', "Delete documents <strong>{$upload['no_upload']}</strong> failed, try again or contact administrator");
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('upload');
    }

    /**
     * Show response upload form.
     * @param $id
     */
    public function response($id = null)
    {
        if (empty($id)) {
            flash('danger', 'This booking has no documents');
            if (!empty(get_url_param('redirect'))) {
                redirect(get_url_param('redirect'), false);
            } else {
                redirect('upload');
            }
        }

        $upload = $this->upload->getById($id);
        $types = $this->documentType->getAll();
        $uploadDocument = $this->uploadDocument->getDocumentsByUpload($id);
        
        $target = array_column($uploadDocument, "document_type");
        $haystack = ["BC 1.6 Draft", "BC 2.8 Draft", "BC 2.7 Draft"];

        if(!empty(array_intersect($haystack, $target))){
            $item_status = false;            
        }else{
            $item_status = true;           
        }
        
        if (isset($_GET['id_document_type'])) {
            if (empty($_GET['id_document_type'])) {
                flash('danger', 'Document is not set properly');
                if (!empty(get_url_param('redirect'))) {
                    redirect(get_url_param('redirect'), false);
                } else {
                    redirect('delivery-order');
                }
            } else {
                $documentType = $this->documentType->getById($_GET['id_document_type']);
                if (empty($documentType)) {
                    flash('danger', 'Document is not found');
                    if (!empty(get_url_param('redirect'))) {
                        redirect(get_url_param('redirect'), false);
                    } else {
                        redirect('delivery-order');
                    }
                }
            }
        }

        $data = [
            'title' => "Response",
            'subtitle' => "Create Response",
            'page' => "upload/response",
            'upload' => $upload,
            'types' => $types,
            'item_status' => $item_status
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Save new response.
     */
    public function save_response()
    {
        $uploadId = $this->input->post('id');
        $upload = $this->upload->getById($uploadId);
        $default_document = $this->bookingType->getBookingTypeById($upload['id_booking_type']);
        $uploadDocuments = $this->uploadDocument->getDocumentsByUpload($uploadId);
        $person = $this->people->getById($upload['id_person']);
        $createdBy = $this->user->getById(UserModel::authenticatedUserData('id'));

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Upload data', 'trim|required');
            $this->form_validation->set_rules('type', 'Document Type', 'trim|required');
            $this->form_validation->set_rules('description', 'Response Description', 'trim|required|max_length[500]');
            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $subtype = $this->input->post('subtype');
                $documentTypeId = $this->input->post('type');
                $description = $this->input->post('description');
                $documentTotalItem = !empty($this->input->post('total_item')) ? $this->input->post('total_item') : null;
                $doc_type_freetime_date = !empty($this->input->post('freetime_date')) ? format_date($this->input->post('freetime_date')) : null;
                $doc_type_expired_date = !empty($this->input->post('expired_date')) ? format_date($this->input->post('expired_date')) : null;
                $documentNumber = $this->input->post('doc_no');
                $documentDate = format_date($this->input->post('doc_date'));
                $files = $this->input->post('doc_file_name');
                $containerType = !empty($this->input->post('container_type')) ? $this->input->post('container_type') : null;
                $fclParties = !empty($this->input->post('parties')) ? $this->input->post('parties') : null;
                $fclShapes = !empty($this->input->post('shapes')) ? $this->input->post('shapes') : null;
                $lclParty = !empty($this->input->post('lcl_party')) ? $this->input->post('lcl_party') : null;
                $lclShape = !empty($this->input->post('lcl_shape')) ? $this->input->post('lcl_shape') : null;

                $document = $this->documentType->getById($documentTypeId);
                $explodeName = explode(' ', $document['document_type']);
                $lastNameDocType = array_pop($explodeName);
                $mainDocType = implode(' ', $explodeName);
                $ref = substr($upload['description'], -4);

                $complianceAdmin = get_setting('admin_compliance');
                $complianceEmails = explode(',', get_setting('email_compliance'));
                $emailComplianceTo = array_shift($complianceEmails);

                // cannot upload E billing if upload is hold
                $statusReleaseDocuments = ['E Billing', 'BPN (Bukti Penerimaan Negara)', 'SPPB', 'SURAT KETERANGAN BEBAS PAJAK', 'MASTERLIST'];
                if ($upload['is_hold'] && in_array($document['document_type'], $statusReleaseDocuments)) {
                    flash('danger', "Upload {$upload['no_upload']} is HOLD, please release first before upload 'E Billing', 'BPN', 'SPPB', 'SKB', 'MASTERLIST'!", '_back', 'upload/view/' . $uploadId);
                }

                $this->db->trans_start();

                $uploadDocument = null;
                $sppbDocument = null;
                $sppfDocument = null;
                $responseFiles = [];
                $isDuplicate = in_array($documentTypeId, array_column($uploadDocuments, 'id_document_type')); // check duplicate document
                if (!$isDuplicate) {
                    $this->uploadDocument->createUploadDocument([
                        'id_upload' => $uploadId,
                        'id_document_type' => $documentTypeId,
                        'no_document' => $documentNumber,
                        'document_date' => $documentDate,
                        'description' => $description,
                        'subtype' => $subtype,
                        'total_item' => $documentTotalItem,
                        'freetime_date' => $doc_type_freetime_date,
                        'expired_date' => $doc_type_expired_date,
                        'is_response' => 1,
                        'is_valid' => $document['document_type'] == DocumentTypeModel::DOC_TILA ? 1 : 0,
                        'created_by' => UserModel::authenticatedUserData('id')
                    ]);
                    $uploadDocumentId = $this->db->insert_id();
                    $uploadDocument = $this->uploadDocument->getDocumentById($uploadDocumentId);

                    if (strpos($document['document_type'], 'SPPF') !== false) {
                        $sppfDocument = $uploadDocument;
                    }
                    if (strpos($document['document_type'], 'SPPB') !== false) {
                        $sppbDocument = $uploadDocument;
                    }

                    if(!empty($files)) {
                        foreach ($files as $file) {
                            $fileData = $this->uploadDocumentFile->createUploadDocumentFile([
                                'id_upload_document' => $uploadDocumentId,
                                'source' => date('Y/m') . '/' . $file,
                                'created_by' => UserModel::authenticatedUserData('id'),
                                'description' => $description,
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
                    }

                    if(!empty($containerType) && $containerType == 'FCL'){
                        $dataFcl = [];
                        foreach ($fclParties as $key => $party) {
                            $dataFcl[]= [
                                'id_upload_document' => $uploadDocumentId,
                                'type' => $containerType,
                                'party' => $party,
                                'shape' => $fclShapes[$key],
                            ];
                        }
                        $this->uploadDocumentParty->create($dataFcl);
                    }else if(!empty($containerType) && $containerType == 'LCL'){
                        $this->uploadDocumentParty->create([
                            'id_upload_document' => $uploadDocumentId,
                            'type' => $containerType,
                            'party' => $lclParty,
                            'shape' => empty($lclShape)? 'Package' : $lclShape,
                        ]);
                    }
                } else {
                    $uploadedDocumentResponse = $this->uploadDocument->getDocumentsByUploadByDocumentType($uploadId, $documentTypeId);
                    $uploadDocument = $this->uploadDocument->getDocumentById($uploadedDocumentResponse['id']);
                    if ($default_document['default_document'] == $mainDocType && $lastNameDocType == 'Draft') {
                        $this->uploadDocument->updateUploadDocument([
                            'is_valid' => 0,
                            'validated_by' => null,
                            'validated_at' => null,
                        ], $uploadedDocumentResponse['id']);
                    }

                    foreach ($files as $file) {
                        $fileData = $this->uploadDocumentFile->createUploadDocumentFile([
                            'id_upload_document' => $uploadedDocumentResponse['id'],
                            'source' => date('Y/m') . '/' . $file,
                            'created_by' => UserModel::authenticatedUserData('id'),
                            'description' => $description,
                        ]);
                        if ($fileData) {
                            #$sourceFile = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . $file;
                            #$destFile = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $document['directory'] . DIRECTORY_SEPARATOR . $file;
                            #$this->uploadDocumentFile->moveTo($sourceFile, $destFile);
                            #$responseFiles[] = $destFile;

                            $sourceFile = 'temp/' . $file;
                            $destFile = $document['directory'] . '/' . date('Y/m') . '/' . $file;
                            $this->uploader->move($sourceFile, $destFile);
                            $responseFiles[] = asset_url($destFile);
                        }
                    }
                }

                //$this->upload->updateUploadStatus($upload, $uploadDocument);

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    $branch = get_active_branch();

                    if (!empty($sppfDocument)) {
                        $this->sppf_notification($upload, $sppfDocument);
                    }

                    //send email
                    $emailTo = get_setting('email_compliance');
                    $emailTemplate = 'emails/basic';
                    $emailTitle = "{$upload['no_upload']} {$person['name']} {$document['document_type']} {$ref} Response";
                    $emailData = [
                        'title' => 'Response file ' . $document['document_type'],
                        'name' => $complianceAdmin,
                        'email' => $emailComplianceTo,
                        'content' => ucfirst($createdBy['name']) . " recently upload document response with no upload <b>{$upload['no_upload']}</b> document {$document['document_type']} (See Attachment). Response {$document['document_type']} description : {$description}. Please wait until your customer finished checking and responding immediately.",
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
                    if($branch['branch_type']=='PLB' && $branch['id']!='4' && $document['is_email_notification']){
                        $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
                    }

                    if (!empty($sppbDocument)) {
                        $this->sppb_notification($sppbDocument);
                    }

                    flash('success', "Response for <strong>{$upload['no_upload']}</strong> document <strong>{$document['document_type']}</strong> successfully added");

                    if (!empty(get_url_param('redirect'))) {
                        redirect(get_url_param('redirect'), false);
                    } else {
                        redirect('upload/view/' . $upload['id']);
                    }
                } else {
                    flash('danger', "Upload response <strong>{$document['document_type']}</strong> failed, try again or contact administrator");
                }

            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->response($uploadId);
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
     * Validate document (approve/reject)
     */
    public function validate()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_VALIDATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Upload Data', 'trim|required|integer');
            $this->form_validation->set_rules('status', 'Upload Status', 'trim|required|integer|in_list[0,1]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $uploadId = $this->input->post('id');
                $statusValidation = $this->input->post('status');

                $upload = $this->upload->getById($uploadId);
                $person = $this->people->getById($upload['id_person']);
                $status = $statusValidation ? UploadModel::STATUS_CLEARANCE : UploadModel::STATUS_REJECTED;

                $this->db->trans_start();

                $this->upload->update([
                    'is_valid' => $statusValidation,
                    'status' => $status
                ], $uploadId);

                $this->statusHistory->create([
                    'id_reference' => $upload['id'],
                    'type' => StatusHistoryModel::TYPE_UPLOAD,
                    'status' => $status,
                    'description' => "{$upload['no_upload']} is {$status}",
                ]);

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    $statusText = 'rejected';
                    $statusClass = 'warning';
                    if ($statusValidation) {
                        $statusText = 'approved';
                        $statusClass = 'success';

                        if (get_active_branch('dashboard_status')) {
                            $emailTo = $person['email'];
                            $emailTitle = $upload['no_upload'] . ' ' . $person['name'] . ' Upload is ready';
                            $emailTemplate = 'emails/basic';
                            $emailData = [
                                'title' => 'Upload Status',
                                'name' => $person['name'],
                                'email' => $person['email'],
                                'content' => 'Your documents with no upload ' . $upload['no_upload'] . ' is ready to proceed to the next step. For more further information please contact our customer support.'
                            ];
                            $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);

                            $this->notification->broadcast([
                                'url' => 'sendMessage',
                                'method' => 'POST',
                                'payload' => [
                                    'chatId' => detect_chat_id(get_setting('whatsapp_group_admin')),
                                    'body' => "📑 Upload document number *{$upload['no_upload']}* ({$upload['description']}) by {$upload['name']} is *ready to book*.",
                                ]
                            ], NotificationModel::TYPE_CHAT_PUSH);
                        }
                    }
                    flash($statusClass, "Documents of <strong>{$upload['no_upload']}</strong> successfully <strong>{$statusText}</strong>");
                } else {
                    flash('danger', "Validating documents <strong>{$upload['no_upload']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('upload');
    }

    /**
     * Hold upload data.
     *
     * @param $id
     */
    public function hold($id)
    {
        AuthorizationModel::mustAuthorized([PERMISSION_UPLOAD_CREATE, PERMISSION_UPLOAD_VALIDATE]);

        $message = $this->input->post('message');

        $upload = $this->upload->getById($id);

        if (in_array($upload['status'], [UploadModel::STATUS_NEW, UploadModel::STATUS_ON_PROCESS])) {
            $this->db->trans_start();

            $this->upload->update([
                'is_hold' => true,
            ], $id);

            $this->statusHistory->create([
                'id_reference' => $id,
                'type' => StatusHistoryModel::TYPE_UPLOAD,
                'status' => UploadModel::STATUS_HOLD,
                'description' => if_empty($message, 'Document is hold'),
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Upload {$upload['no_upload']} is successfully HOLD", '_back', 'upload');
            } else {
                flash('danger', 'Hold upload failed', '_back', 'upload');
            }
        } else {
            flash('warning', 'Only [NEW] and [ON PROCESS] document that allowed to be hold', '_back', 'upload');
        }
    }

    /**
     * Release upload data.
     *
     * @param $id
     */
    public function release($id)
    {
        AuthorizationModel::mustAuthorized([PERMISSION_UPLOAD_CREATE, PERMISSION_UPLOAD_VALIDATE]);

        $message = $this->input->post('message');

        $upload = $this->upload->getById($id);

        if ($upload['is_hold']) {
            $this->db->trans_start();

            $this->upload->update([
                'is_hold' => false,
            ], $id);

            $this->statusHistory->create([
                'id_reference' => $id,
                'type' => StatusHistoryModel::TYPE_UPLOAD,
                'status' => UploadModel::STATUS_RELEASED,
                'description' => if_empty($message, 'Document is released'),
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Upload {$upload['no_upload']} is successfully RELEASED from HOLD", '_back', 'upload');
            } else {
                flash('danger', 'Hold upload failed', '_back', 'upload');
            }
        } else {
            flash('warning', 'Only [HOLD] document that allowed to be release', '_back', 'upload');
        }
    }

    /**
     * Set analyzing point of upload data.
     *
     * @param $id
     */
    public function set_analyzing_point($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_VALIDATE);

        $message = $this->input->post('message');

        $upload = $this->upload->getById($id);
        $documents = $this->uploadDocument->getDocumentsByUpload($id);

        $allowSetAP = !empty(array_filter($documents, function($doc) {
            return $doc['is_valid'] && in_array($doc['id_document_type'], [52, 191, 192]); // bpn, skb, masterlist
        }));
        if ($upload['status'] == UploadModel::STATUS_PAID || $allowSetAP) {
            $this->db->trans_start();

            $this->upload->update([
                'status' => UploadModel::STATUS_AP,
            ], $id);

            $this->statusHistory->create([
                'id_reference' => $id,
                'type' => StatusHistoryModel::TYPE_UPLOAD,
                'status' => UploadModel::STATUS_AP,
                'description' => if_empty($message, 'Document is Analyzing Point'),
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Upload {$upload['no_upload']} is successfully set as Analyzing Point", '_back', 'upload');
            } else {
                flash('danger', 'Hold upload failed', '_back', 'upload');
            }
        } else {
            flash('warning', 'Only [PAID] document or already upload SKB or MASTERLIST that allowed to be set as Analyzing Point', '_back', 'upload');
        }
    }

    /**
     * Get booking type by id type.
     */
    public function ajax_get_booking_type_by_id()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $bookingTypeId = $this->input->get('id_booking_type');
            $bookingType = $this->bookingType->getBookingTypeById($bookingTypeId);

            header('Content-Type: application/json');
            echo json_encode($bookingType);
        }
    }

      /**
     * Get upload by id.
     */
    public function ajax_get_upload_by_id()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $uploadId = $this->input->get('uploadId');
            $uploadById = $this->upload->getById($uploadId);

            header('Content-Type: application/json');
            echo json_encode($uploadById);
        }
    }

    /**
     * Get upload In by customer.
     */
    public function ajax_get_uploads_in_by_customer()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $customerId = $this->input->get('id_customer');
            $uploadIn = $this->upload->getUploadsInByCustomer($customerId);
            header('Content-Type: application/json');
            echo json_encode($uploadIn);
        }
    }

}
