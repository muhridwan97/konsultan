<?php

/**
 * Class OverSpaceM2CustomerNotification
 */
class OverSpaceM2CustomerNotification extends Notify
{
    private $storage;
    private $captureDate;

    public function __construct($storage = null, $captureDate = null)
    {
        $this->storage = $storage;
        $this->captureDate = $captureDate;
    }

    /**
     * Chat notification.
     *
     * @param $notifiable
     * @return array
     */
    public function toChat($notifiable)
    {
        if (!isset($this->storage['used_warehouse_storage'])) {
            $this->storage['used_warehouse_storage'] = $this->storage['warehouse_usage'];
            $this->storage['used_yard_storage'] = $this->storage['yard_usage'];
            $this->storage['used_covered_yard_storage'] = $this->storage['covered_yard_usage'];
        }

        if (empty($this->captureDate)) {
            if (!isset($this->storage['created_at'])) {
                $this->storage['created_at'] = date('Y-m-d H:i:s');
            }
            if (!isset($this->storage['date'])) {
                $this->storage['date'] = date('Y-m-d');
            }
            if (format_date($this->storage['created_at']) == $this->storage['date']) {
                $this->captureDate = $this->storage['created_at'] ?? date('Y-m-d');
            } else {
                $this->captureDate = $this->storage['date'] ?? date('Y-m-d');
            }
        }

        $notifiedCustomerMessage = '';
        if (is_array($notifiable) && $notifiable['id'] != $this->storage['id_customer']) {
            $notifiedCustomerMessage = "\nNotified Member: *" . ($notifiable['name'] ?? $notifiable['customer_name']) . "*";
        }

        return [
            'url' => 'sendMessage',
            'method' => 'POST',
            'payload' => [
                'chatId' => detect_chat_id(is_string($notifiable) ? $notifiable : $notifiable['whatsapp_group']),
                'body' => "‼️ *[STORAGE] SPACE USAGE* ‼️

Customer: *{$this->storage['customer_name']}*$notifiedCustomerMessage
Usage Threshold: *>= 75%*

*[WAREHOUSE STORAGE]* " . ($this->storage['used_warehouse_percent'] >= 75 ? "❌" : "✅") . "
  Capacity: *" . numerical($this->storage['warehouse_capacity'], 2, true) . " M2*
  Storage Used: *" . numerical($this->storage['used_warehouse_storage'], 2, true) . " M2*
  Storage Available: *" . numerical($this->storage['warehouse_capacity'] - $this->storage['used_warehouse_storage'], 2, true) . " M2*
  Status: *" . numerical($this->storage['used_warehouse_percent'], 2, true) . "% Used*

*[YARD STORAGE]* " . ($this->storage['used_yard_percent'] >= 75 ? "❌" : "✅") . "
  Capacity: *" . numerical($this->storage['yard_capacity'], 2, true) . " M2*
  Storage Used: *" . numerical($this->storage['used_yard_storage'], 2, true) . " M2*
  Storage Available: *" . numerical($this->storage['yard_capacity'] - $this->storage['used_yard_storage'], 2, true) . " M2*
  Status: *" . numerical($this->storage['used_yard_percent'], 2, true) . "% Used*

*[COVERED YARD STORAGE]* " . ($this->storage['used_covered_yard_percent'] >= 75 ? "❌" : "✅") . "
  Capacity: *" . numerical($this->storage['covered_yard_capacity'], 2, true) . " M2*
  Storage Used: *" . numerical($this->storage['used_covered_yard_storage'], 2, true) . " M2*
  Storage Available: *" . numerical($this->storage['covered_yard_capacity'] - $this->storage['used_covered_yard_storage'], 2, true) . " M2*
  Status: *" . numerical($this->storage['used_covered_yard_percent'], 2, true) . "% Used*
  
Data captured at: {$this->captureDate}
",
            ]
        ];
    }
}
