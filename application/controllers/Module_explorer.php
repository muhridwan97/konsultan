<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Module_explorer extends CI_Controller
{
    /**
     * Module_explorer constructor.
     */
    public function __construct()
    {
        parent::__construct();

        AuthorizationModel::mustLoggedIn();

        $this->load->model('ModuleModel', 'module');
        $this->load->model('LogModel', 'logHistory');
    }

    /**
     * Show modules data list.
     */
    public function index()
    {
        $modules = $this->module->getAllModules();
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();
        
        $data = [
            'title' => "Module",
            'subtitle' => "Data module",
            'page' => "module_explorer/index",
            'modules' => $modules
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show table schema of modules.
     * @param $moduleId
     */
    public function schema($moduleId)
    {
        $module = $this->module->getModuleById($moduleId);
        $schemas = $this->module->getTablesByModule($moduleId);
        $data = [
            'title' => "Module Schema",
            'subtitle' => "Data schema",
            'page' => "module_explorer/schema",
            'module' => $module,
            'schemas' => $schemas
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Get table data of module.
     * @param $moduleId
     * @param $tableName
     */
    public function table($moduleId, $tableName)
    {
        $module = $this->module->getModuleById($moduleId);
        $tableData = $this->module->getTableContentByModule($moduleId, $tableName);
        $tableFields = $this->module->getTableFieldsByModule($moduleId, $tableName);
        $data = [
            'title' => "Module Data",
            'subtitle' => "Table data",
            'page' => "module_explorer/table",
            'module' => $module,
            'tableFields' => $tableFields,
            'tableData' => $tableData,
            'tableName' => $tableName
        ];
        $this->load->view('template/layout', $data);
    }

}