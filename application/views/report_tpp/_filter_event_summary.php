<form role="form" method="get" class="form-filter"
      id="form-filter-container" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : '' ?>>
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="form-group">
                <div class="row">
                    <div class="col-md-6">
                        <label for="date_type">Transaction Date</label>
                        <select class="form-control select2" id="date_type" name="date_type"
                                data-placeholder="Select date filter">
                            <option value=""></option>
                            <option value="booking_news_date" <?= get_url_param('date_type') == 'booking_news_date' ? 'selected' : '' ?>>
                                EVENT DATE
                            </option>
                            <option value="bc11_date" <?= get_url_param('date_type') == 'bc11_date' ? 'selected' : '' ?>>
                                BC 11 DATE
                            </option>
                            <option value="reference_date" <?= get_url_param('date_type') == 'reference_date' ? 'selected' : '' ?>>
                                BC 15 DATE
                            </option>
                        </select>
                    </div>
                    <div class="col-md-6">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="date_type">Date From</label>
                                <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                                       placeholder="Date from"
                                       maxlength="50" value="<?= set_value('date_from', get_url_param('date_from')) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="date_type">Date To</label>
                                <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                                       placeholder="Date to"
                                       maxlength="50" value="<?= set_value('date_to', get_url_param('date_to')) ?>">
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-12 text-right">
                    <button type="reset" class="btn btn-default" id="btn-reset-filter">Reset Filter</button>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </div>
    </div>
</form>
