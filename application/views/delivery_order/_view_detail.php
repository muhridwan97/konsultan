<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Goods</h3>
    </div>

    <div class="box-body">
        <table class="table table-bordered table-striped no-datatable">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Goods</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Tonnage (Kg)</th>
                <th>Volume (M<sup>3</sup>)</th>
                <th>Is Hold</th>
                <th>Status</th>
                <th>Danger</th>
                <th>Description</th>
            </tr>
            </thead>
            <tbody>
            <?php $no = 1; ?>
            <?php foreach ($deliveryOrderGoods as $deliveryOrderGood) : ?>
                <tr>
                    <td><?= $no++ ?></td>
                    <td><?= $deliveryOrderGood['goods_name'] ?></td>
                    <td><?= numerical($deliveryOrderGood['quantity']) ?></td>
                    <td><?= $deliveryOrderGood['unit'] ?></td>
                    <td><?= numerical($deliveryOrderGood['tonnage']) ?></td>
                    <td><?= numerical($deliveryOrderGood['volume']) ?></td>
                    <td class="<?= $deliveryOrderGood['is_hold'] ? 'bg-red' :'' ?>">
                        <?= $deliveryOrderGood['is_hold'] ? 'Yes' : 'No' ?>
                    </td>
                    <td><?= if_empty($deliveryOrderGood['status'], 'No status') ?></td>
                    <td class="<?= $deliveryOrderGood['status_danger'] != 'NOT DANGER' ? 'bg-red' :'' ?>">
                        <?= $deliveryOrderGood['status_danger'] ?>
                    </td>
                    <td><?= if_empty($deliveryOrderGood['description'], 'No description') ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>