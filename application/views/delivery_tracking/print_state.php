<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $this->config->item('app_name') ?> | Delivery Tracking</title>
    <style>
        @page { margin: 30px; }

        * {
            margin: 0;
            padding: 0;
        }

        body { font-size: 11px; font-family: sans-serif; margin: 30px; }

        p {
            line-height: 1.4;
        }

        small {
            font-size: 9px;
        }

        table td, table th {
            padding: 4px 2px;
            text-align: left;
        }

        .table {
            border: 1px solid #dddddd;
        }
        .table td, .table th {
            border-top: 1px solid #dddddd;
            border-bottom: 1px solid #dddddd;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            color: #212529;
            font-size: 10px;
        }
        table table {
            font-size: 10px;
            margin: -1px 5px 10px;
        }
        .pt-md-0 {
            padding-top: 0;
        }
        .table-warning {
            background-color: #ffe9b8;
        }
        .table-danger {
            background-color: #f8cfce;
        }
        .table-secondary {
            background-color: #f8f8f8;
        }
        .text-muted {
            color: #999999;
        }
        .font-weight-bold {
            font-weight: bold;
        }
        .text-right {
            text-align: right;
        }
        .text-center {
            text-align: center;
        }
        .pull-left {
            float: left;
        }
        .pull-right {
            float: right;
        }
        .clearfix {
            clear: both;
        }
        hr {
            height:0;
            border: 0;
            border-top: 1px solid #dddddd;
            -webkit-box-sizing:content-box;
            -moz-box-sizing:content-box;
            box-sizing:content-box;
        }
    </style>
</head>
<body>
<div style="text-align: center; margin-bottom: 10px">
    <img src="<?= FCPATH . 'assets/app/img/layout/kop_email.jpg' ?>" style="width: 550px; height: 75px">
</div>

<hr>

<table style="margin-bottom: 10px; margin-top: 20px">
    <tr>
        <td style="width: 120px">NO DELIVERY</td>
        <td style="width: 10px">:</td>
        <td class="font-weight-bold"><?= $deliveryTracking['no_delivery_tracking'] ?></td>
        <td style="width: 120px">CUSTOMER</td>
        <td style="width: 10px">:</td>
        <td class="font-weight-bold"><?= $deliveryTracking['customer_name'] ?></td>
    </tr>
    <tr>
        <td>STATUS DATE</td>
        <td style="width: 10px">:</td>
        <td class="font-weight-bold"><?= format_date(if_empty($deliveryTrackingDetail['status_date'], $deliveryTrackingDetail['created_at']), 'd M Y H:i') ?></td>
        <td>UNLOAD LOCATION</td>
        <td style="width: 10px">:</td>
        <td class="font-weight-bold"><?= if_empty($deliveryTrackingDetail['unload_location'], '-') ?></td>
    </tr>
    <tr>
        <td>ARRIVAL DATE</td>
        <td style="width: 10px">:</td>
        <td class="font-weight-bold"><?= format_date($deliveryTrackingDetail['arrival_date'], 'd M Y H:i') ?> (<?= $deliveryTrackingDetail['arrival_date_type'] ?>)</td>
        <td>UNLOAD DATE</td>
        <td style="width: 10px">:</td>
        <td class="font-weight-bold"><?= format_date($deliveryTrackingDetail['unload_date'], 'd M Y H:i') ?> (<?= $deliveryTrackingDetail['unload_date_type'] ?>)</td>
    </tr>
    <tr>
        <td style="vertical-align: top">TRACKING MESSAGE</td>
        <td style="vertical-align: top; width: 10px">:</td>
        <td style="vertical-align: top" class="font-weight-bold" colspan="4"><?= if_empty($deliveryTrackingDetail['tracking_message'], '-') ?></td>
    </tr>
</table>

<p class="font-weight-bold">TRACKING DETAIL</p>
<br>
<table class="table">
    <thead>
    <tr>
        <th style="width: 60px">Police No</th>
        <th style="width: 80px">Safe Conduct</th>
        <th style="width: 80px">Invoice</th>
        <th style="width: 90px">Whey Number</th>
        <th style="width: 100px">Goods</th>
        <th style="width: 40px">Qty</th>
        <th>Note</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($reports as $index => $deliveryItem): ?>
        <tr>
            <td><?= $deliveryItem['no_police'] ?></td>
            <td><?= $deliveryItem['no_safe_conduct'] ?></td>
            <td><?= $deliveryItem['no_invoice'] ?></td>
            <td><?= $deliveryItem['whey_number'] ?></td>
            <td><?= $deliveryItem['goods_name'] ?></td>
            <td><?= numerical($deliveryItem['quantity'], 2, true) ?></td>
            <td>
                <?= if_empty($deliveryItem['unload_location'], '', '', ' - ') ?>
                <?= if_empty($deliveryItem['description'], 'No description') ?>
            </td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>

</body>
</html>

