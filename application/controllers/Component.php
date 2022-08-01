<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Component
 * @property ComponentModel $component
 * @property Exporter $exporter
 */
class Component extends MY_Controller
{
    /**
     * Handling component constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ComponentModel', 'component');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show handling component data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_COMPONENT_VIEW);

        $components = $this->component->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Components", $components);
        } else {
            $this->render('component/index', compact('components'));
        }
    }

    /**
     * Show detail handling component.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_COMPONENT_VIEW);

        $component = $this->component->getById($id);

        $this->render('component/view', compact('component'));
    }

    /**
     * Show create handling component form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_COMPONENT_CREATE);

        $this->render('component/create');
    }

    /**
     * Base validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'handling_component' => 'trim|required|max_length[100]',
            'component_category' => 'trim|required|max_length[50]',
            'description' => 'trim|required|max_length[500]'
        ];
    }

    /**
     * Save new handling component.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_COMPONENT_CREATE);

        if ($this->validate()) {
            $handlingComponent = $this->input->post('handling_component');
            $componentCategory = $this->input->post('component_category');
            $description = $this->input->post('description');

            $save = $this->component->create([
                'handling_component' => $handlingComponent,
                'component_category' => $componentCategory,
                'description' => $description
            ]);

            if ($save) {
                flash('success', "Handling component {$handlingComponent} successfully created", 'component');
            } else {
                flash('danger', "Save handling component {$handlingComponent} failed");
            }
        }
        $this->create();
    }

    /**
     * Show edit handling component form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_COMPONENT_EDIT);

        $component = $this->component->getById($id);

        $this->render('component/edit', compact('component'));
    }

    /**
     * Update data branch by id.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_COMPONENT_EDIT);

        if ($this->validate()) {
            $handlingComponent = $this->input->post('handling_component');
            $componentCategory = $this->input->post('component_category');
            $description = $this->input->post('description');

            $update = $this->component->update([
                'handling_component' => $handlingComponent,
                'component_category' => $componentCategory,
                'description' => $description
            ], $id);

            if ($update) {
                flash('success', "Handling component {$handlingComponent} successfully updated", 'component');
            } else {
                flash('danger', "Update handling component {$handlingComponent} failed");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting handling component data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_COMPONENT_DELETE);

        $component = $this->component->getById($id);

        if ($this->component->delete($id)) {
            flash('warning', "Handling component {$component['handling_component']} successfully deleted");
        } else {
            flash('danger', "Delete handling component {$component['handling_component']} failed");
        }

        redirect('component');
    }

}