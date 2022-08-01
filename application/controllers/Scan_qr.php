<?php

use Milon\Barcode\DNS2D;

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Scan_qr
 * @property BookingModel $booking
 * @property UploadModel $upload
 * @property SafeConductModel $safeConduct
 * @property SafeConductContainerModel $safeConductContainer
 * @property SafeConductGoodsModel $safeConductGoods
 * @property SafeConductChecklistModel $safeConductChecklist
 * @property TransporterEntryPermitModel $transporterEntryPermit
 * @property TransporterEntryPermitContainerModel $transporterEntryPermitContainer
 * @property TransporterEntryPermitGoodsModel $transporterEntryPermitGoods
 * @property TransporterEntryPermitCustomerModel $transporterEntryPermitCustomer
 * @property TransporterEntryPermitBookingModel $transporterEntryPermitBooking
 * @property TransporterEntryPermitUploadModel $transporterEntryPermitUpload
 * @property PeopleModel $people
 * @property ReportStockModel $reportStock
 * @property WorkOrderModel $workOrder
 * @property WorkOrderGoodsModel $workOrderGoods
 */
class Scan_qr extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingTypeModel', 'bookingType');
        $this->load->model('SafeConductModel', 'safeConduct');
        $this->load->model('SafeConductContainerModel', 'safeConductContainer');
        $this->load->model('SafeConductGoodsModel', 'safeConductGoods');
        $this->load->model('SafeConductChecklistModel', 'safeConductChecklist');
        $this->load->model('TransporterEntryPermitModel', 'transporterEntryPermit');
        $this->load->model('TransporterEntryPermitContainerModel', 'transporterEntryPermitContainer');
        $this->load->model('TransporterEntryPermitGoodsModel', 'transporterEntryPermitGoods');
        $this->load->model('TransporterEntryPermitCustomerModel', 'transporterEntryPermitCustomer');
        $this->load->model('TransporterEntryPermitBookingModel', 'transporterEntryPermitBooking');
        $this->load->model('TransporterEntryPermitUploadModel', 'transporterEntryPermitUpload');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('ReportStockModel', 'reportStock');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('UploadModel', 'upload');
    }

    /**
     * Scan qr code view and result.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SCAN_QR_DATA);

        $code = urldecode(trim($this->input->get('code')));

        if (empty($code)) {
            $data = [
                'title' => "Search QR code",
                'subtitle' => "Search data by qr code",
                'page' => "qr/index",
            ];
            $this->load->view('template/layout', $data);
        } else {
            $codePrefix = explode('/', $code);
            switch ($codePrefix[0]) {
                case SafeConductModel::TYPE_CODE_IN:
                case SafeConductModel::TYPE_CODE_OUT:
                    $this->checkSafeConduct($code);
                    break;
                case TransporterEntryPermitModel::TEP_CODE:
                    $this->checkTransporterEntryPermit($code);
                    break;
                default:
                    // because pallet number has no pattern, try to search and decide if pattern recognition or not
                    $this->checkPalletNumber($code);
                    break;
            }
        }
    }

    /**
     * Show result scan of safe conduct.
     *
     * @param $code
     */
    private function checkSafeConduct($code)
    {
        $conditions = ['no_safe_conduct' => $code];
        if (UserModel::authenticatedUserData('user_type') == 'EXTERNAL') {
            $conditions['bookings.id_customer'] = UserModel::authenticatedUserData('id_person');
        }
        $safeConduct = $this->safeConduct->getBy($conditions, true);
        if (empty($safeConduct)) {
            flash('danger', 'Safe conduct code <strong>' . $code . '</strong> is not found', 'scan-qr');
        } else {
            $safeConductContainers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($safeConduct['id']);
            foreach ($safeConductContainers as &$container) {
                $containerGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConductContainer($container['id']);
                $container['goods'] = $containerGoods;
            }
            $safeConductGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($safeConduct['id'], true);
            $safeConductChecklists = $this->safeConductChecklist->getSafeConductChecklistBySafeConductId($safeConduct['id']);

            $barcode = new DNS2D();
            $barcode->setStorPath(APPPATH . "/cache/");
            $qrCode = $barcode->getBarcodePNG($safeConduct['no_safe_conduct'], "QRCODE", 4, 4);

            $data = [
                'title' => "Search QR code",
                'subtitle' => "Found as safe conduct code",
                'page' => "qr/safe_conduct",
                'qrCode' => $qrCode,
                'safeConduct' => $safeConduct,
                'safeConductContainers' => $safeConductContainers,
                'safeConductGoods' => $safeConductGoods,
                'safeConductChecklists' => $safeConductChecklists,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Transporter entry permit scan check.
     *
     * @param $code
     */
    private function checkTransporterEntryPermit($code)
    {
        $tep = $this->transporterEntryPermit->getBy(['transporter_entry_permits.tep_code' => $code], true);
        if (empty($tep)) {
            flash('danger', 'Entry permit code <strong>' . $code . '</strong> is not found', 'scan-qr');
        } else {
            $tepContainers = $this->transporterEntryPermitContainer->getTepContainersByTep($tep['id']);
            foreach ($tepContainers as &$container) {
                $containerGoods = $this->transporterEntryPermitGoods->getTepGoodsByTepContainer($container['id']);
                $container['goods'] = $containerGoods;
            }
            $tepGoods = $this->transporterEntryPermitGoods->getTEPGoodsByTEP($tep['id'], true);
            $safeConducts = $this->safeConduct->getBy(['safe_conducts.id_transporter_entry_permit' => $tep['id']]);

            $customers = [];
            if (empty($tep['id_customer'])) {
                $tepCustomers = $this->transporterEntryPermitCustomer->getBy(['id_tep' => $tep['id']]);
                foreach ($tepCustomers as $customer) {
                    $customers[] = $this->people->getById($customer['id_customer']);
                }
            } else {
                $customers[] = $this->people->getById($tep['id_customer']);
            }

            if (UserModel::authenticatedUserData('user_type') == 'EXTERNAL') {
                $customerId = UserModel::authenticatedUserData('id_person');
                $isOwnedByCustomer = false;
                foreach ($customers as $customer) {
                    if ($customer['id'] == $customerId) {
                        $isOwnedByCustomer = true;
                        break;
                    }
                }
                if (!$isOwnedByCustomer) {
                    flash('danger', 'Entry permit code <strong>' . $code . '</strong> is not found', 'scan-qr');
                }
            }

            $bookings = [];
            if ($tep['tep_category'] == 'INBOUND') {
                if (empty($tep['id_booking'])) {
                    $tepBookings = $this->transporterEntryPermitBooking->getBy(['id_tep' => $tep['id']]);
                    foreach ($tepBookings as $tepBooking) {
                        $bookings[] = $this->booking->getBookingById($tepBooking['id_booking']);
                    }
                } else {
                    $bookings[] = $this->booking->getBookingById($tep['id_booking']);
                }
            } else if ($tep['tep_category'] == 'OUTBOUND') {
                $tepUploads = $this->transporterEntryPermitUpload->getBy(['id_tep' => $tep['id']]);
                foreach ($tepUploads as $tepUpload) {
                    $bookings[] = $this->upload->getById($tepUpload['id_upload']);
                }
            } else {
                $safeConducts = $this->safeConduct->getSafeConductsByTepId($tep['id']);
                foreach ($safeConducts as $safeConduct) {
                    $bookings[] = $this->booking->getBookingById($safeConduct['id']);
                }
            }

            $barcode = new DNS2D();
            $barcode->setStorPath(APPPATH . "/cache/");
            $qrCode = $barcode->getBarcodePNG($tep['tep_code'], "QRCODE", 4, 4);

            $data = [
                'title' => "Search QR code",
                'subtitle' => "Found as TEP code",
                'page' => "qr/tep",
                'qrCode' => $qrCode,
                'tep' => $tep,
                'tepContainers' => $tepContainers,
                'tepGoods' => $tepGoods,
                'customers' => $customers,
                'safeConducts' => $safeConducts,
                'bookings' => $bookings,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Check pallet marking scan check.
     *
     * @param $code
     */
    private function checkPalletNumber($code)
    {
        $customerId = '';
        if (UserModel::authenticatedUserData('user_type') == 'EXTERNAL') {
            $customerId = UserModel::authenticatedUserData('id_person');
        }
        $workOrders = $this->workOrder->getWorkOrderByNoPallet($code, $customerId);
        if (empty($workOrders)) {
            flash('danger', 'Pattern code <strong>' . $code . '</strong> is not recognized', 'scan-qr');
        } else {
            $barcode = new DNS2D();
            $barcode->setStorPath(APPPATH . "/cache/");
            $qrCode = $barcode->getBarcodePNG($code, "QRCODE", 4, 4);

            $goods = $this->workOrderGoods->getBy([
                'work_order_goods.id_work_order' => $workOrders[0]['id'],
                'work_order_goods.no_pallet' => $code
            ], true);

            $stockGoods = $this->reportStock->getStockGoods([
                'data' => 'all',
                'booking' => $goods['id_booking'],
                'no_pallet' => $code,
            ]);

            $data = [
                'title' => "Search QR code",
                'subtitle' => "Found as Pallet code",
                'page' => "qr/pallet",
                'qrCode' => $qrCode,
                'code' => $code,
                'workOrders' => $workOrders,
                'goods' => $goods,
                'stockGoods' => $stockGoods,
            ];
            $this->load->view('template/layout', $data);
        }
    }
}