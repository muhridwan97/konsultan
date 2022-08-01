<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Invoice
 * @property InvoiceModel $invoice
 * @property InvoiceCalculatorModel $invoiceCalculator
 * @property InvoiceDetailModel $invoiceDetail
 * @property PaymentModel $payment
 * @property BookingModel $booking
 * @property BookingContainerModel $bookingContainer
 * @property BookingGoodsModel $bookingGoods
 * @property BookingExtensionModel $bookingExtension
 * @property DocumentTypeModel $documentType
 * @property UploadDocumentFileModel $uploadDocumentFile
 * @property SafeConductModel $safeConduct
 * @property HandlingModel $handling
 * @property HandlingContainerModel $handlingContainer
 * @property HandlingGoodsModel $handlingGoods
 * @property HandlingComponentModel $handlingComponent
 * @property HandlingTypeModel $handlingType
 * @property PeopleModel $people
 * @property ReportModel $report
 * @property WorkOrderModel $workOrder
 * @property WorkOrderContainerModel $workOrderContainer
 * @property WorkOrderGoodsModel $workOrderGoods
 * @property WorkOrderComponentModel $workOrderComponent
 */
class Invoice extends CI_Controller
{
    /**
     * Invoice constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('InvoiceModel', 'invoice');
        $this->load->model('InvoiceCalculatorModel', 'invoiceCalculator');
        $this->load->model('InvoiceDetailModel', 'invoiceDetail');
        $this->load->model('PaymentModel', 'payment');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('BookingExtensionModel', 'bookingExtension');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
        $this->load->model('SafeConductModel', 'safeConduct');
        $this->load->model('HandlingModel', 'handling');
        $this->load->model('HandlingContainerModel', 'handlingContainer');
        $this->load->model('HandlingGoodsModel', 'handlingGoods');
        $this->load->model('HandlingComponentModel', 'handlingComponent');
        $this->load->model('HandlingTypeModel', 'handlingType');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('ReportModel', 'report');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('WorkOrderContainerModel', 'workOrderContainer');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('WorkOrderComponentModel', 'workOrderComponent');
    }

    /**
     * Show branches data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_INVOICE_VIEW);

        $data = [
            'title' => "Invoices",
            'subtitle' => "Data invoice",
            'page' => "invoice/index"
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Get ajax datatable invoice.
     */
    public function invoice_data()
    {
        $startData = $this->input->get('start');
        $lengthData = $this->input->get('length');
        $searchQuery = $this->input->get('search')['value'];
        $orderColumn = $this->input->get('order')[0]['column'];
        $orderColumnOrder = $this->input->get('order')[0]['dir'];

        $reportInvoices = $this->invoice->getAllInvoices($startData, $lengthData, $searchQuery, $orderColumn, $orderColumnOrder);

        $no = $startData + 1;
        foreach ($reportInvoices['data'] as &$row) {
            $row['no'] = $no++;
            $obTpsPerforma = $this->payment->getPayments([
                'booking' => $row['id_booking'],
                'type' => 'OB TPS PERFORMA'
            ]);
            $row['is_performa'] = empty($obTpsPerforma) ? 0 : 1;
        }

        header('Content-Type: application/json');
        echo json_encode($reportInvoices);
    }

