<?php
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Intervention\Image\Image;
use Intervention\Image\ImageManager;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Tally
 * @property AssemblyGoodsModel $assemblyGoods
 * @property HandlingModel $handling
 * @property HandlingContainerModel $handlingContainer
 * @property HandlingGoodsModel $handlingGoods
 * @property SafeConductModel $safeConduct
 * @property SafeConductContainerModel $safeConductContainer
 * @property SafeConductGoodsModel $safeConductGoods
 * @property SafeConductGroupModel $safeConductGroup
 * @property PalletModel $pallet
 * @property WarehouseModel $warehouse
 * @property WorkOrderModel $workOrder
 * @property WorkOrderContainerModel $workOrderContainer
 * @property WorkOrderGoodsModel $workOrderGoods
 * @property WorkOrderHistoryModel $workOrderHistory
 * @property WorkOrderContainerHistoryModel $workOrderContainerHistory
 * @property WorkOrderGoodsHistoryModel $workOrderGoodsHistory
 * @property WorkOrderContainerPositionModel $workOrderContainerPosition
 * @property WorkOrderGoodsPositionModel $workOrderGoodsPosition
 * @property WorkOrderGoodsPhotoModel $workOrderGoodsPhoto
 * @property ContainerModel $container
 * @property GoodsModel $goods
 * @property PositionModel $position
 * @property UnitModel $unit
 * @property Uploader $uploader
 * @property WorkOrderPhotoModel $workOrderPhoto
 * @property NotificationModel $notification
 * @property SettingModel $setting
 * @property BookingModel $booking
 * @property BookingReferenceModel $bookingReference
 * @property BookingTypeModel $bookingType
 * @property ComponentModel $component
 * @property WorkOrderComponentModel $workOrderComponent
 * @property VehicleModel $vehicle
 * @property TransporterEntryPermitModel $transporterEntryPermit
 * @property ArmadaModel $armada
 * @property StatusHistoryModel $statusHistory
 * @property ReportModel $report
 * @property PeopleModel $people
 * @property TransporterEntryPermitChecklistModel $TransporterEntryPermitChecklist
 * @property TransporterEntryPermitChecklistDetailModel $TransporterEntryPermitChecklistDetail
 * @property TransporterEntryPermitChassisModel $transporterEntryPermitChassis
 * @property SafeConductChecklistModel $safeConductChecklist
 * @property SafeConductChecklistDetailModel $safeConductChecklistDetail
 * @property RoleModel $roleModel
 * @property PermissionModel $permission
 * @property RolePermissionModel $rolePermission
 * @property UserModel $userModel
 * @property AllowanceModel $allowance
 * @property EmployeeModel $employee
 * @property HandlingTypeModel $handlingType
 * @property GuestModel $guest
 * @property HeavyEquipmentModel $heavyEquipment
 * @property WorkOrderComponentLaboursModel $workOrderComponentLabours
 * @property WorkOrderComponentHeavyEquipmentsModel $workOrderComponentHeavyEquipments
 * @property HeavyEquipmentEntryPermitModel $heavyEquipmentEntryPermit
 * @property WorkOrderComponentHeepsModel $workOrderComponentHeeps
 * @property WorkOrderOvertimeChargeModel $workOrderOvertimeCharge
 * @property WorkOrderUnlockHandheldModel $workOrderUnlockHandheld
 * @property CustomerStorageCapacityModel $customerStorageCapacity
 * @property StorageUsageModel $storageUsage
 * @property StorageOverSpaceModel $storageOverSpace
 * @property ComplainModel $complain
 * @property ComplainKpiModel $complainKpi
 * @property ComplainHistoryModel $complainHistory
 * @property PutAwayModel $putAway
 */


class Tally extends MY_Controller
{
    /**
     * Tally constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('AssemblyGoodsModel', 'assemblyGoods');
        $this->load->model('HandlingModel', 'handling');
        $this->load->model('HandlingContainerModel', 'handlingContainer');
        $this->load->model('HandlingGoodsModel', 'handlingGoods');
        $this->load->model('HandlingTypeModel', 'handlingType');
        $this->load->model('SafeConductModel', 'safeConduct');
        $this->load->model('SafeConductContainerModel', 'safeConductContainer');
        $this->load->model('SafeConductGoodsModel', 'safeConductGoods');
        $this->load->model('SafeConductGroupModel', 'safeConductGroup');
        $this->load->model('PalletModel', 'pallet');
        $this->load->model('UnitModel', 'unit');
        $this->load->model('GoodsModel', 'goods');
        $this->load->model('WarehouseModel', 'warehouse');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('WorkOrderContainerModel', 'workOrderContainer');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('WorkOrderContainerPositionModel', 'workOrderContainerPosition');
        $this->load->model('WorkOrderGoodsPositionModel', 'workOrderGoodsPosition');
        $this->load->model('WorkOrderGoodsPhotoModel', 'workOrderGoodsPhoto');
        $this->load->model('LogModel', 'logHistory');
        $this->load->model('OvertimeModel', 'Overtime');
        $this->load->model('WorkOrderHistoryModel', 'workOrderHistory');
        $this->load->model('WorkOrderContainerHistoryModel', 'workOrderContainerHistory');
        $this->load->model('WorkOrderGoodsHistoryModel', 'workOrderGoodsHistory');
        $this->load->model('modules/Uploader', 'uploader');
        $this->load->model('WorkOrderPhotoModel', 'workOrderPhoto');
        $this->load->model('NotificationModel', 'notification');
        $this->load->model('SettingModel', 'setting');
        $this->load->model('BookingTypeModel', 'bookingType');
        $this->load->model('VehicleModel', 'vehicle');
        $this->load->model('TransporterEntryPermitModel', 'transporterEntryPermit');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingReferenceModel', 'bookingReference');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('ComponentModel', 'component');
        $this->load->model('WorkOrderComponentModel', 'workOrderComponent');
        $this->load->model('ArmadaModel', 'armada');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('ContainerModel', 'container');
        $this->load->model('ReportModel', 'report');
        $this->load->model('ReportStockModel', 'reportStock');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('TransporterEntryPermitChecklistModel', 'TransporterEntryPermitChecklist');
        $this->load->model('TransporterEntryPermitChecklistDetailModel', 'TransporterEntryPermitChecklistDetail');
        $this->load->model('SafeConductChecklistModel', 'safeConductChecklist');
        $this->load->model('SafeConductChecklistDetailModel', 'safeConductChecklistDetail');
        $this->load->model('RoleModel', 'roleModel');
        $this->load->model('PermissionModel', 'permission');
        $this->load->model('RolePermissionModel', 'rolePermission');
        $this->load->model('UserModel', 'userModel');
        $this->load->model('AllowanceModel', 'allowance');
        $this->load->model('EmployeeModel', 'employee');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('TransporterEntryPermitChecklistModel', 'TransporterEntryPermitChecklist');
        $this->load->model('TransporterEntryPermitChecklistDetailModel', 'TransporterEntryPermitChecklistDetail');
        $this->load->model('TransporterEntryPermitChassisModel', 'transporterEntryPermitChassis');
        $this->load->model('SafeConductChecklistModel', 'safeConductChecklist');
        $this->load->model('SafeConductChecklistDetailModel', 'safeConductChecklistDetail');
        $this->load->model('GuestModel', 'guest');
        $this->load->model('HeavyEquipmentModel', 'heavyEquipment');
        $this->load->model('WorkOrderComponentLaboursModel', 'workOrderComponentLabours');
        $this->load->model('HeavyEquipmentEntryPermitModel', 'heavyEquipmentEntryPermit');
        $this->load->model('WorkOrderComponentHeavyEquipmentsModel', 'workOrderComponentHeavyEquipments');
        $this->load->model('WorkOrderComponentHeepsModel', 'workOrderComponentHeeps');
        $this->load->model('WorkOrderOvertimeChargeModel', 'workOrderOvertimeCharge');
        $this->load->model('WorkOrderUnlockHandheldModel', 'workOrderUnlockHandheld');
        $this->load->model('CustomerStorageCapacityModel', 'customerStorageCapacity');
        $this->load->model('StorageUsageModel', 'storageUsage');
        $this->load->model('StorageOverSpaceModel', 'storageOverSpace');
        $this->load->model('ComplainModel', 'complain');
        $this->load->model('ComplainKpiModel', 'complainKpi');
        $this->load->model('ComplainHistoryModel', 'complainHistory');
        $this->load->model('PutAwayModel', 'putAway');
        $this->load->model('modules/S3FileManager', 's3FileManager');

        $this->setFilterMethods([
            'take_job' => 'POST',
            'release_job' => 'POST',
            'complete_job' => 'POST||GET',
            'request_handover' => 'POST',
            'approve_handover' => 'POST',
            'take_handover' => 'POST',
            'save' => 'POST',
            'send_message' => 'GET',
            'complete_job_checking' => 'POST',
            'upload' => 'POST',
            'upload_to' => 'POST',
            'delete_temp_upload' => 'POST',
            'delete_file' => 'POST',
            'get_plat' => 'POST',
            'get_vas_and_resources' => 'POST',
            'approve_job' => 'POST',
            'checked_job' => 'POST',
            'get_vas_and_resources' => 'POST',            
            'get_goods' => 'POST',
            'get_labours' => 'POST',
            'get_heavy_equipment' => 'POST',
            'ajax_get_used_space' => 'POST',
            'ajax_get_used_pallet' => 'POST',
        ]);
    }

    /**
     * Get work order queue.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_VIEW);

        $permission = $this->permission->getBy(['permission' => PERMISSION_WORKORDER_TAKE_JOB], true);
        $rolePermission = $this->rolePermission->getBy(['id_permission' => $permission['id']]);
        $resultRoleId = array_column($rolePermission, 'id_role');
        $users = $this->userModel->getBy(['user_type' => 'INTERNAL']);

        $handover_users = [];
        foreach ($users as $key => $user) {
            $userRoles = $this->roleModel->getByUser($user['id'], false, get_active_branch('id'));
            if(!empty($userRoles)){
                $roleId = array_column($userRoles, 'id');
                if(!empty(array_intersect($resultRoleId, $roleId))){
                    $handover_users[] = $user;
                }
            };
        }

        $settings = $this->setting->getAllSettings();
        $armadas = $this->armada->getAll(['asc']);
        $workOrders = $this->workOrder->getQueueListWorkOrderHandling();
        $activity_types = $this->component->getBy(['component_category'=>'VALUE ADDITIONAL SERVICES']);

        $i = 0;
        $hasValidatedEditPermission = AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATED_EDIT);
        foreach ($workOrders as &$workOrder) {
            $containers = $this->handlingContainer->getHandlingContainersByHandling($workOrder['id_handling']);
            $goods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrder['id'], true, true);
            if ($containers != null) {
                $workOrders[$i]['container'] = $containers[0]['no_container'];
            } else {
                $workOrders[$i]['container'] = null;
            }
            if ($goods != null) {
                $workOrders[$i]['goods'] = 'Have';
            } else {
                $workOrders[$i]['goods'] = null;
            }
            $workOrderStatuses = $this->statusHistory->getBy([
                'status_histories.type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
                'status_histories.id_reference' => $workOrder['id']
            ]);
            $workOrders[$i]['created_at_status']=null;
            $workOrders[$i]['checked_name']=null;
            foreach ($workOrderStatuses as $workOrderStatus) {
                if ($workOrderStatus['status']=='CHECKED') {
                    $workOrders[$i]['created_at_status'] = $workOrderStatus['created_at'];
                    $workOrders[$i]['checked_name'] = $workOrderStatus['creator_name'];
                }
            }

            if ($hasValidatedEditPermission) {
                $workOrder['status_unlock_handheld'] = WorkOrderUnlockHandheldModel::STATUS_UNLOCKED;
            } else {
                $workOrderUnlockHandheld = $this->workOrderUnlockHandheld->getBy([
                    'id_work_order' => $workOrder['id']
                ], true);
                $workOrder['status_unlock_handheld'] = if_empty($workOrderUnlockHandheld['status'], WorkOrderUnlockHandheldModel::STATUS_LOCKED);
            }

            $i++;
        }
        $resources_types = $this->component->getBy(['component_category'=>'RESOURCES']);

        $expeditions = $this->people->getByType(PeopleModel::$TYPE_EXPEDITION);
        $outstandingChassis = $this->transporterEntryPermitChassis->getBy([
            'transporter_entry_permit_chassis.checked_in_at IS NOT NULL' => null,
            'transporter_entry_permit_chassis.checked_out_at IS NULL' => null,
        ]);

        $isOvertimeValidationCompleted = $this->workOrderOvertimeCharge->isOvertimeValidationCompleted();
        $isOverSpaceValidation75Completed = $this->storageUsage->isOverSpaceValidationCompleted();
        $isOverSpaceValidationPeriodCompleted = $this->storageOverSpace->isOverSpaceValidationCompleted();
        $isOverSpaceValidationCompleted = $isOverSpaceValidation75Completed && $isOverSpaceValidationPeriodCompleted;

        //lock job if complain KPI major or minor +1 days didnt approve investigation
        $complain_kpi = $this->complainKpi->getById(1);
        $complains = $this->complain->getBySubmitApprovalByBranch(get_active_branch('id'));
        $complains2 = $this->complain->getByDisproveApproval(get_active_branch('id'));
        $complains = array_unique(array_merge($complains, $complains2),SORT_REGULAR);
        $lockComplain = false;
        $t1 = strtotime(date('Y-m-d H:i:s'));
        foreach($complains as $complain){
            // if complain set final, ignore for locking
            $finalComplainHistories = $this->complainHistory->getBy([
                'complain_histories.id_complain' => $complain['id'],
                'complain_histories.status' => ComplainModel::STATUS_FINAL
            ]);
            if (!empty($finalComplainHistories)) continue;

            $complainHistory = $this->complainHistory->getLastDisprove($complain['id']);
            if(empty($complainHistory)){
                $complainHistory = $this->complainHistory->getLastOnReview($complain['id']);
            }
            if(!empty($complainHistory)){
                $t2 = strtotime($complainHistory['created_at']);
                $diff = $t1 - $t2;
                $hours = floor($diff / 3600);
                $day = ceil($diff / (3600*24));
                if($complain['value_type'] == 'MAJOR'){
                    if($complain_kpi['major']<=$hours){
                        $mod = $day % $complain_kpi['recur_day'];
                        if($complain_kpi['recur_day']>= $day || $mod == 0){
                            $complainNotif[] = $complain;
                            //to cek if job is lock
                            if(!$lockComplain && ($complain_kpi['major']+24) <= $hours){
                                $lockComplain = true;
                                break;
                            }
                        }
                    }
                }else{
                    if($complain_kpi['minor']<=$hours){
                        $mod = $day % $complain_kpi['recur_day'];
                        if($complain_kpi['recur_day']>= $day || $mod == 0){
                            $complainNotif[] = $complain;
                            //to cek if job is lock
                            if(!$lockComplain && ($complain_kpi['minor']+24) <= $hours){
                                $lockComplain = true;
                                break;
                            }
                        }
                    } 
                }
            }
        }
        $lockComplain = false;

        foreach ($workOrders as &$workOrder) {
            $bookingIn = $this->booking->getBookingById($workOrder['id_booking_in']);
            $booking = $this->booking->getBookingById($workOrder['id_booking']);
            $workOrder['no_reference_inbound'] = get_if_exist($bookingIn, 'no_reference');
            if ($booking['category'] == 'OUTBOUND' && $booking['outbound_type'] == PeopleModel::OUTBOUND_CASH_AND_CARRY && !empty($booking['payout_until_date']) && $booking['payout_until_date'] < date('Y-m-d')) {
                $workOrder['payout_passed'] = true;
            } else {
                $workOrder['payout_passed'] = false;
            }
        }
        $pendingPutAway = $this->putAway->getBy([
            'put_away.status' => [PutAwayModel::STATUS_PENDING, PutAwayModel::STATUS_REOPENED, PutAwayModel::STATUS_PROCESSED, PutAwayModel::STATUS_NOT_PROCESSED],
            'put_away.id_branch' => get_active_branch('id'),
            'put_away.created_at<=' => date('Y-m-d H:i:s',strtotime('-1 day')),
        ]);
        $isPendingPutAway = false;
        if (!empty($pendingPutAway)) {
            $isPendingPutAway = true;
        }
        $this->render('tally/queue', compact('workOrders', 'settings', 'armadas', 'activity_types', 'handover_users', 'expeditions', 'resources_types', 'outstandingChassis', 'isOvertimeValidationCompleted', 'isOverSpaceValidationCompleted', 'lockComplain', 'isPendingPutAway'), 'Tally Queue');
    }

    /**
    * Request Handover
    */
    public function request_handover()
    {
        //get data post
        $data = $this->input->post();

        $this->db->trans_start();

        //update status work order
        $workOrder = $this->workOrder->getWorkOrderById($data['id']);
        $this->workOrder->updateWorkOrder([
            'updated_by' => UserModel::authenticatedUserData('id'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status_validation' => WorkOrderModel::STATUS_VALIDATION_HANDOVER_RELEASED,
        ], $data['id']);

        //add status history
        $this->statusHistory->create([
            'type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
            'id_reference' => $data['id'],
            'status' => WorkOrderModel::STATUS_VALIDATION_HANDOVER_RELEASED,
            'description' => null,
            'data' => json_encode([
                'handover_user_id' => $data['handover_user_id'],
            ])
        ]);

        $this->db->trans_complete();

        if($this->db->trans_status()){
            flash('success',"Request handover is successfully");
        }else{
            flash('danger',"Request handover failed, Please contact administrator!");
        }

        redirect('tally');
    }

    /**
    * Approve Handover By
    */
    public function approve_handover()
    {

        //get data post
        $data = $this->input->post();

        $this->db->trans_start();

        //update status work order
        $workOrder = $this->workOrder->getWorkOrderById($data['id']);
        $this->workOrder->updateWorkOrder([
            'updated_by' => UserModel::authenticatedUserData('id'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status_validation' => WorkOrderModel::STATUS_VALIDATION_HANDOVER_APPROVED,
        ], $data['id']);

        //add status history
        $this->statusHistory->create([
            'type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
            'id_reference' => $data['id'],
            'status' => WorkOrderModel::STATUS_VALIDATION_HANDOVER_APPROVED,
            'description' => null,
            'data' => json_encode([
                'handover_user_id' => $data['handover_user_id'],
            ])
        ]);

        $this->db->trans_complete();

        if($this->db->trans_status()){
            flash('success',"Handover <strong>{$workOrder['no_work_order']}</strong> successfully approved by <strong>". strtoupper(UserModel::authenticatedUserData('name'))."</strong>");
        }else{
            flash('danger',"Approve handover <strong>{$workOrder['no_work_order']}</strong> failed, Please contact administrator!");
        }

        redirect('tally');
    } 

    /**
    * Take Handover
    */
    public function take_handover()
    {

        //get data post
        $data = $this->input->post();

        $this->db->trans_start();

        //update status work order
        $workOrder = $this->workOrder->getWorkOrderById($data['id']);
        $this->workOrder->updateWorkOrder([
            'updated_by' => UserModel::authenticatedUserData('id'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status_validation' => WorkOrderModel::STATUS_VALIDATION_HANDOVER_TAKEN,
        ], $data['id']);

        //add status history
        $this->statusHistory->create([
            'type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
            'id_reference' => $data['id'],
            'status' => WorkOrderModel::STATUS_VALIDATION_HANDOVER_TAKEN,
            'description' => null,
            'data' => json_encode([
                'handover_user_id' => $data['handover_user_id'],
            ])
        ]);

        $this->db->trans_complete();

        if($this->db->trans_status()){
            flash('success',"Handover job <strong>{$workOrder['no_work_order']}</strong> was taken by <strong>". strtoupper(UserModel::authenticatedUserData('name'))."</strong>");
        }else{
            flash('danger',"Taking handover job <strong>{$workOrder['no_work_order']}</strong> failed, Please contact administrator!");
        }

        redirect('tally');
    }

    /**
     * Take job tally.
     */
    public function take_job()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_TAKE_JOB);
        $uploadedPhoto = "";
        $photo_names = $this->input->post('photo_name');
        $id_customer = $this->input->post('id_customer');
        $transporter = $this->input->post('transporter');
        $armada = $this->input->post('armada');
        $armada_type = $this->input->post('armada_type');
        $rute_pengiriman = $this->input->post('rute_pengiriman');
        $armada_description = $this->input->post('armada_description');
        $expedition = $this->input->post('expedition');
        $chassisId = $this->input->post('chassis');

        $id_tep = NULL;
        $id_vehicle = NULL;
        $no_police = '';
        if ($transporter=="internal") {
            $no_police = $this->input->post('plat');
            $arrayPlat = explode(",",$no_police);
            $no_police = $arrayPlat[0];
            $id_vehicle = $arrayPlat[1];
        } else {
            $no_police = $this->input->post('plat_external');
            if(!empty($no_police)){
                $arrayPlat = explode(",",$no_police);
                $no_police = $arrayPlat[0];
                $id_tep = $arrayPlat[1];
            }
        }
        $people = $this->people->getById($id_customer);
        $whatsapp_group=$people['whatsapp_group'];

        $branch = get_active_branch();
        $whatsapp_group_internal=$branch['whatsapp_group'];

        //untuk membuat text
        $workOrderId = $this->input->post('id');
        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
        $containers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderId,true);
        $bookingTypes = $this->bookingType->getBookingTypeById($workOrder['id_booking_type']);
        if(empty($containers)){
            $containers = $this->handlingContainer->getHandlingContainersByHandling($workOrder['id_handling']);
        }
        $containerGoods = $this->handlingGoods->getHandlingGoodsByHandling($workOrder['id_handling'], true);

        $text1 = $workOrder['handling_type']." ".$bookingTypes['default_document']." AJU ".substr($workOrder['no_reference'],-4);
        if (isset($containers[0]['no_container'])) {
            $text2 = $containers[0]['no_container'];
        }else if (isset($containerGoods[0]['ex_no_container'])){
            $text2= 'EX '.$containerGoods[0]['ex_no_container'];
        }else{
            $text2= 'NO EX CONTAINER NUMBER';
        }
        if($text2=='' || $text2== '0'){
            $text2= 'NO EX CONTAINER NUMBER';
        }
        $text3 = "\n".$workOrder['customer_name'];
        $text4 = "\n"."TAKEN BY ".strtoupper(UserModel::authenticatedUserData('username'));
        // if ($text4=='') {
        //     $text4=UserModel::authenticatedUserData('name');
        // }
        if ($text2=='NO EX CONTAINER NUMBER') {
            $hasilText = date('d M Y H:i:s')."\n".$text1.$no_police.$text3.$text4;
        } else {
            if ($no_police=='') {
                $hasilText = date('d M Y H:i:s')."\n".$text1."\n".$text2.$no_police.$text3.$text4;
            } else {
                $hasilText = date('d M Y H:i:s')."\n".$text1."\n".$text2." | ".$no_police.$text3.$text4;
            }
        }

        $this->db->trans_start();
        //sampai sini hasil text
        if ($photo_names[0]!=null) {
            $i=0;
            foreach ($photo_names as $photo_name) {
                $files = $this->input->post('attachments_' . $i . '_name');
                if (empty($files)) {
                    flash('danger', "Take Job {$workOrder['no_work_order']} failed, You haven't uploaded a photo yet");
                    redirect('tally');
                }
                $uploadedPhoto = 'temp/' . $files[0];

                $photo_name=$photo_names[$i];
                $text5 = strtoupper($photo_name);
                $tempText=$hasilText;
                $tempText.="\n".$text5;
                $watermark=$this->watermark($uploadedPhoto,$photo_name,$tempText);
                $uploadedPhoto = 'temp/' . $files[0];
                $destFile = 'tally/' . date('Y/m') . '/' . $files[0];

                // $this->makeFolder(dirname($destFile));
                // rename($uploadedPhoto, $destFile);
                $status= $this->uploader->setDriver('s3')->move($uploadedPhoto, $destFile);
                if (!$status) {
                    flash('danger', "Take Job {$workOrder['no_work_order']} failed, uplod server fail");
                    redirect('tally');
                }

                $path = 'tally/' . date('Y/m') . '/' . $files[0];
                $save = $this->workOrderPhoto->createWorkOrderPhoto([
                    'id_work_order' => $this->input->post('id'),
                    'photo' => $path,
                    'description' => $photo_name,
                    'created_by' => UserModel::authenticatedUserData('id'),
                ]);
                $i++;
            }

            $i=0;
            foreach($photo_names as $photo_name){
                $files = $this->input->post('attachments_' . $i . '_name');
                $path = 'tally/' . date('Y/m') . '/' . $files[0];
                $this->send_message($path,$whatsapp_group,$whatsapp_group_internal);
                $i++;
            }
        }

        if ($this->validate(['id' => 'trim|required|max_length[50]'])) {
            $workOrderId = $this->input->post('id');

            $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
            if ($workOrder['status'] == WorkOrderModel::STATUS_QUEUED) {
                $userId = UserModel::authenticatedUserData('id');
                $takeJob = $this->workOrder->updateWorkOrder([
                    'status' => WorkOrderModel::STATUS_TAKEN,
                    'taken_by' => $userId,
                    'taken_at' => date('Y-m-d H:i:s'),
                    'updated_by' => $userId,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'id_transporter_entry_permit' => if_empty($id_tep, null),
                    'id_tep_chassis' => if_empty($chassisId, null),
                    'id_vehicle' => $id_vehicle,
                    'id_armada' => $armada,
                    'armada_type' => $armada_type,
                    'shipping_route' => $rute_pengiriman,
                    'armada_description' => $armada_description,
                    'expedition' => $expedition,
                ], $workOrderId);

                $this->db->trans_complete();
                if ($takeJob) {
                    flash('success', "Job was taken by <strong>". strtoupper(UserModel::authenticatedUserData('name'))."</strong>, click <strong>Release</strong> to reset job status");
                    redirect('tally/create/' . $workOrder['id']);
                } else {
                    flash('danger', "Taking job <strong>{$workOrder['no_work_order']}</strong> failed");
                }
            } else {
                flash('danger', "Job is already <strong>{$workOrder['status']}</strong>, queued job only can be taken");
            }
        }

        redirect('tally');
    }
    public function watermark($path,$photo_name,$hasilText){
        // $workOrderId = $this->input->post('id');
        // $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
        // $containers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderId,true);
        // $bookingTypes = $this->bookingType->getBookingTypeById($workOrder['id_booking_type']);
        // if(empty($containers)){
        //     $containers = $this->handlingContainer->getHandlingContainersByHandling($workOrder['id_handling']);
        // }

        // $text1 = $workOrder['handling_type']." ".$bookingTypes['default_document']." AJU ".substr($workOrder['no_reference'],-3);
        // $text2 = $containers[0]['no_container'];
        // if ($text2=='') {
        //     $text2='NO CONTAINER NUMBER';
        // }
        // $text3 = $workOrder['customer_name'];
        // $text4 = strtoupper($workOrder['tally_name']);
        // if ($text4=='') {
        //     $text4=UserModel::authenticatedUserData('name');
        // }
        // $text5 = strtoupper($photo_name);
        // print_r($containers);
        // print_r($text2);
        // print_r($workOrder);


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
            // $bg_putih = $manager->make('assets/app/img/layout/bg_putih.jpg');
            // $bg_putih->resize(600, 200);
            // $bg_putih->opacity(50);
        }else{
            $watermark = $manager->make('assets/app/img/layout/watermark1.png');
            $watermark->resize(850, null, function ($constraint) {
                $constraint->aspectRatio();
            });
            // $bg_putih = $manager->make('assets/app/img/layout/bg_putih.jpg');
            // $bg_putih->resize(870, 110);
            // $bg_putih->opacity(50);
        }
        // $bg_hitam = $manager->make('assets/app/img/layout/bg_hitam.jpg');
        // $bg_hitam->resize(1200, 600);
        // $bg_hitam->opacity(50);



