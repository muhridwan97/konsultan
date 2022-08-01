<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Report Activity Summary</title>
</head>

<body>
<?php foreach ($stockMutationGoods as $goods): ?>
    <?php $no = 1; ?>
    <?php $goodsName = key_exists(0, $goods) ? $goods[0]['goods_name'] : '' ?>
    <?php $noGoods = key_exists(0, $goods) ? $goods[0]['no_goods'] : '' ?>

    <table style="width: 100%" border="1">
        <thead>
        <tr>
            <th style="width: 25px">No</th>
            <th>Owner</th>
            <th>Reference</th>
            <th>Handling</th>
            <th>Date</th>
            <th>No Goods</th>
            <th>Goods Name</th>
            <th>Ex Container</th>
            <th>Qty Debit</th>
            <th>Qty Credit</th>
            <th>Qty Balance</th>
            <th>Weight Debit</th>
            <th>Weight Credit</th>
            <th>Weight Balance</th>
            <th>Gross Weight Debit</th>
            <th>Gross Weight Credit</th>
            <th>Gross Weight Balance</th>
            <th>Volume Debit</th>
            <th>Volume Credit</th>
            <th>Volume Balance</th>
        </tr>
        </thead>
        <tbody>
        <?php $lastBalance = 0;
        $lastBalanceWeight = 0;
        $lastBalanceGrossWeight = 0;
        $lastBalanceVolume = 0; ?>
        <?php foreach ($goods as $item): ?>
            <?php
            $lastBalance += $item['quantity'];
            $lastBalanceWeight += $item['total_weight'];
            $lastBalanceGrossWeight += $item['total_gross_weight'];
            $lastBalanceVolume += $item['total_volume'];
            ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlentities($item['owner_name']) ?></td>
                <td class="excel-text"><?= htmlentities($item['no_reference']) ?></td>
                <td><?= $item['handling_type'] ?></td>
                <td><?= format_date($item['completed_at'], 'd F Y') ?></td>
                <td><?= htmlentities($item['no_goods']) ?></td>
                <td><?= htmlentities($item['goods_name']) ?></td>
                <td><?= if_empty(htmlentities($item['ex_no_container']), '-') ?></td>
                <td class="text-right"><?= if_empty(numerical($item['quantity_debit'], 3, true), '') ?></td>
                <td class="text-right"><?= if_empty(numerical($item['quantity_credit'], 3, true), '') ?></td>
                <td class="text-right"><?= $lastBalance ?></td>
                <td class="text-right"><?= if_empty(numerical($item['weight_debit'], 3, true), '') ?></td>
                <td class="text-right"><?= if_empty(numerical($item['weight_credit'], 3, true), '') ?></td>
                <td class="text-right"><?= $lastBalanceWeight ?></td>
                <td class="text-right"><?= if_empty(numerical($item['gross_weight_debit'], 3, true), '') ?></td>
                <td class="text-right"><?= if_empty(numerical($item['gross_weight_credit'], 3, true), '') ?></td>
                <td class="text-right"><?= $lastBalanceGrossWeight ?></td>
                <td class="text-right"><?= if_empty(numerical($item['volume_debit'], 3, true), '') ?></td>
                <td class="text-right"><?= if_empty(numerical($item['volume_credit'], 3, true), '') ?></td>
                <td class="text-right"><?= $lastBalanceVolume ?></td>
            </tr>
        <?php endforeach; ?>
        <tr>
            <td colspan="8"><strong>Total Stock</strong></td>
            <td colspan="2"><strong>Quantity</strong></td>
            <td class="text-right"><strong><?= numerical($lastBalance, 3, true) ?></strong></td>
            <td colspan="2"><strong>Weight</strong></td>
            <td class="text-right"><strong><?= numerical($lastBalanceWeight, 3, true) ?></strong></td>
            <td colspan="2"><strong>Gross Weight</strong></td>
            <td class="text-right"><strong><?= numerical($lastBalanceGrossWeight, 3, true) ?></strong></td>
            <td colspan="2"><strong>Volume</strong></td>
            <td class="text-right"><strong><?= numerical($lastBalanceVolume, 3, true) ?></strong></td>
        </tr>
        </tbody>
    </table>
    <br><br>
<?php endforeach; ?>
</body>
</html>
