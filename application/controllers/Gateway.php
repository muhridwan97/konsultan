<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Gateway
 * @property BranchModel $branch
 */
class Gateway extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BranchModel', 'branch');
    }

    /**
     * Select branch application.
     */
    public function index()
    {
        $userId = UserModel::authenticatedUserData('id');
        if (is_null($userId)) {
            $customerBranches = [];
            flash('danger', 'Your account was not set properly, please contact our administrator fix this issue.');
        } else {
            $customerBranches = $this->branch->getByUser($userId);
        }
        $data = [
            'title' => "Application",
            'subtitle' => "Branch selection",
            'branches' => $customerBranches,
        ];
        $this->load->view('dashboard/application', $data);
    }
}