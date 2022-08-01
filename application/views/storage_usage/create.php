<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create Storage Usage</h3>
    </div>
    <form action="<?= site_url('storage-usage/save') ?>" role="form" method="post">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('date') == '' ?: 'has-error'; ?>">
                <label for="date">Captured Date</label>
                <input type="text" class="form-control datepicker-max-today" id="date" name="date"
                       placeholder="Enter storage date"
                       required maxlength="50" value="<?= set_value('date') ?>">
                <?= form_error('date', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Storage usage description"
                          required maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
            <p><i class="fa fa-info-circle mr10"></i> Current space usage will be automatically captured</p>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Save Storage Usage</button>
        </div>
    </form>
</div>

<script type="text/javascript">
    $(document).ready(function () {
        var today = new Date();
        $('.datepicker-max-today').datepicker({
            format: 'yyyy-mm-dd',
            autoclose: true,
            endDate: new Date(new Date().setDate(new Date().getDate())),
            maxDate: new Date(new Date().setDate(new Date().getDate()))
        }).on('changeDate', function (ev) {
            $(this).datepicker('hide');
        });

        $('.datepicker-max-today').keyup(function () {
            if (this.value.match(/[^0-9]/g)) {
                this.value = this.value.replace(/[^0-9^-]/g, '');
            }
        });
    });
</script>
