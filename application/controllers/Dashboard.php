<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Dashboard
 * @property PeopleModel $people
 * @property ReportModel $report
 * @property ReportStockModel $reportStock
 * @property ReportPerformanceModel $reportPerformance
 * @property ReportStorageModel $reportStorage
 * @property NewsModel $news
 * @property DashboardModel $dashboard
 * @property BookingModel $booking
 * @property WorkOrderModel $workOrder
 * @property WorkOrderGoodsModel $workOrderGoods
 * @property WorkOrderDocumentModel $workOrderDocument
 * @property WorkOrderDocumentFileModel $workOrderDocumentFile
 * @property CustomerStorageCapacityModel $customerStorageCapacity
 * @property TransporterEntryPermitModel $transporterEntryPermit
 * @property GuestModel $guest
 */
class Dashboard extends MY_Controller
{
    /**
     * Dashboard constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('PeopleModel', 'people');
        $this->load->model('ReportModel', 'report');
        $this->load->model('ReportStockModel', 'reportStock');
        $this->load->model('ReportPerformanceModel', 'reportPerformance');
        $this->load->model('ReportStorageModel', 'reportStorage');
        $this->load->model('NewsModel', 'news');
        $this->load->model('DashboardModel', 'dashboard');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('GuestModel', 'guest');
        $this->load->model('CustomerStorageCapacityModel', 'customerStorageCapacity');
        $this->load->driver('cache', ['adapter' => 'file']);

        $this->setFilterMethods([
            'ajax_get_stock_summary' => 'GET',
            'ajax_get_handling_summary' => 'GET',
            'space_usage_summary' => 'GET',
            'dedicated_space' => 'GET',
            'non_dedicated_space' => 'GET',
            'non_dedicated_space_detail' => 'GET',
            'monitoring' => 'GET',
            'security' => 'GET',
            'transporter_in' => 'GET',
            'guest_in' => 'GET',
            'ops_performance' => 'GET',
            'ops_performance_detail' => 'GET',
            'ajax_get_week_performance_customer' => 'GET',
            'invalid_job_outbound' => 'GET',
        ]);
    }

    /**
     * Show dashboard page.
     */
    public function index()
    {
        // print_debug('masuk');
        $userData = UserModel::authenticatedUserData();

        $customerId = $userData['id_person'];
        $userType = $userData['user_type'];
        $activeBranch = get_active_branch('id');

        $cacheLabel = 'statisticData' . $activeBranch;
        if ($userType == 'EXTERNAL') {
            $cacheLabel .= $customerId;
        }

        $data = [
            'statistic' =>  [],//$this->dashboard->getStatisticSummary($customerId),
            'bookingSummaries' =>  [], //$this->dashboard->getBookingSummary($customerId),
            'handlingSummaries' => [],//$this->dashboard->getHandlingSummary($customerId),
            'stockSummaries' => []//$this->dashboard->getStockSummary($customerId),
        ];

        $statisticData = $data;

        $statisticData['news'] = $this->news->getByType([$userType, NewsModel::TYPE_PUBLIC], 5);

        $this->render('dashboard/index', $statisticData);
    }


}
