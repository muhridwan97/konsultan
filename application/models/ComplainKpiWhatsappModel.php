<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ComplainKpiWhatsappModel extends MY_Model
{
    protected $table = 'ref_complain_kpi_whatsapp';
  
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

        $baseQuery
            ->select('ref_department_contact_groups.contact_group')
            ->join(DepartmentContactGroupModel::$tableDepartmentContactGroup. ' AS ref_department_contact_groups','ref_department_contact_groups.id = ref_complain_kpi_whatsapp.id_contact_group','left');

        return $baseQuery;
    }
    /**
     * Delete whatsapp group by id kpi.
     * @param $complainKpiId
     * @param bool $softDelete
     * @return mixed
     */
    public function deleteWhatsappByComplainKpi($complainKpiId)
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
    public function insertWhatsappGroup($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }
}