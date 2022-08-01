<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SafeConductChecklistDetailModel extends MY_Model
{
    protected $table = 'safe_conduct_checklist_details';


    /**
     * UploadDocumentModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active record query builder for all related document data selection.
     * @return CI_DB_query_builder
     */
    private function getDetailsBaseQuery()
    {
        $SafeConductChecklistDetails = $this->db
            ->select([
                'safe_conduct_checklist_details.*',
                'ref_checklists.description as checklist_name',
            ])
            ->from('safe_conduct_checklist_details')
            ->join('ref_checklists', 'ref_checklists.id = safe_conduct_checklist_details.id_checklist', 'left');

        return $SafeConductChecklistDetails;
    }

     /**
     * Get single safe conduct checklist data.
     * @param $id
     * @param bool $withTrash
     * @return array
     */
    public function getChecklistDetailByChecklistId($id)
    {
        $details = $this->getDetailsBaseQuery()->where('id_safe_conduct_checklist', $id);

        return $details->get()->result_array();
    }

  
}