<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class StatusHistoryVmsModel extends MY_Model
{
    protected $table = 'status_histories';

    const TYPE_GUEST = 'guest';
    
    /**
     * AreaModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        if ($this->config->item('sso_enable')) {
            $this->table = env('DB_VMS_DATABASE') . '.status_histories';
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
        $status = $this->db
            ->select('*')
            ->from($this->table)
            ->order_by('id');
        return $status;
    }

}