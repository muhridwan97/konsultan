<p class="lead text-center" style="font-weight: normal">RECEIPT <?= $invoice['type'] ?></p>

<div class="row mb10">
    <div class="col-md-3">
        <p class="mb0">Received From:</p>
    </div>
    <div class="col-md-9">
        <p class="mb0"><strong><?= $customer['name'] ?></strong></p>
    </div>
</div>
<div class="row mb10">
    <div class="col-md-3">
        <p class="mb0">Amount:</p>
    </div>
    <div class="col-md-9">
        <p class="mb0 lead"><strong>Rp. <?= numerical($invoice['total_price'], 0, true) ?></strong></p>
        <span class="text-muted">(<?= number_spelled($invoice['total_price']) ?>)</span>
    </div>
</div>
<div class="row mb10">
    <div class="col-md-3">
        <p class="mb0">For Payment Of:</p>
    </div>
    <div class="col-md-9">
        <p class="mb0">
            Payment for
            <?php
            $itemNames = [];
            $totalNonInvoiceType = 0;
            foreach ($invoiceDetails as $invoiceDetail) {
                if($invoiceDetail['type'] != 'INVOICE' && $invoiceDetail['type'] != 'OTHER') {
                    $totalNonInvoiceType += $invoiceDetail['total'];
                    $itemData = explode(',', $invoiceDetail['item_name']);
                    $itemNames = array_merge($itemNames, $itemData);
                }
            }
            $items = array_unique($itemNames);
            echo $invoice['type'] . ' '.implode(', ', $items) . ' Rp. ' . numerical($totalNonInvoiceType, 0, true);
            ?>
            <br>
            <?php foreach ($invoiceDetails as $detailInvoice): ?>
                <?php if($detailInvoice['type'] == 'INVOICE' || $detailInvoice['type'] == 'OTHER'): ?>
                    <?= $detailInvoice['item_name'] ?> Rp. <?= numerical($detailInvoice['total'], 0, true) ?><br>
                <?php endif; ?>
            <?php endforeach; ?>
            <span class="text-muted"><?= $invoice['item_summary'] ?></span><br>
        </p>
        <span class="text-muted">Note: <?= if_empty($invoice['description'], 'No description') ?></span>
    </div>
</div>

<hr>

<?php if($invoice['type'] == 'BOOKING FULL'): ?>

<?php endif; ?>

<div class="row" style="margin-bottom: 20px">
    <div class="col-xs-6">
        <div style="border: 1px solid #333333; padding: 10px; max-width: 420px" class="mt10">
            In the case of overpayment may be notified before the 6<sup>th</sup> date of the following month after the invoice is published.
            Through that limit, overpayment is considered to be forfeited / non-refundable.
        </div>
    </div>
    <div class="col-xs-6 text-center">
        <p class="mb0"><strong>Jakarta, <?= readable_date($invoice['created_at'], false) ?></strong></p>
        <p><strong>Finance</strong></p>
        <br><br>
        ( <?= get_setting('admin_finance') ?> )
    </div>
</div>