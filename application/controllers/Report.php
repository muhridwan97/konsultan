<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Report
 * @property BranchModel $branch
 * @property ReportModel $reportModel
 * @property ReportBCModel $reportBC
 * @property ReportStockModel $reportStock
 * @property ReportMovementModel $reportMovement
 * @property ReportInvoiceModel $reportInvoice
 * @property ReportChartModel $reportChartModel
 * @property ReportServiceTimeModel $reportServiceTime
 * @property ReportPlanRealizationModel $reportPlanRealization
 * @property ReportHeavyEquipmentModel $reportHeavyEquipment
 * @property PeopleModel $peopleModel
 * @property GoodsModel $goodsModel
 * @property ContainerModel $containerModel
 * @property BookingModel $booking
 * @property BookingControlModel $bookingControl
 * @property BookingControlStatusModel $bookingControlStatus
 * @property BookingContainerModel $bookingContainer
 * @property BookingGoodsModel $bookingGoods
 * @property HandlingTypeModel $handlingType
 * @property SafeConductModel $safeConduct
 * @property SafeConductContainerModel $safeConductContainer
 * @property SafeConductGoodsModel $safeConductGoods
 * @property SafeConductRouteModel $safeConductRoute
 * @property WarehouseModel $warehouse
 * @property WorkOrderModel $workOrder
 * @property WorkOrderContainerModel $workOrderContainer
 * @property WorkOrderGoodsModel $workOrderGoods
 * @property WorkOrderOvertimeChargeModel $workOrderOvertime
 * @property HandlingModel $handling
 * @property HandlingContainerModel $handlingContainer
 * @property HandlingGoodsModel $handlingGoods
 * @property HeavyEquipmentModel $heavyEquipment
 * @property HeavyEquipmentEntryPermitModel $heavyEquipmentEntryPermit
 * @property ReportForkliftModel $reportForklift
 * @property ReportPerformanceModel $reportPerformance
 * @property ReportTransporterModel $reportTransporter
 * @property TransporterEntryPermitModel $transporterEntryPermit
 * @property Exporter $exporter
 * @property ReportPalletModel $reportPallet
 * @property HolidayModel $holiday
 * @property VehicleModel $vehicle
 * @property TargetModel $target
 * @property OpnameSpaceBookingInModel $opnameSpaceBookingIn
 * @property OpnameSpaceCheckHistoryModel $opnameSpaceCheckHistory
 * @property ReportDocumentProductionModel $reportDocumentProductionHistory
 * @property PaymentModel $payment
 */
class Report extends CI_Controller
{
    /**
     * Report constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ReportModel', 'reportModel');
        $this->load->model('ReportBCModel', 'reportBC');
        $this->load->model('ReportStockModel', 'reportStock');
        $this->load->model('ReportMovementModel', 'reportMovement');
        $this->load->model('ReportInvoiceModel', 'reportInvoice');
        $this->load->model('ReportChartModel', 'reportChartModel');
        $this->load->model('PeopleModel', 'peopleModel');
        $this->load->model('GoodsModel', 'goodsModel');
        $this->load->model('AssemblyModel', 'assemblyModel');
        $this->load->model('AssemblyGoodsModel', 'assemblyGoodsModel');
        $this->load->model('ContainerModel', 'containerModel');
        $this->load->model('HandlingTypeModel', 'handlingType');
        $this->load->model('InvoiceModel', 'invoice');
        $this->load->model('UnitModel', 'unitModel');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingTypeModel', 'bookingType');
        $this->load->model('BookingControlModel', 'bookingControl');
        $this->load->model('BookingControlStatusModel', 'bookingControlStatus');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('ComplainModel', 'complain');
        $this->load->model('SafeConductModel', 'safeConduct');
        $this->load->model('SafeConductContainerModel', 'safeConductContainer');
        $this->load->model('SafeConductGoodsModel', 'safeConductGoods');
        $this->load->model('SafeConductRouteModel', 'safeConductRoute');
        $this->load->model('HandlingModel', 'handling');
        $this->load->model('HandlingContainerModel', 'handlingContainer');
        $this->load->model('HandlingGoodsModel', 'handlingGoods');
        $this->load->model('HeavyEquipmentModel', 'heavyEquipment');
        $this->load->model('HeavyEquipmentEntryPermitModel', 'heavyEquipmentEntryPermit');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('WorkOrderContainerModel', 'workOrderContainer');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('WorkOrderOvertimeChargeModel', 'workOrderOvertime');
        $this->load->model('WarehouseModel', 'warehouse');
        $this->load->model('TrackerModel', 'trackerModel');
        $this->load->model('LogModel', 'logHistory');
        $this->load->model('UploadModel', 'uploadModel');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('UploadDocumentModel', 'uploadDocument');
        $this->load->model('ReportComplianceModel', 'reportCompliance');
        $this->load->model('ReportAdminSiteModel', 'reportAdminSite');
        $this->load->model('ReportServiceTimeModel', 'reportServiceTime');
        $this->load->model('ReportPlanRealizationModel', 'reportPlanRealization');
        $this->load->model('ReportForkliftModel', 'reportForklift');
        $this->load->model('ReportPerformanceModel', 'reportPerformance');
        $this->load->model('ReportPlanRealizationModel', 'reportPlanRealization');
        $this->load->model('ReportTransporterModel', 'reportTransporter');
        $this->load->model('ReportHeavyEquipmentModel', 'reportHeavyEquipment');
        $this->load->model('BranchModel', 'branch');
        $this->load->model('TransporterEntryPermitModel', 'transporterEntryPermit'); 
        $this->load->model('TransporterEntryPermitContainerModel', 'transporterEntryPermitContainer');
        $this->load->model('ReportPalletModel', 'reportPallet');
        $this->load->model('HolidayModel', 'holiday');
        $this->load->model('VehicleModel', 'vehicle');
        $this->load->model('TargetModel', 'target');
        $this->load->model('OpnameSpaceBookingInModel', 'opnameSpaceBookingIn');
        $this->load->model('OpnameSpaceCheckHistoryModel', 'opnameSpaceCheckHistory');
        $this->load->model('ReportDocumentProductionModel', 'reportDocumentProductionHistory');
        $this->load->model('PaymentModel', 'payment');
    }

    /**
     * Default index report
     */
    public function index()
    {
        redirect('report/in');
    }

