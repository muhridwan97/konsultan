<?php

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') or exit('No direct script access allowed');

class ReportInvoiceModel extends MY_Model
{

    /**
     * Get report invoice data.
     *
     * @param array $filters
     * @return array
     */
    public function getReportInvoice($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? $filters['search'] : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branch = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch('id');

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        // alias column name by index for sorting data table library
        $columnOrder = [
            0 => "branch",
            1 => "branch",
            2 => "YEAR(invoice_date)",
            3 => "no_reference",
            4 => "no_reference_booking",
            5 => "invoice_date",
            6 => "invoices.type",
            7 => "handling_type",
            8 => "no_invoice",
            9 => "no_faktur",
            10 => "ref_people.name",
            11 => "inbound_date",
            12 => "outbound_date",
            13 => "days",
            14 => "item_summary",
            15 => "storage",
            16 => "lift_on_off",
            17 => "moving",
            18 => "moving_adjustment",
            19 => "seal",
            20 => "fcl_prioritas",
            21 => "fcl_behandle",
            22 => "ob_tps",
            23 => "non_ob_tps",
            24 => "non_ob_tps", // discount
            25 => "admin_fee",
            26 => "dpp",
            27 => "tax",
            28 => "materai",
            29 => "total_price",
            30 => "payment_date",
            31 => "transfer_bank",
            32 => "transfer_amount",
            33 => "cash_amount",
            34 => "(transfer_amount + cash_amount)",
            35 => "over_payment_amount",
            36 => "payment_description",
        ];
        $columnSort = $columnOrder[$column];

        $this->db->start_cache();

        $report = $this->db->select([
            'ref_people.name AS customer_name',
            'ref_branches.branch',
            'invoices.*',
            'COALESCE(booking_invoices.no_reference, handling_invoices.no_reference, work_order_invoices.no_reference) AS no_reference_booking',
            'bookings.id AS id_booking',
            'work_orders.id AS id_work_order',
            'handlings.id AS id_handling',
            'ref_handling_types.handling_type',
            'ref_handling_types_job.handling_type AS handling_type_job',
            'DATEDIFF(invoices.outbound_date, invoices.inbound_date) + 1 AS days',
            "SUM(IF(invoice_details.type = 'STORAGE', (unit_price * quantity * unit_multiplier), 0)) AS storage",
            "SUM(IF(invoice_details.item_name = 'LIFT ON/OFF FULL' OR invoice_details.item_name = 'LIFT ON/OFF FULL WITH LABEL IMDG CODE' OR invoice_details.item_name = 'LIFT ON/OFF EMPTY', (unit_price * quantity * unit_multiplier), 0)) AS lift_on_off",
            "SUM(IF(invoice_details.item_name = 'MOVING IN' OR invoice_details.item_name = 'MOVING IN FULL' OR invoice_details.item_name = 'MOVING IN EMPTY', (unit_price * quantity * unit_multiplier), 0)) AS moving",
            "SUM(IF(invoice_details.item_name = 'GERAKAN DAN PENGATURAN' OR invoice_details.item_name = 'GERAKAN DAN PENGATURAN QTY: 1 (MOVING IN)', (unit_price * quantity * unit_multiplier), 0)) AS moving_adjustment",
            "SUM(IF(invoice_details.item_name = 'SEAL', (unit_price * quantity * unit_multiplier), 0)) AS seal",
            "SUM(IF(invoice_details.item_name = 'PENCACAHAN FCL PRIORITAS', (unit_price * quantity * unit_multiplier), 0)) AS fcl_prioritas",
            "SUM(IF(invoice_details.item_name = 'PENCACAHAN FCL BEHANDLE', (unit_price * quantity * unit_multiplier), 0)) AS fcl_behandle",
            "SUM(IF(invoice_details.item_name = 'OB TPS', (unit_price * quantity * unit_multiplier), 0)) AS ob_tps",
            "SUM(IF(invoice_details.item_name != 'OB TPS' AND invoice_details.type = 'PAYMENT', (unit_price * quantity * unit_multiplier), 0)) AS non_ob_tps",
            "SUM(IF(invoice_details.item_name = 'ADMINISTRASI FEE', (unit_price * quantity * unit_multiplier), 0)) AS admin_fee",
            "SUM(IF(invoice_details.item_name = 'PPN (10%)', (unit_price * quantity * unit_multiplier), 0)) AS tax",
            "SUM(IF(invoice_details.item_name = 'Materai', (unit_price * quantity * unit_multiplier), 0)) AS materai",
            "SUM(IF(invoice_details.type = 'STORAGE' OR invoice_details.type = 'HANDLING' OR invoice_details.type = 'COMPONENT' OR invoice_details.type = 'INVOICE', (unit_price * quantity * unit_multiplier), 0)) AS dpp",
            "SUM(unit_price * quantity * unit_multiplier) AS total_price",
        ])
            ->from('invoices')
            ->join('bookings AS booking_invoices', 'invoices.no_reference = booking_invoices.no_booking', 'left')
            ->join('(
                SELECT no_handling, no_reference 
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
            ) AS handling_invoices', 'invoices.no_reference = handling_invoices.no_handling', 'left')
            ->join('(
                SELECT no_work_order, no_reference 
                FROM bookings
                INNER JOIN handlings ON handlings.id_booking = bookings.id
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
            ) AS work_order_invoices', 'invoices.no_reference = work_order_invoices.no_work_order', 'left')
            ->join('ref_people', 'invoices.id_customer = ref_people.id', 'left')
            ->join('ref_branches', 'invoices.id_branch = ref_branches.id', 'left')
            ->join('bookings', 'invoices.no_reference = bookings.no_booking', 'left')
            ->join('work_orders', 'invoices.no_reference = work_orders.no_work_order', 'left')
            ->join('handlings AS handling_job', 'work_orders.id_handling = handling_job.id', 'left')
            ->join('ref_handling_types AS ref_handling_types_job', 'handling_job.id_handling_type = ref_handling_types_job.id', 'left')
            ->join('handlings', 'invoices.no_reference = handlings.no_handling', 'left')
            ->join('ref_handling_types', 'handlings.id_handling_type = ref_handling_types.id', 'left')
            ->join('invoice_details', 'invoice_details.id_invoice = invoices.id', 'left')
            ->where('invoices.status', 'PUBLISHED')
            ->group_by('invoices.id, handling_invoices.no_reference, work_order_invoices.no_reference');

        if (!empty($filters)) {
            if (key_exists('invoice_type', $filters) && !empty($filters['invoice_type'])) {
                if (is_array($filters['invoice_type'])) {
                    $report->where_in('invoices.type', $filters['invoice_type']);
                } else {
                    $report->where('invoices.type', $filters['invoice_type']);
                }
            }

            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                if (is_array($filters['owner'])) {
                    $report->where_in('invoices.id_customer', $filters['owner']);
                } else {
                    $report->where('invoices.id_customer', $filters['owner']);
                }
            }

            if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                $report->where('DATE(invoices.invoice_date) >=', sql_date_format($filters['date_from']));
            }

            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $report->where('DATE(invoices.invoice_date) <=', sql_date_format($filters['date_to']));
            }
        }

        if (!empty($branch)) {
            $report->where('invoices.id_branch', $branch);
        }

        if ($userType == 'EXTERNAL') {
            $report->where('invoices.id_customer', $customerId);
        }

        if (key_exists('search', $filters) && !empty($filters['search'])) {
            $search = (is_array($filters['search']) ? $search : $filters['search']);
        }

        $report
            ->group_start()
            ->like('ref_branches.branch', $search)
            ->or_like('YEAR(invoices.invoice_date)', $search)
            ->or_like('invoices.no_reference', $search)
            ->or_like('invoices.invoice_date', $search)
            ->or_like('invoices.type', $search)
            ->or_like('invoices.no_invoice', $search)
            ->or_like('invoices.no_faktur', $search)
            ->or_like('ref_people.name', $search)
            ->or_like('invoices.inbound_date', $search)
            ->or_like('invoices.outbound_date', $search)
            ->or_like('booking_invoices.no_reference', $search)
            ->or_like('handling_invoices.no_reference', $search)
            ->or_like('work_order_invoices.no_reference', $search)
            ->or_like('DATEDIFF(invoices.outbound_date, invoices.inbound_date)', $search)
            ->or_like('invoices.item_summary', $search)
            ->group_end();

        $this->db->stop_cache();

        if ($start < 0) {
            $allData = $report->get()->result_array();
            $this->db->flush_cache();
            return $allData;
        }

        $reportTotal = $this->db->count_all_results();
        $report->order_by($columnSort, $sort);
        $reportData = $report->limit($length, $start)->get()->result_array();

        foreach ($reportData as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => get_url_param('draw', $this->input->post('draw')),
            "recordsTotal" => count($reportData),
            "recordsFiltered" => $reportTotal,
            "data" => $reportData
        ];

        $this->db->flush_cache();

        return $pageData;
    }

    /**
     * Get report invoice CIF.
     *
     * @param $filters
     * @return array
     */
    public function getReportInvoiceCIF($filters = [])
    {
        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));
        $handlingTypeIdOutbound = get_setting('default_outbound_handling');

        $this->db->start_cache();

        $baseQuery = $this->db->select([
            'customers.name AS customer_name',
            'bookings.id AS id_booking_in',
            'bookings.no_reference AS no_reference_inbound',
            'ref_booking_types.booking_type AS booking_type_inbound',
            'no_registrations.value AS no_registration',
            'registration_dates.value AS registration_date',
            'booking_cif_invoices.party',
            'inbound_details.goods_name AS cargo_inbound',
            'IFNULL(SUM(booking_cif_invoice_details.weight), 0) AS net_weight_inbound',
            'IFNULL(SUM(booking_cif_invoice_details.gross_weight), 0) AS gross_weight_inbound',
            'booking_cif_invoices.currency_from',
            'booking_cif_invoices.currency_to',
            'ROUND(SUM(booking_cif_invoice_details.quantity * booking_cif_invoice_details.price) + discount + freight + insurance + handling + other, 2) AS total_cif_inbound_from',
            'ROUND((SUM(booking_cif_invoice_details.quantity * booking_cif_invoice_details.price) + discount + freight + insurance + handling + other) * booking_cif_invoices.exchange_value, 2) AS total_cif_inbound_to',
            'outbounds.id AS id_booking_out',
            'outbounds.no_reference AS no_reference_outbound',
            'outbounds.booking_type AS booking_type_outbound',
            'no_registration_outbounds.value AS no_registration_outbound',
            'registration_date_outbounds.value AS registration_date_outbound',
            'outbound_details.goods_name AS cargo_outbound',
            'outbounds.net_weight_outbound',
            'outbounds.gross_weight_outbound',
            'payment_documents.payment_confirmation_date',
            'sppb_documents.sppb_date',
            'sppd_documents.sppd_date',
            'gate_outs.gate_out',
            'ROUND(outbounds.total_price_value, 2) AS total_cif_outbound_from',
            'ROUND(outbounds.total_price_value * outbounds.exchange_value, 2) AS total_cif_outbound_to',
            '(outbounds.total_price_value * outbounds.ndpbm) AS customs_value',
            'outbounds.ndpbm',
            'outbounds.import_duty',
            'outbounds.vat',
            'outbounds.income_tax'
        ])
            ->from('booking_cif_invoices')
            ->join('bookings', 'bookings.id = booking_cif_invoices.id_booking')
            ->join('(
                SELECT DISTINCT booking_extensions.id_booking, booking_extensions.value FROM booking_extensions 
                LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
                WHERE ref_extension_fields.field_name = "nopen"
            ) AS no_registrations', 'no_registrations.id_booking = bookings.id', 'left')
            ->join('(
                SELECT DISTINCT booking_extensions.id_booking, booking_extensions.value FROM booking_extensions 
                LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
                WHERE ref_extension_fields.field_name = "tapen"
            ) AS registration_dates', 'registration_dates.id_booking = bookings.id', 'left')
            ->join('booking_cif_invoice_details AS inbound_details', 'inbound_details.id = (
                SELECT MIN(last_inbound_details.id) AS id
                FROM booking_cif_invoice_details AS last_inbound_details 
                WHERE last_inbound_details.id_booking_cif_invoice = booking_cif_invoices.id
              )', 'left', false)
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type')
            ->join('booking_cif_invoice_details', 'booking_cif_invoice_details.id_booking_cif_invoice = booking_cif_invoices.id')
            ->join("(
                SELECT 
                    bookings.id, 
                    bookings.id_upload, 
                    bookings.id_booking, 
                    bookings.no_reference, 
                    ref_booking_types.booking_type, 
                    booking_cif_invoices.id AS id_booking_cif_invoice,
                    booking_cif_invoices.ndpbm,
                    booking_cif_invoices.import_duty,
                    booking_cif_invoices.vat,
                    booking_cif_invoices.income_tax,
                    booking_cif_invoices.exchange_value,
                    IFNULL(SUM(booking_cif_invoice_details.weight), 0) AS net_weight_outbound,
                    IFNULL(SUM(booking_cif_invoice_details.gross_weight), 0) AS gross_weight_outbound,
                    SUM(booking_cif_invoice_details.quantity * booking_cif_invoice_details.price) + 
                        SUM(booking_cif_invoice_details.quantity / inbound_details.quantity * total_item_value_inbound) AS total_price_value                  
                FROM booking_cif_invoices
                INNER JOIN booking_cif_invoice_details ON booking_cif_invoice_details.id_booking_cif_invoice = booking_cif_invoices.id
                LEFT JOIN (                
                    SELECT 
                        booking_cif_invoice_details.id,
                        booking_cif_invoice_details.quantity,
                        (quantity / total_item_quantity * booking_cif_invoices.total_distributed_cost) AS total_item_value_inbound 
                    FROM booking_cif_invoice_details
                    INNER JOIN (
                        SELECT 
                            booking_cif_invoices.*,
                            SUM(booking_cif_invoice_details.quantity) AS total_item_quantity
                        FROM (
                            SELECT booking_cif_invoices.id, (discount + freight + insurance + handling + other) AS total_distributed_cost 
                            FROM booking_cif_invoices
                        ) AS booking_cif_invoices
                        LEFT JOIN booking_cif_invoice_details ON booking_cif_invoice_details.id_booking_cif_invoice = booking_cif_invoices.id
                        GROUP BY booking_cif_invoices.id
                    ) AS booking_cif_invoices ON booking_cif_invoices.id = booking_cif_invoice_details.id_booking_cif_invoice                
                ) AS inbound_details ON inbound_details.id = booking_cif_invoice_details.id_booking_cif_invoice_detail
                INNER JOIN bookings ON bookings.id = booking_cif_invoices.id_booking
                INNER JOIN ref_booking_types ON ref_booking_types.id = bookings.id_booking_type
                WHERE ref_booking_types.category = 'OUTBOUND'
                GROUP BY booking_cif_invoices.id, bookings.id                
            ) AS outbounds", 'outbounds.id_booking = bookings.id', 'left')
            ->join('(
                SELECT DISTINCT booking_extensions.id_booking, booking_extensions.value FROM booking_extensions 
                LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
                WHERE ref_extension_fields.field_name = "nopen"
            ) AS no_registration_outbounds', 'no_registration_outbounds.id_booking = outbounds.id', 'left')
            ->join('(
                SELECT DISTINCT booking_extensions.id_booking, booking_extensions.value FROM booking_extensions 
                LEFT JOIN ref_extension_fields ON booking_extensions.id_extension_field = ref_extension_fields.id
                WHERE ref_extension_fields.field_name = "tapen"
            ) AS registration_date_outbounds', 'registration_date_outbounds.id_booking = outbounds.id', 'left')
            ->join('booking_cif_invoice_details AS outbound_details', 'outbound_details.id = (
                SELECT MIN(last_outbound_details.id) AS id
                FROM booking_cif_invoice_details AS last_outbound_details 
                WHERE last_outbound_details.id_booking_cif_invoice = outbounds.id_booking_cif_invoice
              )', 'left', false)
            ->join("(
                SELECT 
                    upload_documents.id_upload,
                    DATE(MAX(upload_documents.document_date)) as sppb_date             
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'SPPB'
                GROUP BY id_upload              
            ) AS sppb_documents", 'sppb_documents.id_upload = outbounds.id_upload', 'left')
            ->join("(
                SELECT 
                    upload_documents.id_upload,
                    DATE(MAX(upload_documents.document_date)) as payment_confirmation_date             
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'BPN (Bukti Penerimaan Negara)'
                GROUP BY id_upload              
            ) AS payment_documents", 'payment_documents.id_upload = outbounds.id_upload', 'left')
            ->join("(
                SELECT 
                    upload_documents.id_upload,
                    DATE(MAX(upload_documents.document_date)) as sppd_date             
                FROM upload_documents
                INNER JOIN ref_document_types ON ref_document_types.id = upload_documents.id_document_type
                WHERE ref_document_types.document_type = 'SPPD'
                GROUP BY id_upload              
            ) AS sppd_documents", 'sppd_documents.id_upload = outbounds.id_upload', 'left')
            ->join("(
                SELECT handlings.id_booking, MAX(IF(expedition_type = 'INTERNAL', gate_in_date, gate_out_date)) AS gate_out
                FROM handlings
                INNER JOIN work_orders ON work_orders.id_handling = handlings.id
                INNER JOIN safe_conducts ON safe_conducts.id = work_orders.id_safe_conduct
                WHERE handlings.id_handling_type = '{$handlingTypeIdOutbound}'
                GROUP BY id_booking
            ) AS gate_outs", 'gate_outs.id_booking = outbounds.id', 'left')
            ->where([
                'booking_cif_invoices.is_deleted' => false,
                'bookings.is_deleted' => false,
                'ref_booking_types.category' => 'INBOUND'
            ])
            ->order_by('no_reference_inbound', 'desc')
            ->order_by('no_reference_outbound', 'asc')
            ->group_by('booking_cif_invoices.id, bookings.id, no_registration, registration_date, inbound_details.goods_name, outbound_details.goods_name, outbounds.net_weight_outbound, outbounds.gross_weight_outbound, outbounds.id, outbounds.no_reference, no_registration_outbound, registration_date_outbound, outbounds.ndpbm, outbounds.import_duty, outbounds.vat, outbounds.income_tax, outbounds.total_price_value, outbounds.exchange_value');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if (!empty($filters)) {
            if (key_exists('owner', $filters) && !empty($filters['owner'])) {
                $baseQuery->where_in('customers.id', $filters['owner']);
            }
            if (key_exists('booking', $filters) && !empty($filters['booking'])) {
                $baseQuery->where_in('bookings.id', $filters['booking']);
            }
            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
                if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                    $baseQuery->where('DATE(' . $filters['date_type'] . ') >=', format_date($filters['date_from']));
                }
                if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                    $baseQuery->where('DATE(' . $filters['date_type'] . ') <=', format_date($filters['date_to']));
                }
            }
        }

        $this->db->stop_cache();

        if (key_exists('per_page', $filters) && !empty($filters['per_page'])) {
            $perPage = $filters['per_page'];
        } else {
            $perPage = 25;
        }

        if (key_exists('page', $filters) && !empty($filters['page'])) {
            $currentPage = $filters['page'];

            $totalData = $this->db->count_all_results();

            if (key_exists('sort_by', $filters) && !empty($filters['sort_by'])) {
                if (key_exists('order_method', $filters) && !empty($filters['order_method'])) {
                    $baseQuery->order_by($filters['sort_by'], $filters['order_method']);
                } else {
                    $baseQuery->order_by($filters['sort_by'], 'asc');
                }
            }
            $pageData = $baseQuery->limit($perPage, ($currentPage - 1) * $perPage)->get()->result_array();

            $this->db->flush_cache();

            return [
                '_paging' => true,
                'total_data' => $totalData,
                'total_page_data' => count($pageData),
                'total_page' => ceil($totalData / $perPage),
                'per_page' => $perPage,
                'current_page' => $currentPage,
                'data' => $pageData
            ];
        }

        $data = $baseQuery->get()->result_array();

        $this->db->flush_cache();

        return $data;
    }

    /**
     * Export data to excel.
     *
     * @param $data
     * @return string
     */
    public function exportCIFMonthly($data)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator($this->config->item('app_name'))
            ->setLastModifiedBy($this->config->item('app_name'))
            ->setTitle('CIF Monthly')
            ->setSubject('Data export: CIF Invoice')
            ->setDescription('Data export generated by: ' . $this->config->item('app_name'));


        try {
            $spreadsheet->setActiveSheetIndex(0);
            $activeSheet = $spreadsheet->getActiveSheet()->setTitle('CIF Monthly');

            $activeSheet->setCellValue('A1', 'CIF')
                ->getStyle('A1')
                ->getFont()
                ->setBold(true);

            $activeSheet->setCellValue('A1', 'NO');
            $activeSheet->setCellValue('B1', 'JENIS DOK');
            $activeSheet->setCellValue('C1', 'AJU');
            $activeSheet->setCellValue('D1', 'NOPEN');
            $activeSheet->setCellValue('E1', 'TGL NOPEN');
            $activeSheet->setCellValue('F1', 'PARTY');
            $activeSheet->setCellValue('G1', 'CARGO IN');
            $activeSheet->setCellValue('H1', 'NET WEIGHT');
            $activeSheet->setCellValue('I1', 'GROSS WEIGHT');
            $activeSheet->setCellValue('J1', 'NILAI INVOICE');
            $activeSheet->setCellValue('K1', 'AS USD');
            $activeSheet->setCellValue('L1', 'CUSTOMER');
            $activeSheet->setCellValue('M1', '');
            $activeSheet->setCellValue('N1', '');
            $activeSheet->setCellValue('O1', 'DOK PENGELUARAN');
            $activeSheet->setCellValue('P1', 'AJU');
            $activeSheet->setCellValue('Q1', 'REF BC 1.6');
            $activeSheet->setCellValue('R1', 'NOPEN');
            $activeSheet->setCellValue('S1', 'TGL NOPEN');
            $activeSheet->setCellValue('T1', 'KONFIRMASI BAYAR');
            $activeSheet->setCellValue('U1', 'SPPB');
            $activeSheet->setCellValue('V1', 'NILAI INVOICE');
            $activeSheet->setCellValue('W1', 'AS USD');
            $activeSheet->setCellValue('X1', 'NDPBM');
            $activeSheet->setCellValue('Y1', 'NILAI PABEAN');
            $activeSheet->setCellValue('Z1', 'BM');
            $activeSheet->setCellValue('AA1', 'PPN');
            $activeSheet->setCellValue('AB1', 'PPH');
            $activeSheet->setCellValue('AC1', 'CUSTOMER');
            $activeSheet->setCellValue('AD1', 'CARGO');
            $activeSheet->setCellValue('AE1', 'NET WEIGHT');
            $activeSheet->setCellValue('AF1', 'GROSS WEIGHT');
            $activeSheet->setCellValue('AG1', 'GATE OUT');
            $activeSheet->setCellValue('AH1', 'SPPD');
            $activeSheet->setCellValue('AI1', 'BALANCING');

            $header = [
                'NO', 'JENIS DOK', 'AJU', 'NOPEN', 'TGL NOPEN', 'PARTY', 'CARGO IN', 'NET WEIGHT', 'GROSS WEIGHT', 'NILAI INVOICE', 'AS USD', 'CUSTOMER',
                '', '',
                'DOK PENGELUARAN', 'AJU', 'REF BC 1.6', 'NOPEN', 'TGL NOPEN', 'KONFIRMASI BAYAR', 'SPPB', 'NILAI INVOICE', 'AS USD', 'NDPBM', 'NILAI PABEAN', 'BM', 'PPN', 'PPH', 'CUSTOMER', 'CARGO', 'NET WEIGHT', 'GROSS WEIGHT', 'GATE OUT', 'SPPD', 'BALANCING'
            ];
            $activeSheet->fromArray($header, null, 'A1');

            $columnIterator = $spreadsheet->getActiveSheet()->getColumnIterator();
            foreach ($columnIterator as $column) {
                $activeSheet
                    ->getColumnDimension($column->getColumnIndex())
                    ->setAutoSize(true);

                $activeSheet
                    ->getStyle($column->getColumnIndex() . '1')
                    ->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'color' => ['rgb' => 'FFFF00']
                            ],
                            'font' => [
                                'bold' => true
                            ]
                        ]
                    )
                    ->getAlignment()->setVertical('center');
            }
            $activeSheet->getRowDimension(1)->setRowHeight(30);

            $activeSheet
                ->getStyle('M1:N1')
                ->applyFromArray([
                        'fill' => [
                            'fillType' => Fill::FILL_SOLID,
                            'color' => ['rgb' => '979797']
                        ]
                    ]
                );

            $balance = 0;
            $row = 2;
            $no = 1;
            foreach ($data as $period => $invoices) {
                $activeSheet->setCellValueByColumnAndRow(1, $row, empty($period) ? 'No registration date' : strtoupper(format_date($period . '-1', 'F')));
                $activeSheet->mergeCellsByColumnAndRow(1, $row, 2, $row);
                $activeSheet
                    ->getStyleByColumnAndRow(1, $row, 1, $row)
                    ->applyFromArray([
                            'font' => [
                                'bold' => true,
                                'color' => ['rgb' => '0AAA11']
                            ]
                        ]
                    );
                $row += 1;

                $rowInbound = $row;
                $rowOutbound = $row;
                $noInbound = $no;
                $noOutbound = $no;
                if(key_exists('inbounds', $invoices)) {
                    foreach ($invoices['inbounds'] as $invoice) {
                        $activeSheet->setCellValueByColumnAndRow(1, $rowInbound, $noInbound);
                        $activeSheet->setCellValueByColumnAndRow(2, $rowInbound, $invoice['booking_type_inbound']);
                        $activeSheet->setCellValueByColumnAndRow(3, $rowInbound, $invoice['no_reference_inbound']);
                        $activeSheet->setCellValueByColumnAndRow(4, $rowInbound, $invoice['no_registration']);
                        $activeSheet->setCellValueByColumnAndRow(5, $rowInbound, $invoice['registration_date']);
                        $activeSheet->setCellValueByColumnAndRow(6, $rowInbound, $invoice['party']);
                        $activeSheet->setCellValueByColumnAndRow(7, $rowInbound, $invoice['cargo_inbound']);
                        $activeSheet->setCellValueByColumnAndRow(8, $rowInbound, $invoice['net_weight_inbound']);
                        $activeSheet->setCellValueByColumnAndRow(9, $rowInbound, $invoice['gross_weight_inbound']);
                        $activeSheet->setCellValueByColumnAndRow(10, $rowInbound, $invoice['currency_from'] . ' ' . $invoice['total_cif_inbound_from']);
                        $activeSheet->setCellValueByColumnAndRow(11, $rowInbound, $invoice['currency_to'] . ' ' . $invoice['total_cif_inbound_to']);
                        $activeSheet->setCellValueByColumnAndRow(12, $rowInbound, $invoice['customer_name']);
                        $rowInbound++;
                        $noInbound++;
                    }
                }
                if(key_exists('outbounds', $invoices)) {
                    foreach ($invoices['outbounds'] as $invoice) {
                        $activeSheet->setCellValueByColumnAndRow(1, $rowOutbound, $noOutbound);
                        $activeSheet->setCellValueByColumnAndRow(15, $rowOutbound, $invoice['booking_type_outbound']);
                        $activeSheet->setCellValueByColumnAndRow(16, $rowOutbound, $invoice['no_reference_outbound']);
                        $activeSheet->setCellValueByColumnAndRow(17, $rowOutbound, $invoice['no_reference_inbound']);
                        $activeSheet->setCellValueByColumnAndRow(18, $rowOutbound, $invoice['no_registration_outbound']);
                        $activeSheet->setCellValueByColumnAndRow(19, $rowOutbound, $invoice['registration_date_outbound']);
                        $activeSheet->setCellValueByColumnAndRow(20, $rowOutbound, $invoice['payment_confirmation_date']);
                        $activeSheet->setCellValueByColumnAndRow(21, $rowOutbound, $invoice['sppb_date']);
                        $activeSheet->setCellValueByColumnAndRow(22, $rowOutbound, $invoice['currency_from'] . ' ' . $invoice['total_cif_outbound_from']);
                        $activeSheet->setCellValueByColumnAndRow(23, $rowOutbound, $invoice['currency_to'] . ' ' . $invoice['total_cif_outbound_to']);
                        $activeSheet->setCellValueExplicitByColumnAndRow(24, $rowOutbound, $invoice['ndpbm'], DataType::TYPE_STRING);
                        $activeSheet->setCellValueExplicitByColumnAndRow(25, $rowOutbound, $invoice['customs_value'], DataType::TYPE_STRING);
                        $activeSheet->setCellValueExplicitByColumnAndRow(26, $rowOutbound, $invoice['import_duty'], DataType::TYPE_STRING);
                        $activeSheet->setCellValueExplicitByColumnAndRow(27, $rowOutbound, $invoice['vat'], DataType::TYPE_STRING);
                        $activeSheet->setCellValueExplicitByColumnAndRow(28, $rowOutbound, $invoice['income_tax'], DataType::TYPE_STRING);
                        $activeSheet->setCellValueByColumnAndRow(29, $rowOutbound, $invoice['customer_name']);
                        $activeSheet->setCellValueByColumnAndRow(30, $rowOutbound, $invoice['cargo_outbound']);
                        $activeSheet->setCellValueByColumnAndRow(31, $rowOutbound, $invoice['net_weight_outbound']);
                        $activeSheet->setCellValueByColumnAndRow(32, $rowOutbound, $invoice['gross_weight_outbound']);
                        $activeSheet->setCellValueByColumnAndRow(33, $rowOutbound, $invoice['gate_out']);
                        $activeSheet->setCellValueByColumnAndRow(34, $rowOutbound, $invoice['sppd_date']);
                        $rowOutbound++;
                        $noOutbound++;
                    }
                }

                if($rowInbound > $rowOutbound) {
                    $row = $rowInbound;
                    $no = $noInbound;
                } else {
                    $row = $rowOutbound;
                    $no = $noOutbound;
                }

                $activeSheet->setCellValueByColumnAndRow(1, $row, 'Balancing saldo per ' . format_date($period . '-1', 'Y F'));
                if(key_exists('inbounds', $invoices)) {
                    $totalInbound = array_sum(array_column($invoices['inbounds'], 'total_cif_inbound_to'));
                    $activeSheet->setCellValueByColumnAndRow(11, $row, 'USD ' . $totalInbound);
                    $balance += $totalInbound;
                } else {
                    $activeSheet->setCellValueByColumnAndRow(11, $row, 'USD 0');
                }

                if(key_exists('outbounds', $invoices)) {
                    $totalOutbound = array_sum(array_column($invoices['outbounds'], 'total_cif_outbound_to'));
                    $activeSheet->setCellValueByColumnAndRow(23, $row, 'USD ' . $totalOutbound);
                    $activeSheet->setCellValueByColumnAndRow(26, $row, array_sum(array_column($invoices['outbounds'], 'import_duty')));
                    $activeSheet->setCellValueByColumnAndRow(27, $row, array_sum(array_column($invoices['outbounds'], 'vat')));
                    $activeSheet->setCellValueByColumnAndRow(28, $row, array_sum(array_column($invoices['outbounds'], 'income_tax')));
                    $balance -= $totalOutbound;
                } else {
                    $activeSheet->setCellValueByColumnAndRow(23, $row, 'USD 0');
                    $activeSheet->setCellValueByColumnAndRow(26, $row, 0);
                    $activeSheet->setCellValueByColumnAndRow(27, $row, 0);
                    $activeSheet->setCellValueByColumnAndRow(28, $row, 0);
                }
                $activeSheet->mergeCellsByColumnAndRow(1, $row, 6, $row);
                $activeSheet->setCellValueByColumnAndRow(35, $row, 'USD ' . $balance);
                $activeSheet
                    ->getStyleByColumnAndRow(1, $row, count($header), $row)
                    ->applyFromArray([
                            'fill' => [
                                'fillType' => Fill::FILL_SOLID,
                                'color' => ['rgb' => 'BEE4F7']
                            ],
                            'font' => [
                                'bold' => true
                            ]
                        ]
                    );

                $row += 3;
            }

            $excelWriter = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="CIF Report.xlsx"');
            $excelWriter->save('php://output');

        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            return $e->getMessage();
        }
    }
}
