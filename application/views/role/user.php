<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Users <strong><?= $role['role'] ?></strong></h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped" id="table-user">
            <thead>
            <tr>
                <th>No</th>
                <th>Name</th>
                <th>Username</th>
                <th>Email</th>
                <th>Branch</th>
                <th>Status</th>
                <th>Total Role</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($users as $user): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $user['name'] ?></td>
                    <td><?= $user['username'] ?></td>
                    <td><a href="mailto:<?= $user['email'] ?>"><?= $user['email'] ?></a></td>
                    <td><?= $user['source_branch'] ?></td>
                    <td>
                        <?php
                        $statusLabel = [
                            'PENDING' => 'label-warning',
                            'ACTIVATED' => 'label-success',
                            'SUSPENDED' => 'label-danger',
                        ];
                        $classLabel = 'label-default';
                        if (key_exists($user['status'], $statusLabel)) {
                            $classLabel = $statusLabel[$user['status']];
                        }
                        ?>
                        <span class="label <?= $classLabel ?>"><?= $user['status'] ?></span>
                    </td>
                    <td>
                        <?php if ($user['total_role'] == 0): ?>
                            No role available
                        <?php else: ?>
                            <a href="<?= site_url('user/role/' . $user['id']) ?>">
                                <?= number_format($user['total_role'], 0, ',', '.') ?> roles
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>