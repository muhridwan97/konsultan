<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Booking
 * @property BookingModel $booking
 * @property BookingReferenceModel $bookingReference
 * @property BookingGoodsModel $bookingGoods
 * @property BookingGoodsPositionModel $bookingGoodsPosition
 * @property BookingContainerModel $bookingContainer
 * @property BookingTypeModel $bookingType
 * @property BookingExtensionModel $bookingExtension
 * @property BookingStatusModel $bookingStatus
 * @property BookingCIFInvoiceModel $bookingCIFInvoice
 * @property BookingCIFInvoiceDetailModel $bookingCIFInvoiceDetail
 * @property BookingExporterModel $bookingExporter
 * @property ExtensionFieldModel $extensionField
 * @property HandlingModel $handling
 * @property UploadModel $upload
 * @property UploadDocumentModel $uploadDocument
 * @property UploadReferenceModel $uploadReference
 * @property ContainerModel $container
 * @property GoodsModel $goods
 * @property UnitModel $unit
 * @property PeopleModel $people
 * @property PaymentModel $payment
 * @property InvoiceModel $invoice
 * @property SafeConductModel $safeConduct
 * @property SafeConductAttachmentModel $safeConductAttachment
 * @property StatusHistoryModel $statusHistory
 * @property OpnameModel $opname
 * @property UserModel $user
 * @property WorkOrderModel $workOrder
 * @property WorkOrderContainerModel $workOrderContainer
 * @property WorkOrderGoodsModel $workOrderGoods
 * @property UserTokenModel $userToken
 * @property DiscrepancyHandoverModel $discrepancyHandover
 * @property DiscrepancyHandoverGoodsModel $discrepancyHandoverGoods
 * @property NotificationModel $notification
 * @property Mailer $mailer
 * @property Exporter $exporter
 */
