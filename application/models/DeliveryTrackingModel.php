<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DeliveryTrackingModel extends MY_Model
{
    protected $table = 'delivery_trackings';

    const STATUS_ACTIVE = 'ACTIVE';
    const STATUS_DELIVERED = 'DELIVERED';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        return parent::getBaseQuery($branchId)
            ->select([
                'ref_people.name AS customer_name',
                'COUNT(delivery_tracking_details.id) AS total_delivery_state',
                'ref_employees.name AS employee_name',
                'ref_employees.contact_mobile',
                'ref_department_contact_groups.contact_group'
            ])
            ->join('ref_people', 'ref_people.id = delivery_trackings.id_customer')
            ->join('delivery_tracking_details', 'delivery_tracking_details.id_delivery_tracking = delivery_trackings.id', 'left')
            ->join(env('DB_HR_DATABASE') . '.ref_department_contact_groups', 'ref_department_contact_groups.id = delivery_trackings.id_department_contact_group', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = delivery_trackings.id_user', 'left')
            ->join(EmployeeModel::$tableEmployee, 'ref_employees.id_user = prv_users.id', 'left')
            ->group_by('delivery_trackings.id, ref_employees.id');
    }

    /**
     * Generate auto number.
     *
     * @return string
     */
    public function getAutoNumber()
    {
        $orderData = $this->db->query("
            SELECT CAST(RIGHT(no_delivery_tracking, 6) AS UNSIGNED) + 1 AS order_number 
            FROM delivery_trackings 
            WHERE MONTH(created_at) = MONTH(NOW()) 
              AND YEAR(created_at) = YEAR(NOW())
            ORDER BY CAST(RIGHT(no_delivery_tracking, 6) AS UNSIGNED) DESC LIMIT 1
        ");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return 'DT/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }

    /**
     * Get all delivery with or without deleted records.
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

        $this->db->start_cache();

        $baseQuery = $this->getBaseQuery($branchId)
            ->group_start()
            ->like('delivery_trackings.no_delivery_tracking', $search)
            ->or_like('delivery_trackings.description', $search)
            ->or_like('ref_people.name', $search)
            ->or_like('ref_employees.name', $search)
            ->group_end();

        if (!$withTrashed) {
            $baseQuery->where($this->table . '.is_deleted', false);
        }

        if (key_exists('user', $filters) && !empty($filters['user'])) {
            $baseQuery->where('delivery_trackings.id_user', $filters['user']);
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->where('delivery_trackings.id_customer', $filters['customer']);
        }

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $baseQuery->where('DATE(delivery_trackings.created_at)>=', format_date($filters['date_from']));
        }

        if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
            $baseQuery->where('DATE(delivery_trackings.created_at)<=', format_date($filters['date_to']));
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $baseQuery->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        $total = $this->db->count_all_results();
        if ($column == 'no') $column = 'delivery_trackings.id';
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
     * Get report item.
     *
     * @param $filters
     * @return array
     */
    public function getReportItem($filters)
    {
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $baseQuery = $this->db
            ->select([
                'delivery_trackings.no_delivery_tracking',
                'work_orders.completed_at AS load_date',
                'safe_conducts.no_safe_conduct',
                'safe_conducts.no_police',
                'safe_conducts.driver',
                'safe_conducts.expedition_type',
                'invoice_documents.no_invoice',
                'booking_in.no_reference AS no_reference_inbound',
                'bookings.no_reference AS no_reference_outbound',
                'ref_goods.whey_number',
                'ref_goods.name AS goods_name',
                'delivery_tracking_goods.quantity',
                'delivery_tracking_details.arrival_date',
                'delivery_tracking_details.unload_date',
                'delivery_tracking_details.unload_location',
                'delivery_tracking_goods.description',
            ])
            ->from($this->table)
            ->join('delivery_tracking_details', 'delivery_tracking_details.id_delivery_tracking = delivery_trackings.id', 'left')
            ->join('delivery_tracking_goods', 'delivery_tracking_goods.id_delivery_tracking_detail = delivery_tracking_details.id', 'left')
            ->join('safe_conduct_goods', 'safe_conduct_goods.id = delivery_tracking_goods.id_safe_conduct_goods', 'left')
            ->join('ref_goods', 'ref_goods.id = safe_conduct_goods.id_goods', 'left')
            ->join('safe_conducts', 'safe_conducts.id = safe_conduct_goods.id_safe_conduct', 'left')
            ->join('work_orders', 'work_orders.id_safe_conduct = safe_conducts.id', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling', 'left')
            ->join('bookings', 'bookings.id = safe_conducts.id_booking', 'left')
            ->join('bookings AS booking_in', 'booking_in.id = IFNULL(bookings.id_booking, bookings.id)', 'left')
            ->join("(
                SELECT 
                    upload_documents.id_upload,
                    MAX(upload_documents.no_document) as no_invoice
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'Invoice'
                GROUP BY id_upload              
            ) AS invoice_documents", 'invoice_documents.id_upload = bookings.id_upload', 'left')
            ->where([
                'id_handling_type' => $handlingTypeIdOutbound
            ]);

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->where('delivery_trackings.id_customer', $filters['customer']);
        }

        if (key_exists('delivery_tracking', $filters) && !empty($filters['delivery_tracking'])) {
            $baseQuery->where('delivery_trackings.id', $filters['delivery_tracking']);
        }

        if (key_exists('delivery_tracking_detail', $filters) && !empty($filters['delivery_tracking_detail'])) {
            $baseQuery->where('delivery_tracking_details.id', $filters['delivery_tracking_detail']);
        }

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $baseQuery->where('DATE(delivery_trackings.created_at)>=', format_date($filters['date_from']));
        }

        if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
            $baseQuery->where('DATE(delivery_trackings.created_at)<=', format_date($filters['date_to']));
        }

        return $baseQuery->get()->result_array();
    }
}
