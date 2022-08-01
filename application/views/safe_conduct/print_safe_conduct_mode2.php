<div class="row">
    <div class="col-sm-7 col-xs-8">
        <p class="lead mb0">
            <strong>SURAT JALAN (<?= $safeConduct['type'] ?>)</strong>
        </p>
        <p class="lead mb0">
            No Safe Conduct: <?= $safeConduct['no_safe_conduct'] ?>
        </p>
        <p>
            No Booking: <?= if_empty($safeConduct['no_booking'], '-') ?> (<?= $safeConduct['no_reference'] ?>) <br>
            No Invoice In: <?= if_empty($safeConduct['no_invoice'], '-') ?> <br>
            Customer: <?= $safeConduct['customer_name'] ?>
        </p>
        <p class="mb0"><strong>BRANCH <?= $safeConduct['branch'] ?></strong></p>
    </div>
    <div class="col-sm-5 col-xs-4">
        <div class="pull-right">
            <div class="text-center" style="display: inline-block; margin-right: 20px">
                <img src="data:image/png;base64,<?= $barcodeSafeConduct ?>"
                     alt="<?= $safeConduct['no_safe_conduct'] ?>" width="110px">
                <p class="mb0">NO SAFE CONDUCT</p>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="row">
    <div class="col-xs-6 col-sm-3">
        No Police :
        <p class="mb0"><strong><?= $safeConduct['no_police'] ?></strong></p>
    </div>
    <div class="col-xs-6 col-sm-3">
        Driver :
        <p class="mb0"><strong><?= $safeConduct['driver'] ?></strong></p>
    </div>
    <div class="col-xs-6 col-sm-3">
        Expedition :
        <p class="mb0"><strong><?= $safeConduct['expedition'] ?></strong></p>
    </div>
    <div class="col-xs-6 col-sm-3">
        Published At :
        <p class="mb0">
            <strong>
                <?= format_date($safeConduct['created_at'], 'd F Y H:i') ?>
            </strong>
        </p>
    </div>
</div>

<hr>

<?php if (!empty($safeConductContainers)): ?>
    <p class="lead mt10 mb10">Containers</p>
    <table class="table table-bordered table-condensed table-striped no-datatable mb10">
        <thead>
        <tr>
            <th style="width: 25px">No</th>
            <th>No Container</th>
            <th>Type</th>
            <th>Size</th>
            <th>Is Empty</th>
            <th>Status</th>
            <th>Danger</th>
            <th>Description</th>
        </tr>
        </thead>
        <tbody>
        <?php $no = 1; ?>
        <?php foreach ($safeConductContainers as $container): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $container['no_container'] ?></td>
                <td><?= $container['type'] ?></td>
                <td><?= $container['size'] ?></td>
                <td class="<?= $container['is_empty'] ? 'text-danger' :'' ?>">
                    <?= $container['is_empty'] ? 'Empty' : 'Full' ?>
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
                    <td colspan="7">
                        <table class="table table-condensed table-bordered no-datatable small">
                            <thead>
                            <tr>
                                <th style="width: 25px">No</th>
                                <th>Goods</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Total Weight (Kg)</th>
                                <th>Total Volume (M<sup>3</sup>)</th>
                                <th>Is Hold</th>
                                <th>Status</th>
                                <th>Danger</th>
                                <th>No Pallet</th>
                                <th>Ex Container</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $innerNo = 1; ?>
                            <?php foreach ($container['goods'] as $item): ?>
                                <tr>
                                    <td><?= $innerNo++ ?></td>
                                    <td><?= $item['goods_name'] ?></td>
                                    <td><?= numerical($item['quantity'], 2) ?></td>
                                    <td><?= $item['unit'] ?></td>
                                    <td><?= numerical($item['total_weight'], 2) ?></td>
                                    <td><?= numerical($item['total_volume']) ?></td>
                                    <td class="<?= $item['is_hold'] ? 'text-danger' :'' ?>">
                                        <?= $item['is_hold'] ? 'Yes' : 'No' ?>
                                    </td>
                                    <td><?= if_empty($item['status'], 'No status') ?></td>
                                    <td><?= if_empty($item['status_danger'], 'No status') ?></td>
                                    <td><?= if_empty($item['no_pallet'], '-') ?></td>
                                    <td><?= if_empty($item['ex_no_container'], '-') ?></td>
                                </tr>
                            <?php endforeach; ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>

        <?php if (empty($safeConductContainers)): ?>
            <tr>
                <td colspan="8" class="text-center">No data available</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>


