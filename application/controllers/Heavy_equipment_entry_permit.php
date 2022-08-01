<?php

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Illuminate\Support\Str;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

defined('BASEPATH') OR exit('No direct script access allowed');

use Milon\Barcode\DNS2D;

/**
 * Class Heavy_equipment_entry_permit
 * @property HeavyEquipmentEntryPermitModel $heavyEquipmentEntryPermit
 * @property NotificationModel $notification
 * @property PurchaseOrderModel $purchaseOrder
 * @property DocumentTypeModel $documentType
 * @property UploadDocumentFileModel $uploadDocumentFile
 * @property Uploader $uploader
 */
class Heavy_equipment_entry_permit extends MY_Controller
{
    /**
     * Heavy_equipment_entry_permit constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('HeavyEquipmentEntryPermitModel', 'heavyEquipmentEntryPermit');
        $this->load->model('NotificationModel', 'notification');
        $this->load->model('PurchaseOrderModel', 'purchaseOrder');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
        $this->load->model('modules/Uploader', 'uploader');
        $this->load->model('modules/S3FileManager', 's3FileManager');

        $this->setFilterMethods([
            'ajax_get_data' => 'GET',
            'check_in' => 'POST|PUT|PATCH',
            'check_out' => 'POST|PUT|PATCH',
            'ajax_get_heavy_equipment' => 'GET',
            'ajax_get_heep' => 'GET',
            'ajax_get_heep_all' => 'POST',
        ]);
    }

    /**
     * Show document data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HEEP_VIEW);
        
        $this->render('heavy_equipment_entry_permit/index');
    }

    /**
     * Get ajax paging data TEP.
     */
    public function ajax_get_data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HEEP_VIEW);

        $filters = [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ];

        $data = $this->heavyEquipmentEntryPermit->getAll($filters);
        
        $this->render_json($data);
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
            $filters['(transporter_entry_permit_bookings.id_booking='.get_url_param('id_booking').')'] = null;
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
     * Get ajax requistion pogram purchasing.
     */
    public function ajax_get_heavy_equipment()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HEEP_VIEW);
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $search = $this->input->get('q');
            $page = $this->input->get('page');

            $data = $this->purchaseOrder->getHeavyEquipment($search, $page);
            // print_debug($data);
            header('Content-Type: application/json');
            echo json_encode($data);
        }
    }

    /**
     * Get ajax requistion pogram purchasing.
     */
    public function ajax_get_heep()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HEEP_VIEW);
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $search = $this->input->get('q');
            $page = $this->input->get('page');

            $data = $this->heavyEquipmentEntryPermit->getHEEP($search, $page);
            // print_debug($data);
            header('Content-Type: application/json');
            echo json_encode($data);
        }
    }

    /**
     * Show detail job documents.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HEEP_VIEW);
        $heep = $this->heavyEquipmentEntryPermit->getById($id);
        $this->render('heavy_equipment_entry_permit/view', compact('heep'));
    }

    /**
     * Show form create transporter entry permit.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HEEP_CREATE);

        $this->render('heavy_equipment_entry_permit/create');
    }

    /**
     * Perform deleting entry permit.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HEEP_DELETE);

        $heep = $this->heavyEquipmentEntryPermit->getById($id);

        if ($this->heavyEquipmentEntryPermit->delete($id)) {
            flash('warning', "Entry permit {$heep['heep_code']} is successfully deleted");
        } else {
            flash('danger', "Delete entry permit {$heep['heep_code']} failed");
        }
        redirect('heavy-equipment-entry-permit');
    }
    /**
     * Save transporter entry permit
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HEEP_CREATE);

        if ($this->validate(['relate' => 'required'])) {
            $relate = $this->input->post('relate');
            $purchase_order_id = $this->input->post('purchase_order');
            $heep_reference_id = $this->input->post('heep_reference');
            $description = $this->input->post('description');

            $this->db->trans_start();
            
            // add entry permit
            $codes = [];
            $no_heep = '';
            $expiredDate = date('Y-m-d 23:59:59');
            
            $code = $this->heavyEquipmentEntryPermit->generateCode();
            if (!empty($purchase_order_id)) {
                $purchase_order = $this->purchaseOrder->getById($purchase_order_id);
                $codeGen = explode("/",$code);
                $codeGen = substr($codeGen[1],0,2);
                $noPurcahse = $purchase_order['no_requisition'];
                $noPurcahse = explode("/",$noPurcahse);
                $datePurchase = date('mY',strtotime($purchase_order['created_at']));
                $no_heep = $codeGen.$noPurcahse[0].$datePurchase;
            }else{
                $heep_reference = $this->heavyEquipmentEntryPermit->getById($heep_reference_id);
                $codeGen = explode("/",$code);
                $codeGen = substr($codeGen[1],0,2);
                $noHEEP = $heep_reference['no_heep'];
                $noHEEP = substr($noHEEP,2);
                $datePurchase = date('mY',strtotime($purchase_order['created_at']));
                $no_heep = $codeGen.$noHEEP;
            }
            // print_debug($datePurchase);
            // print_debug($no_heep." ".$code);
            $this->heavyEquipmentEntryPermit->create([
                'id_requisition' => !empty($purchase_order_id)?$purchase_order_id:null,
                'id_heep_reference' => !empty($heep_reference_id)?$heep_reference_id:null,
                'heep_code' => $code,
                'no_heep' => $no_heep,
                'expired_at' => $expiredDate,
                'description' => $description,
                'id_branch' => get_active_branch('id'),
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {                
                flash('success', "Entry permit successfully created", 'heavy-equipment-entry-permit');
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
        AuthorizationModel::mustAuthorized(PERMISSION_HEEP_EDIT);

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

    public function check_in($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SECURITY_CHECK_IN);
        if ($this->validate(['description' => 'trim|required'])) {
            $description = $this->input->post('description');
            $this->db->trans_start();
            $heep = $this->heavyEquipmentEntryPermit->getById($id);
            // upload attachment if exist
            $fileName = '';
            $uploadPassed = true;
            $files = $this->input->post('attachment_name');
            $tempArray = explode('.', $files[0]);
            $extension = end($tempArray);
            if (!empty($files)) {
                $fileName = 'HEEP_' . time() . '_' . rand(100, 999);
                $uploadedPhoto = 'temp/' . $files[0];
                $saveTo = 'security_heep/' . $fileName . '.' .$extension;
                
                $status= $this->uploader->setDriver('s3')->move($uploadedPhoto, $saveTo);
                if (!$status) {
                    flash('danger', "Security Checklist {$heep['heep_code']} failed, uplod server fail");
                    redirect('security/check?code=' . $heep['heep_code']);
                }else{
                    $fileName = $fileName . '.' .$extension;
                }
            }else{
                flash('danger', "Security Checklist {$heep['heep_code']} failed, You haven't uploaded a photo yet");
                redirect('security/check?code=' . $heep['heep_code']);
            }
            $textCode = $heep['heep_code'] . "\n";
            $textJam = date('d F Y H:i:s') . "\n";
            $textSec =  "SECURITY START BY ".strtoupper(UserModel::authenticatedUserData('username')) . "\n";
            $text = $textCode . $textJam . $textSec ;
            // $path = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'security_heep' . DIRECTORY_SEPARATOR . $fileName;
            $pathWa = 'security_heep/'. $fileName;
            $this->watermark($pathWa, $fileName, $text);
            if($uploadPassed){
                $this->heavyEquipmentEntryPermit->update([
                    'checked_in_description' => $description,
                    'checked_in_at' => date('Y-m-d H:i:s'),
                    'checked_in_by' => UserModel::authenticatedUserData('id'),
                    'photo_in' => $fileName,
                ],$id);
            }
            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                $branch = get_active_branch();
                $whatsapp_group = $branch['whatsapp_group_security'];
                // $text = "[CHECK IN HEEP] Entry permit *{$heep['heep_code']}* successfully checked in at ".date('d F Y H:i:s');
                $this->send_message($pathWa, $whatsapp_group);
                flash('warning', "Entry permit <strong>{$heep['heep_code']}</strong> successfully checked in");
            } else {
                flash('danger', "Check in entry permit <strong>{$heep['heep_code']}</strong> failed");
            }
        }
        redirect('security');
    }
    public function check_out($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SECURITY_CHECK_OUT);
        if ($this->validate(['description' => 'trim|required'])) {
            $description = $this->input->post('description');
            $this->db->trans_start();
            $heep = $this->heavyEquipmentEntryPermit->getById($id);
            // upload attachment if exist
            $fileName = '';
            $uploadPassed = true;
            $files = $this->input->post('attachment_name');
            $tempArray = explode('.', $files[0]);
            $extension = end($tempArray);
            if (!empty($files)) {
                $fileName = 'HEEP_' . time() . '_' . rand(100, 999);
                $uploadedPhoto = 'temp/' . $files[0];
                $saveTo = 'security_heep/' . $fileName . '.' .$extension;
                
                $status= $this->uploader->setDriver('s3')->move($uploadedPhoto, $saveTo);
                if (!$status) {
                    flash('danger', "Security Checklist {$heep['heep_code']} failed, uplod server fail");
                    redirect('security/check?code=' . $heep['heep_code']);
                }else{
                    $fileName = $fileName . '.' .$extension;
                }
            }else{
                flash('danger', "Security Checklist {$heep['heep_code']} failed, You haven't uploaded a photo yet");
                redirect('security/check?code=' . $heep['heep_code']);
            }
            $textCode = $heep['heep_code'] . "\n";
            $textJam = date('d F Y H:i:s') . "\n";
            $textSec =  "SECURITY STOP BY ".strtoupper(UserModel::authenticatedUserData('username')) . "\n";
            $text = $textCode . $textJam . $textSec ;
            // $path = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'security_heep' . DIRECTORY_SEPARATOR . $fileName;
            $pathWa = 'security_heep/'. $fileName;
            $this->watermark($pathWa, $fileName, $text);
            if($uploadPassed){
                $this->heavyEquipmentEntryPermit->update([
                    'checked_out_description' => $description,
                    'checked_out_at' => date('Y-m-d H:i:s'),
                    'checked_out_by' => UserModel::authenticatedUserData('id'),
                    'photo_out' => $fileName,
                ],$id);
            }
            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                $branch = get_active_branch();
                $whatsapp_group = $branch['whatsapp_group_security'];
                // $text = "[CHECK OUT HEEP] Entry permit *{$heep['heep_code']}* successfully checked out at ".date('d F Y H:i:s');
                $this->send_message($pathWa, $whatsapp_group);
                flash('warning', "Entry permit <strong>{$heep['heep_code']}</strong> successfully checked out");
            } else {
                flash('danger', "Check out entry permit <strong>{$heep['heep_code']}</strong> failed");
            }
        }
        redirect('security');
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
     * Get ajax heavy equipment.
     */
    public function ajax_get_heep_all(){
        $heavyEquipments = $this->heavyEquipmentEntryPermit->getHEEPAll();
              
        header('Content-Type: application/json');
        echo json_encode($heavyEquipments);
    }
}
