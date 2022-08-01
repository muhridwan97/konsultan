<div class="box box-primary">
    <div class="box-header with-border">
		<h3 class="box-title">View Operation Cut Off</h3>
    </div>
    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Branch</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $operationCutOff['branch'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Shift</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $operationCutOff['shift'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Start</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= format_date($operationCutOff['start'], 'H:i') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">End</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= format_date($operationCutOff['end'], 'H:i') ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Status</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $operationCutOff['status'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($operationCutOff['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($operationCutOff['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Updated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(format_date($operationCutOff['updated_at'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <a href="<?= site_url('operation-cut-off/edit/' . $operationCutOff['id']) ?>>" class="btn btn-primary pull-right">Edit</a>
        </div>
    </form>
</div>