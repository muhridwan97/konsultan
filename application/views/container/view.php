<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Container</h3>
    </div>
    <form role="form">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Container Number</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $container['no_container'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Type</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $container['type'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Size</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $container['size'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($container['description'], '-') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty(format_date($container['updated_at'], 'd F Y H:i'), '-') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty(format_date($container['updated_at'], 'd F Y H:i'), '-') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
        </div>
    </form>
</div>