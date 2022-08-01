<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Roles <strong><?= $user['name'] ?></strong></h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped" id="table-role">
            <thead>
            <tr>
                <th>No</th>
                <th>Branch</th>
                <th>Role</th>
                <th>Description</th>
                <th>Total Permission</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($roles as $role): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $role['branch'] ?></td>
                    <td><?= $role['role'] ?></td>
                    <td><?= $role['description'] ?></td>
                    <td>
                        <?php if($role['total_permission'] == 0): ?>
                            No permission available
                        <?php else: ?>
                            <a href="<?= site_url('role/permission/' . $role['id']) ?>">
                                <?= number_format($role['total_permission'], 0, ',', '.') ?> permissions
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>