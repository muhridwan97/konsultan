<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Monthly Validation</h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-storage-over-space" data-type="monthly">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th class="type-date-from">Date From</th>
                <th class="type-date-to">Date To</th>
                <th>Description</th>
                <th class="text-center">Total Customer</th>
                <th class="type-proceed text-center">Total Proceed</th>
                <th class="type-validated text-center">Total Validated</th>
                <th class="type-skipped text-center">Total Skipped</th>
                <th class="type-status text-center">Status</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_STORAGE_USAGE_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script id="control-storage-over-space-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_STORAGE_USAGE_VIEW)): ?>
                <li>
                    <a href="<?= site_url('storage-over-space/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PLAN_REALIZATION_PRINT)): ?>
                <li>
                    <a href="<?= site_url('storage-over-space/print-over-space/{{id}}') ?>">
                        <i class="fa fa-print"></i> Print
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_STORAGE_USAGE_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('storage-over-space/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-title="Storage over space"
                       data-label="Storage over space {{date_from}} - {{date_to}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/storage-over-space.js') ?>" defer></script>