<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class User
 * @property BranchModel $branch
 * @property UserModel $user
 * @property RoleModel $roleModel
 * @property UserRoleModel $userRole
 * @property Exporter $exporter
 */
class User extends MY_Controller
{
    /**
     * User constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BranchModel', 'branch');
        $this->load->model('UserModel', 'user');
        $this->load->model('RoleModel', 'roleModel');
        $this->load->model('UserRoleModel', 'userRole');
        $this->load->model('modules/Exporter', 'exporter');
        $this->setFilterMethods([
            'ajax_get_data' => 'GET',
            'role' => 'GET',
        ]);
    }

    /**
     * Show user data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_USER_VIEW);

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Users", $this->user->getAll());
        } else {
            $this->render('user/index');
        }
    }

    /**
     * Get ajax datatable user data.
     */
    public function ajax_get_data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_USER_VIEW);

        $filters = array_merge($_GET, [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);

        $data = $this->user->getAll($filters);

        $this->render_json($data);
    }

    /**
     * Show detail user.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_USER_VIEW);

        $user = $this->user->getById($id);

        $this->render('user/view', compact('user'));
    }

    /**
     * Show all roles of specific user.
     *
     * @param $userId
     */
    public function role($userId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ROLE_VIEW);

        $user = $this->user->getById($userId);
        $roles = $this->roleModel->getByUser($userId);

        $this->render('user/role', compact('user', 'roles'));
    }

    /**
     * Show create user form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_USER_CREATE);

        $branches = $this->branch->getAll();
        $roles = $this->roleModel->getAll();
        $statuses = [
            'PENDING' => UserModel::$STATUS_PENDING,
            'ACTIVATED' => UserModel::$STATUS_ACTIVATED,
            'SUSPENDED' => UserModel::$STATUS_SUSPENDED,
        ];

        $this->render('user/create', compact('branches', 'roles', 'statuses'));
    }

    /**
     * Base form validation rules.
     *
     * @param array $params
     * @return array
     */
    protected function _validation_rules(...$params)
    {
        $id = isset($params[0]) ? $params[0] : 0;

        return [
            'name' => 'trim|required|max_length[50]',
            'username' => 'trim|required|max_length[50]|callback_username_exists[' . $id . ']',
            'email' => 'trim|required|valid_email|max_length[50]|callback_email_exists[' . $id . ']',
            'user_type' => 'trim|required',
            'password' => 'trim|min_length[5]' . ($id > 0 ? '' : '|required'),
            'confirm_password' => 'matches[password]',
            'status' => 'required'
        ];
    }

    /**
     * Save new user.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_USER_CREATE);

        if ($this->validate()) {
            $name = $this->input->post('name');
            $username = $this->input->post('username');
            $email = $this->input->post('email');
            $userType = $this->input->post('user_type');
            $password = $this->input->post('password');
            $status = $this->input->post('status');
            $roleBranches = $this->input->post('roles');

            $this->db->trans_start();

            $this->user->create([
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'user_type' => $userType,
                'password' => password_hash($password, CRYPT_BLOWFISH),
                'status' => $status,
            ]);
            $userId = $this->db->insert_id();

            if ($this->config->item('sso_enable')) {
                $this->user->addAccessToApplication($userId);
            }

            foreach ($roleBranches as $branchId => $roles) {
                foreach ($roles as $roleId) {
                    $this->userRole->createUserRole([
                        'id_user' => $userId,
                        'id_role' => $roleId,
                        'id_branch' => $branchId
                    ]);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', 'You are successfully registered', 'user');
            } else {
                flash('danger', 'Register user failed');
            }
        }
        $this->create();
    }

    /**
     * Show edit user form.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_USER_EDIT);

        $branches = $this->branch->getAll();
        $user = $this->user->getById($id);
        $roles = $this->roleModel->getAll();
        $userRoles = $this->roleModel->getByUser($id);
        $statuses = [
            'PENDING' => UserModel::$STATUS_PENDING,
            'ACTIVATED' => UserModel::$STATUS_ACTIVATED,
            'SUSPENDED' => UserModel::$STATUS_SUSPENDED,
        ];

        $this->render('user/edit', compact('branches', 'user', 'roles', 'userRoles', 'statuses'));
    }

    /**
     * Update account settings.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_USER_EDIT);

        if ($this->validate($this->_validation_rules($id))) {
            $name = $this->input->post('name');
            $username = $this->input->post('username');
            $email = $this->input->post('email');
            $userType = $this->input->post('user_type');
            $newPassword = $this->input->post('password');
            $status = $this->input->post('status');
            $roleBranches = $this->input->post('roles');

            $dataAccount = [
                'name' => $name,
                'username' => $username,
                'email' => $email,
                'user_type' => $userType,
                'status' => $status,
            ];

            if ($newPassword != '') {
                $dataAccount['password'] = password_hash($newPassword, CRYPT_BLOWFISH);
            }

            $this->db->trans_start();

            $this->user->update($dataAccount, $id);

            $this->userRole->deleteUserRoleByUser($id);
            foreach ($roleBranches as $branchId => $roles) {
                foreach ($roles as $roleId) {
                    $this->userRole->createUserRole([
                        'id_user' => $id,
                        'id_role' => $roleId,
                        'id_branch' => $branchId
                    ]);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', 'Your account was successfully updated', 'user');
            } else {
                flash('danger', 'Update account failed');
            }
        }
        $this->edit($id);
    }

    /**
     * Check given username is exist or not.
     *
     * @param $username
     * @param $id
     * @return bool
     */
    public function username_exists($username, $id)
    {
        if ($this->user->isUniqueUsername($username, $id)) {
            return true;
        } else {
            $this->form_validation->set_message('username_exists', 'The %s has been registered before');
            return false;
        }
    }

    /**
     * Check given email is exist or not.
     * @param $email
     * @param $id
     * @return bool
     */
    public function email_exists($email, $id)
    {
        if ($this->user->isUniqueEmail($email, $id)) {
            return true;
        } else {
            $this->form_validation->set_message('email_exists', 'The %s has been registered before');
            return false;
        }
    }

    /**
     * Perform deleting user data.
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_USER_DELETE);

        $user = $this->user->getById($id);

        if ($this->user->delete($id)) {
            flash('warning', "User {$user['name']} successfully deleted");
        } else {
            flash('danger', "Delete user {$user['name']} failed");
        }

        redirect('user');
    }

}