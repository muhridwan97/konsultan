<?php
defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Overtime
 * @property OvertimeModel $overtime
 * @property Exporter $exporter
 */
class Overtime extends MY_Controller
{
    /**
     * Overtime constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('OvertimeModel', 'overtime');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show overtime data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OVERTIME_VIEW);

        $overtimes = $this->overtime->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Overtimes", $this->overtime->getAll());
        } else {
            $this->render('overtime/index', compact('overtimes'));
        }
    }


    /**
     * Show detail overtime.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OVERTIME_VIEW);

        $overtime = $this->overtime->getById($id);

        $this->render('overtime/view', compact('overtime'));
    }

    /**
     * Show create overtime form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OVERTIME_CREATE);

        $this->render('overtime/create');
    }

    /**
     * Set validation rules.
     *
     * @return array
     */
    protected function _validation_rules()
    {
        return [
            'first_overtime' => 'trim|required',
            'second_overtime' => 'trim|required',
            'description' => 'trim|max_length[500]'
        ];
    }


    /**
     * Save new overtime.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_POSITION_CREATE);

        $nameOfDay = $this->input->post('name_of_day');
        $description = $this->input->post('description');
        $firstOvertime = strtotime($this->input->post('first_overtime'));
        $secondOvertime = strtotime($this->input->post('second_overtime'));
        $branchId = get_active_branch('id');
        $getDayOvertime = $this->overtime->getDayOvertime($nameOfDay, $branchId);

        if ($firstOvertime <= $secondOvertime) {
            if (is_null($getDayOvertime)) {
                $this->db->trans_start();

                $noOvertime = $this->overtime->getAutoNumberOvertime();
                $this->overtime->create([
                    'id_branch' => $branchId,
                    'no_overtime' => $noOvertime,
                    'name_of_day' => $nameOfDay,
                    'description' => $description,
                    'first_overtime' => $this->input->post('first_overtime'),
                    'second_overtime' => $this->input->post('second_overtime'),
                ]);

                $this->db->trans_complete();


                if ($this->db->trans_status()) {
                    flash('success', "Overtime {$nameOfDay} successfully created", 'overtime');
                } else {
                    flash('danger', "Create overtime {$nameOfDay} failed");
                }

            } else {
                flash('danger', "Overtime {$nameOfDay} is already exists", 'overtime');
            }

        } else {
            flash('danger', "First time must not be smaller than second time", 'overtime');
        }
    }

    /**
     * Show overtime edit form.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OVERTIME_EDIT);

        $overtime = $this->overtime->getById($id);

        $this->render('overtime/edit', compact('overtime'));
    }

    /**
     * Update data overtime by id.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_POSITION_EDIT);

        if ($this->validate()) {
            $description = $this->input->post('description');
            $nameOfDay = $this->input->post('overtimeDay');

            $firstOvertime = strtotime($this->input->post('first_overtime'));
            $secondOvertime = strtotime($this->input->post('second_overtime'));

            if ($firstOvertime <= $secondOvertime) {
                $this->db->trans_start();

                $this->overtime->update([
                    'description' => $description,
                    'first_overtime' => $this->input->post('first_overtime'),
                    'second_overtime' => $this->input->post('second_overtime'),
                ], $id);
                $this->db->trans_complete();

                if ($this->db->trans_status()) {
                    flash('success', "Overtime {$nameOfDay} successfully updated", 'overtime');
                } else {
                    flash('danger', "Update overtime {$nameOfDay} failed");
                }
            } else {
                flash('danger', "Update overtime {$nameOfDay} failed, First time must not be smaller than second time", 'overtime');
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting overtime data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_POSITION_DELETE);

        $overtime = $this->overtime->getById($id);

        if ($this->overtime->delete($id, true)) {
            flash('warning', "Overtime {$overtime['name_of_day']} successfully deleted");
        } else {
            flash('danger', "Delete overtime {$overtime['name_of_day']} failed");
        }
        redirect('overtime');
    }

}