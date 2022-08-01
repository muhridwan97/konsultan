<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ComplainKpiReminderModel extends MY_Model
{
    protected $table = 'ref_complain_kpi_reminders';
  
    /**
     * Automate constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('DepartmentContactGroupModel', 'departmentContactGroupModel');
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

        return $baseQuery;
    }
    /**
     * Delete reminder group by id kpi.
     * @param $complainKpiId
     * @param bool $softDelete
     * @return mixed
     */
    public function deleteReminderByComplainKpi($complainKpiId)
    {
        $deleteCondition = [
            'id_kpi' => $complainKpiId
        ];

        return $this->db->delete($this->table, $deleteCondition);
    }

    /**
     * Insert single or batch data whatsapp.
     * @param $data
     * @return bool
     */
    public function insertReminderGroup($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }
}