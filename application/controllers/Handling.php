<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Handling
 * @property BookingModel $booking
 * @property WorkOrderModel $workOrder
 * @property WorkOrderContainerModel $workOrderContainer
 * @property WorkOrderGoodsModel $workOrderGoods
 * @property WorkOrderComponentModel $workOrderComponent
 * @property HandlingModel $handling
 * @property HandlingContainerModel $handlingContainer
 * @property HandlingGoodsModel $handlingGoods
 * @property HandlingComponentModel $handlingComponent
 * @property HandlingTypeModel $handlingType
 * @property HandlingContainerPositionModel $handlingContainerPosition
 * @property HandlingGoodsPositionModel $handlingGoodsPosition
 * @property PeopleModel $people
 * @property UnitModel $unit
 * @property BranchModel $branch
 * @property ComponentModel $component
 * @property ComponentOrderModel $componentOrder
 * @property Mailer $mailer
 * @property Exporter $exporter
 */
class Handling extends CI_Controller
{
    /**
     * Handling constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingTypeModel', 'bookingType');
        $this->load->model('ComponentModel', 'component');
        $this->load->model('ComponentOrderModel', 'componentOrder');
        $this->load->model('HandlingTypeModel', 'handlingTypeModel');
        $this->load->model('HandlingModel', 'handling');
        $this->load->model('HandlingContainerModel', 'handlingContainer');
        $this->load->model('HandlingGoodsModel', 'handlingGoods');
        $this->load->model('HandlingComponentModel', 'handlingComponent');
        $this->load->model('HandlingTypeModel', 'handlingType');
        $this->load->model('HandlingContainerPositionModel', 'handlingContainerPosition');
        $this->load->model('HandlingGoodsPositionModel', 'handlingGoodsPosition');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('WorkOrderContainerModel', 'workOrderContainer');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('WorkOrderComponentModel', 'workOrderComponent');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('BranchModel', 'branch');
        $this->load->model('UnitModel', 'unit');
        $this->load->model('modules/Mailer', 'mailer');
    }

    /**
     * Show all handling data.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_VIEW);

        if (get_url_param('export')) {
            $this->load->model('modules/Exporter', 'exporter');
            $this->exporter->exportLargeResourceFromArray("Handling", $this->handling->getAllHandlings());
        } else {
            $data = [
                'title' => "Handling",
                'subtitle' => "Data handling",
                'page' => "handling/index",
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable.
     */
    public function data()
    {
        $startData = $this->input->get('start');
        $lengthData = $this->input->get('length');
        $searchQuery = trim($this->input->get('search')['value']);
        $orderColumn = $this->input->get('order')[0]['column'];
        $orderColumnOrder = $this->input->get('order')[0]['dir'];

        $handlings = $this->handling->getAllHandlings($startData, $lengthData, $searchQuery, $orderColumn, $orderColumnOrder);

        $no = $startData + 1;
        $handlingData = [];
        foreach ($handlings['data'] as $row) {
            $row['no'] = $no++;
            array_push($handlingData, $row);
        }

        $data = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => $handlings['total'],
            "recordsFiltered" => $handlings['result'],
            "data" => $handlingData
        ];
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * View detail handling and show related work orders
     * @param $handlingId
     */
    public function view($handlingId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_VIEW);

        $handling = $this->handling->getHandlingById($handlingId);
        $handlingContainers = $this->handlingContainer->getHandlingContainersByHandling($handlingId);
        foreach ($handlingContainers as &$container) {
            $containerGoods = $this->handlingGoods->getHandlingGoodsByHandlingContainer($container['id']);
            $container['goods'] = $containerGoods;
        }
        $handlingGoods = $this->handlingGoods->getHandlingGoodsByHandling($handlingId, true);

        $handlingComponents = $this->handlingComponent->getHandlingComponentsByHandling($handlingId);
        $workOrders = $this->workOrder->getWorkOrdersByHandling($handlingId);
        $data = [
            'title' => "Handling",
            'subtitle' => "Data handling",
            'page' => "handling/view",
            'handling' => $handling,
            'handlingContainers' => $handlingContainers,
            'handlingGoods' => $handlingGoods,
            'handlingComponents' => $handlingComponents,
            'workOrders' => $workOrders
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Create handling request.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_CREATE);

