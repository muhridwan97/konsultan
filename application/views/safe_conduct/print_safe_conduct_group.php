<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8'>
    <title><?= $safeConduct['no_safe_conduct_group'] ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900">
    <style>
        @page {
            margin: 145px 30px 20px 20px;
        }

        * {
            -webkit-box-sizing: border-box;
            -moz-box-sizing: border-box;
            box-sizing: border-box;
        }

        body {
            margin: 5px;
            background: none;
            font-family: Roboto, sans-serif;
            font-weight: 500;
            font-size: 11px;
            line-height: 1.1;
        }

        hr {
            height: 0;
            -webkit-box-sizing: content-box;
            -moz-box-sizing: content-box;
            box-sizing: content-box;
            margin-top: 10px;
            margin-bottom: 10px;
            border: 0;
            border-top: 1px solid #ddd;
        }

        p {
            margin: 0;
            font-family: Roboto, sans-serif;
            font-weight: 500;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            vertical-align: middle;
        }

        table th {
            font-family: Roboto, sans-serif;
            font-weight: 600;
        }

        table.table {
            width: 100%;
            border-collapse: collapse;
            border: 1px solid #000;
        }

        table.table td, table.table th {
            vertical-align: middle;
            border: 1px solid #000;
            padding: 3px;
        }

        .text-center {
            text-align: center;
        }

        .text-right {
            text-align: right;
        }

        small {
            font-size: 9px;
        }
        .header {
            position: fixed;
            top: -115px;
            left: 5px;
            right: 5px;
        }
        .footer {
            position: fixed;
            bottom: 20px;
            left: 5px;
            right: 5px;
        }
    </style>
</head>
<body>
<div class="header">
    <table>
        <tr>
            <td colspan="2">
                <img src="<?= FCPATH . 'assets/app/img/layout/transcon_logo.png' ?>" style="width: 35px; margin-right: 5px; display: inline-block"/>
                <div style="display: inline-block">
                    <p class="lead" style="margin-bottom: 0; line-height: .8; font-weight: bold">Transcon Indonesia</p>
                    <small class="text-muted" style="letter-spacing: 1px">www.transcon-indonesia.com</small>
                </div>
            </td>
            <td class="text-right">
                <small>Print Date: <strong><?= get_url_param('print_date', date('d F Y H:i')) ?></strong></small>
            </td>
        </tr>
    </table>
    <hr style="margin-top: 5px">
    <table>
        <tr>
            <td style="width: 250px">
                <h3 style="margin-bottom: 0; margin-top: 0">SURAT JALAN</h3>
                <p style="margin-bottom: 5px">
                    No Safe Conduct: <strong><?= $safeConduct['no_safe_conduct_group'] ?></strong>
                </p>
                <p><strong>BRANCH <?= $safeConduct['branch'] ?></strong></p>
                <small>SAFE CONDUCT GROUP</small>
            </td>
            <td>
                Total Safe Conduct: <strong><?= count($safeConductGroups) ?> safe conducts</strong><br>
                Total Page:
            </td>
            <td class="text-right">
                <img src="data:image/png;base64,<?= $barcodeSafeConduct ?>"
                     alt="<?= $safeConduct['no_safe_conduct'] ?>" width="60px" style="padding: 4px; outline: 1px solid #aaaaaa;">
            </td>
        </tr>
    </table>
</div>

<div class="footer" style="text-align: right">
    <span style="margin-right: 20px">Customer . . . . .</span>
    <span style="margin-right: 20px">Security . . . . .</span>
    <span style="margin-right: 20px">Transporter . . . . .</span>
    <span>Admin Gate . . . . .</span>
</div>

<table>
    <tr>
        <td>
            Police :
            <p><strong><?= $safeConduct['no_police'] ?></strong></p>
        </td>
        <td>
            Driver :
            <p><strong><?= $safeConduct['driver'] ?></strong></p>
        </td>
        <td>
            Expedition :
            <p><strong><?= $safeConduct['expedition'] ?></strong></p>
        </td>
        <td>
            Tep Code :
            <p><strong><?= $safeConduct['tep_code'] ?></strong></p>
        </td>
    </tr>
</table>
<hr>

