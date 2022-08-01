<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ContainerModel extends MY_Model
{
    protected $table = 'ref_containers';

    /**
     * ContainerModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get base query of container master.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return $this->db
            ->select([
                'ref_containers.*',
                'ref_people.name AS shipping_line',
                'ref_people.email AS shipping_line_email'
            ])
            ->from('ref_containers')
            ->join('ref_people', 'ref_containers.id_shipping_line = ref_people.id', 'left');
    }

    /**
     * Get all container with or without deleted records.
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
            $containers = $this->getBaseQuery();
            if (!$withTrashed) {
                $containers->where('ref_containers.is_deleted', false);
            }
            return $containers->get()->result_array();
        }

        $columnOrder = [
            "ref_containers.id",
            "ref_people.name",
            "ref_containers.no_container",
            "ref_containers.type",
            "ref_containers.size",
            "ref_containers.id",
        ];
        $columnSort = $columnOrder[$column];

        $this->db->start_cache();
        $containers = $this->getBaseQuery()
            ->group_start()
            ->like('ref_people.name', $search)
            ->or_like('ref_containers.no_container', $search)
            ->or_like('ref_containers.type', $search)
            ->or_like('ref_containers.size', $search)
            ->group_end()
            ->order_by($columnSort, $sort);

        if (!$withTrashed) {
            $containers->where('ref_containers.is_deleted', false);
        }

        $this->db->stop_cache();

        $containersTotal = $this->db->count_all_results();
        $containerPage = $containers->limit($length, $start);
        $containerData = $containerPage->get()->result_array();

        foreach ($containerData as &$row) {
            $row['no'] = ++$start;
        }

        return [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($containerData),
            "recordsFiltered" => $containersTotal,
            "data" => $containerData
        ];
    }

    /**
     * Get container data by its number.
     *
     * @param null $containerNo
     * @param int $page
     * @param string $owner
     * @param bool $withTrashed
     * @return mixed
     */
    public function getByNo($containerNo = null, $page = null, $owner = '', $withTrashed = false)
    {
        $branchId = get_active_branch_id();
        $personId = UserModel::authenticatedUserData('id_person', '0');

        $this->db->start_cache();

        $containers = $this->getBaseQuery();

        if (!empty($containerNo)) {
            $containers->like('ref_containers.no_container', $containerNo);
        }

        if (!empty($owner)) {
            if (strtolower($owner) == 'tps') {
                $containers
                    ->join('safe_conduct_containers', 'safe_conduct_containers.id_container = ref_containers.id')
                    ->join('safe_conducts', 'safe_conducts.id = safe_conduct_containers.id_safe_conduct')
                    ->join('bookings', 'bookings.id = safe_conducts.id_booking')
                    ->where('bookings.id_branch', $branchId)
                    ->where('safe_conducts.id_source_warehouse', $personId);
            }
        }

        if (!$withTrashed) {
            $containers->where('ref_containers.is_deleted', false);
        }

        $this->db->stop_cache();

        if (!empty($page) || $page != 0) {
            $totalData = $containers->distinct()->count_all_results();
            $dataPerPage = $containers->limit(10, 10 * ($page - 1));
            $data = $dataPerPage->order_by('ref_containers.created_at', 'desc')->distinct()->get()->result_array();

            return [
                'results' => $data,
                'total_count' => $totalData
            ];
        }

        $containerData = $containers->distinct()->get()->result_array();

        $this->db->flush_cache();

        return $containerData;
    }

}