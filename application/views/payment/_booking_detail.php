<?php if (!empty($bookingContainers) || !empty($bookingGoods)) { ?>
    <div class="form-group <?= form_error('status_danger') == '' ?: 'has-error'; ?>"
         id="booking-detail-data">
        <?php if (!empty($bookingContainers)) { ?>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Containers</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped no-datatable">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Container No</th>
                            <th>Container Size</th>
                            <th>Container Type</th>
                            <th>Initial Status</th>
                            <?php if ($page == 'payment/realization') { ?>
                                <th width="200px">Status</th>
                            <?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($bookingContainers as $container): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $container['no_container'] ?></td>
                                <td><?= $container['size'] ?></td>
                                <td><?= $container['type'] ?></td>
                                <td><?= $container['status_danger'] ?></td>
                                <?php if ($page == 'payment/realization') { ?>
                                    <td>
                                        <select class="select2" name="status_container[<?= $container['id'] ?>]"
                                                id="status_all"
                                                data-placeholder="Status">
                                            <option value=""></option>
                                            <option value="NOT DANGER" <?= $container['status_danger'] == 'NOT DANGER' ? 'selected' : '' ?>>
                                                NOT DANGER
                                            </option>
                                            <option value="DANGER TYPE 1" <?= $container['status_danger'] == 'DANGER TYPE 1' ? 'selected' : '' ?>>
                                                DANGER TYPE 1
                                            </option>
                                            <option value="DANGER TYPE 2" <?= $container['status_danger'] == 'DANGER TYPE 2' ? 'selected' : '' ?>>
                                                DANGER TYPE 2
                                            </option>
                                        </select>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>

        <?php if (!empty($bookingGoods)) { ?>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Goods</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped no-datatable">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Goods</th>
                            <th>Qty</th>
                            <th>Unit</th>
                            <th>Volume</th>
                            <th>Tonnage (Kg)</th>
                            <th>Initial Status</th>
                            <?php if ($page == 'payment/realization') { ?>
                                <th width="50px">Status</th>
                            <?php } ?>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($bookingGoods as $goods): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $goods['goods_name'] ?></td>
                                <td><?= $goods['quantity'] ?></td>
                                <td><?= $goods['unit'] ?></td>
                                <td><?= numerical($goods['total_volume']) ?></td>
                                <td><?= numerical($goods['total_weight'], 3, true) ?></td>
                                <td><?= $goods['status_danger'] ?></td>
                                <?php if ($page == 'payment/realization') { ?>
                                    <td>
                                        <select class="select2" name="status_goods[<?= $goods['id'] ?>]" id="status_all"
                                                data-placeholder="Status">
                                            <option value=""></option>
                                            <option value="not_danger">Not Danger</option>
                                            <option value="danger1">Danger Type 1</option>
                                            <option value="danger2">Danger Type 2</option>
                                        </select>
                                    </td>
                                <?php } ?>
                            </tr>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php } ?>
    </div>
<?php } ?>
