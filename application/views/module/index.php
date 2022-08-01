<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Modules</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_SETTING_EDIT)): ?>
            <a href="<?= site_url('module/create') ?>" class="btn btn-primary pull-right">
                Create Module
            </a>
        <?php endif ?>
    </div>
    <!-- /.box-header -->
    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-module">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Module</th>
                <th>Description</th>
                <th>Type</th>
                <th>Hostname</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($modules as $module): ?>
                <tr>
                    <td class="responsive-hide"><?= $no ?></td>
                    <td class="responsive-title"><?= $module['module_name'] ?></td>
                    <td><?= $module['module_description'] ?></td>
                    <td><?= $module['type'] ?></td>
                    <td><?= $module['hostname'] ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_SETTING_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('module/view/' . $module['id']) ?>">
                                            <i class="fa ion-search"></i> View
                                        </a>
                                    </li>
                                <?php endif ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_SETTING_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('module/edit/' . $module['id']) ?>">
                                            <i class="fa ion-compose"></i> Edit
                                        </a>
                                    </li>
                                <?php endif ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_SETTING_EDIT)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('module/delete/' . $module['id']) ?>" class="btn-delete"
                                           data-id="<?= $module['id'] ?>"
                                           data-title="Module"
                                           data-label="<?= $module['module_name'] ?>">
                                            <i class="fa ion-trash-a"></i> Delete
                                        </a>
                                    </li>
                                <?php endif ?>
                            </ul>
                        </div>
                    </td>
                </tr>
                <?php $no++; endforeach; ?>
            </tbody>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_SETTING_EDIT)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif ?>