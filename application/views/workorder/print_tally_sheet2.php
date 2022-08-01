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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.5.0/css/font-awesome.min.css">
    <!-- Ionicons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/ionicons/2.0.1/css/ionicons.min.css">
    <!-- Theme style -->
    <link rel="stylesheet" href="<?= base_url('assets/template/css/AdminLTE.min.css') ?>">
    <!-- AdminLTE Skins. We have chosen the skin-blue for this starter
        page. However, you can choose any other skin. Make sure you
    apply the skin class to the body tag so the changes take effect. -->
    <link rel="stylesheet" href="<?= base_url('assets/template/css/skins/skin-blue.min.css') ?>">
    <link rel="stylesheet" href="<?= base_url('assets/app/css/app.css') ?>">

    <!-- HTML5 Shim and Respond.js IE8 support of HTML5 elements and media queries -->
    <!-- WARNING: Respond.js doesn't work if you view the page via file:// -->
    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <!-- Google Font -->
    <link rel="stylesheet"
          href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,600,700,300italic,400italic,600italic">
    <style>
        table tr td, table tr th {
            font-size: 12px !important;
            padding: 0 !important;
        }
        .table-form td, .table-form th {
            border: none !important;
        }
    </style>
</head>
<body onload="window.print();">

<div class="page-header clearfix" style="padding-bottom: 15px; margin-bottom: 15px; margin-top: 0">
    <div class="pull-left">
        <img style="display: inline-block; width: 50px;"
             src="<?= base_url('assets/app/img/layout/transcon_logo.png') ?>" alt="Transcon Logo">
        <p class="lead" style="display: inline-block; margin-bottom: 0;">TRANSCON INDONESIA</p>
    </div>
    <div class="lead pull-right" style="margin-bottom: 0; line-height: .8; font-weight: bold">
        TALLY SHEET
        <?php if($fromInboundPackage ?? false): ?>
            <span class="text-danger mb0 mt0">(P)</span>
        <?php endif; ?>
    </div>
</div>


<table class="table table-condensed table-form" style="margin-bottom: 10px">
    <tr>
        <th>Branch</th>
        <td><?= $workOrder['branch'] ?></td>
        <th>Job Number</th>
        <td><?= $workOrder['no_work_order'] ?></td>
    </tr>
    <tr>
        <th>Customer</th>
        <td><?= $workOrder['customer_name'] ?></td>
        <th>Taken At</th>
        <td><?= readable_date($workOrder['taken_at']) ?></td>
    </tr>
    <tr>
        <th>Activity</th>
        <td><?= $workOrder['handling_type'] ?></td>
        <th>Completed At</th>
        <td><?= readable_date($workOrder['completed_at']) ?></td>
    </tr>
    <tr>
        <th>No Reference</th>
        <td><?= $workOrder['no_reference'] ?></td>
        <th>Containers / Payload</th>
        <td>
            <?php foreach ($containers as $index => $container) {
                if ($index > 0) {
                    echo ', ';
                }
                echo $container['no_container'] . ' (' . $container['type'] . ' - ' . $container['size'] . ')'.' / '.numerical($container['volume_payload'], 3, true).'(M<sup>3</sup>)('.numerical($container['length_payload'], 3, true).'x'.numerical($container['width_payload'], 3, true).'x'.numerical($container['height_payload'], 3, true).')';
            } ?>
            <?= empty($containers) ? '-' : '' ?>
        </td>
    </tr>
    <tr>
        <th>No Police</th>
        <td><?= if_empty($workOrder['no_police'], '-') ?></td>
        <th>Driver / Vehicle Type</th>
        <td><?= if_empty($workOrder['driver'], '-') ?> / <?= if_empty($workOrder['vehicle_type'], '-') ?></td>
    </tr>
</table>

