<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BookingControlStatusModel extends MY_Model
{
    protected $table = 'booking_control_statuses';

    const STATUS_PENDING = 'PENDING';
    const STATUS_DRAFT = 'DRAFT';
    const STATUS_CANCELED = 'CANCELED';
    const STATUS_DONE = 'DONE';
    const STATUS_CLEAR = 'CLEAR';

    const CONTROL_STATUSES = [
        self::STATUS_PENDING, self::STATUS_DRAFT, self::STATUS_CANCELED,
        self::STATUS_DONE, self::STATUS_CLEAR
    ];

    /**
     * BookingControlStatusModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery($branchId)
            ->select([
                'prv_users.name AS creator_name'
            ])
            ->join(UserModel::$tableUser, 'prv_users.id = booking_control_statuses.created_by');
    }
}