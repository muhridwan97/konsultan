<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ItemCompliancePhotoModel extends MY_Model
{
    protected $table = 'ref_item_compliance_photos';

    /**
     * Get files by Item Compliance.
     * @param $itemId
     * @return mixed
     */
    public function getFilesByItem($itemId)
    {
        $files = $this->getBaseQuery()
            ->where('ref_item_compliance_photos.id_item', $itemId);

        return $files->get()->result_array();
    }
}