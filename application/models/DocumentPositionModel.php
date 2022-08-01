<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DocumentPositionModel extends MY_Model
{
    protected $table = 'document_positions';
    protected $tablePosition = 'ref_positions';

    public static $tableDocumentPosition = 'document_positions';

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
                'document_positions.*',
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
    public function deleteByDepartment()
    {
        return $this->db->truncate($this->table);
    }

    public function getDocumentPosition(){
        $query = $this->db
            ->select([
                'document_positions.*',
                'ref_positions.position',
            ])
            ->from($this->table)
            ->join($this->tablePosition,"ref_positions.id = document_positions.id_position")
            ->order_by('id', 'desc');

        return $query->get()->result_array();
    }

}
