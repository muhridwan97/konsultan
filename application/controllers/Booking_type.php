<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Booking_type
 * @property ModuleModel $module
 * @property BookingTypeModuleModel $bookingTypeModule
 * @property BookingTypeModel $bookingType
 * @property BookingDocumentTypeModel $bookingDocumentType
 * @property DocumentTypeModel $documentType
 * @property ExtensionFieldModel $extensionField
 * @property BookingExtensionFieldModel $bookingExtensionField
 * @property Exporter $exporter
 */
class Booking_type extends MY_Controller
{
    /**
     * Booking_type constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ModuleModel', 'module');
        $this->load->model('BookingTypeModuleModel', 'bookingTypeModule');
        $this->load->model('BookingTypeModel', 'bookingType');
        $this->load->model('BookingDocumentTypeModel', 'bookingDocumentType');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('ExtensionFieldModel', 'extensionField');
        $this->load->model('BookingExtensionFieldModel', 'bookingExtensionField');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'set_module_import' => 'GET',
            'save_module_import_setting' => 'POST',
            'ajax_get_by_category' => 'GET',
            'ajax_get_customer_booking_types' => 'GET',
        ]);
    }

    /**
     * Show booking type list data.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_TYPE_VIEW);

        $bookingTypes = $this->bookingType->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Booking types", $bookingTypes);
        } else {
            $this->render('booking_type/index', compact('bookingTypes'));
        }
    }

    /**
     * View single booking type data.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_TYPE_VIEW);

        $bookingType = $this->bookingType->getById($id);
        $bookingDocuments = $this->documentType->getByBookingType($id);
        $extensionFields = $this->extensionField->getByBookingType($id);

        $this->render('booking_type/view', compact('bookingType', 'bookingDocuments', 'extensionFields'));
    }

    /**
     * Show create form booking type.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_TYPE_CREATE);

        $documentTypes = $this->documentType->getAll();
        $extensionFields = $this->extensionField->getAll();

        $this->render('booking_type/create', compact('documentTypes', 'extensionFields'));
    }

    /**
     * Show edit form booking type data.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_TYPE_EDIT);

        $bookingType = $this->bookingType->getById($id);
        $documentTypes = $this->documentType->getAll();
        $bookingDocuments = $this->documentType->getByBookingType($id);
        $extensionFields = $this->extensionField->getAll();
        $bookingExtensions = $this->extensionField->getByBookingType($id);

        $this->render('booking_type/edit', compact('bookingType', 'documentTypes', 'bookingDocuments', 'extensionFields', 'bookingExtensions'));
    }

    /**
     * Base rule form validation.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'booking_type' => 'trim|required|max_length[50]',
            'category' => 'trim|required|in_list[INBOUND,OUTBOUND]',
            'type' => 'trim|required|in_list[IMPORT,EXPORT]',
            'with_do' => 'trim|required|in_list[0,1]',
            'document_type' => 'trim|required|integer',
            'description' => 'trim|max_length[300]',
            'document_types[]' => 'trim',
            'extension_fields[]' => 'trim',
        ];
    }

    /**
     * Save new booking type data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_TYPE_CREATE);

        if ($this->validate()) {
            $bookingType = $this->input->post('booking_type');
            $documentType = $this->input->post('document_type');
            $category = $this->input->post('category');
            $type = $this->input->post('type');
            $withDo = $this->input->post('with_do');
            $description = $this->input->post('description');
            $documentTypes = $this->input->post('document_types');
            $dashboardStatus = $this->input->post('dashboard_status');
            $extensionFields = $this->input->post('extension_fields');

            $requirements = [];
            foreach ($documentTypes as $documentType) {
                $requirements[] = $this->input->post('requirement_' . $documentType);
            }

            $this->db->trans_start();

            $this->bookingType->create([
                'booking_type' => $bookingType,
                'id_document_type' => $documentType,
                'type' => $type,
                'category' => $category,
                'with_do' => $withDo,
                'description' => $description,
                'dashboard_status' => $dashboardStatus
            ]);
            $bookingTypeId = $this->db->insert_id();

            for ($i = 0; $i < count($documentTypes); $i++) {
                $this->bookingDocumentType->createBookingDocumentType([
                    'id_booking_type' => $bookingTypeId,
                    'id_document_type' => $documentTypes[$i],
                    'is_required' => $requirements[$i]
                ]);
            }

            for ($i = 0; $i < count($extensionFields); $i++) {
                $this->bookingExtensionField->createBookingExtensionField([
                    'id_booking_type' => $bookingTypeId,
                    'id_extension_field' => $extensionFields[$i]
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Document Type {$bookingType} successfully created", 'booking-type');
            } else {
                flash('danger', "Save booking type {$bookingType} failed");
            }
        }
        $this->create();
    }

    /**
     * Update booking type data.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_TYPE_EDIT);

        if ($this->validate()) {
            $bookingType = $this->input->post('booking_type');
            $documentType = $this->input->post('document_type');
            $type = $this->input->post('type');
            $category = $this->input->post('category');
            $withDo = $this->input->post('with_do');
            $description = $this->input->post('description');
            $documentTypes = $this->input->post('document_types');
            $extensionFields = $this->input->post('extension_fields');
            $dashboardStatus = $this->input->post('dashboard_status');

            $this->db->trans_start();

            $this->bookingType->update([
                'booking_type' => $bookingType,
                'id_document_type' => $documentType,
                'type' => $type,
                'category' => $category,
                'with_do' => $withDo,
                'description' => $description,
                'dashboard_status' => $dashboardStatus
            ], $id);

            $requirements = [];
            foreach ($documentTypes as $documentType) {
                $requirements[] = $this->input->post('requirement_' . $documentType);
            }

            $this->bookingDocumentType->deleteDocumentTypeByBookingType($id);
            for ($i = 0; $i < count($documentTypes); $i++) {
                $this->bookingDocumentType->createBookingDocumentType([
                    'id_booking_type' => $id,
                    'id_document_type' => $documentTypes[$i],
                    'is_required' => $requirements[$i]
                ]);
            }

            $this->bookingExtensionField->deleteExtensionFieldByBookingType($id);
            for ($i = 0; $i < count($extensionFields); $i++) {
                $this->bookingExtensionField->createBookingExtensionField([
                    'id_booking_type' => $id,
                    'id_extension_field' => $extensionFields[$i]
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Booking type {$bookingType} successfully updated", 'booking-type');
            } else {
                flash('danger', "Update booking type {$bookingType} failed");
            }
        }

        $this->edit($id);
    }

    /**
     * Delete booking type data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_TYPE_DELETE);

        $bookingType = $this->bookingType->getById($id);

        if ($this->bookingType->delete($id)) {
            flash('warning', "Booking type {$bookingType['booking_type']} successfully deleted");
        } else {
            flash('danger', "Delete booking type {$bookingType['booking_type']} failed");
        }
        redirect('booking-type');
    }

    /**
     * Set booking type module configuration.
     *
     * @param $id
     */
    public function set_module_import($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_TYPE_EDIT);

