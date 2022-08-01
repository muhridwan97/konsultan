<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class ExtensionFieldModel extends MY_Model
{
    protected $table = 'ref_extension_fields';

    const EXTENSION_TYPE = [
        'SHORT TEXT', 'LONG TEXT', 'EMAIL', 'NUMBER', 'DATE', 'DATE TIME', 'CHECKBOX', 'RADIO', 'SELECT'
    ];

    /**
     * ExtensionFieldModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active record query builder for all related extension field data selection.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        $branches = $this->db
            ->select([
                'ref_extension_fields.*',
                'IFNULL(total_booking_type, 0) AS total_booking_type'
            ])
            ->from($this->table)
            ->join('(
                    SELECT id_extension_field, COUNT(id) AS total_booking_type 
                    FROM ref_booking_extension_fields
                    GROUP BY id_extension_field
                ) AS booking_extension_fields', 'booking_extension_fields.id_extension_field = ref_extension_fields.id', 'left')
            ->order_by('id', 'desc');
        return $branches;
    }

    /**
     * Get extension field data by booking type with or without deleted record.
     *
     * @param $bookingTypeId
     * @param bool $withTrashed
     * @return array
     */
    public function getByBookingType($bookingTypeId, $withTrashed = false)
    {
        $branches = $this->db
            ->select('ref_extension_fields.*')
            ->from($this->table)
            ->join('ref_booking_extension_fields', 'ref_extension_fields.id = ref_booking_extension_fields.id_extension_field')
            ->where('ref_booking_extension_fields.id_booking_type', $bookingTypeId);

        if (!$withTrashed) {
            $branches->where('ref_extension_fields.is_deleted', false);
        }

        return $branches->get()->result_array();
    }

}