<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Heavy Equipment Entry Permit</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_CREATE)): ?>
            <a href="<?= site_url('heavy-equipment-entry-permit/create') ?>" class="btn btn-primary pull-right">
                Create HEEP
            </a>
        <?php endif; ?>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-heep">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <!-- <th class="type-customer">Customer</th> -->
                <th>NO HEEP</th>
                <th>Code</th>
                <th class="type-date-time">Check In At</th>
                <th class="type-checkin-by">Check In By</th>
                <th class="type-date-time">Check Out At</th>
                <th class="type-checkout-by">Check Out By</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script id="control-heep-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_VIEW)): ?>
                <li>
                    <a href="<?= site_url('heavy-equipment-entry-permit/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_PRINT)): ?>
                <li>
                    <a href="<?= site_url('heavy-equipment-entry-permit/print/{{id}}') ?>">
                        <i class="fa fa-print"></i> Print
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_TEP_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('heavy-equipment-entry-permit/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-title="Heavy Equipment Entry Permit"
                       data-label="{{code}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/heep.js?v=0') ?>" defer></script>