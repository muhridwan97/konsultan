<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PeopleContactModel extends CI_Model
{
    private $table = 'ref_people_contacts';

    /**
     * People Contact Model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    public function getContactById($id)
    {
        $this->db->select()
            ->from($this->table)
            ->where('id', $id);
        return $this->db->get()->row_array();
    }

    public function getContactByPerson($personId)
    {
        $this->db->select()
            ->from($this->table)
            ->where('id_person', $personId);
        return $this->db->get()->result_array();
    }

    /**
     * @param $data
     * @return bool
     */
    public function insertContact($data)
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
    public function updateContact($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * @param $id
     * @return bool
     */
    public function deleteContact($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }
}