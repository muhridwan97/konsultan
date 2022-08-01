<form action="" role="form" method="POST" class="form-filter" id="form-lock-tally-history" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="lock_tally_history" value="1">
    <input type="hidden" name="url_param" id="url_param" value="">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Lock Tally
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date_from_locked">Date From</label>
                        <input type="text" class="form-control datepicker" id="date_from_locked" name="date_from_locked"
                               placeholder="Date from" autocomplete="off"
                               maxlength="50" >
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date_to_locked">Date to</label>
                        <input type="text" class="form-control datepicker" id="date_to_locked" name="date_to_locked"
                               placeholder="Date to" autocomplete="off"
                               maxlength="50" >
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="customer_locked">Customer</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('people/ajax_get_people') ?>"
                                data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                                name="customer_locked[]" id="customer_locked"
                                data-placeholder="Select Customer" multiple>
                            <option value=""></option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="handling_type_locked">Handling Type</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('handling_type/ajax_get_handling_types') ?>"
                                data-key-id="id" data-key-label="handling_type" name="handling_type_locked[]" id="handling_type_locked"
                                data-placeholder="Select handling type" multiple>
                            <option value=""></option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <label for="no_work_order_locked">No Job</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('work_order/ajax_get_work_orders') ?>"
                                data-key-id="id" data-key-label="no_work_order" name="no_work_order_locked[]" id="no_work_order_locked"
                                data-placeholder="Select no job" multiple>
                            <option value=""></option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer">
            <div class="row">
                <div class="col-sm-12 text-right">
                    <button type="reset" class="btn btn-default btn-reset-filter-lock-tally-history">Reset</button>
                    <a href="#" class="btn btn-primary btn-submit-unlock-tally-history">Unlock</a>
                    <a href="#" class="btn btn-primary btn-submit-lock-tally-history">Lock</a>
                </div>
            </div>
        </div>
    </div>
</form>
