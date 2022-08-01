<div class="box box-primary">
    <div class="box-header with-border">
		<h3 class="box-title">View Attachment Photo</h3>
    </div>
    <form role="form">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Photo Name</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= $attachmentPhoto['photo_name'] ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty($attachmentPhoto['description'], 'No description') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($attachmentPhoto['created_at']) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($attachmentPhoto['updated_at']) ?>
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