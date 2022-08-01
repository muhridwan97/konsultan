<div class="box box-primary">
    <div class="box-header">
        <div class="box-title">Shifting</div>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_SHIFTING_CREATE)): ?>
            <a href="<?= site_url('shifting/create') ?>" class="btn btn-primary pull-right">
                Create Shifting
            </a>
        <?php endif; ?>
    </div>
    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-shifting">
            <thead>
            <tr>
                <th>No</th>
                <th>No Shifting</th>
                <th>Shifting Date</th>
                <th>Shifting Count</th>
                <th>Status</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1; ?>
            <?php foreach ($shiftings as $shifting): ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $shifting['no_shifting'] ?></td>
                    <td><?= readable_date($shifting['shifting_date']) ?></td>
                    <td><?= $shifting['handling_count'] ?></td>
                    <td>
                        <?php
                        $statusLabel = [
                            ShiftingModel::STATUS_PENDING => 'label-warning',
                            ShiftingModel::STATUS_APPROVED => 'label-primary',
                        ];
                        ?>
                        <div class="label <?= $statusLabel[$shifting['status']] ?>">
                            <?= $shifting['status'] ?>
                        </div>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right">
                                <li class="dropdown-header">ACTION</li>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_SHIFTING_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('shifting/view/' . $shifting['id']) ?>">
                                            <i class="fa ion-search"></i>View
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_SHIFTING_VALIDATE)): ?>
                                    <?php if ($shifting['status'] == ShiftingModel::STATUS_PENDING) : ?>
                                        <li>
                                            <a href="<?= site_url('shifting/validate/' . $shifting['id']) ?>"
                                               class="btn-approve-shifting"
                                               data-id="<?= $shifting['id'] ?>"
                                               data-label="<?= $shifting['no_shifting'] ?>">
                                                <i class="fa ion-checkmark"></i>Approve
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_SHIFTING_DELETE)): ?>
                                    <?php if ($shifting['status'] == ShiftingModel::STATUS_PENDING) : ?>
                                        <li role="separator" class="divider"></li>
                                        <li>
                                            <a href="<?= site_url('shifting/delete/' . $shifting['id']) ?>"
                                               class="btn-delete-shifting"
                                               data-id="<?= $shifting['id'] ?>"
                                               data-label="<?= $shifting['no_shifting'] ?>">
                                                <i class="fa ion-trash-a"></i> Delete
                                            </a>
                                        </li>
                                    <?php endif; ?>
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

<?php if (AuthorizationModel::isAuthorized(PERMISSION_SHIFTING_VALIDATE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-approve-shifting">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Approve Shifting</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Are you sure want to
                            <strong class="text-success">Approve</strong> shifting
                            <strong id="shifting-title"></strong>?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-success">Approve Shifting</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_SHIFTING_DELETE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-delete-shifting">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Delete Shifting</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Are you sure want to delete shifting
                            <strong id="shifting-title"></strong>?
                        </p>
                        <p class="small text-danger">
                            This action will perform soft delete, actual data still exist on database.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete Shifting</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script src="<?= base_url('assets/app/js/shifting.js?v=3') ?>" defer></script>