<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class File_manager extends CI_Controller
{
    /**
     * File manager constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->helper('directory');
        $this->load->model('LogModel', 'logHistory');
    }

    /**
     * Show file upload data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_VALIDATE);
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();

        $path = $this->input->get('path');
        if (empty($path)) {
            $path = './uploads/';
        }
        $directories = explode('/', preg_replace('{/$}', '', $path));
        array_pop($directories);
        $map = directory_map($path, 1);

        usort($map, function ($a, $b) use (&$i) {
            $order = ['folder' => 0, 'file' => 1];

            $aStats = preg_match('{/$}', $a) ? 'folder' : 'file';
            $bStats = preg_match('{/$}', $b) ? 'folder' : 'file';

            if($aStats == $bStats) {
                return strcmp($a, $b);
            }
            return $order[$aStats] > $order[$bStats];
        });

        $data = [
            'title' => "File Manager",
            'subtitle' => "Data upload",
            'page' => "upload/manager",
            'path' => $path,
            'directories' => $directories,
            'parent' => implode('/', $directories) . '/',
            'files' => $map
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Download current directory
     */
    public function download()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UPLOAD_VALIDATE);

        $path = $this->input->get('path');
        if (empty($path)) {
            $path = './uploads/';
        }

        $this->load->library('zip');
        $this->zip->compression_level = 5;
        $this->zip->read_dir($path, false);

        $this->zip->download('uploads_archive_' . date('Ymd') . '.zip');
    }

}