<?php if (!empty($safeConductGoods)): ?>
    <p class="lead mt10 mb10">Goods</p>
    <table class="table table-bordered table-condensed table-striped no-datatable mb10">
        <thead>
        <tr>
            <th style="width: 25px" rowspan="2">No</th>
            <th rowspan="2">Description</th>
            <th rowspan="2">Ex Container</th>
            <th rowspan="2">Label Number</th>
            <th rowspan="2">Qty</th>
            <th rowspan="2">Unit</th>
            <th rowspan="2">Unit Weight (Kg)</th>
            <th rowspan="2">Total Weight (Kg)</th>
            <th colspan="2" class="text-center">Condition</th>
            <th rowspan="2">Remarks</th>
        </tr>
        <tr>
            <th>Issued</th>
            <th>Receive</th>
        </tr>
        </thead>
        <tbody>
        <?php $no = 1; ?>
        <?php foreach ($safeConductGoods as $item): 
            $goods_name = "";
            $goods_name = $item['goods_name'];
            if (strlen($goods_name) >= 9 && strpos($goods_name,"\"",9)) {
                $goods_name_arr = explode ("\"",$goods_name);
                $goods_name = "";
                $i=0;
                foreach ($goods_name_arr as $key) {
                    if ($i==0) {
                        $goods_name .= $key;
                    } else {
                        $goods_name .= "\" ". $key;
                    }
                    $i++;
                }
            }
            if (empty($item['whey_number'])) {
                $item['no_goods']=chunk_split($item['no_goods'],13," ");
            }?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $goods_name ?></td>
                <td><?= if_empty($item['ex_no_container'], '-') ?></td>
                <td><?= if_empty($item['whey_number'], $item['no_goods']) ?></td>
                <td><?= numerical($item['quantity'], 3, true) ?></td>
                <td><?= $item['unit'] ?></td>
                <td><?= numerical($item['unit_weight'], 3, true) ?></td>
                <td><?= numerical($item['total_weight'], 3, true) ?></td>
                <td><?= if_empty($item['status'], 'No status') ?></td>
                <td></td>
                <td></td>
            </tr>
        <?php endforeach; ?>

        <?php if (empty($safeConductGoods)): ?>
            <tr>
                <td colspan="11" class="text-center">No data available</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>

<div class="row mt10">
    <hr>
    <div class="col-xs-3">
        Description :
        <p class="mb0">
            <strong>
                <?= if_empty($safeConduct['description'], '-') ?>
            </strong>
        </p>
    </div>
    <div class="col-xs-3">
        Source Warehouse :
        <p class="mb0">
            <strong>
                <?= if_empty($safeConduct['source_warehouse'], '-') ?>
            </strong>
        </p>
    </div>
    <div class="col-xs-3">
        Security Check In :
        <p class="mb0">
            <strong>
                <?php
                if (is_null($safeConduct['security_in_date'])) {
                    echo '-';
                } else {
                    echo format_date($safeConduct['security_in_date'], 'd F Y H:i');
                }
                ?>
            </strong>
        </p>
    </div>
    <div class="col-xs-3">
        Security Check Out :
        <p class="mb0">
            <strong>
                <?php
                if (is_null($safeConduct['security_out_date'])) {
                    echo '-';
                } else {
                    echo format_date($safeConduct['security_out_date'], 'd F Y H:i');
                }
                ?>
            </strong>
        </p>
    </div>
</div>

<div class="row">
    <hr>
    <div class="col-xs-3 text-center">
        <p><strong>Customer</strong></p>
        <br><br>
        ( . . . . . . . . . . . . . . . . . . . )
    </div>
    <div class="col-xs-3 text-center">
        <p><strong>Security</strong></p>
        <br><br>
        ( . . . . . . . . . . . . . . . . . . . )
    </div>
    <div class="col-xs-3 text-center">
        <p><strong>Transporter</strong></p>
        <br><br>
        ( . . . . . . . . . . . . . . . . . . . )
    </div>
    <div class="col-xs-3 text-center">
        <p><strong>Admin Gate</strong></p>
        <br><br>
        ( . . . . . . . . . . . . . . . . . . . )
    </div>
</div>
<div class="divFooter">
    <span class="mr20">Customer . . . . .</span>
    <span class="mr20">Security . . . . .</span>
    <span class="mr20">Transporter . . . . .</span>
    <span>Admin Gate . . . . .</span>
</div>