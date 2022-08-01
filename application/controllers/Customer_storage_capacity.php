<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Customer_storage_capacity
 * @property CustomerStorageCapacityModel $customerStorageCapacity
 * @property PeopleModel $customer
 * @property Exporter $exporter
 */
class Customer_storage_capacity extends MY_Controller
{
    /**
     * Customer_storage_capacity constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('CustomerStorageCapacityModel', 'customerStorageCapacity');
        $this->load->model('PeopleModel', 'customer');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'data' => 'GET',
        ]);
    }

    /**
     * Show customerStorageCapacity data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CUSTOMER_STORAGE_CAPACITY_VIEW);

        if ($this->input->get('export')) {
            $storageCapacities = $this->customerStorageCapacity->getAll($_GET);
            $this->exporter->exportFromArray('Storage Storage Capacities', $storageCapacities);
        } else {
            $customer = $this->customer->getById($this->input->get('customer'));

            $this->render('customer_storage_capacity/index', compact('customer'));
        }
    }

    /**
     * Get ajax datatable.
     */
    public function data()
    {
        $filters = array_merge(get_url_param('filter') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);

        $customerStorageCapacity = $this->customerStorageCapacity->getAll($filters);

        $this->render_json($customerStorageCapacity);
    }

    /**
     * Show view customer storage capacity form.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CUSTOMER_STORAGE_CAPACITY_VIEW);

        $customerStorageCapacity = $this->customerStorageCapacity->getById($id);

        $this->render('customer_storage_capacity/view', compact('customerStorageCapacity'));
    }

    /**
     * Show create customer storage capacity form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CUSTOMER_STORAGE_CAPACITY_CREATE);

        $customer = $this->customer->getById($this->input->post('customer'));

        $this->render('customer_storage_capacity/create', compact('customer'));
    }

    /**
     * Get base validation rules.
     *
     * @param mixed ...$params
     * @return array
     */
    protected function _validation_rules(...$params)
    {
        $id = isset($params[0]) ? $params[0] : 0;

        return [
            'customer' => 'trim|required',
            'warehouse_capacity' => 'integer|is_natural|greater_than_equal_to[0]|less_than_equal_to[50000]',
            'yard_capacity' => 'integer|is_natural|greater_than_equal_to[0]|less_than_equal_to[50000]',
            'covered_yard_capacity' => 'integer|is_natural|greater_than_equal_to[0]|less_than_equal_to[50000]',
            'effective_date' => [
                'trim', 'required', 'max_length[20]', ['date_exists', function ($effectiveDate) use ($id) {
                    $this->form_validation->set_message('date_exists', 'The %s has been registered before, try another');
                    return empty($this->customerStorageCapacity->getBy([
                        'ref_customer_storage_capacities.id_customer' => $this->input->post('customer'),
                        'ref_customer_storage_capacities.effective_date' => format_date($effectiveDate),
                        'ref_customer_storage_capacities.id!=' => $id
                    ]));
                }]
            ],
            'description' => 'max_length[500]',
        ];
    }

    /**
     * Save new customer storage capacity.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CUSTOMER_STORAGE_CAPACITY_CREATE);

        if ($this->validate()) {
            $branchId = if_empty($this->input->post('branch'), get_active_branch_id());
            $customerId = $this->input->post('customer');
            $warehouseCapacity = $this->input->post('warehouse_capacity');
            $yardCapacity = $this->input->post('yard_capacity');
            $coveredYardCapacity = $this->input->post('covered_yard_capacity');
            $effectiveDate = $this->input->post('effective_date');
            $expiredDate = $this->input->post('expired_date');
            $description = $this->input->post('description');

            $customer = $this->customer->getById($customerId);

            $this->db->trans_status();

            $this->customerStorageCapacity->create([
                'id_branch' => $branchId,
                'id_customer' => $customerId,
                'warehouse_capacity' => $warehouseCapacity,
                'yard_capacity' => $yardCapacity,
                'covered_yard_capacity' => $coveredYardCapacity,
                'effective_date' => format_date($effectiveDate),
                'expired_date' => format_date($expiredDate),
                'description' => $description
            ]);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Customer storage capacity {$customer['name']} effective {$effectiveDate} successfully created", 'customer-storage-capacity');
            } else {
                flash('danger', "Save customer storage capacity failed");
            }
        }
        $this->create();
    }

    /**
     * Show edit customer storage capacity form.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CUSTOMER_STORAGE_CAPACITY_EDIT);

        $customerStorageCapacity = $this->customerStorageCapacity->getById($id);
        $customer = $this->customer->getById(if_empty($this->input->post('customer'), $customerStorageCapacity['id_customer']));

        $this->render('customer_storage_capacity/edit', compact('customerStorageCapacity', 'customer'));
    }

    /**
     * Update data customer storage capacity by id.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CUSTOMER_STORAGE_CAPACITY_EDIT);

        if ($this->validate($this->_validation_rules($id))) {
            $customerId = $this->input->post('customer');
            $warehouseCapacity = $this->input->post('warehouse_capacity');
            $yardCapacity = $this->input->post('yard_capacity');
            $coveredYardCapacity = $this->input->post('covered_yard_capacity');
            $effectiveDate = $this->input->post('effective_date');
            $expiredDate = $this->input->post('expired_date');
            $description = $this->input->post('description');

            $customerStorageCapacity = $this->customerStorageCapacity->getById($id);

            $this->db->trans_status();

            $this->customerStorageCapacity->update([
                'id_customer' => $customerId,
                'warehouse_capacity' => $warehouseCapacity,
                'yard_capacity' => $yardCapacity,
                'covered_yard_capacity' => $coveredYardCapacity,
                'effective_date' => format_date($effectiveDate),
                'expired_date' => format_date($expiredDate),
                'description' => $description
            ], $id);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Customer storage capacity {$customerStorageCapacity['customer_name']} successfully updated", 'customer-storage-capacity');
            } else {
                flash('danger', "Update customer storage capacity {$customerStorageCapacity['no_delivery_tracking']} failed");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting customer storage capacity data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CUSTOMER_STORAGE_CAPACITY_DELETE);

        $customerStorageCapacity = $this->customerStorageCapacity->getById($id);

        if ($customerStorageCapacity['status'] == 'EXPIRED') {
            flash('danger', 'You cannot delete passed storage capacity setting', '_back');
        } else {
            if ($this->customerStorageCapacity->delete($id)) {
                flash('warning', "Customer storage capacity {$customerStorageCapacity['customer_name']} successfully deleted");
            } else {
                flash('danger', "Delete customer storage capacity failed");
            }
            redirect('customer-storage-capacity');
        }
    }
}