<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Warehouses</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WAREHOUSE_CREATE)): ?>
                <a href="<?= site_url('warehouse/create') ?>" class="btn btn-primary">
                    Create Warehouse
                </a>
            <?php endif ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-warehouse">
            <thead>
            <tr>
                <th style="width: 30px;">No</th>
                <th>Branch</th>
                <th>Warehouse</th>
                <th>Type</th>
                <th>Columns (X)</th>
                <th>Rows (Y)</th>
                <th>Total Position</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($warehouses as $warehouse): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title">
                        <a href="<?= site_url('branch/view/' . $warehouse['id_branch']) ?>">
                            <?= $warehouse['branch'] ?>
                        </a>
                    </td>
                    <td><?= $warehouse['warehouse'] ?></td>
                    <td><?= if_empty($warehouse['type'], '-') ?></td>
                    <td><?= numerical($warehouse['total_column'], 0) ?></td>
                    <td><?= numerical($warehouse['total_row'], 0) ?></td>
                    <td>
                        <?php if ($warehouse['total_position'] == 0): ?>
                            No position available
                        <?php else: ?>
                            <a href="<?= site_url('warehouse/position/' . $warehouse['id']) ?>">
                                <?= number_format($warehouse['total_position'], 0, ',', '.') ?> positions
                            </a>
                        <?php endif ?>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_WAREHOUSE_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('warehouse/view/' . $warehouse['id']) ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                <?php endif ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_WAREHOUSE_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('warehouse/edit/' . $warehouse['id']) ?>">
                                            <i class="fa ion-compose"></i>Edit
                                        </a>
                                    </li>
                                <?php endif ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_WAREHOUSE_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('warehouse/delete/' . $warehouse['id']) ?>"
                                           class="btn-delete"
                                           data-id="<?= $warehouse['id'] ?>"
                                           data-title="Warehouse"
                                           data-label="<?= $warehouse['warehouse'] ?>">
                                            <i class="fa ion-trash-a"></i> Delete
                                        </a>
                                    </li>
                                <?php endif ?>

                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_WAREHOUSE_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif ?>