        // // $img->insert($bg_putih, 'top-right', 1, 1);
        // $img->insert($bg_hitam, 'bottom-left', 1, 1);
        $img->insert($watermark, 'top-right', 1, 1);

        $img->text($hasilText, 50, $img->getHeight() - 290, function ($font) {
            $font->file(FCPATH .'assets/plugins/font-googleapis/fonts/SourceSansPro-Bold.ttf');
            $font->size(70);
            $font->color('#FFFF00');
            $font->align('left');
            $font->valign('middle');
        });
        $data = $img->exif();

        $img = $img->encode('jpg', 75);
        $result = $this->s3FileManager->putObjectStream(env('S3_BUCKET'), $path, $img->stream(), $img->mime());
        // $resultImg = $img->save($path, 80);
        // echo $resultImg->response('jpg');
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

        if ($whatsapp_group_internal === false) {
            // fix queue got stuck, see https://chat-api.com/en/swagger.html#/queues/showMessagesQueue
            // or check availability of phone first before send, see https://chat-api.com/en/swagger.html#/testing/checkPhone
            if (!empty($whatsapp_group)) {
                $data = [
                    'url' => 'sendMessage',
                    'method' => 'POST',
                    'payload' => [
                        'chatId' => detect_chat_id($whatsapp_group),
                        'body' => $path, //text data
                    ]
                ];

                $result = $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);
            }
        } else {

            if (!empty($whatsapp_group)) {
                $data = [
                    'url' => 'sendFile',
                    'method' => 'POST',
                    'payload' => [
                        'chatId' => detect_chat_id($whatsapp_group),
                        'body' => asset_url($path),//'https://transcon-indonesia.com/img/front/opening/icon.png', // data:image/jpeg;base64,/9j/4AAQSkZJRgABAQ...
                        'filename' => 'transcon.png'
                    ]
                ];
                $result = $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);
            }

            if (!empty($whatsapp_group_internal)) {
                $data2 = [
                    'url' => 'sendFile',
                    'method' => 'POST',
                    'payload' => [
                        'chatId' => detect_chat_id($whatsapp_group_internal),
                        'body' => asset_url($path),//base_url('uploads/'.$path), // data:image/jpeg;base64,/9j/4AAQSkZJRgABAQ...
                        'filename' => 'transcon.png'
                    ]
                ];
                $result2 = $this->notification->broadcast($data2, NotificationModel::TYPE_CHAT_PUSH);
            }
        }
    }

    /**
     * Get plat nomer by internal or external.
     */
    public function get_plat(){
        $jenis=$this->input->post('jenis');
        if($jenis=="internal"){
            $vehicle=$this->vehicle->getAll();
            foreach ($vehicle as &$vehicleData) {
                $usedVehicles = $this->safeConduct->getBy([
                    'safe_conducts.no_police' => $vehicleData['no_plate'],
                    'safe_conducts.security_in_date IS NOT NULL' => null,
                    'safe_conducts.security_out_date IS NULL' => null,
                ]);
                if(!empty($usedVehicles)) {
                    $vehicleData['_disabled'] = true;
                    $vehicleData['_disabled_data'] = implode(', ', array_column($usedVehicles, 'no_safe_conduct'));
                } else {
                    $vehicleData['_disabled'] = false;
                }
            }
        }else{
            $id_customer=$this->input->post('id_customer');
            $category=$this->input->post('category');
            $handling_type=$this->input->post('handling_type');
            $id_upload=$this->input->post('id_upload');
            if ($handling_type=='EMPTY CONTAINER') {
                // $where=[
                //     'transporter_entry_permits.id_customer'=>$id_customer,
                //     'transporter_entry_permits.tep_category'=>'EMPTY CONTAINER',//$category
                //     'transporter_entry_permits.checked_in_at is not null'=>null,
                //     'transporter_entry_permits.checked_out_at is null'=>null,
                // ];
                // $vehicle1=$this->transporterEntryPermit->getBy($where);
                // $where=[
                //     'transporter_entry_permits.tep_category'=>'EMPTY CONTAINER',//$category
                //     'transporter_entry_permits.checked_in_at is not null'=>null,
                //     'transporter_entry_permits.checked_out_at is null'=>null,
                //     'people_customers.id' => $id_customer
                // ];
                // $vehicle2=$this->transporterEntryPermit->getBy($where);
                // if (empty($vehicle2)) {
                //     $where=[
                //         'transporter_entry_permits.tep_category'=>'EMPTY CONTAINER',//$category
                //         'transporter_entry_permits.checked_in_at is not null'=>null,
                //         'transporter_entry_permits.checked_out_at is null'=>null,
                //         'ref_people.id' => $id_customer
                //     ];
                //     $vehicle2=$this->transporterEntryPermit->getBy($where);
                // }
                $tep_data = $this->transporterEntryPermit->getOutstandingTep([
                    'category' => 'EMPTY CONTAINER',
                    'should_checked_in' => true,
                    'outstanding_checked_out' => true,
                    'id_customer' => $id_customer,
                    'active_days' => 30,
                ]);
            } else {
                // $where=[
                //     'transporter_entry_permits.id_customer'=>$id_customer,
                //     // 'transporter_entry_permits.tep_category'=>'OUTBOUND',//$category
                //     'transporter_entry_permits.checked_in_at is not null'=>null,
                //     'transporter_entry_permits.checked_out_at is null'=>null,
                //     'IF(transporter_entry_permit_request_tep.id is NULL, true, (transporter_entry_permit_uploads.id_upload = "'.$id_upload.'"))'=> null,
                // ];
                // $vehicle1=$this->transporterEntryPermit->getBy($where);
                // $where=[
                //     // 'transporter_entry_permits.tep_category'=>'OUTBOUND',//$category
                //     'transporter_entry_permits.checked_in_at is not null'=>null,
                //     'transporter_entry_permits.checked_out_at is null'=>null,
                //     'people_customers.id' => $id_customer,
                //     'IF(transporter_entry_permit_request_tep.id is NULL, true , (transporter_entry_permit_uploads.id_upload = "'.$id_upload.'"))'=> null,
                // ];
                // $vehicle2=$this->transporterEntryPermit->getBy($where);
                // // print_debug($this->db->last_query());
                // if (empty($vehicle2)) {
                //     $where=[
                //         // 'transporter_entry_permits.tep_category'=>'OUTBOUND',//$category
                //         'transporter_entry_permits.checked_in_at is not null'=>null,
                //         'transporter_entry_permits.checked_out_at is null'=>null,
                //         'ref_people.id' => $id_customer,
                //         'IF(transporter_entry_permit_request_tep.id is NULL, true, (transporter_entry_permit_uploads.id_upload = "'.$id_upload.'"))'=> null,
                //     ];
                //     $vehicle2=$this->transporterEntryPermit->getBy($where);
                // }
                $tep_data = $this->transporterEntryPermit->getOutstandingTep([
                    'should_checked_in' => true,
                    'outstanding_checked_out' => true,
                    'id_customer' => $id_customer,
                    'active_days' => 30,
                    'id_upload' => $id_upload,
                ]);
            }

            // $vehicles = array_merge($vehicle1, $vehicle2);
            $vehicles = $tep_data;//array_merge($vehicle1, $vehicle2)
            $vehicles_uniq = [];
            $baru = [];
            foreach($vehicles AS $vehicle){
                $vehicles_uniq[$vehicle['id']] = $vehicle;
            }
            $i = 0;
            foreach($vehicles_uniq AS $vehicles_un){
                $baru[$i++] = $vehicles_un;
            }
            $vehicle = $baru;
        }
        header('Content-Type: application/json');
        echo json_encode($vehicle);
    }

    /**
     * Get plat nomer by internal or external.
     */
    public function get_heavy_equipment(){
        $jenis=$this->input->post('jenis');
        if($jenis=="internal"){
            $heavyEquipment=$this->heavyEquipment->getAll(['branch' => get_active_branch_id()]);
        }else{
            $heavyEquipment=$this->heavyEquipmentEntryPermit->getHEEPIn();
        }
        header('Content-Type: application/json');
        echo json_encode($heavyEquipment);
    }

    /**
     * Get vas and resources component.
     */
    public function get_vas_and_resources(){
        $id_work_order=$this->input->post('id');
        $workOrder = $this->workOrder->getWorkOrderById($id_work_order);
        $components = $this->component->getByHandlingType($workOrder['id_handling_type'], $workOrder['id_handling']);
        $components_sort = array_column($components, 'id');
        array_multisort($components_sort, SORT_ASC, $components);
        header('Content-Type: application/json');
        echo json_encode($components);
    }
    /**
     * Get labours.
     */
    public function get_labours(){
        $branchIdVms= get_active_branch('id_branch_vms');
        $labours = $this->guest->getLabours($branchIdVms);
        header('Content-Type: application/json');
        echo json_encode($labours);
    }

    /**
     * Get goods by no pallet.
     */
    public function get_goods(){
        $no_pallet=$this->input->post('no_pallet');
        $reportGoods = $this->reportModel->getReportSummaryGoodsExternal([
            'no_pallet' => $no_pallet,
        ]);
        
        header('Content-Type: application/json');
        echo json_encode($reportGoods);
    }
    /**
     * Store upload data.
     */
    public function upload()
    {
        $result = [];

        foreach ($_FILES as $file => $data) {
            $fileName = rand(100, 999) . '_' . time() . '_' . $data['name'];
            $upload = $this->upload_to($file,$fileName);
            $result[$file] = $upload;
        }
        header('Content-Type: application/json');
        echo json_encode($result);
    }
    public function upload_to($file, $fileName = null, $location = null, $fileType = null)
    {
        $config['upload_path'] = is_null($location) ? FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'temp' : $location;
        $config['allowed_types'] = is_null($fileType) ? 'gif|jpg|jpeg|png|pdf|xls|xlsx|doc|docx|ppt|pptx|txt|zip|rar' : $fileType;
        $config['max_size'] = 3000;
        $config['max_width'] = 5000;
        $config['max_height'] = 5000;
        $config['file_ext_tolower'] = true;
        if (!empty($fileName)) {
            $config['file_name'] = $fileName;
        }

        $this->load->library('upload', $config);

        $errors = [];
        $data = [];

        if (is_array($_FILES[$file]['name'])) {
            $totalUploads = count($_FILES[$file]['name']);
            $status = true;
            for ($i = 0; $i < $totalUploads; $i++) {
                $_FILES[$file . '_multiple']['name'] = $_FILES[$file]['name'][$i];
                $_FILES[$file . '_multiple']['type'] = $_FILES[$file]['type'][$i];
                $_FILES[$file . '_multiple']['tmp_name'] = $_FILES[$file]['tmp_name'][$i];
                $_FILES[$file . '_multiple']['error'] = $_FILES[$file]['error'][$i];
                $_FILES[$file . '_multiple']['size'] = $_FILES[$file]['size'][$i];

                if ($this->upload->do_upload($file . '_multiple')) {
                    $data[] = $this->upload->data();
                } else {
                    $errors = $this->upload->display_errors();
                    $status = false;
                    break;
                }
            }
        } else {
            if ($this->upload->do_upload($file)) {
                $data = $this->upload->data();
                $status = true;
            } else {
                $errors = $this->upload->display_errors();
                $status = false;
            }
        }

        return [
            'status' => $status,
            'errors' => $errors,
            'data' => $data
        ];
    }
    /**
     * Delete temporary upload
     */
    public function delete_temp_upload()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $fileTemp = $this->input->post('file');
            $delete = $this->delete_file($fileTemp);
            header('Content-Type: application/json');
            echo json_encode(['status' => $delete]);
        }
    }
    /**
     * Delete file
     * @param $file
     * @param null $base
     * @return bool
     */
    public function delete_file($file, $base = null)
    {
        $directory = is_null($base) ? FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'temp' : $base;
        $filePath = $directory . DIRECTORY_SEPARATOR . $file;
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
    }
    /**
     * Release job tally.
     */
    public function release_job()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_TAKE_JOB);

        $this->form_validation->set_rules('id', 'Job data', 'trim|required|max_length[50]');

        if ($this->validate(['id' => 'trim|required|max_length[50]'])) {
            $workOrderId = $this->input->post('id');

            $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
            $takeJob = $this->workOrder->updateWorkOrder([
                'status' => WorkOrderModel::STATUS_QUEUED,
                'id_transporter_entry_permit' => NULL,
                'id_vehicle' => NULL,
                'id_armada' => NULL,
                'armada_type' => NULL,
                'shipping_route' => NULL,
                'armada_description' => NULL,
                'expedition' => NULL,
            ], $workOrderId);

            if ($takeJob) {
                flash('warning', "Job was released by <strong>{$workOrder['tally_name']}</strong>, another user allowed to retake.");
            } else {
                flash('danger', "Releasing job <strong>{$workOrder['no_work_order']}</strong> failed");
            }
        }

        redirect('tally');
    }

    /**
     * Complete job tally.
     */
    public function complete_job($workOrderId)
    {
        if ($this->input->post('source_submission') != 'modal') {
            flash('danger', "Invalid submission, try to clear cache and retry");
        }
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_COMPLETE);
        $uploadPassed = true;
        //untuk membuat text
        // $workOrderId = $this->input->post('id');
        $activity_type = $this->input->post('activity_type');
        $description_vas = $this->input->post('description_vas');
        $resources = $this->input->post('resources');
        $heavy_equipment = if_empty($this->input->post('heavy_equipment'),[]);
        $forklift = if_empty($this->input->post('forklift'),[]);
        $forklift_external = if_empty($this->input->post('forklift_external'),[]);
        $operator_name = $this->input->post('operator_name');
        $is_owned = $this->input->post('is_owned');
        $capacity = $this->input->post('capacity');
        $labours = if_empty($this->input->post('labours'), []);
        $space = $this->input->post('space');
        $pallet = $this->input->post('pallet');
        if (is_null($pallet)) {
            $pallet = 0;
        }

        $branchPallet = get_active_branch('stock_pallet');
        $initialPallet = get_active_branch('initial_pallet');
        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
        $booking = $this->booking->getBookingById($workOrder['id_booking']);
        if ($booking['category'] == 'OUTBOUND' && $booking['outbound_type'] == PeopleModel::OUTBOUND_CASH_AND_CARRY && !empty($booking['payout_until_date']) && $booking['payout_until_date'] < date('Y-m-d')) {
            flash('danger', "Booking customer cash & carry payout date is passed, please contact Finance to update the payment!", 'tally');
        }
        $peopleSpace = $this->people->getById($workOrder['id_customer']);
        $peopleSpace = $peopleSpace['contract'];
        if (count($heavy_equipment)==2) {
            $is_owned = 'BOTH';
        }elseif (in_array("INTERNAL",$heavy_equipment)) {
            $is_owned = 'OWNED';
        }elseif (in_array("EXTERNAL",$heavy_equipment)) {
            $is_owned = 'LEASED';
        }
        //cek space
        if (!empty($space)) {
            $usedSpace = $this->workOrder->getUsedSpace($workOrder['id_customer']);
            $usedSpace = $usedSpace['total_space'];
            if($workOrder['category_booking'] == "INBOUND" && !empty($space)){
                $space = $space*-1;
            }
            $sisaSpace = $peopleSpace + ($usedSpace+$space);
            // if($sisaSpace<0){
            //     flash('danger', "Complete Job {$workOrder['no_work_order']} failed, because no more space");
            //     redirect('tally');
            // }
        }
        //cek pallet
        if (!empty($pallet)) {
            $usedPallet = $this->workOrder->getUsedPallet();
            $usedPallet = $usedPallet['total_pallet'];
            $tempPallet = $pallet;
            if($workOrder['category_booking'] == "INBOUND" && !empty($pallet)){
                $pallet = $pallet*-1;
            }
            $sisaPallet = $branchPallet + ($usedPallet+$pallet) - ($branchPallet - $initialPallet);

            // print_debug($sisaSpace);
            if($sisaPallet<0){
                flash('danger', "Complete Job {$workOrder['no_work_order']} failed, because no more stock pallet");
                redirect('tally');
            }
        }

        $components = [];
        $componentResources = $this->component->getBy(['id'=>$resources]);
        $countKalmar = [];
        $countForklift = [];
        foreach($forklift AS $id){
            $heavy = $this->heavyEquipment->getById($id);
            if($heavy['type']=='FORKLIFT'){
                $countForklift[] = $heavy;
            }
            if($heavy['type']=='REACH STACKER'){
                $countKalmar[] = $heavy;
            }
        }
        $this->db->trans_start();
        foreach ($componentResources as $resources) {
            if($resources['handling_component']=='Forklift'){
                $this->workOrderComponent->createWorkOrderComponent([
                    "id_work_order" => $workOrderId,
                    "id_component" => $resources['id'],//id forklift
                    "quantity" => (count($countForklift)),
                    "operator_name" => count($countForklift)!=0 ? $operator_name : (count($countKalmar)==0 ? $operator_name : NULL),
                    "is_owned" => count($countForklift)!=0 ? $is_owned : (count($countKalmar)==0 ? $is_owned : NULL),
                    "capacity" => count($countForklift)!=0 ? $capacity : (count($countKalmar)==0 ? $capacity : NULL),
                    "description" => NULL,
                    'status' => 'APPROVED',
                ]);
                $id_work_order_component = $this->db->insert_id();
                if (in_array("INTERNAL",$heavy_equipment)) {
                    foreach ($forklift as $forklift) {
                        $this->workOrderComponentHeavyEquipments->create([
                            'id_work_order_component' => $id_work_order_component,
                            'id_heavy_equipment' => $forklift,
                        ]);
                    }
                }
                if(in_array("EXTERNAL",$heavy_equipment)){
                    foreach ($forklift_external as $forklift_external) {
                        $this->workOrderComponentHeeps->create([
                            'id_work_order_component' => $id_work_order_component,
                            'id_heep' => $forklift_external,
                        ]);
                    }
                }
                if(!empty($countKalmar)){
                    $this->workOrderComponent->createWorkOrderComponent([
                        "id_work_order" => $workOrderId,
                        "id_component" => 2,//id kalmar
                        "quantity" => (count($countKalmar)),
                        "operator_name" => count($countKalmar)!=0 ? $operator_name : NULL,
                        "is_owned" => count($countKalmar)!=0 ? $is_owned : NULL,
                        "capacity" => count($countKalmar)!=0 ? $capacity : NULL,
                        "description" => NULL,
                        'status' => 'APPROVED',
                    ]);
                }
            }
            if($resources['handling_component']=='Labours'){
                $this->workOrderComponent->createWorkOrderComponent([
                    "id_work_order" => $workOrderId,
                    "id_component" => $resources['id'],
                    "quantity" => count($labours),
                    "operator_name" => NULL,
                    "is_owned" => NULL,
                    "capacity" => NULL,
                    "description" => NULL,
                    'status' => 'APPROVED',
                ]);
                $id_work_order_component = $this->db->insert_id();
                foreach ($labours as $labour) {
                    $this->workOrderComponentLabours->create([
                        'id_work_order_component' => $id_work_order_component,
                        'id_labour' => $labour,
                    ]);
                }
            }
            if($resources['handling_component']=='Pallet'){
                array_push($components,[
                    "id_work_order" => $workOrderId,
                    "id_component" => $resources['id'],
                    "quantity" => is_null($tempPallet)?0:$tempPallet,
                    "operator_name" => NULL,
                    "is_owned" => NULL,
                    "capacity" => NULL,
                    "description" => NULL,
                    'status' => 'APPROVED',
                ]);
            }
        }
        if(!empty($activity_type)) {
            foreach ($activity_type as $value) {
                if ($value==13) {//if others
                    array_push($components,[
                        "id_work_order" => $workOrderId,
                        "id_component" => $value,//vas component
                        "quantity" => 1,
                        "operator_name" => NULL,
                        "is_owned" => NULL,
                        "capacity" => NULL,
                        "description" => $description_vas,
                        'status' => 'APPROVED',
                    ]);
                }else{
                    array_push($components,[
                        "id_work_order" => $workOrderId,
                        "id_component" => $value,//vas component
                        "quantity" => 1,
                        "operator_name" => NULL,
                        "is_owned" => NULL,
                        "capacity" => NULL,
                        "description" => NULL,
                        'status' => 'APPROVED',
                    ]);
                }
            }
        }
        // print_debug($components);
        if (!empty($activity_type) || !empty($components)) {
            $this->workOrderComponent->createWorkOrderComponent($components);
        }
        $this->db->trans_complete();

        $containers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderId, true);
        $bookingTypes = $this->bookingType->getBookingTypeById($workOrder['id_booking_type']);
        if (empty($containers)) {
            $containers = $this->handlingContainer->getHandlingContainersByHandling($workOrder['id_handling']);
        }
        $containerGoods = $this->handlingGoods->getHandlingGoodsByHandling($workOrder['id_handling'], true);
        $text1 = $workOrder['handling_type']." ".$bookingTypes['default_document']." AJU ".substr($workOrder['no_reference'],-4)."\n";
        if (isset($containers[0]['no_container'])) {
            $text2 = $containers[0]['no_container'];
        } else if (isset($containerGoods[0]['ex_no_container'])) {
            $text2 = 'EX ' . $containerGoods[0]['ex_no_container'];
        } else {
            $text2 = 'NO EX CONTAINER NUMBER';
        }
        $text3 = $workOrder['customer_name'];
        $text4 = "COMPLETED BY ".strtoupper(UserModel::authenticatedUserData('username'));
        $no_police = '';

        if ($workOrder['id_transporter_entry_permit']!= null) {
            $tep = $this->transporterEntryPermit->getByIdNonBase($workOrder['id_transporter_entry_permit']);
            $no_police = $tep[0]['receiver_no_police'];
        }
        if ($workOrder['id_vehicle']!= null) {
            $tep = $this->vehicle->getBy(['ref_vehicles.id'=>$workOrder['id_vehicle']]);
            $no_police = $tep[0]['no_plate'];
        }
        // unknown tep
        if (empty($workOrder['id_transporter_entry_permit']) && empty($workOrder['id_vehicle'])) {
            $no_police = '0';
        }

        if ($text2=='NO EX CONTAINER NUMBER') {
            $hasilText = date('d M Y H:i:s')."\n".$text1.$no_police."\n".$text3."\n".$text4;
        } else {
            if ($no_police=='') {
                $hasilText = date('d M Y H:i:s')."\n".$text1.$text2.$no_police."\n".$text3."\n".$text4;
            }else{
                $hasilText = date('d M Y H:i:s')."\n".$text1.$text2." | ".$no_police."\n".$text3."\n".$text4;
            }
        }
        //sampai sini hasil text
        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
        $handling_type = $this->handlingType->getHandlingTypeById($workOrder['id_handling_type']);
        $totalDuration = (strtotime($workOrder['taken_at']) + (($handling_type['duration']) * 60));
        $dateTime = strtotime(date('Y-m-d H:i:s'));
        $temp_container_goods = [];

        $temp_container_goods = $this->workOrderGoods->getWorkOrderGoodsByBookingId($workOrder['id_booking'], true);

        //get job detail by work order
        $jobContainers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderId, true);
        foreach ($jobContainers as &$container) {
            $containerGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($container['id']);
            $container['goods'] = $containerGoods;
            foreach ($containerGoods as $jobItem) {
                if ($jobItem['quantity'] <= 0) {
                    flash('danger', 'Quantity ' . $jobItem['goods_name'] . ' in container ' . $container['no_container'] . ' cannot be empty', 'tally');
                }
            }
        }
        $jobGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderId, true, true);
        foreach ($jobGoods as $jobItem) {
            if ($jobItem['quantity'] <= 0) {
                flash('danger', 'Quantity ' . $jobItem['goods_name'] . ' cannot be empty', 'tally');
            }
        }

        //get overtime status
        $overtime_status = [];
        $position = [];
        if (!empty($jobContainers)) {

            foreach ($jobContainers as $container) {
                $overtime_status[] = $container['overtime_status'];
                $position[] = $container['id_position'];
                if (key_exists('goods', $container)) {
                    foreach ($container['goods'] as $item) {
                        $overtime_status[] = $item['overtime_status'];
                        $position[] = $item['id_position'];
                    }
                }

            }
        }

        if (!empty($jobGoods)) {
            foreach ($jobGoods as $item) {
                $overtime_status[] = $item['overtime_status'];
                $position[] = $item['id_position'];
            }
        }
        $uploadedPhoto = "";
        $photo_names = $this->input->post('photo_name');
        $id_customer = $this->input->post('id_customer');

        $people = $this->people->getById($id_customer);
        $whatsapp_group = $people['whatsapp_group'];

        $branch = get_active_branch();
        $whatsapp_group_internal = $branch['whatsapp_group'];
        if ($photo_names[0] != null) {

            $i = 0;
            $this->db->trans_start();
            foreach ($photo_names as $photo_name) {
                $files = $this->input->post('attachments_' . $i . '_name');
                if (empty($files)) {
                    flash('danger', "Complete Job {$workOrder['no_work_order']} failed, You haven't uploaded a photo yet");
                    redirect('tally');
                }
                $uploadedPhoto = 'temp/' . $files[0];
                $photo_name = $photo_names[$i];
                $text5 = strtoupper($photo_name);
                $tempText = $hasilText;
                $tempText .= "\n" . $text5;
                $watermark = $this->watermark($uploadedPhoto, $photo_name, $tempText);
                $uploadedPhoto = 'temp/' . $files[0];
                $destFile = 'tally_complete/' . date('Y/m') . '/' . $files[0];

                $status = $this->uploader->setDriver('s3')->move($uploadedPhoto, $destFile);
                if (!$status) {
                    flash('danger', "Complete Job {$workOrder['no_work_order']} failed, uplod server fail");
                    redirect('tally');
                }
                $path = 'tally_complete/' . date('Y/m') . '/' . $files[0];
                $save = $this->workOrderPhoto->createWorkOrderPhoto([
                    'id_work_order' => $this->input->post('id'),
                    'photo' => $path,
                    'description' => $photo_name,
                    'created_by' => UserModel::authenticatedUserData('id'),
                ]);
                $i++;
            }
            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                $i = 0;
                foreach ($photo_names as $photo_name) {
                    $files = $this->input->post('attachments_' . $i . '_name');
                    $path = 'tally_complete/' . date('Y/m') . '/' . $files[0];
                    $this->send_message($path, $whatsapp_group, $whatsapp_group_internal);
                    $i++;
                }
            }else {
                flash('danger', "Complete Job {$workOrder['no_work_order']} failed, because upload photo fail");
                redirect('tally');
            }
        }
        //complete job ==> jika menambahkan if else flash maka tambahkan juga pada fungsi complete_job_checking
        if ($totalDuration > $dateTime) {
            flash('danger', "Complete Job {$workOrder['no_work_order']} failed, please wait until duration time is finished !", 'tally');
            $uploadPassed = false;
        } else {
            if (empty($jobContainers) && empty($jobGoods)) {
                flash('danger', "Complete Job {$workOrder['no_work_order']} failed, please fill out the goods or container field and set overtime status !", 'tally');
                $uploadPassed = false;
            } else {
                if (!empty($position) || !empty($overtime_status)) {
                    if ((in_array('0', $position) && $workOrder['handling_type'] == "STRIPPING") || (in_array('0', $position) && $workOrder['handling_type'] == "UNLOAD")) {
                        flash('danger', "Complete Job {$workOrder['no_work_order']} failed, Please select the position type!", 'tally');
                        $uploadPassed = false;
                    } elseif (false && in_array('0', $overtime_status)) {
                        flash('danger', "Complete Job {$workOrder['no_work_order']} failed, Please set overtime status!", 'tally');
                        $uploadPassed = false;
                    } else {
                        $userId = UserModel::authenticatedUserData('id');

                        $this->db->trans_start();
                        $this->workOrder->updateWorkOrder([
                            'man_power' => count($labours),
                            'space' => abs($space),
                            'stock_pallet' => abs($pallet),
                            'status' => WorkOrderModel::STATUS_COMPLETED,
                            'completed_at' => date('Y-m-d H:i:s'),
                            'completed_by' =>UserModel::authenticatedUserData('id'),
                            'updated_by' => $userId,
                            'updated_at' => sql_date_format('now')
                        ], $workOrderId);

                        //add status history for handover only
                        if($workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_HANDOVER_TAKEN){
                            $this->workOrder->updateWorkOrder([
                                'updated_by' => UserModel::authenticatedUserData('id'),
                                'updated_at' => date('Y-m-d H:i:s'),
                                'status_validation' => WorkOrderModel::STATUS_VALIDATION_HANDOVER_COMPLETED,
                            ], $workOrderId);

                            $this->statusHistory->create([
                                'type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
                                'id_reference' => $workOrderId,
                                'status' => WorkOrderModel::STATUS_VALIDATION_HANDOVER_COMPLETED,
                                'description' => null,
                                'data' => json_encode([
                                    'handover_user_id' => UserModel::authenticatedUserData('id'),
                                ])
                            ]);
                        }
                        $this->db->trans_complete();

                        if ($this->db->trans_status()) {
                            $work_order_updated = $this->workOrder->getWorkOrderById($workOrderId);
                            $booking = $this->booking->getBookingById($work_order_updated['id_booking']);
                            $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($booking['id']);
                            $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($booking['id']);

                            // CONTAINER STRIPPING
                            $getContainerStocksByBooking = $this->workOrderContainer->getContainerStocksByBooking($booking['id']);
                            foreach ($bookingContainers as $bookingContainer) {
                                $jobBookingContainers = array_filter($getContainerStocksByBooking, function ($containerJob) use ($bookingContainer) {
                                    $getBooking = $this->booking->getBookingById($bookingContainer['id_booking']);
                                    $getWorkorder = $this->workOrder->getWorkOrderById($containerJob['id_work_order']);
                                    if ($containerJob['id_booking'] == $bookingContainer['id_booking'] && $containerJob['id_container'] == $bookingContainer['id_container'] && !empty($containerJob['completed_at']) && $getWorkorder['status'] == WorkOrderModel::STATUS_COMPLETED && $getBooking['category'] == BookingTypeModel::CATEGORY_INBOUND && $getWorkorder['handling_type'] == "STRIPPING") {
                                        return true;
                                    }
                                    return false;
                                });

                                if (!empty($jobBookingContainers)) {
                                    $container_details[] = array_shift($jobBookingContainers);
                                }

                            }

                            $jobBookingContainers = array_filter($getContainerStocksByBooking, function ($containerJob) {
                                $getWorkorder = $this->workOrder->getWorkOrderById($containerJob['id_work_order']);
                                $getBooking = $this->booking->getBookingById($getWorkorder['id_booking']);
                                if (!empty($containerJob['completed_at']) && $getWorkorder['status'] == WorkOrderModel::STATUS_COMPLETED && $getWorkorder['handling_type'] == "STRIPPING" && $getBooking['category'] == BookingTypeModel::CATEGORY_INBOUND) {
                                    return true;
                                }
                                return false;
                            });
                            $container_from_job = array_column($jobBookingContainers, 'id_container');
                            $container_from_booking = array_column($bookingContainers, 'id_container');
                            $counter_check_container = count(array_intersect($container_from_job, $container_from_booking));

                            // LCL UNLOAD
                            $getGoodsStocksByBooking = $this->workOrderGoods->getGoodsStocksByBooking($booking['id']);
                            $jobBookingGoods = array_filter($getGoodsStocksByBooking, function ($item) {
                                $getWorkorder = $this->workOrder->getWorkOrderById($item['id_work_order']);
                                $getBooking = $this->booking->getBookingById($getWorkorder['id_booking']);
                                if (!empty($item['completed_at']) && $getWorkorder['status'] == WorkOrderModel::STATUS_COMPLETED && $getWorkorder['handling_type'] == "UNLOAD" && $getBooking['category'] == BookingTypeModel::CATEGORY_INBOUND) {
                                    return true;
                                }
                                return false;
                            });
                            $goods_from_job = array_column($jobBookingGoods, 'id_goods');
                            $goods_from_booking = array_column($bookingGoods, 'id_goods');
                            $counter_check_goods = count(array_intersect($goods_from_job, $goods_from_booking));

                            $active_branch = get_active_branch('branch');
                            $whatsapp_group = get_setting('whatsapp_group_admin');

                            $qty_goods_from_job_cek = array_column($temp_container_goods, 'quantity', 'id_goods');
                            $qty_goods_from_booking_cek = array_column($bookingGoods, 'quantity', 'id_goods');
                            $cek_qty_goods = count(array_intersect_assoc($qty_goods_from_job_cek, $qty_goods_from_booking_cek));
                            // print_debug($temp_container_goods);

                            // override coding above for distinct a goods in many containers
                            $qty_goods_from_job_cek = array_reduce($temp_container_goods, function($carry, $item) {
                                //$key = if_empty($item['ex_no_container'], 'no-container') . '-' . $item['id_goods'];
                                $key = $item['id_goods'];
                                $carry[$key] = get_if_exist($carry, $key, 0) + $item['quantity'];
                                return $carry;
                            }, []);
                            $qty_goods_from_booking_cek = array_reduce($bookingGoods, function($carry, $item) {
                                //$key = if_empty($item['ex_no_container'], if_empty($item['booking_no_container'], 'no-container')) . '-' . $item['id_goods'];
                                $key = $item['id_goods'];
                                $carry[$key] = get_if_exist($carry, $key, 0) + $item['quantity'];
                                return $carry;
                            }, []);
                            $cek_qty_goods = count(array_intersect_assoc($qty_goods_from_job_cek, $qty_goods_from_booking_cek));

                            $kirimWa = true;
                            if(!empty($jobContainers)){
                                if(!empty($bookingContainers) && (count($container_from_booking) == $counter_check_container) && empty(array_diff($qty_goods_from_job_cek, $qty_goods_from_booking_cek)) && $handling_type['handling_type'] == "STRIPPING"){
                                    $qty_goods_from_job = $qty_goods_from_job_cek; //array_column($temp_container_goods, 'quantity', 'id_goods');
                                    $qty_goods_from_booking = $qty_goods_from_booking_cek; //array_column($bookingGoods, 'quantity', 'id_goods');
                                    $qty_check_goods = array_diff($qty_goods_from_job, $qty_goods_from_booking);
                                    $add_check_goods = array_diff_key($qty_goods_from_job, $qty_goods_from_booking);
                                    $temp_different_goods = [];
                                    $temp_add_goods = [];
                                    //search in array booking different qty goods
                                    foreach ($qty_check_goods as $id_goods => $qty_check_good) {
                                        foreach ($bookingGoods as $key => $bookingGood) {
                                            if ($bookingGood['id_goods'] == $id_goods) {
                                                $bookingGood['diff_qty'] = $qty_check_good - $bookingGood['quantity'];
                                                $temp_different_goods[] = $bookingGood;
                                            }
                                        }
                                    }

                                    //search in array booking add goods
                                    foreach ($add_check_goods as $id_goods => $add_check_good) {
                                        foreach ($temp_container_goods as $key => $temp_container_good) {
                                            if ($temp_container_good['id_goods'] == $id_goods) {
                                                $temp_add_goods[] = $temp_container_good;
                                            }
                                        }
                                    }
                                    $data_add = "";
                                    if(!empty($temp_different_goods) || !empty($temp_add_goods)){
                                        $data_add = "\nTambahan Goods : ";
                                        $adaBaru = 0;
                                        if(!empty($temp_different_goods)){
                                            foreach ($temp_different_goods as $temp_different_goods) {
                                                if($temp_different_goods['diff_qty']<0){
                                                    $kirimWa = false;
                                                }
                                                $data_add .= "\n-".$temp_different_goods['goods_name']." (".$temp_different_goods['diff_qty'].") (quantity change)";
                                            }
                                        }
                                        if(!empty($temp_add_goods)){
                                            foreach ($temp_add_goods as $temp_add_goods) {
                                                if ((int)$temp_different_goods['quantity']>0) {
                                                    $data_add .= "\n-".$temp_add_goods['goods_name']." (".(int)$temp_different_goods['quantity'].") (new Goods)";
                                                    $adaBaru++;
                                                }
                                            }
                                            if($adaBaru==0){
                                                $data_add = "";
                                            }
                                        }
                                    }
                                    $no_reference = substr($booking['no_reference'], -4);
                                    $category = strtolower($booking['category']);
                                    $completed_date = date('d F Y H:i', strtotime($work_order_updated['completed_at']));
                                    $data_stripping = "[INBOUND COMPLETE] Inbound {$workOrder['customer_name']} aju {$no_reference} {$active_branch} telah diselesaikan pada {$completed_date} silahkan melakukan proses selanjutnya.";
                                    $data_stripping = $data_stripping . $data_add;
                                }

                                if ($branch['branch_type']=='PLB' && $branch['id']!='4' && isset($data_stripping) && !empty($whatsapp_group) && $kirimWa) {
                                    // $this->send_message($data_stripping, $whatsapp_group, false);
                                }
                            }else{
                                if(!empty($jobGoods)){
                                    if(!empty($bookingGoods) && (count($qty_goods_from_booking_cek)  == $cek_qty_goods) && $handling_type['handling_type'] == "UNLOAD"){
                                        $qty_goods_from_job = array_column($jobGoods, 'quantity', 'id_goods');
                                        $qty_goods_from_booking = array_column($bookingGoods, 'quantity', 'id_goods');
                                        $qty_check_goods = array_diff($qty_goods_from_job, $qty_goods_from_booking);
                                        $add_check_goods = array_diff_key($qty_goods_from_job, $qty_goods_from_booking);
                                        $temp_different_goods = [];
                                        $temp_add_goods = [];
                                        //search in array booking different qty goods
                                        foreach ($qty_check_goods as $id_goods => $qty_check_good) {
                                            foreach ($bookingGoods as $key => $bookingGood) {
                                                if ($bookingGood['id_goods'] == $id_goods) {
                                                    $bookingGood['diff_qty'] = $qty_check_good - $bookingGood['quantity'];
                                                    $temp_different_goods[] = $bookingGood;
                                                }
                                            }
                                        }

                                        //search in array booking add goods
                                        foreach ($add_check_goods as $id_goods => $add_check_good) {
                                            foreach ($jobGoods as $key => $jobGood) {
                                                if ($jobGood['id_goods'] == $id_goods) {
                                                    $temp_add_goods[] = $jobGood;
                                                }
                                            }
                                        }
                                        $data_add = "";
                                        if(!empty($temp_different_goods) || !empty($temp_add_goods)){
                                            $data_add = "\nTambahan Goods : ";
                                            $adaBaru = 0;
                                            if(!empty($temp_different_goods)){
                                                foreach ($temp_different_goods as $temp_different_goods) {
                                                    if($temp_different_goods['diff_qty']<0){
                                                        $kirimWa = false;
                                                    }
                                                    $data_add .= "\n-".$temp_different_goods['goods_name']." (".$temp_different_goods['diff_qty'].") (quantity change)";
                                                }
                                            }
                                            if(!empty($temp_add_goods)){
                                                foreach ($temp_add_goods as $temp_add_goods) {
                                                    if ((int)$temp_different_goods['quantity']>0) {
                                                        $data_add .= "\n-".$temp_add_goods['goods_name']." (".(int)$temp_different_goods['quantity'].") (new Goods)";
                                                        $adaBaru++;
                                                    }
                                                }
                                                if($adaBaru==0){
                                                    $data_add = "";
                                                }
                                            }
                                        }

                                        $no_reference = substr($booking['no_reference'], -4);
                                        $category = strtolower($booking['category']);
                                        $completed_date = date('d F Y H:i', strtotime($work_order_updated['completed_at']));
                                        $data_unload = "[INBOUND COMPLETE] Inbound {$workOrder['customer_name']} aju {$no_reference} {$active_branch} telah diselesaikan pada {$completed_date} silahkan melakukan proses selanjutnya.";
                                        $data_unload = $data_unload . $data_add;
                                    }

                                    if ($branch['branch_type']=='PLB' && $branch['id']!='4' && isset($data_unload) && !empty($whatsapp_group) && $kirimWa) {
                                        // $this->send_message($data_unload, $whatsapp_group, false);
                                    }
                                }

                            }
                            //tally complete point +1, ingat ada dua complete
                            $data = json_encode($workOrder);
                            $id_employee = $this->employee->getBy(
                                [
                                    'ref_employees.id_user'=>  $workOrder['taken_by']
                                ]
                                ,true);
                            $dashboard_status = get_active_branch('dashboard_status');
                            $id_employee = $id_employee['id'];
                            if ($dashboard_status==1) {
                                if($workOrder['id_branch']!=2 || $workOrder['id_customer']!=9151){
                                    $this->allowance->create([
                                        [
                                            'id_employee' => $id_employee,
                                            'id_component' => 1,
                                            'date' => date('Y-m-d'),
                                            'different_date' => null,
                                            'data' => $data,
                                            'point' => 1,
                                            'description' => 'completed job',
                                        ],
                                        [
                                            'id_employee' => $id_employee,
                                            'id_component' => 20,
                                            'date' => date('Y-m-d'),
                                            'different_date' => null,
                                            'data' => $data,
                                            'point' => 1,
                                            'description' => 'completed job',
                                        ]
                                    ]);
                                }
                            }
                            //$this->over_space_reminder($workOrder['id_customer'], true);
                            flash('success', "Job {$workOrder['no_work_order']} successfully completed");
                        } else {
                            flash('danger', "Complete Job {$workOrder['no_work_order']} failed");
                            $uploadPassed = false;
                        }
                    }
                } else {
                    $userId = UserModel::authenticatedUserData('id');
                    $this->db->trans_start();

                    $this->workOrder->updateWorkOrder([
                        'man_power' => count($labours),
                        'space' => abs($space),
                        'stock_pallet' => abs($pallet),
                        'status' => WorkOrderModel::STATUS_COMPLETED,
                        'completed_at' => date('Y-m-d H:i:s'),
                        'completed_by' =>UserModel::authenticatedUserData('id'),
                        'updated_by' => $userId,
                        'updated_at' => sql_date_format('now')
                    ], $workOrderId);

                    //add status history
                    if($workOrder['status_validation'] == WorkOrderModel::STATUS_VALIDATION_HANDOVER_TAKEN){
                        $this->workOrder->updateWorkOrder([
                            'updated_by' => UserModel::authenticatedUserData('id'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'status_validation' => WorkOrderModel::STATUS_VALIDATION_HANDOVER_COMPLETED,
                        ], $workOrderId);

                        $this->statusHistory->create([
                            'type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
                            'id_reference' => $workOrderId,
                            'status' => WorkOrderModel::STATUS_VALIDATION_HANDOVER_COMPLETED,
                            'description' => null,
                            'data' => json_encode([
                                'handover_user_id' => UserModel::authenticatedUserData('id'),
                            ])
                        ]);
                    }
                    $this->db->trans_complete();

                    if ($this->db->trans_status()) {
                        $work_order_updated = $this->workOrder->getWorkOrderById($workOrderId);
                        $booking = $this->booking->getBookingById($work_order_updated['id_booking']);
                        $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($booking['id']);
                        $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($booking['id']);

                        $container_details = [];
                        $getContainerStocksByBooking = $this->workOrderContainer->getContainerStocksByBooking($booking['id']);
                        foreach ($bookingContainers as $bookingContainer) {
                            $jobBookingContainers = array_filter($getContainerStocksByBooking, function ($containerJob) use ($bookingContainer) {
                                $getBooking = $this->booking->getBookingById($bookingContainer['id_booking']);
                                $getWorkorder = $this->workOrder->getWorkOrderById($containerJob['id_work_order']);
                                if ($containerJob['id_booking'] == $bookingContainer['id_booking'] && $containerJob['id_container'] == $bookingContainer['id_container'] && !empty($containerJob['completed_at']) && $getWorkorder['status'] == WorkOrderModel::STATUS_COMPLETED && $getBooking['category'] == BookingTypeModel::CATEGORY_INBOUND && $getWorkorder['handling_type'] == "STRIPPING") {
                                    return true;
                                }
                                return false;
                            });

                            if (!empty($jobBookingContainers)) {
                                $container_details[] = array_shift($jobBookingContainers);
                            }

                        }

                        $goods_details = [];
                        $getGoodsStocksByBooking = $this->workOrderGoods->getGoodsStocksByBooking($booking['id']);
                        foreach ($bookingGoods as $bookingItem) {
                            $jobBookingGoods = array_filter($getGoodsStocksByBooking, function ($item) use ($bookingItem) {
                                $getBooking = $this->booking->getBookingById($bookingItem['id_booking']);
                                $getWorkorder = $this->workOrder->getWorkOrderById($item['id_work_order']);
                                if ($item['id_booking'] == $bookingItem['id_booking'] && $item['id_goods'] == $bookingItem['id_goods'] && !empty($item['completed_at']) && $getWorkorder['status'] == WorkOrderModel::STATUS_COMPLETED && $getBooking['category'] == BookingTypeModel::CATEGORY_INBOUND && $getWorkorder['handling_type'] == "UNLOAD") {
                                    return true;
                                }
                                return false;
                            });

                            if (!empty($jobBookingGoods)) {
                                $goods_details[] = array_shift($jobBookingGoods);
                            }
                        }

                        $active_branch = get_active_branch('branch');
                        $whatsapp_group = get_setting('whatsapp_group_admin');
                        $unique_containers = array_unique(array_column($container_details, 'id_container'));
                        if (!empty($jobContainers)) {
                            if (!empty($bookingContainers) && (count($bookingContainers) <= (count($unique_containers)))) {
                                $no_reference = substr($booking['no_reference'], -4);
                                $category = strtolower($booking['category']);
                                $completed_date = date('d F Y H:i', strtotime($work_order_updated['completed_at']));
                                $data_stripping = "[INBOUND COMPLETE] Inbound {$workOrder['customer_name']} aju {$no_reference} {$active_branch} telah diselesaikan pada {$completed_date} silahkan melakukan proses selanjutnya.";
                            }

                            if ($branch['branch_type']=='PLB' && $branch['id']!='4' && isset($data_stripping) && !empty($whatsapp_group)) {
                                // $this->send_message($data_stripping, $whatsapp_group, false);
                            }
                        } else {
                            $unique_goods = array_unique(array_column($goods_details, 'id_goods'));
                            if (!empty($jobGoods)) {
                                if (!empty($bookingGoods) && (count($bookingGoods) <= (count($unique_goods)))) {
                                    $no_reference = substr($booking['no_reference'], -4);
                                    $category = strtolower($booking['category']);
                                    $completed_date = date('d F Y H:i', strtotime($work_order_updated['completed_at']));
                                    $data_unload = "[INBOUND COMPLETE] Inbound {$workOrder['customer_name']} aju {$no_reference} {$active_branch} telah diselesaikan pada {$completed_date} silahkan melakukan proses selanjutnya.";
                                }

                                if ($branch['branch_type']=='PLB' && $branch['id']!='4' && isset($data_unload) && !empty($whatsapp_group)) {
                                    // $this->send_message($data_unload, $whatsapp_group, false);
                                }
                            }
                        }
                        //tally complete point +1, ingat ada dua complete
                        $data = json_encode($workOrder);
                        $id_employee = $this->employee->getBy(
                            [
                                'ref_employees.id_user'=>  $workOrder['taken_by']
                            ]
                            ,true);
                        $dashboard_status = get_active_branch('dashboard_status');
                        $id_employee = $id_employee['id'];
                        if ($dashboard_status==1) {
                            if($workOrder['id_branch']!=2 || $workOrder['id_customer']!=9151){
                                $this->allowance->create([
                                    [
                                        'id_employee' => $id_employee,
                                        'id_component' => 1,
                                        'date' => date('Y-m-d'),
                                        'different_date' => null,
                                        'data' => $data,
                                        'point' => 1,
                                        'description' => 'completed job',
                                    ],
                                    [
                                        'id_employee' => $id_employee,
                                        'id_component' => 20,
                                        'date' => date('Y-m-d'),
                                        'different_date' => null,
                                        'data' => $data,
                                        'point' => 1,
                                        'description' => 'completed job',
                                    ]
                                ]);
                            }
                        }
                        //$this->over_space_reminder($workOrder['id_customer'], true);
                        flash('success', "Job {$workOrder['no_work_order']} successfully completed");
                    } else {
                        flash('danger', "Complete Job {$workOrder['no_work_order']} failed");
                        $uploadPassed = false;
                    }

                    redirect('tally');
                }
            }
        }

        redirect('tally');
    }

    /**
     * Complete job tally checking.
     */
    public function complete_job_checking()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_COMPLETE);

        $checkPassed = true;
        $message = 'Ready to complete';

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $workOrderId = $this->input->post('workOrderId');
            $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
            $booking = $this->booking->getBookingById($workOrder['id_booking']);
            $handling_type = $this->handlingType->getHandlingTypeById($workOrder['id_handling_type']);
            $totalDuration = (strtotime($workOrder['taken_at']) + (($handling_type['duration']) * 60));
            $dateTime = strtotime(date('Y-m-d H:i:s'));

            //get job detail by work order
            $jobContainers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderId, true);
            foreach ($jobContainers as &$container) {
                $containerGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($container['id']);
                $container['goods'] = $containerGoods;
            }
            $jobGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderId, true, true);

            //get overtime status
            $overtime_status = [];
            $position = [];
            if (!empty($jobContainers)) {
                foreach($jobContainers as $container){
                    $overtime_status[] = $container['overtime_status'];
                    $position[] = $container['id_position'];
                    if (key_exists('goods', $container)) {
                        foreach ($container['goods'] as $item) {
                            $overtime_status[] = $item['overtime_status'];
                            $position[] = $item['id_position'];

                            if(!empty($workOrder['multiplier_goods']) && empty($item['total_photo'])){
                                $checkPassed = false;
                                $message = "Complete Job {$workOrder['no_work_order']} failed, please upload photos for all goods!";
                            }
                        }
                    }
                }
            }

            $checkMaxTakeJob = false;
            $goodNameOver = '';
            
            $bookingGoodsUncompleted = $this->bookingGoods->getBookingGoodsByBookingHandlingWorkOrderUncompleted($booking['id'], $booking['category']);
            // print_debug($bookingGoodsUncompleted);
            if (!empty($jobGoods)) {
                foreach ($jobGoods as $item) {
                    $overtime_status[] = $item['overtime_status'];
                    $position[] = $item['id_position'];

                    if($workOrder['handling_type'] == 'LOAD'){
                        foreach ($bookingGoodsUncompleted as $key => $bookingGoods) {
                            if($bookingGoods['id_goods'] == $item['id_goods'] 
                                && $bookingGoods['id_unit'] == $item['id_unit']
                                && $bookingGoods['no_pallet'] == $item['no_pallet']
                                && $bookingGoods['ex_no_container'] == $item['ex_no_container']){
                                    if($bookingGoods['left_quantity'] < $item['quantity']){
                                        $checkMaxTakeJob = true;
                                        $goodNameOver = $item['goods_name'];
                                        break;
                                    }
                            }
                        }
                    }
                    
                    if(!empty($workOrder['multiplier_goods']) && empty($item['total_photo'])){
                        $checkPassed = false;
                        $message = "Complete Job {$workOrder['no_work_order']} failed, please upload photos for all goods!";
                    }
                }
            }
            
            $bookingOut = $this->workOrder->getWorkOrdersByBooking($workOrder['id_booking']);
            $totalComplete=0;
            $sameBooking=[];
            foreach ($bookingOut as $key) {
                if ($key['id']!=$workOrderId && ($key['status']=='COMPLETED'||$key['status']=='TAKEN')) {
                    if ($key['handling_type']=='LOAD'||$key['handling_type']=='UNLOAD'||$key['handling_type']=='STRIPPING'||$key['handling_type']=='STUFFING') {
                        if ($key['status_validation']=='PENDING'||$key['status_validation']=='CHECKED') {
                            array_push($sameBooking,$key);
                        }
                    }
                }
            }
            foreach ($sameBooking as $key) {
                $jobGoodsSameBookings = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($key['id'], true, true);
                foreach ($jobGoodsSameBookings as $jobGoodsSameBooking) {
                    foreach ($jobGoods as $jobGood) {
                        if ($jobGood['id_goods']==$jobGoodsSameBooking['id_goods'] && $jobGood['ex_no_container']==$jobGoodsSameBooking['ex_no_container']) {
                            $totalComplete++;
                            break 3;  
                        }
                    }
                }
                
            }
            //complete job
            if ($checkMaxTakeJob) {
                $checkPassed = false;
                $message = "Complete Job {$workOrder['no_work_order']} failed, due {$goodNameOver} pick up is bigger than Booking set!";
            }else if ($totalComplete>0) {
                $checkPassed = false;
                $message = "Complete Job {$workOrder['no_work_order']} failed, please wait until the other job is approved!";
            } else if ($booking['category'] == 'OUTBOUND' && $booking['outbound_type'] == PeopleModel::OUTBOUND_CASH_AND_CARRY && !empty($booking['payout_until_date']) && $booking['payout_until_date'] < date('Y-m-d')) {
                $checkPassed = false;
                $message = "Booking customer cash & carry payout date is passed, please contact Finance to update the payment!";
            } else if ($totalDuration > $dateTime) {
                $checkPassed = false;
                $message = "Complete Job {$workOrder['no_work_order']} failed, please wait until duration time is finished!";
            } else if (empty($jobContainers) && empty($jobGoods)) {
                $checkPassed = false;
                $message = "Complete Job {$workOrder['no_work_order']} failed, please fill out the goods or container field and set overtime status!";
            } else if (!empty($position) || !empty($overtime_status)) {
                if ((in_array('0', $position) && $workOrder['handling_type'] == "STRIPPING") || (in_array('0', $position) && $workOrder['handling_type'] == "UNLOAD")) {
                    $checkPassed = false;
                    $message = "Complete Job {$workOrder['no_work_order']} failed, Please select the position type!";
                } elseif (false && in_array('0', $overtime_status)) {
                    $checkPassed = false;
                    $message = "Complete Job {$workOrder['no_work_order']} failed, Please set overtime status!";
                }
            }
        }
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $checkPassed,
            'message' => $message
        ]);
    }

    /**
     * Input handling data of work order.
     * @param $workOrderId
     * @param null $mode
     */
    public function create($workOrderId, $mode = null)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_CREATE);

        if (!$this->workOrderOvertimeCharge->isOvertimeValidationCompleted()) {
            flash('danger', 'There are work order overtimes outstanding, please validate first!', '_back', 'work-order');
        }

        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
        $isWorkOrderTaken = $workOrder['status'] == WorkOrderModel::STATUS_TAKEN;
        $isTakenByCurrentUser = $workOrder['taken_by'] == UserModel::authenticatedUserData('id');
        $isAdmin = UserModel::authenticatedUserData('username') == 'admin';

        //get data handover
        $getStatusHistory = $this->statusHistory->getBy(['id_reference' => $workOrder['id'], 'status_histories.status' => WorkOrderModel::STATUS_VALIDATION_HANDOVER_RELEASED]);
        $getStatusHistory = end($getStatusHistory);
        $handoverData = json_decode($getStatusHistory['data'], true);
        $getHandoverBy = $this->userModel->getById($handoverData['handover_user_id']);
        $isHandoverByCurrentUser = $getHandoverBy['id'] == UserModel::authenticatedUserData('id');

        if ($isWorkOrderTaken && ($isTakenByCurrentUser || $isHandoverByCurrentUser || $isAdmin)) {
            // // get job detail by safe conduct
            //   $safeConduct = $this->safeConduct->getSafeConductById($workOrder['id_safe_conduct']);
            //   $jobContainers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($safeConduct['id']);
            //   foreach ($jobContainers as &$container) {
            //       $containerGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConductContainer($container['id']);
            //       $container['goods'] = $containerGoods;
            //   }
            //   $jobGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($safeConduct['id'], true);

            //   //check if generate pallet marking
            //   $pallets = $this->pallet->getBy(['pallets.id_booking' => $workOrder['id_booking']]);
            //   foreach ($jobGoods as &$item) {
            //       foreach ($pallets as $pallet) {
            //           if ($pallet['description'] == $item['goods_name']) {
            //               $item['no_pallet'] = $pallet['no_pallet'];
            //           }
            //       }
            //   }
            
            //get job detail by work order
            $jobContainers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderId, true);
            foreach ($jobContainers as &$container) {
                $containerGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($container['id']);
                $container['goods'] = $containerGoods;
                $container['is_work_order'] = true;
            }
            $jobGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderId, true, true);
            // or Handling Containers
            if (empty($jobContainers) && empty($jobGoods)) {
                if ($workOrder['handling_type'] != 'LOAD' || $workOrder['type_booking'] == 'EXPORT') {
                    $jobContainers = $this->handlingContainer->getHandlingContainersByHandling($workOrder['id_handling']);
                    foreach ($jobContainers as &$container) {
                        $containerGoods = $this->handlingGoods->getHandlingGoodsByHandlingContainer($container['id']);
                        $container['goods'] = $containerGoods;
                        $container['is_work_order'] = false;
                    }

                    if ($workOrder['type_booking'] != 'EXPORT') {
                        $jobGoods = $this->handlingGoods->getHandlingGoodsByHandling($workOrder['id_handling'], true);

                        // special case handling ADD UNPACKAGE find assembly item data
                        // adjust missing column from handling goods.
                        if ($workOrder['handling_type'] == 'ADD UNPACKAGE') {
                            // find out source ex container
                            $exNoContainers = array_unique(array_column($jobGoods, 'ex_no_container'));
                            if (!empty($exNoContainers) && count($exNoContainers) == 1) {
                                $workOrder['source_ex_no_container'] = $exNoContainers[0];
                            }

                            // find out overtime status
                            $overtime = $this->Overtime->getDayOvertime(strtoupper(date('l')), get_active_branch_id());
                            if (!empty($overtime)) {
                                $firstOver = strtotime($overtime['first_overtime']);
                                $secondOver = strtotime($overtime['second_overtime']);
                                $overtimeGoods = (date('H', strtotime(date('Y-m-d H:i:s'))) == '00')
                                    ? strtotime(format_date(date('Y-m-d H:i:s'), '24:i:s'))
                                    : strtotime(format_date(date('Y-m-d H:i:s'), 'H:i:s'));
                                // decide overtime status by its overtime date
                                if ($overtimeGoods <= $firstOver) {
                                    $overtimeStatus = 'NORMAL';
                                } elseif ($overtimeGoods <= $secondOver) {
                                    $overtimeStatus = 'OVERTIME 1';
                                } else {
                                    $overtimeStatus = 'OVERTIME 2';
                                }
                            } else {
                                $overtimeStatus = 'NORMAL';
                            }

                            $packageItems = [];
                            foreach ($jobGoods as $jobItem) {
                                $goods = $this->goods->getById($jobItem['id_goods']);
                                $assemblyGoods = $this->assemblyGoods->getBy(['ref_assembly_goods.id_assembly' => $goods['id_assembly']]);

                                // if some of assembly items is empty, then recalculate of weight and volume
                                $weightNeedDistributedCalculation = false;
                                $volumeNeedDistributedCalculation = false;
                                foreach ($assemblyGoods as $assembly) {
                                    if (empty($assembly['unit_weight']) || !($assembly['unit_weight'] <> 0)) {
                                        $weightNeedDistributedCalculation = $workOrder['distributed_calculation'] = true;
                                    }
                                    if (empty($assembly['unit_volume']) || !($assembly['unit_volume'] <> 0)) {
                                        $volumeNeedDistributedCalculation = $workOrder['distributed_calculation'] = true;
                                    }
                                }
                                $totalAssemblyQuantity = array_sum(array_column($assemblyGoods, 'quantity_assembly'));
                                foreach ($assemblyGoods as &$assemblyItem) {
                                    if ($weightNeedDistributedCalculation) {
                                        //$distributedWeight = $goods['unit_weight'] / count($assemblyGoods);
                                        //$distributedWeight = $assemblyItem['quantity_assembly'] / $totalAssemblyQuantity * $goods['unit_weight'];
                                        $distributedWeight = $jobItem['total_weight'] / $totalAssemblyQuantity;
                                        $distributedGrossWeight = $jobItem['total_gross_weight'] / $totalAssemblyQuantity;
                                        $assemblyItem['unit_weight'] = $distributedWeight;
                                        $assemblyItem['total_weight'] = $distributedWeight * $assemblyItem['quantity'];
                                        $assemblyItem['unit_gross_weight'] = $distributedGrossWeight;
                                        $assemblyItem['total_gross_weight'] = $distributedGrossWeight * $assemblyItem['quantity'];
                                    }
                                    if ($volumeNeedDistributedCalculation) {
                                        //$distributedVolume = $goods['unit_volume'] / count($assemblyGoods);
                                        //$distributedVolume = $assemblyItem['quantity_assembly'] / $totalAssemblyQuantity * $goods['unit_volume'];
                                        $distributedVolume = $jobItem['total_volume'] / $totalAssemblyQuantity;
                                        $assemblyItem['unit_volume'] = $distributedVolume;
                                        $assemblyItem['unit_length'] = $distributedVolume;
                                        $assemblyItem['unit_width'] = 1;
                                        $assemblyItem['unit_height'] = 1;
                                        $assemblyItem['total_volume'] = $distributedVolume * $assemblyItem['quantity'];
                                    }
                                    $assemblyItem['id_handling'] = $jobItem['id_handling'];
                                    $assemblyItem['id_handling_container'] = $jobItem['id_handling_container'];
                                    $assemblyItem['id_handling_goods'] = $jobItem['id_handling_goods'];
                                    $assemblyItem['id_work_order_goods'] = $jobItem['id_work_order_goods'];
                                    $assemblyItem['id_owner'] = $jobItem['id_owner'];
                                    $assemblyItem['id_position'] = $jobItem['id_position'];
                                    $assemblyItem['whey_number'] = $jobItem['whey_number'];
                                    $assemblyItem['no_pallet'] = $jobItem['no_pallet'];
                                    $assemblyItem['ex_no_container'] = $jobItem['ex_no_container'];
                                    $assemblyItem['is_hold'] = $jobItem['is_hold'];
                                    $assemblyItem['no_delivery_order'] = $jobItem['no_delivery_order'];
                                    $assemblyItem['status'] = $jobItem['status'];
                                    $assemblyItem['status_danger'] = $jobItem['status_danger'];
                                    $assemblyItem['description'] = $jobItem['description'];
                                    $assemblyItem['position'] = $jobItem['position'];
                                    $assemblyItem['owner_name'] = $jobItem['owner_name'];
                                    $assemblyItem['id_position_blocks'] = $jobItem['id_position_blocks'];
                                    $assemblyItem['position_blocks'] = $jobItem['position_blocks'];
                                    $assemblyItem['overtime_status'] = $overtimeStatus;
                                    $assemblyItem['overtime_date'] = date('Y-m-d H:i:s');
                                    $assemblyItem['id_booking_reference'] = '';
                                }
                                $packageItems = array_merge($packageItems, $assemblyGoods);
                            }
                            // replace existing handling goods from assembly items
                            $jobGoods = $packageItems;
                        }
                    }

                }
            }
            $this->mode_tally($workOrder, $jobContainers, $jobGoods);
            return;
        } else if (!$isWorkOrderTaken) {
            flash('danger', "Job <strong>{$workOrder['no_work_order']}</strong> not yet taken or already complete");
        } else {
            flash('danger', "Job <strong>{$workOrder['no_work_order']}</strong> was taken by <strong>{$workOrder['tally_name']}</strong>, ask him/her to release it first");
        }
        redirect('tally');

    }

    /**
     * Save tally job.
     *
     * @param $workOrderId
     */
    public function save($workOrderId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_TAKE_JOB);

        $mode = $this->input->post('mode');
        $description = $this->input->post('description');
        $warehouseId = $this->input->post('warehouse');
        $containers = if_empty($this->input->post('containers'), []);
        $goods = if_empty($this->input->post('goods'), []);
        $userId = UserModel::authenticatedUserData('id');
        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
