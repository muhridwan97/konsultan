<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Payment_check
 * @property UserModel $user
 * @property StatusHistoryModel $statusHistory
 * @property NotificationModel $notification
 * @property PaymentModel $payment
 * @property Exporter $exporter
 * @property Mailer $mailer
 */
class Payment_check extends MY_Controller
{
    /**
     * Payment_check constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel', 'user');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('NotificationModel', 'notification');
        $this->load->model('PaymentModel', 'payment');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('modules/Mailer', 'mailer');

        $this->setFilterMethods([
            'validate_payment' => 'GET',
            'payment_validate' => 'POST|PUT',
        ]);
    }

    /**
     * Check if token is valid.
     *
     * @param $requisitionId
     * @param $token
     * @return bool
     */
    private function checkToken($requisitionId, $token)
    {
        $statuses = $this->statusHistory->getBy([
            'status_histories.id_reference' => $requisitionId,
            'status_histories.type' => StatusHistoryModel::TYPE_BOOKING_PAYMENT,
            'status_histories.status' => PaymentModel::STATUS_ASK_APPROVAL
        ]);

        $tokenMatch = false;
        foreach ($statuses as $status) {
            $data = (array)json_decode($status['data']);
            if (key_exists('token', $data)) {
                if ($data['token'] == $token) {
                    $tokenMatch = true;
                }
            }
        }

        return $tokenMatch;
    }

    /**
     * Validate payment by top manager.
     *
     * @param $paymentId
     * @param $token
     */
    public function validate_payment($paymentId, $token)
    {
        $this->layout = 'template/payment_check';

        if ($this->checkToken($paymentId, $token)) {
            $payment = $this->payment->getById($paymentId);

            $this->render('payment/payment_check', compact('payment', 'token'));
        } else {
            show_error('Token selection is invalid');
        }
    }

    /**
     * Validate payment by top manager.
     *
     * @param $paymentId
     * @param $token
     */
    public function payment_validate($paymentId, $token)
    {
        if ($this->checkToken($paymentId, $token) || UserModel::isLoggedIn()) {
            $email = base64_decode($this->input->post('email'), UserModel::authenticatedUserData('email'));
            $description = if_empty($this->input->post('description'), $this->input->post('message'));
            $status = if_empty($this->input->post('status'), $this->input->get('status'));

            $payment = $this->payment->getById($paymentId);

            $this->db->trans_start();

            $this->payment->update([
                'status_check' => $status
            ], $payment['id']);

            $this->statusHistory->create([
                'type' => StatusHistoryModel::TYPE_BOOKING_PAYMENT,
                'id_reference' => $payment['id'],
                'status' => $status,
                'description' => if_empty($description, 'Validate payment'),
                'data' => json_encode([
                    'token' => $token,
                    'id_user' => UserModel::authenticatedUserData('id'),
                    'email' => if_empty(UserModel::authenticatedUserData('email'), $email)
                ])
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                $creator = $this->user->getById($payment['created_by']);
                $validators = ['acc_mgr@transcon-indonesia.com', 'fin2@transcon-indonesia.com', 'fin@transcon-indonesia.com'];

                $emailTo = $creator['email'];
                $emailTitle = "Payment check {$payment['no_payment']} ({$payment['payment_type']}) {$payment['customer_name']} with total Rp. " . numerical($payment['amount_request'], 2, true) . " is " . $status;
                $emailTemplate = 'emails/basic';
                $emailData = [
                    'name' => $creator['name'],
                    'email' => $creator['email'],
                    'content' => "
                        Payment for booking {$payment['no_reference']} with description {$payment['description']} 
                        with total Rp. " . numerical($payment['amount_request'], 2, true) . " is <b>{$status}</b> by owner of email {$email}.
                        <br><br>
                        Note: " . if_empty($description, 'no additional message')
                ];
                $emailOptions = [
                    'cc' => $validators
                ];

                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);

                flash('success', 'Payment ' . $payment['no_payment'] . 'successfully ' . $status, '_redirect', 'payment-check/validate-payment/' . $paymentId . '/' . $token . '?email=' . base64_encode($email) . '&autoclose=1');
            } else {
                flash('danger', 'Submit validation is failed, try again or contact administrator');
            }
        } else {
            show_error('Token selection is invalid, data maybe expired or you unauthorized to perform this action');
        }
    }
}
