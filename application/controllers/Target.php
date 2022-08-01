<?php
defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * Class Target
 * @property TargetModel $target
 * @property TargetBranchModel $targetBranch
 * @property BranchModel $branch
 * @property BranchVmsModel $branchVms
 * @property Exporter $exporter
 */
class Target extends MY_Controller
{
    /**
     * Target constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('TargetModel', 'target');
        $this->load->model('TargetBranchModel', 'targetBranch');
        $this->load->model('BranchModel', 'branch');
        $this->load->model('BranchVmsModel', 'branchVms');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show list of targets.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TARGET_VIEW);

        $targets = $this->target->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Targets", $targets);
        } else {
            $this->render('target/index', compact('targets'));
        }
    }

    /**
     * View single target by id.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TARGET_VIEW);

        $target = $this->target->getById($id);
        $targetBranches = $this->targetBranch->getBy(['ref_target_branches.id_target'=>$target['id']]);
        $branches = $this->branch->getAll();

        $this->render('target/view', compact('target', 'branches', 'targetBranches'));
    }

    /**
     * Show create target form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TARGET_CREATE);

        $branches = $this->branch->getAll();

        $this->render('target/create', compact('branches'));
    }

    /**
     * Show edit form target.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TARGET_EDIT);

        $target = $this->target->getById($id);
        $targetBranches = $this->targetBranch->getBy(['ref_target_branches.id_target'=>$target['id']]);
        $branches = $this->branch->getAll();
        $branchVmses = $this->branchVms->getAll();

        $this->render('target/edit', compact('target', 'branches', 'targetBranches', 'branchVmses'));
    }

    /**
     * Set Target data validation.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'target' => 'trim|required|max_length[50]',
        ];
    }

    /**
     * Save data Target.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TARGET_CREATE);

        if ($this->input->server('REQUEST_METHOD') == "POST") {
            $targetName = $this->input->post('target_name');
            $target = $this->input->post('target');
            $description = $this->input->post('description');

            //target-branch
            $branches = $this->input->post('branches');
            $targetBranches = $this->input->post('target_branches');
            $descriptions = $this->input->post('descriptions');

            $this->db->trans_start();

            $this->target->create([
                'target_name' => $targetName,
                'target' => $target,
                'description' => $description,
            ]);
            $id = $this->db->insert_id();
            $componentData = [];
            for ($i = 0; $i < count($branches); $i++) {
                if (!empty($branches[$i])) {
                    $componentRecord['id_target'] = $id;
                    $componentRecord['id_branch'] = $branches[$i];
                    $componentRecord['target'] = $targetBranches[$i];
                    $componentRecord['description'] = $descriptions[$i];
                    $componentRecord['created_by'] = UserModel::authenticatedUserData('id');
                    $componentData[] = $componentRecord;
                }
            }
            if (count($branches)) {
                $this->targetBranch->insertTargetBranch($componentData);
            }
            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Target {$targetName} successfully created", 'target');
            } else {
                flash('danger', "Save Target {$targetName} failed");
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->create();
    }

    /**
     * Update Target data.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TARGET_EDIT);
        if ($this->input->server('REQUEST_METHOD') == "POST") {

            $targetName = $this->input->post('target_name');
            $target = $this->input->post('target');
            $description = $this->input->post('description');

            //target-branch
            $branches = $this->input->post('branches');
            $targetBranches = $this->input->post('target_branches');
            $descriptions = $this->input->post('descriptions');

            $this->db->trans_start();

            $this->target->update([
                'target_name' => $targetName,
                'target' => $target,
                'description' => $description,
            ], $id);
            $this->targetBranch->deleteTargetBranchByTargetId($id);
            $componentData = [];
            for ($i = 0; $i < count($branches); $i++) {
                if (!empty($branches[$i])) {
                    $componentRecord['id_target'] = $id;
                    $componentRecord['id_branch'] = $branches[$i];
                    $componentRecord['target'] = $targetBranches[$i];
                    $componentRecord['description'] = $descriptions[$i];
                    $componentRecord['created_by'] = UserModel::authenticatedUserData('id');
                    $componentData[] = $componentRecord;
                }
            }
            if (count($branches)) {
                $this->targetBranch->insertTargetBranch($componentData);
            }
            $this->db->trans_complete();

            if ($this->db->trans_status()) {
                flash('success', "Target {$targetName} successfully updated", 'target');
            } else {
                flash('danger', "Save Target {$targetName} failed");
            }
        } else {
            flash('danger', 'Only <strong>POST</strong> request allowed');
        }
        $this->edit($id);
    }

    /**
     * Perform deleting data Target.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_TARGET_DELETE);

        $target = $this->target->getById($id);

        if ($this->target->delete($id, true)) {
            flash('warning', "Target {$target['description']} successfully deleted");
        } else {
            flash('danger', "Delete Target {$target['description']} failed");
        }

        redirect('Target');
    }
}