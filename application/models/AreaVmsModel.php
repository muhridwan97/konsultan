<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AreaVmsModel extends MY_Model
{
    protected $table = 'ref_areas';

    /**
     * AreaModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        if ($this->config->item('sso_enable')) {
            $this->table = env('DB_VMS_DATABASE') . '.ref_areas';
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
        $areas = $this->db
            ->select('*')
            ->from($this->table)
            ->order_by('id');
        return $areas;
    }

}