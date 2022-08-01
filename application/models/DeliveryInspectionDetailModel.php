<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class DeliveryInspectionDetailModel extends MY_Model
{
    protected $table = 'delivery_inspection_details';

    /**
     * DeliveryInspectionDetailModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    protected function getBaseQuery($branchId = null)
    {
        return parent::getBaseQuery($branchId)
            ->select([
                'delivery_inspections.date'
            ])
            ->join('delivery_inspections', 'delivery_inspections.id = delivery_inspection_details.id_delivery_inspection');
    }

}
