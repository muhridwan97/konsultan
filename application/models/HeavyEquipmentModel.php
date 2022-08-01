<?php
defined('BASEPATH') or exit('No direct script access allowed');

class HeavyEquipmentModel extends MY_Model
{
    protected $table = 'ref_heavy_equipments';

    public function __construct()
    {
        parent::__construct();

        $this->addFilteredMap('branch', function (CI_DB_query_builder &$baseQuery, &$filters) {
            if (key_exists('branch', $filters) && !empty($filters['branch'])) {
                $baseQuery
                    ->join('ref_heavy_equipment_branches', 'ref_heavy_equipment_branches.id_heavy_equipment = ref_heavy_equipments.id')
                    ->where_in('ref_heavy_equipment_branches.id_branch', $filters['branch']);
            }
        });
    }
}