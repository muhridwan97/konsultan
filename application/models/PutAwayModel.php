<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PutAwayModel extends MY_Model
{
    protected $table = 'put_away';
    
    const STATUS_PENDING = 'PENDING';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_COMPLETED = 'COMPLETED'; 
    const STATUS_PROCESSED = 'PROCESSED';
    const STATUS_REOPENED = 'REOPENED';
    const STATUS_CLOSED = 'CLOSED';
    const STATUS_NOT_PROCESSED = 'NOT PROCESSED';
    const STATUS_NOT_VALIDATED = 'NOT VALIDATED';
    const STATUS_VALIDATED = 'VALIDATED';
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
                'put_away.*',
                'ref_branches.branch as branch',
                'ref_operation_cut_offs.shift',
                'ref_operation_cut_offs.start AS start_shift',
                'ref_operation_cut_offs.end AS end_shift',
                UserModel::$tableUser . '.username AS validate_name',
            ])
            ->from('put_away')
            ->join('ref_branches', 'ref_branches.id = put_away.id_branch', 'left')
            ->join('ref_operation_cut_offs', 'ref_operation_cut_offs.id = put_away.id_shift', 'left')
            ->join(UserModel::$tableUser, UserModel::$tableUser . '.id = put_away.validated_by', 'left');

        return $cycleCounts;
    }

    /**
     * Get all cycle counts.
     * @return array
     */
    public function getAllPutAway()
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $putAway = $this->getBaseQuery()
                ->order_by('put_away.put_away_date', 'DESC');

        if (!empty($branchId)) {
            $putAway->where('put_away.id_branch', $branchId);
        }
        return $putAway->get()->result_array();

    }

    /**
     * Get data cycle counts by branchId and by Date.
     * @return array
     */
    public function putAwayCheck($branchId, $shiftId, $date)
    {

        $putAways = $this->getBaseQuery()->where('put_away.id_branch', $branchId)->where('put_away.id_shift', $shiftId)->where('put_away.put_away_date', $date);
        return $putAways->get()->result_array();

    }

    /**
     * Get data cycle counts by branchId
     * @return array
     */

    public function getBybranchId($id, $shiftId)
    {
        $cycleCounts = $this->getBaseQuery()->where('put_away.id_branch', $id)->where('put_away.id_shift', $shiftId)->order_by('put_away.put_away_date','DESC');

        return $cycleCounts->get()->row_array();
    }

    /**
     * Get data cycle counts by branch, shift, activity type
     * @return array
     */

    public function getLastDataByBranchByShiftByActivity($branch, $shiftId, $activity)
    {
        $cycleCounts = $this->getBaseQuery()->where('put_away.id_branch', $branch)
                        ->where('put_away.id_shift', $shiftId)
                        ->where('put_away.activity_type', $activity)
                        ->order_by('put_away.put_away_date','DESC');

        return $cycleCounts->get()->row_array();
    }

    /**
     * Update cycle count.
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updatePutAway($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

     /**
     * Get auto number for cycle count.
     *
     * @return string
     */
    public function getAutoNumberPutAway()
    {
        $orderData = $this->db->query("
            SELECT IFNULL(CAST(RIGHT(no_put_away, 6) AS UNSIGNED), 0) + 1 AS order_number 
            FROM put_away 
            WHERE SUBSTR(no_put_away, 7, 2) = MONTH(NOW()) 
            AND SUBSTR(no_put_away, 4, 2) = DATE_FORMAT(NOW(), '%y')
            ORDER BY SUBSTR(no_put_away FROM 4) DESC LIMIT 1
        ");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return 'PA/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }
  
}