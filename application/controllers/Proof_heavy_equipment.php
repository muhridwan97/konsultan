<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Proof_heavy_equipment
 * 
 * @property HeavyEquipmentModel $heavyEquipment
 * @property Exporter $exporter
 * @property ProofHeavyEquipmentHistoryModel $proofHeavyEquipmentHistory
 * @property HeavyEquipmentEntryPermitModel $heavyEquipmentEntryPermit
 * @property BranchVmsModel $branchVms
 */
class Proof_heavy_equipment extends MY_Controller
{
    /**
     * Proof_heavy_equipment constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('HeavyEquipmentModel', 'heavyEquipment');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('ProofHeavyEquipmentHistoryModel', 'proofHeavyEquipmentHistory');
        $this->load->model('HeavyEquipmentEntryPermitModel', 'heavyEquipmentEntryPermit');
        $this->load->model('BranchVmsModel', 'branchVms');
        $this->load->model('PeopleModel', 'peopleModel');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->helper('text');

        $this->setFilterMethods([
            'proof_print' => 'GET|POST',
            'ajax_get_heavy_equipment_internal' => 'POST',
            'ajax_get_heavy_equipment_external' => 'POST',
            'edit_print' => 'GET',
        ]);
    }

    /**
     * Show auction data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PROOF_HEAVY_EQUIPMENT_VIEW);

        $heavyEquipmentNames = $this->heavyEquipment->getAll();
        if(get_url_param('filter_proof')){
            $heavyEquipmentFilters = get_url_param('filter_proof') ? $_GET : [];
            $type = key_exists('type', $heavyEquipmentFilters) ? $heavyEquipmentFilters['type'] : 'INTERNAL';
            $selectedCustomers = key_exists('customer', $heavyEquipmentFilters) ? $heavyEquipmentFilters['customer'] : [0];
            $customers = $this->peopleModel->getById($selectedCustomers);

            $id_reference = $this->input->get('heavy_equipment');
            $id_customer = $this->input->get('customer');
            $id_customer_string = null;
            if(!empty($id_customer)){
                $id_customer_string = implode(",",$id_customer);
            }
            // print_debug($id_customer_string);
            $date = date('Y-m-d',strtotime($this->input->get('date')));
            $heavyEquipmentFilters = [
                'heavy_equipment' => $id_reference,
                'date_from' => $this->input->get('date'),
                'date_to' => $this->input->get('date'),
                'customer' => $id_customer,
            ];
            $heavyEquipments = $this->workOrder->getHeavyEquipmentInternal($heavyEquipmentFilters);
            if($type == 'EXTERNAL'){
                $heavyEquipmentNames = $this->heavyEquipmentEntryPermit->getHEEPAll();
                $heavyEquipmentFilters = [
                    'heavy_equipment' => $id_reference,
                    'date_from' => $this->input->get('date'),
                    'date_to' => $this->input->get('date'),
                ];
                $heavyEquipments = $this->workOrder->getHeavyEquipmentExternal($heavyEquipmentFilters); 
            }
            $heavyEquipment = [];
            foreach ($heavyEquipments as $val) {
                $heavyEquipment = $val;
            }
            if (!empty($heavyEquipments)) {
                $historyPrints = $this->proofHeavyEquipmentHistory->getBy([
                    'proof_heavy_equipment_histories.id_reference' => $id_reference,
                    'proof_heavy_equipment_histories.type' => $type,
                    'proof_heavy_equipment_histories.date' => $date,
                    'proof_heavy_equipment_histories.id_customer' => $id_customer_string,
                ]);
            }

            if(!empty($historyPrints)){
                foreach ($historyPrints as &$historyPrint) {
                    $historyPrint['customer'] = $heavyEquipment['customer'];
                }
            }
            $data = [
                'title' => "Proof Heavy Equipment",
                'page' => "proof_heavy_equipment/index",
                'heavyEquipmentNames' => $heavyEquipmentNames,
                'historyPrints' => $historyPrints,
                'customers' => $customers,
            ];
            $this->load->view('template/layout', $data);
        }else{
            $data = [
                'title' => "Proof of Heavy Equipment",
                'page' => "proof_heavy_equipment/index",
                'heavyEquipmentNames' => $heavyEquipmentNames,
            ];
            $this->load->view('template/layout', $data);
        }
        
    }

    public function edit_print(){
        $id_reference = $this->input->get('heavy_equipment');
        $date = $this->input->get('date');
        $date = date('Y-m-d',strtotime($date));
        $type = $this->input->get('type');
        $id_customer = $this->input->get('customer');
        if (!empty($id_customer)) {
            $id_customer_array = explode(",",$id_customer);
        }else{
            $id_customer_array = [];
        }
        $historyPrints = $this->proofHeavyEquipmentHistory->getLastHistory([
            'proof_heavy_equipment_histories.id_reference' => $id_reference,
            'proof_heavy_equipment_histories.type' => $type,
            'proof_heavy_equipment_histories.date' => $date,
            'proof_heavy_equipment_histories.id_customer' => $id_customer==''?null:$id_customer,
        ]);
        if(!empty($historyPrints)){
            $historyPrints['day_name']=date('l',strtotime($historyPrints['date']));
        }
        if ($type == 'EXTERNAL') {
            $heavyEquipmentFilters = [
                'heavy_equipment' => $id_reference,
                'date_from' => $this->input->get('date'),
                'date_to' => $this->input->get('date'),
                'customer' => $id_customer_array,
            ];
            $heavyEquipments = $this->workOrder->getHeavyEquipmentExternal($heavyEquipmentFilters); 
        
        } else {
            $heavyEquipmentFilters = [
                'heavy_equipment' => $id_reference,
                'date_from' => $this->input->get('date'),
                'date_to' => $this->input->get('date'),
                'customer' => $id_customer_array,
            ];
            $heavyEquipments = $this->workOrder->getHeavyEquipmentInternal($heavyEquipmentFilters);
            
        }
        $heavyEquipment = [];
        foreach ($heavyEquipments as $val) {
            $heavyEquipment = $val;
        }
        //buat teks keterangan
        if(!empty($heavyEquipment['keterangan'])){
            $key_keterangan = explode("; ",$heavyEquipment['keterangan']);
            foreach($key_keterangan as &$key_ket){
                $val = explode(", ",$key_ket);
                $key_ket = $val;
            }
            $customer_name = array_column($key_keterangan, '0');
            array_multisort($customer_name, SORT_ASC, $key_keterangan);
            $temp_customer = '';
            $temp_type = '';
            $teks = '';
            $lenght = count($key_keterangan);
            foreach($key_keterangan as $i=>$ket){
                if(isset($ket[0])&&isset($ket[1])&&isset($ket[2])){
                    if($temp_customer!=$ket[0]){
                        if($temp_customer==''){
                            $teks.=$ket[0].' : '.$ket[1].' ( '.$ket[2];
                        }else{
                            $teks.=' )<br> ';
                            $teks.=$ket[0].' : '.$ket[1].' ( '.$ket[2];
                        }
                        $temp_customer = $ket[0];
                        $temp_type = $ket[1];
                    }else{
                        if($temp_type != $ket[1]){
                            $teks.=' )<br> ';
                            $teks.=$ket[0].' : '.$ket[1].' ( '.$ket[2];
                            $temp_type = $ket[1];
                            continue;
                        }
                        $teks.=', '.$ket[2];
                    }
                    if($i==$lenght-1){
                        $teks.=' )';
                    }
                }
            }
            $heavyEquipment['teks'] = $teks;
        }
        // print_debug($heavyEquipment);
        $data_print = [
            'id_reference' => $id_reference,
            'type' => $type,
            'id_customer' => $id_customer,
            'alat_berat' => $heavyEquipment['name_heavy_equipment'],
            'day_name' => $heavyEquipment['day_name'],
            'date' =>  date('d F Y',strtotime($heavyEquipment['tgl'])),
            'start' => $heavyEquipment['start_job'],
            'end' => $heavyEquipment['finish_job'],
            'description' => $heavyEquipment['teks'],
            'remark' => null,
            'sign_location' => null,
        ];
        $data = [
            'title' => "Print Proof Heavy Equipment",
            'page' => "proof_heavy_equipment/edit_print",
            'data_print' => $data_print,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Print plan realization data.
     *
     * @param $id
     */
    public function proof_print()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PROOF_HEAVY_EQUIPMENT_PRINT);
        if ($this->input->server('REQUEST_METHOD') == 'GET'){
            $id_reference = $this->input->get('heavy_equipment');
            $date = $this->input->get('date');
            $type = $this->input->get('type');
            
            if($type == 'EXTERNAL'){
                $heavyEquipmentFilters = [
                    'heavy_equipment' => $id_reference,
                    'date_from' => $date,
                    'date_to' => $date,
                ];
                $heavyEquipments = $this->workOrder->getHeavyEquipmentExternal($heavyEquipmentFilters); 
            }else{
                $heavyEquipmentFilters = [
                    'heavy_equipment' => $id_reference,
                    'date_from' => $date,
                    'date_to' => $date,
                ];
                $heavyEquipments = $this->workOrder->getHeavyEquipmentInternal($heavyEquipmentFilters);
            }
    
            $heavyEquipment = [];
            foreach ($heavyEquipments as $val) {
                $heavyEquipment = $val;
            }
            
            $id_vms = get_active_branch('id_branch_vms');
            $branchVms = $this->branchVms->getById($id_vms);
            $data = [
                'heavyEquipment' => $heavyEquipment,
                'branchVms' => $branchVms,
            ];
            $this->proofHeavyEquipmentHistory->create([
                'id_reference' => $id_reference,     
                'date' => $heavyEquipment['tgl'],
                'start' => $heavyEquipment['start_job'],
                'end' => $heavyEquipment['finish_job'],
                'type' => $type,
            ]);
    
            $page = $this->load->view('proof_heavy_equipment/print',$data, true);
    
            $this->exporter->exportToPdf('proof-of-heavy-equipment', $page);
        }else if ($this->input->server('REQUEST_METHOD') == 'POST'){
            $id_reference = $this->input->post('id_reference');
            $id_customer = $this->input->post('id_customer');
            $date = $this->input->post('date');
            $type = $this->input->post('type');
            $alat_berat = $this->input->post('alat_berat');
            $hari = $this->input->post('hari');
            $start = $this->input->post('start');
            $end = $this->input->post('end');
            $description = $this->input->post('description');
            $remark = $this->input->post('remark');
            $sign_location = $this->input->post('sign_location');
            $heavyEquipment=[
                'id_reference' => $id_reference,
                'date' => $date,
                'type' => $type,
                'alat_berat' => $alat_berat,
                'hari' => $hari,
                'start' => $start,
                'end' => $end,
                'description' => $description,
                'remark' => $remark,
                'sign_location' => $sign_location,
            ];
            $data = [
                'heavyEquipment' => $heavyEquipment,
            ];
            $this->proofHeavyEquipmentHistory->create([
                'id_reference' => $id_reference,     
                'id_customer' => !empty($id_customer)? $id_customer : NULL,
                'date' => date('Y-m-d',strtotime($date)),
                'start' => $start,
                'end' => $end,
                'type' => $type,
                'alat_berat' => $alat_berat,
                'remark' => $remark,
                'description' => $description,
                'sign_location' => $sign_location,
            ]);
            $page = $this->load->view('proof_heavy_equipment/print_by_edit',$data, true);
    
            $this->exporter->exportToPdf('proof-of-heavy-equipment', $page);
        }
    }

    /**
     * Get ajax heavy equipment internal.
     */
    public function ajax_get_heavy_equipment_internal(){
        $id_reference = $this->input->post('id_reference');
        $date = $this->input->post('date');
        $customer = $this->input->post('customer');
        $heavyEquipmentFilters = [
            'heavy_equipment' => $id_reference,
            'date_from' => $date,
            'date_to' => $date,
            'customer' => $customer,
        ];
        $heavyEquipments = $this->workOrder->getHeavyEquipmentInternal($heavyEquipmentFilters);
              
        header('Content-Type: application/json');
        echo json_encode($heavyEquipments);
    }

    /**
     * Get ajax heavy equipment external.
     */
    public function ajax_get_heavy_equipment_external(){
        $id_reference = $this->input->post('id_reference');
        $date = $this->input->post('date');
        $customer = $this->input->post('customer');
        $heavyEquipmentFilters = [
            'heavy_equipment' => $id_reference,
            'date_from' => $date,
            'date_to' => $date,
            'customer' => $customer,
        ];
        $heavyEquipments = $this->workOrder->getHeavyEquipmentExternal($heavyEquipmentFilters);

        header('Content-Type: application/json');
        echo json_encode($heavyEquipments);
    }

    public function view($id){
        AuthorizationModel::mustAuthorized(PERMISSION_PROOF_HEAVY_EQUIPMENT_VIEW);
        $history = $this->proofHeavyEquipmentHistory->getById($id);
        // print_debug($history);
        $data = [
            'title' => "Print History",
            'page' => "proof_heavy_equipment/view",
            'history' => $history,
        ];
        $this->load->view('template/layout', $data);
    }
    
}