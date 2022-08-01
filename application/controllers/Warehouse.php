<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Warehouse
 * @property WarehouseModel $warehouse
 * @property BranchModel $branch
 * @property PositionModel $position
 * @property PositionBlockModel $positionBlock
 */
class Warehouse extends MY_Controller
{
    /**
     * Warehouse constructor.
     */
    public function __construct()
    {
        parent::__construct();

        AuthorizationModel::mustLoggedIn();

        $this->load->model('WarehouseModel', 'warehouse');
        $this->load->model('BranchModel', 'branch');
        $this->load->model('PositionModel', 'position');
        $this->load->model('PositionBlockModel', 'positionBlock');
        $this->load->model('LogModel', 'logHistory');

        $this->setFilterMethods([
            'position' => 'GET',
            'ajax_get_all' => 'GET',
            'ajax_get_warehouse_map' => 'GET'
        ]);
    }

    /**
     * Show warehouse data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WAREHOUSE_VIEW);

        $warehouses = $this->warehouse->getAll();
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();

        $this->render('warehouse/index', compact('warehouses'));
    }

    /**
     * Show detail warehouse.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WAREHOUSE_VIEW);

        $warehouse = $this->warehouse->getById($id);

        $positions = $this->position->getBy(['ref_positions.id_warehouse' => $id]);

        $positionBlocks = $this->positionBlock->getBy(['ref_positions.id_warehouse' => $id]);

        foreach ($positions as &$position) {
            $filteredBlocks = array_filter($positionBlocks, function ($block) use ($position) {
                return $block['id_position'] == $position['id'];
            });
            $position['blocks'] = array_values($filteredBlocks);
        }

        $this->render('warehouse/view', compact('warehouse', 'positions', 'positionBlocks'));
    }

    /**
     * Get position by id warehouse.
     *
     * @param $id
     */
    public function position($id)
    {
        $warehouse = $this->warehouse->getById($id);
        $positions = $this->position->getBy(['ref_positions.id_warehouse' => $id]);

        $this->render('warehouse/position', compact('warehouse', 'positions'));
    }

    /**
     * Show create warehouse form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WAREHOUSE_CREATE);

        $branches = $this->branch->getAll();

        $this->render('warehouse/create', compact('branches'));
    }

    /**
     * Set validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'branch' => 'trim|required|integer',
            'warehouse' => 'trim|required|max_length[50]',
            'type' => 'trim|required|in_list[FIELD,YARD,COVERED YARD,WAREHOUSE]',
            'total_column' => 'trim|required|integer|is_natural_no_zero',
            'total_row' => 'trim|required|integer|is_natural_no_zero',
            'description' => 'trim|max_length[500]'
        ];
    }

    /**
     * Save new warehouse.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WAREHOUSE_CREATE);

        if ($this->validate()) {
            $branch = $this->input->post('branch');
            $warehouse = $this->input->post('warehouse');
            $type = $this->input->post('type');
            $totalColumn = $this->input->post('total_column');
            $totalRow = $this->input->post('total_row');
            $description = $this->input->post('description');

            $save = $this->warehouse->create([
                'id_branch' => $branch,
                'warehouse' => $warehouse,
                'type' => $type,
                'total_column' => $totalColumn,
                'total_row' => $totalRow,
                'description' => $description
            ]);

            if ($save) {
                flash('success', "Warehouse {$warehouse} successfully created", 'warehouse');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->create();
    }

    /**
     * Show edit warehouse form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WAREHOUSE_EDIT);

        $warehouse = $this->warehouse->getById($id);
        $branches = $this->branch->getAll();

        $this->render('warehouse/edit', compact('warehouse', 'branches'));
    }

    /**
     * Update data warehouse by id.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WAREHOUSE_EDIT);

        if ($this->validate()) {
            $branch = $this->input->post('branch');
            $warehouse = $this->input->post('warehouse');
            $type = $this->input->post('type');
            $totalColumn = $this->input->post('total_column');
            $totalRow = $this->input->post('total_row');
            $description = $this->input->post('description');

            $update = $this->warehouse->update([
                'id_branch' => $branch,
                'warehouse' => $warehouse,
                'type' => $type,
                'total_column' => $totalColumn,
                'total_row' => $totalRow,
                'description' => $description
            ], $id);

            if ($update) {
                flash('success', "Warehouse {$warehouse} successfully updated", 'warehouse');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting warehouse data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WAREHOUSE_DELETE);

        $warehouse = $this->warehouse->getById($id);

        if ($this->warehouse->delete($id)) {
            flash('warning', "Warehouse {$warehouse['warehouse']} successfully deleted");
        } else {
            flash('danger', 'Something is getting wrong, try again or contact administrator');
        }
        redirect('warehouse');
    }

    /**
     * Ajax get all warehouse data
     */
    public function ajax_get_all()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $search = $this->input->get('q');
            $page = $this->input->get('page');

            $warehouses = $this->warehouse->getByName($search, $page);

            echo json_encode($warehouses);
        }
    }

    /**
     * Ajax get all warehouse data
     */
    public function ajax_get_warehouse_map()
    {
        $warehouseId = $this->input->get('id_warehouse');

        $warehouse = $this->warehouse->getById($warehouseId);

        $positions = $this->position->getBy(['ref_positions.id_warehouse' => $warehouseId]);

        $positionBlocks = $this->positionBlock->getBy(['ref_positions.id_warehouse' => $warehouseId]);

        foreach ($positions as &$position) {
            $filteredBlocks = array_filter($positionBlocks, function ($block) use ($position) {
                return $block['id_position'] == $position['id'];
            });
            $position['blocks'] = array_values($filteredBlocks);
        }

        $this->render_json([
            'warehouse' => $warehouse,
            'positions' => $positions
        ]);
    }
}