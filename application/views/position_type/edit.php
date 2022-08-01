<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Branch</h3>
    </div>
    <form action="<?= site_url('position-type/update/' . $positionType['id']) ?>" role="form" class="need-validation" method="post">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('position_type') == '' ?: 'has-error'; ?>">
                <label for="position_type">Position Type</label>
                <input type="text" class="form-control" id="position_type" name="position_type"
                       placeholder="Enter position type"
                       required maxlength="50" value="<?= set_value('position_type', $positionType['position_type']) ?>">
                <?= form_error('position_type', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group <?= form_error('is_usable') == '' ?: 'has-error'; ?>">
                        <label for="usable-yes">Is Usable</label>
                        <div>
                            <label for="usable-yes" class="radio-inline">
                                <input type="radio" id="usable-yes" name="is_usable" value="1"
                                    <?= set_radio('is_usable', 1, $positionType['is_usable'] == 1)?>> YES
                            </label>
                            <label for="usable-no" class="radio-inline">
                                <input type="radio" id="usable-no" name="is_usable" value="0"
                                    <?= set_radio('is_usable', 0, $positionType['is_usable'] == 0)?>> NO
                            </label>
                        </div>
                        <?= form_error('is_usable', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group <?= form_error('color') == '' ?: 'has-error'; ?>">
                        <label for="color">Color</label>
                        <input type="color" class="form-control" id="color" name="color"
                               placeholder="Pick color"
                               required maxlength="50" value="<?= set_value('color', $positionType['color']) ?>">
                        <?= form_error('color', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Position type description"
                          maxlength="500"><?= set_value('description', $positionType['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Update Position Type</button>
        </div>
    </form>
</div>