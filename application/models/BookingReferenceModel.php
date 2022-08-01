<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BookingReferenceModel extends MY_Model
{
    protected $table = 'booking_references';

    /**
     * BookingReferenceModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        $baseQuery = parent::getBaseQuery()
            ->select([
                'bookings.no_booking',
                'bookings.no_reference',
                'booking_refs.no_booking AS no_booking_reference',
                'booking_refs.no_reference AS no_ref_reference',
            ])
            ->join('bookings', 'bookings.id = booking_references.id_booking')
            ->join('bookings AS booking_refs', 'booking_refs.id = booking_references.id_booking_reference');

        if (!empty(if_empty($branchId, get_active_branch_id()))) {
            $baseQuery->where('bookings.id_branch', if_empty($branchId, get_active_branch_id()));
        }

        return $baseQuery;
    }
}
