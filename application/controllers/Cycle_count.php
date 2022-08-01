<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Account
 * @property UserModel $user
 * @property UserTokenModel $userToken
 * @property PeopleModel $people
 */
class Cycle_count extends CI_Controller
{
    /**
     * Account constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ReportModel', 'report');
        $this->load->model('ReportStockModel', 'reportStock');
        $this->load->model('BranchModel', 'branch');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('CycleCountModel', 'CycleCount');
        $this->load->model('CycleCountGoodsModel', 'CycleCountGoods');
        $this->load->model('CycleCountContainerModel', 'CycleCountContainer');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
        $this->load->model('LogModel', 'logHistory');
        $this->load->model('HolidayModel', 'holiday');
    }

    
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CYCLE_COUNT_VIEW);

        $cycleCounts = $this->CycleCount->getAllCycleCounts();
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();
        
        $data = [
            'title' => "Cycle Count",
            'page' => "cycle_count/index",
            'cycleCounts' => $cycleCounts,
        ];

        $this->load->view('template/layout', $data);
    }

    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CYCLE_COUNT_VIEW);

        $cycleCounts = $this->CycleCount->getById($id);
        $cycleCountDetails = $this->CycleCountGoods->getCycleCountGoodsById($id);
        $cycleCountContainers = $this->CycleCountContainer->getBy(['cycle_count_containers.id_cycle_count' => $id]);
 
        $data = [
            'title' => "Cycle Count",
            'page' => "cycle_count/view",
            'cycleCounts' => $cycleCounts,
            'cycleCountDetails'=> $cycleCountDetails,
            'cycleCountContainers' => $cycleCountContainers
        ];

        $this->load->view('template/layout', $data);
    }

    public function process($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CYCLE_COUNT_CREATE);

        $cycleCounts = $this->CycleCount->getById($id);
        $cycleCountDetails = $this->CycleCountGoods->getCycleCountGoodsById($id);
        $cycleCountContainers = $this->CycleCountContainer->getBy(['cycle_count_containers.id_cycle_count' => $id]);
        $data = [
                'title' => "Cycle Count",
                'page' => "cycle_count/process",
                'cycleCounts' => $cycleCounts,
                'cycleCountDetails'=> $cycleCountDetails,
                'cycleCountContainers' => $cycleCountContainers
        ];

        $this->load->view('template/layout', $data);
    }

    /**
     * Show result process.
     */
    public function result($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CYCLE_COUNT_RESULT);

        $cycleCounts = $this->CycleCount->getById($id);
        $cycleCountDetails = $this->CycleCountGoods->getCycleCountGoodsById($id);
        $cycleCountContainers = $this->CycleCountContainer->getBy(['cycle_count_containers.id_cycle_count' => $id]);

        $data = [
                'title' => "Cycle Count",
                'page' => "cycle_count/result",
                'cycleCounts' => $cycleCounts,
                'cycleCountDetails'=> $cycleCountDetails,
                'cycleCountContainers' => $cycleCountContainers
        ];

