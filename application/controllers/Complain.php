<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Complain
 * @property BranchModel $branchModel
 * @property ComplainModel $complainModel
 * @property ComplainKpiModel $complainKpi
 * @property ComplainKpiWhatsappModel $complainKpiWhatsapp
 * @property EmployeeModel $employeeModel
 * @property ComplainHistoryModel $complainHistory
 * @property ComplainCategoryModel $complainCategoryModel
 * @property PeopleModel $people
 * @property DepartmentModel $departmentModel
 * @property UserModel $user
 * @property Uploader $uploader
 * @property Mailer $mailer
 */
class Complain extends MY_Controller
{
    /**
     * Complain constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BranchModel', 'branchModel');
        $this->load->model('ComplainModel', 'complainModel');
        $this->load->model('ComplainCategoryModel', 'complainCategoryModel');
        $this->load->model('ComplainHistoryModel', 'complainHistory');
        $this->load->model('PeopleModel', 'people'); 
        $this->load->model('DepartmentModel', 'departmentModel'); 
        $this->load->model('UserModel', 'user');
        $this->load->model('EmployeeModel', 'employeeModel');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
        $this->load->model('ComplainKpiModel', 'complainKpi');
        $this->load->model('ComplainKpiWhatsappModel', 'complainKpiWhatsapp');
        $this->load->model('NotificationModel', 'notification');
        $this->load->model('modules/Uploader', 'uploader');

        $this->setFilterMethods([
            'investigation' => 'GET',
            'view_investigation' => 'GET',
            'save_investigation' => 'POST',
            'edit_investigation' => 'GET',
            'close' => 'POST',
            'result' => 'GET',
            'print' => 'GET',
            'ajax_get_department_detail_by_department_name' => 'GET', 
            'ajax_get_category_detail' => 'GET',
            'upload' => 'POST',
            'approval' => 'POST',
            'conclusion' => 'POST',
            'disprove' => 'POST',
            'response' => 'GET',
            'save_response' => 'POST',
            'view_response' => 'GET',
            'rating' => 'POST',
            'set_final' => 'POST',
            'final_response' => 'POST',
            'edit_conclusion_category' => 'GET',
            'update_conclusion_category' => 'POST',
        ]);
    }

    /**
     * Show list of complains.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_VIEW);

        if(UserModel::authenticatedUserData('user_type') == 'INTERNAL'){
            $complains = $this->complainModel->getAll();
        }else{
            $complains = $this->complainModel->getByCondition([
                'complains.id_customer' => UserModel::authenticatedUserData('id_person'),
            ]);
        }
        foreach ($complains as &$complain) {
            $waitingResult = $this->checkForWaitingTime($complain);
            $complain['max_date_waiting'] = $waitingResult['max_date_waiting'];
            $complain['allow_set_final_conclusion'] = $waitingResult['allow_set_final_conclusion'];
        }

        $profil = $this->employeeModel->getBy(['ref_employees.id_user' => UserModel::authenticatedUserData('id')], true);
        $ftkp_number = $this->complainModel->getAutoNumberFTKP();

        $this->render('complain/index', compact('complains', 'profil','ftkp_number'));
    }

    private function checkForWaitingTime($complain)
    {
        $maxDateWaiting = '';
        $allowSetFinalConclusion = false;
        if ($complain['status'] == ComplainModel::STATUS_FINAL) {
            $lastFinal = $this->complainHistory->getBy([
                'complain_histories.id_complain' => $complain['id'],
                'complain_histories.status' => ComplainModel::STATUS_FINAL,
            ], true);
            $responseWaitingTime = $this->complainKpi->getBy(['kpi' => ComplainKpiModel::KPI_RESPONSE_WAITING_TIME], true);
            $waitingHours = $responseWaitingTime[strtolower($complain['value_type'])];
            $waitingInterval = date_interval_create_from_date_string("+{$waitingHours} hours");
            $maxDateWaiting = date_create($lastFinal['created_at'])->add($waitingInterval)->format('Y-m-d H:i:s');
            if (date('Y-m-d H:i:s') >= $maxDateWaiting) {
                $allowSetFinalConclusion = true;
            }
        }

        return [
            'max_date_waiting' => $maxDateWaiting,
            'allow_set_final_conclusion' => $allowSetFinalConclusion
        ];
    }

    /**
     * View single complain by id.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_VIEW);

        $complain = $this->complainModel->getById($id);
        $user_type = UserModel::authenticatedUserData('user_type');
        if($user_type == 'INTERNAL'){
            $complainHistories = $this->complainHistory->getBy([
                'complain_histories.id_complain' => $id
            ]);
        }else{
            $complainHistories = $this->complainHistory->getBy([
                'complain_histories.id_complain' => $id,
                'complain_histories.status IN ("SUBMITTED","ON REVIEW","CONCLUSION","CLOSED","DISPROVE","FINAL", "FINAL CONCLUSION", "FINAL RESPONSE", "RATING")' => null
            ]);
        }
        $allowSetFinal = in_array('CONCLUSION', array_column($complainHistories, 'status')) // at least already has conclusion
            && !in_array($complain['status'], ['CLOSED', 'ACCEPTED']) // not available if already closed or approved by customer
            && !in_array('FINAL', array_column($complainHistories, 'status')); // not available if already set final

        $lastConclusions = array_filter($complainHistories, function ($history) {
            return $history['status'] == 'CONCLUSION';
        });
        $lastConclusion = [];
        if (!empty($lastConclusions)) {
            $lastConclusion = end($lastConclusions);
        }

        $waitingResult = $this->checkForWaitingTime($complain);
        $maxDateWaiting = $waitingResult['max_date_waiting'];
        $allowSetFinalConclusion = $waitingResult['allow_set_final_conclusion'];

        $dataLabel = [
            ComplainModel::STATUS_CLOSED => 'info',
            ComplainModel::STATUS_ON_REVIEW => 'warning',
            ComplainModel::STATUS_APPROVE => 'info',
            ComplainModel::STATUS_SUBMITTED => 'default',
            ComplainModel::STATUS_DISPROVE => 'warning',
            ComplainModel::STATUS_FINAL => 'danger',
            ComplainModel::STATUS_FINAL_CONCLUSION => 'warning',
            ComplainModel::STATUS_FINAL_RESPONSE => 'info',
            ComplainModel::STATUS_RATING => 'success',
            ComplainModel::STATUS_PROCESSED => 'primary',
            ComplainModel::STATUS_CONCLUSION => 'success',
            ComplainModel::STATUS_PENDING => 'default',
            ComplainModel::STATUS_REJECT => 'danger',
            ComplainModel::STATUS_ACCEPTED => 'success',
        ];

        $this->render('complain/view', compact('complain','complainHistories', 'dataLabel', 'allowSetFinal', 'lastConclusion', 'maxDateWaiting', 'allowSetFinalConclusion'));
    }

    /**
     * View single complain by id.
     *
     * @param $id
     */
    public function view_investigation($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_INVESTIGATION_VIEW);

        $complain = $this->complainModel->getById($id);
        $profil = $this->employeeModel->getBy(['ref_employees.id_user' => UserModel::authenticatedUserData('id')], true);

