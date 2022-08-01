<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Booking_assignment
 * @property BookingAssignmentModel $bookingAssignment
 * @property UserModel $user
 * @property BookingModel $booking
 */
class Booking_assignment extends MY_Controller
{
    /**
     * Auction constructor.
     */
    public function __construct()
    {
        parent::__construct();

        AuthorizationModel::mustLoggedIn();

        $this->load->model('BookingAssignmentModel', 'bookingAssignment');
        $this->load->model('UserModel', 'user');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('LogModel', 'logHistory');

        $this->setFilterMethods([
            'booking_assignment_data' => 'GET'
        ]);
    }

    /**
     * Show bookingAssignment data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_ASSIGNMENT_VIEW);

        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();

        $this->render('booking_assignment/index', []);
    }

    /**
     * Get ajax datatable bookingAssignment.
     */
    public function booking_assignment_data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_ASSIGNMENT_VIEW);

        $filters = [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ];

        $data = $this->bookingAssignment->getAll($filters);

        $this->render_json($data);
    }

    /**
     * Show detail bookingAssignment.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_ASSIGNMENT_VIEW);

        $bookingAssignment = $this->bookingAssignment->getById($id);

        $this->render('booking_assignment/view', compact('bookingAssignment'));
    }

    /**
     * Show form create booking assignment.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_ASSIGNMENT_CREATE);

        $bookings = $this->bookingAssignment->getUnassignedBooking();
        $users = $this->user->getByPermission(PERMISSION_PAYMENT_CREATE);

        $this->render('booking_assignment/create', compact('bookings', 'users'));
    }

    /**
     * Save new assignment data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_ASSIGNMENT_CREATE);

        if ($this->validate()) {
            $bookingId = $this->input->post('booking');
            $userId = $this->input->post('user');
            $description = $this->input->post('description');

            $booking = $this->booking->getBookingById($bookingId);
            $user = $this->user->getById($userId);

            $assignment = $this->bookingAssignment->create([
                'id_booking' => $bookingId,
                'id_user' => $userId,
                'description' => $description,
            ]);

            if ($assignment) {
                flash('success', "Booking {$booking['no_booking']} is assigned to ${user['name']}", 'booking-assignment');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->create();
    }

    /**
     * Perform deleting booking assignment data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_ASSIGNMENT_DELETE);

        if ($this->bookingAssignment->delete($id)) {
            flash('warning', "Assignment is successfully deleted");
        } else {
            flash('danger', "Delete assignment failed");
        }
        redirect('booking-assignment');
    }

    /**
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'booking' => 'trim|required|integer|is_natural_no_zero',
            'user' => 'trim|required|integer|is_natural_no_zero',
        ];
    }
}