<?php

/**
 * Class OverSpaceM2SummaryNotification
 * @property ReportStorageModel $reportStorage
 */
class OverSpaceM2SummaryNotification extends Notify
{
    private $storageCustomer;
    private $storageActivity;
    private $emailOption;

    public function __construct($storageCustomer = null, $storageActivity = null, $emailOption = null)
    {
        parent::__construct();

        $this->storageCustomer = $storageCustomer;
        $this->storageActivity = $storageActivity;
        $this->emailOption = $emailOption;

        $this->load->model('ReportStorageModel', 'reportStorage');
    }

    /**
     * Mail notification.
     *
     * @param $notifiable
     * @return array
     */
    public function toMail($notifiable)
    {
        $excelPath = $this->reportStorage->exportCustomerStorageActivities($this->storageActivity, false);

        $options = array_merge($this->emailOption, [
            'attachment' => [
                [
                    'source' => $excelPath,
                    'disposition' => 'attachment',
                    'file_name' => "storage-activity.xlsx",
                ]
            ]
        ]);

        $tableContent = '<table style="width: 100%; border-collapse: collapse; margin-bottom: 30px; color: #2D2F31; font-size: 13px">
            <tr style="border-bottom: 1px solid #74787E; border-top: 1px solid #74787E;">
               <th style="padding: 5px 4px">Type</th>
               <th style="padding: 5px 4px">Is Over Space</th>
            </tr>
            <tr style="border-bottom: 1px solid #74787E;">
               <td style="padding: 5px 4px">Warehouse</td>
               <td style="padding: 5px 4px">' . ($this->storageCustomer['is_warehouse_over_space'] ? 'YES' : 'NO') . '</td>    
            </tr>
            <tr style="border-bottom: 1px solid #74787E;">
               <td style="padding: 5px 4px">Yard</td>
               <td style="padding: 5px 4px">' . ($this->storageCustomer['is_yard_over_space'] ? 'YES' : 'NO') . '</td>    
            </tr>
            <tr style="border-bottom: 1px solid #74787E;">
               <td style="padding: 5px 4px">Covered Yard</td>
               <td style="padding: 5px 4px">' . ($this->storageCustomer['is_covered_yard_over_space'] ? 'YES' : 'NO') . '</td>    
            </tr>
        </table>';

        return [
            'to' => $notifiable,
            'subject' => "Over space storage summary {$this->storageCustomer['customer_name']} from {$this->storageCustomer['date_from']} until {$this->storageCustomer['date_to']} ({$this->storageCustomer['type']})",
            'template' => 'emails/basic',
            'data' => [
                'title' => 'Over space storage activity',
                'name' => 'Admin',
                'content' => "
                    <p>
                        Below is storage activity of customer {$this->storageCustomer['customer_name']} in period range of dates
                        from {$this->storageCustomer['date_from']} until {$this->storageCustomer['date_to']} ({$this->storageCustomer['type']}).
                    </p>
                    
                    $tableContent
                                
                    <b>See attachment for detail of the activity!</b>
                "
            ],
            'option' => $options
        ];
    }
}
