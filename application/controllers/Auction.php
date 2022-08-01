<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Auction
 * @property AuctionModel $auction
 * @property AuctionDetailModel $auctionDetail
 * @property WorkOrderContainerModel $workOrderContainer
 * @property WorkOrderGoodsModel $workOrderGoods
 * @property BookingModel $booking
 * @property ReportModel $report
 */
class Auction extends MY_Controller
{
    /**
     * Auction constructor.
     */
    public function __construct()
    {
        parent::__construct();

        AuthorizationModel::mustLoggedIn();

        $this->load->model('AuctionModel', 'auction');
        $this->load->model('AuctionDetailModel', 'auctionDetail');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('WorkOrderContainerModel', 'workOrderContainer');
        $this->load->model('WorkOrderGoodsModel', 'workOrderGoods');
        $this->load->model('ReportModel', 'report');
        $this->load->model('LogModel', 'logHistory');

        $this->setFilterMethods([
            'auction_data' => 'GET',
            'print_auction' => 'GET',
            'validate_auction' => 'POST|PUT|PATCH',
        ]);
    }

    /**
     * Show auction data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AUCTION_VIEW);
        $userData = UserModel::authenticatedUserData();
        $branch = get_active_branch();

        $this->render('auction/index', [], 'Auction');
    }

    /**
     * Get ajax datatable auction.
     */
    public function auction_data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AUCTION_VIEW);

        $filters = [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ];

        $data = $this->auction->getAll($filters);

        $this->render_json($data);
    }

    /**
     * Show detail auction.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AUCTION_VIEW);

        $auction = $this->auction->getById($id);
        $auctionDetails = $this->auctionDetail->getBy(['id_auction' => $id]);

        foreach ($auctionDetails as &$auctionDetail) {
            $auctionDetail['containers'] = $this->report->getContainerStockMove(['booking' => $auctionDetail['id_booking']]);
            $auctionDetail['goods'] = $this->report->getGoodsStockMove(['booking' => $auctionDetail['id_booking']]);
        }

        $this->render('auction/view', compact('auction', 'auctionDetails'));
    }

    /**
     * Print auction data.
     *
     * @param $id
     */
    public function print_auction($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AUCTION_PRINT);

        $auction = $this->auction->getById($id);
        $auctionDetails = $this->auctionDetail->getBy(['id_auction' => $id]);

        foreach ($auctionDetails as &$auctionDetail) {
            $auctionDetail['containers'] = $this->report->getContainerStockMove(['booking' => $auctionDetail['id_booking']]);
            $auctionDetail['goods'] = $this->report->getGoodsStockMove(['booking' => $auctionDetail['id_booking']]);
        }

        $this->layout = 'template/print';

        $this->render('auction/_view', compact('auction', 'auctionDetails'));
    }

    /**
     * Show form create auction.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AUCTION_CREATE);

        $bookings = $this->report->getAvailableStockBookingList();

        $this->render('auction/create', compact('bookings'));
    }

    /**
     * Save new auction data.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AUCTION_CREATE);

        if ($this->validate()) {
            $auctionNo = $this->auction->getAutoNumber();
            $branchId = $this->input->post('branch');
            $noDoc = $this->input->post('no_doc');
            $docDate = $this->input->post('doc_date');
            $auctionDate = $this->input->post('auction_date');
            $description = $this->input->post('description');
            $bookings = $this->input->post('bookings');

            $this->db->trans_start();

            $this->auction->create([
                'id_branch' => $branchId,
                'no_auction' => $auctionNo,
                'no_doc' => $noDoc,
                'doc_date' => sql_date_format($docDate, false),
                'auction_date' => sql_date_format($auctionDate, false),
                'status' => AuctionModel::STATUS_PENDING,
                'description' => $description,
            ]);
            $auctionId = $this->db->insert_id();

            if (key_exists('booking', $bookings)) {
                $bookingData = $bookings['booking'];
                $descriptionData = $bookings['description'];
                for ($i = 0; $i < count($bookingData); $i++) {
                    $this->auctionDetail->create([
                        'id_auction' => $auctionId,
                        'id_booking' => $bookingData[$i],
                        'description' => $descriptionData[$i]
                    ]);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Auction {$auctionNo} successfully created", 'auction');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->create();
    }

    /**
     * Show edit auction form.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AUCTION_EDIT);

        $auction = $this->auction->getById($id);
        $auctionDetails = $this->auctionDetail->getBy(['id_auction' => $id]);
        $bookings = $this->report->getAvailableStockBookingList();

        $this->render('auction/edit', compact('auction', 'auctionDetails', 'bookings'));
    }

    /**
     * Save new auction data.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AUCTION_EDIT);

        if ($this->validate()) {
            $branchId = $this->input->post('branch');
            $noDoc = $this->input->post('no_doc');
            $docDate = $this->input->post('doc_date');
            $auctionDate = $this->input->post('auction_date');
            $description = $this->input->post('description');
            $bookings = $this->input->post('bookings');

            $this->db->trans_start();

            $auction = $this->auction->getById($id);

            $this->auction->update([
                'id_branch' => $branchId,
                'no_doc' => $noDoc,
                'doc_date' => sql_date_format($docDate, false),
                'auction_date' => sql_date_format($auctionDate, false),
                'status' => ($auction['status'] == AuctionModel::STATUS_REJECTED ? AuctionModel::STATUS_PENDING : $auction['status']),
                'description' => $description,
            ], $id);

            if (key_exists('booking', $bookings)) {
                $this->auctionDetail->delete(['id_auction' => $id]);
                $bookingData = $bookings['booking'];
                $descriptionData = $bookings['description'];
                for ($i = 0; $i < count($bookingData); $i++) {
                    $this->auctionDetail->create([
                        'id_auction' => $id,
                        'id_booking' => $bookingData[$i],
                        'description' => $descriptionData[$i]
                    ]);
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Auction {$auction['no_auction']} successfully updated", 'auction');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->create();
    }

    /**
     * Perform deleting auction data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AUCTION_DELETE);

        $auction = $this->auction->getById($id);

        if ($this->auction->delete($id, true)) {
            flash('warning', "Auction {$auction['no_auction']} is successfully deleted");
        } else {
            flash('danger', "Delete auction {$auction['no_auction']} failed");
        }
        redirect('auction');
    }

    /**
     * Validate auction data.
     *
     * @param $type
     * @param $id
     */
    public function validate_auction($type, $id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_AUCTION_VALIDATE);

        if ($this->validate(['type' => 'in_list[approve,reject]'], ['type' => $type])) {

            $auction = $this->auction->getById($id);

            if ($type == 'approve') {
                $type = AuctionModel::STATUS_APPROVED;
            } else {
                $type = AuctionModel::STATUS_REJECTED;
            }
            $this->auction->update([
                'status' => $type,
            ], $id);

            if ($this->db->trans_status()) {
                flash('success', "Auction {$auction['no_auction']} is successfully {$type}");
            } else {
                flash('danger', 'Validating auction failed');
            }
        }
        redirect('auction');
    }

    /**
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'branch' => 'trim|required|integer|is_natural_no_zero',
            'no_doc' => 'trim|required|max_length[50]',
            'doc_date' => 'trim|required|max_length[20]',
            'auction_date' => 'trim|required|max_length[20]',
            'description' => 'trim|max_length[500]',
            'bookings[booking][]' => 'required',
            'bookings[description][]' => 'trim|max_length[500]'
        ];
    }
}