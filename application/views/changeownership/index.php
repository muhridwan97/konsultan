<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Change Ownership</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_OWNERSHIP_CREATE)): ?>
            <a href="<?= site_url('change_ownership/create') ?>" class="btn btn-primary pull-right">
                Change Ownership
            </a>
        <?php endif; ?>
    </div>
    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-changeOwnership">
            <thead>
            <tr>
                <th>No</th>
                <th>No Transaction</th>
                <th>Change Date</th>
                <th>From</th>
                <th>To</th>
                <th>Description</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($changeOwnerships as $changeOwnership): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $changeOwnership['no_change_ownership'] ?></td>
                    <td><?= $changeOwnership['change_date'] ?></td>
                    <td><?= $changeOwnership['owner_from'] ?></td>
                    <td><?= $changeOwnership['owner_to'] ?></td>
                    <td><?= if_empty($changeOwnership['description'], 'No description') ?></td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_OWNERSHIP_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('change_ownership/view/' . $changeOwnership['id']) ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_OWNERSHIP_EDIT)): ?>
                                    <li>
                                        <a href="<?= site_url('change_ownership/edit/' . $changeOwnership['id']) ?>">
                                            <i class="fa ion-compose"></i>Edit
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_OWNERSHIP_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <li>
                                        <a href="<?= site_url('change_ownership/delete/' . $changeOwnership['id']) ?>"
                                           class="btn-delete-changeOwnership"
                                           data-id="<?= $changeOwnership['id'] ?>"
                                           data-label="<?= $changeOwnership['no_change_ownership'] ?>">
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
            <tfoot>
            <tr>
                <th>No</th>
                <th>No Transaction</th>
                <th>Change Date</th>
                <th>From</th>
                <th>To</th>
                <th>Description</th>
                <th>Action</th>
            </tr>
            </tfoot>
        </table>
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->

<?php if (AuthorizationModel::isAuthorized(PERMISSION_OWNERSHIP_DELETE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-delete-changeOwnership">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Delete Ownership</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Are you sure want to delete change ownership
                            <strong id="changeOwnership-title"></strong>?</p>
                        <p class="small text-danger">
                            This action will perform soft delete, actual data still exist on database.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete Ownership</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php endif; ?>