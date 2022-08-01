<form role="form" method="get" class="form-filter" id="form-filter-work-order-overtime" <?= (isset($hidden) && $hidden) ? 'style="display:none"' : ''  ?>>
    <input type="hidden" name="filter_work_order_overtime" value="1">
    <div class="panel panel-primary">
        <div class="panel-heading">
            Data Filters
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="customers">Customer</label>
                        <?php if(UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
                            <select class="form-control select2 select2-ajax"
                                    data-url="<?= site_url('people/ajax_get_people') ?>"
                                    data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                                    name="customers" id="customers"
                                    data-placeholder="Select customer">
                                <option value=""></option>
                                <?php if (!empty($customer)): ?>
                                    <option value="<?= $customer['id'] ?>" selected>
                                        <?= $customer['name'] ?>
                                    </option>
                                <?php endif ?>
                            </select>
                        <?php else: ?>
                            <p class="form-control-static">
                                <?= UserModel::authenticatedUserData('name') ?>
                                (<?= UserModel::authenticatedUserData('email') ?>)
                            </p>
                        <?php endif ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="bookings">Booking</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('booking/ajax_get_booking_by_keyword') ?>"
                                data-key-id="id" data-key-label="no_reference" data-key-sublabel="customer_name"
                                name="bookings" id="bookings"
                                data-placeholder="Select booking">
                            <option value=""></option>
                            <?php if (!empty($booking)): ?>
                                <option value="<?= $booking['id'] ?>" selected>
                                    <?= $booking['no_reference'] ?>
                                </option>
                            <?php endif ?>
                        </select>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="job_data">Job Data</label>
                        <select class="form-control select2" id="job_data" name="job_data" data-placeholder="Select job data">
                            <option value="0">ALL JOB</option>
                            <option value="OVERTIME" <?= get_url_param('job_data', key_exists('job_data', $_GET) ? '' : 'OVERTIME') == 'OVERTIME' ? 'selected' : '' ?>>OVERTIME ONLY</option>
                            <option value="NON OVERTIME" <?= get_url_param('job_data') == 'NON OVERTIME' ? 'selected' : '' ?>>NON OVERTIME ONLY</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="status">Status</label>
                                <select class="form-control select2" id="status" name="status" data-placeholder="Select status">
                                    <option value="0">ALL OVERTIME</option>
                                    <option value="PENDING" <?= get_url_param('status') == 'PENDING' ? 'selected' : '' ?>>PENDING</option>
                                    <option value="VALIDATED" <?= get_url_param('status') == 'VALIDATED' ? 'selected' : '' ?>>VALIDATED (Charged)</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="charged_to">Charged To</label>
                                <select class="form-control select2" id="charged_to" name="charged_to" data-placeholder="Select charged to">
                                    <option value="0">ALL CHARGED</option>
                                    <option value="OPERATIONAL" <?= get_url_param('charged_to') == 'OPERATIONAL' ? 'selected' : '' ?>>OPERATIONAL</option>
                                    <option value="CUSTOMER" <?= get_url_param('charged_to') == 'CUSTOMER' ? 'selected' : '' ?>>CUSTOMER</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="date_type">Transaction Date</label>
                        <select class="form-control select2" id="date_type" name="date_type" data-placeholder="Select date filter">
                            <option value="work_order_overtime_charges.created_at" <?= get_url_param('date_type') == 'work_order_overtime_charges.created_at' ? 'selected' : '' ?>>OVERTIME VALIDATED</option>
                            <option value="work_orders.taken_at" <?= get_url_param('date_type') == 'work_orders.taken_at' ? 'selected' : '' ?>>JOB TAKEN</option>
                            <option value="work_orders.completed_at" <?= get_url_param('date_type') == 'work_orders.completed_at' ? 'selected' : '' ?>>JOB COMPLETED</option>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_from">Date From</label>
                        <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                               placeholder="Date from"
                               maxlength="50" value="<?= set_value('date_from', get_url_param('date_from')) ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="date_to">Date To</label>
                        <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                               placeholder="Date to"
                               maxlength="50" value="<?= set_value('date_from', get_url_param('date_to')) ?>">
                    </div>
                </div>
            </div>
        </div>
        <div class="panel-footer text-right">
            <a href="<?= site_url(uri_string(), false) ?>" type="reset" class="btn btn-default">Reset Filter</a>
            <button type="submit" class="btn btn-primary">Apply Filter</button>
        </div>
    </div>
</form>