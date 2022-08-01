<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Upload_document
 * @property UploadModel $upload
 * @property UploadItemPhotoModel $uploadItemPhoto
 * @property UploadItemPhotoFileModel $uploadItemPhotoFile
 * @property ItemComplianceModel $itemCompliance
 * @property ItemCompliancePhotoModel $itemCompliancePhoto
 * @property BookingTypeModel $bookingType
 * @property PeopleModel $people
 * @property UserTokenModel $userToken
 * @property PeopleContactModel $peopleContact
 * @property NotificationModel $notification
 * @property Uploader $uploader
 */
class Upload_item_photo extends MY_Controller
{
    /**
     * Upload constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->library('zip');

        $this->load->model('UploadModel', 'upload');
        $this->load->model('UploadItemPhotoModel', 'uploadItemPhoto');
        $this->load->model('UploadItemPhotoFileModel', 'uploadItemPhotoFile');
        $this->load->model('ItemComplianceModel', 'itemCompliance');
        $this->load->model('ItemCompliancePhotoModel', 'itemCompliancePhoto');
        $this->load->model('BookingTypeModel', 'bookingType');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('UserTokenModel', 'userToken');
        $this->load->model('UserModel', 'user');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('NotificationModel', 'notification');
        $this->load->model('modules/Uploader', 'uploader');

        $this->setFilterMethods([
            'ajax_get_photo_files' => 'GET',
            'add' => 'POST',
            'validated' => 'POST',
            'delete' => 'POST',
        ]);
    }

    

    /**
     * Get photo files.
     */
    public function ajax_get_photo_files()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $itemPhotoId = $this->input->get('id_photo');
            $files = $this->uploadItemPhotoFile->getFilesByItemPhoto($itemPhotoId);
            header('Content-Type: application/json');
            echo json_encode($files);
        }
    }

    /**
     * Add new item.
     */
    public function add()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_CREATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('item_name', 'Item Name', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $itemComplianceId = $this->input->post('item_name');
                $uploadId = $this->input->post('id_upload');
                $description = $this->input->post('description');
                $branch_type = get_active_branch('branch_type');
                $branch_id = get_active_branch('id');
                $createdBy = $this->user->getById(UserModel::authenticatedUserData('id'));

                $upload = $this->upload->getById($uploadId);
                $customer = $this->people->getById($upload['id_person']);
                $itemCompliance = $this->itemCompliance->getById($itemComplianceId);
                $this->db->trans_start();
                $status = UploadItemPhotoModel::STATUS_ON_REVIEW;
                if($itemCompliance['total_file']>0){
                    $status = UploadItemPhotoModel::STATUS_VALIDATED;
                }
                $this->uploadItemPhoto->create([
                    'item_name' => $itemCompliance['item_name'],
                    'no_hs' => $itemCompliance['no_hs'],
                    'id_item' => $itemCompliance['id'],
                    'id_upload' => $uploadId,
                    'status' => $status,
                    'description' => $description,
                ]);

                if($itemCompliance['total_file']==0){
                    $compAdmin = get_setting('admin_compliance');
                    $ref = substr($upload['description'], -6);

                    $emailTo = get_setting('email_compliance');
                    $emailTitle = $upload['no_upload'] . ' ' . $customer['name'] . ' ' . $ref . " Required Item Photo";
                    $emailTemplate = 'emails/basic';
                    $content = ucfirst($createdBy['name']). " recently add required photo item <strong>{$itemCompliance['item_name']}</strong> with no hs: <strong>{$itemCompliance['no_hs']}</strong> description : <strong>{$description}</strong>. Please wait until customer upload photo.";
                    if(empty($itemCompliance['no_hs'])){
                        $content = ucfirst($createdBy['name']). " recently add required photo item <strong>{$itemCompliance['item_name']}</strong> with description : <strong>{$description}</strong>. Please wait until customer upload photo.";
                    }  
                    $emailData = [
                        'title' => 'Required Photo Item',
                        'name' => $compAdmin,
                        'email' => $emailTo,
                        'content' => $content,
                    ];
                    if($branch_type=='PLB' && $branch_id !='4'){
                        $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
                    }
                    $contentCus = "Your documents with no upload {$upload['no_upload']} required photo item <strong>{$itemCompliance['item_name']}</strong> with no hs: <strong>{$itemCompliance['no_hs']}</strong> description : <strong>{$description}</strong>. Please check and respond immediately.";
                    if(empty($itemCompliance['no_hs'])){
                        $contentCus = "Your documents with no upload {$upload['no_upload']} required photo item <strong>{$itemCompliance['item_name']}</strong> with description : <strong>{$description}</strong>. Please check and respond immediately.";
                    }
                    $emailCustomer = $customer['email'];
                    $emailDataCustomer = [
                        'title' => 'Required Photo Item',
                        'name' => $customer['name'],
                        'email' => $customer['email'],
                        'content' => $contentCus,
                    ];

                    $this->mailer->send($emailCustomer, $emailTitle, $emailTemplate, $emailDataCustomer);

                    //notif wa
                    if($branch_type=='PLB' && $branch_id !='4'){
                        // $waCompAdmin = get_setting('whatsapp_group_admin');
                        $whatsapp_group = $customer['whatsapp_group'];          
                        $textWa = '[NEW REQUIRED PHOTO ITEM] *'.$itemCompliance['item_name'].'* no hs *'.$itemCompliance['no_hs'].'* - *'.$customer['name'].'* - AJU:*'.$ref.'* - '.$upload['branch_name'].' - '.date('d/m/Y H:i').' - Description : *'.if_empty($description,'-').'*. please upload immediately';
                        if(empty($itemCompliance['no_hs'])){
                            $textWa = '[NEW REQUIRED PHOTO ITEM] *'.$itemCompliance['item_name'].'* - *'.$customer['name'].'* - AJU:*'.$ref.'* - '.$upload['branch_name'].' - '.date('d/m/Y H:i').' - Description : *'.if_empty($description,'-').'*. please upload immediately';                            
                        }                        
                        if(!empty($whatsapp_group)){
                            $this->send_message($textWa,$whatsapp_group);
                        }
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Additional photo item <strong>{$itemCompliance['item_name']}</strong> for upload no <strong>{$upload['no_upload']}</strong> successfully added");
                    redirect("upload/view/".$this->input->post('id_upload'));
                } else {
                    flash('danger', "Add photo item <strong>{$itemCompliance['item_name']}</strong> for upload no <strong>{$upload['no_upload']}</strong> failed, try again or contact administrator");
                    redirect("upload/view/".$this->input->post('id_upload'));
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect("upload/view/".$this->input->post('id_upload'));
    }

    /**
     * Update new item photo.
     */
    public function update()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_CREATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $photos = $this->input->post('photos');
            $uploadId = $this->input->post('id_upload');
            $branch_type = get_active_branch('branch_type');
            $branch_id = get_active_branch('id');
            $createdBy = $this->user->getById(UserModel::authenticatedUserData('id'));

            $upload = $this->upload->getById($uploadId);
            $customer = $this->people->getById($upload['id_person']);

            $this->db->trans_start();
            $uploadedPaths = [];
            foreach ($photos as $key => $photo) {
                $this->uploadItemPhoto->update([
                    'item_name' => $photo['item_name'],
                    'no_hs' => $photo['no_hs'],
                ], $photo['id_item_photo']);
                $photoFiles = $this->input->post('file_photo_' . $key . '_name');
                if (!empty($photoFiles)) {
                    foreach ($photoFiles as $key => $photoFile) {
                        $sourceFile = 'temp/' . $photoFile;
                        $destFile = 'upload-item-photo/' . format_date('now', 'Y/m/') . $photoFile;
                        if ($this->uploader->setDriver('s3')->move($sourceFile, $destFile)) {
                            $this->uploadItemPhotoFile->create([
                                'id_item_photo' => $photo['id_item_photo'],
                                'photo' => $photoFile,
                                'src' => $destFile,
                                'url' => $this->uploader->setDriver('s3')->getUrl($destFile),
                            ]);
                            $uploadedPaths[] = $this->uploader->setDriver('s3')->getUrl($destFile);
                        }
                    }
                }
            }
            
            $compAdmin = get_setting('admin_compliance');
            $ref = substr($upload['description'], -6);
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
            $emailTitle = $upload['no_upload'] . ' ' . $customer['name'] . ' ' . $ref . " Uploaded Item Photo";
            $emailTemplate = 'emails/basic';
            $emailData = [
                'title' => 'Uploaded Photo Item',
                'name' => $compAdmin,
                'email' => $emailTo,
                'content' => $customer['name']. " recently upload required photo item no upload <strong>{$upload['no_upload']}</strong>. Please check and respond immediately.",
            ];
            if($branch_type=='PLB' && $branch_id !='4'){
                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
            }
            $emailCustomer = $customer['email'];
            $emailDataCustomer = [
                'title' => 'Uploaded Photo Item',
                'name' => $customer['name'],
                'email' => $customer['email'],
                'content' => ucfirst($createdBy['name']). " recently upload required photo item no upload <strong>{$upload['no_upload']}</strong>. Please wait until our employee is finished checking and validate your photo.",
            ];

            $this->mailer->send($emailCustomer, $emailTitle, $emailTemplate, $emailDataCustomer, $emailOptions);

            //notif wa
            if($branch_type=='PLB' && $branch_id !='4'){
                $waCompAdmin = get_setting('whatsapp_group_admin');
                $textWa = '[CUSTOMER UPLOAD PHOTO ITEM] *'.$upload['no_upload'].'* - *'.$customer['name'].'* - AJU:*'.$ref.'* - '.$upload['branch_name'].' - '.date('d/m/Y H:i');
                $this->send_message($textWa,$waCompAdmin);
            }
            

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Additional photo item for upload no <strong>{$upload['no_upload']}</strong> successfully added");
                redirect("upload/view/".$this->input->post('id_upload'));
            } else {
                flash('danger', "Add photo item for upload no <strong>{$upload['no_upload']}</strong> failed, try again or contact administrator");
                redirect("upload/view/".$this->input->post('id_upload'));
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect("upload/view/".$this->input->post('id_upload'));
    }

    /**
     * Approve/reject new item photo.
     */
    public function validated()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_VALIDATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {

            $statusValidation = $this->input->post('status');
            $photoId = $this->input->post('id');
            $uploadId = $this->input->post('id_upload');
            $itemCompliance = $this->input->post('item_name');
            $description = $this->input->post('message');
            $branch_type = get_active_branch('branch_type');
            $branch_id = get_active_branch('id');
            $createdBy = $this->user->getById(UserModel::authenticatedUserData('id'));

            $upload = $this->upload->getById($uploadId);
            $item = $this->uploadItemPhoto->getById($photoId);
            $photoFiles = $this->uploadItemPhotoFile->getFilesByItemPhoto($photoId);
            $customer = $this->people->getById($upload['id_person']);
            
            if(empty($photoFiles)){
                flash('danger', "Validate photo item for upload no <strong>{$upload['no_upload']}</strong> failed, due photo not found or crash");
                redirect("upload/view/".$this->input->post('id_upload'));
            }
            
            $this->db->trans_start();
            if($statusValidation =='1'){
                $status = UploadItemPhotoModel::STATUS_VALIDATED;
                // $noHsExist = $this->itemCompliance->getBy([
                //     'ref_item_compliances.id!=' => $itemCompliance,
                //     'ref_item_compliances.no_hs' => $item['no_hs'],
                //     'ref_item_compliances.id_customer' => $upload['id_person'],
                // ]);
                // if(!empty($noHsExist)){
                //     $this->session->set_flashdata([
                //         'status' => 'danger',
                //         'message' => "Validate photo item for upload no <strong>{$upload['no_upload']}</strong> failed because the HS number already exists",
                //     ]);
                //     redirect("upload/view/".$this->input->post('id_upload'));
                // }

                $this->uploadItemPhoto->update([
                    'id_item' => $itemCompliance,
                    'status' => $status,
                    'description_validated' => $description,
                ], $photoId);
                $this->itemCompliance->update([
                    'no_hs' => $item['no_hs'],
                ], $itemCompliance);
    
                $data = [];
                foreach ($photoFiles as $key => $file) {
                    $data [] = [ 
                        'id_item' => $itemCompliance,
                        'photo' => $file['photo'],
                        'src' => $file['src'],
                        'url' => $file['url'],
                    ];
                }
                $this->itemCompliancePhoto->create($data);
            }else{
                $status = UploadItemPhotoModel::STATUS_REJECTED;
                $this->uploadItemPhoto->update([
                    'status' => $status,
                    'description_validated' => $description,
                ], $photoId);
                $this->uploadItemPhoto->create([
                    'item_name' => $item['item_name'],
                    'id_item' => if_empty($item['id_item'], NULL),
                    'no_hs' => $item['no_hs'],
                    'id_upload' => $uploadId,
                    'status' => UploadItemPhotoModel::STATUS_ON_REVIEW,
                    'description' => 'Generate because reject',
                ]);
            }

            if($status == UploadItemPhotoModel::STATUS_VALIDATED){
                $status = 'VALID';
            }
            $compAdmin = get_setting('admin_compliance');
            $ref = substr($upload['description'], -3);

            $emailTo = get_setting('email_compliance');
            $emailTitle = $upload['no_upload'] . ' ' . $customer['name'] . ' ' . $ref . " " . $status . " Item Photo";
            $emailTemplate = 'emails/basic';
            $emailData = [
                'title' => 'Validated Photo Item',
                'name' => $compAdmin,
                'email' => $emailTo,
                'content' => "Recently we review your photo item that was uploaded before. The photo {$item['item_name']} with no HS {$item['no_hs']} is <b>{$status}</b>.<br><b>Description : </b> " . if_empty($description, 'No description')
            ];
            if($branch_type=='PLB' && $branch_id !='4'){
                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
            }
            $emailCustomer = $customer['email'];
            $emailDataCustomer = [
                'title' => 'Validated Photo Item',
                'name' => $customer['name'],
                'email' => $customer['email'],
                'content' => "Recently we review your photo item that was uploaded before. The photo {$item['item_name']} with no HS {$item['no_hs']} is <b>{$status}</b>.<br><b>Description : </b> " . if_empty($description, 'No description')
            ];

            $this->mailer->send($emailCustomer, $emailTitle, $emailTemplate, $emailDataCustomer);

            //notif wa
            if($branch_type=='PLB' && $branch_id !='4'){
                // $waCompAdmin = get_setting('whatsapp_group_admin');
                $whatsapp_group = $customer['whatsapp_group'];          
                if($status == UploadItemPhotoModel::STATUS_REJECTED){
                    $text = ' Please upload again soon';
                }else{
                    $text = '';
                }
                $textWa = '[VALIDATED PHOTO ITEM] *'.$item['item_name'].'* is *' . $status . '* - *'.$item['no_hs'].'* - '.$upload['branch_name'].' - '.date('d/m/Y H:i').' - Description : *'.if_empty($description,'-').'*.'.$text;
                if(!empty($whatsapp_group)){
                    $this->send_message($textWa,$whatsapp_group);
                }
            }
            
            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                if($statusValidation =='1'){
                    flash('success', "Photo item <strong>{$item['item_name']}</strong> for upload no <strong>{$upload['no_upload']}</strong> successfully Approved");
                }else{
                    flash('danger', "Photo item <strong>{$item['item_name']}</strong> for upload no <strong>{$upload['no_upload']}</strong> successfully Rejected");
                }
                redirect("upload/view/".$this->input->post('id_upload'));
            } else {
                flash('danger', "Validate photo item for upload no <strong>{$upload['no_upload']}</strong> failed, try again or contact administrator");
                redirect("upload/view/".$this->input->post('id_upload'));
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect("upload/view/".$this->input->post('id_upload'));
    }

    /**
     * Delete uploaded data and related photo and files
     */
    public function delete()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_DELETE);

        $uploadId = $this->input->post('id_upload');
        $photoId = $this->input->post('id');
        $photo = $this->uploadItemPhoto->getPhotoById($photoId);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Item Name', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {

                $delete = $this->uploadItemPhoto->delete($photoId);

                if ($delete) {
                    flash('warning', "Photo <strong>{$photo['item_name']}</strong> successfully deleted");
                } else {
                    flash('danger', "Delete photo <strong>{$photo['item_name']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('upload/view/' . $uploadId);
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
