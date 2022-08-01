<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') or exit('No direct script access allowed');

class ReportVehicleProductionModel extends MY_Model
{
    /**
     * ReportVehiclePerformanceModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get daily operational summary.
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getDailyOperationalProduction($filters = [])
    {
        $baseQueryVehicleType = $this->getVehicleGroupType($filters)->get_compiled_select();

        return $this->db
            ->select([
                "customer_name",
                "SUM(inbound_fcl_box) AS inbound_fcl_box",
                "SUM(inbound_lcl_vehicle) AS inbound_lcl_vehicle",
                "SUM(outbound_lcl_vehicle) AS outbound_lcl_vehicle",
                "ROUND(SUM(inbound_fcl_box_frt),2) AS inbound_fcl_box_frt",
                "ROUND(SUM(inbound_lcl_vehicle_frt),2) AS inbound_lcl_vehicle_frt",
                "ROUND(SUM(outbound_lcl_vehicle_frt),2) AS outbound_lcl_vehicle_frt",
                "GROUP_CONCAT(IF(total > 0, CONCAT(total, ' x ', vehicle_type), NULL) SEPARATOR ', ') AS description",
                "SUM(total) AS total",
                "SUM(total_frt) AS total_frt"
            ])
            ->from("({$baseQueryVehicleType}) AS vehicle_types")
            ->group_by('customer_name')
            ->get()
            ->result_array();
    }

    /**
     * Get vehicle group by type.
     *
     * @param array $filters
     * @return CI_DB_mysql_driver|CI_DB_query_builder
     */
    public function getVehicleGroupType($filters = [])
    {
        $baseQueryVehicleCustomer = $this->getVehicleGroupCustomer($filters)->get_compiled_select();

        return $this->db
            ->select([
                "customer_name",
                "IF(size = 'LCL', vehicle_type, size) AS vehicle_type",
                "size",
                "SUM(IF(category = 'INBOUND' AND size != 'LCL', total, 0)) AS inbound_fcl_box",
                "SUM(IF(category = 'INBOUND' AND size = 'LCL', total, 0)) AS inbound_lcl_vehicle",
                "SUM(IF(category = 'OUTBOUND', total, 0)) AS outbound_lcl_vehicle",
                "SUM(IF(category = 'INBOUND' AND size != 'LCL', frt, 0)) AS inbound_fcl_box_frt",
                "SUM(IF(category = 'INBOUND' AND size = 'LCL', frt, 0)) AS inbound_lcl_vehicle_frt",
                "SUM(IF(category = 'OUTBOUND', frt, 0)) AS outbound_lcl_vehicle_frt",
                "SUM(total) AS total",
                "SUM(frt) AS total_frt",
                //"SUM(IF(category = 'INBOUND' AND size != 'LCL', 1, 0)) AS inbound_fcl_box",
                //"COUNT(DISTINCT CASE WHEN category = 'INBOUND' AND size = 'LCL' THEN no_police END) AS inbound_lcl_vehicle",
                //"COUNT(DISTINCT CASE WHEN category = 'OUTBOUND' AND size = 'LCL' THEN no_police END) AS outbound_lcl_vehicle",
                //"GROUP_CONCAT(DISTINCT no_safe_conduct) AS no_safe_conduct",
                //"COUNT(DISTINCT IF(category = 'INBOUND' AND size = 'LCL', no_police, null)) AS inbound_lcl",
                //"COUNT(DISTINCT IF(category = 'OUTBOUND' AND size = 'LCL', no_police, null)) AS outbound_lcl",
            ])
            ->from("({$baseQueryVehicleCustomer}) AS vehicle_customers")
            ->group_by("customer_name, IF(size = 'LCL', vehicle_type, size), size");
    }

    /**
     * Get vehicle group by customer.
     *
     * @param array $filters
     * @return CI_DB_mysql_driver|CI_DB_query_builder
     */
    public function getVehicleGroupCustomer($filters = [])
    {
        $baseQueryVehicleTransaction = $this->getVehicleTransaction($filters)->get_compiled_select();

        return $this->db
            ->select([
                'GROUP_CONCAT(DISTINCT customer_name) AS customer_name',
                'category',
                'vehicle_type',
                'no_police',
                'size',
                'no_container',
                'COUNT(DISTINCT security_out_date) AS total',
                'SUM(frt) AS frt'
            ])
            ->from("({$baseQueryVehicleTransaction}) AS vehicles")
            ->group_by('category, vehicle_type, no_police, size, no_container');
    }

