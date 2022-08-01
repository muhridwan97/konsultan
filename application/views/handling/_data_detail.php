<?php if(!empty($handlingContainers)): ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Containers</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped no-datatable no-wrap responsive">
                    <thead>
                    <tr>
                        <th style="width: 25px">No</th>
                        <th>No Reference</th>
                        <th>No Container</th>
                        <th>Type</th>
                        <th>Size</th>
                        <th>Seal</th>
                        <th>Position</th>
                        <th>Is Empty</th>
                        <th>Is Hold</th>
                        <th>Status</th>
                        <th>Danger</th>
                        <th>Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($handlingContainers as $container): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td title="<?= $container['no_booking_reference'] ?>">
                                <a href="<?= site_url('booking/view/' . if_empty($container['id_booking_reference'], $container['id_booking'])) ?>">
                                    <?= mid_ellipsis(if_empty($container['no_booking_reference'], $container['no_reference'])) ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?= site_url('container/view/' . $container['id_container']) ?>">
                                    <?= $container['no_container'] ?>
                                </a>
                            </td>
                            <td><?= $container['type'] ?></td>
                            <td><?= $container['size'] ?></td>
                            <td><?= if_empty($container['seal'], '-') ?></td>
                            <td><?= if_empty($container['position'], '-') ?></td>
                            <td class="<?= $container['is_empty'] ? 'text-danger' :'' ?>">
                                <?= $container['is_empty'] ? 'Empty' : 'Full' ?>
                            </td>
                            <td class="<?= $container['is_hold'] ? 'text-danger' :'' ?>">
                                <?= $container['is_hold'] ? 'Yes' : 'No' ?>
                            </td>
                            <td><?= if_empty($container['status'], 'No Status') ?></td>
                            <td class="<?= $container['status_danger'] != 'NOT DANGER' ? 'text-danger' :'' ?>">
                                <?= $container['status_danger'] ?>
                            </td>
                            <td><?= if_empty($container['description'], 'No description') ?></td>
                        </tr>
                        <?php if (key_exists('goods', $container) && !empty($container['goods'])): ?>
                            <tr>
                                <td></td>
                                <td colspan="11">
                                    <table class="table table-condensed table-bordered no-datatable responsive">
                                        <thead>
                                        <tr>
                                            <th style="width: 25px">No</th>
                                            <th>Goods</th>
                                            <th>Quantity</th>
                                            <th>Unit</th>
                                            <th>Unit Weight (Kg)</th>
                                            <th>Total Weight (Kg)</th>
                                            <th>Unit Gross (Kg)</th>
                                            <th>Total Gross (Kg)</th>
                                            <th>Unit Length (M)</th>
                                            <th>Unit Width (M)</th>
                                            <th>Unit Height (M)</th>
                                            <th>Unit Volume (M<sup>3</sup>)</th>
                                            <th>Total Volume (M<sup>3</sup>)</th>
                                            <th>No Pallet</th>
                                            <th>Is Hold</th>
                                            <th>Status</th>
                                            <th>Danger</th>
                                            <th>Description</th>
                                        </tr>
                                        </thead>
                                        <tbody>
                                        <?php $innerNo = 1; ?>
                                        <?php foreach ($container['goods'] as $item): ?>
                                            <tr>
                                                <td><?= $innerNo++ ?></td>
                                                <td>
                                                    <a href="<?= site_url('goods/view/' . $item['id_goods']) ?>">
                                                        <?= $item['goods_name'] ?>
                                                    </a><br>
                                                    <small class="text-muted"><?= $item['no_goods'] ?></small>
                                                </td>
                                                <td><?= numerical($item['quantity'], 3, true) ?></td>
                                                <td><?= $item['unit'] ?></td>
                                                <td><?= numerical($item['unit_weight'], 3, true) ?> KG</td>
                                                <td><?= numerical($item['total_weight'], 3, true) ?> KG</td>
                                                <td><?= numerical($item['unit_gross_weight'], 3, true) ?> KG</td>
                                                <td><?= numerical($item['total_gross_weight'], 3, true) ?> KG</td>
                                                <td><?= numerical($item['unit_length'], 3, true) ?> M</td>
                                                <td><?= numerical($item['unit_width'], 3, true) ?> M</td>
                                                <td><?= numerical($item['unit_height'], 3, true) ?> M</td>
                                                <td><?= numerical($item['unit_volume']) ?> M<sup>3</sup></td>
                                                <td><?= numerical($item['total_volume']) ?> M<sup>3</sup></td>
                                                <td><?= if_empty($item['no_pallet'], '-') ?></td>
                                                <td class="<?= $item['is_hold'] ? 'text-danger' :'' ?>"><?= $item['is_hold'] ? 'Yes' : 'No' ?></td>
                                                <td><?= if_empty($item['status'], 'No status') ?></td>
                                                <td class="<?= $item['status_danger'] != 'NOT DANGER' ? 'text-danger' :'' ?>"><?= $item['status_danger'] ?></td>
                                                <td><?= if_empty($item['description'], 'No description') ?></td>
                                            </tr>
                                        <?php endforeach; ?>
                                        </tbody>
                                    </table>
                                </td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>

                    <?php if (empty($handlingContainers)): ?>
                        <tr>
                            <td colspan="12" class="text-center">No data available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if(!empty($handlingGoods)): ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Goods</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped no-datatable no-wrap responsive">
                    <thead>
                    <tr>
                        <th style="width: 25px">No</th>
                        <th>No Reference</th>
                        <th>Goods</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Unit Weight (Kg)</th>
                        <th>Total Weight (Kg)</th>
                        <th>Unit Gross (Kg)</th>
                        <th>Total Gross (Kg)</th>
                        <th>Unit Length (M)</th>
                        <th>Unit Width (M)</th>
                        <th>Unit Height (M)</th>
                        <th>Volume (M<sup>3</sup>)</th>
                        <th>Total Volume (M<sup>3</sup>)</th>
                        <th>Position</th>
                        <th>Is Hold</th>
                        <th>Status</th>
                        <th>Danger</th>
                        <th>Ex Container</th>
                        <th>Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($handlingGoods as $index => $item): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td title="<?= $item['no_booking_reference'] ?>">
                                <a href="<?= site_url('booking/view/' . if_empty($item['id_booking_reference'], $item['id_booking'])) ?>">
                                    <?= mid_ellipsis(if_empty($item['no_booking_reference'], $item['no_reference'])) ?>
                                </a>
                            </td>
                            <td>
                                <a href="<?= site_url('goods/view/' . $item['id_goods']) ?>">
                                    <?= $item['goods_name'] ?>
                                </a><br>
                                <small class="text-muted"><?= $item['no_goods'] ?></small>
                            </td>
                            <td><?= numerical($item['quantity'], 3, true) ?></td>
                            <td><?= $item['unit'] ?></td>
                            <td><?= numerical($item['unit_weight'], 3, true) ?> KG</td>
                            <td><?= numerical($item['total_weight'], 3, true) ?> KG</td>
                            <td><?= numerical($item['unit_gross_weight'], 3, true) ?> KG</td>
                            <td><?= numerical($item['total_gross_weight'], 3, true) ?> KG</td>
                            <td><?= numerical($item['unit_length'], 3, true) ?> M</td>
                            <td><?= numerical($item['unit_width'], 3, true) ?> M</td>
                            <td><?= numerical($item['unit_height'], 3, true) ?> M</td>
                            <td><?= numerical($item['unit_volume']) ?> M<sup>3</sup></td>
                            <td><?= numerical($item['total_volume']) ?> M<sup>3</sup></td>
                            <td><?= if_empty($item['position'], '-') ?></td>
                            <td class="<?= $item['is_hold'] ? 'text-danger' :'' ?>">
                                <?= $item['is_hold'] ? 'Yes' : 'No' ?>
                            </td>
                            <td><?= if_empty($item['status'], 'No status') ?></td>
                            <td class="<?= $item['status_danger'] != 'NOT DANGER' ? 'text-danger' :'' ?>">
                                <?= $item['status_danger'] ?>
                            </td>
                            <td><?= if_empty($item['ex_no_container'], '-') ?></td>
                            <td><?= if_empty($item['description'], 'No description') ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($handlingGoods)): ?>
                        <tr>
                            <td colspan="19" class="text-center">No data available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if(!empty($handlingComponents)): ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Handling Components</h3>
        </div>
        <div class="box-body">
            <table class="table table-bordered table-striped no-datatable">
                <thead>
                <tr>
                    <th style="width: 30px">No</th>
                    <th>Component</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Description</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1; ?>
                <?php foreach ($handlingComponents as $component): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $component['handling_component'] ?></td>
                        <td><?= numerical($component['quantity'], 3, true) ?></td>
                        <td><?= if_empty($component['unit'], '-') ?></td>
                        <td><?= if_empty($component['description'], '-') ?></td>
                    </tr>
                <?php endforeach; ?>
                <?php if (empty($handlingComponents)): ?>
                    <tr>
                        <td colspan="5" class="text-center">No data available</td>
                    </tr>
                <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>
