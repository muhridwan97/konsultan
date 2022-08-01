<?php

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Worksheet\Drawing;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

defined('BASEPATH') OR exit('No direct script access allowed');

class BookingNewsModel extends CI_Model
{
    private $table = 'booking_news';

    /**
     * BookingNewsModel constructor.
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Get active record query builder for all related branch data selection.
     * @return CI_DB_query_builder
     */
    public function getBaseBookingNewsQuery()
    {
        $branchId = get_active_branch('id');

        $bookingNews = $this->db->select([
            'booking_news.*',
            'IFNULL(total_booking, 0) AS total_booking',
            'prv_users.name AS creator_name'
        ])
            ->from('booking_news')
            ->join('(
                    SELECT id_booking_news, COUNT(id) AS total_booking 
                    FROM booking_news_details 
                    GROUP BY id_booking_news
                ) AS booking_news_details', 'booking_news_details.id_booking_news = booking_news.id', 'left')
            ->join(UserModel::$tableUser, 'prv_users.id = booking_news.created_by', 'left')
            ->order_by('booking_news.created_at', 'DESC');

        if (!empty($branchId)) {
            $bookingNews->where('booking_news.id_branch', $branchId);
        }

        return $bookingNews;
    }

    /**
     * Get all booking news with or without deleted records.
     * @param bool $withTrashed
     * @return array
     */
    public function getAllBookingNews($withTrashed = false)
    {
        $bookingNews = $this->getBaseBookingNewsQuery();

        if (!$withTrashed) {
            $bookingNews->where('booking_news.is_deleted', false);
        }

        return $bookingNews->get()->result_array();
    }

    /**
     * Get single booking news data by id with or without deleted record.
     * @param integer $id
     * @param bool $withTrashed
     * @return array
     */
    public function getBookingNewsById($id, $withTrashed = false)
    {
        $bookingNews = $this->getBaseBookingNewsQuery()->where('booking_news.id', $id);

        if (!$withTrashed) {
            $bookingNews->where('booking_news.is_deleted', false);
        }

        return $bookingNews->get()->row_array();
    }

    /**
     * Create new booking news.
     * @param $data
     * @return bool
     */
    public function createBookingNews($data)
    {
        return $this->db->insert($this->table, $data);
    }

    /**
     * Update booking news.
     * @param array $data
     * @param int $id
     * @return bool
     */
    public function updateBookingNews($data, $id)
    {
        return $this->db->update($this->table, $data, ['id' => $id]);
    }

    /**
     * Delete booking news data.
     * @param integer $id
     * @param bool $softDelete
     * @return bool|mixed
     */
    public function deleteBookingNews($id, $softDelete = true)
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

