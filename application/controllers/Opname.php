<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Opname
 * @property OpnameModel $opname
 * @property OpnameGoodsModel $opnameGoods
 * @property OpnameContainerModel $opnameContainer
 * @property ReportStockModel $reportStock
 * @property SettingModel $setting
 * @property Exporter $exporter
 * @property Importer $importer
 * @property Uploader $uploader
 */
class Opname extends CI_Controller
{
    /**
     * Opname constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('OpnameModel', 'opname');
        $this->load->model('OpnameGoodsModel', 'opnameGoods');
        $this->load->model('OpnameContainerModel', 'opnameContainer');
        $this->load->model('ReportStockModel', 'reportStock');
        $this->load->model('SettingModel', 'setting');
        $this->load->model('BranchModel', 'branch');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
        $this->load->model('HolidayModel', 'holiday');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('modules/Importer', 'importer');
        $this->load->model('modules/Uploader', 'uploader');
    }

    /**
     * Show opname data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_VIEW);

        $data = [
            'title' => "Opnames",
            'subtitle' => "Opname Data",
            'page' => "opname/index",
            'opnames' => $this->opname->getAll()
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show view opname form.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_VIEW);

        $opname = $this->opname->getById($id);
        $opnameStocks = $this->opnameGoods->getBy(['opname_goods.id_opname' => $id]);
        $opnameContainers = $this->opnameContainer->getBy(['opname_containers.id_opname' => $id]);

        $data = [
            'title' => "Opnames",
            'subtitle' => "View Opname",
            'page' => "opname/view",
            'opname' => $opname,
            'opnameStocks' => $opnameStocks,
            'opnameContainers' => $opnameContainers
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show process opname form.
     */
    public function process($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_PROCESS);

        $opname = $this->opname->getById($id);
        $opnameStocks = $this->opnameGoods->getBy(['opname_goods.id_opname' => $id]);
        $opnameContainers = $this->opnameContainer->getBy(['opname_containers.id_opname' => $id]);

