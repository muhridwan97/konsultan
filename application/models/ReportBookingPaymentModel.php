<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class ReportBookingPaymentModel
 * @property StatusHistoryModel $statusHistory
 */
class ReportBookingPaymentModel extends MY_Model
{
    protected $table = 'payments';

    /**
     * UploadModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        $this->load->model('UserModel', 'user');
        $this->load->model('StatusHistoryModel', 'statusHistory');
    }

    /**
     * Get report complain.
     *
     * @param array $filters
     * @return array
     */
    public function getAll($filters = [], $withTrashed = false)
    {
        $branchId = get_active_branch('id');
        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $report = $this->db
            ->select([
                'payments.id',
                'status_approved.created_at AS date_approved'
            ])
            ->from($this->table. 'AS payments')
            ->join('status_histories AS status_approved', 'status_approved.id_reference = payments.id AND status_approved.type = "'.StatusHistoryModel::TYPE_BOOKING_PAYMENT.'"' , 'left');

        if (!$withTrashed && $this->db->field_exists('is_deleted', $this->table)) {
            $report->where($this->table . '.is_deleted', false);
        }

        if (!empty($filters)) {
             if (key_exists('customer', $filters) && !empty($filters['customer'])) {
                if (is_array($filters['customer'])) {
                    $report->where_in('ref_people.id', $filters['customer']);
                } else {
                    $report->where('ref_people.id', $filters['customer']);
                }
            }

            if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
                if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                    $report->where('DATE(' . $filters['date_type'] . ')>=', sql_date_format($filters['date_from'], false));
                }
                if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                    $report->where('DATE(' . $filters['date_type'] . ')<=', sql_date_format($filters['date_to'], false));
                }
            }

            // if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            //     $report->where('ref_branch.id', $filters['branch']);
            // }
        }


        $payment = $report->get()->result_array();

        return $payment;
    }
}
