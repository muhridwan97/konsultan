<form role="form" method="get" class="form-filter" id="form-filter" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="form-group">
                <label for="customer">Customer</label>
                <select class="form-control select2 select2-ajax"
                        data-url="<?= site_url('people/ajax_get_people') ?>"
                        data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                        data-add-all-customer="1"
                        name="customer" id="customer"
                        data-placeholder="Select customer">
                    <option value=""></option>
                    <?php if (!empty($selectedCustomer)): ?>
                        <option value="<?= $selectedCustomer['id'] ?>" selected>
                            <?= $selectedCustomer['name'] ?>
                        </option>
                    <?php endif; ?>
                </select>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="booking_type">Booking Type</label>
                        <select class="form-control select2" name="booking_type" id="booking_type" data-placeholder="Select booking type">
                            <option value="0">ALL BOOKING TYPE</option>
                            <?php foreach ($bookingTypes as $bookingType): ?>
                                <option value="<?= $bookingType['id'] ?>"<?= get_url_param('booking_type') == $bookingType['id'] ? ' selected' : '' ?>>
                                    <?= $bookingType['booking_type'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="category">Category</label>
                        <select class="form-control select2" name="category" id="category" data-placeholder="Select category">
                            <option value="0">ALL CATEGORY</option>
                            <option value="INBOUND"<?= get_url_param('category') == 'INBOUND' ? ' selected' : '' ?>>
                                INBOUND
                            </option>
                            <option value="OUTBOUND"<?= get_url_param('category') == 'OUTBOUND' ? ' selected' : '' ?>>
                                OUTBOUND
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="ob_tps_type">OB TPS Type</label>
                        <select class="form-control select2" name="ob_tps_type" id="ob_tps_type" data-placeholder="Select ob tps">
                            <option value="0">ALL TYPE</option>
                            <option value="OB TPS"<?= get_url_param('ob_tps_type') == 'OB TPS' ? ' selected' : '' ?>>
                                OB TPS
                            </option>
                            <option value="OB TPS PERFORMA"<?= get_url_param('ob_tps_type') == 'OB TPS PERFORMA' ? ' selected' : '' ?>>
                                OB TPS PERFORMA
                            </option>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date_from">Date From</label>
                        <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                               placeholder="Create booking from"
                               maxlength="50" value="<?= set_value('date_from', get_url_param('date_from')) ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date_to">Date To</label>
                        <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                               placeholder="Create booking to"
                               maxlength="50" value="<?= set_value('date_to', get_url_param('date_to')) ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer text-right">
            <a href="<?= site_url(uri_string(), false) ?>" class="btn btn-default btn-reset-filter">Reset Filter</a>
            <button type="submit" class="btn btn-primary">Apply Filter</button>
        </div>
    </div>
</form>