        $bookingType = $this->bookingType->getById($id);
        $bookingTypeModules = $this->bookingTypeModule->getBookingTypeModuleByBookingType($id);
        $modules = $this->module->getModulesByType($bookingType['category']);
        $extensionFields = $this->extensionField->getByBookingType($id);

        $this->render('booking_type/module_setting', compact('bookingType', 'bookingTypeModules', 'modules', 'extensionFields'));
    }

    /**
     * Save setting import data.
     *
     * @param $id
     */
    public function save_module_import_setting($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_TYPE_EDIT);

        $rules = [
            'id_booking_type' => 'trim|required|integer',
            'module' => 'trim|required',
        ];

        if ($this->validate($rules)) {
            $bookingTypeId = $this->input->post('id_booking_type');
            $moduleId = $this->input->post('module');

            $headers = $this->input->post('headers');
            $extensions = $this->input->post('extensions');
            $containers = $this->input->post('containers');
            $goods = $this->input->post('goods');

            $module = $this->module->getModuleById($moduleId);

            $this->db->trans_start();

            $this->bookingTypeModule->deleteBookingTypeModuleByBookingType($bookingTypeId);

            foreach ($headers as $header => $values) {
                $this->bookingTypeModule->createBookingTypeModule([
                    'id_booking_type' => $bookingTypeId,
                    'id_module' => $moduleId,
                    'type' => 'HEADER',
                    'category' => $header,
                    'is_reference' => key_exists('is_reference', $values) ? filter_var($values['is_reference'], FILTER_VALIDATE_BOOLEAN) : '',
                    'table' => $values['table'],
                    'field' => $values['field'],
                    'target_table' => $values['table_target'],
                    'target_field' => $values['field_target'],
                    'option' => key_exists('option', $values) ? $values['option'] : '',
                    'value' => key_exists('value', $values) ? $values['value'] : '',
                ]);
            }

            foreach ($extensions as $extension => $values) {
                $this->bookingTypeModule->createBookingTypeModule([
                    'id_booking_type' => $bookingTypeId,
                    'id_module' => $moduleId,
                    'type' => 'EXTENSION',
                    'category' => $extension,
                    'table' => $values['table'],
                    'field' => $values['field'],
                    'target_table' => $values['table_target'],
                    'target_field' => $values['field_target'],
                    'option' => key_exists('option', $values) ? $values['option'] : '',
                    'value' => key_exists('value', $values) ? $values['value'] : '',
                ]);
            }

            foreach ($containers as $container => $values) {
                $this->bookingTypeModule->createBookingTypeModule([
                    'id_booking_type' => $bookingTypeId,
                    'id_module' => $moduleId,
                    'type' => 'CONTAINER',
                    'category' => $container,
                    'is_reference' => key_exists('is_reference', $values) ? filter_var($values['is_reference'], FILTER_VALIDATE_BOOLEAN) : '',
                    'table' => $values['table'],
                    'field' => $values['field'],
                    'target_table' => $values['table_target'],
                    'target_field' => $values['field_target'],
                    'option' => key_exists('option', $values) ? $values['option'] : '',
                    'value' => key_exists('value', $values) ? $values['value'] : '',
                ]);
            }

            foreach ($goods as $good => $values) {
                $this->bookingTypeModule->createBookingTypeModule([
                    'id_booking_type' => $bookingTypeId,
                    'id_module' => $moduleId,
                    'type' => 'GOODS',
                    'category' => $good,
                    'is_reference' => key_exists('is_reference', $values) ? filter_var($values['is_reference'], FILTER_VALIDATE_BOOLEAN) : '',
                    'table' => $values['table'],
                    'field' => $values['field'],
                    'target_table' => $values['table_target'],
                    'target_field' => $values['field_target'],
                    'option' => key_exists('option', $values) ? $values['option'] : '',
                    'value' => key_exists('value', $values) ? $values['value'] : '',
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Module type setting {$module['module_name']} successfully updated", 'booking-type');
            } else {
                flash('danger', "Save module type setting {$module['module_name']} failed");
            }
        }
        $this->set_module_import($id);
    }

    /**
     * Get all booking in by specific category
     */
    public function ajax_get_by_category()
    {
        $category = $this->input->get('category');
        $bookingTypes = $this->bookingType->getBy(['ref_booking_types.category' => $category]);
        $this->render_json($bookingTypes);
    }
    /**
     * Get all branches by Customer .
     */
    public function ajax_get_customer_booking_types()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $customerId = $this->input->get('id_customer');
            if (strpos(get_active_branch('branch'), 'TPP') !== false) {
                $bookingTypes = $this->bookingType->getAllBookingTypes();
            } else {
                $bookingTypes = $this->bookingType->getBookingTypesByCustomer($customerId);
            }
            header('Content-Type: application/json');
            echo json_encode($bookingTypes);
        }
    }
}