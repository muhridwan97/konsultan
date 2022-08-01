<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Opname</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_CREATE)): ?>
            <a href="<?= site_url('opname/create') ?>" class="btn btn-primary pull-right">
                Create Opname
            </a>
        <?php endif; ?>
        <p><?= $this->session->flashdata('message_check'); ?></p>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-opname">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Branch</th>
                <th>No Opname</th>
                <th>Opname Date</th>
                <th>Opname Type</th>
                <th>Description</th>
                <th>Status</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $labelStatus = [
                'PENDING' => 'default',
                'PROCESSED' => 'primary',
                'REOPENED' => 'primary',
                'APPROVED' => 'success',
                'REJECTED' => 'danger',
                'COMPLETED' => 'success',
            ];
            ?>
                <?php $no = 1 ?>
                <?php foreach ($opnames as $opname): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><a href="<?= site_url('branch/view/' . $opname['id_branch']) ?>"><?= $opname['branch'] ?></a></td>
                        <td><?= $opname['no_opname'] ?></td>
                        <td><?= format_date($opname['opname_date'], 'd F Y') ?></td>
                        <td>
                            <?= $opname['opname_type'] ?>
                            <?php if (in_array($opname['status'], ['PROCESSED', 'COMPLETED'])): ?>
                                <br><span class="text-danger"><?= $opname['is_import'] ? 'IMPORT' : 'INPUT' ?></span>
                            <?php endif ?>
                        </td>
                        <td><?= if_empty($opname['description'], 'No description') ?></td>
                        <td>
                            <?php if ($opname['status'] != 'PROCESSED'): ?>
                                <span class="label label-<?= $labelStatus[$opname['status']] ?>"> <?= $opname['status'] ?> </span>
                            <?php else: ?>
                                <div class="form-group">
                                <span class="label label-<?= $labelStatus[$opname['status']] ?>"> <?= $opname['status'] ?> </span>
                                </div>
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_EDIT)): ?>
                                <div class="form-group">
                                    <a href="<?= site_url('opname/edit_status/' . $opname['id']) ?>">
                                        <span class="label label-<?= $labelStatus["COMPLETED"] ?>"> <?= "CLICK TO COMPLETE" ?> </span>
                                    </a>
                                </div>
                                <?php endif; ?>
                            <?php endif; ?>
                        </td>
                        <td>
                            <div class="btn-group">
                                <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                        aria-haspopup="true" aria-expanded="false">
                                    Action <span class="caret"></span>
                                </button>
                                <ul class="dropdown-menu dropdown-menu-right">
                                    <li class="dropdown-header">ACTION</li>
                                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_VIEW)): ?>
                                        <li>
                                            <a href="<?= site_url('opname/view/' . $opname['id']) ?>">
                                                <i class="fa ion-search"></i>View Detail
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_PRINT)): ?>
                                        <li>
                                            <a href="<?= site_url('opname/print_opname/' . $opname['id']) ?>">
                                                <i class="fa fa-print"></i>Print Job Opname
                                            </a>
                                        </li>
                                        <li>
                                            <a href="<?= site_url('opname/download-opname/' . $opname['id']) ?>">
                                                <i class="fa fa-download"></i>Download Opname Form
                                            </a>
                                        </li>
                                    <?php endif; ?>

                                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_PROCESS)): ?>
                                        <?php if(($opname['status'] == OpnameModel::STATUS_PENDING) || ($opname['status'] == OpnameModel::STATUS_PROCESSED) || ($opname['status'] == OpnameModel::STATUS_REOPENED)): ?>
                                            <li role="separator" class="divider"></li>
                                            <li>
                                                <a href="<?= site_url('opname/upload-opname/' . $opname['id']) ?>">
                                                    <i class="fa fa-upload"></i>Upload Opname Form
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= site_url('opname/process/' . $opname['id']) ?>">
                                                    <i class="fa fa-pencil-square-o"></i>Process
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if(($opname['status'] == OpnameModel::STATUS_COMPLETED) || ($opname['status'] == OpnameModel::STATUS_APPROVED)): ?>
                                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_PRINT_RESULT)): ?>
                                            <li role="separator" class="divider"></li>
                                            <li>
                                                <a href="<?= site_url('opname/print-opname-result/' . $opname['id']) ?>">
                                                    <i class="fa fa-print"></i>Print Result
                                                </a>
                                            </li>
                                            <li>
                                                <a href="<?= site_url('opname/download-result/' . $opname['id']) ?>">
                                                    <i class="fa fa-download"></i>Download Result
                                                </a>
                                            </li>
                                        <?php endif; ?>

                                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_VIEW_RESULT)): ?>
                                            <li>
                                                <a href="<?= site_url('opname/result/' . $opname['id']) ?>">
                                                    <i class="fa fa-search-plus"></i>View Result
                                                </a>
                                            </li>
                                            <li role="separator" class="divider"></li>
                                        <?php endif; ?>
                                    <?php endif; ?>


                                    <?php if (($opname['status'] == OpnameModel::STATUS_COMPLETED) || ($opname['status'] == OpnameModel::STATUS_CLOSED)): ?>
                                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_VALIDATE)): ?>
                                            <li>
                                                <a href="<?= site_url('opname/validate/' . $opname['id']) ?>"
                                                   class="btn-validate-opname"
                                                   data-id="<?= $opname['id'] ?>"
                                                   data-label="<?= $opname['no_opname'] ?>">
                                                    <i class="fa ion-checkmark"></i>Validate
                                                </a>
                                            </li>
                                        <?php endif; ?>
                                    <?php endif; ?>

                                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_ACCESS)): ?>
                                        <?php if(($opname['status'] == OpnameModel::STATUS_REJECTED) || ($opname['status'] == OpnameModel::STATUS_CLOSED)): ?>
                                        <li>
                                            <a href="<?= site_url('opname/access/reopen/'. $opname['id']) ?>"
                                               class="btn-validate"
                                               data-validate="reopen"
                                               data-label="{{date}}">
                                                <i class="fa fa-unlock"></i> Re Open Process
                                            </a>
                                        </li>
                                        <?php endif; ?>
                                    <?php endif; ?>
                                    <?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_DELETE)): ?>
                                        <li role="separator" class="divider"></li>
                                        <li>
                                            <a href="<?= site_url('opname/delete/' . $opname['id']) ?>" class="btn-delete-opname"
                                               data-id="<?= $opname['id'] ?>"
                                               data-label="<?= $opname['no_opname'] ?>">
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

<?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_VALIDATE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-validate-opname">
        <div class="modal-dialog" role="upload">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Validating Opname</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Validate opname
                            <strong id="opname-title"></strong>?
                        </p>
                        <div class="form-group">
                            <label for="description" class="control-label">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="2" placeholder="Validation message"></textarea>
                            <span class="help-block">This message will be included in email to customer</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" name="status" value="REJECTED">Reject</button>
                        <button type="submit" class="btn btn-success" name="status" value="APPROVED">Approve</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_OPNAME_DELETE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-delete-opname">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Delete Opname</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Are you sure want to delete opname
                            <strong id="opname-title"></strong>?
                        </p>
                        <p class="text-danger">
                            This action will perform soft delete, actual data still exist on database.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete Opname</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script src="<?= base_url('assets/app/js/opname.js') ?>" defer></script>
