<?php
defined('BASEPATH') or exit('No direct script access allowed');

class ReportPerformanceModel extends MY_Model
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
    public function getReportPerformance($filters = [], $start = -1, $length = 10, $search = '', $column = 0, $sort = 'desc')
    {   
        //get first and last date in current month
        $date_from = date('Y');
        $date_to = date('Y');
        $filters['date_type'] = 'tahun';
        
        if(empty($filters['date_from']) && empty($filters['date_to'])){
            $filters['date_from'] = $date_from;
            $filters['date_to'] = $date_to;
        }
        //for filter database
        if(isset($filters['year'])){
            $filters['date_from'] = $filters['year'];
            $filters['date_to'] = $filters['year'];
            if($filters['year']==date('Y')){
                $filtersDb['date_from'] = date("Y-01-01");
                $filtersDb['date_to'] = date("Y-m-d");
            }else{
                $filtersDb['date_from'] = $filters['year'].'-01-01';
                $filtersDb['date_to'] = $filters['year'].'-12-31';
            }            
        }else{
            $filters['year'] = '';
        }
        if(empty($filters['year']) && empty($filters['year'])){
            $filtersDb['date_from'] = date("Y-01-01");
            $filtersDb['date_to'] = date("Y-m-d");
        }

        // alias column name by index for sorting data table library
        $columnOrder = [
            0 => "minggu",
            1 => "tahun",
        ];
        $columnSort = $columnOrder[0];

        $workOrderPerformance = $this->workOrder->getWorkOrderPerformance($filtersDb);
        // print_debug($this->db->last_query());
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
            foreach ($workOrderPerformance as $key => $val) {
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
            $dataSearch = $workOrderPerformance;
        }
        
        $dataSearch = array_unique($dataSearch, SORT_REGULAR);
        $dataFilter = array_filter($dataSearch, function($item) use($filters){

            //filter creator name (pic)
            // $item_creator_name = explode(",", $item['created_by']);
            // $filter_creator_name = isset($filters['pic']) ? !empty(array_intersect($item_creator_name, $filters['pic'])) : $item['created_by'] == $item['created_by'];

            //filter date
            $format_date_type = $item[$filters['date_type']];
            $format_date_from =  $filters['date_from'];
            $format_date_to = $filters['date_to'];

            //condition
            if( ($format_date_type >= $format_date_from) && ($format_date_type <= $format_date_to) ) {
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

    /**
     * Get report Ops performance.
     * @param array $filters
     * @return array|array[]
     */
    public function getOpsPerformanceInbound($filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $baseQuery = $this->db
            ->select([
                'uploads.no_upload',
                'uploads.description AS no_reference',
                'ref_people.id AS id_customer',
                'ref_people.name AS customer_name',
                'ref_booking_types.booking_type',
                'ref_booking_types.with_do',
                'customers.id AS id_customer',
                'customers.name AS customer_name',
                'ata.ata_date',
                'delivery_orders.do_date',
                'IF(DATEDIFF(do_date, ata_date) < 0, 0, DATEDIFF(do_date, ata_date)) AS st_ata_do',
	            'IF(DATEDIFF(do_date, ata_date) > ref_branches.kpi_inbound_do, 0, 1) AS on_target_ata_do',
                'sppb.sppb_date',
                'IF(DATEDIFF(sppb_date, IF(ata_date > do_date, ata_date, do_date)) < 0, 0, DATEDIFF(sppb_date, IF(ata_date > do_date, ata_date, do_date))) AS st_do_sppb',
                'IF(DATEDIFF(sppb_date, IF(ata_date > do_date, ata_date, do_date)) > ref_branches.kpi_inbound_sppb, 0, 1) AS on_target_do_sppb',
                'gate_in.gate_in_date',
                'DATEDIFF(gate_in_date, sppb_date) AS st_sppb_gate_in',
                'IF(DATEDIFF(gate_in_date, sppb_date) > ref_branches.kpi_inbound_gate_in, 0, 1) AS on_target_sppb_gate_in',
                'IF(bookings.total_container > 0, stripping.stripping_date, stripping.unload_date) AS stripping_date',
                'DATEDIFF(IF(bookings.total_container > 0, stripping.stripping_date, stripping.unload_date), gate_in_date) AS st_gate_in_stripping',
                'IF(DATEDIFF(IF(bookings.total_container > 0, stripping.stripping_date, stripping.unload_date), gate_in_date) > (ref_branches.kpi_inbound_stripping - ref_branches.kpi_inbound_gate_in), 0, 1) AS on_target_gate_in_stripping',
                'IF(DATEDIFF(IF(bookings.total_container > 0, stripping.stripping_date, stripping.unload_date), IF(ata_date > do_date, ata_date, do_date)) > 2, 0, 1) AS on_target_inbound',
                'booking_statuses.booking_complete_date'
            ])
            ->from('uploads')
            ->join('ref_branches', 'ref_branches.id = uploads.id_branch')
            ->join('ref_people', 'ref_people.id = uploads.id_person')
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type')
            ->join('ref_people AS customers', 'customers.id = uploads.id_person')
            ->join("(
                SELECT bookings.*, COUNT(DISTINCT booking_containers.id) AS total_container, COUNT(DISTINCT booking_goods.id) AS total_item
                FROM bookings
                LEFT JOIN booking_containers ON booking_containers.id_booking = bookings.id
                LEFT JOIN booking_goods ON booking_goods.id_booking = bookings.id
                GROUP BY bookings.id
            ) AS bookings", 'bookings.id_upload = uploads.id')
            ->join('(
                SELECT id_booking, MAX(created_at) AS booking_complete_date 
                FROM booking_statuses
                WHERE booking_status = "COMPLETED"
                GROUP BY id_booking
            ) AS booking_statuses', 'booking_statuses.id_booking = bookings.id')
            ->join("(
                SELECT id_upload, upload_documents.document_date AS ata_date
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE document_type = 'ATA' AND upload_documents.is_deleted = FALSE
            ) AS ata", "ata.id_upload = uploads.id")
            ->join("(
                SELECT id_upload, upload_documents.document_date AS do_date
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE document_type = 'DO' AND upload_documents.is_deleted = FALSE
            ) AS delivery_orders", "delivery_orders.id_upload = uploads.id")
            ->join("(
                SELECT id_upload, upload_documents.document_date AS sppb_date
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE document_type = 'SPPB' AND upload_documents.is_deleted = FALSE
            ) AS sppb", "sppb.id_upload = uploads.id")
            ->join("(
                SELECT 
                    id_upload, 
                    MIN(IF(expedition_type = 'INTERNAL', security_out_date, security_in_date)) AS gate_in_date 
                FROM bookings
                INNER JOIN safe_conducts ON safe_conducts.id_booking = bookings.id
                WHERE safe_conducts.is_deleted = FALSE
                GROUP BY id_upload
            ) AS gate_in", "gate_in.id_upload = uploads.id")
            ->join("(
                SELECT 
                    id_booking, 
                    MAX(IF(handling_type = 'UNLOAD', work_orders.completed_at, NULL)) AS unload_date,
                    MAX(IF(handling_type = 'STRIPPING', work_orders.completed_at, NULL)) AS stripping_date
                FROM handlings
                INNER JOIN ref_handling_types ON ref_handling_types.id = handlings.id_handling_type
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                WHERE handlings.is_deleted = FALSE 
                    AND handlings.status = 'APPROVED'
                    AND (ref_handling_types.handling_type = 'UNLOAD' OR ref_handling_types.handling_type = 'STRIPPING')
                    AND work_orders.status = 'COMPLETED'
                GROUP BY id_booking
            ) AS stripping", "stripping.id_booking = bookings.id")
            ->where([
                'uploads.is_deleted' => false
            ])
            ->having('stripping_date IS NOT NULL');

        if (!empty($branchId)) {
            $baseQuery->where('uploads.id_branch', $branchId);
        }

        if (key_exists('customers', $filters) && !empty($filters['customers'])) {
            $baseQuery->where_in('uploads.id_person', $filters['customers']);
        }

        if (key_exists('do_type', $filters)) {
            $baseQuery->where('ref_booking_types.with_do', $filters['do_type']);
        }

        if (key_exists('year', $filters) && !empty($filters['year'])) {
            $baseQuery->having('YEAR(stripping_date)', $filters['year']);
        }

        if (key_exists('month', $filters) && !empty($filters['month'])) {
            $baseQuery->having('MONTH(stripping_date)', $filters['month']);
        }

        if (key_exists('week', $filters) && !empty($filters['week'])) {
            $baseQuery->having('WEEK(stripping_date)', $filters['week'] - 1);
        }

        if (key_exists('on_target_ata_do', $filters)) {
            $baseQuery->having('on_target_ata_do', $filters['on_target_ata_do']);
        }

        if (key_exists('on_target_do_sppb', $filters)) {
            $baseQuery->having('on_target_do_sppb', $filters['on_target_do_sppb']);
        }

        if (key_exists('on_target_sppb_gate_in', $filters)) {
            $baseQuery->having('on_target_sppb_gate_in', $filters['on_target_sppb_gate_in']);
        }

        if (key_exists('on_target_gate_in_stripping', $filters)) {
            $baseQuery->having('on_target_gate_in_stripping', $filters['on_target_gate_in_stripping']);
        }

        if (key_exists('on_target_inbound', $filters)) {
            $baseQuery->having('on_target_inbound', $filters['on_target_inbound']);
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get ops performance outbound.
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getOpsPerformanceOutbound($filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $baseQuery = $this->db
            ->select([
                'uploads.id',
                'uploads.id_branch',
                'uploads.id_person AS id_customer',
                'upload_inbounds.description AS no_reference_in',
                'uploads.description AS no_reference_out',
                'sppb.sppb_date',
                'invoices.no_invoice',
                'safe_conducts.last_gate_out_date',
                'IFNULL(DATEDIFF(DATE(safe_conducts.last_gate_out_date), sppb.sppb_date), 0) AS st_sppb_gate_out',
                'IF(DATEDIFF(DATE(safe_conducts.last_gate_out_date), sppb.sppb_date) <= 3, 1, 0) AS on_target_sppb_gate_out',
                'bookings.status AS booking_status',
                'IF(bookings.status = "COMPLETED", "Done", "On-Progress") AS outbound_status',
                'last_descriptions.description AS description',
            ])
            ->from('uploads')
            ->join('uploads AS upload_inbounds', 'upload_inbounds.id = uploads.id_upload')
            ->join("(
                SELECT id_upload, upload_documents.document_date AS sppb_date
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE document_type = 'SPPB' AND upload_documents.is_deleted = FALSE
            ) AS sppb", 'sppb.id_upload = uploads.id')
            ->join("(
                SELECT id_upload, upload_documents.no_document AS no_invoice
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE document_type = 'Invoice' AND upload_documents.is_deleted = FALSE
            ) AS invoices", 'invoices.id_upload = uploads.id')
            ->join('bookings', 'bookings.id_upload = uploads.id', 'left')
            ->join("(
                SELECT 
                    id_booking,
                    MAX(IF(expedition_type = 'INTERNAL', security_out_date, security_in_date)) AS last_gate_out_date
                FROM safe_conducts
                GROUP BY id_booking
            ) AS safe_conducts", 'bookings.id = safe_conducts.id_booking', 'left')
            ->join("(
                SELECT id_booking, booking_statuses.description 
                FROM booking_statuses
                INNER JOIN (
                    SELECT MAX(id) AS id
                    FROM booking_statuses 
                    WHERE booking_status = 'UPDATE DESCRIPTION' 
                    GROUP BY id_booking
                ) AS last_descriptions ON last_descriptions.id = booking_statuses.id
            ) AS last_descriptions", 'last_descriptions.id_booking = bookings.id', 'left');

        if (!empty($branchId)) {
            $baseQuery->where('uploads.id_branch', $branchId);
        }

        if (key_exists('booking_status', $filters) && !empty($filters['booking_status'])) {
            if ($filters['booking_status'] == 'OUTSTANDING') {
                $baseQuery->where('bookings.status!=', 'COMPLETED');
            } else {
                $baseQuery->where('bookings.status', $filters['booking_status']);
            }
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->where('uploads.id_person', $filters['customer']);
        }

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $baseQuery->where('sppb.sppb_date>=', format_date($filters['date_from']));
        }

        return $baseQuery->get()->result_array();
    }
       
}
