<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Transporter_entry_permit_chassis
 * @property TransporterEntryPermitChassisModel $transporterEntryPermitChassis
 * @property TransporterEntryPermitModel $transporterEntryPermit
 * @property Exporter $exporter
 */
class Transporter_entry_permit_chassis extends MY_Controller
{
    /**
     * Transporter_entry_permit_chassis constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('TransporterEntryPermitChassisModel', 'transporterEntryPermitChassis');
        $this->load->model('TransporterEntryPermitModel', 'transporterEntryPermit');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'ajax_get_data' => 'GET'
        ]);
    }

    /**
     * Show TEP chassis data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_VIEW);

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("TEP request hold", $this->transporterEntryPermitChassis->getAll($_GET));
        } else {
            $this->render('transporter_entry_permit_chassis/index', [], 'TEP Chassis');
        }
    }

    /**
     * Get ajax paging data TEP chassis.
     */
    public function ajax_get_data()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_VIEW);

        $filters = array_merge(get_url_param('filter') ? $_GET : [], [
            'start' => $this->input->get('start'),
            'length' => $this->input->get('length'),
            'search' => $this->input->get('search')['value'],
            'order_by' => $this->input->get('order')[0]['column'],
            'order_method' => $this->input->get('order')[0]['dir'],
        ]);

        $data = $this->transporterEntryPermitChassis->getAll($filters);

        $this->render_json($data);
    }

    /**
     * Show detail tep chassis handling.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TEP_VIEW);

        $tepChassis = $this->transporterEntryPermitChassis->getById($id);
        $tep = $this->transporterEntryPermit->getById($tepChassis['id_tep']);

        $this->render('transporter_entry_permit_chassis/view', compact('tepChassis', 'tep'), 'TEP Chassis');
    }

    /**
     * Show edit form TEP chassis.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized([PERMISSION_TEP_EDIT, PERMISSION_TEP_EDIT_SECURITY]);

        $tepChassis = $this->transporterEntryPermitChassis->getById($id);

        $this->render('transporter_entry_permit_chassis/edit', compact('tepChassis'), 'Edit TEP Chassis');
    }

    /**
     * Update tep chassis data.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized([PERMISSION_TEP_EDIT, PERMISSION_TEP_EDIT_SECURITY]);

        if ($this->validate(['no_chassis' => 'trim|required|max_length[100]'])) {
            $noChassis = $this->input->post('no_chassis');
            $description = $this->input->post('description');

            $update = $this->transporterEntryPermitChassis->update([
                'no_chassis' => $noChassis,
                'description' => if_empty($description, null),
            ], $id);

            if ($update) {
                $this->session->set_flashdata([
                    'status' => 'success',
                    'message' => "TEP Chassis <strong>{$noChassis}</strong> successfully updated",
                ]);
                redirect('transporter-entry-permit-chassis');
            } else {
                $this->session->set_flashdata([
                    'status' => 'danger',
                    'message' => "Update <strong>{$noChassis}</strong> failed, try again later",
                ]);
            }

            $this->edit($id);
        }
    }
}
