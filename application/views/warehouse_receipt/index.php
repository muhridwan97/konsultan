<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Warehouse Receipt</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_WAREHOUSE_RECEIPT_CREATE)): ?>
            <div class="pull-right">
                <a href="<?= site_url('warehouse_receipt/create') ?>" class="btn btn-primary">
                    Create Warehouse Receipt
                </a>
            </div>
        <?php endif; ?>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped table-ajax responsive" id="table-warehouse-receipt">
            <thead>
            <tr>
                <th>No</th>
                <th>No Warehouse Receipt</th>
                <th>No Batch</th>
                <th>Customer</th>
                <th>Issuance Date</th>
                <th>Duration</th>
                <th>Total Ton</th>
                <th>Status</th>
                <th style="width: 60px">Action</th>
            </tr>
            </thead>
            <tfoot>
            <tr>
                <th>No</th>
                <th>No Warehouse Receipt</th>
                <th>No Batch</th>
                <th>Customer</th>
                <th>Issuance Date</th>
                <th>Duration</th>
                <th>Total Ton</th>
                <th>Status</th>
                <th>Action</th>
            </tr>
            </tfoot>
        </table>
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->


<script id="control-warehouse-receipt-template" type="text/x-custom-template">
    <div class="btn-group">
        <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                aria-haspopup="true" aria-expanded="false">
            Action <span class="caret"></span>
        </button>
        <ul class="dropdown-menu dropdown-menu-right">
            <li class="dropdown-header">ACTION</li>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WAREHOUSE_RECEIPT_VIEW)): ?>
                <li>
                    <a href="<?= site_url('warehouse_receipt/view/{{id}}') ?>">
                        <i class="fa ion-search"></i>View Detail
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WAREHOUSE_RECEIPT_PRINT)): ?>
                <li>
                    <a href="<?= site_url('warehouse_receipt/print_warehouse_receipt/{{id}}') ?>">
                        <i class="fa fa-print"></i>Print
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WAREHOUSE_RECEIPT_VALIDATE)): ?>
                <li class="validate">
                    <a href="<?= site_url('warehouse_receipt/validate_warehouse_receipt/{{id}}') ?>"
                       class="btn-validate-warehouse-receipt"
                       data-id="{{id}}"
                       data-label="{{no_warehouse_receipt}}">
                        <i class="fa ion-checkmark"></i>Validate
                    </a>
                </li>
            <?php endif; ?>

            <?php if (AuthorizationModel::isAuthorized(PERMISSION_WAREHOUSE_RECEIPT_DELETE)): ?>
                <li role="separator" class="divider"></li>
                <li>
                    <a href="<?= site_url('warehouse_receipt/delete/{{id}}') ?>"
                       class="btn-delete"
                       data-title="Warehouse receipt"
                       data-label="{{no_warehouse_receipt}}">
                        <i class="fa ion-trash-a"></i> Delete
                    </a>
                </li>
            <?php endif; ?>
        </ul>
    </div>
</script>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_WAREHOUSE_RECEIPT_DELETE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-delete-warehouse-receipt">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Delete Warehouse Receipt</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Are you sure want to delete warehouse receipt
                            <strong id="warehouse-receipt-title"></strong>?
                        </p>
                        <p class="text-danger">
                            This action will perform soft delete, actual data still exist on database.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete Warehouse Receipt</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php endif; ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_WAREHOUSE_RECEIPT_VALIDATE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-validate-warehouse-receipt">
        <div class="modal-dialog">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Validating Warehouse Receipt</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Validate warehouse receipt no
                            <strong id="warehouse-receipt-title"></strong>?
                        </p>
                        <div class="form-group">
                            <label for="description" class="control-label">Description</label>
                            <textarea class="form-control" name="description" id="description" rows="2" placeholder="Validation message"></textarea>
                            <span class="help-block">This message will be included in email to customer</span>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" name="status" value="REJECTED">Reject</button>
                        <button type="submit" class="btn btn-success" name="status" value="APPROVED">Approve and Set Other to Expired</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php endif; ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_AUCTION_DELETE)): ?>
    <?php $this->load->view('template/_modal_delete'); ?>
    <script src="<?= base_url('assets/app/js/delete.js') ?>" defer></script>
<?php endif; ?>

<script src="<?= base_url('assets/app/js/warehouse_receipt.js?v=2') ?>" defer></script>