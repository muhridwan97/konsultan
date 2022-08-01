<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Work_order_handheld_unlocks
 * @property WorkOrderModel $workOrder
 * @property WorkOrderUnlockHandheldModel $workOrderUnlockHandheld
 * @property BookingModel $booking
 * @property PeopleModel $people
 */
class Work_order_unlock_handheld extends MY_Controller
{
    /**
     * Work_order_unlock_handhelds constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('WorkOrderUnlockHandheldModel', 'workOrderUnlockHandheld');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('PeopleModel', 'people');

        $this->setFilterMethods([
            'ajax_get_data' => 'GET',
            'unlock' => 'GET',
        ]);
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_UNLOCK_HANDHELD);
    }

    /**
     * Show unlocked work order data list.
     */
    public function index()
    {
        $customer = $this->people->getById(get_url_param('customers'));
        $booking = $this->booking->getBookingById(get_url_param('bookings'));

        $this->render('workorder_unlock_handheld/index', compact('customer', 'booking'), 'Unlocked Job');
    }

    /**
     * Get ajax paging data unlocked work order.
     */
    public function ajax_get_data()
    {
        $filters = array_merge(if_empty($_GET, []), [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir'],
        ]);

        $data = $this->workOrderUnlockHandheld->getAll($filters);

        $this->render_json($data);
    }

    /**
     * Show view unlocked work order.
     *
     * @param $workOrderId
     */
    public function view($workOrderId)
    {
        $workOrderUnlockHandheld = $this->workOrderUnlockHandheld->getBy([
            'id_work_order' => $workOrderId
        ], true);

        $this->render('workorder_unlock_handheld/view', compact('workOrderUnlockHandheld'));
    }

    /**
     * Unlock work order.
     *
     * @param null $workOrderId
     */
    public function unlock($workOrderId = null)
    {
        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
        $workOrderUnlockHandheld = $this->workOrderUnlockHandheld->getBy([
            'id_work_order' => $workOrderId
        ], true);

        $this->render('workorder_unlock_handheld/unlock', compact('workOrder', 'workOrderUnlockHandheld'));
    }

    /**
     * Save work order overtime.
     *
     * @param $workOrderId
     */
    public function save($workOrderId = null)
    {
        if ($this->validate()) {
            $workOrderId = if_empty($workOrderId, $this->input->post('work_order'));
            $unlockedUntil = $this->input->post('unlocked_until');
            $description = $this->input->post('description');

            $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
            $workOrderUnlockHandheld = $this->workOrderUnlockHandheld->getBy([
                'id_work_order' => $workOrderId
            ], true);

            $data = [
                'id_work_order' => $workOrderId,
                'unlocked_until' => format_date($unlockedUntil),
                'description' => $description,
            ];
            if (empty($workOrderUnlockHandheld)) {
                $save = $this->workOrderUnlockHandheld->create($data);
            } else {
                $save = $this->workOrderUnlockHandheld->update($data, $workOrderUnlockHandheld['id']);
            }

            if ($save) {
                flash('success', "Work order {$workOrder['no_work_order']} successfully unlocked for handheld", 'work-order-unlock-handheld');
            } else {
                flash('danger', "Unlock handheld work order {$workOrder['no_work_order']} failed");
            }
        }
        $this->unlock($workOrderId);
    }

    /**
     * Get base validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'work_order' => 'trim|required',
            'unlocked_until' => 'required|max_length[20]',
            'description' => 'required|max_length[500]',
        ];
    }

    /**
     * Perform deleting unlock handheld data.
     *
     * @param $workOrderId
     */
    public function delete($workOrderId)
    {
        $workOrderUnlockHandheld = $this->workOrderUnlockHandheld->getBy([
            'id_work_order' => $workOrderId
        ], true);

        if ($this->workOrderUnlockHandheld->delete($workOrderUnlockHandheld['id'])) {
            flash('warning', "Unlock deleted, workorder {$workOrderUnlockHandheld['no_work_order']} now is locked for handheld");
        } else {
            flash('danger', "Delete unlock {$workOrderUnlockHandheld['no_work_order']} failed");
        }
        redirect('work-order-unlock-handheld');
    }
}