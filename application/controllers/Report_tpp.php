<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Report_tpp
 * @property ReportModel $reportModel
 * @property ReportTppModel $reportTppModel
 * @property ReportTpsModel $reportTps
 * @property PeopleModel $peopleModel
 * @property GoodsModel $goodsModel
 * @property ContainerModel $containerModel
 * @property UnitModel $unitModel
 * @property BookingModel $bookingModel
 * @property WorkOrderContainerModel $workOrderContainer
 * @property WorkOrderGoodsModel $workOrderGoods
 * @property WarehouseModel $warehouse
 * @property Exporter $exporter
 */
class Report_tpp extends CI_Controller
{
    /**
     * Report constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ReportModel', 'reportModel');
        $this->load->model('ReportTppModel', 'reportTppModel');
        $this->load->model('ReportTpsModel', 'reportTps');
        $this->load->model('PeopleModel', 'peopleModel');
        $this->load->model('GoodsModel', 'goodsModel');
        $this->load->model('ContainerModel', 'containerModel');
        $this->load->model('UnitModel', 'unitModel');
        $this->load->model('BookingModel', 'bookingModel');
        $this->load->model('BookingTypeModel', 'bookingType');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('WorkOrderContainerModel', 'workOrderContainer');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('WarehouseModel', 'warehouse');
        $this->load->model('LogModel', 'logHistory');
    }

    /**
     * Default index report
     */
    public function index()
    {
        redirect('report_tpp/event_summary');
    }

