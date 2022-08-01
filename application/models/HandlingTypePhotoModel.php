<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class HandlingTypePhotoModel extends CI_Model
{
    private $table = 'ref_handling_type_photos';

    /**
     * HandlingTypePhotoModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Insert single or batch data handling type photos.
     * @param $data
     * @return bool
     */
    public function insertHandlingTypePhotos($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }


    /**
     * Delete handling type photo by specific handling type.
     * @param $handlingTypeId
     * @param bool $softDelete
     * @return mixed
     */
    public function deleteHandlingTypePhotoByHandlingType($handlingTypeId)
    {
        $deleteCondition = [
            'id_handling_type' => $handlingTypeId
        ];

        return $this->db->delete($this->table, $deleteCondition);
    }

}