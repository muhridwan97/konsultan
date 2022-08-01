<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Job History</h3>
        <div class="pull-right">
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_LOCK)) : ?>
                <a href="#form-lock-tally-history" class="btn btn-primary btn-lock-toggle">
                    <?= get_url_param('lock_tally_history', 0) ? 'Hide' : 'Show' ?> Lock Menu
                </a>
            <?php endif; ?>
            <a href="#form-filter-tally-history" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_tally_history', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a class="btn btn-primary reload">
                <i class="fa fa-repeat"></i> Refresh
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('workorder/_lock_tally_history', ['hidden' => isset($_GET['filter_lock_tally_history']) ? false : true]) ?>
        <?php $this->load->view('workorder/_filter_tally_history', ['hidden' => isset($_GET['filter_tally_history']) ? false : true]) ?>
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" data-page-length='50' id="table-jobs">
            <thead>
                <tr>
                    <th style="width: 20px">No</th>
                    <th>Customer</th>
                    <th class="field-job">No Job</th>
                    <th class="field-handling no-wrap">Handling</th>
                    <th class="field-gate-date">Gate Date</th>
                    <th class="field-job-date">Job Complete</th>
                    <th class="field-description" style="width: 60px">Description</th>
                    <th class="field-locked-at">Locked At</th>
                    <th class="field-status">Status</th>
                    <th class="field-action" style="width: 60px">Action</th>
                </tr>
            </thead>
        </table>
    </div>
</div>

