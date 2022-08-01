<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Help extends CI_Controller
{
    /**
     * Token constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('LogModel', 'logHistory');

    }
    /**
     * View static help.
     */
    public function index()
    {
        $page = get_url_param('page', 'basic');
        $section = get_url_param('section', 'index');
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();

        $data = [
            'title' => "Help and Contact",
            'subtitle' => "Information",
            'page' => 'help/index',
            'help' => 'help/' . $page . '/' . $section,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Static page user agreement.
     */
    public function terms()
    {
        $this->load->view('help/agreement');
    }
}