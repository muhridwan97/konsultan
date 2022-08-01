<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserModel extends MY_Model
{
    protected $table = 'prv_users';
    protected $tableUserApplication = 'prv_user_applications';
    protected $tableApplication = 'prv_applications';

    public static $tableUser = 'prv_users';

    public static $STATUS_PENDING = 'PENDING';
    public static $STATUS_ACTIVATED = 'ACTIVATED';
    public static $STATUS_SUSPENDED = 'SUSPENDED';

    /**
     * UserModel constructor.
     */
    public function __construct()
    {
        if ($this->config->item('sso_enable')) {
            $ssoDB = env('DB_SSO_DATABASE');
            $this->table = $ssoDB . '.prv_users';
            $this->tableUserApplication = $ssoDB . '.prv_user_applications';
            $this->tableApplication = $ssoDB . '.prv_applications';
            self::$tableUser = $this->table;
        }
    }

    /**
     * Get active record query builder for all related user data selection.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        $baseQuery = $this->db
            ->select([
                'prv_users.*',
                'IFNULL(total_role, 0) AS total_role',
                'ref_people.id AS id_person',
                'ref_people.type',
                'ref_people.name AS person_name',
                'ref_people.address',
                'ref_people.gender',
                'ref_people.birthday',
                'ref_people.photo',
                'ref_people.email AS person_email',
                'ref_people.contact',
                'ref_people.website',
            ])
            ->from($this->table)
            ->join('(
                    SELECT id_user, COUNT(id) AS total_role 
                    FROM prv_user_roles 
                    GROUP BY id_user
                ) AS user_roles', 'user_roles.id_user = prv_users.id', 'left')
            ->join('ref_people_users', 'ref_people_users.id_user = prv_users.id', 'left')
            ->join('ref_people', 'ref_people.id = ref_people_users.id_people', 'left');

        if ($this->config->item('sso_enable')) {
            $baseQuery
                ->join($this->tableUserApplication, 'prv_user_applications.id_user = prv_users.id')
                ->join($this->tableApplication, 'prv_applications.id = prv_user_applications.id_application')
                ->where("TRIM(TRAILING '/' FROM prv_applications.url)=", rtrim(site_url('/', false), '/'));
        }

        return $baseQuery;
    }

    /**
     * Get all user with or without deleted records.
     *
     * @param array $filters
     * @param bool $withTrashed
     * @return mixed
     */
    public function getAll($filters = [], $withTrashed = false)
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $this->db->start_cache();

        $baseQuery = $this->getBaseQuery($branchId);

        if (!$withTrashed) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if (!empty($search)) {
            $baseQuery
                ->group_start()
                ->like('prv_users.name', $search)
                ->or_like('prv_users.username', $search)
                ->or_like('prv_users.email', $search)
                ->or_like('prv_users.status', $search)
                ->or_like('ref_people.name', $search)
                ->or_like('ref_people.type', $search)
                ->group_end();
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        if ($column == 'no') $column = $this->table . '.id';
        $page = $baseQuery->order_by($column, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        foreach ($data as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($data),
            "recordsFiltered" => $total,
            "data" => $data
        ];
        $this->db->flush_cache();

        return $pageData;
    }

    /**
     * Check user authentication and remembering login.
     *
     * @param string $username
     * @param string $password
     * @return bool
     */
    public function authenticate($username, $password)
    {
        $usernameField = 'username';
        $isEmail = filter_var($username, FILTER_VALIDATE_EMAIL);
        if ($isEmail) {
            $usernameField = 'email';
        }

        $user = $this->db->get_where($this->table, [
            $usernameField => $username
        ]);

        if ($user->num_rows() > 0) {
            $result = $user->row_array();
            if ($result['status'] != UserModel::$STATUS_ACTIVATED) {
                return $result['status'];
            }
            $hashedPassword = $result['password'];
            if (password_verify($password, $hashedPassword)) {
                if (password_needs_rehash($hashedPassword, PASSWORD_BCRYPT)) {
                    $newHash = password_hash($password, PASSWORD_BCRYPT);
                    $this->db->update($this->table, ['password' => $newHash], ['id' => $result['id']]);
                }
                $this->session->set_userdata([
                    'auth.id' => $result['id'],
                    'auth.is_logged_in' => true
                ]);

                return true;
            }
        }
        return false;
    }

    /**
     * Check if user has logged in from everywhere.
     *
     * @return bool
     */
    public static function isLoggedIn()
    {
        $CI = get_instance();
        $sessionUserId = $CI->session->userdata('auth.id');
//        session_write_close();

        if (is_null($sessionUserId) || $sessionUserId == '') {
            return false;
        }

        if ($CI->db->field_exists('device_id', self::$tableUser)) {
            $currentId = json_encode([
                'session_id' => $CI->agent->agent_string(),
                'browser' => $CI->agent->browser(),
                'version' => $CI->agent->version(),
                'platform' => $CI->agent->platform(),
                'is_mobile' => $CI->agent->is_mobile()
            ]);
            $userData = $CI->db->get_where(self::$tableUser, ['id' => $sessionUserId])->row_array();

            if ($currentId != $userData['device_id'] && UserModel::authenticatedUserData('username') != 'admin') {
                redirect(sso_url('auth/logout?force_logout=1'), false);
                return false;
            }
        }
        return true;
    }

    /**
     * Destroy user's session
     */
    public function logout()
    {
        if ($this->session->has_userdata('auth.id')) {
            $this->session->unset_userdata([
                'auth.id', 'auth.is_logged_in', 'auth.remember_me', 'auth.remember_token'
            ]);
//            $this->session->sess_destroy();
            return true;
        }
        return false;
    }

    /**
     * Get authenticate user data.
     *
     * @param string $key
     * @param string $default
     * @return mixed
     */
    public static function authenticatedUserData($key = '', $default = '')
    {
        $CI = get_instance();
        $id = 0;
        if ($CI->session->has_userdata('auth.id')) {
            $id = $CI->session->userdata('auth.id');
        }
//        session_write_close();

        $userData = cache_remember('authenticated-user-data-' . $id, 60, function () use ($CI, $id) {
            $result = $CI->db->select([
                'prv_users.*',
                'ref_people.id AS id_person',
                'ref_people.type AS person_type',
//                'ref_employees.id AS id_employee',
//                'ref_employees.id_department',
//                'ref_employees.id_employee AS id_supervisor',
//                'ref_employees.position_level',
            ])
                ->from(UserModel::$tableUser)
                ->join('ref_people_users', 'ref_people_users.id_user = prv_users.id', 'left')
                ->join('ref_people', 'ref_people.id = ref_people_users.id_people', 'left')
//                ->join(EmployeeModel::$tableEmployee, 'ref_employees.id_user = prv_users.id', 'left')
                ->where('prv_users.id', $id)
                ->get();

            return $result->row_array();
        });

        if ($userData == null || count($userData) <= 0) {
            return $default;
        }

        if (!is_null($key) && $key != '') {
            if (key_exists($key, $userData)) {
                return $userData[$key];
            }
            return $default;
        }
        return $userData;
    }

    /**
     * Get users by specific role id.
     *
     * @param integer $roleId
     * @param bool $withTrashed
     * @return mixed
     */
    public function getByRole($roleId, $withTrashed = false)
    {
        $users = $this->getBaseQuery()
            ->select("GROUP_CONCAT(DISTINCT source_branch.branch SEPARATOR ', ') AS source_branch")
            ->join('prv_user_roles', 'prv_user_roles.id_user = prv_users.id')
            ->join("(SELECT ref_branches.branch,prv_user_roles.id_user AS id_user_branch from prv_user_roles 
                    LEFT JOIN ref_branches ON prv_user_roles.id_branch = ref_branches.id
                    ) AS source_branch","source_branch.id_user_branch = prv_users.id","left")
            ->where('prv_user_roles.id_role', $roleId)
            ->group_by("prv_user_roles.id_user");

        if (!$withTrashed) {
            $users->where('prv_users.is_deleted', false);
        }

        return $users->get()->result_array();
    }

    /**
     * Get users by specific role id.
     *
     * @param $permission
     * @param null $branchId
     * @param bool $withTrashed
     * @return mixed
     */
    public function getByPermission($permission, $branchId = null, $withTrashed = false)
    {
        $baseQuery = $this->db->select([
            'prv_users.*',
            'ref_employees.id_department',
            'ref_employees.position_level',
        ])
            ->from(self::$tableUser)
            ->distinct()
            ->join('prv_user_roles', 'prv_user_roles.id_user = prv_users.id')
            ->join('prv_role_permissions', 'prv_role_permissions.id_role = prv_user_roles.id_role')
            ->join('prv_permissions', 'prv_permissions.id = prv_role_permissions.id_permission')
            ->join(EmployeeModel::$tableEmployee, 'ref_employees.id_user = prv_users.id')
            ->where('prv_users.status', 'ACTIVATED')
            ->where('prv_permissions.permission', $permission);

        if (empty($branchId)) {
            $branchId = get_active_branch_id();
        }

        if (!empty($branchId)) {
            $baseQuery->where('prv_user_roles.id_branch', $branchId);
        }

        if ($this->config->item('sso_enable')) {
            $baseQuery
                ->join($this->tableUserApplication, 'prv_user_applications.id_user = prv_users.id')
                ->join($this->tableApplication, 'prv_applications.id = prv_user_applications.id_application')
                ->where("TRIM(TRAILING '/' FROM prv_applications.url)=", rtrim(site_url('/', false), '/'));
        }

        if (!$withTrashed) {
            $baseQuery->where('prv_users.is_deleted', false);
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get single user data by its email address.
     *
     * @param string $email
     * @param bool $withTrashed
     * @return array
     */
    public function getUserByEmail($email, $withTrashed = false)
    {
        $users = $this->getBaseQuery()->where('prv_users.email', $email);

        if (!$withTrashed) {
            $users->where('prv_users.is_deleted', false);
        }

        return $users->get()->row_array();
    }

    /**
     * Check if given email is unique.
     *
     * @param $email
     * @param int $exceptId
     * @return bool
     */
    public function isUniqueEmail($email, $exceptId = 0)
    {
        $user = $this->db->get_where($this->table, [
            'email' => $email,
            'id != ' => $exceptId
        ]);

        if ($user->num_rows() > 0) {
            return false;
        }
        return true;
    }

    /**
     * Check if given username is unique.
     *
     * @param $username
     * @param int $exceptId
     * @return bool
     */
    public function isUniqueUsername($username, $exceptId = 0)
    {
        $user = $this->db->get_where($this->table, [
            'username' => $username,
            'id != ' => $exceptId
        ]);

        if ($user->num_rows() > 0) {
            return false;
        }
        return true;
    }

    /**
     * Attach application.
     *
     * @param $userId
     * @return bool
     */
    public function addAccessToApplication($userId)
    {
        $application = $this->db->from($this->tableApplication)
            ->like("TRIM(TRAILING '/' FROM prv_applications.url)", rtrim(site_url('/', false), '/'))
            ->limit(1)
            ->get()
            ->row_array();

        if (!empty($application)) {
            return $this->db->insert($this->tableUserApplication, [
                'id_user' => $userId,
                'id_application' => $application['id']
            ]);
        }
        return true;
    }

    /**
     * Get unattached users.
     *
     * @param null $exceptId
     * @return array
     */
    public function getUnattachedProfile($exceptId = null)
    {
        $users = $this->getBaseQuery()
            ->group_start()
            ->where('ref_people.id', null)
            ->or_where('ref_people.id', '')
            ->group_end();

        if(!empty($exceptId)) {
            if(is_array($exceptId)){
                $users->distinct()->or_where_in('prv_users.id', $exceptId);
            }else{
                $users->distinct()->or_where('prv_users.id', $exceptId);
            }
        }

        return $users->get()->result_array();
    }
}
