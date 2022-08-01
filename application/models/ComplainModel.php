<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ComplainModel extends MY_Model
{
    protected $table = 'complains';
    const VIA_COMPLAIN = ["CHAT", "PHONE", "EMAIL", "TALK", "SYSTEM"];
    const STATUS_SUBMITTED = "SUBMITTED";
    const STATUS_ON_REVIEW = "ON REVIEW";
    const STATUS_PROCESSED = "PROCESSED";
    const STATUS_RATING = "RATING";
    const STATUS_CLOSED = "CLOSED";
    const STATUS_FINAL = "FINAL";
    const STATUS_FINAL_CONCLUSION = "FINAL CONCLUSION";
    const STATUS_FINAL_RESPONSE = "FINAL RESPONSE";
    const STATUS_CONCLUSION = "CONCLUSION";
    const STATUS_DISPROVE = "DISPROVE";
    const STATUS_APPROVE = "APPROVE";
    const STATUS_PENDING = "PENDING";
    const STATUS_REJECT = "REJECT";
    const STATUS_ACCEPTED = "ACCEPTED";

     /**
     * ComplainModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

     /**
     * Get complain base query.
     *
     * @param null $branchId
     * @param bool $withTrashed
     */
    public function getBaseQuery($branchId = null, $withTrashed = false)
    {
    	$getBaseQuery = $this->db->select([
    						'complains.*',
    						'ref_complain_categories.category',
    						'ref_complain_categories.value_type',
    						'ref_people.name As customer_name',
                            'ref_branches.branch',
                            'conclusion_categories.category AS conclusion_category'
    					])
    					->from($this->table)
    					->join('ref_complain_categories', 'ref_complain_categories.id = complains.id_complain_category', 'left')
    					->join('ref_complain_categories AS conclusion_categories', 'conclusion_categories.id = complains.id_conclusion_category', 'left')
    					->join('ref_people', 'ref_people.id = complains.id_customer', 'left')
                        ->join('ref_branches', 'ref_branches.id = complains.id_branch', 'left')
    					->group_by('complains.id');

    	return $getBaseQuery;
    }

     /**
     * Get auto number for complain.
     * @param string $type
     * @return string
     */
    public function getAutoNumberComplain($type = 'CM')
    {
        $orderData = $this->db->query("
            SELECT CAST(RIGHT(no_complain, 6) AS UNSIGNED) + 1 AS order_number 
            FROM complains 
            WHERE MONTH(created_at) = MONTH(NOW()) 
              AND YEAR(created_at) = YEAR(NOW())
              AND SUBSTRING(no_complain, 1, 2) = '$type'
            ORDER BY id DESC LIMIT 1
            ");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return $type . '/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }

    /**
     * Get auto number for complain.
     * @param string $type
     * @return string
     */
    public function getAutoNumberFTKP($type = 'FTKP')
    {
        $orderData = $this->db->query("
            SELECT CAST(RIGHT(ftkp, 6) AS UNSIGNED) + 1 AS order_number 
            FROM complains 
            WHERE MONTH(created_at) = MONTH(NOW()) 
              AND YEAR(created_at) = YEAR(NOW())
              AND SUBSTRING(ftkp, 1, 4) = '$type'
            ORDER BY id DESC LIMIT 1
            ");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return $type . '/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }

    /**
     * Get data by custom condition.
     *
     * @param $conditions
     * @param bool $resultRow
     * @param bool $withTrashed
     * @return array|int
     */
    public function getBySubmitApproval($resultRow = false, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery();
        
        $baseQuery->group_start()
        ->group_start()
            ->where('status', ComplainModel::STATUS_ON_REVIEW)
        ->group_end()
        ->or_group_start()
            ->where('status', ComplainModel::STATUS_PROCESSED)
            ->where('status_investigation !=', ComplainModel::STATUS_APPROVE)
            ->where('conclusion', '')
        ->group_end()
        ->group_end();

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if($resultRow === 'COUNT') {
            return $baseQuery->count_all_results();
        } else if ($resultRow) {
            return $baseQuery->get()->row_array();
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get data by custom condition.
     *
     * @param $conditions
     * @param bool $resultRow
     * @param bool $withTrashed
     * @return array|int
     */
    public function getByDisproveApproval($branchId = null, $resultRow = false, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery();
        
        $baseQuery->group_start()
        ->group_start()
            ->where('status', ComplainModel::STATUS_DISPROVE)
        ->group_end()
        ->or_group_start()
            ->where('status', ComplainModel::STATUS_PROCESSED)
            ->where('status_investigation !=', ComplainModel::STATUS_APPROVE)
            ->where('conclusion is not null', NULL)
        ->group_end()
        ->group_end();

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if (!empty($branchId)) {
            $baseQuery->where($this->table . '.id_branch', $branchId);
        }

        if($resultRow === 'COUNT') {
            return $baseQuery->count_all_results();
        } else if ($resultRow) {
            return $baseQuery->get()->row_array();
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get data by custom condition.
     *
     * @param $branchId
     * @return array|int
     */
    public function getBySubmitApprovalByBranch($branchId, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery();
        
        $baseQuery->group_start()
        ->group_start()
            ->where('status', ComplainModel::STATUS_ON_REVIEW)
        ->group_end()
        ->or_group_start()
            ->where('status', ComplainModel::STATUS_PROCESSED)
            ->where('status_investigation !=', ComplainModel::STATUS_APPROVE)
            ->where('conclusion', '')
        ->group_end()
        ->group_end();

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        $baseQuery->where($this->table . '.id_branch', $branchId);
        

        return $baseQuery->get()->result_array();
    }

    /**
     * Get data by custom condition.
     *
     * @param $conditions
     * @param bool $resultRow
     * @param bool $withTrashed
     * @return array|int
     */
    public function getByApprovalConclusion($resultRow = false, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery();
        
        $baseQuery->group_start()
            ->group_start()
                ->where('status', ComplainModel::STATUS_PROCESSED)
                ->where('conclusion is not null', NULL)
                ->group_end()
            ->or_group_start()
                ->where('status', ComplainModel::STATUS_DISPROVE)
            ->group_end()
        ->group_end();

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if($resultRow === 'COUNT') {
            return $baseQuery->count_all_results();
        } else if ($resultRow) {
            return $baseQuery->get()->row_array();
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get data by custom condition.
     *
     * @param $conditions
     * @param bool $resultRow
     * @param bool $withTrashed
     * @return array|int
     */
    public function getByConclusionClose($resultRow = false, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery();
        
        $baseQuery
            ->where('status', ComplainModel::STATUS_CONCLUSION)
            ->where('status_investigation', ComplainModel::STATUS_APPROVE);

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if($resultRow === 'COUNT') {
            return $baseQuery->count_all_results();
        } else if ($resultRow) {
            return $baseQuery->get()->row_array();
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get data by custom condition.
     *
     * @param $conditions
     * @param bool $resultRow
     * @param bool $withTrashed
     * @return array|int
     */
    public function getByNotClose($resultRow = false, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery();
        
        $baseQuery
            ->where('status_investigation', ComplainModel::STATUS_APPROVE)
            ->where('status !=', ComplainModel::STATUS_DISPROVE)
            ->where('status !=', ComplainModel::STATUS_CLOSED);

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if($resultRow === 'COUNT') {
            return $baseQuery->count_all_results();
        } else if ($resultRow) {
            return $baseQuery->get()->row_array();
        }

        return $baseQuery->get()->result_array();
    }
    
    /**
     * Get data by custom condition.
     *
     * @param $conditions
     * @param bool $resultRow
     * @param bool $withTrashed
     * @return array|int
     */
    public function getByCondition($conditions, $resultRow = false, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery(get_if_exist($conditions, '_id_branch', null));

        foreach ($conditions as $key => $condition) {
            if ($key == '_id_branch') continue;

            if (is_array($condition)) {
                if (!empty($condition)) {
                    $baseQuery->where_in($key, $condition);
                }
            } else {
                $baseQuery->where($key, $condition);
            }
        }

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        $baseQuery->order_by($this->table . '.' . $this->id, 'desc');

        if($resultRow === 'COUNT') {
            return $baseQuery->count_all_results();
        } else if ($resultRow) {
            return $baseQuery->get()->row_array();
        }

        return $baseQuery->get()->result_array();
    }
}