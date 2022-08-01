<?php

use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Chart\Chart;
use PhpOffice\PhpSpreadsheet\Chart\DataSeries;
use PhpOffice\PhpSpreadsheet\Chart\DataSeriesValues;
use PhpOffice\PhpSpreadsheet\Chart\Layout;
use PhpOffice\PhpSpreadsheet\Chart\Legend;
use PhpOffice\PhpSpreadsheet\Chart\PlotArea;
use PhpOffice\PhpSpreadsheet\Chart\Title;
use PhpOffice\PhpSpreadsheet\Exception;
use PhpOffice\PhpSpreadsheet\IOFactory;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Color;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class ReportTpsModel
 * @property BranchModel $branchModel
 */
class ReportTpsModel extends CI_Model
{
    /**
     * Get report customs daily.
     * @param $filters
     * @return mixed
     */
    public function getReportDeferredTPS($filters = [])
    {
        $branchId = get_if_exist($filters, 'branch', get_active_branch_id());

        $report = $this->db->select([
            'tps.id AS id_tps',
            'tps.name AS tps_name',
            get_ext_field('NO_SP', 'bookings.id', 'no_sprint'),
            get_ext_field('TGL_SP', 'bookings.id', 'sprint_date'),
            'bookings.no_reference',
            '(SELECT no_doc FROM booking_statuses 
                WHERE document_status = "BDN" AND booking_statuses.id_booking = bookings.id
                ORDER BY id DESC LIMIT 1) AS no_bdn',
            '(SELECT no_doc FROM booking_statuses 
                WHERE document_status = "BMN" AND booking_statuses.id_booking = bookings.id
                ORDER BY id DESC LIMIT 1) AS no_bmn',
            'bookings.reference_date',
            'ref_containers.no_container',
            'IF(ref_containers.size = 20, 1, 0) AS container_size_20',
            'IF(ref_containers.size = 40, 1, 0) AS container_size_40',
            'IF(ref_containers.size = 45, 1, 0) AS container_size_45',
            'booking_news.no_booking_news',
            'booking_news.booking_news_date',
            'safe_conducts.tps_gate_out_date',
            'IF(safe_conducts.expedition_type = "INTERNAL", DATE(safe_conducts.security_out_date), DATE(safe_conducts.security_in_date)) AS tpp_gate_in_date',
            'DATE(last_outbound_dates.outbound_date) AS tpp_out_date',
            'bookings.vessel',
            'bookings.voyage',
            'safe_conduct_containers.description AS goods_description',
            'customers.name AS customer_name',
            'IF(last_outbound_dates.outbound_date IS NULL, "TIMBUN", "KELUAR") AS position',
            '"" AS status',
            'last_outbound_dates.outbound_date AS tpp_gate_out_date',
            'IF((SELECT id FROM payments 
                WHERE payment_type = "OB TPS PERFORMA" AND payments.id_booking = bookings.id 
                ORDER BY id DESC LIMIT 1) IS NULL, 0, 1) is_deferred',
            'IF((SELECT id FROM payments 
                WHERE payment_type = "OB TPS PERFORMA" AND payments.id_booking = bookings.id 
                ORDER BY id DESC LIMIT 1) IS NULL, "", "Storage ditangguhkan") AS description',
        ])
            ->from('bookings')
            ->join('ref_people AS customers', 'customers.id = bookings.id_customer')
            ->join('booking_news_details', 'booking_news_details.id_booking = bookings.id', 'left')
            ->join('booking_news', 'booking_news.id = booking_news_details.id_booking_news', 'left')
            ->join('safe_conducts', 'safe_conducts.id_booking = bookings.id')
            ->join('safe_conduct_containers', 'safe_conduct_containers.id_safe_conduct = safe_conducts.id')
            ->join('ref_people AS tps', 'tps.id = safe_conducts.id_source_warehouse')
            ->join('ref_containers', 'ref_containers.id = safe_conduct_containers.id_container')
            ->join("(
                SELECT 
                    IFNULL(bookings.id_booking, bookings.id) AS id_booking,
                    id_container, 
                    MAX(IF(safe_conducts.expedition_type = 'INTERNAL', safe_conducts.security_in_date, safe_conducts.security_out_date)) AS outbound_date 
                FROM bookings
                INNER JOIN safe_conducts ON safe_conducts.id_booking = bookings.id
                INNER JOIN safe_conduct_containers ON safe_conduct_containers.id_safe_conduct = safe_conducts.id
                WHERE safe_conducts.type = 'OUTBOUND'
                GROUP BY IFNULL(bookings.id_booking, bookings.id), id_container
            ) AS last_outbound_dates", 'bookings.id = last_outbound_dates.id_booking
                AND safe_conduct_containers.id_container = last_outbound_dates.id_container', 'left')
            ->where('safe_conducts.type', 'INBOUND');

        if (!empty($branchId)) {
            $report->where('bookings.id_branch', $branchId);
        }

        if (key_exists('tps', $filters) && !empty($filters['tps'])) {
            $report->where('tps.id', $filters['tps']);
        }

        if (key_exists('is_deferred', $filters)) {
            $report->having('is_deferred', $filters['is_deferred']);
        }

        if (!empty(get_if_exist($filters, 'date_from')) && !empty(get_if_exist($filters, 'date_to'))) {
            $report->having('((IFNULL(tps_gate_out_date, tpp_gate_in_date) >= "' . format_date($filters['date_from']) . '"');
            $report->having('IFNULL(tps_gate_out_date, tpp_gate_in_date) <= "' . format_date($filters['date_to']) . '")');

            $report->or_having('(tpp_gate_out_date >= "' . format_date($filters['date_from']) . '"');
            $report->having('tpp_gate_out_date <= "' . format_date($filters['date_to']) . '"))');
        }

        return $report->get()->result_array();
    }

    /**
     * Export report deferred tps to excel.
     *
     * @param $data
     * @param bool $download
     * @param null $storeTo
     * @return string
     */
    public function exportReportDeferredTPS($data, $download = true, $storeTo = null)
    {
        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator($this->config->item('app_name'))
            ->setLastModifiedBy($this->config->item('app_name'))
            ->setTitle('Deferred TPS')
            ->setSubject('Data export: TPS ' . get_if_exist($data['filters'], 'date_from') . ' until ' . get_if_exist($data['filters'], 'date_to'))
            ->setDescription('Data export generated by: ' . $this->config->item('app_name'));

        try {
            $spreadsheet->setActiveSheetIndex(0);
            $spreadsheet->getActiveSheet()->getSheetView()->setZoomScale(80);
            $activeSheet = $spreadsheet->getActiveSheet();

            // set title
            $activeSheet->setCellValue('A1', 'TPP : Transcon Indonesia');
            $activeSheet->setCellValue('A2', 'Alamat : ' . get_active_branch('address'));
            $activeSheet->setCellValue('A3', 'Bulan : ' . format_date(get_if_exist($data['filters'], 'date_from', date('Y-m-d')), 'F'));
            $activeSheet->mergeCells('A1:C1');
            $activeSheet->mergeCells('A2:C2');
            $activeSheet->mergeCells('A3:C3');
            $activeSheet->getStyle('A1:C3')->getFont()->setBold(true);

            $activeSheet->setCellValue('A4', 'Laporan Petikemas BCF 1.5');
            $activeSheet->getStyle('A4')->getFont()->setBold(true);
            $activeSheet->mergeCells('A4:W4');
            $activeSheet->getStyle('A4')
                ->getAlignment()
                ->setHorizontal('center')
                ->setVertical('center');
            $activeSheet->getStyle('A4')
                ->getFont()
                ->setSize(14);

            $activeSheet->setCellValue('A5', 'TPS : ' . get_if_exist($data['tps'], 'name'));
            $activeSheet->mergeCells('A5:C5');
            $activeSheet->getStyle('A5')->getFont()->setBold(true);

            // set table header
            $activeSheet->setCellValue('A6', 'No');
            $activeSheet->mergeCells('A6:A7');
            $activeSheet->setCellValue('B6', 'Surat Perintah');
            $activeSheet->mergeCells('B6:C6');
            $activeSheet->setCellValue('D6', 'Status Barang / Container');
            $activeSheet->mergeCells('D6:G6');
            $activeSheet->setCellValue('H6', 'No Container');
            $activeSheet->mergeCells('H6:H7');
            $activeSheet->setCellValue('I6', 'Ukuran');
            $activeSheet->mergeCells('I6:K6');
            $activeSheet->setCellValue('L6', 'BA  TPP PENARIKAN');
            $activeSheet->mergeCells('L6:M6');
            $activeSheet->setCellValue('N6', 'Gate Out TPS');
            $activeSheet->mergeCells('N6:N7');
            $activeSheet->setCellValue('O6', 'Masuk TPP');
            $activeSheet->mergeCells('O6:O7');
            $activeSheet->setCellValue('P6', 'Keluar TPP');
            $activeSheet->mergeCells('P6:P7');
            $activeSheet->setCellValue('Q6', 'Nama Kapal / Voyage');
            $activeSheet->mergeCells('Q6:Q7');
            $activeSheet->setCellValue('R6', 'Jenis Barang');
            $activeSheet->mergeCells('R6:R7');
            $activeSheet->setCellValue('S6', 'Pemilik');
            $activeSheet->mergeCells('S6:S7');
            $activeSheet->setCellValue('T6', 'Posisi');
            $activeSheet->setCellValue('U6', 'Status');
            $activeSheet->setCellValue('V6', 'Gate Out TPP');
            $activeSheet->mergeCells('V6:V7');
            $activeSheet->setCellValue('W6', 'Keterangan');
            $activeSheet->mergeCells('W6:W7');

            $activeSheet->setCellValue('B7', 'No');
            $activeSheet->setCellValue('C7', 'Tanggal');
            $activeSheet->setCellValue('D7', 'BTD/BCF 1.5');
            $activeSheet->setCellValue('E7', 'No Kep BDN');
            $activeSheet->setCellValue('F7', 'No. Kep BMN');
            $activeSheet->setCellValue('G7', 'Tanggal');
            $activeSheet->setCellValue('I7', '20');
            $activeSheet->setCellValue('J7', '40');
            $activeSheet->setCellValue('K7', '45');
            $activeSheet->setCellValue('L7', 'No');
            $activeSheet->setCellValue('M7', 'Tanggal');
            $activeSheet->setCellValue('T7', 'Timbun/Keluar');
            $activeSheet->setCellValue('U7', 'Lelang/Musnah/Hibah');

            $activeSheet->getStyle('A6:W7')
                ->getAlignment()
                ->setHorizontal('center')
                ->setVertical('center');

            // add table data late summary
            $startRow = 8;
            foreach ($data['reports'] as $index => $report) {
                $activeSheet
                    ->setCellValueByColumnAndRow(1, $startRow + $index, $index + 1)
                    ->setCellValueByColumnAndRow(2, $startRow + $index, if_empty($report['no_sprint'], '-'))
                    ->setCellValueByColumnAndRow(3, $startRow + $index, if_empty($report['sprint_date'], '-'))
                    ->setCellValueByColumnAndRow(4, $startRow + $index, $report['no_reference'])
                    ->setCellValueByColumnAndRow(5, $startRow + $index, if_empty($report['no_bdn'], '-'))
                    ->setCellValueByColumnAndRow(6, $startRow + $index, if_empty($report['no_bmn'], '-'))
                    ->setCellValueByColumnAndRow(7, $startRow + $index, $report['reference_date'])
                    ->setCellValueByColumnAndRow(8, $startRow + $index, $report['no_container'])
                    ->setCellValueByColumnAndRow(9, $startRow + $index, $report['container_size_20'])
                    ->setCellValueByColumnAndRow(10, $startRow + $index, $report['container_size_40'])
                    ->setCellValueByColumnAndRow(11, $startRow + $index, $report['container_size_45'])
                    ->setCellValueByColumnAndRow(12, $startRow + $index, if_empty($report['no_booking_news'], '-'))
                    ->setCellValueByColumnAndRow(13, $startRow + $index, if_empty($report['booking_news_date'], '-'))
                    ->setCellValueByColumnAndRow(14, $startRow + $index, if_empty($report['tps_gate_out_date'], '-'))
                    ->setCellValueByColumnAndRow(15, $startRow + $index, $report['tpp_gate_in_date'])
                    ->setCellValueByColumnAndRow(16, $startRow + $index, if_empty($report['tpp_out_date'], '-'))
                    ->setCellValueByColumnAndRow(17, $startRow + $index, $report['vessel'] . ' ' . $report['voyage'])
                    ->setCellValueByColumnAndRow(18, $startRow + $index, if_empty($report['goods_description'], '-'))
                    ->setCellValueByColumnAndRow(19, $startRow + $index, $report['customer_name'])
                    ->setCellValueByColumnAndRow(20, $startRow + $index, $report['position'])
                    ->setCellValueByColumnAndRow(21, $startRow + $index, if_empty($report['status'], '-'))
                    ->setCellValueByColumnAndRow(22, $startRow + $index, if_empty($report['tpp_gate_out_date'], '-'))
                    ->setCellValueByColumnAndRow(23, $startRow + $index, if_empty($report['description'], '-'));
            }

            $activeSheet->setCellValueByColumnAndRow(1, $startRow + count($data['reports']), 'Total');
            $activeSheet->setCellValueByColumnAndRow(9, $startRow + count($data['reports']), array_sum(array_column($data['reports'], 'container_size_20')));
            $activeSheet->setCellValueByColumnAndRow(10, $startRow + count($data['reports']), array_sum(array_column($data['reports'], 'container_size_40')));
            $activeSheet->setCellValueByColumnAndRow(11, $startRow + count($data['reports']), array_sum(array_column($data['reports'], 'container_size_45')));
            $activeSheet->setCellValueByColumnAndRow(15, $startRow + count($data['reports']), count($data['reports']));
            $activeSheet->setCellValueByColumnAndRow(16, $startRow + count($data['reports']), count(array_filter($data['reports'], function($item) {
                return !empty($item['tpp_out_date']);
            })));
            $activeSheet->mergeCellsByColumnAndRow(1, $startRow + count($data['reports']), 8, $startRow + count($data['reports']));

            // style column number
            $activeSheet->getStyle('A8:A' . (count($data['reports']) + 8))
                ->getAlignment()
                ->setHorizontal('center');

            // style column container count
            $activeSheet->getStyle('I8:K' . (count($data['reports']) + 8))
                ->getAlignment()
                ->setHorizontal('center');

            // style table footer
            $activeSheet->getStyle('A' . (count($data['reports']) + 8) . ':W' . (count($data['reports']) + 8))
                ->getAlignment()
                ->setHorizontal('center');
            $activeSheet->getStyle('A' . (count($data['reports']) + 8) . ':W' . (count($data['reports']) + 8))
                ->getFont()
                ->setBold(true)
                ->setColor(new Color('00FF0000'))
                ->setSize(14);

            // style table data border
            $activeSheet
                ->getStyleByColumnAndRow(1, 6, 23, count($data['reports']) + 8)
                ->applyFromArray([
                        'borders' => array(
                            'allBorders' => array(
                                'borderStyle' => Border::BORDER_THIN,
                                'color' => array('argb' => '202020'),
                            ),
                        ),
                    ]
                );

            // set column width
            $spreadsheet->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('E')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(12);
            $spreadsheet->getActiveSheet()->getColumnDimension('H')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(7);
            $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(7);
            $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(7);
            $spreadsheet->getActiveSheet()->getColumnDimension('L')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('M')->setWidth(15);
            $spreadsheet->getActiveSheet()->getColumnDimension('N')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('O')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('P')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('Q')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('R')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('S')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('T')->setWidth(20);
            $spreadsheet->getActiveSheet()->getColumnDimension('U')->setWidth(25);
            $spreadsheet->getActiveSheet()->getColumnDimension('V')->setAutoSize(true);
            $spreadsheet->getActiveSheet()->getColumnDimension('W')->setAutoSize(true);

            // prepare writer
            $excelWriter = new Xlsx($spreadsheet);
            $fileName = 'Deferred TPS - ' . uniqid() . '.xlsx';
            if ($download) {
                header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
                header('Content-Disposition: attachment;filename="' . $fileName . '"');
                $excelWriter->save('php://output');
            } else {
                if (empty($storeTo)) {
                    $storeTo = './uploads/temp/' . $fileName;
                }
                $excelWriter->save($storeTo);
                return $storeTo;
            }

        } catch (Exception $e) {
            log_message('error', $e->getMessage());
            flash('danger', 'Something went wrong', '_back');
        }
    }
}
