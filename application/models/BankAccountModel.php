<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class BankAccountModel extends MY_Model
{
    protected $table = 'ref_bank_accounts';
    static $tableBankAccount = 'ref_bank_accounts';

    const TYPE_REGULAR = 'REGULAR';
    const TYPE_TAX = 'TAX';
    const TYPE_PETTY_CASH = 'PETTY CASH';

    /**
     * BankAccountModel constructor.
     */
    public function __construct()
    {
        parent::__construct();

        if ($this->config->item('sso_enable')) {
            $this->table = env('DB_FINANCE_DATABASE') . '.ref_bank_accounts';
            self::$tableBankAccount = $this->table;
        }
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
                'prv_users.name AS creator_name'
            ])
            ->join(UserModel::$tableUser, 'prv_users.id = ref_bank_accounts.created_by', 'left');
    }
}
