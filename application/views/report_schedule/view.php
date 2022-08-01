<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Report Schedule</h3>
    </div>
    <div class="form-horizontal form-view">
        <div class="box-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Report Name</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= str_replace(['-', '_'], ' ', $reportSchedule['report_name']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Recurring Period</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($reportSchedule['recurring_period'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Triggered At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($reportSchedule['schedule_label'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Status</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php
                                $taskStatuses = [
                                    ReportScheduleModel::STATUS_ACTIVE => 'success',
                                    ReportScheduleModel::STATUS_INACTIVE => 'danger',
                                ];
                                ?>
                                <span class="label label-<?= get_if_exist($taskStatuses, $reportSchedule['status'], 'primary') ?>">
                            <?= $reportSchedule['status'] ?>
                        </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Last Triggered</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(format_date($reportSchedule['last_triggered_at'], 'd F Y H:i:s'), '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($reportSchedule['description'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($reportSchedule['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Send To Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($reportSchedule['send_to_type'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Send To (Additional)</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($reportSchedule['send_to'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Send CC Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($reportSchedule['send_cc_type'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Send CC (Additional)</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($reportSchedule['send_cc'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Send BCC Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($reportSchedule['send_bcc_type'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Send BCC (Additional)</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($reportSchedule['send_bcc'], '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
        </div>
    </div>
</div>