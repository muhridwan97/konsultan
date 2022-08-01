<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ShiftingModel extends CI_Model
{
    const STATUS_PENDING = 'PENDING';
    const STATUS_APPROVED = 'APPROVED';

    private $table = 'shiftings';
    private $table_detail = 'shifting_details';

    public function __construct()
    {
        parent::__construct();
    }

    private function getBaseShiftingQuery()
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $base = $this->db->select()->from($this->table);

        if (!empty($branchId)) {
            $base->where('shiftings.id_branch', $branchId);
        }

        return $base;
    }

    public function getAllShiftings($withTrashed = false)
    {
        $shifting = $this->getBaseShiftingQuery();
        $shifting->select('handling_shiftings.handling_count');
        $shifting->join('(
                SELECT id_shifting, count(id) AS handling_count
                FROM shifting_details GROUP BY id_shifting
            ) AS handling_shiftings', 'shiftings.id = handling_shiftings.id_shifting', 'left');
        $shifting->order_by('shifting_date', 'desc');

        if (!$withTrashed) {
            $shifting->where('shiftings.is_deleted', false);
        }

        return $shifting->get()->result_array();
    }

    public function getShiftingById($id)
    {
        $shifting = $this->getBaseShiftingQuery();
        $shifting->where('id', $id);

        return $shifting->get()->row_array();
    }

    public function getShiftingDetailsByIdShifting($shiftingId)
    {
        $shiftingDetails = $this->db->select([
            'shifting_details.*',
            'ref_containers.no_container',
            'ref_positions.position',
            'ref_goods.name AS goods_name',
            'ref_goods.unit_length',
            'ref_goods.unit_width',
            'ref_goods.unit_height',
            'ref_goods.unit_volume',
            'ref_goods.unit_weight',
            'ref_goods.unit_gross_weight',
        ])
            ->from('shifting_details')
            ->join('ref_containers', 'shifting_details.id_container = ref_containers.id', 'left')
            ->join('ref_goods', 'shifting_details.id_goods = ref_goods.id', 'left')
            ->join('ref_positions', 'shifting_details.id_position = ref_positions.id', 'left')
            ->where('id_shifting', $shiftingId);
            
        return $shiftingDetails->get()->result_array();
    }

    /**
     * Create shifting record
     * @param $data
     * @return mixed
     */
    public function createShifting($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update shifting record
     * @param $data
     * @param $id
     * @return mixed
     */
    public function updateShifting($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete shifting record
     * @param $id
     * @param bool $softDelete
     * @return mixed
     */
    public function deleteShifting($id, $softDelete = true)
    {
        if ($softDelete) {
            return $this->db->update($this->table, [
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => UserModel::authenticatedUserData('id')
            ], ['id' => $id]);
        }
        return $this->db->delete($this->table, ['id' => $id]);
    }

    /**
     * Create shifting detail
     * @param $data
     * @return mixed
     */
    public function createShiftingDetail($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table_detail, $data);
        }
        return $this->db->insert($this->table_detail, $data);
    }

    /**
     * Update shifting detail
     * @param $data
     * @param $id
     * @return mixed
     */
    public function updateShiftingDetail($data, $id)
    {
        return $this->db->update($this->table_detail, $data, ['id' => $id]);
    }

    /**
     * Delete shifting detail
     * @param $id
     * @param bool $softDelete
     * @return mixed
     */
    public function deleteShiftingDetail($id, $softDelete = true)
    {
        if ($softDelete) {
            return $this->db->update($this->table_detail, [
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => UserModel::authenticatedUserData('id')
            ], ['id' => $id]);
        }
        return $this->db->delete($this->table_detail, ['id' => $id]);
    }

    /**
     * Generate shifting auto number.
     * @param string $type
     * @return string
     */
    public function getAutoNumberShiftingRequest($type = 'SH')
    {
        $orderData = $this->db->query("
            SELECT CAST(RIGHT(no_shifting, 6) AS UNSIGNED) + 1 AS order_number 
            FROM shiftings 
            WHERE MONTH(created_at) = MONTH(NOW()) 
              AND YEAR(created_at) = YEAR(NOW())
              AND SUBSTRING(no_shifting, 1, 2) = '$type'
            ORDER BY CAST(RIGHT(no_shifting, 6) AS UNSIGNED) DESC LIMIT 1
        ");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return $type . '/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }
}
