<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Delivery_tracking
 * @property DeliveryTrackingModel $deliveryTracking
 * @property DeliveryTrackingDetailModel $deliveryTrackingDetail
 * @property DeliveryTrackingAssignmentModel $deliveryTrackingAssignment
 * @property DeliveryTrackingSafeConductModel $deliveryTrackingSafeConduct
 * @property DeliveryTrackingGoodsModel $deliveryTrackingGoods
 * @property DepartmentContactGroupModel $departmentContactGroup
 * @property SafeConductGoodsModel $safeConductGoods
 * @property StatusHistoryModel $statusHistory
 * @property PeopleModel $people
 * @property UserModel $user
 * @property Uploader $uploader
 * @property Exporter $exporter
 * @property ImageEditor $imageEditor
 */
class Delivery_tracking extends MY_Controller
{
    /**
     * Delivery_tracking constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('DeliveryTrackingModel', 'deliveryTracking');
        $this->load->model('DeliveryTrackingDetailModel', 'deliveryTrackingDetail');
        $this->load->model('DeliveryTrackingAssignmentModel', 'deliveryTrackingAssignment');
        $this->load->model('DeliveryTrackingSafeConductModel', 'deliveryTrackingSafeConduct');
        $this->load->model('DeliveryTrackingGoodsModel', 'deliveryTrackingGoods');
        $this->load->model('SafeConductGoodsModel', 'safeConductGoods');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('PeopleModel', 'people');
        $this->load->model('UserModel', 'user');
        $this->load->model('DepartmentContactGroupModel', 'departmentContactGroup');
        $this->load->model('modules/Uploader', 'uploader');
        $this->load->model('modules/Exporter', 'exporter');
        $this->load->model('modules/ImageEditor', 'imageEditor');

        $this->setFilterMethods([
            'data' => 'GET',
            'view_item' => 'GET',
            'print_delivery_tracking' => 'GET',
            'print_delivery_tracking_state' => 'GET',
            'add_delivery_state' => 'GET',
            'save_delivery_state' => 'POST',
            'add_assignment_message' => 'GET',
            'save_assignment_message' => 'POST',
            'close' => 'POST|PUT',
            'add_safe_conduct' => 'GET',
            'save_safe_conduct' => 'POST|PUT',
        ]);
    }

    /**
     * Show deliveryTracking data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_TRACKING_VIEW);

        if ($this->input->get('export')) {
            $reports = $this->deliveryTracking->getReportItem($_GET);
            $this->exporter->exportLargeResourceFromArray('Delivery Trackings', $reports);
        } else {
            $customer = $this->people->getById($this->input->get('customer'));

            $this->render('delivery_tracking/index', compact('customer'));
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

        if (!AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_TRACKING_CREATE)) {
            $filters['user'] = UserModel::authenticatedUserData('id');
        }

        $deliveryTrackings = $this->deliveryTracking->getAll($filters);

        $this->render_json($deliveryTrackings);
    }

    /**
     * Show view delivery tracking form.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_TRACKING_VIEW);

        $deliveryTracking = $this->deliveryTracking->getById($id);
        $deliveryTrackingDetails = $this->deliveryTrackingDetail->getBy(['id_delivery_tracking' => $id]);
        foreach ($deliveryTrackingDetails as &$deliveryTrackingDetail) {
            $deliveryTrackingDetail['goods'] = $this->deliveryTrackingGoods->getBy([
                'delivery_tracking_goods.id_delivery_tracking_detail' => $deliveryTrackingDetail['id']
            ]);
        }
        $deliveryTrackingAssignments = $this->deliveryTrackingAssignment->getBy(['id_delivery_tracking' => $id]);
        $statusHistories = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_DELIVERY_TRACKING,
            'status_histories.id_reference' => $id
        ]);
        $data = compact('deliveryTracking', 'deliveryTrackingDetails', 'deliveryTrackingAssignments', 'statusHistories');

        $this->render('delivery_tracking/view', $data);
    }

    /**
     * Show view tracking of item.
     *
     * @param $id
     */
    public function view_item($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_TRACKING_VIEW);

        $deliveryTracking = $this->deliveryTracking->getById($id);
        $deliveryTrackingDetails = $this->deliveryTrackingDetail->getBy(['id_delivery_tracking' => $id]);
        foreach ($deliveryTrackingDetails as &$deliveryTrackingDetail) {
            $deliveryTrackingDetail['goods'] = $this->deliveryTrackingGoods->getBy([
                'delivery_tracking_goods.id_delivery_tracking_detail' => $deliveryTrackingDetail['id']
            ]);
        }

