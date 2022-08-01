<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Branch
 * @property BranchModel $branch
 * @property WarehouseModel $warehouse
 * @property BranchVmsModel $branchVms
 * @property BranchHrModel $branchHr
 * @property EmployeeModel $employee
 * @property Exporter $exporter
 */
class Branch extends MY_Controller
{
    /**
     * Branch constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BranchModel', 'branch');
        $this->load->model('EmployeeModel', 'employee');
        $this->load->model('WarehouseModel', 'warehouse');
        $this->load->model('BranchVmsModel', 'branchVms');
        $this->load->model('BranchHrModel', 'branchHr');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'warehouse' => 'GET'
        ]);
    }

    /**
     * Show branches data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BRANCH_VIEW);

        $branches = $this->branch->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Branch", $branches);
        } else {
            $this->render('branch/index', compact('branches'));
        }
    }

    /**
     * Show detail branch.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BRANCH_VIEW);

        $branch = $this->branch->getById($id);
        $employee = $this->employee->getById($branch['pic']);
        $cso = $this->employee->getById($branch['id_cso']);

        $this->render('branch/view', compact('branch', 'employee', 'cso'));
    }

    /**
     * Get warehouse by id branch.
     *
     * @param $id
     */
    public function warehouse($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BRANCH_VIEW);

        $branch = $this->branch->getById($id);
        $warehouses = $this->warehouse->getBy(['id_branch' => $id]);

