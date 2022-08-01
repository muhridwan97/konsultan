<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Request Release</h3>
    </div>
    <div class="box-body">
        <div role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">No Release Reference</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $tepRequestHold['no_hold_reference'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Customer Name</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $tepRequestHold['customer_name'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Request Type</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php
                                $statuses = [
                                    TransporterEntryPermitRequestHoldModel::STATUS_HOLD => 'danger',
                                    TransporterEntryPermitRequestHoldModel::STATUS_RELEASED => 'success',
                                ]
                                ?>
                                <span class="label label-<?= get_if_exist($statuses, $tepRequestHold['hold_type'], 'default') ?>">
                                    <?= $tepRequestHold['hold_type'] ?: 'HOLD' ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Status Summary</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php
                                $statuses = [
                                    TransporterEntryPermitRequestHoldModel::STATUS_HOLD => 'danger',
                                    TransporterEntryPermitRequestHoldModel::STATUS_PARTIAL_RELEASE => 'primary',
                                    TransporterEntryPermitRequestHoldModel::STATUS_RELEASED => 'success',
                                ]
                                ?>
                                <span class="label label-<?= get_if_exist($statuses, $tepRequestHold['hold_status'], 'default') ?>">
                                    <?= $tepRequestHold['hold_status'] ?: 'RELEASED' ?>
                                </span>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($tepRequestHold['description'], 'No description') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created By</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $tepRequestHold['creator_name'] ?: '-' ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= format_date($tepRequestHold['created_at'], 'd F Y H:i') ?: '-' ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Updated At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty(format_date($tepRequestHold['updated_at'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Released Items</h3>
            </div>
            <div class="box-body">
                <table class="table no-datatable responsive">
                    <thead>
                    <tr class="no-wrap">
                        <th style="width: 50px" class="text-center">No</th>
                        <th>Reference Hold</th>
                        <th>Goods Name</th>
                        <th>No Reference</th>
                        <th>Unit</th>
                        <th>No Ex Container</th>
                        <th>Description</th>
                        <th>Status</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($tepRequestHoldItems as $index => $item): ?>
                        <tr class="warning">
                            <td class="text-center"><?= $index + 1 ?></td>
                            <td class="text-nowrap">
                                <a href="<?= site_url('transporter-entry-permit-request-hold/view/' . $item['id_hold_reference_source']) ?>">
                                    <?= $item['no_hold_reference_source'] ?>
                                </a>
                            </td>
                            <td><?= $item['goods_name'] ?></td>
                            <td><?= $item['no_reference'] ?></td>
                            <td><?= $item['unit'] ?></td>
                            <td><?= if_empty($item['ex_no_container'], '-') ?></td>
                            <td><?= $item['description'] ?></td>
                            <td>
                                <span class="label label-<?= get_if_exist($statuses, $item['hold_status'], 'default') ?>">
                                    <?= $item['hold_status'] ?: 'RELEASED' ?>
                                </span>
                            </td>
                        </tr>
                        <?php if(!empty($item['request_upload_references'])): ?>
                            <tr>
                                <th></th>
                                <th>Affected Request</th>
                                <th>Created At</th>
                                <th colspan="2">Status</th>
                                <th colspan="2">Fleet</th>
                                <th>Requested</th>
                            </tr>
                            <?php foreach($item['request_upload_references'] as $uploadReference): ?>
                                <tr>
                                    <td></td>
                                    <td><?= $uploadReference['no_request'] ?></td>
                                    <td><?= $uploadReference['created_at'] ?></td>
                                    <td colspan="2"><?= $uploadReference['status_request'] ?></td>
                                    <td colspan="2"><?= $uploadReference['armada'] ?></td>
                                    <td><?= numerical($uploadReference['quantity'], 3, true) ?></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if(empty($tepRequestHoldItems)): ?>
                        <tr>
                            <td colspan="6">No hold goods available</td>
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