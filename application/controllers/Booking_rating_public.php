<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Booking_rating_public
 * @property BookingModel $booking
 * @property BookingContainerModel $bookingContainer
 * @property BookingGoodsModel $bookingGoods
 * @property UserTokenModel $userToken
 */
class Booking_rating_public extends CI_Controller
{
    /**
     * Booking_rating constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('UserTokenModel', 'userToken');
    }

    /**
     * Show index public rating
     */
    public function index()
    {
        $this->load->view('booking_rating/index_public');
    }

    /**
     * Show booking rating preview.
     *
     * @param $token
     */
    public function rate($token)
    {
        $bookingId = get_url_param('id_booking');

        $emailToken = $this->userToken->verifyToken($token, UserTokenModel::$TOKEN_RATING);

        if (!$emailToken) {
            flash('danger', 'Invalid or expired rating token key.', 'booking-rating-public');
        } else {
            if (_is_method('get')) {
                $booking = $this->booking->getBookingById($bookingId);
                $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($bookingId);
                foreach ($bookingContainers as &$container) {
                    $containerGoods = $this->bookingGoods->getBookingGoodsByBookingContainer($container['id']);
                    $container['goods'] = $containerGoods;
                }
                $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($bookingId, true);

                $this->load->view('booking_rating/rate_public', compact('booking', 'bookingContainers', 'bookingGoods', 'token'));
            } else {
                $rating = $this->input->post('rating');
                $description = $this->input->post('description');

                $booking = $this->booking->getBookingById($bookingId);

                $this->db->trans_start();

                $this->booking->updateBooking([
                    'rating' => $rating,
                    'rating_description' => $description,
                    'rated_at' => date('Y-m-d H:i:s')
                ], $bookingId);

                $this->userToken->deleteToken($token);

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Booking {$booking['no_booking']} set rating to " . $rating . ', thank you for your feedback');
                } else {
                    flash('danger', 'Something is getting wrong, try again or contact administrator');
                }
                redirect('booking-rating-public');
            }
        }
    }

}