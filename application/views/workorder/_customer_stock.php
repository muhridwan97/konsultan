<?php if (!empty($stockContainers)): ?>
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Stock Containers</h3>
            <button type="button" class="btn btn-success pull-right btn-take-all" data-type="container">
                Take All
            </button>
        </div>
        <div class="box-body">
            <table class="table table-bordered no-datatable">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>No Container</th>
                    <th>Type</th>
                    <th>Size</th>
                    <th>Seal</th>
                    <th>Position</th>
                    <th>Is Empty</th>
                    <th>Is Hold</th>
                    <th>Status</th>
                    <th>Danger</th>
                    <th style="width: 80px">Action</th>
                </tr>
                </thead>
                <tbody id="source-container-wrapper">
                <tr id="placeholder" style="display: none">
                    <td colspan="11" class="text-center">
                        All containers are taken
                    </td>
                </tr>
                <?php $no = 1; ?>
                <?php foreach ($stockContainers as $container): ?>
                    <tr data-row="<?= $no ?>" data-takeable="<?= (!$container['is_hold'] || !$holdByStatus) ? 1 : 0 ?>">
                        <td><?= $no ?></td>
                        <td><?= $container['no_container'] ?></td>
                        <td><?= $container['container_type'] ?></td>
                        <td><?= $container['container_size'] ?></td>
                        <td><?= if_empty($container['seal'], '-') ?></td>
                        <td><?= if_empty($container['position'], '-') ?></td>
                        <td class="<?= $container['is_empty'] ? 'bg-red' :'' ?>">
                            <?= $container['is_empty'] ? 'Empty' : 'Full' ?>
                        </td>
                        <td class="<?= $container['is_hold'] ? 'bg-red' :'' ?>">
                            <?= $container['is_hold'] ? 'Yes' : 'No' ?>
                        </td>
                        <td><?= if_empty($container['status'], 'No Status') ?></td>
                        <td class="<?= $container['status_danger'] != 'NOT DANGER' ? 'bg-red' :'' ?>">
                            <?= $container['status_danger'] ?>
                        </td>
                        <td>
                            <input type="hidden" name="containers[]" value="<?= $container['id_container'] ?>" disabled>
                            <input type="hidden" name="container_seals[]" value="<?= $container['seal'] ?>" disabled>
                            <input type="hidden" name="container_positions[]" value="<?= $container['id_position'] ?>" disabled>
                            <input type="hidden" name="container_is_empty[]" value="<?= $container['is_empty'] ?>" disabled>
                            <input type="hidden" name="container_is_hold[]" value="<?= $container['is_hold'] ?>" disabled>
                            <input type="hidden" name="container_statuses[]" value="<?= $container['status'] ?>" disabled>
                            <input type="hidden" name="container_status_dangers[]" value="<?= $container['status_danger'] ?>" disabled>
                            <input type="hidden" name="container_warehouses[]" value="<?= $container['id_warehouse'] ?>" disabled>
                            <input type="hidden" name="container_descriptions[]" value="<?= $container['description'] ?>" disabled>
                            <?php if(!$container['is_hold'] || !$holdByStatus): ?>
                                <button type="button" class="btn btn-primary btn-block btn-take" data-type="container" data-id="<?= $no ?>">
                                    Take
                                </button>
                            <?php endif ?>
                        </td>
                    </tr>
                    <?php $no++ ?>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif ?>

