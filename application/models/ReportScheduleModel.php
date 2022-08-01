<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ReportScheduleModel extends MY_Model
{
    protected $table = 'report_schedules';
    protected $id = 'report_name';

    const PERIOD_ONE_TIME = 'ONE TIME';
    const PERIOD_DAILY = 'DAILY';
    const PERIOD_WEEKLY = 'WEEKLY';
    const PERIOD_MONTHLY = 'MONTHLY';
    const PERIOD_ANNUAL = 'ANNUAL';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';

    const SEND_TYPE_DEFAULT = 'DEFAULT';
    const SEND_TYPE_PROFILE = 'PROFILE';
    const SEND_TYPE_PROFILE_CONTACT = 'PROFILE CONTACT';
    const SEND_TYPE_USER = 'USER';
    const SEND_TYPE_BRANCH_PLB_OR_TPP_SUPPORT = 'BRANCH PLB OR TPP SUPPORT';
    const SEND_TYPE_BRANCH_PLB_SUPPORT = 'BRANCH PLB SUPPORT';
    const SEND_TYPE_BRANCH_TPP_SUPPORT = 'BRANCH TPP SUPPORT';

    const REPORT_TPS_DEFERRED = 'report-tps-deferred';
    const REPORT_INBOUND_OUTBOUND = 'report-inbound-outbound';
    const REPORT_TPS_ACTIVITY = 'report-tps-activity';
    const REPORT_TPS_STOCK = 'report-tps-stock';
    const REPORT_SHIPPING_LINE_ACTIVITY = 'report-shipping-line-activity';
    const REPORT_SHIPPING_LINE_STOCK = 'report-shipping-line-stock';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery($branchId)
            ->select([
                "(CASE
                    WHEN recurring_period = 'ONE TIME' THEN DATE_FORMAT(triggered_at, '%d %M %Y - %H:%i')
                    WHEN recurring_period = 'DAILY' THEN IFNULL(DATE_FORMAT(triggered_time, '%H:%i'), '00:00')
                    WHEN recurring_period = 'WEEKLY' THEN CONCAT(ELT(IFNULL(triggered_day, 0) + 1, 'Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'), ' - ', IFNULL(DATE_FORMAT(triggered_time, '%H:%i'), '00:00'))
                    WHEN recurring_period = 'MONTHLY' THEN CONCAT(triggered_date, ' date - ', IFNULL(DATE_FORMAT(triggered_time, '%H:%i'), '00:00'))
                    WHEN recurring_period = 'ANNUAL' THEN CONCAT(DATE_FORMAT(CONCAT('0000-', triggered_month, '-' ,triggered_date), '%M %d'), ' - ', IFNULL(DATE_FORMAT(triggered_time, '%H:%i'), '00:00'))
                    ELSE ''
                END) AS schedule_label
                ",
                'updaters.name AS updater_name'
            ])
            ->join(UserModel::$tableUser . ' AS updaters', 'updaters.id = report_schedules.updated_by', 'left');
    }
}