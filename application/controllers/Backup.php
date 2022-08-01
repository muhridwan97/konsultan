<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Backup
 * @property ModuleModel $module
 * @property LogModel $logHistory
 */
class Backup extends MY_Controller
{
    /**
     * Backup constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ModuleModel', 'module');
        $this->load->model('LogModel', 'logHistory');
        $this->load->library('zip');
        $this->zip->compression_level = 5;

        $this->setFilterMethods([
            'app' => 'GET',
            'database' => 'GET'
        ]);
    }

    /**
     * Show module and main database form.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);

        $modules = $this->module->getAllModules();
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();

        $data = [
            'title' => "System Backup",
            'subtitle' => "App and database backup",
            'page' => "backup/index",
            'modules' => $modules
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Zipping application folder and data.
     * @param $type
     */
    public function app($type)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);

        $path = './';
        if ($type == 'full') {
            $this->zip->read_dir($path, false);
            $this->zip->download('backup_app_data_' . date('Ymd') . '.zip');
        } else {
            $this->load->helper('directory');
            $directories = directory_map($path, 1);
            $includes = ['application/', 'system/', 'index.php'];
            foreach ($directories as $directory) {
                if (in_array($directory, $includes)) {
                    if (preg_match('{/$}', $directory)) {
                        $this->zip->read_dir($directory, false);
                    } else {
                        $this->zip->read_file($directory, false);
                    }
                }
            }
            $this->zip->download('backup_app_' . date('Ymd') . '.zip');
        }
    }

    /**
     * Dump sql module database.
     * @param $module
     * @param $moduleId
     */
    public function database($module, $moduleId = null)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SETTING_EDIT);

        $this->load->helper('file');
        $this->load->helper('download');
        if ($module == 'main' && is_null($moduleId)) {
            $this->load->dbutil();
            $backup = $this->dbutil->backup([
                'format' => 'zip',
                'filename' => $this->db->database . '.sql'
            ]);
            force_download('db_backup_main_' . date('Ymd') . '.zip', $backup);
        } else {
            $selectedModule = $this->module->getModuleById($moduleId);
            $connection = $this->module->setConnectionByModule($selectedModule);
            $this->load->dbutil($connection);
            $backup = $this->dbutil->backup([
                'format' => 'zip',
                'filename' => $selectedModule['database'] . '.sql'
            ]);
            force_download('db_backup_module_' . $selectedModule['database'] . '_' . date('Ymd') . '.zip', $backup);
        }
    }

}