<script id="control-work-order-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right row-workorder"
            data-id="{{id}}"
            data-no="{{no_work_order}}"
            data-attachment="{{attachment}}"
            data-status="{{status}}"
            data-print-total="{{print_total}}"
            data-print-max="{{print_max}}">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VIEW)) : ?>
                <li>
                    <a href="<?= site_url('work-order/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View Job Result
                    </a>
                </li>

                <li class="action-view-pallet-marking" data-validated-edit="<?= AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATED_EDIT) ?>">
                    <a href="#" class="btn-view-pallet-history">
                        <i class="fa ion-search"></i> View Pallet Marking History
                    </a>
                </li>

                <li class="action-open-pallet-marking" data-validated-edit="<?= AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATED_EDIT) ?>">
                    <a href="#"
                       class="btn-request-open-pallet">
                        <i class="fa fa-unlock"></i> Request Unlock Pallet Marking
                    </a>
                </li>
            <?php endif ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATE)) : ?>
                <li class="action-review">
                    <a href="<?= site_url('work-order/review-work-order/{{id}}') ?>" class="btn-validate" data-validate="review Job" data-label="{{no_work_order}}">
                        <i class="fa fa-clipboard"></i> Review Job
                    </a>
                </li>
                <li class="action-validate">
                    <a href="<?= site_url('work-order/validate-work-order/{{id}}') ?>" class="btn-validate" data-validate="validate job" data-label="{{no_work_order}}">
                        <i class="fa fa-check"></i> Validate Job
                    </a>
                </li>
                <li class="action-reject">
                    <a href="<?= site_url('work-order/reject-work-order/{{id}}') ?>" class="btn-validate" data-validate="reject job" data-label="{{no_work_order}}">
                        <i class="fa fa-times"></i> Reject Job
                    </a>
                </li>
                <li role="separator" class="divider divider-validate"></li>
            <?php endif ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_APPROVED)) : ?>
                <li class="action-fix">
                    <a href="<?= site_url('work-order/fix-work-order/{{id}}') ?>" class="btn-validate" data-validate="fix job" data-label="{{no_work_order}}">
                        <i class="fa fa-check-square-o"></i> Fix Job
                    </a>
                </li>
                <li role="separator" class="divider divider-validate"></li>
            <?php endif ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_PALLET_APPROVED)) : ?>
                <li class="action-confirm-pallet-marking">
                    <a href="#" class="btn-unlock-pallet">
                        <i class="fa fa-check"></i>Unlock Pallet Marking Now
                    </a>
                </li>
            <?php endif ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_DISCREPANCY_EDIT)) : ?>
                <li class="action-edit {{is_locked_disable}}">
                    <a data-toggle="{{is_locked_tooltip}}"
                       title="{{is_locked_title}}"
                       href="<?= site_url('tally/edit/{{id}}?discrepancy=1') ?>" class="btn-edit" onclick="{{unlink}}">
                        <i class="fa ion-android-warning"></i> Edit Discrepancy Job
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_EDIT)) : ?>
                
                <li class="action-edit {{is_locked_disable}}" data-validated-edit="<?= AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATED_EDIT) ?>">    
                    <a data-toggle="{{is_locked_tooltip}}"
                       title="{{is_locked_title}}"
                     href="<?= site_url('tally/edit/{{id}}') ?>" class="btn-edit" onclick="{{unlink}}">
                        <i class="fa ion-compose"></i> Edit Job
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('work-order/request-edit/{{id}}') ?>" class="btn-request-edit">
                        <i class="fa ion-compose"></i> Edit By Support
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('work-order/update-print-max/{{id}}') ?>" class="btn-update-max-print">
                        <i class="fa ion-compose"></i> Edit Max Print Job
                    </a>
                </li>
                <li class="action-edit" data-validated-edit="<?= AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_VALIDATED_EDIT) ?>">
                    <a href="#"
                       class="btn-request-open-tally {{btn_request}}">
                        <i class="fa fa-comment"></i> Request Open Tally
                    </a>
                </li>
                <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_LOCK)) : ?>
                <li class="action-edit {{hide_unlock}}">
                    <a href="<?= site_url() ?>work-order/unlock_by_work_order_id/{{id}}?<?= $_SERVER['QUERY_STRING'] ?>" target="_blank" class="unlock-now-request-tally">
                        <i class="fa fa-unlock"></i> Unlock Now
                    </a>
                </li>
                <li class="action-edit {{hide_lock}}">
                    <a href="<?= site_url() ?>work-order/locked_by_work_order_id/{{id}}?<?= $_SERVER['QUERY_STRING'] ?>" target="_blank" class="unlock-now-request-tally">
                        <i class="fa fa-lock"></i> Lock Now
                    </a>
                </li>
                <?php endif; ?>
                <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_UNLOCK_HANDHELD)) : ?>
                    <li>
                        <a href="<?= site_url() ?>work-order-unlock-handheld/unlock/{{id}}">
                            <i class="fa fa-unlock-alt"></i> Unlock Handheld
                        </a>
                    </li>
                <?php endif; ?>
                <li>
                    <a href="<?= site_url('work-order/upload-attachment/{{id}}') ?>" class="btn-upload-attachment">
                        <i class="fa ion-upload"></i> Upload Attachment
                    </a>
                </li>
            <?php endif ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_PRINT)) : ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('work-order/print-work-order/{{id}}?redirect=' . base_url(uri_string())) ?>"
                       class="btn-print-job-sheet">
                        <i class="fa fa-print"></i> Print Job Sheet
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('work-order/print-tally-sheet/{{id}}') ?>" target = "_blank">
                        <i class="fa fa-print"></i> Print Tally Sheet
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('work-order/print-tally-sheet2/{{id}}') ?>" target = "_blank">
                        <i class="fa fa-print"></i> Print Tally Sheet 2
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('work-order/print-tally-sheet3/{{id}}') ?>" target = "_blank">
                        <i class="fa fa-print"></i> Print Tally Sheet 3
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('work-order/print-tally-sheet4/{{id}}') ?>" target = "_blank">
                        <i class="fa fa-print"></i> Print Tally Sheet 4
                    </a>
                </li>
                <li class="action-print-pallet-marking {{is_print}}">
                    <a href="<?= site_url('work-order/print-pallet/{{id}}') ?>" onclick='return {{hidden_print}};' target = "_blank">
                        <i class="fa fa-print"></i> Print Pallet Marking
                    </a>
                </li>
                <li class="action-print-pallet-marking {{is_print}}">
                    <a href="<?= site_url('work-order/print-pallet-label/{{id}}') ?>" onclick='return {{hidden_print}};' target = "_blank">
                        <i class="fa fa-print"></i> Print Label Pallet Marking
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('work-order/print-eir/{{id}}') ?>">
                        <i class="fa fa-print"></i> Print EIR
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('work-order/print-handover-report/{{id}}') ?>">
                        <i class="fa fa-print"></i> Print Handover
                    </a>
                </li>
            <?php endif ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_DELETE)) : ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('work-order/delete/{{id}}') ?>"
                       class="btn-delete" data-title="Auction" data-label="{{no_work_order}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif ?>
        </ul>
    </div>
</script>

<?php if (AuthorizationModel::isAuthorized([PERMISSION_WORKORDER_VALIDATE, PERMISSION_WORKORDER_APPROVED])) : ?>
    <?php $this->load->view('template/_modal_validate') ?>
    <script src="<?= base_url('assets/app/js/validation.js') ?>" defer></script>
<?php endif ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_DELETE)) : ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_PRINT)) : ?>
    <?php $this->load->view('workorder/_modal_confirm_print_job_sheet') ?>
    <?php $this->load->view('workorder/_modal_update_max_print') ?>
<?php endif ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_EDIT)) : ?>
    <?php $this->load->view('workorder/_modal_request_edit') ?>
    <?php $this->load->view('workorder/_modal_upload_attachment') ?>
<?php endif ?>

<?php $this->load->view('workorder/_modal_pallet_marking_history') ?>
<?php $this->load->view('workorder/_modal_confirm_locked_tally') ?>
<?php $this->load->view('workorder/_modal_request_open_tally') ?>
<?php $this->load->view('workorder/_modal_request_open_pallet') ?>
<?php $this->load->view('workorder/_modal_confirm_open_pallet') ?>

<script src="<?= base_url('assets/app/js/work-order.js?v=10') ?>" defer></script>