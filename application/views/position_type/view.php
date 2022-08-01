<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Position Type</h3>
    </div>
    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Position Type</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $positionType['position_type'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Is Usable</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $positionType['is_usable'] ? 'YES' : 'NO' ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Color</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <span style="background-color: <?= $positionType['color'] ?>; padding: 5px; color: white;">
                            <?= if_empty($positionType['color'], '-') ?>
                        </span>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($positionType['description'], 'No description') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= format_date($positionType['created_at'], 'd F Y H:i') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty(format_date($positionType['updated_at'], 'd F Y H:i'), '-') ?>
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