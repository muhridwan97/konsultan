<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create Opname Job</h3>
    </div>
    <form action="<?= site_url('opname/save') ?>" role="form" method="post">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('opname_date') == '' ?: 'has-error'; ?>">
                <label for="opname_date">Opname Date</label>
                <input type="text" class="form-control datepicker" id="opname_date" name="opname_date"
                       placeholder="Enter opname request date"
                       required maxlength="50" value="<?= set_value('opname_date') ?>">
                <?= form_error('opname_date', '<span class="help-block">', '</span>'); ?>
            </div>

             <div class="form-group <?= form_error('opname_type') == '' ?: 'has-error'; ?>">
                <label for="opname_type">Opname Type</label>
                <select class="form-control select2" required name="opname_type" id="opname_type"
                        data-placeholder="Select opname type" style="width: 100%">
                    <option value=""></option>
                    <option value="CONTAINER">CONTAINER</option>
                    <option value="GOODS">GOODS</option>
                </select>
                <?= form_error('opname_type', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Opname Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Opname description"
                          required maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
            <p><i class="fa fa-info-circle mr10"></i> Current stock will be automatically captured</p>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Save Opname</button>
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
