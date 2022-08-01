<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Raw Contact</h3>
    </div>
    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Company</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $rawContact['company'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">PIC</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $rawContact['pic'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Address</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $rawContact['address'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Contact</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $rawContact['contact'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Email</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $rawContact['email'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($rawContact['created_at']) ?>
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