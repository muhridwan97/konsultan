<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ScheduleHolidayModel extends MY_Model
{
    protected $table = 'schedule_holidays';
    protected $table_type = 'schedule_holiday_types';

    public function __construct()
    {
        parent::__construct();

        if ($this->config->item('sso_enable')) {
            $hrDB = env('DB_HR_DATABASE');
            $this->table = $hrDB . '.schedule_holidays';
            $this->table_type = $hrDB . '.schedule_holiday_types';
        }
    }
    /**
     * Get base query.
     *
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = NULL)
    {
        $baseQuery = parent::getBaseQuery($branchId = NULL)
            ->select([
                'schedule_holiday_types.type',
                'schedule_holiday_types.no_schedule_holiday_type',
            ])
            ->join($this->table_type. " AS schedule_holiday_types", 'schedule_holiday_types.id = schedule_holidays.id_schedule_holiday_type', 'left');

        return $baseQuery;
    }

    /**
     * Cek apakah hari libur
     * @param date
     * @return boolean
     */
    public function is_holiday($date=null){
        if (is_null($date)) {
            $date = date("Y-m-d");
        }
        $holiday = $this->db->get_where($this->table, [
            'date' => $date,
        ]);

        if ($holiday->num_rows() > 0) {
            return true;
        }
        return false;
    }

}
