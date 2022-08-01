<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class InvoiceDetailModel extends MY_Model
{
    protected $table = 'invoice_details';

    /**
     * InvoiceDetailDetailModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active record query builder for all related invoice detail selection.
     * @return CI_DB_query_builder
     */
    public function getBaseInvoiceDetailQuery()
    {
        $branchId = get_active_branch('id');
        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $invoiceDetails = $this->db->select([
            'invoices.no_invoice',
            'ref_people.name AS customer_name',
            'invoice_details.*',
            '(unit_price * quantity * unit_multiplier) AS total'
        ])
            ->from($this->table)
            ->join('invoices', 'invoices.id = invoice_details.id_invoice', 'left')
            ->join('ref_people', 'invoices.id_customer = ref_people.id', 'left');

        if (!empty($branchId)) {
            $invoiceDetails->where('invoices.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $invoiceDetails->where('invoices.id_customer', $customerId);
        }

        return $invoiceDetails;
    }

    /**
     * Get all invoice detail with or without deleted records.
     * @return array
     */
    public function getAllInvoiceDetails()
    {
        $invoiceDetails = $this->getBaseInvoiceDetailQuery();

        return $invoiceDetails->get()->result_array();
    }

    /**
     * Get invoice detail data by invoice.
     * @param $invoiceId
     * @param null $type
     * @return mixed
     */
    public function getInvoiceDetailByInvoice($invoiceId, $type = null)
    {
        $invoiceDetails = $this->getBaseInvoiceDetailQuery()->where('invoices.id', $invoiceId);

        if (!empty($type)) {
            if (is_array($type)) {
                $invoiceDetails->where_in('invoice_details.type', $type);
            } else {
                $invoiceDetails->where('invoice_details.type', $type);
            }
        }

        $invoiceDetails = $invoiceDetails->get()->result_array();

        $orderTable = ['STORAGE' => 0, 'HANDLING' => 1, 'COMPONENT' => 2, 'INVOICE' => 3, 'PAYMENT' => 4, 'OTHER' => 5];
        for ($i = 0; $i < count($invoiceDetails) - 1; $i++) {
            for ($j = $i + 1; $j < count($invoiceDetails); $j++) {
                if (key_exists($invoiceDetails[$i]['type'], $orderTable) && key_exists($invoiceDetails[$j]['type'], $orderTable)) {
                    if ($orderTable[$invoiceDetails[$i]['type']] > $orderTable[$invoiceDetails[$j]['type']]) {
                        $temp = $invoiceDetails[$i];
                        $invoiceDetails[$i] = $invoiceDetails[$j];
                        $invoiceDetails[$j] = $temp;
                    }
                } else {
                    show_error('Does not recognize type ' . $invoiceDetails[$i]['type'] . ' in invoice');
                }
            }
        }

        $this->setInvoiceLayout($invoiceDetails);

        return $invoiceDetails;
    }

    /**
     * Generate invoice layout data.
     * @param $invoiceDetails
     */
    public function setInvoiceLayout(&$invoiceDetails)
    {
        foreach ($invoiceDetails as &$invoiceDetail) {
            $days = 0;
            $container20A = 0;
            $container20B = 0;
            $container40A = 0;
            $container4045B = 0;
            $goodsAll = '';

            $containerSummary = [];
            $goodsSummary = [];

            // separate item data (container|goods)
            $items = explode('|', $invoiceDetail['item_summary']);
            if (!empty($items)) {
                // CONTAINER/size, CONTAINER/type, CONTAINER/type/day, etc....
                if (preg_match('/CONTAINER/', $invoiceDetail['unit'])) {
                    $containerSummary = explode(',', $items[0]);
                }
                // GOODS/pcs, GOODS/grm, GOODS/tonnage (Kg), GOODS/volume (M<sup>3</sup>, etc....
                if (preg_match('/GOODS/', $invoiceDetail['unit'])) {
                    $goodsSummary = explode(',', $items[0]);
                }
                // ACTIVITY, ACTIVITY/day, INVOICE, OTHER, etc
                if (count($items) > 1) {
                    // (containers|goods)
                    $containerSummary = explode(',', $items[0]);
                    $goodsSummary = explode(',', $items[1]);
                } else {
                    // decide if contain container or goods
                    $sampleSummary = explode(',', $items[0]);
                    $sampleAttribute = get_string_between(end($sampleSummary), '(', ')');
                    $attributes = explode('-', $sampleAttribute);
                    if (count($attributes) > 3) {
                        // goods contains 4 attribute: APRON PAN I (2.000PCE-3.000Kg-4.000M3-NOT DANGER)
                        $goodsSummary = explode(',', $items[0]);
                    } else {
                        // container contains 3 attribute: TCNU2765102 (STD-40-NOT DANGER)
                        $containerSummary = explode(',', $items[0]);
                    }
                }
            }

            // break down container attributes and classify to the group: 20A, 20B, 40A, 4045B
            foreach ($containerSummary as $container) {
                $containerAttribute = get_string_between($container, '(', ')');
                $attributes = explode('-', $containerAttribute);
                $type = key_exists(0, $attributes) ? $attributes[0] : '';
                $size = key_exists(1, $attributes) ? $attributes[1] : '';
                $danger = key_exists(2, $attributes) ? $attributes[2] : '';

                if ($size == '20') {
                    if ($type == 'STD' || $type == 'HC') {
                        $container20A++;
                    }
                    if ($type == 'OH' || $type == 'OW' || $type == 'OL' || $type == 'OT' || $type == 'FR') {
                        $container20B++;
                    }
                }

                if ($size == '40') {
                    if ($type == 'STD' || $type == 'HC') {
                        $container40A++;
                    }
                    if ($type == 'OH' || $type == 'OW' || $type == 'OL' || $type == 'OT' || $type == 'FR') {
                        $container4045B++;
                    }
                }

                if ($size == '45') {
                    $container4045B++;
                }
            }

            // classify item data to the group depends on unit type (unit name, tonnage, or volume)
            $totalQuantity = 0;
            $totalTonnage = 0;
            $totalVolume = 0;
            foreach ($goodsSummary as $item) {
                $itemAttribute = get_string_between($item, '(', ')');
                $attributes = explode('-', $itemAttribute);
                $quantity = key_exists(0, $attributes) ? preg_replace('/[a-zA-Z]/', '', $attributes[0]) : 0;
                $tonnage = key_exists(1, $attributes) ? preg_replace('/[a-zA-Z]/', '', $attributes[1]) : 0;
                $volume = key_exists(2, $attributes) ? preg_replace('/(M3|m3)/', '', $attributes[2]) : 0;
                $danger = key_exists(3, $attributes) ? $attributes[3] : '';

                $totalQuantity += is_numeric($quantity) ? $quantity : 0;
                $totalTonnage += is_numeric($tonnage) ? $tonnage : 0;
                $totalVolume += is_numeric($volume) ? $volume : 0;

                // show all attribute if we don't know calculate the price (ACTIVITY, ACTIVITY/day)
                $goodsAll = $totalQuantity . ' Unit, ' . $totalTonnage . ' Kg, ' . $totalVolume . ' M<sup>3</sup>';

                // if we specify by price sub type GOODS/etc... GOODS/pcs, GOODS/Tonnage (Kg), GOODS/volume (M<sup>3</sup>)
                if (preg_match('/GOODS/', $invoiceDetail['unit'])) {
                    if (preg_match('/\/tonnage/', $invoiceDetail['unit'])) {
                        $goodsAll = $totalTonnage . ' Kg';
                    }
                    if (preg_match('/\/volume/', $invoiceDetail['unit'])) {
                        $goodsAll = $totalVolume . ' M<sup>3</sup>';
                    }
                    if (!strpos($invoiceDetail['unit'], '/tonnage') && !strpos($invoiceDetail['unit'], '/volume')) {
                        $goodsAll = $totalQuantity . ' Unit';
                    }
                }
            }

            if ($invoiceDetail['item_name'] == 'STORAGE') {
                if (preg_match('/\/day/', $invoiceDetail['unit'])) {
                    $days = numerical($invoiceDetail['unit_multiplier'], 3, true);
                }
            }

            $invoiceDetail['days'] = $days;
            $invoiceDetail['20A'] = $container20A;
            $invoiceDetail['20B'] = $container20B;
            $invoiceDetail['40A'] = $container40A;
            $invoiceDetail['4045B'] = $container4045B;
            $invoiceDetail['LCL'] = $goodsAll;
        }
    }

    /**
     * Get single invoice detail data by id with or without deleted record.
     * @param integer $id
     * @return array
     */
    public function getInvoiceDetailById($id)
    {
        $invoice = $this->getBaseInvoiceDetailQuery()
            ->where('invoice_details.id', $id);

        return $invoice->get()->row_array();
    }

    /**
     * Create new invoice detail data.
     * @param $data
     * @return bool
     */
    public function createInvoiceDetail($data)
    {
        if (key_exists(0, $data) && is_array($data[0])) {
            return $this->db->insert_batch($this->table, $data);
        }
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update invoice detail data.
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateInvoiceDetail($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete invoice detail data.
     * @param integer $id
     * @param bool $softDelete
     * @return bool
     */
    public function deleteInvoiceDetail($id, $softDelete = true)
    {
        if ($softDelete) {
            return $this->db->update($this->table, [
                'is_deleted' => true,
                'deleted_at' => date('Y-m-d H:i:s'),
                'deleted_by' => UserModel::authenticatedUserData('id')
            ], ['id' => $id]);
        }
        return $this->db->delete($this->table, ['id' => $id]);
    }

}