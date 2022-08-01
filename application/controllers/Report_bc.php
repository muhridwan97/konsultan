<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Report_bc
 * @property ReportBCModel $reportModel
 * @property PeopleModel $peopleModel
 * @property GoodsModel $goodsModel
 * @property ContainerModel $containerModel
 * @property UnitModel $unitModel
 * @property BookingModel $bookingModel
 * @property WorkOrderContainerModel $workOrderContainer
 * @property WorkOrderGoodsModel $workOrderGoods
 * @property LogModel $logHistory
 * @property Exporter $exporter
 */
class Report_bc extends MY_Controller
{
    /**
     * Report constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ReportBCModel', 'reportModel');
        $this->load->model('PeopleModel', 'peopleModel');
        $this->load->model('GoodsModel', 'goodsModel');
        $this->load->model('ContainerModel', 'containerModel');
        $this->load->model('UnitModel', 'unitModel');
        $this->load->model('BookingModel', 'bookingModel');
        $this->load->model('BookingTypeModel', 'bookingType');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('WorkOrderContainerModel', 'workOrderContainer');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('LogModel', 'logHistory');

        $this->setFilterMethods([
            'in' => 'GET',
            'in_data' => 'GET|POST',
            'out' => 'GET',
            'out_data' => 'GET|POST',
            'stock_mutation' => 'GET',
            'logs' => 'GET',
            'logs_data' => 'GET',
            'ajax_get_container_and_goods_by_name' => 'GET',
        ]);
    }

    /**
     * Default index report
     */
    public function index()
    {
        redirect('report_bc/in');
    }

    /**
     * In stock report
     */
    public function in()
    {
        $filter = get_url_param('filter_activity') ? $_GET : [];
        $export = get_url_param('export');

        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();

        if (!empty($export)) {
            $reportInbounds = $this->reportModel->getReportInbound($filter);
            $this->exporter->exportFromArray('Inbound', $reportInbounds);
        } else {
            $selectedOwners = key_exists('owner', $filter) ? $filter['owner'] : '';
            $selectedItems = key_exists('item', $filter) ? $filter['item'] : [-1];

            $owners = $this->peopleModel->getBy(['ref_people.name' => $selectedOwners]);
            $items = $this->reportModel->getItemsByName($selectedItems);

            $this->render('report_bc/inbound', compact('owners', 'items'), 'Report In');
        }
    }

    /**
     * Get ajax datatable.
     */
    public function in_data()
    {
        $filters = array_merge(get_url_param('filter_activity') ? $_GET : [], [
            'start' => get_url_param('start', $this->input->post('start')),
            'length' => get_url_param('length', $this->input->post('length')),
            'search' => get_url_param('search', $this->input->post('search')['value']),
            'order_by' => get_url_param('order', $this->input->post('order')[0]['column']),
            'order_method' => get_url_param('order_method', $this->input->post('order')[0]['dir']),
        ]);
        $inbounds = $this->reportModel->getReportInbound($filters);

        $this->render_json($inbounds);
    }

    /**
     * Out stock report
     */
    public function out()
    {
        $filter = get_url_param('filter_activity') ? $_GET : [];
        $export = get_url_param('export');

        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();

        $this->logHistory->create([
            'id_branch' => $branch['id'],
            'type' => 'REPORT BC OUT',
            'data' => [
                'user_id' => $userData['id'],
                'name' => $userData['name'],
                'username' => $userData['username'],
                'access' => $branch['branch'],
            ]
        ]);

        if (!empty($export)) {
            $reportOutbounds = $this->reportModel->getReportOutbound($filter);
            $this->exporter->exportFromArray('Outbound', $reportOutbounds);
        } else {
            $selectedOwners = key_exists('owner', $filter) ? $filter['owner'] : '';
            $selectedItems = key_exists('item', $filter) ? $filter['item'] : [-1];

            $owners = $this->peopleModel->getBy(['ref_people.name' => $selectedOwners]);
            $items = $this->reportModel->getItemsByName($selectedItems);

            $this->render('report_bc/outbound', compact('owners', 'items'), 'Report Out');
        }
    }

    /**
     * Get ajax datatable.
     */
    public function out_data()
    {
        $filters = array_merge(get_url_param('filter_activity') ? $_GET : [], [
            'start' => get_url_param('start', $this->input->post('start')),
            'length' => get_url_param('length', $this->input->post('length')),
            'search' => get_url_param('search', $this->input->post('search')['value']),
            'order_by' => get_url_param('order', $this->input->post('order')[0]['column']),
            'order_method' => get_url_param('order_method', $this->input->post('order')[0]['dir']),
        ]);
        $outbounds = $this->reportModel->getReportOutbound($filters);

        $this->render_json($outbounds);
    }

    /**
     * Show report stock mutation.
     */
    public function stock_mutation()
    {
        $filter = get_url_param('filter_mutation') ? $_GET : [];
        $export = get_url_param('export');

        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();

        $this->logHistory->create([
            'id_branch' => $branch['id'],
            'type' => 'REPORT BC STOCK MUTATION',
            'data' => [
                'user_id' => $userData['id'],
                'name' => $userData['name'],
                'username' => $userData['username'],
                'access' => $branch['branch'],
            ]
        ]);

        $selectedOwners = key_exists('owner', $filter) ? $filter['owner'] : '';
        $selectedItems = key_exists('item', $filter) ? $filter['item'] : [-1];
        $selectedBookings = key_exists('booking', $filter) ? $filter['booking'] : [-1];

        $owners = $this->peopleModel->getBy(['ref_people.name' => $selectedOwners]);
        $items = $this->reportModel->getItemsByName($selectedItems);
        $bookings = $this->bookingModel->getBookingById($selectedBookings);

        if (!empty($export)) {
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=mutation.xls");
            $reportMutations = $this->reportModel->getReportMutation($filter);
            $this->load->view('report_bc/_plain_mutation', ['reportMutations' => array_reverse($reportMutations)]);
        } else {
            if (!empty($filter)) {
                $reportMutations = $this->reportModel->getReportMutation($filter);
            } else {
                $reportMutations = [];
            }

            $reportMutations = array_reverse($reportMutations);

            $this->render('report_bc/stock_mutation', compact('owners', 'items', 'bookings', 'reportMutations'), 'Report Mutation');
        }
    }

    /**
     * Log history report
     */
    public function logs()
    {
        $export = get_url_param('export');
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();

        $this->logHistory->create([
            'id_branch' => $branch['id'],
            'type' => 'REPORT BC LOG HISTORIES',
            'data' => [
                'user_id' => $userData['id'],
                'name' => $userData['name'],
                'username' => $userData['username'],
                'access' => $branch['branch'],
            ]
        ]);

        if (!empty($export)) {
            $logs = $this->logHistory->getAll();
            $this->exporter->exportFromArray('Logs', $logs);
        } else {
            $this->render('logs/index', [], 'Log Histories');
        }
    }

    /**
     * Ajax get all item data
     */
    public function ajax_get_container_and_goods_by_name()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $search = $this->input->get('q');
            $page = $this->input->get('page');

            $items = $this->reportModel->getItemsByName($search, $page);

            $this->render_json($items);
        }
    }

}