<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Extension Fields</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_EXTENSION_FIELD_CREATE)): ?>
                <a href="<?= site_url('extension-field/create') ?>" class="btn btn-primary">
                    Create Extension
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-extension-field">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Field Title</th>
                <th>Field Name</th>
                <th>Type</th>
                <th>Description</th>
                <th>Total Used</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($extensionFields as $extensionField): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $extensionField['field_title'] ?></td>
                    <td><?= $extensionField['field_name'] ?></td>
                    <td><?= $extensionField['type'] ?></td>
                    <td class="responsive-hide"><?= if_empty($extensionField['description'], 'No description') ?></td>
                    <td class="responsive-hide"><?= $extensionField['total_booking_type'] ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_EXTENSION_FIELD_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('extension-field/view/' . $extensionField['id']) ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_EXTENSION_FIELD_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('extension-field/edit/' . $extensionField['id']) ?>">
                                            <i class="fa ion-compose"></i>Edit
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_EXTENSION_FIELD_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('extension-field/delete/' . $extensionField['id']) ?>" class="btn-delete"
                                           data-id="<?= $extensionField['id'] ?>"
                                           data-title="Extension Field"
                                           data-label="<?= $extensionField['field_title'] ?>">
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

<?php if (AuthorizationModel::isAuthorized(PERMISSION_EXTENSION_FIELD_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>