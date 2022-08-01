<?php

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') OR exit('No direct script access allowed');

class ReportDocumentProductionModel extends MY_Model
{
    
    protected $tableScheduleHoliday = 'schedule_holidays';
    protected $tableEmployee = 'ref_employees';
    protected $tableEmployeePositionHistory = 'employee_position_histories';
    protected $tableScheduleDivision = 'schedule_division';
    protected $tableOvertime = 'overtimes';

    
    public function __construct()
    {
        parent::__construct();
        if ($this->config->item('sso_enable')) {
            $this->tableScheduleHoliday = env('DB_HR_DATABASE') . '.schedule_holidays';
            $this->tableEmployee = env('DB_HR_DATABASE') . '.ref_employees';
            $this->tableEmployeePositionHistory = env('DB_HR_DATABASE') . '.employee_position_histories';
            $this->tableScheduleDivision = env('DB_HR_DATABASE') . '.schedule_division';
            $this->tableOvertime = env('DB_HR_DATABASE') . '.overtimes';
        }
    }
    /**
     * Get document production report.
     *
     * @param array $filters
     * @return array|int
     */
    public function getReportDocumentProduction($filters = [])
    {
        $baseQuery = $this->db
            ->select([
                'query_draft.*',
                'query_sppb.jumlah_sppb',
                'admin_document.avg_count_admin',
                'overtime_comp.lembur',
                'work_day.count_day',
            ])
            ->from("(SELECT YEAR(hari_kerja.selected_date)AS tahun, WEEK(hari_kerja.selected_date,1) AS minggu, COUNT(draft.id) AS jumlah_draft FROM 
                (SELECT ref_dates.date AS selected_date FROM ref_dates
                    WHERE ref_dates.date BETWEEN DATE_FORMAT(NOW() ,'%Y-01-01') AND CURRENT_DATE 
                    AND ref_dates.date NOT IN 
                    (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday.") AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja
                LEFT JOIN 
                (SELECT upload_document_files.id,upload_documents.id AS id_upload_document,ref_document_types.`document_type`,upload_document_files.created_at 
                FROM (
                    SELECT MIN(id) AS id, MIN(created_at) AS created_at, id_upload_document 
                    FROM upload_document_files
                    GROUP BY id_upload_document
                ) AS upload_document_files
                LEFT JOIN (select * from upload_documents where is_deleted = false) AS upload_documents ON upload_document_files.`id_upload_document` = upload_documents.id
                LEFT JOIN uploads ON upload_documents.id_upload = uploads.id
                LEFT JOIN ref_people ON ref_people.id = uploads.id_person
                LEFT JOIN ref_document_types ON ref_document_types.`id` = upload_documents.`id_document_type`
                WHERE ref_people.id != '74' AND ref_document_types.`document_type` LIKE '%Draft') AS draft ON DATE(draft.created_at) = hari_kerja.selected_date
                GROUP BY YEAR(hari_kerja.selected_date), WEEK(hari_kerja.selected_date,1)) AS query_draft")
            ->join("(SELECT YEAR(hari_kerja.selected_date)AS tahun, WEEK(hari_kerja.selected_date,1) AS minggu,COUNT(sppb.document_type) AS jumlah_sppb FROM 
                (SELECT ref_dates.date AS selected_date FROM ref_dates
                    WHERE ref_dates.date BETWEEN DATE_FORMAT(NOW() ,'%Y-01-01') AND CURRENT_DATE 
                    AND ref_dates.date NOT IN 
                    (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday.") AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja
                LEFT JOIN 
                (SELECT COUNT(ref_document_types.`document_type`) AS count_sppb, uploads.id,ref_document_types.`document_type`,upload_documents.created_at,doc_sppb.created_at AS sppb_upload FROM uploads
                LEFT JOIN (select * from upload_documents where is_deleted = false) AS upload_documents ON upload_documents.`id_upload` = uploads.id
                LEFT JOIN ref_people ON ref_people.id = uploads.id_person
                LEFT JOIN ref_document_types ON ref_document_types.`id` = upload_documents.`id_document_type`
                LEFT JOIN (SELECT upload_documents.no_document, upload_documents.id_upload,upload_documents.created_at FROM upload_documents
                    LEFT JOIN ref_document_types ON ref_document_types.`id` = upload_documents.`id_document_type`
                    WHERE ref_document_types.`document_type` LIKE '%SPPB%') AS doc_sppb ON doc_sppb.id_upload = uploads.id
                WHERE ref_people.id != '74' AND upload_documents.`is_deleted`!=1 AND (ref_document_types.`document_type` LIKE '%SPPB%' OR ref_document_types.`document_type` LIKE '%Draft')
                GROUP BY uploads.id
                HAVING count_sppb>1) AS sppb ON DATE(sppb.sppb_upload) = hari_kerja.selected_date
                GROUP BY YEAR(hari_kerja.selected_date), WEEK(hari_kerja.selected_date,1)) AS query_sppb", 'query_sppb.tahun = query_draft.tahun AND query_sppb.minggu = query_draft.minggu', 'left')
            ->join("(SELECT tahun,minggu, ROUND(AVG(count_admin),2) AS avg_count_admin FROM 
                (SELECT YEAR(hari_kerja.selected_date)AS tahun, WEEK(hari_kerja.selected_date,1) AS minggu, DAY(hari_kerja.selected_date) AS hari, COUNT(ref_employees.id) AS count_admin FROM 
                (SELECT ref_dates.date AS selected_date FROM ref_dates
                    WHERE ref_dates.date BETWEEN DATE_FORMAT(NOW() ,'%Y-01-01') AND CURRENT_DATE 
                    AND ref_dates.date NOT IN 
                    (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday.") AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja
                LEFT JOIN ".$this->tableEmployee." AS ref_employees ON ref_employees.`enter_date` <= hari_kerja.selected_date AND (ref_employees.`quit_date` IS NULL OR ref_employees.`quit_date`>=hari_kerja.selected_date)
                LEFT JOIN ".$this->tableScheduleDivision." AS `schedule_division` ON `schedule_division`.`id_employee` = `ref_employees`.`id` AND `schedule_division`.`date` = `hari_kerja`.`selected_date`
                INNER JOIN ".$this->tableEmployeePositionHistory." AS position_history ON position_history.id_employee = ref_employees.id AND ((position_history.start_date <= hari_kerja.selected_date AND position_history.end_date > hari_kerja.selected_date) OR (position_history.start_date <= hari_kerja.selected_date AND position_history.end_date IS NULL))
                INNER JOIN document_positions ON document_positions.`id_position` = `position_history`.`id_position`
                WHERE schedule_division.id_schedule != '1'
                GROUP BY YEAR(hari_kerja.selected_date), WEEK(hari_kerja.selected_date,1), DAY(hari_kerja.selected_date)) AS query_admin
                GROUP BY tahun,minggu) AS admin_document", 'query_sppb.tahun = admin_document.tahun AND query_sppb.minggu = admin_document.minggu', 'left')
            ->join("(SELECT YEAR(hari_kerja.selected_date)AS tahun, WEEK(hari_kerja.selected_date,1) AS minggu, SUM(overtimes.overtime_duration) AS lembur FROM 
                (SELECT ref_dates.date AS selected_date FROM ref_dates
                    WHERE ref_dates.date BETWEEN DATE_FORMAT(NOW() ,'%Y-01-01') AND CURRENT_DATE 
                    AND ref_dates.date NOT IN 
                    (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday.") AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja
                LEFT JOIN ".$this->tableEmployee." AS ref_employees ON ref_employees.`enter_date` <= hari_kerja.selected_date AND (ref_employees.`quit_date` IS NULL OR ref_employees.`quit_date`>=hari_kerja.selected_date)
                LEFT JOIN ".$this->tableScheduleDivision." AS `schedule_division` ON `schedule_division`.`id_employee` = `ref_employees`.`id` AND `schedule_division`.`date` = `hari_kerja`.`selected_date`
                INNER JOIN ".$this->tableEmployeePositionHistory." AS position_history ON position_history.id_employee = ref_employees.id AND ((position_history.start_date <= hari_kerja.selected_date AND position_history.end_date > hari_kerja.selected_date) OR (position_history.start_date <= hari_kerja.selected_date AND position_history.end_date IS NULL))
                INNER JOIN document_positions ON document_positions.`id_position` = `position_history`.`id_position`
                INNER JOIN ".$this->tableOvertime." AS overtimes ON overtimes.id_employee = ref_employees.id AND (DATE(overtimes.`overtime_start`)=hari_kerja.selected_date)
                WHERE overtimes.`overtime_type` = 'REALIZATION'
                GROUP BY YEAR(hari_kerja.selected_date), WEEK(hari_kerja.selected_date,1)) 
                AS overtime_comp",'overtime_comp.tahun = admin_document.tahun AND overtime_comp.minggu = admin_document.minggu','left')
            ->join("(SELECT YEAR(work_day.selected_date)AS tahun, WEEK(work_day.selected_date,1) AS minggu, count(work_day.selected_date) AS count_day FROM 
                (SELECT ref_dates.date AS selected_date FROM ref_dates
                WHERE ref_dates.date BETWEEN DATE_FORMAT(NOW() ,'%Y-01-01') AND CURRENT_DATE 
                AND ref_dates.date NOT IN 
                (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday.") AND DAYOFWEEK(ref_dates.date) != 1) AS work_day
                GROUP BY YEAR(work_day.selected_date), WEEK(work_day.selected_date,1)) AS work_day","query_draft.tahun = work_day.tahun AND query_draft.minggu = work_day.minggu",'left');
        return $baseQuery->get()->result_array();
    }

    /**
     * Get draft detail report.
     *
     * @param array $filters
     * @return array|int
     */
    public function getDraftDetail($filters = [])
    {
        $baseQuery = $this->db->select([
            'selected_date',
            'count_draft',
            'document_type',
            'no_upload',
            'id_branch',
            'customer_name',
            'created_at',
            'id',
            'no_document',
            'description AS aju_desc'
        ])
            ->from("(SELECT ref_dates.date AS selected_date FROM ref_dates
                        WHERE ref_dates.date BETWEEN DATE_FORMAT(NOW() ,'%Y-01-01') AND CURRENT_DATE
                        AND ref_dates.date NOT IN 
                        (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday.") AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja")
            ->join("(SELECT 1 AS count_draft,uploads.no_upload, uploads.description, uploads.id_branch,ref_people.name AS customer_name, upload_document_files.id AS id_upload_document_file,upload_documents.id, upload_documents.no_document, ref_document_types.`document_type`,upload_document_files.created_at 
                FROM (
                    SELECT MIN(id) AS id, MIN(created_at) AS created_at, id_upload_document 
                    FROM upload_document_files
                    GROUP BY id_upload_document
                ) AS upload_document_files
                LEFT JOIN (SELECT * FROM upload_documents WHERE is_deleted = false) AS upload_documents ON upload_document_files.`id_upload_document` = upload_documents.id
                LEFT JOIN ref_document_types ON ref_document_types.`id` = upload_documents.`id_document_type`
                LEFT JOIN uploads ON uploads.id = upload_documents.id_upload
                LEFT JOIN ref_people ON ref_people.id = uploads.id_person
                WHERE ref_people.id != '74' AND ref_document_types.`document_type` LIKE '%Draft') AS draft" , "DATE(draft.created_at) = hari_kerja.selected_date");
        
        if (key_exists('week', $filters) && !empty($filters['week'])) {
            $baseQuery->where("WEEK(hari_kerja.selected_date,1)", $filters['week'])
                    ->where("YEAR(hari_kerja.selected_date)",$filters['year']);
        }
        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            $baseQuery->where("ref_branches.`id`", $filters['branch']);
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get sppb detail report.
     *
     * @param array $filters
     * @return array|int
     */
    public function getSppbDetail($filters = [])
    {
        $baseQuery = $this->db->select([
            'selected_date',
            'count_sppb',
            'document_type',
            'no_upload',
            'id_branch',
            'customer_name',
            'created_at',
            'id',
            'sppb_upload',
            'DATE(sppb_upload) AS sppb_upload_date',
            'no_document',
            'description AS aju_desc'
        ])
            ->from("(SELECT ref_dates.date AS selected_date FROM ref_dates
                        WHERE ref_dates.date BETWEEN DATE_FORMAT(NOW() ,'%Y-01-01') AND CURRENT_DATE
                        AND ref_dates.date NOT IN 
                        (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday.") AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja")
            ->join("(SELECT COUNT(ref_document_types.`document_type`) AS count_sppb, uploads.id, uploads.no_upload,uploads.id_branch,ref_document_types.`document_type`,upload_documents.created_at,ref_people.name AS customer_name, doc_sppb.created_at AS sppb_upload, doc_sppb.no_document,uploads.description FROM uploads
                LEFT JOIN upload_documents ON upload_documents.`id_upload` = uploads.id
                LEFT JOIN ref_document_types ON ref_document_types.`id` = upload_documents.`id_document_type`
                LEFT JOIN ref_people ON ref_people.id = uploads.id_person
                LEFT JOIN (SELECT upload_documents.no_document, upload_documents.id_upload,upload_documents.created_at FROM upload_documents
                    LEFT JOIN ref_document_types ON ref_document_types.`id` = upload_documents.`id_document_type`
                    WHERE ref_document_types.`document_type` LIKE '%SPPB%') AS doc_sppb ON doc_sppb.id_upload = uploads.id
                WHERE ref_people.id != '74' AND upload_documents.`is_deleted`!=1 AND (ref_document_types.`document_type` LIKE '%SPPB%' OR ref_document_types.`document_type` LIKE '%Draft')
                GROUP BY uploads.id
                HAVING count_sppb>1) AS sppb" , "DATE(sppb.sppb_upload) = hari_kerja.selected_date","left");
        
        if (key_exists('week', $filters) && !empty($filters['week'])) {
            $baseQuery->where("WEEK(hari_kerja.selected_date,1)", $filters['week'])
                    ->where("YEAR(hari_kerja.selected_date)",$filters['year']);
        }
        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            $baseQuery->where("ref_branches.`id`", $filters['branch']);
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get compliance detail report.
     *
     * @param array $filters
     * @return array|int
     */
    public function getCompDetail($filters = [])
    {
        $baseQuery = $this->db->select([
            'selected_date',
            'DAYNAME(selected_date) AS nama_hari',
            'COUNT(ref_employees.name) AS count_comp',
            "GROUP_CONCAT(ref_employees.name ORDER BY ref_employees.id SEPARATOR ', ') AS name_comp",
        ])
            ->from("(SELECT ref_dates.date AS selected_date FROM ref_dates
                        WHERE ref_dates.date BETWEEN DATE_FORMAT(NOW() ,'%Y-01-01') AND CURRENT_DATE
                        AND ref_dates.date NOT IN 
                        (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday.") AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja")
            ->join($this->tableEmployee." AS ref_employees", "ref_employees.`enter_date` <= hari_kerja.selected_date AND (ref_employees.`quit_date` IS NULL OR ref_employees.`quit_date`>=hari_kerja.selected_date)","left")
            ->join($this->tableScheduleDivision." AS schedule_division", "schedule_division.id_employee = ref_employees.id AND schedule_division.date = hari_kerja.selected_date","left")
            ->join($this->tableEmployeePositionHistory." AS position_history","position_history.id_employee = ref_employees.id AND ((position_history.start_date <= hari_kerja.selected_date AND position_history.end_date > hari_kerja.selected_date) OR (position_history.start_date <= hari_kerja.selected_date AND position_history.end_date IS NULL))","inner")
            ->join("document_positions","document_positions.`id_position` = `position_history`.`id_position`","inner")
            ->where("schedule_division.id_schedule != '1'")
            ->group_by("YEAR(hari_kerja.selected_date), WEEK(hari_kerja.selected_date), DAY(hari_kerja.selected_date)");
        
        if (key_exists('week', $filters) && !empty($filters['week'])) {
            $baseQuery->where("WEEK(hari_kerja.selected_date,1)", $filters['week'])
                    ->where("YEAR(hari_kerja.selected_date)",$filters['year']);
        }
        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            $baseQuery->where("ref_branches.`id`", $filters['branch']);
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get overtime detail report.
     *
     * @param array $filters
     * @return array|int
     */
    public function getOvertimeDetail($filters = [])
    {
        $baseQuery = $this->db->select([
            'selected_date',
            'ref_employees.name',
            'overtimes.overtime_duration',
            'IF(overtimes.overtime_duration is not null, ROUND(overtimes.overtime_duration/60,2) , 0) AS overtime_duration_hour',
            'overtimes.id',
        ])
            ->from("(SELECT ref_dates.date AS selected_date FROM ref_dates
                        WHERE ref_dates.date BETWEEN DATE_FORMAT(NOW() ,'%Y-01-01') AND CURRENT_DATE
                        AND ref_dates.date NOT IN 
                        (SELECT ".$this->tableScheduleHoliday.".`date` FROM ".$this->tableScheduleHoliday.") AND DAYOFWEEK(ref_dates.date) != 1) AS hari_kerja")
            ->join($this->tableEmployee." AS ref_employees", "ref_employees.`enter_date` <= hari_kerja.selected_date AND (ref_employees.`quit_date` IS NULL OR ref_employees.`quit_date`>=hari_kerja.selected_date)","left")
            ->join($this->tableScheduleDivision." AS schedule_division", "schedule_division.id_employee = ref_employees.id AND schedule_division.date = hari_kerja.selected_date","left")
            ->join($this->tableEmployeePositionHistory." AS position_history","position_history.id_employee = ref_employees.id AND ((position_history.start_date <= hari_kerja.selected_date AND position_history.end_date > hari_kerja.selected_date) OR (position_history.start_date <= hari_kerja.selected_date AND position_history.end_date IS NULL))","inner")
            ->join("document_positions","document_positions.`id_position` = `position_history`.`id_position`","inner")
            ->join($this->tableOvertime." AS overtimes", "overtimes.id_employee = ref_employees.id AND (DATE(overtimes.`overtime_start`)=hari_kerja.selected_date)",'inner')
            ->where("schedule_division.id_schedule != '1'")
            ->where("overtimes.`overtime_type` = 'REALIZATION'");
        
        if (key_exists('week', $filters) && !empty($filters['week'])) {
            $baseQuery->where("WEEK(hari_kerja.selected_date,1)", $filters['week'])
                    ->where("YEAR(hari_kerja.selected_date)",$filters['year']);
        }
        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            $baseQuery->where("ref_branches.`id`", $filters['branch']);
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get safe conduct detail by transporter.
     *
     * @param array $filters
     * @return array
     */
    public function getReportTransporterDetail($filters = [])
    {
        $baseQuery = $this->db
            ->select([
                'safe_conducts.*',
                'ref_vehicles.id AS id_vehicle',
                'ref_vehicles.vehicle_name',
                'ref_vehicles.vehicle_type',
                'ref_vehicles.no_plate',
                'ref_vehicles.status',
                'work_orders.id AS id_work_order',
                'work_orders.no_work_order',
                'bookings.no_reference',
                'ref_people.name AS customer_name',
                'ref_branches.branch',
                'ref_branches.id AS id_branch',
            ])
            ->from('safe_conducts')
            ->join("ref_vehicles", 'ref_vehicles.no_plate = safe_conducts.no_police', 'left')
            ->join('bookings', 'bookings.id = safe_conducts.id_booking', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->join('ref_people', 'ref_people.id = bookings.id_customer', 'left')
            ->join('work_orders', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('handlings', 'work_orders.id_handling = handlings.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->group_start()
            ->where_in('ref_handling_types.multiplier_goods', ['1', '-1'])
            ->or_where_in('ref_handling_types.multiplier_container', ['1', '-1'])
            ->group_end()
            ->where('expedition_type', "INTERNAL")
            ->where('safe_conducts.is_deleted', false)
            ->where('ref_vehicles.is_deleted', false)
            ->order_by('safe_conducts.id_safe_conduct_group', 'desc')
            ->order_by('safe_conducts.created_at', 'desc');

        if (key_exists('vehicle', $filters) && !empty($filters['vehicle'])) {
            $baseQuery->where_in('ref_vehicles.no_plate', $filters['vehicle']);
        }

        if (key_exists('week', $filters) && !empty($filters['week'])) {
            $baseQuery->where_in('WEEK(safe_conducts.created_at, 1)', $filters['week']);
        }

        if (key_exists('year', $filters) && !empty($filters['year'])) {
            $baseQuery->where('YEAR(safe_conducts.created_at)', $filters['year']);
        } else {
            $baseQuery->where('YEAR(safe_conducts.created_at)', date('Y'));
        }

        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            $branchData = get_if_exist($filters, 'branch_data', 'VEHICLE');
            if ($branchData == 'TRANSACTION') {
                $baseQuery->where_in('bookings.id_branch', $filters['branch']);
            } else {
                $baseQuery
                    ->join('ref_branches AS vehicle_branches', 'vehicle_branches.id = ref_vehicles.id_branch', 'left')
                    ->where_in('vehicle_branches.id', $filters['branch']);
            }
        }

        if (key_exists('activity_type', $filters) && !empty($filters['activity_type'])) {
            $baseQuery->where_in('ref_booking_types.category', $filters['activity_type']);
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get safe conduct detail by transporter.
     *
     * @param array $filters
     * @return array
     */
    public function getReportTransporterExternalDetail($filters = [])
    {
        $baseQuery = $this->db
            ->select([
                'safe_conducts.*',
                'transporter_entry_permits.id AS id_tep',
                'transporter_entry_permits.receiver_vehicle AS vehicle_name',
                'transporter_entry_permits.receiver_no_police AS no_plate',
                'work_orders.id AS id_work_order',
                'work_orders.no_work_order',
                'bookings.no_reference',
                'ref_people.name AS customer_name',
                'ref_branches.branch',
                'ref_branches.id AS id_branch',
            ])
            ->from('safe_conducts')
            ->join("transporter_entry_permits", 'transporter_entry_permits.id = safe_conducts.id_transporter_entry_permit', 'left')
            ->join('bookings', 'bookings.id = safe_conducts.id_booking', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->join('ref_people', 'ref_people.id = bookings.id_customer', 'left')
            ->join('work_orders', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('handlings', 'work_orders.id_handling = handlings.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->group_start()
            ->where_in('ref_handling_types.multiplier_goods', ['1', '-1'])
            ->or_where_in('ref_handling_types.multiplier_container', ['1', '-1'])
            ->group_end()
            ->where('expedition_type', "EXTERNAL")
            ->where('safe_conducts.is_deleted', false)
            ->order_by('safe_conducts.id_safe_conduct_group', 'desc')
            ->order_by('safe_conducts.created_at', 'desc');

        if (key_exists('vehicle', $filters) && !empty($filters['vehicle'])) {
            $baseQuery->where_in('transporter_entry_permits.receiver_no_police', $filters['vehicle']);
        }

        if (key_exists('week', $filters) && !empty($filters['week'])) {
            $baseQuery->where_in('WEEK(safe_conducts.created_at, 1)', $filters['week']);
        }

        if (key_exists('year', $filters) && !empty($filters['year'])) {
            $baseQuery->where('YEAR(safe_conducts.created_at)', $filters['year']);
        } else {
            $baseQuery->where('YEAR(safe_conducts.created_at)', date('Y'));
        }

        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            $baseQuery->where_in('bookings.id_branch', $filters['branch']);
        }

        if (key_exists('activity_type', $filters) && !empty($filters['activity_type'])) {
            $baseQuery->where_in('ref_booking_types.category', $filters['activity_type']);
        }

        return $baseQuery->get()->result_array();
    }


    /**
     * Export transporter to excel.
     *
     * @param $reports
     * @param $periods
     * @param $vehicles
     * @return string
     */
    public function exportTransporter($reports, $periods, $vehicles)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator($this->config->item('app_name'))
            ->setLastModifiedBy($this->config->item('app_name'))
            ->setTitle('Transporter')
            ->setSubject('Data export: Transporter')
            ->setDescription('Data export generated by: ' . $this->config->item('app_name'));

        try {
            $spreadsheet->setActiveSheetIndex(0);
            $activeSheet = $spreadsheet->getActiveSheet()->setTitle('Transporter');

            $activeSheet->setCellValue('A1', 'WEEK');
            $activeSheet->MergeCells('A1:A2');
            $columnIndex = 2;
            foreach ($vehicles as $vehicle) {
                $activeSheet->setCellValueByColumnAndRow($columnIndex, 1, strtoupper($vehicle['vehicle_name'] . ' - ' . $vehicle['no_plate']));
                $activeSheet->mergeCellsByColumnAndRow($columnIndex, 1, $columnIndex+2, 1);
                $activeSheet->setCellValueByColumnAndRow($columnIndex++, 2, 'Trip');
                $activeSheet->setCellValueByColumnAndRow($columnIndex++, 2, 'Total %');
                $activeSheet->setCellValueByColumnAndRow($columnIndex++, 2, 'Prod %');
            }
            $activeSheet->setCellValueByColumnAndRow($columnIndex, 1, 'TOTAL');

            foreach ($periods as $rowPeriod => $period) {
                $activeSheet->setCellValueByColumnAndRow(1, $rowPeriod + 3, $period);
                if (key_exists($period, $reports)) {
                    foreach ($vehicles as $indexVehicle => $vehicle) {
                        foreach ($reports[$period]['vehicles'] as $report) {
                            if ($report['no_plate'] == $vehicle['no_plate'] && $report['week'] == $period) {
                                $activeSheet->setCellValueByColumnAndRow(2 + (($indexVehicle + 1) - 1)*3, $rowPeriod + 3, $report['total_safe_conduct']);
                                $activeSheet->setCellValueByColumnAndRow(3 + (($indexVehicle + 1) - 1)*3, $rowPeriod + 3, round($report['percentage']) . '%');
                                $activeSheet->setCellValueByColumnAndRow(4 + (($indexVehicle + 1) - 1)*3, $rowPeriod + 3, round($report['prod']) . '%');
                                break;
                            }
                        }
                    }
                    $activeSheet->setCellValueByColumnAndRow(2 + ((count($vehicles) + 1) - 1)*3, $rowPeriod + 3, $reports[$period]['total']);
                }
            }
            $columnIterator = $spreadsheet->getActiveSheet()->getColumnIterator();
            foreach ($columnIterator as $column) {
                $spreadsheet->getActiveSheet()
                    ->getColumnDimension($column->getColumnIndex())
                    ->setAutoSize(true);

                $spreadsheet->getActiveSheet()
                    ->getStyle($column->getColumnIndex() . '1')
                    ->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'color' => ['rgb' => '000000']
                            ],
                            'font' => [
                                'bold' => true,
                                'color' => ['rgb' => 'FFFFFF']
                            ]
                        ]
                    )
                    ->getAlignment()
                    ->setHorizontal('center');
                    $spreadsheet->getActiveSheet()
                    ->getStyle($column->getColumnIndex() . '2')
                    ->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'color' => ['rgb' => '000000']
                            ],
                            'font' => [
                                'bold' => true,
                                'color' => ['rgb' => 'FFFFFF']
                            ]
                        ]
                    )
                    ->getAlignment()
                    ->setHorizontal('center');
            }

            // center all cells
            $spreadsheet->getActiveSheet()
                ->getStyleByColumnAndRow(1, 1, 2 + ((count($vehicles) + 1) - 1)*3 , count($periods) + 2)
                ->getAlignment()
                ->setHorizontal('center');

            if (!empty($data)) {
                $activeSheet->setAutoFilterByColumnAndRow(1, 1, 2 + ((count($vehicles) + 1) - 1)*3 , 1);
            }

            $excelWriter = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Transporter.xlsx"');
            $excelWriter->save('php://output');

        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            return $e->getMessage();
        }
    }

}
