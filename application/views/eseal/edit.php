<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit E-seal</h3>
    </div>
    <form action="<?= site_url('eseal/update/'.$eseal['id']) ?>" role="form" method="post" class="need-validation">
        <input type="hidden" name="id" id="id" value="<?= $eseal['id'] ?>">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <?php if($this->config->item('enable_branch_mode')): ?>
                <input type="hidden" name="branch" id="branch" value="<?= get_active_branch_id() ?>">
            <?php else: ?>
                <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                    <label for="branch">Branch</label>
                    <select class="form-control select2" name="branch" id="branch" data-placeholder="Select branch" required>
                        <option value=""></option>
                        <?php foreach ($branches as $branch): ?>
                            <option value="<?= $branch['id'] ?>"<?= set_select('branch', $branch['id'], $branch['id'] == $eseal['id_branch']) ?>>
                                <?= $branch['branch'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
                </div>
            <?php endif; ?>

            <div class="form-group <?= form_error('device') == '' ?: 'has-error'; ?>">
                <label for="device">Physical Device</label>
                <select class="form-control select2" name="device" id="device" data-placeholder="Select device">
                    <option value="0">NO DEVICE</option>
                    <?php foreach ($devices as $device): ?>
                        <option value="<?= $device['id'] ?>"<?= set_select('device', $device['id'], $device['id'] == $eseal['id_device']) ?>>
                            <?= $device['name'] ?> (ID <?= $device['id'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('device', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('no_eseal') == '' ?: 'has-error'; ?>">
                <label for="no_eseal">No E-seal</label>
                <input type="text" class="form-control" id="no_eseal" name="no_eseal"
                       placeholder="Enter e-seal name"
                       required maxlength="50" value="<?= set_value('no_eseal', $eseal['no_eseal']) ?>">
                <?= form_error('no_eseal', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">E-seal Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="E-seal description"
                          required maxlength="500"><?= set_value('description', $eseal['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Update E-seal</button>
        </div>
    </form>
</div>
