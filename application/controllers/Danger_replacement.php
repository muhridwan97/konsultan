<?php

/**
 * Class Readdress
 * @property BookingModel $booking
 * @property BookingContainerModel $bookingContainer
 * @property BookingGoodsModel $bookingGoods
 * @property ContainerModel $container
 * @property GoodsModel $goods
 * @property SafeConductModel $safeConduct
 * @property SafeConductContainerModel $safeConductContainer
 * @property SafeConductGoodsModel $safeConductGoods
 * @property HandlingModel $handling
 * @property HandlingContainerModel $handlingContainer
 * @property HandlingGoodsModel $handlingGoods
 * @property WorkOrderModel $workOrder
 * @property WorkOrderContainerModel $workOrderContainer
 * @property WorkOrderGoodsModel $workOrderGoods
 * @property DangerReplacementModel $dangerReplacement
 * @property ReportModel $report
 */
class Danger_replacement extends MY_Controller
{
    /**
     * Readdress constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('ContainerModel', 'container');
        $this->load->model('GoodsModel', 'goods');
        $this->load->model('SafeConductModel', 'safeConduct');
        $this->load->model('SafeConductContainerModel', 'safeConductContainer');
        $this->load->model('SafeConductGoodsModel', 'safeConductGoods');
        $this->load->model('HandlingModel', 'handlingModel');
        $this->load->model('HandlingContainerModel', 'handlingContainer');
        $this->load->model('HandlingGoodsModel', 'handlingGoods');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('WorkOrderContainerModel', 'workOrderContainer');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('HandlingModel', 'handling');
        $this->load->model('DangerReplacementModel', 'dangerReplacement');
        $this->load->model('ReportModel', 'report');
        $this->load->model('LogModel', 'logHistory');

        $this->setFilterMethods([
            'danger_replacement_data' => 'GET',
            'validate_danger_replacement' => 'POST|PUT|PATCH',
        ]);
    }

    /**
     * Show index danger replacement
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_READDRESS_VIEW);
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();

        $this->render('danger_replacement/index', [], 'Replacement of danger status');
    }

    /**
     * Get ajax datatable danger replacement.
     */
    public function danger_replacement_data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_READDRESS_VIEW);

        $filters = [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ];

        $data = $this->dangerReplacement->getAll($filters);

        $this->render_json($data);
    }

    /**
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DANGER_REPLACEMENT_VIEW);

        $dangerReplacement = $this->dangerReplacement->getById($id);

        $containers = if_empty(json_decode($dangerReplacement['containers']), []);
        $goods = if_empty(json_decode($dangerReplacement['goods']), []);

        if (!empty($containers)) {
            $containers = $this->container->getById($containers);
        }
        if (!empty($goods)) {
            $goods = $this->goods->getById($goods);
        }

        $this->render('danger_replacement/view', compact('dangerReplacement', 'containers', 'goods'), 'View danger replacement ' . $dangerReplacement['no_booking']);
    }

    /**
     * Create new danger replacement.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DANGER_REPLACEMENT_CREATE);

        $bookings = $this->report->getAvailableStockBookingList('all', ['transaction_exist' => false]);

        $this->render('danger_replacement/create', compact('bookings'), 'Create Danger Replacement');
    }

    /**
     * Save request replace danger status all activity.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DANGER_REPLACEMENT_CREATE);

        if ($this->validate()) {
            $bookingId = $this->input->post('booking');
            $statusDanger = $this->input->post('status_danger');
            $description = $this->input->post('description');
            $containers = $this->input->post('containers');
            $goods = $this->input->post('goods');

            $booking = $this->booking->getBookingById($bookingId);

            $save = $this->dangerReplacement->create([
                'id_booking' => $bookingId,
                'status_danger' => $statusDanger,
                'description' => $description,
                'containers' => json_encode($containers),
                'goods' => json_encode($goods),
                'status' => DangerReplacementModel::STATUS_PENDING,
                'created_by' => UserModel::authenticatedUserData('id')
            ]);

            if ($save) {
                flash('success', "Danger status replacement booking {$booking['no_booking']} successfully created, waiting for approval", 'danger_replacement');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->create();
    }


    /**
     * Validate booking (approve/reject)
     */
    public function validate_danger_replacement()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DANGER_REPLACEMENT_VALIDATE);

        if ($this->validate(['id' => 'required', 'status' => 'required'])) {
            $id = $this->input->post('id');
            $status = $this->input->post('status');

            $this->db->trans_start();

            $dangerReplacement = $this->dangerReplacement->getById($id);

            if ($status == DangerReplacementModel::STATUS_APPROVED) {
                $alertLabel = 'success';

                $this->dangerReplacement->update([
                    'status' => DangerReplacementModel::STATUS_APPROVED
                ], $id);

                $containers = if_empty(json_decode($dangerReplacement['containers']), []);
                $goods = if_empty(json_decode($dangerReplacement['goods']), []);

                $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($dangerReplacement['id_booking']);
                foreach ($bookingContainers as $bookingContainer) {
                    if (in_array($bookingContainer['id_container'], $containers)) {
                        $this->bookingContainer->updateBookingContainer(['status_danger' => $dangerReplacement['status_danger']], $bookingContainer['id']);
                    }
                }

                $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($dangerReplacement['id_booking']);
                foreach ($bookingGoods as $bookingItem) {
                    if (in_array($bookingItem['id_goods'], $goods)) {
                        $this->bookingGoods->updateBookingGoods(['status_danger' => $dangerReplacement['status_danger']], $bookingItem['id']);
                    }
                }

                $safeConducts = $this->safeConduct->getSafeConductsByBooking($dangerReplacement['id_booking']);
                foreach ($safeConducts as $safeConduct) {
                    $safeConductContainers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($safeConduct['id']);
                    foreach ($safeConductContainers as $safeConductContainer) {
                        if (in_array($safeConductContainer['id_container'], $containers)) {
                            $this->safeConductContainer->updateSafeConductContainer(['status_danger' => $dangerReplacement['status_danger']], $safeConductContainer['id']);
                        }
                    }

                    $safeConductGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($safeConduct['id']);
                    foreach ($safeConductGoods as $safeConductGood) {
                        if (in_array($safeConductGood['id_goods'], $goods)) {
                            $this->safeConductGoods->updateSafeConductGoods(['status_danger' => $dangerReplacement['status_danger']], $safeConductGood['id']);
                        }
                    }
                }

                $handlings = $this->handling->getHandlingsByBooking($dangerReplacement['id_booking']);
                foreach ($handlings as $handling) {
                    $handlingContainers = $this->handlingContainer->getHandlingContainersByHandling($handling['id']);
                    foreach ($handlingContainers as $handlingContainer) {
                        if (in_array($handlingContainer['id_container'], $containers)) {
                            $this->handlingContainer->updateHandlingContainer(['status_danger' => $dangerReplacement['status_danger']], $handlingContainer['id']);
                        }
                    }

                    $handlingGoods = $this->handlingGoods->getHandlingGoodsByHandling($handling['id']);
                    foreach ($handlingGoods as $handlingItem) {
                        if (in_array($handlingItem['id_goods'], $goods)) {
                            $this->handlingGoods->updateHandlingGoods(['status_danger' => $dangerReplacement['status_danger']], $handlingItem['id']);
                        }
                    }
                }

                $workOrders = $this->workOrder->getWorkOrdersByBooking($dangerReplacement['id_booking']);
                foreach ($workOrders as $workOrder) {
                    $workOrderContainers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrder['id']);
                    foreach ($workOrderContainers as $workOrderContainer) {
                        if (in_array($workOrderContainer['id_container'], $containers)) {
                            $this->workOrderContainer->updateWorkOrderContainer(['status_danger' => $dangerReplacement['status_danger']], $workOrderContainer['id']);
                        }
                    }

                    $workOrderGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrder['id']);
                    foreach ($workOrderGoods as $workOrderItem) {
                        if (in_array($workOrderItem['id_goods'], $goods)) {
                            $this->workOrderGoods->updateWorkOrderGoods(['status_danger' => $dangerReplacement['status_danger']], $workOrderItem['id']);
                        }
                    }
                }

            } else {
                $alertLabel = 'warning';

                $this->dangerReplacement->update([
                    'status' => DangerReplacementModel::STATUS_REJECTED
                ], $id);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash($alertLabel, "All data in booking {$dangerReplacement['no_booking']} has been {$status}");
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        redirect('danger_replacement');
    }

    /**
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'booking' => 'trim|required',
            'total_items' => 'trim|required|greater_than_equal_to[1]',
            'status_danger' => 'trim|required',
            'description' => 'trim|required|max_length[500]'
        ];
    }
}