<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Booking Type</h3>
    </div>
    <form class="form-horizontal form-view">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Booking Type Name</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $bookingType['booking_type'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Category</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $bookingType['category'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Customs Document Type</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $bookingType['type'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">DO By</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $bookingType['with_do'] ? 'INTERNAL' : 'EXTERNAL' ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty($bookingType['description'], 'No description') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($bookingType['created_at']) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($bookingType['updated_at']) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Extension Fields</label>
                <div class="col-sm-9">
                    <?php foreach ($extensionFields as $extensionField): ?>
                        <p class="form-control-static">
                            <?= $extensionField['field_name'] ?> (<?= $extensionField['type'] ?>)
                        </p>
                    <?php endforeach; ?>
                    <?php if(empty($extensionFields)): ?>
                        <p class="form-control-static">-</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Default Document</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= if_empty($bookingType['default_document'], '-') ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Dashboard Status</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $bookingType['dashboard_status'] == 1 ? "Visible" : "Not Visible" ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Related Documents</label>
                <div class="col-sm-9">
                    <?php foreach ($bookingDocuments as $bookingDocument): ?>
                        <p class="form-control-static">
                            <?= $bookingDocument['document_type'] ?>
                            <?php if($bookingDocument['is_required'] == 2): ?>
                                <?= '(booking required)' ?>
                            <?php elseif($bookingDocument['is_required'] == 1): ?>
                                <?= '(required)' ?>
                            <?php else: ?>
                                <?= '' ?>
                            <?php endif; ?>
                        </p>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
        </div>
    </form>
</div>