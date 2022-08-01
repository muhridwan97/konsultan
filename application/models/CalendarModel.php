<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CalendarModel extends MY_Model
{
    protected $table = 'calendars';

    public static $tableCalendar = 'calendars';

    const TYPE_DAY_OFF = 'DAY OFF';

    public function __construct()
    {
        parent::__construct();

        if ($this->config->item('sso_enable')) {
            $this->table = env('DB_HR_DATABASE') . '.calendars';
            self::$tableCalendar = $this->table;
        }
    }

    /**
     * Get data by custom condition.
     *
     * @param $conditions
     * @param bool $resultRow
     * @param bool $withTrashed
     * @return array
     */
    public function getBy($conditions, $resultRow = false, $withTrashed = false)
    {
        if($this->db->table_exists($this->table)) {
            $baseQuery = $this->getBaseQuery();

            $baseQuery->where($conditions);

            if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
                $baseQuery->where($this->table . '.is_deleted', false);
            }

            if ($resultRow) {
                return $baseQuery->get()->row_array();
            }

            return $baseQuery->get()->result_array();
        }

        return [];
    }
}