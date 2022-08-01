<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Report Activity Summary</title>
</head>

<body>
<?php foreach ($stockMutationContainers as $containers): ?>
    <?php $no = 1; ?>
    <?php $noContainer = key_exists(0, $containers) ? $containers[0]['no_container'] : '' ?>

    <table style="width: 100%" border="1">
        <thead>
        <tr>
            <th style="width: 25px">No</th>
            <th>Owner</th>
            <th>Reference</th>
            <th>Handling</th>
            <th>Date</th>
            <th>No Container</th>
            <th>Type</th>
            <th>Size</th>
            <th>Debit</th>
            <th>Credit</th>
            <th>Balance</th>
        </tr>
        </thead>
        <tbody>
        <?php $lastBalance = 0 ?>
        <?php foreach ($containers as $container): ?>
            <?php $lastBalance += $container['quantity'] ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= htmlentities($container['owner_name']) ?></td>
                <td class="excel-text"><?= htmlentities($container['no_reference']) ?></td>
                <td><?= $container['handling_type'] ?></td>
                <td><?= format_date($container['completed_at'], 'd F Y') ?></td>
                <td><?= $container['no_container'] ?></td>
                <td><?= if_empty($container['container_type'], '-') ?></td>
                <td><?= if_empty($container['container_size'], '-') ?></td>
                <td><?= if_empty(numerical($container['quantity_debit'], 3, true), '') ?></td>
                <td><?= if_empty(numerical($container['quantity_credit'], 3, true), '') ?></td>
                <td><?= $lastBalance ?></td>
            </tr>
        <?php endforeach ?>
        <tr>
            <td colspan="10"><strong>Total Stock</strong></td>
            <td><strong><?= $lastBalance ?></strong></td>
        </tr>
        </tbody>
    </table>
    <br><br>
<?php endforeach ?>
</body>
</html>
