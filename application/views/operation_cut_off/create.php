<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create New Shift</h3>
    </div>
    <form action="<?= site_url('operation-cut-off/save') ?>" role="form" method="post" class="need-validation" id="form-operation-cut-off">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>
            <input type="hidden" name="status" value="<?= OperationCutOffModel::STATUS_ACTIVE ?>">

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                        <label for="branch">Branch</label>
                        <select class="form-control select2" name="branch" id="branch" data-placeholder="Select branch" required>
                            <option value=""></option>
                            <?php foreach($branches as $branch): ?>
                                <option value="<?= $branch['id'] ?>"
                                        data-next-shift="<?= $branch['next_shift'] ?>"
                                        data-next-start="<?= $branch['next_start'] ?>"
                                    <?= set_select('branch', $branch['id']) ?>>
                                    <?= $branch['branch'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('shift') == '' ?: 'has-error'; ?>">
                        <label for="shift">Shift</label>
                        <input type="number" class="form-control" id="shift" name="shift"
                               placeholder="Enter next shift"
                               required min="1" max="5" value="<?= set_value('shift') ?>">
                        <?= form_error('shift', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('start') == '' ?: 'has-error'; ?>">
                        <div class="input-group bootstrap-timepicker <?= form_error('start') == '' ?: 'has-error'; ?>" style="width: 100%">
                            <label for="start">Start</label>
                            <input type="text" class="form-control timepicker" id="start" name="start" placeholder="Start time" required value="<?= set_value('start') ?>">
                        </div>
                        <?= form_error('start', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('end') == '' ?: 'has-error'; ?>">
                        <div class="input-group bootstrap-timepicker <?= form_error('end') == '' ?: 'has-error'; ?>" style="width: 100%">
                            <label for="end">End</label>
                            <input type="text" class="form-control timepicker" id="end" name="end" placeholder="End time" required value="<?= set_value('end') ?>">
                        </div>
                        <?= form_error('end', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Item Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Shift description"
                          maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Save Operation Shift</button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/operation-cut-off.js') ?>" defer></script>