// validation before saving
        $inputIsValid = true;
        $parentGoodsId = null;
        if ($workOrder['handling_type'] == 'ADD UNPACKAGE') {
            foreach ($goods as $item) {
                if (empty($item['unit_volume']) || !($item['unit_volume'] <> 0)) {
                    //$inputIsValid = false;
                    //flash('danger', 'Unit volume cannot be empty');
                    break;
                }
            }
            if ($inputIsValid) {
                $handlingGoods = $this->handlingGoods->getHandlingGoodsByHandling($workOrder['id_handling']);
                if (!empty($handlingGoods)) {
                    $totalSourceVolume = array_sum(array_column($handlingGoods, 'unit_volume'));
                    $totalUnpackageVolume = array_sum(array_column($goods, 'unit_volume'));
                    if ($totalUnpackageVolume < ($totalSourceVolume - 1) || $totalUnpackageVolume > ($totalSourceVolume + 1)) {
                        //$inputIsValid = false;
                        //flash('danger', 'Total unpackage unit volume must >= ' . ($totalSourceVolume - 1) . ' and <=' . ($totalSourceVolume + 1));
                    }
                    $parentGoodsId = $handlingGoods[0]['id_goods'];
                }
            }
        }

        if ($inputIsValid) {
            //save data
            $this->db->trans_start();

            //get job detail by work order
            if (!empty($containers)) {
                $containerId = array_column($containers, 'workOrderContainerId');
                $containerGoods = array_column($containers, 'goods');

                $merge_workOrderGoodsId = [];
                foreach ($containerGoods as $containerGood) {
                    $merge_workOrderGoodsId = array_merge($merge_workOrderGoodsId, array_column($containerGood, 'workOrderGoodsId'));
                }
            } else {
                $this->workOrderContainer->deleteContainersByWorkOrder($workOrderId, false);
            }

            if (!empty($goods)) {
                $itemId = array_column($goods, 'workOrderGoodsId');
            } else {
                //$this->workOrderGoods->deleteGoodsByWorkOrder($workOrderId, false);
                $this->workOrderGoods->delete([
                    'id_work_order' => $workOrderId,
                    'id_work_order_container iS NULL' => null
                ], false);
            }

            $jobContainers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderId, true);
            if (!empty($jobContainers) && !empty($containerId)) {
                foreach ($jobContainers as &$jobContainer) {
                    if ((in_array($jobContainer['id'], $containerId)) == false) {
                        $this->workOrderContainer->deleteContainersById($jobContainer['id'], false);
                    }

                    $jobContainerGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($jobContainer['id']);
                    if (!empty($jobContainerGoods)) {
                        foreach ($jobContainerGoods as $itemJobContainer) {
                            if ((in_array($itemJobContainer['id'], $merge_workOrderGoodsId)) == false) {
                                $this->workOrderGoods->deleteGoodsById($itemJobContainer['id'], false);
                            }
                        }
                    }
                }
            }

            $jobGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderId, true, true);
            if (!empty($jobGoods) && !empty($itemId)) {
                foreach ($jobGoods as $itemGoods) {
                    if ((in_array($itemGoods['id'], $itemId)) == false) {
                        $this->workOrderGoods->deleteGoodsById($itemGoods['id'], false);
                    }
                }
            }

            $this->workOrder->updateWorkOrder([
                'mode' => if_empty($mode, null),
                'id_warehouse' => $warehouseId,
                'description' => $description,
                'updated_by' => $userId,
                'updated_at' => sql_date_format('now')
            ], $workOrderId);

            $noBookingIn = $workOrder['category_booking'] == 'INBOUND' ? $workOrder['no_booking'] : $workOrder['no_booking_in'];
            // $this->workOrderContainer->deleteContainersByWorkOrder($workOrderId, false);
            // $this->workOrderGoods->deleteGoodsByWorkOrder($workOrderId, false);
            foreach ($containers as $container) {
                $foundContainer = $this->container->getById($container['id_container']);
                $getWorkOrderContainerById = $this->workOrderContainer->getWorkOrderContainerById($container['workOrderContainerId']);

                if (empty($getWorkOrderContainerById) || empty($container['workOrderContainerId'])) {
                    $overtimeDate = $container['overtime_status'] != '0' ? date('Y-m-d H:i:s') : null;
                    $this->workOrderContainer->insertWorkOrderContainer([
                        'id_work_order' => $workOrder['id'],
                        'id_owner' => $workOrder['id_customer'],
                        'id_booking_reference' => if_empty($container['id_booking_reference'], if_empty($workOrder['id_booking_in'], null)),
                        'id_container' => $container['id_container'],
                        'id_position' => if_empty($container['id_position'], null),
                        'length_payload' => $container['length_payload'],
                        'width_payload' => $container['width_payload'],
                        'height_payload' => $container['height_payload'],
                        'volume_payload' => $container['volume_payload'],
                        'seal' => $container['seal'],
                        'is_empty' => $container['is_empty'],
                        'is_hold' => $container['is_hold'],
                        'status' => $container['status'],
                        'status_danger' => $container['status_danger'],
                        'overtime_status' => $container['overtime_status'],
                        'overtime_date' => $overtimeDate,
                        'quantity' => 1,
                        'created_by' => $userId
                    ]);

                    $workOrderContainerId = $this->db->insert_id();
                    $positionBlocks = if_empty(explode(',', $container['id_position_blocks']), []);

                    foreach ($positionBlocks as $blockId) {
                        $this->workOrderContainerPosition->create([
                            'id_work_order_container' => $workOrderContainerId,
                            'id_position_block' => $blockId
                        ]);
                    }

                    if (key_exists('goods', $container)) {
                        foreach ($container['goods'] as $item) {
                            $overtimeDate = $item['overtime_status'] != '0' ? date('Y-m-d H:i:s') : null;
                            if($workOrder['category_booking'] == 'INBOUND'){
                                $generatePM = $item['id_goods'].'/'.if_empty(get_if_exist($foundContainer, 'no_container', $item['ex_no_container']), '-').'/'.$noBookingIn;
                            }else{
                                $generatePM = $item['no_pallet'];
                            }
                            $this->workOrderGoods->insertWorkOrderGoods([
                                'id_work_order' => $workOrder['id'],
                                'id_work_order_container' => $workOrderContainerId,
                                'id_owner' => $workOrder['id_customer'],
                                'id_booking_reference' => if_empty($item['id_booking_reference'], if_empty($workOrder['id_booking_in'], null)),
                                'id_goods' => $item['id_goods'],
                                'id_unit' => $item['id_unit'],
                                'id_position' => if_empty($item['id_position'], null),
                                'quantity' => if_empty($item['quantity'], 0),
                                'unit_weight' => $item['unit_weight'],
                                'unit_gross_weight' => $item['unit_gross_weight'],
                                'unit_length' => $item['unit_length'],
                                'unit_width' => $item['unit_width'],
                                'unit_height' => $item['unit_height'],
                                'unit_volume' => $item['unit_volume'],
                                'no_pallet' => $generatePM,//$item['no_pallet']
                                'is_hold' => $item['is_hold'],
                                'status' => $item['status'],
                                'status_danger' => $item['status_danger'],
                                'description' => $item['description'],
                                'ex_no_container' => if_empty(get_if_exist($foundContainer, 'no_container', $item['ex_no_container']), null),
                                'overtime_status' => $item['overtime_status'],
                                'overtime_date' => $overtimeDate,
                                'created_by' => $userId
                            ]);
                            $workOrderGoodsId = $this->db->insert_id();

                            // update goods
                            $this->goods->update([
                                'unit_weight' => $item['unit_weight'],
                                'unit_gross_weight' => $item['unit_gross_weight'],
                                'unit_length' => $item['unit_length'],
                                'unit_width' => $item['unit_width'],
                                'unit_height' => $item['unit_height'],
                                'unit_volume' => $item['unit_volume'],
                            ], $item['id_goods']);

                            $positionBlocks = if_empty(explode(',', $item['id_position_blocks']), []);
                            foreach ($positionBlocks as $blockId) {
                                $this->workOrderGoodsPosition->create([
                                    'id_work_order_goods' => $workOrderGoodsId,
                                    'id_position_block' => $blockId
                                ]);
                            }

                            // add goods photo
                            $tempPhotos = explode(',', get_if_exist($item, 'temp_photos', ''));
                            $tempPhotoDescriptions = explode('||', get_if_exist($item, 'temp_photo_descriptions', ''));
                            foreach ($tempPhotos as $index => $file) {
                                if (!empty($file)) {
                                    $sourceFile = 'temp/' . $file;
                                    $destFile = 'work-order-goods/' . format_date('now', 'Y/m/') . $file;
                                    if ($this->uploader->setDriver('s3')->move($sourceFile, $destFile)) {
                                        $this->workOrderGoodsPhoto->create([
                                            'id_work_order_goods' => $workOrderGoodsId,
                                            'src' => $destFile,
                                            'url' => $this->uploader->setDriver('s3')->getUrl($destFile),
                                            'description' => get_if_exist($tempPhotoDescriptions, $index, null)
                                        ]);
                                    }

                                    $tempText = $getWorkOrderGoodsById['no_work_order'] . "\nUPLOAD AT " . date('d M Y H:i:s') . "\nUPLOADED BY " . strtoupper(UserModel::authenticatedUserData('username')) . "\n" . $getWorkOrderGoodsById['goods_name'];
                                    $this->watermark($destFile, $file, $tempText);
                                }
                            }
                        }
                    }

                } else {
                    if ($container['overtime_status'] != '0') {
                        $overtimeDate = $container['overtime_date'] != '' ? $container['overtime_date'] : date('Y-m-d H:i:s');
                    } else {
                        $overtimeDate = null;
                    }

                    $this->workOrderContainer->updateWorkOrderContainer([
                        'id_owner' => $workOrder['id_customer'],
                        'id_booking_reference' => if_empty($container['id_booking_reference'], if_empty($workOrder['id_booking_in'], null)),
                        'id_container' => $container['id_container'],
                        'id_position' => if_empty($container['id_position'], null),
                        'length_payload' => $container['length_payload'],
                        'width_payload' => $container['width_payload'],
                        'height_payload' => $container['height_payload'],
                        'volume_payload' => $container['volume_payload'],
                        'seal' => $container['seal'],
                        'is_empty' => $container['is_empty'],
                        'is_hold' => $container['is_hold'],
                        'status' => $container['status'],
                        'status_danger' => $container['status_danger'],
                        'overtime_status' => $container['overtime_status'],
                        'overtime_date' => $overtimeDate,
                        'quantity' => 1,
                        'created_by' => $userId
                    ], $container['workOrderContainerId']);

                    $this->workOrderContainerPosition->deleteWorkOrderContainerPositionByworkOrdercontainerId($container['workOrderContainerId']);
                    $positionBlocks = if_empty(explode(',', $container['id_position_blocks']), []);
                    foreach ($positionBlocks as $blockId) {
                        $this->workOrderContainerPosition->create([
                            'id_work_order_container' => $container['workOrderContainerId'],
                            'id_position_block' => $blockId
                        ], $container['workOrderContainerId']);
                    }

                    if (key_exists('goods', $container)) {
                        foreach ($container['goods'] as $item) {
                            $getWorkOrderGoodsById = $this->workOrderGoods->getWorkOrderGoodsById($item['workOrderGoodsId']);
                            if (empty($getWorkOrderGoodsById) || empty($item['workOrderGoodsId'])) {
                                $overtimeDate = $item['overtime_status'] != '0' ? date('Y-m-d H:i:s') : null;
                                if($workOrder['category_booking'] == 'INBOUND'){
                                    $generatePM = $item['id_goods'].'/'.if_empty(get_if_exist($foundContainer, 'no_container', $item['ex_no_container']), '-').'/'.$noBookingIn;
                                }else{
                                    $generatePM = $item['no_pallet'];
                                }
                                $this->workOrderGoods->insertWorkOrderGoods([
                                    'id_work_order' => $workOrder['id'],
                                    'id_work_order_container' => $container['workOrderContainerId'],
                                    'id_booking_reference' => if_empty($item['id_booking_reference'], if_empty($workOrder['id_booking_in'], null)),
                                    'id_owner' => $workOrder['id_customer'],
                                    'id_goods' => $item['id_goods'],
                                    'id_unit' => $item['id_unit'],
                                    'id_position' => if_empty($item['id_position'], null),
                                    'quantity' => if_empty($item['quantity'], 0),
                                    'unit_weight' => $item['unit_weight'],
                                    'unit_gross_weight' => $item['unit_gross_weight'],
                                    'unit_length' => $item['unit_length'],
                                    'unit_width' => $item['unit_width'],
                                    'unit_height' => $item['unit_height'],
                                    'unit_volume' => $item['unit_volume'],
                                    'no_pallet' => $generatePM,// $item['no_pallet']
                                    'is_hold' => $item['is_hold'],
                                    'status' => $item['status'],
                                    'status_danger' => $item['status_danger'],
                                    'description' => $item['description'],
                                    'ex_no_container' => if_empty(get_if_exist($foundContainer, 'no_container', $item['ex_no_container']), null),
                                    'overtime_status' => $item['overtime_status'],
                                    'overtime_date' => $overtimeDate,
                                    'created_by' => $userId
                                ]);

                                $workOrderGoodsId = $this->db->insert_id();

                                // update goods
                                $this->goods->update([
                                    'unit_weight' => $item['unit_weight'],
                                    'unit_gross_weight' => $item['unit_gross_weight'],
                                    'unit_length' => $item['unit_length'],
                                    'unit_width' => $item['unit_width'],
                                    'unit_height' => $item['unit_height'],
                                    'unit_volume' => $item['unit_volume'],
                                ], $item['id_goods']);

                                $positionBlocks = if_empty(explode(',', $item['id_position_blocks']), []);
                                foreach ($positionBlocks as $blockId) {
                                    $this->workOrderGoodsPosition->create([
                                        'id_work_order_goods' => $workOrderGoodsId,
                                        'id_position_block' => $blockId
                                    ]);
                                }

                            } else {

                                if ($item['overtime_status'] != '0') {
                                    $overtimeDate = $item['overtime_date'] != '' ? $item['overtime_date'] : date('Y-m-d H:i:s');
                                } else {
                                    $overtimeDate = null;
                                }

                                if($workOrder['category_booking'] == 'INBOUND'){
                                    $generatePM = $item['id_goods'].'/'.if_empty(get_if_exist($foundContainer, 'no_container', $item['ex_no_container']), '-').'/'.$noBookingIn;
                                }else{
                                    $generatePM = $item['no_pallet'];
                                }
                                $this->workOrderGoods->updateWorkOrderGoods([
                                    'id_booking_reference' => if_empty($item['id_booking_reference'], if_empty($workOrder['id_booking_in'], null)),
                                    'id_goods' => $item['id_goods'],
                                    'id_unit' => $item['id_unit'],
                                    'id_position' => if_empty($item['id_position'], null),
                                    'quantity' => $item['quantity'],
                                    'unit_weight' => $item['unit_weight'],
                                    'unit_gross_weight' => $item['unit_gross_weight'],
                                    'unit_length' => $item['unit_length'],
                                    'unit_width' => $item['unit_width'],
                                    'unit_height' => $item['unit_height'],
                                    'unit_volume' => $item['unit_volume'],
                                    'no_pallet' => $generatePM,//$item['no_pallet']
                                    'is_hold' => $item['is_hold'],
                                    'status' => $item['status'],
                                    'status_danger' => $item['status_danger'],
                                    'description' => $item['description'],
                                    'overtime_status' => $item['overtime_status'],
                                    'overtime_date' => $overtimeDate,
                                    'ex_no_container' => if_empty(get_if_exist($foundContainer, 'no_container', $item['ex_no_container']), null),
                                    'created_by' => $userId
                                ], $item['workOrderGoodsId']);

                                $workOrderGoodsId = $item['workOrderGoodsId'];

                                // update goods
                                $this->goods->update([
                                    'unit_weight' => $item['unit_weight'],
                                    'unit_gross_weight' => $item['unit_gross_weight'],
                                    'unit_length' => $item['unit_length'],
                                    'unit_width' => $item['unit_width'],
                                    'unit_height' => $item['unit_height'],
                                    'unit_volume' => $item['unit_volume'],
                                ], $item['id_goods']);

                                $this->workOrderGoodsPosition->deleteWorkOrderGoodsPositionByworkOrdergoodsId($item['workOrderGoodsId']);
                                $positionBlocks = if_empty(explode(',', $item['id_position_blocks']), []);
                                foreach ($positionBlocks as $blockId) {
                                    $this->workOrderGoodsPosition->create([
                                        'id_work_order_goods' => $item['workOrderGoodsId'],
                                        'id_position_block' => $blockId
                                    ]);
                                }
                            }

                            // add goods photo
                            $tempPhotos = explode(',', get_if_exist($item, 'temp_photos', ''));
                            $tempPhotoDescriptions = explode('||', get_if_exist($item, 'temp_photo_descriptions', ''));
                            foreach ($tempPhotos as $index => $file) {
                                if (!empty($file)) {
                                    $sourceFile = 'temp/' . $file;
                                    $destFile = 'work-order-goods/' . format_date('now', 'Y/m/') . $file;
                                    if ($this->uploader->setDriver('s3')->move($sourceFile, $destFile)) {
                                        $this->workOrderGoodsPhoto->create([
                                            'id_work_order_goods' => $workOrderGoodsId,
                                            'src' => $destFile,
                                            'url' => $this->uploader->setDriver('s3')->getUrl($destFile),
                                            'description' => get_if_exist($tempPhotoDescriptions, $index, null)
                                        ]);
                                        $tempText = $getWorkOrderGoodsById['no_work_order'] . "\nUPLOAD AT " . date('d M Y H:i:s') . "\nUPLOADED BY " . strtoupper(UserModel::authenticatedUserData('username')) . "\n" . $getWorkOrderGoodsById['goods_name'];
                                        $this->watermark($destFile, $file, $tempText);
                                    }
                                }
                            }
                        }
                    }
                }
            }

            foreach ($goods as $item) {
                $getWorkOrderGoodsById = $this->workOrderGoods->getWorkOrderGoodsById($item['workOrderGoodsId']);
                if ($workOrder['category_booking'] == 'INBOUND') {
                    $generatePM = $item['id_goods'] . '/' . if_empty($item['ex_no_container'], '-') . '/' . $noBookingIn;
                } else {
                    $generatePM = $item['no_pallet'];
                }
                if (empty($getWorkOrderGoodsById) || empty($item['workOrderGoodsId'])) {
                    $overtimeDate = $item['overtime_status'] != '0' ? date('Y-m-d H:i:s') : null;
                    $this->workOrderGoods->insertWorkOrderGoods([
                        'id_work_order' => $workOrder['id'],
                        'id_owner' => $workOrder['id_customer'],
                        'id_booking_reference' => if_empty($item['id_booking_reference'], if_empty($workOrder['id_booking_in'], null)),
                        'id_goods' => $item['id_goods'],
                        'id_unit' => $item['id_unit'],
                        'id_position' => if_empty($item['id_position'], null),
                        'quantity' => $item['quantity'],
                        'unit_weight' => $item['unit_weight'],
                        'unit_gross_weight' => $item['unit_gross_weight'],
                        'unit_length' => $item['unit_length'],
                        'unit_width' => $item['unit_width'],
                        'unit_height' => $item['unit_height'],
                        'unit_volume' => $item['unit_volume'],
                        'no_pallet' => if_empty($generatePM, $item['no_pallet']),
                        'is_hold' => $item['is_hold'],
                        'status' => $item['status'],
                        'status_danger' => $item['status_danger'],
                        'description' => $item['description'],
                        'ex_no_container' => if_empty($item['ex_no_container'], null),
                        'overtime_status' => $item['overtime_status'],
                        'overtime_date' => $overtimeDate,
                        'created_by' => $userId
                    ]);
                    $workOrderGoodsId = $this->db->insert_id();
                } else {
                    if ($item['overtime_status'] != '0') {
                        $overtimeDate = $item['overtime_date'] != '' ? $item['overtime_date'] : date('Y-m-d H:i:s');
                    } else {
                        $overtimeDate = null;
                    }
                    $this->workOrderGoods->updateWorkOrderGoods([
                        'id_booking_reference' => if_empty($item['id_booking_reference'], if_empty($workOrder['id_booking_in'], null)),
                        'id_goods' => $item['id_goods'],
                        'id_unit' => $item['id_unit'],
                        'id_position' => $item['id_position'],
                        'quantity' => $item['quantity'],
                        'unit_weight' => $item['unit_weight'],
                        'unit_gross_weight' => $item['unit_gross_weight'],
                        'unit_length' => $item['unit_length'],
                        'unit_width' => $item['unit_width'],
                        'unit_height' => $item['unit_height'],
                        'unit_volume' => $item['unit_volume'],
                        'no_pallet' => if_empty($generatePM, $item['no_pallet']),
                        'is_hold' => $item['is_hold'],
                        'status' => $item['status'],
                        'status_danger' => $item['status_danger'],
                        'description' => $item['description'],
                        'ex_no_container' => if_empty($item['ex_no_container'], null),
                        'overtime_status' => $item['overtime_status'],
                        'overtime_date' => $overtimeDate,
                        'created_by' => $userId
                    ], $item['workOrderGoodsId']);
                    $workOrderGoodsId = $item['workOrderGoodsId'];
                    $this->workOrderGoodsPosition->deleteWorkOrderGoodsPositionByworkOrdergoodsId($item['workOrderGoodsId']);
                }

                // update goods
                $goodsData = [
                    'unit_weight' => $item['unit_weight'],
                    'unit_gross_weight' => $item['unit_gross_weight'],
                    'unit_length' => $item['unit_length'],
                    'unit_width' => $item['unit_width'],
                    'unit_height' => $item['unit_height'],
                    'unit_volume' => $item['unit_volume'],
                ];
                if ($workOrder['handling_type'] == 'ADD UNPACKAGE') {
                    $goodsData['id_goods_parent'] = $parentGoodsId;
                }
                $this->goods->update($goodsData, $item['id_goods']);

                // update goods position detail
                $this->workOrderGoodsPosition->deleteWorkOrderGoodsPositionByworkOrdergoodsId($item['workOrderGoodsId']);
                $positionBlocks = if_empty(explode(',', $item['id_position_blocks']), []);
                foreach ($positionBlocks as $blockId) {
                    $this->workOrderGoodsPosition->create([
                        'id_work_order_goods' => $workOrderGoodsId,
                        'id_position_block' => $blockId
                    ]);
                }

                // add goods photo
                $tempPhotos = explode(',', get_if_exist($item, 'temp_photos', ''));
                $tempPhotoDescriptions = explode('||', get_if_exist($item, 'temp_photo_descriptions', ''));
                foreach ($tempPhotos as $index => $file) {
                    if (!empty($file)) {
                        $sourceFile = 'temp/' . $file;
                        $destFile = 'work-order-goods/' . format_date('now', 'Y/m/') . $file;
                        if ($this->uploader->setDriver('s3')->move($sourceFile, $destFile)) {
                            $this->workOrderGoodsPhoto->create([
                                'id_work_order_goods' => $workOrderGoodsId,
                                'src' => $destFile,
                                'url' => $this->uploader->setDriver('s3')->getUrl($destFile),
                                'description' => get_if_exist($tempPhotoDescriptions, $index, null)
                            ]);
                        }

                        $tempText = $getWorkOrderGoodsById['no_work_order'] . "\nUPLOAD AT " . date('d M Y H:i:s') . "\nUPLOADED BY " . strtoupper(UserModel::authenticatedUserData('username')) . "\n" . $getWorkOrderGoodsById['goods_name'];
                        $this->watermark($destFile, $file, $tempText);
                    }
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Job {$workOrder['no_work_order']} successfully checked", 'tally');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->create($workOrderId, 'NEW');
    }

    /**
     * Show edit job in general mode.
     *
     * @param $workOrderId
     */
    public function edit($workOrderId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_EDIT);

        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);

        $discrepancy = null;
        if (get_url_param('discrepancy', false)) {
            $this->load->model('DiscrepancyHandoverModel', 'discrepancyHandover');
            $discrepancy = $this->discrepancyHandover->getBy([
                'discrepancy_handovers.id_booking' => $workOrder['id_booking'],
                'discrepancy_handovers.status!=' => DiscrepancyHandoverModel::STATUS_CANCELED,
            ], true);

            if (empty($discrepancy)) {
                flash('danger', 'Related booking of this job discrepancy is not occurred, use standard edit instead', '_back', 'tally');
            }
        }

        $today = strtoupper(date('l'));
        $branchId = get_active_branch('id');
        $Overtime = $this->Overtime->getDayOvertime($today, $branchId);

        if (!AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATED_EDIT)) {
            $diffTotal = date_diff(date_create(date('Y-m-d H:i:s')), date_create($workOrder['completed_at']))->format('%R%a');
            if ($diffTotal < -1) {
                flash('danger', 'Edit is not allowed after day of tomorrow', 'work-order');
            }
        }

        $containers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderId, true);
        foreach ($containers as &$container) {
            $containerGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($container['id']);
            $container['goods'] = $containerGoods;

            $containerContainers = $this->workOrderContainer->getWorkOrderContainersByWorkOrderContainer($container['id']);
            $container['containers'] = $containerContainers;
        }

        $goods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderId, true, true);
        foreach ($goods as &$item) {
            $goodsItem = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderGoods($item['id']);
            $item['goods'] = $goodsItem;
        }
        $warehouses = $this->warehouse->getBy(['id_branch' => $workOrder['id_branch']]);

        $units = $this->unit->getAll();

        $vehicle=[];
        $tep_data =[];
        $outstandingChassis =[];
        if(!empty($workOrder['id_vehicle'])){
            $vehicle=$this->vehicle->getAll();
            $workOrder['transporter_category']='INTERNAL';
        }else{
            $id_customer = $workOrder['id_customer'];
            $category = $workOrder['category_booking'];
            $workOrder['transporter_category'] = 'EXTERNAL';

            if($category == "INBOUND"){
                /*$condition1 = [];
                $condition1['ref_people.id'] = $id_customer;
                $condition1['(transporter_entry_permits.tep_category = "INBOUND" OR transporter_entry_permits.tep_category IS NULL)'] = NULL;
                $get_tep1 = $this->transporterEntryPermit->getBy($condition1); // get tep inbound for old data and just only one booking.

                $condition2 = [];
                $condition2['people_tep.id'] = $id_customer;
                $condition2['transporter_entry_permits.tep_category'] = "INBOUND";
                $get_tep2 = $this->transporterEntryPermit->getBy($condition2); // get tep inbound for new data and multiple booking.

                $condition3 = [];
                $condition3['transporter_entry_permit_customers.id_customer'] = $id_customer;
                $condition3['transporter_entry_permits.tep_category'] = "EMPTY CONTAINER";
                $get_tep3 = $this->transporterEntryPermit->getBy($condition3); // get tep empty container

                $tep_in_merge = array_merge($get_tep1, $get_tep2, $get_tep3);
                $tep_unique = array_unique(array_column($tep_in_merge, 'tep_code'));
                $tep_data =  array_intersect_key($tep_in_merge, $tep_unique);*/

                // in case this approach is not working, then revert the commented method above
                // simple get ALL TEP that:
                // - category inbound or empty container depends on handling type (striping, moving, etc shouldn't have tep)
                // - should checked in first (whatever "check out" value is)
                // - referenced by BOOKING ...
                // - maximum created in passed 30 days
                // - except current selected tep in job
                //   (edit in long way back months, years, the selected tep should always included)
                if ($workOrder['handling_type'] == 'UNLOAD') {
                    $tep_data = $this->transporterEntryPermit->getOutstandingTep([
                        'category' => 'INBOUND',
                        'should_checked_in' => true,
                        'id_booking' => $workOrder['id_booking'],
                        'active_days' => 30,
                        'id_except_tep' => $workOrder['id_transporter_entry_permit'],
                    ]);
                } else if ($workOrder['handling_type'] == 'EMPTY CONTAINER') {
                    $tep_data = $this->transporterEntryPermit->getOutstandingTep([
                        'category' => 'EMPTY CONTAINER',
                        'should_checked_in' => true,
                        'id_customer' => $id_customer,
                        'active_days' => 30,
                        'id_except_tep' => $workOrder['id_transporter_entry_permit'],
                    ]);
                }
            }else{
                /*$condition1 = [];
                $condition1['transporter_entry_permits.id_customer'] = $id_customer;
                $condition1['transporter_entry_permits.tep_category'] = "OUTBOUND";
                $condition1['transporter_entry_permits.id_customer IS NOT NULL'] = NULL;
                $get_tep1 = $this->transporterEntryPermit->getBy($condition1); // get tep outbound for old data and just only one customer.

                $condition2 = [];
                $condition2['transporter_entry_permit_customers.id_customer'] = $id_customer;
                $condition2['transporter_entry_permits.tep_category'] = "OUTBOUND";
                $get_tep2 = $this->transporterEntryPermit->getBy($condition2); // get tep outbound for new data and multiple customer.

                $tep_out_merge = array_merge($get_tep2);
                $tep_unique = array_unique(array_column($tep_out_merge, 'tep_code'));
                $tep_data =  array_intersect_key($tep_out_merge, $tep_unique);*/

                // in case this approach not working, then revert the commented method above
                // simple get ALL TEP that:
                // - category outbound
                // - should checked in first (whatever "check out" value is)
                // - owned by CUSTOMER ...
                // - maximum created in passed 30 days
                // - except current selected tep in job
                //   (edit in long way back months, years, the selected tep should always included)
                $tep_data = $this->transporterEntryPermit->getOutstandingTep([
                    'category' => 'OUTBOUND',
                    'should_checked_in' => true,
                    'id_customer' => $id_customer,
                    'active_days' => 30,
                    'id_except_tep' => $workOrder['id_transporter_entry_permit'],
                ]);

                $outstandingChassis = $this->transporterEntryPermitChassis->getOutstandingChassis([
                    'should_checked_in' => true,
                    'outstanding_checked_out' => true,
                    'id_except_chassis' => $workOrder['id_tep_chassis'],
                ]);
            }
            
        }
        
        $components = $this->component->getByHandlingType($workOrder['id_handling_type'], $workOrder['id_handling']);
        $activity_types= [];
        $resources_types= [];
        $component_labour_id= 0;
        $component_forklift_id= 0;
        $data_forklift = [];
        foreach ($components as $component) {
            if($component['component_category']=='VALUE ADDITIONAL SERVICES'){
                $activity_types[] = $component;
            }
            if($component['component_category']=='RESOURCES' || $component['handling_component']=='Forklift'){
                $resources_types[] = $component;
            }
            
        }
        $activity_type_jobs = $this->workOrderComponent->getBy([
            'ref_components.component_category'=>'VALUE ADDITIONAL SERVICES',
            'work_order_components.id_work_order'=> $workOrderId,
            ]);
        
        $resources_type_jobs = $this->workOrderComponent->getBy([
            'ref_components.component_category'=>'RESOURCES',
            'work_order_components.id_work_order'=> $workOrderId,
            ]);
        $resources_type_jobs_forklift = $this->workOrderComponent->getBy([
            'ref_components.handling_component'=>'Forklift',
            'work_order_components.id_work_order'=> $workOrderId,
            ]);
        $resources_type_jobs  = array_merge($resources_type_jobs,$resources_type_jobs_forklift);
        foreach ($resources_type_jobs as $resources_type_job){
            if($resources_type_job['handling_component']=='Labours'){
                $component_labour_id= $resources_type_job['id'];
            }
            if($resources_type_job['handling_component']=='Forklift'){
                $component_forklift_id= $resources_type_job['id'];
                $data_forklift = $resources_type_job;
            }
        }
        if (empty($data_forklift)||empty($data_forklift['is_owned'])) {
            $data = $this->workOrderComponent->getBy([
                'id_work_order' => $workOrderId,
                'is_owned is not null' => null,
            ],true);
            $data_forklift['operator_name'] = $data['operator_name'];
            $data_forklift['is_owned'] = $data['is_owned'];
            $data_forklift['capacity'] = $data['capacity'];
            if(empty($data)){
                $data_forklift['operator_name']='';
                $data_forklift['is_owned']='';
                $data_forklift['capacity']='';
            }
        }
        $heavy_equipment = [];
        $forklift_job_ids = [];
        $forklift_job_ids_ex = [];
        $forklifts ='';
        $forklifts_external ='';
        $forklift_job = $this->workOrderComponentHeavyEquipments->getBy(['id_work_order_component'=>$component_forklift_id]);
        if (!empty($forklift_job)) {
            $heavy_equipment[] = 'INTERNAL';
            $forklifts = $this->heavyEquipment->getAll();
            foreach($forklift_job as $forklift_job){
                $forklift_job_ids[$forklift_job['id_heavy_equipment']] =  $forklift_job;
            }
        }
        $forklift_job = $this->workOrderComponentHeeps->getBy(['id_work_order_component'=>$component_forklift_id]);
        if (!empty($forklift_job)) {
            $heavy_equipment[] = 'EXTERNAL';
            $forklifts_external = $this->heavyEquipmentEntryPermit->getAll();
            foreach($forklift_job as $forklift_job){
                $forklift_job_ids_ex[$forklift_job['id_heep']] =  $forklift_job;
            }
        }
        
        //labours
        $branchIdVms= get_active_branch('id_branch_vms');
        $labours = $this->guest->getLabours($branchIdVms,true);
        $labour_job = $this->workOrderComponentLabours->getBy(['id_work_order_component'=>$component_labour_id]);
        $labour_job_ids = [];
        foreach($labour_job as $labour_job){
            $labour_job_ids[$labour_job['id_labour']] =  $labour_job;
        }
        $temp_activity_type_jobs = [];
        foreach($activity_type_jobs as $activity_type_job){
            $temp_activity_type_jobs[$activity_type_job['id_component']] =  $activity_type_job;
        }
        $activity_type_jobs = $temp_activity_type_jobs;

        $hasValidatedEditPermission = AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATED_EDIT);
        if ($hasValidatedEditPermission) {
            $workOrder['status_unlock_handheld'] = WorkOrderUnlockHandheldModel::STATUS_UNLOCKED;
        } else {
            $workOrderUnlockHandheld = $this->workOrderUnlockHandheld->getBy([
                'id_work_order' => $workOrder['id']
            ], true);
            $workOrder['status_unlock_handheld'] = if_empty($workOrderUnlockHandheld['status'], WorkOrderUnlockHandheldModel::STATUS_LOCKED);
        }

        $component_data = [
            'activity_types' => $activity_types,
            'resources_types' => $resources_types,
            'activity_type_jobs' => $activity_type_jobs,
            'resources_type_jobs' => $resources_type_jobs,
            'labours' => $labours,
            'forklifts' => $forklifts,
            'forklifts_external' => $forklifts_external,
            'labour_job_ids' => $labour_job_ids,
            'forklift_job_ids' => $forklift_job_ids,
            'forklift_job_ids_ex' => $forklift_job_ids_ex,
            'heavy_equipment' => $heavy_equipment, // internal atau external
            'data_forklift' => $data_forklift,
        ];
        // print_debug(empty($containers));

        $this->render('tally/edit', compact('workOrder', 'containers', 'goods', 'warehouses', 'units','Overtime', 'vehicle', 'tep_data', 'component_data', 'outstandingChassis', 'discrepancy'));
    }

    /**
     * Update tally job.
     *
     * @param $workOrderId
     */
    public function update($workOrderId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_EDIT);

        $discrepancyEdit = $this->input->post('discrepancy_edit');
        $warehouseId = $this->input->post('warehouse');
        $description = $this->input->post('description');
        $gateInDate = $this->input->post('gate_in_date');
        $gateOutDate = $this->input->post('gate_out_date');
        $takenAt = $this->input->post('taken_at');
        // $completedAt = $this->input->post('completed_at');
        $containers = $this->input->post('containers');
        $goods = $this->input->post('goods');
        $completedAt = [];
        $userId = UserModel::authenticatedUserData('id');
        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
        $branchId = get_active_branch('id');
        $transporterId = $this->input->post('transporter');
        $transporterCategory = $this->input->post('transporter_category');
        $chassisId = $this->input->post('chassis');
        //vas and resource
        $activity_type = $this->input->post('activity_type');
        $description_vas = $this->input->post('description_vas');
        $resources = $this->input->post('resources');
        $heavy_equipment = $this->input->post('heavy_equipment');
        $forklift = $this->input->post('forklift');
        $forklift_external = $this->input->post('forklift_external');
        $operator_name = $this->input->post('operator_name');
        $is_owned = $this->input->post('is_owned');
        $capacity = $this->input->post('capacity');
        $labours = $this->input->post('labours');
        $space = $this->input->post('space');
        $pallet = $this->input->post('pallet');
        if (is_null($pallet)) {
            $pallet = 0;
        }

        $peopleSpace = $this->people->getById($workOrder['id_customer']);
        $peopleSpace = $peopleSpace['contract'];
        $branchPallet = get_active_branch('stock_pallet');
        if(!empty($heavy_equipment)) {
            if (count($heavy_equipment)==2) {
                $is_owned = 'BOTH';
            }elseif (in_array("INTERNAL",$heavy_equipment)) {
                $is_owned = 'OWNED';
            }elseif (in_array("EXTERNAL",$heavy_equipment)) {
                $is_owned = 'LEASED';
            }
        }
        //cek space
        $usedSpace = $this->workOrder->getUsedSpace($workOrder['id_customer']);
        $usedSpace = $usedSpace['total_space'];
        if($workOrder['category_booking'] == "INBOUND" && !empty($space)){
            $space = $space*-1;
        }
        $sisaSpace = $peopleSpace + ($usedSpace+$space) + abs($workOrder['space']);
        // if($sisaSpace<0){
        //     flash('danger', "Edit Job {$workOrder['no_work_order']} failed, because no more space");
        //     redirect('tally/edit/'.$workOrderId);
        // }
        //cek pallet
        $usedPallet = $this->workOrder->getUsedPallet();
        $usedPallet = $usedPallet['total_pallet'];
        $tempPallet = $pallet;
        if($workOrder['category_booking'] == "INBOUND" && !empty($pallet)){
            $pallet = $pallet*-1;
        }
        $sisaPallet = $branchPallet + ($usedPallet+$pallet) + abs($workOrder['stock_pallet']);
        if($sisaPallet<0){
            flash('danger', "Edit Job {$workOrder['no_work_order']} failed, because no more stock pallet");
            redirect('tally/edit/'.$workOrderId);
        }
        $this->workOrderComponent->deleteByWorkOrderId($workOrderId);
        $components = [];
        $componentResources = $this->component->getBy(['id'=>$resources]);
        $countKalmar = [];
        $countForklift = [];
        //menghitung jumlah forklift dan kalmar
        foreach($forklift AS $id){
            $heavy = $this->heavyEquipment->getById($id);
            if($heavy['type']=='FORKLIFT'){
                $countForklift[] = $heavy;
            }
            if($heavy['type']=='REACH STACKER'){
                $countKalmar[] = $heavy;
            }
        }
        foreach ($componentResources as $resources) {
            if($resources['handling_component']=='Forklift'){
                $this->workOrderComponent->createWorkOrderComponent([
                    "id_work_order" => $workOrderId,
                    "id_component" => $resources['id'],//id forklift
                    "quantity" => (count($countForklift)),
                    "operator_name" => count($countForklift)!=0 ? $operator_name : (count($countKalmar)==0 ? $operator_name : NULL),
                    "is_owned" => count($countForklift)!=0 ? $is_owned : (count($countKalmar)==0 ? $is_owned : NULL),
                    "capacity" => count($countForklift)!=0 ? $capacity : (count($countKalmar)==0 ? $capacity : NULL),
                    "description" => NULL,
                    'status' => 'APPROVED',
                ]);
                $id_work_order_component = $this->db->insert_id(); 
                if (in_array("INTERNAL",$heavy_equipment)) {
                    foreach ($forklift as $forklift) {
                        $this->workOrderComponentHeavyEquipments->create([
                            'id_work_order_component' => $id_work_order_component,
                            'id_heavy_equipment' => $forklift,
                        ]);
                    }
                }
                if(in_array("EXTERNAL",$heavy_equipment)){
                    foreach ($forklift_external as $forklift_external) {
                        $this->workOrderComponentHeeps->create([
                            'id_work_order_component' => $id_work_order_component,
                            'id_heep' => $forklift_external,
                        ]);
                    }
                }
                if(!empty($countKalmar)){
                    $this->workOrderComponent->createWorkOrderComponent([
                        "id_work_order" => $workOrderId,
                        "id_component" => 2,//id kalmar
                        "quantity" => (count($countKalmar)),
                        "operator_name" => count($countKalmar)!=0 ? $operator_name : NULL,
                        "is_owned" => count($countKalmar)!=0 ? $is_owned : NULL,
                        "capacity" => count($countKalmar)!=0 ? $capacity : NULL,
                        "description" => NULL,
                        'status' => 'APPROVED',
                    ]);
                }
            }
            if($resources['handling_component']=='Labours'){
                $this->workOrderComponent->createWorkOrderComponent([
                    "id_work_order" => $workOrderId,
                    "id_component" => $resources['id'],
                    "quantity" => count($labours),
                    "operator_name" => NULL,
                    "is_owned" => NULL,
                    "capacity" => NULL,
                    "description" => NULL,
                    'status' => 'APPROVED',
                ]);
                $id_work_order_component = $this->db->insert_id(); 
                foreach ($labours as $labour) {
                    $this->workOrderComponentLabours->create([
                        'id_work_order_component' => $id_work_order_component,
                        'id_labour' => $labour,
                    ]);
                }
            }
            if($resources['handling_component']=='Pallet'){
                array_push($components,[
                    "id_work_order" => $workOrderId,
                    "id_component" => $resources['id'],
                    "quantity" => is_null($tempPallet)?0:$tempPallet,
                    "operator_name" => NULL,
                    "is_owned" => NULL,
                    "capacity" => NULL,
                    "description" => NULL,
                    'status' => 'APPROVED',
                ]);
            }
        }
        if(!empty($activity_type)) {
            foreach ($activity_type as $value) {
                if ($value==13) {//if others
                    array_push($components,[
                        "id_work_order" => $workOrderId,
                        "id_component" => $value,//vas component
                        "quantity" => 1,
                        "operator_name" => NULL,
                        "is_owned" => NULL,
                        "capacity" => NULL,
                        "description" => $description_vas,
                        'status' => 'APPROVED',
                    ]);
                }else{
                    array_push($components,[
                        "id_work_order" => $workOrderId,
                        "id_component" => $value,//vas component
                        "quantity" => 1,
                        "operator_name" => NULL,
                        "is_owned" => NULL,
                        "capacity" => NULL,
                        "description" => NULL,
                        'status' => 'APPROVED',
                    ]);
                }
            }
        }
        // print_debug($components);
        if (!empty($activity_type) || !empty($components)) {
            $this->workOrderComponent->createWorkOrderComponent($components);
        }

        $this->db->trans_start();
        //tunjangan tally and supervisor
        $dashboard_status = get_active_branch('dashboard_status');
        if(UserModel::authenticatedUserData('id')!=1 && !$discrepancyEdit){//admin tidak usah
            if ($workOrder['status']=="COMPLETED" && $dashboard_status==1) {
                $dateNow = date('Y-m-d');
                $data = json_encode($workOrder);
                if ($workOrder['status_validation']=="PENDING"||$workOrder['status_validation']=="CHECKED") {
                    //tally take
                    $id_employee = $this->employee->getBy(
                        [
                            'ref_employees.id_user'=>  $workOrder['taken_by']
                        ]
                    ,true);
                    $id_employee = $id_employee['id'];
                    if ($dateNow==date('Y-m-d',strtotime($workOrder['completed_at']))) {
                        $date = $dateNow;
                        $dateDif = null;
                    } else {
                        $date = $dateNow;
                        $dateDif = date('Y-m-d',strtotime($workOrder['completed_at']));
                    }
                    if($workOrder['id_branch']!=2 || $workOrder['id_customer']!=9151){
                        $this->allowance->create([
                            [
                                'id_employee' => $id_employee,
                                'id_component' => 1,
                                'date' => $date,
                                'different_date' => $dateDif,
                                'data' => $data,
                                'point' => 0,
                                'description' => 'edited when status completed take',
                            ],
                            [
                                'id_employee' => $id_employee,
                                'id_component' => 20,
                                'date' => $date,
                                'different_date' => $dateDif,
                                'data' => $data,
                                'point' => 0,
                                'description' => 'edited when status completed take',
                            ]
                        ]);
                    }
                } else {
                    //supervisor
                    $statusApproved = $this->statusHistory->getBy([
                        'status_histories.id_reference' => $workOrderId,
                        'status_histories.type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
                        'status_histories.status' => WorkOrderModel::STATUS_VALIDATION_APPROVED
                    ],true);
                    if (!empty($statusApproved)) {
                        $id_spv = $this->employee->getBy(
                            [
                                'ref_employees.id_user'=>  $statusApproved['created_by']
                            ]
                        ,true);
                        $id_spv = $id_spv['id'];
                        if ($dateNow==date('Y-m-d',strtotime($workOrder['completed_at']))) {
                            $date = $dateNow;
                            $dateDif = null;
                        } else {
                            $date = $dateNow;
                            $dateDif = date('Y-m-d',strtotime($workOrder['completed_at']));
                        } 
                        $where = [
                            'id_employee' => $id_spv,
                            'id_component' => 2,
                            'id_reference' => $workOrderId,
                        ];
                        $minPoint = $this->allowance->getPointDiffDate($where);
                        $point = 0;
                        if ($minPoint=='0') {
                            $point = -1;
                        }
                        if ($minPoint==1) {
                            $point = -2;
                        }
                        $cekPoint = $this->allowance->cekPointEditLate($where);
                        if ($minPoint=='-1'){
                            $cekPoint = $this->allowance->cekPointEditLate($where);
                            if($cekPoint > -2){
                                $point = -1;
                            }
                        }
                        if($workOrder['id_branch']!=2 || $workOrder['id_customer']!=9151){
                            $this->allowance->create([
                                'id_employee' => $id_spv,
                                'id_component' => 2,
                                'id_reference' => $workOrderId,
                                'date' => $date,
                                'different_date' => $dateDif,
                                'data' => $data,
                                'point' => $point,
                                'description' => 'edited when status approve',
                            ]);
                        }
                        
                    } else {
                        //tally take
                        $id_employee = $this->employee->getBy(
                            [
                                'ref_employees.id_user'=>  $workOrder['taken_by']
                            ]
                        ,true);
                        $id_employee = $id_employee['id'];
                        if ($dateNow==date('Y-m-d',strtotime($workOrder['completed_at']))) {
                            $date = $dateNow;
                            $dateDif = null;
                        } else {
                            $date = $dateNow;
                            $dateDif = date('Y-m-d',strtotime($workOrder['completed_at']));
                        }      
                        if($workOrder['id_branch']!=2 || $workOrder['id_customer']!=9151){      
                            $this->allowance->create([
                                [
                                    'id_employee' => $id_employee,
                                    'id_component' => 1,
                                    'date' => $date,
                                    'different_date' => $dateDif,
                                    'data' => $data,
                                    'point' => 0,
                                    'description' => 'edited when status completed take',
                                ],
                                [
                                    'id_employee' => $id_employee,
                                    'id_component' => 20,
                                    'date' => $date,
                                    'different_date' => $dateDif,
                                    'data' => $data,
                                    'point' => 0,
                                    'description' => 'edited when status completed take',
                                ]
                            ]);
                        }
                    }
                }
            }
        }
        $this->createHistory($workOrderId);

        // $this->workOrderContainer->deleteContainersByWorkOrder($workOrderId, false);
        // $this->workOrderGoods->deleteGoodsByWorkOrder($workOrderId, false);

        // collect container id or delete all
        $containerId = [];
        $merge_workOrderGoodsId = [];
        if (!empty($containers)) {
            $containerId = array_column($containers, 'workOrderContainerId');
            $containerGoods = array_column($containers, 'goods');

            // get work order goods id inside each of containers
            foreach ($containerGoods as $containerGood) {
                $merge_workOrderGoodsId = array_merge($merge_workOrderGoodsId, array_column($containerGood, 'workOrderGoodsId'));
            }
        }else{
            $this->workOrderContainer->deleteContainersByWorkOrder($workOrderId, false);
        }

        // collect new goods id or delete all
        $itemId = [];
        if (!empty($goods)) {
            $itemId = array_column($goods, 'workOrderGoodsId');
        } else {
            $this->workOrderGoods->delete([
                'id_work_order' => $workOrderId,
                'id_work_order_container iS NULL' => null
            ], false);
            //$this->workOrderGoods->deleteGoodsByWorkOrder($workOrderId, false);
        }

        // delete container and it's goods that not exist in old data
        $jobContainers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderId, true);
        foreach ($jobContainers as &$jobContainer) {
            if (!in_array($jobContainer['id'], $containerId)) {
                $this->workOrderContainer->deleteContainersById($jobContainer['id'], false);
            }

            $jobContainerGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($jobContainer['id']);
            foreach ($jobContainerGoods as $itemJobContainer) {
                if (!in_array($itemJobContainer['id'], $merge_workOrderGoodsId)) {
                    $this->workOrderGoods->deleteGoodsById($itemJobContainer['id'], false);
                }
            }
        }

        // delete goods that not exist in old data
        $jobGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderId, true, true);
        foreach ($jobGoods as $itemGoods) {
            if (!in_array($itemGoods['id'], $itemId)) {
                $this->workOrderGoods->deleteGoodsById($itemGoods['id'], false);
            }
        }

        // insert containers and check if need update of create new one
        if (!empty($containers)) {
            foreach ($containers as $container) {
                // check overtime data
                $overtimeDate = if_empty($container['overtime_date'], date('Y-m-d H:i:s'), '', '', true);
                $overtimeDate = format_date($overtimeDate, 'Y-m-d H:i:s');

                // find out overtime range by given overtime date
                $today = strtoupper(date('l', strtotime($overtimeDate)));
                $Overtime = $this->Overtime->getDayOvertime($today, $branchId);

                $firstOver = strtotime($Overtime['first_overtime']); //Millisecond
                $secondOver = strtotime($Overtime['second_overtime']); //Millisecond
                $overtimeContainer = (date('H', strtotime($overtimeDate)) == '00') ? strtotime(format_date($overtimeDate, '24:i:s')) : strtotime(format_date($overtimeDate, 'H:i:s')); //Millisecond

                // decide overtime type by its range
                if ($overtimeContainer <= $firstOver) {
                    $container['overtime_status'] = 'NORMAL';
                } elseif ($overtimeContainer <= $secondOver) {
                    $container['overtime_status'] = 'OVERTIME 1';
                } else {
                    $container['overtime_status'] = 'OVERTIME 2';
                }

                // update or create container if necessary
                $getWorkOrderContainerById = $this->workOrderContainer->getWorkOrderContainerById($container['workOrderContainerId']);
                $foundContainer = $this->container->getById($container['id_container']);
                $workOrderContainerData = [
                    'id_work_order' => $workOrder['id'],
                    'id_owner' => $workOrder['id_customer'],
                    'id_booking_reference' => if_empty($container['id_booking_reference'], if_empty($workOrder['id_booking_in'], null)),
                    'id_container' => $container['id_container'],
                    'id_position' => $container['id_position'],
                    'length_payload' => $container['length_payload'],
                    'width_payload' => $container['width_payload'],
                    'height_payload' => $container['height_payload'],
                    'volume_payload' => $container['volume_payload'],
                    'seal' => $container['seal'],
                    'is_empty' => $container['is_empty'],
                    'is_hold' => $container['is_hold'],
                    'status' => $container['status'],
                    'status_danger' => $container['status_danger'],
                    'overtime_status' => $container['overtime_status'],
                    'overtime_date' => $overtimeDate,
                    'quantity' => 1,
                    'created_by' => $userId
                ];
                if (empty($getWorkOrderContainerById) || empty($container['workOrderContainerId'])) {
                    $this->workOrderContainer->insertWorkOrderContainer($workOrderContainerData);
                } else {
                    $this->workOrderContainer->updateWorkOrderContainer($workOrderContainerData, $container['workOrderContainerId']);
                }

                $completedAt[] = $overtimeDate;
                $workOrderContainerId = $this->db->insert_id();
                $positionBlocks = if_empty(explode(',', $container['id_position_blocks']), []);

                foreach ($positionBlocks as $blockId) {
                    $this->workOrderContainerPosition->create([
                        'id_work_order_container' => $workOrderContainerId,
                        'id_position_block' => $blockId
                    ]);
                }

                // insert the container goods
                if (key_exists('goods', $container)) {
                    foreach ($container['goods'] as $item) {
                        $overtimeDate = if_empty($item['overtime_date'], date('Y-m-d H:i:s'), '', '', true);
                        $overtimeDate = format_date($overtimeDate, 'Y-m-d H:i:s');

                        $today = strtoupper(date('l', strtotime($overtimeDate)));
                        $Overtime = $this->Overtime->getDayOvertime($today, $branchId);

                        $firstOver = strtotime($Overtime['first_overtime']); //Millisecond
                        $secondOver = strtotime($Overtime['second_overtime']); //Millisecond
                        $overtimeGoods = (date('H', strtotime($overtimeDate)) == '00') ? strtotime(format_date($overtimeDate, '24:i:s')) : strtotime(format_date($overtimeDate, 'H:i:s')); //Millisecond

                        if ($overtimeGoods <= $firstOver) {
                            $item['overtime_status'] = 'NORMAL';
                        } elseif ($overtimeGoods <= $secondOver) {
                            $item['overtime_status'] = 'OVERTIME 1';
                        } else {
                            $item['overtime_status'] = 'OVERTIME 2';
                        }

                        $getWorkOrderGoodsById = $this->workOrderGoods->getWorkOrderGoodsById($item['workOrderGoodsId']);
                        $workOrderContainerGoodsData = [
                            'id_work_order' => $workOrder['id'],
                            'id_work_order_container' => if_empty($container['workOrderContainerId'], $workOrderContainerId),
                            'id_owner' => $workOrder['id_customer'],
                            'id_booking_reference' => if_empty($item['id_booking_reference'], if_empty($workOrder['id_booking_in'], null)),
                            'id_goods' => $item['id_goods'],
                            'id_unit' => $item['id_unit'],
                            'id_position' => $item['id_position'],
                            'quantity' => $item['quantity'],
                            'unit_weight' => $item['unit_weight'],
                            'unit_gross_weight' => $item['unit_gross_weight'],
                            'unit_length' => $item['unit_length'],
                            'unit_width' => $item['unit_width'],
                            'unit_height' => $item['unit_height'],
                            'unit_volume' => $item['unit_volume'],
                            'no_pallet' => $item['no_pallet'],
                            'is_hold' => $item['is_hold'],
                            'status' => $item['status'],
                            'status_danger' => $item['status_danger'],
                            'description' => $item['description'],
                            'ex_no_container' => if_empty(get_if_exist($foundContainer, 'no_container', $item['ex_no_container']), null),
                            'overtime_status' => $item['overtime_status'],
                            'overtime_date' => format_date($overtimeDate, 'Y-m-d H:i:s'),
                            'created_by' => $userId
                        ];
                        if (empty($getWorkOrderGoodsById) || empty($item['workOrderGoodsId'])) {
                            $this->workOrderGoods->insertWorkOrderGoods($workOrderContainerGoodsData);
                            $workOrderGoodsId = $this->db->insert_id();
                        } else {
                            $this->workOrderGoods->updateWorkOrderGoods($workOrderContainerGoodsData, $item['workOrderGoodsId']);
                            $workOrderGoodsId = $item['workOrderGoodsId'];
                        }

                        // update goods
                        $this->goods->update([
                            'unit_weight' => $item['unit_weight'],
                            'unit_gross_weight' => $item['unit_gross_weight'],
                            'unit_length' => $item['unit_length'],
                            'unit_width' => $item['unit_width'],
                            'unit_height' => $item['unit_height'],
                            'unit_volume' => $item['unit_volume'],
                        ], $item['id_goods']);

                        $completedAt[] = $overtimeDate;
                        $this->workOrderGoodsPosition->deleteWorkOrderGoodsPositionByworkOrdergoodsId($item['workOrderGoodsId']);
                        $positionBlocks = if_empty(explode(',', $item['id_position_blocks']), []);
                        foreach ($positionBlocks as $blockId) {
                            $this->workOrderGoodsPosition->create([
                                'id_work_order_goods' => $item['workOrderGoodsId'],
                                'id_position_block' => $blockId
                            ]);
                        }

                        // add goods photo
                        $tempPhotos = explode(',', get_if_exist($item, 'temp_photos', ''));
                        $tempPhotoDescriptions = explode('||', get_if_exist($item, 'temp_photo_descriptions', ''));
                        foreach ($tempPhotos as $index => $file) {
                            if (!empty($file)) {
                                $sourceFile = 'temp/' . $file;
                                $destFile = 'work-order-goods/' . format_date('now', 'Y/m/') . $file;
                                if ($this->uploader->setDriver('s3')->move($sourceFile, $destFile)) {
                                    $this->workOrderGoodsPhoto->create([
                                        'id_work_order_goods' => $workOrderGoodsId,
                                        'src' => $destFile,
                                        'url' => $this->uploader->setDriver('s3')->getUrl($destFile),
                                        'description' => get_if_exist($tempPhotoDescriptions, $index, null)
                                    ]);
                                }
                            }
                        }
                    }
                }
            }
        }

        // insert independent goods and check if need update of create new one
        if (!empty($goods)) {
            foreach ($goods as $item) {
                $overtimeDate = if_empty($item['overtime_date'], date('Y-m-d H:i:s'), '', '', true);
                $overtimeDate = format_date($overtimeDate, 'Y-m-d H:i:s');

                $today = strtoupper(date('l', strtotime($overtimeDate)));
                $Overtime = $this->Overtime->getDayOvertime($today, $branchId);

                $firstOver = strtotime($Overtime['first_overtime']); //Millisecond
                $secondOver = strtotime($Overtime['second_overtime']); //Millisecond
                $overtimeGoods = (date('H', strtotime($overtimeDate)) == '00') ? strtotime(format_date($overtimeDate, '24:i:s')) : strtotime(format_date($overtimeDate, 'H:i:s')); //Millisecond

                // decide overtime status by its overtime date
                if ($overtimeGoods <= $firstOver) {
                    $item['overtime_status'] = 'NORMAL';
                } elseif ($overtimeGoods <= $secondOver) {
                    $item['overtime_status'] = 'OVERTIME 1';
                } else {
                    $item['overtime_status'] = 'OVERTIME 2';
                }

                $getWorkOrderGoodsById = $this->workOrderGoods->getWorkOrderGoodsById($item['workOrderGoodsId']);
                $workOrderGoodsData = [
                    'id_work_order' => $workOrder['id'],
                    'id_owner' => $workOrder['id_customer'],
                    'id_booking_reference' => if_empty($item['id_booking_reference'], if_empty($workOrder['id_booking_in'], null)),
                    'id_goods' => $item['id_goods'],
                    'id_unit' => $item['id_unit'],
                    'id_position' => $item['id_position'],
                    'quantity' => $item['quantity'],
                    'unit_weight' => $item['unit_weight'],
                    'unit_gross_weight' => $item['unit_gross_weight'],
                    'unit_length' => $item['unit_length'],
                    'unit_width' => $item['unit_width'],
                    'unit_height' => $item['unit_height'],
                    'unit_volume' => $item['unit_volume'],
                    'no_pallet' => $item['no_pallet'],
                    'is_hold' => $item['is_hold'],
                    'status' => $item['status'],
                    'status_danger' => $item['status_danger'],
                    'description' => $item['description'],
                    'ex_no_container' => if_empty($item['ex_no_container'], null),
                    'overtime_status' => $item['overtime_status'],
                    'overtime_date' => $overtimeDate,
                    'created_by' => $userId
                ];
                if (empty($getWorkOrderGoodsById) || empty($item['workOrderGoodsId'])) {
                    $this->workOrderGoods->insertWorkOrderGoods($workOrderGoodsData);
                    $workOrderGoodsId = $this->db->insert_id();
                } else {
                    $this->workOrderGoods->updateWorkOrderGoods($workOrderGoodsData, $item['workOrderGoodsId']);
                    $workOrderGoodsId = $item['workOrderGoodsId'];
                }

                // update goods
                $this->goods->update([
                    'unit_weight' => $item['unit_weight'],
                    'unit_gross_weight' => $item['unit_gross_weight'],
                    'unit_length' => $item['unit_length'],
                    'unit_width' => $item['unit_width'],
                    'unit_height' => $item['unit_height'],
                    'unit_volume' => $item['unit_volume'],
                ], $item['id_goods']);

                $completedAt[] = $overtimeDate;

                $positionBlocks = if_empty(explode(',', $item['id_position_blocks']), []);
                foreach ($positionBlocks as $blockId) {
                    $this->workOrderGoodsPosition->create([
                        'id_work_order_goods' => $workOrderGoodsId,
                        'id_position_block' => $blockId
                    ]);
                }

                // add goods photo
                $tempPhotos = explode(',', get_if_exist($item, 'temp_photos', ''));
                $tempPhotoDescriptions = explode('||', get_if_exist($item, 'temp_photo_descriptions', ''));
                foreach ($tempPhotos as $index => $file) {
                    if (!empty($file)) {
                        $sourceFile = 'temp/' . $file;
                        $destFile = 'work-order-goods/' . format_date('now', 'Y/m/') . $file;
                        if ($this->uploader->setDriver('s3')->move($sourceFile, $destFile)) {
                            $this->workOrderGoodsPhoto->create([
                                'id_work_order_goods' => $workOrderGoodsId,
                                'src' => $destFile,
                                'url' => $this->uploader->setDriver('s3')->getUrl($destFile),
                                'description' => get_if_exist($tempPhotoDescriptions, $index, null)
                            ]);
                        }
                    }
                }
            }
        }

        $completeAtMax = date("Y-m-d H:i:s", max(array_map('strtotime', $completedAt)));
        if (!empty($transporterCategory)) {
            if ($transporterCategory == "EXTERNAL") {
                $getTep = $this->transporterEntryPermit->getById($transporterId);
                if (!empty($getTep) && !empty($getTep['checked_out_at']) && !empty($chassisId)) {
                    $this->transporterEntryPermitChassis->update([
                        'id_tep_out' => $getTep['id'],
                        'checked_out_at' => $getTep['checked_out_at'],
                        'checked_out_description' => 'Checked out from TEP ' . $getTep['tep_code'] . ' (from Edit Job)'
                    ], $chassisId);
                }

                if ($workOrder['id_transporter_entry_permit'] != $transporterId) {
                    $this->workOrder->updateWorkOrder([
                        'id_transporter_entry_permit' => $transporterId,
                    ], $workOrderId);
                    $safeConductTep = $this->safeConduct->getSafeConductById($workOrder['id_safe_conduct']);

                    $getTep = $this->transporterEntryPermit->getByIdNonBase($transporterId);
                    if (!empty($safeConductTep)) {
                        // We fetch existing before update to prevent data mixed between groups
                        $existingSafeConductSameVehicles = $this->safeConduct->getBy([
                            'safe_conducts.id_transporter_entry_permit' => $transporterId,
                        ]);

                        $this->safeConduct->updateSafeConduct([
                            'id_transporter_entry_permit' => $transporterId,
                            'vehicle_type' => $getTep['receiver_vehicle'],
                            'no_police' => $getTep['receiver_no_police'],
                            'driver' => $getTep['receiver_name'],
                            'security_in_date' => $getTep['checked_in_at'],
                            // 'security_out_date' => !empty($getTep['checked_out_at']) ? $getTep['checked_out_at'] : null,
                            'updated_by' => UserModel::authenticatedUserData('id'),
                            'updated_at' => sql_date_format('now'),
                        ], $safeConductTep['id']);

                        // Check if safe conduct needs to be grouped in the new tep,
                        // find out the existing data is already grouped
                        $safeConductSameVehicles = $this->safeConduct->getBy([
                            'safe_conducts.id_transporter_entry_permit' => $transporterId,
                        ]);
                        if (!empty($existingSafeConductSameVehicles)) { // at least there is one and + current then 2 in totals
                            $safeConductGroupId = $existingSafeConductSameVehicles[0]['id_safe_conduct_group']; // pick first data
                            if (empty($safeConductGroupId)) {
                                $this->safeConductGroup->create([
                                    'id_branch' => get_active_branch_id(),
                                    'no_safe_conduct_group' => $this->safeConductGroup->getAutoNumber()
                                ]);
                                $safeConductGroupId = $this->db->insert_id();
                            }
                            // should be there are at least 2 safe conducts
                            foreach ($safeConductSameVehicles as $relatedSafeConduct) {
                                $this->safeConduct->updateSafeConduct([
                                    'id_safe_conduct_group' => $safeConductGroupId,
                                ], $relatedSafeConduct['id']);
                            }
                        } else {
                            // new targeted tep no safe conduct attached, then remove from the group
                            $this->safeConduct->updateSafeConduct([
                                'id_safe_conduct_group' => null,
                            ], $safeConductTep['id']);
                        }

                        // check other safe conducts from old tep is left alone, then remove the group as well
                        $oldSafeConductSameVehicles = $this->safeConduct->getBy([
                            'safe_conducts.id_transporter_entry_permit' => $safeConductTep['id_transporter_entry_permit'],
                        ]);
                        if (count($oldSafeConductSameVehicles) == 1) {
                            $this->safeConduct->updateSafeConduct([
                                'id_safe_conduct_group' => null,
                            ], $oldSafeConductSameVehicles[0]['id']);
                        }

                        // safely remove existing group number, because no safe conducts are attached
                        if (empty($oldSafeConductSameVehicles) || count($oldSafeConductSameVehicles) == 1) {
                            $this->safeConductGroup->delete($safeConductTep['id_safe_conduct_group']);
                        }
                    }
                }
            } else {
                if ($workOrder['id_vehicle'] != $transporterId) {
                    $this->workOrder->updateWorkOrder([
                        'id_vehicle' => $transporterId,
                    ], $workOrderId);
                    $safeConductIn = $this->safeConduct->getSafeConductById($workOrder['id_safe_conduct']);
                    $vehicleIn = $this->vehicle->getById($transporterId);
                    if (!empty($safeConductIn)) {
                        $this->safeConduct->updateSafeConduct([
                            'vehicle_type' => $vehicleIn['vehicle_type'],
                            'no_police' => $vehicleIn['no_plate'],
                            'updated_by' => UserModel::authenticatedUserData('id'),
                            'updated_at' => sql_date_format('now'),
                        ], $safeConductIn['id']);
                    }
                }
            }
        }

        $this->workOrder->updateWorkOrder([
            'id_tep_chassis' => if_empty($chassisId, null),
            'id_warehouse' => $warehouseId,
            'description' => $description,
            'taken_at' => sql_date_format($takenAt, true, if_empty($workOrder['taken_at'], null)),
            'completed_at' => sql_date_format($completeAtMax, true, if_empty($workOrder['completed_at'], null)),
            'gate_in_date' => sql_date_format($gateInDate, true, if_empty($workOrder['gate_in_date'], null)),
            'gate_out_date' => sql_date_format($gateOutDate, true, if_empty($workOrder['gate_out_date'], null)),
            'updated_by' => $userId,
            'updated_at' => sql_date_format('now'),
            'space' => abs($space),
            'stock_pallet' => abs($pallet),
        ], $workOrderId);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            flash('success', "Job {$workOrder['no_work_order']} successfully updated", 'work-order');
        } else {
            flash('danger', 'Something is getting wrong, try again or contact administrator');
        }
        $this->edit($workOrderId);
    }

    private function createHistory($workOrderId)
    {
        // PUSH FULL HISTORY TABLE, ALTERNATIVE USE SQL TRIGGER
        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
        $containers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderId, true);
        foreach ($containers as &$container) {
            $containerGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($container['id']);
            $container['goods'] = $containerGoods;
        }
        $goods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderId, true, true);
        foreach ($goods as &$item) {
            $goodsItem = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderGoods($item['id']);
            $item['goods'] = $goodsItem;
        }

        $this->workOrderHistory->createWorkOrder([
            'id_work_order' => $workOrderId,
            'id_handling' => $workOrder['id_handling'],
            'id_safe_conduct' => $workOrder['id_safe_conduct'],
            'id_warehouse' => $workOrder['id_warehouse'],
            'id_transporter_entry_permit' => $workOrder['id_transporter_entry_permit'],
            'id_vehicle' => $workOrder['id_vehicle'],
            'id_armada' => $workOrder['id_armada'],
            'armada_type' => $workOrder['armada_type'],
            'no_work_order' => $workOrder['no_work_order'],
            'queue' => $workOrder['queue'],
            'gate_in_date' => $workOrder['gate_in_date'],
            'gate_in_description' => $workOrder['gate_in_description'],
            'gate_out_date' => $workOrder['gate_out_date'],
            'gate_out_description' => $workOrder['gate_out_description'],
            'overtime' => $workOrder['overtime'],
            'staple' => $workOrder['staple'],
            'man_power' => $workOrder['man_power'],
            'forklift' => $workOrder['forklift'],
            'tools' => $workOrder['tools'],
            'materials' => $workOrder['materials'],
            'armada_description' => $workOrder['armada_description'],
            'shipping_route' => $workOrder['shipping_route'],
            'print_total' => $workOrder['print_total'],
            'print_max' => $workOrder['print_max'],
            'status' => $workOrder['status'],
            'status_validation' => $workOrder['status_validation'],
            'taken_by' => $workOrder['taken_by'],
            'taken_at' => $workOrder['taken_at'],
            'completed_at' => $workOrder['completed_at'],
            'mode' => $workOrder['mode'],
            'attachment' => $workOrder['attachment'],
            'description' => $workOrder['description'],
            'is_void' => $workOrder['is_void'],
            'is_deleted' => $workOrder['is_deleted'],
            'created_at' => sql_date_format('now'),
            'created_by' => UserModel::authenticatedUserData('id'),
            'updated_at' => $workOrder['updated_at'],
            'updated_by' => $workOrder['updated_by'],
            'deleted_at' => $workOrder['deleted_at'],
            'deleted_by' => $workOrder['deleted_by'],
        ]);
        $workOrderHistoryId = $this->db->insert_id();

        foreach ($containers as $workOrderContainer) {
            $this->workOrderContainerHistory->insertWorkOrderContainer([
                'id_work_order' => $workOrderHistoryId,
                'id_container' => $workOrderContainer['id_container'],
                'id_owner' => $workOrderContainer['id_owner'],
                'id_position' => $workOrderContainer['id_position'],
                'quantity' => $workOrderContainer['quantity'],
                'seal' => $workOrderContainer['seal'],
                'is_empty' => $workOrderContainer['is_empty'],
                'is_hold' => $workOrderContainer['is_hold'],
                'status' => $workOrderContainer['status'],
                'status_danger' => $workOrderContainer['status_danger'],
                'overtime_status' => $workOrderContainer['overtime_status'],
                'overtime_date' => $workOrderContainer['overtime_date'],
                'volume_payload' => $workOrderContainer['volume_payload'],
                'length_payload' => $workOrderContainer['length_payload'],
                'width_payload' => $workOrderContainer['width_payload'],
                'height_payload' => $workOrderContainer['height_payload'],
                'description' => $workOrderContainer['description'],
                'is_void' => $workOrderContainer['is_void'],
                'is_deleted' => $workOrderContainer['is_deleted'],
                'created_at' => $workOrderContainer['created_at'],
                'created_by' => $workOrderContainer['created_by'],
                'updated_at' => $workOrderContainer['updated_at'],
                'updated_by' => $workOrderContainer['updated_by'],
                'deleted_at' => $workOrderContainer['deleted_at'],
                'deleted_by' => $workOrderContainer['deleted_by'],
            ]);
            $workOrderContainerHistoryId = $this->db->insert_id();

            if(!empty($workOrderContainer['goods'])) {
                foreach ($workOrderContainer['goods'] as $workOrderContainerGoods) {
                    $this->workOrderGoodsHistory->insertWorkOrderGoods([
                        'id_work_order' => $workOrderHistoryId,
                        'id_work_order_container' => $workOrderContainerHistoryId,
                        'id_owner' => $workOrderContainerGoods['id_owner'],
                        'id_goods' => $workOrderContainerGoods['id_goods'],
                        'id_unit' => $workOrderContainerGoods['id_unit'],
                        'id_position' => $workOrderContainerGoods['id_position'],
                        'quantity' => $workOrderContainerGoods['quantity'],
                        'unit_weight' => $workOrderContainerGoods['unit_weight'],
                        'unit_gross_weight' => $workOrderContainerGoods['unit_gross_weight'],
                        'unit_length' => $workOrderContainerGoods['unit_length'],
                        'unit_width' => $workOrderContainerGoods['unit_width'],
                        'unit_height' => $workOrderContainerGoods['unit_height'],
                        'unit_volume' => $workOrderContainerGoods['unit_volume'],
                        'no_pallet' => $workOrderContainerGoods['no_pallet'],
                        'ex_no_container' => $workOrderContainerGoods['ex_no_container'],
                        'is_hold' => $workOrderContainerGoods['is_hold'],
                        'status' => $workOrderContainerGoods['status'],
                        'status_danger' => $workOrderContainerGoods['status_danger'],
                        'overtime_status' => $workOrderContainerGoods['overtime_status'],
                        'overtime_date' => $workOrderContainerGoods['overtime_date'],
                        'description' => $workOrderContainerGoods['description'],
                        'is_void' => $workOrderContainerGoods['is_void'],
                        'is_deleted' => $workOrderContainerGoods['is_deleted'],
                        'created_at' => $workOrderContainerGoods['created_at'],
                        'created_by' => $workOrderContainerGoods['created_by'],
                        'updated_at' => $workOrderContainerGoods['updated_at'],
                        'updated_by' => $workOrderContainerGoods['updated_by'],
                        'deleted_at' => $workOrderContainerGoods['deleted_at'],
                        'deleted_by' => $workOrderContainerGoods['deleted_by'],
                    ]);
                }
            }
        }

        foreach ($goods as $workOrderHistoryGood) {
            $this->workOrderGoodsHistory->insertWorkOrderGoods([
                'id_work_order' => $workOrderHistoryId,
                'id_owner' => $workOrderHistoryGood['id_owner'],
                'id_goods' => $workOrderHistoryGood['id_goods'],
                'id_unit' => $workOrderHistoryGood['id_unit'],
                'id_position' => $workOrderHistoryGood['id_position'],
                'quantity' => $workOrderHistoryGood['quantity'],
                'unit_weight' => $workOrderHistoryGood['unit_weight_item'],
                'unit_gross_weight' => $workOrderHistoryGood['unit_gross_weight_item'],
                'unit_length' => $workOrderHistoryGood['unit_length_item'],
                'unit_width' => $workOrderHistoryGood['unit_width_item'],
                'unit_height' => $workOrderHistoryGood['unit_height_item'],
                'unit_volume' => $workOrderHistoryGood['unit_volume_item'],
                'no_pallet' => $workOrderHistoryGood['no_pallet'],
                'ex_no_container' => $workOrderHistoryGood['ex_no_container'],
                'is_hold' => $workOrderHistoryGood['is_hold'],
                'status' => $workOrderHistoryGood['status'],
                'status_danger' => $workOrderHistoryGood['status_danger'],
                'overtime_status' => $workOrderHistoryGood['overtime_status'],
                'overtime_date' => $workOrderHistoryGood['overtime_date'],
                'description' => $workOrderHistoryGood['description'],
                'is_void' => $workOrderHistoryGood['is_void'],
                'is_deleted' => $workOrderHistoryGood['is_deleted'],
                'created_at' => $workOrderHistoryGood['created_at'],
                'created_by' => $workOrderHistoryGood['created_by'],
                'updated_at' => $workOrderHistoryGood['updated_at'],
                'updated_by' => $workOrderHistoryGood['updated_by'],
                'deleted_at' => $workOrderHistoryGood['deleted_at'],
                'deleted_by' => $workOrderHistoryGood['deleted_by'],
            ]);
        }
    }

    /**
     * Select mode for tally.
     * @param $workOrder
     */
    private function select_mode($workOrder)
    {
        $data = [
            'title' => "Tally Select Mode",
            'subtitle' => "Select input mode",
            'page' => "tally/mode",
            'workOrder' => $workOrder,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Mode input create tally.
     *
     * @param $workOrder
     * @param $containers
     * @param $goods
     */
    private function mode_tally($workOrder, $containers, $goods)
    {
        $this->load->model('ContainerModel', 'container');
        $this->load->model('PositionModel', 'position');

        $today = strtoupper(date('l'));
        $branchId = get_active_branch('id');
        $Overtime = $this->Overtime->getDayOvertime($today, $branchId);
        $handling_type = $workOrder['handling_type'];
        if (empty($Overtime)) {
            flash('danger', "Overtime {$today} is not exists, please contact administrator!", 'tally');
        } else {
            $booking = $this->booking->getBookingById($workOrder['id_booking']);
            $bookingReferences = $this->bookingReference->getBy(['booking_references.id_booking' => $booking['id']]);

            $hasValidatedEditPermission = AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATED_EDIT);
            if ($hasValidatedEditPermission) {
                $workOrder['status_unlock_handheld'] = WorkOrderUnlockHandheldModel::STATUS_UNLOCKED;
            } else {
                $workOrderUnlockHandheld = $this->workOrderUnlockHandheld->getBy([
                    'id_work_order' => $workOrder['id']
                ], true);
                $workOrder['status_unlock_handheld'] = if_empty($workOrderUnlockHandheld['status'], WorkOrderUnlockHandheldModel::STATUS_LOCKED);
            }

            $data = [
                'mode' => '',
                'booking' => $booking,
                'bookingReferences' => $bookingReferences,
                'workOrder' => $workOrder,
                'handling_type' => $handling_type,
                'warehouses' => $this->warehouse->getBy(['id_branch' => $workOrder['id_branch']]),
                'containers' => $containers,
                'goods' => $goods,
                'Overtime' => $Overtime,
                'units' => $this->unit->getAll()
            ];
            if(isset($workOrder['multiplier_container']) && $workOrder['multiplier_container'] < 0){
                $data['inputSourceContainer'] = 'STOCK';
            }

            $this->render('tally/create', $data);
        }
    }
    /**
     * Approve job tally.
     */
    public function approve_job()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_APPROVED);
        $workOrderId = $this->input->post('id');
        $workOrderNo = $this->input->post('no');
        $message = $this->input->post('message');
        
        $goods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderId, false, false);
        
        foreach ($goods as $item) {
            if(empty($item['total_photo'])){
                flash('danger', "Approved job <strong>{$workOrderNo}</strong> failed, due goods {$item['goods_name']} there are no photos");
                redirect('work-order/view-check/'.$workOrderId); 
            }
        }
        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
        
        foreach ($goods as  $item) {
            if (!empty($workOrder['multiplier_goods']) && empty($item['total_photo'])) {
                flash('danger', "Approved job <strong>{$workOrderNo}</strong> failed, please upload photos for all goods!");
                redirect('work-order/view/' . $workOrderId);
                break;
            }
        }

        $goodsHave='';
        if ($goods != null) {
            $goodsHave = 'Have';
        }
        if(($workOrder['handling_type'] == 'LOAD' || $workOrder['handling_type'] == 'STUFFING') && !empty($goodsHave)){
            foreach ($goods as &$item) {
                $remaining = $this->input->post('remain_'.$item['id']);
                $remaining2 = $this->input->post('remain_'.$item['id']);

                $max_quantity = $this->reportStock->getStockGoodsRemaining(['data' => 'all', 'owner' => $workOrder['id_customer']],$item['id_goods'],$item['id_unit'],$item['ex_no_container']);
                $max_quantity = $max_quantity['stock_quantity'];
                $max_quantity_booking = $this->getStockBookingOutRemaining($workOrder['id_booking'], $item['id_goods']);
                
                if (trim($remaining2)=="") {
                    $remaining = $max_quantity;
                }
                if(!is_null($item['stock_remaining_tally'])){
                    if (trim($remaining2)=="") {
                        if ($item['stock_remaining_tally']!=$remaining) {
                            flash('danger', "Approved job <strong>{$workOrderNo}</strong> failed, because different stock");
                            redirect('work-order/view-check/'.$workOrderId); 
                        }
                    }else if ($item['stock_remaining_tally']!=$remaining ) {
                        if ($max_quantity!=$remaining) {
                            flash('danger', "Approved job <strong>{$workOrderNo}</strong> failed, because different stocks");
                            redirect('work-order/view-check/'.$workOrderId);              
                        }
                    }else if ($item['stock_remaining_tally']==$remaining ){
                        if ($max_quantity!=$remaining) {
                            flash('danger', "Approved job <strong>{$workOrderNo}</strong> failed, because differents stocks");
                            redirect('work-order/view-check/'.$workOrderId);              
                        }
                    }  
                }

                if ($max_quantity<0) {
                    flash('danger', "Approved job <strong>{$workOrderNo}</strong> failed, because quantity more than stock");
                    redirect('work-order/view-check/'.$workOrderId);
                }
                // if ($max_quantity_booking<0) {
                //     flash('danger', "Approved job <strong>{$workOrderNo}</strong> failed, because quantity more than quantity booking");
                //     redirect('work-order/view-check/'.$workOrderId);
                // }
                $item['stock_remaining'] = $max_quantity;
            }
        }
        $this->db->trans_start();
        if(($workOrder['handling_type'] == 'LOAD' || $workOrder['handling_type'] == 'STUFFING') && !empty($goodsHave)){
            $remaining_spv=0;
            foreach ($goods as $barang) {
                $remaining_spv = $this->input->post('remain_'.$barang['id']);
                if (trim($remaining_spv)=="") {
                    $remaining_spv = $barang['stock_remaining'];
                }
                $this->workOrderGoods->updateWorkOrderGoods([
                    'stock_remaining_spv' => $remaining_spv,
                ],$barang['id']);
            }
        }
        $dateNow = date('Y-m-d');
        $statusChecked = $this->statusHistory->getBy([
            'status_histories.id_reference' => $workOrderId,
            'status_histories.type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
            'status_histories.status' => WorkOrderModel::STATUS_VALIDATION_CHECKED
        ],true);
        $id_spv = $this->employee->getBy(
            [
                'ref_employees.id_user'=>  UserModel::authenticatedUserData('id')
            ]
        ,true);
        $id_spv = $id_spv['id'];
        $data = json_encode($workOrder);
        
        if($workOrder['id_branch']!=2 || $workOrder['id_customer']!=9151){//sorik merapi branch medan 1 tidak dapat poin
            if (!empty($statusChecked)) {
                if (date('Y-m-d H:i:s') > date('Y-m-d H:i:s',strtotime('+2 hour',strtotime($workOrder['completed_at']))) && $workOrder['handling_type'] != 'LOAD') {
                    $dateDif = date('Y-m-d',strtotime('+2 hour',strtotime($workOrder['completed_at'])));
                    $fill = [
                        'id_employee' => $id_spv,
                        'id_component' => 2,
                        'id_reference' => $workOrderId,
                        'date' => $dateNow,
                    ];
                    if ($this->allowance->cekReference($fill)) {
                        $this->allowance->create([
                            'id_employee' => $id_spv,
                            'id_component' => 2,
                            'id_reference' => $workOrderId,
                            'date' => $dateNow,
                            'different_date' => date('Y-m-d') == date('Y-m-d',strtotime('+2 hour',strtotime($workOrder['completed_at'])))?null:$dateDif,
                            'data' => $data,
                            'point' => -1,
                            'description' => 'late approve completed job',
                        ]);
                    }
                }
            }
        }
        $diffStock = false;
        $goodsStock = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderId, true, true);
        if (isset($goodsStock)) {
            foreach ($goodsStock as $item) {
                if (!is_null($item['stock_remaining_tally']) && $item['stock_remaining_tally']!=$item['stock_remaining_spv']) {
                    $diffStock = true;
                }
            }
        } 
        if ($diffStock) {
            //tally checked
            $statusChecked = $this->statusHistory->getBy([
                'status_histories.id_reference' => $workOrderId,
                'status_histories.type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
                'status_histories.status' => WorkOrderModel::STATUS_VALIDATION_CHECKED
            ],true);
            $id_employee = $this->employee->getBy(
                [
                    'ref_employees.id_user'=>  $statusChecked['created_by']
                ]
            ,true);
            $id_employee = $id_employee['id'];
            if ($dateNow==date('Y-m-d',strtotime($statusChecked['created_at']))) {
                $date = $dateNow;
                $dateDif = null;
            } else {
                $date = $dateNow;
                $dateDif = date('Y-m-d',strtotime($statusChecked['created_at']));
            } 
            if($workOrder['id_branch']!=2 || $workOrder['id_customer']!=9151){
                $this->allowance->create([
                    [
                        'id_employee' => $id_employee,
                        'id_component' => 1,
                        'date' => $date,
                        'different_date' => $dateDif,
                        'data' => $data,
                        'point' => 0,
                        'description' => 'approve when status checked different stock',
                    ],
                    [
                        'id_employee' => $id_employee,
                        'id_component' => 20,
                        'date' => $date,
                        'different_date' => $dateDif,
                        'data' => $data,
                        'point' => 0,
                        'description' => 'approve when status checked different stock',
                    ]
                ]);
            }
        }
        //approve job point +1 
        $fill = [
            'id_employee' => $id_spv,
            'id_component' => 2,
            'id_reference' => $workOrderId,
            'date' => $dateNow,
        ];
        if($workOrder['id_branch']!=2 || $workOrder['id_customer']!=9151){
            if ($this->allowance->cekReference($fill)) {
                if (date('Y-m-d H:i:s') > date('Y-m-d H:i:s',strtotime('+2 hour',strtotime($workOrder['completed_at']))) && $workOrder['handling_type'] != 'LOAD') {
                    $this->allowance->create([
                        'id_employee' => $id_spv,
                        'id_component' => 2,
                        'id_reference' => $workOrderId,
                        'date' => $dateNow,
                        'different_date' => date('Y-m-d') == date('Y-m-d',strtotime('+2 hour',strtotime($workOrder['completed_at'])))?null:$dateDif,
                        'data' => $data,
                        'point' => -1,
                        'description' => 'late approve completed job',
                    ]);
                }else{
                    $this->allowance->create([
                        'id_employee' => $id_spv,
                        'id_component' => 2,
                        'id_reference' => $workOrderId,
                        'date' => $dateNow,
                        'different_date' => null,
                        'data' => $data,
                        'point' => 1,
                        'description' => 'approve job',
                    ]);
                }
            }
        }   
        $this->workOrder->updateWorkOrder([
            'updated_by' => UserModel::authenticatedUserData('id'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status_validation' => WorkOrderModel::STATUS_VALIDATION_APPROVED,
        ], $workOrderId);

        $this->statusHistory->create([
            'id_reference' => $workOrderId,
            'type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
            'status' => WorkOrderModel::STATUS_VALIDATION_APPROVED,
            'description' => $message
        ]);
        if (($workOrder['handling_type'] == 'LOAD' || $workOrder['handling_type'] == 'EMPTY CONTAINER') && !empty($workOrder['id_transporter_entry_permit'])) {
            $handlingTypeId = get_setting('default_moving_out_handling');
            $noSafeConduct = $this->safeConduct->getAutoNumberSafeConduct(SafeConductModel::TYPE_CODE_OUT);
            $bookingId = $workOrder['id_booking'];
            $customerId = $workOrder['id_customer'];
            $creatorId = UserModel::authenticatedUserData('id');

            // create new handling
            $noHandling = $this->handling->getAutoNumberHandlingRequest();
            $handlingData = [
                'no_handling' => $noHandling,
                'id_handling_type' => $handlingTypeId,
                'id_booking' => $bookingId,
                'id_customer' => $customerId,
                'handling_date' => date('Y-m-d H:i:s'),
                'status' => 'APPROVED',
                'validated_by' => UserModel::authenticatedUserData('id'),
                'validated_at' => date('Y-m-d H:i:s'),
                'created_by' => $creatorId
            ];
            $this->handling->createHandling($handlingData);
            $handlingId = $this->db->insert_id();
            
            $components = $this->component->getByHandlingType($handlingTypeId);
            foreach ($components as $component) {
                $this->handlingComponent->createHandlingComponent([
                    'id_handling' => $handlingId,
                    'id_component' => $component['id'],
                    'quantity' => $component['default_value'],
                    'description' => $component['component_description'],
                    'status' => 'APPROVED'
                ]);
            }
            $tep=$workOrder['id_transporter_entry_permit'];
            $getTep = $this->transporterEntryPermit->getByIdNonBase($tep);
            $this->safeConduct->createSafeConduct([
                'id_booking' => $bookingId,
                'id_handling' => $handlingId,
                'id_eseal' => 0,
                'id_source_warehouse' => 77,//deafault tci
                'id_transporter_entry_permit' => if_empty($tep, null),
                'no_safe_conduct' => $noSafeConduct,
                'expedition_type' => 'EXTERNAL',
                'cy_date' => null,
                'vehicle_type' => !empty($getTep['receiver_vehicle']) ? $getTep['receiver_vehicle'] : null,
                'no_police' =>  !empty($getTep['receiver_no_police']) ? $getTep['receiver_no_police'] : null,
                'driver' => !empty($getTep['receiver_name']) ? $getTep['receiver_name'] : null,
                'expedition' => $workOrder['expedition'],
                'description' => 'auto create safeconduct',
                'type' => 'OUTBOUND',
                'source' => 'WO',
                'security_in_date' =>  !empty($getTep['checked_in_at']) ? $getTep['checked_in_at'] : null,
                'security_out_date' => !empty($getTep['checked_out_at']) ? $getTep['checked_out_at'] : null,
                'created_by' => $creatorId
            ]);
            $safeConductId = $this->db->insert_id();
            // update safe conduct link
            $this->workOrder->updateWorkOrder([
                'id_safe_conduct' => $safeConductId,
            ], $workOrderId);

            // copy result of job to safe conduct
            $containers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderId, true);
            foreach ($containers as $container) {
                $this->safeConductContainer->createSafeConductContainer([
                    'id_safe_conduct' => $safeConductId,
                    'id_booking_reference' => if_empty($container['id_booking_reference'], if_empty($workOrder['id_booking_in'], null)),
                    'id_container' => $container['id_container'],
                    'id_position' => $container['id_position'],
                    'seal' => $container['seal'],
                    'is_empty' => $container['is_empty'],
                    'is_hold' => $container['is_hold'],
                    'status' => $container['status'],
                    'status_danger' => $container['status_danger'],
                    'description' => $container['description'],
                    'quantity' => 1
                ]);
                $safeConductContainerId = $this->db->insert_id();
                $containerGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($container['id']);
                foreach ($containerGoods as $item) {
                    $this->safeConductGoods->createSafeConductGoods([
                        'id_safe_conduct' => $safeConductId,
                        'id_booking_reference' => if_empty($item['id_booking_reference'], if_empty($workOrder['id_booking_in'], null)),
                        'id_safe_conduct_container' => $safeConductContainerId,
                        'id_goods' => $item['id_goods'],
                        'id_unit' => $item['id_unit'],
                        'id_position' => $item['id_position'],
                        'no_pallet' => $item['no_pallet'],
                        'quantity' => $item['quantity'],
                        'unit_weight' => $item['unit_weight'],
                        'unit_gross_weight' => $item['unit_gross_weight'],
                        'unit_length' => $item['unit_length'],
                        'unit_width' => $item['unit_width'],
                        'unit_height' => $item['unit_height'],
                        'unit_volume' => $item['unit_volume'],
                        'is_hold' => $item['is_hold'],
                        'status' => $item['status'],
                        'status_danger' => $item['status_danger'],
                        'ex_no_container' => $item['ex_no_container'],
                        'description' => $item['description']
                    ]);
                }

                $this->handlingContainer->createHandlingContainer([
                    'id_handling' => $handlingId,
                    'id_booking_reference' => if_empty($container['id_booking_reference'], if_empty($workOrder['id_booking_in'], null)),
                    'id_owner' => $customerId,
                    'id_container' => $container['id_container'],
                    'id_position' => $container['id_position'],
                    'quantity' => 1,
                    'seal' => $container['seal'],
                    'is_empty' => $container['is_empty'],
                    'is_hold' => $container['is_hold'],
                    'status' => $container['status'],
                    'status_danger' => $container['status_danger'],
                    'description' => $container['description'],
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);

            }

            $goods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderId, true, true);
            foreach ($goods as $item) {
                $this->safeConductGoods->createSafeConductGoods([
                    'id_safe_conduct' => $safeConductId,
                    'id_booking_reference' => if_empty($item['id_booking_reference'], if_empty($workOrder['id_booking_in'], null)),
                    'id_goods' => $item['id_goods'],
                    'id_unit' => $item['id_unit'],
                    'id_position' => $item['id_position'],
                    'no_pallet' => $item['no_pallet'],
                    'quantity' => $item['quantity'],
                    'unit_weight' => $item['unit_weight'],
                    'unit_gross_weight' => $item['unit_gross_weight'],
                    'unit_length' => $item['unit_length'],
                    'unit_width' => $item['unit_width'],
                    'unit_height' => $item['unit_height'],
                    'unit_volume' => $item['unit_volume'],
                    'is_hold' => $item['is_hold'],
                    'status' => $item['status'],
                    'status_danger' => $item['status_danger'],
                    'ex_no_container' => $item['ex_no_container'],
                    'description' => $item['description']
                ]);

                $this->handlingGoods->createHandlingGoods([
                    'id_handling' => $handlingId,
                    'id_booking_reference' => if_empty($item['id_booking_reference'], if_empty($workOrder['id_booking_in'], null)),
                    'id_owner' => $customerId,
                    'id_goods' => $item['id_goods'],
                    'id_unit' => $item['id_unit'],
                    'id_position' => $item['id_position'],
                    'quantity' => $item['quantity'],
                    'unit_weight' => $item['unit_weight'],
                    'unit_gross_weight' => $item['unit_gross_weight'],
                    'unit_length' => $item['unit_length'],
                    'unit_width' => $item['unit_width'],
                    'unit_height' => $item['unit_height'],
                    'unit_volume' => $item['unit_volume'],
                    'no_pallet' => $item['no_pallet'],
                    'is_hold' => $item['is_hold'],
                    'status' => $item['status'],
                    'status_danger' => $item['status_danger'],
                    'ex_no_container' => $item['ex_no_container'],
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);
            }

            if(isset($getTep)){
                $tepChecklist = $this->TransporterEntryPermitChecklist->getTepChecklistByTepId($tep);

                if(!empty($tepChecklist)){
                    foreach ($tepChecklist as $Checklist) {
                        if(empty($Checklist['id_container']) && $Checklist['type'] == "CHECK IN"){
                            $tepChecklistDetails = $this->TransporterEntryPermitChecklistDetail->getTepChecklistDetailByChecklistId($Checklist['id']);

                            // safe conduct checklist
                            $this->safeConductChecklist->create([
                                'id_safe_conduct' => $safeConductId,
                                'id_container' => null, //id_safe_conduct_container
                                'type' => $Checklist['type'],
                                'attachment' => $Checklist['attachment'],
                            ]);

                            //safe conduct detail
                            $safeConductChecklistId = $this->db->insert_id();
                            if(!empty($tepChecklistDetails)){
                                foreach($tepChecklistDetails as $ChecklistDetail){
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

                // Check if safe conduct needs to be grouped,
                // find out the existing data is already grouped
                $safeConductSameVehicles = $this->safeConduct->getBy([
                    'safe_conducts.id_transporter_entry_permit' => $tep,
                ]);
                if (count($safeConductSameVehicles) > 1) {
                    $safeConductGroupId = $safeConductSameVehicles[0]['id_safe_conduct_group']; // pick first data
                    if (empty($safeConductGroupId)) {
                        $this->safeConductGroup->create([
                            'id_branch' => get_active_branch_id(),
                            'no_safe_conduct_group' => $this->safeConductGroup->getAutoNumber()
                        ]);
                        $safeConductGroupId = $this->db->insert_id();
                    }
                    foreach ($safeConductSameVehicles as $relatedSafeConduct) {
                        $this->safeConduct->updateSafeConduct([
                            'id_safe_conduct_group' => $safeConductGroupId,
                        ], $relatedSafeConduct['id']);
                    }
                }
            }

            $safeConduct = $this->safeConduct->getSafeConductById($safeConductId);
            $handling = $this->handling->getHandlingById($safeConduct['id_handling']);
            $handlingContainers = $this->handlingContainer->getHandlingContainersByHandling($safeConduct['id_handling']);
            $handlingGoods = $this->handlingGoods->getHandlingGoodsByHandling($safeConduct['id_handling'], true);

            $mode = '';
            if (!empty($handlingContainers)) {
                $mode = 'C';
            }
            if (!empty($handlingGoods)) {
                $mode = 'G';
            }

            if (!empty($handlingContainers) && !empty($handlingGoods)) {
                $mode = 'CG';
            }

            $noWorkOrder = $this->workOrder->getAutoNumberWorkOrder($safeConduct['handling_code']);
            $this->workOrder->createWorkOrder([
                'no_work_order' => $noWorkOrder,
                'queue' => 0,
                'id_handling' => $safeConduct['id_handling'],
                'id_safe_conduct' => $safeConduct['id'],
                'status' => 'COMPLETED',
                'taken_by' => UserModel::authenticatedUserData('id'),
                'taken_at' => date('Y-m-d H:i:s'),
                'completed_at' => date('Y-m-d H:i:s'),
                'gate_in_date' => date('Y-m-d H:i:s'),
                'gate_out_date' => date('Y-m-d H:i:s'),
                'description' => 'auto create safeconduct',
                'mode' => $mode,
                'created_by' => UserModel::authenticatedUserData('id')
            ]);
            $workOrderId = $this->db->insert_id();

            $components = $this->component->getByHandlingType($handling['id_handling_type']);
            foreach ($components as $component) {
                $this->workOrderComponent->createWorkOrderComponent([
                    'id_work_order' => $workOrderId,
                    'id_component' => $component['id'],
                    'quantity' => $component['default_value'],
                    'description' => $component['component_description'],
                    'status' => 'APPROVED'
                ]);
            }

            foreach ($handlingContainers as $handlingContainer) {
                $this->workOrderContainer->insertWorkOrderContainer([
                    'id_work_order' => $workOrderId,
                    'id_booking_reference' => if_empty($handlingContainer['id_booking_reference'], if_empty($workOrder['id_booking_in'], null)),
                    'id_owner' => $handlingContainer['id_owner'],
                    'id_container' => $handlingContainer['id_container'],
                    'id_position' => if_empty($handlingContainer['id_position'], 0),
                    'description' => $handlingContainer['description'],
                    'seal' => $handlingContainer['seal'],
                    'is_empty' => $handlingContainer['is_empty'],
                    'status' => $handlingContainer['status'],
                    'quantity' => $handlingContainer['quantity'],
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);
            }

            foreach ($handlingGoods as $handlingItem) {
                $this->workOrderGoods->insertWorkOrderGoods([
                    'id_work_order' => $workOrderId,
                    'id_booking_reference' => if_empty($handlingItem['id_booking_reference'], if_empty($workOrder['id_booking_in'], null)),
                    'id_owner' => $handlingItem['id_owner'],
                    'id_goods' => $handlingItem['id_goods'],
                    'id_unit' => $handlingItem['id_unit'],
                    'id_position' => if_empty($handlingItem['id_position'], 0),
                    'quantity' => $handlingItem['quantity'],
                    'unit_weight' => $handlingItem['unit_weight'],
                    'unit_gross_weight' => $handlingItem['unit_gross_weight'],
                    'unit_length' => $handlingItem['unit_length'],
                    'unit_width' => $handlingItem['unit_width'],
                    'unit_height' => $handlingItem['unit_height'],
                    'unit_volume' => $handlingItem['unit_volume'],
                    'ex_no_container' => $handlingItem['ex_no_container'],
                    'status' => $handlingItem['status'],
                    'no_pallet' => $handlingItem['no_pallet'],
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);
            }
        }

        // TODO: update safe conduct group
        // for INTERNAL trucking, try to find the group if exist by no checked in safe conduct with same no plat police
        /*
        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
        if (in_array($workOrder['handling_type'], ['LOAD', 'EMPTY CONTAINER']) && !empty($workOrder['id_vehicle'])) {
            // This guess might result incorrect data
            $relatedSafeConduct = $this->safeConduct->getBy([
                'safe_conducts.id!=' => $workOrder['id_safe_conduct'],
                'safe_conducts.no_police' => $workOrder['no_plate_take'],
                'safe_conducts.security_in_date IS NULL' => null,
            ], true);

            // but I don't give a fuck, this is weird solution! unpredictable flow, technical debt in a view years later!
            if (!empty($relatedSafeConduct) && !empty($workOrder['id_safe_conduct'])) {
                $safeConductGroupId = if_empty($relatedSafeConduct['id_safe_conduct_group'], null);
                // create group if current group does not exist
                if (empty($safeConductGroupId)) {
                    $this->safeConductGroup->create([
                        'id_branch' => get_active_branch_id(),
                        'no_safe_conduct_group' => $this->safeConductGroup->getAutoNumber()
                    ]);
                    $safeConductGroupId = $this->db->insert_id();
                }

                // update related safe conduct
                $this->safeConduct->updateSafeConduct([
                    'id_safe_conduct_group' => $safeConductGroupId
                ], $relatedSafeConduct['id']);

                // update current safe conduct related the work order
                $this->safeConduct->updateSafeConduct([
                    'id_safe_conduct_group' => $safeConductGroupId
                ], $workOrder['id_safe_conduct']);
            }
        }
        */

        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            flash('success', "Job was <strong>{$workOrderNo}</strong> approved by <strong>". strtoupper(UserModel::authenticatedUserData('name'))."</strong>");
        } else {
            flash('danger', "Approved job <strong>{$workOrderNo}</strong> failed");
        }
        redirect('tally');
    }
    /**
     * Approve job tally.
     */
    public function checked_job()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_TAKE_JOB);
        $workOrderId = $this->input->post('id');
        $workOrderNo = $this->input->post('no');
        $description = $this->input->post('message');
        $checkPassed = true;
        $message = '';
        $redirect = site_url().'tally';

        $goods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderId, true, true);
        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
        $bookingTypes = $this->bookingType->getBookingTypeById($workOrder['id_booking_type']);
        $containers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderId, true);
        if (empty($containers)) {
            $containers = $this->handlingContainer->getHandlingContainersByHandling($workOrder['id_handling']);
        }
        $containerGoods = $this->handlingGoods->getHandlingGoodsByHandling($workOrder['id_handling'], true);
        
        //watermark
        $text1 = $workOrder['handling_type']." ".$bookingTypes['default_document']." AJU ".substr($workOrder['no_reference'],-4)."\n";
        if (isset($containers[0]['no_container'])) {
            $text2 = $containers[0]['no_container'];
        } else if (isset($containerGoods[0]['ex_no_container'])) {
            $text2 = 'EX ' . $containerGoods[0]['ex_no_container'];
        } else {
            $text2 = 'NO EX CONTAINER NUMBER';
        }
        $text3 = $workOrder['customer_name'];
        $text4 = "CHECKED BY ".strtoupper(UserModel::authenticatedUserData('username'));
        $no_police = '';

        if ($workOrder['id_transporter_entry_permit']!= null) {
            $tep = $this->transporterEntryPermit->getByIdNonBase($workOrder['id_transporter_entry_permit']);
            $no_police = $tep[0]['receiver_no_police'];
        }
        if ($workOrder['id_vehicle']!= null) {
            $tep = $this->vehicle->getBy(['ref_vehicles.id'=>$workOrder['id_vehicle']]);
            $no_police = $tep[0]['no_plate'];
        }
        // unknown tep
        if (empty($workOrder['id_transporter_entry_permit']) && empty($workOrder['id_vehicle'])) {
            $no_police = '';
        }

        if ($text2=='NO EX CONTAINER NUMBER') {
            $hasilText = date('d M Y H:i:s')."\n".$text1.$no_police."\n".$text3."\n".$text4;
        } else {
            if ($no_police=='') {
                $hasilText = date('d M Y H:i:s')."\n".$text1.$text2.$no_police."\n".$text3."\n".$text4;
            }else{
                $hasilText = date('d M Y H:i:s')."\n".$text1.$text2." | ".$no_police."\n".$text3."\n".$text4;
            }
        }
        //
        $uploadedPhoto = "";
        $photo_names = $this->input->post('photo_name');
        $id_customer = $this->input->post('id_customer');

        $people = $this->people->getById($id_customer);
        $whatsapp_group = $people['whatsapp_group'];

        $branch = get_active_branch();
        $whatsapp_group_internal = $branch['whatsapp_group'];
        if ($photo_names[0] != null) {
            $i = 0;
            $this->db->trans_start();
            foreach ($photo_names as $photo_name) {
                $files = $this->input->post('attachments_' . $i . '_name');
                if (empty($files)) {
                    // flash('danger', "Checked Job {$workOrder['no_work_order']} failed, You haven't uploaded a photo yet");
                    // redirect('work-order/view-check/'.$workOrderId);
                    $checkPassed = false;
                    $message = "Checked Job {$workOrder['no_work_order']} failed, You haven't uploaded a photo yet";
                    $redirect = site_url().'work-order/view-check/'.$workOrderId;
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => $checkPassed,
                        'message' => $message,
                        'redirect' => $redirect,
                    ]);
                    exit();
                }
                $uploadedPhoto = 'temp/' . $files[0];
                $photo_name = $photo_names[$i];
                $text5 = strtoupper($photo_name);
                $tempText = $hasilText;
                $tempText .= "\n" . $text5;
                $this->watermark($uploadedPhoto, $photo_name, $tempText);
                $uploadedPhoto = 'temp/' . $files[0];
                $destFile = 'tally_complete/' . date('Y/m') . '/' . $files[0];

                $status = $this->uploader->setDriver('s3')->move($uploadedPhoto, $destFile);
                if (!$status) {
                    // flash('danger', "Checked Job {$workOrder['no_work_order']} failed, uplod server fail");
                    // redirect('work-order/view-check/'.$workOrderId);
                    $checkPassed = false;
                    $message = "Checked Job {$workOrder['no_work_order']} failed, uplod server fail";
                    $redirect = site_url().'work-order/view-check/'.$workOrderId;
                    header('Content-Type: application/json');
                    echo json_encode([
                        'status' => $checkPassed,
                        'message' => $message,
                        'redirect' => $redirect,
                    ]);
                    exit();
                }
                $path = 'tally_complete/' . date('Y/m') . '/' . $files[0];
                $this->workOrderPhoto->createWorkOrderPhoto([
                    'id_work_order' => $this->input->post('id'),
                    'photo' => $path,
                    'description' => $photo_name,
                    'created_by' => UserModel::authenticatedUserData('id'),
                ]);
                $i++;
            }
            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                $i = 0;
                foreach ($photo_names as $photo_name) {
                    $files = $this->input->post('attachments_' . $i . '_name');
                    $path = 'tally_complete/' . date('Y/m') . '/' . $files[0];
                    $this->send_message($path, null, $whatsapp_group_internal);
                    $i++;
                }
            }else {
                // flash('danger', "Checked Job {$workOrder['no_work_order']} failed, because upload photo fail");
                // redirect('work-order/view-check/'.$workOrderId);
                $checkPassed = false;
                $message = "Checked Job {$workOrder['no_work_order']} failed, because upload photo fail";
                $redirect = site_url().'work-order/view-check/'.$workOrderId;
                header('Content-Type: application/json');
                echo json_encode([
                    'status' => $checkPassed,
                    'message' => $message,
                    'redirect' => $redirect,
                ]);
                exit();
            }
        }else{//if didnt upload photo
            $checkPassed = false;
            $message = "Checked Job {$workOrder['no_work_order']} failed, because you didnt upload photo";
            $redirect = site_url().'work-order/view-check/'.$workOrderId;
            header('Content-Type: application/json');
            echo json_encode([
                'status' => $checkPassed,
                'message' => $message,
                'redirect' => $redirect,
            ]);
            exit();
        }
        
        $this->db->trans_start();
        $remaining=0;
        foreach ($goods as $item) {
            $remaining = $this->input->post('remain_'.$item['id']);
            $this->workOrderGoods->updateWorkOrderGoods([
                'stock_remaining_tally' => $remaining,
            ],$item['id']);
        }
        $this->workOrder->updateWorkOrder([
            'updated_by' => UserModel::authenticatedUserData('id'),
            'updated_at' => date('Y-m-d H:i:s'),
            'status_validation' => WorkOrderModel::STATUS_VALIDATION_CHECKED,
        ], $workOrderId);

        if ($workOrder['status_validation']== WorkOrderModel::STATUS_VALIDATION_PENDING || $workOrder['status_validation']== WorkOrderModel::STATUS_VALIDATION_HANDOVER_COMPLETED) {
            $this->statusHistory->create([
                'id_reference' => $workOrderId,
                'type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
                'status' => WorkOrderModel::STATUS_VALIDATION_CHECKED,
                'description' => $description
            ]);
        }

        //tally checked point +1
        $data = json_encode($workOrder);
        $id_employee = $this->employee->getBy(
            [
                'ref_employees.id_user'=>  UserModel::authenticatedUserData('id')
            ]
        ,true);
        $id_employee = $id_employee['id'];
        if($workOrder['id_branch']!=2 || $workOrder['id_customer']!=9151){
            $this->allowance->create([
                [
                    'id_employee' => $id_employee,
                    'id_component' => 1,
                    'date' => date('Y-m-d'),
                    'different_date' => null,
                    'data' => $data,
                    'point' => 1,
                    'description' => 'checked job',
                ],
                [
                    'id_employee' => $id_employee,
                    'id_component' => 20,
                    'date' => date('Y-m-d'),
                    'different_date' => null,
                    'data' => $data,
                    'point' => 1,
                    'description' => 'checked job',
                ]
            ]);
        }
        $this->db->trans_complete();
        if ($this->db->trans_status()) {
            flash('success', "Job <strong>{$workOrderNo}</strong> was checked by <strong>". strtoupper(UserModel::authenticatedUserData('name'))."</strong>");
            $checkPassed = true;
            $message = "Job <strong>{$workOrderNo}</strong> was checked by <strong>". strtoupper(UserModel::authenticatedUserData('name'))."</strong>";
            $redirect = site_url().'tally';
        } else {
            flash('danger', "Checked job <strong>{$workOrderNo}</strong> failed");
            $checkPassed = false;
            $message = "Checked job <strong>{$workOrderNo}</strong> failed";
            $redirect = site_url().'work-order/view-check/'.$workOrderId;
        }
        header('Content-Type: application/json');
        echo json_encode([
            'status' => $checkPassed,
            'message' => $message,
            'redirect' => $redirect,
        ]);
        // redirect('tally');
    }
    /**
     * Get stock remaining by no booking and goods.
     * @param $bookingId
     * @param $selectedGoods
     * @return int
     */
    public function getStockRemaining($bookingId,$selectedGoods,$exContainer)
    {
        $selectedContainers = -1;
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
            $inboundContainer = $this->report->getContainerStockMove([
                'booking' => $allBooking['id'],
                'multiplier' => 1,
                'containers' => $selectedContainers
            ]);
            $containerStocks = array_merge($containerStocks, $inboundContainer);

            $inboundGoods = $this->report->getGoodsStockMove([
                'booking' => $allBooking['id'],
                'multiplier' => 1,
                'items' => $selectedGoods
            ]);
            $goodsIns = array_merge($goodsIns, $inboundGoods);
        }
        foreach ($allBookings as $allBooking) {
            $outbounds = $this->report->getContainerStockMove([
                'booking' => $allBooking['id'],
                'multiplier' => -1,
                'containers' => $selectedContainers
            ]);
            $containerOuts = array_merge($containerOuts, $outbounds);

            $outboundGoods = $this->report->getGoodsStockMove([
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

        // add marker by ex container
        $goodsIns = array_map(function ($item) {
            $item['_group'] = $item['id_goods'] . '-' . if_empty($item['ex_no_container'], '');
            return $item;
        }, $goodsIns);

        $goodIds = array_unique(array_column($goodsIns, '_group'));
        $goodsStocks = [];
        foreach ($goodIds as $goodId) {
            $goodsId = explode('-', $goodId);
            $goodsData = $this->goods->getById($goodsId[0]);
            $goodsData['ex_no_container'] = get_if_exist($goodsId, 1, '');
            $goodsStocks[] = $goodsData;
        }

        foreach ($goodsStocks as &$item) {
            $item['inbounds'] = [];
            $item['outbounds'] = [];
            foreach ($goodsIns as $goods) {
                if ($item['id'] == $goods['id_goods'] && $item['ex_no_container'] == $goods['ex_no_container']) {
                    $item['inbounds'][] = $goods;
                }
            }
            foreach ($goodsOuts as $goods) {
                if ($item['id'] == $goods['id_goods'] && $item['ex_no_container'] == $goods['ex_no_container']) {
                    $item['outbounds'][] = $goods;
                }
            }
        }

        $sisaStock=0;
        if (!empty($goodsStocks)) {
            foreach ($goodsStocks as $goods) {
                $totalIn = 0; 
                $totalOut = 0;
                $rowIn = count($goods['inbounds']);
                $rowOut = count($goods['outbounds']);
                $totalRows = $rowIn > $rowOut ? $rowIn : $rowOut;
                if ($goods['ex_no_container']==$exContainer) {
                    for ($i = 0; $i < $totalRows; $i++) { 
                        if (key_exists($i, $goods['inbounds'])) {
                            $totalIn += $goods['inbounds'][$i]['quantity'];
                        }
                        if (key_exists($i, $goods['outbounds'])) {
                            $totalOut += $goods['outbounds'][$i]['quantity'];
                        }
                    }
                }
                $sisaStock+=numerical($totalIn - $totalOut, 3, true);
            }
        }
        return $sisaStock;
    }

    /**
     * Get stock remaining by no booking and goods.
     * @param $bookingId
     * @param $selectedGoods
     * @return int
     */
    public function getStockBookingOutRemaining($bookingId,$selectedGoods)
    {
        $selectedContainers = -1;
        $bookingOuts = $this->booking->getBookingById($bookingId);
        
        $bookingGoodsOuts = $this->bookingGoods->getBookingGoodsByBooking($bookingId, true);

        $containerStocks = [];
        $containerOuts = [];

        $goodsOuts = [];

        $allBooking =  $bookingOuts;
        $outbounds = $this->report->getContainerStockMove([
            'booking' => $bookingId,
            'multiplier' => -1,
            'containers' => $selectedContainers
        ]);
        $containerOuts = array_merge($containerOuts, $outbounds);

        $outboundGoods = $this->report->getGoodsStockMove([
            'booking' => $bookingId,
            'multiplier' => -1,
            'items' => $selectedGoods
        ]);
        $goodsOuts = array_merge($goodsOuts, $outboundGoods);
        
        $quantity_out=0;
        foreach ($goodsOuts as $goods) {
            $quantity_out += $goods['quantity'];
        }
        $hasil=0;
        foreach ($bookingGoodsOuts as $key) {
            if ($key['id_goods']==$selectedGoods) {
                $hasil= $key['quantity']-$quantity_out;
            }
        }
        return numerical($hasil, 3, true);
    }

    public function ajax_get_used_space(){
        
        $id_customer = $this->input->post('id_customer');
        $peopleSpace = $this->people->getById($id_customer);
        $peopleSpace = $peopleSpace['contract'];
        //cek space
        $usedSpace = $this->workOrder->getUsedSpace($id_customer);
        $usedSpace = $usedSpace['total_space'];
        $sisaSpace = $peopleSpace + $usedSpace;
        header('Content-Type: application/json');
        echo json_encode($sisaSpace);
    }

    public function ajax_get_used_pallet(){
        
        $branchPallet = get_active_branch('stock_pallet');
        $initialPallet = get_active_branch('initial_pallet');
        //cek space
        $usedPallet = $this->workOrder->getUsedPallet();
        $usedPallet = $usedPallet['total_pallet'];
        $sisaPallet = $branchPallet + $usedPallet - ($branchPallet - $initialPallet);
        header('Content-Type: application/json');
        echo json_encode($sisaPallet);
    }


    /**
     * Over space reminder for dedicated customers.
     *
     * @param $customerId
     * @param bool $notifyManagementOnly
     */
    private function over_space_reminder($customerId, $notifyManagementOnly = false)
    {
        $this->load->model('notifications/OverSpaceCustomerNotification');
        $managementGroup = get_setting('whatsapp_group_management');
        $customers = $this->people->getCustomersByActiveStorage(['customer' => $customerId]);
        foreach ($customers as $customer) {
            $customer = $this->customerStorageCapacity->getCustomerStorageSummary($customer);
            $warehouseUsagePercent = $customer['warehouse_storages']['total_used_percent'];
            $yardUsagePercent = $customer['yard_storages']['total_used_percent'];
            $coveredUsagePercent = $customer['covered_yard_storages']['total_used_percent'];

            if ($warehouseUsagePercent >= 75 || $yardUsagePercent >= 75 || $coveredUsagePercent >= 75) {
                // send to customer
                $customerBranch = $this->people->getPeopleByIdCustomerIdBranch($customer['id'], $customer['id_branch']);
                if (!empty($customerBranch['whatsapp_group']) && !$notifyManagementOnly) {
                    $customer['whatsapp_group'] = $customerBranch['whatsapp_group'];
                    $this->notification
                        ->via([Notify::CHAT_PUSH])
                        ->to($customer['whatsapp_group'])
                        ->send(new OverSpaceCustomerNotification($customer));
                    echo "Sent over space notification customer " . $customer['name'] . PHP_EOL;
                }

                // send to management
                if (!empty($managementGroup)) {
                    $this->notification
                        ->via([Notify::CHAT_PUSH])
                        ->to($managementGroup)
                        ->send(new OverSpaceCustomerNotification($customer));
                    echo "Sent over space notification management" . PHP_EOL;
                }
            }
        }
    }
}
