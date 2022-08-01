<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Booking_cif_invoice
 * @property BookingModel $booking
 * @property BookingCIFInvoiceModel $bookingCIFInvoice
 * @property BookingCIFInvoiceDetailModel $bookingCIFInvoiceDetail
 */
class Booking_cif_invoice extends MY_Controller
{
    /**
     * Booking_cif_invoice constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BookingCIFInvoiceModel', 'bookingCIFInvoice');
        $this->load->model('BookingCIFInvoiceDetailModel', 'bookingCIFInvoiceDetail');
        $this->load->model('BookingModel', 'booking');

        $this->setFilterMethods([
            'booking_cif_invoice_data' => 'GET',
            'ajax_get_available_booking' => 'GET',
            'ajax_get_booking_reference' => 'GET',
            'ajax_get_stock_booking' => 'GET',
        ]);
    }

    /**
     * Show booking invoice data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_CIF_INVOICE_VIEW);

        $this->render('booking_cif_invoice/index');
    }

    /**
     * Get ajax datatable booking control.
     */
    public function booking_cif_invoice_data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_CIF_INVOICE_VIEW);

        $filters = array_merge(get_url_param('filter') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);

        $data = $this->bookingCIFInvoice->getAll($filters);

        $this->render_json($data);
    }

    /**
     * Ajax get all booking data
     */
    public function ajax_get_available_booking()
    {
        $search = $this->input->get('q');
        $page = $this->input->get('page');

        $bookings = $this->bookingCIFInvoice->getAvailableBooking($search, $page);

        $this->render_json($bookings);
    }

    /**
     * Get booking inbound reference.
     */
    public function ajax_get_booking_reference()
    {
        $bookingInId = $this->input->get('id_booking');
        $booking = $this->booking->getBookingById($bookingInId);
        $cif = $this->bookingCIFInvoice->getBy(['booking_cif_invoices.id_booking' => $booking['id']], true);

        $this->render_json([
            'booking' => $booking,
            'cif' => $cif
        ]);
    }

    /**
     * Get stock booking inbound.
     */
    public function ajax_get_stock_booking()
    {
        $bookingId = $this->input->get('booking');

        $booking = $this->booking->getBookingById($bookingId);
        $goods = $this->bookingCIFInvoice->getAvailableGoodsStock([
            'booking' => $bookingId
        ]);

        $this->render_json([
            'booking' => $booking,
            'goods' => $goods
        ]);
    }

    /**
     * Create cif invoice.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_CIF_INVOICE_CREATE);

        $booking = $this->booking->getBookingById($this->input->post('booking'));
        $currencies = $this->bookingCIFInvoice->getCurrencies();

        $this->render('booking_cif_invoice/create', compact('booking', 'currencies'));
    }

    /**
     * Save CIF invoice
     */
    public function save()
    {
        if ($this->validate()) {
            $bookingId = $this->input->post('booking');
            $currencyFrom = $this->input->post('currency_from');
            $currencyTo = $this->input->post('currency_to');
            $exchangeDate = $this->input->post('exchange_date');
            $exchangeValue = $this->input->post('exchange_value');
            $ndpbm = $this->input->post('ndpbm');
            $importDuty = $this->input->post('import_duty');
            $vat = $this->input->post('vat');
            $incomeTax = $this->input->post('income_tax');
            $party = $this->input->post('party');
            $description = $this->input->post('description');
            $goods = $this->input->post('goods');
            $discount = $this->input->post('discount');
            $freight = $this->input->post('freight');
            $insurance = $this->input->post('insurance');
            $handling = $this->input->post('handling');
            $other = $this->input->post('other');

            $booking = $this->booking->getBookingById($bookingId);
            if ($booking['category'] == 'OUTBOUND') {
                $bookingCIFInbound = $this->bookingCIFInvoice->getBy(['booking_cif_invoices.id_booking' => $booking['id_booking']], true);
                $currencyFrom = $bookingCIFInbound['currency_from'];
                $currencyTo = $bookingCIFInbound['currency_to'];
                $exchangeDate = $bookingCIFInbound['exchange_date'];
                $exchangeValue = numerical($bookingCIFInbound['exchange_value']);
            }

            $this->db->trans_start();

            $this->bookingCIFInvoice->create([
                'id_booking' => $bookingId,
                'currency_from' => $currencyFrom,
                'currency_to' => $currencyTo,
                'exchange_value' => extract_number($exchangeValue),
                'exchange_date' => format_date($exchangeDate),
                'discount' => extract_number($discount) * -1,
                'freight' => extract_number($freight),
                'insurance' => extract_number($insurance),
                'handling' => extract_number($handling),
                'other' => extract_number($other),
                'ndpbm' => if_empty(extract_number($ndpbm), null),
                'import_duty' => if_empty(extract_number($importDuty), null),
                'vat' => if_empty(extract_number($vat), null),
                'income_tax' => if_empty(extract_number($incomeTax), null),
                'party' => $party,
                'description' => $description,
            ]);
            $bookingCIFInvoiceId = $this->db->insert_id();

            foreach ($goods as $item) {
                $this->bookingCIFInvoiceDetail->create([
                    'id_booking_cif_invoice' => $bookingCIFInvoiceId,
                    'id_booking_cif_invoice_detail' => if_empty($item['id_booking_cif_invoice_detail'], null),
                    'goods_name' => $item['goods_name'],
                    'quantity' => $item['quantity'],
                    'weight' => $item['weight'],
                    'gross_weight' => $item['gross_weight'],
                    'volume' => $item['volume'],
                    'price' => $item['price'],
                    'description' => $item['description'],
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', 'Booking CIF invoice ' . $booking['no_reference'] . ' successfully created', 'booking-cif-invoice');
            } else {
                flash('danger', 'Create booking CIF invoice failed');
            }
        }
        $this->create();
    }

    /**
     * Perform deleting cif invoice data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_CIF_INVOICE_DELETE);

        $cif = $this->bookingCIFInvoice->getById($id);

        if ($this->bookingCIFInvoice->delete($id, false)) {
            flash('warning', "CIF invoice {$cif['no_reference']} is successfully deleted");
        } else {
            flash('danger', "Delete invoice {$cif['no_reference']} failed");
        }
        redirect('booking-cif-invoice');
    }

    /**
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'booking' => 'trim|required|integer|is_natural_no_zero',
            'currency_from' => 'trim|max_length[3]',
            'currency_to' => 'trim|max_length[3]',
            'exchange_date' => 'trim|max_length[20]',
            'exchange_value' => 'trim|max_length[50]',
            'description' => 'trim|max_length[500]',
            'goods[]' => 'required',
        ];
    }
}