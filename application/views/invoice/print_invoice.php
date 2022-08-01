<div class="row">
    <div class="col-sm-7 col-xs-6">
        <p class="lead" style="margin-bottom: 10px;">
            <strong>INVOICE <?= strtoupper($invoice['type']) ?></strong>
        </p>
        <p class="lead" style="margin-bottom: 0">
            Invoice No: <?= $invoice['no_invoice'] ?>
        </p>
        <p class="mb0">Reference No: <?= $invoice['no_reference'] ?></p>
    </div>
    <div class="col-sm-5 col-xs-6">
        <div class="pull-right">
            <div class="text-center" style="display: inline-block">
                <img src="data:image/png;base64,<?= $invoiceBarcode ?>" alt="<?= $invoice['no_invoice'] ?>">
            </div>
        </div>
    </div>
</div>

<hr>

<div class="row mb10">
    <div class="col-md-4">
        <p class="mb0">No Customer:</p>
        <strong><?= $customer['no_person'] ?></strong>
    </div>
    <div class="col-md-4">
        <p class="mb0">Customer:</p>
        <strong><?= $customer['name'] ?></strong>
    </div>
    <div class="col-md-4">
        <p class="mb0">Address:</p>
        <strong><?= if_empty($customer['address'], '-') ?></strong>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <p class="mb0">Contact:</p>
        <strong><?= if_empty($customer['contact'], '-') ?></strong>
    </div>
    <div class="col-md-4">
        <p class="mb0">Email:</p>
        <strong><?= if_empty($customer['email'], '-') ?></strong>
    </div>
    <div class="col-md-4">
        <p class="mb0">Tax Number:</p>
        <strong><?= empty($customer['tax_number']) ? '-' : mask_tax_number($customer['tax_number']) ?></strong>
    </div>
</div>

<hr>

<?php if($invoice['type'] == 'BOOKING FULL'): ?>
    <div class="row mb10">
        <div class="col-md-4">
            <p class="mb0">No BL/DO:</p>
            <strong>
                <?php $extensionFound = false; ?>
                <?php foreach ($bookingExtensions as $bookingExtension): ?>
                    <?php if($bookingExtension['field_name'] == 'NO_BL'): ?>
                        <?= $bookingExtension['value'] ?>
                        <?php $extensionFound = true; ?>
                    <?php endif ?>
                <?php endforeach ?>
                <?php if(!$extensionFound): ?>
                    -
                <?php endif ?>
            </strong>
        </div>
        <div class="col-md-4">
            <p class="mb0">Vessel/Voy:</p>
            <strong><?= if_empty($booking['vessel'], '-') ?> / <?= if_empty($booking['voyage'], '-') ?></strong>
        </div>
        <div class="col-md-4">
            <p class="mb0">BC Reference:</p>
            <strong><?= if_empty($booking['no_reference'], '-') ?></strong>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <p class="mb0">Arrival Date:</p>
            <strong>
                <?php $extensionFound = false; ?>
                <?php foreach ($bookingExtensions as $bookingExtension): ?>
                    <?php if($bookingExtension['field_name'] == 'ETA'): ?>
                        <?= $bookingExtension['value'] ?>
                        <?php $extensionFound = true; ?>
                    <?php endif ?>
                <?php endforeach ?>
                <?php if(!$extensionFound): ?>
                    -
                <?php endif ?>
            </strong>
        </div>
        <div class="col-md-4">
            <p class="mb0">Inbound Date:</p>
            <strong><?= readable_date($invoice['inbound_date'], false) ?></strong>
        </div>
        <div class="col-md-4">
            <p class="mb0">Outbound Date / Invoice Created:</p>
            <strong><?= readable_date($invoice['outbound_date'], false) ?></strong>
        </div>
    </div>
<?php endif; ?>

<div class="mt20">
    <?php $this->load->view('invoice/_invoice_detail') ?>
</div>

<div class="row" style="margin-top: 20px">
    <hr>
    <div class="col-xs-12 mb20">
        Invoice Note :
        <p class="mb0">
            <?= if_empty($invoice['item_summary'], 'No item summary') ?><br>
            <strong><?= if_empty($invoice['description'], 'No additional note') ?></strong>
        </p>
    </div>
    <div class="col-xs-12 col-sm-7">
        <div style="border: 1px solid #333333; padding: 10px; max-width: 420px">
            In case of mistake in billing data, please submit a correction within 3x24 hours (3 days).
            Through these limits will not be served.
        </div>
    </div>
    <div class="col-xs-12 col-sm-5">
        <p>
            Bank MANDIRI: Acc No. <strong>1200007010056</strong><br>
            Acc Name: <strong>PT Transcon Indonesia</strong>
        </p>
    </div>
</div>

<div class="row" style="margin-bottom: 20px">
    <hr>
    <div class="col-xs-6 text-center">
        <p class="mb0"><strong>Customer</strong></p>
        <p>&nbsp;</p>
        <br><br>
        ( <?= $customer['name'] ?> )
    </div>
    <div class="col-xs-6 text-center">
        <p class="mb0"><strong>Jakarta, <?= readable_date($invoice['created_at'], false) ?></strong></p>
        <p><strong>Finance</strong></p>
        <br><br>
        ( <?= get_setting('admin_finance') ?> )
    </div>
</div>

<hr>

<p class="mb0 text-center text-muted">-- Thanks for your business --</p>