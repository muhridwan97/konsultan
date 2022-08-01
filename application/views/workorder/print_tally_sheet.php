<div class="row">
    <div class="col-sm-7 col-xs-6">
        <p class="lead" style="margin-bottom: 10px;">
            <strong>TALLY SHEET DOCUMENT</strong>
        </p>
        <p class="lead" style="margin-bottom: 0">
            No Job: <?= $workOrder['no_work_order'] ?>
        </p>
        <p class="mb0">No Handling: <?= $workOrder['no_handling'] ?></p>
        <p>No Booking: <?= if_empty($workOrder['no_booking'], '-') ?> (<?= $workOrder['no_reference'] ?>)</p>
        <p class="mb0"><strong>BRANCH <?= $workOrder['branch'] ?></strong></p>
    </div>
    <div class="col-sm-5 col-xs-6">
        <div class="pull-right">
            <div class="text-center" style="display: inline-block; margin-right: 20px">
                <img src="data:image/png;base64,<?= $barcodeWorkOrder ?>" alt="<?= $workOrder['no_work_order'] ?>">
                <p>NO JOB SHEET</p>
            </div>
            <div class="text-center" style="display: inline-block">
                <img src="data:image/png;base64,<?= $barcodeHandling ?>" alt="<?= $workOrder['no_handling'] ?>">
                <p>NO HANDLING</p>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="row" style="margin-top: 20px">
    <div class="col-xs-4">
        Job Taken :
        <p><strong><?= format_date($workOrder['taken_at'], 'd F Y H:i') ?></strong></p>
    </div>
    <div class="col-xs-4">
        Job Completed :
        <p><strong><?= format_date($workOrder['completed_at'], 'd F Y H:i') ?></strong></p>
    </div>
    <div class="col-xs-4">
        Customer :
        <p><strong><?= $workOrder['customer_name'] ?></strong></p>
    </div>
</div>

<hr>

