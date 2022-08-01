<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') or exit('No direct script access allowed');

class ReportMovementModel extends MY_Model
{
    /**
     * Get balance of transaction movement.
     *
     * @param array $filters
     * @return array|null
     */
    private function getBalance($filters = [])
    {
        if (empty(get_if_exist($filters, 'stock_date'))) {
            show_error('Filter invalid: stock_date parameter is required');
        }

        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $baseQuery = $this->db->select([
            "'" . format_date($filters['stock_date']) . "' AS date",
            'IFNULL(SUM(quantity * multiplier_goods), 0) AS quantity_balance',
            'IFNULL(SUM((ref_goods.unit_gross_weight * quantity) * multiplier_goods) / 1000, 0) AS gross_weight_balance',
            'IFNULL(SUM((ref_goods.unit_volume * quantity) * multiplier_goods), 0) AS volume_balance',
            'IFNULL(SUM(
                IF((ref_goods.unit_volume * quantity) > (ref_goods.unit_gross_weight * quantity / 1000), 
                    (ref_goods.unit_volume * quantity),
                    (ref_goods.unit_gross_weight * quantity / 1000)
                )
                * multiplier_goods
            ), 0) AS weight_volume_balance',
        ])
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            //->join('booking_references', 'booking_references.id_booking = bookings.id', 'left') // remove comments to make query compatible with stock summary
            //->join('bookings AS booking_inbounds', 'booking_inbounds.id = IFNULL(booking_references.id_booking_reference, bookings.id)', 'left')
            //->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id AND IFNULL(work_order_goods.id_booking_reference, bookings.id) = booking_inbounds.id')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id') // comment this list to make query compatible with stock summary
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods')
            //->join('ref_people AS customers', 'customers.id = booking_inbounds.id_customer')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer') // comment this line to make query compatible with stock summary
            ->where([
                'customers.is_deleted' => false,
                'bookings.is_deleted' => false,
                'work_orders.is_deleted' => false,
                'work_order_goods.is_deleted' => false,
                'handlings.is_deleted' => false,
                'ref_handling_types.multiplier_goods!=' => 0,
                'handlings.status' => 'APPROVED',
                'work_orders.status' => 'COMPLETED'
            ]);

        if (key_exists('booking', $filters) && !empty($filters['booking'])) {
            //$baseQuery->where_in('booking_inbounds.id', $filters['booking']);
            $baseQuery->where_in('bookings.id', $filters['booking']);
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->where_in('customers.id', $filters['customer']);
        }

        if (key_exists('goods', $filters) && !empty($filters['goods'])) {
            $baseQuery->where_in('work_order_goods.id_goods', $filters['goods']);
        }

        if (key_exists('stock_date', $filters) && !empty($filters['stock_date'])) {
            $baseQuery->where('work_orders.completed_at<=', format_date($filters['stock_date'], 'Y-m-d 23:59:59'));
        }

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $baseQuery->where('customers.id', $customerId);
        }

        return $baseQuery->get()->row_array();
    }

    /**
     * Get movement summary goods.
     *
     * @param array $filters
     * @return array|int
     */
    public function getMovementGoods($filters = [])
    {
        $branchId = get_if_exist($filters, 'branch', get_active_branch('id'));

        $userType = UserModel::authenticatedUserData('user_type');
        $customerId = UserModel::authenticatedUserData('id_person');

        $baseQuery = $this->db->select([
            'customers.name AS customer_name',
            'bookings.no_reference',
            'ref_booking_types.booking_type',
            'work_orders.no_work_order',
            'ref_handling_types.handling_type',
            'work_orders.completed_at AS date',
            'ref_goods.name AS goods_name',
            'IF(multiplier_goods = 1, quantity, 0) AS quantity_inbound',
            'IF(multiplier_goods = -1, quantity, 0) AS quantity_outbound',
            'IF(multiplier_goods = 1, ref_goods.unit_gross_weight * quantity / 1000, 0) AS gross_weight_inbound',
            'IF(multiplier_goods = -1, ref_goods.unit_gross_weight * quantity / 1000, 0) AS gross_weight_outbound',
            'IF(multiplier_goods = 1, ref_goods.unit_volume * quantity, 0) AS volume_inbound',
            'IF(multiplier_goods = -1, ref_goods.unit_volume * quantity, 0) AS volume_outbound',
            'IF(multiplier_goods = 1, 
                IF((ref_goods.unit_volume * quantity) > (ref_goods.unit_gross_weight * quantity / 1000), 
                    (ref_goods.unit_volume * quantity),
                    (ref_goods.unit_gross_weight * quantity / 1000)
                ), 
                0
            ) AS weight_volume_inbound',
            'IF(multiplier_goods = -1, 
                IF((ref_goods.unit_volume * quantity) > (ref_goods.unit_gross_weight * quantity / 1000), 
                    (ref_goods.unit_volume * quantity),
                    (ref_goods.unit_gross_weight * quantity / 1000)
                ), 
                0
            ) AS weight_volume_outbound',
            'uploads.description AS upload_title',
        ])
            ->from('work_orders')
            ->join('handlings', 'handlings.id = work_orders.id_handling')
            ->join('bookings', 'bookings.id = handlings.id_booking')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type')
            ->join('work_order_goods', 'work_order_goods.id_work_order = work_orders.id')
            ->join('ref_goods', 'ref_goods.id = work_order_goods.id_goods')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer')
            ->join('uploads', 'uploads.id = bookings.id_upload')
            ->where([
                'customers.is_deleted' => false,
                'bookings.is_deleted' => false,
                'work_orders.is_deleted' => false,
                'work_order_goods.is_deleted' => false,
                'handlings.is_deleted' => false,
                'ref_handling_types.multiplier_goods!=' => 0,
                'handlings.status' => 'APPROVED',
                'work_orders.status' => 'COMPLETED'
            ])
            /*->group_start()
                ->where('multiplier_goods', -1)
                ->where('work_order_goods.id_booking_reference IS NOT NULL', null)
                ->or_where('multiplier_goods', 1)
            ->group_end()*/ // compatible with stock summary (should have inbound reference)
            ->order_by('work_orders.completed_at', 'asc');

        if (key_exists('booking', $filters) && !empty($filters['booking'])) {
            $baseQuery->where_in('bookings.id', $filters['booking']);
        }

        if (key_exists('customer', $filters) && !empty($filters['customer'])) {
            $baseQuery->where_in('customers.id', $filters['customer']);
        }

        if (key_exists('goods', $filters) && !empty($filters['goods'])) {
            $baseQuery->where_in('work_order_goods.id_goods', $filters['goods']);
        }

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $baseQuery->where('work_orders.completed_at>=', format_date($filters['date_from'], 'Y-m-d 00:00:00'));
        } else {
            show_error('Filter invalid: date_from parameter is required');
        }

        if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
            $baseQuery->where('work_orders.completed_at<=', format_date($filters['date_to'], 'Y-m-d 23:59:59'));
        } else {
            show_error('Filter invalid: date_to parameter is required');
        }

        if (!empty($branchId)) {
            $baseQuery->where('bookings.id_branch', $branchId);
        }

        if ($userType == 'EXTERNAL') {
            $baseQuery->where('customers.id', $customerId);
        }

        $transactions = $baseQuery->get()->result_array();

        // get last balance
        $balanceDate = date('Y-m-d', strtotime($filters['date_from'] . ' -1 day'));
        $filters['stock_date'] = $balanceDate;
        $balance = $this->getBalance($filters);

        if (empty($balance)) {
            $balance = [
                'date' => $balanceDate,
                'quantity_balance' => 0,
                'gross_weight_balance' => 0,
                'volume_balance' => 0,
                'weight_volume_balance' => 0,
            ];
        }

        $lastQuantityBalance = $balance['quantity_balance'];
        $lastGrossWeightBalance = $balance['gross_weight_balance'];
        $lastVolumeBalanceBalance = $balance['volume_balance'];
        $lastWeightVolumeBalance = $balance['weight_volume_balance'];
        foreach ($transactions as &$transaction) {
            $lastQuantityBalance += $transaction['quantity_inbound'] - $transaction['quantity_outbound'];
            $lastGrossWeightBalance += $transaction['gross_weight_inbound'] - $transaction['gross_weight_outbound'];
            $lastVolumeBalanceBalance += $transaction['volume_inbound'] - $transaction['volume_outbound'];
            $lastWeightVolumeBalance += $transaction['weight_volume_inbound'] - $transaction['weight_volume_outbound'];

            $transaction['quantity_balance'] = $lastQuantityBalance;
            $transaction['gross_weight_balance'] = $lastGrossWeightBalance;
            $transaction['volume_balance'] = $lastVolumeBalanceBalance;
            $transaction['weight_volume_balance'] = $lastWeightVolumeBalance;
        }

        return [
            'balance' => $balance,
            'transactions' => $transactions
        ];
    }

    /**
     * Export data to excel.
     *
     * @param $customerName
     * @param $period
     * @param $data
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     */
    public function exportMovementGoods($customerName, $period, $data)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator($this->config->item('app_name'))
            ->setLastModifiedBy($this->config->item('app_name'))
            ->setTitle('Customer Storage Activity')
            ->setSubject('Customer Storage')
            ->setDescription('Data export generated by: ' . $this->config->item('app_name'));
        $sheet = $spreadsheet->getActiveSheet();

        // add customer title
        $sheet->setCellValue('A1', 'CUSTOMER NAME:');
        $sheet->setCellValue('B1', $customerName);
        $sheet->setCellValue('A2', 'PERIOD DATE:');
        $sheet->setCellValue('B2', "{$period['date_from']} - {$period['date_to']}");

        // add table header row 1
        $sheet->setCellValue('A4', 'DATE');
        $sheet->mergeCells('A4:A5');
        $sheet->setCellValue('B4', 'BOOKING TYPE');
        $sheet->mergeCells('B4:B5');
        $sheet->setCellValue('C4', 'ITEM');
        $sheet->mergeCells('C4:C5');
        $sheet->setCellValue('D4', 'UPLOAD TITLE');
        $sheet->mergeCells('D4:D5');
        $sheet->setCellValue('E4', 'Quantity (Unit)');
        $sheet->mergeCells('E4:G4');
        $sheet->setCellValue('H4', 'Gross Weight (Ton)');
        $sheet->mergeCells('H4:J4');
        $sheet->setCellValue('K4', 'Volume (M3)');
        $sheet->mergeCells('K4:M4');
        $sheet->setCellValue('N4', 'Weight/Volume (M3/Ton)');
        $sheet->mergeCells('N4:P4');

        // header row 2
        $sheet->setCellValue('E5', 'Inbound');
        $sheet->setCellValue('F5', 'Outbound');
        $sheet->setCellValue('G5', 'Balance');
        $sheet->setCellValue('H5', 'Inbound');
        $sheet->setCellValue('I5', 'Outbound');
        $sheet->setCellValue('J5', 'Balance');
        $sheet->setCellValue('K5', 'Inbound');
        $sheet->setCellValue('L5', 'Outbound');
        $sheet->setCellValue('M5', 'Balance');
        $sheet->setCellValue('N5', 'Inbound');
        $sheet->setCellValue('O5', 'Outbound');
        $sheet->setCellValue('P5', 'Balance');

        // styling header fill color and alignment
        $sheet
            ->getStyle('A4:P5')
            ->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '000000']
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => 'FFFFFF']
                    ]
                ]
            );

        $sheet->getStyle('E4:P5')->getAlignment()
            ->setHorizontal(Alignment::HORIZONTAL_CENTER)
            ->setVertical(Alignment::VERTICAL_CENTER);

        $sheet->getStyle('A4:D5')->getAlignment()
            ->setVertical(Alignment::VERTICAL_CENTER);

        // add balance row
        $sheet->setCellValue('A6', $data['balance']['date']);
        $sheet->setCellValue('C6', 'Beginning balance');
        $sheet->mergeCells('C6:D6');
        $sheet->setCellValue('G6', $data['balance']['quantity_balance']);
        $sheet->setCellValue('J6', $data['balance']['gross_weight_balance']);
        $sheet->setCellValue('M6', $data['balance']['volume_balance']);
        $sheet->setCellValue('P6', $data['balance']['weight_volume_balance']);
        $sheet
            ->getStyle("A6:P6")
            ->applyFromArray([
                    'fill' => [
                        'fillType' => Fill::FILL_SOLID,
                        'color' => ['rgb' => '92D050']
                    ],
                    'font' => [
                        'bold' => true,
                        'color' => ['rgb' => '000000']
                    ]
                ]
            );

        // add transaction rows
        $row = 7;
        foreach ($data['transactions'] as $index => $item) {
            $sheet->setCellValue('A' . $row, format_date($item['date']));
            $sheet->setCellValue('B' . $row, $item['booking_type']);
            $sheet->setCellValue('C' . $row, $item['goods_name']);
            $sheet->setCellValue('D' . $row, $item['upload_title']);
            $sheet->setCellValue('E' . $row, $item['quantity_inbound']);
            $sheet->setCellValue('F' . $row, $item['quantity_outbound']);
            $sheet->setCellValue('G' . $row, $item['quantity_balance']);
            $sheet->setCellValue('H' . $row, $item['gross_weight_inbound']);
            $sheet->setCellValue('I' . $row, $item['gross_weight_outbound']);
            $sheet->setCellValue('J' . $row, $item['gross_weight_balance']);
            $sheet->setCellValue('K' . $row, $item['volume_inbound']);
            $sheet->setCellValue('L' . $row, $item['volume_outbound']);
            $sheet->setCellValue('M' . $row, $item['volume_balance']);
            $sheet->setCellValue('N' . $row, $item['weight_volume_inbound']);
            $sheet->setCellValue('O' . $row, $item['weight_volume_outbound']);
            $sheet->setCellValue('P' . $row, $item['weight_volume_balance']);
            $row++;
        }

        // set auto column width
        $columnIterator = $sheet->getColumnIterator();
        foreach ($columnIterator as $column) {
            if (in_array($column->getColumnIndex(), ['A', 'B'])) continue;
            $sheet->getColumnDimension($column->getColumnIndex())->setAutoSize(true);
        }

        // specify for some columns
        $sheet->getColumnDimension('A')->setWidth(17);
        $sheet->getColumnDimension('B')->setWidth(22);

        // apply border for data grid
        $sheet
            ->getStyleByColumnAndRow(1, 4, 16, $row - 1)
            ->applyFromArray([
                    'borders' => array(
                        'allBorders' => array(
                            'borderStyle' => Border::BORDER_THIN,
                        ),
                    ),
                ]
            );

        // send download response
        $writer = new Xlsx($spreadsheet);
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="Goods Movement.xlsx"');
        $writer->save('php://output');
    }

}
