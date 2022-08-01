<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Document Type</h3>
    </div>
    <form action="<?= site_url('document_type/update/' . $documentType['id']) ?>" class="form" method="post">
        <div class="box-body">

            <?php if ($this->session->flashdata('status') != NULL): ?>
                <div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close"><span
                                aria-hidden="true">&times;</span></button>
                    <p><?= $this->session->flashdata('message'); ?></p>
                </div>
            <?php endif; ?>

            <?php if($lastNameDocType == 'Draft' || $lastNameDocType == 'Confirmation'): ?>
                <div class="form-group <?= form_error('document_type') == '' ?: 'has-error'; ?>">
                    <label for="document_type">Document Type</label>
                    <input type="text" class="form-control" id="document_type" name="document_type" readonly
                           placeholder="Enter document type name"
                           value="<?= set_value('document_type', $documentType['document_type']) ?>">
                    <?= form_error('document_type', '<span class="help-block">', '</span>'); ?>
                </div>
            <?php else: ?>
                <div class="form-group <?= form_error('document_type') == '' ?: 'has-error'; ?>">
                    <label for="document_type">Document Type</label>
                    <input type="text" class="form-control" id="document_type" name="document_type" 
                           placeholder="Enter document type name"
                           value="<?= set_value('document_type', $documentType['document_type']) ?>">
                    <?= form_error('document_type', '<span class="help-block">', '</span>'); ?>
                </div>
            <?php endif; ?>

            <div class="form-group <?= form_error('directory') == '' ?: 'has-error'; ?>">
                <label for="directory">Directory</label>
                <input type="text" class="form-control" id="directory" name="directory" placeholder="Enter document type directory"
                       value="<?= set_value('directory', $documentType['directory']) ?>">
                <?= form_error('directory', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('visibility') == '' ?: 'has-error'; ?>">
                <label for="visibility">Visibility</label>
                <div class="row">
                    <div class="col-sm-3">
                        <input type="radio" name="visibility" value="0"
                            <?= set_checkbox('visibility', 0, $documentType['is_visible'] == 0) ?>> Not Visible
                    </div>
                    <div class="col-sm-3">
                        <input type="radio" name="visibility" value="1"
                            <?= set_checkbox('visibility', 1, $documentType['is_visible'] == 1) ?>> Visible
                    </div>
                </div>
                <?= form_error('visibility', '<span class="help-block">', '</span>'); ?>
                <span class="help-block">Choose visible when you want to show this document at delivery order. Default : Not Visible</span>
            </div>

            <?php if($lastNameDocType == 'Draft' || $lastNameDocType == 'Confirmation'): ?>
                <div class="form-group <?= form_error('confirmation') == '' ?: 'has-error'; ?>">
                    <label for="confirmation">Confirmation</label>
                    <div class="row">
                        <div class="col-sm-3">
                            <input type="radio" name="confirmation" value="0" readonly
                                <?= set_checkbox('confirmation', 0, $documentType['is_confirm'] == 0) ?>> Not Confirm
                        </div>
                        <div class="col-sm-3">
                            <input type="radio" name="confirmation" value="1" readonly
                                <?= set_checkbox('confirmation', 1, $documentType['is_confirm'] == 1) ?>> Confirm
                        </div>
                    </div>
                    <?= form_error('confirmation', '<span class="help-block">', '</span>'); ?>
                    <span class="help-block">Choose confirmation when you want to send this document at give response. Default : Not Confirm</span>
                </div>
            <?php else: ?>
                <div class="form-group <?= form_error('confirmation') == '' ?: 'has-error'; ?>">
                    <label for="confirmation">Confirmation</label>
                    <div class="row">
                        <div class="col-sm-3">
                            <input type="radio" name="confirmation" value="0" 
                                <?= set_checkbox('confirmation', 0, $documentType['is_confirm'] == 0) ?>> Not Confirm
                        </div>
                        <div class="col-sm-3">
                            <input type="radio" name="confirmation" value="1"
                                <?= set_checkbox('confirmation', 1, $documentType['is_confirm'] == 1) ?>> Confirm
                        </div>
                    </div>
                    <?= form_error('confirmation', '<span class="help-block">', '</span>'); ?>
                    <span class="help-block">Choose confirm when you want to send this document at give response. Default : Not Confirm</span>
                </div>
            <?php endif; ?>
            <div class="form-group <?= form_error('reminder') == '' ?: 'has-error'; ?>">
                <label for="reminder">Reminder</label>
                <div class="row reminder">
                    <div class="col-sm-3">
                        <input type="radio" name="reminder" id="reminder" value="1"
                            <?= set_checkbox('reminder', 1, $documentType['is_reminder'] == 1) ?>> Yes
                    </div>
                    <div class="col-sm-3">
                        <input type="radio" name="reminder" value="0" 
                            <?= set_checkbox('reminder', 0, $documentType['is_reminder'] == 0) ?>> No
                    </div>
                </div>
                <?= form_error('reminder', '<span class="help-block">', '</span>'); ?>
                <span class="help-block">Choose yes when you want to send reminder this document. Default : No</span>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?> document-reminder">
                <div class="row" >
                     <div class="col-sm-3">
                    <label for="description">Reference of Document Reminder</label>
                    <select class="form-control select2" name="reminder_document" id="reminder_document"
                            data-placeholder="Select main reminding document" >
                        <option value=""></option>
                        <?php foreach ($documentTypes as $docType): ?>
                            <option value="<?= $docType['id'] ?>" <?= set_select('document_type', $docType['id'], $docType['id'] == $documentType['reminder_document']) ?>> 
                                <?= $docType['document_type'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    </div>
                     <div class="col-sm-3">
                    <label for="description">Reference of Document Upload</label>
                    <select class="form-control select2" name="upload_document" id="upload_document"
                            data-placeholder="Select main uploading document" >
                        <option value=""></option>
                        <?php foreach ($documentTypes as $docType): ?>
                            <option value="<?= $docType['id'] ?>" <?= set_select('document_type', $docType['id'], $docType['id'] == $documentType['upload_document']) ?>> 
                                <?= $docType['document_type'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    </div>
                </div>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('reminder_overdue_day') == '' ?: 'has-error'; ?> reminder-overdue">
                <label for="opname_day">Reminder Overdue Day</label>
                <input type="number" class="form-control" id="reminder_overdue_day" name="reminder_overdue_day" min="0" placeholder="Reminder Overdue Day" maxlength="500" value="<?= set_value('reminder_overdue_day',$documentType['reminder_overdue_day']) ?>">
                <?= form_error('reminder_overdue_day', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('is_expired') == '' ?: 'has-error'; ?> expired-reminder">
                <div class="row" >
                    <div class="col-sm-3">
                        <label for="is_expired">Is Expired</label>
                        <select class="form-control select2" name="is_expired" id="is_expired"
                                data-placeholder="Select is expired" >
                            <option value="1" <?= set_select('is_expired', '1', $documentType['is_expired'] == '1') ?>>YES</option>
                            <option value="0" <?= set_select('is_expired', '0', $documentType['is_expired'] == '0') ?>>NO</option>
                        </select>
                    </div>
                    <div class="col-sm-3 active-day-expired" style="display: none;">
                        <label for="active_day">Total Active Period</label>
                        <input type="number" class="form-control" name="active_day" id="active_day" min="0"
                                data-placeholder="Input active period" value="<?= set_value('active_day',$documentType['active_day']) ?>">
                    </div>
                </div>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('email_notification') == '' ?: 'has-error'; ?>">
                <label for="email_notification">Email Notification</label>
                <div class="row email_notification">
                    <div class="col-sm-3">
                        <input type="radio" name="email_notification" id="email_notification" value="1"
                            <?= set_checkbox('email_notification', 1, $documentType['is_email_notification'] == 1) ?>> Yes
                    </div>
                    <div class="col-sm-3">
                        <input type="radio" name="email_notification" value="0" 
                            <?= set_checkbox('email_notification', 0, $documentType['is_email_notification'] == 0) ?>> No
                    </div>
                </div>
                <?= form_error('email_notification', '<span class="help-block">', '</span>'); ?>
                <span class="help-block">Choose yes when you want to send email notification this document. Default : Yes</span>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Document type description"
                          maxlength="500"><?= set_value('description', $documentType['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-success pull-right">
                Update Document Type
            </button>
        </div>
    </form>
</div>
<script src="<?= base_url('assets/app/js/document_type.js?v=1') ?>" defer></script>