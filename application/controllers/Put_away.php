<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Account
 * @property UserModel $user
 * @property UserTokenModel $userToken
 * @property PeopleModel $people
 * @property PutAwayModel $putAway
 * @property OperationCutOffModel $operationCutOff
 * @property WorkOrderGoodsModel $workOrderGoods
 * @property PutAwayGoodsModel $putAwayGoods
 * @property WorkOrderGoodsPhotoModel $workOrderGoodsPhoto
 * @property Uploader $uploader
 */
class Put_away extends CI_Controller
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
        $this->load->model('PutAwayModel', 'putAway');
        $this->load->model('PutAwayGoodsModel', 'putAwayGoods');
        $this->load->model('CycleCountGoodsModel', 'CycleCountGoods');
        $this->load->model('CycleCountContainerModel', 'CycleCountContainer');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('LogModel', 'logHistory');
        $this->load->model('HolidayModel', 'holiday');
        $this->load->model('OperationCutOffModel', 'operationCutOff');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('StatusHistoryModel', 'StatusHistory');
        $this->load->model('WorkOrderGoodsPhotoModel', 'workOrderGoodsPhoto');
        $this->load->model('modules/Uploader', 'uploader');
    }

    
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PUT_AWAY_VIEW);

        $putAway = $this->putAway->getAllPutAway();
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();
        
        $data = [
            'title' => "Put Away",
            'page' => "put_away/index",
            'putAway' => $putAway,
        ];

        $this->load->view('template/layout', $data);
    }

    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PUT_AWAY_VIEW);

        $putAway = $this->putAway->getById($id);
        $putAwayDetails = $this->putAwayGoods->getPutAwayGoodsById($id);
 
        $data = [
            'title' => "Put Away",
            'page' => "put_away/view",
            'putAway' => $putAway,
            'putAwayDetails'=> $putAwayDetails,
        ];

        $this->load->view('template/layout', $data);
    }

    public function process($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PUT_AWAY_CREATE);

        $putAway = $this->putAway->getById($id);
        $putAwayDetails = $this->putAwayGoods->getPutAwayGoodsById($id);
        $data = [
                'title' => "Put Away",
                'page' => "put_away/process",
                'putAway' => $putAway,
                'putAwayDetails'=> $putAwayDetails
        ];

        $this->load->view('template/layout', $data);
    }

    /**
     * Show result process.
     */
    public function result($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PUT_AWAY_RESULT);

        $putAway = $this->putAway->getById($id);
        $putAwayDetails = $this->putAwayGoods->getPutAwayGoodsById($id);

        $data = [
                'title' => "Put Away",
                'page' => "put_away/result",
                'putAway' => $putAway,
                'putAwayDetails'=> $putAwayDetails,
        ];

        $this->load->view('template/layout', $data);
    }

    public function save_process($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PUT_AWAY_CREATE);

            $putAway = $this->putAway->getById($id);
            $putAwayDetails = $this->putAwayGoods->getPutAwayGoodsById($id);

            $no_pallet = $this->input->post('no_pallet');
            $type_goods = $this->input->post('type_goods');
            $position = $this->input->post('position');
            $quantity = $this->input->post('quantity');
            $description = $this->input->post('description');
            $photo = $this->input->post('photo');
            $photo_description = $this->input->post('photo_description');
            $workOrderGoodsId = $this->input->post('workOrderGoodsId');

             // replace only if new photo check uploaded.
            $filePath = '';
            $uploadPassed = true;
            if (!empty($_FILES['photo']['name'])) {
                $upload = $this->uploader->setDriver('s3')->uploadTo('photo', [
                    'destination' => 'complain/' . date('Y/m'),
                ]);
                if ($upload) {
                    $uploadedData = $this->uploader->getUploadedData();
                    $filePath = $uploadedData['uploaded_path'];
                    if (!empty($putAway['photo'])) {
                        $this->uploader->setDriver('s3')->delete($putAway['photo']);
                    }
                } else {
                    $uploadPassed = false;
                    $this->session->set_flashdata([
                        'status' => 'warning',
                        'message' =>  $this->uploader->getDisplayErrors(),
                    ]);
                    return $this->process($id);
                }
            }else{
                $filePath = $putAway['photo'];
            }

          
            $type = PutAwayModel::STATUS_PROCESSED;
            $this->db->trans_start();

            if ($uploadPassed) {
                $this->putAway->updatePutAway([
                        'photo' => $filePath,
                        'status' => $type
                ], $id);
            }

            foreach($putAwayDetails as $key => $goods){
                $this->putAwayGoods->update([
                    'pallet_marking_check' => $no_pallet[$key],
                    'type_goods_check' => $type_goods[$key],
                    'position_check' => $position[$key],
                    'quantity_check' => $quantity[$key],
                    'description_check' => $description[$key],
                ], $goods['id']);

                // add goods photo
                $tempPhotos = explode(',', if_empty($photo[$key], ''));
                $tempPhotoDescriptions = explode('||', if_empty($photo_description[$key], ''));
                foreach ($tempPhotos as $index => $file) {
                    if (!empty($file)) {
                        $sourceFile = 'temp/' . $file;
                        $destFile = 'work-order-goods/' . format_date('now', 'Y/m/') . $file;
                        if ($this->uploader->setDriver('s3')->move($sourceFile, $destFile)) {
                            $this->workOrderGoodsPhoto->create([
                                'id_work_order_goods' => $workOrderGoodsId[$key],
                                'src' => $destFile,
                                'url' => $this->uploader->setDriver('s3')->getUrl($destFile),
                                'description' => get_if_exist($tempPhotoDescriptions, $index, null)
                            ]);
                        }
                    }
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Put Away Audit {$putAway['branch']} successfully updated");
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }

            redirect('put-away');
    }


    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PUT_AWAY_CREATE);

        $operationCutOffs = $this->operationCutOff->getBy([
            'ref_operation_cut_offs.id_branch' => get_active_branch('id'),
            'ref_operation_cut_offs.status' => OperationCutOffModel::STATUS_ACTIVE,
        ]);
        
        $data = [
                'title' => "Put Away",
                'page' => "put_away/create",
                'operationCutOffs' => $operationCutOffs,
        ];

        $this->load->view('template/layout', $data);
    }

    public function save()
    {
        $branchId = get_active_branch('id');
        $request_date = $this->input->post('put_away_date');

        if(!is_null($branchId) && (!is_null($request_date))){

            $shift = $this->input->post('shift');
            $description = $this->input->post('description');
            $branchbyId = $this->branch->getById($branchId);

            $operationCutOff = $this->operationCutOff->getById($shift);
            $start = $request_date.' '.$operationCutOff['start'];
            $end = $request_date.' '.$operationCutOff['end'];
            if(strtotime($start) > strtotime($end)){
                $end = date('Y-m-d H:i:s', strtotime("+1 day", strtotime($end)));
            }
            $goodsInbounds = $this->workOrderGoods->getWorkOrderGoodsByRangeTime($start, $end);
            $checkPutAway = $this->putAway->putAwayCheck($branchId, $shift, $request_date);

            $this->db->trans_start();
            if(empty($checkPutAway)){
                $noPutAway = $this->putAway->getAutoNumberPutAway();
                $this->putAway->create([
                    'id_branch' => $branchId,
                    'no_put_away' => $noPutAway,
                    'put_away_date' =>  $request_date,
                    'start' =>  $operationCutOff['start'],
                    'end' =>  $operationCutOff['end'],
                    'id_shift' => $shift,
                    'description' => $description,
                    'status' => !empty($goodsInbounds) ? 'PENDING' : PutAwayModel::STATUS_PROCESSED
                ]);
                $putAwayID = $this->db->insert_id();

                if(!empty($goodsInbounds)){
                    foreach ($goodsInbounds as $goods){
                        $this->putAwayGoods->create([
                            'id_put_away' => $putAwayID,
                            'id_owner' => $goods['id_owner'],
                            'id_booking' => $goods['id_booking'],
                            'id_goods' => $goods['id_goods'],
                            'id_unit' => $goods['id_unit'],
                            'id_position' => if_empty($goods['id_position'], null),
                            'id_position_blocks' => $goods['id_position_blocks'],
                            'id_work_order' => $goods['id_work_order'],
                            'id_work_order_goods' => $goods['id'],
                            'position_block' => $goods['position_blocks'],
                            'no_pallet' => $goods['no_pallet'],
                            'ex_no_container' => $goods['no_container'],
                            'quantity' => $goods['quantity'],
                            'tonnage' => $goods['total_weight'],
                            'tonnage_gross' => $goods['total_gross_weight'],
                            'volume' => $goods['total_volume'],
                            'status_danger' => $goods['status_danger'],
                            'description' => $goods['description'],
                        ]);
                    }
                }
            }else{
                $this->session->set_flashdata([
                    'status' => 'danger',
                    'message' => "Put away audit failed, Put away audit is already exists!",
                ]);

                redirect('put-away');
            }

            $this->db->trans_complete();
            
        }else{
            $this->session->set_flashdata([
                'status' => 'danger',
                'message' => "Put away audit failed, Please select specific branch!",
            ]);

            redirect('put-away');
        }


        if ($this->db->trans_status()) {
            $this->session->set_flashdata([
                'status' => 'warning',
                'message' => "Put Away successfully generated",
            ]);
        } else {
            $this->session->set_flashdata([
                'status' => 'danger',
                'message' => "Put Away failed, try again or contact administrator",
            ]);
        }

        redirect('put-away');
    }

    /**
     * Perform deleting data cycle count.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PUT_AWAY_DELETE);

        $putAway = $this->putAway->getById($id);

        if ($this->putAway->delete($id)) {
            flash('warning', "Cycle Count {$putAway['branch']} successfully deleted");
        } else {
            flash('danger', "Delete Checklist {$putAway['branch']} failed");
        }

        redirect('put-away');
    }

    /**
     * Print cycle count data.
     *
     * @param $id
     */
    public function print($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PUT_AWAY_PRINT);

        $putAway = $this->putAway->getById($id);
        $putAwayDetails = $this->putAwayGoods->getPutAwayGoodsById($id);
        
        $data = [
                'title' => "Put Away",
                'page' => "put_away/_print",
                'putAway' => $putAway,
                'putAwayDetails'=> $putAwayDetails,
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
        AuthorizationModel::mustAuthorized(PERMISSION_PUT_AWAY_PRINT);

        $putAway = $this->putAway->getById($id);
        $putAwayDetails = $this->putAwayGoods->getPutAwayGoodsById($id);
        
        $data = [
                'title' => "Put Away",
                'page' => "put_away/_print_result",
                'putAway' => $putAway,
                'putAwayDetails'=> $putAwayDetails,
        ];

        $this->load->view('template/print_invoice', $data);
      
    }

     /**
     * Reopen.
     *
     * @param $id
     */
    public function reopen($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PUT_AWAY_VALIDATE);


            $this->db->trans_start();

            $putAway = $this->putAway->getById($id);

            $type = PutAwayModel::STATUS_REOPENED;

            $this->putAway->update([
                'status' => $type,
            ], $id);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Put Away date {$putAway['put_away_date']} is successfully {$type}");
            } else {
                flash('danger', 'Validating Put Away failed');
            }
      
            if (!empty(get_url_param('redirect'))) {
                redirect(get_url_param('redirect'), false);
            } else {
                redirect('put-away');
            }
    }
    /**
     * Validating put away.
     */
    public function validate()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PUT_AWAY_VALIDATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Put Awaya Data', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                $this->session->set_flashdata([
                    'status' => 'warning',
                    'message' => validation_errors(),
                ]);
            } else {
                $opnameId = $this->input->post('id');
                $status = $this->input->post('status');
                $description = $this->input->post('description');

                $this->db->trans_start();

                $opname = $this->putAway->getById($opnameId);

                $this->putAway->update([
                    'status' => $status,
                    'validate_description' => $description,
                    'validated_by' => UserModel::authenticatedUserData('id'),
                    'validated_at' => date('Y-m-d H:i:s'),
                ], $opnameId);

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    $this->session->set_flashdata([
                        'status' => 'success',
                        'message' => "Put Away <strong>{$opname['no_put_away']}</strong> successfully {$status}",
                    ]);
                } else {
                    $this->session->set_flashdata([
                        'status' => 'danger',
                        'message' => "Validating put away <strong>{$opname['no_put_away']}</strong> failed, try again or contact administrator",
                    ]);
                }
            }
        } else {
            $this->session->set_flashdata([
                'status' => 'danger',
                'message' => 'Only <strong>POST</strong> request allowed',
            ]);
        }

        redirect('put-away');
    }

         

}