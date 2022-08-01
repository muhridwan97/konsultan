<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Payments</h3>

        <div class="pull-right">
            <?php if (AuthorizationModel::isAuthorized([PERMISSION_PAYMENT_VALIDATE, PERMISSION_PAYMENT_REALIZE])): ?>
                <button data-toggle="modal" data-target="#modal-resend-bulk-notification" class="btn btn-danger">
                    Resend Bulk Notif
                </button>
            <?php else: ?>
                <form action="<?= site_url('payment/resend-bulk-notification') ?>" method="post" class="inline">
                    <?= _csrf() ?>
                    <button data-target="#modal-resend-bulk-notification" class="btn btn-danger" data-toggle="one-touch">
                        Resend Bulk Draft
                    </button>
                </form>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_REALIZE)): ?>
                <a href="<?= site_url('payment/ask-approval') ?>" class="btn btn-warning batch-action" id="btn-ask-approval-batch" style="display: none">
                    Ask Approval
                </a>
            <?php endif; ?>
            <a href="#form-filter" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_CREATE)): ?>
                <a href="<?= site_url('payment/create') ?>" class="btn btn-primary">
                    Create
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">

        <?php $this->load->view('template/_alert') ?>
        <?php $this->load->view('payment/_filter', ['hidden' => isset($_GET['filter']) ? false : true]) ?>
        <table class="table table-bordered table-striped table-ajax responsive" id="table-payment" data-allow-realize="<?= AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_REALIZE) ?>" data-login="<?= UserModel::authenticatedUserData('id') ?>">
            <thead>
            <tr>
                <th class="type-no" style="width: 20px">No</th>
                <th class="type-booking">No Booking</th>
                <th class="type-payment">No Payment</th>
                <th class="type-type-payment">Type</th>
                <th class="type-payment-date">Payment Date</th>
                <th class="type-currency-bank">Request</th>
                <th class="type-currency">Realization</th>
                <th class="type-status-payment">Status</th>
                <th class="type-status-check">Check</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->

<script id="control-payment-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right row-payment"
            data-id="{{id}}"
            data-label="{{no_payment}} - {{payment_type}}">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_VIEW)): ?>
                <li>
                    <a href="<?= site_url('payment/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_PRINT)): ?>
                <li>
                    <a href="<?= site_url('payment/print_payment/{{id}}') ?>">
                        <i class="fa fa-print"></i> Print Payment
                    </a>
                </li>
            <?php endif; ?>

            <li class="set-notif">
                <a href="<?= site_url('payment/resend/{{id}}') ?>" class="btn-resend">
                    <i class="fa fa-send"></i> Resend Notification
                </a>
            </li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_EDIT)): ?>
                <li class="edit">
                    <a href="<?= site_url('payment/edit/{{id}}') ?>">
                        <i class="fa ion-compose"></i> Edit
                    </a>
                </li>
            <?php endif; ?>


            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_VALIDATE)): ?>
                <li class="validate">
                    <a href="<?= site_url('payment/view/{{id}}') ?>"
                       class="btn-validate-payment">
                        <i class="fa ion-checkmark"></i> Validate
                    </a>
                </li>
            <?php endif; ?>


            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_CREATE)): ?>
                <li role="separator" class="divider submit"></li>
                <?php if ($isTimeSubmission): ?>
                    <li class="submit">
                        <a href="<?= site_url('payment/submit/{{id}}') ?>"
                           class="btn-submission" data-validate="submit" data-label="{{no_payment}} - {{payment_type}}">
                            <i class="fa fa-check-square"></i> Submit
                        </a>
                    </li>
                <?php else: ?>
                    <li class="submit" data-toggle="tooltip" data-title="Only available Work day 08:00 - 15:00 or Saturday: 08:00 - 12:00<?= $isHoliday ? ' (PUBLIC HOLIDAY)' : '' ?>">
                        <a href="javascript:void(0)" style="cursor: not-allowed; pointer-events: none; opacity: .6">
                            <i class="fa fa-check-square"></i> Submit
                        </a>
                    </li>
                <?php endif; ?>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_REALIZE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('payment/switch_charge_position/{{id}}') ?>">
                        <i class="fa ion-refresh"></i> {{charge_position_label}}
                    </a>
                </li>
                <li class="set-bank">
                    <a href="<?= site_url('payment/set-bank/{{id}}') ?>">
                        <i class="fa ion-compose"></i> Set Bank
                    </a>
                </li>
                <li role="separator" class="divider realization"></li>
                <li class="submit-payment">
                    <a href="<?= site_url('payment/tps-realization/{{id}}') ?>">
                        <i class="fa ion-checkmark"></i> TPS Realization
                    </a>
                </li>
                <li class="realization">
                    <a href="<?= site_url('payment/realization/{{id}}') ?>">
                        <i class="fa ion-checkmark"></i> Realization
                    </a>
                </li>
                <li class="reject-submission">
                    <a href="<?= site_url('payment/reject-submission/{{id}}') ?>"
                       class="btn-validate" data-validate="reject" data-label="{{no_payment}} - {{payment_type}} revert to approved">
                        <i class="fa ion-close"></i> Reject Submission
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_CHECK)): ?>
                <li role="separator" class="divider set-check"></li>
                <li class="set-check">
                    <a href="<?= site_url('payment-check/payment-validate/{{id}}/0?status=APPROVED&redirect=' . site_url('payment')) ?>"
                       class="btn-validate" data-validate="approve" data-label="{{check_label}}">
                        <i class="fa ion-checkmark"></i> Check Approve
                    </a>
                </li>
                <li class="set-check">
                    <a href="<?= site_url('payment-check/payment-validate/{{id}}/0?status=REJECTED&redirect=' . site_url('payment')) ?>"
                       class="btn-validate" data-validate="reject" data-label="{{check_label}}">
                        <i class="fa ion-close"></i> Check Reject
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_DELETE)): ?>
                <li role="separator" class="divider delete"></li>
                <li>
                    <a href="<?= site_url('payment/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-title="Payment"
                       data-label="{{no_payment}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>

        </ul>
    </div>
