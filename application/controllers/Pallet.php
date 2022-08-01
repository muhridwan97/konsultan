<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Pallet
 * @property PalletModel $pallet
 * @property BookingModel $booking
 * @property BookingGoodsModel $bookingGoods
 * @property BookingContainerModel $bookingContainer
 */
class Pallet extends CI_Controller
{
    /**
     * Pallet constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('PalletModel', 'pallet');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingGoodsModel', 'bookingGoods');
        $this->load->model('BookingContainerModel', 'bookingContainer');
    }

    /**
     * Show pallet data.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PALLET_VIEW);

        $data = [
            'title' => "Pallet",
            'subtitle' => "Data pallet",
            'page' => "pallet/index",
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Get ajax datatable pallet.
     */
    public function pallet_data()
    {
        $filters = [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ];

        $data = $this->pallet->getAll($filters);

        header('Content-Type: application/json');
        echo json_encode($data);
    }

    /**
     * Show detail warehouse.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PALLET_VIEW);

        $pallet = $this->pallet->getById($id);
        $data = [
            'title' => "Pallet",
            'subtitle' => "View pallet",
            'page' => "pallet/view",
            'pallet' => $pallet
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show create pallet.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PALLET_CREATE);

        $data = [
            'title' => "Pallet",
            'subtitle' => "Create pallet",
            'page' => "pallet/create",
            'bookings' => $this->booking->getAllBookings(['category' => 'INBOUND'])
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Save new pallet.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PALLET_CREATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('branch', 'Branch', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $branch = $this->input->post('branch');
                $booking = $this->input->post('booking');
                $total = $this->input->post('total');
                $description = $this->input->post('description');

                $this->db->trans_start();

                $batch = $this->pallet->getNextBatch();

                if (is_array($total)) {
                    for ($i = 0; $i < count($total); $i++) {
                        for ($j = 0; $j < $total[$i]; $j++) {
                            $this->pallet->create([
                                'id_branch' => $branch,
                                'id_booking' => $booking,
                                'no_pallet' => $this->pallet->getAutoNumberPallet(),
                                'batch' => $batch,
                                'description' => $description[$i]
                            ]);
                        }
                    }
                } else {
                    for ($i = 0; $i < $total; $i++) {
                        $this->pallet->create([
                            'id_branch' => $branch,
                            'no_pallet' => $this->pallet->getAutoNumberPallet(),
                            'batch' => $batch,
                            'description' => $description
                        ]);
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Generate pallet with batch <strong>{$batch}</strong> successfully created", 'pallet');
                } else {
                    flash('danger', "Generate pallet batch <strong>{$batch}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->create();
    }

    /**
     * Print pallet.
     * @param $id
     */
    public function print_pallet($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PALLET_PRINT);

        $pallet = $this->pallet->getById($id);
        $barcode = new \Milon\Barcode\DNS2D();
        $barcode->setStorPath(APPPATH . "/cache/");
        $data = [
            'title' => "Pallet",
            'pallet' => $pallet,
            'barcode' => $barcode
        ];
        $this->load->view('pallet/print_pallet', $data);
    }

    /**
     * Perform deleting pallet data.
     */
    public function delete()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_PALLET_DELETE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Pallet data', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $palletId = $this->input->post('id');
                $batch = $this->input->post('batch');
                if ($batch) {
                    $palletData['no_pallet'] = 'Batch ' . $palletId;
                    $delete = $this->pallet->delete(['batch' => $palletId]);
                } else {
                    $palletData = $this->pallet->getById($palletId);
                    $delete = $this->pallet->delete($palletId);
                }

                if ($delete) {
                    flash('warning', "Pallet <strong>{$palletData['no_pallet']}</strong> successfully deleted");
                } else {
                    flash('danger', "Delete pallet <strong>{$palletData['no_pallet']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('pallet');
    }

    /**
     * Show booking form.
     */
    public function ajax_get_booking_form()
    {
        if ($this->input->server('REQUEST_METHOD') == "GET") {
            $bookingId = $this->input->get('id_booking');
            $booking = $this->booking->getBookingById($bookingId);
            $bookingContainers = $this->bookingContainer->getBookingContainersByBooking($bookingId);
            $bookingGoods = $this->bookingGoods->getBookingGoodsByBooking($bookingId);

            echo $this->load->view('pallet/_booking', [
                'booking' => $booking,
                'bookingContainers' => $bookingContainers,
                'bookingGoods' => $bookingGoods,
            ], true);
        }
    }

    /**
     * Generate many pallet number.
     */
    public function ajax_generate_pallet()
    {
        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $total = $this->input->post('total');
            $this->db->trans_start();

            $batch = $this->pallet->getNextBatch();

            $pallets = [];
            for ($i = 0; $i < $total; $i++) {
                $palletNo = $this->pallet->getAutoNumberPallet();
                $this->pallet->create([
                    'id_branch' => get_active_branch('id'),
                    'no_pallet' => $palletNo,
                    'batch' => $batch,
                ]);
                $pallets[] = $palletNo;
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                header('Content-Type: application/json');
                echo json_encode($pallets);
            } else {
                echo false;
            }
        }
    }
}