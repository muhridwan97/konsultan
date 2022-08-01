<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CorePositionModel extends MY_Model
{
    protected $table = 'core_positions';
    protected $tablePosition = 'ref_positions';

    public static $tableCorePosition = 'core_positions';

    /**
     * UserModel constructor.
     */
    public function __construct()
    {
        if ($this->config->item('sso_enable')) {
            $hrDB = env('DB_HR_DATABASE');
            $this->tablePosition = $hrDB . '.ref_positions';
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
                'core_positions.*',
            ])
            ->from($this->table)
            ->order_by('id', 'desc');

        return $baseQuery;
    }

    /**
     * Delete model data.
     *
     * @param int|array $id
     * @param bool $softDelete
     * @return bool
     */
    public function deleteByDepartment($id)
    {
        $this->db->where('id_department',$id);
        return $this->db->delete($this->table);
    }

    public function getCorePosition(){
        $query = $this->db
            ->select([
                'core_positions.*',
                'ref_positions.position',
            ])
            ->from($this->table)
            ->join($this->tablePosition,"ref_positions.id = core_positions.id_position")
            ->order_by('id', 'desc');

        return $query->get()->result_array();
    }

}
