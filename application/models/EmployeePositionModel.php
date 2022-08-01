<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class EmployeePositionModel extends MY_Model
{
    protected $table = 'ref_positions';

    public static $tablePosition = 'ref_positions';

    /**
     * UserModel constructor.
     */
    public function __construct()
    {
        if ($this->config->item('sso_enable')) {
            $hrDB = env('DB_HR_DATABASE');
            $this->table = $hrDB . '.ref_positions';
            self::$tablePosition = $this->table;
        }
    }

    /**
     * Get base query of table.
     *
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = NULL)
    {
        $baseQuery = $this->db
            ->select([
                'ref_positions.*',
            ])
            ->from($this->table)
            ->order_by('id', 'desc');

        return $baseQuery;
    }

    /**
     * Get person data by name.
     *
     * @param null $name
     * @param null $type
     * @param null $page
     * @param bool $withTrashed
     * @return mixed
     */
    public function getPosition($name, $page = null, $withTrashed = false)
    {
        $this->db->start_cache();

        $position = $this->getBaseQuery();

        if (is_array($name)) {
            $position->where_in('ref_positions.position', $name);
        } else {
            $position->like('ref_positions.position', trim($name));
        }

        if (!$withTrashed) {
            $position->where('ref_positions.is_deleted', false);
        }

        $this->db->stop_cache();

        if (!empty($page) || $page != 0) {
            $positionTotal = $position->count_all_results();
            $positionPage = $position->limit(10, 10 * ($page - 1));
            $positionData = $positionPage->get()->result_array();

            return [
                'results' => $positionData,
                'total_count' => $positionTotal
            ];
        }

        $positionData = $position->get()->result_array();

        $this->db->flush_cache();

        return $positionData;
    }
}
