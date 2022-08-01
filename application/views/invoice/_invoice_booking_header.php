<div class="form-view mb20">
    <div class="row">
        <div class="col-md-4">
            <div class="form-group mb0">
                <label for="customer">Customer</label>
                <p class="form-control-static pt0"><?= $customer['name'] ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb0">
                <label for="customer">Address</label>
                <p class="form-control-static pt0"><?= if_empty($customer['address'], '-') ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb0">
                <label for="customer">Tax Number</label>
                <p class="form-control-static pt0"><?= if_empty($customer['tax_number'], '-') ?></p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group mb0">
                <label for="customer">No DO / BL</label>
                <p class="form-control-static pt0">
                    <?php $extensionFound = false; ?>
                    <?php foreach ($bookingExtensions as $bookingExtension): ?>
                        <?php if($bookingExtension['field_name'] == 'NO_BL'): ?>
                            <?= if_empty($bookingExtension['value'], '-') ?>
                            <?php $extensionFound = true; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if(!$extensionFound): ?>
                        -
                    <?php endif; ?>
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb0">
                <label for="customer">No Reference</label>
                <p class="form-control-static pt0">
                    <?= $booking['no_booking'] ?> (<?= if_empty($booking['no_reference'], 'No reference') ?>)
                </p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb0">
                <label for="customer">ETA</label>
                <p class="form-control-static pt0">
                    <?php $extensionFound = false; ?>
                    <?php foreach ($bookingExtensions as $bookingExtension): ?>
                        <?php if($bookingExtension['field_name'] == 'ETA'): ?>
                            <?= readable_date($bookingExtension['value']) ?>
                            <?php $extensionFound = true; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if(!$extensionFound): ?>
                        -
                    <?php endif; ?>
                </p>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <div class="form-group mb0">
                <label for="customer">Inbound Date</label>
                <p class="form-control-static pt0"><?= readable_date($booking['inbound_date'], false) ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb0">
                <label for="customer">Outbound Date</label>
                <p class="form-control-static pt0"><?= readable_date($booking['outbound_date'], false) ?></p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group mb0">
                <label for="customer">Created At</label>
                <p class="form-control-static pt0"><?= readable_date('now', false) ?></p>
            </div>
        </div>
    </div>
</div>