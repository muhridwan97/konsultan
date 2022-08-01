<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Stock Outbound</h3>
        <div class="pull-right">
            <a href="#form-filter-stock-outbound" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_stock_outbound', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=CONTAINER" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>

    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="form-filter-stock-outbound" <?= get_url_param('filter_stock_outbound', 0) ? '' : 'style="display:none"'  ?>>
            <input type="hidden" name="filter_stock_outbound" value="1">
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
                                    <select class="form-control select2 select2-ajax" style="width: 100%"
                                        data-url="<?= site_url('people/ajax_get_people') ?>"
                                        data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>" data-add-all-customer="1"
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
                                <label for="booking_outbound">Booking Outbound</label>
                                <select class="form-control select2 select2-ajax" style="width: 100%"
                                        data-url="<?= site_url('booking/ajax_get_booking_by_keyword?type=OUTBOUND&owner=' . UserModel::authenticatedUserData('person_type')) ?>"
                                        data-key-id="id" data-key-label="no_reference" data-key-sublabel="customer_name" data-add-empty-value="ALL OUTBOUND"
                                        name="booking_outbound" id="booking_outbound"
                                        data-placeholder="Select outbound">
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
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="stock_date">Stock Date</label>
                                <input type="text" class="form-control datepicker" value="<?= get_url_param('stock_date') ?>"
                                       name="stock_date" id="stock_date" placeholder="Maximum booking and work order date" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="request_status">Request Status</label>
                                <select class="form-control select2" name="request_status" id="request_status" data-placeholder="Request status" style="width: 100%">
                                    <option value="0">All Status</option>
                                    <option value="REQUESTED"<?= get_url_param('request_status') == 'REQUESTED' ? ' selected' : '' ?>>REQUESTED</option>
                                    <option value="UNREQUESTED"<?= get_url_param('request_status') == 'UNREQUESTED' ? ' selected' : '' ?>>UNREQUESTED</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="priority">Priority</label>
                                <select class="form-control select2" name="priority" id="priority" data-placeholder="Priority" style="width: 100%">
                                    <option value="0">All Priority</option>
                                    <option value="Top Urgent"<?= get_url_param('priority') == 'Top Urgent' ? ' selected' : '' ?>>Top Urgent</option>
                                    <option value="1st"<?= get_url_param('priority') == '1st' ? ' selected' : '' ?>>1st</option>
                                    <option value="2nd"<?= get_url_param('priority') == '2nd' ? ' selected' : '' ?>>2nd</option>
                                    <option value="3rd"<?= get_url_param('priority') == '3rd' ? ' selected' : '' ?>>3rd</option>
                                    <option value="NOT SET"<?= get_url_param('priority') == 'NOT SET' ? ' selected' : '' ?>>NOT SET</option>
                                    <option value="NOT REQUESTED"<?= get_url_param('priority') == 'NOT REQUESTED' ? ' selected' : '' ?>>NOT REQUESTED</option>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="hold_status">Hold Status</label>
                                <select class="form-control select2" name="hold_status" id="hold_status" data-placeholder="Hold status" style="width: 100%">
                                    <option value="0">All Status</option>
                                    <option value="HOLD"<?= get_url_param('hold_status') == 'HOLD' ? ' selected' : '' ?>>HOLD</option>
                                    <option value="RELEASED"<?= get_url_param('hold_status') == 'RELEASED' ? ' selected' : '' ?>>RELEASED</option>
                                    <option value="NOT REQUESTED"<?= get_url_param('hold_status') == 'NOT REQUESTED' ? ' selected' : '' ?>>NOT REQUESTED</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="row">
                        <div class="col-sm-3 col-md-2">
                            <select class="form-control select2" id="data" name="data" aria-label="data" style="width: 100%">
                                <option value="stock" <?= get_url_param('data', 'stock') == 'stock' ? 'selected' : ''  ?>>STOCK ONLY</option>
                                <option value="all" <?= get_url_param('data') == 'all' ? 'selected' : ''  ?>>ALL DATA</option>
                            </select>
                        </div>
                        <div class="col-sm-3">
                            <select class="form-control select2" id="outstanding_request" name="outstanding_request" aria-label="outstanding-request" style="width: 100%">
                                <option value="0" <?= get_url_param('outstanding_request') == '0' ? 'selected' : ''  ?>>ALL DATA</option>
                                <option value="1" <?= get_url_param('outstanding_request') == '1' ? 'selected' : ''  ?>>OUTSTANDING REQUEST</option>
                            </select>
                        </div>
                        <div class="col-sm-9 col-md-7 text-right">
                            <a href="<?= site_url(uri_string(), false) ?>" type="reset" class="btn btn-default">Reset Filter</a>
                            <button type="submit" class="btn btn-primary">Apply Filter</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <table class="table table-bordered table-striped no-wrap table-ajax responsive" id="table-stock-outbound">
            <thead>
            <tr>
                <th>No</th>
                <th>Customer</th>
                <th>No Reference Inbound</th>
                <th>No Reference Outbound</th>
                <th>No Goods</th>
                <th>Whey Number</th>
                <th>Goods Name</th>
                <th>Unit</th>
                <th>Ex No Container</th>
                <th class="type-numeric">Booking</th>
                <th class="type-numeric">Requested</th>
                <th class="type-hold-status">Hold Status</th>
                <th class="type-priority-location">Unload Location</th>
                <th class="type-priority-location">Priority</th>
                <th class="type-priority-location">Priority Description</th>
                <th class="type-numeric">Work Order</th>
                <th class="type-numeric">Stock Outbound</th>
                <th class="type-numeric">Age Inbound</th>
                <th class="type-numeric">Age Outbound</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script src="<?= base_url('assets/app/js/report-stock-outbound.js?v=3') ?>" defer></script>