<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Vehicle
 * @property VehicleModel $vehicle
 * @property Exporter $exporter
 */
class Vehicle extends MY_Controller
{
    /**
     * Vehicle constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BranchModel', 'branch');
        $this->load->model('VehicleModel', 'vehicle');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show vehicles data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_VEHICLE_VIEW);

        $vehicles = $this->vehicle->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Vehicles", $vehicles);
        } else {
            $this->render('vehicle/index', compact('vehicles'));
        }
    }

    /**
     * Show detail branch.
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_VEHICLE_VIEW);

        $vehicle = $this->vehicle->getById($id);

        $this->render('vehicle/view', compact('vehicle'));
    }

    /**
     * Show create vehicle form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_VEHICLE_CREATE);

        $branches = $this->branch->getAll();

        $this->render('vehicle/create', compact('branches'));
    }

    protected function _validation_rules()
    {
        return [
            'vehicle_name' => 'trim|required|max_length[50]',
            'vehicle_type' => 'trim|required|max_length[50]',
            'no_plate' => 'trim|required|max_length[50]',
            'description' => 'trim|required|max_length[500]',
        ];
    }

    /**
     * Save new vehicle.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_VEHICLE_CREATE);

        if ($this->validate()) {
            $vehicleName = $this->input->post('vehicle_name');
            $vehicleType = $this->input->post('vehicle_type');
            $noPlate = $this->input->post('no_plate');
            $id_branch = $this->input->post('branch');
            $status = $this->input->post('status');
            $description = $this->input->post('description');

            $save = $this->vehicle->create([
                'id_branch' => $id_branch,
                'vehicle_name' => $vehicleName,
                'vehicle_type' => $vehicleType,
                'no_plate' => $noPlate,
                'status' => $status,
                'description' => $description
            ]);

            if ($save) {
                flash('success', "Vehicle {$vehicleName} successfully created", 'vehicle');
            } else {
                flash('danger', "Save vehicle {$vehicleName} failed");
            }
        }
        $this->create();
    }

    /**
     * Show edit branch form.
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_VEHICLE_EDIT);

        $vehicle = $this->vehicle->getById($id);
        $branches = $this->branch->getAll();

        $this->render('vehicle/edit', compact('vehicle', 'branches'));
    }

    /**
     * Update data vehicle by id.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BRANCH_EDIT);

        if ($this->validate()) {
            $vehicleName = $this->input->post('vehicle_name');
            $vehicleType = $this->input->post('vehicle_type');
            $noPlate = $this->input->post('no_plate');
            $id_branch = $this->input->post('branch');
            $status = $this->input->post('status');
            $description = $this->input->post('description');

            $update = $this->vehicle->update([
                'id_branch' => $id_branch,
                'vehicle_name' => $vehicleName,
                'vehicle_type' => $vehicleType,
                'no_plate' => $noPlate,
                'status' => $status,
                'description' => $description
            ], $id);

            if ($update) {
                flash('success', "Vehicle {$vehicleName} successfully updated", 'vehicle');
            } else {
                flash('danger', "Update vehicle {$vehicleName} failed");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting vehicle data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_VEHICLE_DELETE);

        $vehicle = $this->vehicle->getById($id);

        if ($this->vehicle->delete($id)) {
            flash('warning', "Vehicle {$vehicle['vehicle_name']} successfully deleted");
        } else {
            flash('danger', "Delete vehicle {$vehicle['vehicle_name']} failed");
        }

        redirect('vehicle');
    }

}