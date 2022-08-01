<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class CustomerInventoryInboundModel extends MY_Model
{
    protected $table = 'inbounds';
    protected $tableInboundGoods = 'inbound_goods';

    /**
     * CustomerInventoryInboundModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
        if ($this->config->item('sso_enable')) {
            $this->table = env('DB_CUSTOMER_INVENTORY_DATABASE') . '.inbounds';
            $this->tableInboundGoods = env('DB_CUSTOMER_INVENTORY_DATABASE') . '.inbound_goods';
        }
    }

    /**
     * Get comparison inbound goods and receiving.
     *
     * @param array $filters
     * @return array|array[]
     */
    public function getComparisonInboundGoods($filters = [])
    {
        $branchId = get_if_exist($filters, 'branch', get_active_branch_id());

        $baseQuery = $this->db
            ->select([
                'bookings.id',
                'SUM(safe_conduct_goods.quantity) AS total_outbound',
                'IFNULL(inbound_receives.total_received, 0) AS total_received',
            ])
            ->from('bookings')
            ->join('handlings', 'handlings.id_booking = bookings.id')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('work_orders', 'work_orders.id_handling = handlings.id')
            ->join('safe_conducts', 'safe_conducts.id = work_orders.id_safe_conduct')
            ->join('safe_conduct_goods', 'safe_conduct_goods.id_safe_conduct = safe_conducts.id')
            ->join('(
                SELECT inbounds.no_reference, SUM(inbound_goods.quantity) AS total_received 
                FROM ' . $this->table . '
                INNER JOIN ' . $this->tableInboundGoods . ' ON inbound_goods.id_inbound = inbounds.id
                GROUP BY no_reference
            ) AS inbound_receives', 'inbound_receives.no_reference = bookings.no_reference')
            ->where([
                'handlings.status' => 'APPROVED',
                'work_orders.status' => 'COMPLETED',
                'ref_handling_types.multiplier_goods !=' => 0,
                'safe_conducts.is_deleted' => false
            ])
            ->group_by('bookings.id');

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if (key_exists('no_reference', $filters) && !empty($filters)) {
            $baseQuery->where('bookings.no_reference', $filters['no_reference']);
        }

        return $baseQuery->get()->result_array();
    }

}