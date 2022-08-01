<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PermissionModel extends MY_Model
{
    protected $table = 'prv_permissions';

    /**
     * PermissionModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get all permissions with or without deleted records.
     *
     * @param $filters
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

        $this->db->start_cache();

        $permissions = $this->db->from($this->table);

        if (!empty($filters)) {
            if (!empty($search)) {
                $permissions->group_start();
                $fields = $this->db->list_fields($this->table);
                foreach ($fields as $field) {
                    $permissions->or_like($this->table . '.' . $field, trim($filters['search']));
                }
                $permissions->group_end();
            }
        }

        if (!$withTrashed) {
            $permissions->where(['is_deleted' => false]);
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $permissions
                ->order_by('module', 'asc')
                ->order_by('submodule', 'asc')
                ->get()
                ->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $columnOrder = ["id", "module", "submodule", "permission", "created_at", "id"];
        $columnSort = $columnOrder[$column];

        $total = $this->db->count_all_results();
        $page = $permissions->order_by($columnSort, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        foreach ($data as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($data),
            "recordsFiltered" => $total,
            "data" => $data
        ];

        $this->db->flush_cache();

        return $pageData;
    }

    /**
     * Get permissions by specific role id.
     *
     * @param $roleId
     * @return array
     */
    public function getByRole($roleId)
    {
        $permissions = $this->db->select('prv_permissions.*')
            ->from('prv_permissions')
            ->join('prv_role_permissions', 'prv_role_permissions.id_permission = prv_permissions.id')
            ->where([
                'prv_permissions.is_deleted' => false,
                'prv_role_permissions.id_role' => $roleId
            ])
            ->get();
        return $permissions->result_array();
    }

}