<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Over Space Detail</h3>
    </div>
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Date Range</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= format_date($storageOverSpaceCustomer['date_from'], 'd F Y') ?> - <?= format_date($storageOverSpaceCustomer['date_to'], 'd F Y') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Customer Name</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $storageOverSpaceCustomer['customer_name'] ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Status</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php
                                $statuses = [
                                    StorageOverSpaceCustomerModel::STATUS_PENDING => 'default',
                                    StorageOverSpaceCustomerModel::STATUS_VALIDATED => 'success',
                                    StorageOverSpaceCustomerModel::STATUS_SKIPPED => 'danger',
                                ]
                                ?>
                                <span class="label label-<?= get_if_exist($statuses, $storageOverSpaceCustomer['status'], 'default') ?>">
                                    <?= $storageOverSpaceCustomer['status'] ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= format_date($storageOverSpaceCustomer['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Storage Activity</h3>
                <div class="pull-right">
                    <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                        Export Excel
                    </a>
                </div>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped responsive no-datatable" id="table-over-space">
                    <thead>
                    <tr>
                        <th rowspan="2" style="width: 30px">No</th>
                        <th rowspan="2">No Reference</th>
                        <th rowspan="2">Customer Name</th>
                        <th rowspan="2">Date Activity</th>
                        <th rowspan="2">Type</th>
                        <th rowspan="2">Qty</th>
                        <th rowspan="2" style="width: 140px">Unit</th>
                        <th rowspan="2">Container</th>
                        <th colspan="3" class="text-center">Left Storage (M<sup>2</sup>)</th>
                        <th rowspan="2" style="width: 60px">Used (M<sup>2</sup>)</th>
                    </tr>
                    <tr>
                        <th style="width: 60px">Inbound</th>
                        <th style="width: 60px">Outbound</th>
                        <th style="width: 60px">Balance</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1 ?>
                    <?php foreach ($storageOverSpaceActivities as $index => $itemStorage): ?>
                        <?php if($itemStorage['row_type'] == 'beginning-balance'): ?>
                            <tr class="success">
                                <td></td>
                                <td colspan="7">
                                    Beginning balance stock at <strong><?= $itemStorage['stock_date'] ?></strong>
                                    (Capacity <?= $itemStorage['capacity'] ?> M<sup>2</sup>)
                                </td>
                                <td><?= numerical($itemStorage['inbound_storage'], 2, true) ?></td>
                                <td><?= numerical($itemStorage['outbound_storage'], 2, true) ?></td>
                                <td><?= numerical($itemStorage['left_storage'], 2, true) ?></td>
                                <td><?= numerical($itemStorage['used_storage'], 2, true) ?></td>
                            </tr>
                        <?php elseif($itemStorage['row_type'] == 'transaction'): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td title="<?= $itemStorage['no_reference'] ?>">
                                    <?= mid_ellipsis($itemStorage['no_reference'], 6, 6) ?>
                                </td>
                                <td><?= $itemStorage['customer_name'] ?></td>
                                <td><?= $itemStorage['date_activity'] ?></td>
                                <td>
                                    <?= $itemStorage['activity_type'] ?><br>
                                    <a href="<?= site_url('work-order/view/' . $itemStorage['id_work_order']) ?>">
                                        <?= $itemStorage['handling_type'] ?>
                                    </a><br>
                                    <small class="text-muted"><?= $itemStorage['no_work_order'] ?></small>
                                </td>
                                <td><?= numerical($itemStorage['quantity'], 2, true) ?></td>
                                <td>
                                    <?= $itemStorage['unit'] ?><br>
                                    <small class="text-muted"><?= $itemStorage['goods_name'] ?></small>
                                </td>
                                <td>
                                    <?= if_empty($itemStorage['no_container'], 'LCL') ?><br>
                                    <small class="text-muted">
                                        Total Item <?= numerical($itemStorage['total_goods_loaded_quantity'], 2, true) ?>
                                    </small>
                                </td>
                                <td><?= numerical($itemStorage['inbound_storage'], 2, true) ?></td>
                                <td><?= numerical($itemStorage['outbound_storage'], 2, true) ?></td>
                                <td><?= numerical($itemStorage['left_storage'], 2, true) ?></td>
                                <td><strong><?= numerical($itemStorage['used_storage'], 2, true) ?></strong></td>
                            </tr>
                        <?php elseif($itemStorage['row_type'] == 'change-capacity'): ?>
                            <tr class="danger">
                                <td></td>
                                <td colspan="7">
                                    New capacity effective at <strong><?= $itemStorage['effective_date_capacity'] ?></strong>
                                    Capacity: <?= $itemStorage['capacity'] ?> M<sup>2</sup>
                                </td>
                                <td><?= numerical($itemStorage['inbound_storage'], 2, true) ?></td>
                                <td><?= numerical($itemStorage['outbound_storage'], 2, true) ?></td>
                                <td><?= numerical($itemStorage['left_storage'], 2, true) ?></td>
                                <td><?= numerical($itemStorage['used_storage'], 2, true) ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if(empty($storageOverSpaceActivities)): ?>
                        <tr>
                            <td colspan="12">No dedicated storage</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Status Histories</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 50px" class="text-center">No</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Created At</th>
                        <th>Created By</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($statusHistories as $index => $statusHistory): ?>
                        <tr>
                            <td class="text-center"><?= $index + 1 ?></td>
                            <td>
                                <span class="label label-<?= get_if_exist($statuses, $statusHistory['status'], 'default') ?>">
                                    <?= $statusHistory['status'] ?>
                                </span>
                            </td>
                            <td><?= if_empty($statusHistory['description'], '-') ?></td>
                            <td><?= format_date($statusHistory['created_at'], 'd F Y H:i') ?></td>
                            <td><?= if_empty($statusHistory['creator_name'], "No user") ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($statusHistories)): ?>
                        <tr>
                            <td colspan="5">No statuses available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>