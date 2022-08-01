<p class="lead text-center" style="font-weight: normal">PAYMENT <?= $payment['payment_type'] ?></p>

<div class="row">
    <div class="col-xs-8">
        <div class="row mb10">
            <div class="col-xs-3">
                <p class="mb0">Branch:</p>
            </div>
            <div class="col-xs-9">
                <p class="mb0"><strong><?= $payment['branch'] ?></strong></p>
            </div>
        </div>
        <div class="row mb10">
            <div class="col-xs-3">
                <p class="mb0">No Payment:</p>
            </div>
            <div class="col-xs-9">
                <p class="mb0"><strong><?= $payment['no_payment'] ?></strong></p>
            </div>
        </div>
        <div class="row mb10">
            <div class="col-xs-3">
                <p class="mb0">Reference:</p>
            </div>
            <div class="col-xs-9">
                <p class="mb0"><strong><?= $payment['no_booking'] ?></strong> (<?= $payment['no_reference'] ?>)</p>
            </div>
        </div>
        <div class="row mb10">
            <div class="col-xs-3">
                <p class="mb0">Customer:</p>
            </div>
            <div class="col-xs-9">
                <p class="mb0"><strong><?= $payment['customer_name'] ?></strong></p>
            </div>
        </div>
        <div class="row mb10">
            <div class="col-xs-3">
                <p class="mb0">Requested:</p>
            </div>
            <div class="col-xs-9">
                <p class="mb0 lead" style="line-height: 1">
                    <strong>Rp. <?= numerical($payment['amount_request'], 0, true) ?></strong>
                </p>
            </div>
        </div>
        <div class="row mb10">
            <div class="col-xs-3">
                <p class="mb0">Realized:</p>
            </div>
            <div class="col-xs-9">
                <p class="mb0"><strong>Rp. <?= numerical($payment['amount'], 0, true) ?></strong></p>
            </div>
        </div>
        <div class="row mb10">
            <div class="col-xs-3">
                <p class="mb0">For Payment Of:</p>
            </div>
            <div class="col-xs-9">
                <p class="mb0">
                    <strong><?= $payment['payment_category'] ?> - <?= $payment['payment_type'] ?></strong><br>
                    <?= $payment['description'] ?>
                </p>
                <p class="mb0">No Invoice: <?= $payment['no_invoice'] ?></p>
            </div>
        </div>
        <div class="row mb10">
            <div class="col-xs-3">
                <p class="mb0">Status:</p>
            </div>
            <div class="col-xs-9">
                <p class="mb0"><strong><?= $payment['status'] ?></strong></p>
            </div>
        </div>
        <div class="row mb10">
            <div class="col-xs-3">
                <p class="mb0">Applicant:</p>
            </div>
            <div class="col-xs-9">
                <p class="mb0"><strong><?= ucfirst($payment['applicant_name']) ?></strong></p>
            </div>
        </div>
        <div class="row mb10">
            <div class="col-xs-3">
                <p class="mb0">Requested At:</p>
            </div>
            <div class="col-xs-9">
                <p class="mb0"><strong><?= readable_date($payment['created_at']) ?></strong></p>
            </div>
        </div>
        <div class="row mb10">
            <div class="col-xs-3">
                <p class="mb0">Payment Date:</p>
            </div>
            <div class="col-xs-9">
                <p class="mb0"><strong><?= readable_date($payment['payment_date']) ?></strong></p>
            </div>
        </div>
    </div>
    <div class="col-xs-4">
        <div class="text-center mb20" style="padding:10px 20px; max-width: 200px; border: 1px solid #cccccc">
            <p><strong>Applicant</strong></p>
            <br><br>
            ( <?= $payment['applicant_name'] ?> )
        </div>

        <div class="text-center mb20" style="padding:10px 20px; max-width: 200px; border: 1px solid #cccccc">
            <p><strong>Approved By</strong></p>
            <br><br>
            ( . . . . . . . . . . . . . . . . . . . . . . )
        </div>

        <div class="text-center mb20" style="padding:10px 20px; max-width: 200px; border: 1px solid #cccccc">
            <p><strong>Cashier</strong></p>
            <br><br>
            ( . . . . . . . . . . . . . . . . . . . . . . )
        </div>
    </div>


</div>