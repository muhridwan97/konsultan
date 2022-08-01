<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Add Document Upload</h3>
    </div>

    <form id="form-upload" action="<?= site_url('upload_document/save') ?>" role="form" method="post"
          enctype="multipart/form-data">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <input type="hidden" name="id" value="<?= $upload['id'] ?>">
            <div class="row">
                <div class="col-md-3">
                    <div class="form-group">
                        <label>No Upload</label>
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
                        <label>Upload Title</label>
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

            <div class="box box-primary">
                <div class="box-header">
                    <h4 class="box-title">Uploaded Documents</h4>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped no-datatable">
                        <thead>
                        <tr>
                            <th style="width: 20px">No</th>
                            <th>Type Document</th>
                            <th>No Document</th>
                            <th>Document Date</th>
                            <th>Description</th>
                            <th>Total Files</th>
                            <th>Validated</th>
                            <th>Type</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1;
                        foreach ($documents as $document): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $document['document_type'] ?></td>
                                <td><?= $document['no_document'] ?></td>
                                <td><?= (new DateTime($document['document_date']))->format('d F Y') ?></td>
                                <td><?= empty($document['description']) ? 'No description' : $document['description'] ?></td>
                                <td>
                                    <?php if ($document['total_file'] == 0): ?>
                                        No file available
                                    <?php else: ?>
                                        <a href="<?= site_url('upload_document/view/' . $document['id']) ?>">
                                            <?= number_format($document['total_file'], 0, ',', '.') ?>
                                            files
                                        </a>
                                    <?php endif; ?>
                                </td>
                                <td>
                                    <?php
                                    $statusLabel = [
                                        0 => 'label-warning',
                                        1 => 'label-success',
                                        -1 => 'label-danger',
                                    ];
                                    $classLabel = 'label-default';
                                    if (key_exists($document['is_valid'], $statusLabel)) {
                                        $classLabel = $statusLabel[$document['is_valid']];
                                    }
                                    ?>
                                    <span class="label <?= $classLabel ?>">
                                        <?php if ($document['is_valid'] == 0): ?>
                                            On Review
                                        <?php elseif ($document['is_valid'] == 1): ?>
                                            Valid
                                        <?php else: ?>
                                            Rejected
                                        <?php endif; ?>
                                    </span>
                                </td>
                                <td>
                                    <?php
                                    $statusLabel = [
                                        0 => 'label-success',
                                        1 => 'label-primary',
                                    ];
                                    $classLabel = 'label-default';
                                    if (key_exists($document['is_response'], $statusLabel)) {
                                        $classLabel = $statusLabel[$document['is_response']];
                                    }
                                    ?>
                                    <span class="label <?= $classLabel ?>">
                                        <?= $document['is_response'] ? 'Response' : 'Request' ?>
                                    </span>
                                </td>
                                <td></td>
                            </tr>
                        <?php endforeach; ?>

                        <?php if (count($documents) <= 0): ?>
                            <tr>
                                <td colspan="8">No documents available</td>
                            </tr>
                        <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h4 class="box-title">Add Document Upload</h4>
                </div>
                <div class="box-body" id="form-upload-wrapper">
                    <?php $this->load->view('upload/form', ['documentTypes' => $documentTypes]); ?>
                    <?php if (count($documentTypes) <= 0): ?>
                        <p class="lead">You've uploaded all document of <?= $upload['booking_type'] ?>, <a href="<?= site_url('upload/view/' . $upload['id']) ?>">click
                                here</a> (action - edit/replace) if you want to replace old files</p>
                    <?php endif; ?>
                </div>
                <div class="box-footer">
                    <i class="fa fa-info-circle"></i> &nbsp;
                    If there is no additional form, that mean you have completed all available upload document.
                </div>
            </div>
        </div>

        <div class="box-footer">
            <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
            <?php if (count($documentTypes) > 0): ?>
                <button type="submit" class="btn btn-primary pull-right">
                    Save Additional Document
                </button>
            <?php endif; ?>
        </div>
    </form>
</div>
<!-- /.box -->

<script src="<?= base_url('assets/app/js/upload.js?v=13') ?>" defer></script>