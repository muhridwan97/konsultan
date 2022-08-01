<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Component_order
 * @property ComponentOrderModel $componentOrder
 * @property ComponentModel $component
 * @property DocumentTypeModel $documentType
 * @property UploadDocumentFileModel $uploadDocumentFile
 */
class Component_order extends CI_Controller
{
    /**
     * Component order constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('ComponentOrderModel', 'componentOrder');
        $this->load->model('ComponentModel', 'component');
        $this->load->model('DocumentTypeModel', 'documentType');
        $this->load->model('UploadDocumentFileModel', 'uploadDocumentFile');
    }

    /**
     * Show handling component transaction data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_COMPONENT_TRANSACTION_VIEW);

        $componentOrders = $this->componentOrder->getAllComponentOrders();

        $data = [
            'title' => "Component Orders",
            'subtitle' => "Data transaction",
            'page' => "component_order/index",
            'componentOrders' => $componentOrders
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show detail payment.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_COMPONENT_TRANSACTION_VIEW);

        $componentOrder = $this->componentOrder->getComponentOrderById($id);
        $data = [
            'title' => "Component Orders",
            'subtitle' => "View transaction",
            'page' => "component_order/view",
            'componentOrder' => $componentOrder
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show create payment form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_CREATE);

        $data = [
            'title' => "Component Transaction",
            'subtitle' => "Create transaction",
            'page' => "component_order/create",
            'components' => $this->component->getAll()
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Save new payment.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_CREATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('branch', 'Branch', 'trim|required');
            $this->form_validation->set_rules('component', 'Handling component', 'trim|required');
            $this->form_validation->set_rules('no_transaction', 'No transaction', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('option', 'Item option', 'trim|max_length[50]');
            $this->form_validation->set_rules('transaction_date', 'Transaction date', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('quantity', 'Quantity', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('amount', 'Amount', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('description', 'Transaction description', 'trim|required|max_length[500]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $branch = $this->input->post('branch');
                $component = $this->input->post('component');
                $noTransaction = $this->input->post('no_transaction');
                $option = $this->input->post('option');
                $transactionDate = sql_date_format($this->input->post('transaction_date'));
                $quantity = $this->input->post('quantity');
                $amount = extract_number($this->input->post('amount'));
                $description = $this->input->post('description');

                // upload attachment if exist
                $fileName = '';
                $uploadPassed = true;
                if (!empty($_FILES['attachment']['name'])) {
                    $fileName = 'TR_' . time() . '_' . rand(100, 999);
                    $saveTo = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'component_orders';
                    if ($this->documentType->makeFolder('component_orders')) {
                        $upload = $this->uploadDocumentFile->uploadTo('attachment', $fileName, $saveTo);
                        if (!$upload['status']) {
                            $uploadPassed = false;
                            flash('warning', $upload['errors']);
                        } else {
                            $fileName = $upload['data']['file_name'];
                        }
                    } else {
                        $uploadPassed = false;
                        flash('warning', 'Making folder upload failed, try again');
                    }
                }

                if ($uploadPassed) {
                    $save = $this->componentOrder->createComponentOrder([
                        'id_branch' => $branch,
                        'id_component' => $component,
                        'no_transaction' => $noTransaction,
                        'option' => $option,
                        'transaction_date' => $transactionDate,
                        'quantity' => $quantity,
                        'amount' => $amount,
                        'status' => 'DRAFT',
                        'attachment' => $fileName,
                        'description' => $description
                    ]);

                    if ($save) {
                        flash('success', "Transaction component <strong>{$noTransaction}</strong> successfully created");
                        redirect("component_order");
                    } else {
                        flash('danger', "Save transaction component <strong>{$noTransaction}</strong> failed, try again or contact administrator");
                    }
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->create();
    }

    /**
     * Show edit handling component transaction form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_EDIT);

        $componentOrder = $this->componentOrder->getComponentOrderById($id);
        $components = $this->component->getAll();
        $data = [
            'title' => "Component Transaction",
            'subtitle' => "Edit transaction",
            'page' => "component_order/edit",
            'componentTransaction' => $componentOrder,
            'components' => $components
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Update data payment by id.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PAYMENT_EDIT);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Payment data', 'trim|required|integer');
            $this->form_validation->set_rules('branch', 'Branch', 'trim|required');
            $this->form_validation->set_rules('component', 'Handling component', 'trim|required');
            $this->form_validation->set_rules('no_transaction', 'No transaction', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('option', 'Item option', 'trim|max_length[50]');
            $this->form_validation->set_rules('transaction_date', 'Transaction date', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('quantity', 'Quantity', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('amount', 'Amount', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('description', 'Transaction description', 'trim|required|max_length[500]');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $id = $this->input->post('id');
                $branch = $this->input->post('branch');
                $component = $this->input->post('component');
                $noTransaction = $this->input->post('no_transaction');
                $option = $this->input->post('option');
                $transactionDate = sql_date_format($this->input->post('transaction_date'));
                $quantity = $this->input->post('quantity');
                $amount = extract_number($this->input->post('amount'));
                $description = $this->input->post('description');

                $componentOrder = $this->componentOrder->getComponentOrderById($id);

                // upload attachment if exist, set default old name just in case the attachment does not change
                $fileName = $componentOrder['attachment'];
                $uploadPassed = true;
                if (!empty($_FILES['attachment']['name'])) {
                    // setup location and file name
                    $fileName = 'SF_' . time() . '_' . rand(100, 999);
                    $saveTo = FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'component_orders';

                    // find or create base folder
                    if ($this->documentType->makeFolder('component_orders')) {
                        // try upload with standard config
                        $upload = $this->uploadDocumentFile->uploadTo('attachment', $fileName, $saveTo);
                        if (!$upload['status']) {
                            $uploadPassed = false;
                            flash('warning', $upload['errors']);
                        } else {
                            // delete old file
                            if (!empty($fileName)) {
                                $this->uploadDocumentFile->deleteFile($componentOrder['attachment'], $saveTo);
                            }
                            // put new file name
                            $fileName = $upload['data']['file_name'];
                        }
                    } else {
                        $uploadPassed = false;
                        flash('warning', 'Folder component_transactions is missing or failed to be created, try again');
                    }
                }

                if ($uploadPassed) {
                    $status = $componentOrder['status'];
                    if ($status == 'REJECTED') {
                        $status = 'DRAFT';
                    }
                    $update = $this->componentOrder->updateComponentOrder([
                        'id_branch' => $branch,
                        'id_component' => $component,
                        'no_transaction' => $noTransaction,
                        'option' => $option,
                        'transaction_date' => $transactionDate,
                        'quantity' => $quantity,
                        'amount' => $amount,
                        'status' => $status,
                        'attachment' => $fileName,
                        'description' => $description
                    ], $id);

                    if ($update) {
                        flash('success', "Component transaction <strong>{$noTransaction}</strong> successfully updated");
                        redirect("component_order");
                    } else {
                        flash('danger', "Update component transaction <strong>{$noTransaction}</strong> failed, try again or contact administrator");
                    }
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->edit($id);
    }

    /**
     * Perform deleting handling component transaction data.
     */
    public function delete()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_COMPONENT_TRANSACTION_DELETE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Component transaction data', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $componentOrderId = $this->input->post('id');

