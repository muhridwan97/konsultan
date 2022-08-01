<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Document Files</h3>
    </div>

    <div class="box-body">

        <?php $this->load->view('template/_alert') ?>

        <form class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Document Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $document['type'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Document Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($document['date'], 'd F Y') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Status</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php
                                $statusLabel = [
                                    'EMPTY' => 'label-warning',
                                    'PENDING' => 'label-default',
                                    'REJECTED' => 'label-danger',
                                    'APPROVED' => 'label-success',
                                ];
                                $document['status'] = if_empty($document['status'], $document['total_files'] <= 0 ? 'EMPTY' : 'PENDING');
                                ?>
                                <span class="label <?= $statusLabel[$document['status']] ?>">
                                    <?= $document['status'] ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Validated By</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($document['validator_name'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Validated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($document['validated_at']) ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($document['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created By</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($document['creator_name'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($document['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Updated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(format_date($document['updated_at'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Uploaded Files</h3>
                <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_VALIDATE)): ?>
                    <?php if($document['status'] == 'PENDING'): ?>
                        <div class="pull-right">
                            <a href="<?= site_url('work-order-document/validate-document/approve/' . $document['id'] . '?redirect=' . base_url(uri_string())) ?>"
                               class="btn btn-success btn-sm btn-validate"
                               data-validate="approve"
                               data-label="<?= readable_date($document['date'], false) ?>">
                                <i class="fa ion-checkmark"></i> Approve
                            </a>
                            <a href="<?= site_url('work-order-document/validate-document/reject/' . $document['id'] . '?redirect=' . base_url(uri_string())) ?>"
                               class="btn btn-danger btn-sm btn-validate"
                               data-validate="reject"
                               data-label="<?= readable_date($document['date'], false) ?>">
                                <i class="fa ion-close"></i> Reject
                            </a>
                        </div>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable" id="table-file">
                    <thead>
                    <tr>
                        <th style="width: 20px">No</th>
                        <th>File</th>
                        <th>Created At</th>
                        <th style="width: 70px">Action</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1;
                    foreach ($files as $file): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= basename($file['source']) ?></td>
                            <td><?= format_date($document['created_at'], 'd F Y H:i') ?></td>
                            <td>
                                <a href="<?= asset_url($file['source']) ?>" class="btn btn-primary">
                                    Download
                                </a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if (empty($files)): ?>
                        <tr>
                            <td colspan="5">No document files</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Job Data</h3>
            </div>
            <div class="box-body">
                <?php $this->load->view('workorder_document/_job_list', compact('workOrders')) ?>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_EDIT)): ?>
            <?php if ($document['status'] != WorkOrderDocumentModel::STATUS_APPROVED): ?>
                <a href="<?= site_url("work-order-document/edit/" . $document['id']) ?>"
                   class="btn btn-warning pull-right">
                    Edit Files
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_VALIDATE)): ?>
    <?php $this->load->view('template/_modal_validate'); ?>
<?php endif; ?>

<script src="<?= base_url('assets/app/js/validation.js') ?>" defer></script>
<script src="<?= base_url('assets/app/js/upload_document_file.js') ?>" defer></script>