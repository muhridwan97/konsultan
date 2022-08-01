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
        <h3 style="margin-bottom: 0">SERVICE HOUR</h3>
        <p style="margin-bottom: 10px; font-size: 14px">Service hour <?= $serviceHour['service_day'] ?></p>
    </div>
</div>

<hr>

<table style="margin-bottom: 10px; margin-top: 20px">
    <tr>
        <td>Service Day</td>
        <td class="font-weight-bold">: <?= $serviceHour['service_day'] ?></td>
        <td>Effective Date</td>
        <td class="font-weight-bold">: <?= $serviceHour['effective_date'] ?></td>
    </tr>
    <tr>
        <td>Start Hour</td>
        <td class="font-weight-bold">: <?= format_date($serviceHour['service_time_start'], 'H:i') ?></td>
        <td>End Hour</td>
        <td class="font-weight-bold">: <?= format_date($serviceHour['service_time_end'], 'H:i') ?></td>
    </tr>
    <tr>
        <td>Description</td>
        <td class="font-weight-bold">: <?= if_empty($serviceHour['description'], '-') ?></td>
        <td>Created At</td>
        <td class="font-weight-bold">: <?= $serviceHour['created_at'] ?></td>
    </tr>
</table>

<p class="font-weight-bold">Service Hour Histories</p><br>
<table class="table">
    <thead>
    <tr>
        <th class="text-center">No</th>
        <th>Effective Date</th>
        <th>Start</th>
        <th>End</th>
        <th>Created At</th>
    </tr>
    </thead>
    <tbody>
    <?php foreach ($relatedServiceHours as $index => $relatedServiceHour): ?>
        <tr>
            <td class="text-center"><?= $index + 1 ?></td>
            <td><?= $relatedServiceHour['effective_date'] ?></td>
            <td><?= format_date($relatedServiceHour['service_time_start'], 'H:i') ?></td>
            <td><?= format_date($relatedServiceHour['service_time_end'], 'H:i') ?></td>
            <td><?= format_date($relatedServiceHour['created_at'], 'd F Y H:i') ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if(empty($relatedServiceHours)): ?>
        <tr>
            <td colspan="5">No service hour history</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
</body>
</html>

