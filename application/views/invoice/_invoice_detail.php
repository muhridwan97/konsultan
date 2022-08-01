<table class="table table-bordered table-striped no-datatable">
    <thead>
    <tr>
        <th rowspan="2" class="text-center">No</th>
        <th rowspan="2">Item Name</th>
        <th colspan="5" class="text-center">Specification</th>
        <th rowspan="2" class="text-center no-wrap">Total</th>
    </tr>
    <tr>
        <th>Unit</th>
        <th>Type</th>
        <th>Quantity</th>
        <th>Unit Price</th>
        <th>Multiplier</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php $totalPrice = 0 ?>
    <?php $lastType = '' ?>
    <?php foreach ($invoiceDetails as $invoiceDetail): ?>

        <?php if ($invoiceDetail['type'] != $lastType): ?>
            <?php if (!empty($lastType) && $lastType != 'INVOICE' && $lastType != 'OTHER'): ?>
                <tr>
                    <th colspan="7">Sub total with <?= $invoiceDetails[$no-2]['type'] ?></th>
                    <th class="text-right no-wrap">Rp. <?= numerical($totalPrice, 0) ?></th>
                </tr>
            <?php endif; ?>
            <?php $lastType = $invoiceDetail['type']; ?>
        <?php endif; ?>

        <?php $totalPrice += $invoiceDetail['total']; ?>

        <?php if ($invoiceDetail['type'] == 'INVOICE' || $invoiceDetail['type'] == 'OTHER'): ?>
            <tr>
                <td colspan="7"><?= $invoiceDetail['item_name'] ?></td>
                <td class="text-right no-wrap <?= $invoiceDetail['total'] < 0 ? 'text-danger' : '' ?>">
                    Rp. <?= numerical($invoiceDetail['total'], 0) ?>
                </td>
            </tr>
        <?php else: ?>
            <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td>
                    <?= str_replace(',', ', ', $invoiceDetail['item_name']) ?><br>
                    <small class="text-muted">
                        <?= str_replace(',', ', ', $invoiceDetail['description']) ?>
                    </small>
                </td>
                <td><?= $invoiceDetail['unit'] ?></td>
                <td><?= $invoiceDetail['type'] ?></td>
                <td><?= numerical($invoiceDetail['quantity'], 3, true) ?></td>
                <td class="text-right">Rp. <?= numerical($invoiceDetail['unit_price'], 0) ?></td>
                <td><?= numerical($invoiceDetail['unit_multiplier'], 3, true) ?></td>
                <td class="text-right no-wrap <?= $invoiceDetail['total'] < 0 ? 'text-danger' : '' ?>">
                    Rp. <?= numerical($invoiceDetail['total'], 0) ?>
                </td>
            </tr>
        <?php endif ?>
    <?php endforeach ?>

    <tr>
        <td colspan="7"><strong>Total Price</strong></td>
        <td class="text-right no-wrap"><strong>Rp. <?= numerical($totalPrice, 0) ?></strong></td>
    </tr>
    </tbody>
</table>