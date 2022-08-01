<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Handling Components</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_COMPONENT_CREATE)): ?>
                <a href="<?= site_url('component/create') ?>" class="btn btn-primary">
                    Create Component
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-handling-component">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Component</th>
                <th>Category</th>
                <th>Description</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($components as $handlingComponent): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $handlingComponent['handling_component'] ?></td>
                    <td><?= $handlingComponent['component_category'] ?></td>
                    <td><?= if_empty($handlingComponent['description'], 'No description') ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_COMPONENT_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('component/view/' . $handlingComponent['id']) ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_COMPONENT_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('component/edit/' . $handlingComponent['id']) ?>">
                                            <i class="fa ion-compose"></i>Edit
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_COMPONENT_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('component/delete/' . $handlingComponent['id']) ?>" class="btn-delete"
                                           data-id="<?= $handlingComponent['id'] ?>"
                                           data-title="Component"
                                           data-label="<?= $handlingComponent['handling_component'] ?>">
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

<?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_COMPONENT_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif ?>