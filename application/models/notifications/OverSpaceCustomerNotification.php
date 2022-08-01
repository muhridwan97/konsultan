<?php

/**
 * Class OverSpaceCustomerNotification
 */
class OverSpaceCustomerNotification extends Notify
{
    private $customer;

    public function __construct($customer = null)
    {
        $this->customer = $customer;
    }

    /**
     * Chat notification.
     *
     * @param $notifiable
     * @return array
     */
    public function toChat($notifiable)
    {
        return [
            'url' => 'sendMessage',
            'method' => 'POST',
            'payload' => [
                'chatId' => detect_chat_id($notifiable),
                'body' => "‼️ *[STORAGE] OVER SPACE* ‼️

Customer: *{$this->customer['name']}*
Effective Date: *" . format_date($this->customer['effective_date'], 'd F Y') . "*
Over Space Threshold: *>= 75%*

*[WAREHOUSE STORAGE]* " . ($this->customer['warehouse_storages']['total_used_percent'] >= 75 ? "❌" : "✅") . "
  Capacity M2: *" . numerical($this->customer['warehouse_capacity'], 2, true) . " M2*
  Capacity TEUS: *" . numerical($this->customer['warehouse_capacity_teus'], 2, true) . " TEUS*
  Storage Used: *" . numerical($this->customer['warehouse_storages']['total_used'], 2, true) . " TEUS*
  Storage Available: *" . numerical($this->customer['warehouse_capacity_teus_left'], 2, true) . " TEUS*
  Status: *" . numerical($this->customer['warehouse_storages']['total_used_percent'], 2, true) . "% Used*

*[YARD STORAGE]* " . ($this->customer['yard_storages']['total_used_percent'] >= 75 ? "❌" : "✅") . "
  Capacity M2: *" . numerical($this->customer['yard_capacity'], 2, true) . " M2*
  Capacity TEUS: *" . numerical($this->customer['yard_capacity_teus'], 2, true) . " TEUS*
  Storage Used: *" . numerical($this->customer['yard_storages']['total_used'], 2, true) . " TEUS*
  Storage Available: *" . numerical($this->customer['yard_capacity_teus_left'], 2, true) . " TEUS*
  Status: *" . numerical($this->customer['yard_storages']['total_used_percent'], 2, true) . "% Used*

*[COVERED YARD STORAGE]* " . ($this->customer['covered_yard_storages']['total_used_percent'] >= 75 ? "❌" : "✅") . "
  Capacity M2: *" . numerical($this->customer['covered_yard_capacity'], 2, true) . " M2*
  Capacity TEUS: *" . numerical($this->customer['covered_yard_capacity_teus'], 2, true) . " TEUS*
  Storage Used: *" . numerical($this->customer['covered_yard_storages']['total_used'], 2, true) . " TEUS*
  Storage Available: *" . numerical($this->customer['covered_yard_capacity_teus_left'], 2, true) . " TEUS*
  Status: *" . numerical($this->customer['covered_yard_storages']['total_used_percent'], 2, true) . "% Used*",
            ]
        ];
    }
}
