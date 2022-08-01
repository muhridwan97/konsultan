<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DeliveryTrackingSafeConductModel extends MY_Model
{
    protected $table = 'delivery_tracking_safe_conducts';

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
                'bookings.no_reference',
            ])
            ->join('safe_conducts', 'safe_conducts.id = delivery_tracking_safe_conducts.id_safe_conduct', 'left')
            ->join('bookings', 'bookings.id = safe_conducts.id_booking', 'left');
    }
}
