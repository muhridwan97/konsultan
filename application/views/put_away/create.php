<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create Put Away Audit</h3>
    </div>
    <form action="<?= site_url('put-away/save') ?>" role="form" method="post">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('put_away_date') == '' ?: 'has-error'; ?>">
                <label for="put_away_date">Put Away Date</label>
                <input type="text" class="form-control datepicker" id="put_away_date" name="put_away_date"
                       placeholder="Enter put away request date"
                       required maxlength="50" value="<?= set_value('put_away_date') ?>">
                <?= form_error('put_away_date', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('put_away_shift') == '' ?: 'has-error'; ?>">
                <label for="put_away_shift">Put Away Shift</label>
                <select class="form-control select2" required name="shift" id="put_away_shift"
                        data-placeholder="Select put away shift" style="width: 100%">
                    <option value=""></option>
                    <?php foreach ($operationCutOffs as $key => $cutOff): ?>
                        <option value="<?= $cutOff['id'] ?>"><?= $cutOff['shift'] ?> (<?=format_date($cutOff['start'],'H:i')?> - <?=format_date($cutOff['end'],'H:i')?>)</option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('put_away_shift', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Put Away Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Put Away description"
                          required maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
            <p><i class="fa fa-info-circle mr10"></i> Job Inbound on shift period will be automatically captured</p>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Save Put Away Audit</button>
        </div>
    </form>
</div>

<script type="text/javascript">
$(document).ready(function () {
    var today = new Date();
    $('.datepicker').datepicker({
        format: 'yyyy-mm-dd',
        autoclose:true,
        endDate: new Date(new Date().setDate(new Date().getDate())),
        maxDate: new Date(new Date().setDate(new Date().getDate()))
    }).on('changeDate', function (ev) {
            $(this).datepicker('hide');
        });

    $('.datepicker').keyup(function () {
        if (this.value.match(/[^0-9]/g)) {
            this.value = this.value.replace(/[^0-9^-]/g, '');
        }
    });
});
</script>