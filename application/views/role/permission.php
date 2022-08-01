<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Permissions <strong><?= $role['role'] ?></strong></h3>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped">
            <thead>
            <tr>
                <th>No</th>
                <th>Permission Name</th>
                <th>Module</th>
                <th>Submodule</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1; ?>
            <?php foreach ($permissions as $permission): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $permission['permission'] ?></td>
                    <td><?= $permission['module'] ?></td>
                    <td><?= $permission['submodule'] ?></td>
                    <td><?= if_empty($permission['description'], 'No Description') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>