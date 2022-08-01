<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OperationCutOffJoinModel extends MY_Model
{
    protected $table = 'ref_operation_cut_off_joins';

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

    /**
     * Delete work order component data by no_group.
     * @param integer $id
     * @return bool
     */
    public function deleteByNoGroup($no_group)
    {
        return $this->db->delete($this->table, ['no_group' => $no_group]);
    }

    /**
     * Get auto number for opname.
     *
     * @return string
     */
    public function getAutoNumber()
    {
        $orderData = $this->db->query("
            SELECT (no_group+1) AS order_number
            FROM ref_operation_cut_off_joins 
            ORDER BY no_group DESC LIMIT 1
        ");
        $orderPad = '1';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = $lastOrder['order_number'];
        }
        return $orderPad;
    }
}
