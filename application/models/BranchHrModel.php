<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BranchHrModel extends MY_Model
{
    protected $table = 'ref_work_locations';

    /**
     * BranchModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        if ($this->config->item('sso_enable')) {
            $this->table = env('DB_HR_DATABASE') . '.ref_work_locations';
        }
    }

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        $branches = $this->db
            ->select('*')
            ->from($this->table)
            ->order_by('id');
        return $branches;
    }

}