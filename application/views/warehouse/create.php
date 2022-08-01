<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create new Warehouse</h3>
    </div>
    <form action="<?= site_url('warehouse/save') ?>" role="form" class="need-validation" method="post">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                <label for="branch">Branch</label>
                <select class="form-control select2" name="branch" id="branch" data-placeholder="Select branch" required>
                    <option value=""></option>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>" <?= set_value('branch', get_active_branch_id()) == $branch['id'] ? 'selected' : '' ?>>
                            <?= $branch['branch'] ?>
                        </option>
                    <?php endforeach ?>
                </select>
                <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('warehouse') == '' ?: 'has-error'; ?>">
                        <label for="warehouse">Warehouse Name</label>
                        <input type="text" class="form-control" id="warehouse" name="warehouse"
                               placeholder="Enter warehouse name"
                               required maxlength="50" value="<?= set_value('warehouse') ?>">
                        <?= form_error('warehouse', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('type') == '' ?: 'has-error'; ?>">
                        <label for="type">Type</label>
                        <select name="type" id="type" class="form-control select2" style="width: 100%">
                            <option value="YARD"<?= set_select('type', 'YARD') ?>>FIELD / YARD</option>
                            <option value="COVERED YARD"<?= set_select('type', 'COVERED YARD') ?>>COVERED YARD</option>
                            <option value="WAREHOUSE"<?= set_select('type', 'WAREHOUSE') ?>>WAREHOUSE</option>
                        </select>
                        <?= form_error('type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('total_column') == '' ?: 'has-error'; ?>">
                        <label for="total_column">Total Column (X)</label>
                        <input type="number" class="form-control" id="total_column" name="total_column"
                               placeholder="Total x coordinate" min="1"
                               required maxlength="50" value="<?= set_value('total_column') ?>">
                        <?= form_error('total_column', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('total_row') == '' ?: 'has-error'; ?>">
                        <label for="total_row">Total Row (Y)</label>
                        <input type="number" class="form-control" id="total_row" name="total_row"
                               placeholder="Total y coordinate" min="1"
                               required maxlength="50" value="<?= set_value('total_row') ?>">
                        <?= form_error('total_row', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Warehouse description"
                          maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Save Warehouse</button>
        </div>
    </form>
</div>