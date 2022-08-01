<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Checklist Type</h3>
    </div>
    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Checklist Type</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $checklist_type['checklist_type'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Checklist Subtype</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($checklist_type['subtype'], 'No description') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($checklist_type['created_at']) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($checklist_type['updated_at']) ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
        </div>
    </form>
</div>