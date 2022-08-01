<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UploadDocumentModel extends MY_Model
{
    protected $table = 'upload_documents';

    /**
     * UploadDocumentModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active record query builder for all related document data selection.
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return $this->db
            ->select([
                'upload_documents.*',
                'uploads.no_upload',
                'ref_booking_types.category',
                'uploads.description AS upload_description',
                'ref_people.name AS customer_name',
                'ref_document_types.document_type',
                'ref_document_types.directory',
                'IFNULL(COUNT(upload_document_files.id), 0) AS total_file',
                'MAX(upload_document_files.created_at) AS last_upload_file',
                'prv_users.name AS created_name',
            ])
            ->from('upload_documents')
            ->join('uploads', 'uploads.id = upload_documents.id_upload', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_people', 'ref_people.id = uploads.id_person', 'left')
            ->join('upload_document_files', 'upload_document_files.id_upload_document = upload_documents.id', 'left')
            ->join('ref_document_types', 'upload_documents.id_document_type = ref_document_types.id', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = upload_documents.created_by', 'left')
            ->group_by('upload_documents.id');
    }

    /**
     * Get all documents with or without trashed records.
     * @param bool $withTrashed
     * @return array
     */
    public function getAllDocuments($withTrashed = false)
    {
        $documents = $this->getBaseQuery();

        if ($withTrashed) {
            return $documents->get()->result_array();
        }

        return $documents
            ->where('upload_documents.is_deleted', false)->get()
            ->result_array();
    }

    /**
     * Get upload data by id.
     * @param integer $id
     * @param bool $withTrashed
     * @return array
     */
    public function getDocumentById($id, $withTrashed = false)
    {
        $documents = $this->getBaseQuery();

        if ($withTrashed) {
            return $documents->where(['upload_documents.id' => $id])->get()->row_array();
        }

        return $documents
            ->select([
                'prv_users.user_type AS user_type',
            ])
            ->where([
                'upload_documents.is_deleted' => false,
                'upload_documents.id' => $id
            ])->get()
            ->row_array();
    }

    /**
     * @param integer $uploadId
     * @param null $isResponse
     * @param bool $withTrashed
     * @return mixed
     */
    public function getDocumentsByUpload($uploadId, $isResponse = null, $withTrashed = false)
    {
        $documents = $this->getBaseQuery()->where(['upload_documents.id_upload' => $uploadId]);

        if (!is_null($isResponse)) {
            if ($isResponse) {
                $documents->where(['upload_documents.is_response' => true]);
            } else {
                $documents->where(['upload_documents.is_response' => false]);
            }
        }

        if ($withTrashed) {
            return $documents->get()->result_array();
        }

        return $documents
            ->where(['upload_documents.is_deleted' => false])
            ->get()->result_array();
    }

     /**
     * @param integer $uploadId
     * @param null $isResponse
     * @param bool $withTrashed
     * @return mixed
     */
    public function getDocumentsByUploadByDocumentType($uploadId, $documentTypeId, $isResponse = null, $withTrashed = false, $document_type = null)
    {

        if(!is_null($document_type)){
            $documents = $this->getBaseQuery()->where(['upload_documents.id_upload' => $uploadId, 'ref_document_types.document_type' => $document_type]);
        }else{
            $documents = $this->getBaseQuery()->where(['upload_documents.id_upload' => $uploadId, 'upload_documents.id_document_type' => $documentTypeId]);
        }

        if (!is_null($isResponse)) {
            if ($isResponse) {
                $documents->where(['upload_documents.is_response' => true]);
            } else {
                $documents->where(['upload_documents.is_response' => false]);
            }
        }

        if ($withTrashed) {
            return $documents->get()->row_array();
        }

        return $documents
            ->where(['upload_documents.is_deleted' => false])
            ->get()->row_array();
    }

    /**
     * @param integer $uploadId
     * @param bool $withTrashed
     * @return mixed
     */
    public function getLastResponseDocumentsByUpload($uploadId, $withTrashed = false)
    {
        $documents = $this->getBaseQuery()->where(['upload_documents.id_upload' => $uploadId]);

        $documents->where(['upload_documents.is_response' => true]);

        if ($withTrashed) {
            return $documents->order_by('upload_documents.id', 'DESC')->get()->row_array();
        }

        return $documents
            ->where(['upload_documents.is_deleted' => false])
            ->order_by('upload_documents.id', 'DESC')
            ->get()->row_array();
    }

    /**
     * Get  all documents by booking id with or without deleted records.
     * @param $id
     * @param null $documentTypeId
     * @param bool $withTrashed
     * @return array
     */
    public function getDocumentsByBooking($id, $documentTypeId = null, $withTrashed = false)
    {
        $documents = $this->db->select([
            'upload_documents.id',
            'upload_documents.no_document',
            'upload_documents.document_date',
            'ref_document_types.document_type',
            'upload_documents.is_valid',
            'upload_documents.is_valid',
            'upload_documents.validated_at',
            'validators.name AS validator',
            'uploaders.name AS uploader',
            'upload_documents.created_at',
        ])
            ->from('bookings')
            ->join('upload_documents', 'upload_documents.id_upload = bookings.id_upload')
            ->join('ref_document_types', 'ref_document_types.id = upload_documents.id_document_type')
            ->join(UserModel::$tableUser . ' AS validators', 'validators.id = upload_documents.validated_by', 'left')
            ->join(UserModel::$tableUser . ' AS uploaders', 'uploaders.id = upload_documents.created_by', 'left')
            ->where('bookings.id', $id);

        if (!$withTrashed) {
            $documents->where('upload_documents.is_deleted', false);
        }

        if(!empty($documentTypeId)) {
            $documents->where('upload_documents.id_document_type', $documentTypeId);
            return $documents->get()->row_array();
        }

        return $documents->get()->result_array();
    }

    /**
     * Create new upload.
     * @param $data
     */
    public function createUploadDocument($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update document data.
     * @param $data
     * @param $id
     */
    public function updateUploadDocument($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete document data.
     * @param $id
     * @param bool $softDelete
     */
    public function deleteUploadDocument($id, $softDelete = true)
    {
        if ($softDelete) {
            return $this->db->update($this->table, [
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s')
            ], ['id' => $id]);
        }
        return $this->db->delete($this->table, ['id' => $id]);
    }

}