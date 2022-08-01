<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ComponentPriceModel extends CI_Model
{
    private $table = 'ref_component_prices';

    const STATUS_PENDING = 'PENDING';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';

    /**
     * ComponentPriceModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active record query builder for all related component price data selection.
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    public function getBaseComponentPriceQuery($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $componentPrices = $this->db
            ->select([
                'ref_component_prices.*',
                'ref_branches.branch AS branch_name',
                'ref_people.name AS customer_name',
                'ref_handling_types.handling_type AS handling_type_name',
                'ref_components.handling_component AS component_name',
                'ref_units.unit',
                'validator.name AS validator_name',
                'creator.name AS creator_name'
            ])
            ->from('ref_component_prices')
            ->join('ref_branches', 'ref_component_prices.id_branch = ref_branches.id', 'left')
            ->join('ref_people', 'ref_component_prices.id_customer = ref_people.id', 'left')
            ->join('ref_handling_types', 'ref_component_prices.id_handling_type = ref_handling_types.id', 'left')
            ->join('ref_components', 'ref_component_prices.id_component = ref_components.id', 'left')
            ->join('ref_units', 'ref_component_prices.goods_unit = ref_units.id', 'left')
            ->join(UserModel::$tableUser . ' AS validator', 'validator.id = ref_component_prices.validated_by', 'left')
            ->join(UserModel::$tableUser . ' AS creator', 'creator.id = ref_component_prices.created_by', 'left');

        if (!empty($branchId)) {
            $componentPrices->where('ref_component_prices.id_branch', $branchId);
        }
        return $componentPrices;
    }

    /**
     * Get all component prices with or without deleted records.
     * @param null $customerId
     * @param int $start
     * @param int $length
     * @param string $search
     * @param int $column
     * @param string $sort
     * @param bool $withTrashed
     * @return array
     */
    public function getAllComponentPrices($customerId = null, $start = -1, $length = 10, $search = '', $column = 0, $sort = 'DESC', $withTrashed = false)
    {
        $columnOrder = [
            "ref_component_prices.id",
            "customer_name",
            "price_type",
            "price_subtype",
            "handling_type_name",
            "component_name",
            "rule",
            "price",
            "status",
            "invoices.id",
        ];
        $columnSort = $columnOrder[$column];

        $branchId = get_active_branch('id');

        $this->db->start_cache();

        $search = trim($search);
        $componentPrices = $this->getBaseComponentPriceQuery($branchId)
            ->group_start()
            ->like('ref_people.name', $search)
            ->or_like('ref_component_prices.price_type', $search)
            ->or_like('ref_component_prices.price_subtype', $search)
            ->or_like('ref_handling_types.handling_type', $search)
            ->or_like('ref_components.handling_component', $search)
            ->or_like('ref_component_prices.rule', $search)
            ->or_like('ref_component_prices.price', $search)
            ->or_like('ref_component_prices.description', $search)
            ->or_like('ref_component_prices.status', $search)
            ->group_end();

        if (!$withTrashed) {
            $componentPrices->where('ref_component_prices.is_deleted', false);
        }

        if (!empty($customerId)) {
            $componentPrices->where('ref_component_prices.id_customer', $customerId);
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $componentPrices->order_by('id', 'desc')->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $componentPriceTotal = $this->db->count_all_results();
        $componentPrices->order_by($columnSort, $sort);
        $componentPriceData = $componentPrices->limit($length, $start)->get()->result_array();

        $this->db->flush_cache();

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($componentPriceData),
            "recordsFiltered" => $componentPriceTotal,
            "data" => $componentPriceData
        ];

        return $pageData;
    }

    /**
     * Get single component price data by id with or without deleted record.
     * @param integer $id
     * @param bool $withTrashed
     * @return array
     */
    public function getComponentPriceById($id, $withTrashed = false)
    {
        $componentPrices = $this->getBaseComponentPriceQuery()->where('ref_component_prices.id', $id);

        if (!$withTrashed) {
            $componentPrices->where('ref_component_prices.is_deleted', false);
        }

        return $componentPrices->get()->row_array();
    }

    /**
     * Get component prices data by customer with or without deleted record.
     * @param integer $idCustomer
     * @param bool $withTrashed
     * @return array
     */
    public function getComponentPricesByCustomer($idCustomer, $withTrashed = false)
    {
        $componentPrices = $this->db
            ->select('ref_component_prices.*')
            ->from($this->table)
            ->join('ref_people', 'ref_component_prices.id_customer = ref_people.id')
            ->where('ref_component_prices.id_customer', $idCustomer);

        if (!$withTrashed) {
            $componentPrices->where('ref_component_prices.is_deleted', false);
        }

        return $componentPrices->get()->result_array();
    }

    /**
     * Get price by branch and handling.
     * @param $branchId
     * @param $handlingId
     * @param $withTrashed
     * @return mixed
     */
    public function getComponentPriceByBranchHandling($branchId, $handlingId, $withTrashed = false)
    {
        $componentPrices = $this->getBaseComponentPriceQuery()
            ->where([
                'ref_component_prices.id_branch' => $branchId,
                'ref_component_prices.id_handling_type' => $handlingId,
            ]);

        if (!$withTrashed) {
            $componentPrices->where('ref_component_prices.is_deleted', false);
        }

        return $componentPrices->get()->row_array();
    }

    /**
     * Create new component price.
     * @param $data
     * @return bool
     */
    public function createComponentPrice($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update component price.
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateComponentPrice($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete component price data.
     * @param integer $id
     * @param bool $softDelete
     * @return bool
     */
    public function deleteComponentPrice($id, $softDelete = true)
    {
        if ($softDelete) {
            return $this->db->update($this->table, [
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => UserModel::authenticatedUserData('id')
            ], ['id' => $id]);
        }
        return $this->db->delete($this->table, ['id' => $id]);
    }

}