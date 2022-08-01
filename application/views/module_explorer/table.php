<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            <a href="<?= site_url('module_explorer') ?>">Modules</a> /
            <a href="<?= site_url('module_explorer/schema/' . $module['id']) ?>"><?= $module['module_name'] ?></a>
            / <?= $tableName ?>
        </h3>
    </div>
    <div class="box-body table-responsive">
        <table class="table table-striped no-wrap">
            <thead>
            <tr>
                <?php foreach ($tableFields as $field): ?>
                    <th><?= $field ?></th>
                <?php endforeach; ?>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($tableData as $fields): ?>
                <tr>
                    <?php foreach ($fields as $value): ?>
                        <td><?= $value ?></td>
                    <?php endforeach; ?>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="box-footer">
        <a href="<?= site_url('module_explorer/schema/' . $module['id']) ?>" class="btn btn-primary">
            Back to Schema Table
        </a>
    </div>
</div>