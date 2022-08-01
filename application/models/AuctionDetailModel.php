<?php

class AuctionDetailModel extends MY_Model
{
    protected $table = 'auction_details';

    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery($branchId)
            ->select(['bookings.no_booking', 'bookings.no_reference', 'ref_people.name AS customer_name'])
            ->join('bookings', 'bookings.id = auction_details.id_booking', 'left')
            ->join('ref_people', 'ref_people.id = bookings.id_customer', 'left');
    }
}