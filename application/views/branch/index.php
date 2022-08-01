<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Branches</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_BRANCH_CREATE)): ?>
                <a href="<?= site_url('branch/create') ?>" class="btn btn-primary">
                    Create Branch
                </a>
            <?php endif ?>
        </div>
    </div>
    <div class="box-body">

        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-branch">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Branch</th>
                <th>Branch Type</th>
                <th>Address</th>
                <th>PIC</th>
                <th>Description</th>
                <th>Whatsapp Group</th>
                <th>Whatsapp Group Security</th>
                <th>Total Warehouse</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($branches as $branch): ?>
                <?php $employee = $this->employee->getById($branch['pic']); ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $branch['branch'] ?></td>
                    <td class="responsive-title"><?= if_empty($branch['branch_type'], '-') ?></td>
                    <td><?= if_empty($branch['address'], 'No address') ?></td>
                    <td><?= if_empty($employee['name'], 'No PIC') ?></td>
                    <td><?= if_empty($branch['description'], 'No description') ?></td>
                    <td><?= if_empty($branch['whatsapp_group'], 'No group') ?></td>
                    <td><?= if_empty($branch['whatsapp_group_security'], 'No group') ?></td>
                    <td>
                        <?php if ($branch['total_warehouse'] == 0): ?>
                            No warehouse
                        <?php else: ?>
                            <a href="<?= site_url('branch/warehouse/' . $branch['id']) ?>">
                                <?= number_format($branch['total_warehouse'], 0, ',', '.') ?> warehouses
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

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_BRANCH_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('branch/view/' . $branch['id']) ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                <?php endif ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_BRANCH_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('branch/edit/' . $branch['id']) ?>">
                                            <i class="fa ion-compose"></i>Edit
                                        </a>
                                    </li>
                                <?php endif ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_BRANCH_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('branch/delete/' . $branch['id']) ?>" class="btn-delete"
                                           data-id="<?= $branch['id'] ?>"
                                           data-title="Branch"
                                           data-label="<?= $branch['branch'] ?>">
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

<?php if (AuthorizationModel::isAuthorized(PERMISSION_BRANCH_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif ?>