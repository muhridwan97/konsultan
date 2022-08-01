<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BookingNewsDetailModel extends CI_Model
{
    private $table = 'booking_news_details';

    /**
     * BookingNewsModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active record query builder for all related branch data selection.
     * @return mixed
     */
    public function getBaseBookingNewsDetailQuery()
    {
        $bookingNews = $this->db
            ->select([
                'booking_news.id',
                'booking_news.no_booking_news',
                'ref_containers.no_container',
                'ref_containers.type',
                'ref_containers.size',
                'bookings.no_booking',
                'bookings.no_reference',
                'bookings.reference_date',
                'bookings.booking_date',
                'bookings.vessel',
                'ref_people.name AS customer_name',
                'booking_containers.seal',
                'booking_containers.description AS dog',
                'booking_news_details.condition',
                'booking_news_details.description',
            ])
            ->from($this->table)
            ->join('booking_news', 'booking_news.id = booking_news_details.id_booking_news', 'left')
            ->join('bookings', 'bookings.id = booking_news_details.id_booking', 'left')
            ->join('ref_people', 'bookings.id_customer = ref_people.id', 'left')
            ->join('booking_containers', 'booking_containers.id_booking = bookings.id', 'left')
            ->join('ref_containers', 'ref_containers.id = booking_containers.id_container', 'left');
        return $bookingNews;
    }

    /**
     * Get all booking news detail with or without deleted records.
     * @param $bookingNewsId
     * @param bool $raw
     * @return array
     */
    public function getBookingNewsDetailByBookingNews($bookingNewsId, $raw = false)
    {
        if ($raw) {
            $bookingNewsDetails = $this->db->get_where($this->table, [
                'id_booking_news' => $bookingNewsId
            ]);
        } else {
            $bookingNewsDetails = $this->getBaseBookingNewsDetailQuery()
                ->where('booking_news_details.id_booking_news', $bookingNewsId)->get();
        }

        return $bookingNewsDetails->result_array();
    }

    /**
     * Create new booking news detail.
     * @param $data
     * @return bool
     */
    public function createBookingNewsDetail($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Delete booking news data.
     * @param integer $id
     * @return mixed
     */
    public function deleteBookingNewsDetailByBookingNews($id)
    {
        return $this->db->delete($this->table, ['id_booking_news' => $id]);
    }
}