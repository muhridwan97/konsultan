<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class HeavyEquipment
 * @property HeavyEquipmentModel $heavyEquipment
 * @property HeavyEquipmentBranchModel $heavyEquipmentBranch
 * @property BranchModel $branch
 * @property Exporter $exporter
 */
class Heavy_equipment extends MY_Controller
{
    /**
     * HeavyEquipment constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('HeavyEquipmentModel', 'heavyEquipment');
        $this->load->model('HeavyEquipmentBranchModel', 'heavyEquipmentBranch');
        $this->load->model('BranchModel', 'branch');
        $this->load->model('modules/Exporter', 'exporter');

        $this->setFilterMethods([
            'ajax_get_heavy_equipment' => 'POST',
        ]);
    }

    /**
     * Show heavy equipments data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HEAVY_EQUIPMENT_VIEW);

        $heavyEquipments = $this->heavyEquipment->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Heavy equipments", $heavyEquipments);
        } else {
            $this->render('heavy_equipment/index', compact('heavyEquipments'));
        }
    }

    /**
     * Show detail heavy equipment.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HEAVY_EQUIPMENT_VIEW);

        $heavyEquipments = $this->heavyEquipment->getById($id);
        $heavyEquipmentBranches = $this->heavyEquipmentBranch->getBy(['id_heavy_equipment' => $id]);

        $this->render('heavy_equipment/view', compact('heavyEquipments', 'heavyEquipmentBranches'));
    }

    /**
     * Show create heavy equipment form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HEAVY_EQUIPMENT_CREATE);

        $branches = $this->branch->getAll();

        $this->render('heavy_equipment/create', compact('branches'));
    }

    protected function _validation_rules()
    {
        return [
            'name' => 'trim|required|max_length[50]',
            'type' => 'trim|required|max_length[50]',
            'branches[]' => 'trim|required',
            'description' => 'trim|required|max_length[500]',
        ];
    }

    /**
     * Save new heavy equipment.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HEAVY_EQUIPMENT_CREATE);

        if ($this->validate()) {
            $name = $this->input->post('name');
            $type = $this->input->post('type');
            $branches = $this->input->post('branches') ?? [];
            $description = $this->input->post('description');

            $this->db->trans_start();

            $this->heavyEquipment->create([
                'name' => $name,
                'type' => $type,
                'description' => $description
            ]);
            $heavyEquipmentId = $this->db->insert_id();

            foreach ($branches as $branchId) {
                $this->heavyEquipmentBranch->create([
                    'id_heavy_equipment' => $heavyEquipmentId,
                    'id_branch' => $branchId,
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Heavy Equipment {$name} successfully created", 'heavy_equipment');
            } else {
                flash('danger', "Save Heavy Equipment {$name} failed");
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
        AuthorizationModel::mustAuthorized(PERMISSION_HEAVY_EQUIPMENT_EDIT);

        $heavyEquipment = $this->heavyEquipment->getById($id);
        $heavyEquipmentBranches = $this->heavyEquipmentBranch->getBy(['id_heavy_equipment' => $id]);
        $branches = $this->branch->getAll();

        $this->render('heavy_equipment/edit', compact('heavyEquipment', 'heavyEquipmentBranches', 'branches'));
    }

    /**
     * Update data heavy equipment by id.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_BRANCH_EDIT);

        if ($this->validate()) {
            $name = $this->input->post('name');
            $type = $this->input->post('type');
            $branches = $this->input->post('branches') ?? [];
            $description = $this->input->post('description');

            $this->db->trans_start();

            $this->heavyEquipment->update([
                'name' => $name,
                'type' => $type,
                'description' => $description
            ], $id);

            $this->heavyEquipmentBranch->delete(['id_heavy_equipment' => $id]);
            foreach ($branches as $branchId) {
                $this->heavyEquipmentBranch->create([
                    'id_heavy_equipment' => $id,
                    'id_branch' => $branchId,
                ]);
            }

            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Heavy Equipment {$name} successfully updated", 'heavy_equipment');
            } else {
                flash('danger', "Update Heavy Equipment {$name} failed");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting heavy equipment data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_HEAVY_EQUIPMENT_DELETE);

        $heavyEquipments = $this->heavyEquipment->getById($id);

        if ($this->heavyEquipment->delete($id)) {
            flash('warning', "Heavy Equipment {$heavyEquipments['name']} successfully deleted");
        } else {
            flash('danger', "Delete Heavy Equipment {$heavyEquipments['name']} failed");
        }

        redirect('heavy_equipment');
    }

    /**
     * Get ajax heavy equipment.
     */
    public function ajax_get_heavy_equipment(){
        $heavyEquipments = $this->heavyEquipment->getAll();
              
        header('Content-Type: application/json');
        echo json_encode($heavyEquipments);
    }

}