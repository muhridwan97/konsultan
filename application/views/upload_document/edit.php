<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit <?= $document['document_type'] ?></h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form action="<?= site_url('upload_document/update/' . $document['id'] . (isset($_GET['redirect']) ? '?redirect=' . $_GET['redirect'] : '')) ?>" role="form" method="post" enctype="multipart/form-data" id="form-edit-upload-document">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <input type="hidden" name="id" id="id" value="<?= $document['id'] ?>">
            <input type="hidden" name="document_type" id="document_type" value="<?= $document['document_type'] ?>">

            <div class="row" style="margin-bottom: 10px">
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Document Type</label>
                        <p class="form-control-static">
                            <?= $document['document_type'] ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">No Document</label>
                        <input type="text" class="form-control" name="doc_no" required
                               value="<?= $document['no_document'] ?>">
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Document Date</label>
                        <input type="text" class="form-control datepicker" name="doc_date"
                               required
                               value="<?= (new DateTime($document['document_date']))->format('d F Y') ?>">
                    </div>
                </div>
            </div>
            <?php if(in_array($document['document_type'], ["BC 1.6 Draft", "BC 2.8 Draft", "BC 2.7 Draft"]) == true): ?>
            <div class="row total-item-wrapper" style="margin-bottom: 10px">
                <div class="col-md-4 total_item" style="display: none;">
                    <div class="form-group">
                        <label class="control-label">Total Item</label>
                        <input type="number" class="form-control" name="total_item" id="total_item" min='1' <?= in_array($document['document_type'], ["BC 1.6 Draft", "BC 2.8 Draft", "BC 2.7 Draft"]) == true ? 'required' : '' ?> placeholder="Enter Total Item" value="<?= !empty($document['total_item']) ? numerical($document['total_item'],3,true) : '' ?>">
                    </div>
                </div>
            </div>
            <?php endif; ?>
            <div class="row do-wrapper" style="margin-bottom: 10px">
                <div class="col-md-4 document_subtype" style="display: none;">
                    <div class="form-group">
                        <label class="control-label">Document Subtype</label>
                        <select class="form-control select2" name="document_subtype" <?= $document['document_type'] == DocumentTypeModel::DOC_DO ? 'required' : '' ?> id="document_subtype"
                                data-placeholder="Select document subtype" style="width: 100%" >
                            <option value=""></option>
                            <option value="SOC" <?= set_select('subtype', 'SOC', $document['subtype'] == 'SOC') ?>>
                                SOC
                            </option>
                            <option value="COC" <?= set_select('subtype', 'COC', $document['subtype'] == 'COC') ?>>
                                COC
                            </option> 
                            <option value="LCL" <?= set_select('subtype', 'LCL', $document['subtype'] == 'LCL') ?>>
                                LCL
                            </option>
                        </select>
                    </div>
                </div>
                <div class="col-md-4 expired_date" style="display: none;">
                    <div class="form-group">
                        <label class="control-label">Expired Date</label>
                        <input type="text" class="form-control datepicker" <?= $document['document_type'] == DocumentTypeModel::DOC_DO ? 'required' : '' ?> name="expired_date" id="expired_date" placeholder="Select Expired Date" value="<?= !empty($document['expired_date']) ? (new DateTime($document['expired_date']))->format('d F Y') : '' ?>">
                    </div>
                </div>
                <div class="col-md-4 freetime_date" style="display: none;">
                    <div class="form-group">
                        <label class="control-label">Freetime Date</label>
                        <input type="text" class="form-control datepicker" name="freetime_date" id="freetime_date" placeholder="Select Expired Date" <?= $document['document_type'] == DocumentTypeModel::DOC_DO ? 'required' : '' ?> value="<?= !empty($document['freetime_date']) ? (new DateTime($document['freetime_date']))->format('d F Y') : '' ?>">
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h4 class="box-title">Old Files</h4>
                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_CREATE)): ?>
                        <a href="<?= site_url("upload_document_file/create/" . $document['id']) ?>"
                           class="btn btn-primary btn-sm pull-right">
                            <i class="fa ion-plus-round"></i> Add <?= $document['document_type'] ?> Files
                        </a>
                    <?php endif; ?>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped no-datatable">
                        <thead>
                        <tr>
                            <th style="width: 20px">No</th>
                            <th>File</th>
                            <th>Description</th>
                            <th style="width: 80px">Action</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1;
                        foreach ($files as $file): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $file['source'] ?></td>
                                <td><?= empty($file['description']) ? 'No description' : $file['description'] ?></td>
                                <td>
                                    <a href="<?= asset_url($file['directory'] . '/' . $file['source']) ?>"
                                       class="btn btn-primary btn-sm" target="_blank">
                                        View File
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                        <?php if (count($files) <= 0): ?>
                            <tr>
                                <td colspan="3">No files available</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
                <div class="box-footer">
                    <i class="fa fa-info-circle"></i> &nbsp;
                    Consider add and delete specific file rather than replace all uploaded document,
                    <a href="<?= site_url('upload_document/view/' . $document['id']) ?>">check here</a>.
                </div>
            </div>

            <?php if( (isset($lastNameDocType) && $lastNameDocType != "Draft") && ($created_by['type'] != PeopleModel::$TYPE_CUSTOMER) ): ?>
            <div class="box box-danger">
                <div class="box-header">
                    <h4 class="box-title text-danger">Replace With File</h4>
                </div>
                <div class="box-body" id="form-upload-wrapper">
                    <div class="panel panel-danger">
                        <div class="panel-heading">
                            <?= $document['document_type'] ?> replacement
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="doc_file_<?= $document['id'] ?>">
                                    <?= $document['document_type'] ?> <span style="color:#a9a9a9">(Upload max 3 MB)</span>
                                </label>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="btn btn-danger btn-block fileinput-button">
                                            <span class="button-file">Select file</span>
                                            <input class="upload-document" id="doc_file_<?= $document['id'] ?>"
                                                   type="file"
                                                   name="doc_file_<?= $document['id'] ?>">
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
                    <div class="box-footer">
                        <i class="fa fa-info-circle"></i> &nbsp;
                        Leave it blank if you just edit Doc. number or date only
                    </div>
                </div>
            </div>
            <?php endif; ?>

        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">
                Update Document
            </button>
        </div>
    </form>
</div>
<!-- /.box -->

<script src="<?= base_url('assets/app/js/upload.js?v=11') ?>" defer></script>