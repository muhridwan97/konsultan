<?php

use Milon\Barcode\DNS2D;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Safe_conduct
 * @property BookingModel $booking
 * @property BookingGoodsModel $bookingGoods
 * @property BookingContainerModel $bookingContainer
 * @property BookingTypeModel $bookingType
 * @property BranchModel $branch
 * @property ComponentModel $component
 * @property DocumentTypeModel $documentType
 * @property EsealModel $eseal
 * @property HandlingModel $handling
 * @property HandlingContainerModel $handlingContainer
 * @property HandlingGoodsModel $handlingGoods
 * @property HandlingComponentModel $handlingComponent
 * @property InvoiceModel $invoice
 * @property InvoiceHandlingModel $invoiceHandling
 * @property InvoiceDetailModel $invoiceDetail
 * @property PeopleModel $people
 * @property SafeConductModel $safeConduct
 * @property SafeConductContainerModel $safeConductContainer
 * @property SafeConductGoodsModel $safeConductGoods
 * @property UploadDocumentModel $uploadDocument
 * @property UploadDocumentFileModel $uploadDocumentFile
 * @property VehicleModel $vehicle
 * @property WorkOrderModel $workOrder
 * @property WorkOrderContainerModel $workOrderContainer
 * @property WorkOrderGoodsModel $workOrderGoods
 * @property WorkOrderComponentModel $workOrderComponent
 * @property TransporterEntryPermitModel $transporterEntryPermit
 * @property TransporterEntryPermitChassisModel $transporterEntryPermitChassis
 * @property SafeConductChecklistModel $safeConductChecklist
 * @property SafeConductChecklistDetailModel $safeConductChecklistDetail
 * @property SafeConductChecklistPhotoModel $safeConductChecklistPhoto
 * @property SafeConductRouteModel $safeConductRoute
 * @property SafeConductGroupModel $safeConductGroup
 * @property SafeConductHistoryModel $safeConductHistory
 * @property SafeConductAttachmentModel $safeConductAttachment
 * @property WarehouseOriginTrackingContainerModel $warehouseOriginTrackingContainer
 * @property SoapClientRequest $soapClientRequest
 * @property Uploader $uploader
 * @property Exporter $exporter
 */
