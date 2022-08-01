<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ComponentModel extends MY_Model
{
    protected $table = 'ref_components';

    /**
     * ComponentModel constructor.
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
        $branchId = get_active_branch('id');

        $handlingTools = $this->getBaseQuery()
            ->select('ref_handling_type_components.description AS component_description')
            ->select('ref_handling_type_components.default_value')
            ->join('ref_handling_type_components', 'ref_components.id = ref_handling_type_components.id_component')
            ->join('ref_handling_types', 'ref_handling_types.id = ref_handling_type_components.id_handling_type')
            ->where('ref_handling_types.id', $handlingTypeId);

        if (!empty($handlingId)) {
            $handlingTools->select([
                'handling_components.id_component_order',
                'handling_components.quantity AS handling_component_qty',
                'handling_components.id_unit AS handling_component_unit',
                'handling_components.description AS handling_component_desc',
                'ref_units.unit AS unit',
            ])
                ->join('(SELECT * FROM handling_components WHERE id_handling = "' . $handlingId . '") AS handling_components',
                    'handling_components.id_component = ref_components.id', 'left')
                ->join('ref_units', 'ref_units.id = handling_components.id_unit', 'left');
        }

        if (!$withTrashed) {
            $handlingTools->where('ref_components.is_deleted', false);
        }

        if (!empty($branchId)) {
            $handlingTools->where('ref_handling_type_components.id_branch', $branchId);
        }

        return $handlingTools->get()->result_array();
    }

}