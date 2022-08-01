<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UploadDocumentPartyModel extends MY_Model
{
    protected $table = 'upload_document_parties';

    /**
     * Get party by upload document
     * @param $uploadId
     * @return mixed
     */
    public function getPartyByUploadDocument($uploadId)
    {
        $files = $this->getBaseQuery()
            ->where('upload_document_parties.id_upload_document', $uploadId);

        return $files->get()->result_array();
    }
}