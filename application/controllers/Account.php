<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Account
 * @property UserModel $user
 * @property UserTokenModel $userToken
 * @property PeopleModel $people
 * @property Mailer $mailer
 */
class Account extends CI_Controller
{
    /**
     * Account constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel', 'user');
        $this->load->model('UserTokenModel', 'userToken');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('modules/Mailer', 'mailer');
    }

    /**
     * Show account preferences.
     */
    public function index()
    {
        AuthorizationModel::mustLoggedIn();

        $userId = UserModel::authenticatedUserData('id');
        $user = $this->user->getById($userId);
        $person = $this->people->getBy(['ref_people_user.id_user' => $user['id']], true);
        $data = [
            'title' => "Account",
            'subtitle' => "View user account",
            'page' => "account/index",
            'user' => $user,
            'person' => $person
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Check user authentication using username or email.
     */
    public function auth()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('username', 'Username or Email', 'trim|required');
            $this->form_validation->set_rules('password', 'Password', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $username = $this->input->post('username');
                $password = $this->input->post('password');
                $remember = $this->input->post('remember');

                $authenticated = $this->user->authenticate($username, $password);

                if ($authenticated === UserModel::$STATUS_PENDING || $authenticated === UserModel::$STATUS_SUSPENDED) {
                    flash('danger', 'Your account still <strong>' . $authenticated . '</strong>, contact our administrator');
                } else {
                    if ($authenticated) {
                        if ($remember) {
                            $loggedEmail = UserModel::authenticatedUserData('email');
                            $token = $this->userToken->createToken($loggedEmail, UserTokenModel::$TOKEN_REMEMBER);

                            if ($loggedEmail == false) {
                                flash('danger', 'Failed create <strong>remember token</strong>.');
                            } else {
                                set_cookie('remember_token', $token, 3600 * 24 * 30);
                                $this->session->set_userdata('remember_me', true);
                                $this->session->set_userdata('remember_token', $token);
                            }
                        }

                        if ($this->config->item('enable_branch_mode')) {
                            redirect("gateway");
                        } else {
                            redirect("dashboard");
                        }
                    } else {
                        flash('danger', 'Username and password mismatch');
                    }
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->load->view('account/login');
    }

    /**
     * Signing out users.
     */
    public function logout()
    {
        if ($this->user->logout()) {
            $rememberToken = get_cookie('remember_token');
            if (!empty($rememberToken)) {
                delete_cookie('remember_token');
                $this->userToken->deleteToken($rememberToken);
            }
            redirect('welcome', false);
        }
        redirect('dashboard');
    }

    /**
     * Register new user
     */
    public function register()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[50]|callback_username_exists');
            $this->form_validation->set_rules('email', 'Email address', 'trim|required|valid_email|max_length[50]|callback_email_exists');
            $this->form_validation->set_rules('password', 'Current password', 'trim|required|min_length[5]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'matches[password]');
            $this->form_validation->set_rules('agree', 'User agreement', 'required');
            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $name = $this->input->post('name');
                $username = $this->input->post('username');
                $email = $this->input->post('email');
                $password = $this->input->post('password');

                $save = $this->user->create([
                    'name' => $name,
                    'username' => $username,
                    'email' => $email,
                    'password' => password_hash($password, CRYPT_BLOWFISH),
                ]);
                if ($save) {
                    flash('success', "You are successfully registered, please contact administrator to activate your account");
                    redirect("/");
                } else {
                    flash('danger', "Register user failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->load->view('account/register');
    }

    /**
     * Forgot password, send email token reset password.
     */
    public function forgot()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('email', 'Email address', [
                'trim', 'required', 'valid_email', 'max_length[50]', [
                    'email_exist', function ($value) {
                        return !$this->user->isUniqueEmail($value);
                    }]
            ], [
                'email_exist' => 'Email is not registered in our system'
            ]);

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form input is invalid');
            } else {
                $email = $this->input->post('email');

                $this->load->model('UserTokenModel', 'userToken');
                $token = $this->userToken->createToken($email, UserTokenModel::$TOKEN_PASSWORD);
                $user = $this->user->getUserByEmail($email);
                if (!$token || empty($user)) {
                    flash('danger', 'Failed to create reset password token, try again!');
                } else {
                    $emailTo = $email;
                    $emailTitle = 'Reset Password Token';
                    $emailTemplate = 'emails/reset_password';
                    $emailData = [
                        'user' => $user,
                        'token' => $token,
                    ];
                    $send = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);

                    if ($send) {
                        flash('success', "We have sent <strong>{$email}</strong> email token to reset your password", 'welcome');
                    }

                    flash('warning', 'Send email token to reset password failed');
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->load->view('account/forgot');
    }

    /**
     * Recover password.
     * @param $token
     */
    public function reset($token)
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('email', 'Email address', 'trim|required|valid_email|max_length[50]');
            $this->form_validation->set_rules('new_password', 'New Password', 'trim|required|min_length[5]|max_length[50]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'trim|required|matches[new_password]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $email = $this->input->post('email');
                $newPassword = $this->input->post('new_password');
                $dataAccount = [
                    'password' => password_hash($newPassword, CRYPT_BLOWFISH)
                ];

                $this->load->model('UserTokenModel', 'userToken');
                $emailToken = $this->userToken->verifyToken($token, UserTokenModel::$TOKEN_PASSWORD);
                if ($email != $emailToken) {
                    flash('danger', 'Token is mismatch with email');
                } else {
                    $this->db->trans_start();

                    $this->user->update($dataAccount, ['email' => $email]);
                    $this->userToken->deleteToken($token);

                    $this->db->trans_complete();

                    if ($this->db->trans_status() === FALSE) {
                        flash('danger', 'Transaction failed, try again or contact our administrator');
                    } else {
                        $user = $this->user->getUserByEmail($email);
                        $this->load->library('email');
                        $this->email->from('no-reply@transcon-indonesia.com', 'Transcon Indonesia');
                        $this->email->to($email);
                        $this->email->subject('TCI Handling - Password Recovered');
                        $this->email->message($this->load->view('emails/password_recovered', [
                            'user' => $user
                        ], true));
                        $this->email->send();

                        flash('success', 'Your password is recovered');

                        redirect('welcome');
                    }
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }

        redirect('welcome/reset/' . $token);
    }

    /**
     * Update account settings.
     */
    public function update()
    {
        AuthorizationModel::mustLoggedIn();

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('name', 'Name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('username', 'Username', 'trim|required|max_length[50]|callback_username_exists');
            $this->form_validation->set_rules('email', 'Email address', 'trim|required|valid_email|max_length[50]|callback_email_exists');
            $this->form_validation->set_rules('password', 'Current password', 'trim|required|callback_match_password');
            $this->form_validation->set_rules('new_password', 'New Password', 'max_length[50]');
            $this->form_validation->set_rules('confirm_password', 'Confirm Password', 'matches[new_password]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $name = $this->input->post('name');
                $username = $this->input->post('username');
                $email = $this->input->post('email');
                $newPassword = $this->input->post('new_password');

                $dataAccount = [
                    'name' => $name,
                    'username' => $username,
                    'email' => $email
                ];

                if ($newPassword != '') {
                    $dataAccount['password'] = password_hash($newPassword, CRYPT_BLOWFISH);
                }

                $userId = UserModel::authenticatedUserData('id');
                $update = $this->user->update($dataAccount, $userId);

                if ($update) {
                    flash('success', "Your account was successfully updated", 'account');
                } else {
                    flash('danger', "Update account failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->index();
    }

    /**
     * Check given username is exist or not.
     * @param $username
     * @return bool
     */
    public function username_exists($username)
    {
        if ($this->user->isUniqueUsername($username, UserModel::authenticatedUserData('id', 0))) {
            return true;
        } else {
            $this->form_validation->set_message('username_exists', 'The %s has been registered before, try another');
            return false;
        }
    }

    /**
     * Check given email is exist or not.
     * @param $email
     * @return bool
     */
    public function email_exists($email)
    {
        if ($this->user->isUniqueEmail($email, UserModel::authenticatedUserData('id', 0))) {
            return true;
        } else {
            $this->form_validation->set_message('email_exists', 'The %s has been registered before, try another');
            return false;
        }
    }

    /**
     * Check given password is match with logged user.
     * @param $password
     * @return bool
     */
    public function match_password($password)
    {
        $user = $this->user->getById(UserModel::authenticatedUserData('id'));
        if (password_verify($password, $user['password'])) {
            return true;
        }
        $this->form_validation->set_message('match_password', 'The %s mismatch with your password');
        return false;
    }

}
