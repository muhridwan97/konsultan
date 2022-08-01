<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Dashboard
 * @property TransporterEntryPermitModel $transporterEntryPermit
 * @property PhBidOrderSummaryModel $phBidOrderSummary
 */
class Dashboard_outbound_tracking extends MY_Controller
{
    /**
     * Dashboard_outbound_tracking constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('PhBidOrderSummaryModel', 'phBidOrderSummary');

        $this->setFilterMethods([
            'tracking_detail' => 'GET',
            'ajax_get_data_tracking' => 'GET',
        ]);
    }

    /**
     * Show outbound progress.
     */
    public function index()
    {
        $outboundStats = $this->phBidOrderSummary->getOutboundProgressTotal();

        $this->render('dashboard_outbound_tracking/index', compact('outboundStats'));
    }

    /**
     * Show detail of tracking status
     */
    public function tracking_detail()
    {
        $title = ucwords(str_replace(['-', '_'], ' ', get_url_param('status')));

        $this->render('dashboard_outbound_tracking/tracking_detail', [], $title);
    }

    /**
     * Get ajax paging data.
     */
    public function ajax_get_data_tracking()
    {
        $filters = array_merge($_GET, [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir'],
        ]);

        $data = $this->phBidOrderSummary->getOrderContainerPaging($filters);

        $this->render_json($data);
    }
}
