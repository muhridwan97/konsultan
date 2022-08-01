<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Chassis</h3>
    </div>
    <form action="<?= site_url('transporter-entry-permit-chassis/update/' . $tepChassis['id']) ?>" role="form" method="post">
        
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-group">
                <label for="no_chassis" class="control-label">No Chassis</label>
                <input type="text" class="form-control" name="no_chassis" id="no_chassis"
                       placeholder="Chassis number" required maxlength="100" value="<?= set_value('no_chassis', $tepChassis['no_chassis']) ?>">
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Chassis description"
                          maxlength="500"><?= set_value('description', $tepChassis['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary">Back</a>
            <button type="submit" class="btn btn-primary pull-right" id="btn-update-tep" data-toggle="one-touch">
                Update Chassis
            </button>
        </div>
    </form>
</div>