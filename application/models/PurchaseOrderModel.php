<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PurchaseOrderModel extends MY_Model
{
    protected $table = 'requisitions';
    protected $tableOffer = 'purchase_offers';
    protected $tableItem = 'ref_item_categories';

    public static $tableRequisition = 'requisitions';

    /**
     * BranchModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        if ($this->config->item('sso_enable')) {
            $this->table = env('DB_PURCHASE_DATABASE') . '.requisitions';
            $this->tableOffer = env('DB_PURCHASE_DATABASE') . '.purchase_offers';
            PurchaseOrderModel::$tableRequisition = env('DB_PURCHASE_DATABASE') . '.requisitions';
            $this->tableItem = env('DB_PURCHASE_DATABASE') . '.ref_item_categories';
        }
    }

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    public function getHeavyEquipment($search, $page = null, $withTrashed = false)
    {
        $this->db->start_cache();
        $query = $this->db
            ->select([
                $this->table.'.*',
                ])
            ->from($this->table)
            ->join($this->tableItem, $this->tableItem.'.id = '.$this->table.'.id_item_category','left')
            ->where($this->table.'.id_item_category is not null', null)
            // ->where($this->tableOffer.'.status', 'ORDERED')
            // ->where($this->table.'.id_category', 39)//OPS RENT, HEAVY EQUIPMENT
            ->where_in($this->tableItem.'.item_name', ['FORKLIFT','CRANE'])
            ->order_by($this->table.'.id');
        
        $query->group_start();
        if (is_array($search)) {
            $query->where_in($this->table.'.no_requisition', $search);
        } else {
            $query->like($this->table.'.no_requisition', trim($search));
        }
        $query->group_end();

        if (!$withTrashed) {
            $query->where($this->table . '.is_deleted', false);
        }

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

}