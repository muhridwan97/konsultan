<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RoleModel extends MY_Model
{
    protected $table = 'prv_roles';

    /**
     * RoleModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active record query builder for all related role data selection.
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return $this->db
            ->select([
                'prv_roles.*',
                'IFNULL(total_permission, 0) AS total_permission',
                'IFNULL(total_user, 0) AS total_user'
            ])
            ->from($this->table)
            ->join('(
                    SELECT id_role, COUNT(id) AS total_permission
                    FROM prv_role_permissions
                    GROUP BY id_role
                ) AS role_permissions', 'role_permissions.id_role = prv_roles.id', 'left')
            ->join('(
                    SELECT id_role, COUNT(DISTINCT prv_users.id) AS total_user
                    FROM prv_user_roles
                    INNER JOIN '.UserModel::$tableUser.' ON prv_users.id = prv_user_roles.id_user
                    WHERE prv_users.is_deleted = 0
                    GROUP BY id_role
                ) AS user_roles', 'user_roles.id_role = prv_roles.id', 'left');
    }

    /**
     * Get all roles of specific user.
     * @param integer $userId
     * @param bool $withTrashed
     * @return array
     */
    public function getByUser($userId, $withTrashed = false, $branchId = null)
    {
        $roles = $this->getBaseQuery()
            ->select([
                'prv_user_roles.id_branch',
                'ref_branches.branch'
            ])
            ->join('prv_user_roles', 'prv_user_roles.id_role = prv_roles.id')
            ->join('ref_branches', 'ref_branches.id = prv_user_roles.id_branch')
            ->where('prv_roles.is_deleted', false)
            ->where('prv_user_roles.id_user', $userId);

        if (!$withTrashed) {
            $roles->where('prv_roles.is_deleted', false);
        }

        if (!is_null($branchId)) {
            $roles->where('ref_branches.id', $branchId);
        }

        return $roles->get()->result_array();
    }

}