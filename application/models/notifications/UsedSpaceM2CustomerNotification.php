<?php

/**
 * Class UsedSpaceCustomerNotification
 */
class UsedSpaceM2CustomerNotification extends Notify
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
                'body' => "🏭 *[STORAGE] USED SPACE* 🏭 ️

Customer: *{$this->customer['customer_name']}*
Branch: *{$this->customer['branch']}*
Date: *" . date('Y-m-d') . "*
Used Warehouse: *" . numerical($this->customer['used_warehouse_storage'], 2, true) . " M2*
Used Yard: *" . numerical($this->customer['used_yard_storage'], 2, true) . " M2*
Used Covered: *" . numerical($this->customer['used_covered_yard_storage'], 2, true) . " M2*",
            ]
        ];
    }
}
