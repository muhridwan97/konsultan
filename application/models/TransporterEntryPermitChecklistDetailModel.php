<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransporterEntryPermitChecklistDetailModel extends MY_Model
{
    protected $table = 'transporter_entry_permit_checklist_details';


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
    private function getBaseTepChecklistDetailsQuery()
    {
        $SafeConductChecklistDetails = $this->db
            ->select([
                'transporter_entry_permit_checklist_details.*',
                'ref_checklists.description as checklist_name',
            ])
            ->from('transporter_entry_permit_checklist_details')
            ->join('ref_checklists', 'ref_checklists.id = transporter_entry_permit_checklist_details.id_checklist', 'left');

        return $SafeConductChecklistDetails;
    }

     /**
     * Get single tep checklist data.
     * @param $id
     * @param bool $withTrash
     * @return array
     */
    public function getTepChecklistDetailByChecklistId($id)
    {
        $getTepChecklistDetailByChecklistId = $this->getBaseTepChecklistDetailsQuery()->where('id_tep_checklist', $id);


        return $getTepChecklistDetailByChecklistId->get()->result_array();
    }

  
}