<form role="form" method="get" class="form-filter" id="filter_transporter" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter_transporter" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-7">
                    <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                        <label for="branch">Branch</label>
                        <select class="form-control select2" name="branch[]" id="branch" multiple data-placeholder="Select branch" style="width: 100%">
                            <option value=""></option>
                            <?php foreach ($allBranches as $branch): ?>
                                <option value="<?= $branch['id'] ?>" <?= in_array($branch['id'], get_url_param('branch', [])) ? 'selected' : '' ?>>
                                    <?= $branch['branch'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group <?= form_error('branch_data') == '' ?: 'has-error'; ?>">
                        <label for="branch_data">Branch Data</label>
                        <select class="form-control select2" name="branch_data" id="branch_data" data-placeholder="Select branch" style="width: 100%">
                            <option value="VEHICLE"<?= get_url_param('branch_data') == 'VEHICLE' ? ' selected' : '' ?>>
                                VEHICLE BRANCH
                            </option>
                            <option value="TRANSACTION"<?= get_url_param('branch_data') == 'TRANSACTION' ? ' selected' : '' ?>>
                                TRANSACTION BRANCH
                            </option>
                        </select>
                        <?= form_error('branch_data', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group <?= form_error('activity_type') == '' ?: 'has-error'; ?>">
                        <label for="activity_type">Activity Type</label>
                        <select class="form-control select2" name="activity_type" id="activity_type" data-placeholder="Select branch" style="width: 100%">
                            <option value="0"<?= get_url_param('activity_type') == '0' ? ' selected' : '' ?>>
                                ALL ACTIVITY
                            </option>
                            <option value="INBOUND"<?= get_url_param('activity_type') == 'INBOUND' ? ' selected' : '' ?>>
                                INBOUND
                            </option>
                            <option value="OUTBOUND"<?= get_url_param('activity_type') == 'OUTBOUND' ? ' selected' : '' ?>>
                                OUTBOUND
                            </option>
                        </select>
                        <?= form_error('activity_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group <?= form_error('vehicle_type') == '' ?: 'has-error'; ?>">
                        <label for="vehicle_type">Vehicle Type</label>
                        <select class="form-control select2" name="vehicle_type" id="vehicle_type" data-placeholder="Select vehicle type" style="width: 100%">
                            <option value="0">ALL TYPE</option>
                            <?php foreach (array_unique(array_column($allVehicles, 'vehicle_type')) as $vehicleType): ?>
                                <option value="<?= $vehicleType ?>" <?= get_url_param('vehicle_type') == $vehicleType ? 'selected' : '' ?>>
                                    <?= $vehicleType ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <?= form_error('vehicle_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group <?= form_error('vehicle') == '' ?: 'has-error'; ?>">
                        <label for="vehicle">Vehicle</label>
                        <select class="form-control select2" name="vehicle" id="vehicle" data-placeholder="Select vehicle" style="width: 100%">
                            <option value="0">ALL VEHICLE</option>
                            <?php foreach ($allVehicles as $vehicle): ?>
                                <option value="<?= $vehicle['no_plate'] ?>" data-type="<?= $vehicle['vehicle_type'] ?>" <?= get_url_param('vehicle') == $vehicle['no_plate'] ? 'selected' : '' ?>>
                                    <?= $vehicle['vehicle_name'] ?> - <?= $vehicle['no_plate'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <?= form_error('vehicle', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group <?= form_error('status') == '' ?: 'has-error'; ?>">
                        <label for="status">Vehicle Status</label>
                        <div class="row">
                            <div class="col-sm-6 col-md-5">
                                <label class="mt10" for="status_active">
                                    <input type="radio" id="status_active" name="status" value="ACTIVE" <?= get_url_param('status') == "ACTIVE" || empty(get_url_param('status')) ? 'checked' : '' ?>> Active
                                </label>
                            </div>
                            <div class="col-sm-6 col-md-5">
                                <label class="mt10" for="status_inactive">
                                    <input type="radio" id="status_inactive" name="status" value="INACTIVE" <?= get_url_param('status') == "INACTIVE" ? 'checked' : '' ?>> Inactive
                                </label>
                            </div>
                        </div>
                        <?= form_error('status', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group <?= form_error('week') == '' ?: 'has-error'; ?>">
                        <label for="week">Week (<?= if_empty( get_url_param('year'),date('Y')) ?>)</label>
                        <select class="form-control select2" name="week" id="week" data-placeholder="Select period">
                            <option value="0">ALL WEEK</option>
                            <?php foreach ($allPeriods as $period): ?>
                                <option value="<?= $period ?>" <?= get_url_param('week') == $period ? 'selected' : '' ?>>
                                    <?= $period ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <?= form_error('week', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('year') == '' ?: 'has-error'; ?>">
                <label for="year">Year</label>
                <select class="form-control select2" name="year" id="year" data-placeholder="Select branch" style="width: 100%">
                    <option value=""></option>
                    <?php for ($i=2017; $i <= date('Y') ; $i++): ?>
                        <option value="<?= $i ?>" <?= $i == get_url_param('year') ? 'selected' : '' ?>>
                            <?= $i ?>
                        </option>
                    <?php endfor ?>
                </select>
                <?= form_error('year', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-12 text-right">
                    <a href="<?= site_url('report/transporter') ?>" class="btn btn-default">Reset</a>
                    <button type="submit" class="btn btn-success" name="export" value="1">Export Report</button>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>
</form>

<script defer>
    const filterTransporter = $('#filter_transporter');
    const selectVehicleType = filterTransporter.find('#vehicle_type');
    const selectVehicle = filterTransporter.find('#vehicle');

    selectVehicleType.on('change', function () {
        const vehicleType = $(this).val();
        selectVehicle.find('option').prop('disabled', false);
        if (vehicleType && vehicleType != '0') {
            selectVehicle.find(`option[data-type!="${vehicleType}"]`).not('[value=0]').prop('disabled', true);
            if (selectVehicle.find('option:selected').data('type') != vehicleType) {
                selectVehicle.val('0').trigger('change');
            }
        }
        selectVehicle.select2();
    });

    selectVehicleType.trigger('change');
</script>
