<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UserRoleModel extends CI_Model
{
    private $table = 'prv_user_roles';

    /**
     * UserRoleModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create role permission.
     * @param $data
     * @return bool|int
     */
    public function createUserRole($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Delete role by user.
     * @param $userId
     * @return mixed
     */
    public function deleteUserRoleByUser($userId)
    {
        return $this->db->delete($this->table, ['id_user' => $userId]);
    }

}