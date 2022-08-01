<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransporterEntryPermitRequestTepModel extends MY_Model
{
    protected $table = 'transporter_entry_permit_request_tep';

    /**
     * TEP Customers Model constructor.
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
    	$baseQuery = parent::getBaseQuery()
            ->select([
                'transporter_entry_permit_requests.no_request',
                'transporter_entry_permit_requests.armada',
            ])
            ->join('transporter_entry_permit_requests', 'transporter_entry_permit_requests.id = transporter_entry_permit_request_tep.id_request');
        return $baseQuery;

    }

    /**
     * Get another id request by request in same tep.
     *
     * @param $requestId
     */
    public function getAnotherReqByReq($requestId){
        $baseQuery = $this->getBaseQuery()
                ->where('transporter_entry_permit_request_tep.id_tep IN (SELECT id_tep FROM transporter_entry_permit_request_tep where transporter_entry_permit_request_tep.id_request = '.$requestId.')',null);
        
        return $baseQuery->get()->result_array();
    }

}
