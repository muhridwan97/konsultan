<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UploadDocumentFileModel extends MY_Model
{
    protected $table = 'upload_document_files';

    /**
     * FileModel constructor.
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
        $uploads = $this->db
            ->select([
                'upload_document_files.*',
                'upload_documents.no_document',
                'upload_documents.document_date',
                'upload_documents.id_document_type',
                'ref_document_types.document_type',
                'ref_document_types.directory',
                'prv_users.name AS uploader',

            ])
            ->from('upload_document_files')
            ->join('upload_documents', 'upload_document_files.id_upload_document = upload_documents.id')
            ->join(UserModel::$tableUser, 'prv_users.id = upload_document_files.created_by', 'left')
            ->join('ref_document_types', 'upload_documents.id_document_type = ref_document_types.id');

        return $uploads;
    }

    /**
     * Get all files of documents.
     * @return array
     */
    public function getAllFiles()
    {
        return $this->getBaseQuery()
            ->get()->result_array();
    }

    /**
     * Get specific file by id.
     * @param $id
     * @return mixed
     */
    public function getFileById($id)
    {
        return $this->getBaseQuery()
            ->where('upload_document_files.id', $id)
            ->get()->row_array();
    }

    /**
     * Get files by document.
     * @param $documentId
     * @return mixed
     */
    public function getFilesByDocument($documentId)
    {
        $files = $this->getBaseQuery()
            ->where('upload_document_files.id_upload_document', $documentId);

        return $files->get()->result_array();
    }

    /**
     * Get files by bookings.
     *
     * @param $bookingId
     * @param null $documentType
     * @return array
     */
    public function getFilesByBooking($bookingId, $documentType = null)
    {
        $files = $this->getBaseQuery()
            ->join('uploads', 'uploads.id = upload_documents.id_upload')
            ->join('bookings', 'bookings.id_upload = uploads.id')
            ->where('bookings.id', $bookingId);

        if(!empty($documentType)) {
            $files->where('upload_documents.id_document_type', $documentType);
        }

        return $files->get()->result_array();
    }

    /**
     * Create new file document.
     * @param $data
     */
    public function createUploadDocumentFile($data)
    {
        return $this->db->insert($this->table, $data);
    }


    /**
     * Update document file.
     * @param $data
     * @param $id
     */
    public function updateUploadDocumentFile($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Upload file to temporary.
     * @param $file
     * @param null $fileName
     * @param string $location
     * @param null $fileType
     * @return array
     */
    public function uploadTo($file, $fileName = null, $location = null, $fileType = null)
    {
        $config['upload_path'] = is_null($location) ? FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'temp' : $location;
        $config['allowed_types'] = is_null($fileType) ? 'gif|jpg|jpeg|png|pdf|xls|xlsx|doc|docx|ppt|pptx|txt|zip|rar' : $fileType;
        $config['max_size'] = 3000;
        $config['max_width'] = 5000;
        $config['max_height'] = 5000;
        $config['file_ext_tolower'] = true;
        if (!empty($fileName)) {
            $config['file_name'] = $fileName;
        }

        $this->load->library('upload', $config);

        $errors = [];
        $data = [];
        
        if (is_array($_FILES[$file]['name'])) {
            $totalUploads = count($_FILES[$file]['name']);
            $status = true;
            for ($i = 0; $i < $totalUploads; $i++) {
                $_FILES[$file . '_multiple']['name'] = $_FILES[$file]['name'][$i];
                $_FILES[$file . '_multiple']['type'] = $_FILES[$file]['type'][$i];
                $_FILES[$file . '_multiple']['tmp_name'] = $_FILES[$file]['tmp_name'][$i];
                $_FILES[$file . '_multiple']['error'] = $_FILES[$file]['error'][$i];
                $_FILES[$file . '_multiple']['size'] = $_FILES[$file]['size'][$i];

                if ($this->upload->do_upload($file . '_multiple')) {
                    $data[] = $this->upload->data();
                } else {
                    $errors = $this->upload->display_errors();
                    $status = false;
                    break;
                }
            }
        } else {
            if ($this->upload->do_upload($file)) {
                $data = $this->upload->data();
                $status = true;
            } else {
                $errors = $this->upload->display_errors();
                $status = false;
            }
        }

        return [
            'status' => $status,
            'errors' => $errors,
            'data' => $data
        ];
    }

      /**
     * Copy file from one location to another.
     * @param $from
     * @param $to
     * @return bool
     */
    public function copyTo($from, $to)
    {
        return copy($from, $to);
    }

    /**
     * Move file from one location to another.
     * @param $from
     * @param $to
     * @return bool
     */
    public function moveTo($from, $to)
    {
        return rename($from, $to);
    }

    /**
     * Delete file
     * @param $file
     * @param null $base
     * @return bool
     */
    public function deleteFile($file, $base = null)
    {
        $directory = is_null($base) ? FCPATH . 'uploads' . DIRECTORY_SEPARATOR . 'temp' : $base;
        $filePath = $directory . DIRECTORY_SEPARATOR . $file;
        if (file_exists($filePath)) {
            return unlink($filePath);
        }
        return true;
    }

    /**
     * Delete document data.
     * @param $id
     */
    public function deleteUploadDocumentFile($id)
    {
        return $this->db->delete($this->table, ['id' => $id]);
    }

}