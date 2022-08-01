<?php

/**
 * Class Readdress
 * @property BookingModel $booking
 * @property HandlingModel $handling
 * @property HandlingContainerModel $handlingContainer
 * @property HandlingGoodsModel $handlingGoods
 * @property WorkOrderModel $workOrder
 * @property WorkOrderContainerModel $workOrderContainer
 * @property WorkOrderGoodsModel $workOrderGoods
 * @property ReaddressModel $readdress
 * @property InvoiceModel $invoice
 * @property ReportModel $report
 * @property PeopleModel $people
 */
class Readdress extends MY_Controller
{
    /**
     * Readdress constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BookingModel', 'booking');
        $this->load->model('HandlingModel', 'handlingModel');
        $this->load->model('HandlingContainerModel', 'handlingContainer');
        $this->load->model('HandlingGoodsModel', 'handlingGoods');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('WorkOrderContainerModel', 'workOrderContainer');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('HandlingModel', 'handling');
        $this->load->model('InvoiceModel', 'invoice');
        $this->load->model('ReaddressModel', 'readdress');
        $this->load->model('ReportModel', 'report');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('LogModel', 'logHistory');
        $this->load->model('UploadModel', 'uploadModel');

        $this->setFilterMethods([
            'readdress_data' => 'GET',
            'validate_readdress' => 'POST|PUT|PATCH',
        ]);
    }

    /**
     * Show index readdress
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_READDRESS_VIEW);

        $this->render('readdress/index', [], 'Readdress');
    }

    /**
     * Get ajax datatable readdress.
     */
    public function readdress_data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_READDRESS_VIEW);

        $filters = [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ];

        $data = $this->readdress->getAll($filters);

        $this->render_json($data);
    }

    /**
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_READDRESS_VIEW);

        $readdress = $this->readdress->getById($id);

        $this->render('readdress/view', compact('readdress'), 'View readdress ' . $readdress['no_booking']);
    }

    /**
     * Create new readdress.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_READDRESS_CREATE);

        $this->render('readdress/create', [], 'Create Readdress');
    }

    /**
     * Booking get booking
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_READDRESS_CREATE);

        if ($this->validate()) {
            $bookingId = $this->input->post('booking');
            $customerId = $this->input->post('customer');
            $description = $this->input->post('description');

            $booking = $this->booking->getBookingById($bookingId);

            $save = $this->readdress->create([
                'id_booking' => $bookingId,
                'id_customer_from' => $booking['id_customer'],
                'id_customer_to' => $customerId,
                'status' => ReaddressModel::STATUS_PENDING,
                'description' => $description,
                'created_by' => UserModel::authenticatedUserData('id')
            ]);

            if ($save) {
                flash('success', "Readdress booking {$booking['no_booking']} successfully created, waiting for approval", 'readdress');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->create();
    }


    /**
     * Validate booking (approve/reject)
     */
    public function validate_readdress()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_READDRESS_VALIDATE);

        if ($this->validate(['id' => 'required', 'status' => 'required'])) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');

            $this->db->trans_start();

            $readdress = $this->readdress->getById($id);

            if ($status == ReaddressModel::STATUS_APPROVED) {
                $alertLabel = 'success';

                $this->readdress->update([
                    'status' => ReaddressModel::STATUS_APPROVED
                ], $id);

                $bookingOut = $this->booking->getBookingOutByBookingIn($readdress['id_booking']);
                $bookings = array_merge([$readdress['id_booking']], array_column(if_empty($bookingOut, []), 'id'));

                foreach ($bookings as $bookingId) {

                    //update booking
                    $this->booking->updateBooking(['id_customer' => $readdress['id_customer_to']], $bookingId);

                    //update invoice
                    $book = $this->booking->getBookingById($bookingId);
                    $invoices = $this->invoice->getInvoicesByNoReference($book['no_booking']);
                    foreach ($invoices as $invoice) {
                        $this->invoice->updateInvoice(['id_customer' => $readdress['id_customer_to']], $invoice['id']);
                    }

                    //update online
                    $handlings = $this->handling->getHandlingsByBooking($bookingId);
                    foreach ($handlings as $handling) {
                        $this->handling->updateHandling(['id_customer' => $readdress['id_customer_to']], $handling['id']);

                        $handlingContainers = $this->handlingContainer->getHandlingContainersByHandling($handling['id']);
                        foreach ($handlingContainers as $handlingContainer) {
                            $this->handlingContainer->updateHandlingContainer(['id_owner' => $readdress['id_customer_to']], $handlingContainer['id']);
                        }

                        $handlingGoods = $this->handlingGoods->getHandlingGoodsByHandling($handling['id']);
                        foreach ($handlingGoods as $handlingItem) {
                            $this->handlingGoods->updateHandlingGoods(['id_owner' => $readdress['id_customer_to']], $handlingItem['id']);
                        }

                        $invoices = $this->invoice->getInvoicesByNoReference($handling['no_handling']);
                        foreach ($invoices as $invoice) {
                            $this->invoice->updateInvoice(['id_customer' => $readdress['id_customer_to']], $invoice['id']);
                        }
                    }

                    //update workorder
                    $workOrders = $this->workOrder->getWorkOrdersByBooking($bookingId);
                    foreach ($workOrders as $workOrder) {
                        $workOrderContainers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrder['id']);
                        foreach ($workOrderContainers as $workOrderContainer) {
                            $this->workOrderContainer->updateWorkOrderContainer(['id_owner' => $readdress['id_customer_to']], $workOrderContainer['id']);
                        }

                        $workOrderGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrder['id']);
                        foreach ($workOrderGoods as $workOrderItem) {
                            $this->workOrderGoods->updateWorkOrderGoods(['id_owner' => $readdress['id_customer_to']], $workOrderItem['id']);
                        }

                        $invoices = $this->invoice->getInvoicesByNoReference($workOrder['no_work_order']);
                        foreach ($invoices as $invoice) {
                            $this->invoice->updateInvoice(['id_customer' => $readdress['id_customer_to']], $invoice['id']);
                        }
                    }

                    //update upload
                    $upload = $this->uploadModel->getUploadsByBookingId($bookingId);
                    $this->uploadModel->update([ 'id_person' => $readdress['id_customer_to']], $upload['id']);
                }

            } else {
                $alertLabel = 'warning';

                $this->readdress->update([
                    'status' => ReaddressModel::STATUS_REJECTED
                ], $id);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash($alertLabel, "Readdress booking {$readdress['no_booking']} has been {$status}");
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        redirect('readdress');
    }

    /**
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'booking' => 'trim|required',
            'customer' => 'trim|required',
            'description' => 'trim|required|max_length[500]'
        ];
    }
}