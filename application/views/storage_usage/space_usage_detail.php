<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Containerized & LCL Detail</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped responsive">
            <thead>
            <tr>
                <th style="width: 30px">No</th>
                <th>Type</th>
                <th>Goods Name</th>
                <th>Unit</th>
                <th>Container</th>
                <th class="text-right" style="width: 150px">Usage Storage (M<sup>2</sup>)</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($customerStorageDetails as $index => $storageUsage): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $storageUsage['warehouse_type'] ?></td>
                    <td>
                        <?= $storageUsage['goods_name'] ?><br>
                        <small class="text-muted"><?= $storageUsage['no_reference'] ?></small>
                    </td>
                    <td><?= $storageUsage['unit'] ?></td>
                    <td>
                        <?= $storageUsage['ex_no_container'] ?>
                        <?= $storageUsage['ex_no_container'] != 'LCL' && $storageUsage['is_lcl'] ? '(LCL)' : '' ?>
                    </td>
                    <td class="text-right">
                        <a href="<?= site_url("storage-usage-report/space-usage-detail-activity?warehouse_type={$storageUsage['warehouse_type']}&customer={$storageUsage['id_customer']}&booking={$storageUsage['id_booking']}&ex_no_container={$storageUsage['ex_no_container']}&is_lcl={$storageUsage['is_lcl']}&goods={$storageUsage['id_goods']}&unit={$storageUsage['id_unit']}") ?>">
                            <?= numerical($storageUsage['used_storage'], 3, true) ?>
                        </a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <td></td>
                <td colspan="4"><strong>TOTAL</strong></td>
                <td class="text-right">
                    <?= numerical(array_sum(array_column($customerStorageDetails, 'used_storage')), 3, true) ?> M<sup>2</sup>
                </td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>