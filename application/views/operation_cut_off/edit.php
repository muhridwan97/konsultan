<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Shift</h3>
    </div>
    <form action="<?= site_url('operation-cut-off/update/' . $operationCutOff['id']) ?>" role="form" method="post" class="need-validation" id="form-operation-cut-off">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>
            <input type="hidden" name="branch" id="branch" value="<?= $operationCutOff['id_branch'] ?>">
            <input type="hidden" name="shift" id="shift" value="<?= $operationCutOff['shift'] ?>">

            <div class="form-group">
                <label>Branch - Shift</label>
                <p class="form-control-static"><?= $operationCutOff['branch'] ?> - Shift <?= $operationCutOff['shift'] ?></p>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('start') == '' ?: 'has-error'; ?>">
                        <div class="input-group bootstrap-timepicker <?= form_error('start') == '' ?: 'has-error'; ?>" style="width: 100%">
                            <label for="start">Start</label>
                            <input type="text" class="form-control timepicker" id="start" name="start" placeholder="Start time" required readonly value="<?= set_value('start', format_date($operationCutOff['start'], 'H:i')) ?>">
                        </div>
                        <?= form_error('start', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('end') == '' ?: 'has-error'; ?>">
                        <div class="input-group bootstrap-timepicker <?= form_error('end') == '' ?: 'has-error'; ?>" style="width: 100%">
                            <label for="end">End</label>
                            <input type="text" class="form-control timepicker" id="end" name="end" placeholder="End time" required readonly value="<?= set_value('end', format_date($operationCutOff['end'], 'H:i')) ?>">
                        </div>
                        <?= form_error('end', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('status') == '' ?: 'has-error'; ?>">
                <label for="status">Status</label>
                <select class="form-control select2" name="status" id="status" data-placeholder="Select status" required>
                    <option value=""></option>
                    <option value="ACTIVE"<?= set_select('status', 'ACTIVE', $operationCutOff['status'] == 'ACTIVE') ?>>
                        ACTIVE
                    </option>
                    <option value="INACTIVE"<?= set_select('status', 'INACTIVE', $operationCutOff['status'] == 'INACTIVE') ?>>
                        INACTIVE
                    </option>
                </select>
                <?= form_error('status', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('is_send') == '' ?: 'has-error'; ?>">
                <label for="is_send">Send Notification</label>
                <div class="row is_send">
                    <div class="col-sm-3">
                        <input type="radio" name="is_send" id="is_send" value="1"
                            <?= set_checkbox('is_send', 1, $operationCutOff['is_send'] == 1) ?>> Yes
                    </div>
                    <div class="col-sm-3">
                        <input type="radio" name="is_send" value="0" 
                            <?= set_checkbox('is_send', 0, $operationCutOff['is_send'] == 0) ?>> No
                    </div>
                </div>
                <?= form_error('is_send', '<span class="help-block">', '</span>'); ?>
                <span class="help-block">Choose yes when you want to send reminder this shift. Default : Yes</span>
            </div>

            <div class="form-group <?= form_error('cut_off') == '' ?: 'has-error'; ?>">
                <label for="cut_off">Join Cut Off</label>
                <select class="form-control select2" name="cut_off[]" id="cut_off" data-placeholder="Select cut off" multiple>
                    <option value=""></option>
                    <?php foreach ($operationCutOffAll as $key => $cut_off) : ?>
                    <option value="<?= $cut_off['id'] ?>"<?= set_select('cut_off', $cut_off['id'], array_search($cut_off['id'], array_column($operationCutOffJoin, 'id_operation_cut_off')) !== false) ?>>
                        <?= $cut_off['branch'] ?> ( <?= $cut_off['start'] ?> - <?= $cut_off['end'] ?> ) shift <?= $cut_off['shift'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('cut_off', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('attendance_device') == '' ?: 'has-error'; ?>">
                <label for="attendance_device">Cloud Id</label>
                <select class="form-control select2" name="attendance_device" id="attendance_device" data-placeholder="Select attendance device">
                    <option value=""></option>
                    <?php foreach ($attendanceDevice as $key => $attendance_device) : ?>
                    <option value="<?= $attendance_device['cloud_id'] ?>"<?= set_select('attendance_device', $attendance_device['cloud_id'], $attendance_device['cloud_id'] == $operationCutOff['cloud_id'] ) ?>>
                        <?= $attendance_device['device_name'] ?> ( <?= $attendance_device['cloud_id'] ?> )
                    </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('attendance_device', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('whatsapp_group') == '' ?: 'has-error'; ?>">
                <label for="whatsapp_group">Whatsapp Group</label>
                <input type="text" class="form-control" id="whatsapp_group" name="whatsapp_group"
                        placeholder="Enter whatsapp group number"
                        value="<?= set_value('whatsapp_group', $operationCutOff['whatsapp_group']) ?>">
                <?= form_error('whatsapp_group', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('schedule') == '' ?: 'has-error'; ?>">
                <label for="schedule">Schedule</label>
                <select class="form-control select2" name="schedule[]" id="schedule" data-placeholder="Select schedule" multiple>
                    <option value=""></option>
                    <?php foreach ($schedules as $key => $schedule) : ?>
                    <option value="<?= $schedule['id'] ?>"<?= set_select('schedule', $schedule['id'], array_search($schedule['id'], array_column($operationCutOffSchedule, 'id_schedule')) !== false) ?>>
                        <?= $schedule['schedule_name'] ?> ( <?= $schedule['start'] ?> - <?= $schedule['end'] ?> ) <?= $schedule['schedule_code'] ?>
                    </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('schedule', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Item Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Shift description"
                          maxlength="500"><?= set_value('description', $operationCutOff['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Update Operation Shift</button>
        </div>
    </form>
</div>