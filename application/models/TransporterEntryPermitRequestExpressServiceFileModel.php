<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransporterEntryPermitRequestExpressServiceFileModel extends MY_Model
{
    protected $table = 'transporter_entry_permit_request_express_service_files';

    /**
     * Get files by tep request.
     * @param $itemPhotoId
     * @return mixed
     */
    public function getFilesByTepReq($tepReqId)
    {
        $files = $this->getBaseQuery()
            ->where('transporter_entry_permit_request_express_service_files.id_tep_req', $tepReqId);

        return $files->get()->result_array();
    }
}