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
            No Invoice In: <?= if_empty($safeConduct['no_invoice'], '-') ?>
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
        No Police / Vehicle:
        <p class="mb0"><strong><?= $safeConduct['no_police'] ?> / <?= $safeConduct['vehicle_type'] ?></strong></p>
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
                <?= (new DateTime($safeConduct['created_at']))->format('d F Y H:i') ?>
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
            <th>Seal</th>
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
                <td><?= $container['seal'] ?></td>
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
                    <td colspan="8">
                        <table class="table table-condensed table-bordered no-datatable small">
                            <thead>
                            <tr>
                                <th style="width: 25px">No</th>
                                <th>Goods</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Unit Weight (Kg)</th>
                                <th>Total Weight (Kg)</th>
                                <th>Unit Volume (M<sup>3</sup>)</th>
                                <th>Total Volume (M<sup>3</sup>)</th>
                                <th>Unit Length (M)</th>
                                <th>Unit Width (M)</th>
                                <th>Unit Height (M)</th>
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
                                    <td><?= numerical($item['quantity'], 3, true) ?></td>
                                    <td><?= $item['unit'] ?></td>
                                    <td><?= numerical($item['unit_weight'], 3, true) ?></td>
                                    <td><?= numerical($item['total_weight'], 3, true) ?></td>
                                    <td><?= numerical($item['unit_volume']) ?></td>
                                    <td><?= numerical($item['total_volume']) ?></td>
                                    <td><?= numerical($item['unit_length'], 3, true) ?></td>
                                    <td><?= numerical($item['unit_width'], 3, true) ?></td>
                                    <td><?= numerical($item['unit_height'], 3, true) ?></td>
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
        </tbody>
    </table>
<?php endif; ?>


<?php if (!empty($safeConductGoods)): ?>
    <p class="lead mt10 mb10">Goods</p>
    <table class="table table-bordered table-condensed table-striped no-datatable mb10">
        <thead>
        <tr>
            <th style="width: 25px">No</th>
            <th>Goods</th>
            <th>Qty</th>
            <th>Unit</th>
            <th>Unit Weight (Kg)</th>
            <th>Total Weight (Kg)</th>
            <th>Unit Volume (M<sup>3</sup>)</th>
            <th>Total Volume (M<sup>3</sup>)</th>
            <!-- <th>Unit Length (M)</th>
            <th>Unit Width (M)</th>
            <th>Unit Height (M)</th> -->
            <th>Status</th>
            <th>Danger</th>
            <th>No Pallet</th>
            <th>Ex Container</th>
        </tr>
        </thead>
        <tbody>
        <?php $no = 1; ?>
        <?php foreach ($safeConductGoods as $item): 
            $no_pallet="";
            if (!empty($item['no_pallet'])) {
                $no_pallet = $item['no_pallet'];
                $no_pallet_arr = explode ("/",$no_pallet);
                $no_pallet = "";
                $i=0;
                foreach ($no_pallet_arr as $key) {
                    if ($i==0) {
                        $no_pallet .= $key;
                    } else {
                        $no_pallet .= "/ ". $key;
                    }
                    $i++;
                }
            }
            //untuk merapikan nama goods yang panjang
            $goods_name = "";
            $goods_name = $item['goods_name'];
            if (strlen($goods_name)>9) {
                if (strpos($goods_name,"\"",9)) {
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
            }
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $goods_name ?></td>
                <td><?= numerical($item['quantity'], 3, true) ?></td>
                <td><?= $item['unit'] ?></td>
                <td><?= numerical($item['unit_weight'], 3, true) ?></td>
                <td><?= numerical($item['total_weight'], 3, true) ?></td>
                <td><?= numerical($item['unit_volume']) ?></td>
                <td><?= numerical($item['total_volume']) ?></td>
                <td><?= if_empty($item['status'], 'No status') ?></td>
                <td><?= if_empty($item['status_danger'], 'No status') ?></td>
                <td><?= if_empty($no_pallet, '-') ?></td>
                <td><?= if_empty($item['ex_no_container'], '-') ?></td>
            </tr>
        <?php endforeach; ?>
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
        Security Check In (Start):
        <p class="mb0">
            <strong>
                <?= if_empty(format_date($safeConduct['security_in_date'], 'd F Y H:i'), '-') ?>
            </strong>
        </p>
    </div>
    <div class="col-xs-3">
        Security Check Out (Stop):
        <p class="mb0">
            <strong>
                <?= if_empty(format_date($safeConduct['security_out_date'], 'd F Y H:i'), '-') ?>
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
        <p><strong>Staff Hangar BC</strong></p>
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
    <span class="mr20">Staff Hangar BC . . . . .</span>
    <span>Admin Gate . . . . .</span>
</div>