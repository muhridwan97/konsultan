<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Mutation Goods</h3>
        <div class="pull-right">
            <a href="#form-filter-mutation-goods" class="btn btn-primary btn-filter-toggle">
                Hide Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=GOODS"
               class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="form-filter-mutation-goods">
            <input type="hidden" name="filter_goods" value="1">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Data Filters
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-6">
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
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="goods">Goods</label>
                                <select class="form-control select2 select2-ajax"
                                        data-url="<?= site_url('goods/ajax_get_goods_by_name') ?>"
                                        data-key-id="id" data-key-label="name"
                                        name="goods[]" id="goods"
                                        data-placeholder="Select goods" multiple>
                                    <option value=""></option>
                                    <?php foreach ($goods as $item): ?>
                                        <option value="<?= $item['id'] ?>" selected>
                                            <?= $item['name'] ?>
                                        </option>
                                    <?php endforeach ?>
                                </select>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="bookings">Booking</label>
                        <select class="form-control select2 select2-ajax"
                                data-url="<?= site_url('booking/ajax_get_booking_by_keyword?owner=' . UserModel::authenticatedUserData('person_type')) ?>"
                                data-key-id="id" data-key-label="no_reference" data-key-sublabel="customer_name"
                                name="bookings" id="bookings"
                                data-placeholder="Select booking">
                            <option value=""></option>
                            <?php if (!empty($booking) && get_url_param('filter_goods')): ?>
                                <option value="<?= $booking['id'] ?>" selected>
                                    <?= $booking['no_reference'] ?>
                                </option>
                            <?php endif ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <div class="row">
                            <div class="col-md-6">
                                <label for="date_from">Date From</label>
                                <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                                       placeholder="Completed work order from"
                                       maxlength="50" value="<?= set_value('date_from', get_url_param('date_from')) ?>">
                            </div>
                            <div class="col-md-6">
                                <label for="date_to">Date To</label>
                                <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                                       placeholder="Completed work order to"
                                       maxlength="50" value="<?= set_value('date_to', get_url_param('date_to')) ?>">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer text-right">
                    <button type="reset" class="btn btn-default" id="btn-reset-filter">Reset Filter</button>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </form>

        <?php foreach ($stockMutationGoods as $goods): ?>
            <?php $no = 1; ?>
            <?php $goodsName = key_exists(0, $goods) ? $goods[0]['goods_name'] : '' ?>
            <?php $noGoods = key_exists(0, $goods) ? $goods[0]['no_goods'] : '' ?>

            <div class="panel panel-default" id="<?= $noGoods ?>">
                <div class="panel-heading">
                    <?= $goodsName ?>
                </div>
                <div class="panel-body">
                    <div class="table-responsive">
                        <table class="table table-bordered no-wrap no-datatable mb0">
                            <thead>
                            <tr>
                                <th style="width: 25px">No</th>
                                <th>Owner</th>
                                <th>Reference</th>
                                <th>Handling</th>
                                <th>Date</th>
                                <th>No Goods</th>
                                <th>Goods Name</th>
                                <th>Ex Container</th>
                                <th class="text-right">Qty Debit</th>
                                <th class="text-right">Qty Credit</th>
                                <th class="text-right">Qty Balance</th>
                                <th class="text-right">Weight Debit</th>
                                <th class="text-right">Weight Credit</th>
                                <th class="text-right">Weight Balance</th>
                                <th class="text-right">Gross Weight Debit</th>
                                <th class="text-right">Gross Weight Credit</th>
                                <th class="text-right">Gross Weight Balance</th>
                                <th class="text-right">Volume Debit</th>
                                <th class="text-right">Volume Credit</th>
                                <th class="text-right">Volume Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $lastBalance = 0;
                            $lastBalanceWeight = 0;
                            $lastBalanceGrossWeight = 0;
                            $lastBalanceVolume = 0; ?>
                            <?php foreach ($goods as $item): ?>
                                <?php
                                $lastBalance += $item['quantity'];
                                $lastBalanceWeight += $item['total_weight'];
                                $lastBalanceGrossWeight += $item['total_gross_weight'];
                                $lastBalanceVolume += $item['total_volume'];
                                $rowClass = [
                                    -1 => 'danger',
                                    0 => 'default',
                                    1 => 'default',
                                ]
                                ?>
                                <tr class="<?= $rowClass[$item['multiplier_goods']] ?>">
                                    <td><?= $no++ ?></td>
                                    <td><?= $item['owner_name'] ?></td>
                                    <td><?= $item['no_reference'] ?></td>
                                    <td>
                                        <a href="<?= site_url('work-order/view/' . $item['id_work_order']) ?>">
                                            <?= $item['handling_type'] ?>
                                        </a>
                                    </td>
                                    <td><?= format_date($item['completed_at'], 'd F Y') ?></td>
                                    <td><?= $item['no_goods'] ?></td>
                                    <td><?= $item['goods_name'] ?></td>
                                    <td><?= if_empty($item['ex_no_container'], '-') ?></td>
                                    <td class="text-right"><?= if_empty(numerical($item['quantity_debit'], 3, true), '') ?></td>
                                    <td class="text-right"><?= if_empty(numerical($item['quantity_credit'], 3, true), '') ?></td>
                                    <td class="text-right"><?= $lastBalance ?></td>
                                    <td class="text-right"><?= if_empty(numerical($item['weight_debit'], 3, true), '') ?></td>
                                    <td class="text-right"><?= if_empty(numerical($item['weight_credit'], 3, true), '') ?></td>
                                    <td class="text-right"><?= $lastBalanceWeight ?></td>
                                    <td class="text-right"><?= if_empty(numerical($item['gross_weight_debit'], 3, true), '') ?></td>
                                    <td class="text-right"><?= if_empty(numerical($item['gross_weight_credit'], 3, true), '') ?></td>
                                    <td class="text-right"><?= $lastBalanceGrossWeight ?></td>
                                    <td class="text-right"><?= if_empty(numerical($item['volume_debit'], 3, true), '') ?></td>
                                    <td class="text-right"><?= if_empty(numerical($item['volume_credit'], 3, true), '') ?></td>
                                    <td class="text-right"><?= $lastBalanceVolume ?></td>
                                </tr>
                            <?php endforeach; ?>
                            <tr>
                                <td colspan="8"><strong>Total Stock</strong></td>
                                <td colspan="2"><strong>Quantity</strong></td>
                                <td class="text-right"><strong><?= numerical($lastBalance, 3, true) ?></strong></td>
                                <td colspan="2"><strong>Weight</strong></td>
                                <td class="text-right"><strong><?= numerical($lastBalanceWeight, 3, true) ?></strong></td>
                                <td colspan="2"><strong>Gross Weight</strong></td>
                                <td class="text-right"><strong><?= numerical($lastBalanceGrossWeight, 3, true) ?></strong></td>
                                <td colspan="2"><strong>Volume</strong></td>
                                <td class="text-right"><strong><?= numerical($lastBalanceVolume, 3, true) ?></strong></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>

<?php if (get_url_param('filter_goods') && empty($stockMutationGoods)): ?>
    <div class="panel">
        <div class="panel-body">
            <p class="lead mb0">
                No data mutation available
            </p>
        </div>
    </div>
<?php endif; ?>
