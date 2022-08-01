<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Change_ownership extends CI_Controller
{
    /**
     * Handling request constructor.
     */
    public function __construct()
    {
        parent::__construct();

        AuthorizationModel::mustLoggedIn();

        $this->load->model('ChangeOwnershipModel', 'changeOwnership');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('HandlingModel', 'handling');
        $this->load->model('HandlingContainerModel', 'handlingContainer');
        $this->load->model('HandlingGoodsModel', 'handlingGoods');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('WorkOrderContainerModel', 'workOrderContainer');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('UnitModel', 'units');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('LogModel', 'logHistory');
    }

    /**
     * Show handling request data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OWNERSHIP_VIEW);

        $changeOwnerships = $this->changeOwnership->getAllChangeOwnerships();
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();
        
        $data = [
            'title' => "Change Ownership",
            'subtitle' => "Data change ownership",
            'page' => "changeownership/index",
            'changeOwnerships' => $changeOwnerships
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * View ownership.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OWNERSHIP_VIEW);

        $changeOwnership = $this->changeOwnership->getChangeOwnershipById($id);
        $changeOwnershipContainers = $this->bookingContainer->getBookingContainersByBooking($changeOwnership['id_booking_in']);
        $changeOwnershipGoods = $this->bookingGoods->getBookingGoodsByBooking($changeOwnership['id_booking_in']);
        $data = [
            'title' => "View Change Ownership",
            'subtitle' => "View change ownership",
            'page' => "changeownership/view",
            'changeOwnership' => $changeOwnership,
            'changeOwnershipContainers' => $changeOwnershipContainers,
            'changeOwnershipGoods' => $changeOwnershipGoods
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show ownership history by delivery order.
     * @param $idDeliveryOrder
     */
    public function deliveryOrder($idDeliveryOrder)
    {
        $ownershiphistories = $this->ownershiphistories->getOwnershipHistoriesByDeliveryOrder($idDeliveryOrder);
        $data = [
            'title' => "Ownership Histories",
            'subtitle' => "Data change ownership",
            'page' => "changeownership/deliveryorder",
            'ownershiphistories' => $ownershiphistories
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Create ownership history.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OWNERSHIP_CREATE);

        $data = [
            'title' => "Change Ownership",
            'subtitle' => "Create new change ownership",
            'page' => "changeownership/create"
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Edit change ownership.
     * @param $id
     */
    public function edit($id = null)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OWNERSHIP_EDIT);

        $changeOwnership = $this->changeOwnership->getChangeOwnershipById($id);
        $currentOwner = $this->people->getById($changeOwnership['id_owner_from']);
        $newOwner = $this->people->getById($changeOwnership['id_owner_to']);
        $booking = $this->booking->getBookingById($changeOwnership['id_booking']);
        $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($changeOwnership['id_booking']);
        $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($changeOwnership['id_booking']);
        $bookingOfCustomer = $this->booking->getBookingStocksByCustomer($changeOwnership['id_owner_from']);
        $data = [
            'title' => "Change Ownership",
            'subtitle' => "Edit change ownership",
            'page' => "changeownership/edit",
            'changeOwnership' => $changeOwnership,
            'currentOwner' => $currentOwner,
            'newOwner' => $newOwner,
            'booking' => $booking,
            'bookingContainers' => $bookingContainers,
            'bookingGoods' => $bookingGoods,
            'bookingOfCustomer' => $bookingOfCustomer
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Get list histories.
     */
    public function getListHistories()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OWNERSHIP_VIEW);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $idDeliveryOrder = $this->input->post('idDeliveryOrder');

            $ownershiphistories = $this->ownershiphistories->getOwnershipHistoriesByDeliveryOrder($idDeliveryOrder);
            $data = [
                'ownershiphistories' => $ownershiphistories,
            ];
            $this->load->view('changeownership/histories', $data);
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
            redirect('change_ownership/create');
        }
    }

    /**
     * Save new ownership history.
     */
    public function save()
    {
        // Change ownership will create new booking. handling and workorder
        // Stock out from current owner than stock in to new owner
        // Serial number for booking, handling and workorder :
        // Booking Out = CO, Booking In = CI
        // Handling Out = HO, Handling In = HI
        // Workorder Out = WO, Workorder In = WI

        AuthorizationModel::mustAuthorized(PERMISSION_OWNERSHIP_CREATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('currentOwner', 'Current Owner', 'trim|required');
            $this->form_validation->set_rules('newOwner', 'New Owner', 'trim|required');
            $this->form_validation->set_rules('booking', 'Booking', 'trim|required');
            $this->form_validation->set_rules('changeDate', 'Change Date', 'trim|required');
            $this->form_validation->set_rules('description', 'Description', 'trim');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $currentOwner = $this->input->post('currentOwner');
                $newOwner = $this->input->post('newOwner');
                $bookingId = $this->input->post('booking');
                $changeDate = sql_date_format($this->input->post('changeDate'));
                $description = $this->input->post('description');

                $containers = $this->input->post('containers');
                $containerSeals = $this->input->post('container_seals');
                $containerPositions = $this->input->post('container_positions');
                $containerIsEmpty = $this->input->post('container_is_empty');
                $containerStatuses = $this->input->post('container_statuses');
                $containerWarehouses = $this->input->post('container_warehouses');

                $containerData = [];
                for ($i = 0; $i < count($containers); $i++) {
                    $containerData[$i]['id_container'] = $containers[$i];
                    $containerData[$i]['id_warehouse'] = $containerWarehouses[$i];
                    $containerData[$i]['id_position'] = $containerPositions[$i];
                    $containerData[$i]['seal'] = $containerSeals[$i];
                    $containerData[$i]['is_empty'] = $containerIsEmpty[$i];
                    $containerData[$i]['status'] = $containerStatuses[$i];
                }

                $goods = $this->input->post('goods');
                $quantities = $this->input->post('quantities');
                $tonnages = $this->input->post('tonnages');
                $volumes = $this->input->post('volumes');
                $units = $this->input->post('units');
                $positions = $this->input->post('positions');
                $noPallets = $this->input->post('no_pallets');
                $noDOs = $this->input->post('no_delivery_orders');
                $statuses = $this->input->post('statuses');
                $warehouses = $this->input->post('warehouses');

                $goodsData = [];
                for ($i = 0; $i < count($goods); $i++) {
                    $goodsData[$i]['id_goods'] = $goods[$i];
                    $goodsData[$i]['id_warehouse'] = $warehouses[$i];
                    $goodsData[$i]['id_position'] = $positions[$i];
                    $goodsData[$i]['id_unit'] = $units[$i];
                    $goodsData[$i]['quantity'] = $quantities[$i];
                    $goodsData[$i]['tonnage'] = $tonnages[$i];
                    $goodsData[$i]['volume'] = $volumes[$i];
                    $goodsData[$i]['no_pallet'] = $noPallets[$i];
                    $goodsData[$i]['no_delivery_order'] = $noDOs[$i];
                    $goodsData[$i]['status'] = $statuses[$i];
                }

                if (!empty($warehouses)) {
                    $warehouseId = end($warehouses);
                } else if (!empty($containerWarehouses)) {
                    $warehouseId = end($containerWarehouses);
                } else {
                    $warehouseId = '';
                }

                $this->db->trans_start();

                // get current booking
                $currentBooking = $this->booking->getBookingById($bookingId);

                $outbound = $this->createOutboundByChangeOwner($currentBooking, $currentOwner, $newOwner, $changeDate, $description, $warehouseId, $containerData, $goodsData);
                $inbound = $this->createInboundByChangeOwner($currentBooking, $currentOwner, $newOwner, $changeDate, $description, $warehouseId, $containerData, $goodsData);

                // create change ownership transactions
                $noChangeOwnership = $this->changeOwnership->getAutoNumberChangeOwnership();
                $this->changeOwnership->createChangeOwnership([
                    'no_change_ownership' => $noChangeOwnership,
                    'id_booking' => $bookingId,
                    'id_booking_out' => $outbound['bookingId'],
                    'id_booking_in' => $inbound['bookingId'],
                    'id_handling_out' => $outbound['handlingId'],
                    'id_handling_in' => $inbound['handlingId'],
                    'id_work_order_out' => $outbound['workorderId'],
                    'id_work_order_in' => $inbound['workorderId'],
                    'change_date' => $changeDate,
                    'id_owner_from' => $currentOwner,
                    'id_owner_to' => $newOwner,
                    'description' => $description
                ]);

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Ownership has been changed");
                    redirect("change_ownership");
                } else {
                    flash('danger', "Save handling request failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->create();
    }

    /**
     * Create outbound process for change ownership.
     * @param $currentBooking
     * @param $currentOwner
     * @param $newOwner
     * @param $changeDate
     * @param $description
     * @param $warehouseId
     * @param $containers
     * @param $goods
     * @return array
     */
    private function createOutboundByChangeOwner($currentBooking, $currentOwner, $newOwner, $changeDate, $description, $warehouseId, $containers, $goods)
    {
        $newOwnerData = $this->people->getById($newOwner);

        // create a new booking to remove items from stock
        $noBookingOut = $this->booking->getAutoNumberBooking(BookingModel::NO_CHANGEOWNER_OUT);
        $this->booking->createBooking([
            'no_booking' => $noBookingOut,
            'id_booking' => $currentBooking['id'],
            'id_booking_type' => 2,
            'id_supplier' => $currentBooking['id_supplier'],
            'id_customer' => $currentOwner,
            'id_branch' => $currentBooking['id_branch'],
            'id_upload' => $currentBooking['id_upload'],
            'no_reference' => $currentBooking['no_reference'],
            'reference_date' => $currentBooking['reference_date'],
            'vessel' => $currentBooking['vessel'],
            'voyage' => $currentBooking['voyage'],
            'booking_date' => $changeDate,
            'mode' => $currentBooking['mode'],
            'description' => $currentBooking['description'] . ", Change Ownership: " . $description,
            'status' => BookingModel::STATUS_COMPLETED,
            'created_by' => UserModel::authenticatedUserData('id')
        ]);
        $bookingIdOut = $this->db->insert_id();

        // create a new handling out
        $noHandling = $this->handling->getAutoNumberHandlingRequest('HO');
        $handlingTypeId = get_setting('default_outbound_handling');
        $this->handling->createHandling([
            'no_handling' => $noHandling,
            'id_booking' => $bookingIdOut,
            'id_handling_type' => $handlingTypeId,
            'id_customer' => $currentOwner,
            'handling_date' => $changeDate,
            'status' => 'APPROVED',
            'description' => 'Change Ownership to ' . $newOwnerData['name'],
            'validated_by' => UserModel::authenticatedUserData('id'),
            'created_by' => UserModel::authenticatedUserData('id')
        ]);
        $handlingIdOut = $this->db->insert_id();

        // create a new workorder out
        $noWorkOrder = $this->workOrder->getAutoNumberWorkOrder('CO');
        $this->workOrder->createWorkOrder([
            'no_work_order' => $noWorkOrder,
            'id_handling' => $handlingIdOut,
            'id_warehouse' => $warehouseId,
            'description' => 'Change Ownership to ' . $newOwnerData['name'],
            'gate_in_date' => $changeDate,
            'gate_in_description' => 'Change Ownership to ' . $newOwnerData['name'],
            'gate_out_date' => $changeDate,
            'gate_out_description' => 'Change Ownership to ' . $newOwnerData['name'],
            'status' => 'COMPLETED',
            'taken_by' => UserModel::authenticatedUserData('id'),
            'taken_at' => $changeDate,
            'completed_at' => $changeDate,
            'mode' => $currentBooking['mode'],
            'created_by' => UserModel::authenticatedUserData('id')
        ]);
        $workOrderIdOut = $this->db->insert_id();

        if (!empty($containers)) {
            foreach ($containers as $container) {
                $this->bookingContainer->createBookingContainer([
                    'id_booking' => $bookingIdOut,
                    'id_container' => $container['id_container'],
                    'id_position' => if_empty($container['id_position'], null),
                    'seal' => $container['seal'],
                    'is_empty' => $container['is_empty'],
                    'status' => $container['status'],
                    'quantity' => 1,
                ]);

                $this->handlingContainer->createHandlingContainer([
                    'id_handling' => $handlingIdOut,
                    'id_owner' => $currentOwner,
                    'id_container' => $container['id_container'],
                    'id_position' => if_empty($container['id_position'], null),
                    'seal' => $container['seal'],
                    'is_empty' => $container['is_empty'],
                    'status' => $container['status'],
                    'quantity' => 1,
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);

                $this->workOrderContainer->insertWorkOrderContainer([
                    'id_work_order' => $workOrderIdOut,
                    'id_owner' => $currentOwner,
                    'id_container' => $container['id_container'],
                    'id_position' => if_empty($container['id_position'], null),
                    'description' => $container['description'],
                    'seal' => $container['seal'],
                    'is_empty' => $container['is_empty'],
                    'status' => $container['status'],
                    'quantity' => 1,
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);
            }
        }

        // insert goods if any
        if (!empty($goods)) {
            foreach ($goods as $item) {
                $this->bookingGoods->createBookingGoods([
                    'id_booking' => $bookingIdOut,
                    'id_goods' => $item['id_goods'],
                    'id_unit' => $item['id_unit'],
                    'id_position' => if_empty($item['id_position'], null),
                    'quantity' => $item['quantity'],
                    'tonnage' => $item['tonnage'],
                    'volume' => $item['volume'],
                    'no_pallet' => $item['no_pallet'],
                    'no_delivery_order' => $item['no_delivery_order'],
                    'status' => $item['status'],
                ]);

                $this->handlingGoods->createHandlingGoods([
                    'id_handling' => $handlingIdOut,
                    'id_owner' => $currentOwner,
                    'id_goods' => $item['id_goods'],
                    'id_unit' => $item['id_unit'],
                    'id_position' => if_empty($item['id_position'], null),
                    'quantity' => $item['quantity'],
                    'tonnage' => $item['tonnage'],
                    'volume' => $item['volume'],
                    'no_pallet' => $item['no_pallet'],
                    'no_delivery_order' => $item['no_delivery_order'],
                    'status' => $item['status'],
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);

                $this->workOrderGoods->insertWorkOrderGoods([
                    'id_work_order' => $workOrderIdOut,
                    'id_owner' => $currentOwner,
                    'id_goods' => $item['id_goods'],
                    'id_unit' => $item['id_unit'],
                    'id_position' => if_empty($item['id_position'], null),
                    'quantity' => $item['quantity'],
                    'unit_weight' => $item['unit_weight'],
                    'unit_gross_weight' => $item['unit_gross_weight'],
                    'unit_length' => $item['unit_length'],
                    'unit_width' => $item['unit_width'],
                    'unit_height' => $item['unit_height'],
                    'unit_volume' => $item['unit_volume'],
                    'no_pallet' => $item['no_pallet'],
                    'no_delivery_order' => $item['no_delivery_order'],
                    'status' => $item['status'],
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);
            }
        }

        return [
            'bookingId' => $bookingIdOut,
            'handlingId' => $handlingIdOut,
            'workorderId' => $workOrderIdOut,
        ];
    }

    /**
     * Create inbound process for change ownership.
     * @param $currentBooking
     * @param $currentOwner
     * @param $newOwner
     * @param $changeDate
     * @param $description
     * @param $warehouseId
     * @param $containers
     * @param $goods
     * @return array
     */
    private function createInboundByChangeOwner($currentBooking, $currentOwner, $newOwner, $changeDate, $description, $warehouseId, $containers, $goods)
    {
        $currentOwnerData = $this->people->getById($currentOwner);

        // create a new booking for new customer
        $noBookingIn = $this->booking->getAutoNumberBooking(BookingModel::NO_CHANGEOWNER_IN);
        $this->booking->createBooking([
            'no_booking' => $noBookingIn,
            'id_booking_type' => 1,
            'id_supplier' => $currentBooking['id_supplier'],
            'id_customer' => $newOwner,
            'id_branch' => $currentBooking['id_branch'],
            'id_upload' => $currentBooking['id_upload'],
            'no_reference' => $currentBooking['no_reference'],
            'reference_date' => $currentBooking['reference_date'],
            'vessel' => $currentBooking['vessel'],
            'voyage' => $currentBooking['voyage'],
            'booking_date' => $changeDate,
            'mode' => $currentBooking['mode'],
            'description' => $description,
            'status' => BookingModel::STATUS_COMPLETED,
            'created_by' => UserModel::authenticatedUserData('id')
        ]);
        $bookingIdIn = $this->db->insert_id();

        // create a new handling out
        $noHandling = $this->handling->getAutoNumberHandlingRequest('HI');
        $handlingTypeId = get_setting('default_inbound_handling');
        $this->handling->createHandling([
            'no_handling' => $noHandling,
            'id_booking' => $bookingIdIn,
            'id_handling_type' => $handlingTypeId,
            'id_customer' => $newOwner,
            'handling_date' => $changeDate,
            'status' => 'APPROVED',
            'description' => 'Change Ownership from ' . $currentOwnerData['name'],
            'validated_by' => UserModel::authenticatedUserData('id'),
            'created_by' => UserModel::authenticatedUserData('id')
        ]);
        $handlingIdIn = $this->db->insert_id();

        // create a new workorder out
        $noWorkOrder = $this->workOrder->getAutoNumberWorkOrder('CI');
        $this->workOrder->createWorkOrder([
            'no_work_order' => $noWorkOrder,
            'id_handling' => $handlingIdIn,
            'id_warehouse' => $warehouseId,
            'description' => 'Change Ownership from ' . $currentOwnerData['name'],
            'gate_in_date' => $changeDate,
            'gate_in_description' => 'Change Ownership from ' . $currentOwnerData['name'],
            'gate_out_date' => $changeDate,
            'gate_out_description' => 'Change Ownership from ' . $currentOwnerData['name'],
            'status' => 'COMPLETED',
            'taken_by' => UserModel::authenticatedUserData('id'),
            'taken_at' => $changeDate,
            'completed_at' => $changeDate,
            'mode' => $currentBooking['mode'],
            'created_by' => UserModel::authenticatedUserData('id')
        ]);
        $workOrderIdIn = $this->db->insert_id();

        if (!empty($containers)) {
            foreach ($containers as $container) {
                $this->bookingContainer->createBookingContainer([
                    'id_booking' => $bookingIdIn,
                    'id_container' => $container['id_container'],
                    'id_position' => if_empty($container['id_position'], null),
                    'seal' => $container['seal'],
                    'is_empty' => $container['is_empty'],
                    'status' => $container['status'],
                    'quantity' => 1,
                ]);

                $this->handlingContainer->createHandlingContainer([
                    'id_handling' => $handlingIdIn,
                    'id_owner' => $newOwner,
                    'id_container' => $container['id_container'],
                    'id_position' => if_empty($container['id_position'], null),
                    'seal' => $container['seal'],
                    'is_empty' => $container['is_empty'],
                    'status' => $container['status'],
                    'quantity' => 1,
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);

                $this->workOrderContainer->insertWorkOrderContainer([
                    'id_work_order' => $workOrderIdIn,
                    'id_owner' => $newOwner,
                    'id_container' => $container['id_container'],
                    'id_position' => if_empty($container['id_position'], null),
                    'description' => $container['description'],
                    'seal' => $container['seal'],
                    'is_empty' => $container['is_empty'],
                    'status' => $container['status'],
                    'quantity' => 1,
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);
            }
        }

        // insert goods if any
        if (!empty($goods)) {
            foreach ($goods as $item) {
                $this->bookingGoods->createBookingGoods([
                    'id_booking' => $bookingIdIn,
                    'id_goods' => $item['id_goods'],
                    'id_unit' => $item['id_unit'],
                    'id_position' => if_empty($item['id_position'], null),
                    'quantity' => $item['quantity'],
                    'tonnage' => $item['tonnage'],
                    'volume' => $item['volume'],
                    'no_pallet' => $item['no_pallet'],
                    'no_delivery_order' => $item['no_delivery_order'],
                    'status' => $item['status'],
                ]);

                $this->handlingGoods->createHandlingGoods([
                    'id_handling' => $handlingIdIn,
                    'id_owner' => $newOwner,
                    'id_goods' => $item['id_goods'],
                    'id_unit' => $item['id_unit'],
                    'id_position' => if_empty($item['id_position'], null),
                    'quantity' => $item['quantity'],
                    'tonnage' => $item['tonnage'],
                    'volume' => $item['volume'],
                    'no_pallet' => $item['no_pallet'],
                    'no_delivery_order' => $item['no_delivery_order'],
                    'status' => $item['status'],
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);

                $this->workOrderGoods->insertWorkOrderGoods([
                    'id_work_order' => $workOrderIdIn,
                    'id_owner' => $newOwner,
                    'id_goods' => $item['id_goods'],
                    'id_unit' => $item['id_unit'],
                    'id_position' => if_empty($item['id_position'], null),
                    'quantity' => $item['quantity'],
                    'unit_weight' => $item['unit_weight'],
                    'unit_gross_weight' => $item['unit_gross_weight'],
                    'unit_length' => $item['unit_length'],
                    'unit_width' => $item['unit_width'],
                    'unit_height' => $item['unit_height'],
                    'unit_volume' => $item['unit_volume'],
                    'no_pallet' => $item['no_pallet'],
                    'no_delivery_order' => $item['no_delivery_order'],
                    'status' => $item['status'],
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);
            }
        }

        return [
            'bookingId' => $bookingIdIn,
            'handlingId' => $handlingIdIn,
            'workorderId' => $workOrderIdIn,
        ];
    }

    /**
     * Update data change ownership by id.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OWNERSHIP_EDIT);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('currentOwner', 'Current Owner', 'trim|required');
            $this->form_validation->set_rules('newOwner', 'New Owner', 'trim|required');
            $this->form_validation->set_rules('booking', 'Booking', 'trim|required');
            $this->form_validation->set_rules('changeDate', 'Change Date', 'trim|required');
            $this->form_validation->set_rules('description', 'Description', 'trim');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $currentOwner = $this->input->post('currentOwner');
                $newOwner = $this->input->post('newOwner');
                $bookingId = $this->input->post('booking');
                $changeDate = (new DateTime($this->input->post('changeDate')))->format('Y-m-d H:i:s');
                $description = $this->input->post('description');

                $containers = $this->input->post('containers');
                $goods = $this->input->post('goods');
                $quantities = $this->input->post('quantities');
                $tonnages = $this->input->post('tonnages');
                $volumes = $this->input->post('volumes');

                $this->db->trans_start();

                // TODO: check if ownership has changed to another owner

                // get current Change Ownership
                $currentChangeOwner = $this->changeOwnership->getChangeOwnershipById($id);

                // get current Booking
                $currentBooking = $this->booking->getBookingById($bookingId);

                $outbound = $this->updateOutboundByChangeOwner($currentChangeOwner, $currentBooking, $currentOwner, $changeDate, $description,
                    $containers, $goods, $quantities, $tonnages, $volumes);
                $inbound = $this->updateInboundByChangeOwner($currentChangeOwner, $currentBooking, $newOwner, $changeDate, $description,
                    $containers, $goods, $quantities, $tonnages, $volumes);

                $update = $this->changeOwnership->updateOwnershipHistory([
                    'id_booking' => $bookingId,
                    'id_booking_out' => $outbound['bookingId'],
                    'id_booking_in' => $inbound['bookingId'],
                    'id_handling_out' => $outbound['handlingId'],
                    'id_handling_in' => $inbound['handlingId'],
                    'id_work_order_out' => $outbound['workorderId'],
                    'id_work_order_in' => $inbound['workorderId'],
                    'change_date' => $changeDate,
                    'id_owner_from' => $currentOwner,
                    'id_owner_to' => $newOwner,
                    'description' => $description
                ], $id);

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Ownership history successfully updated");
                    redirect("change_ownership");
                } else {
                    flash('danger', "Update ownership history failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->edit($id);
    }

    private function updateOutboundByChangeOwner($currentChangeOwner, $currentBooking, $currentOwner, $changeDate, $description,
                                                 $containers, $goods, $quantities, $tonnages, $volumes)
    {
        $bookingIdOut = $currentChangeOwner['id_booking_out'];
        $handlingIdOut = $currentChangeOwner['id_handling_out'];
        $workOrderIdOut = $currentChangeOwner['id_work_order_out'];

        $this->booking->updateBooking([
            'id_booking' => $currentBooking['id'],
            'id_booking_type' => 2,
            'id_supplier' => $currentBooking['id_supplier'],
            'id_customer' => $currentOwner,
            'id_upload' => $currentBooking['id_upload'],
            'booking_date' => $changeDate,
            'mode' => $currentBooking['mode'],
            'description' => $description,
            'status' => BookingModel::STATUS_COMPLETED,
            'created_by' => UserModel::authenticatedUserData('id')
        ], $bookingIdOut);

        // create a new handling out
        $handlingTypeId = get_setting('default_outbound_handling');
        $this->handling->updateHandling([
            'id_booking' => $bookingIdOut,
            'id_handling_type' => $handlingTypeId,
            'id_customer' => $currentOwner,
            'handling_date' => $changeDate,
            'status' => 'APPROVED',
            'description' => 'Change Ownership',
            'validated_by' => UserModel::authenticatedUserData('id'),
            'created_by' => UserModel::authenticatedUserData('id')
        ], $handlingIdOut);

        // create a new workorder out
        $this->workOrder->updateWorkOrder([
            'id_handling' => $handlingIdOut, // should EXIST, from new record or scanned handling number
            'description' => $description,
            'gate_in_date' => $changeDate,
            'gate_in_description' => 'Change Ownership',
            'gate_out_date' => $changeDate,
            'gate_out_description' => 'Change Ownership',
            'status' => 'COMPLETED',
            'mode' => $currentBooking['mode'],
            'created_by' => UserModel::authenticatedUserData('id')
        ], $workOrderIdOut);

        // delete existing detail before create new
        $this->bookingContainer->deleteBookingContainerByBooking($bookingIdOut);
        $this->handlingContainer->deleteHandlingContainersByHandling($handlingIdOut);
        $this->workOrderContainer->deleteContainersByWorkOrder($workOrderIdOut);

        $this->bookingGoods->deleteBookingGoodsByBooking($bookingIdOut);
        $this->handlingGoods->deleteHandlingGoodsByHandling($handlingIdOut);
        $this->workOrderGoods->deleteGoodsByWorkOrder($workOrderIdOut);

        // insert booking's containers if any
        if (!empty($containers)) {
            foreach ($containers as $container) {
                $containerStock = $this->workOrderContainer->getWorkOrderContainerById($container);
                if (!empty($containerStock)) {
                    $this->bookingContainer->createBookingContainer([
                        'id_booking' => $bookingIdOut,
                        'id_container' => $containerStock['id_container'],
                        'seal' => $containerStock['seal'],
                        'description' => $containerStock['description'],
                        'quantity' => 1
                    ]);

                    $this->handlingContainer->createHandlingContainer([
                        'id_handling' => $handlingIdOut,
                        'id_work_order_container' => $container,
                        'id_owner' => $containerStock['id_owner'],
                        'id_container' => $containerStock['id_container'],
                        'id_position' => if_empty($containerStock['id_position'], null),
                        'quantity' => $containerStock['quantity'],
                        'seal' => $containerStock['seal'],
                        'description' => $containerStock['description'],
                    ]);

                    $this->workOrderContainer->insertWorkOrderContainer([
                        'id_work_order' => $workOrderIdOut,
                        'id_owner' => $containerStock['id_owner'],
                        'id_container' => $containerStock['id_container'],
                        'id_position' => if_empty($containerStock['id_position'], null),
                        'description' => $containerStock['description'],
                        'seal' => $containerStock['seal'],
                        'quantity' => 1,
                        'created_by' => UserModel::authenticatedUserData('id')
                    ]);
                }
            }
        }

        // insert goods if any
        if (!empty($goods)) {
            $itemIndex = 0;
            foreach ($goods as $item) {
                $itemStock = $this->workOrderGoods->getWorkOrderGoodsById($item);
                if (!empty($item)) {
                    $this->bookingGoods->createBookingGoods([
                        'id_booking' => $bookingIdOut,
                        'id_goods' => $itemStock['id_goods'],
                        'id_unit' => $itemStock['id_unit'],
                        'quantity' => key_exists($itemIndex, $quantities) ? $quantities[$itemIndex] : $itemStock['quantity'],
                        'tonnage' => key_exists($itemIndex, $tonnages) ? $tonnages[$itemIndex] : $itemStock['tonnage'],
                        'volume' => key_exists($itemIndex, $volumes) ? $volumes[$itemIndex] : $itemStock['volume'],
                        'description' => $itemStock['description']
                    ]);

                    $this->handlingGoods->createHandlingGoods([
                        'id_handling' => $handlingIdOut,
                        'id_work_order_goods' => $item,
                        'id_owner' => $itemStock['id_owner'],
                        'id_goods' => $itemStock['id_goods'],
                        'id_unit' => $itemStock['id_unit'],
                        'id_position' => if_empty($itemStock['id_position'], null),
                        'quantity' => key_exists($itemIndex, $quantities) ? $quantities[$itemIndex] : $itemStock['quantity'],
                        'unit_weight' => key_exists($itemIndex, $tonnages) ? $tonnages[$itemIndex] : $itemStock['unit_weight'],
                        'unit_volume' => key_exists($itemIndex, $volumes) ? $volumes[$itemIndex] : $itemStock['unit_volume'],
                        'no_pallet' => $itemStock['no_pallet'],
                        'description' => $itemStock['description'],
                    ]);

                    $this->workOrderGoods->insertWorkOrderGoods([
                        'id_work_order' => $workOrderIdOut,
                        'id_owner' => $itemStock['id_owner'],
                        'id_goods' => $itemStock['id_goods'],
                        'id_unit' => $itemStock['id_unit'],
                        'id_position' => if_empty($itemStock['id_position'], null),
                        'quantity' => key_exists($itemIndex, $quantities) ? $quantities[$itemIndex] : $itemStock['quantity'],
                        'unit_weight' => key_exists($itemIndex, $tonnages) ? $tonnages[$itemIndex] : $itemStock['unit_weight'],
                        'unit_volume' => key_exists($itemIndex, $volumes) ? $volumes[$itemIndex] : $itemStock['unit_volume'],
                        'no_pallet' => $itemStock['no_pallet'],
                        'created_by' => UserModel::authenticatedUserData('id')
                    ]);
                }
                $itemIndex++;
            }
        }
    }

    private function updateInboundByChangeOwner($currentChangeOwner, $currentBooking, $newOwner, $changeDate, $description,
                                                $containers, $goods, $quantities, $tonnages, $volumes)
    {
        $bookingIdIn = $currentChangeOwner['id_booking_in'];
        $handlingIdIn = $currentChangeOwner['id_handling_in'];
        $workOrderIdIn = $currentChangeOwner['id_work_order_in'];

        $this->booking->updateBooking([
            'id_booking_type' => 1, // TODO: change this to inbound default or new reserved booking type for change owner
            'id_supplier' => $currentBooking['id_supplier'],
            'id_customer' => $newOwner,
            'id_upload' => $currentBooking['id_upload'],
            'booking_date' => $changeDate,
            'mode' => $currentBooking['mode'],
            'description' => $description,
            'status' => BookingModel::STATUS_COMPLETED,
            'created_by' => UserModel::authenticatedUserData('id')
        ], $bookingIdIn);

        $handlingTypeId = get_setting('default_inbound_handling');
        $this->handling->updateHandling([
            'id_handling_type' => $handlingTypeId,
            'id_customer' => $newOwner,
            'handling_date' => $changeDate,
            'status' => 'APPROVED',
            'description' => 'Change Ownership',
            'validated_by' => UserModel::authenticatedUserData('id'),
            'created_by' => UserModel::authenticatedUserData('id')
        ], $handlingIdIn);

        $this->workOrder->createWorkOrder([
            'description' => $description,
            'gate_in_date' => $changeDate,
            'gate_in_description' => 'Change Ownership',
            'gate_out_date' => $changeDate,
            'gate_out_description' => 'Change Ownership',
            'status' => 'COMPLETED',
            'mode' => $currentBooking['mode'],
            'created_by' => UserModel::authenticatedUserData('id')
        ], $workOrderIdIn);

        // delete existing detail before create new
        $this->bookingContainer->deleteBookingContainerByBooking($bookingIdIn);
        $this->handlingContainer->deleteHandlingContainersByHandling($handlingIdIn);
        $this->workOrderContainer->deleteContainersByWorkOrder($workOrderIdIn);

        $this->bookingGoods->deleteBookingGoodsByBooking($bookingIdIn);
        $this->handlingGoods->deleteHandlingGoodsByHandling($handlingIdIn);
        $this->workOrderGoods->deleteGoodsByWorkOrder($workOrderIdIn);

        // insert booking's containers if any
        if (!empty($containers)) {
            foreach ($containers as $container) {
                $containerStock = $this->workOrderContainer->getWorkOrderContainerById($container);
                if (!empty($containerStock)) {
                    $this->bookingContainer->createBookingContainer([
                        'id_booking' => $bookingIdIn,
                        'id_container' => $containerStock['id_container'],
                        'seal' => $containerStock['seal'],
                        'description' => $containerStock['description'],
                        'quantity' => 1
                    ]);

                    $this->handlingContainer->createHandlingContainer([
                        'id_handling' => $handlingIdIn,
                        'id_work_order_container' => $container,
                        'id_owner' => $containerStock['id_owner'],
                        'id_container' => $containerStock['id_container'],
                        'id_position' => if_empty($containerStock['id_position'], null),
                        'quantity' => $containerStock['quantity'],
                        'seal' => $containerStock['seal'],
                        'description' => $containerStock['description'],
                    ]);

                    $this->workOrderContainer->insertWorkOrderContainer([
                        'id_work_order' => $workOrderIdIn,
                        'id_owner' => $containerStock['id_owner'],
                        'id_container' => $containerStock['id_container'],
                        'id_position' => if_empty($containerStock['id_position'], null),
                        'description' => $containerStock['description'],
                        'seal' => $containerStock['seal'],
                        'quantity' => 1,
                        'created_by' => UserModel::authenticatedUserData('id')
                    ]);
                }
            }
        }

        // insert goods if any
        if (!empty($goods)) {
            $itemIndex = 0;
            foreach ($goods as $item) {
                $itemStock = $this->workOrderGoods->getWorkOrderGoodsById($item);

                if (!empty($item)) {
                    $this->bookingGoods->createBookingGoods([
                        'id_booking' => $bookingIdIn,
                        'id_goods' => $itemStock['id_goods'],
                        'id_unit' => $itemStock['id_unit'],
                        'quantity' => key_exists($itemIndex, $quantities) ? $quantities[$itemIndex] : $itemStock['quantity'],
                        'tonnage' => key_exists($itemIndex, $tonnages) ? $tonnages[$itemIndex] : $itemStock['tonnage'],
                        'volume' => key_exists($itemIndex, $volumes) ? $volumes[$itemIndex] : $itemStock['volume'],
                        'description' => $itemStock['description']
                    ]);

                    $this->handlingGoods->createHandlingGoods([
                        'id_handling' => $handlingIdIn,
                        'id_work_order_goods' => $item,
                        'id_owner' => $itemStock['id_owner'],
                        'id_goods' => $itemStock['id_goods'],
                        'id_unit' => $itemStock['id_unit'],
                        'id_position' => if_empty($itemStock['id_position'], null),
                        'quantity' => key_exists($itemIndex, $quantities) ? $quantities[$itemIndex] : $itemStock['quantity'],
                        'tonnage' => key_exists($itemIndex, $tonnages) ? $tonnages[$itemIndex] : $itemStock['tonnage'],
                        'volume' => key_exists($itemIndex, $volumes) ? $volumes[$itemIndex] : $itemStock['volume'],
                        'no_pallet' => $itemStock['no_pallet'],
                        'description' => $itemStock['description'],
                    ]);

                    $this->workOrderGoods->insertWorkOrderGoods([
                        'id_work_order' => $workOrderIdIn,
                        'id_owner' => $itemStock['id_owner'],
                        'id_goods' => $itemStock['id_goods'],
                        'id_unit' => $itemStock['id_unit'],
                        'id_position' => if_empty($itemStock['id_position'], null),
                        'quantity' => key_exists($itemIndex, $quantities) ? $quantities[$itemIndex] : $itemStock['quantity'],
                        'unit_weight' => key_exists($itemIndex, $tonnages) ? $tonnages[$itemIndex] : $itemStock['unit_weight'],
                        'unit_volume' => key_exists($itemIndex, $volumes) ? $volumes[$itemIndex] : $itemStock['unit_volume'],
                        'no_pallet' => $itemStock['no_pallet'],
                        'created_by' => UserModel::authenticatedUserData('id')
                    ]);
                }
                $itemIndex++;
            }
        }
    }

    /**
     * Delete ownership history
     */
    public function delete()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OWNERSHIP_DELETE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Ownership History', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $changeOwnershipId = $this->input->post('id');

                $this->db->trans_start();
                $changeOwnership = $this->changeOwnership->getChangeOwnershipById($changeOwnershipId);

                // TODO: check if ownership has changed to another owner
                // if already change to another owner, transaction could not be deleted

                // delete booking, handling and workorder
                $this->booking->deleteBooking($changeOwnership['id_booking_out']);
                $this->booking->deleteBooking($changeOwnership['id_booking_in']);

                $this->handling->deleteHandling($changeOwnership['id_handling_out']);
                $this->handling->deleteHandling($changeOwnership['id_handling_in']);

                $this->workOrder->deleteWorkOrder($changeOwnership['id_work_order_out']);
                $this->workOrder->deleteWorkOrder($changeOwnership['id_work_order_in']);

                $this->changeOwnership->deleteChangeOwnership($changeOwnershipId);

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('warning', "Change Ownership of <strong>{$changeOwnership['no_change_ownership']} by {$changeOwnership['name']}</strong> successfully deleted");
                } else {
                    flash('danger', "Delete change ownership <strong>{$changeOwnership['no_change_ownership']}{$changeOwnership['name']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('change_ownership');
    }

}