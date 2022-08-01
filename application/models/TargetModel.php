<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TargetModel extends MY_Model
{
    protected $table = 'ref_targets';
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
        $targets = $this->db
            ->select([
                'ref_targets.*',
            ])
            ->from('ref_targets');
            // ->join('ref_target_types', 'ref_target_types.id = ref_targets.id_target_type', 'left');

        return $targets;
    }
    
    public function getTargetTransporter(){
        $targets = $this->getBaseQuery()
                    ->select([
                        'ref_target_branches.target AS target_branch',
                        'ref_target_branches.id_branch AS id_branch',
                        'branch_vms.branch AS branch_name',
                    ])
                    ->join('ref_target_branches','ref_target_branches.id_target = ref_targets.id','left')
                    ->join($this->tableVms.' AS branch_vms','branch_vms.id = ref_target_branches.id_branch','left')
                    ->where($this->table.'.id',3);
        return $targets->get()->result_array();
    }

    public function getTargetForklift(){
        $targets = $this->getBaseQuery()
                    ->select([
                        'ref_target_branches.target AS target_branch',
                        'ref_target_branches.id_branch AS id_branch',
                        'branch_vms.branch AS branch_name',
                    ])
                    ->join('ref_target_branches','ref_target_branches.id_target = ref_targets.id','left')
                    ->join($this->tableVms.' AS branch_vms','branch_vms.id = ref_target_branches.id_branch','left')
                    ->where($this->table.'.id',2);
        return $targets->get()->result_array();
    }

    public function getTargetPerformance(){
        $targets = $this->getBaseQuery()
                    ->select([
                        'ref_target_branches.target AS target_branch',
                        'ref_target_branches.id_branch AS id_branch',
                        'branch_vms.branch AS branch_name',
                    ])
                    ->join('ref_target_branches','ref_target_branches.id_target = ref_targets.id','left')
                    ->join($this->tableVms.' AS branch_vms','branch_vms.id = ref_target_branches.id_branch','left')
                    ->where($this->table.'.id',1);
        return $targets->get()->result_array();
    }
  
}