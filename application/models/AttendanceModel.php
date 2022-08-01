<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AttendanceModel extends MY_Model
{

    protected $table = 'attendances';
    protected $tableNonEmployee = 'ref_non_employees';
    protected $tableSchedule = 'schedules';
    protected $tableOvertimes = 'overtimes';
    protected $tableOvertimeStatuses = 'overtime_statuses';
    protected $tableScheduleDivision = 'schedule_division';
    protected $tableScheduleDivisionNonEmployee = 'schedule_division_non_employees';
    protected $tablePosition = 'ref_positions';

    public function __construct()
    {
        parent::__construct();

        if ($this->config->item('sso_enable')) {
            $hrDB = env('DB_HR_DATABASE');
            $this->table = $hrDB . '.attendances';
            $this->tableNonEmployee = $hrDB . '.ref_non_employees';
            $this->tableSchedule = $hrDB . '.schedules';
            $this->tableOvertimes = $hrDB . '.overtimes';
            $this->tableOvertimeStatuses = $hrDB . '.overtime_statuses';
            $this->tableScheduleDivision = $hrDB . '.schedule_division';
            $this->tableScheduleDivisionNonEmployee = $hrDB . '.schedule_division_non_employees';
            $this->tablePosition = $hrDB . '.ref_positions';
        }
    }

    /**
     * Get base query.
     *
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = NULL)
    {
        $baseQuery = parent::getBaseQuery()
                    ->select([
                        'ref_employees.name AS employee_name',
                        'ref_non_employees.name AS non_employee_name'
                    ])
                    ->join(EmployeeModel::$tableEmployee." AS ref_employees", 'ref_employees.id = attendances.id_employee AND attendances.attendance_owner = "EMPLOYEE"', 'left', false)
                    ->join($this->tableNonEmployee.' AS ref_non_employees', 'ref_non_employees.id = attendances.id_employee AND attendances.attendance_owner = "NON EMPLOYEE"', 'left', false);
        return $baseQuery;
    }

    /**
     * Get attendance employee by department.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    public function getAttendanceByDepartment($filters = [])
    {
        $this->db->start_cache();
        
        $query = $this->getBaseQuery()
                ->select([
                    'schedules.start',
                    'schedules.end',
                    'ref_positions.position',
                ])
                ->join($this->tableScheduleDivision,"schedule_division.date = attendances.date AND schedule_division.id_employee = attendances.id_employee AND attendances.attendance_owner = 'EMPLOYEE'","left")
                ->join($this->tableSchedule,"schedules.id = schedule_division.id_schedule","left")
                ->join($this->tablePosition,"ref_positions.id = ref_employees.id_position","left")
                ->group_by('attendances.id_employee')
                ->order_by('attendances.on_duty','ASC')
                ->where("attendances.attendance_owner", 'EMPLOYEE');

        if (!empty($filters['branch']) && !is_null($filters['branch'])) {
            $query->where('ref_employees.work_location',$filters['branch']);
        }
        if (!empty($filters['department']) && !is_null($filters['department'])) {
            $query->where('ref_employees.id_department',$filters['department']);
        }
        if (!empty($filters['checked_in_from']) && !is_null($filters['checked_in_from'])) {
            $query->where('attendances.check_in >=',$filters['checked_in_from']);
        }
        if (!empty($filters['checked_in_to']) && !is_null($filters['checked_in_to'])) {
            $query->where('attendances.check_in <=',$filters['checked_in_to']);
        }
        if (!empty($filters['schedule_from']) && !is_null($filters['schedule_from']) && !empty($filters['schedule_to']) && !is_null($filters['schedule_to'])) {
            $query->group_start();
            $query->group_start();
            $query->where("STR_TO_DATE(CONCAT(schedule_division.date, ' ', schedules.start), '%Y-%m-%d %H:%i:%s') BETWEEN '".$filters['schedule_from']. "' AND '".$filters['schedule_to']."'");
            $query->or_where("STR_TO_DATE(CONCAT(schedule_division.date, ' ', schedules.end), '%Y-%m-%d %H:%i:%s') BETWEEN '".$filters['schedule_from']. "' AND '".$filters['schedule_to']."'");
            $query->group_end();
            $query->or_group_start();
            $query->where("'".$filters['schedule_from']. "' BETWEEN STR_TO_DATE(CONCAT(schedule_division.date, ' ', schedules.start), '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE(CONCAT(schedule_division.date, ' ', schedules.end), '%Y-%m-%d %H:%i:%s')");
            $query->or_where("'".$filters['schedule_to']."' BETWEEN STR_TO_DATE(CONCAT(schedule_division.date, ' ', schedules.start), '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE(CONCAT(schedule_division.date, ' ', schedules.end), '%Y-%m-%d %H:%i:%s')");
            $query->group_end();
            $query->group_end();
        }
        if (!empty($filters['cloud_id']) && !is_null($filters['cloud_id'])) {
            $query->group_start();
            $query->where('attendances.check_in_cloud_id',$filters['cloud_id']);
            $query->or_where('attendances.check_out_cloud_id',$filters['cloud_id']);
            $query->group_end();
        }

        if (!empty($filters['is_check_in']) && !is_null($filters['is_check_in'])) {
            $query->where('attendances.check_in IS NOT NULL');
        }
        if (!empty($filters['id_schedule']) && !is_null($filters['id_schedule']) && !empty($filters['schedule_date']) && !is_null($filters['schedule_date']) ) {
            $query->where_in('schedules.id' , $filters['id_schedule']);
            $query->where('schedule_division.date', format_date($filters['schedule_date']));
        }
        $this->db->stop_cache();


        $queryData = $query->get()->result_array();

        $this->db->flush_cache();

        return $queryData;
    }

    /**
     * Get attendance employee by department.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    public function getOvertimeByDepartment($filters = [])
    {
        $this->db->start_cache();
        
        $query = $this->db
                ->distinct()
                ->select([
                    'overtimes.*',
                    'ref_employees.name AS employee_name',
                    'ref_positions.position',
                    'schedules.start AS work_start',
                    'schedules.end AS work_end',
                ])
                ->from($this->tableOvertimes)
                ->join(EmployeeModel::$tableEmployee." AS ref_employees", 'ref_employees.id = overtimes.id_employee', 'left', false)
                ->join($this->tableOvertimeStatuses,'overtime_statuses.id_overtime = overtimes.id','left')
                ->join($this->tablePosition,"ref_positions.id = ref_employees.id_position","left")
                ->join($this->tableScheduleDivision, 'schedule_division.id_employee = ref_employees.id
				AND IF(TIME(overtimes.overtime_start)<= "03:00:00" , DATE_ADD(DATE(overtimes.overtime_start), INTERVAL -1 DAY) , DATE(overtimes.overtime_start) ) = schedule_division.date', 'left')
                ->join($this->tableSchedule, 'schedules.id = schedule_division.id_schedule', 'left')
                ->join($this->table, 'attendances.off_duty = overtimes.overtime_start AND attendances.id_employee = ref_employees.id', 'left')
                ->order_by('overtimes.overtime_start','ASC')
                ->where('overtimes.overtime_type','PLAN')
                ->where('overtime_statuses.status','APPROVED');

        if (!empty($filters['branch']) && !is_null($filters['branch'])) {
            $query->where('ref_employees.work_location',$filters['branch']);
        }
        if (!empty($filters['department']) && !is_null($filters['department'])) {
            $query->where('ref_employees.id_department',$filters['department']);
        }
        if (!empty($filters['overtime_from']) && !is_null($filters['overtime_from']) && !empty($filters['overtime_to']) && !is_null($filters['overtime_to'])) {
            $query->group_start();
            $query->group_start();
            $query->where("overtimes.overtime_start BETWEEN '".$filters['overtime_from']. "' AND '".$filters['overtime_to']."'");
            $query->or_where("IFNULL(attendances.check_out ,overtimes.overtime_end) BETWEEN '".$filters['overtime_from']. "' AND '".$filters['overtime_to']."'");
            $query->group_end();
            $query->or_group_start();
            $query->where("'".$filters['overtime_from']. "' BETWEEN overtimes.overtime_start AND IFNULL(attendances.check_out ,overtimes.overtime_end)");
            $query->or_where("'".$filters['overtime_to']."' BETWEEN overtimes.overtime_start AND IFNULL(attendances.check_out ,overtimes.overtime_end)");
            $query->group_end();
            $query->group_end();
        }
        
        if (!empty($filters['cloud_id']) && !is_null($filters['cloud_id'])) {
            $query->group_start();
            $query->where('attendances.check_in_cloud_id',$filters['cloud_id']);
            $query->or_where('attendances.check_out_cloud_id',$filters['cloud_id']);
            $query->group_end();
        }
        $this->db->stop_cache();


        $queryData = $query->get()->result_array();

        $this->db->flush_cache();

        return $queryData;
    }

    /**
     * Get attendance employee by department.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    public function getAttendanceByLabour($filters = [])
    {
        $this->db->start_cache();
        
        $query = $this->getBaseQuery()
                ->select([
                    'schedules.start',
                    'schedules.end',
                ])
                ->join($this->tableScheduleDivisionNonEmployee,"schedule_division_non_employees.date = attendances.date AND schedule_division_non_employees.id_non_employee = attendances.id_employee AND attendances.attendance_owner = 'NON EMPLOYEE'","left")
                ->join($this->tableSchedule,"schedules.id = schedule_division_non_employees.id_schedule","left")
                ->where("attendances.attendance_owner", 'NON EMPLOYEE')
                ->where("ref_non_employees.type", 'LABOR');

        if (!empty($filters['branch']) && !is_null($filters['branch'])) {
            $query->where('ref_non_employees.id_work_location',$filters['branch']);
        }
        if (!empty($filters['checked_in_from']) && !is_null($filters['checked_in_from'])) {
            $query->where('attendances.check_in >=',$filters['checked_in_from']);
        }
        if (!empty($filters['checked_in_to']) && !is_null($filters['checked_in_to'])) {
            $query->where('attendances.check_in <=',$filters['checked_in_to']);
        }
        if (!empty($filters['schedule_from']) && !is_null($filters['schedule_from']) && !empty($filters['schedule_to']) && !is_null($filters['schedule_to'])) {
            $query->group_start();
            $query->group_start();
            $query->where("STR_TO_DATE(CONCAT(schedule_division_non_employees.date, ' ', schedules.start), '%Y-%m-%d %H:%i:%s') BETWEEN '".$filters['schedule_from']. "' AND '".$filters['schedule_to']."'");
            $query->or_where("STR_TO_DATE(CONCAT(schedule_division_non_employees.date, ' ', schedules.end), '%Y-%m-%d %H:%i:%s') BETWEEN '".$filters['schedule_from']. "' AND '".$filters['schedule_to']."'");
            $query->group_end();
            $query->or_group_start();
            $query->where("'".$filters['schedule_from']. "' BETWEEN STR_TO_DATE(CONCAT(schedule_division_non_employees.date, ' ', schedules.start), '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE(CONCAT(schedule_division_non_employees.date, ' ', schedules.end), '%Y-%m-%d %H:%i:%s')");
            $query->or_where("'".$filters['schedule_to']."' BETWEEN STR_TO_DATE(CONCAT(schedule_division_non_employees.date, ' ', schedules.start), '%Y-%m-%d %H:%i:%s') AND STR_TO_DATE(CONCAT(schedule_division_non_employees.date, ' ', schedules.end), '%Y-%m-%d %H:%i:%s')");
            $query->group_end();
            $query->group_end();
        }
        if (!empty($filters['is_check_in']) && !is_null($filters['is_check_in'])) {
            $query->group_start();
            $query->where('attendances.check_in IS NOT NULL');
            $query->or_where('attendances.check_out IS NOT NULL');
            $query->group_end();
        }
        if (!empty($filters['id_schedule']) && !is_null($filters['id_schedule']) && !empty($filters['schedule_date']) && !is_null($filters['schedule_date']) ) {
            $query->where_in('schedules.id' , $filters['id_schedule']);
            $query->where('schedule_division_non_employees.date', format_date($filters['schedule_date']));
        }
        if (!empty($filters['cloud_id']) && !is_null($filters['cloud_id'])) {
            $query->group_start();
            $query->where('attendances.check_in_cloud_id',$filters['cloud_id']);
            $query->or_where('attendances.check_out_cloud_id',$filters['cloud_id']);
            $query->group_end();
        }
        $this->db->stop_cache();


        $queryData = $query->get()->result_array();

        $this->db->flush_cache();

        return $queryData;
    }

}
