<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Booking Type</h3>
    </div>
    <form action="<?= site_url('booking_type/update/' . $bookingType['id']) ?>" class="form" method="post">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="form-group <?= form_error('booking_type') == '' ?: 'has-error'; ?>">
                <label for="booking_type">Booking Type</label>
                <input type="text" class="form-control" id="booking_type" name="booking_type"
                       placeholder="Enter booking type name"
                       value="<?= set_value('booking_type', $bookingType['booking_type']) ?>">
                <?= form_error('booking_type'); ?>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('category') == '' ?: 'has-error'; ?>">
                        <label for="inbound">Category</label>
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="inbound">
                                    <input type="radio" id="inbound" name="category"
                                           value="INBOUND" <?= $bookingType['category'] == 'INBOUND' ? 'checked' : ''; ?>>
                                    Inbound
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label for="outbound">
                                    <input type="radio" id="outbound" name="category"
                                           value="OUTBOUND" <?= $bookingType['category'] == 'OUTBOUND' ? 'checked' : ''; ?>>
                                    Outbound
                                </label>
                            </div>
                        </div>
                        <?= form_error('category'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('type') == '' ?: 'has-error'; ?>">
                        <label for="category-inbound">Customs Document Type</label>
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="type-inbound">
                                    <input type="radio" id="type-import" name="type" value="IMPORT"
                                        <?= set_radio('type', 'IMPORT', $bookingType['type'] == 'IMPORT') ?>>
                                    Import
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label for="type-export">
                                    <input type="radio" id="type-export" name="type" value="EXPORT"
                                        <?= set_radio('type', 'EXPORT', $bookingType['type'] == 'EXPORT') ?>>
                                    Export
                                </label>
                            </div>
                        </div>
                        <?= form_error('type'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('with_do') == '' ?: 'has-error'; ?>">
                        <label for="with-do">DO By</label>
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="with-do">
                                    <input type="radio" id="with-do" name="with_do" value="1"
                                        <?= set_radio('with_do', 1, $bookingType['with_do'] == 1) ?>> INTERNAL
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label for="non-do">
                                    <input type="radio" id="non-do" name="with_do" value="0"
                                        <?= set_radio('with_do', 0, $bookingType['with_do'] == 0) ?>> EXTERNAL
                                </label>
                            </div>
                        </div>
                        <?= form_error('with_do'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('dashboard_status') == '' ?: 'has-error'; ?>">
                        <label for="with-do">Dashboard Status</label>
                        <div class="row">
                            <div class="col-sm-6">
                                <label for="visible">
                                    <input type="radio" id="visible" name="dashboard_status" value="1" <?= set_radio('dashboard_status', 1, $bookingType['dashboard_status'] == 1) ?>> Visible
                                </label>
                            </div>
                            <div class="col-sm-6">
                                <label for="not-Visible">
                                    <input type="radio" id="not-Visible" name="dashboard_status" value="0" <?= set_radio('dashboard_status', 1, $bookingType['dashboard_status'] == 0) ?>> Not Visible
                                </label>
                            </div>
                        </div>
                        <?= form_error('dashboard_status'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description"
                          placeholder="Document type description"
                          maxlength="500"><?= set_value('description', $bookingType['description']) ?></textarea>
                <?= form_error('description'); ?>
            </div>
            <div class="form-group <?= form_error('extension_fields[]') == '' ?: 'has-error'; ?>">
                <label for="document_type">Extension fields</label>
                <div class="row">
                    <?php foreach ($extensionFields as $extensionField): ?>
                        <?php
                        $isExtensionChecked = false;
                        foreach ($bookingExtensions as $bookingExtension) {
                            if ($bookingExtension['id'] == $extensionField['id']) {
                                $isExtensionChecked = true;
                                break;
                            }
                        }
                        ?>
                        <div class="col-sm-4">
                            <div class="checkbox icheck" style="margin-top: 0">
                                <label>
                                    <input type="checkbox" name="extension_fields[]"
                                           id="extension_<?= $extensionField['id'] ?>"
                                           value="<?= $extensionField['id'] ?>"
                                        <?php echo set_checkbox('extension_fields', $extensionField['id'], $isExtensionChecked); ?>>
                                    &nbsp; <?= $extensionField['field_title'] ?> (<?= $extensionField['type'] ?>)
                                </label>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?= form_error('extension_fields[]'); ?>
            </div>
            <div class="form-group <?= form_error('document_type') == '' ?: 'has-error'; ?>">
                <label for="document_type">Main Document Type</label>
                <select class="form-control select2" name="document_type" id="document_type"
                        data-placeholder="Select main booking document" required>
                    <option value=""></option>
                    <?php foreach ($documentTypes as $documentType): ?>
                        <option value="<?= $documentType['id'] ?>" <?= set_select('document_type', $documentType['id'], $documentType['id'] == $bookingType['id_document_type']) ?>>
                            <?= $documentType['document_type'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('document_type'); ?>
            </div>
            <div class="form-group <?= form_error('document_types') == '' ?: 'has-error'; ?>">
                <label for="document_types">Document Types</label>
                <div class="row">
                    <?php foreach ($documentTypes as $documentType): ?>
                        <?php
                        $hasRole = false;
                        $isReq = -1;
                        foreach ($bookingDocuments as $bookingDocument) {
                            if ($documentType['id'] == $bookingDocument['id']) {
                                $hasRole = true;
                                $isReq = $bookingDocument['is_required'];
                                break;
                            }
                        }
                        ?>
                        <div class="col-sm-4" style="margin-bottom:20px">
                            <div class="row">
                                <div class="col-xs-6 col-md-12">
                                    <div class="checkbox icheck" style="margin-top: 0">
                                        <label>
                                            <input type="checkbox" name="document_types[]"
                                                   id="document_type_<?= $documentType['id'] ?>"
                                                   value="<?= $documentType['id'] ?>"
                                                <?php echo set_checkbox('document_types', $documentType['id'], $hasRole); ?>>
                                            &nbsp; <?= $documentType['document_type'] ?>
                                        </label>
                                    </div>
                                </div>
                                <div class="col-xs-6 col-md-12">
                                    <div class="form-group <?= form_error('requirement_' . $documentType['id']) == '' ?: 'has-error'; ?>">
                                        <select class="form-control select2" data-placeholder="Is required"
                                                name="requirement_<?= $documentType['id'] ?>"
                                                id="requirement_<?= $documentType['id'] ?>"
                                                data-placeholder="Select requirement">
                                            <option value=""></option>
                                            <option value="0" <?= set_select('requirement_' . $documentType['id'], 0, $isReq == 0) ?>>
                                                Not Required
                                            </option>
                                            <option value="1" <?= set_select('requirement_' . $documentType['id'], 1, $isReq == 1) ?>>
                                                Required
                                            </option>
                                            <option value="2" <?= set_select('requirement_' . $documentType['id'], 2, $isReq == 2) ?>>
                                                Booking Required
                                            </option>
                                            <option value="3" <?= set_select('requirement_' . $documentType['id'], 3, $isReq == 3) ?>>
                                                Inbound Required
                                            </option>
                                        </select>
                                        <?= form_error('requirement_' . $documentType['id']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <?= form_error('document_types[]'); ?>
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

<script src="<?= base_url('assets/app/js/booking_type.js') ?>" defer></script>