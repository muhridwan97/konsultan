<?php

use Carbon\Carbon;

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Automate
 * @property ReportModel $reportModel
 * @property ReportBCModel $reportBcModel
 * @property ReportTppModel $reportTppModel
 * @property ReportStockModel $reportStock
 * @property ReportPerformanceModel $reportPerformance
 * @property BookingModel $bookingModel
 * @property BookingStatusModel $bookingStatus
 * @property PeopleModel $peopleModel
 * @property PeopleBranchModel $peopleBranchModel
 * @property PeopleContactModel $peopleContactModel
 * @property WorkOrderDocumentModel $workOrderDocument
 * @property BranchModel $branchModel
 * @property CalendarModel $calendarModel
 * @property UploadModel $upload
 * @property UploadDocumentModel $uploadDocument
 * @property DocumentTypeModel $documentType
 * @property HolidayModel $holiday
 * @property OpnameModel $opname
 * @property OpnameContainerModel $opnameContainer
 * @property OpnameGoodsModel $opnameGoods
 * @property CycleCountModel $cycleCount
 * @property SafeConductModel $safeConduct
 * @property SafeConductAttachmentModel $safeConductAttachment
 * @property ModuleModel $module
 * @property Mailer $mailer
 * @property Exporter $exporter
 * @property ReportScheduleModel $reportSchedule
 * @property ReportTpsModel $reportTps
 * @property ReportVehicleProductionModel $reportVehicleProduction
 * @property UserModel $user
 * @property WorkOrderModel $workOrder
 * @property WorkOrderHistoryLockModel $workOrderHistoryLock
 * @property WorkOrderLockAutomateModel $workOrderLockAutomate
 * @property DeliveryTrackingModel $deliveryTracking
 * @property DeliveryTrackingDetailModel $deliveryTrackingDetail
 * @property DeliveryTrackingAssignmentModel $deliveryTrackingAssignment
 * @property DeliveryTrackingGoodsModel $deliveryTrackingGoods
 * @property WorkOrderOvertimeChargeModel $workOrderOvertimeCharge
 * @property TransporterEntryPermitModel $transporterEntryPermit
 * @property TransporterEntryPermitTrackingModel $transporterEntryPermitTracking
 * @property CustomerStorageCapacityModel $customerStorageCapacity
 * @property ReportStorageModel $reportStorage
 * @property StorageUsageModel $storageUsage
 * @property StorageUsageCustomerModel $storageUsageCustomer
 * @property StorageOverSpaceModel $storageOverSpace
 * @property StorageOverSpaceCustomerModel $storageOverSpaceCustomer
 * @property StorageOverSpaceActivityModel $storageOverSpaceActivity
 * @property SafeConductGoodsModel $safeConductGoods
 * @property StatusHistoryModel $statusHistory
 * @property DashboardModel $dashboard
 * @property ComplainKpiModel $complainKpi
 * @property ComplainHistoryModel $complainHistory
 * @property ComplainKpiWhatsappModel $complainKpiWhatsapp
 * @property NotificationModel $notification
 * @property CustomerStorageJob $customerStorageJob
 * @property PhBidOrderSummaryModel $phBidOrderSummary
 * @property PhBidOrderContainerModel $phBidOrderContainer
 * @property OperationCutOffModel $operationCutOff
 * @property PutAwayModel $putAway
 * @property PutAwayGoodsModel $putAwayGoods
 * @property WorkOrderGoodsModel $workOrderGoods
 * @property DiscrepancyHandoverModel $discrepancyHandover
 * @property HeavyEquipmentModel $heavyEquipment
 * @property HeavyEquipmentEntryPermitModel $heavyEquipmentEntryPermit
 * @property AttendanceModel $attendance
 * @property OperationCutOffScheduleModel $operationCutOffSchedule
 * @property DeliveryInspectionModel $deliveryInspection
 * @property DeliveryInspectionDetailModel $deliveryInspectionDetail
 */
class Automate extends CI_Controller
{
    private $email_bcc = [
        'mkt_mgr@transcon-indonesia.com',
        //'tpp_mgr@transcon-indonesia.com',
        'cso1@transcon-indonesia.com',
        'it@transcon-indonesia.com',
        'it1@transcon-indonesia.com',
        'it2@transcon-indonesia.com',
        'it5@transcon-indonesia.com',
        'opr_div2@transcon-indonesia.com',
        'nandi@mgesolution.com',
        'direktur@transcon-indonesia.com'
    ];

    /**
     * Automate constructor.
     */
    public function __construct()
    {
        parent::__construct();
        if (is_cli() || ENVIRONMENT == 'development') {
            //echo 'Warehouse automate module is initiating...' . PHP_EOL;
        } else {
            echo "This module is CLI only!" . PHP_EOL;
            die();
        }

        $this->load->model('UploadModel', 'upload');
        $this->load->model('BranchModel', 'branchModel');
        $this->load->model('CalendarModel', 'calendarModel');
        $this->load->model('BookingModel', 'bookingModel');
        $this->load->model('BookingTypeModel', 'bookingTypeModel');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('BookingStatusModel', 'bookingStatus');
        $this->load->model('PeopleModel', 'peopleModel'); 
        $this->load->model('PeopleBranchModel', 'peopleBranchModel');
        $this->load->model('PeopleContactModel', 'peopleContactModel');
        $this->load->model('PeopleDocumentTypeReminderModel', 'peopleDocumentTypeReminder');
        $this->load->model('SafeConductModel', 'safeConduct');
        $this->load->model('SafeConductAttachmentModel', 'safeConductAttachment');
        $this->load->model('ReportModel', 'reportModel');
        $this->load->model('ReportBCModel', 'reportBcModel');
        $this->load->model('ReportTppModel', 'reportTppModel');
        $this->load->model('ReportStockModel', 'reportStock');
        $this->load->model('ReportStorageModel', 'reportStorage');

        $this->load->model('UploadDocumentModel', 'uploadDocument');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('UserModel', 'user');

        $this->load->model('OpnameModel', 'opname');
        $this->load->model('OpnameGoodsModel', 'opnameGoods');
        $this->load->model('OpnameContainerModel', 'opnameContainer');
        $this->load->model('CycleCountModel', 'cycleCount');
        $this->load->model('CycleCountGoodsModel', 'cycleCountGoods');
        $this->load->model('CycleCountContainerModel', 'cycleCountContainer');
        $this->load->model('HolidayModel', 'holiday');
        $this->load->model('modules/Mailer', 'mailer');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('NotificationModel', 'notification');

        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('WorkOrderHistoryLockModel', 'workOrderHistoryLock');
        $this->load->model('WorkOrderLockAutomateModel', 'workOrderLockAutomate');
        $this->load->model('WorkOrderOvertimeChargeModel', 'workOrderOvertimeCharge');

        $this->load->model('DeliveryTrackingModel', 'deliveryTracking');
        $this->load->model('DeliveryTrackingDetailModel', 'deliveryTrackingDetail');
        $this->load->model('DeliveryTrackingAssignmentModel', 'deliveryTrackingAssignment');
        $this->load->model('DeliveryTrackingGoodsModel', 'deliveryTrackingGoods');

        $this->load->model('ComplainModel', 'complainModel');
        $this->load->model('ComplainCategoryModel', 'complainCategoryModel');
        $this->load->model('DepartmentModel', 'departmentModel');
        $this->load->model('EmployeeModel', 'employeeModel'); 
        $this->load->model('ServiceHourModel', 'serviceHourModel');
        $this->load->model('ComplainKpiModel', 'complainKpi');
        $this->load->model('ComplainHistoryModel', 'complainHistory');
        $this->load->model('ComplainKpiWhatsappModel', 'complainKpiWhatsapp');
        
        $this->load->model('OpnameSpaceModel', 'OpnameSpace');
        $this->load->model('OpnameSpaceBookingInModel', 'opnameSpaceBookingIn');
        $this->load->model('OpnameSpaceUploadHistoryModel', 'opnameSpaceUploadHistory');
        $this->load->model('OpnameSpaceCheckHistoryModel', 'opnameSpaceCheckHistory');
        $this->load->model('StatusHistoryModel', 'statusHistory');

        $this->load->model('ReportScheduleModel', 'reportSchedule');
        $this->load->model('ReportTpsModel', 'reportTps');

        $this->load->model('TransporterEntryPermitModel', 'transporterEntryPermit');
        $this->load->model('CustomerStorageCapacityModel', 'customerStorageCapacity');
        $this->load->model('DashboardModel', 'dashboard');
        
        $this->load->model('OperationCutOffModel', 'operationCutOff');
        $this->load->model('PutAwayModel', 'putAway');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('PutAwayGoodsModel', 'putAwayGoods');
        $this->load->model('HeavyEquipmentModel', 'heavyEquipment');
        $this->load->model('HeavyEquipmentEntryPermitModel', 'heavyEquipmentEntryPermit');
        $this->load->model('AttendanceModel', 'attendance');
        $this->load->model('OperationCutOffScheduleModel', 'operationCutOffSchedule');

        ini_set('memory_limit', '32384M');
    }

    /**
     * Index automation command.
     */
    public function index()
    {
        echo "Available automation command:" . PHP_EOL;
        echo '- automate clean_old_temp param:days' . PHP_EOL;
        echo '- automate application_backup param:destination' . PHP_EOL;
    }

    /**
     * Clean old temp upload files.
     * call in terminal: `php index.php automate clean_old_temp 14`
     * @param int $age in days
     */
    public function clean_old_temp($age = 7)
    {
        $this->load->helper('directory');

        $path = './uploads/temp/';
        $map = directory_map($path, 1);
        $totalOldFiles = 0;
        foreach ($map as $file) {
            if (file_exists($path . $file)) {
                $today = new DateTime();
                $fileTimestamp = new DateTime(date("F d Y H:i:s.", filectime($path . $file)));
                $interval = $today->diff($fileTimestamp)->format('%R%a');
                if (intval($interval) <= -$age && $file != '.gitkeep') {
                    if (unlink($path . $file)) {
                        echo 'File: ' . ($path . $file) . ' was deleted<br>';
                        $totalOldFiles++;
                    }
                }
            }
        }
        echo $totalOldFiles . ' files were deleted (more than ' . $age . ' days old)' . PHP_EOL;
    }

    /**
     * Perform silent backup to specific destination including application and database.
     * call in terminal: `php index.php automate application_backup`
     * @param bool $dbOnly
     * @param bool $dbModule
     * @param string $destination
     */
    public function application_backup($dbOnly = true, $dbModule = true, $destination = '/var/www/backup/')
    {
        echo 'Backup is getting started...' . PHP_EOL;

        $this->load->model('ModuleModel', 'module');
        $this->load->library('zip');
        $this->load->helper('file');
        $this->load->dbutil();

        if (file_exists($destination)) {
            $backupPath = $destination . 'backup_app_' . date('Ymd_His') . '.zip';

            if ($dbOnly == 'true') {
                $path = './';
                $this->zip->read_dir($path, false);
            }

            $this->dbutil->backup([
                'format' => 'zip',
                'filename' => $this->db->database . '.sql'
            ]);

            if ($dbModule == 'true') {
                $modules = $this->module->getAllModules();
                foreach ($modules as $module) {
                    $selectedModule = $this->module->getModuleById($module['id']);
                    $connection = $this->module->setConnectionByModule($selectedModule);
                    $this->load->dbutil($connection);
                    $this->dbutil->backup([
                        'format' => 'zip',
                        'filename' => $selectedModule['database'] . '.sql'
                    ]);
                }
            }

            $this->zip->archive($backupPath);
            echo 'Application backup stored at ' . $backupPath . PHP_EOL;
        } else {
            echo 'Please set valid destination path to backup: ' . $destination . ' does not exist!' . PHP_EOL;
        }
    }

    /**
     * Get chat queue
     */
    public function get_chat_queue()
    {
        $result = $this->notification->broadcast([
            'url' => 'showMessagesQueue',
            'method' => 'GET',
            'payload' => []
        ], NotificationModel::TYPE_CHAT_PUSH);

        print_debug($result);
    }

    /**
     * Reset or clear chat queue
     */
    public function clear_chat_queue()
    {
        $result = $this->notification->broadcast([
            'url' => 'clearMessagesQueue',
            'method' => 'POST',
            'payload' => []
        ], NotificationModel::TYPE_CHAT_PUSH);

        print_debug($result);
    }

    /**
     * Trigger report by scheduler data
     */
    public function report_scheduler()
    {
        echo "report_scheduler started at " . date('Y-m-d H:i:s') . PHP_EOL;
        $timestamp = strtotime('now');
        $day = date('w', $timestamp);

        $reportSchedules = $this->reportSchedule->getBy(['report_schedules.status' => ReportScheduleModel::STATUS_ACTIVE]);
        foreach ($reportSchedules as $reportSchedule) {
            $isTriggered = false;
            $time = format_date($reportSchedule['triggered_time'], 'H:i');
            switch ($reportSchedule['recurring_period']) {
                case ReportScheduleModel::PERIOD_ONE_TIME:
                    if (date('Y-m-d H:i') == format_date($reportSchedule['triggered_at'], 'Y-m-d H:i')) {
                        $isTriggered = true;
                    }
                    break;
                case ReportScheduleModel::PERIOD_DAILY:
                    if ($time == date('H:i')) {
                        $isTriggered = true;
                    }
                    break;
                case ReportScheduleModel::PERIOD_WEEKLY:
                    if ($day == $reportSchedule['triggered_day'] && $time == date('H:i')) {
                        $isTriggered = true;
                    }
                    break;
                case ReportScheduleModel::PERIOD_MONTHLY:
                    $dateLabel = date('Y-m-' . str_pad($reportSchedule['triggered_date'], 2, '0', STR_PAD_LEFT));
                    if (date('Y-m-d') == $dateLabel && $time == date('H:i')) {
                        $isTriggered = true;
                    }
                    break;
                case ReportScheduleModel::PERIOD_ANNUAL:
                    $dateLabel = date('Y-' . str_pad($reportSchedule['triggered_month'], 2, '0', STR_PAD_LEFT) . '-' . str_pad($reportSchedule['triggered_date'], 2, '0', STR_PAD_LEFT));
                    if (date('Y-m-d') == $dateLabel && $time == date('H:i')) {
                        $isTriggered = true;
                    }
                    break;
                default:
                    $isTriggered = false;
                    break;
            }

            if ($isTriggered) {
                switch ($reportSchedule['report_name']) {
                    case ReportScheduleModel::REPORT_TPS_DEFERRED:
                        echo "  send_report_deferred_tps started at " . date('Y-m-d H:i:s') . PHP_EOL;
                        $this->send_report_deferred_tps($reportSchedule);
                        echo "  send_report_deferred_tps completed at " . date('Y-m-d H:i:s') . PHP_EOL;
                        break;
                    case ReportScheduleModel::REPORT_INBOUND_OUTBOUND:
                        echo "  report_inbound_outbound started at " . date('Y-m-d H:i:s') . PHP_EOL;
                        $this->report_inbound_outbound($reportSchedule);
                        echo "  report_inbound_outbound completed at " . date('Y-m-d H:i:s') . PHP_EOL;
                        break;
                    case ReportScheduleModel::REPORT_TPS_ACTIVITY:
                        echo "  report_tps_activity started at " . date('Y-m-d H:i:s') . PHP_EOL;
                        $this->report_tps_activity($reportSchedule);
                        echo "  report_tps_activity completed at " . date('Y-m-d H:i:s') . PHP_EOL;
                        break;
                    case ReportScheduleModel::REPORT_SHIPPING_LINE_ACTIVITY:
                        echo "  report_shipping_activity started at " . date('Y-m-d H:i:s') . PHP_EOL;
                        $this->report_shipping_activity($reportSchedule);
                        echo "  report_shipping_activity completed at " . date('Y-m-d H:i:s') . PHP_EOL;
                        break;
                    //case Report...
                    //    break;
                }
            }
        }
    }

    /**
     * Report daily activity such as inbound, outbound, and handling.
     * @param int $period in day
     * @param string $periodLabel
     * @throws Exception
     */
    public function period_report($period = 7, $periodLabel = null)
    {
        if (empty($periodLabel)) {
            if ($period <= 1) {
                $periodLabel = 'daily';
            } else if ($period <= 7) {
                $periodLabel = 'weekly';
            } else if ($period <= 31) {
                $periodLabel = 'monthly';
            } else if ($period <= 366) {
                $periodLabel = 'annual';
            }
        }
        // $this->report_inbound_outbound($period, $periodLabel);
        $this->report_service_time($period, $periodLabel);
        $this->report_stock($period, $periodLabel);
        //$this->report_plan_realization($period, $periodLabel);
        $this->report_aging($period, $periodLabel);
        $this->report_mutation($period, $periodLabel);
    }

    /**
     * Report activity.
     * this report SHOULD triggered from report_scheduler() function.
     * 
     * @param $reportSchedule
     * @throws Exception
     */
    public function report_inbound_outbound($reportSchedule)
    {
        $period = 1;
        $periodLabel = 'daily';
        switch ($reportSchedule['recurring_period']) {
            case ReportScheduleModel::PERIOD_DAILY:
                $periodLabel = 'daily';
                $period = 1;
                break;
            case ReportScheduleModel::PERIOD_WEEKLY:
                $periodLabel = 'weekly';
                $period = 7;
                break;
            case ReportScheduleModel::PERIOD_MONTHLY:
                $periodLabel = 'monthly';
                $period = 30;
                break;
            case ReportScheduleModel::PERIOD_ANNUAL:
                $periodLabel = 'annual';
                $period = 365;
                break;
        }
        $date = new DateTime();
        $date->sub(new DateInterval("P{$period}D"));
        $dateFrom = $date->format('d F Y');
        $dateLabel = 'since ' . $dateFrom . ' until ' . date('d F Y');
        $getBranchByPLB = $this->branchModel->getBy(['branch_type' => "PLB"]);
        $emailSupportPLB = array_unique(array_filter(array_column($getBranchByPLB, "email_support")));
        $getBranchByTPP = $this->branchModel->getBy(['branch_type' => "TPP"]);
        $emailSupportTPP = array_unique(array_filter(array_column($getBranchByTPP, "email_support")));

        $reportBCTPPConditions = [
            'data' => 'realization',
            'date_from' => $dateFrom,
            'date_type' => 'transaction_date',
            'all_data' => true,
            'branch_type' => "TPP",
        ];

        $reportBCPLBConditions = [
            'data' => 'realization',
            'date_from' => $dateFrom,
            'date_type' => 'transaction_date',
            'all_data' => true,
            'branch_type' => "PLB",
        ];

        $TPPInbounds = $this->reportBcModel->getReportInbound($reportBCTPPConditions);
        $TPPOutbounds = $this->reportBcModel->getReportOutbound($reportBCTPPConditions);

        $plbInbounds = $this->reportBcModel->getReportInbound($reportBCPLBConditions);
        $plbOutbounds = $this->reportBcModel->getReportOutbound($reportBCPLBConditions);

        $TPPCustomers = [];
        if (!empty($TPPInbounds)) {
            $TPPCustomers = array_merge($TPPCustomers, array_column($TPPInbounds, 'id_owner'));
        }
        if (!empty($TPPOutbounds)) {
            $TPPCustomers = array_merge($TPPCustomers, array_column($TPPOutbounds, 'id_owner'));
        }

        $TPPCustomers = array_unique($TPPCustomers);
        $TPPCustomers = $this->peopleModel->getBy([
            'ref_people.id' => values($TPPCustomers, '-1')
        ]);

        $plbCustomers = [];
        if (!empty($plbInbounds)) {
            $plbCustomers = array_merge($plbCustomers, array_column($plbInbounds, 'id_owner'));
        }
        if (!empty($plbOutbounds)) {
            $plbCustomers = array_merge($plbCustomers, array_column($plbOutbounds, 'id_owner'));
        }
        $plbCustomers = array_unique($plbCustomers);
        $plbCustomers = $this->peopleModel->getBy([
            'ref_people.id' => values($plbCustomers, '-1')
        ]);

        foreach($TPPCustomers as $TPPCustomer){
            $reportBCTPPConditions['owner'] = $TPPCustomer['name'];
            $TPPInbounds = $this->reportBcModel->getReportInbound($reportBCTPPConditions);
            $TPPOutbounds = $this->reportBcModel->getReportOutbound($reportBCTPPConditions);

            if (!empty($TPPInbounds) || !empty($TPPOutbounds)) {
                $reportInData = $this->exporter->exportLargeResourceFromArray('Inbound', $TPPInbounds, false);
                $reportOutData = $this->exporter->exportLargeResourceFromArray('Outbound', $TPPOutbounds, false);

                $reportConditions = [
                    'data' => 'realization',
                    'date_from' => $dateFrom,
                    'date_type' => 'completed_at',
                    'owner' => $TPPCustomer['id'],
                    'branch_type' => "TPP",
                ];

                $reportConditions['total_size'] = 20;
                $inContainer20 = $this->reportModel->getReportActivityContainer('INBOUND', $reportConditions);
                $reportConditions['total_size'] = 40;
                $inContainer40 = $this->reportModel->getReportActivityContainer('INBOUND', $reportConditions);
                $reportConditions['total_size'] = 45;
                $inContainer45 = $this->reportModel->getReportActivityContainer('INBOUND', $reportConditions);

                $reportConditions['total_size'] = 20;
                $outContainer20 = $this->reportModel->getReportActivityContainer('OUTBOUND', $reportConditions);
                $reportConditions['total_size'] = 40;
                $outContainer40 = $this->reportModel->getReportActivityContainer('OUTBOUND', $reportConditions);
                $reportConditions['total_size'] = 45;
                $outContainer45 = $this->reportModel->getReportActivityContainer('OUTBOUND', $reportConditions);

                $reportConditions['total_size'] = 'all';
                $inGoodsTotals = $this->reportModel->getReportActivityGoods('INBOUND', $reportConditions);
                $reportConditions['total_size'] = 'quantity';
                $inGoodsQuantity = $this->reportModel->getReportActivityGoods('INBOUND', $reportConditions);
                $reportConditions['total_size'] = 'unit_weight';
                $inGoodsTonnage = $this->reportModel->getReportActivityGoods('INBOUND', $reportConditions);
                $reportConditions['total_size'] = 'unit_volume';
                $inGoodsVolume = $this->reportModel->getReportActivityGoods('INBOUND', $reportConditions);

                $reportConditions['total_size'] = 'all';
                $outGoodsTotals = $this->reportModel->getReportActivityGoods('OUTBOUND', $reportConditions);
                $reportConditions['total_size'] = 'quantity';
                $outGoodsQuantity = $this->reportModel->getReportActivityGoods('OUTBOUND', $reportConditions);
                $reportConditions['total_size'] = 'unit_weight';
                $outGoodsTonnage = $this->reportModel->getReportActivityGoods('OUTBOUND', $reportConditions);
                $reportConditions['total_size'] = 'unit_volume';
                $outGoodsVolume = $this->reportModel->getReportActivityGoods('OUTBOUND', $reportConditions);

                $tableData = $this->load->view('emails/partials/_inbound_outbound', [
                    'dateFrom' => $dateFrom,
                    'inContainer20' => $inContainer20,
                    'inContainer40' => $inContainer40,
                    'inContainer45' => $inContainer45,
                    'outContainer20' => $outContainer20,
                    'outContainer40' => $outContainer40,
                    'outContainer45' => $outContainer45,
                    'inGoodsQuantity' => $inGoodsQuantity,
                    'inGoodsTonnage' => $inGoodsTonnage,
                    'inGoodsVolume' => $inGoodsVolume,
                    'inGoodsTotal' => $inGoodsTotals,
                    'outGoodsQuantity' => $outGoodsQuantity,
                    'outGoodsTonnage' => $outGoodsTonnage,
                    'outGoodsVolume' => $outGoodsVolume,
                    'outGoodsTotal' => $outGoodsTotals,
                ], true);

                echo 'Sending activity tpp email report ' . $TPPCustomer['name'] . '...' . PHP_EOL;

                // collect email addresses
                $emailTo = $this->getEmailFromProfile($reportSchedule['send_to_type'] == ReportScheduleModel::SEND_TYPE_BRANCH_PLB_OR_TPP_SUPPORT ? ReportScheduleModel::SEND_TYPE_BRANCH_TPP_SUPPORT : $reportSchedule['send_to_type'], $reportSchedule['send_to'], $TPPCustomer);
                $emailCC = $this->getEmailFromProfile($reportSchedule['send_cc_type'] == ReportScheduleModel::SEND_TYPE_BRANCH_PLB_OR_TPP_SUPPORT ? ReportScheduleModel::SEND_TYPE_BRANCH_TPP_SUPPORT : $reportSchedule['send_cc_type'], $reportSchedule['send_cc'], $TPPCustomer);
                $emailBCC = $this->getEmailFromProfile($reportSchedule['send_bcc_type'] == ReportScheduleModel::SEND_TYPE_BRANCH_PLB_OR_TPP_SUPPORT ? ReportScheduleModel::SEND_TYPE_BRANCH_TPP_SUPPORT : $reportSchedule['send_bcc_type'], $reportSchedule['send_bcc'], $TPPCustomer);

                // if (!empty($TPPCustomer['email'])) {
                //     $emailTo = $TPPCustomer['email'];
                //     $emailCC = implode(",", $emailSupportTPP);
                // } else {
                //     $emailTo = implode(",", $emailSupportTPP);
                // }
                // $emailBCC = $this->email_bcc;
                $dateFileFormat = $date->format('Y_m_d');
                $attachments = [];
                if (!empty($TPPInbounds)) {
                    $attachments[] = [
                        'source' => $reportInData,
                        'disposition' => 'attachment',
                        'file_name' => "report_in_{$dateFileFormat}.xlsx",
                    ];
                }
                if (!empty($TPPOutbounds)) {
                    $attachments[] = [
                        'source' => $reportOutData,
                        'disposition' => 'attachment',
                        'file_name' => "report_out_{$dateFileFormat}.xlsx",
                    ];
                }

                $emailTitle = "Customer {$TPPCustomer['name']}: Activity TPP report {$dateLabel} ({$periodLabel})";
                $emailTemplate = 'emails/basic';
                $emailData = [
                    'title' => 'Activity Routine Report',
                    'name' => 'Transcon Management',
                    'email' => implode(",", $emailSupportTPP),
                    'content' => $tableData,
                ];

                $emailOptions = [
                    'cc' => isset($emailCC) ? $emailCC : null,
                    'bcc' => isset($emailBCC) ? $emailBCC : null,
                    'attachment' => $attachments,
                ];

                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);

            }
        }

        foreach($plbCustomers as $plbCustomer){
            $reportBCPLBConditions['owner'] = $plbCustomer['name'];

            $plbInbounds = $this->reportBcModel->getReportInbound($reportBCPLBConditions);
            $plbOutbounds = $this->reportBcModel->getReportOutbound($reportBCPLBConditions);

            if (!empty($plbInbounds) || !empty($plbOutbounds)) {
                $reportInData = $this->exporter->exportLargeResourceFromArray('Inbound', $plbInbounds, false);
                $reportOutData = $this->exporter->exportLargeResourceFromArray('Outbound', $plbOutbounds, false);

                $reportConditions = [
                    'data' => 'realization',
                    'date_from' => $dateFrom,
                    'date_type' => 'completed_at',
                    'owner' => $plbCustomer['id'],
                    'branch_type' => "PLB",
                ];

                $reportConditions['total_size'] = 20;
                $inContainer20 = $this->reportModel->getReportActivityContainer('INBOUND', $reportConditions);
                $reportConditions['total_size'] = 40;
                $inContainer40 = $this->reportModel->getReportActivityContainer('INBOUND', $reportConditions);
                $reportConditions['total_size'] = 45;
                $inContainer45 = $this->reportModel->getReportActivityContainer('INBOUND', $reportConditions);

                $reportConditions['total_size'] = 20;
                $outContainer20 = $this->reportModel->getReportActivityContainer('OUTBOUND', $reportConditions);
                $reportConditions['total_size'] = 40;
                $outContainer40 = $this->reportModel->getReportActivityContainer('OUTBOUND', $reportConditions);
                $reportConditions['total_size'] = 45;
                $outContainer45 = $this->reportModel->getReportActivityContainer('OUTBOUND', $reportConditions);

                $reportConditions['total_size'] = 'all';
                $inGoodsTotals = $this->reportModel->getReportActivityGoods('INBOUND', $reportConditions);
                $reportConditions['total_size'] = 'quantity';
                $inGoodsQuantity = $this->reportModel->getReportActivityGoods('INBOUND', $reportConditions);
                $reportConditions['total_size'] = 'unit_weight';
                $inGoodsTonnage = $this->reportModel->getReportActivityGoods('INBOUND', $reportConditions);
                $reportConditions['total_size'] = 'unit_volume';
                $inGoodsVolume = $this->reportModel->getReportActivityGoods('INBOUND', $reportConditions);

                $reportConditions['total_size'] = 'all';
                $outGoodsTotals = $this->reportModel->getReportActivityGoods('OUTBOUND', $reportConditions);
                $reportConditions['total_size'] = 'quantity';
                $outGoodsQuantity = $this->reportModel->getReportActivityGoods('OUTBOUND', $reportConditions);
                $reportConditions['total_size'] = 'unit_weight';
                $outGoodsTonnage = $this->reportModel->getReportActivityGoods('OUTBOUND', $reportConditions);
                $reportConditions['total_size'] = 'unit_volume';
                $outGoodsVolume = $this->reportModel->getReportActivityGoods('OUTBOUND', $reportConditions);

                $tableData = $this->load->view('emails/partials/_inbound_outbound', [
                    'dateFrom' => $dateFrom,
                    'inContainer20' => $inContainer20,
                    'inContainer40' => $inContainer40,
                    'inContainer45' => $inContainer45,
                    'outContainer20' => $outContainer20,
                    'outContainer40' => $outContainer40,
                    'outContainer45' => $outContainer45,
                    'inGoodsQuantity' => $inGoodsQuantity,
                    'inGoodsTonnage' => $inGoodsTonnage,
                    'inGoodsVolume' => $inGoodsVolume,
                    'inGoodsTotal' => $inGoodsTotals,
                    'outGoodsQuantity' => $outGoodsQuantity,
                    'outGoodsTonnage' => $outGoodsTonnage,
                    'outGoodsVolume' => $outGoodsVolume,
                    'outGoodsTotal' => $outGoodsTotals,
                ], true);

                echo 'Sending activity email plb report ' . $plbCustomer['name'] . '...' . PHP_EOL;

                // collect email addresses
                $emailTo = $this->getEmailFromProfile($reportSchedule['send_to_type'] == ReportScheduleModel::SEND_TYPE_BRANCH_PLB_OR_TPP_SUPPORT ? ReportScheduleModel::SEND_TYPE_BRANCH_PLB_SUPPORT : $reportSchedule['send_to_type'], $reportSchedule['send_to'], $plbCustomer);
                $emailCC = $this->getEmailFromProfile($reportSchedule['send_cc_type'] == ReportScheduleModel::SEND_TYPE_BRANCH_PLB_OR_TPP_SUPPORT ? ReportScheduleModel::SEND_TYPE_BRANCH_PLB_SUPPORT : $reportSchedule['send_cc_type'], $reportSchedule['send_cc'], $plbCustomer);
                $emailBCC = $this->getEmailFromProfile($reportSchedule['send_bcc_type'] == ReportScheduleModel::SEND_TYPE_BRANCH_PLB_OR_TPP_SUPPORT ? ReportScheduleModel::SEND_TYPE_BRANCH_PLB_SUPPORT : $reportSchedule['send_bcc_type'], $reportSchedule['send_bcc'], $plbCustomer);
                // if (!empty($plbCustomer['email'])) {
                //     $emailTo = $plbCustomer['email'];
                //     $emailCC = implode(",", $emailSupportPLB);
                // } else {
                //     $emailTo = implode(",", $emailSupportPLB);
                // }
                // $emailBCC = $this->email_bcc;

                $dateFileFormat = $date->format('Y_m_d');
                $attachments = [];
                if (!empty($plbInbounds)) {
                    $attachments[] = [
                        'source' => $reportInData,
                        'disposition' => 'attachment',
                        'file_name' => "report_in_{$dateFileFormat}.xlsx",
                    ];
                }
                if (!empty($plbOutbounds)) {
                    $attachments[] = [
                        'source' => $reportOutData,
                        'disposition' => 'attachment',
                        'file_name' => "report_out_{$dateFileFormat}.xlsx",
                    ];
                }

                $emailTitle = "Customer {$plbCustomer['name']}: Activity PLB report {$dateLabel} ({$periodLabel})";
                $emailTemplate = 'emails/basic';
                $emailData = [
                    'title' => 'Activity Routine Report',
                    'name' => 'Transcon Management',
                    'email' => implode(",", $emailSupportPLB),
                    'content' => $tableData,
                ];

                $emailOptions = [
                    'cc' => isset($emailCC) ? $emailCC : null,
                    'bcc' => isset($emailBCC) ? $emailBCC : null,
                    'attachment' => $attachments,
                ];

                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
            }
        }

        echo 'Activity email report per customer were sent...' . PHP_EOL;
    }

    /**
     * Report service time.
     * @param $period
     * @param $periodLabel
     * @param null $branch
     * @throws Exception
     */
    public function report_service_time($period, $periodLabel, $branch = null)
    {
        $date = new DateTime();
        $date->sub(new DateInterval("P{$period}D"));
        $dateFrom = $date->format('d F Y');
        $dateLabel = 'since ' . $dateFrom . ' until ' . date('d F Y');
        $getBranchByPLB = $this->branchModel->getBy(['branch_type' => "PLB"]);
        $emailSupportPLB = array_unique(array_filter(array_column($getBranchByPLB, "email_support")));
        $getBranchByTPP = $this->branchModel->getBy(['branch_type' => "TPP"]);
        $emailSupportTPP = array_unique(array_filter(array_column($getBranchByTPP, "email_support")));

        $branchName = '';
        if (!empty($branch)) {
            $branchData = $this->branchModel->getById($branch);
            if (!empty($branchData)) {
                $branchName = $branchData['branch'];
            }
        }

        $reportBCTPPConditions = [
            'data' => 'realization',
            'date_from' => $dateFrom,
            'date_type' => 'gate_in_date',
            'branch' => $branch,
            'branch_type' => "TPP",
        ];

        $reportBCPLBConditions = [
            'data' => 'realization',
            'date_from' => $dateFrom,
            'date_type' => 'gate_in_date',
            'branch' => $branch,
            'branch_type' => "PLB",
        ];

        $serviceTimeTPP = $this->reportModel->getReportServiceTime($reportBCTPPConditions);
        $serviceTimePLB = $this->reportModel->getReportServiceTime($reportBCPLBConditions);

        $reportServiceTimeDataTPP = $this->exporter->exportFromArray('Service time', $serviceTimeTPP, false);
        $reportServiceTimeDataPLB = $this->exporter->exportFromArray('Service time', $serviceTimePLB, false);

        $reportConditions['average'] = true;
        $serviceTimeAvgsTPP = $this->reportModel->getReportServiceTime($reportBCTPPConditions);
        $serviceTimeAvgsPLB = $this->reportModel->getReportServiceTime($reportBCPLBConditions);

        $tableDataTPP = $this->load->view('emails/partials/_service_time', [
            'dateFrom' => $dateFrom,
            'serviceTimeAvgs' => $serviceTimeAvgsTPP,
        ], true);

        $tableDataPLB = $this->load->view('emails/partials/_service_time', [
            'dateFrom' => $dateFrom,
            'serviceTimeAvgs' => $serviceTimeAvgsPLB,
        ], true);

        echo 'Sending service time email report...' . PHP_EOL;

        $emailToTPP = implode(",",$emailSupportTPP);
        $emailToPLB = implode(",",$emailSupportPLB);
        $emailBCC = $this->email_bcc;
        $dateFileFormat = $date->format('Y_m_d');

        // For NON PLB
        $TPPAttachments = [];
        $TPPAttachments[] = [
            'source' => $reportServiceTimeDataTPP,
            'disposition' => 'attachment',
            'file_name' => "report_service_time_{$dateFileFormat}.xlsx",
        ];

        $emailTitleTPP = "{$branchName} Service time TPP report non plb {$dateLabel} ({$periodLabel})";
        $emailTemplateTPP = 'emails/basic';
        $emailDataTPP = [
            'title' => 'Service Time Routine Report',
            'name' => 'Transcon Management',
            'email' => implode(",",$emailSupportTPP),
            'content' => $tableDataTPP,
        ];
        $emailOptionsTPP = [
            'bcc' => isset($emailBCC) ? $emailBCC : null,
            'attachment' => $TPPAttachments,
        ];

        $this->mailer->send($emailToTPP, $emailTitleTPP, $emailTemplateTPP, $emailDataTPP, $emailOptionsTPP);

        // For PLB
        $plbAttachments = [];
        $plbAttachments[] = [
            'source' => $reportServiceTimeDataPLB,
            'disposition' => 'attachment',
            'file_name' => "report_service_time_{$dateFileFormat}.xlsx",
        ];

        $emailTitlePLB = "{$branchName} Service time PLB report plb {$dateLabel} ({$periodLabel})";
        $emailTemplatePLB = 'emails/basic';
        $emailDataPLB = [
            'title' => 'Service Time Routine Report',
            'name' => 'Transcon Management',
            'email' => implode(",",$emailSupportPLB),
            'content' => $tableDataPLB,
        ];
        $emailOptionsPLB = [
            'bcc' => isset($emailBCC) ? $emailBCC : null,
            'attachment' => $plbAttachments,
        ];

        $this->mailer->send($emailToPLB, $emailTitlePLB, $emailTemplatePLB, $emailDataPLB, $emailOptionsPLB);

        echo 'Service time email reports were sent...' . PHP_EOL;
    }

    /**
     * Report stock data.
     * @param $period
     * @param $periodLabel
     * @throws Exception
     */
    public function report_stock($period, $periodLabel)
    {
        $date = new DateTime();
        $date->sub(new DateInterval("P{$period}D"));
        $dateStock = $date->format('d F Y');
        $getBranchByPLB = $this->branchModel->getBy(['branch_type' => "PLB"]);
        $emailSupportPLB = array_unique(array_filter(array_column($getBranchByPLB, "email_support")));
        $getBranchByTPP = $this->branchModel->getBy(['branch_type' => "TPP"]);
        $emailSupportTPP = array_unique(array_filter(array_column($getBranchByTPP, "email_support")));

        $reportTPPConditions = [
            'data' => 'stock',
            'stock_date' => $dateStock,
            'branch_type' => "TPP",
        ];
        $TPPContainers = $this->reportStock->getStockContainers($reportTPPConditions);
        $TPPGoods = $this->reportStock->getStockGoods($reportTPPConditions);

        $reportTPPConditions['total_size'] = 20;
        $reportTPPContainer20 = $this->reportStock->getStockContainers($reportTPPConditions);
        $reportTPPConditions['total_size'] = 40;
        $reportTPPContainer40 = $this->reportStock->getStockContainers($reportTPPConditions);
        $reportTPPConditions['total_size'] = 45;
        $reportTPPContainer45 = $this->reportStock->getStockContainers($reportTPPConditions);

        $reportTPPConditions['total_size'] = 'all';
        $reportTPPGoodsTotals = $this->reportStock->getStockGoods($reportTPPConditions);
        $reportTPPConditions['total_size'] = 'quantity';
        $reportTPPGoodsQuantity = $this->reportStock->getStockGoods($reportTPPConditions);
        $reportTPPConditions['total_size'] = 'unit_weight';
        $reportTPPGoodsTonnage = $this->reportStock->getStockGoods($reportTPPConditions);
        $reportTPPConditions['total_size'] = 'unit_volume';
        $reportTPPGoodsVolume = $this->reportStock->getStockGoods($reportTPPConditions);

        $reportTPPContainerData = $this->exporter->exportFromArray('Stock containers', $TPPContainers, false);
        $reportTPPGoodsData = $this->exporter->exportFromArray('Stock goods', $TPPGoods, false);

        $tableDataTPP = $this->load->view('emails/partials/_stock', [
            'dateStock' => $dateStock,
            'reportContainer20' => $reportTPPContainer20,
            'reportContainer40' => $reportTPPContainer40,
            'reportContainer45' => $reportTPPContainer45,
            'reportGoodsTotals' => $reportTPPGoodsTotals,
            'reportGoodsQuantity' => $reportTPPGoodsQuantity,
            'reportGoodsTonnage' => $reportTPPGoodsTonnage,
            'reportGoodsVolume' => $reportTPPGoodsVolume,
        ], true);

        $reportPLBConditions = [
            'data' => 'stock',
            'stock_date' => $dateStock,
            'branch_type' => "PLB",
        ];
        $PLBContainers = $this->reportStock->getStockContainers($reportPLBConditions);
        $PLBGoods = $this->reportStock->getStockGoods($reportPLBConditions);

        $reportPLBConditions['total_size'] = 20;
        $reportPLBContainer20 = $this->reportStock->getStockContainers($reportPLBConditions);
        $reportPLBConditions['total_size'] = 40;
        $reportPLBContainer40 = $this->reportStock->getStockContainers($reportPLBConditions);
        $reportPLBConditions['total_size'] = 45;
        $reportPLBContainer45 = $this->reportStock->getStockContainers($reportPLBConditions);

        $reportPLBConditions['total_size'] = 'all';
        $reportPLBGoodsTotals = $this->reportStock->getStockGoods($reportPLBConditions);
        $reportPLBConditions['total_size'] = 'quantity';
        $reportPLBGoodsQuantity = $this->reportStock->getStockGoods($reportPLBConditions);
        $reportPLBConditions['total_size'] = 'unit_weight';
        $reportPLBGoodsTonnage = $this->reportStock->getStockGoods($reportPLBConditions);
        $reportPLBConditions['total_size'] = 'unit_volume';
        $reportPLBGoodsVolume = $this->reportStock->getStockGoods($reportPLBConditions);

        $reportPLBContainerData = $this->exporter->exportFromArray('Stock containers', $PLBContainers, false);
        $reportPLBGoodsData = $this->exporter->exportFromArray('Stock goods', $PLBGoods, false);

        $tableDataPLB = $this->load->view('emails/partials/_stock', [
            'dateStock' => $dateStock,
            'reportContainer20' => $reportPLBContainer20,
            'reportContainer40' => $reportPLBContainer40,
            'reportContainer45' => $reportPLBContainer45,
            'reportGoodsTotals' => $reportPLBGoodsTotals,
            'reportGoodsQuantity' => $reportPLBGoodsQuantity,
            'reportGoodsTonnage' => $reportPLBGoodsTonnage,
            'reportGoodsVolume' => $reportPLBGoodsVolume,
        ], true);

        echo 'Sending stock email report...' . PHP_EOL;

        $emailToTPP = implode(",",$emailSupportTPP);
        $emailToPLB = implode(",",$emailSupportPLB);
        $emailBCC = $this->email_bcc;

        $dateFileFormat = $date->format('Y_m_d');
        $TPPAttachments = [];
        $TPPAttachments[] = [
            'source' => $reportTPPContainerData,
            'disposition' => 'attachment',
            'file_name' => "report_stock_container_{$dateFileFormat}.xlsx",
        ];

        $TPPAttachments[] = [
            'source' => $reportTPPGoodsData,
            'disposition' => 'attachment',
            'file_name' => "report_stock_goods_{$dateFileFormat}.xlsx",
        ];

        $emailTitleTPP = "Stock data tpp report until {$dateStock} ({$periodLabel})";
        $emailTemplateTPP = 'emails/basic';
        $emailDataTPP = [
            'title' => 'Stock Routine Report',
            'name' => 'Transcon Management',
            'email' => implode(",",$emailSupportTPP),
            'content' => $tableDataTPP,
        ];

        $emailOptionsTPP = [
            'bcc' => isset($emailBCC) ? $emailBCC : null,
            'attachment' => $TPPAttachments,
        ];

        $this->mailer->send($emailToTPP, $emailTitleTPP, $emailTemplateTPP, $emailDataTPP, $emailOptionsTPP);

        $PLBAttachments = [];
        $PLBAttachments[] = [
            'source' => $reportPLBContainerData,
            'disposition' => 'attachment',
            'file_name' => "report_stock_container_{$dateFileFormat}.xlsx",
        ];

        $PLBAttachments[] = [
            'source' => $reportPLBGoodsData,
            'disposition' => 'attachment',
            'file_name' => "report_stock_goods_{$dateFileFormat}.xlsx",
        ];

        $emailTitlePLB = "Stock data plb report until {$dateStock} ({$periodLabel})";
        $emailTemplatePLB = 'emails/basic';
        $emailDataPLB = [
            'title' => 'Stock Routine Report',
            'name' => 'Transcon Management',
            'email' => implode(",",$emailSupportPLB),
            'content' => $tableDataPLB,
        ];

        $emailOptionsPLB = [
            'bcc' => isset($emailBCC) ? $emailBCC : null,
            'attachment' => $PLBAttachments,
        ];

        $this->mailer->send($emailToPLB, $emailTitlePLB, $emailTemplatePLB, $emailDataPLB, $emailOptionsPLB);

        echo 'Stock email reports were sent...' . PHP_EOL;
    }

    /**
     * Send report pending booking.
     * @param $period
     * @param $periodLabel
     * @throws Exception
     */
    public function report_plan_realization($period, $periodLabel)
    {
        $date = new DateTime();
        $date->sub(new DateInterval("P{$period}D"));
        $dateFetch = $date->format('d F Y');
        $getBranchByPLB = $this->branchModel->getBy(['branch_type' => "PLB"]);
        $emailSupportPLB = array_unique(array_filter(array_column($getBranchByPLB, "email_support")));
        $getBranchByTPP = $this->branchModel->getBy(['branch_type' => "TPP"]);
        $emailSupportTPP = array_unique(array_filter(array_column($getBranchByTPP, "email_support")));

        $reportTPPConditions = [
            'data' => 'pending',
            'date_fetch' => $dateFetch,
            'branch_type' => "TPP",
        ];
        $TPPContainers = $this->reportModel->getReportRealizationContainer($reportTPPConditions);
        $TPPgoods = $this->reportModel->getReportRealizationGoods($reportTPPConditions);
        $reportTPPContainerData = $this->exporter->exportFromArray('Pending containers', $TPPContainers, false);
        $reportTPPGoodsData = $this->exporter->exportFromArray('Pending goods', $TPPgoods, false);

        $reportPLBConditions = [
            'data' => 'pending',
            'date_fetch' => $dateFetch,
            'branch_type' => "PLB",
        ];

        $PLBContainers = $this->reportModel->getReportRealizationContainer($reportPLBConditions);
        $PLBGoods = $this->reportModel->getReportRealizationGoods($reportPLBConditions);
        $reportPLBContainerData = $this->exporter->exportFromArray('Pending containers', $PLBContainers, false);
        $reportPLBGoodsData = $this->exporter->exportFromArray('Pending goods', $PLBGoods, false);

        echo 'Sending pending activity report...' . PHP_EOL;

        $emailToTPP = implode(",",$emailSupportTPP);
        $emailToPLB = implode(",",$emailSupportPLB);
        $emailBCC = $this->email_bcc;

        $dateFileFormat = $date->format('Y_m_d');
        $TPPAttachments = [];
        $TPPAttachments[] = [
            'source' => $reportTPPContainerData,
            'disposition' => 'attachment',
            'file_name' => "report_pending_container_{$dateFileFormat}.xlsx",
        ];

        $TPPAttachments[] = [
            'source' => $reportTPPGoodsData,
            'disposition' => 'attachment',
            'file_name' => "report_pending_goods_{$dateFileFormat}.xlsx",
        ];

        $emailTitleTPP = "Pending data report tpp until {$dateFetch} ({$periodLabel})";
        $emailTemplateTPP = 'emails/basic';
        $emailDataTPP = [
            'name' => 'Transcon Management',
            'title' => 'Pending Activity Routine Report',
            'email' =>$emailToTPP,
            'content' => 'We would inform you about TCI Warehouse and Field pending activity data tpp until ' . $dateFetch . ' (this email may contains attachment)',
        ];

        $emailOptionsTPP = [
            'bcc' => isset($emailBCC) ? $emailBCC : null,
            'attachment' => $TPPAttachments,
        ];

        $this->mailer->send($emailToTPP, $emailTitleTPP, $emailTemplateTPP, $emailDataTPP, $emailOptionsTPP);

        $PlbAttachments = [];
        $PlbAttachments[] = [
            'source' => $reportPLBContainerData,
            'disposition' => 'attachment',
            'file_name' => "report_pending_container_{$dateFileFormat}.xlsx",
        ];

        $PlbAttachments[] = [
            'source' => $reportPLBGoodsData,
            'disposition' => 'attachment',
            'file_name' => "report_pending_goods_{$dateFileFormat}.xlsx",
        ];

        $emailTitlePLB = "Pending data report plb until {$dateFetch} ({$periodLabel})";
        $emailTemplatePLB = 'emails/basic';
        $emailDataPLB = [
            'name' => 'Transcon Management',
            'title' => 'Pending Activity Routine Report',
            'email' => $emailToPLB,
            'content' => 'We would inform you about TCI Warehouse and Field pending activity data plb until ' . $dateFetch . ' (this email may contains attachment)',
        ];

        $emailOptionsPLB = [
            'bcc' => isset($emailBCC) ? $emailBCC : null,
            'attachment' => $PlbAttachments,
        ];

        $this->mailer->send($emailToPLB, $emailTitlePLB, $emailTemplatePLB, $emailDataPLB, $emailOptionsPLB);

        echo 'Pending activity email reports were sent...' . PHP_EOL;
    }

    /**
     * Send report aging booking.
     * @param $period
     * @param $periodLabel
     * @throws Exception
     */
    public function report_aging($period, $periodLabel)
    {
        $date = new DateTime();
        $date->sub(new DateInterval("P{$period}D"));
        $dateFetch = $date->format('d F Y');
        $getBranchByPLB = $this->branchModel->getBy(['branch_type' => "PLB"]);
        $emailSupportPLB = array_unique(array_column($getBranchByPLB, "email_support"));
        $getBranchByTPP = $this->branchModel->getBy(['branch_type' => "TPP"]);
        $emailSupportTPP = array_unique(array_column($getBranchByTPP, "email_support"));

        $reportTPPConditions = [
            'day_to' => $dateFetch,
            'branch_type' => "TPP",
        ];

        $reportPLBConditions = [
            'day_to' => $dateFetch,
            'branch_type' => "PLB",
        ];

        $tppContainers = $this->reportModel->getReportAgingContainer($reportTPPConditions);
        $tppGoods = $this->reportModel->getReportAgingGoods($reportTPPConditions);

        $plbContainers = $this->reportModel->getReportAgingContainer($reportPLBConditions);
        $plbGoods = $this->reportModel->getReportAgingGoods($reportPLBConditions);

        $reportTPPContainerData = $this->exporter->exportFromArray('Aging containers', $tppContainers, false);
        $reportTPPGoodsData = $this->exporter->exportFromArray('Aging goods', $tppGoods, false);

        $reportPLBContainerData = $this->exporter->exportFromArray('Aging containers', $plbContainers, false);
        $reportPLBGoodsData = $this->exporter->exportFromArray('Aging goods', $plbGoods, false);

        echo 'Sending aging report...' . PHP_EOL;

        $emailToTPP = implode(",", $emailSupportTPP);
        $emailToPLB = implode(",", $emailSupportPLB);
        $emailBCC = [
            //'tpp_mgr@transcon-indonesia.com',
            'cso1@transcon-indonesia.com',
            'it@transcon-indonesia.com',
            'it1@transcon-indonesia.com',
            'it2@transcon-indonesia.com',
            'it5@transcon-indonesia.com',
            'opr_div2@transcon-indonesia.com',
            'nandi@mgesolution.com',
            'direktur@transcon-indonesia.com'
        ];
        $dateFileFormat = $date->format('Y_m_d');

        $tppAttachments = [];
        $tppAttachments[] = [
            'source' => $reportTPPContainerData,
            'disposition' => 'attachment',
            'file_name' => "report_aging_container_{$dateFileFormat}.xlsx",
        ];

        $tppAttachments[] = [
            'source' => $reportTPPGoodsData,
            'disposition' => 'attachment',
            'file_name' => "report_aging_goods_{$dateFileFormat}.xlsx",
        ];

        $emailTitleTPP = "Aging data report TPP at {$dateFetch} ({$periodLabel}";
        $emailTemplateTPP = 'emails/basic';
        $emailDataTPP = [
            'name' => 'Transcon Management',
            'title' => 'Aging Routine Report',
            'email' => $emailToTPP,
            'content' => 'We would inform you about aging data at ' . $dateFetch . ' (this email may contains attachment)',
        ];

        $emailOptionsTPP = [
            'bcc' => isset($emailBCC) ? $emailBCC : null,
            'attachment' => $tppAttachments,
        ];

        $this->mailer->send($emailToTPP, $emailTitleTPP, $emailTemplateTPP, $emailDataTPP, $emailOptionsTPP);

        $PLBAttachments = [];
        $PLBAttachments[] = [
            'source' => $reportPLBContainerData,
            'disposition' => 'attachment',
            'file_name' => "report_aging_container_{$dateFileFormat}.xlsx",
        ];

        $PLBAttachments[] = [
            'source' => $reportPLBGoodsData,
            'disposition' => 'attachment',
            'file_name' => "report_aging_goods_{$dateFileFormat}.xlsx",
        ];

        $emailTitlePLB = "Aging data report PLB at {$dateFetch} ({$periodLabel}";
        $emailTemplatePLB = 'emails/basic';
        $emailDataPLB = [
            'name' => 'Transcon Management',
            'title' => 'Aging Routine Report',
            'email' => $emailToPLB,
            'content' => 'We would inform you about aging data at ' . $dateFetch . ' (this email may contains attachment)',
        ];

        $emailOptionsPLB = [
            'bcc' => isset($emailBCC) ? $emailBCC : null,
            'attachment' => $PLBAttachments,
        ];

        $this->mailer->send($emailToPLB, $emailTitlePLB, $emailTemplatePLB, $emailDataPLB, $emailOptionsPLB);

        echo 'Aging activity email reports were sent...' . PHP_EOL;
    }

    /**
     * Report mutation.
     * @param $period
     * @param $periodLabel
     * @throws Exception
     */
    public function report_mutation($period, $periodLabel)
    {
        $date = new DateTime();
        $date->sub(new DateInterval("P{$period}D"));
        $dateFetch = $date->format('d F Y');
        $getBranchByPLB = $this->branchModel->getBy(['branch_type' => "PLB"]);
        $emailSupportPLB = array_unique(array_column($getBranchByPLB, "email_support"));
        $getBranchByTPP = $this->branchModel->getBy(['branch_type' => "TPP"]);
        $emailSupportTPP = array_unique(array_column($getBranchByTPP, "email_support"));

        $reportTPPConditions = [
            'day_to' => $dateFetch,
            'branch_type' => "TPP",
        ];

        $reportPLBConditions = [
            'day_to' => $dateFetch,
            'branch_type' => "PLB",
        ];

        $TPPContainers = $this->reportModel->getReportMutationContainer($reportTPPConditions);
        $TPPGoods = $this->reportModel->getReportMutationGoods($reportTPPConditions);

        $PLBContainers = $this->reportModel->getReportMutationContainer($reportPLBConditions);
        $PLBGoods = $this->reportModel->getReportMutationGoods($reportPLBConditions);

        $html = $this->load->view('report/_plain_mutation_container', ['stockMutationContainers' => $TPPContainers], true);
        $reportContainerTPPData = $this->exporter->exportFrom('Mutation Container', $html, Exporter::EXCEL, false);

        $html = $this->load->view('report/_plain_mutation_container', ['stockMutationContainers' => $PLBContainers], true);
        $reportContainerPLBData = $this->exporter->exportFrom('Mutation Container', $html, Exporter::EXCEL, false);

        $html = $this->load->view('report/_plain_mutation_container', ['stockMutationGoods' => $TPPGoods], true);
        $reportGoodsTPPData = $this->exporter->exportFrom('Mutation Goods', $html, Exporter::EXCEL, false);

        $html = $this->load->view('report/_plain_mutation_container', ['stockMutationGoods' => $PLBGoods], true);
        $reportGoodsPLBData = $this->exporter->exportFrom('Mutation Goods', $html, Exporter::EXCEL, false);
        echo 'Sending mutation report...' . PHP_EOL;

        $emailToTPP = implode(",", $emailSupportTPP);
        $emailToPLB = implode(",", $emailSupportPLB);
        $emailBCC = $this->email_bcc;
        $dateFileFormat = $date->format('Y_m_d');

        $attachmentsTPP = [];
        $attachmentsTPP[] = [
            'source' => $reportContainerTPPData,
            'disposition' => 'attachment',
            'file_name' => "report_mutation_container_{$dateFileFormat}.xlsx",
        ];

        $attachmentsTPP[] = [
            'source' => $reportGoodsTPPData,
            'disposition' => 'attachment',
            'file_name' => "report_mutation_goods_{$dateFileFormat}.xlsx",
        ];

        $emailTitleTPP = "Mutation data report TPP at {$dateFetch} ({$periodLabel})";
        $emailTemplateTPP = 'emails/basic';
        $emailDataTPP = [
            'name' => 'Transcon Management',
            'title' => 'Mutation Routine Report',
            'email' => $emailToTPP,
            'content' => 'We would inform you about TCI Warehouse and Field stock mutation data at ' . $dateFetch . ' (this email may contains attachment)',
        ];

        $emailOptionsTPP = [
            'bcc' => isset($emailBCC) ? $emailBCC : null,
            'attachment' => $attachmentsTPP,
        ];
        $this->mailer->send($emailToTPP, $emailTitleTPP, $emailTemplateTPP, $emailDataTPP, $emailOptionsTPP);

        $attachmentsPLB = [];
        $attachmentsPLB[] = [
            'source' => $reportContainerPLBData,
            'disposition' => 'attachment',
            'file_name' => "report_mutation_container_{$dateFileFormat}.xlsx",
        ];
        $attachmentsPLB[] = [
            'source' => $reportGoodsPLBData,
            'disposition' => 'attachment',
            'file_name' => "report_mutation_goods_{$dateFileFormat}.xlsx",
        ];
        $emailTitlePLB = "Mutation data report PLB at {$dateFetch} ({$periodLabel})";
        $emailTemplatePLB = 'emails/basic';
        $emailDataPLB = [
            'name' => 'Transcon Management',
            'title' => 'Mutation Routine Report',
            'email' => $emailToPLB,
            'content' => 'We would inform you about TCI Warehouse and Field stock mutation data at ' . $dateFetch . ' (this email may contains attachment)',
        ];

        $emailOptionsPLB = [
            'bcc' => isset($emailBCC) ? $emailBCC : null,
            'attachment' => $attachmentsPLB,
        ];

        $this->mailer->send($emailToPLB, $emailTitlePLB, $emailTemplatePLB, $emailDataPLB, $emailOptionsPLB);
        echo 'Mutation email reports were sent...' . PHP_EOL;
    }

    /**
     * Report booking that was empty already.
     * @param $period
     * @param string $periodLabel
     * @throws Exception
     */
    public function report_complete_booking($period, $periodLabel)
    {
        $date = new DateTime();
        $date->sub(new DateInterval("P{$period}D"));
        $dateFrom = $date->format('d F Y');
        $dateLabel = 'since ' . $dateFrom . ' until ' . date('d F Y');
        $getBranchByPLB = $this->branchModel->getBy(['branch_type' => "PLB"]);
        $emailSupportPLB = array_unique(array_filter(array_column($getBranchByPLB, "email_support")));
        $getBranchByTPP = $this->branchModel->getBy(['branch_type' => "TPP"]);
        $emailSupportTPP = array_unique(array_filter(array_column($getBranchByTPP, "email_support")));

        $reportConditionsTPP = [
            'data' => 'realization',
            'date_from' => $dateFrom,
            'date_type' => 'transaction_date',
            'branch_type' => "TPP",
        ];
        $bookingType = "all";
        $bookingConditionsTPP = [
            'branch_type' => "TPP",
        ];
        $bookingOutboundsTPP = $this->reportBcModel->getReportOutbound($reportConditionsTPP);
        $availableBookingsTPP = $this->reportModel->getAvailableStockBookingList($bookingType, $bookingConditionsTPP);

        $bookingEmptyListTPP = [];
        // find out which bookings aren't available in booking stock (TPP).
        $inboundsTPP = array_column($bookingOutboundsTPP, 'booking_no_in');
        foreach ($inboundsTPP as $inboundTPP) {
            $isEmpty = true;
            foreach ($availableBookingsTPP as $availableBookingTPP) {
                if ($availableBookingTPP['no_booking'] == $inboundTPP) {
                    $isEmpty = false;
                }
            }
            if ($isEmpty) {
                $bookingEmptyListTPP[] = $inboundTPP;
            }
        }
        $bookingsTPP = $this->bookingModel->getBookingByNo(empty($bookingEmptyListTPP) ? '' : $bookingEmptyListTPP);

        $reportConditionsPLB = [
            'data' => 'realization',
            'date_from' => $dateFrom,
            'date_type' => 'transaction_date',
            'branch_type' => "PLB",
        ];

        $bookingConditionsPLB = [
            'branch_type' => "PLB",
        ];
        $bookingOutboundsPLB = $this->reportBcModel->getReportOutbound($reportConditionsPLB);
        $availableBookingsPLB = $this->reportModel->getAvailableStockBookingList($bookingType, $bookingConditionsPLB);

        $bookingEmptyListPLB = [];
        // find out which bookings aren't available in booking stock (PLB).
        $inboundsPLB = array_column($bookingOutboundsPLB, 'booking_no_in');
        foreach ($inboundsPLB as $inboundPLB) {
            $isEmpty = true;
            foreach ($availableBookingsPLB as $availableBookingPLB) {
                if ($availableBookingPLB['no_booking'] == $inboundPLB) {
                    $isEmpty = false;
                }
            }
            if ($isEmpty) {
                $bookingEmptyListPLB[] = $inboundPLB;
            }
        }
        $bookingsPLB = $this->bookingModel->getBookingByNo(empty($bookingEmptyListPLB) ? '' : $bookingEmptyListPLB);

        if (!empty($bookingsTPP)) {
            $messages = '<ol>';
            foreach ($bookingsTPP as $booking_tpp) {
                $messages .= '<li>' . $booking_tpp['customer_name'] . ' - ' . $booking_tpp['no_booking'] . ' (' . $booking_tpp['no_reference'] . ')</li><br>';
            }
            $messages .= '</ol>';

            echo 'Sending booking complete tpp report...' . PHP_EOL;

            $emailToTPP = $emailSupportTPP;
            $emailCC = ['fin2@transcon-indonesia.com', 'fin@transcon-indonesia.com'];
            $emailBCC = $this->email_bcc;

            $emailTitleTPP = "Booking empty and complete data report tpp at {$dateFrom} ({$periodLabel})";
            $emailTemplateTPP = 'emails/basic';
            $emailDataTPP = [
                'name' => 'Transcon Management',
                'title' => 'Booking Complete Report',
                'email' => implode(",", $emailToTPP),
                'content' => 'We would inform you about Booking Complete data at ' . $dateLabel . ': <br>' . $messages,
            ];

            $emailOptionsTPP = [
                'cc' => null,
                'bcc' => isset($emailBCC) ? $emailBCC : null,
            ];

            $this->mailer->send($emailToTPP, $emailTitleTPP, $emailTemplateTPP, $emailDataTPP, $emailOptionsTPP);

            echo 'Booking complete email reports tpp were sent...' . PHP_EOL;
        } else {
            echo 'No booking complete tpp available...' . PHP_EOL;
        }

        if (!empty($bookingsPLB)) {
            $messages = '<ol>';
            foreach ($bookingsPLB as $booking_plb) {
                $messages .= '<li>' . $booking_plb['customer_name'] . ' - ' . $booking_plb['no_booking'] . ' (' . $booking_plb['no_reference'] . ')</li><br>';
            }
            $messages .= '</ol>';

            echo 'Sending booking complete plb report...' . PHP_EOL;

            $emailToPLB = $emailSupportPLB;
            $emailCC = ['fin2@transcon-indonesia.com', 'fin@transcon-indonesia.com'];
            $emailBCC = $this->email_bcc;

            $emailTitlePLB = "Booking empty and complete data report plb at {$dateFrom} ({$periodLabel})";
            $emailTemplatePLB = 'emails/basic';
            $emailDataPLB = [
                'name' => 'Transcon Management',
                'title' => 'Booking Complete Report',
                'email' => implode(",", $emailToPLB),
                'content' => 'We would inform you about Booking Complete data at ' . $dateLabel . ': <br>' . $messages,
            ];

            $emailOptionsPLB = [
                'cc' => null,
                'bcc' => isset($emailBCC) ? $emailBCC : null,
            ];

            $this->mailer->send($emailToPLB, $emailTitlePLB, $emailTemplatePLB, $emailDataPLB, $emailOptionsPLB);

            echo 'Booking complete email reports plb were sent...' . PHP_EOL;
        } else {
            echo 'No booking complete plb available...' . PHP_EOL;
        }
    }

    /**
     * Report daily activity TPS
     * same format as shipping line activity
     * @param $reportSchedule
     */
    public function report_tps_activity($reportSchedule)
    {
        echo "report_tps_activity started at " . date('Y-m-d H:i:s') . PHP_EOL;

        $period = 1;
        $periodLabel = 'daily';
        switch ($reportSchedule['recurring_period']) {
            case ReportScheduleModel::PERIOD_DAILY:
                $periodLabel = 'daily';
                $period = 1;
                break;
            case ReportScheduleModel::PERIOD_WEEKLY:
                $periodLabel = 'weekly';
                $period = 7;
                break;
            case ReportScheduleModel::PERIOD_MONTHLY:
                $periodLabel = 'monthly';
                $period = 30;
                break;
            case ReportScheduleModel::PERIOD_ANNUAL:
                $periodLabel = 'annual';
                $period = 365;
                break;
        }

        $date = new DateTime();
        $date->sub(new DateInterval("P{$period}D"));
        $dateFrom = $date->format('d F Y');
        $dateLabel = 'since ' . $dateFrom . ' until ' . date('d F Y');

        $getBranchByTPP = $this->branchModel->getBy(['branch_type' => "TPP"]);
        $emailSupportTPP = array_unique(array_column($getBranchByTPP, "email_support"));

        $reportConditions = [
            'date_from' => $dateFrom,
            'date_type' => 'completed_at',
            'branch_type' => "TPP",
        ];
        $activities = $this->reportTppModel->getTppContainerActivity($reportConditions);

        $originWarehouses = [];
        if (!empty($activities)) {
            $originWarehouses = array_unique(array_column($activities, 'tps_name'));
            $originWarehouses = $this->peopleModel->getBy([
                'ref_people.name' => $originWarehouses
            ]);
        }

        foreach ($originWarehouses as $index => $originWarehouse) {
            $reportConditions['tps_id'] = $originWarehouse['id'];

            $reportConditions['category'] = 'INBOUND';
            $inbounds = $this->reportTppModel->getTppContainerActivity($reportConditions);

            $reportConditions['category'] = 'OUTBOUND';
            $outbounds = $this->reportTppModel->getTppContainerActivity($reportConditions);

            $reportInData = null;
            if (!empty($inbounds)) {
                foreach ($inbounds as &$inbound) {
                    unset($inbound['id_tps']);
                    unset($inbound['id_shipping_line']);
                    unset($inbound['outbound_date']);
                }
                $reportInData = $this->exporterModel->export('Inbound', $inbounds, false);
            }

            $reportOutData = null;
            if (!empty($outbounds)) {
                foreach ($outbounds as &$outbound) {
                    unset($outbound['id_tps']);
                    unset($outbound['id_shipping_line']);
                }
                $reportOutData = $this->exporterModel->export('Outbound', $outbounds, false);
            }

            // collect email addresses
            $sendTypeTo = $reportSchedule['send_to_type'] == ReportScheduleModel::SEND_TYPE_BRANCH_PLB_OR_TPP_SUPPORT ? ReportScheduleModel::SEND_TYPE_BRANCH_TPP_SUPPORT : $reportSchedule['send_to_type'];
            $sendTypeCC = $reportSchedule['send_cc_type'] == ReportScheduleModel::SEND_TYPE_BRANCH_PLB_OR_TPP_SUPPORT ? ReportScheduleModel::SEND_TYPE_BRANCH_TPP_SUPPORT : $reportSchedule['send_cc_type'];
            $sendTypeBCC = $reportSchedule['send_bcc_type'] == ReportScheduleModel::SEND_TYPE_BRANCH_PLB_OR_TPP_SUPPORT ? ReportScheduleModel::SEND_TYPE_BRANCH_TPP_SUPPORT : $reportSchedule['send_bcc_type'];

            if (empty($originWarehouse['email'])) {
                $originWarehouse['email'] = implode(',', $emailSupportTPP);
            }
            $emailTo = $this->getEmailFromProfile($sendTypeTo, $reportSchedule['send_to'], $originWarehouse);
            $emailCC = $this->getEmailFromProfile($sendTypeCC, $reportSchedule['send_cc'], $originWarehouse);
            $emailBCC = $this->getEmailFromProfile($sendTypeBCC, $reportSchedule['send_bcc'], $originWarehouse);

            $emailTitle = "(TPS) {$originWarehouse['name']}: {$periodLabel} report TPP {$dateLabel}";
            $emailTemplate = 'emails/basic';
            $emailData = [
                'name' => $originWarehouse['name'],
                'title' => 'Activity Report',
                'content' => 'We would inform you about latest activities in our warehouse ' . $dateLabel . ' (this email may contains attachment)',
                'email' => implode(",", $emailTo)
            ];

            $attachments = [];
            $dateFileFormat = $date->format('Y_m_d');
            if (!empty($inbounds)) {
                $attachments[] = [
                    'source' => $reportInData,
                    'disposition' => 'attachment',
                    'file_name' => "report_in_{$dateFileFormat}.xlsx",
                    'mime' => 'application/vnd.ms-excel',
                ];
            }
            if (!empty($outbounds)) {
                $attachments[] = [
                    'source' => $reportOutData,
                    'disposition' => 'attachment',
                    'file_name' => "report_out_{$dateFileFormat}.xlsx",
                    'mime' => 'application/vnd.ms-excel',
                ];
            }

            $emailOptions = [
                'cc' => isset($emailCC) ? $emailCC : null,
                'bcc' => isset($emailBCC) ? $emailBCC : null,
                'attachment' => $attachments,
            ];

            if (!empty($inbounds) || !empty($outbounds)) {
                echo 'Sending activity tpp email report of tps ' . $originWarehouse['name'] . '...' . PHP_EOL;
                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
                echo 'TPS daily reports email were sent to tpp contacts...' . PHP_EOL;
            } else {
                echo 'No data, tps daily reports were not sent to tpp team...' . PHP_EOL;
            }
        }
        echo "report_tps_activity ended at " . date('Y-m-d H:i:s') . PHP_EOL;
    }

    /**
     * Get TPS stock
     * @param $reportSchedule
     */
    public function report_tps_stock($reportSchedule)
    {
        echo "report_tps_stock started at " . date('Y-m-d H:i:s') . PHP_EOL;

        $dateLabel = date('d F Y');

        $getBranchByTPP = $this->branchModel->getBy(['branch_type' => "TPP"]);
        $emailSupportTPP = array_unique(array_filter(array_column($getBranchByTPP, "email_support")));

        $branchId = 6;
        $branchType = "TPP";

        $stocks = $this->reportTppModel->getShippingLineStock(null, $branchId, $branchType);
        $tpsIds = array_unique(array_column($stocks, 'id_tps'));
        $tps = $this->peopleModel->getBy([
            'ref_people.id' => $tpsIds
        ]);

        foreach ($tps as $tpsData) {
            $tpsStock = array_filter($stocks, function ($stock) use ($tpsData) {
                return ($stock['id_tps'] == $tpsData['id']);
            });
            $stockData = $this->exporterModel->export('Stock', array_values($tpsStock), false);

            // collect email addresses
            $sendTypeTo = $reportSchedule['send_to_type'] == ReportScheduleModel::SEND_TYPE_BRANCH_PLB_OR_TPP_SUPPORT ? ReportScheduleModel::SEND_TYPE_BRANCH_TPP_SUPPORT : $reportSchedule['send_to_type'];
            $sendTypeCC = $reportSchedule['send_cc_type'] == ReportScheduleModel::SEND_TYPE_BRANCH_PLB_OR_TPP_SUPPORT ? ReportScheduleModel::SEND_TYPE_BRANCH_TPP_SUPPORT : $reportSchedule['send_cc_type'];
            $sendTypeBCC = $reportSchedule['send_bcc_type'] == ReportScheduleModel::SEND_TYPE_BRANCH_PLB_OR_TPP_SUPPORT ? ReportScheduleModel::SEND_TYPE_BRANCH_TPP_SUPPORT : $reportSchedule['send_bcc_type'];

            if (empty($tpsData['email'])) {
                $tpsData['email'] = implode(',', $emailSupportTPP);
            }
            $emailTo = $this->getEmailFromProfile($sendTypeTo, $reportSchedule['send_to'], $tpsData);
            $emailCC = $this->getEmailFromProfile($sendTypeCC, $reportSchedule['send_cc'], $tpsData);
            $emailBCC = $this->getEmailFromProfile($sendTypeBCC, $reportSchedule['send_bcc'], $tpsData);

            $emailTitle = "(TPS) {$tpsData['name']}: stock report TPP at {$dateLabel}";
            $emailTemplate = 'emails/basic';
            $emailData = [
                'name' => $tpsData['name'],
                'title' => 'TPS Report',
                'content' => 'We would inform you about stock in our warehouse at ' . $dateLabel . ' (this email may contains attachment)',
                'email' => implode(",", $emailTo)
            ];

            $attachments = [];
            $dateFileFormat = date('Y_m_d');
            if (!empty($tpsStock)) {
                $attachments[] = [
                    'source' => $stockData,
                    'disposition' => 'attachment',
                    'file_name' => "report_stock_{$dateFileFormat}.xlsx",
                    'mime' => 'application/vnd.ms-excel',
                ];

                $emailOptions = [
                    'cc' => isset($emailCC) ? $emailCC : null,
                    'bcc' => isset($emailBCC) ? $emailBCC : null,
                    'attachment' => $attachments,
                ];

                echo 'Sending TPS stock tpp email report ' . $tpsData['name'] . '...' . PHP_EOL;
                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
                echo 'TPS stock reports email were sent...' . PHP_EOL;
            } else {
                echo 'No data, tps stock reports were not sent to tpp team...' . PHP_EOL;
            }
        }
        echo "report_tps_stock ended at " . date('Y-m-d H:i:s') . PHP_EOL;
    }

    /**
     * Report daily activity Shipping Line
     * same format as tps activity
     * @param $reportSchedule
     */
    public function report_shipping_activity($reportSchedule = null)
    {
        echo "report_shipping_activity started at " . date('Y-m-d H:i:s') . PHP_EOL;

        $period = 1;
        $periodLabel = 'daily';
        switch ($reportSchedule['recurring_period']) {
            case ReportScheduleModel::PERIOD_DAILY:
                $periodLabel = 'daily';
                $period = 1;
                break;
            case ReportScheduleModel::PERIOD_WEEKLY:
                $periodLabel = 'weekly';
                $period = 7;
                break;
            case ReportScheduleModel::PERIOD_MONTHLY:
                $periodLabel = 'monthly';
                $period = 30;
                break;
            case ReportScheduleModel::PERIOD_ANNUAL:
                $periodLabel = 'annual';
                $period = 365;
                break;
        }

        $date = new DateTime();
        $date->sub(new DateInterval("P{$period}D"));
        $dateFrom = $date->format('d F Y');
        $dateLabel = 'since ' . $dateFrom . ' until ' . date('d F Y');

        $getBranchByTPP = $this->branchModel->getBy(['branch_type' => "TPP"]);
        $emailSupportTPP = array_unique(array_filter(array_column($getBranchByTPP, "email_support")));

        $reportConditions = [
            'date_from' => $dateFrom,
            'date_type' => 'completed_at',
            'branch_type' => "TPP",
        ];
        $activities = $this->reportTppModel->getTppContainerActivity($reportConditions);

        $shippingLines = [];
        if (!empty($activities)) {
            $shippingLines = array_unique(array_column($activities, 'id_shipping_line'));
            $shippingLines = $this->peopleModel->getBy([
                'ref_people.id' => $shippingLines
            ]);
        }

        foreach ($shippingLines as $shippingLine) {
            $reportConditions['shipping_line_id'] = $shippingLine['id'];

            $reportConditions['category'] = 'INBOUND';
            $inbounds = $this->reportTppModel->getTppContainerActivity($reportConditions);

            $reportConditions['category'] = 'OUTBOUND';
            $outbounds = $this->reportTppModel->getTppContainerActivity($reportConditions);

            $reportInData = null;
            if (!empty($inbounds)) {
                foreach ($inbounds as &$inbound) {
                    unset($inbound['id_tps']);
                    unset($inbound['id_shipping_line']);
                    unset($inbound['outbound_date']);
                }
                $reportInData = $this->exporter->exportFromArray('Inbound', $inbounds, false);
            }

            $reportOutData = null;
            if (!empty($outbounds)) {
                foreach ($outbounds as &$outbound) {
                    unset($outbound['id_tps']);
                    unset($outbound['id_shipping_line']);
                }
                $reportOutData = $this->exporter->exportFromArray('Outbound', $outbounds, false);
            }

            // collect email addresses
            $sendTypeTo = $reportSchedule['send_to_type'] == ReportScheduleModel::SEND_TYPE_BRANCH_PLB_OR_TPP_SUPPORT ? ReportScheduleModel::SEND_TYPE_BRANCH_TPP_SUPPORT : $reportSchedule['send_to_type'];
            $sendTypeCC = $reportSchedule['send_cc_type'] == ReportScheduleModel::SEND_TYPE_BRANCH_PLB_OR_TPP_SUPPORT ? ReportScheduleModel::SEND_TYPE_BRANCH_TPP_SUPPORT : $reportSchedule['send_cc_type'];
            $sendTypeBCC = $reportSchedule['send_bcc_type'] == ReportScheduleModel::SEND_TYPE_BRANCH_PLB_OR_TPP_SUPPORT ? ReportScheduleModel::SEND_TYPE_BRANCH_TPP_SUPPORT : $reportSchedule['send_bcc_type'];

            if (empty($shippingLine['email'])) {
                $shippingLine['email'] = implode(',', $emailSupportTPP);
            }
            $emailTo = $this->getEmailFromProfile($sendTypeTo, $reportSchedule['send_to'], $shippingLine);
            $emailCC = $this->getEmailFromProfile($sendTypeCC, $reportSchedule['send_cc'], $shippingLine);
            $emailBCC = $this->getEmailFromProfile($sendTypeBCC, $reportSchedule['send_bcc'], $shippingLine);

            $emailTitle = "(Shipping Line) {$shippingLine['name']}: {$periodLabel} report TPP {$dateLabel}";
            $emailTemplate = 'emails/basic';
            $emailData = [
                'name' => $shippingLine['name'],
                'title' => 'Activity Report',
                'content' => 'We would inform you about latest activities in our warehouse ' . $dateLabel . '. Please let us know if the container we reported is not yours! (this email may contains attachment)',
                'email' => implode(",", $emailSupportTPP),
            ];

            $attachments = [];
            $dateFileFormat = $date->format('Y_m_d');
            if (!empty($inbounds)) {
                $attachments[] = [
                    'source' => $reportInData,
                    'disposition' => 'attachment',
                    'file_name' => "report_in_{$dateFileFormat}.xlsx",
                ];
            }
            if (!empty($outbounds)) {
                $attachments[] = [
                    'source' => $reportOutData,
                    'disposition' => 'attachment',
                    'file_name' => "report_out_{$dateFileFormat}.xlsx",
                ];
            }

            $emailOptions = [
                'cc' => isset($emailCC) ? $emailCC : null,
                'bcc' => isset($emailBCC) ? $emailBCC : null,
                'attachment' => $attachments,
            ];

            if (!empty($inbounds) || !empty($outbounds)) {
                echo 'Sending activity email report ' . $shippingLine['name'] . '...' . PHP_EOL;
                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
                echo 'Shipping Line daily reports email were sent to tpp team...' . PHP_EOL;
            } else {
                echo 'No data, shipping line daily reports were not sent to tpp team...' . PHP_EOL;
            }
        }
        echo "report_shipping_activity ended at " . date('Y-m-d H:i:s') . PHP_EOL;
    }

    /**
     * Get shipping line stock
     * @param null $branch
     * @throws Exception
     */
    public function report_shipping_stock($reportSchedule = null)
    {
        echo "report_shipping_stock started at " . date('Y-m-d H:i:s') . PHP_EOL;

        $dateLabel = date('d F Y');

        $getBranchByTPP = $this->branchModel->getBy(['branch_type' => "TPP"]);
        $emailSupportTPP = array_unique(array_filter(array_column($getBranchByTPP, "email_support")));

        $branchId = 6;
        $branchType = "TPP";

        $stocks = $this->reportTppModel->getShippingLineStock(null, $branchId, $branchType);
        $shippingLineIds = array_unique(array_column($stocks, 'shipping_line_name'));
        $shippingLines = $this->peopleModel->getBy([
            'ref_people.name' => $shippingLineIds
        ]);

        foreach ($shippingLines as $shippingLine) {
            $shippingLineStock = array_filter($stocks, function ($stock) use ($shippingLine) {
                return ($stock['shipping_line_name'] == $shippingLine['name']);
            });
            $stockData = $this->exporter->exportFromArray('Stock', array_values($shippingLineStock), false);

            // collect email addresses
            $sendTypeTo = $reportSchedule['send_to_type'] == ReportScheduleModel::SEND_TYPE_BRANCH_PLB_OR_TPP_SUPPORT ? ReportScheduleModel::SEND_TYPE_BRANCH_TPP_SUPPORT : $reportSchedule['send_to_type'];
            $sendTypeCC = $reportSchedule['send_cc_type'] == ReportScheduleModel::SEND_TYPE_BRANCH_PLB_OR_TPP_SUPPORT ? ReportScheduleModel::SEND_TYPE_BRANCH_TPP_SUPPORT : $reportSchedule['send_cc_type'];
            $sendTypeBCC = $reportSchedule['send_bcc_type'] == ReportScheduleModel::SEND_TYPE_BRANCH_PLB_OR_TPP_SUPPORT ? ReportScheduleModel::SEND_TYPE_BRANCH_TPP_SUPPORT : $reportSchedule['send_bcc_type'];

            if (empty($shippingLine['email'])) {
                $shippingLine['email'] = implode(',', $emailSupportTPP);
            }
            $emailTo = $this->getEmailFromProfile($sendTypeTo, $reportSchedule['send_to'], $shippingLine);
            $emailCC = $this->getEmailFromProfile($sendTypeCC, $reportSchedule['send_cc'], $shippingLine);
            $emailBCC = $this->getEmailFromProfile($sendTypeBCC, $reportSchedule['send_bcc'], $shippingLine);

            $emailTitle = "(Shipping Line) {$shippingLine['name']}: stock report tpp at {$dateLabel}";
            $emailTemplate = 'emails/basic';
            $emailData = [
                'name' => $shippingLine['name'],
                'title' => 'Shipping Line Stock Report',
                'content' => 'We would inform you about stock in our warehouse at ' . $dateLabel . ' (this email may contains attachment)',
                'email' => implode(",", $emailTo)
            ];

            $attachments = [];
            $dateFileFormat = date('Y_m_d');
            if (!empty($shippingLineStock)) {
                $attachments[] = [
                    'source' => $stockData,
                    'disposition' => 'attachment',
                    'file_name' => "report_stock_{$dateFileFormat}.xlsx",
                ];
                $emailOptions = [
                    'cc' => isset($emailCC) ? $emailCC : null,
                    'bcc' => isset($emailBCC) ? $emailBCC : null,
                    'attachment' => $attachments,
                ];
                echo 'Sending Shipping Line stock email report tpp ' . $shippingLine['name'] . '...' . PHP_EOL;
                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
                echo 'Shipping Line stock reports email were sent to tpp team...' . PHP_EOL;
            } else {
                echo 'No data, shipping line stock reports were not sent to tpp team...' . PHP_EOL;
            }
        }
        echo "report_shipping_stock ended at " . date('Y-m-d H:i:s') . PHP_EOL;
    }

    /**
     * Report daily customs
     * same format as tps or shipping activity
     * @param $period
     * @param $periodLabel
     * @param null $branch
     * @throws Exception
     */
    public function report_customs_activity($period, $periodLabel, $branch = null)
    {
        $date = new DateTime();
        $date->sub(new DateInterval("P{$period}D"));
        $dateFrom = $date->format('d F Y');
        $dateLabel = 'since ' . $dateFrom . ' until ' . date('d F Y');

        $branchName = '';
        if (!empty($branch)) {
            $branchData = $this->branchModel->getById($branch);
            if (!empty($branchData)) {
                $branchName = $branchData['branch'];
            }
        }

        $reportConditions = [
            'date_from' => $dateFrom,
            'date_type' => 'completed_at',
            'branch' => $branch
        ];

        $customs = $this->peopleModel->getByTypeBranch(PeopleModel::$TYPE_CUSTOMS, $branch);
        foreach ($customs as $custom) {
            $customContacts = $this->peopleContactModel->getContactByPerson($custom['id']);
            $customEmails = array_column(if_empty($customContacts, []), 'email');

            $reportConditions['category'] = 'INBOUND';
            $inbounds = $this->reportTppModel->getTppContainerActivity($reportConditions);

            $reportConditions['category'] = 'OUTBOUND';
            $outbounds = $this->reportTppModel->getTppContainerActivity($reportConditions);

            $reportInData = null;
            if (!empty($inbounds)) {
                foreach ($inbounds as &$inbound) {
                    unset($inbound['id_tps']);
                    unset($inbound['id_shipping_line']);
                    unset($inbound['outbound_date']);
                }
                $reportInData = $this->exporterModel->export('Inbound', $inbounds, false);
            }

            $reportOutData = null;
            if (!empty($outbounds)) {
                foreach ($outbounds as &$outbound) {
                    unset($outbound['id_tps']);
                    unset($outbound['id_shipping_line']);
                }
                $reportOutData = $this->exporterModel->export('Outbound', $outbounds, false);
            }

            echo 'Sending activity email report ' . $custom['name'] . '...' . PHP_EOL;

            if (!empty($custom['email'])) {
                $emailTo = $custom['email'];
            } else {
                $emailTo = get_setting('email_support');
            }
            $emailCC = $customEmails;
            $emailBCC = $this->email_bcc;

            $emailTitle = "{$branchName} Customs activity {$periodLabel} report {$dateLabel}";
            $emailTemplate = 'emails/basic';
            $emailData = [
                'name' => $custom['name'],
                'title' => 'Activity Report',
                'content' => 'We would inform you about latest activities in ' . $branchName . ' Transcon at ' . $dateFrom . ' (this email may contains attachment)',
                'email' => get_setting('email_support'),
            ];

            $attachments = [];
            $dateFileFormat = $date->format('Y_m_d');
            if (!empty($inbounds)) {
                $attachments[] = [
                    'source' => $reportInData,
                    'disposition' => 'attachment',
                    'file_name' => "report_in_{$dateFileFormat}.xlsx",
                    'mime' => 'application/vnd.ms-excel',
                ];
            }
            if (!empty($outbounds)) {
                $attachments[] = [
                    'source' => $reportOutData,
                    'disposition' => 'attachment',
                    'file_name' => "report_out_{$dateFileFormat}.xlsx",
                    'mime' => 'application/vnd.ms-excel',
                ];
            }

            $emailOptions = [
                'cc' => isset($emailCC) ? $emailCC : null,
                'bcc' => isset($emailBCC) ? $emailBCC : null,
                'attachment' => $attachments,
            ];

            if (!empty($inbounds) || !empty($outbounds)) {
                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
                echo 'Customs activity daily reports email were sent...' . PHP_EOL;
            } else {
                echo 'No data, customs activity reports were not sent...' . PHP_EOL;
            }

            if (ENVIRONMENT == 'development' || ENVIRONMENT == 'testing') {
                break;
            }
        }
    }

    /**
     * Get shipping line stock
     * @param null $branch
     * @throws Exception
     */
    public function report_customs_stock($branch = null)
    {
        $dateLabel = date('d F Y');

        $branchName = '';
        if (!empty($branch)) {
            $branchData = $this->branchModel->getById($branch);
            if (!empty($branchData)) {
                $branchName = $branchData['branch'];
            }
        }

        $stocks = $this->reportTppModel->getShippingLineStock(null, $branch);

        $customs = $this->peopleModel->getByTypeBranch(PeopleModel::$TYPE_CUSTOMS, $branch);
        foreach ($customs as $custom) {
            $customContacts = $this->peopleContactModel->getContactByPerson($custom['id']);
            $customEmails = array_column(if_empty($customContacts, []), 'email');

            $stockData = $this->exporterModel->export('Stock', $stocks, false);

            echo 'Sending activity email report ' . $custom['name'] . '...' . PHP_EOL;

            if (!empty($custom['email'])) {
                $emailTo = $custom['email'];
            } else {
                $emailTo = get_setting('email_support');
            }
            $emailCC = $customEmails;
            $emailBCC = $this->email_bcc;

            $emailTitle = "{$branchName} Customs {$custom['name']}: stock report at {$dateLabel}";
            $emailTemplate = 'emails/basic';
            $emailData = [
                'name' => $custom['name'],
                'title' => 'Customs Stock Report',
                'content' => 'We would inform you about stock in ' . $branchName . ' Transcon at ' . $dateLabel . ' (this email may contains attachment)',
                'email' => get_setting('email_support')
            ];

            $attachments = [];
            $dateFileFormat = date('Y_m_d');
            if (!empty($stocks)) {
                $attachments[] = [
                    'source' => $stockData,
                    'disposition' => 'attachment',
                    'file_name' => "report_stock_{$dateFileFormat}.xlsx",
                    'mime' => 'application/vnd.ms-excel',
                ];
                $emailOptions = [
                    'cc' => isset($emailCC) ? $emailCC : null,
                    'bcc' => isset($emailBCC) ? $emailBCC : null,
                    'attachment' => $attachments,
                ];
                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
                echo 'Customs stock reports email were sent...' . PHP_EOL;
            } else {
                echo 'No data, customs stock reports were not sent...' . PHP_EOL;
            }
        }
    }

    /**
     * TPP Comprehensive Weekly, TPP Comprehensive Daily (TPP Daily Report), Comprehensive
     * @param int $period
     * @param string $periodLabel
     * @param null $branch
     * @throws Exception NOTE: TPP Comprehensive Weekly = report_service_time tanpa jumlah container
     */
    public function report_comprehensive($period = 7, $periodLabel = 'daily', $branch = null)
    {
        $date = new DateTime();
        $date->sub(new DateInterval("P{$period}D"));
        $dateFrom = $date->format('d F Y');
        $getBranchByPLB = $this->branchModel->getBy(['branch_type' => "PLB"]);
        $emailSupportPLB = array_unique(array_filter(array_column($getBranchByPLB, "email_support")));
        $getBranchByTPP = $this->branchModel->getBy(['branch_type' => "TPP"]);
        $emailSupportTPP = array_unique(array_filter(array_column($getBranchByTPP, "email_support")));

        $date = new DateTime();
        $dateBefore = $period + 1;
        $date->sub(new DateInterval("P{$dateBefore}D"));
        $dateStock = $date->format('d F Y');

        $date = new DateTime();
        $date->sub(new DateInterval("P1D"));
        $dateTo = $date->format('d F Y');

        $branchName = '';
        if (!empty($branch)) {
            $branchData = $this->branchModel->getById($branch);
            if (!empty($branchData)) {
                $branchName = $branchData['branch'];
            }
        }

        echo 'Sending activity email report tpp, ' . PHP_EOL;

        $tpp_reportConditions = [
            'data' => 'realization',
            'date_type' => 'completed_at',
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'branch_type' => "TPP",
        ];
        $tpp_reportConditions['total_size'] = 20;
        $tpp_inContainer20 = $this->reportModel->getReportActivityContainer('INBOUND', $tpp_reportConditions, $branch);
        $tpp_reportConditions['total_size'] = 40;
        $tpp_inContainer40 = $this->reportModel->getReportActivityContainer('INBOUND', $tpp_reportConditions, $branch);
        $tpp_reportConditions['total_size'] = 45;
        $tpp_inContainer45 = $this->reportModel->getReportActivityContainer('INBOUND', $tpp_reportConditions, $branch);

        $tpp_reportConditions['total_size'] = 20;
        $tpp_outContainer20 = $this->reportModel->getReportActivityContainer('OUTBOUND', $tpp_reportConditions, $branch);
        $tpp_reportConditions['total_size'] = 40;
        $tpp_outContainer40 = $this->reportModel->getReportActivityContainer('OUTBOUND', $tpp_reportConditions, $branch);
        $tpp_reportConditions['total_size'] = 45;
        $tpp_outContainer45 = $this->reportModel->getReportActivityContainer('OUTBOUND', $tpp_reportConditions, $branch);

        $tpp_reportConditions = [
            'data' => 'stock',
            'branch' => $branch,
            'branch_type' => "TPP",
        ];
        $tpp_reportConditions['stock_date'] = $dateStock;
        $tpp_reportConditions['total_size'] = 20;
        $tpp_previousContainerStock20 = $this->reportStock->getStockContainers($tpp_reportConditions);
        $tpp_reportConditions['total_size'] = 40;
        $tpp_previousContainerStock40 = $this->reportStock->getStockContainers($tpp_reportConditions);
        $tpp_reportConditions['total_size'] = 45;
        $tpp_previousContainerStock45 = $this->reportStock->getStockContainers($tpp_reportConditions);

        $tpp_reportConditions['stock_date'] = $dateTo;
        $tpp_reportConditions['total_size'] = 20;
        $tpp_currentContainerStock20 = $this->reportStock->getStockContainers($tpp_reportConditions);
        $tpp_reportConditions['total_size'] = 40;
        $tpp_currentContainerStock40 = $this->reportStock->getStockContainers($tpp_reportConditions);
        $tpp_reportConditions['total_size'] = 45;
        $tpp_currentContainerStock45 = $this->reportStock->getStockContainers($tpp_reportConditions);

        // TEU = Twenty-foot equivalent Unit
        $tpp_teus = $tpp_currentContainerStock20 + ($tpp_currentContainerStock40 * 2) + ($tpp_currentContainerStock45 * 2);
        // YOR = Yard Occupancy Ratio
        // 1420 = Max Occupancy in TPP Transcon
        $tpp_yor = round((($tpp_teus / 1420) * 100), 2, PHP_ROUND_HALF_UP);

        $tpp_result = $this->reportTppModel->getGrowthContainer($branch);

        // stock <= 365 days
        $tpp_stockGrowth = $tpp_result['growth'];
        // 365 < stock < 731
        $tpp_stockSlowGrowth = $tpp_result['slow_growth'];
        // stock >= 731 days
        $tpp_stockNoGrowth = $tpp_result['no_growth'];

        $tpp_dwellingGrowth = 0;
        $tpp_dwellingSlowGrowth = 0;
        $tpp_dwellingNoGrowth = 0;

        // sum of aging time diff per stock growth less than or equal to 365
        if ($tpp_stockGrowth > 0) {
            $tpp_dwellingGrowth = round(($tpp_result['time_growth'] / $tpp_stockGrowth), 0, PHP_ROUND_HALF_UP);
        }
        // sum of aging time diff per stock growth more than 365 and less than 731
        if ($tpp_stockSlowGrowth > 0) {
            $tpp_dwellingSlowGrowth = round(($tpp_result['time_slow_growth'] / $tpp_stockSlowGrowth), 0, PHP_ROUND_HALF_UP);
        }
        // sum of aging time diff per stock growth more than or equal to 731
        if ($tpp_stockNoGrowth > 0) {
            $tpp_dwellingNoGrowth = round((($tpp_result['time_no_growth'] / $tpp_stockNoGrowth)), 0, PHP_ROUND_HALF_UP);
        }

        $tpp_dataReport = [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'previousContainerStock20' => $tpp_previousContainerStock20,
            'previousContainerStock40' => $tpp_previousContainerStock40,
            'previousContainerStock45' => $tpp_previousContainerStock45,
            'inContainer20' => $tpp_inContainer20,
            'inContainer40' => $tpp_inContainer40,
            'inContainer45' => $tpp_inContainer45,
            'outContainer20' => $tpp_outContainer20,
            'outContainer40' => $tpp_outContainer40,
            'outContainer45' => $tpp_outContainer45,
            'currentContainerStock20' => $tpp_currentContainerStock20,
            'currentContainerStock40' => $tpp_currentContainerStock40,
            'currentContainerStock45' => $tpp_currentContainerStock45,
            'teus' => $tpp_teus,
            'yor' => $tpp_yor,
            'stockGrowth' => $tpp_stockGrowth,
            'stockSlowGrowth' => $tpp_stockSlowGrowth,
            'stockNoGrowth' => $tpp_stockNoGrowth,
            'dwellingGrowth' => $tpp_dwellingGrowth,
            'dwellingSlowGrowth' => $tpp_dwellingSlowGrowth,
            'dwellingNoGrowth' => $tpp_dwellingNoGrowth
        ];

        $tpp_tableData = $this->load->view('emails/partials/_comprehensive', $tpp_dataReport, true);

        $tpp_emailTo = implode(",", $emailSupportTPP);
        $emailBCC = $this->email_bcc;

        $tpp_emailTitle = "{$branchName} Comprehensive {$periodLabel} report tpp {$dateFrom} to {$dateTo}";
        $tpp_emailTemplate = 'emails/basic';
        $tpp_emailData = [
            'name' => 'Transcon Management',
            'title' => "Comprehensive report {$periodLabel}",
            'content' => $tpp_tableData,
            'email' => implode(",", $emailSupportTPP)
        ];

        $tpp_emailOptions = [
            'bcc' => isset($emailBCC) ? $emailBCC : null,
        ];

        $this->mailer->send($tpp_emailTo, $tpp_emailTitle, $tpp_emailTemplate, $tpp_emailData, $tpp_emailOptions);
        echo 'Daily comprehensive reports email were sent to tpp team...' . PHP_EOL;

        echo 'Sending activity email report plb, ' . PHP_EOL;

        $plb_reportConditions = [
            'data' => 'realization',
            'date_type' => 'completed_at',
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'branch_type' => "PLB",
        ];

        $plb_reportConditions['total_size'] = 20;
        $plb_inContainer20 = $this->reportModel->getReportActivityContainer('INBOUND', $plb_reportConditions, $branch);
        $plb_reportConditions['total_size'] = 40;
        $plb_inContainer40 = $this->reportModel->getReportActivityContainer('INBOUND', $plb_reportConditions, $branch);
        $plb_reportConditions['total_size'] = 45;
        $plb_inContainer45 = $this->reportModel->getReportActivityContainer('INBOUND', $plb_reportConditions, $branch);

        $plb_reportConditions['total_size'] = 20;
        $plb_outContainer20 = $this->reportModel->getReportActivityContainer('OUTBOUND', $plb_reportConditions, $branch);
        $plb_reportConditions['total_size'] = 40;
        $plb_outContainer40 = $this->reportModel->getReportActivityContainer('OUTBOUND', $plb_reportConditions, $branch);
        $plb_reportConditions['total_size'] = 45;
        $plb_outContainer45 = $this->reportModel->getReportActivityContainer('OUTBOUND', $plb_reportConditions, $branch);

        $plb_reportConditions = [
            'data' => 'stock',
            'branch' => $branch,
            'branch_type' => "PLB",
        ];
        $plb_reportConditions['stock_date'] = $dateStock;
        $plb_reportConditions['total_size'] = 20;
        $plb_previousContainerStock20 = $this->reportStock->getStockContainers($plb_reportConditions);
        $plb_reportConditions['total_size'] = 40;
        $plb_previousContainerStock40 = $this->reportStock->getStockContainers($plb_reportConditions);
        $plb_reportConditions['total_size'] = 45;
        $plb_previousContainerStock45 = $this->reportStock->getStockContainers($plb_reportConditions);

        $plb_reportConditions['stock_date'] = $dateTo;
        $plb_reportConditions['total_size'] = 20;
        $plb_currentContainerStock20 = $this->reportStock->getStockContainers($plb_reportConditions);
        $plb_reportConditions['total_size'] = 40;
        $plb_currentContainerStock40 = $this->reportStock->getStockContainers($plb_reportConditions);
        $plb_reportConditions['total_size'] = 45;
        $plb_currentContainerStock45 = $this->reportStock->getStockContainers($plb_reportConditions);

        // TEU = Twenty-foot equivalent Unit
        $plb_teus = $plb_currentContainerStock20 + ($plb_currentContainerStock40 * 2) + ($plb_currentContainerStock45 * 2);
        // YOR = Yard Occupancy Ratio
        // 1420 = Max Occupancy in TPP Transcon
        $plb_yor = round((($plb_teus / 1420) * 100), 2, PHP_ROUND_HALF_UP);

        $plb_result = $this->reportTppModel->getGrowthContainer($branch);

        // stock <= 365 days
        $plb_stockGrowth = $plb_result['growth'];
        // 365 < stock < 731
        $plb_stockSlowGrowth = $plb_result['slow_growth'];
        // stock >= 731 days
        $plb_stockNoGrowth = $plb_result['no_growth'];

        $plb_dwellingGrowth = 0;
        $plb_dwellingSlowGrowth = 0;
        $plb_dwellingNoGrowth = 0;

        // sum of aging time diff per stock growth less than or equal to 365
        if ($plb_stockGrowth > 0) {
            $plb_dwellingGrowth = round(($plb_result['time_growth'] / $plb_stockGrowth), 0, PHP_ROUND_HALF_UP);
        }
        // sum of aging time diff per stock growth more than 365 and less than 731
        if ($plb_stockSlowGrowth > 0) {
            $plb_dwellingSlowGrowth = round(($plb_result['time_slow_growth'] / $plb_stockSlowGrowth), 0, PHP_ROUND_HALF_UP);
        }
        // sum of aging time diff per stock growth more than or equal to 731
        if ($plb_stockNoGrowth > 0) {
            $plb_dwellingNoGrowth = round((($plb_result['time_no_growth'] / $plb_stockNoGrowth)), 0, PHP_ROUND_HALF_UP);
        }

        $plb_dataReport = [
            'dateFrom' => $dateFrom,
            'dateTo' => $dateTo,
            'previousContainerStock20' => $plb_previousContainerStock20,
            'previousContainerStock40' => $plb_previousContainerStock40,
            'previousContainerStock45' => $plb_previousContainerStock45,
            'inContainer20' => $plb_inContainer20,
            'inContainer40' => $plb_inContainer40,
            'inContainer45' => $plb_inContainer45,
            'outContainer20' => $plb_outContainer20,
            'outContainer40' => $plb_outContainer40,
            'outContainer45' => $plb_outContainer45,
            'currentContainerStock40' => $plb_currentContainerStock40,
            'currentContainerStock45' => $plb_currentContainerStock45,
            'teus' => $plb_teus,
            'yor' => $plb_yor,
            'stockGrowth' => $plb_stockGrowth,
            'stockSlowGrowth' => $plb_stockSlowGrowth,
            'stockNoGrowth' => $plb_stockNoGrowth,
            'dwellingGrowth' => $plb_dwellingGrowth,
            'dwellingSlowGrowth' => $plb_dwellingSlowGrowth,
            'dwellingNoGrowth' => $plb_dwellingNoGrowth
        ];

        $plb_tableData = $this->load->view('emails/partials/_comprehensive', $plb_dataReport, true);

        $plb_emailTo = implode(",", $emailSupportPLB);
        $emailBCC = $this->email_bcc;

        $plb_emailTitle = "{$branchName} Comprehensive {$periodLabel} report plb {$dateFrom} to {$dateTo}";
        $plb_emailTemplate = 'emails/basic';
        $plb_emailData = [
            'name' => 'Transcon Management',
            'title' => "Comprehensive report {$periodLabel}",
            'content' => $plb_tableData,
            'email' => implode(",", $emailSupportPLB)
        ];

        $plb_emailOptions = [
            'bcc' => isset($emailBCC) ? $emailBCC : null,
        ];

        $this->mailer->send($plb_emailTo, $plb_emailTitle, $plb_emailTemplate, $plb_emailData, $plb_emailOptions);

        echo 'Daily comprehensive reports email were sent to plb team...' . PHP_EOL;
    }

    /**
     * Report stock data.
     * @param $period
     * @param $periodLabel
     * @param null $branch
     * @throws Exception
     * @throws PHPExcel_Exception
     * @throws PHPExcel_Reader_Exception
     * @throws PHPExcel_Writer_Exception
     */
    public function report_stock_branch($period, $periodLabel, $branch = null)
    {
        $branchName = '';
        if (!empty($branch)) {
            $branchData = $this->branchModel->getById($branch);
            if (!empty($branchData)) {
                $branchName = $branchData['branch'];
            }
        }

        $date = new DateTime();
        $date->sub(new DateInterval("P{$period}D"));
        $dateStock = $date->format('d F Y');
        $getBranchByPLB = $this->branchModel->getBy(['branch_type' => "PLB"]);
        $emailSupportPLB = array_unique(array_filter(array_column($getBranchByPLB, "email_support")));
        $getBranchByTPP = $this->branchModel->getBy(['branch_type' => "TPP"]);
        $emailSupportTPP = array_unique(array_filter(array_column($getBranchByTPP, "email_support")));

        $reportConditionsTPP = [
            'data' => 'stock',
            'stock_date' => $dateStock,
            'branch' => $branch,
            'branch_type' => "TPP",
        ];
        $tpp_stocks = $this->reportModel->getReportSummaryGoodsSimple($reportConditionsTPP);
        $tpp_customers = array_unique(array_column(values($tpp_stocks, []), 'owner_name'));
        $tpp_customers = $this->peopleModel->getBy([
            'ref_people.name' => values($tpp_customers, '-1')
        ]);

        foreach ($tpp_customers as $tpp_customer) {
            $reportConditionsTPP['owner'] = $tpp_customer['id'];
            $tpp_customerStocks = $this->reportModel->getReportSummaryGoodsSimple($reportConditionsTPP);
            if (!empty($tpp_customerStocks)) {
                echo 'Sending stock email report to tpp team...' . PHP_EOL;

                if (!empty($tpp_customer['email'])) {
                    $emailTo = $tpp_customer['email'];
                } else {
                    $emailTo = implode(",", $emailSupportTPP);
                }
                $emailBCC = $this->email_bcc;

                $tpp_emailTitle = "{$branchName} {$tpp_customer['name']} - Stock data report tpp until {$dateStock} ({$periodLabel})";
                $tpp_emailTemplate = 'emails/basic';
                $tpp_emailData = [
                    'name' => $tpp_customer['name'],
                    'title' => 'Stock Customer Routine Report',
                    'content' => 'We would inform you about stock in ' . $branchName . ' Transcon at ' . $dateStock . ' (this email may contains attachment)',
                    'email' => implode(",", $emailSupportTPP),
                ];

                $tpp_attachments = [];
                $dateFileFormat = $date->format('Y_m_d');
                $tpp_reportStocksData = $this->exporter->exportFromArray('Stock goods', $tpp_customerStocks, false);
                $tpp_attachments[] = [
                    'source' => $tpp_reportStocksData,
                    'disposition' => 'attachment',
                    'file_name' => "report_stock_goods_{$dateFileFormat}.xlsx",
                ];
                $tpp_emailOptions = [
                    'bcc' => isset($emailBCC) ? $emailBCC : null,
                    'attachment' => $tpp_attachments,
                ];
                $this->mailer->send($emailTo, $tpp_emailTitle, $tpp_emailTemplate, $tpp_emailData, $tpp_emailOptions);
            }
        }
        echo 'Stock customer email reports were sent to tpp team...' . PHP_EOL;

        $reportConditionsPLB = [
            'data' => 'stock',
            'stock_date' => $dateStock,
            'branch' => $branch,
            'branch_type' => "PLB",
        ];
        $plb_stocks = $this->reportModel->getReportSummaryGoodsSimple($reportConditionsPLB);

        $plb_customers = array_unique(array_column(values($plb_stocks, []), 'owner_name'));
        $plb_customers = $this->peopleModel->getBy([
            'ref_people.name' => values($plb_customers, '-1')
        ]);

        foreach ($plb_customers as $plb_customer) {
            $reportConditionsPLB['owner'] = $plb_customer['id'];
            $plb_customerStocks = $this->reportModel->getReportSummaryGoodsSimple($reportConditionsPLB);
            if (!empty($plb_customerStocks)) {
                echo 'Sending stock email report to plb team...' . PHP_EOL;

                if (!empty($plb_customer['email'])) {
                    $emailTo = $plb_customer['email'];
                } else {
                    $emailTo = implode(",", $emailSupportPLB);
                }
                $emailBCC = $this->email_bcc;

                $plb_emailTitle = "{$branchName} {$plb_customer['name']} - Stock data report plb until {$dateStock} ({$periodLabel})";
                $plb_emailTemplate = 'emails/basic';
                $plb_emailData = [
                    'name' => $plb_customer['name'],
                    'title' => 'Stock Customer Routine Report',
                    'content' => 'We would inform you about stock in ' . $branchName . ' Transcon at ' . $dateStock . ' (this email may contains attachment)',
                    'email' => implode(",", $emailSupportPLB),
                ];

                $plb_attachments = [];
                $dateFileFormat = $date->format('Y_m_d');
                $plb_reportStocksData = $this->exporter->exportFromArray('Stock goods', $plb_customerStocks, false);
                $plb_attachments[] = [
                    'source' => $plb_reportStocksData,
                    'disposition' => 'attachment',
                    'file_name' => "report_stock_goods_{$dateFileFormat}.xlsx",
                ];
                $plb_emailOptions = [
                    'bcc' => isset($emailBCC) ? $emailBCC : null,
                    'attachment' => $plb_attachments,
                ];
                $this->mailer->send($emailTo, $plb_emailTitle, $plb_emailTemplate, $plb_emailData, $plb_emailOptions);
            }
        }

        echo 'Stock customer email reports were sent to plb team...' . PHP_EOL;
    }

    /**
     * Reminder customer if outbound sppb more than 14 days and the booking is not empty (clear) yet.
     */
    public function outstanding_outbound_sppb_reminder()
    {
        $outbounds = $this->bookingModel->getOutstandingOutboundSPPB();
        $branches = $this->branchModel->getBy([
            'ref_branches.branch_type' => 'PLB'
        ]);
        $branchIds = array_column($branches, 'id');
        if (!empty($outbounds)) {
            $customerIds = array_column($outbounds, 'id_customer');
            $customers = $this->peopleModel->getById($customerIds);
            foreach ($customers as $customer) {
                $customerBookings = array_filter($outbounds, function ($outbound) use ($customer,$branchIds) {
                    return $customer['id'] == $outbound['id_customer'] && in_array($customer['id_branch'],$branchIds);
                });
                $temp_branch = [];
                foreach ($customerBookings as $branch) {
                    if(!in_array($branch['id_branch'],$temp_branch)){
                        $temp_branch[] = $branch['id_branch'];
                    }
                }
                foreach ($temp_branch as $tempBranch) {
                    $peopleBranchById = $this->peopleModel->getPeopleByIdCustomerIdBranch($customer['id'],$tempBranch);
                    $whatsapp_group = $peopleBranchById['whatsapp_group'];
                    $data_text = "*Outstanding Outbound SPPB*". "\n\n" .
                    "Hello, ".$customer['name']. "\n" .
                    "We would inform you about booking out that NOT COMPLETED since SPPB in last 30 days.". "\n";
                    $no = 1;
                    foreach ($customerBookings as $index => $booking) {
                        if($tempBranch==$booking['id_branch']){
                            $data_text .= "*" . $no++ .
                            ". Outbound* : " . substr_replace($booking['no_reference'], '...', 6, strlen($booking['no_reference']) - 12) . "\n     " .
                            "*SPPB Upload* : " . format_date($booking['sppb_upload_date'], 'd M Y') . "\n     " .
                            "*Containers Left* : " . numerical($booking['stock_booking_containers'], 0, true) . " CARGO". "\n     " .
                            "*Goods Left* : " . numerical($booking['stock_booking_goods'], 3, true) . " UNITS". "\n\n";
                        }
                        
                    }
                    $this->send_message($data_text, $whatsapp_group);
                }  
            }
        }
    }

    /**
     * Auto complete booking out.
     */
    public function auto_complete_booking_out()
    {
        $outbounds = $this->bookingModel->getOutstandingOutboundSPPB(false);
        $data_text = "";
        foreach ($outbounds as $outbound) {
            if ($outbound['stock_booking_containers'] <= 0 && $outbound['stock_booking_goods'] <= 0) {
                $booking = $this->bookingModel->getBookingById($outbound['id']);

                $bookingJobCompleted = true;
                $bookingJobGate = true;
                $safeConductCompleted = true;
                $safeConductHasAttachment = true;
                if ($booking['category'] == 'OUTBOUND') {
                    // check if all booking is completed and gate out already
                    $workOrders = $this->workOrder->getWorkOrdersByBooking($booking['id']);
                    if (empty($workOrders)) $bookingJobGate = false;
                    foreach ($workOrders as $workOrder) {
                        if ($workOrder['status'] != WorkOrderModel::STATUS_COMPLETED) {
                            $bookingJobCompleted = false;
                        }
                        if (empty($workOrder['gate_in_date']) || empty($workOrder['gate_out_date'])) {
                            $bookingJobGate = false;
                        }
                        if (!$bookingJobCompleted || !$bookingJobGate) {
                            break;
                        }
                    }

                    // check if all safe conduct is checked in security already
                    $safeConducts = $this->safeConduct->getSafeConductsByBooking($booking['id']);
                    if (empty($safeConducts)) {
                        $safeConductCompleted = false;
                        $safeConductHasAttachment = false;
                    }
                    foreach ($safeConducts as $safeConduct) {
                        if (empty($safeConduct['security_in_date']) || empty($safeConduct['security_out_date'])) {
                            $safeConductCompleted = false;
                            break;
                        }
                        $safeConductAttachments = $this->safeConductAttachment->getBy([
                            'safe_conduct_attachments.id_safe_conduct' => $safeConduct['id']
                        ]);
                        if (empty($safeConductAttachments)) {
                            $safeConductHasAttachment = false;
                            break;
                        }
                    }
                }

                if ($bookingJobCompleted && $bookingJobGate && $safeConductCompleted && $safeConductHasAttachment) {
                    $this->db->trans_start();

                    $this->bookingModel->updateBooking([
                        'status' => BookingModel::STATUS_COMPLETED
                    ], $booking['id']);

                    $this->bookingStatus->createBookingStatus([
                        'id_booking' => $booking['id'],
                        'booking_status' => BookingModel::STATUS_COMPLETED,
                        'document_status' => $booking['document_status'],
                        'no_doc' => $booking['no_reference'],
                        'doc_date' => sql_date_format($booking['reference_date'], false),
                        'description' => 'Auto completed',
                        'created_by' => 0
                    ]);

                    $this->db->trans_complete();

                    if ($this->db->trans_status()) {
                        echo 'Auto completed booking ' . $outbound['id'];
                        $branchData = $this->branchModel->getById($booking['id_branch']);
                        $active_branch = $branchData['branch'];
                        $no_reference = substr($booking['no_reference'], -4);
                        $data_text .= "[OUTBOUND COMPLETE] Outbound {$workOrder['customer_name']} aju {$no_reference} {$active_branch} automate complete.\n\n";
                    }
                }
            }
        }
        $whatsapp_group = get_setting('whatsapp_group_admin');
        $this->send_message($data_text, $whatsapp_group);
    }

    /**
     * Generate daily job document.
     */
    public function generate_daily_job_document()
    {
        $this->load->model('WorkOrderDocumentModel', 'workOrderDocument');

        $currentDate = format_date('now');

        $this->db->trans_start();

        $dates = $this->calendarModel->getBy([
            'calendars.type' => CalendarModel::TYPE_DAY_OFF
        ]);
        $dates = array_column($dates, 'date');

        $branches = $this->branchModel->getAll();
        foreach ($branches as $branch) {
            $document = $this->workOrderDocument->getBy(['id_branch' => $branch['id'], 'date' => $currentDate], true);

            if (empty($document)) {
                // exclude day off of sunday
                if (!in_array($currentDate, $dates) && date('w', strtotime($currentDate)) != 0) {
                    $this->workOrderDocument->create([
                        'id_branch' => $branch['id'],
                        'date' => format_date('now'),
                        'type' => 'JOB SUMMARY',
                        'created_by' => 0
                    ]);
                    log_message('info', "Job document branch {$branch['branch']} has been generated.");
                } else {
                    log_message('info', "{$currentDate} is a day off.");
                }
            } else {
                log_message('info', "Branch {$branch['branch']} already has document job");
            }
        }

        $this->db->trans_complete();
    }

    /**
     * Reminder upload document COO Receipt
     */
    public function coo_reminder()
    {
        $uploads = $this->upload->getAll(['date_from' => '2021-01-01']);
        $data_reminder = [];
        $nothing_reminder = [];
        foreach ($uploads as $upload) {
            $documents = $this->uploadDocument->getDocumentsByUpload($upload['id']);
            $documentTypeId = array_column($documents, 'id_document_type');

            foreach ($documents as $document) {

                $getDocType = $this->documentType->getById($document['id_document_type']);
                $getPeopleDocReminder = $this->peopleDocumentTypeReminder->getBy(['id_customer' => $upload['id_person'], 'id_document_type' => $getDocType['id']]);

                if ($getDocType['is_reminder']  && !empty($getPeopleDocReminder)) {

                    $branch =  $this->branchModel->getById($upload['id_branch']);
                    $cutOff = strtotime("2019-04-23");
                    $aju = substr($upload['description'], -4);
                    $docDate = strtotime(format_date($upload['created_at'], 'Y-m-d'));

                    if ((in_array($getDocType['reminder_document'], $documentTypeId) == true) && (in_array($getDocType['upload_document'], $documentTypeId) == false)) {

                        $reminderDocument = $this->uploadDocument->getDocumentsByUploadByDocumentType($upload['id'], $getDocType['reminder_document']);
                        $uploadDocument = $this->documentType->getById($getDocType['upload_document']);
                        $getUploadById = $this->upload->getById($reminderDocument['id_upload']);
                        $person = $this->peopleModel->getById($getUploadById['id_person']);
                        $peopleBranchById = $this->peopleModel->getPeopleByIdCustomerIdBranch($getUploadById['id_person'],$upload['id_branch']);
                        $whatsapp_group_admin = get_setting('whatsapp_group_admin');
                        $whatsapp_group = $peopleBranchById['whatsapp_group'];
                        if($peopleBranchById['id']=="74"){//if PT. Mega Setia Agung Kimia
                            continue;    
                        }
                        $dateDiff = date_diff(new Datetime($reminderDocument['document_date']), new Datetime(date('Y-m-d')));
                        $dueDate = date('d F Y', strtotime(" +" . $getDocType['reminder_overdue_day'] . " days", strtotime($reminderDocument['document_date'])));
                        $expiredDateDiff = date_diff(new Datetime($document['document_date']), new Datetime(date('Y-m-d')));
                        if ($docDate >= $cutOff) {
                            if ($dateDiff->format("%a") <= $getDocType['reminder_overdue_day'] && $dateDiff->format("%a") >= 0 && (!$getDocType['is_expired'] || ($getDocType['is_expired'] && $expiredDateDiff->format("%a") <= $getDocType['active_day']))) {
                                $title = '*Reminder Upload Document ' .$upload['no_upload'] . ' ' . $person['name'] . ' ' . $uploadDocument['document_type'] . ' ' . $aju . "*" . "\n\n";
                                // $hallo = "Hello, User\n\n";
                                $content = 'Recognizing your very busy schedule, I am sending you this note as a reminder to upload document ' . $uploadDocument['document_type'] . ' with no upload ' . $upload['no_upload'] . ' was due on date ' . $dueDate . '. For more further information please contact our customer support.';
                                $data_text = $title.$content;
                                
                                $data_reminder[] =  $document['id'];
                                $this->send_message($data_text, $whatsapp_group);
                                echo 'Reminder upload for document were sent...' . PHP_EOL;
                            }

                            if ($dateDiff->format("%a") > $getDocType['reminder_overdue_day'] && (!$getDocType['is_expired'] || ($getDocType['is_expired'] && $expiredDateDiff->format("%a") <= $getDocType['active_day']))) {
                                $title = '*Reminder Upload Document ' .$upload['no_upload'] . ' ' . $person['name'] . ' ' . $uploadDocument['document_type'] . ' ' . $aju . "*" . "\n\n";
                                $content = 'This letter is to inform you that the upload document ' . $uploadDocument['document_type'] . ' with no upload ' . $upload['no_upload'] . ' is past due since ' . $dueDate . '. We kindly request you to upload the document immediately. For more further information please contact our customer support.';
                                $data_text = $title.$content;
                                $data_reminder[] = $document['id'];
                                $this->send_message($data_text, $whatsapp_group);
                                echo 'Reminder upload for document were sent...' . PHP_EOL;
                            }
                        }
                    }

                    if ((in_array($getDocType['reminder_document'], $documentTypeId) == true) && (in_array($getDocType['upload_document'], $documentTypeId) == true)) {
                        $nothing_reminder[] = $document['id'];
                    }
                }
            }
        }
        if (isset($data_reminder) && empty($data_reminder) && isset($nothing_reminder) && !empty($nothing_reminder)) {
            $upload_document = $this->documentType->getById($getDocType['upload_document']);
            $reminder_document = $this->documentType->getById($getDocType['reminder_document']);
            $customer = $this->peopleModel->getById($upload['id_person']);
            $whatsapp_group_admin = get_setting('whatsapp_group_admin');

            if ($docDate >= $cutOff) {
                $title = "*Nothing Document Reminder*" . "\n\n";
                $content = 'This is a letter to inform you that there is nothing document reminder for uploaded. For more further information please contact our customer support.';
                $data_text = $title.$content;
                
                $this->send_message($data_text, $whatsapp_group_admin);
                echo 'Reminder upload for document were sent...' . PHP_EOL;
            }
        }
    }

    /**
     * Reminder upload document expired
     */
    public function billing_expired_reminder()
    {
        $uploads = $this->upload->getAll(['date_from' => '2021-04-24']);
        $data_reminder = [];
        foreach ($uploads as $upload) {
            $documents = $this->uploadDocument->getDocumentsByUpload($upload['id']);
            $documentTypeId = array_column($documents, 'id_document_type');

            foreach ($documents as $document) {

                $getDocType = $this->documentType->getById($document['id_document_type']);
                $getPeopleDocReminder = $this->peopleDocumentTypeReminder->getBy(['id_customer' => $upload['id_person'], 'id_document_type' => $getDocType['id']]);

                if ($getDocType['is_reminder']  && !empty($getPeopleDocReminder)) {

                    $aju = substr($upload['description'], -4);

                    if ((in_array($getDocType['reminder_document'], $documentTypeId) == true) && (in_array($getDocType['upload_document'], $documentTypeId) == false)) {

                        $reminderDocument = $this->uploadDocument->getDocumentsByUploadByDocumentType($upload['id'], $getDocType['reminder_document']);
                        $getUploadById = $this->upload->getById($reminderDocument['id_upload']);
                        $peopleBranchById = $this->peopleModel->getPeopleByIdCustomerIdBranch($getUploadById['id_person'],$upload['id_branch']);
                        $whatsapp_group = $peopleBranchById['whatsapp_group'];
                        if($peopleBranchById['id']=="74"){//if PT. Mega Setia Agung Kimia
                            continue;    
                        }

                        $dueDate = date('d F Y', strtotime(" +" . $getDocType['active_day'] . " days", strtotime($document['last_upload_file'])));
                        $expiredDateDiff = date_diff(new Datetime($document['last_upload_file']), new Datetime(date('Y-m-d')));
                        
                        if ($getDocType['is_expired'] && $expiredDateDiff->format("%a") > $getDocType['active_day']) {
                            $data_text = "Document : ".$getDocType['document_type']."\n";
                            $data_text .= "   No upload : ".$upload['no_upload']."\n";
                            $data_text .= "   Aju : ".$aju."\n";
                            $data_text .= "   Expired : ".$dueDate."\n";

                            $data_reminder[$upload['id_person'].'-'.$upload['id_branch']][] =  $data_text;
                        }
                    }
                }
            }
        }
        // print_debug($data_reminder);
        if (isset($data_reminder) && !empty($data_reminder)) {

            foreach ($data_reminder as $customerAndBranch => $texts) {
                $arrayCusBranch = explode('-',$customerAndBranch);
                $customerId = $arrayCusBranch[0];
                $branchId = $arrayCusBranch[1];
                $peopleBranchById = $this->peopleModel->getPeopleByIdCustomerIdBranch($customerId,$branchId);
                $whatsapp_group = $peopleBranchById['whatsapp_group'];
                $title = "*Expired Document Reminder*" . "\n\n";
                $content = "Hello, ".$peopleBranchById['name'].", we would inform you about document expired". "\n";
                $data_text = $title.$content;
                foreach ($texts as $key => $text) {
                    $data_text.= ($key+1).'.'.$text;
                }
                $this->send_message($data_text, $whatsapp_group);
                echo 'Reminder upload for document were sent...' . PHP_EOL;
            }
        }
    }

    /**
     * Generate daily opname.
     */
    public function opname_generate()
    {

        $this->db->trans_start();

        $branches = $this->branchModel->getAll();
        foreach ($branches as $branch) {
            $StockGoodsMerges = [];
            $StockContainersMerges = [];
            $statusGoods = "GOODS";
            $statusContainer = "CONTAINER";

            $branchId = $branch['id'];
            $LastOpnameGoods = $this->opname->getLastOpname($branchId, $statusGoods);
            $LastOpnameContainer = $this->opname->getLastOpname($branchId, $statusContainer);

            $checkGoods = !is_null($LastOpnameGoods) ? $dateDiffGoods = date_diff(new DateTime(), new Datetime($LastOpnameGoods['opname_date'])) : $dateDiffGoods = null;
            $checkContainer = !is_null($LastOpnameContainer) ? $dateDiffContainer = date_diff(new DateTime(), new Datetime($LastOpnameContainer['opname_date'])) : $dateDiffContainer = null;

            $getPublicHoliday = $this->holiday->getAllData();
            $checkHoliday = in_array(date('Y-m-d'), array_column($getPublicHoliday, "date"));
            $diff_date_goods = $dateDiffGoods == null ? 0 : $dateDiffGoods->format("%a");
            $diff_date_container = $dateDiffContainer == null ? 0 : $dateDiffContainer->format("%a");

            if (!is_null($branch['opname_day_name'])) {
                if (((!empty($LastOpnameGoods)) && ($diff_date_goods >= $branch['opname_day']) && (date('l') == $branch['opname_day_name'])) || (empty($LastOpnameGoods) && empty($LastOpnameContainer) && date('D') == $branch['opname_day_name']) || (empty($LastOpnameGoods) && !empty($LastOpnameContainer) && $diff_date_container >= $branch['opname_day'] && date('l') == $branch['opname_day_name'])
                ) {

                    $StockGoods = $this->reportStock->getStockGoods([
                        'data' => 'all',
                        'branch' => $branchId,
                        'quantity' => 1,
                    ]);

                    $StockGoodsMerges = array_merge($StockGoodsMerges, $StockGoods);
                    $opnameCheckGoods = $this->opname->opnameCheck($branchId, $statusGoods, date('Y-m-d'));
                    if ($branch['opname_day'] > 0) {
                        if (empty($opnameCheckGoods)) {
                            if (!empty($StockGoodsMerges)) {
                                $noOpname = $this->opname->getAutoNumberOpname();
                                $this->opname->create([
                                    'id_branch' => $branchId,
                                    'no_opname' => $noOpname,
                                    'opname_date' => date('Y-m-d'),
                                    'opname_type' => "GOODS",
                                    'status' => 'PENDING'
                                ]);
                                $opnameId = $this->db->insert_id();
                                foreach ($StockGoodsMerges as $stockGoodsMerge) {
                                    $this->opnameGoods->create([
                                        'id_opname' => $opnameId,
                                        'id_owner' => $stockGoodsMerge['id_owner'],
                                        'id_booking' => $stockGoodsMerge['id_booking'],
                                        'id_goods' => $stockGoodsMerge['id_goods'],
                                        'id_unit' => $stockGoodsMerge['id_unit'],
                                        'id_position' => $stockGoodsMerge['id_position'],
                                        'id_position_blocks' => $stockGoodsMerge['id_position_blocks'],
                                        'position_block' => $stockGoodsMerge['position_blocks'],
                                        'no_pallet' => $stockGoodsMerge['no_pallet'],
                                        'no_reference' => $stockGoodsMerge['no_reference'],
                                        'ex_no_container' => $stockGoodsMerge['ex_no_container'],
                                        'quantity' => $stockGoodsMerge['stock_quantity'],
                                        'tonnage' => $stockGoodsMerge['stock_weight'],
                                        'tonnage_gross' => $stockGoodsMerge['stock_gross_weight'],
                                        'volume' => $stockGoodsMerge['stock_volume'],
                                        'status_danger' => $stockGoodsMerge['status_danger'],
                                        'description' => $stockGoodsMerge['description'],
                                    ]);
                                }
                                echo "Opname goods branch {$branch['branch']} has been generated."."<br/>";
                            } else {
                                echo "Data goods branch {$branch['branch']} is empty."."<br/>";
                            }
                        } else {
                            echo "Opname goods branch {$branch['branch']} is already exist."."<br/>";
                        }
                    } else {
                        echo "Opname goods day branch {$branch['branch']} is not set."."<br/>";
                    }
                } else {
                    echo "Opname goods branch {$branch['branch']} is a day off."."<br/>";
                }

                if (((!empty($LastOpnameContainer)) && ($diff_date_container >= $branch['opname_day']) && (date('l') == $branch['opname_day_name'])) || ((empty($LastOpnameContainer) && empty($LastOpnameGoods))  && (date('l') == $branch['opname_day_name'])) || ((empty($LastOpnameContainer) && !empty($LastOpnameGoods) && $diff_date_goods >= $branch['opname_day'] && date('l') == $branch['opname_day_name']))
                ) {

                    $StockContainers = $this->reportStock->getStockContainers([
                        'data' => 'all',
                        'branch' => $branchId,
                        'quantity' => 1,
                    ]);
                    $StockContainersMerges = array_merge($StockContainersMerges, $StockContainers);
                    $opnameCheckContainer = $this->opname->opnameCheck($branchId, $statusContainer, date('Y-m-d'));
                    if ($branch['opname_day'] > 0) {
                        if (empty($opnameCheckContainer)) {
                            if (!empty($StockContainersMerges)) {
                                $noOpnameContainer = $this->opname->getAutoNumberOpname();
                                $this->opname->create([
                                    'id_branch' => $branchId,
                                    'no_opname' => $noOpnameContainer,
                                    'opname_date' => date('Y-m-d'),
                                    'opname_type' => "CONTAINER",
                                    'status' => 'PENDING'
                                ]);

                                $opnameId = $this->db->insert_id();

                                foreach ($StockContainersMerges as $StockContainersMerge) {
                                    $this->opnameContainer->create([
                                        'id_opname' => $opnameId,
                                        'id_owner' => $StockContainersMerge['id_owner'],
                                        'id_booking' => $StockContainersMerge['id_booking'],
                                        'id_container' => $StockContainersMerge['id_container'],
                                        'id_position' => $StockContainersMerge['id_position'],
                                        'no_reference' => $StockContainersMerge['no_reference'],
                                        'id_position_blocks' => $StockContainersMerge['id_position_blocks'],
                                        'position_block' => $StockContainersMerge['position_blocks'],
                                        'quantity' => $StockContainersMerge['stock'],
                                        'seal' => $StockContainersMerge['seal'],
                                        'status_danger' => $StockContainersMerge['status_danger'],
                                        'description' => $StockContainersMerge['description'],
                                    ]);
                                }
                                echo "Opname container branch {$branch['branch']} has been generated."."<br/>";
                            } else {
                                echo "Data container {$branch['branch']} is empty."."<br/>";
                            }
                        } else {
                            echo "Opname container branch {$branch['branch']} is already exist."."<br/>";
                        }
                    } else {
                        echo "Opname container day branch {$branch['branch']} is not set."."<br/>";
                    }
                } else {
                    echo "Opname container day {$branch['branch']} is a day off."."<br/>";
                }
            } else {
                echo "Name of the opname day {$branch['branch']} is not set."."<br/>";
            }
        }
        $this->db->trans_complete();
    }

    /**
     * Generate daily cycleCount.
     */
    public function cycle_count_generate()
    {

        $this->db->trans_start();

        $branches = $this->branchModel->getAll();
        $request_date = date('Y-m-d');
        foreach ($branches as $branch) {
            $goodsFromActivity = [];
            $containerFromActivity = [];
            $goodsFromActivityOut = [];
            $containerFromActivityOut = [];
            $goods_merges = [];
            $container_merges = [];
            $data_random = [];
            $data_random_container = [];
            $data_random2 = [];
            $data_random_container2 = [];

            $typeGoods = "GOODS";
            $typeContainer = "CONTAINER";
            $branchId = $branch['id'];
            $branchbyId = $this->branchModel->getById($branchId);
            $LastCycleCountGoods = $this->cycleCount->getLastDataByBranchByTypeByActivity($branchId, $typeGoods, 'ALL');
            $LastCycleCountContainer = $this->cycleCount->getLastDataByBranchByTypeByActivity($branchId, $typeContainer, 'ALL');

            $dateDiffGoods = !is_null($LastCycleCountGoods) ?  date_diff(new DateTime($request_date), new Datetime($LastCycleCountGoods['cycle_count_date'])) : false;
            $dateDiffContainer = !is_null($LastCycleCountContainer) ?  date_diff(new DateTime($request_date), new Datetime($LastCycleCountContainer['cycle_count_date'])) : false;
            $date_from_goods = !is_null($LastCycleCountGoods) ? $LastCycleCountGoods['cycle_count_date'] : date('Y-m-d');
            $date_from_container = !is_null($LastCycleCountContainer) ? $LastCycleCountContainer['cycle_count_date'] : date('Y-m-d');

            $getPublicHoliday = $this->holiday->getAllData();
            $checkHoliday = in_array($request_date, array_column($getPublicHoliday, "date"));

            if(!$checkHoliday){

                // GOODS ENTRY
                $checkCycleCount = $this->cycleCount->CycleCountCheck($branchId, $typeGoods, date('Y-m-d'));
                if(empty($checkCycleCount)){
                    if( ((!empty($LastCycleCountGoods) && ($dateDiffGoods->format("%a") >= $branch['cycle_count_day']))  ||
                            (empty($LastCycleCountGoods))) && ($branchbyId['cycle_count_day'] > 0 && $branchbyId['cycle_count_goods'] > 0) ){

                        $activityIn = $this->reportModel->getGoodsStockMove(['date_from' => $date_from_goods, 'date_to' => date('Y-m-d'), 'multiplier' => 1, 'branch' => $branchId]);
                        $activityOut= $this->reportModel->getGoodsStockMove(['date_from' => $date_from_goods, 'date_to' => date('Y-m-d'), 'multiplier' => -1, 'branch' => $branchId]);
                        $activityInOuts = array_merge($activityIn, $activityOut);

                        foreach($activityInOuts as $activityInOut){
                            $data = $this->reportStock->getStockGoods([
                                'data' => 'ALL',
                                'branch' => $activityInOut['id_branch'],
                                'booking' => $activityInOut['id_booking'],
                                'search' => $activityInOut['goods_name']
                            ]);
                            $goodsFromActivity = array_merge($goodsFromActivity, $data);
                        }

                        foreach($activityOut as $stockOut){
                            $dataOut = $this->reportStock->getStockGoods([
                                'data' => 'ALL',
                                'branch' => $stockOut['id_branch'],
                                'booking' => $stockOut['id_booking'],
                                'search' => $stockOut['goods_name']
                            ]);
                            $goodsFromActivityOut = array_merge($goodsFromActivityOut, $dataOut);
                        }

                        $duplicateFields = ["id_owner" => array(), "id_booking" => array(), "id_goods" => array(), "id_unit" => array()];
                        foreach ($goodsFromActivity as $index => $value) {
                            if(in_array($value['id_owner'], $duplicateFields['id_owner']) && in_array($value['id_booking'], $duplicateFields['id_booking']) && in_array($value['id_goods'], $duplicateFields['id_goods']) && in_array($value['id_unit'], $duplicateFields['id_unit']) )
                            {
                                unset($goodsFromActivity[$index]);
                            }else{
                                array_push($duplicateFields['id_owner'], $value['id_owner']);
                                array_push($duplicateFields['id_booking'], $value['id_booking']);
                                array_push($duplicateFields['id_goods'], $value['id_goods']);
                                array_push($duplicateFields['id_unit'], $value['id_unit']);
                            }
                        }
                        $totalGoodsFromActivity = count($goodsFromActivity);
                        $totalGoodsFromAge = $branchbyId['cycle_count_goods'] - $totalGoodsFromActivity;

                        // get random goods from activity
                        $totalRandom = $totalGoodsFromActivity >= $branchbyId['cycle_count_goods'] ? $branchbyId['cycle_count_goods'] : $totalGoodsFromActivity;
                        if ($totalRandom == 1) {
                            $random = array_rand($goodsFromActivity, $totalRandom);
                            $data_random[] = $goodsFromActivity[$random];
                        } else
                            if (($totalRandom != 1) && ($totalRandom != 0)) {
                                $random = array_rand($goodsFromActivity, $totalRandom);
                                foreach ($random as $key => $value) {
                                    $data_random[] = $goodsFromActivity[$value];
                                }
                            }
                        $goods_merges = array_merge($goods_merges, $data_random);

                        // get random goods from age
                        if ($totalGoodsFromAge > 0) {
                            $column = 25;

                            $stock_goods = $this->reportStock->getStockGoods([
                                'data' => 'stock',
                                'start' => 0,
                                'length' => 20,
                                'order_by' => $column,
                                'order_method' => 'ASC',
                                'branch' => $branchId
                            ]);

                            $totalGoods = count($stock_goods['data']);

                            if ($totalGoods > 0) {
                                $random_goods = array_rand($stock_goods['data'], $totalGoods > $totalGoodsFromAge ? $totalGoodsFromAge : $totalGoods);
                                foreach ($random_goods as $key => $value) {
                                    $data_random2[] = $stock_goods['data'][$value];
                                }
                                $goods_merges = array_merge($goods_merges, $data_random2);
                            }
                        }

                        $goods_merges = array_merge($goods_merges, $goodsFromActivityOut);
                        $duplicateFieldOuts = ["id_owner" => array(), "id_booking" => array(), "id_goods" => array(), "id_unit" => array()];
                        foreach ($goods_merges as $key => $val) {
                            if(in_array($val['id_owner'], $duplicateFieldOuts['id_owner']) && in_array($val['id_booking'], $duplicateFieldOuts['id_booking']) && in_array($val['id_goods'], $duplicateFieldOuts['id_goods']) && in_array($val['id_unit'], $duplicateFieldOuts['id_unit']) )
                            {
                                unset($goods_merges[$key]);
                            }else{
                                array_push($duplicateFieldOuts['id_owner'], $val['id_owner']);
                                array_push($duplicateFieldOuts['id_booking'], $val['id_booking']);
                                array_push($duplicateFieldOuts['id_goods'], $val['id_goods']);
                                array_push($duplicateFieldOuts['id_unit'], $val['id_unit']);
                            }
                        }

                        $noCycleCount = $this->cycleCount->getAutoNumberCycleCount();
                        $this->cycleCount->create([
                            'id_branch' => $branchId,
                            'no_cycle_count' => $noCycleCount,
                            'cycle_count_date' =>  date('Y-m-d'),
                            'type' => $typeGoods,
                            'activity_type' => "ALL",
                            'description' => null,
                            'status' => 'PENDING'
                        ]);

                        $cycleCountID = $this->db->insert_id();

                        if(!empty($goods_merges)){
                            foreach ($goods_merges as $goods_merge){
                                $this->cycleCountGoods->create([
                                    'id_cycle_count' => $cycleCountID,
                                    'id_owner' => $goods_merge['id_owner'],
                                    'id_booking' => $goods_merge['id_booking'],
                                    'id_goods' => $goods_merge['id_goods'],
                                    'id_unit' => $goods_merge['id_unit'],
                                    'id_position' => $goods_merge['id_position'],
                                    'id_position_blocks' => $goods_merge['id_position_blocks'],
                                    'position_block' => $goods_merge['position_blocks'],
                                    'ex_no_container' => $goods_merge['no_container'],
                                    'quantity' => $goods_merge['stock_quantity'],
                                    'tonnage' => $goods_merge['stock_tonnage'],
                                    'tonnage_gross' => $goods_merge['stock_tonnage_gross'],
                                    'volume' => $goods_merge['stock_volume'],
                                    'status_danger' => $goods_merge['status_danger'],
                                    'description' => $goods_merge['description'],
                                ]);
                            }
                        }
                        echo ("Cycle Count goods branch {$branch['branch']} with all activities has been generated.");
                    }else{

                        $activityIn = $this->reportModel->getGoodsStockMove(['date_from' => $date_from_goods, 'date_to' => date('Y-m-d'), 'multiplier' => 1, 'branch' => $branchId]);
                        $activityOut= $this->reportModel->getGoodsStockMove(['date_from' => $date_from_goods, 'date_to' => date('Y-m-d'), 'multiplier' => -1, 'branch' => $branchId]);
                        $activityInOuts = array_merge($activityIn, $activityOut);

                        foreach($activityOut as $stockOut){
                            $dataOut = $this->reportStock->getStockGoods([
                                'data' => 'ALL',
                                'branch' => $stockOut['id_branch'],
                                'booking' => $stockOut['id_booking'],
                                'search' => $stockOut['goods_name']
                            ]);
                            $goodsFromActivityOut = array_merge($goodsFromActivityOut, $dataOut);
                        }

                        $duplicateFieldOuts = ["id_owner" => array(), "id_booking" => array(), "id_goods" => array(), "id_unit" => array()];
                        foreach ($goodsFromActivityOut as $key => $val) {
                            if(in_array($val['id_owner'], $duplicateFieldOuts['id_owner']) && in_array($val['id_booking'], $duplicateFieldOuts['id_booking']) && in_array($val['id_goods'], $duplicateFieldOuts['id_goods']) && in_array($val['id_unit'], $duplicateFieldOuts['id_unit']) )
                            {
                                unset($goodsFromActivityOut[$key]);
                            }else{
                                array_push($duplicateFieldOuts['id_owner'], $val['id_owner']);
                                array_push($duplicateFieldOuts['id_booking'], $val['id_booking']);
                                array_push($duplicateFieldOuts['id_goods'], $val['id_goods']);
                                array_push($duplicateFieldOuts['id_unit'], $val['id_unit']);
                            }
                        }

                        $noCycleCount = $this->cycleCount->getAutoNumberCycleCount();
                        $this->cycleCount->create([
                            'id_branch' => $branchId,
                            'no_cycle_count' => $noCycleCount,
                            'cycle_count_date' =>  date('Y-m-d'),
                            'type' => $typeGoods,
                            'activity_type' => "OUT",
                            'description' => null,
                            'status' => 'PENDING'
                        ]);
                        $cycleCountID = $this->db->insert_id();

                        if(!empty($goodsFromActivityOut)){
                            foreach ($goodsFromActivityOut as $goods_out){
                                $this->cycleCountGoods->create([
                                    'id_cycle_count' => $cycleCountID,
                                    'id_owner' => $goods_out['id_owner'],
                                    'id_booking' => $goods_out['id_booking'],
                                    'id_goods' => $goods_out['id_goods'],
                                    'id_unit' => $goods_out['id_unit'],
                                    'id_position' => $goods_out['id_position'],
                                    'id_position_blocks' => $goods_out['id_position_blocks'],
                                    'position_block' => $goods_out['position_blocks'],
                                    'ex_no_container' => $goods_out['no_container'],
                                    'quantity' => $goods_out['stock_quantity'],
                                    'tonnage' => $goods_out['stock_tonnage'],
                                    'tonnage_gross' => $goods_out['stock_tonnage_gross'],
                                    'volume' => $goods_out['stock_volume'],
                                    'status_danger' => $goods_out['status_danger'],
                                    'description' => $goods_out['description'],
                                ]);
                            }
                        }
                        echo ("Cycle Count goods branch {$branch['branch']} with out activity has been generated.");
                    }
                }else{
                    echo ("Cycle Count goods is already exist.");
                }

                // CONTAINER ENTRY
                $checkContainerCycleCount = $this->cycleCount->CycleCountCheck($branchId, $typeContainer, date('Y-m-d'));
                if(empty($checkContainerCycleCount)){
                    if( ((!empty($LastCycleCountContainer) && ($dateDiffContainer->format("%a") >= $branch['cycle_count_day']))  ||
                            empty($LastCycleCountContainer)) && ($branchbyId['cycle_count_day'] > 0 && $branchbyId['cycle_count_container'] > 0) ){

                        $activityIn = $this->reportModel->getContainerStockMove(['date_from' => $date_from_container, 'date_to' => date('Y-m-d'), 'multiplier' => 1, 'branch' => $branchId]);
                        $activityOut= $this->reportModel->getContainerStockMove(['date_from' => $date_from_container, 'date_to' => date('Y-m-d'), 'multiplier' => -1, 'branch' => $branchId]);
                        $activityInOutContainers = array_merge($activityIn, $activityOut);

                        foreach($activityInOutContainers as $activityInOutContainer){
                            $dataContainer = $this->reportStock->getStockContainers([
                                'data' => 'ALL',
                                'branch' => $activityInOutContainer['id_branch'],
                                'booking' => $activityInOutContainer['id_booking_in'],
                                'search' => $activityInOutContainer['no_container']
                            ]);
                            $containerFromActivity = array_merge($containerFromActivity, $dataContainer);
                        }

                        foreach($activityOut as $stockOut){
                            $dataOut = $this->reportStock->getStockContainers([
                                'data' => 'ALL',
                                'branch' => $stockOut['id_branch'],
                                'booking' => $stockOut['id_booking_in'],
                                'search' => $stockOut['no_container']
                            ]);
                            $containerFromActivityOut = array_merge($containerFromActivityOut, $dataOut);
                        }

                        $duplicateFields = ["id_owner" => array(), "id_booking" => array(), "id_container" => array()];
                        foreach ($containerFromActivity as $index => $value) {
                            if(in_array($value['id_owner'], $duplicateFields['id_owner']) && in_array($value['id_booking'], $duplicateFields['id_booking']) && in_array($value['id_container'], $duplicateFields['id_container'])){
                                unset($containerFromActivity[$index]);
                            }else{
                                array_push($duplicateFields['id_owner'], $value['id_owner']);
                                array_push($duplicateFields['id_booking'], $value['id_booking']);
                                array_push($duplicateFields['id_container'], $value['id_container']);
                            }
                        }

                        $totalContainerFromActivity = count($containerFromActivity);
                        $totalContainerFromAge = $branchbyId['cycle_count_container'] - $totalContainerFromActivity;

                        // get random container from activity
                        $totalRandomContainer = $totalContainerFromActivity >= $branchbyId['cycle_count_container'] ? $branchbyId['cycle_count_container'] : $totalContainerFromActivity;
                        if ($totalRandomContainer == 1) {
                            $randomContainer = array_rand($containerFromActivity, $totalRandomContainer);
                            $data_random_container[] = $containerFromActivity[$randomContainer];
                        } else
                            if (($totalRandomContainer != 1) && ($totalRandomContainer != 0)) {
                                $randomContainer = array_rand($containerFromActivity, $totalRandomContainer);
                                foreach ($randomContainer as $key => $valueContainer) {
                                    $data_random_container[] = $containerFromActivity[$valueContainer];
                                }
                            }
                        $container_merges = array_merge($container_merges, $data_random_container);

                        // get random container from age
                        if ($totalContainerFromAge > 0) {
                            $column = 15;

                            $stock_container = $this->reportStock->getStockContainers([
                                'data' => 'stock',
                                'start' => 0,
                                'length' => 20,
                                'order_by' => $column,
                                'order_method' => 'ASC',
                                'branch' => $branchId
                            ]);
                            $totalContainer = count($stock_container['data']);

                            if ($totalContainer > 0) {
                                $random_container = array_rand($stock_container['data'], $totalContainer > $totalContainerFromAge ? $totalContainerFromAge : $totalContainer);
                                if(!empty($random_container)) {
                                    foreach ($random_container as $key => $value) {
                                        $data_random_container2[] = $stock_container['data'][$value];
                                    }

                                    $container_merges = array_merge($container_merges, $data_random_container2);
                                }
                            }
                        }

                        $container_merges = array_merge($container_merges, $containerFromActivityOut);
                        $duplicateFieldOuts = ["id_owner" => array(), "id_booking" => array(), "id_container" => array()];
                        foreach ($container_merges as $key => $val) {
                            if(in_array($val['id_owner'], $duplicateFieldOuts['id_owner']) && in_array($val['id_booking'], $duplicateFieldOuts['id_booking']) && in_array($val['id_container'], $duplicateFieldOuts['id_container']) )
                            {
                                unset($container_merges[$key]);
                            }else{
                                array_push($duplicateFieldOuts['id_owner'], $val['id_owner']);
                                array_push($duplicateFieldOuts['id_booking'], $val['id_booking']);
                                array_push($duplicateFieldOuts['id_container'], $val['id_container']);
                            }
                        }

                        $noCycleCount = $this->cycleCount->getAutoNumberCycleCount();
                        $this->cycleCount->create([
                            'id_branch' => $branchId,
                            'no_cycle_count' => $noCycleCount,
                            'cycle_count_date' =>  date('Y-m-d'),
                            'type' => $typeContainer,
                            'description' => null,
                            'status' => 'PENDING'
                        ]);

                        $cycleCountID = $this->db->insert_id();
                        if(!empty($container_merges)){
                            foreach ($container_merges as $container_merge){
                                $this->cycleCountContainer->create([
                                    'id_cycle_count' => $cycleCountID,
                                    'id_owner' => $container_merge['id_owner'],
                                    'id_booking' => $container_merge['id_booking'],
                                    'id_container' => $container_merge['id_container'],
                                    'id_position' => $container_merge['id_position'],
                                    'id_position_blocks' => $container_merge['id_position_blocks'],
                                    'position_block' => $container_merge['position_blocks'],
                                    'quantity' => $container_merge['stock'],
                                    'no_reference' => $container_merge['no_reference'],
                                    'seal' => $container_merge['seal'],
                                    'status_danger' => $container_merge['status_danger'],
                                    'description' => $container_merge['description'],
                                ]);
                            }
                        }
                        echo ("Cycle Count container branch {$branch['branch']} with all activities has been generated.");
                    }else{

                        $activityIn = $this->reportModel->getContainerStockMove(['date_from' => $date_from_container, 'date_to' => date('Y-m-d'), 'multiplier' => 1, 'branch' => $branchId]);
                        $activityOut = $this->reportModel->getContainerStockMove(['date_from' => $date_from_container, 'date_to' => date('Y-m-d'), 'multiplier' => -1, 'branch' => $branchId]);
                        $activityInOutContainers = array_merge($activityIn, $activityOut);

                        foreach($activityOut as $stockOut){
                            $dataOut = $this->reportStock->getStockContainers([
                                'data' => 'ALL',
                                'branch' => $stockOut['id_branch'],
                                'booking' => $stockOut['id_booking_in'],
                                'search' => $stockOut['no_container']
                            ]);
                            $containerFromActivityOut = array_merge($containerFromActivityOut, $dataOut);
                        }

                        $duplicateFieldOuts = ["id_owner" => array(), "id_booking" => array(), "id_container" => array()];
                        foreach ($containerFromActivityOut as $key => $val) {
                            if(in_array($val['id_owner'], $duplicateFieldOuts['id_owner']) && in_array($val['id_booking'], $duplicateFieldOuts['id_booking']) && in_array($val['id_container'], $duplicateFieldOuts['id_container']) )
                            {
                                unset($containerFromActivityOut[$key]);
                            }else{
                                array_push($duplicateFieldOuts['id_owner'], $val['id_owner']);
                                array_push($duplicateFieldOuts['id_booking'], $val['id_booking']);
                                array_push($duplicateFieldOuts['id_container'], $val['id_container']);
                            }
                        }

                        $noCycleCount = $this->cycleCount->getAutoNumberCycleCount();
                        $this->cycleCount->create([
                            'id_branch' => $branchId,
                            'no_cycle_count' => $noCycleCount,
                            'cycle_count_date' =>  date('Y-m-d'),
                            'type' => $typeContainer,
                            'activity_type' => "OUT",
                            'description' => null,
                            'status' => 'PENDING'
                        ]);
                        $cycleCountID = $this->db->insert_id();

                        if(!empty($containerFromActivityOut)){
                            foreach ($containerFromActivityOut as $container_out){
                                $this->cycleCountContainer->create([
                                    'id_cycle_count' => $cycleCountID,
                                    'id_owner' => $container_out['id_owner'],
                                    'id_booking' => $container_out['id_booking'],
                                    'id_container' => $container_out['id_container'],
                                    'id_position' => $container_out['id_position'],
                                    'id_position_blocks' => $container_out['id_position_blocks'],
                                    'position_block' => $container_out['position_blocks'],
                                    'quantity' => $container_out['stock'],
                                    'no_reference' => $container_out['no_reference'],
                                    'seal' => $container_out['seal'],
                                    'status_danger' => $container_out['status_danger'],
                                    'description' => $container_out['description'],
                                ]);
                            }
                        }
                        echo ("Cycle Count container branch {$branch['branch']} with out activity has been generated.");
                    }
                }else{
                    echo ("Cycle Count container is already exist");
                }
            }else{
                echo ("Cycle Count day is a day off.");
            }
        }

        $this->db->trans_complete();
    }

    /**
     * Generate daily freetime do notification.
     */
    public function generate_daily_freetime_do_notification()
    {
        $branches = $this->branchModel->getAll();

        echo 'Sending freetime delivery order notification...' . PHP_EOL;
        foreach ($branches as $branch) {

            $container_data = [];
            $stockContainers = $this->reportStock->getStockContainers(['branch' => $branch['id']]);

            if (!empty($stockContainers)) {
                foreach ($stockContainers as $stockContainer) { // ambil stock container
                    $people = "";
                    $uploads = $this->upload->getUploadsByBookingId($stockContainer['id_booking']);
                    if (isset($uploads)) { // Ambil data upload dari no booking yang ada di container
                        $getDocumentsByUpload = $this->uploadDocument->getDocumentsByUpload($uploads['id']);
                        $people = $this->peopleModel->getById($uploads['id_person']);
                    }

                    if (isset($getDocumentsByUpload) && !empty($getDocumentsByUpload)) {  // jika dokumen upload tidak kosong
                        $getData = in_array(DocumentTypeModel::DOC_DO, array_column($getDocumentsByUpload, 'document_type'));
                        if ($getData) { // jika terdapat dokumen DO pada data upload yang diperoleh dari booking tsb.

                            $aju = substr($uploads['description'], -3);
                            $dateTime = strtotime(date("Y-m-d"));
                            $getDocumentsByUploadByDocumentType = $this->uploadDocument->getDocumentsByUploadByDocumentType($uploads['id'], null, null, false, DocumentTypeModel::DOC_DO);
                            $freetimeDate = null;
                            if (isset($getDocumentsByUploadByDocumentType['freetime_date'])) { //jika ada upload dokumen
                                $freetimeDate = date('Y-m-d', strtotime($getDocumentsByUploadByDocumentType['freetime_date']));
                            }
                            $expDate = null;
                            if (isset($getDocumentsByUploadByDocumentType['expired_date'])) {
                                $expDate = date('Y-m-d', strtotime($getDocumentsByUploadByDocumentType['expired_date']));
                            }
                            if (!is_null($freetimeDate) && !is_null($expDate) && $getDocumentsByUploadByDocumentType['subtype'] == "COC") { // jika kolom freetime tidak null dan subtype dokumen DO adalah COC

                                // tanggal notifikasi diperoleh dari beberapa hari sebelum tanggal freetime (settingan beberapa hari didapatkan dari settingan do notif yang ada dibranch)
                                $notificationDate = (date('Y-m-d', strtotime("-" . $branch['do_notif'] . " day", strtotime($freetimeDate))));

                                if ($branch['branch_type'] == BranchModel::BRANCH_TYPE_PLB) { //khusus PLB
                                    if (strtotime($notificationDate) <= $dateTime && $dateTime <= strtotime($freetimeDate)) { //jika tanggal notifikasi lebih kecil sama dg dari tanggal hari ini dan tanggal hari ini lebih kecil sama dg tanggal freetime
                                        $emailTo = $branch['email_operational'];
                                        $emailTitle = $uploads['no_upload'] . ' ' . $people['name'] . ' ' . $stockContainer['no_container'] . ' ' . "AJU" . ' ' . $aju . ' Freetime Delivery Order Notification';
                                        $emailTemplate = "emails/basic";
                                        $emailData = [
                                            'title' => 'Freetime Delivery Order Notification',
                                            'name' => 'TCI Admin Operational',
                                            'email' => $emailTo,
                                            'content' => 'Recognizing your very busy schedule, I am sending you this note as a reminder to container checkout <b>' . $stockContainer['no_container'] . '</b> with no upload ' . $uploads['no_upload'] . ' and owner ' . $stockContainer['owner_name'] . ' was due on date ' . $freetimeDate . '. For more further information please contact our customer support.'

                                        ];
                                        //$this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
                                    }

                                    if ($dateTime > strtotime($freetimeDate)) { // jika tanggal hari ini melebihi tanggal freetime
                                        $emailTo = $branch['email_operational'];
                                        $emailTitle = $uploads['no_upload'] . ' ' . $people['name'] . ' ' . $stockContainer['no_container'] . ' ' . "AJU" . ' ' . $aju . ' Freetime Delivery Order Notification';
                                        $emailTemplate = "emails/basic";
                                        $emailData = [
                                            'title' => 'Freetime Delivery Order Notification',
                                            'name' => 'TCI Admin Operational',
                                            'email' => $emailTo,
                                            'content' => 'Container <b>' . $stockContainer['no_container'] . '</b> with no upload ' . $uploads['no_upload'] . ' and owner ' . $stockContainer['owner_name'] . ' is past due since.' . date('d F Y', strtotime($freetimeDate)) . ' We kindly request you to container checkout immediately. For more further information please contact our customer support.'

                                        ];
                                        //$this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);
                                    }
                                }

                                $isEmpty = $stockContainer['is_empty'] ? 'EMPTY' : 'FULL';
                                $container_data[] = [
                                    "id_work_order_container" => $stockContainer["id_work_order_container"],
                                    "customer" => $people['name'],
                                    "no_container" => $stockContainer['no_container'],
                                    "is_empty" => $isEmpty,
                                    "freetime" =>  $freetimeDate,
                                    "exp_date" => $expDate,
                                ];
                            }
                        }
                    }
                }
                $no = 0;
                $data_table = [];
                array_multisort(array_column($container_data, 'freetime'), SORT_ASC, array_column($container_data, 'exp_date'), SORT_ASC, array_column($container_data, 'is_empty'), SORT_DESC, $container_data);
                if (!empty($container_data)) {
                    if ($branch['branch_type'] == BranchModel::BRANCH_TYPE_PLB) { //khusus PLB
                        foreach ($container_data as $data) {
                            $no = $no + 1;
                            $count_no = (strlen($no)) - 1;
                            $str_space = str_repeat("  ", $count_no);
                            $space = "     " . $str_space;
                            $data_table[] =  $no . ".  " . strtoupper($data['customer']) . "\n" . $space . $data['no_container'] . " *(" . $data['is_empty'] . ")*" . "\n" . $space . "*Expired DO : " . date("d F 'y", strtotime($data['exp_date'])) . "*" . "\n" . $space . "*Freetime : " . date("d F 'y", strtotime($data['freetime'])) . "*";
                        }
                        $data_table = "*Daftar Expired DO & Freetime " . $branch['description'] . " Per " . date("d F Y") . "*" . "\n\n" . implode("\n\n", $data_table);
                        $whatsapp_group_internal = $branch['whatsapp_group'];
                        if (!empty($whatsapp_group_internal)) {
                            $this->send_message($data_table, $whatsapp_group_internal);
                        }
                    }
                } else {
                    if ($branch['branch_type'] == BranchModel::BRANCH_TYPE_PLB) { //khusus PLB
                        $whatsapp_group_internal = $branch['whatsapp_group'];
                        $data_table = "*Daftar Expired DO & Freetime " . $branch['description'] . " Per " . date("d F Y") . "*" . "\n\n" . "Empty container return";
                        if (!empty($whatsapp_group_internal)) {
                            $this->send_message($data_table, $whatsapp_group_internal);
                        }
                    }
                }
            } else {
                if ($branch['branch_type'] == BranchModel::BRANCH_TYPE_PLB) { //khusus PLB
                    $whatsapp_group_internal = $branch['whatsapp_group'];
                    $data_table = "*Daftar Expired DO & Freetime " . $branch['description'] . " Per " . date("d F Y") . "*" . "\n\n" . "No stock container available";
                    if (!empty($whatsapp_group_internal)) {
                        $this->send_message($data_table, $whatsapp_group_internal);
                    }
                }
            }
        }

        echo 'Freetime delivery order notification were sent...' . PHP_EOL;
    }

    /**
     * Generate daily inbound outbound upload progress.
     */
    public function generate_daily_inbound_outbound_progress()
    {
        $filters = [
            'data' => 'not_sppd', // belum sppd
            'date_type' => "uploads.created_at",
            'date_from' => "2019-08-25 00:00:00",
            'dashboard' => true
        ];
        $inboundProgress = $this->reportModel->getReportUploadProgressInbound($filters);
        $outboundProgress = $this->reportModel->getReportUploadProgressOutbound($filters);
        $documentProgress = array_merge($inboundProgress, $outboundProgress);

        $customers = array_unique(array_column($documentProgress, 'id_customer'));
        $customers = $this->peopleModel->getBy([
            'ref_people.id' => if_empty($customers, '-1')
        ]);

        foreach($customers AS $customer){
            $inboundProgressCustomers = array_filter($inboundProgress, function($item) use($customer) {
                return $item['id_customer'] == $customer['id'];
            });

            $outboundProgressCustomers = array_filter($outboundProgress, function($item2) use($customer) {
                return $item2['id_customer'] == $customer['id'];
            });

            $peopleBranchByIdCustomers = $this->peopleBranchModel->getPeopleBranchByCustomer($customer['id']);
            foreach ($peopleBranchByIdCustomers as $peopleBranchByIdCustomer) {
            	$inboundProgressCustomerPerBranches = array_filter($inboundProgressCustomers, function($customerBranch) use($peopleBranchByIdCustomer) {
	                return $customerBranch['id_branch'] == $peopleBranchByIdCustomer['id_branch'];
	            }); 
            	$no = 0;
	            $data_in = [];
	            foreach($inboundProgressCustomerPerBranches AS $inboundProgressCustomer){
	                $no = $no+1;
	                $status1 = is_null($inboundProgressCustomer['draft_date']) ? "Tunggu Draft" : (is_null($inboundProgressCustomer['confirmation_date']) ? "Tunggu Konfirmasi" : "Sudah Konfirmasi");
	                $status2 = is_null($inboundProgressCustomer['eta_date']) ? "ETA Belum Diisi" : (strtotime(date('Y-m-d')) < strtotime($inboundProgressCustomer['eta_date']) ? "Tunggu Kapal Sandar" : "Kapal Telah Sandar");
	                $status3 = is_null($inboundProgressCustomer['do_date']) ? "Tunggu DO" : "Sudah DO";
	                $status4 = is_null($inboundProgressCustomer['hardcopy_date']) ? "Tunggu Dok. Original" : "Sudah Dok. Original";

	                $invoice = is_null($inboundProgressCustomer['no_invoice']) ? "Tunggu Invoice" : $inboundProgressCustomer['no_invoice'];
	                $etadate = is_null($inboundProgressCustomer['eta_date']) ? "Tunggu ETA" : (date('d F Y', strtotime($inboundProgressCustomer['eta_date'])));
                    $party = is_null($inboundProgressCustomer['parties']) ? "-" : $inboundProgressCustomer['type_parties']." (".$inboundProgressCustomer['parties'].")";
	                $bl_numb = is_null($inboundProgressCustomer['no_bill_of_loading']) ? "Tunggu BL" : $inboundProgressCustomer['no_bill_of_loading'];
	                $sppb = $inboundProgressCustomer['sppb_date'];
	                $status4_hc = ($inboundProgressCustomer['branch_name'] != "PLB MEDAN" && $inboundProgressCustomer['branch_name'] != "PLB MEDAN 2" && $inboundProgressCustomer['branch_name'] != "PLB MEDAN-BELAWAN") ? "\n                    4.  ".$status4 : "";
	                $all_status = is_null($sppb) ? "1.  ".$status1."\n                    2.  ".$status2."\n                    3.  ".$status3.$status4_hc : "Tunggu SPPD";
	                $data_in[] = "*".$no.".* "."*Aju :* ".$inboundProgressCustomer['no_reference']."\n     *Invoice :* ".$invoice."\n     *ETA :* ".$etadate."\n     *Party :* ".$party."\n     *BL :* ".$bl_numb."\n     *Status :* ".$all_status;
	            }

	            $detail_in = ($no == 0) ? "No Data Available" : implode("\n\n", $data_in);
	            $data_in = "*Daftar Inbound Progress " . $customer['name'] . " Per " . date("d F Y") . "*" . "\n\n" . $detail_in;

	            $outboundProgressCustomerPerBranches = array_filter($outboundProgressCustomers, function($customerBranch2) use($peopleBranchByIdCustomer) {
	                return $customerBranch2['id_branch'] == $peopleBranchByIdCustomer['id_branch'];
	            }); 
	            $no = 0;
	            $data_out = [];
	            foreach($outboundProgressCustomerPerBranches AS $outboundProgressCustomer){
	                $no = $no+1;
	                $invoice = $outboundProgressCustomer['no_invoice'];
	                $draft_date = $outboundProgressCustomer['draft_date'];
	                $confirm_date = $outboundProgressCustomer['confirmation_date'];
	                $sppd_inbound = $outboundProgressCustomer['sppd_in_date'];
	                $billing = $outboundProgressCustomer['billing_date'];
	                $bpn = $outboundProgressCustomer['bpn_date'];
	                $sppb = $outboundProgressCustomer['sppb_date'];
	                $sppd = $outboundProgressCustomer['sppd_date'];

	                $status1 = is_null($draft_date) ? "Tunggu Draft" : "Sudah Draft";
	                $status2 = is_null($draft_date) ? "Tunggu Konfirmasi" : (is_null($confirm_date) ? "Tunggu Konfirmasi" : "Sudah Konfirmasi");
	                $status3 = is_null($sppd_inbound) ? "Tunggu SPPD Inbound" : "Sudah SPPD Inbound";
	                $status4 = is_null($draft_date) ? "Tunggu Billing" : (is_null($confirm_date) ? "Tunggu Billing" : (is_null($billing) ? "Tunggu Billing" : "Sudah Billing"));
	                $status5 = is_null($draft_date) ? "Tunggu Pembayaran" : (is_null($confirm_date) ? "Tunggu Pembayaran" : (is_null($billing) ? "Tunggu Pembayaran" : (is_null($bpn) ? "Tunggu Pembayaran" : "Sudah Pembayaran")));
	                $status6 = is_null($draft_date) ? "Tunggu SPPB" : (is_null($confirm_date) ? "Tunggu SPPB" : (is_null($billing) ? "Tunggu SPPB" : (is_null($bpn) ? "Tunggu SPPB" : (is_null($sppb) ? "Tunggu SPPB" : "Sudah SPPB"))));

	                if(!empty($sppb) && !is_null($sppb)){
	                    $all_status = is_null($sppd) ? "Tunggu SPPD" : "Sudah SPPD";
	                }else{
	                    $all_status = "1.  ".$status1."\n                     2.  ".$status2."\n                     3.  ".$status3."\n                     4.  ".$status4."\n                     5.  ".$status5."\n                     6.  ".$status6;
	                }
	                $data_out[] = "*".$no.".* "."*Invoice  :* ".$invoice."\n     "."*Aju Inbound  :* ".$outboundProgressCustomer['no_reference_inbound']."\n     "."*Aju Outbound :* ".$outboundProgressCustomer['no_reference']."\n     "."*Status  :* ".$all_status;
	            }
	            $detail_out = ($no == 0) ? "No Data Available" : implode("\n\n", $data_out);
	            $data_out = "*Daftar Outbound Progress " . $customer['name'] . " Per " . date("d F Y") . "*" . "\n\n" . $detail_out;
	            $data_in_out = $data_in."\n\n\n".$data_out;
	            $branch = $this->branchModel->getById($peopleBranchByIdCustomer['id_branch']);
      
	            if(!empty($inboundProgressCustomerPerBranches) || !empty($outboundProgressCustomerPerBranches)){
                    if(!is_null($peopleBranchByIdCustomer['whatsapp_group']) && !empty($peopleBranchByIdCustomer['whatsapp_group'])){
                        $this->send_message($data_in_out, $peopleBranchByIdCustomer['whatsapp_group']); //'6282334658122-1570688260' Testing Group
                        echo "Daily upload progress {$customer['name']} branch {$branch['branch']} notification were sent..." . PHP_EOL."<br/>";
                    }else{
                        echo "Daily upload progress {$customer['name']} branch {$branch['branch']} notification not sent to wa..." . PHP_EOL."<br/>";
                    }
                }
            }
        }
    }

    /**
     * Generate daily inbound outbound upload progress per branch.
     */
    public function generate_daily_inbound_outbound_progress_per_branch()
    {
        $filters = [
            'data' => 'not_sppd', // belum sppd
            'date_type' => "uploads.created_at",
            'date_from' => "2019-08-25 00:00:00",
            'dashboard' => true
        ];
        $inboundProgress = $this->reportModel->getReportUploadProgressInbound($filters);
        $outboundProgress = $this->reportModel->getReportUploadProgressOutbound($filters);
        $documentProgress = array_merge($inboundProgress, $outboundProgress);

        $branches = array_unique(array_column($documentProgress, 'id_branch'));
        $branches = $this->branchModel->getBy([
            'ref_branches.id' => if_empty($branches, '-1')
        ]);
        foreach($branches AS $branch){
            $inboundProgressBranches = array_filter($inboundProgress, function($item) use($branch) {
                return $item['id_branch'] == $branch['id'];
            });

            $inboundCustomers = array_unique(array_column($inboundProgressBranches, 'id_customer'));
            $inboundCustomers = $this->peopleModel->getBy([
                'ref_people.id' => if_empty($inboundCustomers, '-1')
            ]);

            $no_besar = 0;
            $data_inbound = [];
            foreach ($inboundCustomers as $inboundCustomer) {
                $inboundProgressCustomers = array_filter($inboundProgressBranches, function($item2) use($inboundCustomer) {
                    return $item2['id_customer'] == $inboundCustomer['id'];
                });
                $no_besar = $no_besar+1;
                $no_kecil = 0;
                $data_in = [];
                foreach($inboundProgressCustomers AS $inboundProgressCustomer){
                    $no_kecil = $no_kecil+1;
                    $status1 = is_null($inboundProgressCustomer['draft_date']) ? "Tunggu Draft" : (is_null($inboundProgressCustomer['confirmation_date']) ? "Tunggu Konfirmasi" : "Sudah Konfirmasi");
                    $status2 = is_null($inboundProgressCustomer['eta_date']) ? "Tunggu Kapal Sandar" : "Kapal Telah Sandar";
                    $status3 = is_null($inboundProgressCustomer['do_date']) ? "Tunggu DO" : "Sudah DO";
                    $status4 = is_null($inboundProgressCustomer['hardcopy_date']) ? "Tunggu Dok. Original" : "Sudah Dok. Original";

                    $invoice = is_null($inboundProgressCustomer['no_invoice']) ? "Tunggu Invoice" : $inboundProgressCustomer['no_invoice'];
                    $etadate = is_null($inboundProgressCustomer['eta_date']) ? "Tunggu ETA" : (date('d F Y H:i', strtotime($inboundProgressCustomer['eta_date'])));
                    $bl_numb = is_null($inboundProgressCustomer['no_bill_of_loading']) ? "Tunggu BL" : $inboundProgressCustomer['no_bill_of_loading'];
                    $sppb = $inboundProgressCustomer['sppb_date'];
                    $status4_hc = ($inboundProgressCustomer['branch_name'] != "PLB MEDAN" && $inboundProgressCustomer['branch_name'] != "PLB MEDAN 2" && $inboundProgressCustomer['branch_name'] != "PLB MEDAN-BELAWAN") ? "\n                    4.  ".$status4 : "";

                    $all_status = is_null($sppb) ? "1.  ".$status1."\n                    2.  ".$status2."\n                    3.  ".$status3.$status4_hc : "Tunggu SPPD";
                    $data_in[] = "*".$no_besar.".".$no_kecil."* "."*Aju :* ".$inboundProgressCustomer['no_reference']."\n     *Invoice :* ".$invoice."\n     *ETA :* ".$etadate."\n     *BL :* ".$bl_numb."\n     *Status :* ".$all_status;
                }
                $detail_in = ($no_kecil == 0) ? "No Data Available" : implode("\n\n", $data_in);
                $data_inbound[] = "*".$no_besar.".* "."*Progres Inbound " . $inboundCustomer['name']. "*" ."\n\n" . $detail_in;
            }

            $perBatch = 1;
            $totalBatch = ceil(count($data_inbound) / $perBatch);
            $mod = count($data_inbound) % $perBatch;
            for($i = 0; $i < $totalBatch; $i++) {
                $startIndex = $i * $perBatch;
                $endIndex = ($i * $perBatch) + $perBatch - 1;
                $totalTake = $perBatch;

                if($mod != 0 && $i == $totalBatch-1) {
                    $endIndex = ($i * $perBatch) + $mod - 1;
                    $totalTake = $mod;
                }
                $takenInbound = array_slice($data_inbound, $startIndex, $totalTake);
                $data_inbound_branch = ($no_besar == 0) ? "No Data Available" : implode("\n\n", $takenInbound);
                $all_inbound = "*Daftar Progres Dokumen Inbound " . $branch['description'] . " Per " . date("d F Y H:i") . "*" . "\n\n" . $data_inbound_branch;
                $whatsapp_group = get_setting('whatsapp_group_admin');
                if(!empty($whatsapp_group) && !is_null($whatsapp_group)){
                    $this->send_message($all_inbound, $whatsapp_group);
                    echo "Daily upload in progress notification were sent..." . PHP_EOL."<br/>";
                }else{
                    echo "Daily upload in progress notification not sent to wa..." . PHP_EOL."<br/>";
                }
            }

            //OUTBOUND
            $outboundProgressBranches = array_filter($outboundProgress, function($item3) use($branch) {
                return $item3['id_branch'] == $branch['id'];
            });

            $outboundCustomers = array_unique(array_column($outboundProgressBranches, 'id_customer'));
            $outboundCustomers = $this->peopleModel->getBy([
                'ref_people.id' => if_empty($outboundCustomers, '-1')
            ]);

            $no_besar_out = 0;
            $data_outbound = [];
            foreach ($outboundCustomers as $outboundCustomer) {
                $outboundProgressCustomers = array_filter($outboundProgressBranches, function($item4) use($outboundCustomer) {
                    return $item4['id_customer'] == $outboundCustomer['id'];
                });
                $no_besar_out = $no_besar_out+1;
                $no_kecil = 0;
                $data_out = [];
                foreach($outboundProgressCustomers AS $outboundProgressCustomer){
                    $no_kecil = $no_kecil+1;
                    $invoice = $outboundProgressCustomer['no_invoice'];
                    $draft_date = $outboundProgressCustomer['draft_date'];
                    $confirm_date = $outboundProgressCustomer['confirmation_date'];
                    $sppd_inbound = $outboundProgressCustomer['sppd_in_date'];
                    $billing = $outboundProgressCustomer['billing_date'];
                    $bpn = $outboundProgressCustomer['bpn_date'];
                    $sppb = $outboundProgressCustomer['sppb_date'];
                    $sppd = $outboundProgressCustomer['sppd_date'];

                    $status1 = is_null($draft_date) ? "Tunggu Draft" : "Sudah Draft";
                    $status2 = is_null($draft_date) ? "Tunggu Konfirmasi" : (is_null($confirm_date) ? "Tunggu Konfirmasi" : "Sudah Konfirmasi");
                    $status3 = is_null($draft_date) ? "Tunggu SPPD Inbound" : (is_null($confirm_date) ? "Tunggu SPPD Inbound" : (is_null($sppd_inbound) ? "Tunggu SPPD Inbound" : "Sudah SPPD Inbound"));
                    $status4 = is_null($draft_date) ? "Tunggu Billing" : (is_null($confirm_date) ? "Tunggu Billing" : (is_null($sppd_inbound) ? "Tunggu Billing" : (is_null($billing) ? "Tunggu Billing" : "Sudah Billing")));
                    $status5 = is_null($draft_date) ? "Tunggu Pembayaran" : (is_null($confirm_date) ? "Tunggu Pembayaran" : (is_null($sppd_inbound) ? "Tunggu Pembayaran" : (is_null($billing) ? "Tunggu Pembayaran" : (is_null($bpn) ? "Tunggu Pembayaran" : "Sudah Pembayaran"))));
                    $status6 = is_null($draft_date) ? "Tunggu SPPB" : (is_null($confirm_date) ? "Tunggu SPPB" : (is_null($sppd_inbound) ? "Tunggu SPPB" : (is_null($billing) ? "Tunggu SPPB" : (is_null($bpn) ? "Tunggu SPPB" : (is_null($sppb) ? "Tunggu SPPB" : "Sudah SPPB")))));

                    if(!empty($sppb) && !is_null($sppb)){
                        $all_status = is_null($sppd) ? "Tunggu SPPD" : "Sudah SPPD";
                    }else{
                        $all_status = "1.  ".$status1."\n                     2.  ".$status2."\n                     3.  ".$status3."\n                     4.  ".$status4."\n                     5.  ".$status5."\n                     6.  ".$status6;
                    }
                    $data_out[] = "*".$no_besar_out.".".$no_kecil."* "."*Invoice  :* ".$invoice."\n     "."*Aju Inbound  :* ".$outboundProgressCustomer['no_reference_inbound']."\n     "."*Aju Outbound :* ".$outboundProgressCustomer['no_reference']."\n     "."*Status  :* ".$all_status;
                }

                $detail_out = ($no_kecil == 0) ? "No Data Available" : implode("\n\n", $data_out);
                $data_outbound[] = "*".$no_besar_out.".* "."*Progres Outbound " . $outboundCustomer['name'] . "*" . "\n\n" . $detail_out;
            }
            $data_outbound_branch = ($no_besar_out == 0) ? "No Data Available" : implode("\n\n", $data_outbound);
            $all_outbound = "*Daftar Progres Dokumen Outbound " . $branch['description'] . " Per " . date("d F Y H:i") . "*" . "\n\n" . $data_outbound_branch;
            $all_in_out = $all_inbound."\n\n\n".$all_outbound;

            $perBatchOut = 1;
            $totalBatchOut = ceil(count($data_outbound) / $perBatchOut);
            $modOut = count($data_outbound) % $totalBatchOut;
            for($i = 0; $i < $totalBatchOut; $i++) {
                $startIndex = $i * $perBatchOut;
                $endIndex = ($i * $perBatchOut) + $perBatchOut - 1;
                $totalTake = $perBatchOut;

                if($modOut != 0 && $i == $totalBatchOut-1) {
                    $endIndex = ($i * $perBatchOut) + $modOut - 1;
                    $totalTake = $modOut;
                }
                $takenOutbound = array_slice($data_outbound, $startIndex, $totalTake);
                $data_outbound_branch = ($no_besar_out == 0) ? "No Data Available" : implode("\n\n", $takenOutbound);
                $all_outbound = "*Daftar Progres Dokumen Outbound " . $branch['description'] . " Per " . date("d F Y H:i") . "*" . "\n\n" . $data_outbound_branch;
                $whatsapp_group = get_setting('whatsapp_group_admin');
                if(!empty($whatsapp_group) && !is_null($whatsapp_group)){
                    $this->send_message($all_outbound, $whatsapp_group);
                    echo "Daily upload out progress notification were sent..." . PHP_EOL."<br/>";
                }else{
                    echo "Daily upload out progress notification not sent to wa..." . PHP_EOL."<br/>";
                }
            }
        }
    }

    /**
     * Generate daily realization document
     */
    public function generate_daily_realization_document()
    {
        $inboundFilters = [
            'date_type' => 'document_draft_by_created.min_created_at_draft',
            'date_from' => date('Y-m-d 00:00:00', strtotime('-1 days')),
            'date_to' => date('Y-m-d 23:59:59', strtotime('-1 days')),
            'category' => bookingTypeModel::CATEGORY_INBOUND,
        ];

        $number = 0;
        $dataInbound = [];
        $dataUploadByFilter = [];
        $inboundUploads = $this->upload->getRealizationDocument($inboundFilters);
        foreach ($inboundUploads as $key => $inboundUpload) {
            $number = $number+1;
            $inboundFilters['id_booking_type'] = $inboundUpload['id_booking_type'];
            $inboundFilters['id_upload'] = $inboundUpload['id'];
            $inboundUploadByFilters = $this->upload->getRealizationDocument($inboundFilters);

            $singleUpload = array_shift($inboundUploadByFilters);
            $customer = $this->peopleModel->getById($singleUpload['id_person']);

            $totalItems = numerical($singleUpload['total_item'], 3, true);
            $aju = substr($singleUpload['description'], -4);
            if(is_null($singleUpload['min_validated_at'])){
                $draft_time = "-";
                $pic = "Not yet validated";
            }else{
                $draft_time = round((strtotime($singleUpload['min_created_at_draft']) - strtotime($singleUpload['min_validated_at']))/3600, 3)." Jam";
                $pic = $singleUpload['validated_name'];
            }
            $dataInbound[] = "*".$number.".* "."*Aju  :* ".$aju."\n     "."*Nama Dokumen  :* ".$singleUpload['default_document']."\n     "."*Waktu Draft :* ".$draft_time."\n     "."*Total Item  :* ".$totalItems."\n     "."*PIC  :* ".$pic."\n     "."*Customer  :* ".$customer['name'];
        }
        $detailInbound = ($number == 0) ? "No Data Available" : implode("\n\n", $dataInbound);
        $allInbound = "*DATA INBOUND*"."\n\n".$detailInbound;

        $outboundFilters = [
            'date_type' => 'document_draft_by_created.min_created_at_draft',
            'date_from' => date('Y-m-d 00:00:00', strtotime('-1 days')),
            'date_to' => date('Y-m-d 23:59:59', strtotime('-1 days')),
            'category' => bookingTypeModel::CATEGORY_OUTBOUND,
        ];

        $number_out = 0;
        $dataOutbound = [];
        $dataUploadByFilter = [];
        $outboundUploads = $this->upload->getRealizationDocument($outboundFilters);
        foreach ($outboundUploads as $key => $outboundUpload) {
            $number_out = $number_out+1;
            $outboundFilters['id_booking_type'] = $outboundUpload['id_booking_type'];
            $outboundFilters['id_upload'] = $outboundUpload['id'];
            $outboundUploadByFilters = $this->upload->getRealizationDocument($outboundFilters);

            $singleUploadOut = array_shift($outboundUploadByFilters);
            $customerOutbound = $this->peopleModel->getById($singleUploadOut['id_person']);

            $totalItemOuts = numerical($singleUploadOut['total_item'], 3, true);
            $aju = substr($singleUploadOut['description'], -4);
            if(is_null($singleUploadOut['min_validated_at'])){
                $draft_time_out = "-";
                $pic = "Not yet validated";
            }else{
                $draft_time_out = round((strtotime($singleUploadOut['min_created_at_draft']) - strtotime($singleUploadOut['min_validated_at']))/3600, 3)." Jam";
                $pic = $singleUploadOut['validated_name'];
            }
            $dataOutbound[] = "*".$number_out.".* "."*Aju  :* ".$aju."\n     "."*Nama Dokumen  :* ".$singleUploadOut['default_document']."\n     "."*Waktu Draft :* ".$draft_time_out."\n     "."*Total Item  :* ".$totalItemOuts."\n     "."*PIC  :* ".$pic."\n     "."*Customer  :* ".$customerOutbound['name'];
        }
        $detailOutbound = ($number_out == 0) ? "No Data Available" : implode("\n\n", $dataOutbound);
        $allOutbound = "*DATA OUTBOUND*"."\n\n".$detailOutbound;
        $draftInboundOutbound = "*Daftar Dokumen Realisasi Draft Per " . date("d F Y", strtotime("-1 days")) . "*" . "\n\n" . $allInbound."\n\n\n".$allOutbound;

        $whatsapp_group = get_setting('whatsapp_group_admin');
        if(!empty($whatsapp_group) && !is_null($whatsapp_group)){
            $this->send_message($draftInboundOutbound, $whatsapp_group);
            echo "Daily document realization notification were sent..." . PHP_EOL."<br/>";
        }else{
            echo "Daily document realization notification not sent to wa..." . PHP_EOL."<br/>";
        }
    }

    /**
     * Send a message to a new or existing chat.
     * 6281333377368-1557128212@g.us
     */
    public function send_message($text, $whatsapp_group_internal)
    {
        $data = [
            'url' => 'sendMessage',
            'method' => 'POST',
            'payload' => [
                'chatId' => detect_chat_id($whatsapp_group_internal),
                'body' => $text,
            ]
        ];

        $result = $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);
    }

    public function report_compliance()
    {
        $date = date('Y-m-d', strtotime("-1 days"));
        $dateMinData = date('Y-m-d', strtotime("-7 days"));
        $dateAwal = $date . " 00:00:00";
        $dateAkhir = $date . " 23:59:59";
        $users = $this->user->getBy(['user_type' => 'INTERNAL']);

        $uploads = $this->upload->getAll(['branch' => null,'dashboard_status'=>1, 'date_from' => $dateMinData]);
        $data_reports = [];
        $total_uploads = 0;
        $total_handle = 0;
        foreach ($uploads as $upload) {
            $documents = $this->uploadDocument->getDocumentsByUpload($upload['id']);
            if (date($upload['created_at']) >= $dateAwal && date($upload['created_at']) <= $dateAkhir) {
                $total_uploads++;
            }
            foreach ($documents as $document) {
                if (date($document['created_at']) >= $dateAwal && date($document['created_at']) <= $dateAkhir) {
                    # code...
                    if ($upload['id_document_type'] == $document['id_document_type']) {

                        foreach ($users as $user) {
                            $user['count'] = 0;
                            $user['doc'] = [];
                            $user['count_draft'] = 0;
                            $user['doc_draft'] = [];
                            if ($document['created_by'] == $user['id']&&$document['created_at']!='') {
                                if (!isset($data_reports[$user['username']])) {
                                    $data_reports[$user['username']] = $user;
                                }
                                $data_reports[$user['username']]['count'] += 1;
                                array_unshift($data_reports[$user['username']]['doc'], $upload['id']);
                                //hitung upload yang di handle pada hari itu
                                if (date($upload['created_at']) >= $dateAwal && date($upload['created_at']) <= $dateAkhir) {
                                    $total_handle++;
                                }
                            }
                        }
                    }
                }
                if (date($document['created_at']) >= $dateAwal && date($document['created_at']) <= $dateAkhir) {
                    # code...
                    if ($upload['main_docs_name'].' Draft' == $document['document_type']) {
                        foreach ($users as $user) {
                            $user['count'] = 0;
                            $user['doc'] = [];
                            $user['count_draft'] = 0;
                            $user['doc_draft'] = [];
                            if ($document['created_by'] == $user['id']) {
                                if (!isset($data_reports[$user['username']])) {
                                    $data_reports[$user['username']] = $user;
                                }
                                $data_reports[$user['username']]['count_draft'] += 1;
                                array_unshift($data_reports[$user['username']]['doc_draft'], $upload['id']);
                            }
                        }
                    }
                }
            }
        }
        $count = array_column($data_reports, 'count');
        array_multisort($count, SORT_DESC, $data_reports);
        $tableData = $this->load->view('emails/partials/_report_compliance', [
            'date' => $date,
            'data_reports' => $data_reports,
            'total_uploads' => $total_uploads,
            'total_handle' => $total_handle,
        ], true);

        echo 'Sending Compliance report...' . PHP_EOL;

        $emailTo = ['direktur@transcon-indonesia.com','compliance_mgr@transcon-indonesia.com','comp_sbyadm2@transcon-indonesia.com'];//direktur@transcon-indonesia.com
        $emailBCC = $this->email_bcc;

        $emailTitle = "Report Compliance";
        $emailTemplate = 'emails/basic';
        $emailData = [
            'title' => 'Report Compliance',
            'name' => 'Transcon Management',
            'email' => get_setting('email_support'),
            'content' => $tableData,
        ];
        $emailOptions = [
            'bcc' => 'it@transcon-indonesia.com',
        ];

        $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);

        echo 'Compliance reports were sent...' . PHP_EOL;
    }

    /**
     * Generate daily lock.
     */
    public function lock_tally() {
        $date_now = date('Y-m-d 00:00:00');
        // $date_now = date('2019-09-30 00:00:00');
        $requests = $this->workOrderLockAutomate->getAllApprove();

        foreach ($requests as $request) {
            $where=[];
            if ($date_now<$request['date_from'] && $request['is_locked']==0 ||$date_now>$request['date_to'] && $request['is_locked']==0) {
                $where=[
                    'id_branch' => $request['id_branch'],
                    'id_work_order' => $request['id_work_order']
                ];
                $this->workOrder->setLocked([
                    'is_locked' => '1',
                    'locked_by' => $request['approve_by'],
                    'locked_at' => "'".$date_now."'"
                ],$where);
                $this->workOrderHistoryLock->create([
                    'id_work_order' => $request['id_work_order'],
                    'description' => 'Locked automate',
                    'status' => 'LOCKED',
                    'locked_by' => $request['approve_by'],
                    'locked_at' => $date_now,
                    'created_at' => $date_now,
                    'created_by' => $request['approve_by']
                ]);
            }
        }

    }
    /**
     * Generate daily unlock.
     */
    public function unlock_tally() {
        $date_now = date('Y-m-d 00:00:00');
        // $date_now = date('2019-09-25 00:00:00');
        $requests = $this->workOrderLockAutomate->getAllApprove();
    
        foreach ($requests as $request) {
            $where=[];
            if ($date_now>=$request['date_from'] && $date_now<=$request['date_to'] && $request['is_locked']==1) {
                $where=[
                    'id_branch' => $request['id_branch'],
                    'id_work_order' => $request['id_work_order']
                ];
                $this->workOrder->setLocked([
                    'is_locked' => '0',
                    'locked_by' => 'NULL',
                    'locked_at' => 'NULL'
                ],$where);
                $this->workOrderHistoryLock->create([
                    'id_work_order' => $request['id_work_order'],
                    'description' => 'Unlocked automate',
                    'status' => 'UNLOCKED',
                    'locked_by' => $request['approve_by'],
                    'locked_at' => $date_now,
                    'created_at' => $date_now,
                    'created_by' => $request['approve_by']
                ]);
             
            }
        }
    }

    /**
     * Reminder work order that not validated yet.
     */
    public function reminder_overtime_validation()
    {
        $branches = $this->branchModel->getBy(['branch_type' => BranchModel::BRANCH_TYPE_PLB]);
        foreach ($branches as $branch) {
            if (!empty($branch['whatsapp_group'])) {
                $workOrderOvertimes = $this->workOrderOvertimeCharge->getAll([
                    'branch' => $branch['id'],
                    'status' => 'PENDING'
                ]);
                if (!empty($workOrderOvertimes)) {
                    $message = "*JOB OVERTIME VALIDATION:*\n\n";
                    foreach ($workOrderOvertimes as $workOrderOvertime) {
                        $message .= "*" . $workOrderOvertime['no_work_order'] . "*\n";
                        $message .= $workOrderOvertime['customer_name'] . "\n";
                        $message .= $workOrderOvertime['no_reference'] . "\n";
                        $message .= '*Completed At:* ' . $workOrderOvertime['completed_at'] . "\n";
                        $message .= "*Overtime:* " . $workOrderOvertime['total_overtime'] . ' / ' . $workOrderOvertime['total_overtime_hour'] . " Hours(s)\n";
                        $message .= "*Branch:* " . $workOrderOvertime['branch'] . "\n\n";
                    }

                    $send = $this->notification->broadcast([
                        'url' => 'sendMessage',
                        'method' => 'POST',
                        'payload' => [
                            'chatId' => detect_chat_id($branch['whatsapp_group']),
                            'body' => $message,
                        ]
                    ], NotificationModel::TYPE_CHAT_PUSH);

                    // log the result
                    if (is_array($send) && !empty(get_if_exist($send, 'error'))) {
                        log_message('error', $send['error']);
                    } else {
                        echo 'Overtime pending notification branch ' . $branch['branch'] . ' with total ' . count($workOrderOvertimes) . ' is sent' . PHP_EOL;
                    }
                }
            }
        }
    }

    /**
     * Send delivery tracking in certain time everyday.
     * expected run every 09:00, 13:00 and 17:00
     */
    public function send_delivery_tracking()
    {
        $deliveryTrackings = $this->deliveryTracking->getBy([
            'delivery_trackings.status' => DeliveryTrackingModel::STATUS_ACTIVE
        ]);
        if (!empty($deliveryTrackings)) {
            foreach ($deliveryTrackings as $deliveryTracking) {
                $customer = $this->peopleModel->getById($deliveryTracking['id_customer']);
                $customerBranch = $this->peopleBranchModel->getBy([
                    'id_customer' => $customer['id'],
                    'id_branch' => $deliveryTracking['id_branch'],
                ], true);

                $deliveryTrackingDetails = $this->deliveryTrackingDetail->getBy([
                    'id_delivery_tracking' => $deliveryTracking['id']
                ]);

                if (!empty($deliveryTrackingDetails)) {
                    // filter only data that does not been sent yet
                    $lastTrackingDetails = array_filter($deliveryTrackingDetails, function ($detail) {
                        return !$detail['is_sent'];
                    });

                    // if all detail already sent, just pick VERY LATEST ONE data
                    if (empty($lastTrackingDetails)) {
                        $lastTrackingDetails = [end($deliveryTrackingDetails)];
                    }

                    // get goods of every tracking detail
                    foreach ($lastTrackingDetails as &$deliveryTrackingDetail) {
                        $deliveryTrackingDetail['goods'] = $this->deliveryTrackingGoods->getBy([
                            'delivery_tracking_goods.id_delivery_tracking_detail' => $deliveryTrackingDetail['id']
                        ]);
                    }

                    // loop through the delivery details
                    foreach ($lastTrackingDetails as $lastTrackingDetail) {
                        $data = [
                            'deliveryTracking' => $deliveryTracking,
                            'deliveryTrackingDetail' => $lastTrackingDetail,
                            'reports' => $this->deliveryTracking->getReportItem([
                                'delivery_tracking' => $deliveryTracking['id'],
                                'delivery_tracking_detail' => $lastTrackingDetail['id']
                            ])
                        ];
                        $page = $this->load->view('delivery_tracking/print_state', $data, true);
                        $pdf = $this->exporter->exportToPdf('Delivery ' . url_title($deliveryTracking['no_delivery_tracking']), $page, ['buffer' => true]);
                        $pdfFileName = url_title(if_empty($deliveryTracking['description'], 'delivery')) . '.pdf';
                        file_put_contents(FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . $pdfFileName, $pdf);

                        $sendPdf = $this->notification->broadcast([
                            'url' => 'sendFile',
                            'method' => 'POST',
                            'payload' => [
                                'chatId' => detect_chat_id($customerBranch['whatsapp_group']),
                                'body' => base_url('uploads/temp/' . $pdfFileName),
                                'filename' => $pdfFileName,
                                'caption' => $pdfFileName
                            ]
                        ], NotificationModel::TYPE_CHAT_PUSH);

                        // attachment or photo included
                        if (!empty($lastTrackingDetail['attachment'])) {
                            $fileSrc = base_url('uploads/' . $lastTrackingDetail['attachment']);
                            if (ENVIRONMENT == 'development') {
                                // generate random picture,
                                // alternate https://loremflickr.com/320/240
                                // or https://source.unsplash.com/random
                                $fileSrc = 'https://picsum.photos/400/300';
                            }
                            $this->notification->broadcast([
                                'url' => 'sendFile',
                                'method' => 'POST',
                                'payload' => [
                                    'chatId' => detect_chat_id($customerBranch['whatsapp_group']),
                                    'body' => $fileSrc,
                                    'filename' => basename($lastTrackingDetail['attachment']),
                                    'caption' => if_empty($deliveryTracking['description'], 'No description')
                                ]
                            ], NotificationModel::TYPE_CHAT_PUSH);
                        }

                        // log the result
                        if (is_array($sendPdf) && !empty(get_if_exist($sendPdf, 'error'))) {
                            log_message('error', $sendPdf['error']);
                        } else {
                            $this->deliveryTrackingDetail->update([
                                'is_sent' => true
                            ], $lastTrackingDetail['id']);
                            echo 'Delivery ' . $deliveryTracking['no_delivery_tracking'] . ' detail id ' . $lastTrackingDetail['id'] . ' is sent' . PHP_EOL;
                        }
                    }
                }

            }
        }
    }

    /**
     * Send delivery assignment in certain time everyday.
     * expected run every 08:00, 12:00 and 16:00
     */
    public function send_delivery_assignment()
    {
        $deliveryTrackings = $this->deliveryTracking->getBy([
            'delivery_trackings.status' => DeliveryTrackingModel::STATUS_ACTIVE
        ]);
        if (!empty($deliveryTrackings)) {
            foreach ($deliveryTrackings as $deliveryTracking) {
                $deliveryTrackingAssignments = $this->deliveryTrackingAssignment->getBy([
                    'id_delivery_tracking' => $deliveryTracking['id']
                ]);

                if (!empty($deliveryTrackingAssignments)) {
                    // filter only data that does not been sent yet
                    $lastTrackingMessages = array_filter($deliveryTrackingAssignments, function ($detail) {
                        return !$detail['is_sent'];
                    });

                    // if all detail already sent, just pick VERY LATEST ONE data
                    if (empty($lastTrackingMessages)) {
                        $lastTrackingMessages = [end($deliveryTrackingAssignments)];
                    }

                    // loop through the delivery message
                    foreach ($lastTrackingMessages as $lastTrackingMessage) {
                        $message = "*DELIVERY ASSIGNMENT:*\n";
                        $message .= $deliveryTracking['no_delivery_tracking'] . "\n";
                        $message .= $deliveryTracking['customer_name'] . "\n";
                        $message .= $deliveryTracking['employee_name'] . "\n\n";
                        $message .= "*ASSIGNMENT MESSAGE:*\n";
                        $message .= $lastTrackingMessage['assignment_message'];

                        // attachment or photo included
                        if (!empty($lastTrackingMessage['attachment'])) {
                            $fileSrc = base_url('uploads/' . $lastTrackingMessage['attachment']);
                            if (ENVIRONMENT == 'development') {
                                // generate random picture,
                                // alternate https://loremflickr.com/320/240
                                // or https://source.unsplash.com/random
                                $fileSrc = 'https://picsum.photos/400/300';
                            }
                            $send = $this->notification->broadcast([
                                'url' => 'sendFile',
                                'method' => 'POST',
                                'payload' => [
                                    'chatId' => detect_chat_id($deliveryTracking['reminder_type'] == 'EMPLOYEE' ? $deliveryTracking['contact_mobile'] : $deliveryTracking['contact_group']),
                                    'body' => $fileSrc,
                                    'filename' => basename($lastTrackingMessage['attachment']),
                                    'caption' => $message
                                ]
                            ], NotificationModel::TYPE_CHAT_PUSH);
                        } else { // attachment not set then sent the text only
                            $send = $this->notification->broadcast([
                                'url' => 'sendMessage',
                                'method' => 'POST',
                                'payload' => [
                                    'chatId' => detect_chat_id($deliveryTracking['reminder_type'] == 'EMPLOYEE' ? $deliveryTracking['contact_mobile'] : $deliveryTracking['contact_group']),
                                    'body' => $message,
                                ]
                            ], NotificationModel::TYPE_CHAT_PUSH);
                        }

                        // log the result
                        if (is_array($send) && !empty(get_if_exist($send, 'error'))) {
                            log_message('error', $send['error']);
                        } else {
                            $this->deliveryTrackingAssignment->update([
                                'is_sent' => true
                            ], $lastTrackingMessage['id']);
                            echo 'Delivery ' . $deliveryTracking['no_delivery_tracking'] . ' assignment message id ' . $lastTrackingMessage['id'] . ' is sent' . PHP_EOL;
                        }
                    }
                }

            }
        }
    }

    /**
     * Complain reminder
     */
    public function reminder_complain()
    {
        $complains = $this->complainModel->getAll();
        $thisday = strtoupper(date('l'));
        $serviceHour = $this->serviceHourModel->getBy(['ref_service_hours.service_day' => $thisday], true);
 
        //filter data complain yang sudah setting pic tapi belum ada tindak lanjut
        $dataComplains = array_filter($complains, function ($data) {
            if(!empty($data['department']) && empty($data['investigation_result'])){
                return $data;
            }
        });

        if(!empty($dataComplains)){
            $threehours = 60*60*3;
            $thirtyminutes = 60*30;
            $notif = [];
            foreach($dataComplains AS $complain){

                $chatId = get_setting('whatsapp_group_complain');
                $customer_data = $this->people->getById($complain['id_customer']);
                $complain_category = $this->complainCategoryModel->getById($complain['id_complain_category']);
                $chatMessage = "*Reminder to Respond The Complaint ". $complain['no_complain'] ."*\n\n";
                $chatMessage .= "Recognizing your very busy schedule, I am sending you this note as a reminder to check and respond the customer complaint immediately.\n\n";
                $chatMessage = $chatMessage."Code       : *" . $complain['no_complain'] . "*\n";
                $chatMessage = $chatMessage."Category   : *" . $complain_category['category'] . " (" .$complain_category['value_type'].")". "*\n";
                $chatMessage = $chatMessage."Customer   : *" . $customer_data['name'] . "*\n";
                $chatMessage = $chatMessage."Department : *" . $complain['department'] . "*\n";
                $chatMessage = $chatMessage."Date       : *" . format_date($complain['complain_date'],"H:i:s d F Y") . "*\n";
                $chatMessage = $chatMessage."Complaint  : *" . $complain['complain'] . "*\n\n";
                $department = $this->departmentModel->getBy(['department' =>  $complain['department']], true);

                //tgl setting pic = tanggal update jika ada/tanggal created
                $uploadtime = $complain['pic_date'];
                //format tanggal untuk setting pic
                $settingtime = date('Y-m-d h:i A', strtotime($uploadtime));
                //Start operasional hari ini
                $starttime = date('Y-m-d h:i A', strtotime(date('Y-m-d')." ".$serviceHour['service_time_start'])); 
                //End operasional hari ini
                $endtime = date('Y-m-d h:i A', strtotime(date('Y-m-d')." ".$serviceHour['service_time_end'])); 
                $maxtime = date('Y-m-d h:i A', strtotime($settingtime)+$threehours); // 3 jam setelah setting PIC

                //conten email
                $contentEmail = "Recognizing your very busy schedule, I am sending you this note as a reminder to check and respond the complaint <b>" .  $complain['no_complain']. " was due on date " . date('d F Y h:i A', strtotime($maxtime)) . ".</b>"." Thank you.";
                $contentEmail = $contentEmail."<table>";
                $contentEmail = $contentEmail.'<tr>
                                        <td style="width:150px">Code</td> 
                                        <td style="width:5px">:</td>
                                        <td><b>' . $complain['no_complain'] . '</b></td>
                                    </tr>';
                $contentEmail = $contentEmail.'<tr>
                                        <td style="width:150px">Category</td> 
                                        <td style="width:5px">:</td>
                                        <td><b>' . $complain_category['category'] . ' (' .$complain_category['value_type'].')'. '</b></td>
                                    </tr>';
                $contentEmail = $contentEmail.'<tr>
                                        <td style="width:150px">Customer</td> 
                                        <td style="width:5px">:</td>
                                        <td><b>' . $customer_data['name'] . '</b></td>
                                    </tr>';
                $contentEmail = $contentEmail.'<tr>
                                        <td style="width:150px">Department</td> 
                                        <td style="width:5px">:</td>
                                        <td><b>' . $complain['department'] . '</b></td>
                                    </tr>';
                $contentEmail = $contentEmail.'<tr>
                                        <td style="width:150px">Date</td> 
                                        <td style="width:5px">:</td>
                                        <td><b>' . format_date($complain['complain_date'],"H:i:s d F Y") . '</b></td>
                                    </tr>';
                $contentEmail = $contentEmail.'<tr>
                                        <td style="width:150px">Complaint</td> 
                                        <td style="width:5px">:</td>
                                        <td><b>' . $complain['complain'] . '</b></td>
                                    </tr>
                                    </table></br>';

                //content2
                //conten email
                $contentEmail2 = "This letter is to inform you that the customer complain " . $complain['no_complain'] . " is past due since " . date('d F Y h:i A', strtotime($maxtime)) ."</br></br>";
                $contentEmail2 = $contentEmail2."<table>";
                $contentEmail2 = $contentEmail2.'<tr>
                                        <td style="width:150px">Code</td> 
                                        <td style="width:5px">:</td>
                                        <td><b>' . $complain['no_complain'] . '</b></td>
                                    </tr>';
                $contentEmail2 = $contentEmail2.'<tr>
                                        <td style="width:150px">Category</td> 
                                        <td style="width:5px">:</td>
                                        <td><b>' . $complain_category['category'] . ' (' .$complain_category['value_type'].')'. '</b></td>
                                    </tr>';
                $contentEmail2 = $contentEmail2.'<tr>
                                        <td style="width:150px">Customer</td> 
                                        <td style="width:5px">:</td>
                                        <td><b>' . $customer_data['name'] . '</b></td>
                                    </tr>';
                $contentEmail2 = $contentEmail2.'<tr>
                                        <td style="width:150px">Department</td> 
                                        <td style="width:5px">:</td>
                                        <td><b>' . $complain['department'] . '</b></td>
                                    </tr>';
                $contentEmail2 = $contentEmail2.'<tr>
                                        <td style="width:150px">Date</td> 
                                        <td style="width:5px">:</td>
                                        <td><b>' . format_date($complain['complain_date'],"H:i:s d F Y") . '</b></td>
                                    </tr>';
                $contentEmail2 = $contentEmail2.'<tr>
                                        <td style="width:150px">Complaint</td> 
                                        <td style="width:5px">:</td>
                                        <td><b>' . $complain['complain'] . '</b></td>
                                    </tr>
                                    </table></br>';
                $contentEmail2 .= "We kindly request you to check and respond immediately. Thank you.";
                // Jika tanggal setting pic lebih besar dari tanggal start dan lebih kecil dari tanggal akhir (hari ini) 
                if( (strtotime($settingtime) > strtotime($starttime)) && (strtotime($settingtime) < strtotime($endtime)) ){
                    $maxtime = date('Y-m-d h:i A', strtotime($settingtime)+$threehours); // 3 jam setelah setting PIC
                     
                    //jika tgl server lbh besar dari tgl setting dan lebih kecil dari tanggal maximal (hari ini)
                    if( (strtotime(date('Y-m-d h:i A')) >= strtotime($settingtime)) && 
                        (strtotime(date('Y-m-d h:i A')) <= strtotime($maxtime)) ){
                        
                        //reminder past due (whatsapp group)     
                        $send = $this->notification->broadcast([
                            'url' => 'sendMessage',
                            'method' => 'POST',
                            'payload' => [
                                'chatId' => detect_chat_id($chatId),
                                'body' => $chatMessage,
                            ]
                        ], NotificationModel::TYPE_CHAT_PUSH);

                        if (is_array($send) && !empty(get_if_exist($send, 'error'))) {
                            log_message('error', $send['error']);
                        } else {
                            echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is sent to wa..." . PHP_EOL;
                        }

                        //email message
                        $emailTo = $department['email_pic'];
                        $emailTitle = "Reminder to Respond The Complaint ". $complain['no_complain'];
                        $emailTemplate = "emails/basic";
                        $emailData = [
                            'title' => 'Reminder',
                            'name' => 'User',
                            'email' => $emailTo,
                            'content' => $contentEmail,

                        ];
                        
                        $sendEmail = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);

                        if (!$sendEmail) {
                            echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is not sent to email..." . PHP_EOL;
                        } else {
                            echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is sent to email..." . PHP_EOL;
                        }

                    }else{

                        //Jika waktu server lebih dari batas maximal untuk respon komplen (masih diantara waktu start - end)
                        if(strtotime(date('Y-m-d h:i A')) > strtotime($maxtime)) {

                            //notif email for check and respond complaint to PIC N Direktur (Only One)
                            if(!$complain['is_sent']){

                                $emailTo = $department['email_pic'];
                                $emailTitle = "Complaint Notification ". $complain['no_complain'];
                                $emailTemplate = "emails/basic";
                                $emailData = [
                                    'title' => 'Complaint Notification',
                                    'name' => 'User',
                                    'email' => $emailTo,
                                    'content' => $contentEmail

                                ];

                                $emailOptions = [
                                    'cc' => 'direktur@transcon-indonesia.com',
                                ];

                                $sendNotif = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions); 
                                if($sendNotif){
                                    $this->complainModel->update([
                                        'is_sent' => 1,
                                    ], $complain['id']);
                                    echo "Notification to respond the customer complaint ". $complain['no_complain'] ." is sent to email..." . PHP_EOL;
                                }else{
                                    echo "Notification to respond the customer complaint ". $complain['no_complain'] ." is not sent to email..." . PHP_EOL;
                                }
                            }

                            if($complain['is_sent']){
                                //reminder past due (whatsapp group) 
                                $chatId = get_setting('whatsapp_group_complain');
                                $chatMessage = "*Reminder to Respond The Complaint ". $complain['no_complain'] ."*\n\n";
                                // $chatMessage .=  "This message is to inform you that the customer complain " . $complain['no_complain'] . " is past due since " . date('d F Y h:i A', strtotime($maxtime)) . ". We kindly request you to check and respond immediately. Thank you.";

                                //content wa
                                $chatMessage = $chatMessage. "This message is to inform you that the customer complain " . $complain['no_complain'] . " is past due since " . date('d F Y h:i A', strtotime($maxtime)) . "\n\n";
                                $chatMessage = $chatMessage."Code       : *" . $complain['no_complain'] . "*\n";
                                $chatMessage = $chatMessage."Category   : *" . $complain_category['category'] . " (" .$complain_category['value_type'].")". "*\n";
                                $chatMessage = $chatMessage."Customer   : *" . $customer_data['name'] . "*\n";
                                $chatMessage = $chatMessage."Department : *" . $complain['department'] . "*\n";
                                $chatMessage = $chatMessage."Date       : *" . format_date($complain['complain_date'],"H:i:s d F Y") . "*\n";
                                $chatMessage = $chatMessage."Complaint  : *" . $complain['complain'] . "*\n\n";
                                $chatMessage = $chatMessage."We kindly request you to check and respond immediately. Thank you.";

                                $send = $this->notification->broadcast([
                                    'url' => 'sendMessage',
                                    'method' => 'POST',
                                    'payload' => [
                                        'chatId' => detect_chat_id($chatId),
                                        'body' => $chatMessage,
                                    ]
                                ], NotificationModel::TYPE_CHAT_PUSH);

                                if (is_array($send) && !empty(get_if_exist($send, 'error'))) {
                                    log_message('error', $send['error']);
                                } else {
                                    echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is sent to wa..." . PHP_EOL;
                                }

                                //  email message
                                $emailTo = $department['email_pic'];
                                $emailTitle = "Reminder to Respond The Complaint ". $complain['no_complain'];
                                $emailTemplate = "emails/basic";
                                
                                $emailData = [
                                    'title' => 'Reminder',
                                    'name' => 'User',
                                    'email' => $emailTo,
                                    'content' => $contentEmail2,
                                ];

                                $emailOptions = [
                                    'cc' => 'direktur@transcon-indonesia.com',
                                ];

                                $sendEmail = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);

                                if (!$sendEmail) {
                                    echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is not sent to email..." . PHP_EOL;
                                } else {
                                    echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is sent to email..." . PHP_EOL;
                                }
                            }
                        }
                    }
                }else{ //jika tanggal setting tidak berada diantara tanggal start - end
              
                    //jika tgl setting pic sm dg tgl server (hari ini)
                    if( date('Y-m-d') == date('Y-m-d', strtotime($settingtime))){ 
                        // 3 jam setelah tgl start (hari ini juga)
                        $maxtime = date('Y-m-d h:i A', strtotime($starttime)+$threehours); 

                        //jika tgl server lbh besar dari tgl start dan lebih kecil dari tgl maximal dan
                        // tanggal setting lbh kcil dari tgl start (hari ini)
                        if( (strtotime(date('Y-m-d h:i A')) > strtotime($starttime)) && 
                           (strtotime(date('Y-m-d h:i A')) < strtotime($maxtime)) && 
                           (strtotime($settingtime) < strtotime($starttime)) ){
                            
                            //reminder past due (whatsapp group) 
                            $send = $this->notification->broadcast([
                                'url' => 'sendMessage',
                                'method' => 'POST',
                                'payload' => [
                                    'chatId' => detect_chat_id($chatId),
                                    'body' => $chatMessage,
                                ]
                            ], NotificationModel::TYPE_CHAT_PUSH);

                            if (is_array($send) && !empty(get_if_exist($send, 'error'))) {
                                log_message('error', $send['error']);
                            } else {
                                echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is sent to wa..." . PHP_EOL;
                            }

                             //email message
                            $emailTo = $department['email_pic'];
                            $emailTitle = "Reminder to Respond The Complaint ". $complain['no_complain'];
                            $emailTemplate = "emails/basic";
                            
                            $emailData = [
                                'title' => 'Reminder',
                                'name' => 'User',
                                'email' => $emailTo,
                                'content' => $contentEmail,

                            ];
                            $sendEmail =$this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);

                            if (!$sendEmail) {
                                echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is not sent to email..." . PHP_EOL;
                            } else {
                                echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is sent to email..." . PHP_EOL;
                            }
                        }else{

                            if( strtotime(date('Y-m-d h:i A')) > strtotime($maxtime) && 
                                (strtotime($settingtime) < strtotime($starttime)) ) {

                                if(!$complain['is_sent']){

                                    $emailTo = $department['email_pic'];
                                    $emailTitle = "Complaint Notification ". $complain['no_complain'];
                                    $emailTemplate = "emails/basic";
                                    $emailData = [
                                        'title' => 'Complaint Notification',
                                        'name' => 'User',
                                        'email' => $emailTo,
                                        'content' => $contentEmail,

                                    ];

                                    $emailOptions = [
                                        'cc' => 'direktur@transcon-indonesia.com',
                                    ];

                                    $sendNotif =$this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions); 

                                    if($sendNotif){
                                        $this->complainModel->update([
                                            'is_sent' => 1,
                                        ], $complain['id']);

                                        echo "Notification to respond the customer complaint ". $complain['no_complain'] ." is sent to email..." . PHP_EOL;
                                    }else{
                                        echo "Notification to respond the customer complaint ". $complain['no_complain'] ." is not sent to email..." . PHP_EOL;
                                    }
                                }

                                if($complain['is_sent']){
                                      //reminder past due (whatsapp group) 
                                    $chatId = get_setting('whatsapp_group_complain');
                                    $chatMessage = "*Reminder to Respond The Complaint ". $complain['no_complain'] ."*\n\n";
                                    //content wa
                                    $chatMessage = $chatMessage. "This message is to inform you that the customer complain " . $complain['no_complain'] . " is past due since " . date('d F Y h:i A', strtotime($maxtime)) . "\n\n";
                                    $chatMessage = $chatMessage."Code       : *" . $complain['no_complain'] . "*\n";
                                    $chatMessage = $chatMessage."Category   : *" . $complain_category['category'] . " (" .$complain_category['value_type'].")". "*\n";
                                    $chatMessage = $chatMessage."Customer   : *" . $customer_data['name'] . "*\n";
                                    $chatMessage = $chatMessage."Department : *" . $complain['department'] . "*\n";
                                    $chatMessage = $chatMessage."Date       : *" . format_date($complain['complain_date'],"H:i:s d F Y") . "*\n";
                                    $chatMessage = $chatMessage."Complaint  : *" . $complain['complain'] . "*\n\n";
                                    $chatMessage = $chatMessage."We kindly request you to check and respond immediately. Thank you.";

                                    $send = $this->notification->broadcast([
                                        'url' => 'sendMessage',
                                        'method' => 'POST',
                                        'payload' => [
                                            'chatId' => detect_chat_id($chatId),
                                            'body' => $chatMessage,
                                        ]
                                    ], NotificationModel::TYPE_CHAT_PUSH);
                                    
                                    if (is_array($send) && !empty(get_if_exist($send, 'error'))) {
                                        log_message('error', $send['error']);
                                    } else {
                                        echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is sent to wa..." . PHP_EOL;
                                    }

                                    //email message
                                    $emailTo = $department['email_pic'];
                                    $emailTitle = "Reminder to Respond The Complaint ". $complain['no_complain'];
                                    $emailTemplate = "emails/basic";
                                    $emailData = [
                                        'title' => 'Reminder',
                                        'name' => 'User',
                                        'email' => $emailTo,
                                        'content' => $contentEmail2,

                                    ];

                                    $emailOptions = [
                                        'cc' => 'direktur@transcon-indonesia.com',
                                    ];
                                    $sendEmail = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);

                                    if (!$sendEmail) {
                                        echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is not sent to email..." . PHP_EOL;
                                    } else {
                                        echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is sent to email..." . PHP_EOL;
                                    }
                                }
                            }
                        }

                    }else{ // jika tgl setting pic bukan hari ini

                        //satu hari setelah tanggal setting pic
                        $onedayafterupdate = date('Y-m-d', strtotime($settingtime. ' + 1 days'));
                        // tanggal start dari satu hari setelah tanggal setting pic
                        $starttime = date('Y-m-d h:i A', strtotime($onedayafterupdate." ".$serviceHour['service_time_start']));
                        // tanggal batas akhir dari satu hari setelah tanggal setting pic
                        $maxtime = date('Y-m-d h:i A', strtotime($starttime)+$threehours); 

                        // jika tgl server sm dengan tgl start dan
                        // tgl server lebih besar dari tgl start (tgl start dimulai  satu hari setelah tgl setting pic) dan
                        // lebih kecil dari tgl batas akhir (dimulai satu hari setelah tanggal setting pic tambah 3 jam)
                        if( date('Y-m-d') == date('Y-m-d', strtotime($starttime)) ){ // tgl server == tgl start
                            if( (strtotime(date('Y-m-d h:i A')) > strtotime($starttime)) && 
                               (strtotime(date('Y-m-d h:i A')) < strtotime($maxtime)) ){

                                //reminder past due (whatsapp group) 
                                $send = $this->notification->broadcast([
                                    'url' => 'sendMessage',
                                    'method' => 'POST',
                                    'payload' => [
                                        'chatId' => detect_chat_id($chatId),
                                        'body' => $chatMessage,
                                    ]
                                ], NotificationModel::TYPE_CHAT_PUSH);

                                 if (is_array($send) && !empty(get_if_exist($send, 'error'))) {
                                    log_message('error', $send['error']);
                                } else {
                                    echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is sent to wa..." . PHP_EOL;
                                }

                                 //email message
                                $emailTo = $department['email_pic'];
                                $emailTitle = "Reminder to Respond The Complaint ". $complain['no_complain'];
                                $emailTemplate = "emails/basic";
                                $emailData = [
                                    'title' => 'Reminder',
                                    'name' => 'User',
                                    'email' => $emailTo,
                                    'content' => $contentEmail,

                                ];
                                $sendEmail = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData);

                                if (!$sendEmail) {
                                    echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is not sent to email..." . PHP_EOL;
                                } else {
                                    echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is sent to email..." . PHP_EOL;
                                }
                            }else{

                                if(strtotime(date('Y-m-d h:i A')) > strtotime($maxtime)) {

                                    //notif email for check and respond complaint to PIC N Direktur (Only One)
                                    if(!$complain['is_sent']){

                                        $emailTo = $department['email_pic'];
                                        $emailTitle = "Complaint Notification ". $complain['no_complain'];
                                        $emailTemplate = "emails/basic";
                                        $emailData = [
                                            'title' => 'Complaint Notification',
                                            'name' => 'User',
                                            'email' => $emailTo,
                                            'content' => $contentEmail

                                        ];

                                        $emailOptions = [
                                            'cc' => 'direktur@transcon-indonesia.com',
                                        ];

                                        $sendNotif =$this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions); 

                                        if($sendNotif){
                                            $this->complainModel->update([
                                                'is_sent' => 1,
                                            ], $complain['id']);

                                            echo "Notification to respond the customer complaint ". $complain['no_complain'] ." is sent to email..." . PHP_EOL;
                                        }else{
                                            echo "Notification to respond the customer complaint ". $complain['no_complain'] ." is not sent to email..." . PHP_EOL;
                                        }
                                    }

                                    if($complain['is_sent']){
                                        //reminder past due (whatsapp group) 
                                        $chatId = get_setting('whatsapp_group_complain');
                                        $chatMessage = "*Reminder to Respond The Complaint ". $complain['no_complain'] ."*\n\n";
                                        // $chatMessage .=  "This message is to inform you that the customer complain " . $complain['no_complain'] . " is past due since " . date('d F Y h:i A', strtotime($maxtime)) . ". We kindly request you to check and respond immediately. Thank you.";
                                        //content wa
                                        $chatMessage = $chatMessage. "This message is to inform you that the customer complain " . $complain['no_complain'] . " is past due since " . date('d F Y h:i A', strtotime($maxtime)) . "\n\n";
                                        $chatMessage = $chatMessage."Code       : *" . $complain['no_complain'] . "*\n";
                                        $chatMessage = $chatMessage."Category   : *" . $complain_category['category'] . " (" .$complain_category['value_type'].")". "*\n";
                                        $chatMessage = $chatMessage."Customer   : *" . $customer_data['name'] . "*\n";
                                        $chatMessage = $chatMessage."Department : *" . $complain['department'] . "*\n";
                                        $chatMessage = $chatMessage."Date       : *" . format_date($complain['complain_date'],"H:i:s d F Y") . "*\n";
                                        $chatMessage = $chatMessage."Complaint  : *" . $complain['complain'] . "*\n\n";
                                        $chatMessage = $chatMessage."We kindly request you to check and respond immediately. Thank you.";

                                        $send = $this->notification->broadcast([
                                            'url' => 'sendMessage',
                                            'method' => 'POST',
                                            'payload' => [
                                                'chatId' => detect_chat_id($chatId),
                                                'body' => $chatMessage,
                                            ]
                                        ], NotificationModel::TYPE_CHAT_PUSH);

                                         if (is_array($send) && !empty(get_if_exist($send, 'error'))) {
                                            log_message('error', $send['error']);
                                        } else {
                                            echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is sent to wa..." . PHP_EOL;
                                        }

                                        //email message
                                        $emailTo = $department['email_pic'];
                                        $emailTitle = "Reminder to Respond The Complaint ". $complain['no_complain'];
                                        $emailTemplate = "emails/basic";
                                        $emailData = [
                                            'title' => 'Reminder',
                                            'name' => 'User',
                                            'email' => $emailTo,
                                            'content' => $contentEmail2,

                                        ];

                                        $emailOptions = [
                                            'cc' => 'direktur@transcon-indonesia.com',
                                        ];
                                        $sendEmail = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);

                                        if (!$sendEmail) {
                                            echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is not sent to email..." . PHP_EOL;
                                        } else {
                                            echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is sent to email..." . PHP_EOL;
                                        }
                                    }
                                }
                            }
                        }else{
                            if(strtotime(date('Y-m-d h:i A')) > strtotime($maxtime)) {

                                //notif email for check and respond complaint to PIC N Direktur (Only One)
                                if(!$complain['is_sent']){

                                    $emailTo = $department['email_pic'];
                                    $emailTitle = "Complaint Notification ". $complain['no_complain'];
                                    $emailTemplate = "emails/basic";
                                    $emailData = [
                                        'title' => 'Complaint Notification',
                                        'name' => 'User',
                                        'email' => $emailTo,
                                        'content' => $contentEmail

                                    ];

                                    $emailOptions = [
                                        'cc' => 'direktur@transcon-indonesia.com',
                                    ];

                                    $sendNotif = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions); 
                                    if($sendNotif){
                                        $this->complainModel->update([
                                            'is_sent' => 1,
                                        ], $complain['id']);
                                        echo "Notification to respond the customer complaint ". $complain['no_complain'] ." is sent to email..." . PHP_EOL;
                                    }else{
                                        echo "Notification to respond the customer complaint ". $complain['no_complain'] ." is not sent to email..." . PHP_EOL;
                                    }
                                }

                                if($complain['is_sent']){

                                    //reminder past due (whatsapp group) 
                                    $chatId = get_setting('whatsapp_group_complain');
                                    $chatMessage = "*Reminder to Respond The Complaint ". $complain['no_complain'] ."*\n\n";
                                    // $chatMessage .=  "This message is to inform you that the customer complain " . $complain['no_complain'] . " is past due since " . date('d F Y h:i A', strtotime($maxtime)) . ". We kindly request you to check and respond immediately. Thank you.";
                                    //content wa
                                    $chatMessage = $chatMessage. "This message is to inform you that the customer complain " . $complain['no_complain'] . " is past due since " . date('d F Y h:i A', strtotime($maxtime)) . "\n\n";
                                    $chatMessage = $chatMessage."Code       : *" . $complain['no_complain'] . "*\n";
                                    $chatMessage = $chatMessage."Category   : *" . $complain_category['category'] . " (" .$complain_category['value_type'].")". "*\n";
                                    $chatMessage = $chatMessage."Customer   : *" . $customer_data['name'] . "*\n";
                                    $chatMessage = $chatMessage."Department : *" . $complain['department'] . "*\n";
                                    $chatMessage = $chatMessage."Date       : *" . format_date($complain['complain_date'],"H:i:s d F Y") . "*\n";
                                    $chatMessage = $chatMessage."Complaint  : *" . $complain['complain'] . "*\n\n";
                                    $chatMessage = $chatMessage."We kindly request you to check and respond immediately. Thank you.";

                                    $send = $this->notification->broadcast([
                                        'url' => 'sendMessage',
                                        'method' => 'POST',
                                        'payload' => [
                                            'chatId' => detect_chat_id($chatId),
                                            'body' => $chatMessage,
                                        ]
                                    ], NotificationModel::TYPE_CHAT_PUSH);

                                    if (is_array($send) && !empty(get_if_exist($send, 'error'))) {
                                        log_message('error', $send['error']);
                                    } else {
                                        echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is sent to wa..." . PHP_EOL;
                                    }

                                    //reminder past due (email message)
                                    $emailTo = $department['email_pic'];
                                    $emailTitle = "Reminder to Respond The Complaint ". $complain['no_complain'];
                                    $emailTemplate = "emails/basic";
                                    $emailData = [
                                        'title' => 'Reminder',
                                        'name' => 'User',
                                        'email' => $emailTo,
                                        'content' => $contentEmail2,

                                    ];

                                    $emailOptions = [
                                        'cc' => 'direktur@transcon-indonesia.com',
                                    ];
                                    $sendEmail = $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);

                                    if (!$sendEmail) {
                                        echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is not sent to email..." . PHP_EOL;
                                    } else {
                                        echo "Reminder to respond the customer complaint ". $complain['no_complain'] ." is sent to email..." . PHP_EOL;
                                    }
                                }
                            }
                        }
                        
                    }
                }
            }

        }else{
            echo 'No data, reminder to respond the customer complaint were not sent to whatsapp group complaint...' . PHP_EOL;
        }
    }

    public function autoGenerateOpnameSpace()
    {
        $branches = $this->branchModel->getAll();
        $request_date = date('Y-m-d');
        foreach($branches AS $branch){
            $branchId = $branch['id'];
            $checkOpnameSpace = $this->OpnameSpace->opnameSpaceCheck($branchId,$request_date);
            if(!empty($checkOpnameSpace)){
                echo "opname-space stocks failed, opname-space is already exists!";
                continue;
            }
            if(!is_null($branchId) && (!is_null($request_date))){
                $description = 'Automate Generate';
                $lastOpnameSpace = $this->OpnameSpace->getLastDataByBranch($branchId);
    
                // $filterContainer['data'] = 'stock';
                // $reportContainers = $this->reportStock->getStockContainers($filterContainer);
                // $id_booking_containers = array_unique(array_column($reportContainers, 'id_booking'));
                $filterGoods = [];
                $filterGoods['data'] = 'stock';
                $reportGoods = $this->reportStock->getStockGoods($filterGoods);
                $id_booking_goods = array_unique(array_column($reportGoods, 'id_booking'));
                $opnameSpace = $this->opnameSpaceBookingIn->getReportOpnameSpace([
                    'booking' => $id_booking_goods,
                ]);
                // print_debug($opnameSpace);
                $cekMinus = [];
                $cekMinusResult = [];
                $dataCapacities = $this->workOrder->getOverCapacity(['sum_space_minus'=>'1']);
                // print_debug($id_booking_goods);
                foreach($dataCapacities as $dataCapacity){
                    if(in_array($dataCapacity['id_booking'], $id_booking_goods)){
                        $cekMinus[] = $dataCapacity;
                    }
                }
                foreach ($cekMinus as &$result) {
                    $filterGoods['booking'] = $result['id_booking'];
                    $result['sum_space_cal'] = '';
                    foreach($opnameSpace as $space){
                        if($result['id_booking'] == $space['id_booking']){
                            $result['sum_space_cal'] = $result['sum_space']+$space['space_diff'];
                        }
                    }
                    if ($result['sum_space']<=0) {
                        if(!empty($result['sum_space_cal'])){
                            if($result['sum_space_cal']<=0){
                                $cekMinusResult[] = $result['id_booking'];
                            }
                        }else{
                            $cekMinusResult[] = $result['id_booking'];
                        }
                    }
                }
                $overCapacities = $this->workOrder->getDataOpnameSpace([
                    'branch' => $branchId,
                    'completed' => isset($lastOpnameSpace['opname_space_date'])?$lastOpnameSpace['opname_space_date']:null,
                    'or_booking' => $cekMinusResult,
                ]);
                $resultFilters = [];
                $totalSpace = [];
                $lastUpdated = "2000-06-05 13:18:17";
                //filter untuk yg ada stock aja
                foreach($overCapacities as $overCapacity){
                    if(in_array($overCapacity['id_booking'], $id_booking_goods) ){
                        $resultFilters[] = $overCapacity;
                    }
                }
        
                $customer_name = array_column($resultFilters, 'customer_name');
                array_multisort($customer_name, SORT_ASC, $resultFilters);
                $noOpnameSpace = $this->OpnameSpace->getAutoNumberOpnameSpace();
                $checkOpnameSpace = $this->OpnameSpace->opnameSpaceCheck($branchId,$request_date);
                $this->db->trans_start();
                if(empty($checkOpnameSpace)){
                    $data = [
                        'id_branch' => $branchId,
                        'no_opname_space' => $noOpnameSpace,
                        'opname_space_date' =>  $request_date,
                        'description' => $description,
                        'status' => OpnameSpaceModel::STATUS_PENDING,
                    ];
                    $this->OpnameSpace->create($data);
                    $opnameSpaceId = $this->db->insert_id();
                    
                    foreach ($resultFilters as $booking){
                        $this->opnameSpaceBookingIn->create([
                            'id_opname_space' => $opnameSpaceId,
                            'id_booking' => $booking['id_booking'],
                            'no_reference' => $booking['no_reference'],
                            'description' => $booking['description'],
                        ]);
                    }
                    $this->statusHistory->create([
                        'id_reference' => $opnameSpaceId,
                        'type' => StatusHistoryModel::TYPE_OPNAME_SPACE,
                        'status' => OpnameSpaceModel::STATUS_PENDING,
                        'description' => 'Create opname space',
                        'data' => json_encode($data)
                    ]);
                }else{
                    echo "opname-space stocks failed, opname-space is already exists!";
                }
    
                $this->db->trans_complete();
                
            }else{
                echo "Opname Space stocks failed, Please select specific branch!";    
            }
    
    
            if ($this->db->trans_status()) {
                echo "Opname Space successfully generated";
            } else {
                echo "Opname Space failed, try again or contact administrator";
            }
        }
    }

    /**
     * Send ops performance outbound.
     */
    public function send_ops_performance_outbound()
    {
        $this->load->model('ReportPerformanceModel', 'reportPerformance');

        $selectedBranch = 8;
        $selectedCustomer = 9359;
        $minimumDate = '2020-10-24';

        $filters = [
            'branch' => $selectedBranch,
            'customer' => $selectedCustomer,
            'date_from' => $minimumDate
        ];
        $reports = $this->reportPerformance->getOpsPerformanceOutbound($filters);

        $customerIds = if_empty(array_unique(array_column($reports, 'id_customer')), []);
        foreach ($customerIds as $customerId) {
            $customer = $this->peopleModel->getById($customerId);
            $customerBranch = $this->peopleBranchModel->getBy(['id_customer' => $customerId, 'id_branch' => $selectedBranch], true);

            if (!empty($customerBranch) && !empty($customerBranch['whatsapp_group'])) {
                $reportCustomers = array_filter($reports, function ($report) use ($customer) {
                    return $report['id_customer'] == $customer['id'];
                });

                $reportCompleted = array_values(array_filter($reportCustomers, function ($report) {
                    return $report['booking_status'] == 'COMPLETED';
                }));
                $reportOutstanding = array_values(array_filter($reportCustomers, function ($report) {
                    return $report['booking_status'] != 'COMPLETED';
                }));

                // generate pdf, and put into temp folder (we need persist to disk because chat does not support stream buffer data
                $page = $this->load->view('report/print_performance_outbound', compact('reportCompleted', 'reportOutstanding'), true);
                $pdf = $this->exporter->exportToPdf('Performance outbound per ' . date('Y-m-d'), $page, ['paper' => 'A4', 'orientation' => 'landscape', 'buffer' => true]);
                $pdfFileName = "Performance outbound " . date('Y-m-d') . ".pdf";
                file_put_contents(FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . $pdfFileName, $pdf);

                $sendPdf = $this->notification->broadcast([
                    'url' => 'sendFile',
                    'method' => 'POST',
                    'payload' => [
                        'chatId' => detect_chat_id($customerBranch['whatsapp_group']),
                        'body' => base_url('uploads/temp/' . $pdfFileName),
                        'filename' => $pdfFileName,
                        'caption' => $pdfFileName
                    ]
                ], NotificationModel::TYPE_CHAT_PUSH);

                if ($sendPdf) {
                    echo 'Outbound performance ' . $customer['name'] . ' successfully sent';
                } else {
                    echo 'Send outbound performance ' . $customer['name'] . ' failed';
                }
            } else {
                echo 'Outbound performance: no group available for ' . $customer['name'];
            }
        }
    }

    /**
     * Send report deferred tps activity,
     * this report SHOULD triggered from report_scheduler() function.
     *
     * @param $reportSchedule
     */
    private function send_report_deferred_tps($reportSchedule)
    {
        $dateFrom = ''; // we can decide default behaviour if report schedule is not set
        $dateTo = date('Y-m-d');
        switch ($reportSchedule['recurring_period']) {
            case ReportScheduleModel::PERIOD_DAILY:
                $dateFrom = date('Y-m-d', strtotime('-1 days'));
                break;
            case ReportScheduleModel::PERIOD_WEEKLY:
                $dateFrom = date('Y-m-d', strtotime('-1 weeks'));
                break;
            case ReportScheduleModel::PERIOD_MONTHLY:
                $dateFrom = date('Y-m-d', strtotime('-1 months'));
                break;
            case ReportScheduleModel::PERIOD_ANNUAL:
                $dateFrom = date('Y-m-d', strtotime('-1 years'));
                break;
        }

        $filters = [
            'date_from' => $dateFrom,
            'date_to' => $dateTo,
            'is_deferred' => true
        ];
        $reportTps = $this->reportTps->getReportDeferredTPS($filters);
        $tpsIds = array_unique(array_column($reportTps, 'id_tps'));
        foreach ($tpsIds as $tpsId) {
            $tps = $this->peopleModel->getById($tpsId);
            $data = [
                'reports' => array_filter($reportTps, function ($report) use ($tpsId) {
                    return $report['id_tps'] == $tpsId;
                }),
                'filters' => $filters,
                'tps' => $tps
            ];
            $export = $this->reportTps->exportReportDeferredTPS($data, false);

            // collect email addresses
            $emailTo = $this->getEmailFromProfile($reportSchedule['send_to_type'], $reportSchedule['send_to'], $tps);
            $emailCC = $this->getEmailFromProfile($reportSchedule['send_cc_type'], $reportSchedule['send_cc'], $tps);
            $emailBCC = $this->getEmailFromProfile($reportSchedule['send_bcc_type'], $reportSchedule['send_bcc'], $tps);

            if (!empty($emailTo)) {
                $emailTitle = "TPS deferred activity report: {$tps['name']} from " . (empty($dateFrom) ? 'the start' : $dateFrom) . " until {$dateTo}";
                $emailTemplate = 'emails/basic';
                $emailData = [
                    'name' => $tps['name'],
                    'title' => 'Deferred Activity Report',
                    'content' => 'We would inform you about activity in Transcon Indonesia from ' . (empty($dateFrom) ? 'the start' : $dateFrom) . ' until ' . $dateTo . ' (this email may contains attachment)',
                    'email' => $emailTo[0]
                ];

                $emailOptions = [
                    'cc' => $emailCC,
                    'bcc' => $emailBCC,
                    'attachment' => [
                        [
                            'source' => $export,
                            'disposition' => 'attachment',
                            'file_name' => "tps_deferred_activity.xlsx",
                        ]
                    ],
                ];

                $this->mailer->send($emailTo, $emailTitle, $emailTemplate, $emailData, $emailOptions);
                echo 'Deferred TPS reports email were sent...' . PHP_EOL;
            }
        }
    }

    /**
     * Helper to get email collection from people.
     *
     * @param $sendType
     * @param $additionalEmails
     * @param $profile
     * @return array
     */
    private function getEmailFromProfile($sendType, $additionalEmails, $profile) // make another function if it gets from user or other sources
    {
        $emails = [];
        switch ($sendType) {
            case ReportScheduleModel::SEND_TYPE_DEFAULT:
                // decided outside this function (define in every report what default behaviour of recipients acquired from)
                break;
            case ReportScheduleModel::SEND_TYPE_PROFILE:
                if (!empty($profile['email'])) {
                    $emails[] = $profile['email'];
                }
                break;
            case ReportScheduleModel::SEND_TYPE_PROFILE_CONTACT:
                $personContacts = $this->peopleContactModel->getContactByPerson($profile['id']);
                foreach ($personContacts as $contact) {
                    if (!empty($contact['email'])) {
                        $emails[] = $contact['email'];
                    }
                }
                break;
            case ReportScheduleModel::SEND_TYPE_USER:
                $user = $this->user->getById($profile['id']);
                if (!empty($user)) {
                    $emails[] = $user['email'];
                }
                break;
            case ReportScheduleModel::SEND_TYPE_BRANCH_TPP_SUPPORT:
                $getBranchByTPP = $this->branchModel->getBy(['branch_type' => "TPP"]);
                $emailSupportTPP = array_unique(array_filter(array_column($getBranchByTPP, "email_support")));
                if (!empty($emailSupportTPP)) {
                    foreach($emailSupportTPP as $email){
                        $emails[] = $email;
                    }
                }
                break;
            case ReportScheduleModel::SEND_TYPE_BRANCH_PLB_SUPPORT:            
                $getBranchByPLB = $this->branchModel->getBy(['branch_type' => "PLB"]);
                $emailSupportPLB = array_unique(array_filter(array_column($getBranchByPLB, "email_support")));
                if (!empty($emailSupportPLB)) {
                    foreach($emailSupportPLB as $email){
                        $emails[] = $email;
                    }
                }
                break; 
                
        }
        if (!empty(trim($additionalEmails))) {
            $emails = array_merge($emails, explode(',', $additionalEmails));
        }

        return $emails;
    }

    /**
     * Send Notification WA when tep checkin above 2 hours.
     *
     * @param $sendType
     * @param $additionalEmails
     * @param $profile
     * @return array
     */
    public function send_notification_tep() //automate every minute
    {
        $teps = $this->transporterEntryPermit->getAllTep(
            [
                'transporter_entry_permits.tep_category' => 'OUTBOUND',
                'transporter_entry_permits.checked_in_at is not null' => null,
                'transporter_entry_permits.checked_out_at is null' => null,
                'TIMESTAMPDIFF(MINUTE,transporter_entry_permits.checked_in_at,NOW()) >= 120' => null,
                'DATE(transporter_entry_permits.expired_at)>=' => '2021-02-10',//cut off biar gak banyak
            ]);
        $data_text = "*[TEP OUTBOUND MORE THAN 2 HOURS]*". "\n\n" .
        "We would inform you about TEP check in more than 2 hours.". "\n";     
        $data_body = '';
        $no = 1;
        $available = false;
        foreach($teps as $tep){
            $to_time = strtotime($tep['checked_in_at']);
            $from_time = strtotime(date('Y-m-d H:i:s'));
            $minute =  round(abs($to_time - $from_time) / 60);
            $mod = ($minute % 30);
            if($mod == 1 && $minute <= 240){
                $available = true;
                $data_body .= $no.". No TEP : *".$tep['tep_code']."*\n".
                              "    Customer : *".if_empty($tep['customer_name_out'], '-')."*\n".
                              "    Nopol : *".strtoupper($tep['receiver_no_police'])."*\n".
                              "    Aju : *".$tep['no_aju_multi']."*\n".
                              "    Check In : *".format_date($tep['checked_in_at'],'h:i:s')."* - ".format_date($tep['checked_in_at'],'d F Y')."\n\n";
                $no++;
            }
        }
        if($available){
            $whatsapp_group = get_setting('whatsapp_group_admin');
            $data_text .= $data_body;
            $this->send_message($data_text, $whatsapp_group);
            echo "successfully sent notification";
        }else{
            echo "no notification sent";
        }
    }

    /**
     * Over space reminder M<sup>2</sup> format.
     *
     * @param false $notifyManagementOnly
     */
    public function over_space_reminder_m2($notifyManagementOnly = false)
    {
        $this->load->model('notifications/OverSpaceM2CustomerNotification');
        $managementGroup = get_setting('whatsapp_group_management');

        $customers = $this->customerStorageCapacity->getAll();
        $customerIds = array_unique(array_column($customers, 'id_customer'));
        foreach ($customerIds as $customerId) {
            $customer = $this->peopleModel->getById($customerId);
            if (empty($customer['id_parent'])) {
                $balances = $this->reportStorage->getCustomerBalanceStorage([
                    'customer' => $customerId,
                    'customer_include_member' => true,
                    'data' => 'stock',
                ]);
                // print_debug([$customer['name'], $balances], false);

                if (!empty($balances)) {
                    $balance = $balances[0];
                    if ($balance['used_warehouse_percent'] >= 75 || $balance['used_yard_percent'] >= 75 || $balance['used_covered_yard_percent'] >= 75) {
                        // send to customer
                        $customerBranch = $this->peopleModel->getPeopleByIdCustomerIdBranch($customer['id'], $customer['id_branch']);
                        if (!empty($customerBranch['whatsapp_group']) && !$notifyManagementOnly) {
                            $customer['whatsapp_group'] = $customerBranch['whatsapp_group'];
                            $this->notification
                                ->via([Notify::CHAT_PUSH])
                                ->to($customer['whatsapp_group'])
                                ->send(new OverSpaceM2CustomerNotification($balance));
                            echo "Sent over space notification customer " . $customer['name'] . PHP_EOL;
                        }

                        // send to management
                        if (!empty($managementGroup)) {
                            $this->notification
                                ->via([Notify::CHAT_PUSH])
                                ->to($managementGroup)
                                ->send(new OverSpaceM2CustomerNotification($balance));
                            echo "Sent over space M2 notification management" . PHP_EOL;
                        }
                    }
                }
            }
        }
    }

    /**
     * Over space reminder for dedicated customers.
     * Should set every day at 08:30
     *
     * @param bool $notifyManagementOnly
     */
    public function over_space_reminder($notifyManagementOnly = false)
    {
        $this->load->model('notifications/OverSpaceCustomerNotification');
        $managementGroup = get_setting('whatsapp_group_management');
        $customers = $this->peopleModel->getCustomersByActiveStorage();
        foreach ($customers as $customer) {
            $customer = $this->customerStorageCapacity->getCustomerStorageSummary($customer);
            $warehouseUsagePercent = $customer['warehouse_storages']['total_used_percent'];
            $yardUsagePercent = $customer['yard_storages']['total_used_percent'];
            $coveredUsagePercent = $customer['covered_yard_storages']['total_used_percent'];

            if ($warehouseUsagePercent >= 75 || $yardUsagePercent >= 75 || $coveredUsagePercent >= 75) {
                // send to customer
                $customerBranch = $this->peopleModel->getPeopleByIdCustomerIdBranch($customer['id'], $customer['id_branch']);
                if (!empty($customerBranch['whatsapp_group']) && !$notifyManagementOnly) {
                    $customer['whatsapp_group'] = $customerBranch['whatsapp_group'];
                    $this->notification
                        ->via([Notify::CHAT_PUSH])
                        ->to($customer['whatsapp_group'])
                        ->send(new OverSpaceCustomerNotification($customer));
                    echo "Sent over space notification customer " . $customer['name'] . PHP_EOL;
                }

                // send to management
                if (!empty($managementGroup)) {
                    $this->notification
                        ->via([Notify::CHAT_PUSH])
                        ->to($managementGroup)
                        ->send(new OverSpaceCustomerNotification($customer));
                    echo "Sent over space notification management" . PHP_EOL;
                }
            }
        }
    }


    /**
     * Used space for cash and carry customer.
     * Should set at 09:30 every sunday
     *
     * @param false $notifyManagementOnly
     */
    public function used_space_reminder_m2($notifyManagementOnly = false)
    {
        $this->load->model('notifications/UsedSpaceM2CustomerNotification');

        $managementGroup = get_setting('whatsapp_group_management');

        $goodsStorages = $this->reportStorage->getGoodsStockUsage([
            'branch_type' => BranchModel::BRANCH_TYPE_PLB,
            'outbound_type' => PeopleModel::OUTBOUND_CASH_AND_CARRY,
        ]);
        $containerizedGoodsStorages = $this->reportStorage->getContainerizedGoodsStockUsage([
            'branch_type' => BranchModel::BRANCH_TYPE_PLB,
            'outbound_type' => PeopleModel::OUTBOUND_CASH_AND_CARRY,
        ]);
        $customerIds = array_merge(array_column($goodsStorages, 'id_customer'), array_column($containerizedGoodsStorages, 'id_customer'));
        $customerIds = array_unique($customerIds);

        $usedStorages = [];
        foreach ($customerIds as $customerId) {
            $usedStorages[$customerId] = [
                'id_customer' => $customerId,
                'customer_name' => 'Customer',
                'used_warehouse_storage' => 0,
                'used_yard_storage' => 0,
                'used_covered_yard_storage' => 0,
            ];
            foreach ($goodsStorages as $itemStorage) {
                if ($itemStorage['id_customer'] == $customerId) {
                    $usedStorages[$customerId]['customer_name'] = $itemStorage['customer_name'];
                    $usedStorages[$customerId]['used_warehouse_storage'] += $itemStorage['used_warehouse_storage'];
                    $usedStorages[$customerId]['used_yard_storage'] += $itemStorage['used_yard_storage'];
                    $usedStorages[$customerId]['used_covered_yard_storage'] += $itemStorage['used_covered_yard_storage'];
                }
            }
            foreach ($containerizedGoodsStorages as $containerizedItemStorage) {
                if ($containerizedItemStorage['id_customer'] == $customerId) {
                    $usedStorages[$customerId]['customer_name'] = $containerizedItemStorage['customer_name'];
                    $usedStorages[$customerId]['used_warehouse_storage'] += $containerizedItemStorage['used_warehouse_storage'];
                    $usedStorages[$customerId]['used_yard_storage'] += $containerizedItemStorage['used_yard_storage'];
                    $usedStorages[$customerId]['used_covered_yard_storage'] += $containerizedItemStorage['used_covered_yard_storage'];
                }
            }
        }

        foreach ($usedStorages as $goodsStorage) {
            if (empty($goodsStorage['used_warehouse_storage']) && empty($goodsStorage['used_yard_storage']) && empty($goodsStorage['used_covered_yard_storage'])) {
                continue;
            }
            $customer = $this->peopleModel->getById($goodsStorage['id_customer']);
            $customerBranch = $this->peopleModel->getPeopleByIdCustomerIdBranch($customer['id'], $customer['id_branch']);
            $goodsStorage['branch'] = $customerBranch['branch'];
            $goodsStorage['whatsapp_group'] = $customerBranch['whatsapp_group'];
            if (!empty($customerBranch['whatsapp_group']) && !$notifyManagementOnly) {
                $this->notification
                    ->via([Notify::CHAT_PUSH])
                    ->to($goodsStorage['whatsapp_group'])
                    ->send(new UsedSpaceM2CustomerNotification($goodsStorage));
                echo "Sent used space notification customer " . $customer['name'] . PHP_EOL;
            }

            // send to management
            if (!empty($managementGroup)) {
                $this->notification
                    ->via([Notify::CHAT_PUSH])
                    ->to($managementGroup)
                    ->send(new UsedSpaceM2CustomerNotification($goodsStorage));
                echo "Sent used space M2 notification management" . PHP_EOL;
            }
        }
    }

    /**
     * Used space for cash and carry customer.
     * Should set at 09:30 every sunday
     *
     * @param false $notifyManagementOnly
     */
    public function used_space_reminder($notifyManagementOnly = false)
    {
        $this->load->model('notifications/UsedSpaceCustomerNotification');

        $managementGroup = get_setting('whatsapp_group_management');

        $goodsStorages = $this->dashboard->getGoodsStorageCapacityUsage([
            'branch_type' => BranchModel::BRANCH_TYPE_PLB,
            'outbound_type' => PeopleModel::OUTBOUND_CASH_AND_CARRY,
            'have_whatsapp_group' => true,
        ]);
        $containerStorages = $this->dashboard->getContainersStorageCapacityUsage([
            'branch_type' => BranchModel::BRANCH_TYPE_PLB,
            'outbound_type' => PeopleModel::OUTBOUND_CASH_AND_CARRY,
            'have_whatsapp_group' => true,
        ]);
        $containerStorages = array_map(function($containerItem) {
            $containerItem['goods_name'] = 'CONTAINER';
            $containerItem['id_goods'] = 0;
            $containerItem['unit_length'] = 0;
            $containerItem['unit_width'] = 0;
            return $containerItem;
        }, $containerStorages);
        $goodsStorages = array_merge($goodsStorages, $containerStorages);

        $customerIds = array_column($goodsStorages, 'id_customer');
        $parentIds = array_column($goodsStorages, 'id_parent');
        $customerUniqueIds = array_unique(array_merge($customerIds, $parentIds));

        foreach ($customerUniqueIds as $customerId) {
            $customer = $this->peopleModel->getById($customerId);
            if (!empty($customer) && empty($customer['id_parent'])) {
                $storageData = array_filter($goodsStorages, function ($itemStorage) use ($customerId) {
                    return in_array($customerId, [$itemStorage['id_customer'], $itemStorage['id_parent']]);
                });
                $warehouseData = array_filter($storageData, function ($item) {
                    return $item['warehouse_type'] == 'WAREHOUSE';
                });
                $yardData = array_filter($storageData, function ($item) {
                    return $item['warehouse_type'] == 'YARD';
                });
                $coveredYardData = array_filter($storageData, function ($item) {
                    return $item['warehouse_type'] == 'COVERED YARD';
                });
                $customer['warehouse_space_used'] = array_sum(array_column($warehouseData, 'teus_used'));
                $customer['yard_space_used'] = array_sum(array_column($yardData, 'teus_used'));
                $customer['covered_yard_space_used'] = array_sum(array_column($coveredYardData, 'teus_used'));

                // send to customer
                $customerBranch = $this->peopleModel->getPeopleByIdCustomerIdBranch($customer['id'], $customer['id_branch']);
                $customer['branch'] = $customerBranch['branch'];
                $customer['whatsapp_group'] = $customerBranch['whatsapp_group'];
                if (!empty($customerBranch['whatsapp_group']) && !$notifyManagementOnly) {
                    $this->notification
                        ->via([Notify::CHAT_PUSH])
                        ->to($customer['whatsapp_group'])
                        ->send(new UsedSpaceCustomerNotification($customer));
                    echo "Sent used space notification customer " . $customer['name'] . PHP_EOL;
                }

                // send to management
                if (!empty($managementGroup)) {
                    $this->notification
                        ->via([Notify::CHAT_PUSH])
                        ->to($managementGroup)
                        ->send(new UsedSpaceCustomerNotification($customer));
                    echo "Sent used space notification management" . PHP_EOL;
                }
            }
        }
    }

    /**
     * Space usage summary
     */
    public function used_space_summary()
    {
        $this->load->model('notifications/UsedSpaceSummaryNotification');

        $branches = $this->branchModel->getBy(['ref_branches.dashboard_status' => 1]);

        foreach ($branches as $branch) {
            $customerStorages = $this->peopleModel->getCustomersByActiveStorage(['branch' => $branch['id']]);
            foreach ($customerStorages as &$customer) {
                $customer = $this->customerStorageCapacity->getCustomerStorageSummary($customer);
            }

            $emailTo = get_setting('email_finance2');
            if (!empty($customerStorages) && !empty($emailTo)) {
                $this->notification
                    ->via([Notify::MAIL_PUSH])
                    ->to($emailTo)
                    ->send(new UsedSpaceSummaryNotification($customerStorages, $branch));
                echo "Sent used space summary notification branch " . $branch['branch'] . PHP_EOL;
            }
        }
    }

    /**
     * Space usage summary in M<sup>2</sup>
     */
    public function used_space_summary_m2()
    {
        $this->load->model('notifications/UsedSpaceM2SummaryNotification');

        $branches = $this->branchModel->getBy(['ref_branches.dashboard_status' => 1]);

        foreach ($branches as $branch) {
            $customers = $this->customerStorageCapacity->getAll(['branch' => $branch['id']]);
            $customerIds = array_unique(array_column($customers, 'id_customer'));
            $customerStorages = [];
            foreach ($customerIds as $customerId) {
                $customer = $this->peopleModel->getById($customerId);
                if (empty($customer['id_parent'])) {
                    $balances = $this->reportStorage->getCustomerBalanceStorage([
                        'customer' => $customerId,
                        'customer_include_member' => true,
                        'data' => 'all-data'
                    ]);

                    if (!empty($balances)) {
                        $customerStorages[] = $balances[0];
                    }
                }
            }

            $emailTo = get_setting('email_finance2');
            if (!empty($customerStorages) && !empty($emailTo)) {
                $this->notification
                    ->via([Notify::MAIL_PUSH])
                    ->to($emailTo)
                    ->send(new UsedSpaceM2SummaryNotification($customerStorages, $branch));
                echo "Sent used space M2 summary notification branch " . $branch['branch'] . PHP_EOL;
            }
        }
    }

    /**
     * Generate storage usage for usage more than 75%
     */
    public function generate_over_space_75()
    {
        $this->load->model('StorageUsageModel', 'storageUsage');
        $this->load->model('StorageUsageCustomerModel', 'storageUsageCustomer');
        $this->load->model('notifications/OverSpaceM2CustomerNotification');

        $branches = $this->branchModel->getBy([
            'ref_branches.dashboard_status' => 1
        ]);

        $this->db->trans_start();

        foreach ($branches as $branch) {
            $currentStorageUsage = $this->storageUsage->getBy([
                'storage_usages.date' => date('Y-m-d'),
                'storage_usages.id_branch' => $branch['id']
            ]);

            if (empty($currentStorageUsage)) {
                $customers = $this->customerStorageCapacity->getAll(['branch' => $branch['id']]);
                $customerIds = array_unique(array_column($customers, 'id_customer'));

                $customerStorages75 = [];
                foreach ($customerIds as $customerId) {
                    $customer = $this->peopleModel->getById($customerId);
                    if (empty($customer['id_parent'])) {
                        $balances = $this->reportStorage->getCustomerBalanceStorage([
                            'branch' => $branch['id'],
                            'customer' => $customerId,
                            'customer_include_member' => true,
                            'data' => 'stock'
                        ]);

                        if (!empty($balances)) {
                            $currentBalance = $balances[0];
                            $warehousePercent = $currentBalance['used_warehouse_percent'] >= 75;
                            $yardPercent = $currentBalance['used_yard_percent'] >= 75;
                            $coveredYardPercent = $currentBalance['used_covered_yard_percent'] >= 75;
                            if ($warehousePercent || $yardPercent || $coveredYardPercent) {
                                $currentBalance['need_revalidate'] = true;

                                // if last transaction is validated and no transaction after, we send data immediately
                                $lastTransaction = $this->storageUsageCustomer->getLastStorageUsage($currentBalance['id_customer'], $branch['id'], date('Y-m-d'));
                                if (!empty($lastTransaction)) {
                                    $transactions = [];
                                    $customerMembers = $this->peopleModel->getBy(['ref_people.id_parent' => $lastTransaction['id_customer']]);
                                    if (!empty($customerMembers)) {
                                        foreach ($customerMembers as $customerMember) {
                                            $transactions = array_merge($transactions, $this->workOrder->getBy([
                                                'bookings.id_customer' => $customerMember['id'],
                                                'work_orders.completed_at>=' => $lastTransaction['date'] . ' 00:00:00',
                                                'work_orders.completed_at<=' => date('Y-m-d H:i:s'),
                                                'ref_handling_types.multiplier_goods!=' => 0,
                                            ]));
                                        }
                                    } else {
                                        $transactions = $this->workOrder->getBy([
                                            'bookings.id_customer' => $lastTransaction['id_customer'],
                                            'work_orders.completed_at>=' => $lastTransaction['date'] . ' 00:00:00',
                                            'work_orders.completed_at<=' => date('Y-m-d H:i:s'),
                                            'ref_handling_types.multiplier_goods!=' => 0,
                                        ]);
                                    }

                                    if (empty($transactions)) {
                                        $currentBalance['need_revalidate'] = false;
                                    }
                                }

                                $customerStorages75[] = $currentBalance;
                            }
                        }
                    }
                }

                $customerStorageNeedRevalidates = array_filter($customerStorages75, function($item) {
                    return $item['need_revalidate'];
                });
                $customerStorageSendImmediately = array_filter($customerStorages75, function($item) {
                    return !$item['need_revalidate'];
                });

                if (!empty($customerStorageNeedRevalidates)) {
                    $this->storageUsage->create([
                        'id_branch' => $branch['id'],
                        'date' => date('Y-m-d'),
                        'status' => StorageUsageModel::STATUS_PENDING,
                        'description' => 'Auto generated'
                    ]);
                    $storageUsageId = $this->db->insert_id();

                    $this->statusHistory->create([
                        'id_reference' => $storageUsageId,
                        'type' => StatusHistoryModel::TYPE_STORAGE_USAGE,
                        'status' => StorageUsageModel::STATUS_PENDING,
                        'description' => 'Auto generate daily storage usage',
                    ]);

                    foreach ($customerStorageNeedRevalidates as $customerStorage) {
                        $this->storageUsageCustomer->create([
                            'id_storage_usage' => $storageUsageId,
                            'id_customer' => $customerStorage['id_customer'],
                            'warehouse_capacity' => $customerStorage['warehouse_capacity'],
                            'yard_capacity' => $customerStorage['yard_capacity'],
                            'covered_yard_capacity' => $customerStorage['covered_yard_capacity'],
                            'warehouse_usage' => $customerStorage['used_warehouse_storage'],
                            'yard_usage' => $customerStorage['used_yard_storage'],
                            'covered_yard_usage' => $customerStorage['used_covered_yard_storage'],
                            'status' => StorageUsageCustomerModel::STATUS_PENDING
                        ]);
                        $storageUsageCustomerId = $this->db->insert_id();

                        $this->statusHistory->create([
                            'id_reference' => $storageUsageCustomerId,
                            'type' => StatusHistoryModel::TYPE_STORAGE_USAGE_CUSTOMER,
                            'status' => StorageUsageCustomerModel::STATUS_PENDING,
                            'description' => 'Auto generate customer storage usage',
                        ]);
                    }
                }

                // send immediately for no need-validation data
                if (!empty($customerStorageSendImmediately)) {
                    foreach ($customerStorageSendImmediately as $customerStorage) {
                        // for group member notify only the member that has stock
                        $customerMembers = $this->peopleModel->getBy(['ref_people.id_parent' => $customerStorage['id_customer']]);
                        if (empty($customerMembers)) {
                            $notifiedCustomerIds = [$customerStorage['id_customer']];
                        } else {
                            $notifiedCustomerIds = array_column($customerMembers, 'id');
                        }
                        foreach ($notifiedCustomerIds as $customerId) {
                            $customer = $this->peopleModel->getById($customerId);
                            $customerBranch = $this->peopleModel->getPeopleByIdCustomerIdBranch($customer['id'], $branch['id']);
                            $availableStocks = $this->reportStock->getStockGoods([
                                'data' => 'stock',
                                'owner' => $customerId
                            ]);
                            if (!empty($customerBranch['whatsapp_group']) && !empty($availableStocks)) {
                                $customer['whatsapp_group'] = $customerBranch['whatsapp_group'];
                                if ($branch['id'] == 8) {
                                    $customer['whatsapp_group'] = get_setting('whatsapp_group_management');
                                }
                                $this->notification
                                    ->via([Notify::CHAT_PUSH])
                                    ->to($customer)
                                    ->send(new OverSpaceM2CustomerNotification($customerStorage));
                                echo "Storage usage {$customerStorage['customer_name']} branch {$branch['branch']} send immediately" . PHP_EOL;
                            }
                        }
                    }
                }

                if (empty($customerStorages75)) {
                    echo "Storage usage data branch {$branch['branch']} no customer over 75%" . PHP_EOL;
                }
            } else {
                echo "Storage usage data branch {$branch['branch']} already exist" . PHP_EOL;
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            echo "Space usage more than equal 75% at " . date('Y-m-d') . " generated or resend" . PHP_EOL;
        } else {
            echo "Generate space usage more than equal 75% failed";
        }
    }

    /**
     * Generate storage usage for usage more than 75% weekly
     */
    public function generate_over_space_summary_weekly()
    {
        $this->load->model('jobs/CustomerStorageJob', 'customerStorageJob');
        $this->load->model('StorageOverSpaceModel', 'storageOverSpace');

        $type = StorageOverSpaceModel::TYPE_WEEKLY;
        $dateFrom = date('Y-m-d', strtotime('-7 days'));
        $dateTo = date('Y-m-d', strtotime('-1 days'));
        $this->customerStorageJob->generateOverSpaceSummary($type, $dateFrom, $dateTo);
    }

    /**
     * Generate storage usage for usage more than 75% monthly,
     * assume called at 12:00AM the first of every month (0 0 1 * *)
     */
    public function generate_over_space_summary_monthly()
    {
        $this->load->model('jobs/CustomerStorageJob', 'customerStorageJob');
        $this->load->model('StorageOverSpaceModel', 'storageOverSpace');

        $type = StorageOverSpaceModel::TYPE_MONTHLY;
        $dateFrom = (new DateTime('now'))->sub(new DateInterval('P1M'))->format('Y-m-01');
        $dateTo = format_date($dateFrom, 'Y-m-t');

        // will produce Year 2038 problem
        // $dateFrom = date('Y-m-01', strtotime('-1 month'));
        // $dateTo = date('Y-m-01', strtotime($dateFrom));

        $this->customerStorageJob->generateOverSpaceSummary($type, $dateFrom, $dateTo);
    }

    /**
     * Send Notification WA when tep late checkin, send every 1 hours.
     *
     */
    public function send_notification_tep_late() //automate every minute
    {
        $teps = $this->transporterEntryPermit->getAllTep(
            [
                'transporter_entry_permits.tep_category' => 'OUTBOUND',
                'transporter_entry_permits.checked_in_at is null' => null,
                'transporter_entry_permits.checked_out_at is null' => null,
                'DATE(transporter_entry_permits.expired_at)' => date('Y-m-d'),
                'DATE(transporter_entry_permits.expired_at)>=' => '2021-05-04',//cut off biar gak banyak
            ]);
        $data_body = '';
        $available = false;
        $from_time = strtotime(date('Y-m-d H:i:s'));
        $date_now = date('Y-m-d');
        foreach($teps as $tep){
            $to_time = strtotime($date_now." ".$tep['queue_time']);
            $minute =  round(abs($to_time - $from_time) / 60);
            $mod = ($minute % 60);
            if($mod == 1 || $minute == 1){
                $available = true;
                $data_body = "[LATE CHECK IN] Transporter entry permit with code *".$tep['tep_code']."* has late check-in\n";
                $data_body .= " No TEP : *".$tep['tep_code']."*\n".
                              "    Customer : *".if_empty($tep['customer_name_out'], '-')."*\n".
                              "    Aju : *".$tep['no_aju_multi']."*\n".
                              "    Queue time : *".format_date($tep['queue_time'],'h:i:s')."* \n\n";
                $customerId = !empty($tep['id_customer_out'])? $tep['id_customer_out'] : $tep['id_customer_out_ecs'];
                $customerId = explode(",",$customerId);
                $customers = $this->people->getById($customerId);
                foreach ($customers as $key => $customer) {
                    $whatsapp_group = $customer['whatsapp_group'];
                    $this->send_message($data_body, $whatsapp_group);    
                    echo "successfully sent notification";
                }          
            }
        }
        if($available){
        }else{
            echo "no notification sent";
        }
    }

    /**
     * Send Notification WA rule complain KPI
     *
     */
    public function send_notification_complain_kpi() //automate trigger every 1 hours
    {
        echo "send_notification_complain_kpi started at " . date('Y-m-d H:i:s') . PHP_EOL;
        $time = date('H:00:00');
        $dateTime = date('Y-m-d H:i:s');
        $t1 = strtotime($dateTime);
        $complainKpi = $this->complainKpi->getByReminder([
            'time' => $time
        ]);
        foreach ($complainKpi as $key => $complain_kpi) {
            if ($complain_kpi['kpi'] == ComplainKpiModel::KPI_RESPONSE_WAITING_TIME) {
                continue;
            }
            switch($complain_kpi['kpi']){
                case ComplainKpiModel::KPI_SUBMIT_APPROVAL :
                    $complainNotif = [];
                    $lockedBranch = [];
                    $minLockBranch = [];
                    $complains = $this->complainModel->getBySubmitApproval();
                    $complains2 = $this->complainModel->getByDisproveApproval();
                    $complains = array_unique(array_merge($complains, $complains2),SORT_REGULAR);
                    //init lock branch
                    foreach ($complains as $complain){
                        $lockedBranch[$complain['id_branch']] = false;
                        $minLockBranch[$complain['id_branch']] = '2121-09-02 00:00:00';
                    }
                    foreach ($complains as $complain){
                        $complainHistory = $this->complainHistory->getLastDisprove($complain['id']);
                        if(empty($complainHistory)){
                            $complainHistory = $this->complainHistory->getLastOnReview($complain['id']);
                        }                        
                        if(empty($complainHistory)){
                            continue;
                        }
                        $t2 = strtotime($complainHistory['created_at']);
                        
                        $diff = $t1 - $t2;
                        $hours = floor($diff / 3600);
                        $day = ceil($diff / (3600*24));
                        if($complain['value_type'] == 'MAJOR'){
                            if($complain_kpi['major']<=$hours){
                                $mod = $day % $complain_kpi['recur_day'];
                                if($complain_kpi['recur_day']>= $day || $mod == 0){
                                    $complainNotif[] = $complain;
                                    //to cek if job is lock
                                    if(!$lockedBranch[$complain['id_branch']] && ($complain_kpi['major']+24) <= $hours){
                                        $lockedBranch[$complain['id_branch']] = true;
                                    }
                                    $lockTime = date('Y-m-d H:i:s',strtotime("+".($complain_kpi['major']+24)." hour",strtotime($complainHistory['created_at'])));
                                    //get minimum lock time
                                    if($minLockBranch[$complain['id_branch']] > $lockTime){
                                        $minLockBranch[$complain['id_branch']] = $lockTime;
                                    }
                                }
                            }
                        }else{
                            if($complain_kpi['minor']<=$hours){
                                $mod = $day % $complain_kpi['recur_day'];
                                if($complain_kpi['recur_day']>= $day || $mod == 0){
                                    $complainNotif[] = $complain;
                                    //to cek if job is lock
                                    if(!$lockedBranch[$complain['id_branch']] && ($complain_kpi['minor']+24) <= $hours){
                                        $lockedBranch[$complain['id_branch']] = true;
                                    }
                                    $lockTime = date('Y-m-d H:i:s',strtotime("+".($complain_kpi['minor']+24)." hour",strtotime($complainHistory['created_at'])));
                                    //get minimum lock time
                                    if($minLockBranch[$complain['id_branch']]>$lockTime){
                                        $minLockBranch[$complain['id_branch']] = $lockTime;
                                    }
                                }
                            } 
                        }
                    }
                    if(!empty($complainNotif)){
                        foreach($complainNotif as $complaint){
                            $complainWhatsapp = $this->complainKpiWhatsapp->getBy([
                                'id_kpi' => $complain_kpi['id'],
                                'group' => $complaint['department'],
                                'id_branch_warehouse' => $complaint['id_branch'],
                            ],true);
                            if(empty($complainWhatsapp)){
                                $complainWhatsapp = $this->complainKpiWhatsapp->getBy([
                                    'id_kpi' => $complain_kpi['id'],
                                    'group' => $complaint['department'],
                                    'id_branch_warehouse' => 0,
                                ],true);
                            }
                            $customer_data = $this->people->getById($complaint['id_customer']);
                            $complain_category = $this->complainCategoryModel->getById($complaint['id_complain_category']);
                            $chatMessage = "*Reminder to Investigation The Complaint ". $complaint['no_complain'] ."*\n\n";
                            $chatMessage .= "Recognizing your very busy schedule, I am sending you this note as a reminder to check and respond the customer complaint immediately.\n\n";
                            $chatMessage = $chatMessage."Code       : *" . $complaint['no_complain'] . "*\n";
                            $chatMessage = $chatMessage."Category   : *" . $complain_category['category'] . " (" .$complain_category['value_type'].")". "*\n";
                            $chatMessage = $chatMessage."Customer   : *" . $customer_data['name'] . "*\n";
                            $chatMessage = $chatMessage."Department : *" . $complaint['department'] . "*\n";
                            $chatMessage = $chatMessage."Branch : *" . $complaint['branch'] . "*\n";
                            $chatMessage = $chatMessage."Date       : *" . format_date($complaint['complain_date'],"H:i:s d F Y") . "*\n";
                            $chatMessage = $chatMessage."Complaint  : *" . $complaint['complain'] . "*\n";
                            $chatMessage = $chatMessage."Status Investigation     : *" . $complaint['status_investigation'] . "*\n";
                            $chatMessage = $chatMessage."Status Complain     : *" . $complaint['status'] . "*\n";

                            if($lockedBranch[$complaint['id_branch']]){
                                $chatMessage = $chatMessage."*The job has been locked, please approve investigation to unlock job*\n\n";
                            }else{
                                $chatMessage = $chatMessage."*The job will be lock, please approve investigation to avoid being lock job at ".format_date($minLockBranch[$complaint['id_branch']],"H:i:s d F Y")."*\n\n";
                            }

                            if ($complaint['status'] == ComplainModel::STATUS_ON_REVIEW) {
                                $chatMessage .= "AWAITING : *{$complaint['department']}* (for investigation)";
                            } else if ($complaint['status_investigation'] == ComplainModel::STATUS_PENDING) {
                                $chatMessage .= "AWAITING : *MANAGER* (for investigation approval)";
                            } else if ($complaint['status_investigation'] == ComplainModel::STATUS_REJECT) {
                                $chatMessage .= "AWAITING : *{$complaint['department']}* (for investigation revision)";
                            }

                            $send = $this->notification->broadcast([
                                'url' => 'sendMessage',
                                'method' => 'POST',
                                'payload' => [
                                    'chatId' => detect_chat_id($complainWhatsapp['contact_group']),
                                    'body' => $chatMessage,
                                ]
                            ], NotificationModel::TYPE_CHAT_PUSH);
                            
                            if (is_array($send) && !empty(get_if_exist($send, 'error'))) {
                                log_message('error', $send['error']);
                            } else {
                                echo "Reminder to respond the customer complaint ". $complaint['no_complain'] ." is sent to wa..." . PHP_EOL;
                            }
                        }                        
                    }
                break;
                case ComplainKpiModel::KPI_APPROVAL_CONCLUSION :
                    $complainNotif = [];
                    $complains = $this->complainModel->getByApprovalConclusion();
                    foreach ($complains as $complain){
                        $complainHistory1 = $this->complainHistory->getLastApproveProcess($complain['id']);
                        $complainHistory2 = $this->complainHistory->getLastDisprove($complain['id']);
                        $complainHistory = [];
                        if(!empty($complainHistory1) && !empty($complainHistory2)){
                            $complainHistory = array_unique(array_merge($complainHistory1,$complainHistory2), SORT_REGULAR);
                        }elseif (!empty($complainHistory1)) {
                            $complainHistory = $complainHistory1;
                        }elseif (!empty($complainHistory2)) {
                            $complainHistory = $complainHistory2;
                        }
                        if(empty($complainHistory)){
                            continue;
                        }
                        $t2 = strtotime($complainHistory['created_at']);
                        $diff = $t1 - $t2;
                        $hours = floor($diff / 3600);
                        $day = ceil($diff / (3600*24));
                        if($complain['value_type'] == 'MAJOR'){
                            if($complain_kpi['major']<=$hours){
                                $mod = $day % $complain_kpi['recur_day'];
                                if($complain_kpi['recur_day']>= $day || $mod == 0){
                                    $complainNotif[] = $complain;
                                }
                            }
                        }else{
                            if($complain_kpi['minor']<=$hours){
                                $mod = $day % $complain_kpi['recur_day'];
                                if($complain_kpi['recur_day']>= $day || $mod == 0){
                                    $complainNotif[] = $complain;
                                }
                            } 
                        }
                    }
                    if(!empty($complainNotif)){
                        foreach($complainNotif as $complaint){
                            $customer_data = $this->people->getById($complaint['id_customer']);
                            $complain_category = $this->complainCategoryModel->getById($complaint['id_complain_category']);
                            $chatMessage = "*Reminder to Conclusion The Complaint ". $complaint['no_complain'] ."*\n\n";
                            $chatMessage .= "Recognizing your very busy schedule, I am sending you this note as a reminder to check and respond the customer complaint immediately.\n\n";
                            $chatMessage = $chatMessage."Code       : *" . $complaint['no_complain'] . "*\n";
                            $chatMessage = $chatMessage."Category   : *" . $complain_category['category'] . " (" .$complain_category['value_type'].")". "*\n";
                            $chatMessage = $chatMessage."Customer   : *" . $customer_data['name'] . "*\n";
                            $chatMessage = $chatMessage."Department : *" . $complaint['department'] . "*\n";
                            $chatMessage = $chatMessage."Date       : *" . format_date($complaint['complain_date'],"H:i:s d F Y") . "*\n";
                            $chatMessage = $chatMessage."Complaint  : *" . $complaint['complain'] . "*\n";
                            $chatMessage = $chatMessage."Status Investigation     : *" . $complaint['status_investigation'] . "*\n";
                            $chatMessage = $chatMessage."Status Complain     : *" . $complaint['status'] . "*\n\n";

                            $complainHistories = $this->complainHistory->getBy([
                                'complain_histories.id_complain' => $complaint['id']
                            ]);
                            $hasConclusionStatus = in_array(ComplainModel::STATUS_CONCLUSION, array_column($complainHistories, 'status'));
                            if ($complaint['status'] == ComplainModel::STATUS_PROCESSED && $complaint['status_investigation'] == ComplainModel::STATUS_APPROVE && !$hasConclusionStatus) {
                                $chatMessage .= "AWAITING : *CSO* (for investigation conclusion)";
                            } else if ($complaint['status'] == ComplainModel::STATUS_DISPROVE) {
                                $chatMessage .= "AWAITING : *{$complaint['department']}* (for disprove response)";
                            } else if ($complaint['status'] == ComplainModel::STATUS_PROCESSED && $complaint['status_investigation'] == ComplainModel::STATUS_APPROVE && $hasConclusionStatus) {
                                $chatMessage .= "AWAITING : *CSO* (for response conclusion)";
                            }

                            $send = $this->notification->broadcast([
                                'url' => 'sendMessage',
                                'method' => 'POST',
                                'payload' => [
                                    'chatId' => detect_chat_id($complain_kpi['whatsapp_group']),
                                    'body' => $chatMessage,
                                ]
                            ], NotificationModel::TYPE_CHAT_PUSH);
                            
                            if (is_array($send) && !empty(get_if_exist($send, 'error'))) {
                                log_message('error', $send['error']);
                            } else {
                                echo "Reminder to respond the customer complaint ". $complaint['no_complain'] ." is sent to wa..." . PHP_EOL;
                            }
                        }                        
                    }
                break;
                case ComplainKpiModel::KPI_CONCLUSION_CLOSE :
                    $complainNotif = [];
                    // $complains = $this->complainModel->getByConclusionClose();
                    $complains = $this->complainModel->getByNotClose();
                    foreach ($complains as $complain){
                        // $complainHistory = $this->complainHistory->getLastConclusion($complain['id']);
                        // if(empty($complainHistory)){
                        //     continue;
                        // }
                        // $t2 = strtotime($complainHistory['created_at']);
                        $t2 = strtotime($complain['created_at']);
                        $diff = $t1 - $t2;
                        $hours = floor($diff / 3600);
                        $day = ceil($diff / (3600*24));
                        if($complain['value_type'] == 'MAJOR'){
                            if($complain_kpi['major']<=$hours){
                                $mod = $day % $complain_kpi['recur_day'];
                                if($mod == 0){
                                    $complainNotif[] = $complain;
                                }
                            }
                        }else{
                            if($complain_kpi['minor']<=$hours){
                                $mod = $day % $complain_kpi['recur_day'];
                                if($complain_kpi['recur_day']>= $day || $mod == 0){
                                    $complainNotif[] = $complain;
                                }
                            } 
                        }
                    }
                    if(!empty($complainNotif)){
                        foreach($complainNotif as $complaint){
                            $customer_data = $this->people->getById($complaint['id_customer']);
                            $complain_category = $this->complainCategoryModel->getById($complaint['id_complain_category']);
                            $chatMessage = "*Reminder to Close The Complaint ". $complaint['no_complain'] ."*\n\n";
                            $chatMessage .= "Recognizing your very busy schedule, I am sending you this note as a reminder to check and respond the customer complaint immediately.\n\n";
                            $chatMessage = $chatMessage."Code       : *" . $complaint['no_complain'] . "*\n";
                            $chatMessage = $chatMessage."Category   : *" . $complain_category['category'] . " (" .$complain_category['value_type'].")". "*\n";
                            $chatMessage = $chatMessage."Customer   : *" . $customer_data['name'] . "*\n";
                            $chatMessage = $chatMessage."Department : *" . $complaint['department'] . "*\n";
                            $chatMessage = $chatMessage."Date       : *" . format_date($complaint['complain_date'],"H:i:s d F Y") . "*\n";
                            $chatMessage = $chatMessage."Complaint  : *" . $complaint['complain'] . "*\n";
                            $chatMessage = $chatMessage."Status Investigation     : *" . $complaint['status_investigation'] . "*\n";
                            $chatMessage = $chatMessage."Status Complain     : *" . $complaint['status'] . "*\n\n";

                            if ($complaint['status'] == ComplainModel::STATUS_ACCEPTED) {
                                $chatMessage .= "AWAITING : *CSO* (for set final category conclusion)";
                            } else if ($complaint['status'] == ComplainModel::STATUS_CONCLUSION) {
                                $chatMessage .= "AWAITING : *Customer* (for conclusion approval)";
                            } else if ($complaint['status'] == ComplainModel::STATUS_FINAL) {
                                $lastFinal = $this->complainHistory->getBy([
                                    'complain_histories.id_complain' => $complaint['id'],
                                    'complain_histories.status' => ComplainModel::STATUS_FINAL,
                                ], true);
                                $responseWaitingTime = $this->complainKpi->getBy(['kpi' => ComplainKpiModel::KPI_RESPONSE_WAITING_TIME], true);
                                $waitingHours = $responseWaitingTime[strtolower($complaint['value_type'])];
                                $waitingInterval = date_interval_create_from_date_string("+{$waitingHours} hours");
                                $maxDateWaiting = date_create($lastFinal['created_at'])->add($waitingInterval)->format('Y-m-d H:i');

                                $chatMessage .= "AWAITING : *CSO* (waiting response until {$maxDateWaiting} then final category conclusion)";
                            } else if ($complaint['status'] == ComplainModel::STATUS_FINAL_CONCLUSION) {
                                $chatMessage .= "AWAITING : *CSO* (for closing)";
                            }

                            $send = $this->notification->broadcast([
                                'url' => 'sendMessage',
                                'method' => 'POST',
                                'payload' => [
                                    'chatId' => detect_chat_id($complain_kpi['whatsapp_group']),
                                    'body' => $chatMessage,
                                ]
                            ], NotificationModel::TYPE_CHAT_PUSH);
                            
                            if (is_array($send) && !empty(get_if_exist($send, 'error'))) {
                                log_message('error', $send['error']);
                            } else {
                                echo "Reminder to respond the customer complaint ". $complaint['no_complain'] ." is sent to wa..." . PHP_EOL;
                            }
                        }                        
                    }
                break;
            }
        }
        //tes battery
        $data = [
            'url' => 'me',
            'method' => 'GET',
            'payload' => [],
        ];
        $me = $this->notification->broadcast($data, NotificationModel::TYPE_CHAT_PUSH);
        if(empty($me)){
            $send = $this->notification->broadcast([
                'url' => 'sendMessage',
                'method' => 'POST',
                'payload' => [
                    'chatId' => detect_chat_id('6282326680414'),
                    'body' => 'Wa not responding',
                ]
            ], NotificationModel::TYPE_CHAT_PUSH);
        }else if($me['battery'] < 21 ){
            $send = $this->notification->broadcast([
                'url' => 'sendMessage',
                'method' => 'POST',
                'payload' => [
                    'chatId' => detect_chat_id('6282326680414'),
                    'body' => 'Battery Health = *'.$me['battery'].'*',
                ]
            ], NotificationModel::TYPE_CHAT_PUSH);
        }
        echo "send_notification_complain_kpi ended at " . date('Y-m-d H:i:s') . PHP_EOL;
    }

    /**
     * Send fleet tracking from phbid.
     */
    public function send_phbid_fleet_tracking_summary()
    {
        echo "send_phbid_fleet_tracking_summary started at " . date('Y-m-d H:i:s') . PHP_EOL;

        $this->load->model('PhBidOrderSummaryModel', 'phBidOrderSummary');
        $this->load->model('PhBidOrderContainerModel', 'phBidOrderContainer');

        $currentData = date('d F Y H:i');
        $phBidOutbound = $this->phBidOrderSummary->getOutboundProgressStats();
        $lastNotifiedAt = $this->phBidOrderContainer->getLastContainerNotification();

        $phBidOutbound['outstanding_order'] = preg_replace('/[0-9]+/', "*$0*", $phBidOutbound['outstanding_order'] ?: 0);
        $phBidOutbound['waiting_stuffing'] = preg_replace('/[0-9]+/', "*$0*", $phBidOutbound['waiting_stuffing'] ?: 0);
        $phBidOutbound['waiting_stuffing_tep_checked_in'] = preg_replace('/[0-9]+/', "*$0*", $phBidOutbound['waiting_stuffing_tep_checked_in'] ?: 0);
        $phBidOutbound['waiting_stuffing_tep_checked_out_yesterday'] = preg_replace('/[0-9]+/', "*$0*", $phBidOutbound['waiting_stuffing_tep_checked_out_yesterday'] ?: 0);
        $phBidOutbound['waiting_stuffing_tep_checked_out'] = preg_replace('/[0-9]+/', "*$0*", $phBidOutbound['waiting_stuffing_tep_checked_out'] ?: 0);
        $phBidOutbound['rm_kolam'] = preg_replace('/[0-9]+/', "*$0*", $phBidOutbound['rm_kolam'] ?: 0);
        $phBidOutbound['site_transit'] = preg_replace('/[0-9]+/', "*$0*", $phBidOutbound['site_transit'] ?: 0);
        $phBidOutbound['site_unloaded'] = preg_replace('/[0-9]+/', "*$0*", $phBidOutbound['site_unloaded'] ?: 0);

        $chatMessage = "*Outbound Recap Per {$currentData} WIB*\n";
        $chatMessage .= "1. Outstanding Order: {$phBidOutbound['outstanding_order']}\n";
        $chatMessage .= "2. Waiting Stuffing: {$phBidOutbound['waiting_stuffing']}\n";
        $chatMessage .= "3. IN PLB: {$phBidOutbound['waiting_stuffing_tep_checked_in']}\n";
        $chatMessage .= "4. Out PLB H-1: {$phBidOutbound['waiting_stuffing_tep_checked_out_yesterday']}\n";
        $chatMessage .= "5. Total PLB-RM kolam: {$phBidOutbound['waiting_stuffing_tep_checked_out']}\n";
        $chatMessage .= "6. RM Kolam: {$phBidOutbound['rm_kolam']}\n";
        $chatMessage .= "7. Site Transit: {$phBidOutbound['site_transit']}\n";
        $chatMessage .= "8. Site Unloaded: {$phBidOutbound['site_unloaded']}\n\n";
        $chatMessage .= "Note:\n";
        $chatMessage .= "*Site Unloaded* since last unloaded: " . ($lastNotifiedAt ?? '') . "\n";
        $broadcastData = [
            'url' => 'sendMessage',
            'method' => 'POST',
            'payload' => [
                'chatId' => detect_chat_id('6281232850984-1587696310'), // HEAVY EQP & QHSE
                'body' => $chatMessage,
            ]
        ];
        $result = $this->notification->broadcast($broadcastData, NotificationModel::TYPE_CHAT_PUSH);
        if ($result && ($result['sent'] ?? false)) {
            $update = $this->phBidOrderContainer->updateContainerNotification();
            if (!$update) {
                echo "failed to update notified_at" . PHP_EOL;
            }
        }

        $broadcastData['payload']['chatId'] = detect_chat_id('6281232850984-1595575413'); // Coordinator
        $this->notification->broadcast($broadcastData, NotificationModel::TYPE_CHAT_PUSH);

        echo "send_phbid_fleet_tracking_summary ended at " . date('Y-m-d H:i:s') . PHP_EOL;
    }

    /**
     * Send fleet tracking goods.
     */
    public function send_phbid_fleet_tracking_goods()
    {
        echo "send_phbid_fleet_tracking_goods started at " . date('Y-m-d H:i:s') . PHP_EOL;

        $this->load->model('PhBidOrderSummaryModel', 'phBidOrderSummary');
        $this->load->model('SafeConductGoodsModel', 'safeConductGoods');
        $this->load->model('DeliveryInspectionModel', 'deliveryInspection');
        $this->load->model('DeliveryInspectionDetailModel', 'deliveryInspectionDetail');

        $currentDate = date('d F Y H:i');
        $siteTransits = $this->phBidOrderSummary->getOrderContainerPaging([
            'status' => 'site-transit'
        ]);
        $currentSiteTransits = [];

        $chatMessage = "*Site Transit At {$currentDate} WIB*\n\n";
        foreach ($siteTransits as $index => $siteTransit) {
            // check existing vehicle
            $existingInspectionDetail = $this->deliveryInspectionDetail->getBy([
                'delivery_inspections.is_deleted' => false,
                'delivery_inspection_details.id_tep' => $siteTransit['id_tep']
            ]);
            if (empty($existingInspectionDetail)) {
                $currentSiteTransits[] = $siteTransit;
                $isFoundBefore = false;
            } else {
                $isFoundBefore = true;
            }

            // build list item
            $no = str_pad($index + 1, 2, "0", STR_PAD_LEFT);
            $chatMessage .= "{$no}.  {$siteTransit['nomor_kontainer']} (" . ($isFoundBefore ? 'OLD' : 'NEW') . ")\n";
            $safeConducts = $this->safeConduct->getBy([
                'safe_conducts.id_transporter_entry_permit' => $siteTransit['id_tep']
            ]);
            foreach ($safeConducts as $safeConduct) {
                $safeConductGoods = $this->safeConductGoods->getBy([
                    'safe_conduct_goods.id_safe_conduct' => $safeConduct['id']
                ]);

                foreach ($safeConductGoods as $safeConductItem) {
                    $chatMessage .= "       - " . if_empty($safeConductItem['whey_number'], $safeConductItem['no_goods']) . "\n";
                }
            }
            $chatMessage .= "\n";
        }
        if (empty($siteTransits)) {
            $chatMessage .= "No vehicles in Site Transit";
        }

        $this->notification->broadcast([
            'url' => 'sendMessage',
            'method' => 'POST',
            'payload' => [
                'chatId' => detect_chat_id('6281232850984-1587696310'), // HEAVY EQP & QHSE
                'body' => $chatMessage,
            ]
        ], NotificationModel::TYPE_CHAT_PUSH);

        // capture site transit into delivery inspection
        $this->db->trans_start();

        $this->deliveryInspection->create([
            'id_branch' => 8, // hard-coded MEDAN 2
            'date' => format_date($currentDate, 'Y-m-d'),
            'total_vehicle' => count($currentSiteTransits)
        ]);
        $deliveryInspectionId = $this->db->insert_id();
        foreach ($currentSiteTransits as $siteTransit) {
            $this->deliveryInspectionDetail->create([
                'id_delivery_inspection' => $deliveryInspectionId,
                'id_tep' => $siteTransit['id_tep'],
                'tep_code' => $siteTransit['tep_code'],
                'no_order' => $siteTransit['nomor_order'],
                'no_vehicle' => $siteTransit['nomor_kontainer'],
                'vehicle_type' => $siteTransit['vehicle'],
            ]);
        }
        $this->db->trans_complete();

        echo "send_phbid_fleet_tracking_goods ended at " . date('Y-m-d H:i:s') . PHP_EOL;
    }

    /**
     * Auto link tep phbid feet with tep
     */
    public function auto_link_tep_phbid_fleet()
    {
        echo "auto_link_tep_phbid_fleet started at " . date('Y-m-d H:i:s') . PHP_EOL;

        $this->load->model('PhBidOrderSummaryModel', 'phBidOrderSummary');
        $this->load->model('TransporterEntryPermitTrackingModel', 'transporterEntryPermitTracking');

        $unlinkedTep = $this->transporterEntryPermit->getOutstandingTep([
            'category' => 'OUTBOUND',
            'should_checked_in' => true,
            'outstanding_tracking_link' => true,
            'branch' => 8,
        ]);

        foreach ($unlinkedTep as $tep) {
            // can return multiple when matching many TEP (same vehicle multi trips)
            $orderContainer = $this->phBidOrderSummary->getOrderContainerTransactions([
                'outstanding_tracking_link' => true,
                'no_plat_matching' => $tep['receiver_no_police'],
                'checked_in_matching' => $tep['checked_in_at']
            ])
                ->get()
                ->row_array(); // result_array() then check which fleet is correct

            $this->db->trans_start();

            if (!empty($orderContainer)) {
                echo "auto linked {$orderContainer['nomor_kontainer']} with {$tep['receiver_no_police']}" . PHP_EOL;
                $this->transporterEntryPermitTracking->create([
                    'id_tep' => $tep['id'],
                    'id_phbid_tracking' => $orderContainer['id'],
                    'id_phbid_reference' => $orderContainer['id_reference'],
                    'phbid_no_vehicle' => $orderContainer['nomor_kontainer'],
                    'phbid_no_order' => $orderContainer['nomor_order'],
                    'status' => TransporterEntryPermitTrackingModel::STATUS_LINKED,
                    'description' => "Auto linked",
                ]);
                $tepTrackingId = $this->db->insert_id();

                $this->statusHistory->create([
                    'type' => StatusHistoryModel::TYPE_TEP_TRACKING,
                    'id_reference' => $tepTrackingId,
                    'status' => TransporterEntryPermitTrackingModel::STATUS_LINKED,
                    'description' => "TEP {$tep['tep_code']} auto linked with {$orderContainer['nomor_kontainer']}",
                    'data' => json_encode([
                        'id_phbid_tracking' => $orderContainer['id'],
                        'id_phbid_reference' => $orderContainer['id_reference'],
                        'phbid_no_vehicle' => $orderContainer['nomor_kontainer'],
                        'phbid_no_order' => $orderContainer['nomor_order'],
                    ])
                ]);
            }

            $this->db->trans_complete();
        }
        echo "auto_link_tep_phbid_fleet ended at " . date('Y-m-d H:i:s') . PHP_EOL;
    }

    /**
     * Reminder booking outstanding.
     */
    public function send_reminder_booking_outstanding()
    {
        echo "send_reminder_booking_outstanding started at " . date('Y-m-d H:i:s') . PHP_EOL;

        $outstandingBookings = $this->upload->getOutstandingBooking();

        if (empty($outstandingBookings)) {
            echo "no outstanding booking available" . PHP_EOL;
        } else {
            $chatMessage = "📋 *Outstanding Booking:*\n";
            $chatMessage .= "———————————————\n";
            foreach ($outstandingBookings as $index => $outstandingBooking) {
                $no = str_pad($index + 1, 2, "0", STR_PAD_LEFT);
                $chatMessage .= "{$no}.  *No Upload:* {$outstandingBooking['no_upload']}\n";
                $chatMessage .= "       *Customer:* " . $outstandingBooking['customer_name'] . "\n";
                $chatMessage .= "       *Description:* " . substr($outstandingBooking['description'], -5) . "\n";
                $chatMessage .= "       *Category:* {$outstandingBooking['category']}\n";
                $chatMessage .= "       *SPPB Date:* {$outstandingBooking['sppb_date']}\n";
                $chatMessage .= "       *Branch:* {$outstandingBooking['branch']}\n";
                $chatMessage .= "       *Status:* " . if_empty($outstandingBooking['booking_status'], $outstandingBooking['upload_status']) . "\n";
                $chatMessage .= "       *Age:* " . if_empty($outstandingBooking['age'], 0) . "\n";
                $chatMessage .= "\n";
            }
            $result = $this->notification->broadcast([
                'url' => 'sendMessage',
                'method' => 'POST',
                'payload' => [
                    'chatId' => detect_chat_id('6281232850984-1595575413'), // OPS coordinator
                    'body' => $chatMessage,
                ]
            ], NotificationModel::TYPE_CHAT_PUSH);

            if ($result && !($result['sent'] ?? false)) {
                echo "failed to send reminder outstanding booking" . PHP_EOL;
            }
        }

        echo "send_reminder_booking_outstanding ended at " . date('Y-m-d H:i:s') . PHP_EOL;
    }

    /**
     * Send daily operational production.
     *
     * Medan all day
     * S1 : 07:01 - 19:00 Kirim 19:00
     * S2 : 19:01 - 07:00 Kirim 07:00
     *
     * Jakarta Minggu Libur
     * S1 : 00:01 - 14:00 Kirim 14:00
     * S2 : 14:01 - 00:00 Kirim 00:00
     *
     * Surabaya  Minggu Libur
     * 19:01 - 19:00 Kirim 19:00
     *
     * @param $branch
     * @param $shift
     */
    public function daily_operational_production()
    {
        echo "daily_operational_production started at " . date('Y-m-d H:i:s') . PHP_EOL;

        $this->load->model('ReportVehicleProductionModel', 'reportVehicleProduction');
        $operationCutOff = $this->operationCutOff->getAllByGrouping([
            'status' => OperationCutOffModel::STATUS_ACTIVE,
            'is_send' => 1,
        ]);

        $dateNow = date('Y-m-d');
        $timeNow = date('Y-m-d H:i');
        $dateTemp = $dateNow;
        foreach($operationCutOff AS $cutOff){
            $dateNow = $dateTemp;
            $timeStart = $dateNow.' '.$cutOff['start'];
            $timeEnd = $dateNow.' '.$cutOff['end'];
            if(strtotime($timeStart) > strtotime($timeEnd)){
                $timeStart = date('Y-m-d H:i:s',strtotime("-1 day",strtotime($timeStart)));
                $dateNow = date('Y-m-d',strtotime("-1 day",strtotime($dateNow)));
            }else{
                $timeStart = date('Y-m-d H:i:s',strtotime($timeStart));
            }

            $timeEnd = date('Y-m-d H:i:s',strtotime($timeEnd));
            $timeEndCheck = date('Y-m-d H:i',strtotime("+1 minutes",strtotime($timeEnd)));
            if($timeNow == $timeEndCheck){
                $dateFrom = $timeStart;
                $dateTo = $timeEnd;
                $shiftId = $cutOff['id'];

                echo "daily_operational_production {$cutOff['branch']} - {$cutOff['shift']} from {$dateFrom} to {$dateTo}" . PHP_EOL;

                $schedules = $this->operationCutOffSchedule->getBy([
                    'id_operation_cut_off' => $shiftId
                ]);
                $scheduleIds = [];
                $scheduleIds = array_column($schedules, 'id_schedule');

                $reports = $this->reportVehicleProduction->getDailyOperationalProduction([
                    'branch' => explode(",",$cutOff['id_branch_group']),
                    'checked_out_from' => $dateFrom,
                    'checked_out_to' => $dateTo
                ]);

                $heavyEquipment = $this->heavyEquipment->getAll([
                    'branch' => explode(",",$cutOff['id_branch_group']),
                ]);
                $heeps = $this->heavyEquipmentEntryPermit->getHEEPByOperationalProduction([
                    'branch' => explode(",",$cutOff['id_branch_group']),
                    'checked_in_from' => $dateFrom,
                    'checked_in_to' => $dateTo
                ]);

                //dateTo for attendance
                $dateToAtt = $dateTo;
                if(format_date($dateFrom,'Y-m-d') != format_date($dateTo,'Y-m-d')){
                    $dateToAtt = format_date($dateFrom,'Y-m-d 23:59:59');
                }

                $strStart = strtotime($cutOff['start']);
                $strEnd = strtotime($cutOff['end']);
                if(($strStart - $strEnd) == 60 && $cutOff['end'] > '12:00:00'){//jika cuma 1 shift dan ngirim diatas jam 12
                    $schedule_date_time = $dateTo;
                }else{
                    $schedule_date_time = $dateFrom;
                }
                $attendances = $this->attendance->getAttendanceByDepartment([
                    // 'branch' => $branches[$branch]['work_location'],
                    'department' => 1,
                    'is_check_in' => true,
                    // 'schedule_from' => $dateFrom,
                    // 'schedule_to' => ($branch == 'Surabaya') ? $dateTo : $dateToAtt,
                    'cloud_id' => $cutOff['cloud_id'],
                    'id_schedule' => $scheduleIds,
                    'schedule_date' => $schedule_date_time,
                ]);
                $attendaceEmployeeId = array_column($attendances,'id_employee');

                $overtimes = $this->attendance->getOvertimeByDepartment([
                    // 'branch' => $branches[$branch]['work_location'],
                    'department' => 1,
                    'overtime_from' => (($strStart - $strEnd) == 60 && $cutOff['end'] > '12:00:00') ? date('Y-m-d', strtotime("+1 day", strtotime($dateFrom))) : $dateFrom,
                    'overtime_to' => (($strStart - $strEnd) == 60 && $cutOff['end'] > '12:00:00') ? date('Y-m-d', strtotime("+1 day", strtotime($dateTo))) : $dateTo,
                    'cloud_id' => $cutOff['cloud_id'],
                ]);
                $overtimeFix = [];
                foreach($overtimes as $overtime){
                    if(!in_array($overtime['id_employee'], $attendaceEmployeeId)){
                        $overtime['check_in'] = '';
                        $overtime['check_out'] = '';
                        $workEnd = format_date($overtime['overtime_start'],'Y-m-d').' '.$overtime['work_end'];
                        $workStart = format_date($overtime['overtime_start'],'Y-m-d').' '.$overtime['work_start'];
                        if($workStart > $workEnd){
                            $workEnd = date('Y-m-d H:i', strtotime($workEnd . ' +1 day'));
                        }
                        $attendance = $this->attendance->getBy([
                            'attendances.id_employee' => $overtime['id_employee'],
                            'attendances.attendance_owner' => 'EMPLOYEE',
                            'attendances.off_duty' => $workEnd
                        ],true);
                        $overtime['check_in'] = $attendance['check_in'];
                        $overtime['check_out'] = $attendance['check_out'];
                        $overtimeFix[] = $overtime;
                    }
                }

                $attendanceLabors = $this->attendance->getAttendanceByLabour([
                    'cloud_id' => $cutOff['cloud_id'],
                    'is_check_in' => true,
                    // 'schedule_from' => $dateFrom,
                    // 'schedule_to' => ($branch == 'Surabaya') ? $dateTo : $dateToAtt
                    'id_schedule' => $scheduleIds,
                    'schedule_date' => (($strStart - $strEnd) == 60 && $cutOff['end'] > '12:00:00') ? $dateTo : $dateFrom,
                ]);

                $heepName = [];
                $heavy = [];
                if(!empty($heeps)){
                    $heepName = array_column($heeps,'item_name');
                }
                if(!empty($heavyEquipment)){
                    $heavy = array_column($heavyEquipment,'name');
                }
                $heavyName = array_merge($heavy,$heepName);
                $branch = $cutOff['branch'];
                $shift = 's'.$cutOff['shift'];

                $page = $this->load->view('report_production/print_vehicle_summary', compact('reports', 'branch', 'dateFrom', 'dateTo', 'shift', 'heavyName', 'attendances', 'attendanceLabors', 'overtimeFix'), true);
                $pdf = $this->exporter->exportToPdf('Vehicle Daily Production', $page, ['buffer' => true]);
                if(($strStart - $strEnd) == 60 && $cutOff['end'] > '12:00:00'){
                    $pdfFileName = url_title($cutOff['branch'] . ' - ' . strtoupper($shift) . ' -  PROD - ' . format_date($dateTo, 'Ymd')) . '.pdf';
                }else{
                    $pdfFileName = url_title($cutOff['branch'] . ' - ' . strtoupper($shift) . ' -  PROD - ' . format_date($dateFrom, 'Ymd')) . '.pdf';
                }
                file_put_contents(FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . $pdfFileName, $pdf);

                $message = "*Daily Operational Production*\n";
                $message .= "*BRANCH*: {$cutOff['branch']}\n";
                $message .= "*DATE FROM*: {$dateFrom}\n";
                $message .= "*DATE TO*: {$dateTo}\n";
                $result = $this->notification->broadcast([
                    'url' => 'sendFile',
                    'method' => 'POST',
                    'payload' => [
                        'chatId' => detect_chat_id($cutOff['whatsapp_group']),
                        'body' => base_url('uploads/temp/' . $pdfFileName),
                        'filename' => $pdfFileName,
                        'caption' => $message
                    ]
                ], NotificationModel::TYPE_CHAT_PUSH);

                if ($result && !($result['sent'] ?? false)) {
                    echo "failed to send daily_operational_production" . PHP_EOL;
                }
            }
        }

        echo "daily_operational_production ended at " . date('Y-m-d H:i:s') . PHP_EOL;
    }

    /**
     * Send daily operational production.
     *
     * Medan all day
     * S1 : 07:01 - 19:00 Kirim 19:00
     * S2 : 19:01 - 07:00 Kirim 07:00
     *
     * Jakarta Minggu Libur
     * S1 : 00:01 - 14:00 Kirim 14:00
     * S2 : 14:01 - 00:00 Kirim 00:00
     *
     * Surabaya  Minggu Libur
     * 19:01 - 19:00 Kirim 19:00
     *
     * @param $branch
     * @param $shift
     */
    public function daily_operational_production_test($branch, $shift)
    {
        echo "daily_operational_production started at " . date('Y-m-d H:i:s') . PHP_EOL;

        $this->load->model('ReportVehicleProductionModel', 'reportVehicleProduction');

        $yesterdayTime = strtotime('-1 day');
        $branches = [
            'Medan' => [
                'alias' => 'MDN',
                'id_branch' => 2,
                'days' => [1, 2, 3, 4, 5, 6, 7], // monday - sunday
                'schedules' => [
                    's1' => ['from' => date('Y-m-d 07:01:00'), 'to' => date('Y-m-d 19:00:59'), 'id' => 3],
                    's2' => ['from' => date('Y-m-d 19:01:00', $yesterdayTime), 'to' => date('Y-m-d 07:00:59'), 'id' => 4],
                ],
                'notification_group' => '6281232850984-1595575413',
                'work_location' => 3,
                'cloud_id' => 'C2696422DF323437',
            ],
            'Jakarta' => [
                'alias' => 'JKT',
                'id_branch' => 1,
                'days' => [1, 2, 3, 4, 5, 6], // monday - saturday
                'schedules' => [
                    's1' => ['from' => date('Y-m-d 00:01:00'), 'to' => date('Y-m-d 14:00:59'), 'id' => 1],
                    's2' => ['from' => date('Y-m-d 14:01:00', $yesterdayTime), 'to' => date('Y-m-d 00:00:59'), 'id' => 2],
                ],
                'notification_group' => '6281232850984-1595575413',
                'work_location' => 1,
                'cloud_id' => 'C269248053291121',
            ],
            'Surabaya' => [
                'alias' => 'SBY',
                'id_branch' => [5, 15],
                'days' => [1, 2, 3, 4, 5, 6], // monday - saturday
                'schedules' => [
                    's1' => ['from' => date('Y-m-d 19:01:00', $yesterdayTime), 'to' => date('Y-m-d 19:00:59'), 'id' => 7],
                ],
                'notification_group' => '6281232850984-1595575413',
                'work_location' => 2,
                'cloud_id' => 'C269248053181321',
            ]
        ];

        $shiftData = $branches[$branch]['schedules'][$shift];
        $dateFrom = $shiftData['from'];
        $dateTo = $shiftData['to'];
        $shiftId = $shiftData['id'];

        echo "daily_operational_production {$branch} - {$shift} from {$dateFrom} to {$dateTo}" . PHP_EOL;

        $schedules = $this->operationCutOffSchedule->getBy([
            'id_operation_cut_off' => $shiftId
        ]);
        $scheduleIds = [];
        $scheduleIds = array_column($schedules, 'id_schedule');

        $reports = $this->reportVehicleProduction->getDailyOperationalProduction([
            'branch' => $branches[$branch]['id_branch'],
            'checked_out_from' => $dateFrom,
            'checked_out_to' => $dateTo
        ]);

        $heavyEquipment = $this->heavyEquipment->getAll([
            'branch' => $branches[$branch]['id_branch'],
        ]);
        $heeps = $this->heavyEquipmentEntryPermit->getHEEPByOperationalProduction([
            'branch' => $branches[$branch]['id_branch'],
            'checked_in_from' => $dateFrom,
            'checked_in_to' => $dateTo
        ]);

        //dateTo for attendance
        $dateToAtt = $dateTo;
        if(format_date($dateFrom,'Y-m-d') != format_date($dateTo,'Y-m-d')){
            $dateToAtt = format_date($dateFrom,'Y-m-d 23:59:59');
        }

        $attendances = $this->attendance->getAttendanceByDepartment([
            // 'branch' => $branches[$branch]['work_location'],
            'department' => 1,
            'is_check_in' => true,
            // 'schedule_from' => $dateFrom,
            // 'schedule_to' => ($branch == 'Surabaya') ? $dateTo : $dateToAtt,
            'cloud_id' => $branches[$branch]['cloud_id'],
            'id_schedule' => $scheduleIds,
            'schedule_date' => ($branch == 'Surabaya') ? $dateTo : $dateFrom,
        ]);
        $attendaceEmployeeId = array_column($attendances,'id_employee');

        $overtimes = $this->attendance->getOvertimeByDepartment([
            // 'branch' => $branches[$branch]['work_location'],
            'department' => 1,
            'overtime_from' => ($branch == 'Surabaya') ? date('Y-m-d', strtotime("+1 day", strtotime($dateFrom))) : $dateFrom,
            'overtime_to' => ($branch == 'Surabaya') ? date('Y-m-d', strtotime("+1 day", strtotime($dateTo))) : $dateTo,
            'cloud_id' => $branches[$branch]['cloud_id'],
        ]);
        $overtimeFix = [];
        foreach($overtimes as $overtime){
            if(!in_array($overtime['id_employee'], $attendaceEmployeeId)){
                $overtime['check_in'] = '';
                $overtime['check_out'] = '';
                $workEnd = format_date($overtime['overtime_start'],'Y-m-d').' '.$overtime['work_end'];
                $workStart = format_date($overtime['overtime_start'],'Y-m-d').' '.$overtime['work_start'];
                if($workStart > $workEnd){
                    $workEnd = date('Y-m-d H:i', strtotime($workEnd . ' +1 day'));
                }
                $attendance = $this->attendance->getBy([
                    'attendances.id_employee' => $overtime['id_employee'],
                    'attendances.attendance_owner' => 'EMPLOYEE',
                    'attendances.off_duty' => $workEnd
                ],true);
                $overtime['check_in'] = $attendance['check_in'];
                $overtime['check_out'] = $attendance['check_out'];
                $overtimeFix[] = $overtime;
            }
        }

        $attendanceLabors = $this->attendance->getAttendanceByLabour([
            'branch' => $branches[$branch]['work_location'],
            'is_check_in' => true,
            // 'schedule_from' => $dateFrom,
            // 'schedule_to' => ($branch == 'Surabaya') ? $dateTo : $dateToAtt
            'id_schedule' => $scheduleIds,
            'schedule_date' => ($branch == 'Surabaya') ? $dateTo : $dateFrom,
        ]);

        $heepName = [];
        $heavy = [];
        if(!empty($heeps)){
            $heepName = array_column($heeps,'item_name');
        }
        if(!empty($heavyEquipment)){
            $heavy = array_column($heavyEquipment,'name');
        }
        $heavyName = array_merge($heavy,$heepName);

        $page = $this->load->view('report_production/print_vehicle_summary', compact('reports', 'branch', 'dateFrom', 'dateTo', 'shift', 'heavyName', 'attendances', 'attendanceLabors', 'overtimeFix'), true);
        $pdf = $this->exporter->exportToPdf('Vehicle Daily Production', $page, ['buffer' => true]);
        if($branch == 'Surabaya'){
            $pdfFileName = url_title($branches[$branch]['alias'] . ' - ' . strtoupper($shift) . ' -  PROD - ' . format_date($dateTo, 'Ymd')) . '.pdf';
        }else{
            $pdfFileName = url_title($branches[$branch]['alias'] . ' - ' . strtoupper($shift) . ' -  PROD - ' . format_date($dateFrom, 'Ymd')) . '.pdf';
        }
        file_put_contents(FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'temp' . DIRECTORY_SEPARATOR . $pdfFileName, $pdf);

        $message = "*Daily Operational Production*\n";
        $message .= "*BRANCH*: {$branch}\n";
        $message .= "*DATE FROM*: {$dateFrom}\n";
        $message .= "*DATE TO*: {$dateTo}\n";
        $result = $this->notification->broadcast([
            'url' => 'sendFile',
            'method' => 'POST',
            'payload' => [
                'chatId' => detect_chat_id($branches[$branch]['notification_group']),
                'body' => base_url('uploads/temp/' . $pdfFileName),
                'filename' => $pdfFileName,
                'caption' => $message
            ]
        ], NotificationModel::TYPE_CHAT_PUSH);

        if ($result && !($result['sent'] ?? false)) {
            echo "failed to send daily_operational_production" . PHP_EOL;
        }

        echo "daily_operational_production ended at " . date('Y-m-d H:i:s') . PHP_EOL;
    }

    /**
     * generate_ref_dates every year on Y-12-01
     */
    public function generate_ref_dates()
    {
        echo "generate_ref_dates started at " . date('Y-m-d H:i:s') . PHP_EOL;
        $start = date('Y-01-01',strtotime("+1 year"));
        $end = date('Y-12-31',strtotime("+1 year"));
        $this->db->trans_start();
        $dates = $this->db->select('*, YEARWEEK(date, 2) AS year_week')
                ->from("(SELECT ADDDATE('{$start}',t4.i*10000 + t3.i*1000 + t2.i*100 + t1.i*10 + t0.i) date FROM
                (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t0,
                (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t1,
                (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t2,
                (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t3,
                (SELECT 0 i UNION SELECT 1 UNION SELECT 2 UNION SELECT 3 UNION SELECT 4 UNION SELECT 5 UNION SELECT 6 UNION SELECT 7 UNION SELECT 8 UNION SELECT 9) t4) v")
                ->where("date <= '{$end}'")->get()->result_array();

        $this->db->insert_batch('ref_dates', $dates);

        $this->db->trans_complete();
        echo "generate_ref_dates ended at " . date('Y-m-d H:i:s') . PHP_EOL;
    }

    /**
     * Handover confirmation reminder
     */
    public function discrepancy_handover_confirmation_reminder()
    {
        echo "discrepancy_handover_confirmation_reminder started at " . date('Y-m-d H:i:s') . PHP_EOL;

        $this->load->model('DiscrepancyHandoverModel', 'discrepancyHandover');

        $outstandingHandovers = $this->discrepancyHandover->getBy([
            'discrepancy_handovers.status' => DiscrepancyHandoverModel::STATUS_UPLOADED
        ]);
        $customerIds = array_unique(array_column($outstandingHandovers, 'id_customer'));
        foreach ($customerIds as $customerId) {
            $customer = $this->peopleModel->getById($customerId);
            $customerHandovers = array_filter($outstandingHandovers, function ($handover) use ($customerId) {
                return $handover['id_customer'] == $customerId;
            });

            $branchIds = array_unique(array_column($customerHandovers, 'id_branch'));
            foreach ($branchIds as $branchId) {
                $branch = $this->branchModel->getById($branchId);
                $peopleBranch = $this->peopleBranchModel->getBy([
                    'id_customer' => $customerId,
                    'id_branch' => $branchId
                ], true);

                if (!empty($peopleBranch['whatsapp_group'])) {
                    $customerBranchHandovers = array_filter($customerHandovers, function ($handover) use ($branchId) {
                        return $handover['id_branch'] == $branchId;
                    });

                    $message = "*DISCREPANCY CONFIRMATION REMINDER*\n";
                    $message .= "————————————————\n";
                    $message .= "*Customer:* {$customer['name']}\n";
                    $message .= "*Branch:* {$branch['branch']}\n";

                    foreach (array_values($customerBranchHandovers) as $index => $customerBranchHandover) {
                        $attachmentUrl = asset_url($customerBranchHandover['attachment']);
                        $no = str_pad($index + 1, 2, "0", STR_PAD_LEFT);
                        $message .= "\n";
                        $message .= "{$no}.  *No Discrepancy:* {$customerBranchHandover['no_discrepancy']}\n";
                        $message .= "       *No Reference:* {$customerBranchHandover['no_reference']}\n";
                        $message .= "       *Total Item:* {$customerBranchHandover['total_discrepancy_item']} items\n";
                        $message .= "       *Attachment:* " . if_empty(get_tiny_url($attachmentUrl), $attachmentUrl) . "\n";
                    }

                    $this->notification->broadcast([
                        'url' => 'sendMessage',
                        'method' => 'POST',
                        'payload' => [
                            'chatId' => detect_chat_id($peopleBranch['whatsapp_group']),
                            'body' => $message,
                        ]
                    ], NotificationModel::TYPE_CHAT_PUSH);

                    echo "Sent {$customer['name']} - branch {$branch['branch']} at " . date('Y-m-d H:i:s') . PHP_EOL;
                }
            }
        }

        echo "discrepancy_handover_confirmation_reminder ended at " . date('Y-m-d H:i:s') . PHP_EOL;
    }

    /*
     * Send Notification WA when oustanding booking inbound didnt completed after 2 day.
     *
     */
    public function outstanding_booking_complete() //automate every 9 oclock
    {
        $branches = $this->branchModel->getBy([
            'ref_branches.dashboard_status' => 1
        ]);
        foreach($branches as $key => $branch){
            $bookings = $this->bookingModel->getOutstandingInbound($branch['id']);
            $data_header = "*Outstanding Booking Complete Branch {$branch['branch']}*\n\n";
            $data_body = '';
            foreach($bookings as $key => $booking){
                $data_body .= ($key+1) . ". Aju Inbound : ".substr($booking['no_reference'], -4)."\n";
                $data_body .= "   ".$booking['customer_name']."\n";
                $data_body .= "   Party : ".$booking['party']."\n";
                $data_body .= "   SPPB : ".format_date($booking['sppb_upload_date'], 'd/m/Y')."\n";
                $data_body .= "   First In PLB : ".format_date($booking['first_in'], 'd/m/Y')."\n";
                $now = time(); // or your date as well
                $first_in = strtotime($booking['first_in']);
                $datediff = $now - $first_in;
                $age = round($datediff / (60 * 60 * 24));
                $data_body .= "   Age : ".$age." days \n\n";
            }
            if(!empty($bookings)){
                $whatsapp_group = $branch['whatsapp_group'];
                $data_text = $data_header.$data_body;
                $this->send_message($data_text, $whatsapp_group);
                echo "successfully sent notification to branch ". $branch['branch'] . PHP_EOL;
            }else{
                $whatsapp_group = $branch['whatsapp_group'];
                $data_text = $data_header.'No Outstanding Booking';
                $this->send_message($data_text, $whatsapp_group);
                echo "no notification sent to branch ". $branch['branch'] . PHP_EOL;
            }
        }
    }

    /**
     * Send outstanding outbound incomplete.
     */
    public function outstanding_booking_outbound_complete()
    {
        echo "outstanding_booking_outbound_complete started at " . date('Y-m-d H:i:s') . PHP_EOL;

        $branches = $this->branchModel->getBy(['ref_branches.dashboard_status' => 1]);
        foreach($branches as $branch) {
            $outstandingOutbounds = $this->bookingModel->getOutstandingOutboundComplete(['branch' => $branch['id']]);
            $chatMessage = "*Outstanding Booking Outbound Complete {$branch['branch']}:*\n";
            $chatMessage .= "————————————————————";
            foreach ($outstandingOutbounds as $index => $outstandingOutbound) {
                $no = str_pad($index + 1, 2, "0", STR_PAD_LEFT);
                $age = Carbon::now()->diffInDays(Carbon::parse(if_empty(format_date($outstandingOutbound['last_activity_date'], 'Y-m-d'), null)));
                $chatMessage .= "\n{$no}.  *No Reference:* " . substr($outstandingOutbound['no_reference'], -4) . "\n";
                $chatMessage .= "       *Customer:* " . $outstandingOutbound['customer_name'] . "\n";
                $chatMessage .= "       *SPPB Date:* {$outstandingOutbound['sppb_upload_date']}\n";
                $chatMessage .= "       *Last Outbound Activity:* {$outstandingOutbound['last_activity_date']}\n";
                $chatMessage .= "       *Age:* {$age} day(s)\n";
            }
            if (empty($outstandingOutbounds)) {
                $chatMessage .= "\nNo data outstanding outbound complete";
            }
            $result = $this->notification->broadcast([
                'url' => 'sendMessage',
                'method' => 'POST',
                'payload' => [
                    'chatId' => detect_chat_id($branch['whatsapp_group']),
                    'body' => $chatMessage,
                ]
            ], NotificationModel::TYPE_CHAT_PUSH);

            if ($result && !($result['sent'] ?? false)) {
                echo "failed to send reminder outstanding booking outbound complete" . PHP_EOL;
            }
        }
        echo "outstanding_booking_outbound_complete ended at " . date('Y-m-d H:i:s') . PHP_EOL;
    }
        
    /**
     * Generate put away audit.
     */
    public function put_away_generate(){
        echo "put_away_generate started at " . date('Y-m-d H:i:s') . PHP_EOL;
        $dateNow = date('Y-m-d');
        $timeNow = date('Y-m-d H:i');
        $dateTemp = $dateNow;
        $operationCutOff = $this->operationCutOff->getAll(['status' => OperationCutOffModel::STATUS_ACTIVE]);

        foreach($operationCutOff AS $cutOff){
            $dateNow = $dateTemp;
            $timeStart = $dateNow.' '.$cutOff['start'];
            $timeEnd = $dateNow.' '.$cutOff['end'];
            if(strtotime($timeStart) > strtotime($timeEnd)){
                $timeStart = date('Y-m-d H:i:s',strtotime("-1 day",strtotime($timeStart)));
                $dateNow = date('Y-m-d',strtotime("-1 day",strtotime($dateNow)));
            }else{
                $timeStart = date('Y-m-d H:i:s',strtotime($timeStart));
            }
            $timeEnd = date('Y-m-d H:i:s',strtotime($timeEnd));
            $timeEndCheck = date('Y-m-d H:i',strtotime("+1 minutes",strtotime($timeEnd)));
            if($timeNow == $timeEndCheck){
                $pendingPutAway = $this->putAway->getBy([
                    'put_away.status' => PutAwayModel::STATUS_PENDING,
                    'put_away.id_branch' => $cutOff['id_branch'],
                ]);
                foreach($pendingPutAway AS $pending){
                    $this->putAway->update([
                        'status' => PutAwayModel::STATUS_NOT_PROCESSED,
                    ],$pending['id']);
                }
                $goodsInbounds = $this->workOrderGoods->getWorkOrderGoodsByRangeTime($timeStart, $timeEnd, $cutOff['id_branch']);
                $checkPutAway = $this->putAway->putAwayCheck($cutOff['id_branch'], $cutOff['id'], $dateNow);

                $this->db->trans_start();
                if(empty($checkPutAway)){
                    $noPutAway = $this->putAway->getAutoNumberPutAway();
                    $this->putAway->create([
                        'id_branch' => $cutOff['id_branch'],
                        'no_put_away' => $noPutAway,
                        'put_away_date' =>  $dateNow,
                        'start' =>  $cutOff['start'],
                        'end' =>  $cutOff['end'],
                        'id_shift' => $cutOff['id'],
                        'description' => 'Auto Generate',
                        'status' => !empty($goodsInbounds) ? 'PENDING' : PutAwayModel::STATUS_VALIDATED
                    ]);
                    $putAwayID = $this->db->insert_id();

                    if(!empty($goodsInbounds)){
                        foreach ($goodsInbounds as $goods){
                            $this->putAwayGoods->create([
                                'id_put_away' => $putAwayID,
                                'id_owner' => $goods['id_owner'],
                                'id_booking' => $goods['id_booking'],
                                'id_goods' => $goods['id_goods'],
                                'id_unit' => $goods['id_unit'],
                                'id_position' => if_empty($goods['id_position'], null),
                                'id_position_blocks' => $goods['id_position_blocks'],
                                'id_work_order' => $goods['id_work_order'],
                                'id_work_order_goods' => $goods['id'],
                                'position_block' => $goods['position_blocks'],
                                'no_pallet' => $goods['no_pallet'],
                                'ex_no_container' => $goods['no_container'],
                                'quantity' => $goods['quantity'],
                                'tonnage' => $goods['total_weight'],
                                'tonnage_gross' => $goods['total_gross_weight'],
                                'volume' => $goods['total_volume'],
                                'status_danger' => $goods['status_danger'],
                                'description' => $goods['description'],
                            ]);
                        }
                    }
                    echo "Put away audit successfully created on shift".$cutOff['shift'] ." branch ". $cutOff['branch']. PHP_EOL;
                }else{
                    echo "Put away audit failed, Put away audit is already exists! on shift".$cutOff['shift'] ." branch ". $cutOff['branch']. PHP_EOL;
                }

                $this->db->trans_complete();
            }
        }

        echo "put_away_generate end at " . date('Y-m-d H:i:s') . PHP_EOL;
    }
}
