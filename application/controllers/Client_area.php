<?php

use Dompdf\Dompdf;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Client_area
 * @property InvoiceModel $invoice
 * @property InvoiceDetailModel $invoiceDetail
 * @property InvoiceCalculatorModel $invoiceCalculator
 * @property PeopleModel $people
 * @property ContainerModel $container
 * @property BookingModel $booking
 * @property BookingExtensionModel $bookingExtension
 * @property RawContactModel $rawContact
 * @property TrackerModel $trackerModel
 * @property NewsModel $news
 * @property Mailer $mailer
 */
class Client_area extends CI_Controller
{
    /**
     * Client_area constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('InvoiceModel', 'invoice');
        $this->load->model('InvoiceDetailModel', 'invoiceDetail');
        $this->load->model('InvoiceCalculatorModel', 'invoiceCalculator');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('ContainerModel', 'container');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingExtensionModel', 'bookingExtension');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('SafeConductContainerModel', 'safeConductContainer');
        $this->load->model('SafeConductGoodsModel', 'safeConductGoods');
        $this->load->model('HandlingContainerModel', 'handlingContainer');
        $this->load->model('HandlingGoodsModel', 'handlingGoods');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('WorkOrderContainerModel', 'workOrderContainer');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('TrackerModel', 'trackerModel');
        $this->load->model('PaymentModel', 'payment');
        $this->load->model('RawContactModel', 'rawContact');
        $this->load->model('NewsModel', 'news');
        $this->load->model('modules/Mailer', 'mailer');
    }

    /**
     * Show client area.
     */
    public function index()
    {
        $data = [
            'title' => "Client Area",
            'subtitle' => "Access your warehouse information",
            'page' => "client_area/index",
        ];
        $this->load->view('template/layout_public', $data);
    }

    /**
     * Show invoice tracker.
     */
    public function invoice()
    {
        $this->load->helper('cookie');

        // populate contact questioner
        $company = get_url_param('company');
        $address = get_url_param('address');
        $pic = get_url_param('pic');
        $contact = get_url_param('contact');
        $email = get_url_param('email');

        $bl = trim(get_url_param('bl'));
        $containerNo = trim(get_url_param('no_container'));

        $guessedData = $this->rawContact->getBy([
            'company' => $company,
            'contact' => $contact,
            'email' => $email
        ], true);

        if (empty($guessedData) && !empty($company)) {
            $contactData = [
                'company' => $company,
                'address' => $address,
                'pic' => $pic,
                'contact' => $contact,
                'email' => $email,
                'ip' => $this->input->ip_address(),
                'data' => json_encode([
                    'bl' => $bl,
                    'container_number' => $containerNo
                ])
            ];
            $this->rawContact->create($contactData);
            $contactId = $this->db->insert_id();

            $expireIn = 60 * 60 * 24 * 30;
            set_cookie('q_company', $company, $expireIn);
            set_cookie('q_address', $address, $expireIn);
            set_cookie('q_pic', $pic, $expireIn);
            set_cookie('q_contact', $contact, $expireIn);
            set_cookie('q_email', $email, $expireIn);

            $this->send_email_raw_contact($contactData);
        } else {
            $contactId = $guessedData['id'];
        }

        $container = $this->container->getBy(['ref_containers.no_container' => $containerNo], true);
        $bookings = [];
        if (!empty($bl)) {
            $bookings = $this->booking->getBookingsByExtensionField('NO_BL', $bl, empty($container['id']) ? null : $container['id']);
        }
        $invoices = [];
        if (!empty($containerNo)) {
            $invoices = $this->invoice->getInvoicesByBLNoContainer($containerNo, $bl);
        }

        foreach ($bookings as &$booking) {
            foreach ($invoices as $invoice) {
                if ($booking['no_booking'] == $invoice['no_reference'] && $invoice['status'] == 'PUBLISHED') {
                    //$booking['invoice'] = true;
                    break;
                }
            }
        }

        $data = [
            'title' => "TPP Cost Estimation",
            'subtitle' => "Find your invoice",
            'page' => "client_area/invoice",
            'container' => $container,
            'bookings' => $bookings,
            'invoices' => $invoices,
            'contactId' => $contactId
        ];
        $this->load->view('template/layout_public', $data);
    }

    /**
     * Send raw contact.
     * @param $contact
     */
    private function send_email_raw_contact($contact)
    {
        if (ENVIRONMENT == 'production') {
            $emailSupport = 'team_it@transcon-indonesia.com';
            $maintainer = 'fin@transcon-indonesia.com';
        } else {
            $emailSupport = 'it5@transcon-indonesia.com';
            $maintainer = '';
        }

        $contactDetail = '';
        foreach ($contact as $key => $value) {
            $contactDetail .= '<b>' . ucfirst($key) . '</b> : ' . $value . '<br>';
        }

        $emailTo = $emailSupport;
        $emailTitle = $contact['company'] . ' was submitted at ' . date('d F Y H:i');
        $emailTemplate = 'emails/basic';
        $emailData = [
            'title' => 'Customer Contact Gathering',
            'name' => 'TCI',
            'email' => $emailSupport,
            'content' => 'Recently we receive customer submitted data as follow:<br><br>' . $contactDetail . '<br>Please collect them in menu [Utility - Raw Contact] in TCI Warehouse v3.0'
        ];

        $emailOptions = [
            'cc' => $maintainer,
        ];

        $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
    }


