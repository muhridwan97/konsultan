<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Position
 * @property PositionTypeModel $positionType
 * @property PositionModel $position
 * @property PositionBlockModel $positionBlock
 * @property PeopleModel $people
 * @property WarehouseModel $warehouse
 * @property Exporter $exporter
 */
class Position extends MY_Controller
{
    /**
     * Position constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('PositionTypeModel', 'positionType');
        $this->load->model('PositionModel', 'position');
        $this->load->model('PositionBlockModel', 'positionBlock');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('WarehouseModel', 'warehouse');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'position_data' => 'GET',
            'ajax_get_all' => 'GET',
            'ajax_get_position_block' => 'GET'
        ]);
    }

    /**
     * Show position data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_POSITION_VIEW);

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Positions", $this->position->getAll());
        } else {
            $this->render('position/index');
        }
    }

    /**
     * Get ajax datatable auction.
     */
    public function position_data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_POSITION_VIEW);

        $filters = [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ];

        $data = $this->position->getAll($filters);

        $this->render_json($data);
    }

    /**
     * Show detail position.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_POSITION_VIEW);

        $position = $this->position->getById($id);
        $blocks = $this->positionBlock->getBy(['ref_position_blocks.id_position' => $id]);

        $this->render('position/view', compact('position', 'blocks'));
    }

    /**
     * Show create position form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_POSITION_CREATE);

        $warehouses = $this->warehouse->getAll();
        $positionTypes = $this->positionType->getAll();

        $this->render('position/create', compact('warehouses', 'positionTypes'));
    }

    /**
     * Set validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'warehouse' => 'trim|required|integer',
            'position' => 'trim|required|max_length[50]',
            'position_type' => 'trim|required|integer',
            'description' => 'trim|max_length[500]'
        ];
    }

    /**
     * Save new position.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_POSITION_CREATE);

        if ($this->validate()) {
            $warehouseId = $this->input->post('warehouse');
            $positionTypeId = $this->input->post('position_type');
            $customerId = $this->input->post('customer');
            $position = $this->input->post('position');
            $blocks = $this->input->post('blocks');
            $description = $this->input->post('description');

            $this->db->trans_start();

            $this->position->create([
                'id_position_type' => $positionTypeId,
                'id_warehouse' => $warehouseId,
                'id_customer' => $customerId,
                'position' => $position,
                'description' => $description
            ]);
            $positionId = $this->db->insert_id();

            foreach ($blocks as $block) {
                $blockCoordinate = explode('-', extract_number($block));
                $this->positionBlock->create([
                    'id_position' => $positionId,
                    'position_block' => $block,
                    'position_x' => get_if_exist($blockCoordinate, '0'),
                    'position_y' => get_if_exist($blockCoordinate, '1'),
                    'position_z' => get_if_exist($blockCoordinate, '2'),
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Position {$position} successfully created", 'position');
            } else {
                flash('danger', "Create position {$position} failed");
            }
        }
        $this->create();
    }

    /**
     * Show position edit form.
     *
     * @param integer $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_POSITION_EDIT);

        $positionTypes = $this->positionType->getAll();
        $position = $this->position->getById($id);
        $person = $this->people->getById($position['id_customer']);
        $warehouse = $this->warehouse->getById($position['id_warehouse']);

        $this->render('position/edit', compact('positionTypes', 'position', 'person', 'warehouse'));
    }

    /**
     * Update data position by id.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_POSITION_EDIT);

        if ($this->validate()) {
            $warehouseId = $this->input->post('warehouse');
            $positionTypeId = $this->input->post('position_type');
            $customerId = $this->input->post('customer');
            $position = $this->input->post('position');
            $blocks = $this->input->post('blocks');
            $description = $this->input->post('description');

            $this->db->trans_start();

            $this->position->update([
                'id_position_type' => $positionTypeId,
                'id_warehouse' => $warehouseId,
                'id_customer' => $customerId,
                'position' => $position,
                'description' => $description
            ], $id);

            $this->positionBlock->delete(['id_position' => $id]);
            foreach ($blocks as $block) {
                $blockCoordinate = explode('-', extract_number($block));
                $this->positionBlock->create([
                    'id_position' => $id,
                    'position_block' => $block,
                    'position_x' => get_if_exist($blockCoordinate, '0'),
                    'position_y' => get_if_exist($blockCoordinate, '1'),
                    'position_z' => get_if_exist($blockCoordinate, '2'),
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Position {$position} successfully updated", 'position');
            } else {
                flash('danger', "Update position {$position} failed");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting position data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_POSITION_DELETE);

        $position = $this->position->getById($id);

        if ($this->position->delete($id)) {
            flash('warning', "Position {$position['position']} successfully deleted");
        } else {
            flash('danger', "Delete position {$position['position']} failed");
        }
        redirect('position');
    }

    /**
     * Get position query
     */
    public function ajax_get_all()
    {
        $search = $this->input->get('q');
        $page = $this->input->get('page');

        $positions = $this->position->getByName($search, $page);

        $this->render_json($positions);
    }

    /**
     * Get position block query
     */
    public function ajax_get_position_block()
    {
        $positionId = $this->input->get('id_position');

        $positions = $this->positionBlock->getBy(['id_position' => $positionId]);

        $this->render_json($positions);
    }
}