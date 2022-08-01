<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Raw Contact</h3>
        <div class="pull-right">
            <a href="<?= site_url('raw_contact/export') ?>" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-raw-contact">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Company</th>
                <th>PIC</th>
                <th>Address</th>
                <th>Contact</th>
                <th>Email</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php $this->load->view('template/_modal_delete'); ?>

<script id="control-raw-contact-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <li>
                <a href="<?= site_url('raw_contact/view/{{id}}') ?>">
                    <i class="fa ion-search"></i>View Contact
                </a>
            </li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_INVOICE_VIEW)): ?>
                <li>
                    <a href="<?= site_url('raw_contact/invoice/{{id}}') ?>">
                        <i class="fa ion-document"></i> View Invoice
                    </a>
                </li>
            <?php endif; ?>

            <li role="separator" class="divider"></li>

            <li>
                <a href="<?= site_url('raw_contact/delete/{{id}}') ?>"
                   class="btn-delete"
                   data-title="Auction"
                   data-label="{{company}}">
                    <i class="fa ion-trash-a"></i> Delete
                </a>
            </li>

        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<script src="<?= base_url('assets/app/js/raw_contact.js') ?>" defer></script>