<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PlanTransporterEntryPermitModel extends MY_Model
{
    protected $table = 'plan_transporter_entry_permits';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery($branchId)
            ->select([
                'transporter_entry_permit_bookings.id_booking',
                'transporter_entry_permits.tep_code',
                'transporter_entry_permits.checked_in_at',
                'transporter_entry_permits.checked_out_at',
            ])
            ->join('transporter_entry_permits', 'transporter_entry_permits.id = plan_transporter_entry_permits.id_transporter_entry_permit')
            ->join('transporter_entry_permit_bookings', 'transporter_entry_permit_bookings.id_tep = transporter_entry_permits.id');
    }

}
