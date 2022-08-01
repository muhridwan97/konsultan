<div class="box box-primary">
    <div class="box-header with-border">
		<h3 class="box-title">View Logs</h3>
    </div>
    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Type</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $log['type'] ?></p>
                </div>
            </div>
			<div class="form-group">
                <label class="col-sm-3">Host</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= isset($log['data']['host']) ? if_empty($log['data']['host'], '-') : '-' ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Path</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= isset($log['data']['path']) ? if_empty($log['data']['path'], '-') : '-' ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">IP</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= isset($log['data']['ip']) ? if_empty($log['data']['ip'], '-') : '-' ?></p>
                </div>
            </div>
			<div class="form-group">
                <label class="col-sm-3">Platform</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= isset($log['data']['platform']) ? if_empty($log['data']['platform'], '-') : '-' ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Browser</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= isset($log['data']['browser']) ? if_empty($log['data']['browser'], 'No description') : '-' ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= format_date($log['created_at'], 'd F Y H:i') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
        </div>
    </form>
</div>