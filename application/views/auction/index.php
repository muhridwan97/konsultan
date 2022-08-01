<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Auction Data</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_AUCTION_CREATE)): ?>
            <a href="<?= site_url('auction/create') ?>" class="btn btn-primary pull-right">
                Create Auction
            </a>
        <?php endif; ?>
    </div>
    <hr>
    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-auction">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>No Auction</th>
                <th>No Doc</th>
                <th class="type-date">Doc Date</th>
                <th class="type-date">Auction Date</th>
                <th class="type-status">Status</th>
                <th class="type-action" style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_AUCTION_VALIDATE)): ?>
    <?php $this->load->view('template/_modal_validate'); ?>
<?php endif; ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_AUCTION_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script id="control-auction-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_AUCTION_VIEW)): ?>
                <li>
                    <a href="<?= site_url('auction/view/{{id}}') ?>">
                        <i class="fa ion-search"></i> View
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_AUCTION_PRINT)): ?>
                <li>
                    <a href="<?= site_url('auction/print_auction/{{id}}?redirect=' . base_url(uri_string())) ?>">
                        <i class="fa fa-print"></i> Print
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_AUCTION_EDIT)): ?>
                <li class="edit">
                    <a href="<?= site_url('auction/edit/{{id}}') ?>">
                        <i class="fa ion-compose"></i> Edit
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_AUCTION_VALIDATE)): ?>
                <li>
                    <a href="<?= site_url('auction/validate_auction/approve/{{id}}') ?>"
                       class="btn-validate"
                       data-validate="approve"
                       data-label="{{no_auction}}">
                        <i class="fa ion-checkmark"></i> Approve
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('auction/validate_auction/reject/{{id}}') ?>"
                       class="btn-validate"
                       data-validate="reject"
                       data-label="{{no_auction}}">
                        <i class="fa ion-close"></i> Reject
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_AUCTION_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('auction/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-title="Auction"
                       data-label="{{no_auction}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<script src="<?= base_url('assets/app/js/validation.js') ?>" defer></script>
<script src="<?= base_url('assets/app/js/auction.js') ?>" defer></script>