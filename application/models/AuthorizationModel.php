<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AuthorizationModel extends CI_Model
{
    private static $isEnable = true;

    /**
     * AuthorizationModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        self::$isEnable = $this->config->item('enable_authorization');
    }

    /**
     * Check if user is logged in.
     * @param string $defaultRedirect
     */
    public static function mustLoggedIn($defaultRedirect = 'welcome')
    {
        if (!UserModel::isLoggedIn()) {
            $CI = get_instance();
            if ($CI->config->item('sso_enable')) {
                $loginPage = sso_url('auth/login');
            } else {
                $loginPage = $defaultRedirect;
            }
            $redirectTo = '?redirect=' . urlencode(current_url());
            redirect($loginPage . $redirectTo, false);
        }
    }

    /**
     * Quick check user permission.
     * @param $permission
     * @param null $module
     */
    public static function mustAuthorized($permission, $module = null)
    {
        if (self::$isEnable) {
            if (self::isUnauthorized($permission)) {
                self::redirectUnauthorized($module);
            }
        }
    }

    /**
     * Quick check user permission all.
     * @param array $permissions
     * @param bool $requiredAll
     * @param null $module
     */
    public static function checkAuthorizedAll($permissions = [], $requiredAll = true, $module = null)
    {
        $isAuthorized = false;
        foreach ($permissions as $permission) {
            if ($requiredAll) {
                if (self::isUnauthorized($permission)) {
                    $isAuthorized = false;
                    break;
                } else {
                    $isAuthorized = true;
                }
            } else {
                if (self::isAuthorized($permission)) {
                    $isAuthorized = true;
                    break;
                }
            }
        }
        if (!$isAuthorized) {
            self::redirectUnauthorized($module);
        }
    }

    /**
     * Redirecting unauthorized route.
     * @param null $redirectModule
     * @return string
     */
    public static function redirectUnauthorized($redirectModule = null)
    {
        $CI = get_instance();
        $message = 'You are <strong>UNAUTHORIZED</strong> perform this action.';

        flash('danger', $message);

        if ($redirectModule == 'plain-text') {
            return $message;
        }

        if ($CI->agent->is_referral()) {
            redirect($CI->agent->referrer());
        } else if (!is_null($redirectModule)) {
            redirect($redirectModule);
        } else {
            redirect('dashboard');
        }
    }

    /**
     * Check if given or logged user has a permission.
     * @param $permission
     * @param int $idUser
     * @return bool
     */
    public static function isAuthorized($permission, $idUser = null)
    {
        if (self::$isEnable) {
            return AuthorizationModel::checkAuthorization($permission, $idUser, true);
        }
        return true;
    }

    /**
     * Check if given or logged user is unauthorized of a permission.
     * @param string $permission
     * @param null|integer $idUser
     * @return bool
     */
    public static function isAuthorizedByBranch($permission, $idBranch = null, $idUser = null)
    {
        if (self::$isEnable) {
            return AuthorizationModel::checkAuthorization($permission, $idUser, true, $idBranch);
        }
        return true;
    }

    /**
     * Check if given or logged user is unauthorized of a permission.
     * @param string $permission
     * @param null|integer $idUser
     * @return bool
     */
    public static function isUnauthorized($permission, $idUser = null)
    {
        if (self::$isEnable) {
            return AuthorizationModel::checkAuthorization($permission, $idUser, false);
        }
        return true;
    }

    /**
     * Check authorization by granted or denied point of view.
     * @param $permission
     * @param $idUser
     * @param bool $grantedCheck
     * @return bool
     */
    private static function checkAuthorization($permission, $idUser, $grantedCheck = true, $idBranch = null)
    {
        $CI = get_instance();
        if (empty($idUser)) {
            $idUser = UserModel::authenticatedUserData('id', 0);
        }
        if (empty($idBranch)) {
            $branchId = get_active_branch('id');
        } else {
            $branchId = $idBranch;
        }

        $CI->load->driver('cache', ['adapter' => 'file']);
        $cacheKey = 'permissions-' . $branchId . '-' . url_title(strtolower(is_array($permission) ? json_encode($permission) : $permission)) . '-' . $idUser;
        $permissionNumRow = $CI->cache->get($cacheKey);

        if ($permissionNumRow === false) {
            $permissionQuery = $CI->db->select('prv_permissions.id, prv_permissions.permission')
                ->from(UserModel::$tableUser)
                ->join('prv_user_roles', 'prv_users.id = prv_user_roles.id_user')
                ->join('prv_roles', 'prv_user_roles.id_role = prv_roles.id')
                ->join('prv_role_permissions', 'prv_roles.id = prv_role_permissions.id_role')
                ->join('prv_permissions', 'prv_role_permissions.id_permission = prv_permissions.id')
                ->where('prv_users.id', $idUser)
                ->group_start()
                ->where_in('prv_permissions.permission', $permission)
                ->or_where('prv_users.username', 'admin')
                ->group_end();

            // role per branch
            if (!empty($branchId)) {
                $permissionQuery->where('prv_user_roles.id_branch', $branchId);
            }

            $permissionNumRow = $permissionQuery->get()->num_rows();

            $CI->cache->save($cacheKey, $permissionNumRow, 120);
        }

        if ($permissionNumRow > 0) {
            if ($grantedCheck) {
                return true;
            } else {
                log_message('info', 'User ID ' . $idUser . ' has no permission : ' . json_encode($permission));
                return false;
            }
        }

        if ($grantedCheck) {
            log_message('info', 'User ID ' . $idUser . ' has no permission : ' . json_encode($permission));
            return false;
        } else {
            return true;
        }
    }

    /**
     * Check if a user has a role.
     * @param $role
     * @param null|integer $idUser
     * @return bool
     */
    public static function hasRole($role, $idUser = null)
    {
        $branchId = get_active_branch('id');
        if (self::$isEnable) {
            $CI = get_instance();
            if ($idUser == null) {
                $idUser = UserModel::authenticatedUserData('id', 0);
            }
            $permission = $CI->db->select('prv_roles.id, prv_roles.role')
                ->from(UserModel::$tableUser)
                ->join('prv_user_roles', 'prv_users.id = prv_user_roles.id_user')
                ->join('prv_roles', 'prv_user_roles.id_role = prv_roles.id')
                ->where('prv_users.id', $idUser)
                ->where('prv_roles.role', $role);

            // role per branch
            if (!empty($branchId)) {
                $permission->where('prv_user_roles.id_branch', $branchId);
            }

            if ($permission->get()->num_rows() > 0) {
                return true;
            } else {
                log_message('info', 'User ID ' . $idUser . ' has no role : ' . $role);
            }
            return false;
        }
        return true;
    }

    /**
     * Check application access.
     *
     * @param $userId
     * @return bool
     */
    public static function hasApplicationAccess($userId = null)
    {
        $CI =& get_instance();
        if ($userId == null) {
            $userId = UserModel::authenticatedUserData('id', 0);
        }
        $CI->db = $CI->load->database('sso', true);

        $applications = $CI->db->select()->from('prv_users')
            ->distinct()
            ->join('prv_user_applications', 'prv_user_applications.id_user = prv_users.id')
            ->join('prv_applications', 'prv_applications.id = prv_user_applications.id_application')
            ->where('prv_user_applications.id_user', $userId)
            ->where("TRIM(TRAILING '/' FROM " . ('prv_applications.url') . ")=", rtrim(site_url('/', false), '/'));

        $total = $applications->get()->num_rows();

        $CI->db = $CI->load->database('default', true);

        if ($total > 0) {
            return true;
        } else {
            $message = 'You are <strong>UNAUTHORIZED</strong> to access this application.';

            if ($CI->input->is_ajax_request()) {
                return $message;
            } else {
                flash('danger', $message);
            }

            log_message('info', 'User ID ' . $userId . ' has no access to application ' . site_url('/'));
        }
        return false;
    }

}