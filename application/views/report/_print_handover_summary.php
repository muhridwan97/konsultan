<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">

    <title><?= $title ?> | Warehouse</title>
    <link rel="shortcut icon" href="<?= base_url('assets/app/img/layout/favicon.png') ?>">
    <!-- Tell the browser to be responsive to screen width -->
    <meta content="width=device-width, initial-scale=1, maximum-scale=1, user-scalable=no" name="viewport">
    <!-- Bootstrap 3.3.7 -->
    <link rel="stylesheet" href="<?= base_url('assets/plugins/bootstrap/css/bootstrap.min.css') ?>">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('assets/template/css/AdminLTE.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/template/css/skins/skin-blue.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/app/css/app.css') ?>">
    <style>
        body
        {
            background-color:#FFFFFF;
        }
        table { page-break-inside:avoid }
        tr    { page-break-inside:avoid; page-break-after:auto }
        thead { display:table-header-group }
        tfoot { display:table-footer-group }

        @media screen {
            div.divFooter {
                display: none;
            }
        }
        @media print {
            div.divFooter {
                position: fixed;
                bottom: 0;
                right: 0;
            }
        }
    </style>
    <!-- Google Font -->
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <script type="text/javascript">
        window.baseUrl = <?php echo json_encode(base_url()); ?>;
    </script>
</head>
<body onload="window.print();">

<span class="pull-right pt10">
    <strong>Date:</strong>
    <?= get_url_param('from_date', readable_date('now', false)) ?>
    <?php if(get_url_param('from_date') != get_url_param('to_date')): ?>
        - <?= get_url_param('to_date', readable_date('now', false)) ?>
    <?php endif; ?>
</span>
<p class="lead mb0"><strong>TALLY SHEET REPORT</strong></p>
<p class="mb10"><strong>BRANCH <?= get_active_branch('branch') ?></strong></p>

<?php if(empty($reportHandovers)): ?>
    <p class="lead">There is no activity available.</p>
<?php endif; ?>


