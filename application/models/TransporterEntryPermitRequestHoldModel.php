<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class TransporterEntryPermitRequestHoldModel
 */
class TransporterEntryPermitRequestHoldModel extends MY_Model
{
    protected $table = 'transporter_entry_permit_request_holds';

    const STATUS_HOLD = 'HOLD';
    const STATUS_PARTIAL_RELEASE = 'PARTIAL RELEASED';
    const STATUS_RELEASED = 'RELEASED';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        $baseQuery = parent::getBaseQuery()
            ->select([
                'customers.name AS customer_name',
                'prv_users.name AS creator_name',
                '(
                    SELECT GROUP_CONCAT(ref_goods.name SEPARATOR ",") AS goods_name 
                    FROM transporter_entry_permit_request_hold_items
                    INNER JOIN ref_goods ON ref_goods.id = transporter_entry_permit_request_hold_items.id_goods
                    WHERE transporter_entry_permit_request_hold_items.id_tep_hold = transporter_entry_permit_request_holds.id
                ) AS goods_name'
            ])
            ->join('ref_people AS customers', 'customers.id = transporter_entry_permit_request_holds.id_customer', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = transporter_entry_permit_request_holds.created_by', 'left');

        if (!empty($branchId)) {
            $baseQuery->where('transporter_entry_permit_request_holds.id_branch', $branchId);
        }

        return $baseQuery;
    }

    /**
     * Get all tep tracking with or without deleted records.
     *
     * @param array $filters
     * @param bool $withTrashed
     * @return mixed
     */
    public function getAll($filters = [], $withTrashed = false)
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $user = UserModel::authenticatedUserData();
        $userType = $user['user_type'];
        $customerId = $user['id_person'];

        $this->db->start_cache();

        $baseQuery = $this->getBaseQuery($branchId);

        if (!empty($search)) {
            $baseQuery
                ->group_start()
                ->like('transporter_entry_permit_request_holds.hold_type', $search)
                ->or_like('transporter_entry_permit_request_holds.description', $search)
                ->or_like('prv_users.name', $search)
                ->group_end();
        }

        if ($userType == 'EXTERNAL') {
            $baseQuery->where('transporter_entry_permit_request_holds.id_customer', $customerId);
        }

        if (key_exists('hold_type', $filters) && !empty($filters['hold_type'])) {
            $baseQuery->where('transporter_entry_permit_request_holds.hold_type', $filters['hold_type']);
        }

        if (key_exists('status', $filters) && !empty($filters['status'])) {
            $baseQuery->where('transporter_entry_permit_request_holds.hold_status', $filters['status']);
        }

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $baseQuery->where('DATE(transporter_entry_permit_request_holds.created_at)>=', format_date($filters['date_from']));
        }

        if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
            $baseQuery->where('DATE(transporter_entry_permit_request_holds.created_at)<=', format_date($filters['date_to']));
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        if ($column == 'no') $column = $this->table . '.id';
        $page = $baseQuery->order_by($column, $sort)->limit($length, $start);
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
     * Get auto number for request hold.
     *
     * @param string $type
     * @return string
     */
    public function getAutoNumber($type = 'REQ-HOLD')
    {
        $orderData = $this->db->query("
            SELECT CAST(RIGHT(no_hold_reference, 6) AS UNSIGNED) + 1 AS order_number 
            FROM transporter_entry_permit_request_holds 
            WHERE MONTH(created_at) = MONTH(NOW()) 
			  AND YEAR(created_at) = YEAR(NOW())
			  AND SUBSTRING(no_hold_reference, 1, 8) = '$type'
            ORDER BY id DESC LIMIT 1
            ");

        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return $type . '/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }

}
