<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AttachmentPhotoModel extends MY_Model
{
    protected $table = 'ref_attachment_photos';

    /**
     * AttachmentPhotoModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get components by handling type.
     *
     * @param $handlingTypeId
     * @param $handlingId
     * @param bool $withTrashed
     * @return mixed
     */
    public function getByHandlingType($handlingTypeId, $handlingId = null, $withTrashed = false)
    {
        $handlingAttachment = $this->getBaseQuery()
            ->select('ref_handling_type_photos.condition')
            ->select('ref_handling_type_photos.description AS photo_description')
            ->join('ref_handling_type_photos', 'ref_attachment_photos.id = ref_handling_type_photos.id_attachment_photo')
            ->join('ref_handling_types', 'ref_handling_types.id = ref_handling_type_photos.id_handling_type')
            ->where('ref_handling_types.id', $handlingTypeId);

        // if (!empty($handlingId)) {
        //     $handlingAttachment->select([
        //         'handling_components.id_component_order',
        //         'handling_components.quantity AS handling_component_qty',
        //         'handling_components.id_unit AS handling_component_unit',
        //         'handling_components.description AS handling_component_desc',
        //         'ref_units.unit AS unit',
        //     ])
        //         ->join('(SELECT * FROM handling_components WHERE id_handling = "' . $handlingId . '") AS handling_components',
        //             'handling_components.id_component = ref_components.id', 'left')
        //         ->join('ref_units', 'ref_units.id = handling_components.id_unit', 'left');
        // }

        if (!$withTrashed) {
            $handlingAttachment->where('ref_attachment_photos.is_deleted', false);
        }

        return $handlingAttachment->get()->result_array();
    }

    /**
     * Get components by handling type.
     *
     * @param array $conditions
     * @param $handlingId
     * @param bool $withTrashed
     * @return mixed
     */
    public function getByCondition($conditions, $withTrashed = false)
    {
        $handlingAttachment = $this->getBaseQuery()
            ->select('ref_handling_type_photos.condition')
            ->select('ref_handling_type_photos.description AS photo_description')
            ->join('ref_handling_type_photos', 'ref_attachment_photos.id = ref_handling_type_photos.id_attachment_photo')
            ->join('ref_handling_types', 'ref_handling_types.id = ref_handling_type_photos.id_handling_type');
        
        if (key_exists('id_handling_type', $conditions) && !empty($conditions['id_handling_type'])) {
            $handlingAttachment->where('ref_handling_types.id', $conditions['id_handling_type']);
        }
        if (key_exists('condition', $conditions) && !empty($conditions['condition'])) {
            $handlingAttachment->where('ref_handling_type_photos.condition', $conditions['condition']);
        }
        if (!$withTrashed) {
            $handlingAttachment->where('ref_attachment_photos.is_deleted', false);
        }

        return $handlingAttachment->get()->result_array();
    }

}