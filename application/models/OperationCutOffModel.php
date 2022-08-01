<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class OperationCutOffModel
 * @property BranchModel $branch
 */
class OperationCutOffModel extends MY_Model
{
    protected $table = 'ref_operation_cut_offs';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_INACTIVE = 'INACTIVE';
    
    /**
     * OperationCutOffModel constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->addFilteredMap('status', function (CI_DB_query_builder &$baseQuery, &$filters) {
            if (key_exists('status', $filters) && !empty($filters['status'])) {
                $baseQuery->where($this->table . '.status', $filters['status']);
            }
        });
    }
    /**
     * Get master vehicle base query.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery()
            ->select([
                'ref_branches.branch',
                'ref_operation_cut_off_joins.no_group',
            ])
            ->join('ref_branches', 'ref_branches.id = ref_operation_cut_offs.id_branch', 'left')
            ->join('ref_operation_cut_off_joins', 'ref_operation_cut_offs.id = ref_operation_cut_off_joins.id_operation_cut_off', 'left');
    }

    /**
     * @param $date
     * @param $dateTo
     * @param $branches
     * @return array
     */
    public function getRangeDatePerBranch($date, $dateTo, $branches)
    {
        $this->load->model('BranchModel', 'branch');

        if (!is_array($branches)) $branches = [$branches];
        $date = format_date($date, 'Y-m-d');
        $dateTo = format_date($dateTo ?? $date, 'Y-m-d');

        $dateRanges = [];
        foreach ($branches as $branchId) {
            $branch = $this->branch->getById($branchId);
            $shifts = $this->db->from($this->table)->where([
                'id_branch' => $branchId,
                'is_deleted' => false,
                'status' => self::STATUS_ACTIVE
            ])
                ->order_by('shift')
                ->get()
                ->result_array();

            if (!empty($shifts)) {
                $start = $date . ' ' . $shifts[0]['start'];
                $end = $date . ' ' . end($shifts)['end'];

                if ($end < $start) {
                    $interval = date_interval_create_from_date_string('+ 1 day');
                    $dateObj = date_create_from_format('Y-m-d H:i:s', $end)->add($interval);
                    $end = $dateObj->format('Y-m-d H:i:s');
                }

                $dateRanges[$branchId] = [
                    'id_branch' => $branchId,
                    'branch' => $branch['branch'],
                    'start' => $start,
                    'end' => $end
                ];

                // replaced with data to (by calculating the range and add to end date)
                $diff = difference_date($date, $dateTo);
                $end = (new \Carbon\Carbon($dateRanges[$branchId]['end']))->addDays($diff)->format('Y-m-d H:i:s');
                $dateRanges[$branchId]['end'] = $end;
            } else {
                $dateRanges[$branchId] = [
                    'id_branch' => $branchId,
                    'branch' => $branch['branch'],
                    'start' => null,
                    'end' => null
                ];
            }
        }
        return $dateRanges;
    }

    /**
     * Get last shift of the branch.
     *
     * @param $branchId
     * @return array|null
     */
    public function getLastShiftBranch($branchId)
    {
        $data = $this->db->from($this->table)
            ->where('id_branch', $branchId)
            ->where('is_deleted', false)
            ->order_by('shift', 'desc');

        return $data->get()->row_array();

    }

    /**
     * Get single model data by id with or without deleted record.
     *
     * @param $cutOffId
     * @param bool $withTrashed
     * @return mixed
     */
    public function getWithoutThisId($cutOffId, $withTrashed = false)
    {
        $baseQuery = $this->getBaseQuery();

        if(is_array($cutOffId)) {
            $baseQuery->where_not_in($this->table . '.' . $this->id, $cutOffId);
        } else {
            $baseQuery->where($this->table . '.' . $this->id . '!=', $cutOffId);
        }

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get master vehicle base query.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    public function getAllByGrouping($filters = [])
    {
        $baseQuery = parent::getBaseQuery()
            ->select([
                'ref_branches.branch',
                'ref_operation_cut_off_joins.no_group',
                "GROUP_CONCAT(DISTINCT ref_operation_cut_off_joins.id_branch) AS id_branch_group",
            ])
            ->join('ref_branches', 'ref_branches.id = ref_operation_cut_offs.id_branch', 'left')
            ->join('(SELECT
                    ref_operation_cut_off_joins.*, ref_branches.id AS id_branch FROM ref_operation_cut_off_joins
                    JOIN ref_operation_cut_offs ON ref_operation_cut_offs.id = ref_operation_cut_off_joins.id_operation_cut_off
                    JOIN ref_branches ON ref_branches.id = ref_operation_cut_offs.id_branch
                )AS ref_operation_cut_off_joins', 'ref_operation_cut_offs.id = ref_operation_cut_off_joins.id_operation_cut_off', 'left')
            ->group_by('ref_operation_cut_off_joins.no_group,ref_operation_cut_offs.shift');

        if (key_exists('status', $filters) && !empty($filters['status'])) {
            $baseQuery->where('ref_operation_cut_offs.status', $filters['status']);
        }
        if (key_exists('is_send', $filters) && !empty($filters['is_send'])) {
            $baseQuery->where('ref_operation_cut_offs.is_send', $filters['is_send']);
        }

        return $baseQuery->get()->result_array();
    }
}