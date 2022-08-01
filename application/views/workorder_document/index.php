<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Document Data</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_CREATE)): ?>
            <a href="<?= site_url('work-order-document/create') ?>" class="btn btn-primary pull-right">
                Upload Document
            </a>
        <?php endif; ?>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" data-page-length='50' id="table-work-order-document">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th class="type-date">Doc Date</th>
                <th class="type-numeric">Total Files</th>
                <th class="type-status">Status Doc</th>
                <th class="type-status-job">Job Validation</th>
                <th class="type-job-state">Job State</th>
                <th>Validated By</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_VALIDATE)): ?>
    <?php $this->load->view('template/_modal_validate'); ?>
<?php endif; ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script id="control-work-order-document-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_VIEW)): ?>
                <li>
                    <a href="<?= site_url('work-order-document/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View
                    </a>
                </li>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_PRINT)): ?>
                <li>
                    <a href="<?= site_url('work-order-document/print-document/{{id}}?redirect=' . base_url(uri_string())) ?>">
                        <i class="fa fa-print"></i> Print
                    </a>
                </li>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_EDIT)): ?>
                <li class="edit">
                    <a href="<?= site_url('work-order-document/edit/{{id}}') ?>">
                        <i class="fa ion-compose"></i> Edit
                    </a>
                </li>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_VALIDATE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('work-order-document/validate-document/approve/{{id}}') ?>"
                       class="btn-validate"
                       data-validate="approve"
                       data-label="{{date}}">
                        <i class="fa ion-checkmark"></i> Approve
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('work-order-document/validate-document/reject/{{id}}') ?>"
                       class="btn-validate"
                       data-validate="reject"
                       data-label="{{date}}">
                        <i class="fa ion-close"></i> Reject
                    </a>
                </li>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORK_ORDER_DOCUMENT_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('work-order-document/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-title="Job Document"
                       data-label="{{date}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/validation.js') ?>" defer></script>
<script src="<?= base_url('assets/app/js/work-order-document.js') ?>" defer></script>