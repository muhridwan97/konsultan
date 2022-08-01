<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Component Transactions</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_COMPONENT_TRANSACTION_CREATE)): ?>
            <a href="<?= site_url('component_order/create') ?>" class="btn btn-primary pull-right">
                <i class="fa ion-plus-round"></i> Create Component Order
            </a>
        <?php endif; ?>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <table class="table table-bordered table-striped responsive" id="table-component-transaction">
            <thead>
            <tr>
                <th rowspan="2" style="width: 30px">No</th>
                <th rowspan="2">No Transaction</th>
                <th rowspan="2">Component</th>
                <th rowspan="2">Date</th>
                <th rowspan="2">Qty</th>
                <th rowspan="2">Amount</th>
                <th rowspan="2">Description</th>
                <td colspan="2" class="text-center"><strong>Status</strong></td>
                <th rowspan="2" style="width: 60px">Action</th>
            </tr>
            <tr>
                <th>Trans</th>
                <th>State</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1;
            foreach ($componentOrders as $componentOrder): ?>
                <tr>
                    <td class="responsive-hide"><?= $no++ ?></td>
                    <td class="responsive-title"><?= $componentOrder['no_transaction'] ?></td>
                    <td><?= $componentOrder['handling_component'] ?></td>
                    <td><?= readable_date($componentOrder['transaction_date'], false) ?></td>
                    <td><?= numerical($componentOrder['quantity'], 3, true) ?> <?= $componentOrder['option'] ?></td>
                    <td>Rp. <?= numerical($componentOrder['amount'], 0) ?></td>
                    <td><?= if_empty($componentOrder['description'], '-') ?></td>
                    <td>
                        <?php
                        $statuses = [
                            'DRAFT' => 'default',
                            'APPROVED' => 'success',
                            'REJECTED' => 'danger',
                        ]
                        ?>
                        <span class="label label-<?= $statuses[$componentOrder['status']] ?>">
                            <?= $componentOrder['status'] ?>
                        </span>
                    </td>
                    <td>
                        <?php
                        $states = [
                            '0' => 'success',
                            '1' => 'default',
                        ]
                        ?>
                        <span class="label label-<?= $states[$componentOrder['is_void']] ?>">
                            <?= $componentOrder['is_void'] ? 'VOID' : 'ACTIVE' ?>
                        </span>
                    </td>
                    <td>
                        <div class="btn-group">
                            <button type="button" class="btn btn-primary dropdown-toggle" data-toggle="dropdown"
                                    aria-haspopup="true" aria-expanded="false">
                                Action <span class="caret"></span>
                            </button>
                            <ul class="dropdown-menu dropdown-menu-right row-component-transaction"
                                data-id="<?= $componentOrder['id'] ?>"
                                data-label="<?= $componentOrder['no_transaction'] .' (' . $componentOrder['handling_component'] . ')' ?>">
                                <li class="dropdown-header">ACTION</li>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_VIEW)): ?>
                                    <li>
                                        <a href="<?= site_url('component_order/view/' . $componentOrder['id']) ?>">
                                            <i class="fa ion-search"></i> View
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_EDIT)): ?>
                                    <?php if ($componentOrder['status'] != 'APPROVED' && !$componentOrder['is_void']): ?>
                                        <li>
                                            <a href="<?= site_url('component_order/edit/' . $componentOrder['id']) ?>">
                                                <i class="fa ion-compose"></i> Edit
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_COMPONENT_TRANSACTION_VALIDATE)): ?>
                                    <?php if($componentOrder['status'] != 'APPROVED' && !$componentOrder['is_void']): ?>
                                        <li>
                                            <a href="<?= site_url('component_order/validate/' . $componentOrder['id']) ?>"
                                                class="btn-validate-component-transaction">
                                                <i class="fa ion-checkmark"></i> Validate
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                <?php endif; ?>

                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_COMPONENT_TRANSACTION_DELETE)): ?>
                                    <li role="separator" class="divider"></li>
                                    <?php if (!$componentOrder['is_void']): ?>
                                        <li>
                                            <a href="<?= site_url('component_order/void/' . $componentOrder['id']) ?>"
                                               class="btn-void-component-transaction">
                                                <i class="fa ion-close"></i> Void
                                            </a>
                                        </li>
                                    <?php endif; ?>
                                    <li>
                                        <a href="<?= site_url('component_order/delete/' . $componentOrder['id']) ?>"
                                           class="btn-delete-component-transaction">
                                            <i class="fa ion-trash-a"></i> Delete
                                        </a>
                                    </li>
                                <?php endif; ?>

                            </ul>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th>No</th>
                <th>No Transaction</th>
                <th>Component</th>
                <th>Date</th>
                <th>Qty</th>
                <th>Amount</th>
                <th>Description</th>
                <th>Trans</th>
                <th>State</th>
                <th>Action</th>
            </tr>
            </tfoot>
        </table>
    </div>
    <!-- /.box-body -->
</div>
<!-- /.box -->

<?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_COMPONENT_TRANSACTION_VALIDATE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-validate-component-transaction">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Component Order Validation</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Validate component order
                            <strong id="payment-title"></strong>?
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Cancel</button>
                        <button type="submit" class="btn btn-danger" name="status" value="REJECTED">Reject</button>
                        <button type="submit" class="btn btn-success" name="status" value="APPROVED">Approve</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php endif; ?>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_COMPONENT_TRANSACTION_DELETE)): ?>
    <div class="modal fade" tabindex="-1" role="dialog" id="modal-delete-component-transaction">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Delete Order Transaction</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Are you sure want to delete component transaction
                            <strong id="component-transaction-title"></strong>?
                        </p>
                        <p class="text-danger">
                            This action will perform soft delete, actual data still exist on database.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-danger">Delete Component Order</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->

    <div class="modal fade" tabindex="-1" role="dialog" id="modal-void-component-transaction">
        <div class="modal-dialog" role="document">
            <div class="modal-content">
                <form action="#" method="post">
                    <input type="hidden" name="id" id="id">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                            <span aria-hidden="true">&times;</span>
                        </button>
                        <h4 class="modal-title">Void Component Order</h4>
                    </div>
                    <div class="modal-body">
                        <p class="lead" style="margin-bottom: 0">Are you sure want to void (deactivate) component transaction
                            <strong id="component-transaction-title"></strong>?
                        </p>
                        <p class="text-danger">
                            This order transaction will be frozen and unavailable right now.
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn btn-warning">Void Component Order</button>
                    </div>
                </form>
            </div><!-- /.modal-content -->
        </div><!-- /.modal-dialog -->
    </div><!-- /.modal -->
<?php endif; ?>

<script src="<?= base_url('assets/app/js/handling_component_transaction.js') ?>" defer></script>
