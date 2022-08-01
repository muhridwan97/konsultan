<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TransporterEntryPermitRequestUploadModel extends MY_Model
{
    protected $table = 'transporter_entry_permit_request_uploads';

    /**
     * TEP Customers Model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active record query builder for all related tep data.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = NULL)
    {
        return parent::getBaseQuery()
            ->select([
                'uploads.description AS no_reference_upload',
                'ref_goods.name AS goods_name',
                'ref_units.unit',
                'ref_booking_types.booking_type',
                'transporter_entry_permit_requests.no_request',
            ])
            ->join('uploads', 'uploads.id = transporter_entry_permit_request_uploads.id_upload', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_goods', 'ref_goods.id = transporter_entry_permit_request_uploads.id_goods', 'left')
            ->join('ref_units', 'ref_units.id = transporter_entry_permit_request_uploads.id_unit', 'left')
            ->join('transporter_entry_permit_requests', 'transporter_entry_permit_requests.id = transporter_entry_permit_request_uploads.id_request', 'left');

    }

    /**
     * Get goods data by tep request.
     * @param $requestId
     * @return mixed
     */
    public function getByRequestId($requestId)
    {
        $files = $this->getBaseQuery()
            ->where_in('transporter_entry_permit_request_uploads.id_request', $requestId);

        return $files->get()->result_array();
    }

    /**
     * Get outstanding priority.
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getOutstandingItemPriority($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $this->db->start_cache();

        $baseQuery = $this->db
            ->select([
                'transporter_entry_permit_request_uploads.id_upload',
                'uploads.description AS upload_description',
                'transporter_entry_permit_request_uploads.id_goods',
                'transporter_entry_permit_request_uploads.id_unit',
                'ref_people.name AS customer_name',
                'ref_goods.name AS goods_name',
                'ref_goods.no_goods',
                'ref_units.unit',
                'transporter_entry_permit_request_uploads.ex_no_container',
                'GROUP_CONCAT(DISTINCT transporter_entry_permit_request_uploads.hold_status) AS hold_statuses',
                'GROUP_CONCAT(DISTINCT transporter_entry_permit_request_uploads.priority) AS priorities', // should be atomic value, if not then there is something wrong in process
                'GROUP_CONCAT(DISTINCT transporter_entry_permit_request_uploads.unload_location) AS unload_locations', // should be atomic value
                'GROUP_CONCAT(DISTINCT transporter_entry_permit_requests.no_request) AS no_requests',
                'GROUP_CONCAT(DISTINCT transporter_entry_permit_request_uploads.id) AS id_tep_request_uploads'
            ])
            ->from('transporter_entry_permit_request_uploads')
            ->join('transporter_entry_permit_requests', 'transporter_entry_permit_requests.id = transporter_entry_permit_request_uploads.id_request')
            ->join('ref_goods', 'ref_goods.id = transporter_entry_permit_request_uploads.id_goods')
            ->join('ref_units', 'ref_units.id = transporter_entry_permit_request_uploads.id_unit')
            ->join('uploads', 'uploads.id = transporter_entry_permit_request_uploads.id_upload')
            ->join('ref_people', 'ref_people.id = uploads.id_person')
            ->join('bookings', 'bookings.id_upload = uploads.id', 'left')
            ->where([
                'bookings.status !=' => 'COMPLETED',
            ])
            ->group_by('transporter_entry_permit_request_uploads.id_upload, id_goods, id_unit, ex_no_container');

        if (!empty($branchId)) {
            $baseQuery->where('transporter_entry_permit_requests.id_branch', $branchId);
        }

        if (!empty($search)) {
            $baseQuery
                ->group_start()
                ->like('ref_goods.name', $search)
                ->or_like('ref_units.unit', $search)
                ->or_like('transporter_entry_permit_requests.no_request', $search)
                ->or_like('transporter_entry_permit_request_uploads.ex_no_container', $search)
                ->or_like('transporter_entry_permit_request_uploads.hold_status', $search)
                ->or_like('bookings.no_booking', $search)
                ->or_like('bookings.no_reference', $search)
                ->or_like('uploads.description', $search)
                ->or_like('ref_people.name', $search)
                ->group_end();
        }

        if (key_exists('hold_type', $filters) && !empty($filters['hold_type'])) {
            $baseQuery->where('transporter_entry_permit_request_holds.hold_type', $filters['hold_type']);
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->where('uploads.id_person', $filters['customer']);
        }

        if (key_exists('goods', $filters) && !empty($filters['goods'])) {
            $baseQuery->where('transporter_entry_permit_request_uploads.id_goods', $filters['goods']);
        }

        if (key_exists('unit', $filters) && !empty($filters['unit'])) {
            $baseQuery->where('transporter_entry_permit_request_uploads.id_unit', $filters['unit']);
        }

        if (key_exists('ex_no_container', $filters)) {
            $baseQuery->where('IFNULL(transporter_entry_permit_request_uploads.ex_no_container, "") = "' . if_empty($filters['ex_no_container']) . '"');
        }

        if (key_exists('upload', $filters) && !empty($filters['unit'])) {
            $baseQuery->where('transporter_entry_permit_request_uploads.id_upload', if_empty($filters['upload']));
        }

        if (key_exists('priority', $filters) && !empty($filters['priority'])) {
            $baseQuery->where('transporter_entry_permit_request_uploads.priority', $filters['priority']);
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
}
