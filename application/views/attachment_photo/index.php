<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Attachment Photos</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_ATTACHMENT_PHOTO_CREATE)): ?>
                <a href="<?= site_url('attachment-photo/create') ?>" class="btn btn-primary">
                    Handling Photo
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-attachment-photo">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Photo Name</th>
                <th>Description</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($attachmentPhotos as $photo): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $photo['photo_name'] ?></td>
                    <td><?= if_empty($photo['description'], 'No description') ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_ATTACHMENT_PHOTO_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('attachment-photo/view/' . $photo['id']) ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_ATTACHMENT_PHOTO_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('attachment-photo/edit/' . $photo['id']) ?>">
                                            <i class="fa ion-compose"></i>Edit
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_ATTACHMENT_PHOTO_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('attachment-photo/delete/' . $photo['id']) ?>" class="btn-delete"
                                           data-id="<?= $photo['id'] ?>"
                                           data-title="Attachment Photo"
                                           data-label="<?= $photo['photo_name'] ?>">
                                            <i class="fa ion-trash-a"></i> Delete
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_ATTACHMENT_PHOTO_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif ?>