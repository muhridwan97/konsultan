<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BookingCIFInvoiceDetailModel extends MY_Model
{
    protected $table = 'booking_cif_invoice_details';

    /**
     * Get active record query builder for all related warehouse data selection.
     * @param null $branchId
     * @return CI_DB_query_builder
     */
    protected function getBaseQuery($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch_id();
        }

        $query = $this->db->select([
            'booking_cif_invoice_details.*',
            'IF(inbound_details.id IS NOT NULL, 
                booking_cif_invoice_details.quantity / inbound_details.quantity * total_item_value_inbound, 
                booking_cif_invoice_details.quantity / booking_cif_invoices.total_item_quantity * booking_cif_invoices.total_distributed_cost
            ) AS total_item_value',
            '(booking_cif_invoice_details.quantity * price) AS total_price',
            'bookings.no_booking',
            'bookings.no_reference'
        ])
            ->from($this->table)
            ->join('(
                SELECT 
                    booking_cif_invoices.*,
                    SUM(booking_cif_invoice_details.quantity) AS total_item_quantity
                FROM (
                    SELECT booking_cif_invoices.*, (discount + freight + insurance + handling + other) AS total_distributed_cost 
                    FROM booking_cif_invoices
                ) AS booking_cif_invoices
                LEFT JOIN booking_cif_invoice_details ON booking_cif_invoice_details.id_booking_cif_invoice = booking_cif_invoices.id
                GROUP BY booking_cif_invoices.id
            ) AS booking_cif_invoices', 'booking_cif_invoices.id = booking_cif_invoice_details.id_booking_cif_invoice')
            ->join('(                
                SELECT 
                    booking_cif_invoice_details.id,
                    booking_cif_invoice_details.quantity,
                    (quantity / total_item_quantity * booking_cif_invoices.total_distributed_cost) AS total_item_value_inbound 
                FROM booking_cif_invoice_details
                INNER JOIN (
                    SELECT 
                        booking_cif_invoices.*,
                        SUM(booking_cif_invoice_details.quantity) AS total_item_quantity
                    FROM (
                        SELECT booking_cif_invoices.*, (discount + freight + insurance + handling + other) AS total_distributed_cost 
                        FROM booking_cif_invoices
                    ) AS booking_cif_invoices
                    LEFT JOIN booking_cif_invoice_details ON booking_cif_invoice_details.id_booking_cif_invoice = booking_cif_invoices.id
                    GROUP BY booking_cif_invoices.id
                ) AS booking_cif_invoices ON booking_cif_invoices.id = booking_cif_invoice_details.id_booking_cif_invoice                
            ) AS inbound_details', 'inbound_details.id = booking_cif_invoice_details.id_booking_cif_invoice_detail', 'left')
            ->join('bookings', 'bookings.id = booking_cif_invoices.id_booking');

        if (!empty($branchId)) {
            $query->where('bookings.id_branch', $branchId);
        }

        return $query;
    }

}