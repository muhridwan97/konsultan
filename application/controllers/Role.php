<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Role
 * @property RoleModel $role
 * @property RolePermissionModel $rolePermission
 * @property PermissionModel $permission
 * @property UserModel $user
 * @property Exporter $exporter
 */
class Role extends MY_Controller
{
    /**
     * Role constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('RoleModel', 'role');
        $this->load->model('RolePermissionModel', 'rolePermission');
        $this->load->model('PermissionModel', 'permission');
        $this->load->model('UserModel', 'user');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'permission' => 'GET',
            'user' => 'GET'
        ]);
    }

    /**
     * Show roles data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ROLE_VIEW);

        $roles = $this->role->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Roles", $roles);
        } else {
            $this->render('role/index', compact('roles'));
        }
    }

    /**
     * Show detail role.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ROLE_VIEW);

        $role = $this->role->getById($id);
        $userPermissions = $this->permission->getByRole($id);

        $this->render('role/view', compact('role', 'userPermissions'));
    }

    /**
     * Show permissions of specific role.
     *
     * @param $id
     */
    public function permission($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PERMISSION_VIEW);

        $role = $this->role->getById($id);
        $permissions = $this->permission->getByRole($id);

        $this->render('role/permission', compact('role', 'permissions'), 'Role Permission');
    }

    /**
     * Show all users by a role.
     *
     * @param $id
     */
    public function user($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_USER_VIEW);

        $role = $this->role->getById($id);
        $users = $this->user->getByRole($id);

        $this->render('role/user', compact('role', 'users'), 'User Role');
    }

    /**
     * Show create role form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ROLE_CREATE);

        $permissions = $this->permission->getAll();

        $this->render('role/create', compact('permissions'));
    }

    /**
     * Rule validation.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'role' => 'trim|required|max_length[50]',
            'description' => 'trim|required|max_length[500]',
            'permissions[]' => [
                'trim', 'required', ['array_count', function ($fields) {
                    $this->form_validation->set_message('array_count', 'The %s field must be checked at least one');
                    return !empty($fields);
                }]
            ]
        ];
    }

    /**
     * Save new role data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ROLE_CREATE);

        if ($this->validate()) {
            $role = $this->input->post('role');
            $description = $this->input->post('description');
            $permissions = $this->input->post('permissions');

            $this->db->trans_start();

            $this->role->create([
                'role' => $role,
                'description' => $description,
                'created_by' => UserModel::authenticatedUserData('id')
            ]);
            $roleId = $this->db->insert_id();

            foreach ($permissions as $permissionId) {
                $this->rolePermission->create([
                    'id_role' => $roleId,
                    'id_permission' => $permissionId
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Role {$role} successfully created", 'role');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->create();
    }

    /**
     * Show edit role form.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ROLE_EDIT);

        $role = $this->role->getById($id);
        $rolePermissions = $this->permission->getByRole($id);
        $permissions = $this->permission->getAll();

        $this->render('role/edit', compact('role', 'rolePermissions', 'permissions'));
    }

    /**
     * Update data role by id.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ROLE_EDIT);

        if ($this->validate()) {
            $role = $this->input->post('role');
            $description = $this->input->post('description');
            $permissions = $this->input->post('permissions');

            $this->db->trans_start();

            $this->role->update([
                'role' => $role,
                'description' => $description,
            ], $id);

            $this->rolePermission->delete(['id_role' => $id]);
            foreach ($permissions as $permissionId) {
                $this->rolePermission->create([
                    'id_role' => $id,
                    'id_permission' => $permissionId
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Role {$role} successfully updated", 'role');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }

        $this->edit($id);
    }

    /**
     * Perform deleting role data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ROLE_DELETE);

        $role = $this->role->getById($id);

        if ($this->role->delete($id, true)) {
            flash('warning', "Role {$role['role']} successfully deleted");
        } else {
            flash('danger', 'Something is getting wrong, try again or contact administrator');
        }
        redirect('role');
    }
}