</script>

<div class="modal fade" id="modal-resend-bulk-notification">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <form action="<?= site_url('payment/resend-bulk-notification') ?>" method="post">
                <?= _csrf() ?>
                <div class="modal-header">
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <h4 class="modal-title">Resend Notification</h4>
                </div>
                <div class="modal-body">
                    <p class="lead">
                        This action will resend notification to all branches payment with status
                        <?php if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_REALIZE)): ?>
                            <strong>PENDING</strong> and <strong>APPROVED</strong>
                        <?php else: ?>
                            <strong>APPROVED</strong>
                        <?php endif; ?>
                        to group depends on its status.
                    </p>
                    <div class="row">
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="date_from">Date From</label>
                                <input type="text" class="form-control daterangepicker2" name="date_from" id="date_from" required
                                       placeholder="Payment create date from" autocomplete="off">
                            </div>
                        </div>
                        <div class="col-sm-6">
                            <div class="form-group">
                                <label for="date_to">Date To</label>
                                <input type="text" class="form-control daterangepicker2" name="date_to" id="date_to" required
                                       placeholder="Payment create date to" autocomplete="off">
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">
                        CLOSE
                    </button>
                    <button type="submit" class="btn btn-primary" data-toggle="one-touch">
                        RESEND ALL BRANCHES
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_VALIDATE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-validate-payment">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Payment Validation</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Validate payment
                            <strong id="payment-title"></strong>?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" name="status" value="REJECTED">Reject</button>
                        <button type="submit" class="btn btn-success" name="status" value="APPROVED">Approve</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php endif; ?>

<?php $this->load->view('payment/_modal_ask_approval'); ?>
<?php $this->load->view('payment/_modal_resend_notification'); ?>
<?php $this->load->view('payment/_modal_submission') ?>
<?php $this->load->view('template/_modal_validate') ?>
<script src="<?= base_url('assets/app/js/validation.js') ?>" defer></script>
<?php if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>
<script src="<?= base_url('assets/app/js/payment.js?v=7') ?>" defer></script>
