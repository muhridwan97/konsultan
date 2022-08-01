<div class="row">
    <div class="col-xs-7">
        <p class="lead mb0">
            <strong><?= strtoupper($workOrder['handling_type']) ?> JOB DOCUMENT</strong>
        </p>
        <span class="text-muted"><?= if_empty($workOrder['handling_type_description'], '', '(', ')', true) ?></span>
        <p class="lead mb0">
            No Job: <strong><?= $workOrder['no_work_order'] ?></strong>
        </p>
        <p class="mb0">No Handling: <?= $workOrder['no_handling'] ?></p>
        <p class="mb0">No Booking: <?= if_empty($workOrder['no_booking'], '-') ?> (<?= $workOrder['no_reference'] ?>)</p>
        <?php if(!empty($bookingInbound)): ?>
            <p class="mb0">Ref Inbound: <?= if_empty($bookingInbound['no_booking'], '-') ?> (<?= $bookingInbound['no_reference'] ?>)</p>
        <?php endif; ?>
        <p class="mb0 mt10"><strong>BRANCH <?= $workOrder['branch'] ?></strong></p>
    </div>
    <div class="col-xs-5">
        <div class="pull-right">
            <div class="text-center" style="display: inline-block; margin-right: 20px">
                <img src="data:image/png;base64,<?= $barcodeWorkOrder ?>" alt="<?= $workOrder['no_work_order'] ?>">
                <p class="mb0">NO JOB SHEET</p>
            </div>
            <div class="text-center" style="display: inline-block">
                <img src="data:image/png;base64,<?= $barcodeHandling ?>" alt="<?= $workOrder['no_handling'] ?>">
                <p class="mb0">NO HANDLING</p>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="row">
    <div class="col-xs-6 col-sm-2">
        Handling Date :
        <p><strong><?= format_date($workOrder['handling_date'], 'd F Y') ?></strong></p>
    </div>
    <div class="col-xs-6 col-sm-2">
        Job Date :
        <p><strong><?= format_date($workOrder['created_at'], 'd F Y') ?></strong></p>
    </div>
    <div class="col-xs-6 col-sm-3">
        Queue :
        <p>
            <strong>
                <?= $workOrder['queue'] ?><sup>
                    <?php
                    if ($workOrder['queue'] % 10 == 1) {
                        echo 'st';
                    } else if ($workOrder['queue'] % 10 == 2) {
                        echo 'nd';
                    } else if ($workOrder['queue'] % 10 == 3) {
                        echo 'rd';
                    } else {
                        echo 'th';
                    }
                    ?></sup>
                at <?= format_date($workOrder['created_at'], 'd F Y') ?>
            </strong>
        </p>
    </div>
    <div class="col-xs-6 col-sm-5">
        Customer :
        <p><strong><?= $workOrder['customer_name'] ?></strong></p>
    </div>
</div>

<hr>

<?php if (!empty($jobContainers)): ?>
    <p class="lead mt10 mb10">Containers</p>
    <table class="table table-bordered table-striped no-datatable small mb10">
        <thead>
        <tr>
            <th style="width: 25px">No</th>
            <th>No Container</th>
            <th>Type</th>
            <th>Size</th>
            <th>Position</th>
            <th>Is Empty</th>
            <th>Danger</th>
            <th>Seal</th>
        </tr>
        </thead>
        <tbody>
        <?php $no = 1; ?>
        <?php foreach ($jobContainers as $container): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $container['no_container'] ?></td>
                <td><?= $container['type'] ?></td>
                <td><?= $container['size'] ?></td>
                <td><?= if_empty($container['position'], '-') ?></td>
                <td class="<?= $container['is_empty'] ? 'bg-red' :'' ?>">
                    <?= $container['is_empty'] ? 'Empty' : 'Full' ?>
                </td>
                <td class="<?= $container['status_danger'] != 'NOT DANGER' ? 'bg-red' :'' ?>">
                    <?= $container['status_danger'] ?>
                </td>
                <td><?= if_empty($container['seal'], '-') ?></td>
            </tr>
            <?php if (key_exists('goods', $container) && !empty($container['goods'])): ?>
                <tr>
                    <td></td>
                    <td colspan="7">
                        <table class="table table-condensed no-datatable ">
                            <thead>
                            <tr>
                                <th style="width: 25px">No</th>
                                <th>Goods</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Ex Container</th>
                                <th style="width: 100px">No Pallet</th>
                                <th>Unit Weight (Kg)</th>
                                <th>Total Weight (Kg)</th>
                                <th>Unit Gross (Kg)</th>
                                <th>Total Gross (Kg)</th>
                                <th>Unit Volume (M<sup>3</sup>)</th>
                                <th>Total Volume (M<sup>3</sup>)</th>
                                <th>Position</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php $innerNo = 1; ?>
                            <?php foreach ($container['goods'] as $item): ?>
                                <tr>
                                    <td><?= $innerNo++ ?></td>
                                    <td><?= $item['goods_name'] ?></td>
                                    <td><?= numerical($item['quantity']) ?></td>
                                    <td><?= $item['unit'] ?></td>
                                    <td><?= if_empty($item['ex_no_container'], '-') ?></td>
                                    <td style="word-break: break-all"><?= if_empty($item['no_pallet'], '-') ?></td>
                                    <td><?= numerical($item['unit_weight'], 3, true) ?></td>
                                    <td><?= numerical($item['total_weight'], 3, true) ?></td>
                                    <td><?= numerical($item['unit_gross_weight'], 3, true) ?></td>
                                    <td><?= numerical($item['total_gross_weight'], 3, true) ?></td>
                                    <td>
                                        <?= numerical($item['unit_volume']) ?><br>
                                        <small class="text-muted"><?= numerical($item['unit_length'], 3, true) ?>l x
                                            <?= numerical($item['unit_width'], 3, true) ?>w x
                                            <?= numerical($item['unit_height'], 3, true) ?>h
                                        </small>
                                    </td>
                                    <td><?= numerical($item['total_volume']) ?> M<sup>3</sup></td>
                                    <td><?= if_empty($item['position'], '-') ?></td>
                                </tr>
                            <?php endforeach ?>
                            </tbody>
                        </table>
                    </td>
                </tr>
            <?php endif ?>
        <?php endforeach ?>

        <?php if (empty($jobContainers)): ?>
            <tr>
                <td colspan="6" class="text-center">No data available</td>
            </tr>
        <?php endif ?>
        </tbody>
    </table>
