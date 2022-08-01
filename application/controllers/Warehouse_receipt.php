<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Warehouse_receipt
 * @property PeopleModel $people
 * @property WarehouseReceiptModel $warehouseReceipt
 * @property WarehouseReceiptDetailModel $warehouseReceiptDetail
 */
class Warehouse_receipt extends MY_Controller
{
    /**
     * Warehouse_receipt constructor.
     */
    public function __construct()
    {
        parent::__construct();

        AuthorizationModel::mustLoggedIn();

        $this->load->model('PeopleModel', 'people');
        $this->load->model('WarehouseReceiptModel', 'warehouseReceipt');
        $this->load->model('WarehouseReceiptDetailModel', 'warehouseReceiptDetail');
        $this->load->model('LogModel', 'logHistory');
        $this->load->model('modules/Mailer', 'mailer');
        $this->setFilterMethods([
            'warehouse_receipt_data' => 'GET',
            'print_warehouse_receipt' => 'GET',
            'validate_warehouse_receipt' => 'POST|PUT|PATCH',
        ]);
    }

    /**
     * Show warehouse receipt data.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WAREHOUSE_RECEIPT_VIEW);
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();

        $this->render('warehouse_receipt/index');
    }

    /**
     * Get ajax datatable pallet.
     */
    public function warehouse_receipt_data()
    {
        $filters = [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ];

        $data = $this->warehouseReceipt->getAll($filters);

        $this->render_json($data);
    }

    /**
     * Show detail warehouse receipt.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WAREHOUSE_RECEIPT_VIEW);

        $warehouseReceipt = $this->warehouseReceipt->getById($id);
        $warehouseReceiptDetails = $this->warehouseReceiptDetail->getBy(['id_warehouse_receipt' => $id]);

        $this->render('warehouse_receipt/view', compact('warehouseReceipt', 'warehouseReceiptDetails'));
    }

    /**
     * Print warehouse receipt.
     * @param $id
     */
    public function print_warehouse_receipt($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WAREHOUSE_RECEIPT_PRINT);

        $warehouseReceipt = $this->warehouseReceipt->getById($id);
        $warehouseReceiptDetails = $this->warehouseReceiptDetail->getSummaryByWarehouseReceipt($id);
        $data = [
            'title' => "Warehouse receipt",
            'warehouseReceipt' => $warehouseReceipt,
            'warehouseReceiptDetails' => $warehouseReceiptDetails
        ];

        $wrPage = $this->load->view('warehouse_receipt/print_warehouse_receipt', $data, true);
        $wrPage = str_replace("\n", "", $wrPage);
        $wrPage = str_replace("\r", "", $wrPage);

