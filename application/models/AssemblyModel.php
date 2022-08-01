<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AssemblyModel extends MY_Model
{
    protected $table = 'ref_assemblies';

    /**
     * GoodsModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get master goods base query.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return $this->db->select([
            'ref_assemblies.*'
        ])
            ->from($this->table);
    }

     /**
     * Get auto number for assembly.
     * @param string $type
     * @return string
     */
    public function getAutoNumberAssembly($type = 'AS')
    {
        $orderData = $this->db->query("
            SELECT CAST(RIGHT(no_assembly, 6) AS UNSIGNED) + 1 AS order_number 
            FROM  ref_assemblies 
            WHERE MONTH(created_at) = MONTH(NOW()) 
            AND YEAR(created_at) = YEAR(NOW())
            ORDER BY SUBSTR(no_assembly FROM 4) DESC LIMIT 1
            ");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return $type . '/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }

   
}