<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            Item Hold History
        </h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>

        <div role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">No Reference</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $goods['no_reference'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Goods Name</label>
                        <div class="col-sm-8">
                            <p class="form-control-static" title="<?= $goods['no_goods'] ?>">
                                <?= $goods['goods_name'] ?>
                            </p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Unit</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= $goods['unit'] ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Ex No Container</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($goods['ex_no_container'], '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-bordered table-striped responsive">
            <thead>
            <tr class="text-nowrap">
                <th style="width: 30px">No</th>
                <th>Type</th>
                <th>No Reference</th>
                <th>Goods</th>
                <th>Unit</th>
                <th>Ex No Container</th>
                <th>Description</th>
                <th>Created At</th>
                <th>Created By</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $statuses = [
                TransporterEntryPermitRequestHoldModel::STATUS_HOLD => 'danger',
                TransporterEntryPermitRequestHoldModel::STATUS_RELEASED => 'success',
            ]
            ?>
            <?php foreach ($tepHoldItems as $index => $holdItem): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td>
                        <span class="label label-<?= get_if_exist($statuses, $holdItem['hold_type'], 'default') ?>">
                            <?= $holdItem['hold_type'] ?: 'HOLD' ?>
                        </span>
                    </td>
                    <td>
                        <?= $holdItem['no_reference'] ?><br>
                        <small class="text-muted"><?= $holdItem['no_hold_reference'] ?></small>
                    </td>
                    <td><?= $holdItem['goods_name'] ?></td>
                    <td><?= $holdItem['unit'] ?></td>
                    <td><?= if_empty($holdItem['ex_no_container'], '-') ?></td>
                    <td><?= if_empty($holdItem['description'], '-') ?></td>
                    <td><?= $holdItem['created_at'] ?></td>
                    <td><?= $holdItem['creator_name'] ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
    <div class="box-footer">
        <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary">Back</a>
        <div class="pull-right">
            <?php $currentData = $tepHoldItems[0] ?? [] ?>
            <?php if (($currentData['hold_status'] ?? get_url_param('hold_status')) == 'HOLD'): ?>
                <a href="<?= site_url('transporter-entry-permit-request-release/create') ?>?<?= $_SERVER['QUERY_STRING'] ?>" class="btn btn-success">
                    RELEASE CURRENT ITEM
                </a>
            <?php endif; ?>
            <?php if (($currentData['hold_status'] ?? get_url_param('hold_status')) == 'RELEASED'): ?>
                <a href="<?= site_url('transporter-entry-permit-request-hold/create') ?>?<?= $_SERVER['QUERY_STRING'] ?>" class="btn btn-danger">
                    HOLD CURRENT ITEM
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>