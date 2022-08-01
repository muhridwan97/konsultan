<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

defined('BASEPATH') OR exit('No direct script access allowed');

use Milon\Barcode\DNS2D;

/**
 * Class Transporter_entry_permit
 * @property TransporterEntryPermitModel $transporterEntryPermit
 * @property BookingModel $booking
 * @property BookingGoodsModel $bookingGoods
 * @property BookingContainerModel $bookingContainer
 * @property PeopleModel $people
 * @property Mailer $mailer
 * @property Uploader $uploader
 * @property TransporterEntryPermitCustomerModel $transporterEntryPermitCustomer
 * @property WorkOrderModel $workOrder
 * @property TransporterEntryPermitRequestModel $transporterEntryPermitRequest
 * @property TransporterEntryPermitSlotModel $transporterEntryPermitSlot
 * @property ReportModel $reportModel
 * @property ReportStockModel $reportStock
 * @property TransporterEntryPermitRequestTepModel $transporterEntryPermitRequestTep
 * @property TransporterEntryPermitRequestUploadModel $transporterEntryPermitRequestUpload
 * @property TransporterEntryPermitUploadModel $transporterEntryPermitUpload
 * @property TransporterEntryPermitHistoryModel $transporterEntryPermitHistory
 * @property TransporterEntryPermitRequestStatusHistoryModel $transporterEntryPermitRequestStatusHistory
 * @property TransporterEntryPermitRequestFileModel $transporterEntryPermitRequestFile
 * @property TransporterEntryPermitRequestExpressServiceFileModel $transporterEntryPermitRequestExpressServiceFile
 * @property TransporterEntryPermitRequestHoldItemModel $transporterEntryPermitHoldItem
 * @property TransporterEntryPermitChassisModel $transporterEntryPermitChassis
 * @property TransporterEntryPermitChecklistPhotoModel $transporterEntryPermitChecklistPhoto
 * @property UploadModel $upload
 * @property ScheduleHolidayModel $scheduleHoliday
 * @property EsealModel $eseal
 */
