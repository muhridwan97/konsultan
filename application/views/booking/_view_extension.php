<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Additional Booking Information</h3>
    </div>
    <div class="box-body">
        <?php if (empty($bookingExtensions)): ?>
            <p class="text-muted">No extension field available</p>
        <?php endif; ?>
        <form role="form" class="form-horizontal form-view mb0">
            <div class="row">
                <?php foreach ($bookingExtensions as $bookingExtension): ?>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label class="col-sm-4"><?= $bookingExtension['field_title'] ?></label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?php if (in_array($bookingExtension['type'], ['CHECKBOX', '...', '...'])): ?>
                                        <?= implode(', ', json_decode($bookingExtension['value'])) ?>
                                    <?php else: ?>
                                        <?= if_empty($bookingExtension['value'], '-') ?>
                                    <?php endif; ?>
                                </p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </form>
    </div>
</div>