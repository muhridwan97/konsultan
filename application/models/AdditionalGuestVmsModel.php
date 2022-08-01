<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AdditionalGuestVmsModel extends MY_Model
{
    protected $table = 'additional_guest';

    /**
     * AdditionalGuestVmsModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        if ($this->config->item('sso_enable')) {
            $this->table = env('DB_VMS_DATABASE') . '.additional_guest';
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
        $additional_guests = $this->db
            ->select('*')
            ->from($this->table)
            ->order_by('id');
        return $additional_guests;
    }

}