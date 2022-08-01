<div class="panel panel-default">
    <div class="panel-body">
        <p class="mb10"><strong>CHARGE PLAN DETAIL</strong></p>

        <table class="table table-bordered no-datatable">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Price</th>
                <th>Multiplier</th>
                <th class="text-right">Total</th>
            </tr>
            </thead>
            <tbody>

            <?php $no = 1; ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $handling['handling_type'] ?></td>
                <td>1</td>
                <td>Activity</td>
                <td>Rp. XXX.XXX</td>
                <td>1</td>
                <td class="text-right">Rp. XXX.XXX</td>
            </tr>

            <?php if (!empty($handlingContainers)): ?>
                <?php foreach ($handlingContainers as $container): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $container['no_container'] ?></td>
                        <td><?= $container['quantity'] ?></td>
                        <td><?= $container['size'] ?> Ft</td>
                        <td>Rp. XXX.XXX</td>
                        <td>1</td>
                        <td class="text-right">Rp. XXX.XXX</td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($handlingGoods)): ?>
                <?php foreach ($handlingGoods as $item): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $item['goods_name'] ?></td>
                        <td><?= numerical($item['quantity']) ?></td>
                        <td><?= $item['unit'] ?></td>
                        <td>Rp. XXX.XXX</td>
                        <td>1</td>
                        <td class="text-right">Rp. XXX.XXX</td>
                    </tr>
                <?php endforeach; ?>
            <?php endif; ?>

            <?php if (!empty($handlingContainers) || !empty($handlingGoods)): ?>
                <tr>
                    <td colspan="6">
                        <strong>Sub total</strong>
                    </td>
                    <td class="text-right"><strong>Rp. XXX.XXX</strong></td>
                </tr>
            <?php endif; ?>


            <?php if (!empty($handling['components'])): ?>
                <?php $no = 1; ?>
                <?php foreach ($handling['components'] as $component): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $component['handling_component'] ?></td>
                        <td><?= numerical($component['quantity']) ?></td>
                        <td>Minutes</td>
                        <td>Rp. 14.000</td>
                        <td>1</td>
                        <td class="text-right">Rp. XXX.XXX</td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="6">
                        <strong>Sub total</strong>
                    </td>
                    <td class="text-right"><strong>Rp. XXX.XXX</strong></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td></td>
                <td colspan="5">Admin</td>
                <td class="text-right">Rp. XXX.XXX</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="5">Tax 10%</td>
                <td class="text-right">Rp. XXX.XXX</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="5">Discount</td>
                <td class="text-right text-danger">- Rp. XXX.XXX</td>
            </tr>
            <tr>
                <td colspan="6" class="lead">
                    <strong>Total Handling Price</strong>
                </td>
                <td class="text-right lead"><strong>Rp. XXX.XXX</strong></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>

<div class="panel panel-default">
    <div class="panel-body">
        <p class="mb10"><strong>CHARGE REALIZATION DETAIL</strong></p>

        <table class="table table-bordered no-datatable">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th>Item</th>
                <th>Quantity</th>
                <th>Unit</th>
                <th>Price</th>
                <th>Multiplier</th>
                <th class="text-right">Total</th>
            </tr>
            </thead>
            <tbody>

            <?php foreach ($workOrders as $workOrder): ?>
                <?php if($workOrder['status'] == 'COMPLETED'): ?>
                    <?php $no = 1; ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $workOrder['handling_type'] ?></td>
                        <td>1</td>
                        <td>Activity</td>
                        <td>Rp. XXX.XXX</td>
                        <td>1</td>
                        <td class="text-right">Rp. XXX.XXX</td>
                    </tr>

                    <?php if (!empty($workOrder['containers'])): ?>
                        <?php foreach ($workOrder['containers'] as $container): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $container['no_container'] ?></td>
                                <td><?= $container['quantity'] ?></td>
                                <td><?= $container['size'] ?> Ft</td>
                                <td>Rp. XXX.XXX</td>
                                <td>1</td>
                                <td class="text-right">Rp. XXX.XXX</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!empty($workOrder['goods'])): ?>
                        <?php foreach ($workOrder['goods'] as $item): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td><?= $item['goods_name'] ?></td>
                                <td><?= numerical($item['quantity']) ?></td>
                                <td><?= $item['unit'] ?></td>
                                <td>Rp. XXX.XXX</td>
                                <td>1</td>
                                <td class="text-right">Rp. XXX.XXX</td>
                            </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>

                    <?php if (!empty($workOrder['containers']) || !empty($workOrder['goods'])): ?>
                        <tr>
                            <td colspan="6">
                                <strong>Sub total</strong>
                            </td>
                            <td class="text-right"><strong>Rp. XXX.XXX</strong></td>
                        </tr>
                    <?php endif; ?>

                <?php endif; ?>
            <?php endforeach; ?>


            <?php if (!empty($workOrder['components'])): ?>
                <?php $no = 1; ?>
                <?php foreach ($workOrder['components'] as $component): ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td><?= $component['handling_component'] ?></td>
                        <td><?= numerical($component['quantity']) ?></td>
                        <td>Minutes</td>
                        <td>Rp. XXX.XXX</td>
                        <td>1</td>
                        <td class="text-right">Rp. XXX.XXX</td>
                    </tr>
                <?php endforeach; ?>
                <tr>
                    <td colspan="6">
                        <strong>Sub total</strong>
                    </td>
                    <td class="text-right"><strong>Rp. XXX.XXX</strong></td>
                </tr>
            <?php endif; ?>
            <tr>
                <td></td>
                <td colspan="5">Admin</td>
                <td class="text-right">Rp. XXX.XXX</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="5">Tax 10%</td>
                <td class="text-right">Rp. XXX.XXX</td>
            </tr>
            <tr>
                <td></td>
                <td colspan="5">Discount</td>
                <td class="text-right text-danger">- Rp. XXX.XXX</td>
            </tr>
            <tr>
                <td colspan="6" class="lead">
                    <strong>Total Job Price</strong>
                </td>
                <td class="text-right lead"><strong>Rp. XXX.XXX</strong></td>
            </tr>
            </tbody>
        </table>
    </div>
</div>