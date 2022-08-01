<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Handover</h3>
    </div>
    <div class="box-body">
        <div role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">No Safe Conduct</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <a href="<?= site_url('safe-conduct/view/' . $safeConductHandover['id_safe_conduct']) ?>">
                                    <?= $safeConductHandover['no_safe_conduct'] ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">No Reference</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <a href="<?= site_url('booking/view/' . $safeConductHandover['id_booking']) ?>">
                                    <?= $safeConductHandover['no_reference'] ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">No Police</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $safeConductHandover['no_police'] ?: '-' ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Vehicle</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $safeConductHandover['vehicle_type'] ?: '-' ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Received Date</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty(format_date($safeConductHandover['received_date'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Driver Handover Date</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty(format_date($safeConductHandover['driver_handover_date'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">TEP Code</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $safeConductHandover['tep_code'] ?: '-' ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Status</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php
                                $statuses = [
                                    SafeConductHandoverModel::STATUS_PENDING => 'default',
                                    SafeConductHandoverModel::STATUS_RECEIVED => 'primary',
                                    SafeConductHandoverModel::STATUS_HANDOVER => 'success',
                                ]
                                ?>
                                <span class="label label-<?= get_if_exist($statuses, $safeConductHandover['status'], 'success') ?>">
                                    <?= $safeConductHandover['status'] ?: 'HANDOVER' ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($safeConductHandover['description'], 'No description') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created By</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $safeConductHandover['creator_name'] ?: '-' ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= format_date($safeConductHandover['created_at'], 'd F Y H:i') ?: '-' ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Updated At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty(format_date($safeConductHandover['updated_at'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header with-border">
                <h3 class="box-title">Tracking Link</h3>
            </div>
            <div class="box-body">
                <div role="form" class="form-horizontal form-view">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-sm-4">Tracking</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?php if(!empty($safeConductHandover['id_tep_tracking'])): ?>
                                            <a href="<?= site_url('transporter-entry-permit-tracking/view/' . $safeConductHandover['id_tep_tracking']) ?>">
                                                Tracking Link
                                            </a>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Site Transit Actual</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $safeConductHandover['site_transit_actual_date'] ?: '-' ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Unloading Actual</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= $safeConductHandover['unloading_actual_date'] ?: '-' ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
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