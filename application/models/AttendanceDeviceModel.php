<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AttendanceDeviceModel extends MY_Model
{
    protected $table = 'ref_attendance_devices';

    public function __construct()
    {
        parent::__construct();

        if ($this->config->item('sso_enable')) {
            $hrDB = env('DB_HR_DATABASE');
            $this->table = $hrDB . '.ref_attendance_devices';
        }
    }
}
