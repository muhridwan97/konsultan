<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">
            Current Hold Items
        </h3>
        <div class="pull-right">
            <a href="<?= site_url('transporter-entry-permit-request-release/create') ?>" class="btn btn-primary">
                Request Release
            </a>
        </div>
    </div>

    <div class="box-body">
        <?php $this->load->view('template/_alert') ?>
        <table class="table table-bordered table-striped responsive">
            <thead>
            <tr class="text-nowrap">
                <th style="width: 30px">No</th>
                <th>No Hold Ref</th>
                <th>Customer</th>
                <th>Goods</th>
                <th>Unit</th>
                <th>Ex No Container</th>
                <th>Status</th>
            </tr>
            </thead>
            <tbody>
            <?php
            $statuses = [
                TransporterEntryPermitRequestHoldModel::STATUS_HOLD => 'danger',
                TransporterEntryPermitRequestHoldModel::STATUS_RELEASED => 'success',
            ]
            ?>
            <?php foreach ($holdItems as $index => $holdItem): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td class="text-nowrap">
                        <a href="<?= site_url('transporter-entry-permit-request-hold/view/' . $holdItem['id_tep_hold']) ?>">
                            <?= $holdItem['no_hold_reference'] ?>
                        </a>
                    </td>
                    <td><?= $holdItem['customer_name'] ?></td>
                    <td><?= $holdItem['goods_name'] ?></td>
                    <td><?= $holdItem['unit'] ?></td>
                    <td><?= $holdItem['ex_no_container'] ?></td>
                    <td class="text-center">
                        <span class="label label-<?= get_if_exist($statuses, $holdItem['hold_type'], 'default') ?>">
                            <?= $holdItem['hold_type'] ?: 'HOLD' ?>
                        </span>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>