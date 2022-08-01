<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Report_tracking
 * @property BookingModel $booking
 * @property ReportTrackingModel $reportTracking
 * @property Exporter $exporter
 */
class Report_tracking extends MY_Controller
{
    /**
     * Report_tracking constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BookingModel', 'booking');
        $this->load->model('ReportTrackingModel', 'reportTracking');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'outbound_tracking' => 'GET',
            'ajax_get_outbound_tracking' => 'GET',
        ]);
    }

    /**
     * Show outbound tracking report
     */
    public function outbound_tracking()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_OUTBOUND_TRACKING);

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Outbound tracking", $this->reportTracking->getOutboundTracking($_GET));
        } else {
            $bookings = $this->booking->getBookingsByConditions(['bookings.id' => get_url_param('bookings')]);
            $this->render('report_tracking/outbound_tracking', compact('bookings'));
        }
    }

    /**
     * Get ajax paging data outbound tracking.
     */
    public function ajax_get_outbound_tracking()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_OUTBOUND_TRACKING);

        $filters = array_merge(get_url_param('filter') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir'],
        ]);

        $data = $this->reportTracking->getOutboundTracking($filters);

        $this->render_json($data);
    }
}
