<?php if(!empty($bookingContainers)): ?>
    <p class="lead mb10 mt20">Containers</p>
    <table class="table">
        <thead>
        <tr>
            <th>No Container</th>
            <th>Type</th>
            <th>Size</th>
            <th>Total Pallet</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($bookingContainers as $container): ?>
            <tr>
                <td><?= $container['no_container'] ?></td>
                <td><?= $container['type'] ?></td>
                <td><?= $container['size'] ?></td>
                <td>
                    <input type="hidden" name="description[]" value="<?= $container['no_container'] ?>">
                    <input type="number" value="1" min="0" name="total[]" class="form-control">
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if(!empty($bookingGoods)): ?>
    <p class="lead mb10 mt20">Goods</p>
    <table class="table">
        <thead>
        <tr>
            <th>No Goods</th>
            <th>Goods Name</th>
            <th>Total Pallet</th>
        </tr>
        </thead>
        <tbody>
        <?php foreach ($bookingGoods as $goods): ?>
            <tr>
                <td><?= $goods['no_goods'] ?></td>
                <td><?= $goods['goods_name'] ?></td>
                <td>
                    <input type="hidden" name="description[]" value="<?= $goods['goods_name'] ?>">
                    <input type="number" value="1" min="0" name="total[]" class="form-control">
                </td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
<?php endif; ?>