<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Entry Permit Data</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_CREATE)): ?>
            <a href="<?= site_url('transporter-entry-permit/create') ?>" class="btn btn-primary pull-right">
                Create TEP
            </a>
        <?php endif; ?>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>
        <input type="hidden" name="id_user" id="id_user" value="<?= UserModel::authenticatedUserData('id') ?>">
        <table class="table table-bordered table-striped table-ajax responsive" id="table-tep">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th class="type-customer">Customer</th>
                <th class="type-booking">Booking</th>
                <th>TEP Category</th>
                <th class="type-code">Code</th>
                <th class="type-checkin-by">Check In</th>
                <th class="type-checkout-by">Check Out</th>
                <th class="type-carrier">Carrier</th>
                <th class="type-safe-conduct">Safe Conduct</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php $this->load->view('transporter_entry_permit/_modal_cancel'); ?>
<?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script id="control-tep-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_VIEW)): ?>
                <li>
                    <a href="<?= site_url('transporter-entry-permit/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View
                    </a>
                </li>
            <?php endif; ?>
            <?php if (get_active_branch_id() == 8 && AuthorizationModel::isAuthorized(PERMISSION_TEP_EDIT)): ?>
                <li>
                    <a href="<?= site_url('linked-entry-permit/update-linked-tep/{{id}}') ?>">
                        <i class="fa fa-refresh"></i> Update Linked TEP
                    </a>
                </li>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_EDIT_SECURITY)): ?>
                <li>
                    <a href="<?= site_url('transporter-entry-permit/edit-tep/{{id}}') ?>">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                </li>
            <?php else: ?>
                <li class="{{canEdit}}">
                    <a href="<?= site_url('transporter-entry-permit/edit-tep/{{id}}') ?>">
                        <i class="fa fa-edit"></i> Edit
                    </a>
                </li>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_PRINT)): ?>
                <li>
                    <a href="<?= site_url('transporter-entry-permit/print/{{id}}') ?>">
                        <i class="fa fa-print"></i> Print
                    </a>
                </li>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_DELETE)): ?>
                <li>
                    <a href="#"
                        data-url = "<?= site_url('transporter-entry-permit/cancel_tep/{{id}}') ?>"
                       class="btn-cancel {{cancel}}"
                       data-title="Transporter Entry Permit"
                       data-label="{{code}}">
                        <i class="fa fa-close"></i> Cancel
                    </a>
                </li>
            <?php endif; ?>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('transporter-entry-permit/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-title="Transporter Entry Permit"
                       data-label="{{code}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/tep.js?v=8') ?>" defer></script>