        $deliveryTrackingGoods = $this->deliveryTrackingGoods->getBy([
            'delivery_tracking_details.id_delivery_tracking' => $id
        ]);
        $deliveryGoods = array_unique(array_column($deliveryTrackingGoods, 'id_safe_conduct_goods'));
        if (empty($deliveryGoods)) {
            $safeConductGoods = [];
        } else {
            $safeConductGoods = $this->safeConductGoods->getBy(['safe_conduct_goods.id' => $deliveryGoods]);
        }
        foreach ($safeConductGoods as &$safeConductItem) {
            $safeConductItem['states'] = array_filter($deliveryTrackingDetails, function ($detail) use ($safeConductItem) {
                foreach ($detail['goods'] as $item) {
                    if ($item['id_safe_conduct_goods'] == $safeConductItem['id']) {
                        return true;
                    }
                }
                return false;
            });
        }

        $this->render('delivery_tracking/view_item', compact('deliveryTracking', 'safeConductGoods'));
    }

    /**
     * Print delivery tracking.
     *
     * @param $id
     */
    public function print_delivery_tracking($id)
    {
        $deliveryTracking = $this->deliveryTracking->getById($id);
        $deliveryTrackingDetails = $this->deliveryTrackingDetail->getBy(['id_delivery_tracking' => $id]);
        foreach ($deliveryTrackingDetails as &$deliveryTrackingDetail) {
            $deliveryTrackingDetail['goods'] = $this->deliveryTrackingGoods->getBy([
                'delivery_tracking_goods.id_delivery_tracking_detail' => $deliveryTrackingDetail['id']
            ]);
        }
        $statusHistories = $this->statusHistory->getBy([
            'status_histories.type' => StatusHistoryModel::TYPE_DELIVERY_TRACKING,
            'status_histories.id_reference' => $id
        ]);
        $data = compact('deliveryTracking', 'deliveryTrackingDetails', 'statusHistories');

        $page = $this->load->view('delivery_tracking/print', $data, true);

        $this->exporter->exportToPdf('Delivery ' . url_title($deliveryTracking['no_delivery_tracking']), $page);
    }

    /**
     * Print delivery tracking state.
     *
     * @param $id
     * @param $detailId
     */
    public function print_delivery_tracking_state($id, $detailId)
    {
        $deliveryTracking = $this->deliveryTracking->getById($id);
        $deliveryTrackingDetail = $this->deliveryTrackingDetail->getById($detailId);
        $reports = $this->deliveryTracking->getReportItem([
            'delivery_tracking' => $id,
            'delivery_tracking_detail' => $detailId
        ]);

        $page = $this->load->view('delivery_tracking/print_state', compact('deliveryTracking', 'deliveryTrackingDetail', 'reports'), true);

        $this->exporter->exportToPdf('Delivery ' . url_title($deliveryTracking['no_delivery_tracking']), $page);
    }

    /**
     * Show create delivery tracking form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_TRACKING_CREATE);

        $customer = $this->people->getById($this->input->post('customer'));
        $users = $this->user->getByPermission(PERMISSION_DELIVERY_TRACKING_ADD_STATE);

        $employeeDepartment = UserModel::authenticatedUserData('id_department');
        if (empty($employeeDepartment) || AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_TRACKING_ASSIGNMENT)) {
            $contactGroups = $this->departmentContactGroup->getAll();
        } else {
            $contactGroups = $this->departmentContactGroup->getBy(['ref_department_contact_groups.id_department' => UserModel::authenticatedUserData('id_department')]);
        }

        $this->render('delivery_tracking/create', compact('customer', 'users', 'contactGroups'));
    }

    /**
     * Get base validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'customer' => 'trim|required',
            'description' => 'trim|max_length[500]',
            'tracking_message' => 'max_length[500]',
        ];
    }

    /**
     * Save new delivery tracking.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_TRACKING_CREATE);

        if ($this->validate()) {
            $branchId = if_empty($this->input->post('branch'), get_active_branch_id());
            $customerId = $this->input->post('customer');
            $userId = $this->input->post('user');
            $reminderType = $this->input->post('reminder_type');
            $contactGroup = $this->input->post('contact_group');
            $description = $this->input->post('description');
            $assignmentMessage = $this->input->post('assignment_message');
            $noDeliveryTracking = $this->deliveryTracking->getAutoNumber();

            $uploadedFile = '';
            $uploadFile = true;
            if (!empty($_FILES['attachment']['name'])) {
                $uploadFile = $this->uploader->uploadTo('attachment', ['destination' => 'delivery-tracking/' . date('Y/m')]);
                if ($uploadFile) {
                    $uploadedData = $this->uploader->getUploadedData();
                    $uploadedFile = $uploadedData['uploaded_path'];
                } else {
                    flash('danger', $this->uploader->getDisplayErrors());
                }
            }

            if ($uploadFile) {
                $this->db->trans_start();

                $this->deliveryTracking->create([
                    'id_branch' => $branchId,
                    'id_customer' => $customerId,
                    'id_user' => $userId,
                    'no_delivery_tracking' => $noDeliveryTracking,
                    'status' => DeliveryTrackingModel::STATUS_ACTIVE,
                    'reminder_type' => $reminderType,
                    'id_department_contact_group' => if_empty($contactGroup, null),
                    'description' => $description
                ]);
                $deliveryTrackingId = $this->db->insert_id();

                if (!empty($assignmentMessage)) {
                    $this->deliveryTrackingAssignment->create([
                        'id_delivery_tracking' => $deliveryTrackingId,
                        'assignment_message' => $assignmentMessage,
                        'attachment' => if_empty($uploadedFile, null),
                    ]);
                }

                $this->statusHistory->create([
                    'id_reference' => $deliveryTrackingId,
                    'type' => StatusHistoryModel::TYPE_DELIVERY_TRACKING,
                    'status' => DeliveryTrackingModel::STATUS_ACTIVE,
                    'description' => 'Initial delivery status'
                ]);

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Tracking delivery {$noDeliveryTracking} successfully created", 'delivery-tracking');
                } else {
                    flash('danger', "Save delivery tracking {$noDeliveryTracking} failed");
                }
            }
        }
        $this->create();
    }

    /**
     * Show edit delivery tracking form.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_TRACKING_EDIT);

        $deliveryTracking = $this->deliveryTracking->getById($id);
        $customer = $this->people->getById(if_empty($deliveryTracking['id_customer'], $this->input->post('customer')));
        $users = $this->user->getByPermission(PERMISSION_DELIVERY_TRACKING_ADD_STATE);
        $employeeDepartment = UserModel::authenticatedUserData('id_department');
        if (empty($employeeDepartment) || AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_TRACKING_ASSIGNMENT)) {
            $contactGroups = $this->departmentContactGroup->getAll();
        } else {
            $contactGroups = $this->departmentContactGroup->getBy(['ref_department_contact_groups.id_department' => UserModel::authenticatedUserData('id_department')]);
        }

        $this->render('delivery_tracking/edit', compact('deliveryTracking', 'customer', 'users', 'contactGroups'));
    }

    /**
     * Update data delivery tracking by id.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_TRACKING_EDIT);

        if ($this->validate()) {
            $customerId = $this->input->post('customer');
            $userId = $this->input->post('user');
            $reminderType = $this->input->post('reminder_type');
            $contactGroup = $this->input->post('contact_group');
            $description = $this->input->post('description');

            $deliveryTracking = $this->deliveryTracking->getById($id);

            $update = $this->deliveryTracking->update([
                'id_customer' => $customerId,
                'id_user' => $userId,
                'reminder_type' => $reminderType,
                'id_department_contact_group' => if_empty($contactGroup, null),
                'description' => $description
            ], $id);

            if ($update) {
                flash('success', "Delivery tracking {$deliveryTracking['no_delivery_tracking']} successfully updated", 'delivery-tracking');
            } else {
                flash('danger', "Update delivery tracking {$deliveryTracking['no_delivery_tracking']} failed");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting delivery tracking data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_TRACKING_DELETE);

        $deliveryTracking = $this->deliveryTracking->getById($id);

        if ($this->deliveryTracking->delete($id)) {
            flash('warning', "Delivery tracking {$deliveryTracking['no_delivery_tracking']} successfully deleted");
        } else {
            flash('danger', "Delete delivery tracking {$deliveryTracking['no_delivery_tracking']} failed");
        }
        redirect('delivery-tracking');
    }

    /**
     * Show add delivery tracking form.
     *
     * @param $id
     */
    public function add_delivery_state($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_TRACKING_ADD_STATE);

        $deliveryTracking = $this->deliveryTracking->getById($id);
        $deliveryTrackingSafeConducts = $this->deliveryTrackingSafeConduct->getBy([
            'delivery_tracking_safe_conducts.id_delivery_tracking' => $id
        ]);
        foreach ($deliveryTrackingSafeConducts as &$safeConduct) {
            $safeConduct['goods'] = $this->safeConductGoods->getSafeConductGoodsBySafeConduct($safeConduct['id_safe_conduct']);
        }

        $this->render('delivery_tracking/add_delivery_state', compact('deliveryTracking', 'deliveryTrackingSafeConducts'));
    }

    /**
     * Save new delivery tracking detail.
     *
     * @param $deliveryTrackingId
     */
    public function save_delivery_state($deliveryTrackingId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_TRACKING_ADD_STATE);

        if ($this->validate(['status_date' => 'trim|required', 'tracking_message' => 'trim|required|max_length[500]'])) {
            $arrivalDate = $this->input->post('arrival_date');
            $arrivalDateType = $this->input->post('arrival_date_type');
            $unloadDate = $this->input->post('unload_date');
            $unloadDateType = $this->input->post('unload_date_type');
            $unloadLocation = $this->input->post('unload_location');
            $safeConductGoods = $this->input->post('safe_conduct_goods');
            $statusDate = $this->input->post('status_date');
            $trackingMessage = $this->input->post('tracking_message');
            $description = $this->input->post('description');

            $deliveryTracking = $this->deliveryTracking->getById($deliveryTrackingId);

            $uploadedPhoto = '';
            $uploadFile = true;
            if (!empty($_FILES['attachment']['name'])) {
                $uploadFile = $this->uploader->uploadTo('attachment', ['destination' => 'delivery-tracking/' . date('Y/m')]);
                if ($uploadFile) {
                    $uploadedData = $this->uploader->getUploadedData();
                    $uploadedPhoto = $uploadedData['uploaded_path'];
                    $this->imageEditor->watermark(FCPATH . 'uploads' . DIRECTORY_SEPARATOR . $uploadedPhoto);
                } else {
                    flash('danger', $this->uploader->getDisplayErrors());
                }
            }

            if ($uploadFile) {
                $this->db->trans_start();

                $this->deliveryTrackingDetail->create([
                    'id_delivery_tracking' => $deliveryTrackingId,
                    'arrival_date' => if_empty(format_date($arrivalDate, 'Y-m-d H:i'), null),
                    'arrival_date_type' => $arrivalDateType,
                    'unload_date' => if_empty(format_date($unloadDate, 'Y-m-d H:i'), null),
                    'unload_date_type' => $unloadDateType,
                    'unload_location' => $unloadLocation,
                    'tracking_message' => $trackingMessage,
                    'status_date' => format_date($statusDate, 'Y-m-d H:i'),
                    'attachment' => if_empty($uploadedPhoto, null),
                    'description' => $description,
                ]);
                $deliveryTrackingDetailId = $this->db->insert_id();

                foreach ($safeConductGoods as $safeConductItem) {
                    $this->deliveryTrackingGoods->create([
                        'id_delivery_tracking_detail' => $deliveryTrackingDetailId,
                        'id_safe_conduct_goods' => $safeConductItem['id_safe_conduct_goods'],
                        'quantity' => $safeConductItem['quantity'],
                        'description' => $safeConductItem['description']
                    ]);
                }

                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Tracking delivery {$deliveryTracking['no_delivery_tracking']} successfully added", 'delivery-tracking');
                } else {
                    flash('danger', "Save delivery tracking detail {$deliveryTracking['no_delivery_tracking']} failed");
                }
            }
        }
        $this->add_delivery_state($deliveryTrackingId);
    }

    /**
     * Show add assignment message form.
     *
     * @param $id
     */
    public function add_assignment_message($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_TRACKING_CREATE);

        $deliveryTracking = $this->deliveryTracking->getById($id);

        $this->render('delivery_tracking/add_assignment_message', compact('deliveryTracking'));
    }

    /**
     * Save new assignment message.
     *
     * @param $deliveryTrackingId
     */
    public function save_assignment_message($deliveryTrackingId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_TRACKING_CREATE);

        if ($this->validate(['assignment_message' => 'trim|required|max_length[500]'])) {
            $assignmentMessage = $this->input->post('assignment_message');

            $deliveryTracking = $this->deliveryTracking->getById($deliveryTrackingId);

            $uploadedFile = '';
            $uploadFile = true;
            if (!empty($_FILES['attachment']['name'])) {
                $uploadFile = $this->uploader->uploadTo('attachment', ['destination' => 'delivery-tracking/' . date('Y/m')]);
                if ($uploadFile) {
                    $uploadedData = $this->uploader->getUploadedData();
                    $uploadedFile = $uploadedData['uploaded_path'];
                } else {
                    flash('danger', $this->uploader->getDisplayErrors());
                }
            }

            if ($uploadFile) {
                $save = $this->deliveryTrackingAssignment->create([
                    'id_delivery_tracking' => $deliveryTrackingId,
                    'assignment_message' => $assignmentMessage,
                    'attachment' => if_empty($uploadedFile, null),
                ]);

                if ($save) {
                    flash('success', "Assignment message {$deliveryTracking['no_delivery_tracking']} successfully added", 'delivery-tracking');
                } else {
                    flash('danger', "Save assignment message {$deliveryTracking['no_delivery_tracking']} failed");
                }
            }
        }
        $this->add_assignment_message($deliveryTrackingId);
    }

    /**
     * Close delivery tracking.
     *
     * @param $id
     */
    public function close($id)
    {
        $message = $this->input->post('message');

        $deliveryTracking = $this->deliveryTracking->getById($id);
        $deliveryTrackingDetails = $this->deliveryTrackingDetail->getBy([
            'id_delivery_tracking' => $deliveryTracking['id'],
            'is_sent' => 0
        ]);

        if (!empty($deliveryTrackingDetails)) {
            $closeMessage = 'This delivery has some data that not been sent yet, you cannot close it now';
            flash('danger', $closeMessage, '_back', 'delivery-tracking');
        }

        $this->db->trans_start();

        $this->deliveryTracking->update([
            'status' => DeliveryTrackingModel::STATUS_DELIVERED
        ], $id);

        $this->statusHistory->create([
            'id_reference' => $id,
            'type' => StatusHistoryModel::TYPE_DELIVERY_TRACKING,
            'status' => DeliveryTrackingModel::STATUS_DELIVERED,
            'description' => if_empty($message, 'Delivery completed')
        ]);

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            flash('success', "Delivery tracking {$deliveryTracking['no_delivery_tracking']} is successfully closed");
        } else {
            flash('danger', "Close delivery failed, try again or contact administrator");
        }
        redirect('delivery-tracking');
    }

    /**
     * Show safe conduct outbound.
     * @param $id
     */
    public function add_safe_conduct($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_DELIVERY_TRACKING_ADD_STATE);

        $deliveryTracking = $this->deliveryTracking->getById($id);
        $deliveryTrackingSafeConducts = $this->deliveryTrackingSafeConduct->getBy([
            'id_delivery_tracking' => $id
        ]);

        $this->render('delivery_tracking/add_safe_conduct', compact('deliveryTracking', 'deliveryTrackingSafeConducts'));
    }

    /**
     * Show safe conduct outbound.
     * @param $id
     */
    public function save_safe_conduct($id)
    {
        $safeConducts = $this->input->post('safe_conducts');

        $deliveryTracking = $this->deliveryTracking->getById($id);

        $this->db->trans_start();

        $deliveryTrackingSafeConducts = $this->deliveryTrackingSafeConduct->getBy([
            'id_delivery_tracking' => $id
        ]);
        $existingSafeConductIds = array_column($deliveryTrackingSafeConducts, 'id_safe_conduct');

        // add if not exist
        foreach ($safeConducts as $safeConductId) {
            if (!in_array($safeConductId, $existingSafeConductIds)) {
                $this->deliveryTrackingSafeConduct->create([
                    'id_delivery_tracking' => $id,
                    'id_safe_conduct' => $safeConductId
                ]);
            }
        }

        // delete excluded
        foreach ($deliveryTrackingSafeConducts as $oldSafeConduct) {
            $mustDeleted = true;
            foreach ($safeConducts as $safeConductId) {
                if ($oldSafeConduct['id_safe_conduct'] == $safeConductId) {
                    $mustDeleted = false;
                    break;
                }
            }
            if ($mustDeleted) {
                $this->deliveryTrackingSafeConduct->delete($oldSafeConduct['id']);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            flash('success', "Delivery tracking {$deliveryTracking['no_delivery_tracking']} safe conduct is successfully updated");
        } else {
            flash('danger', "Update delivery failed, try again or contact administrator");
        }
        redirect('delivery-tracking');
    }

}