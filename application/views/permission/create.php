<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create new Permission</h3>
    </div>
    <form action="<?= site_url('permission/save') ?>" role="form" method="post">
        <div class="box-body">
            <!-- alert -->
            <?php if ($this->session->flashdata('status') != NULL): ?>
                <div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <p><?= $this->session->flashdata('message'); ?></p>
                </div>
            <?php endif ?>
            <!-- end of alert -->
            <div class="form-group <?= form_error('module') == '' ?: 'has-error'; ?>">
                <label for="module">Module</label>
                <input type="text" class="form-control" id="module" name="module"
                       placeholder="Enter module permission"
                       required maxlength="50" value="<?= set_value('module') ?>">
                <?= form_error('module', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('submodule') == '' ?: 'has-error'; ?>">
                <label for="submodule">Module</label>
                <input type="text" class="form-control" id="submodule" name="submodule"
                       placeholder="Enter submodule permission"
                       required maxlength="50" value="<?= set_value('submodule') ?>">
                <?= form_error('submodule', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('permission') == '' ?: 'has-error'; ?>">
                <label for="permission">Permission Name</label>
                <input type="text" class="form-control" id="permission" name="permission"
                       placeholder="Enter Permission Name"
                       required maxlength="50" value="<?= set_value('permission') ?>">
                <?= form_error('permission', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Permission Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Permission description"
                          required maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer">
			<a href="<?= site_url('permission') ?>" class="btn btn-primary">Back to Permissions List</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Save Permission</button>
        </div>
    </form>
</div>