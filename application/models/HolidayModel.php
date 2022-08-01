<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HolidayModel extends MY_Model
{
    protected $table = 'schedule_holidays';
    protected $tableScheduleHolidaytypes = 'schedule_holiday_types';

    public static $tableHoliday = 'schedule_holidays';

    /**
     * UserModel constructor.
     */
    public function __construct()
    {
        if ($this->config->item('sso_enable')) {
            $hrDB = env('DB_HR_DATABASE');
            $this->table = $hrDB . '.schedule_holidays';
            $this->tableScheduleHolidaytypes = $hrDB . '.schedule_holiday_types';
            self::$tableHoliday = $this->table;
        }
    }

    /**
     * Get active record query builder for all related user data selection.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        $baseQuery = $this->db
            ->select([
                'schedule_holidays.*',
                'schedule_holiday_types.type',
            ])
            ->from($this->table)
            ->order_by('schedule_holidays.id', 'desc');

        if ($this->config->item('sso_enable')) {
            $baseQuery
                ->join($this->tableScheduleHolidaytypes, 'schedule_holidays.id_schedule_holiday_type = schedule_holiday_types.id');
        }

        return $baseQuery;
    }

     /**
     * Get all data schedule holiday .
     * @return mixed
     */
    public function getAllData($withTrashed = false)
    {
        $data = $this->getBaseQuery();

        if (!$withTrashed) {
            $data->where('schedule_holidays.is_deleted', false);
        }

        return $data->get()->result_array();
    }

      /**
     * Get data by off date .
     * @return mixed
     */
    public function getByDate($offDate, $withTrashed = false)
    {
        $data = $this->getBaseQuery()->where('date', $offDate);

        if (!$withTrashed) {
            $data->where('schedule_holidays.is_deleted', false);
        }

        return $data->get()->row_array();
    }

    public function getHariKerja ($start,$end){
        $data = $this->db->select('*')
                        ->from("(SELECT ADDDATE('1970-01-01',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) selected_date FROM
                        (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
                        (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
                        (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
                        (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
                        (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4) v")
                        ->where("selected_date BETWEEN DATE('".$start."') AND DATE('".$end."')")
                        ->where("selected_date NOT IN 
                            (SELECT ".$this->table.".`date` FROM ".$this->table.") AND DAYOFWEEK(selected_date) != 1");
        return $data->get()->result_array();
    }
}
