<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Import Opname Space</h3>
    </div>
    <form action="<?= site_url('opname-space/upload-opname/' . $opnameSpaces['id']) ?>" role="form" method="post" id="form-opname-upload" enctype="multipart/form-data">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="form-horizontal form-view">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-3">No Opname</label>
                            <div class="col-sm-9">
                                <p><?= if_empty($opnameSpaces['no_opname_space'], '-') ?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">Opname Date</label>
                            <div class="col-sm-9">
                                <p><?= format_date($opnameSpaces['opname_space_date'], 'd F Y') ?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">Description</label>
                            <div class="col-sm-9">
                                <p><?= if_empty($opnameSpaces['description'], ' No Description') ?></p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-3">Status</label>
                            <div class="col-sm-9">
                                <p class="<?= $opnameSpaces['status'] != 'APPROVED' ? 'text-danger' : '' ?>"><?= $opnameSpaces['status'] ?></p>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3">Validated By</label>
                            <div class="col-sm-9">
                                <p><?= if_empty($opnameSpaces['validated_by'], '-') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header with-border">
                    <h3 class="box-title">Opname Import</h3>
                </div>
                <div class="box-body">
                    <div class="form-group <?= form_error('opname_source') == '' ?: 'has-error'; ?>">
                        <label for="opname_source">Opname Source</label>
                        <div>
                            <a href="<?= site_url('opname-space/process/' . $opnameSpaces['id'].'?export=true') ?>" class="btn btn-primary">
                                <i class="fa fa-download mr10"></i>Download
                            </a>
                        </div>
                    </div>
                    <div class="form-group <?= form_error('opname_result') == '' ?: 'has-error'; ?>">
                        <label for="opname_result">Opname Result</label>
                        <input type="file" name="opname_result" id="opname_result"
                               accept="application/vnd.openxmlformats-officedocument.spreadsheetml.sheet, application/vnd.ms-excel" required
                               placeholder="Select data">
                        <p class="text-danger mt10">Uploaded data will override existing data by its related id</p>
                        <?= form_error('opname_result', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" class="btn btn-primary pull-right">
                Upload Opname Result
            </button>
        </div>
    </form>
</div>
