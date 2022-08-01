<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Payment
 * @property PaymentModel $payment
 * @property PeopleModel $peopleModel
 * @property BookingModel $booking
 * @property ReportModel $report
 * @property DocumentTypeModel $documentType
 * @property UploadDocumentFileModel $uploadDocumentFile
 * @property BookingContainerModel $bookingContainer
 * @property BookingGoodsModel $bookingGoods
 * @property BookingAssignmentModel $bookingAssignment
 * @property BankAccountModel $bankAccount
 * @property StatusHistoryModel $statusHistory
 * @property BranchModel $branch
 * @property UploadModel $uploadModel
 * @property InvoiceModel $invoice
 * @property InvoiceDetailModel $invoiceDetail
 * @property Mailer $mailer
 * @property AllowanceModel $allowance
 * @property EmployeeModel $employee
 * @property ScheduleHolidayModel $scheduleHoliday
 * @property UploadDocumentModel $uploadDocument
 * @property UserModel $user
 * @property Uploader $uploader
 * @property Exporter $exporter
 */
class Payment extends CI_Controller
{
    /**
     * Payment constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BankAccountModel', 'bankAccount');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('BookingAssignmentModel', 'bookingAssignment');
        $this->load->model('BranchModel', 'branch');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('PaymentModel', 'payment');
        $this->load->model('PeopleModel', 'peopleModel'); 
        $this->load->model('LogModel', 'logHistory');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('NotificationModel', 'notification');
        $this->load->model('ReportModel', 'report');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('InvoiceModel', 'invoice');
        $this->load->model('InvoiceDetailModel', 'invoiceDetail');
        $this->load->model('UploadModel', 'uploadModel');
        $this->load->model('UploadDocumentModel', 'uploadDocument');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
        $this->load->model('UserModel', 'userModel');
        $this->load->model('AllowanceModel', 'allowance');
        $this->load->model('EmployeeModel', 'employee');
        $this->load->model('ScheduleHolidayModel', 'scheduleHoliday');
        $this->load->model('UserModel', 'user');
        $this->load->model('modules/Uploader', 'uploader');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show branches data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_VIEW);

        $filters = get_url_param('filter') ? $_GET : [];
        $selectedOwners = get_if_exist($filters, 'customers', [0]);
        $customers = $this->peopleModel->getBy(['ref_people.id' => $selectedOwners]);

        $allowCheck = AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_CHECK);
        $allowRealize = AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_REALIZE);
        $allowCreate = AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_CREATE);
        $isChecker = false;
        if ($allowCheck && (!$allowRealize || !$allowCreate) && !key_exists('status_checks', $_GET)) {
            $isChecker = true;
        }

        // check submission time (not holiday, sunday, with time range by work day regular or saturday)"
        $isSunday = date('D') == 'Sun';
        $isSaturday = date('D') == 'Sat';
        $fromTime = strtotime(date('Y-m-d 08:00:00'));
        $toTime = strtotime(date('Y-m-d ' . ($isSaturday ? '12:00:00' : '15:00:00')));
        $isHoliday = $isSunday || !empty($this->scheduleHoliday->getBy(['date' => date('Y-m-d')]));
        $isTimeSubmission = !$isHoliday && (time() >= $fromTime && time() <= $toTime);

        if (get_url_param('export')) {
            $allowValidate = AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_VALIDATE);
            if (!$allowRealize && !$allowValidate && !$allowCheck) {
                $filters['users'] = UserModel::authenticatedUserData('id');
            }
            if ($allowCheck && (!$allowRealize || !$allowCreate) && !key_exists('status_checks', $filters)) {
                $filters['status_checks'] = PaymentModel::STATUS_ASK_APPROVAL;
            }
            $this->exporter->exportLargeResourceFromArray("Payments", $this->payment->getAll($filters));
        } else {
            $data = [
                'title' => "Payments",
                'subtitle' => "Data payment",
                'page' => "payment/index",
                'isChecker' => $isChecker,
                'customers' => $customers,
                'isTimeSubmission' => $isTimeSubmission,
                'isHoliday' => $isHoliday,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable.
     */
    public function data()
    {
        $filters = array_merge(get_url_param('filter') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);

        $allowCheck = AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_CHECK);
        $allowRealize = AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_REALIZE);
        $allowValidate = AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_VALIDATE);
        $allowCreate = AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_CREATE);
        if (!$allowRealize && !$allowValidate && !$allowCheck) {
            $filters['user_pic'] = UserModel::authenticatedUserData('id');
        }
        if ($allowCheck && (!$allowRealize || !$allowCreate) && !key_exists('status_checks', $filters)) {
            $filters['status_checks'] = PaymentModel::STATUS_ASK_APPROVAL;
        }

        $payments = $this->payment->getAllPayments($filters);

        header('Content-Type: application/json');
        echo json_encode($payments);
    }

    /**
     * Show detail payment.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_VIEW);

        $payment = $this->payment->getPaymentById($id);
        $statusHistories = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_BOOKING_PAYMENT,
            'status_histories.id_reference' => $id
        ]);
        foreach ($statusHistories as &$statusHistory) {
            $statusHistory['data'] = json_decode($statusHistory['data'], true);
        }

        $data = [
            'title' => "Payments",
            'subtitle' => "View payment",
            'page' => "payment/view",
            'payment' => $payment,
            'statusHistories' => $statusHistories
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show create payment form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_CREATE);

        if (!AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_REALIZE) && !AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_PIC)) {
            // check if there are cash bond in pass before 5 days is not settled yet
            $lastCashBonds = $this->payment->getBy([
                '_id_branch' => get_active_branch('id'),
                'payments.user_pic' => UserModel::authenticatedUserData('id'),
                'payments.is_realized' => 0,
                'IFNULL(payments.approved_at, payments.created_at)<' => date('Y-m-d', strtotime('-5 day')),
                'IFNULL(payments.approved_at, payments.created_at)>=' => '2019-10-01',
                'payments.status!=' => PaymentModel::STATUS_REJECTED,
            ]);
            $totalNotRealized = count($lastCashBonds);

            if ($totalNotRealized > 0) {
                flash('warning', "You have {$totalNotRealized} cash bond not realized passed after 5 days, please settle to admin before make new one");
                redirect('payment');
            }
        }
        $pics = $this->user->getByPermission(PERMISSION_PAYMENT_CREATE);

        $data = [
            'title' => "Payments",
            'subtitle' => "Create payment",
            'page' => "payment/create",
            'bookings' => [],
            'uploads' => $this->uploadModel->getUploadsByBookingType([
                'with_do' => true
            ]),
            'pics' => $pics
        ];
        $this->load->view('template/layout', $data);
    }

     /**
     * Send a message to a new or existing chat.
     * 6281333377368-1557128212@g.us
     */
    public function send_message($text, $whatsapp_group_internal)
    {
        $data = [
            'url' => 'sendMessage',
            'method' => 'POST',
            'payload' => [
                'chatId' => detect_chat_id($whatsapp_group_internal),
                'body' => $text,
            ]
        ];

        $result = $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);

        return $result;
    }

    /**
     * Resend Notification.
     * @param $id
     */
    public function resend($id)
    {
        $payment = $this->payment->getPaymentById($id);
        $created_by = $this->userModel->getById($payment['created_by']);

        if($payment['status'] == PaymentModel::STATUS_APPROVED){
            //get approve by
            $approved_by = $this->userModel->getById($payment['approved_by']);

            //get upload data
            if(empty($payment['id_booking'])) {
                $uploadData = $this->uploadModel->getById($payment['id_upload']);                    
            } else {
                $uploadData = $this->uploadModel->getUploadsByBookingId($payment['id_booking']);                    
            }
            $documents = $this->uploadDocument->getDocumentsByUpload($uploadData['id']);

            //get booking data
            $bookingData = $this->booking->getBookingById($payment['id_booking']);
            $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($payment['id_booking']);
            $containerSize = array_count_values(array_column($bookingContainers, "size"));
            $containerType = implode(',', array_keys(array_count_values(array_column($bookingContainers, "type"))));
            $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($payment['id_booking'], true);

            //get data ATA
            $documentATA = array_filter($documents, function($doc) {
                return $doc['document_type'] == 'ATA';
            });

            if(!empty($documentATA)){
                foreach ($documentATA as $dataATA) {
                    $data_ata = $dataATA['document_date'];
                }
            }else{
                $data_ata = null;
            }

            //get data people
            $people = $this->peopleModel->getById($uploadData['id_person']);

            //get data partai
            $dataContainerSize = [];
            foreach($containerSize as $key => $conSize){
                $dataContainerSize[] = '('.$conSize.' x '.$key.')';
            }
            if(!empty($bookingContainers) && !empty($dataContainerSize)){
                $containerTypeFormat = !empty($containerType) ? '/'.$containerType : null;
                $partai = implode(',', $dataContainerSize).$containerTypeFormat;
            }else{
                if(!empty($bookingGoods)){
                    $partai = 'LCL';
                }else{
                    $partai = '-';
                }
            }

            $data_ata = !empty($data_ata) == true ? date('d F Y', strtotime($data_ata)) : '-';
            $withdrawal_date = !empty($payment['withdrawal_date']) == true ? date('d F Y H:i', strtotime($payment['withdrawal_date'])) : '-';
            $combinePaymentMethod = $payment['ask_payment'] == 'BANK' ? ($payment['ask_payment'].'/'.$payment['payment_method']) : $payment['ask_payment'];
            $accountData = $payment['ask_payment'] == 'BANK' ? '('.$payment['bank_name'].')/('.$payment['holder_name'].')/('.$payment['bank_account_number'].')' : '-';

            $submission = '*'.'Data Pengajuan '.$payment['no_payment'].' By '.if_empty($created_by['name'], '-').'*'."\n".'     *Telah disetujui oleh '.if_empty($approved_by['name'], '-')."*".' *(Resend Notif)*'."\n".
                    "\n".'*'.'1. Tanggal ATA ='.'* '. $data_ata.
                    "\n".'*'.'2. AJU/BCF ='.'* '.$uploadData['description'].
                    "\n".'*'.'3. Customer ='.'* '. $payment['customer_name'].
                    "\n".'*'.'4. Partai ='.'* '.if_empty($partai, '-').
                    "\n".'*'.'5. Tanggal Penarikan ='.'* '. $withdrawal_date.
                    "\n".'*'.'6. Metode Pembayaran ='.'* '.$combinePaymentMethod.
                    "\n".'*'.'7. Nomor Rekening ='.'* '.$accountData.
                    "\n".'*'.'8. Nominal Pengajuan ='.'* '.if_empty('Rp. '.number_format($payment['amount_request']), '-').
                    "\n".'*'.'9. Deskripsi ='.'* '.if_empty($payment['description'], '-').
                    "\n".'*'.'10. Tanggal Pengajuan ='.'* '.format_date($payment['created_at'], 'd F Y H:i').
                    "\n".'*'.'11. PIC ='.'* '.$payment['pic_name'];

            if($payment['ask_payment'] == 'BANK'){
                if(!$payment['is_realized']){
                    $sendsubmission = null ;
                    $whatsapp_group_submission = get_setting('whatsapp_group_submission'); //group pengajuan
                    if(!empty($whatsapp_group_submission) && !is_null($whatsapp_group_submission)){
                        $sendsubmission = $this->send_message($submission, $whatsapp_group_submission);
                    }

                    $sendtransfer = null;
                    $whatsapp_group_transfer = get_setting('whatsapp_group_transfer'); //group transfer
                    if(!empty($whatsapp_group_transfer) && !is_null($whatsapp_group_transfer)){
                        $sendtransfer = $this->send_message($submission, $whatsapp_group_transfer);
                    }

                    if( (!empty($sendsubmission['sent']) && $sendsubmission['sent'] == true) && (!empty($sendtransfer['sent']) && $sendtransfer['sent'] == true) ){
                        flash('success', "Resend notif payment <strong>{$payment['no_payment']}</strong> to whatsapp group submission and transfer successfully");
                    }else{
                        if( (!empty($sendsubmission['sent']) && $sendsubmission['sent'] == true) && empty($sendtransfer['sent']) ){
                            flash('warning', "Resend notif payment <strong>{$payment['no_payment']}</strong> to whatsapp group submission successfully, but notif payment to whatsapp group transfer failed");
                        }elseif( (!empty($sendtransfer['sent']) && $sendtransfer['sent'] == true) && empty($sendsubmission['sent']) ){
                            flash('warning', "Resend notif payment <strong>{$payment['no_payment']}</strong> to whatsapp group transfer successfully, but notif payment to whatsapp group submission failed");
                        }else{
                            flash('danger', "Resend notif payment <strong>{$payment['no_payment']}</strong> failed, please try again");
                        }
                    }
                }else{
                    flash('danger', "Resend notif payment <strong>{$payment['no_payment']}</strong> failed, payment is realized");
                }
            }else{
                $whatsapp_group_submission = get_setting('whatsapp_group_submission'); //group pengajuan
                if(!empty($whatsapp_group_submission) && !is_null($whatsapp_group_submission) && !$payment['is_realized']){
                    $send = $this->send_message($submission, $whatsapp_group_submission);
                    if(!empty($send['sent']) && $send['sent'] == true){
                        flash('success', "Resend notif payment <strong>{$payment['no_payment']}</strong> successfully");
                    }else{
                        flash('danger', "Resend notif payment <strong>{$payment['no_payment']}</strong> failed, please try again.");
                    }
                }else{
                    flash('danger', "Resend notif payment <strong>{$payment['no_payment']}</strong> failed, please set whatsapp group number or payment is realized");
                }
            }
        }else{

            // get upload data
            if(empty($payment['id_booking'])) {
                $uploadData = $this->uploadModel->getById($payment['id_upload']);                    
            } else {
                $uploadData = $this->uploadModel->getUploadsByBookingId($payment['id_booking']);                    
            }
            $documents = $this->uploadDocument->getDocumentsByUpload($uploadData['id']);

            //get data ATA
            $documentATA = array_filter($documents, function($doc) {
                return $doc['document_type'] == 'ATA';
            });

            if(!empty($documentATA)){
                foreach ($documentATA as $dataATA) {
                    $data_ata = $dataATA['document_date'];
                }
            }else{
                $data_ata = null;
            }

            //get booking data
            $bookingData = $this->booking->getBookingById($uploadData['id_booking']);
            $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($bookingData['id']);
            $containerSize = array_count_values(array_column($bookingContainers, "size"));
            $containerType = implode(',', array_keys(array_count_values(array_column($bookingContainers, "type"))));
            $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($payment['id_booking'], true);

            //get data people
            $people = $this->peopleModel->getById($uploadData['id_person']);

            //get data container size and container type (partai)
            $dataContainerSize = [];
            foreach($containerSize as $key => $conSize){
                $dataContainerSize[] = '('.$conSize.' x '.$key.')';
            }
            if(!empty($bookingContainers) && !empty($dataContainerSize)){
                $containerTypeFormat = !empty($containerType) ? '/'.$containerType : null;
                $partai = implode(',', $dataContainerSize).$containerTypeFormat;
            }else{
                if(!empty($bookingGoods)){
                    $partai = 'LCL';
                }else{
                    $partai = '-';
                }
            }

            //get withdrawal date, payment method, bank data
            $data_ata = !empty($data_ata) == true ? date('d F Y', strtotime($data_ata)) : '-';
            $withdrawalFormat = !empty($payment['withdrawal_date']) ? date('d F Y H:i', strtotime($payment['withdrawal_date'])) : '-';
            $combinePaymentMethod = $payment['ask_payment'] == 'BANK' ? ($payment['ask_payment'].'/'.$payment['payment_method']) : $payment['ask_payment'];
            $accountData = $payment['ask_payment'] == 'BANK' ? '('.$payment['holder_name'].')/('.$payment['bank_name'].')/('.$payment['bank_account_number'].')' : '-';


            //whatsapp format
            $submission = '*'.'Data Pengajuan '.$payment['no_payment'].' By '.if_empty($created_by['name'], '-').'*'."\n".'*(Resend notif)*'."\n".
            "\n".'*'.'1. Tanggal ATA ='.'* '. $data_ata.
            "\n".'*'.'2. AJU/BCF ='.'* '.$uploadData['description'].
            "\n".'*'.'3. Customer ='.'* '. $people['name'].
            "\n".'*'.'4. Partai ='.'* '.if_empty($partai, '-').
            "\n".'*'.'5. Tanggal Penarikan ='.'* '.$withdrawalFormat .
            "\n".'*'.'6. Metode Pembayaran ='.'* '.$combinePaymentMethod.  
            "\n".'*'.'7. Nomor Rekening ='.'* '.$accountData.
            "\n".'*'.'8. Nominal Pengajuan ='.'* '.if_empty('Rp. '.number_format($payment['amount_request']), '-').
            "\n".'*'.'9. Deskripsi ='.'* '.if_empty($payment['description'], '-').
            "\n".'*'.'10. Tanggal Pengajuan ='.'* '.format_date($payment['created_at'], 'd F Y H:i').
            "\n".'*'.'11. PIC ='.'* '.$payment['pic_name'];

            //send whatsapp notif
            $whatsapp_group = get_setting('whatsapp_group_submission'); // group pengajuan
            if(!empty($whatsapp_group) && !is_null($whatsapp_group) && empty($payment['is_realized'])){
                $send = $this->send_message($submission, $whatsapp_group);
                if(!empty($send['sent']) && $send['sent'] == true){
                    flash('success', "Resend notif payment <strong>{$payment['no_payment']}</strong> successfully");
                }else{
                    flash('danger', "Resend notif payment <strong>{$payment['no_payment']}</strong> failed, please try again.");
                }
            }else{
                flash('danger', "Resend notif payment <strong>{$payment['no_payment']}</strong> failed, please set whatsapp group number or payment is realized");
            }
        }

        redirect("payment");
    }

    /**
     * Save new payment.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_CREATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('payment_category', 'Payment category', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('payment_type', 'Payment type', 'trim|required|max_length[50]');
            if (empty($this->input->post('paid_by_customer'))) {
                $this->form_validation->set_rules('ask_payment', 'Ask Payment', 'trim|required');
                $this->form_validation->set_rules('payment_date', 'Payment date', 'trim|max_length[50]');
                $this->form_validation->set_rules('amount', 'Amount', 'trim|required|max_length[50]');
                $this->form_validation->set_rules('settlement_date', 'Settlement', 'trim|required|max_length[50]');
                $this->form_validation->set_rules('no_invoice', 'No Invoice', 'trim|max_length[50]');
            }
            $this->form_validation->set_rules('description', 'Payment description', 'trim|required|max_length[500]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $booking = $this->input->post('booking');
                $upload = $this->input->post('upload');
                $paymentCategory = $this->input->post('payment_category');
                $paymentType = $this->input->post('payment_type');
                $paymentDate = sql_date_format($this->input->post('payment_date'));
                $askPayment = $this->input->post('ask_payment');
                $paymentMethod = $this->input->post('payment_method');
                $paidByCustomer = $this->input->post('paid_by_customer');
                $bankName = $this->input->post('bank_name');
                $holderName = $this->input->post('account_holder_name');
                $accountNumber = $this->input->post('account_number');
                $withdrawalDate = $this->input->post('withdrawal_date');
                $withdrawalTime = $this->input->post('withdrawal_time');
                $amount = extract_number($this->input->post('amount'));
                $noInvoice = $this->input->post('no_invoice');
                $settlementDate = sql_date_format($this->input->post('settlement_date'));
                $description = $this->input->post('description');
                $includeRealization = $this->input->post('include_realization');
                $withdrawalDateTime = sql_date_format($withdrawalDate." ".$withdrawalTime);
                $pic = $this->input->post('pic');
                
                // get upload data
                if (empty($booking)) {
                    $uploadData = $this->uploadModel->getById($upload);
                } else {
                    $uploadData = $this->uploadModel->getUploadsByBookingId($booking);
                }
                $documents = $this->uploadDocument->getDocumentsByUpload($uploadData['id']);

                // Check document ATA booking NON DO, ETA for booking with DO
                if (in_array("ATA", array_column($documents, 'document_type')) == false && $paymentType != "DO" && false) {
                    flash('warning', 'Please upload ATA document (Booking without DO)!');
                } elseif (in_array("ETA", array_column($documents, 'document_type')) == false && $paymentType == "DO") {
                    flash('warning', 'Please upload ETA document (Booking with DO)!');
                } else {
                    // upload attachment if exist
                    $fileName = '';
                    $uploadPassed = true;
                    if (!empty($_FILES['attachment']['name'])) {
                        $fileName = 'TR_' . time() . '_' . rand(100, 999);
                        $saveTo = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'payments';
                        if ($this->documentType->makeFolder('payments')) {
                            $uploadedFile = $this->uploadDocumentFile->uploadTo('attachment', $fileName, $saveTo);
                            if (!$uploadedFile['status']) {
                                $uploadPassed = false;
                                flash('warning', $uploadedFile['errors']);
                            } else {
                                $fileName = $uploadedFile['data']['file_name'];
                            }
                        } else {
                            $uploadPassed = false;
                            flash('warning', 'Making folder upload failed, try again');
                        }
                    }

                    if ($uploadPassed) {
                        $noPayment = $this->payment->getAutoNumberPayment();
                        $data = [
                            'no_payment' => $noPayment,
                            'id_booking' => $booking,
                            'id_upload' => if_empty($upload, null),
                            'no_invoice' => $noInvoice,
                            'payment_category' => $paymentCategory,
                            'payment_type' => $paymentType,
                            'ask_payment' => $askPayment,
                            'payment_method' => $paymentMethod,
                            'holder_name' => $holderName,
                            'bank_name' => $bankName,
                            'bank_account_number' => $accountNumber,
                            'withdrawal_date' => $withdrawalDateTime,
                            'amount_request' => $amount,
                            'settlement_date' => $settlementDate,
                            'status' => PaymentModel::STATUS_DRAFT,
                            'attachment' => $fileName,
                            'description' => $description,
                            'created_by' => UserModel::authenticatedUserData('id'),
                            'user_pic' => if_empty($pic, UserModel::authenticatedUserData('id')),
                        ];

                        if ($includeRealization || $paidByCustomer) {
                            $data['status'] = PaymentModel::STATUS_APPROVED;
                            $data['amount'] = 0;
                            $data['approved_at'] = date('Y-m-d H:i:s');
                            $data['approved_by'] = UserModel::authenticatedUserData('id');
                            $data['is_submitted'] = true;
                            $data['submitted_at'] = date('Y-m-d H:i:s');
                            $data['submitted_by'] = UserModel::authenticatedUserData('id');
                        }

                        if ($includeRealization) {
                            $data['amount'] = $amount;
                            $data['is_realized'] = 1;
                            $data['realized_at'] = date('Y-m-d H:i:s');
                            $data['realized_by'] = UserModel::authenticatedUserData('id');
                            $data['payment_date'] = $paymentDate;
                        }

                        $this->db->trans_start();

                        $this->payment->createPayment($data);
                        $paymentId = $this->db->insert_id();

                        $this->statusHistory->create([
                            'type' => StatusHistoryModel::TYPE_BOOKING_PAYMENT,
                            'id_reference' => $paymentId,
                            'status' => PaymentModel::STATUS_DRAFT,
                            'description' => 'Payment created',
                        ]);

                        if ($includeRealization) {
                            $this->statusHistory->create([
                                'type' => StatusHistoryModel::TYPE_BOOKING_PAYMENT,
                                'id_reference' => $paymentId,
                                'status' => PaymentModel::STATUS_SUBMITTED,
                                'description' => 'Auto submit payment',
                            ]);
                        }

                        $this->db->trans_complete();

                        if ($this->db->trans_status()) {
                            $payment = $this->payment->getById($paymentId);

                            //get booking data
                            $bookingData = $this->booking->getBookingById($uploadData['id_booking']);
                            $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($bookingData['id']);
                            $containerSize = array_count_values(array_column($bookingContainers, "size"));
                            $containerType = implode(',', array_keys(array_count_values(array_column($bookingContainers, "type"))));
                            $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($booking, true);

                            // get data ATA
                            $documentATA = array_filter($documents, function($doc) {
                                return $doc['document_type'] == 'ATA';
                            });

                            $dataAta = null;
                            if (!empty($documentATA)) {
                                foreach ($documentATA as $dataATA) {
                                    $dataAta = $dataATA['document_date'];
                                }
                            }

                            // get data people
                            $people = $this->peopleModel->getById($uploadData['id_person']);

                            // get data container size and container type (party)
                            $dataContainerSize = [];
                            foreach ($containerSize as $key => $conSize) {
                                $dataContainerSize[] = '(' . $conSize . ' x ' . $key . ')';
                            }
                            if (!empty($bookingContainers) && !empty($dataContainerSize)) {
                                $containerTypeFormat = !empty($containerType) ? '/' . $containerType : null;
                                $party = implode(',', $dataContainerSize) . $containerTypeFormat;
                            } else {
                                $party = empty($bookingGoods) ? '-' : 'LCL';
                            }

                            // get withdrawal date, payment method, bank data
                            $dataAta = !empty($dataAta) == true ? date('d F Y', strtotime($dataAta)) : '-';
                            $withdrawalFormat = !empty($withdrawalDateTime) ? date('d F Y H:i', strtotime($withdrawalDateTime)) : '-';
                            $combinePaymentMethod = $askPayment == 'BANK' ? ($askPayment.'/'.$paymentMethod) : $askPayment;
                            $accountData = $askPayment == 'BANK' ? '('.$holderName.')/('.$bankName.')/('.$accountNumber.')' : '-';

                            // whatsapp format
                            $submission = '*'.'Data Pengajuan '.$noPayment.' By '.if_empty(UserModel::authenticatedUserData('name'), '-').'*'."\n".
                                "\n".'*'.'1. Tanggal ATA ='.'* '. $dataAta.
                                "\n".'*'.'2. AJU/BCF ='.'* '.$uploadData['description'].
                                "\n".'*'.'3. Customer ='.'* '. $people['name'].
                                "\n".'*'.'4. Partai ='.'* '.if_empty($party, '-').
                                "\n".'*'.'5. Tanggal Penarikan ='.'* '.$withdrawalFormat .
                                "\n".'*'.'6. Metode Pembayaran ='.'* '.$combinePaymentMethod.
                                "\n".'*'.'7. Nomor Rekening ='.'* '.$accountData.
                                "\n".'*'.'8. Nominal Pengajuan ='.'* '.if_empty('Rp. '.number_format($amount), '-').
                                "\n".'*'.'9. Deskripsi ='.'* '.if_empty($description, '-').
                                "\n".'*'.'10. Tanggal Pengajuan ='.'* '.format_date($payment['created_at'], 'd F Y H:i').
                                "\n".'*'.'11. PIC ='.'* '.$payment['pic_name'];

                            // send whatsapp
                            $whatsapp_group = get_setting('whatsapp_group_submission');
                            if (!empty($whatsapp_group) && empty($includeRealization) && $paymentType != PaymentModel::TYPE_OB_TPS_PERFORMA) {
                                $this->send_message($submission, $whatsapp_group);
                            }
                            flash('success', "Payment <strong>{$paymentType}</strong> successfully created");

                            redirect("payment");
                        } else {
                            flash('danger', "Save payment <strong>{$paymentType}</strong> failed, try again or contact administrator");
                        }
                    }
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->create();
    }

    /**
     * Show edit payment form.
     * @param $id
     */
    public function realization($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_REALIZE);

        $payment = $this->payment->getPaymentById($id);
        $lastPaymentBillings = $this->payment->getPaymentsByBooking($payment['id_booking'], PaymentModel::PAYMENT_BILLING);
        $lastPaymentNonBillings = $this->payment->getPaymentsByBooking($payment['id_booking'], PaymentModel::PAYMENT_NON_BILLING);
        $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($payment['id_booking']);
        $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($payment['id_booking']);
        $data = [
            'title' => "Payments",
            'subtitle' => "Payment Realization",
            'page' => "payment/realization",
            'payment' => $payment,
            'bookings' => $this->report->getAvailableStockBookingList('all', ['transaction_exist' => false]),
            'bookingContainers' => $bookingContainers,
            'bookingGoods' => $bookingGoods,
            'lastPayment' => $this->load->view('payment/_transaction', [
                'lastPaymentBillings' => $lastPaymentBillings,
                'lastPaymentNonBillings' => $lastPaymentNonBillings,
                'currentPaymentId' => $payment['id']
            ], true)
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show submit payment.
     *
     * @param $id
     */
    public function tps_realization($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_REALIZE);

        $payment = $this->payment->getPaymentById($id);
        $data = [
            'title' => "Payments",
            'subtitle' => "Payment Submission",
            'page' => "payment/tps_realization",
            'payment' => $payment,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Update tps realization payment by id.
     *
     * @param $id
     */
    public function update_tps_realization($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_REALIZE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('tps_invoice_payment_date', 'Payment date', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('tps_invoice_bank_name', 'Bank name', 'trim|required|max_length[50]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $paymentDate = $this->input->post('tps_invoice_payment_date');
                $bankName = $this->input->post('tps_invoice_bank_name');

                $payment = $this->payment->getPaymentById($id);

                $uploadedFile = $payment['tps_invoice_attachment'];
                $uploadFile = true;
                if (!empty($_FILES['tps_invoice_attachment']['name'])) {
                    $uploadFile = $this->uploader->setDriver('s3')->uploadTo('tps_invoice_attachment', [
                        'destination' => 'payment-tps/' . date('Y/m')
                    ]);
                    if ($uploadFile) {
                        $uploadedData = $this->uploader->getUploadedData();
                        $uploadedFile = $uploadedData['uploaded_path'];
                        if (!empty($payment['tps_invoice_attachment'])) {
                            $this->uploader->setDriver('s3')->delete($payment['tps_invoice_attachment']);
                        }
                    } else {
                        flash('danger', $this->uploader->getDisplayErrors());
                    }
                }
                if ($uploadFile) {

                    $update = $this->payment->update([
                        'tps_invoice_payment_date' => format_date($paymentDate),
                        'tps_invoice_bank_name' => $bankName,
                        'tps_invoice_attachment' => if_empty($uploadedFile, null),
                    ], $id);

                    if ($update) {
                        flash('success', "TPS invoice payment {$payment['no_payment']} updated", 'payment');
                    } else {
                        flash('danger', "Update TPS invoice payment {$payment['no_payment']} failed");
                    }
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->tps_realization($id);
    }

    /**
     * Show edit payment form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_EDIT);

        $payment = $this->payment->getPaymentById($id);

        $lastPaymentBillings = $this->payment->getPaymentsByBooking($payment['id_booking'], PaymentModel::PAYMENT_BILLING);
        $lastPaymentNonBillings = $this->payment->getPaymentsByBooking($payment['id_booking'], PaymentModel::PAYMENT_NON_BILLING);

        $pics = $this->user->getByPermission(PERMISSION_PAYMENT_CREATE);
        $data = [
            'title' => "Payments",
            'subtitle' => "Edit payment",
            'page' => "payment/edit",
            'payment' => $payment,
            'booking' => $this->booking->getBookingById($payment['id_booking']),
            'uploads' => $this->uploadModel->getUploadsByBookingType([
                'with_do' => true
            ]),
            'lastPayment' => $this->load->view('payment/_transaction', [
                'lastPaymentBillings' => $lastPaymentBillings,
                'lastPaymentNonBillings' => $lastPaymentNonBillings,
                'currentPaymentId' => $payment['id']
            ], true),
            'pics' => $pics,
        ];

        $this->load->view('template/layout', $data);
    }

    /**
     * Show detail payment.
     * @param $id
     */
    public function set_bank($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_REALIZE);

        $payment = $this->payment->getPaymentById($id);
        $bankAccounts = $this->bankAccount->getBy(
            ['bank_type' => [BankAccountModel::TYPE_REGULAR, BankAccountModel::TYPE_PETTY_CASH]
            ]);
        $data = [
            'title' => "Payments",
            'subtitle' => "Set payment bank",
            'page' => "payment/set_bank",
            'payment' => $payment,
            'bankAccounts' => $bankAccounts
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Update data payment by id.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_EDIT);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Payment data', 'trim|required|integer');
            $this->form_validation->set_rules('payment_category', 'Payment category', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('payment_type', 'Payment type', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('payment_date', 'Payment date', 'trim|max_length[50]');
            $this->form_validation->set_rules('settlement_date', 'Settlement date', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('amount', 'Amount', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('no_invoice', 'No Invoice', 'trim|max_length[50]');
            $this->form_validation->set_rules('description', 'Payment description', 'trim|required|max_length[500]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $id = $this->input->post('id');
                $booking = $this->input->post('booking');
                $paymentCategory = $this->input->post('payment_category');
                $paymentType = $this->input->post('payment_type');
                $askPayment = $this->input->post('ask_payment');
                $paymentMethod = $this->input->post('payment_method');
                $bankName = $this->input->post('bank_name');
                $holderName = $this->input->post('account_holder_name');
                $accountNumber = $this->input->post('account_number');
                $withdrawalDate = $this->input->post('withdrawal_date');
                $withdrawalTime = $this->input->post('withdrawal_time');
                $settlementDate = sql_date_format($this->input->post('settlement_date'));
                $paymentDate = sql_date_format($this->input->post('payment_date'));
                $amount = extract_number($this->input->post('amount'));
                $noInvoice = $this->input->post('no_invoice');
                $description = $this->input->post('description');
                $withdrawalDateTime = sql_date_format($withdrawalDate." ".$withdrawalTime);
                $pic = $this->input->post('pic');

                $paymentMethod = $askPayment == 'BANK' ? ($paymentMethod) : null;
                $accountNumber = $askPayment == 'BANK' ? $accountNumber : null;
                $withdrawalDateTime = $askPayment == 'BANK' ? $withdrawalDateTime : null;
                $bankName = $askPayment == 'BANK' ? $bankName : null;
                $holderName = $askPayment == 'BANK' ? $holderName : null;

                $payment = $this->payment->getPaymentById($id); // get data payment sebelum di update

                $fieldAmount = 'amount';
                $fieldAttachment = 'attachment_realization';
                $folderAttachment = 'payment_realizations';
                if ((empty($payment['amount']) || $payment['amount'] <= 0) && !$payment['is_realized']) {
                    $fieldAmount = 'amount_request';
                    $fieldAttachment = 'attachment';
                    $folderAttachment = 'payments';
                }

                // upload attachment if exist, set default old name just in case the attachment does not change
                $fileName = $payment['attachment'];
                $uploadPassed = true;
                if (!empty($_FILES['attachment']['name'])) {
                    // setup location and file name
                    $fileName = 'TR_' . time() . '_' . rand(100, 999);
                    $saveTo = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $folderAttachment;

                    // find or create base folder
                    if ($this->documentType->makeFolder($folderAttachment)) {
                        // try upload with standard config
                        $upload = $this->uploadDocumentFile->uploadTo('attachment', $fileName, $saveTo);
                        if (!$upload['status']) {
                            $uploadPassed = false;
                            flash('warning', $upload['errors']);
                        } else {
                            // delete old file
                            if (!empty($payment['attachment'])) {
                                $this->uploadDocumentFile->deleteFile($payment['attachment'], $saveTo);
                            }
                            // put new file name
                            $fileName = $upload['data']['file_name'];
                        }
                    } else {
                        $uploadPassed = false;
                        flash('warning', 'Folder payments is missing or failed to be created, try again');
                    }
                }

                if ($uploadPassed) {
                    $status = $payment['status'];
                    if ($status == 'REJECTED') {
                        $status = 'DRAFT';
                    }

                    $this->db->trans_start();

                    $this->payment->updatePayment([
                        'id_booking' => $booking,
                        'no_invoice' => $noInvoice,
                        'payment_category' => $paymentCategory,
                        'payment_type' => $paymentType,
                        'ask_payment' => $askPayment, 
                        'payment_method' => $paymentMethod, 
                        'holder_name' => $holderName,
                        'bank_name' => $bankName,
                        'bank_account_number' => $accountNumber, 
                        'withdrawal_date' => $withdrawalDateTime,
                        'settlement_date' => $settlementDate,
                        'payment_date' => if_empty($paymentDate, $payment['payment_date']),
                        $fieldAmount => $amount,
                        'status' => $status,
                        $fieldAttachment => $fileName,
                        'description' => $description,
                        'user_pic' => if_empty($pic, UserModel::authenticatedUserData('id')),
                    ], $id);


                    $this->statusHistory->create([
                        'type' => StatusHistoryModel::TYPE_BOOKING_PAYMENT,
                        'id_reference' => $id,
                        'status' => $status,
                        'description' => 'Payment updated',
                    ]);

                    $this->db->trans_complete();

                    if ($this->db->trans_status()) {

                        //get data payment after di update
                        $payment = $this->payment->getPaymentById($id);

                        //get upload data
                        $upload = $this->uploadModel->getUploadsByBookingId($payment['id_booking']);
                        $documents = $this->uploadDocument->getDocumentsByUpload($upload['id']);

                        //get booking data
                        $bookingData = $this->booking->getBookingById($payment['id_booking']);
                        $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($payment['id_booking']);
                        $containerSize = array_count_values(array_column($bookingContainers, "size"));
                        $containerType = implode(',', array_keys(array_count_values(array_column($bookingContainers, "type"))));
                        $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($payment['id_booking'], true);

                        //get data ATA
                        $documentATA = array_filter($documents, function($doc) {
                            return $doc['document_type'] == 'ATA';
                        });

                        if(!empty($documentATA)){
                            foreach ($documentATA as $dataATA) {
                                $data_ata = $dataATA['document_date'];
                            }
                        }else{
                            $data_ata = null;
                        }

                        //get data people
                        $people = $this->peopleModel->getById($upload['id_person']);

                        //get data partai
                        $dataContainerSize = [];
                        foreach($containerSize as $key => $conSize){
                            $dataContainerSize[] = '('.$conSize.' x '.$key.')';
                        }
                        if(!empty($bookingContainers) && !empty($dataContainerSize)){
                            $containerTypeFormat = !empty($containerType) ? '/'.$containerType : null;
                            $partai = implode(',', $dataContainerSize).$containerTypeFormat;
                        }else{
                            if(!empty($bookingGoods)){
                                $partai = 'LCL';
                            }else{
                                $partai = '-';
                            }
                        }

                        //format data
                        $data_ata = !empty($data_ata) == true ? date('d F Y', strtotime($data_ata)) : '-';
                        $withdrawal_date = !empty($payment['withdrawal_date']) == true ? date('d F Y H:i', strtotime($payment['withdrawal_date'])) : '-';
                        $combinePaymentMethod = $askPayment == 'BANK' ? ($askPayment.'/'.$paymentMethod) : $askPayment;
                        $accountData = $askPayment == 'BANK' ? '('.$holderName.')/('.$bankName.')/('.$accountNumber.')' : '-';

                        $created_by = $this->userModel->getById($payment['created_by']);

                        // data pengajuan untuk dikirimkan ke whatsapp
                        $submission = '*'.'Data Pengajuan '.$payment['no_payment'].' By '.if_empty($created_by['name'], '-').'*'."\n".'     *Telah diubah oleh '.UserModel::authenticatedUserData('name')."*"."\n".
                            "\n".'*'.'1. Tanggal ATA ='.'* '. $data_ata.
                            "\n".'*'.'2. AJU/BCF ='.'* '.$upload['description'].
                            "\n".'*'.'3. Customer ='.'* '. $payment['customer_name'].
                            "\n".'*'.'4. Partai ='.'* '.if_empty($partai, '-').
                            "\n".'*'.'5. Tanggal Penarikan ='.'* '. $withdrawal_date.
                            "\n".'*'.'6. Metode Pembayaran ='.'* '.$combinePaymentMethod.
                            "\n".'*'.'7. Nomor Rekening ='.'* '.$accountData.
                            "\n".'*'.'8. Nominal Pengajuan ='.'* '.if_empty('Rp. '.number_format($payment['amount_request']), '-').
                            "\n".'*'.'9. Deskripsi ='.'* '.if_empty($payment['description'], '-').
                            "\n".'*'.'10. Tanggal Pengajuan ='.'* '.format_date($payment['created_at'], 'd F Y H:i').
                            "\n".'*'.'11. PIC ='.'* '.$payment['pic_name'];

                        $whatsapp_group_submission = get_setting('whatsapp_group_submission'); // group pengajuan
                        if(!empty($whatsapp_group_submission) && !is_null($whatsapp_group_submission) && (empty($payment['amount']) || $payment['amount'] <= 0) && $paymentType != PaymentModel::TYPE_OB_TPS_PERFORMA){
                            $this->send_message($submission, $whatsapp_group_submission);
                        }

                        flash('success', "Payment <strong>{$paymentType}</strong> for booking {$payment['no_booking']} successfully updated");
                        
                        redirect("payment");

                    } else {
                        flash('danger', "Update payment <strong>{$paymentType}</strong> for booking {$payment['no_booking']} failed, try again or contact administrator");
                    }
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->edit($id);
    }

    /**
     * Perform deleting payment data.
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_DELETE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {

            $payment = $this->payment->getPaymentById($id);

            $invoiceDetail = $this->invoiceDetail->getBy([
                'description' => $payment['no_payment'],
            ], true);
            if (!empty($invoiceDetail)) {
                $invoice = $this->invoice->getInvoiceById($invoiceDetail['id_invoice']);

                if (!empty($invoice)) {
                    flash('danger', "Payment {$payment['no_payment']} is already used in invoice {$invoice['no_invoice']}", '_back', 'payment');
                }
            }

            $delete = $this->payment->deletePayment($id);

            if ($delete) {
                flash('warning', "Payment <strong>{$payment['payment_type']}</strong> for booking {$payment['no_booking']} successfully deleted");
            } else {
                flash('danger', "Delete payment <strong>{$payment['payment_type']}</strong> for booking {$payment['no_booking']} failed, try again or contact administrator");
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('payment');
    }

    /**
     * Toggle charge position.
     *
     * @param $id
     */
    public function switch_charge_position($id)
    {
        $payment = $this->payment->getPaymentById($id);
        $switchTo = $payment['charge_position'] == PaymentModel::CHARGE_BEFORE_TAX ? PaymentModel::CHARGE_AFTER_TAX : PaymentModel::CHARGE_BEFORE_TAX;

        $this->payment->updatePayment([
            'charge_position' => $switchTo
        ], $id);

        $this->statusHistory->create([
            'type' => StatusHistoryModel::TYPE_BOOKING_PAYMENT,
            'id_reference' => $id,
            'status' => $payment['status'],
            'description' => 'Switch charge to: ' . $switchTo,
        ]);

        flash('warning', "Payment <strong>{$payment['payment_type']}</strong> for booking {$payment['no_booking']} switched to {$switchTo}");
        redirect('payment');
    }

    /**
     * Submit payment settle document.
     * @param $paymentId
     */
    public function submit($paymentId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_CREATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $message = $this->input->post('message');
            $payment = $this->payment->getPaymentById($paymentId);

            $uploadedFile = $payment['submission_attachment'];
            $uploadFile = true;
            if (!empty($_FILES['attachment']['name'])) {
                $uploadFile = $this->uploader->uploadTo('attachment', ['destination' => 'payment-submission/' . date('Y/m')]);
                if ($uploadFile) {
                    $uploadedData = $this->uploader->getUploadedData();
                    $uploadedFile = $uploadedData['uploaded_path'];
                } else {
                    flash('danger', $this->uploader->getDisplayErrors());
                }
            }

            if ($uploadFile) {
                $this->db->trans_start();

                $this->payment->updatePayment([
                    'is_submitted' => true,
                    'submitted_at' => date('Y-m-d H:i:s'),
                    'submitted_by' => UserModel::authenticatedUserData('id'),
                    'submission_attachment' => if_empty($uploadedFile, null)
                ], $paymentId);

                $this->statusHistory->create([
                    'type' => StatusHistoryModel::TYPE_BOOKING_PAYMENT,
                    'id_reference' => $paymentId,
                    'status' => PaymentModel::STATUS_SUBMITTED,
                    'description' => if_empty($message, 'Submit payment'),
                    'data' => json_encode([
                        'no_payment' => $payment['no_payment'],
                        'submission_attachment' => if_empty($uploadedFile, null)
                    ])
                ]);

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Payment of <strong>{$payment['payment_type']}</strong> successfully <strong>submitted</strong>");
                } else {
                    flash('danger', "Submit payment <strong>{$payment['payment_type']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('payment');
    }

    /**
     * Reject payment submission.
     * @param $paymentId
     */
    public function reject_submission($paymentId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_REALIZE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $message = $this->input->post('message');
            $payment = $this->payment->getPaymentById($paymentId);

            $this->db->trans_start();

            $this->payment->updatePayment([
                'submitted_at' => null,
                'submitted_by' => null
            ], $paymentId);

            $this->statusHistory->create([
                'type' => StatusHistoryModel::TYPE_BOOKING_PAYMENT,
                'id_reference' => $paymentId,
                'status' => PaymentModel::STATUS_SUBMISSION_REJECTED,
                'description' => 'Submission rejected: ' . if_empty($message, $payment['no_payment']),
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('warning', "Payment of <strong>{$payment['no_payment']}</strong> successfully <strong>rejected</strong>");
            } else {
                flash('danger', "Reject payment submission <strong>{$payment['no_payment']}</strong> failed, try again or contact administrator");
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('payment');
    }

    /**
     * Validate document (approve/reject)
     * @param $type
     * @param $paymentId
     */
    public function validate($type, $paymentId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_VALIDATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {

            $payment = $this->payment->getPaymentById($paymentId);
            if ($payment['status'] != PaymentModel::STATUS_DRAFT) {
                flash('danger', 'Payment already validated, current status is ' . $payment['status'], '_back', 'payment');
            }

            $this->form_validation->set_data(['type' => $type]);
            $this->form_validation->set_rules('type', 'payment status', 'in_list[approve,reject]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $statuses = [
                    'approve' => "APPROVED",
                    'reject' => "REJECTED",
                ];
                $statusValidation = $statuses[$type];

                if (!empty($payment['id_booking'])) {
                    $noPaymentBooking = $payment['no_booking'] . " (" . $payment['no_reference'] . ")";
                } else {
                    $noPaymentBooking = $payment['no_upload'] . " (" . $payment['upload_description'] . ")";
                }

                $data = [
                    'status' => $statusValidation
                ];

                if ($statusValidation == 'APPROVED') {
                    $data['approved_at'] = date('Y-m-d H:i:s');
                    $data['approved_by'] = UserModel::authenticatedUserData('id');
                }

                $this->db->trans_start();

                $this->payment->updatePayment($data, $paymentId);

                $this->statusHistory->create([
                    'type' => StatusHistoryModel::TYPE_BOOKING_PAYMENT,
                    'id_reference' => $paymentId,
                    'status' => $statusValidation,
                    'description' => $statusValidation == 'APPROVED' ? 'Payment approved' : 'Payment rejected',
                ]);

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    $statusText = 'rejected';
                    $statusClass = 'warning';

                    //get upload data
                    $upload = $this->uploadModel->getUploadsByBookingId($payment['id_booking']);
                    $documents = $this->uploadDocument->getDocumentsByUpload($upload['id']);

                    //get booking data
                    $bookingData = $this->booking->getBookingById($payment['id_booking']);
                    $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($payment['id_booking']);
                    $containerSize = array_count_values(array_column($bookingContainers, "size"));
                    $containerType = implode(',', array_keys(array_count_values(array_column($bookingContainers, "type"))));
                    $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($payment['id_booking'], true);

                    //get data ATA
                    $documentATA = array_filter($documents, function($doc) {
                        return $doc['document_type'] == 'ATA';
                    });

                    if(!empty($documentATA)){
                        foreach ($documentATA as $dataATA) {
                            $data_ata = $dataATA['document_date'];
                        }
                    }else{
                        $data_ata = null;
                    }

                    //get data people
                    $people = $this->peopleModel->getById($upload['id_person']);

                    //get data partai
                    $dataContainerSize = [];
                    foreach($containerSize as $key => $conSize){
                        $dataContainerSize[] = '('.$conSize.' x '.$key.')';
                    }
                    if(!empty($bookingContainers) && !empty($dataContainerSize)){
                        $containerTypeFormat = !empty($containerType) ? '/'.$containerType : null;
                        $partai = implode(',', $dataContainerSize).$containerTypeFormat;
                    }else{
                        if(!empty($bookingGoods)){
                            $partai = 'LCL';
                        }else{
                            $partai = '-';
                        }
                    }

                    $data_ata = !empty($data_ata) == true ? date('d F Y', strtotime($data_ata)) : '-';
                    $withdrawal_date = !empty($payment['withdrawal_date']) == true ? date('d F Y H:i', strtotime($payment['withdrawal_date'])) : '-';
                    $combinePaymentMethod = $payment['ask_payment'] == 'BANK' ? ($payment['ask_payment'].'/'.$payment['payment_method']) : $payment['ask_payment'];
                    $accountData = $payment['ask_payment'] == 'BANK' ? '('.$payment['bank_name'].')/('.$payment['holder_name'].')/('.$payment['bank_account_number'].')' : '-';

                    $created_by = $this->userModel->getById($payment['created_by']);

                    if ($statusValidation == 'APPROVED') {
                        $statusText = 'approved';
                        $statusClass = 'success';

                        if($payment['ask_payment'] == 'BANK'){
                            $submission = '*'.'Data Pengajuan '.$payment['no_payment'].' By '.if_empty($created_by['name'], '-').'*'."\n".'     *Telah disetujui oleh '.UserModel::authenticatedUserData('name')."*"."\n".
                                "\n".'*'.'1. Tanggal ATA ='.'* '. $data_ata.
                                "\n".'*'.'2. AJU/BCF ='.'* '.$upload['description'].
                                "\n".'*'.'3. Customer ='.'* '. $payment['customer_name'].
                                "\n".'*'.'4. Partai ='.'* '.if_empty($partai, '-').
                                "\n".'*'.'5. Tanggal Penarikan ='.'* '. $withdrawal_date.
                                "\n".'*'.'6. Metode Pembayaran ='.'* '.$combinePaymentMethod.
                                "\n".'*'.'7. Nomor Rekening ='.'* '.$accountData.
                                "\n".'*'.'8. Nominal Pengajuan ='.'* '.if_empty('Rp. '.number_format($payment['amount_request']), '-').
                                "\n".'*'.'9. Deskripsi ='.'* '.if_empty($payment['description'], '-').
                                "\n".'*'.'10. Tanggal Pengajuan ='.'* '.format_date($payment['created_at'], 'd F Y H:i').
                                "\n".'*'.'11. PIC ='.'* '.$payment['pic_name'];

                            if(empty($payment['amount']) || $payment['amount'] <= 0){
                                $whatsapp_group_submission = get_setting('whatsapp_group_submission'); //group pengajuan
                                if(!empty($whatsapp_group_submission) && !is_null($whatsapp_group_submission)){
                                    $this->send_message($submission, $whatsapp_group_submission);
                                }

                                $whatsapp_group_transfer = get_setting('whatsapp_group_transfer'); //group transfer
                                if(!empty($whatsapp_group_transfer) && !is_null($whatsapp_group_transfer)){
                                    $this->send_message($submission, $whatsapp_group_transfer);
                                }
                            }
                        }else{
                            $submission = '*'.'Data Pengajuan '.$payment['no_payment'].' By '.if_empty($created_by['name'], '-').'*'."\n".'     *Telah disetujui oleh '.UserModel::authenticatedUserData('name')."*"."\n".
                                "\n".'*'.'1. Tanggal ATA ='.'* '. $data_ata.
                                "\n".'*'.'2. AJU/BCF ='.'* '.$upload['description'].
                                "\n".'*'.'3. Customer ='.'* '. $payment['customer_name'].
                                "\n".'*'.'4. Partai ='.'* '.if_empty($partai, '-').
                                "\n".'*'.'5. Tanggal Penarikan ='.'* '. $withdrawal_date.
                                "\n".'*'.'6. Metode Pembayaran ='.'* '.$combinePaymentMethod.
                                "\n".'*'.'7. Nomor Rekening ='.'* '.$accountData.
                                "\n".'*'.'8. Nominal Pengajuan ='.'* '.if_empty('Rp. '.number_format($payment['amount_request']), '-').
                                "\n".'*'.'9. Deskripsi ='.'* '.if_empty($payment['description'], '-').
                                "\n".'*'.'10. Tanggal Pengajuan ='.'* '.format_date($payment['created_at'], 'd F Y H:i').
                                "\n".'*'.'11. PIC ='.'* '.$payment['pic_name'];

                            $whatsapp_group_submission = get_setting('whatsapp_group_submission'); //group pengajuan
                            if(!empty($whatsapp_group_submission) && !is_null($whatsapp_group_submission) && (empty($payment['amount']) || $payment['amount'] <= 0)){
                                $this->send_message($submission, $whatsapp_group_submission);
                            }
                        }

                    }else{
                        $submission = '*'.'Data Pengajuan '.$payment['no_payment'].' By '.if_empty($created_by['name'], '-').'*'."\n".'     *Telah ditolak oleh '.UserModel::authenticatedUserData('name')."*"."\n".
                            "\n".'*'.'1. Tanggal ATA ='.'* '. $data_ata.
                            "\n".'*'.'2. AJU/BCF ='.'* '.$upload['description'].
                            "\n".'*'.'3. Customer ='.'* '. $payment['customer_name'].
                            "\n".'*'.'4. Partai ='.'* '.if_empty($partai, '-').
                            "\n".'*'.'5. Tanggal Penarikan ='.'* '. $withdrawal_date.
                            "\n".'*'.'6. Metode Pembayaran ='.'* '.$combinePaymentMethod.
                            "\n".'*'.'7. Nomor Rekening ='.'* '.$accountData.
                            "\n".'*'.'8. Nominal Pengajuan ='.'* '.if_empty('Rp. '.number_format($payment['amount_request']), '-').
                            "\n".'*'.'9. Deskripsi ='.'* '.if_empty($payment['description'], '-').
                            "\n".'*'.'10. Tanggal Pengajuan ='.'* '.format_date($payment['created_at'], 'd F Y H:i').
                            "\n".'*'.'11. PIC ='.'* '.$payment['pic_name'];

                        $whatsapp_group_submission = get_setting('whatsapp_group_submission'); //group pengajuan
                        if(!empty($whatsapp_group_submission) && !is_null($whatsapp_group_submission) && (empty($payment['amount']) || $payment['amount'] <= 0)){
                            $this->send_message($submission, $whatsapp_group_submission);
                        }
                    }

                    flash($statusClass, "Payment of <strong>{$payment['payment_type']}</strong> for booking {$noPaymentBooking} successfully <strong>{$statusText}</strong>");
                } else {
                    flash('danger', "Validating payment <strong>{$payment['payment_type']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('payment');
    }

    /**
     * Update bank payment.
     *
     * @param $id
     */
    public function update_bank($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_REALIZE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('amount', 'Bank Account', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('bank_account', 'Bank Account', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('description', 'Payment description', 'trim|required|max_length[500]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $description = $this->input->post('description');
                $amount = $this->input->post('amount');
                $bankAccountId = $this->input->post('bank_account');
                $sendImmediately = $this->input->post('send_immediately');

                $bankAccount = $this->bankAccount->getById($bankAccountId);
                $payment = $this->payment->getPaymentById($id);

                $this->db->trans_start();

                $this->payment->update([
                    'id_bank_account' => $bankAccountId,
                    'amount' => extract_number($amount),
                    'invoice_description' => $description
                ], $id);

                // bank regular need confirmation
                if ($bankAccount['bank_type'] == BankAccountModel::TYPE_REGULAR) {
                    if ($sendImmediately) {
                        $updateLastBank = $payment['id_bank_account'] != $bankAccount;
                        $statusPendingOrRejected = in_array($payment['status_check'], [
                            PaymentModel::STATUS_PENDING, PaymentModel::STATUS_REJECTED
                        ]);
                        if ($updateLastBank || $statusPendingOrRejected) {
                            $this->payment->update([
                                'status_check' => PaymentModel::STATUS_ASK_APPROVAL
                            ], $payment['id']);
                        }
                    }
                } else {
                    $this->payment->update([
                        'status_check' => PaymentModel::STATUS_APPROVED
                    ], $payment['id']);
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    if ($bankAccount['bank_type'] == BankAccountModel::TYPE_REGULAR && $sendImmediately) {
                        if (!$this->send_email_ask_approval($payment['id'])) {
                            flash('success', "Bank payment {$payment['no_payment']} is successfully updated, but email failed to be sent");
                        }
                    }
                    flash('success', "Bank payment {$payment['no_payment']} is successfully updated", 'payment');
                } else {
                    flash('danger', 'Update bank payment failed, try again or contact administrator');
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->set_bank($id);
    }

    /**
     * Ask approval batch
     */
    public function ask_approval()
    {
        if ($this->send_email_ask_approval()) {
            flash('success', "All payment is requested for approval");
        } else {
            flash('success', "Failed sent payment request");
        }
        redirect('payment');
    }

    /**
     * Send email approval.
     *
     * @param $paymentId
     * @return bool
     */
    public function send_email_ask_approval($paymentId = null)
    {
        $emailTo = 'direktur@transcon-indonesia.com';
        $payments = [];
        if (!empty($paymentId)) {
            $payment = $this->payment->getById($paymentId);
            if (!empty($payment)) {
                $payments = [$payment];
            }
        } else {
            $batchId = explode(',', $this->input->post('id'));
            foreach ($batchId as $paymentId) {
                $payment = $this->payment->getById($paymentId);
                if ($payment['bank_type'] == BankAccountModel::TYPE_REGULAR && $payment['status_check'] != PaymentModel::STATUS_APPROVED) {
                    $payments[] = $payment;
                }
            }
        }
        if (!empty($payments)) {
            $this->load->helper('string');
            $creatorId = UserModel::authenticatedUserData('id');

            foreach ($payments as &$payment) {
                $token = random_string('alnum', 32);

                if (in_array($payment['status_check'], [PaymentModel::STATUS_PENDING, PaymentModel::STATUS_REJECTED])) {
                    $this->payment->update([
                        'status_check' => PaymentModel::STATUS_ASK_APPROVAL
                    ], $payment['id']);
                }

                $payment['token'] = $token;
                $this->statusHistory->create([
                    'type' => StatusHistoryModel::TYPE_BOOKING_PAYMENT,
                    'id_reference' => $payment['id'],
                    'status' => PaymentModel::STATUS_ASK_APPROVAL,
                    'description' => 'Ask approval bank transfer',
                    'data' => json_encode([
                        'token' => $token,
                        'email' => $emailTo,
                        'creator' => $creatorId
                    ])
                ]);
            }

            $emailTitle = 'Booking payment (batch) request approval at ' . date('d F Y H:i');
            if (count($payments) == 1) {
                $payment = end($payments);
                $emailTitle = 'Booking payment ' . $payment['payment_type'] . ' customer ' . $payment['customer_name'] . ' is requested via ' . $payment['bank'];
            }
            $emailTemplate = 'emails/payment_request_approval';
            $emailData = [
                'title' => 'Bank Payment Notification',
                'name' => 'Manager',
                'email' => 'direktur@transcon-indonesia.com',
                'payments' => $payments,
            ];
            $emailOptions = [
                'cc' => ['acc_mgr@transcon-indonesia.com', 'fin2@transcon-indonesia.com', 'fin@transcon-indonesia.com'],
            ];
            //return $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
            return true;
        }
        return false;
    }

    /**
     * Update realization payment by id.
     * @param $id
     */
    public function update_realization($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_REALIZE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('amount', 'Amount', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('payment_category', 'Payment category', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('payment_type', 'Payment type', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('payment_date', 'Payment date', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('no_invoice', 'No Invoice', 'trim|max_length[50]');
            $this->form_validation->set_rules('description', 'Payment description', 'trim|required|max_length[500]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $amount = extract_number($this->input->post('amount'));
                $noInvoice = $this->input->post('no_invoice');
                $paymentCategory = $this->input->post('payment_category');
                $paymentType = $this->input->post('payment_type');
                $paymentDate = sql_date_format($this->input->post('payment_date'));
                $description = $this->input->post('description');
                $statusContainer = $this->input->post('status_container');
                $statusGoods = $this->input->post('status_goods');

                $payment = $this->payment->getPaymentById($id);

                $this->db->trans_start();
                // upload attachment if exist
                $fileName = '';
                $uploadPassed = true;
                if (!empty($_FILES['attachment']['name'])) {
                    $fileName = 'TR_' . time() . '_' . rand(100, 999);
                    $saveTo = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'payment_realizations';
                    if ($this->documentType->makeFolder('payment_realizations')) {
                        $upload = $this->uploadDocumentFile->uploadTo('attachment', $fileName, $saveTo);
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
                    $update = $this->payment->updatePayment([
                        'no_invoice' => $noInvoice,
                        'amount' => $amount,
                        'is_realized' => 1,
                        'attachment_realization' => $fileName,
                        'payment_category' => $paymentCategory,
                        'payment_type' => $paymentType,
                        'payment_date' => $paymentDate,
                        'invoice_description' => $description,
                        'realized_by' => UserModel::authenticatedUserData('id'),
                        'realized_at' => sql_date_format('now'),
                    ], $id);

                    $this->statusHistory->create([
                        'type' => StatusHistoryModel::TYPE_BOOKING_PAYMENT,
                        'id_reference' => $id,
                        'status' => PaymentModel::STATUS_REALIZED,
                        'description' => 'Payment realized',
                    ]);

                    if (!empty($statusContainer)) {
                        foreach ($statusContainer as $key => $status) {
                            $this->bookingContainer->updateBookingContainer([
                                'status_danger_payment' => $status
                            ], $key);
                        }
                    }

                    if (!empty($statusGoods)) {
                        foreach ($statusGoods as $key => $status) {
                            $this->bookingGoods->updateBookingGoods([
                                'status_danger_payment' => $status
                            ], $key);
                        }
                    }

                    //point kas bon ketika realization <= 2 elapsed_day_until_realized
                    
                    $id_adm = $this->employee->getBy(
                        [
                            'ref_employees.id_user'=>  $payment['user_pic'],
                        ]
                    ,true);
                    $id_adm = $id_adm['id'];
                    $data = json_encode($payment);
                    $date = date('Y-m-d');
                    $temp_selisih = 0;
                    $temp_tanggal = date("Y-m-d",strtotime($payment['approved_at']));
                    $temp_tanggal = strtotime($temp_tanggal);

                    //mengecek selisih hari dengan hari libur dan minggu
                    if($update){
                        //mengecek selisih hari dengan hari libur dan minggu
                        while(($temp_tanggal = strtotime("+1 days",$temp_tanggal)) <= strtotime($date)){
                            // $temp_tanggal = strtotime("+1 days",$temp_tanggal);
                            $holiday=$this->scheduleHoliday->is_holiday(date("Y-m-d",$temp_tanggal));
                            
                            if(date('w',$temp_tanggal)!=0 && !$holiday){
                                $temp_selisih++;
                            }
                            // if ($temp_selisih>=5) { //kasih pembatasan biar tidak terlalu bnyak looping
                            //     break;
                            // }
                        }

                        if ($temp_selisih <= 4 && $paymentCategory==PaymentModel::PAYMENT_BILLING && $paymentType == 'AS PER BILL'){
                            //Tunjangan Kas Bon
                            $this->allowance->create([
                                [
                                    'id_employee' => $id_adm,
                                    'id_component' => 7,
                                    'id_reference' => $payment['id'],
                                    'date' => $date,
                                    'different_date' => null,
                                    'data' => $data,
                                    'point' => 1,
                                    'description' => 'realize payment kas bon H+'.$temp_selisih,
                                ],
                                [
                                    'id_employee' => $id_adm,
                                    'id_component' => 21,
                                    'id_reference' => $payment['id'],
                                    'date' => $date,
                                    'different_date' => null,
                                    'data' => $data,
                                    'point' => 1,
                                    'description' => 'realize payment kas bon H+'.$temp_selisih,
                                ]
                            ]);
                        }else if ($temp_selisih <= 2) {
                            $this->allowance->create([
                                [
                                    'id_employee' => $id_adm,
                                    'id_component' => 7,
                                    'id_reference' => $payment['id'],
                                    'date' => $date,
                                    'different_date' => null,
                                    'data' => $data,
                                    'point' => 1,
                                    'description' => 'realize payment kas bon H+'.$temp_selisih,
                                ],
                                [
                                    'id_employee' => $id_adm,
                                    'id_component' => 21,
                                    'id_reference' => $payment['id'],
                                    'date' => $date,
                                    'different_date' => null,
                                    'data' => $data,
                                    'point' => 1,
                                    'description' => 'realize payment kas bon H+'.$temp_selisih,
                                ]
                            ]);
                        }else{
                            $this->allowance->create([
                                [
                                    'id_employee' => $id_adm,
                                    'id_component' => 7,
                                    'id_reference' => $payment['id'],
                                    'date' => $date,
                                    'different_date' => null,
                                    'data' => $data,
                                    'point' => 0,
                                    'description' => 'realize payment kas bon H+'.$temp_selisih,
                                ],
                                [
                                    'id_employee' => $id_adm,
                                    'id_component' => 21,
                                    'id_reference' => $payment['id'],
                                    'date' => $date,
                                    'different_date' => null,
                                    'data' => $data,
                                    'point' => 0,
                                    'description' => 'realize payment kas bon H+'.$temp_selisih,
                                ]
                            ]);
                        }
                        
                    }

                    $this->db->trans_complete();

                    if ($this->db->trans_status()) {
                        flash('success', "Payment <strong>{$payment['payment_type']}</strong> for booking {$payment['no_booking']} successfully updated");
                        redirect("payment");
                    } else {
                        flash('danger', "Update payment <strong>{$payment['payment_type']}</strong> for booking {$payment['no_booking']} failed, try again or contact administrator");
                    }
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->edit($id);
    }

    /**
     * Print receipt
     * @param $id
     */
    public function print_payment($id)
    {
        $payment = $this->payment->getPaymentById($id);
        $data = [
            'title' => "Print Payment",
            'subtitle' => "Payment receipt",
            'page' => "payment/print_payment",
            'payment' => $payment,
        ];
        $this->load->view('template/print', $data);
    }

    /**
     * Mass resend notification.
     */
    public function resend_bulk_notification()
    {
        $dateFrom = $this->input->post('date_from');
        $dateTo = $this->input->post('date_to');

        if (difference_date($dateFrom, $dateTo) > 30) {
            flash('danger', 'Maximum resend notification maximum 30 days in ranges', '_back');
        }

        $branches = $this->branch->getAll();
        $payments = [];

        $paymentConditions = [
            'payments.payment_type!=' => PaymentModel::TYPE_OB_TPS_PERFORMA,
            'payments.is_realized' => false,
            //'payments.amount<=' => 0,
        ];
        if (!empty($dateFrom)) {
            $paymentConditions['payments.created_at>='] = format_date($dateFrom, 'Y-m-d H:i:s');
        }
        if (!empty($dateTo)) {
            $paymentConditions['payments.created_at<='] = format_date($dateTo, 'Y-m-d H:i:s');
        }
        if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_REALIZE)) {
            $paymentConditions['payments.status'] = [PaymentModel::STATUS_DRAFT, PaymentModel::STATUS_APPROVED];
        } else if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_VALIDATE)) {
            $paymentConditions['payments.status'] = PaymentModel::STATUS_APPROVED;
            $paymentConditions['payments.approved_by'] = UserModel::authenticatedUserData('id');
        } else {
            $paymentConditions['payments.status'] = PaymentModel::STATUS_DRAFT;
            $paymentConditions['payments.created_by'] = UserModel::authenticatedUserData('id');
        }
        foreach ($branches as $branch) {
            $paymentConditions['_id_branch'] = $branch['id'];
            $payments = array_merge($payments, $this->payment->getBy($paymentConditions));
        }

        $whatsappGroupSubmission = get_setting('whatsapp_group_submission');
        $whatsappGroupTransfer = get_setting('whatsapp_group_transfer');

        foreach ($payments as $index => $payment) {
            $party = '-';
            if (empty($payment['id_booking'])) {
                $uploadData = $this->uploadModel->getById($payment['id_upload']);
            } else {
                $uploadData = $this->uploadModel->getUploadsByBookingId($payment['id_booking']);
                $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($payment['id_booking']);
                $containerSize = array_count_values(array_column($bookingContainers, "size"));
                $containerType = implode(',', array_keys(array_count_values(array_column($bookingContainers, "type"))));

                $dataContainerSize = [];
                foreach ($containerSize as $key => $conSize) {
                    $dataContainerSize[] = '(' . $conSize . ' x ' . $key . ')';
                }
                if (!empty($bookingContainers) && !empty($dataContainerSize)) {
                    $containerTypeFormat = !empty($containerType) ? '/' . $containerType : null;
                    $party = implode(',', $dataContainerSize) . $containerTypeFormat;
                } else {
                    $party = 'LCL';
                }
            }
            $documentATA = $this->uploadDocument->getBy([
                'upload_documents.id_upload' => $uploadData['id'],
                'ref_document_types.document_type' => 'ATA'
            ], true);

            $approvedMessage = '';
            if ($payment['status'] == PaymentModel::STATUS_APPROVED) {
                $approvedMessage = '     *Telah disetujui oleh '.if_empty($payment['validator_name'], '-')." (Resend Notification)*" . "\n";
            }

            $submission = '*'.'Data Pengajuan '.$payment['no_payment'].' By '.if_empty($payment['applicant_name'], '-').'*'."\n".
                $approvedMessage .
                "\n".'*'.'1. Tanggal ATA ='.'* '. (empty($documentATA) ? '-' : format_date($documentATA['document_date'], 'd F Y')).
                "\n".'*'.'2. AJU/BCF ='.'* '.$uploadData['description'].
                "\n".'*'.'3. Customer ='.'* '. $payment['customer_name'].
                "\n".'*'.'4. Partai ='.'* '.if_empty($party, '-').
                "\n".'*'.'5. Tanggal Penarikan ='.'* '. format_date($payment['withdrawal_date'], 'd F Y H:i') .
                "\n".'*'.'6. Metode Pembayaran ='.'* '. ($payment['ask_payment'] == 'BANK' ? ($payment['ask_payment'].'/'.$payment['payment_method']) : $payment['ask_payment']) .
                "\n".'*'.'7. Nomor Rekening ='.'* '. ($payment['ask_payment'] == 'BANK' ? '('.$payment['bank_name'].')/('.$payment['holder_name'].')/('.$payment['bank_account_number'].')' : '-') .
                "\n".'*'.'8. Nominal Pengajuan ='.'* '.if_empty('Rp. '.number_format($payment['amount_request']), '-').
                "\n".'*'.'9. Deskripsi ='.'* '.if_empty($payment['description'], '-');

            if ($payment['status'] == PaymentModel::STATUS_APPROVED) {
                $this->send_message($submission, $whatsappGroupSubmission);
                if ($payment['ask_payment'] == 'BANK') {
                    $this->send_message($submission, $whatsappGroupTransfer);
                }
            } else {
                $this->send_message($submission, $whatsappGroupSubmission);
            }
        }

        flash('success', "Mass notification resend result " . count($payments) . " payments", 'payment');
    }

    /**
     * Get all payment related this booking.
     */
    public function ajax_get_payment_by_booking()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $bookingId = $this->input->get('id_booking');
            $paymentBillings = $this->payment->getPaymentsByBooking($bookingId, PaymentModel::PAYMENT_BILLING);
            $paymentNonBillings = $this->payment->getPaymentsByBooking($bookingId, PaymentModel::PAYMENT_NON_BILLING);
            echo $this->load->view('payment/_transaction', [
                'paymentBillings' => $paymentBillings,
                'paymentNonBillings' => $paymentNonBillings
            ], true);
        }
    }

    public function ajax_get_booking_detail_by_booking()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $bookingId = $this->input->get('id_booking');
            $page = $this->input->get('page');

            $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($bookingId);
            $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($bookingId);

            echo $this->load->view('payment/_booking_detail', [
                'bookingContainers' => $bookingContainers,
                'bookingGoods' => $bookingGoods,
                'page' => $page
            ], true);
        }
    }

    /**
     * Get assigned booking OB TPS
     */
    public function ajax_get_assigned_booking()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $paymentCategory = $this->input->get('category');
            $paymentType = $this->input->get('type');
            $filters = [
                'transaction_exist' => false,
                'allow_empty_stock' => AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_REALIZE) || AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_PIC),
                'allow_empty_limit' => date('Y-m-d', strtotime('-21 day'))
            ];

            if (!AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_REALIZE) && !AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_PIC) && $paymentType == 'EMPTY CONTAINER REPAIR') {
                $filters['allow_empty_stock'] = true;
                $filters['allow_empty_limit'] = date('Y-m-d', strtotime('-1 day'));
            }

            $bookings = $this->report->getAvailableStockBookingList('all', $filters);

            if ($paymentType == 'OB TPS') {
                // for OB TPS matching user creator with assigned booking
                $userId = UserModel::authenticatedUserData('id');
                $assignedBookings = $this->bookingAssignment->getAssignedBookings($userId);
                $bookings = array_filter($bookings, function ($booking) use ($assignedBookings) {
                    $isFound = false;
                    $selectedBooking = $this->booking->getBookingById($booking['id_booking']);
                    if ($selectedBooking['with_do']) {
                        foreach ($assignedBookings as $assignedBooking) {
                            if ($booking['id_booking'] == $assignedBooking['id_booking']) {
                                // allow create payment for OB TPS a day before ETA
                                /*
                                $booking = $this->booking->getBookingById($booking['id_booking']);
                                if (!empty($booking) && !empty($booking['eta'])) {
                                    if (difference_date(date('Y-m-d'), $booking['eta']) < 2) {
                                        $isFound = true;
                                    }
                                    break;
                                }
                                */

                                $isFound = true;
                                break;
                            }
                        }
                    } else {
                        $isFound = true;
                    }
                    return $isFound;
                });
            }

            // skip payment if already has invoice
            if ($paymentCategory == 'BILLING') {
                $invoices = $this->invoice->getBy([
                    'invoices.no_reference' => array_column($bookings, 'no_booking'),
                    'invoices.status' => InvoiceModel::STATUS_PUBLISHED
                ]);
                $bookings = array_filter($bookings, function ($booking) use ($invoices) {
                    if (in_array($booking['no_booking'], array_column($invoices, 'no_reference'))) {
                        return false;
                    }
                    return true;
                });
                $bookings = array_values($bookings);
            }

            header('Content-Type: application/json');
            echo json_encode($bookings);
        }
    }
}
