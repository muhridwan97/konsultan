<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') OR exit('No direct script access allowed');

class ReportHeavyEquipmentModel extends MY_Model
{
    /**
     * Get transporter report.
     *
     * @param array $filters
     * @return array|int
     */
    public function getReportHeavyEquipmentUsage($filters = [])
    {
        $column = key_exists('order_by', $filters) ? $filters['order_by'] : 0;
        $sort = key_exists('order_method', $filters) ? $filters['order_method'] : 'desc';
        $search = key_exists('search', $filters) ? trim($filters['search']) : '';
        $length = key_exists('length', $filters) ? $filters['length'] : 10;
        $start = key_exists('start', $filters) ? $filters['start'] : -1;
        $branchId = key_exists('branch', $filters) ? $filters['branch'] : get_active_branch_id();

        $this->db->start_cache();

        $baseQuery = $this->db
            ->select([
                'requisitions.created_at AS request_date',
                'requisitions.no_requisition',
                'ref_item_categories.item_name AS item_category',
                '(SELECT GROUP_CONCAT(no_purchase) 
                    FROM ' . env('DB_PURCHASE_DATABASE') . '.purchase_offers
                    INNER JOIN ' . env('DB_PURCHASE_DATABASE') . '.purchase_orders ON purchase_orders.id_purchase_offer = purchase_offers.id 
                    WHERE id_requisition = requisitions.id 
                    GROUP BY id_requisition
                ) AS no_purchase_order', // prevent join & group by to optimize query
                'ref_branches.branch',
                'work_orders.taken_at',
                'work_orders.completed_at',
                '(SELECT GROUP_CONCAT(item_name) 
                    FROM ' . env('DB_PURCHASE_DATABASE') . '.requisition_items 
                    WHERE id_requisition = requisitions.id 
                    GROUP BY id_requisition
                ) AS heavy_equipment_item', // prevent join & group by to optimize query
                'IFNULL(booking_in.no_reference, bookings.no_reference) AS no_reference_in',
                'IF(booking_in.no_reference IS NULL, NULL, bookings.no_reference) AS no_reference_out',
                'customers.name AS customer_name',
                'ref_handling_types.handling_type',
                'work_orders.no_work_order',
            ])
            ->from(env('DB_PURCHASE_DATABASE') . '.requisitions')
            ->join(env('DB_PURCHASE_DATABASE') . '.ref_item_categories', 'ref_item_categories.id = requisitions.id_item_category', 'left')
            ->join('heavy_equipment_entry_permits', 'heavy_equipment_entry_permits.id_requisition = requisitions.id', 'left')
            ->join('work_order_component_heeps', 'work_order_component_heeps.id_heep = heavy_equipment_entry_permits.id', 'left')
            ->join('work_order_components', 'work_order_components.id = work_order_component_heeps.id_work_order_component', 'left')
            ->join('work_orders', 'work_orders.id = work_order_components.id_work_order', 'left')
            ->join('handlings', 'handlings.id = work_orders.id_handling', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('bookings', 'bookings.id = handlings.id_booking', 'left')
            ->join('bookings AS booking_in', 'booking_in.id = bookings.id_booking', 'left')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer', 'left')
            ->where([
                'requisitions.is_deleted' => 'false',
                'requisitions.id_category' => '39', // OPS RENT, HEAVY EQUIPMENT
            ])
            ->where_not_in('requisitions.status', ['PENDING', 'REJECTED', 'CANCELLED']);

        if (!empty($search)) {
            $baseQuery
                ->having('request_date LIKE', '%' . $search . '%')
                ->or_having('no_requisition LIKE', '%' . $search . '%')
                ->or_having('item_category LIKE', '%' . $search . '%')
                ->or_having('no_purchase_order LIKE', '%' . $search . '%')
                ->or_having('branch LIKE', '%' . $search . '%')
                ->or_having('taken_at LIKE', '%' . $search . '%')
                ->or_having('completed_at LIKE', '%' . $search . '%')
                ->or_having('heavy_equipment_item LIKE', '%' . $search . '%')
                ->or_having('no_reference_in LIKE', '%' . $search . '%')
                ->or_having('no_reference_out LIKE', '%' . $search . '%')
                ->or_having('customer_name LIKE', '%' . $search . '%')
                ->or_having('handling_type LIKE', '%' . $search . '%')
                ->or_having('no_work_order LIKE', '%' . $search . '%');
        }

        if (key_exists('customers', $filters) && !empty($filters['customers'])) {
            if($filters['customers'] == '-1') {
                $baseQuery->where('customers.id IS NULL');
            } else {
                $baseQuery->where_in('customers.id', $filters['customers']);
            }
        }

        if (key_exists('bookings', $filters) && !empty($filters['bookings'])) {
            $baseQuery
                ->group_start()
                ->where_in('bookings.id', $filters['bookings'])
                ->or_where_in('booking_in.id', $filters['bookings'])
                ->group_end();
        }

        if (key_exists('handling_types', $filters) && !empty($filters['handling_types'])) {
            $baseQuery->where_in('ref_handling_types.id', $filters['handling_types']);
        }

        if (key_exists('branches', $filters) && !empty($filters['branches'])) {
            $baseQuery->where_in('ref_branches.id', $filters['branches']);
        }

        if (key_exists('date_type', $filters) && !empty($filters['date_type'])) {
            if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
                $baseQuery->where('DATE(' . $filters['date_type'] . ')>=', format_date($filters['date_from']));
            }

            if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
                $baseQuery->where('DATE(' . $filters['date_type'] . ')<=', format_date($filters['date_to']));
            }
        }

        $this->db->stop_cache();

        if ($start < 0) {
            if ($column == 'no') $column = 'request_date';
            $allData = $baseQuery->order_by($column, $sort)->get()->result_array();

            $this->db->flush_cache();

            return $allData;
        }

        //$total = $this->db->count_all_results();
        $finalStockQuery = $this->db->get_compiled_select();
        $total = $this->db->query("SELECT COUNT(*) AS numrows FROM ({$finalStockQuery}) AS CI_count_all_results")->row_array()['numrows'];


        if ($column == 'no') $column = 'request_date';
        $page = $baseQuery->order_by($column, $sort)->limit($length, $start);
        $data = $page->get()->result_array();

        foreach ($data as &$row) {
            $row['no'] = ++$start;
        }

        $pageData = [
            "draw" => $this->input->get('draw'),
            "recordsTotal" => count($data),
            "recordsFiltered" => $total,
            "data" => $data
        ];
        $this->db->flush_cache();

        return $pageData;
    }

    /**
     * Export transporter to excel.
     *
     * @param $reports
     * @param $periods
     * @param $vehicles
     * @return string
     */
    public function exportTransporter($reports, $periods, $vehicles)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator($this->config->item('app_name'))
            ->setLastModifiedBy($this->config->item('app_name'))
            ->setTitle('Transporter')
            ->setSubject('Data export: Transporter')
            ->setDescription('Data export generated by: ' . $this->config->item('app_name'));

        try {
            $spreadsheet->setActiveSheetIndex(0);
            $activeSheet = $spreadsheet->getActiveSheet()->setTitle('Transporter');

            $activeSheet->setCellValue('A1', 'WEEK');
            $activeSheet->MergeCells('A1:A2');
            $columnIndex = 2;
            foreach ($vehicles as $vehicle) {
                $activeSheet->setCellValueByColumnAndRow($columnIndex, 1, strtoupper($vehicle['vehicle_name'] . ' - ' . $vehicle['no_plate']));
                $activeSheet->mergeCellsByColumnAndRow($columnIndex, 1, $columnIndex+2, 1);
                $activeSheet->setCellValueByColumnAndRow($columnIndex++, 2, 'Trip');
                $activeSheet->setCellValueByColumnAndRow($columnIndex++, 2, 'Total %');
                $activeSheet->setCellValueByColumnAndRow($columnIndex++, 2, 'Prod %');
            }
            $activeSheet->setCellValueByColumnAndRow($columnIndex, 1, 'TOTAL');

            foreach ($periods as $rowPeriod => $period) {
                $activeSheet->setCellValueByColumnAndRow(1, $rowPeriod + 3, $period);
                if (key_exists($period, $reports)) {
                    foreach ($vehicles as $indexVehicle => $vehicle) {
                        foreach ($reports[$period]['vehicles'] as $report) {
                            if ($report['no_plate'] == $vehicle['no_plate'] && $report['week'] == $period) {
                                $activeSheet->setCellValueByColumnAndRow(2 + (($indexVehicle + 1) - 1)*3, $rowPeriod + 3, $report['total_safe_conduct']);
                                $activeSheet->setCellValueByColumnAndRow(3 + (($indexVehicle + 1) - 1)*3, $rowPeriod + 3, round($report['percentage']) . '%');
                                $activeSheet->setCellValueByColumnAndRow(4 + (($indexVehicle + 1) - 1)*3, $rowPeriod + 3, round($report['prod']) . '%');
                                break;
                            }
                        }
                    }
                    $activeSheet->setCellValueByColumnAndRow(2 + ((count($vehicles) + 1) - 1)*3, $rowPeriod + 3, $reports[$period]['total']);
                }
            }
            $columnIterator = $spreadsheet->getActiveSheet()->getColumnIterator();
            foreach ($columnIterator as $column) {
                $spreadsheet->getActiveSheet()
                    ->getColumnDimension($column->getColumnIndex())
                    ->setAutoSize(true);

                $spreadsheet->getActiveSheet()
                    ->getStyle($column->getColumnIndex() . '1')
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
                    )
                    ->getAlignment()
                    ->setHorizontal('center');
                    $spreadsheet->getActiveSheet()
                    ->getStyle($column->getColumnIndex() . '2')
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
                    )
                    ->getAlignment()
                    ->setHorizontal('center');
            }

            // center all cells
            $spreadsheet->getActiveSheet()
                ->getStyleByColumnAndRow(1, 1, 2 + ((count($vehicles) + 1) - 1)*3 , count($periods) + 2)
                ->getAlignment()
                ->setHorizontal('center');

            if (!empty($data)) {
                $activeSheet->setAutoFilterByColumnAndRow(1, 1, 2 + ((count($vehicles) + 1) - 1)*3 , 1);
            }

            $excelWriter = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Transporter.xlsx"');
            $excelWriter->save('php://output');

        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            return $e->getMessage();
        }
    }

}
