<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Transporter_entry_permit_tracking
 * @property TransporterEntryPermitModel $transporterEntryPermit
 * @property TransporterEntryPermitTrackingModel $transporterEntryPermitTracking
 * @property SafeConductModel $safeConduct
 * @property SafeConductHandoverModel $safeConductHandover
 * @property PhBidOrderSummaryModel $phBidOrderSummary
 * @property PhBidOrderContainerModel $phBidOrderContainer
 * @property StatusHistoryModel $statusHistory
 * @property Exporter $exporter
 */
class Transporter_entry_permit_tracking extends MY_Controller
{
    /**
     * Transporter_entry_permit_tracking constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('TransporterEntryPermitModel', 'transporterEntryPermit');
        $this->load->model('TransporterEntryPermitTrackingModel', 'transporterEntryPermitTracking');
        $this->load->model('SafeConductHandoverModel', 'safeConductHandover');
        $this->load->model('SafeConductModel', 'safeConduct');
        $this->load->model('PhBidOrderSummaryModel', 'phBidOrderSummary');
        $this->load->model('PhBidOrderContainerModel', 'phBidOrderContainer');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'ajax_get_data' => 'GET',
            'confirm_site_transit' => 'GET|PUT',
            'confirm_unloading' => 'GET|PUT',
        ]);
    }

    /**
     * Show TEP tracking data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized([PERMISSION_TEP_TRACKING_LINK, PERMISSION_TEP_TRACKING_VALIDATE]);

        if (get_url_param('export')) {
            $filters = array_merge($_GET, [
                'status' => get_url_param('status', 'NOT LINKED')
            ]);
            $this->exporter->exportFromArray("TEP trackings", $this->transporterEntryPermitTracking->getAll($filters));
        } else {
            $this->render('transporter_entry_permit_tracking/index');
        }
    }

    /**
     * Get ajax paging data TEP tracking link.
     */
    public function ajax_get_data()
    {
        AuthorizationModel::mustAuthorized([PERMISSION_TEP_TRACKING_LINK, PERMISSION_TEP_TRACKING_VALIDATE]);

        $defaultStatus = 'NOT LINKED';
        if (!AuthorizationModel::isAuthorized(PERMISSION_TEP_TRACKING_LINK)) {
            $defaultStatus = 'LINKED DATA';
        }
        $filters = array_merge(get_url_param('filter') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir'],
            'status' => get_url_param('status', $defaultStatus)
        ]);

        $data = $this->transporterEntryPermitTracking->getAll($filters);

