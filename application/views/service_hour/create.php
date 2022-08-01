<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create Service Hour</h3>
    </div>

    <form action="<?= site_url('service-hour/save') ?>" role="form" method="post" id="form-service-hour">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <?php if($this->config->item('enable_branch_mode')): ?>
                <input type="hidden" name="branch" id="branch" value="<?= get_active_branch('id') ?>">
            <?php else: ?>
                <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                    <label for="branch">Branch</label>
                    <select class="form-control select2" name="branch" id="branch" data-placeholder="Select branch" required>
                        <option value=""></option>
                        <?php foreach (get_customer_branch() as $branch): ?>
                            <option value="<?= $branch['id'] ?>"<?= set_select('branch', $branch['id'], $branch['id'] == get_active_branch('id')) ?>>
                                <?= $branch['branch'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
                </div>
            <?php endif; ?>

            <div class="form-group <?= form_error('service_day') == '' ?: 'has-error'; ?>">
                <label for="service_day">Service Day</label>
                <select class="form-control select2" name="service_day" id="service_day" data-placeholder="Select day" required>
                    <option value=""></option>
                    <option value="SUNDAY"<?= set_select('service_day', 'SUNDAY') ?>>SUNDAY</option>
                    <option value="MONDAY"<?= set_select('service_day', 'MONDAY') ?>>MONDAY</option>
                    <option value="TUESDAY"<?= set_select('service_day', 'TUESDAY') ?>>TUESDAY</option>
                    <option value="WEDNESDAY"<?= set_select('service_day', 'WEDNESDAY') ?>>WEDNESDAY</option>
                    <option value="THURSDAY"<?= set_select('service_day', 'THURSDAY') ?>>THURSDAY</option>
                    <option value="FRIDAY"<?= set_select('service_day', 'FRIDAY') ?>>FRIDAY</option>
                    <option value="SATURDAY"<?= set_select('service_day', 'SATURDAY') ?>>SATURDAY</option>
                </select>
                <?= form_error('service_day', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group <?= form_error('service_time_start') == '' ?: 'has-error'; ?>">
                        <label for="service_time_start">Start Hour</label>
                        <div class="input-group bootstrap-timepicker <?= form_error('service_time_start') == '' ?: 'has-error'; ?>">
                            <input type="text" class="form-control time-picker" id="service_time_start" name="service_time_start"
                                   placeholder="Service start" value="<?= set_value('service_time_start', '08:00') ?>">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button"><i class="fa fa-clock-o"></i></button>
                            </span>
                        </div>
                        <?= form_error('service_time_start', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group <?= form_error('service_time_end') == '' ?: 'has-error'; ?>">
                        <label for="service_time_end">End Hour</label>
                        <div class="input-group bootstrap-timepicker <?= form_error('service_time_end') == '' ?: 'has-error'; ?>">
                            <input type="text" class="form-control time-picker" id="service_time_end" name="service_time_end"
                                   placeholder="Service end" value="<?= set_value('service_time_end', '16:00') ?>">
                            <span class="input-group-btn">
                                <button class="btn btn-default" type="button"><i class="fa fa-clock-o"></i></button>
                            </span>
                        </div>
                        <?= form_error('service_time_end', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('effective_date') == '' ?: 'has-error'; ?>">
                <label for="effective_date" class="control-label">Effective Date</label>
                <input type="text" class="form-control datepicker" name="effective_date" id="effective_date"
                       value="<?= set_value('effective_date') ?>" placeholder="Effective date of service">
                <?= form_error('effective_date', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Service hour description"
                          maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Save Service Hour
            </button>
        </div>
    </form>
</div>