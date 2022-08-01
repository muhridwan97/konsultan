<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class NewsModel extends MY_Model
{
    protected $table = 'news';

    const TYPE_PUBLIC = 'PUBLIC';
    const TYPE_EXTERNAL = 'EXTERNAL';
    const TYPE_INTERNAL = 'INTERNAL';
    /**
     * Get active record query builder for all related position data selection.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        $news = $this->db->select([
            'news.*',
            'prv_users.username AS author_name',
            'prv_users.email AS author_email',
        ])
            ->from($this->table)
            ->join(UserModel::$tableUser, 'prv_users.id = news.created_by');

        return $news;
    }

    /**
     * Get all news with or without deleted records.
     *
     * @param array $filters
     * @param bool $withTrashed
     * @return mixed
     */
    public function getAll($filters = [], $withTrashed = false)
    {
        $getAllData = empty($filters);
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;

        // alias column name by index for sorting data table library
        $columnOrder = [
            0 => "news.id",
            1 => "news.title",
            2 => "news.content",
            3 => "news.type",
            4 => "news.is_sticky",
            5 => "news.id",
        ];
        $columnSort = $columnOrder[$column];

        $this->db->start_cache();
        $news = $this->getBaseQuery()
            ->group_start()
            ->like('news.title', $search)
            ->or_like('news.content', $search)
            ->or_like('news.type', $search)
            ->or_like('news.is_sticky', $search)
            ->group_end();

        if (!$withTrashed) {
            $news->where($this->table . '.is_deleted', false);
        }
        $this->db->stop_cache();

        if ($getAllData) {
            $allData = $news->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        $page = $news->order_by($columnSort, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        foreach ($data as &$row) {
            $row['no'] = ++$start;
            $row['content'] = strip_tags($row['content']);
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
     * Get news data by conditions.
     *
     * @param $type
     * @param null $limit
     * @param bool $withTrashed
     * @return mixed
     */
    public function getByType($type, $limit = null, $withTrashed = false)
    {
        $news = $this->getBaseQuery()->order_by('news.id', "DESC");

        if (is_array($type)) {
            $news->where_in('type', $type);
        } else {
            $news->where('type', $type);
        }

        if (!empty($limit)) {
            $news->limit($limit);
        }

        if (!$withTrashed) {
            $news->where($this->table . '.is_deleted', false);
        }

        return $news->get()->result_array();
    }

}