<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Synchronize
 * @property SynchronizeModel $synchronize
 * @property BookingModel $booking
 * @property BookingGoodsModel $bookingGoods
 * @property BookingContainerModel $bookingContainer
 * @property BookingExtensionModel $bookingExtension
 * @property LogModel $logHistory
 */
class Synchronize extends CI_Controller
{
    public function __construct()
    {
        parent::__construct();

        $this->load->model('SynchronizeModel', 'synchronize');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('BookingContainerModel', 'bookingContainer');
        $this->load->model('BookingExtensionModel', 'bookingExtension');
    }

    /**
     * Index synchronize command.
     */
    public function index()
    {
        $bookings = $this->synchronize->getBookingSynchronize();

        $data = [
            'title' => "Synchronize",
            'subtitle' => "Synchronize",
            'page' => "synchronize/index",
            'bookings' => $bookings
        ];
        $this->load->view('template/layout', $data);
    }

    public function upstream($id)
    {
        $referenceHeader = $this->synchronize->getBookingSynchronize($id);
        $referenceContainer = $this->bookingContainer->getBookingContainersByBooking($id);
        $referenceGoods = $this->bookingGoods->getBookingGoodsByBooking($id);
        $existingBooking = $this->synchronize->getTppTransByBcf($referenceHeader['no_reference']);

        $username = UserModel::authenticatedUserData('name');

        if (empty($existingBooking)) {
            $this->synchronize->connection->trans_start();

            $cargo = '';
            $m3 = 0;
            $quantity = 0;
            foreach ($referenceGoods as $goods) {
                $cargo .= ' ' . $goods['goods_name'];
                $m3 += $goods['volume'];
                $quantity += $goods['quantity'];
                $unit = $goods['unit'];
            }

            $transType = 'LCL';
            if (empty($referenceContainer)) {
                $transType = 'FCL';
            }

            $no_order = $this->synchronize->getAutoNumberTppTrans();
            $this->synchronize->createTppTrans([
                'no_order' => $no_order,
                'jenis_trans' => $transType,
                'bc_11' => $referenceHeader['no_bc11'],
                'pos_bc_11' => $referenceHeader['bc11_pos'],
                'tgl_bc_11' => $referenceHeader['bc11_date'],
                'bc_15' => $referenceHeader['no_reference'],
                'tgl_bc_15' => $referenceHeader['reference_date'],
                'status_owner' => $referenceHeader['document_status'],
                'vessel' => $referenceHeader['vessel'],
                'voyage' => $referenceHeader['voyage'],
                'etad' => null,
                'cargo' => !empty($referenceHeader['description']) ? $referenceHeader['description'] : $cargo,
                'm3' => $m3,
                'consignee' => $referenceHeader['customer_name'],
                'consignee_add' => '',
                'skep_bdn' => null,
                'tgl_bdn' => null,
                'username' => $username,
                'userdate' => sql_date_format('now')
            ]);
            foreach ($referenceContainer as $container) {
                $this->synchronize->createTppTranscont([
                    'no_orderdtl' => $this->synchronize->getAutoNumberTppTranscont($no_order),
                    'no_unit' => $container['no_container'],
                    'sizecode' => $container['size'],
                    'typecode' => $container['type'],
                    'jumlah' => $quantity,
                    'satuan' => $unit,
                    'kubikasi' => 0,
                    'tgl_in' => null,
                    'tgl_out' => null,
                ]);
            };

            $this->synchronize->connection->trans_complete();

            if ($this->synchronize->connection->trans_status()) {
                flash('success', "Order <strong>{$no_order}</strong> successfully created");
            } else {
                flash('danger', "Save order <strong>{$no_order}</strong> failed, try again or contact administrator");
            }
        } else {
            flash('danger', "Same order bcf <strong>{$referenceHeader['no_reference']}</strong> found");
        }
        redirect("synchronize");
    }

    public function view($cacahId)
    {
        $nhp = $this->nhp->getTppCacahsByCacahId($cacahId);
        $containers = $this->nhp->getTppCacahContsByCacahId($cacahId);
        $details = $this->nhp->getTppCacahDetailsByCacahId($cacahId);

        $data = [
            'nhp' => $nhp,
            'containers' => $containers,
            'details' => $details
        ];
        $this->load->view('nhp/view', $data);
    }

    /**
     * To create pdf from tci cacah application
     * Not Yet implemented
     * @param $cacahId
     */
    public function generate_pdf($cacahId)
    {
        $this->load->helper('number');
        $this->load->helper('angka');
        $nhp = $this->nhp->getTppCacahsByCacahId($cacahId);
        $containers = $this->nhp->getTppCacahContsByCacahId($cacahId);
        $details = $this->nhp->getTppCacahDetailsByCacahId($cacahId);
        foreach ($details as $key => $item) {
            $index = substr($item['cacahdtl_id'], 0, 14);
            $detailContainers[$index][$key] = $item;
        }

        $data = [
            'nhp' => $nhp,
            'containers' => $containers,
            'detailContainers' => $detailContainers
        ];
        $html = $this->load->view('nhp/print_nhp', $data, true);

        $this->load->library('pdf');
        $this->pdf->generate($html);
    }

    /**
     * To create word from tci cacah application
     * Not Yet implemented
     * @param $cacahId
     */
    public function generate_word($cacahId)
    {
        $filename = 'nhp_' . $cacahId . '.docx';
        header("Content-Type: application/vnd.openxmlformats-officedocument.wordprocessing‌​ml.document");// you should look for the real header that you need if it's not Word 2007!!!
        header('Content-Disposition: attachment; filename=' . $filename);

        $phpWord = new PhpOffice\PhpWord\PhpWord();
        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(11);
        $section = $phpWord->addSection(array(
            'paperSize' => 'A4',
            'marginLeft' => 900,
            'marginRight' => 900,
            'marginTop' => 900,
            'marginBottom' => 900
        ));

        $this->load->helper('number');
        $this->load->helper('angka');

        $nhp = $this->nhp->getTppCacahsByCacahId($cacahId);
        $containers = $this->nhp->getTppCacahContsByCacahId($cacahId);
        $details = $this->nhp->getTppCacahDetailsByCacahId($cacahId);
        foreach ($details as $key => $item) {
            $index = substr($item['cacahdtl_id'], 0, 14);
            $detailContainers[$index][$key] = $item;
        }
        $data = [
            'nhp' => $nhp,
            'containers' => $containers,
            'detailContainers' => $detailContainers,
            'phpWord' => $phpWord,
            'section' => $section,
        ];
        $html = $this->load->view('nhp/nhp_word', $data, true);

        PhpOffice\PhpWord\Shared\Html::addHtml($section, $html, false, false);

        $objWritter = PhpOffice\PhpWord\IOFactory::createWriter($phpWord, 'Word2007');
        $objWritter->save("php://output");
//        $objWritter->save($filename);
    }
}