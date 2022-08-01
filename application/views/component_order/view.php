<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Transaction</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_HANDLING_COMPONENT_EDIT)): ?>
            <?php if ($componentOrder['status'] != 'APPROVED' && !$componentOrder['is_void']): ?>
                <a href="<?= site_url('component_order/edit/' . $componentOrder['id']) ?>"
                   class="btn btn-primary pull-right">
                    Edit Component Transaction
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">No Transaction</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $componentOrder['no_transaction'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Branch</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $componentOrder['branch'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Handling Component</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $componentOrder['handling_component'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Component Category</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $componentOrder['component_category'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Payment Date</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($componentOrder['transaction_date']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Quantity</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= numerical($componentOrder['quantity'], 0, true) ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Amount</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">Rp. <?= numerical($componentOrder['amount'], 0, true) ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Options</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($componentOrder['option'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($componentOrder['description'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Attachment</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php if (empty($componentOrder['attachment'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="<?= base_url('uploads/component_orders/' . $componentOrder['attachment']) ?>">
                                        Download Attachment
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Void</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php
                                $states = [
                                    '0' => 'success',
                                    '1' => 'default',
                                ]
                                ?>
                                <span class="label label-<?= $states[$componentOrder['is_void']] ?>">
                            <?= $componentOrder['is_void'] ? 'VOID' : 'ACTIVE' ?>
                        </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Status</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
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
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($componentOrder['created_at']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Updated At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($componentOrder['updated_at']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
    <div class="box-footer">
        <a href="<?= site_url('component_order') ?>" class="btn btn-primary">
            Back to Component Transaction List
        </a>
    </div>
</div>