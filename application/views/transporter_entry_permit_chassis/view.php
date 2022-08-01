<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Chassis Delivery</h3>
    </div>
    <div class="box-body">
        <div role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">TEP Code</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <a href="<?= site_url('transporter-entry-permit/view/' . $tepChassis['id_tep']) ?>">
                                    <?= $tepChassis['tep_code'] ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">No Chassis</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $tepChassis['no_chassis'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">No Work Order</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php if (!empty($tepChassis['id_work_order'])): ?>
                                    <a href="<?= site_url('work-order/view/' . $tepChassis['id_work_order']) ?>">
                                        <?= $tepChassis['no_work_order'] ?>
                                    </a>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Checked In At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($tepChassis['checked_in_at'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Checked Out At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($tepChassis['checked_out_at'], '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Checked In Desc</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($tepChassis['checked_in_description'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Checked Out Desc</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($tepChassis['checked_out_description'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($tepChassis['description'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= format_date($tepChassis['created_at'], 'd F Y H:i') ?: '-' ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Updated At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty(format_date($tepChassis['updated_at'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>