<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Discrepancy Handover Data</h3>
        <div class="pull-right">
            <a href="#form-filter" class="btn btn-info btn-filter-toggle">
                <?= get_url_param('filter', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <?php $this->load->view('discrepancy_handover/_filter', ['hidden' => isset($_GET['filter']) ? false : true]) ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-discrepancy-handover">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>No Discrepancy</th>
                <th class="type-date">Date</th>
                <th>No Reference</th>
                <th>Customer</th>
                <th>Total Item</th>
                <th class="type-status">Status</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_DISCREPANCY_HANDOVER_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script id="control-discrepancy-handover-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_DISCREPANCY_HANDOVER_VIEW)): ?>
                <li>
                    <a href="<?= site_url('discrepancy-handover/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View
                    </a>
                </li>
                <li class="action-print">
                    <a href="<?= site_url('discrepancy-handover/print-discrepancy-handover/{{id}}') ?>">
                        <i class="fa fa-print"></i> Print
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_DISCREPANCY_HANDOVER_EDIT)): ?>
                <li role="separator" class="divider action-upload"></li>
                <li class="action-upload">
                    <a href="<?= site_url('discrepancy-handover/upload/{{id}}') ?>"
                       class="btn-upload"
                       data-label="{{no_discrepancy}}"
                       data-attachment="{{attachment}}"
                       data-attachment-url="{{attachment_url}}"
                       data-no-reference="{{no_reference}}">
                        <i class="fa fa-upload"></i> <span class="btn-upload-label">Upload</span>
                    </a>
                </li>
                <li class="action-resend">
                    <a href="<?= site_url('discrepancy-handover/resend-confirm-email/{{id}}') ?>" class="btn-validate"
                       data-validate="resend"
                       data-with-message="false"
                       data-label="{{no_discrepancy}}">
                        <i class="fa fa-send"></i> Resend Email
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_DISCREPANCY_HANDOVER_VALIDATE)): ?>
                <li role="separator" class="divider action-usage"></li>
                <li class="action-usage">
                    <a href="<?= site_url('discrepancy-handover/not-use/{{id}}') ?>" class="btn-validate"
                       data-validate="not use"
                       data-label="{{no_discrepancy}}"
                       data-theme="danger">
                        <i class="fa ion-close-circled"></i> Not Use
                    </a>
                </li>
                <li class="action-usage">
                    <a href="<?= site_url('discrepancy-handover/in-use/{{id}}') ?>" class="btn-validate"
                       data-validate="in use"
                       data-label="{{no_discrepancy}}"
                       data-theme="success">
                        <i class="fa ion-checkmark-circled"></i> In Use
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_DISCREPANCY_HANDOVER_PROCEED)): ?>
                <li role="separator" class="divider action-realization"></li>
                <li class="action-realization">
                    <a href="<?= site_url('discrepancy-handover/document/{{id}}') ?>" class="btn-validate"
                       data-validate="document"
                       data-label="{{no_discrepancy}}"
                       data-theme="primary">
                        <i class="fa fa-file-o"></i> Set Document
                    </a>
                </li>
                <li class="action-realization">
                    <a href="<?= site_url('discrepancy-handover/physical/{{id}}') ?>" class="btn-validate"
                       data-validate="physical"
                       data-label="{{no_discrepancy}}"
                       data-theme="success">
                        <i class="fa fa-cube"></i> Set Physical
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_DISCREPANCY_HANDOVER_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li class="action-cancel">
                    <a href="<?= site_url('discrepancy-handover/cancel/{{id}}') ?>" class="btn-validate"
                       data-validate="cancel"
                       data-label="{{no_discrepancy}}">
                        <i class="fa ion-close"></i> Cancel
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('discrepancy-handover/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-title="Discrepancy"
                       data-label="{{no_discrepancy}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_DISCREPANCY_HANDOVER_EDIT)): ?>
    <?php $this->load->view('discrepancy_handover/_modal_attachment') ?>
<?php endif; ?>

<?php if (AuthorizationModel::isAuthorized([PERMISSION_DISCREPANCY_HANDOVER_PROCEED, PERMISSION_DISCREPANCY_HANDOVER_VALIDATE, PERMISSION_DISCREPANCY_HANDOVER_DELETE])): ?>
    <?php $this->load->view('template/_modal_validate') ?>
    <script src="<?= base_url('assets/app/js/validation.js') ?>" defer></script>
<?php endif; ?>
<script src="<?= base_url('assets/app/js/discrepancy-handover.js') ?>" defer></script>