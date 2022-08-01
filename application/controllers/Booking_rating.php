<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Booking_rating
 * @property BookingModel $booking
 */
class Booking_rating extends MY_Controller
{
    /**
     * Booking_rating constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BookingModel', 'booking');

        $this->setFilterMethods([
            'rate' => 'POST|PUT'
        ]);
    }

    /**
     * Show booking rating outstanding data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_RATE);

        $bookings = BookingModel::getOutstandingRating(['outstanding' => true]);

        $this->render('booking_rating/index', compact('bookings'));
    }

    /**
     * Rate booking data.
     *
     * @param $bookingId
     */
    public function rate($bookingId)
    {
        if ($this->validate(['rating' => 'required'])) {
            $rating = $this->input->post('rating');
            $description = $this->input->post('description');

            $booking = $this->booking->getBookingById($bookingId);

            $update = $this->booking->updateBooking([
                'rating' => $rating,
                'rating_description' => $description,
                'rated_at' => date('Y-m-d H:i:s')
            ], $bookingId);

            if ($update) {
                flash('success', "Booking {$booking['no_booking']} set rating to " . $rating);
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        redirect('booking-rating');
    }

}