        $dompdf = new \Dompdf\Dompdf(['isHtml5ParserEnabled' => true]);
        $dompdf->loadHtml($wrPage);
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream($warehouseReceipt['no_warehouse_receipt'] . ".pdf", array("Attachment" => false));
    }

    /**
     * Show create warehouse receipt form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WAREHOUSE_RECEIPT_CREATE);

        $divider = get_setting('warehouse_receipt_weight') / 1000;

        $this->render('warehouse_receipt/create', compact('divider', 'handlings'));
    }

    /**
     * Save warehouse receipt.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WAREHOUSE_RECEIPT_CREATE);

        if ($this->validate()) {
            $branch = $this->input->post('branch');
            $customer = $this->input->post('customer');
            $requestDate = $this->input->post('request_date');
            $orderOf = $this->input->post('order_of');
            $duration = $this->input->post('duration');
            $issuanceDate = $this->input->post('issuance_date');
            $description = $this->input->post('description');

            $goods = $this->input->post('goods');
            $quantities = $this->input->post('quantities');
            $tonnages = $this->input->post('tonnages');
            $tonnagesGross = $this->input->post('tonnages_gross');
            $volumes = $this->input->post('volumes');
            $units = $this->input->post('units');
            $positions = $this->input->post('positions');
            $noPallets = $this->input->post('no_pallets');
            $noDOs = $this->input->post('no_delivery_orders');
            $isHolds = $this->input->post('is_hold');
            $statuses = $this->input->post('statuses');
            $statusDangers = $this->input->post('status_dangers');
            $inboundDates = $this->input->post('inbound_dates');

            $wrValue = get_setting('warehouse_receipt_weight');

            $totalWr = ceil(array_sum($tonnages) / $wrValue);

            $this->db->trans_start();

            $this->warehouseReceipt->update([
                'status' => 'EXPIRED'
            ], [
                'id_customer' => $customer,
                'status' => 'PENDING',
            ]);

            $batch = $this->warehouseReceipt->getNextBatch();

            $i = 0;

            for ($generatedWr = 0; $generatedWr < $totalWr; $generatedWr++) {
                $this->warehouseReceipt->create([
                    'id_branch' => $branch,
                    'id_customer' => $customer,
                    'no_warehouse_receipt' => $this->warehouseReceipt->getAutoNumberWarehouseReceipt(),
                    'no_batch' => $batch,
                    'request_date' => sql_date_format($requestDate, false),
                    'order_of' => $orderOf,
                    'issuance_date' => sql_date_format($issuanceDate, false),
                    'duration' => $duration,
                    'description' => $description,
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);
                $warehouseReceiptId = $this->db->insert_id();

                $totalWeight = 0;
                for (; $i < count($goods); $i++) {
                    $this->warehouseReceiptDetail->create([
                        'id_warehouse_receipt' => $warehouseReceiptId,
                        'id_owner' => $customer,
                        'id_goods' => $goods[$i],
                        'id_unit' => $units[$i],
                        'id_position' => $positions[$i],
                        'quantity' => $quantities[$i],
                        'tonnage' => $tonnages[$i],
                        'tonnage_gross' => $tonnagesGross[$i],
                        'volume' => $volumes[$i],
                        'no_pallet' => $noPallets[$i],
                        'no_delivery_order' => $noDOs[$i],
                        'status' => $statuses[$i],
                        'status_danger' => $statusDangers[$i],
                        'is_hold' => $isHolds[$i],
                        'inbound_date' => $inboundDates[$i],
                    ]);
                    $totalWeight += $tonnages[$i];
                    if ($totalWeight >= $wrValue) {
                        break;
                    }
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Generate warehouse receipt with total batch <strong>{$totalWr}</strong> WR successfully created", 'warehouse_receipt');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->create();
    }

    /**
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'branch' => 'trim|required|integer|is_natural_no_zero',
            'customer' => 'trim|required|integer',
            'request_date' => 'trim|required',
            'order_of' => 'trim|required',
            'duration' => 'trim|required',
            'issuance_date' => 'trim|required',
            'description' => 'trim|max_length[500]',
        ];
    }

    /**
     * Perform deleting warehouse receipt data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WAREHOUSE_RECEIPT_DELETE);

        $warehouseReceipt = $this->warehouseReceipt->getById($id);

        if ($this->warehouseReceipt->delete($id)) {
            flash('warning', "Warehouse receipt {$warehouseReceipt['no_warehouse_receipt']} is successfully deleted");
        } else {
            flash('danger', "Delete warehouse receipt {$warehouseReceipt['no_warehouse_receipt']} failed");
        }
        redirect('warehouse_receipt');
    }

    /**
     * Validate booking (approve/reject).
     *
     * @param $id
     */
    public function validate_warehouse_receipt($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WAREHOUSE_RECEIPT_VALIDATE);

        if ($this->validate(['status' => 'in_list[APPROVED,REJECTED]'])) {
            $status = $this->input->post('status');
            $description = $this->input->post('description');

            $warehouseReceipt = $this->warehouseReceipt->getById($id);

            if ($status == WarehouseReceiptModel::STATUS_APPROVED) {
                $this->warehouseReceipt->update([
                    'status' => 'EXPIRED'
                ], [
                    'id_customer' => $warehouseReceipt['id_customer'],
                    'status' => 'APPROVED',
                    'no_batch !=' => $warehouseReceipt['no_batch'],
                ]);

                $this->warehouseReceipt->update([
                    'status' => 'EXPIRED'
                ], [
                    'id_customer' => $warehouseReceipt['id_customer'],
                    'status' => 'PENDING',
                    'no_batch !=' => $warehouseReceipt['no_batch'],
                ]);
            }

            $updateStatus = $this->warehouseReceipt->update([
                'status' => $status,
                'validated_at' => sql_date_format('now'),
                'validated_by' => UserModel::authenticatedUserData('id')
            ], $id);

            if ($updateStatus) {
                $statusClass = 'warning';
                if ($status == WarehouseReceiptModel::STATUS_APPROVED) {
                    $statusClass = 'success';
                }
                $customer = $this->people->getById($warehouseReceipt['id_customer']);
                
                if (ENVIRONMENT == 'production') {
                    if (!empty($customer['email'])) {
                        $emailTo = $customer['email'];
                        $emailCC = get_setting('email_support');
                    } else {
                        $emailTo = get_setting('email_support');
                    }
                } else {
                     $emailTo = get_setting('email_bug_report');
                }

                $emailTitle = "Status warehouse receipt " . $warehouseReceipt['no_warehouse_receipt'] . ' is ' . $status;
                $emailTemplate = 'emails/basic';
                $emailData = [
                    'name' => $customer['name'],
                    'email' => $customer['email'],
                    'content' => 'Recently we review your WR that was created before. The result of WR validation no ' . $warehouseReceipt['no_warehouse_receipt'] . ' (Batch ref: ' . $warehouseReceipt['no_batch'] . ') is <b>' . $status . '</b>.<br><b>Note:</b> ' . $description
                ];

                $emailOptions = [
                    'cc' => isset($emailCC) ? $emailCC : null,
                ];

                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);

                if ($this->email->send()) {
                    flash($statusClass, "Warehouse receipt no {$warehouseReceipt['no_warehouse_receipt']} successfully {$status}");
                } else {
                    flash('warning', "Warehouse receipt no {$warehouseReceipt['no_warehouse_receipt']} successfully {$status}, but email sent email failed");
                }
            } else {
                flash('danger', 'Validating warehouse receipt failed');
            }
        }
        redirect('warehouse_receipt');
    }
}