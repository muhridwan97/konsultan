<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Heavy Equipment Usage</h3>
        <div class="pull-right">
            <a href="#form-filter-heavy-equipment" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_heavy_equipment', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>

    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="form-filter-heavy-equipment" <?= get_url_param('filter_heavy_equipment', 0) ? '' : 'style="display:none"'  ?>>
            <input type="hidden" name="filter_heavy_equipment" value="1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Data Filters
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customers">Customer</label>
                                <select class="form-control select2 select2-ajax"
                                        data-url="<?= site_url('people/ajax_get_people') ?>"
                                        data-add-all-customer="true"
                                        data-add-empty-value="NO CUSTOMER" data-empty-value="-1"
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
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="bookings">Booking</label>
                                <select class="form-control select2 select2-ajax"
                                        data-url="<?= site_url('booking/ajax_get_booking_by_keyword?type=_all') ?>"
                                        data-add-empty-value="ALL BOOKING" data-empty-value="0"
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
                                <label for="branches">Branch</label>
                                <select class="form-control select2" id="branches" name="branches" data-placeholder="Select branch">
                                    <option value=""></option>
                                    <option value="0">ALL BRANCH</option>
                                    <?php foreach($branches as $branch): ?>
                                        <option value="<?= $branch['id'] ?>"<?= set_select('branches', $branch['id'], $branch['id'] == get_url_param('branches')) ?>>
                                            <?= $branch['branch'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="handling_types">Handling Types</label>
                                <select class="form-control select2" id="handling_types" name="handling_types[]" multiple data-placeholder="Select handling type">
                                    <option value=""></option>
                                    <?php foreach($handlingTypes as $handlingType): ?>
                                        <option value="<?= $handlingType['id'] ?>"<?= set_select('handling_types', $handlingType['id'], in_array($handlingType['id'], get_url_param('handling_types', []))) ?>>
                                            <?= $handlingType['handling_type'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="date_type">Transaction Date</label>
                                <select class="form-control select2" id="date_type" name="date_type" data-placeholder="Select date filter">
                                    <option value="requisitions.created_at" <?= get_url_param('date_type') == 'requisitions.created_at' ? 'selected' : '' ?>>REQUEST DATE</option>
                                    <option value="work_orders.taken_at" <?= get_url_param('date_type') == 'work_orders.taken_at' ? 'selected' : '' ?>>JOB TAKEN</option>
                                    <option value="work_orders.completed_at" <?= get_url_param('date_type') == 'work_orders.completed_at' ? 'selected' : '' ?>>JOB COMPLETED</option>
                                </select>
                            </div>
                            <div class="col-md-6">
                                <div class="row">
                                    <div class="col-md-6">
                                        <label for="date_from">Date From</label>
                                        <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                                               placeholder="Date from"
                                               maxlength="50" value="<?= set_value('date_from', get_url_param('date_from')) ?>">
                                    </div>
                                    <div class="col-md-6">
                                        <label for="date_to">Date To</label>
                                        <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                                               placeholder="Date to"
                                               maxlength="50" value="<?= set_value('date_to', get_url_param('date_to')) ?>">
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

        <table class="table table-bordered table-striped table-ajax responsive no-wrap" id="table-heavy-equipment-usage">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Request Date</th>
                <th>No Request</th>
                <th>Item Category</th>
                <th>No Purchase Order</th>
                <th>Branch</th>
                <th class="type-date-time">Taken At</th>
                <th class="type-date-time">Completed At</th>
                <th>Item Name</th>
                <th>Reference In</th>
                <th>Reference Out</th>
                <th>Customer Name</th>
                <th>Handling Type</th>
                <th>No Work Order</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script src="<?= base_url('assets/app/js/report-heavy-equipment.js') ?>" defer></script>