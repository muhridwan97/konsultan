<div class="form-horizontal form-view">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-sm-3">Customer</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $booking['customer_name'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">No Booking</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $booking['no_booking'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-sm-3">Booking Type</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $booking['booking_type'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Booking Date</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= (new DateTime($booking['booking_date']))->format('d F Y') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if (!empty($stockContainers)): ?>
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Stock Containers</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered no-datatable">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>No Job</th>
                    <th>No Container</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Seal</th>
                    <th>Position</th>
                    <th>Description</th>
                    <th style="width: 80px">Action</th>
                </tr>
                </thead>
                <tbody id="source-container-wrapper">
                <tr id="placeholder" style="display: none">
                    <td colspan="9" class="text-center">
                        All containers are taken
                    </td>
                </tr>
                <?php $no = 1;
                foreach ($stockContainers as $container): ?>
                    <tr data-row="<?= $container['id'] ?>" class="default">
                        <td><?= $no++ ?></td>
                        <td class="row-job">
                            <a href="<?= site_url('work-order/view/' . $container['id_work_order']) ?>" target="_blank">
                                <?= $container['no_work_order'] ?>
                            </a>
                        </td>
                        <td><?= $container['no_container'] ?></td>
                        <td><?= $container['type'] ?></td>
                        <td><?= $container['size'] ?></td>
                        <td><?= if_empty($container['seal'], '-') ?></td>
                        <td><?= if_empty($container['position'], '-') ?></td>
                        <td><?= if_empty($container['description'], 'No description') ?></td>
                        <td>
                            <input type="hidden" name="containers[]" value="<?= $container['id'] ?>" disabled>
                            <button type="button" class="btn btn-primary btn-block btn-take"
                                    data-type="container"
                                    data-id="<?= $container['id'] ?>">
                                Take
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($stockGoods)): ?>
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Stock Goods</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered no-datatable">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>No Job</th>
                    <th>Goods</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Tonnage (Kg)</th>
                    <th>Volume (M<sup>3</sup>)</th>
                    <th>Position</th>
                    <th>No Pallet</th>
                    <th>Description</th>
                    <th style="width: 80px">Action</th>
                </tr>
                </thead>
                <tbody id="source-item-wrapper">
                <tr id="placeholder" style="display: none">
                    <td colspan="11" class="text-center">
                        All items are taken
                    </td>
                </tr>
                <?php $no = 1; ?>
                <?php foreach ($stockGoods as $item): ?>
                    <tr data-row="<?= $item['id'] ?>">
                        <td><?= $no++ ?></td>
                        <td class="row-job">
                            <a href="<?= site_url('work-order/view/' . $item['id_work_order']) ?>" target="_blank">
                                <?= $item['no_work_order'] ?>
                            </a>
                        </td>
                        <td><?= $item['goods_name'] ?></td>
                        <td>
                            <input type="hidden" step="any" min="0" max="<?= $item['quantity'] ?>" class="form-control" id="quantity" name="quantities[]" value="<?= $item['quantity'] ?>" disabled>
                            <span id="quantity-label"><?= numerical($item['quantity']) ?></span>
                        </td>
                        <td><?= $item['unit'] ?></td>
                        <td>
                            <input type="hidden" step="any" min="0" max="<?= $item['tonnage'] ?>" class="form-control" id="tonnage" name="tonnages[]" value="<?= $item['tonnage'] ?>" disabled>
                            <span id="tonnage-label"><?= numerical($item['tonnage']) ?></span>
                        </td>
                        <td>
                            <input type="hidden" step="any" min="0" max="<?= $item['volume'] ?>" class="form-control" id="volume" name="volumes[]" value="<?= $item['volume'] ?>" disabled>
                            <span id="volume-label"><?= numerical($item['volume']) ?></span>
                        </td>
                        <td><?= if_empty($item['position'], 'No Position') ?></td>
                        <td><?= if_empty($item['no_pallet'], 'No Pallet') ?></td>
                        <td><?= if_empty($item['description'], 'No description') ?></td>
                        <td>
                            <input type="hidden" name="goods[]" value="<?= $item['id'] ?>" disabled>
                            <button type="button" class="btn btn-primary btn-block btn-take"
                                    data-type="item"
                                    data-id="<?= $item['id'] ?>">
                                Take
                            </button>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php if (empty($stockContainers) && empty($stockGoods)): ?>
    <p class="text-danger lead">No stock available</p>
<?php endif; ?>
