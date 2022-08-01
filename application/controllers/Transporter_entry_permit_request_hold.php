<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Transporter_entry_permit_item_hold
 * @property TransporterEntryPermitRequestHoldModel $transporterEntryPermitHold
 * @property TransporterEntryPermitRequestHoldItemModel $transporterEntryPermitHoldItem
 * @property TransporterEntryPermitRequestHoldReferenceModel $transporterEntryPermitHoldReference
 * @property TransporterEntryPermitRequestUploadModel $transporterEntryPermitRequestUpload
 * @property BookingGoodsModel $bookingGoods
 * @property PeopleModel $people
 * @property ReportStockModel $reportStock
 * @property StatusHistoryModel $statusHistory
 * @property Exporter $exporter
 */
class Transporter_entry_permit_request_hold extends MY_Controller
{
    /**
     * Transporter_entry_permit_item_hold constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('TransporterEntryPermitRequestHoldModel', 'transporterEntryPermitHold');
        $this->load->model('TransporterEntryPermitRequestHoldItemModel', 'transporterEntryPermitHoldItem');
        $this->load->model('TransporterEntryPermitRequestHoldReferenceModel', 'transporterEntryPermitHoldReference');
        $this->load->model('TransporterEntryPermitRequestUploadModel', 'transporterEntryPermitRequestUpload');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('ReportStockModel', 'reportStock');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'view_hold_items' => 'GET',
            'view_history' => 'GET',
            'ajax_get_data' => 'GET',
            'ajax_get_outstanding_item_request' => 'GET',
            'ajax_get_hold_item_request' => 'GET',
        ]);
    }

    /**
     * Show TEP request hold data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_REQUEST);

        if (get_url_param('export')) {
            $filters = array_merge($_GET, [
                'hold_type' => get_url_param('hold_type', TransporterEntryPermitRequestHoldModel::STATUS_HOLD)
            ]);
            $this->exporter->exportFromArray("TEP request hold", $this->transporterEntryPermitHold->getAll($filters));
        } else {
            $this->render('transporter_entry_permit_request_hold/index', [], 'Request Hold');
        }
    }

    /**
     * Get ajax paging data TEP request hold.
     */
    public function ajax_get_data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_REQUEST);

        $filters = array_merge(get_url_param('filter') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir'],
            'hold_type' => get_url_param('hold_type', TransporterEntryPermitRequestHoldModel::STATUS_HOLD)
        ]);

        $data = $this->transporterEntryPermitHold->getAll($filters);

        $this->render_json($data);
    }

    /**
     * View current hold items.
     */
    public function view_hold_items()
    {
        $conditions = [
            'transporter_entry_permit_request_hold_items.hold_status' => TransporterEntryPermitRequestHoldModel::STATUS_HOLD
        ];

        if (UserModel::authenticatedUserData('user_type') != 'INTERNAL') {
            $conditions['transporter_entry_permit_request_holds.id_customer'] = UserModel::authenticatedUserData('id_person');
        }
        $holdItems = $this->transporterEntryPermitHoldItem->getBy($conditions);

        $this->render('transporter_entry_permit_request_hold/view_hold_items', compact('holdItems'), 'View Hold Items');
    }

    /**
     * View history data from items.
     */
    public function view_history()
    {
        $bookingId = $this->input->get('id_booking');
        $goodsId = $this->input->get('id_goods');
        $unitId = $this->input->get('id_unit');
        $exNoContainer = $this->input->get('ex_no_container');

        $tepHoldItems = $this->transporterEntryPermitHoldItem->getBy([
            'transporter_entry_permit_request_hold_items.id_booking' => $bookingId,
            'transporter_entry_permit_request_hold_items.id_goods' => $goodsId,
            'transporter_entry_permit_request_hold_items.id_unit' => $unitId,
            'IFNULL(transporter_entry_permit_request_hold_items.ex_no_container, "")=' => $exNoContainer,
        ]);

        $goods = $this->bookingGoods->getBy([
            'bookings.id' => $bookingId,
            'ref_goods.id' => $goodsId,
            'ref_units.id' => $unitId,
            'booking_goods.ex_no_container' => if_empty($exNoContainer, null),
        ], true);

        $this->render('transporter_entry_permit_request_hold/view_history', compact('tepHoldItems', 'goods'), 'Hold Item Status History');
    }

    /**
     * Show detail tep request hold.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_REQUEST);

        $tepRequestHold = $this->transporterEntryPermitHold->getById($id);
        $tepRequestHoldItems = $this->transporterEntryPermitHoldItem->getBy(['transporter_entry_permit_request_hold_items.id_tep_hold' => $id]);
        foreach ($tepRequestHoldItems as &$tepRequestHoldItem) {
            $tepRequestHoldItem['request_upload_references'] = $this->transporterEntryPermitHoldReference->getBy([
                'id_tep_hold_item' => $tepRequestHoldItem['id']
            ]);
        }

        $this->render('transporter_entry_permit_request_hold/view', compact('tepRequestHold', 'tepRequestHoldItems'), 'Request Item Hold');
    }

    /**
     * Show form create tep request hold.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_REQUEST);

        $customer = $this->people->getById(if_empty($this->input->post('customer'), $this->input->get('id_customer')));
        $goods = [];

        $bookingId = $this->input->get('id_booking');
        $goodsId = $this->input->get('id_goods');
        $unitId = $this->input->get('id_unit');
        $exNoContainer = if_empty($this->input->get('ex_no_container'), null);
        if (!empty($bookingId) && !empty($goodsId) && !empty($unitId)) {
            $outstandingRequests = $this->reportStock->getStockOutboundWithRequest([
                'outstanding_request' => true,
                'booking' => $bookingId,
                'goods' => $goodsId,
                'unit' => $unitId,
                'ex_no_container' => $exNoContainer,
            ]);
            if (empty($outstandingRequests)) {
                flash('danger', 'Selected item cannot set hold because it is not in OUTSTANDING REQUESTED state', 'transporter-entry-permit-request-hold');
            } else {
                $customer = $this->people->getById($outstandingRequests[0]['id_customer']);
                $goods[] = [
                    'no_reference_inbound' => $outstandingRequests[0]['no_reference_inbound'],
                    'no_reference_outbound' => $outstandingRequests[0]['no_reference_outbound'],
                    'goods_name' => $outstandingRequests[0]['goods_name'],
                    'no_goods' => $outstandingRequests[0]['no_goods'],
                    'unit' => $outstandingRequests[0]['unit'],

                    'id_upload' => $outstandingRequests[0]['id_upload_outbound'],
                    'id_booking' => $outstandingRequests[0]['id_booking_outbound'],
                    'id_goods' => $outstandingRequests[0]['id_goods'],
                    'id_unit' => $outstandingRequests[0]['id_unit'],
                    'ex_no_container' => $outstandingRequests[0]['ex_no_container'],
                    'quantity' => $outstandingRequests[0]['request_quantity'],
                    'description' => 'Request to hold',
                    'id_request_uploads' => $outstandingRequests[0]['id_request_uploads'],
                    'id_requests' => $outstandingRequests[0]['id_requests'],
                    'no_requests' => $outstandingRequests[0]['no_requests'],
                ];
            }
        }

        $this->render('transporter_entry_permit_request_hold/create', compact('customer', 'goods'), 'Request Hold');
    }

    /**
     * Save new tep request hold data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_REQUEST);

        if ($this->validate()) {
            $customerId = $this->input->post('customer');
            $description = $this->input->post('description');
            $goods = if_empty($this->input->post('goods'), []);

            $requestNumber = $this->transporterEntryPermitHold->getAutoNumber('REQ-HOLD');

            $this->db->trans_start();

            $this->transporterEntryPermitHold->create([
                'id_branch' => get_active_branch_id(),
                'id_customer' => $customerId,
                'no_hold_reference' => $requestNumber,
                'hold_type' => TransporterEntryPermitRequestHoldModel::STATUS_HOLD,
                'hold_status' => TransporterEntryPermitRequestHoldModel::STATUS_HOLD,
                'description' => $description,
            ]);
            $tepRequestHoldId = $this->db->insert_id();

            foreach ($goods as $item) {
                $this->transporterEntryPermitHoldItem->create([
                    'id_tep_hold' => $tepRequestHoldId,
                    'id_booking' => if_empty($item['id_booking'], null),
                    'id_upload' => if_empty($item['id_upload'], null),
                    'id_goods' => $item['id_goods'],
                    'id_unit' => $item['id_unit'],
                    'ex_no_container' => if_empty($item['ex_no_container'], null),
                    'quantity' => $item['quantity'],
                    'hold_status' => TransporterEntryPermitRequestHoldModel::STATUS_HOLD,
                    'description' => $item['description']
                ]);
                $tepRequestHoldItemId = $this->db->insert_id();

                // store data reference, (hold items only contain reference to booking, goods, unit, and ex no container)
                // selected data could requested in multiple TEP requests which live inside transporterEntryPermitRequestUpload record data
                $requestUploadIds = explode(',', $item['id_request_uploads'] ?? '') ?? [];
                foreach ($requestUploadIds as $requestUploadId) {
                    // make sure incoming data is truly available in tep request, then proceed
                    $tepRequestUpload = $this->transporterEntryPermitRequestUpload->getById($requestUploadId);
                    if (!empty($tepRequestUpload)) {
                        // store reference to use later (to know which request item is hold -> back to released)
                        $this->transporterEntryPermitHoldReference->create([
                            'id_tep_hold_item' => $tepRequestHoldItemId,
                            'id_tep_request_upload' => $tepRequestUpload['id'],
                            'quantity' => $tepRequestUpload['quantity'],
                            'description' => $tepRequestUpload['no_request']
                        ]);

                        // mark tep request item as HOLD -> back to release later
                        $this->transporterEntryPermitRequestUpload->update([
                            'hold_status' => TransporterEntryPermitRequestHoldModel::STATUS_HOLD
                        ], $tepRequestUpload['id']);
                    }
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "TEP request for hold items successfully created", 'transporter-entry-permit-request-hold');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->create();
    }

    /**
     * Perform deleting delete data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_REQUEST);

        $tepRequestHold = $this->transporterEntryPermitHold->getById($id);

        if ($this->transporterEntryPermitHold->delete($id)) {
            flash('warning', "Tep request hold {$tepRequestHold['no_hold_reference']} is successfully deleted");
        } else {
            flash('danger', "Delete request request hold {$tepRequestHold['no_hold_reference']} failed");
        }
        redirect('transporter-entry-permit-request-hold');
    }

    /**
     * Return validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'description' => 'trim|max_length[500]',
            'goods[]' => 'required',
        ];
    }

    /**
     * Get outstanding request.
     */
    public function ajax_get_outstanding_item_request()
    {
        $customerId = $this->input->get('id_customer');

        $outstandingRequests = $this->reportStock->getStockOutboundWithRequest([
            'customer' => $customerId,
            'outstanding_request' => true
        ]);

        // exclude item that already hold
        $holdItems = $this->transporterEntryPermitHoldItem->getBy([
            'transporter_entry_permit_request_hold_items.hold_status' => TransporterEntryPermitRequestHoldModel::STATUS_HOLD
        ]);
        foreach ($outstandingRequests as $index => &$outstandingRequest) {
            $outstandingRequest['_is_hold'] = false;
            foreach ($holdItems as $holdItem) {
                $sameBooking = $outstandingRequest['id_booking_outbound'] == $holdItem['id_booking'];
                $sameGoods = $outstandingRequest['id_goods'] == $holdItem['id_goods'];
                $sameUnit = $outstandingRequest['id_unit'] == $holdItem['id_unit'];
                $sameExNoContainer = $outstandingRequest['ex_no_container'] == $holdItem['ex_no_container'];
                if ($sameBooking && $sameGoods && $sameUnit && $sameExNoContainer) {
                    $outstandingRequest['_is_hold'] = true;
                    //unset($outstandingRequests[$index]); // remove from list instead
                    break;
                }
            }
        }

        $this->render_json(array_values($outstandingRequests));
    }

    /**
     * Get hold item request.
     */
    public function ajax_get_hold_item_request()
    {
        $customerId = $this->input->get('id_customer');

        $holdGoods = $this->transporterEntryPermitHoldItem->getBy([
            'transporter_entry_permit_request_holds.id_customer' => $customerId,
            'transporter_entry_permit_request_hold_items.hold_status' => TransporterEntryPermitRequestHoldModel::STATUS_HOLD,
        ]);

        $this->render_json($holdGoods);
    }
}
