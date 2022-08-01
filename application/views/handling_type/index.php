<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Handling Types</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_TYPE_CREATE)): ?>
                <a href="<?= site_url('handling_type/create') ?>" class="btn btn-primary">
                    Create Handling Type
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-handling-type">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Handling Type</th>
                <th>Code</th>
                <th>Category</th>
                <th>Duration</th>
                <th>Total Sub</th>
                <th>Description</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($handlingTypes as $handlingType): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $handlingType['handling_type'] ?></td>
                    <td><?= $handlingType['handling_code'] ?></td>
                    <td><?= $handlingType['category'] ?></td>
                    <td class="responsive-hide"><?= $handlingType['duration'] ?> minutes</td>
                    <td class="responsive-hide"><?= $handlingType['total_component'] ?> items</td>
                    <td><?= $handlingType['description'] ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_TYPE_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('handling_type/view/' . $handlingType['id']) ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_TYPE_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('handling_type/edit/' . $handlingType['id']) ?>">
                                            <i class="fa ion-compose"></i>Edit
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_TYPE_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('handling_type/delete/' . $handlingType['id']) ?>" class="btn-delete"
                                           data-id="<?= $handlingType['id'] ?>"
                                           data-title="Handling Type"
                                           data-label="<?= $handlingType['handling_type'] ?>">
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

<?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_TYPE_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>