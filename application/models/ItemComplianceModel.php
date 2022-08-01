<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ItemComplianceModel extends MY_Model
{
    protected $table = 'ref_item_compliances';

    /**
     * Get master item compliance base query.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery()
                ->select([
                    'ref_people.name AS customer_name',
                    'IFNULL(COUNT(ref_item_compliance_photos.id), 0) AS total_file',
                ])
                ->join('ref_item_compliance_photos', 'ref_item_compliance_photos.id_item = ref_item_compliances.id', 'left')
                ->join('ref_people', 'ref_people.id = ref_item_compliances.id_customer', 'left')
                ->group_by('ref_item_compliances.id');;
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
            $item = $this->getBaseQuery();
            if (!$withTrashed) {
                $item->where('ref_item_compliances.is_deleted', false);
            }
            return $item->get()->result_array();
        }

        $columnOrder = [
            "ref_item_compliances.id",
            "ref_people.name",
            "ref_item_compliances.item_name",
            "ref_item_compliances.no_hs",
        ];
        $columnSort = $columnOrder[$column];

        $this->db->start_cache();

        $item = $this->getBaseQuery()
            ->group_start()
            ->like('ref_people.name', trim($search))
            ->or_like('ref_item_compliances.no_hs', trim($search))
            ->or_like('ref_item_compliances.item_name', trim($search))
            ->or_like('ref_item_compliances.unit', trim($search))
            ->group_end()
            ->order_by($columnSort, $sort);

        if (!empty($filters)) {
            if (key_exists('customer', $filters) && !empty($filters['customer'])) {
                $item->where_in('ref_item_compliances.id_customer', $filters['customer']);
            }
        }

        if (!$withTrashed) {
            $item->where('ref_item_compliances.is_deleted', false);
        }

        $this->db->stop_cache();

        $itemTotal = $this->db->count_all_results();
        $itemPage = $item->limit($length, $start);
        $itemData = $itemPage->get()->result_array();

        foreach ($itemData as &$row) {
            $row['no'] = ++$start;
        }

        return [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($itemData),
            "recordsFiltered" => $itemTotal,
            "data" => $itemData
        ];
    }

    /**
     * Get item data by its name.
     *
     * @param null $itemName
     * @param int $page
     * @param string $owner
     * @param bool $withTrashed
     * @return mixed
     */
    public function getByItem($itemName = null, $page = null, $owner = '', $withTrashed = false)
    {
        $this->db->start_cache();

        $item = $this->getBaseQuery();

        if (!empty($itemName)) {
            $item->like('ref_item_compliances.item_name', $itemName);
        }

        if (!empty($owner)) {
            $item->where('ref_people.id', $owner);
        }

        if (!$withTrashed) {
            $item->where('ref_item_compliances.is_deleted', false);
        }

        $this->db->stop_cache();

        if (!empty($page) || $page != 0) {
            $totalData = $item->distinct()->count_all_results();
            $dataPerPage = $item->limit(10, 10 * ($page - 1));
            $data = $dataPerPage->order_by('ref_item_compliances.created_at', 'desc')->distinct()->get()->result_array();

            return [
                'results' => $data,
                'total_count' => $totalData
            ];
        }

        $itemData = $item->distinct()->get()->result_array();

        $this->db->flush_cache();

        return $itemData;
    }
}