<?php if (!empty($containers)): ?>
    <p class="lead mt20 mb10">Containers</p>
    <table class="table table-bordered table-condensed table-striped no-datatable">
        <thead>
        <tr>
            <th style="width: 25px">No</th>
            <th>No Container</th>
            <th>Type</th>
            <th>Size</th>
            <th>Seal</th>
            <th>Position</th>
            <th>Is Empty</th>
            <th>Danger</th>
            <th>Description</th>
        </tr>
        </thead>
        <tbody>
        <?php $no = 1; ?>
        <?php foreach ($containers as $container): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $container['no_container'] ?></td>
                <td><?= $container['type'] ?></td>
                <td><?= $container['size'] ?></td>
                <td><?= if_empty($container['seal'], '-') ?></td>
                <td><?= if_empty($container['position'], '-') ?> | <?= if_empty($container['type_warehouse'], '-')?></td>
                <td class="<?= $container['is_empty'] ? 'text-danger' :'' ?>">
                    <?= $container['is_empty'] ? 'Empty' : 'Full' ?>
                </td>
                <td class="<?= $container['status_danger'] != 'NOT DANGER' ? 'text-danger' :'' ?>">
                    <?= $container['status_danger'] ?>
                </td>
                <td><?= if_empty($container['description'], '-') ?></td>
            </tr>
            <?php
            $containerGoodsExist = key_exists('goods', $container) && !empty($container['goods']);
            $containerContainersExist = key_exists('containers', $container) && !empty($container['containers']);
            ?>
            <?php if ($containerGoodsExist || $containerContainersExist): ?>
                <tr>
                    <td></td>
                    <td colspan="8">
                        <?php if ($containerContainersExist): ?>
                            <table class="table table-condensed no-datatable">
                                <thead>
                                <tr>
                                    <th style="width: 25px">No</th>
                                    <th>No Container</th>
                                    <th>Type</th>
                                    <th>Size</th>
                                    <th>Seal</th>
                                    <th>Position</th>
                                    <th>Is Empty</th>
                                    <th>Danger</th>
                                    <th>Description</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $innerNo = 1; ?>
                                <?php foreach ($container['containers'] as $containerItem): ?>
                                    <tr>
                                        <td><?= $innerNo++ ?></td>
                                        <td><?= $containerItem['no_container'] ?></td>
                                        <td><?= $containerItem['type'] ?></td>
                                        <td><?= $containerItem['size'] ?></td>
                                        <td><?= if_empty($containerItem['seal'], '-') ?></td>
                                        <td><?= if_empty($containerItem['position'], '-') ?> | <?= if_empty($containerItem['type_warehouse'], '-')?></td>
                                        <td class="<?= $containerItem['is_empty'] ? 'text-danger' :'' ?>">
                                            <?= $containerItem['is_empty'] ? 'Empty' : 'Full' ?>
                                        </td>
                                        <td class="<?= $containerItem['status_danger'] != 'NOT DANGER' ? 'text-danger' :'' ?>">
                                            <?= $containerItem['status_danger'] ?>
                                        </td>
                                        <td><?= if_empty($containerItem['description'], '-') ?></td>
                                    </tr>
                                <?php endforeach ?>
                                </tbody>
                            </table>
                        <?php endif ?>

                        <?php if ($containerGoodsExist): ?>
                            <table class="table table-condensed no-datatable">
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
                                    <th>Unit Volume (M<sup>3</sup>)</th>
                                    <th>Total Volume (M<sup>3</sup>)</th>
                                    <th>Position</th>
                                    <th>No Pallet</th>
                                    <th>Danger</th>
                                    <th>Ex Container</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $innerNo = 1; ?>
                                <?php foreach ($container['goods'] as $item): ?>
                                    <tr>
                                        <td><?= $innerNo++ ?></td>
                                        <td>
                                            <?= $item['goods_name'] ?><br>
                                            <small class="text-muted"><?= if_empty($item['whey_number'], if_empty($item['no_goods'], '-'), '', '', true) ?></small>
                                        </td>
                                        <td><?= numerical($item['quantity'], 3, true) ?></td>
                                        <td><?= $item['unit'] ?></td>
                                        <td><?= numerical($item['unit_weight'], 3, true) ?></td>
                                        <td><?= numerical($item['total_weight'], 3, true) ?></td>
                                        <td><?= numerical($item['unit_gross_weight'], 3, true) ?></td>
                                        <td><?= numerical($item['total_gross_weight'], 3, true) ?></td>
                                        <td>
                                            <?= numerical($item['unit_volume']) ?><br>
                                            <small class="text-muted"><?= numerical($item['unit_length'], 3, true) ?>l x
                                                <?= numerical($item['unit_width'], 3, true) ?>w x
                                                <?= numerical($item['unit_height'], 3, true) ?>h
                                            </small>
                                        </td>
                                        <td><?= numerical($item['total_volume']) ?> M<sup>3</sup></td>
                                        <td>
                                            <?= if_empty($item['position'], '-') ?> | <?= if_empty($item['type_warehouse'], '-')?>
                                            <small class="text-muted" style="display: block">
                                                <?= str_replace(',', ', ', $item['position_blocks']) ?>
                                            </small>
                                        </td>
                                        <td><?= if_empty($item['no_pallet'], '-') ?></td>
                                        <td><?= $item['status_danger'] ?></td>
                                        <td><?= if_empty($item['ex_no_container'], '-') ?></td>
                                    </tr>
                                <?php endforeach ?>
                                </tbody>
                            </table>
                        <?php endif ?>
                    </td>
                </tr>
            <?php endif ?>
        <?php endforeach ?>

        <?php if (empty($containers)): ?>
            <tr>
                <td colspan="9" class="text-center">No data available</td>
            </tr>
        <?php endif ?>
        </tbody>
    </table>
<?php endif ?>


