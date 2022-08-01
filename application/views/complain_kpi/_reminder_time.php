<script id="row-reminder-template" type="text/x-custom-template">
    <div class="form-group row-reminder <?= form_error('reminders[]') == '' ?: 'has-error'; ?>">
        <div class="row">
            <div class="form-group" >
                <div class="col-md-10">
                    <label for="reminder">Reminder time </label>
                    <input type="number" class="form-control" id="reminder_detail" name="reminders[]" min="0" max="23" step='1'
                        placeholder="Enter reminder time">
                </div>
                <div class="col-md-2">
                    <br>
                    <button type="button" class="btn btn-sm btn-danger btn-remove-reminder">
                        <i class="ion-trash-b"></i>
                    </button>
                </div>
            </div>
        </div>
        
        <?= form_error('reminders[]', '<span class="help-block">', '</span>'); ?>
    </div>
</script>