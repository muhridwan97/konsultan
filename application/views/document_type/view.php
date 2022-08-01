<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Document Type</h3>
    </div>
    <form role="form">
        <div class="box-body">
            <div class="form-group">
                <label class="col-sm-3">Document Type Name</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= $documentType['document_type'] ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Directory</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= $documentType['directory'] ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Visibility</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= $documentType['is_visible'] == 1 ? 'TRUE' : 'FALSE' ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Confirmation</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= $documentType['is_confirm'] == 1 ? 'TRUE' : 'FALSE' ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Reminder</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= $documentType['is_reminder'] == 1 ? 'TRUE' : 'FALSE' ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Email Notification</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= $documentType['is_email_notification'] == 1 ? 'TRUE' : 'FALSE' ?>
                    </p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-3">Document Reminder</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?php if ($documentType['reminder_document'] != 0): ?>
                        <?php foreach ($allDocumentTypes as $docType): ?>
                            <?php if($docType['id'] == $documentType['reminder_document']): ?>
                                <?= $docType['document_type'] ?>
                                    <?php break; ?>
                                <?php endif; ?>
                        <?php endforeach; ?>
                        <?php else: ?>
                            <?= '-' ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Document Upload</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?php if ($documentType['upload_document'] != 0): ?>
                            <?php foreach ($allDocumentTypes as $docType): ?>
                                <?php if($docType['id'] == $documentType['upload_document']): ?>
                                    <?= $docType['document_type'] ?>
                                        <?php break; ?>
                                    <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <?= '-' ?>
                        <?php endif; ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Reminder Expired</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= $documentType['is_expired'] == 1 ? 'TRUE' : 'FALSE' ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Reminder Active</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= if_empty($documentType['active_day'], '-')?> days
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Description</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= empty($documentType['description']) ? 'No description' : $documentType['description'] ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Created At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($documentType['created_at']) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3 control-label">Updated At</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= readable_date($documentType['updated_at']) ?>
                    </p>
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