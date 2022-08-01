<table class="table table-bordered table-striped no-datatable" id="table-invoice-editor">
    <thead>
    <tr>
        <th rowspan="2" class="text-center">No</th>
        <th rowspan="2">Item Name</th>
        <th colspan="5" class="text-center">Specification</th>
        <th rowspan="2" class="text-center no-wrap">Total</th>
        <th rowspan="2" class="text-center" style="width: 30px">
            <button class="btn btn-sm btn-success btn-add-invoice-table" type="button"
                    data-authorized="<?= AuthorizationModel::isAuthorized(PERMISSION_INVOICE_EDIT) ? 'true' : 'false' ?>"
                    data-permission="<?= PERMISSION_INVOICE_EDIT ?>">
                <i class="fa ion-plus"></i>
            </button>
        </th>
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
    <?php foreach ($invoiceDetails as $invoiceDetail): ?>
        <?php $totalPrice += $invoiceDetail['total']; ?>

        <?php
        $rowClass = '';
        if ($invoiceDetail['item_name'] == 'PPN (10%)') {
            $rowClass = ' class="tax"';
        } else if ($invoiceDetail['item_name'] == 'Materai') {
            $rowClass = ' class="stamp"';
        }
        ?>
        <tr<?= $rowClass ?>>
            <td class="text-center"><?= $no++ ?></td>
            <td style="word-break: break-word;">
                <span class="label-item"><?= str_replace(',', ', ', $invoiceDetail['item_name']) ?></span><br>
                <span class="small text-muted label-description">
                    <?= str_replace(',', ', ', if_empty($invoiceDetail['description'], 'No description')) ?>
                </span><br>
                <span class="small text-muted label-item-summary">
                    <?= str_replace(',', ', ', if_empty(item_summary_modifier($invoiceDetail['item_summary']), 'No item summary')) ?>
                </span>

                <input type="hidden" id="item_name" name="invoice_details[item_name][]"
                       value="<?= $invoiceDetail['item_name'] ?>">
                <input type="hidden" id="description" name="invoice_details[description][]"
                       value="<?= $invoiceDetail['description'] ?>">
                <input type="hidden" id="item_summary" name="invoice_details[item_summary][]"
                       value="<?= key_exists('item_summary', $invoiceDetail) ? $invoiceDetail['item_summary'] : '' ?>">
            </td>
            <td>
                <span class="label-unit"><?= $invoiceDetail['unit'] ?></span>
                <input type="hidden" id="unit" name="invoice_details[unit][]" value="<?= $invoiceDetail['unit'] ?>">
            </td>
            <td>
                <span class="label-type"><?= $invoiceDetail['type'] ?></span>
                <input type="hidden" id="type" name="invoice_details[type][]" value="<?= $invoiceDetail['type'] ?>">
            </td>
            <td>
                <span class="label-quantity"><?= numerical($invoiceDetail['quantity'], 3, true) ?></span>
                <input type="hidden" id="quantity" name="invoice_details[quantity][]"
                       value="<?= numerical($invoiceDetail['quantity'], 3, true, '.', ',') ?>">
            </td>
            <td class="text-right">
                <span class="label-price"><?= numerical($invoiceDetail['unit_price'], 0, true) ?></span>
                <input type="hidden" id="unit_price" name="invoice_details[unit_price][]"
                       value="<?= numerical($invoiceDetail['unit_price'], 0, true, '.', '') ?>">
            </td>
            <td>
                <span class="label-multiplier"><?= numerical($invoiceDetail['unit_multiplier'], 3, true) ?></span>
                <input type="hidden" id="unit_multiplier" name="invoice_details[unit_multiplier][]"
                       value="<?= $invoiceDetail['unit_multiplier'] ?>">
            </td>
            <td class="text-right no-wrap">
                <span class="label-total">Rp. <?= numerical($invoiceDetail['total'], 0) ?></span>
            </td>
            <td>
                <?php if($invoiceDetail['type'] != 'OTHER'): ?>
                    <button class="btn btn-sm btn-primary btn-edit-invoice-table" type="button"
                            data-authorized="<?= AuthorizationModel::isAuthorized(PERMISSION_INVOICE_EDIT) ? 'true' : 'false' ?>"
                            data-permission="<?= PERMISSION_INVOICE_EDIT ?>">
                        <i class="fa ion-compose"></i>
                    </button>
                <?php endif ?>
            </td>
        </tr>

    <?php endforeach ?>

    <tr class="skip-ordering">
        <th colspan="7">Total Price</th>
        <th class="text-right no-wrap label-total-price">Rp. <?= numerical($totalPrice, 0) ?></th>
        <th></th>
    </tr>
    </tbody>
</table>