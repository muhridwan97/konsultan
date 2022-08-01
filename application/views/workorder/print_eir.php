<div>
    <p class="lead mb0">
        <strong>EQUIPMENT INTERCHANGE RECEIPT</strong>
    </p>
    <p class="lead mb0">
        No Job: <?= $workOrder['no_work_order'] ?>
    </p>
    <p>No Booking: <?= if_empty($workOrder['no_booking'], '-') ?> (<?= $workOrder['no_reference'] ?>)</p>
    <p class="mb0"><strong>BRANCH <?= $workOrder['branch'] ?></strong></p>
</div>

<hr>

<div class="row" style="margin-top: 20px">
    <div class="col-xs-3">
        Transaction Date :
        <p><strong><?= readable_date($workOrder['completed_at']) ?></strong></p>
    </div>
    <div class="col-xs-2">
        Driver :
        <p><strong><?= if_empty($workOrder['driver'], '-') ?></strong></p>
    </div>
    <div class="col-xs-2">
        No Police :
        <p><strong><?= if_empty($workOrder['no_police'], '-') ?></strong></p>
    </div>
    <div class="col-xs-3">
        Vessel :
        <p><strong><?= if_empty($workOrder['vessel'], '-') ?></strong></p>
    </div>
    <div class="col-xs-2">
        Voyage :
        <p><strong><?= if_empty($workOrder['voyage'], '-') ?></strong></p>
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
                <td><?= if_empty($container['position'], '-') ?></td>
                <td class="<?= $container['is_empty'] ? 'bg-red' :'' ?>">
                    <?= $container['is_empty'] ? 'Empty' : 'Full' ?>
                </td>
                <td class="<?= $container['status_danger'] != 'NOT DANGER' ? 'bg-red' :'' ?>">
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
                                        <td><?= if_empty($containerItem['position'], '-') ?></td>
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
                                    <th>Total Weight (Kg)</th>
                                    <th>Total Volume (M<sup>3</sup>)</th>
                                    <th>Unit Length (M)</th>
                                    <th>Unit Width (M)</th>
                                    <th>Unit Height (M)</th>
                                    <th>Position</th>
                                    <th>No Pallet</th>
                                    <th>Description</th>
                                </tr>
                                </thead>
                                <tbody>
                                <?php $innerNo = 1; ?>
                                <?php foreach ($container['goods'] as $item): ?>
                                    <tr>
                                        <td><?= $innerNo++ ?></td>
                                        <td><?= $item['goods_name'] ?></td>
                                        <td><?= numerical($item['quantity'], 3, true) ?></td>
                                        <td><?= $item['unit'] ?></td>
                                        <td><?= numerical($item['total_weight'], 3, true) ?></td>
                                        <td><?= numerical($item['total_volume']) ?></td>
                                        <td><?= numerical($item['unit_length'], 3, true) ?></td>
                                        <td><?= numerical($item['unit_width'], 3, true) ?></td>
                                        <td><?= numerical($item['unit_height'], 3, true) ?></td>
                                        <td><?= if_empty($item['position'], '-') ?></td>
                                        <td><?= if_empty($item['no_pallet'], '-') ?></td>
                                        <td><?= if_empty($item['description'], 'No description') ?></td>
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
            <th>Total Weight (Kg)</th>
            <th>Total Volume (M<sup>3</sup>)</th>
            <th>Unit Length (M)</th>
            <th>Unit Width (M)</th>
            <th>Unit Height (M)</th>
            <th>Position</th>
            <th>No Pallet</th>
            <th>Ex Container</th>
            <th>Description</th>
        </tr>
        </thead>
        <tbody>
        <?php $no = 1; ?>
        <?php foreach ($goods as $item): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $item['goods_name'] ?></td>
                <td><?= numerical($item['quantity'], 3, true) ?></td>
                <td><?= $item['unit'] ?></td>
                <td><?= numerical($item['total_weight'], 3, true) ?></td>
                <td><?= numerical($item['total_volume']) ?></td>
                <td><?= numerical($item['unit_length'], 3, true) ?></td>
                <td><?= numerical($item['unit_width'], 3, true) ?></td>
                <td><?= numerical($item['unit_height'], 3, true) ?></td>
                <td><?= if_empty($item['position'], '-') ?></td>
                <td><?= if_empty($item['no_pallet'], '-') ?></td>
                <td><?= if_empty($item['ex_no_container'], '-') ?></td>
                <td><?= if_empty($item['description'], '-') ?></td>
            </tr>
            <?php if (key_exists('goods', $item) && !empty($item['goods'])): ?>
                <tr>
                    <td></td>
                    <td colspan="10">
                        <table class="table table-condensed no-datatable">
                            <thead>
                            <tr>
                                <th style="width: 25px">No</th>
                                <th>Goods</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Total Weight (Kg)</th>
                                <th>Total Volume (M<sup>3</sup>)</th>
                                <th>Unit Length (M)</th>
                                <th>Unit Width (M)</th>
                                <th>Unit Height (M)</th>
                                <th>Position</th>
                                <th>No Pallet</th>
                                <th>Ex Container</th>
                                <th>Description</th>
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
                                    <td><?= numerical($itemGoods['total_weight'], 3, true) ?></td>
                                    <td><?= numerical($itemGoods['total_volume']) ?></td>
                                    <td><?= numerical($itemGoods['unit_length'], 3, true) ?></td>
                                    <td><?= numerical($itemGoods['unit_width'], 3, true) ?></td>
                                    <td><?= numerical($itemGoods['unit_height'], 3, true) ?></td>
                                    <td><?= if_empty($itemGoods['position'], '-') ?></td>
                                    <td><?= if_empty($itemGoods['no_pallet'], '-') ?></td>
                                    <td><?= if_empty($itemGoods['ex_no_container'], '-') ?></td>
                                    <td><?= if_empty($itemGoods['description'], '-') ?></td>
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
                <td colspan="13" class="text-center">No data available</td>
            </tr>
        <?php endif ?>
        </tbody>
    </table>
<?php endif ?>

<div class="row">
    <hr>
    <div class="col-xs-4 text-center"></div>
    <div class="col-xs-4 text-center"></div>
    <div class="col-xs-4 text-center">
        <p><strong>Admin Gate</strong></p>
        <br><br>
        ( . . . . . . . . . . . . . . . . . . . )
    </div>
</div>