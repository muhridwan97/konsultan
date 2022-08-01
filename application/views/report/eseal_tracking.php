<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Eseal Tracking</h3>
        <div class="pull-right">
            <a href="#form-filter-eseal-tracking" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_work_order_summary_container', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=CONTAINER" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>

    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="form-filter-eseal-tracking" <?= get_url_param('filter_eseal_tracking', 0) ? '' : 'style="display:none"'  ?>>
            <input type="hidden" name="filter_eseal_tracking" value="1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Data Filters
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer">Customer</label>
                                <?php if(UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
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
                                <label for="booking">Booking</label>
                                <select class="form-control select2 select2-ajax"
                                        data-url="<?= site_url('booking/ajax_get_booking_by_keyword') ?>"
                                        data-key-id="id" data-key-label="no_reference" data-key-sublabel="customer_name"
                                        name="booking" id="booking"
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
                    <div class="form-group">
                        <label for="status_tracking">Status Tracking</label>
                        <select class="form-control select2" id="status_tracking" name="status_tracking[]" multiple data-placeholder="Select status">
                            <?php
                            $statuses = [
                                SafeConductModel::TRACKING_NO_ESEAL,
                                SafeConductModel::TRACKING_EMPTY_ROUTE,
                                SafeConductModel::TRACKING_SUSPECTED,
                                SafeConductModel::TRACKING_START_ONLY,
                                SafeConductModel::TRACKING_STOP_ONLY,
                                SafeConductModel::TRACKING_NORMAL,
                            ]
                            ?>
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?= $status ?>" <?= in_array($status, get_url_param('status_tracking', [])) ? 'selected' : '' ?>>
                                    <?= $status ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="date_type">Filter Date</label>
                                <select class="form-control select2" id="date_type" name="date_type" data-placeholder="Select date filter">
                                    <option value="safe_conducts.created_at" <?= get_url_param('date_type') == 'safe_conducts.created_at' ? 'selected' : '' ?>>SAFE CONDUCT CREATED</option>
                                    <option value="safe_conducts.security_in_date" <?= get_url_param('date_type') == 'safe_conducts.security_in_date' ? 'selected' : '' ?>>SECURITY START</option>
                                    <option value="safe_conducts.security_out_date" <?= get_url_param('date_type') == 'safe_conducts.security_out_date' ? 'selected' : '' ?>>SECURITY STOP</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="date_from">Date From</label>
                                        <input type="text" class="form-control datepicker" id="date_from" name="date_from" placeholder="Date from"
                                               maxlength="50" readonly value="<?= set_value('date_from', get_url_param('date_from')) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="date_to">Date To</label>
                                        <input type="text" class="form-control datepicker" id="date_to" name="date_to" placeholder="Date to"
                                               maxlength="50" readonly value="<?= set_value('date_to', get_url_param('date_to')) ?>">
                                    </div>
                                </div>
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

        <table class="table table-bordered table-striped no-wrap table-ajax responsive" id="table-eseal-tracking">
            <thead>
            <tr>
                <th>No</th>
                <th>Customer</th>
                <th>No Reference</th>
                <th class="type-safe-conduct">No Safe Conduct</th>
                <th class="type-eseal">No Eseal</th>
                <th>Driver</th>
                <th>No Police</th>
                <th>Expedition</th>
                <th>Security Start</th>
                <th>Security Stop</th>
                <th class="type-loading">Loading</th>
                <th class="type-source">Source Warehouse</th>
                <th class="type-destination">Destination Warehouse</th>
                <th>Status Tracking</th>
                <th class="type-numeric">Total Route</th>
                <th class="type-numeric">Total Distance (M)</th>
                <th class="type-action">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script src="<?= base_url('assets/app/js/report-eseal-tracking.js?v=1') ?>" defer></script>
