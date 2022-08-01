<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create New Heavy Equipment</h3>
    </div>
    <form action="<?= site_url('heavy_equipment/save') ?>" role="form" method="post" class="need-validation">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('name') == '' ?: 'has-error'; ?>">
                <label for="name">Heavy Equipment Name</label>
                <input type="text" class="form-control" id="name" name="name"
                       placeholder="Enter Heavy Equipment Name"
                       required maxlength="50" value="<?= set_value('name') ?>">
                <?= form_error('name', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('type') == '' ?: 'has-error'; ?>">
                <label for="type">Heavy Equipment Type</label>
                <select class="form-control select2" required name="type" id="type"
                        data-placeholder="Select Heavy Equipment Type" style="width: 100%" >
                    <option value=""></option>
                    <option value="FORKLIFT" <?= set_select('type', 'FORKLIFT') ?>>
                        FORKLIFT
                    </option>
                    <option value="CRANE" <?= set_select('type', 'CRANE') ?>>
                        CRANE
                    </option>
                    <option value="REACH STACKER" <?= set_select('type', 'REACH STACKER') ?>>
                        REACH STACKER
                    </option>
                </select>
                <?= form_error('type', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('branches[]') == '' ?: 'has-error'; ?>">
                <label for="branch">Branches</label>
                <select class="form-control select2" name="branches[]" id="branch" data-placeholder="Select branch" required multiple>
                    <option value=""></option>
                    <?php foreach ($branches as $branch): ?>
                        <option value="<?= $branch['id'] ?>"<?= set_select('branches[]', $branch['id']) ?>>
                            <?= $branch['branch'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('branches[]', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Vehicle description"
                          required maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Save Heavy Equipment</button>
        </div>
    </form>
</div>