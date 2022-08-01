<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BookingTypeModel extends MY_Model
{
    protected $table = 'ref_booking_types';

    const CATEGORY_INBOUND = 'INBOUND';
    const CATEGORY_OUTBOUND = 'OUTBOUND';

    const TYPE_IMPORT = 'IMPORT';
    const TYPE_EXPORT = 'EXPORT';

    /**
     * Get active record query builder for all related booking type data.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        $bookingTypes = $this->db->select([
            'ref_booking_types.*',
            'default_document.document_type AS default_document'
        ])
            ->from('ref_booking_types')
            ->join('ref_document_types AS default_document', 'ref_booking_types.id_document_type = default_document.id', 'left');
        return $bookingTypes;
    }


    /**
     * Get booking type data by Id.
     * @param null $Id
     */
    public function getBookingTypeById($Id)
    {
        $bookingTypes = $this->getBaseQuery()->where('ref_booking_types.id', $Id);

        return $bookingTypes->get()->row_array();
    }

     /**
     * Get booking type data by Id.
     * @param null $BookingType
     */
    public function getBookingTypeByBookingType($BookingType)
    {
        $bookingTypes = $this->getBaseQuery()->where('ref_booking_types.booking_type', $BookingType);

        return $bookingTypes->get()->row_array();
    }

    /**
     * Get all handling types with or without deleted records.
     * @param bool $withTrashed
     * @return array
     */
    public function getAllBookingTypes($withTrashed = false)
    {
        $handlingTypes = $this->getBaseQuery();

        if (!$withTrashed) {
            $handlingTypes->where('ref_booking_types.is_deleted', false);
        }

        return $handlingTypes->get()->result_array();
    }
    /**
     * Get allocated handling type by customer.
     * @param $customerId
     * @return mixed
     */
    public function getBookingTypesByCustomer($customerId)
    {
        $handlingTypes = $this->getBaseQuery()
            ->join('ref_people_booking_types', 'ref_booking_types.id = ref_people_booking_types.id_booking_type', 'left')
            ->where([
                'ref_people_booking_types.id_customer' => $customerId,
                'ref_booking_types.is_deleted' => false
            ]);

        return $handlingTypes->get()->result_array();
    }
}