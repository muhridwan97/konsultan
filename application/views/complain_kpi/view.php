<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View KPI</h3>
    </div>
    <form role="form" class="form-horizontal form-view">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">KPI</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $complain_kpi['kpi'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Major</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $complain_kpi['major'] ?> hour</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Minor</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $complain_kpi['minor'] ?> hour</p>
                </div>
            </div>
            <?php if($complain_kpi['kpi'] != ComplainKpiModel::KPI_RESPONSE_WAITING_TIME): ?>
            <div class="form-group">
                <label class="col-sm-3">Reminder day</label>
                <div class="col-sm-9">
                    <p class="form-control-static">Every <?= $complain_kpi['recur_day'] ?> day</p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Reminder time</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $complain_kpi['reminder'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Whatsapp group</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $complain_kpi['whatsapp_group'] ?></p>
                </div>
            </div>
            <?php endif; ?>
            <div class="form-group">
                <label class="col-sm-3">Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($complain_kpi['description'], 'No description') ?></p>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
        </div>
    </form>
</div>