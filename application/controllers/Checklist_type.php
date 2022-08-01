<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Checklist_type
 * @property ChecklistTypeModel $checklistType
 * @property Exporter $exporter
 */
class Checklist_type extends MY_Controller
{
    /**
     * Unit constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ChecklistTypeModel', 'checklistType');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show list of checklist types.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CHECKLIST_TYPE_VIEW);

        $checklistTypes = $this->checklistType->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Checklist type", $checklistTypes);
        } else {
            $this->render('checklist_type/index', compact('checklistTypes'));
        }
    }

    /**
     * View single checklist_type by id.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CHECKLIST_TYPE_VIEW);

        $checklist_type = $this->checklistType->getById($id);

        $this->render('checklist_type/view', compact('checklist_type'));
    }

    /**
     * Show create checklist_type form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CHECKLIST_TYPE_CREATE);

        $this->render('checklist_type/create');
    }

    /**
     * Show edit form checklist_type.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CHECKLIST_TYPE_EDIT);

        $checklist_type = $this->checklistType->getById($id);

        $this->render('checklist_type/edit', compact('checklist_type'));
    }

    /**
     * Set checklist_type data validation.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'checklist_type' => 'trim|required|max_length[500]',
        ];
    }

    /**
     * Save data checklist_type.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CHECKLIST_TYPE_CREATE);

        if ($this->validate()) {
            $checklist_type = $this->input->post('checklist_type');
            $checklist_subtype = $this->input->post('subtype');

            $save = $this->checklistType->create([
                'checklist_type' => $checklist_type,
                'subtype' => $checklist_subtype,
            ]);

            if ($save) {
                flash('success', "Checklist type {$checklist_type} successfully created", 'checklist_type');
            } else {
                flash('danger', "Save Checklist type {$checklist_type} failed");
            }
        }
        $this->create();
    }

    /**
     * Update checklist_type data.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CHECKLIST_TYPE_EDIT);

        $checklist_type = $this->input->post('checklist_type');
        $checklist_subtype = $this->input->post('subtype');

        $update = $this->checklistType->update([
            'checklist_type' => $checklist_type,
             'subtype' => $checklist_subtype,
        ], $id);

        if ($update) {
            flash('success', "Checklist type {$checklist_type} updated", 'checklist_type');
        } else {
            flash('danger', "Update Checklist type {$checklist_type}} failed");
        }
        $this->edit($id);
    }

    /**
     * Perform deleting data checklist_type.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CHECKLIST_TYPE_DELETE);

        $checklist_type = $this->checklistType->getById($id);

        if ($this->checklistType->delete($id, true)) {
            flash('warning', "Checklist type {$checklist_type['checklist_type']} successfully deleted");
        } else {
            flash('danger', "Delete unit {$checklist_type['checklist_type']} failed");
        }

        redirect('checklist_type');
    }
}