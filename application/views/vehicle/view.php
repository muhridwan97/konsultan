<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Vehicle</h3>
    </div>
    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Vehicle Name</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $vehicle['vehicle_name'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Vehicle Type</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $vehicle['vehicle_type'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Vehicle Branch</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($vehicle['branch'], 'No Branch'); ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Vehicle Status</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $vehicle['status'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Plate Number</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $vehicle['no_plate'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $vehicle['description'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= format_date($vehicle['created_at'], 'd F Y H:i') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty(format_date($vehicle['updated_at'], 'd F Y H:i'), '-') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
        </div>
    </form>
</div>