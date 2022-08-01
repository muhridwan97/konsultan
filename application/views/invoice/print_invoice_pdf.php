<?php
if (isset($invoice['type']) && $invoice['type'] == 'BOOKING DEPO') {
    $this->load->view('invoice/print_invoice_depo_pdf');
} else {
    $this->load->view('invoice/print_invoice_tpp_pdf');
}