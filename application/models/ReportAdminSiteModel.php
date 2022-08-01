<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ReportAdminSiteModel extends MY_Model
{
   
    /**
     * ReportAdminSiteModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('TransporterEntryPermitModel', 'transporterEntryPermit');
        $this->load->model('SafeConductModel', 'safeConduct');
        $this->load->model('HandlingModel', 'handling');
    }

     /**
     * Get admin site data.
     */
    public function getReportAdminSite($filters = [], $start = -1, $length = 10, $search = '', $column = 0, $sort = 'desc')
    {   
        //get first and last date in current month
        $date_from = date('Y-m-01');
        $date_to = date('Y-m-t');
        $filters['date_type'] = 'created_at';
        
        if(empty($filters['date_from']) && empty($filters['date_to'])){
            $filters['date_from'] = $date_from;
            $filters['date_to'] = $date_to;
        }

        // alias column name by index for sorting data table library
        $columnOrder = [
            0 => "created_at",
            1 => "doc_no",
            2 => "type",
            3 => "created_at",
            4 => "creator_name",
            5 => "customer_name",
        ];
        $columnSort = $columnOrder[$column];

        $safeConducts = $this->safeConduct->getAllSafeConducts();
        $handlings = $this->handling->getAllHandlings();
        $transporterEntryPermits = $this->transporterEntryPermit->getAll();

        $data_safe_conducts = [];
        foreach($safeConducts as $safeConduct){
            $type = $safeConduct['type'] == "INBOUND" ? "Safe Conduct Inbound" : "Safe Conduct Outbound";
            $data_safe_conducts[] = ["doc_no" => $safeConduct['no_safe_conduct'], "type" => $type, "created_at" => $safeConduct['created_at'], "creator_name" => $safeConduct['creator_name'], "customer_name" => $safeConduct['customer_name'], "id_customer" => $safeConduct['id_customer'], "created_by" => $safeConduct['created_by']];
        }

        $data_handlings = [];
        foreach($handlings as $handling){
            $data_handlings[] = ["doc_no" => $handling['no_handling'], "type" => "Job ".$handling['handling_type'], "created_at" => $handling['created_at'], "creator_name" => $handling['creator_name'], "customer_name" => $handling['customer_name'], "id_customer" => $handling['id_customer'], "created_by" => $handling['created_by'] ];
        }

        $data_tep = [];
        foreach ($transporterEntryPermits as $tep) {
            if($tep['tep_category'] == "OUTBOUND"){
                $customer_id = !empty($tep['id_customer_out']) ? $tep['id_customer_out'] : $tep['id_customer'];
                $customer_name = !empty($tep['customer_name_out']) ? $tep['customer_name_out'] : "-";
            }else{
                $customer_id = !empty($tep['id_customer_in']) ? $tep['id_customer_in'] : $tep['id_customer_booking'];
                $customer_name = !empty($tep['customer_name_in']) ? $tep['customer_name_in'] : $tep['customer_name'];
            }
           
            $data_tep[] = ["doc_no" => $tep['tep_code'], "type" => "TEP ".$tep['tep_category'], 'created_at' => $tep['created_at'], "creator_name" => $tep["creator_name"], "customer_name" => $customer_name, "id_customer" => $customer_id, "created_by" => $tep['created_by']];
        }

        $data_admin_site = array_merge($data_safe_conducts, $data_handlings, $data_tep);

        if(strtolower($sort) == "asc"){
            $sortBy = SORT_ASC;
        }else{
            if(strtolower($sort) == "desc"){
                $sortBy = SORT_DESC;
            }else{
                $sortBy = SORT_ASC;
            }
        }

        if(!empty($search)){
            $dataSearch = [];
            foreach ($data_admin_site as $key => $val) {
                if (stripos(date('d F Y H::s', strtotime($val['created_at'])), $search) !== false) {
                    $dataSearch[] = $val;
                }elseif(stripos($val['creator_name'], $search) !== false){
                    $dataSearch[] = $val;
                }elseif(stripos($val['doc_no'], $search) !== false){
                    $dataSearch[] = $val;
                }elseif(stripos($val['type'], $search) !== false){
                    $dataSearch[] = $val;
                }elseif(stripos($val['customer_name'], $search) !== false){
                    $dataSearch[] = $val;
                }
            }          
        }else{
            $dataSearch = $data_admin_site;
        }
        
        $dataSearch = array_unique($dataSearch, SORT_REGULAR);
        $dataFilter = array_filter($dataSearch, function($item) use($filters){

            //filter creator name (pic)
            $item_creator_name = explode(",", $item['created_by']);
            $filter_creator_name = isset($filters['pic']) ? !empty(array_intersect($item_creator_name, $filters['pic'])) : $item['created_by'] == $item['created_by'];

            //filter date
            $format_date_type = date('Y-m-d', strtotime($item[$filters['date_type']]));
            $format_date_from = date('Y-m-d', strtotime($filters['date_from']));
            $format_date_to = date('Y-m-d', strtotime($filters['date_to']));

            //condition
            if( (strtotime($format_date_type) >= strtotime($format_date_from)) && (strtotime($format_date_type) <= strtotime($format_date_to)) && $filter_creator_name ) {
                return $item;
            }
        });

        $dataFilter = array_values($dataFilter);

        if ($start < 0) {
            $allData = $dataFilter;
            return $allData;
        }

        $reportTotal = count($dataFilter);
        $columnFocus = array_column($dataFilter, $columnSort);
        array_multisort($columnFocus, $sortBy, $dataFilter);
        $reportData = array_slice($dataFilter, $start, $length);

        $pageData = [
            "draw" => get_url_param('draw', $this->input->post('draw')),
            "recordsTotal" => count($reportData),
            "recordsFiltered" => $reportTotal,
            "data" => $reportData
        ];

        return $pageData;

    }
       
}
