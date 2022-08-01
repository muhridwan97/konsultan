<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create new Unit</h3>
    </div>
    <form action="<?= site_url('unit/save') ?>" class="form need-validation" method="post">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('unit') == '' ?: 'has-error'; ?>">
                <label for="unit">Unit Name</label>
                <input type="text" class="form-control" id="unit" name="unit"
                       placeholder="Enter unit name"
                       required value="<?= set_value('unit') ?>">
                <?= form_error('unit', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Unit description</label>
                <textarea class="form-control" id="description" name="description"
                          placeholder="Unit description"
                          required maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Save</button>
        </div>
    </form>
</div>