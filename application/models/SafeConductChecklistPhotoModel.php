<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SafeConductChecklistPhotoModel extends MY_Model
{
    protected $table = 'safe_conduct_checklist_photos';

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
                'safe_conduct_checklists.id_safe_conduct',
                'safe_conduct_checklists.type',
            ])
            ->join('safe_conduct_checklists', 'safe_conduct_checklists.id = safe_conduct_checklist_photos.id_safe_conduct_checklist');
    }
}