        $this->load->view('template/layout', $data);
    }

    public function save_process($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CYCLE_COUNT_CREATE);

            $cycleCounts = $this->CycleCount->getById($id);
            $cycleCountDetails = $this->CycleCountGoods->getCycleCountGoodsById($id);
            $cycleCountContainers = $this->CycleCountContainer->getBy(['cycle_count_containers.id_cycle_count' => $id]);

            $position = $this->input->post('position');
            $quantity = $this->input->post('quantity');
            $description = $this->input->post('description');
            $photo = $_FILES['photo']['name'];

             // replace only if new photo check uploaded.
            $uploadPassed = true;
            $fileName = '';
            if (!empty($photo)) {
                // delete photo before
                $filePath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . "cycle_count_photo";
                $this->uploadDocumentFile->deleteFile($cycleCounts['photo'], $filePath);

                // re upload photo
                $fileName = 'CN_' . time() . '_' . rand(100, 999);
                $saveTo = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'cycle_count_photo';
                if ($this->documentType->makeFolder('cycle_count_photo')) {
                    $upload = $this->uploadDocumentFile->uploadTo('photo', $fileName, $saveTo);
                    if (!$upload['status']) {
                        $uploadPassed = false;
                        flash('warning', $upload['errors']);
                    } else {
                        $fileName = $upload['data']['file_name'];
                    }
                }else {
                    $uploadPassed = false;
                    flash('warning', 'Making folder upload failed, try again');
                }
            }else{
                $fileName = $cycleCounts['photo'];
            }

          
            $type = CycleCountModel::STATUS_PROCESSED;
            $this->db->trans_start();

            if ($uploadPassed) {

            $update = $this->CycleCount->updateCycleCount([
                    'photo' => $fileName,
                    'status' => $type
            ], $id);

            }

            if($cycleCounts['type'] == "GOODS"){
                foreach($cycleCountDetails as $key => $goods){
                    $update = $this->CycleCountGoods->update([
                        'position_check' => $position[$key],
                        'quantity_check' => $quantity[$key],
                        'description_check' => $description[$key],
                    ], $goods['id']);
                }
            }else{
                foreach($cycleCountContainers as $key => $container){
                    $update = $this->CycleCountContainer->update([
                        'position_check' => $position[$key],
                        'quantity_check' => $quantity[$key],
                        'description_check' => $description[$key],
                    ], $container['id']);
                }

            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Cycle Count {$cycleCounts['branch']} successfully updated");
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }

            redirect('cycle-count');
    }


    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CYCLE_COUNT_CREATE);

        $data = [
                'title' => "Cycle Count",
                'page' => "cycle_count/create",
        ];

        $this->load->view('template/layout', $data);
    }

    public function save()
    {
        $branches = $this->branch->getAll();
        $branchId = get_active_branch('id');
        $request_date = $this->input->post('cycle_count_date');
        $getPublicHoliday = $this->holiday->getAllData();

        if(!is_null($branchId) && (!is_null($request_date))){

            $goodsFromActivity = [];
            $goodsFromActivityOut = []; 
            $containerFromActivity = [];
            $containerFromActivityOut = [];
            $goods_merges = [];
            $container_merges = [];
            $data_random = [];
            $data_random2 = [];

            $type = $this->input->post('type');
            $description = $this->input->post('description');
            $branchbyId = $this->branch->getById($branchId);

            $LastCycleCount = $this->CycleCount->getLastDataByBranchByTypeByActivity($branchId, $type, "ALL");
            $dateDiff = !is_null($LastCycleCount) ?  date_diff(new DateTime($request_date), new Datetime($LastCycleCount['cycle_count_date'])) : false;

            if(date('Y-m-d',strtotime($request_date)) == date('Y-m-d')){
                $date_from = (!is_null($LastCycleCount) && (strtotime($LastCycleCount['cycle_count_date']) > strtotime($request_date))) || is_null($LastCycleCount)  ? $request_date : $LastCycleCount['cycle_count_date'];
                $date_to = !is_null($LastCycleCount) && (strtotime($LastCycleCount['cycle_count_date']) > strtotime($request_date) ) ? $LastCycleCount['cycle_count_date'] : $request_date;
            }else{
                $date_from = $request_date;
                $date_to = $request_date;
            }

            $checkCycleCount = $this->CycleCount->CycleCountCheck($branchId, $type, $request_date);

            if ($type == "GOODS"){ //GOODS
                $activityIn = $this->report->getGoodsStockMove(['date_from' => $date_from, 'date_to' => $date_to, 'multiplier' => 1, 'branch' => $branchId]); // add stock
                $activityOut= $this->report->getGoodsStockMove(['date_from' => $date_from, 'date_to' => $date_to, 'multiplier' => -1, 'branch' => $branchId]); // remove stock
                $activityInOuts = array_merge($activityIn, $activityOut);

                foreach($activityInOuts as $activityInOut){
                    $data = $this->reportStock->getStockGoods([
                        'data' => 'ALL', 
                        'branch' => $activityInOut['id_branch'], 
                        'booking' => $activityInOut['id_booking'],
                        'search' => $activityInOut['goods_name']
                    ]);
                    $goodsFromActivity = array_merge($goodsFromActivity, $data);
                }

                foreach($activityOut as $stockOut){
                    $dataOut = $this->reportStock->getStockGoods([
                        'data' => 'ALL', 
                        'branch' => $stockOut['id_branch'], 
                        'booking' => $stockOut['id_booking'],
                        'search' => $stockOut['goods_name']
                    ]);
                    $goodsFromActivityOut = array_merge($goodsFromActivityOut, $dataOut);
                }

                $duplicateFields = ["id_owner" => array(), "id_booking" => array(), "id_goods" => array(), "id_unit" => array()];
                foreach ($goodsFromActivity as $index => $value) {
                    if(in_array($value['id_owner'], $duplicateFields['id_owner']) && in_array($value['id_booking'], $duplicateFields['id_booking']) && in_array($value['id_goods'], $duplicateFields['id_goods']) && in_array($value['id_unit'], $duplicateFields['id_unit']) )
                    {
                        unset($goodsFromActivity[$index]);
                    }else{
                        array_push($duplicateFields['id_owner'], $value['id_owner']);
                        array_push($duplicateFields['id_booking'], $value['id_booking']);
                        array_push($duplicateFields['id_goods'], $value['id_goods']);
                        array_push($duplicateFields['id_unit'], $value['id_unit']);
                    }
                }

                $totalGoodsFromActivity = count($goodsFromActivity);

                $this->db->trans_start();
                if(empty($checkCycleCount)){
                    if( (($dateDiff != false && $dateDiff->format("%a") >= $branchbyId['cycle_count_day']) || ($dateDiff == false)) && (date('Y-m-d',strtotime($request_date)) == date('Y-m-d')) && ($branchbyId['cycle_count_day'] > 0 && $branchbyId['cycle_count_goods'] > 0) ){
                        $totalGoodsFromAge = $branchbyId['cycle_count_goods'] - $totalGoodsFromActivity;

                        // get random goods from activity
                        $totalRandom = $totalGoodsFromActivity >= $branchbyId['cycle_count_goods'] ? $branchbyId['cycle_count_goods'] : $totalGoodsFromActivity;
                        if ($totalRandom == 1){
                             $random = array_rand($goodsFromActivity, $totalRandom);
                             $data_random[] = $goodsFromActivity[$random];
                        }else
                            if(($totalRandom != 1) && ($totalRandom != 0)){
                            $random = array_rand($goodsFromActivity, $totalRandom);
                            foreach ($random as $key => $value) {
                                $data_random[] = $goodsFromActivity[$value];
                            }
                        }
                        $goods_merges = array_merge($goods_merges, $data_random);

                        // get random goods from age
                        if($totalGoodsFromAge > 0) {
                            $column = 25; //column age
                            $stock_goods = $this->reportStock->getStockGoods([
                                'data' => 'stock',
                                'start' => 0,
                                'length' => 20,
                                'order_by' => $column ,
                                'order_method' => 'ASC',
                                'branch' => $branchId 
                            ]); 

                            $totalGoods = count($stock_goods['data']);
                            if($totalGoods > 0){
                                $random_goods = array_rand($stock_goods['data'], $totalGoods > $totalGoodsFromAge ? $totalGoodsFromAge : $totalGoods);
                                foreach ($random_goods as $key => $value) {
                                    $data_random2[] = $stock_goods['data'][$value];
                                }

                                $goods_merges = array_merge($goods_merges, $data_random2);
                            }
                        }

                        $goods_merges = array_merge($goods_merges, $goodsFromActivityOut);
                        $duplicateFieldOuts = ["id_owner" => array(), "id_booking" => array(), "id_goods" => array(), "id_unit" => array()];
                        foreach ($goods_merges as $key => $val) {
                            if(in_array($val['id_owner'], $duplicateFieldOuts['id_owner']) && in_array($val['id_booking'], $duplicateFieldOuts['id_booking']) && in_array($val['id_goods'], $duplicateFieldOuts['id_goods']) && in_array($val['id_unit'], $duplicateFieldOuts['id_unit']) )
                            {
                                unset($goods_merges[$key]);
                            }else{
                                array_push($duplicateFieldOuts['id_owner'], $val['id_owner']);
                                array_push($duplicateFieldOuts['id_booking'], $val['id_booking']);
                                array_push($duplicateFieldOuts['id_goods'], $val['id_goods']);
                                array_push($duplicateFieldOuts['id_unit'], $val['id_unit']);
                            }
                        }

                        $noCycleCount = $this->CycleCount->getAutoNumberCycleCount();
                        $this->CycleCount->create([
                            'id_branch' => $branchId,
                            'no_cycle_count' => $noCycleCount,
                            'cycle_count_date' =>  $request_date,
                            'type' => $type,
                            'activity_type' => "ALL",
                            'description' => $description,
                            'status' => 'PENDING'
                        ]);
                        $cycleCountID = $this->db->insert_id();

                        if(!empty($goods_merges)){
                            foreach ($goods_merges as $goods_merge){
                                $this->CycleCountGoods->create([
                                    'id_cycle_count' => $cycleCountID,
                                    'id_owner' => $goods_merge['id_owner'],
                                    'id_booking' => $goods_merge['id_booking'],
                                    'id_goods' => $goods_merge['id_goods'],
                                    'id_unit' => $goods_merge['id_unit'],
                                    'id_position' => if_empty($goods_merge['id_position'], null),
                                    'id_position_blocks' => $goods_merge['id_position_blocks'],
                                    'position_block' => $goods_merge['position_blocks'],
                                    'ex_no_container' => $goods_merge['no_container'],
                                    'quantity' => $goods_merge['stock_quantity'],
                                    'tonnage' => $goods_merge['stock_weight'],
                                    'tonnage_gross' => $goods_merge['stock_gross_weight'],
                                    'volume' => $goods_merge['stock_volume'],
                                    'status_danger' => $goods_merge['status_danger'],
                                    'description' => $goods_merge['description'],
                                ]);
                            }
                        }
                    }else{

                        $duplicateFieldOuts = ["id_owner" => array(), "id_booking" => array(), "id_goods" => array(), "id_unit" => array()];
                        foreach ($goodsFromActivityOut as $key => $val) {
                            if(in_array($val['id_owner'], $duplicateFieldOuts['id_owner']) && in_array($val['id_booking'], $duplicateFieldOuts['id_booking']) && in_array($val['id_goods'], $duplicateFieldOuts['id_goods']) && in_array($val['id_unit'], $duplicateFieldOuts['id_unit']) )
                            {
                                unset($goodsFromActivityOut[$key]);
                            }else{
                                array_push($duplicateFieldOuts['id_owner'], $val['id_owner']);
                                array_push($duplicateFieldOuts['id_booking'], $val['id_booking']);
                                array_push($duplicateFieldOuts['id_goods'], $val['id_goods']);
                                array_push($duplicateFieldOuts['id_unit'], $val['id_unit']);
                            }
                        }

                        $noCycleCount = $this->CycleCount->getAutoNumberCycleCount();
                        $this->CycleCount->create([
                            'id_branch' => $branchId,
                            'no_cycle_count' => $noCycleCount,
                            'cycle_count_date' =>  $request_date,
                            'type' => $type,
                            'activity_type' => "OUT",
                            'description' => $description,
                            'status' => 'PENDING'
                        ]);
                        $cycleCountID = $this->db->insert_id();

                        if(!empty($goodsFromActivityOut)){
                            foreach ($goodsFromActivityOut as $goods_out){
                                $this->CycleCountGoods->create([
                                    'id_cycle_count' => $cycleCountID,
                                    'id_owner' => $goods_out['id_owner'],
                                    'id_booking' => $goods_out['id_booking'],
                                    'id_goods' => $goods_out['id_goods'],
                                    'id_unit' => $goods_out['id_unit'],
                                    'id_position' => if_empty($goods_out['id_position'], null),
                                    'id_position_blocks' => $goods_out['id_position_blocks'],
                                    'position_block' => $goods_out['position_blocks'],
                                    'ex_no_container' => $goods_out['no_container'],
                                    'quantity' => $goods_out['stock_quantity'],
                                    'tonnage' => $goods_out['stock_tonnage'],
                                    'tonnage_gross' => $goods_out['stock_tonnage_gross'],
                                    'volume' => $goods_out['stock_volume'],
                                    'status_danger' => $goods_out['status_danger'],
                                    'description' => $goods_out['description'],
                                ]);
                            }
                        }
                    }
                }else{
                    flash('danger', "Cycle count stocks failed, Cycle count goods is already exists!");

                    redirect('cycle-count');
                }

                $this->db->trans_complete();

            }else{ //CONTAINER  

                $activityIn = $this->report->getContainerStockMove(['date_from' => $date_from, 'date_to' => $date_to, 'multiplier' => 1, 'branch' => $branchId]);
                $activityOut= $this->report->getContainerStockMove(['date_from' => $date_from, 'date_to' => $date_to, 'multiplier' => -1, 'branch' => $branchId]);
                $activityInOuts = array_merge($activityIn, $activityOut);

                foreach($activityInOuts as $activityInOut){
                    $dataContainer = $this->reportStock->getStockContainers([
                        'data' => 'ALL', 
                        'branch' => $activityInOut['id_branch'], 
                        'booking' => $activityInOut['id_booking_in'],
                        'search' => $activityInOut['no_container']
                    ]);
                    $containerFromActivity = array_merge($containerFromActivity, $dataContainer);
                }

                foreach($activityOut as $stockOut){
                    $dataOut = $this->reportStock->getStockContainers([
                        'data' => 'ALL', 
                        'branch' => $stockOut['id_branch'], 
                        'booking' => $stockOut['id_booking_in'],
                        'search' => $stockOut['no_container']
                    ]);
                    $containerFromActivityOut = array_merge($containerFromActivityOut, $dataOut);
                }

                $duplicateFields = ["id_owner" => array(), "id_booking" => array(), "id_container" => array()];
                foreach ($containerFromActivity as $index => $value) {
                    if(in_array($value['id_owner'], $duplicateFields['id_owner']) && in_array($value['id_booking'], $duplicateFields['id_booking']) && in_array($value['id_container'], $duplicateFields['id_container'])){
                         unset($containerFromActivity[$index]);
                    }else{
                        array_push($duplicateFields['id_owner'], $value['id_owner']);
                        array_push($duplicateFields['id_booking'], $value['id_booking']);
                        array_push($duplicateFields['id_container'], $value['id_container']);
                    }
                }

                $totalContainerFromActivity = count($containerFromActivity);

                $this->db->trans_start();
                if( empty($checkCycleCount) ){
                    if( (($dateDiff != false && $dateDiff->format("%a") >= $branchbyId['cycle_count_day']) || ($dateDiff == false)) && (date('Y-m-d',strtotime($request_date)) == date('Y-m-d')) && ($branchbyId['cycle_count_day'] > 0 && $branchbyId['cycle_count_container'] > 0) ){

                        $totalContainerFromAge = $branchbyId['cycle_count_container'] - $totalContainerFromActivity;
                        // get random container from activity
                        $totalRandom = $totalContainerFromActivity >= $branchbyId['cycle_count_container'] ? $branchbyId['cycle_count_container'] : $totalContainerFromActivity;
                        if ($totalRandom == 1){
                             $random = array_rand($containerFromActivity, $totalRandom);
                             $data_random[] = $containerFromActivity[$random];
                        }else
                            if(($totalRandom != 1) && ($totalRandom != 0)){
                            $random = array_rand($containerFromActivity, $totalRandom);
                            foreach ($random as $key => $value) {
                                $data_random[] = $containerFromActivity[$value];
                            }
                        }
                        $container_merges = array_merge($container_merges, $data_random);

                        // get random container from age
                        if($totalContainerFromAge > 0) {
                            $column = 15;

                            $stock_container= $this->reportStock->getStockContainers([
                                'data' => 'stock',
                                'start' => 0,
                                'length' => 20,
                                'order_by' => $column ,
                                'order_method' => 'ASC',
                                'branch' => $branchId 
                            ]); 

                            $totalContainer = count($stock_container['data']);
                            if($totalContainer > 0){
                                $random_container = array_rand($stock_container['data'], $totalContainer > $totalContainerFromAge ? $totalContainerFromAge : $totalContainer);
                                foreach ($random_container as $key => $value) {
                                    $data_random2[] = $stock_container['data'][$value];
                                }

                                $container_merges = array_merge($container_merges, $data_random2);
                            }
                        }

                        $container_merges = array_merge($container_merges, $containerFromActivityOut);
                        $duplicateFieldOuts = ["id_owner" => array(), "id_booking" => array(), "id_container" => array()];
                        foreach ($container_merges as $key => $val) {
                            if(in_array($val['id_owner'], $duplicateFieldOuts['id_owner']) && in_array($val['id_booking'], $duplicateFieldOuts['id_booking']) && in_array($val['id_container'], $duplicateFieldOuts['id_container']) )
                            {
                                unset($container_merges[$key]);
                            }else{
                                array_push($duplicateFieldOuts['id_owner'], $val['id_owner']);
                                array_push($duplicateFieldOuts['id_booking'], $val['id_booking']);
                                array_push($duplicateFieldOuts['id_container'], $val['id_container']);
                            }
                        }

                        $noCycleCount = $this->CycleCount->getAutoNumberCycleCount();
                        $this->CycleCount->create([
                            'id_branch' => $branchId,
                            'no_cycle_count' => $noCycleCount,
                            'cycle_count_date' =>  $request_date,
                            'type' => $type,
                            'activity_type' => "ALL",
                            'description' => $description,
                            'status' => 'PENDING'
                        ]);

                        $cycleCountID = $this->db->insert_id();

                        if(!empty($container_merges)){
                            foreach ($container_merges as $container_merge){
                                $this->CycleCountContainer->create([
                                    'id_cycle_count' => $cycleCountID,
                                    'id_owner' => $container_merge['id_owner'],
                                    'id_booking' => $container_merge['id_booking'],
                                    'id_container' => $container_merge['id_container'],
                                    'id_position' => if_empty($container_merge['id_position'], null),
                                    'id_position_blocks' => $container_merge['id_position_blocks'],
                                    'position_block' => $container_merge['position_blocks'],
                                    'quantity' => $container_merge['stock'],
                                    'no_reference' => $container_merge['no_reference'],
                                    'seal' => $container_merge['seal'],
                                    'status_danger' => $container_merge['status_danger'],
                                    'description' => $container_merge['description'],
                                ]);
                            }
                        }
                    }else{

                        $duplicateFieldOuts = ["id_owner" => array(), "id_booking" => array(), "id_container" => array()];
                        foreach ($containerFromActivityOut as $key => $val) {
                            if(in_array($val['id_owner'], $duplicateFieldOuts['id_owner']) && in_array($val['id_booking'], $duplicateFieldOuts['id_booking']) && in_array($val['id_container'], $duplicateFieldOuts['id_container']) )
                            {
                                unset($containerFromActivityOut[$key]);
                            }else{
                                array_push($duplicateFieldOuts['id_owner'], $val['id_owner']);
                                array_push($duplicateFieldOuts['id_booking'], $val['id_booking']);
                                array_push($duplicateFieldOuts['id_container'], $val['id_container']);
                            }
                        }

                        $noCycleCount = $this->CycleCount->getAutoNumberCycleCount();
                        $this->CycleCount->create([
                            'id_branch' => $branchId,
                            'no_cycle_count' => $noCycleCount,
                            'cycle_count_date' =>  $request_date,
                            'type' => $type,
                            'activity_type' => "OUT",
                            'description' => $description,
                            'status' => 'PENDING'
                        ]);
                        $cycleCountID = $this->db->insert_id();

                        if(!empty($containerFromActivityOut)){
                            foreach ($containerFromActivityOut as $container_out){
                                $this->CycleCountContainer->create([
                                    'id_cycle_count' => $cycleCountID,
                                    'id_owner' => $container_out['id_owner'],
                                    'id_booking' => $container_out['id_booking'],
                                    'id_container' => $container_out['id_container'],
                                    'id_position' => if_empty($container_out['id_position'], null),
                                    'id_position_blocks' => $container_out['id_position_blocks'],
                                    'position_block' => $container_out['position_blocks'],
                                    'quantity' => $container_out['stock'],
                                    'no_reference' => $container_out['no_reference'],
                                    'seal' => $container_out['seal'],
                                    'status_danger' => $container_out['status_danger'],
                                    'description' => $container_out['description'],
                                ]);
                            }
                        }
                    }

                }else{
                    flash('danger', "Cycle count stocks failed, Cycle count container is already exists!");

                    redirect('cycle-count');
                }

                $this->db->trans_complete();
            }
            
        }else{
            flash('danger', "Cycle Count stocks failed, Please select specific branch!");

            redirect('cycle-count');
        }


        if ($this->db->trans_status()) {
            flash('warning', "Cycle Count successfully generated");
        } else {
            flash('danger', "Cycle Count failed, try again or contact administrator");
        }

        redirect('cycle-count');
    }

    /**
     * Perform deleting data cycle count.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CYCLE_COUNT_DELETE);

        $cycleCount = $this->CycleCount->getById($id);

        if ($this->CycleCount->delete($id)) {
            flash('warning', "Cycle Count {$cycleCount['branch']} successfully deleted");
        } else {
            flash('danger', "Delete Checklist {$cycleCount['branch']} failed");
        }

        redirect('cycle-count');
    }

    /**
     * Print cycle count data.
     *
     * @param $id
     */
    public function print($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CYCLE_COUNT_PRINT);

        $cycleCounts = $this->CycleCount->getById($id);
        $cycleCountDetails = $this->CycleCountGoods->getCycleCountGoodsById($id);
        $cycleCountContainers = $this->CycleCountContainer->getBy(['cycle_count_containers.id_cycle_count' => $id]);
        
        $data = [
                'title' => "Cycle Count",
                'page' => "cycle_count/_print",
                'cycleCounts' => $cycleCounts,
                'cycleCountDetails'=> $cycleCountDetails,
                'cycleCountContainers' => $cycleCountContainers
        ];

        $this->load->view('template/print_invoice', $data);
      
    }

    /**
     * Print cycle count data.
     *
     * @param $id
     */
    public function print_result($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CYCLE_COUNT_PRINT);

        $cycleCounts = $this->CycleCount->getById($id);
        $cycleCountDetails = $this->CycleCountGoods->getCycleCountGoodsById($id);
        $cycleCountContainers = $this->CycleCountContainer->getBy(['cycle_count_containers.id_cycle_count' => $id]);
        
        $data = [
                'title' => "Cycle Count",
                'page' => "cycle_count/_print_result",
                'cycleCounts' => $cycleCounts,
                'cycleCountDetails'=> $cycleCountDetails,
                'cycleCountContainers' => $cycleCountContainers
        ];

        $this->load->view('template/print_invoice', $data);
      
    }

     /**
     * Validate process cycle count.
     *
     * @param $type
     * @param $id
     */
    public function validate($type, $id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CYCLE_COUNT_VALIDATE);


            $this->db->trans_start();

            $CycleCount = $this->CycleCount->getById($id);

            if ($type == 'reopen') {
                $type = CycleCountModel::STATUS_REOPENED;
            } else {
                $type = CycleCountModel::STATUS_CLOSED;
            }

            $this->CycleCount->update([
                'status' => $type,
                'validated_by' => UserModel::authenticatedUserData('id'),
                'validated_at' => sql_date_format('now')
            ], $id);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Cycle count date {$CycleCount['cycle_count_date']} is successfully {$type}");
            } else {
                flash('danger', 'Validating cycle count failed');
            }
      
            if (!empty(get_url_param('redirect'))) {
                redirect(get_url_param('redirect'), false);
            } else {
                redirect('cycle-count');
            }
    }

         

}