<?php $outerNo = 1 ?>
<?php foreach ($reportHandovers as $reportHandover): ?>

    <table class="table table-bordered table-condensed no-datatable">
        <thead>
        <tr>
            <th style="width: 25px">No</th>
            <th>Customer</th>
            <th>Handling Type</th>
            <th>No Job</th>
            <th>No Police</th>
            <th>No Invoice</th>
            <th>No Booking</th>
            <th>Job Complete Date</th>
            <th>Taken By</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <td><?= $outerNo++ ?></td>
            <td><?= $reportHandover['customer_name'] ?></td>
            <td><?= $reportHandover['handling_type'] ?></td>
            <td><?= $reportHandover['no_work_order'] ?></td>
            <td><?= if_empty($reportHandover['no_police'], '-') ?></td>
            <td><?= if_empty($reportHandover['invoice_number'], '-') ?></td>
            <td><?= $reportHandover['no_booking'] ?></td>
            <td><?= readable_date($reportHandover['completed_at'], false) ?></td>
            <td><?= $reportHandover['tally_name'] ?></td>
        </tr>
        <tr>
            <td></td>
            <td colspan="8">
                <?php $containers = $reportHandover['containers'] ?>
                <?php if (!empty($containers)): ?>
                    <table class="table table-bordered table-condensed table-striped mb0">
                        <thead>
                        <tr>
                            <th style="width: 25px">No</th>
                            <th>No Container</th>
                            <th>Type</th>
                            <th>Size</th>
                            <th>Seal</th>
                            <th>Danger</th>
                            <th>Is Empty</th>
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
                                <td><?= $container['seal'] ?></td>
                                <td><?= $container['status_danger'] ?></td>
                                <td><?= $container['is_empty'] ? 'Empty' : 'Full' ?></td>
                            </tr>
                            <?php
                            $containerGoodsExist = key_exists('goods', $container) && !empty($container['goods']);
                            $containerContainersExist = key_exists('containers', $container) && !empty($container['containers']);
                            ?>
                            <?php if ($containerGoodsExist || $containerContainersExist): ?>
                                <tr>
                                    <td></td>
                                    <td colspan="6">
                                        <?php if ($containerContainersExist): ?>
                                            <table class="table table-condensed no-datatable mb0">
                                                <thead>
                                                <tr>
                                                    <th style="width: 25px">No</th>
                                                    <th>No Container</th>
                                                    <th>Type</th>
                                                    <th>Size</th>
                                                    <th>Seal</th>
                                                    <th>Danger</th>
                                                    <th>Is Empty</th>
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
                                                        <td><?= $containerItem['seal'] ?></td>
                                                        <td><?= $containerItem['status_danger'] ?></td>
                                                        <td><?= $containerItem['is_empty'] ? 'Empty' : 'Full' ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <?php endif; ?>

                                        <?php if ($containerGoodsExist): ?>
                                            <table class="table table-condensed no-datatable mb0">
                                                <thead>
                                                <tr>
                                                    <th style="width: 25px">No</th>
                                                    <th>Goods</th>
                                                    <th>Quantity</th>
                                                    <th>Unit</th>
                                                    <th>Tonnage (Kg)</th>
                                                    <th>Volume (M<sup>3</sup>)</th>
                                                    <th>Position</th>
                                                    <th>No Pallet</th>
                                                    <th>Danger</th>
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
                                                        <td><?= numerical($item['tonnage'], 2) ?></td>
                                                        <td><?= numerical($item['volume'], 2) ?></td>
                                                        <td><?= if_empty($item['position'], '-') ?></td>
                                                        <td><?= if_empty($item['no_pallet'], '-') ?></td>
                                                        <td><?= $item['status_danger'] ?></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                                </tbody>
                                            </table>
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>

                        <?php if (empty($containers)): ?>
                            <tr>
                                <td colspan="7" class="text-center">No data available</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                <?php endif; ?>


                <?php $goods = $reportHandover['goods'] ?>
                <?php if(!empty($goods)): ?>
                    <table class="table table-bordered table-condensed table-striped mb0">
                        <thead>
                        <tr>
                            <th style="width: 25px">No</th>
                            <th>Goods</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Tonnage (Kg)</th>
                            <th>Volume (M<sup>3</sup>)</th>
                            <th>Position</th>
                            <th>No Pallet</th>
                            <th>Danger</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1; ?>
                        <?php foreach ($goods as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $item['goods_name'] ?></td>
                                <td><?= numerical($item['quantity'], 2) ?></td>
                                <td><?= $item['unit'] ?></td>
                                <td><?= numerical($item['tonnage'], 2) ?></td>
                                <td><?= numerical($item['volume'], 2) ?></td>
                                <td><?= if_empty($item['position'], '-') ?></td>
                                <td><?= if_empty($item['no_pallet'], '-') ?></td>
                                <td><?= if_empty($item['status_danger'], '-') ?></td>
                            </tr>
                            <?php if (key_exists('goods', $item) && !empty($item['goods'])): ?>
                                <tr>
                                    <td></td>
                                    <td colspan="8">
                                        <table class="table table-condensed mb0">
                                            <thead>
                                            <tr>
                                                <th style="width: 25px">No</th>
                                                <th>Goods</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                                <th>Tonnage (Kg)</th>
                                                <th>Volume (M<sup>3</sup>)</th>
                                                <th>Position</th>
                                                <th>No Pallet</th>
                                                <th>Danger</th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            <?php $innerNo = 1; ?>
                                            <?php foreach ($item['goods'] as $itemGoods): ?>
                                                <tr>
                                                    <td><?= $innerNo++ ?></td>
                                                    <td><?= $itemGoods['goods_name'] ?></td>
                                                    <td><?= numerical($itemGoods['quantity'],2) ?></td>
                                                    <td><?= $itemGoods['unit'] ?></td>
                                                    <td><?= numerical($itemGoods['tonnage'], 2) ?></td>
                                                    <td><?= numerical($itemGoods['volume'], 2) ?></td>
                                                    <td><?= if_empty($itemGoods['position'], '-') ?></td>
                                                    <td><?= if_empty($itemGoods['no_pallet'], '-') ?></td>
                                                    <td><?= if_empty($itemGoods['status_danger'], '-') ?></td>
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
            </td>
        </tr>
        </tbody>
    </table>

<?php endforeach; ?>

<div class="row" style="margin-bottom: 20px">
    <hr>
    <div class="col-xs-4 text-center">
        <p><strong>Tally Staff 1</strong></p>
        <br><br>
        ( . . . . . . . . . . . . . . . . . . . )
    </div>
    <div class="col-xs-4 text-center">
        <p><strong>Tally Staff 2</strong></p>
        <br><br>
        ( . . . . . . . . . . . . . . . . . . . )
    </div>
    <div class="col-xs-4 text-center">
        <p><strong>Supervisor</strong></p>
        <br><br>
        ( . . . . . . . . . . . . . . . . . . . )
    </div>
</div>

<div class="divFooter">
    <span class="mr20">Tally Staff 1 . . . . .</span>
    <span class="mr20">Tally Staff 2 . . . . .</span>
    <span>Supervisor . . . . .</span>
</div>

</body>
</html>
