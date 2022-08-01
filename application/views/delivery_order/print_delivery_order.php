<div class="row">
    <div class="col-sm-7 col-xs-8">
        <p class="lead" style="margin-bottom: 10px;">
            <strong>DELIVERY ORDER</strong>
        </p>
        <p class="lead" style="margin-bottom: 0">
            No Delivery Order: <?= $deliveryOrder['no_delivery_order'] ?>
        </p>
        <p>
            No Booking: <?= if_empty($deliveryOrder['no_booking'], '-') ?> <br>
        </p>
    </div>
    <div class="col-sm-5 col-xs-4">
        <div class="pull-right">
            <div class="text-center" style="display: inline-block; margin-right: 20px">
                <img src="data:image/png;base64,<?= $barcodeDeliveryOrder ?>"
                     alt="<?= $deliveryOrder['no_delivery_order'] ?>">
                <p>NO DELIVERY ORDER</p>
            </div>
        </div>
    </div>
</div>

<hr>

<p class="lead mt20 mb10">Goods</p>
<table class="table table-bordered table-striped no-datatable">
    <thead>
    <tr>
        <th style="width: 25px">No</th>
        <th>Goods</th>
        <th>Quantity</th>
        <th>Unit</th>
        <th>Tonnage (Kg)</th>
        <th>Volume (M<sup>3</sup>)</th>
        <th>Description</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php foreach ($deliveryOrderGoods as $item): ?>
        <tr>
            <td><?= $no++ ?></td>
            <td><?= $item['goods_name'] ?></td>
            <td><?= numerical($item['quantity']) ?></td>
            <td><?= $item['unit'] ?></td>
            <td><?= numerical($item['tonnage']) ?></td>
            <td><?= numerical($item['volume']) ?></td>
            <td><?= if_empty($item['description'], 'No description') ?></td>
        </tr>
    <?php endforeach; ?>

    <?php if (empty($deliveryOrderGoods)): ?>
        <tr>
            <td colspan="7" class="text-center">No data available</td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>

<div class="row mt20">
    <hr>
    <div class="col-xs-12">
        Description :
        <p class="mb0">
            <strong>
                <?= if_empty($deliveryOrder['description'], 'No description') ?>
            </strong>
        </p>
    </div>
</div>