<?php if (!empty($stockGoods)): ?>
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Stock Goods</h3>
            <button type="button" class="btn btn-success pull-right btn-take-all" data-type="item">
                Take All
            </button>
        </div>
        <div class="box-body">
            <table class="table table-bordered no-datatable">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>Goods</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Tonnage (Kg)</th>
                    <th>Tonnage Gross (Kg)</th>
                    <th>Volume (M<sup>3</sup>)</th>
                    <th>Position</th>
                    <th>No Pallet</th>
                    <th>Is Hold</th>
                    <th>Status</th>
                    <th>Danger</th>
                    <th style="width: 80px">Action</th>
                </tr>
                </thead>
                <tbody id="source-item-wrapper">
                <tr id="placeholder" style="display: none">
                    <td colspan="13" class="text-center">
                        All items are taken
                    </td>
                </tr>
                <?php $no = 1; ?>
                <?php foreach ($stockGoods as $item): ?>
                    <tr data-row="<?= $no ?>" data-takeable="<?= (!$item['is_hold'] || !$holdByStatus) ? 1 : 0 ?>">
                        <td><?= $no ?></td>
                        <td><?= $item['goods_name'] ?></td>
                        <td>
                            <input type="hidden" step="any" min="0" max="<?= $item['stock_quantity'] ?>" class="form-control" id="quantity" name="quantities[]" value="<?= $item['stock_quantity'] ?>" disabled>
                            <span id="quantity-label"><?= numerical($item['stock_quantity']) ?></span>
                        </td>
                        <td><?= $item['unit'] ?></td>
                        <td>
                            <input type="hidden" step="any" min="0" max="<?= $item['stock_tonnage'] ?>" class="form-control" id="tonnage" name="tonnages[]" value="<?= $item['stock_tonnage'] ?>" disabled>
                            <span id="tonnage-label"><?= numerical($item['stock_tonnage']) ?></span>
                        </td>
                        <td>
                            <input type="hidden" step="any" min="0" max="<?= $item['stock_tonnage_gross'] ?>" class="form-control" id="tonnage-gross" name="tonnages_gross[]" value="<?= $item['stock_tonnage_gross'] ?>" disabled>
                            <span id="tonnage-gross-label"><?= numerical($item['stock_tonnage_gross']) ?></span>
                        </td>
                        <td>
                            <input type="hidden" step="any" min="0" max="<?= $item['stock_volume'] ?>" class="form-control" id="volume" name="volumes[]" value="<?= $item['stock_volume'] ?>" disabled>
                            <span id="volume-label"><?= numerical($item['stock_volume']) ?></span>
                        </td>
                        <td><?= if_empty($item['position'], 'No Position') ?></td>
                        <td><?= if_empty($item['no_pallet'], 'No Pallet') ?></td>
                        <td class="<?= $item['is_hold'] ? 'bg-red' :'' ?>">
                            <?= $item['is_hold'] ? 'Yes' : 'No' ?>
                        </td>
                        <td><?= if_empty($item['status'], 'No Status') ?></td>
                        <td class="<?= $item['status_danger'] != 'NOT DANGER' ? 'bg-red' :'' ?>">
                            <?= $item['status_danger'] ?>
                        </td>
                        <td>
                            <input type="hidden" name="goods[]" value="<?= $item['id_goods'] ?>" disabled>
                            <input type="hidden" name="units[]" value="<?= $item['id_unit'] ?>" disabled>
                            <input type="hidden" name="positions[]" value="<?= $item['id_position'] ?>" disabled>
                            <input type="hidden" name="no_pallets[]" value="<?= $item['no_pallet'] ?>" disabled>
                            <input type="hidden" name="no_delivery_orders[]" value="<?= $item['no_delivery_order'] ?>" disabled>
                            <input type="hidden" name="is_hold[]" value="<?= $item['is_hold'] ?>" disabled>
                            <input type="hidden" name="statuses[]" value="<?= $item['status'] ?>" disabled>
                            <input type="hidden" name="status_dangers[]" value="<?= $item['status_danger'] ?>" disabled>
                            <input type="hidden" name="warehouses[]" value="<?= $item['id_warehouse'] ?>" disabled>
                            <input type="hidden" name="descriptions[]" value="<?= $item['description'] ?>" disabled>
                            <input type="hidden" name="inbound_dates[]" value="<?= $item['inbound_date'] ?>" disabled>
                            <?php if(!$item['is_hold'] || !$holdByStatus): ?>
                                <button type="button" class="btn btn-primary btn-block btn-take" data-type="item" data-id="<?= $no ?>">
                                    Take
                                </button>
                            <?php endif ?>
                        </td>
                    </tr>
                    <?php $no++ ?>
                <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif ?>

<?php if (empty($stockContainers) && empty($stockGoods)): ?>
    <p class="text-danger lead">No stock available</p>
<?php endif ?>