class Booking extends CI_Controller
{
    /**
     * Booking constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('BookingGoodsPositionModel', 'bookingGoodsPosition');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('BookingTypeModel', 'bookingType');
        $this->load->model('BookingExtensionModel', 'bookingExtension');
        $this->load->model('BookingStatusModel', 'bookingStatus');
        $this->load->model('BookingCIFInvoiceModel', 'bookingCIFInvoice');
        $this->load->model('BookingCIFInvoiceDetailModel', 'bookingCIFInvoiceDetail');
        $this->load->model('BookingReferenceModel', 'bookingReference');
        $this->load->model('ExtensionFieldModel', 'extensionField');
        $this->load->model('HandlingModel', 'handling');
        $this->load->model('UploadModel', 'upload');
        $this->load->model('UploadDocumentModel', 'uploadDocument');
        $this->load->model('UploadReferenceModel', 'uploadReference');
        $this->load->model('ContainerModel', 'container');
        $this->load->model('GoodsModel', 'goods');
        $this->load->model('UnitModel', 'unit');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('PaymentModel', 'payment');
        $this->load->model('InvoiceModel', 'invoice');
        $this->load->model('SafeConductModel', 'safeConduct');
        $this->load->model('SafeConductAttachmentModel', 'safeConductAttachment');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('UserModel', 'user');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('WorkOrderContainerModel', 'workOrderContainer');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('OpnameModel', 'opname');
        $this->load->model('BranchModel', 'branch');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('NotificationModel', 'notification');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show bookings data list.
     */
    public function index()
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_VIEW, PERMISSION_BOOKING_OUT_VIEW], false);

        $branch = get_active_branch();

        $opnamePendingStatus = $this->opname->opnamePendingStatus();
        $opnameRejectStatus = $this->opname->opnameRejectStatus();
        $opnameProcessStatus = $this->opname->opnameProcessStatus();
        $opnamePendingDay = false;

        $opnamePendingDates = array_column($opnamePendingStatus, "opname_date");
        $opnameProcessDates = array_column($opnameProcessStatus, "opname_date");
        $opnameRejectDates = array_column($opnameRejectStatus, "opname_date");

        if (!empty($opnamePendingDates)) {
            foreach ($opnamePendingDates as $opnamePendingDate) {
                $date_diff = date_diff(new DateTime(), new Datetime($opnamePendingDate));
                if ($date_diff->format("%a") >= $branch['opname_pending_day']) {
                    $opnamePendingDay = true;
                    break;
                }
            }
        }

        if (!empty($opnameProcessDates)) {
            foreach ($opnameProcessDates as $opnameProcessDate) {
                $date_diff = date_diff(new DateTime(), new Datetime($opnameProcessDate));
                if ($date_diff->format("%a") >= $branch['opname_pending_day']) {
                    $opnamePendingDay = true;
                    break;
                }
            }
        }

        if (!empty($opnameRejectDates)) {
            foreach ($opnameRejectDates as $opnameRejectDate) {
                $date_diff = date_diff(new DateTime(), new Datetime($opnameRejectDate));
                if ($date_diff->format("%a") >= $branch['opname_pending_day']) {
                    $opnamePendingDay = true;
                    break;
                }
            }
        }

        if ($export = get_url_param('export')) {
            if ($export == 'csv') {
                $this->exporter->exportToCsv($this->booking->getAll(array_merge($_GET, ['unbuffered' => true])), "Booking");
            } else {
                $this->exporter->exportLargeResourceFromArray("Bookings", $this->booking->getAll($_GET));
            }
        } else {
            $bookingTypes = $this->bookingType->getAll();
            $selectedCustomer = $this->people->getById($this->input->get('customer'));
            $data = [
                'title' => "Booking",
                'subtitle' => "Data booking",
                'page' => "booking/index",
                'opnamePendingDay' => $opnamePendingDay,
                'bookingTypes' => $bookingTypes,
                'selectedCustomer' => $selectedCustomer,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Get ajax datatable.
     */
    public function data()
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_VIEW, PERMISSION_BOOKING_OUT_VIEW], false);

        $filters = array_merge(get_url_param('filter') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);
        $bookings = $this->booking->getAllBookings($filters);

        foreach ($bookings['data'] as &$row) {
            $obTpsPerforma = $this->payment->getPayments([
                'booking' => $row['id'],
                'type' => 'OB TPS PERFORMA'
            ]);
            $row['is_performa'] = empty($obTpsPerforma) ? 0 : 1;
            $row['booking_references'] = $this->bookingReference->getBy(['booking_references.id_booking' => $row['id']]);
        }

        header('Content-Type: application/json');
        echo json_encode($bookings);
    }

    /**
     * Show detail booking.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_VIEW, PERMISSION_BOOKING_OUT_VIEW], false);

        $booking = $this->booking->getBookingById($id);
        $bookingReferences = $this->bookingReference->getBy(['booking_references.id_booking' => $id]);
        $bookingOut = $this->booking->getBookingOutByBookingIn($id);
        $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($id);
        foreach ($bookingContainers as &$container) {
            $containerGoods = $this->bookingGoods->getBookingGoodsByBookingContainer($container['id']);
            $container['goods'] = $containerGoods;
        }
        $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($id, true);
        $bookingExtensions = $this->bookingExtension->getBookingExtensionByBooking($id);
        $data = [
            'title' => "Booking",
            'subtitle' => "View booking",
            'page' => "booking/view",
            'booking' => $booking,
            'bookingReferences' => $bookingReferences,
            'bookingOut' => $bookingOut,
            'bookingGoods' => $bookingGoods,
            'bookingContainers' => $bookingContainers,
            'bookingExtensions' => $bookingExtensions
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Export booking data to excel.
     *
     * @param $id
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function export_booking($id)
    {
        $this->load->model('BookingExporterModel', 'bookingExporter');

        $booking = $this->booking->getById($id);

        $this->bookingExporter->exportBooking($booking);
    }

    /**
     * Show create booking.
     * @param null $uploadId
     * @throws Exception
     */
    public function create($uploadId = null)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_CREATE, PERMISSION_BOOKING_OUT_CREATE], false);

        $branch = get_active_branch();
        $opnamePendingStatus = $this->opname->opnamePendingStatus();
        $opnameRejectStatus = $this->opname->opnameRejectStatus();
        $opnameProcessStatus = $this->opname->opnameProcessStatus();
        $opnamePendingDay = false;

        $opnamePendingDates = array_column($opnamePendingStatus, "opname_date");
        $opnameProcessDates = array_column($opnameProcessStatus, "opname_date");
        $opnameRejectDates = array_column($opnameRejectStatus, "opname_date");

        if (!empty($opnamePendingDates)) {
            foreach ($opnamePendingDates as $opnamePendingDate) {
                $date_diff = date_diff(new DateTime(), new Datetime($opnamePendingDate));
                if ($date_diff->format("%a") >= $branch['opname_pending_day']) {
                    $opnamePendingDay = true;
                    break;
                }
            }
        }

        if (!empty($opnameProcessDates)) {
            foreach ($opnameProcessDates as $opnameProcessDate) {
                $date_diff = date_diff(new DateTime(), new Datetime($opnameProcessDate));
                if ($date_diff->format("%a") >= $branch['opname_pending_day']) {
                    $opnamePendingDay = true;
                    break;
                }
            }
        }

        if (!empty($opnameRejectDates)) {
            foreach ($opnameRejectDates as $opnameRejectDate) {
                $date_diff = date_diff(new DateTime(), new Datetime($opnameRejectDate));
                if ($date_diff->format("%a") >= $branch['opname_pending_day']) {
                    $opnamePendingDay = true;
                    break;
                }
            }
        }

        if ($opnamePendingDay == true) {
            redirect("booking");
        } else {
            $uploads = [];
            $selectedUpload = [];
            $selectedCustomer = [];
            $selectedDocuments = [];
            $selectedBookingTypes = [];
            if (!is_null($uploadId)) {
                $selectedUpload = $this->upload->getById($uploadId);
                if (!empty($selectedUpload)) {
                    $uploads = [$selectedUpload];
                    $selectedCustomer = $this->people->getById($selectedUpload['id_person']);
                    $selectedDocuments = $this->uploadDocument->getDocumentsByUpload($selectedUpload['id']);
                    $selectedBookingTypes = $this->bookingType->getBy(['ref_booking_types.category' => $selectedUpload['category']]);
                }
            }

            $data = [
                'title' => "Bookings",
                'subtitle' => "Create booking",
                'page' => "booking/create",
                'uploads' => $uploads,
                'containers' => [],
                'goods' => [],
                'units' => $this->unit->getAll(),
                'selectedUpload' => $selectedUpload,
                'selectedCustomer' => $selectedCustomer,
                'selectedDocuments' => $selectedDocuments,
                'selectedBookingTypes' => $selectedBookingTypes,
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Save booking data.
     * Save new booking.
     */
    public function save()
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_CREATE, PERMISSION_BOOKING_OUT_CREATE], false);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('category', 'Booking type', 'trim|required');
            $this->form_validation->set_rules('type', 'Booking type', 'trim|required|integer');
            $this->form_validation->set_rules('booking_date', 'Booking date', 'trim|required');
            $this->form_validation->set_rules('branch', 'Branch', 'trim|required|integer');
            $this->form_validation->set_rules('customer', 'Customer', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $category = $this->input->post('category');
                $type = $this->input->post('type');
                $document = $this->input->post('document');
                $date = format_date($this->input->post('booking_date'), 'Y-m-d H:i:s');
                $noReference = $this->input->post('no_reference');
                $referenceDate = format_date($this->input->post('reference_date'), 'Y-m-d');
                $vessel = $this->input->post('vessel');
                $voyage = $this->input->post('voyage');
                $description = $this->input->post('description');
                $branch = $this->input->post('branch');
                $supplier = $this->input->post('supplier');
                $customer = $this->input->post('customer');
                $mode = $this->input->post('mode');
                $documentStatus = $this->input->post('document_status');
                $bookingInId = $this->input->post('booking_in');
                $extensions = $this->input->post('extensions');
                $containers = $this->input->post('containers');
                $goods = $this->input->post('goods');
                $netto = $this->input->post('netto');
                $bruto = $this->input->post('bruto');

                $bookingType = $this->bookingType->getById($type);
                $customerData = $this->people->getById($customer);
                if ($category == BookingTypeModel::CATEGORY_INBOUND) {
                    $noBooking = $this->booking->getAutoNumberBooking(BookingModel::NO_INBOUND);
                    $paymentStatus = null;
                } else {
                    $noBooking = $this->booking->getAutoNumberBooking(BookingModel::NO_OUTBOUND);
                    $paymentStatus = $customerData['outbound_type'] == PeopleModel::OUTBOUND_CASH_AND_CARRY ? 'PENDING' : 'APPROVED';
                }
                $this->db->trans_start();

                $bookingInReference = null;
                if ($bookingType['category'] == BookingTypeModel::CATEGORY_OUTBOUND && $bookingType['type'] == BookingTypeModel::TYPE_IMPORT) {
                    if (is_array($bookingInId)) {
                        $bookingInReference = if_empty(reset($bookingInId), null);
                    } else {
                        $bookingInReference = if_empty($bookingInId, null);
                    }
                }

                // inserting booking header
                $this->booking->createBooking([
                    'no_booking' => $noBooking,
                    'no_reference' => $noReference,
                    'reference_date' => $referenceDate,
                    'id_booking' => $bookingInReference,
                    'id_booking_type' => $type,
                    'id_supplier' => $supplier,
                    'id_customer' => $customer,
                    'id_upload' => $document,
                    'id_branch' => $branch,
                    'booking_date' => $date,
                    'mode' => $mode,
                    'vessel' => $vessel,
                    'voyage' => $voyage,
                    'description' => $description,
                    'status' => BookingModel::STATUS_BOOKED,
                    'status_payout' => $paymentStatus,
                    'document_status' => $documentStatus,
                    'created_by' => UserModel::authenticatedUserData('id'),
                    'total_netto' => extract_number($netto),
                    'total_bruto' => extract_number($bruto),
                ]);
                $bookingId = $this->db->insert_id();

                // set booking reference
                if ($bookingType['category'] == BookingTypeModel::CATEGORY_OUTBOUND) {
                    if (!empty($bookingInId) && !is_array($bookingInId)) {
                        $bookingInId = [$bookingInId];
                    }
                    foreach ($bookingInId as $bookingRefId) {
                        $this->bookingReference->create([
                            'id_booking' => $bookingId,
                            'id_booking_reference' => $bookingRefId
                        ]);
                    }
                }

                // insert booking status
                $this->bookingStatus->createBookingStatus([
                    'id_booking' => $bookingId,
                    'booking_status' => BookingModel::STATUS_BOOKED,
                    'document_status' => $documentStatus,
                    'no_doc' => $noReference,
                    'doc_date' => format_date($referenceDate),
                    'description' => 'First create booking',
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);

                // insert booking extension if needed
                if (!empty($extensions)) {
                    foreach ($extensions as $name => $value) {
                        $extensionField = $this->extensionField->getBy(['ref_extension_fields.field_name' => $name], true);
                        if (!empty($extensionField)) {
                            if (in_array($extensionField['type'], ['CHECKBOX', '...', '...'])) {
                                $value = json_encode($value);
                            } else if ($extensionField['type'] == 'DATE') {
                                $value = format_date($value);
                            } else if ($extensionField['type'] == 'DATE TIME') {
                                $value = format_date($value, 'Y-m-d H:i:s');
                            }
                            $this->bookingExtension->createBookingExtension([
                                'id_booking' => $bookingId,
                                'id_extension_field' => $extensionField['id'],
                                'value' => $value
                            ]);
                        }
                    }
                }

                if (!empty($containers)) {
                    foreach ($containers as $container) {
                        $this->bookingContainer->createBookingContainer([
                            'id_booking' => $bookingId,
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
                        $bookingContainerId = $this->db->insert_id();

                        if (key_exists('goods', $container)) {
                            foreach ($container['goods'] as $item) {
                                $this->bookingGoods->createBookingGoods([
                                    'id_booking' => $bookingId,
                                    'id_booking_container' => $bookingContainerId,
                                    'id_booking_reference' => if_empty($item['id_booking_reference'], null),
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
                                    'no_pallet' => $category == BookingTypeModel::CATEGORY_INBOUND? $item['no_pallet']."/".$noBooking : $item['no_pallet'],
                                    'is_hold' => $item['is_hold'],
                                    'status' => $item['status'],
                                    'status_danger' => $item['status_danger'],
                                    'description' => $item['description'],
                                    'ex_no_container' => if_empty($item['ex_no_container'], null)
                                ]);
                            }
                        }
                    }
                }

                if (!empty($goods)) {
                    foreach ($goods as $item) {
                        $this->bookingGoods->createBookingGoods([
                            'id_booking' => $bookingId,
                            'id_booking_reference' => if_empty($item['id_booking_reference'], null),
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
                            'no_pallet' => $category == BookingTypeModel::CATEGORY_INBOUND? $item['no_pallet']."/".$noBooking : $item['no_pallet'],
                            'is_hold' => $item['is_hold'],
                            'status' => $item['status'],
                            'status_danger' => $item['status_danger'],
                            'description' => $item['description'],
                            'ex_no_container' => if_empty($item['ex_no_container'], null)
                        ]);
                        $bookingGoodsId = $this->db->insert_id();

                        $positionBlocks = if_empty(explode(',', $item['id_position_blocks'] ?? ''), []);
                        foreach ($positionBlocks as $blockId) {
                            $this->bookingGoodsPosition->create([
                                'id_booking_goods' => $bookingGoodsId,
                                'id_position_block' => $blockId
                            ]);
                        }
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Booking <strong>{$noBooking}</strong> successfully created", 'booking');
                } else {
                    flash('danger', "Create booking <strong>{$noBooking}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->create();
    }

    /**
     * Show edit booking.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_EDIT, PERMISSION_BOOKING_OUT_EDIT], false);

        $booking = $this->booking->getBookingById($id);
        $bookingReferences = $this->bookingReference->getBy(['booking_references.id_booking' => $id]);
        $bookingIn = $this->booking->getBookingStocksByCustomer($booking['id_customer'], $booking['id_booking']);
        if (empty($bookingIn) && !empty($bookingReferences)) {
            $bookingIn = $this->booking->getBookingById(array_column($bookingReferences, 'id_booking_reference'));
        }

        $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($id);
        foreach ($bookingContainers as &$container) {
            $containerGoods = $this->bookingGoods->getBookingGoodsByBookingContainer($container['id']);
            $container['goods'] = $containerGoods;
        }
        $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($id, true);
        $bookingExtensions = $this->bookingExtension->getBookingExtensionByBooking($id);
        $extensionFields = $this->extensionField->getByBookingType($booking['id_booking_type']);
        $extensions = $this->load->view('extension_field/_extensions', [
            'extensionFields' => $extensionFields,
            'bookingExtensions' => $bookingExtensions
        ], true);
        $data = [
            'title' => "Bookings",
            'subtitle' => "Edit booking",
            'page' => "booking/edit",
            'booking' => $booking,
            'bookingReferences' => $bookingReferences,
            'types' => $this->bookingType->getBy(['ref_booking_types.category' => $booking['category']]),
            'uploads' => $this->upload->getUploadsByBookingType([
                'id_booking_type' => $booking['id_booking_type'],
                'id_customer' => $booking['id_customer'],
                'id_supplier' => $booking['id_supplier'],
                'available_only' => true,
                'except' => $booking['id_upload']
            ]),
            'containers' => $bookingContainers,
            'goods' => $bookingGoods,
            'units' => $this->unit->getAll(),
            'customer' => $this->people->getById($booking['id_customer']),
            'supplier' => $this->people->getById($booking['id_supplier']),
            'extensionFields' => $extensionFields,
            'extensions' => $extensions,
            'bookingIn' => $bookingIn
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show edit booking.
     * @param $id
     */
    public function edit_extension($id)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_EDIT, PERMISSION_BOOKING_OUT_EDIT], false);

        $booking = $this->booking->getBookingById($id);
        $bookingExtensions = $this->bookingExtension->getBookingExtensionByBooking($id);
        $bookingStatuses = $this->bookingStatus->getBookingStatusesByBooking($id);
        $extensionFields = $this->extensionField->getByBookingType($booking['id_booking_type']);
        $extensions = $this->load->view('extension_field/_extensions', [
            'extensionFields' => $extensionFields,
            'bookingExtensions' => $bookingExtensions
        ], true);

        $data = [
            'title' => "Bookings",
            'subtitle' => "Edit booking extension",
            'page' => "booking/edit_extension",
            'booking' => $booking,
            'bookingStatus' => end($bookingStatuses),
            'extensionFields' => $extensionFields,
            'extensions' => $extensions,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show edit booking payment status.
     * @param $id
     */
    public function edit_payment_status($id)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_EDIT_PAYMENT_STATUS, PERMISSION_BOOKING_OUT_EDIT_PAYMENT_STATUS], false);

        $booking = $this->booking->getBookingById($id);

        $data = [
            'title' => "Bookings",
            'subtitle' => "Edit payment status",
            'page' => "booking/edit_payment_status",
            'booking' => $booking,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show edit booking bcf status.
     * @param $id
     */
    public function edit_bcf_status($id)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_EDIT_BCF_STATUS, PERMISSION_BOOKING_OUT_EDIT_BCF_STATUS], false);

        $booking = $this->booking->getBookingById($id);

        $data = [
            'title' => "Bookings",
            'subtitle' => "Edit BCF status",
            'page' => "booking/edit_bcf_status",
            'booking' => $booking,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Update booking by id.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_EDIT, PERMISSION_BOOKING_OUT_EDIT], false);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('category', 'Booking type', 'trim|required');
            $this->form_validation->set_rules('id', 'Booking data', 'trim|required|integer');
            $this->form_validation->set_rules('type', 'Booking type', 'trim|required|integer');
            $this->form_validation->set_rules('booking_date', 'Booking date', 'trim|required');
            $this->form_validation->set_rules('branch', 'Branch', 'trim|required|integer');
            $this->form_validation->set_rules('customer', 'Customer', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $id = $this->input->post('id');
                $category = $this->input->post('category');
                $type = $this->input->post('type');
                $document = $this->input->post('document');
                $date = format_date($this->input->post('booking_date'), 'Y-m-d H:i:s');
                $noReference = $this->input->post('no_reference');
                $referenceDate = format_date($this->input->post('reference_date'));
                $vessel = $this->input->post('vessel');
                $voyage = $this->input->post('voyage');
                $description = $this->input->post('description');
                $branch = $this->input->post('branch');
                $supplier = $this->input->post('supplier');
                $customer = $this->input->post('customer');
                $mode = $this->input->post('mode');
                $documentStatus = $this->input->post('document_status');
                $bookingInId = $this->input->post('booking_in');
                $extensions = $this->input->post('extensions');
                $containers = $this->input->post('containers');
                $goods = $this->input->post('goods');
                $netto = $this->input->post('netto');
                $bruto = $this->input->post('bruto');

                $bookingType = $this->bookingType->getById($type);
                $booking = $this->booking->getBookingById($id);
                if ($booking['category'] != $category) {
                    if ($category == BookingTypeModel::CATEGORY_INBOUND) {
                        $noBooking = $this->booking->getAutoNumberBooking(BookingModel::NO_INBOUND);
                    } else {
                        $noBooking = $this->booking->getAutoNumberBooking(BookingModel::NO_OUTBOUND);
                    }
                } else {
                    $noBooking = $booking['no_booking'];
                }

                $this->db->trans_start();

                $bookingInReference = null;
                if ($bookingType['category'] == BookingTypeModel::CATEGORY_OUTBOUND && $bookingType['type'] == BookingTypeModel::TYPE_IMPORT) {
                    if (is_array($bookingInId)) {
                        $bookingInReference = if_empty(reset($bookingInId), null);
                    } else {
                        $bookingInReference = if_empty($bookingInId, null);
                    }
                }

                // update booking data
                $this->booking->updateBooking([
                    'no_reference' => $noReference,
                    'reference_date' => $referenceDate,
                    'id_booking' => $bookingInReference,
                    'id_booking_type' => $type,
                    'id_supplier' => $supplier,
                    'id_customer' => $customer,
                    'id_upload' => $document,
                    'id_branch' => $branch,
                    'no_booking' => $noBooking,
                    'booking_date' => $date,
                    'mode' => $mode,
                    'vessel' => $vessel,
                    'voyage' => $voyage,
                    'status' => $booking['status'] == BookingModel::STATUS_REJECTED ? BookingModel::STATUS_BOOKED : $booking['status'],
                    'document_status' => $documentStatus,
                    'description' => $description,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => UserModel::authenticatedUserData('id'),
                    'total_netto' => extract_number($netto),
                    'total_bruto' => extract_number($bruto),
                ], $id);

                // set booking reference
                $this->bookingReference->delete(['id_booking' => $id]);
                if ($bookingType['category'] == BookingTypeModel::CATEGORY_OUTBOUND) {
                    if (!empty($bookingInId) && !is_array($bookingInId)) {
                        $bookingInId = [$bookingInId];
                    }
                    foreach ($bookingInId as $bookingRefId) {
                        $this->bookingReference->create([
                            'id_booking' => $id,
                            'id_booking_reference' => $bookingRefId
                        ]);
                    }
                }

                // insert booking if necessary status
                if ($booking['document_status'] != $documentStatus) {
                    $this->bookingStatus->createBookingStatus([
                        'id_booking' => $id,
                        'booking_status' => $booking['status'],
                        'document_status' => $documentStatus,
                        'no_doc' => $noReference,
                        'doc_date' => format_date($referenceDate),
                        'description' => 'Update booking data',
                        'created_by' => UserModel::authenticatedUserData('id')
                    ]);
                }

                // update or insert booking extension if needed
                if (!empty($extensions)) {
                    $bookingExtensions = $this->bookingExtension->getBookingExtensionByBooking($id);
                    foreach ($extensions as $name => $value) {
                        $extensionField = $this->extensionField->getBy(['ref_extension_fields.field_name' => $name], true);
                        if (!empty($extensionField)) {

                            if (in_array($extensionField['type'], ['CHECKBOX', '...', '...'])) {
                                $value = json_encode($value);
                            } else if ($extensionField['type'] == 'DATE') {
                                $value = format_date($value);
                            } else if ($extensionField['type'] == 'DATE TIME') {
                                $value = format_date($value, 'Y-m-d H:i:s');
                            }

                            $isNewExtension = true;
                            foreach ($bookingExtensions as $bookingExtension) {
                                if ($bookingExtension['id_extension_field'] == $extensionField['id']) {
                                    $this->bookingExtension->updateBookingExtension([
                                        'value' => $value
                                    ], $bookingExtension['id']);
                                    $isNewExtension = false;
                                    break;
                                }
                            }

                            if ($isNewExtension) {
                                $this->bookingExtension->createBookingExtension([
                                    'id_booking' => $id,
                                    'id_extension_field' => $extensionField['id'],
                                    'value' => $value
                                ]);
                            }
                        }
                    }
                }

                $this->bookingContainer->deleteBookingContainerByBooking($id);
                $this->bookingGoods->deleteBookingGoodsByBooking($id);

                if (!empty($containers)) {
                    foreach ($containers as $container) {
                        $this->bookingContainer->createBookingContainer([
                            'id_booking' => $id,
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
                        $bookingContainerId = $this->db->insert_id();

                        if (key_exists('goods', $container)) {
                            foreach ($container['goods'] as $item) {
                                $this->bookingGoods->createBookingGoods([
                                    'id_booking' => $id,
                                    'id_booking_container' => $bookingContainerId,
                                    'id_booking_reference' => if_empty($item['id_booking_reference'], null),
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
                            }
                        }
                    }
                }

                if (!empty($goods)) {
                    foreach ($goods as $item) {
                        $this->bookingGoods->createBookingGoods([
                            'id_booking' => $id,
                            'id_booking_reference' => if_empty($item['id_booking_reference'], null),
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
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Booking <strong>{$booking['no_booking']}</strong> successfully updated", 'booking');
                } else {
                    flash('danger', "Update booking <strong>{$booking['no_booking']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->edit($id);
    }


    /**
     * Update extension field in booking regardless status but void.
     * @param $id
     */
    public function update_extension($id)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_EDIT, PERMISSION_BOOKING_OUT_EDIT], false);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $extensions = $this->input->post('extensions');
            $bookingStatus = $this->input->post('booking_status');
            $documentStatus = $this->input->post('document_status');
            $noDoc = $this->input->post('no_doc');
            $docDate = $this->input->post('doc_date');
            $description = $this->input->post('description');

            $this->db->trans_start();

            $booking = $this->booking->getBookingById($id);

            $bookingStatuses = $this->bookingStatus->getBookingStatusesByBooking($id);
            $docStatus = '';
            if (!empty($bookingStatuses)) {
                $docStatus = end($bookingStatuses)['document_status'];
            }

            if ($documentStatus != $docStatus || $bookingStatus != $booking['status']) {
                $this->booking->updateBooking([
                    'document_status' => $documentStatus
                ], $id);

                $this->bookingStatus->createBookingStatus([
                    'id_booking' => $id,
                    'booking_status' => $bookingStatus == 'UPDATE DESCRIPTION' ? $bookingStatus : $booking['status'],
                    'document_status' => $documentStatus,
                    'no_doc' => $noDoc,
                    'doc_date' => format_date($docDate),
                    'description' => $description,
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);
            }

            if (!empty($extensions)) {
                $bookingExtensions = $this->bookingExtension->getBookingExtensionByBooking($id);
                foreach ($extensions as $name => $value) {
                    $extensionField = $this->extensionField->getBy(['ref_extension_fields.field_name' => $name], true);
                    if (!empty($extensionField)) {

                        if (in_array($extensionField['type'], ['CHECKBOX', '...', '...'])) {
                            $value = json_encode($value);
                        } else if ($extensionField['type'] == 'DATE') {
                            $value = format_date($value);
                        } else if ($extensionField['type'] == 'DATE TIME') {
                            $value = format_date($value, 'Y-m-d H:i:s');
                        }

                        $isNewExtension = true;
                        foreach ($bookingExtensions as $bookingExtension) {
                            if ($bookingExtension['id_extension_field'] == $extensionField['id']) {
                                $this->bookingExtension->updateBookingExtension([
                                    'value' => $value
                                ], $bookingExtension['id']);
                                $isNewExtension = false;
                                break;
                            }
                        }

                        if ($isNewExtension) {
                            $this->bookingExtension->createBookingExtension([
                                'id_booking' => $id,
                                'id_extension_field' => $extensionField['id'],
                                'value' => $value
                            ]);
                        }
                    }
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Booking extension <strong>{$booking['no_booking']}</strong> successfully updated", 'booking');
            } else {
                flash('danger', "Update booking extension <strong>{$booking['no_booking']}</strong> failed, try again or contact administrator");
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->edit_extension($id);
    }

    /**
     * Update payment status.
     * @param $id
     */
    public function update_payment_status($id)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_EDIT_PAYMENT_STATUS, PERMISSION_BOOKING_OUT_EDIT_PAYMENT_STATUS], false);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $paymentStatus = $this->input->post('payment_status');
            $description = $this->input->post('description');

            $this->db->trans_start();

            $booking = $this->booking->getBookingById($id);

            if ($booking['payment_status'] != $paymentStatus) {
                $userId = UserModel::authenticatedUserData('id');
                $user = $this->user->getById($userId);

                $this->booking->updateBooking([
                    'payment_status' => $paymentStatus,
                    'updated_by' => UserModel::authenticatedUserData('id'),
                    'updated_at' => date('Y-m-d H:i:s')
                ], $id);

                $this->statusHistory->create([
                    'id_reference' => $id,
                    'type' => StatusHistoryModel::TYPE_BOOKING_PAYMENT_STATUS,
                    'status' => $paymentStatus,
                    'description' => $description
                ]);

                $customer = $this->people->getById($booking['id_customer']);
                $adminEmail = get_active_branch('email');

                if (ENVIRONMENT == 'production') {
                    $emailCC = $customer['email'];
                }

                $emailTo = $adminEmail;
                $emailTitle = "Payment status booking " . $booking['no_booking'] . ' changed to ' . $paymentStatus;
                $emailTemplate = 'emails/basic';
                $emailData = [
                    'title' => 'Payment Status Update',
                    'name' => if_empty(get_active_branch('pic'), 'Admin'),
                    'email' => $adminEmail,
                    'content' => 'Booking ' . $booking['no_booking'] . ' (' . $booking['no_reference'] . ') was changed to <b>' . $paymentStatus . '</b> by ' . $user . ' at ' . readable_date('now') . ', please evaluate immediately.<br><b>Note:</b> ' . $description
                ];
                $emailOption = [
                    'cc' => isset($emailCC) ? $emailCC : null,
                ];

                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOption);

            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Booking payment status <strong>{$booking['no_booking']}</strong> successfully updated", 'booking');
            } else {
                flash('danger', "Update payment status <strong>{$booking['no_booking']}</strong> failed, try again or contact administrator");
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->edit_payment_status($id);
    }

    /**
     * Update payment status.
     * @param $id
     */
    public function update_bcf_status($id)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_EDIT_BCF_STATUS, PERMISSION_BOOKING_OUT_EDIT_BCF_STATUS], false);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $bcfStatus = $this->input->post('bcf_status');
            $description = $this->input->post('description');

            $this->db->trans_start();

            $booking = $this->booking->getBookingById($id);

            if ($booking['bcf_status'] != $bcfStatus) {
                $userId = UserModel::authenticatedUserData('id');
                $user = $this->user->getById($userId);

                $this->booking->updateBooking([
                    'bcf_status' => $bcfStatus,
                    'updated_by' => UserModel::authenticatedUserData('id'),
                    'updated_at' => date('Y-m-d H:i:s')
                ], $id);

                $this->statusHistory->create([
                    'id_reference' => $id,
                    'type' => StatusHistoryModel::TYPE_BOOKING_BCF_STATUS,
                    'status' => $bcfStatus,
                    'description' => $description
                ]);

                $customer = $this->people->getById($booking['id_customer']);
                $adminEmail = get_active_branch('email');

                if (ENVIRONMENT == 'production') {
                    $emailCC = $customer['email'];
                }

                $emailTo = $adminEmail;
                $emailTitle = "BCF status booking " . $booking['no_booking'] . ' changed to ' . $bcfStatus;
                $emailTemplate = 'emails/basic';
                $emailData = [
                    'title' => 'BCF Status Update',
                    'name' => if_empty(get_active_branch('pic'), 'Admin'),
                    'email' => $adminEmail,
                    'content' => 'Booking ' . $booking['no_booking'] . ' (' . $booking['no_reference'] . ') was changed to <b>' . $bcfStatus . '</b> by ' . $user . ' at ' . readable_date('now') . ', please evaluate immediately.<br><b>Note:</b> ' . $description
                ];

                $emailOption = [
                    'cc' => isset($emailCC) ? $emailCC : null,
                ];

                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOption);

            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Booking BCF status <strong>{$booking['no_booking']}</strong> successfully updated", 'booking');
            } else {
                flash('danger', "Update payment status <strong>{$booking['no_booking']}</strong> failed, try again or contact administrator");
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->edit_bcf_status($id);
    }

    /**
     * Perform deleting booking data.
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_DELETE, PERMISSION_BOOKING_OUT_DELETE], false);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $bookingData = $this->booking->getBookingById($id);

            if ($this->booking->deleteBooking($id)) {
                flash('warning', "Booking <strong>{$bookingData['no_booking']}</strong> successfully deleted");
            } else {
                flash('danger', "Delete booking <strong>{$bookingData['no_booking']}</strong> failed, try again or contact administrator");
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('booking');
    }


    /**
     * Validate booking data
     * @param $type
     * @param $bookingId
     */
    public function revert($type, $bookingId)
    {
        AuthorizationModel::isAuthorized(PERMISSION_BOOKING_STATUS_REVERT);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_data(['type' => $type]);
            $this->form_validation->set_rules('type', 'Booking status', 'in_list[booked]');

             if ($this->form_validation->run() == FALSE) {
                 flash('warning', validation_errors());
            } else {

                $description = $this->input->post('message');
                $statuses = [
                    'booked' => BookingModel::STATUS_BOOKED,
                ];
                $status = $statuses[$type];
                $booking = $this->booking->getBookingById($bookingId);

                $this->db->trans_start();

                $this->booking->updateBooking([
                    'status' => $status
                ], $bookingId);

                $this->bookingStatus->createBookingStatus([
                    'id_booking' => $bookingId,
                    'booking_status' => $status,
                    'document_status' => $booking['document_status'],
                    'no_doc' => $booking['no_reference'],
                    'doc_date' => format_date($booking['reference_date']),
                    'description' => $description,
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    $statusClass = 'success';
                    flash($statusClass, "Booking no <strong>{$booking['no_booking']}</strong> successfully <strong>{$status}</strong>");
                } else {
                    flash('danger', "Validating booking no <strong>{$booking['no_booking']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('booking');
    }

    /**
     * Validate booking data
     * @param $type
     * @param $bookingId
     */
    public function validate($type, $bookingId)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_VALIDATE, PERMISSION_BOOKING_OUT_VALIDATE], false);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_data(['type' => $type]);
            $this->form_validation->set_rules('type', 'Booking status', 'in_list[approve,reject,complete]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $description = $this->input->post('message');
                $statuses = [
                    'approve' => BookingModel::STATUS_APPROVED,
                    'reject' => BookingModel::STATUS_REJECTED,
                    'complete' => BookingModel::STATUS_COMPLETED,
                ];
                $status = $statuses[$type];
                $booking = $this->booking->getBookingById($bookingId);

                if ($status == BookingModel::STATUS_APPROVED && ($booking['category'] == 'OUTBOUND' || $booking['branch_type'] == 'PLB')) {
                    if (strpos($booking['document_type'], 'SPPB') === false) {
                        flash('danger', 'Cannot approve booking does not has SPPB document', '_back', 'booking');
                    }
                }

                if ($booking['status'] == BookingModel::STATUS_COMPLETED && $status == BookingModel::STATUS_APPROVED && $booking['category'] == 'INBOUND') {
                    $this->load->model('DiscrepancyHandoverModel', 'discrepancyHandover');
                    $discrepancyHandover = $this->discrepancyHandover->getBy([
                        'discrepancy_handovers.id_booking' => $bookingId,
                        'discrepancy_handovers.status!=' => DiscrepancyHandoverModel::STATUS_CANCELED
                    ], true);
                    if (!empty($discrepancyHandover)) {
                        flash('danger', "Booking has discrepancy handover {$discrepancyHandover['no_discrepancy']}, please cancel or delete before revert!", '_back', 'booking');
                    }
                }

                $this->db->trans_start();

                $this->booking->updateBooking([
                    'status' => $status
                ], $bookingId);

                $this->bookingStatus->createBookingStatus([
                    'id_booking' => $bookingId,
                    'booking_status' => $status,
                    'document_status' => $booking['document_status'],
                    'no_doc' => $booking['no_reference'],
                    'doc_date' => format_date($booking['reference_date']),
                    'description' => $description,
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);

                $discrepancies = [];
                $discrepancyHandoverId = null;
                if ($statuses[$type] == BookingModel::STATUS_COMPLETED && $booking['category'] == 'INBOUND') {
                    $discrepancies = $this->bookingGoods->getStockDiscrepancy($bookingId);
                    if (!empty($discrepancies)) {
                        $this->load->model('DiscrepancyHandoverModel', 'discrepancyHandover');
                        $this->load->model('DiscrepancyHandoverGoodsModel', 'discrepancyHandoverGoods');

                        $this->discrepancyHandover->create([
                            'no_discrepancy' => $this->discrepancyHandover->getAutoNumber(),
                            'id_booking' => $bookingId,
                            'description' => 'Auto generate discrepancy handover'
                        ]);
                        $discrepancyHandoverId = $this->db->insert_id();

                        $discrepancies = $this->bookingGoods->getStockDiscrepancy($bookingId);
                        foreach ($discrepancies as $discrepancy) {
                            $this->discrepancyHandoverGoods->create([
                                'id_discrepancy_handover' => $discrepancyHandoverId,
                                'id_goods' => $discrepancy['id_goods'],
                                'id_unit' => $discrepancy['id_unit'],
                                'source' => $discrepancy['source'],
                                'assembly_type' => $discrepancy['assembly_type'],
                                'stock_exist' => $discrepancy['stock_exist'],
                                'quantity_booking' => $discrepancy['quantity_booking'],
                                'quantity_stock' => $discrepancy['quantity_stock'],
                                'quantity_difference' => $discrepancy['quantity_difference'],
                            ]);
                        }

                        $this->statusHistory->create([
                            'id_reference' => $discrepancyHandoverId,
                            'type' => StatusHistoryModel::TYPE_DISCREPANCY_HANDOVER,
                            'status' => DiscrepancyHandoverModel::STATUS_PENDING,
                            'description' => 'Generated discrepancy handover',
                        ]);
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    $statusClass = 'warning';
                    if ($statuses[$type] != BookingModel::STATUS_REJECTED) {
                        $statusClass = 'success';
                    }

                    if ($statuses[$type] == BookingModel::STATUS_COMPLETED) {
                        $branch = $this->branch->getById($booking['id_branch']);
                        if ($branch['dashboard_status'] == true) {
                            if ($booking['category'] == BookingTypeModel::CATEGORY_INBOUND) {
                                $no_reference = substr($booking['no_reference'], -4);
                                $category = strtolower($booking['category']);
                                $name = UserModel::authenticatedUserData('name');
                                $data_whatsapp = "[BOOKING COMPLETE] Booking {$category} {$booking['customer_name']} aju {$no_reference} telah dikomplitkan oleh {$name}, Silahkan tim compliance untuk melanjutkan proses SPPD Inbound.";
                            }

                            if ($booking['category'] == BookingTypeModel::CATEGORY_OUTBOUND) {
                                $no_reference = substr($booking['no_reference'], -4);
                                $category = strtolower($booking['category']);
                                $name = UserModel::authenticatedUserData('name');
                                $data_whatsapp = "[BOOKING COMPLETE] Booking {$category} {$booking['customer_name']} aju {$no_reference} telah dikomplitkan oleh {$name}, Silahkan tim compliance untuk melanjutkan proses SPPD Outbound.";
                            }
                            $whatsapp_group = get_setting('whatsapp_group_admin');
                            if (isset($data_whatsapp) && !empty($whatsapp_group)) {
                                $this->send_message($data_whatsapp, $whatsapp_group);
                            }
                        }

                        if (!empty($discrepancies) && !empty($discrepancyHandoverId)) {
                            $discrepancyHandover = $this->discrepancyHandover->getById($discrepancyHandoverId);
                            $message = "*Discrepancy Handover {$discrepancyHandover['no_discrepancy']}*\n";
                            $message .= "————————————————————\n";
                            $message .= "Booking with Reference Number *{$booking['no_reference']}* ";
                            $message .= "by Customer *{$booking['customer_name']}* ";
                            $message .= "in Branch *{$booking['branch']}* contains " . count($discrepancies) . " discrepancy items.\n\n";
                            $message .= "Click the link below to see details: \n";
                            $message .= site_url('discrepancy-handover/view/' . $discrepancyHandoverId);

                            // send to compliance group
                            $complianceGroup = get_setting('whatsapp_group_admin');
                            $this->notification->broadcast([
                                'url' => 'sendMessage',
                                'method' => 'POST',
                                'payload' => [
                                    'chatId' => detect_chat_id($complianceGroup),
                                    'body' => $message,
                                ]
                            ], NotificationModel::TYPE_CHAT_PUSH);
                        }
                    }

                    if ($statuses[$type] == BookingModel::STATUS_APPROVED) {
                        $branch = $this->branch->getById($booking['id_branch']);
                        if ($branch['dashboard_status'] == true) {
                            if ($booking['category'] == BookingTypeModel::CATEGORY_INBOUND) {
                                $no_reference = substr($booking['no_reference'], -4);
                                $category = strtolower($booking['category']);
                                $data_approved = "[BOOKING CREATED] Booking {$category} {$booking['customer_name']} aju {$no_reference} telah dibuat, Silahkan tim operasional untuk melanjutkan proses selanjutnya.";
                            }
                            if ($booking['category'] == BookingTypeModel::CATEGORY_OUTBOUND) {
                                $no_reference = substr($booking['no_reference'], -4);
                                $category = strtolower($booking['category']);
                                $data_approved = "[BOOKING CREATED] Booking {$category} {$booking['customer_name']} aju {$no_reference} telah dibuat, Silahkan tim operasional untuk melanjutkan proses selanjutnya.";
                            }
                            $whatsapp_group = get_setting('whatsapp_group_admin');
                            if (isset($data_approved) && !empty($whatsapp_group)) {
                                $this->send_message($data_approved, $whatsapp_group);
                            }
                        }
                    }

                    flash($statusClass, "Booking no <strong>{$booking['no_booking']}</strong> successfully <strong>{$status}</strong>");
                } else {
                    flash('danger', "Validating booking no <strong>{$booking['no_booking']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('booking');
    }

    /**
     * Send a message to a new or existing chat.
     * 6281333377368-1557128212@g.us
     */
    public function send_message($data, $whatsapp_group)
    {
        $data = [
            'url' => 'sendMessage',
            'method' => 'POST',
            'payload' => [
                'chatId' => detect_chat_id($whatsapp_group),
                'body' => $data,
            ]
        ];

        $result = $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);
    }

    /**
     * Validate booking payout
     * @param $bookingId
     */
    public function validate_payout($bookingId)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_EDIT_PAYMENT_STATUS, PERMISSION_BOOKING_OUT_EDIT_PAYMENT_STATUS], false);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('status', 'Status', 'trim|required|in_list[DATE,PENDING,REJECTED,APPROVED,PARTIAL APPROVED]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $payoutUntilDate = $this->input->post('payout_until_date');
                $status = $this->input->post('status');
                $description = $this->input->post('description');

                $booking = $this->booking->getBookingById($bookingId);

                $this->db->trans_start();

                if ($status == 'DATE') {
                    $this->booking->updateBooking([
                        'payout_until_date' => if_empty(format_date($payoutUntilDate), null),
                    ], $bookingId);
                    $status = $booking['status_payout'];
                } else {
                    if ($status == 'PENDING') {
                        $payoutUntilDate = null;
                    }
                    $this->booking->updateBooking([
                        'status_payout' => $status,
                        'payout_until_date' => if_empty(format_date($payoutUntilDate), null),
                    ], $bookingId);
                }

                $this->statusHistory->create([
                    'id_reference' => $bookingId,
                    'type' => StatusHistoryModel::TYPE_BOOKING_PAYMENT_STATUS,
                    'status' => $status,
                    'description' => $description . if_empty($payoutUntilDate, '', ' (', ')')
                ]);

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    $statusClass = 'warning';
                    if ($status == BookingModel::STATUS_APPROVED) {
                        $statusClass = 'success';
                    }

                    $emailTo = UserModel::authenticatedUserData('email');
                    $emailTitle = "payout booking " . $booking['no_booking'] . ' is ' . $status;
                    $emailTemplate = 'emails/basic';
                    $emailData = [
                        'title' => 'Booking Payout',
                        'name' => UserModel::authenticatedUserData('name'),
                        'email' => $emailTo,
                        'content' => 'Recently we review your booking that was created before. The result of booking payout no ' . $booking['no_booking'] . ' (' . $booking['no_reference'] . ') customer name ' . $booking['customer_name'] . ' is <b>' . $status . '</b>.<br><b>Note:</b> ' . $description
                    ];
                    $emailOptions = [
                        'cc' => get_setting('email_finance')
                    ];

                    $send = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);

                    if ($send) {
                        flash($statusClass, "Booking no <strong>{$booking['no_booking']}</strong> successfully <strong>{$status}</strong>");
                    } else {
                        flash('warning', "Booking no <strong>{$booking['no_booking']}</strong> successfully <strong>{$status}</strong> but email failed to be sent");
                    }
                } else {
                    flash('danger', "Validating booking no <strong>{$booking['no_booking']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('booking');
    }

    /**
     * Print booking data.
     * @param $bookingId
     */
    public function print_booking($bookingId)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_PRINT, PERMISSION_BOOKING_OUT_PRINT], false);

        $booking = $this->booking->getBookingById($bookingId);
        $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($bookingId);
        foreach ($bookingContainers as &$container) {
            $containerGoods = $this->bookingGoods->getBookingGoodsByBookingContainer($container['id']);
            $container['goods'] = $containerGoods;
        }
        $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($bookingId, true);
        $bookingExtensions = $this->bookingExtension->getBookingExtensionByBooking($bookingId);

        $barcode = new \Milon\Barcode\DNS2D();
        $barcode->setStorPath(APPPATH . "/cache/");
        $bookingBarcode = $barcode->getBarcodePNG($booking['no_booking'], "QRCODE", 5, 5);

        $data = [
            'title' => 'Print Booking',
            'page' => 'booking/print_booking',
            'booking' => $booking,
            'bookingGoods' => $bookingGoods,
            'bookingContainers' => $bookingContainers,
            'bookingExtensions' => $bookingExtensions,
            'bookingBarcode' => $bookingBarcode
        ];
        $this->load->view('template/print', $data);
    }

    /**
     * Create booking status.
     * @param $bookingId
     */
    public function status($bookingId)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_VIEW, PERMISSION_BOOKING_OUT_VIEW], false);

        $booking = $this->booking->getBookingById($bookingId);
        $bookingReferences = $this->bookingReference->getBy(['booking_references.id_booking' => $bookingId]);
        $bookingOut = $this->booking->getBookingOutByBookingIn($bookingId);
        $bookingStatuses = $this->bookingStatus->getBookingStatusesByBooking($bookingId);
        $paymentStatuses = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_BOOKING_PAYMENT_STATUS,
            'status_histories.id_reference' => $bookingId
        ]);
        $bcfStatuses = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_BOOKING_BCF_STATUS,
            'status_histories.id_reference' => $bookingId
        ]);
        $data = [
            'title' => "Booking",
            'subtitle' => "View booking safe conduct",
            'page' => "booking/status",
            'booking' => $booking,
            'bookingReferences' => $bookingReferences,
            'bookingOut' => $bookingOut,
            'bookingStatuses' => $bookingStatuses,
            'paymentStatuses' => $paymentStatuses,
            'bcfStatuses' => $bcfStatuses
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show cif booking data
     * @param $bookingId
     */
    public function cif($bookingId)
    {
        $cif = $this->bookingCIFInvoice->getBy(['booking_cif_invoices.id_booking' => $bookingId], true);
        $cifDetails = $this->bookingCIFInvoiceDetail->getBy(['booking_cif_invoice_details.id_booking_cif_invoice' => $cif['id']]);

        $booking = $this->booking->getBookingById($bookingId);
        $bookingOut = $this->booking->getBookingOutByBookingIn($bookingId);

        $cifOutbounds = [];
        foreach ($bookingOut as $outbound) {
            $cifOutbound = $this->bookingCIFInvoice->getBy(['booking_cif_invoices.id_booking' => $outbound['id']], true);
            if(!empty($cifOutbound)) {
                $cifOutbounds[] = $cifOutbound;
            }
        }
        $cifInbound = $this->bookingCIFInvoice->getBy(['booking_cif_invoices.id_booking' => $booking['id_booking']], true);

        $data = [
            'title' => "Booking CIF",
            'subtitle' => "View booking invoice",
            'page' => "booking/cif",
            'booking' => $booking,
            'bookingOut' => $bookingOut,
            'cif' => $cif,
            'cifInbound' => $cifInbound,
            'cifDetails' => $cifDetails,
            'cifOutbounds' => $cifOutbounds
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show safe conduct by booking.
     * @param $bookingId
     */
    public function safe_conduct($bookingId)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_VIEW, PERMISSION_BOOKING_OUT_VIEW], false);

        $booking = $this->booking->getBookingById($bookingId);
        $bookingReferences = $this->bookingReference->getBy(['booking_references.id_booking' => $bookingId]);
        $bookingOut = $this->booking->getBookingOutByBookingIn($bookingId);
        $safeConducts = $this->safeConduct->getSafeConductsByBooking($bookingId);
        $data = [
            'title' => "Booking",
            'subtitle' => "View booking safe conduct",
            'page' => "booking/safe_conduct",
            'booking' => $booking,
            'bookingReferences' => $bookingReferences,
            'bookingOut' => $bookingOut,
            'safeConducts' => $safeConducts,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * View booking's handling.
     * Get handling by booking.
     * @param $bookingId
     */
    public function handling($bookingId)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_VIEW, PERMISSION_BOOKING_OUT_VIEW], false);

        $booking = $this->booking->getBookingById($bookingId);
        $bookingReferences = $this->bookingReference->getBy(['booking_references.id_booking' => $bookingId]);
        $bookingOut = $this->booking->getBookingOutByBookingIn($bookingId);
        $handlings = $this->handling->getHandlingsByBooking($bookingId);
        $data = [
            'title' => "Booking",
            'subtitle' => "View booking handling",
            'page' => "booking/handling",
            'booking' => $booking,
            'bookingReferences' => $bookingReferences,
            'bookingOut' => $bookingOut,
            'handlings' => $handlings,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * View booking's handling.
     * Get handling by booking.
     * @param $bookingId
     */
    public function work_order($bookingId)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_VIEW, PERMISSION_BOOKING_OUT_VIEW], false);

        $booking = $this->booking->getBookingById($bookingId);
        $bookingReferences = $this->bookingReference->getBy(['booking_references.id_booking' => $bookingId]);
        $bookingOut = $this->booking->getBookingOutByBookingIn($bookingId);
        $workOrders = $this->workOrder->getWorkOrdersByBooking($bookingId);
        $data = [
            'title' => "Booking",
            'subtitle' => "View booking handling",
            'page' => "booking/work_order",
            'booking' => $booking,
            'bookingReferences' => $bookingReferences,
            'bookingOut' => $bookingOut,
            'workOrders' => $workOrders,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * View booking's payment.
     * Get payment by booking.
     * @param $bookingId
     */
    public function payment($bookingId)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_VIEW, PERMISSION_BOOKING_OUT_VIEW], false);

        $booking = $this->booking->getBookingById($bookingId);
        $bookingReferences = $this->bookingReference->getBy(['booking_references.id_booking' => $bookingId]);
        $bookingOut = $this->booking->getBookingOutByBookingIn($bookingId);
        $paymentNonBillings = $this->payment->getPaymentsByBooking($bookingId, PaymentModel::PAYMENT_NON_BILLING);
        $paymentBillings = $this->payment->getPaymentsByBooking($bookingId, PaymentModel::PAYMENT_BILLING);
        
        $data = [
            'title' => "Booking",
            'subtitle' => "View booking payment",
            'page' => "booking/payment",
            'booking' => $booking,
            'bookingReferences' => $bookingReferences,
            'bookingOut' => $bookingOut,
            'paymentNonBillings' => $paymentNonBillings,
            'paymentBillings' => $paymentBillings,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * View booking's invoice.
     * Get invoice by booking.
     * @param $bookingId
     */
    public function invoice($bookingId)
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_VIEW, PERMISSION_BOOKING_OUT_VIEW], false);

        $booking = $this->booking->getBookingById($bookingId);
        $bookingReferences = $this->bookingReference->getBy(['booking_references.id_booking' => $bookingId]);
        $bookingOut = $this->booking->getBookingOutByBookingIn($bookingId);
        $invoices = $this->invoice->getInvoicesByNoReference($booking['no_booking']);
        $data = [
            'title' => "Booking",
            'subtitle' => "View booking invoice",
            'page' => "booking/invoice",
            'booking' => $booking,
            'bookingReferences' => $bookingReferences,
            'bookingOut' => $bookingOut,
            'invoices' => $invoices,
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Ajax get all people data
     */
    public function ajax_get_booking_by_keyword()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $type = $this->input->get('type');
            $search = $this->input->get('q');
            $page = $this->input->get('page');
            $owner = $this->input->get('owner');

            $bookings = $this->booking->getBookingByKeyword($type, $search, $page, $owner);

            header('Content-Type: application/json');
            echo json_encode($bookings);
        }
    }

    /**
     * Get single booking information by id
     */
    public function ajax_get_booking_by_id()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $bookingId = $this->input->get('id_booking');
            $booking = $this->booking->getBookingById($bookingId);
            header('Content-Type: application/json');
            echo json_encode($booking);
        }
    }

    /**
     * Get all booking by specific type
     */
    public function ajax_get_booking_by_type()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $bookingTypeId = $this->input->get('id_booking_type');
            $bookings = $this->booking->getBookingsByBookingType($bookingTypeId);
            header('Content-Type: application/json');
            echo json_encode($bookings);
        }
    }

    /**
     * Get all booking by specific category
     */
    public function ajax_get_booking_by_category()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $bookingCategory = $this->input->get('category');
            $bookingStatus = $this->input->get('status');
            $bookings = $this->booking->getAllBookings([
                'category' => $bookingCategory,
                'status' => explode(',', $bookingStatus)
            ]);
            header('Content-Type: application/json');
            echo json_encode($bookings);
        }
    }

    /**
     * Get booking by booking customer (STOCK).
     */
    public function ajax_get_stock_booking_by_customer()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $customerId = $this->input->get('id_customer');
            $bookingId = $this->input->get('id_booking');
            $bookings = $this->booking->getBookingStocksByCustomer($customerId, $bookingId);
            header('Content-Type: application/json');
            echo json_encode($bookings);
        }
    }

    /**
     * Get all booking by specific id upload
     */
    public function ajax_get_booking_in_by_id_upload()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $uploadId = $this->input->get('id_upload');
            $upload = $this->upload->getById($uploadId);
            $uploadReferences = $this->uploadReference->getBy(['upload_references.id_upload' => $uploadId]);
            $bookings = $this->booking->getBookingsByConditions([
                'bookings.id_upload' => if_empty($upload['id_upload'], if_empty(array_column($uploadReferences, 'id_upload_reference'), [-1]))
            ]);

            header('Content-Type: application/json');
            echo json_encode($bookings);
        }
    }

    /**
     * Get booking detail.
     */
    public function ajax_get_booking_detail()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $bookingId = $this->input->get('id');
            $goodsOnly = $this->input->get('goods_only');
            $booking = $this->booking->getBookingById($bookingId);
            $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($bookingId);
            foreach ($bookingContainers as &$container) {
                $containerGoods = $this->bookingGoods->getBookingGoodsByBookingContainer($container['id']);
                $container['goods'] = $containerGoods;
            }
            $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($bookingId, true);

            header('Content-Type: application/json');
            echo json_encode([
                'booking' => $booking,
                'containers' => $goodsOnly ? [] : $bookingContainers,
                'goods' => $bookingGoods,
            ]);
        }
    }

    /**
     * Check if booking data is already converted to handling, and all job related is completed.
     */
    public function ajax_check_booking_ready_to_complete()
    {
        $bookingId = $this->input->get('id_booking');
        $category = $this->input->get('category');

        $booking = $this->booking->getBookingById($bookingId);

        // find out if all booking data is converted to handling already
        $bookingContainers = $this->bookingContainer->getBookingContainersByBookingHandling($bookingId, $category);
        $bookingGoods = $this->bookingGoods->getBookingGoodsByBookingHandling($bookingId, true, $category);
        $bookingConvertedToHandlings = empty($bookingContainers) && empty($bookingGoods);

        // check if all booking is completed, validated and gate out already
        $workOrders = $this->workOrder->getWorkOrdersByBooking($bookingId);
        $bookingJobCompleted = true;
        $bookingJobGate = true;
        $bookkingJobValidated = true;
        if (empty($workOrders)) $bookingJobGate = false;
        foreach ($workOrders as $workOrder) {
            if ($workOrder['status'] != WorkOrderModel::STATUS_COMPLETED) {
                $bookingJobCompleted = false;
            }
            if (empty($workOrder['gate_in_date']) || empty($workOrder['gate_out_date'])) {
                $bookingJobGate = false;
            }
            if ($workOrder['status_validation'] !== WorkOrderModel::STATUS_VALIDATION_VALIDATED) {
                $bookkingJobValidated = false;
            }
            if (!$bookingJobCompleted || !$bookingJobGate || !$bookkingJobValidated) {
                break;
            }
        }

        // check if all safe conduct is checked in security already
        $safeConductCompleted = true;
        $safeConductAttachment = true;
        $safeConducts = $this->safeConduct->getSafeConductsByBooking($bookingId);
        if (empty($safeConducts)) {
            $safeConductCompleted = false;
            $safeConductAttachment = false;
        }
        foreach ($safeConducts as $safeConduct) {
            if (empty($safeConduct['security_in_date']) || empty($safeConduct['security_out_date'])) {
                $safeConductCompleted = false;
                break;
            }
        }
        if ($category == 'OUTBOUND' && $booking['created_at'] >= '2019-11-16 00:00:01') {
            foreach ($safeConducts as $safeConduct) {
                $safeConductAttachments = $this->safeConductAttachment->getBy([
                    'safe_conduct_attachments.id_safe_conduct' => $safeConduct['id']
                ]);
                if (empty($safeConductAttachments)) {
                    $safeConductAttachment = false;
                    break;
                }
            }
        }

        // check if job has difference with booking
        $diffData = $this->booking->getDifferenceBookingToWorkOrder($bookingId);
        $isDifferent = $diffData['stock_booking_containers'] != 0 || $diffData['stock_booking_goods'] != 0;

        // check if booking is discrepancy with stock
        $stockDiscrepancies = $category == 'INBOUND' ? $this->bookingGoods->getStockDiscrepancy($bookingId) : [];

        $result = [
            'id_booking' => $bookingId,
            'category' => $category,
            'handling_converted' => $bookingConvertedToHandlings,
            'handling_outstanding_data' => [
                'containers' => $bookingContainers,
                'goods' => $bookingGoods,
            ],
            'work_order_completed' => $bookingJobCompleted,
            'work_order_gate_checked' => $bookingJobGate,
            'work_order_validated' => $bookkingJobValidated,
            'safe_conduct_security_checked' => $safeConductCompleted,
            'safe_conduct_attachment' => $safeConductAttachment,
            'different_booking_to_work_order' => $isDifferent,
            'different_data' => [
                'containers' => $diffData['stock_booking_containers'],
                'goods' => $diffData['stock_booking_goods'],
            ],
            'stock_discrepancies' => $stockDiscrepancies
        ];

        header('Content-Type: application/json');
        $this->output
            ->set_content_type('application/json')
            ->set_output(json_encode($result));
    }

    /**
     * Ajax get all goods booking data
     */
    public function ajax_get_goods_by_name()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $bookingId = $this->input->get('booking_id');
            $search = $this->input->get('q');
            $page = $this->input->get('page');

            $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($bookingId, true);
            $goods = [];
            foreach ($bookingGoods as $bookingGood) {
                $item = $this->goods->getById($bookingGood['id_goods']);
                if(!empty($item)) {
                    $item['status'] = $bookingGood['status'];
                    $item['status_danger'] = $bookingGood['status_danger'];
                    $item['is_hold'] = $bookingGood['is_hold'];
                    $item['unit'] = $bookingGood['unit'];
                    $item['id_unit'] = $bookingGood['id_unit'];
                    $item['unit_weight'] = $bookingGood['unit_weight'];
                    $item['unit_gross_weight'] = $bookingGood['unit_gross_weight'];
                    $item['unit_length'] = $bookingGood['unit_length'];
                    $item['unit_width'] = $bookingGood['unit_width'];
                    $item['unit_height'] = $bookingGood['unit_height'];
                    $item['unit_volume'] = $bookingGood['unit_volume'];
                    $goods[] = $item;
                }
            }

            echo json_encode([
                'results' => $goods,
                'total_count' => count($goods)
            ]);
        }
    }

    /**
     * Get status hold by booking customer.
     * @param $bookingId
     */
    public function ajax_get_status_hold_by($bookingId)
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $holdByInvoice = if_empty($this->input->get('hold_by_invoice'), 0);

            $booking = $this->booking->getBookingById($bookingId);

            $isHold = false;
            $message = '';
            if ($holdByInvoice) {
                if (strpos(get_active_branch('branch'), 'TPP') !== false) {
                    $invoiceBooking = $this->invoice->getInvoicesByNoReference($booking['no_booking'], 'PUBLISHED');
                    if (empty($invoiceBooking)) {
                        $isHold = true;
                        $message = 'Published invoice is required to perform this action!';
                    }
                }
            }
            header('Content-Type: application/json');
            echo json_encode([
                'hold' => $isHold,
                'message' => $message
            ]);
        }
    }

    /**
     * Check status invoice
     */
    public function ajax_check_status_invoice()
    {
        header('Content-Type: application/json');
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $bookingId = $this->input->get('id_booking');

            $booking = $this->booking->getBookingById($bookingId);

            if (get_active_branch('branch_type') == 'TPP') {
                $invoiceBooking = $this->invoice->getInvoicesByNoReference($booking['no_booking'], 'PUBLISHED');
                if (empty($invoiceBooking)) {
                    echo json_encode([
                        'status' => 0
                    ]);
                } else {
                    echo json_encode([
                        'status' => 1,
                        'invoice' => $invoiceBooking
                    ]);
                }
                die();
            }
        }
        echo json_encode([
            'status' => 1
        ]);
    }
}
