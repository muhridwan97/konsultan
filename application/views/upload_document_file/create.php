<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Add <?= $document['document_type'] ?></h3>
    </div>

    <form action="<?= site_url('upload_document_file/save') ?>" role="form" method="post" enctype="multipart/form-data">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <div class="row">
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
                        <p class="form-control-static">
                            <?= $document['no_document'] ?>
                        </p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label class="control-label">Document Date</label>
                        <p class="form-control-static">
                            <?= (new DateTime($document['document_date']))->format('d F Y') ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h4 class="box-title">Add File Upload</h4>
                </div>
                <div class="box-body" id="form-upload-wrapper">
                    <input type="hidden" name="id_upload_document" id="id_upload_document"
                           value="<?= $document['id'] ?>">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <?= $document['document_type'] ?>
                        </div>
                        <div class="panel-body">
                            <div class="form-group">
                                <label for="doc_file_<?= $document['id'] ?>">
                                    <?= $document['document_type'] ?> <span style="color:#a9a9a9">(Upload max 3 MB)</span>
                                </label>
                                <div class="row">
                                    <div class="col-sm-3">
                                        <div class="btn btn-primary btn-block fileinput-button">
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

                </div>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">
                Save Files
            </button>
        </div>
    </form>
</div>
<!-- /.box -->

<script src="<?= base_url('assets/app/js/upload.js?v=10') ?>" defer></script>