<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Pallet Marking</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_PALLET_CREATE)): ?>
            <div class="pull-right">
                <a href="<?= site_url('pallet/create') ?>" class="btn btn-primary">
                    <i class="fa ion-plus-round"></i> Create Pallet
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-pallet">
            <thead>
            <tr>
                <th>No</th>
                <th>No Pallet</th>
                <th>Batch</th>
                <th>Description</th>
                <th>Related Booking</th>
                <th>Generated At</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
        </table>
    </div>
</div>

<script id="control-pallet-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PALLET_VIEW)): ?>
                <li>
                    <a href="<?= site_url('pallet/view/{{id}}') ?>">
                        <i class="fa ion-search"></i>View Detail
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PALLET_PRINT)): ?>
                <li>
                    <a href="<?= site_url('pallet/print-pallet/{{id}}') ?>">
                        <i class="fa fa-print"></i> Print Pallet
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_PALLET_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('pallet/delete/{{id}}') ?>"
                       class="btn-delete-pallet"
                       data-id="{{id}}"
                       data-label="{{no_pallet}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
                <li>
                    <a href="<?= site_url('pallet/delete/{{id}}') ?>"
                       class="btn-delete-pallet-batch"
                       data-id="{{batch}}"
                       data-label="{{batch_label}}">
                        <i class="fa ion-trash-a"></i> Delete Batch
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_PALLET_DELETE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-delete-pallet">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <input type="hidden" name="batch" id="batch">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Delete Pallet</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Are you sure want to delete booking
                            <strong id="pallet-title"></strong>?
                        </p>
                        <p class="text-danger">
                            This action will perform soft delete, actual data still exist on database.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete Pallet</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
<?php endif; ?>

<script src="<?= base_url('assets/app/js/pallet.js') ?>" defer></script>