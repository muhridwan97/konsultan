<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">CIF Invoice Data</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_CIF_INVOICE_CREATE)): ?>
            <a href="<?= site_url('booking-cif-invoice/create') ?>" class="btn btn-primary pull-right">
                Create Invoice
            </a>
        <?php endif; ?>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-booking-cif">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Customer</th>
                <th>No Reference</th>
                <th>Category</th>
                <th class="type-total-item">Total Item</th>
                <th class="type-currency">Total Price</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_CIF_INVOICE_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script id="control-booking-cif-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_CIF_INVOICE_VIEW)): ?>
                <li>
                    <a href="<?= site_url('booking/cif/{{id_booking}}') ?>">
                        <i class="fa ion-search"></i> View
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_CIF_INVOICE_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('booking-cif-invoice/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-title="CIF Booking"
                       data-label="{{no_reference}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/booking-cif-invoice.js') ?>" defer></script>