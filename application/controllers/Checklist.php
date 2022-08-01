<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Unit
 * @property ChecklistModel $checklist
 * @property ChecklistTypeModel $checklistType
 * @property Exporter $exporter
 */
class checklist extends MY_Controller
{
    /**
     * Unit constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ChecklistModel', 'checklist');
        $this->load->model('ChecklistTypeModel', 'checklistType');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show list of checklists.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CHECKLIST_VIEW);

        $checklists = $this->checklist->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Checklist", $checklists);
        } else {
            $this->render('checklist/index', compact('checklists'));
        }
    }

    /**
     * View single checklist by id.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CHECKLIST_VIEW);

        $checklist = $this->checklist->getById($id);

        $this->render('checklist/view', compact('checklist'));
    }

    /**
     * Show create checklist form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CHECKLIST_CREATE);

        $checklist_types = $this->checklistType->getAll();

        $this->render('checklist/create', compact('checklist_types'));
    }

    /**
     * Show edit form checklist.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CHECKLIST_EDIT);

        $checklist = $this->checklist->getById($id);
        $checklist_types = $this->checklistType->getAll();

        $this->render('checklist/edit', compact('checklist', 'checklist_types'));
    }

    /**
     * Set checklist data validation.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'checklist' => 'trim|required|max_length[50]',
        ];
    }

    /**
     * Save data checklist.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CHECKLIST_CREATE);

        if ($this->validate()) {
            $checklist = $this->input->post('checklist');
            $checklist_type = $this->input->post('checklist_type');

            $save = $this->checklist->create([
                'description' => $checklist,
                'id_checklist_type' => $checklist_type
            ]);

            if ($save) {
                flash('success', "Checklist {$checklist} successfully created", 'checklist');
            } else {
                flash('danger', "Save checklist {$checklist} failed");
            }
        }
        $this->create();
    }

    /**
     * Update checklist data.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CHECKLIST_EDIT);

        $checklist = $this->input->post('checklist');
        $checklist_type = $this->input->post('checklist_type');

        $update = $this->checklist->update([
            'description' => $checklist,
            'id_checklist_type' => $checklist_type
        ], $id);


        if ($update) {
            flash('success', "Checklist {$checklist} successfully updated", 'checklist');
        } else {
            flash('danger', "Save checklist {$checklist} failed");
        }
        $this->edit($id);
    }

    /**
     * Perform deleting data checklist.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_CHECKLIST_DELETE);

        $checklist = $this->checklist->getById($id);

        if ($this->checklist->delete($id, true)) {
            flash('warning', "Checklist {$checklist['description']} successfully deleted");
        } else {
            flash('danger', "Delete checklist {$checklist['description']} failed");
        }

        redirect('checklist');
    }
}