<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Invoices</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_INVOICE_CREATE)): ?>
            <a href="<?= site_url('invoice/create') ?>" class="btn btn-primary pull-right">
                Create Invoice
            </a>
        <?php endif ?>
    </div>

    <div class="box-body">

        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive table-ajax" id="table-invoice">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Customer</th>
                <th>No Invoice</th>
                <th>No Ref</th>
                <th>Date</th>
                <th>Total</th>
                <th>Type</th>
                <th>Status</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>


<?php $this->load->view('invoice/_modal_print_invoice') ?>
<?php $this->load->view('invoice/_modal_publish_invoice') ?>
<?php $this->load->view('invoice/_modal_cancel_invoice') ?>
<?php $this->load->view('invoice/_modal_delete_invoice') ?>
<?php $this->load->view('invoice/_modal_upload_faktur') ?>
<?php $this->load->view('invoice/_modal_pay_invoice') ?>

<script id="control-invoice-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right row-invoice"
            data-id="{{id}}"
            data-is-performa="{{is_performa}}"
            data-label="{{no_invoice}}">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_INVOICE_VIEW)): ?>
                <li>
                    <a href="<?= site_url('invoice/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_INVOICE_CREATE)): ?>
                <li class="upload-faktur">
                    <a href="<?= site_url('invoice/upload_faktur/{{id}}') ?>"
                       class="btn-upload-faktur" data-no-faktur="{{no_faktur}}" data-attachment-faktur="{{attachment_faktur}}">
                        <i class="fa ion-upload"></i> {{label_upload_faktur}}
                    </a>
                </li>
                <li class="update-payment">
                    <a href="<?= site_url('invoice/payment/{{id}}') ?>"
                       class="btn-pay-invoice" data-payment-date="{{payment_date}}"
                       data-transfer-bank="{{transfer_bank}}"
                       data-transfer-amount="{{transfer_amount}}"
                       data-cash-amount="{{cash_amount}}"
                       data-over-payment-amount="{{over_payment_amount}}"
                       data-payment-description="{{payment_description}}">
                        <i class="fa ion-cash"></i> {{payment_update_payment}}
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_INVOICE_PRINT)): ?>
                <li>
                    <a href="<?= site_url('invoice/print_invoice_pdf/{{id}}') ?>" class="btn-print-invoice">
                        <i class="fa fa-print"></i> Print Invoice PDF
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('invoice/print_receipt/{{id}}') ?>">
                        <i class="fa fa-print"></i> Print Receipt
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('invoice/print_invoice/{{id}}') ?>">
                        <i class="fa fa-print"></i> Print Invoice Raw
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_INVOICE_VALIDATE)): ?>
                <li class="publish-invoice">
                    <a href="<?= site_url('invoice/publish/{{id}}') ?>" class="btn-publish-invoice">
                        <i class="fa ion-checkmark"></i>Publish Invoice
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_INVOICE_DELETE)): ?>
                <li class="cancel-invoice">
                    <a href="<?= site_url('invoice/cancel/{{id}}') ?>" class="btn-cancel-invoice">
                        <i class="fa ion-close"></i>Cancel Invoice
                    </a>
                </li>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('invoice/delete/{{id}}') ?>"
                       class="btn-delete-invoice">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>

        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/invoice.js?v=3') ?>" defer></script>