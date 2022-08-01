<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Transporter_entry_permit_item_priority
 * @property TransporterEntryPermitRequestUploadModel $transporterEntryPermitRequestUpload
 * @property TransporterEntryPermitUploadModel $transporterEntryPermitUpload
 * @property TransporterEntryPermitRequestTepModel $transporterEntryPermitRequestTep
 * @property TransporterEntryPermitRequestPriorityItemModel $transporterEntryPermitRequestPriorityItem
 * @property BookingGoodsModel $bookingGoods
 * @property ReportStockModel $reportStock
 * @property PeopleModel $people
 * @property Exporter $exporter
 */
class Transporter_entry_permit_request_priority extends MY_Controller
{
    /**
     * Transporter_entry_permit_item_priority constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('TransporterEntryPermitRequestUploadModel', 'transporterEntryPermitRequestUpload');
        $this->load->model('TransporterEntryPermitUploadModel', 'transporterEntryPermitUpload');
        $this->load->model('TransporterEntryPermitRequestTepModel', 'transporterEntryPermitRequestTep');
        $this->load->model('TransporterEntryPermitRequestPriorityItemModel', 'transporterEntryPermitRequestPriorityItem');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('ReportStockModel', 'reportStock');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'edit_batch' => 'GET',
            'view_history' => 'GET',
            'ajax_get_data' => 'GET',
        ]);
    }

    /**
     * Show TEP request priority data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_REQUEST);

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("TEP request hold", $this->transporterEntryPermitRequestUpload->getOutstandingItemPriority($_GET));
        } else {
            $selectedCustomer = $this->people->getById($this->input->get('customer'));
            $this->render('transporter_entry_permit_request_priority/index', compact('selectedCustomer'), 'Request Priority');
        }
    }

    /**
     * Get ajax paging data TEP request priority.
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
            'outstanding_request' => true,
        ]);

        $data = $this->reportStock->getStockOutboundWithRequest($filters);
        //$data = $this->transporterEntryPermitRequestUpload->getOutstandingItemPriority($filters);

        $this->render_json($data);
    }

    private function getTepRequestUploadData()
    {
        return $this->transporterEntryPermitRequestUpload->getBy([
            'transporter_entry_permit_request_uploads.id_upload' => get_url_param('id_upload'),
            'transporter_entry_permit_request_uploads.id_goods' => get_url_param('id_goods'),
            'transporter_entry_permit_request_uploads.id_unit' => get_url_param('id_unit'),
            'IFNULL(transporter_entry_permit_request_uploads.ex_no_container, "") = "' . if_empty(get_url_param('ex_no_container'), "") . '"' => null,
        ]);
    }

    /**
     * View tep request upload item.
     */
    public function view()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_REQUEST);

        $tepRequestUploads = $this->getTepRequestUploadData();

        $this->render('transporter_entry_permit_request_priority/view', compact('tepRequestUploads'));
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

        $tepPriorityItems = $this->transporterEntryPermitRequestPriorityItem->getBy([
            'transporter_entry_permit_request_priority_items.id_booking' => $bookingId,
            'transporter_entry_permit_request_priority_items.id_goods' => $goodsId,
            'transporter_entry_permit_request_priority_items.id_unit' => $unitId,
            'IFNULL(transporter_entry_permit_request_priority_items.ex_no_container, "")=' => $exNoContainer,
        ]);

        $goods = $this->bookingGoods->getBy([
            'bookings.id' => $bookingId,
            'ref_goods.id' => $goodsId,
            'ref_units.id' => $unitId,
            'booking_goods.ex_no_container' => if_empty($exNoContainer, null),
        ], true);

        $this->render('transporter_entry_permit_request_priority/view_history', compact('tepPriorityItems', 'goods'), 'Priority Item Status History');
    }

    /**
     * Show edit tep request set priority.
     */
    public function edit()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_REQUEST);

        $tepRequestUploads = $this->getTepRequestUploadData();

        $this->render('transporter_entry_permit_request_priority/edit', compact('tepRequestUploads'), 'Edit Item Priority');
    }

    /**
     * Show edit batch tep request set priority.
     */
    public function edit_batch()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_REQUEST);

        $params = $_GET;

        if (empty($params)) {
            flash('danger', 'Invalid item priority param', '_back', 'transporter-entry-permit-request-priority');
        }

        $uploadIds = $params['id_upload'];
        $goodsIds = $params['id_goods'];
        $unitIds = $params['id_unit'];
        $exNoContainer = $params['ex_no_container'];

        $tepRequestUploadGroups = [];
        foreach ($uploadIds as $index => $uploadId) {
            $tepRequestUploadGroups[] = $this->transporterEntryPermitRequestUpload->getBy([
                'transporter_entry_permit_request_uploads.id_upload' => $uploadIds[$index],
                'transporter_entry_permit_request_uploads.id_goods' => $goodsIds[$index],
                'transporter_entry_permit_request_uploads.id_unit' => $unitIds[$index],
                'IFNULL(transporter_entry_permit_request_uploads.ex_no_container, "") = "' . if_empty($exNoContainer[$index], "") . '"' => null,
            ]);
        }

        $this->render('transporter_entry_permit_request_priority/edit_batch', compact('tepRequestUploadGroups'), 'Edit Item Priority');
    }

    /**
     * Perform update priority and location of item.
     */
    public function update()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_REQUEST);

        if ($this->validate()) {
            $unloadLocations = $this->input->post('unload_location');
            $priorities = $this->input->post('priority');
            $descriptions = $this->input->post('description');
            $tepRequestUploadIdGroups = if_empty($this->input->post('tep_request_unloads'), []);

            if (!is_array($unloadLocations)) {
                $unloadLocations = [$unloadLocations];
            }
            if (!is_array($priorities)) {
                $priorities = [$priorities];
            }
            if (!is_array(end($tepRequestUploadIdGroups))) {
                $tepRequestUploadIdGroups = [$tepRequestUploadIdGroups];
            }

            $this->db->trans_start();

            foreach ($unloadLocations as $index => $unloadLocation) {
                foreach ($tepRequestUploadIdGroups[$index] as $tepRequestUploadId) {
                    $tepRequestUpload = $this->transporterEntryPermitUpload->getById($tepRequestUploadId);
                    // update request upload
                    $this->transporterEntryPermitRequestUpload->update([
                        'priority' => $priorities[$index],
                        'priority_description' => $descriptions[$index],
                        'unload_location' => strtoupper($unloadLocation)
                    ], $tepRequestUploadId);

                    // update realization tep upload
                    // get realization of tep from request
                    $entryPermits = $this->transporterEntryPermitRequestTep->getBy([
                        'transporter_entry_permit_request_tep.id_request' => $tepRequestUpload['id_request']
                    ]);

                    // update related goods in tep upload item -> release from "hold" status
                    foreach ($entryPermits as $entryPermit) {
                        $this->transporterEntryPermitUpload->update([
                            'priority' => $priorities[$index],
                            'priority_description' => $descriptions[$index],
                            'unload_location' => strtoupper($unloadLocation)
                        ], [
                            'transporter_entry_permit_uploads.id_tep' => $entryPermit['id_tep'],
                            'transporter_entry_permit_uploads.id_upload' => $tepRequestUpload['id_upload'],
                            'transporter_entry_permit_uploads.id_goods' => $tepRequestUpload['id_goods'],
                            'transporter_entry_permit_uploads.id_unit' => $tepRequestUpload['id_unit'],
                            'IFNULL(transporter_entry_permit_uploads.ex_no_container, "") = "' . if_empty($tepRequestUpload['ex_no_container'], "") . '"' => null,
                            //'transporter_entry_permit_uploads.ex_no_container' => null,
                        ]);
                    }
                }

                $this->transporterEntryPermitRequestPriorityItem->create([
                    'id_booking' => is_array(get_url_param('id_booking')) ? get_url_param('id_booking')[$index] : get_url_param('id_booking', null),
                    'id_upload' => is_array(get_url_param('id_upload')) ? get_url_param('id_upload')[$index] : get_url_param('id_upload', null),
                    'id_goods' => is_array(get_url_param('id_goods')) ? get_url_param('id_goods')[$index] : get_url_param('id_goods', null),
                    'id_unit' => is_array(get_url_param('id_unit')) ? get_url_param('id_unit')[$index] : get_url_param('id_unit', null),
                    'ex_no_container' => is_array(get_url_param('ex_no_container')) ? if_empty(get_url_param('ex_no_container')[$index], null) : if_empty(get_url_param('ex_no_container', null), null),
                    'unload_location' => $unloadLocations[$index],
                    'priority' => $priorities[$index],
                    'description' => $descriptions[$index],
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "TEP priority items successfully updated", 'transporter-entry-permit-request-priority');
            } else {
                flash('danger', 'Something is getting wrong, try again later');
            }
        }
        $this->edit();
    }

    /**
     * Return validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            //'unload_location' => 'required|max_length[200]',
            //'priority' => 'required|max_length[50]',
            'tep_request_unloads[]' => 'required',
        ];
    }
}
