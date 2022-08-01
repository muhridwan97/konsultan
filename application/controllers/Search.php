<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Search
 * @property HandlingModel $handling
 * @property WorkOrderModel $workOrder
 */
class Search extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();
        AuthorizationModel::mustLoggedIn();
        $this->load->model('HandlingModel', 'handling');
        $this->load->model('WorkOrderModel', 'workOrder');
    }

    /**
     * Show global search result.
     */
    public function index()
    {
        $query = $_GET['q'];
        $data = [
            'title' => "Search",
            'subtitle' => "result of '" . $query . "'",
            'page' => "search/index",
            'handlings' => $this->handling->search($query),
            'workOrders' => $this->workOrder->search($query),
        ];

        $this->load->view('template/layout', $data);
    }

}