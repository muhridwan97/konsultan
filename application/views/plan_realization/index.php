<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Plan Realization Data</h3>
        <div class="pull-right">
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PLAN_REALIZATION_CREATE)): ?>
                <a href="<?= site_url('plan-realization/create') ?>" class="btn btn-primary">
                    Generate Plan
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-plan-realization">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th class="type-date">Date</th>
                <th>Total Inbound</th>
                <th>Total Outbound</th>
                <th>Analysis</th>
                <th class="type-status">Status</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_PLAN_REALIZATION_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script id="control-plan-realization-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PLAN_REALIZATION_VIEW)): ?>
                <li>
                    <a href="<?= site_url('plan-realization/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PLAN_REALIZATION_PRINT)): ?>
                <li>
                    <a href="<?= site_url('plan-realization/print-plan-realization/{{id}}') ?>">
                        <i class="fa fa-print"></i> Print
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PLAN_REALIZATION_CREATE)): ?>
                <li>
                    <a href="<?= site_url('plan-realization/send-plan-realization/{{id}}') ?>"
                       class="btn-validate" data-validate-title="Send notification" data-validate="send {{send_label}}" data-label="{{date}}">
                        <i class="fa ion-android-send"></i> Resend {{send_label}}
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PLAN_REALIZATION_EDIT)): ?>
                <li role="separator" class="divider close-realization"></li>
                <li class="close-realization">
                    <a href="<?= site_url('plan-realization/close/{{id}}') ?>">
                        <i class="fa ion-close"></i> Close Realization
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PLAN_REALIZATION_EDIT)): ?>
                <li class="edit-plan">
                    <a href="<?= site_url('plan-realization/edit/{{id}}') ?>">
                        <i class="fa ion-compose"></i> Edit
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PLAN_REALIZATION_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('plan-realization/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-title="Plan realization"
                       data-label="Plan date {{date}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/plan-realization.js?v=2') ?>" defer></script>
<?php if (AuthorizationModel::isAuthorized(PERMISSION_PLAN_REALIZATION_CREATE)): ?>
    <?php $this->load->view('template/_modal_validate') ?>
    <script src="<?= base_url('assets/app/js/validation.js') ?>" defer></script>
<?php endif; ?>