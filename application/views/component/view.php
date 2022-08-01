<div class="box box-primary">
    <div class="box-header with-border">
		<h3 class="box-title">View Component</h3>
    </div>
    <form role="form">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Handling Component</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= $component['handling_component'] ?>
                    </p>
                </div>
            </div>
			<div class="form-group">
                <label class="col-sm-3">Category</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= $component['component_category'] ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty($component['description'], 'No description') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($component['created_at']) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($component['updated_at']) ?>
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