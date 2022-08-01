<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ComplainHistoryModel extends MY_Model
{
    protected $table = 'complain_histories';
  
    /**
     * Complain histories Model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

     /**
     * Get active record query builder for all related complain data.
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = NULL)
    {
        $baseQuery = parent::getBaseQuery()
        ->select([
            'prv_users.name AS creator_name',
        ])
        ->join(UserModel::$tableUser, 'complain_histories.created_by = prv_users.id', 'left');
        
        return $baseQuery;

    }

    /**
     * Get last disprove from history by custom condition.
     *
     * @param $id
     * @return array|int
     */
    public function getLastDisprove($id)
    {
        $baseQuery = $this->getBaseQuery()
                ->where('complain_histories.id_complain', $id)
                ->where('complain_histories.status', ComplainModel::STATUS_DISPROVE)
                ->order_by('created_at','desc');

        return $baseQuery->get()->row_array();
    }

    /**
     * Get last response from history by custom condition.
     *
     * @param $id
     * @return array|int
     */
    public function getLastResponse($id)
    {
        $baseQuery = $this->getBaseQuery()
                ->where('complain_histories.id_complain', $id)
                ->where('complain_histories.status', ComplainModel::STATUS_PROCESSED)
                ->order_by('created_at','desc');

        return $baseQuery->get()->row_array();
    }

    /**
     * Get last response from history by custom condition.
     *
     * @param $id
     * @return array|int
     */
    public function getLastOnReview($id)
    {
        $baseQuery = $this->getBaseQuery()
                ->where('complain_histories.id_complain', $id)
                ->where('complain_histories.status', ComplainModel::STATUS_ON_REVIEW)
                ->order_by('created_at','desc');

        return $baseQuery->get()->row_array();
    }

    /**
     * Get last response from history by custom condition.
     *
     * @param $id
     * @return array|int
     */
    public function getLastApproveProcess($id)
    {
        $baseQuery = $this->getBaseQuery()
                ->where('complain_histories.id_complain', $id)
                ->where('complain_histories.status_investigation', ComplainModel::STATUS_APPROVE)
                ->order_by('created_at','desc');

        return $baseQuery->get()->row_array();
    }

    /**
     * Get last response from history by custom condition.
     *
     * @param $id
     * @return array|int
     */
    public function getLastConclusion($id)
    {
        $baseQuery = $this->getBaseQuery()
                ->where('complain_histories.id_complain', $id)
                ->where('complain_histories.status', ComplainModel::STATUS_CONCLUSION)
                ->order_by('created_at','desc');

        return $baseQuery->get()->row_array();
    }

    /**
     * Get last response from history by custom condition.
     *
     * @param $id
     * @return array|int
     */
    public function getHistoryDisprove($id)
    {
        $baseQuery = $this->getBaseQuery()
                ->where('complain_histories.id_complain', $id)
                ->where('complain_histories.created_at>=(SELECT created_at FROM complain_histories WHERE complain_histories.id_complain = "'.$id.'" AND complain_histories.status = "CONCLUSION" ORDER BY created_at ASC LIMIT 1)', null)
                ->where('complain_histories.created_at<=(SELECT created_at FROM complain_histories WHERE complain_histories.id_complain = "'.$id.'" AND complain_histories.status = "CONCLUSION" ORDER BY created_at DESC LIMIT 1)', null);

        return $baseQuery->get()->result_array();
    }
}