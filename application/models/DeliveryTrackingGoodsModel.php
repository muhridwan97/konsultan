<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DeliveryTrackingGoodsModel extends MY_Model
{
    protected $table = 'delivery_tracking_goods';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery($branchId)
            ->select([
                'safe_conducts.no_safe_conduct',
                'safe_conducts.no_police',
                'ref_goods.no_goods',
                'ref_goods.name AS goods_name',
                'safe_conduct_goods.unit_weight',
                'safe_conduct_goods.unit_gross_weight',
                'safe_conduct_goods.unit_length',
                'safe_conduct_goods.unit_width',
                'safe_conduct_goods.unit_height',
                'ref_goods.name AS whey_number',
            ])
            ->join('delivery_tracking_details', 'delivery_tracking_details.id = delivery_tracking_goods.id_delivery_tracking_detail', 'left')
            ->join('safe_conduct_goods', 'safe_conduct_goods.id = delivery_tracking_goods.id_safe_conduct_goods', 'left')
            ->join('safe_conducts', 'safe_conducts.id = safe_conduct_goods.id_safe_conduct', 'left')
            ->join('ref_goods', 'ref_goods.id = safe_conduct_goods.id_goods');
    }
}