        $this->render_json($data);
    }

    /**
     * Show detail view.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized([PERMISSION_TEP_TRACKING_LINK, PERMISSION_TEP_TRACKING_VALIDATE]);

        $transporterEntryPermitTracking = $this->transporterEntryPermitTracking->getById($id);
        $safeConductHandovers = $this->safeConductHandover->getBy(['id_tep' => $transporterEntryPermitTracking['id_tep']]);
        $statusHistories = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_TEP_TRACKING,
            'status_histories.id_reference' => $id
        ]);

        $this->render('transporter_entry_permit_tracking/view', compact('transporterEntryPermitTracking', 'safeConductHandovers', 'statusHistories'));
    }

    /**
     * Show form create link tep.
     * @param null $tepId
     */
    public function create($tepId = null)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_TRACKING_LINK);

        $unlinkedTep = $this->transporterEntryPermit->getOutstandingTep([
            'category' => 'OUTBOUND',
            'should_checked_in' => true,
            'outstanding_tracking_link' => true,
            'id' => $tepId,
        ]);
        $trackedVehicles = $this->phBidOrderSummary->getOrderContainerTransactions([
            'outstanding_tracking_link' => true,
        ])->get();

        $this->render('transporter_entry_permit_tracking/create', compact('unlinkedTep', 'trackedVehicles'));
    }

    /**
     * Save new tracking linked tep data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_TRACKING_LINK);

        if ($this->validate()) {
            $tepId = $this->input->post('tep');
            $trackingVehicleId = $this->input->post('tracked_vehicle');
            $description = $this->input->post('description');

            $tep = $this->transporterEntryPermit->getById($tepId);
            $vehicle = $this->phBidOrderContainer->getById($trackingVehicleId);

            $this->db->trans_start();

            $this->transporterEntryPermitTracking->create([
                'id_tep' => $tepId,
                'id_phbid_tracking' => $trackingVehicleId,
                'id_phbid_reference' => $vehicle['id_reference'],
                'phbid_no_vehicle' => $vehicle['nomor_kontainer'],
                'phbid_no_order' => $vehicle['nomor_order'],
                'status' => TransporterEntryPermitTrackingModel::STATUS_LINKED,
                'description' => $description,
            ]);
            $tepTrackingId = $this->db->insert_id();

            $this->statusHistory->create([
                'type' => StatusHistoryModel::TYPE_TEP_TRACKING,
                'id_reference' => $tepTrackingId,
                'status' => TransporterEntryPermitTrackingModel::STATUS_LINKED,
                'description' => "TEP {$tep['tep_code']} linked with {$vehicle['nomor_kontainer']}",
                'data' => json_encode([
                    'id_phbid_tracking' => $trackingVehicleId,
                    'id_phbid_reference' => $vehicle['id_reference'],
                    'phbid_no_vehicle' => $vehicle['nomor_kontainer'],
                    'phbid_no_order' => $vehicle['nomor_order'],
                ])
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "TEP {$tep['tep_code']} successfully linked with {$vehicle['nomor_kontainer']}", 'transporter-entry-permit-tracking');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->create();
    }

    /**
     * Confirm site transit.
     *
     * @param $id
     */
    public function confirm_site_transit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_TRACKING_VALIDATE);

        $transporterEntryPermitTracking = $this->transporterEntryPermitTracking->getById($id);

        if (_is_method('put') && $this->validate(['site_transit_actual_date' => 'required'])) {
            $siteTransitActualDate = format_date($this->input->post('site_transit_actual_date'), 'Y-m-d H:i:s');
            $description = $this->input->post('description');

            $this->db->trans_start();

            $this->transporterEntryPermitTracking->update([
                'status' => TransporterEntryPermitTrackingModel::STATUS_SITE_TRANSIT,
                'site_transit_actual_date' => $siteTransitActualDate,
                'site_transit_description' => $description
            ], $id);

            $this->statusHistory->create([
                'type' => StatusHistoryModel::TYPE_TEP_TRACKING,
                'id_reference' => $id,
                'status' => TransporterEntryPermitTrackingModel::STATUS_SITE_TRANSIT,
                'description' => if_empty($description, "Site transit updated at {$siteTransitActualDate}", '', '', true),
                'data' => json_encode([
                    'site_transit_actual_date' => $siteTransitActualDate,
                    'site_transit_description' => $description
                ])
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Site transit TEP {$transporterEntryPermitTracking['tep_code']} successfully updated");
                redirect('transporter-entry-permit-tracking');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        } else {
            $this->render('transporter_entry_permit_tracking/confirm_site_transit', compact('transporterEntryPermitTracking'), 'TEP Confirm Transit');
        }
    }

    /**
     * Confirm unloading.
     *
     * @param $id
     */
    public function confirm_unloading($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_TRACKING_VALIDATE);

        $transporterEntryPermitTracking = $this->transporterEntryPermitTracking->getById($id);

        if (_is_method('put') && $this->validate(['unloading_actual_date' => 'required'])) {
            $unloadingActualDate = format_date($this->input->post('unloading_actual_date'), 'Y-m-d H:i:s');
            $description = $this->input->post('description');

            $this->db->trans_start();

            $this->transporterEntryPermitTracking->update([
                'status' => TransporterEntryPermitTrackingModel::STATUS_UNLOADED,
                'unloading_actual_date' => $unloadingActualDate,
                'unloading_description' => $description
            ], $id);

            $this->statusHistory->create([
                'type' => StatusHistoryModel::TYPE_TEP_TRACKING,
                'id_reference' => $id,
                'status' => TransporterEntryPermitTrackingModel::STATUS_UNLOADED,
                'description' => if_empty($description, "Unloading updated at {$unloadingActualDate}", '', '', true),
                'data' => json_encode([
                    'unloading_actual_date' => $unloadingActualDate,
                    'unloading_description' => $description
                ])
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Unloading TEP {$transporterEntryPermitTracking['tep_code']} successfully updated");
                redirect('transporter-entry-permit-tracking');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        } else {
            $this->render('transporter_entry_permit_tracking/confirm_unloading', compact('transporterEntryPermitTracking'), 'TEP Confirm Unloading');
        }
    }

    /**
     * Show form edit link tep.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_TRACKING_LINK_EDIT);

        $transporterEntryPermitTracking = $this->transporterEntryPermitTracking->getById($id);

        $this->render('transporter_entry_permit_tracking/edit', compact('transporterEntryPermitTracking'));
    }

    /**
     * Confirm site transit.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_TRACKING_LINK_EDIT);

        $transporterEntryPermitTracking = $this->transporterEntryPermitTracking->getById($id);
        $siteTransitActualDate = format_date($this->input->post('site_transit_actual_date'), 'Y-m-d H:i:s');
        $unloadingActualDate = format_date($this->input->post('unloading_actual_date'), 'Y-m-d H:i:s');
        $siteTransitDescription = $this->input->post('site_transit_description');
        $unloadingDescription = $this->input->post('unloading_description');

        if (!empty($siteTransitActualDate) && $transporterEntryPermitTracking['status'] == TransporterEntryPermitTrackingModel::STATUS_LINKED) {
            $transporterEntryPermitTracking['status'] = TransporterEntryPermitTrackingModel::STATUS_SITE_TRANSIT;
        }
        if (!empty($unloadingActualDate) && $transporterEntryPermitTracking['status'] == TransporterEntryPermitTrackingModel::STATUS_SITE_TRANSIT) {
            $transporterEntryPermitTracking['status'] = TransporterEntryPermitTrackingModel::STATUS_UNLOADED;
        }

        $this->db->trans_start();

        $data = [
            'status' => $transporterEntryPermitTracking['status'],
            'site_transit_actual_date' => $siteTransitActualDate ?: $transporterEntryPermitTracking['site_transit_actual_date'],
            'site_transit_description' => $siteTransitDescription ?: $transporterEntryPermitTracking['site_transit_description'],
            'unloading_actual_date' => $unloadingActualDate ?: $transporterEntryPermitTracking['unloading_actual_date'],
            'unloading_description' => $unloadingDescription ?: $transporterEntryPermitTracking['unloading_description'],
        ];
        $this->transporterEntryPermitTracking->update($data, $id);

        $this->statusHistory->create([
            'type' => StatusHistoryModel::TYPE_TEP_TRACKING,
            'id_reference' => $id,
            'status' => 'UPDATE',
            'description' => 'Linked tep updated',
            'data' => json_encode($data)
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            flash('success', "Site transit TEP {$transporterEntryPermitTracking['tep_code']} successfully updated", 'transporter-entry-permit-tracking');
        } else {
            flash('danger', 'Something is getting wrong, try again or contact administrator');
        }
    }

    /**
     * Perform deleting delete data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_TRACKING_LINK_EDIT);

        $tepTracking = $this->transporterEntryPermitTracking->getById($id);
        $safeConductHandover = $this->safeConductHandover->getBy([
            'transporter_entry_permits.id' => $tepTracking['id_tep']
        ]);
        if (!empty($safeConductHandover)) {
            flash('danger', "Tep {$tepTracking['tep_code']} link is already handover {$safeConductHandover[0]['no_safe_conduct']}, delete handover first", '_back');
        }

        if ($this->transporterEntryPermitTracking->delete($id, true)) {
            flash('warning', "Tep {$tepTracking['tep_code']} link to {$tepTracking['phbid_no_vehicle']} is successfully deleted");
        } else {
            flash('danger', "Delete link tep {$tepTracking['tep_code']} failed");
        }
        redirect('transporter-entry-permit-tracking');
    }

    /**
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'tep' => ['required', 'integer', 'is_natural_no_zero',
                ['tep_exists', function ($tepId) {
                    $this->form_validation->set_message('tep_exists', 'Tep is already linked, try another');
                    return empty($this->transporterEntryPermitTracking->getBy([
                        'id_tep' => $tepId,
                    ]));
                }]
            ],
            'tracked_vehicle' => ['required', 'integer', 'is_natural_no_zero',
                ['tracking_exists', function ($trackingId) {
                    $this->form_validation->set_message('tracking_exists', 'Tracked vehicle is already linked, try another');
                    return empty($this->transporterEntryPermitTracking->getBy([
                        'id_phbid_tracking' => $trackingId,
                    ]));
                }]
            ],
            'description' => 'trim|max_length[500]',
        ];
    }
}
