<form role="form" method="get" class="form-filter" id="form-filter-pallet" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter_pallet" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-12">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="date_type">Date From</label>
                                <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                                       placeholder="Date from"
                                       maxlength="50" value="<?= get_url_param('filter_pallet') ? set_value('date_from', get_url_param('date_from')) : date('01 F Y') ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="date_type">Date To</label>
                                <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                                       placeholder="Date to"
                                       maxlength="50" value="<?= get_url_param('filter_pallet') ? set_value('date_to', get_url_param('date_to')) : date('t F Y') ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-12 text-right">
                    <button type="reset" class="btn btn-default btn-reset-filter">Reset Filter</button>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>
</form>
