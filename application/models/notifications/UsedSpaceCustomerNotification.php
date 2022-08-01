<?php

/**
 * Class UsedSpaceCustomerNotification
 */
class UsedSpaceCustomerNotification extends Notify
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

Customer: *{$this->customer['name']}*
Branch: *{$this->customer['branch']}*
Date: *" . date('Y-m-d') . "*
Used Warehouse: *" . numerical($this->customer['warehouse_space_used'], 2, true) . " TEUS*
Used Yard: *" . numerical($this->customer['yard_space_used'], 2, true) . " TEUS*
Used Covered: *" . numerical($this->customer['covered_yard_space_used'], 2, true) . " TEUS*",
            ]
        ];
    }
}
