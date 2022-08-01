<form role="form" method="get" class="form-filter" id="filter_forklift" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter_forklift" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
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
                    <!-- <button type="submit" class="btn btn-success" name="export" value="1">Export Report</button> -->
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>
</form>
<!-- 
<script defer>
    const filterTransporter = $('#filter_forklift');
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
</script> -->
