<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PeopleHandlingTypeModel extends CI_Model
{
    private $table = 'ref_people_handling_types';

    /**
     * PeopleHandlingTypeModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create people handling type.
     * @param $data
     * @return bool|int
     */
    public function createPeopleHandlingType($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Delete person.
     * @param $customerId
     * @return mixed
     */
    public function deletePeopleHandlingTypeByCustomer($customerId)
    {
        return $this->db->delete($this->table, ['id_customer' => $customerId]);
    }
}