<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BookingExtensionFieldModel extends CI_Model
{
    private $table = 'ref_booking_extension_fields';

    /**
     * BookingExtensionFieldModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create booking extension field.
     * @param $data
     * @return bool|int
     */
    public function createBookingExtensionField($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Delete extension field by booking type.
     * @param $roleId
     * @return mixed
     */
    public function deleteExtensionFieldByBookingType($roleId)
    {
        return $this->db->delete($this->table, ['id_booking_type' => $roleId]);
    }

}