<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Gate
 * @property BookingModel $booking
 * @property BookingContainerModel $bookingContainer
 * @property BookingGoodsModel $bookingGoods
 * @property ComponentPriceModel $componentPrice
 * @property DeliveryOrderModel $deliveryOrder
 * @property DeliveryOrderGoodsModel $deliveryOrderGoods
 * @property InvoiceModel $invoice
 * @property HandlingTypeModel $handlingType
 * @property HandlingModel $handling
 * @property HandlingComponentModel $handlingComponent
 * @property ComponentModel $component
 * @property ComponentOrderModel $componentOrder
 * @property SafeConductModel $safeConduct
 * @property SafeConductContainerModel $safeConductContainer
 * @property SafeConductGoodsModel $safeConductGoods
 * @property UnitModel $unit
 * @property WorkOrderModel $workOrder
 * @property WorkOrderContainerModel $workOrderContainer
 * @property WorkOrderGoodsModel $workOrderGoods
 * @property LogModel $logHistory
 * @property WorkOrderComponentModel $workOrderComponent
 */
class Gate extends CI_Controller
{
    /**
     * Gate constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('ComponentPriceModel', 'componentPrice');
        $this->load->model('DeliveryOrderModel', 'deliveryOrder');
        $this->load->model('DeliveryOrderGoodsModel', 'deliveryOrderGoods');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('InvoiceModel', 'invoice');
        $this->load->model('HandlingTypeModel', 'handlingType');
        $this->load->model('HandlingModel', 'handling');
        $this->load->model('HandlingComponentModel', 'handlingComponent');
        $this->load->model('ComponentModel', 'component');
        $this->load->model('ComponentOrderModel', 'componentOrder');
        $this->load->model('SafeConductModel', 'safeConduct');
        $this->load->model('SafeConductContainerModel', 'safeConductContainer');
        $this->load->model('SafeConductGoodsModel', 'safeConductGoods');
        $this->load->model('UnitModel', 'unit');
        $this->load->model('UploadDocumentModel', 'uploadDocument');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('WorkOrderContainerModel', 'workOrderContainer');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('LogModel', 'logHistory');
        $this->load->model('WorkOrderComponentModel', 'workOrderComponent');
    }

    /**
     * Show gate scan form.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_GATE_CHECK_IN);
        AuthorizationModel::mustAuthorized(PERMISSION_GATE_CHECK_OUT);
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();

        $data = [
            'title' => "Gate",
            'subtitle' => "Gate check point",
            'page' => "gate/index"
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Check scanned code.
     */
    public function check()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_GATE_CHECK_IN);
        AuthorizationModel::mustAuthorized(PERMISSION_GATE_CHECK_OUT);

        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->form_validation->set_data($this->input->get());
            $this->form_validation->set_rules('code', 'Document code', 'trim|required|max_length[50]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid', 'gate');
            } else {
                $code = urldecode(trim($this->input->get('code')));
                $codePrefix = explode('/', $code);

                $handlingTypeCodes = $this->handlingType->getAllHandlingTypes();
                if (!empty($handlingTypeCodes)) {
                    $handlingTypeCodes = array_column($handlingTypeCodes, 'handling_code');
                } else {
                    $handlingTypeCodes = [];
                }

                if ($codePrefix[0] == SafeConductModel::TYPE_CODE_IN || $codePrefix[0] == SafeConductModel::TYPE_CODE_OUT) {
                    $this->checkSafeConduct($code);
                } else if ($codePrefix[0] == BookingModel::NO_INBOUND || $codePrefix[0] == BookingModel::NO_OUTBOUND) {
                    $this->checkBooking($code);
                } else if ($codePrefix[0] == 'HR') {
                    $this->checkHandling($code);
                } else if (in_array($codePrefix[0], $handlingTypeCodes)) {
                    $this->checkJob($code);
                } else {
                    flash('status_check::danger', "message_check::Pattern code <strong>{$code}</strong> is not recognized");
                    redirect('gate?code=' . $code);
                }
                $this->session->unset_userdata(['status_check', 'message_check']);
            }
        } else {
            flash('danger', 'Only <strong>GET</strong> request allowed', 'gate');
        }
    }

    /**
     * Booking scan check
     * @param $code
     */
    private function checkBooking($code)
    {
        $booking = $this->booking->getBookingByNo($code);
        if (empty($booking) || in_array($booking['status'], [BookingModel::STATUS_BOOKED, BookingModel::STATUS_REJECTED])) {
            flash('status_check::danger', "message_check::Booking code <strong>{$code}</strong> is not found");
            redirect('gate?code=' . $code);
        } else if ($booking['category'] == 'OUTBOUND' && in_array($booking['status_payout'], ['PENDING', 'REJECTED'])) {
            flash('status_check::danger', "message_check::Booking code <strong>{$code}</strong> is not completing PAY OUT yet");
            redirect('gate?code=' . $code);
        } else if ($booking['category'] == 'OUTBOUND' && $booking['outbound_type'] == PeopleModel::OUTBOUND_CASH_AND_CARRY && !empty($booking['payout_until_date']) && $booking['payout_until_date'] < date('Y-m-d')) {
            flash('status_check::danger', "message_check::Booking code <strong>{$code}</strong> valid payment until " . format_date($booking['payout_until_date'], 'd F Y'));
            redirect('gate?code=' . $code);
        } else if ($booking['status'] == BookingModel::STATUS_COMPLETED) {
            flash('status_check::danger', "message_check::Booking code <strong>{$code}</strong> is COMPLETED, you are not allowed to make job that affected stock");
            redirect('gate?code=' . $code);
        } else {
            flash('status_check::success', "message_check::Recognized as <strong>{$booking['category']} Booking</strong> code pattern");
            $barcode = new \Milon\Barcode\DNS2D();
            $barcode->setStorPath(APPPATH . "/cache/");
            $qrCode = $barcode->getBarcodePNG($booking['no_booking'], "QRCODE", 8, 8);

            $bookingType = $booking['category'];
            $handlingTypeId = get_setting('default_outbound_handling');
            if ($bookingType == 'INBOUND') {
                $handlingTypeId = get_setting('default_inbound_handling');
            }

            $units = $this->unit->getAll();
            $components = $this->component->getByHandlingType($handlingTypeId);
            $componentTransactions = [];
            foreach ($components as $component) {
                $componentTransactions[$component['id']] = $this->componentOrder->getComponentOrdersByHandlingComponent($component['id'], true);
            }
            $workOrders = $this->workOrder->getWorkOrdersByBooking($booking['id']);
            $source = 'BOOKING';
            //$bookingContainers = $this->bookingContainer->getBookingContainersByBooking($booking['id']);
            $bookingContainers = $this->bookingContainer->getBookingContainersByBookingHandling($booking['id'], $booking['category']);
            foreach ($bookingContainers as &$container) {
                $containerGoods = $this->bookingGoods->getBookingGoodsByBookingContainer($container['id']);
                $container['goods'] = $containerGoods;
            }
            //$bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($booking['id'], true);
            $bookingGoods = $this->bookingGoods->getBookingGoodsByBookingHandling($booking['id'], true, $booking['category']);

            $data = [
                'title' => "Gate",
                'subtitle' => "Gate check point",
                'page' => 'gate/booking',
                'qrCode' => $qrCode,
                'booking' => $booking,
                'bookingContainers' => $bookingContainers,
                'bookingGoods' => $bookingGoods,
                'workOrders' => $workOrders,
                'source' => $source,
                'components' => $components,
                'componentTransactions' => $componentTransactions,
                'units' => $units,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get booking data for creating job.
     */
    public function ajax_get_booking_data()
    {
        $bookingId = $this->input->get('id_booking');
        $booking = $this->booking->getBookingById($bookingId);

        $bookingContainers = $this->bookingContainer->getBookingContainersByBookingHandling($booking['id'], $booking['category']);
        $bookingGoods = $this->bookingGoods->getBookingGoodsByBookingHandling($booking['id'], true, $booking['category']);

        header('Content-Type: application/json');
        echo json_encode([
            'booking' => $booking,
            'containers' => $bookingContainers,
            'goods' => $bookingGoods
        ]);
    }

    /**
     * Scan safe conduct code.
     * @param $code
     */
    private function checkSafeConduct($code)
    {
        $safeConduct = $this->safeConduct->getSafeConductByNo($code);
        if (empty($safeConduct)) {
            flash('status_check::danger', "message_check::Safe conduct code <strong>{$code}</strong> is not found");
            redirect('gate?code=' . $code);
        } else {
            flash('status_check::success', "message_check::Recognized as <strong>{$safeConduct['type']} Safe Conduct</strong> code pattern");
            $barcode = new \Milon\Barcode\DNS2D();
            $barcode->setStorPath(APPPATH . "/cache/");
            $qrCode = $barcode->getBarcodePNG($safeConduct['no_safe_conduct'], "QRCODE", 8, 8);

            $safeConductType = $safeConduct['type'];
            $handlingTypeId = get_setting('default_outbound_handling');
            if ($safeConductType == 'INBOUND') {
                $handlingTypeId = get_setting('default_inbound_handling');
            }

            $units = $this->unit->getAll();
            $components = $this->component->getByHandlingType($handlingTypeId);
            $componentTransactions = [];
            foreach ($components as $component) {
                $componentTransactions[$component['id']] = $this->componentOrder->getComponentOrdersByHandlingComponent($component['id'], true);
            }

            $workOrders = $this->workOrder->getWorkOrdersBySafeConduct($safeConduct['id']);
            $lockCreateJob = false;
            foreach ($workOrders as $workOrder) {
                if ($workOrder['id_handling_type'] == $handlingTypeId) {
                    $lockCreateJob = true;
                }
            }
            $bookingData = $this->booking->getBookingById($safeConduct['id_booking']);
            $documentTypes = $this->documentType->getByBookingType($bookingData['id_booking_type']);
            $uploadDocuments = $this->uploadDocument->getDocumentsByUpload($bookingData['id_upload']);

            // start -- lock create job by is required in master booking type
            $doStatus = false; 
            $lockCreateJobStatus = false;
            foreach ($documentTypes as $documentType) {
                if (($documentType['is_required'] == 3) && (in_array($documentType['id'], array_column($uploadDocuments, 'id_document_type')) == false)) {
                    $doStatus = true;
                    break;
                }
            }

            if ($doStatus == true) {
                $lockCreateJobStatus = true;
            }
            // end -- lock create job by is required in master booking type

            // start -- lock create job by security end before (internal inbound  only)
            $lockCreateJobBySecurityEnd = false;
            if($safeConduct['type'] == SafeConductModel::TYPE_INBOUND && $safeConduct['expedition_type'] == "INTERNAL"){
                if(empty($safeConduct['security_out_date'])){
                    $lockCreateJobBySecurityEnd = true;
                }
            }
            // end -- lock create job by security end before (internal inbound  only)

            $data = [
                'title' => "Gate",
                'subtitle' => "Gate check point",
                'page' => 'gate/safe_conduct',
                'qrCode' => $qrCode,
                'safeConduct' => $safeConduct,
                'workOrders' => $workOrders,
                'components' => $components,
                'componentTransactions' => $componentTransactions,
                'units' => $units,
                'lockCreateJob' => $lockCreateJob,
                'lockCreateJobStatus' => $lockCreateJobStatus,
                'lockCreateJobBySecurityEnd' => $lockCreateJobBySecurityEnd,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Scan handling pattern.
     * @param $code
     */
    private function checkHandling($code)
    {
        $handling = $this->handling->getHandlingByNo($code, true);
        $handlingType = $this->handlingType->getHandlingTypeById($handling['id_handling_type']);
        $workOrders = $this->workOrder->getWorkOrdersByHandling($handling['id']);
        $booking = $this->booking->getBookingById($handling['id_booking']);

        // check invoice TPP branch
        $allowCreateJob = true;
        if (strpos(get_active_branch('branch'), 'TPP') !== false) {
            $invoiceHandling = $this->invoice->getInvoicesByNoReference($code, 'PUBLISHED');
            // only if we setting handling price
            $componentPrice = $this->componentPrice->getComponentPriceByBranchHandling($handling['id_branch'], $handling['id_handling_type']);
            $invoiceRequire = ($handling['handling_code'] == 'PL' || $handling['handling_code'] == 'PS' || $handling['handling_code'] == 'PP' || $handling['handling_code'] == 'MO' || $handling['handling_code'] == 'P2');
            if ($invoiceRequire) {
                $allowCreateJob = !empty($invoiceHandling) /* || empty($componentPrice) */
                ;
            }
        }

        if (empty($handling)) {
            flash('status_check::danger', "message_check::Handling code <strong>{$code}</strong> is not found");
            redirect('gate?code=' . $code);
        } else if (empty($workOrders) && $booking['status'] == BookingModel::STATUS_COMPLETED && $handling['id_handling_type'] != 11 && ($handlingType['multiplier_container'] != 0 || $handlingType['multiplier_goods'] != 0 )) {
            flash('status_check::danger', "message_check::Handling code <strong>{$code}</strong> cannot converted to job because related booking is COMPLETED");
            redirect('gate?code=' . $code);
        } else {
            flash('status_check::success', "message_check::Recognized as ORDER <strong>{$handling['handling_type']}</strong> Job code pattern");
            $barcode = new \Milon\Barcode\DNS2D();
            $barcode->setStorPath(APPPATH . "/cache/");
            $qrCode = $barcode->getBarcodePNG($handling['no_handling'], "QRCODE", 8, 8);

            $units = $this->unit->getAll();
            $components = $this->component->getByHandlingType($handling['id_handling_type']);
            $componentTransactions = [];
            foreach ($components as $component) {
                $componentTransactions[$component['id']] = $this->componentOrder->getComponentOrdersByHandlingComponent($component['id'], true);
            }
            $data = [
                'title' => "Gate",
                'subtitle' => "Gate check point",
                'page' => 'gate/handling',
                'handling' => $handling,
                'workOrders' => $workOrders,
                'qrCode' => $qrCode,
                'components' => $components,
                'componentTransactions' => $componentTransactions,
                'units' => $units,
                'allowCreateJob' => $allowCreateJob
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Show detail job in gate.
     * @param $code
     */
    private function checkJob($code)
    {
        $workOrder = $this->workOrder->getWorkOrderByNo($code);
        if (empty($workOrder)) {
            flash('status_check::danger', "message_check::Job code <strong>{$code}</strong> is not found");
            redirect('gate?code=' . $code);
        } else {
            flash('status_check::success', "message_check::Recognized as <strong>{$workOrder['handling_type']}</strong> Job code pattern");
            $barcode = new \Milon\Barcode\DNS2D();
            $barcode->setStorPath(APPPATH . "/cache/");
            $qrCode = $barcode->getBarcodePNG($workOrder['no_work_order'], "QRCODE", 8, 8);

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

            $units = $this->unit->getAll();
            $components = $this->component->getByHandlingType($workOrder['id_handling_type'], $workOrder['id_handling']);
            $componentTransactions = [];
            $components2 = $this->workOrderComponent->getWorkOrderComponentsByWorkOrder($workOrder['id']);
            $i=0;
            foreach ($components as $component) {
                $componentTransactions[$component['id']] = $this->componentOrder->getComponentOrdersByHandlingComponent($component['id'], true);
                foreach ($components2 as $component2) {
                    if ($component['id']==$component2['id_component']) {
                        $components[$i]['handling_component_qty'] = $component2['quantity'];
                        $components[$i]['operator_name'] = $component2['operator_name'];
                        $components[$i]['is_owned'] = $component2['is_owned'];
                        $components[$i]['capacity'] = $component2['capacity'];
                        if (!empty($component2['description'])) {
                            $components[$i]['description'] = $component2['description'];
                        }
                    }
                }
                $i++;
            }
            
            $data = [
                'title' => "Gate",
                'subtitle' => "Gate check point",
                'page' => 'gate/job',
                'workOrder' => $workOrder,
                'qrCode' => $qrCode,
                'containers' => $containers,
                'goods' => $goods,
                'components' => $components,
                'componentTransactions' => $componentTransactions,
                'units' => $units
            ];
            $this->load->view('template/layout', $data);
        }
    }
}