<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Transporter_entry_permit_item_release
 * @property TransporterEntryPermitRequestHoldModel $transporterEntryPermitHold
 * @property TransporterEntryPermitRequestHoldItemModel $transporterEntryPermitHoldItem
 * @property TransporterEntryPermitRequestHoldReferenceModel $transporterEntryPermitHoldReference
 * @property TransporterEntryPermitRequestUploadModel $transporterEntryPermitRequestUpload
 * @property TransporterEntryPermitUploadModel $transporterEntryPermitUpload
 * @property TransporterEntryPermitRequestTepModel $transporterEntryPermitRequestTep
 * @property PeopleModel $people
 * @property ReportStockModel $reportStock
 * @property StatusHistoryModel $statusHistory
 * @property Exporter $exporter
 */
class Transporter_entry_permit_request_release extends MY_Controller
{
    /**
     * Transporter_entry_permit_request_release constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('TransporterEntryPermitRequestHoldModel', 'transporterEntryPermitHold');
        $this->load->model('TransporterEntryPermitRequestHoldItemModel', 'transporterEntryPermitHoldItem');
        $this->load->model('TransporterEntryPermitRequestHoldReferenceModel', 'transporterEntryPermitHoldReference');
        $this->load->model('TransporterEntryPermitRequestUploadModel', 'transporterEntryPermitRequestUpload');
        $this->load->model('TransporterEntryPermitUploadModel', 'transporterEntryPermitUpload');
        $this->load->model('TransporterEntryPermitRequestTepModel', 'transporterEntryPermitRequestTep');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('ReportStockModel', 'reportStock');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'ajax_get_data' => 'GET',
        ]);
    }

    /**
     * Show TEP request release data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_REQUEST);

        if (get_url_param('export')) {
            $filters = array_merge($_GET, [
                'hold_type' => get_url_param('hold_type', TransporterEntryPermitRequestHoldModel::STATUS_RELEASED)
            ]);
            $this->exporter->exportFromArray("TEP request release", $this->transporterEntryPermitHold->getAll($filters));
        } else {
            $this->render('transporter_entry_permit_request_release/index', [], 'Request Release');
        }
    }

    /**
     * Get ajax paging data TEP request release.
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
            'hold_type' => get_url_param('hold_type', TransporterEntryPermitRequestHoldModel::STATUS_RELEASED)
        ]);

        $data = $this->transporterEntryPermitHold->getAll($filters);

        $this->render_json($data);
    }

    /**
     * Show detail tep request release.
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

        $this->render('transporter_entry_permit_request_release/view', compact('tepRequestHold', 'tepRequestHoldItems'), 'Request Item Hold');
    }

    /**
     * Show form create tep request release.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_REQUEST);

        $customer = $this->people->getById(if_empty($this->input->post('customer'), $this->input->get('id_customer')));
        $holdGoodId = null;

        $bookingId = $this->input->get('id_booking');
        $goodsId = $this->input->get('id_goods');
        $unitId = $this->input->get('id_unit');
        $exNoContainer = if_empty($this->input->get('ex_no_container'), null);
        if (!empty($bookingId) && !empty($goodsId) && !empty($unitId)) {
            $holdItems = $this->transporterEntryPermitHoldItem->getBy([
                'transporter_entry_permit_request_holds.hold_type' => 'HOLD',
                'transporter_entry_permit_request_hold_items.id_booking' => $bookingId,
                'transporter_entry_permit_request_hold_items.id_goods' => $goodsId,
                'transporter_entry_permit_request_hold_items.id_unit' => $unitId,
                'transporter_entry_permit_request_hold_items.ex_no_container' => $exNoContainer,
                'transporter_entry_permit_request_hold_items.hold_status' => TransporterEntryPermitRequestHoldModel::STATUS_HOLD,
            ]);

            if (empty($holdItems)) {
                flash('danger', 'Selected item cannot set release because it is not HOLD, please select existing item instead');
            } else {
                $customer = $this->people->getById($holdItems[0]['id_customer']);
                $holdGoodId = $holdItems[0]['id'];
            }
        }

        $this->render('transporter_entry_permit_request_release/create', compact('customer', 'holdGoodId'), 'Request Release');
    }

    /**
     * Save new tep request release data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_REQUEST);

        if ($this->validate()) {
            $customerId = $this->input->post('customer');
            $description = $this->input->post('description');
            $holdGoodsIds = if_empty($this->input->post('hold_goods'), []);

            $requestNumber = $this->transporterEntryPermitHold->getAutoNumber('REQ-RLSD');

            $this->db->trans_start();

            $this->transporterEntryPermitHold->create([
                'id_branch' => get_active_branch_id(),
                'id_customer' => $customerId,
                'no_hold_reference' => $requestNumber,
                'hold_type' => TransporterEntryPermitRequestHoldModel::STATUS_RELEASED,
                'hold_status' => TransporterEntryPermitRequestHoldModel::STATUS_RELEASED,
                'description' => $description,
            ]);
            $tepRequestHoldId = $this->db->insert_id();

            foreach ($holdGoodsIds as $holdGoodsId) {
                $tepHoldItem = $this->transporterEntryPermitHoldItem->getById($holdGoodsId);
                $tepHoldItemReferences = $this->transporterEntryPermitHoldReference->getBy([
                    'id_tep_hold_item' => $tepHoldItem['id']
                ]);

                if (!empty($tepHoldItem)) {
                    $this->transporterEntryPermitHoldItem->update([
                        'hold_status' => TransporterEntryPermitRequestHoldModel::STATUS_RELEASED
                    ], $tepHoldItem['id']);

                    // copy data from hold -> released (add reference: id_tep_hold_item_reference)
                    $this->transporterEntryPermitHoldItem->create([
                        'id_tep_hold' => $tepRequestHoldId,
                        'id_tep_hold_item_reference' => $tepHoldItem['id'],
                        'id_booking' => if_empty($tepHoldItem['id_booking'], null),
                        'id_upload' => if_empty($tepHoldItem['id_upload'], null),
                        'id_goods' => $tepHoldItem['id_goods'],
                        'id_unit' => $tepHoldItem['id_unit'],
                        'ex_no_container' => if_empty($tepHoldItem['ex_no_container'], null),
                        'quantity' => $tepHoldItem['quantity'],
                        'hold_status' => TransporterEntryPermitRequestHoldModel::STATUS_RELEASED,
                        'description' => 'Goods released'
                    ]);
                    $tepRequestHoldItemId = $this->db->insert_id();

                    foreach ($tepHoldItemReferences as $tepHoldItemReference) {
                        $this->transporterEntryPermitHoldReference->create([
                            'id_tep_hold_item' => $tepRequestHoldItemId,
                            'id_tep_request_upload' => $tepHoldItemReference['id_tep_request_upload'],
                            'quantity' => $tepHoldItemReference['quantity'],
                            'description' => $tepHoldItemReference['description']
                        ]);

                        // mark tep request item as RELEASED -> from HOLD
                        $this->transporterEntryPermitRequestUpload->update([
                            'hold_status' => TransporterEntryPermitRequestHoldModel::STATUS_RELEASED
                        ], $tepHoldItemReference['id_tep_request_upload']);

                        // mark tep item as RELEASED -> from HOLD (after SET TEP)
                        $this->releaseRealizedTepItem($tepHoldItemReference);
                    }

                    // check if requested hold is need update header status
                    $this->updateRequestHoldStatus($tepHoldItem['id_tep_hold']);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "TEP request for release items successfully created", 'transporter-entry-permit-request-release');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->create();
    }

    /**
     * Release tep item.
     *
     * @param $tepHoldItemReference
     */
    private function releaseRealizedTepItem($tepHoldItemReference)
    {
        $requestId = $tepHoldItemReference['id_tep_request'];
        $requestUploadItem = $this->transporterEntryPermitRequestUpload->getById($tepHoldItemReference['id_tep_request_upload']);

        // get realization of tep from request
        $entryPermits = $this->transporterEntryPermitRequestTep->getBy([
            'transporter_entry_permit_request_tep.id_request' => $requestId
        ]);

        // update related goods in tep upload item -> release from "hold" status
        foreach ($entryPermits as $entryPermit) {
            $this->transporterEntryPermitUpload->update([
                'hold_status' => TransporterEntryPermitRequestHoldModel::STATUS_RELEASED
            ], [
                'transporter_entry_permit_uploads.id_tep' => $entryPermit['id_tep'],
                'transporter_entry_permit_uploads.id_upload' => $requestUploadItem['id_upload'],
                'transporter_entry_permit_uploads.id_goods' => $requestUploadItem['id_goods'],
                'transporter_entry_permit_uploads.id_unit' => $requestUploadItem['id_unit'],
                'IFNULL(transporter_entry_permit_uploads.ex_no_container, "") = "' . if_empty($requestUploadItem['ex_no_container'], "") . '"' => null,
                //'transporter_entry_permit_uploads.ex_no_container' => null,
            ]);
        }
    }