    /**
     * Export report to excel.
     *
     * @param $data
     * @throws \PhpOffice\PhpSpreadsheet\Writer\Exception
     * @throws \PhpOffice\PhpSpreadsheet\Exception
     */
    public function exportExcel($data)
    {
        $bookingNews = $data['bookingNews'];
        $bookingNewsDetails = $data['bookingNewsDetails'];
        $noBA = "BA-{$bookingNews['no_booking_news']}/TI/MKT/" . roman_number(format_date($bookingNews['booking_news_date'], 'm')) . "/" . format_date($bookingNews['booking_news_date'], 'Y');

        $day = [
            'Sunday' => 'Minggu',
            'Monday' => 'Senin',
            'Tuesday' => 'Selasa',
            'Wednesday' => 'Rabu',
            'Thursday' => 'Kamis',
            'Friday' => 'Jum\'at',
            'Saturday' => 'Sabtu',
        ];
        $dayOrg = format_date($bookingNews['booking_news_date'], 'l');
        $dayName = key_exists($dayOrg, $day) ? $day[$dayOrg] : $dayOrg;

        $spreadsheet = new Spreadsheet();
        $spreadsheet->getProperties()
            ->setCreator($this->config->item('app_name'))
            ->setLastModifiedBy($this->config->item('app_name'))
            ->setTitle($data['title'])
            ->setSubject('Data export: ' . $data['title'])
            ->setDescription('Data export generated by: ' . $this->config->item('app_name'));

        $excelWriter = new Xlsx($spreadsheet);

        $activeSheet = $spreadsheet->getActiveSheet();

        $drawing = new Drawing();
        $drawing->setName('Header Image');
        $drawing->setDescription('Header Image Content');
        $drawing->setPath(FCPATH . "assets/app/img/layout/header-tci-iso-aeo.png");
        $drawing->setCoordinates('C1');
        $drawing->setHeight(100);
        $drawing->setWorksheet($activeSheet);

        $spreadsheet
            ->getActiveSheet()
            ->getStyle('A6:M6')
            ->applyFromArray([
                    'borders' => [
                        'top' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]
            );

        if($bookingNews['type'] == 'CANCELING') {
            $activeSheet->setCellValue('C7', "BERITA ACARA TIDAK DAPAT DIPINDAHKAN TERHADAP BARANG YANG DINYATAKAN TIDAK DIKUASAI");
        } else {
            $activeSheet->setCellValue('C7', "BERITA ACARA PEMINDAHAN BARANG YANG DINYATAKAN TIDAK DIKUASAI");
        }
        $activeSheet->setCellValue('C8', "Nomor : {$noBA}");

        $activeSheet->mergeCells('C7:K7');
        $activeSheet->mergeCells('C8:K8');

        $activeSheet
            ->getStyle('C7:K8')
            ->applyFromArray([
                    'font' => [
                        'bold' => true,
                        'size'  => 13,
                    ]
                ]
            )
            ->getAlignment()
            ->setHorizontal('center')
            ->setVertical('center');

        $activeSheet->mergeCells('A10:M10');
        $spreadsheet->getActiveSheet()->getRowDimension('10')->setRowHeight(70);

        $textContent = "Pada hari {$dayName} Tanggal " . format_date($bookingNews['booking_news_date'], 'd F Y') . ",";
        if ($bookingNews['type'] == 'CANCELING') {
            $textContent .= "Kami Sampaikan Pembatalan Realisasi Pemindahan Barang Yang Dinyatakan Tidak Dikuasai Yang berasal dari Tempat Penimbunan Sementara Lap. {$bookingNews['tps']} ke Tempat Penimbunan Pabean PT. Transcon Indonesia Yang Beralamat Di Jl. Denpasar Blok II no 1 dan 16 KBN Marunda, Cilincing, Jakarta Utara. Dengan Data Sebagai Berikut:";
        } else {
            $textContent .= "dilaksanakan Penarikan Barang yang tidak dikuasai dari lokasi Tempat Penimbunan Sementara Lap. TPS {$bookingNews['tps']} ke Tempat Penimbunan Pabean PT. Transcon Indonesia yang beralamat di Jln. Denpasar Blok II No 1 dan 16 KBN Marunda, Cilincing, Jakarta Utara. Sesuai Surat Pemberitahuan Kepala Seksi Tempat Penimbunan Nomor : {$bookingNews['no_sprint']} tanggal " . format_date($bookingNews['sprint_date'], 'd F Y') . ", tentang Perintah Pemindahan Barang yang dinyatakan tidak Dikuasai, sebelum pemindahan akan dilakukan pengecekan dengan data sebagai berikut:";
        }

        $activeSheet->setCellValue('A10', $textContent);
        $spreadsheet->getActiveSheet()
            ->getStyle('A10')
            ->getAlignment()
            ->setWrapText(true)
            ->setVertical('center')
            ->setHorizontal('left');

        $tableColumn = 1;
        $tableRow = 12;
        $activeSheet->setCellValueByColumnAndRow($tableColumn++, $tableRow, "No");
        if ($bookingNews['type'] == 'CANCELING') {
            $activeSheet->setCellValueByColumnAndRow($tableColumn++, $tableRow, "S-Print Pemindahan / Tgl");
        }
        $activeSheet->setCellValueByColumnAndRow($tableColumn++, $tableRow, "BCF 1.5");
        $activeSheet->setCellValueByColumnAndRow($tableColumn++, $tableRow, "No Container");
        $activeSheet->setCellValueByColumnAndRow($tableColumn++, $tableRow, "Size");
        $activeSheet->setCellValueByColumnAndRow($tableColumn++, $tableRow, "Ex. Vessel");
        if ($bookingNews['type'] == 'WITHDRAWAL') {
            $activeSheet->setCellValueByColumnAndRow($tableColumn++, $tableRow, "Seal");
        }
        $activeSheet->setCellValueByColumnAndRow($tableColumn, $tableRow, "DOG");
        $activeSheet->mergeCellsByColumnAndRow($tableColumn, $tableRow, $tableColumn + 2, $tableRow);
        $tableColumn += 3;
        $activeSheet->setCellValueByColumnAndRow($tableColumn++, $tableRow, "Condition");
        $activeSheet->setCellValueByColumnAndRow($tableColumn++, $tableRow, "Consignee");
        $activeSheet->setCellValueByColumnAndRow($tableColumn, $tableRow, "Description");
        $activeSheet->mergeCellsByColumnAndRow($tableColumn, $tableRow, $tableColumn + 1, $tableRow);
        $tableColumn++;

        $lastNoBooking = '';
        $lastConsignee = '';
        $rowStart = 13;
        $rowStartConsignee = 13;
        $no = 1;
        foreach ($bookingNewsDetails as $bookingNewsDetail) {
            $tableRow++;
            $tableColumn = 1;
            $activeSheet->setCellValueByColumnAndRow($tableColumn++, $tableRow, $no++);
            if ($bookingNews['type'] == 'CANCELING') {
                $activeSheet->setCellValueByColumnAndRow($tableColumn, $tableRow, $bookingNews['no_sprint'] . "\n" . format_date($bookingNews['sprint_date'], 'd F Y'));
                $activeSheet->getStyleByColumnAndRow($tableColumn++, $tableRow)->getAlignment()->setWrapText(true);
            }
            $activeSheet->setCellValueByColumnAndRow($tableColumn, $tableRow, $bookingNewsDetail['no_reference'] . "\n" . format_date($bookingNewsDetail['reference_date'], 'd F Y'));
            $activeSheet->getStyleByColumnAndRow($tableColumn++, $tableRow)->getAlignment()->setWrapText(true);
            $activeSheet->setCellValueByColumnAndRow($tableColumn++, $tableRow, $bookingNewsDetail['no_container']);
            $activeSheet->setCellValueByColumnAndRow($tableColumn++, $tableRow, $bookingNewsDetail['size']);
            $activeSheet->setCellValueByColumnAndRow($tableColumn++, $tableRow, $bookingNewsDetail['vessel']);
            if ($bookingNews['type'] == 'WITHDRAWAL') {
                $activeSheet->setCellValueByColumnAndRow($tableColumn++, $tableRow, $bookingNewsDetail['seal']);
            }
            $activeSheet->setCellValueByColumnAndRow($tableColumn, $tableRow, $bookingNewsDetail['description']);
            $activeSheet->mergeCellsByColumnAndRow($tableColumn, $tableRow, $tableColumn + 2, $tableRow);
            $tableColumn += 3;
            $activeSheet->setCellValueByColumnAndRow($tableColumn++, $tableRow, $bookingNewsDetail['condition']);
            $activeSheet->setCellValueByColumnAndRow($tableColumn++, $tableRow, $bookingNewsDetail['customer_name']);
            $activeSheet->setCellValueByColumnAndRow($tableColumn, $tableRow, $bookingNewsDetail['description']);
            $activeSheet->mergeCellsByColumnAndRow($tableColumn, $tableRow, $tableColumn + 1, $tableRow);
            $tableColumn++;

            if ($lastNoBooking != $bookingNewsDetail['no_reference'] && $tableRow - 1 > $rowStart) {
                $columnMerge = $bookingNews['type'] == 'CANCELING' ? 3 : 2;
                $activeSheet->mergeCellsByColumnAndRow($columnMerge, $rowStart, $columnMerge, $tableRow - 1);
                $rowStart = $tableRow;
                $lastNoBooking = $bookingNewsDetail['no_reference'];
            }

            if ($lastConsignee != $bookingNewsDetail['customer_name'] && $tableRow - 1 > $rowStartConsignee) {
                $columnMerge = 11;
                $activeSheet->mergeCellsByColumnAndRow($columnMerge, $rowStartConsignee, $columnMerge, $tableRow - 1);
                $rowStartConsignee = $tableRow;
                $lastConsignee = $bookingNewsDetail['customer_name'];
            }
        }

        if ($bookingNews['type'] == 'CANCELING') {
            $activeSheet->mergeCellsByColumnAndRow(2, 13, 2 , 13 + count($bookingNewsDetails) - 1);
        }

        $spreadsheet->getActiveSheet()->getColumnDimension('A')->setWidth(6);
        if ($bookingNews['type'] == 'CANCELING') {
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(28);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(16);
            $spreadsheet->getActiveSheet()->getColumnDimension('D')->setWidth(13);
        } else {
            $spreadsheet->getActiveSheet()->getColumnDimension('B')->setWidth(16);
            $spreadsheet->getActiveSheet()->getColumnDimension('C')->setWidth(13);
        }
        $spreadsheet->getActiveSheet()->getColumnDimension($bookingNews['type'] == 'CANCELING' ? 'E' : 'D')->setWidth(6);
        $spreadsheet->getActiveSheet()->getColumnDimension($bookingNews['type'] == 'CANCELING' ? 'F' : 'E')->setWidth(15);
        $spreadsheet->getActiveSheet()->getColumnDimension('F')->setWidth(13);
        $spreadsheet->getActiveSheet()->getColumnDimension('G')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('I')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('J')->setWidth(10);
        $spreadsheet->getActiveSheet()->getColumnDimension('K')->setWidth(20);

        // add table border and set vertical to middle
        $spreadsheet
            ->getActiveSheet()
            ->getStyleByColumnAndRow(1, 12, $tableColumn, $tableRow)
            ->applyFromArray([
                    'borders' => [
                        'allBorders' => [
                            'borderStyle' => Border::BORDER_THIN,
                            'color' => ['rgb' => '000000'],
                        ],
                    ],
                ]
            )
            ->getAlignment()
            ->setVertical('center');

        // no alignment
        $spreadsheet->getActiveSheet()
            ->getStyleByColumnAndRow(1, 12, 1, $tableRow)
            ->getAlignment()
            ->setHorizontal('center');

        // container size alignment
        $spreadsheet->getActiveSheet()
            ->getStyleByColumnAndRow($bookingNews['type'] == 'CANCELING' ? 5 : 4, 12, $bookingNews['type'] == 'CANCELING' ? 5 : 4, $tableRow)
            ->getAlignment()
            ->setHorizontal('center');

        // condition alignment
        $spreadsheet->getActiveSheet()
            ->getStyleByColumnAndRow(10, 12, 10, $tableRow)
            ->getAlignment()
            ->setHorizontal('center');

        $tableRow += 2;

        $activeSheet->setCellValueByColumnAndRow(1, $tableRow++, "Demikian Berita Acara Nomor: {$noBA} ini dibuat dengan sebenar-benarnya untuk dapat dipertanggung jawabkan di kemudian hari.");

        $activeSheet->setCellValueByColumnAndRow(1, $tableRow + 1, "Mengetahui:");
        $activeSheet->setCellValueByColumnAndRow(1, $tableRow + 2, "Kepala Seksi Penimbunan")
            ->getStyleByColumnAndRow(1, $tableRow + 2)->getFont()->setBold(true);
        $activeSheet->setCellValueByColumnAndRow(1, $tableRow + 6, $bookingNews['chief_name'])
            ->getStyleByColumnAndRow(1, $tableRow + 6)->getFont()->setBold(true);
        $activeSheet->setCellValueByColumnAndRow(1, $tableRow + 7, "NIP. {$bookingNews['chief_nip']}");

        $activeSheet->setCellValueByColumnAndRow(7, $tableRow + 1, "Disaksikan Oleh:");
        $activeSheet->setCellValueByColumnAndRow(7, $tableRow + 2, "Hanggar TPS")
            ->getStyleByColumnAndRow(7, $tableRow + 2)->getFont()->setBold(true);
        $activeSheet->setCellValueByColumnAndRow(7, $tableRow + 5, "NIP. ");

        $activeSheet->setCellValueByColumnAndRow(10, $tableRow + 2, "Hanggar TPP")
            ->getStyleByColumnAndRow(10, $tableRow + 2)->getFont()->setBold(true);
        $activeSheet->setCellValueByColumnAndRow(10, $tableRow + 5, "NIP. ");

        $tableRow = $tableRow + 5;
        if ($bookingNews['type'] == 'WITHDRAWAL') {
            $activeSheet->setCellValueByColumnAndRow(7, $tableRow + 2, "Petugas Pintu TPS")
                ->getStyleByColumnAndRow(7, $tableRow + 2)->getFont()->setBold(true);
            $activeSheet->setCellValueByColumnAndRow(7, $tableRow + 5, "NIP. ");

            $activeSheet->setCellValueByColumnAndRow(10, $tableRow + 2, "Petugas Pintu TPP")
                ->getStyleByColumnAndRow(10, $tableRow + 2)->getFont()->setBold(true);
            $activeSheet->setCellValueByColumnAndRow(10, $tableRow + 5, "NIP. ");

            $tableRow = $tableRow + 5;
        }

        $activeSheet->setCellValueByColumnAndRow(7, $tableRow + 2, "Pengusaha TPS")
            ->getStyleByColumnAndRow(7, $tableRow + 2)->getFont()->setBold(true);
        $activeSheet->setCellValueByColumnAndRow(7, $tableRow + 5, "NIP. ");

        $activeSheet->setCellValueByColumnAndRow(10, $tableRow + 2, "Pengusaha TPP")
            ->getStyleByColumnAndRow(10, $tableRow + 2)->getFont()->setBold(true);
        $activeSheet->setCellValueByColumnAndRow(10, $tableRow + 5, "NIP. ");

        $this->load->helper('download');
        $storeTo = './uploads/temp/' . $data['title'] . '-' . uniqid() . '.xlsx';
        $excelWriter->save($storeTo);
        force_download($storeTo, null, true);
    }

}