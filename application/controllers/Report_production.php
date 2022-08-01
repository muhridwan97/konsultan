<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Report_production
 * @property ReportVehicleProductionModel $reportVehicleProduction
 * @property OperationCutOffModel $operationCutOff
 * @property BranchModel $branch
 * @property Exporter $exporter
 */
class Report_production extends MY_Controller
{
    /**
     * Report_production constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ReportVehicleProductionModel', 'reportVehicleProduction');
        $this->load->model('OperationCutOffModel', 'operationCutOff');
        $this->load->model('BranchModel', 'branch');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'fleet_production_activity' => 'GET',
        ]);
    }

    /**
     * Show data fleet production activity
     */
    public function fleet_production_activity()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_REPORT_FLEET_PRODUCTION_ACTIVITY);

        $filters = get_url_param('filter') ? $_GET : [];
        $export = get_url_param('export');

        $inbounds = [];
        $outbounds = [];
        $dataRanges = [];
        if (!empty($filters) || $export) {
            $dateFrom = $this->input->get('date_from');
            $dateTo = $this->input->get('date_to');
            $branches = $this->input->get('branch');

            $filters['date_cut_off'] = $dataRanges = $this->operationCutOff->getRangeDatePerBranch($dateFrom, $dateTo, $branches);

            $incompleteShiftSetup = null;
            foreach ($dataRanges as $dataRange) {
                if (empty($dataRange['start']) || empty($dataRange['end'])) {
                    $incompleteShiftSetup = $dataRange;
                    break;
                }
            }
            if (!empty($incompleteShiftSetup)) {
                $dataRanges = [];
                flash('danger', "Branch {$incompleteShiftSetup['branch']} has no operation cut off time setting");
            } else {
                $cacheKey = 'fleet-production-' . md5(json_encode($filters));
                $cachedTransactions = cache_remember($cacheKey, 60, function () use ($filters) {
                    $inbounds = $this->reportVehicleProduction->getReportFleetProductionInbound($filters);
                    $outbounds = $this->reportVehicleProduction->getReportFleetProductionOutbound($filters);
                    return compact('inbounds', 'outbounds');
                });
                $inbounds = $cachedTransactions['inbounds'];
                $outbounds = $cachedTransactions['outbounds'];
            }
        }

        if ($export) {
            try {
                $this->reportVehicleProduction->exportFleetProductionActivity($filters, $inbounds, $outbounds, $dataRanges);
            } catch (Exception $e) {
                flash('danger', 'Cannot export data to excel: ' . $e->getMessage());
            }
        } else {
            $branches = $this->branch->getAll();

            $this->render('report_production/fleet_production_activity', compact('inbounds', 'outbounds', 'branches', 'dataRanges'));
        }
    }

}
