<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Print <?= date('d F Y',strtotime($history['date'])) ?></h3>
    </div>
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Alat Berat</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($history['name'], 'No name') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($history['type'], 'No type') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Hari</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(date('l',strtotime($history['date'])), 'No day') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Jam</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($history['start'], '-') ?> - <?= if_empty($history['end'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Keterangan</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($history['description'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Remark</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($history['remark'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">City</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($history['sign_location'], '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>

    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>