        $this->render('complain/view_investigation', compact('complain', 'profil'));
    }

    /**
     * View response by id.
     *
     * @param $id
     */
    public function view_response($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_INVESTIGATION_VIEW);

        $complain = $this->complainModel->getById($id);
        $profil = $this->employeeModel->getBy(['ref_employees.id_user' => UserModel::authenticatedUserData('id')], true);
        $lastDisprove = $this->complainHistory->getLastDisprove($id);
        $lastResponse = $this->complainHistory->getLastResponse($id);

        $this->render('complain/view_response', compact('complain', 'profil', 'lastDisprove', 'lastResponse'));
    }

    /**
     * Show create complain form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_CREATE);

        $categories = $this->complainCategoryModel->getBy([
            'ref_complain_categories.category_type' => ComplainCategoryModel::CATEGORY_COMPLAIN
        ]);
        $via = ComplainModel::VIA_COMPLAIN;
        $people_login = $this->people->getById(UserModel::authenticatedUserData('id_person'));
        $departments = $this->departmentModel->getAll();
        if(UserModel::authenticatedUserData('user_type') == 'INTERNAL'){
            $branches = $this->branchModel->getAll();
        }else{
            $branches = $this->branchModel->getByCustomer(UserModel::authenticatedUserData('id_person'));
        }
        $owners = $this->people->getAll();

        $this->render('complain/create', compact('owners','categories', 'via', 'people_login', 'departments', 'branches'));
    }

    /**
     * Show edit form complain.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_EDIT);

        $complain = $this->complainModel->getById($id);
        $categories = $this->complainCategoryModel->getBy([
            'ref_complain_categories.category_type' => ComplainCategoryModel::CATEGORY_COMPLAIN
        ]);
        $via = ComplainModel::VIA_COMPLAIN;
        $people_login = $this->people->getById(UserModel::authenticatedUserData('id_person'));
        $departments = $this->departmentModel->getAll();
        $customer = $this->people->getById($complain['id_customer']);

        $this->render('complain/edit', compact('customer','complain', 'categories', 'via', 'people_login', 'departments'));
    }

    /**
     * Show investigation form complain.
     *
     * @param $id
     */
    public function investigation($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_INVESTIGATION_CREATE);

        $complain = $this->complainModel->getById($id);
        $customer = $this->people->getById($complain['id_customer']);

        $this->render('complain/create_investigation', compact('customer','complain'));
    }

    /**
     * Show investigation form complain.
     *
     * @param $id
     */
    public function save_investigation($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_INVESTIGATION_CREATE);

        $complain = $this->complainModel->getById($id);
        $department = $this->departmentModel->getBy(['department' =>  $complain['department']], true);
        $customer = $this->people->getById($complain['id_customer']);
        $investigation_result = $this->input->post('investigation');
        $corrective = $this->input->post('corrective');
        $prevention = $this->input->post('prevention');
        $userPosition = UserModel::authenticatedUserData('position_level');

        if($complain['value_type'] == ComplainCategoryModel::TYPE_MAJOR && empty($_FILES['investigation_attachment']['name'])){
            flash('warning', "Please fill out the investigation attachment !");
            return $this->investigation($id);
        }

        //upload attachment if exist
        $filePath = '';
        $fileName = '';
        $uploadPassed = true;

        if (!empty($_FILES['investigation_attachment']['name'])) {
            $extension = pathinfo($_FILES['investigation_attachment']['name'], PATHINFO_EXTENSION);
            $fileName = 'INVESTIGATION_ATTACHMENT_' . time() . '_' . rand(100, 999). '.' . $extension;
            $upload = $this->uploader->setDriver('s3')->uploadTo('investigation_attachment', [
                'destination' => 'complain/' . date('Y/m'),
                'file_name' => $fileName
            ]);
            if ($upload) {
                $uploadedData = $this->uploader->getUploadedData();
                $filePath = $uploadedData['uploaded_path'];
            } else {
                $uploadPassed = false;
                flash('warning', $this->uploader->getDisplayErrors());
                return $this->investigation($id);
            }
        }
        
        $this->db->trans_start();
        $update = $this->complainModel->update([
            'investigation_result' => $investigation_result,
            'corrective' => $corrective,
            'prevention' => $prevention,
            'investigation_attachment' => $filePath,
            'investigation_date' => date('Y-m-d H:i:s'),
            'status_investigation' => ($userPosition=='MANAGER'|| $userPosition=='DIRECTOR')? ComplainModel::STATUS_APPROVE : ComplainModel::STATUS_PENDING,
            'status' => ComplainModel::STATUS_PROCESSED,
        ], $id);

        $description = 'investigation result : '.$investigation_result.'</br>';
        $description .= 'corrective : '.$corrective.'</br>';
        $description .= 'prevention : '.$prevention.'</br>';
        $complainData = $this->complainModel->getById($id);
        $this->complainHistory->create([
            'id_complain' => $id,
            'attachment' => $filePath,
            'description' => $description,
            'status' => $complainData['status'],
            'status_investigation' => $complainData['status_investigation'],
            'data' => json_encode($complainData),
        ]);

        $created_by = UserModel::authenticatedUserData('name');
        $emailTo = $department['email_pic'].','.get_setting('email_support');
        $emailTitle = $complain['no_complain']. ' - ' . $customer['name'] . '- Investigation Result';
        $emailTemplate = 'emails/basic';
        $emailData = [
            'title' => 'Investigation Result',
            'name' => !empty($department) ? $department['department'] : "User",
            'email' => "User",
            'content' => 'The investigation result data with complain number <b>' . $complain['no_complain'] . '</b> has been created by '. $created_by .'. <b> Investigation : </b>'.$investigation_result,
        ];

        //content wa
        $content = "I want to tell you about processed complain by ". UserModel::authenticatedUserData('name')."\n\n";
        $content = $content."Code       : *" . $complainData['no_complain'] . "*\n";
        $content = $content."Customer   : *" . $customer['name'] . "*\n";
        $content = $content."Date       : *" . date("H:i d F Y") . "*\n";
        $content = $content."investigation result  : *" . $investigation_result . "*\n";
        $content = $content."corrective  : *" . $corrective . "*\n";
        $content = $content."prevention  : *" . $prevention . "*\n";
        $content = $content."status  : *" . $complainData['status_investigation'] . "*\n\n";
        $content = $content."Please response immediately.";
        //send wa notif to CSO
        // $branch = $this->branchModel->getById(get_active_branch('id'));
        // $cso = $this->employeeModel->getById($branch['id_cso']);
        // $chatId = $cso['contact_mobile'];
        $chatId = get_setting('whatsapp_group_complain');
        $chatMessage = "*Investigation Submitted*\n\n";
        $chatMessage .= $content;
        if ($userPosition=='MANAGER'|| $userPosition=='DIRECTOR') {
            $chatMessage .= "\n\nAWAITING : *CSO* (for investigation conclusion)";
        } else {
            $chatMessage .= "\n\nAWAITING : *MANAGER* (for investigation approval)";
        }
        if($complainData['status_investigation'] == ComplainModel::STATUS_APPROVE){
            $this->send_message($chatMessage, $chatId);
        }
        $complainWhatsapp = $this->complainKpiWhatsapp->getBy([
            'id_kpi' => 1,
            'group' => $complainData['department'],
            'id_branch_warehouse' => $complainData['id_branch'],
        ],true);
        if(empty($complainWhatsapp)){
            $complainWhatsapp = $this->complainKpiWhatsapp->getBy([
                'id_kpi' => 1,
                'group' => $complainData['department'],
                'id_branch_warehouse' => 0,
            ],true);
        }
        $chatId = detect_chat_id($complainWhatsapp['contact_group']);
        $this->send_message($chatMessage, $chatId);
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            if($complainData['status_investigation'] == ComplainModel::STATUS_APPROVE){
                // $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
            }
            flash('success', "Complain investigation with number {$complain['no_complain']} successfully created", 'complain');
        } else {
            flash('danger', "Save complain investigation with number {$complain['no_complain']} failed");
        }
    }

    /**
     * Response disprove from user.
     *
     * @param $id
     */
    public function response($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_INVESTIGATION_CREATE);

        $complain = $this->complainModel->getById($id);
        $customer = $this->people->getById($complain['id_customer']);
        $lastDisprove = $this->complainHistory->getLastDisprove($id);

        $this->render('complain/create_response', compact('customer','complain', 'lastDisprove'));
    }

    /**
     * Save response form disprove.
     *
     * @param $id
     */
    public function save_response($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_INVESTIGATION_CREATE);

        $complain = $this->complainModel->getById($id);
        $department = $this->departmentModel->getBy(['department' =>  $complain['department']], true);
        $customer = $this->people->getById($complain['id_customer']);
        $response = $this->input->post('response');
        $userPosition = UserModel::authenticatedUserData('position_level');

        //upload attachment if exist
        $filePath = '';
        $fileName = '';
        $uploadPassed = true;

        if (!empty($_FILES['attachment']['name'])) {
            $extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
            $fileName = 'ATTACHMENT_' . time() . '_' . rand(100, 999). '.' . $extension;
            $upload = $this->uploader->setDriver('s3')->uploadTo('attachment', [
                'destination' => 'complain/' . date('Y/m'),
                'file_name' => $fileName
            ]);
            if ($upload) {
                $uploadedData = $this->uploader->getUploadedData();
                $filePath = $uploadedData['uploaded_path'];
            } else {
                $uploadPassed = false;
                flash('warning', $this->uploader->getDisplayErrors());
                return $this->response($id);
            }
        }
        
        $this->db->trans_start();
        $update = $this->complainModel->update([
            'status_investigation' => ($userPosition=='MANAGER' || $userPosition=='DIRECTOR')? ComplainModel::STATUS_APPROVE : ComplainModel::STATUS_PENDING,
            'status' => ComplainModel::STATUS_PROCESSED,
        ], $id);

        $description = 'Response : '.$response;
        $complainData = $this->complainModel->getById($id);
        $this->complainHistory->create([
            'id_complain' => $id,
            'attachment' => $filePath,
            'description' => $description,
            'status' => $complainData['status'],
            'status_investigation' => $complainData['status_investigation'],
            'data' => json_encode($complainData),
        ]);

        $created_by = UserModel::authenticatedUserData('name');
        $emailTo = $department['email_pic'].','.get_setting('email_support');
        $emailTitle = $complain['no_complain']. ' - ' . $customer['name'] . '- Response Result';
        $emailTemplate = 'emails/basic';
        $emailData = [
            'title' => 'Response Result',
            'name' => !empty($department) ? $department['department'] : "User",
            'email' => "User",
            'content' => 'The Response result data with complain number <b>' . $complain['no_complain'] . '</b> has been created by '. $created_by .'. <b> Response : </b>'.$response,
        ];

        //content wa
        $content = "I want to tell you about processed complain by ". UserModel::authenticatedUserData('name')."\n\n";
        $content = $content."Code       : *" . $complainData['no_complain'] . "*\n";
        $content = $content."Customer   : *" . $customer['name'] . "*\n";
        $content = $content."Date       : *" . date("H:i d F Y") . "*\n";
        $content = $content."Response   : *" . $response . "*\n";
        $content = $content."Status     : *" . $complainData['status_investigation'] . "*\n\n";
        $content = $content."Please response immediately.";
        //send wa notif to CSO
        // $branch = $this->branchModel->getById(get_active_branch('id'));
        // $cso = $this->employeeModel->getById($branch['id_cso']);
        // $chatId = $cso['contact_mobile'];
        $chatId = get_setting('whatsapp_group_complain');
        $chatMessage = "*Response Disprove Submitted*\n\n";
        $chatMessage .= $content;
        $chatMessage .= "\n\nAWAITING: *CSO* (for response conclusion)";
        $this->send_message($chatMessage, $chatId);

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            if($complainData['status_investigation'] == ComplainModel::STATUS_APPROVE){
                // $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
            }
            flash('success', "Complain Response with number {$complain['no_complain']} successfully created", 'complain');
        } else {
            flash('danger', "Save complain response with number {$complain['no_complain']} failed");
        }
    }

    /**
     * Edit single complain by id.
     *
     * @param $id
     */
    public function edit_investigation($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_INVESTIGATION_EDIT);

        $complain = $this->complainModel->getById($id);
        $customer = $this->people->getById($complain['id_customer']);
        // print_debug($complain);
        $this->render('complain/edit_investigation', compact('complain','customer'));
    }

    /**
     * Set complain data validation.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'customer' => 'trim|required',
            'via' => 'trim|required',
            'complain' => 'trim|required|max_length[500]',
        ];


    }

    /**
     * Save data complain.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_CREATE);
        
        if ($this->validate()) {
            $customer = $this->input->post('customer');
            $category = $this->input->post('category');
            $department = $this->input->post('department');
            $via = $this->input->post('via');
            $complain = $this->input->post('complain');
            $complainAttachment = $_FILES['complain_attachment']['name'];
            $complain_date = date('Y-m-d H:i:s');
            $branchId = $this->input->post('branch');

            $departmentBranchEmails = [];
            if(!empty($department)) {
                $departmentDetails = $this->departmentModel->getBy(['department' =>  $department], true);
                $userBranches = $this->user->getByPermission(PERMISSION_COMPLAIN_INVESTIGATION_CREATE, $branchId);
                $userBranches = array_filter($userBranches, function($userBranch) use ($departmentDetails) {
                    return $userBranch['id_department'] == $departmentDetails['id'];
                });
                $departmentBranchEmails = array_column($userBranches, 'email');
                if(empty($departmentDetails['email_pic']) && is_null($departmentDetails['email_pic'])){
                    //flash('warning', "Please Set Email PIC !");
                    //return $this->create();
                }
            }

            $this->db->trans_start();

             //upload attachment if exist
            $filePath = '';
            $fileName = '';
            $uploadPassed = true;

            if (!empty($_FILES['complain_attachment']['name'])) {
                $extension = pathinfo($_FILES['complain_attachment']['name'], PATHINFO_EXTENSION);
                $fileName = 'COMPLAIN_ATTACHMENT_' . time() . '_' . rand(100, 999). '.' . $extension;
                $upload = $this->uploader->setDriver('s3')->uploadTo('complain_attachment', [
                    'destination' => 'complain/' . date('Y/m'),
                    'file_name' => $fileName
                ]);
                if ($upload) {
                    $uploadedData = $this->uploader->getUploadedData();
                    $filePath = $uploadedData['uploaded_path'];
                } else {
                    $uploadPassed = false;
                    flash('warning', $this->uploader->getDisplayErrors());
                    return $this->create();
                }
            }
            
            $created_by = UserModel::authenticatedUserData('name');
            $user_type = UserModel::authenticatedUserData('user_type');

            $complain_number = $this->complainModel->getAutoNumberComplain();
            $this->complainModel->create([
                'no_complain' => $complain_number,
                'id_customer' => $customer,
                'id_complain_category' => $category,
                'id_branch' => $branchId,
                'department' => $department,
                'via' => $via,
                'complain' => $complain,
                'complain_date' => $complain_date,
                'complaint_attachment' => $filePath,
                'pic_date' => !empty($department) ? $complain_date : null,
                'status' => $user_type == 'INTERNAL' ? ComplainModel::STATUS_ON_REVIEW : ComplainModel::STATUS_SUBMITTED ,
            ]);            
            $complainId = $this->db->insert_id();
            $complainData = $this->complainModel->getById($complainId);

            $this->complainHistory->create([
                'id_complain' => $complainId,
                'attachment' => $filePath,
                'status' => $complainData['status'],
                'description'=> $user_type == 'INTERNAL' ? 'Set Category By CSO' : 'Complain : '.$complain,
                'data' => json_encode($complainData),
            ]);

            $this->db->trans_complete();

            $customer_data = $this->people->getById($customer);
            $branch = $this->branchModel->getById(get_active_branch('id'));
            $cso = $this->employeeModel->getById($branch['id_cso']);
            $complain_category = $this->complainCategoryModel->getById($category);
            
            //content wa
            $content = "I want to tell you about a new complaint by ". $customer_data['name']."\n\n";
            $content = $content."Code       : *" . $complain_number . "*\n";
            if($user_type == 'INTERNAL'){
                $content = $content."Category   : *" . $complain_category['category'] . " (" .$complain_category['value_type'].")". "*\n";
            }
            $content = $content."Customer   : *" . $customer_data['name'] . "*\n";
            if($user_type == 'INTERNAL'){
                $content = $content."Department : *" . $department . "*\n";
            }
            $content = $content."Date       : *" . format_date($complain_date,"H:i:s d F Y") . "*\n";
            $content = $content."Complaint  : *" . $complain . "*\n\n";
            $content = $content."The complaint data has been created by ". $created_by .". We will check and respond immediately.";
            if ($user_type == 'INTERNAL') {
                $content .= "\n\nAWAITING : *" . $department . "* (for investigation)";
            } else {
                $content .= "\n\nAWAITING : *CSO* (for set complain category)";
            }

            $complainWhatsapp = $this->complainKpiWhatsapp->getBy([
                'id_kpi' => 1,
                'group' => $department,
                'id_branch_warehouse' => $complainData['id_branch'],
            ],true);
            if(empty($complainWhatsapp)){
                $complainWhatsapp = $this->complainKpiWhatsapp->getBy([
                    'id_kpi' => 1,
                    'group' => $department,
                    'id_branch_warehouse' => 0,
                ],true);
            }
            $emailTo = get_setting('email_support');
            $cc = [$customer_data['email']];
            if(empty($department)){
                //$emailTo = get_setting('email_support').','.$customer_data['email']; // send to CS and Customer

                //send wa notif to CSO
                // $chatId = $cso['contact_mobile'];
                $chatId = get_setting('whatsapp_group_complain');
                $chatMessage = "*Complain Submitted*\n\n";
                $chatMessage .= $content;
            }else{
                //$emailTo = $departmentDetails['email_pic'].','.get_setting('email_support').','.$customer_data['email']; // send to PIC, CSO, Customer
                $cc = array_merge($cc, $departmentBranchEmails);

                //send wa notif to whatsapp group complain
                $chatId = detect_chat_id($complainWhatsapp['contact_group']);
                $chatMessage = "*Complain Submitted*\n\n";
                $chatMessage .= $content;
            }
            //conten email
            $content = "I want to tell you about a new complaint by ". $customer_data['name']."</br></br>";
            $content = $content."<table>";
            $content = $content.'<tr>
                                    <td style="width:150px">Code</td> 
                                    <td style="width:5px">:</td>
                                    <td><b>' . $complain_number . '</b></td>
                                </tr>';
            if($user_type == 'INTERNAL'){
                $content = $content.'<tr>
                                        <td style="width:150px">Category</td> 
                                        <td style="width:5px">:</td>
                                        <td><b>' . $complain_category['category'] . ' (' .$complain_category['value_type'].')'. '</b></td>
                                    </tr>';
            }
            $content = $content.'<tr>
                                    <td style="width:150px">Customer</td> 
                                    <td style="width:5px">:</td>
                                    <td><b>' . $customer_data['name'] . '</b></td>
                                </tr>';
            if($user_type == 'INTERNAL'){
                $content = $content.'<tr>
                                        <td style="width:150px">Department</td> 
                                        <td style="width:5px">:</td>
                                        <td><b>' . $department . '</b></td>
                                    </tr>';
            }
            $content = $content.'<tr>
                                    <td style="width:150px">Date</td> 
                                    <td style="width:5px">:</td>
                                    <td><b>' . format_date($complain_date,"H:i:s d F Y") . '</b></td>
                                </tr>';
            $content = $content.'<tr>
                                    <td style="width:150px">Complaint</td> 
                                    <td style="width:5px">:</td>
                                    <td><b>' . $complain . '</b></td>
                                </tr>
                                </table></br>';
            $content = $content."The complaint data has been created by ". $created_by .". We will check and respond immediately.";
            $emailTitle = $complain_number. ' - ' . $customer_data['name'] . '- Complain Submitted';
            $emailTemplate = 'emails/basic';
            $emailData = [
                'title' => 'Complain Submitted',
                'name' => !empty($department) ? $department : "User",
                'email' => "User",
                'content' => $content,
            ];

            if ($this->db->trans_status()) {
                $emailOptions = ['cc' => $cc];
                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
                $this->send_message($chatMessage, $chatId);
                flash('success', "Complain with number {$complain_number} successfully created", 'complain');
            } else {
                flash('danger', "Save complain with number {$complain_number} failed");
            }
        }

        $this->create();
    }

    /**
     * Send a message to a new or existing chat.
     * 6281333377368-1557128212@g.us
     */
    public function send_message($text, $whatsapp)
    {
        $data = [
            'url' => 'sendMessage',
            'method' => 'POST',
            'payload' => [
                'chatId' => detect_chat_id($whatsapp),
                'body' => $text,
            ]
        ];

        $result = $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);

        return $result;
    }

    /**
     * Validate complain data.
     *
     * @param $id
     */
    public function close($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_VALIDATE);

        $complain = $this->complainModel->getById($id);
        $customer = $this->people->getById($complain['id_customer']);
        $effective = $this->input->post('effective');
        $ftkp = $this->input->post('ftkp');

        $update = $this->complainModel->update([
            'effective' =>  $effective,
            'ftkp' => $ftkp,
            'status' => ComplainModel::STATUS_CLOSED,
            'close_date' => date('Y-m-d H:i:s')
        ], $id);
        $complainData = $this->complainModel->getById($id);
        
        $this->complainHistory->create([
            'id_complain' => $id,
            'status' => ComplainModel::STATUS_CLOSED,
            'description'=> 'Closed By CSO',
            'data' => json_encode($complainData),
        ]);

        $created_by = UserModel::authenticatedUserData('name');
        $departmentDetails = $this->departmentModel->getBy(['department' =>  $complain['department']], true);

        $complain_category = $this->complainCategoryModel->getById($complainData['id_complain_category']);
        
        $emailTo = $departmentDetails['email_pic'].','.get_setting('email_support').','.$customer['email'];
        $emailTitle = $complain['no_complain']. ' - ' . $customer['name'] . '- Complain Result';
        $emailTemplate = 'emails/basic';
        $emailData = [
            'title' => 'Complain Result',
            'name' => "User",
            'email' => "User",
            'content' => 'The complain data with number '.$complain['no_complain'].' is <b> Closed </b>. <br/><br/>'.'<b> FTKP : </b>'. $ftkp,
        ];

        //content wa
        $content = "I want to tell you about a closed complaint ". $customer['name']."\n\n";
        $content = $content."Code       : *" . $complainData['no_complain'] . "*\n";
        $content = $content."Category   : *" . $complain_category['category'] . " (" .$complain_category['value_type'].")". "*\n";
        $content = $content."Customer   : *" . $customer['name'] . "*\n";
        $content = $content."Department : *" . $departmentDetails['department'] . "*\n";
        $content = $content."Date       : *" . format_date($complainData['complain_date'],"H:i:s d F Y") . "*\n";
        $content = $content."Complaint  : *" . $complainData['complain'] . "*\n";
        if (!empty($complainData['conclusion_category'])) {
            $content = $content."Conclusion Category  : *" . $complainData['conclusion_category'] . "*\n";
        }
        $content = "\n".$content."The complaint data has been closed by ". $created_by .".";

        //whatsapp message
        $chatId = get_setting('whatsapp_group_complain');
        $chatMessage = "*Complain Closed*\n";
        $chatMessage .= $content;
        if (empty($complain['rating'])) {
            $chatMessage .= "\n\nAWAITING: *Customer* (for rating)";
        }

        if ($update) {
            $this->send_message($chatMessage, $chatId);
            // $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
            flash('success', "Complain with number {$complain['no_complain']} validated", 'complain');
        } else {
            flash('danger', "Validate data complain with number {$complain['no_complain']} failed");
        }
    }

    /**
     * View result complain by id.
     *
     * @param $id
     */
    public function result($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_RESULT);

        $complain = $this->complainModel->getById($id);

        $this->render('complain/view_result', compact('complain'));
    }

    /**
     * Update complain data.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_EDIT);
        $this->db->trans_start();

        $department = $this->input->post('department');
        $category = $this->input->post('category');
        $complain_data = $this->complainModel->getById($id);
        $complain_number = $complain_data['no_complain'];

        $departmentBranchEmails = [];
        if(!empty($department)) {
            $departmentDetails = $this->departmentModel->getBy(['department' =>  $department], true);
            $userBranches = $this->user->getByPermission(PERMISSION_COMPLAIN_INVESTIGATION_CREATE, $complain_data['id_branch']);
            $userBranches = array_filter($userBranches, function($userBranch) use ($departmentDetails) {
                return $userBranch['id_department'] == $departmentDetails['id'];
            });
            $departmentBranchEmails = array_column($userBranches, 'email');
            if(empty($departmentDetails['email_pic']) && is_null($departmentDetails['email_pic'])){
                //flash('warning', "Please Set Email PIC !");
                //return $this->edit($id);
            }
        }
        $complainData = $this->complainModel->getById($id);

        //upload attachment if exist
        $filePath = '';
        $fileName = '';
        $uploadPassed = true;

        if (!empty($_FILES['complain_attachment']['name'])) {
            $extension = pathinfo($_FILES['complain_attachment']['name'], PATHINFO_EXTENSION);
            $fileName = 'COMPLAIN_ATTACHMENT_' . time() . '_' . rand(100, 999). '.' . $extension;
            $upload = $this->uploader->setDriver('s3')->uploadTo('complain_attachment', [
                'destination' => 'complain/' . date('Y/m'),
                'file_name' => $fileName
            ]);
            if ($upload) {
                $uploadedData = $this->uploader->getUploadedData();
                $filePath = $uploadedData['uploaded_path'];
            } else {
                $uploadPassed = false;
                flash('warning', $this->uploader->getDisplayErrors());
                return $this->edit($id);
            }
        }

        $this->complainModel->update([
            'id_complain_category' => $category,
            'department' => $department,
            'pic_date' => date('Y-m-d H:i:s'),
            'status' => ComplainModel::STATUS_ON_REVIEW,            
        ], $id);
        
        $this->complainHistory->create([
            'id_complain' => $id,
            'status' => ComplainModel::STATUS_ON_REVIEW,
            'description'=> 'Set Category By CSO',
            'attachment' => $filePath,
            'data' => json_encode($complainData),
        ]);
        
        $this->db->trans_complete();
        $complain_category = $this->complainCategoryModel->getById($category);

        $created_by = UserModel::authenticatedUserData('name');
        $customer_data = $this->people->getById($complain_data['id_customer']);

        //conten email
        $content = "I want to tell you about a new complaint by ". $customer_data['name']."</br></br>";
        $content = $content."<table>";
        $content = $content.'<tr>
                                <td style="width:150px">Code</td> 
                                <td style="width:5px">:</td>
                                <td><b>' . $complain_number . '</b></td>
                            </tr>';
        $content = $content.'<tr>
                                <td style="width:150px">Category</td> 
                                <td style="width:5px">:</td>
                                <td><b>' . $complain_category['category'] . ' (' .$complain_category['value_type'].')'. '</b></td>
                            </tr>';
        $content = $content.'<tr>
                                <td style="width:150px">Customer</td> 
                                <td style="width:5px">:</td>
                                <td><b>' . $customer_data['name'] . '</b></td>
                            </tr>';
        $content = $content.'<tr>
                                <td style="width:150px">Department</td> 
                                <td style="width:5px">:</td>
                                <td><b>' . $department . '</b></td>
                            </tr>';
        $content = $content.'<tr>
                                <td style="width:150px">Date</td> 
                                <td style="width:5px">:</td>
                                <td><b>' . format_date($complain_data['complain_date'],"H:i:s d F Y") . '</b></td>
                            </tr>';
        $content = $content.'<tr>
                                <td style="width:150px">Complaint</td> 
                                <td style="width:5px">:</td>
                                <td><b>' . $complain_data['complain'] . '</b></td>
                            </tr>
                            </table></br>';
        $content = $content.'The complain data with number <b>' . $complain_data['no_complain'] . '</b> has been updated by '. $created_by .'. For more further information please contact our customer support.';
        // email message
        //$emailTo = $departmentDetails['email_pic'].','.get_setting('email_support');
        $emailTo = get_setting('email_support');
        $emailTitle = $complain_data['no_complain']. ' - ' . $customer_data['name'] . '- Complain Information';
        $emailTemplate = 'emails/basic';
        $emailData = [
            'title' => 'Complain Information',
            'name' => "User",
            'email' => "User",
            'content' => $content,
        ];

        //content wa
        $content = "I want to tell you about a new complaint by ". $customer_data['name']."\n\n";
        $content = $content."Code       : *" . $complain_number . "*\n";
        $content = $content."Category   : *" . $complain_category['category'] . " (" .$complain_category['value_type'].")". "*\n";
        $content = $content."Customer   : *" . $customer_data['name'] . "*\n";
        $content = $content."Department : *" . $department . "*\n";
        $content = $content."Date       : *" . format_date($complain_data['complain_date'],"H:i:s d F Y") . "*\n";
        $content = $content."Complaint  : *" . $complain_data['complain'] . "*\n\n";
        $content = $content."The complaint data has been created by ". $created_by .". We will check and respond immediately.";

        //whatsapp message
        $complainWhatsapp = $this->complainKpiWhatsapp->getBy([
            'id_kpi' => 1,
            'group' => $department,
            'id_branch_warehouse' => $complain_data['id_branch'],
        ],true);
        if(empty($complainWhatsapp)){
            $complainWhatsapp = $this->complainKpiWhatsapp->getBy([
                'id_kpi' => 1,
                'group' => $department,
                'id_branch_warehouse' => 0,
            ],true);
        }
        $chatId = detect_chat_id($complainWhatsapp['contact_group']);
        $chatMessage = "*Complain Updated*\n\n";
        $chatMessage .= $content;
        $chatMessage .= "The complaint data with number *" . $complain_number . "* has been updated by ". $created_by .". Please check and respond immediately.\n\n";
        $chatMessage .= "AWAITING : *{$department}* (for investigation)";

        if ($this->db->trans_status()) {
            $emailOptions = ['cc' => $departmentBranchEmails];
            $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
            $this->send_message($chatMessage, $chatId);
            flash('success', "Complain with number {$complain_data['no_complain']} updated", 'complain');
        } else {
            flash('danger', "Update data complain with number {$complain_data['no_complain']} failed");
        }

        $this->edit($id);
    }

    /**
     * Perform deleting data complain.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_DELETE);

         $complain = $this->complainModel->getById($id);

        if ($this->complainModel->delete($id, true)) {
            flash('warning', "Complain with number <strong> {$complain['no_complain']} </strong> successfully deleted");
        } else {
            flash('danger', "Delete complain {$complain['no_complain']} failed");
        }

        redirect('complain');
    }

     /**
     * Print complain data.
     * @param $id
     */
    public function print($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_PRINT);

        $complain = $this->complainModel->getById($id);
        $kabag = $this->employeeModel->getBy(['ref_departments.department' => $complain['department'], 'ref_employees.position_level' => PeopleModel::POSITION_LEVEL_MANAGER ], true);
        $complainHistories = $this->complainHistory->getHistoryDisprove($complain['id']);

        $data = [
            'title' => "Complain",
            'subtitle' => "Print complain",
            'page' => "complain/print_complain",
            'complain' => $complain,
            'kabag' => $kabag,
            'complainHistories' => $complainHistories,
        ];

        $report = $this->load->view('template/print_complain', $data, true);
        $dompdf = new \Dompdf\Dompdf(['isHtml5ParserEnabled' => true, 'isRemoteEnabled' => true]);
        $dompdf->loadHtml($report);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();
        $dompdf->stream("complain.pdf", array("Attachment" => false));

        // $this->load->view('template/print_complain', $data);

    }

     /**
     * upload complain data.
     * @param $id
     */
    public function upload($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_UPLOAD);

        $complain = $this->complainModel->getById($id);

        $this->db->trans_start();

        //upload attachment if exist
        $filePath = '';
        $fileName = '';
        $uploadPassed = true;
        if (!empty($_FILES['attachment']['name'])) {
            $extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
            $fileName = 'COMPLAIN_' . time() . '_' . rand(100, 999). '.' . $extension;
            $upload = $this->uploader->setDriver('s3')->uploadTo('attachment', [
                'destination' => 'complain/' . date('Y/m'),
                'file_name' => $fileName
            ]);
            if ($upload) {
                $uploadedData = $this->uploader->getUploadedData();
                $filePath = $uploadedData['uploaded_path'];
            } else {
                $uploadPassed = false;
                flash('warning', $this->uploader->getDisplayErrors());
                redirect('complain');
            }
        }

        if ($uploadPassed) {

            $data = [
                'attachment' => $filePath,
            ];

            $this->complainModel->update($data, $id);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            flash('success', "Upload data complain <strong>{$complain['no_complain']}</strong> successfully created");
        } else {
            flash('danger', "Upload data complain <strong>{$complain['no_complain']}</strong> failed, try again or contact administrator");
        }
        redirect('complain');
    }

    /**
     * approval if investigate by non manager.
     *
     */
    public function approval()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_VALIDATE);

        $id = $this->input->post('id_complain');
        $complain = $this->complainModel->getById($id);
        $customer = $this->people->getById($complain['id_customer']);
        $approval = $this->input->post('approval');
        $note = $this->input->post('note');

        //upload attachment if exist
        $filePath = '';
        $fileName = '';
        $uploadPassed = true;
        if (!empty($_FILES['attachment']['name'])) {
            $extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
            $fileName = 'APPROVAL_' . time() . '_' . rand(100, 999). '.' . $extension;
            $upload = $this->uploader->setDriver('s3')->uploadTo('attachment', [
                'destination' => 'complain/' . date('Y/m'),
                'file_name' => $fileName
            ]);
            if ($upload) {
                $uploadedData = $this->uploader->getUploadedData();
                $filePath = $uploadedData['uploaded_path'];
            } else {
                $uploadPassed = false;
                flash('warning', $this->uploader->getDisplayErrors());
                redirect('complain');
            }
        }
        $this->db->trans_start();
        $this->complainModel->update([
            'note' => $note,
            'status_investigation' => $approval == ComplainModel::STATUS_APPROVE? ComplainModel::STATUS_APPROVE : ComplainModel::STATUS_REJECT,
        ], $id);
        $complainAfter = $this->complainModel->getById($id);

        $this->complainHistory->create([
            'id_complain' => $id,
            'description' => 'Note : '.$note,
            'attachment' => $filePath,
            'status' => $complainAfter['status'],
            'status_investigation' => $complainAfter['status_investigation'],
            'data' => json_encode($complainAfter)
        ]);
        //content wa
        $content = "I want to tell you about processed complain by ". UserModel::authenticatedUserData('name')."\n\n";
        $content = $content."Code       : *" . $complainAfter['no_complain'] . "*\n";
        $content = $content."Customer   : *" . $customer['name'] . "*\n";
        $content = $content."Date       : *" . date("H:i d F Y") . "*\n";
        $content = $content."Note       : *" . $note . "*\n\n";
        $content = $content."Please response immediately.";
        //send wa notif to CSO
        // $branch = $this->branchModel->getById(get_active_branch('id'));
        // $cso = $this->employeeModel->getById($branch['id_cso']);
        // $chatId = $cso['contact_mobile'];
        $complainWhatsapp = $this->complainKpiWhatsapp->getBy([
            'id_kpi' => 1,
            'group' => $complainAfter['department'],
            'id_branch_warehouse' => $complainAfter['id_branch'],
        ],true);
        if(empty($complainWhatsapp)){
            $complainWhatsapp = $this->complainKpiWhatsapp->getBy([
                'id_kpi' => 1,
                'group' => $complainAfter['department'],
                'id_branch_warehouse' => 0,
            ],true);
        }
        $chatId = detect_chat_id($complainWhatsapp['contact_group']);
        $chatIdCom = get_setting('whatsapp_group_complain');
        $chatMessage = "*".$approval." Investigation Submitted*\n\n";
        $chatMessage .= $content;
        if ($approval == ComplainModel::STATUS_APPROVE) {
            $chatMessage .= "\n\nAWAITING: *CSO* (for investigation conclusion)";
        } else {
            $chatMessage .= "\n\nAWAITING: {$complainAfter['department']} (for investigation revision)";
        }
        $this->send_message($chatMessage, $chatId);
        $this->send_message($chatMessage, $chatIdCom);
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            // $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
            flash('success', "Complain with number {$complain['no_complain']} ". $approval, 'complain');
        } else {
            flash('danger', "Approval data complain with number {$complain['no_complain']} failed");
        }
    }

    /**
     * conclusion.
     *
     */
    public function conclusion()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_VALIDATE);

        $id = $this->input->post('id_complain');
        $complain = $this->complainModel->getById($id);
        $customer = $this->people->getById($complain['id_customer']);
        $conclusion = $this->input->post('conclusion');
        $email_to = $this->input->post('email_to');
        $email_cc = $this->input->post('email_cc');

        //upload attachment if exist
        $filePath = '';
        $fileName = '';
        $uploadPassed = true;
        if (!empty($_FILES['attachment']['name'])) {
            $extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
            $fileName = 'CONCLUSION_' . time() . '_' . rand(100, 999). '.' . $extension;
            $upload = $this->uploader->setDriver('s3')->uploadTo('attachment', [
                'destination' => 'complain/' . date('Y/m'),
                'file_name' => $fileName
            ]);
            if ($upload) {
                $uploadedData = $this->uploader->getUploadedData();
                $filePath = $uploadedData['uploaded_path'];
            } else {
                $uploadPassed = false;
                flash('warning', $this->uploader->getDisplayErrors());
                redirect('complain');
            }
        }
        $this->db->trans_start();
        $this->complainModel->update([
            'conclusion' => $conclusion,
            'email_to' => $email_to,
            'email_cc' => $email_cc,
            'status' => ComplainModel::STATUS_CONCLUSION,
        ], $id);
        $complainAfter = $this->complainModel->getById($id);

        $this->complainHistory->create([
            'id_complain' => $id,
            'description' => 'Conclusion : '.$conclusion,
            'attachment' => $filePath,
            'status' => $complainAfter['status'],
            'status_investigation' => $complainAfter['status_investigation'],
            'data' => json_encode($complainAfter)
        ]);

        $created_by = UserModel::authenticatedUserData('name');
        $departmentDetails = $this->departmentModel->getBy(['department' =>  $complain['department']], true);

        $content = "Mohon maaf sebelumnya atas ketidaknyamanannya,
        terkait komplain no ".$complain['no_complain']." ".$complainAfter['category'].",
        Terlampir kami sampaikan atas case ini sebagai berikut: <br/><br/>
        <strong>Result</strong> : <br/>". nl2br(htmlspecialchars($conclusion))."<br/><br/>Demikian disampaikan terima kasih atas perhatian dan kerjasamanya.";
        $emailTo = $email_to;
        $emailTitle = '[COMPLAIN_'.$complain['no_complain'].'] ' .$complainAfter['category'] .' - ' . $customer['name'];
        $emailTemplate = 'emails/email_conclusion';
        $emailData = [
            'title' => '',
            'name' => $customer['name'],
            'email' => "User",
            'content' => $content,
        ];
        $emailOptions = [
            'cc' => $email_cc,
            'attachment' => [
                [
                    'source' => !empty($filePath)?$this->uploader->setDriver('s3')->getUrl($filePath):'',
                    'disposition' => 'attachment',
                    'file_name' => $fileName,
                ]
            ],
            'subject_title' => true,
        ];

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
            flash('success', "Conclusion complain with number {$complain['no_complain']} created", 'complain');
        } else {
            flash('danger', "Conclusion data complain with number {$complain['no_complain']} failed");
        }
    }

    /**
     * disprove if investigate by non manager.
     *
     */
    public function disprove()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_VIEW);

        $id = $this->input->post('id_complain');
        $complain = $this->complainModel->getById($id);
        $customer = $this->people->getById($complain['id_customer']);
        $disprove = $this->input->post('disprove');
        $note = $this->input->post('note');

        $rating = $this->input->post('rating');
        $reason = $this->input->post('reason');

        //upload attachment if exist
        $filePath = '';
        $fileName = '';
        $uploadPassed = true;
        if (!empty($_FILES['attachment']['name'])) {
            $extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
            $fileName = 'DISPROVE_' . time() . '_' . rand(100, 999). '.' . $extension;
            $upload = $this->uploader->setDriver('s3')->uploadTo('attachment', [
                'destination' => 'complain/' . date('Y/m'),
                'file_name' => $fileName
            ]);
            if ($upload) {
                $uploadedData = $this->uploader->getUploadedData();
                $filePath = $uploadedData['uploaded_path'];
            } else {
                $uploadPassed = false;
                flash('warning', $this->uploader->getDisplayErrors());
                redirect('complain');
            }
        }
        $this->db->trans_start();
        if($disprove == ComplainModel::STATUS_ACCEPTED){
            $update = $this->complainModel->update([
                'rating' =>  $rating,
                'rating_reason' => $reason,
                'status' => ComplainModel::STATUS_ACCEPTED,
            ], $id);
            $complainAfter = $this->complainModel->getById($id);
            
            $this->complainHistory->create([
                'id_complain' => $id,
                'status' => ComplainModel::STATUS_ACCEPTED,
                'description'=> 'Accepted : '.$rating.' star, '.$reason,
                'attachment' => $filePath,
                'status_investigation' => $complainAfter['status_investigation'],
                'data' => json_encode($complainAfter)
            ]);
        }else{
            $this->complainModel->update([
                'status' => $disprove == ComplainModel::STATUS_DISPROVE?ComplainModel::STATUS_DISPROVE : ComplainModel::STATUS_ACCEPTED,
                'status_investigation' => $disprove == ComplainModel::STATUS_DISPROVE?ComplainModel::STATUS_PENDING : ComplainModel::STATUS_APPROVE,
            ], $id);
            $complainAfter = $this->complainModel->getById($id);
    
            $this->complainHistory->create([
                'id_complain' => $id,
                'description' => $disprove == ComplainModel::STATUS_DISPROVE? 'Disprove : '.$note : 'Accepted : '.$note ,
                'attachment' => $filePath,
                'status' => $complainAfter['status'],
                'status_investigation' => $complainAfter['status_investigation'],
                'data' => json_encode($complainAfter)
            ]);
        }

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            //content wa
            $messageContent = $disprove == ComplainModel::STATUS_DISPROVE? $note : $rating.' star, '.$reason;
            $content = "I want to tell you about a new ". ucfirst($disprove) ." by ". $customer['name']."\n\n";
            $content = $content."Code       : *" . $complainAfter['no_complain'] . "*\n";
            $content = $content."Customer   : *" . $customer['name'] . "*\n";
            $content = $content."Date       : *" . date("H:i d F Y") . "*\n";
            $content = $content. ucfirst($disprove) ."  : *" . $messageContent . "*\n\n";
            $content = $content."Please response immediately.";
            //send wa notif to CSO
            // $branch = $this->branchModel->getById(get_active_branch('id'));
            // $cso = $this->employeeModel->getById($branch['id_cso']);
            // $chatId = $cso['contact_mobile'];
            $chatId = get_setting('whatsapp_group_complain');
            $chatMessage = "*". ucfirst($disprove) ." Submitted*\n\n";
            $chatMessage .= $content;
            if ($disprove == ComplainModel::STATUS_DISPROVE) {
                $chatMessage .= "\n\nAWAITING : *{$complain['department']}* (for disprove response)";
            } else {
                $chatMessage .= "\n\nAWAITING : *CSO* (for set final category conclusion)";
            }
            $this->send_message($chatMessage, $chatId);
            //department
            $complainWhatsapp = $this->complainKpiWhatsapp->getBy([
                'id_kpi' => 1,
                'group' => $complainAfter['department'],
                'id_branch_warehouse' => $complainAfter['id_branch'],
            ],true);
            if(empty($complainWhatsapp)){
                $complainWhatsapp = $this->complainKpiWhatsapp->getBy([
                    'id_kpi' => 1,
                    'group' => $complainAfter['department'],
                    'id_branch_warehouse' => 0,
                ],true);
            }
            $chatId = detect_chat_id($complainWhatsapp['contact_group']);
            $this->send_message($chatMessage, $chatId);
            // $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
            flash('success', "Complain with number {$complain['no_complain']} ". $disprove, 'complain');
        } else {
            flash('danger', ucfirst($disprove) ." data complain with number {$complain['no_complain']} failed");
        }
    }

    /**
     * Rating complain data.
     *
     * @param $id
     */
    public function rating($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_VIEW);

        $complain = $this->complainModel->getById($id);
        $rating = $this->input->post('rating');
        $reason = $this->input->post('reason');

        $update = $this->complainModel->update([
            'rating' =>  $rating,
            'rating_reason' => $reason,
        ], $id);

        $complainData = $this->complainModel->getById($id);
        
        $this->complainHistory->create([
            'id_complain' => $id,
            'status' => ComplainModel::STATUS_RATING,
            'description'=> 'Rating: ' . $rating . ', ' . $reason,
            'data' => json_encode($complainData),
        ]);

        if ($update) {
            flash('success', "Complain with number {$complain['no_complain']} give rating successfully", 'complain');
        } else {
            flash('danger', "Rating data complain with number {$complain['no_complain']} failed");
        }
    }

    /**
     * Set final complain.
     *
     * @param $id
     */
    public function set_final($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_EDIT);

        $complain = $this->complainModel->getById($id);
        $description = $this->input->post('description');

        $uploadPassed = true;
        $filePath = null;
        if (!empty($_FILES['attachment']['name'])) {
            $extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
            $fileName = 'FINAL_' . uniqid() . '.' . $extension;
            $uploadPassed = $this->uploader->setDriver('s3')->uploadTo('attachment', [
                'destination' => 'complain/' . date('Y/m'),
                'file_name' => $fileName
            ]);
            if ($uploadPassed) {
                $uploadedData = $this->uploader->getUploadedData();
                $filePath = $uploadedData['uploaded_path'];
            } else {
                flash('warning', $this->uploader->getDisplayErrors());
            }
        }

        if ($uploadPassed) {
            $this->db->trans_start();

            $this->complainModel->update([
                'status' => ComplainModel::STATUS_FINAL,
            ], $id);

            $this->complainHistory->create([
                'id_complain' => $id,
                'status' => ComplainModel::STATUS_FINAL,
                'description' => 'Set Final : ' . if_empty($description, 'Final Session'),
                'attachment' => $filePath,
                'data' => json_encode($this->complainModel->getById($id)),
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                $customer = $this->people->getById($complain['id_customer']);
                $content = "Mohon maaf sebelumnya atas ketidaknyamanannya,
                    terkait komplain no ".$complain['no_complain']." ".$complain['category'].",
                    Terlampir kami sampaikan atas case ini sebagai berikut: <br/><br/>
                    <strong>Final Result</strong> : <br/>". nl2br(htmlspecialchars($description))."<br/><br/>Demikian disampaikan terima kasih atas perhatian dan kerjasamanya.";
                $emailTo = $complain['email_to'];
                $emailTitle = '[COMPLAIN_'.$complain['no_complain'].'] [FINAL] ' .$complain['category'] .' - ' . $complain['name'];
                $emailTemplate = 'emails/email_conclusion';
                $emailData = [
                    'title' => '',
                    'name' => $customer['name'],
                    'email' => "User",
                    'content' => $content,
                ];
                $emailOptions = [
                    'cc' => $complain['email_cc'],
                    'subject_title' => true,
                ];

                if (!empty($filePath)) {
                    $emailOptions['attachment'] =  [
                        [
                            'source' => $this->uploader->setDriver('s3')->getUrl($filePath),
                            'disposition' => 'attachment',
                            'file_name' => basename($filePath),
                        ]
                    ];
                }
                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);

                flash('success', "Complain with number {$complain['no_complain']} successfully set as FINAL", 'complain');
            } else {
                flash('danger', "Set final data complain with number {$complain['no_complain']} failed");
            }
        }

        $this->view($id);
    }

    /**
     * Add final response.
     *
     * @param $id
     */
    public function final_response($id)
    {
        $canEdit = AuthorizationModel::isAuthorized(PERMISSION_COMPLAIN_EDIT);

        $response = $this->input->post('response');
        $rating = $this->input->post('rating');
        $reason = $this->input->post('reason');

        $complain = $this->complainModel->getById($id);

        $uploadPassed = true;
        $filePath = null;
        if (!empty($_FILES['attachment']['name'])) {
            $extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
            $fileName = 'RESPONSE_' . uniqid() . '.' . $extension;
            $uploadPassed = $this->uploader->setDriver('s3')->uploadTo('attachment', [
                'destination' => 'complain/' . date('Y/m'),
                'file_name' => $fileName
            ]);
            if ($uploadPassed) {
                $uploadedData = $this->uploader->getUploadedData();
                $filePath = $uploadedData['uploaded_path'];
            } else {
                flash('warning', $this->uploader->getDisplayErrors());
            }
        }

        if ($uploadPassed) {
            $this->db->trans_start();

            $this->complainModel->update([
                'rating' => if_empty($rating, $complain['rating']),
                'rating_reason' => if_empty($reason, $complain['rating_reason']),
            ], $id);

            $this->complainHistory->create([
                'id_complain' => $id,
                'status' => ComplainModel::STATUS_FINAL_RESPONSE,
                'description' => 'Response ' . ($canEdit ? ' by CSO' : '') . ': ' . if_empty($response, 'Final Response'),
                'attachment' => $filePath,
                'data' => json_encode($this->complainModel->getById($id)),
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                if (!$canEdit) {
                    $content = "I want to tell you about complain response by ". UserModel::authenticatedUserData('name')."\n\n";
                    $content = $content."Code       : *" . $complain['no_complain'] . "*\n";
                    $content = $content."Customer   : *" . $complain['customer_name'] . "*\n";
                    $content = $content."Date       : *" . date("H:i d F Y") . "*\n";
                    $content = $content."Final Response   : *" . $response . "*\n";
                    $content = $content."Status     : *" . $complain['status_investigation'] . "*\n\n";
                    $content = $content."Please review immediately.";

                    $chatId = get_setting('whatsapp_group_complain');
                    $chatMessage = "*Final Response Submitted*\n\n";
                    $chatMessage .= $content;
                    $chatMessage .= "\n\nAWAITING: *CSO* (review the final response)";
                    $this->send_message($chatMessage, $chatId);
                }
                flash('success', "Complain with number {$complain['no_complain']} successfully responded");
            } else {
                flash('danger', "Add final response complain with number {$complain['no_complain']} failed");
            }
        }

        redirect('complain/view/' . $id);
    }

    /**
     * Edit conclusion category.
     *
     * @param $id
     */
    public function edit_conclusion_category($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_EDIT);

        $complain = $this->complainModel->getById($id);
        $conclusions = $this->complainCategoryModel->getBy([
            'ref_complain_categories.category_type' => ComplainCategoryModel::CATEGORY_CONCLUSION,
            'ref_complain_categories.value_type' => $complain['value_type'],
        ]);

        $this->render('complain/edit_final_conclusion', compact('complain', 'conclusions'));
    }

    /**
     * Update conclusion category.
     *
     * @param $id
     */
    public function update_conclusion_category($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_COMPLAIN_EDIT);

        $complain = $this->complainModel->getById($id);
        $conclusionCategoryId = $this->input->post('conclusion_category');
        $description = $this->input->post('description');

        $uploadPassed = true;
        $filePath = null;
        if (!empty($_FILES['attachment']['name'])) {
            $extension = pathinfo($_FILES['attachment']['name'], PATHINFO_EXTENSION);
            $fileName = 'FINAL_CONCLUSION_' . uniqid() . '.' . $extension;
            $uploadPassed = $this->uploader->setDriver('s3')->uploadTo('attachment', [
                'destination' => 'complain/' . date('Y/m'),
                'file_name' => $fileName
            ]);
            if ($uploadPassed) {
                $uploadedData = $this->uploader->getUploadedData();
                $filePath = $uploadedData['uploaded_path'];
            } else {
                flash('warning', $this->uploader->getDisplayErrors());
            }
        }

        if ($uploadPassed) {
            $conclusionCategory = $this->complainCategoryModel->getById($conclusionCategoryId);

            $this->db->trans_start();

            $this->complainModel->update([
                'id_conclusion_category' => $conclusionCategoryId,
                'status' => ComplainModel::STATUS_FINAL_CONCLUSION,
            ], $id);

            $this->complainHistory->create([
                'id_complain' => $id,
                'status' => ComplainModel::STATUS_FINAL_CONCLUSION,
                'description' => 'Final Conclusion : ' . $conclusionCategory['category'] . "\n" . if_empty($description, 'Set Final Conclusion'),
                'attachment' => $filePath,
                'data' => json_encode($this->complainModel->getById($id)),
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Complain with number {$complain['no_complain']} successfully updated with final conclusion", 'complain');
            } else {
                flash('danger', "Set final conclusion data complain with number {$complain['no_complain']} failed");
            }
        }

        $this->view($id);
    }

     /**
     * Get department detail.
     *
     * @param $department
     */
    public function ajax_get_department_detail_by_department_name()
    {
        $departmentName = $this->input->get('department');
        $department = $this->departmentModel->getBy(['department' => $departmentName], true);

        $this->render_json($department);
    }

    /**
     * Get complain detail.
     *
     * @param $department
     */
    public function ajax_get_category_detail()
    {
        $categoryId = $this->input->get('category');
        $category = $this->complainCategoryModel->getById($categoryId);

        $this->render_json($category);
    }
}