<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Booking_news
 * @property BookingModel $booking
 * @property BookingTypeModel $bookingType
 * @property BookingNewsModel $bookingNews
 * @property BookingNewsDetailModel $bookingNewsDetail
 * @property PeopleModel $people
 */
class Booking_news extends CI_Controller
{
    /**
     * BookingNews constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BookingModel', 'booking');
        $this->load->model('BookingTypeModel', 'bookingType');
        $this->load->model('BookingNewsModel', 'bookingNews');
        $this->load->model('BookingNewsDetailModel', 'bookingNewsDetail');
        $this->load->model('PeopleModel', 'people');
    }

    /**
     * Show booking news data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_NEWS_VIEW);

        $bookingNews = $this->bookingNews->getAllBookingNews();
        $data = [
            'title' => "Booking Accumulation Report",
            'subtitle' => "Data booking news",
            'page' => "booking_news/index",
            'bookingNews' => $bookingNews
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show detail booking news.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_NEWS_VIEW);

        $bookingNews = $this->bookingNews->getBookingNewsById($id);
        $bookingNewsDetails = $this->bookingNewsDetail->getBookingNewsDetailByBookingNews($id);
        $data = [
            'title' => "Booking Accumulation Report",
            'subtitle' => "View booking news",
            'page' => "booking_news/view",
            'bookingNews' => $bookingNews,
            'bookingNewsDetails' => $bookingNewsDetails
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Show detail branch.
     * @param $id
     */
    public function print_report($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_NEWS_VIEW);

        $format = if_empty($this->input->get('format'), 'pdf');

        $bookingNews = $this->bookingNews->getBookingNewsById($id);
        $bookingNewsDetails = $this->bookingNewsDetail->getBookingNewsDetailByBookingNews($id);
        $detailColumns = [];
        foreach ($bookingNewsDetails as $bookingNewsDetail) {
            if (key_exists($bookingNewsDetail['no_reference'], $detailColumns)) {
                $detailColumns[$bookingNewsDetail['no_reference']] = $detailColumns[$bookingNewsDetail['no_reference']] + 1;
            } else {
                $detailColumns[$bookingNewsDetail['no_reference']] = 1;
            }
        }

        $data = [
            'title' => "Booking Accumulation Report",
            'subtitle' => "Print booking news",
            'page' => "booking_news/print",
            'bookingNews' => $bookingNews,
            'bookingNewsDetails' => $bookingNewsDetails,
            'detailColumns' => $detailColumns,
        ];

