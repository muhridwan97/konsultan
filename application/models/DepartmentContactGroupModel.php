<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DepartmentContactGroupModel extends MY_Model
{
    protected $table = 'ref_department_contact_groups';
    protected $tableDepartment = 'ref_departments';

    public static $tableDepartmentContactGroup = 'ref_department_contact_groups';

    public function __construct()
    {
        if ($this->config->item('sso_enable')) {
            $this->table = env('DB_HR_DATABASE') . '.ref_department_contact_groups';
            $this->tableDepartment = env('DB_HR_DATABASE') . '.ref_departments';
            self::$tableDepartmentContactGroup = env('DB_HR_DATABASE') . '.ref_department_contact_groups';
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

        $baseQuery
            ->select('ref_departments.department')
            ->join($this->tableDepartment.' AS ref_departments','ref_departments.id = ref_department_contact_groups.id_department','left');

        return $baseQuery;
    }
}
