<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') OR exit('No direct script access allowed');

class OpnameModel extends MY_Model
{
    protected $table = 'opnames';

    const STATUS_PENDING = 'PENDING';
    const STATUS_APPROVED = 'APPROVED';
    const STATUS_REJECTED = 'REJECTED';
    const STATUS_COMPLETED = 'COMPLETED'; 
    const STATUS_PROCESSED = 'PROCESSED';
    const STATUS_REOPENED = 'REOPENED';
    const STATUS_CLOSED = 'CLOSED';

    /**
     * Get active record query builder for all related data selection.
     * @param null $branchId
     * @return mixed
     */
    protected function getBaseQuery($branchId = null)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $opnames = $this->db->select([
            'opnames.*',
            'prv_users.username as requested_by',
            'validator.username as validated_by',
            'ref_branches.branch',
        ])
            ->from($this->table)
            ->join(UserModel::$tableUser, 'prv_users.id = opnames.created_by', 'left')
            ->join(UserModel::$tableUser . ' AS validator', 'validator.id = opnames.validated_by', 'left')
            ->join('ref_branches', 'ref_branches.id = opnames.id_branch', 'left');

        if (!empty($branchId)) {
            $opnames->where('opnames.id_branch', $branchId);
        }

        return $opnames;
    }

    /**
     * Get last opname goods.
     * @param null $branchId
     * @return mixed
     */
    public function getLastOpname($branchId , $type, $withTrashed = false)
    {
        $opnames = $this->getBaseQuery()->where('opnames.id_branch', $branchId)->where('opnames.opname_type', $type)->where('opnames.opname_date !=', date("Y-m-d"))->order_by('opname_date', 'DESC');

        if (!$withTrashed) {
            $opnames->where('opnames.is_deleted', false);
        }

        return $opnames->get()->row_array();
    }

     /**
     * Get opname by branchId and by opname date.
     * @return array
     */
    public function opnameCheck($branchId, $type, $date, $withTrashed = false)
    {

        $opnames = $this->getBaseQuery()->where('opnames.id_branch', $branchId)->where('opnames.opname_type', $type)->where('opname_date', $date);

        if (!$withTrashed) {
            $opnames->where('opnames.is_deleted', false);
        }

        return $opnames->get()->result_array();

    }

    /**
     * Get opname by pending status.
     * @return array
     */
    public function opnamePendingStatus($branchId = null, $withTrashed = false)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $opnames = $this->getBaseQuery()->where('opnames.status', OpnameModel::STATUS_PENDING);

        if (!$withTrashed) {
            $opnames->where('opnames.is_deleted', false);
        }

        if (!empty($branchId)) {
            $opnames->where('opnames.id_branch', $branchId);
        }
        

        return $opnames->get()->result_array();

    }

    /**
     * Get opname by processed status.
     * @return array
    */
    public function opnameProcessStatus($branchId = null, $withTrashed = false)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $opnames = $this->getBaseQuery()->where('opnames.status', OpnameModel::STATUS_PROCESSED);

        if (!$withTrashed) {
            $opnames->where('opnames.is_deleted', false);
        }

        if (!empty($branchId)) {
            $opnames->where('opnames.id_branch', $branchId);
        }
        

        return $opnames->get()->result_array();

    }

    /**
     * Get opname by rejected status
     * @return array
     */
    public function opnameRejectStatus($branchId = null, $withTrashed = false)
    {
        if (empty($branchId)) {
            $branchId = get_active_branch('id');
        }

        $opnames = $this->getBaseQuery()->where('opnames.status', OpnameModel::STATUS_REJECTED);

        if (!$withTrashed) {
            $opnames->where('opnames.is_deleted', false);
        }

        if (!empty($branchId)) {
            $opnames->where('opnames.id_branch', $branchId);
        }
        
        return $opnames->get()->result_array();
    }

    /**
     * Get auto number for opname.
     *
     * @return string
     */
    public function getAutoNumberOpname()
    {
        $orderData = $this->db->query("
            SELECT IFNULL(CAST(RIGHT(no_opname, 6) AS UNSIGNED), 0) + 1 AS order_number 
            FROM opnames 
            WHERE SUBSTR(no_opname, 7, 2) = MONTH(NOW()) 
			AND SUBSTR(no_opname, 4, 2) = DATE_FORMAT(NOW(), '%y')
            ORDER BY SUBSTR(no_opname FROM 4) DESC LIMIT 1
        ");
        $orderPad = '000001';
        if ($orderData->num_rows()) {
            $lastOrder = $orderData->row_array();
            $orderPad = str_pad($lastOrder['order_number'], 6, '0', STR_PAD_LEFT);
        }
        return 'OP/' . date('y') . '/' . date('m') . '/' . $orderPad;
    }

    /**
     * Export data to excel.
     *
     * @param $opname
     * @param $data
     * @return false|string
     */
    public function exportResultToExcel($opname, $data)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator($this->config->item('app_name'))
            ->setLastModifiedBy($this->config->item('app_name'))
            ->setTitle('Opname')
            ->setSubject('Data export: Opname')
            ->setDescription('Data export generated by: ' . $this->config->item('app_name'));

        try {
            $spreadsheet->setActiveSheetIndex(0);
            $activeSheet = $spreadsheet->getActiveSheet()->setTitle('Opname ' . $opname['opname_type']);

            $activeSheet->setCellValue('A1', 'Opname Type');
            $activeSheet->setCellValue('B1', $opname['opname_type']);
            $activeSheet->setCellValue('A2', 'Opname Date');
            $activeSheet->setCellValue('B2', format_date($opname['opname_date']));
            $activeSheet->setCellValue('A3', 'Location Accuracy');
            $activeSheet->setCellValue('B3', $opname['location_accuracy'] . '%');
            $activeSheet->setCellValue('A4', 'Quantity Accuracy');
            $activeSheet->setCellValue('B4', $opname['quantity_accuracy'] . '%');

            $rowPointer = 5;
            $columnPointer = 1;

            if ($opname['opname_type'] == 'GOODS') {
                $headers = [
                    'No', 'No Pallet', 'Customer Name', 'No Reference', 'No Goods', 'Goods name', 'Ex No Container',
                    'Unit', 'Position', 'Quantity', 'Position Check', 'Quantity Check', 'Description Check'
                ];
                foreach ($headers as $index => $headerValue) {
                    $activeSheet->setCellValueByColumnAndRow($index + 1, $rowPointer, $headerValue);
                }
                $rowPointer++;
                foreach ($data as $index => $datum) {
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $index + 1);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['no_pallet']);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['name']);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['no_reference']);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['no_goods']);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['name_goods']);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['ex_no_container']);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['unit']);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['position']);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['quantity']);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['position_check']);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['quantity_check']);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['description_check']);

                    $this->setDiscrepancyCellColor($spreadsheet, $datum, 11, $index + $rowPointer);
                    $columnPointer = 1;
                }
                $activeSheet->setCellValueByColumnAndRow(9, count($data) + $rowPointer, 'Total Stock');
                $activeSheet->setCellValueByColumnAndRow(10, count($data) + $rowPointer, array_sum(array_column($data, 'quantity')));
                $activeSheet->setCellValueByColumnAndRow(11, count($data) + $rowPointer, 'Total Check');
                $activeSheet->setCellValueByColumnAndRow(12, count($data) + $rowPointer, array_sum(array_column($data, 'quantity_check')));

                $spreadsheet->getActiveSheet()
                    ->getStyleByColumnAndRow(9, count($data) + $rowPointer, 12, count($data) + $rowPointer)
                    ->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFC000']],
                        'font' => ['bold' => true]
                    ]);
            } else {
                $headers = ['No', 'Customer Name', 'No Reference', 'No Container', 'Seal', 'Position', 'Quantity', 'Position Check', 'Quantity Check', 'Description Check'];
                foreach ($headers as $index => $headerValue) {
                    $activeSheet->setCellValueByColumnAndRow($index + 1, $rowPointer, $headerValue);
                }
                $rowPointer++;
                foreach ($data as $index => $datum) {
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $index + 1);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['name']);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['no_reference']);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['no_container']);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['seal']);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['position']);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['quantity']);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['position_check']);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['quantity_check']);
                    $activeSheet->setCellValueByColumnAndRow($columnPointer++, $index + $rowPointer, $datum['description_check']);

                    $this->setDiscrepancyCellColor($spreadsheet, $datum, 8, $index + $rowPointer);
                    $columnPointer = 1;
                }
                $activeSheet->setCellValueByColumnAndRow(6, count($data) + $rowPointer, 'Total Stock');
                $activeSheet->setCellValueByColumnAndRow(7, count($data) + $rowPointer, array_sum(array_column($data, 'quantity')));
                $activeSheet->setCellValueByColumnAndRow(8, count($data) + $rowPointer, 'Total Check');
                $activeSheet->setCellValueByColumnAndRow(9, count($data) + $rowPointer, array_sum(array_column($data, 'quantity_check')));

                $spreadsheet->getActiveSheet()
                    ->getStyleByColumnAndRow(6, count($data) + $rowPointer, 9, count($data) + $rowPointer)
                    ->applyFromArray([
                        'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => 'FFC000']],
                        'font' => ['bold' => true]
                    ]);
            }

            $rowPointer = 5;
            $columnIterator = $spreadsheet->getActiveSheet()->getColumnIterator();
            foreach ($columnIterator as $column) {
                $spreadsheet->getActiveSheet()
                    ->getColumnDimension($column->getColumnIndex())
                    ->setAutoSize(true);

                $spreadsheet->getActiveSheet()
                    ->getStyle($column->getColumnIndex() . $rowPointer)
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

            if (!empty($data)) {
                $activeSheet->setAutoFilterByColumnAndRow(1, $rowPointer, count($headers), $rowPointer);
            }

            $excelWriter = new Xlsx($spreadsheet);
            header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
            header('Content-Disposition: attachment;filename="Opname.xlsx"');
            $excelWriter->save('php://output');

        } catch (\PhpOffice\PhpSpreadsheet\Exception $e) {
            return $e->getMessage();
        }

        return false;
    }

    private function setDiscrepancyCellColor($spreadsheet, $datum, $startCol, $row)
    {
        $positionColor = $datum['position'] != $datum['position_check'] ? 'C00000' : '00B050';
        $quantityColor = $datum['quantity'] <> $datum['quantity_check'] ? 'C00000' : '00B050';

        // color position cell
        $spreadsheet->getActiveSheet()
            ->getStyleByColumnAndRow($startCol, $row)
            ->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $positionColor]],
                    'font' => ['color' => ['rgb' => 'FFFFFF']]
                ]
            );

        // color quantity cell
        $spreadsheet->getActiveSheet()
            ->getStyleByColumnAndRow($startCol + 1, $row)
            ->applyFromArray([
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'color' => ['rgb' => $quantityColor]],
                    'font' => ['color' => ['rgb' => 'FFFFFF']]
                ]
            );
    }
}