<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CycleCountModel extends MY_Model
{
    protected $table = 'cycle_count';
    
    const STATUS_PENDING = 'PENDING';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_COMPLETED = 'COMPLETED'; 
    const STATUS_PROCESSED = 'PROCESSED';
    const STATUS_REOPENED = 'REOPENED';
    const STATUS_CLOSED = 'CLOSED';
    /**
     * Get active record query builder for all related warehouse data selection.
     *
     * @param null $ChecklistTypeId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($ChecklistTypeId = null)
    {

        $cycleCounts = $this->db
            ->select([
                'cycle_count.*',
                'ref_branches.branch as branch',
            ])
            ->from('cycle_count')
            ->join('ref_branches', 'ref_branches.id = cycle_count.id_branch', 'left');

        return $cycleCounts;
    }

    /**
     * Get all cycle counts.
     * @return array
     */
    public function getAllCycleCounts()
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $cycleCounts = $this->getBaseQuery();

        if (!empty($branchId)) {
            $cycleCounts->where('cycle_count.id_branch', $branchId);
        }
        return $cycleCounts->get()->result_array();

    }

    /**
     * Get data cycle counts by branchId and by Date.
     * @return array
     */
    public function CycleCountCheck($branchId, $type, $date)
    {

        $cycleCounts = $this->getBaseQuery()->where('cycle_count.id_branch', $branchId)->where('cycle_count.type', $type)->where('cycle_count.cycle_count_date', $date);
        return $cycleCounts->get()->result_array();

    }

    /**
     * Get data cycle counts by branchId
     * @return array
     */

    public function getBybranchId($id, $type)
    {
        $cycleCounts = $this->getBaseQuery()->where('cycle_count.id_branch', $id)->where('cycle_count.type', $type)->order_by('cycle_count.cycle_count_date','DESC');

        return $cycleCounts->get()->row_array();
    }

    /**
     * Get data cycle counts by branch, goods type, activity type
     * @return array
     */

    public function getLastDataByBranchByTypeByActivity($branch, $type, $activity)
    {
        $cycleCounts = $this->getBaseQuery()->where('cycle_count.id_branch', $branch)
                        ->where('cycle_count.type', $type)
                        ->where('cycle_count.activity_type', $activity)
                        ->order_by('cycle_count.cycle_count_date','DESC');

        return $cycleCounts->get()->row_array();
    }

    /**
     * Update cycle count.
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateCycleCount($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

     /**
     * Get auto number for cycle count.
     *
     * @return string
     */
    public function getAutoNumberCycleCount()
    {
        $orderData = $this->db->query("
            SELECT IFNULL(CAST(RIGHT(no_cycle_count, 6) AS UNSIGNED), 0) + 1 AS order_number 
            FROM cycle_count 
            WHERE SUBSTR(no_cycle_count, 7, 2) = MONTH(NOW()) 
            AND SUBSTR(no_cycle_count, 4, 2) = DATE_FORMAT(NOW(), '%y')
            ORDER BY SUBSTR(no_cycle_count FROM 4) DESC LIMIT 1
        ");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return 'CC/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }
  
}