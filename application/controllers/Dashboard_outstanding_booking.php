<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Dashboard
 * @property UploadModel $upload
 * @property Exporter $exporter
 */
class Dashboard_outstanding_booking extends MY_Controller
{
    /**
     * Dashboard_outstanding_booking constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('UploadModel', 'upload');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show outstanding booking.
     */
    public function index()
    {
        $outstandingBookings = $this->upload->getOutstandingBooking();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Outstanding Booking", $outstandingBookings);
        } else {
            $this->render('dashboard_outstanding_booking/index', compact('outstandingBookings'));
        }
    }

}
