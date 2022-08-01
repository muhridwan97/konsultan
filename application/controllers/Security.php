<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

defined('BASEPATH') or exit('No direct script access allowed');

use Milon\Barcode\DNS2D;

/**
 * Class Security
 * @property BookingModel $booking
 * @property BookingContainerModel $bookingContainer
 * @property BookingGoodsModel $bookingGoods
 * @property SafeConductModel $safeConduct
 * @property SafeConductContainerModel $safeConductContainer
 * @property SafeConductGoodsModel $safeConductGoods
 * @property ChecklistTypeModel $checklistType
 * @property ChecklistModel $checklist
 * @property SafeConductChecklistModel $safeConductChecklist
 * @property SafeConductChecklistDetailModel $safeConductChecklistDetail
 * @property TransporterEntryPermitModel $transporterEntryPermit
 * @property TransporterEntryPermitRequestTepModel $transporterEntryPermitRequestTep
 * @property TransporterEntryPermitChassisModel $transporterEntryPermitChassis
 * @property DocumentTypeModel $documentType
 * @property UploadDocumentFileModel $uploadDocumentFile
 * @property LogModel $logHistory
 * @property TransporterEntryPermitCustomerModel $transporterEntryPermitCustomer
 * @property TransporterEntryPermitBookingModel $transporterEntryPermitBooking
 * @property TransporterEntryPermitChecklistPhotoModel $transporterEntryPermitChecklistPhoto
 * @property NotificationModel $notification
 * @property WorkOrderModel $workOrder
 * @property PeopleModel $people
 * @property HeavyEquipmentEntryPermitModel $heavyEquipmentEntryPermit
 * @property SecurityCheckPhotoTypeModel $securityCheckPhotoType
 * @property SafeConductChecklistPhotoModel $safeConductChecklistPhoto
 * @property Uploader $uploader
 */
