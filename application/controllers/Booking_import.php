<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Booking_import
 * @property BranchModel $branch
 * @property BookingModel $booking
 * @property BookingGoodsModel $bookingGoods
 * @property BookingContainerModel $bookingContainer
 * @property BookingTypeModel $bookingType
 * @property BookingExtensionModel $bookingExtension
 * @property BookingStatusModel $bookingStatus
 * @property ContainerModel $container
 * @property GoodsModel $goods
 * @property AssemblyModel $assembly
 * @property AssemblyGoodsModel $assemblyGoods
 * @property ExtensionFieldModel $extensionField
 * @property UnitModel $unit
 * @property UploadModel $uploadModel
 * @property UploadDocumentModel $uploadDocument
 * @property PeopleModel $people
 * @property OpnameModel $opname
 * @property ReportStockModel $reportStock
 * @property Uploader $uploader
 */
class Booking_import extends MY_Controller
{
    /**
     * Booking_in_import constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ModuleModel', 'module');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('BookingTypeModel', 'bookingType');
        $this->load->model('BookingStatusModel', 'bookingStatus');
        $this->load->model('BookingExtensionModel', 'bookingExtension');
        $this->load->model('ExtensionFieldModel', 'extensionField');
        $this->load->model('UploadDocumentModel', 'uploadDocument');
        $this->load->model('UploadModel', 'uploadModel');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('ContainerModel', 'container');
        $this->load->model('GoodsModel', 'goods');
        $this->load->model('AssemblyModel', 'assembly');
        $this->load->model('AssemblyGoodsModel', 'assemblyGoods');
        $this->load->model('UnitModel', 'unit');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('UserModel', 'user');
        $this->load->model('ReportStockModel', 'reportStock');
        $this->load->model('OpnameModel', 'opname');
        $this->load->model('BranchModel', 'branch');
        $this->load->model('modules/Uploader', 'uploader');

        $this->setFilterMethods([
            'upload' => 'POST',
            'preview' => 'GET',
            'get_stock_import' => 'GET',
            'import_save' => 'POST',
        ]);
    }

    /**
     * Show create booking import.
     * @throws Exception
     */
    public function create()
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
                if ($date_diff->format("%a") > $branch['opname_pending_day']) {
                    $opnamePendingDay = true;
                    break;
                }
            }
        }

        if (!empty($opnameProcessDates)) {
            foreach ($opnameProcessDates as $opnameProcessDate) {
                $date_diff = date_diff(new DateTime(), new Datetime($opnameProcessDate));
                if ($date_diff->format("%a") > $branch['opname_pending_day']) {
                    $opnamePendingDay = true;
                    break;
                }
            }
        }

        if (!empty($opnameRejectDates)) {
            foreach ($opnameRejectDates as $opnameRejectDate) {
                $date_diff = date_diff(new DateTime(), new Datetime($opnameRejectDate));
                if ($date_diff->format("%a") > $branch['opname_pending_day']) {
                    $opnamePendingDay = true;
                    break;
                }
            }
        }

        if ($opnamePendingDay == true) {
            redirect("booking");
        } else {
            $data = [
                'title' => "Bookings",
                'subtitle' => "Import booking",
                'page' => "booking_import/create",
            ];
            $this->load->view('template/layout', $data);
        }
    }

    /**
     * Upload data xml to temporary.
     */
    public function upload()
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_CREATE, PERMISSION_BOOKING_OUT_CREATE], false);

        $category = $this->input->post('category');
        $createPackage = $this->input->post('create_package');
        $bookingIn = $this->input->post('booking_in');
        $upload = $this->input->post('document');

        $uploadDoc = $this->uploadModel->getById($upload);

        $ext = pathinfo($_FILES['xml_document']['name'], PATHINFO_EXTENSION);
        $fileName = uniqid() . '.' . $ext;
        if ($this->uploader->uploadTo('xml_document', ['file_name' => $fileName])) {
            $filePath = FCPATH . 'uploads/temp/' . $fileName;
            $isFileExist = file_exists($filePath);

            if ($isFileExist) {
                $xml = simplexml_load_file($filePath);
                if (!empty($xml)) {
                    $parsedData = json_decode(json_encode($xml), true);
                    $header = $parsedData['header'];

                    $db = 'plbtranscon_tpbdb';
                    if ($category == 'INBOUND') {
                        if ($this->db->query('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "plbtranscon_tpbdb"')->result_array()) {
                            $db = 'plbtranscon_tpbdb';
                        } else if ($this->db->query('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "bc_tpbdb"')->result_array()) {
                            $db = 'bc_tpbdb';
                        }
                    } else {
                        if ($this->db->query('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "plbtranscon_plbdb"')->result_array()) {
                            $db = 'plbtranscon_plbdb';
                        } else if ($this->db->query('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "bc_plbdb"')->result_array()) {
                            $db = 'bc_plbdb';
                        }
                    }

                    $this->db->trans_start();

                    // create package and unit package
                    if ($createPackage) {
                        $totalPackage = $header['kemasan']['jumlahKemasan'];
                        $packageName = $header['kemasan']['kodeJenisKemasan'];

                        $package = $this->db->from($db . '.referensi_kemasan')
                            ->where('KODE_KEMASAN', $packageName)
                            ->get()
                            ->row_array();

                        if (!empty($package)) {
                            $packageName = $package['URAIAN_KEMASAN'];
                        }

                        $foundUnitPackage = $this->unit->getBy(['unit' => $packageName], true);
                        if (empty($foundUnitPackage)) {
                            $this->unit->create([
                                'unit' => $packageName,
                                'description' => $packageName,
                            ]);
                        }

                        for ($i = 0; $i < $totalPackage; $i++) {
                            $noGoods = $header['nomorAju'] . ' / ' . $header['kemasan']['kodeJenisKemasan'] . ' / ' . ($i + 1);
                            $itemPackage = $this->goods->getBy(['ref_goods.no_goods' => $noGoods], true);
                            if (empty($itemPackage)) {
                                $this->goods->create([
                                    'id_customer' => $uploadDoc['id_person'],
                                    'no_goods' => $noGoods,
                                    'name' => substr($header['nomorAju'], -6) . ' / ' . $packageName . ' / ' . ($i + 1),
                                    'no_hs' => '',
                                    'type_goods' => $packageName,
                                    'unit_length' => 0,
                                    'unit_width' => 0,
                                    'unit_height' => 0,
                                    'unit_volume' => 0,
                                    'unit_weight' => 0,
                                    'unit_gross_weight' => 0,
                                ]);
                            }
                        }
                    }

                    if (!key_exists('0', $header['barang']) || !is_array($header['barang'][0])) {
                        $header['barang'] = [$header['barang']];
                    }
                    foreach ($header['barang'] as $item) {
                        // create goods if not exist
                        $noGoods = $header['nomorAju'] . '-' . $item['seriBarang'];
                        $foundGoods = $this->goods->getBy(['no_goods' => $noGoods], true);
                        if (!$foundGoods) {
                            $this->goods->create([
                                'id_customer' => $uploadDoc['id_person'],
                                'no_goods' => $noGoods,
                                'name' => trim($item['uraian'] . ' ' . if_empty($item['merk']) . ' ' . if_empty($item['tipe']) . ' ' . if_empty($item['ukuran'])),
                                'no_hs' => '',
                                'type_goods' => if_empty($item['tipe']),
                                'unit_length' => 0,
                                'unit_width' => 0,
                                'unit_height' => 0,
                                'unit_volume' => 0,
                                'unit_weight' => $item['netto'] / if_empty($item['jumlahSatuan'], 1),
                                'unit_gross_weight' => 0,
                            ]);
                        }

                        // create unit if not exist
                        $unitType = $this->db->from($db . '.referensi_satuan')
                            ->where('KODE_SATUAN', $item['kodeSatuan'])
                            ->get()
                            ->row_array();
                        if (!empty($unitType)) {
                            $unit = $unitType['URAIAN_SATUAN'];
                            $foundUnit = $this->unit->getBy(['unit' => $unit], true);
                            if (empty($foundUnit)) {
                                $this->unit->create([
                                    'unit' => $unit,
                                    'description' => $unit,
                                ]);
                            }
                        } else {
                            $foundUnit = $this->unit->getBy(['unit' => $item['kodeSatuan']], true);
                            if (empty($foundUnit)) {
                                $this->unit->create([
                                    'unit' => $item['kodeSatuan'],
                                    'description' => $item['kodeSatuan'],
                                ]);
                            }
                        }
                    }
                    $this->db->trans_complete();

                    if (!$this->db->trans_status()) {
                        flash('danger', 'Create package or goods failed, please upload again');
                    }
                }

                redirect('booking-import/preview?import=1&upload=' . $upload . '&category=' . $category . '&booking_in=' . $bookingIn . '&create_package=' . $createPackage . '&file=' . base64_encode($fileName));
            } else {
                flash('danger', 'File is not found, please upload again');
            }
        } else {
            flash('danger', $this->uploader->getDisplayErrors());
        }
        redirect('booking-import/create');
    }

    /**
     * Get stock from xml data.
     */
    public function get_stock_import()
    {
        $file = base64_decode($this->input->get('file'));
        $xml = simplexml_load_file(FCPATH . 'uploads/temp/' . $file);
        if (!empty($xml)) {
            $parsedData = json_decode(json_encode($xml), true);
            $header = $parsedData['header'];

            if ($this->db->query('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "plbtranscon_tpbdb"')->result_array()) {
                $db = 'plbtranscon_tpbdb';
            } else if ($this->db->query('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "bc_tpbdb"')->result_array()) {
                $db = 'bc_tpbdb';
            }

            $goods = [];
            if (!key_exists('0', $header['barang']) || !is_array($header['barang'][0])) {
                $header['barang'] = [$header['barang']];
            }
            foreach ($header['barang'] as $item) {
                $noGoods = $header['nomorAju'] . '-' . $item['seriBarang'];
                $foundGoods = $this->goods->getBy(['no_goods' => $noGoods], true);

                $unitId = '';
                $unit = $item['kodeSatuan'];
                if (!empty($db)) {
                    $unitType = $this->db->from($db . '.referensi_satuan')
                        ->where('KODE_SATUAN', $item['kodeSatuan'])
                        ->get()
                        ->row_array();
                    if (!empty($unitType)) {
                        $foundUnit = $this->unit->getBy(['unit' => $unitType['URAIAN_SATUAN']], true);
                        if (!empty($foundUnit)) {
                            $unitId = $foundUnit['id'];
                            $unit = $unitType['URAIAN_SATUAN'];
                        } else {
                            $foundUnit = $this->unit->getBy(['unit' => $item['kodeSatuan']], true);
                            $unitId = $foundUnit['id'];
                            $unit = $foundUnit['unit'];
                        }
                    }
                }

                if ($foundGoods) {
                    $goods[] = [
                        'id_owner' => $foundGoods['id_customer'],
                        'owner_name' => $foundGoods['customer_name'],
                        'id_booking' => -1,
                        'no_reference' => $header['nomorAju'],
                        'id_goods' => $foundGoods['id'],
                        'no_goods' => $foundGoods['no_goods'],
                        'goods_name' => $foundGoods['name'],
                        'id_unit' => $unitId,
                        'unit' => $unit,
                        'stock_quantity' => $item['jumlahSatuan'],
                        'unit_weight' => $foundGoods['unit_weight'],
                        'stock_weight' => $item['jumlahSatuan'] * $foundGoods['unit_weight'],
                        'unit_gross_weight' => $foundGoods['unit_gross_weight'],
                        'stock_gross_weight' => $item['jumlahSatuan'] * $foundGoods['unit_gross_weight'],
                        'unit_length' => $foundGoods['unit_length'],
                        'unit_width' => $foundGoods['unit_width'],
                        'unit_height' => $foundGoods['unit_height'],
                        'unit_volume' => $foundGoods['unit_volume'],
                        'stock_volume' => $item['jumlahSatuan'] * $foundGoods['unit_volume'],
                        'ex_no_container' => '',
                        'no_pallet' => '',
                        'id_position' => '',
                        'id_position_block' => '',
                        'is_hold' => 0,
                        'status' => 'GOOD',
                        'status_danger' => 'NOT DANGER',
                        'description' => '',
                    ];
                }
            }

            $this->render_json([
                'booking' => [
                    'id' => '-1',
                    'no_reference' => $header['nomorAju'],
                    'customer_name' => $header['namaPemilik'],
                ],
                'containers' => [],
                'goods' => $goods,
            ]);
        } else {
            $this->render_json([]);
        }
    }

    /**
     * Show preview data
     */
    public function preview()
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_CREATE, PERMISSION_BOOKING_OUT_CREATE], false);

        $uploadId = $this->input->get('upload');
        $bookingInId = $this->input->get('booking_in');
        $createPackage = $this->input->get('create_package');
        $file = base64_decode($this->input->get('file'));
        $xml = simplexml_load_file(FCPATH . 'uploads/temp/' . $file);
        if (!empty($xml)) {
            $parsedData = json_decode(json_encode($xml), true);
            $header = $parsedData['header'];

            if (!empty($this->booking->getBookingsByConditions(['TRIM(bookings.no_reference)' => $header['nomorAju']]))) {
                flash('danger', 'Booking with no reference ' . $header['nomorAju'] . ' is already exist!', '_back');
            }

            $bookingIn = $this->booking->getBookingById($bookingInId);
            $upload = $this->uploadModel->getById($uploadId);
            $uploadDocuments = $this->uploadDocument->getDocumentsByUpload($upload['id']);
            $bookingType = $this->bookingType->getById($upload['id_booking_type']);
            $referenceDate = '';
            foreach ($uploadDocuments as $document) {
                if ($document['id_document_type'] == $bookingType['id_document_type']) {
                    $referenceDate = $document['document_date'];
                    break;
                }
            }
            $customer = $this->people->getById($upload['id_person']);
            $supplier = $this->people->getBy([
                'ref_people.name' => get_if_exist($header, 'namaPemasok'),
                'ref_people.type' => PeopleModel::$TYPE_SUPPLIER,
            ], true);

            if (empty($supplier)) {
                if (key_exists('namaPemasok', $header)) {
                    $supplier = [
                        'id' => '0',
                        'name' => get_if_exist($header, 'namaPemasok'),
                        'address' => get_if_exist($header, 'alamatPemasok'),
                        'no_person' => 'CREATE NEW'
                    ];
                } else {
                    $supplier = [];
                }
            }

            $booking = [
                'category' => $upload['category'],
                'id_booking_type' => $upload['id_booking_type'],
                'booking_type' => $upload['booking_type'],
                'booking_date' => date('d F Y H:i'),
                'id_upload' => $upload['id'],
                'no_upload' => $upload['no_upload'],
                'id_customer' => $upload['id_person'],
                'customer_name' => $upload['name'],
                'id_supplier' => get_if_exist($supplier, 'id'),
                'supplier_name' => get_if_exist($supplier, 'name'),
                'supplier_address' => get_if_exist($supplier, 'address'),
                'no_reference' => $header['nomorAju'],
                'reference_date' => $referenceDate,
                'vessel' => get_if_exist($header, 'namaPengangkut'),
                'voyage' => get_if_exist($header, 'nomorVoyFlight'),
                'document_status' => '',
                'description' => '',
                'netto' => 0,
                'bruto' => 0,
            ];

            $bl = $this->uploadDocument->getDocumentsByUploadByDocumentType($upload['id'], 6);

            $db = 'plbtranscon_tpbdb';
            if ($upload['category'] == 'INBOUND') {
                if ($this->db->query('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "plbtranscon_tpbdb"')->result_array()) {
                    $db = 'plbtranscon_tpbdb';
                } else if ($this->db->query('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "bc_tpbdb"')->result_array()) {
                    $db = 'bc_tpbdb';
                }
            } else {
                if ($this->db->query('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "plbtranscon_plbdb"')->result_array()) {
                    $db = 'plbtranscon_plbdb';
                } else if ($this->db->query('SELECT SCHEMA_NAME FROM INFORMATION_SCHEMA.SCHEMATA WHERE SCHEMA_NAME = "bc_plbdb"')->result_array()) {
                    $db = 'bc_plbdb';
                }
            }

            $dockD = null;
            $dockS = null;
            if (!empty($db)) {
                $dockD = $this->db->from($db . '.referensi_pelabuhan')
                    ->where('KODE_PELABUHAN', get_if_exist($header, 'kodePelBongkar'))
                    ->get()
                    ->row_array();
                $dockS = $this->db->from($db . '.referensi_pelabuhan')
                    ->where('KODE_PELABUHAN', get_if_exist($header, 'kodePelMuat'))
                    ->get()
                    ->row_array();
            }

            $noRegistration = '';
            $registrationDate = '';
            $dockDestination = !empty($dockD) ? $dockD['URAIAN_PELABUHAN'] : '';
            $dockSource = !empty($dockS) ? $dockS['URAIAN_PELABUHAN'] : '';
            $eta = get_if_exist($header, 'tanggalTiba');
            $noBL = !empty($bl) ? $bl['no_document'] : '';
            $bc11 = get_if_exist($header, 'nomorBc11');
            $bc11Date = get_if_exist($header, 'tanggalBc11');
            $pos = get_if_exist($header, 'posBc11');
            $cif_no = get_if_exist($header, 'cif');

            $extensionFields = $this->extensionField->getByBookingType($upload['id_booking_type']);
            $extensionFields = array_map(function ($extensionField) {
                if (!in_array($extensionField['id'], [22, 21, 24])) { // NOPEN, TGL, CIF NO
                    $extensionField['option'] = '{"readonly":true}';
                }
                return $extensionField;
            }, $extensionFields);

            $bookingExtensions = [
                [
                    'id_extension_field' => 22,
                    'value' => $noRegistration,
                ],
                [
                    'id_extension_field' => 21,
                    'value' => $registrationDate,
                ],
                [
                    'id_extension_field' => 7,
                    'value' => $dockDestination,
                ],
                [
                    'id_extension_field' => 6,
                    'value' => $dockSource,
                ],
                [
                    'id_extension_field' => 5,
                    'value' => $eta,
                ],
                [
                    'id_extension_field' => 4,
                    'value' => $noBL,
                ],
                [
                    'id_extension_field' => 2,
                    'value' => $bc11,
                ],
                [
                    'id_extension_field' => 3,
                    'value' => $bc11Date,
                ],
                [
                    'id_extension_field' => 1,
                    'value' => $pos,
                ],
                [
                    'id_extension_field' => 24,
                    'value' => $cif_no,
                ],
            ];
            $extensions = $this->load->view('extension_field/_extensions', [
                'extensionFields' => $extensionFields,
                'bookingExtensions' => $bookingExtensions
            ], true);

            $containers = [];
            $goods = [];

            if (!empty($header['kontainer'])) {
                if (!key_exists('0', $header['kontainer']) || !is_array($header['kontainer'][0])) {
                    $header['kontainer'] = [$header['kontainer']];
                }
                foreach ($header['kontainer'] as $container) {
                    $foundContainer = $this->container->getBy(['no_container' => $container['nomorKontainer']], true);
                    $containers[] = [
                        'id_shipping_line' => empty($foundContainer) ? '' : $foundContainer['id_shipping_line'],
                        'shipping_line' => empty($foundContainer) ? '' : $foundContainer['shipping_line'],
                        'id_container' => empty($foundContainer) ? '' : $foundContainer['id'],
                        'no_container' => empty($foundContainer) ? $container['nomorKontainer'] : $foundContainer['no_container'],
                        'size' => empty($foundContainer) ? '' : $foundContainer['size'],
                        'type' => empty($foundContainer) ? '' : $foundContainer['type'],
                        'seal' => '',
                        'position' => '',
                        'id_position' => '',
                        'id_position_blocks' => '',
                        'length_payload' => 0,
                        'width_payload' => 0,
                        'height_payload' => 0,
                        'volume_payload' => 0,
                        'is_empty' => 0,
                        'is_hold' => 0,
                        'status' => 'GOOD',
                        'status_danger' => 'NOT DANGER',
                        'description' => ''
                    ];
                }
            }

            if (!empty($header['barang'])) {
                if (!key_exists('0', $header['barang']) || !is_array($header['barang'][0])) {
                    $header['barang'] = [$header['barang']];
                }
                if ($createPackage) {
                    $totalPackage = $header['kemasan']['jumlahKemasan'];
                    $packageName = $header['kemasan']['kodeJenisKemasan'];

                    $package = $this->db->from($db . '.referensi_kemasan')
                        ->where('KODE_KEMASAN', $packageName)
                        ->get()
                        ->row_array();

                    if (!empty($package)) {
                        $packageName = $package['URAIAN_KEMASAN'];
                    }

                    $foundUnitPackage = $this->unit->getBy(['unit' => $packageName], true);
                    if (empty($foundUnitPackage)) {
                        $this->unit->create([
                            'unit' => $packageName,
                            'description' => $packageName,
                        ]);
                        $packageUnitId = $this->db->insert_id();
                    } else {
                        $packageUnitId = $foundUnitPackage['id'];
                    }

                    for ($i = 0; $i < $totalPackage; $i++) {
                        $noGoods = $header['nomorAju'] . ' / ' . $header['kemasan']['kodeJenisKemasan'] . ' / ' . ($i + 1);
                        $itemPackage = $this->goods->getBy(['ref_goods.no_goods' => $noGoods], true);
                        if (!empty($itemPackage)) {
                            $goods[] = [
                                'id_goods' => $itemPackage['id'],
                                'no_goods' => $itemPackage['no_goods'],
                                'goods_name' => $itemPackage['name'],
                                'type_goods' => $itemPackage['type_goods'],
                                'no_hs' => $itemPackage['no_hs'],
                                'quantity' => 1,
                                'id_unit' => $packageUnitId,
                                'unit' => $packageName,
                                'id_position' => '',
                                'id_position_blocks' => '',
                                'ex_no_container' => '',
                                'unit_weight' => 0,
                                'unit_gross_weight' => 0,
                                'unit_volume' => 0,
                                'unit_length' => 0,
                                'unit_width' => 0,
                                'unit_height' => 0,
                                'position' => '',
                                'no_pallet' => '',
                                'is_hold' => 0,
                                'status' => 'GOOD',
                                'status_danger' => 'NOT DANGER',
                                'description' => ''
                            ];
                        }
                    }
                } else {
                    if (!key_exists('0', $header['barang']) || !is_array($header['barang'][0])) {
                        $header['barang'] = [$header['barang']];
                    }
                    foreach ($header['barang'] as $item) {
                        $foundGoods = $this->goods->getBy(['no_goods' => $header['nomorAju'] . '-' . $item['seriBarang']], true);

                        $unitId = '';
                        $unit = $item['kodeSatuan'];
                        if (!empty($db)) {
                            $unitType = $this->db->from($db . '.referensi_satuan')
                                ->where('KODE_SATUAN', $item['kodeSatuan'])
                                ->get()
                                ->row_array();
                            if (!empty($unitType)) {
                                $unit = $unitType['URAIAN_SATUAN'];
                                $foundUnit = $this->unit->getBy(['unit' => $unit], true);
                                if (!empty($foundUnit)) {
                                    $unitId = $foundUnit['id'];
                                }
                            }
                        }
                        $goods[] = [
                            'id_goods' => empty($foundGoods) ? '' : $foundGoods['id'],
                            'no_goods' => empty($foundGoods) ? ($header['nomorAju'] . '-' . $item['seriBarang']) : $foundGoods['no_goods'],
                            'goods_name' => empty($foundGoods) ? trim($item['uraian'] . ' ' . if_empty($item['merk']) . ' ' . if_empty($item['tipe']) . ' ' . if_empty($item['ukuran'])) : $foundGoods['name'],
                            'type_goods' => empty($foundGoods) ? '' : $foundGoods['type_goods'],
                            'no_hs' => empty($foundGoods) ? '' : $foundGoods['no_hs'],
                            'quantity' => $item['jumlahSatuan'],
                            'id_unit' => $unitId,
                            'unit' => $unit,
                            'whey_number' => '',
                            'id_position' => '',
                            'id_position_blocks' => '',
                            'ex_no_container' => '',
                            'unit_weight' => $item['netto'] / if_empty($item['jumlahSatuan'], 1),
                            'unit_gross_weight' => 0,
                            'unit_volume' => 0,
                            'unit_length' => 0,
                            'unit_width' => 0,
                            'unit_height' => 0,
                            'position' => '',
                            'no_pallet' => '',
                            'is_hold' => 0,
                            'status' => 'GOOD',
                            'status_danger' => 'NOT DANGER',
                            'description' => ''
                        ];
                        // calculate total netto
                        $booking['netto'] += if_empty($item['netto'],0);
                    }
                }
            }

            // check if the item or container meet the requirement
            $errorMessages = [];
            if ($upload['category'] == 'OUTBOUND') {
                if (!empty($containers)) {
                    $stockContainers = $this->reportStock->getStockContainers([
                        'data' => 'stock',
                        'booking' => $bookingInId
                    ]);

                    foreach ($containers as $index => &$container) {
                        $isFound = false;
                        $foundStock = null;
                        foreach ($stockContainers as $stockContainer) {
                            if ($container['id_container'] == $stockContainer['id_container']) {
                                $isFound = true;
                                $foundStock = $stockContainer;
                                break;
                            }
                        }
                        if ($isFound) {
                            $container = [
                                'id_shipping_line' => $container['id_shipping_line'],
                                'shipping_line' => $container['shipping_line'],
                                'id_container' => $container['id_container'],
                                'no_container' => $container['no_container'],
                                'size' => $container['size'],
                                'type' => $container['type'],
                                'seal' => $foundStock['seal'],
                                'position' => $foundStock['position'],
                                'id_position' => if_empty($foundStock['id_position'], null),
                                'id_position_blocks' => $foundStock['id_position_blocks'],
                                'length_payload' => get_if_exist($foundStock, 'length_payload'),
                                'width_payload' => get_if_exist($foundStock, 'width_payload'),
                                'height_payload' => get_if_exist($foundStock, 'height_payload'),
                                'volume_payload' => get_if_exist($foundStock, 'volume_payload'),
                                'is_empty' => $foundStock['is_empty'],
                                'is_hold' => $foundStock['is_hold'],
                                'status' => $foundStock['status'],
                                'status_danger' => $foundStock['status_danger'],
                                'description' => $foundStock['description']
                            ];
                        } else {
                            unset($containers[$index]);
                            //$errorMessages[] = 'Container ' . $container['no_container'] . ' is not found in stock';
                        }
                    }
                }

                if (!empty($goods)) {
                    $stockGoods = $this->reportStock->getStockGoods([
                        'data' => 'stock',
                        'booking' => $bookingInId
                    ]);

                    foreach ($goods as $index => &$item) {
                        $isFound = false;
                        $foundStock = null;
                        foreach ($stockGoods as $stockItem) {
                            if ($item['id_goods'] == $stockItem['id_goods']) {
                                $isFound = true;
                                $foundStock = $stockItem;
                                break;
                            }
                        }
                        if ($isFound) {
                            if ($item['id_unit'] != $foundStock['id_unit']) {
                                $errorMessages[] = 'Unit ' . $item['unit'] . ' of item ' . $item['goods_name'] . ' is not matched with stock item (' . $foundStock['unit'] . ')';
                            }
                            if ($item['quantity'] > $foundStock['stock_quantity']) {
                                $errorMessages[] = 'Quantity item ' . $item['goods_name'] . ' (' . numerical($item['quantity'], 3, true) . ') ' . ' exceeded stock ' . numerical($foundStock['stock_quantity'], 3, true);
                            }

                            $item = [
                                'id_goods' => $item['id_goods'],
                                'no_goods' => $item['no_goods'],
                                'goods_name' => $item['goods_name'],
                                'type_goods' => $item['type_goods'],
                                'no_hs' => $item['no_hs'],
                                'quantity' => $item['quantity'],
                                'id_unit' => $foundStock['id_unit'],
                                'unit' => $foundStock['unit'],
                                'id_position' => if_empty($foundStock['id_position'], null),
                                'id_position_blocks' => $foundStock['id_position_blocks'],
                                'ex_no_container' => $foundStock['no_container'],
                                'unit_weight' => $item['unit_weight'],
                                'unit_gross_weight' => $foundStock['stock_gross_weight'],
                                'unit_volume' => $foundStock['stock_volume'],
                                'unit_length' => $foundStock['unit_length'],
                                'unit_width' => $foundStock['unit_width'],
                                'unit_height' => $foundStock['unit_height'],
                                'position' => $foundStock['position'],
                                'no_pallet' => $foundStock['no_pallet'],
                                'is_hold' => $foundStock['is_hold'],
                                'status' => $foundStock['status'],
                                'status_danger' => $foundStock['status_danger'],
                                'description' => $foundStock['description'],
                            ];
                        } else {
                            unset($goods[$index]);
                            //$errorMessages[] = 'Goods ' . $item['goods_name'] . ' is not found in stock';
                        }
                    }
                }
            }

            $data = [
                'title' => "Booking",
                'subtitle' => "Import booking",
                'page' => "booking_import/preview",
                'booking' => $booking,
                'upload' => $upload,
                'uploadDocuments' => $uploadDocuments,
                'customer' => $customer,
                'supplier' => $supplier,
                'extensionFields' => $extensionFields,
                'extensions' => $extensions,
                'bookingIn' => $bookingIn,
                'containers' => array_values($containers),
                'goods' => array_values($goods),
                'units' => $this->unit->getAll(),
                'errorMessages' => $errorMessages
            ];
            $this->load->view('template/layout', $data);

        } else {
            flash('danger', 'Empty XML data');
        }
    }

    /**
     * Import data to booking
     */
    public function import_save()
    {
        AuthorizationModel::checkAuthorizedAll([PERMISSION_BOOKING_IN_CREATE, PERMISSION_BOOKING_OUT_CREATE], false);

        $this->form_validation->set_rules('booking_date', 'Booking date', 'trim|required');
        $this->form_validation->set_rules('branch', 'Branch', 'trim|required|integer');
        $this->form_validation->set_rules('no_reference', 'No Reference', 'trim|required|max_length[50]');

        if ($this->form_validation->run() == FALSE) {
            flash('warning', 'Form inputs are invalid');
        } else {
            $uploadId = $this->input->get('upload');
            $file = base64_decode($this->input->get('file'));
            $sourceFile = 'booking_xml/' . date('Y/m/') . $file;

            $upload = $this->uploadModel->getById($uploadId);

            $category = $upload['category'];
            $type = $upload['id_booking_type'];
            $date = sql_date_format($this->input->post('booking_date'));
            $noReference = $this->input->post('no_reference');
            $referenceDate = sql_date_format($this->input->post('reference_date'), false);
            $vessel = $this->input->post('vessel');
            $voyage = $this->input->post('voyage');
            $description = $this->input->post('description');
            $branch = $this->input->post('branch');
            $supplier = $this->input->post('supplier');
            $supplierName = $this->input->post('supplier_name');
            $supplierAddress = $this->input->post('address');
            $customer = $upload['id_person'];
            $mode = 'IMPORT XML';
            $documentStatus = $this->input->post('document_status');
            $bookingInId = $this->input->post('booking_in');
            $extensions = $this->input->post('extensions');
            $containers = $this->input->post('containers');
            $goods = $this->input->post('goods');
            $netto = $this->input->post('netto');
            $bruto = $this->input->post('bruto');

            $customerData = $this->people->getById($customer);
            if ($category == BookingTypeModel::CATEGORY_INBOUND) {
                $noBooking = $this->booking->getAutoNumberBooking(BookingModel::NO_INBOUND);
                $paymentStatus = null;
            } else {
                $noBooking = $this->booking->getAutoNumberBooking(BookingModel::NO_OUTBOUND);
                $paymentStatus = $customerData['outbound_type'] == PeopleModel::OUTBOUND_CASH_AND_CARRY ? 'PENDING' : 'APPROVED';
            }

            $this->db->trans_start();

            // create supplier if necessary
            if ($supplier == 0 && !empty($supplierName)) {
                $this->people->create([
                    'id_branch' => $branch,
                    'type' => PeopleModel::$TYPE_SUPPLIER,
                    'type_user' => 'NON USER',
                    'no_person' => uniqid(),
                    'name' => $supplierName,
                    'address' => $supplierAddress,
                    'gender' => 'NONE',
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);
                $supplier = $this->db->insert_id();
            }

            // inserting booking header
            $this->booking->createBooking([
                'no_booking' => $noBooking,
                'no_reference' => $noReference,
                'reference_date' => $referenceDate,
                'id_booking' => if_empty($bookingInId, null, '', '', true),
                'id_booking_type' => $type,
                'id_supplier' => $supplier,
                'id_customer' => $customer,
                'id_upload' => $uploadId,
                'id_branch' => $branch,
                'booking_date' => $date,
                'vessel' => $vessel,
                'voyage' => $voyage,
                'description' => $description,
                'status' => BookingModel::STATUS_BOOKED,
                'status_payout' => $paymentStatus,
                'document_status' => $documentStatus,
                'mode' => $mode,
                'source_file' => $sourceFile,
                'created_by' => UserModel::authenticatedUserData('id'),
                'total_netto' => extract_number($netto),
                'total_bruto' => extract_number($bruto),
            ]);
            $bookingId = $this->db->insert_id();

            // insert booking status
            $this->bookingStatus->createBookingStatus([
                'id_booking' => $bookingId,
                'booking_status' => BookingModel::STATUS_IMPORTED,
                'document_status' => $documentStatus,
                'no_doc' => $noReference,
                'doc_date' => sql_date_format($referenceDate, false),
                'description' => 'Import data from XML',
                'created_by' => UserModel::authenticatedUserData('id')
            ]);
            $this->bookingStatus->createBookingStatus([
                'id_booking' => $bookingId,
                'booking_status' => BookingModel::STATUS_BOOKED,
                'document_status' => $documentStatus,
                'no_doc' => $noReference,
                'doc_date' => sql_date_format($referenceDate, false),
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
                            $value = sql_date_format($value, false);
                        } else if ($extensionField['type'] == 'DATE TIME') {
                            $value = sql_date_format($value);
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
                foreach ($containers as &$container) {
                    $foundContainer = $this->container->getBy(['no_container' => $container['no_container']], true);
                    if (empty($foundContainer)) {
                        $this->container->create([
                            'id_shipping_line' => $container['id_shipping_line'],
                            'no_container' => $container['no_container'],
                            'type' => $container['type'],
                            'size' => $container['size'],
                        ]);
                        $container['id_container'] = $this->db->insert_id();
                    } else {
                        $container['id_container'] = $foundContainer['id'];
                    }

                    $this->bookingContainer->createBookingContainer([
                        'id_booking' => $bookingId,
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
                        $this->createGoodsPackage($container['goods'], $bookingId, $bookingContainerId);
                    }
                }
            }

            $this->createGoodsPackage($goods, $bookingId);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                $this->uploader->move('temp/' . $file, $sourceFile);
                flash('success', "Booking <strong>{$noBooking}</strong> successfully created", 'booking');
            } else {
                flash('danger', "Create booking <strong>{$noBooking}</strong> failed, try again or contact administrator");
            }
        }
        $this->preview();
    }

    /**
     * Create goods and its assembly package if necessary.
     * @param $goods
     * @param $bookingId
     * @param null $bookingContainerId
     */
    private function createGoodsPackage($goods, $bookingId, $bookingContainerId = null)
    {
        if (!empty($goods)) {
            foreach ($goods as $item) {
                if (key_exists('goods', $item) && !empty($item['goods'])) {
                    $this->assembly->create([
                        'no_assembly' => $this->assembly->getAutoNumberAssembly(),
                        'quantity_package' => $item['quantity']
                    ]);
                    $assemblyId = $this->db->insert_id();

                    foreach ($item['goods'] as $childGoods) {
                        $this->assemblyGoods->create([
                            'id_assembly' => $assemblyId,
                            'id_unit' => $childGoods['id_unit'],
                            'assembly_goods' => $childGoods['id_goods'],
                            'quantity_assembly' => $childGoods['quantity'],
                        ]);
                        // update goods's parent
                        $this->goods->update([
                            'id_goods_parent' => $item['id_goods']
                        ], $childGoods['id_goods']);
                    }

                    $this->goods->update([
                        'id_assembly' => $assemblyId,
                        'unit_weight' => $item['unit_weight'],
                    ], $item['id_goods']);
                }

                // ex no container fallback
                if (!empty($bookingContainerId) && empty($item['ex_no_container'])) {
                    $bookingContainer = $this->bookingContainer->getById($bookingContainerId);
                    $item['ex_no_container'] = if_empty($bookingContainer['no_container'], null);
                }

                $this->bookingGoods->createBookingGoods([
                    'id_booking' => $bookingId,
                    'id_booking_container' => $bookingContainerId,
                    'id_goods' => $item['id_goods'],
                    'id_unit' => $item['id_unit'],
                    'id_position' => if_empty($item['id_position'], null),
                    'quantity' => $item['quantity'],
                    'unit_length' => $item['unit_length'],
                    'unit_width' => $item['unit_width'],
                    'unit_height' => $item['unit_height'],
                    'unit_volume' => $item['unit_volume'],
                    'unit_weight' => $item['unit_weight'],
                    'unit_gross_weight' => $item['unit_gross_weight'],
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