<?php if(!empty($goods)): ?>
    <p class="lead mt20 mb10">Goods</p>
    <table class="table table-bordered table-condensed table-striped no-datatable">
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
            <th>Unit Volume (M<sup>3</sup>)</th>
            <th>Total Volume (M<sup>3</sup>)</th>
            <th>Position</th>
            <th>No Pallet</th>
            <th>Danger</th>
            <th>Ex Container</th>
        </tr>
        </thead>
        <tbody>
        <?php $no = 1; ?>
        <?php foreach ($goods as $item): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td>
                    <?= $item['goods_name'] ?><br>
                    <small class="text-muted"><?= if_empty($item['whey_number'], if_empty($item['no_goods'], '-')) ?></small>
                </td>
                <td><?= numerical($item['quantity'], 3, true) ?></td>
                <td><?= $item['unit'] ?></td>
                <td><?= numerical($item['unit_weight'], 3, true) ?></td>
                <td><?= numerical($item['total_weight'], 3, true) ?></td>
                <td><?= numerical($item['unit_gross_weight'], 3, true) ?></td>
                <td><?= numerical($item['total_gross_weight'], 3, true) ?></td>
                <td>
                    <?= numerical($item['unit_volume']) ?><br>
                    <small class="text-muted"><?= numerical($item['unit_length'], 3, true) ?>l x
                    <?= numerical($item['unit_width'], 3, true) ?>w x
                    <?= numerical($item['unit_height'], 3, true) ?>h
                    </small>
                </td>
                <td><?= numerical($item['total_volume']) ?> M<sup>3</sup></td>
                <td>
                    <?= if_empty($item['position'], '-') ?> | <?= if_empty($item['type_warehouse'], '-')?>
                    <small class="text-muted" style="display: block">
                        <?= str_replace(',', ', ', $item['position_blocks']) ?>
                    </small>
                </td>
                <td><?= if_empty($item['no_pallet'], '-') ?></td>
                <td><?= $item['status_danger'] ?></td>
                <td><?= if_empty($item['ex_no_container'], '-') ?></td>
            </tr>
            <?php if (key_exists('goods', $item) && !empty($item['goods'])): ?>
                <tr>
                    <td></td>
                    <td colspan="13">
                        <table class="table table-condensed no-datatable">
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
                                <th>Unit Volume (M<sup>3</sup>)</th>
                                <th>Total Volume (M<sup>3</sup>)</th>
                                <th>Position</th>
                                <th>No Pallet</th>
                                <th>Danger</th>
                                <th>Ex Container</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $innerNo = 1; ?>
                            <?php foreach ($item['goods'] as $itemGoods): ?>
                                <tr>
                                    <td><?= $innerNo++ ?></td>
                                    <td><?= $itemGoods['goods_name'] ?></td>
                                    <td><?= numerical($itemGoods['quantity'], 3, true) ?></td>
                                    <td><?= $itemGoods['unit'] ?></td>
                                    <td><?= numerical($itemGoods['unit_weight'], 3, true) ?></td>
                                    <td><?= numerical($itemGoods['total_weight'], 3, true) ?></td>
                                    <td><?= numerical($itemGoods['unit_gross_weight'], 3, true) ?></td>
                                    <td><?= numerical($itemGoods['total_gross_weight'], 3, true) ?></td>
                                    <td>
                                        <?= numerical($itemGoods['unit_volume']) ?><br>
                                        <small class="text-muted"><?= numerical($itemGoods['unit_length'], 3, true) ?>l x
                                            <?= numerical($itemGoods['unit_width'], 3, true) ?>w x
                                            <?= numerical($itemGoods['unit_height'], 3, true) ?>h
                                        </small>
                                    </td>
                                    <td><?= numerical($itemGoods['total_volume']) ?> M<sup>3</sup></td>
                                    <td>
                                        <?= if_empty($itemGoods['position'], '-') ?> | <?= if_empty($itemGoods['type_warehouse'], '-')?>
                                        <small class="text-muted" style="display: block">
                                            <?= str_replace(',', ', ', $itemGoods['position_blocks']) ?>
                                        </small>
                                    </td>
                                    <td><?= if_empty($itemGoods['no_pallet'], '-') ?></td>
                                    <td><?= $itemGoods['status_danger'] ?></td>
                                    <td><?= if_empty($itemGoods['ex_no_container'], '-') ?></td>
                                </tr>
                            <?php endforeach ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            <?php endif ?>
        <?php endforeach ?>

        <?php if (empty($goods)): ?>
            <tr>
                <td colspan="14" class="text-center">No data available</td>
            </tr>
        <?php endif ?>
        </tbody>
    </table>
<?php endif ?>

<div class="row" style="margin-top: 20px">
    <hr>
    <div class="col-xs-4">
        Gate Check In :
        <p>
            <strong>
                <?php
                if (empty($workOrder['gate_in_date'])) {
                    echo '-';
                } else {
                    echo format_date($workOrder['gate_in_date'], 'd F Y H:i');
                }
                ?>
            </strong>
        </p>
    </div>
    <div class="col-xs-4">
        Gate Check Out :
        <p>
            <strong>
                <?php
                if (empty($workOrder['gate_out_date'])) {
                    echo '-';
                } else {
                    echo format_date($workOrder['gate_out_date'], 'd F Y H:i');
                }
                ?>
            </strong>
        </p>
    </div>
    <div class="col-xs-4">
        Job Description :
        <p><strong><?= if_empty($workOrder['description'], '-') ?></strong></p>
    </div>
</div>

<div class="row" style="margin-bottom: 20px">
    <hr>
    <div class="col-xs-3">
        <p><strong>Tally 1</strong></p>
        <p><?= $workOrder['tally_name'] ?></p>
    </div>
    <div class="col-xs-3">
        <p><strong>Tally 2</strong></p>
        <p><?= $tally2['creator_name'] ?? '-' ?></p>
    </div>
    <div class="col-xs-3">
        <p><strong>Coordinator</strong></p>
        <p><?= $coordinator['creator_name'] ?? '-' ?></p>
    </div>
</div>