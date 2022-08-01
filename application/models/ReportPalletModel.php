<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ReportPalletModel extends MY_Model
{
   
    /**
     * ReportAdminSiteModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('SafeConductModel', 'safeConduct');
        $this->load->model('HandlingModel', 'handling');
    }

     /**
     * Get admin site data.
     */
    public function getReportPallet($filters = [], $start = -1, $length = 10, $search = '', $column = 0, $sort = 'desc')
    {   
        //get first and last date in current month
        $date_from = date('Y-m-01');
        $date_to = date('Y-m-t');
        $filters['date_type'] = 'completed_at';
        
        if(empty($filters['date_from']) && empty($filters['date_to'])){
            $filters['date_from'] = $date_from;
            $filters['date_to'] = $date_to;
        }

        // alias column name by index for sorting data table library
        $columnOrder = [
            0 => "completed_at",
        ];
        $columnSort = $columnOrder[0];

        // $branchPallet = get_active_branch('stock_pallet');
        $initialPallet = get_active_branch('initial_pallet');
        $workOrderPallet = $this->workOrder->getWorkOrderPallet();
        // krsort($workOrderPallet);
        $totalSpace = 0;
        foreach ($workOrderPallet as $index=> &$dataPallet) {
            $dataPallet['sisa_pallet'] = $initialPallet + $dataPallet['real_stock_pallet'];
            $initialPallet = $dataPallet['sisa_pallet'];
        }
        
        // ksort($workOrderPallet);
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
            foreach ($workOrderPallet as $key => $val) {
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
            $dataSearch = $workOrderPallet;
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
