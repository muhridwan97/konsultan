<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Role</h3>
    </div>

    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Role Title</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $role['role'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($role['description'], '-') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Permissions</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?php if($role['total_permission'] == 0): ?>
                            No permission available
                        <?php else: ?>
                            <a href="<?= site_url('role/permission/' . $role['id']) ?>">
                                <?= number_format($role['total_permission'], 0, ',', '.') ?> permissions
                            </a>
                        <?php endif; ?>
                    </p>
                    <div class="row">
                        <?php foreach ($userPermissions as $permission): ?>
                            <div class="col-md-4">
                                <ul style="padding-left: 16px">
                                    <li><?= $permission['permission'] ?></li>
                                </ul>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Users</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?php if($role['total_user'] == 0): ?>
                            No user available
                        <?php else: ?>
                            <a href="<?= site_url('user/role/' . $role['id']) ?>">
                                <?= number_format($role['total_user'], 0, ',', '.') ?> users
                            </a>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= format_date($role['created_at'], 'd F Y H:i') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty(format_date($role['updated_at'], 'd F Y H:i'), '-') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
        </div>
    </form>
</div>