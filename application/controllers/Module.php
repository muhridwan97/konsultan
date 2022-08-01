<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Module
 * @property ModuleModel $module
 */
class Module extends CI_Controller
{
    /**
     * Module constructor.
     */
    public function __construct()
    {
        parent::__construct();

        AuthorizationModel::mustLoggedIn();

        $this->load->model('ModuleModel', 'module');
        $this->load->model('LogModel', 'logHistory');
    }

    /**
     * Show import data list
     */
    public function ajax_get_module_by_type()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $type = $this->input->get('type');
            $data = $this->module->getModulesByType($type);;
            header('Content-Type: application/json');
            echo json_encode($data);
        }
    }

    /**
     * Get partial view with table list name.
     */
    public function ajax_get_module_table()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $moduleId = $this->input->get('id_module');
            $tables = $this->module->getTablesByModule($moduleId);
            echo $this->load->view('module/_module_table', [
                'moduleId' => $moduleId,
                'tables' => $tables
            ], true);
        }
    }

    /**
     * Get partial view with table fields.
     */
    public function ajax_get_module_table_field()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $moduleId = $this->input->get('id_module');
            $tableName = $this->input->get('table_name');
            $fields = $this->module->getTableFieldsByModule($moduleId, $tableName);
            echo $this->load->view('module/_module_table_field', [
                'moduleId' => $moduleId,
                'tableName' => $tableName,
                'fields' => $fields
            ], true);
        }
    }

    /**
     * Show modules data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);

        $modules = $this->module->getAllModules();
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();

        $data = [
            'title' => "Module",
            'subtitle' => "Data module",
            'page' => "module/index",
            'modules' => $modules
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show create module setting.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);

        $data = [
            'title' => "Module",
            'subtitle' => "Create Module",
            'page' => "module/create"
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Save new module.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('module_name', 'Module name', 'trim|required|max_length[100]|is_unique[ref_modules.module_name]');
            $this->form_validation->set_rules('module_description', 'Module description', 'trim|required|max_length[500]');
            $this->form_validation->set_rules('type', 'Module type', 'trim|required');
            $this->form_validation->set_rules('hostname', 'Server or hostname', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('database', 'Database', 'trim|required|max_length[50]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $moduleName = $this->input->post('module_name');
                $moduleDescription = $this->input->post('module_description');
                $type = $this->input->post('type');
                $hostname = $this->input->post('hostname');
                $username = $this->input->post('username');
                $password = $this->input->post('password');
                $database = $this->input->post('database');
                $port = $this->input->post('port');
                $tableHeader = $this->input->post('table_header');
                $tableHeaderId = $this->input->post('table_header_id');
                $tableHeaderTitle = $this->input->post('table_header_title');
                $tableHeaderSubtitle = $this->input->post('table_header_subtitle');
                $tableContainer = $this->input->post('table_container');
                $tableContainerRef = $this->input->post('table_container_ref');
                $tableGoods = $this->input->post('table_goods');
                $tableGoodsRef = $this->input->post('table_goods_ref');

                $save = $this->module->createModule([
                    'module_name' => $moduleName,
                    'module_description' => $moduleDescription,
                    'type' => $type,
                    'hostname' => $hostname,
                    'username' => $username,
                    'password' => $password,
                    'database' => $database,
                    'port' => $port,
                    'table_header' => $tableHeader,
                    'table_header_id' => $tableHeaderId,
                    'table_header_title' => $tableHeaderTitle,
                    'table_header_subtitle' => $tableHeaderSubtitle,
                    'table_container' => $tableContainer,
                    'table_container_ref' => $tableContainerRef,
                    'table_goods' => $tableGoods,
                    'table_goods_ref' => $tableGoodsRef
                ]);

                if ($save) {
                    flash('success', "Module <strong>{$moduleName}</strong> successfully created", 'module');
                } else {
                    flash('danger', "Save module <strong>{$moduleName}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->create();
    }


    /**
     * Show detail module.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);

        $module = $this->module->getModuleById($id);
        $data = [
            'title' => "Module",
            'subtitle' => "View module",
            'page' => "module/view",
            'module' => $module
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show edit module form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);

        $module = $this->module->getModuleById($id);
        $data = [
            'title' => "Module",
            'subtitle' => "Edit Module",
            'page' => "module/edit",
            'module' => $module
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Update module data.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Module data', 'trim|required');
            $this->form_validation->set_rules('module_name', 'Module name', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('module_description', 'Module description', 'trim|required|max_length[500]');
            $this->form_validation->set_rules('type', 'Module type', 'trim|required');
            $this->form_validation->set_rules('hostname', 'Server or hostname', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('password', 'Password', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('database', 'Database', 'trim|required|max_length[50]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $id = $this->input->post('id');
                $moduleName = $this->input->post('module_name');
                $moduleDescription = $this->input->post('module_description');
                $type = $this->input->post('type');
                $hostname = $this->input->post('hostname');
                $username = $this->input->post('username');
                $password = $this->input->post('password');
                $database = $this->input->post('database');
                $port = $this->input->post('port');
                $tableHeader = $this->input->post('table_header');
                $tableHeaderId = $this->input->post('table_header_id');
                $tableHeaderTitle = $this->input->post('table_header_title');
                $tableHeaderSubtitle = $this->input->post('table_header_subtitle');
                $tableContainer = $this->input->post('table_container');
                $tableContainerRef = $this->input->post('table_container_ref');
                $tableGoods = $this->input->post('table_goods');
                $tableGoodsRef = $this->input->post('table_goods_ref');

                $update = $this->module->updateModule([
                    'module_name' => $moduleName,
                    'module_description' => $moduleDescription,
                    'type' => $type,
                    'hostname' => $hostname,
                    'username' => $username,
                    'password' => $password,
                    'database' => $database,
                    'port' => $port,
                    'table_header' => $tableHeader,
                    'table_header_id' => $tableHeaderId,
                    'table_header_title' => $tableHeaderTitle,
                    'table_header_subtitle' => $tableHeaderSubtitle,
                    'table_container' => $tableContainer,
                    'table_container_ref' => $tableContainerRef,
                    'table_goods' => $tableGoods,
                    'table_goods_ref' => $tableGoodsRef
                ], $id);

                if ($update) {
                    flash('success', "Module <strong>{$moduleName}</strong> successfully updated", 'module');
                } else {
                    flash('danger', "Update module <strong>{$moduleName}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->edit($id);
    }

    /**
     * Perform deleting item data.
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);

        if ($this->input->server('REQUEST_METHOD') == "POST") {

            $moduleData = $this->module->getModuleById($id);

            if ($this->module->deletemodule($id)) {
                flash('warning', "Module <strong>{$moduleData['module_name']}</strong> successfully deleted");
            } else {
                flash('danger', "Delete module <strong>{$moduleData['module_name']}</strong> failed, try again or contact administrator");
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('module');
    }
}