<form role="form" method="get" class="form-filter" id="form-filter-queue-tep" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter_queue_tep" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div>
                <label for="date_type">Date</label>
                <input type="text" class="form-control datepicker" id="expired_date" name="expired_date"
                        placeholder="Date"
                        maxlength="50" value="<?= get_url_param('filter_queue_tep') ? set_value('expired_date', get_url_param('expired_date')) : '' ?>">
            </div>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-12 text-right">
                    <button type="reset" class="btn btn-default btn-reset-filter-queue-tep">Reset Filter</button>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>
</form>
