<?php
defined('BASEPATH') or exit('No direct script access allowed');

class TransporterEntryPermitCustomerModel extends MY_Model
{
    protected $table = 'transporter_entry_permit_customers';

    /**
     * TransporterEntryPermitCustomerModel constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->addFilteredMap('customer', function (CI_DB_query_builder &$baseQuery, &$filters) {
            if (key_exists('customer', $filters) && !empty($filters['customer'])) {
                $baseQuery->where($this->table . '.id_customer', $filters['customer']);
            }
        });

        $this->addFilteredMap('branch', function (CI_DB_query_builder &$baseQuery, &$filters) {
            if (key_exists('branch', $filters) && !empty($filters['branch'])) {
                $baseQuery->where('transporter_entry_permits.id_branch', $filters['branch']);
            }
        });

        $this->addFilteredMap('outstanding_linked_tep', function (CI_DB_query_builder &$baseQuery, &$filters) {
            if (key_exists('outstanding_linked_tep', $filters) && $filters['outstanding_linked_tep']) {
                $baseQuery
                    ->where([
                        'transporter_entry_permits.tep_category' => 'OUTBOUND',
                        'transporter_entry_permits.checked_in_at IS NULL' => null,
                        'transporter_entry_permits.checked_out_at IS NULL' => null,
                    ]);
            }
        });
    }

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
                'transporter_entry_permits.tep_code',
                'transporter_entry_permits.tep_category',
                'transporter_entry_permits.receiver_name',
                'transporter_entry_permits.receiver_vehicle',
                'transporter_entry_permits.receiver_no_police',
                'transporter_entry_permits.checked_in_at',
                'transporter_entry_permits.checked_out_at',
                'transporter_entry_permits.id_linked_tep',
                'ref_people.name AS customer_name',
                'ref_branches.branch',
            ])
            ->join('transporter_entry_permits', 'transporter_entry_permits.id = transporter_entry_permit_customers.id_tep')
            ->join('ref_people', 'ref_people.id = transporter_entry_permit_customers.id_customer')
            ->join('ref_branches', 'ref_branches.id = transporter_entry_permits.id_branch')
            ->where('ref_people.is_deleted', false);
    }

    function getCustomerByIdTep($id)
    {
        $baseQuery = parent::getBaseQuery()
            ->select([
                'customer.*',
            ])
            ->join('ref_people AS customer', 'customer.id = ' . $this->table . '.id_customer', 'left')
            ->where($this->table . '.id_tep', $id);

        return $baseQuery->get()->result_array();
    }

    function getBookingByIdTep($id)
    {
        $baseQuery = parent::getBaseQuery()
            ->select([
                'bookings.*',
            ])
            ->join('transporter_entry_permits', 'transporter_entry_permits.id = ' . $this->table . '.id_tep', 'left')
            ->join('safe_conducts', 'safe_conducts.id_transporter_entry_permit = transporter_entry_permits.id', 'left')
            ->join('bookings', 'bookings.id = safe_conducts.id_booking', 'left')
            ->where($this->table . '.id_tep', $id)
            ->group_by('bookings.id, transporter_entry_permit_customers.id');

        return $baseQuery->get()->result_array();
    }
}
