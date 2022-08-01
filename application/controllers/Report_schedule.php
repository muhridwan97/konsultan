<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Report_schedule
 * @property ReportScheduleModel $reportSchedule
 * @property Exporter $exporter
 */
class Report_schedule extends MY_Controller
{
    /**
     * Report_schedule constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ReportScheduleModel', 'reportSchedule');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show report data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_SCHEDULE_VIEW);

        $reportSchedules = $this->reportSchedule->getAll($_GET);
        if ($this->input->get('export')) {
            $this->exporter->exportFromArray('Report Schedule', $reportSchedules);
        } else {
            $this->render('report_schedule/index', compact('reportSchedules'));
        }
    }

    /**
     * Show edit schedule form.
     *
     * @param $reportName
     */
    public function view($reportName)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_SCHEDULE_VIEW);

        $reportSchedule = $this->reportSchedule->getById($reportName);

        $this->render('report_schedule/view', compact('reportSchedule'));
    }

    /**
     * Show edit schedule form.
     *
     * @param $reportName
     */
    public function edit($reportName)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_SCHEDULE_EDIT);

        $reportSchedule = $this->reportSchedule->getById($reportName);

        $this->render('report_schedule/edit', compact('reportSchedule'));
    }

    /**
     * Update report schedule.
     *
     * @param $reportName
     */
    public function update($reportName)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_SCHEDULE_EDIT);

        if ($this->validate()) {
            $sendToType = $this->input->post('send_to_type');
            $sendTo = $this->input->post('send_to');
            $sendCcType = $this->input->post('send_cc_type');
            $sendCc = $this->input->post('send_cc');
            $sendBccType = $this->input->post('send_bcc_type');
            $sendBcc = $this->input->post('send_bcc');
            $description = $this->input->post('description');
            $recurringPeriod = $this->input->post('recurring_period');
            $triggeredAt = $this->input->post('triggered_at');
            $triggeredMonth = $this->input->post('triggered_month');
            $triggeredDate = $this->input->post('triggered_date');
            $triggeredDay = $this->input->post('triggered_day');
            $triggeredTime = $this->input->post('triggered_time');
            $status = $this->input->post('status');

            $this->reportSchedule->update([
                'send_to_type' => $sendToType,
                'send_to' => $sendTo,
                'send_cc_type' => $sendCcType,
                'send_cc' => $sendCc,
                'send_bcc_type' => $sendBccType,
                'send_bcc' => $sendBcc,
                'recurring_period' => $recurringPeriod,
                'triggered_at' => $recurringPeriod == ReportScheduleModel::PERIOD_ONE_TIME ? (format_date($triggeredAt, 'Y-m-d') . ' ' . $triggeredTime) : null,
                'triggered_month' => if_empty($triggeredMonth, null),
                'triggered_date' => if_empty($triggeredDate, null),
                'triggered_day' => if_empty($triggeredDay, null),
                'triggered_time' => if_empty($triggeredTime, null),
                'status' => if_empty($status, ReportScheduleModel::STATUS_INACTIVE),
                'description' => if_empty($description, null),
            ], $reportName);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Schedule " . str_replace(['-', '_'], ' ', $reportName) . " successfully updated", 'report-schedule');
            } else {
                flash('danger', "Save report schedule failed");
            }
        }
        $this->edit($reportName);
    }

    /**
     * Get base validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'status' => 'required|in_list[ACTIVE,INACTIVE]',
            'description' => 'trim|max_length[500]',
        ];
    }
}