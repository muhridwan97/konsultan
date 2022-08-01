<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Overtime</h3>
    </div>
    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">No Overtime</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $overtime['no_overtime'] ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3">Name Of Day</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $overtime['name_of_day'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">First Overtime</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $overtime['first_overtime'] ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3">Second Overtime</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $overtime['second_overtime'] ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3">Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $overtime['description'] ?></p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($overtime['created_at']) ?>
                    </p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($overtime['updated_at']) ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
        </div>
    </form>
</div>