<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Extension_field
 * @property ExtensionFieldModel $extensionField
 * @property Exporter $exporter
 */
class Extension_field extends MY_Controller
{
    /**
     * Extension field constructor.
     */
    public function __construct()
    {
        parent::__construct();

        AuthorizationModel::mustLoggedIn();

        $this->load->model('ExtensionFieldModel', 'extensionField');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'ajax_get_by_booking_type' => 'GET'
        ]);
    }

    /**
     * Show extension field data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_EXTENSION_FIELD_VIEW);

        $extensionFields = $this->extensionField->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Extension fields", $extensionFields);
        } else {
            $this->render('extension_field/index', compact('extensionFields'));
        }
    }

    /**
     * Show detail extension fields.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_EXTENSION_FIELD_VIEW);

        $extensionField = $this->extensionField->getById($id);

        $this->render('extension_field/view', compact('extensionField'));
    }

    /**
     * Get extension field booking type.
     */
    public function ajax_get_by_booking_type()
    {
        $bookingTypeId = $this->input->get('id_booking_type');
        $extensionFields = $this->extensionField->getByBookingType($bookingTypeId);
        echo $this->load->view('extension_field/_extensions', ['extensionFields' => $extensionFields], true);
    }

    /**
     * Show create extension field form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_EXTENSION_FIELD_CREATE);

        $this->render('extension_field/create');
    }

    protected function _validation_rules()
    {
        return [
            'field_title' => 'trim|required|max_length[50]',
            'field_name' => 'trim|required|max_length[50]|regex_match[/^[A-Za-z0-9_]+$/]',
            'type' => 'trim|required|max_length[50]',
            'description' => 'trim|required|max_length[500]',
        ];
    }

    /**
     * Save new extension field.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_EXTENSION_FIELD_CREATE);

        if ($this->validate()) {
            $fieldTitle = $this->input->post('field_title');
            $fieldName = $this->input->post('field_name');
            $type = $this->input->post('type');
            $options = $this->input->post('options');
            $description = $this->input->post('description');

            $save = $this->extensionField->create([
                'field_title' => $fieldTitle,
                'field_name' => $fieldName,
                'type' => $type,
                'option' => json_encode($options),
                'description' => $description
            ]);

            if ($save) {
                flash('success', "Extension field {$fieldTitle} successfully created", 'extension-field');
            } else {
                flash('danger', "Save extension field {$fieldTitle} failed");
            }
        }
        $this->create();
    }

    /**
     * Show edit extension field form.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_EXTENSION_FIELD_EDIT);

        $extensionField = $this->extensionField->getById($id);

        $this->render('extension_field/edit', compact('extensionField'));
    }

    /**
     * Update data extension field by id.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_EXTENSION_FIELD_EDIT);

        if ($this->validate()) {
            $fieldTitle = $this->input->post('field_title');
            $fieldName = $this->input->post('field_name');
            $type = $this->input->post('type');
            $options = $this->input->post('options');
            $description = $this->input->post('description');

            $update = $this->extensionField->update([
                'field_title' => $fieldTitle,
                'field_name' => $fieldName,
                'type' => $type,
                'option' => json_encode($options),
                'description' => $description
            ], $id);

            if ($update) {
                flash('success', "Extension field {$fieldTitle} successfully updated", 'extension-field');
            } else {
                flash('danger', "Update extension field {$fieldTitle} failed");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting extension field data.
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_EXTENSION_FIELD_DELETE);

        $extensionField = $this->extensionField->getById($id);

        if ($this->extensionField->delete($id)) {
            flash('warning', "Extension field {$extensionField['field_name']} successfully deleted");
        } else {
            flash('danger', "Delete extension field {$extensionField['field_name']} failed");
        }

        redirect('extension-field');
    }

}