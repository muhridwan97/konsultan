<div class="box box-primary">
    <div class="box-header with-border">
		<h3 class="box-title">View Module</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Module Name</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $module['module_name'] ?></p>
                </div>
            </div>
			<div class="form-group">
                <label class="col-sm-3">Module Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty($module['module_description'], '-') ?>
                    </p>
                </div>
            </div>
			<div class="form-group">
                <label class="col-sm-3">Type</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $module['type'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Hostname</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $module['hostname'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Port</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($module['port'], '(default)') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Database</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $module['database'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Username</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $module['username'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Username</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $module['table_header'] ?> (<?= $module['table_header_id'] ?>)</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Username</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $module['table_container'] ?> (<?= $module['table_container_ref'] ?>)</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Username</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $module['table_goods'] ?> (<?= $module['table_goods_ref'] ?>)</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Password</label>
                <div class="col-sm-9">
                    <p class="form-control-static">(hidden)</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= (new DateTime($module['created_at']))->format('d F Y H:i') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= (new DateTime($module['updated_at']))->format('d F Y H:i') ?>
                    </p>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <a href="<?= site_url('module') ?>" class="btn btn-primary">Back to Module List</a>
        </div>
    </form>
</div>
<!-- /.box -->