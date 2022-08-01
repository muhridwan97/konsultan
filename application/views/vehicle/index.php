<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Vehicles</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_VEHICLE_CREATE)): ?>
                <a href="<?= site_url('vehicle/create') ?>" class="btn btn-primary">
                    Create Vehicle
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-vehicle">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Vehicles</th>
                <th>Branch</th>
                <th>Status</th>
                <th>Type</th>
                <th>No Plate</th>
                <th>Description</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($vehicles as $vehicle): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $vehicle['vehicle_name'] ?></td>
                    <td><?= if_empty($vehicle['branch'], 'NO BRANCH'); ?></td>
                    <td><?= $vehicle['status'] ?></td>
                    <td><?= $vehicle['vehicle_type'] ?></td>
                    <td><?= $vehicle['no_plate'] ?></td>
                    <td><?= $vehicle['description'] ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_VEHICLE_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('vehicle/view/' . $vehicle['id']) ?>">
                                            <i class="fa ion-search"></i> View
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_VEHICLE_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('vehicle/edit/' . $vehicle['id']) ?>">
                                            <i class="fa ion-compose"></i> Edit
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_VEHICLE_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('vehicle/delete/' . $vehicle['id']) ?>" class="btn-delete"
                                           data-id="<?= $vehicle['id'] ?>"
                                           data-title="Vehicle"
                                           data-label="<?= $vehicle['vehicle_name'] . ' ('.$vehicle['no_plate'].')' ?>">
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

<?php if (AuthorizationModel::isAuthorized(PERMISSION_VEHICLE_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>