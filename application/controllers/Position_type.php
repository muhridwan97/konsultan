<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Position_type
 * @property PositionTypeModel $positionType
 * @property Exporter $exporter
 */
class Position_type extends MY_Controller
{
    /**
     * Position_type constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('PositionTypeModel', 'positionType');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show position types data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_POSITION_TYPE_VIEW);

        $positionTypes = $this->positionType->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Position types", $positionTypes);
        } else {
            $this->render('position_type/index', compact('positionTypes'));
        }
    }

    /**
     * Show detail permission type.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_POSITION_TYPE_VIEW);

        $positionType = $this->positionType->getById($id);

        $this->render('position_type/view', compact('positionType'));
    }

    /**
     * Show create permission type form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_POSITION_TYPE_CREATE);

        $this->render('position_type/create');
    }

    /**
     * Set validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'position_type' => 'trim|required|max_length[50]',
            'is_usable' => 'trim|required|in_list[0,1]',
            'color' => 'trim|required|max_length[50]',
            'description' => 'trim|required|max_length[500]'
        ];
    }

    /**
     * Save new permission type.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_POSITION_TYPE_CREATE);

        if ($this->validate()) {
            $positionType = $this->input->post('position_type');
            $isUsable = $this->input->post('is_usable');
            $color = $this->input->post('color');
            $description = $this->input->post('description');

            $save = $this->positionType->create([
                'position_type' => $positionType,
                'is_usable' => $isUsable,
                'color' => $color,
                'description' => $description
            ]);

            if ($save) {
                flash('success', "Position {$positionType} successfully created", 'position-type');
            } else {
                flash('danger', 'Create position type failed');
            }
        }
        $this->create();
    }

    /**
     * Show edit position type form.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_POSITION_TYPE_EDIT);

        $positionType = $this->positionType->getById($id);

        $this->render('position_type/edit', compact('positionType'));
    }

    /**
     * Update data position type by id.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_POSITION_TYPE_EDIT);

        if ($this->validate()) {
            $positionType = $this->input->post('position_type');
            $isUsable = $this->input->post('is_usable');
            $color = $this->input->post('color');
            $description = $this->input->post('description');

            $update = $this->positionType->update([
                'position_type' => $positionType,
                'is_usable' => $isUsable,
                'color' => $color,
                'description' => $description
            ], $id);

            if ($update) {
                flash('success', "Position type {$positionType} successfully updated", 'position-type');
            } else {
                flash('danger', 'Update position type failed');
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting position type data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_POSITION_TYPE_DELETE);

        $permissionType = $this->positionType->getById($id);

        if ($this->positionType->delete($id)) {
            flash('warning', "Position type {$permissionType['permission_type']} successfully deleted");
        } else {
            flash('danger', 'Delete position type failed');
        }
        redirect('position-type');
    }
}