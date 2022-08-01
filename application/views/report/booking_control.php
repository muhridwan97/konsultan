<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Report Control</h3>
        <div class="pull-right">
            <a href="#form-filter-control" class="btn btn-primary btn-filter-toggle">
                Hide Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="form-filter-control">
            <input type="hidden" name="filter_control" value="1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Data Filters
                </div>
                <div class="panel-body">
                    <div class="form-group">
                        <label for="owner">Owner</label>
                        <?php if(UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
                            <select class="form-control select2 select2-ajax"
                                    data-url="<?= site_url('people/ajax_get_people') ?>"
                                    data-key-id="id" data-key-label="name" data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                                    name="owner[]" id="owner"
                                    data-placeholder="Select owner" multiple>
                                <option value=""></option>
                                <?php foreach ($owners as $owner): ?>
                                    <option value="<?= $owner['id'] ?>" selected>
                                        <?= $owner['name'] ?>
                                    </option>
                                <?php endforeach ?>
                            </select>
                        <?php else: ?>
                            <p class="form-control-static">
                                <?= UserModel::authenticatedUserData('name') ?>
                                (<?= UserModel::authenticatedUserData('email') ?>)
                            </p>
                        <?php endif ?>
                    </div>
                    <div class="form-group">
                        <label for="status_control">Control Status</label>
                        <select class="form-control select2" name="status_control[]" id="status_control" data-placeholder="Select status" multiple>
                            <?php $statuses = array_merge(['OUTSTANDING'], BookingControlStatusModel::CONTROL_STATUSES) ?>
                            <?php foreach ($statuses as $status): ?>
                                <option value="<?= $status ?>"<?= set_select('status_control', $status, in_array($status, get_if_exist($_GET, 'status_control', []))) ?>>
                                    <?= $status ?>
                                </option>
                            <?php endforeach ?>
                        </select>
                        <?= form_error('status_control', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <a href="<?= site_url(uri_string(), false) ?>" type="reset" class="btn btn-default">Reset Filter</a>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </form>

        <?php if(!empty($bookings)): ?>
            <table class="table table-bordered table-striped responsive">
                <thead>
                <tr>
                    <th>No</th>
                    <th>Customer</th>
                    <th>Type</th>
                    <th>No Booking</th>
                    <th>No Ref In/Out</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Control</th>
                    <th style="width: 60px">Action</th>
                </tr>
                </thead>
                <tbody>
                <?php
                $statuses = [
                    BookingModel::STATUS_BOOKED => 'default',
                    BookingModel::STATUS_REJECTED => 'danger',
                    BookingModel::STATUS_APPROVED => 'success',
                    BookingControlStatusModel::STATUS_CANCELED => 'danger',
                    BookingControlStatusModel::STATUS_PENDING => 'default',
                    BookingControlStatusModel::STATUS_DRAFT => 'warning',
                    BookingControlStatusModel::STATUS_DONE => 'primary',
                    BookingControlStatusModel::STATUS_CLEAR => 'success',
                ];
                ?>
                <?php foreach ($bookings as $index => $booking): ?>
                    <tr class="<?= $booking['category'] == 'INBOUND' ? '' : 'danger' ?>">
                        <td><?= $index + 1 ?></td>
                        <td><?= $booking['customer_name'] ?></td>
                        <td>
                            <?= $booking['category'] ?><br>
                            <?= $booking['booking_type'] ?>
                        </td>
                        <td>
                            <?= $booking['no_booking'] ?><br>
                            <small class="text-muted"><?= $booking['no_reference'] ?></small>
                        </td>
                        <td>
                            <?= $booking['no_reference_in'] ?>
                            <?= str_replace(',', '<br>', $booking['no_reference_out']) ?>
                        </td>
                        <td><?= readable_date($booking['booking_date']) ?></td>
                        <td>
                            <span class="label label-<?= get_if_exist($statuses, $booking['status'], 'default') ?>">
                                <?= $booking['status'] ?>
                            </span>
                        </td>
                        <td>
                            <span class="label label-<?= get_if_exist($statuses, $booking['status_control'], 'default') ?>">
                                <?= $booking['status_control'] ?>
                            </span>
                        </td>
                        <td>
                            <a href="<?= site_url('report/booking-control-detail?booking=' . $booking['id']) ?>"
                               class="btn btn-primary btn-sm">
                                VIEW
                            </a>
                        </td>
                    </tr>
                <?php endforeach ?>
                </tbody>
            </table>
        <?php endif ?>

    </div>
</div>