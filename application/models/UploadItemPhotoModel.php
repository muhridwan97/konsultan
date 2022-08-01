<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UploadItemPhotoModel extends MY_Model
{
    protected $table = 'upload_item_photos';

    const STATUS_ON_REVIEW = 'ON REVIEW';
    const STATUS_VALIDATED = 'VALIDATED';
    const STATUS_REJECTED = 'REJECTED';

    /**
     * UploadDocumentModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active record query builder for all related document data selection.
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        $uploads = $this->db
            ->select([
                'upload_item_photos.*',
                'uploads.no_upload',
                'ref_booking_types.category',
                'uploads.description AS upload_description',
                'ref_people.id AS id_customer',
                'ref_people.name AS customer_name',
                'IFNULL(COUNT(upload_item_photo_files.id), 0) AS total_file',
                'prv_users.name AS created_name',
                'ref_item_compliances.item_name AS item_name_master',
                'ref_item_compliances.no_hs AS no_hs_master',
                'IFNULL(COUNT(ref_item_compliance_photos.id), 0) AS total_file_master',
            ])
            ->from('upload_item_photos')
            ->join('uploads', 'uploads.id = upload_item_photos.id_upload', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = uploads.id_booking_type', 'left')
            ->join('ref_people', 'ref_people.id = uploads.id_person', 'left')
            ->join('upload_item_photo_files', 'upload_item_photo_files.id_item_photo = upload_item_photos.id', 'left')
            ->join('ref_item_compliances', 'ref_item_compliances.id = upload_item_photos.id_item', 'left')
            ->join('ref_item_compliance_photos', 'ref_item_compliance_photos.id_item = ref_item_compliances.id', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = upload_item_photos.created_by', 'left')
            ->group_by('upload_item_photos.id');

        return $uploads;
    }

    /**
     * Get all documents with or without trashed records.
     * @param bool $withTrashed
     * @return array
     */
    public function getAllPhotos($withTrashed = false)
    {
        $photos = $this->getBaseQuery();

        if ($withTrashed) {
            return $photos->get()->result_array();
        }

        return $photos
            ->where('upload_item_photos.is_deleted', false)->get()
            ->result_array();
    }

    /**
     * Get upload data by id.
     * @param integer $id
     * @param bool $withTrashed
     * @return array
     */
    public function getPhotoById($id, $withTrashed = false)
    {
        $documents = $this->getBaseQuery();

        if ($withTrashed) {
            return $documents->where(['upload_item_photos.id' => $id])->get()->row_array();
        }

        return $documents
            ->where([
                'upload_item_photos.is_deleted' => false,
                'upload_item_photos.id' => $id
            ])->get()
            ->row_array();
    }

    /**
     * @param integer $uploadId
     * @param bool $withPhoto
     * @param bool $withTrashed
     * @return mixed
     */
    public function getPhotosByUpload($uploadId, $withPhoto = true, $withTrashed = false)
    {
        $documents = $this->getBaseQuery()->where(['upload_item_photos.id_upload' => $uploadId]);

        if (!$withPhoto) {
            $documents->having(['total_file' => 0]);
            $documents->having(['total_file_master' => 0]);
        }else{
            $documents->having(['total_file > ' => 0]);
            $documents->or_having(['total_file_master > ' => 0]);
        }

        if ($withTrashed) {
            return $documents->get()->result_array();
        }

        return $documents
            ->where(['upload_item_photos.is_deleted' => false])
            ->get()->result_array();
    }

     /**
     * @param integer $uploadId
     * @param null $isResponse
     * @param bool $withTrashed
     * @return mixed
     */
    public function getPhotosByUploadByDocumentType($uploadId, $documentTypeId, $isResponse = null, $withTrashed = false, $document_type = null)
    {

        if(!is_null($document_type)){
            $documents = $this->getBaseQuery()->where(['upload_item_photos.id_upload' => $uploadId, 'ref_document_types.document_type' => $document_type]);
        }else{
            $documents = $this->getBaseQuery()->where(['upload_item_photos.id_upload' => $uploadId, 'upload_item_photos.id_document_type' => $documentTypeId]);
        }

        if (!is_null($isResponse)) {
            if ($isResponse) {
                $documents->where(['upload_item_photos.is_response' => true]);
            } else {
                $documents->where(['upload_item_photos.is_response' => false]);
            }
        }

        if ($withTrashed) {
            return $documents->get()->row_array();
        }

        return $documents
            ->where(['upload_item_photos.is_deleted' => false])
            ->get()->row_array();
    }

    /**
     * @param integer $uploadId
     * @param bool $withTrashed
     * @return mixed
     */
    public function getLastResponseDocumentsByUpload($uploadId, $withTrashed = false)
    {
        $documents = $this->getBaseQuery()->where(['upload_item_photos.id_upload' => $uploadId]);

        $documents->where(['upload_item_photos.is_response' => true]);

        if ($withTrashed) {
            return $documents->order_by('upload_item_photos.id', 'DESC')->get()->row_array();
        }

        return $documents
            ->where(['upload_item_photos.is_deleted' => false])
            ->order_by('upload_item_photos.id', 'DESC')
            ->get()->row_array();
    }

    /**
     * Get  all documents by booking id with or without deleted records.
     * @param $id
     * @param null $documentTypeId
     * @param bool $withTrashed
     * @return array
     */
    public function getPhotosByBooking($id, $documentTypeId = null, $withTrashed = false)
    {
        $documents = $this->db->select([
            'upload_item_photos.id',
            'upload_item_photos.no_document',
            'upload_item_photos.document_date',
            'ref_document_types.document_type',
            'upload_item_photos.is_valid',
            'upload_item_photos.is_valid',
            'upload_item_photos.validated_at',
            'validators.name AS validator',
            'uploaders.name AS uploader',
            'upload_item_photos.created_at',
        ])
            ->from('bookings')
            ->join('upload_item_photos', 'upload_item_photos.id_upload = bookings.id_upload')
            ->join('ref_document_types', 'ref_document_types.id = upload_item_photos.id_document_type')
            ->join(UserModel::$tableUser . ' AS validators', 'validators.id = upload_item_photos.validated_by', 'left')
            ->join(UserModel::$tableUser . ' AS uploaders', 'uploaders.id = upload_item_photos.created_by', 'left')
            ->where('bookings.id', $id);

        if (!$withTrashed) {
            $documents->where('upload_item_photos.is_deleted', false);
        }

        if(!empty($documentTypeId)) {
            $documents->where('upload_item_photos.id_document_type', $documentTypeId);
            return $documents->get()->row_array();
        }

        return $documents->get()->result_array();
    }

}