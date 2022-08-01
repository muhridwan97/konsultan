<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Plan_realization
 * @property ReportPlanRealizationModel $reportPlanRealization
 * @property PlanRealizationModel $planRealization
 * @property PlanResourceModel $planResource
 * @property PlanResourceReferenceModel $planResourceReference
 * @property PlanInboundModel $planInbound
 * @property PlanOutboundModel $planOutbound
 * @property PlanTransporterEntryPermitModel $planTransporterEntryPermit
 * @property TransporterEntryPermitModel $transporterEntryPermit
 * @property StatusHistoryModel $statusHistory
 * @property NotificationModel $notification
 * @property Exporter $exporter
 */
class Plan_realization extends MY_Controller
{
    /**
     * Plan_realization constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ReportPlanRealizationModel', 'reportPlanRealization');
        $this->load->model('PlanRealizationModel', 'planRealization');
        $this->load->model('PlanResourceModel', 'planResource');
        $this->load->model('PlanResourceReferenceModel', 'planResourceReference');
        $this->load->model('PlanInboundModel', 'planInbound');
        $this->load->model('PlanOutboundModel', 'planOutbound');
        $this->load->model('PlanTransporterEntryPermitModel', 'planTransporterEntryPermit');
        $this->load->model('TransporterEntryPermitModel', 'transporterEntryPermit');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('NotificationModel', 'notification');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->helper('text');

        $this->setFilterMethods([
            'ajax_get_data' => 'GET',
            'print_plan_realization' => 'GET',
            'close' => 'GET',
            'close_realization' => 'POST|PUT',
            'send_plan_realization' => 'POST|PUT',
        ]);
    }

    /**
     * Show auction data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PLAN_REALIZATION_VIEW);

        $this->render('plan_realization/index', []);
    }

    /**
     * Get ajax datatable plan realization.
     */
    public function ajax_get_data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PLAN_REALIZATION_VIEW);

        $filters = [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ];

        $data = $this->planRealization->getAll($filters);

        $this->render_json($data);
    }

    /**
     * Get plan realization data.
     *
     * @param $id
     * @return array
     */
    private function getPlanRealizationData($id)
    {
        $planRealization = $this->planRealization->getById($id);
        $resources = $this->planResource->getBy([
            'plan_resources.id_reference' => $id,
            'plan_resources.type' => PlanResourceModel::TYPE_ALL
        ]);

        $inbounds = $this->planInbound->getBy(['id_plan_realization' => $id]);
        foreach ($inbounds as &$inbound) {
            $inbound['resources'] = $this->planResource->getBy([
                'plan_resources.id_reference' => $inbound['id'],
                'plan_resources.type' => PlanResourceModel::TYPE_INBOUND
            ]);
            $inbound['transporter_entry_permits'] = $this->planTransporterEntryPermit->getBy([
                'id_plan_realization' => $id,
                'transporter_entry_permit_bookings.id_booking' => $inbound['id_booking']
            ]);
        }

        $activeInbound = array_filter($inbounds, function($inbound) {
            return !$inbound['is_skipped'];
        });
        $inboundTarget = array_sum(array_column($activeInbound, 'outstanding_20'))
            + array_sum(array_column($activeInbound, 'outstanding_40'))
            + array_sum(array_column($activeInbound, 'outstanding_lcl'));
        $inboundRealization = array_sum(array_column($activeInbound, 'close_realization_20'))
            + array_sum(array_column($activeInbound, 'close_realization_40'))
            + array_sum(array_column($activeInbound, 'close_realization_lcl'));
        $inboundSummary = [
            'target' => $inboundTarget,
            'realization' => $inboundRealization,
            'achievement' => empty($inboundTarget) ? 100 : (array_sum(array_column($activeInbound, 'achievement')) / (count($activeInbound) * 100) * 100),
        ];
        if ($inboundSummary['achievement'] > 100) $inboundSummary['achievement'] = 100;

        $outbounds = $this->planOutbound->getBy(['id_plan_realization' => $id]);
        foreach ($outbounds as &$outbound) {
            $outbound['resources'] = $this->planResource->getBy([
                'plan_resources.id_reference' => $outbound['id'],
                'plan_resources.type' => PlanResourceModel::TYPE_OUTBOUND
            ]);
            $outbound['transporter_entry_permits'] = $this->planTransporterEntryPermit->getBy([
                'id_plan_realization' => $id,
                'transporter_entry_permit_bookings.id_booking' => $outbound['id_booking']
            ]);
        }

        $activeOutbound = array_filter($outbounds, function($outbound) {
            return !$outbound['is_skipped'];
        });
        $outboundTarget = array_sum(array_column($activeOutbound, 'outstanding_20'))
            + array_sum(array_column($activeOutbound, 'outstanding_40'))
            + array_sum(array_column($activeOutbound, 'outstanding_lcl'));
        $outboundRealization = array_sum(array_column($activeOutbound, 'close_realization_20'))
            + array_sum(array_column($activeOutbound, 'close_realization_40'))
            + array_sum(array_column($activeOutbound, 'close_realization_lcl'));
        $outboundSummary = [
            'target' => $outboundTarget,
            'realization' => $outboundRealization,
            'achievement' => empty($outboundTarget) ? 100 : (array_sum(array_column($activeOutbound, 'achievement')) / (count($activeOutbound) * 100) * 100),
        ];
        if ($outboundSummary['achievement'] > 100) $outboundSummary['achievement'] = 100;

        $opsTeusInbound = (array_sum(array_column($activeInbound, 'close_realization_40')) * 2) + array_sum(array_column($activeInbound, 'close_realization_20'));
        $opsTeusOutbound = (array_sum(array_column($activeOutbound, 'close_realization_40')) * 2) + array_sum(array_column($activeOutbound, 'close_realization_20'));
        $opsTeus = $opsTeusInbound + $opsTeusOutbound;
        $planRealization['ops_teus'] = $opsTeus == 0 ? 0 : (4 / $opsTeus);

        $opsLcl = array_sum(array_column($activeInbound, 'close_realization_lcl')) + array_sum(array_column($activeOutbound, 'close_realization_lcl'));
        $planRealization['ops_lcl'] = $opsLcl == 0 ? 0 : (4 / $opsLcl);

        $lbrForklift = array_sum(array_column(array_filter($resources, function ($resource) {
            return strtolower($resource['resource']) == 'labor' || strtolower($resource['resource'] == 'forklift');
        }), 'realization'));
        $planRealization['lbr_teus'] = $opsTeus == 0 ? 0 : ($lbrForklift / $opsTeus);
        $planRealization['lbr_lcl'] = $opsLcl == 0 ? 0 : ($lbrForklift / $opsLcl);

        return compact('planRealization', 'resources', 'inbounds', 'inboundSummary', 'outbounds', 'outboundSummary');
    }

    /**
     * Show detail plan realization.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PLAN_REALIZATION_VIEW);

        $data = $this->getPlanRealizationData($id);
        $data['statusHistories'] = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_PLAN_REALIZATION,
            'status_histories.id_reference' => $id
        ]);

        $this->render('plan_realization/view', $data);
    }

    /**
     * Print plan realization data.
     *
     * @param $id
     */
    public function print_plan_realization($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PLAN_REALIZATION_PRINT);

        $page = $this->load->view('plan_realization/print', $this->getPlanRealizationData($id), true);

        $this->exporter->exportToPdf('plan-realization', $page, ['paper' => 'legal', 'orientation' => 'landscape']);
    }

    /**
     * Show form create plan realization.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PLAN_REALIZATION_CREATE);

        $planExist = $this->planRealization->getBy([
            'plan_realizations.date' => format_date('now'),
            'status' => PlanRealizationModel::STATUS_OPEN
        ]);
        if (!empty($planExist)) {
            flash('danger', 'Plan for date ' . format_date('now') . ' is already exist and open, please close last plan first', '_back', 'plan-realization');
        }

        $resources = $this->reportPlanRealization->getResource();
        $inbounds = $this->reportPlanRealization->getInbound();
        $outbounds = $this->reportPlanRealization->getOutbound();
        $outstandingTep = $this->transporterEntryPermit->getBy([
            'transporter_entry_permits.checked_in_at IS NULL' => null,
            'transporter_entry_permits.created_at>=' => '2021-01-01',
            //'NOW()<=transporter_entry_permits.expired_at' => null,
        ]);
        foreach ($inbounds as &$inbound) {
            $inbound['transporter_entry_permits'] = array_filter($outstandingTep, function ($tep) use ($inbound) {
                return $tep['id_booking'] == $inbound['id_booking'];
            });
        }

        foreach ($outbounds as &$outbound) {
            $outbound['transporter_entry_permits'] = array_filter($outstandingTep, function ($tep) use ($outbound) {
                return $tep['id_booking'] == $outbound['id_booking'] || (($tep['id_customer'] ?? $tep['id_customer_out']) == $outbound['id_customer'] && $tep['tep_category'] == 'OUTBOUND');
            });
        }

        $this->render('plan_realization/create', compact('resources', 'inbounds', 'outbounds'));
    }

    /**
     * Save new plan realization data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PLAN_REALIZATION_CREATE);

        if ($this->validate()) {
            $branchId = if_empty($this->input->post('branch'), get_active_branch_id());
            $resources = $this->input->post('resources');
            $inbounds = if_empty($this->input->post('inbounds'), []);
            $outbounds = if_empty($this->input->post('outbounds'), []);
            $description = $this->input->post('description');
            $send = $this->input->post('send');
            $date = date('Y-m-d');

            $dataResources = $this->reportPlanRealization->getResource(['transaction' => true]);
            $dataInbounds = $this->reportPlanRealization->getInbound();
            $dataOutbounds = $this->reportPlanRealization->getOutbound();

            $this->db->trans_start();

            $this->planRealization->create([
                'id_branch' => if_empty($branchId, get_active_branch_id()),
                'date' => $date,
                'description' => $description,
            ]);
            $planRealizationId = $this->db->insert_id();

            foreach ($dataResources as $resource) {
                if (key_exists($resource['resource'], $resources)) {
                    $selectedResource = $resources[$resource['resource']];
                    $this->planResource->create([
                        'id_reference' => $planRealizationId,
                        'type' => PlanResourceModel::TYPE_ALL,
                        'resource' => $resource['resource'],
                        'unit' => $resource['unit'],
                        'plan' => $resource['plan'],
                        'realization' => $resource['realization'],
                        'description' => $selectedResource['description'],
                    ]);
                    $planResourceId = $this->db->insert_id();
                    foreach ($resource['transactions'] as $transaction) {
                        $this->planResourceReference->create([
                            'id_plan_resource' => $planResourceId,
                            'id_reference' => $transaction['id'],
                            'no_reference' => $transaction['no_reference'],
                            'type' => $transaction['type'],
                        ]);
                    }
                }
            }

            foreach ($dataInbounds as $inbound) {
                if (key_exists($inbound['id_booking'], $inbounds)) {
                    $selectedInbound = $inbounds[$inbound['id_booking']];
                    if(!key_exists('location', $selectedInbound)) {
                        continue;
                    }
                    $this->planInbound->create([
                        'id_plan_realization' => $planRealizationId,
                        'id_booking' => $inbound['id_booking'],
                        'item_name' => $inbound['item_name'],
                        'unloading_location' => $selectedInbound['location'],
                        'special_equipment' => 'Labor:' . $selectedInbound['resources']['labor'] . ', Forklift:' . $selectedInbound['resources']['forklift'] . ', Crane:' . $selectedInbound['resources']['crane'],
                        'sppb_date' => $inbound['sppb_date'],
                        'inbound_date' => $inbound['inbound_date'],
                        'party_20' => $inbound['party_20'],
                        'party_40' => $inbound['party_40'],
                        'party_lcl' => $inbound['party_lcl'],
                        'realization_20' => $inbound['realization_20'],
                        'realization_40' => $inbound['realization_40'],
                        'realization_lcl' => $inbound['realization_lcl'],
                        'description' => $selectedInbound['description'],
                    ]);
                    $planInboundId = $this->db->insert_id();

                    $this->planResource->create([
                        'id_reference' => $planInboundId,
                        'type' => PlanResourceModel::TYPE_INBOUND,
                        'resource' => PlanResourceModel::RESOURCE_LABOR,
                        'unit' => 'People',
                        'plan' => $selectedInbound['resources']['labor'],
                    ]);
                    $this->planResource->create([
                        'id_reference' => $planInboundId,
                        'type' => PlanResourceModel::TYPE_INBOUND,
                        'resource' => PlanResourceModel::RESOURCE_FORKLIFT,
                        'unit' => 'Unit',
                        'plan' => $selectedInbound['resources']['forklift'],
                    ]);
                    $this->planResource->create([
                        'id_reference' => $planInboundId,
                        'type' => PlanResourceModel::TYPE_INBOUND,
                        'resource' => PlanResourceModel::RESOURCE_CRANE,
                        'unit' => 'Unit',
                        'plan' => $selectedInbound['resources']['crane'],
                    ]);

                    if (key_exists('tep', $selectedInbound)) {
                        foreach ($selectedInbound['tep'] as $tep) {
                            $this->planTransporterEntryPermit->create([
                                'id_plan_realization' => $planRealizationId,
                                'id_transporter_entry_permit' => $tep
                            ]);
                        }
                    }
                }
            }

            foreach ($dataOutbounds as $outbound) {
                if (key_exists($outbound['id_booking'], $outbounds)) {
                    $selectedOutbound = $outbounds[$outbound['id_booking']];
                    $this->planOutbound->create([
                        'id_plan_realization' => $planRealizationId,
                        'id_booking' => $outbound['id_booking'],
                        'item_name' => $outbound['item_name'],
                        'loading_location' => $outbound['loading_location'],
                        'special_equipment' => 'Labor:' . $selectedOutbound['resources']['labor'] . ', Forklift:' . $selectedOutbound['resources']['forklift'] . ', Crane:' . $selectedOutbound['resources']['crane'],
                        'sppb_date' => $outbound['sppb_date'],
                        'instruction_date' => $outbound['instruction_date'],
                        'party_20' => $outbound['party_20'],
                        'party_40' => $outbound['party_40'],
                        'party_lcl' => $outbound['party_lcl'],
                        'realization_20' => $outbound['realization_20'],
                        'realization_40' => $outbound['realization_40'],
                        'realization_lcl' => $outbound['realization_lcl'],
                        'description' => $selectedOutbound['description'],
                    ]);
                    $planOutboundId = $this->db->insert_id();

                    $this->planResource->create([
                        'id_reference' => $planOutboundId,
                        'type' => PlanResourceModel::TYPE_OUTBOUND,
                        'resource' => PlanResourceModel::RESOURCE_LABOR,
                        'unit' => 'People',
                        'plan' => $selectedOutbound['resources']['labor'],
                    ]);
                    $this->planResource->create([
                        'id_reference' => $planOutboundId,
                        'type' => PlanResourceModel::TYPE_OUTBOUND,
                        'resource' => PlanResourceModel::RESOURCE_FORKLIFT,
                        'unit' => 'Unit',
                        'plan' => $selectedOutbound['resources']['forklift'],
                    ]);
                    $this->planResource->create([
                        'id_reference' => $planOutboundId,
                        'type' => PlanResourceModel::TYPE_OUTBOUND,
                        'resource' => PlanResourceModel::RESOURCE_CRANE,
                        'unit' => 'Unit',
                        'plan' => $selectedOutbound['resources']['crane'],
                    ]);

                    if (key_exists('tep', $selectedOutbound)) {
                        foreach ($selectedOutbound['tep'] as $tep) {
                            $this->planTransporterEntryPermit->create([
                                'id_plan_realization' => $planRealizationId,
                                'id_transporter_entry_permit' => $tep
                            ]);
                        }
                    }
                }
            }

            $result = $this->getPlanRealizationData($planRealizationId);
            unset($result['planRealization']);
            $this->statusHistory->create([
                'id_reference' => $planRealizationId,
                'type' => StatusHistoryModel::TYPE_PLAN_REALIZATION,
                'status' => PlanRealizationModel::STATUS_OPEN,
                'description' => 'Create plan',
                'data' => json_encode($result)
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                if ($send) {
                    $this->sendNotificationToGroup($planRealizationId);
                }
                flash('success', "Plan {$date} successfully created", 'plan-realization');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->create();
    }

    /**
     * Edit detail plan realization.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PLAN_REALIZATION_EDIT);

        $data = $this->getPlanRealizationData($id);
        $data['statusHistories'] = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_PLAN_REALIZATION,
            'status_histories.id_reference' => $id
        ]);

        $inbounds = $this->reportPlanRealization->getInbound();
        $outbounds = $this->reportPlanRealization->getOutbound();
        $outstandingTep = $this->transporterEntryPermit->getBy([
            'transporter_entry_permits.checked_in_at IS NULL' => null,
            'transporter_entry_permits.created_at>=' => '2021-01-01',
            //'NOW()<=transporter_entry_permits.expired_at' => null,
        ]);
        foreach ($inbounds as &$inbound) {
            $inbound['transporter_entry_permits'] = array_filter($outstandingTep, function ($tep) use ($inbound) {
                return $tep['id_booking'] == $inbound['id_booking'];
            });
        }

        foreach ($outbounds as &$outbound) {
            $outbound['transporter_entry_permits'] = array_filter($outstandingTep, function ($tep) use ($outbound) {
                return $tep['id_booking'] == $outbound['id_booking'] || (($tep['id_customer'] ?? $tep['id_customer_out']) == $outbound['id_customer'] && $tep['tep_category'] == 'OUTBOUND');
            });
        }
        $data['newInbounds'] = $inbounds;
        $data['newOutbounds'] = $outbounds;

        $this->render('plan_realization/edit', $data);
    }

    /**
     * Update plan and realization.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PLAN_REALIZATION_EDIT);

        if ($this->validate()) {
            $existingInbounds = if_empty($this->input->post('existing_inbounds'), []);
            $existingOutbounds = if_empty($this->input->post('existing_outbounds'), []);
            $inbounds = if_empty($this->input->post('inbounds'), []);
            $outbounds = if_empty($this->input->post('outbounds'), []);
            $description = $this->input->post('description');
            $send = $this->input->post('send');

            $dataInbounds = $this->reportPlanRealization->getInbound();
            $dataOutbounds = $this->reportPlanRealization->getOutbound();

            $this->db->trans_start();

            $this->planRealization->update([
                'description' => $description,
            ], $id);

            // deleted removed bookings
            $currentInbounds = $this->planInbound->getBy(['id_plan_realization' => $id]);
            foreach ($currentInbounds as $currentInbound) {
                if (!in_array($currentInbound['id'], $existingInbounds)) {
                    $this->planInbound->delete($currentInbound['id']);
                }
            }
            $currentOutbounds = $this->planOutbound->getBy(['id_plan_realization' => $id]);
            foreach ($currentOutbounds as $currentOutbound) {
                if (!in_array($currentOutbound['id'], $existingOutbounds)) {
                    $this->planOutbound->delete($currentOutbound['id']);
                }
            }

            // insert new booking
            foreach ($dataInbounds as $inbound) {
                if (key_exists($inbound['id_booking'], $inbounds)) {
                    $selectedInbound = $inbounds[$inbound['id_booking']];
                    if(!key_exists('location', $selectedInbound)) {
                        continue;
                    }
                    $this->planInbound->create([
                        'id_plan_realization' => $id,
                        'id_booking' => $inbound['id_booking'],
                        'item_name' => $inbound['item_name'],
                        'unloading_location' => $selectedInbound['location'],
                        'special_equipment' => 'Labor:' . $selectedInbound['resources']['labor'] . ', Forklift:' . $selectedInbound['resources']['forklift'] . ', Crane:' . $selectedInbound['resources']['crane'],
                        'sppb_date' => $inbound['sppb_date'],
                        'inbound_date' => $inbound['inbound_date'],
                        'party_20' => $inbound['party_20'],
                        'party_40' => $inbound['party_40'],
                        'party_lcl' => $inbound['party_lcl'],
                        'realization_20' => $inbound['realization_20'],
                        'realization_40' => $inbound['realization_40'],
                        'realization_lcl' => $inbound['realization_lcl'],
                        'description' => $selectedInbound['description'],
                    ]);
                    $planInboundId = $this->db->insert_id();

                    $this->planResource->create([
                        'id_reference' => $planInboundId,
                        'type' => PlanResourceModel::TYPE_INBOUND,
                        'resource' => PlanResourceModel::RESOURCE_LABOR,
                        'unit' => 'People',
                        'plan' => $selectedInbound['resources']['labor'],
                    ]);
                    $this->planResource->create([
                        'id_reference' => $planInboundId,
                        'type' => PlanResourceModel::TYPE_INBOUND,
                        'resource' => PlanResourceModel::RESOURCE_FORKLIFT,
                        'unit' => 'Unit',
                        'plan' => $selectedInbound['resources']['forklift'],
                    ]);
                    $this->planResource->create([
                        'id_reference' => $planInboundId,
                        'type' => PlanResourceModel::TYPE_INBOUND,
                        'resource' => PlanResourceModel::RESOURCE_CRANE,
                        'unit' => 'Unit',
                        'plan' => $selectedInbound['resources']['crane'],
                    ]);

                    if (key_exists('tep', $selectedInbound)) {
                        foreach ($selectedInbound['tep'] as $tep) {
                            $this->planTransporterEntryPermit->create([
                                'id_plan_realization' => $id,
                                'id_transporter_entry_permit' => $tep
                            ]);
                        }
                    }
                }
            }

            foreach ($dataOutbounds as $outbound) {
                if (key_exists($outbound['id_booking'], $outbounds)) {
                    $selectedOutbound = $outbounds[$outbound['id_booking']];
                    $this->planOutbound->create([
                        'id_plan_realization' => $id,
                        'id_booking' => $outbound['id_booking'],
                        'item_name' => $outbound['item_name'],
                        'loading_location' => $outbound['loading_location'],
                        'special_equipment' => 'Labor:' . $selectedOutbound['resources']['labor'] . ', Forklift:' . $selectedOutbound['resources']['forklift'] . ', Crane:' . $selectedOutbound['resources']['crane'],
                        'sppb_date' => $outbound['sppb_date'],
                        'instruction_date' => $outbound['instruction_date'],
                        'party_20' => $outbound['party_20'],
                        'party_40' => $outbound['party_40'],
                        'party_lcl' => $outbound['party_lcl'],
                        'realization_20' => $outbound['realization_20'],
                        'realization_40' => $outbound['realization_40'],
                        'realization_lcl' => $outbound['realization_lcl'],
                        'description' => $selectedOutbound['description'],
                    ]);
                    $planOutboundId = $this->db->insert_id();

                    $this->planResource->create([
                        'id_reference' => $planOutboundId,
                        'type' => PlanResourceModel::TYPE_OUTBOUND,
                        'resource' => PlanResourceModel::RESOURCE_LABOR,
                        'unit' => 'People',
                        'plan' => $selectedOutbound['resources']['labor'],
                    ]);
                    $this->planResource->create([
                        'id_reference' => $planOutboundId,
                        'type' => PlanResourceModel::TYPE_OUTBOUND,
                        'resource' => PlanResourceModel::RESOURCE_FORKLIFT,
                        'unit' => 'Unit',
                        'plan' => $selectedOutbound['resources']['forklift'],
                    ]);
                    $this->planResource->create([
                        'id_reference' => $planOutboundId,
                        'type' => PlanResourceModel::TYPE_OUTBOUND,
                        'resource' => PlanResourceModel::RESOURCE_CRANE,
                        'unit' => 'Unit',
                        'plan' => $selectedOutbound['resources']['crane'],
                    ]);

                    if (key_exists('tep', $selectedOutbound)) {
                        foreach ($selectedOutbound['tep'] as $tep) {
                            $this->planTransporterEntryPermit->create([
                                'id_plan_realization' => $id,
                                'id_transporter_entry_permit' => $tep
                            ]);
                        }
                    }
                }
            }

            $result = $this->getPlanRealizationData($id);
            unset($result['planRealization']);
            $this->statusHistory->create([
                'id_reference' => $id,
                'type' => StatusHistoryModel::TYPE_PLAN_REALIZATION,
                'status' => PlanRealizationModel::STATUS_OPEN,
                'description' => 'Update plan',
                'data' => json_encode($result)
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                if ($send) {
                    $this->sendNotificationToGroup($id, true);
                }
                flash('success', "Plan successfully updated", 'plan-realization');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->edit($id);
    }


    /**
     * Send notification to branch group.
     * @param $id
     */
    public function send_plan_realization($id)
    {
        if (!empty(get_active_branch('whatsapp_group'))) {
            $send = $this->sendNotificationToGroup($id);
            $planRealization = $this->planRealization->getById($id);
            $type = ($planRealization['status'] == PlanRealizationModel::STATUS_CLOSED ? 'Realization' : 'Plan');
            if (is_array($send) && (!get_if_exist($send, 'sent') || !empty(get_if_exist($send, 'error')))) {
                flash('danger', $type . ' is failed to be sent, try again or contact administrator');
            } else {
                flash('success', $type . ' is successfully sent');
            }
        } else {
            flash('danger', 'No whatsapp group available for this branch');
        }
        redirect('plan-realization');
    }

    /**
     * Send notification to branch group.
     *
     * @param $id
     * @param bool $isUpdated
     * @return array|bool
     */
    private function sendNotificationToGroup($id, $isUpdated = false)
    {
        $additionalMessage = $this->input->post('message');

        $data = $this->getPlanRealizationData($id);
        $planRealization = $data['planRealization'];
        $type = ($planRealization['status'] == PlanRealizationModel::STATUS_CLOSED ? 'Realization' : 'Plan');
        $whatsappBranch = get_active_branch('whatsapp_group');

        $page = $this->load->view('plan_realization/print', $data, true);
        $pdf = $this->exporter->exportToPdf('plan-realization', $page, ['paper' => 'A4', 'orientation' => 'landscape', 'buffer' => true]);
        $pdfFileName = url_title($type . ' ' . $planRealization['date']) . '.pdf';
        file_put_contents(FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . $pdfFileName, $pdf);
        //$base64EncodedData = 'data:application/pdf;base64,' . base64_encode($pdf);

        if (!empty($whatsappBranch)) {
            $message = "*" . strtoupper($type) . " " . $planRealization['date'] . ": " . ($isUpdated ? 'UPDATED' : '') . "*\n";
            $message .= "*BRANCH*:" . $planRealization['branch'] . "\n";
            $message .= "*TOTAL INBOUND*:" . $planRealization['total_inbound'] . "\n";
            $message .= "*TOTAL OUTBOUND*:" . $planRealization['total_outbound'] . "\n";
            if ($type == 'Plan') {
                $message .= "*PLAN DESCRIPTION:*\n";
                $message .= if_empty($planRealization['description'], 'No description') . "*\n";
            } else {
                $message .= "*PLAN ANALYSIS:*\n";
                $message .= if_empty($planRealization['analysis'], 'No analysis') . "*\n";
            }
            $message .= "*MESSAGE:*\n";
            $message .= if_empty($additionalMessage, 'No additional message');
            $send = $this->notification->broadcast([
                'url' => 'sendFile',
                'method' => 'POST',
                'payload' => [
                    'chatId' => detect_chat_id($whatsappBranch),
                    'body' => base_url('uploads/temp/' . $pdfFileName),
                    'filename' => $type . ' ' . url_title($planRealization['date']) . '.pdf',
                    'caption' => $message
                ]
            ], NotificationModel::TYPE_CHAT_PUSH);

            return $send;
        }
        return false;
    }

    /**
     * Show close plan realization form.
     *
     * @param $id
     */
    public function close($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PLAN_REALIZATION_EDIT);

        $planRealization = $this->planRealization->getById($id);
        $resources = $this->planResource->getBy([
            'id_reference' => $id,
            'plan_resources.type' => PlanResourceModel::TYPE_ALL,
            'compare' => true,
            'compare_date' => $planRealization['date']
        ]);
        $inbounds = $this->planInbound->getBy(['id_plan_realization' => $id, 'compare' => true]);
        foreach ($inbounds as &$inbound) {
            $inbound['resources'] = $this->planResource->getBy([
                'plan_resources.id_reference' => $inbound['id'],
                'plan_resources.type' => PlanResourceModel::TYPE_INBOUND,
                'compare' => true,
                'compare_date' => $planRealization['date']
            ]);
        }
        $outbounds = $this->planOutbound->getBy(['id_plan_realization' => $id, 'compare' => true, 'compare_date' => $planRealization['date']]);
        foreach ($outbounds as &$outbound) {
            $outbound['resources'] = $this->planResource->getBy([
                'plan_resources.id_reference' => $outbound['id'],
                'plan_resources.type' => PlanResourceModel::TYPE_OUTBOUND,
                'compare' => true,
                'compare_date' => $planRealization['date']
            ]);
        }

        $this->render('plan_realization/close', compact('planRealization', 'resources', 'inbounds', 'outbounds'));
    }

    /**
     * Close realization.
     *
     * @param $id
     */
    public function close_realization($id)
    {
        if ($this->validate(['analysis' => 'required|max_length[1000]'])) {
            $inboundSkips = $this->input->post('inbound_skips') ?? [];
            $outboundSkips = $this->input->post('outbound_skips') ?? [];
            $analysis = $this->input->post('analysis');
            $send = $this->input->post('send');

            $planRealization = $this->planRealization->getById($id);
            $resources = $this->planResource->getBy([
                'id_reference' => $id,
                'plan_resources.type' => PlanResourceModel::TYPE_ALL,
                'compare' => true,
                'compare_date' => $planRealization['date']
            ]);
            $inbounds = $this->planInbound->getBy(['id_plan_realization' => $id, 'compare' => true]);
            foreach ($inbounds as &$inboundResourceData) {
                $inboundResourceData['resources'] = $this->planResource->getBy([
                    'plan_resources.id_reference' => $inboundResourceData['id'],
                    'plan_resources.type' => PlanResourceModel::TYPE_INBOUND,
                    'compare' => true,
                    'compare_date' => $planRealization['date']
                ]);
            }
            $outbounds = $this->planOutbound->getBy(['id_plan_realization' => $id, 'compare' => true, 'compare_date' => $planRealization['date']]);
            foreach ($outbounds as &$outboundResourceData) {
                $outboundResourceData['resources'] = $this->planResource->getBy([
                    'plan_resources.id_reference' => $outboundResourceData['id'],
                    'plan_resources.type' => PlanResourceModel::TYPE_OUTBOUND,
                    'compare' => true,
                    'compare_date' => $planRealization['date']
                ]);
            }

            $this->db->trans_start();

            $this->planRealization->update([
                'analysis' => $analysis,
                'status' => PlanRealizationModel::STATUS_CLOSED
            ], $id);

            foreach ($resources as $resource) {
                $this->planResource->update([
                    'close_realization' => $resource['current_realization']
                ], $resource['id']);
            }

            foreach ($inbounds as $inbound) {
                $this->planInbound->update([
                    'close_realization_20' => $inbound['current_realization_20'],
                    'close_realization_40' => $inbound['current_realization_40'],
                    'close_realization_lcl' => $inbound['current_realization_lcl'],
                    'realization_location' => $inbound['current_unloading_location'],
                    'is_skipped' => in_array($inbound['id_booking'], $inboundSkips),
                ], $inbound['id']);

                foreach ($inbound['resources'] as $inboundResource) {
                    $this->planResource->update([
                        'close_realization' => $inboundResource['current_realization']
                    ], $inboundResource['id']);
                }
            }

            foreach ($outbounds as $outbound) {
                $this->planOutbound->update([
                    'close_realization_20' => $outbound['current_realization_20'],
                    'close_realization_40' => $outbound['current_realization_40'],
                    'close_realization_lcl' => $outbound['current_realization_lcl'],
                    'realization_location' => $outbound['current_unloading_location'],
                    'is_skipped' => in_array($outbound['id_booking'], $outboundSkips),
                ], $outbound['id']);

                foreach ($outbound['resources'] as $outboundResource) {
                    $this->planResource->update([
                        'close_realization' => $outboundResource['current_realization']
                    ], $outboundResource['id']);
                }
            }

            $result = $this->getPlanRealizationData($id);
            unset($result['planRealization']);
            $this->statusHistory->create([
                'id_reference' => $id,
                'type' => StatusHistoryModel::TYPE_PLAN_REALIZATION,
                'status' => PlanRealizationModel::STATUS_CLOSED,
                'description' => 'Close realization',
                'data' => json_encode($result)
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                if ($send) {
                    $this->sendNotificationToGroup($id);
                }
                flash('success', "Plan {$planRealization['date']} successfully realize closed", 'plan-realization');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->close($id);
    }

    /**
     * Perform deleting plan realization data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AUCTION_DELETE);

        $planRealization = $this->planRealization->getById($id);

        if ($this->planRealization->delete($id, true)) {
            flash('warning', "Plan {$planRealization['date']} is successfully deleted");
        } else {
            flash('danger', "Delete plan {$planRealization['date']} failed");
        }
        redirect('plan-realization');
    }

    /**
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'branch' => 'trim|integer|is_natural_no_zero',
            'description' => 'trim|max_length[500]',
        ];
    }
}