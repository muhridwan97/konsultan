<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Operation Cut Off</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPERATION_CUT_OFF_CREATE)): ?>
                <a href="<?= site_url('operation-cut-off/create') ?>" class="btn btn-primary">
                    Create Shift
                </a>
            <?php endif; ?>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-operation-cut-off">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Branch</th>
                <th>Shift</th>
                <th>Start</th>
                <th>End</th>
                <th>Status</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($operationCutOffs as $index => $operationCutOff): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $operationCutOff['branch'] ?></td>
                    <td><?= $operationCutOff['shift'] ?></td>
                    <td><?= format_date($operationCutOff['start'], 'H:i') ?></td>
                    <td><?= format_date($operationCutOff['end'], 'H:i') ?></td>
                    <td><?= $operationCutOff['status'] ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPERATION_CUT_OFF_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('operation-cut-off/view/' . $operationCutOff['id']) ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPERATION_CUT_OFF_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('operation-cut-off/edit/' . $operationCutOff['id']) ?>">
                                            <i class="fa ion-compose"></i>Edit
                                        </a>
                                    </li>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPERATION_CUT_OFF_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('operation-cut-off/delete/' . $operationCutOff['id']) ?>" class="btn-delete"
                                           data-id="<?= $operationCutOff['id'] ?>"
                                           data-title="Operation cut off"
                                           data-label="<?= $operationCutOff['branch'] ?> - Shift <?= $operationCutOff['shift'] ?>">
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

<?php if (AuthorizationModel::isAuthorized(PERMISSION_OPERATION_CUT_OFF_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>
