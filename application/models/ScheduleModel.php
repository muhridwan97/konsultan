<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ScheduleModel extends MY_Model
{
    protected $table = 'schedules';

    public function __construct()
    {
        parent::__construct();

        if ($this->config->item('sso_enable')) {
            $hrDB = env('DB_HR_DATABASE');
            $this->table = $hrDB . '.schedules';
        }
    }
    /**
     * Get base query.
     *
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = NULL)
    {
        $baseQuery = parent::getBaseQuery($branchId = NULL);

        return $baseQuery;
    }
}
