<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DepartmentModel extends MY_Model
{
    protected $table = 'ref_departments';

    public static $tableDepartment = 'ref_departments';

    /**
     * UserModel constructor.
     */
    public function __construct()
    {
        if ($this->config->item('sso_enable')) {
            $hrDB = env('DB_HR_DATABASE');
            $this->table = $hrDB . '.ref_departments';
            self::$tableDepartment = $this->table;
        }
    }

    /**
     * Get base query of table.
     *
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = NULL)
    {
        $baseQuery = $this->db
            ->select([
                'ref_departments.*',
            ])
            ->from($this->table)
            ->order_by('id', 'desc');

        return $baseQuery;
    }
}
