<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Upload Response Document</h3>
    </div>
    <form action="<?= site_url('upload/save_response?redirect=' . ($_GET['redirect'] ?? '')) ?>" role="form" method="post"
          enctype="multipart/form-data" id="form-response">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <input type="hidden" value="<?= $upload['id'] ?>" name="id" id="id">

            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="description" class="control-label">No Upload</label>
                        <p class="form-control-static"><?= $upload['no_upload'] ?></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Upload Type</label>
                        <p class="form-control-static"><?= $upload['booking_type'] ?></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="description" class="control-label">Upload Title</label>
                        <p class="form-control-static"><?= $upload['description'] ?></p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="description" class="control-label">Uploader</label>
                        <p class="form-control-static">
                            <?= $upload['uploader_name'] ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('type') == '' ?: 'has-error'; ?>">
                <label for="type">Document Type</label>
                <select class="form-control select2" id="document_type" name="type" required
                        data-placeholder="Select document type" <?= empty(get_url_param('id_document_type')) ? '' : 'disabled' ?>>
                    <option value=""></option>
                    <?php foreach ($types as $type) : ?>
                        <option value="<?= $type['id'] ?>" <?php echo set_select('type', $type['id'], $type['id'] == get_url_param('id_document_type')); ?>>
                            <?= $type['document_type'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if(!empty(get_url_param('id_document_type'))): ?>
                    <input type="hidden" id="type" name="type" value="<?= get_url_param('id_document_type') ?>">
                <?php endif ?>
                <input type="hidden" id="category" name="category" value="<?= $upload['category'] ?>">
                <?= form_error('type', '<span class="help-block">', '</span>'); ?>
            </div>

            <?php if($item_status == true): ?>
            <div class="form-group <?= form_error('total_item') == '' ?: 'has-error'; ?> total_item">
                <label for="total_item">Total Item</label>
                <input type="number" class="form-control" name="total_item" id="total_item" min='1' placeholder="Enter Total Item">
                <?= form_error('subtype', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('container_type') == '' ?: 'has-error'; ?> container_type">
                <label for="container_type">Type</label>
                <select class="form-control select2" id="container_type" name="container_type"
                        data-placeholder="Select document type">
                    <option value=""></option>
                    <option value="FCL" <?php echo set_select('container_type', "FCL"); ?>>
                        FCL
                    </option>
                    <option value="LCL" <?php echo set_select('container_type', "LCL"); ?>>
                        LCL
                    </option>
                </select>
                <?= form_error('container_type', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="row" id="lcl-type" style="display: none;">
                <div class="col-sm-3">
                    <div class="form-group <?= form_error('lcl_party[]') == '' ?: 'has-error'; ?>">
                        <label for="lcl_party">Party</label>
                        <input type="number" class="form-control" name="lcl_party" id="lcl_party" min='1' placeholder="Enter Total Party">
                        <?= form_error('lcl_party[]', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-sm-3">
                    <div class="form-group <?= form_error('lcl_shape') == '' ?: 'has-error'; ?>">
                        <label for="lcl_shape">Shape</label>
                        <input type="text" class="form-control" name="lcl_shape" id="lcl_shape" placeholder="Enter Shape" value="Package" readonly>
                        <?= form_error('lcl_shape', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div id="fcl-type" style="display: none;">
                <div class="row form-group row-fcl">
                    <div class="col-md-3">
                        <div class="form-group <?= form_error('parties[]') == '' ?: 'has-error'; ?>">
                            <label for="party">Party</label>
                            <input type="number" class="form-control" name="parties[]" id="party" min='1' placeholder="Enter Total Party">
                            <?= form_error('parties[]', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                    <div class="col-md-3">
                        <div class="form-group <?= form_error('shapes[]') == '' ?: 'has-error'; ?>">
                            <label for="shape">Shape</label>
                            <select class="form-control select2" id="shape" name="shapes[]" style="width:100%"
                                    data-placeholder="Select document type">
                                <option value=""></option>
                                <option value="20 Feet" <?php echo set_select('shapes[]', "20 Feet"); ?>>
                                    20 Feet
                                </option>
                                <option value="40 Feet" <?php echo set_select('shapes[]', "40 Feet"); ?>>
                                    40 Feet
                                </option>
                                <option value="45 Feet" <?php echo set_select('shapes[]', "45 Feet"); ?>>
                                    45 Feet
                                </option>
                            </select>
                            <?= form_error('shapes[]', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                </div>
                <div id="fcl-type-multi">
                </div>
                
                <div class="form-group">
                    <button type="button" class="btn btn-sm btn-primary" id="btn-add-fcl">ADD FCL</button>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-group <?= form_error('subtype') == '' ?: 'has-error'; ?> document_subtype">
                <label for="subtype">Subtype</label>
                <select class="form-control select2" id="document_subtype" name="subtype" data-placeholder="Select document subtype" <?= empty(get_url_param('subtype')) ? '' : 'disabled' ?>>
                    <option value=""></option>
                    <option value="<?= "SOC" ?>"> <?= "SOC" ?>
                    <option value="<?= "COC" ?>"> <?= "COC" ?>
                    <option value="<?= "LCL" ?>"> <?= "LCL" ?>
                </select>
                <?= form_error('subtype', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('freetime_date') == '' ?: 'has-error'; ?> freetime_date" style="display: none;">
                <label for="freetime_date" class="control-label">Freetime Date</label>
                <input type="text" class="form-control datepicker" name="freetime_date" id="freetime_date" placeholder="Freetime date of document type">
                <?= form_error('freetime_date', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('expired_date') == '' ?: 'has-error'; ?> expired_date" style="display: none;">
                <label for="expired_date" class="control-label">Expired Date</label>
                <input type="text" class="form-control datepicker" name="expired_date" id="expired_date" placeholder="Expired date of document type">
                <?= form_error('expired_date', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description" class="control-label">Response Description</label>
                <textarea class="form-control" id="description" name="description"
                          placeholder="Response description"
                          required maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="box box-success">
                <div class="box-header">
                    <h4 class="box-title text-success">Upload response</h4>
                </div>
                <div class="box-body" id="form-upload-wrapper">
                    <div class="panel panel-success">
                        <div class="panel-heading">
                            Response document
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="form-group">
                                            <label for="doc_no" class="control-label">
                                                Document Number
                                            </label>
                                            <input type="text" class="form-control"
                                                   placeholder="Number of document response"
                                                   id="doc_no" name="doc_no">
                                        </div>
                                    </div>
                                    <div class="col-md-3">
                                        <div class="form-group">
                                            <label for="doc_date" class="control-label">
                                                Document Date
                                            </label>
                                            <input type="text" class="form-control datepicker"
                                                   placeholder="Date of document response"
                                                   id="doc_date" name="doc_date" required>
                                        </div>
                                    </div>
                                    <div class="col-md-6">
                                        <div class="form-group">
                                            <label for="doc_file">
                                                Response file
                                            </label>
                                            <div class="row">
                                                <div class="col-sm-3">
                                                    <div class="btn btn-primary btn-block fileinput-button">
                                                        <span class="button-file">Select file</span>
                                                        <input class="upload-document" id="doc_file" type="file"
                                                               name="doc_file">
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
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="box-footer">
                        <i class="fa fa-info-circle"></i> &nbsp;
                        This response file and related information will be sent to customer
                    </div>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">
                Send Response
            </button>
        </div>
    </form>
</div>
<!-- /.box -->

<?php $this->load->view('upload/_fcl_type') ?>
<script src="<?= base_url('assets/app/js/upload.js?v=13') ?>" defer></script>