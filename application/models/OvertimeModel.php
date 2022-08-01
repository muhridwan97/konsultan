<?php

class OvertimeModel extends MY_Model
{
    protected $table = 'ref_overtimes';

    /**
     * PositionModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active record query builder for all related overtime data selection.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $overtimes = $this->db->select([
            'ref_overtimes.*',
            'ref_branches.branch',
        ])
            ->from('ref_overtimes')
            ->join('ref_branches', 'ref_branches.id = ref_overtimes.id_branch', 'left');

        if (!empty($branchId)) {
            $overtimes->where('ref_overtimes.id_branch', $branchId);
        }

        return $overtimes;
    }

    /**
     * Get auto number for overtime.
     *
     * @return string
     */
     public function getDayOvertime($day, $branchId, $withTrashed = false)
    {
        $overtime = $this->getBaseQuery()->where('ref_overtimes.name_of_day', $day)->where('ref_overtimes.id_branch', $branchId);
        
        if (!$withTrashed) {
            $overtime->where('ref_overtimes.is_deleted', false);
        }
        return $overtime->get()->row_array();

    }

     /**
     * Get auto number for overtime.
     *
     * @return string
     */
    public function getAutoNumberOvertime()
    {
        $orderData = $this->db->query("
            SELECT IFNULL(CAST(RIGHT(no_overtime, 6) AS UNSIGNED), 0) + 1 AS order_number 
            FROM ref_overtimes 
            WHERE SUBSTR(no_overtime, 7, 2) = MONTH(NOW()) 
            AND SUBSTR(no_overtime, 4, 2) = DATE_FORMAT(NOW(), '%y')
            ORDER BY SUBSTR(no_overtime FROM 4) DESC LIMIT 1
        ");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return 'OV/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }


}