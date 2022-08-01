<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmployeeModel extends MY_Model
{

    protected $table = 'ref_employees';
    protected $tableDepartment = 'ref_departments';
    public static $tableEmployee = 'ref_employees';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';

    public function __construct()
    {
        parent::__construct();

        if ($this->config->item('sso_enable')) {
            $hrDB = env('DB_HR_DATABASE');
            $this->table = $hrDB . '.ref_employees';
            self::$tableEmployee = $this->table;
            $this->tableDepartment = $hrDB . '.ref_departments';
        }
    }

    /**
     * Get base query.
     *
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = NULL)
    {
        $this->load->model('DepartmentModel');
        $baseQuery = parent::getBaseQuery()
            ->select([
                'ref_departments.department',
                'supervisors.name AS supervisor_name',
                UserModel::$tableUser . '.username',
                UserModel::$tableUser . '.email',
            ]);

            if ($this->config->item('sso_enable')) {
                $baseQuery
                    ->join($this->tableDepartment, 'ref_departments.id = ref_employees.id_department', 'left')                    
                    ->join(EmployeeModel::$tableEmployee . ' AS supervisors', 'supervisors.id = ref_employees.id_employee', 'left')
                    ->join(UserModel::$tableUser, UserModel::$tableUser . '.id = ref_employees.id_user', 'left');
            }

        return $baseQuery;
    }

    /**
     * Generate employee number.
     *
     * @return string
     */
    public function getAutoNumberEmployee()
    {
        $orderData = $this->db->query("
            SELECT IFNULL(CAST(RIGHT(no_employee, 5) AS UNSIGNED), 0) + 1 AS order_number 
            FROM " . EmployeeModel::$tableEmployee . "
            WHERE no_employee LIKE 'EMP%'
            ORDER BY no_employee DESC LIMIT 1
        ");
        $orderPad = '00001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 5, '0', STR_PAD_LEFT);
        }
        return 'EMP' . $orderPad;
    }

    /**
     * Check if given employee is unique.
     *
     * @param $no
     * @param int $exceptId
     * @return bool
     */
    public function isUniqueEmployeeNo($no, $exceptId = 0)
    {
        $user = $this->db->get_where($this->table, [
            'no_employee' => $no,
            'id != ' => $exceptId
        ]);

        if ($user->num_rows() > 0) {
            return false;
        }
        return true;
    }

    /**
     * Check if the employee have subordinates.
     * @param $supervisorId
     * @return bool
     */
    public static function hasSubordinate($supervisorId)
    {
        if(empty($supervisorId)) {
            return false;
        }

        $CI = get_instance();
        $result = $CI->db->get_where(self::$tableEmployee, ['id_employee' => $supervisorId]);
        $subordinates = $result->row_array();
        return !empty($subordinates);
    }
}
