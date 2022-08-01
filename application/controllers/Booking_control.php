<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Booking_control
 * @property BookingModel $booking
 * @property BookingControlModel $bookingControl
 * @property BookingControlStatusModel $bookingControlStatus
 * @property HandlingTypeModel $handlingType
 * @property ReportModel $report
 * @property GoodsModel $goods
 * @property PeopleModel $people
 * @property WorkOrderModel $workOrder
 * @property WorkOrderContainerModel $workOrderContainer
 * @property WorkOrderGoodsModel $workOrderGoods
 */
class Booking_control extends MY_Controller
{
    /**
     * Booking_control constructor.
     */
    public function __construct()
    {
        parent::__construct();

        AuthorizationModel::mustLoggedIn();

        $this->load->model('BookingControlModel', 'bookingControl');
        $this->load->model('BookingControlStatusModel', 'bookingControlStatus');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('HandlingTypeModel', 'handlingType');
        $this->load->model('GoodsModel', 'goods');
        $this->load->model('ReportModel', 'report');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('WorkOrderContainerModel', 'workOrderContainer');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('LogModel', 'logHistory');

        $this->setFilterMethods([
            'booking_control_data' => 'GET',
            'change_status' => 'POST',
        ]);
    }

    /**
     * Show booking control data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_CONTROL_VIEW);

        $filter = get_url_param('filter') ? $_GET : [];
        $selectedOwners = key_exists('owner', $filter) ? $filter['owner'] : 0;
        $owners = $this->people->getById($selectedOwners);

        $this->render('booking_control/index', compact('owners'));

    }

    /**
     * Get ajax datatable booking control.
     */
    public function booking_control_data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_CONTROL_VIEW);

        $filters = array_merge(get_url_param('filter') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);

        $data = $this->bookingControl->getAll($filters);

        $this->render_json($data);
    }

    /**
     * Show booking control data.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_CONTROL_VIEW);

        $handlingInbounds = $this->handlingType->getHandlingTypesByMultiplier(1);
        $handlingOutbounds = $this->handlingType->getHandlingTypesByMultiplier(-1);
        $handlingStockMove = array_column(if_empty(array_merge($handlingInbounds, $handlingOutbounds), []), 'id');

        $booking = $this->bookingControl->getById($id);
        $bookingControlStatuses = $this->bookingControlStatus->getBy(['booking_control_statuses.id_booking' => $id]);

        if ($booking['category'] == 'INBOUND') {
            $inboundId = $id;
            $bookingOut = $this->booking->getBookingOutByBookingIn($id);
        } else {
            $inboundId = $booking['id_booking_in'];
            $bookingOut = [$booking];
        }

        $workOrderInbounds = $this->workOrder->getWorkOrdersByHandlingType($handlingStockMove, $inboundId);
        foreach ($workOrderInbounds as &$workOrderInbound) {
            $workOrderInbound['containers'] = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderInbound['id'], true);
            foreach ($workOrderInbound['containers'] as &$container) {
                $container['goods'] = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($container['id']);
                $container['containers'] = $this->workOrderContainer->getWorkOrderContainersByWorkOrderContainer($container['id']);
            }

            $workOrderInbound['goods'] = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderInbound['id'], true, true);
            foreach ($workOrderInbound['goods'] as &$item) {
                $item['goods'] = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderGoods($item['id']);
            }
        }

        if (!empty($bookingOut)) {
            $workOrderOutbounds = $this->workOrder->getWorkOrdersByHandlingType($handlingStockMove, array_column(if_empty($bookingOut, []), 'id'));
            foreach ($workOrderOutbounds as &$workOrderOutbound) {
                $workOrderOutbound['containers'] = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrderOutbound['id'], true);
                foreach ($workOrderOutbound['containers'] as &$container) {
                    $container['goods'] = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($container['id']);
                    $container['containers'] = $this->workOrderContainer->getWorkOrderContainersByWorkOrderContainer($container['id']);
                }

                $workOrderOutbound['goods'] = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrderOutbound['id'], true, true);
                foreach ($workOrderOutbound['goods'] as &$item) {
                    $item['goods'] = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderGoods($item['id']);
                }
            }
        }

        // stock comparator
        $containerStocks = [];
        $containerOuts = [];

        $goodsIns = [];
        $goodsOuts = [];

        $allBookings = array_merge([$booking], $bookingOut);
        foreach ($allBookings as $allBooking) {
            $inboundContainer = $this->report->getContainerStockMove(['booking' => $allBooking['id'], 'multiplier' => 1]);
            $containerStocks = array_merge($containerStocks, $inboundContainer);

            $inboundGoods = $this->report->getGoodsStockMove(['booking' => $allBooking['id'], 'multiplier' => 1]);
            $goodsIns = array_merge($goodsIns, $inboundGoods);
        }
        foreach ($allBookings as $allBooking) {
            $outbounds = $this->report->getContainerStockMove(['booking' => $allBooking['id'], 'multiplier' => -1]);
            $containerOuts = array_merge($containerOuts, $outbounds);

            $outboundGoods = $this->report->getGoodsStockMove(['booking' => $allBooking['id'], 'multiplier' => -1]);
            $goodsOuts = array_merge($goodsOuts, $outboundGoods);
        }

        foreach ($containerStocks as &$containerIn) {
            $containerIn['outbounds'] = [];
            foreach ($containerOuts as $index => &$containerOut) {
                if ($containerIn['no_container'] == $containerOut['no_container']) {
                    $containerIn['outbounds'][] = $containerOut;
                    unset($containerOuts[$index]);
                }
            }
        }

        $goodIds = array_unique(array_column($goodsIns, 'id_goods'));
        if (!empty($goodIds)) {
            $goodsStocks = $this->goods->getById($goodIds);
        } else {
            $goodsStocks = [];
        }

        foreach ($goodsStocks as &$item) {
            $item['inbounds'] = [];
            $item['outbounds'] = [];
            foreach ($goodsIns as $goods) {
                if ($item['id'] == $goods['id_goods']) {
                    $item['inbounds'][] = $goods;
                }
            }
            foreach ($goodsOuts as $goods) {
                if ($item['id'] == $goods['id_goods']) {
                    $item['outbounds'][] = $goods;
                }
            }
        }

        $data = compact('booking', 'bookingOut', 'bookingControlStatuses', 'workOrderInbounds', 'workOrderOutbounds', 'containerStocks', 'goodsStocks');

        $this->render('booking_control/view', $data);
    }

    /**
     * Default validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'status_control' => 'required|in_list[PENDING,DRAFT,CANCELED,DONE,CLEAR]',
            'description' => 'required|max_length[500]',
        ];
    }

    /**
     * Change status booking control.
     *
     * @param $id
     */
    public function change_status($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_CONTROL_MANAGE);

        if ($this->validate()) {
            $statusControl = $this->input->post('status_control');
            $description = $this->input->post('description');

            $booking = $this->booking->getBookingById($id);

            $this->db->trans_start();

            $this->booking->updateBooking([
                'status_control' => $statusControl
            ], $id);

            $this->bookingControlStatus->create([
                'id_booking' => $id,
                'status_control' => $statusControl,
                'description' => $description
            ]);

            // revert booking out when booking in revert from clear
            if ($booking['status_control'] == BookingControlModel::STATUS_CLEAR && $statusControl != BookingControlModel::STATUS_CLEAR && $booking['category'] == 'INBOUND') {
                $bookingOuts = $this->booking->getBookingOutByBookingIn($id);
                foreach ($bookingOuts as $bookingOut) {
                    if ($bookingOut['status_control'] == BookingControlModel::STATUS_CLEAR) {
                        $this->booking->updateBooking([
                            'status_control' => BookingControlModel::STATUS_DONE,
                        ], $bookingOut['id']);

                        $this->bookingControlStatus->create([
                            'id_booking' => $bookingOut['id'],
                            'status_control' => $statusControl,
                            'description' => $description
                        ]);
                    }
                }
            }

            // make booking out clear when booking in is cleared
            if ($statusControl == BookingControlModel::STATUS_CLEAR && $booking['category'] == 'INBOUND') {
                $bookingOuts = $this->booking->getBookingOutByBookingIn($id);
                $allDone = true;
                foreach ($bookingOuts as $bookingOut) {
                    if (!in_array($bookingOut['status_control'], [BookingControlStatusModel::STATUS_DONE, BookingControlStatusModel::STATUS_CLEAR])) {
                        $allDone = false;
                    }
                }

                if ($allDone) {
                    foreach ($bookingOuts as $bookingOut) {
                        $this->booking->updateBooking([
                            'status_control' => $statusControl
                        ], $bookingOut['id']);

                        $this->bookingControlStatus->create([
                            'id_booking' => $bookingOut['id'],
                            'status_control' => $statusControl,
                            'description' => 'Auto CLEAR from DONE'
                        ]);
                    }
                } else {
                    flash('danger', "Some of booking out is not DONE yet", 'booking-control/index');
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Booking {$booking['no_booking']} is set to {$statusControl}");
            } else {
                flash('danger', 'Update status control booking failed, try again or contact administrator');
            }
        }

        redirect('booking-control');
    }

}
