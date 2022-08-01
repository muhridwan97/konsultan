<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class InvoiceModel extends MY_Model
{
    protected $table = 'invoices';

    const STATUS_DRAFT = 'DRAFT';
    const STATUS_PUBLISHED = 'PUBLISHED';
    const STATUS_CANCELED = 'CANCELED';

    /**
     * InvoiceModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Generate invoice auto number
     * @param string $type
     * @return string
     */
    public function getAutoNumberInvoice($type = 'DRAFT')
    {
        $branchId = get_active_branch('id');
        if (empty($branchId)) {
            $branchId = '0';
        }

        $code = 'INV';
        if ($type == 'DRAFT') {
            $code = 'DRF';
        }

        $orderData = $this->db->query("
            SELECT CAST(RIGHT(no_invoice, 6) AS UNSIGNED) + 1 AS order_number 
            FROM invoices 
            WHERE MONTH(created_at) = MONTH(NOW()) 
              AND YEAR(created_at) = YEAR(NOW())
              AND SUBSTR(no_invoice, 5, 3) = '{$code}'      
              AND id_branch = '{$branchId}'
            ORDER BY no_invoice DESC LIMIT 1
        ");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return 'TCI.' . $code . '.' . $branchId . '.' . date('Ymd') . '.' . $orderPad;
    }

    /**
     * Get active record query builder for all related invoice selection.
     * @param null $branchId
     * @param null $userType
     * @param null $customerId
     * @return CI_DB_query_builder
     */
    public function getBaseInvoiceQuery($branchId = null, $userType = null, $customerId = null)
    {
        if(empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        if(empty($userType)) {
            $userType = UserModel::authenticatedUserData('user_type');
        }

        if(empty($customerId)) {
            $customerId = UserModel::authenticatedUserData('id_person');
        }

        $invoices = $this->db->select([
            'invoices.*',
            'bookings.id AS id_booking',
            'work_orders.id AS id_work_order',
            'handlings.id AS id_handling',
            'ref_people.name AS customer_name',
            'prv_users.username AS admin_name',
            '(
                SELECT SUM(unit_price * quantity * unit_multiplier)
                FROM invoice_details
                WHERE invoice_details.id_invoice = invoices.id
            ) AS total_price',
            //'SUM(unit_price * quantity * unit_multiplier) AS total_price',
            'COALESCE(booking_invoices.no_booking, handling_invoices.no_booking, work_order_invoices.no_booking) AS no_booking',
            'COALESCE(booking_invoices.no_reference, handling_invoices.no_reference, work_order_invoices.no_reference) AS no_reference_booking'
        ])
            ->from($this->table)
            //->join('invoice_details', 'invoices.id = invoice_details.id_invoice', 'left')
            ->join('ref_people', 'invoices.id_customer = ref_people.id', 'left')
            ->join('bookings', 'invoices.no_reference = bookings.no_booking', 'left')
            ->join('work_orders', 'invoices.no_reference = work_orders.no_work_order', 'left')
            ->join('handlings', 'invoices.no_reference = handlings.no_handling', 'left')
            ->join(UserModel::$tableUser, 'invoices.created_by = prv_users.id', 'left')
            ->join('bookings AS booking_invoices', 'invoices.no_reference = booking_invoices.no_booking', 'left')
            ->join('(
                SELECT no_handling, no_reference, no_booking FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
            ) AS handling_invoices', 'invoices.no_reference = handling_invoices.no_handling', 'left')
            ->join('(
                SELECT no_work_order, no_reference, no_booking FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
            ) AS work_order_invoices', 'invoices.no_reference = work_order_invoices.no_work_order', 'left');
            //->group_by('invoices.id, handling_invoices.no_booking, work_order_invoices.no_booking');

        if (!empty($branchId)) {
            $invoices->where('invoices.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $invoices->where('invoices.id_customer', $customerId);
        }

        return $invoices;
    }

    /**
     * Get all invoice with or without deleted records.
     * @param int $start
     * @param int $length
     * @param string $search
     * @param int $column
     * @param string $sort
     * @param bool $withTrashed
     * @return array
     */
    public function getAllInvoices($start = -1, $length = 10, $search = '', $column = 0, $sort = 'DESC', $withTrashed = false)
    {
        $columnOrder = [
            0 => "invoices.id",
            1 => "customer_name",
            2 => "no_invoice",
            3 => "invoices.no_reference",
            4 => "invoice_date",
            5 => "total_price",
            6 => "type",
            7 => "invoices.status",
            8 => "invoices.id",
        ];
        $columnSort = $columnOrder[$column];

        $branchId = get_active_branch('id');
        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $this->db->start_cache();

        $invoices = $this->getBaseInvoiceQuery($branchId, $userType, $customerId);

        if (!empty($search)) {
            $search = trim($search);
            $invoices->group_start()
                ->like('ref_people.name', $search)
                ->or_like('invoices.no_reference', $search)
                ->or_like('invoices.no_invoice', $search)
                ->or_like('invoice_date', $search)
                ->or_like('invoices.type', $search)
                ->or_like('invoices.no_faktur', $search)
                ->or_like('invoices.status', $search)
                ->or_like('invoices.inbound_date', $search)
                ->or_like('invoices.outbound_date', $search)
                ->or_like('invoices.item_summary', $search)
                ->group_end();
        }

        if (!$withTrashed) {
            $invoices->where('invoices.is_deleted', false);
        }

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $invoices->order_by('id', 'desc')->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $reportTotal = $this->db->count_all_results();
        $invoices->order_by($columnSort, $sort);
        $reportData = $invoices->limit($length, $start)->get()->result_array();

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($reportData),
            "recordsFiltered" => $reportTotal,
            "data" => $reportData
        ];

        $this->db->flush_cache();

        return $pageData;
    }

    /**
     * Get single invoice data by id with or without deleted record.
     * @param integer $id
     * @param $withTrashed
     * @return array
     */
    public function getInvoiceById($id, $withTrashed = false)
    {
        $invoice = $this->getBaseInvoiceQuery()->where('invoices.id', $id);

        if (!$withTrashed) {
            $invoice->where('invoices.is_deleted', false);
        }

        return $invoice->get()->row_array();
    }

    /**
     * Get invoice by reference number.
     *
     * @param $referenceNo
     * @param null $status
     * @param bool $withTrashed
     * @return mixed
     */
    public function getInvoicesByNoReference($referenceNo, $status = null, $withTrashed = false)
    {
        $invoices = $this->getBaseInvoiceQuery()
            ->where('invoices.no_reference', $referenceNo)
            ->order_by('id', 'desc');

        if (!empty($status)) {
            if (is_array($status)) {
                $invoices->where_in('invoices.status', $status);
            } else {
                $invoices->where('invoices.status', $status);
            }
        }

        if (!$withTrashed) {
            $invoices->where('invoices.is_deleted', false);
        }

        return $invoices->get()->result_array();
    }

    /**
     * Get invoice by raw contacts.
     *
     * @param $rawContactId
     * @return mixed
     */
    public function getInvoicesByRawContact($rawContactId)
    {
        $invoices = $this->getBaseInvoiceQuery()
            ->where('invoices.id_raw_contact', $rawContactId)
            ->order_by('id', 'desc');

        return $invoices->get()->result_array();
    }

    /**
     * Get invoice by bl or no container.
     * @param $noBl
     * @param $noContainer
     * @return array
     */
    public function getInvoicesByBLNoContainer($noContainer, $noBl = null)
    {
        $invoices = $this->db->select('invoices.*, ref_people.name AS customer_name')
            ->from('invoices')
            ->join('ref_people', 'ref_people.id = invoices.id_customer', 'left')
            ->join('bookings', 'invoices.no_reference = bookings.no_booking', 'left');
            //->where('invoices.status', 'PUBLISHED');

        if(!empty($noContainer)) {
            $invoices->like('invoices.item_summary', $noContainer);
        }

        if (!empty($noBl)) {
            $invoices
                ->join('booking_extensions', 'booking_extensions.id_booking = bookings.id', 'left')
                ->join('ref_extension_fields', 'booking_extensions.id_extension_field = ref_extension_fields.id', 'left')
                ->group_start()
                ->where('ref_extension_fields.field_name', 'NO_BL')
                ->where('booking_extensions.value', $noBl)
                ->group_end();
        }

        return $invoices->get()->result_array();
    }

    /**
     * Get invoice data by type.
     * @param array $types
     * @param null $customerId
     * @param array $statuses
     * @param bool $withTrashed
     * @return mixed
     */
    public function getInvoicesByTypes($types, $customerId = null, $statuses = [], $withTrashed = false)
    {
        $invoices = $this->getBaseInvoiceQuery()->order_by('id', 'desc');

        if (is_array($types)) {
            $invoices->where_in('invoices.type', $types);
        } else {
            $invoices->where('invoices.type', $types);
        }

        if (!empty($statuses)) {
            if (is_array($statuses)) {
                $invoices->where_in('invoices.status', $statuses);
            } else {
                $invoices->where('invoices.status', $statuses);
            }
        }

        if (!empty($customerId)) {
            $invoices->where('invoices.id_customer', $customerId);
        }

        if (!$withTrashed) {
            $invoices->where('invoices.is_deleted', false);
        }

        return $invoices->get()->result_array();
    }

    /**
     * Create new invoice data.
     * @param $data
     * @return bool
     */
    public function createInvoice($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update invoice data.
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateInvoice($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete invoice data.
     * @param integer $id
     * @param bool $softDelete
     * @return bool
     */
    public function deleteInvoice($id, $softDelete = true)
    {
        if ($softDelete) {
            return $this->db->update($this->table, [
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => UserModel::authenticatedUserData('id')
            ], ['id' => $id]);
        }
        return $this->db->delete($this->table, ['id' => $id]);
    }

    /**
     * Build party short string text by item_summary
     * @param $invoice
     */
    public function buildPartyLabel(&$invoice)
    {
        $totalItem = 0;
        $totalDanger = 0;
        $partyText = '';

        $party = explode(', ', $invoice['item_summary']);
        $partyData = [];
        $partyDataLCL = [];
        foreach ($party as $item) {
            $totalItem++;

            $attributes = get_string_between($item, '(', ')');
            if (!preg_match('/Kg/', $attributes) && !preg_match('/M3/', $attributes)) {
                $itemArray = explode('-', $attributes);
                if (count($itemArray) > 1 && is_numeric($itemArray[1])) {
                    $temp = $itemArray[1];
                    $itemArray[1] = $itemArray[0];
                    $itemArray[0] = $temp;
                }
                if (key_exists(2, $itemArray)) {
                    if ($itemArray[2] != 'NOT DANGER') {
                        $totalDanger++;
                    }
                    unset($itemArray[2]);
                }
                $attr = implode('-', $itemArray);
                $partyData[$attr][] = $item;
            } else {
                $itemAttribute = get_string_between($item, '(', ')');
                $attributes = explode('-', $itemAttribute);
                $quantity = key_exists(0, $attributes) ? preg_replace('/[a-zA-Z]/', '', $attributes[0]) : 0;
                $tonnage = key_exists(1, $attributes) ? preg_replace('/[a-zA-Z]/', '', $attributes[1]) : 0;
                $volume = key_exists(2, $attributes) ? preg_replace('/(M3|m3)/', '', $attributes[2]) : 0;

                $partyDataLCL['quantity'] = (key_exists('quantity', $partyDataLCL) ? floatval($partyDataLCL['quantity']) : 0) + floatval($quantity);
                $partyDataLCL['tonnage'] = (key_exists('tonnage', $partyDataLCL) ? floatval($partyDataLCL['tonnage']) : 0) + floatval($tonnage);
                $partyDataLCL['volume'] = (key_exists('volume', $partyDataLCL) ? floatval($partyDataLCL['volume']) : 0) + floatval($volume);
                if (key_exists(3, $attributes)) {
                    if ($attributes[3] != 'NOT DANGER') {
                        $totalDanger++;
                    }
                }
            }
        }

        $count = 0;
        foreach ($partyData as $datum => $items) {
            if ($count > 0) {
                $partyText .= ', ';
            }
            $partyText .= count($items) . 'x' . $datum;
            $count++;
        }
        if (!empty($partyDataLCL)) {
            if (!empty($partyData)) {
                $partyText .= ', ';
            }
            $partyText .= 'Items ' . $partyDataLCL['tonnage'] . 'Kg, ' . $partyDataLCL['volume'] . 'M3';
        }
        $invoice['party'] = $partyText;
        $invoice['danger'] = $totalDanger . ' / ' . $totalItem;
    }
}