class Security extends CI_Controller
{
    /**
     * Security constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('BookingTypeModel', 'BookingType');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('SafeConductModel', 'safeConduct');
        $this->load->model('SafeConductContainerModel', 'safeConductContainer');
        $this->load->model('SafeConductGoodsModel', 'safeConductGoods');
        $this->load->model('ChecklistTypeModel', 'checklistType');
        $this->load->model('ChecklistModel', 'checklist');
        $this->load->model('SafeConductChecklistModel', 'safeConductChecklist');
        $this->load->model('SafeConductChecklistDetailModel', 'safeConductChecklistDetail');
        $this->load->model('TransporterEntryPermitModel', 'transporterEntryPermit');
        $this->load->model('TransporterEntryPermitContainerModel', 'transporterEntryPermitContainer');
        $this->load->model('TransporterEntryPermitGoodsModel', 'transporterEntryPermitGoods');
        $this->load->model('TransporterEntryPermitChecklistModel', 'TransporterEntryPermitChecklist');
        $this->load->model('TransporterEntryPermitChecklistDetailModel', 'TransporterEntryPermitChecklistDetail');
        $this->load->model('TransporterEntryPermitRequestTepModel', 'transporterEntryPermitRequestTep');
        $this->load->model('TransporterEntryPermitChassisModel', 'transporterEntryPermitChassis');
        $this->load->model('TransporterEntryPermitChecklistPhotoModel', 'transporterEntryPermitChecklistPhoto');
        $this->load->model('NotificationModel', 'notification');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
        $this->load->model('LogModel', 'logHistory');
        $this->load->model('ReportStockModel', 'reportStock');
        $this->load->model('EsealModel', 'eseal');
        $this->load->model('TransporterEntryPermitCustomerModel', 'transporterEntryPermitCustomer');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('HeavyEquipmentEntryPermitModel', 'heavyEquipmentEntryPermit');        
        $this->load->model('TransporterEntryPermitBookingModel', 'transporterEntryPermitBooking');
        $this->load->model('HeavyEquipmentEntryPermitModel', 'heavyEquipmentEntryPermit');
        $this->load->model('SecurityCheckPhotoTypeModel', 'securityCheckPhotoType');
        $this->load->model('SafeConductChecklistPhotoModel', 'safeConductChecklistPhoto');
        $this->load->model('modules/Uploader', 'uploader');
        $this->load->model('modules/S3FileManager', 's3FileManager');
    }

    /**
     * Show security scan form.
     */
    public function index()
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_SECURITY_CHECK_IN, PERMISSION_SECURITY_CHECK_OUT], false);
        
        $data = [
            'title' => "Security",
            'subtitle' => "Security check point",
            'page' => "security/index"
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Check scanned code.
     */
    public function check()
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_SECURITY_CHECK_IN, PERMISSION_SECURITY_CHECK_OUT], false);

        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $this->form_validation->set_data($this->input->get());
            $this->form_validation->set_rules('code', 'Safe conduct code', 'trim|required|max_length[50]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid', 'security');
            } else {
                $code = urldecode(trim($this->input->get('code')));
                $codePrefix = explode('/', $code);

                switch ($codePrefix[0]) {
                    case BookingModel::NO_INBOUND:
                    case BookingModel::NO_OUTBOUND:
                        $this->checkBooking($code);
                        break;
                    case SafeConductModel::TYPE_CODE_IN:
                    case SafeConductModel::TYPE_CODE_OUT:
                    case SafeConductModel::TYPE_CODE_GROUP:
                        if ($codePrefix[0] == SafeConductModel::TYPE_CODE_GROUP) {
                            $safeConductItem = $this->safeConduct->getBy([
                                'no_safe_conduct_group' => $code
                            ], true);
                            if (!empty($safeConductItem)) {
                                $code = $safeConductItem['no_safe_conduct'];
                            }
                        }
                        $this->checkSafeConduct($code);
                        break;
                    case TransporterEntryPermitModel::TEP_CODE:
                        $this->checkTransporterEntryPermit($code);
                        break;
                    case HeavyEquipmentEntryPermitModel::HEEP_CODE:
                        $this->checkHeavyEquipmentEntryPermit($code);
                        break;
                    default:
                        flash('status_check::danger', "message_check::Pattern code <strong>{$code}</strong> is not recognized");
                        redirect('security?code=' . $code);
                        break;
                }
                $this->session->unset_userdata(['status_check', 'message_check']);
            }
        } else {
            flash('danger', 'Only <strong>GET</strong> request allowed', 'security');
        }
    }

    /**
     * Booking scan check.
     *
     * @param $code
     */
    private function checkBooking($code)
    {
        $booking = $this->booking->getBookingByNo($code);
        if (empty($booking)) {
            flash('status_check::danger', "message_check::Booking code <strong>{$code}</strong> is not found");
            redirect('security?code=' . $code);
        } else {
            flash('status_check::success', "message_check::Recognized as <strong>{$booking['category']} Booking</strong> code pattern");
            $barcode = new DNS2D();
            $barcode->setStorPath(APPPATH . "/cache/");
            $qrCode = $barcode->getBarcodePNG($booking['no_booking'], "QRCODE", 8, 8);

            $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($booking['id']);
            foreach ($bookingContainers as &$container) {
                $containerGoods = $this->bookingGoods->getBookingGoodsByBookingContainer($container['id']);
                $container['goods'] = $containerGoods;
            }
            $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($booking['id'], true);

            $data = [
                'title' => "Security",
                'subtitle' => "Security check point",
                'page' => 'security/booking',
                'qrCode' => $qrCode,
                'booking' => $booking,
                'bookingContainers' => $bookingContainers,
                'bookingGoods' => $bookingGoods,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Show result scan of safe conduct.
     *
     * @param $code
     */
    private function checkSafeConduct($code)
    {
        $safeConduct = $this->safeConduct->getSafeConductByNo($code);

        if (empty($safeConduct)) {
            flash('status_check::danger', "message_check::Safe conduct code <strong>{$code}</strong> is not found");
            redirect('security?code=' . $code);
        } else if (false && $safeConduct['id_customer'] == 9151 && $safeConduct['expedition_type'] == 'EXTERNAL' && !empty($safeConduct['id_safe_conduct_group'])) {
            flash('status_check::danger', 'message_check::External delivery SMGP should updated from Medan 2');
            redirect('security?code=' . $code);
        } else {
            $allSafeConducts = [$safeConduct];
            if (!empty($safeConduct['id_safe_conduct_group'])) {
                $relatedSafeConducts = $this->safeConduct->getBy([
                    'safe_conducts.id!=' => $safeConduct['id'],
                    'safe_conducts.id_safe_conduct_group' => $safeConduct['id_safe_conduct_group']
                ]);
                $allSafeConducts = array_merge($allSafeConducts, $relatedSafeConducts);
            }
            foreach ($allSafeConducts as &$allSafeConduct) {
                $safeConductContainers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($allSafeConduct['id']);
                foreach ($safeConductContainers as &$container) {
                    $containerGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConductContainer($allSafeConduct['id']);
                    $container['goods'] = $containerGoods;
                }
                $safeConductGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($allSafeConduct['id'], true);

                $allSafeConduct['containers'] = $safeConductContainers;
                $allSafeConduct['goods'] = $safeConductGoods;
            }

            // check if related safe conduct with current no police is used
            // exclude related safe conduct (in same group)
            $usedVehicles = $this->safeConduct->getBy([
                'safe_conducts.id!=' => $safeConduct['id'],
                //'IFNULL(safe_conducts.id_safe_conduct_group, "")!=' => if_empty($safeConduct['id_safe_conduct_group'], ''),
                'safe_conducts.no_police' => $safeConduct['no_police'],
                'safe_conducts.security_in_date IS NOT NULL' => null,
                'safe_conducts.security_out_date IS NULL' => null,
            ]);
            if (!empty($usedVehicles)) {
                // old data may be trapped in condition 2 or more safe conduct with same no police no checked out,
                // then give a chance to check out, later it should not possible to be happen in that situation.
                if (!empty($safeConduct['security_in_date'])) {
                    foreach ($usedVehicles as $indexUsed => $usedVehicle) {
                        if (!empty($usedVehicle['security_in_date']) && empty($usedVehicle['security_out_date'])) {
                            unset($usedVehicles[$indexUsed]);
                        }
                    }
                }
                if (!empty($usedVehicles)) {
                    flash('status_check::danger', "message_check::Plat police no <strong>{$safeConduct['no_police']}</strong> is used in " . implode(', ', array_column($usedVehicles, 'no_safe_conduct')) . ", please check out these safe conduct first!");
                    redirect('security?code=' . $code);
                }
            }

            flash('status_check::success', "message_check::Recognized as <strong>{$safeConduct['type']} Safe Conduct</strong> code pattern");

            $barcode = new DNS2D();
            $barcode->setStorPath(APPPATH . "/cache/");
            $qrCode = $barcode->getBarcodePNG($safeConduct['no_safe_conduct'], "QRCODE", 8, 8);
            $eseals = $this->eseal->getAll();

            $safeConductContainers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($safeConduct['id']);
            foreach ($safeConductContainers as &$container) {
                $containerGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConductContainer($container['id']);
                $container['goods'] = $containerGoods;
            }
            $safeConductGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($safeConduct['id'], true);
            $ChecklistTypes = $this->checklistType->getAll();
            $Checklists = $this->checklist->getAll();

            $data_checklists = [];
            $data_checklist_types = [];
            foreach ($ChecklistTypes as $ChecklistType) {
                foreach ($Checklists as $Checklist) {
                    if ($Checklist['id_checklist_type'] == $ChecklistType['id']) {
                        $data_checklist_types[] = $ChecklistType;
                        break;
                    }
                }
            }

            $allowCheckIn = true;
            $allowCheckOut = true;

            foreach ($allSafeConducts as $currentSafeConduct) {
                if (empty($currentSafeConduct['containers']) && empty($currentSafeConduct['goods'])) {
                    if ($currentSafeConduct['total_check_in'] <= 0) {
                        $allowCheckIn = false;
                    }
                    if ($currentSafeConduct['total_check_out'] <= 0) {
                        $allowCheckOut = false;
                    }
                }

                if (count($currentSafeConduct['goods']) > 0) {
                    if ($currentSafeConduct['total_check_in'] <= 0) {
                        $allowCheckIn = false;
                    }

                    if ($currentSafeConduct['total_check_out'] <= 0) {
                        $allowCheckOut = false;
                    }
                }

                foreach ($currentSafeConduct['containers'] as $safeConductContainer) {
                    if ($safeConductContainer['total_check_in'] <= 0) {
                        $allowCheckIn = false;
                    }

                    if ($safeConductContainer['total_check_in'] >= 1 && $currentSafeConduct['total_check_out'] <= 0) {//if check in with tep, checkout with safeconduct
                        $allowCheckOut = false;
                    }

                    if ($safeConductContainer['total_check_out'] <= 0 && $safeConductContainer['total_check_in'] <= 0) { // in case change container when check in and checkout
                        $allowCheckOut = false;
                    }
                }
            }

            /*
            $stockContainers = $this->reportStock->getStockContainers([
                'data' => 'stock',
                'booking' => $safeConduct['id_booking']
            ]);

            $stockGoods = $this->reportStock->getStockGoods([
                'data' => 'stock',
                'booking' => $safeConduct['id_booking']
            ]);
            */

            if(AuthorizationModel::isAuthorized(PERMISSION_SECURITY_CHECK_PHOTO)){
                $allowBrowse = 'UNLOCK';
            }else{
                $allowBrowse = 'LOCK';
            }

            $securityCheckPhotoTypes = $this->getSecurityCheckPhotos($safeConduct);

            $data = [
                'title' => "Security",
                'subtitle' => "Security check point",
                'page' => 'security/safe_conduct',
                'qrCode' => $qrCode,
                'safeConduct' => $safeConduct,
                'allSafeConducts' => $allSafeConducts,
                'safeConductContainers' => $safeConductContainers,
                'safeConductGoods' => $safeConductGoods,
                'ChecklistTypes' => $ChecklistTypes,
                'Checklists' => $Checklists,
                'data_checklists' => $data_checklists,
                'data_checklist_types' => $data_checklist_types,
                'allowCheckIn' => $allowCheckIn,
                'allowCheckOut' => $allowCheckOut,
                //'stockContainers' => $stockContainers,
                //'stockGoods' => $stockGoods,
                'eseals' => $eseals,
                'allowBrowse' => $allowBrowse,
                'securityCheckPhotoTypes' => $securityCheckPhotoTypes,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get security check photo type by conditions.
     *
     * @param $safeConduct
     * @return array|int
     */
    private function getSecurityCheckPhotos($safeConduct)
    {
        $category = 'INBOUND';
        if ($safeConduct['type'] == 'OUTBOUND') {
            $workOrders = $this->workOrder->getWorkOrdersBySafeConduct($safeConduct['id']);
            foreach ($workOrders as $workOrder) {
                if ($workOrder['handling_type'] == 'EMPTY CONTAINER') {
                    $category = 'EMPTY CONTAINER';
                    break;
                }
            }
        }
        return $this->securityCheckPhotoType->getBy([
            'category' => $category,
            'type' => empty($safeConduct['security_in_date']) ? 'START' : 'STOP'
        ]);
    }

    /**
     * Get security check photo type by conditions.
     *
     * @param $tep
     * @return array|int
     */
    private function getSecurityCheckTEPPhotos($tep)
    {
        return $this->securityCheckPhotoType->getBy([
            'category' => $tep['tep_category'],
            'type' => empty($tep['checked_in_at']) ? 'START' : 'STOP'
        ]);
    }

    /**
     * Booking scan check.
     *
     * @param $code
     */
    private function checkTransporterEntryPermit($code)
    {
        $tep = $this->transporterEntryPermit->getBy(['transporter_entry_permits.tep_code' => $code, 
            'NOW()<=transporter_entry_permits.expired_at OR(transporter_entry_permits.checked_in_at is not null AND transporter_entry_permits.checked_out_at is null AND transporter_entry_permits.tep_code = "'.$code.'")' => null], true);
	$isTepEmpty = empty($tep);
        // hard-coded prevent smgp in medan1 to be checked in-out (should use branch medan 2 linked)
        $linkedTepReference = $this->transporterEntryPermit->getAll([
            'branch' => 8,
            'linked_tep' => $tep['id']
        ]);
        $multiBookings = $this->transporterEntryPermitBooking->getBookingByIdTep($tep['id']);
        $tep['category'] = isset($multiBookings[0]['category']) ?  $multiBookings[0]['category'] : 'no category';
        $tep['id_booking'] = isset($multiBookings[0]['id_booking']) ?  $multiBookings[0]['id_booking'] : 'no category';
        if (!empty($linkedTepReference) && get_active_branch_id() == 2 && strpos($tep['id_customer_out'], '9151') !== false) {
            flash("status_check::danger", "message_check::TEP code <strong>{$code}</strong> should be linked and checked in Medan 2");
            redirect('security?code=' . $code);
        } elseif ($isTepEmpty) {
            flash('status_check::danger', "message_check::TEP code <strong>{$code}</strong> is not found or expired");
            redirect('security?code=' . $code);
        } else {
            if (date("Y-m-d",strtotime($tep['expired_at'])) != date('Y-m-d') && ($tep['tep_category'] == 'OUTBOUND' || !empty($tep['no_reference_in_req'])) && !(!empty($tep['checked_in_at']) && empty($tep['checked_out_at']) )){
                flash('status_check::danger', "message_check::TEP code <strong>{$code}</strong> is not for today");
                redirect('security?code=' . $code);
            }
            flash('status_check::success', "message_check::Recognized as <strong>{$tep['category']} TEP</strong> code pattern");
            $barcode = new DNS2D();
            $barcode->setStorPath(APPPATH . "/cache/");
            $qrCode = $barcode->getBarcodePNG($tep['tep_code'], "QRCODE", 8, 8);

            $eseals = $this->eseal->getAll();
            $getSafeConductByTep = $this->safeConduct->getSafeConductsByTepId($tep['id']);
            $tepContainers = $this->transporterEntryPermitContainer->getTepContainersByTep($tep['id']);
            foreach ($tepContainers as &$container) {
                $containerGoods = $this->transporterEntryPermitGoods->getTepGoodsByTepContainer($container['id']);
                $container['goods'] = $containerGoods;
            }
            $tepGoods = $this->transporterEntryPermitGoods->getTEPGoodsByTEP($tep['id'], true);

            $tepChecklists = $this->TransporterEntryPermitChecklist->getTepChecklistByTepId($tep['id']);
            $tep_checkin_data = $this->TransporterEntryPermitChecklist->getTepChecklistInByTepId($tep['id']);
            $tep_checkout_data = $this->TransporterEntryPermitChecklist->getTepChecklistOutByTepId($tep['id']);
            $ChecklistTypes = $this->checklistType->getAll();
            $Checklists = $this->checklist->getAll();
            $multiCustomer = $this->transporterEntryPermitCustomer->getCustomerByIdTep($tep['id']);

            $data_checklists = [];
            $data_checklist_types = [];
            foreach ($ChecklistTypes as $ChecklistType) {
                foreach ($Checklists as $Checklist) {
                    if ($Checklist['id_checklist_type'] == $ChecklistType['id']) {
                        $data_checklist_types[] = $ChecklistType;
                        break;
                    }
                }
            }

            $allowCheckIn = true;
            $allowCheckOut = true;

            if (count($tepGoods) > 0) {
                if ($tep['total_check_in'] <= 0) {
                    $allowCheckIn = false;
                }

                if ($tep['total_check_out'] <= 0) {
                    $allowCheckOut = false;
                }
            }

            foreach ($tepContainers as $tepContainer) {
                if ($tepContainer['total_check_in'] <= 0 && $tep['total_check_in'] <= 0) {
                    $allowCheckIn = false;
                }

                if ($tepContainer['total_check_out'] <= 0 && $tep['total_check_out'] <= 0) {
                    $allowCheckOut = false;
                }
            }
            if(AuthorizationModel::isAuthorized(PERMISSION_SECURITY_CHECK_PHOTO)){
                $allowBrowse = 'UNLOCK';
            }else{
                $allowBrowse = 'LOCK';
            }

            // check armada owner is TCI
            $requests = $this->transporterEntryPermitRequestTep->getBy([
                'transporter_entry_permit_request_tep.id_tep' => $tep['id']
            ]);
            $tep['armada_owner'] = '';
            foreach ($requests as $request) {
                if ($request['armada'] == 'TCI') {
                    $tep['armada_owner'] = $request['armada'];
                    break;
                }
            }

            $outstandingChassis = [];
            if (!empty($tep['checked_in_at']) && empty($tep['checked_out_at'])) {
                $outstandingChassis = $this->transporterEntryPermitChassis->getBy([
                    'transporter_entry_permit_chassis.checked_in_at IS NOT NULL' => null,
                    'transporter_entry_permit_chassis.checked_out_at' => null,
                ]);
            }

            $securityCheckPhotoTypes = $this->getSecurityCheckTEPPhotos($tep);
            $data = [
                'title' => "Security",
                'subtitle' => "Security check point",
                'page' => 'security/tep',
                'qrCode' => $qrCode,
                'tep' => $tep,
                'tepContainers' => $tepContainers,
                'tepGoods' => $tepGoods,
                'ChecklistTypes' => $ChecklistTypes,
                'Checklists' => $Checklists,
                'data_checklists' => $data_checklists,
                'data_checklist_types' => $data_checklist_types,
                'allowCheckIn' => $allowCheckIn,
                'allowCheckOut' => $allowCheckOut,
                'tepChecklists' => $tepChecklists,
                'tep_checkin_data' => $tep_checkin_data,
                'tep_checkout_data' => $tep_checkout_data,
                'eseals' => $eseals,
                'multiCustomer' => $multiCustomer,
                'multiBookings' => $multiBookings,
                'SafeConductByTEP' => $getSafeConductByTep,
                'allowBrowse' => $allowBrowse,
                'outstandingChassis' => $outstandingChassis,
                'securityCheckPhotoTypes' => $securityCheckPhotoTypes
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Heavy Equipment entry permit scan check.
     *
     * @param $code
     */
    private function checkHeavyEquipmentEntryPermit($code)
    {
        $heep = $this->heavyEquipmentEntryPermit->getBy(['heavy_equipment_entry_permits.heep_code' => $code, 
        'NOW()<=heavy_equipment_entry_permits.expired_at OR(heavy_equipment_entry_permits.checked_in_at is not null AND heavy_equipment_entry_permits.checked_out_at is null AND heavy_equipment_entry_permits.heep_code = "'.$code.'")' => null], true);
        // print_debug($heep);
        $tep="";
        if(AuthorizationModel::isAuthorized(PERMISSION_SECURITY_CHECK_PHOTO)){
            $allowBrowse = 'UNLOCK';
        }else{
            $allowBrowse = 'LOCK';
        }
        if (empty($heep)) {
            flash('status_check::danger', "message_check::HEEP code <strong>{$code}</strong> is not found or expired");
            redirect('security?code=' . $code);
        } else {
            if (date("Y-m-d",strtotime($heep['expired_at'])) != date('Y-m-d') && !(!empty($heep['checked_in_at']) && empty($heep['checked_out_at'])) ){
                flash('status_check::danger', "message_check::HEEP code <strong>{$code}</strong> is not for today");
                redirect('security?code=' . $code);
            }
            flash('status_check::success', "message_check::Recognized as <strong>HEEP</strong> code pattern");
            $barcode = new DNS2D();
            $barcode->setStorPath(APPPATH . "/cache/");
            $qrCode = $barcode->getBarcodePNG($heep['heep_code'], "QRCODE", 8, 8);


            $data = [
                'title' => "Security",
                'subtitle' => "Security check point",
                'page' => 'security/heep',
                'qrCode' => $qrCode,
                'heep' => $heep,
                'allowBrowse' => $allowBrowse,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    public function watermark($path, $photo_name, $hasilText)
    {
        $manager = new ImageManager();

        $img = $manager->make(asset_url($path));
        $width = 2000;
        $height = null;
        if ($img->getHeight() > $img->getWidth()) {
            $height = 2000;
            $width = null;
        }

        // now you are able to resize the instance
        $img->resize($width, $height, function ($constraint) {
            $constraint->aspectRatio();
        });

        // and insert a watermark for example
        if ($img->getHeight() > $img->getWidth()) {
            $watermark = $manager->make('assets/app/img/layout/watermark2.png');
            $watermark->resize(550, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        } else {
            $watermark = $manager->make('assets/app/img/layout/watermark1.png');
            $watermark->resize(850, null, function ($constraint) {
                $constraint->aspectRatio();
            });
        }
        $img->insert($watermark, 'top-right', 1, 1);

        $img->text($hasilText, 50, $img->getHeight() - 290, function ($font) {
            $font->file(FCPATH . 'assets/plugins/font-googleapis/fonts/SourceSansPro-Bold.ttf');
            $font->size(65);
            $font->color('#FFFF00');
            $font->align('left');
            $font->valign('middle');
        });
        $data = $img->exif();

        $img = $img->encode('jpg', 75);
        $result = $this->s3FileManager->putObjectStream(env('S3_BUCKET'), $path, $img->stream(), $img->mime());
        if ($result) {
            return true;
        } else {
            return false;
        }
    }

    /**
     * Send a message to a new or existing chat.
     * 6281333377368-1557128212@g.us
     * @param $filePath
     * @param $whatsappGroup
     * @param string $whatsappGroupInternal
     * @param null $caption
     */
    public function send_message($filePath, $whatsappGroup, $whatsappGroupInternal = null, $caption = null)
    {
        if (!empty($whatsappGroup)) {
            $data = [
                'url' => 'sendFile',
                'method' => 'POST',
                'payload' => [
                    'chatId' => detect_chat_id($whatsappGroup),
                    'body' => asset_url($filePath), //'https://transcon-indonesia.com/img/front/opening/icon.png',
                    'filename' => 'transcon.png',
                ]
            ];
            if (!empty($caption)) {
                $data['payload']['caption'] = $caption;
            }

            $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);
        }

        if (!empty($whatsappGroupInternal)) {
            $data2 = [
                'url' => 'sendFile',
                'method' => 'POST',
                'payload' => [
                    'chatId' => detect_chat_id($whatsappGroupInternal),
                    'body' => asset_url($filePath), //'https://transcon-indonesia.com/img/front/opening/icon.png',
                    'filename' => 'transcon.png',
                ]
            ];

            $this->notification->broadcast($data2, NotificationModel::TYPE_CHAT_PUSH);
        }
    }


    /**
     * Show result security checklist of safe conduct.
     */
    public function security_checklist()
    {
        $redirect = $this->input->get('redirect');
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('reason', 'Restriction', 'trim|max_length[500]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $id_tep = $this->input->post('id_tep');
                $id_tep_container = $this->input->post('id_tep_container');
                $id_safe_conduct = $this->input->post('id_safe_conduct');
                $id_safe_conduct_container = $this->input->post('id_safe_conduct_container');
                
                $tep = $this->transporterEntryPermit->getByIdNonBase($id_tep);
                $tepContainers = $this->transporterEntryPermitContainer->getTepContainersByTep($tep['id']);
                foreach ($tepContainers as &$container) {
                    $containerGoods = $this->transporterEntryPermitGoods->getTepGoodsByTepContainer($container['id']);
                    $container['goods'] = $containerGoods;
                }
                $tepGoods = $this->transporterEntryPermitGoods->getTEPGoodsByTEP($tep['id'], true);

                $safeConduct = $this->safeConduct->getSafeConductById($id_safe_conduct);
                $multiAju = $this->transporterEntryPermitCustomer->getBookingByIdTep($safeConduct['id_transporter_entry_permit']);
                $safeConductContainers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($safeConduct['id']);
                foreach ($safeConductContainers as &$container) {
                    $containerGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConductContainer($container['id']);
                    $container['goods'] = $containerGoods;
                }
                $safeConductGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($safeConduct['id'], true);

                $no_container = $this->input->post('no_container');
                $type = $this->input->post('security_type');
                $id_checklists = $this->input->post('id_checklists');
                $results = $this->input->post('result');
                $reasons = $this->input->post('reason');
                //butuh method
                if (!empty($id_safe_conduct)) {

                    if (empty($id_safe_conduct_container)) {
                        $id_container = null;
                    } else {
                        $id_container = $id_safe_conduct_container;
                    }

                    $this->db->trans_start();

                    //upload attachment if exist
                    $attachmentSeal = '';
                    $description ='';
                    $uploadPassed = true;
                    $files = $this->input->post('attachment_name');
                    $photoNames = $this->input->post('security_check_photos');

                    if (!empty($files)) {
                        $uploadStatus = true;
                        foreach ($files as $index => $file) {
                            $uploadedPhoto = 'temp/' . $file;
                            $saveTo = 'safe_conducts_checklist/' . date('Y/m/') . $file;
                            $status = $this->uploader->setDriver('s3')->move($uploadedPhoto, $saveTo);
                            if (!$status) {
                                $uploadStatus = false;
                            }
                            $files[$index] = $saveTo;
                        }
                        if (!$uploadStatus) {
                            flash('danger', "Security Checklist {$safeConduct['no_safe_conduct']} failed, upload to server failed");
                            redirect('security/check?code=' . $safeConduct['no_safe_conduct']);
                        }
                    } else {
                        flash('danger', "Security Checklist {$safeConduct['no_safe_conduct']} failed, You haven't uploaded a photo yet");
                        redirect('security/check?code=' . $safeConduct['no_safe_conduct']);
                    }

                    //seal photo
                    $fileAttachments = $this->input->post('attachment_seal_name');
                    if (!empty($fileAttachments)) {
                        //$attachmentSeal = 'SEAL_' . time() . '_' . rand(100, 999);
                        //$uploadedPhoto = 'temp/' . DIRECTORY_SEPARATOR . $fileAttachments[0];
                        //$saveTo = 'safe_conducts_checklist' . DIRECTORY_SEPARATOR . $attachmentSeal . '.' .$extension;
                        
                        //$status= $this->uploader->move($uploadedPhoto, $saveTo);

                        $uploadedPhoto = 'temp/' . $fileAttachments[0];
                        $saveTo = 'safe_conducts_checklist/' . date('Y/m/') . $fileAttachments[0];
                        $status = $this->uploader->setDriver('s3')->move($uploadedPhoto, $saveTo);
                        if (!$status) {
                            $uploadPassed = false;
                            flash('danger', "Security Checklist {$safeConduct['no_safe_conduct']} failed, uplod server fail");
                            redirect('security/check?code=' . $safeConduct['no_safe_conduct']);
                        }else{
                            //$attachmentSeal = $attachmentSeal . '.' .$extension;
                            $attachmentSeal = $saveTo;
                            $description = $this->input->post('description');
                        }
                    }

                    if ($uploadPassed) {
                        // safe conduct checklist
                        $this->safeConductChecklist->create([
                            'id_safe_conduct' => $id_safe_conduct,
                            'id_container' => $id_container,
                            'type' => $type,
                            //'attachment' => $fileName,
                            'attachment_seal' => $attachmentSeal,
                            'description' => $description,
                        ]);

                        //safe conduct detail
                        $safeConductChecklistId = $this->db->insert_id();
                        foreach ($id_checklists as $index => $id_checklist) {
                            $this->safeConductChecklistDetail->create([
                                'id_safe_conduct_checklist' => $safeConductChecklistId,
                                'id_checklist' => $id_checklist,
                                'result' => in_array($id_checklist, if_empty($results, [])),
                                'description' => $reasons[$index],
                            ]);
                        }

                        // checklist photo
                        foreach ($files as $index => $file) {
                            $this->safeConductChecklistPhoto->create([
                                'id_safe_conduct_checklist' => $safeConductChecklistId,
                                'photo' => $file,
                                'title' => $photoNames[$index] ?? 'Attachment'
                            ]);
                        }

                        $safeConductJobs = $this->workOrder->getWorkOrdersBySafeConduct($safeConduct['id']);

                        // add same checklist photo as current checked safe conduct data, check in only (INTERNAL)
                        // check out (before loading) each safe conduct (member of group) may be loading different container
                        // TODO: update safe conduct group
                        // TODO: some data is not relevant (if one vehicle loading container of 2 different customers)
                        $allSafeConducts = [$safeConduct];
                        if (/*$type == 'CHECK IN' && */!empty($safeConduct['id_safe_conduct_group'])) {
                            $relatedSafeConducts = $this->safeConduct->getBy([
                                'safe_conducts.id!=' => $safeConduct['id'],
                                'safe_conducts.id_safe_conduct_group' => $safeConduct['id_safe_conduct_group']
                            ]);
                            foreach ($relatedSafeConducts as $relatedSafeConduct) {
                                $allSafeConducts = array_merge($allSafeConducts, [$relatedSafeConduct]);
                                $safeConductJobs = array_merge($safeConductJobs, $this->workOrder->getWorkOrdersBySafeConduct($relatedSafeConduct['id']));
                                $relatedContainers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($relatedSafeConduct['id']);
                                $relatedContainerId = $id_container;
                                if(empty($relatedContainers)) {
                                    $relatedContainerId = null; // if another is empty / goods load
                                } else {
                                    continue; // skip it, ask for checklist manual each container
                                }

                                $this->safeConductChecklist->create([
                                    'id_safe_conduct' => $relatedSafeConduct['id'],
                                    'id_container' => $relatedContainerId,
                                    'type' => $type,
                                    //'attachment' => $fileName,
                                    'attachment_seal' => $attachmentSeal,
                                    'description' => $description,
                                ]);
                                $safeConductChecklistId = $this->db->insert_id();
                                foreach ($id_checklists as $index => $id_checklist) {
                                    $this->safeConductChecklistDetail->create([
                                        'id_safe_conduct_checklist' => $safeConductChecklistId,
                                        'id_checklist' => $id_checklist,
                                        'result' => in_array($id_checklist, if_empty($results, [])),
                                        'description' => $reasons[$index],
                                    ]);
                                }

                                // checklist photo
                                foreach ($files as $index => $file) {
                                    $this->safeConductChecklistPhoto->create([
                                        'id_safe_conduct_checklist' => $safeConductChecklistId,
                                        'photo' => $file,
                                        'title' => $photoNames[$index] ?? 'Attachment'
                                    ]);
                                }
                            }
                        }

                        // get related job, 1 safe conduct could have many jobs (unload & moving in, empty container & moving out, etc)
                        // exclude moving in and out and take out only latest one job regarding the safe conduct data
                        $relatedWorkOrders = array_filter($safeConductJobs, function ($workOrder) {
                            return !in_array($workOrder['handling_type'], [get_setting('default_moving_in_handling'), get_setting('default_moving_out_handling')]);
                        });
                        $relatedWorkOrder = end($relatedWorkOrders);

                        $safeConductChecklist = $this->safeConductChecklist->getById($safeConductChecklistId);
                        $getContainer = $this->safeConductContainer->getSafeConductContainersById($safeConductChecklist['id_container']);
                        $handlingTypeTitle = empty($relatedWorkOrder) ? '' : ($relatedWorkOrder['handling_type'] . "\n");
                        $text_date = date('d F Y H:i:s', strtotime($safeConductChecklist['created_at'])) . "\n";
                        $text_security_in = $safeConduct['expedition_type'] == "INTERNAL"  && $safeConductChecklist['type'] == "CHECK IN" ? "INTERNAL OUT BY " : "INTERNAL IN BY ";
                        $text_security_out = $safeConduct['expedition_type'] == "EXTERNAL"  && $safeConductChecklist['type'] == "CHECK IN" ? "EXTERNAL IN BY " : "EXTERNAL OUT BY ";

                        $text_type = $safeConduct['expedition_type'] == "INTERNAL" ? $text_security_in.strtoupper(UserModel::authenticatedUserData('username')) . "\n" : $text_security_out.strtoupper(UserModel::authenticatedUserData('username')) . "\n";
                        $text_container_number = !empty($getContainer) ? $getContainer['no_container'] . "\n" : null;
                        $text_police_number = $safeConduct['no_police'];
                        $text_driver = $safeConduct['driver'] . "\n";
                        
                        $text_code = !empty($safeConduct['tep_code']) ? empty($safeConduct['no_police']) ? substr($tep['tep_code'], 0, -2) . "**" . "\n": null : implode(',', array_column($allSafeConducts, 'no_safe_conduct')) . "\n";

                        if (count($allSafeConducts) > 1 && !empty($safeConduct['id_safe_conduct_group'])) {
                            $text_customer_name = "";
                            $text_aju = strtoupper(implode(", ", array_unique(array_column($allSafeConducts, 'type')))) . " AJU ";
                            foreach ($allSafeConducts as $index => $singleSafeConduct) {
                                $text_aju .= (($index > 0 ? ", " : "") . substr($singleSafeConduct['no_reference'], -4));
                            }
                        } else if (count($multiAju) > 1) {
                            $text_customer_name = "";
                            $i=0;
                            $text_aju = strtoupper($safeConduct['type']) . " AJU ";
                            foreach ($multiAju as $aju) {
                                if ($i==0) {
                                    $text_aju .= substr($aju['no_reference'], -4);
                                } else {
                                    $text_aju .= ", ".substr($aju['no_reference'], -4);
                                }
                                $i++;                                
                            }
                        } else {
                            $text_customer_name = $safeConduct['customer_name'] . "\n";
                            $text_aju = strtoupper($safeConduct['type']) . " AJU " . substr($safeConduct['no_reference'], -4);    
                        }    
                        if ($relatedWorkOrder['handling_type'] == "EMPTY CONTAINER") {
                            $getContainerBySc = $this->safeConductContainer->getSafeConductContainersBySafeConduct($safeConductChecklist['id_safe_conduct']);
                            $text_container_number = !empty($getContainer) ? "\n".$relatedWorkOrder['handling_type']." ". $getContainer['no_container'] : (!empty($getContainerBySc)? "\n".$relatedWorkOrder['handling_type']." ".$getContainerBySc[0]['no_container'] :null);
                            $tempText = $text_date . $text_type . $text_police_number . " | " . $text_driver . $text_customer_name . $text_code . $text_aju . $text_container_number;
                        }else{
                            $tempText = $text_date . $text_type . $text_container_number . $text_police_number . " | " . $text_driver . $text_customer_name . $text_code . $handlingTypeTitle . $text_aju;
                        }

                        //$path = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'safe_conducts_checklist' . DIRECTORY_SEPARATOR . $fileName;
                        foreach ($files as $index => $file) {
                            $this->watermark($file, basename($file), $tempText);
                        }
                        if (!empty($attachmentSeal)) {
                            //$pathSeal = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'safe_conducts_checklist' . DIRECTORY_SEPARATOR . $attachmentSeal;
                            //$pathWaSeal = 'safe_conducts_checklist/'. $attachmentSeal;
                            $this->watermark($attachmentSeal, basename($attachmentSeal), $tempText);
                        }
                    }

                    // $people = $this->people->getById($safeConduct['id_customer']);
                    // $whatsapp_group = $people['whatsapp_group'];

                    $branch = get_active_branch();
                    $whatsapp_group_internal = $branch['whatsapp_group'];
                    $whatsapp_group = $branch['whatsapp_group_security'];
                    $this->db->trans_complete();

                    if ($this->db->trans_status()) {
                        foreach ($files as $index => $file) {
                            $this->send_message($file, $whatsapp_group, $whatsapp_group_internal);
                        }
                        if (!empty($attachmentSeal)) {
                            $this->send_message($attachmentSeal, $whatsapp_group, $whatsapp_group_internal);
                        }

                        // notify customer about empty container
                        // when expedition internal notify when check in
                        // when expedition external notify when check out because vendor check in via TEP
                        $internalCondition = $safeConduct['expedition_type'] == 'INTERNAL' && $type == 'CHECK IN';
                        $externalCondition = $safeConduct['expedition_type'] == 'EXTERNAL' && $type == 'CHECK OUT';
                        if (!empty($relatedWorkOrder)) {
                            if ($relatedWorkOrder['handling_type'] == 'EMPTY CONTAINER' && ($internalCondition || $externalCondition)) {
                                $relatedCustomer = $this->people->getById($relatedWorkOrder['id_customer']);
                                if (!empty($relatedCustomer['whatsapp_group'])) {
                                    $caption = "*EMPTY CONTAINER*\n";
                                    $caption .= "This container is in the process of returning to DEPO";
                                    $this->send_message($files[0], $relatedCustomer['whatsapp_group'], null, $caption);
                                }
                            }
                        } else {
                            log_message('error', "No job for safe conduct {$safeConduct['no_safe_conduct']} to be notified to customer");
                        }

                        flash('warning', "Safe conduct checklist <strong>{$safeConduct['no_safe_conduct']}</strong> successfully checked");
                    } else {
                        flash('danger', "Check out safe conduct checklist <strong>{$safeConduct['no_safe_conduct']}</strong> failed, try again or contact administrator");
                    }

                    if ($redirect == '') {
                        redirect('security/check?code=' . $safeConduct['no_safe_conduct']);
                    } else {
                        redirect($redirect, false);
                    }
                }

                if (!empty($id_tep)) {
                    if (empty($id_tep_container)) {
                        $id_container = null;
                    } else {
                        $id_container = $id_tep_container;
                    }

                    // get related job of transporter entry permit
                    // exclude moving in and out and take out only latest one job regarding the safe conduct data
                    $relatedWorkOrders = array_filter($this->workOrder->getWorkOrderBaseQuery()->where(['work_orders.id_transporter_entry_permit' => $id_tep])->get()->result_array(), function ($workOrder) {
                        return !in_array($workOrder['handling_type'], [get_setting('default_moving_in_handling'), get_setting('default_moving_out_handling')]);
                    });
                    $relatedWorkOrder = end($relatedWorkOrders);

                    $this->db->trans_start();

                    // upload attachment if exist
                    $attachmentSeal = '';
                    $description = '';
                    $uploadPassed = true;
                    $files = $this->input->post('attachment_name');
                    $photoNames = $this->input->post('security_check_photos');

                    if (!empty($files)) {
                        $uploadStatus = true;
                        foreach ($files as $index => $file) {
                            $uploadedPhoto = 'temp/' . $file;
                            $saveTo = 'safe_conducts_checklist/' . date('Y/m/') . $file;
                            $status = $this->uploader->setDriver('s3')->move($uploadedPhoto, $saveTo);
                            if (!$status) {
                                $uploadStatus = false;
                            }
                            $files[$index] = $saveTo;
                        }
                        if (!$uploadStatus) {
                            flash('danger', "Security Checklist {$tep['tep_code']} failed, upload to server failed");
                            redirect('security/check?code=' . $tep['tep_code']);
                        }
                    } else {
                        flash('danger', "Security Checklist {$tep['tep_code']} failed, You haven't uploaded a photo yet");
                        redirect('security/check?code=' . $tep['tep_code']);
                    }

                    $fileAttachments = $this->input->post('attachment_seal_name');
                    if (!empty($fileAttachments)) {
                        //$attachmentSeal = 'SEAL_' . time() . '_' . rand(100, 999);
                        //$uploadedPhoto = 'temp' . DIRECTORY_SEPARATOR . $files[0];
                        //$saveTo = 'safe_conducts_checklist' . DIRECTORY_SEPARATOR . $attachmentSeal . '.' .$extension;
                        
                        //$status= $this->uploader->move($uploadedPhoto, $saveTo);

                        $uploadedPhoto = 'temp/' . $fileAttachments[0];
                        $saveTo = 'safe_conducts_checklist/' . date('Y/m/') . $fileAttachments[0];
                        $status = $this->uploader->setDriver('s3')->move($uploadedPhoto, $saveTo);
                        if (!$status) {
                            $uploadPassed = false;
                            flash('danger', "Security Checklist {$safeConduct['no_safe_conduct']} failed, uplod server fail");
                            redirect('security/check?code=' . $safeConduct['no_safe_conduct']);
                        }else{
                            //$attachmentSeal = $attachmentSeal . '.' .$extension;
                            $attachmentSeal = $saveTo;
                            $description = $this->input->post('description');
                        }
                    }

                    if ($uploadPassed) {
                        // tep checklist
                        $this->TransporterEntryPermitChecklist->create([
                            'id_tep' => $id_tep,
                            'id_container' => $id_container,
                            'type' => $type,
                            //'attachment' => $fileName,
                            'attachment_seal' => $attachmentSeal,
                            'description' => $description,
                        ]);

                        //tep checklist detail
                        $tepChecklistId = $this->db->insert_id();
                        foreach ($id_checklists as $index => $id_checklist) {
                            if (!is_null($results)) {
                                $result = in_array($id_checklist, $results);
                            } else {
                                $result = 0;
                            }
                            $this->TransporterEntryPermitChecklistDetail->create([
                                'id_tep_checklist' => $tepChecklistId,
                                'id_checklist' => $id_checklist,
                                'result' => $result,
                                'description' => $reasons[$index],
                            ]);
                        }

                        // checklist photo
                        foreach ($files as $index => $file) {
                            $this->transporterEntryPermitChecklistPhoto->create([
                                'id_tep_checklist' => $tepChecklistId,
                                'photo' => $file,
                                'title' => $photoNames[$index] ?? 'Attachment'
                            ]);
                        }

                        if($type=='CHECK OUT'){
                            $tepChecklist = $this->TransporterEntryPermitChecklist->getById($tepChecklistId);
                            $getContainer = $this->transporterEntryPermitContainer->getById($tepChecklist['id_container']);
                            $handlingTypeTitle = empty($relatedWorkOrder) ? '' : ($relatedWorkOrder['handling_type'] . "\n");
                            $text_date = date('d F Y H:i:s', strtotime($tepChecklist['created_at'])) . "\n";
                            $text_type = $tepChecklist['type'] == "CHECK IN" ? "EXTERNAL IN BY ".strtoupper(UserModel::authenticatedUserData('username')) . "\n" : "EXTERNAL OUT BY ".strtoupper(UserModel::authenticatedUserData('username')) . "\n";
                            $text_container_number = !empty($getContainer) ? $getContainer['no_container'] . "\n" : null;
                            $text_customer_name = !empty($tep['customer_name']) ? $tep['customer_name'] . "\n" : $tep['customer_name_in'] . "\n";
                            $text_aju = !empty(substr($tep['no_reference'], -4)) ? strtoupper($tep['tep_category']) ." AJU " . substr($tep['no_reference'], -4) : strtoupper($tep['tep_category']) ;
                            $text_driver = !empty($tep['receiver_no_police']) && !empty($tep['receiver_name']) ? $tep['receiver_no_police'] . " | " . $tep['receiver_name'] . "\n" : null;
                            $text_code = ($text_driver==null)?substr($tep['tep_code'], 0, -2) . "**" . "\n":null;
                            $tempText = $text_date . $text_type . $text_container_number . $text_driver . $text_customer_name . $text_code . $handlingTypeTitle . $text_aju;
                            // $path = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'safe_conducts_checklist' . DIRECTORY_SEPARATOR . $fileName;
                            //$pathWa = 'safe_conducts_checklist/'. $fileName;
                            //$watermark = $this->watermark($pathWa, $fileName, $tempText);
                            foreach ($files as $index => $file) {
                                $this->watermark($file, basename($file), $tempText);
                            }
                            if (!empty($attachmentSeal)) {
                                //$pathSeal = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'safe_conducts_checklist' . DIRECTORY_SEPARATOR . $attachmentSeal;
                                //$pathWaSeal = 'safe_conducts_checklist/'. $attachmentSeal;
                                //$this->watermark($pathSeal, $attachmentSeal, $tempText);
                                $this->watermark($attachmentSeal, basename($attachmentSeal), $tempText);
                            }  
                        }
                    }

                    $this->db->trans_complete();
                    if($type=='CHECK OUT'){
                        $branch = get_active_branch();
                        $whatsapp_group_internal = $branch['whatsapp_group'];
                        $whatsapp_group = $branch['whatsapp_group_security'];
                    }
                    if ($this->db->trans_status()) {
                        if($type=='CHECK OUT'){
                            //$this->send_message($pathWa, $whatsapp_group, $whatsapp_group_internal);
                            foreach ($files as $index => $file) {
                                $this->send_message($file, $whatsapp_group, $whatsapp_group_internal);
                            }
                            if (!empty($attachmentSeal)) {
                                $this->send_message($attachmentSeal, $whatsapp_group, $whatsapp_group_internal);
                            }

                            // notify customer about empty container
                            if (!empty($relatedWorkOrder)) {
                                if ($relatedWorkOrder['handling_type'] == 'EMPTY CONTAINER') {
                                    $relatedCustomer = $this->people->getById($relatedWorkOrder['id_customer']);
                                    if (!empty($relatedCustomer['whatsapp_group'])) {
                                        $caption = "*EMPTY CONTAINER*\n";
                                        $caption .= "This container is in the process of returning to DEPO";
                                        $this->send_message($files[0], $relatedCustomer['whatsapp_group'], null, $caption);
                                    }
                                }
                            } else {
                                log_message('error', "No job for TEP {$tep['tep_code']} to be notified to customer");
                            }
                        }
                        flash('warning', "Transporter Entry Permit checklist <strong>{$tep['tep_code']}</strong> successfully checked");
                    } else {
                        flash('danger', "Transporter Entry Permit checklist <strong>{$tep['tep_code']}</strong> failed, try again or contact administrator");
                    }

                    if ($redirect == '') {
                        redirect('security/check?code=' . $tep['tep_code']);
                    } else {
                        redirect($redirect, false);
                    }
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed, please wait until page loaded');
        }

        if ($redirect == '') {
            redirect('security/check');
        } else {
            redirect($redirect, false);
        }
    }
}
