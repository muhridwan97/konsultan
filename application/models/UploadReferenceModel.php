<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UploadReferenceModel extends MY_Model
{
    protected $table = 'upload_references';

    /**
     * UploadReferenceModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        $baseQuery = parent::getBaseQuery()
            ->select([
                'uploads.no_upload',
                'upload_refs.no_upload AS no_upload_reference'
            ])
            ->join('uploads', 'uploads.id = upload_references.id_upload')
            ->join('uploads AS upload_refs', 'upload_refs.id = upload_references.id_upload_reference');

        if (!empty(if_empty($branchId, get_active_branch_id()))) {
            $baseQuery->where('uploads.id_branch', if_empty($branchId, get_active_branch_id()));
        }

        return $baseQuery;
    }

    /**
     * Delete upload reference by upload id.
     * @param $teptId
     * @return mixed
     */
    public function deleteUploadReferenceByUpload($uploadId)
    {
        return $this->db->delete($this->table, ['id_upload' => $uploadId]);
    }
}
