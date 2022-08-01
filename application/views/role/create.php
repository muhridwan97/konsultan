<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create new Role</h3>
    </div>
    <form action="<?= site_url('role/save') ?>" role="form" method="post" class="need-validation" id="form-role">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('role') == '' ?: 'has-error'; ?>">
                <label for="role">Role Title</label>
                <input type="text" class="form-control" id="role" name="role"
                       placeholder="Enter role name"
                       required maxlength="50" value="<?= set_value('role') ?>">
                <?= form_error('role', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Role Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Role description"
                          required maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
			<div class="form-group <?= form_error('permissions[]') == '' ?: 'has-error'; ?>">
                <label for="permission">Permissions</label>
                <span class="help-block" style="margin-bottom: 0">Role at least must has one permission</span>
				<div class="row">
                    <?php $lastGroup = '' ?>
                    <?php $lastSubGroup = '' ?>
                    <?php foreach ($permissions as $permission): ?>
                        <?php
                        $module = $permission['module'];
                        $submodule = $permission['submodule'];
                        if($lastGroup != $module):
                            $lastGroup = $module;
                            $lastGroupName = preg_replace('/ /', '_', $lastGroup);
                            ?>
                            <div class="col-xs-12 mt20">
                                <hr>
                                <h4 class="mt20">
                                    <div class="checkbox icheck" style="margin-top: 0">
                                        <label>
                                            <input type="checkbox" name="check_all_<?= $lastGroupName ?>" class="check_all" value="<?= $lastGroupName ?>"
                                                <?php echo set_checkbox('check_all_'.$lastGroupName, $lastGroupName); ?>>
                                            <?= ucwords($lastGroup) ?> (Check All)
                                        </label>
                                    </div>
                                </h4>
                                <hr>
                            </div>
                        <?php endif; ?>

                        <?php
                        if($lastSubGroup != $submodule):
                        $lastSubGroup = $submodule;
                        ?>
                            <div class="col-xs-12">
                                <p><strong><?= ucwords($lastSubGroup) ?></strong></p>
                            </div>
                        <?php endif; ?>

                        <div class="col-sm-4">
                            <div class="checkbox icheck" style="margin-top: 0">
                                <label>
                                    <input type="checkbox" name="permissions[]" class="<?= $lastGroupName ?>" id="permission_<?= $permission['id'] ?>" value="<?= $permission['id'] ?>"
                                        <?php echo set_checkbox('permissions', $permission['id']); ?>>
                                    &nbsp; <?= ucwords(preg_replace('/(_|\-)/', ' ', $permission['permission'])) ?>
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">Save Role</button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/role.js') ?>" defer></script>