<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">System Backup</h3>
    </div>
    <div class="box-body">
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Application Backup</h3>
            </div>
            <div class="box-body">
                <a href="<?= site_url("backup/app/full") ?>"
                   class="btn btn-primary pull-right">
                    App + Data
                </a>
                <a href="<?= site_url("backup/app/quick") ?>"
                   class="btn btn-info pull-right mr10">
                    App Only
                </a>
                <h4 class="mb0 mt0 text-primary">
                    Full application backup
                </h4>
                <p class="text-muted mb0">
                    Export app folder and download the data
                </p>
            </div>
        </div>
        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Databases</h3>
            </div>
            <div class="box-body">
                <div class="panel panel-primary">
                    <div class="panel-body">
                        <a href="<?= site_url("backup/database/main") ?>"
                           class="btn btn-primary pull-right">
                            Backup Now
                        </a>
                        <h4 class="mb0 mt0 text-primary">
                            Primary Database <small>(Warehouse)</small>
                        </h4>
                        <p class="text-muted mb0">
                            Transcon warehouse system
                        </p>
                    </div>
                    <div class="panel-footer">
                        <strong>Live on</strong>
                        <?= $this->db->hostname ?><?= empty($this->db->port) ? '' : ':' . $this->db->port ?>
                        /<?= $this->db->database ?>
                    </div>
                </div>

                <?php foreach ($modules as $module): ?>
                    <div class="panel panel-default">
                        <div class="panel-body">
                            <a href="<?= site_url("backup/database/module/{$module['id']}") ?>"
                               class="btn btn-default pull-right">
                                Backup Now
                            </a>
                            <h4 class="mb0 mt0"><?= $module['module_name'] ?>
                                <small>(<?= $module['type'] ?>)</small>
                            </h4>
                            <p class="text-muted mb0"><?= $module['module_description'] ?></p>
                        </div>
                        <div class="panel-footer">
                            <strong>Live on</strong>
                            <?= $module['hostname'] ?><?= empty($module['port']) ? '' : ':' . $module['port'] ?>
                            /<?= $module['database'] ?>
                        </div>
                    </div>
                <?php endforeach ?>

                <?php if (count($modules) == 0): ?>
                    No modules available.
                <?php endif ?>
            </div>
        </div>
    </div>
</div>