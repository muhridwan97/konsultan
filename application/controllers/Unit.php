<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Unit
 * @property UnitModel $unit
 * @property Exporter $exporter
 */
class Unit extends MY_Controller
{
    /**
     * Unit constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('UnitModel', 'unit');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show list of units.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UNIT_VIEW);

        $units = $this->unit->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Units", $units);
        } else {
            $this->render('unit/index', compact('units'));
        }
    }

    /**
     * View single unit by id.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UNIT_VIEW);

        $unit = $this->unit->getById($id);

        $this->render('unit/view', compact('unit'));
    }

    /**
     * Show create unit form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UNIT_CREATE);

        $this->render('unit/create');
    }

    /**
     * Show edit form unit.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UNIT_EDIT);

        $unit = $this->unit->getById($id);

        $this->render('unit/edit', compact('unit'));
    }

    /**
     * Set unit data validation.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'unit' => 'trim|required|max_length[50]',
            'description' => 'trim|required|max_length[500]',
        ];
    }

    /**
     * Save data unit.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UNIT_CREATE);

        if ($this->validate()) {
            $unit = $this->input->post('unit');
            $description = $this->input->post('description');

            $save = $this->unit->create([
                'unit' => $unit,
                'description' => $description
            ]);

            if ($save) {
                flash('success', "Unit {$unit} successfully created", 'unit');
            } else {
                flash('danger', "Save unit {$unit} failed");
            }
        }
        $this->create();
    }

    /**
     * Update unit data.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UNIT_EDIT);

        $unit = $this->input->post('unit');
        $description = $this->input->post('description');

        $update = $this->unit->update([
            'unit' => $unit,
            'description' => $description
        ], $id);

        if ($update) {
            flash('success', "Unit {$unit} successfully updated", 'unit');
        } else {
            flash('danger', "Update unit {$unit} failed");
        }
        $this->edit($id);
    }

    /**
     * Perform deleting data unit.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_UNIT_DELETE);

        $unit = $this->unit->getById($id);

        if ($this->unit->delete($id)) {
            flash('warning', "Unit {$unit['unit']} successfully deleted");
        } else {
            flash('danger', "Delete unit {$unit['unit']} failed");
        }

        redirect('unit');
    }
}