<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Booking</h3>
        <?php if (AuthorizationModel::isAuthorized([PERMISSION_BOOKING_IN_CREATE, PERMISSION_BOOKING_OUT_CREATE])): ?>
            <div class="pull-right">
                <a href="#form-filter" class="btn btn-info btn-filter-toggle">
                    <?= get_url_param('filter', 0) ? 'Hide' : 'Show' ?> Filter
                </a>
                <div class="btn-group">
                    <button type="button" class="btn btn-success dropdown-toggle" data-toggle="dropdown">
                        <i class="fa fa-file-excel-o"></i>
                    </button>
                    <ul class="dropdown-menu dropdown-menu-right">
                        <li class="dropdown-header">FORMAT TYPE</li>
                        <li>
                            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=excel">
                                <i class="fa fa-file-excel-o"></i> Excel
                            </a>
                        </li>
                        <li>
                            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=csv">
                                <i class="fa fa-file-o"></i> CSV
                            </a>
                        </li>
                    </ul>
                </div>
                <?php if($opnamePendingDay == false): ?>
                    <a href="<?= site_url('booking-import/create') ?>" class="btn btn-primary">
                        Import
                    </a>
                    <a href="<?= site_url('booking/create') ?>" class="btn btn-primary">
                        Create Booking
                    </a>
                <?php endif; ?>
            </div>
        <?php endif; ?>
    </div>

    <div class="box-body">
        <?php if (AuthorizationModel::isAuthorized([PERMISSION_BOOKING_IN_CREATE, PERMISSION_BOOKING_OUT_CREATE])): ?>
            <?php if($opnamePendingDay): ?>
                <div class="alert alert-danger">Opname is pending, please validate first before create new booking.</div>
            <?php endif; ?>
        <?php endif; ?>

        <?php $this->load->view('template/_alert') ?>

        <?php $this->load->view('booking/_filter', ['hidden' => isset($_GET['filter']) ? false : true]) ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-booking">
            <thead>
            <tr>
                <th>No</th>
                <th>Customer</th>
                <th>No Booking</th>
                <th>Date</th>
                <th>Type</th>
                <th>Document</th>
                <th>Status</th>
                <th class="type-payout">Payout</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script id="control-booking-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right row-booking"
            data-id="{{id}}"
            data-category="{{category}}"
            data-label="{{no_booking}}"
            data-status-payout="{{status_payout}}"
            data-payout-until-date="{{payout_until_date}}">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_IN_VIEW) || AuthorizationModel::isAuthorized(PERMISSION_BOOKING_OUT_VIEW)): ?>
                <li>
                    <a href="<?= site_url('booking/view/{{id}}') ?>">
                        <i class="fa ion-search"></i>View Detail
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('booking/status/{{id}}') ?>">
                        <i class="fa ion-search"></i>View Statuses
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('booking/cif/{{id}}') ?>">
                        <i class="fa ion-search"></i>View CIF
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('booking/payment/{{id}}') ?>">
                        <i class="fa ion-search"></i>View Payments
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('booking/invoice/{{id}}') ?>">
                        <i class="fa ion-search"></i>View Invoices
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('booking/safe_conduct/{{id}}') ?>">
                        <i class="fa ion-search"></i>View Safe Conducts
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('booking/handling/{{id}}') ?>">
                        <i class="fa ion-search"></i>View Handlings
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('booking/work-order/{{id}}') ?>">
                        <i class="fa ion-search"></i>View Jobs
                    </a>
                </li>
                <li role="separator" class="divider"></li>
            <?php endif; ?>

            <?php
            $authorizeInPrint = AuthorizationModel::isAuthorized(PERMISSION_BOOKING_IN_PRINT);
            $authorizeOutPrint = AuthorizationModel::isAuthorized(PERMISSION_BOOKING_OUT_PRINT);
            ?>
            <?php if ($authorizeInPrint || $authorizeOutPrint): ?>
                <li data-authorize-in="<?= $authorizeInPrint ?>" data-authorize-out="<?= $authorizeOutPrint ?>">
                    <a href="<?= site_url('booking/print_booking/{{id}}?redirect=' . base_url(uri_string())) ?>">
                        <i class="fa fa-print"></i> Print Booking
                    </a>
                </li>
                <li data-authorize-in="<?= $authorizeInPrint ?>" data-authorize-out="<?= $authorizeOutPrint ?>">
                    <a href="<?= site_url('booking/export-booking/{{id}}') ?>">
                        <i class="fa fa-file-excel-o"></i> Export Booking
                    </a>
                </li>
                <li role="separator" class="divider"></li>
            <?php endif; ?>

            <?php
            $authorizeInValidate = AuthorizationModel::isAuthorized(PERMISSION_BOOKING_IN_VALIDATE);
            $authorizeOutValidate = AuthorizationModel::isAuthorized(PERMISSION_BOOKING_OUT_VALIDATE);
            ?>
            <?php if ($authorizeInValidate || $authorizeOutValidate): ?>
                <li class="action-validate" data-authorize-in="<?= $authorizeInValidate ?>" data-authorize-out="<?= $authorizeOutValidate ?>">
                    <a href="<?= site_url('booking/validate/approve/{{id}}') ?>" class="btn-validate" data-toggle="tooltip" data-validate="approve" data-label="{{no_booking}}">
                        <i class="fa ion-checkmark"></i>Approve
                    </a>
                </li>
                <li class="action-validate" data-authorize-in="<?= $authorizeInValidate ?>" data-authorize-out="<?= $authorizeOutValidate ?>">
                    <a href="<?= site_url('booking/validate/reject/{{id}}') ?>" class="btn-validate" data-toggle="tooltip" data-validate="reject" data-label="{{no_booking}}">
                        <i class="fa ion-close"></i>Reject
                    </a>
                </li>
                <li class="action-complete" data-authorize-in="<?= $authorizeInValidate ?>" data-authorize-out="<?= $authorizeOutValidate ?>">
                    <a href="<?= site_url('booking/validate/complete/{{id}}') ?>" class="btn-validate" data-validate="complete" data-label="{{no_booking}}">
                        <i class="fa ion-checkmark"></i>Complete
                    </a>
                </li>
                <li class="action-revert-complete" data-authorize-in="<?= $authorizeInValidate ?>" data-authorize-out="<?= $authorizeOutValidate ?>">
                    <a href="<?= site_url('booking/validate/approve/{{id}}') ?>" class="btn-validate" data-validate="revert complete" data-label="{{no_booking}}">
                        <i class="fa ion-refresh"></i>Revert To Approve
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_STATUS_REVERT)): ?>
                <li class="action-revert-booked">
                    <a href="<?= site_url('booking/revert/booked/{{id}}') ?>" class="btn-validate" data-validate="revert booked" data-label="{{no_booking}}">
                        <i class="fa ion-refresh"></i>Revert To Booked
                    </a>
                </li>
            <?php endif; ?>

            <?php
            $authorizeInValidatePayout = AuthorizationModel::isAuthorized(PERMISSION_BOOKING_IN_EDIT_PAYMENT_STATUS);
            $authorizeOutValidatePayout = AuthorizationModel::isAuthorized(PERMISSION_BOOKING_IN_EDIT_PAYMENT_STATUS);
            ?>
            <?php if ($authorizeInValidatePayout || $authorizeOutValidatePayout): ?>
                <li class="action-validate-payout">
                    <a href="<?= site_url('booking/validate-payout/{{id}}') ?>"
                       class="btn-validate-payout"
                       data-id="{{id}}"
                       data-label="{{no_booking}}">
                        <i class="fa ion-checkmark"></i>Payout Validate
                    </a>
                </li>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_CONTROL_REVERT)): ?>
                <li class="action-payout-revert">
                    <a href="<?= site_url('booking/validate-payout/{{id}}') ?>"
                       class="btn-revert-payout"
                       data-id="{{id}}"
                       data-label="{{no_booking}}">
                        <i class="fa ion-refresh"></i>Revert Payout
                    </a>
                </li>
            <?php endif; ?>

            <?php
            $authorizeInEdit = AuthorizationModel::isAuthorized(PERMISSION_BOOKING_IN_EDIT);
            $authorizeOutEdit = AuthorizationModel::isAuthorized(PERMISSION_BOOKING_OUT_EDIT);
            ?>
            <?php if ($authorizeInEdit || $authorizeOutEdit): ?>
                <li class="action-edit edit" data-authorize-in="<?= $authorizeInEdit ?>" data-authorize-out="<?= $authorizeOutEdit ?>">
                    <a href="<?= site_url('booking/edit/{{id}}') ?>">
                        <i class="fa ion-compose"></i>Edit Booking
                    </a>
                </li>
                <li class="edit-extension" data-authorize-in="<?= $authorizeInEdit ?>" data-authorize-out="<?= $authorizeOutEdit ?>">
                    <a href="<?= site_url('booking/edit_extension/{{id}}') ?>">
                        <i class="fa ion-compose"></i>Edit Extension
                    </a>
                </li>
            <?php endif; ?>

            <?php
            $authorizeInEditPaymentStatus = AuthorizationModel::isAuthorized(PERMISSION_BOOKING_IN_EDIT_PAYMENT_STATUS);
            $authorizeOutEditPaymentStatus = AuthorizationModel::isAuthorized(PERMISSION_BOOKING_OUT_EDIT_PAYMENT_STATUS);
            ?>
            <?php if ($authorizeInEditPaymentStatus || $authorizeOutEditPaymentStatus): ?>
                <li class="action-edit" data-authorize-in="<?= $authorizeInEditPaymentStatus ?>" data-authorize-out="<?= $authorizeOutEditPaymentStatus ?>">
                    <a href="<?= site_url('booking/edit_payment_status/{{id}}') ?>">
                        <i class="fa ion-compose"></i>Edit Payment Status
                    </a>
                </li>
            <?php endif; ?>

            <?php
            $authorizeInEditBCFStatus = AuthorizationModel::isAuthorized(PERMISSION_BOOKING_IN_EDIT_BCF_STATUS);
            $authorizeOutEditBCFStatus = AuthorizationModel::isAuthorized(PERMISSION_BOOKING_OUT_EDIT_BCF_STATUS);
            ?>
            <?php if ($authorizeInEditBCFStatus || $authorizeOutEditBCFStatus): ?>
                <li class="action-edit" data-authorize-in="<?= $authorizeInEditBCFStatus ?>" data-authorize-out="<?= $authorizeOutEditBCFStatus ?>">
                    <a href="<?= site_url('booking/edit_bcf_status/{{id}}') ?>">
                        <i class="fa ion-compose"></i>Edit BCF Status
                    </a>
                </li>
            <?php endif; ?>

            <?php
            $authorizeInDelete = AuthorizationModel::isAuthorized(PERMISSION_BOOKING_IN_DELETE);
            $authorizeOutDelete = AuthorizationModel::isAuthorized(PERMISSION_BOOKING_OUT_DELETE);
            ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_IN_DELETE) || AuthorizationModel::isAuthorized(PERMISSION_BOOKING_OUT_EDIT)): ?>
                <li role="separator" class="action-delete divider" data-authorize-in="<?= $authorizeInDelete ?>" data-authorize-out="<?= $authorizeOutDelete ?>"></li>
                <li class="action-delete" data-authorize-in="<?= $authorizeInDelete ?>" data-authorize-out="<?= $authorizeOutDelete ?>">
                    <a href="<?= site_url('booking/delete/{{id}}') ?>" class="btn-delete" data-title="Booking" data-label="{{no_booking}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_IN_DELETE) || AuthorizationModel::isAuthorized(PERMISSION_BOOKING_OUT_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete') ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>
<?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_IN_VALIDATE) || AuthorizationModel::isAuthorized(PERMISSION_BOOKING_OUT_VALIDATE) || AuthorizationModel::isAuthorized(PERMISSION_BOOKING_STATUS_REVERT)): ?>
    <?php $this->load->view('template/_modal_validate') ?>
    <script src="<?= base_url('assets/app/js/validation.js') ?>" defer></script>
<?php endif; ?>
<?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_IN_EDIT_PAYMENT_STATUS) || AuthorizationModel::isAuthorized(PERMISSION_BOOKING_OUT_EDIT_PAYMENT_STATUS)): ?>
    <?php $this->load->view('booking/_modal_payout') ?>
<?php endif; ?>
<?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_CONTROL_REVERT)): ?>
    <?php $this->load->view('booking/_modal_payout_revert') ?>
<?php endif; ?>
<?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_IN_DELETE) || AuthorizationModel::isAuthorized(PERMISSION_BOOKING_OUT_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete') ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script src="<?= base_url('assets/app/js/booking.js?v=10') ?>" defer></script>