    /**
     * Show detail invoice.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_INVOICE_VIEW);

        $invoice = $this->invoice->getInvoiceById($id);
        $invoiceDetails = $this->invoiceDetail->getInvoiceDetailByInvoice($id);
        $data = [
            'title' => "Invoices",
            'subtitle' => "View invoice",
            'page' => "invoice/view",
            'invoice' => $invoice,
            'invoiceDetails' => $invoiceDetails,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show create invoice form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_INVOICE_CREATE);

        $data = [
            'title' => "Invoices",
            'subtitle' => "Create invoice",
            'page' => "invoice/create",
            'customers' => $this->people->getByType(PeopleModel::$TYPE_CUSTOMER),
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Save invoice data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_INVOICE_CREATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('type', 'Handling type', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('description', 'Invoice note', 'trim|max_length[500]');
            $this->form_validation->set_rules('customer', 'Customer', 'trim|required');
            $this->form_validation->set_rules('branch', 'Branch', 'trim|required');
            $this->form_validation->set_rules('status', 'Branch', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $invoiceStatus = $this->input->post('status');
                $invoiceType = $this->input->post('type');
                $customer = $this->input->post('customer');
                $branch = $this->input->post('branch');
                $description = $this->input->post('description');
                $bookingIn = $this->input->post('booking_in');
                $bookingInvoice = $this->input->post('booking_invoice');
                $handling = $this->input->post('handling');
                $workOrder = $this->input->post('work_order');
                $noReference = $this->input->post('no_reference');
                $inboundDate = sql_date_format('now', false);
                $outboundDate = sql_date_format($this->input->post('outbound_date'), false, sql_date_format('now', false));
                $invoiceDetails = $this->input->post('invoice_details');

                $invoiceDetailData = [];

                $itemSummary = '';
                $itemContainers = [];
                $itemGoods = [];

                $this->db->trans_start();

                switch ($invoiceType) {
                    case 'BOOKING FULL':
                    case 'BOOKING DEPO':
                        $booking = $this->booking->getBookingById($bookingIn);
                        $noReference = $booking['no_booking'];

                        if ($invoiceType == 'BOOKING DEPO') {
                            $handlingTypeIdOutbound = $this->handlingType->getHandlingTypesByMultiplier(-1);
                            $handlingOut = array_column(if_empty($handlingTypeIdOutbound, []), 'id');

                            $bookingOuts = $this->booking->getBookingOutByBookingIn($bookingIn);
                            $bookingIds = [$bookingIn];
                            foreach ($bookingOuts as $bookingOut) {
                                $bookingIds[] = $bookingOut['id'];
                            }

                            $workOrders = $this->workOrder->getWorkOrdersByHandlingType($handlingOut, $bookingIds);
                            $booking['outbound_date'] = empty($workOrders) ? sql_date_format('now', false) : sql_date_format($workOrders[0]['completed_at'], false);
                            $booking['inbound_date'] = $this->invoiceCalculator->getInboundDate($bookingIn, true);
                            $outboundDate = $booking['outbound_date'];
                        } else {
                            $booking['outbound_date'] = $outboundDate;
                            $booking['inbound_date'] = $this->invoiceCalculator->getInboundDate($bookingIn);
                        }

                        $itemContainers = $this->invoiceCalculator->getBaseStockContainers($booking, true, $invoiceType == 'BOOKING DEPO');
                        $itemGoods = $this->invoiceCalculator->getBaseStockGoods($booking, true, $invoiceType == 'BOOKING DEPO');

                        $inboundDate = $booking['inbound_date'];
                        break;
                    case 'BOOKING FULL EXTENSION':
                        $invoice = $this->invoice->getInvoiceById($bookingInvoice);
                        $booking = $this->booking->getBookingByNo($invoice['no_reference']);
                        $noReference = $booking['no_booking'];

                        $booking['inbound_date'] = $invoice['outbound_date'];
                        $booking['outbound_date'] = $outboundDate;

                        $itemContainers = $this->invoiceCalculator->getBaseStockContainers($booking, true);
                        $itemGoods = $this->invoiceCalculator->getBaseStockGoods($booking, true);

                        $inboundDate = $booking['inbound_date'];
                        break;
                    case 'HANDLING':
                        $handling = $this->handling->getHandlingById($handling);
                        $noReference = $handling['no_handling'];

                        $itemContainers = $this->handlingContainer->getHandlingContainersByHandling($handling['id']);
                        $itemGoods = $this->handlingGoods->getHandlingGoodsByHandling($handling['id'], true);
                        break;
                    case 'WORK ORDER':
                        $workOrder = $this->workOrder->getWorkOrderById($workOrder);
                        $noReference = $workOrder['no_work_order'];

                        $itemContainers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrder['id'], true);
                        $itemGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrder['id'], true, true);
                        break;
                    case 'CUSTOM':
                        $invoiceDetailData = [];
                        break;
                    default:
                        $invoiceDetailData = [];
                        break;
                }

                // populate additional invoice detail
                if (!empty($invoiceDetails) && key_exists('item_name', $invoiceDetails)) {
                    for ($i = 0; $i < count($invoiceDetails['item_name']); $i++) {
                        if (!empty($invoiceDetails['item_name'][$i])) {
                            $invoiceItem['item_name'] = $invoiceDetails['item_name'][$i];
                            $invoiceItem['unit'] = $invoiceDetails['unit'][$i];
                            $invoiceItem['quantity'] = $invoiceDetails['quantity'][$i];
                            $invoiceItem['unit_price'] = extract_number($invoiceDetails['unit_price'][$i]);
                            $invoiceItem['unit_multiplier'] = $invoiceDetails['unit_multiplier'][$i];
                            $invoiceItem['type'] = $invoiceDetails['type'][$i];
                            $invoiceItem['description'] = $invoiceDetails['description'][$i];
                            $invoiceItem['item_summary'] = $invoiceDetails['item_summary'][$i];
                            $invoiceDetailData[] = $invoiceItem;
                        }
                    }
                }

                // populate item summary
                foreach ($itemContainers as &$container) {
                    if (!empty($itemSummary)) {
                        $itemSummary .= ', ';
                    }
                    if (!key_exists('type', $container)) {
                        $container['type'] = $container['container_type'];
                    }
                    if (!key_exists('size', $container)) {
                        $container['size'] = $container['container_size'];
                    }
                    $itemSummary .= $container['no_container'] . ' (' . $container['type'] . '-' . $container['size'] . '-' . $container['status_danger'] . ')';
                }
                foreach ($itemGoods as $good) {
                    if (!empty($itemSummary)) {
                        $itemSummary .= ', ';
                    }
                    $itemSummary .= $good['goods_name'] . ' (' . numerical($good['quantity']) . $good['unit'] . '-' . numerical($good['tonnage']) . 'Kg-' . numerical($good['volume']) . 'M3-' . $good['status_danger'] . ')';
                }

                // create invoice
                $noInvoice = $this->invoice->getAutoNumberInvoice($invoiceStatus);
                $this->invoice->createInvoice([
                    'no_invoice' => $noInvoice,
                    'no_reference' => $noReference,
                    'id_branch' => $branch,
                    'id_customer' => $customer,
                    'type' => $invoiceType,
                    'invoice_date' => sql_date_format('now'),
                    'inbound_date' => $inboundDate,
                    'outbound_date' => $outboundDate,
                    'item_summary' => $itemSummary,
                    'description' => $description,
                    'status' => $invoiceStatus,
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);
                $invoiceId = $this->db->insert_id();

                // adjust data for saving
                if (!empty($invoiceDetailData)) {
                    foreach ($invoiceDetailData as &$invoiceDetail) {
                        $invoiceDetail['id_invoice'] = $invoiceId;
                        $invoiceDetail['created_by'] = UserModel::authenticatedUserData('id');
                        if (key_exists('total', $invoiceDetail)) {
                            unset($invoiceDetail['total']);
                        }
                    }
                    $this->invoiceDetail->createInvoiceDetail($invoiceDetailData);
                } else {
                    show_error('Detail invoice is missing, no activity available to be charged.');
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Invoice <strong>{$noInvoice}</strong> successfully created");
                    redirect("invoice");
                } else {
                    flash('danger', "Save invoice <strong>{$noInvoice}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->create();
    }

    /**
     * Print invoice data.
     * @param $id
     */
    public function print_invoice($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_INVOICE_PRINT);

