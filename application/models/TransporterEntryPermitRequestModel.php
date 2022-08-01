<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransporterEntryPermitRequestModel extends MY_Model
{
    protected $table = 'transporter_entry_permit_requests';

    /**
     * TEP Customers Model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

     /**
     * Get active record query builder for all related tep data.
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = NULL)
    {
    	$baseQuery = parent::getBaseQuery();
        return $baseQuery;

    }
    
    function getSlotRequest($filters = []){
        $branchId = get_active_branch('id');
        $baseQuery = parent::getBaseQuery()
            ->where($this->table.'.id_branch', $branchId)
            ->where($this->table.'.status !=', 'SKIP')
            ->group_by('IFNULL('.$this->table.'.queue_time,'.$this->table.'.id)');
            
        if (key_exists('tep_date', $filters) && !empty($filters['tep_date'])) {
            $baseQuery->where($this->table.'.tep_date', $filters['tep_date']);
        }else{
            $baseQuery->where('date_format('.$this->table.'.created_at,"%Y-%m-%d") = date_format(now(),"%Y-%m-%d")');
        }
        return $baseQuery->get()->result_array();
    }

    function getAll($filters = [], $withTrashed = false){
        if (empty($filters['date'])) {
            $filters['date'] = date('Y-m-d',strtotime('+1 days',time()));
        } 
        $branchId = get_active_branch('id');
        $baseQuery = $this->db
            ->select([
                'transporter_entry_permit_requests.*',
                'ref_people.name AS customer_name',
                'prv_users.name AS set_name',
                'uploads.description AS no_reference',
                'upload_in.description AS no_reference_in',
                'GROUP_CONCAT(DISTINCT SUBSTRING(upload_multi.description, -4)) AS no_reference_multi',
                'GROUP_CONCAT(DISTINCT SUBSTRING(upload_multi_in.description, -4)) AS no_reference_in_multi',
                'GROUP_CONCAT(DISTINCT transporter_entry_permit_request_uploads.id_upload) AS id_upload_multi',
                'transporter_entry_permits.expired_at',
                'GROUP_CONCAT(DISTINCT transporter_entry_permits.tep_code SEPARATOR " ") as tep_code',
                'IFNULL(tep_request_tep.slot_created, 0) AS slot_created',
            ])
            ->from($this->table)
            ->join('ref_people', 'ref_people.id = transporter_entry_permit_requests.id_customer', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = transporter_entry_permit_requests.set_by','left')
            ->join('transporter_entry_permit_request_uploads','transporter_entry_permit_request_uploads.id_request = '.$this->table.'.id','left')
            ->join('uploads','uploads.id='.$this->table.'.id_upload','left')
            ->join('uploads AS upload_in','upload_in.id = uploads.id_upload','left')
            ->join('uploads AS upload_multi','upload_multi.id=transporter_entry_permit_request_uploads.id_upload','left')
            ->join('uploads AS upload_multi_in','upload_multi_in.id = upload_multi.id_upload','left')
            ->join('transporter_entry_permit_request_tep','transporter_entry_permit_request_tep.id_request = '.$this->table.'.id','left')
            ->join('transporter_entry_permits','transporter_entry_permits.id = transporter_entry_permit_request_tep.id_tep','left')
            ->join('(SELECT id_request, COUNT(id_request) AS slot_created FROM transporter_entry_permit_request_tep
                    GROUP BY id_request) AS tep_request_tep','tep_request_tep.id_request = '.$this->table.'.id','left')
            ->where('transporter_entry_permit_requests.id_branch', $branchId)
            // ->or_group_start()
            //     ->where('transporter_entry_permit_requests.armada','TCI')
            //     ->where('transporter_entry_permit_requests.tep_date IS NULL')
            //     ->where('transporter_entry_permit_requests.id_branch', $branchId)
            // ->group_end()
            ->having("transporter_entry_permit_requests.armada = 'TCI' AND transporter_entry_permit_requests.status = 'PENDING'")
            ->or_having('date(transporter_entry_permit_requests.tep_date)', $filters['date'])
            ->group_by('transporter_entry_permit_requests.id');

        if (key_exists('status', $filters) && !empty($filters['status'])) {
            $baseQuery->where($this->table.'.status', $filters['status']);
        }

        if (key_exists('armada', $filters) && !empty($filters['armada'])) {
            $baseQuery->where($this->table.'.armada', $filters['armada']);
        }

        if (key_exists('request', $filters) && !empty($filters['request'])) {
            $baseQuery->where_in($this->table.'.id', $filters['request']);
        }

        if (key_exists('q', $filters) && !empty(trim($filters['q']))) {
            $search = trim($filters['q']);
            $baseQuery
                ->group_start()
                ->like('transporter_entry_permit_requests.no_request', $search)
                ->or_like('transporter_entry_permit_requests.category', $search)
                ->or_like('transporter_entry_permit_requests.armada', $search)
                ->or_where("EXISTS (
                    SELECT tep_request_items.id 
                    FROM transporter_entry_permit_request_uploads AS tep_request_items
                    INNER JOIN ref_goods ON ref_goods.id = tep_request_items.id_goods
                    WHERE tep_request_items.id_request = transporter_entry_permit_requests.id 
                        AND ref_goods.name LIKE '%{$search}%'
                )")
                ->group_end();
        }

        return $baseQuery->get()->result_array();
    }

    function getMinTime($filters = []){
        $branchId = get_active_branch('id');
        $query = $this->db->select('MAX('.$this->table.'.queue_time) AS min_time')
            ->from($this->table)
            ->where($this->table.'.id_branch', $branchId);
        if (key_exists('tep_date', $filters) && !empty($filters['tep_date'])) {
            $query->where($this->table.'.tep_date', $filters['tep_date']);
        }else{
            $query->where('date_format('.$this->table.'.created_at,"%Y-%m-%d") = date_format(now(),"%Y-%m-%d")');
        }
        return $query->get()->row_array();
    }

    /**
     * Get auto number for request.
     * @param string $type
     * @return string
     */
    public function getAutoNumberRequest($type = 'REQ')
    {
        $orderData = $this->db->query("
            SELECT CAST(RIGHT(no_request, 6) AS UNSIGNED) + 1 AS order_number 
            FROM transporter_entry_permit_requests 
            WHERE MONTH(created_at) = MONTH(NOW()) 
			  AND YEAR(created_at) = YEAR(NOW())
			  AND SUBSTRING(no_request, 1, 3) = '$type'
            ORDER BY id DESC LIMIT 1
            ");
            
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return $type . '/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }

    function getByRequestId($id){
        $baseQuery = $this->db
            ->select([
                'transporter_entry_permit_requests.*',
                'ref_people.name AS customer_name',
                'prv_users.name AS set_name',
                'uploads.description AS no_reference',
                'upload_in.description AS no_reference_in',
                'GROUP_CONCAT(DISTINCT SUBSTRING(upload_multi.description, -4)) AS no_reference_multi',
                'GROUP_CONCAT(DISTINCT SUBSTRING(upload_multi_in.description, -4)) AS no_reference_in_multi',
                'GROUP_CONCAT(DISTINCT transporter_entry_permit_request_uploads.id_upload) AS id_upload_multi',
                'transporter_entry_permits.expired_at',
                'IFNULL(tep_request_tep.slot_created, 0) AS slot_created',
                'COUNT(DISTINCT transporter_entry_permits.tep_code) as count_tep_code',
            ])
            ->from($this->table)
            ->join('ref_people', 'ref_people.id = transporter_entry_permit_requests.id_customer', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = transporter_entry_permit_requests.set_by','left')
            ->join('transporter_entry_permit_request_uploads','transporter_entry_permit_request_uploads.id_request = '.$this->table.'.id','left')
            ->join('uploads','uploads.id='.$this->table.'.id_upload','left')
            ->join('uploads AS upload_in','upload_in.id = uploads.id_upload','left')
            ->join('uploads AS upload_multi','upload_multi.id=transporter_entry_permit_request_uploads.id_upload','left')
            ->join('uploads AS upload_multi_in','upload_multi_in.id = upload_multi.id_upload','left')
            ->join('transporter_entry_permit_request_tep','transporter_entry_permit_request_tep.id_request = '.$this->table.'.id','left')
            ->join('transporter_entry_permits','transporter_entry_permits.id = transporter_entry_permit_request_tep.id_tep','left')
            ->join('(SELECT id_request, COUNT(id_request) AS slot_created FROM transporter_entry_permit_request_tep
                    GROUP BY id_request) AS tep_request_tep','tep_request_tep.id_request = '.$this->table.'.id','left')
            ->where('transporter_entry_permit_requests.id', $id)
            ->group_by('transporter_entry_permit_requests.id');

        return $baseQuery->get()->row_array();
    }

    /**
     * Get request by id tep
     * @return array
     */
    public function getRequestByTep($id_tep)
    {
        $baseQuery = $this->getBaseQuery()
                    ->join('transporter_entry_permit_request_tep','transporter_entry_permit_request_tep.id_request=transporter_entry_permit_requests.id','left')
                    ->where('transporter_entry_permit_request_tep.id_tep',$id_tep);

        return $baseQuery->get()->result_array();

    }

    function getReqTCI($filters = [], $withTrashed = false){
        if (empty($filters['date'])) {
            $filters['date'] = date('Y-m-d',strtotime('+1 days',time()));
        } 
        $branchId = get_active_branch('id');
        $baseQuery = $this->db
            ->select([
                'transporter_entry_permit_requests.*',
                'ref_people.name AS customer_name',
                'prv_users.name AS set_name',
                'uploads.description AS no_reference',
                'upload_in.description AS no_reference_in',
                'GROUP_CONCAT(DISTINCT SUBSTRING(upload_multi.description, -4)) AS no_reference_multi',
                'GROUP_CONCAT(DISTINCT SUBSTRING(upload_multi_in.description, -4)) AS no_reference_in_multi',
                'GROUP_CONCAT(DISTINCT transporter_entry_permit_request_uploads.id_upload) AS id_upload_multi',
                'transporter_entry_permits.expired_at',
                'IFNULL(tep_request_tep.slot_created, 0) AS slot_created',
                'GROUP_CONCAT(DISTINCT transporter_entry_permits.tep_code SEPARATOR " ") as tep_code',
            ])
            ->from($this->table)
            ->join('ref_people', 'ref_people.id = transporter_entry_permit_requests.id_customer', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = transporter_entry_permit_requests.set_by','left')
            ->join('transporter_entry_permit_request_uploads','transporter_entry_permit_request_uploads.id_request = '.$this->table.'.id','left')
            ->join('uploads','uploads.id='.$this->table.'.id_upload','left')
            ->join('uploads AS upload_in','upload_in.id = uploads.id_upload','left')
            ->join('uploads AS upload_multi','upload_multi.id=transporter_entry_permit_request_uploads.id_upload','left')
            ->join('uploads AS upload_multi_in','upload_multi_in.id = upload_multi.id_upload','left')
            ->join('transporter_entry_permit_request_tep','transporter_entry_permit_request_tep.id_request = '.$this->table.'.id','left')
            ->join('transporter_entry_permits','transporter_entry_permits.id = transporter_entry_permit_request_tep.id_tep','left')
            ->join('(SELECT id_request, COUNT(id_request) AS slot_created FROM transporter_entry_permit_request_tep
                    GROUP BY id_request) AS tep_request_tep','tep_request_tep.id_request = '.$this->table.'.id','left')
            ->where('transporter_entry_permit_requests.id_branch', $branchId)
            // ->or_group_start()
            //     ->where('transporter_entry_permit_requests.armada','TCI')
            //     ->where('transporter_entry_permit_requests.tep_date IS NULL')
            //     ->where('transporter_entry_permit_requests.id_branch', $branchId)
            // ->group_end()
            ->having("transporter_entry_permit_requests.armada = 'TCI'")
            ->group_by('transporter_entry_permit_requests.id');

        if (key_exists('status', $filters) && !empty($filters['status'])) {
            $baseQuery->where($this->table.'.status', $filters['status']);
        }

        if (key_exists('armada', $filters) && !empty($filters['armada'])) {
            $baseQuery->where($this->table.'.armada', $filters['armada']);
        }

        if (key_exists('request', $filters) && !empty($filters['request'])) {
            $baseQuery->where_in($this->table.'.id', $filters['request']);
        }

        return $baseQuery->get()->result_array();
    }

    function getAllData($filters = [], $withTrashed = false){
        $branchId = get_active_branch('id');
        $baseQuery = $this->db
            ->select([
                'transporter_entry_permit_requests.*',
                'ref_people.name AS customer_name',
                'prv_users.name AS set_name',
                'uploads.description AS no_reference',
                'upload_in.description AS no_reference_in',
                'GROUP_CONCAT(DISTINCT SUBSTRING(upload_multi.description, -4)) AS no_reference_multi',
                'GROUP_CONCAT(DISTINCT SUBSTRING(upload_multi_in.description, -4)) AS no_reference_in_multi',
                'GROUP_CONCAT(DISTINCT transporter_entry_permit_request_uploads.id_upload) AS id_upload_multi',
                'transporter_entry_permits.expired_at',
                'IFNULL(tep_request_tep.slot_created, 0) AS slot_created',
                'GROUP_CONCAT(DISTINCT transporter_entry_permits.tep_code SEPARATOR " ") as tep_code',
            ])
            ->from($this->table)
            ->join('ref_people', 'ref_people.id = transporter_entry_permit_requests.id_customer', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = transporter_entry_permit_requests.set_by','left')
            ->join('transporter_entry_permit_request_uploads','transporter_entry_permit_request_uploads.id_request = '.$this->table.'.id','left')
            ->join('uploads','uploads.id='.$this->table.'.id_upload','left')
            ->join('uploads AS upload_in','upload_in.id = uploads.id_upload','left')
            ->join('uploads AS upload_multi','upload_multi.id=transporter_entry_permit_request_uploads.id_upload','left')
            ->join('uploads AS upload_multi_in','upload_multi_in.id = upload_multi.id_upload','left')
            ->join('transporter_entry_permit_request_tep','transporter_entry_permit_request_tep.id_request = '.$this->table.'.id','left')
            ->join('transporter_entry_permits','transporter_entry_permits.id = transporter_entry_permit_request_tep.id_tep','left')
            ->join('(SELECT id_request, COUNT(id_request) AS slot_created FROM transporter_entry_permit_request_tep
                    GROUP BY id_request) AS tep_request_tep','tep_request_tep.id_request = '.$this->table.'.id','left')
            ->where('transporter_entry_permit_requests.id_branch', $branchId)
            ->group_by('transporter_entry_permit_requests.id');

        // if (key_exists('aju', $filters) && !empty($filters['aju'])) {
        //     $baseQuery->where_in('transporter_entry_permit_request_uploads.id_upload', $filters['aju']);
        // }
        if (key_exists('aju', $filters) && !empty($filters['aju'])) {
            foreach ($filters['aju'] as $key => $id_upload) {
                $baseQuery->or_having("id_upload_multi LIKE '%$id_upload%' ");
            }            
        }
        if (key_exists('date', $filters) && !empty($filters['date'])) {
            $baseQuery->where("DATE(transporter_entry_permit_requests.tep_date)", format_date($filters['date']));
        }

        if (key_exists('q', $filters) && !empty(trim($filters['q']))) {
            $search = trim($filters['q']);
            $baseQuery
                ->group_start()
                ->like('transporter_entry_permit_requests.no_request', $search)
                ->or_like('transporter_entry_permit_requests.category', $search)
                ->or_like('transporter_entry_permit_requests.armada', $search)
                ->or_where("EXISTS (
                    SELECT tep_request_items.id 
                    FROM transporter_entry_permit_request_uploads AS tep_request_items
                    INNER JOIN ref_goods ON ref_goods.id = tep_request_items.id_goods
                    WHERE tep_request_items.id_request = transporter_entry_permit_requests.id 
                        AND ref_goods.name LIKE '%{$search}%'
                )")
                ->group_end();
        }

        return $baseQuery->get()->result_array();
    }
}
