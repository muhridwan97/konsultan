<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class TransporterEntryPermitRequestPriorityItemModel
 */
class TransporterEntryPermitRequestPriorityItemModel extends MY_Model
{
    protected $table = 'transporter_entry_permit_request_priority_items';

    /**
     * Get base query of table.
     *
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery()
            ->select([
                'ref_people.name AS customer_name',
                'bookings.no_reference',
                'ref_goods.name AS goods_name',
                'ref_units.unit',
                'prv_users.name AS creator_name'
            ])
            ->join('bookings', 'bookings.id = transporter_entry_permit_request_priority_items.id_booking', 'left')
            ->join('ref_people', 'ref_people.id = bookings.id_customer', 'left')
            ->join('ref_goods', 'ref_goods.id = transporter_entry_permit_request_priority_items.id_goods', 'left')
            ->join('ref_units', 'ref_units.id = transporter_entry_permit_request_priority_items.id_unit', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = transporter_entry_permit_request_priority_items.created_by', 'left');
    }

}
