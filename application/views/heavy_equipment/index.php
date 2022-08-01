<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Heavy Equipments</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_HEAVY_EQUIPMENT_CREATE)): ?>
                <a href="<?= site_url('heavy_equipment/create') ?>" class="btn btn-primary">
                    Create Heavy Equipment
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-heavy-equipment">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Name</th>
                <th>Type</th>
                <th>Description</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($heavyEquipments as $heavyEquipment): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $heavyEquipment['name'] ?></td>
                    <td><?= $heavyEquipment['type'] ?></td>
                    <td><?= $heavyEquipment['description'] ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_HEAVY_EQUIPMENT_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('heavy_equipment/view/' . $heavyEquipment['id']) ?>">
                                            <i class="fa ion-search"></i> View
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_HEAVY_EQUIPMENT_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('heavy_equipment/edit/' . $heavyEquipment['id']) ?>">
                                            <i class="fa ion-compose"></i> Edit
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_HEAVY_EQUIPMENT_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('heavy_equipment/delete/' . $heavyEquipment['id']) ?>" class="btn-delete"
                                           data-id="<?= $heavyEquipment['id'] ?>"
                                           data-title="Heavy Equipment"
                                           data-label="<?= $heavyEquipment['name'] ?>">
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

<?php if (AuthorizationModel::isAuthorized(PERMISSION_HEAVY_EQUIPMENT_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>