    /**
     * In stock report
     */
    public function in()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_IN);

        $containerFilter = get_url_param('filter_container') ? $_GET : [];
        $goodsFilter = get_url_param('filter_goods') ? $_GET : [];

        // populate filter data
        $selectedContainers = key_exists('container', $containerFilter) ? $containerFilter['container'] : [0];
        $selectedGoods = key_exists('goods', $goodsFilter) ? $goodsFilter['goods'] : [0];
        $selectedOwners = key_exists('owner', $containerFilter) ? $containerFilter['owner'] : [0];
        if (!isset($selectedOwners[0]) || empty($selectedOwners[0])) {
            $selectedOwners = key_exists('owner', $goodsFilter) ? $goodsFilter['owner'] : [0];
        }

        // get filter data
        $owners = $this->peopleModel->getById($selectedOwners);
        $containers = $this->containerModel->getById($selectedContainers);
        $goods = $this->goodsModel->getById($selectedGoods);

        // container report
        $containerSummary = $this->reportModel->getActivityContainerSummary('INBOUND', $containerFilter);
        $reportContainerTotals = $containerSummary['total_transaction'] ?? 0;
        $reportContainer20 = $containerSummary['total_20'] ?? 0;
        $reportContainer40 = $containerSummary['total_40'] ?? 0;
        $reportContainer45 = $containerSummary['total_45'] ?? 0;
        $reportContainerChart = $this->reportChartModel->getReportChartActivityContainer($containerFilter);

        // goods report
        $goodsSummary = $this->reportModel->getActivityGoodsSummary('INBOUND', $goodsFilter);
        $reportGoodsTotals = $goodsSummary['total_transaction'] ?? 0;
        $reportGoodsQuantity = $goodsSummary['total_quantity'] ?? 0;
        $reportGoodsWeight = $goodsSummary['total_weight'] ?? 0;
        $reportGoodsVolume = $goodsSummary['total_volume'] ?? 0;
        $reportGoodsChart = $this->reportChartModel->getReportChartActivityGoods($goodsFilter);

        $export = get_url_param('export');
        if (!empty($export)) {
            if ($export == 'CONTAINER') {
                $report = $this->reportModel->getReportActivityContainer('INBOUND', $containerFilter);
            } else {
                $report = $this->reportModel->getReportActivityGoods('INBOUND', $goodsFilter);
            }
            foreach ($report as $index => &$data) {
                unset($report[$index]['id_booking_in']);
                unset($report[$index]['no_booking_in']);
                unset($report[$index]['no_reference_in']);
                unset($report[$index]['reference_date_in']);
                unset($report[$index]['booking_type_in']);

                if ($export == 'GOODS') {
                    $data['quantity'] = numerical($data['quantity'], 3, true);
                    $data['unit_weight'] = numerical($data['unit_weight'], 3, true);
                    $data['total_weight'] = numerical($data['total_weight'], 3, true);
                    $data['unit_gross_weight'] = numerical($data['unit_gross_weight'], 3, true);
                    $data['total_gross_weight'] = numerical($data['total_gross_weight'], 3, true);
                    $data['unit_volume'] = numerical($data['unit_volume']);
                    $data['total_volume'] = numerical($data['total_volume']);
                }
            }
            $this->exporter->exportLargeResourceFromArray('Inbound ' . $export, $report);
        } else {
            $data = [
                'title' => "Report In",
                'page' => "report/inbound",
                'owners' => $owners,
                'goods' => $goods,
                'containers' => $containers,
                'reportContainerTotals' => $reportContainerTotals,
                'reportContainer20' => $reportContainer20,
                'reportContainer40' => $reportContainer40,
                'reportContainer45' => $reportContainer45,
                'reportContainerChart' => $reportContainerChart,
                'reportGoodsTotals' => $reportGoodsTotals,
                'reportGoodsQuantity' => $reportGoodsQuantity,
                'reportGoodsWeight' => $reportGoodsWeight,
                'reportGoodsVolume' => $reportGoodsVolume,
                'reportGoodsChart' => $reportGoodsChart,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable inbound outbound container.
     *
     * @param $type
     */
    public function activity_container_data($type)
    {
        $filters = array_merge(get_url_param('filter_container') ? $_GET : [], [
            'start' => get_url_param('start', $this->input->post('start')),
            'length' => get_url_param('length', $this->input->post('length')),
            //'search' => get_url_param('search', $this->input->post('search'))['value'],
            'order_by' => get_url_param('order', $this->input->post('order'))[0]['column'],
            'order_method' => get_url_param('order', $this->input->post('order'))[0]['dir'],
        ]);
        $reportContainers = $this->reportModel->getReportActivityContainer($type, $filters);

        header('Content-Type: application/json');
        echo json_encode($reportContainers);
    }

    /**
     * Get ajax datatable inbound outbound goods.
     *
     * @param $type
     */
    public function activity_goods_data($type)
    {
        $filters = array_merge(get_url_param('filter_goods') ? $_GET : [], [
            'start' => get_url_param('start', $this->input->post('start')),
            'length' => get_url_param('length', $this->input->post('length')),
            //'search' => get_url_param('search', $this->input->post('search'))['value'],
            'order_by' => get_url_param('order', $this->input->post('order'))[0]['column'],
            'order_method' => get_url_param('order', $this->input->post('order'))[0]['dir'],
        ]);

        if ($type == 'INBOUND' || UserModel::authenticatedUserData('user_type') == 'INTERNAL') {
            $reportGoods = $this->reportModel->getReportActivityGoods($type, $filters);
        } else {
            $reportGoods = $this->reportModel->getReportActivityGoodsExternal($type, $filters);
        }

        header('Content-Type: application/json');
        echo json_encode($reportGoods);
    }

    /**
     * Get complain report
     */
    public function complain()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_COMPLAIN);

        $complainFilter = get_url_param('filter_complain') ? $_GET : [];
        $selectedCustomers = key_exists('customer', $complainFilter) ? $complainFilter['customer'] : [0];
        $customers = $this->peopleModel->getById($selectedCustomers);
        $complains = $this->reportModel->getReportComplain($complainFilter);

        $export = get_url_param('export');
        if (!empty($export)) {
            $reportComplains = $this->reportModel->getReportComplain($complainFilter);
            $this->exporter->exportFromArray('Complain', $reportComplains);
        } else {
            $data = [
                'title' => "Complain",
                'page' => "report/complain",
                'complains' => $complains,
                'customers' => $customers
            ];
            $this->load->view('template/layout', $data);
        }
    }

      /**
     * Get over capacity report
     */
    public function over_capacity()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_OVER_CAPACITY);
        if(get_url_param('filter_over_capacity')){
            $overCapacityFilter = get_url_param('filter_over_capacity') ? $_GET : [];
            $selectedCustomers = key_exists('customer', $overCapacityFilter) ? $overCapacityFilter['customer'] : [0];
            $customers = $this->peopleModel->getById($selectedCustomers);
            $overCapacities = $this->workOrder->getOverCapacity($overCapacityFilter);
    
            // $filterContainer['data'] = 'stock';
            // $reportContainers = $this->reportStock->getStockContainers($filterContainer);
            // $id_booking_containers = array_unique(array_column($reportContainers, 'id_booking'));
            $filterGoods['data'] = 'stock';
            $reportGoods = $this->reportStock->getStockGoods($filterGoods);
            $id_booking_goods = array_unique(array_column($reportGoods, 'id_booking'));
            $resultFilters = [];
            $totalSpace = [];
            $lastUpdated = "2000-06-05 13:18:17";
            //filter untuk yg ada stock aja
            foreach($overCapacities as $overCapacity){
                if(in_array($overCapacity['id_booking'], $id_booking_goods) ){
                    $resultFilters[] = $overCapacity;
                }
            }
            $arrayBooking = array_column($resultFilters,'id_booking');
            $opnameSpace = $this->opnameSpaceBookingIn->getReportOpnameSpace([
                'booking' => $arrayBooking,
            ]);
            
            foreach($resultFilters As &$result){
                $filterGoods['booking'] = $result['id_booking'];
                // $filterContainer['booking'] = $result['id_booking'];
                $goods = $this->reportStock->getStockGoods($filterGoods);
                // $container = $this->reportStock->getStockContainers($filterGoods);
                // $goods = $this->workOrderGoods->getWorkOrderGoodsByBookingId($result['id_booking']);
                $result['detail_goods'] = $goods;
                $result['space_check'] = '';
                $result['space_diff'] = '';
                $result['sum_space_cal'] = '';
                // $result['detail_container'] = $container;
                if(strtotime($result['updated_at'])>$lastUpdated){
                    $lastUpdated = $result['updated_at'];
                }
                foreach($opnameSpace as $space){
                    if($result['id_booking'] == $space['id_booking']){
                        $result['space_check'] = $space['space_check'];
                        $result['space_diff'] = $space['space_diff'];
                        $result['sum_space_cal'] = $result['sum_space']+$space['space_diff'];
                    }
                }
            }
            $customer_name = array_column($resultFilters, 'customer_name');
            array_multisort($customer_name, SORT_ASC, $resultFilters);
            $customer_name = array_column($customers, 'name');
            array_multisort($customer_name, SORT_ASC, $customers);
            $multiOverCapacities = [];
            $multiSpace = [];
            $multiLastUpdate = [];
            $tempCustomer = '';
            $arrayCustomer = [];
            $arraySpace = [];
            $tempLastUpdate = "2000-06-05 13:18:17";
            $kunci = array_keys($resultFilters);
            foreach ($resultFilters as $key=>$hasil) {
                if($tempCustomer!=$hasil['customer_name']){
                    if($tempCustomer!=''){
                        $multiOverCapacities [] = $arrayCustomer;
                        $multiSpace [] = $arraySpace;
                        $multiLastUpdate [] = $tempLastUpdate;
                    }
                    $tempCustomer = $hasil['customer_name'];
                    $arrayCustomer = [];
                    $arraySpace = [];
                    $tempLastUpdate = "2000-06-05 13:18:17";
                    $arrayCustomer [] = $hasil;
                    $arraySpace[] = if_empty($hasil['sum_space_cal'], $hasil['sum_space']);
                    if(strtotime($hasil['updated_at'])>$tempLastUpdate){
                        $tempLastUpdate = $hasil['updated_at'];
                    }
                }else{
                    $arrayCustomer [] = $hasil;
                    $arraySpace[] = if_empty($hasil['sum_space_cal'], $hasil['sum_space']);
                    if(strtotime($hasil['updated_at'])>$tempLastUpdate){
                        $tempLastUpdate = $hasil['updated_at'];
                    }
                }
                if ($key == end($kunci)){
                    $multiOverCapacities [] = $arrayCustomer;
                    $multiSpace [] = $arraySpace;
                    $multiLastUpdate [] = $tempLastUpdate;
                }
            }
            $export = $this->input->get('export');
            if($export && !empty($multiOverCapacities)){
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $kolom = 1;
                $baris = 1;
                foreach($multiOverCapacities as $key => $multiOverCapacity){
                    $sheet->setCellValueByColumnAndRow($kolom, $baris++, $customers[$key]['name']);
                    $sheet->setCellValueByColumnAndRow($kolom, $baris++, "Updated ".date('d-F-Y',strtotime($multiLastUpdate[$key])));
                    $sheet->setCellValueByColumnAndRow($kolom, $baris++, "Total Capacity     : ".number_format($customers[$key]['contract'],2)." m2");
                    $sheet->setCellValueByColumnAndRow($kolom, $baris++, "Utilitas Spaces    : ".array_sum($multiSpace[$key])." m2");
                    $persen = (number_format($customers[$key]['contract'],2)==0)? number_format($customers[$key]['contract'],2): number_format( array_sum($multiSpace[$key])/$customers[$key]['contract'] * 100,2);
                    $sheet->setCellValueByColumnAndRow($kolom, $baris++, "Occupancy Rate (%) : ".$persen." %");
                    $barisTabel = $baris;
                    $sheet->setCellValueByColumnAndRow($kolom++, $baris, "No");
                    $sheet->setCellValueByColumnAndRow($kolom++, $baris, "No Reference");
                    $sheet->setCellValueByColumnAndRow($kolom++, $baris, "Total Space");
                    $sheet->setCellValueByColumnAndRow($kolom++, $baris, "Ex No Container");
                    $sheet->setCellValueByColumnAndRow($kolom++, $baris, "No Goods");
                    $sheet->setCellValueByColumnAndRow($kolom, $baris++, "Goods Name");
                    $kolomTabel = $kolom;
                    $kolom = 1;
                    $number = 0;
                    foreach ($multiOverCapacity as $overCapacity){
                        $countData = count($overCapacity['detail_goods']) ;
                        $countGoods = !empty($overCapacity['detail_goods']) && $countData > 1 ? $countData = $countData-1 : 0;

                        $sheet->setCellValueByColumnAndRow($kolom, $baris, $number = $number+1);
                        $sheet->mergeCellsByColumnAndRow($kolom,$baris,$kolom,$baris+$countGoods);
                        $kolom++;
                        $sheet->setCellValueByColumnAndRow($kolom, $baris, $overCapacity['no_reference_inbound']);
                        $sheet->mergeCellsByColumnAndRow($kolom,$baris,$kolom,$baris+$countGoods);
                        $kolom++;
                        $sheet->setCellValueByColumnAndRow($kolom, $baris, if_empty($overCapacity['sum_space_cal'], $overCapacity['sum_space']). " m2");
                        $sheet->mergeCellsByColumnAndRow($kolom,$baris,$kolom,$baris+$countGoods);
                        $kolom++;
                        if (!empty($countData)){
                            foreach($overCapacity['detail_goods'] AS $detail){
                                $kolomGoods = $kolom;
                                $sheet->setCellValueByColumnAndRow($kolomGoods++, $baris, if_empty($detail['ex_no_container'], '-'));    
                                $sheet->setCellValueByColumnAndRow($kolomGoods++, $baris, if_empty($detail['no_goods'], '-'));
                                $sheet->setCellValueByColumnAndRow($kolomGoods++, $baris, if_empty($detail['goods_name'], '-'));
                                $baris++;
                            }
                        }else{
                            $sheet->setCellValueByColumnAndRow($kolom++, $baris, "-");
                            $sheet->setCellValueByColumnAndRow($kolom++, $baris, "-");
                            $sheet->setCellValueByColumnAndRow($kolom, $baris, "-");
                            $baris++;
                        }
                        $kolom = 1;
                    }
                    $sheet->setCellValueByColumnAndRow(1, $baris, "Total");
                    $sheet->mergeCellsByColumnAndRow(1,$baris,2,$baris);
                    $sheet->setCellValueByColumnAndRow(3, $baris, array_sum($multiSpace[$key])." m2");
                    $sheet->mergeCellsByColumnAndRow(4,$baris,6,$baris);
                    $baris++;
                    $sheet
                        ->getStyleByColumnAndRow(1,$barisTabel,$kolomTabel,$baris-1)
                        ->applyFromArray([
                                'borders' => array(
                                    'allBorders' => array(
                                        'borderStyle' => Border::BORDER_THIN,
                                    ),
                                ),
                            ]
                        );
                    $baris++;
                }
                $sheet->getColumnDimension('A')->setWidth(4);
                foreach(range('B','F') as $columnID) {
                    $sheet->getColumnDimension($columnID)
                        ->setAutoSize(true);
                }
                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="Report Over Space.xlsx"');
                $writer->save('php://output');

            }else{
                $data = [
                    'title' => "Over Capacity",
                    'page' => "report/over_capacity",
                    'overCapacities' => $resultFilters,
                    'customers' => $customers,
                    'totalSpace' => $totalSpace,
                    'lastUpdated' => $lastUpdated,
                    'multiOverCapacities' => $multiOverCapacities,
                    'multiSpace' => $multiSpace,
                    'multiLastUpdate' => $multiLastUpdate,
                ];
                $this->load->view('template/layout', $data);
            }
            
        }else{
            $data = [
                'title' => "Over Capacity",
                'page' => "report/over_capacity",
            ];
            $this->load->view('template/layout', $data);
        }        
    }

    /**
     * Get over capacity report
     */
    public function over_capacity_detail()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_OVER_CAPACITY);
        $no_reference = $this->input->get('no_reference');
        $id_booking = $this->input->get('bookingId');
        $filters = [
            'booking' => $id_booking,
        ];

        $opnameSpace = $this->opnameSpaceBookingIn->getReportOpnameSpace([
            'booking' => $id_booking,
        ]);
        $checkHistory = $this->opnameSpaceBookingIn->getBy([
            'opname_space_booking_in.id_booking' => $id_booking,
            'opname_spaces.status' => OpnameSpaceModel::STATUS_VALIDATED,
            ]);
        $workOrders = $this->workOrder->getOverCapacityDetail($filters);

        $date = array_column($checkHistory, 'opname_space_date');
        array_multisort($date, SORT_DESC, $checkHistory);

        $data = [
            'title' => "Over Capacity Detail " .$workOrders[0]['customer_name'],
            'page' => "report/over_capacity_detail",
            'workOrders' => $workOrders,
            'opnameSpace' => $opnameSpace,
            'checkHistory' => $checkHistory,
        ];
        $this->load->view('template/layout', $data);

    }

      /**
     * Get over capacity report
     */
    public function over_capacity_opname()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_SPACES_VALIDATE);
        if(get_url_param('filter_over_capacity')){
            $textPersen = '';
            $overCapacityFilter = get_url_param('filter_over_capacity') ? $_GET : [];
            $selectedCustomers = key_exists('customer', $overCapacityFilter) ? $overCapacityFilter['customer'] : [0];
            if(key_exists('opname', $overCapacityFilter)){
                $id_opname = $overCapacityFilter['opname'];
                $selectedCustomers = $this->opnameSpaceBookingIn->getOpnameSpaceCustomerById($id_opname);
                $selectedCustomers = array_column($selectedCustomers,'id_customer');
                $overCapacityFilter['customer'] = $selectedCustomers;

                $cekPersen = $this->opnameSpaceBookingIn->cekPersen($id_opname);
                foreach ($cekPersen as $index => $persen) {
                    if($index==0){
                        $textPersen.= $persen['no_reference'];
                    }else{
                        $textPersen.= ", ".$persen['no_reference'];
                    }
                }
            }
            $customers = $this->peopleModel->getById($selectedCustomers);
            $overCapacities = $this->workOrder->getOverCapacity($overCapacityFilter);
    
            // $filterContainer['data'] = 'stock';
            // $reportContainers = $this->reportStock->getStockContainers($filterContainer);
            // $id_booking_containers = array_unique(array_column($reportContainers, 'id_booking'));
            $filterGoods['data'] = 'stock';
            $reportGoods = $this->reportStock->getStockGoods($filterGoods);
            $id_booking_goods = array_unique(array_column($reportGoods, 'id_booking'));
            $resultFilters = [];
            $totalSpace = [];
            $lastUpdated = "2000-06-05 13:18:17";
            //filter untuk yg ada stock aja
            foreach($overCapacities as $overCapacity){
                if(in_array($overCapacity['id_booking'], $id_booking_goods) ){
                    $resultFilters[] = $overCapacity;
                }
            }
            $arrayBooking = array_column($resultFilters,'id_booking');
            $opnameSpace = $this->opnameSpaceBookingIn->getOpnameSpaceReview([
                'booking' => $arrayBooking,
            ]);
            
            foreach($resultFilters As &$result){
                $filterGoods['booking'] = $result['id_booking'];
                // $filterContainer['booking'] = $result['id_booking'];
                $goods = $this->reportStock->getStockGoods($filterGoods);
                // $container = $this->reportStock->getStockContainers($filterGoods);
                // $goods = $this->workOrderGoods->getWorkOrderGoodsByBookingId($result['id_booking']);
                $result['detail_goods'] = $goods;
                $result['space_check'] = '';
                $result['space_diff'] = '';
                $result['sum_space_cal'] = '';
                // $result['detail_container'] = $container;
                if(strtotime($result['updated_at'])>$lastUpdated){
                    $lastUpdated = $result['updated_at'];
                }
                foreach($opnameSpace as $space){
                    if($result['id_booking'] == $space['id_booking']){
                        $result['space_check'] = $space['space_check'];
                        $result['space_diff'] = $space['space_diff'];
                        $result['sum_space_cal'] = $result['sum_space']+$space['space_diff'];
                    }
                }
            }
            $customer_name = array_column($resultFilters, 'customer_name');
            array_multisort($customer_name, SORT_ASC, $resultFilters);
            $customer_name = array_column($customers, 'name');
            array_multisort($customer_name, SORT_ASC, $customers);
            $multiOverCapacities = [];
            $multiSpace = [];
            $multiLastUpdate = [];
            $tempCustomer = '';
            $arrayCustomer = [];
            $arraySpace = [];
            $tempLastUpdate = "2000-06-05 13:18:17";
            $kunci = array_keys($resultFilters);
            foreach ($resultFilters as $key=>$hasil) {
                if($tempCustomer!=$hasil['customer_name']){
                    if($tempCustomer!=''){
                        $multiOverCapacities [] = $arrayCustomer;
                        $multiSpace [] = $arraySpace;
                        $multiLastUpdate [] = $tempLastUpdate;
                    }
                    $tempCustomer = $hasil['customer_name'];
                    $arrayCustomer = [];
                    $arraySpace = [];
                    $tempLastUpdate = "2000-06-05 13:18:17";
                    $arrayCustomer [] = $hasil;
                    $arraySpace[] = if_empty($hasil['sum_space_cal'], $hasil['sum_space']);
                    if(strtotime($hasil['updated_at'])>$tempLastUpdate){
                        $tempLastUpdate = $hasil['updated_at'];
                    }
                }else{
                    $arrayCustomer [] = $hasil;
                    $arraySpace[] = if_empty($hasil['sum_space_cal'], $hasil['sum_space']);
                    if(strtotime($hasil['updated_at'])>$tempLastUpdate){
                        $tempLastUpdate = $hasil['updated_at'];
                    }
                }
                if ($key == end($kunci)){
                    $multiOverCapacities [] = $arrayCustomer;
                    $multiSpace [] = $arraySpace;
                    $multiLastUpdate [] = $tempLastUpdate;
                }
            }
            $export = $this->input->get('export');
            if($export && !empty($multiOverCapacities)){
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $kolom = 1;
                $baris = 1;
                foreach($multiOverCapacities as $key => $multiOverCapacity){
                    $sheet->setCellValueByColumnAndRow($kolom, $baris++, $customers[$key]['name']);
                    $sheet->setCellValueByColumnAndRow($kolom, $baris++, "Updated ".date('d-F-Y',strtotime($multiLastUpdate[$key])));
                    $sheet->setCellValueByColumnAndRow($kolom, $baris++, "Total Capacity     : ".number_format($customers[$key]['contract'],2)." m2");
                    $sheet->setCellValueByColumnAndRow($kolom, $baris++, "Utilitas Spaces    : ".array_sum($multiSpace[$key])." m2");
                    $persen = (number_format($customers[$key]['contract'],2)==0)? number_format($customers[$key]['contract'],2): number_format( array_sum($multiSpace[$key])/$customers[$key]['contract'] * 100,2);
                    $sheet->setCellValueByColumnAndRow($kolom, $baris++, "Occupancy Rate (%) : ".$persen." %");
                    $barisTabel = $baris;
                    $sheet->setCellValueByColumnAndRow($kolom++, $baris, "No");
                    $sheet->setCellValueByColumnAndRow($kolom++, $baris, "No Reference");
                    $sheet->setCellValueByColumnAndRow($kolom++, $baris, "Total Space");
                    $sheet->setCellValueByColumnAndRow($kolom++, $baris, "Ex No Container");
                    $sheet->setCellValueByColumnAndRow($kolom++, $baris, "No Goods");
                    $sheet->setCellValueByColumnAndRow($kolom, $baris++, "Goods Name");
                    $kolomTabel = $kolom;
                    $kolom = 1;
                    $number = 0;
                    foreach ($multiOverCapacity as $overCapacity){
                        $countData = count($overCapacity['detail_goods']) ;
                        $countGoods = !empty($overCapacity['detail_goods']) && $countData > 1 ? $countData = $countData-1 : 0;

                        $sheet->setCellValueByColumnAndRow($kolom, $baris, $number = $number+1);
                        $sheet->mergeCellsByColumnAndRow($kolom,$baris,$kolom,$baris+$countGoods);
                        $kolom++;
                        $sheet->setCellValueByColumnAndRow($kolom, $baris, $overCapacity['no_reference_inbound']);
                        $sheet->mergeCellsByColumnAndRow($kolom,$baris,$kolom,$baris+$countGoods);
                        $kolom++;
                        if($overCapacity['sum_space']==0){
                            $sheet->setCellValueByColumnAndRow($kolom, $baris, (!empty($overCapacity['sum_space'])||!empty($overCapacity['sum_space_cal']))?(!empty($overCapacity['sum_space_cal'])? $overCapacity['sum_space_cal'] ." (".if_empty($overCapacity['sum_space'],0).")" : $overCapacity['sum_space']). " m2":'-');
                        }else{
                            $symbol = $overCapacity['space_diff']>0 ? '+' : '';
                            $sheet->setCellValueByColumnAndRow($kolom, $baris, (!empty($overCapacity['sum_space'])||!empty($overCapacity['sum_space_cal']))?(!empty($overCapacity['sum_space_cal'])? $overCapacity['sum_space_cal'] ." (".if_empty($overCapacity['sum_space'],0).")"." (".$symbol.numerical($overCapacity['space_diff']/abs(if_empty($overCapacity['sum_space'],0))*100,0)."%)" : $overCapacity['sum_space']). " m2":'-');
                        }
                        $sheet->mergeCellsByColumnAndRow($kolom,$baris,$kolom,$baris+$countGoods);
                        $kolom++;
                        if (!empty($countData)){
                            foreach($overCapacity['detail_goods'] AS $detail){
                                $kolomGoods = $kolom;
                                $sheet->setCellValueByColumnAndRow($kolomGoods++, $baris, if_empty($detail['ex_no_container'], '-'));    
                                $sheet->setCellValueByColumnAndRow($kolomGoods++, $baris, if_empty($detail['no_goods'], '-'));
                                $sheet->setCellValueByColumnAndRow($kolomGoods++, $baris, if_empty($detail['goods_name'], '-'));
                                $baris++;
                            }
                        }else{
                            $sheet->setCellValueByColumnAndRow($kolom++, $baris, "-");
                            $sheet->setCellValueByColumnAndRow($kolom++, $baris, "-");
                            $sheet->setCellValueByColumnAndRow($kolom, $baris, "-");
                            $baris++;
                        }
                        $kolom = 1;
                    }
                    $sheet->setCellValueByColumnAndRow(1, $baris, "Total");
                    $sheet->mergeCellsByColumnAndRow(1,$baris,2,$baris);
                    $sheet->setCellValueByColumnAndRow(3, $baris, array_sum($multiSpace[$key])." m2");
                    $sheet->mergeCellsByColumnAndRow(4,$baris,6,$baris);
                    $baris++;
                    $sheet
                        ->getStyleByColumnAndRow(1,$barisTabel,$kolomTabel,$baris-1)
                        ->applyFromArray([
                                'borders' => array(
                                    'allBorders' => array(
                                        'borderStyle' => Border::BORDER_THIN,
                                    ),
                                ),
                            ]
                        );
                    $baris++;
                }
                $sheet->getColumnDimension('A')->setWidth(4);
                foreach(range('B','F') as $columnID) {
                    $sheet->getColumnDimension($columnID)
                        ->setAutoSize(true);
                }
                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="Report Over Space.xlsx"');
                $writer->save('php://output');

            }else{
                $data = [
                    'title' => "Over Capacity",
                    'page' => "report/over_capacity_opname",
                    'overCapacities' => $resultFilters,
                    'customers' => $customers,
                    'totalSpace' => $totalSpace,
                    'lastUpdated' => $lastUpdated,
                    'multiOverCapacities' => $multiOverCapacities,
                    'multiSpace' => $multiSpace,
                    'multiLastUpdate' => $multiLastUpdate,
                ];
                if (!empty($textPersen)) {
                    flash('status10::10percent', "message10::Warning, opname differs by more than 10 percent 
                        here's the list : " . $textPersen);
                }
                $this->load->view('template/layout', $data);
            }
            
        }else{
            $data = [
                'title' => "Over Capacity",
                'page' => "report/over_capacity_opname",
            ];
            $this->load->view('template/layout', $data);
        }        
    }
    
    /**
     * Get over capacity opname detail
     */
    public function over_capacity_detail_opname()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_SPACES_VALIDATE);
        $no_reference = $this->input->get('no_reference');
        $id_booking = $this->input->get('bookingId');
        $filters = [
            'booking' => $id_booking,
        ];

        $opnameSpace = $this->opnameSpaceBookingIn->getOpnameSpaceReview([
            'booking' => $id_booking,
        ]);
        $checkHistory = $this->opnameSpaceBookingIn->getBy([
            'opname_space_booking_in.id_booking' => $id_booking,
            ]);
        $workOrders = $this->workOrder->getOverCapacityDetail($filters);

        $date = array_column($checkHistory, 'opname_space_date');
        array_multisort($date, SORT_DESC, $checkHistory);

        $data = [
            'title' => "Over Capacity Detail " .$workOrders[0]['customer_name'],
            'page' => "report/over_capacity_detail",
            'workOrders' => $workOrders,
            'opnameSpace' => $opnameSpace,
            'checkHistory' => $checkHistory,
        ];
        $this->load->view('template/layout', $data);

    }

    /**
     * transporter Usage report
     */
    public function transporter()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_TRANSPORTER);
        $filters = get_url_param('filter_transporter') ? $_GET : [];

        $allPeriods = [];
        if(!empty(get_url_param('year')) && get_url_param('year') != date('Y')){
            for ($i = 1; $i <= 53; $i++) {
                $allPeriods[] = $i;
            }
        }else{
            for ($i = 1; $i <= date('W'); $i++) {
                $allPeriods[] = $i;
            }
        }
        array_multisort($allPeriods, SORT_DESC);
        $periods = !empty(get_url_param('week')) ? [get_url_param('week')] : $allPeriods;

        $allVehicles = $this->vehicle->getAll();
        $allBranches = $this->branch->getAll();

        $vehicleConditions['status'] = get_url_param('status', 'ACTIVE');
        if (!empty(get_url_param('vehicle_type'))) $vehicleConditions['vehicle_type'] = get_url_param('vehicle_type');
        if (!empty(get_url_param('vehicle'))) $vehicleConditions['no_plate'] = get_url_param('vehicle');
        if (!empty(get_url_param('branch')) && get_url_param('branch_data', 'VEHICLE') == 'VEHICLE') $vehicleConditions['id_branch'] = get_url_param('branch');
        $vehicles = $this->vehicle->getBy($vehicleConditions);

        $results = $this->reportTransporter->getReportTransporter($filters);
        $externals = $this->reportTransporter->getReportTransporterExternal($filters);
        $targets = $this->target->getTargetTransporter();
        $target_all = $targets[0]['target'];
        $target_branch = [];
        foreach($targets as $target){
            $target_branch[$target['id_branch']] = $target;
        }
        // group result data into week, we will match with table later.
        $groupWeeks = array();
        foreach ($results as $result) {
            $groupWeeks[$result['week']]['week'] = $result['week'];
            $groupWeeks[$result['week']]['total'] = 0;
            $groupWeeks[$result['week']]['total_external'] = 0;
            $result['percentage'] = 0;
            $groupWeeks[$result['week']]['vehicles'][] = $result;
        }
        $groupWeekExternals = array();
        foreach ($externals as $external) {
            $groupWeekExternals[$external['week']]['vehicles'][] = $external;
        }
        foreach ($groupWeeks as $week=>&$groupWeek) {
            $tempColumn = [];
            if(isset($groupWeekExternals[$week]['vehicles'])){
                $tempColumn = array_column($groupWeekExternals[$week]['vehicles'], 'total_safe_conduct');
            }
            $groupWeek['total'] = array_sum(array_column($groupWeek['vehicles'], 'total_safe_conduct'));
            if(empty($tempColumn)){
                $groupWeek['total_external'] = 0;
            }else{
                $groupWeek['total_external'] = array_sum($tempColumn);
            }

            foreach ($groupWeek['vehicles'] as &$vehicle) {
                $tempTarget = isset($target_branch[$vehicle['id_branch_vms']]['target_branch']) ? $target_branch[$vehicle['id_branch_vms']]['target_branch'] : $target_all;
                $tempTarget = $tempTarget==0 ? 1 : $tempTarget;
                $vehicle['percentage'] = $vehicle['total_safe_conduct'] / $groupWeek['total'] * 100;
                $vehicle['prod'] = $vehicle['total_safe_conduct'] / $tempTarget * 100;
            }
        }

        $target_all = $this->target->getTargetTransporter();

        if ($this->input->get('export')) {
            $this->reportTransporter->exportTransporter($groupWeeks, $periods, $vehicles);
        } else {
            $data = [
                'title' => "Report Transporter",
                'page' => "report/transporter",
                'allPeriods' => $allPeriods,
                'allVehicles' => $allVehicles,
                'allBranches' => $allBranches,
                'vehicles' => $vehicles,
                'periods' => $periods,
                'reports' => $groupWeeks,
                'target_all' => $target_all,
            ];
            $this->load->view('template/layout', $data);
        }
    }

     /**
     * Get transporter detail report
     */
    public function transporter_detail()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_TRANSPORTER);

        $details = $this->reportTransporter->getReportTransporterDetail([
            'year' => get_url_param('year'),
            'week' => get_url_param('week'),
            'vehicle' => get_url_param('vehicle'),
            'branch' => get_url_param('branch'),
            'branch_data' => get_url_param('branch_data', 'VEHICLE'),
            'activity_type' => get_url_param('activity_type'),
        ]);

        foreach ($details as $index => &$detail) {
            $detail['total_row'] = if_empty(count(array_filter($details, function ($safeConduct) use($detail) {
                return !empty($detail['id_safe_conduct_group']) && $detail['id_safe_conduct_group'] == $safeConduct['id_safe_conduct_group'];
            })), 1);
        }

        $data = [
            'title' => "Transporter Detail",
            'page' => "report/transporter_detail",
            'details' => $details,
        ];
        $this->load->view('template/layout', $data);
    }

     /**
     * Get transporter external detail report
     */
    public function transporter_external_detail()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_TRANSPORTER);

        $details = $this->reportTransporter->getReportTransporterExternalDetail([
            'year' => get_url_param('year'),
            'week' => get_url_param('week'),
            'branch' => get_url_param('branch'),
            'activity_type' => get_url_param('activity_type'),
        ]);

        foreach ($details as $index => &$detail) {
            $detail['total_row'] = if_empty(count(array_filter($details, function ($tep) use($detail) {
                return !empty($detail['id_transporter_entry_permit']) && $detail['id_transporter_entry_permit'] == $tep['id_transporter_entry_permit'];
            })), 1);
        }
        $tepId = array_column($details, 'id_transporter_entry_permit');
        array_multisort($tepId, SORT_DESC, $details);

        $data = [
            'title' => "Transporter External Detail",
            'page' => "report/transporter_external_detail",
            'details' => $details,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Out stock report
     */
    public function out()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_OUT);

        $containerFilter = get_url_param('filter_container') ? $_GET : [];
        $goodsFilter = get_url_param('filter_goods') ? $_GET : [];

        // populate filter data
        $selectedContainers = key_exists('container', $containerFilter) ? $containerFilter['container'] : [0];
        $selectedGoods = key_exists('goods', $goodsFilter) ? $goodsFilter['goods'] : [0];
        $selectedOwners = key_exists('owner', $containerFilter) ? $containerFilter['owner'] : [0];
        if (!isset($selectedOwners[0]) || empty($selectedOwners[0])) {
            $selectedOwners = key_exists('owner', $goodsFilter) ? $goodsFilter['owner'] : [0];
        }

        // get filter data
        $owners = $this->peopleModel->getById($selectedOwners);
        $containers = $this->containerModel->getById($selectedContainers);
        $goods = $this->goodsModel->getById($selectedGoods);

        // container report
        $containerSummary = $this->reportModel->getActivityContainerSummary('OUTBOUND', $containerFilter);
        $reportContainerTotals = $containerSummary['total_transaction'] ?? 0;
        $reportContainer20 = $containerSummary['total_20'] ?? 0;
        $reportContainer40 = $containerSummary['total_40'] ?? 0;
        $reportContainer45 = $containerSummary['total_45'] ?? 0;
        $reportContainerChart = $this->reportChartModel->getReportChartActivityContainer($containerFilter);

        // goods report
        $goodsSummary = $this->reportModel->getActivityGoodsSummary('OUTBOUND', $goodsFilter);
        $reportGoodsTotals = $goodsSummary['total_transaction'] ?? 0;
        $reportGoodsQuantity = $goodsSummary['total_quantity'] ?? 0;
        $reportGoodsWeight = $goodsSummary['total_weight'] ?? 0;
        $reportGoodsVolume = $goodsSummary['total_volume'] ?? 0;
        $reportGoodsChart = $this->reportChartModel->getReportChartActivityGoods($goodsFilter);

        $export = get_url_param('export');
        if (!empty($export)) {
            if($export == 'CONTAINER') {
                $report = $this->reportModel->getReportActivityContainer('OUTBOUND', $containerFilter);
            } else {
                if (UserModel::authenticatedUserData('user_type') == 'INTERNAL') {
                    $report = $this->reportModel->getReportActivityGoods('OUTBOUND', $goodsFilter);
                } else {
                    $report = $this->reportModel->getReportActivityGoodsExternal('OUTBOUND', $goodsFilter);
                }
            }
            foreach ($report as $index => &$data) {
                if($export == 'GOODS') {
                    $data['quantity'] = numerical($data['quantity'], 3, true);
                    $data['unit_weight'] = numerical($data['unit_weight'], 3, true);
                    $data['total_weight'] = numerical($data['total_weight'], 3, true);
                    $data['unit_gross_weight'] = numerical($data['unit_gross_weight'], 3, true);
                    $data['total_gross_weight'] = numerical($data['total_gross_weight'], 3, true);
                    $data['unit_volume'] = numerical($data['unit_volume']);
                    $data['total_volume'] = numerical($data['total_volume']);
                }
            }
            $this->exporter->exportLargeResourceFromArray('Outbound ' . $export, $report);
        } else {
            $data = [
                'title' => "Report Out",
                'page' => "report/outbound",
                'owners' => $owners,
                'goods' => $goods,
                'containers' => $containers,
                'reportContainerTotals' => $reportContainerTotals,
                'reportContainer20' => $reportContainer20,
                'reportContainer40' => $reportContainer40,
                'reportContainer45' => $reportContainer45,
                'reportContainerChart' => $reportContainerChart,
                'reportGoodsTotals' => $reportGoodsTotals,
                'reportGoodsQuantity' => $reportGoodsQuantity,
                'reportGoodsWeight' => $reportGoodsWeight,
                'reportGoodsVolume' => $reportGoodsVolume,
                'reportGoodsChart' => $reportGoodsChart,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * In progress report
     */
    public function in_progress()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_IN_PROGRESS);

        $inboundProgressFilter = get_url_param('filter_inbound_progress') ? $_GET : [];
        $selectedOwners = key_exists('owner', $inboundProgressFilter) ? $inboundProgressFilter['owner'] : [0];
        $owners = $this->peopleModel->getById($selectedOwners);
        $branches = $this->branch->getAll();

        $export = get_url_param('export');
        if (!empty($export)) {
            $reportInboundProgress = $this->reportModel->getReportUploadProgressInbound($inboundProgressFilter);
            foreach ($reportInboundProgress as $key => &$report) {
                $report['upload_date'] = format_date($report['upload_date']);
                $report['draft_date'] = format_date($report['draft_date']);
                $report['confirmation_date'] = format_date($report['confirmation_date']);
                $report['do_date'] = format_date($report['do_date']);
                $report['expired_do_date'] = format_date($report['expired_do_date']);
                $report['freetime_do_date'] = format_date($report['freetime_do_date']);
                $report['sppb_date'] = format_date($report['sppb_date']);
                $report['sppd_date'] = format_date($report['sppd_date']);
                $report['hardcopy_date'] = format_date($report['hardcopy_date']);
                unset($report['id_branch']);
                unset($report['id_customer']);
            }
            $this->exporter->exportLargeResourceFromArray($export, $reportInboundProgress);
        } else {
            $data = [
                'title' => "Report In Progress",
                'page' => "report/inbound_progress",
                'owners' => $owners,
                'branches' => $branches,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable inbound progress data.
     */
    public function inbound_progress_data()
    {
        $filters = array_merge(get_url_param('filter_inbound_progress') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);
        $reportInboundProgress = $this->reportModel->getReportUploadProgressInbound($filters);

        header('Content-Type: application/json');
        echo json_encode($reportInboundProgress);
    }

    /**
     * Out progress report
     */
    public function out_progress()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_OUT_PROGRESS);

        $outboundProgressFilter = get_url_param('filter_outbound_progress') ? $_GET : [];
        $selectedOwners = key_exists('owner', $outboundProgressFilter) ? $outboundProgressFilter['owner'] : [0];
        $owners = $this->peopleModel->getById($selectedOwners);
        $branches = $this->branch->getAll();

        $export = get_url_param('export');
        if (!empty($export)) {
            $reportOutboundProgress = $this->reportModel->getReportUploadProgressOutbound($outboundProgressFilter);
            foreach ($reportOutboundProgress as $key => &$report) {
                $report['upload_date'] = format_date($report['upload_date']);
                $report['draft_date'] = format_date($report['draft_date']);
                $report['confirmation_date'] = format_date($report['confirmation_date']);
                $report['billing_date'] = format_date($report['billing_date']);
                $report['bpn_date'] = format_date($report['bpn_date']);
                $report['sppb_date'] = format_date($report['sppb_date']);
                $report['sppf_date'] = format_date($report['sppf_date']);
                $report['sppd_date'] = format_date($report['sppd_date']);
                $report['sppd_in_date'] = format_date($report['sppd_in_date']);
                unset($report['id_branch']);
                unset($report['id_customer']);
            }
            $this->exporter->exportLargeResourceFromArray($export, $reportOutboundProgress);
        } else {
            $data = [
                'title' => "Report Out Progress",
                'page' => "report/outbound_progress",
                'owners' => $owners,
                'branches' => $branches,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable outbound progress data.
     */
    public function outbound_progress_data()
    {
        $filters = array_merge(get_url_param('filter_outbound_progress') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);
        $reportOutboundProgress = $this->reportModel->getReportUploadProgressOutbound($filters);

        header('Content-Type: application/json');
        echo json_encode($reportOutboundProgress);
    }

    /**
     * Admin site report
     */
    public function admin_site()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_ADMIN_SITE);

        $filter = get_url_param('filter_admin_site') ? $_GET : [];

        $selectedPIC = key_exists('pic', $filter) ? $filter['pic'] : [0];
        if (empty($selectedPIC)) {
            $selectedPIC = key_exists('pic', $filter) ? $filter['pic'] : [0];
        }
        
        $data_report = $this->reportAdminSite->getReportAdminSite();

        $allPIC = [];
        $createdBy = [];
        foreach($data_report as $report) {
            if(in_array($report['created_by'], $createdBy) == false){
                array_push($createdBy, $report['created_by']);
                $allPIC[] = ['id' => $report['created_by'], 'name' => $report['creator_name']];
            }
        }

        $pic = array_filter($allPIC, function($item) use( $selectedPIC ){

            $item_created_by = explode(",", $item['id']);
            if(is_array($selectedPIC)  && !empty(array_intersect($item_created_by, $selectedPIC)) ){
                return $item;
            }
        });

        $export = get_url_param('export');
        if (!empty($export)) {
            $report = $this->reportAdminSite->getReportAdminSite($filter);
            $this->exporter->exportFromArray($export, $report);
        } else {
            $data = [
                'title' => "Report Admin Site",
                'page' => "report/admin_site",
                'pic' => $pic,
                'data_report' => $data_report,
                'allPIC' => $allPIC,
            ];
        }

        $this->load->view('template/layout', $data);
    }

    /**
     * Get ajax datatable admin site data.
     * @param $type
     */
    public function admin_site_data()
    {
        $filter = get_url_param('filter_admin_site') ? $_GET : [];

        $startData = get_url_param('start', $this->input->post('start'));
        $lengthData = get_url_param('length', $this->input->post('length'));
        $searchQuery = get_url_param('search', $this->input->post('search'))['value'];
        $order = get_url_param('order', $this->input->post('order'));
        $orderColumn = $order[0]['column'];
        $orderColumnOrder = $order[0]['dir'];
        
        $report = $this->reportAdminSite->getReportAdminSite($filter, $startData, $lengthData, $searchQuery, $orderColumn, $orderColumnOrder);

        $no = $startData + 1;
        foreach ($report['data'] as &$row) {
            $row['no'] = $no++;
        }

        header('Content-Type: application/json');
        echo json_encode($report);
    }

    /**
     * Report invoice.
     */
    public function invoice()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_INVOICE);

        $filter = get_url_param('filter_invoice') ? $_GET : [];

        $selectedOwners = key_exists('owner', $filter) ? $filter['owner'] : [0];
        $owners = $this->peopleModel->getById($selectedOwners);

        $export = get_url_param('export');
        if (!empty($export)) {
            $reportInvoices = $this->reportInvoice->getReportInvoice($filter);
            $this->exporter->exportFromArray('Invoice', $reportInvoices);
        } else {
            $data = [
                'title' => "Report Invoice",
                'page' => "report/invoice",
                'owners' => $owners,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable invoice.
     */
    public function invoice_data()
    {
        $filters = array_merge(get_url_param('filter_invoice') ? $_GET : [], [
            'start' => get_url_param('start', $this->input->post('start')),
            'length' => get_url_param('length', $this->input->post('length')),
            'search' => get_url_param('search', $this->input->post('search'))['value'],
            'order_by' => get_url_param('order', $this->input->post('order'))[0]['column'],
            'order_method' => get_url_param('order', $this->input->post('order'))[0]['dir'],
            'data' => get_url_param('data', 'stock')
        ]);

        $reportInvoices = $this->reportInvoice->getReportInvoice($filters);

        header('Content-Type: application/json');
        echo json_encode($reportInvoices);
    }

    /**
     * Get report invoice CIF
     */
    public function invoice_cif()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_INVOICE);

        $filters = array_merge($_GET, ['page' => get_url_param('page', 1)]);

        $selectedOwners = key_exists('owner', $filters) ? $filters['owner'] : 0;
        $owners = $this->peopleModel->getById($selectedOwners);

        $selectedBookings = key_exists('booking', $filters) ? $filters['booking'] : 0;
        $bookings = $this->booking->getBookingById($selectedBookings);

        $export = $this->input->get('export');
        if ($export) unset($filters['page']);

        $reportInvoices = $this->reportInvoice->getReportInvoiceCIF($filters);

        if ($export) {
            if($this->input->get('export_type') == 'monthly') {
                $lastData = '';
                $monthlyData = [];
                foreach ($reportInvoices as $index => $reportInvoice) {
                    if ($reportInvoice['no_reference_inbound'] != $lastData) {
                        $monthlyData[format_date($reportInvoice['registration_date'], 'Y-m')]['inbounds'][] = $reportInvoice;
                        $lastData = $reportInvoice['no_reference_inbound'];
                    }
                    if (!empty($reportInvoice['id_booking_out'])) {
                        $monthlyData[format_date($reportInvoice['registration_date_outbound'], 'Y-m')]['outbounds'][] = $reportInvoice;
                    }
                }
                $this->reportInvoice->exportCIFMonthly($monthlyData);
            } else {
                $report = [];
                foreach ($reportInvoices as $index => &$reportInvoice) {
                    $reportInvoice['balance_from_currency'] = $reportInvoice['total_cif_inbound_from'];
                    $reportInvoice['balance_to_currency'] = $reportInvoice['total_cif_inbound_to'];
                    $stopCalculateBalance = false;
                    foreach ($reportInvoices as $reportInvoiceNext) {
                        if (!$stopCalculateBalance && $reportInvoice['no_reference_inbound'] == $reportInvoiceNext['no_reference_inbound']) {
                            echo $reportInvoice['balance_from_currency'] .' - '. $reportInvoiceNext['total_cif_outbound_from'] . '<br>';
                            $reportInvoice['balance_from_currency'] -= $reportInvoiceNext['total_cif_outbound_from'];
                            $reportInvoice['balance_to_currency'] -= $reportInvoiceNext['total_cif_outbound_to'];
                        }
                        if ($reportInvoice['no_reference_inbound'] == $reportInvoiceNext['no_reference_inbound'] && $reportInvoice['no_reference_outbound'] == $reportInvoiceNext['no_reference_outbound']) {
                            $stopCalculateBalance = true;
                        }
                    }
                    $reportInvoice['balance_from_currency'] = round($reportInvoice['balance_from_currency'], 2);
                    $reportInvoice['balance_to_currency'] = round($reportInvoice['balance_to_currency'], 2);

                    $report[] = [
                        'no' => $index + 1,
                        'jenis_dok' => $reportInvoice['booking_type_inbound'],
                        'aju' => $reportInvoice['no_reference_inbound'],
                        'nopen' => $reportInvoice['no_registration'],
                        'tgl_nopen' => $reportInvoice['registration_date'],
                        'party' => $reportInvoice['party'],
                        'cargo_inbound' => $reportInvoice['cargo_inbound'],
                        'nilai_invoice' => $reportInvoice['total_cif_inbound_from'],
                        'nilai_invoice_as_usd' => $reportInvoice['total_cif_inbound_to'],
                        'customer' => $reportInvoice['customer_name'],
                        'dok_pengeluaran' => $reportInvoice['booking_type_outbound'],
                        'aju_pengeluaran' => $reportInvoice['no_reference_outbound'],
                        'nopen_pengeluaran' => $reportInvoice['no_registration_outbound'],
                        'tgl_nopen_pengeluaran' => $reportInvoice['registration_date_outbound'],
                        'konfirmasi_bayar' => '',
                        'sppb' => $reportInvoice['sppb_date'],
                        'nilai_invoice_out' => $reportInvoice['total_cif_outbound_from'],
                        'nilai_invoid_out_as_usd' => $reportInvoice['total_cif_outbound_to'],
                        'ndpbm' => $reportInvoice['ndpbm'],
                        'nilai_pabean' => $reportInvoice['customs_value'],
                        'bm' => $reportInvoice['import_duty'],
                        'ppn' => $reportInvoice['vat'],
                        'pph' => $reportInvoice['income_tax'],
                        'cargo_outbound' => $reportInvoice['cargo_outbound'],
                        'gate_out' => $reportInvoice['gate_out'],
                        'sppd' => $reportInvoice['sppd_date'],
                        'balance' => $reportInvoice['balance_from_currency'],
                        'balance_as_usd' => $reportInvoice['balance_to_currency'],
                    ];
                }
                $this->exporter->exportFromArray('CIF Invoice', $report);
            }
        } else {
            foreach ($reportInvoices['data'] as &$reportInvoice) {
                $totalRow = 0;
                $reportInvoice['balance_from_currency'] = $reportInvoice['total_cif_inbound_from'];
                $reportInvoice['balance_to_currency'] = $reportInvoice['total_cif_inbound_to'];
                $stopCalculateBalance = false;
                foreach ($reportInvoices['data'] as $reportInvoiceNext) {
                    if(!$stopCalculateBalance && $reportInvoice['no_reference_inbound'] == $reportInvoiceNext['no_reference_inbound']) {
                        $reportInvoice['balance_from_currency'] -= $reportInvoiceNext['total_cif_outbound_from'];
                        $reportInvoice['balance_to_currency'] -= $reportInvoiceNext['total_cif_outbound_to'];
                    }
                    if ($reportInvoice['no_reference_inbound'] == $reportInvoiceNext['no_reference_inbound'] && $reportInvoice['no_reference_outbound'] == $reportInvoiceNext['no_reference_outbound']) {
                        $stopCalculateBalance = true;
                    }
                    if ($reportInvoice['no_reference_inbound'] == $reportInvoiceNext['no_reference_inbound']) {
                        $totalRow++;
                    }
                }
                $reportInvoice['total_outbound'] = $totalRow;
            }
            $data = [
                'title' => "Report CIF Invoice",
                'page' => "report/invoice_cif",
                'owner' => $owners,
                'booking' => $bookings,
                'reports' => $reportInvoices,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Report handover data.
     */
    public function handover()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_HANDOVER);

        $filter = get_url_param('filter_handover') ? $_GET : [];

        $reportHandovers = $this->workOrder->getWorkOrderSummary($filter);

        $print = get_url_param('print', false);
        if ($print) {
            foreach ($reportHandovers as &$reportHandover) {
                $containers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($reportHandover['id'], true);
                foreach ($containers as &$container) {
                    $containerGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($container['id']);
                    $container['goods'] = $containerGoods;

                    $containerContainers = $this->workOrderContainer->getWorkOrderContainersByWorkOrderContainer($container['id']);
                    $container['containers'] = $containerContainers;
                }
                $goods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($reportHandover['id'], true, true);
                foreach ($goods as &$item) {
                    $goodsItem = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderGoods($item['id']);
                    $item['goods'] = $goodsItem;
                }
                $reportHandover['containers'] = $containers;
                $reportHandover['goods'] = $goods;
            }
            $data = [
                'title' => "Print Handover",
                'reportHandovers' => $reportHandovers
            ];
            $this->load->view('report/_print_handover_summary', $data);
        } else {
            $data = [
                'title' => "Report Handover",
                'page' => "report/handover",
                'reportHandovers' => $reportHandovers
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Report realization data.
     */
    public function realization()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_PLAN_REALIZATION);

        $containerFilter = get_url_param('filter_summary_container') ? $_GET : [];
        $goodsFilter = get_url_param('filter_summary_goods') ? $_GET : [];

        $selectedOwners = key_exists('owner', $containerFilter) ? $containerFilter['owner'] : [0];
        if (empty($selectedOwners)) {
            $selectedOwners = key_exists('owner', $goodsFilter) ? $goodsFilter['owner'] : [0];
        }
        $owners = $this->peopleModel->getById($selectedOwners);

        $export = get_url_param('export');
        if (!empty($export)) {
            $reportRealizationContainers = $this->reportModel->getReportRealizationContainer($containerFilter);
            $reportRealizationGoods = $this->reportModel->getReportRealizationGoods($goodsFilter);
            $reportData = $export == 'CONTAINER' ? $reportRealizationContainers : $reportRealizationGoods;
            $this->exporter->exportFromArray('Realization ' . $export, $reportData);
        } else {
            $data = [
                'title' => "Report Realization",
                'page' => "report/realization",
                'owners' => $owners,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable realization container.
     */
    public function realization_container_data()
    {
        $filters = array_merge(get_url_param('filter_summary_container') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);
        $realization = $this->reportModel->getReportRealizationContainer($filters);

        header('Content-Type: application/json');
        echo json_encode($realization);
    }

    /**
     * Get ajax datatable realization goods.
     */
    public function realization_goods_data()
    {
        $filters = array_merge(get_url_param('filter_summary_goods') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);
        $realization = $this->reportModel->getReportRealizationGoods($filters);

        header('Content-Type: application/json');
        echo json_encode($realization);
    }

    /**
     * Report plan realization data.
     */
    public function plan_realization()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_PLAN_REALIZATION);

        $resources = $this->reportPlanRealization->getResource($_GET);

        $inbounds = $this->reportPlanRealization->getInbound($_GET);
        $inboundTarget = array_sum(array_column($inbounds, 'party_20')) + array_sum(array_column($inbounds, 'party_40')) + array_sum(array_column($inbounds, 'party_lcl'));
        $inboundRealization = array_sum(array_column($inbounds, 'realization_20')) + array_sum(array_column($inbounds, 'realization_40')) + array_sum(array_column($inbounds, 'realization_lcl'));
        $inboundSummary = [
            'target' => $inboundTarget,
            'realization' => $inboundRealization,
            'achievement' => $inboundRealization / if_empty($inboundTarget, 1) * 100,
        ];

        $outbounds = $this->reportPlanRealization->getOutbound($_GET);
        $outboundTarget = array_sum(array_column($outbounds, 'party_20')) + array_sum(array_column($outbounds, 'party_40')) + array_sum(array_column($outbounds, 'party_lcl'));
        $outboundRealization = array_sum(array_column($outbounds, 'realization_20')) + array_sum(array_column($outbounds, 'realization_40')) + array_sum(array_column($outbounds, 'realization_lcl'));
        $outboundSummary = [
            'target' => $outboundTarget,
            'realization' => $outboundRealization,
            'achievement' => $outboundRealization / if_empty($outboundTarget, 1) * 100,
        ];


        $outstandingTep = $this->transporterEntryPermit->getBy([
            'transporter_entry_permits.checked_in_at IS NULL' => null,
            'transporter_entry_permits.created_at>=' => '2020-05-01',
            //'NOW()<=transporter_entry_permits.expired_at' => null,
        ]);
        foreach ($inbounds as &$inbound) {
            $inbound['transporter_entry_permits'] = array_filter($outstandingTep, function ($tep) use($inbound) {
                return $tep['id_booking'] == $inbound['id_booking'];
            });
        }

        foreach ($outbounds as &$outbound) {
            $outbound['transporter_entry_permits'] = array_filter($outstandingTep, function ($tep) use($outbound) {
                return $tep['id_booking'] == $outbound['id_booking'];
            });
        }

        $opsTeusInbound = (array_sum(array_column($inbounds, 'realization_40')) * 2) + array_sum(array_column($inbounds, 'realization_20'));
        $opsTeusOutbound = (array_sum(array_column($outbounds, 'realization_40')) * 2) + array_sum(array_column($outbounds, 'realization_20'));
        $opsTeus = $opsTeusInbound + $opsTeusOutbound;
        $planRealization['ops_teus'] = $opsTeus == 0 ? 0 : (4 / $opsTeus);

        $opsLcl = array_sum(array_column($inbounds, 'realization_lcl')) + array_sum(array_column($outbounds, 'realization_lcl'));
        $planRealization['ops_lcl'] = $opsLcl == 0 ? 0 : (4 / $opsLcl);

        $lbrForklift = array_sum(array_column(array_filter($resources, function ($resource) {
            return strtolower($resource['resource']) == 'labor' || strtolower($resource['resource'] == 'forklift');
        }), 'realization'));
        $planRealization['lbr_teus'] = $opsTeus == 0 ? 0 : ($lbrForklift / $opsTeus);
        $planRealization['lbr_lcl'] = $opsLcl == 0 ? 0 : ($lbrForklift / $opsLcl);

        $data = [
            'title' => "Report Plan Realization",
            'page' => "report/plan_realization",
            'planRealization' => $planRealization,
            'resources' => $resources,
            'inbounds' => $inbounds,
            'inboundSummary' => $inboundSummary,
            'outbounds' => $outbounds,
            'outboundSummary' => $outboundSummary,
        ];
        if (isset($_GET['export'])) {
            $data['withoutCloseRealization'] = true;
            $this->load->helper('text');
            $page = $this->load->view('plan_realization/print', $data, true);

            $this->exporter->exportToPdf('plan-realization', $page, ['paper' => 'legal', 'orientation' => 'landscape']);
        } else {
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Show booking rating list
     */
    public function booking_rating()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_BOOKING_RATING);

        $filters = get_url_param('filter_booking_rating') ? $_GET : [];

        $selectedOwners = key_exists('owner', $filters) ? $filters['owner'] : '';
        $owners = $this->peopleModel->getBy(['ref_people.id' => $selectedOwners]);

        $export = get_url_param('export');
        if (!empty($export)) {
            $reports = $this->reportModel->getReportBookingRating($filters);
            $this->exporter->exportFromArray('Booking Rating', $reports);
        } else {
            $bookings = $this->reportModel->getReportBookingRating($filters);

            $totalBooking = count($bookings);
            $totalRating = 0;
            $totalRated = 0;
            $totalOutstanding = 0;
            $totalSppb = 0;
            $totalCompleted = 0;
            $totalCompletedSppb = 0;

            foreach ($bookings as $booking) {
                if (!empty($booking['completed_date'])) {
                    if (empty($booking['rating'])) {
                        $totalOutstanding++;
                    } else {
                        $totalRating += $booking['rating'];
                        $totalRated++;
                    }
                }

                if (!empty($booking['sppb_date'])) {
                    $totalSppb++;
                    if (!empty($booking['completed_date'])) {
                        $totalCompletedSppb++;
                    }
                }
                if (!empty($booking['completed_date'])) {
                    $totalCompleted++;
                }
            }

            if ($totalRated > 0) {
                $avg = $totalRating / $totalRated;
            } else {
                $avg = 0;
            }

            $data = [
                'title' => "Report Booking Rating",
                'page' => "report/booking_rating",
                'owners' => $owners,
                'totalBooking' => $totalBooking,
                'totalCompletedSppb' => $totalCompletedSppb,
                'totalCompleted' => $totalCompleted,
                'totalSppb' => $totalSppb,
                'totalRated' => $totalRated,
                'totalOutstanding' => $totalOutstanding,
                'totalAverage' => $avg,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get booking rating ajax data.
     */
    public function booking_rating_data()
    {
        $filters = array_merge(get_url_param('filter_booking_rating') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);
        $bookings = $this->reportModel->getReportBookingRating($filters);

        header('Content-Type: application/json');
        echo json_encode($bookings);
    }

    /**
     * Show outstanding rating by filters
     */
    public function outstanding_rating()
    {
        $filters = get_url_param('filter_booking_rating') ? $_GET : [];

        $filters['rating_outstanding'] = true;
        $bookings = $this->reportModel->getReportBookingRating($filters);

        $data = [
            'title' => "Report Booking Rating",
            'page' => "report/outstanding_rating",
            'bookings' => $bookings
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show outstanding completed by filters
     */
    public function outstanding_completed()
    {
        $filters = get_url_param('filter_booking_rating') ? $_GET : [];

        $filters['completed_outstanding'] = true;
        $bookings = $this->reportModel->getReportBookingRating($filters);

        $data = [
            'title' => "Report Booking Completed",
            'page' => "report/outstanding_completed",
            'bookings' => $bookings
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show booking summary item list
     */
    public function booking_summary()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_BOOKING_SUMMARY);

        $filter = get_url_param('filter_booking_summary') ? $_GET : [];

        $selectedOwners = key_exists('owner', $filter) ? $filter['owner'] : '';
        $owners = $this->peopleModel->getBy(['ref_people.name' => $selectedOwners]);

        $export = get_url_param('export');
        if (!empty($export)) {
            $reports = $this->reportModel->getReportBookingSummary($filter);
            $this->exporter->exportFromArray('Booking Summary', $reports);
        } else {
            $data = [
                'title' => "Report Booking Summary",
                'page' => "report/booking_summary",
                'owners' => $owners
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Booking report summary ajax data table.
     */
    public function booking_summary_data()
    {
        $filters = array_merge(get_url_param('filter_booking_summary') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);
        $reports = $this->reportModel->getReportBookingSummary($filters);

        header('Content-Type: application/json');
        echo json_encode($reports);
    }

    /**
     * Get data tracking by no reference
     */
    public function booking_tracker()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_BOOKING_TRACKER);

        $bookingId = get_url_param('booking');

        $selectedBooking = $this->booking->getBookingById($bookingId);
        $bookingOuts = [];
        $bookings = [];
        if (!empty($selectedBooking)) {
            $bookingOuts = $this->booking->getBookingOutByBookingIn($bookingId);
            $bookings = array_merge([$selectedBooking], $bookingOuts);
        }

        foreach ($bookings as &$booking) {
            $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($booking['id']);
            foreach ($bookingContainers as &$container) {
                $containerGoods = $this->bookingGoods->getBookingGoodsByBookingContainer($container['id']);
                $container['goods'] = $containerGoods;
            }
            $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($booking['id'], true);

            $booking['details']['containers'] = $bookingContainers;
            $booking['details']['goods'] = $bookingGoods;
        }

        $safeConducts = $this->safeConduct->getSafeConductsByBooking($bookingId);
        foreach ($bookingOuts as $bookingOut) {
            $safeConductOuts = $this->safeConduct->getSafeConductsByBooking($bookingOut['id']);
            $safeConducts = array_merge($safeConducts, $safeConductOuts);
        }

        foreach ($safeConducts as &$safeConduct) {
            $safeConductContainers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($safeConduct['id']);
            foreach ($safeConductContainers as &$container) {
                $containerGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConductContainer($container['id']);
                $container['goods'] = $containerGoods;
            }
            $safeConductGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($safeConduct['id'], true);

            $safeConduct['details']['containers'] = $safeConductContainers;
            $safeConduct['details']['goods'] = $safeConductGoods;
        }

        $handlings = $this->handling->getHandlingsByBooking($bookingId);
        foreach ($bookingOuts as $bookingOut) {
            $handlingOuts = $this->handling->getHandlingsByBooking($bookingOut['id']);
            $handlings = array_merge($handlings, $handlingOuts);
        }
        foreach ($handlings as &$handling) {
            $handlingContainers = $this->handlingContainer->getHandlingContainersByHandling($handling['id']);
            foreach ($handlingContainers as &$container) {
                $containerGoods = $this->handlingGoods->getHandlingGoodsByHandlingContainer($container['id']);
                $container['goods'] = $containerGoods;
            }
            $handlingGoods = $this->handlingGoods->getHandlingGoodsByHandling($handling['id'], true);

            $handling['details']['containers'] = $handlingContainers;
            $handling['details']['goods'] = $handlingGoods;
        }

        $workOrders = $this->workOrder->getWorkOrdersByBooking($bookingId);
        foreach ($bookingOuts as $bookingOut) {
            $workOrderOuts = $this->workOrder->getWorkOrdersByBooking($bookingOut['id']);
            $workOrders = array_merge($workOrders, $workOrderOuts);
        }

        foreach ($workOrders as &$workOrder) {
            $containers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrder['id'], true);
            foreach ($containers as &$container) {
                $containerGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($container['id']);
                $container['goods'] = $containerGoods;

                $containerContainers = $this->workOrderContainer->getWorkOrderContainersByWorkOrderContainer($container['id']);
                $container['containers'] = $containerContainers;
            }
            $goods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrder['id'], true, true);
            foreach ($goods as &$item) {
                $goodsItem = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderGoods($item['id']);
                $item['goods'] = $goodsItem;
            }

            $workOrder['details']['containers'] = $containers;
            $workOrder['details']['goods'] = $goods;
        }

        $data = [
            'title' => "Report Booking Tracker",
            'page' => "report/booking_tracker",
            'booking' => $selectedBooking,
            'bookings' => $bookings,
            'safeConducts' => $safeConducts,
            'handlings' => $handlings,
            'workOrders' => $workOrders,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Report comparator data.
     */
    public function booking_comparator()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_BOOKING_COMPARATOR);

        $export = get_url_param('export');

        if (!empty($export)) {
            $report = $this->reportModel->getBookingGoodsComparator($_GET);
            $this->exporter->exportFromArray('Comparator', $report);
        } else {
            $data = [
                'title' => "Report Comparator",
                'page' => "report/booking_comparator",
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get booking comparator data table
     */
    public function booking_comparator_data()
    {
        $filters = array_merge($_GET, [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);
        $report = $this->reportModel->getBookingGoodsComparator($filters);

        header('Content-Type: application/json');
        echo json_encode($report);
    }

    /**
     * Get data tracking by no reference
     */
    public function booking_control()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_BOOKING_CONTROL);

        $filter = get_url_param('filter_control') ? $_GET : [];
        $export = get_url_param('export');
        $owners = get_if_exist($filter, 'owner', 0);
        $statusControl = get_if_exist($filter, 'status_control', 0);

        $conditions = ['bookings.id_customer' => $owners];
        if (!empty($statusControl)) {
            if (in_array('OUTSTANDING', $statusControl)) {
                $statusControl = array_merge($statusControl, ['PENDING', 'DRAFT', 'DONE']);
            }
            $conditions['bookings.status_control'] = $statusControl;
        }
        $bookings = $this->bookingControl->getBy($conditions);

        if (!empty($export)) {
            $this->exporter->exportFromArray('Booking Control', $bookings);
        } else {
            $data = [
                'title' => "Report Booking Control",
                'page' => "report/booking_control",
                'owners' => $this->peopleModel->getById($owners),
                'bookings' => $bookings,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get data tracking by no reference
     */
    public function booking_control_detail()
    {
        $export = get_url_param('export');
        $bookingId = get_url_param('booking');
        $handlingTypeIds = get_url_param('handling_type');

        $booking = $this->booking->getBookingById($bookingId);
        $handlingTypes = $this->handlingType->getAllhandlingTypes();

        if (!empty($booking)) {
            $booking['work_orders'] = $this->workOrder->getWorkOrdersByHandlingType($handlingTypeIds, $bookingId);
            foreach ($booking['work_orders'] as &$workOrderInbound) {
                $workOrderInbound['containers'] = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderInbound['id'], true);
                foreach ($workOrderInbound['containers'] as &$container) {
                    $container['goods'] = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($container['id']);
                    $container['containers'] = $this->workOrderContainer->getWorkOrderContainersByWorkOrderContainer($container['id']);
                }

                $workOrderInbound['goods'] = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderInbound['id'], true, true);
                foreach ($workOrderInbound['goods'] as &$item) {
                    $item['goods'] = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderGoods($item['id']);
                }
            }
        }

        $bookingOuts = $this->booking->getBookingOutByBookingIn($bookingId);
        foreach ($bookingOuts as &$bookingOut) {
            $bookingOut['work_orders'] = $this->workOrder->getWorkOrdersByHandlingType($handlingTypeIds, $bookingOut['id']);
            foreach ($bookingOut['work_orders'] as &$workOrderInbound) {
                $workOrderInbound['containers'] = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderInbound['id'], true);
                foreach ($workOrderInbound['containers'] as &$container) {
                    $container['goods'] = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($container['id']);
                    $container['containers'] = $this->workOrderContainer->getWorkOrderContainersByWorkOrderContainer($container['id']);
                }

                $workOrderInbound['goods'] = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderInbound['id'], true, true);
                foreach ($workOrderInbound['goods'] as &$item) {
                    $item['goods'] = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderGoods($item['id']);
                }
            }
        }

        $containerStocks = [];
        $containerOuts = [];

        $goodsIns = [];
        $goodsOuts = [];

        $bookingInMerge = [];
        if (!empty($booking)) {
            $bookingInMerge = [$booking];
        }
        $allBookings = array_merge($bookingInMerge, $bookingOuts);
        foreach ($allBookings as $allBooking) {
            $inboundContainer = $this->reportModel->getContainerStockMove(['booking' => $allBooking['id'], 'multiplier' => 1]);
            $containerStocks = array_merge($containerStocks, $inboundContainer);

            $inboundGoods = $this->reportModel->getGoodsStockMove(['booking' => $allBooking['id'], 'multiplier' => 1]);
            $goodsIns = array_merge($goodsIns, $inboundGoods);
        }
        foreach ($allBookings as $allBooking) {
            $outbounds = $this->reportModel->getContainerStockMove(['booking' => $allBooking['id'], 'multiplier' => -1]);
            $containerOuts = array_merge($containerOuts, $outbounds);

            $outboundGoods = $this->reportModel->getGoodsStockMove(['booking' => $allBooking['id'], 'multiplier' => -1]);
            $goodsOuts = array_merge($goodsOuts, $outboundGoods);
        }

        foreach ($containerStocks as &$containerIn) {
            $containerIn['outbounds'] = [];
            foreach ($containerOuts as $index => &$containerOut) {
                if ($containerIn['no_container'] == $containerOut['no_container']) {
                    $containerIn['outbounds'][] = $containerOut;
                    unset($containerOuts[$index]);
                }
            }
        }

        $goodIds = array_unique(array_column($goodsIns, 'id_goods'));
        if (!empty($goodIds)) {
            $goodsStocks = $this->goodsModel->getById($goodIds);
        } else {
            $goodsStocks = [];
        }

        foreach ($goodsStocks as &$item) {
            $item['inbounds'] = [];
            $item['outbounds'] = [];
            foreach ($goodsIns as $goods) {
                if ($item['id'] == $goods['id_goods']) {
                    $item['inbounds'][] = $goods;
                }
            }
            foreach ($goodsOuts as $goods) {
                if ($item['id'] == $goods['id_goods']) {
                    $item['outbounds'][] = $goods;
                }
            }
        }

        if (!empty($export)) {
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=booking-control.xls");
            $this->load->view('report/_plain_booking_control', [
                'booking' => $booking,
                'bookingOuts' => $bookingOuts,
                'containerStocks' => $containerStocks,
                'goodsStocks' => $goodsStocks,
            ]);
        } else {
            $data = [
                'title' => "Booking Control Detail",
                'page' => "report/booking_control_detail",
                'booking' => $booking,
                'handlingTypes' => $handlingTypes,
                'bookingOuts' => $bookingOuts,
                'containerStocks' => $containerStocks,
                'goodsStocks' => $goodsStocks,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Container tracker
     */
    public function container_tracker()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_CONTAINER_TRACKER);

        $containerId = get_url_param('container');

        $container = $this->containerModel->getById($containerId);

        $bookings = [];
        $safeConducts = [];
        $handlings = [];
        $workOrders = [];
        if (!empty($container)) {
            $bookings = $this->trackerModel->getBookingsByContainer($containerId);
            foreach ($bookings as &$booking) {
                $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($booking['id']);
                foreach ($bookingContainers as &$container) {
                    $containerGoods = $this->bookingGoods->getBookingGoodsByBookingContainer($container['id']);
                    $container['goods'] = $containerGoods;
                }
                $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($booking['id'], true);

                $booking['details']['containers'] = $bookingContainers;
                $booking['details']['goods'] = $bookingGoods;
            }

            $safeConducts = $this->trackerModel->getSafeConductsByContainer($containerId);
            foreach ($safeConducts as &$safeConduct) {
                $safeConductContainers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($safeConduct['id']);
                foreach ($safeConductContainers as &$container) {
                    $containerGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConductContainer($container['id']);
                    $container['goods'] = $containerGoods;
                }
                $safeConductGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($safeConduct['id'], true);

                $safeConduct['details']['containers'] = $safeConductContainers;
                $safeConduct['details']['goods'] = $safeConductGoods;
            }

            $handlings = $this->trackerModel->getHandlingsByContainer($containerId);
            foreach ($handlings as &$handling) {
                $handlingContainers = $this->handlingContainer->getHandlingContainersByHandling($handling['id']);
                foreach ($handlingContainers as &$container) {
                    $containerGoods = $this->handlingGoods->getHandlingGoodsByHandlingContainer($container['id']);
                    $container['goods'] = $containerGoods;
                }
                $handlingGoods = $this->handlingGoods->getHandlingGoodsByHandling($handling['id'], true);

                $handling['details']['containers'] = $handlingContainers;
                $handling['details']['goods'] = $handlingGoods;
            }

            $workOrders = $this->trackerModel->getWorkOrdersByContainer($containerId);
            foreach ($workOrders as &$workOrder) {
                $containers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrder['id'], true);

                foreach ($containers as &$container) {
                    $containerGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($container['id']);
                    $container['goods'] = $containerGoods;

                    $containerContainers = $this->workOrderContainer->getWorkOrderContainersByWorkOrderContainer($container['id']);
                    $container['containers'] = $containerContainers;
                }
                $goods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrder['id'], true, true);
                foreach ($goods as &$item) {
                    $goodsItem = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderGoods($item['id']);
                    $item['goods'] = $goodsItem;
                }

                $workOrder['details']['containers'] = $containers;
                $workOrder['details']['goods'] = $goods;
            }
        }
        $cont = $this->containerModel->getById($containerId);

        $data = [
            'title' => "Report Container Tracker",
            'page' => "report/container_tracker",
            'container' => $cont,
            'bookings' => $bookings,
            'safeConducts' => $safeConducts,
            'handlings' => $handlings,
            'workOrders' => $workOrders,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Get report eseal tracking
     */
    public function eseal_tracking()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_ESEAL_TRACKING);
        
        if (!empty(get_url_param('export'))) {
            $report = $this->reportServiceTime->getEsealTracking($_GET);
            $this->exporter->exportLargeResourceFromArray('Eseal tracking', $report);
        } else {
            $customer = $this->peopleModel->getById(get_url_param('customer'));
            $booking = $this->booking->getBookingById(get_url_param('booking'));
            $data = [
                'title' => "Eseal tracking data",
                'page' => "report/eseal_tracking",
                'customer' => $customer,
                'booking' => $booking,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get eseal tracking data page
     */
    public function eseal_tracking_data()
    {
        $filters = array_merge($_GET, [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);
        $stockSummary = $this->reportServiceTime->getEsealTracking($filters);

        header('Content-Type: application/json');
        echo json_encode($stockSummary);
    }

    /**
     * Show eseal tracking route.
     *
     * @param $safeConductId
     */
    public function eseal_route($safeConductId)
    {
        $safeConduct = $this->safeConduct->getSafeConductById($safeConductId);
        $routes = $this->safeConductRoute->getBy(['id_safe_conduct' => $safeConductId]);
        foreach ($routes as &$route) {
            unset($route['data']);
        }

        if (!empty(get_url_param('export'))) {
            $this->exporter->exportFromArray('Eseal route ' . url_title($safeConduct['no_safe_conduct']), $routes);
        } else {
            $data = [
                'title' => "Tracking route",
                'page' => "report/eseal_route",
                'safeConduct' => $safeConduct,
                'routes' => $routes,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Show report service time.
     */
    public function service_time()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_SERVICE_TIME);

        $filter = get_url_param('filter_service_time') ? $_GET : [];

        $selectedOwners = key_exists('owner', $filter) ? $filter['owner'] : [0];
        $selectedContainers = key_exists('container', $filter) ? $filter['container'] : [0];

        $owners = $this->peopleModel->getById($selectedOwners);
        $containers = $this->containerModel->getById($selectedContainers);

        $serviceTimeDriver = $this->reportModel->getReportServiceTimeDriver($filter);
        $serviceTimeTallyField = $this->reportModel->getReportServiceTimeTally('field', $filter);
        $serviceTimeTallyWarehouse = $this->reportModel->getReportServiceTimeTally('warehouse', $filter);

        $export = get_url_param('export');
        if (!empty($export)) {
            switch ($export) {
                case 'summary':
                    $serviceTime = $this->reportModel->getReportServiceTimeSummary();
                    $this->exporter->exportFromArray('Service Time Summary', $serviceTime);
                    break;
                case 'all':
                    $serviceTime = $this->reportModel->getReportServiceTime($filter);
                    $this->exporter->exportFromArray('All Service Time', $serviceTime);
                    break;
                case 'monthly':
                    $serviceTimeMonthly = $this->reportModel->getReportServiceTimeMonthly($filter);
                    $this->exporter->exportFromArray('Monthly Service Time', $serviceTimeMonthly);
                    break;
                case 'weekly':
                    $serviceTimeWeekly = $this->reportModel->getReportServiceTimeWeekly($filter);
                    $this->exporter->exportFromArray('Weekly Service Time', $serviceTimeWeekly);
                    break;
                case 'driver':
                    $this->exporter->exportFromArray('Driver Service Time', $serviceTimeDriver);
                    break;
                case 'tally_field':
                    $this->exporter->exportFromArray('Tally Field Service Time', $serviceTimeTallyField);
                    break;
                case 'tally_warehouse':
                    $this->exporter->exportFromArray('Tally Warehouse Service Time', $serviceTimeTallyWarehouse);
                    break;
            }
        } else {
            $serviceTimeChartIn = $this->reportChartModel->getReportChartServiceTime('INBOUND', $filter);
            $serviceTimeChartOut = $this->reportChartModel->getReportChartServiceTime('OUTBOUND', $filter);
            $data = [
                'title' => "Report Service Time",
                'page' => "report/service_time",
                'owners' => $owners,
                'containers' => $containers,
                'serviceTimesDriver' => $serviceTimeDriver,
                'serviceTimesTallyField' => $serviceTimeTallyField,
                'serviceTimesTallyWarehouse' => $serviceTimeTallyWarehouse,
                'serviceTimeChartIn' => $serviceTimeChartIn,
                'serviceTimeChartOut' => $serviceTimeChartOut,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable service time.
     */
    public function service_time_summary_data()
    {
        $filters = array_merge(get_url_param('filter_service_time_summary') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);
        $serviceTime = $this->reportModel->getReportServiceTimeSummary($filters);

        header('Content-Type: application/json');
        echo json_encode($serviceTime);
    }

    /**
     * Get ajax datatable service time.
     */
    public function service_time_data()
    {
        $filters = array_merge(get_url_param('filter_service_time') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);
        $serviceTime = $this->reportModel->getReportServiceTime($filters);

        header('Content-Type: application/json');
        echo json_encode($serviceTime);
    }

    /**
     * Get ajax datatable service time monthly.
     */
    public function service_time_monthly_data()
    {
        $filters = array_merge(get_url_param('filter_service_time') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);
        $serviceTime = $this->reportModel->getReportServiceTimeMonthly($filters);

        header('Content-Type: application/json');
        echo json_encode($serviceTime);
    }

    /**
     * Get ajax datatable service time weekly.
     */
    public function service_time_weekly_data()
    {
        $filters = array_merge(get_url_param('filter_service_time') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);
        $serviceTime = $this->reportModel->getReportServiceTimeWeekly($filters);

        header('Content-Type: application/json');
        echo json_encode($serviceTime);
    }

    /**
     * Show report service control inbound.
     */

    public function service_time_control_inbound()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_SERVICE_TIME_CONTROL_IN);

        $filters = array_merge($_GET, ['page' => get_url_param('page', 1)]);

        $export = $this->input->get('export');
        if ($export) unset($filters['page']);

        $serviceTimes = $this->reportModel->getReportServiceTimeControlInbound($filters);
        // print_debug($serviceTimes);
        if (!$export) {
            unset($filters['page']);
            $draftingRed = 0;
            $draftingGreen = 0;
            $draftingYellow = 0;
            $yellowDraftCek = true;
            $draftCek = true;

            $confirmRed = 0;
            $confirmGreen = 0;
            $confirmYellow = 0;
            $yellowConfirmCek = true;
            $confirmCek = true;

            $doRed = 0;
            $doGreen = 0;
            $doYellow = 0;
            $yellowDoCek = true;
            $doCek = true;

            $sppbRed = 0;
            $sppbGreen = 0;
            $sppbYellow = 0;
            $yellowSppbCek = true;
            $sppbCek = true;

            $penarikanRed = 0;
            $penarikanGreen = 0;
            $penarikanYellow = 0;
            $yellowPenarikanCek = true;
            $penarikanCek = true;

            $bongkarRed = 0;
            $bongkarGreen = 0;
            $bongkarYellow = 0;
            $yellowBongkarCek = true;
            $bongkarCek = true;

            $totalnya = 0;
            $cek = '';

            $is_stripping = null;

            $serviceTimesSummary = $this->reportModel->getReportServiceTimeControlInbound($filters);

            // print_debug($this->db->last_query());
            // print_debug($serviceTimesSummary);
            foreach ($serviceTimesSummary as $s) {
                if ($cek != $s['no_upload']) {
                    //draft
                    if ($s['is_late_draft'] == '') {
                        $draftingYellow += 1;
                        $yellowDraftCek = FALSE;
                        $draftCek = TRUE;
                    } else if ($s['is_late_draft'] >= 1) {
                        $draftingRed += 1;
                        // $serviceTimesSummary[$totalnya]['Red']=$draftingRed;
                        $draftCek = false;
                    } else {
                        $draftingGreen += 1;
                        $draftCek = TRUE;
                        // print_r($draftingGreen);
                    }
                    //confirm
                    if ($s['is_late_confirmation'] == '') {
                        $confirmYellow += 1;
                        $yellowConfirmCek = FALSE;
                        $confirmCek = TRUE;
                    } else if ($s['is_late_confirmation'] >= 1) {
                        $confirmRed += 1;
                        $confirmCek = false;
                    } else {
                        $confirmGreen += 1;
                        $confirmCek = TRUE;
                    }
                    //do
                    if ($s['is_late_do'] == '') {
                        $doYellow += 1;
                        $yellowDoCek = FALSE;
                        $doCek = TRUE;
                    } else if ($s['is_late_do'] >= 1) {
                        $doRed += 1;
                        $doCek = false;
                    } else {
                        $doGreen += 1;
                        $doCek = TRUE;
                    }
                    //sppb
                    if ($s['is_late_sppb'] == '') {
                        $sppbYellow += 1;
                        $yellowSppbCek = FALSE;
                        $sppbCek = TRUE;
                    } else if ($s['is_late_sppb'] >= 1) {
                        $sppbRed += 1;
                        $sppbCek = false;
                    } else {
                        $sppbGreen += 1;
                        $sppbCek = TRUE;
                    }
                    // //penarikan
                    // if ($s['is_late_security_inbound']=='') {
                    //     $penarikanCek=TRUE;
                    // }else if ($s['is_late_security_inbound']>= 1) {
                    //     $penarikanRed+=1;
                    //     $penarikanCek=false;
                    // }else{
                    //     $penarikanGreen+=1;
                    //     $penarikanCek=TRUE;
                    // }
                    // //bongkar
                    $max = date("1970-01-01 00:00:00");
                    $is_late_bogkar = null;
                    $is_stripping = null;
                    $maxTemp = $max;
                    $maxComplete = $max;

                    $max_un = $max;
                    $max_st = $max;
                    $status_st = 0;
                    $is_late_bongkar_x = '';
                    // if ($s['expedition_type']=='INTERNAL') {
                    $cekin = $s['no_upload'];
                    $loop = $totalnya;
                    do {
                        if ($serviceTimesSummary[$loop]['last_in_tci'] == null || $max == null) {
                            $max = null;
                            $is_late_bogkar = '';
                        } else {
                            if (strtotime($maxTemp) < strtotime($serviceTimesSummary[$loop]['last_in_tci'])) {
                                $maxTemp = $serviceTimesSummary[$loop]['last_in_tci'];
                                $is_late_bogkar = $serviceTimesSummary[$loop]['is_late_bongkar'];
                            }
                        }
                        //dengan last stripping or unload
                        if ($serviceTimesSummary[$loop]['is_stripping'] == null || $serviceTimesSummary[$loop]['bongkar_date'] == NULL || $serviceTimesSummary[$loop]['last_in_tci'] == NULL) {
                            $is_stripping = null;
                            $status_st = '';
                        } else {
                            if ($serviceTimesSummary[$loop]['is_stripping'] == 0 && $status_st === 0) {
                                $max_un = $serviceTimesSummary[$loop]['terakhir_unload'];
                            } else if ($status_st === 0 || $status_st == 1) {
                                $status_st = 1;
                                if ($max_st < $serviceTimesSummary[$loop]['terakhir_stripping']) {
                                    $max_st = $serviceTimesSummary[$loop]['terakhir_stripping'];
                                }
                            }
                        }
                        $loop++;
                        if (!isset($serviceTimesSummary[$loop]['no_upload'])) {
                            break;
                        }
                    } while ($cekin == $serviceTimesSummary[$loop]['no_upload']);


                    if ($status_st === '') {
                        $is_late_bongkar_x = '';
                    } else {
                        if ($status_st) {
                            $tmp = date('Y-m-d', strtotime($maxTemp));
                            if (date('H:i:s', strtotime($maxTemp)) < '12:00:00') {
                                $is_late_bongkar_x = ($max_st > date('Y-m-d', strtotime($maxTemp)) . ' 23:59:59') ? 1 : 0;
                            } else {
                                $is_late_bongkar_x = ($max_st > date('Y-m-d', strtotime('+1 days', strtotime($tmp))) . ' 12:00:00') ? 1 : 0;

                            }
                        } else {
                            $tmp = date('Y-m-d', strtotime($maxTemp));
                            if (date('H:i:s', strtotime($maxTemp)) < '12:00:00') {
                                $is_late_bongkar_x = ($max_un > date('Y-m-d', strtotime($maxTemp)) . ' 23:59:59') ? 1 : 0;
                            } else {
                                $is_late_bongkar_x = ($max_un > date('Y-m-d', strtotime('+1 days', strtotime($tmp))) . ' 12:00:00') ? 1 : 0;
                                // if ($s['no_upload']=='UP/19/08/000141') {
                                //     print_debug($is_late_bongkar_x);
                                // }
                            }
                        }
                    }

                    $cekin = $s['no_upload'];
                    $loop = $totalnya;
                    do {
                        $serviceTimesSummary[$loop]['is_late_bongkar'] = $is_late_bogkar;
                        $serviceTimesSummary[$loop]['is_late_bongkar_last'] = $is_late_bongkar_x;
                        $loop++;
                        if (!isset($serviceTimesSummary[$loop]['no_upload'])) {
                            break;
                        }
                    } while ($cekin == $serviceTimesSummary[$loop]['no_upload']);
                    $y = 0;
                    foreach ($serviceTimes['data'] as $data) {
                        if ($data['no_upload'] == $cekin) {
                            $serviceTimes['data'][$y]['is_late_bongkar'] = $is_late_bogkar;
                            $serviceTimes['data'][$y]['is_late_bongkar_last'] = $is_late_bongkar_x;
                        }
                        $y++;
                    }
                    // } else {
                    //     $cekin=$s['no_upload'];
                    //     $loop=$totalnya;
                    //     do {
                    //         if ($serviceTimesSummary[$loop]['security_in_date']==null || $max=null) {
                    //             $max=null;
                    //             $is_late_bogkar='';
                    //         }else {
                    //             if ($max<$serviceTimesSummary[$loop]['security_in_date']) {
                    //                 $max=$serviceTimesSummary[$loop]['security_in_date'];
                    //                 $is_late_bogkar=$serviceTimesSummary[$loop]['is_late_bongkar'];
                    //             }
                    //         }
                    //         $loop++;
                    //         if (!isset($serviceTimesSummary[$loop]['no_upload'])) {
                    //             break;
                    //         }
                    //     } while ($cekin == $serviceTimesSummary[$loop]['no_upload']);
                    //     $cekin=$s['no_upload'];
                    //     $loop=$totalnya;
                    //     do {
                    //         $serviceTimesSummary[$loop]['is_late_bongkar']=$is_late_bogkar;
                    //         $loop++;
                    //         if (!isset($serviceTimesSummary[$loop]['no_upload'])) {
                    //             break;
                    //         }
                    //     } while ($cekin == $serviceTimesSummary[$loop]['no_upload']);
                    //     $y=0;
                    //     foreach ($serviceTimes['data'] as $data) {
                    //         if ($data['no_upload']==$cekin) {
                    //             $serviceTimes['data'][$y]['is_late_bongkar']=$is_late_bogkar;
                    //         }
                    //         $y++;
                    //     }
                    // }

                    //tanpa last stripping
                    // if ($serviceTimesSummary[$totalnya]['is_late_bongkar']=='') {
                    //     $bongkarYellow+=1;
                    // }else if ($serviceTimesSummary[$totalnya]['is_late_bongkar']>= 1) {
                    //     $bongkarRed+=1;
                    // }else{
                    //     $bongkarGreen+=1;
                    // }
                    //dengan last stripping

                    if ($serviceTimesSummary[$totalnya]['is_late_bongkar_last'] === '') {
                        $bongkarYellow += 1;
                    } else if ($serviceTimesSummary[$totalnya]['is_late_bongkar_last'] >= 1) {
                        $bongkarRed += 1;
                    } else {
                        $bongkarGreen += 1;
                    }
                    //penarikan
                    $max = date("1970-01-01 00:00:00");
                    $maxTemp = $max;
                    $is_late_security_inbound = null;
                    // if ($s['expedition_type']=='INTERNAL') {
                    $cekin = $s['no_upload'];
                    $loop = $totalnya;
                    do {
                        if ($serviceTimesSummary[$loop]['last_in_tci'] == null || $max == null) {
                            $max = null;
                            $is_late_security_inbound = '';
                        } else {
                            if (strtotime($maxTemp) < strtotime($serviceTimesSummary[$loop]['last_in_tci'])) {
                                $maxTemp = $serviceTimesSummary[$loop]['last_in_tci'];
                                $is_late_security_inbound = $serviceTimesSummary[$loop]['is_late_security_inbound'];
                            }
                        }
                        $loop++;
                        if (!isset($serviceTimesSummary[$loop]['no_upload'])) {
                            break;
                        }
                    } while ($cekin == $serviceTimesSummary[$loop]['no_upload']);
                    $cekin = $s['no_upload'];
                    $loop = $totalnya;
                    do {
                        $serviceTimesSummary[$loop]['is_late_security_inbound'] = $is_late_security_inbound;
                        $serviceTimesSummary[$loop]['security_inbound_max'] = $maxTemp;
                        $loop++;
                        if (!isset($serviceTimesSummary[$loop]['no_upload'])) {
                            break;
                        }
                    } while ($cekin == $serviceTimesSummary[$loop]['no_upload']);
                    $y = 0;
                    foreach ($serviceTimes['data'] as $data) {
                        if ($data['no_upload'] == $cekin) {
                            $serviceTimes['data'][$y]['is_late_security_inbound'] = $is_late_security_inbound;
                            if ($maxTemp == '1970-01-01 00:00:00') {
                                $maxTemp = null;
                            }
                            $serviceTimes['data'][$y]['security_inbound_max'] = $maxTemp;
                        }
                        $y++;
                    }
                    // } else {
                    //     $cekin=$s['no_upload'];
                    //     $loop=$totalnya;
                    //     do {
                    //         if ($serviceTimesSummary[$loop]['security_in_date']==null || $max=null) {
                    //             $max=null;
                    //             $is_late_security_inbound='';
                    //         }else {
                    //             if ($max<$serviceTimesSummary[$loop]['security_in_date']) {
                    //                 $max=$serviceTimesSummary[$loop]['security_in_date'];
                    //                 $is_late_security_inbound=$serviceTimesSummary[$loop]['is_late_security_inbound'];
                    //             }
                    //         }
                    //         $loop++;
                    //         if (!isset($serviceTimesSummary[$loop]['no_upload'])) {
                    //             break;
                    //         }
                    //     } while ($cekin == $serviceTimesSummary[$loop]['no_upload']);
                    //     $cekin=$s['no_upload'];
                    //     $loop=$totalnya;
                    //     do {
                    //         $serviceTimesSummary[$loop]['is_late_security_inbound']=$is_late_security_inbound;
                    //         $serviceTimesSummary[$loop]['security_inbound_max']=$max;
                    //         $loop++;
                    //         if (!isset($serviceTimesSummary[$loop]['no_upload'])) {
                    //             break;
                    //         }
                    //     } while ($cekin == $serviceTimesSummary[$loop]['no_upload']);
                    //     $y=0;
                    //     foreach ($serviceTimes['data'] as $data) {
                    //         if ($data['no_upload']==$cekin) {
                    //             $serviceTimes['data'][$y]['is_late_security_inbound']=$is_late_security_inbound;
                    //             $serviceTimes['data'][$y]['security_inbound_max']=$max;
                    //         }
                    //         $y++;
                    //     }
                    // }
                    //penarikan
                    if ($serviceTimesSummary[$totalnya]['is_late_security_inbound'] == '') {
                        $penarikanYellow += 1;
                    } else if ($serviceTimesSummary[$totalnya]['is_late_security_inbound'] >= 1) {
                        $penarikanRed += 1;
                    } else {
                        $penarikanGreen += 1;
                    }

                    $cek = $s['no_upload'];
                } else {
                    //draft
                    if ($draftCek) {
                        if ($s['is_late_draft'] == '') {
                            if ($yellowDraftCek) {
                                $draftingYellow += 1;
                                $yellowDraftCek = FALSE;
                            }
                        } else if ($s['is_late_draft'] >= 1) {
                            $draftingRed += 1;
                            $draftingGreen -= 1;
                            $draftCek = false;
                            // $serviceTimesSummary[$totalnya]['Red']=$draftingRed;
                        }
                    }
                    //comfirm
                    if ($confirmCek) {
                        if ($s['is_late_confirmation'] == '') {
                            if ($yellowConfirmCek) {
                                $confirmYellow += 1;
                                $yellowConfirmCek = FALSE;
                            }
                        } else if ($s['is_late_confirmation'] >= 1) {
                            $confirmRed += 1;
                            $confirmGreen -= 1;
                            $confirmCek = false;
                        }
                    }
                    //do
                    if ($doCek) {
                        if ($s['is_late_do'] == '') {
                            if ($yellowDoCek) {
                                $doYellow += 1;
                                $yellowDoCek = FALSE;
                            }

                        } else if ($s['is_late_do'] >= 1) {
                            $doRed += 1;
                            $doGreen -= 1;
                            $doCek = false;
                        }
                    }
                    //sppb
                    if ($sppbCek) {
                        if ($s['is_late_sppb'] == '') {
                            if ($yellowSppbCek) {
                                $sppbYellow += 1;
                                $yellowSppbCek = FALSE;
                            }
                        } else if ($s['is_late_sppb'] >= 1) {
                            $sppbRed += 1;
                            $sppbGreen -= 1;
                            $sppbCek = false;
                        }
                    }
                    // //penarikan
                    // if ($penarikanCek) {
                    //     if ($s['is_late_security_inbound']=='') {
                    //     }else if ($s['is_late_security_inbound']>= 1) {
                    //         $penarikanRed+=1;
                    //         $penarikanGreen-=1;
                    //         $penarikanCek=false;
                    //     }
                    // }
                    //bongkar
                    // if ($bongkarCek) {
                    //     if ($s['is_late_bongkar']=='') {
                    //         if ($yellowBongkarCek) {
                    //             $bongkarYellow+=1;
                    //             $yellowBongkarCek=FALSE;
                    //         }
                    //     }else if ($s['is_late_bongkar']>= 1) {
                    //         $bongkarRed+=1;
                    //         $bongkarGreen-=1;
                    //         $bongkarCek=false;
                    //     }
                    // }
                }
                // //penarikan
                // if ($s['is_late_security_inbound']=='') {
                //     $penarikanYellow+=1;
                // }else if ($s['is_late_security_inbound']>= 1) {
                //     $penarikanRed+=1;
                // }else{
                //     $penarikanGreen+=1;
                // }
                //bongkar
                // if ($s['is_late_bongkar']=='') {
                //     $bongkarYellow+=1;
                // }else if ($s['is_late_bongkar']>= 1) {
                //     $bongkarRed+=1;
                // }else{
                //     $bongkarGreen+=1;
                // }
                $totalnya += 1;
            }

            $serviceTimes['summary']['drafting']['merah'] = $draftingRed;
            $serviceTimes['summary']['drafting']['hijau'] = $draftingGreen;
            $serviceTimes['summary']['drafting']['kuning'] = $draftingYellow;

            if ($draftingGreen <= 0) {
                $serviceTimes['summary']['drafting']['persen'] = 0;
            } else {
                $serviceTimes['summary']['drafting']['persen'] = round(($draftingGreen / ($draftingGreen + $draftingRed)) * 100, 1);
            }


            $serviceTimes['summary']['confirm']['merah'] = $confirmRed;
            $serviceTimes['summary']['confirm']['hijau'] = $confirmGreen;
            $serviceTimes['summary']['confirm']['kuning'] = $confirmYellow;

            if ($confirmGreen <= 0) {
                $serviceTimes['summary']['confirm']['persen'] = 0;
            } else {
                $serviceTimes['summary']['confirm']['persen'] = round(($confirmGreen / ($confirmGreen + $confirmRed)) * 100, 1);
            }

            $serviceTimes['summary']['do']['merah'] = $doRed;
            $serviceTimes['summary']['do']['hijau'] = $doGreen;
            $serviceTimes['summary']['do']['kuning'] = $doYellow;
            if ($doGreen <= 0) {
                $serviceTimes['summary']['do']['persen'] = 0;
            } else {
                $serviceTimes['summary']['do']['persen'] = round(($doGreen / ($doGreen + $doRed)) * 100, 1);
            }

            $serviceTimes['summary']['sppb']['merah'] = $sppbRed;
            $serviceTimes['summary']['sppb']['hijau'] = $sppbGreen;
            $serviceTimes['summary']['sppb']['kuning'] = $sppbYellow;

            if ($sppbGreen <= 0) {
                $serviceTimes['summary']['sppb']['persen'] = 0;
            } else {
                $serviceTimes['summary']['sppb']['persen'] = round(($sppbGreen / ($sppbGreen + $sppbRed)) * 100, 1);
            }

            $serviceTimes['summary']['penarikan']['merah'] = $penarikanRed;
            $serviceTimes['summary']['penarikan']['hijau'] = $penarikanGreen;
            $serviceTimes['summary']['penarikan']['kuning'] = $penarikanYellow;

            if ($penarikanGreen <= 0) {
                $serviceTimes['summary']['penarikan']['persen'] = 0;
            } else {
                $serviceTimes['summary']['penarikan']['persen'] = round(($penarikanGreen / ($penarikanGreen + $penarikanRed)) * 100, 1);
            }

            $serviceTimes['summary']['bongkar']['merah'] = $bongkarRed;
            $serviceTimes['summary']['bongkar']['hijau'] = $bongkarGreen;
            $serviceTimes['summary']['bongkar']['kuning'] = $bongkarYellow;

            if ($bongkarGreen <= 0) {
                $serviceTimes['summary']['bongkar']['persen'] = 0;
            } else {
                $serviceTimes['summary']['bongkar']['persen'] = round(($bongkarGreen / ($bongkarGreen + $bongkarRed)) * 100, 1);
            }
        }
        // print_debug($serviceTimesSummary);

        if ($export) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'NO');
            $sheet->mergeCells('A1:A2');
            $sheet->setCellValue('B1', 'NO REFERENCE');
            $sheet->mergeCells('B1:B2');
            $sheet->setCellValue('C1', 'CUSTOMER NAME');
            $sheet->mergeCells('C1:C2');
            $sheet->setCellValue('D1', 'CUSTOMER');
            $sheet->setCellValue('E1', 'COMPLIANCE');
            $sheet->mergeCells('E1:F1');
            $sheet->setCellValue('G1', 'CUSTOMER');
            $sheet->setCellValue('H1', 'COMPLIANCE');
            $sheet->mergeCells('H1:I1');
            $sheet->setCellValue('J1', 'OPERATIONAL');
            $sheet->mergeCells('J1:N1');
            $sheet->setCellValue('O1', 'COMPLIANCE');
            $sheet->setCellValue('P1', 'SERVICE TIME');
            $sheet->mergeCells('P1:R1');


            $sheet->setCellValue('D2', 'UPLOAD DATE');
            $sheet->setCellValue('E2', 'DRAFT');
            $sheet->setCellValue('F2', 'ATA');
            $sheet->setCellValue('G2', 'CONFIRM');
            $sheet->setCellValue('H2', 'DO');
            $sheet->setCellValue('I2', 'SPPB');
            $sheet->setCellValue('J2', 'SECURITY IN');
            $sheet->setCellValue('K2', 'UNLOAD');
            $sheet->setCellValue('L2', 'STRIPPING');
            $sheet->setCellValue('M2', 'BONGKAR');
            $sheet->setCellValue('N2', 'BOOKING COMPLETE');
            $sheet->setCellValue('O2', 'SPPD');
            $sheet->setCellValue('P2', 'ST DOC');
            $sheet->setCellValue('Q2', 'ST INBOUND DELIVERY');
            $sheet->setCellValue('R2', 'ST UNLOADING');


            $sheet->getStyle('A1:C1')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getStyle('E1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('H1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('J1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('O1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet
                ->getStyle('A1:R2')
                ->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['rgb' => '000000']
                        ],
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => 'FFFFFF']
                        ]
                    ]
                );

            $sheet
                ->getStyle('D1:R1')
                ->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['rgb' => '777777']
                        ],
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => 'FFFFFF']
                        ],
                        'borders' => array(
                            'allBorders' => array(
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => array('rgb' => 'FFFFFF'),
                            ),
                        ),
                    ]
                );

            foreach ($serviceTimes as $index => &$serviceTime) {
                $fields = [
                    'D' . ($index + 3) => $serviceTime['is_late_upload'],
                    'E' . ($index + 3) => $serviceTime['is_late_draft'],
                    'F' . ($index + 3) => $serviceTime['is_late_confirmation'],
                    'H' . ($index + 3) => $serviceTime['is_late_do'],
                    'I' . ($index + 3) => $serviceTime['is_late_sppb'],
                    'J' . ($index + 3) => $serviceTime['is_late_security_inbound'],
                    'K' . ($index + 3) => $serviceTime['is_late_unload'],
                    'L' . ($index + 3) => $serviceTime['is_late_stripping'],
                    'M' . ($index + 3) => $serviceTime['is_late_bongkar'],
                    'N' . ($index + 3) => $serviceTime['is_late_completed'],
                    'P' . ($index + 3) => $serviceTime['is_late_sppd'],
                    'Q' . ($index + 3) => $serviceTime['is_late_st_inbound_delivery'],
                    'R' . ($index + 3) => $serviceTime['is_late_st_unload'],
                ];
                $sheet->setCellValue('B' . ($index + 3), $serviceTime['no_reference']);
                $sheet->setCellValue('C' . ($index + 3), $serviceTime['customer_name']);
                $sheet->setCellValue('D' . ($index + 3), $serviceTime['upload_date']);
                $sheet->setCellValue('E' . ($index + 3), $serviceTime['draft_date']);
                $sheet->setCellValue('F' . ($index + 3), $serviceTime['ata_date']);
                $sheet->setCellValue('G' . ($index + 3), $serviceTime['confirmation_date']);
                $sheet->setCellValue('H' . ($index + 3), $serviceTime['do_date']);
                $sheet->setCellValue('I' . ($index + 3), $serviceTime['sppb_date']);
                $sheet->setCellValue('J' . ($index + 3), $serviceTime['security_inbound']);
                $sheet->setCellValue('K' . ($index + 3), $serviceTime['unload_date']);
                $sheet->setCellValue('L' . ($index + 3), $serviceTime['stripping_date']);
                $sheet->setCellValue('M' . ($index + 3), $serviceTime['bongkar_date']);
                $sheet->setCellValue('N' . ($index + 3), $serviceTime['completed_date']);
                $sheet->setCellValue('O' . ($index + 3), $serviceTime['sppd_date']);
                $sheet->setCellValue('P' . ($index + 3), $serviceTime['service_time_doc']);
                $sheet->setCellValue('Q' . ($index + 3), $serviceTime['service_time_inbound_delivery_label']);
                $sheet->setCellValue('R' . ($index + 3), $serviceTime['service_time_unload_label']);

                foreach ($fields as $coor => $field) {
                    $sheet->getStyle($coor)
                        ->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'color' => ['rgb' => ($field == '' ? 'fcf8e3' : ($field >= 1 ? 'f2dede' : 'dff0d8'))]
                            ]
                        ]);
                }
                $sheet->getStyle('P' . ($index + 3))
                    ->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['rgb' => ($serviceTime['service_time_doc'] == '' ? 'fcf8e3' : ($serviceTime['service_time_doc'] = 1 ? 'f2dede' : 'dff0d8'))]
                        ]
                    ]);
            }

            $columnIterator = $sheet->getColumnIterator();
            foreach ($columnIterator as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="ST Control Inbound.xlsx"');
            $writer->save('php://output');
        } else {
            $selectedOwners = get_url_param('owner');
            $selectedBookings = get_url_param('booking');
            $owners = $this->peopleModel->getBy(['ref_people.id' => $selectedOwners]);
            $bookings = if_empty($this->booking->getBookingById($selectedBookings), []);


            foreach ($serviceTimes['data'] as &$serviceTime) {
                $totalRow = 0;
                foreach ($serviceTimes['data'] as $serviceTimeNext) {
                    if ($serviceTime['no_upload'] == $serviceTimeNext['no_upload']) {
                        $totalRow++;
                    }
                }
                $serviceTime['total_detail'] = $totalRow;
            }
            // print_debug($serviceTimes);

            $data = [
                'title' => "Report Service Time",
                'page' => "report/service_time_control_inbound",
                'reports' => $serviceTimes,
                'owners' => $owners,
                'bookings' => $bookings,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable.
     */
    public function data_by_color($isLate = 'is_late_draft', $color = 'green', $bound = 'inbound')
    {
        $filters[0] = $isLate;
        $filters[1] = $color;
        $filter = array_merge($_GET);

        unset($filter['page']);
        // print_debug($filter);
        if ($bound == 'inbound') {
            $serviceTimesSummary = $this->reportModel->getReportServiceTimeControlInbound($filter);
        } else {
            $serviceTimesSummary = $this->reportModel->getReportServiceTimeControlOutbound($filter);
        }
        if ($isLate == 'is_late_bongkar') {
            $totalnya = 0;
            $cek = '';
            foreach ($serviceTimesSummary as $s) {
                if ($cek != $s['no_upload']) {
                    $max = date("1970-01-01 00:00:00");
                    $is_late_bogkar = null;

                    $maxTemp = $max;
                    $max_un = $max;
                    $max_st = $max;
                    $status_st = 0;
                    $is_late_bongkar_x = '';
                    // if ($s['expedition_type']=='INTERNAL') {
                    $cekin = $s['no_upload'];
                    $loop = $totalnya;
                    do {
                        if ($serviceTimesSummary[$loop]['last_in_tci'] == null || $max == null) {
                            $max = null;
                            $is_late_bogkar = '';
                        } else {
                            if ($maxTemp < $serviceTimesSummary[$loop]['last_in_tci']) {
                                $maxTemp = $serviceTimesSummary[$loop]['last_in_tci'];
                                $is_late_bogkar = $serviceTimesSummary[$loop]['is_late_bongkar'];
                            }
                        }
                        //dengan last stripping or unload
                        if ($serviceTimesSummary[$loop]['is_stripping'] == null || $serviceTimesSummary[$loop]['bongkar_date'] == NULL || $serviceTimesSummary[$loop]['last_in_tci'] == NULL) {
                            $is_stripping = null;
                            $status_st = '';
                        } else {
                            if ($serviceTimesSummary[$loop]['is_stripping'] == 0 && $status_st === 0) {
                                $max_un = $serviceTimesSummary[$loop]['terakhir_unload'];
                            } else if ($status_st === 0 || $status_st == 1) {
                                $status_st = 1;
                                if ($max_st < $serviceTimesSummary[$loop]['terakhir_stripping']) {
                                    $max_st = $serviceTimesSummary[$loop]['terakhir_stripping'];
                                }
                            }
                        }
                        $loop++;
                        if (!isset($serviceTimesSummary[$loop]['no_upload'])) {
                            break;
                        }
                    } while ($cekin == $serviceTimesSummary[$loop]['no_upload']);

                    if ($status_st === '') {
                        $is_late_bongkar_x = '';
                    } else {

                        if ($status_st) {
                            $tmp = date('Y-m-d', strtotime($maxTemp));
                            if (date('H:i:s', strtotime($maxTemp)) < '12:00:00') {
                                $is_late_bongkar_x = ($max_st > date('Y-m-d', strtotime($maxTemp)) . ' 23:59:59') ? 1 : 0;
                            } else {
                                $is_late_bongkar_x = ($max_st > date('Y-m-d', strtotime('+1 days', strtotime($tmp))) . ' 12:00:00') ? 1 : 0;

                            }
                        } else {
                            $tmp = date('Y-m-d', strtotime($maxTemp));
                            if (date('H:i:s', strtotime($maxTemp)) < '12:00:00') {
                                $is_late_bongkar_x = ($max_un > date('Y-m-d', strtotime($maxTemp)) . ' 23:59:59') ? 1 : 0;
                            } else {
                                $is_late_bongkar_x = ($max_un > date('Y-m-d', strtotime('+1 days', strtotime($tmp))) . ' 12:00:00') ? 1 : 0;
                                // if ($s['no_upload']=='UP/19/08/000141') {
                                //     print_debug($is_late_bongkar_x);
                                // }
                            }
                        }
                    }
                    if ($color != 'red') {
                        $cekin = $s['no_upload'];
                        $loop = $totalnya;
                        do {
                            if ($status_st) {
                                $serviceTimesSummary[$loop]['bongkar_date_last'] = $max_st;
                            } else {
                                $serviceTimesSummary[$loop]['bongkar_date_last'] = $max_un;
                            }
                            $serviceTimesSummary[$loop]['last_in_tci_max'] = $maxTemp;
                            $serviceTimesSummary[$loop]['is_late_bongkar'] = $is_late_bogkar;
                            $serviceTimesSummary[$loop]['is_late_bongkar_last'] = $is_late_bongkar_x;
                            $loop++;
                            if (!isset($serviceTimesSummary[$loop]['no_upload'])) {
                                break;
                            }
                        } while ($cekin == $serviceTimesSummary[$loop]['no_upload']);
                    } else {
                        $cekin = $s['no_upload'];
                        $loop = $totalnya;
                        do {
                            $serviceTimesSummary[$loop]['is_late_bongkar_last'] = $is_late_bongkar_x;
                            $loop++;
                            if (!isset($serviceTimesSummary[$loop]['no_upload'])) {
                                break;
                            }
                        } while ($cekin == $serviceTimesSummary[$loop]['no_upload']);
                    }
                    $cek = $s['no_upload'];
                }
                $totalnya += 1;
            }
        }
        if ($isLate == 'is_late_security_inbound' && $color != 'red') {
            $totalnya = 0;
            $cek = '';
            foreach ($serviceTimesSummary as $s) {
                if ($cek != $s['no_upload']) {
                    $max = date("1970-01-01 00:00:00");
                    $is_late_security_inbound = null;
                    // if ($s['expedition_type']=='INTERNAL') {
                    $cekin = $s['no_upload'];
                    $loop = $totalnya;
                    do {
                        if ($serviceTimesSummary[$loop]['last_in_tci'] == null || $max == null) {
                            $max = null;
                            $is_late_security_inbound = '';
                        } else {
                            if ($max < $serviceTimesSummary[$loop]['last_in_tci']) {
                                $max = $serviceTimesSummary[$loop]['last_in_tci'];
                                $is_late_security_inbound = $serviceTimesSummary[$loop]['is_late_security_inbound'];
                            }
                        }
                        $loop++;
                        if (!isset($serviceTimesSummary[$loop]['no_upload'])) {
                            break;
                        }
                    } while ($cekin == $serviceTimesSummary[$loop]['no_upload']);
                    $cekin = $s['no_upload'];
                    $loop = $totalnya;
                    do {
                        $serviceTimesSummary[$loop]['is_late_security_inbound'] = $is_late_security_inbound;
                        $loop++;
                        if (!isset($serviceTimesSummary[$loop]['no_upload'])) {
                            break;
                        }
                    } while ($cekin == $serviceTimesSummary[$loop]['no_upload']);
                    // }

                    $cek = $s['no_upload'];
                }
                $totalnya += 1;
            }
        }
        if ($isLate == 'is_late_booking_complete') {
            $totalnya = 0;
            $cek = '';
            foreach ($serviceTimesSummary as $s) {
                if ($cek != $s['no_upload']) {
                    $max = date("1970-01-01 00:00:00");
                    $is_late_booking_complete = null;
                    // if ($s['expedition_type']=='INTERNAL') {
                    $cekin = $s['no_upload'];
                    $loop = $totalnya;
                    do {
                        if ($serviceTimesSummary[$loop]['muat_internal_external'] == null || $max == null) {
                            $max = null;
                            $is_late_booking_complete = '';
                        } else {
                            if ($max < $serviceTimesSummary[$loop]['muat_internal_external']) {
                                $max = $serviceTimesSummary[$loop]['muat_internal_external'];
                                $is_late_booking_complete = $serviceTimesSummary[$loop]['is_late_booking_complete'];
                            }
                        }
                        $loop++;
                        if (!isset($serviceTimesSummary[$loop]['no_upload'])) {
                            break;
                        }
                    } while ($cekin == $serviceTimesSummary[$loop]['no_upload']);
                    $cekin = $s['no_upload'];
                    $loop = $totalnya;
                    do {
                        $serviceTimesSummary[$loop]['is_late_booking_complete'] = $is_late_booking_complete;
                        $serviceTimesSummary[$loop]['muat_internal_external_max'] = $max;
                        $loop++;
                        if (!isset($serviceTimesSummary[$loop]['no_upload'])) {
                            break;
                        }
                    } while ($cekin == $serviceTimesSummary[$loop]['no_upload']);
                    // }

                    $cek = $s['no_upload'];
                }
                $totalnya += 1;
            }
        }
        $reports = $serviceTimesSummary;
        $red = 0;
        $green = 0;
        $yellow = 0;
        $cek = '';
        $draftCek = TRUE;
        $yellowCek = TRUE;
        $arrayRed = [];
        $arrayGreen = [];
        $arrayYellow = [];

        $i = 0;

        $cekBongkar = '';
        $cekPenarikan = '';
        $cekKuning = '';
        // $greenNo='';
        $cekHijau = 0;

        $cekMaxTime = '';
        $cekStatus = '';

        $noRed = 0;
        foreach ($reports as $s) {
            if ($isLate == 'is_late_security_inbound' && $color == 'red') {
                $is_late_security_inbound = $s['is_late_security_inbound'];
                $cekin = $s['no_upload'];
                $loop = $i;
                if ($cek != $s['no_upload']) {
                    $cekHijau = 0;
                    $no_upload = '';
                }
                do {
                    if ($reports[$loop]['last_in_tci'] == null) {
                        $no_upload = $reports[$loop]['no_upload'];
                        // print_debug($reports[$loop]['no_upload']);
                        // if ($s['no_upload']=='UP/19/08/000124') {
                        // print_debug($no_upload);
                        // }
                    }
                    if ($reports[$loop]['is_late_security_inbound'] >= 1) {
                        $cekHijau = $reports[$loop]['is_late_security_inbound'];
                        // print_debug($reports[$loop]['no_upload']);
                    }
                    $loop++;
                    if (!isset($reports[$loop]['no_upload'])) {
                        break;
                    }
                } while ($cekin == $reports[$loop]['no_upload']);
                if ($no_upload != $s['no_upload']) {
                    if ($cekHijau >= 1) {
                        $arrayRed[$red] = $reports[$i];
                        if ($cekPenarikan != $s['no_upload']) {
                            $arrayRed[$red]['no'] = (++$noRed);
                        } else {
                            $arrayRed[$red]['no'] = $arrayRed[$red - 1]['no'];
                        }
                        $cekPenarikan = $s['no_upload'];
                        $red += 1;
                    }
                }
                $cek = $s['no_upload'];
            } else if ($isLate == 'is_late_bongkar' && $color == 'red') {

                $is_late_bongkar = $s['is_late_bongkar'];
                $cekin = $s['no_upload'];
                $loop = $i;
                if ($cek != $s['no_upload']) {
                    $cekHijau = 0;
                    $no_upload = '';
                    $cekStatus = '';
                    $cekMaxTime = '';
                    $max = date("1970-01-01 00:00:00");
                }

                do {
                    if ($reports[$loop]['last_in_tci'] == null) {
                        $no_upload = $reports[$loop]['no_upload'];
                    }
                    // if ($reports[$loop]['is_late_bongkar']>=1) {
                    //     $cekHijau=$reports[$loop]['is_late_bongkar'];
                    // }
                    //dengan last stripping
                    if ($reports[$loop]['is_late_bongkar_last'] >= 1) {
                        $cekHijau = $reports[$loop]['is_late_bongkar_last'];
                    }
                    if ($max < $reports[$loop]['last_in_tci']) {
                        $max = $reports[$loop]['last_in_tci'];
                        $cekMaxTime = $reports[$loop]['no_upload'];
                        // $cekStatus=$reports[$loop]['is_late_bongkar'];
                        //dengan last stripping
                        $cekStatus = $reports[$loop]['is_late_bongkar_last'];
                    }

                    $loop++;
                    if (!isset($reports[$loop]['no_upload'])) {
                        break;
                    }
                } while ($cekin == $reports[$loop]['no_upload']);
                if ($no_upload != $s['no_upload']) {
                    if ($cekHijau >= 1) {
                        if ($cekMaxTime == $s['no_upload'] && $cekStatus >= 1) {
                            $arrayRed[$red] = $reports[$i];
                            if ($cekBongkar != $s['no_upload']) {
                                $arrayRed[$red]['no'] = (++$noRed);
                            } else {
                                $arrayRed[$red]['no'] = $arrayRed[$red - 1]['no'];
                            }
                            $cekBongkar = $s['no_upload'];
                            $red += 1;
                        }

                    }
                }
                $cek = $s['no_upload'];
                // // if ($s[$isLate]>= 1) {
                //     $arrayRed[$red]=$reports[$i];
                //     if ($cekBongkar!=$s['no_upload']) {
                //         $arrayRed[$red]['no']=(++$noRed);
                //     } else {
                //         $arrayRed[$red]['no']=$arrayRed[$red-1]['no'];
                //     }
                //     $cekBongkar=$s['no_upload'];
                //     $red+=1;
                // // }
            } else {
                if ($cek != $s['no_upload']) {
                    if ($isLate == 'is_late_bongkar') {
                        // if ($s[$filters[0]]=='') {
                        //     $arrayYellow[$yellow]=$reports[$i];
                        //     $arrayYellow[$yellow]['no']=($yellow+1);
                        //     $yellow+=1;
                        //     $draftCek=false;
                        // }else if ($s[$filters[0]]>= 1) {
                        //     $arrayRed[$red]=$reports[$i];
                        //     $arrayRed[$red]['no']=($red+1);
                        //     $red+=1;
                        //     $draftCek=false;
                        // }else{
                        //     $arrayGreen[$green]=$reports[$i];
                        //     $arrayGreen[$green]['no']=($green+1);
                        //     $green+=1;
                        //     $draftCek=false;
                        // }
                        //dengan last stripping
                        if ($s['is_late_bongkar_last'] === '') {
                            $arrayYellow[$yellow] = $reports[$i];
                            $arrayYellow[$yellow]['no'] = ($yellow + 1);
                            $yellow += 1;
                            $draftCek = false;
                        } else if ($s['is_late_bongkar_last'] >= 1) {
                            $arrayRed[$red] = $reports[$i];
                            $arrayRed[$red]['no'] = ($red + 1);
                            $red += 1;
                            $draftCek = false;
                        } else {
                            $arrayGreen[$green] = $reports[$i];
                            $arrayGreen[$green]['no'] = ($green + 1);
                            $green += 1;
                            $draftCek = false;
                        }
                    } else {
                        //draft
                        if ($s[$filters[0]] == '') {
                            $arrayYellow[$yellow] = $reports[$i];
                            $arrayYellow[$yellow]['no'] = ($yellow + 1);
                            $yellow += 1;
                            $yellowCek = FALSE;
                            $draftCek = TRUE;
                        } else if ($s[$filters[0]] >= 1) {
                            $arrayRed[$red] = $reports[$i];
                            $arrayRed[$red]['no'] = ($red + 1);
                            $red += 1;
                            // $serviceTimesSummary[$totalnya]['Red']=$draftingRed;
                            $draftCek = false;
                        } else {
                            $arrayGreen[$green] = $reports[$i];
                            $arrayGreen[$green]['no'] = ($green + 1);
                            $green += 1;
                            $draftCek = TRUE;
                            // print_r($draftingGreen);
                        }
                        # code...
                    }
                    $cek = $s['no_upload'];
                } else {
                    //draft
                    if ($isLate != 'is_late_booking_complete') {
                        if ($draftCek) {
                            if ($s[$filters[0]] == '') {
                                if ($yellowCek) {
                                    $arrayYellow[$yellow] = $reports[$i];
                                    $arrayYellow[$yellow]['no'] = ($yellow + 1);
                                    $yellow += 1;
                                    $yellowCek = FALSE;
                                }
                            } else if ($s[$filters[0]] >= 1) {
                                $arrayRed[$red] = $reports[$i];
                                $arrayRed[$red]['no'] = ($red + 1);
                                $red += 1;
                                unset($arrayGreen[$green]);
                                $green -= 1;
                                $draftCek = false;
                                // $serviceTimesSummary[$totalnya]['Red']=$draftingRed;
                            }
                        }
                    }

                }
            }
            $i++;
        }
        if ($filters[1] == 'red') {
            $data = [
                "data" => $arrayRed
            ];
        }
        if ($filters[1] == 'green') {
            $data = [
                "data" => $arrayGreen
            ];
        }
        if ($filters[1] == 'yellow') {
            $data = [
                "data" => $arrayYellow
            ];
        }

        // print_debug($data);
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * Show report service control inbound.
     */
    public function service_time_control_outbound()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_SERVICE_TIME_CONTROL_OUT);

        $filters = array_merge($_GET, ['page' => get_url_param('page', 1)]);

        $export = $this->input->get('export');
        if ($export) unset($filters['page']);

        $serviceTimes = $this->reportModel->getReportServiceTimeControlOutbound($filters);
        if (!$export) {
            unset($filters['page']);
            $draftingRed = 0;
            $draftingGreen = 0;
            $draftingYellow = 0;
            $yellowDraftCek = true;
            $draftCek = true;

            $confirmRed = 0;
            $confirmGreen = 0;
            $confirmYellow = 0;
            $yellowConfirmCek = true;
            $confirmCek = true;

            $billingRed = 0;
            $billingGreen = 0;
            $billingYellow = 0;
            $yellowBillingCek = true;
            $billingCek = true;

            $sppbRed = 0;
            $sppbGreen = 0;
            $sppbYellow = 0;
            $yellowSppbCek = true;
            $sppbCek = true;

            $paymentRed = 0;
            $paymentGreen = 0;
            $paymentYellow = 0;
            $yellowPaymentCek = true;
            $paymentCek = true;

            $muatRed = 0;
            $muatGreen = 0;
            $muatYellow = 0;
            $yellowMuatCek = true;
            $muatCek = true;

            $totalnya = 0;
            $cek = '';
            $serviceTimesSummary = $this->reportModel->getReportServiceTimeControlOutbound($filters);

            foreach ($serviceTimesSummary as $s) {
                if ($cek != $s['no_upload']) {
                    //draft
                    if ($s['is_late_draft'] == '') {
                        $draftingYellow += 1;
                        $yellowDraftCek = FALSE;
                        $draftCek = TRUE;
                    } else if ($s['is_late_draft'] >= 1) {
                        $draftingRed += 1;
                        $draftCek = false;
                    } else {
                        $draftingGreen += 1;
                        $draftCek = TRUE;
                    }
                    //confirm
                    if ($s['is_late_confirmation'] == '') {
                        $confirmYellow += 1;
                        $yellowConfirmCek = FALSE;
                        $confirmCek = TRUE;
                    } else if ($s['is_late_confirmation'] >= 1) {
                        $confirmRed += 1;
                        $confirmCek = false;
                    } else {
                        $confirmGreen += 1;
                        $confirmCek = TRUE;
                    }
                    //billing
                    if ($s['is_late_billing'] == '') {
                        $billingYellow += 1;
                        $yellowBillingCek = FALSE;
                        $billingCek = TRUE;
                    } else if ($s['is_late_billing'] >= 1) {
                        $billingRed += 1;
                        $billingCek = false;
                    } else {
                        $billingGreen += 1;
                        $billingCek = TRUE;
                    }
                    //sppb
                    if ($s['is_late_sppb'] == '') {
                        $sppbYellow += 1;
                        $yellowSppbCek = FALSE;
                        $sppbCek = TRUE;
                    } else if ($s['is_late_sppb'] >= 1) {
                        $sppbRed += 1;
                        $sppbCek = false;
                    } else {
                        $sppbGreen += 1;
                        $sppbCek = TRUE;
                    }
                    //payment
                    if ($s['is_late_payment'] == '') {
                        $paymentYellow += 1;
                        $yellowPaymentCek = FALSE;
                        $paymentCek = TRUE;
                    } else if ($s['is_late_payment'] >= 1) {
                        $paymentRed += 1;
                        $paymentCek = false;
                    } else {
                        $paymentGreen += 1;
                        $paymentCek = TRUE;
                    }
                    //muat
                    $max = date("1970-01-01 00:00:00");
                    $maxTemp = $max;
                    $is_late_booking_complete = null;
                    // if ($s['expedition_type']=='INTERNAL') {
                    $cekin = $s['no_upload'];
                    $loop = $totalnya;
                    do {
                        if ($serviceTimesSummary[$loop]['muat_internal_external'] == null || $max == null) {
                            $max = null;
                            $is_late_booking_complete = '';
                        } else {
                            if (strtotime($maxTemp) < strtotime($serviceTimesSummary[$loop]['muat_internal_external'])) {
                                $maxTemp = $serviceTimesSummary[$loop]['muat_internal_external'];
                                $is_late_booking_complete = $serviceTimesSummary[$loop]['is_late_booking_complete'];
                            }
                        }
                        $loop++;
                        if (!isset($serviceTimesSummary[$loop]['no_upload'])) {
                            break;
                        }
                    } while ($cekin == $serviceTimesSummary[$loop]['no_upload']);
                    $cekin = $s['no_upload'];
                    $loop = $totalnya;
                    do {
                        $serviceTimesSummary[$loop]['is_late_booking_complete'] = $is_late_booking_complete;
                        $serviceTimesSummary[$loop]['muat_internal_external_max'] = $maxTemp;
                        $loop++;
                        if (!isset($serviceTimesSummary[$loop]['no_upload'])) {
                            break;
                        }
                    } while ($cekin == $serviceTimesSummary[$loop]['no_upload']);
                    $y = 0;
                    foreach ($serviceTimes['data'] as $data) {
                        if ($data['no_upload'] == $cekin) {
                            $serviceTimes['data'][$y]['is_late_booking_complete'] = $is_late_booking_complete;
                            if ($maxTemp == '1970-01-01 00:00:00') {
                                $maxTemp = null;
                            }
                            $serviceTimes['data'][$y]['muat_internal_external_max'] = $maxTemp;
                        }
                        $y++;
                    }
                    //muat
                    if ($serviceTimesSummary[$totalnya]['is_late_booking_complete'] == '') {
                        $muatYellow += 1;
                    } else if ($serviceTimesSummary[$totalnya]['is_late_booking_complete'] >= 1) {
                        $muatRed += 1;
                    } else {
                        $muatGreen += 1;
                    }
                    $cek = $s['no_upload'];
                } else {
                    //draft
                    if ($draftCek) {
                        if ($s['is_late_draft'] == '') {
                            if ($yellowDraftCek) {
                                $draftingYellow += 1;
                                $yellowDraftCek = FALSE;
                            }
                        } else if ($s['is_late_draft'] >= 1) {
                            $draftingRed += 1;
                            $draftingGreen -= 1;
                            $draftCek = false;
                            // $serviceTimesSummary[$totalnya]['Red']=$draftingRed;
                        }
                    }
                    //comfirm
                    if ($confirmCek) {
                        if ($s['is_late_confirmation'] == '') {
                            if ($yellowConfirmCek) {
                                $confirmYellow += 1;
                                $yellowConfirmCek = FALSE;
                            }
                        } else if ($s['is_late_confirmation'] >= 1) {
                            $confirmRed += 1;
                            $confirmGreen -= 1;
                            $confirmCek = false;
                        }
                    }
                    //billing
                    if ($billingCek) {
                        if ($s['is_late_billing'] == '') {
                            if ($yellowBillingCek) {
                                $billingYellow += 1;
                                $yellowBillingCek = FALSE;
                            }
                        } else if ($s['is_late_billing'] >= 1) {
                            $billingRed += 1;
                            $billingGreen -= 1;
                            $billingCek = false;
                        }
                    }
                    //sppb
                    if ($sppbCek) {
                        if ($s['is_late_sppb'] == '') {
                            if ($yellowSppbCek) {
                                $sppbYellow += 1;
                                $yellowSppbCek = FALSE;
                            }
                        } else if ($s['is_late_sppb'] >= 1) {
                            $sppbRed += 1;
                            $sppbGreen -= 1;
                            $sppbCek = false;
                        }
                    }
                    //payment
                    if ($paymentCek) {
                        if ($s['is_late_payment'] == '') {
                            if ($yellowPaymentCek) {
                                $paymentYellow += 1;
                                $yellowPaymentCek = FALSE;
                            }
                        } else if ($s['is_late_payment'] >= 1) {
                            $paymentRed += 1;
                            $paymentGreen -= 1;
                            $paymentCek = false;
                        }
                    }
                    //muat
                    // if ($muatCek) {
                    //     if ($s['is_late_booking_complete']=='') {
                    //         if ($yellowMuatCek) {
                    //             $muatYellow+=1;
                    //             $yellowMuatCek=FALSE;
                    //         }
                    //     }else if ($s['is_late_booking_complete']>= 1) {
                    //         $muatRed+=1;
                    //         $muatGreen-=1;
                    //         $muatCek=false;
                    //     }
                    // }
                }
                $totalnya += 1;
            }
            $serviceTimes['summary']['drafting']['merah'] = $draftingRed;
            $serviceTimes['summary']['drafting']['hijau'] = $draftingGreen;
            $serviceTimes['summary']['drafting']['kuning'] = $draftingYellow;

            if ($draftingGreen <= 0) {
                $serviceTimes['summary']['drafting']['persen'] = 0;
            } else {
                $serviceTimes['summary']['drafting']['persen'] = round(($draftingGreen / ($draftingGreen + $draftingRed)) * 100, 1);
            }

            $serviceTimes['summary']['confirm']['merah'] = $confirmRed;
            $serviceTimes['summary']['confirm']['hijau'] = $confirmGreen;
            $serviceTimes['summary']['confirm']['kuning'] = $confirmYellow;

            if ($confirmGreen <= 0) {
                $serviceTimes['summary']['confirm']['persen'] = 0;
            } else {
                $serviceTimes['summary']['confirm']['persen'] = round(($confirmGreen / ($confirmGreen + $confirmRed)) * 100, 1);
            }

            $serviceTimes['summary']['billing']['merah'] = $billingRed;
            $serviceTimes['summary']['billing']['hijau'] = $billingGreen;
            $serviceTimes['summary']['billing']['kuning'] = $billingYellow;

            if ($billingGreen <= 0) {
                $serviceTimes['summary']['billing']['persen'] = 0;
            } else {
                $serviceTimes['summary']['billing']['persen'] = round(($billingGreen / ($billingGreen + $billingRed)) * 100, 1);
            }

            $serviceTimes['summary']['sppb']['merah'] = $sppbRed;
            $serviceTimes['summary']['sppb']['hijau'] = $sppbGreen;
            $serviceTimes['summary']['sppb']['kuning'] = $sppbYellow;

            if ($sppbGreen <= 0) {
                $serviceTimes['summary']['sppb']['persen'] = 0;
            } else {
                $serviceTimes['summary']['sppb']['persen'] = round(($sppbGreen / ($sppbGreen + $sppbRed)) * 100, 1);
            }

            $serviceTimes['summary']['payment']['merah'] = $paymentRed;
            $serviceTimes['summary']['payment']['hijau'] = $paymentGreen;
            $serviceTimes['summary']['payment']['kuning'] = $paymentYellow;

            if ($paymentGreen <= 0) {
                $serviceTimes['summary']['payment']['persen'] = 0;
            } else {
                $serviceTimes['summary']['payment']['persen'] = round(($paymentGreen / ($paymentGreen + $paymentRed)) * 100, 1);
            }

            $serviceTimes['summary']['muat']['merah'] = $muatRed;
            $serviceTimes['summary']['muat']['hijau'] = $muatGreen;
            $serviceTimes['summary']['muat']['kuning'] = $muatYellow;

            if ($muatGreen <= 0) {
                $serviceTimes['summary']['muat']['persen'] = 0;
            } else {
                $serviceTimes['summary']['muat']['persen'] = round(($muatGreen / ($muatGreen + $muatRed)) * 100, 1);
            }
        }
        // print_debug($serviceTimesSummary);
        if ($export) {
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();

            $sheet->setCellValue('A1', 'NO');
            $sheet->mergeCells('A1:A2');
            $sheet->setCellValue('B1', 'NO REFERENCE');
            $sheet->mergeCells('B1:B2');
            $sheet->setCellValue('C1', 'INBOUND REFERENCE');
            $sheet->mergeCells('C1:C2');
            $sheet->setCellValue('D1', 'CUSTOMER NAME');
            $sheet->mergeCells('D1:D2');
            $sheet->setCellValue('E1', 'CUSTOMER');
            $sheet->setCellValue('F1', 'COMPLIANCE');
            $sheet->setCellValue('G1', 'CUSTOMER');
            $sheet->setCellValue('H1', 'COMPLIANCE');
            $sheet->setCellValue('I1', 'CUSTOMER');
            $sheet->mergeCells('I1:J1');
            $sheet->setCellValue('K1', 'COMPLIANCE');
            $sheet->setCellValue('L1', 'OPERATIONAL');
            $sheet->mergeCells('L1:P1');
            $sheet->setCellValue('Q1', 'SERVICE TIME');


            $sheet->setCellValue('E2', 'UPLOAD DATE');
            $sheet->setCellValue('F2', 'DRAFT');
            $sheet->setCellValue('G2', 'CONFIRM');
            $sheet->setCellValue('H2', 'BILLING');
            $sheet->setCellValue('I2', 'PAYMENT');
            $sheet->setCellValue('J2', 'BPN');
            $sheet->setCellValue('K2', 'SPPB');
            $sheet->setCellValue('L2', 'SECURITY IN');
            $sheet->setCellValue('M2', 'SECURITY OUT');
            $sheet->setCellValue('N2', 'STUFFING/LOAD');
            $sheet->setCellValue('O2', 'GATE OUT');
            $sheet->setCellValue('P2', 'MUAT');
            $sheet->setCellValue('Q2', 'ST LOADING');


            $sheet->getStyle('A1:D1')->getAlignment()
                ->setHorizontal(Alignment::HORIZONTAL_LEFT)
                ->setVertical(Alignment::VERTICAL_CENTER);

            $sheet->getStyle('I1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);
            $sheet->getStyle('L1')->getAlignment()->setHorizontal(Alignment::HORIZONTAL_CENTER);

            $sheet
                ->getStyle('A1:Q2')
                ->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['rgb' => '000000']
                        ],
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => 'FFFFFF']
                        ]
                    ]
                );

            $sheet
                ->getStyle('E1:Q2')
                ->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['rgb' => '777777']
                        ],
                        'font' => [
                            'bold' => true,
                            'color' => ['rgb' => 'FFFFFF']
                        ],
                        'borders' => array(
                            'allBorders' => array(
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => array('rgb' => 'FFFFFF'),
                            ),
                        ),
                    ]
                );

            foreach ($serviceTimes as $index => &$serviceTime) {
                $fields = [
                    'F' . ($index + 3) => $serviceTime['is_late_draft'],
                    'G' . ($index + 3) => $serviceTime['is_late_confirmation'],
                    'H' . ($index + 3) => $serviceTime['is_late_billing'],
                    'I' . ($index + 3) => $serviceTime['is_late_payment'],
                    'J' . ($index + 3) => $serviceTime['is_late_bpn'],
                    'K' . ($index + 3) => $serviceTime['is_late_sppb'],
                    'N' . ($index + 3) => $serviceTime['is_late_load'],
                    'O' . ($index + 3) => $serviceTime['is_late_gate_out'],
                    'P' . ($index + 3) => $serviceTime['is_late_booking_complete'],
                    'Q' . ($index + 3) => $serviceTime['is_late_st_load'],
                ];
                $sheet->setCellValue('B' . ($index + 3), $serviceTime['no_reference']);
                $sheet->setCellValue('C' . ($index + 3), $serviceTime['booking_in_reference']);
                $sheet->setCellValue('D' . ($index + 3), $serviceTime['customer_name']);
                $sheet->setCellValue('E' . ($index + 3), $serviceTime['upload_date']);
                $sheet->setCellValue('F' . ($index + 3), $serviceTime['draft_date']);
                $sheet->setCellValue('G' . ($index + 3), $serviceTime['confirmation_date']);
                $sheet->setCellValue('H' . ($index + 3), $serviceTime['billing_date']);
                $sheet->setCellValue('I' . ($index + 3), $serviceTime['payment_date']);
                $sheet->setCellValue('J' . ($index + 3), $serviceTime['bpn_date']);
                $sheet->setCellValue('K' . ($index + 3), $serviceTime['sppb_date']);
                $sheet->setCellValue('L' . ($index + 3), $serviceTime['tep_checked_in_date']);
                $sheet->setCellValue('M' . ($index + 3), $serviceTime['security_out_date']);
                $sheet->setCellValue('N' . ($index + 3), $serviceTime['load_date']);
                $sheet->setCellValue('O' . ($index + 3), $serviceTime['gate_out_date']);
                $sheet->setCellValue('P' . ($index + 3), $serviceTime['booking_complete']);
                $sheet->setCellValue('Q' . ($index + 3), $serviceTime['service_time_load_label']);

                foreach ($fields as $coor => $field) {
                    $sheet->getStyle($coor)
                        ->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'color' => ['rgb' => ($field == '' ? 'fcf8e3' : ($field >= 1 ? 'f2dede' : 'dff0d8'))]
                            ]
                        ]);
                }
            }

            $columnIterator = $sheet->getColumnIterator();
            foreach ($columnIterator as $column) {
                $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
            }

            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="ST Control Outbound.xlsx"');
            $writer->save('php://output');
        } else {
            $selectedOwners = get_url_param('owner');
            $selectedBookings = get_url_param('booking');
            $owners = $this->peopleModel->getBy(['ref_people.id' => $selectedOwners]);
            $bookings = $this->booking->getBookingById($selectedBookings);

            foreach ($serviceTimes['data'] as &$serviceTime) {
                $totalRow = 0;
                foreach ($serviceTimes['data'] as $serviceTimeNext) {
                    if ($serviceTime['no_upload'] == $serviceTimeNext['no_upload']) {
                        $totalRow++;
                    }
                }
                $serviceTime['total_detail'] = $totalRow;
            }

            $data = [
                'title' => "Report Service Time",
                'page' => "report/service_time_control_outbound",
                'reports' => $serviceTimes,
                'owners' => $owners,
                'bookings' => $bookings,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Show report stock mutation container.
     */
    public function stock_mutation_container()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_STOCK_MOVEMENT);

        $filter = get_url_param('filter_container') ? $_GET : [];
        $export = get_url_param('export');

        if (!empty($filter) || $export) {
            $reportMutationContainers = $this->reportModel->getReportMutationContainer($filter);
        } else {
            $reportMutationContainers = [];
        }

        $selectedOwners = key_exists('owner', $filter) ? $filter['owner'] : [0];
        $selectedContainers = key_exists('container', $filter) ? $filter['container'] : [0];

        $owners = $this->peopleModel->getById($selectedOwners);
        $containers = $this->containerModel->getById($selectedContainers);
        $booking = $this->booking->getBookingById(get_url_param('bookings'));

        if (!empty($export)) {
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=mutation_container.xls");
            $this->load->view('report/_plain_mutation_container', [
                'stockMutationContainers' => $reportMutationContainers,
            ]);
        } else {
            $data = [
                'title' => "Report Mutation",
                'page' => "report/stock_mutation_container",
                'owners' => $owners,
                'containers' => $containers,
                'booking' => $booking,
                'stockMutationContainers' => $reportMutationContainers,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Show report stock mutation goods.
     */
    public function stock_mutation_goods()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_STOCK_MOVEMENT);

        $filter = get_url_param('filter_goods') ? $_GET : [];
        $export = get_url_param('export');

        if (!empty($filter) || $export) {
            $reportMutationGoods = $this->reportModel->getReportMutationGoods($filter);
        } else {
            $reportMutationGoods = [];
        }

        $selectedOwners = key_exists('owner', $filter) ? $filter['owner'] : [0];
        $selectedGoods = key_exists('goods', $filter) ? $filter['goods'] : [0];

        $owners = $this->peopleModel->getById($selectedOwners);
        $goods = $this->goodsModel->getById($selectedGoods);
        $booking = $this->booking->getBookingById(get_url_param('bookings'));

        if (!empty($export)) {
            header("Content-type: application/vnd-ms-excel");
            header("Content-Disposition: attachment; filename=mutation_container.xls");
            $this->load->view('report/_plain_mutation_goods', [
                'stockMutationGoods' => $reportMutationGoods,
            ]);
        } else {
            $data = [
                'title' => "Report Mutation",
                'page' => "report/stock_mutation_goods",
                'owners' => $owners,
                'goods' => $goods,
                'booking' => $booking,
                'stockMutationGoods' => $reportMutationGoods,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Show report movement goods.
     */
    public function movement_goods()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_STOCK_MOVEMENT);

        $filter = get_url_param('filter') ? $_GET : [];
        $export = get_url_param('export');

        if (!empty($filter) || $export) {
            $goodsMovements = $this->reportMovement->getMovementGoods($filter);
        } else {
            $goodsMovements = [];
        }

        $customer = $this->peopleModel->getById(get_url_param('customer'));
        $items = $this->goodsModel->getById(get_url_param('goods'));
        $booking = $this->booking->getBookingById(get_url_param('booking'));

        if (!empty($export)) {
            $this->reportMovement->exportMovementGoods($customer['name'], $filter, $goodsMovements);
        } else {
            $data = [
                'title' => "Report Movement",
                'page' => "report/movement_goods",
                'selectedCustomer' => $customer,
                'selectedItems' => $items,
                'selectedBooking' => $booking,
                'goodsMovements' => $goodsMovements,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get stock tracking by no reference.
     */
    public function stock_comparator()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_STOCK_COMPARATOR);

        $bookingId = get_url_param('booking');
        $selectedContainers = get_url_param('containers');
        $selectedGoods = get_url_param('items');

        $bookingIn = $this->booking->getBookingById($bookingId);
        $bookingOuts = $this->booking->getBookingOutByBookingIn($bookingId);

        $containerStocks = [];
        $containerOuts = [];

        $goodsIns = [];
        $goodsOuts = [];

        $bookingInMerge = [];
        if (!empty($bookingIn)) {
            $bookingInMerge = [$bookingIn];
        }
        $allBookings = array_merge($bookingInMerge, $bookingOuts);
        foreach ($allBookings as $allBooking) {
            $inboundContainer = $this->reportModel->getContainerStockMove([
                'booking' => $allBooking['id'],
                'multiplier' => 1,
                'containers' => $selectedContainers
            ]);
            $containerStocks = array_merge($containerStocks, $inboundContainer);

            $inboundGoods = $this->reportModel->getGoodsStockMove([
                'booking' => $allBooking['id'],
                'multiplier' => 1,
                'items' => $selectedGoods
            ]);
            $goodsIns = array_merge($goodsIns, $inboundGoods);
        }
        foreach ($allBookings as $allBooking) {
            $outbounds = $this->reportModel->getContainerStockMove([
                'booking' => $allBooking['id'],
                'multiplier' => -1,
                'containers' => $selectedContainers
            ]);
            $containerOuts = array_merge($containerOuts, $outbounds);

            $outboundGoods = $this->reportModel->getGoodsStockMove([
                'booking' => $allBooking['id'],
                'multiplier' => -1,
                'items' => $selectedGoods
            ]);
            $goodsOuts = array_merge($goodsOuts, $outboundGoods);
        }

        foreach ($containerStocks as &$containerIn) {
            $containerIn['outbounds'] = [];
            foreach ($containerOuts as $index => &$containerOut) {
                if ($containerIn['no_container'] == $containerOut['no_container']) {
                    $containerIn['outbounds'][] = $containerOut;
                    unset($containerOuts[$index]);
                }
            }
        }

        // add marker by ex container and goods unit
        $goodsIns = array_map(function ($item) {
            $item['_group'] = $item['id_goods'] . '-' . if_empty($item['ex_no_container'], '') . '-' . if_empty($item['id_unit'], '');
            return $item;
        }, $goodsIns);

        $goodIds = array_unique(array_column($goodsIns, '_group'));
        $goodsStocks = [];
        foreach ($goodIds as $goodId) {
            $goodsId = explode('-', $goodId);
            $goodsData = $this->goodsModel->getById($goodsId[0]);
            $goodsData['ex_no_container'] = get_if_exist($goodsId, 1, '');
            $goodsData['id_unit'] = get_if_exist($goodsId, 2, '');
            $goodsStocks[] = $goodsData;
        }

        /*
        if (!empty($goodIds)) {
            $goodsStocks = $this->goodsModel->getById($goodIds);
        } else {
            $goodsStocks = [];
        }
        */

        foreach ($goodsStocks as &$item) {
            $item['inbounds'] = [];
            $item['outbounds'] = [];
            foreach ($goodsIns as $goods) {
                if ($item['id'] == $goods['id_goods'] && $item['id_unit'] == $goods['id_unit'] && $item['ex_no_container'] == $goods['ex_no_container']) {
                    $item['inbounds'][] = $goods;
                }
            }
            foreach ($goodsOuts as $goods) {
                if ($item['id'] == $goods['id_goods'] && $item['id_unit'] == $goods['id_unit'] && $item['ex_no_container'] == $goods['ex_no_container']) {
                    $item['outbounds'][] = $goods;
                }
            }
        }

        $data = [
            'title' => "Stock Comparator",
            'page' => "report/stock_comparator",
            'booking' => $bookingIn,
            'containers' => $this->containerModel->getBy(['ref_containers.id' => $selectedContainers]),
            'items' => $this->goodsModel->getBy(['ref_goods.id' => $selectedGoods]),
            'containerStocks' => $containerStocks,
            'goodsStocks' => $goodsStocks,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show report stock summary.
     */
    public function stock_summary()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_STOCK);

        $warehouses = $this->warehouse->getBy(['ref_warehouses.id_branch' => get_active_branch('id')]);

        $containerFilter = get_url_param('filter_summary_container') ? $_GET : [];
        $goodsFilter = get_url_param('filter_summary_goods') ? $_GET : [];

        $selectedBookings = key_exists('booking', $containerFilter) ? $containerFilter['booking'] : [0];
        $selectedOwners = key_exists('owner', $containerFilter) ? $containerFilter['owner'] : [0];
        if (empty($selectedBookings) || empty($selectedBookings[0])) {
            $selectedBookings = key_exists('booking', $goodsFilter) ? $goodsFilter['booking'] : [0];
        }
        if (empty($selectedOwners) || empty($selectedOwners[0])) {
            $selectedOwners = key_exists('owner', $goodsFilter) ? $goodsFilter['owner'] : [0];
        }
        $selectedSourceWarehouses = key_exists('source_warehouses', $containerFilter) ? $containerFilter['source_warehouses'] : [0];

        $owners = $this->peopleModel->getById($selectedOwners);
        $sourceWarehouses = $this->peopleModel->getById($selectedSourceWarehouses);
        $bookings = $this->booking->getBookingById($selectedBookings);

        $cacheKey = 'stock-container-summary-' . get_active_branch_id();
        $containerSummary = cache_remember($cacheKey, 60 * 5, function () use ($containerFilter) {
            return $this->reportStock->getStockContainerSummary($containerFilter);
        });
        $reportContainerTotals = $containerSummary['total_all'] ?? 0;
        $reportContainer20 = $containerSummary['total_20'] ?? 0;
        $reportContainer40 = $containerSummary['total_40'] ?? 0;
        $reportContainer45 = $containerSummary['total_45'] ?? 0;

        $cacheKey = 'stock-goods-summary-' . get_active_branch_id();
        $goodsSummary = cache_remember($cacheKey, 60 * 5, function () use ($goodsFilter) {
            return $this->reportStock->getStockGoodsSummary($goodsFilter);
        });
        $reportGoodsQuantity = $goodsSummary['total_quantity'] ?? 0;
        $reportGoodsWeight = $goodsSummary['total_weight'] ?? 0;
        $reportGoodsGrossWeight = $goodsSummary['total_gross_weight'] ?? 0;
        $reportGoodsVolume = $goodsSummary['total_volume'] ?? 0;

        $export = get_url_param('export');
        if (!empty($export)) {
            $userType = UserModel::authenticatedUserData('user_type');
            if ($userType == 'INTERNAL') {
                $reportGoods = $this->reportStock->getStockGoods($goodsFilter);
            } else {
                $reportGoods = $this->reportModel->getReportSummaryGoodsExternal($goodsFilter);
            }
            $reportData = $export == 'CONTAINER' ? $this->reportStock->getStockContainersOptimized($containerFilter) : $reportGoods;
            if ($export == 'GOODS' && $userType == 'INTERNAL') {
                foreach ($reportData as &$datum) {
                    $datum['stock_weight_(kg)'] = $datum['stock_weight'];
                    unset($datum['stock_weight']);

                    $datum['stock_gross_weight_(kg)'] = $datum['stock_gross_weight'];
                    unset($datum['stock_gross_weight']);

                    $datum['unit_length_(m)'] = $datum['unit_length'];
                    unset($datum['unit_length']);

                    $datum['unit_width_(m)'] = $datum['unit_width'];
                    unset($datum['unit_width']);

                    $datum['unit_height_(m)'] = $datum['unit_height'];
                    unset($datum['unit_height']);

                    $datum['stock_volume_(m3)'] = $datum['stock_volume'];
                    unset($datum['stock_volume']);
                }
            }
            $this->exporter->exportFromArray('Summary ' . $export, $reportData);
        } else {
            $data = [
                'title' => "Stock Summary",
                'page' => "report/stock_summary",
                'owners' => $owners,
                'sourceWarehouses' => $sourceWarehouses,
                'warehouses' => $warehouses,
                'bookings' => $bookings,
                'reportContainerTotals' => $reportContainerTotals,
                'reportContainer20' => $reportContainer20,
                'reportContainer40' => $reportContainer40,
                'reportContainer45' => $reportContainer45,
                'reportGoodsQuantity' => $reportGoodsQuantity,
                'reportGoodsWeight' => $reportGoodsWeight,
                'reportGoodsGrossWeight' => $reportGoodsGrossWeight,
                'reportGoodsVolume' => $reportGoodsVolume,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable stock summary container.
     */
    public function stock_summary_container_data()
    {
        $filters = array_merge(get_url_param('filter_summary_container') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);
        $stockSummary = $this->reportStock->getStockContainersOptimized($filters);

        header('Content-Type: application/json');
        echo json_encode($stockSummary);
    }

    /**
     * Get ajax datatable stock summary goods.
     */
    public function stock_summary_goods_data()
    {
        $filters = array_merge(get_url_param('filter_summary_goods') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);

        if (UserModel::authenticatedUserData('user_type') == 'INTERNAL') {
            $stockSummary = $this->reportStock->getStockGoodsOptimized($filters);
        } else {
            $stockSummary = $this->reportModel->getReportSummaryGoodsExternal($filters);
        }

        header('Content-Type: application/json');
        echo json_encode($stockSummary);
    }

    /**
     * Stock outbound data
     */
    public function stock_outbound()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_STOCK_OUTBOUND);

        $export = get_url_param('export');
        if (!empty($export)) {
            $reportData = $this->reportStock->getStockOutboundWithRequest($_GET);
            $this->exporter->exportFromArray('Stock outbound', $reportData);
        } else {
            $customer = $this->peopleModel->getById(get_if_exist($_GET, 'customer'));
            $booking = $this->booking->getBookingById(get_if_exist($_GET, 'booking_outbound'));
            $data = [
                'title' => "Stock Outbound",
                'page' => "report/stock_outbound",
                'customer' => $customer,
                'booking' => $booking,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable stock outbound goods.
     */
    public function stock_outbound_data()
    {
        $user = UserModel::authenticatedUserData();

        $defaultFilter = [];
        if ($user['user_type'] == 'EXTERNAL') {
            $defaultFilter['customer'] = $user['id_person'];
        }

        $filters = array_merge(get_url_param('filter_stock_outbound') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ], $defaultFilter);
        $stockSummary = $this->reportStock->getStockOutboundWithRequest($filters);

        header('Content-Type: application/json');
        echo json_encode($stockSummary);
    }

    /**
     * Get aging of stock
     */
    public function stock_aging()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_STOCK_AGING);

        $containerFilter = get_url_param('filter_age_container') ? $_GET : [];
        $goodsFilter = get_url_param('filter_age_goods') ? $_GET : [];
        $owners = $this->peopleModel->getById(get_url_param('owner'));

        $reportContainers = $this->reportModel->getReportAgingContainer($containerFilter);

        $export = get_url_param('export');
        if (!empty($export)) {
            $reportGoods = $this->reportModel->getReportAgingGoods($goodsFilter);
            $reportData = $export == 'CONTAINER' ? $reportContainers : $reportGoods;
            $this->exporter->exportFromArray('Aging ' . $export, $reportData);
        } else {
            $data = [
                'title' => "Stock Aging",
                'page' => "report/stock_aging",
                'owners' => $owners,
                'reportContainers' => $reportContainers,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable.
     */
    public function stock_aging_goods_data()
    {
        $filters = array_merge(get_url_param('filter_age_goods') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);

        $stockAging = $this->reportModel->getReportAgingGoods($filters);

        header('Content-Type: application/json');
        echo json_encode($stockAging);
    }


    /**
     * Get stock status
     */
    public function stock_status()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_STOCK);

        $bookingGoods = $this->reportModel->getAvailableStockBookingList('goods');

        $goodsFilter = get_url_param('filter_summary_goods') ? $_GET : [];

        $selectedOwners = key_exists('owner', $goodsFilter) ? $goodsFilter['owner'] : [0];

        $owners = $this->peopleModel->getById($selectedOwners);

        $export = get_url_param('export');
        if (!empty($export)) {
            $reportGoods = $this->reportModel->getReportStatusGoods($goodsFilter);
            $this->exporter->exportFromArray('Stock Status', $reportGoods);
        } else {
            $data = [
                'title' => "Stock Status",
                'page' => "report/stock_status",
                'owners' => $owners,
                'bookingGoods' => $bookingGoods,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable stock status.
     */
    public function stock_status_data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_STOCK);

        $filters = array_merge(get_url_param('filter_summary_goods') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir'],
            'data' => get_url_param('data', 'stock')
        ]);

        $data = $this->reportModel->getReportStatusGoods($filters);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * Show report stock summary.
     */
    public function stock_assembly_goods()
    {
        $bookingGoods = $this->reportModel->getAvailableStockBookingList('goods');
        $warehouses = $this->warehouse->getBy(['ref_warehouses.id_branch' => get_active_branch('id')]);

        $goodsFilter = get_url_param('filter_summary_goods') ? $_GET : [];
        $selectedOwners = key_exists('owner', $goodsFilter) ? $goodsFilter['owner'] : [0];
        $owners = $this->peopleModel->getById($selectedOwners);

        $goodsTotalFilter = $goodsFilter;
        $goodsTotalFilter['total_size'] = 'all';
        $reportGoodsTotals = $this->reportStock->getStockAssemblyGoods($goodsTotalFilter);

        $reportGoods = $this->reportStock->getStockAssemblyGoods($goodsFilter);

        $reportGoodsQuantity = 0;
        $reportGoodsTonnage = 0;
        $reportGoodsVolume = 0;
        foreach ($reportGoods as $item) {
            $reportGoodsQuantity += $item['stock_quantity'];
            $reportGoodsTonnage += $item['stock_tonnage'];
            $reportGoodsVolume += $item['stock_volume'];
        }

        $export = get_url_param('export');

        if (!empty($export)) {
            $reportData = $reportGoods;
            if ($export == 'GOODS') {
                foreach ($reportData as &$datum) {
                    $datum['weight_(kg)'] = $datum['stock_tonnage'];
                    unset($datum['stock_tonnage']);

                    $datum['weight_gross_(kg)'] = $datum['stock_tonnage_gross'];
                    unset($datum['stock_tonnage_gross']);

                    $datum['length_(m)'] = $datum['stock_length'];
                    unset($datum['stock_length']);

                    $datum['width_(m)'] = $datum['stock_width'];
                    unset($datum['stock_width']);

                    $datum['height_(m)'] = $datum['stock_height'];
                    unset($datum['stock_height']);

                    $datum['volume_(m3)'] = $datum['stock_volume'];
                    unset($datum['stock_volume']);
                }
            }
            $this->exporter->exportFromArray('Summary Assembly ' . $export, $reportData);
        } else {
            $data = [
                'title' => "Stock Assembly Goods",
                'page' => "report/stock_assembly_goods",
                'owners' => $owners,
                'warehouses' => $warehouses,
                'bookingGoods' => $bookingGoods,
                'reportGoodsTotals' => $reportGoodsTotals,
                'reportGoodsQuantity' => $reportGoodsQuantity,
                'reportGoodsTonnage' => $reportGoodsTonnage,
                'reportGoodsVolume' => $reportGoodsVolume,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable stock summary assembly goods.
     */
    public function stock_summary_assembly_goods_data()
    {
        $filters = array_merge(get_url_param('filter_summary_goods') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);
        $stockSummary = $this->reportStock->getStockAssemblyGoods($filters);

        header('Content-Type: application/json');
        echo json_encode($stockSummary);
    }

    /**
     * Get work order summary.
     */
    public function work_order_summary()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_WORK_ORDER_SUMMARY);

        $export = get_url_param('export');
        if (!empty($export)) {
            if ($export == 'CONTAINER') {
                $report = $this->reportModel->getWorkOrderContainerSummary($_GET);
            } else {
                $report = $this->reportModel->getWorkOrderGoodsSummary($_GET);
                foreach ($report as &$data) {
                    $data['quantity'] = numerical($data['quantity'], 3, true);
                    $data['unit_weight'] = numerical($data['unit_weight'], 3, true);
                    $data['total_weight'] = numerical($data['total_weight'], 3, true);
                    $data['unit_gross_weight'] = numerical($data['unit_gross_weight'], 3, true);
                    $data['total_gross_weight'] = numerical($data['total_gross_weight'], 3, true);
                    $data['unit_length'] = numerical($data['unit_length'], 3, true);
                    $data['unit_width'] = numerical($data['unit_width'], 3, true);
                    $data['unit_height'] = numerical($data['unit_height'], 3, true);
                    $data['unit_volume'] = numerical($data['unit_volume']);
                    $data['total_volume'] = numerical($data['total_volume']);
                }
            }
            $this->exporter->exportLargeResourceFromArray('Work order ' . $export, $report);
        } else {
            $handlingTypes = $this->handlingType->getAllHandlingTypes();
            $customer = $this->peopleModel->getById(get_url_param('customers'));
            $booking = $this->booking->getBookingsByConditions(['bookings.id' => get_url_param('bookings')]);
            $containers = $this->containerModel->getBy(['ref_containers.id' => get_url_param('containers')]);
            $goods = $this->goodsModel->getBy(['ref_goods.id' => get_url_param('goods')]);
            $data = [
                'title' => "Work order summary",
                'page' => "report/work_order_summary",
                'handlingTypes' => $handlingTypes,
                'customer' => $customer,
                'booking' => $booking,
                'containers' => $containers,
                'goods' => $goods,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * get summary container work order.
     */
    public function work_order_summary_container_data()
    {
        $filters = array_merge(get_url_param('filter_work_order_summary_container') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);

        $data = $this->reportModel->getWorkOrderContainerSummary($filters);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * get summary goods work order.
     */
    public function work_order_summary_goods_data()
    {
        $filters = array_merge(get_url_param('filter_work_order_summary_goods') ? $_GET : [], [
            'start' => $this->input->post('start'),
            'length' => $this->input->post('length'),
            'search' => $this->input->post('search')['value'],
            'order_by' => $this->input->post('order')[0]['column'],
            'order_method' => $this->input->post('order')[0]['dir']
        ]);
        $data = $this->reportModel->getWorkOrderGoodsSummary($filters);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * Get report compliance.
     */
    public function report_compliance()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_COMPLIANCE);

        $filters = array_merge(get_url_param('filter_report_compliance') ? $_GET : [], [
            'user_type' => 'INTERNAL'
        ]);
        $filters['customers'] = get_url_param('customers') == 'all' ? '' : get_url_param('customers');
        $filters['pic'] = get_url_param('pic') == 'all' ? '' : get_url_param('pic');
        $filters['branch'] = get_url_param('branch') == 'all' ? '' : get_url_param('branch');
        $filters['doc_type'] = get_url_param('doc_type') == 'all' ? '' : get_url_param('doc_type');
        // print_debug($filters);
        $compliance = $this->reportCompliance->getAll($filters);
        $complianceAll = $this->reportCompliance->getAllWithoutFilter();
        $branches = $this->branch->getBy(['dashboard_status' => '1']);
        $export = get_url_param('export');
        
        $validated_at = array_column($compliance, 'created_at');
        array_multisort($validated_at, SORT_DESC, $compliance);
        if (!empty($export)) {
            $this->exporter->exportFromArray('Report Compliance ', $compliance);
        } else {
            $customer = $this->peopleModel->getAll(['branch' => null]);
            $users = $this->user->getBy(['user_type' => 'INTERNAL']);
            $usersDoc = [];
            $name = '';
            foreach ($users as $user) {
                foreach ($complianceAll as $comp) {
                    if (in_array($user['name'], $comp, true)) {
                        if ($name != $user['name']) {
                            $name = $user['name'];
                            array_push($usersDoc, $user);
                        }
                    }
                }
            }
            $data = [
                'title' => "Report Compliance",
                'page' => "report/report_compliance",
                'compliances' => $compliance,
                'customer' => $customer,
                'pic' => $usersDoc,
                'branches' => $branches,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * get summary report compliance.
     */
    public function summary_report_compliance()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $date_now = date('Y-m-d');
            $month = $this->input->post('month');
            $year = $this->input->post('year');
            $date_from = date('Y-m-d', strtotime($year . '-' . $month . '-01'));
            if (date('Y-m', strtotime($date_now)) == date('Y-m', strtotime($date_from))) {
                $date_to = $date_now;
            } else {
                $date_to = date('Y-m-t', strtotime($date_from));
            }
            $filters = [
                'user_type' => 'INTERNAL',
                'date_from' => $date_from,
                'date_to' => $date_to,
                'doc_type' => 'sppb'
            ];

            $compliance = $this->reportCompliance->getAll($filters);
            $validated_at = array_column($compliance, 'created_at');
            array_multisort($validated_at, SORT_ASC, $compliance);
            $temp_date = $date_from;
            $hasil_hari = [];
            $hasil_hari['total'] = 0;
            $i = '01';
            foreach ($compliance as $comp) {
                if (date('Y-m-d', strtotime($comp['created_at'])) == $temp_date) {
                    if (!isset($hasil_hari[$i])) {
                        $temp_nilai = 1;
                        $hasil_hari[$i] = $temp_nilai;
                        $hasil_hari['total']++;
                    } else {
                        $temp_nilai = $hasil_hari[$i] + 1;
                        $hasil_hari[$i] = $temp_nilai;
                        $hasil_hari['total']++;
                    }
                } else {
                    $i = date('d', strtotime($comp['created_at']));
                    $temp_date = date('Y-m-d', strtotime($comp['created_at']));
                    if (!isset($hasil_hari[$i])) {
                        $temp_nilai = 1;
                        $hasil_hari[$i] = $temp_nilai;
                        $hasil_hari['total']++;
                    }
                }
            }
            $periode_awal = $date_from;
            $periode_akhir = date('d-m-Y', strtotime($date_to));
            // pisahkan tanggal, bulan tahun dari periode_awal
            $explodeTgl1 = explode("-", $periode_awal);
            // membaca bagian-bagian dari periode_awal
            $tgl1 = $explodeTgl1[2];
            $bln1 = $explodeTgl1[1];
            $thn1 = $explodeTgl1[0];
            // counter looping
            $i = 0;
            // counter untuk jumlah hari minggu
            $sum = 0;

            do {
                // mengenerate tanggal berikutnya
                $tanggal = date("d-m-Y", mktime(0, 0, 0, $bln1, $tgl1 + $i, $thn1));

                // cek jika harinya minggu, maka counter $sum bertambah satu, lalu tampilkan tanggalnya
                if (date("w", mktime(0, 0, 0, $bln1, $tgl1 + $i, $thn1)) == 0) {
                    $sum++;
                }

                $i++;
            } while ($tanggal != $periode_akhir);
            $average = $hasil_hari['total'] / (date('d', strtotime($date_to)) - $sum);
            $average = round($average, 2);
            header('Content-Type: application/json');
            echo json_encode($average);
        }
    }
    /**
     * get summary report compliance.
     */
    public function summary_report_compliance_draft()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $date_now = date('Y-m-d');
            $month = $this->input->post('month');
            $year = $this->input->post('year');
            $date_from = date('Y-m-d', strtotime($year . '-' . $month . '-01'));
            if (date('Y-m', strtotime($date_now)) == date('Y-m', strtotime($date_from))) {
                $date_to = $date_now;
            } else {
                $date_to = date('Y-m-t', strtotime($date_from));
            }
            $filters = [
                'user_type' => 'INTERNAL',
                'date_from' => $date_from,
                'date_to' => $date_to,
                'doc_type' => 'draft'
            ];

            $compliance = $this->reportCompliance->getAll($filters);
            $validated_at = array_column($compliance, 'created_at');
            array_multisort($validated_at, SORT_ASC, $compliance);
            $temp_date = $date_from;
            $hasil_hari = [];
            $hasil_hari['total'] = 0;
            $i = '01';
            foreach ($compliance as $comp) {
                if (date('Y-m-d', strtotime($comp['created_at'])) == $temp_date) {
                    if (!isset($hasil_hari[$i])) {
                        $temp_nilai = 1;
                        $hasil_hari[$i] = $temp_nilai;
                        $hasil_hari['total']++;
                    } else {
                        $temp_nilai = $hasil_hari[$i] + 1;
                        $hasil_hari[$i] = $temp_nilai;
                        $hasil_hari['total']++;
                    }
                } else {
                    $i = date('d', strtotime($comp['created_at']));
                    $temp_date = date('Y-m-d', strtotime($comp['created_at']));
                    if (!isset($hasil_hari[$i])) {
                        $temp_nilai = 1;
                        $hasil_hari[$i] = $temp_nilai;
                        $hasil_hari['total']++;
                    }
                }
            }
            $periode_awal = $date_from;
            $periode_akhir = date('d-m-Y', strtotime($date_to));
            // pisahkan tanggal, bulan tahun dari periode_awal
            $explodeTgl1 = explode("-", $periode_awal);
            // membaca bagian-bagian dari periode_awal
            $tgl1 = $explodeTgl1[2];
            $bln1 = $explodeTgl1[1];
            $thn1 = $explodeTgl1[0];
            // counter looping
            $i = 0;
            // counter untuk jumlah hari minggu
            $sum = 0;

            do {
                // mengenerate tanggal berikutnya
                $tanggal = date("d-m-Y", mktime(0, 0, 0, $bln1, $tgl1 + $i, $thn1));

                // cek jika harinya minggu, maka counter $sum bertambah satu, lalu tampilkan tanggalnya
                if (date("w", mktime(0, 0, 0, $bln1, $tgl1 + $i, $thn1)) == 0) {
                    $sum++;
                }

                $i++;
            } while ($tanggal != $periode_akhir);
            $average = $hasil_hari['total'] / (date('d', strtotime($date_to)) - $sum);
            $average = round($average, 2);
            header('Content-Type: application/json');
            echo json_encode($average);
        }
    }

    /**
     * Get work order overtime summary.
     */
    public function work_order_overtime()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_WORK_ORDER_OVERTIME);

        $export = get_url_param('export');
        if (!empty($export)) {
            $report = $this->workOrderOvertime->getAll($_GET);
            foreach ($report as $index => &$data) {
                $data['overtime_attachment'] = $data['status'] == 'NORMAL' ? '' : (empty($row['overtime_attachment']) ? 'NO' : asset_url($row['overtime_attachment']));
                unset($report[$index]['service_time_start']);
                unset($report[$index]['total_overtime']);
                unset($report[$index]['effective_date']);
                unset($report[$index]['total_overtime_hour']);
            }
            $this->exporter->exportFromArray('Work order overtime', $report);
        } else {
            $customer = $this->peopleModel->getById(get_url_param('customers'));
            $booking = $this->booking->getBookingById(get_url_param('bookings'));
            $data = [
                'title' => "Work order overtime",
                'page' => "report/work_order_overtime",
                'customer' => $customer,
                'booking' => $booking,
            ];
            $this->load->view('template/layout', $data);
        }
    }
    
    /**
     * Get work order overtime summary.
     */
    public function heavy_equipment_usage()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_HEAVY_EQUIPMENT_USAGE);

        $export = get_url_param('export');
        if (!empty($export)) {
            $report = $this->reportHeavyEquipment->getReportHeavyEquipmentUsage($_GET);
            $this->exporter->exportFromArray('Heavy equipment usage', $report);
        } else {
            $branches = $this->branch->getAll();
            $handlingTypes = $this->handlingType->getAllHandlingTypes();
            $customer = $this->peopleModel->getById(get_url_param('customers'));
            $booking = $this->db->from('bookings')->where('id', get_url_param('bookings'))->get()->row_array();
            $data = [
                'title' => "Heavy Equipment Usage",
                'page' => "report/heavy_equipment_usage",
                'branches' => $branches,
                'handlingTypes' => $handlingTypes,
                'customer' => $customer,
                'booking' => $booking,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get heavy equipment data.
     */
    public function heavy_equipment_usage_data()
    {
        $filters = array_merge(get_url_param('filter_heavy_equipment') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);
        $report = $this->reportHeavyEquipment->getReportHeavyEquipmentUsage($filters);

        header('Content-Type: application/json');
        echo json_encode($report);
    }

    /**
     * Pallet report
     */
    public function pallet()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_PALLET);

        $filter = get_url_param('filter_pallet') ? $_GET : [];
        
        $export = get_url_param('export');
        if (!empty($export)) {
            $data = [];
            $reports = $this->reportPallet->getReportPallet($filter);
            foreach($reports as $index=>$report){
                if($report['category']=='OUTBOUND'){
                    $aju = "IN ". substr($report['no_reference_in'],-5)." | OUT ".substr($report['no_reference'],-5);
                }else{
                    $aju = "IN ". substr($report['no_reference'],-5);
                }
                $temp=[
                    'no' => $index+1,
                    'no_job' => $report['no_work_order'],
                    'aju' => $aju,
                    'tanggal_aktivitas' => date('d F Y',strtotime($report['completed_at'])),
                    'no_container' => $report['no_container'],
                    'type' => $report['size'],
                    'jenis_aktivitas' => $report['category'],
                    'qty' => $report['qty'],
                    'pallet' => $report['stock_pallet'],
                    'pallet_sisa' => $report['sisa_pallet'],
                ];
                $data[] = $temp;
            }
            $this->exporter->exportFromArray($export, $data);
        } else {
            $data = [
                'title' => "Report Pallet",
                'page' => "report/pallet",
            ];
        }

        $this->load->view('template/layout', $data);
    }

    /**
     * Get ajax datatable admin site data.
     * @param $type
     */
    public function pallet_data()
    {
        $filter = get_url_param('filter_pallet') ? $_GET : [];

        $startData = get_url_param('start', $this->input->post('start'));
        $lengthData = get_url_param('length', $this->input->post('length'));
        $searchQuery = get_url_param('search', $this->input->post('search'))['value'];
        $order = get_url_param('order', $this->input->post('order'));
        $orderColumn = $order[0]['column'];
        $orderColumnOrder = $order[0]['dir'];
        
        $report = $this->reportPallet->getReportPallet($filter, $startData, $lengthData, $searchQuery, $orderColumn, 'asc');

        $no = $startData + 1;
        foreach ($report['data'] as &$row) {
            $row['no'] = $no++;
        }

        header('Content-Type: application/json');
        echo json_encode($report);
    }

    /**
     * Forklift Usage report
     */
    public function forklift()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_FORKLIFT);
        $filter = get_url_param('filter_forklift') ? $_GET : [];

        $selectedPIC = key_exists('pic', $filter) ? $filter['pic'] : [0];
        if (empty($selectedPIC)) {
            $selectedPIC = key_exists('pic', $filter) ? $filter['pic'] : [0];
        }

        
        // $data_report = $this->reportAdminSite->getReportAdminSite();

        // $allPIC = [];
        // $createdBy = [];
        // foreach($data_report as $report) {
        //     if(in_array($report['created_by'], $createdBy) == false){
        //         array_push($createdBy, $report['created_by']);
        //         $allPIC[] = ['id' => $report['created_by'], 'name' => $report['creator_name']];
        //     }
        // }

        // $pic = array_filter($allPIC, function($item) use( $selectedPIC ){

        //     $item_created_by = explode(",", $item['id']);
        //     if(is_array($selectedPIC)  && !empty(array_intersect($item_created_by, $selectedPIC)) ){
        //         return $item;
        //     }
        // });
        $target_all = $this->target->getTargetForklift();

        $export = get_url_param('export');
        if (!empty($export)) {
            $report = $this->reportAdminSite->getReportAdminSite($filter);
            $this->exporter->exportFromArray($export, $report);
        } else {
            $data = [
                'title' => "Report Forklift",
                'page' => "report/forklift",
                // 'pic' => $pic,
                // 'data_report' => $data_report,
                // 'allPIC' => $allPIC,
                'target_all' => $target_all,
            ];
        }

        $this->load->view('template/layout', $data);
    }

    /**
     * Get ajax datatable admin site data.
     * @param $type
     */
    public function forklift_data()
    {
        $filter = get_url_param('filter_forklift') ? $_GET : [];

        $startData = get_url_param('start', $this->input->post('start'));
        $lengthData = get_url_param('length', $this->input->post('length'));
        $searchQuery = get_url_param('search', $this->input->post('search'))['value'];
        $order = get_url_param('order', $this->input->post('order'));
        $orderColumn = $order[0]['column'];
        $orderColumnOrder = $order[0]['dir'];
        
        $report = $this->reportForklift->getReportForklift($filter, $startData, $lengthData, $searchQuery, $orderColumn, $orderColumnOrder);
        // print_debug($this->db->last_query());
        $no = $startData + 1;
        foreach ($report['data'] as &$row) {
            $row['no'] = $no++;
        }

        header('Content-Type: application/json');
        echo json_encode($report);
    }

    /**
     * performance Usage report
     */
    public function performance()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_PERFORMANCE);

        $filter = get_url_param('filter_performance') ? $_GET : [];

        $selectedPIC = key_exists('pic', $filter) ? $filter['pic'] : [0];
        if (empty($selectedPIC)) {
            $selectedPIC = key_exists('pic', $filter) ? $filter['pic'] : [0];
        }

        
        // $data_report = $this->reportAdminSite->getReportAdminSite();

        // $allPIC = [];
        // $createdBy = [];
        // foreach($data_report as $report) {
        //     if(in_array($report['created_by'], $createdBy) == false){
        //         array_push($createdBy, $report['created_by']);
        //         $allPIC[] = ['id' => $report['created_by'], 'name' => $report['creator_name']];
        //     }
        // }

        // $pic = array_filter($allPIC, function($item) use( $selectedPIC ){

        //     $item_created_by = explode(",", $item['id']);
        //     if(is_array($selectedPIC)  && !empty(array_intersect($item_created_by, $selectedPIC)) ){
        //         return $item;
        //     }
        // });

        $target_all = $this->target->getTargetPerformance();

        $export = get_url_param('export');
        if (!empty($export)) {
            // $report = $this->reportAdminSite->getReportAdminSite($filter);
            // $this->exporter->exportFromArray($export, $report);
        } else {
            $data = [
                'title' => "Report performance",
                'page' => "report/performance",
                // 'pic' => $pic,
                // 'data_report' => $data_report,
                // 'allPIC' => $allPIC,
                'target_all' => $target_all,
            ];
        }

        $this->load->view('template/layout', $data);
    }

    /**
     * Get ajax datatable performance data.
     * @param $type
     */
    public function performance_data()
    {
        $filter = get_url_param('filter_performance') ? $_GET : [];

        $startData = get_url_param('start', $this->input->post('start'));
        $lengthData = get_url_param('length', $this->input->post('length'));
        $searchQuery = get_url_param('search', $this->input->post('search'))['value'];
        $order = get_url_param('order', $this->input->post('order'));
        $orderColumn = $order[0]['column'];
        $orderColumnOrder = $order[0]['dir'];
        
        $report = $this->reportPerformance->getReportPerformance($filter, $startData, $lengthData, $searchQuery, $orderColumn, $orderColumnOrder);

        $no = $startData + 1;
        foreach ($report['data'] as &$row) {
            $row['no'] = $no++;
        }

        header('Content-Type: application/json');
        echo json_encode($report);
    }

    public function forklift_detail()
    {
        $branchId = $this->input->get('branchId');
        $tahun = $this->input->get('tahun');
        $minggu = $this->input->get('minggu');
        $ownership = $this->input->get('ownership');
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_FORKLIFT);
        $filters = [
            'branch' => $branchId,
            'year' => $tahun,
            'week' => $minggu,
            'ownership' => $ownership,
        ];
        $results = $this->workOrder->getWorkOrderForkliftByCondition($filters);
        // print_debug($this->db->last_query());
        $date1 = new DateTime();
        $dateFirst = $date1->modify(get_url_param('tahun',0).'W'.sprintf("%02d", (get_url_param('minggu',0)+1)));
        $dateFirst = $dateFirst->format('Y-m-d');
        $dateLast = $date1->modify(get_url_param('tahun',0).'W'.sprintf("%02d", (get_url_param('minggu',0)+1))." +6 days");
        $dateLast = $dateLast->format('Y-m-d');
        if(date('Y-m-d')<$dateLast){
            $dateLast = date('Y-m-d');
        }
        $hariKerja = $this->holiday->getHariKerja($dateFirst,$dateLast);
        $tanggalKerja = [];
        foreach($hariKerja as $kerja){
            $tanggalKerja [] = $kerja['selected_date'];
        }
        $datas = [
            'draw' => null,
            'recordsTotal' => count($results),
            'recordsFiltered' => count($results),
            'data' => $results
        ];
        $data = [
            'title' => "Detail Freighton",
            'page' => "report/forklift_detail",
            'datas' => $datas,
            'tanggalKerja' => $tanggalKerja,
        ];
        $this->load->view('template/layout', $data);
    }
    
    public function performance_detail()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_PERFORMANCE);
        $branchId = $this->input->get('branchId');
        $tahun = $this->input->get('tahun');
        $minggu = $this->input->get('minggu');
        $is_core = $this->input->get('is_core');
        $filters = [
            'branch' => $branchId,
            'year' => $tahun,
            'week' => $minggu,
            'is_core' => $is_core,
        ];
        $results = $this->workOrder->getOpsByCondition($filters);
        // print_debug($this->db->last_query());   
        $datas = [
            'draw' => null,
            'recordsTotal' => count($results),
            'recordsFiltered' => count($results),
            'data' => $results
        ];
        $data = [
            'title' => "Report Ops Detail",
            'page' => "report/ops_detail",
            'datas' => $datas,
        ];
        $this->load->view('template/layout', $data);
    }

      /**
     * Get heavy equipment report
     */
    public function heavy_equipment()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_HEAVY_EQUIPMENT);
        $heavyEquipmentNames = $this->heavyEquipment->getAll();
        if(get_url_param('filter_heavy_equipment')){
            $heavyEquipmentFilters = get_url_param('filter_heavy_equipment') ? $_GET : [];
            $type = key_exists('type', $heavyEquipmentFilters) ? $heavyEquipmentFilters['type'] : 'INTERNAL';
            $selectedCustomers = key_exists('customer', $heavyEquipmentFilters) ? $heavyEquipmentFilters['customer'] : [0];
            $customers = $this->peopleModel->getById($selectedCustomers);
            
            $id_customer = $this->input->get('customer');
            $heavyEquipmentFilters = [
                'heavy_equipment' => $this->input->get('heavy_equipment'),
                'date_from' => $this->input->get('date_from'),
                'date_to' => $this->input->get('date_to'),
                'customer' => $id_customer,
            ];
            $heavyEquipments = $this->workOrder->getHeavyEquipmentInternal($heavyEquipmentFilters);
            // print_debug($heavyEquipments);
            if($type == 'EXTERNAL'){
                $heavyEquipmentNames = $this->heavyEquipmentEntryPermit->getHEEPAll();
                $heavyEquipmentFilters = [
                    'heavy_equipment' => $this->input->get('heavy_equipment'),
                    'date_from' => $this->input->get('date_from'),
                    'date_to' => $this->input->get('date_to'),
                    'customer' => $id_customer,
                ];
                $heavyEquipments = $this->workOrder->getHeavyEquipmentExternal($heavyEquipmentFilters);
                // print_debug($heavyEquipments);
            }
            $nameHeavy = '';
            $totalJam = 0;
            foreach ($heavyEquipments as $key => &$heavyEquipment) {
                if(!empty($heavyEquipment['name_heavy_equipment'])){
                    $nameHeavy = $heavyEquipment['name_heavy_equipment'];
                }
                if(!empty($heavyEquipment['jam'])){
                    $totalJam += $heavyEquipment['jam'];
                }
                if(!empty($heavyEquipment['keterangan'])){
                    $key_keterangan = explode("; ",$heavyEquipment['keterangan']);
                    foreach($key_keterangan as &$key_ket){
                        $val = explode(", ",$key_ket);
                        $key_ket = $val;
                    }
                    $customer_name = array_column($key_keterangan, '0');
                    array_multisort($customer_name, SORT_ASC, $key_keterangan);
                    $temp_customer = '';
                    $temp_type = '';
                    $teks = '';
                    $lenght = count($key_keterangan);
                    foreach($key_keterangan as $i=>$ket){
                        if(isset($ket[0])&&isset($ket[1])&&isset($ket[2])){
                            if($temp_customer!=$ket[0]){
                                if($temp_customer==''){
                                    $teks.=$ket[0].' : '.$ket[1].' ( '.$ket[2];
                                }else{
                                    $teks.=' )<br> ';
                                    $teks.=$ket[0].' : '.$ket[1].' ( '.$ket[2];
                                }
                                $temp_customer = $ket[0];
                                $temp_type = $ket[1];
                            }else{
                                if($temp_type != $ket[1]){
                                    $teks.=' )<br> ';
                                    $teks.=$ket[0].' : '.$ket[1].' ( '.$ket[2];
                                    $temp_type = $ket[1];
                                    continue;
                                }
                                $teks.=', '.$ket[2];
                            }
                            if($i==$lenght-1){
                                $teks.=' )';
                            }
                        }
                    }
                    $heavyEquipment['teks'] = $teks;
                }
            }
            $export = $this->input->get('export');
            if($export){
                $spreadsheet = new Spreadsheet();
                $sheet = $spreadsheet->getActiveSheet();
                $kolom = 1;
                $baris = 9;
                $drawing = new \PhpOffice\PhpSpreadsheet\Worksheet\Drawing();
                $drawing->setName('Kop');
                $drawing->setDescription('Kop');
                $drawing->setPath(FCPATH."assets/app/img/layout/kop_email.jpg"); // put your path and image here
                $drawing->setCoordinates('A1');
                $drawing->setHeight(100);
                $drawing->setWorksheet($spreadsheet->getActiveSheet());

                $sheet->setCellValue("A7","LAPORAN PEMAKAIAN ".$nameHeavy);
                $sheet->mergeCells("A7:F7");
                $sheet->getStyle('A7')->getAlignment()->setHorizontal('center');
                $sheet->getStyle('A7')->getFont()->setBold(true);
                $sheet->getStyle('A7')->getFont()->setSize(18);
                $sheet->setCellValueByColumnAndRow($kolom, $baris, "NO");
                $sheet->mergeCellsByColumnAndRow($kolom,$baris,$kolom++,$baris+1);
                $sheet->setCellValueByColumnAndRow($kolom, $baris, "TANGGAL");
                $sheet->mergeCellsByColumnAndRow($kolom,$baris,$kolom++,$baris+1);
                $sheet->setCellValueByColumnAndRow($kolom, $baris, "JAM PEMAKAIAN");
                $sheet->mergeCellsByColumnAndRow($kolom,$baris,$kolom+1,$baris);
                $sheet->setCellValueByColumnAndRow($kolom++, $baris+1, "MULAI");
                $sheet->setCellValueByColumnAndRow($kolom++, $baris+1, "SELESAI");
                $sheet->setCellValueByColumnAndRow($kolom, $baris, "KETERANGAN");
                $sheet->mergeCellsByColumnAndRow($kolom,$baris,$kolom++,$baris+1);
                $sheet->setCellValueByColumnAndRow($kolom, $baris, "TOTAL JAM");
                $sheet->mergeCellsByColumnAndRow($kolom,$baris,$kolom++,$baris+1);
                $barisTabel = $baris;
                $baris +=2;
                $kolom = 1;
                $no = 1;
                $kolomTabel = 0;
                if(get_url_param('filter_heavy_equipment') == true && get_url_param('type') == 'INTERNAL'){
                    foreach ($heavyEquipments as $heavyEquipment){
                        $kolom = 1;
                        $sheet->setCellValueByColumnAndRow($kolom++, $baris, $no++);
                        $sheet->setCellValueByColumnAndRow($kolom++, $baris, $heavyEquipment['day_name'].", ". date('d-M-Y',strtotime($heavyEquipment['selected_date'])));
                        $sheet->setCellValueByColumnAndRow($kolom++, $baris, $heavyEquipment['name_heavy_equipment']==''? $heavyEquipment['service_time_start'] : $heavyEquipment['start_job']);
                        $sheet->setCellValueByColumnAndRow($kolom++, $baris, $heavyEquipment['name_heavy_equipment']==''? $heavyEquipment['service_time_end'] : $heavyEquipment['finish_job']);
                        $sheet->setCellValueByColumnAndRow($kolom++, $baris, $heavyEquipment['name_heavy_equipment']==''? 'Stand By' : $heavyEquipment['handling_type']);
                        $sheet->setCellValueByColumnAndRow($kolom++, $baris, $heavyEquipment['jam']>0 ? $heavyEquipment['jam'] : '-');
                        $baris++;
                    }
                }elseif(get_url_param('filter_heavy_equipment') == true && get_url_param('type') == 'EXTERNAL'){
                    foreach ($heavyEquipments as $heavyEquipment){
                        $kolom = 1;
                        $sheet->setCellValueByColumnAndRow($kolom++, $baris, $no++);
                        $sheet->setCellValueByColumnAndRow($kolom++, $baris, $heavyEquipment['day_name'].", ". date('d-M-Y',strtotime($heavyEquipment['selected_date'])));
                        $sheet->setCellValueByColumnAndRow($kolom++, $baris, $heavyEquipment['name_heavy_equipment']==''? $heavyEquipment['service_time_start'] : $heavyEquipment['start_job']);
                        $sheet->setCellValueByColumnAndRow($kolom++, $baris, $heavyEquipment['name_heavy_equipment']==''? $heavyEquipment['service_time_end'] : $heavyEquipment['finish_job']);
                        $sheet->setCellValueByColumnAndRow($kolom++, $baris, $heavyEquipment['name_heavy_equipment']==''? ( !empty($heavyEquipment['date_checked_out'])? (($heavyEquipment['date_checked_out']>=$heavyEquipment['selected_date'] && $heavyEquipment['date_checked_in']<=$heavyEquipment['selected_date'])?'Stand By':'-') :(($heavyEquipment['date_checked_in']<=$heavyEquipment['selected_date']?'Stand By':'-'))) : $heavyEquipment['handling_type']);
                        $sheet->setCellValueByColumnAndRow($kolom++, $baris, $heavyEquipment['jam']>0 ? $heavyEquipment['jam'] : '-');
                        $baris++;
                    }
                }
                $kolomTabel = $kolom-1;
                $kolom = 1;
                $sheet->mergeCellsByColumnAndRow($kolom,$baris,$kolom+3,$baris+1);
                $sheet->setCellValueByColumnAndRow($kolom+4, $baris, "TOTAL JAM");
                $sheet->setCellValueByColumnAndRow($kolom+4, $baris+1, "TOTAL JAM");
                $sheet->setCellValueByColumnAndRow($kolom+5, $baris, $totalJam);
                $sheet->setCellValueByColumnAndRow($kolom+5, ++$baris, $totalJam<=200? 0: $totalJam-200);

                $sheet->setCellValueByColumnAndRow($kolom+1, $baris+2, "__________, ".date('d F Y'));
                $sheet->setCellValueByColumnAndRow($kolom+1, $baris+3, "PT TRANSCON INDONESIA");
                $sheet->setCellValueByColumnAndRow($kolom+1, $baris+7, "(".UserModel::authenticatedUserData('name').")");

                $sheet
                    ->getStyleByColumnAndRow(1,$barisTabel,$kolomTabel,$baris)
                    ->applyFromArray([
                            'borders' => array(
                                'allBorders' => array(
                                    'borderStyle' => Border::BORDER_THIN,
                                ),
                            ),
                        ]
                    );
                $sheet->getColumnDimension('A')->setWidth(4);
                foreach(range('B','F') as $columnID) {
                    $sheet->getColumnDimension($columnID)
                        ->setAutoSize(true);
                }
                $writer = new Xlsx($spreadsheet);
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="Report Heavy Equipment.xlsx"');
                $writer->save('php://output');
            }else{
                $data = [
                    'title' => "Heavy Equipment",
                    'page' => "report/heavy_equipment",
                    'heavyEquipments' => $heavyEquipments,
                    'heavyEquipmentNames' => $heavyEquipmentNames,
                    'nameHeavy' => $nameHeavy,
                    'totalJam' => $totalJam,
                    'customers' => $customers,
                ];
                $this->load->view('template/layout', $data);
            }
            
        }else{
            $data = [
                'title' => "Heavy Equipment",
                'page' => "report/heavy_equipment",
                'heavyEquipmentNames' => $heavyEquipmentNames,
            ];
            $this->load->view('template/layout', $data);
        }        
    }

    /**
     * report document production
     */
    public function document_production()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_DOCUMENT_PRODUCTION);

        $reportProduction = $this->reportDocumentProductionHistory->getReportDocumentProduction();
        // print_debug($this->db->last_query());
        $columnFocus = array_column($reportProduction, 'minggu');
        array_multisort($columnFocus, SORT_DESC, $reportProduction);
        if ($this->input->get('export')) {

        } else {
            $data = [
                'title' => "Report Document Production",
                'page' => "report/document_production",
                'reportProductions' => $reportProduction,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * report document production detail draft
     */
    public function document_production_detail_draft()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_DOCUMENT_PRODUCTION);
        $tahun = $this->input->get('tahun');
        $minggu = $this->input->get('minggu');
        $filters = [
            'year' => $tahun,
            'week' => $minggu,
        ];
        $results = $this->reportDocumentProductionHistory->getDraftDetail($filters);
        $datas = [
            'draw' => null,
            'recordsTotal' => count($results),
            'recordsFiltered' => count($results),
            'data' => $results
        ];
        $data = [
            'title' => "Report Draft Detail",
            'page' => "report/draft_detail",
            'datas' => $datas,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * report document production detail sppb
     */
    public function document_production_detail_sppb()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_DOCUMENT_PRODUCTION);
        $tahun = $this->input->get('tahun');
        $minggu = $this->input->get('minggu');
        $filters = [
            'year' => $tahun,
            'week' => $minggu,
        ];
        $results = $this->reportDocumentProductionHistory->getSppbDetail($filters);
        // print_debug($results);
        $columnFocus = array_column($results, 'sppb_upload_date');
        array_multisort($columnFocus, SORT_ASC, $results);
        $datas = [
            'draw' => null,
            'recordsTotal' => count($results),
            'recordsFiltered' => count($results),
            'data' => $results
        ];
        $data = [
            'title' => "Report Sppb Detail",
            'page' => "report/sppb_detail",
            'datas' => $datas,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * report document production detail comp
     */
    public function document_production_detail_comp()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_DOCUMENT_PRODUCTION);
        $tahun = $this->input->get('tahun');
        $minggu = $this->input->get('minggu');
        $filters = [
            'year' => $tahun,
            'week' => $minggu,
        ];
        $results = $this->reportDocumentProductionHistory->getCompDetail($filters);
        $datas = [
            'draw' => null,
            'recordsTotal' => count($results),
            'recordsFiltered' => count($results),
            'data' => $results
        ];
        $data = [
            'title' => "Report Compliance Detail",
            'page' => "report/comp_detail",
            'datas' => $datas,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * report document production detail overtime
     */
    public function document_production_detail_overtime()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_DOCUMENT_PRODUCTION);
        $tahun = $this->input->get('tahun');
        $minggu = $this->input->get('minggu');
        $filters = [
            'year' => $tahun,
            'week' => $minggu,
        ];
        $results = $this->reportDocumentProductionHistory->getOvertimeDetail($filters);
        
        $columnFocus = array_column($results, 'selected_date');
        array_multisort($columnFocus, SORT_ASC, $results);

        $datas = [
            'draw' => null,
            'recordsTotal' => count($results),
            'recordsFiltered' => count($results),
            'data' => $results
        ];
        $data = [
            'title' => "Report Overtime Detail",
            'page' => "report/overtime_detail",
            'datas' => $datas,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Get booking payment report
     */
    public function booking_payment()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_BOOKING_PAYMENTS);

        $paymentFilter = get_url_param('filter_booking_payments') ? $_GET : [];
        $selectedCustomers = key_exists('customers', $paymentFilter) ? $paymentFilter['customers'] : [0];
        $customers = $this->peopleModel->getById($selectedCustomers);
        $bookingPayments = $this->payment->getAllReportPayments($paymentFilter);
        $export = get_url_param('export');
        
        $allowCheck = AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_CHECK);
        $allowRealize = AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_REALIZE);
        $allowCreate = AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_CREATE);
        $isChecker = false;
        if ($allowCheck && (!$allowRealize || !$allowCreate) && !key_exists('status_checks', $_GET)) {
            $isChecker = true;
        }
        $branches = $this->branch->getAll();

        if (!empty($export)) {
            $reportPayments = $this->payment->getAllReportPayments($paymentFilter);
            $this->exporter->exportFromArray('Report Payments', $reportPayments);
        } else {
            $data = [
                'title' => "Booking Payment Report",
                'page' => "report/booking_payment",
                'isChecker' => $isChecker,
                'bookingPayments' => $bookingPayments,
                'customers' => $customers,
                'branches' => $branches,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable.
     */
    public function data_booking_payment()
    {
        $filters = array_merge(get_url_param('filter_booking_payments') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);

        $allowCheck = AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_CHECK);
        $allowRealize = AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_REALIZE);
        $allowValidate = AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_VALIDATE);
        $allowCreate = AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_CREATE);
        if (!$allowRealize && !$allowValidate && !$allowCheck) {
            $filters['users'] = UserModel::authenticatedUserData('id');
        }
        if ($allowCheck && (!$allowRealize || !$allowCreate) && !key_exists('status_checks', $filters)) {
            $filters['status_checks'] = PaymentModel::STATUS_ASK_APPROVAL;
        }

        $payments = $this->payment->getAllReportPayments($filters);

        header('Content-Type: application/json');
        echo json_encode($payments);
    }

    /**
     * report performance inbound and outbound
     */
    public function performance_in_out()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_PERFORMANCE_IN_OUT);

		if (empty(get_url_param('year'))) {
			$filters['year'] = date('Y');
		}else {
			$filters['year'] = get_url_param('year');
		}
        
        if (!empty(get_url_param('customer'))) {
            $filters['customers'] = get_url_param('customer');
            $customers = $this->peopleModel->getById($filters['customers']);
        }

        $reportPerformances = $this->reportModel->getPerformanceInOutReport($filters);
        if (!empty($reportPerformances) && empty(get_url_param('month'))) {
			$firstWeek = end($reportPerformances);
			$firstWeekRange = get_week_date_range_sql_mode_2($firstWeek['week'], $firstWeek['year']);
			if ($firstWeekRange['week_start'] != $filters['year'] . '-01-01') {
				$lastYearFilter = $filters;
				$lastYearFilter['year'] = $filters['year'] - 1;
				$lastYearReports = $this->reportModel->getPerformanceInOutReport($lastYearFilter);
				if (!empty($lastYearReports)) {
					$reportPerformances[] = $lastYearReports[0];
				}
			}
		}

		if ($this->input->get('export')) {
			$this->exporter->exportFromArray('Performance In Out', $reportPerformances);
        } else {
            $data = [
                'title' => "Report Performance Inbound Outbound",
                'page' => "report/performance_in_out",
                'reportPerformances' => $reportPerformances,
            ];

            if (!empty(get_url_param('customer'))) {
                $data['customers'] = $customers;
            }

            $this->load->view('template/layout', $data);
        }
    }

    /**
     * report performance inbound and outbound detail sppb complete
     */
    public function performance_in_out_detail_sppb_complete()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_PERFORMANCE_IN_OUT);

        $filters = $_GET;
		if (empty(get_url_param('year_week'))) {
			$filters['year_week'] = date('oW'); 
		}
        $reportPerformances = $this->reportModel->getPerformanceInOutReportDetailSppbComplete($filters);
		if ($this->input->get('export')) {
			$this->exporter->exportFromArray('Performance In Out Detail SPPB Complete', $reportPerformances);
        } else {
            $data = [
                'title' => "Detail SPPB Complete",
                'page' => "report/performance_in_out_detail_sppb_complete",
                'reportPerformances' => $reportPerformances,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * report performance inbound and outbound detail total lcl container
     */
    public function performance_in_out_detail_total_lcl_container()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_PERFORMANCE_IN_OUT);

        $filters = $_GET;
		if (empty(get_url_param('year_week'))) {
			$filters['year_week'] = date('oW'); 
		}
        $reportPerformances = $this->reportModel->getPerformanceInOutReportDetailTotalLclContainer($filters);
		if ($this->input->get('export')) {
			$this->exporter->exportFromArray('Performance In Out Detail Total In', $reportPerformances);
        } else {
            $data = [
                'title' => "Detail Total LCL Container",
                'page' => "report/performance_in_out_detail_total_lcl_container",
                'reportPerformances' => $reportPerformances,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * report performance inbound and outbound detail st sppb request
     */
    public function performance_in_out_detail_st_sppb_request()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_PERFORMANCE_IN_OUT);

        $filters = $_GET;
		if (empty(get_url_param('year_week'))) {
			$filters['year_week'] = date('oW'); 
		}
        $reportPerformances = $this->reportModel->getPerformanceInOutReportDetailStSppbRequest($filters);
		if ($this->input->get('export')) {
			$this->exporter->exportFromArray('Performance In Out Detail SPPB Request', $reportPerformances);
        } else {
            $data = [
                'title' => "Detail St SPPB Request",
                'page' => "report/performance_in_out_detail_st_sppb_request",
                'reportPerformances' => $reportPerformances,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * report performance inbound and outbound detail st request complete
     */
    public function performance_in_out_detail_st_request_complete()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_PERFORMANCE_IN_OUT);

        $filters = $_GET;
		if (empty(get_url_param('year_week'))) {
			$filters['year_week'] = date('oW'); 
		}
        //sama dengan performance_in_out_detail_st_sppb_request
        $reportPerformances = $this->reportModel->getPerformanceInOutReportDetailStSppbRequest($filters);
		if ($this->input->get('export')) {
			$this->exporter->exportFromArray('Performance In Out Detail St Request Complete', $reportPerformances);
        } else {
            $data = [
                'title' => "Detail St Request Complete",
                'page' => "report/performance_in_out_detail_st_request_complete",
                'reportPerformances' => $reportPerformances,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * report performance inbound and outbound detail Total Fleet
     */
    public function performance_in_out_detail_total_fleet()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_PERFORMANCE_IN_OUT);

        $filters = $_GET;
		if (empty(get_url_param('year_week'))) {
			$filters['year_week'] = date('oW'); 
		}
        //sama dengan performance_in_out_detail_st_sppb_request
        $reportPerformances = $this->reportModel->getPerformanceInOutReportDetailTotalFleet($filters);
		if ($this->input->get('export')) {
			$this->exporter->exportFromArray('Performance In Out Detail Total Fleet', $reportPerformances);
        } else {
            $data = [
                'title' => "Detail Total Out",
                'page' => "report/performance_in_out_detail_total_fleet",
                'reportPerformances' => $reportPerformances,
            ];
            $this->load->view('template/layout', $data);
        }
    }
}