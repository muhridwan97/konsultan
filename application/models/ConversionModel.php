<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ConversionModel extends MY_Model
{
    protected $table = 'ref_conversions';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get basic query for booking data.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        $bookings = $this->db->select([
            'ref_conversions.*',
            'from_units.unit AS unit_from',
            'to_units.unit AS unit_to',
            'ref_goods.name',
        ])
            ->from($this->table)
            ->join('ref_units AS from_units', 'ref_conversions.id_unit_from = from_units.id', 'left')
            ->join('ref_units AS to_units', 'ref_conversions.id_unit_to = to_units.id', 'left')
            ->join('ref_goods', 'ref_conversions.id_goods = ref_goods.id', 'left');

        return $bookings;
    }

    /**
     * Get unit conversion of goods.
     *
     * @param $goodsId
     * @param null $unitId
     * @return array
     */
    public function getUnitGoodsConversions($goodsId, $unitId = null)
    {
        $conversionUnits = $this->db->distinct()
            ->select(['id_unit_to', 'unit_to', 'value'])
            ->from('view_conversions')
            ->where('id_goods', $goodsId);

        if (!empty($unitId)) {
            $conversionUnits->where('id_unit_from', $unitId);
        }

        return $conversionUnits->get()->result_array();
    }

    /**
     * Check if conversion of goods from-to unit is exist.
     *
     * @param $idGoods
     * @param $idUnitFrom
     * @param $idUnitTo
     * @return bool
     */
    public function isConversionExist($idGoods, $idUnitFrom, $idUnitTo)
    {
        if ($idUnitFrom == $idUnitTo) {
            return true;
        }

        $conversionForward = $this->db->get_where('view_conversions', [
            'id_goods' => $idGoods,
            'id_unit_from' => $idUnitFrom,
            'id_unit_to' => $idUnitTo
        ]);
        $forwardRulesFound = $conversionForward->num_rows();

        $conversionBackward = $this->db->get_where($this->table, [
            'id_goods' => $idGoods,
            'id_unit_from' => $idUnitTo,
            'id_unit_to' => $idUnitFrom
        ]);
        $backwardRulesFound = $conversionBackward->num_rows();

        return $forwardRulesFound || $backwardRulesFound;
    }

    /**
     * Get value conversion per single unit (Eg. box to pcs is "5 factor" (return value 5))
     *
     * @param $goodsId
     * @param $idUnitFrom
     * @param $idUnitTo
     * @return float|int
     */
    public function getConversionValue($goodsId, $idUnitFrom, $idUnitTo)
    {
        if ($idUnitFrom == $idUnitTo) {
            return 1;
        }

        // forward rule check
        $conversion = $this->db->select('value')
            ->from('view_conversions')
            ->where([
                'id_goods' => $goodsId,
                'id_unit_from' => $idUnitFrom,
                'id_unit_to' => $idUnitTo,
            ])->get();

        if ($conversion->num_rows() > 0) {
            $conversion = $conversion->row_array();
            return $conversion['value'];
        }

        // backward rule check
        $conversion = $this->db->select('value')
            ->from('view_conversions')
            ->where([
                'id_goods' => $goodsId,
                'id_unit_from' => $idUnitTo,
                'id_unit_to' => $idUnitFrom,
            ])->get();
        if ($conversion->num_rows() > 0) {
            $conversion = $conversion->row_array();
            return 1 / $conversion['value'];
        }
        return null; // no conversion value found
    }

    /**
     * Convert a unit to another unit,
     * (Eg. how many 5 box to pcs?, return 15 (exact result value of conversion)).
     *
     * @param $goodsId
     * @param $idUnitFrom
     * @param $idUnitTo
     * @param $value
     * @param bool $isCeiling
     * @return float|int
     */
    public function convert($goodsId, $idUnitFrom, $idUnitTo, $value, $isCeiling = false)
    {
        if ($idUnitFrom == $idUnitTo) {
            return $value;
        }

        // forward rule check
        $conversion = $this->db->select('value')
            ->from('ref_conversions')
            ->where([
                'id_goods' => $goodsId,
                'id_unit_from' => $idUnitFrom,
                'id_unit_to' => $idUnitTo,
            ])->get();

        if ($conversion->num_rows() > 0) {
            $conversion = $conversion->row_array();
            $result = $conversion['value'] * $value;
            if ($isCeiling) {
                $result = ceil($result);
            }
            return $result;
        }

        // backward rule check
        $conversion = $this->db->select('value')
            ->from('ref_conversions')
            ->where([
                'id_goods' => $goodsId,
                'id_unit_from' => $idUnitTo,
                'id_unit_to' => $idUnitFrom,
            ])->get();
        if ($conversion->num_rows() > 0) {
            $conversion = $conversion->row_array();
            $result = $value / $conversion['value'];
            if ($isCeiling) {
                $result = ceil($result);
            }
            return $result;
        }
        return null; // there is no conversion rules, (alternative just return original value).
    }
}