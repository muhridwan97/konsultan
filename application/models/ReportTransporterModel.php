<?php

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') OR exit('No direct script access allowed');

class ReportTransporterModel extends MY_Model
{
    /**
     * Get transporter report.
     *
     * @param array $filters
     * @return array|int
     */
    public function getReportTransporter($filters = [])
    {
        // first we find out if safe conduct related with job and booking (branch) and some filters
        // remember in "safe conduct group" we "count no police ONCE" not twice or multiple
        $baseQuery = $this->db
            ->select([
                'COUNT(safe_conducts.id) AS total_safe_conduct',
                'MAX(safe_conducts.created_at) AS created_at', // in safe conduct group we may have different date creation, we count as single entity, just pick any
                'safe_conducts.no_police',
                'ref_branches.id_branch_vms',
            ])
            ->from('safe_conducts')
            ->join("ref_vehicles", 'ref_vehicles.no_plate = safe_conducts.no_police', 'left')
            ->join('bookings', 'bookings.id = safe_conducts.id_booking', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('work_orders', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('handlings', 'work_orders.id_handling = handlings.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->group_start()
            ->where_in('ref_handling_types.multiplier_goods', ['1', '-1'])
            ->or_where_in('ref_handling_types.multiplier_container', ['1', '-1'])
            ->group_end()
            ->where('expedition_type', "INTERNAL")
            ->where('safe_conducts.is_deleted', false)
            ->where('ref_vehicles.is_deleted', false)
            ->group_by('IFNULL(safe_conducts.id_safe_conduct_group, safe_conducts.id), no_police');

        if (key_exists('vehicle', $filters) && !empty($filters['vehicle'])) {
            $baseQuery->where_in('ref_vehicles.no_plate', $filters['vehicle']);
        }

        if (key_exists('status', $filters) && !empty($filters['status'])) {
            $baseQuery->where_in('ref_vehicles.status', $filters['status']);
        }

        if (key_exists('vehicle_type', $filters) && !empty($filters['vehicle_type'])) {
            $baseQuery->where_in('ref_vehicles.vehicle_type', $filters['vehicle_type']);
        }

        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            $branchData = get_if_exist($filters, 'branch_data', 'VEHICLE');
            if ($branchData == 'TRANSACTION') {
                $baseQuery->where_in('bookings.id_branch', $filters['branch']);
            } else {
                $baseQuery->where_in('ref_vehicles.id_branch', $filters['branch']);
            }
        }

        // in safe conduct group it should in the same branch so pick any
        if (key_exists('branch_data', $filters) && !empty($filters['branch_data'])) {
            if ($filters['branch_data'] == 'TRANSACTION') {
                $baseQuery->select('bookings.id_branch')->group_by('bookings.id_branch');
            } else {
                $baseQuery->select('ref_vehicles.id_branch')->group_by('ref_vehicles.id_branch');
            }
        } else {
            $baseQuery->select('ref_vehicles.id_branch')->group_by('ref_vehicles.id_branch');
        }

        if (key_exists('activity_type', $filters) && !empty($filters['activity_type'])) {
            $baseQuery->where_in('ref_booking_types.category', $filters['activity_type']);
        }

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $baseQuery->where('DATE(' . get_if_exist($filters, 'date_type', 'safe_conducts.created_at') . ')>=', format_date($filters['date_from']));
        }

        if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
            $baseQuery->where('DATE(' . get_if_exist($filters, 'date_type', 'safe_conducts.created_at') . ')<=', format_date($filters['date_to']));
        }

        $baseQuerySafeConduct = $baseQuery->get_compiled_select();

        $baseQuery = $this->db->select([
            'YEAR(safe_conducts.created_at) AS year',
            'WEEK(safe_conducts.created_at, 1) AS week', // we add +1 to PHP api compatibility (PHP date week start from 1)
            'DATE_ADD(DATE(safe_conducts.created_at), INTERVAL - WEEKDAY(safe_conducts.created_at) - 1 day) AS week_start_date',
            'DATE_ADD(DATE_ADD(DATE(safe_conducts.created_at), INTERVAL - WEEKDAY(safe_conducts.created_at) - 1 day), INTERVAL 6 DAY) AS week_until_date',
            'safe_conducts.id_branch', // either vehicle branch of booking branch (transaction)
            'ref_vehicles.id AS id_vehicle',
            'ref_vehicles.vehicle_name',
            'ref_vehicles.vehicle_type',
            'ref_vehicles.no_plate',
            'ref_vehicles.status',
            'COUNT(safe_conducts.no_police) AS total_safe_conduct',
            'safe_conducts.id_branch_vms'
        ])
            ->from('ref_vehicles')
            ->join("({$baseQuerySafeConduct}) AS safe_conducts", 'safe_conducts.no_police = ref_vehicles.no_plate')
            ->where('ref_vehicles.is_deleted', false)
            ->group_by('YEAR(safe_conducts.created_at), WEEK(safe_conducts.created_at, 1), week_start_date, week_until_date, ref_vehicles.id, safe_conducts.id_branch');

        if (key_exists('week', $filters) && !empty($filters['week'])) {
            $baseQuery->having('week', $filters['week']);
        }

        if (key_exists('year', $filters) && !empty($filters['year'])) {
            $baseQuery->having('year', $filters['year']);
        } else {
            $baseQuery->having('year = YEAR(CURDATE())'); // use current year
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get transporter report.
     *
     * @param array $filters
     * @return array|int
     */
    public function getReportTransporterExternal($filters = [])
    {
        // first we find out if safe conduct related with job and booking (branch) and some filters
        // remember in "safe conduct group" we "count no police ONCE" not twice or multiple
        $baseQuery = $this->db
            ->select([
                'COUNT(safe_conducts.id) AS total_safe_conduct',
                'MAX(safe_conducts.created_at) AS created_at', // in safe conduct group we may have different date creation, we count as single entity, just pick any
                'safe_conducts.no_police',
                'ref_branches.id_branch_vms',
                'safe_conducts.id_transporter_entry_permit',
            ])
            ->from('safe_conducts')
            ->join('bookings', 'bookings.id = safe_conducts.id_booking', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('work_orders', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('handlings', 'work_orders.id_handling = handlings.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->group_start()
            ->where_in('ref_handling_types.multiplier_goods', ['1', '-1'])
            ->or_where_in('ref_handling_types.multiplier_container', ['1', '-1'])
            ->group_end()
            ->where('expedition_type', "EXTERNAL")
            ->where('safe_conducts.is_deleted', false)
            ->group_by('IFNULL(safe_conducts.id_transporter_entry_permit, safe_conducts.id), no_police');


        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            $baseQuery->where_in('bookings.id_branch', $filters['branch']);
        }

        // in safe conduct group it should in the same branch so pick any
        if (key_exists('branch_data', $filters) && !empty($filters['branch_data'])) {
            $baseQuery->select('bookings.id_branch')->group_by('bookings.id_branch');
        } else {
            $baseQuery->select('bookings.id_branch')->group_by('bookings.id_branch');
        }

        if (key_exists('activity_type', $filters) && !empty($filters['activity_type'])) {
            $baseQuery->where_in('ref_booking_types.category', $filters['activity_type']);
        }

        if (key_exists('date_from', $filters) && !empty($filters['date_from'])) {
            $baseQuery->where('DATE(' . get_if_exist($filters, 'date_type', 'safe_conducts.created_at') . ')>=', format_date($filters['date_from']));
        }

        if (key_exists('date_to', $filters) && !empty($filters['date_to'])) {
            $baseQuery->where('DATE(' . get_if_exist($filters, 'date_type', 'safe_conducts.created_at') . ')<=', format_date($filters['date_to']));
        }

        $baseQuerySafeConduct = $baseQuery->get_compiled_select();
        
        $baseQuery = $this->db->select([
            'YEAR(safe_conducts.created_at) AS year',
            'WEEK(safe_conducts.created_at, 1) AS week', // we add +1 to PHP api compatibility (PHP date week start from 1)
            'DATE_ADD(DATE(safe_conducts.created_at), INTERVAL - WEEKDAY(safe_conducts.created_at) - 1 day) AS week_start_date',
            'DATE_ADD(DATE_ADD(DATE(safe_conducts.created_at), INTERVAL - WEEKDAY(safe_conducts.created_at) - 1 day), INTERVAL 6 DAY) AS week_until_date',
            'safe_conducts.id_branch', // either vehicle branch of booking branch (transaction)
            'transporter_entry_permits.id AS id_tep',
            'transporter_entry_permits.receiver_vehicle',
            'transporter_entry_permits.receiver_no_police',
            'COUNT(safe_conducts.no_police) AS total_safe_conduct',
            'safe_conducts.id_branch_vms'
        ])
            ->from('transporter_entry_permits')
            ->join("({$baseQuerySafeConduct}) AS safe_conducts", 'safe_conducts.id_transporter_entry_permit = transporter_entry_permits.id')
            ->group_by('YEAR(safe_conducts.created_at), WEEK(safe_conducts.created_at, 1), week_start_date, week_until_date, transporter_entry_permits.id, safe_conducts.id_branch');

        if (key_exists('week', $filters) && !empty($filters['week'])) {
            $baseQuery->having('week', $filters['week']);
        }

        if (key_exists('year', $filters) && !empty($filters['year'])) {
            $baseQuery->having('year', $filters['year']);
        } else {
            $baseQuery->having('year = YEAR(CURDATE())'); // use current year
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get safe conduct detail by transporter.
     *
     * @param array $filters
     * @return array
     */
    public function getReportTransporterDetail($filters = [])
    {
        $baseQuery = $this->db
            ->select([
                'safe_conducts.*',
                'ref_vehicles.id AS id_vehicle',
                'ref_vehicles.vehicle_name',
                'ref_vehicles.vehicle_type',
                'ref_vehicles.no_plate',
                'ref_vehicles.status',
                'work_orders.id AS id_work_order',
                'work_orders.no_work_order',
                'bookings.no_reference',
                'ref_people.name AS customer_name',
                'ref_branches.branch',
                'ref_branches.id AS id_branch',
            ])
            ->from('safe_conducts')
            ->join("ref_vehicles", 'ref_vehicles.no_plate = safe_conducts.no_police', 'left')
            ->join('bookings', 'bookings.id = safe_conducts.id_booking', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->join('ref_people', 'ref_people.id = bookings.id_customer', 'left')
            ->join('work_orders', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('handlings', 'work_orders.id_handling = handlings.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->group_start()
            ->where_in('ref_handling_types.multiplier_goods', ['1', '-1'])
            ->or_where_in('ref_handling_types.multiplier_container', ['1', '-1'])
            ->group_end()
            ->where('expedition_type', "INTERNAL")
            ->where('safe_conducts.is_deleted', false)
            ->where('ref_vehicles.is_deleted', false)
            ->order_by('safe_conducts.id_safe_conduct_group', 'desc')
            ->order_by('safe_conducts.created_at', 'desc');

        if (key_exists('vehicle', $filters) && !empty($filters['vehicle'])) {
            $baseQuery->where_in('ref_vehicles.no_plate', $filters['vehicle']);
        }

        if (key_exists('week', $filters) && !empty($filters['week'])) {
            $baseQuery->where_in('WEEK(safe_conducts.created_at, 1)', $filters['week']);
        }

        if (key_exists('year', $filters) && !empty($filters['year'])) {
            $baseQuery->where('YEAR(safe_conducts.created_at)', $filters['year']);
        } else {
            $baseQuery->where('YEAR(safe_conducts.created_at)', date('Y'));
        }

        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            $branchData = get_if_exist($filters, 'branch_data', 'VEHICLE');
            if ($branchData == 'TRANSACTION') {
                $baseQuery->where_in('bookings.id_branch', $filters['branch']);
            } else {
                $baseQuery
                    ->join('ref_branches AS vehicle_branches', 'vehicle_branches.id = ref_vehicles.id_branch', 'left')
                    ->where_in('vehicle_branches.id', $filters['branch']);
            }
        }

        if (key_exists('activity_type', $filters) && !empty($filters['activity_type'])) {
            $baseQuery->where_in('ref_booking_types.category', $filters['activity_type']);
        }

        return $baseQuery->get()->result_array();
    }

    /**
     * Get safe conduct detail by transporter.
     *
     * @param array $filters
     * @return array
     */
    public function getReportTransporterExternalDetail($filters = [])
    {
        $baseQuery = $this->db
            ->select([
                'safe_conducts.*',
                'transporter_entry_permits.id AS id_tep',
                'transporter_entry_permits.receiver_vehicle AS vehicle_name',
                'transporter_entry_permits.receiver_no_police AS no_plate',
                'work_orders.id AS id_work_order',
                'work_orders.no_work_order',
                'bookings.no_reference',
                'ref_people.name AS customer_name',
                'ref_branches.branch',
                'ref_branches.id AS id_branch',
            ])
            ->from('safe_conducts')
            ->join("transporter_entry_permits", 'transporter_entry_permits.id = safe_conducts.id_transporter_entry_permit', 'left')
            ->join('bookings', 'bookings.id = safe_conducts.id_booking', 'left')
            ->join('ref_booking_types', 'ref_booking_types.id = bookings.id_booking_type', 'left')
            ->join('ref_branches', 'ref_branches.id = bookings.id_branch', 'left')
            ->join('ref_people', 'ref_people.id = bookings.id_customer', 'left')
            ->join('work_orders', 'safe_conducts.id = work_orders.id_safe_conduct', 'left')
            ->join('handlings', 'work_orders.id_handling = handlings.id', 'left')
            ->join('ref_handling_types', 'ref_handling_types.id = handlings.id_handling_type', 'left')
            ->group_start()
            ->where_in('ref_handling_types.multiplier_goods', ['1', '-1'])
            ->or_where_in('ref_handling_types.multiplier_container', ['1', '-1'])
            ->group_end()
            ->where('expedition_type', "EXTERNAL")
            ->where('safe_conducts.is_deleted', false)
            ->order_by('safe_conducts.id_safe_conduct_group', 'desc')
            ->order_by('safe_conducts.created_at', 'desc');

        if (key_exists('vehicle', $filters) && !empty($filters['vehicle'])) {
            $baseQuery->where_in('transporter_entry_permits.receiver_no_police', $filters['vehicle']);
        }

        if (key_exists('week', $filters) && !empty($filters['week'])) {
            $baseQuery->where_in('WEEK(safe_conducts.created_at, 1)', $filters['week']);
        }

        if (key_exists('year', $filters) && !empty($filters['year'])) {
            $baseQuery->where('YEAR(safe_conducts.created_at)', $filters['year']);
        } else {
            $baseQuery->where('YEAR(safe_conducts.created_at)', date('Y'));
        }

        if (key_exists('branch', $filters) && !empty($filters['branch'])) {
            $baseQuery->where_in('bookings.id_branch', $filters['branch']);
        }

        if (key_exists('activity_type', $filters) && !empty($filters['activity_type'])) {
            $baseQuery->where_in('ref_booking_types.category', $filters['activity_type']);
        }

        return $baseQuery->get()->result_array();
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
