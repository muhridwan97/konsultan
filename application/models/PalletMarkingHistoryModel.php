<?php

class PalletMarkingHistoryModel extends MY_Model
{
    protected $table = 'pallet_marking_histories';

    const STATUS_FREE = 'FREE';
    const STATUS_REQUESTED = 'REQUESTED';
    const STATUS_LOCKED = 'LOCKED';
    const STATUS_UNLOCKED = 'UNLOCKED';
    const STATUS_PRINTED = 'PRINTED';

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
            ->join(UserModel::$tableUser, 'prv_users.id = pallet_marking_histories.created_by', 'left');
    }
}
