<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class TransporterEntryPermitBookingModel extends MY_Model
{
    protected $table = 'transporter_entry_permit_bookings';

    /**
     * TEP Bookings Model constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

     /**
     * Get active record query builder for all related tep data.
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = NULL)
    {
    	$baseQuery = parent::getBaseQuery();
        return $baseQuery;

    }

    function getBookingByIdTep($id)
    {
        $baseQuery = parent::getBaseQuery()
            ->select([
                'bookings.no_booking',
                'bookings.no_reference',
                'ref_booking_types.booking_type',
                'ref_booking_types.category',
                'ref_people.name AS customer_name'
            ])
            ->join('bookings', 'bookings.id = ' . $this->table . '.id_booking', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_people', 'ref_people.id = bookings.id_customer', 'left')
            ->where($this->table . '.id_tep', $id);

        return $baseQuery->get()->result_array();
    }
}
