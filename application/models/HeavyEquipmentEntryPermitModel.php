<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HeavyEquipmentEntryPermitModel extends MY_Model
{
    protected $table = 'heavy_equipment_entry_permits';
    protected $tableRequisition = 'requisitions';
    protected $tableItemCategory = 'ref_item_categories';

    const HEEP_CODE = 'HP';

    public function __construct()
    {
        parent::__construct();

        if ($this->config->item('sso_enable')) {
            $purchaseDB = env('DB_PURCHASE_DATABASE');
            $this->tableRequisition = $purchaseDB . '.requisitions';
            $this->tableItemCategory = $purchaseDB . '.ref_item_categories';
        }
    }

    /**
     * Get active record query builder for all related warehouse data selection.
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        if(empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $baseQuery = parent::getBaseQuery()
            ->select([
                'heavy_equipment_entry_permits.no_heep',
                'heavy_equipment_entry_permits.heep_code',
                'heavy_equipment_entry_permits.checked_in_at',
                'heavy_equipment_entry_permits.checked_out_at',
                'ref_people.id AS id_customer',               
                'ref_people.name AS customer_name',
                'checker_outs.name AS checker_out_name', 
                'creator.name AS creator_name', 
                'checkers.name AS checker_name',
                'requisition.no_requisition'
                
            ])
            ->join('ref_people', 'ref_people.id = heavy_equipment_entry_permits.id_customer', 'left')
            ->join(UserModel::$tableUser . ' AS checkers', 'checkers.id = heavy_equipment_entry_permits.checked_in_by', 'left')
            ->join(UserModel::$tableUser . ' AS checker_outs', 'checker_outs.id = heavy_equipment_entry_permits.checked_out_by', 'left')
            ->join(UserModel::$tableUser . ' AS creator', 'creator.id = heavy_equipment_entry_permits.created_by', 'left')
            ->join('(SELECT id,heep_code FROM heavy_equipment_entry_permits) AS heep_reference', 'heep_reference.id = heavy_equipment_entry_permits.id_heep_reference', 'left')
            ->join(PurchaseOrderModel::$tableRequisition . ' AS requisition', 'requisition.id = heavy_equipment_entry_permits.id_requisition', 'left')
            ->where_in('heavy_equipment_entry_permits.id_branch', $branchId);

        return $baseQuery;
    }

    /**
     * Get all transporter entry data.
     *
     * @param array $filters
     * @param bool $withTrashed
     * @return mixed
     */
    public function getAll($filters = [], $withTrashed = false)
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branch = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $columns = [
            "heavy_equipment_entry_permits.id",
            "ref_people.name",
            "heavy_equipment_entry_permits.heep_code",
            "heavy_equipment_entry_permits.no_heep",
            "heavy_equipment_entry_permits.checked_in_at",
            "heavy_equipment_entry_permits.checked_in_by",
            "heavy_equipment_entry_permits.checked_out_at",
            "heavy_equipment_entry_permits.checked_out_by",
        ];
        $columnSort = $columns[$column];

        $this->db->start_cache();

        $baseQuery = $this->getBaseQuery($branch);

        if (!empty($search)) {
            $baseQuery->group_start();
            foreach ($columns as $field) {
                $baseQuery->or_like($field, trim($search));
            }
            $baseQuery->or_like('ref_people.name', trim($search));
            $baseQuery->group_end();
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        $page = $baseQuery->order_by($columnSort, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        $this->db->flush_cache();

        foreach ($data as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($data),
            "recordsFiltered" => $total,
            "data" => $data
        ];

        return $pageData;
    }

    /**
     * Generate unique code number
     * @param int $maxTrial
     * @return string
     */
    public function generateCode($maxTrial = 10)
    {
        $this->load->helper('string');

        $code = self::HEEP_CODE . '/' . strtoupper(random_string('alnum', 6));

        if (!empty($this->getBy(['heavy_equipment_entry_permits.heep_code' => $code, 'heavy_equipment_entry_permits.expired_at>=NOW()' => null]))) {
            if ($maxTrial > 0) {
                return $this->generateCode($maxTrial - 1);
            }
        }

        return $code;
    }

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    public function getHEEP($search, $page = null, $withTrashed = false)
    {
        $this->db->start_cache();
        $query = $this->getBaseQuery();
        
        $query->group_start();
        if (is_array($search)) {
            $query->where_in('heavy_equipment_entry_permits.heep_code', $search);
        } else {
            $query->like('heavy_equipment_entry_permits.heep_code', trim($search))
                ->or_like('heavy_equipment_entry_permits.no_heep', trim($search));
        }
        $query->group_end();

        $this->db->stop_cache();

        if (!empty($page) || $page != 0) {
            $queryTotal = $query->count_all_results();
            $queryPage = $query->limit(10, 10 * ($page - 1));
            $queryData = $queryPage->get()->result_array();

            return [
                'results' => $queryData,
                'total_count' => $queryTotal
            ];
        }

        $queryData = $query->get()->result_array();

        $this->db->flush_cache();

        return $queryData;
    }

     /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    public function getHEEPIn()
    {
        $this->db->start_cache();
        $query = $this->getBaseQuery()
                ->where('heavy_equipment_entry_permits.checked_in_at is not null', null)
                ->where('heavy_equipment_entry_permits.checked_out_at is null', null);

        $this->db->stop_cache();


        $queryData = $query->get()->result_array();

        $this->db->flush_cache();

        return $queryData;
    }

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    public function getHEEPAll()
    {
        $this->db->start_cache();
        $query = parent::getBaseQuery()
                ->select([
                    'heavy_equipment_entry_permits.heep_code AS name',
                    'requisition.no_requisition',
                    'requisition.request_title'
                    ])
                ->join(PurchaseOrderModel::$tableRequisition . ' AS requisition', 'requisition.id = heavy_equipment_entry_permits.id_requisition', 'left');

        $this->db->stop_cache();
        $queryData = $query->get()->result_array();
        $this->db->flush_cache();

        return $queryData;
    }

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    public function getHEEPByOperationalProduction($filters = [])
    {
        $this->db->start_cache();
        $branchId = null;
        if (!empty($filters['branch']) && !is_null($filters['branch'])) {
            $branchId = $filters['branch'];
        }
        $query = $this->getBaseQuery($branchId)
                ->select([
                    'ref_item_categories.item_name'
                ])
                ->join($this->tableItemCategory." AS ref_item_categories","ref_item_categories.id = requisition.id_item_category","left");

        if (!empty($filters['checked_in_from']) && !is_null($filters['checked_in_from'])) {
            $query->where('heavy_equipment_entry_permits.checked_in_at >=',$filters['checked_in_from']);
        }
        if (!empty($filters['checked_in_to']) && !is_null($filters['checked_in_to'])) {
            $query->where('heavy_equipment_entry_permits.checked_in_at <=',$filters['checked_in_to']);
        }
        $this->db->stop_cache();


        $queryData = $query->get()->result_array();

        $this->db->flush_cache();

        return $queryData;
    }
    
}