<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Conversion</h3>
    </div>
    <form class="form-horizontal form-view">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Goods</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $conversion['name'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Unit From</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $conversion['unit_from'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Value</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $conversion['value'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Unit To</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $conversion['unit_to'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= format_date($conversion['created_at'], 'd F Y H:i') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty(format_date($conversion['updated_at'], 'd F Y H:i'), '-') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
        </div>
    </form>
</div>