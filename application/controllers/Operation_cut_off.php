<?php

use Carbon\Carbon;

defined('BASEPATH') or exit('No direct script access allowed');

/**
 * Class Operation_cut_off
 * @property OperationCutOffModel $operationCutOff
 * @property OperationCutOffScheduleModel $operationCutOffSchedule
 * @property ScheduleModel $schedule
 * @property BranchModel $branch
 * @property AttendanceDeviceModel $attendanceDevice
 * @property OperationCutOffJoinModel $operationCutOffJoin
 * @property Exporter $exporter
 */
class Operation_cut_off extends MY_Controller
{
    /**
     * Operation_cut_off constructor.
     */
    public function __construct()
    {
        parent::__construct();

        $this->load->model('BranchModel', 'branch');
        $this->load->model('OperationCutOffModel', 'operationCutOff');
        $this->load->model('OperationCutOffScheduleModel', 'operationCutOffSchedule');
        $this->load->model('ScheduleModel', 'schedule');
        $this->load->model('AttendanceDeviceModel', 'attendanceDevice');
        $this->load->model('OperationCutOffJoinModel', 'operationCutOffJoin');
        $this->load->model('modules/Exporter', 'exporter');
    }

    /**
     * Show operation cut off data list.
     */
    public function index()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPERATION_CUT_OFF_VIEW);

        $operationCutOffs = $this->operationCutOff->getAll();

        if (get_url_param('export')) {
            $this->exporter->exportFromArray("Operation Cut Off", $operationCutOffs);
        } else {
            $this->render('operation_cut_off/index', compact('operationCutOffs'));
        }
    }

    /**
     * Show operation cut off data.
     *
     * @param $id
     */
    public function view($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPERATION_CUT_OFF_VIEW);

        $operationCutOff = $this->operationCutOff->getById($id);

        $this->render('operation_cut_off/view', compact('operationCutOff'));
    }

    /**
     * Show create item form.
     */
    public function create()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPERATION_CUT_OFF_CREATE);

        $branches = $this->branch->getAll();

        foreach ($branches as &$branch) {
            $branchCutOff = $this->operationCutOff->getLastShiftBranch($branch['id']);
            $branch['next_shift'] = ($branchCutOff['shift'] ?? 0) + 1;
            $branch['next_start'] = empty($branchCutOff['end']) ? '' : (Carbon::parse($branchCutOff['end'])->addMinutes(1)->format('H:i'));
        }

        $this->render('operation_cut_off/create', compact('branches'));
    }

    /**
     * Get base validation rules.
     *
     * @param array $params
     * @return array
     */
    protected function _validation_rules(...$params)
    {
        $id = isset($params[0]) ? $params[0] : 0;
        return [
            'branch' => empty($id) ? 'required' : '',
            'shift' => [
                'required', 'max_length[20]', ['shift_exists', function ($shift) use ($id) {
                    $this->form_validation->set_message('shift_exists', 'The %s is already exist, try another');
                    return empty($this->operationCutOff->getBy([
                        'ref_operation_cut_offs.id_branch' => $this->input->post('branch'),
                        'ref_operation_cut_offs.shift' => $shift,
                        'ref_operation_cut_offs.id!=' => $id
                    ]));
                }]
            ],
            'start' => 'required',
            'end' => [
                'required', ['min_end', function ($end) use ($id) {
                    $this->form_validation->set_message('min_end', 'The %s cannot bellow the start time or larger than start shift 1');

                    $start = $this->input->post('start');
                    $endLargerThanStart = $end > $this->input->post('start');
                    $shift1 = $this->operationCutOff->getBy([
                        'ref_operation_cut_offs.id_branch' => $this->input->post('branch'),
                        'ref_operation_cut_offs.shift' => 1,
                        'ref_operation_cut_offs.id!=' => $id
                    ], true);
                    if (!empty($shift1)) {
                        $isShift1 = false;
                        $endBelowShift1 = $end < $shift1['start'];
                        $endBelowShift1 = $start > $shift1['start'] ? ($endLargerThanStart || $endBelowShift1) : ($endBelowShift1 && ($start < $shift1['start'] && $endLargerThanStart));
                    } else {
                        $isShift1 = true;
                        $endBelowShift1 = true;
                    }

                    return $endBelowShift1 || $isShift1;
                }]
            ],
        ];
    }

    /**
     * Save new item.
     */
    public function save()
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPERATION_CUT_OFF_CREATE);

        if ($this->validate()) {
            $branchId = $this->input->post('branch');
            $shift = $this->input->post('shift');
            $start = $this->input->post('start');
            $end = $this->input->post('end');
            $status = $this->input->post('status');
            $description = $this->input->post('description');

            $save = $this->operationCutOff->create([
                'id_branch' => $branchId,
                'shift' => $shift,
                'start' => $start,
                'end' => $end,
                'status' => $status,
                'description' => if_empty($description, null)
            ]);

            if ($save) {
                flash('success', "Operation cut off shift {$shift} successfully created", 'operation-cut-off');
            } else {
                flash('danger', "Save operation cut off failed");
            }
        }
        $this->create();
    }

    /**
     * Show edit item form.
     *
     * @param $id
     */
    public function edit($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPERATION_CUT_OFF_EDIT);

        $operationCutOff = $this->operationCutOff->getById($id);
        $operationCutOffSchedule = $this->operationCutOffSchedule->getBy(['ref_operation_cut_off_schedules.id_operation_cut_off' => $id]);
        $schedules = $this->schedule->getAll();
        $operationCutOffAll = $this->operationCutOff->getWithoutThisId($id);
        $operationCutOffJoin = $this->operationCutOffJoin->getBy([
            'no_group' => $operationCutOff['no_group']
        ]);
        $attendanceDevice = $this->attendanceDevice->getAll();
        // print_debug($operationCutOff);

        $this->render('operation_cut_off/edit', compact('operationCutOff', 'operationCutOffSchedule', 'schedules', 'operationCutOffAll', 'attendanceDevice', 'operationCutOffJoin'));
    }

    /**
     * Update operation cut off data.
     *
     * @param $id
     */
    public function update($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPERATION_CUT_OFF_EDIT);

        if ($this->validate($this->_validation_rules($id))) {
            $start = $this->input->post('start');
            $end = $this->input->post('end');
            $status = $this->input->post('status');
            $description = $this->input->post('description');
            $schedules = $this->input->post('schedule');
            $isSend = $this->input->post('is_send');
            $cutOffJoins = $this->input->post('cut_off');
            $attendanceDevice = $this->input->post('attendance_device');
            $whatsappGroup = $this->input->post('whatsapp_group');

            $cutOffBefore = $this->operationCutOff->getById($id);

            $update = $this->operationCutOff->update([
                'start' => $start,
                'end' => $end,
                'status' => $status,
                'is_send' => $isSend,
                'cloud_id' => $attendanceDevice,
                'whatsapp_group' => $whatsappGroup,
                'description' => if_empty($description, null)
            ], $id);

            $this->operationCutOffJoin->deleteByNoGroup($cutOffBefore['no_group']);
            $noGroup = $this->operationCutOffJoin->getAutoNumber();
            $this->operationCutOffSchedule->deleteByOperationCutOff($id);
            $dataSchedule = [];
            foreach ($schedules as $key => $id_schedule) {
                $dataSchedule[] = [
                    'id_operation_cut_off' => $id,
                    'id_schedule' => $id_schedule,
                ];
            }
            $this->operationCutOffSchedule->create($dataSchedule);

            $dataJoint[] = [
                'id_operation_cut_off' => $id,
                'no_group' => $noGroup,
            ];
            foreach ($cutOffJoins as $key => $id_cut_off) {
                $dataJoint[] = [
                    'id_operation_cut_off' => $id_cut_off,
                    'no_group' => $noGroup,
                ];
            }
            $this->operationCutOffJoin->create($dataJoint);

            if ($update) {
                flash('success', "Operation cut off successfully updated", 'operation-cut-off');
            } else {
                flash('danger', "Update operation cut off failed");
            }
        }
        $this->edit($id);
    }

    /**
     * Perform deleting item data.
     *
     * @param $id
     */
    public function delete($id)
    {
        AuthorizationModel::mustAuthorized(PERMISSION_OPERATION_CUT_OFF_DELETE);

        $operationCutOff = $this->operationCutOff->getById($id);

        $this->db->trans_start();

        $this->operationCutOff->delete($id);

        if ($operationCutOff['shift'] == 1) {
            // move other data in the branch down
            $otherCutOffs = $this->operationCutOff->getBy([
                'ref_operation_cut_offs.id_branch' => $operationCutOff['id_branch']
            ]);
            foreach ($otherCutOffs as $otherCutOff) {
                $this->operationCutOff->update([
                    'shift' => $otherCutOff['shift'] - 1
                ], $otherCutOff['id']);
            }
        } else {
            // merge start shift ahead to match end of behind shift
            $aheadCutOffs = $this->operationCutOff->getBy([
                'ref_operation_cut_offs.id_branch' => $operationCutOff['id_branch'],
                'ref_operation_cut_offs.shift>' => $operationCutOff['shift'],
            ]);
            if (!empty($aheadCutOffs)) {
                $behindCutOff = $this->operationCutOff->getBy([
                    'ref_operation_cut_offs.id_branch' => $operationCutOff['id_branch'],
                    'ref_operation_cut_offs.shift' => $operationCutOff['shift'] - 1,
                ], true);
                if (!empty($behindCutOff)) {
                    foreach ($aheadCutOffs as $index => $aheadCutOff) {
                        $newStartFromBehind = Carbon::parse($behindCutOff['end'])->addMinutes(1)->format('H:i');
                        $this->operationCutOff->update([
                            'shift' => $aheadCutOff['shift'] - 1,
                            'start' => ($index == 0 ? $newStartFromBehind : $aheadCutOff['start'])
                        ], $aheadCutOff['id']);
                    }
                } else {
                    // deleted data should shift 1 (already handled above)
                }
            } else {
                // deleted data should be latest shift (do nothing)
            }
        }

        $this->db->trans_complete();

        if ($this->db->trans_status()) {
            if ($operationCutOff['shift'] == 1) {
                flash('warning', "Deleting shift 1 will move one level down other data");
            } else {
                flash('warning', "Operation cut off successfully deleted");
            }
        } else {
            flash('danger', "Delete operation cut off failed");
        }
        redirect('operation-cut-off');
    }
}
