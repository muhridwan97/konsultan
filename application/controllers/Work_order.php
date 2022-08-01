<?php

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Work_order
 * @property BookingModel $booking
 * @property BookingGoodsModel $bookingGoods
 * @property BookingContainerModel $bookingContainer
 * @property InvoiceModel $invoice
 * @property InvoiceHandlingModel $invoiceHandling
 * @property InvoiceWorkOrderModel $invoiceWorkOrder
 * @property InvoiceDetailModel $invoiceDetail
 * @property WorkOrderModel $workOrder
 * @property WorkOrderContainerModel $workOrderContainer
 * @property WorkOrderGoodsModel $workOrderGoods
 * @property WorkOrderComponentModel $workOrderComponent
 * @property WorkOrderHistoryModel $workOrderHistory
 * @property WorkOrderContainerHistoryModel $workOrderContainerHistory
 * @property WorkOrderGoodsHistoryModel $workOrderGoodsHistory
 * @property HandlingModel $handling
 * @property HandlingContainerModel $handlingContainer
 * @property HandlingGoodsModel $handlingGoods
 * @property HandlingComponentModel $handlingComponent
 * @property HandlingTypeModel $handlingType
 * @property PalletMarkingHistoryModel $palletMarkingHistory
 * @property DocumentTypeModel $documentType
 * @property UploadDocumentFileModel $uploadDocumentFile
 * @property SafeConductModel $safeConduct
 * @property SafeConductContainerModel $safeConductContainer
 * @property SafeConductGoodsModel $safeConductGoods
 * @property PeopleModel $people
 * @property ReportModel $report
 * @property ReportStockModel $reportStock
 * @property WorkOrderHistoryLockModel $workOrderHistoryLock
 * @property WorkOrderLockAutomateModel $workOrderLockAutomate
 * @property GoodsModel $goods
 * @property UserModel $userModel
 * @property ComponentModel $component
 * @property GuestModel $guest
 * @property HeavyEquipmentModel $heavyEquipment
 * @property WorkOrderComponentLaboursModel $workOrderComponentLabours
 * @property WorkOrderComponentHeavyEquipmentsModel $workOrderComponentHeavyEquipments
 * @property HeavyEquipmentEntryPermitModel $heavyEquipmentEntryPermit
 * @property WorkOrderComponentHeepsModel $workOrderComponentHeeps
 * @property WorkOrderUnlockHandheldModel $workOrderUnlockHandheld
 * @property TransporterEntryPermitModel $transporterEntryPermit
 * @property TransporterEntryPermitRequestHoldItemModel $transporterEntryPermitHoldItem
 * @property TransporterEntryPermitChassisModel $transporterEntryPermitChassis
 * @property Mailer $mailer
 * @property Exporter $exporter
 */
class Work_order extends CI_Controller
{
    /**
     * Work_order constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingTypeModel', 'bookingType');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('InvoiceModel', 'invoice');
        $this->load->model('InvoiceHandlingModel', 'invoiceHandling');
        $this->load->model('InvoiceWorkOrderModel', 'invoiceWorkOrder');
        $this->load->model('InvoiceDetailModel', 'invoiceDetail');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('WorkOrderContainerModel', 'workOrderContainer');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('WorkOrderComponentModel', 'workOrderComponent');
        $this->load->model('WorkOrderHistoryModel', 'workOrderHistory');
        $this->load->model('WorkOrderContainerHistoryModel', 'workOrderContainerHistory');
        $this->load->model('WorkOrderGoodsHistoryModel', 'workOrderGoodsHistory');
        $this->load->model('WorkOrderPhotoModel', 'workOrderPhoto');
        $this->load->model('HandlingModel', 'handling');
        $this->load->model('HandlingContainerModel', 'handlingContainer');
        $this->load->model('HandlingGoodsModel', 'handlingGoods');
        $this->load->model('HandlingTypeModel', 'handlingType');
        $this->load->model('HandlingComponentModel', 'handlingComponent');
        $this->load->model('HolidayModel', 'holiday');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
        $this->load->model('PalletMarkingHistoryModel', 'palletMarkingHistory');
        $this->load->model('SafeConductModel', 'safeConduct');
        $this->load->model('SafeConductContainerModel', 'safeConductContainer');
        $this->load->model('SafeConductGoodsModel', 'safeConductGoods');
        $this->load->model('ReportModel', 'report');
        $this->load->model('ReportStockModel', 'reportStock');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('WorkOrderHistoryLockModel', 'workOrderHistoryLock');
        $this->load->model('WorkOrderLockAutomateModel', 'workOrderLockAutomate');
        $this->load->model('GoodsModel', 'goods');
        $this->load->model('UserModel', 'userModel');
        $this->load->model('ComponentModel', 'component');
        $this->load->model('GuestModel', 'guest');
        $this->load->model('HeavyEquipmentModel', 'heavyEquipment');
        $this->load->model('WorkOrderComponentLaboursModel', 'workOrderComponentLabours');
        $this->load->model('HeavyEquipmentEntryPermitModel', 'heavyEquipmentEntryPermit');
        $this->load->model('WorkOrderComponentHeavyEquipmentsModel', 'workOrderComponentHeavyEquipments');
        $this->load->model('WorkOrderComponentHeepsModel', 'workOrderComponentHeeps');
        $this->load->model('WorkOrderUnlockHandheldModel', 'workOrderUnlockHandheld');
        $this->load->model('TransporterEntryPermitRequestHoldItemModel', 'transporterEntryPermitHoldItem');
        $this->load->model('TransporterEntryPermitModel', 'transporterEntryPermit');
        $this->load->model('TransporterEntryPermitChassisModel', 'transporterEntryPermitChassis');
    }

    /**
     * Show index data work order.
     */
    public function index()
    {
        $historyJobFilter = get_url_param('filter_tally_history') ? $_GET : [];

        if (get_url_param('export')) {
            $this->load->model('modules/Exporter', 'exporter');
            $this->exporter->exportLargeResourceFromArray("Work orders", $this->workOrder->getAllWorkOrders($historyJobFilter));
        } else {
            $selectedCustomers = key_exists('customer', $historyJobFilter) ? $historyJobFilter['customer'] : [0];
            $selectedHandlingTypes = key_exists('handling_type', $historyJobFilter) ? $historyJobFilter['handling_type'] : [0];
            $selectedPeoples = key_exists('people', $historyJobFilter) ? $historyJobFilter['people'] : [0];

            $customers = $this->people->getById($selectedCustomers);
            $handlingTypes = $this->handlingType->getHandlingTypeById($selectedHandlingTypes);
            $peoples = $this->people->getById($selectedPeoples);

            $data = [
                'title' => "Tally",
                'subtitle' => "History of job",
                'page' => "workorder/index",
                'customers' => $customers,
                'handlingTypes' => $handlingTypes,
                'peoples' => $peoples,
            ];

            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable.
     */
    public function data()
    {
        $filters = array_merge(get_url_param('filter_tally_history') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);
        $workOrders = $this->workOrder->getAllWorkOrders($filters);

        foreach ($workOrders['data'] as &$row) {
            $complete_date = $row['completed_at'];
            if(!empty($complete_date) && $row['multiplier_goods'] == 1){
                $endDate = date('Y-m-d', strtotime('+3 day', strtotime($complete_date)));
                $offDate = $this->holiday->getBy(['date >' => date('Y-m-d', strtotime($complete_date)), 'date <=' => $endDate]);

                $row['total_offday'] = 1;
                if(empty($offDate)){
                    if(date('l', strtotime('+1 day', strtotime($complete_date))) != "Sunday"){
                        $row['total_offday'] = 1;
                    }else{
                        $row['total_offday'] = 2;
                    }
                }else{
                    for($start = 1; $start <= 3; $start++){
                        $workDay = date('Y-m-d', strtotime('+'.$start.' day', strtotime($complete_date)));
                        if(in_array($workDay, $offDate) == false && date('l', strtotime($workDay)) != "Sunday"){
                            $row['total_offday'] = $start;
                            break;
                        }
                    }
                }
            }else{
                $row['total_offday'] = 0;
            }
        }

        header('Content-Type: application/json');
        echo json_encode($workOrders);
    }

    /**
     * View detail work order.
     *
     * @param $workOrderId
     */
    public function view($workOrderId)
    {
        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
        $workOrderHistories = $this->workOrderHistory->getBy(['id_work_order' => $workOrderId]);
        $workOrderPhotos = $this->workOrderPhoto->getWorkOrderPhotoById($workOrderId);
        $components = $this->workOrderComponent->getWorkOrderComponentsByWorkOrder($workOrderId);
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
            $item['parent_goods_name'] = '';
            $item['parent_no_goods'] = '';
            if (!empty($item['id_goods_parent'])) {
                $goodsParent = $this->goods->getById($item['id_goods_parent']);
                if (!empty($goodsParent)) {
                    $item['parent_goods_name'] = $goodsParent['name'];
                    $item['parent_no_goods'] = $goodsParent['no_goods'];
                    $item['unpackage_quantity'] = 0;

                    $unpackage = $this->workOrderGoods->getWorkOrderUnpackageQuantity($item['id_goods_parent']);
                    if (!empty($unpackage)) {
                       $item['unpackage_quantity'] = $unpackage['total'];
                    }
                }
            }
        }

        $workOrderStatuses = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
            'status_histories.id_reference' => $workOrderId
        ]);
        foreach ($workOrderStatuses as &$workOrderStatus) {
            $workOrderStatus['goods']=$goods;
        }
        // print_debug($workOrderStatuses);
        $lockHistories=$this->workOrderHistoryLock->getHistoryLockByWorkOrderId($workOrderId);
        
