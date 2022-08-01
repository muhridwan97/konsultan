<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Extension Field</h3>
    </div>
    <form role="form">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Field Title</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $extensionField['field_title'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Field Name</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $extensionField['field_name'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Type</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $extensionField['type'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Option</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= print_all(json_decode($extensionField['option'], true)) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $extensionField['description'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($extensionField['created_at']) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($extensionField['updated_at']) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Preview</label>
                <div class="col-sm-9 col-md-6">
                    <?php $this->load->view('extension_field/_field', ['extensionField' => $extensionField]) ?>
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