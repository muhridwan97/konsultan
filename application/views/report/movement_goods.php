<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Goods Movement</h3>
        <div class="pull-right">
            <a href="#form-filter" class="btn btn-info btn-filter-toggle">
                <?= get_url_param('filter', 1) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="form-filter" <?= get_url_param('filter', 1) ? '' : 'style="display:none"'  ?>>
            <input type="hidden" name="filter" value="1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Data Filters
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="customer">Customer (Required)</label>
                                <?php if(UserModel::authenticatedUserData('user_type') == 'INTERNAL'): ?>
                                    <select class="form-control select2 select2-ajax"
                                            data-url="<?= site_url('people/ajax_get_people') ?>"
                                            data-key-id="id" data-key-label="name" data-key-sublabel="outbound_type"
                                            data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                                            name="customer" id="customer"
                                            data-placeholder="Select customer" required>
                                        <option value=""></option>
                                        <?php if (!empty($selectedCustomer)): ?>
                                            <option value="<?= $selectedCustomer['id'] ?>" selected>
                                                <?= $selectedCustomer['name'] ?>
                                            </option>
                                        <?php endif; ?>
                                    </select>
                                <?php else: ?>
                                    <p class="form-control-static">
                                        <?= UserModel::authenticatedUserData('name') ?>
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="goods">Items</label>
                                <select class="form-control select2 select2-ajax"
                                        data-url="<?= site_url('goods/ajax_get_goods_by_name') ?>"
                                        data-key-id="id" data-key-label="name" data-key-sublabel="no_goods"
                                        id="goods" name="goods[]" data-placeholder="Select item" multiple>
                                    <option value=""></option>
                                    <?php if (!empty($selectedItems)): ?>
                                        <?php foreach ($selectedItems as $selectedItem): ?>
                                            <option value="<?= $selectedItem['id'] ?>" selected>
                                                <?= $selectedItem['name'] ?> - <?= $selectedItem['no_goods'] ?>
                                            </option>
                                        <?php endforeach; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_from">Date From (Required)</label>
                                <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                                       placeholder="Stock date from" required autocomplete="off"
                                       maxlength="50" value="<?= set_value('date_from', get_url_param('date_from')) ?>">
                            </div>
                        </div>
                        <div class="col-md-3">
                            <div class="form-group">
                                <label for="date_to">Date To (Required)</label>
                                <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                                       placeholder="Transaction until" required autocomplete="off"
                                       maxlength="50" value="<?= set_value('date_to', get_url_param('date_to')) ?>">
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="booking">Booking</label>
                                <select class="form-control select2 select2-ajax"
                                        data-url="<?= site_url('booking/ajax_get_booking_by_keyword?type=INBOUND&owner=' . UserModel::authenticatedUserData('person_type')) ?>"
                                        data-key-id="id" data-key-label="no_reference" data-key-sublabel="customer_name"
                                        name="booking" id="booking"
                                        data-placeholder="Select inbound booking">
                                    <option value=""></option>
                                    <?php if (!empty($selectedBooking)): ?>
                                        <option value="<?= $selectedBooking['id'] ?>" selected>
                                            <?= $selectedBooking['no_reference'] ?> - <?= $selectedBooking['customer_name'] ?>
                                        </option>
                                    <?php endif; ?>
                                </select>
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

        <table class="table table-bordered table-striped responsive no-datatable" id="table-over-space">
            <thead>
            <tr>
                <th rowspan="2" style="width: 30px">No</th>
                <th rowspan="2">Date</th>
                <th rowspan="2">Item Name</th>
                <th rowspan="2">Upload Title</th>
                <th colspan="3" class="text-center">Quantity (Unit)</th>
                <th colspan="3" class="text-center">Gross Weight (Ton)</th>
                <th colspan="3" class="text-center">Volume (M<sup>3</sup>)</th>
                <th colspan="3" class="text-center">V/W (M<sup>3</sup>/TON)</th>
            </tr>
            <tr>
                <th class="text-center">In</th>
                <th class="text-center">Out</th>
                <th class="text-center">Balance</th>
                <th class="text-center">In</th>
                <th class="text-center">Out</th>
                <th class="text-center">Balance</th>
                <th class="text-center">In</th>
                <th class="text-center">Out</th>
                <th class="text-center">Balance</th>
                <th class="text-center">In</th>
                <th class="text-center">Out</th>
                <th class="text-center">Balance</th>
            </tr>
            </thead>
            <tbody>
            <?php if(!empty($goodsMovements)): ?>
                <tr class="success">
                    <td></td>
                    <td><?= $goodsMovements['balance']['date'] ?></td>
                    <td>Beginning balance</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-center"><?= numerical($goodsMovements['balance']['quantity_balance'], 5, true) ?></td>
                    <td></td>
                    <td></td>
                    <td class="text-center"><?= numerical($goodsMovements['balance']['gross_weight_balance'], 5, true) ?></td>
                    <td></td>
                    <td></td>
                    <td class="text-center"><?= numerical($goodsMovements['balance']['volume_balance'], 5, true) ?></td>
                    <td></td>
                    <td></td>
                    <td class="text-center"><?= numerical($goodsMovements['balance']['weight_volume_balance'], 5, true) ?></td>
                </tr>
                <?php foreach ($goodsMovements['transactions'] as $index => $item): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td>
                            <?= format_date($item['date'], 'Y-m-d') ?><br>
                            <small class="text-muted text-nowrap"><?= $item['no_work_order'] ?></small>
                        </td>
                        <td>
                            <?= $item['goods_name'] ?><br>
                            <small class="text-muted text-nowrap"><?= $item['booking_type'] ?></small>
                        </td>
                        <td>
                            <?= $item['upload_title'] ?><br>
                        </td>
                        <td class="text-center"><?= numerical($item['quantity_inbound'], 5, true) ?></td>
                        <td class="text-center"><?= numerical($item['quantity_outbound'], 5, true) ?></td>
                        <td class="text-center"><?= numerical($item['quantity_balance'], 5, true) ?></td>
                        <td class="text-center"><?= numerical($item['gross_weight_inbound'], 5, true) ?></td>
                        <td class="text-center"><?= numerical($item['gross_weight_outbound'], 5, true) ?></td>
                        <td class="text-center"><?= numerical($item['gross_weight_balance'], 5, true) ?></td>
                        <td class="text-center"><?= numerical($item['volume_inbound'], 5, true) ?></td>
                        <td class="text-center"><?= numerical($item['volume_outbound'], 5, true) ?></td>
                        <td class="text-center"><?= numerical($item['volume_balance'], 5, true) ?></td>
                        <td class="text-center"><?= numerical($item['weight_volume_inbound'], 5, true) ?></td>
                        <td class="text-center"><?= numerical($item['weight_volume_outbound'], 5, true) ?></td>
                        <td class="text-center"><?= numerical($item['weight_volume_balance'], 5, true) ?></td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>
            <?php if(empty($goodsMovements)): ?>
                <tr>
                    <td colspan="16">No data available</td>
                </tr>
            <?php endif; ?>
            </tbody>
        </table>
    </div>
</div>