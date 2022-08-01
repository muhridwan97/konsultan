<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class SafeConductChecklistModel extends MY_Model
{
    protected $table = 'safe_conduct_checklists';

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
    private function getBaseSafeConductChecklistQuery()
    {
        $SafeConductChecklists = $this->db
            ->select([
                'safe_conduct_checklists.*',
                'prv_users.name AS created_name',
                'ref_containers.no_container as no_container',
                'safe_conducts.no_safe_conduct as no_safe_conduct',
                'safe_conducts.expedition_type'
            ])
            ->from('safe_conduct_checklists')
            ->join('safe_conduct_containers', 'safe_conduct_containers.id = safe_conduct_checklists.id_container', 'left')
            ->join('ref_containers', 'ref_containers.id = safe_conduct_containers.id_container', 'left')
            ->join('safe_conducts', 'safe_conducts.id = safe_conduct_checklists.id_safe_conduct', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = safe_conduct_checklists.created_by', 'left');

        return $SafeConductChecklists;
    }

       /**
     * Get safe conduct checklist data by safe conduct Id.
     * @param $id
     * @param bool $withTrash
     * @return array
     */
    public function getSafeConductChecklistBySafeConductId($id)
    {
        $Checklists = $this->getBaseSafeConductChecklistQuery()->where('safe_conduct_checklists.id_safe_conduct', $id);


        return $Checklists->get()->result_array();
    }


    /**
     * Get safe conduct checklist data in by safe conduct Id.
     * @param $id
     * @param bool $withTrash
     * @return array
     */
    public function getSafeConductChecklistInBySafeConductId($id)
    {
        $Checklists = $this->getBaseSafeConductChecklistQuery()->where('safe_conduct_checklists.id_safe_conduct', $id)->where('safe_conduct_checklists.type',"CHECK IN");


        return $Checklists->get()->result_array();
    }

    /**
     * Get safe conduct checklist data out by safe conduct Id.
     * @param $id
     * @param bool $withTrash
     * @return array
     */
    public function getSafeConductChecklistOutBySafeConductId($id)
    {
        $Checklists = $this->getBaseSafeConductChecklistQuery()->where('safe_conduct_checklists.id_safe_conduct', $id)->where('safe_conduct_checklists.type',"CHECK OUT");


        return $Checklists->get()->result_array();
    }


     /**
     * Get single safe conduct checklist in container data.
     * @param $id
     * @param bool $withTrash
     * @return array
     */
    public function getSafeConductChecklistInById($id)
    {
        $SafeConductChecklists = $this->getBaseSafeConductChecklistQuery()->where('safe_conduct_checklists.id_container', $id)
        						->where('safe_conduct_checklists.type',"CHECK IN");


        return $SafeConductChecklists->get()->row_array();
    }

     /**
     * Get single safe conduct checklist out container data.
     * @param $id
     * @param bool $withTrash
     * @return array
     */
    public function getSafeConductChecklistOutById($id)
    {
        $SafeConductChecklists = $this->getBaseSafeConductChecklistQuery()->where('safe_conduct_checklists.id_container', $id)
                                ->where('safe_conduct_checklists.type',"CHECK OUT");


        return $SafeConductChecklists->get()->row_array();
    }

    /**
     * Get single safe conduct checklist in goods data.
     * @param $id
     * @param bool $withTrash
     * @return array
     */
    public function getSafeConductChecklistInGoodsById($id)
    {
        $SafeConductChecklists = $this->getBaseSafeConductChecklistQuery()->where('safe_conduct_checklists.id_safe_conduct', $id)
                                ->where('safe_conduct_checklists.id_container', null)
                                ->where('safe_conduct_checklists.type',"CHECK IN");


        return $SafeConductChecklists->get()->row_array();
    }

     /**
     * Get single safe conduct checklist out goods data.
     * @param $id
     * @param bool $withTrash
     * @return array
     */
    public function getSafeConductChecklistOutGoodsById($id)
    {
        $SafeConductChecklists = $this->getBaseSafeConductChecklistQuery()->where('safe_conduct_checklists.id_safe_conduct', $id)
                                ->where('safe_conduct_checklists.id_container', null)
                                ->where('safe_conduct_checklists.type',"CHECK OUT");


        return $SafeConductChecklists->get()->row_array();
    }
  
}