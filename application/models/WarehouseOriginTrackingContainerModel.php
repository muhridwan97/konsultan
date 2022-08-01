<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class WarehouseOriginTrackingContainerModel extends MY_Model
{
    protected $table = 'warehouse_origin_tracking_containers';
    static $tableBankAccount = 'ref_bank_accounts';

    const STATUS_SUCCESS = 'SUCCESS';
    const STATUS_FAILED = 'FAILED';

    /**
     * BankAccountModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

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
                'safe_conducts.no_safe_conduct',
                'safe_conducts.security_in_date',
                'safe_conducts.security_out_date',
            ])
            ->join('safe_conducts', 'safe_conducts.id = warehouse_origin_tracking_containers.id_safe_conduct', 'left');
    }
}
