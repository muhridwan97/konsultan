<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title><?= $this->config->item('app_name') ?> | Storage Over Space</title>
    <style>
        @page { margin: 20px; }

        * {
            margin: 0;
            padding: 0;
        }

        body { font-size: 10px; font-family: sans-serif; margin: 20px; }

        p {
            line-height: 1.4;
        }

        table td, table th {
            padding: 5px 3px;
        }

        .table {
            border: 1px solid #777;
        }
        .table td, .table th {
            border-top: 1px solid #777;
            border-bottom: 1px solid #777;
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
        .table-error,
        .table-error td {
            background-color: #ffb3b2 !important;
        }
        .table-success {
            background-color: #daf9b8;
        }
        .table-secondary {
            background-color: #f8f8f8;
        }
        .label {
            display: inline-block;
            padding: .2em .5em;
            line-height: 1;
            color: #fff;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: .25em;
            font-size: 0.8em;
        }
        .label-danger {
            background-color: #dd4b39 !important;
        }
        .label-success {
            background-color: #00a65a !important;
        }
        .label-default {
            background-color: #d2d6de !important;
            color: #444 !important;
        }
        .font-weight-bold {
            font-weight: bold;
        }
        .text-danger {
            color: #dd4b39;
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
<div style="margin-bottom: 20px; margin-left: 70px">
    <img src="<?= FCPATH . 'assets/app/img/layout/header-tci-iso-aeo.png' ?>" style="width: 550px">
</div>

<h4 style="margin-bottom: 10px; font-size: 12px">
    Over Space <span class="text-danger"><?= $storageOverSpace['type'] ?></span> branch <?= $storageOverSpace['branch'] ?>
    (<span class="text-danger"><?= format_date($storageOverSpace['date_from'], 'd F Y') ?> - <?= format_date($storageOverSpace['date_to'], 'd F Y') ?></span>)
</h4>
<table class="table" style="margin-bottom: 10px">
    <thead class="table-success">
    <tr>
        <th rowspan="2" style="width: 30px" class="text-center">No</th>
        <th rowspan="2">Customer Name</th>
        <th colspan="3" class="text-center">Warehouse (M<sup>2</sup>)</th>
        <th colspan="3" class="text-center">Yard (M<sup>2</sup>)</th>
        <th colspan="3" class="text-center">Covered Yard (M<sup>2</sup>)</th>
        <th rowspan="2" class="text-center" style="width: 50px">Status</th>
    </tr>
    <tr class="text-center">
        <th style="background-color: #c5f594">Leased</th>
        <th class="table-danger">Usage</th>
        <th>Balance</th>
        <th style="background-color: #c5f594">Leased</th>
        <th class="table-danger">Usage</th>
        <th>Balance</th>
        <th style="background-color: #c5f594">Leased</th>
        <th class="table-danger">Usage</th>
        <th>Balance</th>
    </tr>
    </thead>
    <tbody>
    <?php $balanceWarehouseTotal = 0; $balanceYardTotal = 0; $balanceCoveredYardTotal = 0 ?>
    <?php foreach ($storageOverSpaceCustomers as $index => $customer): ?>
        <tr>
            <td class="text-center"><?= $index + 1 ?></td>
            <td><?= $customer['customer_name'] ?></td>
            <td class="text-center"><?= numerical($customer['last_storages']['warehouse_capacity'], 2, true) ?></td>
            <td class="text-center table-danger"><?= numerical($customer['last_storages']['warehouse_usage'], 2, true) ?></td>
            <td class="text-center"><?= numerical($balanceWarehouse = $customer['last_storages']['warehouse_capacity'] - $customer['last_storages']['warehouse_usage'], 2, true) ?></td>
            <td class="text-center"><?= numerical($customer['last_storages']['yard_capacity'], 2, true) ?></td>
            <td class="text-center table-danger"><?= numerical($customer['last_storages']['yard_usage'], 2, true) ?></td>
            <td class="text-center"><?= numerical($balanceYard = $customer['last_storages']['yard_capacity'] - $customer['last_storages']['yard_usage'], 2, true) ?></td>
            <td class="text-center"><?= numerical($customer['last_storages']['covered_yard_capacity'], 2, true) ?></td>
            <td class="text-center table-danger"><?= numerical($customer['last_storages']['covered_yard_usage'], 2, true) ?></td>
            <td class="text-center"><?= numerical($balanceCoveredYard = $customer['last_storages']['covered_yard_capacity'] - $customer['last_storages']['covered_yard_usage'], 2, true) ?></td>
            <td class="text-center" style="text-align: center">
                <span class="label label-<?= $customer['status'] == 'VALIDATED' ? 'success' : ($customer['status'] == 'SKIPPED' ? 'danger' : 'default') ?>">
                    <?= $customer['status'] ?>
                </span>
            </td>
            <?php
                $balanceWarehouseTotal += $balanceWarehouse;
                $balanceYardTotal += $balanceYard;
                $balanceCoveredYardTotal += $balanceCoveredYard;
            ?>
        </tr>
        <?php if (empty($storageOverSpaceCustomers)): ?>
            <tr>
                <td colspan="12">No over space available</td>
            </tr>
        <?php endif; ?>
    <?php endforeach; ?>
    <tr class="table-warning font-weight-bold">
        <td></td>
        <td>Balance</td>
        <td colspan="2"></td>
        <td class="text-center"><?= $balanceWarehouseTotal ?></td>
        <td colspan="2"></td>
        <td class="text-center"><?= $balanceYardTotal ?></td>
        <td colspan="2"></td>
        <td class="text-center"><?= $balanceCoveredYardTotal ?></td>
        <td></td>
    </tr>
    <tr class="table-warning font-weight-bold">
        <td></td>
        <td colspan="10">Validation (%)</td>
        <td class="text-center"><?= numerical($storageOverSpace['total_validated'] / (empty($storageOverSpaceCustomers) ? 1 : count($storageOverSpaceCustomers)) * 100, 1, true) ?>%</td>
    </tr>
    </tbody>
</table>
<p>
    Note: this information is data as of
    <strong><?= format_date($storageOverSpace['date_to'], 'd F Y') ?></strong>
</p>
</body>
</html>

