<?php

/**
 * Class UsedSpaceM2SummaryNotification
 */
class UsedSpaceM2SummaryNotification extends Notify
{
    private $customerStorages;
    private $branch;

    public function __construct($customerStorages = null, $branch = null)
    {
        $this->customerStorages = $customerStorages;
        $this->branch = $branch;
    }

    /**
     * Mail notification.
     *
     * @param $notifiable
     * @return array
     */
    public function toMail($notifiable)
    {
        return [
            'to' => $notifiable,
            'subject' => "Used space storage summary",
            'template' => 'emails/used_space_summary_m2',
            'data' => [
                'name' => 'Admin',
                'email' => $notifiable,
                'branch' => $this->branch,
                'customerStorages' => $this->customerStorages,
            ],
        ];
    }
}
