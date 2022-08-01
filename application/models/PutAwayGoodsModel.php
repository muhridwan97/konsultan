<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class PutAwayGoodsModel extends MY_Model
{
    protected $table = 'put_away_goods';

   /**
     * Get active record query builder for all related warehouse data selection.
     *
     * @param null $ChecklistTypeId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($ChecklistTypeId = null)
    {
        $putAway = $this->db
            ->select([
                'put_away.*',
                'put_away_goods.*',
                'ref_people.name',
                'bookings.no_booking',
                'bookings.no_reference',
                'ref_goods.no_goods',
                'ref_goods.name as goods_name',
                'ref_goods.type_goods',
                'ref_units.unit',
                'ref_positions.position',
            ])
            ->from('put_away_goods')
            ->join('put_away', 'put_away.id = put_away_goods.id_put_away', 'left')
            ->join('ref_people', 'ref_people.id = put_away_goods.id_owner', 'left')
            ->join('bookings', 'bookings.id = put_away_goods.id_booking', 'left')
            ->join('ref_goods', 'ref_goods.id = put_away_goods.id_goods', 'left')
            ->join('ref_units', 'ref_units.id = put_away_goods.id_unit', 'left')
            ->join('ref_positions', 'ref_positions.id = put_away_goods.id_position', 'left'); 


        return $putAway;
    }

    public function getPutAwayGoodsById($id)
    {

        $putAway = $this->getBaseQuery()->where('put_away_goods.id_put_away', $id)
                ->select([
                    'COUNT(work_order_goods_photos.id) AS count_photo',
                    'approver.name AS approver_name',
                    'status_histories.created_at AS approved_at',
                    'work_orders.no_work_order'
                ])
                ->join('work_order_goods_photos', 'work_order_goods_photos.id_work_order_goods = put_away_goods.id_work_order_goods', 'left')
                ->join('(
                    SELECT * FROM status_histories
                    WHERE status_histories.status = "APPROVED"
                ) AS status_histories',"status_histories.id_reference = put_away_goods.id_work_order AND status_histories.type = '".StatusHistoryModel::TYPE_WORK_ORDER_VALIDATION."'",'left')
                ->join(UserModel::$tableUser . ' AS approver', 'approver.id = status_histories.created_by', 'left')
                ->join('work_orders', 'work_orders.id = put_away_goods.id_work_order', 'left')
                ->where('status_histories.status', 'APPROVED')
                ->group_by('put_away_goods.id');

        return $putAway->get()->result_array();
    }

    /**
     * Update put away.
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updatePutAwayGoods($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }
  
}