        $this->render('branch/warehouse', compact('branch', 'warehouses'));
    }

    /**
     * Show create branch form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BRANCH_CREATE);

        $branch_vms = $this->branchVms->getAll();
        $branch_hr = $this->branchHr->getAll();
        $employees = $this->employee->getAll();
        $dataCSO = $this->employee->getBy(['ref_employees.status' => EmployeeModel::STATUS_ACTIVE]);

        $get_days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];

        $this->render('branch/create', compact('get_days', 'branch_vms', 'employees', 'branch_hr', 'dataCSO'));
    }

    /**
     * Set validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'branch' => 'trim|required|max_length[50]',
            'address' => 'trim|required|max_length[300]',
            'pic' => 'trim|required|max_length[50]',
            'contact' => 'trim|required|max_length[50]',
            'email' => 'trim|required|max_length[50]',
            'description' => 'trim|required|max_length[500]',
            'cycle_count_day' => 'trim|max_length[500]',
            'cycle_count_goods' => 'trim|max_length[500]',
            'kpi_inbound_do' => 'trim|required|numeric|integer|is_natural',
            'kpi_inbound_sppb' => [
                'trim', 'required', 'numeric', 'integer', 'is_natural', ['min_value', function ($input) {
                    $do = $this->input->post('kpi_inbound_do');

                    if ($input < $do) {
                        $this->form_validation->set_message('min_value', 'The {field} not allowed below the DO.');
                        return false;
                    }
                    return true;
                }]
            ],
            'kpi_inbound_gate_in' => [
                'trim', 'required', 'numeric', 'integer', 'is_natural', ['min_value', function ($input) {
                    $sppb = $this->input->post('kpi_inbound_sppb');

                    if ($input < $sppb) {
                        $this->form_validation->set_message('min_value', 'The {field} not allowed below the SPPB.');
                        return false;
                    }
                    return true;
                }]
            ],
            'kpi_inbound_stripping' => [
                'trim', 'required', 'numeric', 'integer', 'is_natural', ['min_value', function ($input) {
                    $gateIn = $this->input->post('kpi_inbound_gate_in');

                    if ($input < $gateIn) {
                        $this->form_validation->set_message('min_value', 'The {field} not allowed below the Gate In.');
                        return false;
                    }
                    return true;
                }]
            ],
        ];
    }

    /**
     * Save new branch.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BRANCH_CREATE);

        if ($this->validate()) {
            $branch = $this->input->post('branch');
            $pic = $this->input->post('pic');
            $contact = $this->input->post('contact');
            $email = $this->input->post('email');
            $address = $this->input->post('address');
            $description = $this->input->post('description');
            $cycle_count_day = $this->input->post('cycle_count_day');
            $cycle_count_goods = $this->input->post('cycle_count_goods');
            $cycle_count_container = $this->input->post('cycle_count_container');
            $opname_day = $this->input->post('opname_day');
            $opname_day_name = $this->input->post('opname_day_name');
            $opname_pending_day = $this->input->post('opname_pending_day');
            $email_compliance = $this->input->post('email_compliance');
            $admin_compliance = $this->input->post('admin_compliance');
            $email_support = $this->input->post('email_support');
            $admin_support = $this->input->post('admin_support');
            $email_operational = $this->input->post('email_operational');
            $whatsapp_group = $this->input->post('whatsapp_group');
            $whatsapp_group_security = $this->input->post('whatsapp_group_security');
            $dashboardStatus = $this->input->post('dashboard_status');
            $tallyCheckApproval = $this->input->post('tally_check_approval');
            $do_notif = $this->input->post('do_notif');
            $branch_type = $this->input->post('branch_type');
            $max_slot_tep = $this->input->post('max_slot_tep');
            $branch_vms = $this->input->post('branch_vms');
            $branch_hr = $this->input->post('branch_hr');
            $qr_code_status = $this->input->post('qr_code_status');
            $stock_pallet = $this->input->post('stock_pallet');
            $initial_pallet = $this->input->post('initial_pallet');
            $cso = $this->input->post('cso');
            $kpiInboundDo = $this->input->post('kpi_inbound_do');
            $kpiInboundSppb = $this->input->post('kpi_inbound_sppb');
            $kpiInboundGateIn = $this->input->post('kpi_inbound_gate_in');
            $kpiInboundStripping = $this->input->post('kpi_inbound_stripping');
            $max_time_request = $this->input->post('max_time_request');

            $getBranchByBranchDelete = $this->branch->getBy(["branch" => $branch], false, false);
            $getBranchByBranchNonDelete = $this->branch->getBy(["branch" => $branch], false, true);
            if (empty($getBranchByBranchDelete) && empty($getBranchByBranchNonDelete)) {
                $save = $this->branch->create([
                    'id_branch_vms' => $branch_vms,
                    'id_branch_hr' => $branch_hr,
                    'id_cso' => $cso,
                    'branch' => $branch,
                    'address' => $address,
                    'pic' => $pic,
                    'contact' => $contact,
                    'email' => $email,
                    'description' => $description,
                    'cycle_count_day' => $cycle_count_day,
                    'cycle_count_goods' => $cycle_count_goods,
                    'cycle_count_container' => $cycle_count_container,
                    'opname_day' => $opname_day,
                    'opname_day_name' => $opname_day_name,
                    'opname_pending_day' => $opname_pending_day,
                    'admin_compliance' => $admin_compliance,
                    'email_compliance' => $email_compliance,
                    'email_operational' => $email_operational,
                    'email_support' => $email_support,
                    'admin_support' => $admin_support,
                    'whatsapp_group' => $whatsapp_group,
                    'whatsapp_group_security' => $whatsapp_group_security,
                    'dashboard_status' => $dashboardStatus,
                    'tally_check_approval' => $tallyCheckApproval,
                    'do_notif' => $do_notif,
                    'branch_type' => $branch_type,
                    'max_slot_tep' => $max_slot_tep,
                    'qr_code_status' => $qr_code_status,
                    'stock_pallet' => $stock_pallet,
                    'initial_pallet' => $initial_pallet,
                    'kpi_inbound_do' => $kpiInboundDo,
                    'kpi_inbound_sppb' => $kpiInboundSppb,
                    'kpi_inbound_gate_in' => $kpiInboundGateIn,
                    'kpi_inbound_stripping' => $kpiInboundStripping,
                    'max_time_request' => $max_time_request,
                ]);

                if ($save) {
                    flash('success', "Branch {$branch} successfully created", 'branch');
                } else {
                    flash('danger', 'Something is getting wrong, try again or contact administrator');
                }
            } else {
                flash('danger', "Create branch {$branch} failed, Branch {$branch} is already exist", 'branch');
            }
        }
        $this->create();
    }

    /**
     * Show edit branch form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BRANCH_EDIT);

        $branch = $this->branch->getById($id);
        $employees = $this->employee->getAll();
        $branch_vms = $this->branchVms->getAll();
        $branch_hr = $this->branchHr->getAll();
        $get_days = ['Sunday', 'Monday', 'Tuesday', 'Wednesday', 'Thursday', 'Friday', 'Saturday'];
        $dataCSO = $this->employee->getBy(['ref_employees.status' => EmployeeModel::STATUS_ACTIVE]);

        $this->render('branch/edit', compact('branch', 'get_days', 'branch_vms', 'employees', 'branch_hr', 'dataCSO'));
    }

    /**
     * Update data branch by id.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BRANCH_EDIT);

        if ($this->validate()) {
            $branch = $this->input->post('branch');
            $pic = $this->input->post('pic');
            $contact = $this->input->post('contact');
            $email = $this->input->post('email');
            $address = $this->input->post('address');
            $description = $this->input->post('description');
            $cycle_count_day = $this->input->post('cycle_count_day');
            $cycle_count_goods = $this->input->post('cycle_count_goods');
            $cycle_count_container = $this->input->post('cycle_count_container');
            $opname_day = $this->input->post('opname_day');
            $opname_day_name = $this->input->post('opname_day_name');
            $opname_pending_day = $this->input->post('opname_pending_day');
            $email_compliance = $this->input->post('email_compliance');
            $admin_compliance = $this->input->post('admin_compliance');
            $email_support = $this->input->post('email_support');
            $admin_support = $this->input->post('admin_support');
            $email_operational = $this->input->post('email_operational');

            $whatsapp_group = $this->input->post('whatsapp_group');
            $whatsapp_group_security = $this->input->post('whatsapp_group_security');
            $dashboardStatus = $this->input->post('dashboard_status');
            $tallyCheckApproval = $this->input->post('tally_check_approval');
            $do_notif = $this->input->post('do_notif');
            $branch_type = $this->input->post('branch_type');
            $max_slot_tep = $this->input->post('max_slot_tep');
            $branch_vms = $this->input->post('branch_vms');
            $branch_hr = $this->input->post('branch_hr');
            $qr_code_status = $this->input->post('qr_code_status');
            $stock_pallet = $this->input->post('stock_pallet');
            $initial_pallet = $this->input->post('initial_pallet');
            $cso = $this->input->post('cso');
            $kpiInboundDo = $this->input->post('kpi_inbound_do');
            $kpiInbounSppb = $this->input->post('kpi_inbound_sppb');
            $kpiInboundGateIn = $this->input->post('kpi_inbound_gate_in');
            $kpiInboundStripping = $this->input->post('kpi_inbound_stripping');
            $max_time_request = $this->input->post('max_time_request');

            $update = $this->branch->update([
                'id_branch_vms' => $branch_vms,
                'id_branch_hr' => $branch_hr,
                'id_cso' => $cso,
                'branch' => $branch,
                'address' => $address,
                'pic' => $pic,
                'contact' => $contact,
                'email' => $email,
                'description' => $description,
                'cycle_count_day' => $cycle_count_day,
                'cycle_count_goods' => $cycle_count_goods,
                'cycle_count_container' => $cycle_count_container,
                'opname_day' => $opname_day,
                'opname_day_name' => $opname_day_name,
                'opname_pending_day' => $opname_pending_day,
                'email_compliance' => $email_compliance,
                'admin_compliance' => $admin_compliance,
                'email_support' => $email_support,
                'admin_support' => $admin_support,
                'email_operational' => $email_operational,
                'whatsapp_group' => $whatsapp_group,
                'whatsapp_group_security' => $whatsapp_group_security,
                'dashboard_status' => $dashboardStatus,
                'tally_check_approval' => $tallyCheckApproval,
                'do_notif' => $do_notif,
                'branch_type' => $branch_type,
                'max_slot_tep' => $max_slot_tep,
                'qr_code_status' => $qr_code_status,
                'stock_pallet' => $stock_pallet,
                'initial_pallet' => $initial_pallet,
                'kpi_inbound_do' => $kpiInboundDo,
                'kpi_inbound_sppb' => $kpiInbounSppb,
                'kpi_inbound_gate_in' => $kpiInboundGateIn,
                'kpi_inbound_stripping' => $kpiInboundStripping,
                'max_time_request' => $max_time_request,
            ], $id);

            if ($update) {
                flash('success', "Branch {$branch} successfully updated", 'branch');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting branch data.
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BRANCH_DELETE);

        $branch = $this->branch->getById($id);

        if ($this->branch->delete($id)) {
            flash('warning', "Branch {$branch['branch']} successfully deleted");
        } else {
            flash('danger', 'Something is getting wrong, try again or contact administrator');
        }
        redirect('branch');
    }
}