        if ($format == 'excel') {
            $this->bookingNews->exportExcel($data);
        } else {
            $report = $this->load->view('template/print_pdf', $data, true);
            echo $report;die();
            $dompdf = new \Dompdf\Dompdf(['isHtml5ParserEnabled' => true]);
            $dompdf->loadHtml($report);
            $dompdf->set_option("isPhpEnabled", true);
            $dompdf->setPaper('A4', 'landscape');
            $dompdf->render();
            $dompdf->stream("ba_report.pdf", array("Attachment" => false));
        }
    }

    /**
     * Show create booking news.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_NEWS_CREATE);

        $data = [
            'title' => "Booking News",
            'subtitle' => "Create booking news",
            'page' => "booking_news/create",
            'tps' => $this->people->getByType(PeopleModel::$TYPE_TPS),
            'bookings' => $this->booking->getAllBookings([
                'category' => BookingTypeModel::CATEGORY_INBOUND
            ]),
        ];
        $this->load->view('template/layout', $data);
    }

    /**
     * Save booking news
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_NEWS_CREATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('type', 'Report type', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('branch', 'Branch', 'trim|required');
            $this->form_validation->set_rules('no_booking_news', 'Report number', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('booking_news_date', 'Report date', 'trim|required|alpha_numeric_spaces');
            $this->form_validation->set_rules('no_sprint', 'SPRINT number', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('sprint_date', 'SPRINT date', 'trim|required|alpha_numeric_spaces');
            $this->form_validation->set_rules('tps', 'TPS', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('chief_name', 'Chief name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('chief_nip', 'Chief NIP', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('description', 'Description', 'trim|required|max_length[500]');
            $this->form_validation->set_rules('bookings[]', 'Booking data', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $branch = $this->input->post('branch');
                $type = $this->input->post('type');
                $noBookingNews = $this->input->post('no_booking_news');
                $bookingNewsDate = sql_date_format($this->input->post('booking_news_date'), false);
                $noSprint = $this->input->post('no_sprint');
                $sprintDate = sql_date_format($this->input->post('sprint_date'), false);
                $tps = $this->input->post('tps');
                $chiefName = $this->input->post('chief_name');
                $chiefNip = $this->input->post('chief_nip');
                $description = $this->input->post('description');
                $bookings = $this->input->post('bookings');

                $this->db->trans_start();

                // inserting booking news header
                $this->bookingNews->createBookingNews([
                    'id_branch' => $branch,
                    'type' => $type,
                    'no_booking_news' => $noBookingNews,
                    'booking_news_date' => $bookingNewsDate,
                    'no_sprint' => $noSprint,
                    'sprint_date' => $sprintDate,
                    'tps' => $tps,
                    'chief_name' => $chiefName,
                    'chief_nip' => $chiefNip,
                    'description' => $description,
                    'created_by' => UserModel::authenticatedUserData('id')
                ]);
                $bookingNewsId = $this->db->insert_id();

                // insert booking news detail
                if (!empty($bookings)) {
                    if (key_exists('booking', $bookings)) {
                        $bookingData = $bookings['booking'];
                        $conditionData = $bookings['condition'];
                        $descriptionData = $bookings['description'];

                        for ($i = 0; $i < count($bookingData); $i++) {
                            $this->bookingNewsDetail->createBookingNewsDetail([
                                'id_booking_news' => $bookingNewsId,
                                'id_booking' => $bookingData[$i],
                                'condition' => $conditionData[$i],
                                'description' => $descriptionData[$i],
                            ]);
                        }
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Booking news <strong>{$noBookingNews}</strong> successfully created", 'booking_news');
                } else {
                    flash('danger', "Create booking news <strong>{$noBookingNews}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->create();
    }

    /**
     * Show edit booking news.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_NEWS_EDIT);

        $data = [
            'title' => "Booking News",
            'subtitle' => "Edit booking news",
            'page' => "booking_news/edit",
            'tps' => $this->people->getByType(PeopleModel::$TYPE_TPS),
            'bookings' => $this->booking->getAllBookings([
                'category' => BookingTypeModel::CATEGORY_INBOUND
            ]),
            'bookingNews' => $this->bookingNews->getBookingNewsById($id),
            'bookingNewsDetails' => $this->bookingNewsDetail->getBookingNewsDetailByBookingNews($id, true),
        ];

        $this->load->view('template/layout', $data);
    }

    /**
     * Update booking new by id.
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_NEWS_CREATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Report data', 'trim|required|integer');
            $this->form_validation->set_rules('branch', 'Branch', 'trim|required');
            $this->form_validation->set_rules('type', 'Report type', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('no_booking_news', 'Report number', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('booking_news_date', 'Report date', 'trim|required|alpha_numeric_spaces');
            $this->form_validation->set_rules('no_sprint', 'SPRINT number', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('sprint_date', 'SPRINT date', 'trim|required|alpha_numeric_spaces');
            $this->form_validation->set_rules('tps', 'TPS', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('chief_name', 'Chief name', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('chief_nip', 'Chief NIP', 'trim|required|max_length[50]');
            $this->form_validation->set_rules('description', 'Description', 'trim|required|max_length[500]');
            $this->form_validation->set_rules('bookings[]', 'Booking data', 'trim|required');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', 'Form inputs are invalid');
            } else {
                $id = $this->input->post('id');
                $branch = $this->input->post('branch');
                $type = $this->input->post('type');
                $noBookingNews = $this->input->post('no_booking_news');
                $bookingNewsDate = sql_date_format($this->input->post('booking_news_date'), false);
                $noSprint = $this->input->post('no_sprint');
                $sprintDate = sql_date_format($this->input->post('sprint_date'), false);
                $tps = $this->input->post('tps');
                $chiefName = $this->input->post('chief_name');
                $chiefNip = $this->input->post('chief_nip');
                $description = $this->input->post('description');
                $bookings = $this->input->post('bookings');

                $this->db->trans_start();

                // update booking news header
                $this->bookingNews->updateBookingNews([
                    'id_branch' => $branch,
                    'type' => $type,
                    'no_booking_news' => $noBookingNews,
                    'booking_news_date' => $bookingNewsDate,
                    'no_sprint' => $noSprint,
                    'sprint_date' => $sprintDate,
                    'tps' => $tps,
                    'chief_name' => $chiefName,
                    'chief_nip' => $chiefNip,
                    'description' => $description,
                    'updated_at' => date('Y-m-d H:i:s'),
                    'updated_by' => UserModel::authenticatedUserData('id')
                ], $id);

                // insert booking news detail
                if (!empty($bookings)) {
                    if (key_exists('booking', $bookings)) {
                        $bookingData = $bookings['booking'];
                        $conditionData = $bookings['condition'];
                        $descriptionData = $bookings['description'];

                        $this->bookingNewsDetail->deleteBookingNewsDetailByBookingNews($id);
                        for ($i = 0; $i < count($bookingData); $i++) {
                            $this->bookingNewsDetail->createBookingNewsDetail([
                                'id_booking_news' => $id,
                                'id_booking' => $bookingData[$i],
                                'condition' => $conditionData[$i],
                                'description' => $descriptionData[$i],
                            ]);
                        }
                    }
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Booking news <strong>{$noBookingNews}</strong> successfully updated");

                    redirect("booking_news");
                } else {
                    flash('danger', "Update booking news <strong>{$noBookingNews}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->edit($id);
    }

    /**
     * Perform deleting booking news data.
     */
    public function delete()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BOOKING_NEWS_DELETE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $this->form_validation->set_rules('id', 'Booking news data', 'trim|required|integer');

            if ($this->form_validation->run() == FALSE) {
                flash('warning', validation_errors());
            } else {
                $bookingNewsId = $this->input->post('id');
                $bookingNewsData = $this->bookingNews->getBookingNewsById($bookingNewsId);
                $delete = $this->bookingNews->deleteBookingNews($bookingNewsId);

                if ($delete) {
                    flash('warning', "Booking <strong>{$bookingNewsData['no_booking_news']}</strong> successfully deleted");
                } else {
                    flash('danger', "Delete booking news <strong>{$bookingNewsData['no_booking_news']}</strong> failed, try again or contact administrator");
                }
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        redirect('booking_news');
    }
}