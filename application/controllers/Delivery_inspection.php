<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Delivery_inspection
 * @property DeliveryInspectionModel $deliveryInspection
 * @property DeliveryInspectionDetailModel $deliveryInspectionDetail
 * @property SafeConductModel $safeConduct
 * @property NotificationModel $notification
 * @property Uploader $uploader
 * @property Exporter $exporter
 */
class Delivery_inspection extends MY_Controller
{
    /**
     * Delivery_inspection constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('DeliveryInspectionModel', 'deliveryInspection');
        $this->load->model('DeliveryInspectionDetailModel', 'deliveryInspectionDetail');
        $this->load->model('SafeConductModel', 'safeConduct');
        $this->load->model('NotificationModel', 'notification');
        $this->load->model('modules/Uploader', 'uploader');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'data' => 'GET',
        ]);
    }

    /**
     * Show delivery inspection data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_INSPECTION_VIEW);

        if ($this->input->get('export')) {
            $reports = $this->deliveryInspection->getAll($_GET);
            $this->exporter->exportFromArray('Delivery Inspection', $reports);
        } else {
            $this->render('delivery_inspection/index');
        }
    }

    /**
     * Get ajax datatable.
     */
    public function data()
    {
        $filters = array_merge(get_url_param('filter') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir']
        ]);

        $deliveryTrackings = $this->deliveryInspection->getAll($filters);

        $this->render_json($deliveryTrackings);
    }

    /**
     * Show view delivery inspection form.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_INSPECTION_VIEW);

        $deliveryInspection = $this->deliveryInspection->getById($id);
        $deliveryInspectionDetails = $this->deliveryInspectionDetail->getBy([
            'id_delivery_inspection' => $id
        ]);
        foreach ($deliveryInspectionDetails as &$deliveryInspectionDetail) {
            $deliveryInspectionDetail['safe_conducts'] = $this->safeConduct->getBy([
                'safe_conducts.id_transporter_entry_permit' => $deliveryInspectionDetail['id_tep']
            ]);
        }

        $this->render('delivery_inspection/view', compact('deliveryInspection', 'deliveryInspectionDetails'));
    }

    /**
     * Get base validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'location' => 'trim|required',
            'pic_tci' => 'trim|required',
            'pic_khaisan' => 'trim|required',
            'pic_smgp' => 'trim|required',
            'total_vehicle' => 'trim|required',
            'description' => 'trim|max_length[500]',
        ];
    }

    /**
     * Show edit delivery inspection form.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_INSPECTION_EDIT);

        $deliveryInspection = $this->deliveryInspection->getById($id);

        if ($deliveryInspection['status'] == DeliveryInspectionModel::STATUS_PENDING) {
            $olderInspection = $this->deliveryInspection->getBy([
                'delivery_inspections.date<' => $deliveryInspection['date'],
                'delivery_inspections.status' => DeliveryInspectionModel::STATUS_PENDING,
            ], true);
            if (!empty($olderInspection)) {
                flash('danger', "Please completing data before this one first ({$deliveryInspection['date']})", 'delivery-inspection');
            }
        }

        $deliveryInspectionDetails = $this->deliveryInspectionDetail->getBy([
            'id_delivery_inspection' => $id
        ]);

        $this->render('delivery_inspection/edit', compact('deliveryInspection', 'deliveryInspectionDetails'));
    }

    /**
     * Update data delivery inspection by id.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_INSPECTION_EDIT);

        if ($this->validate()) {
            $deliveryInspection = $this->deliveryInspection->getById($id);

            $location = $this->input->post('location');
            $picTCI = $this->input->post('pic_tci');
            $picKhaisan = $this->input->post('pic_khaisan');
            $picSMGP = $this->input->post('pic_smgp');
            $totalMatch = if_empty($this->input->post('total_match'), 0);
            $totalUnmatch = $deliveryInspection['total_vehicle'] - $totalMatch;
            $description = $this->input->post('description');

            $status = $deliveryInspection['status'] == 'PENDING'
                ? DeliveryInspectionModel::STATUS_CONFIRMED
                : $deliveryInspection['status'];

            $update = $this->deliveryInspection->update([
                'location' => $location,
                'pic_tci' => $picTCI,
                'pic_khaisan' => $picKhaisan,
                'pic_smgp' => $picSMGP,
                'total_match' => $totalMatch,
                'total_unmatch' => $totalUnmatch,
                'status' => $status,
                'description' => $description
            ], $id);

            if ($update) {
                $deliveryInspection = $this->deliveryInspection->getById($id);

                $chatMessage = "*Hand over process {$deliveryInspection['date']}*\n";
                $chatMessage .= "————————————————\n";
                $chatMessage .= "*Location:* {$deliveryInspection['location']}\n";
                $chatMessage .= "*PIC TCI:* {$deliveryInspection['pic_tci']}\n";
                $chatMessage .= "*PIC Khaisan:* {$deliveryInspection['pic_khaisan']}\n";
                $chatMessage .= "*PIC SMGP:* {$deliveryInspection['pic_smgp']}\n";
                $chatMessage .= "*Total Vehicle:* {$deliveryInspection['total_vehicle']}\n";
                $chatMessage .= "*Total Actual:* {$deliveryInspection['total_match']}\n";
                $chatMessage .= "*Total Unmatch:* {$deliveryInspection['total_unmatch']}";

                $broadcastData = [
                    'url' => 'sendMessage',
                    'method' => 'POST',
                    'payload' => [
                        'chatId' => detect_chat_id('6281232850984-1587696310'), // HEAVY EQP & QHSE
                        'body' => $chatMessage,
                    ]
                ];
                $this->notification->broadcast($broadcastData, NotificationModel::TYPE_CHAT_PUSH);

                flash('success', "Delivery inspection {$deliveryInspection['date']} successfully updated", 'delivery-inspection');
            } else {
                flash('danger', "Update delivery inspection failed");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting delivery inspection data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_INSPECTION_DELETE);

        $deliveryInspection = $this->deliveryInspection->getById($id);

        if ($this->deliveryInspection->delete($id)) {
            flash('warning', "Delivery inspection {$deliveryInspection['date']} successfully deleted");
        } else {
            flash('danger', "Delete delivery inspection failed");
        }
        redirect('delivery-inspection');
    }

}