<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Document Files</h3>
        <?php if ((UserModel::authenticatedUserData('user_type') != "INTERNAL" && $document['is_valid'] != 1) || ((UserModel::authenticatedUserData('user_type') == "INTERNAL"))): ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_CREATE)): ?>
                <a href="<?= site_url("upload_document_file/create/" . $document['id']) ?>"
                   class="btn btn-primary pull-right">
                    <i class="fa ion-plus-round"></i> Add <?= $document['document_type'] ?> Files
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <!-- /.box-header -->

    <div class="box-body">

        <?php $this->load->view('template/_alert') ?>

        <form class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Document Type</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $document['document_type'] ?> (<?= $document['is_response'] ? 'RESPONSE' : 'REQUEST' ?>)
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Document Subtype</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($document['subtype'], "-") ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">No Document</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $document['no_document'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Document Date</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($document['document_date'], false) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Total Item</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty(numerical($document['total_item'],3,true), ' - ') ?>
                            </p>
                        </div>
                    </div>
                    <?php if(!empty($parties)): ?>
                        <?php foreach ($parties as $key => $party): ?>
                            <?php if($key == 0): ?>
                            <div class="form-group">
                                <label class="col-sm-4">Party</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= $party['type'] ?> (<?= if_empty(numerical($party['party'],2,true), ' - ') ?> - <?= $party['shape'] ?>)
                                    </p>
                                </div>
                            </div>
                            <?php else: ?>
                            <div class="form-group">
                                <label class="col-sm-4"> </label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?= $party['type'] ?> (<?= if_empty(numerical($party['party'],2,true), ' - ') ?> - <?= $party['shape'] ?>)
                                    </p>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Freetime Date</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty(readable_date($document['freetime_date'], false),'-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Expired Date</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty(readable_date($document['expired_date'], false),'-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($document['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($document['created_at']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Updated At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($document['updated_at']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
        <table class="table table-bordered table-striped" style="overflow-x: scroll; display:block;" id="table-file">
            <thead>
            <tr>
                <th style="width: 20px">No</th>
                <th>File</th>
                <th>Description</th>
                <th style="width: 200px;">Description 2</th>
                <th>Description Date</th>
                <th>Attachment</th>
                <th>Created By</th>
                <th>Created At</th>
                <th style="width: 60px">Action</th>

            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($files as $file): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td>
                        <a href="<?= asset_url(if_empty($file['directory'], '', '', '/') . $file['source']) ?>" target="_blank">
                            <?= basename($file['source']) ?>
                        </a>
                    </td>
                    <td><?= empty($file['description']) ? 'No description' : $file['description'] ?></td>
                    <td style="word-break: break-word;"><?= empty($file['description2']) ? 'No description' : $file['description2'] ?></td>
                    <td><?= empty($file['description_date']) ? '-' : readable_date($file['description_date']) ?> </td>
                    <td>
                        <?php if(empty($file['description_attachment'])): ?>
                            <?= " No Attachment" ?>
                        <?php else: ?>
                            <?php if($file['id_document_type'] == 194): ?>
                                <a href="<?= asset_url(if_empty($file['directory'], '', '', '/') . $file['description_attachment']) ?>" target="_blank">
                                    <?= basename($file['description_attachment']) ?>
                                </a>
                            <?php else: ?>
                                <a href="<?= asset_url('description_attachments/' . ltrim($file['description_attachment'], 'description_attachments/')) ?>" target="_blank">
                                    <?= basename($file['description_attachment']) ?>
                                </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                    <td><?= if_empty($file['uploader'], '-') ?> </td>
                    <td><?= format_date($file['created_at'], 'd F Y H:i') ?> </td>
                    <td>
                        <a href="<?= asset_url($file['directory'] . '/' . $file['source']) ?>"
                           class="btn btn-primary">
                            <i class="fa ion-ios-search-strong"></i>
                        </a>
                        <?php if ((UserModel::authenticatedUserData('user_type') != "INTERNAL" && $document['is_valid'] != 1) || ((UserModel::authenticatedUserData('user_type') == "INTERNAL"))): ?>
                            <?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_DELETE)): ?>
                            <a href="<?= site_url('upload_document_file/delete/' . $file['id']) ?>"
                               class="btn btn-danger btn-delete-file"
                               data-id="<?= $file['id'] ?>"
                               data-id-document="<?= $file['id_upload_document'] ?>"
                               data-label="<?= $file['source'] ?>">
                                <i class="ion-trash-b"></i>
                            </a>
                            <?php endif; ?>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <!-- /.box-body -->
    <div class="box-footer">
        <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
        <a href="<?= site_url('upload/view/' . $document['id_upload']) ?>" class="btn btn-primary">Back to Document</a>
    </div>
</div>
<!-- /.box -->

<?php if (AuthorizationModel::isAuthorized(PERMISSION_UPLOAD_DELETE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-delete-file">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="id_upload_document" id="id_upload_document">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Delete File</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Are you sure want to delete file
                            <strong id="file-title"></strong>?
                        </p>
                        <p class="text-danger">
                            This action will delete file related files.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete File</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php endif; ?>

<script src="<?= base_url('assets/app/js/upload_document_file.js') ?>" defer></script>