<?php foreach ($documentTypes as $documentType): ?>
    <div class="panel panel-<?= $documentType['is_required'] == 1 ? 'primary' : 'default' ?><?= $documentType['is_required'] == 1 ? ' required-document' : ($documentType['document_type'] == DocumentTypeModel::DOC_DO ? ' do-required' : ' optional-document') ?>">
        <div class="panel-heading">
            <?= $documentType['document_type'] ?>
            <span class="pull-right"><?= $documentType['is_required'] == 1 ? 'Required' : 'Optional' ?></span>
        </div>
        <div class="panel-body">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="doc_no_<?= $documentType['id'] ?>" class="control-label">
                            Document Number
                        </label>
                        <input type="text" class="form-control doc_no"
                               placeholder="Number of <?= $documentType['document_type'] ?>"
                            <?= $documentType['is_required'] == 1 ? 'required' : '' ?>
                               id="doc_no_<?= $documentType['id'] ?>" name="doc_no_<?= $documentType['id'] ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="doc_date_<?= $documentType['id'] ?>" class="control-label">
                            Document Date
                        </label>
                        <input type="text" class="form-control datepicker doc_date"
                               placeholder="Date of <?= $documentType['document_type'] ?>"
                            <?= $documentType['is_required'] == 1 ? 'required' : '' ?>
                               id="doc_date_<?= $documentType['id'] ?>" name="doc_date_<?= $documentType['id'] ?>">
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="doc_file_<?= $documentType['id'] ?>">
                            <?= $documentType['document_type'] ?> <span style="color:#a9a9a9">(Upload max 3 MB)</span>
                        </label>
                        <div class="row">
                            <div class="col-sm-3">
                                <div class="btn btn-primary btn-block fileinput-button">
                                    <span class="button-file">Select file</span>
                                    <input class="upload-document" id="doc_file_<?= $documentType['id'] ?>" type="file" name="doc_file_<?= $documentType['id'] ?>">
                                </div>
                                <div class="upload-input-wrapper"></div>
                            </div>
                            <div class="col-sm-9">
                                <div id="progress" class="progress progress-upload">
                                    <div class="progress-bar progress-bar-success"></div>
                                </div>
                                <div class="uploaded-file"></div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php if($documentType['document_type'] == DocumentTypeModel::DOC_DO): ?>
                <div class="col-md-3">
                    <div class="form-group document_subtype">
                        <label for="subtype">Subtype</label>
                        <select class="form-control select2" id="document_subtype"  <?= $documentType['is_required'] == 1 ? 'required' : '' ?> name="doc_subtype_<?= $documentType['id'] ?>" placeholder="Select document subtype" <?= empty(get_url_param('subtype')) ? '' : 'disabled' ?>>
                            <option value="" selected>Select document subtype</option>
                            <option value="<?= "SOC" ?>"> <?= "SOC" ?>
                            <option value="<?= "COC" ?>"> <?= "COC" ?>
                            <option value="<?= "LCL" ?>"> <?= "LCL" ?>
                        </select>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group expired_date" style="display: none;">
                        <label for="expired_date" class="control-label">Expired Date</label>
                        <input type="text" class="form-control datepicker" name="doc_expired_date_<?= $documentType['id'] ?>" id="expired_date" placeholder="Expired date of document type">
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group freetime_date" style="display: none;">
                        <label for="freetime_date" class="control-label">Freetime Date</label>
                        <input type="text" class="form-control datepicker" name="doc_freetime_date_<?= $documentType['id'] ?>" id="freetime_date" placeholder="Freetime date of document type">
                    </div>
                </div>
                <?php endif ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
<script src="<?= base_url('assets/app/js/upload_form.js') ?>" defer></script>