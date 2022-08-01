<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class TransporterEntryPermitRequestHoldReferenceModel
 */
class TransporterEntryPermitRequestHoldReferenceModel extends MY_Model
{
    protected $table = 'transporter_entry_permit_request_hold_references';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        $baseQuery = parent::getBaseQuery()
            ->select([
                'transporter_entry_permit_requests.id AS id_tep_request',
                'transporter_entry_permit_requests.no_request',
                'transporter_entry_permit_requests.status AS status_request',
                'transporter_entry_permit_requests.armada',
                'transporter_entry_permit_request_uploads.id_upload',
                'uploads.no_upload',
                'bookings.no_booking',
                'bookings.no_reference',
            ])
            ->join('transporter_entry_permit_request_uploads', 'transporter_entry_permit_request_uploads.id = transporter_entry_permit_request_hold_references.id_tep_request_upload', 'left')
            ->join('uploads', 'uploads.id = transporter_entry_permit_request_uploads.id_upload', 'left')
            ->join('bookings', 'bookings.id_upload = uploads.id', 'left')
            ->join('transporter_entry_permit_requests', 'transporter_entry_permit_requests.id = transporter_entry_permit_request_uploads.id_request', 'left');

        if (!empty($branchId)) {
            $baseQuery->where('transporter_entry_permits.id_branch', $branchId);
        }

        return $baseQuery;
    }

}