class Transporter_entry_permit extends MY_Controller
{
    /**
     * Transporter_entry_permit constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('TransporterEntryPermitModel', 'transporterEntryPermit');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingTypeModel', 'BookingType'); 
        $this->load->model('GuestModel', 'guest');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('EmployeeModel', 'employee');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('modules/Uploader', 'uploader');
        $this->load->model('modules/S3FileManager', 's3FileManager');

        $this->load->model('SafeConductModel', 'safeConduct');
        $this->load->model('SafeConductContainerModel', 'safeConductContainer');
        $this->load->model('SafeConductGoodsModel', 'safeConductGoods');
        $this->load->model('SafeConductChecklistModel', 'safeConductChecklist');
        $this->load->model('SafeConductChecklistDetailModel', 'safeConductChecklistDetail');

        $this->load->model('TransporterEntryPermitContainerModel', 'transporterEntryPermitContainer');
        $this->load->model('TransporterEntryPermitGoodsModel', 'transporterEntryPermitGoods');
        $this->load->model('TransporterEntryPermitChecklistModel', 'TransporterEntryPermitChecklist');
        $this->load->model('TransporterEntryPermitChecklistDetailModel', 'TransporterEntryPermitChecklistDetail');
        $this->load->model('TransporterEntryPermitBookingModel', 'transporterEntryPermitBooking');
        $this->load->model('TransporterEntryPermitChecklistPhotoModel', 'transporterEntryPermitChecklistPhoto');
        $this->load->model('TransporterEntryPermitCustomerModel', 'transporterEntryPermitCustomer');
        $this->load->model('TransporterEntryPermitRequestModel', 'transporterEntryPermitRequest');
        $this->load->model('TransporterEntryPermitSlotModel', 'transporterEntryPermitSlot');
        $this->load->model('TransporterEntryPermitRequestTepModel', 'transporterEntryPermitRequestTep');
        $this->load->model('TransporterEntryPermitRequestUploadModel', 'transporterEntryPermitRequestUpload');
        $this->load->model('TransporterEntryPermitUploadModel', 'transporterEntryPermitUpload');
        $this->load->model('TransporterEntryPermitHistoryModel', 'transporterEntryPermitHistory');
        $this->load->model('TransporterEntryPermitRequestStatusHistoryModel', 'transporterEntryPermitRequestStatusHistory');
        $this->load->model('TransporterEntryPermitRequestFileModel', 'transporterEntryPermitRequestFile');
        $this->load->model('TransporterEntryPermitRequestExpressServiceFileModel', 'transporterEntryPermitRequestExpressServiceFile');
        $this->load->model('TransporterEntryPermitRequestHoldItemModel', 'transporterEntryPermitHoldItem');
        $this->load->model('TransporterEntryPermitChassisModel', 'transporterEntryPermitChassis');

        $this->load->model('NotificationModel', 'notification');
        $this->load->model('ReportModel', 'reportModel');
        $this->load->model('ReportStockModel', 'reportStock');
        
        $this->load->model('UploadModel', 'upload');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('ScheduleHolidayModel', 'scheduleHoliday');

        //model khusus VMS
        $this->load->model('AreaVmsModel', 'areaVms'); 
        $this->load->model('AdditionalGuestVmsModel', 'additionalGuestVms');
        $this->load->model('BranchVmsModel', 'branchVms');
        $this->load->model('StatusHistoryVmsModel', 'statusHistoryVms'); 
        $this->load->model('ReportModel', 'reportModel');
        $this->load->model('ReportStockModel', 'reportStock');
        
        $this->load->model('UploadModel', 'upload');
        $this->load->model('EsealModel', 'eseal');
        $this->load->library('zip');

        $this->setFilterMethods([
            'ajax_get_data' => 'GET',
            'ajax_get_tep' => 'GET',
            'ajax_get_outstanding_tep' => 'GET',
            'check_in' => 'POST|PUT|PATCH',
            'check_out' => 'POST|PUT|PATCH',
            'view_checklist_in' => 'GET',
            'view_checklist_in_goods' => 'GET',
            'view_checklist_out' => 'GET',
            'view_checklist_out_goods' => 'GET',
            'ajax_get_tep_by_id' => 'GET',
            'get_tep_reference_customer' => 'POST',
            'get_tep_reference_booking' => 'POST',
            'check_out_now' => 'POST',
            'print'=>'GET',
            'request'=>'GET',
            'ajax_get_available_sppb'=>'GET',
            'save_request'=>'POST',
            'queue'=>'GET',
            'set_tep'=>'POST',
            'update_set_tep'=>'POST',
            'add_slot'=>'POST',
            'create_outbound'=>'GET',
            'ajax_get_goods_container'=>'GET',
            'queue_tep'=>'GET',
            'set_skip'=>'POST',
            'edit_tep' => 'GET',
            'update_tep' => 'POST',
            'ajax_get_tep_queue' => 'GET',
            'ajax_get_request_by_id' => 'GET',
            'ajax_set_merge_request' => 'POST',
            'ajax_get_tep_req_files' => 'GET',
            'upload_s3' => 'POST',
            'download_file' => 'POST',
            'cancel_tep' => 'POST',
            'ajax_get_tep_req_express_files' => 'GET',
            'download_express_file' => 'POST',
            'request_inbound'=>'GET',
            'ajax_get_stock_by_customer' => 'GET',
            'create_tep_request'=>'GET',
            'save_tep_request' => 'POST',
            'ajax_get_goods_by_id_request' => 'GET',
            'ajax_slot_tep_by_date' => 'GET',
            'get_tep_reference_by_request' => 'POST',
            'save_request_inbound'=>'POST',
        ]);
    }

    /**
     * Show document data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_VIEW);
        // print_debug(UserModel::authenticatedUserData('id')=='15');
        $this->render('transporter_entry_permit/index');
    }

    /**
     * Get ajax paging data TEP.
     */
    public function ajax_get_data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_VIEW);

        $filters = [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ];

        $data = $this->transporterEntryPermit->getAll($filters);
        
        $this->render_json($data);
    }

    /**
     * Get outstanding tep.
     */
    public function ajax_get_outstanding_tep()
    {
        $filters = $_GET;

        // unattached: checked in and not checked out yet (inbound)
        if (get_url_param('status') == 'UNATTACHED') {
            $filters['should_checked_in'] = true;
            $filters['active_days'] = 7;
            $filters['outstanding_checked_out'] = false; // or without this filter return same result
        }

        // outbound (job related), for empty container allowed to take checked out tep
        if (get_url_param('id_work_order')) {
            $workOrder = $this->workOrder->getWorkOrderById(get_url_param('id_work_order'));
            if ($workOrder['handling_type'] == 'EMPTY CONTAINER') {
                $filters['outstanding_checked_out'] = false;
            } else {
                $filters['outstanding_checked_out'] = true;
            }

            // for admin allowed get tep for related job (even already checked out)
            if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATED_EDIT) && !empty($workOrder['id_transporter_entry_permit'])) {
                $filters['id_except_tep'] = $workOrder['id_transporter_entry_permit'];
            }
        }

        $outstandingTep = $this->transporterEntryPermit->getOutstandingTep($filters);

        $this->render_json($outstandingTep);
    }

    /**
     * Get ajax data tep.
     */
    public function ajax_get_tep()
    {
        $filters = [];

        // if (get_url_param('category')) {
        //     $filters['transporter_entry_permits.tep_category'] = get_url_param('category');
        // }

        if (get_url_param('id_customer')) {
            $filters['(transporter_entry_permit_customer.id_customer='.get_url_param('id_customer').')'] = null;
        }

        if (get_url_param('id_booking')) {
            $filters['(transporter_entry_permits.id_booking='.get_url_param('id_booking').' OR transporter_entry_permit_bookings.id_booking='.get_url_param('id_booking').' OR booking_uploads.id='.get_url_param('id_booking').')'] = null;
        }
        if (get_url_param('status') == 'UNATTACHED') {
            // by default all must checked in first
            $filters['transporter_entry_permits.checked_in_at IS NOT NULL'] = null;

            // outbound (job) must before checkout except Empty Container
            $allowCheckout = false;
            if (get_url_param('id_work_order')) {
                $workOrder = $this->workOrder->getWorkOrderById(get_url_param('id_work_order'));
                if ($workOrder['handling_type'] == 'EMPTY CONTAINER') {
                    $allowCheckout = true;
                }
            }

            if (get_url_param('id_customer') && !$allowCheckout) {
                $filters['transporter_entry_permits.checked_out_at IS NULL'] = null;
            }
        }

        $data = $this->transporterEntryPermit->getBy($filters);
        if (get_url_param('id_customer')) {
            unset($filters['transporter_entry_permits.id_customer']);
            $filters['people_customers.id'] = get_url_param('id_customer');
        }
        if (get_url_param('category') == 'OUTBOUND') {
            $data = array_merge($data,$this->transporterEntryPermit->getBy($filters));
        }
        $safe_conducts = $this->safeConduct->getSafeConductsByBooking(get_url_param('id_booking'));

        if (get_url_param('status') == 'UNATTACHED') {
            if (get_url_param('id_booking')) {
                $safe_conducts = $this->safeConduct->getSafeConductsByBooking(get_url_param('id_booking'));

                foreach($data as $index => $dt){
                    $check_safe_conduct = array_filter($safe_conducts, function($sf) use($dt){
                        return $sf['id_booking'] == get_url_param('id_booking') && $sf['id_transporter_entry_permit'] == $dt['id'];
                    });
                    if(!empty($check_safe_conduct)){
                        unset($data[$index]);                
                    }
                }
            }
        }
        $data = array_values($data);
        $this->render_json($data);
    }

    /**
     * Show detail of tep checklist in container
     * @param $id
     */
    public function view_checklist_in($id)
    {
        $tepChecklists = $this->TransporterEntryPermitChecklist->getTepChecklistInById($id);
        $tepChecklistDetails = $this->TransporterEntryPermitChecklistDetail->getTepChecklistDetailByChecklistId($tepChecklists['id']);

        $data = [
            'title' => "Checklist In",
            'subtitle' => "View Transporter Entry Permit Checklist In",
            'page' => "transporter_entry_permit/view_checklist_in",
            'tepChecklists' => $tepChecklists,
            'tepChecklistDetails' => $tepChecklistDetails,
        ];
        $this->load->view('template/layout', $data);
    }


    /**
     * Show detail of tep checklist_in goods
     * @param $id
     */
    public function view_checklist_in_goods($id)
    {
        $tepChecklists = $this->TransporterEntryPermitChecklist->getTepChecklistInGoodsById($id);
        $tepChecklistDetails = $this->TransporterEntryPermitChecklistDetail->getTepChecklistDetailByChecklistId($tepChecklists['id']);

        $data = [
            'title' => "Checklist In",
            'subtitle' => "View Transporter Entry Permit Checklist In",
            'page' => "transporter_entry_permit/view_checklist_in",
            'tepChecklists' => $tepChecklists,
            'tepChecklistDetails' => $tepChecklistDetails,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show detail of tep checklist out container
     * @param $id
     */
    public function view_checklist_out($id)
    {
        $tepChecklists = $this->TransporterEntryPermitChecklist->getTepChecklistOutById($id);
        $tepChecklistDetails = $this->TransporterEntryPermitChecklistDetail->getTepChecklistDetailByChecklistId($tepChecklists['id']);

        $data = [
            'title' => "Checklist Out",
            'subtitle' => "View Transporter Entry Permit Checklist Out",
            'page' => "transporter_entry_permit/view_checklist_out",
            'tepChecklists' => $tepChecklists,
            'tepChecklistDetails' => $tepChecklistDetails,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show detail of tep checklist out goods
     * @param $id
     */
    public function view_checklist_out_goods($id)
    {
        $tepChecklists = $this->TransporterEntryPermitChecklist->getTepChecklistOutGoodsById($id);
        $tepChecklistDetails = $this->TransporterEntryPermitChecklistDetail->getTepChecklistDetailByChecklistId($tepChecklists['id']);

        $data = [
            'title' => "Checklist Out",
            'subtitle' => "View Transporter Entry Permit Checklist Out",
            'page' => "transporter_entry_permit/view_checklist_out",
            'tepChecklists' => $tepChecklists,
            'tepChecklistDetails' => $tepChecklistDetails,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show detail job documents.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_VIEW);

        $tepChecklistsIn = $this->transporterEntryPermitChecklistPhoto->getBy([
            'transporter_entry_permit_checklists.id_tep' => $id,
            'transporter_entry_permit_checklists.type' => 'CHECK IN'
        ]);
        $tepChecklistsOut = $this->transporterEntryPermitChecklistPhoto->getBy([
            'transporter_entry_permit_checklists.id_tep' => $id,
            'transporter_entry_permit_checklists.type' => 'CHECK OUT'
        ]);
        $attachmentSealIn = $this->TransporterEntryPermitChecklist->getBy([
            'transporter_entry_permit_checklists.id_tep' => $id,
            'transporter_entry_permit_checklists.type' => 'CHECK IN',
            'transporter_entry_permit_checklists.attachment_seal !=' => ''
        ]);
        $attachmentSealOut = $this->TransporterEntryPermitChecklist->getBy([
            'transporter_entry_permit_checklists.id_tep' => $id,
            'transporter_entry_permit_checklists.type' => 'CHECK OUT',
            'transporter_entry_permit_checklists.attachment_seal !=' => ''
        ]);
        
        $tep = $this->transporterEntryPermit->getById($id);
        $tepRequests = $this->transporterEntryPermitRequest->getRequestByTep($tep['id']);
        $multiCustomer = $this->transporterEntryPermitCustomer->getCustomerByIdTep($id);
        $multiBookings = $this->transporterEntryPermitBooking->getBookingByIdTep($id);
        $tepHistories = $this->transporterEntryPermitHistory->getBy(['id_tep'=>$id]);
        $linkedTep = $this->transporterEntryPermit->getAll([
            'branch' => 2, // hard-coded medan 1
            'tep' => if_empty($tep['id_linked_tep'], -1)
        ]);
        if (!empty($linkedTep)) {
            $linkedTep = $linkedTep[0];
        }

        $requestId = array_column($tepRequests, 'id');
        if(!empty($requestId)){
            $listGoods = $this->transporterEntryPermitRequestUpload->getByRequestId($requestId);
        }else{
            $listGoods = [];
        }
        
        $this->render('transporter_entry_permit/view', compact('tep','tepRequests', 'multiCustomer','tepChecklistsIn','tepChecklistsOut', 'attachmentSealIn', 'attachmentSealOut', 'tepHistories', 'linkedTep', 'listGoods'));
    }

    /**
     * Show form create transporter entry permit.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_CREATE);

        $bookingsId = $this->input->post('bookings');
        $bookings = [];
        if (empty($bookingsId)) {
            $bookings = $this->booking->getBookingById($bookingsId);
        }

        $this->render('transporter_entry_permit/create', compact('bookings'));
    }

    /**
     * Save transporter entry permit
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_CREATE);

        if ($this->validate()) {
            $tepCategory = $this->input->post('tep_category');
            $bookingId = if_empty($this->input->post('booking'), []);
            $customerId = $this->input->post('customer');
            $totalCode = $this->input->post('total_code');
            $emailType = $this->input->post('email_type');
            $inputEmail = $this->input->post('input_email');
            $description = $this->input->post('description');
            $tep_before = $this->input->post('tep_before');
            $tep_reference = $this->input->post('tep_reference');
            $uploadId = $this->input->post('aju');

            $this->db->trans_start();

            // add entry permit
            $codes = [];
            if ($tepCategory == "OUTBOUND") {
                $expiredDate = date('Y-m-d 23:59:59');
            } else {
                $expiredDate = date('Y-m-d H:i:s', strtotime('+1 month', strtotime('now')));
            }
            for ($i = 0; $i < $totalCode; $i++) {
                $code = $this->transporterEntryPermit->generateCode();
                $this->transporterEntryPermit->create([
                    'tep_code' => $code,
                    'tep_category' => $tepCategory,
                    'expired_at' => $expiredDate,
                    'description' => $description,
                    'id_branch' => get_active_branch('id'),
                    'id_tep_reference' => $tep_before == "yes" ? $tep_reference : null,
                ]);
                $codes[] = ['code' => $code, 'expired_at' => $expiredDate];
                $tepId = $this->db->insert_id();
                if(!empty($customerId)){
                    if($tepCategory == "EMPTY CONTAINER"||$tepCategory == "OUTBOUND"){
                        foreach($customerId AS $customer_id){
                            $this->transporterEntryPermitCustomer->create([
                                'id_tep'=> $tepId,
                                'id_customer' => $customer_id,
                            ]);
                        }
                    }
                }
                if(!empty($bookingId)){
                    if($tepCategory == "INBOUND"){
                        foreach($bookingId AS $booking_id){
                            $this->transporterEntryPermitBooking->create([
                                'id_tep'=> $tepId,
                                'id_booking' => $booking_id,
                                ]);
                        }
                    }
                }
                if(count($customerId) > 1){
                    if($tepCategory == "OUTBOUND"){
                        foreach($customerId AS $customerId){
                            $this->transporterEntryPermitCustomer->create([
                                'id_tep'=> $tepId,
                                'id_customer' => $customerId,
                            ]);
                        }
                    }
                }
                foreach ($uploadId as $id_upload) {
                    $this->transporterEntryPermitUpload->create([
                        'id_tep' => $tepId,
                        'id_upload' => $id_upload
                    ]);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                // send email to customer
                $rows = '';
                $barcode = new DNS2D();
                $barcode->setStorPath(APPPATH . "cache/");
                $baseFolder = 'qr/' . date('Y/m');
                $this->uploader->makeFolder($baseFolder);
                $codeText = "";
                foreach ($codes as $index => &$code) {
                    $no = $index + 1;
                    $qrCode = $barcode->getBarcodePNG($code['code'], "QRCODE", 8, 8);
                    $qrFileName = $baseFolder . '/' . Str::slug($code['code']) . '-' . uniqid() . '.jpg';
                    base64_to_jpeg($qrCode, Uploader::UPLOAD_PATH . $qrFileName);

                    $rows .= "
                        <tr style='border-bottom: 1px solid #aaaaaa'>
                            <td align='center'>{$no}</td>
                            <th>{$code['code']}</th>
                            <td>{$code['expired_at']}</td>
                        </tr>
                        <tr style='border-bottom: 1px solid #aaaaaa'>
                            <td align='center' colspan='2'>QR Code</td>
                            <td><img src='" . base_url('uploads/' . $qrFileName) . "' alt='{$code['code']}'></td>
                        </tr>
                    ";
                    $codeText .= $code['code'] . ", ";
                    
                    //add text in barcode
                    list($orig_w, $orig_h) = getimagesize(Uploader::UPLOAD_PATH . $qrFileName);
                    $orig_img = imagecreatefromstring(file_get_contents(Uploader::UPLOAD_PATH . $qrFileName));

                    $output_w = 168;
                    $output_h = 200;
                    $scale = $output_w/$orig_w;

                    // calc new image dimensions
                    $new_h =  $orig_h * $scale;

                    // create new image and fill with background colour
                    $new_img = imagecreatetruecolor($output_w, $output_h);
                    $bgcolor = imagecolorallocate($new_img,255, 255, 255); // white
                    $textColor = imagecolorallocate($new_img, 0, 0, 0); // white
                    imagefill($new_img, 0, 0, $bgcolor); // fill background colour
                    // copy and resize original image into center of new image
                    imagecopyresampled($new_img, $orig_img, 0, 0, 0, 0, $output_w, $new_h, $orig_w, $orig_h);
                    $new_filename = Uploader::UPLOAD_PATH . $qrFileName;
                    imagettftext($new_img, 18, 0, 22 , 195 , $textColor, FCPATH . 'assets/plugins/font-googleapis/fonts/SourceSansPro-Bold.ttf', $code['code']);
                    //save it
                    imagejpeg($new_img, $new_filename, 80);
                    $code['qr_code'] = base_url('uploads/' . $qrFileName);
                }

                $nameCreated = UserModel::authenticatedUserData('name');
                $customerText = "";
                $whatsapp_group_internal = get_active_branch('whatsapp_group');
                if($tepCategory == "EMPTY CONTAINER"||$tepCategory == "OUTBOUND"){
                    foreach ($customerId as $idCustomer) {
                        $customer = $this->people->getById($idCustomer);
                        if($emailType == 'INPUT'){
                            $emailTo = $inputEmail;    
                        }else{
                            $emailTo = $customer['email'];
                        }
                        $emailTitle = "Transporter entry permit {$customer['name']} for {$customer['name']} with " . count($codes) . " total codes";
                        $emailTemplate = 'emails/basic';
                        $emailData = [
                            'name' => $customer['name'],
                            'email' => $emailTo,
                            'content' => "
                                Transporter entry permit is generated with " . count($codes) . " total codes and will be expired at <b>{$expiredDate}</b>,
                                please deliver this information (the code) to your transporter vendor or driver. <br> 
                                (THE CODES ARE NEEDED WHEN VISIT AND PICK UP FOR INBOUND / OUTBOUND ACTIVITY) .
                                <br><br>
                                <table style='width:100%; font-size: 14px; text-align: left; border-collapse: collapse; border:1px solid #aaaaaa' cellpadding='10'>
                                    <tr style='border-bottom: 1px solid #aaaaaa'>
                                        <th align='center'>No</th>
                                        <th>Code</th>
                                        <th>Expired At</th>
                                    </tr>
                                    {$rows}
                                </table>
                            "
                        ];
                        $customerText .= $customer['name'] . ", ";
                        if(!empty($emailTo)){
                            $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
                        }
                    }
                    if($tepCategory == "OUTBOUND"){
                        $data_whatsapp = "[TEP CREATED] Transporter entry permit OUTBOUND is created by {$nameCreated} for customer {$customerText} code TEP {$codeText}";
                        $this->send_message_text($data_whatsapp,$whatsapp_group_internal);
                    }
                }else{
                    $id_customers = [];
                    foreach($bookingId AS $idBooking){
                        $booking = $this->booking->getBookingById($idBooking);
                        $id_customers[] = $booking['id_customer'];
                    }
                    $idCustomers = array_unique($id_customers);
                    foreach($idCustomers AS $idCustomer){
                        $customer = $this->people->getById($idCustomer);
                        if($emailType == 'INPUT'){
                            $emailTo = $inputEmail;    
                        }else{
                            $emailTo = $customer['email'];
                        }
                        $emailTitle = "Transporter entry permit {$customer['name']} for {$customer['name']} with " . count($codes) . " total codes";
                        $emailTemplate = 'emails/basic';
                        $emailData = [
                            'name' => $customer['name'],
                            'email' => $emailTo,
                            'content' => "
                                Transporter entry permit is generated with " . count($codes) . " total codes and will be expired at <b>{$expiredDate}</b>,
                                please deliver this information (the code) to your transporter vendor or driver. <br> 
                                (THE CODES ARE NEEDED WHEN VISIT AND PICK UP FOR INBOUND / OUTBOUND ACTIVITY) .
                                <br><br>
                                <table style='width:100%; font-size: 14px; text-align: left; border-collapse: collapse; border:1px solid #aaaaaa' cellpadding='10'>
                                    <tr style='border-bottom: 1px solid #aaaaaa'>
                                        <th align='center'>No</th>
                                        <th>Code</th>
                                        <th>Expired At</th>
                                    </tr>
                                    {$rows}
                                </table>
                            "
                        ];
                        if(!empty($emailTo)){
                            $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
                        }
                    }
                }

                // send qr code image
                foreach ($codes as $index => $codenya) {
                    $this->send_message_text("[TEP CREATED] Transporter entry permit {$tepCategory} is created by {$nameCreated} for customer {$customerText} code TEP {$codenya['code']}", $whatsapp_group_internal, $codenya['qr_code']);
                }

                flash('success', "Entry permit for {$customerText} successfully created", 'transporter-entry-permit');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->create();
    }

    /**
     * Save transporter entry permit
     */
    public function save_tep_request()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_CREATE);

        if ($this->validate()) {
            $requestId = if_empty($this->input->post('tep_request'), []);
            $totalCode = $this->input->post('total_code');
            $emailType = $this->input->post('email_type');
            $inputEmail = $this->input->post('input_email');
            $description = $this->input->post('description');
            $tep_before = $this->input->post('tep_before');
            $tep_reference = $this->input->post('tep_reference');
            $tep_date = $this->input->post('tep_date');
            $tep_time = $this->input->post('tep_time');
            $goods = $this->input->post('goods');

            $this->db->trans_start();
            $requests = $this->transporterEntryPermitRequest->getReqTCI([
                'request' => $requestId
            ]);

            // add entry permit
            $codes = [];
            $expiredDate = date('Y-m-d 23:59:59', strtotime($tep_date));
            $tepCategory = "OUTBOUND";
            for ($i = 0; $i < $totalCode; $i++) {
                $code = $this->transporterEntryPermit->generateCode();
                $this->transporterEntryPermit->create([
                    'tep_code' => $code,
                    'tep_category' => $tepCategory,
                    'expired_at' => $expiredDate,
                    'description' => $description,
                    'id_branch' => get_active_branch('id'),
                    'id_tep_reference' => $tep_before == "yes" ? $tep_reference : null,
                ]);
                $codes[] = ['code' => $code, 'expired_at' => $expiredDate];
                $tepId = $this->db->insert_id();
                if(!empty($requests)){
                    foreach($requests AS $request){
                        $this->transporterEntryPermitCustomer->create([
                            'id_tep'=> $tepId,
                            'id_customer' => $request['id_customer'],
                            ]);
                        $this->transporterEntryPermitRequest->update([
                            'queue_time'=>$tep_time,
                            'tep_date'=> format_date($tep_date),
                            'slot'=> empty($request['slot_created']) ? $totalCode: $request['slot_created']+$totalCode ,
                            'status'=>'SET',
                            'set_by'=>UserModel::authenticatedUserData('id')
                        ],$request['id']); 
                        $this->transporterEntryPermitRequestTep->create([
                            'id_request' => $request['id'],
                            'id_tep' => $tepId
                        ]);
                        $this->transporterEntryPermitRequestStatusHistory->create([
                            'id_request' => $request['id'],
                            'date' => $tep_date,
                            'status' => 'SET',
                            'description' => 'SET REQUEST',
                        ]);
                    }
                }

                foreach ($goods as $item) {
                    $this->transporterEntryPermitUpload->create([
                        'id_tep' => $tepId,
                        'id_upload' => $item['id_upload'],
                        'id_goods' => $item['id_goods'],
                        'quantity' => $item['quantity'],
                        'work_order_quantity' => $item['work_order_quantity'],
                        'id_unit' => $item['id_unit'],
                        'goods_name' => $item['goods_name'],
                        'no_invoice' => $item['no_invoice'],
                        'no_bl' => $item['no_bl'],
                        'no_goods' => $item['no_goods'],
                        'unit' => $item['unit'],
                        'whey_number' => $item['whey_number'],
                        'ex_no_container' => $item['ex_no_container'],
                        'no_reference' => $item['no_reference'],
                        'hold_status' => $item['hold_status'],
                        'unload_location' => if_empty($item['unload_location'], null),
                        'priority' => if_empty($item['priority'], null),
                        'priority_description' => if_empty($item['priority_description'], null),
                    ]);
                }
            }

            $this->db->trans_complete();
 
            if ($this->db->trans_status()) {
                // send email to customer
                $rows = '';
                $barcode = new DNS2D();
                $barcode->setStorPath(APPPATH . "cache/");
                $baseFolder = 'qr/' . date('Y/m');
                $this->uploader->makeFolder($baseFolder);
                $codeText = "";
                $reqCustomer = [];
                $allCustomer = [];
                $ajuCustomer = [];
                $noReferences = '[NO UPLOAD REFERENCE]';
                $reqAll = '';
                $your_code = "";
                foreach ($codes as $index => &$code) {
                    $no = $index + 1;
                    $qrCode = $barcode->getBarcodePNG($code['code'], "QRCODE", 8, 8);
                    $qrFileName = $baseFolder . '/' . Str::slug($code['code']) . '-' . uniqid() . '.jpg';
                    base64_to_jpeg($qrCode, Uploader::UPLOAD_PATH . $qrFileName);

                    $your_code .= $code['code'].", ";
                    $rows .= "
                        <tr style='border-bottom: 1px solid #aaaaaa'>
                            <td align='center'>{$no}</td>
                            <th>{$code['code']}</th>
                            <td>{$code['expired_at']}</td>
                        </tr>
                        <tr style='border-bottom: 1px solid #aaaaaa'>
                            <td align='center' colspan='2'>QR Code</td>
                            <td><img src='" . base_url('uploads/' . $qrFileName) . "' alt='{$code['code']}'></td>
                        </tr>
                    ";
                    $codeText .= $code['code'] . ", ";

                    //add text in barcode
                    list($orig_w, $orig_h) = getimagesize(Uploader::UPLOAD_PATH . $qrFileName);
                    $orig_img = imagecreatefromstring(file_get_contents(Uploader::UPLOAD_PATH . $qrFileName));

                    $output_w = 168;
                    $output_h = 200;
                    $scale = $output_w/$orig_w;

                    // calc new image dimensions
                    $new_h =  $orig_h * $scale;

                    // create new image and fill with background colour
                    $new_img = imagecreatetruecolor($output_w, $output_h);
                    $bgcolor = imagecolorallocate($new_img,255, 255, 255); // white
                    $textColor = imagecolorallocate($new_img, 0, 0, 0); // white
                    imagefill($new_img, 0, 0, $bgcolor); // fill background colour
                    // copy and resize original image into center of new image
                    imagecopyresampled($new_img, $orig_img, 0, 0, 0, 0, $output_w, $new_h, $orig_w, $orig_h);
                    $new_filename = Uploader::UPLOAD_PATH . $qrFileName;
                    imagettftext($new_img, 18, 0, 22 , 195 , $textColor, FCPATH . 'assets/plugins/font-googleapis/fonts/SourceSansPro-Bold.ttf', $code['code']);
                    //save it
                    imagejpeg($new_img, $new_filename, 80);
                    $code['qr_code'] = base_url('uploads/' . $qrFileName);
                }

                $nameCreated = UserModel::authenticatedUserData('name');
                $customerText = "";
                $whatsapp_group_internal = get_active_branch('whatsapp_group');
                foreach ($requests as $request) {
                    $customer = $this->people->getById($request['id_customer']);

                    //set all customer
                    if (isset($allCustomer[$request['id_customer']])) {
                        $allCustomer[$request['id_customer']] = $customer;
                    }

                    //set all request
                    if(empty($reqAll)){
                        $reqAll = $request['no_request'];
                    }else{
                        $reqAll .= ', '.$request['no_request'];
                    }

                    //set all request by customer
                    if(isset($reqCustomer[$request['id_customer']])){
                        $reqCustomer[$request['id_customer']] .= ', '.$request['no_request'];
                    }else{
                        $reqCustomer[$request['id_customer']] = $request['no_request'];
                    }

                    $tepRequestMergeUploads = $this->transporterEntryPermitRequestUpload->getBy(['id_request' => $request['id_request']]);
                    if(isset($ajuCustomer[$request['id_customer']])){
                        $ajuCustomer[$request['id_customer']] .= ', '.implode(', ', array_map(function ($noRef) {
                            return substr($noRef, -6, 6);
                        }, array_unique(array_column($tepRequestMergeUploads, 'no_reference_upload'))));
                    }else{
                        $ajuCustomer[$request['id_customer']] = implode(', ', array_map(function ($noRef) {
                            return substr($noRef, -6, 6);
                        }, array_unique(array_column($tepRequestMergeUploads, 'no_reference_upload'))));
                        if($noReferences == '[NO UPLOAD REFERENCE]'){
                            $noReferences = $ajuCustomer[$request['id_customer']];
                        }else{
                            $noReferences .= ', '.$ajuCustomer[$request['id_customer']];
                        }
                    }
                    
                    if($emailType == 'INPUT'){
                        $emailTo = $inputEmail;    
                    }else{
                        $emailTo = $customer['email'];
                    }
                    $emailTitle = "Transporter entry permit {$customer['name']} for {$customer['name']} with " . count($codes) . " total codes";
                    $emailTemplate = 'emails/basic';
                    $emailData = [
                        'name' => $customer['name'],
                        'email' => $emailTo,
                        'content' => "
                            Transporter entry permit is generated with " . count($codes) . " total codes and will be expired at <b>{$expiredDate}</b>,
                            please deliver this information (the code) to your transporter vendor or driver. <br> 
                            (THE CODES ARE NEEDED WHEN VISIT AND PICK UP FOR INBOUND / OUTBOUND ACTIVITY) .
                            <br><br>
                            <table style='width:100%; font-size: 14px; text-align: left; border-collapse: collapse; border:1px solid #aaaaaa' cellpadding='10'>
                                <tr style='border-bottom: 1px solid #aaaaaa'>
                                    <th align='center'>No</th>
                                    <th>Code</th>
                                    <th>Expired At</th>
                                </tr>
                                {$rows}
                            </table>
                        "
                    ];
                    $customerText .= $customer['name'] . ", ";
                    if(!empty($emailTo)){
                        $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
                    }
                }
                
                $data_whatsapp = "[TEP CREATED] Transporter entry permit OUTBOUND is created by {$nameCreated} for customer {$customerText} code TEP {$codeText} from request ".$reqAll;
                $this->send_message_text($data_whatsapp,$whatsapp_group_internal);
 
                 // send qr code image
                 foreach ($codes as $index => $codenya) {
                     $this->send_message_text("[TEP CREATED] Transporter entry permit {$tepCategory} is created by {$nameCreated} for customer {$customerText} code TEP {$codenya['code']} from request ".$reqAll."", $whatsapp_group_internal, $codenya['qr_code']);
                 }

                 //for notif customer
                foreach ($allCustomer as $key => $customer) {
                     
                    $data_whatsapp = "[TEP] Hello, {$customer['name']}, Your code {$your_code} with request ".$reqCustomer[$customer['id']]." is used for reference ".$ajuCustomer[$customer['id']].". The Transporter Entry Permit is queue at {$tep_time} ( ".date('d F Y', strtotime($expiredDate))." ), please deliver this information (the code) to your transporter vendor or driver.";
                    $whatsapp_group = $customer['whatsapp_group'];
                    
                    $this->send_message_text($data_whatsapp,$whatsapp_group);
                    // send qr code image
                    foreach ($codes as $index => $codenya) {
                        $this->send_message_text("[TEP] Hello, {$customer['name']}, Your code {$codenya['code']} with request ".$reqCustomer[$customer['id']]."  is used for reference {$noReferences}. the Transporter Entry Permit is queue at {$tep_time} ( ".date('d F Y', strtotime($expiredDate))." )", $whatsapp_group, $codenya['qr_code']);
                    }
                }
 
                 flash('success', "Entry permit for {$customerText} successfully created", 'transporter-entry-permit');
             } else {
                 flash('danger', 'Something is getting wrong, try again or contact administrator');
             }
         }
         $this->create();
     }


    /**
     * Update tep.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_EDIT);

        $tep = $this->transporterEntryPermit->getById($id);

        if ($tep['category'] == 'INBOUND' || $tep['tep_category'] == 'INBOUND') {
            $containers = $this->input->post('containers');
            $goods = $this->input->post('goods');

            $this->db->trans_start();

            $this->transporterEntryPermit->update([
                'id_eseal' => $this->input->post('eseal'),
            ], $id);

            // update TEP Container Or Goods
            if (!empty($containers)) {
                foreach ($containers as $container) {

                    $id_position = !is_null($container['id_position']) && $container['id_position'] != '' && $container['id_position'] != 'null' ? $container['id_position'] : null;
                    $this->transporterEntryPermitContainer->createTepContainer([
                        'id_tep' => $tep['id'],
                        'id_booking_container' => $container['id_reference'],
                        'id_container' => $container['id_container'],
                        'id_position' => $id_position,
                        'seal' => if_empty($container['seal'], null),
                        'is_empty' => $container['is_empty'],
                        'is_hold' => $container['is_hold'],
                        'status' => $container['status'],
                        'status_danger' => $container['status_danger'],
                        'description' => $container['description'],
                        'quantity' => 1
                    ]);
                }

            }

            if (!empty($goods)) {
                foreach ($goods as $item) {
                    $id_position = !is_null($container['id_position']) && $container['id_position'] != '' && $container['id_position'] != 'null' ? $container['id_position'] : null;

                    $this->transporterEntryPermitGoods->createTepGoods([
                        'id_tep' => $id,
                        'id_delivery_order_goods' => null,
                        'id_booking_goods' => $item['id_reference'],
                        'id_goods' => $item['id_goods'],
                        'id_unit' => $item['id_unit'],
                        'id_position' => $id_position,
                        'no_pallet' => if_empty($item['no_pallet'], null),
                        'no_delivery_order' => null,
                        'quantity' => $item['quantity'],
                        'length' => $item['length'],
                        'width' => $item['width'],
                        'height' => $item['height'],
                        'volume' => $item['volume'],
                        'tonnage' => $item['tonnage'],
                        'tonnage_gross' => $item['tonnage_gross'],
                        'is_hold' => $item['is_hold'],
                        'status' => $item['status'],
                        'status_danger' => $item['status_danger'],
                        'ex_no_container' => $item['ex_no_container'],
                        'description' => $item['description']
                    ]);
                }
            }
            if (empty($goods) && empty($containers)) {
                $this->transporterEntryPermit->update([
                    'checked_in_description' => "without container",
                ], $id);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('warning', "Transporter entry permit <strong>{$tep['tep_code']}</strong> successfully updated");
            } else {
                flash('danger', "Update transporter entry permit <strong>{$tep['tep_code']}</strong> failed, try again or contact administrator");
            }

        }

        redirect('security/check?code=' . $tep['tep_code']);
    }

    /**
     * Save transporter entry permit
     * @param $id
     */
    public function check_in($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SECURITY_CHECK_IN);

        if ($this->validate(['receiver_name' => 'trim|required|max_length[50]'])) {
            $receiverName = $this->input->post('receiver_name');
            $receiverContact = $this->input->post('receiver_contact');
            $receiverEmail = $this->input->post('receiver_email');
            $receiverVehicle = $this->input->post('receiver_vehicle');
            $receiverNoPolice = $this->input->post('receiver_no_police');
            $additional_guest_name = $this->input->post('additional_guest_name');
            $chassisHandlingType = $this->input->post('chassis_handling_type');
            $noChassis = $this->input->post('no_chassis');
            $description = $this->input->post('description');
            $branch = get_active_branch();
            $tepReq = $this->transporterEntryPermitRequest->getRequestByTep($id);
            $timeCheckin = date('Y-m-d H:i:s');

            $clean_plat = preg_replace('/[^a-zA-Z0-9]/', '', $receiverNoPolice);
            $vehicle_in = $this->transporterEntryPermit->getBy([
                "transporter_entry_permits.receiver_no_police is not NULL"=>NULL,
                "transporter_entry_permits.checked_out_at is NULL"=>NULL,
                'transporter_entry_permits.checked_in_at >='=> "2019-11-23 00:00:00",
            ]);

            $canCheckIn = true;
            foreach ($vehicle_in as $vehicle) {
                $clean_vehicle = preg_replace('/[^a-zA-Z0-9]/', '', $vehicle['receiver_no_police']); 
                if (strtolower(trim($clean_plat))==strtolower(trim($clean_vehicle))) {
                    $canCheckIn = false;
                }
            }           
            //konek ke VMS
            $getBranchVms = $this->branchVms->getById($branch['id_branch_vms']);

            // generate unique id
            while (true) {
                $uniqId = uniqid();
                $uniqNum = rand(1, 5);
                $uniqId = strtoupper(substr($uniqId, $uniqNum, 5));

                if (empty($this->guest->getBy(['uniqid' => $uniqId], true))) {
                    break;
                }
            }

            $this->db->trans_start();

            $tep1 = $this->transporterEntryPermit->getById($id);
            $employee = $this->employee->getById($branch['pic']);

            // create chassis
            $chassisDelivery = $chassisHandlingType == 'drop-chassis';
            if ($chassisDelivery) {
                $this->transporterEntryPermitChassis->create([
                    'id_tep' => $id,
                    'no_chassis' => $noChassis,
                    'checked_in_at' => $timeCheckin,
                    'checked_in_description' => 'Checked in from TEP ' . $tep1['tep_code']
                ]);
            }

            if(!empty($employee)){

                if ($canCheckIn) {

                    $this->transporterEntryPermit->update([
                        'receiver_name' => $receiverName,
                        'receiver_contact' => $receiverContact,
                        'receiver_email' => $receiverEmail,
                        'receiver_vehicle' => $receiverVehicle,
                        'receiver_no_police' => $receiverNoPolice,
                        'additional_guest_name' => !empty($additional_guest_name) ? implode(',', $additional_guest_name) : null,
                        'checked_in_description' => $description,
                        'checked_in_at' => $timeCheckin,
                        'checked_in_by' => UserModel::authenticatedUserData('id'),
                        'chassis_delivery' => $chassisDelivery
                    ], $id);

                    // update linked tep
                    if (!empty($tep1['id_linked_tep'])) {
                        $linkedTep = $this->transporterEntryPermit->getAll([
                            'branch' => 2, // hard-coded medan 1
                            'tep' => $tep1['id_linked_tep']
                        ]);
                        if (!empty($linkedTep)) {
                            $linkedTep = $linkedTep[0];
                            $tep = $this->transporterEntryPermit->getById($id);
                            $this->transporterEntryPermit->update([
                                'checked_in_at' => if_empty($tep['checked_in_at'], null),
                                'checked_in_by' => if_empty($tep['checked_in_by'], null),
                                'checked_in_description' => if_empty($tep['checked_in_description'], null),
                                'receiver_name' => if_empty($tep['receiver_name'], null),
                                'receiver_contact' => if_empty($tep['receiver_contact'], null),
                                'receiver_email' => if_empty($tep['receiver_email'], null),
                                'receiver_vehicle' => if_empty($tep['receiver_vehicle'], null),
                                'receiver_no_police' => if_empty($tep['receiver_no_police'], null),
                                'additional_guest_name' => if_empty($tep['additional_guest_name'], null),
                            ], $linkedTep['id']);
                        }
                    }

                    //input ke VMS
                    if($tep1['tep_category'] == "OUTBOUND"){
                        $customerNameTEP = !empty($tep1['customer_name_out']) ? $tep1['customer_name_out'] : "-";
                    }else{
                        if($tep1['tep_category'] == "INBOUND"){
                            $customerNameTEP = !empty($tep1['customer_name_in']) ? $tep1['customer_name_in'] : (!empty($tep1['customer_name']) ? $tep1['customer_name'] : $tep1['customer_name_out'] );
                        }else{
                            $customerNameTEP = !empty($tep1['customer_name_out_ecs']) ? $tep1['customer_name_out_ecs'] : $tep1['customer_name_out_ec'];
                        }
                    }

                    $this->guest->create([
                        'id_branch' => $branch['id_branch_vms'],
                        'id_area' => $getBranchVms['id_area'],
                        'id_karyawan' => $branch['pic'],
                        'uniqid' => $uniqId,
                        'nama_visitor' => $receiverName,
                        'email' => $receiverEmail,
                        'no_telepon' => $receiverContact,
                        'asal' => $customerNameTEP,
                        'keperluan' => 'Kegiatan Inbound Outbound', // Hard Code
                        'janjian' => date('Y-m-d H:i:s'),
                        'hse' => 'Y',
                        'status' => GuestModel::STATUS_SCHEDULED
                    ]);

                    $guestId = $this->db->insert_id();
                    $guest = $this->guest->getById($guestId);
                    $area = $this->areaVms->getById($getBranchVms['id_area']);


                    if(!empty($additional_guest_name)){
                        foreach($additional_guest_name AS $additional_guest){
                            $this->additionalGuestVms->create([
                                'id_guest' => $guestId,
                                'nama_visitor' => $additional_guest
                             ]);
                        }
                    }

                    $this->statusHistoryVms->create([
                        'type' => StatusHistoryVmsModel::TYPE_GUEST,
                        'id_reference' => $guestId,
                        'status' => GuestModel::STATUS_SCHEDULED,
                        'description' => 'Initial creating data',
                        'data' => json_encode([
                            'guest' => $guest,
                            'creator' => UserModel::authenticatedUserData('name')
                        ])
                    ]);

                    $this->statusHistoryVms->create([
                        'type' => StatusHistoryVmsModel::TYPE_GUEST,
                        'id_reference' => $guestId,
                        'status' => GuestModel::STATUS_AREA_VISITED,
                        'description' => $area['area'],
                        'data' => json_encode([
                            'visit' => $area,
                            'creator' => UserModel::authenticatedUserData('name')
                        ])
                    ]);

                } else {
                    flash('danger', "Check in entry permit <strong>{$tep1['tep_code']} - {$receiverNoPolice}</strong> failed, because plate number hasn't checked out yet");
                    redirect('security');
                }
            }else{
                flash('danger', "Check in entry permit <strong>{$tep1['tep_code']} - {$receiverNoPolice}</strong> failed, please set pic name in your branch !");
                redirect('security');
            }       

            $tep = $this->transporterEntryPermit->getById($id);

            $getSafeConductByTep = $this->safeConduct->getSafeConductsByTepId($id);
            if (!empty($getSafeConductByTep)) {
                // check in
                foreach ($getSafeConductByTep as $safeConductTep) {
                    $this->safeConduct->updateSafeConduct([
                        'security_in_date' => date('Y-m-d H:i:s'),
                        'updated_by' => UserModel::authenticatedUserData('id')
                    ], $safeConductTep['id']);
                }
            }
            $tepChecklist = $this->TransporterEntryPermitChecklist->getBy(['id_tep' => $id]);
            //$fileName = $tepChecklist[0]['attachment'];
            $attachmentSeal = $tepChecklist[0]['attachment_seal'];
            $getContainer = $this->transporterEntryPermitContainer->getById($tepChecklist[0]['id_container']);
            $text_date = date('d F Y H:i:s', strtotime($tepChecklist[0]['created_at'])) . "\n";

            $text_type = $tepChecklist[0]['type'] == "CHECK IN" ? "EXTERNAL IN BY " . strtoupper(UserModel::authenticatedUserData('username')) . "\n" : "EXTERNAL OUT BY " . strtoupper(UserModel::authenticatedUserData('username')) . "\n";
            $text_container_number = !empty($getContainer) ? $getContainer['no_container'] . "\n" : null;
            $text_customer_name = !empty($tep['customer_name']) ? $tep['customer_name'] . "\n" : (!empty($tep['customer_name_out']) ?$tep['customer_name_out'] . "\n":'');
            $text_aju = !empty(substr($tep['no_reference'], -4)) ? strtoupper($tep['tep_category']) . " AJU " . substr($tep['no_reference'], -4) : strtoupper($tep['tep_category']);
            $text_driver = !empty($tep['receiver_no_police']) && !empty($tep['receiver_name']) ? $tep['receiver_no_police'] . " | " . $tep['receiver_name'] . "\n" : null;
            $text_code = ($text_driver == null) ? substr($tep['tep_code'], 0, -2) . "**" . "\n" : null;
            $tempText = $text_date . $text_type . $text_container_number . $text_driver . $text_customer_name . $text_code . $text_aju;
            // $path = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'safe_conducts_checklist' . DIRECTORY_SEPARATOR . $fileName;
            /*$pathWa = 'safe_conducts_checklist/' . $fileName;
            $watermark = $this->watermark($pathWa, $fileName, $tempText);
            if (!empty($attachmentSeal)) {
                $pathSeal = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'safe_conducts_checklist' . DIRECTORY_SEPARATOR . $attachmentSeal;
                $pathWaSeal = 'safe_conducts_checklist/'. $attachmentSeal;
                $this->watermark($pathSeal, $attachmentSeal, $tempText);
            }*/

            $tepChecklistPhotos = $this->transporterEntryPermitChecklistPhoto->getBy([
                'id_tep_checklist' => $tepChecklist[0]['id'],
            ]);
            foreach ($tepChecklistPhotos as $index => $tepChecklistPhoto) {
                $this->watermark($tepChecklistPhoto['photo'], basename($tepChecklistPhoto['photo']), $tempText);
            }
            if (!empty($attachmentSeal)) {
                $this->watermark($attachmentSeal, basename($attachmentSeal), $tempText);
            }

            $whatsapp_group_internal = $branch['whatsapp_group'];
            $whatsapp_group = $branch['whatsapp_group_security'];

            $customerId = !empty($tep1['id_customer_out'])? $tep1['id_customer_out'] : $tep1['id_customer_out_ecs'];
            $customerId = explode(",",$customerId);
            $customers = $this->people->getById($customerId);

            if(!empty($tepReq[0]['queue_time'])){
                $timeQueue = $tepReq[0]['tep_date'] . ' ' . $tepReq[0]['queue_time'];
                if($timeCheckin > $timeQueue){
                    $requestId = $this->transporterEntryPermitRequestTep->getby(['id_tep' => $id]);
                    $ajuCustomer = [];
                    foreach ($requestId as $id_req) {
                        $tep_request=$this->transporterEntryPermitRequest->getById($id_req['id_request']);                    
                        $tepRequestMergeUploads = $this->transporterEntryPermitRequestUpload->getBy(['id_request' => $id_req['id_request']]);
                        foreach($customerId AS $customer_id){
                            if($tep_request['id_customer'] == $customer_id){
                                $ajuCustomer[$customer_id] = implode(', ', array_map(function ($noRef) {
                                    return substr($noRef, -6, 6);
                                }, array_unique(array_column($tepRequestMergeUploads, 'no_reference_upload'))));
                            }
                        }
                    }
                    foreach ($customers as $key => $customer) {
                        $whatsapp_group = $customer['whatsapp_group'];
                        $data_whatsapp = "[LATE CHECK IN] Transporter entry permit with code *".$tep1['tep_code']."* has late check-in from *".date("H:i:s d F Y", strtotime($timeQueue))."* to *".date("H:i:s d F Y", strtotime($timeCheckin))."*\n";
                        $data_whatsapp .= "Nopol : *".$receiverNoPolice."*\n";
                        $data_whatsapp .= "Aju : *".$ajuCustomer[$customer['id']]."*\n";
                        $this->send_message_text($data_whatsapp,$whatsapp_group);
                    }
                }
            }
            
            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                //$this->send_message($pathWa, $whatsapp_group, $whatsapp_group_internal);
                //if (!empty($attachmentSeal)) {
                //    $this->send_message($pathWaSeal, $whatsapp_group, $whatsapp_group_internal);
                //}
                foreach ($tepChecklistPhotos as $tepChecklistPhoto) {
                    $this->send_message($tepChecklistPhoto['photo'], $whatsapp_group, $whatsapp_group_internal);
                }
                if (!empty($attachmentSeal)) {
                    $this->send_message($attachmentSeal, $whatsapp_group, $whatsapp_group_internal);
                }
                flash('warning', "Entry permit <strong>{$tep['tep_code']}</strong> successfully checked in");
            } else {
                flash('danger', "Check in entry permit <strong>{$tep['tep_code']}</strong> failed");
            }
        }
        redirect('security');
    }

    /**
     * Save transporter entry permit out
     * @param $id
     */
    public function check_out($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SECURITY_CHECK_OUT);

        $tep = $this->transporterEntryPermit->getById($id);
        $tepContainer = $this->transporterEntryPermitContainer->getTepContainersByTep($tep['id']);
        $tepChecklist = $this->TransporterEntryPermitChecklist->getTepChecklistByTepId($tep['id']);

        $this->db->trans_start();

        if ($tep['category'] == "OUTBOUND") {
            if(!empty($tep['id_safe_conduct'])){
                $idSafeConducts = explode(",", $getSafeConductById);
                foreach($safeConducts AS $idSafeConduct){
                    $safeConduct = $this->safeConduct->getSafeConductById($idSafeConduct); 
                    if (!empty($safeConduct)) {
                        $safeConductContainers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($safeConduct['id']);
                        foreach ($safeConductContainers as &$container) {
                            $containerGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConductContainer($container['id']);
                            $container['goods'] = $containerGoods;
                        }
                        $safeConductGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($safeConduct['id'], true);
                        $safeConductChecklist = $this->safeConductChecklist->getSafeConductChecklistBySafeConductId($safeConduct['id']);

                        if (!empty($safeConductContainers)) {
                            foreach ($safeConductContainers as $containers) {
                                $this->transporterEntryPermitContainer->createTepContainer([
                                    'id_tep' => $tep['id'],
                                    'id_booking_container' => $container['id_booking_container'],
                                    'id_container' => $container['id_container'],
                                    'id_position' => $container['id_position'],
                                    'seal' => if_empty($container['seal'], null),
                                    'is_empty' => $container['is_empty'],
                                    'is_hold' => $container['is_hold'],
                                    'status' => $container['status'],
                                    'status_danger' => $container['status_danger'],
                                    'description' => $container['description'],
                                    'quantity' => $container['quantity'],
                                ]);

                                $tepContainerId = $this->db->insert_id();

                                if (!empty($safeConductChecklist)) {
                                    foreach ($safeConductChecklist as $Checklist) {
                                        if (!empty($Checklist['id_container']) && $Checklist['type'] == "CHECK OUT" && $Checklist['id_container'] == $container['id']) {
                                            $safeConductChecklistDetails = $this->safeConductChecklistDetail->getChecklistDetailByChecklistId($Checklist['id']);

                                            // tep checklist
                                            $this->TransporterEntryPermitChecklist->create([
                                                'id_tep' => $tep['id'],
                                                'id_container' => $tepContainerId,
                                                'type' => $Checklist['type'],
                                                'attachment' => $Checklist['attachment'],
                                            ]);

                                            //tep checklist detail
                                            $tepChecklistId = $this->db->insert_id();
                                            if (!empty($safeConductChecklistDetails)) {
                                                foreach ($safeConductChecklistDetails as $ChecklistDetail) {
                                                    $this->TransporterEntryPermitChecklistDetail->create([
                                                        'id_tep_checklist' => $tepChecklistId,
                                                        'id_checklist' => $ChecklistDetail['id_checklist'],
                                                        'result' => $ChecklistDetail['result'],
                                                        'description' => $ChecklistDetail['description'],
                                                    ]);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if (!empty($safeConductGoods)) {
                            foreach ($safeConductGoods as $item) {
                                $this->transporterEntryPermitGoods->createTepGoods([
                                    'id_tep' => $tep['id'],
                                    'id_delivery_order_goods' => null,
                                    'id_booking_goods' => $item['id_booking_goods'],
                                    'id_goods' => $item['id_goods'],
                                    'id_unit' => $item['id_unit'],
                                    'id_position' => $item['id_position'],
                                    'no_pallet' => if_empty($item['no_pallet'], null),
                                    'no_delivery_order' => null,
                                    'quantity' => $item['quantity'],
                                    'length' => $item['length'],
                                    'width' => $item['width'],
                                    'height' => $item['height'],
                                    'volume' => $item['volume'],
                                    'tonnage' => $item['tonnage'],
                                    'tonnage_gross' => $item['tonnage_gross'],
                                    'is_hold' => $item['is_hold'],
                                    'status' => $item['status'],
                                    'status_danger' => $item['status_danger'],
                                    'ex_no_container' => $item['ex_no_container'],
                                    'description' => $item['description']
                                ]);
                            }
                        }

                        if (!empty($safeConductChecklist)) {
                            foreach ($safeConductChecklist as $Checklist) {
                                if (empty($Checklist['id_container']) && $Checklist['type'] == "CHECK OUT") {
                                    $safeConductChecklistDetails = $this->safeConductChecklistDetail->getChecklistDetailByChecklistId($Checklist['id']);

                                    // tep checklist
                                    $this->TransporterEntryPermitChecklist->create([
                                        'id_tep' => $tep['id'],
                                        'id_container' => null,
                                        'type' => $Checklist['type'],
                                        'attachment' => $Checklist['attachment'],
                                    ]);

                                    //tep checklist detail
                                    $tepChecklistId = $this->db->insert_id();
                                    if (!empty($safeConductChecklistDetails)) {
                                        foreach ($safeConductChecklistDetails as $ChecklistDetail) {
                                            $this->TransporterEntryPermitChecklistDetail->create([
                                                'id_tep_checklist' => $tepChecklistId,
                                                'id_checklist' => $ChecklistDetail['id_checklist'],
                                                'result' => $ChecklistDetail['result'],
                                                'description' => $ChecklistDetail['description'],
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        } else {
            if(!empty($tep['id_safe_conduct'])){
                $idSafeConducts = explode(",", $getSafeConductById);
                foreach($safeConducts AS $idSafeConduct){
                    $safeConduct = $this->safeConduct->getSafeConductById($idSafeConduct);
                    
                    if (!empty($safeConduct)) {
                        if (!empty($tepContainer)) {
                            foreach ($tepContainer as $container) {
                                if (!empty($tepChecklist)) {
                                    foreach ($tepChecklist as $Checklist) {
                                        if (!empty($Checklist['id_container']) && $Checklist['type'] == "CHECK OUT" && $Checklist['id_container'] == $container['id']) {
                                            $tepChecklistDetails = $this->TransporterEntryPermitChecklistDetail->getTepChecklistDetailByChecklistId($Checklist['id']);

                                            // safe conduct checklist
                                            $this->safeConductChecklist->create([
                                                'id_safe_conduct' => $safeConductId,
                                                'id_container' => $safeConductContainerId, //id_safe_conduct_container
                                                'type' => $Checklist['type'],
                                                'attachment' => $Checklist['attachment'],
                                            ]);

                                            //safe conduct detail
                                            $safeConductChecklistId = $this->db->insert_id();
                                            if (!empty($tepChecklistDetails)) {
                                                foreach ($tepChecklistDetails as $ChecklistDetail) {
                                                    $this->safeConductChecklistDetail->create([
                                                        'id_safe_conduct_checklist' => $safeConductChecklistId,
                                                        'id_checklist' => $ChecklistDetail['id_checklist'],
                                                        'result' => $ChecklistDetail['result'],
                                                        'description' => $ChecklistDetail['description'],
                                                    ]);
                                                }
                                            }
                                        }
                                    }
                                }
                            }
                        }

                        if (!empty($tepChecklist)) {
                            foreach ($tepChecklist as $Checklist) {
                                if (empty($Checklist['id_container']) && $Checklist['type'] == "CHECK OUT") {
                                    $tepChecklistDetails = $this->TransporterEntryPermitChecklistDetail->getTepChecklistDetailByChecklistId($Checklist['id']);

                                    // safe conduct checklist
                                    $this->safeConductChecklist->create([
                                        'id_safe_conduct' => $safeConduct['id'],
                                        'id_container' => null, //id_safe_conduct_container
                                        'type' => $Checklist['type'],
                                        'attachment' => $Checklist['attachment'],
                                    ]);

                                    //safe conduct detail
                                    $safeConductChecklistId = $this->db->insert_id();
                                    if (!empty($tepChecklistDetails)) {
                                        foreach ($tepChecklistDetails as $ChecklistDetail) {
                                            $this->safeConductChecklistDetail->create([
                                                'id_safe_conduct_checklist' => $safeConductChecklistId,
                                                'id_checklist' => $ChecklistDetail['id_checklist'],
                                                'result' => $ChecklistDetail['result'],
                                                'description' => $ChecklistDetail['description'],
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
        $booking = $this->booking->getBookingById($tep['id_booking']);
        $description = $this->input->post('description');

        $checkOut = $this->transporterEntryPermit->update([
            'checked_out_description' => $description,
            'checked_out_at' => !empty($tep['id_safe_conduct']) == true && !is_null($safeConduct['security_out_date']) ? $safeConduct['security_out_date'] : date('Y-m-d H:i:s'),
            'checked_out_by' => UserModel::authenticatedUserData('id')
        ], $id);

        $tepAfterUpdate = $this->transporterEntryPermit->getById($id);
        $getSafeConductByTep = $this->safeConduct->getSafeConductsByTepId($id);
        if (!empty($getSafeConductByTep)) {
            // check out
            foreach ($getSafeConductByTep as $safeConductTep) {
                $this->safeConduct->updateSafeConduct([
                    'security_out_date' => date('Y-m-d H:i:s'),
                    'updated_by' => UserModel::authenticatedUserData('id')
                ], $safeConductTep['id']);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            flash('warning', "Entry permit <strong>{$tep['tep_code']}</strong> successfully checked out");
        } else {
            flash('danger', "Check out entry permit <strong>{$tep['tep_code']}</strong> failed");
        }

        redirect('security');
    }

    public function check_out_now()
    {
        $idTep = $this->input->post('id');
        $description = $this->input->post('description');
        $label = $this->input->post('label');
        $chassisHandlingType = $this->input->post('chassis_handling_type');
        $chassisId = $this->input->post('chassis');

        $this->db->trans_start();

        $timeCheckOut = date('Y-m-d H:i:s');

        $this->transporterEntryPermit->update([
            'checked_out_description' => $description,
            'checked_out_at' => $timeCheckOut,
            'checked_out_by' => UserModel::authenticatedUserData('id')
        ], $idTep);

        // update chassis checkout
        $pickupChassis = $chassisHandlingType == 'pickup-chassis';
        if ($pickupChassis) {
            $this->transporterEntryPermitChassis->update([
                'id_tep_out' => $idTep,
                'checked_out_at' => $timeCheckOut,
                'checked_out_description' => 'Checked out from TEP ' . $label
            ], $chassisId);
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            flash('warning', "Entry permit <strong>{$label}</strong> successfully checked out");
        } else {
            flash('danger', "Check out entry permit <strong>{$label}</strong> failed");
        }
        redirect('security');
    }


    /**
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'total_code' => 'trim|required|integer|is_natural_no_zero',
            'description' => 'trim|max_length[500]',
        ];
    }

    /**
     * Perform deleting entry permit.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_DELETE);

        $tep = $this->transporterEntryPermit->getById($id);

        if ($this->transporterEntryPermit->delete($id)) {
            flash('warning', "Entry permit {$tep['tep_code']} is successfully deleted");
        } else {
            flash('danger', "Delete entry permit {$tep['tep_code']} failed");
        }
        redirect('transporter-entry-permit');
    }

    /**
     * Perform cancel entry permit.
     *
     * @param $id
     */
    public function cancel_tep($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_DELETE);

        $tep = $this->transporterEntryPermit->getById($id);
        $update = $this->transporterEntryPermit->update([
            'expired_at' => date("Y-m-d 23:59:59", strtotime("- 1 day")),
        ],$id);
        if ($update) {
            flash('warning', "Entry permit {$tep['tep_code']} is successfully cancel");
        } else {
            flash('danger', "Cancel entry permit {$tep['tep_code']} failed");
        }
        redirect('transporter-entry-permit');
    }

    /**
     * Get tep data for creating safe conduct.
     */
    public function ajax_get_tep_by_id()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $tepId = $this->input->get('id_tep');
            $tep = $this->transporterEntryPermit->getById($tepId);

            $tepContainers = $this->transporterEntryPermitContainer->getTepContainersByTep($tep['id']);
            foreach ($tepContainers as &$container) {
                $containerGoods = $this->transporterEntryPermitGoods->getTepGoodsByTepContainer($container['id']);
                $container['goods'] = $containerGoods;
            }
            $tepGoods = $this->transporterEntryPermitGoods->getTEPGoodsByTEP($tep['id'], true);
            if (isset($_SERVER['HTTP_ACCEPT']) && preg_match('/text\/html/', $_SERVER['HTTP_ACCEPT'])) {
                if (!empty($tep)) {
                    if (!empty($tepContainers) || !empty($goods)) {
                        echo $this->load->view('transporter_entry_permit/_data_header', [
                            'tep' => $tep,
                        ], true);
                        echo $this->load->view('transporter_entry_permit/_data_detail', [
                            'tep' => $tep,
                            'tepContainers' => $tepContainers,
                            'tepGoods' => $tepGoods,
                        ], true);
                    } else {
                        echo $this->load->view('transporter_entry_permit/_data_header', [
                            'tep' => $tep,
                        ], true);
                    }
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'tep' => $tep,
                    'tepContainers' => $tepContainers,
                    'tepGoods' => $tepGoods,
                ]);
            }
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
     */
    public function send_message($path, $whatsapp_group, $whatsapp_group_internal)
    {
        $data = [
            'url' => 'sendFile',
            'method' => 'POST',
            'payload' => [
                'chatId' => detect_chat_id($whatsapp_group),
                'body' => asset_url($path), //base_url('uploads/'.$path), //'https://transcon-indonesia.com/img/front/opening/icon.png',
                'filename' => 'transcon.png',
            ]
        ];
        $data2 = [
            'url' => 'sendFile',
            'method' => 'POST',
            'payload' => [
                'chatId' => detect_chat_id($whatsapp_group_internal),
                'body' => asset_url($path), ///'https://transcon-indonesia.com/img/front/opening/icon.png',
                'filename' => 'transcon.png',
            ]
        ];

        $result = $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);
        $result2 = $this->notification->broadcast($data2, NotificationModel::TYPE_CHAT_PUSH);

    }
    /**
     * Get tep reference by customer.
     */
    public function get_tep_reference_customer(){
        $id_customer=$this->input->post('id_customer');
        $where=[
            'people_customers.id'=>$id_customer,
            'transporter_entry_permits.tep_category'=>'OUTBOUND'
            // 'checked_out_at is not null'=>null
        ];
        $vehicle=$this->transporterEntryPermit->getBy($where);
        $vehicles=[];
        foreach ($vehicle as $key ) {
            $id_tep = $key['id_tep_reference'];
            $i=0;
            foreach ($vehicle as $key2) {
                if ($key2['id']==$id_tep) {
                    unset($vehicle[$i]);
                }
                $i++;
            }
        }
        foreach ($vehicle as $value) {
            array_push($vehicles,$value);
        }        
        header('Content-Type: application/json');
        echo json_encode($vehicles);
    }

    /**
     * Get tep reference by customer.
     */
    public function get_tep_reference_by_request(){
        $id_request=$this->input->post('id_request');
        $requests = $this->transporterEntryPermitRequest->getAll([
            'request' => $id_request,
        ]);
        $id_customer = array_column($requests, 'id_customer');
        $where=[
            'people_customers.id'=>$id_customer,
            'transporter_entry_permits.tep_category'=>'OUTBOUND'
            // 'checked_out_at is not null'=>null
        ];
        $vehicle=$this->transporterEntryPermit->getBy($where);
        $vehicles=[];
        foreach ($vehicle as $key ) {
            $id_tep = $key['id_tep_reference'];
            $i=0;
            foreach ($vehicle as $key2) {
                if ($key2['id']==$id_tep) {
                    unset($vehicle[$i]);
                }
                $i++;
            }
        }
        foreach ($vehicle as $value) {
            array_push($vehicles,$value);
        }        
        header('Content-Type: application/json');
        echo json_encode($vehicles);
    }

    /**
     * Get tep reference by booking.
     */
    // public function get_tep_reference_booking(){
    //     $id_booking=$this->input->post('id_booking');
    //     $where=[
    //         'bookings.id'=>$id_booking,
    //         'transporter_entry_permits.tep_category'=>'INBOUND'
    //         // 'checked_out_at is not null'=>null
    //     ];
    //     $vehicle=$this->transporterEntryPermit->getBy($where);
        
    //     header('Content-Type: application/json');
    //     echo json_encode($vehicle);
    // }
    
    /**
     * Show print layout safe conduct.
     */
    public function print($tepId){
        $tep = $this->transporterEntryPermit->getBy(['transporter_entry_permits.id' => $tepId, 'NOW()<=transporter_entry_permits.expired_at' => null], true);
        $branch = get_active_branch();
        $tep['branch']=$branch['branch'];
        $barcode = new DNS2D();
        $barcode->setStorPath(APPPATH . "/cache/");
        $tepBarcode = $barcode->getBarcodePNG($tep['tep_code'], "QRCODE", 8, 8);
        $data = [
            'title' => 'Print Transporter Entry Permit',
            'page' => 'transporter_entry_permit/print_transporter_entry_permit',
            'tep' => $tep,
            'tepBarcode' => $tepBarcode,
        ];
        $this->load->view('template/print', $data);
    }
    /**
     * Show form request transporter entry permit outbound.
     */
    public function request()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_REQUEST);

        $idPerson = UserModel::authenticatedUserData('id_person');

        $id_booking=array();

        $counterHari = 0;
        do {
            $counterHari++;
            $temp_tanggal = date('Y-m-d',strtotime('+'.$counterHari.' days',time()));
            $holiday=$this->scheduleHoliday->is_holiday($temp_tanggal);
        } while (date('w',strtotime($temp_tanggal))==0 || $holiday);
        // print_debug($temp_tanggal);
        $slot = $this->transporterEntryPermitRequest->getSlotRequest(['tep_date'=>$temp_tanggal]);
        $slot_filled=0;
        foreach ($slot as $slot) {
            $slot_filled += $slot['slot'];
        }
        
        $slot_remain = get_active_branch('max_slot_tep')-$slot_filled;
        $slot_add = $this->transporterEntryPermitSlot->getSlot(['tep_date'=>$temp_tanggal]);
        if (!empty($slot_add)) {
            $slot_remain+=$slot_add[0]['total_slot'];
        }

        $uploads = $this->upload->getUploadSppbByCondition([
            'uploads.id_person' => $idPerson,
            'ref_booking_types.category' => 'OUTBOUND',
            'doc_type.document_type' => 'SPPB',
            '`doc_type_sppd`.`document_type` IS NULL' => null,
            'uploads.id_branch' => get_active_branch('id'),
            ]);
        $customers = $this->people->getByType(PeopleModel::$TYPE_CUSTOMER);
        $userType = UserModel::authenticatedUserData('user_type');
        if ($userType=='EXTERNAL') {
            if (empty($uploads)) {
                flash('warning', "You can't request transporter entry permit because you don't have SPPB active");
            }
        }
        
        $holidayDate=$this->scheduleHoliday->getBy(["schedule_holidays.date>='".$temp_tanggal."'" => null]);
        $holidayDate = array_column($holidayDate,'date');
        $holidayDate = implode(',',$holidayDate);

        $this->render('transporter_entry_permit/request', compact('uploads','customers','slot_remain', 'temp_tanggal', 'holidayDate'));
    }

    /**
     * Show form request transporter entry permit inbound.
     */
    public function request_inbound()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_REQUEST);

        $idPerson = UserModel::authenticatedUserData('id_person');

        $id_booking=array();

        $counterHari = 0;
        do {
            $counterHari++;
            $temp_tanggal = date('Y-m-d',strtotime('+'.$counterHari.' days',time()));
            $holiday=$this->scheduleHoliday->is_holiday($temp_tanggal);
        } while (date('w',strtotime($temp_tanggal))==0 || $holiday);
        // print_debug($temp_tanggal);
        $slot = $this->transporterEntryPermitRequest->getSlotRequest(['tep_date'=>$temp_tanggal]);
        $slot_filled=0;
        foreach ($slot as $slot) {
            $slot_filled += $slot['slot'];
        }
        
        $slot_remain = get_active_branch('max_slot_tep')-$slot_filled;
        $slot_add = $this->transporterEntryPermitSlot->getSlot(['tep_date'=>$temp_tanggal]);
        if (!empty($slot_add)) {
            $slot_remain+=$slot_add[0]['total_slot'];
        }

        $uploads = $this->upload->getUploadSppbByCondition([
            'uploads.id_person' => $idPerson,
            'ref_booking_types.category' => 'INBOUND',
            'doc_type.document_type' => 'SPPB',
            '`doc_type_sppd`.`document_type` IS NULL' => null,
            'uploads.id_branch' => get_active_branch('id'),
            "ref_booking_types.booking_type IN ('BC 3.3', 'BC 4.0', 'BC27 INBOUND')" => null,
            ]);

        $customers = $this->people->getByType(PeopleModel::$TYPE_CUSTOMER);
        $userType = UserModel::authenticatedUserData('user_type');
        if ($userType=='EXTERNAL') {
            if (empty($uploads)) {
                flash('warning', "You can't request transporter entry permit because you don't have SPPB active");
            }
        }
        $holidayDate=$this->scheduleHoliday->getBy(["schedule_holidays.date>='".$temp_tanggal."'" => null]);
        $holidayDate = array_column($holidayDate,'date');
        $holidayDate = implode(',',$holidayDate);
        
        $this->render('transporter_entry_permit/request_inbound', compact('uploads','customers','slot_remain', 'temp_tanggal', 'holidayDate'));
    }

    /**
     * Get available booking to make request tep.
     */
    public function ajax_get_available_sppb()
    {
        $idPerson = $this->input->get('id_customer');
        $category = $this->input->get('category');

        if($category == 'INBOUND'){
            $upload = $this->upload->getUploadSppbByCondition([
                'uploads.id_person' => $idPerson,
                'ref_booking_types.category' => 'INBOUND',
                'doc_type.document_type' => 'SPPB',
                '`doc_type_sppd`.`document_type` IS NULL' => null,
                'uploads.id_branch' => get_active_branch('id'),
                "ref_booking_types.booking_type IN ('BC 3.3', 'BC 4.0', 'BC27 INBOUND')" => null,
                ]);
        }else{
            $upload = $this->upload->getUploadSppbByCondition([
                'uploads.id_person' => $idPerson,
                'ref_booking_types.category' => 'OUTBOUND',
                'doc_type.document_type' => 'SPPB',
                '`doc_type_sppd`.`document_type` IS NULL' => null,
                'uploads.id_branch' => get_active_branch('id'),
                ]);
        }

        header('Content-Type: application/json');
        echo json_encode($upload);
    }
    /**
     * Get available booking to make request tep.
     */
    public function ajax_get_goods_container()
    {
        $id_booking = $this->input->get('id_booking');
        $bookingIn = $this->booking->getBookingById($id_booking);
        //stock goods        
        $record_goods = $this->reportStock->getStockGoods([
            'booking'=>$bookingIn['id_booking'],
            'data'=>'stock',
        ]);
        //stock container
        $record_container = $this->reportStock->getStockContainers([
            'booking'=>$bookingIn['id_booking'],
            'data'=>'stock',
        ]);
        $bookingData['record_goods'] = $record_goods;
        $bookingData['record_container'] = $record_container;
        
        header('Content-Type: application/json');
        echo json_encode($bookingData);
    }
    /**
     * Save request transporter entry permit
     */
    public function save_request()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_REQUEST);

        if ($this->validate([
            'description' => 'trim|max_length[500]',
            'armada' => 'trim|required',
        ])) {
            $armada = $this->input->post('armada');
            $noRequestMulti = [];
            if($armada == 'TCI'){
                $goods = $this->input->post('goods');
                $customerId = $this->input->post('customer');
                $location = $this->input->post('location');
                $armada = $this->input->post('armada');
                $description = $this->input->post('description');
                $booking = $this->input->post('booking');
                $customer = $this->people->getById($customerId);
                if (empty($booking)) {
                    flash('warning', "You can't request transporter entry permit because you don't have booking active", 'transporter-entry-permit/request');
                }
    
                $this->db->trans_start();
                // add request entry permit
                $aju_sort = array_column($goods, 'no_reference');
                array_multisort($aju_sort, SORT_ASC, $goods);
                $tempAju = [];
                foreach ($goods as $item) {
                    if ((in_array($item['no_reference'], $tempAju)) == false) {
                        $tempAju[] = $item['no_reference'];
                    }
                }

                foreach($tempAju as $aju){
                    $noRequest = $this->transporterEntryPermitRequest->getAutoNumberRequest();
                    $noRequestMulti[] = $noRequest;
                    $this->transporterEntryPermitRequest->create([
                        'id_customer'=>$customerId,
                        'id_branch'=>get_active_branch('id'),
                        'no_request'=>$noRequest,
                        'armada'=>$armada,
                        'location'=>$location,
                        'description'=>$description,
                    ]);   
                    $request_id = $this->db->insert_id();
                    $dataGoods = [];
                    foreach ($goods as $item) {
                        if($item['no_reference'] == $aju){
                            $dataGoods[] = [
                                'id_request' => $request_id,
                                'id_upload' => $item['id_upload'],
                                'id_goods' => $item['id_goods'],
                                'quantity' => $item['quantity'],
                                'work_order_quantity' => $item['work_order_quantity'],
                                'id_unit' => $item['id_unit'],
                                'goods_name' => $item['goods_name'],
                                'no_invoice' => $item['no_invoice'],
                                'no_bl' => $item['no_bl'],
                                'no_goods' => $item['no_goods'],
                                'unit' => $item['unit'],
                                'ex_no_container' => $item['ex_no_container'],
                                'no_reference' => $item['no_reference'],
                                'whey_number' => $item['whey_number'],
                                'hold_status' => if_empty($item['hold_status'], 'OK'),
                                'priority' => if_empty($item['priority'], null),
                                'unload_location' => if_empty($item['unload_location'], null),
                            ];
                        }                        
                    } 
                    $this->transporterEntryPermitRequestUpload->create($dataGoods);
                    $this->transporterEntryPermitRequestStatusHistory->create([
                        'id_request' => $request_id,
                        'status' => 'PENDING',
                        'description' => 'CREATE REQUEST',
                    ]);
                }                
            }else{
                $goods = $this->input->post('goods');
                $customerId = $this->input->post('customer');
                $slot = $this->input->post('slot');
                $description = $this->input->post('description');
                $booking = $this->input->post('booking');
                $tep_date = format_date($this->input->post('tep_date'),'Y-m-d');
                // $doFiles = $this->input->post('memo');

                $customer = $this->people->getById($customerId);
    
                $counterHari = 0;
                do {
                    $counterHari++;
                    $temp_tanggal = date('Y-m-d',strtotime('+'.$counterHari.' days',time()));
                    $holiday=$this->scheduleHoliday->is_holiday($temp_tanggal);
                } while (date('w',strtotime($temp_tanggal))==0 || $holiday);
                
                //max time request from master people if not set from master branches
                $maxTimeToday = '20:00';
                if(!empty($customer['max_time_request'])){
                    $maxTimeToday = $customer['max_time_request'];
                }elseif (!empty(get_active_branch('max_time_request'))) {
                    $maxTimeToday = get_active_branch('max_time_request');
                }

                //batas waktu request jam 20:00, namun untuk h-2,h-3,... itu gak ada batasan waktu
                if($tep_date==date('Y-m-d', strtotime("+ 1 day")) && (time() > strtotime($maxTimeToday))){
                    flash('warning', "You can't request transporter entry permit because it has passed the request limit, which is ".$maxTimeToday, 'transporter-entry-permit/request');
                }
                $slotnow = $this->transporterEntryPermitRequest->getSlotRequest(['tep_date'=>$tep_date]);
                $slot_filled=0;
    
                foreach ($slotnow as $slotnow) {
                    $slot_filled += $slotnow['slot'];
                }
                
                $slot_remain = get_active_branch('max_slot_tep')-$slot_filled;
                $slot_add = $this->transporterEntryPermitSlot->getSlot(['tep_date'=>$tep_date]);
                if (!empty($slot_add)) {
                    $slot_remain+=$slot_add[0]['total_slot'];
                }
                if (($slot_remain-(int)$slot)<0) {
                    flash('warning', "You can't request transporter entry permit because the slot has recently changed", 'transporter-entry-permit/request');
                }
                if (empty($booking)) {
                    flash('warning', "You can't request transporter entry permit because you don't have booking active", 'transporter-entry-permit/request');
                }
    
                $this->db->trans_start();
                // add request entry permit
                
                $noRequest = $this->transporterEntryPermitRequest->getAutoNumberRequest();
                $this->transporterEntryPermitRequest->create([
                    'id_customer'=>$customerId,
                    'id_branch'=>get_active_branch('id'),
                    'no_request'=>$noRequest,
                    'slot'=>$slot,
                    'description'=>$description,
                    'tep_date'=>$tep_date
                ]);   
                $request_id = $this->db->insert_id();   
                foreach ($goods as $item) {
                    $this->transporterEntryPermitRequestUpload->create([
                        'id_request' => $request_id,
                        'id_upload' => $item['id_upload'],
                        'id_goods' => $item['id_goods'],
                        'quantity' => $item['quantity'],
                        'work_order_quantity' => $item['work_order_quantity'],
                        'id_unit' => $item['id_unit'],
                        'goods_name' => $item['goods_name'],
                        'no_invoice' => $item['no_invoice'],
                        'no_bl' => $item['no_bl'],
                        'no_goods' => $item['no_goods'],
                        'unit' => $item['unit'],
                        'ex_no_container' => $item['ex_no_container'],
                        'no_reference' => $item['no_reference'],
                        'hold_status' => if_empty($item['hold_status'], 'OK'),
                        'priority' => if_empty($item['priority'], null),
                        'unload_location' => if_empty($item['unload_location'], null),
                    ]);
                } 
                $this->transporterEntryPermitRequestStatusHistory->create([
                    'id_request' => $request_id,
                    'date' => $tep_date,
                    'status' => 'PENDING',
                    'description' => 'CREATE REQUEST',
                ]);
                
                // foreach ($doFiles as $key => $doFile) {
                //     $sourceFile = 'temp/' . $doFile;
                //     $destFile = 'tep-request/' . format_date('now', 'Y/m/') . $doFile;
                //     if ($this->uploader->setDriver('s3')->move($sourceFile, $destFile)) {
                //         $this->transporterEntryPermitRequestFile->create([
                //             'id_tep_req' => $request_id,
                //             'file' => $doFile,
                //             'src' => $destFile,
                //             'url' => $this->uploader->setDriver('s3')->getUrl($destFile),
                //         ]);
                //         $uploadedPaths[] = $this->uploader->setDriver('s3')->getUrl($destFile);
                //     }
                // }
            }
            
            $this->db->trans_complete();

            if($armada == 'TCI'){
                $noRequest = implode(", ",$noRequestMulti); 
            }

            if ($this->db->trans_status()) {
                // send email to customer
                
                $emailTo = $customer['email'];
                $emailTitle = "Request transporter entry permit {$customer['name']} ";
                $emailTemplate = 'emails/basic';
                $emailData = [
                    'name' => $customer['name'],
                    'email' => $emailTo,
                    'content' => "
                        Transporter entry permit is request with no {$noRequest},
                        please wait for tep code from admin. <br>                         
                    "
                ];
                if(!empty($emailTo)){
                    $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
                }
                if(isset($tep_date)){
                    $data_whatsapp = "[REQUEST TEP] Transporter entry permit is request with no {$noRequest}, for date ".date('d F Y', strtotime($tep_date))." by {$customer['name']}, please wait for tep code from admin.";
                }else{
                    $data_whatsapp = "[REQUEST TEP] Transporter entry permit is request with no {$noRequest} by {$customer['name']}, please wait for tep code from admin.";
                }
                $whatsapp_group = $customer['whatsapp_group'];
                $whatsapp_group_internal = get_active_branch('whatsapp_group');
                $this->send_message_text($data_whatsapp,$whatsapp_group);
                $this->send_message_text($data_whatsapp,$whatsapp_group_internal);
                flash('success', "Request entry permit for {$customer['name']} successfully created", 'transporter-entry-permit/request');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->request();
    }

    /**
     * Save request transporter entry permit inbound
     */
    public function save_request_inbound()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_REQUEST);

        if ($this->validate([
            'slot' => 'trim|required|integer|is_natural_no_zero',
            'description' => 'trim|max_length[500]',
        ])) {
            $customerId = $this->input->post('customer');
            $slot = $this->input->post('slot');
            $description = $this->input->post('description');
            $booking = $this->input->post('booking');
            $uploadId = $this->input->post('aju');
            $doFiles = $this->input->post('memo');
            $category = $this->input->post('category');
            $tep_date = format_date($this->input->post('tep_date'),'Y-m-d');
            $counterHari = 0;
            // do {
            //     $counterHari++;
            //     $temp_tanggal = date('Y-m-d',strtotime('+'.$counterHari.' days',time()));
            //     $holiday=$this->scheduleHoliday->is_holiday($temp_tanggal);
            // } while (date('w',strtotime($temp_tanggal))==0 || $holiday);
            
            //batas waktu request jam 20:00, namun untuk h-2,h-3,... itu gak ada batasan waktu
            if($tep_date==date('Y-m-d') && (time() > strtotime('today 8 pm'))){
                flash('warning', "You can't request transporter entry permit because it has passed the request limit, which is 20:00", 'transporter-entry-permit/request');
            }

            $slotnow = $this->transporterEntryPermitRequest->getSlotRequest(['tep_date'=>$tep_date]);
            $slot_filled=0;

            foreach ($slotnow as $slotnow) {
                $slot_filled += $slotnow['slot'];
            }
            
            $slot_remain = get_active_branch('max_slot_tep')-$slot_filled;
            $slot_add = $this->transporterEntryPermitSlot->getSlot(['tep_date'=>$tep_date]);
            if (!empty($slot_add)) {
                $slot_remain+=$slot_add[0]['total_slot'];
            }
            if (($slot_remain-(int)$slot)<0) {
                flash('warning', "You can't request transporter entry permit because the slot has recently changed", 'transporter-entry-permit/request');
            }
            if (empty($booking)) {
                flash('warning', "You can't request transporter entry permit because you don't have booking active", 'transporter-entry-permit/request');
            }

            $this->db->trans_start();
            // add request entry permit
            
            $noRequest = $this->transporterEntryPermitRequest->getAutoNumberRequest();
            $this->transporterEntryPermitRequest->create([
                'id_customer'=>$customerId,
                'id_branch'=>get_active_branch('id'),
                // 'id_upload'=>$uploadId,
                'no_request'=>$noRequest,
                'slot'=>$slot,
                'category'=>$category,
                'description'=>$description,
                'tep_date'=>$tep_date
            ]);   
            $request_id = $this->db->insert_id();   
            foreach ($uploadId as $upload_id) {
                $this->transporterEntryPermitRequestUpload->create([
                    'id_request' => $request_id,
                    'id_upload' => $upload_id,
                ]);
            } 
            $this->transporterEntryPermitRequestStatusHistory->create([
                'id_request' => $request_id,
                'date' => $tep_date,
                'status' => 'PENDING',
                'description' => 'CREATE REQUEST',
            ]);
            
            foreach ($doFiles as $key => $doFile) {
                $sourceFile = 'temp/' . $doFile;
                $destFile = 'tep-request/' . format_date('now', 'Y/m/') . $doFile;
                if ($this->uploader->setDriver('s3')->move($sourceFile, $destFile)) {
                    $this->transporterEntryPermitRequestFile->create([
                        'id_tep_req' => $request_id,
                        'file' => $doFile,
                        'src' => $destFile,
                        'url' => $this->uploader->setDriver('s3')->getUrl($destFile),
                    ]);
                    $uploadedPaths[] = $this->uploader->setDriver('s3')->getUrl($destFile);
                }
            }
            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                // send email to customer
                $customer = $this->people->getById($customerId);
                
                $emailTo = $customer['email'];
                $emailTitle = "Request transporter entry permit {$customer['name']} ";
                $emailTemplate = 'emails/basic';
                $emailData = [
                    'name' => $customer['name'],
                    'email' => $emailTo,
                    'content' => "
                        Transporter entry permit is request with no {$noRequest},
                        please wait for tep code from admin. <br>                         
                    "
                ];
                if(!empty($emailTo)){
                    $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
                }
                $data_whatsapp = "[REQUEST TEP] Transporter entry permit is request with no {$noRequest}, for date ".date('d F Y', strtotime($tep_date))." by {$customer['name']}, please wait for tep code from admin.";
                $whatsapp_group = $customer['whatsapp_group'];
                $whatsapp_group_internal = get_active_branch('whatsapp_group');
                $this->send_message_text($data_whatsapp,$whatsapp_group);
                $this->send_message_text($data_whatsapp,$whatsapp_group_internal);
                flash('success', "Request entry permit for {$customer['name']} successfully created", 'transporter-entry-permit/request_inbound');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->request_inbound();
    }
    /**
     * Show form queue request transporter entry permit.
     */
    public function queue()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_REQUEST_VIEW);

        $filters = get_url_param('filter_queue') ? $_GET : [];
        if(isset($filters['date'])){
            $filters['date'] = date('Y-m-d',strtotime($filters['date']));
            $temp_tanggal = $filters['date']; 
        }else{
            $counterHari = 0;
            do {
                $counterHari++;
                $temp_tanggal = date('Y-m-d',strtotime('+'.$counterHari.' days',time()));
                $holiday=$this->scheduleHoliday->is_holiday($temp_tanggal);
            } while (date('w',strtotime($temp_tanggal))==0 || $holiday);
            $filters['date'] = $temp_tanggal;
        }
        $user = UserModel::authenticatedUserData();
        if(!isset($filters['aju']) && empty(trim($filters['q'] ?? ''))){
            $requests = $this->transporterEntryPermitRequest->getAll($filters);
        }else{
            if (empty($_GET['date'] ?? '') && (isset($filters['aju']) || !empty(trim($filters['q'] ?? '')))) {
                unset($filters['date']);
            }
            $requests = $this->transporterEntryPermitRequest->getAllData($filters);
        }
        $requestsIdUpload = $this->transporterEntryPermitRequestUpload->getAll();
        $filterIdUpload = array_unique(array_column($requestsIdUpload, 'id_upload'));
        $requests_ex = [];
        if($user['user_type'] == "EXTERNAL"){
            foreach ($requests as $key => &$request) {
                $request['no_queue'] = $key+1;
                if($user['id_person'] == $request['id_customer']){
                    $requests_ex[] = $request;
                }
            } 
            $requests = $requests_ex;
            $uploads = $this->upload->getBy([
                'uploads.id_person' => $user['id_person'],
                'ref_booking_types.category' => 'OUTBOUND',
                'uploads.id_branch' => get_active_branch('id'),
                'uploads.id' => $filterIdUpload,
            ]);
        }else{
            foreach ($requests as $key => &$request) {
                $request['no_queue'] = $key+1;
                $request['is_add_tep'] = 0;
                if($request['armada'] == "TCI" && $request['status'] == 'SET'){
                    $listGoods = $this->transporterEntryPermitRequestUpload->getByRequestId($request['id']);
                    foreach ($listGoods as $key => $goods) {
                        $stockGoods = $this->reportStock->getStockRequest([
                            'customer' => $request['id_customer'],
                            'data' => 'all',
                            'stock_outbound_job' => 'stock',
                            'id_upload' => $goods['id_upload'],
                            'ex_no_container' => $goods['ex_no_container'],
                            'id_goods' => $goods['id_goods'],
                            'id_unit' => $goods['id_unit'],
                        ]);
                        if( isset($stockGoods[0]) &&($goods['quantity'] - ($stockGoods[0]['work_order_quantity'] - $goods['work_order_quantity'])) > 0){ 
                            $request['is_add_tep'] = 1;
                            continue 2;
                        }
                    }
                }
            }
            $uploads = $this->upload->getBy([
                'ref_booking_types.category' => 'OUTBOUND',
                'uploads.id_branch' => get_active_branch('id'),
                'uploads.id' => $filterIdUpload,
            ]);
        }

        $min_time = $this->transporterEntryPermitRequest->getMinTime(['tep_date' => $temp_tanggal]);
        $min_time_today = $this->transporterEntryPermitRequest->getMinTime(['tep_date' => date('Y-m-d')]);
        if (!empty($min_time)) {
            $min_time['min_time'] = date('H:i', strtotime('+1 minutes', strtotime($min_time['min_time'])));
        } else {
            $min_time['min_time'] = "";
        }
        if (!empty($min_time_today)) {
            $min_time_today['min_time'] = date('H:i', strtotime('+1 minutes', strtotime($min_time_today['min_time'])));
        } else {
            $min_time_today['min_time'] = "";
        }

        if (!empty($requests)) {
            // get all item to saving connection resource
            $tepRequestUploads = $this->transporterEntryPermitRequestUpload->getBy([
                'id_request' => array_column($requests, 'id')
            ]);

            // fill items related request id
            foreach ($requests as &$request) {
                $selectedItems = array_filter($tepRequestUploads, function ($item) use ($request) {
                    return $item['id_request'] == $request['id'];
                });
                $request['tep_request_uploads'] = array_values($selectedItems);
            }
        }

        $this->render('transporter_entry_permit/queue', compact('requests','min_time', 'min_time_today', 'temp_tanggal', 'uploads'),'Request Transporter Entry Permit');
    }
    /**
     * Show form queue request transporter entry permit.
     */
    public function queue_tep()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_VIEW);

        $user = UserModel::authenticatedUserData();
        $filters = get_url_param('filter_queue_tep') ? $_GET : ['expired_date' => date('Y-m-d')];
        $filters['expired_date'] = date('Y-m-d',strtotime($filters['expired_date']));
        // $filters['tep_category'] = 'OUTBOUND';
        $filters['sort_by'] = '(transporter_entry_permit_requests.queue_time * -1)';
        if($user['user_type'] == "EXTERNAL"){
            $filters['id_customer'] = $user['id_person'];
        }
        $expired_date = $filters['expired_date']; 
        $teps = $this->transporterEntryPermit->getQueueTep($filters);
        foreach ($teps as $key => &$tep) {
            $requestIds = explode(',',$tep['id_tep_req_multi']);
            $tep['id_tep_req_multi'] = $requestIds;
        }
        // print_debug($teps);
        $datas = [
            'draw' => null,
            'recordsTotal' => count($teps),
            'recordsFiltered' => count($teps),
            'data' => $teps
        ];
        $this->render('transporter_entry_permit/queue_tep', compact('datas','expired_date'));
    }
    /**
     * Save transporter entry permit request
     */
    public function set_tep()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_CREATE);

        $category = 'OUTBOUND';
        $customerId = $this->input->post('id_customer');
        $requestId = $this->input->post('id');
        $queue_time = $this->input->post('queue_time');
        $description = $this->input->post('description');
        $id_upload = $this->input->post('id_upload');
        $tep_date = $this->input->post('tep_date');
        $tep_date = date('Y-m-d',strtotime($tep_date));
        $customerId = explode(",",$customerId);
        $customerId = array_unique($customerId);
        $id_request = explode(",",$requestId);
        $id_upload_array = explode(",",$id_upload);
        $id_upload_array = array_unique($id_upload_array);
        $serviceFiles = $this->input->post('service');
        $tepCategory = if_empty($this->input->post('category'), $category );
        $express_service_type = $this->input->post('express_service_type');
        $this->db->trans_start();
        
        $listGoods = $this->transporterEntryPermitRequestUpload->getByRequestId($id_request);
        // add entry permit
        $codes = [];
        $uploadedPaths = [];
        $expiredDate = date('Y-m-d 23:59:59', strtotime($tep_date));
        $tep_request=$this->transporterEntryPermitRequest->getById($id_request[0]);
        if($tep_request['tep_date'] != $tep_date && empty($serviceFiles)){
            flash('warning', 'You haven\'t uploaded the express service file, please upload it','transporter-entry-permit/queue');
        }
        $tepRequestUploads = $this->transporterEntryPermitRequestUpload->getBy(['id_request' => $tep_request['id']]);
        $noReferences = '[NO UPLOAD REFERENCE]';
        if (!empty($tepRequestUploads)) {
            $noReferences = implode(', ', array_map(function ($noRef) {
                return substr($noRef, -6, 6);
            }, array_column($tepRequestUploads, 'no_reference_upload')));
        }
        for ($i = 0; $i < $tep_request['slot']; $i++) {
            $code = $this->transporterEntryPermit->generateCode();
            $this->transporterEntryPermit->create([
                'tep_code' => $code,
                'tep_category' => $tepCategory,
                'expired_at' => $expiredDate,
                'description' => $description,
                'id_branch' => get_active_branch('id'),
            ]);
            $codes[] = ['code' => $code, 'expired_at' => $expiredDate];
            $tepId = $this->db->insert_id();
            foreach($id_request AS $requestId){
                $this->transporterEntryPermitRequest->update([
                    'queue_time'=>$queue_time,
                    'tep_date'=> $tep_request['tep_date'] != $tep_date ? $tep_date : $tep_request['tep_date'],
                    'status'=>'SET',
                    'express_service_type' => $express_service_type,
                    'set_by'=>UserModel::authenticatedUserData('id')
                ],$requestId); 
                $this->transporterEntryPermitRequestTep->create([
                    'id_request' => $requestId,
                    'id_tep' => $tepId
                ]);
                $this->transporterEntryPermitRequestStatusHistory->create([
                    'id_request' => $requestId,
                    'date' => $tep_request['tep_date'] != $tep_date ? $tep_date : $tep_request['tep_date'] ,
                    'status' => 'SET',
                    'description' => $tep_request['tep_date'] != $tep_date ? 'SET FOR TODAY' : 'SET REQUEST',
                ]);

                if($tep_request['tep_date'] != $tep_date){
                    //upload file express service in same day required
                    foreach ($serviceFiles as $key => $serviceFile) {
                        $sourceFile = 'temp/' . $serviceFile;
                        $destFile = 'tep-request/' . format_date('now', 'Y/m/') . $serviceFile;
                        if ($this->uploader->setDriver('s3')->move($sourceFile, $destFile)) {
                            $this->transporterEntryPermitRequestExpressServiceFile->create([
                                'id_tep_req' => $requestId,
                                'file' => $serviceFile,
                                'src' => $destFile,
                                'url' => $this->uploader->setDriver('s3')->getUrl($destFile),
                            ]);
                            $uploadedPaths[] = $this->uploader->setDriver('s3')->getUrl($destFile);
                        }
                    }
                }                
            }
            
            if($tepCategory == 'OUTBOUND'){
                foreach ($listGoods as $goods) {
                    $this->transporterEntryPermitUpload->create([
                        'id_tep' => $tepId,
                        'id_upload' => $goods['id_upload'],
                        'id_goods' => $goods['id_goods'],
                        'quantity' => $goods['quantity'],
                        'id_unit' => $goods['id_unit'],
                        'goods_name' => $goods['goods_name'],
                        'no_invoice' => $goods['no_invoice'],
                        'no_bl' => $goods['no_bl'],
                        'no_goods' => $goods['no_goods'],
                        'unit' => $goods['unit'],
                        'whey_number' => $goods['whey_number'],
                        'ex_no_container' => $goods['ex_no_container'],
                        'no_reference' => $goods['no_reference'],
                        'hold_status' => $goods['hold_status'],
                        'unload_location' => if_empty($goods['unload_location'], null),
                        'priority' => if_empty($goods['priority'], null),
                        'priority_description' => if_empty($goods['priority_description'], null),
                    ]);
                }
            }else{
                foreach ($id_upload_array as $id_upload) {
                    $this->transporterEntryPermitUpload->create([
                        'id_tep' => $tepId,
                        'id_upload' => $id_upload
                    ]);
                }
            }
            
            if(!empty($customerId)){
                foreach($customerId AS $customer_id){
                    $this->transporterEntryPermitCustomer->create([
                        'id_tep'=> $tepId,
                        'id_customer' => $customer_id,
                    ]);
                }
            }
        }
        
        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            // send email to customer
            $rows = '';
            $barcode = new DNS2D();
            $barcode->setStorPath(APPPATH . "cache/");
            $baseFolder = 'qr/' . date('Y/m');
            $this->uploader->makeFolder($baseFolder);
            $your_code = "";
            foreach ($codes as $index => &$code) {
                $no = $index + 1;
                $qrCode = $barcode->getBarcodePNG($code['code'], "QRCODE", 8, 8);
                $qrFileName = $baseFolder . '/' . Str::slug($code['code']) . '-' . uniqid() . '.jpg';
                base64_to_jpeg($qrCode, Uploader::UPLOAD_PATH . $qrFileName);
                $your_code .= $code['code'].", ";
                $rows .= "
                    <tr style='border-bottom: 1px solid #aaaaaa'>
                        <td align='center'>{$no}</td>
                        <th>{$code['code']}</th>
                        <td>{$code['expired_at']}</td>
                    </tr>
                    <tr style='border-bottom: 1px solid #aaaaaa'>
                        <td align='center' colspan='2'>QR Code</td>
                        <td><img src='" . base_url('uploads/' . $qrFileName) . "' alt='{$code['code']}'></td>
                    </tr>
                ";
                //add text in barcode
                list($orig_w, $orig_h) = getimagesize(Uploader::UPLOAD_PATH . $qrFileName);
                $orig_img = imagecreatefromstring(file_get_contents(Uploader::UPLOAD_PATH . $qrFileName));

                $output_w = 168;
                $output_h = 200;
                $scale = $output_w/$orig_w;

                // calc new image dimensions
                $new_h =  $orig_h * $scale;

                // create new image and fill with background colour
                $new_img = imagecreatetruecolor($output_w, $output_h);
                $bgcolor = imagecolorallocate($new_img,255, 255, 255); // white
                $textColor = imagecolorallocate($new_img, 0, 0, 0); // white
                imagefill($new_img, 0, 0, $bgcolor); // fill background colour
                // copy and resize original image into center of new image
                imagecopyresampled($new_img, $orig_img, 0, 0, 0, 0, $output_w, $new_h, $orig_w, $orig_h);
                $new_filename = Uploader::UPLOAD_PATH . $qrFileName;
                imagettftext($new_img, 18, 0, 22 , 195 , $textColor, FCPATH . 'assets/plugins/font-googleapis/fonts/SourceSansPro-Bold.ttf', $code['code']);
                //save it
                imagejpeg($new_img, $new_filename, 80);
                $code['qr_code'] = base_url('uploads/' . $qrFileName);
            }

            $ajuCustomer = [];
            $reqCustomer = [];
            $text_no_req = '';
            $text_customer = '';
            foreach($customerId AS $customer_id){
                $customer = $this->people->getById($customer_id);
                $text_customer .= $customer['name'].", ";
            }
            $mergeNoReferences = '';
            foreach ($id_request as $id_req) {
                $tep_request_new=$this->transporterEntryPermitRequest->getById($id_req);                    
                $text_no_req .= $tep_request_new['no_request'].", ";
                $tepRequestMergeUploads = $this->transporterEntryPermitRequestUpload->getBy(['id_request' => $id_req]);
                if (!empty($tepRequestMergeUploads)) {
                    $mergeNoReferences .= (empty($mergeNoReferences) ? '' : ', ') . implode(', ', array_map(function ($noRef) {
                            return substr($noRef, -6, 6);
                        }, array_unique(array_column($tepRequestMergeUploads, 'no_reference_upload'))));
                    //buat misahin aju kepemilikin customer mana
                    foreach($customerId AS $customer_id){
                        if($tep_request_new['id_customer'] == $customer_id){
                            $ajuCustomer[$customer_id] = implode(', ', array_map(function ($noRef) {
                                return substr($noRef, -6, 6);
                            }, array_unique(array_column($tepRequestMergeUploads, 'no_reference_upload'))));
                            if(isset($reqCustomer[$customer_id])){
                                $reqCustomer[$customer_id] .= ', '.$tep_request_new['no_request'];
                            }else{
                                $reqCustomer[$customer_id] = $tep_request_new['no_request'];
                            }
                        }
                    }    
                }
            }
            if(count($id_request)>1){
                $data_whatsapp_merge = "[MERGE REQUEST] Hello, {$text_customer} Your request has been merged with no {$text_no_req} contain all reference {$mergeNoReferences}";
                $whatsapp_group_internal = get_active_branch('whatsapp_group');
                $this->send_message_text($data_whatsapp_merge,$whatsapp_group_internal);
            }
            foreach($customerId AS $customer_id){
                $customer = $this->people->getById($customer_id);
                $emailTo = $customer['email'];
                $emailTitle = "Transporter entry permit {$customer['name']} for {$customer['name']} with " . count($codes) . " total codes";
                $emailTemplate = 'emails/basic';
                $emailData = [
                    'name' => $customer['name'],
                    'email' => $emailTo,
                    'content' => "
                        Transporter entry permit is queue at <b>{$queue_time}</b> ( ".date('Y-m-d', strtotime($expiredDate))." ) and will be expired at <b>{$expiredDate}</b>,
                        please deliver this information (the code) to your transporter vendor or driver. <br> 
                        (THE CODES ARE NEEDED WHEN VISIT AND PICK UP FOR INBOUND / OUTBOUND ACTIVITY) .
                        <br><br>
                        <table style='width:100%; font-size: 14px; text-align: left; border-collapse: collapse; border:1px solid #aaaaaa' cellpadding='10'>
                            <tr style='border-bottom: 1px solid #aaaaaa'>
                                <th align='center'>No</th>
                                <th>Code</th>
                                <th>Expired At</th>
                            </tr>
                            {$rows}
                        </table>
                    "
                ];
                if(!empty($emailTo)){
                    $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
                }
                if(!empty($serviceFiles) && $tep_request['tep_date'] != $tep_date && $express_service_type == 'CUSTOMER'){
                    $attachments = [];
                    foreach ($uploadedPaths as $uploadedPath) {
                        $attachments[] = [
                            'source' => $uploadedPath,
                        ];
                    }
    
                    $emailOptions = [
                        'cc' => ['opr_div2@transcon-indonesia.com', 'opr_sbyspv1@transcon-id.com', 'compliance_mgr@transcon-indonesia.com', 'sales_support@transcon-indonesia.com'],
                        'attachment' => $attachments,
                        'subject_title' => true,
                    ];
    
                    $emailTo = 'acc_mgr@transcon-indonesia.com,findata@transcon-id.com,fin2@transcon-indonesia.com';
                    $emailTitle = "EXPRESS SERVICE CONFIRMATION ".$customer['name']." ".format_date($tep_date,"d/m/Y");
                    $emailTemplate = 'emails/basic';
                    $emailData = [
                        'title' => 'Express Service TEP',
                        'name' => 'Transcon Indonesia',
                        'email' => $emailTo,
                        'content' => 'The customer '.$customer['name'].' with no reference '.$noReferences.' and TEP code '.$your_code.' has confirmed the express service fee. The following is the attached file',
                    ];
                    
                    $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
                }
                $data_whatsapp = "[TEP] Hello, {$customer['name']}, Your code {$your_code} with request ".$reqCustomer[$customer_id]." is used for reference ".$ajuCustomer[$customer_id].". The Transporter Entry Permit is queue at {$queue_time} ( ".date('d F Y', strtotime($expiredDate))." ), please deliver this information (the code) to your transporter vendor or driver.";
                $whatsapp_group = $customer['whatsapp_group'];
                
                if(count($id_request)>1){
                    $this->send_message_text($data_whatsapp_merge,$whatsapp_group);
                }
                $this->send_message_text($data_whatsapp,$whatsapp_group);
                // send qr code image
                foreach ($codes as $index => $codenya) {
                    $this->send_message_text("[TEP] Hello, {$customer['name']}, Your code {$codenya['code']} with request ".$reqCustomer[$customer_id]."  is used for reference {$noReferences}. the Transporter Entry Permit is queue at {$queue_time} ( ".date('d F Y', strtotime($expiredDate))." )", $whatsapp_group, $codenya['qr_code']);
                }
            }
            flash('success', "Entry permit for {$customer['name']} successfully created", 'transporter-entry-permit/queue');
        } else {
            flash('danger', 'Something is getting wrong, try again or contact administrator');
        }
        
        $this->queue();
    }

    /**
     * Update set tep.
     *
     * @param $requestId
     */
    public function update_set_tep($requestId)
    {
        $tepDate = format_date($this->input->post('tep_date'));
        $queueTime = $this->input->post('queue_time');
        $description = $this->input->post('description');
        $serviceFiles = $this->input->post('service');
        $expiredDate = date('Y-m-d 23:59:59', strtotime($tepDate));
        $tepRequest = $this->transporterEntryPermitRequest->getById($requestId);
        $express_service_type = $this->input->post('express_service_type');
        $anotherReqs = $this->transporterEntryPermitRequestTep->getAnotherReqByReq($requestId);
        
        if($tepRequest['tep_date'] != $tepDate && empty($serviceFiles)){
            flash('warning', 'You haven\'t uploaded the express service file, please upload it','transporter-entry-permit/queue');
        }

        $this->db->trans_start();
        $tepRequestUploads = $this->transporterEntryPermitRequestUpload->getBy(['id_request' => $requestId]);
        $noReferences = '[NO UPLOAD REFERENCE]';
        if (!empty($tepRequestUploads)) {
            $noReferences = implode(', ', array_map(function ($noRef) {
                return substr($noRef, -6, 6);
            }, array_column($tepRequestUploads, 'no_reference_upload')));
        }

        // $anotherReqs will be more than 1 if request is merged before
        foreach ($anotherReqs as $key => $anotherReq) {
            $this->transporterEntryPermitRequest->update([
                'queue_time' => $queueTime,
                'tep_date' => $tepDate,
                'status' => 'SET',
                'set_by' => UserModel::authenticatedUserData('id')
            ], $anotherReq['id_request']);
    
            $this->transporterEntryPermitRequestStatusHistory->create([
                'id_request' => $anotherReq['id_request'],
                'date' => $tepDate,
                'status' => 'SET',
                'description' => ($tepRequest['tep_date'] != $tepDate ? 'SET FOR TODAY' : 'SET REQUEST') . ' (UPDATED)',
            ]);
        }        

        $tepReferences = $this->transporterEntryPermitRequestTep->getBy(['id_request' => $requestId]);
        foreach ($tepReferences as $tepItem) {
            $tep = $this->transporterEntryPermit->getById($tepItem['id_tep']);
            $this->transporterEntryPermit->update([
                'expired_at' => $expiredDate,
                'description' => if_empty($tep['description'], '', '', ' ') . if_empty($description, '', '(Updated: ', ')'),
            ], $tepItem['id_tep']);
        }

        if($tepRequest['tep_date'] != $tepDate){
            //upload file express service in same day required
            foreach ($serviceFiles as $key => $serviceFile) {
                $sourceFile = 'temp/' . $serviceFile;
                $destFile = 'tep-request/' . format_date('now', 'Y/m/') . $serviceFile;
                if ($this->uploader->setDriver('s3')->move($sourceFile, $destFile)) {
                    $this->transporterEntryPermitRequestExpressServiceFile->create([
                        'id_tep_req' => $requestId,
                        'file' => $serviceFile,
                        'src' => $destFile,
                        'url' => $this->uploader->setDriver('s3')->getUrl($destFile),
                    ]);
                    $uploadedPaths[] = $this->uploader->setDriver('s3')->getUrl($destFile);
                }
            }
        }  

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            if ($tepRequest['tep_date'] != $tepDate || $tepRequest['queue_time'] != $queueTime) {
                foreach ($tepReferences as $tepItem) {
                    $tep = $this->transporterEntryPermit->getById($tepItem['id_tep']);

                    $tepCustomers = $this->transporterEntryPermitCustomer->getBy([
                        'id_tep'=> $tepItem['id_tep']
                    ]);
                    foreach ($tepCustomers as $tepCustomer) {
                        $customer = $this->people->getById($tepCustomer['id_customer']);
                        $data_whatsapp = "[TEP DATE UPDATE] Hello, {$customer['name']}, Your code {$tep['tep_code']} is updated and queued at {$queueTime} ( " . date('d F Y', strtotime($expiredDate)) . " ).";
                        $whatsapp_group = $customer['whatsapp_group'];
                        $this->send_message_text($data_whatsapp, $whatsapp_group);

                        //email express service
                        if(!empty($serviceFiles) && $tepRequest['tep_date'] != $tepDate && $express_service_type == 'CUSTOMER'){
                            $attachments = [];
                            foreach ($uploadedPaths as $uploadedPath) {
                                $attachments[] = [
                                    'source' => $uploadedPath,
                                ];
                            }
            
                            $emailOptions = [
                                'cc' => ['opr_div2@transcon-indonesia.com', 'opr_sbyspv1@transcon-id.com', 'compliance_mgr@transcon-indonesia.com', 'sales_support@transcon-indonesia.com'],
                                'attachment' => $attachments,
                                'subject_title' => true,
                            ];
            
                            $emailTo = 'acc_mgr@transcon-indonesia.com,findata@transcon-id.com,fin2@transcon-indonesia.com';
                            $emailTitle = "EXPRESS SERVICE CONFIRMATION ".$customer['name']." ".format_date($tepDate,"d/m/Y");
                            $emailTemplate = 'emails/basic';
                            $emailData = [
                                'title' => 'Express Service TEP',
                                'name' => 'Transcon Indonesia',
                                'email' => $emailTo,
                                'content' => 'The customer '.$customer['name'].' with no reference '.$noReferences.' has confirmed the express service fee. The following is the attached file',
                            ];
                            
                            $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
                        }
                    }
                }
            }

            flash('success', "Entry permit request and tep successfully updated");
        } else {
            flash('danger', 'Something is getting wrong, try again or contact administrator');
        }
        redirect('transporter-entry-permit/queue');
    }

    /**
     * add merge request, for join tep after created before
     */
    public function add_merge()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_SLOT);
        $slot = $this->input->post('slot');
        $date = $this->input->post('date');
        $date = date('Y-m-d',strtotime($date));
        // print_debug('stop');
        $this->db->trans_start();
        $this->transporterEntryPermitSlot->create([
            'slot' => $slot,
            'date' => $date,
            'id_branch' => get_active_branch('id'),
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            flash('success', "Add Slot successfully created", 'transporter-entry-permit/queue');
        }else{
            flash('danger', "Add Slot fail created", 'transporter-entry-permit/queue');
        }
    }

    /**
     * add slot request
     */
    public function add_slot()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_SLOT);
        $slot = $this->input->post('slot');
        $date = $this->input->post('date');
        $date = date('Y-m-d',strtotime($date));
        $this->db->trans_start();
        $this->transporterEntryPermitSlot->create([
            'slot' => $slot,
            'date' => $date,
            'id_branch' => get_active_branch('id'),
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            flash('success', "Add Slot successfully created", 'transporter-entry-permit/queue');
        }else{
            flash('danger', "Add Slot fail created", 'transporter-entry-permit/queue');
        }
    }
    /**
     * add slot request
     */
    public function set_skip()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_CREATE);
        $id_request = $this->input->post('id');
        $skip_reason = $this->input->post('skip_reason');
        $this->db->trans_start();
        
        $request=$this->transporterEntryPermitRequest->getByRequestId($id_request);
        $this->transporterEntryPermitRequest->update([
            'status'=>'SKIP',
            'tep_date' => empty($request['tep_date']) ? date('Y-m-d') : $request['tep_date'] ,
            'skip_reason' => $skip_reason,
            'set_by'=>UserModel::authenticatedUserData('id')
        ],$id_request); 

        $this->transporterEntryPermitRequestStatusHistory->create([
            'id_request' => $id_request,
            'date' => empty($request['tep_date']) ? NULL : $request['tep_date'] ,
            'status' => 'SKIP',
            'description' => 'SKIP REQUEST',
        ]);
        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $tep_request=$this->transporterEntryPermitRequest->getById($id_request);
            $customer = $this->people->getById($tep_request['id_customer']);
            $data_whatsapp = "[SKIP REQUEST] Hallo, {$customer['name']} your request has been skip with reason \"".$skip_reason."\"";
            $whatsapp_group = $customer['whatsapp_group'];
            $whatsapp_group_internal = get_active_branch('whatsapp_group');
            $this->send_message_text($data_whatsapp,$whatsapp_group);
            $this->send_message_text($data_whatsapp,$whatsapp_group_internal);
            flash('success', "Skip request successfully", 'transporter-entry-permit/queue');
        }else{
            flash('danger', "Skip request fail created", 'transporter-entry-permit/queue');
        }
    }
    /**
     * Send a message to a new or existing chat.
     * 6281333377368-1557128212@g.us
     */
    public function send_message_text($text,$whatsapp_group, $image = null)
    {
        $data = [
            'url' => empty($image) ? 'sendMessage' : 'sendFile',
            'method' => 'POST',
            'payload' => [
                'chatId' => detect_chat_id($whatsapp_group),
                'body' => empty($image) ? $text : $image,
            ]
        ];
        if (!empty($image)) {
            $data['payload']['filename'] = basename($image);
            $data['payload']['caption'] = $text;
        }

        $result = $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);
    }
    /**
     * Show form create transporter entry permit.
     */
    public function create_outbound()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_CREATE_OUTBOUND);

        $this->render('transporter_entry_permit/create_outbound');
    }
    /**
     * Show form create transporter entry permit request.
     */
    public function create_tep_request($requestId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_CREATE_OUTBOUND);

        $listRequests = [];
        $requests = $this->transporterEntryPermitRequest->getReqTCI();
        foreach ($requests as $key => $request) {
            if($request['status'] == 'SET' && $request['tep_date'] >= date('Y-m-d',strtotime('-14 day'))){
                $listRequests[] = $request;
                //lama
                // $listGoods = $this->transporterEntryPermitRequestUpload->getByRequestId($request['id']);
                // //biar ringan
                // if(count($listGoods)>4){
                //     $listRequests[] = $request;
                //     continue;
                // }
                // //ini berat
                // foreach ($listGoods as $key => $goods) {
                //     $stockGoods = $this->reportStock->getStockRequest([
                //         'customer' => $request['id_customer'],
                //         'data' => 'all',
                //         'id_upload' => $goods['id_upload'],
                //         'ex_no_container' => $goods['ex_no_container'],
                //         'id_goods' => $goods['id_goods'],
                //         'id_unit' => $goods['id_unit'],
                //     ]);
                //     if( isset($stockGoods[0]) &&($goods['quantity'] - ($stockGoods[0]['work_order_quantity'] - $goods['work_order_quantity'])) > 0){ 
                //         $listRequests[] = $request;
                //         continue 2;
                //     }
                // }
            }else if($request['status'] == 'PENDING'){
                $listRequests[] = $request;
            }
        }
        $request = $this->transporterEntryPermitRequest->getById($requestId);
        $listGoods = $this->transporterEntryPermitRequestUpload->getByRequestId($requestId);
        
        $holidayDate=$this->scheduleHoliday->getBy(["schedule_holidays.date>='".date('Y-m-d')."'" => null]);
        $holidayDate = array_column($holidayDate,'date');
        $holidayDate = implode(',',$holidayDate);
        // print_debug($holidayDate);
        $this->render('transporter_entry_permit/create_tep_request', compact('listRequests', 'listGoods', 'request', 'holidayDate'));
    }
    /**
     * Show edit job in general mode.
     *
     * @param $workOrderId
     */
    public function edit_tep($tepId)
    {
        $teps = $this->transporterEntryPermit->getById($tepId);
        if(AuthorizationModel::isAuthorized(PERMISSION_TEP_EDIT_SECURITY) || ($teps['can_edit']==1 && $teps['checked_in_by']==UserModel::authenticatedUserData('id')) && empty($teps['checked_out_by'])){
            $eseals = $this->eseal->getAll();
            $e_seal = $this->eseal->getById($teps['id_eseal']);
            $containers = $this->transporterEntryPermitContainer->getBy(
                [
                    'transporter_entry_permit_containers.id_tep'=> $tepId,
                ]
            );
            $tepChassis = $this->transporterEntryPermitChassis->getBy([
                'transporter_entry_permit_chassis.id_tep' => $tepId
            ], true);
            $this->render('transporter_entry_permit/edit', compact('teps', 'tepChassis', 'eseals', 'e_seal', 'containers'), 'Edit Transporter Entry Permit ');
        }else{
            flash('warning', "You don't have permission");
            redirect('transporter_entry_permit');
        }
    }

    public function update_tep($tepId)
    {        
        $tep1 = $this->transporterEntryPermit->getById($tepId);
        if(AuthorizationModel::isAuthorized(PERMISSION_TEP_EDIT_SECURITY) || ($tep1['can_edit']==1 && $tep1['checked_in_by']==UserModel::authenticatedUserData('id')) && empty($tep1['checked_out_by'])){
            if ($this->validate(['receiver_name' => 'trim|required|max_length[50]'])) {
                $receiverName = $this->input->post('receiver_name');
                $receiverContact = $this->input->post('receiver_contact');
                $receiverEmail = $this->input->post('receiver_email');
                $receiverVehicle = $this->input->post('receiver_vehicle');
                $receiverNoPolice = $this->input->post('receiver_no_police');
                $eseal = $this->input->post('eseal');
                $containers = $this->input->post('containers');
                $tepChassisId = $this->input->post('id_tep_chassis');
                $noChassis = $this->input->post('no_chassis');
                $clean_plat = preg_replace('/[^a-zA-Z0-9]/', '', $receiverNoPolice);
                $vehicle_in = $this->transporterEntryPermit->getBy([
                    "transporter_entry_permits.receiver_no_police is not NULL"=>NULL,
                    "transporter_entry_permits.checked_out_at is NULL"=>NULL,
                    'transporter_entry_permits.checked_in_at >='=> "2019-11-23 00:00:00",
                ]);

                $canCheckIn = true;
                foreach ($vehicle_in as $vehicle) {
                    $clean_vehicle = preg_replace('/[^a-zA-Z0-9]/', '', $vehicle['receiver_no_police']); 
                    if (strtolower(trim($clean_plat))==strtolower(trim($clean_vehicle))) {
                        $canCheckIn = false;
                    }
                }  
                $tep1 = $this->transporterEntryPermit->getById($tepId);
                if (strtolower(trim($clean_plat))==strtolower(trim($tep1['receiver_no_police']))) {
                    $canCheckIn = true;
                }
                if($canCheckIn){
                    $this->db->trans_start();
                    $this->transporterEntryPermit->update([
                        'id_eseal' => $eseal,
                        'receiver_name' => $receiverName,
                        'receiver_contact' => $receiverContact,
                        'receiver_email' => $receiverEmail,
                        'receiver_vehicle' => $receiverVehicle,
                        'receiver_no_police' => $receiverNoPolice,
                    ], $tepId);

                    if (!empty($tepChassisId)) {
                        $this->transporterEntryPermitChassis->update([
                            'no_chassis' => $noChassis
                        ], $tepChassisId);
                    }
        
                    // update TEP Container Or Goods
                    if (!empty($containers)) {
                        $this->transporterEntryPermitContainer->deleteTepContainerByTep($tepId);
                        foreach ($containers as $container) {
        
                            $id_position = !is_null($container['id_position']) && $container['id_position'] != '' && $container['id_position'] != 'null' ? $container['id_position'] : null;
                            $this->transporterEntryPermitContainer->createTepContainer([
                                'id_tep' => $tepId,
                                'id_booking_container' => $container['id_reference'],
                                'id_container' => $container['id_container'],
                                'id_position' => $id_position,
                                'seal' => if_empty($container['seal'], null),
                                'is_empty' => $container['is_empty'],
                                'is_hold' => $container['is_hold'],
                                'status' => $container['status'],
                                'status_danger' => $container['status_danger'],
                                'description' => $container['description'],
                                'quantity' => 1
                            ]);
                        }
                    }
                    $this->transporterEntryPermitHistory->create([
                        'id_tep' => $tepId,
                        'data' => json_encode($tep1),
                    ]);
                    $this->db->trans_complete();
                    if($this->db->trans_status()){
                        if(!AuthorizationModel::hasRole(ROLE_SECURITY)){
                            flash('success', "Edit entry permit <strong>{$tep1['tep_code']} - {$receiverNoPolice}</strong> successfully");
                            redirect('transporter-entry-permit');
                        }else{
                            flash('success', "Edit entry permit <strong>{$tep1['tep_code']} - {$receiverNoPolice}</strong> successfully");
                            redirect('dashboard/transporter-in');
                        }
                    }
                } else {
                    flash('danger', "Edit entry permit <strong>{$tep1['tep_code']} - {$receiverNoPolice}</strong> failed, because plate number hasn't checked out yet");
                    redirect('transporter-entry-permit/edit-tep/'.$tepId);
                }   
            }
        }else{
            if(!AuthorizationModel::hasRole(ROLE_SECURITY)){
                flash('warning', "You don't have permission");
                redirect('transporter_entry_permit');
            }else{
                flash('warning', "You don't have permission");
                redirect('dashboard/transporter-in');
            }
        }
    }
    /**
     * Get ajax tep queue.
     */
    public function ajax_get_tep_queue()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $slot = $this->input->get('slot');
            $armada = $this->input->get('armada');
            $category = $this->input->get('category');
            $id_request = $this->input->get('id_request');

            // $filters = get_url_param('filter_queue_tep') ? $_GET : ['expired_date>=' => date('Y-m-d')];
            // $filters['expired_date'] = date('Y-m-d',strtotime($filters['expired_date']));
            $filters['tep_category'] = $category;
            $filters['sort_by'] = '(transporter_entry_permit_requests.queue_time * -1)';
            $filters['not_expired'] = date('Y-m-d');
            $filters['has_queue'] = true;
            $filters['not_checkout'] = true;
            $filters['armada'] = $armada;
            $filters['id_request'] = $id_request;
            $teps = $this->transporterEntryPermit->getQueueTep($filters);
            
            header('Content-Type: application/json');
            echo json_encode([
                'teps' => $teps,
            ]);
        }
    }

    /**
     * Get ajax request by id.
     */
    public function ajax_get_request_by_id()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $id = $this->input->get('id');

            $request = $this->transporterEntryPermitRequest->getByRequestId($id);
            
            header('Content-Type: application/json');
            echo json_encode([
                'request' => $request,
            ]);
        }
    }

    /**
     * Get ajax merge request.
     */
    public function ajax_set_merge_request()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $id_request = $this->input->post('id_request');
            $id_customer = $this->input->post('id_customer');
            $id_upload = $this->input->post('id_upload');
            $id_tep = $this->input->post('id_tep');
            $armada = $this->input->post('armada');
            $id_upload_array = explode(",",$id_upload);
            $listGoods = $this->transporterEntryPermitRequestUpload->getByRequestId($id_request);
            
            //untuk mencari id_request yang memilikit tep yang dimerge
            $merge_request=$this->transporterEntryPermitRequest->getRequestByTep($id_tep);
            $merge_request = array_column($merge_request,'id');
            array_push($merge_request,$id_request);
            
            $this->db->trans_start();
            $tep=$this->transporterEntryPermit->getById($id_tep);
            $request=$this->transporterEntryPermitRequest->getByRequestId($id_request);
            
            if($armada == 'CUSTOMER'){
                if($request['slot']==($request['slot_created']+1)){
                    $this->transporterEntryPermitRequest->update([
                        'queue_time'=>$tep['queue_time'],
                        'tep_date'=> date('Y-m-d',strtotime($tep['expired_at'])),
                        'status'=>'SET',
                        'set_by'=>UserModel::authenticatedUserData('id')
                    ],$id_request); 
                }
            }else{
                $this->transporterEntryPermitRequest->update([
                    'slot' => $request['count_tep_code']+1,
                    'queue_time'=>$tep['queue_time'],
                    'tep_date'=> date('Y-m-d',strtotime($tep['expired_at'])),
                    'status'=>'SET',
                    'set_by'=>UserModel::authenticatedUserData('id')
                ],$id_request); 
            }

            $this->transporterEntryPermitRequestTep->create([
                'id_request' => $id_request,
                'id_tep' => $id_tep
            ]);
            $this->transporterEntryPermitRequestStatusHistory->create([
                'id_request' => $id_request,
                'date' => date('Y-m-d',strtotime($tep['expired_at'])),
                'status' => 'SET',
                'description' => 'MERGE WITH TEP QUEUE',
            ]);
            foreach ($listGoods as $goods) {
                $this->transporterEntryPermitUpload->create([
                    'id_tep' => $id_tep,
                    'id_upload' => $goods['id_upload'],
                    'id_goods' => $goods['id_goods'],
                    'quantity' => $goods['quantity'],
                    'id_unit' => $goods['id_unit'],
                    'goods_name' => $goods['goods_name'],
                    'no_invoice' => $goods['no_invoice'],
                    'no_bl' => $goods['no_bl'],
                    'no_goods' => $goods['no_goods'],
                    'unit' => $goods['unit'],
                    'whey_number' => $goods['whey_number'],
                    'ex_no_container' => $goods['ex_no_container'],
                    'no_reference' => $goods['no_reference'],
                    'hold_status' => $goods['hold_status'],
                    'unload_location' => if_empty($goods['unload_location'], null),
                    'priority' => if_empty($goods['priority'], null),
                    'priority_description' => if_empty($goods['priority_description'], null),
                ]);
            }
            $cekCustomer = $this->transporterEntryPermitCustomer->getBy([
                'transporter_entry_permit_customers.id_tep' => $id_tep,
                'transporter_entry_permit_customers.id_customer' => $id_customer,]);
            if(empty($cekCustomer)){
                $this->transporterEntryPermitCustomer->create([
                    'id_tep'=> $id_tep,
                    'id_customer' => $id_customer,
                    ]);
            }
        }
        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            // send email to customer
            $rows = '';
            $barcode = new DNS2D();
            $barcode->setStorPath(APPPATH . "cache/");
            $baseFolder = 'qr/' . date('Y/m');
            $this->uploader->makeFolder($baseFolder);
            $your_code = "";
            
            $codes[] = ['code' => $tep['tep_code'], 'expired_at' => $tep['expired_at']];
            foreach ($codes as $index => $code) {
                $no = $index + 1;
                $qrCode = $barcode->getBarcodePNG($code['code'], "QRCODE", 8, 8);
                $qrFileName = $baseFolder . '/' . Str::slug($code['code']) . '-' . uniqid() . '.jpg';
                base64_to_jpeg($qrCode, Uploader::UPLOAD_PATH . $qrFileName);
                $your_code .= $code['code'].", ";
                $rows .= "
                    <tr style='border-bottom: 1px solid #aaaaaa'>
                        <td align='center'>{$no}</td>
                        <th>{$code['code']}</th>
                        <td>{$code['expired_at']}</td>
                    </tr>
                    <tr style='border-bottom: 1px solid #aaaaaa'>
                        <td align='center' colspan='2'>QR Code</td>
                        <td><img src='" . base_url('uploads/' . $qrFileName) . "' alt='{$code['code']}'></td>
                    </tr>
                ";
            }
            
            $text_no_req = '';
            $text_customer = '';
            $customer = $this->people->getById($id_customer);
            $text_customer = $customer['name'];

            $mergeNoReferences = '';
            foreach ($merge_request as $id_req) {
                $tep_request=$this->transporterEntryPermitRequest->getById($id_req);
                $text_no_req .= $tep_request['no_request'].", ";
                $tepRequestMergeUploads = $this->transporterEntryPermitRequestUpload->getBy(['id_request' => $id_req]);
                if (!empty($tepRequestMergeUploads)) {
                    $mergeNoReferences .= (empty($mergeNoReferences) ? '' : ', ') . implode(', ', array_map(function ($noRef) {
                            return substr($noRef, -6, 6);
                        }, array_unique(array_column($tepRequestMergeUploads, 'no_reference_upload'))));
                }
            }
            
            $data_whatsapp_merge = "[MERGE REQUEST] Hello, {$text_customer} Your request has been merged with TEP Code {$tep['tep_code']} contain all reference {$mergeNoReferences}";
            $whatsapp_group_internal = get_active_branch('whatsapp_group');
            $this->send_message_text($data_whatsapp_merge,$whatsapp_group_internal);
        
            $emailTo = $customer['email'];
            $emailTitle = "Transporter entry permit {$customer['name']} for {$customer['name']} with " . count($codes) . " total codes";
            $emailTemplate = 'emails/basic';
            $emailData = [
                'name' => $customer['name'],
                'email' => $emailTo,
                'content' => "
                    Transporter entry permit is queue at <b>{$tep['queue_time']}</b> ( ".date('Y-m-d', strtotime($tep['expired_at']))." ) and will be expired at <b>{$tep['expired_at']}</b>,
                    please deliver this information (the code) to your transporter vendor or driver. <br> 
                    (THE CODES ARE NEEDED WHEN VISIT AND PICK UP FOR INBOUND / OUTBOUND ACTIVITY) .
                    <br><br>
                    <table style='width:100%; font-size: 14px; text-align: left; border-collapse: collapse; border:1px solid #aaaaaa' cellpadding='10'>
                        <tr style='border-bottom: 1px solid #aaaaaa'>
                            <th align='center'>No</th>
                            <th>Code</th>
                            <th>Expired At</th>
                        </tr>
                        {$rows}
                    </table>
                "
            ];
            if(!empty($emailTo)){
                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
            }
            
            $whatsapp_group = $customer['whatsapp_group'];
            $this->send_message_text($data_whatsapp_merge,$whatsapp_group);
        }
      
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $this->db->trans_status(),
        ]);
    }

    /**
     * Get photo files.
     */
    public function ajax_get_tep_req_files()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $tepReqId = $this->input->get('id_tep_req');
            $files = $this->transporterEntryPermitRequestFile->getFilesByTepReq($tepReqId);
            header('Content-Type: application/json');
            echo json_encode($files);
        }
    }

    /**
     * Get express service files.
     */
    public function ajax_get_tep_req_express_files()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $tepReqId = $this->input->get('id_tep_req');
            $files = $this->transporterEntryPermitRequestExpressServiceFile->getFilesByTepReq($tepReqId);
            header('Content-Type: application/json');
            echo json_encode($files);
        }
    }

    /**
     * Store upload data.
     */
    public function upload_s3()
    {
        $result = [];
        foreach ($_FILES as $file => $data) {
            $fileTitle = url_title(pathinfo($data['name'], PATHINFO_FILENAME));
            $extension = pathinfo($data['name'], PATHINFO_EXTENSION);
            $fileName = uniqid() . '_' . $fileTitle . '.' . $extension;
            $upload = $this->uploader->setDriver('s3')->uploadTo($file, ['file_name' => $fileName, 'size' => 5000]);
            $result[$file] = [
                'status' => $upload,
                'errors' => $this->uploader->getDisplayErrors(),
                'data' => $this->uploader->getUploadedData()
            ];
        }
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    /**
     * Download file.
     */
    public function download_file()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_CREATE);
        $tepReqId = $this->input->post('id_tep_req');
        $tepReq = $this->transporterEntryPermitRequest->getById($tepReqId);

        $files = $this->transporterEntryPermitRequestFile->getFilesByTepReq($tepReqId);
        foreach ($files as $value) {
            $data = file_get_contents($value['url']);
            $newDir = 'DO or Memo/' . urlencode($value['file']);
            $this->zip->add_data($newDir, $data);
        }

        $this->zip->download($tepReq['no_request'] . '.zip');
    }
    /**
     * Download express service file.
     */
    public function download_express_file()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_CREATE);
        $tepReqId = $this->input->post('id_tep_req');
        $tepReq = $this->transporterEntryPermitRequest->getById($tepReqId);

        $files = $this->transporterEntryPermitRequestExpressServiceFile->getFilesByTepReq($tepReqId);
        foreach ($files as $value) {
            $data = file_get_contents($value['url']);
            $newDir = 'Express Service/' . urlencode($value['file']);
            $this->zip->add_data($newDir, $data);
        }

        $this->zip->download($tepReq['no_request'] . '.zip');
    }

    /**
     * Get stock by customer.
     */
    public function ajax_get_stock_by_customer()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $customerId = $this->input->get('id_customer');

            $stockOutboudGoods = $this->reportStock->getStockRequest([
                'customer' => $customerId,
                'data' => 'stock'
            ]);

            //filter with stock summary
            $stockSummary = $this->reportModel->getReportSummaryGoodsExternal([
                'owner' => $customerId,
                'data' => 'stock'
            ]);
            $stockGoods = [];
            foreach ($stockOutboudGoods as $key => $stock) {
                foreach ($stockSummary as $key => $summary) {
                    if($summary['no_reference'] == $stock['no_reference_inbound'] 
                        && $summary['id_goods'] == $stock['id_goods']
                        && $summary['unit'] == $stock['unit']
                        && $summary['invoice_number'] == $stock['no_invoice']
                        && $summary['ex_no_container'] == $stock['ex_no_container']){
                        $stockGoods[] = $stock;
                        break;
                    }
                }
            }

            // exclude item that already hold
            $holdItems = $this->transporterEntryPermitHoldItem->getBy([
                'transporter_entry_permit_request_hold_items.hold_status' => "HOLD"
            ]);
            foreach ($stockGoods as $index => &$outstandingRequest) {
                $outstandingRequest['_is_hold'] = false;
                $outstandingRequest['hold_status'] = 'OK';
                foreach ($holdItems as $holdItem) {
                    $sameBooking = $outstandingRequest['id_booking_outbound'] == $holdItem['id_booking'];
                    $sameGoods = $outstandingRequest['id_goods'] == $holdItem['id_goods'];
                    $sameUnit = $outstandingRequest['id_unit'] == $holdItem['id_unit'];
                    $sameExNoContainer = $outstandingRequest['ex_no_container'] == $holdItem['ex_no_container'];
                    if ($sameBooking && $sameGoods && $sameUnit && $sameExNoContainer) {
                        $outstandingRequest['_is_hold'] = true;
                        $outstandingRequest['hold_status'] = 'HOLD';
                        //unset($outstandingRequests[$index]); // remove from list instead
                        break;
                    }
                }

                // add additional information unload location and priority - can be queried in getStockRequest()
                $outstandingRequest['unload_location'] = $outstandingRequest['unload_location'] ?? null;
                $outstandingRequest['priority'] = $outstandingRequest['priority'] ?? null;
                $existingItemTransactions = $this->transporterEntryPermitRequestUpload->getBy([
                    'transporter_entry_permit_request_uploads.id_upload' => $outstandingRequest['id_upload'],
                    'transporter_entry_permit_request_uploads.id_goods' => $outstandingRequest['id_goods'],
                    'transporter_entry_permit_request_uploads.id_unit' => $outstandingRequest['id_unit'],
                    'transporter_entry_permit_request_uploads.ex_no_container' => $outstandingRequest['ex_no_container'],
                ]);
                if (!empty($existingItemTransactions)) {
                    $outstandingRequest['unload_location'] = $existingItemTransactions[0]['unload_location'];
                    $outstandingRequest['priority'] = $existingItemTransactions[0]['priority'];
                }
            }

            header('Content-Type: application/json');
            echo json_encode([
                'goods' => $stockGoods,
            ]);
        }
    }

    /**
     * Get stock by id request.
     */
    public function ajax_get_goods_by_id_request()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $requestId = $this->input->get('id_request');

            $listGoods = $this->transporterEntryPermitRequestUpload->getByRequestId($requestId);
            header('Content-Type: application/json');
            echo json_encode([
                'goods' => $listGoods,
            ]);
        }
    }

    /**
     * Get slot by date.
     */
    public function ajax_slot_tep_by_date()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $tepDate = $this->input->get('tep_date');
            $tepDate = format_date($tepDate, 'Y-m-d');

            $slot = $this->transporterEntryPermitRequest->getSlotRequest(['tep_date'=>$tepDate]);
            $slot_filled=0;
            foreach ($slot as $slot) {
                $slot_filled += $slot['slot'];
            }
            
            $slot_remain = get_active_branch('max_slot_tep')-$slot_filled;
            $slot_add = $this->transporterEntryPermitSlot->getSlot(['tep_date'=>$tepDate]);
            if (!empty($slot_add)) {
                $slot_remain+=$slot_add[0]['total_slot'];
            }
            $min_time = $this->transporterEntryPermitRequest->getMinTime(['tep_date' => $tepDate]);
            if (!empty($min_time)) {
                $min_time['min_time'] = date('H:i', strtotime('+1 minutes', strtotime($min_time['min_time'])));
            } else {
                $min_time['min_time'] = "";
            }
            header('Content-Type: application/json');
            echo json_encode([
                'slot' => $slot_remain,
                'min_time' => $min_time['min_time'],
            ]);
        }
    }
}
