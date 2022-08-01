<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Position Type</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_POSITION_TYPE_CREATE)): ?>
                <a href="<?= site_url('position-type/create') ?>" class="btn btn-primary">
                    Create Position Type
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-position-type">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Position Type</th>
                <th>Is Usable</th>
                <th>Color</th>
                <th>Created At</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($positionTypes as $positionType): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $positionType['position_type'] ?></td>
                    <td><?= $positionType['is_usable'] == 1 ? 'YES' : 'NO' ?></td>
                    <td style="background-color: <?= $positionType['color'] ?>; color: white">
                        <?= if_empty($positionType['color'], '-') ?>
                    </td>
                    <td><?= readable_date($positionType['created_at']) ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_POSITION_TYPE_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('position-type/view/' . $positionType['id']) ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_POSITION_TYPE_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('position-type/edit/' . $positionType['id']) ?>">
                                            <i class="fa ion-compose"></i>Edit
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_POSITION_TYPE_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('position-type/delete/' . $positionType['id']) ?>" class="btn-delete"
                                           data-id="<?= $positionType['id'] ?>"
                                           data-title="Position Type"
                                           data-label="<?= $positionType['position_type'] ?>">
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

<?php if (AuthorizationModel::isAuthorized(PERMISSION_POSITION_TYPE_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>