<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ChecklistModel extends MY_Model
{
    protected $table = 'ref_checklists';

    /**
     * ChecklistModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active record query builder for all related warehouse data selection.
     *
     * @param null $ChecklistTypeId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($ChecklistTypeId = null)
    {
        $checklists = $this->db
            ->select([
                'ref_checklists.*',
                'ref_checklist_types.checklist_type',
            ])
            ->from('ref_checklists')
            ->join('ref_checklist_types', 'ref_checklist_types.id = ref_checklists.id_checklist_type', 'left');

        return $checklists;
    }

    

  
}