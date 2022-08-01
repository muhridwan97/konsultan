<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransporterEntryPermitChecklistPhotoModel extends MY_Model
{
    protected $table = 'transporter_entry_permit_checklist_photos';

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
                'transporter_entry_permit_checklists.id_tep',
                'transporter_entry_permit_checklists.type',
            ])
            ->join('transporter_entry_permit_checklists', 'transporter_entry_permit_checklists.id = transporter_entry_permit_checklist_photos.id_tep_checklist');
    }
}