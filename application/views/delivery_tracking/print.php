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
<div class="clearfix" style="height: 70px">
    <img class="pull-left" src="<?= FCPATH .'assets/app/img/layout/transcon_logo.png' ?>" style="width: 75px;">
    <p class="pull-right text-right" style="font-size: 14px; margin-top: 5px">Print Date<br><strong><?= date('d F Y') ?></strong></p>
    <div class="text-center" style="margin-left: 20px; margin-top: 10px">
        <h3 style="margin-bottom: 0">DELIVERY TRACKING</h3>
        <p style="margin-bottom: 10px; font-size: 14px">No: <?= $deliveryTracking['no_delivery_tracking'] ?></p>
    </div>
</div>

<hr>

<table style="margin-bottom: 10px; margin-top: 20px">
    <tr>
        <td>No Delivery Tracking</td>
        <td class="font-weight-bold">: <?= $deliveryTracking['no_delivery_tracking'] ?></td>
        <td>Customer</td>
        <td class="font-weight-bold">: <?= $deliveryTracking['customer_name'] ?></td>
    </tr>
    <tr>
        <td>Status</td>
        <td class="font-weight-bold">: <?= $deliveryTracking['status'] ?></td>
        <td>Description</td>
        <td class="font-weight-bold">: <?= $deliveryTracking['description'] ?></td>
    </tr>
</table>

<table class="table">
    <thead>
    <tr>
        <th class="text-center">No</th>
        <th>Tracking Message</th>
        <th>Arrival</th>
        <th>Unload</th>
        <th>Location</th>
        <th>Attachment</th>
        <th>Is Sent</th>
        <th>Created At</th>
        <th>Created By</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php foreach ($deliveryTrackingDetails as $deliveryTrackingDetail): ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td>
                <?= nl2br($deliveryTrackingDetail['tracking_message']) ?><br>
                <small class="text-muted"><?= format_date($deliveryTrackingDetail['status_date'], 'd M Y H:i') ?></small>
            </td>
            <td><?= if_empty(format_date($deliveryTrackingDetail['arrival_date'], 'd M Y H:i'), '-') ?></td>
            <td><?= if_empty(format_date($deliveryTrackingDetail['unload_date'], 'd M Y H:i'), '-') ?></td>
            <td><?= if_empty($deliveryTrackingDetail['unload_location'], '-') ?></td>
            <td style="padding-top: 20px">
                <?php if (empty($deliveryTrackingDetail['attachment'])): ?>
                    -
                <?php else: ?>
                    <a href="<?= base_url('uploads/' . $deliveryTrackingDetail['attachment']) ?>" target="_blank">
                        <img src="<?= FCPATH . 'uploads/' . $deliveryTrackingDetail['attachment'] ?>" alt="Attachment" style="width: 100px">
                    </a>
                <?php endif; ?>
            </td>
            <td><?= $deliveryTrackingDetail['is_sent'] ? 'YES' : 'WAITING' ?></td>
            <td><?= format_date($deliveryTrackingDetail['created_at'], 'd M Y H:i') ?></td>
            <td><?= $deliveryTrackingDetail['creator_name'] ?></td>
        </tr>
        <?php if (!empty($deliveryTrackingDetail['goods'])): ?>
            <tr>
                <td></td>
                <td colspan="10">
                    <table class="table">
                        <thead>
                        <tr>
                            <th style="width: 30px" class="text-center">No</th>
                            <th style="width: 100px">No Safe Conduct</th>
                            <th>Item</th>
                            <th style="width: 100px">Quantity</th>
                            <th>Description</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php foreach ($deliveryTrackingDetail['goods'] as $innerIndex => $goods): ?>
                            <tr>
                                <td class="text-center"><?= $innerIndex + 1 ?></td>
                                <td><?= $goods['no_safe_conduct'] ?></td>
                                <td><?= $goods['goods_name'] ?></td>
                                <td><?= numerical($goods['quantity'], 2, true) ?></td>
                                <td><?= if_empty($goods['description'], '-') ?></td>
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

<br>
<br>

<h4 style="margin-bottom: 10px">Status Histories</h4>
<table class="table">
    <thead>
    <tr>
        <th class="text-center">No</th>
        <th>Status</th>
        <th>Description</th>
        <th>Created At</th>
        <th>Created By</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php foreach ($statusHistories as $status): ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td><?= $deliveryTracking['status'] ?></td>
            <td><?= if_empty($status['description'], '-') ?></td>
            <td><?= format_date($status['created_at'], 'd F Y H:i') ?></td>
            <td><?= $status['creator_name'] ?></td>
        </tr>
    <?php endforeach; ?>
    </tbody>
</table>
</body>
</html>

