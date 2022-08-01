<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class RawContactModel extends MY_Model
{
    protected $table = 'raw_contacts';

    /**
     * Get all contact with or without deleted records.
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
            0 => "raw_contacts.id",
            1 => "raw_contacts.company",
            2 => "raw_contacts.pic",
            3 => "raw_contacts.address",
            4 => "raw_contacts.contact",
            5 => "raw_contacts.email",
            6 => "raw_contacts.id",
        ];
        $columnSort = $columnOrder[$column];

        $this->db->start_cache();
        $contacts = $this->db->from('raw_contacts')
            ->group_start()
            ->like('raw_contacts.company', $search)
            ->or_like('raw_contacts.pic', $search)
            ->or_like('raw_contacts.address', $search)
            ->or_like('raw_contacts.contact', $search)
            ->or_like('raw_contacts.email', $search)
            ->group_end();

        $this->db->stop_cache();

        if ($getAllData) {
            $allData = $contacts->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        $page = $contacts->order_by($columnSort, $sort)->limit($length, $start);
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

}