    /**
     * Preview container with button save draft.
     * @param $bookingId
     */
    public function invoice_estimation_preview($bookingId)
    {
        $data = [
            'title' => "Invoice Preview",
            'subtitle' => "Draft invoice estimation",
            'bookingId' => $bookingId,
        ];

        $this->load->view('client_area/invoice_preview', $data);
    }

    /**
     * Print invoice estimation
     * @param $bookingId
     */
    public function invoice_estimation_print($bookingId)
    {
        $booking = $this->booking->getBookingById($bookingId);
        $customer = $this->people->getById($booking['id_customer']);
        $bookingExtensions = $this->bookingExtension->getBookingExtensionByBooking($booking['id']);

        $booking['inbound_date'] = $this->invoiceCalculator->getInboundDate($booking['id']);
        $booking['outbound_date'] = sql_date_format(get_url_param('outbound_date', 'now'), false);
        $invoiceDetails = $this->invoiceCalculator->getInvoiceBooking($booking);

        // populate item summary
        $itemSummary = '';
        $itemContainers = $this->invoiceCalculator->getBaseStockContainers($booking, true);
        $itemGoods = $this->invoiceCalculator->getBaseStockGoods($booking, true);
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

        $invoice = [
            'no_invoice' => 'ESTIMATION-XXX',
            'no_reference' => $booking['no_booking'],
            'no_reference_booking' => $booking['no_reference'],
            'status' => 'ESTIMATION',
            'inbound_date' => $booking['inbound_date'],
            'outbound_date' => $booking['outbound_date'],
            'item_summary' => $itemSummary,
            'created_at' => sql_date_format('now', false),
            'admin_name' => 'Published Only'
        ];

        $this->invoice->buildPartyLabel($invoice);
        $this->invoiceDetail->setInvoiceLayout($invoiceDetails);

        $data = [
            'title' => "Invoices",
            'subtitle' => "Print invoice",
            'page' => "invoice/print_invoice_pdf",
            'customer' => $customer,
            'booking' => $booking,
            'bookingExtensions' => $bookingExtensions,
            'invoice' => $invoice,
            'invoiceDetails' => $invoiceDetails,
        ];

        $report = $this->load->view('template/print_pdf', $data, true);
        $dompdf = new Dompdf(['isHtml5ParserEnabled' => true]);
        $dompdf->loadHtml($report);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();
        $dompdf->stream("invoice_estimation.pdf", array("Attachment" => false));
    }

