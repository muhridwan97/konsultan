<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Linked Entry Permit</h3>
    </div>

    <form action="<?= site_url('linked-entry-permit/update-linked-tep/' . $tep['id']) ?>" role="form" method="post" class="need-validation">
        <?= _csrf() ?>
        <?= _method('put') ?>

        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-horizontal form-view">
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-3">TEP Category</label>
                            <div class="col-sm-9">
                                <p class="form-control-static"><?= if_empty($tep['tep_category'], '-') ?></p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">TEP Code</label>
                            <div class="col-sm-9">
                                <p class="form-control-static"><?= if_empty($tep['tep_code'], '-') ?></p>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-3">Linked TEP</label>
                            <div class="col-sm-9">
                                <p class="form-control-static">
                                    <a href="<?= site_url('p/' . $linkedTep['id_branch'] . '/transporter-entry-permit/view/' . $linkedTep['id'], false) ?>">
                                        <?= if_empty($linkedTep['tep_code'], '-') ?>
                                    </a>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-3">Branch</label>
                            <div class="col-sm-9">
                                <p class="form-control-static"><?= if_empty($linkedTep['branch'], '-') ?></p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="alert alert-danger">
                <h4 class="mt0 mb0">Update linked entry permit</h4>
                <p class="mb0">
                    Are you sure want to update linked tep <strong><?= $linkedTep['tep_code'] ?></strong>,
                    related TEP may filled with checkout date and <strong>cannot be picked in job</strong> anymore?
                </p>
            </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-danger pull-right">
                Update Linked TEP
            </button>
        </div>
    </form>
</div>