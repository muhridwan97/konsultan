<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BookingStatusModel extends CI_Model
{
    private $table = 'booking_statuses';

    /**
     * BookingStatusModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active record query builder for all related warehouse data selection.
     * @return CI_DB_query_builder
     */
    private function getBaseBookingStatusQuery()
    {
        $bookingStatuses = $this->db
            ->select([
                'booking_statuses.*',
                'bookings.no_booking',
                'bookings.no_reference',
                'prv_users.username AS creator_name'
            ])
            ->from('booking_statuses')
            ->join('bookings', 'bookings.id = booking_statuses.id_booking', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = booking_statuses.created_by', 'left');

        return $bookingStatuses;
    }

    /**
     * Get all booking statuses with or without deleted records.
     * @return mixed
     */
    public function getAllBookingStatuses()
    {
        $bookingStatuses = $this->getBaseBookingStatusQuery();

        return $bookingStatuses->get()->result_array();
    }

    /**
     * Get single warehouse data by id with or without deleted record.
     * @param $id
     * @return mixed
     */
    public function getBookingStatusesById($id)
    {
        $bookingStatus = $this->getBaseBookingStatusQuery()->where('booking_statuses.id', $id);

        return $bookingStatus->get()->row_array();
    }

    /**
     * Get all booking statuses by given booking id.
     * @param $bookingId
     * @return array
     */
    public function getBookingStatusesByBooking($bookingId)
    {
        $bookingStatuses = $this->getBaseBookingStatusQuery()->where('bookings.id', $bookingId);

        return $bookingStatuses->get()->result_array();
    }

    /**
     * Create new warehouse.
     * @param $data
     * @return bool
     */
    public function createBookingStatus($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update warehouse.
     * @param $data
     * @param $id
     * @return bool
     */
    public function updateBookingStatus($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete booking status data.
     * @param $id
     * @return bool
     */
    public function deleteBookingStatus($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

    /**
     * Delete booking status data.
     * @param $bookingId
     * @return bool
     */
    public function deleteBookingStatusesByBooking($bookingId)
    {
        return $this->db->delete($this->table, ['id_booking' => $bookingId]);
    }
}