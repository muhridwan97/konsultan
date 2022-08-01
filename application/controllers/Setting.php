<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Setting
 * @property SettingModel $setting
 * @property HandlingTypeModel $handlingType
 * @property PeopleModel $people
 * @property UnitModel $unit
 * @property EmployeePositionModel $employeePosition
 * @property CorePositionModel $corePosition
 * @property DocumentPositionModel $documentPosition
 * @property UserModel $user
 */
class Setting extends CI_Controller
{
    /**
     * Setting constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('HandlingTypeModel', 'handlingType');
        $this->load->model('SettingModel', 'setting');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('UnitModel', 'unit');
        $this->load->model('EmployeePositionModel', 'employeePosition');
        $this->load->model('CorePositionModel', 'corePosition');
        $this->load->model('DocumentPositionModel', 'documentPosition');
    }

    /**
     * Show setting form.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);

        $settings = $this->setting->getAllSettings();
        $customers = $this->people->getByType(PeopleModel::$TYPE_CUSTOMER);
        $units = $this->unit->getAll();
        $handlingType = $this->handlingType->getAllHandlingTypes();
        $positions = $this->corePosition->getCorePosition();
        $documentPositions = $this->documentPosition->getDocumentPosition();

        $data = [
            'title' => "Setting",
            'subtitle' => "System preferences",
            'page' => "setting/index",
            'settings' => $settings,
            'customers' => $customers,
            'units' => $units,
            'handlingTypes' => $handlingType,
            'positions' => $positions,
            'documentPositions' => $documentPositions,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Save settings.
     */
    public function update()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('app_name', 'Application name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('app_version', 'Application version', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('meta_url', 'Url', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('meta_keywords', 'Meta keywords', 'trim|required|max_length[300]');
            $this->form_validation->set_rules('meta_description', 'Meta description', 'trim|required|max_length[500]');
            $this->form_validation->set_rules('email_bug_report', 'Email bug report', 'trim|required|max_length[50]|valid_email');
            $this->form_validation->set_rules('email_support', 'Email customer support', 'trim|required|max_length[200]');
            $this->form_validation->set_rules('email_finance', 'Email finance', 'trim|required|max_length[200]');
            $this->form_validation->set_rules('email_finance2', 'Email finance', 'trim|required|max_length[200]');
            $this->form_validation->set_rules('email_compliance', 'Email compliance', 'trim|required|max_length[200]');
            $this->form_validation->set_rules('operations_administrator_email', 'Email operational', 'trim|required|max_length[200]');
            $this->form_validation->set_rules('admin_developer', 'Admin developer', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('admin_operational', 'Admin ops', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('admin_support', 'Admin support', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('admin_finance', 'Admin finance', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('admin_finance2', 'Admin finance', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('admin_compliance', 'Admin compliance', 'trim|required');
            $this->form_validation->set_rules('whatsapp_group_admin', 'Whatsapp Group Admin', 'trim|max_length[50]');
            $this->form_validation->set_rules('whatsapp_group_submission', 'Whatsapp Group Submission', 'trim|max_length[50]');
            $this->form_validation->set_rules('whatsapp_group_transfer', 'Whatsapp Group Transfer', 'trim|max_length[50]');
            $this->form_validation->set_rules('whatsapp_group_complain', 'Whatsapp Group Complain', 'trim|max_length[50]');
            $this->form_validation->set_rules('whatsapp_group_management', 'Whatsapp Group Management', 'trim|max_length[50]');
            $this->form_validation->set_rules('default_inbound_handling', 'Inbound handling', 'trim|required|integer');
            $this->form_validation->set_rules('default_outbound_handling', 'Outbound handling', 'trim|required|integer');
            $this->form_validation->set_rules('default_moving_in_handling', 'Moving in handling', 'trim|required|integer');
            $this->form_validation->set_rules('default_moving_out_handling', 'Moving out handling', 'trim|required|integer');
            $this->form_validation->set_rules('password', 'Current password', 'trim|required|callback_match_password');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                if (!isset($_POST['strict_upload'])) {
                    $_POST['strict_upload'] = 0;
                }
                if (!isset($_POST['strict_booking'])) {
                    $_POST['strict_booking'] = 0;
                }
                if (!isset($_POST['strict_delivery_order'])) {
                    $_POST['strict_delivery_order'] = 0;
                }
                if (!isset($_POST['strict_safe_conduct'])) {
                    $_POST['strict_safe_conduct'] = 0;
                }
                if (!isset($_POST['strict_print_limitation'])) {
                    $_POST['strict_print_limitation'] = 0;
                }
                if (!isset($_POST['strict_handling'])) {
                    $_POST['strict_handling'] = 0;
                }

                if (!isset($_POST['invoice_handling_auto'])) {
                    $_POST['invoice_handling_auto'] = 0;
                }
                if (!isset($_POST['invoice_job_auto'])) {
                    $_POST['invoice_job_auto'] = 0;
                }

                if (!isset($_POST['lock_opname'])) {
                    $_POST['lock_opname'] = 0;
                }

                if (!isset($_POST['lock_document_transaction'])) {
                    $_POST['lock_document_transaction'] = 0;
                }

                if (!isset($_POST['lock_warehouse_transaction'])) {
                    $_POST['lock_warehouse_transaction'] = 0;
                }

                $this->db->trans_start();

                //core position
                $positions = $this->input->post('positions');
                if (isset($_POST['positions'])) {
                    unset($_POST['positions']);
                }
                $this->corePosition->deleteByDepartment(1);//1 is ops department
                foreach ($positions as $position) {
                    $this->corePosition->create([
                        'id_department' => 1,
                        'id_position' => $position,
                    ]);
                }

                //document production position
                $documentPositions = $this->input->post('documentPositions');
                if (isset($_POST['documentPositions'])) {
                    unset($_POST['documentPositions']);
                }
                $this->documentPosition->deleteByDepartment();
                foreach ($documentPositions as $documentPosition) {
                    $this->documentPosition->create([
                        'id_department' => 6,
                        'id_position' => $documentPosition,
                    ]);
                }
                $this->setting->updateSettings($this->input->post());

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Settings successfully updated", 'setting');
                } else {
                    flash('danger', "Update settings failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->index();
    }

    /**
     * Check given password is match with logged user.
     * @param $password
     * @return bool
     */
    public function match_password($password)
    {
        $this->load->model('UserModel', 'user');
        $user = $this->user->getById(UserModel::authenticatedUserData('id'));
        if (password_verify($password, $user['password'])) {
            return true;
        }
        $this->form_validation->set_message('match_password', 'The %s mismatch with your password');
        return false;
    }

    /**
     * Ajax get all employee position
     */
    public function ajax_get_employee_position()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $search = $this->input->get('q');
            $page = $this->input->get('page');

            $positions = $this->employeePosition->getPosition($search, $page);

            echo json_encode($positions);
        }
    }

}