    /**
     * Show customs daily report.
     */
    public function customs_daily()
    {
        $filter = get_url_param('filter_activity') ? $_GET : [];

        if (!empty($filter)) {
            $inboundData = $this->reportTppModel->getReportCustomsDaily('INBOUND', $filter);
            $outboundData = $this->reportTppModel->getReportCustomsDaily('OUTBOUND', $filter);
        } else {
            $inboundData = [];
            $outboundData = [];
        }

        // group data into date
        $reportCustomsDaily = [];
        foreach ($inboundData as $datum) {
            $reportCustomsDaily[format_date($datum['completed_at'])]['inbound'][] = $datum;
        }
        foreach ($outboundData as $datum) {
            $reportCustomsDaily[format_date($datum['completed_at'])]['outbound'][] = $datum;
        }

        // order the key because if outbound come after inbound in some condition there is transaction without inbound,
        // then the date created as new key below the inbound list as new date so we make sure that the order keep still
        ksort($reportCustomsDaily);

        // fill the blank key, as above there is the date has inbound but outbound, vice versa,
        // then add the key with empty data
        $totalMovement20In = $totalMovement40In = $totalMovement45In = $totalMovementLCLIn = $totalMovementBCFIn = 0;
        $totalMovement20Out = $totalMovement40Out = $totalMovement45Out = $totalMovementLCLOut = $totalMovementBCFOut = 0;
        $totalMovement20InHold = $totalMovement40InHold = $totalMovement45InHold = $totalMovementLCLInHold = $totalMovementBCFInHold = 0;
        $totalMovement20OutHold = $totalMovement40OutHold = $totalMovement45OutHold = $totalMovementLCLOutHold = $totalMovementBCFOutHold = 0;
        $inboundCollection = [];
        $outboundCollection = [];
        foreach ($reportCustomsDaily as &$report) {
            if (!key_exists('inbound', $report)) {
                $report['inbound'] = [];
            }
            if (!key_exists('outbound', $report)) {
                $report['outbound'] = [];
            }

            $totalMovementBCFIn += count(array_unique(array_column(array_filter($report['inbound'], function ($in) use ($inboundCollection) {
                return $in['booking_type'] != 'TEGAHAN' && !in_array($in['no_reference'], $inboundCollection);
            }), 'no_reference')));
            $totalMovementBCFInHold += count(array_unique(array_column(array_filter($report['inbound'], function ($in) use ($inboundCollection) {
                return $in['booking_type'] == 'TEGAHAN' && !in_array($in['no_reference'], $inboundCollection);
            }), 'no_reference')));

            $total20 = $total40 = $total45 = $totalLCL = 0;
            $total20Hold = $total40Hold = $total45Hold = $totalLCLHold = 0;
            foreach ($report['inbound'] as $inbound) {
                $inboundCollection[] = $inbound['no_reference'];
                if($inbound['booking_type'] == 'TEGAHAN') { // hard-coded hold booking
                    if ($inbound['item_type'] == 'CONTAINER') {
                        switch ($inbound['container_size']) {
                            case 20: $total20Hold++; break;
                            case 40: $total40Hold++; break;
                            case 45: $total45Hold++; break;
                        }
                    } else {
                        $totalLCLHold++;
                    }
                } else {
                    if ($inbound['item_type'] == 'CONTAINER') {
                        switch ($inbound['container_size']) {
                            case 20: $total20++; break;
                            case 40: $total40++; break;
                            case 45: $total45++; break;
                        }
                    } else {
                        $totalLCL++;
                    }
                }
            }

            // add inbound summary
            $report['inbound_summary'] = [
                'total_20' => $total20, 'total_40' => $total40, 'total_45' => $total45, 'total_lcl' => $totalLCL
            ];
            $totalMovement20In += $total20;
            $totalMovement40In += $total40;
            $totalMovement45In += $total45;
            $totalMovementLCLIn += $totalLCL;
            $report['inbound_movement_summary'] = [
                'total_20' => $totalMovement20In,
                'total_40' => $totalMovement40In,
                'total_45' => $totalMovement45In,
                'total_lcl' => $totalMovementLCLIn,
                'total_container' => ($totalMovement20In + $totalMovement40In + $totalMovement45In),
                'total_bcf' => $totalMovementBCFIn,
            ];

            $totalMovement20InHold += $total20Hold;
            $totalMovement40InHold += $total40Hold;
            $totalMovement45InHold += $total45Hold;
            $totalMovementLCLInHold += $totalLCLHold;
            $report['hold_inbound_movement_summary'] = [
                'total_20' => $totalMovement20InHold,
                'total_40' => $totalMovement40InHold,
                'total_45' => $totalMovement45InHold,
                'total_lcl' => $totalMovementLCLInHold,
                'total_container' => ($totalMovement20InHold + $totalMovement40InHold + $totalMovement45InHold),
                'total_bcf' => $totalMovementBCFInHold,
            ];

            $totalMovementBCFOut += count(array_unique(array_column(array_filter($report['outbound'], function ($out) use ($outboundCollection) {
                return $out['booking_type'] != 'TEGAHAN' && !in_array($out['no_reference'], $outboundCollection);
            }), 'no_reference')));
            $totalMovementBCFOutHold += count(array_unique(array_column(array_filter($report['outbound'], function ($out) use ($outboundCollection) {
                return $out['booking_type'] == 'TEGAHAN' && !in_array($out['no_reference'], $outboundCollection);
            }), 'no_reference')));

            $total20 = $total40 = $total45 = $totalLCL = 0;
            $total20Hold = $total40Hold = $total45Hold = $totalLCLHold = 0;
            foreach ($report['outbound'] as $outbound) {
                $outboundCollection[] = $outbound['no_reference'];
                if($outbound['booking_type'] == 'TEGAHAN') { // hard-coded hold booking
                    if ($outbound['item_type'] == 'CONTAINER') {
                        switch ($outbound['container_size']) {
                            case 20: $total20Hold++; break;
                            case 40: $total40Hold++; break;
                            case 45: $total45Hold++; break;
                        }
                    } else {
                        $totalLCLHold++;
                    }
                }
                else {
                    if ($outbound['item_type'] == 'CONTAINER') {
                        switch ($outbound['container_size']) {
                            case 20: $total20++; break;
                            case 40: $total40++; break;
                            case 45: $total45++; break;
                        }
                    } else {
                        $totalLCL++;
                    }
                }
            }

            // add outbound summary
            $report['outbound_summary'] = [
                'total_20' => $total20, 'total_40' => $total40, 'total_45' => $total45, 'total_lcl' => $totalLCL,
            ];
            $totalMovement20Out += $total20;
            $totalMovement40Out += $total40;
            $totalMovement45Out += $total45;
            $totalMovementLCLOut += $totalLCL;
            $report['outbound_movement_summary'] = [
                'total_20' => $totalMovement20Out,
                'total_40' => $totalMovement40Out,
                'total_45' => $totalMovement45Out,
                'total_lcl' => $totalMovementLCLOut,
                'total_container' => ($totalMovement20Out + $totalMovement40Out + $totalMovement45Out),
                'total_bcf' => $totalMovementBCFOut,
            ];

            $totalMovement20OutHold += $total20Hold;
            $totalMovement40OutHold += $total40Hold;
            $totalMovement45OutHold += $total45Hold;
            $totalMovementLCLOutHold += $totalLCLHold;
            $report['hold_outbound_movement_summary'] = [
                'total_20' => $totalMovement20OutHold,
                'total_40' => $totalMovement40OutHold,
                'total_45' => $totalMovement45OutHold,
                'total_lcl' => $totalMovementLCLOutHold,
                'total_container' => ($totalMovement20OutHold + $totalMovement40OutHold + $totalMovement45OutHold),
                'total_bcf' => $totalMovementBCFOutHold,
            ];
        }

        if (!empty(get_url_param('export'))) {
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=customs_daily.xls");
            $this->load->view('report_tpp/_plain_customs_daily', [
                'reportCustomsDaily' => $reportCustomsDaily
            ]);
        } else {
            $data = [
                'title' => "Customs Daily Report",
                'page' => "report_tpp/customs_daily",
                'reportCustomsDaily' => $reportCustomsDaily
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Show customs stock report.
     */
    public function customs_stock()
    {
        $filter = get_url_param('filter_activity') ? $_GET : [];
        $export = get_url_param('export');

        if (!empty($export)) {
            $reportCustomsStock = $this->reportTppModel->getReportCustomsStock($filter);
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=customs_daily.xls");
            $this->load->view('report_tpp/_plain_customs_stock', [
                'reportCustomsStock' => $reportCustomsStock
            ]);
        } else {
            $data = [
                'title' => "Customs Stock Report",
                'page' => "report_tpp/customs_stock",
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable custom stock.
     */
    public function customs_stock_data()
    {
        $filters = array_merge(get_url_param('filter_activity') ? $_GET : [], [
            'start' => get_url_param('start', $this->input->post('start')),
            'length' => get_url_param('length', $this->input->post('length')),
            'search' => get_url_param('search', $this->input->post('search'))['value'],
            'order_by' => get_url_param('order', $this->input->post('order'))[0]['column'],
            'order_method' => get_url_param('order', $this->input->post('order'))[0]['dir'],
        ]);
        $customStock = $this->reportTppModel->getReportCustomsStock($filters);

        header('Content-Type: application/json');
        echo json_encode($customStock);
    }

    /**
     * Get stock summary.
     */
    public function stock_summary()
    {
        $containerFilter = get_url_param('filter_summary_container') ? $_GET : [];

        $export = get_url_param('export');
        if (!empty($export)) {
            $reportData = $this->reportTppModel->getStockContainers($containerFilter);
            $this->exporter->exportFromArray('Stock Summary ' . $export, $reportData);
            exit();
        }

        $warehouses = $this->warehouse->getBy(['ref_warehouses.id_branch' => get_active_branch('id')]);
        $bookingContainers = $this->reportModel->getAvailableStockBookingList('container');
        $selectedOwners = key_exists('owner', $containerFilter) ? $containerFilter['owner'] : 0;
        $owners = $this->peopleModel->getById($selectedOwners);
        $data = [
            'title' => "Stock Summary",
            'page' => "report_tpp/stock_summary",
            'owners' => $owners,
            'warehouses' => $warehouses,
            'bookingContainers' => $bookingContainers,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Get stock datatable.
     */
    public function stock_summary_container_data()
    {
        $filters = array_merge(get_url_param('filter_summary_container') ? $_GET : [], [
            'start' => get_url_param('start', $this->input->get('start')),
            'length' => get_url_param('length', $this->input->get('length')),
            'search' => get_url_param('search', $this->input->get('search'))['value'],
            'order_by' => get_url_param('order', $this->input->get('order'))[0]['column'],
            'order_method' => get_url_param('order', $this->input->get('order'))[0]['dir'],
        ]);
        $stockContainers = $this->reportTppModel->getStockContainers($filters);

        header('Content-Type: application/json');
        echo json_encode($stockContainers);
    }

    /**
     * Show report custom ex.
     */
    public function customs_ex()
    {
        $filter = get_url_param('filter_ex') ? $_GET : [];
        $export = get_url_param('export');
        $type = get_url_param('type');

        if (!empty($export)) {
            switch ($type) {
                case 'btd':
                    $reports = $this->reportTppModel->getReportBTD($filter);
                    $plainView = '_plain_ex_btd';
                    break;
                case 'bdn':
                    $reports = $this->reportTppModel->getReportBDN($filter);
                    $plainView = '_plain_ex_bdn';
                    break;
                case 'bmn':
                    $reports = $this->reportTppModel->getReportBMN($filter);
                    $plainView = '_plain_ex_bmn';
                    break;
                case 'tegahan':
                    $reports = $this->reportTppModel->getReportTegahan($filter);
                    $plainView = '_plain_ex_tegahan';
                    break;
                default:
                    die('Invalid report type');
            }
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=customs_ex_{$type}.xls");
            $this->load->view('report_tpp/' . $plainView, [
                'reports' => $reports
            ]);
        } else {
            $data = [
                'title' => "Customs Ex Report",
                'page' => "report_tpp/customs_ex",
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Report BTD ajax call data.
     */
    public function custom_btd_data()
    {
        $filters = array_merge(get_url_param('filter_ex') ? $_GET : [], [
            'start' => get_url_param('start', $this->input->post('start')),
            'length' => get_url_param('length', $this->input->post('length')),
            'search' => get_url_param('search', $this->input->post('search'))['value'],
            'order_by' => get_url_param('order', $this->input->post('order'))[0]['column'],
            'order_method' => get_url_param('order', $this->input->post('order'))[0]['dir'],
        ]);
        $report = $this->reportTppModel->getReportBTD($filters);

        header('Content-Type: application/json');
        echo json_encode($report);
    }

    /**
     * Report BDN ajax call data.
     */
    public function custom_bdn_data()
    {
        $filters = array_merge(get_url_param('filter_ex') ? $_GET : [], [
            'start' => get_url_param('start', $this->input->post('start')),
            'length' => get_url_param('length', $this->input->post('length')),
            'search' => get_url_param('search', $this->input->post('search'))['value'],
            'order_by' => get_url_param('order', $this->input->post('order'))[0]['column'],
            'order_method' => get_url_param('order', $this->input->post('order'))[0]['dir'],
        ]);
        $report = $this->reportTppModel->getReportBDN($filters);

        header('Content-Type: application/json');
        echo json_encode($report);
    }

    /**
     * Report BMN ajax call data.
     */
    public function custom_bmn_data()
    {
        $filters = array_merge(get_url_param('filter_ex') ? $_GET : [], [
            'start' => get_url_param('start', $this->input->post('start')),
            'length' => get_url_param('length', $this->input->post('length')),
            'search' => get_url_param('search', $this->input->post('search'))['value'],
            'order_by' => get_url_param('order', $this->input->post('order'))[0]['column'],
            'order_method' => get_url_param('order', $this->input->post('order'))[0]['dir'],
        ]);
        $report = $this->reportTppModel->getReportBMN($filters);

        header('Content-Type: application/json');
        echo json_encode($report);
    }

    /**
     * Report BMN ajax call data.
     */
    public function custom_tegahan_data()
    {
        $filters = array_merge(get_url_param('filter_ex') ? $_GET : [], [
            'start' => get_url_param('start', $this->input->post('start')),
            'length' => get_url_param('length', $this->input->post('length')),
            'search' => get_url_param('search', $this->input->post('search'))['value'],
            'order_by' => get_url_param('order', $this->input->post('order'))[0]['column'],
            'order_method' => get_url_param('order', $this->input->post('order'))[0]['dir'],
        ]);
        $report = $this->reportTppModel->getReportTegahan($filters);

        header('Content-Type: application/json');
        echo json_encode($report);
    }

    /**
     * Event news report
     */
    public function event_summary()
    {
        $filter = $_GET;
        $export = get_url_param('export');

        if (!empty($filter)) {
            $reports = $this->reportTppModel->getReportEventSummary($filter);

            $reportData = [];
            foreach ($reports as $key => $datum) {
                $keyMonthYear = format_date($datum['booking_news_date'], 'F Y');
                $keyType = $datum['type'];
                $reportData[$keyMonthYear][$keyType][$key] = $datum;
            }
        } else {
            $reportData = [];
        }

        if (!empty($export)) {
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=event_summary.xls");
            $this->load->view('report_tpp/_plain_event_summary', [
                'reportData' => $reportData
            ]);
        } else {
            $data = [
                'title' => "Event Summary",
                'page' => "report_tpp/event_summary",
                'reportData' => $reportData
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Shipping line stock report
     */
    public function shipping_line_stock()
    {
        $filter = $_GET;
        $export = get_url_param('export');
        $file_date = empty(get_url_param('stock_date')) ?
            (new DateTime())->format('d-m-Y') :
            format_date(get_url_param('stock_date'), 'd-m-Y');

        $shippingLines = $this->peopleModel->getByType(PeopleModel::$TYPE_SHIPPING_LINE);
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();

        $this->logHistory->create([
            'id_branch' => $branch['id'],
            'type' => 'REPORT TPP SHIPPING LINE',
            'data' => [
                'user_id' => $userData['id'],
                'name' => $userData['name'],
                'username' => $userData['username'],
                'access' => $branch['branch'],
            ]
        ]);

        if (!empty($export)) {
            $shippingLineStock = $this->reportTppModel->getReportShippingLineStock($filter);
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment;filename=shipping_line_stock_" . $file_date . ".xls");
            $this->load->view('report_tpp/_plain_shipping_line_stock', [
                'shippingLineStock' => $shippingLineStock
            ]);
        } else {
            $data = [
                'title' => "Shipping Line",
                'page' => "report_tpp/shipping_line_stock",
                'shippingLines' => $shippingLines
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable.
     */
    public function shipping_line_stock_data()
    {
        $filters = array_merge(get_url_param('filter_activity') ? $_GET : [], [
            'start' => get_url_param('start', $this->input->post('start')),
            'length' => get_url_param('length', $this->input->post('length')),
            'search' => get_url_param('search', $this->input->post('search'))['value'],
            'order_by' => get_url_param('order', $this->input->post('order'))[0]['column'],
            'order_method' => get_url_param('order', $this->input->post('order'))[0]['dir'],
        ]);
        $shippingLine = $this->reportTppModel->getReportShippingLineStock($filters);

        header('Content-Type: application/json');
        echo json_encode($shippingLine);
    }

    /**
     * Show report deferred tps
     */
    public function deferred_tps()
    {
        $report = empty(get_url_param('filter')) ? [] : $this->reportTps->getReportDeferredTPS($_GET);

        if (get_url_param('export')) {
            $data = [
                'reports' => $report,
                'filters' => $_GET,
                'tps' => if_empty($this->peopleModel->getById(get_if_exist($_GET, 'tps')), [])
            ];
            $this->reportTps->exportReportDeferredTPS($data, true);
        } else {
            $data = [
                'title' => "TPS",
                'page' => "report_tpp/deferred_tps",
                'reports' => $report,
                'tps' => $this->peopleModel->getByType([PeopleModel::$TYPE_TPS]),
            ];
            $this->load->view('template/layout', $data);
        }
    }
}
