<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TargetBranchModel extends MY_Model
{
    protected $table = 'ref_target_branches';
    protected $tableVms = 'ref_branches';

    /**
     * TargetModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        if ($this->config->item('sso_enable')) {
            $this->tableVms = env('DB_VMS_DATABASE') . '.ref_branches';
        }
    }

    /**
     * Get active record query builder for all related warehouse data selection.
     *
     * @param null $TargetTypeId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($TargetTypeId = null)
    {
        $targets = parent::getBaseQuery()
            ->select(['ref_branches.branch AS branch_name',
                    'branchVms.branch AS branch_name_vms'])
            ->join('ref_branches', 'ref_branches.id = ref_target_branches.id_branch', 'left')
            ->join($this->tableVms.' AS branchVms', 'branchVms.id = ref_target_branches.id_branch', 'left');

        return $targets;
    }

    /**
     * Delete target branch by target.
     * @param $targetId
     * @return mixed
     */
    public function deleteTargetBranchByTargetId($targetId)
    {
        $deleteCondition = [
            'id_target' => $targetId
        ];
        return $this->db->delete($this->table, $deleteCondition);
    }  

    /**
     * Insert single or batch data handling type components.
     * @param $data
     * @return bool
     */
    public function insertTargetBranch($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }
}