<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Space Usage Monitoring</h3>
        <div class="pull-right">
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <table class="table table-bordered table-striped responsive" id="table-over-space">
            <thead>
            <tr>
                <th rowspan="2" style="width: 30px">No</th>
                <th rowspan="2">Customer</th>
                <th colspan="3" class="text-center">Leased Storage (M<sup>2</sup>)</th>
                <th colspan="3" class="text-center">Usage Storage (M<sup>2</sup>)</th>
            </tr>
            <tr>
                <th>Warehouse</th>
                <th>Yard</th>
                <th>Covered Yard</th>
                <th>Warehouse Used</th>
                <th>Yard Used</th>
                <th>Covered Yard Used</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($customerStorages as $index => $customerStorage): ?>
                <tr>
                    <td><?= $index + 1 ?></td>
                    <td><?= $customerStorage['customer_name'] ?></td>
                    <td><?= numerical($customerStorage['warehouse_capacity'], 2, true) ?> M<sup>2</sup></td>
                    <td><?= numerical($customerStorage['yard_capacity'], 2, true) ?> M<sup>2</sup></td>
                    <td><?= numerical($customerStorage['covered_yard_capacity'], 2, true) ?> M<sup>2</sup></td>
                    <td>
                        <?php if($customerStorage['used_warehouse_storage'] <> 0): ?>
                            <a href="<?= site_url('storage-usage-report/space-usage-detail?warehouse_type=WAREHOUSE&customer=' . $customerStorage['id_customer']) ?>">
                        <?php endif; ?>

                            <?= numerical($customerStorage['used_warehouse_storage'], 2, true) ?>
                            <br>
                            (<?= numerical($customerStorage['used_warehouse_percent'], 1, true) ?>%)

                        <?php if($customerStorage['used_warehouse_storage'] > 0): ?>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($customerStorage['used_yard_storage'] <> 0): ?>
                            <a href="<?= site_url('storage-usage-report/space-usage-detail?warehouse_type=YARD&customer=' . $customerStorage['id_customer']) ?>">
                        <?php endif; ?>

                            <?= numerical($customerStorage['used_yard_storage'], 2, true) ?>
                            <br>
                            (<?= numerical($customerStorage['used_yard_percent'], 1, true) ?>%)

                        <?php if($customerStorage['used_yard_storage'] > 0): ?>
                            </a>
                        <?php endif; ?>
                    </td>
                    <td>
                        <?php if($customerStorage['used_covered_yard_storage'] <> 0): ?>
                            <a href="<?= site_url('storage-usage-report/space-usage-detail?warehouse_type=COVERED+YARD&customer=' . $customerStorage['id_customer']) ?>">
                        <?php endif; ?>

                            <?= numerical($customerStorage['used_covered_yard_storage'], 2, true) ?>
                            <br>
                            (<?= numerical($customerStorage['used_covered_yard_percent'], 1, true) ?>%)

                        <?php if($customerStorage['used_covered_yard_storage'] > 0): ?>
                            </a>
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <td></td>
                <td><strong>TOTAL</strong></td>
                <td><?= numerical(array_sum(array_column($customerStorages, 'warehouse_capacity')), 2, true) ?> M<sup>2</sup></td>
                <td><?= numerical(array_sum(array_column($customerStorages, 'yard_capacity')), 2, true) ?> M<sup>2</sup></td>
                <td><?= numerical(array_sum(array_column($customerStorages, 'covered_yard_capacity')), 2, true) ?> M<sup>2</sup></td>
                <td><?= numerical(array_sum(array_column($customerStorages, 'used_warehouse_storage')), 2, true) ?> M<sup>2</sup></td>
                <td><?= numerical(array_sum(array_column($customerStorages, 'used_yard_storage'))) ?> M<sup>2</sup></td>
                <td><?= numerical(array_sum(array_column($customerStorages, 'used_covered_yard_storage')), 2, true) ?> M<sup>2</sup></td>
            </tr>
            </tfoot>
        </table>
    </div>
</div>