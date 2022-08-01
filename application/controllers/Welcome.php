<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Welcome
 * @property UserModel $user
 * @property UserTokenModel $userToken
 */
class Welcome extends CI_Controller
{
    /**
     * Welcome constructor.
     */
    public function __construct()
    {
        parent::__construct();
        if (UserModel::isLoggedIn()) {
            if ($this->config->item('enable_branch_mode')) {
                redirect("gateway", false);
            } else {
                redirect("dashboard", false);
            }
        }
        $this->load->model('UserModel', 'user');
        $this->load->model('UserTokenModel', 'userToken');
    }

    /**
     * Show default login page.
     */
    public function index()
    {
        $this->load->view('account/login');
    }

    /**
     * Show default register page.
     */
    public function register()
    {
        $this->load->view('account/register');
    }

    /**
     * Show forgot password form.
     */
    public function forgot()
    {
        $this->load->view('account/forgot');
    }

    /**
     * Show reset password form.
     * @param string $token
     */
    public function reset($token)
    {
        $email = $this->userToken->verifyToken($token, UserTokenModel::$TOKEN_PASSWORD);
        if (!$email) {
            flash('danger', 'Invalid or expired reset token key', 'welcome');
        }
        $data = [
            'token' => $token,
            'email' => $email
        ];
        $this->load->view('account/reset', $data);
    }
}
