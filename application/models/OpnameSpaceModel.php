<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class OpnameSpaceModel extends MY_Model
{
    protected $table = 'opname_spaces';
    
    const STATUS_PENDING = 'PENDING';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_COMPLETED = 'COMPLETED'; 
    const STATUS_PROCESSED = 'PROCESSED';
    const STATUS_REOPENED = 'REOPENED';
    const STATUS_CLOSED = 'CLOSED';
    const STATUS_VALIDATED = 'VALIDATED';
    /**
     * Get active record query builder for all related warehouse data selection.
     *
     * @param null $ChecklistTypeId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($ChecklistTypeId = null)
    {

        $opnameSpaces = $this->db
            ->select([
                'opname_spaces.*',
                'ref_branches.branch as branch',
            ])
            ->from('opname_spaces')
            ->join('ref_branches', 'ref_branches.id = opname_spaces.id_branch', 'left');

        return $opnameSpaces;
    }

    /**
     * Get all cycle counts.
     * @return array
     */
    public function getAllOpnameSpaces()
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $opnameSpaces = $this->getBaseQuery();

        if (!empty($branchId)) {
            $opnameSpaces->where('opname_spaces.id_branch', $branchId);
        }
        return $opnameSpaces->get()->result_array();

    }

    /**
     * Get data cycle counts by branchId and by Date.
     * @return array
     */
    public function opnameSpaceCheck($branchId, $date)
    {

        $opnameSpaces = $this->getBaseQuery()
                ->where('opname_spaces.id_branch', $branchId)
                ->where('opname_spaces.opname_space_date', $date);
        return $opnameSpaces->get()->result_array();

    }

    /**
     * Get data cycle counts by branchId
     * @return array
     */

    public function getBybranchId($id, $type)
    {
        $opnameSpaces = $this->getBaseQuery()->where('opname_spaces.id_branch', $id)->where('opname_spaces.type', $type)->order_by('opname_spaces.opname_spaces_date','DESC');

        return $opnameSpaces->get()->row_array();
    }

    /**
     * Get data cycle counts by branch, goods type, activity type
     * @return array
     */

    public function getLastDataByBranch($branch)
    {
        $opnameSpaces = $this->getBaseQuery()->where('opname_spaces.id_branch', $branch)
                        ->order_by('opname_spaces.opname_space_date','DESC');

        return $opnameSpaces->get()->row_array();
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
    public function getAutoNumberOpnameSpace()
    {
        $orderData = $this->db->query("
            SELECT IFNULL(CAST(RIGHT(no_opname_space, 6) AS UNSIGNED), 0) + 1 AS order_number 
            FROM opname_spaces 
            WHERE SUBSTR(no_opname_space, 7, 2) = MONTH(NOW()) 
            AND SUBSTR(no_opname_space, 4, 2) = DATE_FORMAT(NOW(), '%y')
            ORDER BY SUBSTR(no_opname_space FROM 4) DESC LIMIT 1
        ");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return 'OS/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }
  
}