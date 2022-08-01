<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BookingDocumentTypeModel extends CI_Model
{
    private $table = 'ref_booking_document_types';

    /**
     * BookingDocumentTypeModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Create booking type document.
     *
     * @param $data
     * @return bool|int
     */
    public function createBookingDocumentType($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Delete document type by booking type.
     * @param $roleId
     * @return mixed
     */
    public function deleteDocumentTypeByBookingType($roleId)
    {
        return $this->db->delete($this->table, ['id_booking_type' => $roleId]);
    }

}