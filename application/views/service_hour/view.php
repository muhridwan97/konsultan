<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View <?= $serviceHour['service_day'] ?></h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_SERVICE_HOUR_EDIT)): ?>
            <a href="<?= site_url('service-hour/edit/' . $serviceHour['id']) ?>" class="btn btn-primary pull-right">
                Edit Service Hour
            </a>
        <?php endif; ?>
    </div>
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Service Day</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $serviceHour['service_day'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Start Hour</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($serviceHour['service_time_start'], 'H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">End Hour</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($serviceHour['service_time_end'], 'H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Effective Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($serviceHour['effective_date'], 'd F Y') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($serviceHour['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created By</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($serviceHour['creator_name'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($serviceHour['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Updated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(format_date($serviceHour['updated_at'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Service Hour Histories</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>Effective Date</th>
                        <th>Start</th>
                        <th>End</th>
                        <th>Created At</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($relatedServiceHours as $index => $relatedServiceHour): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $relatedServiceHour['effective_date'] ?></td>
                            <td><?= format_date($relatedServiceHour['service_time_start'], 'H:i') ?></td>
                            <td><?= format_date($relatedServiceHour['service_time_end'], 'H:i') ?></td>
                            <td><?= format_date($relatedServiceHour['created_at'], 'd F Y H:i') ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($relatedServiceHours)): ?>
                        <tr>
                            <td colspan="5">No service hour history</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
        <a href="<?= site_url('service-hour/print-service-hour/' . $serviceHour['id']) ?>" class="btn btn-primary pull-right">
            Print
        </a>
    </div>
</div>