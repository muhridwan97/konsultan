<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Category Type</h3>
    </div>
    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Value Type</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $complainCategory['value_type'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Category</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $complainCategory['category'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($complainCategory['created_at']) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($complainCategory['updated_at']) ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
        </div>
    </form>
</div>