        $data = [
            'title' => "Opnames",
            'subtitle' => "Process Opname",
            'page' => "opname/process",
            'opname' => $opname,
            'opnameStocks' => $opnameStocks,
            'opnameContainers' => $opnameContainers
        ];
        $this->load->view('template/layout', $data);
    }

     /**
     * Save procces.
     */
    public function save_process($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_PROCESS);

        $opname = $this->opname->getById($id);
        $opnameGoods = $this->opnameGoods->getBy(['opname_goods.id_opname' => $id]);
        $opnameContainers = $this->opnameContainer->getBy(['opname_containers.id_opname' => $id]);
        $type = OpnameModel::STATUS_PROCESSED;

        if(empty($_POST)){
            $update = $this->opname->update([
                'status' => $type
            ], $id);

        }else{
            $position = $this->input->post('position');
            $quantity = $this->input->post('quantity');
            $description = $this->input->post('description');
            $photo = $_FILES['photo']['name'];

             // replace only if new photo check uploaded.
            $uploadPassed = true;
            $fileName = '';
            if (!empty($photo)) {
                // delete photo before
                $filePath = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . "opname_photo";
                if(!empty($opname['photo_check'])){
                    $this->uploadDocumentFile->deleteFile($opname['photo_check'], $filePath);
                }

                // re upload photo
                $fileName = 'CN_' . time() . '_' . rand(100, 999);
                $saveTo = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'opname_photo';
                if ($this->documentType->makeFolder('opname_photo')) {
                    $upload = $this->uploadDocumentFile->uploadTo('photo', $fileName, $saveTo);
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
            }else{
                $fileName = $opname['photo_check'];
            }

            $this->db->trans_start();
            
            if ($uploadPassed == true) {
                $update = $this->opname->update([
                        'photo_check' => $fileName,
                        'status' => $type
                ], $id);
            }
            
            if($opname['opname_type'] == "GOODS"){
                foreach($opnameGoods as $key => $opnameItem){
                    $qty = $quantity[$key] == null ? null : $quantity[$key];
                    $update = $this->opnameGoods->update([
                        'position_check' => $position[$key],
                        'quantity_check' => $qty,
                        'description_check' => $description[$key],
                    ], $opnameItem['id']);
                }
            }else{
                foreach($opnameContainers as $key => $container){
                    $qty = $quantity[$key] == null ? null : $quantity[$key];
                    $update = $this->opnameContainer->update([
                        'position_check' => $position[$key],
                        'quantity_check' => $qty,
                        'description_check' => $description[$key],
                    ], $container['id']);
                }

            }

            $this->db->trans_complete();

        }



        if ($this->db->trans_status()) {
            flash('success', "Opname {$opname['branch']} successfully updated", 'opname');
        } else {
            flash('danger', 'Something is getting wrong, try again or contact administrator');
        }
        $this->load->view('template/layout', $data);
    }


    /**
     * Complete status opname.
     */
    public function edit_status($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_EDIT);
        $opname = $this->opname->getById($id);
        $opnameGoods = $this->opnameGoods->getBy(['opname_goods.id_opname' => $id]);
        $opnameContainers = $this->opnameContainer->getBy(['opname_containers.id_opname' => $id]);
        $type = OpnameModel::STATUS_COMPLETED;

        if($opname['opname_type'] == "GOODS"){
            $getPositionCheck = in_array(null, array_column($opnameGoods, "position_check"));
            $getQuantityCheck = in_array(null, array_column($opnameGoods, "quantity_check"));

            if( $getPositionCheck == true || $getQuantityCheck == true){
                flash('danger', "Opname {$opname['no_opname']} complete failed, Please complete the form opname!", 'opname');
            }else{
                $update = $this->opname->update([
                'status' => $type,
                'completed_at' => date('Y-m-d H:i:s')
                ], $id);

                if ($update == true) {
                    flash('success', "Opname {$opname['no_opname']} successfully completed", 'opname');
                } else {
                    flash('danger', "Update Opname {$opname['no_opname']} failed, try again or contact administrator", 'opname');
                }
            }

        }else{
            $getPositionCheck = in_array(null, array_column($opnameContainers, "position_check"));
            $getQuantityCheck = in_array(null, array_column($opnameContainers, "quantity_check"));

            if( $getPositionCheck == true || $getQuantityCheck == true){
                flash('danger', "Opname {$opname['no_opname']} complete failed, Please complete the form opname!", 'opname');
            }else{
                $update = $this->opname->update([
                'status' => $type,
                'completed_at' => date('Y-m-d H:i:s')
                ], $id);

                if ($update == true) {
                    flash('success', "Opname {$opname['no_opname']} successfully completed", 'opname');
                } else {
                    flash('danger', "Update Opname {$opname['no_opname']} failed, try again or contact administrator", 'opname');
                }
            }
        }
    }

    /**
     * Show result process.
     */
    public function result($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_VIEW_RESULT);

        $opname = $this->opname->getById($id);
        $opnameStocks = $this->opnameGoods->getBy(['opname_goods.id_opname' => $id]);
        $opnameContainers = $this->opnameContainer->getBy(['opname_containers.id_opname' => $id]);

        $this->calculateAccuracy($opname, $opname['opname_type'] == 'GOODS' ? $opnameStocks : $opnameContainers);

        $data = [
            'title' => "Opnames",
            'subtitle' => "Result Opname",
            'page' => "opname/result",
            'opname' => $opname,
            'opnameStocks' => $opnameStocks,
            'opnameContainers' => $opnameContainers
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show create opname form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_CREATE);

        $data = [
            'title' => "Opnames",
            'subtitle' => "Create Opname",
            'page' => "opname/create",
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Save new opname.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_CREATE);
        $branches = $this->branch->getAll();

        $branchId = get_active_branch('id');
        $opnameDate = sql_date_format($this->input->post('opname_date'));
        $getPublicHoliday = $this->holiday->getAllData();

        if(!is_null($branchId) && !is_null($opnameDate)){
            $StockMerges = [];
            
            $branch = $this->branch->getById($branchId);
            $description = $this->input->post('description');
            $opname_type = $this->input->post('opname_type');
            $LastOpname = $this->opname->getLastOpname($branchId, $opname_type);
            if($opname_type == "GOODS" && empty($LastOpname)){
                $LastOpname = $this->opname->getLastOpname($branchId, "CONTAINER");
            }
            if($opname_type == "CONTAINER" && empty($LastOpname)){
                $LastOpname = $this->opname->getLastOpname($branchId, "GOODS");
            }

            $opnameCheck = $this->opname->opnameCheck($branchId,$opname_type,$opnameDate);
            $check = !is_null($LastOpname) ? $dateDiff = date_diff(new DateTime($opnameDate), new Datetime($LastOpname['opname_date'])) : $dateDiff = null;
            $diff_date = $dateDiff == null ? 0 : $dateDiff->format("%a");

            $this->db->trans_start();
            if(!is_null($branch['opname_day_name'])){
                if( ( (date('l',strtotime($opnameDate))) == $branch['opname_day_name']) && ( $branch['opname_day'] > 0 ) ) {
                    if(empty($opnameCheck) || is_null($opnameCheck)){
                        if( ((date('Y-m-d',strtotime($opnameDate))) != date('Y-m-d')) && ((date('l',strtotime($opnameDate))) == $branch['opname_day_name']) ){
                                $noOpname = $this->opname->getAutoNumberOpname();
                                $this->opname->create([
                                    'id_branch' => $branchId,
                                    'no_opname' => $noOpname,
                                    'opname_date' => $opnameDate,
                                    'description' => $description,
                                    'opname_type' =>$opname_type,
                                    'description_check' => "Late stock opname",
                                    'status' => 'PENDING'
                                ]);
                        }else{

                            if( ( (!empty($LastOpname)) && ($diff_date >= $branch['opname_day']) && (date('l',strtotime($opnameDate))) == $branch['opname_day_name']) || 
                                ( (empty($LastOpname))  && ((date('l',strtotime($opnameDate))) == $branch['opname_day_name'])) ){

                                if($opname_type == "GOODS"){
                                    $StockGoods = $this->reportStock->getStockGoods([
                                    'data' => 'all', 
                                    'branch' => $branchId,
                                    'quantity' => 1,
                                    ]);
                                    $StockMerges = array_merge($StockMerges, $StockGoods);
                                }else{
                                    $StockContainers = $this->reportStock->getStockContainers([
                                    'data' => 'all', 
                                    'branch' => $branchId,
                                    'quantity' => 1,
                                    ]);
                                    $StockMerges = array_merge($StockMerges, $StockContainers);
                                }

                                if(empty($StockMerges)){
                                    $noOpname = $this->opname->getAutoNumberOpname();
                                    $this->opname->create([
                                        'id_branch' => $branchId,
                                        'no_opname' => $noOpname,
                                        'opname_date' => $opnameDate,
                                        'description' => $description,
                                        'opname_type' => $opname_type,
                                        'description_check' => "There is no stock",
                                        'status' => 'PENDING'
                                    ]);

                                }else{
                                    $noOpname = $this->opname->getAutoNumberOpname();
                                    $this->opname->create([
                                        'id_branch' => $branchId,
                                        'no_opname' => $noOpname,
                                        'opname_date' => $opnameDate,
                                        'description' => $description,
                                        'opname_type' =>$opname_type,
                                        'status' => 'PENDING'
                                    ]);
                                    $opnameId = $this->db->insert_id();
                                    if($opname_type == "GOODS"){
                                        foreach($StockMerges as $stockGoodsMerge){
                                        $this->opnameGoods->create([
                                            'id_opname' => $opnameId,
                                            'id_owner' => $stockGoodsMerge['id_owner'],
                                            'id_booking' => $stockGoodsMerge['id_booking'],
                                            'id_goods' => $stockGoodsMerge['id_goods'],
                                            'id_unit' => $stockGoodsMerge['id_unit'],
                                            'id_position' => if_empty($stockGoodsMerge['id_position'], null),
                                            'id_position_blocks' => $stockGoodsMerge['id_position_blocks'],
                                            'position_block' => $stockGoodsMerge['position_blocks'],
                                            'no_pallet' => $stockGoodsMerge['no_pallet'],
                                            'no_reference' => $stockGoodsMerge['no_reference'],
                                            'ex_no_container' => $stockGoodsMerge['ex_no_container'],
                                            'quantity' => $stockGoodsMerge['stock_quantity'],
                                            'tonnage' => $stockGoodsMerge['stock_weight'],
                                            'tonnage_gross' => $stockGoodsMerge['stock_gross_weight'],
                                            'volume' => $stockGoodsMerge['stock_volume'],
                                            'status_danger' => $stockGoodsMerge['status_danger'],
                                            'description' => $stockGoodsMerge['description'],
                                        ]);
                                        }

                                    }else{
                                        foreach($StockMerges as $stockContainerMerge){
                                        $this->opnameContainer->create([
                                            'id_opname' => $opnameId,
                                            'id_owner' => $stockContainerMerge['id_owner'],
                                            'id_booking' => $stockContainerMerge['id_booking'],
                                            'id_container' => $stockContainerMerge['id_container'],
                                            'id_position' => if_empty($stockContainerMerge['id_position'], null),
                                            'id_position_blocks' => $stockContainerMerge['id_position_blocks'],
                                            'position_block' => $stockContainerMerge['position_blocks'],
                                            'quantity' => $stockContainerMerge['stock'],
                                            'no_reference' => $stockContainerMerge['no_reference'],
                                            'seal' => $stockContainerMerge['seal'],
                                            'status_danger' => $stockContainerMerge['status_danger'],
                                            'description' => $stockContainerMerge['description'],
                                        ]);
                                        }
                                    }
                                   
                                }
                            }else{
                                flash('danger', "Opname stocks failed, Opname day setting is mismatched");

                                redirect('opname');
                            }
                        }
                    }else{
                        flash('danger', "Opname stocks failed, Opname data is already exists");

                        redirect('opname');

                    }
                }else{
                    flash('danger', "Opname stocks failed, Please set opname day in branch setting or contact administrator");

                    redirect('opname');
                }
            }

            $this->db->trans_complete();

        }else{
            flash('danger', "Opname stocks failed, Please select specific branch!");

            redirect('opname');
        }

        if ($this->db->trans_status()) {
            flash('warning', "Opname stocks successfully generated");
        } else {
            flash('danger', "Opname stocks failed, try again or contact administrator");
        }

        redirect('opname');
    }

    /**
     * Print booking data.
     * @param $opnameId
     */
    public function print_opname($opnameId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_PRINT);

        $opname = $this->opname->getById($opnameId);
        $opnameStocks = $this->opnameGoods->getBy(['opname_goods.id_opname' => $opnameId]);
        $opnameContainers = $this->opnameContainer->getBy(['opname_containers.id_opname' => $opnameId]);

        $barcode = new \Milon\Barcode\DNS2D();
        $barcode->setStorPath(APPPATH . "/cache/");
        $opnameBarcode = $barcode->getBarcodePNG($opname['no_opname'], "QRCODE", 5, 5);

        $data = [
            'title' => 'Print Opname',
            'page' => 'opname/print_opname',
            'opname' => $opname,
            'opnameStocks' => $opnameStocks,
            'opnameBarcode' => $opnameBarcode,
            'opnameContainers' => $opnameContainers
        ];
        $this->load->view('template/print_invoice', $data);
    }

    /**
     * Download opname data.
     * @param $opnameId
     */
    public function download_opname($opnameId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_PRINT);

        $opname = $this->opname->getById($opnameId);

        $opnameData = null;
        if ($opname['opname_type'] == "GOODS") {
            $goods = $this->opnameGoods->getBy(['opname_goods.id_opname' => $opnameId]);
            $opnameData = array_map(function ($data) {
                return  [
                    'related_id' => $data['id'],
                    'no_pallet' => $data['no_pallet'],
                    'name' => $data['name'],
                    'no_reference' => $data['no_reference'],
                    'no_goods' => $data['no_goods'],
                    'name_goods' => $data['name_goods'],
                    'ex_no_container' => $data['ex_no_container'],
                    'unit' => $data['unit'],
                    'position' => $data['position'],
                    'position_check' => '',
                    'quantity_check' => '',
                    'description_check' => '',
                ];
            }, $goods);
        } else {
            $containers = $this->opnameContainer->getBy(['opname_containers.id_opname' => $opnameId]);
            $opnameData = array_map(function ($data) {
                return  [
                    'related_id' => $data['id'],
                    'name' => $data['name'],
                    'no_reference' => $data['no_reference'],
                    'no_container' => $data['no_container'],
                    'seal' => $data['seal'],
                    'position' => $data['position'],
                    'position_check' => '',
                    'quantity_check' => '',
                    'description_check' => '',
                ];
            }, $containers);
        }
        $no_reference = preg_replace("/[^0-9]+/", "", array_column($opnameData, "no_reference"));
        ARRAY_MULTISORT($no_reference, SORT_ASC, $opnameData);

        $this->exporter->exportFromArray('Opname-' . $opname['opname_type'] . '-' . url_title($opname['no_opname']), $opnameData);
    }

    /**
     * Upload opname result
     * @param $id
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function upload_opname($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_PROCESS);

        $opname = $this->opname->getById($id);

        if (_is_method('post')) {
            $uploadedFile = $opname['file_check'];
            if ($this->uploader->uploadTo('opname_result', [
                'destination' => 'opname_file'
                ])) {
                $result = $this->uploader->getUploadedData();
                $uploadedFile = $result['file_name'];
                $opnameData = $this->importer->importFrom($result['full_path']);

                if (!empty($opnameData)) {
                    if ($opname['opname_type'] == 'GOODS' && !key_exists('NO GOODS', $opnameData[0])) {
                        flash('danger', 'You upload invalid opname GOODS TYPE', 'opname/upload-opname/' . $id);
                    } else if ($opname['opname_type'] == 'CONTAINER' && !key_exists('NO CONTAINER', $opnameData[0])) {
                        flash('danger', 'You upload invalid opname CONTAINER TYPE', 'opname/upload-opname/' . $id);
                    }

                    foreach ($opnameData as $opnameItem) {
                        if (key_exists('POSITION CHECK', $opnameItem) && key_exists('QUANTITY CHECK', $opnameItem) && key_exists('RELATED ID', $opnameItem)) {
                            if (trim($opnameItem['POSITION CHECK']) == '' || trim($opnameItem['QUANTITY CHECK']) == '' || trim($opnameItem['DESCRIPTION CHECK']) == '') {
                                flash('danger', 'All position, quantity and description must be filled', 'opname/upload-opname/' . $id);
                            }
                        } else {
                            flash('danger', 'Column quantity check, position check and related id must exist in excel table', 'opname/upload-opname/' . $id);
                        }
                    }

                    $uploadedPhoto = $opname['photo_check'];
                    if (!empty($_FILES['photo']['name'])) {
                        $uploadFile = $this->uploader->uploadTo('photo', [
                            'destination' => 'opname_photo'
                        ]);
                        if ($uploadFile) {
                            $uploadedData = $this->uploader->getUploadedData();
                            $uploadedPhoto = $uploadedData['file_name'];
                        } else {
                            flash('danger', $this->uploader->getDisplayErrors(), 'opname/upload-opname/' . $id);
                        }
                    }

                    $this->db->trans_start();

                    $this->opname->update([
                        'status' => OpnameModel::STATUS_PROCESSED,
                        'photo_check' => $uploadedPhoto,
                        'file_check' => $uploadedFile,
                        'is_import' => 1
                    ], $id);

                    foreach ($opnameData as $opnameItem) {

                        if ($opname['opname_type'] == 'GOODS') {
                            $opnameGoods = $this->opnameGoods->getById($opnameItem['RELATED ID']);

                            if(empty($opnameGoods)){
                                $data = [
                                    'id_opname' => $id,
                                    'position_check' => $opnameItem['POSITION CHECK'],
                                    'quantity_check' => $opnameItem['QUANTITY CHECK'],
                                    'description_check' => $opnameItem['DESCRIPTION CHECK'],
                                ];
                                $this->opnameGoods->create($data);
                            }else{
                                $data = [
                                    'position_check' => $opnameItem['POSITION CHECK'],
                                    'quantity_check' => $opnameItem['QUANTITY CHECK'],
                                    'description_check' => $opnameItem['DESCRIPTION CHECK'],
                                ];
                                $this->opnameGoods->update($data, $opnameItem['RELATED ID']);
                            }
                        } else {
                            $opnameContainer = $this->opnameContainer->getById($opnameItem['RELATED ID']);

                            if(empty($opnameContainer)){
                                $data = [
                                    'id_opname' => $id,
                                    'position_check' => $opnameItem['POSITION CHECK'],
                                    'quantity_check' => $opnameItem['QUANTITY CHECK'],
                                    'description_check' => $opnameItem['DESCRIPTION CHECK'],
                                ];
                                $this->opnameContainer->create($data);
                            }else{
                                $data = [
                                    'position_check' => $opnameItem['POSITION CHECK'],
                                    'quantity_check' => $opnameItem['QUANTITY CHECK'],
                                    'description_check' => $opnameItem['DESCRIPTION CHECK'],
                                ];
                                $this->opnameContainer->update($data, $opnameItem['RELATED ID']);
                            }
                        }
                    }

                    $this->db->trans_complete();

                    if ($this->db->trans_status()) {
                        flash('success', "Opname {$opname['no_opname']} successfully proceed", 'opname');
                    } else {
                        flash('danger', 'Something is getting wrong, try again or contact administrator');
                    }
                } else {
                    flash('danger', 'Data opname is empty');
                }
            } else {
                flash('danger', $this->uploader->getDisplayErrors(), 'opname/upload-opname/' . $id);
            }
        } else {
            $opname = $this->opname->getById($id);
            $opnameStocks = $this->opnameGoods->getBy(['opname_goods.id_opname' => $id]);
            $opnameContainers = $this->opnameContainer->getBy(['opname_containers.id_opname' => $id]);

            $data = [
                'title' => "Opnames",
                'subtitle' => "Upload Opname",
                'page' => "opname/upload",
                'opname' => $opname,
                'opnameStocks' => $opnameStocks,
                'opnameContainers' => $opnameContainers
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Print booking data.
     * @param $opnameId
     */
    public function print_opname_result($opnameId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_PRINT_RESULT);

        $opname = $this->opname->getById($opnameId);
        $opnameStocks = $this->opnameGoods->getBy(['opname_goods.id_opname' => $opnameId]);
        $opnameContainers = $this->opnameContainer->getBy(['opname_containers.id_opname' => $opnameId]);
        $this->calculateAccuracy($opname, $opname['opname_type'] == 'GOODS' ? $opnameStocks : $opnameContainers);

        $barcode = new \Milon\Barcode\DNS2D();
        $barcode->setStorPath(APPPATH . "/cache/");
        $opnameBarcode = $barcode->getBarcodePNG($opname['no_opname'], "QRCODE", 5, 5);

        $data = [
            'title' => 'Print Opname Result',
            'page' => 'opname/print_opname_result',
            'opname' => $opname,
            'opnameStocks' => $opnameStocks,
            'opnameBarcode' => $opnameBarcode,
            'opnameContainers' => $opnameContainers
        ];
        $this->load->view('template/print_invoice', $data);
    }

    /**
     * Download result of opname into excel.
     *
     * @param $opnameId
     */
    public function download_result($opnameId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_PRINT_RESULT);

        $opname = $this->opname->getById($opnameId);
        if ($opname['opname_type'] == "GOODS") {
            $data = $this->opnameGoods->getBy(['opname_goods.id_opname' => $opnameId]);
        } else {
            $data = $this->opnameContainer->getBy(['opname_containers.id_opname' => $opnameId]);
        }
        $this->calculateAccuracy($opname, $data);

        $this->opname->exportResultToExcel($opname, $data);
    }

    private function calculateAccuracy(&$opname, $data) {
        $opname['location_accuracy'] = array_sum(array_column($data, 'is_location_match')) / count($data) * 100;
        $stockQuantity = array_sum(array_column($data, 'quantity'));
        $checkQuantity = array_sum(array_column($data, 'quantity_check'));
        $opname['quantity_accuracy'] = $checkQuantity > $stockQuantity ? ($stockQuantity / $checkQuantity * 100) : ($checkQuantity / $stockQuantity * 100);
    }
    
    /**
     * Validating opname request.
     */
    public function validate()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_VALIDATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Opname Data', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $opnameId = $this->input->post('id');
                $status = $this->input->post('status');

                $this->db->trans_start();

                $opname = $this->opname->getById($opnameId);

                $this->opname->update([
                    'status' => $status,
                    'validated_by' => UserModel::authenticatedUserData('id'),
                    'validated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => UserModel::authenticatedUserData('id'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ], $opnameId);

                $statusClass = 'warning';
                // $this->setting->updateSetting('lock_opname', 0);
                // if ($status == OpnameModel::STATUS_APPROVED) {
                //     $statusClass = 'success';
                //     $this->setting->updateSetting('lock_opname', 1);
                // }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash($statusClass, "Opname <strong>{$opname['no_opname']}</strong> successfully {$status}");
                } else {
                    flash('danger', "Validating opname <strong>{$opname['no_opname']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }

        redirect('opname');
    }

    /**
     * Access process opname.
     *
     * @param $type
     * @param $id
     */
    public function access($type, $id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_ACCESS);

            $this->db->trans_start();
            $opname = $this->opname->getById($id);

            if ($type == 'reopen') {
                $type = OpnameModel::STATUS_REOPENED;
            } else {
                $type = OpnameModel::STATUS_CLOSED;
            }

            $this->opname->update([
                'status' => $type,
                'validated_by' => UserModel::authenticatedUserData('id'),
                'validated_at' => sql_date_format('now')
            ], $id);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Opname {$opname['no_opname']} is successfully {$type}");
            } else {
                flash('danger', 'Access process opname failed');
            }
      
            if (!empty(get_url_param('redirect'))) {
                redirect(get_url_param('redirect'), false);
            } else {
                redirect('opname');
            }
    }

     /**
     * Perform deleting opname data.
     */
    public function delete()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_DELETE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Opname Data', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $opnameId = $this->input->post('id');

                $opnameData = $this->opname->getById($opnameId);
                $delete = $this->opname->delete($opnameId);

                if ($delete) {
                    flash('warning', "Opname <strong>{$opnameData['no_opname']}</strong> successfully deleted");
                } else {
                    flash('danger', "Delete opname <strong>{$opnameData['no_opname']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('opname');
    }

 
}
