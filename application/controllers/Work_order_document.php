<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Work_order_document
 * @property WorkOrderDocumentModel $workOrderDocument
 * @property WorkOrderDocumentFileModel $workOrderDocumentFile
 * @property WorkOrderModel $workOrder
 * @property Uploader $uploader
 */
class Work_order_document extends MY_Controller
{
    /**
     * Work_order_document constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('WorkOrderDocumentModel', 'workOrderDocument');
        $this->load->model('WorkOrderDocumentFileModel', 'workOrderDocumentFile');
        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('modules/Uploader', 'uploader');
        $this->load->model('LogModel', 'logHistory');

        $this->setFilterMethods([
            'ajax_get_data' => 'GET',
            'print_document' => 'GET',
            'validate_document' => 'POST|PUT|PATCH',
        ]);
    }

    /**
     * Show document data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_VIEW);
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();

        $this->render('workorder_document/index', [], 'Job Documents');
    }

    /**
     * Get ajax paging data job document.
     */
    public function ajax_get_data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_VIEW);

        $filters = [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ];

        $data = $this->workOrderDocument->getAll($filters);

        $this->render_json($data);
    }

    /**
     * Show detail job documents.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_VIEW);

        $document = $this->workOrderDocument->getById($id);
        $files = $this->workOrderDocumentFile->getBy(['id_work_order_document' => $id]);
        $workOrders = $this->workOrder->getWorkOrderSummary(['from_date' => $document['date'], 'to_date' => $document['date']]);

        $this->render('workorder_document/view', compact('document', 'files', 'workOrders'));
    }

    /**
     * Print document data.
     *
     * @param $id
     */
    public function print_document($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_PRINT);

        $document = $this->workOrderDocument->getById($id);
        $files = $this->workOrderDocumentFile->getBy(['id_work_order_document' => $id]);

        $this->layout = 'template/print';

        $this->render('workorder_document/_print', compact('document', 'files'));
    }

    /**
     * Show form create work order document.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_CREATE);

        $this->render('workorder_document/create', [], 'Create Job Document');
    }

    /**
     * Save work order document
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_CREATE);

        if ($this->validate()) {
            $branchId = $this->input->post('branch');
            $date = $this->input->post('date');
            $description = $this->input->post('description');
            $files = $this->input->post('input_files_uploaded[]');

            $this->db->trans_start();

            $this->workOrderDocument->create([
                'id_branch' => $branchId,
                'date' => sql_date_format($date, false),
                'status' => WorkOrderDocumentModel::STATUS_PENDING,
                'description' => $description,
                'type' => WorkOrderDocumentModel::TYPE_JOB_SUMMARY,
            ]);
            $documentId = $this->db->insert_id();

            foreach ($files as $file) {
                if (!empty($file)) {
                    $sourceFile = 'temp/' . $file;
                    $destFile = 'job-documents/' . format_date($date, 'Y/m/d') . '/' . $file;
                    if ($this->uploader->move($sourceFile, $destFile)) {
                        $this->workOrderDocumentFile->create([
                            'id_work_order_document' => $documentId,
                            'source' => $destFile
                        ]);
                    }
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Document job {$date} successfully created", 'work-order-document');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->create();
    }

    /**
     * Show form edit work order document.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_EDIT);

        $document = $this->workOrderDocument->getById($id);
        $files = $this->workOrderDocumentFile->getBy(['id_work_order_document' => $id]);
        $files = array_column($files, 'source');

        $this->render('workorder_document/edit', compact('document', 'files'), 'Edit Job Document');
    }

    /**
     * Update work order document.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_EDIT);

        if ($this->validate($this->_validation_rules($id))) {
            $branchId = $this->input->post('branch');
            $date = $this->input->post('date');
            $description = $this->input->post('description');
            $filesOld = $this->input->post('input_files_uploaded_old[]');
            $files = $this->input->post('input_files_uploaded[]');

            $this->db->trans_start();

            $this->workOrderDocument->update([
                'id_branch' => $branchId,
                'date' => sql_date_format($date, false),
                'status' => WorkOrderDocumentModel::STATUS_PENDING,
                'description' => $description,
            ], $id);

            $documentFiles = $this->workOrderDocumentFile->getBy(['id_work_order_document' => $id]);
            $documentFileNames = array_column($documentFiles, 'source');
            $deletedFiles = array_diff($documentFileNames, if_empty($filesOld, []));
            foreach ($deletedFiles as $file) {
                if (!empty($file)) {
                    $this->workOrderDocumentFile->delete([
                        'id_work_order_document' => $id,
                        'source' => $file
                    ]);
                    $this->uploader->delete($file);
                }
            }

            foreach ($files as $file) {
                if (!empty($file)) {
                    $sourceFile = 'temp/' . $file;
                    $destFile = 'job-documents/' . format_date($date, 'Y/m/d') . '/' . $file;
                    if ($this->uploader->move($sourceFile, $destFile)) {
                        $this->workOrderDocumentFile->create([
                            'id_work_order_document' => $id,
                            'source' => $destFile
                        ]);
                    }
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Document job {$date} successfully updated", 'work-order-document');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->edit($id);
    }

    /**
     * @param array $params
     * @return array
     */
    protected function _validation_rules(...$params)
    {
        $id = isset($params[0]) ? $params[0] : 0;

        $branch = $this->input->post('branch');

        return [
            'branch' => 'trim|required|integer|is_natural_no_zero',
            'date' => [
                'trim', 'required', 'max_length[25]', ['date_exists', function ($date) use ($id, $branch) {
                    $this->form_validation->set_message('date_exists', 'The document date %s has been created before!');
                    $document = $this->workOrderDocument->getBy([
                        'id_branch' => $branch,
                        'date' => sql_date_format($date, false),
                    ], true);
                    return empty($document) || !empty($id);
                }]
            ],
            'description' => 'trim|max_length[500]',
            'input_files_uploaded[]' => 'required',
        ];
    }

    /**
     * Validate document data.
     *
     * @param $type
     * @param $id
     */
    public function validate_document($type, $id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_VALIDATE);

        if ($this->validate(['type' => 'in_list[approve,reject]'], ['type' => $type])) {

            $this->db->trans_start();

            $document = $this->workOrderDocument->getById($id);

            if ($type == 'approve') {
                $type = WorkOrderDocumentModel::STATUS_APPROVED;
            } else {
                $type = WorkOrderDocumentModel::STATUS_REJECTED;
            }

            $this->workOrderDocument->update([
                'status' => $type,
                'validated_by' => UserModel::authenticatedUserData('id'),
                'validated_at' => sql_date_format('now')
            ], $id);

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Job document date {$document['date']} is successfully {$type}");
            } else {
                flash('danger', 'Validating document failed');
            }
        }
        if (!empty(get_url_param('redirect'))) {
            redirect(get_url_param('redirect'), false);
        } else {
            redirect('work-order-document');
        }
    }

    /**
     * Perform deleting document data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_DELETE);

        $document = $this->workOrderDocument->getById($id);

        if ($this->workOrderDocument->delete($id, true)) {
            flash('warning', "Job document date {$document['date']} is successfully deleted");
        } else {
            flash('danger', "Delete job document {$document['date']} failed");
        }
        redirect('work-order-document');
    }
}