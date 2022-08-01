<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ComplainKpiModel extends MY_Model
{
    protected $table = 'ref_complain_kpi';
    protected $tableDepartmentContact = 'ref_departments';
  
    const KPI_SUBMIT_APPROVAL = 'submit-approval';
    const KPI_APPROVAL_CONCLUSION = 'approval-conclusion';
    const KPI_CONCLUSION_CLOSE = 'conclusion-close';
    const KPI_RESPONSE_WAITING_TIME = 'response-waiting-time';

    /**
     * UserModel constructor.
     */
    public function __construct()
    {
        if ($this->config->item('sso_enable')) {
            $hrDB = env('DB_HR_DATABASE');
            $this->tableDepartmentContact = $hrDB . '.ref_department_contact_groups';
        }
    }
    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        $baseQuery = $this->db->select([$this->table . '.*'])->from($this->table);

        if ($this->db->field_exists('id_branch', $this->table)) {
            if (!empty($branchId)) {
                $baseQuery->where($this->table . '.id_branch', $branchId);
            }
        }

        $baseQuery->select("GROUP_CONCAT(ref_department_contact_groups.contact_group SEPARATOR ', ') AS whatsapp_groups")
            ->select('ref_complain_kpi_whatsapp.whatsapp_group')
            ->join('ref_complain_kpi_whatsapp','ref_complain_kpi.id = ref_complain_kpi_whatsapp.id_kpi','left')
            ->join($this->tableDepartmentContact. ' AS ref_department_contact_groups','ref_department_contact_groups.id = ref_complain_kpi_whatsapp.id_contact_group','left')
            ->group_by('ref_complain_kpi.id');

        return $baseQuery;
    }

    /**
     * Get data by custom condition.
     *
     * @param $conditions
     * @param bool $resultRow
     * @param bool $withTrashed
     * @return array|int
     */
    public function getByReminder($conditions, $resultRow = false, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery(get_if_exist($conditions, '_id_branch', null));
        $baseQuery->join('ref_complain_kpi_reminders', 'ref_complain_kpi.id = ref_complain_kpi_reminders.id_kpi', 'left');
        if (key_exists('time', $conditions) && !empty($conditions['time'])) {
            $baseQuery->where('ref_complain_kpi.reminder', $conditions['time'])
                        ->or_where('ref_complain_kpi_reminders.reminder_time', $conditions['time']);
        }

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if($resultRow === 'COUNT') {
            return $baseQuery->count_all_results();
        } else if ($resultRow) {
            return $baseQuery->get()->row_array();
        }

        return $baseQuery->get()->result_array();
    }
}