    /**
     * Save draft invoice from preview.
     */
    public function save_draft_invoice()
    {

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $bookingId = $this->input->post('booking_id');
            $contactId = $this->input->post('contact_id');
            $outboundDate = $this->input->post('outbound_date');
            $bl = $this->input->post('bl');
            $noContainer = $this->input->post('no_container');

            $booking = $this->booking->getBookingById($bookingId);
            $booking['inbound_date'] = $this->invoiceCalculator->getInboundDate($booking['id']);
            $booking['outbound_date'] = sql_date_format($outboundDate, false);
            $invoiceDetails = $this->invoiceCalculator->getInvoiceBooking($booking);

            // populate item summary
            $itemSummary = '';
            $itemContainers = $this->invoiceCalculator->getBaseStockContainers($booking, true);
            $itemGoods = $this->invoiceCalculator->getBaseStockGoods($booking, true);
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
            $this->db->trans_start();
            // create invoice
            $noInvoice = $this->invoice->getAutoNumberInvoice('DRAFT');
            $this->invoice->createInvoice([
                'no_invoice' => $noInvoice,
                'no_reference' => $booking['no_booking'],
                'id_branch' => $booking['id_branch'],
                'id_customer' => $booking['id_customer'],
                'id_raw_contact' => if_empty($contactId, null),
                'type' => 'BOOKING FULL',
                'invoice_date' => sql_date_format('now'),
                'inbound_date' => $booking['inbound_date'],
                'outbound_date' => $booking['outbound_date'],
                'item_summary' => $itemSummary,
                'description' => '-',
                'status' => 'DRAFT',
                'created_by' => 1
            ]);
            $invoiceId = $this->db->insert_id();

            // adjust data for saving
            if (!empty($invoiceDetails)) {
                foreach ($invoiceDetails as &$invoiceDetail) {
                    $invoiceDetail['id_invoice'] = $invoiceId;
                    $invoiceDetail['created_by'] = 1;
                    if (key_exists('total', $invoiceDetail)) {
                        unset($invoiceDetail['total']);
                    }
                }
                $this->invoiceDetail->createInvoiceDetail($invoiceDetails);
            } else {
                show_error('Detail invoice is missing, no activity available to be charged.');
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                if ($booking['branch_type']=='TPP') {
                    $raw = $this->rawContact->getById($contactId);
                    $customer = [
                        'name' => if_empty($raw['company'], UserModel::authenticatedUserData('name'))];
                    $request = $booking;
                    
                    $request['no_invoice']=$noInvoice;
                    $mail_date = date("d F Y H:i:s");
                    $request['mail_date']=$mail_date;
                    // print_debug($request);
                    if ($this->send_email_request($customer, $request)) {
                        flash('success', "Draft Invoice <strong>{$noInvoice}</strong> successfully created");
                    }else{
                        flash('success', "Draft Invoice <strong>{$noInvoice}</strong> successfully created, but email not send to finance");
                    }
                } else {
                    flash('success', "Draft Invoice <strong>{$noInvoice}</strong> successfully created");
                }
                
                redirect("client_area/invoice?bl={$bl}&no_container={$noContainer}", false);
            } else {
                flash('danger', "Save draft invoice <strong>{$noInvoice}</strong> failed, try again or contact administrator");
            }

        } else {
            show_error('Unauthorized', 201);
        }
    }
    /**
     * Send email to administrator.
     * @param $operational
     * @param $workOrder
     * @param bool $isUpdate
     * @return bool
     */
    private function send_email_request($customer, $request, $isUpdate = false)
    {
        $operationsAdministratorEmail = get_setting('email_finance3');

        $subject =  $customer['name'] . ' request draft invoice ';

        $emailTo = $operationsAdministratorEmail;
        $emailTitle = $subject;
        $emailTemplate = 'emails/request_draft_invoice';
        $emailData = [
            'customer' => $customer,
            'request' => $request,
            'operationsAdministratorEmail' => $operationsAdministratorEmail
        ];

        $send = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
        if ($send) {
            return true;
        }
        return false;
    }

    /**
     * Print invoice
     * @param $id
     */
    public function invoice_print($id)
    {
        $invoice = $this->invoice->getInvoiceById($id);
        $invoiceDetails = $this->invoiceDetail->getInvoiceDetailByInvoice($id);
        $customer = $this->people->getById($invoice['id_customer']);

        $booking = $this->booking->getBookingByNo($invoice['no_reference']);
        $bookingExtensions = $this->bookingExtension->getBookingExtensionByBooking($booking['id']);

        $this->invoice->buildPartyLabel($invoice);
        $this->invoiceDetail->setInvoiceLayout($invoiceDetails);

        $data = [
            'title' => "Invoices",
            'subtitle' => "Print invoice",
            'page' => "invoice/print_invoice_pdf",
            'customer' => $customer,
            'invoice' => $invoice,
            'booking' => $booking,
            'bookingExtensions' => $bookingExtensions,
            'invoiceDetails' => $invoiceDetails,
        ];

        $report = $this->load->view('template/print_pdf', $data, true);
        $dompdf = new Dompdf(['isHtml5ParserEnabled' => true]);
        $dompdf->loadHtml($report);
        $dompdf->setPaper('letter', 'portrait');
        $dompdf->render();
        $dompdf->stream("invoice.pdf", array("Attachment" => false));
    }

    /**
     * Show invoice tracker.
     */
    public function container()
    {
        $containerNo = get_url_param('no_container');
        $container = $this->container->getBy(['ref_containers.no_container' => $containerNo], true);

        $bookings = [];
        if (!empty($container)) {
            $tppId = null;
            $bookings = $this->trackerModel->getBookingsByContainer($container['id'], 'INBOUND', $tppId);
            foreach ($bookings as &$booking) {
                // booking out and its jobs
                $outbounds = $this->booking->getBookingOutByBookingIn($booking['id']);
                foreach ($outbounds as &$outbound) {
                    $outbound['jobs'] = $this->trackerModel->getWorkOrdersByContainer($container['id'], $outbound['id']);
                }
                $booking['outbounds'] = $outbounds;

                // booking in jobs
                $workOrders = $this->trackerModel->getWorkOrdersByContainer($container['id'], $booking['id']);
                $booking['jobs'] = $workOrders;
            }
        }
        $container = $this->container->getById($container['id']);

        $data = [
            'title' => "Container Tracker",
            'subtitle' => "Find your container",
            'page' => "client_area/container",
            'container' => $container,
            'bookings' => $bookings,
        ];
        $this->load->view('template/layout_public', $data);
    }

    /**
     * Show news list.
     */
    public function news()
    {
        $news = $this->news->getByType([NewsModel::TYPE_PUBLIC]);

        $data = [
            'title' => "Public News",
            'subtitle' => "News public",
            'page' => "client_area/news",
            'news' => $news,
        ];
        $this->load->view('template/layout_public', $data);
    }

    /**
     * Show news list.
     * @param $id
     */
    public function news_view($id)
    {
        $news = $this->news->getById($id);

        $data = [
            'title' => "Public News",
            'subtitle' => "News public",
            'page' => "client_area/news_view",
            'news' => $news,
        ];
        $this->load->view('template/layout_public', $data);
    }

    /**
     * Show client area help.
     */
    public function help()
    {
        $data = [
            'title' => "Help & Support",
            'subtitle' => "Find help and information",
            'page' => "client_area/help",
        ];
        $this->load->view('template/layout_public', $data);
    }
}
