<?php
/**
 * Created by PhpStorm.
 * User: angga
 * Date: 30/12/17
 * Time: 1:39
 */

class MaintenanceMode
{
    /**
     * Check if website is currently upgrading or maintenance.
     */
    public function checkMaintenanceMode()
    {
        if (env('MAINTENANCE_MODE')) {
            include(APPPATH . '/views/errors/html/error_503.php');
            die();
        }
    }
}