                $componentOrder = $this->componentOrder->getComponentOrderById($componentOrderId);
                $delete = $this->componentOrder->deleteComponentOrder($componentOrderId);

                if ($delete) {
                    flash('warning', "Component transaction <strong>{$componentOrder['no_transaction']}</strong> successfully deleted");
                } else {
                    flash('danger', "Delete component transaction <strong>{$componentOrder['no_transaction']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('component_order');
    }

    /**
     * Perform deleting handling component transaction data.
     */
    public function void()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_COMPONENT_TRANSACTION_DELETE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Component transaction data', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $componentOrderId = $this->input->post('id');

                $componentOrder = $this->componentOrder->getComponentOrderById($componentOrderId);
                $void = $this->componentOrder->updateComponentOrder([
                    'is_void' => 1
                ], $componentOrderId);

                if ($void) {
                    flash('warning', "Component transaction <strong>{$componentOrder['no_transaction']}</strong> successfully deleted");
                } else {
                    flash('danger', "Delete component transaction <strong>{$componentOrder['no_transaction']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('component_order');
    }

    /**
     * Validate document (approve/reject)
     */
    public function validate()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HANDLING_COMPONENT_TRANSACTION_VALIDATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Component transaction data', 'trim|required|integer');
            $this->form_validation->set_rules('status', 'Transaction status', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $componentOrderId = $this->input->post('id');
                $statusValidation = $this->input->post('status');

                $componentOrder = $this->componentOrder->getComponentOrderById($componentOrderId);
                $status = $this->componentOrder->updateComponentOrder([
                    'status' => $statusValidation
                ], $componentOrderId);

                if ($status) {
                    $statusText = 'rejected';
                    $statusClass = 'warning';
                    if ($statusValidation == 'APPROVED') {
                        $statusText = 'approved';
                        $statusClass = 'success';
                    }
                    flash($statusClass, "Component transaction <strong>{$componentOrder['no_transaction']}</strong> successfully <strong>{$statusText}</strong>");
                } else {
                    flash('danger', "Validating component transaction <strong>{$componentOrder['no_transaction']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('component_order');
    }

}