    /**
     * Check if request hold data need updated to be: PARTIAL RELEASED or RELEASED.
     *
     * @param $tepRequestHoldId
     */
    private function updateRequestHoldStatus($tepRequestHoldId)
    {
        $tepHold = $this->transporterEntryPermitHold->getById($tepRequestHoldId);
        $tepHoldItems = $this->transporterEntryPermitHoldItem->getBy(['transporter_entry_permit_request_hold_items.id_tep_hold' => $tepHold['id']]);
        $currentStatus = $tepHold['hold_status'];
        $isAnyHold = false;
        foreach ($tepHoldItems as $tepHoldItem) {
            if ($tepHoldItem['hold_status'] == TransporterEntryPermitRequestHoldModel::STATUS_HOLD) {
                $isAnyHold = true;
            } else {
                $currentStatus = TransporterEntryPermitRequestHoldModel::STATUS_PARTIAL_RELEASE;
            }
        }

        if ($isAnyHold) {
            $this->transporterEntryPermitHold->update([
                'hold_status' => $currentStatus
            ], $tepHold['id']);
        } else {
            $this->transporterEntryPermitHold->update([
                'hold_status' => TransporterEntryPermitRequestHoldModel::STATUS_RELEASED
            ], $tepHold['id']);
        }
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
            flash('warning', "Tep request release {$tepRequestHold['no_hold_reference']} is successfully deleted");
        } else {
            flash('danger', "Delete request request release {$tepRequestHold['no_hold_reference']} failed");
        }
        redirect('transporter-entry-permit-request-release');
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
            'hold_goods[]' => 'required',
        ];
    }
}
