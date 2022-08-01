<script id="row-fcl-template" type="text/x-custom-template">
    <div class="row form-group row-fcl">
        <div class="col-md-3">
            <div class="form-group <?= form_error('parties[]') == '' ?: 'has-error'; ?>">
                <label for="party">Party</label>
                <input type="number" class="form-control" name="parties[]" id="party" min='1' placeholder="Enter Total Party">
                <?= form_error('parties[]', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="col-md-3">
            <div class="form-group <?= form_error('shapes[]') == '' ?: 'has-error'; ?>">
                <label for="shape">Shape</label>
                <select class="form-control select2" id="shape" name="shapes[]"
                        data-placeholder="Select document type">
                    <option value=""></option>
                    <option value="20 Feet" <?php echo set_select('shapes[]', "20 Feet"); ?>>
                        20 Feet
                    </option>
                    <option value="40 Feet" <?php echo set_select('shapes[]', "40 Feet"); ?>>
                        40 Feet
                    </option>
                    <option value="45 Feet" <?php echo set_select('shapes[]', "45 Feet"); ?>>
                        45 Feet
                    </option>
                </select>
                <?= form_error('shapes[]', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="col-md-2">
            <br>
            <button type="button" class="btn btn-sm btn-danger btn-remove-fcl">
                <i class="ion-trash-b"></i>
            </button>
        </div>
    </div>
</script>