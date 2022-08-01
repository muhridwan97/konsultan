<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Account
 * @property UserModel $user
 * @property UserTokenModel $userToken
 * @property PeopleModel $people
 */
class Opname_space extends CI_Controller
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
        $this->load->model('OpnameSpaceModel', 'OpnameSpace');
        $this->load->model('OpnameSpaceBookingInModel', 'opnameSpaceBookingIn');
        $this->load->model('OpnameSpaceUploadHistoryModel', 'opnameSpaceUploadHistory');
        $this->load->model('OpnameSpaceCheckHistoryModel', 'opnameSpaceCheckHistory');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
        $this->load->model('LogModel', 'logHistory');
        $this->load->model('HolidayModel', 'holiday');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('modules/Importer', 'importer');
        $this->load->model('modules/Uploader', 'uploader');
    }

    
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_SPACES_VIEW);

        $opnameSpaces = $this->OpnameSpace->getAllOpnameSpaces();
        
        $data = [
            'title' => "Opname Space",
            'page' => "opname_space/index",
            'opnameSpaces' => $opnameSpaces,
        ];

        $this->load->view('template/layout', $data);
    }

    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_SPACES_VIEW);

        $opnameSpaces = $this->OpnameSpace->getById($id);
        $opnameSpaceDetails = $this->opnameSpaceBookingIn->getOpnameSpaceBookingInById($id);
        $opnameSpaceUploadHistories = $this->opnameSpaceUploadHistory->getBy([
            'opname_space_upload_histories.id_opname_space' => $id,
            ]);
        $filterGoods = [];
        foreach($opnameSpaceDetails As $result){
            $filterGoods['booking'][] = $result['id_booking'];
        }
        $goods = $this->reportStock->getStockGoods($filterGoods);
        $bookingSort = array_column($goods, 'id_booking');
        array_multisort($bookingSort, SORT_ASC, $goods);
        // print_debug($opnameSpaceDetails);
        foreach($opnameSpaceDetails as &$hasil){
            foreach ($goods as $barang) {
                if($hasil['id_booking']==$barang['id_booking']){
                    $hasil['detail_goods'][] = $barang;
                }
            }
        }

        $customer_name = array_column($opnameSpaceDetails, 'customer_name');
        array_multisort($customer_name, SORT_ASC, $opnameSpaceDetails);
        
        $data = [
            'title' => "Opname Space",
            'page' => "opname_space/view",
            'opnameSpaces' => $opnameSpaces,
            'opnameSpaceDetails'=> $opnameSpaceDetails,
            'opnameSpaceUploadHistories'=> $opnameSpaceUploadHistories,
        ];
        $data['statusHistories'] = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_OPNAME_SPACE,
            'status_histories.id_reference' => $id
        ]);
        
        $this->load->view('template/layout', $data);
    }

    public function process($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_SPACES_PROCESS);

        $opnameSpaces = $this->OpnameSpace->getById($id);
        $opnameSpaceDetails = $this->opnameSpaceBookingIn->getOpnameSpaceBookingInById($id);
        $opnameSpaceUploadHistories = $this->opnameSpaceUploadHistory->getBy([
            'opname_space_upload_histories.id_opname_space' => $id,
            ]);
        $filterGoods = [];
        foreach($opnameSpaceDetails As $result){
            $filterGoods['booking'][] = $result['id_booking'];
        }
        $goods = $this->reportStock->getStockGoods($filterGoods);
        $bookingSort = array_column($goods, 'id_booking');
        array_multisort($bookingSort, SORT_ASC, $goods);
        // print_debug($opnameSpaceDetails);
        foreach($opnameSpaceDetails as &$hasil){
            foreach ($goods as $barang) {
                if($hasil['id_booking']==$barang['id_booking']){
                    $hasil['detail_goods'][] = $barang;
                }
            }
        }

        $customer_name = array_column($opnameSpaceDetails, 'customer_name');
        array_multisort($customer_name, SORT_ASC, $opnameSpaceDetails);
        $export = $this->input->get('export');

        if($export && !empty($opnameSpaceDetails)){
            $spreadsheet = new Spreadsheet();
            $sheet = $spreadsheet->getActiveSheet();
            $sheet->setCellValue('A1','Branch : '.$opnameSpaces['branch']);
            $sheet->setCellValue('A2','Description : '.if_empty($opnameSpaces['description'], 'No description'));
            $sheet->setCellValue('A3','Date : '.format_date($opnameSpaces['opname_space_date'], 'd F Y'));
            $kolom = 1;
            $baris = 5;
            $number = 0;
            
            $sheet->setCellValueByColumnAndRow($kolom++, $baris, "NO");
            $sheet->setCellValueByColumnAndRow($kolom++, $baris, "RELATED ID");
            $sheet->setCellValueByColumnAndRow($kolom++, $baris, "CUSTOMER");
            $sheet->setCellValueByColumnAndRow($kolom++, $baris, "NO REFERENCE");
            $sheet->setCellValueByColumnAndRow($kolom++, $baris, "EX NO CONTAINER");
            $sheet->setCellValueByColumnAndRow($kolom++, $baris, "NO GOODS");
            $sheet->setCellValueByColumnAndRow($kolom++, $baris, "GOODS NAME");
            $sheet->setCellValueByColumnAndRow($kolom++, $baris, "SPACE CHECK");
            $sheet->setCellValueByColumnAndRow($kolom++, $baris, "DESCRIPTION CHECK");
            $sheet->setCellValueByColumnAndRow($kolom, $baris++, "ID BOOKING");
            $kolom = 1;
            foreach($opnameSpaceDetails as $key => $opnameSpaceDetail){
                $countData = 0;
                if(!empty($opnameSpaceDetail['detail_goods'])){
                    $countData = count($opnameSpaceDetail['detail_goods']) ;
                }
                $countGoods = !empty($opnameSpaceDetail['detail_goods']) && $countData > 1 ? $countData = $countData-1 : 0;

                $sheet->setCellValueByColumnAndRow($kolom, $baris, $number = $number+1);
                $sheet->mergeCellsByColumnAndRow($kolom,$baris,$kolom,$baris+$countGoods);
                $kolom++;
                $sheet->setCellValueByColumnAndRow($kolom, $baris, $opnameSpaceDetail['id']);
                $sheet->mergeCellsByColumnAndRow($kolom,$baris,$kolom,$baris+$countGoods);
                $kolom++;
                $sheet->setCellValueByColumnAndRow($kolom, $baris, $opnameSpaceDetail['customer_name']);
                $sheet->mergeCellsByColumnAndRow($kolom,$baris,$kolom,$baris+$countGoods);
                $kolom++;
                $sheet->setCellValueByColumnAndRow($kolom, $baris, $opnameSpaceDetail['no_reference']);
                $sheet->mergeCellsByColumnAndRow($kolom,$baris,$kolom,$baris+$countGoods);
                $kolom++;
                
                $tempKolom = $kolom+3;
                $tempBaris = $baris;
                if (!empty($countData)){
                    foreach($opnameSpaceDetail['detail_goods'] AS $detail){
                        $kolomGoods = $kolom;
                        $sheet->setCellValueByColumnAndRow($kolomGoods++, $baris, if_empty($detail['ex_no_container'], '-'));    
                        $sheet->setCellValueByColumnAndRow($kolomGoods++, $baris, if_empty($detail['no_goods'], '-'));
                        $sheet->setCellValueByColumnAndRow($kolomGoods++, $baris, if_empty($detail['goods_name'], '-'));
                        $baris++;
                    }
                }else{
                    $sheet->setCellValueByColumnAndRow($kolom++, $baris, "-");
                    $sheet->setCellValueByColumnAndRow($kolom++, $baris, "-");
                    $sheet->setCellValueByColumnAndRow($kolom, $baris, "-");
                    $baris++;
                }

                $sheet->getStyle('H' . ($tempBaris))
					->getNumberFormat()
					->setFormatCode(PhpOffice\PhpSpreadsheet\Style\NumberFormat::FORMAT_NUMBER_00);
                $sheet->setCellValueByColumnAndRow($tempKolom, $tempBaris, "");
                $sheet->mergeCellsByColumnAndRow($tempKolom,$tempBaris,$tempKolom++,$tempBaris+$countGoods);
                $sheet->setCellValueByColumnAndRow($tempKolom, $tempBaris, "");
                $sheet->mergeCellsByColumnAndRow($tempKolom,$tempBaris,$tempKolom++,$tempBaris+$countGoods);
                $sheet->setCellValueByColumnAndRow($tempKolom, $tempBaris, $opnameSpaceDetail['id_booking']);
                $sheet->mergeCellsByColumnAndRow($tempKolom,$tempBaris,$tempKolom,$tempBaris+$countGoods);
                $kolom = 1;
            }
            $sheet
                    ->getStyleByColumnAndRow(1,5,$tempKolom,$baris-1)
                    ->applyFromArray([
                            'borders' => array(
                                'allBorders' => array(
                                    'borderStyle' => Border::BORDER_THIN,
                                ),
                            ),
                        ]
                    );
            $sheet->getColumnDimension('A')->setWidth(4);
            $sheet->getColumnDimension('J')->setWidth(0);
            foreach(range('B','I') as $columnID) {
                $sheet->getColumnDimension($columnID)
                    ->setAutoSize(true);
            }
            $sheet->setAutoFilter('A5:I6');
			$sheet->freezePane('E6');
            $writer = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Template Opname Space '.format_date($opnameSpaces['opname_space_date'], 'd F Y').'.xlsx"');
            $writer->save('php://output');
        }else{
            $data = [
                'title' => "Opname Space",
                'page' => "opname_space/process",
                'opnameSpaces' => $opnameSpaces,
                'opnameSpaceDetails'=> $opnameSpaceDetails,
                'opnameSpaceUploadHistories'=> $opnameSpaceUploadHistories,
            ];

            $this->load->view('template/layout', $data);
        }
        // print_debug($opnameSpaceDetails);
        
    }

    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_SPACES_PROCESS);

        $opnameSpaces = $this->OpnameSpace->getById($id);
        $opnameSpaceDetails = $this->opnameSpaceBookingIn->getOpnameSpaceBookingInById($id);
        $opnameSpaceUploadHistories = $this->opnameSpaceUploadHistory->getBy([
            'opname_space_upload_histories.id_opname_space' => $id,
            ]);
        $filterGoods = [];
        foreach($opnameSpaceDetails As $result){
            $filterGoods['booking'][] = $result['id_booking'];
        }
        $goods = $this->reportStock->getStockGoods($filterGoods);
        $bookingSort = array_column($goods, 'id_booking');
        array_multisort($bookingSort, SORT_ASC, $goods);
        // print_debug($opnameSpaceDetails);
        foreach($opnameSpaceDetails as &$hasil){
            foreach ($goods as $barang) {
                if($hasil['id_booking']==$barang['id_booking']){
                    $hasil['detail_goods'][] = $barang;
                }
            }
        }

        $customer_name = array_column($opnameSpaceDetails, 'customer_name');
        array_multisort($customer_name, SORT_ASC, $opnameSpaceDetails);
        $data = [
            'title' => "Edit Opname Space",
            'page' => "opname_space/edit",
            'opnameSpaces' => $opnameSpaces,
            'opnameSpaceDetails'=> $opnameSpaceDetails,
            'opnameSpaceUploadHistories'=> $opnameSpaceUploadHistories,
        ];

        $this->load->view('template/layout', $data);
        
    }

    /**
     * Save procces.
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_SPACES_EDIT);

        $opnameSpace = $this->OpnameSpace->getById($id);
        $branchId = get_active_branch('id');
        $opnameSpaceBookingIn = $this->opnameSpaceBookingIn->getBy(['opname_space_booking_in.id_opname_space' => $id]);
        
        $bookingIds = array_column($opnameSpaceBookingIn,'id_booking');
        $overCapacitiesIndex = [];
        
        $overCapacities = $this->workOrder->getDataOpnameSpace([
            'branch' => $branchId,
            'booking' => $bookingIds,
        ]);
        foreach ($overCapacities as $overCapacity) {
            $index = empty($overCapacity['id_booking_in'])? $overCapacity['id_booking'] : $overCapacity['id_booking_in'];
            $overCapacitiesIndex[$index] = $overCapacity;
        }
        $this->db->trans_start();
        $space_check = $this->input->post('space_check');
        foreach($opnameSpaceBookingIn as $key => $opnameItem){
            $qty = $space_check[$key] == null ? null : $space_check[$key];   
            $spaceAwal = $overCapacitiesIndex[$opnameItem['id_booking']]['sum_space']; 
            $check = $qty;
            $diff = 0;
            if ($check<$spaceAwal) {
                $diff = abs($spaceAwal - $check)*-1;
            }else{
                $diff = abs($spaceAwal - $check);
            }
            $update = $this->opnameSpaceBookingIn->update([
                'space_check' => $qty,
                'space_diff' => $diff,
                // 'description_check' => $description[$key],
            ], $opnameItem['id']);
            if($update){
                $data = [
                    'id_space_booking' => $opnameItem['id'],
                    'id_booking' => $opnameItem['id_booking'],
                    'space_check' => $qty,
                    'space_diff' => $diff,
                    'description' => "edit",
                ];
                $this->opnameSpaceCheckHistory->create($data);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            flash('success', "Opname {$opnameSpace['no_opname_space']} successfully updated", 'opname-space');
        } else {
            flash('danger', 'Something is getting wrong, try again or contact administrator', 'opname-space/edit/'.$id);
        }
    }

    /**
     * Show result process.
     */
    public function result($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_SPACES_RESULT);

        $cycleCounts = $this->OpnameSpace->getById($id);
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
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_SPACES_CREATE);

            $cycleCounts = $this->OpnameSpace->getById($id);
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

            $update = $this->OpnameSpace->updateCycleCount([
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
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_SPACES_CREATE);

        $data = [
                'title' => "Opname Space",
                'page' => "opname_space/create",
        ];

        $this->load->view('template/layout', $data);
    }

    public function save()
    {
        $branches = $this->branch->getAll();
        $branchId = get_active_branch('id');
        $request_date = $this->input->post('opname_space_date');
        $getPublicHoliday = $this->holiday->getAllData();

        if(!is_null($branchId) && (!is_null($request_date))){
            $description = $this->input->post('description');
            $branchbyId = $this->branch->getById($branchId);
            $lastOpnameSpace = $this->OpnameSpace->getLastDataByBranch($branchId);

            // $filterContainer['data'] = 'stock';
            // $reportContainers = $this->reportStock->getStockContainers($filterContainer);
            // $id_booking_containers = array_unique(array_column($reportContainers, 'id_booking'));
            $filterGoods = [];
            $filterGoods['data'] = 'stock';
            $reportGoods = $this->reportStock->getStockGoods($filterGoods);
            $id_booking_goods = array_unique(array_column($reportGoods, 'id_booking'));
            $opnameSpace = $this->opnameSpaceBookingIn->getReportOpnameSpace([
                'booking' => $id_booking_goods,
            ]);
            // print_debug($opnameSpace);
            $cekMinus = [];
            $cekMinusResult = [];
            $dataCapacities = $this->workOrder->getOverCapacity(['sum_space_minus'=>'1']);
            // print_debug($id_booking_goods);
            foreach($dataCapacities as $dataCapacity){
                if(in_array($dataCapacity['id_booking'], $id_booking_goods)){
                    $cekMinus[] = $dataCapacity;
                }
            }
            foreach ($cekMinus as &$result) {
                $filterGoods['booking'] = $result['id_booking'];
                $result['sum_space_cal'] = '';
                foreach($opnameSpace as $space){
                    if($result['id_booking'] == $space['id_booking']){
                        $result['sum_space_cal'] = $result['sum_space']+$space['space_diff'];
                    }
                }
                if ($result['sum_space']<=0) {
                    if(!empty($result['sum_space_cal'])){
                        if($result['sum_space_cal']<=0){
                            $cekMinusResult[] = $result['id_booking'];
                        }
                    }else{
                        $cekMinusResult[] = $result['id_booking'];
                    }
                }
            }
            $overCapacities = $this->workOrder->getDataOpnameSpace([
                'branch' => $branchId,
                'completed' => isset($lastOpnameSpace['opname_space_date'])?$lastOpnameSpace['opname_space_date']:null,
                'or_booking' => $cekMinusResult,
            ]);

            $resultFilters = [];
            $totalSpace = [];
            $lastUpdated = "2000-06-05 13:18:17";
            //filter untuk yg ada stock aja
            foreach($overCapacities as $overCapacity){
                if(in_array($overCapacity['id_booking'], $id_booking_goods) ){
                    $resultFilters[] = $overCapacity;
                }
            }
    
            $customer_name = array_column($resultFilters, 'customer_name');
            array_multisort($customer_name, SORT_ASC, $resultFilters);
            $noOpnameSpace = $this->OpnameSpace->getAutoNumberOpnameSpace();
            $checkOpnameSpace = $this->OpnameSpace->opnameSpaceCheck($branchId,$request_date);
            $this->db->trans_start();
            if(empty($checkOpnameSpace)){
                $data = [
                    'id_branch' => $branchId,
                    'no_opname_space' => $noOpnameSpace,
                    'opname_space_date' =>  $request_date,
                    'description' => $description,
                    'status' => OpnameSpaceModel::STATUS_PENDING,
                ];
                $this->OpnameSpace->create($data);
                $opnameSpaceId = $this->db->insert_id();
                
                foreach ($resultFilters as $booking){
                    $this->opnameSpaceBookingIn->create([
                        'id_opname_space' => $opnameSpaceId,
                        'id_booking' => $booking['id_booking'],
                        'no_reference' => $booking['no_reference'],
                        'description' => $booking['description'],
                    ]);
                }
                $this->statusHistory->create([
                    'id_reference' => $opnameSpaceId,
                    'type' => StatusHistoryModel::TYPE_OPNAME_SPACE,
                    'status' => OpnameSpaceModel::STATUS_PENDING,
                    'description' => 'Create opname space',
                    'data' => json_encode($data)
                ]);
            }else{
                flash('danger', "opname-space stocks failed, opname-space is already exists!");

                redirect('opname-space');
            }

            $this->db->trans_complete();
            
        }else{
            flash('danger', "Opname Space stocks failed, Please select specific branch!");

            redirect('opname-space/create');
        }


        if ($this->db->trans_status()) {
            flash('warning', "Opname Space successfully generated");
        } else {
            flash('danger', "Opname Space failed, try again or contact administrator");
        }

        redirect('opname-space');
    }

    /**
     * Perform deleting data cycle count.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_SPACES_DELETE);

        $opnameSpace = $this->OpnameSpace->getById($id);

        if ($this->OpnameSpace->delete($id)) {
            flash('warning', "Opname Space {$opnameSpace['no_opname_space']} successfully deleted");
        } else {
            flash('danger', "Delete Opname Space {$opnameSpace['no_opname_space']} failed");
        }

        redirect('opname-space');
    }

    /**
     * Print cycle count data.
     *
     * @param $id
     */
    public function print($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_SPACES_PRINT);

        $cycleCounts = $this->OpnameSpace->getById($id);
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
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_SPACES_PRINT);

        $cycleCounts = $this->OpnameSpace->getById($id);
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
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_SPACES_VALIDATE);

        $description = $this->input->post('description');
        $this->db->trans_start();

        $OpnameSpace = $this->OpnameSpace->getById($id);

        $textPersen = '';
        if ($type == 'reopen') {
            $type = OpnameSpaceModel::STATUS_REOPENED;
        } else {
            $type = OpnameSpaceModel::STATUS_VALIDATED;
            $cekPersen = $this->opnameSpaceBookingIn->cekPersen($id);
            foreach ($cekPersen as $index => $persen) {
                if($index==0){
                    $textPersen.= $persen['no_reference'];
                }else{
                    $textPersen.= ", ".$persen['no_reference'];
                }
            }
        }
        $this->OpnameSpace->update([
            'status' => $type,
            'validated_by' => UserModel::authenticatedUserData('id'),
            'validated_at' => sql_date_format('now')
        ], $id);

        $this->statusHistory->create([
            'id_reference' => $id,
            'type' => StatusHistoryModel::TYPE_OPNAME_SPACE,
            'status' => $type,
            'description' => $description,
            'data' => json_encode($OpnameSpace)
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            if (!empty($textPersen)) {
                flash('warning', "Validating opname Space {$OpnameSpace['no_opname_space']} successfully, but opname differs by more than 10 percent 
                here's the list : ".$textPersen);
            }else{
                flash('success', "Opname Space date {$OpnameSpace['opname_space_date']} is successfully {$type}");
            }
        } else {
            flash('danger', 'Validating Opname Space failed');
        }
    
        if (!empty(get_url_param('redirect'))) {
            redirect(get_url_param('redirect'), false);
        } else {
            redirect('opname-space');
        }
    }

    /**
     * Upload opname result
     * @param $id
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Reader\Exception
     */
    public function upload_opname($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_SPACES_PROCESS);

        $opnameSpaces = $this->OpnameSpace->getById($id);
        $branchId = get_active_branch('id');

        if (_is_method('post')) {
            $uploadedFile = $opnameSpaces['file'];
            if ($this->uploader->uploadTo('opname_result', [
                'destination' => 'opname_space_file'
                ])) {
                $result = $this->uploader->getUploadedData();
                $uploadedFile = $result['file_name'];
                $opnameData = $this->importer->importFrom($result['full_path'],true,['row' => 5, 'column' => []]);
                if (!empty($opnameData)) {
                    foreach ($opnameData as $opnameItem) {
                        if (key_exists('SPACE CHECK', $opnameItem) && key_exists('RELATED ID', $opnameItem)) {
                            // if (trim($opnameItem['SPACE CHECK']) == '') {// gak bisa karna di merge jadi ada yg slalu kosong
                            //     flash('danger', 'All space must be filled', 'opname-space/upload-opname/' . $id);
                            // }
                        } else {
                            flash('danger', 'Column space check and related id must exist in excel table', 'opname-space/upload-opname/' . $id);
                        }
                    }
                    $bookingIds = [];
                    $overCapacitiesIndex = [];
                    foreach ($opnameData as $dataBooking) {
                        if(trim($dataBooking['ID BOOKING']) != ''){
                            $bookingIds[]=$dataBooking['ID BOOKING'];
                        }
                    }
                    $overCapacities = $this->workOrder->getDataOpnameSpace([
                        'branch' => $branchId,
                        'booking' => $bookingIds,
                    ]);
                    foreach ($overCapacities as $overCapacity) {
                        $index = empty($overCapacity['id_booking_in'])? $overCapacity['id_booking'] : $overCapacity['id_booking_in'];
                        $overCapacitiesIndex[$index] = $overCapacity;
                    }
                    $this->db->trans_start();

                    $this->OpnameSpace->update([
                        'status' => OpnameSpaceModel::STATUS_PROCESSED,
                        'file' => $uploadedFile
                    ], $id);
                    $data = [
                        'id_opname_space' => $id,
                        'file' => $uploadedFile,
                    ];
                    $this->opnameSpaceUploadHistory->create($data);

                    foreach ($opnameData as $opnameItem) {
                        if(trim($opnameItem['RELATED ID']) != ''){
                            $opnameSpaceBookingIn = $this->opnameSpaceBookingIn->getById($opnameItem['RELATED ID']);

                            if(empty($opnameSpaceBookingIn)){
                                $checkSpace = $this->workOrder->getDataOpnameSpace([
                                    'branch' => $branchId,
                                    'booking' => $opnameItem['ID BOOKING'],
                                ]);
                                if(!empty($checkSpace) && trim($opnameItem['SPACE CHECK']) != ''){
                                    $spaceAwal = $checkSpace[0]['sum_space']; 
                                    $check = $opnameItem['SPACE CHECK'];
                                    $diff = 0;
                                    if ($check<$spaceAwal) {
                                        $diff = abs($spaceAwal - $check)*-1;
                                    }else{
                                        $diff = abs($spaceAwal - $check);
                                    }
                                    $data = [
                                        'id_opname_space' => $id,
                                        'space_check' => $opnameItem['SPACE CHECK'],
                                        'space_diff' => $diff,
                                        'description_check' => $opnameItem['DESCRIPTION CHECK'],
                                    ];
                                    $this->opnameSpaceBookingIn->create($data);
                                }
                            }else{
                                if(trim($opnameItem['SPACE CHECK']) != ''){
                                    $spaceAwal = $overCapacitiesIndex[$opnameItem['ID BOOKING']]['sum_space']; 
                                    $check = $opnameItem['SPACE CHECK'];
                                    $diff = 0;
                                    if ($check<$spaceAwal) {
                                        $diff = abs($spaceAwal - $check)*-1;
                                    }else{
                                        $diff = abs($spaceAwal - $check);
                                    }
                                    $data = [
                                        'space_check' => $opnameItem['SPACE CHECK'],
                                        'space_diff' => $diff,
                                        'description_check' => $opnameItem['DESCRIPTION CHECK'],
                                    ];
                                    $updateSpace = $this->opnameSpaceBookingIn->update($data, $opnameItem['RELATED ID']);
                                    if($updateSpace){
                                        $data = [
                                            'id_space_booking' => $opnameItem['RELATED ID'],
                                            'id_booking' => $opnameSpaceBookingIn['id_booking'],
                                            'space_check' => $opnameItem['SPACE CHECK'],
                                            'space_diff' => $diff,
                                            'description' => $opnameItem['DESCRIPTION CHECK'],
                                        ];
                                        $this->opnameSpaceCheckHistory->create($data);
                                    }
                                }
                            }
                        }
                    }

                    $this->db->trans_complete();

                    if ($this->db->trans_status()) {
                        flash('success', "Opname {$opnameSpaces['no_opname_space']} successfully proceed", 'opname-space');
                    } else {
                        flash('danger', 'Something is getting wrong, try again or contact administrator');
                    }
                } else {
                    flash('danger', 'Data opname space is empty');
                }
            } else {
                flash('danger', $this->uploader->getDisplayErrors(), 'opname-space/upload-opname/' . $id);
            }
        } else {
            $opnameSpaces = $this->OpnameSpace->getById($id);

            $data = [
                'title' => "Opnames",
                'subtitle' => "Upload Opname",
                'page' => "opname_space/upload",
                'opnameSpaces' => $opnameSpaces,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Complete status opname.
     */
    public function edit_status($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPNAME_SPACES_PROCESS);
        $opnameSpace = $this->OpnameSpace->getById($id);
        $opnameSpaceBookingIn = $this->opnameSpaceBookingIn->getBy(['opname_space_booking_in.id_opname_space' => $id]);
        $type = OpnameSpaceModel::STATUS_COMPLETED;

        $getSpaceCheck = in_array(null, array_column($opnameSpaceBookingIn, "space_check"));

        if( $getSpaceCheck == true ){
            flash('danger', "Opname {$opnameSpace['no_opname_space']} complete failed, Please complete the form opname space!", 'opname-space');
        }else{
            $update = $this->OpnameSpace->update([
                'status' => $type,
            ], $id);
            $this->statusHistory->create([
                'id_reference' => $id,
                'type' => StatusHistoryModel::TYPE_OPNAME_SPACE,
                'status' => OpnameSpaceModel::STATUS_COMPLETED,
                'description' => 'Completed',
                'data' => json_encode($opnameSpace)
            ]);

            if ($update == true) {
                flash('success', "Opname {$opnameSpace['no_opname_space']} successfully completed", 'opname-space');
            } else {
                flash('danger', "Update Opname {$opnameSpace['no_opname_space']} failed, try again or contact administrator", 'opname-space');
            }
        }
    }

}