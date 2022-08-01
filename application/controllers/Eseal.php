<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Eseal
 * @property EsealModel $eseal
 * @property BranchModel $branch
 * @property Exporter $exporter
 */
class Eseal extends MY_Controller
{
    /**
     * Eseal constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('EsealModel', 'eseal');
        $this->load->model('BranchModel', 'branch');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show branches data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ESEAL_VIEW);

        $eseals = $this->eseal->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Eseals", $eseals);
        } else {
            $this->render('eseal/index', compact('eseals'));
        }
    }

    /**
     * Show detail eseal.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ESEAL_VIEW);

        $eseal = $this->eseal->getById($id);

        $this->render('eseal/view', compact('eseal'));
    }

    /**
     * Show create eseal form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BRANCH_CREATE);

        $branches = $this->branch->getAll();
        $devices = $this->eseal->getAllDevices(true);

        $this->render('eseal/create', compact('branches', 'devices'));
    }

    /**
     * Set validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'no_eseal' => 'trim|required|max_length[50]',
            'description' => 'trim|required|max_length[500]'
        ];
    }

    /**
     * Save new eseal.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ESEAL_CREATE);

        if ($this->validate()) {
            $branchId = $this->input->post('branch');
            $deviceId = $this->input->post('device');
            $noEseal = $this->input->post('no_eseal');
            $description = $this->input->post('description');

            $device = [];
            if(!empty($deviceId)) {
                $device = $this->eseal->getDeviceById($deviceId);
            }

            $save = $this->eseal->create([
                'id_branch' => if_empty($branchId, null),
                'id_device' => get_if_exist($device, 'id', null),
                'device_name' => get_if_exist($device, 'name', null),
                'no_eseal' => $noEseal,
                'description' => $description
            ]);

            if ($save) {
                flash('success', "E-seal {$noEseal} successfully created", 'eseal');
            } else {
                flash('danger', "Save E-seal {$noEseal} failed");
            }
        }
        $this->create();
    }

    /**
     * Show edit eseal form.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ESEAL_EDIT);

        $eseal = $this->eseal->getById($id);
        $branches = $this->branch->getAll();
        $devices = $this->eseal->getAllDevices(true, $eseal['id_device']);

        $this->render('eseal/edit', compact('eseal', 'branches', 'devices'));
    }

    /**
     * Update data eseal by id.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ESEAL_EDIT);

        if ($this->validate()) {
            $branchId = $this->input->post('branch');
            $deviceId = $this->input->post('device');
            $noEseal = $this->input->post('no_eseal');
            $description = $this->input->post('description');

            $device = [];
            if(!empty($deviceId)) {
                $device = $this->eseal->getDeviceById($deviceId);
            }

            $update = $this->eseal->update([
                'id_branch' => if_empty($branchId, null),
                'id_device' => get_if_exist($device, 'id', null),
                'device_name' => get_if_exist($device, 'name', null),
                'no_eseal' => $noEseal,
                'description' => $description
            ], $id);

            if ($update) {
                flash('success', "E-seal <strong>{$noEseal}</strong> successfully updated", 'eseal');
            } else {
                flash('danger', "Update E-seal <strong>{$noEseal}</strong> failed");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting eseal data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_ESEAL_DELETE);

        $eseal = $this->eseal->getById($id);

        if ($this->eseal->delete($id)) {
            flash('warning', "E-seal {$eseal['no_eseal']} successfully deleted");
        } else {
            flash('danger', "Delete E-seal {$eseal['no_eseal']} failed");
        }

        redirect('eseal');
    }

}