        $invoice = $this->invoice->getInvoiceById($id);
        $invoiceDetails = $this->invoiceDetail->getInvoiceDetailByInvoice($id);
        $customer = $this->people->getById($invoice['id_customer']);

        $booking = $this->booking->getBookingByNo($invoice['no_reference']);
        $bookingExtensions = $this->bookingExtension->getBookingExtensionByBooking($booking['id']);

        $barcode = new \Milon\Barcode\DNS2D();
        $barcode->setStorPath(APPPATH . "/cache/");
        $invoiceBarcode = $barcode->getBarcodePNG($invoice['no_invoice'], "QRCODE", 4, 4);

        $data = [
            'title' => "Invoices",
            'subtitle' => "Print invoice",
            'page' => "invoice/print_invoice",
            'customer' => $customer,
            'invoice' => $invoice,
            'booking' => $booking,
            'bookingExtensions' => $bookingExtensions,
            'invoiceDetails' => $invoiceDetails,
            'invoiceBarcode' => $invoiceBarcode
        ];
        $this->load->view('template/print_invoice', $data);
    }

    /**
     * Print invoice PDF
     * @param $id
     */
    public function print_invoice_pdf($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_INVOICE_PRINT);

        $invoice = $this->invoice->getInvoiceById($id);
        $invoiceDetails = $this->invoiceDetail->getInvoiceDetailByInvoice($id);
        $customer = $this->people->getById($invoice['id_customer']);

        $booking = $this->booking->getBookingByNo($invoice['no_booking']);
        $bookingOuts = $this->booking->getBookingOutByBookingIn($booking['id']);
        $bookingExtensions = $this->bookingExtension->getBookingExtensionByBooking($booking['id']);
        $expeditions = [];
        foreach ($bookingOuts as $bookingOut) {
            $safeConducts = $this->safeConduct->getSafeConductsByBooking($bookingOut['id']);
            $expeditions = array_merge($expeditions, array_column(if_empty($safeConducts, []), 'expedition'));
        }

        foreach ($expeditions as $index => $expedition) {
            if ($expedition == 'NONE') {
                unset($expeditions[$index]);
            }
        }

        $this->invoice->buildPartyLabel($invoice);
        $this->invoiceDetail->setInvoiceLayout($invoiceDetails);

        $data = [
            'title' => "Invoices",
            'subtitle' => "Print invoice",
            'page' => "invoice/print_invoice_pdf",
            'customer' => $customer,
            'invoice' => $invoice,
            'booking' => $booking,
            'bookingOuts' => $bookingOuts,
            'expedition' => implode(', ', array_unique($expeditions)),
            'bookingExtensions' => $bookingExtensions,
            'invoiceDetails' => $invoiceDetails,
        ];

        $report = $this->load->view('template/print_pdf', $data, true);
        $dompdf = new \Dompdf\Dompdf(['isHtml5ParserEnabled' => true]);
        $dompdf->loadHtml($report);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();
        $dompdf->stream("invoice.pdf", array("Attachment" => false));
    }

    /**
     * Print receipt
     * @param $id
     */
    public function print_receipt($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_INVOICE_PRINT);

        $invoice = $this->invoice->getInvoiceById($id);
        $invoiceDetails = $this->invoiceDetail->getInvoiceDetailByInvoice($id);
        $customer = $this->people->getById($invoice['id_customer']);

        $booking = $this->booking->getBookingByNo($invoice['no_reference']);
        $bookingExtensions = $this->bookingExtension->getBookingExtensionByBooking($booking['id']);

        $data = [
            'title' => "Receipt",
            'subtitle' => "Print receipt",
            'page' => "invoice/print_receipt",
            'customer' => $customer,
            'invoice' => $invoice,
            'booking' => $booking,
            'bookingExtensions' => $bookingExtensions,
            'invoiceDetails' => $invoiceDetails,
        ];
        $this->load->view('template/print_invoice', $data);
    }

    /**
     * Perform publish invoice data.
     */
    public function publish()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_INVOICE_VALIDATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Invoice data', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $invoiceId = $this->input->post('id');

                $invoice = $this->invoice->getInvoiceById($invoiceId);
                $noInvoice = $this->invoice->getAutoNumberInvoice('PUBLISHED');
                $status = $this->invoice->updateInvoice([
                    'no_invoice' => $noInvoice,
                    'status' => 'PUBLISHED'
                ], $invoiceId);

                if ($status) {
                    flash('success', "Invoice <strong>{$invoice['no_invoice']}</strong> successfully published with new number {$noInvoice}");
                } else {
                    flash('danger', "Publish invoice <strong>{$invoice['no_invoice']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('invoice');
    }

    /**
     * Perform cancel invoice data.
     */
    public function cancel()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_INVOICE_VALIDATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Invoice data', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $invoiceId = $this->input->post('id');

                $invoice = $this->invoice->getInvoiceById($invoiceId);
                $delete = $this->invoice->updateInvoice([
                    'status' => 'CANCELED'
                ], $invoiceId);

                if ($delete) {
                    flash('success', "Invoice <strong>{$invoice['no_invoice']}</strong> successfully cancelled");
                } else {
                    flash('danger', "Publish invoice <strong>{$invoice['no_invoice']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('invoice');
    }

    /**
     * Perform deleting invoice data.
     */
    public function delete()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_INVOICE_DELETE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Invoice data', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $invoiceId = $this->input->post('id');

                $invoice = $this->invoice->getInvoiceById($invoiceId);
                $delete = $this->invoice->deleteInvoice($invoiceId);

                if ($delete) {
                    flash('warning', "Invoice <strong>{$invoice['no_invoice']}</strong> successfully deleted");
                } else {
                    flash('danger', "Delete invoice <strong>{$invoice['no_invoice']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('invoice');
    }


    /**
     * Upload attachment invoice data
     * @param $invoiceId
     */
    public function upload_faktur($invoiceId)
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Invoice data', 'trim|required');
            $this->form_validation->set_rules('no_faktur', 'Faktur number', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $noFaktur = $this->input->post('no_faktur');
                $invoice = $this->invoice->getInvoiceById($invoiceId);

                $fileName = '';
                $uploadPassed = true;
                if (!empty($_FILES['attachment_faktur']['name'])) {
                    $fileName = 'IF_' . time() . '_' . rand(100, 999);
                    $saveTo = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'invoice_faktur';
                    if ($this->documentType->makeFolder('invoice_faktur')) {
                        $upload = $this->uploadDocumentFile->uploadTo('attachment_faktur', $fileName, $saveTo);
                        if (!$upload['status']) {
                            $uploadPassed = false;
                            flash('warning', $upload['errors']);
                        } else {
                            $fileName = $upload['data']['file_name'];
                        }
                    } else {
                        $uploadPassed = false;
                        flash('warning', 'Making folder upload failed, try again');
                    }
                }

                if ($uploadPassed) {
                    $update = $this->invoice->updateInvoice([
                        'attachment_faktur' => $fileName,
                        'no_faktur' => $noFaktur,
                    ], $invoiceId);

                    if ($update) {
                        flash('success', "Invoice <strong>{$invoice['no_invoice']}</strong> successfully attached with faktur file");
                    } else {
                        flash('danger', "Upload attachment invoice <strong>{$invoice['no_invoice']}</strong> failed, try again or contact administrator");
                    }
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed, please wait until page loaded');
        }
        redirect('invoice');
    }

    /**
     * Update payment invoice
     * @param $invoiceId
     */
    public function payment($invoiceId)
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Invoice data', 'trim|required');
            $this->form_validation->set_rules('payment_date', 'Payment date', 'trim|required');
            $this->form_validation->set_rules('transfer_bank', 'Transfer bank', 'trim');
            $this->form_validation->set_rules('transfer_amount', 'Transfer amount', 'trim');
            $this->form_validation->set_rules('cash_amount', 'Cash amount', 'trim');
            $this->form_validation->set_rules('over_payment_amount', 'Over payment invoice', 'trim');
            $this->form_validation->set_rules('payment_description', 'Description', 'trim|max_length[500]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $paymentDate = $this->input->post('payment_date');
                $transferBank = $this->input->post('transfer_bank');
                $transferAmount = $this->input->post('transfer_amount');
                $cashAmount = $this->input->post('cash_amount');
                $overPaymentAmount = $this->input->post('over_payment_amount');
                $paymentDescription = $this->input->post('payment_description');

                $invoice = $this->invoice->getInvoiceById($invoiceId);

                $update = $this->invoice->updateInvoice([
                    'payment_date' => sql_date_format($paymentDate),
                    'transfer_bank' => $transferBank,
                    'transfer_amount' => extract_number($transferAmount),
                    'cash_amount' => extract_number($cashAmount),
                    'over_payment_amount' => extract_number($overPaymentAmount),
                    'payment_description' => $paymentDescription,
                ], $invoiceId);

                if ($update) {
                    flash('success', "Payment invoice <strong>{$invoice['no_invoice']}</strong> successfully updated");
                } else {
                    flash('danger', "Update payment invoice <strong>{$invoice['no_invoice']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed, please wait until page loaded');
        }
        redirect('invoice');
    }

    /**
     * Get booking data for creating invoice.
     */
    public function ajax_get_booking()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $customerId = $this->input->get('id_customer');
            $type = $this->input->get('type');
            $bookings = $this->booking->getBookingsByUnpublishedInvoice($customerId, $type);
            header('Content-Type: application/json');
            echo json_encode($bookings);
        }
    }

    /**
     * Get booking data for creating invoice extension.
     */
    public function ajax_get_booking_invoice()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $customerId = $this->input->get('id_customer');
            $type = $this->input->get('type');
            $types = explode(',', $type);
            $invoices = $this->invoice->getInvoicesByTypes($types, $customerId, 'PUBLISHED');
            header('Content-Type: application/json');
            echo json_encode($invoices);
        }
    }

    /**
     * Get handling data for creating invoice
     */
    public function ajax_get_handling()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $customerId = $this->input->get('id_customer');
            $handlings = $this->handling->getHandlingsByUnpublishedInvoice($customerId);
            header('Content-Type: application/json');
            echo json_encode($handlings);
        }
    }

    /**
     * Get work oder data for creating invoice.
     */
    public function ajax_get_work_order()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $customerId = $this->input->get('id_customer');
            $workOrders = $this->workOrder->getWorkOrdersByUnpublishedInvoice($customerId);
            header('Content-Type: application/json');
            echo json_encode($workOrders);
        }
    }

    /**
     * Get all payment related this booking depo mode.
     */
    public function ajax_get_charge_by_booking_depo()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $customerSetting = $this->input->get('setting');
            $bookingId = $this->input->get('id_booking');

            $jobs = $this->workOrder->getWorkOrdersByBooking($bookingId);
            if (!empty($jobs)) {
                $inboundType = get_setting('default_inbound_handling');
                $withoutSafeConduct = false;
                foreach ($jobs as $job) {
                    if ($job['id_handling_type'] == $inboundType) {
                        if (empty($job['id_safe_conduct'])) {
                            $withoutSafeConduct = true;
                        }
                    }
                }
                if ($withoutSafeConduct) {
                    echo "<div class='alert alert-danger'>Some inbound job has no safe conduct found!</div>";
                    return;
                }
            }

            $booking = $this->booking->getBookingById($bookingId);
            $bookingExtensions = $this->bookingExtension->getBookingExtensionByBooking($bookingId);
            $customer = $this->people->getById($booking['id_customer']);

            $handlingTypeIdOutbound = $this->handlingType->getHandlingTypesByMultiplier(-1);
            $handlingOut = array_column(if_empty($handlingTypeIdOutbound, []), 'id');

            $bookingOuts = $this->booking->getBookingOutByBookingIn($bookingId);
            $bookingIds = [$bookingId];
            foreach ($bookingOuts as $bookingOut) {
                $bookingIds[] = $bookingOut['id'];
            }

            $workOrders = $this->workOrder->getWorkOrdersByHandlingType($handlingOut, $bookingIds);
            $booking['outbound_date'] = empty($workOrders) ? sql_date_format('now', false) : sql_date_format($workOrders[0]['completed_at'], false);
            $booking['inbound_date'] = $this->invoiceCalculator->getInboundDate($bookingId, true);
            $invoiceDetails = $this->invoiceCalculator->getInvoiceBooking($booking, $customerSetting, false, true);

            $allOutboundDateIsset = true;
            $invalidData = '';
            foreach ($invoiceDetails as $invoiceDetail) {
                if ($invoiceDetail['type'] == 'STORAGE') {
                    $range = explode('-', $invoiceDetail['description']);
                    if (!isset($range[1]) || empty(trim($range[1]))) {
                        $allOutboundDateIsset = false;
                        $invalidData = $invoiceDetail['item_summary'];
                        break;
                    }
                }
            }

            if ($allOutboundDateIsset) {
                $infoMessages = $this->checkStatusInfo($bookingId);

                echo $this->load->view('invoice/_invoice_booking_in', [
                    'booking' => $booking,
                    'bookingExtensions' => $bookingExtensions,
                    'invoiceDetails' => $invoiceDetails,
                    'customer' => $customer,
                    'infoMessages' => $infoMessages,
                ], true);
            } else {
                echo "<div class='alert alert-danger'>Safe conduct is not checked in yet for item {$invalidData}</div>";
            }
        }
    }

    /**
     * Get all payment related this booking.
     */
    public function ajax_get_charge_by_booking_full()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $customerSetting = $this->input->get('setting');
            $bookingId = $this->input->get('id_booking');
            $outboundDate = sql_date_format($this->input->get('outbound_date'), false, sql_date_format('now', false));

            $booking = $this->booking->getBookingById($bookingId);
            $bookingExtensions = $this->bookingExtension->getBookingExtensionByBooking($bookingId);
            $customer = $this->people->getById($booking['id_customer']);

            $booking['outbound_date'] = $outboundDate;
            $booking['inbound_date'] = $this->invoiceCalculator->getInboundDate($bookingId);
            $invoiceDetails = $this->invoiceCalculator->getInvoiceBooking($booking, $customerSetting);

            $itemContainers = $this->invoiceCalculator->getBaseStockContainers($booking, true);
            $itemGoods = $this->invoiceCalculator->getBaseStockGoods($booking, true);

            $validationMessages = $this->checkStatusDanger($bookingId, $itemContainers, $itemGoods);
            $infoMessages = $this->checkStatusInfo($bookingId);

            echo $this->load->view('invoice/_invoice_booking_in', [
                'booking' => $booking,
                'bookingExtensions' => $bookingExtensions,
                'invoiceDetails' => $invoiceDetails,
                'customer' => $customer,
                'validationMessages' => $validationMessages,
                'infoMessages' => $infoMessages,
            ], true);
        }
    }

    /**
     * Get booking extension charges.
     */
    public function ajax_get_charge_by_booking_full_extension()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $customerSetting = $this->input->get('setting');
            $invoiceId = $this->input->get('id_invoice');
            $outboundDate = sql_date_format($this->input->get('outbound_date'), false, sql_date_format('now', false));

            $invoice = $this->invoice->getInvoiceById($invoiceId);

            $booking = $this->booking->getBookingByNo($invoice['no_reference']);
            $bookingExtensions = $this->bookingExtension->getBookingExtensionByBooking($booking['id']);
            $customer = $this->people->getById($booking['id_customer']);

            $booking['inbound_date'] = $invoice['outbound_date'];
            $booking['outbound_date'] = $outboundDate;
            $invoiceDetails = $this->invoiceCalculator->getInvoiceBooking($booking, $customerSetting, true);

            $itemContainers = $this->invoiceCalculator->getBaseStockContainers($booking, true);
            $itemGoods = $this->invoiceCalculator->getBaseStockGoods($booking, true);

            $validationMessages = $this->checkStatusDanger($booking['id'], $itemContainers, $itemGoods);
            $infoMessages = $this->checkStatusInfo($booking['id']);

            echo $this->load->view('invoice/_invoice_booking_in_extension', [
                'invoiceDetails' => $invoiceDetails,
                'booking' => $booking,
                'bookingExtensions' => $bookingExtensions,
                'customer' => $customer,
                'validationMessages' => $validationMessages,
                'infoMessages' => $infoMessages,
            ], true);
        }
    }

    /**
     * Get handling detail data and component
     */
    public function ajax_get_charge_by_handling()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $customerSetting = $this->input->get('setting');
            $handlingId = $this->input->get('id_handling');
            $handling = $this->handling->getHandlingById($handlingId);
            $handlingContainers = $this->handlingContainer->getHandlingContainersByHandling($handlingId);
            foreach ($handlingContainers as &$container) {
                $containerGoods = $this->handlingGoods->getHandlingGoodsByHandlingContainer($container['id']);
                $container['goods'] = $containerGoods;
            }
            $handlingGoods = $this->handlingGoods->getHandlingGoodsByHandling($handlingId, true);
            $handlingComponents = $this->handlingComponent->getHandlingComponentsByHandling($handlingId);

            $booking = $this->booking->getBookingById($handling['id_booking']);
            $invoiceDetails = $this->invoiceCalculator->getInvoiceHandling($booking, $handling, $customerSetting);

            $itemContainers = $this->handlingContainer->getHandlingContainersByHandling($handling['id']);
            $itemGoods = $this->handlingGoods->getHandlingGoodsByHandling($handling['id'], true);

            $validationMessages = $this->checkStatusDanger($handling['id_booking'], $itemContainers, $itemGoods);
            $infoMessages = $this->checkStatusInfo($booking['id']);

            if (isset($_SERVER['HTTP_ACCEPT']) && preg_match('/text\/html/', $_SERVER['HTTP_ACCEPT'])) {
                if (!empty($handling) && (!empty($handlingContainers) || !empty($handlingGoods))) {
                    echo $this->load->view('invoice/_invoice_handling', [
                        'handling' => $handling,
                        'handlingContainers' => $handlingContainers,
                        'handlingGoods' => $handlingGoods,
                        'handlingComponents' => $handlingComponents,
                        'invoiceDetails' => $invoiceDetails,
                        'validationMessages' => $validationMessages,
                        'infoMessages' => $infoMessages,
                    ], true);
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'handling' => $handling,
                    'handlingContainers' => $handlingContainers,
                    'handlingGoods' => $handlingGoods,
                    'handlingComponents' => $handlingComponents,
                    'invoiceDetails' => $invoiceDetails,
                    'validationMessages' => $validationMessages,
                    'infoMessages' => $infoMessages,
                ]);
            }
        }
    }

    /**
     * Get handling detail data and component
     */
    public function ajax_get_charge_by_work_order()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $customerSetting = $this->input->get('setting');
            $workOrderId = $this->input->get('id_work_order');
            $workOrder = $this->workOrder->getWorkOrderById($workOrderId);

            $containers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderId, true);
            foreach ($containers as &$container) {
                $containerGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($container['id']);
                $container['goods'] = $containerGoods;

                $containerContainers = $this->workOrderContainer->getWorkOrderContainersByWorkOrderContainer($container['id']);
                $container['containers'] = $containerContainers;
            }

            $goods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderId, true, true);
            foreach ($goods as &$item) {
                $goodsItem = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderGoods($item['id']);
                $item['goods'] = $goodsItem;
            }
            $components = $this->workOrderComponent->getWorkOrderComponentsByWorkOrder($workOrderId);

            $booking = $this->booking->getBookingById($workOrder['id_booking']);
            $invoiceDetails = $this->invoiceCalculator->getInvoiceWorkOrder($booking, $workOrder, $customerSetting);

            $itemContainers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrder['id'], true);
            $itemGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrder['id'], true, true);

            $validationMessages = $this->checkStatusDanger($workOrder['id_booking'], $itemContainers, $itemGoods);
            $infoMessages = $this->checkStatusInfo($booking['id']);

            if (isset($_SERVER['HTTP_ACCEPT']) && preg_match('/text\/html/', $_SERVER['HTTP_ACCEPT'])) {
                if (!empty($workOrder) && (!empty($containers) || !empty($goods))) {
                    echo $this->load->view('invoice/_invoice_work_order', [
                        'workOrder' => $workOrder,
                        'containers' => $containers,
                        'goods' => $goods,
                        'components' => $components,
                        'invoiceDetails' => $invoiceDetails,
                        'validationMessages' => $validationMessages,
                        'infoMessages' => $infoMessages,
                    ], true);
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'workOrder' => $workOrder,
                    'containers' => $containers,
                    'goods' => $goods,
                    'components' => $components,
                    'invoiceDetail' => $invoiceDetails,
                    'validationMessages' => $validationMessages,
                    'infoMessages' => $infoMessages,
                ]);
            }
        }
    }

    /**
     * Check status
     * @param $bookingId
     * @return array
     */
    public function checkStatusInfo($bookingId)
    {
        $infoMessages = [];
        $obTpsPerforma = $this->payment->getPayments([
            'booking' => $bookingId,
            'type' => 'OB TPS PERFORMA'
        ]);
        if (!empty($obTpsPerforma)) {
            $infoMessages[] = 'This booking contain OB TPS PERFORMA!';
        }

        return $infoMessages;
    }

    /**
     * Check status danger between booking and stock (booking stock, handling, work order)
     * @param $bookingId
     * @param $itemContainers
     * @param $itemGoods
     * @return array
     */
    private function checkStatusDanger($bookingId, $itemContainers = null, $itemGoods = null)
    {
        $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($bookingId);
        $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($bookingId);

        $validationMessages = [];

        if (empty($itemContainers) && empty($itemGoods)) {
            $validationMessages[] = 'Item containers or goods not found!';
        }

        foreach ($itemContainers as $stockContainer) {
            $containerFound = false;
            foreach ($bookingContainers as $bookingContainer) {
                if ($stockContainer['id_container'] == $bookingContainer['id_container']) {
                    $containerFound = true;
                    if ($stockContainer['status_danger'] != $bookingContainer['status_danger_payment']) {
                        $validationMessages[] = 'Container ' . $stockContainer['no_container'] . ' should have a <strong>' . if_empty($bookingContainer['status_danger_payment'], 'unknown') . '</strong> status (current status is ' . $stockContainer['status_danger'] . '), request handling Status Update';
                    }
                    break;
                }
            }
            if (!$containerFound) {
                $validationMessages[] = 'Container ' . $stockContainer['no_container'] . ' not found in booking (status unknown), contact Ops';
            }
        }

        foreach ($itemGoods as $stockItem) {
            $itemFound = false;
            foreach ($bookingGoods as $bookingItem) {
                if ($stockItem['id_goods'] == $bookingItem['id_goods']) {
                    $itemFound = true;
                    if ($stockItem['status_danger'] != $bookingItem['status_danger_payment']) {
                        $validationMessages[] = 'Item ' . $stockItem['goods_name'] . ' should have a <strong>' . if_empty($bookingItem['status_danger_payment'], 'unknown') . '</strong> status (current status is ' . $stockItem['status_danger'] . '), request handling Status Update';
                    }
                    break;
                }
            }
            if (!$itemFound) {
                $validationMessages[] = 'Item ' . $stockItem['goods_name'] . ' not found in booking (status unknown), contact Ops';
            }
        }
        return [];
        return $validationMessages;
    }
}