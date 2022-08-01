<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Work_order_overtime
 * @property WorkOrderModel $workOrder
 * @property WorkOrderOvertimeChargeModel $workOrderOvertime
 * @property BookingModel $booking
 * @property PeopleModel $people
 * @property Uploader $uploader
 */
class Work_order_overtime extends MY_Controller
{
    /**
     * Work_order_document constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('WorkOrderModel', 'workOrder');
        $this->load->model('WorkOrderOvertimeChargeModel', 'workOrderOvertime');
        $this->load->model('BookingModel', 'booking');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('modules/Uploader', 'uploader');

        $this->setFilterMethods([
            'ajax_get_data' => 'GET',
            'validate_overtime' => 'GET',
        ]);
    }

    /**
     * Show document data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_VIEW);

        $customer = $this->people->getById(get_url_param('customers'));
        $booking = $this->booking->getBookingById(get_url_param('bookings'));

        $this->render('workorder_overtime/index', compact('customer', 'booking'), 'Job Overtime');
    }

    /**
     * Get ajax paging data work order overtime.
     */
    public function ajax_get_data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_VIEW);

        $filters = array_merge(get_url_param('filter_work_order_overtime') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir'],
        ]);

        $data = $this->workOrderOvertime->getAll($filters);

        $this->render_json($data);
    }

    /**
     * Show view work order overtime.
     *
     * @param $workOrderId
     */
    public function view($workOrderId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_VIEW);

        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
        $workOrderOvertime = $this->workOrderOvertime->getBy([
            'work_orders.id' => $workOrder['id'],
        ], true);

        $this->render('workorder_overtime/view', compact('workOrder', 'workOrderOvertime'));
    }

    /**
     * Show validate form.
     *
     * @param array $workOrderId
     * @return bool|void
     */
    public function validate_overtime($workOrderId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_VALIDATE_OVERTIME);

        $workOrder = $this->workOrder->getWorkOrderById($workOrderId);
        $workOrderOvertime = $this->workOrderOvertime->getBy([
            'work_orders.id' => $workOrder['id'],
        ], true);

        $this->render('workorder_overtime/validate', compact('workOrder', 'workOrderOvertime'));
    }

    /**
     * Save work order overtime.
     *
     * @param $workOrderId
     */
    public function save($workOrderId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_WORKORDER_VALIDATE_OVERTIME);

        if ($this->validate()) {
            $overtimeChargedTo = $this->input->post('overtime_charged_to');
            $reason = $this->input->post('reason');
            $description = $this->input->post('description');

            $workOrderOvertime = $this->workOrderOvertime->getBy([
                'work_orders.id' => $workOrderId,
            ], true);

            $attachment = $workOrderOvertime['overtime_attachment'];
            if (!empty($_FILES['overtime_attachment']['name'])) {
                $uploadFile = $this->uploader->uploadTo('overtime_attachment', [
                    'destination' => 'work-order-overtime/' . date('Y/m')
                ]);
                if ($uploadFile) {
                    $uploadedData = $this->uploader->getUploadedData();
                    $attachment = $uploadedData['uploaded_path'];
                } else {
                    flash('warning', $this->uploader->getDisplayErrors());
                }
            } else {
                $uploadFile = true;
            }

            if ($uploadFile) {
                $data = [
                    'id_work_order' => $workOrderId,
                    'overtime_charged_to' => $overtimeChargedTo,
                    'overtime_attachment' => $attachment,
                    'reason' => $reason,
                    'description' => $description,
                    'created_at' => date('Y-m-d H:i:s'),
                    'created_by' => UserModel::authenticatedUserData('id'),
                ];
                if (empty($workOrderOvertime['id'])) {
                    $save = $this->workOrderOvertime->create($data);
                } else {
                    $save = $this->workOrderOvertime->update($data, $workOrderOvertime['id']);
                }

                if ($save) {
                    flash('success', "Validate overtime {$workOrderOvertime['no_work_order']} successfully updated", 'work-order-overtime');
                } else {
                    flash('danger', "Update overtime {$workOrderOvertime['no_work_order']} failed");
                }
            }
        }
        $this->validate_overtime($workOrderId);
    }

    /**
     * Get base validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'overtime_charged_to' => 'trim|required|in_list[OPERATIONAL,CUSTOMER]',
            'reason' => 'trim|min_length[25]|max_length[500]',
            'description' => 'trim|max_length[500]',
        ];
    }
}