        $data = [
            'title' => "Handling",
            'subtitle' => "Create handling",
            'page' => "handling/create",
            'units' => $this->unit->getAll()
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Edit handling request.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_EDIT);

        $handling = $this->handling->getHandlingById($id);

        $containers = $this->handlingContainer->getHandlingContainersByHandling($id);
        $goods = $this->handlingGoods->getHandlingGoodsByHandling($id);

        $customer = $this->people->getById($handling['id_customer']);
        $branches = $this->branch->getByCustomer($handling['id_customer']);
        $getBookingById = $this->booking->getBookingById($handling['id_booking']);
        $bookings = $this->booking->getBookingStocksByCustomer($handling['id_customer']);
        $getAllBookings = $this->booking->getAllBookings();
        $checkBookingStocksByCustomer = in_array($handling["no_booking"], array_column($bookings, "no_booking"));

        if (strpos(get_active_branch('branch'), 'TPP') !== false) {
            $handlingTypes = $this->handlingType->getAllHandlingTypes();
        } else {
            $handlingTypes = $this->handlingType->getHandlingTypesByCustomer($handling['id_customer']);
        }

        $data = [
            'title' => "Handling",
            'subtitle' => "Edit Handling",
            'page' => "handling/edit",
            'handling' => $handling,
            'containers' => $containers,
            'goods' => $goods,
            'units' => $this->unit->getAll(),
            'customer' => $customer,
            'branches' => $branches,
            'handlingTypes' => $handlingTypes,
            'refData' => $bookings,
            'getAllBookings' => $getAllBookings,
            'checkBookingStocksByCustomer' => $checkBookingStocksByCustomer,
            'getBookingById' => $getBookingById,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Save handling data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_CREATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('customer', 'Customer', 'trim|required');
            $this->form_validation->set_rules('handling_type', 'Handling Type', 'trim|required');
            $this->form_validation->set_rules('plan_date', 'Plan Date', 'trim|required');
            $this->form_validation->set_rules('ref_data', 'Referenced Data', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $noHandling = $this->handling->getAutoNumberHandlingRequest();

                $customer = $this->input->post('customer');
                $handlingType = $this->input->post('handling_type');
                $planDate = sql_date_format($this->input->post('plan_date'));
                $booking = $this->input->post('ref_data');
                $description = $this->input->post('description');
                $containers = $this->input->post('containers');
                $goods = $this->input->post('goods');

                $this->db->trans_start();

                $no_booking = $this->booking->getNoBookingInByBookingId($booking);
                $no_booking = !empty($no_booking['no_booking_in']) ? $no_booking['no_booking_in'] : $no_booking['no_booking'];

                $this->handling->createHandling([
                    'no_handling' => $noHandling,
                    'id_booking' => $booking,
                    'id_handling_type' => $handlingType,
                    'id_customer' => $customer,
                    'handling_date' => $planDate,
                    'status' => 'PENDING',
                    'description' => $description,
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);
                $handlingId = $this->db->insert_id();

                if (!empty($containers)) {
                    foreach ($containers as $container) {
                        $this->handlingContainer->createHandlingContainer([
                            'id_owner' => $customer,
                            'id_handling' => $handlingId,
                            'id_container' => $container['id_container'],
                            'id_position' => if_empty($container['id_position'], null),
                            'seal' => $container['seal'],
                            'is_empty' => $container['is_empty'],
                            'is_hold' => $container['is_hold'],
                            'status' => $container['status'],
                            'status_danger' => $container['status_danger'],
                            'description' => $container['description'],
                            'quantity' => 1
                        ]);
                        $handlingContainerId = $this->db->insert_id();

                        $positionBlocks = if_empty(explode(',', $container['id_position_blocks']), []);
                        foreach ($positionBlocks as $blockId) {
                            $this->handlingContainerPosition->create([
                                'id_handling_container' => $handlingContainerId,
                                'id_position_block' => $blockId
                            ]);
                        }

                        if (key_exists('goods', $container)) {
                            foreach ($container['goods'] as $item) {
                                $this->handlingGoods->createHandlingGoods([
                                    'id_owner' => $customer,
                                    'id_handling' => $handlingId,
                                    'id_handling_container' => $handlingContainerId,
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
                                    'no_pallet' => $item['id_goods'] . "/" . if_empty($item['ex_no_container'], '-') . "/" . $no_booking,
                                    'is_hold' => $item['is_hold'],
                                    'status' => $item['status'],
                                    'status_danger' => $item['status_danger'],
                                    'description' => $item['description'],
                                    'ex_no_container' => if_empty($item['ex_no_container'], null)
                                ]);
                                $handlingGoodsId = $this->db->insert_id();

                                $positionBlocks = if_empty(explode(',', $item['id_position_blocks']), []);
                                foreach ($positionBlocks as $blockId) {
                                    $this->handlingGoodsPosition->create([
                                        'id_handling_goods' => $handlingGoodsId,
                                        'id_position_block' => $blockId
                                    ]);
                                }
                            }
                        }
                    }
                }

                if (!empty($goods)) {
                    foreach ($goods as $item) {
                        $this->handlingGoods->createHandlingGoods([
                            'id_handling' => $handlingId,
                            'id_owner' => $customer,
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
                            'is_hold' => $item['is_hold'],
                            'status' => $item['status'],
                            'status_danger' => $item['status_danger'],
                            'description' => $item['description'],
                            'ex_no_container' => if_empty($item['ex_no_container'], null)
                        ]);
                        $handlingGoodsId = $this->db->insert_id();

                        $positionBlocks = if_empty(explode(',', $item['id_position_blocks']), []);
                        foreach ($positionBlocks as $blockId) {
                            $this->handlingGoodsPosition->create([
                                'id_handling_goods' => $handlingGoodsId,
                                'id_position_block' => $blockId
                            ]);
                        }
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    /*
                    $handling = $this->handling->getHandlingById($handlingId);
                    $customer = $this->people->getById($handling['id_customer']);
                    if (!$this->send_email_request($customer, $handling)) {
                        flash('warning', "Handling request saved but admin does not get notification, please contact our customer service that you have handling request", 'handling');
                    } else {
                        flash('success', "Handling request successfully created", 'handling');
                    }
                    */
                    flash('success', "Handling request successfully created", 'handling');
                } else {
                    flash('danger', "Save handling failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->create();
    }

    /**
     * Send email to administrator.
     * @param $customer
     * @param $handling
     * @param bool $isUpdate
     * @return bool
     */
    private function send_email_request($customer, $handling, $isUpdate = false)
    {
        $operationsAdministratorEmail = get_setting('operations_administrator_email');

        $subject = 'Customer ' . $customer['name'] . ' request handling ' . $handling['handling_type'] . ' with no ' . $handling['no_handling'];
        if ($isUpdate) {
            $subject = '[UPDATE] Customer ' . $customer['name'] . ' update handling request ' . $handling['no_handling'];
        }
        $subject .= ' for ' . readable_date($handling['handling_date']);

        $emailTo = $operationsAdministratorEmail;
        $emailTitle = $subject;
        $emailTemplate = 'emails/handling_requested';
        $emailData = [
            'customer' => $customer,
            'handling' => $handling,
            'operationsAdministratorEmail' => $operationsAdministratorEmail,
            'isUpdate' => $isUpdate
        ];

        $send = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
        if ($send) {
            return true;
        }
        return false;
    }

    /**
     * Update handling data.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_EDIT);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('customer', 'Customer', 'trim|required');
            $this->form_validation->set_rules('handling_type', 'Handling Type', 'trim|required');
            $this->form_validation->set_rules('plan_date', 'Plan Date', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $customer = $this->input->post('customer');
                $handlingType = $this->input->post('handling_type');
                $planDate = sql_date_format($this->input->post('plan_date'));
                $booking = $this->input->post('ref_data');
                $description = $this->input->post('description');
                $containers = $this->input->post('containers');
                $goods = $this->input->post('goods');

                $this->db->trans_start();

                $this->handling->updateHandling([
                    'id_booking' => $booking,
                    'id_handling_type' => $handlingType,
                    'id_customer' => $customer,
                    'handling_date' => $planDate,
                    'description' => $description,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => UserModel::authenticatedUserData('id')
                ], $id);

                if (!empty($containers) || !empty($goods)) {
                    $this->handlingGoods->deleteHandlingGoodsByHandling($id);
                    $this->handlingContainer->deleteHandlingContainersByHandling($id);
                }

                if (!empty($containers)) {
                    foreach ($containers as $container) {
                        $this->handlingContainer->createHandlingContainer([
                            'id_owner' => $customer,
                            'id_handling' => $id,
                            'id_container' => $container['id_container'],
                            'id_position' => if_empty($container['id_position'], null),
                            'seal' => $container['seal'],
                            'is_empty' => $container['is_empty'],
                            'is_hold' => $container['is_hold'],
                            'status' => $container['status'],
                            'status_danger' => $container['status_danger'],
                            'description' => $container['description'],
                            'quantity' => 1
                        ]);
                        $handlingContainerId = $this->db->insert_id();

                        $positionBlocks = if_empty(explode(',', $container['id_position_blocks']), []);
                        foreach ($positionBlocks as $blockId) {
                            $this->handlingContainerPosition->create([
                                'id_handling_container' => $handlingContainerId,
                                'id_position_block' => $blockId
                            ]);
                        }

                        if (key_exists('goods', $container)) {
                            foreach ($container['goods'] as $item) {
                                $this->handlingGoods->createHandlingGoods([
                                    'id_owner' => $customer,
                                    'id_handling' => $id,
                                    'id_handling_container' => $handlingContainerId,
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
                                    'is_hold' => $item['is_hold'],
                                    'status' => $item['status'],
                                    'status_danger' => $item['status_danger'],
                                    'description' => $item['description'],
                                    'ex_no_container' => if_empty($item['ex_no_container'], null)
                                ]);
                                $handlingGoodsId = $this->db->insert_id();

                                $positionBlocks = if_empty(explode(',', $item['id_position_blocks']), []);
                                foreach ($positionBlocks as $blockId) {
                                    $this->handlingGoodsPosition->create([
                                        'id_handling_goods' => $handlingGoodsId,
                                        'id_position_block' => $blockId
                                    ]);
                                }
                            }
                        }
                    }
                }

                if (!empty($goods)) {
                    foreach ($goods as $item) {
                        $this->handlingGoods->createHandlingGoods([
                            'id_handling' => $id,
                            'id_owner' => $customer,
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
                            'is_hold' => $item['is_hold'],
                            'status' => $item['status'],
                            'status_danger' => $item['status_danger'],
                            'description' => $item['description'],
                            'ex_no_container' => if_empty($item['ex_no_container'], null)
                        ]);
                        $handlingGoodsId = $this->db->insert_id();

                        $positionBlocks = if_empty(explode(',', $item['id_position_blocks']), []);
                        foreach ($positionBlocks as $blockId) {
                            $this->handlingGoodsPosition->create([
                                'id_handling_goods' => $handlingGoodsId,
                                'id_position_block' => $blockId
                            ]);
                        }
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    /*
                    $handling = $this->handling->getHandlingById($id);
                    $customer = $this->people->getById($customer);
                    if (!$this->send_email_request($customer, $handling, true)) {
                        flash('warning', "Handling request saved but admin does not get notification, please contact our customer service that you have handling request", 'handling');
                    } else {
                        flash('success', "Handling request successfully updated", 'handling');
                    }
                    */
                    flash('success', "Handling request successfully updated", 'handling');
                } else {
                    flash('danger', "Save handling failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->edit($id);
    }

    /**
     * Validating handling request.
     * @param $type
     */
    public function validate($type)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_VALIDATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Handling Data', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $handlingId = $this->input->post('id');
                $components = $this->input->post('components');

                $this->db->trans_start();

                if ($type == 'approve') {
                    $type = HandlingModel::STATUS_APPROVED;

                    // create handling component detail
                    if (!empty($components)) {
                        $this->handlingComponent->deleteHandlingComponentByHandling($handlingId);
                        foreach ($components as $componentId => $component) {
                            if (!empty($componentId)) {
                                $this->handlingComponent->createHandlingComponent([
                                    'id_handling' => $handlingId,
                                    'id_component' => $componentId,
                                    'id_component_order' => $component['transaction'],
                                    'quantity' => $component['quantity'],
                                    'id_unit' => $component['unit'],
                                    'description' => $component['description'],
                                    'status' => 'APPROVED',
                                ]);
                            }
                        }
                    }

                } else {
                    $type = HandlingModel::STATUS_REJECTED;
                }

                $handling = $this->handling->getHandlingById($handlingId);

                $this->handling->updateHandling([
                    'status' => $type,
                    'validated_by' => UserModel::authenticatedUserData('id'),
                    'validated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => UserModel::authenticatedUserData('id'),
                    'updated_at' => date('Y-m-d H:i:s'),
                ], $handlingId);

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    /*
                    $handling = $this->handling->getHandlingById($handlingId);
                    $customer = $this->people->getById($handling['id_customer']);
                    if (!$this->send_email_validate($customer, $handling)) {
                        flash('warning', "Handling is validated but email failed, try resend remainder");
                    } else {
                        flash('success', "Handling <strong>{$handling['no_handling']}</strong> successfully {$type} and requester was notified by email");
                    }
                    */
                    flash('success', "Handling <strong>{$handling['no_handling']}</strong> successfully {$type}");
                } else {
                    flash('danger', "Validating handling <strong>{$handling['no_handling']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }

        redirect('handling');
    }

    /**
     * Send notification email of handling.
     * @param $customer
     * @param $handling
     * @return bool
     */
    private function send_email_validate($customer, $handling)
    {

        $emailTo = $customer['email'];
        $emailTitle = "Handling request " . $handling['no_handling'] . ' is ' . $handling['status'] . ' on ' . date('d F Y');
        $qrName = 'barcode_handling_' . time() . '.png';
        if ($handling['status'] == HandlingModel::STATUS_APPROVED) {
            $barcode = new \Milon\Barcode\DNS2D();
            $barcode->setStorPath(APPPATH . "/cache/");
            $qrCode = base64_decode($barcode->getBarcodePNG($handling['no_handling'], "QRCODE", 15, 15));
            file_put_contents(APPPATH . 'cache/' . $qrName, $qrCode);

            $attachments = [];
            $attachments[] = [
                'source' => APPPATH . 'cache/' . $qrName,
            ];

            $emailTemplate = 'emails/handling_approved';
            $emailData = [
                'customer' => $customer,
                'handling' => $handling,
                'qrCode' => $qrCode,
                'pathQrCode' => APPPATH . 'cache/' . $qrName
            ];
        } else {
            $reason = $this->input->post('reason');
            $emailTemplate = 'emails/handling_rejected';
            $emailData = [
                'customer' => $customer,
                'handling' => $handling,
                'reason' => $reason
            ];
        }

        $emailOptions = [
            'attachment' => isset($attachments) ? $attachments : null,
        ];


        $send = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);

        if ($send) {
            if (file_exists(APPPATH . 'cache/' . $qrName)) {
                unlink(APPPATH . 'cache/' . $qrName);
            }
            return true;
        }

        return false;
    }

    /**
     * Print handling pass.
     * @param $handlingId
     */
    public function print_handling($handlingId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_PRINT);

        $handling = $this->handling->getHandlingById($handlingId);

        $barcode = new \Milon\Barcode\DNS2D();
        $barcode->setStorPath(APPPATH . "/cache/");
        $qrCode = $barcode->getBarcodePNG($handling['no_handling'], "QRCODE", 13, 13);
        $data = [
            'handling' => $handling,
            'qrCode' => $qrCode
        ];
        $this->load->view('handling/print', $data);
    }

    /**
     * Print handling invoice.
     * @param $handlingId
     */
    public function print_charge_component($handlingId)
    {
        $handling = $this->handling->getHandlingById($handlingId);
        $handling['components'] = $this->handlingComponent->getHandlingComponentsByHandling($handlingId);

        $handlingContainers = $this->handlingContainer->getHandlingContainersByHandling($handlingId);
        foreach ($handlingContainers as &$container) {
            $containerGoods = $this->handlingGoods->getHandlingGoodsByHandlingContainer($container['id']);
            $container['goods'] = $containerGoods;
        }
        $handlingGoods = $this->handlingGoods->getHandlingGoodsByHandling($handlingId, true);

        $workOrders = $this->workOrder->getWorkOrdersByHandling($handlingId);
        foreach ($workOrders as &$workOrder) {
            $workOrder['components'] = $this->workOrderComponent->getWorkOrderComponentsByWorkOrder($workOrder['id']);

            $workOrder['containers'] = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrder['id'], true);
            foreach ($workOrder['containers'] as &$container) {
                $containerGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($container['id']);
                $container['goods'] = $containerGoods;

                $containerContainers = $this->workOrderContainer->getWorkOrderContainersByWorkOrderContainer($container['id']);
                $container['containers'] = $containerContainers;
            }

            $workOrder['goods'] = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrder['id'], true, true);
            foreach ($workOrder['goods'] as &$item) {
                $goodsItem = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderGoods($item['id']);
                $item['goods'] = $goodsItem;
            }
        }

        $customer = $this->people->getById($handling['id_customer']);
        $data = [
            'title' => 'Print Handling Invoice',
            'page' => 'handling/charge_components',
            'handling' => $handling,
            'handlingContainers' => $handlingContainers,
            'handlingGoods' => $handlingGoods,
            'workOrders' => $workOrders,
            'customer' => $customer
        ];
        $this->load->view('template/print', $data);
    }

    /**
     * Perform deleting handling data.
     */
    public function delete()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_DELETE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Handling Data', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $handlingId = $this->input->post('id');
                $handling = $this->handling->getHandlingById($handlingId);

                // only owner of data allowed to delete handling
                if (AuthorizationModel::hasRole(ROLE_CUSTOMER)) {
                    $customerId = UserModel::authenticatedUserData('id_person');
                    if ($handling['id_customer'] != $customerId) {
                        show_error('Only owner of data could delete the data');
                    }
                }

                $delete = $this->handling->deleteHandling($handlingId);

                if ($delete) {
                    flash('warning', "Handling <strong>{$handling['no_handling']}</strong> successfully deleted");
                } else {
                    flash('danger', "Delete handling <strong>{$handling['no_handling']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('handling');
    }

    public function ajax_get_all_handling()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $search = $this->input->get('q');
            $page = $this->input->get('page');

            $handling = $this->handling->getHandlingByNo($search, true, $page);

            echo json_encode($handling);
        }
    }

