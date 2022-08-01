<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class TransporterEntryPermitRequestHoldItemModel
 */
class TransporterEntryPermitRequestHoldItemModel extends MY_Model
{
    protected $table = 'transporter_entry_permit_request_hold_items';

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
                'ref_people.id AS id_customer',
                'ref_people.name AS customer_name',
                'transporter_entry_permit_request_holds.no_hold_reference',
                'transporter_entry_permit_request_holds.hold_type',
                'bookings.no_reference',
                'ref_goods.name AS goods_name',
                'ref_units.unit',
                '(
                    SELECT GROUP_CONCAT(transporter_entry_permit_requests.no_request SEPARATOR ",") AS no_requests 
                    FROM transporter_entry_permit_request_hold_references
                    INNER JOIN transporter_entry_permit_request_uploads ON transporter_entry_permit_request_uploads.id = transporter_entry_permit_request_hold_references.id_tep_request_upload
                    INNER JOIN transporter_entry_permit_requests ON transporter_entry_permit_requests.id = transporter_entry_permit_request_uploads.id_request
                    WHERE transporter_entry_permit_request_hold_references.id_tep_hold_item = transporter_entry_permit_request_hold_items.id
                ) AS no_requests',
                'reference_holds.id AS id_hold_reference_source',
                'reference_holds.no_hold_reference AS no_hold_reference_source',
                'prv_users.name AS creator_name'
            ])
            ->join('transporter_entry_permit_request_holds', 'transporter_entry_permit_request_holds.id = transporter_entry_permit_request_hold_items.id_tep_hold', 'left')
            ->join('ref_people', 'ref_people.id = transporter_entry_permit_request_holds.id_customer', 'left')
            ->join('transporter_entry_permit_request_hold_items AS reference_hold_items', 'reference_hold_items.id = transporter_entry_permit_request_hold_items.id_tep_hold_item_reference', 'left')
            ->join('transporter_entry_permit_request_holds AS reference_holds', 'reference_holds.id = reference_hold_items.id_tep_hold', 'left')
            ->join('bookings', 'bookings.id = transporter_entry_permit_request_hold_items.id_booking', 'left')
            ->join('ref_goods', 'ref_goods.id = transporter_entry_permit_request_hold_items.id_goods', 'left')
            ->join('ref_units', 'ref_units.id = transporter_entry_permit_request_hold_items.id_unit', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = transporter_entry_permit_request_hold_items.created_by', 'left');
    }

}
