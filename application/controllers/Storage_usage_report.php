<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Storage_usage_report
 * @property CustomerStorageCapacityModel $customerStorageCapacity
 * @property PeopleModel $people
 * @property GoodsModel $goods
 * @property ReportStorageModel $reportStorage
 * @property Exporter $exporter
 */
class Storage_usage_report extends MY_Controller
{
    /**
     * Storage_usage_report constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('CustomerStorageCapacityModel', 'customerStorageCapacity');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('GoodsModel', 'goods');
        $this->load->model('ReportStorageModel', 'reportStorage');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'space_usage_summary' => 'GET',
            'space_usage_movement' => 'GET',
            'space_usage_detail' => 'GET',
            'space_usage_detail_activity' => 'GET',
        ]);
    }

    /**
     * Get space usage summary
     */
    public function space_usage_summary()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_VIEW);

        $customers = $this->customerStorageCapacity->getAll();
        $customerIds = array_unique(array_column($customers, 'id_customer'));
        //$customerStorages = [];
        $customerReportIds = [];
        foreach ($customerIds as $customerId) {
            $customer = $this->people->getById($customerId);
            if (empty($customer['id_parent'])) {
                //$balances = $this->reportStorage->getCustomerBalanceStorage([
                //    'customer' => $customerId,
                //    'customer_include_member' => true,
                //    'data' => 'all-data'
                //]);

                //if (!empty($balances)) {
                //    $customerStorages[] = $balances[0];
                //}

                $customerReportIds[] = $customerId;
            }
        }

        $customerStorages = $this->reportStorage->getCustomerBalanceStorage([
            'customer' => if_empty($customerReportIds, -1),
            'customer_include_member' => true,
            'data' => 'all-data'
        ]);

        if (get_url_param('export')) {
            $this->exporter->exportFromArray('Storage usage summary', $customerStorages);
        } else {
            $this->render('storage_usage/space_usage_summary', compact('customerStorages'), 'Space usage summary');
        }
    }

    /**
     * Get space usage detail.
     */
    public function space_usage_detail()
    {
        $customerStorageDetails = $this->reportStorage->getCustomerStorageDetail([
            'warehouse_type' => get_url_param('warehouse_type'),
            'customer' => get_url_param('customer'),
            'customer_include_member' => true,
            'data' => 'active'
        ]);

        if (get_url_param('export')) {
            $this->exporter->exportFromArray('Storage usage goods', $customerStorageDetails);
        } else {
            $this->render('storage_usage/space_usage_detail', compact('customerStorageDetails'), 'Space usage detail');
        }
    }

    /**
     * Get space usage detail activity.
     */
    public function space_usage_detail_activity()
    {
        $filters = [
            'customer' => get_url_param('customer'),
            'warehouse_type' => get_url_param('warehouse_type'),
            'booking' => get_url_param('booking'),
            'goods' => get_url_param('goods'),
            'unit' => get_url_param('unit'),
            'transaction_only' => true,
        ];
        if (get_url_param('ex_no_container') == 'LCL' || get_url_param('is_lcl')) {
            $customerStorageDetails = $this->reportStorage->getGoodsStorageActivityUsage($filters);
        } else {
            $filters['ex_no_container'] = get_url_param('ex_no_container');
            $customerStorageDetails = $this->reportStorage->getContainerizedGoodsStorageActivityUsage($filters);
        }

        $customerId = get_url_param('customer');
        $goodsId = get_url_param('goods');
        $customer = [];
        $goods = [];
        if (!is_array($customerId)) {
            $customer = $this->people->getById($customerId);
        }
        if (!is_array($goodsId)) {
            $goods = $this->goods->getById($goodsId);
        }

        if (get_url_param('export')) {
            $this->exporter->exportFromArray('Storage usage goods activity', $customerStorageDetails);
        } else {
            $this->render('storage_usage/space_usage_detail_activity', compact('customerStorageDetails', 'customer', 'goods'), 'Space usage detail activity');
        }
    }

    /**
     * Dedicated space storage usage.
     *
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function space_usage_movement()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_STORAGE_USAGE_VIEW);

        $goodsStorages = [];
        if (!empty($_GET)) {
            $filters = array_merge($_GET, [
                'customer_storage_self' => true,
                'customer_include_member' => true
            ]);

            if (empty(get_if_exist($filters, 'customer')) || empty(get_if_exist($filters, 'date_from'))) {
                show_error('Date and customer is required filter');
            }
            //$goodsStorages = $this->reportStorage->getContainerizedGoodsStorageActivityUsage($filters);
            //$goodsStorages = $this->reportStorage->getGoodsStorageActivityUsage($filters);
            $goodsStorages = $this->reportStorage->getStorageActivityUsage($filters);
        }

        if (get_url_param('export')) {
            $this->reportStorage->exportCustomerStorageActivity($goodsStorages);
        } else {
            $selectedCustomer = $this->people->getById($this->input->get('customer'));

            $this->render('storage_usage/space_usage_movement', compact('goodsStorages', 'selectedCustomer'), 'Space usage movement');
        }
    }
}