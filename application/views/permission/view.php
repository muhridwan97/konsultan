<div class="box box-primary">
    <div class="box-header with-border">
		<h3 class="box-title">View Permission</h3>
    </div>
    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Permission Name</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $permission['permission'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Module</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $permission['module'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Sub Module</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $permission['submodule'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($permission['description'], 'No description') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= format_date($permission['created_at'], 'd F Y H:i') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty(format_date($permission['updated_at'], 'd F Y H:i:s'), '-') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
        </div>
    </form>
</div>