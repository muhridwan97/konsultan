<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OperationCutOffScheduleModel extends MY_Model
{
    protected $table = 'ref_operation_cut_off_schedules';

    public function __construct()
    {
        parent::__construct();

    }
    /**
     * Get base query.
     *
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = NULL)
    {
        $baseQuery = parent::getBaseQuery($branchId = NULL);

        return $baseQuery;
    }

    /**
     * Delete work order component data by id_operation_cut_off.
     * @param integer $id
     * @return bool
     */
    public function deleteByOperationCutOff($id_operation_cut_off)
    {
        return $this->db->delete($this->table, ['id_operation_cut_off' => $id_operation_cut_off]);
    }
}
