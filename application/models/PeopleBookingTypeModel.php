<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PeopleBookingTypeModel extends CI_Model
{
    private $table = 'ref_people_booking_types';

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
    public function createPeopleBookingType($data)
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
    public function deletePeopleBookingTypeByCustomer($customerId)
    {
        return $this->db->delete($this->table, ['id_customer' => $customerId]);
    }
}