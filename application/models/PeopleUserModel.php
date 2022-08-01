<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PeopleUserModel extends CI_Model
{
    private $table = 'ref_people_users';

    /**
     * People User Model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getUserById($id)
    {
        $this->db->select()
            ->from($this->table)
            ->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function getUserByPerson($personId)
    {
        $this->db->select([
            $this->table . '.*',
            'prv_users.name',
            'prv_users.username',
            'prv_users.email',
        ])
            ->from($this->table)
            ->join(UserModel::$tableUser, 'prv_users.id = ' . $this->table . '.id_user')
            ->where('id_people', $personId);
        return $this->db->get()->result_array();
    }

    /**
     * @param $data
     * @return bool
     */
    public function insertUser($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateUser($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteUser($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    /**
     * Delete model data.
     *
     * @param int|array $id
     * @param bool $softDelete
     * @return bool
     */
    public function delete($id, $softDelete = true)
    {
        if ($softDelete && $this->db->field_exists('is_deleted', $this->table)) {
            return $this->db->update($this->table, [
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => UserModel::authenticatedUserData('id')
            ], (is_array($id) ? $id : [$this->id => $id]));
        }
        return $this->db->delete($this->table, (is_array($id) ? $id : [$this->id => $id]));
    }
}