<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BookingExtensionModel extends CI_Model
{
    private $table = 'booking_extensions';

    /**
     * BookingExtensionModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Base booking extension base query.
     *
     * @return CI_DB_query_builder
     */
    private function getBaseBookingExtensionQuery()
    {
        $bookingExtensions = $this->db->select([
            'booking_extensions.*',
            'ref_extension_fields.field_title',
            'ref_extension_fields.field_name',
            'ref_extension_fields.type'
        ])
            ->from($this->table)
            ->join('ref_extension_fields', 'booking_extensions.id_extension_field = ref_extension_fields.id', 'left');

        return $bookingExtensions;
    }

    /**
     * Get booking's extension.
     * @param $bookingId
     * @return mixed
     */
    public function getBookingExtensionByBooking($bookingId)
    {
        $bookingFields = $this->getBaseBookingExtensionQuery()->where('id_booking', $bookingId);

        return $bookingFields->get()->result_array();
    }

    /**
     * Create new booking with details.
     * @param $data
     * @return bool
     */
    public function createBookingExtension($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update booking extension.
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateBookingExtension($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete booking's extension
     * @param $bookingId
     * @return mixed
     */
    public function deleteBookingExtensionByBooking($bookingId)
    {
        return $this->db->delete($this->table, ['id_booking' => $bookingId]);
    }

    /**
     * Delete booking data.
     * @param $id
     * @return bool
     */
    public function deleteBookingExtension($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }
}