<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Outbound Tracking</h3>
        <div class="pull-right">
            <a href="#form-filter" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
        </div>
    </div>

    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="form-filter" <?= get_url_param('filter', 0) ? '' : 'style="display:none"'  ?>>
            <input type="hidden" name="filter" value="1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Data Filters
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="bookings">Booking</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('booking/ajax_get_booking_by_keyword?type=OUTBOUND') ?>"
                                data-key-id="id" data-key-label="no_reference" data-key-sublabel="customer_name"
                                name="bookings[]" id="bookings" multiple
                                data-placeholder="Select booking outbound">
                            <option value=""></option>
                            <?php foreach($bookings as $booking): ?>
                                <option value="<?= $booking['id'] ?>" selected>
                                    <?= $booking['no_reference'] ?> - <?= $booking['customer_name'] ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="date_type">Transaction Date</label>
                                <select class="form-control select2" id="date_type" name="date_type" data-placeholder="Select date filter">
                                    <option value="0">NO DATE FILTER</option>
                                    <option value="tanggal_order"<?= get_url_param('date_type') == 'tanggal_order' ? ' selected' : '' ?>>ORDER DATE</option>
                                    <option value="tanggal_ambil_kontainer"<?= get_url_param('date_type') == 'tanggal_ambil_kontainer' ? ' selected' : '' ?>>TAKE CONTAINER</option>
                                    <option value="checked_in_at"<?= get_url_param('date_type') == 'checked_in_at' ? ' selected' : '' ?>>CHECKED IN AT</option>
                                    <option value="checked_out_at"<?= get_url_param('date_type') == 'checked_out_at' ? ' selected' : '' ?>>CHECKED OUT AT</option>
                                    <option value="stuffing_date"<?= get_url_param('date_type') == 'stuffing_date' ? ' selected' : '' ?>>STUFFING DATE</option>
                                    <option value="tanggal_dooring"<?= get_url_param('date_type') == 'tanggal_dooring' ? ' selected' : '' ?>>SITE TRANSIT</option>
                                    <option value="site_transit_actual_date"<?= get_url_param('date_type') == 'site_transit_actual_date' ? ' selected' : '' ?>>ACTUAL SITE TRANSIT</option>
                                    <option value="tanggal_kontainer_kembali_kedepo"<?= get_url_param('date_type') == 'tanggal_kontainer_kembali_kedepo' ? ' selected' : '' ?>>UNLOADING DATE</option>
                                    <option value="unloading_actual_date"<?= get_url_param('date_type') == 'unloading_actual_date' ? ' selected' : '' ?>>UNLOADING ACTUAL DATE</option>
                                    <option value="received_date"<?= get_url_param('date_type') == 'received_date' ? ' selected' : '' ?>>RECEIVED DATE</option>
                                    <option value="driver_handover_date"<?= get_url_param('date_type') == 'driver_handover_date' ? ' selected' : '' ?>>DRIVER HANDOVER DATE</option>
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

        <table class="table table-bordered table-striped no-wrap table-ajax responsive" id="table-outbound-tracking">
            <thead>
            <tr>
                <th>No</th>
                <th>No Ref Outbound</th>
                <th>No Ref Inbound</th>
                <th>No Order</th>
                <th>Order Date</th>
                <th>No Safe Conduct</th>
                <th>No Safe Conduct Group</th>
                <th>No Safe Conduct Desc</th>
                <th>No Plat</th>
                <th>PhBid No Plat</th>
                <th>Vehicle Type</th>
                <th>PhBid Vehicle Type</th>
                <th>Driver</th>
                <th>TEP Code</th>
                <th>Tracking Link Desc</th>
                <th>No Work Order</th>
                <th>Taken At</th>
                <th>Completed At</th>
                <th>Ambil Kontainer /<br>Take Container</th>
                <th>Checked In</th>
                <th>Checked Out</th>
                <th>RM Kolam /<br>Stuffing</th>
                <th>Dooring /<br>Site Transit</th>
                <th>Site Transit Actual</th>
                <th>Site Transit Desc</th>
                <th>Kontainer Kembali Kedepo /<br>Unloading</th>
                <th>Unloading Actual</th>
                <th>Unloading Desc</th>
                <th>Received Date</th>
                <th>Driver Handover Date</th>
                <th>Handover Desc</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script src="<?= base_url('assets/app/js/report-tracking.js?v=2') ?>" defer></script>