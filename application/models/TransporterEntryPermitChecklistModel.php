<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransporterEntryPermitChecklistModel extends MY_Model
{
    protected $table = 'transporter_entry_permit_checklists';

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
    private function getBaseTepChecklistQuery()
    {
        $tepChecklists = $this->db
            ->select([
                'transporter_entry_permit_checklists.*',
                'prv_users.name AS created_name',
                'ref_containers.no_container as no_container',
                'transporter_entry_permits.tep_code as tep_code'
            ])
            ->from('transporter_entry_permit_checklists')
            ->join('transporter_entry_permit_containers', 'transporter_entry_permit_containers.id = transporter_entry_permit_checklists.id_container', 'left')
            ->join('ref_containers', 'ref_containers.id = transporter_entry_permit_containers.id_container', 'left')
            ->join('transporter_entry_permits', 'transporter_entry_permits.id = transporter_entry_permit_checklists.id_tep', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = transporter_entry_permit_checklists.created_by', 'left');

        return $tepChecklists;
    }


     /**
     * Get tep checklist data by tep Id.
     * @param $id
     * @param bool $withTrash
     * @return array
     */
    public function getTepChecklistByTepId($id)
    {
        $tepChecklists = $this->getBaseTepChecklistQuery()->where('transporter_entry_permit_checklists.id_tep', $id);


        return $tepChecklists->get()->result_array();
    }

    /**
     * Get TEP checklist data in by TEP Id.
     * @param $id
     * @param bool $withTrash
     * @return array
     */
    public function getTepChecklistInByTepId($id)
    {
        $Checklists = $this->getBaseTepChecklistQuery()->where('transporter_entry_permit_checklists.id_tep', $id)->where('transporter_entry_permit_checklists.type',"CHECK IN");


        return $Checklists->get()->result_array();
    }

    /**
     * Get Tep checklist data out by Tep Id.
     * @param $id
     * @param bool $withTrash
     * @return array
     */
    public function getTepChecklistOutByTepId($id)
    {
        $Checklists = $this->getBaseTepChecklistQuery()->where('transporter_entry_permit_checklists.id_tep', $id)->where('transporter_entry_permit_checklists.type',"CHECK OUT");


        return $Checklists->get()->result_array();
    }

     /**
     * Get single tep checklist in container data.
     * @param $id
     * @param bool $withTrash
     * @return array
     */
    public function getTepChecklistInById($id)
    {
        $tepChecklists = $this->getBaseTepChecklistQuery()->where('transporter_entry_permit_checklists.id_container', $id)
        						->where('transporter_entry_permit_checklists.type',"CHECK IN");


        return $tepChecklists->get()->row_array();
    }

     /**
     * Get single safe conduct checklist out container data.
     * @param $id
     * @param bool $withTrash
     * @return array
     */
    public function getTepChecklistOutById($id)
    {
        $tepChecklists = $this->getBaseTepChecklistQuery()->where('transporter_entry_permit_checklists.id_container', $id)
                                ->where('transporter_entry_permit_checklists.type',"CHECK OUT");


        return $tepChecklists->get()->row_array();
    }

    /**
     * Get single safe conduct checklist in goods data.
     * @param $id
     * @param bool $withTrash
     * @return array
     */
    public function getTepChecklistInGoodsById($id)
    {
        $tepChecklists = $this->getBaseTepChecklistQuery()->where('transporter_entry_permit_checklists.id_tep', $id)
                                ->where('transporter_entry_permit_checklists.id_container', null)
                                ->where('transporter_entry_permit_checklists.type',"CHECK IN");


        return $tepChecklists->get()->row_array();
    }

     /**
     * Get single safe conduct checklist out goods data.
     * @param $id
     * @param bool $withTrash
     * @return array
     */
    public function getTepChecklistOutGoodsById($id)
    {
        $tepChecklists = $this->getBaseTepChecklistQuery()->where('transporter_entry_permit_checklists.id_tep', $id)
                                ->where('transporter_entry_permit_checklists.id_container', null)
                                ->where('transporter_entry_permit_checklists.type',"CHECK OUT");


        return $tepChecklists->get()->row_array();
    }
    /**
     * Get single safe conduct checklist in goods data.
     * @param $id
     * @param bool $withTrash
     * @return array
     */
    public function getTepChecklistInPhotoById($id)
    {
        $tepChecklists = $this->getBaseTepChecklistQuery()->where('transporter_entry_permit_checklists.id_tep', $id)
                                ->where('transporter_entry_permit_checklists.type',"CHECK IN");


        return $tepChecklists->get()->result_array();
    }
     /**
     * Get single safe conduct checklist out goods data.
     * @param $id
     * @param bool $withTrash
     * @return array
     */
    public function getTepChecklistOutPhotoById($id)
    {
        $tepChecklists = $this->getBaseTepChecklistQuery()->where('transporter_entry_permit_checklists.id_tep', $id)
                                ->where('transporter_entry_permit_checklists.type',"CHECK OUT");
        return $tepChecklists->get()->result_array();
    }
  
}