<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Document_type
 * @property DocumentTypeModel $documentType
 * @property S3FileManager $s3FileManager
 * @property Uploader $uploader
 * @property Exporter $exporter
 */
class Document_type extends MY_Controller
{
    /**
     * Document_type constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('modules/S3FileManager', 's3FileManager');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('modules/Uploader', 'uploader');
        $this->uploader->setDriver('s3');

        $this->setFilterMethods([
            'ajax_get_booking_document_type' => 'GET',
            'ajax_get_document_type_by_id' => 'GET'
        ]);
    }

    /**
     * Show all data document type.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DOCUMENT_TYPE_VIEW);

        $documentTypes = $this->documentType->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Document types", $documentTypes);
        } else {
            $this->render('document_type/index', compact('documentTypes'));
        }
    }

    /**
     * Show single data of document type.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DOCUMENT_TYPE_VIEW);

        $documentType = $this->documentType->getById($id);
        $allDocumentTypes = $this->documentType->getAll();

        $this->render('document_type/view', compact('documentType', 'allDocumentTypes'));
    }

    /**
     * Get document type by booking type.
     */
    public function ajax_get_booking_document_type()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $bookingTypeId = $this->input->get('id_booking_type');
            $type = $this->input->get('type');
            $documentTypes = $this->documentType->getByBookingType($bookingTypeId);
            if ($type == 'form') {
                $this->load->view('upload/form', ['documentTypes' => $documentTypes]);
            } else {
                $this->render($documentTypes);
            }
        }
    }

    /**
     * Get document type by id.
     */
    public function ajax_get_document_type_by_id()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $Id = $this->input->get('id');
            $documentType = $this->documentType->getById($Id);
            $this->render_json($documentType);
        }
    }

    /**
     * Show form create new document type
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DOCUMENT_TYPE_CREATE);

        $documentTypes = $this->documentType->getAll();

        $this->render('document_type/create', compact('documentTypes'));
    }

    /**
     * Get base validation rules.
     *
     * @param array $params
     * @return array
     */
    protected function _validation_rules(...$params)
    {
        $id = isset($params[0]) ? $params[0] : 0;
        $dir = isset($params[1]) ? $params[1] : '';
        $ruleDirectory = '';
        if (!empty($dir)) {
            $document = $this->documentType->getById($id);
            if ($document['directory'] != $dir) {
                $ruleDirectory = '|is_unique[ref_document_types.directory]';
            }
        }
        return [
            'document_type' => 'trim|required|max_length[50]',
            'directory' => 'trim|required|max_length[300]|regex_match[/^[a-zA-Z0-9-_\s]+$/]' . $ruleDirectory,
            'visibility' => 'integer',
            'description' => 'trim|max_length[300]',
        ];
    }

    /**
     * Save new document type.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DOCUMENT_TYPE_CREATE);

        if ($this->validate()) {
            $documentType = $this->input->post('document_type');
            $directory = $this->input->post('directory');
            $visibility = $this->input->post('visibility');
            $confirmation = $this->input->post('confirmation');
            $reminder = $this->input->post('reminder');
            $reminderDocumentId = $reminder != 0 ? $this->input->post('reminder_document') : 0;
            $uploadDocumentId = $reminder != 0 ? $this->input->post('upload_document') : 0;
            $reminder_overdue_day = $reminder != 0 ? $this->input->post('reminder_overdue_day') : 0;
            $description = $this->input->post('description');
            $isExpired = $this->input->post('is_expired');
            $activeDay = if_empty($this->input->post('active_day'), null);
            $emailNotification = $this->input->post('email_notification');

            $this->db->trans_start();
            //the main document
            $this->documentType->create([
                'document_type' => $documentType,
                'directory' => $directory,
                'is_visible' => $visibility,
                'is_confirm' => $confirmation,
                'is_reminder' => $reminder,
                'is_email_notification' => $emailNotification,
                'reminder_document' => $reminderDocumentId,
                'upload_document' => $uploadDocumentId,
                'reminder_overdue_day' => $reminder_overdue_day,
                'is_reserved' => false,
                'description' => $description,
                'is_expired' => $isExpired,
                'active_day' => $activeDay,
            ]);

            if ($confirmation == 1) {
                //document draft
                $this->documentType->create([
                    'document_type' => $documentType . ' Draft',
                    'directory' => $directory . '_draft',
                    'is_visible' => $visibility,
                    'is_confirm' => 0,
                    'is_reminder' => 0,
                    'is_reserved' => false,
                    'is_email_notification' => 0,
                    'reminder_overdue_day' => 0,
                    'description' => $description
                ]);
                //$this->documentType->makeFolder($directory . '_draft');
                $this->uploader->makeFolder($directory . '_draft');

                //document confirmation
                $this->documentType->create([
                    'document_type' => $documentType . ' Confirmation',
                    'directory' => $directory . '_conf',
                    'is_visible' => $visibility,
                    'is_confirm' => 0,
                    'is_reminder' => 0,
                    'is_email_notification' => 0,
                    'reminder_overdue_day' => 0,
                    'is_reserved' => false,
                    'description' => $description
                ]);
                //$this->documentType->makeFolder($directory . '_conf');
                $this->uploader->makeFolder($directory . '_conf');
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                //$this->documentType->makeFolder($directory);
                $this->uploader->makeFolder($directory);
                flash('success', "Document Type {$documentType} successfully created", 'document-type');
            } else {
                flash('danger', "Save document type {$documentType} failed");
            }
        }
        $this->create();
    }

    /**
     * Edit data document type.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DOCUMENT_TYPE_EDIT);

        $documentType = $this->documentType->getById($id);
        $explodeDocumentType = explode(' ', $documentType['document_type']);
        $lastNameDocType = array_pop($explodeDocumentType);
        $documentTypes = $this->documentType->getAll();

        if ($documentType['is_reserved']) {
            flash('danger', 'You cannot edit reserved document');
            redirect('document_type');
        }

        $this->render('document_type/edit', compact('documentType', 'lastNameDocType', 'documentTypes'));
    }

    /**
     * Update document type by specific id.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DOCUMENT_TYPE_EDIT);

        $documentType = $this->documentType->getById($id);

        if ($documentType['is_reserved']) {
            flash('danger', 'You cannot update reserved document');
            redirect('document_type');
        }

        if ($this->validate($this->_validation_rules($id, $this->input->post('directory')))) {
            $document = $this->input->post('document_type');
            $directory = $this->input->post('directory');
            $visibility = $this->input->post('visibility');
            $confirmation = $this->input->post('confirmation');
            $reminder = $this->input->post('reminder');
            $emailNotification = $this->input->post('email_notification');
            $reminderDocumentId = $reminder != 0 ? $this->input->post('reminder_document') : 0;
            $uploadDocumentId = $reminder != 0 ? $this->input->post('upload_document') : 0;
            $description = $this->input->post('description');
            $reminder_overdue_day = $reminder != 0 ? $this->input->post('reminder_overdue_day') : 0;
            $isExpired = $this->input->post('is_expired');
            $activeDay = if_empty($this->input->post('active_day'), null);

            $this->db->trans_start();
            $this->documentType->update([
                'document_type' => $document,
                'directory' => $directory,
                'is_visible' => $visibility,
                'is_confirm' => $confirmation,
                'is_reminder' => $reminder,
                'is_email_notification' => $emailNotification,
                'reminder_document' => $reminderDocumentId,
                'reminder_overdue_day' => $reminder_overdue_day,
                'upload_document' => $uploadDocumentId,
                'is_reserved' => false,
                'description' => $description,
                'is_expired' => $isExpired,
                'active_day' => $activeDay,
            ], $id);

            $docType = $this->documentType->getReservedDocumentType($documentType['document_type'] . ' Draft');
            if ($confirmation == 1 && empty($docType)) {
                //document draft
                $this->documentType->create([
                    'document_type' => $document . ' Draft',
                    'directory' => $directory . '_draft',
                    'is_visible' => $visibility,
                    'is_confirm' => 0,
                    'is_reminder' => 0,
                    'is_reserved' => false,
                    'reminder_overdue_day' => 0,
                    'is_email_notification' => 0,
                    'description' => $description
                ]);
                //$this->documentType->makeFolder($directory . '_draft');
                $this->uploader->makeFolder($directory . '_draft');

                //document confirmation
                $this->documentType->create([
                    'document_type' => $document . ' Confirmation',
                    'directory' => $directory . '_conf',
                    'is_visible' => $visibility,
                    'is_confirm' => 0,
                    'is_reserved' => false,
                    'reminder_overdue_day' => 0,
                    'is_email_notification' => 0,
                    'description' => $description
                ]);
                //$this->documentType->makeFolder($directory . '_conf');
                $this->uploader->makeFolder($directory . '_conf');

            } else if (!empty($docType)) {
                $docTypeDraft = $this->documentType->getReservedDocumentType($documentType['document_type'] . ' Draft');
                $this->documentType->update([
                    'document_type' => $document . ' Draft',
                    'directory' => $directory . '_draft',
                ], $docTypeDraft['id']);
                //$this->documentType->renameFolder($docTypeDraft['directory'], $directory . '_draft');
                $this->uploader->move(rtrim($docTypeDraft['directory'], '/') . '/', $directory . '_draft/');

                $docTypeConf = $this->documentType->getReservedDocumentType($documentType['document_type'] . ' Confirmation');
                $this->documentType->update([
                    'document_type' => $document . ' Confirmation',
                    'directory' => $directory . '_conf',
                ], $docTypeConf['id']);
                //$this->documentType->renameFolder($docTypeConf['directory'], $directory . '_conf');
                $this->uploader->move(rtrim($docTypeConf['directory'], '/') . '/', $directory . '_conf/');
            }

            $this->db->trans_complete();
            if ($this->db->trans_status()) {
                $oldDir = rtrim($documentType['directory'], '/') . '/';
                $newDir = rtrim($directory, '/') . '/';
                if ($oldDir != $newDir) {
                    //$this->documentType->renameFolder($documentType['directory'], $directory);
                    $this->uploader->move($oldDir, $newDir);
                }
                flash('success', "Document Type {$document} successfully updated", 'document-type');
            } else {
                flash('danger', "Update document type {$document} failed");
            }
        }

        $this->edit($id);
    }

    /**
     * Delete specific document type.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DOCUMENT_TYPE_DELETE);

        $documentType = $this->documentType->getById($id);
        $explodeDocumentType = explode(' ', $documentType['document_type']);
        $lastNameDocType = array_pop($explodeDocumentType);
        $docTypeName = implode(' ', $explodeDocumentType);
        $mainDocType = $this->documentType->getReservedDocumentType($docTypeName);

        if ($documentType['is_reserved']) {
            flash('danger', 'You cannot delete reserved document', 'document-type');
        }

        if (isset($mainDocType)) {
            if (($lastNameDocType == 'Draft' || $lastNameDocType == 'Confirmation') && ($mainDocType['is_confirm'] == 1)) {
                flash('danger', 'You cannot delete this document because document is being use in another process', 'document-type');
            }
        }

        if ($lastNameDocType != 'Draft' || $lastNameDocType != 'Confirmation') {
            $getAllDocTypes = $this->documentType->getAll();
            if (in_array($documentType['document_type'] . ' Draft', array_column($getAllDocTypes, 'document_type')) == true) {
                $getDocType = $this->documentType->getReservedDocumentType($documentType['document_type'] . ' Draft');
                $this->documentType->delete($getDocType['id']);
                //$this->documentType->renameFolder($getDocType['directory'], 'deleted_' . date('Ymd') . '_' . $getDocType['directory']);
                $this->uploader->move($getDocType['directory'] . '/', 'deleted_' . date('Ymd') . '_' . $getDocType['directory'] . '/');

            }
            if (in_array($documentType['document_type'] . ' Confirmation', array_column($getAllDocTypes, 'document_type')) == true) {
                $getDocType = $this->documentType->getReservedDocumentType($documentType['document_type'] . ' Confirmation');
                $this->documentType->delete($getDocType['id']);
                //$this->documentType->renameFolder($getDocType['directory'], 'deleted_' . date('Ymd') . '_' . $getDocType['directory']);
                $this->uploader->move($getDocType['directory'] . '/', 'deleted_' . date('Ymd') . '_' . $getDocType['directory'] . '/');
            }

        }

        if ($this->documentType->delete($id)) {
            //$this->documentType->renameFolder($documentType['directory'], 'deleted_' . date('Ymd') . '_' . $documentType['directory']);
            $this->uploader->move($documentType['directory'] . '/', 'deleted_' . date('Ymd') . '_' . $documentType['directory'] . '/');
            flash('warning', "Document type {$documentType['document_type']} successfully deleted");
        } else {
            flash('danger', "Delete document type {$documentType['document_type']} failed");
        }

        redirect('document-type');
    }
}