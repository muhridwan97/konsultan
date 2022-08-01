<div class="row">
    <div class="col-md-6">
        <div class="form-group">
            <label class="col-sm-4 control-label">Vehicle</label>
            <div class="col-sm-8">
                <p class="form-control-static">
                    <?= if_empty($tep['receiver_vehicle'], '-') ?>
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-4 control-label">No Police</label>
            <div class="col-sm-8">
                <p class="form-control-static">
                    <?= if_empty($tep['receiver_no_police'], '-') ?>
                </p>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="form-group">
            <label class="col-sm-4 control-label">Contact</label>
            <div class="col-sm-8">
                <p class="form-control-static">
                    <?= if_empty($tep['receiver_contact'], '-') ?>
                </p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-4 control-label">Carrier</label>
            <div class="col-sm-8">
                <p class="form-control-static">
                    <?= if_empty($tep['receiver_name'], '-') ?>
                </p>
            </div>
        </div>
    </div>
</div>