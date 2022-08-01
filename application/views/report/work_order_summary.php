<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Container Summary</h3>
        <div class="pull-right">
            <a href="#form-filter-work-order-container" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_work_order_summary_container', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=CONTAINER" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>

    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="form-filter-work-order-container" <?= get_url_param('filter_work_order_summary_container', 0) ? '' : 'style="display:none"'  ?>>
            <input type="hidden" name="filter_work_order_summary_container" value="1">
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
                                        <?php if (!empty($customer) && get_url_param('filter_work_order_summary_container')): ?>
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
                                    <?php if (!empty($booking) && get_url_param('filter_work_order_summary_container')): ?>
                                        <option value="<?= $booking['id'] ?>" selected>
                                            <?= $booking['no_reference'] ?>
                                        </option>
                                    <?php endif ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="handling_types">Handling Types</label>
                        <select class="form-control select2" id="handling_types" name="handling_types[]" multiple data-placeholder="Select handling type">
                            <option value=""></option>
                            <?php foreach($handlingTypes as $handlingType): ?>
                                <option value="<?= $handlingType['id'] ?>"<?= set_select('handling_types', $handlingType['id'], get_url_param('filter_work_order_summary_container') ? in_array($handlingType['id'], get_url_param('handling_types', [])) : false) ?>>
                                    <?= $handlingType['handling_type'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="containers">No Container</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('container/ajax_get_container_by_no') ?>"
                                data-key-id="id" data-key-label="no_container"
                                id="containers" name="containers[]" data-placeholder="Select container" multiple>
                            <option value=""></option>
                            <?php foreach ($containers as $container): ?>
                                <option value="<?= $container['id'] ?>"<?= set_select('containers', $container['id'], get_url_param('filter_work_order_summary_container') ? in_array($container['id'], get_url_param('containers', [])) : false) ?>>
                                    <?= $container['no_container'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="date_type">Transaction Date</label>
                                <select class="form-control select2" id="date_type" name="date_type" data-placeholder="Select date filter">
                                    <option value="work_orders.created_at" <?= get_url_param('filter_work_order_summary_container') ? get_url_param('date_type') == 'work_orders.created_at' ? 'selected' : '' : '' ?>>JOB CREATED</option>
                                    <option value="work_orders.taken_at" <?= get_url_param('filter_work_order_summary_container') ? get_url_param('date_type') == 'work_orders.taken_at' ? 'selected' : '' : '' ?>>JOB TAKEN</option>
                                    <option value="work_orders.completed_at" <?= get_url_param('filter_work_order_summary_container') ? get_url_param('date_type') == 'work_orders.completed_at' ? 'selected' : '' : '' ?>>JOB COMPLETED</option>
                                    <option value="security_in_date" <?= get_url_param('filter_work_order_summary_container') ? get_url_param('date_type') == 'security_in_date' ? 'selected' : '' : '' ?>>SECURITY IN DATE</option>
                                    <option value="security_out_date" <?= get_url_param('filter_work_order_summary_container') ? get_url_param('date_type') == 'security_out_date' ? 'selected' : '' : '' ?>>SECURITY OUT DATE</option>
                                    <option value="checked_in_at" <?= get_url_param('filter_work_order_summary_container') ? get_url_param('date_type') == 'checked_in_at' ? 'selected' : '' : '' ?>>TEP CHECK IN</option>
                                    <option value="checked_out_at" <?= get_url_param('filter_work_order_summary_container') ? get_url_param('date_type') == 'checked_out_at' ? 'selected' : '' : '' ?>>TEP CHECK OUT</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="date_from">Date From</label>
                                        <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                                               placeholder="Date from"
                                               maxlength="50" value="<?= get_url_param('filter_work_order_summary_container') ? set_value('date_from', get_url_param('date_from')) : '' ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="date_to">Date To</label>
                                        <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                                               placeholder="Date to"
                                               maxlength="50" value="<?= get_url_param('filter_work_order_summary_container') ? set_value('date_to', get_url_param('date_to')) : '' ?>">
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

        <table class="table table-bordered table-striped no-wrap table-ajax responsive" id="table-work-order-summary-container">
            <thead>
            <tr>
                <th>No</th>
                <th>Customer</th>
                <th>No Reference</th>
                <th>No Booking</th>
                <th>No Handling</th>
                <th>Handling Type</th>
                <th>Handling Status</th>
                <th>No Safe Conduct</th>
                <th>No Police</th>
                <th>Security In</th>
                <th>Security Out</th>
                <th>TEP Code</th>
                <th>TEP Checked In</th>
                <th>TEP Checked Out</th>
                <th>No Work Order</th>
                <th>Taken At</th>
                <th>Completed At</th>
                <th>Taken By</th>
                <th>No Container</th>
                <th>Type</th>
                <th>Size</th>
                <th>Seal</th>
                <th class="type-empty">Is Empty</th>
                <th class="type-hold">Is Hold</th>
                <th>Status Condition</th>
                <th class="type-danger">Status Danger</th>
                <th>Description</th>
                <th>Created At</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Goods Summary</h3>
        <div class="pull-right">
            <a href="#form-filter-work-order-goods" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_work_order_summary_goods', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=GOODS" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>

    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="form-filter-work-order-goods" <?= get_url_param('filter_work_order_summary_goods', 0) ? '' : 'style="display:none"'  ?>>
            <input type="hidden" name="filter_work_order_summary_goods" value="1">
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
                                        <?php if (!empty($customer) && get_url_param('filter_work_order_summary_goods')): ?>
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
                                    <?php if (!empty($booking) && get_url_param('filter_work_order_summary_goods')): ?>
                                        <option value="<?= $booking['id'] ?>" selected>
                                            <?= $booking['no_reference'] ?>
                                        </option>
                                    <?php endif ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="handling_types">Handling Types</label>
                        <select class="form-control select2" id="handling_types" name="handling_types[]" multiple data-placeholder="Select handling type">
                            <option value=""></option>
                            <?php foreach($handlingTypes as $handlingType): ?>
                                <option value="<?= $handlingType['id'] ?>"<?= set_select('handling_types', $handlingType['id'], get_url_param('filter_work_order_summary_goods') ? in_array($handlingType['id'], get_url_param('handling_types', [])) : false) ?>>
                                    <?= $handlingType['handling_type'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="goods">Goods</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('goods/ajax_get_goods_by_name') ?>"
                                data-key-id="id" data-key-label="name"
                                name="goods[]" id="goods"
                                data-placeholder="Select goods" multiple>
                            <option value=""></option>
                            <?php foreach ($goods as $item): ?>
                                <option value="<?= $item['id'] ?>"<?= set_select('goods', $item['id'], get_url_param('filter_work_order_summary_goods') ? in_array($item['id'], get_url_param('goods', [])) : false) ?>>
                                    <?= $item['name'] ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="date_type">Transaction Date</label>
                                <select class="form-control select2" id="date_type" name="date_type" data-placeholder="Select date filter">
                                    <option value="work_orders.created_at" <?= get_url_param('filter_work_order_summary_goods') ? get_url_param('date_type') == 'work_orders.created_at' ? 'selected' : '' : '' ?>>JOB CREATED</option>
                                    <option value="work_orders.taken_at" <?= get_url_param('filter_work_order_summary_goods') ? get_url_param('date_type') == 'work_orders.taken_at' ? 'selected' : '' : '' ?>>JOB TAKEN</option>
                                    <option value="work_orders.completed_at" <?= get_url_param('filter_work_order_summary_goods') ? get_url_param('date_type') == 'work_orders.completed_at' ? 'selected' : '' : '' ?>>JOB COMPLETED</option>
                                    <option value="security_in_date" <?= get_url_param('filter_work_order_summary_goods') ? get_url_param('date_type') == 'security_in_date' ? 'selected' : '' : '' ?>>SECURITY IN DATE</option>
                                    <option value="security_out_date" <?= get_url_param('filter_work_order_summary_goods') ? get_url_param('date_type') == 'security_out_date' ? 'selected' : '' : '' ?>>SECURITY OUT DATE</option>
                                    <option value="checked_in_at" <?= get_url_param('filter_work_order_summary_goods') ? get_url_param('date_type') == 'checked_in_at' ? 'selected' : '' : '' ?>>TEP CHECK IN</option>
                                    <option value="checked_out_at" <?= get_url_param('filter_work_order_summary_goods') ? get_url_param('date_type') == 'checked_out_at' ? 'selected' : '' : '' ?>>TEP CHECK OUT</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="date_from">Date From</label>
                                        <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                                               placeholder="Date from"
                                               maxlength="50" value="<?= get_url_param('filter_work_order_summary_goods') ? set_value('date_from', get_url_param('date_from')) : '' ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="date_to">Date To</label>
                                        <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                                               placeholder="Date to"
                                               maxlength="50" value="<?= get_url_param('filter_work_order_summary_goods') ? set_value('date_to', get_url_param('date_to')) : '' ?>">
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

        <table class="table table-bordered table-striped no-wrap table-ajax responsive" id="table-work-order-summary-goods">
            <thead>
            <tr>
                <th>No</th>
                <th>Customer</th>
                <th>No Reference</th>
                <th>No Booking</th>
                <th>No Handling</th>
                <th>Handling Type</th>
                <th>Handling Status</th>
                <th>No Safe Conduct</th>
                <th>No Police</th>
                <th>Security In</th>
                <th>Security Out</th>
                <th>TEP Code</th>
                <th>TEP Checked In</th>
                <th>TEP Checked Out</th>
                <th>No Work Order</th>
                <th>Taken At</th>
                <th>Completed At</th>
                <th>Taken By</th>
                <th>SPV Name</th>
                <th>No Goods</th>
                <th>Goods Name</th>
                <th>No HS</th>
                <th>Whey Number</th>
                <th class="type-numeric">Quantity</th>
                <th class="type-numeric">Unit Tonnage</th>
                <th class="type-numeric">Total Tonnage</th>
                <th class="type-numeric">Unit Gross</th>
                <th class="type-numeric">Total Gross</th>
                <th class="type-numeric">Unit Volume</th>
                <th class="type-numeric">Total Volume</th>
                <th class="type-numeric">Length</th>
                <th class="type-numeric">Width</th>
                <th class="type-numeric">Height</th>
                <th>No Pallet</th>
                <th>Ex No Container</th>
                <th class="type-hold">Is Hold</th>
                <th>Status Condition</th>
                <th class="type-danger">Status Danger</th>
                <th>Description</th>
                <th>Created At</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script src="<?= base_url('assets/app/js/report-work-order-summary.js?v=3') ?>" defer></script>