    /**
     * Get vehicle transaction.
     *
     * @param array $filters
     * @return CI_DB_query_builder
     */
    public function getVehicleTransaction($filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $baseQuery = $this->db
            ->select([
                "bookings.id_branch",
                "bookings.id_customer",
                "ref_people.name AS customer_name",
                "IF(handlings.id_handling_type = 2, 'OUTBOUND', 'INBOUND') AS category",
                "safe_conducts.no_safe_conduct",
                "IFNULL(safe_conducts.security_out_date, work_orders.completed_at) AS security_out_date", // for stripping get from completed_at
                "work_orders.no_work_order",
                "work_orders.completed_at",
                "IFNULL(ref_containers.no_container, 'LCL') AS no_container",
                "UPPER(safe_conducts.vehicle_type) AS vehicle_type",
                "safe_conducts.no_police",
                "IFNULL(ref_containers.size, 'LCL') AS size",
                "IF(IFNULL(SUM((work_order_goods.`unit_gross_weight`/1000)* work_order_goods.`quantity`), 0)
                    >IFNULL(SUM(work_order_goods.`unit_volume`*work_order_goods.`quantity`), 0), 
                    IFNULL(SUM((work_order_goods.`unit_gross_weight`/1000)*work_order_goods.`quantity`), 0), 
                    IFNULL(SUM(work_order_goods.`unit_volume`*work_order_goods.`quantity`), 0)) AS frt"
            ])
            ->from('bookings')
            ->join('ref_people', 'ref_people.id = bookings.id_customer')
            ->join('handlings', 'handlings.id_booking = bookings.id')
            ->join('work_orders', 'work_orders.id_handling = handlings.id')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id', 'left')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id', 'left')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container', 'left')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->where([
                'bookings.is_deleted' => false,
                'handlings.status' => 'APPROVED',
                'handlings.is_deleted' => false,
                'work_orders.status' => 'COMPLETED',
                'work_orders.is_deleted' => false,
                //'work_orders.completed_at >=' => '2021-08-04 16:30:04',
                //'work_orders.completed_at <=' => '2021-08-05 20:36:26',
            ])
            //->where_in('no_safe_conduct', ['SI/21/09/000971', 'SO/21/08/000177', 'SI/21/08/000188', 'SO/21/08/000169', 'SI/21/08/000162', 'SI/21/08/000163', 'SI/21/08/000143', 'SI/21/08/000144', 'SO/21/08/000178', 'SO/21/08/000179', 'SO/21/08/000166', 'SO/21/08/000181'])
            ->where_in('handlings.id_handling_type', [1, 2, 3])
            ->where('!(handlings.id_handling_type = 1 AND size IS NOT NULL)', null, false) // unload with container will excluded
            ->group_by('work_orders.id');

        $subBranch = '';
        if (!empty($branchId)) {
            if (is_array($branchId)) {
                $baseQuery->where_in('bookings.id_branch', $branchId);
                $branchIdList = implode(',', $branchId);
                $subBranch = "AND bo.id_branch IN ({$branchIdList})";
            } else {
                $baseQuery->where('bookings.id_branch', $branchId);
                $subBranch = "AND bo.id_branch = {$branchId}";
            }
        }

        // striping using completed_at
        // unload LCL & load using security_out_date
        $subCheckoutFrom = '';
        if (key_exists('checked_out_from', $filters) && !empty($filters['checked_out_from'])) {
            $baseQuery->where('IF(handlings.id_handling_type = 3, work_orders.completed_at, safe_conducts.security_out_date)>=', $filters['checked_out_from']);
            $subCheckoutFrom = "AND IF(ha.id_handling_type = 3, wo.completed_at, sf.security_out_date)>= '{$filters['checked_out_from']}'";
            // $baseQuery->where('work_orders.completed_at>=', $filters['checked_out_from']);
            // $subCheckoutFrom = "AND wo.completed_at>= '{$filters['checked_out_from']}'";
        }

        $subCheckoutTo = '';
        if (key_exists('checked_out_to', $filters) && !empty($filters['checked_out_to'])) {
            $baseQuery->where('IF(handlings.id_handling_type = 3, work_orders.completed_at, safe_conducts.security_out_date)<=', $filters['checked_out_to']);
            $subCheckoutTo = "AND IF(ha.id_handling_type = 3, wo.completed_at, sf.security_out_date)<= '{$filters['checked_out_to']}'";
            // $baseQuery->where('work_orders.completed_at<=', $filters['checked_out_to']);
            // $subCheckoutTo = "AND wo.completed_at<= '{$filters['checked_out_to']}'";
        }

        // if using completed_at as cut off then LOAD has not safe conduct will be excluded
        $baseQuery
            ->group_start()
                ->group_start()
                ->where('handlings.id_handling_type', 2)
                ->where('safe_conducts.no_safe_conduct IS NOT NULL')
                ->group_end()
                ->or_where('handlings.id_handling_type!=', 2)
            ->group_end();

        // helper query to exclude if 1 fleet contains LCL & container, then try to remove LCL items,
        // so it will count as single transaction
        $baseQuery
            ->group_start()
            ->where("IFNULL(ref_containers.no_container, 'LCL') != 'LCL'")
            ->or_where("NOT EXISTS (
                SELECT IFNULL(rc.no_container, 'LCL') AS no_container
                FROM bookings AS bo
                INNER JOIN handlings AS ha ON ha.id_booking = bo.id
                INNER JOIN work_orders AS wo ON wo.id_handling = ha.id
                LEFT JOIN work_order_containers AS woc ON woc.id_work_order = wo.id
                LEFT JOIN ref_containers AS rc ON rc.id = woc.id_container
                LEFT JOIN safe_conducts AS sf ON sf.id = wo.id_safe_conduct
                WHERE bo.is_deleted = 0
                    AND ha.status = 'APPROVED'
                    AND ha.is_deleted = 0
                    AND wo.status = 'COMPLETED'
                    AND wo.is_deleted = 0
                    AND ha.id_handling_type IN(1, 2, 3)
                    AND !(ha.id_handling_type = 1 AND size IS NOT NULL)
                    {$subBranch}
                    {$subCheckoutFrom}
                    {$subCheckoutTo}

                    AND IFNULL(sf.security_out_date, wo.completed_at) = IFNULL(safe_conducts.security_out_date, work_orders.completed_at)
                    AND safe_conducts.no_police = sf.no_police
                    AND IFNULL(rc.no_container, 'LCL') != 'LCL'
            )")
            ->group_end();

        return $baseQuery;
    }

    /**
     * Get report fleet production inbound.
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getReportFleetProductionInbound($filters = [])
    {
        $filters['category'] = 'INBOUND';
        $filters['query'] = true;
        $baseQueryTransaction = $this->getReportFleetProductionInboundVehicleGroup($filters)->get_compiled_select();

        return $this->db
            ->select([
                'id_branch',
                'branch',
                'inbound_date',
                'GROUP_CONCAT(DISTINCT data_reference) AS data_reference',
                'GROUP_CONCAT(DISTINCT completed_date) AS completed_date',
                'GROUP_CONCAT(DISTINCT sppb_date) AS sppb_date',
                'GROUP_CONCAT(DISTINCT no_reference) AS no_reference',
                'GROUP_CONCAT(DISTINCT customer_name) AS customer_name',
                "GROUP_CONCAT(IF(total > 0, CONCAT(total, ' x ', container_size), NULL) SEPARATOR ', ') AS party",
                'GROUP_CONCAT(DISTINCT no_container) AS no_container',
            ])
            ->from("({$baseQueryTransaction}) AS transactions")
            ->group_by('branch, inbound_date, no_police')
            ->get()
            ->result_array();
    }

    /**
     * Get report fleet production inbound for intermediate query.
     *
     * @param array $filters
     * @return array|CI_DB_query_builder
     */
    private function getReportFleetProductionInboundVehicleGroup($filters = [])
    {
        $baseQueryTransaction = $this->getReportFleetProductionActivity($filters)->get_compiled_select();

        $baseQuery = $this->db
            ->select([
                'id_branch',
                'branch',
                'in_out_date AS inbound_date',
                'no_police',
                'GROUP_CONCAT(DISTINCT data_reference) AS data_reference',
                'GROUP_CONCAT(DISTINCT completed_date) AS completed_date',
                'GROUP_CONCAT(DISTINCT sppb_date) AS sppb_date',
                'GROUP_CONCAT(DISTINCT no_reference) AS no_reference',
                'GROUP_CONCAT(DISTINCT customer_name) AS customer_name',
                'GROUP_CONCAT(DISTINCT no_container) AS no_container',
                'container_size',
                'COUNT(container_size) AS total',
            ])
            ->from("({$baseQueryTransaction}) AS transaction_groups")
            ->group_by('branch, in_out_date, no_police, container_size');

        if ($filters['query'] ?? false) {
            return $baseQuery;
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get report fleet production outbound.
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getReportFleetProductionOutbound($filters = [])
    {
        $filters['category'] = 'OUTBOUND';
        $filters['query'] = true;
        $baseQueryTransaction = $this->getReportFleetProductionActivity($filters)->get_compiled_select();

        return $this->db
            ->select([
                'id_branch',
                'branch',
                'in_out_date AS outbound_date',
                'no_police',
                'GROUP_CONCAT(DISTINCT no_reference) AS no_reference',
                'GROUP_CONCAT(DISTINCT no_reference_inbound) AS no_reference_inbound',
                'vehicle_type',
                'GROUP_CONCAT(DISTINCT customer_name) AS customer_name',
                'driver',
            ])
            ->from("({$baseQueryTransaction}) AS transactions")
            ->group_by('branch, in_out_date, no_police, vehicle_type, driver')
            ->get()
            ->result_array();
    }

    /**
     * Get report fleet production
     * @param array $filters
     * @return CI_DB_query_builder|array
     */
    public function getReportFleetProductionActivity($filters = [])
    {
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $baseQuery = $this->db
            ->select([
                "ref_branches.id AS id_branch",
                "ref_branches.branch",
                // do optimize version of query by join two table instead single join with OR conditions
                // LOAD job, take outbound_date from security_out_date
                // UNLOAD & STRIPPING, take inbound_date from safe_conduct in/out depends on expedition_type field (internal/external)
                // use safe_conduct_inbounds for fallback to find out which safe conduct for the container inside stripping job
                "IF(handlings.id_handling_type = 2,
                    safe_conducts.security_out_date, 
                    IF(safe_conducts.no_safe_conduct IS NULL,
                        IF(safe_conduct_inbounds.expedition_type = 'INTERNAL', safe_conduct_inbounds.security_out_date, safe_conduct_inbounds.security_in_date),
                        IF(safe_conducts.expedition_type = 'INTERNAL', safe_conducts.security_out_date, safe_conducts.security_in_date)
                    )
                ) AS in_out_date",
                "IF(handlings.id_handling_type = 3, work_orders.no_work_order, safe_conducts.no_safe_conduct) AS data_reference",
                //"IF(handlings.id_handling_type = 3, work_orders.completed_at, safe_conducts.security_out_date) AS completed_date",
                "IF(handlings.id_handling_type = 2, safe_conducts.security_out_date, work_orders.completed_at) AS completed_date",
                "(
                    SELECT MAX(upload_documents.document_date) AS sppb_date             
                    FROM upload_documents
                    INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                    WHERE ref_document_types.document_type = 'SPPB'
                        AND upload_documents.is_deleted = FALSE
                        AND upload_documents.id_upload = bookings.id_upload
                ) AS sppb_date",
                "bookings.no_reference",
                "IFNULL((
                    SELECT GROUP_CONCAT(no_reference)
                    FROM booking_references
                    INNER JOIN bookings AS booking_inbounds ON booking_inbounds.id = booking_references.id_booking_reference
                    WHERE booking_references.id_booking = bookings.id
                ), bookings.no_reference) AS no_reference_inbound",
                "ref_people.name AS customer_name",
                "UPPER(safe_conducts.vehicle_type) AS vehicle_type",
                "safe_conducts.no_police",
                "safe_conducts.driver",
                "IFNULL(ref_containers.no_container, 'LCL') AS no_container",
                "IFNULL(ref_containers.size, 'LCL') AS container_size",
                "IF(ref_containers.id IS NULL, 'LCL', CONCAT(1, 'x', ref_containers.size)) AS party"
            ])
            ->from('bookings')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch')
            ->join('ref_people', 'ref_people.id = bookings.id_customer')
            ->join('handlings', 'handlings.id_booking = bookings.id')
            ->join('work_orders', 'work_orders.id_handling = handlings.id')
            ->join('work_order_containers', 'work_order_containers.id_work_order = work_orders.id', 'left')
            ->join('ref_containers', 'ref_containers.id = work_order_containers.id_container', 'left')
            // optimize query by join safe_conducts twice instead single join with OR conditions
            // could achieve same result with on condition ((sf.id_booking = bo.id AND sf.id_container = woc.id_container) OR wo.id_safe_conduct = sf.id)
            ->join("(
                SELECT 
                    safe_conducts.no_safe_conduct,
                    safe_conducts.expedition_type,
                    safe_conducts.security_out_date, 
                    safe_conducts.security_in_date,
                    safe_conducts.id_booking, 
                    safe_conduct_containers.id_container
                FROM safe_conducts
                INNER JOIN safe_conduct_containers ON safe_conduct_containers.id_safe_conduct = safe_conducts.id
                WHERE safe_conducts.is_deleted = FALSE AND safe_conducts.type = 'INBOUND'
            ) AS safe_conduct_inbounds", 'safe_conduct_inbounds.id_booking = bookings.id 
	            AND safe_conduct_inbounds.id_container = ref_containers.id', 'left')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->where([
                'bookings.is_deleted' => false,
                'handlings.status' => 'APPROVED',
                'handlings.is_deleted' => false,
                'work_orders.status' => 'COMPLETED',
                'work_orders.is_deleted' => false,
            ])
            ->where_in('handlings.id_handling_type', [1, 2, 3]) // job UNLOAD, LOAD, STRIPPING only
            ->where('!(handlings.id_handling_type = 1 AND ref_containers.id IS NOT NULL)', null, false); // unload with container will be excluded

        if (!empty($branchId)) {
            if (is_array($branchId)) {
                $baseQuery->where_in('bookings.id_branch', $branchId);
            } else {
                $baseQuery->where('bookings.id_branch', $branchId);
            }
        }

        if (key_exists('category', $filters) && !empty($filters['category'])) {
            if ($filters['category'] == 'OUTBOUND') {
                $baseQuery->where('handlings.id_handling_type', 2);
            }
            if ($filters['category'] == 'INBOUND') {
                $baseQuery->where('handlings.id_handling_type!=', 2);
            }
        }

        // another approach, do direct join into ref_operation_cut_offs and add conditions by date,
        // first make cut off data in single row then join with transaction so it can be filtered,
        // bellow is simple version by adding multiple OR conditions
        if (key_exists('date_cut_off', $filters) && !empty($filters['date_cut_off'])) {
            $dateCategory = 'completed_at';
            if (($filters['category'] ?? '') == 'INBOUND') {
                // should be optimized! because INDEX will be disabled on function expression
                //$dateCategory = 'IF(handlings.id_handling_type = 3, work_orders.completed_at, safe_conducts.security_out_date)';
                $dateCategory = 'work_orders.completed_at';
            }
            if (($filters['category'] ?? '') == 'OUTBOUND') {
                $dateCategory = 'safe_conducts.security_out_date';
            }

            // OR conditions is performance killer, do UNION to improve query performance,
            // or run multiple query with optimized index
            $baseQuery->group_start();
            foreach (array_values($filters['date_cut_off']) as $index => $dateCutOff) {
                $baseQuery
                    ->or_group_start()
                    ->where('ref_branches.id', $dateCutOff['id_branch'])
                    ->where($dateCategory . '>=', format_date($dateCutOff['start'], 'Y-m-d H:i:s'))
                    ->where($dateCategory . '<=', format_date($dateCutOff['end'], 'Y-m-d H:i:s'))
                    ->group_end();

                /*
                Alternative query category INBOUND to enable INDEX, but still performance killer because OR conditions
                $baseQuery
                    ->or_group_start()
                        ->where('ref_branches.id', $dateCutOff['id_branch'])
                        ->group_start()
                            ->group_start()
                                ->where('handlings.id_handling_type', 3)
                                ->where('work_orders.completed_at>=', format_date($dateCutOff['start'], 'Y-m-d H:i:s'))
                                ->where('work_orders.completed_at<=', format_date($dateCutOff['end'], 'Y-m-d H:i:s'))
                            ->group_end()
                            ->or_group_start()
                                ->where('handlings.id_handling_type!=', 3)
                                ->where('safe_conducts.security_out_date>=', format_date($dateCutOff['start'], 'Y-m-d H:i:s'))
                                ->where('safe_conducts.security_out_date<=', format_date($dateCutOff['end'], 'Y-m-d H:i:s'))
                            ->group_end()
                        ->group_end()
                    ->group_end();
                */
            }
            $baseQuery->group_end();
        }

        if ($filters['query'] ?? false) {
            return $baseQuery;
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Export vehicle production to excel.
     *
     * @param $filters
     * @param $inbounds
     * @param $outbounds
     * @param array $dataRanges
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportFleetProductionActivity($filters, $inbounds, $outbounds, $dataRanges = [])
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator($this->config->item('app_name'))
            ->setLastModifiedBy($this->config->item('app_name'))
            ->setTitle('Transporter')
            ->setSubject('Data export: Fleet Production')
            ->setDescription('Data export generated by: ' . $this->config->item('app_name'));

        $spreadsheet->setActiveSheetIndex(0);
        $activeSheet = $spreadsheet->getActiveSheet()->setTitle('Fleet Production');

        // title
        $activeSheet->setCellValue('A1', 'ACTIVITY DAILY REPORT');
        $activeSheet->mergeCells('A1:D1');
        $activeSheet->setCellValue('A2', 'Date: ' . ($filters['date_from'] ?? '') . ' - ' . ($filters['date_to'] ?? $filters['date_from'] ?? ''));
        $activeSheet->mergeCells('A2:D2');

        $activeSheet->getStyle('A1:A1')->applyFromArray(['font' => ['size' => 16, 'bold' => true]]);
        $activeSheet->getStyle('A2:A2')->applyFromArray(['font' => ['bold' => true]]);

        // inbound
        $activeSheet->setCellValue('A4', 'INBOUND');
        $activeSheet->mergeCells('A4:B4');
        $activeSheet->setCellValue('A5', 'No');
        $activeSheet->setCellValue('B5', 'Branch');
        $activeSheet->setCellValue('C5', 'Cut Off From');
        $activeSheet->setCellValue('D5', 'Cut Off To');
        $activeSheet->setCellValue('E5', 'Inbound Date');
        $activeSheet->setCellValue('F5', 'Data Reference');
        $activeSheet->setCellValue('G5', 'Complete Date');
        $activeSheet->setCellValue('H5', 'SPPB Date');
        $activeSheet->setCellValue('I5', 'Inbound Reference');
        $activeSheet->setCellValue('J5', 'Customer Name');
        $activeSheet->setCellValue('K5', 'Party');
        $activeSheet->setCellValue('L5', 'No Container');

        $activeSheet->getStyle('A4:K5')->applyFromArray(['font' => ['bold' => true]]);
        $activeSheet->getStyle('A5:K5')->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFF00']],
            'font' => ['bold' => true]
        ]);

        $rowPointer = 6;
        foreach ($inbounds as $index => $inbound) {
            $activeSheet->setCellValue("A" . ($rowPointer + $index), $index + 1);
            $activeSheet->setCellValue("B" . ($rowPointer + $index), $inbound['branch']);
            $activeSheet->setCellValue("C" . ($rowPointer + $index), $filters['date_cut_off'][$inbound['id_branch']]['start']);
            $activeSheet->setCellValue("D" . ($rowPointer + $index), $filters['date_cut_off'][$inbound['id_branch']]['end']);
            $activeSheet->setCellValue("E" . ($rowPointer + $index), $inbound['inbound_date'] ?? $inbound['in_out_date']);
            $activeSheet->setCellValue("F" . ($rowPointer + $index), $inbound['data_reference']);
            $activeSheet->setCellValue("G" . ($rowPointer + $index), $inbound['completed_date']);
            $activeSheet->setCellValue("H" . ($rowPointer + $index), $inbound['sppb_date']);
            $activeSheet->setCellValue("I" . ($rowPointer + $index), $inbound['no_reference']);
            $activeSheet->setCellValue("J" . ($rowPointer + $index), $inbound['customer_name']);
            $activeSheet->setCellValue("K" . ($rowPointer + $index), $inbound['party']);
            $activeSheet->setCellValue("L" . ($rowPointer + $index), $inbound['no_container']);
        }

        // inbound table border
        $activeSheet->getStyle('A5:L' . ($rowPointer + count($inbounds) - 1))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        //$activeSheet->setAutoFilter("A5:L5");

        // outbound
        $rowPointer += count($inbounds) + 2;
        $activeSheet->setCellValue('A' . $rowPointer, 'OUTBOUND');
        $activeSheet->mergeCells("A{$rowPointer}:B{$rowPointer}");

        $activeSheet->getStyle("A{$rowPointer}:K" . ($rowPointer + 1))->applyFromArray(['font' => ['bold' => true]]);
        $activeSheet->getStyle("A" . ($rowPointer + 1) . ":K" . ($rowPointer + 1))->applyFromArray([
            'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFFF00']],
            'font' => ['bold' => true]
        ]);

        $rowPointer += 1;
        $activeSheet->setCellValue("A{$rowPointer}", 'No');
        $activeSheet->setCellValue("B{$rowPointer}", 'Branch');
        $activeSheet->setCellValue("C{$rowPointer}", 'Cut Off From');
        $activeSheet->setCellValue("D{$rowPointer}", 'Cut Off To');
        $activeSheet->setCellValue("E{$rowPointer}", 'Outbound Date');
        $activeSheet->setCellValue("F{$rowPointer}", 'No Police');
        $activeSheet->setCellValue("G{$rowPointer}", 'Outbound Reference');
        $activeSheet->setCellValue("H{$rowPointer}", 'Inbound Reference');
        $activeSheet->setCellValue("I{$rowPointer}", 'Vehicle Type');
        $activeSheet->setCellValue("J{$rowPointer}", 'Customer Name');
        $activeSheet->setCellValue("K{$rowPointer}", 'Driver Name');

        $rowPointer += 1;
        foreach ($outbounds as $index => $outbound) {
            $activeSheet->setCellValue("A" . ($rowPointer + $index), $index + 1);
            $activeSheet->setCellValue("B" . ($rowPointer + $index), $outbound['branch']);
            $activeSheet->setCellValue("C" . ($rowPointer + $index), $filters['date_cut_off'][$outbound['id_branch']]['start']);
            $activeSheet->setCellValue("D" . ($rowPointer + $index), $filters['date_cut_off'][$outbound['id_branch']]['end']);
            $activeSheet->setCellValue("E" . ($rowPointer + $index), $outbound['outbound_date'] ?? $outbound['in_out_date']);
            $activeSheet->setCellValue("F" . ($rowPointer + $index), $outbound['no_police']);
            $activeSheet->setCellValue("G" . ($rowPointer + $index), $outbound['no_reference']);
            $activeSheet->setCellValue("H" . ($rowPointer + $index), $outbound['no_reference_inbound']);
            $activeSheet->setCellValue("I" . ($rowPointer + $index), $outbound['vehicle_type']);
            $activeSheet->setCellValue("J" . ($rowPointer + $index), $outbound['customer_name']);
            $activeSheet->setCellValue("K" . ($rowPointer + $index), $outbound['driver']);
        }

        // outbound table border
        $activeSheet->getStyle("A" . ($rowPointer - 1) . ":K" . ($rowPointer + count($outbounds) - 1))
            ->getBorders()
            ->getAllBorders()
            ->setBorderStyle(Border::BORDER_THIN);

        //$activeSheet->setAutoFilter("A" . ($rowPointer - 1) . ":K" . ($rowPointer - 1));

        // set auto column width
        $columnIterator = $spreadsheet->getActiveSheet()->getColumnIterator();
        foreach ($columnIterator as $column) {
            $spreadsheet->getActiveSheet()
                ->getColumnDimension($column->getColumnIndex())
                ->setAutoSize(true);
        }

        // additional information about date range branch cut off
        $rowPointer += count($outbounds) + 2;
        if (!empty($dataRanges)) {
            $activeSheet->setCellValue("B" . $rowPointer, 'Branch Cut Off:');
            $rowPointer++;
            foreach (array_values($dataRanges) as $index => $dataRange) {
                $activeSheet->setCellValue("B" . ($rowPointer + $index), $dataRange['branch']);
                $activeSheet->setCellValue("C" . ($rowPointer + $index), "Start " . format_date($dataRange['start'], 'Y-m-d H:i') . " - End " . format_date($dataRange['end'], 'Y-m-d H:i'));
                $activeSheet->mergeCells("C" . ($rowPointer + $index) . ":E" . ($rowPointer + $index));
            }
        }

        $excelWriter = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Fleet Production.xlsx"');
        $excelWriter->save('php://output');
    }
}
