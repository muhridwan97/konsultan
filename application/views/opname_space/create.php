<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create Opname Space Job</h3>
    </div>
    <form action="<?= site_url('opname-space/save') ?>" role="form" method="post">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('opname_space_date') == '' ?: 'has-error'; ?>">
                <label for="opname_space_date">Opname Space Date</label>
                <input type="text" class="form-control datepicker" id="opname_space_date" name="opname_space_date"
                       placeholder="Enter opname space request date" autocomplete="off"
                       required maxlength="50" value="<?= set_value('opname_space_date') ?>">
                <?= form_error('opname_space_date', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Opname Space Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="opname space description"
                          required maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
            <p><i class="fa fa-info-circle mr10"></i> Current stock will be automatically captured</p>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Save Opname Space Job</button>
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