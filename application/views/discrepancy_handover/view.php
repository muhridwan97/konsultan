<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Discrepancy Handover</h3>
    </div>
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">No Discrepancy</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $discrepancyHandover['no_discrepancy'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Customer</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($discrepancyHandover['customer_name'], 'No customer') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Reference</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= $discrepancyHandover['no_reference'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Attachment</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if (empty($discrepancyHandover['attachment'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="<?= asset_url($discrepancyHandover['attachment']) ?>">
                                        <?= basename($discrepancyHandover['attachment']) ?>
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($discrepancyHandover['description'], 'No description') ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Explanation</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($discrepancyHandover['explanation'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Explanation File</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if (empty($discrepancyHandover['explanation_attachment'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="<?= asset_url($discrepancyHandover['explanation_attachment']) ?>">
                                        <?= basename($discrepancyHandover['explanation_attachment']) ?>
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Status</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php
                                $statuses = [
                                    DiscrepancyHandoverModel::STATUS_PENDING => 'default',
                                    DiscrepancyHandoverModel::STATUS_UPLOADED => 'primary',
                                    DiscrepancyHandoverModel::STATUS_CONFIRMED => 'success',
                                    DiscrepancyHandoverModel::STATUS_EXPLAINED => 'success',
                                    DiscrepancyHandoverModel::STATUS_CANCELED => 'danger',
                                    DiscrepancyHandoverModel::STATUS_IN_USE => 'info',
                                    DiscrepancyHandoverModel::STATUS_NOT_USE => 'danger',
                                    DiscrepancyHandoverModel::STATUS_DOCUMENT => 'primary',
                                    DiscrepancyHandoverModel::STATUS_PHYSICAL => 'success',
                                ]
                                ?>
                                <span class="label label-<?= get_if_exist($statuses, $discrepancyHandover['status'], 'default') ?>">
                                    <?= $discrepancyHandover['status'] ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= format_date($discrepancyHandover['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Updated At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(format_date($discrepancyHandover['updated_at'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Discrepancy Goods</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>Source</th>
                        <th>Type</th>
                        <th>Goods</th>
                        <th>Unit</th>
                        <th>Qty Booking</th>
                        <th>Qty Stock</th>
                        <th>Qty Diff</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($discrepancyHandoverGoods as $discrepancyHandoverItem): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $discrepancyHandoverItem['source'] ?></td>
                            <td><?= $discrepancyHandoverItem['assembly_type'] ?></td>
                            <td>
                                <?= $discrepancyHandoverItem['goods_name'] ?><br>
                                <small class="text-muted"><?= $discrepancyHandoverItem['no_goods'] ?></small>
                            </td>
                            <td><?= $discrepancyHandoverItem['unit'] ?></td>
                            <td><?= numerical($discrepancyHandoverItem['quantity_booking'], 2, true) ?></td>
                            <td><?= numerical($discrepancyHandoverItem['quantity_stock'], 2, true) ?></td>
                            <td><?= numerical($discrepancyHandoverItem['quantity_difference'], 2, true) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($discrepancyHandoverGoods)): ?>
                        <tr>
                            <td colspan="8">No discrepancy goods available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Status History</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Created By</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($statusHistories as $status): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <span class="label label-<?= get_if_exist($statuses, $status['status'], 'default') ?>">
                                    <?= $status['status'] ?>
                                </span>
                            </td>
                            <td><?= if_empty($status['description'], '-') ?></td>
                            <td><?= format_date($status['created_at'], 'd F Y H:i') ?></td>
                            <td><?= $status['creator_name'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
        <div class="pull-right">
            <?php if ($discrepancyHandover['status'] == DiscrepancyHandoverModel::STATUS_PENDING && AuthorizationModel::isAuthorized(PERMISSION_DISCREPANCY_HANDOVER_VALIDATE)): ?>
                <a href="<?= site_url('discrepancy-handover/not-use/' . $discrepancyHandover['id']) ?>" class="btn btn-danger btn-validate"
                   data-validate="not use"
                   data-label="<?= $discrepancyHandover['no_discrepancy'] ?>"
                   data-theme="danger">
                    <i class="fa ion-close-circled mr5"></i> Not Use
                </a>
                <a href="<?= site_url('discrepancy-handover/in-use/' . $discrepancyHandover['id']) ?>" class="btn btn-success btn-validate"
                   data-validate="in use"
                   data-label="<?= $discrepancyHandover['no_discrepancy'] ?>"
                   data-theme="success">
                    <i class="fa ion-checkmark-circled mr5"></i> In Use
                </a>
            <?php endif ?>
            <?php if (in_array($discrepancyHandover['status'], [DiscrepancyHandoverModel::STATUS_EXPLAINED, DiscrepancyHandoverModel::STATUS_CONFIRMED]) && AuthorizationModel::isAuthorized(PERMISSION_DISCREPANCY_HANDOVER_PROCEED)): ?>
                <a href="<?= site_url('discrepancy-handover/not-use/' . $discrepancyHandover['id']) ?>" class="btn btn-info btn-validate"
                   data-validate="document"
                   data-label="<?= $discrepancyHandover['no_discrepancy'] ?>"
                   data-theme="danger">
                    <i class="fa fa-file-o mr5"></i> Set Document
                </a>
                <a href="<?= site_url('discrepancy-handover/in-use/' . $discrepancyHandover['id']) ?>" class="btn btn-success btn-validate"
                   data-validate="physical"
                   data-label="<?= $discrepancyHandover['no_discrepancy'] ?>"
                   data-theme="success">
                    <i class="fa fa-cube mr5"></i> Set Physical
                </a>
            <?php endif ?>
            <a href="<?= site_url('discrepancy-handover/print-discrepancy-handover/' . $discrepancyHandover['id']) ?>" class="btn btn-primary">
                Print
            </a>
        </div>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized([PERMISSION_DISCREPANCY_HANDOVER_PROCEED, PERMISSION_DISCREPANCY_HANDOVER_VALIDATE, PERMISSION_DISCREPANCY_HANDOVER_DELETE])): ?>
    <?php $this->load->view('template/_modal_validate') ?>
    <script src="<?= base_url('assets/app/js/validation.js') ?>" defer></script>
<?php endif; ?>