<?php endif ?>


<?php if (!empty($jobGoods)): ?>
    <p class="lead mt10 mb10">Goods</p>
    <table class="table table-bordered table-striped no-datatable small mb10" style="font-size: 82%;">
        <thead>
        <tr>
            <th style="width: 25px">No</th>
            <th>Goods</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Ex Container</th>
            <th style="width: 100px">No Pallet</th>
            <th>Unit Weight (Kg)</th>
            <th>Total Weight (Kg)</th>
            <th>Unit Gross (Kg)</th>
            <th>Total Gross (Kg)</th>
            <th>Unit Volume (M<sup>3</sup>)</th>
            <th>Total Volume (M<sup>3</sup>)</th>
            <th>Position</th>
            <?php if(get_active_branch('branch_type') == BranchModel::BRANCH_TYPE_TPP): ?>
                <th>Danger</th>
            <?php endif; ?>
        </tr>
        </thead>
        <tbody>
        <?php $no = 1; ?>
        <?php foreach ($jobGoods as $item): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $item['goods_name'] ?></td>
                <td><?= numerical($item['quantity'], 3, true) ?></td>
                <td><?= $item['unit'] ?></td>
                <td><?= if_empty($item['ex_no_container'], '-') ?></td>
                <td style="word-break: break-all"><?= if_empty($item['no_pallet'], '-') ?></td>
                <td><?= numerical($item['unit_weight'], 3, true) ?></td>
                <td><?= numerical($item['total_weight'], 3, true) ?></td>
                <td><?= numerical($item['unit_gross_weight'], 3, true) ?></td>
                <td><?= numerical($item['total_gross_weight'], 3, true) ?></td>
                <td>
                    <?= numerical($item['unit_volume']) ?><br>
                    <small class="text-muted"><?= numerical($item['unit_length'], 3, true) ?>l x
                        <?= numerical($item['unit_width'], 3, true) ?>w x
                        <?= numerical($item['unit_height'], 3, true) ?>h
                    </small>
                </td>
                <td><?= numerical($item['total_volume']) ?> M<sup>3</sup></td>
                <td><?= if_empty($item['position'], '-') ?></td>
                <?php if(get_active_branch('branch_type') == BranchModel::BRANCH_TYPE_TPP): ?>
                    <td><?= if_empty($item['status_danger'], '-') ?></td>
                <?php endif; ?>
            </tr>
        <?php endforeach ?>

        <?php if (empty($jobGoods)): ?>
            <tr>
                <td colspan="14" class="text-center">No data available</td>
            </tr>
        <?php endif ?>
        </tbody>
    </table>
<?php endif ?>

<?php if (!empty($components)): ?>
    <p class="lead mt10 mb10">Components</p>
    <table class="table table-bordered table-striped no-datatable">
        <thead>
        <tr>
            <th style="width: 30px">No</th>
            <th>Component</th>
            <th>Quantity</th>
            <th>Unit</th>
            <th>Description</th>
        </tr>
        </thead>
        <tbody>
        <?php $no = 1; ?>
        <?php foreach ($components as $component): ?>
            <tr>
                <td><?= $no++ ?></td>
                <td><?= $component['handling_component'] ?></td>
                <td><?= numerical(key_exists('quantity', $component) ? $component['quantity'] : $component['handling_component_qty']) ?></td>
                <td><?= if_empty($component['unit'], '-') ?></td>
                <td><?= if_empty($component['description'], '-') ?></td>
            </tr>
        <?php endforeach ?>
        <?php if (empty($components)): ?>
            <tr>
                <td colspan="5" class="text-center">No data available</td>
            </tr>
        <?php endif ?>
        </tbody>
    </table>
<?php endif ?>

<div class="row mt10">
    <hr>
    <div class="col-xs-4">
        Gate Check In :
        <p>
            <strong>
                <?php
                if (empty($workOrder['gate_in_date'])) {
                    echo '-';
                } else {
                    echo format_date($workOrder['gate_in_date'], 'd F Y H:i');
                }
                ?>
            </strong>
        </p>
    </div>
    <div class="col-xs-4">
        Gate Check Out :
        <p>
            <strong>
                <?php
                if (empty($workOrder['gate_out_date'])) {
                    echo '-';
                } else {
                    echo format_date($workOrder['gate_out_date'], 'd F Y H:i');
                }
                ?>
            </strong>
        </p>
    </div>
    <div class="col-xs-4">
        Job Description :
        <p><strong><?= if_empty($workOrder['description'], '-') ?></strong></p>
    </div>
</div>

<div class="row">
    <hr>
    <div class="col-xs-6 text-center">
        <p><strong>Tally Staff</strong></p>
        <br><br>
        ( . . . . . . . . . . . . . . . . . . . )
    </div>
    <div class="col-xs-6 text-center">
        <p><strong>Warehouse Chief</strong></p>
        <br><br>
        ( . . . . . . . . . . . . . . . . . . . )
    </div>
</div>