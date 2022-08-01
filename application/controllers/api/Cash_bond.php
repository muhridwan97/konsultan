<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Cash_bond
 * @property PaymentModel $payment
 */
class Cash_bond extends MY_Controller
{
    public function __construct()
    {
        parent::__construct();
        $this->load->model('PaymentModel', 'payment');

        $this->setFilterMethods([
            'payment' => 'GET'
        ]);
    }

    /**
     * Get payment data.
     */
    public function payment()
    {
        $filters = [
            'type' => get_url_param('type'),
            'non_performa' => get_url_param('non_performa', 1),
            'from_date' => get_url_param('from_date'),
            'to_date' => get_url_param('to_date'),
            'is_realized' => get_url_param('is_realized'),
        ];
        $payments = $this->payment->getPayments($filters);

        $this->render_json($payments);
    }
}