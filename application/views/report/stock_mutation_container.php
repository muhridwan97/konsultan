<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Mutation Containers</h3>
        <div class="pull-right">
            <a href="#form-filter-mutation-container" class="btn btn-primary btn-filter-toggle">
                Hide Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=CONTAINER" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <form role="form" method="get" class="form-filter" id="form-filter-mutation-container">
            <input type="hidden" name="filter_container" value="1">
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
                                        <?php endforeach; ?>
                                    </select>
                                <?php else: ?>
                                    <p class="form-control-static">
                                        <?= UserModel::authenticatedUserData('name') ?>
                                        (<?= UserModel::authenticatedUserData('email') ?>)
                                    </p>
                                <?php endif; ?>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label for="container">No Container</label>
                                <select class="form-control select2 select2-ajax"
                                        data-url="<?= site_url('container/ajax_get_container_by_no') ?>"
                                        data-key-id="id" data-key-label="no_container"
                                        id="container" name="container[]" data-placeholder="Select container" multiple>
                                    <option value=""></option>
                                    <?php foreach ($containers as $container): ?>
                                        <option value="<?= $container['id'] ?>" selected>
                                            <?= $container['no_container'] ?>
                                        </option>
                                    <?php endforeach; ?>
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
                <div class="panel-footer text-right">
                    <button type="reset" class="btn btn-default" id="btn-reset-filter">Reset Filter</button>
                    <button type="submit" class="btn btn-primary">Apply Filter</button>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <?php foreach ($stockMutationContainers as $containers): ?>
                <?php $no = 1; ?>
                <?php $noContainer = key_exists(0, $containers) ? $containers[0]['no_container'] : '' ?>

                <div class="panel panel-default" id="<?= $noContainer ?>">
                    <div class="panel-heading">
                        <?= $noContainer ?>
                    </div>
                    <div class="panel-body">
                        <table class="table table-bordered no-wrap no-datatable mb0">
                            <thead>
                            <tr>
                                <th style="width: 25px">No</th>
                                <th>Owner</th>
                                <th>Reference</th>
                                <th>Handling</th>
                                <th>Date</th>
                                <th>No Container</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th class="text-right">Debit</th>
                                <th class="text-right">Credit</th>
                                <th class="text-right">Balance</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $lastBalance = 0 ?>
                            <?php foreach ($containers as $container): ?>
                                <?php $lastBalance += $container['quantity'] ?>
                                <?php
                                $rowClass = [
                                    -1 => 'danger',
                                    0 => 'default',
                                    1 => 'default',
                                ]
                                ?>
                                <tr class="<?= $rowClass[$container['multiplier_container']] ?>">
                                    <td><?= $no++ ?></td>
                                    <td><?= $container['owner_name'] ?></td>
                                    <td><?= $container['no_reference'] ?></td>
                                    <td>
                                        <a href="<?= site_url('work-order/view/' . $container['id_work_order']) ?>">
                                            <?= $container['handling_type'] ?>
                                        </a>
                                    </td>
                                    <td><?= format_date($container['completed_at'], 'd F Y') ?></td>
                                    <td><?= $container['no_container'] ?></td>
                                    <td><?= if_empty($container['container_type'], '-') ?></td>
                                    <td><?= if_empty($container['container_size'], '-') ?></td>
                                    <td class="text-right"><?= if_empty(numerical($container['quantity_debit'], 3, true), '') ?></td>
                                    <td class="text-right"><?= if_empty(numerical($container['quantity_credit'], 3, true), '') ?></td>
                                    <td class="text-right"><?= $lastBalance ?></td>
                                </tr>
                            <?php endforeach ?>
                            <tr>
                                <td colspan="10"><strong>Total Stock</strong></td>
                                <td class="text-right"><strong><?= $lastBalance ?></strong></td>
                            </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endforeach ?>
        </div>
    </div>
</div>

<?php if(get_url_param('filter_container') && empty($stockMutationContainers)): ?>
    <div class="panel">
        <div class="panel-body">
            <p class="lead mb0">
                No data mutation available
            </p>
        </div>
    </div>
<?php endif ?>
