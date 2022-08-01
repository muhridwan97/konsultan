<form action="<?= site_url('client_area/invoice') ?>" id="form-track-invoice">
    <div class="form-group">
        <label for="bl">BL Number</label>
        <input type="text" name="bl" id="bl" class="form-control"
               value="<?= get_url_param('bl') ?>" placeholder="BL reference number">
    </div>
    <div class="form-group">
        <label for="no_container">Container Number</label>
        <input type="text" name="no_container" id="no_container" class="form-control" required
               value="<?= get_url_param('no_container') ?>" placeholder="Related container number">
        <span class="help-block">Input container number without dash or another symbol character Eg. TCLU3412</span>
    </div>
    <div class="form-group text-right">
        <button type="submit" class="btn btn-primary btn-flat btn-track-invoice">Track Invoice</button>
    </div>
</form>