<table class="table table-bordered table-condensed table-striped no-datatable" style="margin-bottom: 15px">
    <thead>
    <tr>
        <th style="width: 25px" rowspan="2">No</th>
        <th rowspan="2">Goods</th>
        <th rowspan="2">Package</th>
        <th rowspan="2">Qty</th>
        <th rowspan="2">Unit</th>
        <th colspan="2" class="text-center">Total Weight</th>
        <th colspan="4" class="text-center">Dimension</th>
        <th rowspan="2">Position</th>
        <th rowspan="2">No Pallet</th>
        <th rowspan="2">Ex Container</th>
        <th rowspan="2">Description</th>
        <th colspan="2" class="text-center">Overtime</th>
    </tr>
    <tr>
        <th>Total Net (Kg)</th>
        <th>Total Gross (Kg)</th>
        <th>Total Volume (M<sup>3</sup>)</th>
        <th>Unit Length (M)</th>
        <th>Unit Width (M)</th>
        <th>Unit Height (M)</th>
        <th>Status</th>
        <th>Date</th>
    </tr>
    </thead>
    <tbody>
    <?php
    $no = 1;
    $totalQuantity = 0;
    $totalWeight = 0;
    $totalGross = 0;
    $totalVolume = 0;
    $no = 1;
    ?>
    <?php foreach ($containers as $container): ?>
        <?php if(key_exists('goods', $container) && !empty($container['goods'])): ?>
            <?php foreach ($container['goods'] as $item): ?>
                <?php
                $totalQuantity += $item['quantity'];
                $totalWeight += $item['total_weight'];
                $totalGross += $item['total_gross_weight'];
                $totalVolume += $item['total_volume'];
                ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $item['goods_name'] ?><br><small class="text-muted"><?= $item['no_goods'] ?></small></td>
                    <td>
                        <?= if_empty($item['parent_goods_name'], '-') ?><br>
                        <small class="text-muted"><?= $item['parent_no_goods'] ?></small>
                        <?php if($workOrder['handling_type'] == 'LOAD'): ?>
                            - <small class="text-muted">Unpackage: <?= numerical($item['unpackage_quantity'] ?? 0, 3, true) ?? 0 ?></small>
                        <?php endif; ?>
                    </td>
                    <td><?= numerical($item['quantity'], 3, true) ?></td>
                    <td><?= if_empty($item['unit'], '-') ?></td>
                    <td><?= numerical($item['total_weight'], 3, true) ?></td>
                    <td><?= numerical($item['total_gross_weight'], 3, true) ?></td>
                    <td><?= numerical($item['total_volume']) ?></td>
                    <td><?= numerical($item['unit_length'], 3, true) ?></td>
                    <td><?= numerical($item['unit_width'], 3, true) ?></td>
                    <td><?= numerical($item['unit_height'], 3, true) ?></td>
                    <td><?= if_empty($item['position'], '-')?> | <?= if_empty($item['type_warehouse'], '-')?></td>
                    <td><?= if_empty($item['no_pallet'], '-') ?></td>
                    <td><?= if_empty($item['ex_no_container'], '-') ?></td>
                    <td><?= if_empty($item['description'], '-') ?></td>
                    <td><?= if_empty($item['overtime_status'], '-') ?></td>
                    <td><?= if_empty($item['overtime_date'], '-') ?></td>
                </tr>
            <?php endforeach ?>
        <?php endif ?>
    <?php endforeach ?>

    <?php foreach ($goods as $item): ?>
        <?php if(empty($item['id_work_order_container'])): ?>
            <?php
            $totalQuantity += $item['quantity'];
            $totalWeight += $item['total_weight'];
            $totalGross += $item['total_gross_weight'];
            $totalVolume += $item['total_volume'];
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $item['goods_name'] ?><br><small class="text-muted"><?= $item['no_goods'] ?></small></td>
                <td>
                    <?= if_empty($item['parent_goods_name'], '-') ?><br>
                    <small class="text-muted"><?= $item['parent_no_goods'] ?></small>
                    <?php if($workOrder['handling_type'] == 'LOAD'): ?>
                        - <small class="text-muted">Unpackage: <?= numerical($item['unpackage_quantity'] ?? 0, 3, true) ?? 0 ?></small>
                    <?php endif; ?>
                </td>
                <td><?= numerical($item['quantity'], 3, true) ?></td>
                <td><?= if_empty($item['unit'], '-') ?></td>
                <td><?= numerical($item['total_weight'], 3, true) ?></td>
                <td><?= numerical($item['total_gross_weight'], 3, true) ?></td>
                <td><?= numerical($item['total_volume']) ?></td>
                <td><?= numerical($item['unit_length'], 3, true) ?></td>
                <td><?= numerical($item['unit_width'], 3, true) ?></td>
                <td><?= numerical($item['unit_height'], 3, true) ?></td>
                <td><?= if_empty($item['position'], '-') ?> | <?= if_empty($item['type_warehouse'], '-')?></td>
                <td><?= if_empty($item['no_pallet'], '-') ?></td>
                <td><?= if_empty($item['ex_no_container'], '-') ?></td>
                <td><?= if_empty($item['description'], '-') ?></td>
                <td><?= if_empty($item['overtime_status'], '-') ?></td>
                <td><?= if_empty(format_date($item['overtime_date'], 'd-m-Y H:i'), '-') ?></td>
            </tr>
        <?php endif ?>
    <?php endforeach ?>
    <tr>
        <th colspan="3">Total</th>
        <td><?= numerical($totalQuantity, 3, true) ?></td>
        <td></td>
        <td><?= numerical($totalWeight, 3, true) ?></td>
        <td><?= numerical($totalGross, 3, true) ?></td>
        <td><?= numerical($totalVolume, 3, true) ?></td>
        <td colspan="9"></td>
    </tr>
    </tbody>
</table>

<div class="row" style="font-size: 12px">
    <div class="col-xs-3">
        <strong>Man Power :</strong>
        <p><?= if_empty($workOrder['man_power'], 0) ?> People</p>
    </div>
    <div class="col-xs-3">
        <strong>Overtime :</strong>
        <p><?= numerical($workOrder['overtime'], 1, true) ?> Hours</p>
    </div>
    <div class="col-xs-3">
        <strong>Description :</strong>
        <p><?= if_empty($workOrder['description'], '-') ?></p>
    </div>
    <div class="col-xs-3">
        <strong>Tally :</strong>
        <br>
        <br>
        ( . . . . . . . . . . . . . . . . . . . )
    </div>
</div>

</body>
</html>

