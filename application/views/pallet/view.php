<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Pallet</h3>
    </div>
    <form role="form">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">No pallet</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $pallet['no_pallet'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Branch</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $pallet['batch'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Related Booking</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($pallet['no_booking'], 'No booking') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($pallet['description'], 'No description') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= format_date($pallet['created_at'], 'd F Y H:i') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty(format_date($pallet['updated_at'], 'd F Y H:i'), '-') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary">Back</a>
        </div>
    </form>
</div>