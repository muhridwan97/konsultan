<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            <a href="<?= site_url('module_explorer') ?>">Modules</a> / <?= $module['module_name'] ?>
        </h3>
    </div>
    <div class="box-body">
        <div class="list-group">
            <a href="#" class="list-group-item disabled">
                Schema <?= $module['database'] ?>
            </a>
            <?php foreach ($schemas as $schema): ?>
                <a href="<?= site_url("module_explorer/table/{$module['id']}/{$schema['table_name']}") ?>" class="list-group-item">
                    <i class="fa ion-ios-list-outline"></i> &nbsp; <?= $schema['table_name'] ?>
                    <span class="badge"><?= $schema['table_rows'] ?> Records</span>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <div class="box-footer">
        <a href="<?= site_url('module_explorer') ?>" class="btn btn-primary">
            Back to Module List
        </a>
    </div>
</div>