        $components2 = $this->component->getByHandlingType($workOrder['id_handling_type'], $workOrder['id_handling']);
        $activity_types= [];
        $resources_types= [];
        $component_labour_id= 0;
        $component_forklift_id= 0;
        $data_forklift = [];
        foreach ($components2 as $component) {
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
            if(empty($data)){
                $data_forklift['operator_name']='';
                $data_forklift['is_owned']='';
                $data_forklift['capacity']='';
            }else{
                $data_forklift['operator_name'] = $data['operator_name'];
                $data_forklift['is_owned'] = $data['is_owned'];
                $data_forklift['capacity'] = $data['capacity'];
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

        $tep = $this->transporterEntryPermit->getById($workOrder['id_transporter_entry_permit']);
        $tepChassis = $this->transporterEntryPermitChassis->getById($workOrder['id_tep_chassis']);

        $data = [
            'title' => "Tally",
            'subtitle' => "Data job order",
            'page' => "workorder/view",
            'workOrder' => $workOrder,
            'containers' => $containers,
            'goods' => $goods,
            'components' => $components,
            'workOrderHistories' => $workOrderHistories,
            'workOrderPhotos' => $workOrderPhotos,
            'workOrderStatuses' => $workOrderStatuses,
            'lockHistories' => $lockHistories,
            'component_data' => $component_data,
            'tep' => $tep,
            'chassis' => $tepChassis,
        ];

        $this->load->view('template/layout', $data);
    }
    /**
     * View detail work order.
     *
     * @param $workOrderId
     */
    public function view_check($workOrderId)
    {
        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
        $workOrderHistories = $this->workOrderHistory->getBy(['id_work_order' => $workOrderId]);
        $workOrderPhotos = $this->workOrderPhoto->getWorkOrderPhotoById($workOrderId);
        $components = $this->workOrderComponent->getWorkOrderComponentsByWorkOrder($workOrderId);
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
            if ($goodsItem) {
                // $goodsItem['stock_remaining'] = $this->getStockRemaining($workOrder['id_booking_in']==null?$workOrder['id_booking']:$workOrder['id_booking_in'], $goodsItem['id_goods'],$item['ex_no_container']);
                $max_quantity = $this->reportStock->getStockGoodsRemaining(['data' => 'all', 'owner' => $workOrder['id_customer']],$item['id_goods'],$item['id_unit'],$item['ex_no_container']);
                $goodsItem['stock_remaining']= $max_quantity['stock_quantity'];
            }
            $item['goods'] = $goodsItem;
            // $item['stock_remaining'] = $this->getStockRemaining($workOrder['id_booking_in']==null?$workOrder['id_booking']:$workOrder['id_booking_in'], $item['id_goods'],$item['ex_no_container']);
            $max_quantity = $this->reportStock->getStockGoodsRemaining(['data' => 'all', 'owner' => $workOrder['id_customer']],$item['id_goods'],$item['id_unit'],$item['ex_no_container']);
            $item['stock_remaining'] = $max_quantity['stock_quantity'];
            $item['goods'] = $goodsItem;
            $item['parent_goods_name'] = '';
            $item['parent_no_goods'] = '';
            if (!empty($item['id_goods_parent'])) {
                $goodsParent = $this->goods->getById($item['id_goods_parent']);
                if (!empty($goodsParent)) {
                    $item['parent_goods_name'] = $goodsParent['name'];
                    $item['parent_no_goods'] = $goodsParent['no_goods'];
                    $item['unpackage_quantity'] = 0;

                    $unpackage = $this->workOrderGoods->getWorkOrderUnpackageQuantity($item['id_goods_parent']);
                    if (!empty($unpackage)) {
                       $item['unpackage_quantity'] = $unpackage['total'];
                    }
                }
            }
        }
        $workOrderStatuses = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
            'status_histories.id_reference' => $workOrderId
        ]);
        foreach ($workOrderStatuses as &$workOrderStatus) {
            $workOrderStatus['goods']=$goods;
        }
        $lockHistories=$this->workOrderHistoryLock->getHistoryLockByWorkOrderId($workOrderId);

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
            'title' => "Tally",
            'subtitle' => "Data job order",
            'page' => "workorder/view_check",
            'workOrder' => $workOrder,
            'containers' => $containers,
            'goods' => $goods,
            'components' => $components,
            'workOrderPhotos' => $workOrderPhotos,
            'lockHistories' => $lockHistories,
            'workOrderHistories' => $workOrderHistories,
            'workOrderStatuses' => $workOrderStatuses,
            'check' => TRUE,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * View detail work order.
     *
     * @param $workOrderId
     */
    public function view_upload($workOrderId)
    {
        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
        $workOrderHistories = $this->workOrderHistory->getBy(['id_work_order' => $workOrderId]);
        $workOrderPhotos = $this->workOrderPhoto->getWorkOrderPhotoById($workOrderId);
        $components = $this->workOrderComponent->getWorkOrderComponentsByWorkOrder($workOrderId);
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
            if ($goodsItem) {
                // $goodsItem['stock_remaining'] = $this->getStockRemaining($workOrder['id_booking_in']==null?$workOrder['id_booking']:$workOrder['id_booking_in'], $goodsItem['id_goods'],$item['ex_no_container']);
                $max_quantity = $this->reportStock->getStockGoodsRemaining(['data' => 'all', 'owner' => $workOrder['id_customer']],$item['id_goods'],$item['id_unit'],$item['ex_no_container']);
                $goodsItem['stock_remaining']= $max_quantity['stock_quantity'];
            }
            $item['goods'] = $goodsItem;
            // $item['stock_remaining'] = $this->getStockRemaining($workOrder['id_booking_in']==null?$workOrder['id_booking']:$workOrder['id_booking_in'], $item['id_goods'],$item['ex_no_container']);
            $max_quantity = $this->reportStock->getStockGoodsRemaining(['data' => 'all', 'owner' => $workOrder['id_customer']],$item['id_goods'],$item['id_unit'],$item['ex_no_container']);
            $item['stock_remaining'] = $max_quantity['stock_quantity'];
            $item['goods'] = $goodsItem;
            $item['parent_goods_name'] = '';
            $item['parent_no_goods'] = '';
            if (!empty($item['id_goods_parent'])) {
                $goodsParent = $this->goods->getById($item['id_goods_parent']);
                if (!empty($goodsParent)) {
                    $item['parent_goods_name'] = $goodsParent['name'];
                    $item['parent_no_goods'] = $goodsParent['no_goods'];
                    $item['unpackage_quantity'] = 0;

                    $unpackage = $this->workOrderGoods->getWorkOrderUnpackageQuantity($item['id_goods_parent']);
                    if (!empty($unpackage)) {
                       $item['unpackage_quantity'] = $unpackage['total'];
                    }
                }
            }
        }
        $workOrderStatuses = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
            'status_histories.id_reference' => $workOrderId
        ]);
        foreach ($workOrderStatuses as &$workOrderStatus) {
            $workOrderStatus['goods']=$goods;
        }
        $lockHistories=$this->workOrderHistoryLock->getHistoryLockByWorkOrderId($workOrderId);

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
            'title' => "Tally",
            'subtitle' => "Data job order",
            'page' => "workorder/view_upload",
            'workOrder' => $workOrder,
            'containers' => $containers,
            'goods' => $goods,
            'components' => $components,
            'workOrderHistories' => $workOrderHistories,
            'workOrderPhotos' => $workOrderPhotos,
            'workOrderStatuses' => $workOrderStatuses,
            'lockHistories' => $lockHistories,
            'check' => TRUE,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * View history work order.
     *
     * @param $workOrderId
     */
    public function history($workOrderId)
    {
        $workOrder = $this->workOrderHistory->getWorkOrderById($workOrderId);
        $containers = $this->workOrderContainerHistory->getWorkOrderContainersByWorkOrder($workOrderId, true);
        foreach ($containers as &$container) {
            $containerGoods = $this->workOrderGoodsHistory->getWorkOrderGoodsByWorkOrderContainer($container['id']);
            $container['goods'] = $containerGoods;
        }
        $goods = $this->workOrderGoodsHistory->getWorkOrderGoodsByWorkOrder($workOrderId, true, true);

        $data = [
            'title' => "Tally",
            'subtitle' => "History job order",
            'page' => "workorder/view_history",
            'workOrder' => $workOrder,
            'containers' => $containers,
            'goods' => $goods,
        ];

        $this->load->view('template/layout', $data);
    }

    /**
     * Review work order data.
     *
     * @param $workOrderId
     */
    public function review_work_order($workOrderId)
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $message = $this->input->post('message');
            $workOrder = $this->workOrder->getWorkOrderById($workOrderId);

            $this->db->trans_start();

            $this->workOrder->updateWorkOrder([
                'status_validation' => WorkOrderModel::STATUS_VALIDATION_ON_REVIEW,
            ], $workOrderId);

            $this->statusHistory->create([
                'id_reference' => $workOrder['id'],
                'type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
                'status' => WorkOrderModel::STATUS_VALIDATION_ON_REVIEW,
                'description' => $message
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Work order <strong>{$workOrder['no_work_order']}</strong> successfully reviewed");
            } else {
                flash('danger', "Review work order <strong>{$workOrder['no_work_order']}</strong> failed");
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed, please wait until page loaded');
        }
        redirect('work-order');
    }

    /**
     * Reject work order data.
     *
     * @param $workOrderId
     */
    public function reject_work_order($workOrderId)
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $message = $this->input->post('message');
            $workOrder = $this->workOrder->getWorkOrderById($workOrderId);

            $this->db->trans_start();

            $this->workOrder->updateWorkOrder([
                'status_validation' => WorkOrderModel::STATUS_VALIDATION_REJECT,
            ], $workOrderId);

            $this->statusHistory->create([
                'id_reference' => $workOrder['id'],
                'type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
                'status' => WorkOrderModel::STATUS_VALIDATION_REJECT,
                'description' => $message
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Work order <strong>{$workOrder['no_work_order']}</strong> successfully rejected");
            } else {
                flash('danger', "Rejected work order <strong>{$workOrder['no_work_order']}</strong> failed");
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed, please wait until page loaded');
        }
        redirect('work-order');
    }

    /**
     * Fix work order data.
     *
     * @param $workOrderId
     */
    public function fix_work_order($workOrderId)
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $message = $this->input->post('message');
            $workOrder = $this->workOrder->getWorkOrderById($workOrderId);

            $this->db->trans_start();

            $this->workOrder->updateWorkOrder([
                'status_validation' => WorkOrderModel::STATUS_VALIDATION_FIXED,
            ], $workOrderId);

            $this->statusHistory->create([
                'id_reference' => $workOrder['id'],
                'type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
                'status' => WorkOrderModel::STATUS_VALIDATION_FIXED,
                'description' => $message
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Work order <strong>{$workOrder['no_work_order']}</strong> successfully fixed");
            } else {
                flash('danger', "Fixed work order <strong>{$workOrder['no_work_order']}</strong> failed");
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed, please wait until page loaded');
        }
        redirect('work-order');
    }

    /**
     * Validate work order data.
     *
     * @param $workOrderId
     */
    public function validate_work_order($workOrderId)
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $message = $this->input->post('message');

            $workOrder = $this->workOrder->getWorkOrderById($workOrderId);

            $this->db->trans_start();

            $this->workOrder->updateWorkOrder([
                'status_validation' => WorkOrderModel::STATUS_VALIDATION_VALIDATED,
            ], $workOrderId);

            $this->statusHistory->create([
                'id_reference' => $workOrder['id'],
                'type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
                'status' => WorkOrderModel::STATUS_VALIDATION_VALIDATED,
                'description' => $message
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Work order <strong>{$workOrder['no_work_order']}</strong> successfully validated");
            } else {
                flash('danger', "Validate work order <strong>{$workOrder['no_work_order']}</strong> failed");
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed, please wait until page loaded');
        }
        redirect('work-order');
    }

    /**
     * Upload attachment work order data
     * @param $workOrderId
     */
    public function upload_attachment($workOrderId)
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Work order data', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $workOrder = $this->workOrder->getWorkOrderById($workOrderId);

                $fileName = '';
                $uploadPassed = true;
                if (!empty($_FILES['attachment']['name'])) {
                    $fileName = 'WO_' . time() . '_' . rand(100, 999);
                    $saveTo = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'work_orders';
                    if ($this->documentType->makeFolder('work_orders')) {
                        $upload = $this->uploadDocumentFile->uploadTo('attachment', $fileName, $saveTo);
                        if (!$upload['status']) {
                            $uploadPassed = false;
                            flash('warning', $upload['errors']);
                        } else {
                            $fileName = $upload['data']['file_name'];
                        }
                    } else {
                        $uploadPassed = false;
                        flash('warning', 'Making folder upload failed, try again');
                    }
                }

                if ($uploadPassed) {
                    $update = $this->workOrder->updateWorkOrder([
                        'attachment' => $fileName
                    ], $workOrderId);

                    if ($update) {
                        flash('success', "Work order <strong>{$workOrder['no_work_order']}</strong> successfully attached with file");
                    } else {
                        flash('danger', "Upload attachment work order <strong>{$workOrder['no_work_order']}</strong> failed, try again or contact administrator");
                    }
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed, please wait until page loaded');
        }
        redirect('work_order');
    }

    /**
     * Set locked for pallet marking access.
     * @param $workOrderId
     */
    public function set_pallet_status()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {

            $workOrderId = $this->input->post('id');
            $pallet_status = $this->input->post('pallet_locked');
            $goods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderId);

            $this->db->trans_start();

            $this->workOrder->updateWorkOrder([
                'pallet_status' => $pallet_status,
            ], $workOrderId);

            $this->palletMarkingHistory->create([
                'id_reference' => $workOrderId,
                'description' => implode(',',array_column($goods, 'no_pallet')),
                'status' => $pallet_status,
                'created_by' => UserModel::authenticatedUserData('id'),
                'created_at' => date('Y-m-d H:i:s')
            ]);

            $this->db->trans_complete();
            $workOrder = $this->workOrder->getWorkOrderById($workOrderId);

            if ($this->db->trans_status()) {
                flash('success', "Job no <strong>{$workOrder['no_work_order']}</strong> successfully set status");
            } else {
                flash('danger', "Job no <strong>{$workOrder['no_work_order']}</strong> failed set status");
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed, please wait until page loaded');
        }
        redirect('work_order');
    }

    /**
     * Request to application support to update data.
     * @param $workOrderId
     */
    public function request_edit($workOrderId)
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Work order data', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $description = $this->input->post('description');
                $reason = $this->input->post('reason');

                $workOrder = $this->workOrder->getWorkOrderById($workOrderId);

                $emailSupport = get_setting('email_bug_report', 'team_it@transcon-indonesia.com');
                $adminSupport = get_setting('admin_developer', 'Team IT');

                $emailTo = $emailSupport;
                $emailTitle = 'Request update job ' . $workOrder['no_work_order'] . ' at ' . date('d F Y H:i');
                $emailTemplate = 'emails/basic';
                $emailData = [
                    'title' => 'Request Edit Job',
                    'name' => $adminSupport,
                    'email' => $emailSupport,
                    'content' => '<b>' . UserModel::authenticatedUserData('username') . '</b> ask request edit for job ' . $workOrder['no_work_order'] . '.<br><b>Description:</b> ' . $description . '<br><b>Reason:</b> ' . $reason . '<br>'
                ];
                $emailOptions = [
                    'cc' => UserModel::authenticatedUserData('email'),
                ];

                $send = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);

                if ($send) {
                    flash('success', "Job no <strong>{$workOrder['no_work_order']}</strong> successfully requested to be updated");
                } else {
                    flash('danger', "Job no <strong>{$workOrder['no_work_order']}</strong> failed send email");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed, please wait until page loaded');
        }
        redirect('work_order');
    }

    /**
     * Create work order data.
     * @param null $type
     */
    public function create($type = null)
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id_booking', 'Booking data', 'trim|max_length[50]');
            $this->form_validation->set_rules('id_handling', 'Handling data', 'trim|max_length[50]');
            $this->form_validation->set_rules('id_safe_conduct', 'Safe conduct data', 'trim|max_length[50]');
            $this->form_validation->set_rules('description', 'Description', 'trim|max_length[500]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $bookingId = $this->input->post('id_booking'); // required when booking is scanned in gate (create related handling)
                $handlingDate = $this->input->post('handling_date'); // required when booking is scanned in gate (create related handling)
                $safeConductId = $this->input->post('id_safe_conduct'); // required when safe conduct is scanned in gate (create related handling)
                $handlingId = $this->input->post('id_handling'); // required when handling is scanned in gate (fetch available handling)
                $containersData = $this->input->post('containers'); // available when create job from booking
                $goodsData = $this->input->post('goods'); // available when create job from booking
                $description = $this->input->post('description'); // additional desc when create job in gate
                $components = $this->input->post('components'); // handling component

                $this->db->trans_start();

                // create handling data first if Inbound or Outbound type is set (required either $bookingId or $safeConductId)
                if (!is_null($type)) {
                    if ($type == SafeConductModel::TYPE_INBOUND) {
                        $handlingTypeId = get_setting('default_inbound_handling');
                    } else {
                        $handlingTypeId = get_setting('default_outbound_handling');
                    }
                    $handlingType = $this->handlingType->getHandlingTypeById($handlingTypeId);
                    $handlingCode = $handlingType['handling_code'];
                    // create new handling (Load or Unload based by data scanned such SF or booking)
                    $noHandling = $this->handling->getAutoNumberHandlingRequest();
                    $handlingData = [
                        'no_handling' => $noHandling,
                        'id_handling_type' => $handlingTypeId,
                        'handling_date' => if_empty(format_date($handlingDate, 'Y-m-d H:i:s'), date('Y-m-d H:i:s')),
                        'status' => 'APPROVED',
                        'validated_by' => UserModel::authenticatedUserData('id'),
                        'validated_at' => date('Y-m-d H:i:s'),
                        'created_by' => UserModel::authenticatedUserData('id')
                    ];

                    // booking number is scanned
                    if (empty($safeConductId) && !empty($bookingId)) {
                        $booking = $this->booking->getBookingById($bookingId);
                        if (empty($containersData) && empty($goodsData)) {
                            // take whatever in booking detail
                            $containers = $this->bookingContainer->getBookingContainersByBooking($bookingId);
                            $goods = $this->bookingGoods->getBookingGoodsByBooking($bookingId, true);
                        } else {
                            // take only specific data from POST
                            $containers = [];
                            if (!empty($containersData)) {
                                foreach ($containersData as $container) {
                                    $container = $this->bookingContainer->getBookingContainerById($container['id_reference']);
                                    if (!empty($container)) {
                                        $containers[] = $container;
                                    }
                                }
                            }

                            $goods = $goodsData;
                        }

                        $redirectCode = $booking['no_booking'];
                        $ownerId = $booking['id_customer'];

                        $handlingData['id_booking'] = $booking['id'];
                        $handlingData['id_customer'] = $booking['id_customer'];
                    } else { // safe conduct is scanned
                        $safeConduct = $this->safeConduct->getSafeConductById($safeConductId);
                        $containers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($safeConductId);
                        $goods = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($safeConductId, true);

                        $redirectCode = $safeConduct['no_safe_conduct'];
                        $ownerId = $safeConduct['id_customer'];

                        $handlingData['id_booking'] = $safeConduct['id_booking'];
                        $handlingData['id_customer'] = $safeConduct['id_customer'];
                    }

                    // create handling
                    $this->handling->createHandling($handlingData);
                    $handlingId = $this->db->insert_id();

                    // insert detail handling container
                    foreach ($containers as $container) {
                        $this->handlingContainer->createHandlingContainer([
                            'id_handling' => $handlingId,
                            'id_booking_reference' => if_empty($container['id_booking_reference'], null),
                            'id_container' => $container['id_container'],
                            'id_owner' => $ownerId,
                            'id_position' => $container['id_position'],
                            'quantity' => $container['quantity'],
                            'seal' => $container['seal'],
                            'is_empty' => $container['is_empty'],
                            'status' => $container['status'],
                            'status_danger' => $container['status_danger'],
                            'description' => $container['description'],
                        ]);
                        $handlingContainerId = $this->db->insert_id();

                        if (empty($safeConductId) && !empty($bookingId)) {
                            $containerGoods = $this->bookingGoods->getBookingGoodsByBookingContainer($container['id']);
                        } else {
                            $containerGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConductContainer($container['id']);
                        }

                        // insert child goods of container
                        foreach ($containerGoods as $containerGood) {
                            $this->handlingGoods->createHandlingGoods([
                                'id_handling' => $handlingId,
                                'id_handling_container' => $handlingContainerId,
                                'id_owner' => $ownerId,
                                'id_booking_reference' => if_empty($containerGood['id_booking_reference'], null),
                                'id_goods' => $containerGood['id_goods'],
                                'id_unit' => $containerGood['id_unit'],
                                'id_position' => $containerGood['id_position'],
                                'quantity' => $containerGood['quantity'],
                                'unit_weight' => $containerGood['unit_weight'],
                                'unit_gross_weight' => $containerGood['unit_gross_weight'],
                                'unit_length' => $containerGood['unit_length'],
                                'unit_width' => $containerGood['unit_width'],
                                'unit_height' => $containerGood['unit_height'],
                                'unit_volume' => $containerGood['unit_volume'],
                                'no_pallet' => $containerGood['no_pallet'],
                                'status' => $containerGood['status'],
                                'status_danger' => $containerGood['status_danger'],
                                'ex_no_container' => if_empty($containerGood['ex_no_container'], null),
                                'description' => $containerGood['description'],
                            ]);
                        }
                    }

                    // insert detail handling goods with no parent container
                    if(!empty($goods)) {
                        foreach ($goods as $good) {
                            $this->handlingGoods->createHandlingGoods([
                                'id_handling' => $handlingId,
                                'id_owner' => $ownerId,
                                'id_booking_reference' => if_empty($good['id_booking_reference'], null),
                                'id_goods' => $good['id_goods'],
                                'id_unit' => $good['id_unit'],
                                'id_position' => $good['id_position'],
                                'quantity' => $good['quantity'],
                                'unit_weight' => $good['unit_weight'],
                                'unit_gross_weight' => $good['unit_gross_weight'],
                                'unit_length' => $good['unit_length'],
                                'unit_width' => $good['unit_width'],
                                'unit_height' => $good['unit_height'],
                                'unit_volume' => $good['unit_volume'],
                                'no_pallet' => $good['no_pallet'],
                                'status' => $good['status'],
                                'ex_no_container' => if_empty($good['ex_no_container'], null),
                                'description' => $good['description'],
                            ]);
                        }
                    }
                } else {
                    $handling = $this->handling->getHandlingById($handlingId);
                    $handlingCode = $handling['handling_code'];
                    $redirectCode = $handling['no_handling'];
                }

                // create handling component detail
                if (!empty($components)) {
                    foreach ($components as $componentId => $component) {
                        if (!empty($componentId)) {
                            $this->handlingComponent->createHandlingComponent([
                                'id_handling' => $handlingId,
                                'id_component' => $componentId,
                                'id_component_order' => $component['transaction'],
                                'quantity' => $component['quantity'],
                                'id_unit' => $component['unit'],
                                'description' => $component['description'],
                                'status' => 'APPROVED',
                            ]);
                        }
                    }
                }

                // create auto invoice handling
                $autoHandlingInvoice = get_setting('invoice_handling_auto');
                if ($autoHandlingInvoice) {
                    $handling = $this->handling->getHandlingById($handlingId);
                    $invoiceDetailData = $this->invoiceHandling->getInvoiceHandlingPrices($handling);

                    $itemSummary = '';
                    $handlingContainers = $this->handlingContainer->getHandlingContainersByHandling($handlingId);
                    $handlingGoods = $this->handlingGoods->getHandlingGoodsByHandling($handlingId, true);

                    foreach ($handlingContainers as $container) {
                        if (!empty($itemSummary)) {
                            $itemSummary .= ', ';
                        }
                        $itemSummary .= $container['no_container'] . ' (' . $container['type'] . '-' . $container['size'] . ')';
                    }
                    foreach ($handlingGoods as $good) {
                        if (!empty($itemSummary)) {
                            $itemSummary .= ', ';
                        }
                        $itemSummary .= $good['goods_name'] . ' (' . $good['tonnage'] . 'Kg-' . $good['volume'] . 'M3)';
                    }

                    $noInvoice = $this->invoice->getAutoNumberInvoice();
                    $this->invoice->createInvoice([
                        'no_invoice' => $noInvoice,
                        'no_reference' => $handling['no_handling'],
                        'id_branch' => $handling['id_branch'],
                        'id_customer' => $handling['id_customer'],
                        'type' => 'HANDLING',
                        'invoice_date' => date('Y-m-d H:i:s'),
                        'item_summary' => $itemSummary,
                        'description' => 'Auto create invoice',
                        'status' => 'DRAFT',
                        'created_by' => UserModel::authenticatedUserData('id')
                    ]);
                    $invoiceId = $this->db->insert_id();

                    if (!empty($invoiceDetailData)) {
                        foreach ($invoiceDetailData as &$invoiceDetail) {
                            $invoiceDetail['id_invoice'] = $invoiceId;
                            $invoiceDetail['created_by'] = UserModel::authenticatedUserData('id');
                            if (key_exists('total', $invoiceDetail)) {
                                unset($invoiceDetail['total']);
                            }
                        }
                        $this->invoiceDetail->createInvoiceDetail($invoiceDetailData);
                    } else {
                        //show_error('Detail invoice is missing, contact your administrator');
                    }
                }

                $noWorkOrder = $this->workOrder->getAutoNumberWorkOrder($handlingCode);
                $queue = $this->workOrder->getQueueNumberWorkOrder();

                $this->workOrder->createWorkOrder([
                    'no_work_order' => $noWorkOrder,
                    'queue' => $queue,
                    'id_handling' => $handlingId, // should EXIST, from new record or scanned handling number
                    'id_safe_conduct' => $safeConductId, // empty when job created by booking or handling
                    'description' => $description,
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('warning', "Handling job <strong>{$noWorkOrder}</strong> successfully created");
                } else {
                    flash('danger', "Create handling job <strong>{$noWorkOrder}</strong> failed, try again or contact administrator");
                }
                redirect('gate/check?code=' . $redirectCode);
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed, please wait until page loaded');
        }
        redirect('gate');
    }

    /**
     * Perform deleting work order data.
     * @param $workOrderId
     */
    public function delete($workOrderId)
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $workOrder = $this->workOrder->getWorkOrderById($workOrderId);

            $this->db->trans_start();

            $this->workOrder->deleteWorkOrder($workOrderId);
            $this->workOrderContainer->deleteContainersByWorkOrder($workOrderId);
            $this->workOrderGoods->deleteGoodsByWorkOrder($workOrderId);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('warning', "Handling <strong>{$workOrder['no_work_order']}</strong> successfully deleted");
            } else {
                flash('danger', "Delete work order <strong>{$workOrder['no_work_order']}</strong> failed, try again or contact administrator");
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('work-order');
    }

    /**
     * Check in job sheet.
     */
    public function check_in()
    {
        $redirect = $this->input->get('redirect');
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Job data', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('description', 'Description', 'trim|max_length[500]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $workOrderId = $this->input->post('id');
                $description = $this->input->post('description');

                $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
                $isNonWarehouse = $workOrder['handling_category'] == HandlingTypeModel::CATEGORY_NON_WAREHOUSE;

                $dataWorkOrder = [
                    'gate_in_date' => date('Y-m-d H:i:s'),
                    'gate_in_description' => $description,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => UserModel::authenticatedUserData('id')
                ];
                if ($isNonWarehouse) {
                    $dataWorkOrder['status'] = WorkOrderModel::STATUS_TAKEN;
                    $dataWorkOrder['taken_by'] = UserModel::authenticatedUserData('id');
                    $dataWorkOrder['taken_at'] = date('Y-m-d H:i:s');
                }

                $checkIn = $this->workOrder->updateWorkOrder($dataWorkOrder, $workOrderId);

                if ($checkIn) {
                    flash('success', "Job <strong>{$workOrder['no_work_order']}</strong> successfully checked in");
                } else {
                    flash('danger', "Check in job <strong>{$workOrder['no_work_order']}</strong> failed, try again or contact administrator");
                }

                if ($redirect == '') {
                    redirect('gate/check?code=' . $workOrder['no_handling']);
                } else {
                    redirect($redirect, false);
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed, please wait until page loaded');
        }
        if ($redirect == '') {
            redirect('gate/check');
        } else {
            redirect($redirect, false);
        }
    }

    /**
     * Check out job sheet.
     */
    public function check_out()
    {
        $redirect = $this->input->get('redirect');
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Job data', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('overtime', 'Overtime', 'trim|integer|is_natural');
            $this->form_validation->set_rules('staple', 'Staple', 'trim|integer|is_natural');
            $this->form_validation->set_rules('man_power', 'Man power', 'trim|integer|is_natural');
            $this->form_validation->set_rules('forklift', 'Forklift', 'trim|integer|is_natural');
            $this->form_validation->set_rules('tools', 'Tools', 'trim|max_length[500]');
            $this->form_validation->set_rules('materials', 'Materials', 'trim|max_length[500]');
            $this->form_validation->set_rules('description', 'Description', 'trim|max_length[500]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $workOrderId = $this->input->post('id');
                $overtime = $this->input->post('overtime');
                $staple = $this->input->post('staple');
                $manPower = $this->input->post('man_power');
                $forklift = $this->input->post('forklift');
                $tools = $this->input->post('tools');
                $materials = $this->input->post('materials');
                $description = $this->input->post('description');
                $components = $this->input->post('components');

                $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
                $handling = $this->handling->getHandlingById($workOrder['id_handling']);
                $containers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderId);
                $goods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderId);

                $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
                $isNonWarehouse = $workOrder['handling_category'] == HandlingTypeModel::CATEGORY_NON_WAREHOUSE;

                if ($workOrder['status'] == WorkOrderModel::STATUS_COMPLETED || $isNonWarehouse) {
                    $dataWorkOrder = [
                        'gate_out_date' => date('Y-m-d H:i:s'),
                        'gate_out_description' => $description,
                        'overtime' => $overtime,
                        'staple' => $staple,
                        'man_power' => $manPower,
                        'forklift' => $forklift,
                        'tools' => $tools,
                        'materials' => $materials,
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => UserModel::authenticatedUserData('id')
                    ];
                    if ($isNonWarehouse) {
                        $dataWorkOrder['status'] = WorkOrderModel::STATUS_COMPLETED;
                        $dataWorkOrder['taken_by'] = UserModel::authenticatedUserData('id');
                        $dataWorkOrder['completed_at'] = date('Y-m-d H:i:s');
                    }

                    $this->db->trans_start();

                    // create work order component detail
                    if (!empty($components)) {
                        foreach ($components as $componentId => $component) {
                            if (!empty($componentId)) {
                                $this->workOrderComponent->createWorkOrderComponent([
                                    'id_work_order' => $workOrderId,
                                    'id_component' => $componentId,
                                    'id_component_order' => $component['transaction'],
                                    'quantity' => $component['quantity'],
                                    'id_unit' => $component['unit'],
                                    'description' => $component['description'],
                                    'status' => 'APPROVED',
                                ]);
                            }
                        }
                    }

                    // checking out
                    $this->workOrder->updateWorkOrder($dataWorkOrder, $workOrderId);

                    // create auto invoice work order
                    $autoJobInvoice = get_setting('invoice_job_auto');
                    if ($autoJobInvoice) {
                        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
                        $invoiceDetailData = $this->invoiceWorkOrder->getInvoiceWorkOrderPrices($workOrder);

                        $itemSummary = '';
                        $workOrderContainers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderId, true);
                        $workOrderGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderId, true, true);

                        foreach ($workOrderContainers as $container) {
                            if (!empty($itemSummary)) {
                                $itemSummary .= ', ';
                            }
                            $itemSummary .= $container['no_container'] . ' (' . $container['type'] . '-' . $container['size'] . ')';
                        }
                        foreach ($workOrderGoods as $good) {
                            if (!empty($itemSummary)) {
                                $itemSummary .= ', ';
                            }
                            $itemSummary .= $good['goods_name'] . ' (' . $good['tonnage'] . 'Kg-' . $good['volume'] . 'M3)';
                        }

                        $noInvoice = $this->invoice->getAutoNumberInvoice();
                        $this->invoice->createInvoice([
                            'no_invoice' => $noInvoice,
                            'no_reference' => $workOrder['no_work_order'],
                            'id_branch' => $workOrder['id_branch'],
                            'id_customer' => $workOrder['id_customer'],
                            'type' => 'WORK ORDER',
                            'invoice_date' => date('Y-m-d H:i:s'),
                            'item_summary' => $itemSummary,
                            'description' => 'Auto create invoice',
                            'status' => 'DRAFT',
                            'created_by' => UserModel::authenticatedUserData('id')
                        ]);
                        $invoiceId = $this->db->insert_id();

                        if (!empty($invoiceDetailData)) {
                            foreach ($invoiceDetailData as &$invoiceDetail) {
                                $invoiceDetail['id_invoice'] = $invoiceId;
                                $invoiceDetail['created_by'] = UserModel::authenticatedUserData('id');
                                if (key_exists('total', $invoiceDetail)) {
                                    unset($invoiceDetail['total']);
                                }
                            }
                            $this->invoiceDetail->createInvoiceDetail($invoiceDetailData);
                        } else {
                            //show_error('Detail invoice is missing, contact your administrator');
                        }
                    }

                    $this->db->trans_complete();

                    if ($this->db->trans_status()) {
                        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);

                        // suruh pak jamil komen
                        // --------------------------------------------------------------------------------------------------
                        // $emailTo = $workOrder['customer_email'];
                        // $emailTitle = 'Handling job summary No ' . $workOrder['no_work_order'] . ' on ' . date('d F Y');
                        // $emailTemplate = 'emails/job_summary';
                        // $emailData = [
                        //     'handling' => $handling,
                        //     'workOrder' => $workOrder,
                        //     'containers' => $containers,
                        //     'goods' => $goods,
                        //     'description' => $description
                        // ];

                        // $send = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);

                        // if ($send) {
                        //     flash('success', "Job <strong>{$workOrder['no_work_order']}</strong> successfully checked out, and customer was notified");
                        // } else {
                        //     flash('warning', "Customer does not notified but job <strong>{$workOrder['no_work_order']}</strong> successfully checked out");
                        // }
                        // -----------------------------------------------------------------------------------------------------------------------

                        flash('warning', "Customer does not notified but job <strong>{$workOrder['no_work_order']}</strong> successfully checked out");
                    } else {
                        flash('danger', "Check out job <strong>{$workOrder['no_work_order']}</strong> failed, try again or contact administrator");
                    }
                } else {
                    flash('danger', "Job <strong>{$workOrder['no_work_order']}</strong> still in progress, please confirm to your Tally man");
                }
                if ($redirect == '') {
                    redirect('gate/check?code=' . $workOrder['no_handling']);
                } else {
                    redirect($redirect, false);
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed, please wait until page loaded');
        }
        if ($redirect == '') {
            redirect('gate/check');
        } else {
            redirect($redirect, false);
        }
    }

    /**
     * Print work order.
     */
    public function print_work_order()
    {
        $redirect = $this->input->get('redirect');
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Job data', 'trim|required|max_length[50]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $workOrderId = $this->input->post('id');
                $workOrder = $this->workOrder->getWorkOrderById($workOrderId);

                // get job detail by safe conduct
                $safeConduct = $this->safeConduct->getSafeConductById($workOrder['id_safe_conduct']);
                $jobContainers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($safeConduct['id']);
                foreach ($jobContainers as &$container) {
                    $containerGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConductContainer($container['id']);
                    $container['goods'] = $containerGoods;
                }
                $jobGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($safeConduct['id'], true);

                // or handling booking.
                if (empty($workOrder['id_safe_conduct'])) {
                    $jobContainers = $this->handlingContainer->getHandlingContainersByHandling($workOrder['id_handling']);
                    foreach ($jobContainers as &$container) {
                        $containerGoods = $this->handlingGoods->getHandlingGoodsByHandlingContainer($container['id']);
                        $container['goods'] = $containerGoods;
                    }
                    $jobGoods = $this->handlingGoods->getHandlingGoodsByHandling($workOrder['id_handling'], true);
                }
                $components = $this->handlingComponent->getHandlingComponentsByHandling($workOrder['id_handling']);

                if ($workOrder['print_total'] < $workOrder['print_max']) {
                    $update = $this->workOrder->updateWorkOrder([
                        'print_total' => intval($workOrder['print_total']) + 1
                    ], $workOrderId);

                    if ($update) {
                        $barcode = new \Milon\Barcode\DNS2D();
                        $barcode->setStorPath(APPPATH . "/cache/");
                        $workOrderBarcode = $barcode->getBarcodePNG($workOrder['no_work_order'], "QRCODE", 4, 4);
                        $handlingBarcode = $barcode->getBarcodePNG($workOrder['no_handling'], "QRCODE", 4, 4);

                        $bookingInbound = $this->booking->getBookingById($workOrder['id_booking_in']);
                        $data = [
                            'title' => 'Print Job Sheet',
                            'page' => 'workorder/print_work_order',
                            'workOrder' => $workOrder,
                            'jobContainers' => $jobContainers,
                            'jobGoods' => $jobGoods,
                            'barcodeWorkOrder' => $workOrderBarcode,
                            'barcodeHandling' => $handlingBarcode,
                            'components' => $components,
                            'bookingInbound' => $bookingInbound,
                        ];
                        $this->load->view('template/print', $data);
                        return;
                    } else {
                        flash('danger', "Print <strong>{$workOrder['no_work_order']}</strong> failed, try again or contact administrator");
                    }
                } else {
                    flash('danger', "You've reached total print of work order <strong>{$workOrder['no_work_order']}</strong>, please contact administrator to grant permission");
                }
                if ($redirect == '') {
                    redirect('gate/check?code=' . $workOrder['no_handling']);
                } else {
                    redirect($redirect, false);
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed, please wait until page loaded');
        }
        if ($redirect == '') {
            redirect('gate/check');
        } else {
            redirect($redirect, false);
        }
    }

    /**
     * Print tally sheet result of checking goods or container.
     * @param $workOrderId
     */
    public function print_tally_sheet($workOrderId)
    {
        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
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

        $barcode = new \Milon\Barcode\DNS2D();
        $barcode->setStorPath(APPPATH . "/cache/");
        $workOrderBarcode = $barcode->getBarcodePNG($workOrder['no_work_order'], "QRCODE", 4, 4);
        $handlingBarcode = $barcode->getBarcodePNG($workOrder['no_handling'], "QRCODE", 4, 4);

        $tally2 = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
            'status_histories.id_reference' => $workOrderId,
            'status_histories.status' => WorkOrderModel::STATUS_VALIDATION_CHECKED,
        ], true);
        $coordinator = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
            'status_histories.id_reference' => $workOrderId,
            'status_histories.status' => WorkOrderModel::STATUS_VALIDATION_APPROVED,
        ], true);

        $data = [
            'title' => 'Print Tally Sheet',
            'page' => 'workorder/print_tally_sheet',
            'workOrder' => $workOrder,
            'containers' => $containers,
            'goods' => $goods,
            'barcodeWorkOrder' => $workOrderBarcode,
            'barcodeHandling' => $handlingBarcode,
            'tally2' => $tally2,
            'coordinator' => $coordinator,
        ];
        $this->load->view('template/print_wide', $data);
    }


    /**
     * Print tally sheet mode 2.
     * @param $workOrderId
     */
    public function print_tally_sheet2($workOrderId)
    {
        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
        $containers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderId, true);
        foreach ($containers as &$container) {
            $container['goods'] = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($container['id']);

            foreach ($container['goods'] as &$containerItem) {
                $containerItem['parent_goods_name'] = '';
                $containerItem['parent_no_goods'] = '';
                if (!empty($containerItem['id_goods_parent'])) {
                    $goodsParent = $this->goods->getById($containerItem['id_goods_parent']);
                    if (!empty($goodsParent)) {
                        $item['parent_goods_name'] = $goodsParent['name'];
                        $item['parent_no_goods'] = $goodsParent['no_goods'];
                        $item['unpackage_quantity'] = 0;

                        $unpackage = $this->workOrderGoods->getWorkOrderUnpackageQuantity($item['id_goods_parent']);
                        if (!empty($unpackage)) {
                            $item['unpackage_quantity'] = $unpackage['total'];
                        }
                    }
                }
            }
        }
        $goods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderId);
        foreach ($goods as &$item) {
            $item['parent_goods_name'] = '';
            $item['parent_no_goods'] = '';
            if (!empty($item['id_goods_parent'])) {
                $goodsParent = $this->goods->getById($item['id_goods_parent']);
                if (!empty($goodsParent)) {
                    $item['parent_goods_name'] = $goodsParent['name'];
                    $item['parent_no_goods'] = $goodsParent['no_goods'];
                    $item['unpackage_quantity'] = 0;

                    $unpackage = $this->workOrderGoods->getWorkOrderUnpackageQuantity($item['id_goods_parent']);
                    if (!empty($unpackage)) {
                        $item['unpackage_quantity'] = $unpackage['total'];
                    }
                }
            }
        }

        $fromInboundPackage = false;
        $bookingInboundIds = explode(',', $workOrder['id_booking_in']);
        foreach ($bookingInboundIds as $bookingInboundId) {
            $addUnpackages = $this->handling->getBy([
                'ref_handling_types.handling_type' => 'ADD UNPACKAGE',
                'handlings.id_booking' => $bookingInboundId
            ]);
            if (!empty($addUnpackages)) {
                $fromInboundPackage = true;
                break;
            }
        }

        $data = [
            'title' => 'Print Tally Sheet',
            'workOrder' => $workOrder,
            'containers' => $containers,
            'goods' => $goods,
            'fromInboundPackage' => $fromInboundPackage
        ];
        $this->load->view('workorder/print_tally_sheet2', $data);
    }

    /**
     * Print tally sheet mode 3.
     * @param $workOrderId
     */
    public function print_tally_sheet3($workOrderId)
    {
        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
        $containers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderId, true);
        foreach ($containers as &$container) {
            $containerGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($container['id']);
            $container['goods'] = $containerGoods;

            $containerContainers = $this->workOrderContainer->getWorkOrderContainersByWorkOrderContainer($container['id']);
            $container['containers'] = $containerContainers;
        }
        $goods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderId);
        $data = [
            'title' => 'Print Tally Sheet',
            'workOrder' => $workOrder,
            'containers' => $containers,
            'goods' => $goods,
        ];
        $this->load->view('workorder/print_tally_sheet3', $data);
    }
    /**
     * Print tally sheet mode 4.
     * @param $workOrderId
     */
    public function print_tally_sheet4($workOrderId)
    {
        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
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
        $workOrderStatuses = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION,
            'status_histories.id_reference' => $workOrderId
        ]);
        foreach ($workOrderStatuses as &$workOrderStatus) {
            $workOrderStatus['goods']=$goods;
        }

        $goods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderId);
        $data = [
            'title' => 'Print Tally Sheet',
            'workOrder' => $workOrder,
            'containers' => $containers,
            'goods' => $goods,
            'workOrderStatuses' => $workOrderStatuses,
        ];
        $this->load->view('workorder/print_tally_sheet4', $data);
    }

    /**
     * Print eir result of checking goods or container.
     * @param $workOrderId
     */
    public function print_eir($workOrderId)
    {
        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
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

        $barcode = new \Milon\Barcode\DNS2D();
        $barcode->setStorPath(APPPATH . "/cache/");
        $workOrderBarcode = $barcode->getBarcodePNG($workOrder['no_work_order'], "QRCODE", 4, 4);
        $handlingBarcode = $barcode->getBarcodePNG($workOrder['no_handling'], "QRCODE", 4, 4);

        $data = [
            'title' => 'Print EIR',
            'page' => 'workorder/print_eir',
            'workOrder' => $workOrder,
            'containers' => $containers,
            'goods' => $goods,
            'barcodeWorkOrder' => $workOrderBarcode,
            'barcodeHandling' => $handlingBarcode,
        ];
        $this->load->view('template/print', $data);
    }

    /**
     * Print handover report.
     * @param $workOrderId
     */
    public function print_handover_report($workOrderId)
    {
        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
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

        $barcode = new \Milon\Barcode\DNS2D();
        $barcode->setStorPath(APPPATH . "/cache/");
        $workOrderBarcode = $barcode->getBarcodePNG($workOrder['no_work_order'], "QRCODE", 4, 4);
        $handlingBarcode = $barcode->getBarcodePNG($workOrder['no_handling'], "QRCODE", 4, 4);

        $defaultHandlingOutbound = get_setting('default_outbound_handling');
        if ($workOrder['id_handling_type'] == $defaultHandlingOutbound) {
            $firstPerson = 'Transcon Indonesia';
            $secondPerson = $workOrder['customer_name'];
        } else {
            $firstPerson = $workOrder['customer_name'];
            $secondPerson = 'Transcon Indonesia';
        }

        $data = [
            'title' => 'Print Handover Report',
            'page' => 'workorder/print_handover_report',
            'workOrder' => $workOrder,
            'containers' => $containers,
            'goods' => $goods,
            'firstPerson' => $firstPerson,
            'secondPerson' => $secondPerson,
            'barcodeWorkOrder' => $workOrderBarcode,
            'barcodeHandling' => $handlingBarcode,
        ];
        $this->load->view('template/print', $data);
    }

     /**
     * get data pallet.
     * @param $id
     */
    public function pallet_histories()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $id_pallet = $this->input->get('id_pallet');
            $palletHistories = $this->palletMarkingHistory->getBy(['id_reference' => $id_pallet]); 
            if (isset($_SERVER['HTTP_ACCEPT']) && preg_match('/text\/html/', $_SERVER['HTTP_ACCEPT'])) {
                echo $this->load->view('workorder/_field_pallet_marking_histories', [
                    'palletHistories' => $palletHistories
                ], true);
            }

        }
    }

    /**
     * Print pallet.
     * @param $id
     */
    public function print_pallet($id)
    {
        $barcode = new \Milon\Barcode\DNS2D();
        $barcode->setStorPath(APPPATH . "/cache/");

        $goods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($id);
        $workOrder = $this->workOrder->getWorkOrderById($id);

        if (empty($goods)) {
            flash('danger', 'No any goods or pallet available');
            $redirect = $this->input->get('redirect');
            if (empty($redirect)) {
                redirect('work_order');
            } else {
                redirect($redirect, false);
            }
        }

        $this->db->trans_start();

        $this->palletMarkingHistory->create([
            'id_reference' => $id,
            'description' => implode(',',array_column($goods, 'no_pallet')),
            'created_by' => UserModel::authenticatedUserData('id'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->workOrder->updateWorkOrder([
            'print_pallet_total' => intval($workOrder['print_pallet_total']) + 1,
            'pallet_status' => PalletMarkingHistoryModel::STATUS_PRINTED,
        ], $id);

        $this->db->trans_complete();

        $data = [
            'title' => "Pallet",
            'goods' => $goods,
            'barcode' => $barcode,
            'tally_name' => $workOrder['tally_name'],
        ];

        if($this->db->trans_status()){
            $this->load->view('workorder/print_pallet', $data);
        }else{
            flash('danger', 'Print failed, please try again !');
        }

        $page = $this->load->view('workorder/print_pallet', $data, true);

        $dompdf = new \Dompdf\Dompdf(['isHtml5ParserEnabled' => true]);
        $dompdf->loadHtml($page);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("pallets.pdf", array("Attachment" => false));
    }

    /**
     * Print pallet by label.
     * @param $id
     */
    public function print_pallet_label($id)
    {
        $barcode = new \Milon\Barcode\DNS2D();
        $barcode->setStorPath(APPPATH . "/cache/");

        $goods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($id);
        $workOrder = $this->workOrder->getWorkOrderById($id);

        if (empty($goods)) {
            flash('danger', 'No any goods or pallet available');
            $redirect = $this->input->get('redirect');
            if (empty($redirect)) {
                redirect('work_order');
            } else {
                redirect($redirect, false);
            }
        }

        $this->db->trans_start();

        $this->palletMarkingHistory->create([
            'id_reference' => $id,
            'description' => implode(',',array_column($goods, 'no_pallet')),
            'created_by' => UserModel::authenticatedUserData('id'),
            'created_at' => date('Y-m-d H:i:s')
        ]);

        $this->workOrder->updateWorkOrder([
            'print_pallet_total' => intval($workOrder['print_pallet_total']) + 1,
            'pallet_status' => PalletMarkingHistoryModel::STATUS_PRINTED,
        ], $id);

        $this->db->trans_complete();

        $data = [
            'title' => "Pallet",
            'goods' => $goods,
            'barcode' => $barcode,
            'tally_name' => $workOrder['tally_name'],
        ];

        if($this->db->trans_status()){
            $this->load->view('workorder/print_pallet_label', $data);
        }else{
            flash('danger', 'Print failed, please try again !');
        }

        $page = $this->load->view('workorder/print_pallet_label', $data, true);
        
        $dompdf = new \Dompdf\Dompdf(['isHtml5ParserEnabled' => true]);
        $dompdf->loadHtml($page);
        $customPaper = array(0,0,283.46,425.196);//100 mm X 150 mm (rumus = inch x 72)
        $dompdf->setPaper($customPaper, 'portrait');
        $dompdf->render();
        $dompdf->stream("pallets.pdf", array("Attachment" => false));
    }

    /**
     * Update work order total print max.
     */
    public function update_print_max()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Job data', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $workOrderId = $this->input->post('id');
                $maxPrint = $this->input->post('print_max');

                $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
                $updatePrintMax = $this->workOrder->updateWorkOrder([
                    'print_max' => $maxPrint
                ], $workOrderId);

                if ($updatePrintMax) {
                    flash('success', "Total print of job <strong>{$workOrder['no_work_order']}</strong> successfully updated to <strong>{$maxPrint}</strong> x print");
                } else {
                    flash('danger', "Update total print max job <strong>{$workOrder['no_work_order']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('work_order');
    }

    /**
     * Get booking data for creating safe conduct.
     */
    public function ajax_get_work_orders_by_id()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $workOrderId = $this->input->get('id_work_order');
            $workOrder = $this->workOrder->getWorkOrderById($workOrderId);

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

            if (isset($_SERVER['HTTP_ACCEPT']) && preg_match('/text\/html/', $_SERVER['HTTP_ACCEPT'])) {
                if (!empty($workOrder) && (!empty($containers) || !empty($goods))) {
                    echo $this->load->view('workorder/_data_detail', [
                        'workOrder' => $workOrder,
                        'containers' => $containers,
                        'goods' => $goods
                    ], true);
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'workOrder' => $workOrder,
                    'containers' => $containers,
                    'goods' => $goods
                ]);
            }
        }
    }

    /**
     * Get stock by booking customer.
     */
    public function ajax_get_stock_by_booking()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $exceptWorkOrderId = $this->input->get('except_work_order');
            $bookingId = $this->input->get('id_booking');
            $holdByStatus = if_empty($this->input->get('hold_by_status'), 0);
            $holdByInvoice = if_empty($this->input->get('hold_by_invoice'), 0);

            if (!is_array($bookingId)) {
                $bookingId = explode(',', $bookingId);
            }

            $bookings = $this->booking->getBookingById($bookingId);

            if ($holdByInvoice) {
                if (strpos(get_active_branch('branch'), 'TPP') !== false) {
                    $isInvoiceExist = true;
                    foreach ($bookings as $booking) {
                        $invoiceBooking = $this->invoice->getInvoicesByNoReference($booking['no_booking'], 'PUBLISHED');
                        if (empty($invoiceBooking)) {
                            $isInvoiceExist = false;
                        }
                    }
                    if (!$isInvoiceExist) {
                        echo '<p class="text-danger lead">Invoice is required to perform this action!</p>';
                        die();
                    }
                }
            }

            if ($holdByStatus) {
                /*
                if (strpos(get_active_branch('branch'), 'MEDAN') !== false) {
                    $paymentComplete = BookingModel::PAYMENT_SKB_BILLING_DONE;
                    $bcfComplete = BookingModel::BCF_SPPF_DONE;
                    if ($booking['payment_status'] != $paymentComplete || $booking['bcf_status'] != $bcfComplete) {
                        echo "
                              <p>{$booking['no_booking']} has payment status {$booking['payment_status']} and BCF status {$booking['bcf_status']}</p>
                              <p class='text-danger lead'>
                                Payment must have status <strong>{$paymentComplete}</strong> and BCF must have status <strong>{$bcfComplete}</strong> to place a booking out!
                              </p>
                              ";
                        die();
                    }
                }
                */
            }

            $stockContainers = $this->reportStock->getStockContainers([
                'data' => 'stock',
                'booking' => $bookingId,
                'except_work_order' => $exceptWorkOrderId,
            ]);

            $stockGoods = $this->reportStock->getStockGoods([
                'data' => 'stock',
                'booking' => $bookingId,
                'except_work_order' => $exceptWorkOrderId,
            ]);

            if (isset($_SERVER['HTTP_ACCEPT']) && preg_match('/text\/html/', $_SERVER['HTTP_ACCEPT'])) {
                if (!empty($bookings)) {
                    echo $this->load->view('workorder/_booking_stock', [
                        'booking' => reset($bookings),
                        'bookings' => $bookings,
                        'containers' => $stockContainers,
                        'goods' => $stockGoods,
                        'holdByStatus' => $holdByStatus
                    ], true);
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'booking' => reset($bookings),
                    'bookings' => $bookings,
                    'containers' => $stockContainers,
                    'goods' => $stockGoods,
                    'holdByStatus' => $holdByStatus
                ]);
            }
        }
    }

    /**
     * Get stock by booking customer.
     */
    public function ajax_get_stock_by_scanner()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $bookingId = $this->input->post('id_booking');
            $no_pallet = $this->input->post('no_pallet');
            $bookingIdOut = $this->input->post('id_booking_out');

            if (!is_array($bookingId)) {
                $bookingId = explode(',', $bookingId);
            }

            $foundItem = null;

            $stockGoods = $this->reportStock->getStockGoods([
                'data' => 'stock',
                'booking' => $bookingId,
                'no_pallet' => $no_pallet,//'7683/BSIU2971450/BI/20/01/000005'
            ]);
            if (empty($stockGoods)) {
                $status = 'Stock not found';
            } else {
                $stockItem = $stockGoods[0];
                $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($bookingIdOut, true);
                $foundInBookingOut = false;
                foreach ($bookingGoods as $goods) {
                    if ($goods['id_goods'] == $stockItem['id_goods'] && $goods['id_unit'] == $stockItem['id_unit'] && if_empty($goods['ex_no_container']) == if_empty($stockItem['ex_no_container'], '')) {
                        $foundInBookingOut = true;
                        break;
                    }
                }

                if ($foundInBookingOut) {
                    $holdItem = $this->transporterEntryPermitHoldItem->getBy([
                        'transporter_entry_permit_request_hold_items.hold_status' => 'HOLD',
                        'transporter_entry_permit_request_hold_items.id_booking' => $bookingIdOut,
                        'transporter_entry_permit_request_hold_items.id_goods' => $stockItem['id_goods'],
                        'transporter_entry_permit_request_hold_items.id_unit' => $stockItem['id_unit'],
                        'IFNULL(transporter_entry_permit_request_hold_items.ex_no_container, "") = "' . if_empty($stockItem['ex_no_container'], "") . '"' => null,
                        //'transporter_entry_permit_request_hold_items.ex_no_container' => null,
                    ]);
                    if (empty($holdItem)) {
                        $foundItem = $stockItem;
                        $status = 'OK';
                    } else {
                        $status = 'Item is hold, release first to continue';
                    }
                } else {
                    $status = 'Stock booking out not found';
                }
            }

            header('Content-Type: application/json');
            echo json_encode([
                'status' => $status,
                'goods' => $foundItem,
            ]);
        }
    }

    /**
     * Get stock by customer.
     */
    public function ajax_get_stock_by_customer()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $customerId = $this->input->get('id_customer');
            $holdByStatus = if_empty($this->input->get('hold_by_status'), 0);

            $stockContainers = $this->reportStock->getStockContainers([
                'data' => 'stock',
                'owner' => $customerId
            ]);

            $stockGoods = $this->reportStock->getStockGoods([
                'data' => 'stock',
                'owner' => $customerId
            ]);

            if (isset($_SERVER['HTTP_ACCEPT']) && preg_match('/text\/html/', $_SERVER['HTTP_ACCEPT'])) {
                echo $this->load->view('workorder/_customer_stock', [
                    'stockContainers' => $stockContainers,
                    'stockGoods' => $stockGoods,
                    'holdByStatus' => $holdByStatus
                ], true);
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'stockContainers' => $stockContainers,
                    'stockGoods' => $stockGoods,
                    'holdByStatus' => $holdByStatus
                ]);
            }
        }
    }

    /**
     * Get work order by date.
     */
    public function ajax_get_work_order_by_date()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $workOrders = $this->workOrder->getWorkOrderSummary($_GET);

            $this->load->view('workorder_document/_job_list', compact('workOrders'));
        }
    }
    /**
     * Ajax get all no work orders
     */
    public function ajax_get_work_orders()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $search = $this->input->get('q');
            $page = $this->input->get('page');

            $noWorkOrders = $this->workOrder->getNoWorkOrder($search, $page);
            echo json_encode($noWorkOrders);
        }
    }

    /**
     * Generate many pallet number.
     */
    public function ajax_generate_pallet()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $total = $this->input->post('total');
            $pallets = [];

            $noPallet = $this->workOrderGoods->getAutoNumberPallet();
            for ($i = 0; $i < $total; $i++) {
                $pallets[] = $noPallet;

                $parts = explode('/', $noPallet);
                $order = array_pop($parts) + 1;
                $noPallet = implode('/', $parts) . '/' . str_pad($order, 6, '0', STR_PAD_LEFT);
            }
            header('Content-Type: application/json');
            echo json_encode($pallets);
        }
    }
    public function locked_tally(){
        $param = $this->input->post('url_param');
        $date_from = $this->input->post('date_from_locked');
        $date_to = $this->input->post('date_to_locked');
        $customers = $this->input->post('customer_locked');
        $handling_type = $this->input->post('handling_type_locked');
        $no_work_order = $this->input->post('no_work_order_locked');
        $description="";
        // print_debug($date_from);
        if (!empty($customers)) {
            if (count($customers)>1) {
                $customers=implode( ", ", $customers);
            }else {
                $customers=implode($customers);
            }
        }
        if (!empty($handling_type)) {
            if (count($handling_type)>1) {
                $handling_type=implode( ", ", $handling_type);
            }else {
                $handling_type=implode($handling_type);
            }
        }
        if (!empty($no_work_order)) {
            if (count($no_work_order)>1) {
                $no_work_order=implode( ", ", $no_work_order);
            }else {
                $no_work_order=implode($no_work_order);
            }
        }
        
        if ($date_from!=null && $date_to!=null) {
            $description="Locked tally by complete from ".$date_from." to ".$date_to;

            $date_from = $date_from." 00:00:00";
            $date_to = $date_to." 23:59:59";
            
            $date_from = date("Y-m-d H:i:s", strtotime($date_from));
            $date_to = date("Y-m-d H:i:s", strtotime($date_to));           
        }else {
            $description="Locked";
        }
        // }
        // print_r($customers);
        // print_r($date_from);
        // print_r($date_to);
        $branch = get_active_branch();

        $where=[
            'id_branch' => $branch['id'],
            'date_from' => $date_from,
            'date_to' => $date_to,
            'id_customers' => $customers,
            'id_handling_type' => $handling_type,
            'no_work_order' => $no_work_order
        ];
        // print_r($where);
        $date_now_history = date('Y-m-d H:i:s');
        $date_now = "'".$date_now_history."'";
        $this->workOrder->setLocked([
            'is_locked' => '1',
            'locked_by' => UserModel::authenticatedUserData('id'),
            'locked_at' => $date_now
        ],$where);
        
        $id_work_orders = $this->workOrder->getIdWorkOrderByLockedParam($where);
        // $id_temp="";
        // $loop_count=0;
        foreach ($id_work_orders as $id_work_order) {
            $this->workOrderHistoryLock->create([
                'id_work_order' => $id_work_order->id,
                'description' => $description,
                'status' => 'LOCKED',
                'locked_by' => UserModel::authenticatedUserData('id'),
                'locked_at' => $date_now_history,
                'created_at' => $date_now_history,
                'created_by' => UserModel::authenticatedUserData('id')
            ]);
        }        
        // print_r($this->db->last_query());
        if (!empty($param)) {
            redirect('work-order?'.$param);
        } else {
            redirect('work-order');
        }
        
        
    }
    public function unlocked_tally(){
        $param = $this->input->post('url_param');
        $date_from = $this->input->post('date_from_locked');
        $date_to = $this->input->post('date_to_locked');
        $customers = $this->input->post('customer_locked');
        $handling_type = $this->input->post('handling_type_locked');
        $no_work_order = $this->input->post('no_work_order_locked');
        $description="";
        // print_debug($date_from);
        if (!empty($customers)) {
            if (count($customers)>1) {
                $customers=implode( ", ", $customers);
            }else {
                $customers=implode($customers);
            }
        }
        if (!empty($handling_type)) {
            if (count($handling_type)>1) {
                $handling_type=implode( ", ", $handling_type);
            }else {
                $handling_type=implode($handling_type);
            }
        }
        if (!empty($no_work_order)) {
            if (count($no_work_order)>1) {
                $no_work_order=implode( ", ", $no_work_order);
            }else {
                $no_work_order=implode($no_work_order);
            }
        }
        
        if ($date_from!=null && $date_to!=null) {
            $description="Unlocked tally by complete from ".$date_from." to ".$date_to;
            $date_from = $date_from." 00:00:00";
            $date_to = $date_to." 23:59:59";
            
            $date_from = date("Y-m-d H:i:s", strtotime($date_from));
            $date_to = date("Y-m-d H:i:s", strtotime($date_to));
        }else {
            $description="Unlocked";
        }

        $date_now_history = date('Y-m-d H:i:s');

        $branch = get_active_branch();

        $where=[
            'id_branch' => $branch['id'],
            'date_from' => $date_from,
            'date_to' => $date_to,
            'id_customers' => $customers,
            'id_handling_type' => $handling_type,
            'no_work_order' => $no_work_order
        ];
        // print_r($where);

        $this->workOrder->setLocked([
            'is_locked' => '0',
            'locked_by' => 'NULL',
            'locked_at' => 'NULL'
        ],$where);
        
        $id_work_orders = $this->workOrder->getIdWorkOrderByLockedParam($where);

        foreach ($id_work_orders as $id_work_order) {
            $this->workOrderHistoryLock->create([
                'id_work_order' => $id_work_order->id,
                'description' => $description,
                'status' => 'UNLOCKED',
                'locked_by' => UserModel::authenticatedUserData('id'),
                'locked_at' => $date_now_history,
                'created_at' => $date_now_history,
                'created_by' => UserModel::authenticatedUserData('id')
            ]);
        }   
        // print_r($this->db->last_query());
        if (!empty($param)) {
            redirect('work-order?'.$param);
        } else {
            redirect('work-order');
        }
    }
    public function locked_by_work_order_id($id){
        $description="Locked Now";
        $redirect = get_url_param('redirect') ? $_GET : [];
        $param = $_SERVER['QUERY_STRING'];
        $date_now_history = date('Y-m-d H:i:s');

        $branch = get_active_branch();

        $where=[
            'id_branch' => $branch['id'],
            'id_work_order' => $id
        ];
        // print_debug($redirect['redirect']);

        $this->workOrder->setLocked([
            'is_locked' => '1',
            'locked_by' =>  UserModel::authenticatedUserData('id'),
            'locked_at' =>  "'".$date_now_history."'"
        ],$where);
        
        $this->workOrderHistoryLock->create([
            'id_work_order' => $id,
            'description' => $description,
            'status' => 'LOCKED',
            'locked_by' => UserModel::authenticatedUserData('id'),
            'locked_at' => $date_now_history,
            'created_at' => $date_now_history,
            'created_by' => UserModel::authenticatedUserData('id')
        ]); 
        // print_r($this->db->last_query());
        if (!empty($redirect)) {
            redirect($redirect['redirect']);
        }
        if (!empty($param)) {
            redirect('work-order?'.$param);
        }
        redirect('work-order');
    }
    public function unlock_by_work_order_id($id){
        $description="Unlocked Now";
        $redirect = get_url_param('redirect') ? $_GET : [];
        $param = $_SERVER['QUERY_STRING'];
        $date_now_history = date('Y-m-d H:i:s');

        $branch = get_active_branch();

        $where=[
            'id_branch' => $branch['id'],
            'id_work_order' => $id
        ];
        // print_debug($redirect['redirect']);

        $this->workOrder->setLocked([
            'is_locked' => '0',
            'locked_by' => 'NULL',
            'locked_at' => 'NULL'
        ],$where);
        
        $this->workOrderHistoryLock->create([
            'id_work_order' => $id,
            'description' => $description,
            'status' => 'UNLOCKED',
            'locked_by' => UserModel::authenticatedUserData('id'),
            'locked_at' => $date_now_history,
            'created_at' => $date_now_history,
            'created_by' => UserModel::authenticatedUserData('id')
        ]); 
        // print_r($this->db->last_query());
        if (!empty($redirect)) {
            redirect($redirect['redirect']);
        }
        if (!empty($param)) {
            redirect('work-order?'.$param);
        }
        redirect('work-order');
    }

    public function set_request_unlock_tally()
    {
        $id = $this->input->post('id');
        $date_from = $this->input->post('date_from_request');
        $date_to = $this->input->post('date_to_request');
        $description = $this->input->post('unlocked_reason');
        $editType = $this->input->post('edit_type');
        $branch = get_active_branch();
        $descriptions = "Request Unlocked tally from " . $date_from . " to " . $date_to;
        $descriptions = $descriptions . "\n Reason : " . $description;
        $request =[
            'id_work_order' => $id,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'id_branch' => $branch['id'],
            'description' => $description,
        ];
        $date_from = date("Y-m-d", strtotime($date_from));
        $date_to = date("Y-m-d", strtotime($date_to));
        $create_at = date('Y-m-d H:i:s');
        $workOrder = $this->workOrder->getWorkOrderById($id);

        // find out requested job contains same goods in another job
        $relatedWorkOrders = [];
        if ($editType == 'unit-attribute') {
            $workOrderGoods = $this->workOrderGoods->getBy(['work_order_goods.id_work_order' => $id]);
            foreach ($workOrderGoods as $workOrderItem) {
                $relatedGoods = $this->workOrderGoods->getBy([
                    'work_order_goods.id_goods' => $workOrderItem['id_goods'],
                    'work_order_goods.id_work_order !=' => $id,
                ]);
                foreach ($relatedGoods as $relatedItem) {
                    // exclude moving in and out
                    if (strpos($relatedItem['no_work_order'], 'MO') !== 0 && strpos($relatedItem['no_work_order'], 'MI') !== 0) {
                        $relatedWorkOrders[$relatedItem['id_work_order']] = [
                            'id_work_order' => $relatedItem['id_work_order'],
                            'no_work_order' => $relatedItem['no_work_order'],
                            'no_booking' => $relatedItem['no_booking'],
                            'no_reference' => $relatedItem['no_reference'],
                        ];
                    }
                }
            }
        }

        $this->db->trans_start();
        $dataLock = [
            'id_work_order' => $id,
            'date_from' => $date_from,
            'date_to' => $date_to,
            'description' => $description . (!empty($relatedWorkOrders) ? " (Relate with job " . implode(",", array_column($relatedWorkOrders, 'no_work_order')) . " request) [REQUESTED JOB]" : ''),
            'created_at' =>  $create_at,
            'created_by' => UserModel::authenticatedUserData('id'),
            'status' => 'REQUEST',
            'id_branch' => $branch['id'],
            'request_by' => UserModel::authenticatedUserData('id')
        ];
        $this->workOrderLockAutomate->create($dataLock);

        $dataLockHistory = [
            'id_work_order' => $id,
            'description' => $descriptions,
            'status' => 'REQUEST',
            'created_at' => $create_at,
            'created_by' => UserModel::authenticatedUserData('id')
        ];
        $this->workOrderHistoryLock->create($dataLockHistory);

        // request unlock for related job in same data
        if (!empty($relatedWorkOrders)) {
            $relatedWorkOrders[$workOrder['id']] = [
                'id_work_order' => $workOrder['id'],
                'no_work_order' => $workOrder['no_work_order'],
                'no_booking' => $workOrder['no_booking'],
                'no_reference' => $workOrder['no_reference'],
            ];
            foreach ($relatedWorkOrders as $relatedWorkOrder) {
                $relatedOthers = array_filter($relatedWorkOrders, function ($currentWorkOrder) use ($relatedWorkOrder) {
                    return $currentWorkOrder['id_work_order'] != $relatedWorkOrder['id_work_order'];
                });
                if ($relatedWorkOrder['id_work_order'] != $workOrder['id']) {
                    $dataLock['id_work_order'] = $relatedWorkOrder['id_work_order'];
                    $dataLock['description'] = $description . " (Relate with job " . implode(",", array_column($relatedOthers, 'no_work_order')) . " request)";
                    $this->workOrderLockAutomate->create($dataLock);

                    $dataLockHistory['id_work_order'] = $relatedWorkOrder['id_work_order'];
                    $dataLockHistory['description'] = $descriptions . " (Relate with job " . implode(",", array_column($relatedOthers, 'no_work_order')) . " request)";
                    $this->workOrderHistoryLock->create($dataLockHistory);
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            $operational = ['name' => UserModel::authenticatedUserData('name')];
            $workOrder = $this->workOrder->getWorkOrderById($id);
            $request['no_work_order'] = $workOrder['no_work_order'];
            $request['mail_date'] = date("d F Y H:i:s", strtotime($create_at));

            if (!$this->send_email_request($operational, $request, $relatedWorkOrders)) {
                flash('warning', "Unlocked request saved but admin does not get notification, please contact our customer service that you have unlocked tally request");
            } else {
                flash('success', "Unlocked request successfully created");
            }
            redirect('work-order');
        } else {
            flash('danger', "Unlocked request failed, try again or contact administrator");
        }
    }

    /**
     * Send email to administrator.
     * @param $operational
     * @param $workOrder
     * @param $relatedWorkOrders
     * @return bool
     */
    private function send_email_request($operational, $workOrder, $relatedWorkOrders)
    {
        $operationsAdministratorEmail = get_setting('email_finance2');

        $subject =  $operational['name'] . ' request unlock tally with no ' . $workOrder['no_work_order'];

        if (!empty($relatedWorkOrders)) {
            $subject .= ' and ' . count($relatedWorkOrders) . ' related jobs';
        }
        
        $subject .= ' from ' . $workOrder['date_from'] . ' to ' . $workOrder['date_to'];

        $emailTo = $operationsAdministratorEmail;
        $emailTitle = $subject;
        $emailTemplate = 'emails/unlocked_request';
        $emailData = [
            'customer' => $operational,
            'workOrder' => $workOrder,
            'relatedWorkOrders' => $relatedWorkOrders,
            'operationsAdministratorEmail' => $operationsAdministratorEmail
        ];

        $send = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
        if ($send) {
            return true;
        }
        return false;
    }

    public function approve_unlock_tally(){
        $id = $this->input->post('id');
        $id_work_order = $this->input->post('id_work_order');
        $date_from = $this->input->post('date_from_approve');
        $date_to = $this->input->post('date_to_approve');
        $approve_reason = $this->input->post('approve_reason');
        $updated_at = date('Y-m-d H:i:s');

        $descriptions = "Approve Unlocked tally from ".$date_from." to ".$date_to;
        $descriptions = $descriptions."\n Reason : ".$approve_reason;

        $date_from = date("Y-m-d", strtotime($date_from));
        $date_to = date("Y-m-d", strtotime($date_to));

        $this->db->trans_start();

        $this->workOrderLockAutomate->update([
            'date_from' => $date_from,
            'date_to' => $date_to,
            'updated_at' =>  $updated_at,
            'status' => 'APPROVE',
            'approve_by' => UserModel::authenticatedUserData('id')
        ],$id);
        $this->workOrderHistoryLock->create([
            'id_work_order' => $id_work_order,
            'description' => $descriptions,
            'status' => 'APPROVE',
            'created_at' => $updated_at,
            'created_by' => UserModel::authenticatedUserData('id')
        ]);

        $this->unlock_tally();

        $this->db->trans_complete();
        
        redirect('work-order/request-unlock-tally');
    }
    public function unlock_tally() {
        $date_now = date('Y-m-d 00:00:00');
        // $date_now = date('2019-09-25 00:00:00');
        $requests = $this->workOrderLockAutomate->getAllApprove();
        // print_debug($date_now);
        foreach ($requests as $request) {
            $where=[];
            if ($date_now>=$request['date_from'] && $date_now<=$request['date_to'] && $request['is_locked']==1) {
                $where=[
                    'id_branch' => $request['id_branch'],
                    'id_work_order' => $request['id_work_order']
                ];
                $this->workOrder->setLocked([
                    'is_locked' => '0',
                    'locked_by' => 'NULL',
                    'locked_at' => 'NULL'
                ],$where);
                $this->workOrderHistoryLock->create([
                    'id_work_order' => $request['id_work_order'],
                    'description' => 'Unlocked automate',
                    'status' => 'UNLOCKED',
                    'locked_by' => $request['approve_by'],
                    'locked_at' => $date_now,
                    'created_at' => $date_now,
                    'created_by' => $request['approve_by']
                ]);
                // print_debug($where);
            }
        }
    }
    public function reject_unlock_tally(){
        $id = $this->input->post('id');
        $id_work_order = $this->input->post('id_work_order');
        $date_from = $this->input->post('date_from_reject');
        $date_to = $this->input->post('date_to_reject');
        $reject_reason = $this->input->post('reject_reason');
        $updated_at = date('Y-m-d H:i:s');

        $descriptions = "Reject Unlocked tally from ".$date_from." to ".$date_to;
        $descriptions = $descriptions."\n Reason : ".$reject_reason;
      

        $this->workOrderLockAutomate->update([
            'updated_at' =>  $updated_at,
            'status' => 'REJECT'
        ],$id);
        $this->workOrderHistoryLock->create([
            'id_work_order' => $id_work_order,
            'description' => $descriptions,
            'status' => 'REJECT',
            'created_at' => $updated_at,
            'created_by' => UserModel::authenticatedUserData('id')
        ]);
        
        redirect('work-order/request-unlock-tally');
    }
    

    public function request_unlock_tally(){
        $id = UserModel::authenticatedUserData('id');
        if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_LOCK)){
            $requests = $this->workOrderLockAutomate->getAllRequestUnlock();
        }else {
            $requests = $this->workOrderLockAutomate->getRequestUnlockById($id);
        }
        $data = [
            'title' => "Unlock Request",
            'subtitle' => "List Request",
            'page' => "workorder/request_unlock_tally",
            'requests' => $requests
        ];
        $this->load->view('template/layout', $data);
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
     * Get stock by booking customer.
     */
    public function ajax_get_goods_in_container()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $bookingId = $this->input->get('id_booking');
            $id_container = $this->input->get('id_container');

            $stockGoods = $this->booking->getGoodsInContainer([
                'data' => 'stock',
                'bookings' => $bookingId,
                'id_container' => $id_container,
            ]);

            header('Content-Type: application/json');
            echo json_encode($stockGoods);
        }
    }

    /**
     * Ajax get all people data per branch
     */
    public function ajax_search_work_order()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $search = $this->input->get('q');
            $page = $this->input->get('page');

            $workOrders = $this->workOrder->getWorkOrdersByQuery($search, $page);

            echo json_encode($workOrders);
        }
    }
}
