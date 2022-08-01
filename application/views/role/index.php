<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Roles</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_ROLE_CREATE)): ?>
                <a href="<?= site_url('role/create') ?>" class="btn btn-primary">
                    Create Role
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-role">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Role</th>
                <th>Description</th>
                <th>Total Permission</th>
                <th>Total User</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($roles as $role): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $role['role'] ?></td>
                    <td><?= is_null($role['description']) ? 'No description' : $role['description'] ?></td>
                    <td>
                        <?php if ($role['total_permission'] == 0): ?>
                            No permission available
                        <?php else: ?>
                            <a href="<?= site_url('role/permission/' . $role['id']) ?>">
                                <?= number_format($role['total_permission'], 0, ',', '.') ?> permissions
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if ($role['total_user'] == 0): ?>
                            No user available
                        <?php else: ?>
                            <a href="<?= site_url('role/user/' . $role['id']) ?>">
                                <?= number_format($role['total_user'], 0, ',', '.') ?> users
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_ROLE_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('role/view/' . $role['id']) ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_ROLE_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('role/edit/' . $role['id']) ?>">
                                            <i class="fa ion-compose"></i>Edit
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_ROLE_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('role/delete/' . $role['id']) ?>" class="btn-delete"
                                           data-id="<?= $role['id'] ?>"
                                           data-title="Role"
                                           data-label="<?= $role['role'] ?>">
                                            <i class="fa ion-trash-a"></i> Delete
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_ROLE_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>