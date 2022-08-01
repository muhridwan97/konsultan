<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Modules Explorer</h3>
    </div>
    <div class="box-body">
        <?php foreach ($modules as $module): ?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <a href="<?= site_url("module_explorer/schema/{$module['id']}") ?>" class="btn btn-primary pull-right">
                        Browse Data
                    </a>
                    <h4 class="mb0 mt0"><?= $module['module_name'] ?>
                        <small>(<?= $module['type'] ?>)</small>
                    </h4>
                    <p class="text-muted mb0"><?= $module['module_description'] ?></p>
                </div>
                <div class="panel-footer">
                    <strong>Live on</strong> <?= $module['hostname'] ?><?= empty($module['port']) ? '' : ':' . $module['port'] ?>/<?= $module['database'] ?>
                </div>
            </div>
        <?php endforeach; ?>

        <?php if(count($modules) == 0): ?>
            No modules available.
        <?php endif; ?>
    </div>
</div>