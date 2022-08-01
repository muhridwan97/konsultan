<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Token
 * @property UserTokenModel $userToken
 * @property UserModel $user
 * @property LogModel $logHistory
 */
class Token extends MY_Controller
{
    /**
     * Token constructor.
     */
    public function __construct()
    {
        parent::__construct();

        AuthorizationModel::mustLoggedIn();

        $this->load->model('UserTokenModel', 'userToken');
        $this->load->model('UserModel', 'user');
        $this->load->model('LogModel', 'logHistory');

        $this->setFilterMethods([
            'ajax_check_token' => 'GET'
        ]);
    }

    /**
     * Show token list data.
     */
    public function index()
    {
        $email = UserModel::authenticatedUserData('email');
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();
        
        $data = [
            'title' => "Token",
            'subtitle' => "Generate user token",
            'page' => "token/index",
            'tokens' => $this->userToken->getUserTokenByEmail($email)
        ];

        $this->load->view('template/layout', $data);
    }

    /**
     * Save data token.
     */
    public function save()
    {
        $this->form_validation->set_rules('type', 'Unit', 'trim|required|max_length[50]');

        if ($this->form_validation->run() == FALSE) {
            flash('warning', 'Form inputs are invalid');
        } else {
            $type = $this->input->post('type');
            $maxActivation = $this->input->post('max_activation');
            $expireDate = sql_date_format($this->input->post('expired_at'));

            $save = $this->userToken->createToken(UserModel::authenticatedUserData('email'), $type, 8, $maxActivation, $expireDate);

            if ($save) {
                flash('success', "Token <strong>{$type}</strong> successfully created");
            } else {
                flash('danger', "Save token <strong>{$type}</strong> failed, try again or contact administrator");
            }
        }
        redirect("token");
    }

    /**
     * Perform deleting data token.
     * @param $id
     */
    public function delete($id)
    {
        $userToken = $this->userToken->getById($id);

        if ($this->userToken->delete($id, false)) {
            flash('warning', "Token <strong>{$userToken['type']}</strong> successfully deleted");
        } else {
            flash('danger', "Delete token <strong>{$userToken['type']}</strong> failed, try again or contact administrator");
        }
        redirect('token');
    }


    /**
     * Check token by id
     */
    public function ajax_check_token()
    {
        $permission = $this->input->get('permission');
        $token = $this->input->get('token');
        $userToken = $this->userToken->getUserTokenByTokenKey($token, true, true);
        if (empty($userToken)) {
            $result = [
                'status' => 'invalid',
                'message' => 'Invalid or expired token value',
                'permission' => $permission,
                'is_authorized' => false,
            ];
        } else {
            $user = $this->user->getUserByEmail($userToken['email']);
            $isAuthorized = AuthorizationModel::isAuthorized($permission, $user['id']);

            $tokenActivation = $this->userToken->activateToken($token);

            if ($tokenActivation) {
                $result = [
                    'status' => 'success',
                    'permission' => $permission,
                    'is_authorized' => $isAuthorized,
                    'token_owner' => $user,
                ];
            } else {
                $result = [
                    'status' => 'failed',
                    'message' => 'Your token reach maximum number of activation',
                    'permission' => $permission,
                    'is_authorized' => false,
                ];
            }
        }

        $this->render_json($result);
    }
}