<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransporterEntryPermitUploadModel extends MY_Model
{
    protected $table = 'transporter_entry_permit_uploads';

    /**
     * TEP Bookings Model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

     /**
     * Get active record query builder for all related tep data.
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = NULL)
    {
    	$baseQuery = parent::getBaseQuery();
        return $baseQuery;

    }
}
