<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">E-seals</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_ESEAL_CREATE)): ?>
                <a href="<?= site_url('eseal/create') ?>" class="btn btn-primary">
                    Create E-seal
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-eseal">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>No E-seal</th>
                <th>Description</th>
                <th>Is Used</th>
                <th>Latest Used In</th>
                <th>Device ID</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($eseals as $eseal): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $eseal['no_eseal'] ?></td>
                    <td><?= if_empty($eseal['description'], '-') ?></td>
                    <td><?= $eseal['is_used'] ? 'Yes' : 'No' ?></td>
                    <td><?= if_empty($eseal['used_in'], '-') ?></td>
                    <td>
                        <?= if_empty($eseal['id_device'], 'Not Connected') ?><br>
                        <small class="text-muted"><?= $eseal['device_name'] ?></small>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_ESEAL_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('eseal/view/' . $eseal['id']) ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_ESEAL_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('eseal/edit/' . $eseal['id']) ?>">
                                            <i class="fa ion-compose"></i>Edit
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_ESEAL_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('eseal/delete/' . $eseal['id']) ?>" class="btn-delete"
                                           data-id="<?= $eseal['id'] ?>"
                                           data-title="Eseal"
                                           data-label="<?= $eseal['no_eseal'] ?>">
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

<?php if (AuthorizationModel::isAuthorized(PERMISSION_ESEAL_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>
