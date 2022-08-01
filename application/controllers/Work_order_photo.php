<?php

use Intervention\Image\Image;
use Intervention\Image\ImageManager;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Work_order_photo
 * @property WorkOrderGoodsModel $workOrderGoods
 * @property WorkOrderGoodsPhotoModel $workOrderGoodsPhoto
 * @property HandlingTypeModel $handlingType
 * @property PeopleModel $people
 * @property PalletMarkingHistoryModel $palletMarkingHistory
 * @property WorkOrderHistoryModel $workOrderHistory
 * @property WorkOrderPhotoModel $workOrderPhoto
 * @property WorkOrderComponentModel $workOrderComponent
 * @property WorkOrderContainerModel $workOrderContainer
 * @property WorkOrderHistoryLockModel $workOrderHistoryLock
 * @property ComponentModel $component
 * @property WorkOrderComponentHeavyEquipmentsModel $workOrderComponentHeavyEquipments
 * @property WorkOrderComponentHeepsModel $workOrderComponentHeeps
 * @property GuestModel $guest
 * @property WorkOrderComponentLaboursModel $workOrderComponentLabours
 * @property GoodsModel $goods
 * @property HeavyEquipmentModel $heavyEquipment
 * @property Uploader $uploader
 */
class Work_order_photo extends MY_Controller
{
    /**
     * Work_order_photo constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('WorkOrderGoodsPhotoModel', 'workOrderGoodsPhoto');
        $this->load->model('modules/Uploader', 'uploader');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('HandlingTypeModel', 'handlingType');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('WorkOrderHistoryModel', 'workOrderHistory');
        $this->load->model('PalletMarkingHistoryModel', 'palletMarkingHistory');
        $this->load->model('WorkOrderPhotoModel', 'workOrderPhoto');
        $this->load->model('WorkOrderComponentModel', 'workOrderComponent');
        $this->load->model('WorkOrderContainerModel', 'workOrderContainer');
        $this->load->model('WorkOrderHistoryLockModel', 'workOrderHistoryLock');
        $this->load->model('ComponentModel', 'component');
        $this->load->model('WorkOrderComponentHeavyEquipmentsModel', 'workOrderComponentHeavyEquipments');
        $this->load->model('WorkOrderComponentHeepsModel', 'workOrderComponentHeeps');
        $this->load->model('GuestModel', 'guest');
        $this->load->model('WorkOrderComponentLaboursModel', 'workOrderComponentLabours');
        $this->load->model('GoodsModel', 'goods');
        $this->load->model('HeavyEquipmentModel', 'heavyEquipment');

        $this->setFilterMethods([
            'ajax_photos' => 'GET',
            'edit_by_tally' => 'GET',
            'save_by_tally' => 'POST',
        ]);
    }

    /**
     * Show index data work order.
     */
    public function index()
    {
        $filter = get_url_param('filter_work_order_photo') ? $_GET : [];

        if (get_url_param('export')) {
            $this->load->model('modules/Exporter', 'exporter');
            $this->exporter->exportLargeResourceFromArray("Work orders", $this->workOrder->getAllWorkOrderPhoto($filter));
        } else {
            $selectedCustomers = key_exists('customer', $filter) ? $filter['customer'] : [0];
            $selectedHandlingTypes = key_exists('handling_type', $filter) ? $filter['handling_type'] : [0];
            $selectedPeoples = key_exists('people', $filter) ? $filter['people'] : [0];
            $selectedGoods = key_exists('goods', $filter) ? $filter['goods'] : [0];

            $customers = $this->people->getById($selectedCustomers);
            $handlingTypes = $this->handlingType->getHandlingTypeById($selectedHandlingTypes);
            $peoples = $this->people->getById($selectedPeoples);
            $goods = $this->goods->getById($selectedGoods);

            $data = [
                'title' => "Work Order",
                'subtitle' => "History of job",
                'page' => "workorder_photo/index",
                'customers' => $customers,
                'handlingTypes' => $handlingTypes,
                'peoples' => $peoples,
                'goods' => $goods,
            ];

            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable.
     */
    public function data()
    {
        $filters = array_merge(get_url_param('filter_work_order_photo') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);
        $workOrders = $this->workOrder->getAllWorkOrderPhoto($filters);
        
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

        $data = [
            'title' => "Tally",
            'subtitle' => "Data job order",
            'page' => "workorder_photo/view",
            'workOrder' => $workOrder,
            'containers' => $containers,
            'goods' => $goods,
            'components' => $components,
            'workOrderHistories' => $workOrderHistories,
            'workOrderPhotos' => $workOrderPhotos,
            'workOrderStatuses' => $workOrderStatuses,
            'lockHistories' => $lockHistories,
            'component_data' => $component_data,
        ];

        $this->load->view('template/layout', $data);
    }
}