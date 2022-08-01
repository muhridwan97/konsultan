<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Work_order_document_file
 * @property WorkOrderDocumentFileModel $workOrderDocumentFile
 * @property Uploader $uploader
 */
class Work_order_document_file extends MY_Controller
{
    /**
     * Auction constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('WorkOrderDocumentFileModel', 'workOrderDocumentFile');
        $this->load->model('modules/Uploader', 'uploader');
    }

    /**
     * Perform deleting document file data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_DELETE);

        $file = $this->workOrderDocumentFile->getById($id);

        $filename = basename($file['source']);

        if ($this->workOrderDocumentFile->delete($id, true)) {
            flash('warning', "Job document file {$filename} is successfully deleted");
        } else {
            flash('danger', "Delete job document file {$filename} failed");
        }
        redirect('work-order-document/view/' . $file['id_work_order_document']);
    }
}