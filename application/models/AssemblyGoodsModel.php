<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class AssemblyGoodsModel extends MY_Model
{
    protected $table = 'ref_assembly_goods';

    /**
     * GoodsModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get master assembly goods base query.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return $this->db->select([
            'ref_assembly_goods.*',
            'ref_assemblies.no_assembly',
            'ref_assembly_goods.quantity_assembly AS quantity',
            'goods.id AS id_goods',
            'goods.name AS goods_name',
            'goods.no_goods',
            'goods.unit_weight',
            'goods.unit_gross_weight',
            'goods.unit_volume',
            'goods.unit_length',
            'goods.unit_width',
            'goods.unit_height',
            'units.unit',
            '(quantity_assembly * goods.unit_weight) AS total_weight',
            '(quantity_assembly * goods.unit_gross_weight) AS total_gross_weight',
            '(quantity_assembly * goods.unit_volume) AS total_volume',
        ])
            ->from($this->table)
            ->join('ref_assemblies', 'ref_assembly_goods.id_assembly = ref_assemblies.id', 'left')
            ->join('ref_goods AS goods', 'ref_assembly_goods.assembly_goods = goods.id', 'left')
            ->join('ref_units AS units', 'ref_assembly_goods.id_unit = units.id', 'left');
    }

    /**
     * Get assembly goods by id_assembly and assemblygoods.
     * @param $assemblyId
     * @param $assemblyGoods
     * @param $idUnit
     * @return CI_DB_query_builder
     */
    public function getAssemblyGoodsByAssemblyIdByassemblyGoods($assemblyId, $assemblyGoods, $idUnit)
    {

        $cycleCounts = $this->getBaseQuery()
                        ->where('ref_assembly_goods.id_assembly', $assemblyId)
                        ->where('ref_assembly_goods.assembly_goods', $assemblyGoods)
                        ->where('ref_assembly_goods.id_unit', $idUnit);

        return $cycleCounts->get()->row_array();
    }
}