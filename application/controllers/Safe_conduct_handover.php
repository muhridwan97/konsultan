<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Safe_conduct_handover
 * @property SafeConductHandoverModel $safeConductHandover
 * @property SafeConductModel $safeConduct
 * @property StatusHistoryModel $statusHistory
 * @property Exporter $exporter
 */
class Safe_conduct_handover extends MY_Controller
{
    /**
     * Safe_conduct_handover constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('SafeConductHandoverModel', 'safeConductHandover');
        $this->load->model('SafeConductModel', 'safeConduct');
        $this->load->model('StatusHistoryModel', 'statusHistory');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'ajax_get_data' => 'GET',
        ]);
    }

    /**
     * Show handover data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_HANDOVER);

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Safe conduct handover", $this->safeConductHandover->getAll($_GET));
        } else {
            $this->render('safe_conduct_handover/index');
        }
    }

    /**
     * Get ajax paging data safe conduct handover.
     */
    public function ajax_get_data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_HANDOVER);

        $filters = array_merge(get_url_param('filter') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir'],
        ]);

        $data = $this->safeConductHandover->getAll($filters);

        $this->render_json($data);
    }

    /**
     * Show detail view.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_HANDOVER);

        $safeConductHandover = $this->safeConductHandover->getById($id);

        $this->render('safe_conduct_handover/view', compact('safeConductHandover'));
    }

    /**
     * Show form create safe conduct handover.
     *
     * @param null $safeConductId
     */
    public function create($safeConductId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_HANDOVER);

        $safeConductId = if_empty($safeConductId, $this->input->post('safe_conduct'));
        $safeConduct = $this->safeConduct->getSafeConductById($safeConductId);
        $safeConductHandover = $this->safeConductHandover->getBy(['id_safe_conduct' => $safeConductId], true);
        if (!empty($safeConductHandover) && $safeConductHandover['status'] == SafeConductHandoverModel::STATUS_HANDOVER) {
            flash('danger', "Safe conduct {$safeConduct['no_safe_conduct']} is already handover", '_back', 'safe-conduct-handover');
        }
        $safeConductGroups = empty($safeConduct['id_safe_conduct_group']) ? [] : $this->safeConduct->getBy(['id_safe_conduct_group' => $safeConduct['id_safe_conduct_group']]);

        $this->render('safe_conduct_handover/create', compact('safeConductHandover', 'safeConduct', 'safeConductGroups'));
    }

    /**
     * Save new tracking linked tep data.
     * @param $safeConductId
     */
    public function save($safeConductId)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_SAFE_CONDUCT_HANDOVER);

        if ($this->validate()) {
            $safeConductId = if_empty($safeConductId, $this->input->post('safe_conduct'));
            $receivedDate = $this->input->post('received_date');
            $driverHandoverDate = $this->input->post('driver_handover_date');
            $description = $this->input->post('description');

            $safeConduct = $this->safeConduct->getSafeConductById($safeConductId);

            $this->db->trans_start();

            $driverHandoverDate = format_date($driverHandoverDate, 'Y-m-d H:i:s');
            $data = [
                'id_safe_conduct' => $safeConductId,
                'received_date' => format_date($receivedDate, 'Y-m-d H:i:s'),
                'driver_handover_date' => if_empty($driverHandoverDate, null),
                'status' => empty($driverHandoverDate) ? SafeConductHandoverModel::STATUS_RECEIVED : SafeConductHandoverModel::STATUS_HANDOVER,
                'description' => $description,
            ];
            $this->safeConductHandover->create($data);

            // create or update safe conduct group member
            if (!empty($safeConduct['id_safe_conduct_group'])) {
                $safeConductGroups = $this->safeConduct->getBy([
                    'safe_conducts.id!=' => $safeConductId,
                    'id_safe_conduct_group' => $safeConduct['id_safe_conduct_group']
                ]);
                foreach ($safeConductGroups as $safeConduct) {
                    $safeConductHandover = $this->safeConductHandover->getBy(['id_safe_conduct' => $safeConduct['id']], true);
                    if (empty($safeConductHandover)) {
                        $data['id_safe_conduct'] = $safeConduct['id'];
                        $this->safeConductHandover->create($data);
                    } else {
                        $this->safeConductHandover->update($data, $safeConductHandover['id']);
                    }
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Safe conduct " . if_empty($safeConduct['no_safe_conduct_group'], $safeConduct['no_safe_conduct']) . " successfully handover", 'safe-conduct-handover');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->create($safeConductId);
    }

    /**
     * Show form edit safe conduct handover.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized([PERMISSION_SAFE_CONDUCT_EDIT, PERMISSION_SAFE_CONDUCT_HANDOVER]);

        $safeConductHandover = $this->safeConductHandover->getById($id);
        $safeConductGroups = empty($safeConductHandover['id_safe_conduct_group']) ? [] : $this->safeConduct->getBy(['id_safe_conduct_group' => $safeConductHandover['id_safe_conduct_group']]);

        $this->render('safe_conduct_handover/edit', compact('safeConductHandover', 'safeConductGroups'));
    }

    /**
     * Confirm site transit.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized([PERMISSION_SAFE_CONDUCT_EDIT, PERMISSION_SAFE_CONDUCT_HANDOVER]);

        if ($this->validate($this->_validation_rules($id))) {
            $receivedDate = $this->input->post('received_date');
            $driverHandoverDate = $this->input->post('driver_handover_date');
            $description = $this->input->post('description');

            $safeConductHandover = $this->safeConductHandover->getById($id);

            $this->db->trans_start();

            $driverHandoverDate = format_date($driverHandoverDate, 'Y-m-d H:i:s');
            $data = [
                'received_date' => format_date($receivedDate, 'Y-m-d H:i:s'),
                'driver_handover_date' => if_empty(format_date($driverHandoverDate, 'Y-m-d H:i:s'), null),
                'status' => $safeConductHandover['status'] != SafeConductHandoverModel::STATUS_HANDOVER && !empty($driverHandoverDate)
                    ? SafeConductHandoverModel::STATUS_HANDOVER
                    : $safeConductHandover['status'],
                'description' => $description,
            ];
            $this->safeConductHandover->update($data, $id);

            // update safe conduct group
            if (!empty($safeConductHandover['id_safe_conduct_group'])) {
                // get related safe conduct exept this one
                $safeConductGroups = $this->safeConduct->getBy([
                    'safe_conducts.id!=' => $safeConductHandover['id_safe_conduct'],
                    'id_safe_conduct_group' => $safeConductHandover['id_safe_conduct_group']
                ]);

                // loop through the safe conduct group member (except the current one)
                foreach ($safeConductGroups as $safeConduct) {
                    // check if it has handover already to decide if handover need to be created or updated
                    $safeConductHandover = $this->safeConductHandover->getBy(['id_safe_conduct' => $safeConduct['id']], true);
                    if (empty($safeConductHandover)) {
                        $data['id_safe_conduct'] = $safeConduct['id'];
                        $this->safeConductHandover->create($data);
                    } else {
                        $this->safeConductHandover->update($data, $safeConductHandover['id']);
                    }
                }
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Safe conduct " . if_empty($safeConductHandover['no_safe_conduct_group'], $safeConductHandover['no_safe_conduct']) . " successfully updated", 'safe-conduct-handover');
            } else {
                flash('danger', 'Something is getting wrong, try again or contact administrator');
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting delete data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized([PERMISSION_SAFE_CONDUCT_EDIT, PERMISSION_SAFE_CONDUCT_HANDOVER]);

        $safeConductHandover = $this->safeConductHandover->getById($id);

        $this->db->trans_start();

        $this->safeConductHandover->delete($id, true);

        if (!empty($safeConductHandover['id_safe_conduct_group'])) {
            $safeConductGroupHandovers = $this->safeConductHandover->getBy([
                'safe_conduct_handovers.id!=' => $id,
                'id_safe_conduct_group' => $safeConductHandover['id_safe_conduct_group']
            ]);
            foreach ($safeConductGroupHandovers as $safeConductGroupHandover) {
                $this->safeConductHandover->delete($safeConductGroupHandover['id'], true);
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            flash('warning', "Safe conduct handover {$safeConductHandover['no_safe_conduct']} is successfully deleted");
        } else {
            flash('danger', "Delete safe conduct handover {$safeConductHandover['no_safe_conduct']} failed");
        }
        redirect('safe-conduct-handover');
    }

    /**
     * @param mixed ...$params
     * @return array
     */
    protected function _validation_rules(...$params)
    {
        $id = $params[0] ?? 0;

        return [
            'safe_conduct' => [
                'required', 'integer', 'is_natural_no_zero', ['handover_exist', function ($safeConductId) use ($id) {
                    $safeConductHandover = $this->safeConductHandover->getBy([
                        'id_safe_conduct' => $safeConductId,
                        'safe_conduct_handovers.id!=' => $id
                    ]);
                    $this->form_validation->set_message('handover_exist', 'The %s has been exist');
                    return empty($safeConductHandover);
                }]
            ],
            'received_date' => 'required|max_length[30]',
            'driver_handover_date' => 'max_length[30]',
            'description' => 'trim|max_length[500]',
        ];
    }
}
