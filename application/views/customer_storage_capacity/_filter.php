<form role="form" method="get" class="form-filter" id="form-filter" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="customer">Customer</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('people/ajax_get_people') ?>"
                                data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                                name="customer" id="customer"
                                data-placeholder="Select customer">
                            <option value=""></option>
                            <?php if (!empty($customer)): ?>
                                <option value="<?= $customer['id'] ?>" selected>
                                    <?= $customer['name'] ?>
                                </option>
                            <?php endif; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="status">Status</label>
                        <select class="form-control select2" name="status" id="status" data-placeholder="Select status">
                            <option value="0">ALL STATUS</option>
                            <option value="ACTIVE"<?= get_url_param('status', 'ACTIVE') == 'ACTIVE' ? ' selected' : '' ?>>
                                ACTIVE
                            </option>
                            <option value="PENDING"<?= get_url_param('status') == 'PENDING' ? ' selected' : '' ?>>
                                PENDING
                            </option>
                            <option value="EXPIRED"<?= get_url_param('status') == 'EXPIRED' ? ' selected' : '' ?>>
                                EXPIRED
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="effective_date_from">Effective Date From</label>
                        <input type="text" class="form-control datepicker" id="effective_date_from" name="effective_date_from"
                               placeholder="Date from"
                               maxlength="50" value="<?= set_value('effective_date_from', get_url_param('effective_date_from')) ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="effective_date_to">Effective Date To</label>
                        <input type="text" class="form-control datepicker" id="effective_date_to" name="effective_date_to"
                               placeholder="Date to"
                               maxlength="50" value="<?= set_value('effective_date_to', get_url_param('effective_date_to')) ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer text-right">
            <a href="<?= site_url(uri_string(), false) ?>" class="btn btn-default btn-reset-filter">Reset Filter</a>
            <button type="submit" class="btn btn-success" name="export" value="1">Export</button>
            <button type="submit" class="btn btn-primary">Apply Filter</button>
        </div>
    </div>
</form>
