<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Overtime <?= $workOrder['no_work_order'] ?></h3>
    </div>
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">No Work Order</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <a href="<?= site_url('work-order/view/' . $workOrderOvertime['id_work_order']) ?>">
                                    <?= $workOrderOvertime['no_work_order'] ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Reference</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $workOrderOvertime['no_reference'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Customer</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $workOrderOvertime['customer_name'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Completed At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($workOrderOvertime['completed_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Service Day</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $workOrderOvertime['service_day'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Service End</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($workOrderOvertime['service_time_end'], 'H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Total Overtime</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $workOrderOvertime['total_overtime_minute'] ?> Minute(s)
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Effective Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($workOrderOvertime['effective_date'], 'd F Y') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Charged To</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($workOrderOvertime['overtime_charged_to'], '<span class="label label-default">PENDING</span>') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Attachment</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if (empty($workOrderOvertime['overtime_attachment'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="<?= asset_url($workOrderOvertime['overtime_attachment']) ?>">
                                        <?= basename($workOrderOvertime['overtime_attachment']) ?>
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Reason</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($workOrderOvertime['reason'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($workOrderOvertime['description'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Validated By</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($workOrderOvertime['validator_name'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Validated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(format_date($workOrderOvertime['created_at'], 'd F Y H:i'), '-') ?>
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