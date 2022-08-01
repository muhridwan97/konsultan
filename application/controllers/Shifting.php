<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * @property ShiftingModel $shifting
 * @property BookingModel $booking
 * @property BookingGoodsModel $bookingGoods
 * @property BookingGoodsPositionModel $bookingGoodsPosition
 * @property BookingContainerModel $bookingContainer
 * @property HandlingTypeModel $handlingType
 * @property HandlingModel $handling
 * @property HandlingContainerModel $handlingContainer
 * @property HandlingGoodsModel $handlingGoods
 * @property WorkOrderModel $workOrder
 * @property WorkOrderContainerModel $workOrderContainer
 * @property WorkOrderGoodsModel $workOrderGoods
 * @property ReportModel $report
 * @property ReportStockModel $reportStock
 * @property ShiftingDetailModel $shiftingDetail
 * @property ShiftingDetailPositionModel $shiftingDetailPosition
 * @property HandlingContainerPositionModel $handlingContainerPosition
 * @property HandlingGoodsPositionModel $handlingGoodsPosition
 * @property WorkOrderContainerPositionModel $workOrderContainerPosition
 * @property WorkOrderGoodsPositionModel $workOrderGoodsPosition
 */
class Shifting extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ShiftingModel', 'shifting');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('BookingGoodsPositionModel', 'bookingGoodsPosition');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('HandlingTypeModel', 'handlingType');
        $this->load->model('HandlingModel', 'handling');
        $this->load->model('HandlingContainerModel', 'handlingContainer');
        $this->load->model('HandlingGoodsModel', 'handlingGoods');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('WorkOrderContainerModel', 'workOrderContainer');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('ReportModel', 'report');
        $this->load->model('ReportStockModel', 'reportStock');
        $this->load->model('PositionBlockModel', 'positionBlock');
        $this->load->model('ShiftingDetailPositionModel', 'shiftingDetailPosition');
        $this->load->model('ShiftingDetailModel', 'shiftingDetail');
        $this->load->model('HandlingContainerPositionModel', 'handlingContainerPosition');
        $this->load->model('HandlingGoodsPositionModel', 'handlingGoodsPosition');
        $this->load->model('WorkOrderContainerPositionModel', 'workOrderContainerPosition');
        $this->load->model('WorkOrderGoodsPositionModel', 'workOrderGoodsPosition');
    }

    /**
     * Show shifting data.
     */
    public function index()
    {
        $shiftings = $this->shifting->getAllShiftings();
        
        $data = [
            'title' => "Shifting",
            'subtitle' => "Data Shifting",
            'page' => "shifting/index",
            'shiftings' => $shiftings
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show create shifting data.
     */
    public function create()
    {
        $filters = ['data' => 'stock'];
        $stockGoods = $this->reportStock->getStockGoods($filters);
        $stockContainers = $this->reportStock->getStockContainers($filters);
        $result = [];

        foreach ($stockGoods as $good) {
            array_push($result, [
                'container_goods_type' => 'goods',
                'container_goods' => $good['goods_name'],
                'id_container_goods' => $good['id_goods'],
                'quantity' => $good['stock_quantity'],
                'weight' => $good['stock_weight'],
                'gross_weight' => $good['stock_gross_weight'],
                'length' => $good['unit_length'],
                'width' => $good['unit_width'],
                'height' => $good['unit_height'],
                'volume' => $good['stock_volume'],
                'id_booking' => $good['id_booking'],
                'no_reference' => $good['no_reference'],
                'id_customer' => $good['id_owner'],
                'customer_name' => $good['owner_name'],
                'seal' => null,
                'last_position' => $good['position'],
                'position_blocks' => $good['position_blocks'],
                'id_unit' => $good['id_unit'],
                'unit' => $good['unit'],
                'no_pallet' => $good['no_pallet'],
                'is_empty' => null,
                'is_hold' => $good['is_hold'],
                'status' => $good['status'],
                'status_danger' => $good['status_danger'],
                'ex_no_container' => $good['ex_no_container'],
                'description' => $good['description'],
                'whey_number' => $good['whey_number'],
            ]);
        }

        foreach ($stockContainers as $container) {
            array_push($result, [
                'container_goods_type' => 'container',
                'container_goods' => $container['no_container'],
                'id_container_goods' => $container['id_container'],
                'quantity' => $container['stock'],
                'weight' => null,
                'gross_weight' => null,
                'length' => null,
                'width' => null,
                'height' => null,
                'volume' => null,
                'id_booking' => $container['id_booking'],
                'no_reference' => $container['no_reference'],
                'id_customer' => $container['id_owner'],
                'customer_name' => $container['owner_name'],
                'seal' => $container['seal'],
                'last_position' => $container['position'],
                'position_blocks' => $container['position_blocks'],
                'id_unit' => null,
                'unit' => 'null',
                'no_pallet' => 'null',
                'is_empty' => $container['is_empty'],
                'is_hold' => $container['is_hold'],
                'status' => $container['status'],
                'status_danger' => $container['status_danger'],
                'ex_no_container' => null,
                'description' => $container['description'],
                'whey_number' => null,
            ]);
        }

        $data = [
            'title' => "Shifting",
            'subtitle' => "Create New Shifting",
            'page' => "shifting/create",
            'items' => $result
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show shifting result.
     *
     * @param $id
     */
    public function view($id)
    {
        $shifting = $this->shifting->getShiftingById($id);
        $shiftingDetails = $this->shiftingDetail->getBy(['shifting_details.id_shifting' => $id]);

        $data = [
            'title' => "Shifting",
            'subtitle' => "View Shifting",
            'page' => "shifting/view",
            'shifting' => $shifting,
            'shiftingDetails' => $shiftingDetails
        ];

        $this->load->view('template/layout', $data);
    }

    /**
     * Save shifting result.
     */
    public function save()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('new_position[]', ' Positions', 'trim|required');
            $this->form_validation->set_rules('shifting_date', 'Shifting data', 'trim|required');
            $this->form_validation->set_rules('description', 'Description', 'trim|max_length[500]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $shiftingDate = $this->input->post('shifting_date');
                $description = $this->input->post('description');

                $id_container_goods = $this->input->post('container_goods[]');
                $positions = $this->input->post('new_position[]');
                $position_blocks = $this->input->post('position_blocks[]');
                $booking = $this->input->post('booking[]');
                $container_goods_type = $this->input->post('container_goods_type[]');
                $customer = $this->input->post('customer[]');
                $quantity = $this->input->post('quantity[]');
                $unit = $this->input->post('unit[]');
                $tonnage = $this->input->post('tonnage[]');
                $tonnageGross = $this->input->post('tonnage_gross[]');
                $length = $this->input->post('length[]');
                $width = $this->input->post('width[]');
                $height = $this->input->post('height[]');
                $volume = $this->input->post('volume[]');
                $seal = $this->input->post('seal[]');
                $noPallet = $this->input->post('no_pallet[]');
                $exContainer = $this->input->post('ex_no_container[]');
                $isEmpty = $this->input->post('is_empty[]');
                $isHold = $this->input->post('is_hold[]');
                $status = $this->input->post('status[]');
                $statusDanger = $this->input->post('status_danger[]');
                $noDeliveryOrder = $this->input->post('no_delivery_order[]');
                $descriptions = $this->input->post('descriptions[]');

                $this->db->trans_start();

                $branchId = get_active_branch('id');
                $noShifting = $this->shifting->getAutoNumberShiftingRequest();
                $this->shifting->createShifting([
                    'no_shifting' => $noShifting,
                    'id_branch' => $branchId,
                    'shifting_date' => sql_date_format($shiftingDate),
                    'status' => ShiftingModel::STATUS_PENDING,
                    'description' => $description,
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);
                $shiftingId = $this->db->insert_id();

                foreach ($id_container_goods as $index => $id_container_good) {
                    $this->shifting->createShiftingDetail([
                        'id_shifting' => $shiftingId,
                        'id_booking' => $booking[$index],
                        'id_customer' => $customer[$index],
                        'id_container' => ($container_goods_type[$index] == 'container' ? $id_container_good : null),
                        'id_goods' => ($container_goods_type[$index] == 'goods' ? $id_container_good : null),
                        'id_position' => if_empty($positions[$index], null),
                        'id_unit' => $unit[$index],
                        'quantity' => $quantity[$index],
                        'tonnage' => $tonnage[$index],
                        'tonnage_gross' => $tonnageGross[$index],
                        'length' => $length[$index],
                        'width' => $width[$index],
                        'height' => $height[$index],
                        'volume' => $volume[$index],
                        'seal' => if_empty($seal[$index], null),
                        'no_pallet' => if_empty($noPallet[$index], null),
                        'ex_no_container' => if_empty($exContainer[$index], null),
                        'is_empty' => $isEmpty[$index],
                        'is_hold' => $isHold[$index],
                        'status' => $status[$index],
                        'status_danger' => $statusDanger[$index],
                        'no_delivery_order' => if_empty($noDeliveryOrder[$index], null),
                        'description' => $descriptions[$index],
                    ]);
                    $shiftingDetailId = $this->db->insert_id();

                    $positionBlocks = if_empty(explode(',', get_if_exist($position_blocks, $index)), []);
                    foreach ($positionBlocks as $blockId) {
                        $this->shiftingDetailPosition->create([
                            'id_shifting_detail' => $shiftingDetailId,
                            'id_position_block' => $blockId
                        ]);
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Shifting <strong>{$noShifting}</strong> successfully created");
                } else {
                    flash('danger', "Shifting <strong>{$noShifting}</strong> failed, try again or contact administrator");
                }
                redirect('shifting');
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed, please wait until page loaded');
        }
        $this->create();
    }

    /**
     * Validate and auto create handling and work order.
     */
    public function validate()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SHIFTING_VALIDATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Shifting Data', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $shiftingId = $this->input->post('id');

                $shifting = $this->shifting->getShiftingById($shiftingId);
                $shiftingDetails = $this->shifting->getShiftingDetailsByIdShifting($shiftingId);

                $handlingTypeId = get_setting('default_shifting_handling');
                $handlingType = $this->handlingType->getHandlingTypeById($handlingTypeId);
                $handlingCode = $handlingType['handling_code'];

                $bookingShiftings = array();
                foreach ($shiftingDetails as $shiftingDetail) {
                    $bookingShiftings[$shiftingDetail['id_booking']][] = $shiftingDetail;
                }

                $this->db->trans_start();

                $this->shifting->updateShifting([
                    'STATUS' => 'APPROVED'
                ], $shifting['id']);

                foreach ($bookingShiftings as $key => $bookingShifting) {
                    $booking = $this->booking->getBookingById($key);
                    $handlingNo = $this->handling->getAutoNumberHandlingRequest();
                    $handlingData = [
                        'id_handling_type' => $handlingTypeId,
                        'id_customer' => $booking['id_customer'],
                        'id_booking' => $key,
                        'id_shifting' => $shifting['id'],
                        'no_handling' => $handlingNo,
                        'handling_date' => $shifting['shifting_date'],
                        'status' => 'APPROVED',
                        'description' => $shifting['no_shifting'],
                        'created_by' => UserModel::authenticatedUserData('id'),
                        'validated_by' => UserModel::authenticatedUserData('id'),
                        'validated_at' => date('Y-m-d H:i:s'),
                    ];
                    $this->handling->createHandling($handlingData);
                    $handlingId = $this->db->insert_id();

                    // todo: id_warehouse
                    $workOrderNo = $this->workOrder->getAutoNumberWorkOrder($handlingCode);
                    $workOrderData = [
                        'id_handling' => $handlingId,
                        'no_work_order' => $workOrderNo,
                        'gate_in_date' => $shifting['shifting_date'],
                        'gate_out_date' => $shifting['shifting_date'],
                        'status' => 'COMPLETED',
                        'taken_by' => UserModel::authenticatedUserData('id'),
                        'taken_at' => $shifting['shifting_date'],
                        'created_by' => UserModel::authenticatedUserData('id'),
                        'completed_at' => $shifting['shifting_date']
                    ];
                    $this->workOrder->createWorkOrder($workOrderData);
                    $workOrderId = $this->db->insert_id();

                    foreach ($bookingShifting as $detail) {
                        $shiftingDetailPositionByDetailShiftingId = $this->shiftingDetailPosition->getShiftingDetailPositionByDetailshiftingId($detail['id']);
                        $positionBlocks = array_column($shiftingDetailPositionByDetailShiftingId, 'id_position_block') ?? [];

                        if (!empty($detail['id_container'])) {
                            $handlingContainer = [
                                'id_handling' => $handlingId,
                                'id_container' => $detail['id_container'],
                                'id_owner' => $detail['id_customer'],
                                'id_position' => if_empty($detail['id_position'], null),
                                'quantity' => $detail['quantity'],
                                'seal' => if_empty($detail['seal'], null, '', '', true),
                                'status' => $detail['status'],
                                'status_danger' => $detail['status_danger'],
                                'is_empty' => $detail['is_empty'],
                                'is_hold' => $detail['is_hold'],
                                'description' => if_empty($detail['description'], '', '', '', true),
                                'created_by' => UserModel::authenticatedUserData('id'),
                            ];
                            $this->handlingContainer->createHandlingContainer($handlingContainer);

                            $handlingContainerId = $this->db->insert_id();
                            if (!empty($positionBlocks)) {
                                foreach ($positionBlocks as $blockId) {
                                    $this->handlingContainerPosition->create([
                                        'id_handling_container' => $handlingContainerId,
                                        'id_position_block' => $blockId
                                    ]);
                                }
                            }

                            $workOrderContainer = [
                                'id_work_order' => $workOrderId,
                                'id_container' => $detail['id_container'],
                                'id_owner' => $detail['id_customer'],
                                'id_position' => if_empty($detail['id_position'], null),
                                'quantity' => $detail['quantity'],
                                'seal' => if_empty($detail['seal'], null, '', '', true),
                                'status' => $detail['status'],
                                'status_danger' => $detail['status_danger'],
                                'is_empty' => $detail['is_empty'],
                                'is_hold' => $detail['is_hold'],
                                'description' => if_empty($detail['description'], '', '', '', true),
                                'created_by' => UserModel::authenticatedUserData('id'),
                            ];
                            $this->workOrderContainer->insertWorkOrderContainer($workOrderContainer);

                            $workOrderContainerId = $this->db->insert_id();
                            if (!empty($positionBlocks)) {
                                foreach ($positionBlocks as $blockId) {
                                    $this->workOrderContainerPosition->create([
                                        'id_work_order_container' => $workOrderContainerId,
                                        'id_position_block' => $blockId
                                    ]);
                                }
                            }

                            $this->updateContainerPositionHistory($detail);
                        } else if (!empty($detail['id_goods'])) {
                            $handlingGoods = [
                                'id_handling' => $handlingId,
                                'id_goods' => $detail['id_goods'],
                                'id_unit' => $detail['id_unit'],
                                'id_owner' => $detail['id_customer'],
                                'id_position' => if_empty($detail['id_position'], null),
                                'quantity' => $detail['quantity'],
                                'unit_volume' => $detail['unit_volume'],
                                'unit_weight' => $detail['unit_weight'],
                                'unit_gross_weight' => $detail['unit_gross_weight'],
                                'unit_length' => $detail['unit_length'],
                                'unit_width' => $detail['unit_width'],
                                'unit_height' => $detail['unit_height'],
                                'status' => $detail['status'],
                                'status_danger' => $detail['status_danger'],
                                'is_hold' => $detail['is_hold'],
                                'no_pallet' => if_empty($detail['no_pallet'], null, '', '', true),
                                'ex_no_container' => if_empty($detail['ex_no_container'], null, '', '', true),
                                'description' => if_empty($detail['description'], '', '', '', true),
                                'created_by' => UserModel::authenticatedUserData('id'),
                            ];
                            $this->handlingGoods->createHandlingGoods($handlingGoods);
                            $handlingGoodsId = $this->db->insert_id();

                            if (!empty($positionBlocks)) {
                                foreach ($positionBlocks as $blockId) {
                                    $this->handlingGoodsPosition->create([
                                        'id_handling_goods' => $handlingGoodsId,
                                        'id_position_block' => $blockId
                                    ]);
                                }
                            }
                            $workOrderGoods = [
                                'id_work_order' => $workOrderId,
                                'id_goods' => $detail['id_goods'],
                                'id_owner' => $detail['id_customer'],
                                'id_position' => if_empty($detail['id_position'], null),
                                'quantity' => $detail['quantity'],
                                'id_unit' => $detail['id_unit'],
                                'unit_volume' => $detail['unit_volume'],
                                'unit_weight' => $detail['unit_weight'],
                                'unit_gross_weight' => $detail['unit_gross_weight'],
                                'unit_length' => $detail['unit_length'],
                                'unit_width' => $detail['unit_width'],
                                'unit_height' => $detail['unit_height'],
                                'status' => $detail['status'],
                                'status_danger' => $detail['status_danger'],
                                'is_hold' => $detail['is_hold'],
                                'no_pallet' => if_empty($detail['no_pallet'], null, '', '', true),
                                'ex_no_container' => if_empty($detail['ex_no_container'], null, '', '', true),
                                'description' => if_empty($detail['description'], '', '', '', true),
                                'created_by' => UserModel::authenticatedUserData('id'),
                            ];
                            $this->workOrderGoods->insertWorkOrderGoods($workOrderGoods);
                            $workOrderGoodsId = $this->db->insert_id();
                            if (!empty($positionBlocks)) {
                                foreach ($positionBlocks as $blockId) {
                                    $this->workOrderGoodsPosition->create([
                                        'id_work_order_goods' => $workOrderGoodsId,
                                        'id_position_block' => $blockId
                                    ]);
                                }
                            }

                            $this->updateGoodsPositionHistory($detail, $positionBlocks);
                        }
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Shifting <strong>{$shifting['no_shifting']}</strong> successfully approved");
                } else {
                    flash('danger', "Shifting <strong>{$shifting['no_shifting']}</strong> failed, try again or contact administrator");
                }
                redirect('shifting');
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('shifting');
    }

    /**
     * Update container position history by container id.
     * @param $detail
     */
    private function updateContainerPositionHistory($detail)
    {
        // update data inbound
        $this->updateContainerPosition($detail['id_booking'], $detail);

        // update data outbound
        $bookingOuts = $this->booking->getBookingOutByBookingIn($detail['id_booking']);
        foreach ($bookingOuts as $bookingOut) {
            $this->updateContainerPosition($bookingOut['id'], $detail);
        }
    }

    /**
     * Update container position by booking.
     *
     * @param $bookingId
     * @param $detail
     */
    private function updateContainerPosition($bookingId, $detail)
    {
        // booking
        $bookingContainers = $this->bookingContainer->getBy([
            'bookings.id' => $bookingId,
            'booking_containers.id_container' => $detail['id_container'],
        ]);
        foreach ($bookingContainers as $bookingContainer) {
            $this->bookingContainer->update([
                'id_position' => $detail['id_position']
            ], $bookingContainer['id']);
        }

        // handling
        $handlingContainers = $this->handlingContainer->getBy([
            'bookings.id' => $bookingId,
            'handling_containers.id_container' => $detail['id_container'],
        ]);
        foreach ($handlingContainers as $handlingContainer) {
            $this->handlingContainer->update([
                'id_position' => $detail['id_position']
            ], $handlingContainer['id']);
        }

        // work order
        $workOrderContainers = $this->workOrderContainer->getBy([
            'bookings.id' => $bookingId,
            'work_order_containers.id_container' => $detail['id_container'],
        ]);
        foreach ($workOrderContainers as $workOrderContainer) {
            $this->workOrderContainer->update([
                'id_position' => $detail['id_position']
            ], $workOrderContainer['id']);
        }
    }

    /**
     * Update goods position history by goods id, unit id, ex no container.
     *
     * @param $detail
     * @param $positionBlocks
     */
    private function updateGoodsPositionHistory($detail, $positionBlocks)
    {
        // update data inbound
        $this->updateGoodsPosition($detail['id_booking'], $detail, $positionBlocks);

        // update data outbound
        $bookingOuts = $this->booking->getBookingOutByBookingIn($detail['id_booking']);
        foreach ($bookingOuts as $bookingOut) {
            $this->updateGoodsPosition($bookingOut['id'], $detail, $positionBlocks);
        }
    }

    /**
     * Update goods position by booking.
     *
     * @param $bookingId
     * @param $detail
     * @param $positionBlocks
     */
    private function updateGoodsPosition($bookingId, $detail, $positionBlocks)
    {
        // booking
        $bookingGoods = $this->bookingGoods->getBy([
            'bookings.id' => $bookingId,
            'booking_goods.id_goods' => $detail['id_goods'],
            'booking_goods.id_unit' => $detail['id_unit'],
            'booking_goods.ex_no_container' => $detail['ex_no_container']
        ]);

        foreach ($bookingGoods as $bookingItem) {
            $this->bookingGoods->update([
                'id_position' => $detail['id_position']
            ], $bookingItem['id']);

            $this->bookingGoodsPosition->delete(['id_booking_goods' => $bookingItem['id']]);
            foreach ($positionBlocks as $blockId) {
                $this->bookingGoodsPosition->create([
                    'id_booking_goods' => $bookingItem['id'],
                    'id_position_block' => $blockId
                ]);
            }
        }

        // handling
        $handlingGoods = $this->handlingGoods->getBy([
            'bookings.id' => $bookingId,
            'handling_goods.id_goods' => $detail['id_goods'],
            'handling_goods.id_unit' => $detail['id_unit'],
            'handling_goods.ex_no_container' => $detail['ex_no_container']
        ]);

        foreach ($handlingGoods as $handlingItem) {
            $this->handlingGoods->update([
                'id_position' => $detail['id_position']
            ], $handlingItem['id']);

            $this->handlingGoodsPosition->delete(['id_handling_goods' => $handlingItem['id']]);
            foreach ($positionBlocks as $blockId) {
                $this->handlingGoodsPosition->create([
                    'id_handling_goods' => $handlingItem['id'],
                    'id_position_block' => $blockId
                ]);
            }
        }

        // work order
        $workOrderGoods = $this->workOrderGoods->getBy([
            'bookings.id' => $bookingId,
            'work_order_goods.id_goods' => $detail['id_goods'],
            'work_order_goods.id_unit' => $detail['id_unit'],
            'work_order_goods.ex_no_container' => $detail['ex_no_container']
        ]);

        foreach ($workOrderGoods as $workOrderItem) {
            $this->workOrderGoods->update([
                'id_position' => $detail['id_position']
            ], $workOrderItem['id']);

            $this->workOrderGoodsPosition->delete(['id_work_order_goods' => $workOrderItem['id']]);
            foreach ($positionBlocks as $blockId) {
                $this->workOrderGoodsPosition->create([
                    'id_work_order_goods' => $workOrderItem['id'],
                    'id_position_block' => $blockId
                ]);
            }
        }
    }

    /**
     * Delete shifting history
     */
    public function delete()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SHIFTING_DELETE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Shifting Data', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $shiftingId = $this->input->post('id');

                $shifting = $this->shifting->getShiftingById($shiftingId);
                $delete = $this->shifting->deleteShifting($shiftingId);

                if ($delete) {
                    flash('warning', "Shifting <strong>{$shifting['no_shifting']}</strong> successfully deleted");
                } else {
                    flash('danger', "Delete shifting <strong>{$shifting['no_shifting']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('shifting');
    }

    /**
     * Get merging stock data.
     */
    public function ajax_get_container_goods()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $search = $this->input->get('q');
            $page = $this->input->get('page');

            $startData = ($page == 1) ? 0 : (($page - 1) * 10);

            $filters = [
                'data' => 'stock',
                'search' => $search,
                'start' => $startData
            ];

            $stockGoods = $this->reportStock->getStockGoods($filters);
            $stockContainers = $this->reportStock->getStockContainers($filters);
            $result = [];

            foreach ($stockGoods['data'] as $good) {
                array_push($result, [
                    'container_goods_type' => 'goods',
                    'container_goods' => $good['goods_name'],
                    'id_container_goods' => $good['id_goods'],
                    'quantity' => $good['stock_quantity'],
                    'tonnage' => $good['stock_weight'],
                    'tonnage_gross' => $good['stock_gross_weight'],
                    'length' => $good['unit_length'],
                    'width' => $good['unit_width'],
                    'height' => $good['unit_height'],
                    'volume' => $good['stock_volume'],
                    'id_booking' => $good['id_booking'],
                    'no_reference' => $good['no_reference'],
                    'id_customer' => $good['id_owner'],
                    'customer_name' => $good['owner_name'],
                    'seal' => null,
                    'last_position' => $good['position'],
                    'position_blocks' => $good['position_blocks'],
                    'id_unit' => $good['id_unit'],
                    'unit' => $good['unit'],
                    'no_pallet' => $good['no_pallet'],
                    'is_empty' => null,
                    'is_hold' => $good['is_hold'],
                    'status' => $good['status'],
                    'status_danger' => $good['status_danger'],
                    'no_delivery_order' => $good['no_delivery_order'],
                    'ex_no_container' => $good['no_container'],
                    'description' => $good['description'],
                ]);
            }

            foreach ($stockContainers['data'] as $container) {
                array_push($result, [
                    'container_goods_type' => 'container',
                    'container_goods' => $container['no_container'],
                    'id_container_goods' => $container['id_container'],
                    'quantity' => $container['stock'],
                    'tonnage' => null,
                    'tonnage_gross' => null,
                    'volume' => null,
                    'id_booking' => $container['id_booking'],
                    'no_reference' => $container['no_reference'],
                    'id_customer' => $container['id_owner'],
                    'customer_name' => $container['owner_name'],
                    'seal' => $container['seal'],
                    'last_position' => $container['position'],
                    'position_blocks' => $good['position_blocks'],
                    'id_unit' => null,
                    'unit' => 'null',
                    'no_pallet' => 'null',
                    'is_empty' => $container['is_empty'],
                    'is_hold' => $container['is_hold'],
                    'status' => $container['status'],
                    'status_danger' => $container['status_danger'],
                    'no_delivery_order' => null,
                    'ex_no_container' => null,
                    'description' => $container['description'],
                ]);
            }

            echo json_encode([
                'results' => $result,
                'total_count' => $stockGoods['recordsFiltered'] + $stockContainers['recordsFiltered']
            ]);
        }
    }
}
