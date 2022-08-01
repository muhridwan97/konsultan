 <form class="form-horizontal form-view">
    <div class="row">
        <div class="col-xs-6 col-sm-3">
            Branch :
            <p><strong><?=  $cycleCounts['branch'] ?> </strong></p>
        </div>
        <div class="col-xs-6 col-sm-3">
            Description :
            <p><strong> <?= if_empty($cycleCounts['description'], 'No description') ?></strong></p>
        </div>
        <div class="col-xs-6 col-sm-3">
            Cycle Count Date :
            <p class="form-control-static">
                <strong><?= format_date($cycleCounts['cycle_count_date'], 'd F Y') ?></strong>
            </p>
        </div>
        <div class="col-xs-6 col-sm-3">
            Status :
            <p class="<?= $cycleCounts['status'] != 'APPROVED' ? 'text-danger' : '' ?>">
                <strong><?= $cycleCounts['status'] ?></strong>
            </p>
        </div>
    </div>
</form>

<?php if($cycleCounts['type'] == "GOODS"): ?>
<p class="lead mb10"><strong>Goods</strong></p>
<table class="table table-bordered table-condensed table-striped no-datatable">
    <thead>
        <tr>
            <th class="text-center" style="width: 20px">No</th>
            <th class="text-center">Owner Name</th>
            <th class="text-center">No Booking</th>
            <th class="text-center">No Goods</th>
            <th class="text-center">Position</th>
            <th class="text-center">Ex No Container</th>
            <th class="text-center">Goods Name</th>
            <th class="text-center">Quantity</th>
            <th class="text-center">Unit</th>
            <th class="text-center">Position Check</th>
            <th class="text-center">Quantity Check</th>
            <th class="text-center">Description Check</th>
        </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php foreach ($cycleCountDetails as $stock): ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td ><?= $stock['name'] ?></td>
            <td class="text-center">
                <?= $stock['no_booking'] ?> <br/>
                <small><?= "(".$stock['no_reference'].")" ?></small> 
            </td>
            <td class="text-center"><?= $stock['no_goods'] ?></td>
            <td>
                <?= !empty($stock['position']) ? $stock['position'] : '-' ?>
                <small class="text-muted" style="display: block">
                   <?= !empty($stock['position_block']) ? $stock['position_block'] : '' ?>
                </small>
            </td>
            <td class="text-center"><?= if_empty($stock['ex_no_container'],'-') ?></td>
            <td class="text-center"><?= $stock['goods_name'] ?></td>
            <td class="text-center"><?= if_empty(numerical($stock['quantity'],3,true), ' - ') ?></td>
            <td class="text-center"><?= $stock['unit'] ?></td>
            <td class="text-center"><?= $stock['position_check'] ?></td>
            <td class="text-center"><?= $stock['quantity_check'] ?></td>
            <td class="text-center"><?= $stock['description_check'] ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if(empty($cycleCountDetails)): ?>
        <tr>
            <td colspan="12" class="text-center"><?= "no document" ?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
<?php endif; ?>

<?php if($cycleCounts['type'] == "CONTAINER"): ?>
<p class="lead mb10"><strong>Container</strong></p>
<table class="table table-bordered table-condensed table-striped no-datatable">
    <thead>
    <tr>
        <th class="text-center" style="width: 20px">No</th>
        <th class="text-center">Owner Name</th>
        <th class="text-center">No Booking</th>
        <th class="text-center">No Container</th>
        <th class="text-center">Quantity</th>
        <th class="text-center">Position</th>
        <th class="text-center">Seal</th>
        <th class="text-center">Position Check</th>
        <th class="text-center">Quantity Check</th>
        <th class="text-center">Description Check</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php foreach ($cycleCountContainers as $container): ?>
           <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td ><?= $container['name'] ?></td>
                <td class="text-center">
                    <?= $container['no_booking'] ?>
                    <small><?= "(".$container['no_reference'].")" ?></small> 
                </td>
                <td class="text-center"><?= $container['no_container'] ?></td>
                <td class="text-center"><?= if_empty(numerical($container['quantity'],3,true), ' - ') ?></td>
                <td>
                    <?= !empty($container['position']) ? $container['position'] : '-' ?>
                    <small class="text-muted" style="display: block">
                       <?= !empty($container['position_block']) ? $container['position_block'] : '' ?>
                    </small>
                </td>
                <td class="text-center"><?= if_empty($container['seal'],'-') ?></td>
                <td class="text-center"><?= if_empty($container['position_check'],'-') ?></td>
                <td class="text-center"><?= $container['quantity_check'] ?></td>
                <td class="text-center"><?= $container['description_check'] ?></td>
            </tr>
    <?php endforeach; ?>
    <?php if(empty($cycleCountContainers)): ?>
        <tr>
            <td colspan="12" class="text-center"><?= "no document" ?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
<?php endif; ?>