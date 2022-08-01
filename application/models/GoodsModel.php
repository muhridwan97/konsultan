<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class GoodsModel extends MY_Model
{
    protected $table = 'ref_goods';

    /**
     * GoodsModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get master goods base query.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return $this->db->select([
            'ref_goods.*',
            'ref_people.name AS customer_name',
            'ref_assemblies.no_assembly AS no_assembly',
        ])
            ->from($this->table)
            ->join('ref_people', 'ref_people.id = ref_goods.id_customer', 'left')
            ->join('ref_assemblies', 'ref_assemblies.id = ref_goods.id_assembly', 'left');
    }

    /**
     * Get all goods with or without deleted records.
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

        if ($start < 0) {
            $goods = $this->getBaseQuery();
            if (!$withTrashed) {
                $goods->where('ref_goods.is_deleted', false);
            }
            return $goods->get()->result_array();
        }

        $columnOrder = [
            "ref_goods.id",
            "ref_people.name",
            "ref_goods.no_goods",
            "ref_goods.no_hs",
            "ref_goods.whey_number",
            "ref_goods.name",
            "ref_goods.id",
            "ref_goods.id",
        ];
        $columnSort = $columnOrder[$column];

        $this->db->start_cache();

        $goods = $this->getBaseQuery()
            ->group_start()
            ->like('ref_people.name', trim($search))
            ->or_like('ref_goods.no_goods', trim($search))
            ->or_like('ref_goods.no_hs', trim($search))
            ->or_like('ref_goods.name', trim($search))
            ->group_end()
            ->order_by($columnSort, $sort);

        if (!$withTrashed) {
            $goods->where('ref_goods.is_deleted', false);
        }

        $this->db->stop_cache();

        $goodsTotal = $this->db->count_all_results();
        $goodsPage = $goods->limit($length, $start);
        $goodsData = $goodsPage->get()->result_array();

        foreach ($goodsData as &$row) {
            $row['no'] = ++$start;
        }

        return [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($goodsData),
            "recordsFiltered" => $goodsTotal,
            "data" => $goodsData
        ];
    }

    /**
     * Check if goods name is unique per customer.
     *
     * @param $customerId
     * @param $noGoods
     * @param $goodsId
     * @return bool
     */
    public function isUniqueGoods($customerId, $noGoods, $goodsId)
    {
        $user = $this->db->get_where($this->table, [
            'id_customer' => $customerId,
            'no_goods' => $noGoods,
            'id != ' => $goodsId
        ]);

        if ($user->num_rows() > 0) {
            return false;
        }
        return true;
    }

    /**
     * Get goods data by name.
     *
     * @param null $name
     * @param $page
     * @param bool $withTrashed
     * @return mixed
     */
    public function getGoodsByName($name = null, $page, $withTrashed = false)
    {
        $this->db->start_cache();

        $goods = $this->getBaseQuery();

        if (!empty($name)) {
            $goods->group_start();
            $goods->like('ref_goods.no_goods', $name);
            $goods->or_like('ref_goods.name', $name);
            $goods->group_end();
        }

        if (!$withTrashed) {
            $goods->where('ref_goods.is_deleted', false);
        }

        $this->db->stop_cache();

        if (!empty($page) || $page != 0) {
            $totalData = $goods->count_all_results();
            $dataPerPage = $goods->limit(10, 10 * ($page - 1));
            $data = $dataPerPage->order_by('ref_goods.created_at', 'desc')->get()->result_array();

            return [
                'results' => $data,
                'total_count' => $totalData
            ];
        }

        return $goods->get()->result_array();
    }

}