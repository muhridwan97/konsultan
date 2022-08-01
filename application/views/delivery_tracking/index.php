<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Delivery Tracking Data</h3>
        <div class="pull-right">
            <a href="#form-filter" class="btn btn-info btn-filter-toggle">
                <?= get_url_param('filter', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                <i class="fa fa-file-excel-o"></i>
            </a>
            <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_TRACKING_CREATE)): ?>
                <a href="<?= site_url('delivery-tracking/create') ?>" class="btn btn-primary">
                    Create Delivery
                </a>
            <?php endif; ?>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <?php $this->load->view('delivery_tracking/_filter', ['hidden' => isset($_GET['filter']) ? false : true]) ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-delivery-tracking">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>No Delivery Tracking</th>
                <th>Customer</th>
                <th class="type-assignment">Assigned To</th>
                <th>Total State</th>
                <th class="type-status">Status</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_TRACKING_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script id="control-delivery-tracking-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_TRACKING_VIEW)): ?>
                <li>
                    <a href="<?= site_url('delivery-tracking/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_TRACKING_PRINT)): ?>
                <li>
                    <a href="<?= site_url('delivery-tracking/print-delivery-tracking/{{id}}') ?>">
                        <i class="fa fa-print"></i> Print
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_TRACKING_EDIT)): ?>
                <li class="edit">
                    <a href="<?= site_url('delivery-tracking/edit/{{id}}') ?>">
                        <i class="fa ion-compose"></i> Edit
                    </a>
                </li>
                <li role="separator" class="divider edit"></li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_TRACKING_CREATE)): ?>
                <li class="edit">
                    <a href="<?= site_url('delivery-tracking/add-assignment-message/{{id}}') ?>">
                        <i class="fa fa-sticky-note-o"></i> Add Assignment Message
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_TRACKING_ADD_STATE)): ?>
                <li role="separator" class="divider edit"></li>
                <li class="edit">
                    <a href="<?= site_url('delivery-tracking/add-safe-conduct/{{id}}') ?>">
                        <i class="fa ion-checkmark-circled"></i> Set Safe Conduct
                    </a>
                </li>
                <li class="edit">
                    <a href="<?= site_url('delivery-tracking/add-delivery-state/{{id}}') ?>">
                        <i class="fa ion-plus-round"></i> Add Delivery State
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_TRACKING_CLOSE)): ?>
                <li class="edit">
                    <a href="<?= site_url('delivery-tracking/close/{{id}}') ?>" class="btn-validate" data-validate="set delivered" data-label="{{no_delivery_tracking}}">
                        <i class="fa ion-close"></i> Close Delivery
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_TRACKING_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('delivery-tracking/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-title="Auction"
                       data-label="{{no_delivery_tracking}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_DELIVERY_TRACKING_CLOSE)): ?>
    <?php $this->load->view('template/_modal_validate') ?>
    <script src="<?= base_url('assets/app/js/validation.js') ?>" defer></script>
<?php endif; ?>
<script src="<?= base_url('assets/app/js/delivery-tracking.js') ?>" defer></script>