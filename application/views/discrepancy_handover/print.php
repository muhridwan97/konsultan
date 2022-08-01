<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset='utf-8'>
    <title><?= $discrepancyHandover['no_discrepancy'] ?></title>
    <link rel="stylesheet" href="https://fonts.googleapis.com/css?family=Roboto:300,400,500,700,900">
    <style>
        @page {
            margin: 20px 20px 20px 20px;
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
            <small>Print Date: <strong><?= date('d F Y H:i') ?></strong></small>
        </td>
    </tr>
</table>
<hr style="margin-top: 5px">
<table>
    <tr>
        <td style="width: 250px">
            <h3 style="margin-bottom: 0; margin-top: 0">DISCREPANCY HANDOVER</h3>
            <p style="margin-bottom: 5px">
                No Handover: <strong><?= $discrepancyHandover['no_discrepancy'] ?></strong>
            </p>
        </td>
        <td>
            Total Items: <strong><?= count($discrepancyHandoverGoods) ?> items</strong><br>
        </td>
        <td>
            Created At: <strong><?= $discrepancyHandover['created_at'] ?></strong><br>
        </td>
    </tr>
</table>

<?php if (!empty($discrepancyHandoverGoods)): ?>
    <table class="table table-bordered" style="margin-bottom: 15px">
        <thead>
        <tr>
            <th style="width: 25px" class="text-center">No</th>
            <th>Source</th>
            <th>Type</th>
            <th>Goods</th>
            <th>Unit</th>
            <th>Qty Booking</th>
            <th>Qty Stock</th>
            <th>Qty Diff</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($discrepancyHandoverGoods as $index => $discrepancyHandoverItem): ?>
            <tr>
                <td><?= $index + 1 ?></td>
                <td><?= $discrepancyHandoverItem['source'] ?></td>
                <td><?= $discrepancyHandoverItem['assembly_type'] ?></td>
                <td>
                    <?= $discrepancyHandoverItem['goods_name'] ?><br>
                    <small class="text-muted"><?= $discrepancyHandoverItem['no_goods'] ?></small>
                </td>
                <td><?= $discrepancyHandoverItem['unit'] ?></td>
                <td><?= numerical($discrepancyHandoverItem['quantity_booking'], 2, true) ?></td>
                <td><?= numerical($discrepancyHandoverItem['quantity_stock'], 2, true) ?></td>
                <td><?= numerical($discrepancyHandoverItem['quantity_difference'], 2, true) ?></td>
            </tr>
        <?php endforeach; ?>
        <?php if(empty($discrepancyHandoverGoods)): ?>
            <tr>
                <td colspan="8">No discrepancy goods available</td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>

<hr>

<table style="margin-top: 20px">
    <tr>
        <td class="text-center">
            <p><strong>Customer</strong></p>
            <br><br><br><br><br>
            ( . . . . . . . . . . . . . . . . . . . )
        </td>
        <td class="text-center">
            <p><strong>Internal</strong></p>
            <br><br><br><br><br>
            ( . . . . . . . . . . . . . . . . . . . )
        </td>
    </tr>
</table>

</body>
</html>
