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
                        <?= readable_date($booking['booking_date'], false) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if ($source == 'DO'): ?>
    <div class="alert alert-success" role="alert">
        <button type="button" class="close" data-dismiss="alert" aria-label="Close">
            <span aria-hidden="true">&times;</span>
        </button>
        <p>This booking has <strong>delivery order (DO)</strong> system!</p>
    </div>
<?php endif; ?>

<input type="hidden" name="source" value="<?= $source ?>">

<?php if (!empty($containers)): ?>
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Containers</h3>
        </div>
        <div class="box-body">
            <div class="form-group">
                <input type="text" id="search-data" onkeyup="searchTable(this, 'table-container', 1)"
                       class="form-control" placeholder="Search for container number...">
            </div>
            <table class="table no-datatable responsive" id="table-container">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>No Container</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Seal</th>
                    <th>Is Empty</th>
                    <th>Is Hold</th>
                    <th>Status</th>
                    <th>Danger</th>
                    <th style="width: 80px">Action</th>
                </tr>
                </thead>
                <tbody id="source-container-wrapper">
                <tr id="placeholder" style="display: none">
                    <td colspan="10" class="text-center">All containers are loaded</td>
                </tr>
                <?php $no = 1;
                foreach ($containers as $container): ?>
                    <tr data-row="<?= $container['id'] ?>" class="default">
                        <td><?= $no++ ?></td>
                        <td><?= $container['no_container'] ?></td>
                        <td><?= $container['type'] ?></td>
                        <td><?= $container['size'] ?></td>
                        <td><?= if_empty($container['seal'], '-') ?></td>
                        <td><?= $container['is_empty'] ? 'Empty' : 'Full' ?></td>
                        <td><?= $container['is_hold'] ? 'Yes' : 'No' ?></td>
                        <td><?= if_empty($container['status'], 'No Status') ?></td>
                        <td><?= $container['status_danger'] ?></td>
                        <td>
                            <input type="hidden" name="containers[]" value="<?= $container['id'] ?>" disabled>
                            <button type="button" class="btn btn-primary btn-block btn-take"
                                    data-type="container"
                                    data-id="<?= $container['id'] ?>">
                                Take
                            </button>
                        </td>
                    </tr>
                    <?php if (key_exists('goods', $container) && !empty($container['goods'])): ?>
                        <tr data-row="<?= $container['id'] ?>" class="skip-ordering">
                            <td></td>
                            <td colspan="9">
                                <div class="table-editor-wrapper">
                                    <div class="table-editor-scroller">
                                        <table class="table no-datatable responsive no-wrap">
                                            <thead>
                                            <tr>
                                                <th style="width: 25px">No</th>
                                                <th>Goods</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                                <th>Weight (Kg)</th>
                                                <th>Gross (Kg)</th>
                                                <th>Length (M)</th>
                                                <th>Width (M)</th>
                                                <th>Height (M)</th>
                                                <th>Volume (M<sup>3</sup>)</th>
                                                <th>Is Hold</th>
                                                <th>Status</th>
                                                <th>Danger</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $innerNo = 1; ?>
                                            <?php foreach ($container['goods'] as $item): ?>
                                                <tr>
                                                    <td><?= $innerNo++ ?></td>
                                                    <td><?= $item['goods_name'] ?></td>
                                                    <td><?= numerical($item['quantity']) ?></td>
                                                    <td><?= if_empty($item['unit'], '-') ?></td>
                                                    <td><?= numerical($item['tonnage']) ?></td>
                                                    <td><?= numerical($item['tonnage_gross']) ?></td>
                                                    <td><?= numerical($item['length']) ?></td>
                                                    <td><?= numerical($item['width']) ?></td>
                                                    <td><?= numerical($item['height']) ?></td>
                                                    <td><?= numerical($item['volume']) ?></td>
                                                    <td><?= $item['is_hold'] ? 'Yes' : 'No' ?></td>
                                                    <td><?= if_empty($item['status'], 'No status') ?></td>
                                                    <td><?= if_empty($item['status_danger'], 'No status') ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </td>
                        </tr>
                    <?php endif; ?>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($goods)): ?>
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">
                <?php if ($source == 'DO'): ?>
                    <th>Delivery Order (DO) Goods</th>
                <?php else: ?>
                    <th>Goods</th>
                <?php endif; ?>
            </h3>
        </div>
        <div class="box-body">
            <div class="form-group">
                <input type="text" id="search-data" onkeyup="searchTable(this, 'table-goods', 1)"
                       class="form-control" placeholder="Search for goods name...">
            </div>

            <div class="table-editor-wrapper">
                <div class="table-editor-scroller">
                    <table class="table no-datatable responsive no-wrap" id="table-goods">
                        <thead>
                        <tr>
                            <th style="width: 25px">No</th>
                            <?php if ($source == 'DO'): ?>
                                <th>No Delivery Order</th>
                            <?php endif; ?>
                            <th>Goods</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Weight (Kg)</th>
                            <th>Gross (Kg)</th>
                            <th>Length (M)</th>
                            <th>Width (M)</th>
                            <th>Height (M)</th>
                            <th>Volume (M<sup>3</sup>)</th>
                            <th>Is Hold</th>
                            <th>Status</th>
                            <th>Danger</th>
                            <th>Ex Container</th>
                            <th class="text-right sticky-col-right">
                                Action
                            </th>
                        </tr>
                        </thead>
                        <tbody id="source-item-wrapper">
                        <tr id="placeholder" style="display: none">
                            <td colspan="16" class="text-center">
                                All items are loaded
                            </td>
                        </tr>
                        <?php $no = 1; ?>
                        <?php foreach ($goods as $item): ?>
                            <tr data-row="<?= $item['id'] ?>">
                                <td><?= $no++ ?></td>
                                <?php if ($source == 'DO'): ?>
                                    <td><?= $item['no_delivery_order'] ?></td>
                                <?php endif; ?>
                                <td><?= $item['goods_name'] ?></td>
                                <td>
                                    <input type="hidden" step="any" min="0" max="<?= $item['left_quantity'] ?>"
                                           class="form-control" id="quantity" name="quantities[]"
                                           value="<?= $item['left_quantity'] ?>" style="width: 100px" disabled>
                                    <span id="quantity-label"><?= numerical($item['left_quantity']) ?></span>
                                </td>
                                <td><?= $item['unit'] ?></td>
                                <td>
                                    <input type="hidden" step="any" min="0" max="<?= $item['left_tonnage'] ?>"
                                           class="form-control" id="tonnage" name="tonnages[]"
                                           value="<?= $item['left_tonnage'] ?>" style="width: 100px" disabled>
                                    <span id="tonnage-label"><?= numerical($item['left_tonnage']) ?></span>
                                </td>
                                <td>
                                    <input type="hidden" step="any" min="0" max="<?= $item['left_tonnage_gross'] ?>"
                                           class="form-control" id="tonnage-gross" name="tonnage_gross[]"
                                           value="<?= $item['left_tonnage_gross'] ?>" style="width: 100px" disabled>
                                    <span id="tonnage-gross-label"><?= numerical($item['left_tonnage_gross']) ?></span>
                                </td>
                                <td>
                                    <input type="hidden" step="any" min="0" max="<?= $item['left_length'] ?>"
                                           class="form-control" id="length" name="lengths[]"
                                           value="<?= $item['left_length'] ?>" style="width: 100px" disabled>
                                    <span id="length-label"><?= numerical($item['left_length']) ?></span>
                                </td>
                                <td>
                                    <input type="hidden" step="any" min="0" max="<?= $item['left_width'] ?>"
                                           class="form-control" id="width" name="widths[]"
                                           value="<?= $item['left_width'] ?>" style="width: 100px" disabled>
                                    <span id="width-label"><?= numerical($item['left_width']) ?></span>
                                </td>
                                <td>
                                    <input type="hidden" step="any" min="0" max="<?= $item['left_height'] ?>"
                                           class="form-control" id="height" name="heights[]"
                                           value="<?= $item['left_height'] ?>" style="width: 100px" disabled>
                                    <span id="height-label"><?= numerical($item['left_height']) ?></span>
                                </td>
                                <td>
                                    <input type="hidden" step="any" min="0" max="<?= $item['left_volume'] ?>"
                                           class="form-control" id="volume" name="volumes[]"
                                           value="<?= $item['left_volume'] ?>" style="width: 100px" disabled>
                                    <span id="volume-label"><?= numerical($item['left_volume']) ?></span>
                                </td>
                                <td>
                                    <?= $item['is_hold'] ? 'Yes' : 'No' ?>
                                </td>
                                <td><?= if_empty($item['status'], 'No status') ?></td>
                                <td><?= if_empty($item['status_danger'], 'No status') ?></td>
                                <td><?= if_empty($item['ex_no_container'], '-') ?></td>
                                <td class="sticky-col-right">
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

        </div>
    </div>
<?php endif; ?>