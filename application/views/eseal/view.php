<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View E-seal</h3>
    </div>
    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">E-seal Number</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $eseal['no_eseal'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Is Used</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $eseal['is_used'] ? 'Yes' : 'No' ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Used In</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($eseal['used_in'], '-') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Device ID</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($eseal['id_device'], '-') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Device Name</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($eseal['device_name'], '-') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($eseal['description'], '-') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= format_date($eseal['created_at'], 'd F Y H:i') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty(format_date($eseal['updated_at'], 'd F Y H:i'), '-') ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
        </div>
    </form>
</div>
