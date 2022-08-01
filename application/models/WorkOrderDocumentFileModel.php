<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WorkOrderDocumentFileModel extends MY_Model
{
    protected $table = 'work_order_document_files';

    /**
     * Get latest upload data.
     *
     * @return array
     */
    public function getLatestDocument()
    {
        return $this->getBaseQuery()
            ->order_by('created_at', 'desc')
            ->limit(1)
            ->get()
            ->row_array();
    }
}