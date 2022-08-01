<?php

class PhBidOrderContainerModel extends MY_Model
{
    protected $table = 'order_containers';
    public static $tableOrderContainer = 'order_containers';

    public function __construct()
    {
        parent::__construct();

        $this->table = env('DB_PHBID_DATABASE') . '.order_containers';
        self::$tableOrderContainer = env('DB_PHBID_DATABASE') . '.order_containers';

        $this->load->model('PhBidOrderSummaryModel');
    }

    protected function getBaseQuery($branchId = null)
    {
        if (empty(env('DB_PHBID_DATABASE'))) {
            show_error("PHBID configuration is not set, you cannot access this feature.");
        }

        return parent::getBaseQuery($branchId)
            ->select([
                'order_summaries.nomor_lelang',
                'order_summaries.nomor_order',
            ])
            ->join(PhBidOrderSummaryModel::$tableOrder, 'order_summaries.id = order_containers.id_order_summary');
    }

    /**
     * Get last detail notification.
     *
     * @return mixed|null
     */
    public function getLastContainerNotification()
    {
        $baseQuery = $this->db->select_max('notified_at');

        return $baseQuery->get($this->table)->row_array()['notified_at'] ?? null;
    }

    /**
     * Update notified order detail.
     *
     * @param array $auction
     * @return bool|mixed|string
     */
    public function updateContainerNotification($auction = [])
    {
        if (empty($auction)) {
            $auction = PhBidOrderSummaryModel::RESERVED_AUCTION;
        }
        $auctionString = implode("','", $auction);
        $auctionFilter = "AND nomor_lelang IN('{$auctionString}')";

        return $this->db->query("
            UPDATE {$this->table}
            INNER JOIN " . PhBidOrderSummaryModel::$tableOrder . " ON order_summaries.id = order_containers.id_order_summary
            INNER JOIN transporter_entry_permit_trackings ON transporter_entry_permit_trackings.id_phbid_tracking = order_containers.id
            SET notified_at = NOW()
            WHERE notified_at IS NULL 
                AND unloading_actual_date IS NOT NULL 
                {$auctionFilter}
        ");
    }
}