<?php if (empty($safeConductContainers) && empty($safeConductGoods)): ?>
    <?php if (!empty($safeConductGroups)): ?>
        <h3 style="margin-bottom: 10px; margin-top: 0">References</h3>
        <table class="table table-bordered" style="margin-bottom: 15px">
            <thead>
            <tr>
                <th style="width: 25px" class="text-center">No</th>
                <th>No Reference</th>
                <th>No Safe Conduct</th>
                <th>Customer</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($safeConductGroups as $index => $safeConductGroup): ?>
                <tr>
                    <td class="text-center"><?= $index + 1 ?></td>
                    <td><?= $safeConductGroup['no_reference'] ?></td>
                    <td><?= $safeConductGroup['no_safe_conduct'] ?></td>
                    <td><?= $safeConductGroup['customer_name'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
<?php endif; ?>

<?php if (!empty($safeConductContainers)): ?>
    <h3 style="margin-bottom: 10px; margin-top: 0">Containers</h3>
    <table class="table table-bordered" style="margin-bottom: 15px">
        <thead>
        <tr>
            <th style="width: 25px" class="text-center">No</th>
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
        <?php $containerCounter = 1; ?>
        <?php foreach ($safeConductGroups as $groupIndex => $safeConductItem): ?>
            <?php if(!empty($safeConductItem['containers']) || (empty($safeConductItem['containers']) && empty($safeConductGoods))): ?>
                <tr>
                    <td></td>
                    <td colspan="7">
                        <p class="mb0"><?= $safeConductItem['customer_name'] ?></p>
                        <strong><?= $safeConductItem['no_reference'] ?></strong>
                        &nbsp; <span class="text-muted">(<?= $safeConductItem['no_safe_conduct'] ?>)</span>
                        &nbsp;- <strong><?= $containerCounter++ ?></strong>
                    </td>
                </tr>
                <?php $no = 1; ?>
                <?php foreach ($safeConductItem['containers'] as $container): ?>
                    <tr>
                        <td class="text-center"><?= $no++ ?></td>
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
                <tr>
                    <td colspan="8">&nbsp;</td>
                </tr>
            <?php endif; ?>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if (!empty($safeConductGoods)): ?>
    <h3 style="margin-bottom: 10px; margin-top: 0">Goods</h3>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th style="width: 25px" rowspan="2" class="text-center">No</th>
            <th rowspan="2" style="width: 160px">Description</th>
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
        <?php $goodsCounter = 1; ?>
        <?php foreach ($safeConductGroups as $groupIndex => $safeConductItem): ?>
            <tr>
                <td></td>
                <td colspan="10">
                    <p class="mb0"><?= $safeConductItem['customer_name'] ?></p>
                    <strong><?= $safeConductItem['no_reference'] ?></strong>
                    &nbsp; <span class="text-muted">(<?= $safeConductItem['no_safe_conduct'] ?>)</span>
                    &nbsp;- <strong><?= $goodsCounter++ ?></strong>
                </td>
            </tr>
            <?php $no = 1; ?>
            <?php foreach ($safeConductItem['goods'] as $item):
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
                    <td class="text-center"><?= $no++ ?></td>
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
            <tr>
                <td colspan="11">&nbsp;</td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>


<hr>

<table>
    <tr>
        <td>
            Description :
            <p>
                <strong>
                    <?= if_empty($safeConduct['description'], '-') ?>
                </strong>
            </p>
        </td>
        <td>
            Source Warehouse :
            <p>
                <strong>
                    <?= if_empty($safeConduct['source_warehouse'], '-') ?>
                </strong>
            </p>
        </td>
        <td>
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
        </td>
        <td>
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
        </td>
    </tr>
</table>

<hr>

<table style="margin-top: 20px">
    <tr>
        <td class="text-center">
            <p><strong>Customer</strong></p>
            <br><br><br><br><br>
            ( . . . . . . . . . . . . . . . . . . . )
        </td>
        <td class="text-center">
            <p><strong>Security</strong></p>
            <br><br><br><br><br>
            ( . . . . . . . . . . . . . . . . . . . )
        </td>
        <td class="text-center">
            <p><strong>Transporter</strong></p>
            <br><br><br><br><br>
            ( . . . . . . . . . . . . . . . . . . . )
        </td>
        <td class="text-center">
            <p><strong>Admin Gate</strong></p>
            <br><br><br><br><br>
            ( . . . . . . . . . . . . . . . . . . . )
        </td>
    </tr>
</table>

<script type="text/php">
    $x = 252;
    $y = 83;
    $text = "{PAGE_NUM} of {PAGE_COUNT}";
    $font = $fontMetrics->get_font("helvetica", "bold");
    $size = 8;
    $pdf->page_text($x, $y, $text, $font, $size);
</script>
</body>
</html>
