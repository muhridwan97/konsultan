<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class UploadItemPhotoFileModel extends MY_Model
{
    protected $table = 'upload_item_photo_files';

    /**
     * Get files by Item Photo.
     * @param $itemPhotoId
     * @return mixed
     */
    public function getFilesByItemPhoto($itemPhotoId)
    {
        $files = $this->getBaseQuery()
            ->where('upload_item_photo_files.id_item_photo', $itemPhotoId);

        return $files->get()->result_array();
    }
}