<?php

class StatusHistoryModel extends MY_Model
{
    protected $table = 'status_histories';

    const TYPE_BOOKING_PAYMENT = 'booking-payment';
    const TYPE_BOOKING_PAYMENT_STATUS = 'booking-payment-status';
    const TYPE_BOOKING_BCF_STATUS = 'booking-bcf-status';
    const TYPE_WORK_ORDER_VALIDATION = 'work-order-validation';
    const TYPE_PLAN_REALIZATION = 'plan-realization';
    const TYPE_DELIVERY_TRACKING = 'delivery-tracking';
    const TYPE_OPNAME_SPACE = 'opname-space';
    const TYPE_STORAGE_USAGE = 'storage-usage';
    const TYPE_STORAGE_USAGE_CUSTOMER = 'storage-usage-customer';
    const TYPE_STORAGE_OVER_SPACE = 'storage-over-space';
    const TYPE_STORAGE_OVER_SPACE_CUSTOMER = 'storage-over-space-customer';
    const TYPE_TEP_TRACKING = 'tep-tracking';
    const TYPE_UPLOAD = 'upload';
    const TYPE_DISCREPANCY_HANDOVER = 'discrepancy-handover';

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
                'prv_users.name AS creator_name'
            ])
            ->join(UserModel::$tableUser, 'prv_users.id = status_histories.created_by', 'left');
    }
}