class Safe_conduct extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('BookingTypeModel', 'bookingType');
        $this->load->model('BranchModel', 'branch');
        $this->load->model('ComponentModel', 'component');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('EsealModel', 'eseal');
        $this->load->model('HandlingModel', 'handling');
        $this->load->model('HandlingContainerModel', 'handlingContainer');
        $this->load->model('HandlingGoodsModel', 'handlingGoods');
        $this->load->model('HandlingComponentModel', 'handlingComponent');
        $this->load->model('InvoiceModel', 'invoice');
        $this->load->model('InvoiceHandlingModel', 'invoiceHandling');
        $this->load->model('InvoiceDetailModel', 'invoiceDetail');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('SafeConductModel', 'safeConduct');
        $this->load->model('SafeConductContainerModel', 'safeConductContainer');
        $this->load->model('SafeConductGoodsModel', 'safeConductGoods');
        $this->load->model('SafeConductAttachmentModel', 'safeConductAttachment');
        $this->load->model('UploadDocumentModel', 'uploadDocument');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
        $this->load->model('VehicleModel', 'vehicle');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('WorkOrderContainerModel', 'workOrderContainer');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('WorkOrderComponentModel', 'workOrderComponent');
        $this->load->model('SafeConductChecklistModel', 'safeConductChecklist');
        $this->load->model('SafeConductChecklistDetailModel', 'safeConductChecklistDetail');
        $this->load->model('SafeConductChecklistPhotoModel', 'safeConductChecklistPhoto');
        $this->load->model('SafeConductRouteModel', 'safeConductRoute');
        $this->load->model('SafeConductGroupModel', 'safeConductGroup');
        $this->load->model('SafeConductHistoryModel', 'safeConductHistory');
        $this->load->model('TransporterEntryPermitModel', 'transporterEntryPermit');
        $this->load->model('TransporterEntryPermitContainerModel', 'transporterEntryPermitContainer');
        $this->load->model('TransporterEntryPermitGoodsModel', 'transporterEntryPermitGoods');
        $this->load->model('TransporterEntryPermitChecklistModel', 'TransporterEntryPermitChecklist');
        $this->load->model('TransporterEntryPermitChecklistDetailModel', 'TransporterEntryPermitChecklistDetail');
        $this->load->model('WarehouseOriginTrackingContainerModel', 'warehouseOriginTrackingContainer');
        $this->load->model('modules/SoapClientRequest', 'soapClientRequest');
        $this->load->model('TransporterEntryPermitChassisModel', 'transporterEntryPermitChassis');
        $this->load->model('modules/Uploader', 'uploader');
        $this->load->model('modules/S3FileManager', 's3FileManager');
    }

    /**
     * Show all safe conduct in data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized([PERMISSION_SAFE_CONDUCT_IN_VIEW, PERMISSION_SAFE_CONDUCT_OUT_VIEW]);

        if (get_url_param('export')) {
            $this->load->model('modules/Exporter', 'exporter');
            $type = isset($_GET['type']) && strtolower($_GET['type']) != 'all' ? $_GET['type'] : '';
            $this->exporter->exportLargeResourceFromArray("Safe conducts", $this->safeConduct->getAllSafeConducts($type));
        } else {
            $data = [
                'title' => "Safe Conduct",
                'subtitle' => "Data safe conduct",
                'page' => "safe_conduct/index",
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable.
     */
    public function data()
    {
        $type = isset($_GET['type']) && strtolower($_GET['type']) != 'all' ? $_GET['type'] : '';
        $startData = $this->input->get('start');
        $lengthData = $this->input->get('length');
        $searchQuery = trim($this->input->get('search')['value']);
        $orderColumn = $this->input->get('order')[0]['column'];
        $orderColumnOrder = $this->input->get('order')[0]['dir'];

        $safeConducts = $this->safeConduct->getAllSafeConducts($type, $startData, $lengthData, $searchQuery, $orderColumn, $orderColumnOrder);

        $safeConductAttachments = $this->safeConductAttachment->getBy([
            'safe_conduct_attachments.id_safe_conduct' => array_column($safeConducts['data'], 'id')
        ]);

        $no = $startData + 1;
        $safeConductData = [];
        foreach ($safeConducts['data'] as &$row) {
            $row['no'] = $no++;

            $row['safe_conduct_attachments'] = array_filter($safeConductAttachments, function($safeConductAttachment) use ($row) {
                return $safeConductAttachment['id_safe_conduct'] == $row['id'];
            });

            array_push($safeConductData, $row);
        }

        $data = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => $safeConducts['total'],
            "recordsFiltered" => $safeConducts['result'],
            "data" => $safeConductData,
        ];
        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * Show detail of safe conducts checklist_in container
     * @param $id
     */
    public function view_checklist_in($id)
    {
        $safeConductChecklist = $this->safeConductChecklist->getSafeConductChecklistInById($id);
        $safeConductChecklistDetails = $this->safeConductChecklistDetail->getChecklistDetailByChecklistId($safeConductChecklist['id']);
        $safeConductChecklistPhotos = $this->safeConductChecklistPhoto->getBy([
            'safe_conduct_checklist_photos.id_safe_conduct_checklist' => $safeConductChecklist['id'],
            'safe_conduct_checklists.type' => 'CHECK IN'
        ]);

        $data = [
            'title' => "Checklist",
            'subtitle' => "View safe conduct checklist",
            'page' => "safe_conduct/view_checklist_in",
            'safeConductChecklist' => $safeConductChecklist,
            'safeConductChecklistDetails' => $safeConductChecklistDetails,
            'safeConductChecklistPhotos' => $safeConductChecklistPhotos,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show detail of safe conducts checklist out container
     * @param $id
     */
    public function view_checklist_out($id)
    {
        $safeConductChecklist = $this->safeConductChecklist->getSafeConductChecklistOutById($id);
        $safeConductChecklistDetails = $this->safeConductChecklistDetail->getChecklistDetailByChecklistId($safeConductChecklist['id']);
        $safeConductChecklistPhotos = $this->safeConductChecklistPhoto->getBy([
            'safe_conduct_checklist_photos.id_safe_conduct_checklist' => $safeConductChecklist['id'],
            'safe_conduct_checklists.type' => 'CHECK OUT'
        ]);

        $data = [
            'title' => "Checklist",
            'subtitle' => "View safe conduct checklist",
            'page' => "safe_conduct/view_checklist_out",
            'safeConductChecklist' => $safeConductChecklist,
            'safeConductChecklistDetails' => $safeConductChecklistDetails,
            'safeConductChecklistPhotos' => $safeConductChecklistPhotos,
        ];
        $this->load->view('template/layout', $data);
    }

     /**
     * Show detail of safe conducts checklist_in goods
     * @param $id
     */
    public function view_checklist_in_goods($id)
    {
        $safeConductChecklist = $this->safeConductChecklist->getSafeConductChecklistInGoodsById($id);
        $safeConductChecklistDetails = $this->safeConductChecklistDetail->getChecklistDetailByChecklistId($safeConductChecklist['id']);
        $safeConductChecklistPhotos = $this->safeConductChecklistPhoto->getBy([
            'safe_conduct_checklist_photos.id_safe_conduct_checklist' => $safeConductChecklist['id'],
            'safe_conduct_checklists.type' => 'CHECK IN'
        ]);

        $data = [
            'title' => "Checklist",
            'subtitle' => "View safe conduct checklist",
            'page' => "safe_conduct/view_checklist_in",
            'safeConductChecklist' => $safeConductChecklist,
            'safeConductChecklistDetails' => $safeConductChecklistDetails,
            'safeConductChecklistPhotos' => $safeConductChecklistPhotos,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show detail of safe conducts checklist out goods
     * @param $id
     */
    public function view_checklist_out_goods($id)
    {
        $safeConductChecklist = $this->safeConductChecklist->getSafeConductChecklistOutGoodsById($id);
        $safeConductChecklistDetails = $this->safeConductChecklistDetail->getChecklistDetailByChecklistId($safeConductChecklist['id']);
        $safeConductChecklistPhotos = $this->safeConductChecklistPhoto->getBy([
            'safe_conduct_checklist_photos.id_safe_conduct_checklist' => $safeConductChecklist['id'],
            'safe_conduct_checklists.type' => 'CHECK OUT'
        ]);

        $data = [
            'title' => "Checklist",
            'subtitle' => "View safe conduct checklist",
            'page' => "safe_conduct/view_checklist_out",
            'safeConductChecklist' => $safeConductChecklist,
            'safeConductChecklistDetails' => $safeConductChecklistDetails,
            'safeConductChecklistPhotos' => $safeConductChecklistPhotos,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show detail of safe conducts.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized([PERMISSION_SAFE_CONDUCT_IN_VIEW, PERMISSION_SAFE_CONDUCT_OUT_VIEW]);

        $safeConduct = $this->safeConduct->getSafeConductById($id);
        $relatedSafeConducts = $this->safeConduct->getBy(['safe_conducts.id_safe_conduct_group' => if_empty($safeConduct['id_safe_conduct_group'], '0')]);
        $safeConductContainers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($id);
        foreach ($safeConductContainers as &$container) {
            $containerGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConductContainer($container['id']);
            $container['goods'] = $containerGoods;
        }
        $safeConductGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($id, true);
        $safeConductAttachments = $this->safeConductAttachment->getBy([
            'safe_conduct_attachments.id_safe_conduct' => $id
        ]);
        $workOrders = $this->workOrder->getWorkOrdersBySafeConduct($id);

        $safeConductChecklistsIn = $this->safeConductChecklistPhoto->getBy([
            'safe_conduct_checklists.id_safe_conduct' => $id,
            'safe_conduct_checklists.type' => 'CHECK IN'
        ]);
        $safeConductChecklistsOut = $this->safeConductChecklistPhoto->getBy([
            'safe_conduct_checklists.id_safe_conduct' => $id,
            'safe_conduct_checklists.type' => 'CHECK OUT'
        ]);
        $attachmentSealIn = $this->safeConductChecklist->getBy([
            'safe_conduct_checklists.id_safe_conduct' => $id,
            'safe_conduct_checklists.type' => 'CHECK IN',
            'safe_conduct_checklists.attachment_seal !=' => ''
        ]);
        $attachmentSealOut = $this->safeConductChecklist->getBy([
            'safe_conduct_checklists.id_safe_conduct' => $id,
            'safe_conduct_checklists.type' => 'CHECK OUT',
            'safe_conduct_checklists.attachment_seal !=' => ''
        ]);

        $safeConductHistories = $this->safeConductHistory->getBy([
            'id_safe_conduct' => $id,
        ]);

        $data = [
            'title' => "Safe Conduct",
            'subtitle' => "View safe conduct",
            'page' => "safe_conduct/view",
            'safeConduct' => $safeConduct,
            'safeConductContainers' => $safeConductContainers,
            'safeConductGoods' => $safeConductGoods,
            'safeConductAttachments' => $safeConductAttachments,
            'workOrders' => $workOrders,
            'safeConductChecklistsIn' => $safeConductChecklistsIn,
            'safeConductChecklistsOut' => $safeConductChecklistsOut,
            'attachmentSealIn' => $attachmentSealIn,
            'attachmentSealOut' => $attachmentSealOut,
            'relatedSafeConducts' => $relatedSafeConducts,
            'safeConductHistories' => $safeConductHistories,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show detail of safe conducts.
     * @param $id
     */
    public function view_container_submission($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_IN_VIEW);
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_OUT_VIEW);

        $safeConduct = $this->safeConduct->getSafeConductById($id);
        $warehouseOriginContainers = $this->warehouseOriginTrackingContainer->getBy(['id_safe_conduct' => $id]);

        $data = [
            'title' => "Warehouse Origin Container",
            'subtitle' => "View warehouse origin tracker",
            'page' => "safe_conduct/view_container_submission",
            'safeConduct' => $safeConduct,
            'warehouseOriginContainers' => $warehouseOriginContainers,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show create safe conduct form
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_IN_CREATE);
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_OUT_CREATE);

        $data = [
            'title' => "Safe Conduct",
            'subtitle' => "View safe conduct",
            'page' => "safe_conduct/create",
            'workOrders' => $this->workOrder->getWorkOrdersByEmptySafeConduct(),
            'eseals' => $this->eseal->getAll(),
            'drivers' => $this->people->getByType(PeopleModel::$TYPE_DRIVER),
            'expeditions' => $this->people->getByType(PeopleModel::$TYPE_EXPEDITION),
            'vehicles' => $this->vehicle->getAll(),
            'warehouses' => $this->people->getByType([PeopleModel::$TYPE_TPS]),
            //'transporterEntryPermits' => $this->transporterEntryPermit->getBy([
            //    'safe_conducts.id IS NULL' => null,
            //    'transporter_entry_permits.checked_in_at IS NOT NULL' => null
            //]),
            'transporterEntryPermits' => [],
            'booking' => $this->booking->getBookingById($this->input->post('booking')),
            'relatedSafeConducts' => $this->safeConduct->getBy(['safe_conducts.id' => $this->input->post('related_safe_conducts')]),
        ];

        foreach ($data['workOrders'] as &$workOrder) {
            $workOrder['no_chassis'] = '';
            if (!empty($workOrder['id_tep_chassis'])) {
                $chassis = $this->transporterEntryPermitChassis->getById($workOrder['id_tep_chassis']);
                $workOrder['no_chassis'] = $chassis['no_chassis'] ?? '';
            }
        }
        $this->load->view('template/layout', $data);
    }

    /**
     * Save new safe conduct in.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_IN_CREATE);
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_OUT_CREATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('category', 'Safe conduct category', 'trim|required');
            $this->form_validation->set_rules('expedition_type', 'Expedition type', 'trim|required');
            $this->form_validation->set_rules('eseal', 'E-seal', 'trim|integer');
            $this->form_validation->set_rules('vehicle', 'Vehicle type', 'trim|max_length[50]');
            $this->form_validation->set_rules('no_police', 'Police plat number', 'trim|max_length[20]');
            $this->form_validation->set_rules('booking', 'Booking data', 'trim');
            $this->form_validation->set_rules('work_order', 'Job data', 'trim');
            $this->form_validation->set_rules('driver', 'Driver name', 'trim|max_length[50]');
            $this->form_validation->set_rules('expedition', 'Expedition', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('description', 'Description', 'trim|max_length[500]');
            $this->form_validation->set_rules('tep', 'Transporter entry permit', 'trim|integer');
            $this->form_validation->set_rules('source', 'Source', 'trim');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $source = $this->input->post('source');
                $safeConductType = $this->input->post('category');
                $warehouse = $this->input->post('warehouse');
                $expeditionType = $this->input->post('expedition_type');
                $vehicle = $this->input->post('vehicle');
                $bookingId = $this->input->post('booking');
                $relatedSafeConducts = $this->input->post('related_safe_conducts');
                $noPolice = $this->input->post('no_police');
                $driver = $this->input->post('driver');
                $expedition = $this->input->post('expedition');
                $esealId = $this->input->post('eseal');
                $tep = $this->input->post('tep');
                $cy_date = $this->input->post('cy_date');
                $description = $this->input->post('description');

                $containers = $this->input->post('containers');
                $goods = $this->input->post('goods');

                $workOrder = $this->input->post('work_order'); // outbound

                if ($safeConductType == SafeConductModel::TYPE_INBOUND) {
                    $handlingTypeId = get_setting('default_moving_in_handling');
                    $noSafeConduct = $this->safeConduct->getAutoNumberSafeConduct(SafeConductModel::TYPE_CODE_IN);
                } else {
                    $handlingTypeId = get_setting('default_moving_out_handling');
                    $noSafeConduct = $this->safeConduct->getAutoNumberSafeConduct(SafeConductModel::TYPE_CODE_OUT);
                }

                $this->db->trans_start();

                // upload attachment if exist
                $fileName = '';
                $uploadPassed = true;
                if (!empty($_FILES['attachment']['name'])) {
                    $fileName = 'SF_' . time() . '_' . rand(100, 999);
                    $saveTo = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'safe_conducts';
                    if ($this->documentType->makeFolder('safe_conducts')) {
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
                    $booking = $this->booking->getBookingById($bookingId);
                    $getTep = $this->transporterEntryPermit->getByIdNonBase($tep);
                    $customerId = $booking['id_customer'];
                    $creatorId = UserModel::authenticatedUserData('id');
                    if ($safeConductType == 'OUTBOUND') {
                        $workOrderData = $this->workOrder->getWorkOrderById($workOrder);
                        $bookingId = $workOrderData['id_booking'];
                        $customerId = $workOrderData['id_customer'];
                        $this->workOrder->updateWorkOrder([
                            'id_transporter_entry_permit' => if_empty($tep, if_empty($workOrderData['id_transporter_entry_permit'], null))
                        ], $workOrder);
                    }

                    // create new handling
                    $noHandling = $this->handling->getAutoNumberHandlingRequest();
                    $handlingData = [
                        'no_handling' => $noHandling,
                        'id_handling_type' => $handlingTypeId,
                        'id_booking' => $bookingId,
                        'id_customer' => $customerId,
                        'handling_date' => date('Y-m-d H:i:s'),
                        'status' => 'APPROVED',
                        'validated_by' => UserModel::authenticatedUserData('id'),
                        'validated_at' => date('Y-m-d H:i:s'),
                        'created_by' => $creatorId
                    ];
                    $this->handling->createHandling($handlingData);
                    $handlingId = $this->db->insert_id();

                    $components = $this->component->getByHandlingType($handlingTypeId);
                    foreach ($components as $component) {
                        $this->handlingComponent->createHandlingComponent([
                            'id_handling' => $handlingId,
                            'id_component' => $component['id'],
                            'quantity' => $component['default_value'],
                            'description' => $component['component_description'],
                            'status' => 'APPROVED'
                        ]);
                    }

                    // create group if exist
                    $safeConductGroupId = null;
                    if (!empty($relatedSafeConducts)) {
                        $this->safeConductGroup->create([
                            'id_branch' => get_active_branch_id(),
                            'no_safe_conduct_group' => $this->safeConductGroup->getAutoNumber()
                        ]);
                        $safeConductGroupId = $this->db->insert_id();
                        foreach ($relatedSafeConducts as $relatedSafeConduct) {
                            $this->safeConduct->updateSafeConduct([
                                'id_safe_conduct_group' => $safeConductGroupId,
                                'vehicle_type' => $vehicle,
                                'no_police' => $noPolice,
                                'driver' => $driver,
                            ], $relatedSafeConduct);
                        }
                    }

                    // create safe conduct
                    $this->safeConduct->createSafeConduct([
                        'id_booking' => $bookingId,
                        'id_handling' => $handlingId,
                        'id_eseal' => $esealId,
                        'id_source_warehouse' => $warehouse,
                        'id_transporter_entry_permit' => if_empty($tep, null),
                        'id_safe_conduct_group' => if_empty($safeConductGroupId, null),
                        'no_safe_conduct' => $noSafeConduct,
                        'expedition_type' => $expeditionType,
                        'cy_date' => !empty($cy_date) ? date('Y-m-d H:i:s', strtotime($cy_date)) : null,
                        'vehicle_type' => !empty($tep) == true && !empty($getTep['receiver_vehicle']) ? $getTep['receiver_vehicle'] : $vehicle,
                        'no_police' => !empty($tep) == true && !empty($getTep['receiver_no_police']) ? $getTep['receiver_no_police'] : $noPolice,
                        'driver' => !empty($tep) == true && !empty($getTep['receiver_name']) ? $getTep['receiver_name'] : $driver,
                        'expedition' => $expedition,
                        'description' => $description,
                        'type' => $safeConductType,
                        'attachment' => $fileName,
                        'source' => if_empty($source, 'WO'),
                        'security_in_date' => !empty($tep) == true && !empty($getTep['checked_in_at']) ? $getTep['checked_in_at'] : null,
                        'security_out_date' => !empty($tep) == true && !empty($getTep['checked_out_at']) ? $getTep['checked_out_at'] : null,
                        'created_by' => $creatorId
                    ]);
                    $safeConductId = $this->db->insert_id();

                    // if not manually grouped then check if tep is selected
                    if (empty($relatedSafeConducts) && !empty($tep)) {
                        // Check if safe conduct needs to be grouped,
                        // find out the existing data is already grouped
                        $safeConductSameVehicles = $this->safeConduct->getBy([
                            'safe_conducts.id_transporter_entry_permit' => $tep,
                        ]);
                        if (count($safeConductSameVehicles) > 1) {
                            $safeConductGroupId = $safeConductSameVehicles[0]['id_safe_conduct_group']; // pick first data
                            if (empty($safeConductGroupId)) {
                                $this->safeConductGroup->create([
                                    'id_branch' => get_active_branch_id(),
                                    'no_safe_conduct_group' => $this->safeConductGroup->getAutoNumber()
                                ]);
                                $safeConductGroupId = $this->db->insert_id();
                            }
                            foreach ($safeConductSameVehicles as $relatedSafeConduct) {
                                $this->safeConduct->updateSafeConduct([
                                    'id_safe_conduct_group' => $safeConductGroupId,
                                ], $relatedSafeConduct['id']);
                            }
                        }
                    }

                    if ($safeConductType == 'INBOUND') {
                        if(isset($getTep)){
                            $tepContainer = $this->transporterEntryPermitContainer->getTepContainersByTep($tep);
                            $tepGoods = $this->transporterEntryPermitGoods->getTEPGoodsByTEP($tep);
                            $tepChecklist = $this->TransporterEntryPermitChecklist->getTepChecklistByTepId($tep);

                            $this->safeConduct->updateSafeConduct([
                                'id_eseal' => $getTep['id_eseal'],
                                'updated_at' => date('Y-m-d H:i:s'),
                                'updated_by' => UserModel::authenticatedUserData('id')
                            ], $safeConductId);

                            // set tracking data
                            $this->updateSafeConductTrackingRoute($safeConductId);

                            if (!empty($tepContainer)) {
                                foreach ($tepContainer as $container) {
                                    $this->safeConductContainer->createSafeConductContainer([
                                        'id_safe_conduct' => $safeConductId,
                                        'id_booking_container' => $container['id_booking_container'],
                                        'id_container' => $container['id_container'],
                                        'id_position' => if_empty($container['id_position'], null),
                                        'seal' => if_empty($container['seal'], null),
                                        'is_empty' => $container['is_empty'],
                                        'is_hold' => $container['is_hold'],
                                        'status' => $container['status'],
                                        'status_danger' => $container['status_danger'],
                                        'description' => $container['description'],
                                        'quantity' => 1
                                    ]);
                                    $safeConductContainerId = $this->db->insert_id();

                                    $this->handlingContainer->createHandlingContainer([
                                        'id_handling' => $handlingId,
                                        'id_owner' => $customerId,
                                        'id_container' => $container['id_container'],
                                        'id_position' => if_empty($container['id_position'], null),
                                        'seal' => if_empty($container['seal'], null),
                                        'quantity' => 1,
                                        'is_empty' => $container['is_empty'],
                                        'is_hold' => $container['is_hold'],
                                        'status' => $container['status'],
                                        'status_danger' => $container['status_danger'],
                                        'description' => $container['description'],
                                        'created_by' => $creatorId
                                    ]);


                                    if(!empty($tepChecklist)){
                                        foreach ($tepChecklist as $Checklist) {
                                            if(!empty($Checklist['id_container']) && $Checklist['type'] == "CHECK IN" && $Checklist['id_container'] == $container['id']){
                                                $tepChecklistDetails = $this->TransporterEntryPermitChecklistDetail->getTepChecklistDetailByChecklistId($Checklist['id']);

                                                // safe conduct checklist
                                                $this->safeConductChecklist->create([
                                                    'id_safe_conduct' => $safeConductId,
                                                    'id_container' => $safeConductContainerId, //id_safe_conduct_container
                                                    'type' => $Checklist['type'],
                                                    'attachment' => $Checklist['attachment'],
                                                    'attachment_seal' => $Checklist['attachment_seal'],
                                                ]);

                                                //safe conduct detail
                                                $safeConductChecklistId = $this->db->insert_id();
                                                if(!empty($tepChecklistDetails)){
                                                    foreach($tepChecklistDetails as $ChecklistDetail){
                                                        $this->safeConductChecklistDetail->create([
                                                            'id_safe_conduct_checklist' => $safeConductChecklistId,
                                                            'id_checklist' => $ChecklistDetail['id_checklist'],
                                                            'result' => $ChecklistDetail['result'],
                                                            'description' => $ChecklistDetail['description'],
                                                        ]);
                                                    }
                                                }
                                            }

                                            if(!empty($Checklist['id_container']) && $Checklist['type'] == "CHECK OUT" && $Checklist['id_container'] == $container['id']){
                                                $tepChecklistDetails = $this->TransporterEntryPermitChecklistDetail->getTepChecklistDetailByChecklistId($Checklist['id']);

                                                // safe conduct checklist
                                                $this->safeConductChecklist->create([
                                                    'id_safe_conduct' => $safeConductId,
                                                    'id_container' => $safeConductContainerId, //id_safe_conduct_container
                                                    'type' => $Checklist['type'],
                                                    'attachment' => $Checklist['attachment'],
                                                    'attachment_seal' => $Checklist['attachment_seal'],
                                                ]);

                                                //safe conduct detail
                                                $safeConductChecklistId = $this->db->insert_id();
                                                if(!empty($tepChecklistDetails)){
                                                    foreach($tepChecklistDetails as $ChecklistDetail){
                                                        $this->safeConductChecklistDetail->create([
                                                            'id_safe_conduct_checklist' => $safeConductChecklistId,
                                                            'id_checklist' => $ChecklistDetail['id_checklist'],
                                                            'result' => $ChecklistDetail['result'],
                                                            'description' => $ChecklistDetail['description'],
                                                        ]);
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            if (!empty($tepGoods)) {
                                foreach ($tepGoods as $item) {
                                    $this->safeConductGoods->createSafeConductGoods([
                                        'id_safe_conduct' => $safeConductId,
                                        'id_delivery_order_goods' => null,
                                        'id_booking_goods' => $item['id_booking_goods'],
                                        'id_goods' => $item['id_goods'],
                                        'id_unit' => $item['id_unit'],
                                        'id_position' => if_empty($item['id_position'], null),
                                        'no_pallet' => if_empty($item['no_pallet'], null),
                                        'quantity' => $item['quantity'],
                                        'unit_weight' => $item['unit_weight'],
                                        'unit_gross_weight' => $item['unit_gross_weight'],
                                        'unit_length' => $item['unit_length'],
                                        'unit_width' => $item['unit_width'],
                                        'unit_height' => $item['unit_height'],
                                        'unit_volume' => $item['unit_volume'],
                                        'is_hold' => $item['is_hold'],
                                        'status' => $item['status'],
                                        'status_danger' => $item['status_danger'],
                                        'ex_no_container' => $item['ex_no_container'],
                                        'description' => $item['description']
                                    ]);

                                    $this->handlingGoods->createHandlingGoods([
                                        'id_handling' => $handlingId,
                                        'id_owner' => $customerId,
                                        'id_goods' => $item['id_goods'],
                                        'id_unit' => $item['id_unit'],
                                        'id_position' => if_empty($item['id_position'], null),
                                        'no_pallet' => if_empty($item['no_pallet'], null),
                                        'quantity' => $item['quantity'],
                                        'unit_weight' => $item['unit_weight'],
                                        'unit_gross_weight' => $item['unit_gross_weight'],
                                        'unit_length' => $item['unit_length'],
                                        'unit_width' => $item['unit_width'],
                                        'unit_height' => $item['unit_height'],
                                        'unit_volume' => $item['unit_volume'],
                                        'is_hold' => $item['is_hold'],
                                        'status' => $item['status'],
                                        'status_danger' => $item['status_danger'],
                                        'ex_no_container' => if_empty($item['ex_no_container'], null),
                                        'created_by' => $creatorId
                                    ]);
                                }

                                if(!empty($tepChecklist)){
                                    foreach ($tepChecklist as $Checklist) {
                                        if(empty($Checklist['id_container']) && $Checklist['type'] == "CHECK IN"){
                                            $tepChecklistDetails = $this->TransporterEntryPermitChecklistDetail->getTepChecklistDetailByChecklistId($Checklist['id']);

                                            // safe conduct checklist
                                            $this->safeConductChecklist->create([
                                                'id_safe_conduct' => $safeConductId,
                                                'id_container' => null, //id_safe_conduct_container
                                                'type' => $Checklist['type'],
                                                'attachment' => $Checklist['attachment'],
                                            ]);

                                            //safe conduct detail
                                            $safeConductChecklistId = $this->db->insert_id();
                                            if(!empty($tepChecklistDetails)){
                                                foreach($tepChecklistDetails as $ChecklistDetail){
                                                    $this->safeConductChecklistDetail->create([
                                                        'id_safe_conduct_checklist' => $safeConductChecklistId,
                                                        'id_checklist' => $ChecklistDetail['id_checklist'],
                                                        'result' => $ChecklistDetail['result'],
                                                        'description' => $ChecklistDetail['description'],
                                                    ]);
                                                }
                                            }
                                        }

                                        if(empty($Checklist['id_container']) && $Checklist['type'] == "CHECK OUT"){
                                            $tepChecklistDetails = $this->TransporterEntryPermitChecklistDetail->getTepChecklistDetailByChecklistId($Checklist['id']);

                                            // safe conduct checklist
                                            $this->safeConductChecklist->create([
                                                'id_safe_conduct' => $safeConductId,
                                                'id_container' => null, //id_safe_conduct_container
                                                'type' => $Checklist['type'],
                                                'attachment' => $Checklist['attachment'],
                                            ]);

                                            //safe conduct detail
                                            $safeConductChecklistId = $this->db->insert_id();
                                            if(!empty($tepChecklistDetails)){
                                                foreach($tepChecklistDetails as $ChecklistDetail){
                                                    $this->safeConductChecklistDetail->create([
                                                        'id_safe_conduct_checklist' => $safeConductChecklistId,
                                                        'id_checklist' => $ChecklistDetail['id_checklist'],
                                                        'result' => $ChecklistDetail['result'],
                                                        'description' => $ChecklistDetail['description'],
                                                    ]);
                                                }
                                            }
                                        }
                                    }
                                }
                            }

                            if(!empty($tepChecklist)){
                                foreach ($tepChecklist as $Checklist) {
                                    if(empty($Checklist['id_container']) && $Checklist['type'] == "CHECK IN"){
                                        $tepChecklistDetails = $this->TransporterEntryPermitChecklistDetail->getTepChecklistDetailByChecklistId($Checklist['id']);

                                        // safe conduct checklist
                                        $this->safeConductChecklist->create([
                                            'id_safe_conduct' => $safeConductId,
                                            'id_container' => null, //id_safe_conduct_container
                                            'type' => $Checklist['type'],
                                            'attachment' => $Checklist['attachment'],
                                        ]);

                                        //safe conduct detail
                                        $safeConductChecklistId = $this->db->insert_id();
                                        if(!empty($tepChecklistDetails)){
                                            foreach($tepChecklistDetails as $ChecklistDetail){
                                                $this->safeConductChecklistDetail->create([
                                                    'id_safe_conduct_checklist' => $safeConductChecklistId,
                                                    'id_checklist' => $ChecklistDetail['id_checklist'],
                                                    'result' => $ChecklistDetail['result'],
                                                    'description' => $ChecklistDetail['description'],
                                                ]);
                                            }
                                        }
                                    }

                                    if(empty($Checklist['id_container']) && $Checklist['type'] == "CHECK OUT"){
                                        $tepChecklistDetails = $this->TransporterEntryPermitChecklistDetail->getTepChecklistDetailByChecklistId($Checklist['id']);

                                        // safe conduct checklist
                                        $this->safeConductChecklist->create([
                                            'id_safe_conduct' => $safeConductId,
                                            'id_container' => null, //id_safe_conduct_container
                                            'type' => $Checklist['type'],
                                            'attachment' => $Checklist['attachment'],
                                        ]);

                                        //safe conduct detail
                                        $safeConductChecklistId = $this->db->insert_id();
                                        if(!empty($tepChecklistDetails)){
                                            foreach($tepChecklistDetails as $ChecklistDetail){
                                                $this->safeConductChecklistDetail->create([
                                                    'id_safe_conduct_checklist' => $safeConductChecklistId,
                                                    'id_checklist' => $ChecklistDetail['id_checklist'],
                                                    'result' => $ChecklistDetail['result'],
                                                    'description' => $ChecklistDetail['description'],
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }

                        }
                    } else {
                        // update safe conduct link
                        $this->workOrder->updateWorkOrder([
                            'id_safe_conduct' => $safeConductId,
                            'id_transporter_entry_permit' => if_empty($tep, null),
                        ], $workOrder);

                        // copy result of job to safe conduct
                        $containers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrder, true);
                        foreach ($containers as $container) {
                            $this->safeConductContainer->createSafeConductContainer([
                                'id_safe_conduct' => $safeConductId,
                                'id_booking_reference' => if_empty($container['id_booking_reference'], null),
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
                            $safeConductContainerId = $this->db->insert_id();
                            $containerGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($container['id']);
                            foreach ($containerGoods as $item) {
                                $this->safeConductGoods->createSafeConductGoods([
                                    'id_safe_conduct' => $safeConductId,
                                    'id_booking_reference' => if_empty($item['id_booking_reference'], null),
                                    'id_safe_conduct_container' => $safeConductContainerId,
                                    'id_goods' => $item['id_goods'],
                                    'id_unit' => $item['id_unit'],
                                    'id_position' => if_empty($item['id_position'], null),
                                    'no_pallet' => $item['no_pallet'],
                                    'quantity' => $item['quantity'],
                                    'unit_weight' => $item['unit_weight'],
                                    'unit_gross_weight' => $item['unit_gross_weight'],
                                    'unit_length' => $item['unit_length'],
                                    'unit_width' => $item['unit_width'],
                                    'unit_height' => $item['unit_height'],
                                    'unit_volume' => $item['unit_volume'],
                                    'is_hold' => $item['is_hold'],
                                    'status' => $item['status'],
                                    'status_danger' => $item['status_danger'],
                                    'ex_no_container' => $item['ex_no_container'],
                                    'description' => $item['description']
                                ]);
                            }

                            $this->handlingContainer->createHandlingContainer([
                                'id_handling' => $handlingId,
                                'id_booking_reference' => if_empty($container['id_booking_reference'], null),
                                'id_owner' => $customerId,
                                'id_container' => $container['id_container'],
                                'id_position' => if_empty($container['id_position']),
                                'quantity' => 1,
                                'seal' => $container['seal'],
                                'is_empty' => $container['is_empty'],
                                'is_hold' => $container['is_hold'],
                                'status' => $container['status'],
                                'status_danger' => $container['status_danger'],
                                'description' => $container['description'],
                                'created_by' => UserModel::authenticatedUserData('id')
                            ]);

                        }

                        $goods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrder, true, true);
                        foreach ($goods as $item) {
                            $this->safeConductGoods->createSafeConductGoods([
                                'id_safe_conduct' => $safeConductId,
                                'id_booking_reference' => if_empty($item['id_booking_reference'], null),
                                'id_goods' => $item['id_goods'],
                                'id_unit' => $item['id_unit'],
                                'id_position' => if_empty($item['id_position'], null),
                                'no_pallet' => $item['no_pallet'],
                                'quantity' => $item['quantity'],
                                'unit_weight' => $item['unit_weight'],
                                'unit_gross_weight' => $item['unit_gross_weight'],
                                'unit_length' => $item['unit_length'],
                                'unit_width' => $item['unit_width'],
                                'unit_height' => $item['unit_height'],
                                'unit_volume' => $item['unit_volume'],
                                'is_hold' => $item['is_hold'],
                                'status' => $item['status'],
                                'status_danger' => $item['status_danger'],
                                'ex_no_container' => $item['ex_no_container'],
                                'description' => $item['description']
                            ]);

                            $this->handlingGoods->createHandlingGoods([
                                'id_handling' => $handlingId,
                                'id_booking_reference' => if_empty($item['id_booking_reference'], null),
                                'id_owner' => $customerId,
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
                                'ex_no_container' => $item['ex_no_container'],
                                'created_by' => UserModel::authenticatedUserData('id')
                            ]);
                        }

                        if(isset($getTep)){
                            $tepChecklist = $this->TransporterEntryPermitChecklist->getTepChecklistByTepId($tep);

                            if(!empty($tepChecklist)){
                                foreach ($tepChecklist as $Checklist) {
                                    if(empty($Checklist['id_container']) && $Checklist['type'] == "CHECK IN"){
                                        $tepChecklistDetails = $this->TransporterEntryPermitChecklistDetail->getTepChecklistDetailByChecklistId($Checklist['id']);

                                        // safe conduct checklist
                                        $this->safeConductChecklist->create([
                                            'id_safe_conduct' => $safeConductId,
                                            'id_container' => null, //id_safe_conduct_container
                                            'type' => $Checklist['type'],
                                            'attachment' => $Checklist['attachment'],
                                        ]);

                                        //safe conduct detail
                                        $safeConductChecklistId = $this->db->insert_id();
                                        if(!empty($tepChecklistDetails)){
                                            foreach($tepChecklistDetails as $ChecklistDetail){
                                                $this->safeConductChecklistDetail->create([
                                                    'id_safe_conduct_checklist' => $safeConductChecklistId,
                                                    'id_checklist' => $ChecklistDetail['id_checklist'],
                                                    'result' => $ChecklistDetail['result'],
                                                    'description' => $ChecklistDetail['description'],
                                                ]);
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }

                    // create job moving and detail
                    if($expeditionType == "EXTERNAL"){
                        $safeConduct = $this->safeConduct->getSafeConductById($safeConductId);
                        $handling = $this->handling->getHandlingById($safeConduct['id_handling']);
                        $handlingContainers = $this->handlingContainer->getHandlingContainersByHandling($safeConduct['id_handling']);
                        $handlingGoods = $this->handlingGoods->getHandlingGoodsByHandling($safeConduct['id_handling'], true);

                        // push container to TPS services
                        $this->submitWarehouseOriginContainer($safeConduct);

                        $mode = '';
                        if (!empty($handlingContainers)) {
                            $mode = 'C';
                        }
                        if (!empty($handlingGoods)) {
                            $mode = 'G';
                        }

                        if (!empty($handlingContainers) && !empty($handlingGoods)) {
                            $mode = 'CG';
                        }

                        $noWorkOrder = $this->workOrder->getAutoNumberWorkOrder($safeConduct['handling_code']);
                        $this->workOrder->createWorkOrder([
                            'no_work_order' => $noWorkOrder,
                            'queue' => 0,
                            'id_handling' => $safeConduct['id_handling'],
                            'id_safe_conduct' => $safeConduct['id'],
                            'status' => 'COMPLETED',
                            'taken_by' => UserModel::authenticatedUserData('id'),
                            'taken_at' => date('Y-m-d H:i:s'),
                            'completed_at' => date('Y-m-d H:i:s'),
                            'gate_in_date' => date('Y-m-d H:i:s'),
                            'gate_out_date' => date('Y-m-d H:i:s'),
                            'description' => $description,
                            'mode' => $mode,
                            'created_by' => UserModel::authenticatedUserData('id')
                        ]);
                        $workOrderId = $this->db->insert_id();

                        $components = $this->component->getByHandlingType($handling['id_handling_type']);
                        foreach ($components as $component) {
                            $this->workOrderComponent->createWorkOrderComponent([
                                'id_work_order' => $workOrderId,
                                'id_component' => $component['id'],
                                'quantity' => $component['default_value'],
                                'description' => $component['component_description'],
                                'status' => 'APPROVED'
                            ]);
                        }

                        foreach ($handlingContainers as $handlingContainer) {
                            $this->workOrderContainer->insertWorkOrderContainer([
                                'id_work_order' => $workOrderId,
                                'id_booking_reference' => if_empty($handlingContainer['id_booking_reference'], null),
                                'id_owner' => $handlingContainer['id_owner'],
                                'id_container' => $handlingContainer['id_container'],
                                'id_position' => if_empty($handlingContainer['id_position'], null),
                                'description' => $handlingContainer['description'],
                                'seal' => $handlingContainer['seal'],
                                'is_empty' => $handlingContainer['is_empty'],
                                'status' => $handlingContainer['status'],
                                'quantity' => $handlingContainer['quantity'],
                                'created_by' => UserModel::authenticatedUserData('id')
                            ]);
                        }

                        foreach ($handlingGoods as $handlingItem) {
                            $this->workOrderGoods->insertWorkOrderGoods([
                                'id_work_order' => $workOrderId,
                                'id_booking_reference' => if_empty($handlingItem['id_booking_reference'], null),
                                'id_owner' => $handlingItem['id_owner'],
                                'id_goods' => $handlingItem['id_goods'],
                                'id_unit' => $handlingItem['id_unit'],
                                'id_position' => if_empty($handlingItem['id_position'], null),
                                'quantity' => $handlingItem['quantity'],
                                'unit_weight' => $handlingItem['unit_weight'],
                                'unit_gross_weight' => $handlingItem['unit_gross_weight'],
                                'unit_length' => $handlingItem['unit_length'],
                                'unit_width' => $handlingItem['unit_width'],
                                'unit_height' => $handlingItem['unit_height'],
                                'unit_volume' => $handlingItem['unit_volume'],
                                'ex_no_container' => $handlingItem['ex_no_container'],
                                'status' => $handlingItem['status'],
                                'no_pallet' => $handlingItem['no_pallet'],
                                'created_by' => UserModel::authenticatedUserData('id')
                            ]);
                        }
                    }

                    $this->db->trans_complete();

                    if ($this->db->trans_status()) {
                        flash('success', "Safe conduct <strong>{$noSafeConduct}</strong> successfully created");
                        redirect("safe_conduct");
                    } else {
                        flash('danger', "Create safe conduct <strong>{$noSafeConduct}</strong> failed, try again or contact administrator");
                    }
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->create();
    }

     /**
     * Edit desc safe conduct out.
     * @param $id
     */
    public function edit_description($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_OUT_EDIT);
        $safeConduct = $this->safeConduct->getSafeConductById($id);

        $data = [
            'title' => "Edit Safe Conduct",
            'subtitle' => "Edit Description Safe Conduct Out ",
            'page' => "safe_conduct/edit_description",
            'safeConduct' => $safeConduct,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Update desc safe conduct out.
     * @param $id
     */
    public function update_description($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_OUT_EDIT);
        
        $safeConduct = $this->safeConduct->getSafeConductById($id);
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('description', 'Description', 'trim|max_length[500]');
            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $description = $this->input->post('description');

               // update safe conduct
                $updated = $this->safeConduct->updateSafeConduct([
                    'description' => $description,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => UserModel::authenticatedUserData('id'),
                ], $id);


                if ($updated) {
                    flash('success', "Safe conduct <strong>{$safeConduct['no_safe_conduct']}</strong> successfully description updated");
                    redirect("safe_conduct");
                } else {
                    flash('danger', "Update description safe conduct <strong>{$safeConduct['no_safe_conduct']}</strong> failed, try again or contact administrator");
                }

            }

        }else{
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->edit_description($id);
    }

    /**
     * Edit desc safe conduct out.
     * @param $id
     */
    public function edit_tps($id)
    {
        AuthorizationModel::isAuthorized(PERMISSION_SAFE_CONDUCT_IN_EDIT);

        $data = [
            'title' => "Edit TPS Safe Conduct",
            'subtitle' => "Edit TPS data",
            'page' => "safe_conduct/edit_tps",
            'safeConduct' => $this->safeConduct->getSafeConductById($id),
            'warehouses' => $this->people->getByType([PeopleModel::$TYPE_TPS]),
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Update tps safe conduct.
     *
     * @param $id
     */
    public function update_tps($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_IN_EDIT);

        $safeConduct = $this->safeConduct->getSafeConductById($id);
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('warehouse', 'Warehouse of Origin', 'required');
            $this->form_validation->set_rules('tps_gate_out_date', 'Gate Out TPS', 'required');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $sourceWarehouseId = $this->input->post('warehouse');
                $tpsGateOutDate = $this->input->post('tps_gate_out_date');

                $updated = $this->safeConduct->updateSafeConduct([
                    'id_source_warehouse' => $sourceWarehouseId,
                    'tps_gate_out_date' => format_date($tpsGateOutDate),
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => UserModel::authenticatedUserData('id'),
                ], $id);

                if ($updated) {
                    flash('success', "Safe conduct {$safeConduct['no_safe_conduct']} successfully description updated", 'safe-conduct');
                } else {
                    flash('danger', "Update safe conduct {$safeConduct['no_safe_conduct']} failed", 'safe-conduct');
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->edit_tps($id);
    }

    /**
     * Edit safe conduct data.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_IN_EDIT);
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_OUT_EDIT);

        $safeConduct = $this->safeConduct->getSafeConductById($id);
        $relatedSafeConducts = $this->safeConduct->getBy([
            'safe_conducts.id!=' => $id,
            'safe_conducts.id_safe_conduct_group' => if_empty($safeConduct['id_safe_conduct_group'], '0')
        ]);
        $containers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($id);
        foreach ($containers as &$container) {
            $container['id_reference'] = $container['id_booking_container'];
        }
        $goods = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($id, true);
        foreach ($goods as &$item) {
            $item['id_reference'] = $item['id_booking_goods'];
        }

        $workOrders = $this->workOrder->getWorkOrdersByEmptySafeConduct($safeConduct['id_work_order']);
        if($safeConduct['type'] == 'INBOUND'){
            $transporterEntryPermits = $this->transporterEntryPermit->getBy(['safe_conducts.id IS NULL' => null, 'transporter_entry_permits.tep_category' =>'INBOUND', 'transporter_entry_permit_bookings.id_booking' => $safeConduct['id_booking'], 'transporter_entry_permits.checked_in_at IS NOT NULL' => null ]);
        }else{
            $transporterEntryPermits = $this->transporterEntryPermit->getBy(['transporter_entry_permits.tep_category' =>'OUTBOUND', 'transporter_entry_permit_bookings.id_booking IS NULL' => null, 'transporter_entry_permits.checked_out_at IS NULL' => null ]);
        }
        $data = [
            'title' => "Safe Conduct",
            'subtitle' => "Edit safe conduct",
            'page' => "safe_conduct/edit",
            'bookings' => $this->booking->getAllBookings(['category' => $safeConduct['type'], 'status' => BookingModel::STATUS_APPROVED]),
            'workOrders' => $workOrders,
            'eseals' => $this->eseal->getAll(),
            'safeConduct' => $safeConduct,
            'relatedSafeConducts' => $relatedSafeConducts,
            'containers' => $containers,
            'goods' => $goods,
            'drivers' => $this->people->getByType(PeopleModel::$TYPE_DRIVER),
            'expeditions' => $this->people->getByType(PeopleModel::$TYPE_EXPEDITION),
            'vehicles' => $this->vehicle->getAll(),
            'warehouses' => $this->people->getByType([PeopleModel::$TYPE_TPS]),
            'transporterEntryPermits' => $transporterEntryPermits
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Centralize update tracking data.
     *
     * @param $id
     */
    private function updateSafeConductTrackingRoute($id)
    {
        // check if safe conduct if not set (OR delete existing routes and re-insert)
        $safeConductRoutes = $this->safeConductRoute->getBy(['id_safe_conduct' => $id]);
        if (!empty($safeConductRoutes)) {
            return;
        }
        $safeConduct = $this->safeConduct->getSafeConductById($id);
        if ($safeConduct['expedition_type'] == 'INTERNAL') {
            if (!empty($safeConduct['security_in_date']) && !empty($safeConduct['security_out_date'])) {
                $this->setTrackingRoute($safeConduct, $safeConduct['security_in_date'], $safeConduct['security_out_date']);
            }
        } else {
            if (!empty($safeConduct['security_in_date'])) {
                $this->setTrackingRoute($safeConduct, date('Y-m-d H:i:s', strtotime('-3 hour', strtotime($safeConduct['security_in_date']))), $safeConduct['security_in_date']);
            }
        }
    }

    /**
     * Update old safe conduct in.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_IN_EDIT);
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_OUT_EDIT);

        $edit_type = $this->input->get('edit_type');
        if(isset($edit_type)){
            $safeConduct = $this->safeConduct->getSafeConductById($id);
            $handling = $this->handling->getHandlingById($safeConduct['id_handling']);

            if($safeConduct['type'] == 'INBOUND'){
                $containers = $this->input->post('containers');
                $goods = $this->input->post('goods');
                $esealId = $this->input->post('eseal');

                $this->db->trans_start();

                // update safe conduct
                if(empty($safeConduct['id_eseal'])){
                    $this->safeConduct->updateSafeConduct([
                        'id_eseal' => if_empty($esealId, null),
                        'updated_at' => date('Y-m-d H:i:s'),
                        'updated_by' => UserModel::authenticatedUserData('id')
                    ], $id);

                    $this->updateSafeConductTrackingRoute($id);
                }

                if (!empty($containers)) {
                    foreach ($containers as $container) {
                        $this->safeConductContainer->createSafeConductContainer([
                            'id_safe_conduct' => $id,
                            'id_booking_reference' => if_empty($container['id_booking_reference'], null),
                            'id_booking_container' => $container['id_reference'],
                            'id_container' => $container['id_container'],
                            'id_position' => if_empty($container['id_position'], null),
                            'seal' => if_empty($container['seal'], null),
                            'is_empty' => $container['is_empty'],
                            'is_hold' => $container['is_hold'],
                            'status' => $container['status'],
                            'status_danger' => $container['status_danger'],
                            'description' => $container['description'],
                            'quantity' => 1
                        ]);

                        if (!empty($handling)) {
                            $this->handlingContainer->createHandlingContainer([
                                'id_handling' => $handling['id'],
                                'id_booking_reference' => if_empty($container['id_booking_reference'], null),
                                'id_owner' => $handling['id_customer'],
                                'id_container' => $container['id_container'],
                                'id_position' => if_empty($container['id_position'], null),
                                'seal' => if_empty($container['seal'], null),
                                'quantity' => 1,
                                'is_empty' => $container['is_empty'],
                                'is_hold' => $container['is_hold'],
                                'status' => $container['status'],
                                'status_danger' => $container['status_danger'],
                                'description' => $container['description'],
                                'created_by' => UserModel::authenticatedUserData('id'),
                            ]);
                        }
                    }
                }

                if (!empty($goods)) {
                    foreach ($goods as $item) {
                        $this->safeConductGoods->createSafeConductGoods([
                            'id_safe_conduct' => $id,
                            'id_booking_reference' => if_empty($item['id_booking_reference'], null),
                            'id_delivery_order_goods' => null,
                            'id_booking_goods' => $item['id_reference'],
                            'id_goods' => $item['id_goods'],
                            'id_unit' => $item['id_unit'],
                            'id_position' => if_empty($item['id_position'], null),
                            'no_pallet' => if_empty($item['no_pallet'], null),
                            'quantity' => $item['quantity'],
                            'unit_weight' => $item['unit_weight'],
                            'unit_gross_weight' => $item['unit_gross_weight'],
                            'unit_length' => $item['unit_length'],
                            'unit_width' => $item['unit_width'],
                            'unit_height' => $item['unit_height'],
                            'unit_volume' => $item['unit_volume'],
                            'is_hold' => $item['is_hold'],
                            'status' => $item['status'],
                            'status_danger' => $item['status_danger'],
                            'ex_no_container' => $item['ex_no_container'],
                            'description' => $item['description']
                        ]);

                        $this->handlingGoods->createHandlingGoods([
                            'id_handling' => $handling['id'],
                            'id_booking_reference' => if_empty($item['id_booking_reference'], null),
                            'id_owner' => $handling['id_customer'],
                            'id_goods' => $item['id_goods'],
                            'id_unit' => $item['id_unit'],
                            'id_position' => if_empty($item['id_position'], null),
                            'no_pallet' => if_empty($item['no_pallet'], null),
                            'quantity' => $item['quantity'],
                            'unit_weight' => $item['unit_weight'],
                            'unit_gross_weight' => $item['unit_gross_weight'],
                            'unit_length' => $item['unit_length'],
                            'unit_width' => $item['unit_width'],
                            'unit_height' => $item['unit_height'],
                            'unit_volume' => $item['unit_volume'],
                            'is_hold' => $item['is_hold'],
                            'status' => $item['status'],
                            'status_danger' => $item['status_danger'],
                            'ex_no_container' => if_empty($item['ex_no_container'], null),
                            'created_by' => UserModel::authenticatedUserData('id'),
                        ]);
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('warning', "Safe conduct <strong>{$safeConduct['no_safe_conduct']}</strong> successfully updated");
                } else {
                    flash('danger', "Update safe conduct <strong>{$safeConduct['no_safe_conduct']}</strong> failed, try again or contact administrator");
                }
            }

            if($edit_type == "update_data"){
                redirect("safe_conduct");
            }else{
                redirect('security/check?code=' . $safeConduct['no_safe_conduct']);
            }

        }else{
            if ($this->input->server('REQUEST_METHOD') == "POST") {
                $this->form_validation->set_rules('id', 'Safe conduct data', 'trim|required|integer');
                $this->form_validation->set_rules('category', 'Safe conduct category', 'trim|required');
                $this->form_validation->set_rules('expedition_type', 'Expedition type', 'trim|required');
                $this->form_validation->set_rules('eseal', 'E-seal', 'trim|integer');
                $this->form_validation->set_rules('vehicle', 'Vehicle type', 'trim|required|max_length[50]');
                $this->form_validation->set_rules('no_police', 'Police plat number', 'trim|required|max_length[20]');
                $this->form_validation->set_rules('booking', 'Booking data', 'trim');
                $this->form_validation->set_rules('work_order', 'Job data', 'trim');
                $this->form_validation->set_rules('driver', 'Driver name', 'trim|required|max_length[50]');
                $this->form_validation->set_rules('expedition', 'Expedition', 'trim|required|max_length[100]');
                $this->form_validation->set_rules('description', 'Description', 'trim|max_length[500]');
                $this->form_validation->set_rules('tep', 'Transporter entry permit', 'trim|integer');
                $this->form_validation->set_rules('source', 'Source', 'trim');

                if ($this->form_validation->run() == FALSE) {
                    flash('warning', 'Form inputs are invalid');
                } else {
                    $id = $this->input->post('id');
                    $source = $this->input->post('source');
                    $warehouse = $this->input->post('warehouse');
                    $safeConductType = $this->input->post('category');
                    $expeditionType = $this->input->post('expedition_type');
                    $vehicle = $this->input->post('vehicle');
                    $bookingId = $this->input->post('booking');
                    $relatedSafeConducts = $this->input->post('related_safe_conducts');
                    $noPolice = $this->input->post('no_police');
                    $driver = $this->input->post('driver');
                    $expedition = $this->input->post('expedition');
                    $eseal = $this->input->post('eseal');
                    $tep = $this->input->post('tep');
                    $description = $this->input->post('description');
                    $containers = $this->input->post('containers');
                    $goods = $this->input->post('goods');
                    $workOrder = $this->input->post('work_order');
                    $cy_date = $this->input->post('cy_date');

                    if ($safeConductType == SafeConductModel::TYPE_INBOUND) {
                        $handlingTypeId = get_setting('default_moving_in_handling');
                    } else {
                        $handlingTypeId = get_setting('default_moving_out_handling');
                        $workOrderData = $this->workOrder->getWorkOrderById($workOrder);
                        $bookingId = $workOrderData['id_booking'];
                    }

                    $this->db->trans_start();

                    $safeConduct = $this->safeConduct->getSafeConductById($id);
                    $getTep = $this->transporterEntryPermit->getByIdNonBase($tep);
                    $handling = $this->handling->getHandlingById($safeConduct['id_handling']);

                    // upload attachment if exist, set default old name just in case the attachment does not change
                    $fileName = $safeConduct['attachment'];
                    $uploadPassed = true;
                    if (!empty($_FILES['attachment']['name'])) {
                        // setup location and file name
                        $fileName = 'SF_' . time() . '_' . rand(100, 999);
                        $saveTo = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'safe_conducts';

                        // find or create base folder
                        if ($this->documentType->makeFolder('safe_conducts')) {
                            // try upload with standard config
                            $upload = $this->uploadDocumentFile->uploadTo('attachment', $fileName, $saveTo);
                            if (!$upload['status']) {
                                $uploadPassed = false;
                                flash('warning', $upload['errors']);
                            } else {
                                // delete old file
                                if (!empty($fileName)) {
                                    $this->uploadDocumentFile->deleteFile($safeConduct['attachment'], $saveTo);
                                }
                                // put new file name
                                $fileName = $upload['data']['file_name'];
                            }
                        } else {
                            $uploadPassed = false;
                            flash('warning', 'Folder safe_conducts is missing or failed to be created, try again');
                        }
                    }

                    if ($uploadPassed) {
                        $creatorId = UserModel::authenticatedUserData('id');

                        // release eseal
                        if (empty($safeConduct['security_out_date'])) {
                            if (!empty($safeConduct['id_eseal'])) {
                                $this->eseal->update([
                                    'is_used' => 0,
                                    'used_in' => ''
                                ], $safeConduct['id_eseal']);
                            }

                            if (!empty($eseal)) {
                                $this->eseal->update([
                                    'is_used' => 1,
                                    'used_in' => $safeConduct['no_safe_conduct']
                                ], $eseal);
                            }
                        }

                        // update handling if necessary
                        $this->handling->updateHandling([
                            'id_handling_type' => $handlingTypeId,
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => UserModel::authenticatedUserData('id')
                        ], $safeConduct['id_handling']);

                        // re-insert handling component
                        $this->handlingComponent->deleteHandlingComponentByHandling($safeConduct['id_handling']);
                        $components = $this->component->getByHandlingType($handlingTypeId);
                        foreach ($components as $component) {
                            $this->handlingComponent->createHandlingComponent([
                                'id_handling' => $handling['id'],
                                'id_component' => $component['id'],
                                'quantity' => $component['default_value'],
                                'description' => $component['component_description'],
                                'status' => 'APPROVED'
                            ]);
                        }

                        // TODO: update safe conduct group
                        $safeConductGroupId = if_empty($safeConduct['id_safe_conduct_group'], null);

                        // reset group first and add again later
                        if (!empty($safeConductGroupId)) {
                            $this->safeConduct->updateSafeConduct([
                                'id_safe_conduct_group' => null
                            ], ['id_safe_conduct_group' => $safeConductGroupId]);
                        }

                        if (!empty($relatedSafeConducts)) {
                            // create group if current group does not exist
                            if (empty($safeConductGroupId)) {
                                $this->safeConductGroup->create([
                                    'id_branch' => get_active_branch_id(),
                                    'no_safe_conduct_group' => $this->safeConductGroup->getAutoNumber()
                                ]);
                                $safeConductGroupId = $this->db->insert_id();
                            }
                            // add to group again
                            foreach ($relatedSafeConducts as $relatedSafeConductId) {
                                $this->safeConduct->updateSafeConduct([
                                    'id_safe_conduct_group' => $safeConductGroupId,
                                    'vehicle_type' => $vehicle,
                                    'no_police' => $noPolice,
                                    'driver' => $driver,
                                ], $relatedSafeConductId);
                            }
                        }

                        // update safe conduct
                        $this->safeConduct->updateSafeConduct([
                            'id_booking' => $bookingId,
                            'id_source_warehouse' => $warehouse,
                            'id_transporter_entry_permit' => if_empty($tep, null),
                            'id_safe_conduct_group' => if_empty($safeConductGroupId, null),
                            'expedition_type' => $expeditionType,
                            'vehicle_type' => $vehicle,
                            'no_police' => $noPolice,
                            'driver' => $driver,
                            'expedition' => $expedition,
                            'id_eseal' => $eseal,
                            'description' => $description,
                            'type' => $safeConductType,
                            'attachment' => $fileName,
                            'cy_date' => !empty($cy_date) ? date('Y-m-d H:i:s', strtotime($cy_date)) : null,
                            'source' => if_empty($source, 'WO'),
                            'updated_at' => date('Y-m-d H:i:s'),
                            'updated_by' => UserModel::authenticatedUserData('id'),
                        ], $id);

                        if ($safeConductType == 'INBOUND') {
                            $this->safeConductGoods->deleteSafeConductGoodsBySafeConduct($id);
                            $this->safeConductContainer->deleteSafeConductContainerBySafeConduct($id);

                            $this->handlingContainer->deleteHandlingContainersByHandling($safeConduct['id_handling']);
                            $this->handlingGoods->deleteHandlingGoodsByHandling($safeConduct['id_handling']);

                            if (isset($getTep)) {
                                $tep_containers = $this->transporterEntryPermitContainer->getTepContainersByTep($tep);
                                $tep_checklists = $this->TransporterEntryPermitChecklist->getTepChecklistByTepId($tep);

                                // update safe conduct from tep
                                $this->safeConduct->updateSafeConduct([
                                    'vehicle_type' => $getTep['receiver_vehicle'],
                                    'driver' => $getTep['receiver_name'],
                                    'no_police' => $getTep['receiver_no_police'],
                                    'security_in_date' => $getTep['checked_in_at'],
                                    'security_in_description' => $getTep['checked_in_description'],
                                    'security_out_date' => $getTep['checked_out_at'],
                                    'security_out_description' => $getTep['checked_out_description'],
                                    'updated_at' => date('Y-m-d H:i:s'),
                                    'updated_by' => UserModel::authenticatedUserData('id'),
                                ], $id);

                                // update safe conduct container from tep
                                if (!empty($tep_containers)) {
                                    foreach ($tep_containers as $container) {
                                        $this->safeConductContainer->createSafeConductContainer([
                                            'id_safe_conduct' => $id,
                                            'id_booking_reference' => if_empty($container['id_booking_reference'], null),
                                            'id_booking_container' => $container['id_booking_container'],
                                            'id_container' => $container['id_container'],
                                            'id_position' => if_empty($container['id_position'], null),
                                            'seal' => if_empty($container['seal'], null),
                                            'is_empty' => $container['is_empty'],
                                            'is_hold' => $container['is_hold'],
                                            'status' => $container['status'],
                                            'status_danger' => $container['status_danger'],
                                            'description' => $container['description'],
                                            'quantity' => 1
                                        ]);
                                        $safeConductContainerId = $this->db->insert_id();

                                        if (!empty($handling)) {
                                            $this->handlingContainer->createHandlingContainer([
                                                'id_handling' => $handling['id'],
                                                'id_booking_reference' => if_empty($container['id_booking_reference'], null),
                                                'id_owner' => $handling['id_customer'],
                                                'id_container' => $container['id_container'],
                                                'id_position' => if_empty($container['id_position'], null),
                                                'seal' => if_empty($container['seal'], null),
                                                'quantity' => 1,
                                                'is_empty' => $container['is_empty'],
                                                'is_hold' => $container['is_hold'],
                                                'status' => $container['status'],
                                                'status_danger' => $container['status_danger'],
                                                'description' => $container['description'],
                                                'created_by' => $creatorId
                                            ]);
                                        }

                                        if(!empty($tep_checklists)){
                                            foreach ($tep_checklists as $Checklist) {
                                                if(!empty($Checklist['id_container']) && $Checklist['type'] == "CHECK IN" && $Checklist['id_container'] == $container['id']){
                                                    $tepChecklistDetails = $this->TransporterEntryPermitChecklistDetail->getTepChecklistDetailByChecklistId($Checklist['id']);

                                                    // safe conduct checklist
                                                    $this->safeConductChecklist->create([
                                                        'id_safe_conduct' => $id,
                                                        'id_container' => $safeConductContainerId, //id_safe_conduct_container
                                                        'type' => $Checklist['type'],
                                                        'attachment' => $Checklist['attachment'],
                                                    ]);

                                                    //safe conduct detail
                                                    $safeConductChecklistId = $this->db->insert_id();
                                                    if(!empty($tepChecklistDetails)){
                                                        foreach($tepChecklistDetails as $ChecklistDetail){
                                                            $this->safeConductChecklistDetail->create([
                                                                'id_safe_conduct_checklist' => $safeConductChecklistId,
                                                                'id_checklist' => $ChecklistDetail['id_checklist'],
                                                                'result' => $ChecklistDetail['result'],
                                                                'description' => $ChecklistDetail['description'],
                                                            ]);
                                                        }
                                                    }
                                                }

                                                if(!empty($Checklist['id_container']) && $Checklist['type'] == "CHECK OUT" && $Checklist['id_container'] == $container['id']){
                                                    $tepChecklistDetails = $this->TransporterEntryPermitChecklistDetail->getTepChecklistDetailByChecklistId($Checklist['id']);

                                                    // safe conduct checklist
                                                    $this->safeConductChecklist->create([
                                                        'id_safe_conduct' => $id,
                                                        'id_container' => $safeConductContainerId, //id_safe_conduct_container
                                                        'type' => $Checklist['type'],
                                                        'attachment' => $Checklist['attachment'],
                                                    ]);

                                                    //safe conduct detail
                                                    $safeConductChecklistId = $this->db->insert_id();
                                                    if(!empty($tepChecklistDetails)){
                                                        foreach($tepChecklistDetails as $ChecklistDetail){
                                                            $this->safeConductChecklistDetail->create([
                                                                'id_safe_conduct_checklist' => $safeConductChecklistId,
                                                                'id_checklist' => $ChecklistDetail['id_checklist'],
                                                                'result' => $ChecklistDetail['result'],
                                                                'description' => $ChecklistDetail['description'],
                                                            ]);
                                                        }
                                                    }
                                                }
                                            }
                                        }
                                    }
                                }
                            } else {
                                if (!empty($containers)) {
                                    foreach ($containers as $container) {
                                        $this->safeConductContainer->createSafeConductContainer([
                                            'id_safe_conduct' => $id,
                                            'id_booking_reference' => if_empty($container['id_booking_reference'], null),
                                            'id_booking_container' => $container['id_reference'],
                                            'id_container' => $container['id_container'],
                                            'id_position' => if_empty($container['id_position'], null),
                                            'seal' => if_empty($container['seal'], null),
                                            'is_empty' => $container['is_empty'],
                                            'is_hold' => $container['is_hold'],
                                            'status' => $container['status'],
                                            'status_danger' => $container['status_danger'],
                                            'description' => $container['description'],
                                            'quantity' => 1
                                        ]);

                                        if (!empty($handling)) {
                                            $this->handlingContainer->createHandlingContainer([
                                                'id_handling' => $handling['id'],
                                                'id_booking_reference' => if_empty($container['id_booking_reference'], null),
                                                'id_owner' => $handling['id_customer'],
                                                'id_container' => $container['id_container'],
                                                'id_position' => if_empty($container['id_position'], null),
                                                'seal' => if_empty($container['seal'], null),
                                                'quantity' => 1,
                                                'is_empty' => $container['is_empty'],
                                                'is_hold' => $container['is_hold'],
                                                'status' => $container['status'],
                                                'status_danger' => $container['status_danger'],
                                                'description' => $container['description'],
                                                'created_by' => $creatorId
                                            ]);
                                        }
                                    }
                                }

                                if (!empty($goods)) {
                                    foreach ($goods as $item) {
                                        $this->safeConductGoods->createSafeConductGoods([
                                            'id_safe_conduct' => $id,
                                            'id_booking_reference' => if_empty($item['id_booking_reference'], null),
                                            'id_delivery_order_goods' => null,
                                            'id_booking_goods' => $item['id_reference'],
                                            'id_goods' => $item['id_goods'],
                                            'id_unit' => $item['id_unit'],
                                            'id_position' => if_empty($item['id_position'], null),
                                            'no_pallet' => if_empty($item['no_pallet'], null),
                                            'quantity' => $item['quantity'],
                                            'unit_weight' => $item['unit_weight'],
                                            'unit_gross_weight' => $item['unit_gross_weight'],
                                            'unit_length' => $item['unit_length'],
                                            'unit_width' => $item['unit_width'],
                                            'unit_height' => $item['unit_height'],
                                            'unit_volume' => $item['unit_volume'],
                                            'is_hold' => $item['is_hold'],
                                            'status' => $item['status'],
                                            'status_danger' => $item['status_danger'],
                                            'ex_no_container' => $item['ex_no_container'],
                                            'description' => $item['description']
                                        ]);

                                        $this->handlingGoods->createHandlingGoods([
                                            'id_handling' => $handling['id'],
                                            'id_booking_reference' => if_empty($item['id_booking_reference'], null),
                                            'id_owner' => $handling['id_customer'],
                                            'id_goods' => $item['id_goods'],
                                            'id_unit' => $item['id_unit'],
                                            'id_position' => if_empty($item['id_position'], null),
                                            'no_pallet' => if_empty($item['no_pallet'], null),
                                            'quantity' => $item['quantity'],
                                            'unit_weight' => $item['unit_weight'],
                                            'unit_gross_weight' => $item['unit_gross_weight'],
                                            'unit_length' => $item['unit_length'],
                                            'unit_width' => $item['unit_width'],
                                            'unit_height' => $item['unit_height'],
                                            'unit_volume' => $item['unit_volume'],
                                            'is_hold' => $item['is_hold'],
                                            'status' => $item['status'],
                                            'status_danger' => $item['status_danger'],
                                            'ex_no_container' => if_empty($item['ex_no_container'], null),
                                            'created_by' => $creatorId
                                        ]);
                                    }
                                }
                            }

                        } else {
                            $this->safeConductGoods->deleteSafeConductGoodsBySafeConduct($id);
                            $this->safeConductContainer->deleteSafeConductContainerBySafeConduct($id);

                            $this->handlingContainer->deleteHandlingContainersByHandling($safeConduct['id_handling']);
                            $this->handlingGoods->deleteHandlingGoodsByHandling($safeConduct['id_handling']);

                            // update safe conduct link (not necessary actually)
                            $this->workOrder->updateWorkOrder([
                                'id_safe_conduct' => $id
                            ], $workOrder);

                            // copy result of job to safe conduct
                            $containers = $this->workOrderContainer->getWorkOrderContainersByWorkOrder($workOrder, true);
                            foreach ($containers as $container) {
                                $this->safeConductContainer->createSafeConductContainer([
                                    'id_safe_conduct' => $id,
                                    'id_booking_reference' => if_empty($container['id_booking_reference'], null),
                                    'id_container' => $container['id_container'],
                                    'id_position' => if_empty($container['id_position'], null),
                                    'seal' => $container['seal'],
                                    'is_empty' => $container['is_empty'],
                                    'is_hold' => $container['is_hold'],
                                    'status' => $container['status'],
                                    'status_danger' => $container['status_danger'],
                                    'description' => $container['description'],
                                    'quantity' => $container['quantity']
                                ]);
                                $safeConductContainerId = $this->db->insert_id();
                                $containerGoods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrderContainer($container['id']);
                                foreach ($containerGoods as $item) {
                                    $this->safeConductGoods->createSafeConductGoods([
                                        'id_safe_conduct' => $id,
                                        'id_booking_reference' => if_empty($item['id_booking_reference'], null),
                                        'id_safe_conduct_container' => $safeConductContainerId,
                                        'id_goods' => $item['id_goods'],
                                        'id_unit' => $item['id_unit'],
                                        'id_position' => if_empty($item['id_position'], null),
                                        'no_pallet' => $item['no_pallet'],
                                        'quantity' => $item['quantity'],
                                        'unit_weight' => $item['unit_weight'],
                                        'unit_gross_weight' => $item['unit_gross_weight'],
                                        'unit_length' => $item['unit_length'],
                                        'unit_width' => $item['unit_width'],
                                        'unit_height' => $item['unit_height'],
                                        'unit_volume' => $item['unit_volume'],
                                        'is_hold' => $item['is_hold'],
                                        'status' => $item['status'],
                                        'status_danger' => $item['status_danger'],
                                        'ex_no_container' => $item['ex_no_container'],
                                        'description' => $item['description']
                                    ]);
                                }

                                if (!empty($handling)) {
                                    $this->handlingContainer->createHandlingContainer([
                                        'id_handling' => $handling['id'],
                                        'id_booking_reference' => if_empty($container['id_booking_reference'], null),
                                        'id_owner' => $handling['id_customer'],
                                        'id_container' => $container['id_container'],
                                        'id_position' => if_empty($container['id_position'], null),
                                        'quantity' => 1,
                                        'seal' => $container['seal'],
                                        'is_hold' => $container['is_hold'],
                                        'is_empty' => $container['is_empty'],
                                        'status' => $container['status'],
                                        'status_danger' => $container['status_danger'],
                                        'description' => $container['description'],
                                        'created_by' => UserModel::authenticatedUserData('id')
                                    ]);
                                }
                            }

                            $goods = $this->workOrderGoods->getWorkOrderGoodsByWorkOrder($workOrder, true, true);
                            foreach ($goods as $item) {
                                $this->safeConductGoods->createSafeConductGoods([
                                    'id_safe_conduct' => $id,
                                    'id_booking_reference' => if_empty($item['id_booking_reference'], null),
                                    'id_goods' => $item['id_goods'],
                                    'id_unit' => $item['id_unit'],
                                    'id_position' => if_empty($item['id_position'], null),
                                    'no_pallet' => $item['no_pallet'],
                                    'quantity' => $item['quantity'],
                                    'unit_weight' => $item['unit_weight'],
                                    'unit_gross_weight' => $item['unit_gross_weight'],
                                    'unit_length' => $item['unit_length'],
                                    'unit_width' => $item['unit_width'],
                                    'unit_height' => $item['unit_height'],
                                    'unit_volume' => $item['unit_volume'],
                                    'is_hold' => $item['is_hold'],
                                    'status' => $item['status'],
                                    'status_danger' => $item['status_danger'],
                                    'ex_no_container' => $item['ex_no_container'],
                                    'description' => $item['description']
                                ]);

                                $this->handlingGoods->createHandlingGoods([
                                    'id_handling' => $handling['id'],
                                    'id_booking_reference' => if_empty($item['id_booking_reference'], null),
                                    'id_owner' => $handling['id_customer'],
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
                                    'ex_no_container' => $item['ex_no_container'],
                                    'created_by' => UserModel::authenticatedUserData('id')
                                ]);
                            }
                        }

                        $this->db->trans_complete();

                        if ($this->db->trans_status()) {
                            flash('success', "Safe conduct <strong>{$safeConduct['no_safe_conduct']}</strong> successfully updated");
                            redirect("safe_conduct");
                        } else {
                            flash('danger', "Update safe conduct <strong>{$safeConduct['no_safe_conduct']}</strong> failed, try again or contact administrator");
                        }
                    }
                }
            } else {
                flash('danger', 'Only <strong>POST</strong> request allowed');
            }
            $this->edit($id);
        }

    }

    /**
     * Retry to submit warehouse origin container tracking.
     *
     * @param $id
     */
    public function retry_warehouse_origin_submission($id) {
        $warehouseOriginContainer = $this->warehouseOriginTrackingContainer->getById($id);
        $safeConduct = $this->safeConduct->getSafeConductById($warehouseOriginContainer['id_safe_conduct']);

        if (strpos($safeConduct['source_warehouse'], 'KOJA') !== false) {
            $payloadData = [
                'wsdl' => env('KOJA_WSDL'),
                'service' => 'sendContainer',
                'params' => [
                    'username' => env('KOJA_USERNAME'),
                    'password' => env('KOJA_PASSWORD'),
                    'fstream' => '{"CNTR_ID":"' . $warehouseOriginContainer['no_container'] . '"}'
                ]
            ];
            try {
                $services = $this->soapClientRequest->request($payloadData);
                $result = $services;
                $data = json_decode($services, true);

                $status = WarehouseOriginTrackingContainerModel::STATUS_FAILED;
                if (!is_null($data) && key_exists('DATA', $data)) {
                    // check by status
                    if (key_exists('STATUS', $data['DATA']) && ($data['STATUS'] || $data['STATUS'] != 'false')) {
                        $status = WarehouseOriginTrackingContainerModel::STATUS_SUCCESS;
                    }
                    // when container already inserted, status return false then check key data
                    if (key_exists('CNTR_DETAIL', $data['DATA'])) {
                        $status = WarehouseOriginTrackingContainerModel::STATUS_SUCCESS;
                    }
                }

            } catch (Exception $e) {
                $status = WarehouseOriginTrackingContainerModel::STATUS_FAILED;
                $result = '';
            }

            $update = $this->warehouseOriginTrackingContainer->update([
                'tracking_payload_data' => json_encode($payloadData),
                'tracking_payload_result' => is_string($result) ? $result : json_encode($result),
                'status' => $status,
            ], $id);

            if ($update && $status == WarehouseOriginTrackingContainerModel::STATUS_SUCCESS) {
                flash('success', 'Warehouse origin tracking submission successfully retried', '_back', 'safe-conduct/view-container-submission/' . $warehouseOriginContainer['id_safe_conduct']);
            } else {
                flash('danger', 'Retry submission failed, try again or contact administrator', '_back', 'safe-conduct/view-container-submission/' . $warehouseOriginContainer['id_safe_conduct']);
            }
        }
        redirect('safe-conduct/view-container-submission/' . $warehouseOriginContainer['id_safe_conduct']);
    }

    /**
     * Send container to origin warehouse.
     *
     * @param $safeConduct
     */
    public function submitWarehouseOriginContainer($safeConduct) {
        $tps = $this->people->getById($safeConduct['id_source_warehouse']);
        if (strpos($tps['name'], 'KOJA') !== false) {
            $safeConductContainers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($safeConduct['id']);
            foreach ($safeConductContainers as $safeConductContainer) {
                $payloadData = [
                    'wsdl' => env('KOJA_WSDL'),
                    'service' => 'sendContainer',
                    'params' => [
                        'username' => env('KOJA_USERNAME'),
                        'password' => env('KOJA_PASSWORD'),
                        'fstream' => '{"CNTR_ID":"' . $safeConductContainer['no_container'] . '"}'
                    ]
                ];
                try {
                    $services = $this->soapClientRequest->request($payloadData);
                    $result = $services;
                    $data = json_decode($services, true);

                    $status = WarehouseOriginTrackingContainerModel::STATUS_FAILED;
                    if (!is_null($data) && key_exists('DATA', $data)) {
                        // check by status
                        if (key_exists('STATUS', $data['DATA']) && ($data['STATUS'] || $data['STATUS'] != 'false')) {
                            $status = WarehouseOriginTrackingContainerModel::STATUS_SUCCESS;
                        }
                        // when container already inserted, status return false then check key data
                        if (key_exists('CNTR_DETAIL', $data['DATA'])) {
                            $status = WarehouseOriginTrackingContainerModel::STATUS_SUCCESS;
                        }
                    }
                } catch (Exception $e) {
                    $status = WarehouseOriginTrackingContainerModel::STATUS_FAILED;
                    $result = '';
                }

                $this->warehouseOriginTrackingContainer->create([
                    'id_safe_conduct' => $safeConduct['id'],
                    'no_container' => $safeConductContainer['no_container'],
                    'tracking_payload_data' => json_encode($payloadData),
                    'tracking_payload_result' => is_string($result) ? $result : json_encode($result),
                    'status' => $status,
                ]);
            }
        }
    }

    /**
     * Set tracking route in safe conduct.
     *
     * @param $safeConduct
     * @param $fromDate
     * @param $toDate
     */
    public function setTrackingRoute($safeConduct, $fromDate, $toDate)
    {
        if (!empty($safeConduct['id_eseal'])) {
            // release eseal
            $this->eseal->update([
                'is_used' => 0,
                'used_in' => null
            ], $safeConduct['id_eseal']);

            // get tracking data
            $eseal = $this->eseal->getById($safeConduct['id_eseal']);

            // find out if from to date is range date (not in same day)
            $totalFetchedDay = 0;
            $diffDate = difference_date(format_date($fromDate), format_date($toDate));
            if ($diffDate > 0 && $diffDate <= 2) { // maximum 2 days
                $totalFetchedDay = $diffDate;
            } else if ($diffDate > 2) {
                $totalFetchedDay = 2;
            }

            $routes = [];
            if ($totalFetchedDay == 0) {
                $routeFilters = [
                    'distinct' => 1,
                    'lock_status' => 1,
                    'from_time' => format_date($fromDate, 'H:i:s'),
                    'to_time' => format_date($toDate, 'H:i:s'),
                ];
                $routes = $this->eseal->getDeviceTrackingData($eseal['id_device'], format_date($fromDate), $routeFilters);
            } else {
                // Given input 2019-12-15 05:27:38 to 2019-12-17 08:27:38 then loop should result values below
                // 2019-12-15  05:27:38 - 23:59:59
                // 2019-12-16  00:00:00 - 23:59:59
                // 2019-12-17  00:00:00 - 08:27:38
                for ($i = 0; $i <= $totalFetchedDay; $i++) {
                    $date = date('Y-m-d', strtotime('+' . $i . ' day', strtotime($fromDate)));
                    $fromTime = format_date($fromDate, 'H:i:s');
                    $toTime = format_date($toDate, 'H:i:s');
                    if ($i == 0) {
                        $toTime = '23:59:59';
                    } else if ($i > 0 && $i < $totalFetchedDay) {
                        $fromTime = '00:00:00';
                        $toTime = '23:59:59';
                    } else {
                        $fromTime = '00:00:00';
                    }
                    $routeFilters = [
                        'distinct' => 1,
                        'lock_status' => 1,
                        'from_time' => $fromTime,
                        'to_time' => $toTime,
                    ];
                    $fetchedRoute = $this->eseal->getDeviceTrackingData($eseal['id_device'], $date, $routeFilters);
                    if (empty($routes)) {
                        $routes = $fetchedRoute;
                    } else {
                        $routes['tracking_distance'] = $routes['tracking_distance'] + $fetchedRoute['tracking_distance'];
                        $routes['tracking'] = array_merge($routes['tracking'], $fetchedRoute['tracking']);
                    }
                }
            }

            if (!empty($routes) && !empty($routes['tracking'])) {
                foreach ($routes['tracking'] as $route) {
                    $this->safeConductRoute->create([
                        'id_safe_conduct' => $safeConduct['id'],
                        'longitude' => $route['position']['longitude'],
                        'latitude' => $route['position']['latitude'],
                        'distance' => $route['distance'],
                        'distance_unit' => $route['distance_unit'],
                        'address' => $route['address'] ?? '',
                        'time' => $route['time'],
                        'data' => json_encode([
                            'lockStatus' => $route['variables']['lockStatus'],
                            'speed' => get_if_exist($route['variables'], 'speed', $route['velocity']['groundSpeed']),
                            'vehicleNumber' => $route['variables']['vehicleNumber'] ?? '',
                            'driverName' => $route['variables']['driverName'] ?? '',
                            'ajuNumber' => $route['variables']['ajuNumber'] ?? '',
                            'doNumber' => $route['variables']['doNumber'] ?? '',
                            'containerNumber' => $route['variables']['containerNumber'] ?? '',
                            'pickUpFrom' => $route['variables']['pickUpFrom'] ?? '',
                            'destination' => $route['variables']['destination'] ?? '',
                        ])
                    ]);
                }

                // find out if start and stop in right position
                $sourceWarehouse = $this->people->getById($safeConduct['id_source_warehouse']);
                $branchWarehouse = $this->branch->getById($safeConduct['id_branch']);
                $sourceWarehouseAddress = get_if_exist($sourceWarehouse, 'address', '');
                $branchAddress = get_if_exist($branchWarehouse, 'address', '');

                $populateFirstTracking = array_slice(if_empty($routes['tracking'], []), 0, 7);
                $populateLastTracking = array_slice(if_empty($routes['tracking'], []), -7);

                // set normal if first 5 data in same address with source warehouse
                $startNormal = false;
                if (!empty($populateFirstTracking)) {
                    foreach ($populateFirstTracking as $trackingAddress) {
                        if (empty($trackingAddress['address'])) continue;
                        similar_text($trackingAddress['address'], $sourceWarehouseAddress, $percent);
                        if ($percent >= 40) {
                            $startNormal = true;
                            break;
                        }
                    }
                }

                // set normal if last 5 data in same address with branch's warehouse
                $stopNormal = false;
                if (!empty($populateLastTracking)) {
                    foreach ($populateLastTracking as $trackingAddress) {
                        if (empty($trackingAddress['address'])) continue;
                        similar_text($trackingAddress['address'], $branchAddress, $percent);
                        if ($percent >= 40) {
                            $stopNormal = true;
                            break;
                        }
                    }
                }
                if (count($routes['tracking']) <= 3) {
                    $statusTracking = SafeConductModel::TRACKING_SUSPECTED;
                } else if ($startNormal && !$stopNormal) {
                    $statusTracking = SafeConductModel::TRACKING_START_ONLY;
                } else if (!$startNormal && $stopNormal) {
                    $statusTracking = SafeConductModel::TRACKING_STOP_ONLY;
                } else if ($startNormal && $stopNormal) {
                    $statusTracking = SafeConductModel::TRACKING_NORMAL;
                } else {
                    $statusTracking = SafeConductModel::TRACKING_SUSPECTED;
                }
            } else {
                $statusTracking = SafeConductModel::TRACKING_EMPTY_ROUTE;
            }
        } else {
            $statusTracking = SafeConductModel::TRACKING_NO_ESEAL;
        }
        $this->safeConduct->updateSafeConduct([
            'status_tracking' => $statusTracking,
        ], $safeConduct['id']);
    }

    /**
     * Perform deleting safe conduct data.
     */
    public function delete()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_IN_DELETE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Safe conduct data', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $safeConductId = $this->input->post('id');
                $safeConductData = $this->safeConduct->getSafeConductById($safeConductId);
                $workOrders = $this->workOrder->getWorkOrdersBySafeConduct($safeConductId);

                $handlingTypeIdOutbound = get_setting('default_outbound_handling');
                $handlingTypeIdMovingOut = get_setting('default_moving_out_handling');
                $handlingEmptyContainer = 11;

                $this->db->trans_start();

                $this->safeConduct->deleteSafeConduct($safeConductId);

                $this->handling->deleteHandling($safeConductData['id_handling']);

                foreach ($workOrders as $workOrder) {
                    if (in_array($workOrder['id_handling_type'], [$handlingTypeIdOutbound, $handlingTypeIdMovingOut, $handlingEmptyContainer])) {
                        $this->workOrder->updateWorkOrder([
                            'id_safe_conduct' => null
                        ], $workOrder['id']);
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('warning', "Safe conduct <strong>{$safeConductData['no_safe_conduct']}</strong> successfully deleted");
                } else {
                    flash('danger', "Delete safe conduct <strong>{$safeConductData['no_safe_conduct']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('safe_conduct');
    }

    /**
     * Show print layout safe conduct.
     */
    public function print_safe_conduct()
    {
        $redirect = $this->input->get('redirect');
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Safe conduct data', 'trim|required|max_length[50]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $id = $this->input->post('id');
                $safeConduct = $this->safeConduct->getSafeConductById($id);

                $this->check_print_group($safeConduct);

                if ($safeConduct['print_total'] < $safeConduct['print_max']) {
                    $update = $this->safeConduct->updateSafeConduct([
                        'print_total' => intval($safeConduct['print_total']) + 1
                    ], $id);

                    if ($update) {
                        $barcode = new DNS2D();
                        $barcode->setStorPath(APPPATH . "/cache/");

                        // find out if safe conduct is groups
                        if (!empty($safeConduct['no_safe_conduct_group'])) {
                            $this->print_group($safeConduct);
                        } else {
                            $safeConductContainers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($id);
                            foreach ($safeConductContainers as &$container) {
                                $containerGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConductContainer($container['id']);
                                $container['goods'] = $containerGoods;
                            }
                            $safeConductGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($id, true);
                            $safeConductBarcode = $barcode->getBarcodePNG($safeConduct['no_safe_conduct'], "QRCODE", 4, 4);
                            $data = [
                                'title' => 'Print Safe Conduct',
                                'page' => 'safe_conduct/print_safe_conduct',
                                'safeConduct' => $safeConduct,
                                'safeConductContainers' => $safeConductContainers,
                                'safeConductGoods' => $safeConductGoods,
                                'barcodeSafeConduct' => $safeConductBarcode,
                            ];
                            $this->load->view('template/print', $data);
                        }
                        return;
                    } else {
                        flash('danger', "Print <strong>{$safeConduct['no_safe_conduct']}</strong> failed, try again or contact administrator");
                    }
                } else {
                    flash('danger', "You've reached total print of safe conduct <strong>{$safeConduct['no_safe_conduct']}</strong>, please contact administrator to grant permission");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed, please wait until the page is loaded');
        }
        if ($redirect == '') {
            redirect('safe_conduct');
        } else {
            redirect($redirect, false);
        }
    }

    /**
     * Show print layout safe conduct.
     * @param $id
     */
    public function print_safe_conduct_mode2($id)
    {
        $safeConduct = $this->safeConduct->getSafeConductById($id);
        $safeConductContainers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($id);
        foreach ($safeConductContainers as &$container) {
            $containerGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConductContainer($container['id']);
            $container['goods'] = $containerGoods;
        }
        $safeConductGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($id, true);

        $barcode = new DNS2D();
        $barcode->setStorPath(APPPATH . "/cache/");

        $this->check_print_group($safeConduct);
        if (!empty($safeConduct['no_safe_conduct_group'])) {
            $this->print_group($safeConduct);
        } else {
            $safeConductBarcode = $barcode->getBarcodePNG($safeConduct['no_safe_conduct'], "QRCODE", 4, 4);
            $data = [
                'title' => 'Print Safe Conduct',
                'page' => 'safe_conduct/print_safe_conduct_mode2',
                'safeConduct' => $safeConduct,
                'safeConductContainers' => $safeConductContainers,
                'safeConductGoods' => $safeConductGoods,
                'barcodeSafeConduct' => $safeConductBarcode,
            ];
            $this->load->view('template/print', $data);
        }
    }

    /**
     * Check if safe conduct is ready to be printed.
     *
     * @param $safeConduct
     */
    private function check_print_group($safeConduct)
    {
        if (!empty($safeConduct['id_transporter_entry_permit'])) {
            $workOrders = $this->workOrder->getBy([
                'work_orders.id_transporter_entry_permit' => $safeConduct['id_transporter_entry_permit']
            ]);
            $outstandingJob = null;
            foreach ($workOrders as $workOrder) {
                if (empty($workOrder['id_safe_conduct'])) {
                    $outstandingJob = $workOrder;
                    break;
                }
            }

            if (!empty($outstandingJob)) {
                flash('danger', "Job {$outstandingJob['no_work_order']} with same entry permit is not completed and without safe conduct", '_back', 'safe-conduct');
            }
        }
    }

    /**
     * Print group safe conduct.
     *
     * @param $safeConduct
     */
    private function print_group($safeConduct)
    {
        $barcode = new DNS2D();
        $barcode->setStorPath(APPPATH . "/cache/");

        // find out if safe conduct is groups
        $safeConductContainers = [];
        $safeConductGoods = [];

        $safeConductGroups = $this->safeConduct->getBy([
            'safe_conducts.id_safe_conduct_group' => $safeConduct['id_safe_conduct_group']
        ]);
        foreach ($safeConductGroups as &$safeConductGroupItem) {
            $safeConductGroupItem['containers'] = $this->safeConductContainer->getSafeConductContainersBySafeConduct($safeConductGroupItem['id']);
            $safeConductContainers = array_merge($safeConductContainers, $safeConductGroupItem['containers']);
            foreach ($safeConductGroupItem['containers'] as &$container) {
                $container['goods'] = $this->safeConductGoods->getSafeConductGoodsBySafeConductContainer($container['id']);
            }
            $safeConductGroupItem['goods'] = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($safeConductGroupItem['id'], true);
            $safeConductGoods = array_merge($safeConductGoods, $safeConductGroupItem['goods']);
        }
        $safeConductBarcode = $barcode->getBarcodePNG($safeConduct['no_safe_conduct_group'], "QRCODE", 4, 4);
        $data = [
            'title' => 'Print Safe Conduct',
            'page' => 'safe_conduct/print_safe_conduct_group_mode2',
            'safeConduct' => $safeConduct,
            'safeConductGroups' => $safeConductGroups,
            'safeConductContainers' => $safeConductContainers,
            'safeConductGoods' => $safeConductGoods,
            'barcodeSafeConduct' => $safeConductBarcode,
        ];

        $dompdf = new \Dompdf\Dompdf(['isHtml5ParserEnabled' => true, 'isPhpEnabled' => true]);
        $dompdf->loadHtml($this->load->view('safe_conduct/print_safe_conduct_group', $data, true));
        $dompdf->setPaper('A4', 'portrait');
        $dompdf->render();
        $dompdf->stream("safe-conduct-group.pdf", array("Attachment" => false));
        //$this->load->view('template/print', $data);
    }

    /**
     * Update safe conduct total print max.
     */
    public function update_print_max()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Safe conduct data', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $safeConductId = $this->input->post('id');
                $maxPrint = $this->input->post('print_max');

                $safeConduct = $this->safeConduct->getSafeConductById($safeConductId);
                $updatePrintMax = $this->safeConduct->updateSafeConduct([
                    'print_max' => $maxPrint
                ], $safeConductId);

                if ($updatePrintMax) {
                    flash('success', "Total print of safe conduct <strong>{$safeConduct['no_safe_conduct']}</strong> successfully updated to <strong>{$maxPrint}</strong> x print");
                } else {
                    flash('danger', "Update total print max safe conduct <strong>{$safeConduct['no_work_order']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('safe_conduct');
    }


    /**
     * Check in safe conduct document.
     */
    public function check_in()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SECURITY_CHECK_IN);

        $redirect = $this->input->get('redirect');
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Safe conduct data', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('no_police', 'Police plat number', 'trim|required|max_length[20]');
            $this->form_validation->set_rules('driver', 'Driver name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('expedition', 'Expedition', 'trim|required|max_length[100]');
            $this->form_validation->set_rules('description', 'Description', 'trim|max_length[500]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $safeConductId = $this->input->post('id');
                $noPolice = $this->input->post('no_police');
                $driver = $this->input->post('driver');
                $expedition = $this->input->post('expedition');
                $description = $this->input->post('description');

                $this->db->trans_start();

                // get safe conduct data
                $safeConduct = $this->safeConduct->getSafeConductById($safeConductId);

                // check in
                $checkedInAt = date('Y-m-d H:i:s');
                $this->safeConduct->updateSafeConduct([
                    'no_police' => $noPolice,
                    'driver' => $driver,
                    'expedition' => $expedition,
                    'security_in_date' => $checkedInAt,
                    'security_in_description' => $description,
                    'updated_by' => UserModel::authenticatedUserData('id')
                ], $safeConductId);

                // TODO: update safe conduct group
                // update data related (safe conduct group) same as current checked in safe conduct
                $allSafeConducts = [$safeConduct];
                if (!empty($safeConduct['id_safe_conduct_group'])) {
                    $relatedSafeConducts = $this->safeConduct->getBy([
                        'safe_conducts.id!=' => $safeConductId,
                        'safe_conducts.id_safe_conduct_group' => $safeConduct['id_safe_conduct_group']
                    ]);
                    foreach ($relatedSafeConducts as $relatedSafeConduct) {
                        $this->safeConduct->updateSafeConduct([
                            'no_police' => $noPolice,
                            'driver' => $driver,
                            'expedition' => $expedition,
                            'security_in_date' => $checkedInAt,
                            'security_in_description' => $description,
                            'updated_by' => UserModel::authenticatedUserData('id')
                        ], $relatedSafeConduct['id']);
                    }
                    $allSafeConducts = array_merge($allSafeConducts, $relatedSafeConducts);
                }

                // create job moving and detail
                foreach ($allSafeConducts as $safeConduct) {
                    $handling = $this->handling->getHandlingById($safeConduct['id_handling']);
                    $handlingContainers = $this->handlingContainer->getHandlingContainersByHandling($safeConduct['id_handling']);
                    $handlingGoods = $this->handlingGoods->getHandlingGoodsByHandling($safeConduct['id_handling'], true);

                    $mode = '';
                    if (!empty($handlingContainers)) {
                        $mode = 'C';
                    }
                    if (!empty($handlingGoods)) {
                        $mode = 'G';
                    }

                    if (!empty($handlingContainers) && !empty($handlingGoods)) {
                        $mode = 'CG';
                    }

                    $noWorkOrder = $this->workOrder->getAutoNumberWorkOrder($safeConduct['handling_code']);
                    $this->workOrder->createWorkOrder([
                        'no_work_order' => $noWorkOrder,
                        'queue' => 0,
                        'id_handling' => $safeConduct['id_handling'],
                        'id_safe_conduct' => $safeConduct['id'],
                        'status' => 'COMPLETED',
                        'taken_by' => UserModel::authenticatedUserData('id'),
                        'taken_at' => date('Y-m-d H:i:s'),
                        'completed_at' => date('Y-m-d H:i:s'),
                        'gate_in_date' => date('Y-m-d H:i:s'),
                        'gate_out_date' => date('Y-m-d H:i:s'),
                        'description' => $description,
                        'mode' => $mode,
                        'created_by' => UserModel::authenticatedUserData('id')
                    ]);
                    $workOrderId = $this->db->insert_id();

                    $components = $this->component->getByHandlingType($handling['id_handling_type']);
                    foreach ($components as $component) {
                        $this->workOrderComponent->createWorkOrderComponent([
                            'id_work_order' => $workOrderId,
                            'id_component' => $component['id'],
                            'quantity' => $component['default_value'],
                            'description' => $component['component_description'],
                            'status' => 'APPROVED'
                        ]);
                    }

                    foreach ($handlingContainers as $handlingContainer) {
                        $this->workOrderContainer->insertWorkOrderContainer([
                            'id_work_order' => $workOrderId,
                            'id_owner' => $handlingContainer['id_owner'],
                            'id_container' => $handlingContainer['id_container'],
                            'id_position' => if_empty($handlingContainer['id_position'], null),
                            'description' => $handlingContainer['description'],
                            'seal' => $handlingContainer['seal'],
                            'is_empty' => $handlingContainer['is_empty'],
                            'status' => $handlingContainer['status'],
                            'quantity' => $handlingContainer['quantity'],
                            'created_by' => UserModel::authenticatedUserData('id')
                        ]);
                    }

                    foreach ($handlingGoods as $handlingItem) {
                        $this->workOrderGoods->insertWorkOrderGoods([
                            'id_work_order' => $workOrderId,
                            'id_owner' => $handlingItem['id_owner'],
                            'id_goods' => $handlingItem['id_goods'],
                            'id_unit' => $handlingItem['id_unit'],
                            'id_position' => if_empty($handlingItem['id_position'], null),
                            'quantity' => $handlingItem['quantity'],
                            'unit_weight' => $handlingItem['unit_weight'],
                            'unit_gross_weight' => $handlingItem['unit_gross_weight'],
                            'unit_length' => $handlingItem['unit_length'],
                            'unit_width' => $handlingItem['unit_width'],
                            'unit_height' => $handlingItem['unit_height'],
                            'unit_volume' => $handlingItem['unit_volume'],
                            'ex_no_container' => $handlingItem['ex_no_container'],
                            'status' => $handlingItem['status'],
                            'no_pallet' => $handlingItem['no_pallet'],
                            'created_by' => UserModel::authenticatedUserData('id')
                        ]);
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Safe conduct <strong>{$safeConduct['no_safe_conduct']}</strong> successfully checked in");
                } else {
                    flash('danger', "Check in safe conduct <strong>{$safeConduct['no_safe_conduct']}</strong> failed, try again or contact administrator");
                }

                redirect('security');
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed, please wait until page loaded');
        }
         redirect('security');
    }

    /**
     * Check out safe conduct sheet.
     */
    public function check_out()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SECURITY_CHECK_OUT);

        $redirect = $this->input->get('redirect');
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Safe conduct data', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('description', 'Description', 'trim|max_length[500]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $safeConductId = $this->input->post('id');
                $description = $this->input->post('description');

                $safeConduct = $this->safeConduct->getSafeConductById($safeConductId);
                $safeConductChecklists = $this->safeConductChecklist->getSafeConductChecklistOutBySafeConductId($safeConductId);
                $safeConductContainers = $this->safeConductContainer->getSafeConductContainersBySafeConduct($safeConductId);
                $safeConductGoods = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($safeConductId, true);
                //non base query
                $getTep = $this->transporterEntryPermit->getByIdNonBase($safeConduct['id_transporter_entry_permit']);
                $tepChecklist = $this->TransporterEntryPermitChecklist->getTepChecklistByTepId($safeConduct['id_transporter_entry_permit']);

                $this->db->trans_start();

                // upload photo if exist
                $fileName = '';
                $uploadPassed = true;
                if (!empty($_FILES['photo']['name'])) {
                    $fileName = 'SF_PHOTO_' . time() . '_' . rand(100, 999);
                    $saveTo = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'safe_conducts_photo';
                    if ($this->documentType->makeFolder('safe_conducts_photo')) {
                        $upload = $this->uploadDocumentFile->uploadTo('photo', $fileName, $saveTo);
                        if (!$upload['status']) {
                            $uploadPassed = false;
                            flash('warning', $upload['errors']);
                        } else {
                            $fileNames = array_column($upload['data'], 'file_name');
                            $fileName = implode('/', $fileNames);
                        }
                    } else {
                        $uploadPassed = false;
                        flash('warning', 'Making folder upload failed, try again');
                    }
                }

                if ($uploadPassed) {
                    // checking out safe conduct
                    $checkedOutAt = date('Y-m-d H:i:s');
                    $this->safeConduct->updateSafeConduct([
                        'photo' => $fileName,
                        'security_out_date' => !empty($getTep['checked_out_at']) == true && $getTep['tep_category'] == "INBOUND" ?  $getTep['checked_out_at'] : $checkedOutAt,
                        'security_out_description' => $description,
                        'updated_by' => UserModel::authenticatedUserData('id')
                    ], $safeConductId);

                    // update checkout chassis if exist
                    if ($safeConduct['type'] == 'OUTBOUND') {
                        $workOrder = $this->workOrder->getBy([
                            'work_orders.id_safe_conduct' => $safeConductId,
                            'work_orders.id_tep_chassis IS NOT NULL' => null
                        ], true);
                        if (!empty($workOrder['id_tep_chassis'])) {
                            $this->transporterEntryPermitChassis->update([
                                'id_tep_out' => $getTep['id'] ?? null,
                                'checked_out_at' => $checkedOutAt,
                                'checked_out_description' => 'Checked out from ' . (empty($getTep['tep_code']) ? $safeConduct['no_safe_conduct'] : $getTep['tep_code'])
                            ], $workOrder['id_tep_chassis']);
                        }
                    }

                    // set tracking data
                    if($safeConduct['expedition_type'] == 'INTERNAL') {
                        $safeConduct = $this->safeConduct->getSafeConductById($safeConductId);
                        $this->setTrackingRoute($safeConduct, $safeConduct['security_in_date'], $safeConduct['security_out_date']);

                        // push container to TPS services
                        $this->submitWarehouseOriginContainer($safeConduct);
                    }

                    // TODO: update safe conduct group
                    // update data related (safe conduct group) same as current checked out safe conduct
                    if (!empty($safeConduct['id_safe_conduct_group'])) {
                        $relatedSafeConducts = $this->safeConduct->getBy([
                            'safe_conducts.id!=' => $safeConductId,
                            'safe_conducts.id_safe_conduct_group' => $safeConduct['id_safe_conduct_group']
                        ]);
                        foreach ($relatedSafeConducts as $relatedSafeConduct) {
                            $this->safeConduct->updateSafeConduct([
                                'photo' => $fileName,
                                'security_out_date' => !empty($getTep['checked_out_at']) == true && $getTep['tep_category'] == "INBOUND" ?  $getTep['checked_out_at'] : $checkedOutAt,
                                'security_out_description' => $description,
                                'updated_by' => UserModel::authenticatedUserData('id')
                            ], $relatedSafeConduct['id']);

                            // set tracking data related safe conduct
                            if($safeConduct['expedition_type'] == 'INTERNAL') {
                                $safeConductRelated = $this->safeConduct->getSafeConductById($safeConductId);
                                $this->setTrackingRoute($safeConductRelated, $safeConductRelated['security_in_date'], $safeConductRelated['security_out_date']);

                                // push container to TPS services
                                $this->submitWarehouseOriginContainer($safeConduct);
                            }
                        }
                    }

                    if(!empty($getTep)){
                        $safeConductByTeps = $this->safeConduct->getSafeConductsByTepId($getTep['id']);
                        if(!empty($safeConductByTeps)){
                            foreach($safeConductByTeps as $safe_conduct_by_tep){
                                $this->safeConduct->updateSafeConduct([
                                    'photo' => $fileName,
                                    'security_out_date' => !empty($getTep['checked_out_at']) == true && $getTep['tep_category'] == "INBOUND" ?  $getTep['checked_out_at'] : date('Y-m-d H:i:s'),
                                    'security_out_description' => $description,
                                    'updated_by' => UserModel::authenticatedUserData('id'),
                                ], $safe_conduct_by_tep['id']);
                            }
                        }

                        //checking out TEP
                        $safeConductAfterUpdate = $this->safeConduct->getSafeConductById($safeConductId);
                        $this->transporterEntryPermit->update([
                            'checked_out_description' => $description,
                            'checked_out_at' => $safeConductAfterUpdate['security_out_date'],
                            'checked_out_by' => UserModel::authenticatedUserData('id')
                        ], $getTep['id']);
                    }

                    // create auto invoice handling
                    $autoHandlingInvoice = get_setting('invoice_handling_auto');
                    if ($autoHandlingInvoice && !empty($safeConduct['id_handling'])) {
                        $handling = $this->handling->getHandlingById($safeConduct['id_handling']);
                        $invoiceDetailData = $this->invoiceHandling->getInvoiceHandlingPrices($handling);

                        $itemSummary = '';
                        $handlingContainers = $this->handlingContainer->getHandlingContainersByHandling($safeConduct['id_handling']);
                        $handlingGoods = $this->handlingGoods->getHandlingGoodsByHandling($safeConduct['id_handling'], true);

                        foreach ($handlingContainers as $container) {
                            if (!empty($itemSummary)) {
                                $itemSummary .= ', ';
                            }
                            $itemSummary .= $container['no_container'] . ' (' . $container['type'] . '-' . $container['size'] . ')';
                        }
                        foreach ($handlingGoods as $good) {
                            if (!empty($itemSummary)) {
                                $itemSummary .= ', ';
                            }
                            $itemSummary .= $good['goods_name'] . ' (' . $good['total_weight'] . 'Kg-' . $good['total_volume'] . 'M3)';
                        }

                        $noInvoice = $this->invoice->getAutoNumberInvoice();
                        $this->invoice->createInvoice([
                            'no_invoice' => $noInvoice,
                            'no_reference' => $handling['no_handling'],
                            'id_branch' => $handling['id_branch'],
                            'id_customer' => $handling['id_customer'],
                            'type' => 'HANDLING',
                            'invoice_date' => date('Y-m-d H:i:s'),
                            'item_summary' => $itemSummary,
                            'description' => 'Auto create invoice',
                            'status' => 'DRAFT',
                            'created_by' => UserModel::authenticatedUserData('id')
                        ]);
                        $invoiceId = $this->db->insert_id();

                        if (!empty($invoiceDetailData)) {
                            foreach ($invoiceDetailData as &$invoiceDetail) {
                                $invoiceDetail['id_invoice'] = $invoiceId;
                                $invoiceDetail['created_by'] = UserModel::authenticatedUserData('id');
                                if (key_exists('total', $invoiceDetail)) {
                                    unset($invoiceDetail['total']);
                                }
                            }
                            $this->invoiceDetail->createInvoiceDetail($invoiceDetailData);
                        } else {
                            //show_error('Detail invoice is missing, contact your administrator');
                        }
                    }

                    if(!empty($getTep)){
                         //Update Safe Conduct Checklist From TEP
                        // if(!empty($safeConductContainers) && !empty($tepChecklist)){
                        //     foreach($safeConductContainers as $container){
                        //         foreach($tepChecklist as $checklist){
                        //             if(!empty($checklist['id_container']) && $checklist['type'] == "CHECK OUT" && $checklist['id_container'] == $container['id']){
                        //                 $tepChecklistDetails = $this->TransporterEntryPermitChecklistDetail->getTepChecklistDetailByChecklistId($checklist['id']);

                        //                 // safe conduct checklist
                        //                 $this->safeConductChecklist->create([
                        //                     'id_safe_conduct' => $safeConductId,
                        //                     'id_container' => $container['id'], //id_safe_conduct_container
                        //                     'type' => $checklist['type'],
                        //                     'attachment' => $checklist['attachment'],
                        //                 ]);

                        //                 //safe conduct detail
                        //                 $safeConductChecklistId = $this->db->insert_id();
                        //                 if(!empty($tepChecklistDetails)){
                        //                     foreach($tepChecklistDetails as $ChecklistDetail){
                        //                         $this->safeConductChecklistDetail->create([
                        //                             'id_safe_conduct_checklist' => $safeConductChecklistId,
                        //                             'id_checklist' => $ChecklistDetail['id_checklist'],
                        //                             'result' => $ChecklistDetail['result'],
                        //                             'description' => $ChecklistDetail['description'],
                        //                         ]);
                        //                     }
                        //                 }
                        //             }
                        //         }
                        //     }
                        // }

                        // //Update Safe Conduct Checklist From TEP
                        // if(!empty($tepChecklist)){
                        //     foreach($tepChecklist as $checklist){
                        //         if(empty($checklist['id_container']) && $checklist['type'] == "CHECK OUT"){
                        //             $tepChecklistDetails = $this->TransporterEntryPermitChecklistDetail->getTepChecklistDetailByChecklistId($checklist['id']);

                        //             // safe conduct checklist
                        //             $this->safeConductChecklist->create([
                        //                 'id_safe_conduct' => $safeConductId,
                        //                 'id_container' => null, //id_safe_conduct_container
                        //                 'type' => $checklist['type'],
                        //                 'attachment' => $checklist['attachment'],
                        //             ]);

                        //             //safe conduct detail
                        //             $safeConductChecklistId = $this->db->insert_id();
                        //             if(!empty($tepChecklistDetails)){
                        //                 foreach($tepChecklistDetails as $ChecklistDetail){
                        //                     $this->safeConductChecklistDetail->create([
                        //                         'id_safe_conduct_checklist' => $safeConductChecklistId,
                        //                         'id_checklist' => $ChecklistDetail['id_checklist'],
                        //                         'result' => $ChecklistDetail['result'],
                        //                         'description' => $ChecklistDetail['description'],
                        //                     ]);
                        //                 }
                        //             }
                        //         }
                        //     }
                        // }

                        // if(!empty($safeConductContainers)){
                        //     foreach($safeConductContainers as $container){
                        //         $this->transporterEntryPermitContainer->createTepContainer([
                        //             'id_tep' => $getTep['id'],
                        //             'id_booking_container' => $container['id_booking_container'],
                        //             'id_container' => $container['id_container'],
                        //             'id_position' => if_empty($container['id_position'], null),
                        //             'seal' => if_empty($container['seal'], null),
                        //             'is_empty' => $container['is_empty'],
                        //             'is_hold' => $container['is_hold'],
                        //             'status' => $container['status'],
                        //             'status_danger' => $container['status_danger'],
                        //             'description' => $container['description'],
                        //             'quantity' => $container['quantity'],
                        //         ]);

                        //         $tepContainerId = $this->db->insert_id();

                        //         if(!empty($safeConductChecklists)){
                        //             foreach ($safeConductChecklists as $Checklist) {
                        //                 if(!empty($Checklist['id_container']) && $Checklist['type'] == "CHECK OUT" && $Checklist['id_container'] == $container['id']){
                        //                     $safeConductChecklistDetails = $this->safeConductChecklistDetail->getChecklistDetailByChecklistId($Checklist['id']);

                        //                     // tep checklist
                        //                     $this->TransporterEntryPermitChecklist->create([
                        //                         'id_tep' => $getTep['id'],
                        //                         'id_container' => $tepContainerId,
                        //                         'type' => $Checklist['type'],
                        //                         'attachment' => $Checklist['attachment'],
                        //                     ]);

                        //                     //tep checklist detail
                        //                     $tepChecklistId = $this->db->insert_id();
                        //                     if(!empty($safeConductChecklistDetails)){
                        //                         foreach($safeConductChecklistDetails as $ChecklistDetail){
                        //                             $this->TransporterEntryPermitChecklistDetail->create([
                        //                                 'id_tep_checklist' => $tepChecklistId,
                        //                                 'id_checklist' => $ChecklistDetail['id_checklist'],
                        //                                 'result' => $ChecklistDetail['result'],
                        //                                 'description' => $ChecklistDetail['description'],
                        //                             ]);
                        //                         }
                        //                     }
                        //                 }
                        //             }
                        //         }
                        //     }
                        // }

                        //for inbound external (check in tep, checkout safe conduct -> update tep)
                        if(!empty($safeConductChecklists)){
                            foreach ($safeConductChecklists as $Checklist) {
                                if(empty($Checklist['id_container']) && $Checklist['type'] == "CHECK OUT"){
                                    $safeConductChecklistDetails = $this->safeConductChecklistDetail->getChecklistDetailByChecklistId($Checklist['id']);

                                    // tep checklist
                                    $this->TransporterEntryPermitChecklist->create([
                                        'id_tep' => $getTep['id'],
                                        'id_container' => null,
                                        'type' => $Checklist['type'],
                                        'attachment' => $Checklist['attachment'],
                                    ]);

                                    //tep checklist detail
                                    $tepChecklistId = $this->db->insert_id();
                                    if(!empty($safeConductChecklistDetails)){
                                        foreach($safeConductChecklistDetails as $ChecklistDetail){
                                            $this->TransporterEntryPermitChecklistDetail->create([
                                                'id_tep_checklist' => $tepChecklistId,
                                                'id_checklist' => $ChecklistDetail['id_checklist'],
                                                'result' => $ChecklistDetail['result'],
                                                'description' => $ChecklistDetail['description'],
                                            ]);
                                        }
                                    }
                                }
                            }
                        }
                    }

                    $this->db->trans_complete();

                    if ($this->db->trans_status()) {
                        flash('warning', "Safe conduct <strong>{$safeConduct['no_safe_conduct']}</strong> successfully checked out");
                    } else {
                        flash('danger', "Check out safe conduct <strong>{$safeConduct['no_safe_conduct']}</strong> failed, try again or contact administrator");
                    }
                     redirect('security');
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed, please wait until page loaded');
        }
         redirect('security');
    }

    /**
     * Get safe conduct by bookings.
     */
    public function ajax_get_safe_conducts_by_booking()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $bookingId = $this->input->get('id_booking');
            $safeConducts = $this->safeConduct->getSafeConductsByBooking($bookingId);
            if (isset($_SERVER['HTTP_ACCEPT']) && preg_match('/text\/html/', $_SERVER['HTTP_ACCEPT'])) {
                if (!empty($safeConducts)) {
                    echo $this->load->view('safe_conduct/_booking_safe_conduct', ['safeConducts' => $safeConducts], true);
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode($safeConducts);
            }
        }
    }

        /**
     * Get safe conduct by bookings.
     */
    public function ajax_get_containers_by_input()
    {

            $data_all = $this->input->post();
            header('Content-Type: application/json');
            echo json_encode($data_all);
    }

    /**
     * Get available booking to make safe conduct.
     */
    public function ajax_get_available_booking()
    {
        $bookingCategory = $this->input->get('category');
        $bookingStatus = $this->input->get('status');

        $conditions = [
            'bookings.status' => explode(',', $bookingStatus),
            'ref_booking_types.category' => $bookingCategory
        ];

        $bookingData = $this->booking->getBookingsByConditions($conditions);

        $tila = $this->documentType->getReservedDocumentType(DocumentTypeModel::DOC_TILA);
        $bookingData = array_filter($bookingData, function ($booking) use ($tila) {
            if ($booking['with_do']) {
                $uploadedTila = $this->uploadDocument->getDocumentsByBooking($booking['id'], get_if_exist($tila, 'id'));
                if (!empty($uploadedTila) && $uploadedTila['is_valid']) {
                    return true;
                }
                return false;
            }
            return true;
        });
        $bookingData = array_values($bookingData);

        header('Content-Type: application/json');
        echo json_encode($bookingData);
    }

    /**
     * Get booking data for creating safe conduct.
     */
    public function ajax_get_booking_data()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $bookingId = $this->input->get('id_booking');
            $exceptSafeConductId = $this->input->get('id_except_safe_conduct');
            $booking = $this->booking->getBookingById($bookingId);

            // fetch safe conduct data from booking directly
            $source = 'BOOKING';
            //$bookingContainers = $this->bookingContainer->getBookingContainersByBooking($bookingId);
            $bookingContainers = $this->bookingContainer->getBookingContainersByBookingSafeConduct($bookingId, $exceptSafeConductId);
            foreach ($bookingContainers as &$container) {
                $containerGoods = $this->bookingGoods->getBookingGoodsByBookingContainer($container['id']);
                $container['goods'] = $containerGoods;
            }
            //$bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($bookingId, true);
            $bookingGoods = $this->bookingGoods->getBookingGoodsByBookingSafeConduct($bookingId, true, $exceptSafeConductId);

            if (isset($_SERVER['HTTP_ACCEPT']) && preg_match('/text\/html/', $_SERVER['HTTP_ACCEPT'])) {
                if (!empty($booking) && (!empty($bookingContainers) || !empty($bookingGoods))) {
                    echo $this->load->view('safe_conduct/_booking_take', [
                        'source' => $source,
                        'booking' => $booking,
                        'containers' => $bookingContainers,
                        'goods' => $bookingGoods
                    ], true);
                }
            } else {
                header('Content-Type: application/json');
                echo json_encode([
                    'source' => $source,
                    'booking' => $booking,
                    'containers' => $bookingContainers,
                    'goods' => $bookingGoods
                ]);
            }
        }
    }

    /**
     * Ajax get all people data
     */
    public function ajax_get_by_keyword()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $search = $this->input->get('q');
            $page = $this->input->get('page');

            $filters = ['except' => get_url_param('except')];
            if (key_exists('check_in', $_GET)) {
                $filters['check_in'] = get_url_param('check_in', 0);
            }
            if (key_exists('check_out', $_GET)) {
                $filters['check_out'] = get_url_param('check_out', 0);
            }
            if (key_exists('type', $_GET)) {
                $filters['type'] = get_url_param('type');
            }
            if (key_exists('customer', $_GET)) {
                $filters['customer'] = get_url_param('customer');
            }

            $safeConducts = $this->safeConduct->getByKeyword($filters, $search, $page);

            header('Content-Type: application/json');
            echo json_encode($safeConducts);
        }
    }

    /**
     * Upload attachment for booking complete outbound.
     * @param $id
     */
    public function upload_attachment($id)
    {
        $safeConduct = $this->safeConduct->getSafeConductById($id);
        $files = $this->input->post('attachments');

        $this->db->trans_start();

        $this->safeConductAttachment->delete(['id_safe_conduct' => $id]);
        foreach ($files as $file) {
            $uploadedPhoto = 'temp/' . $file;
            $destFile = 'safe_conducts_booking/' . date('Y/m') . '/' . $file;
            $path = 'safe_conducts_booking/' . date('Y/m') . '/' . $file;
            $this->uploader->setDriver('s3')->move($uploadedPhoto, $destFile);
            $this->safeConductAttachment->create([
                'id_safe_conduct' => $id,
                'src' => $path
            ]);
        }

        // update attachment of the group
        if (!empty($safeConduct['id_safe_conduct_group'])) {
            $safeConductAttachments = $this->safeConductAttachment->getBy([
                'safe_conduct_attachments.id_safe_conduct' => $id
            ]);
            $relatedSafeConducts = $this->safeConduct->getBy(['safe_conducts.id_safe_conduct_group' => if_empty($safeConduct['id_safe_conduct_group'], '0')]);
            foreach ($relatedSafeConducts as $relatedSafeConduct) {
                if ($relatedSafeConduct['id'] != $id) {
                    $this->safeConductAttachment->delete(['id_safe_conduct' => $relatedSafeConduct['id']]);
                    foreach ($safeConductAttachments as $safeConductAttachment) {
                        $this->safeConductAttachment->create([
                            'id_safe_conduct' => $relatedSafeConduct['id'],
                            'src' => $safeConductAttachment['src'],
                        ]);
                    }
                }
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            flash('success', "Attachment safe conduct {$safeConduct['no_safe_conduct']} successfully uploaded");
        } else {
            flash('danger', 'Attachment failed to be saved, try again');
        }

        redirect('safe_conduct');
    }

    /**
     * Store upload data.
     */
    public function upload()
    {
        $result = [];

        foreach ($_FILES as $file => $data) {
            $fileName = rand(100, 999) . '_' . time() . '_' . $data['name'];
            $upload = $this->upload_to($file, $fileName);
            $result[$file] = $upload;
        }
        header('Content-Type: application/json');
        echo json_encode($result);
    }

    public function upload_to($file, $fileName = null, $location = null, $fileType = null)
    {
        $config['upload_path'] = is_null($location) ? FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'temp' : $location;
        $config['allowed_types'] = is_null($fileType) ? 'gif|jpg|jpeg|png|pdf|xls|xlsx|doc|docx|ppt|pptx|txt|zip|rar' : $fileType;
        $config['max_size'] = 3000;
        $config['max_width'] = 5000;
        $config['max_height'] = 5000;
        $config['file_ext_tolower'] = true;
        if (!empty($fileName)) {
            $config['file_name'] = $fileName;
        }

        $this->load->library('upload', $config);

        $errors = [];
        $data = [];

        if (is_array($_FILES[$file]['name'])) {
            $totalUploads = count($_FILES[$file]['name']);
            $status = true;
            for ($i = 0; $i < $totalUploads; $i++) {
                $_FILES[$file . '_multiple']['name'] = $_FILES[$file]['name'][$i];
                $_FILES[$file . '_multiple']['type'] = $_FILES[$file]['type'][$i];
                $_FILES[$file . '_multiple']['tmp_name'] = $_FILES[$file]['tmp_name'][$i];
                $_FILES[$file . '_multiple']['error'] = $_FILES[$file]['error'][$i];
                $_FILES[$file . '_multiple']['size'] = $_FILES[$file]['size'][$i];

                if ($this->upload->do_upload($file . '_multiple')) {
                    $data[] = $this->upload->data();
                } else {
                    $errors = $this->upload->display_errors();
                    $status = false;
                    break;
                }
            }
        } else {
            if ($this->upload->do_upload($file)) {
                $data = $this->upload->data();
                $status = true;
            } else {
                $errors = $this->upload->display_errors();
                $status = false;
            }
        }

        return [
            'status' => $status,
            'errors' => $errors,
            'data' => $data
        ];
    }

    /**
     * Delete temporary upload
     */
    public function delete_temp_upload()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $fileTemp = $this->input->post('file');
            $delete = $this->delete_file($fileTemp);
            header('Content-Type: application/json');
            echo json_encode(['status' => $delete]);
        }
    }

    /**
     * Delete file
     * @param $file
     * @param null $base
     * @return bool
     */
    public function delete_file($file, $base = null)
    {
        $directory = is_null($base) ? FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'temp' : $base;
        $filePath = $directory . DIRECTORY_SEPARATOR . $file;
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
    }

    
    /**
     * Edit safe conduct data.
     * @param $id
     */
    public function edit_safe_conduct($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_EDIT);

        $safeConduct = $this->safeConduct->getSafeConductById($id);

        $data = [
            'title' => "Safe Conduct",
            'subtitle' => "Edit safe conduct",
            'page' => "safe_conduct/edit_safe_conduct",
            'safeConduct' => $safeConduct,
            'drivers' => $this->people->getByType(PeopleModel::$TYPE_DRIVER),
            'expeditions' => $this->people->getByType(PeopleModel::$TYPE_EXPEDITION),
            'vehicles' => $this->vehicle->getAll(),
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Update safe conduct data.
     * @param $id
     */
    public function update_safe_conduct($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_EDIT);
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Safe conduct data', 'trim|required|integer');
            $this->form_validation->set_rules('vehicle', 'Vehicle type', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('no_police', 'Police plat number', 'trim|required|max_length[20]');
            $this->form_validation->set_rules('driver', 'Driver name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('expedition', 'Expedition', 'trim|required|max_length[100]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
                $this->edit_safe_conduct($id);
            } else {
                $id = $this->input->post('id');
                $vehicle = $this->input->post('vehicle');
                $noPolice = $this->input->post('no_police');
                $driver = $this->input->post('driver');
                $expedition = $this->input->post('expedition');

                $this->db->trans_start();
                $safe_conduct = $this->safeConduct->getSafeConductById($id);
                // update safe conduct
                $update = $this->safeConduct->updateSafeConduct([
                    'vehicle_type' => $vehicle,
                    'no_police' => $noPolice,
                    'driver' => $driver,
                    'expedition' => $expedition,
                ], $id);

                if($update){
                    $this->safeConductHistory->create([
                        'id_safe_conduct' => $id,
                        'data' => json_encode($safe_conduct),
                    ]);
                }
                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Safe conduct <strong>{$safe_conduct['no_safe_conduct']}</strong> successfully updated");
                    redirect("safe_conduct");
                } else {
                    flash('danger', "Update safe conduct <strong>{$safe_conduct['no_safe_conduct']}</strong> failed, try again or contact administrator");
                }
            }
        }
    }

}