    /**
     * Get stock by booking customer.
     */
    public function ajax_get_handling_component_form()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $handlingTypeId = $this->input->get('id_handling_type');

            $units = $this->unit->getAll();
            $components = $this->component->getByHandlingType($handlingTypeId);
            $componentTransactions = [];
            foreach ($components as $component) {
                $componentTransactions[$component['id']] = $this->componentOrder->getComponentOrdersByHandlingComponent($component['id'], true);
            }

            if (isset($_SERVER['HTTP_ACCEPT']) && preg_match('/text\/html/', $_SERVER['HTTP_ACCEPT'])) {
                if (!empty($components)) {
                    echo $this->load->view('gate/_field_handling_component', [
                        'components' => $components,
                        'componentTransactions' => $componentTransactions,
                        'units' => $units
                    ], true);
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'components' => $components,
                    'componentTransactions' => $componentTransactions,
                ]);
            }
        }
    }

    /**
     * Get stock by booking customer.
     */
    public function ajax_get_stock_by_booking()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $bookingId = $this->input->get('id_booking');
            $booking = $this->booking->getBookingById($bookingId);
            $stockContainers = $this->workOrderContainer->getContainerStocksByBooking($bookingId);
            $stockGoods = $this->workOrderGoods->getGoodsStocksByBooking($bookingId);

            if (isset($_SERVER['HTTP_ACCEPT']) && preg_match('/text\/html/', $_SERVER['HTTP_ACCEPT'])) {
                if (!empty($booking)) {
                    echo $this->load->view('handling/_booking_stock', [
                        'booking' => $booking,
                        'stockContainers' => $stockContainers,
                        'stockGoods' => $stockGoods
                    ], true);
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'booking' => $booking,
                    'stockContainers' => $stockContainers,
                    'stockGoods' => $stockGoods
                ]);
            }
        }
    }
}