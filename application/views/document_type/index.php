<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Document Types</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_DOCUMENT_TYPE_CREATE)): ?>
                <a href="<?= site_url('document-type/create') ?>" class="btn btn-primary">
                    Create Document Type
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-document-type">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Document</th>
                <th>Directory</th>
                <th>Is Visible</th>
                <th>Is Confirmed</th>
                <th>Is Reminder</th>
                <th>Is Reserved</th>
                <th>Is Email Notification</th>
                <th>Description</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($documentTypes as $documentType): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $documentType['document_type'] ?></td>
                    <td><?= $documentType['directory'] ?></td>
                    <td><?= $documentType['is_visible'] ? 'Yes' : 'No' ?></td>
                    <td><?= $documentType['is_confirm'] ? 'Yes' : 'No' ?></td>
                    <td><?= $documentType['is_reminder'] ? 'Yes' : 'No' ?></td>
                    <td<?= $documentType['is_reserved'] ? ' class="danger"' : '' ?>><?= $documentType['is_reserved'] ? 'Yes' : 'No' ?></td>
                    <td><?= $documentType['is_email_notification'] ? 'Yes' : 'No' ?></td>
                    <td><?= if_empty($documentType['description'], 'No description') ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_DOCUMENT_TYPE_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('document_type/view/' . $documentType['id']) ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if(!$documentType['is_reserved']): ?>
                                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_DOCUMENT_TYPE_EDIT)): ?>
                                        <li>
                                            <a href="<?= site_url('document-type/edit/' . $documentType['id']) ?>">
                                                <i class="fa ion-compose"></i>Edit
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_DOCUMENT_TYPE_DELETE)): ?>
                                        <li role="separator" class="divider"></li>
                                        <li>
                                            <a href="<?= site_url('document_type/delete/' . $documentType['id']) ?>"
                                               class="btn-delete"
                                               data-id="<?= $documentType['id'] ?>"
                                               data-title="Document Type"
                                               data-label="<?= $documentType['document_type'] ?>">
                                                <i class="fa ion-trash-a"></i> Delete
                                            </a>
                                        </li>
                                    <?php endif; ?>
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

<?php if (AuthorizationModel::isAuthorized(PERMISSION_DOCUMENT_TYPE_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>