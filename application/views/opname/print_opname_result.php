<div class="row">
    <div class="col-sm-7 col-xs-6">
        <p class="lead" style="margin-bottom: 0px;">
            <strong>OPNAME RESULT DOCUMENT</strong>
        </p>
        <p class="lead" style="margin-bottom: 0">
            No Opname: <?= $opname['no_opname'] ?>
        </p>
        <p style="margin-bottom: 5px"><strong>BRANCH <?= $opname['branch'] ?></strong></p>
        <p class="mb0">Location Accuracy <?= numerical($opname['location_accuracy'] ?? 0, 2, true) ?>%</p>
        <p class="mb0">Quantity Accuracy <?= numerical($opname['quantity_accuracy'] ?? 0, 2, true) ?>%</p>
    </div>
    <div class="col-sm-5 col-xs-6">
        <div class="pull-right">
            <div class="text-center" style="display: inline-block">
                <img src="data:image/png;base64,<?= $opnameBarcode ?>" alt="<?= $opname['no_opname'] ?>">
                <p>NO OPNAME</p>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="row" style="margin-top: 20px">
    <div class="col-xs-6 col-sm-3">
        Opname Request Date :
        <p><strong><?= readable_date($opname['opname_date']) ?> </strong></p>
    </div>
    <div class="col-xs-6 col-sm-3">
        Requested By :
        <p><strong><?= if_empty($opname['requested_by'], '-') ?></strong></p>
    </div>
    <div class="col-xs-6 col-sm-3">
        Status :
        <p class="<?= $opname['status'] != 'APPROVED' ? 'text-danger' : '' ?>"><strong><?= $opname['status'] ?></strong></p>
    </div>
    <div class="col-xs-6 col-sm-3">
        Validated By :
        <p><strong><?= if_empty($opname['validated_by'], '-') ?></strong></p>
    </div>
</div>

<hr>
<?php if($opname['opname_type'] == "GOODS"): ?>
<p class="lead mb10"><strong>Goods</strong></p>
<table class="table table-bordered table-condensed table-striped no-datatable">
    <thead>
        <tr>
            <th class="text-center" style="width: 20px">No</th>
            <th class="text-center">No Pallet</th>
            <th class="text-center">Owner Name</th>
            <th class="text-center">No Booking</th>
            <th class="text-center">No Goods</th>
            <th class="text-center">Goods Name</th>
            <th class="text-center">Position</th>
            <th class="text-center">Ex No Container</th>
            <th class="text-center">Unit</th>
            <th class="text-center">Position Check</th>
            <th class="text-center">Quantity Check</th>
            <th class="text-center">Description Check</th>
        </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php foreach ($opnameStocks as $stock): ?>
        <tr>
            <td class="text-center"><?= $no++ ?></td>
            <td class="text-center"><?= $stock['no_pallet'] ?></td>
            <td ><?= $stock['name'] ?></td>
            <td class="text-center">
                <?= $stock['no_booking'] ?> <br/>
                <small><?= "(".$stock['no_reference'].")" ?></small> 
            </td>
            <td class="text-center"><?= $stock['no_goods'] ?></td>
            <td class="text-center"><?= $stock['name_goods'] ?></td>
            <td>
                <?= !empty($stock['position']) ? $stock['position'] : '-' ?>
                <small class="text-muted" style="display: block">
                   <?= !empty($stock['position_block']) ? $stock['position_block'] : '' ?>
                </small>
            </td>
            <td class="text-center"><?= if_empty($stock['ex_no_container'],'-') ?></td>
            <td class="text-center"><?= $stock['unit'] ?></td>
            <td class="text-center"><?= $stock['position_check'] ?></td>
            <td class="text-center"><?= $stock['quantity_check'] ?></td>
            <td class="text-center"><?= $stock['description_check'] ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if(is_null($opnameStocks)): ?>
        <tr>
            <td colspan="12" class="text-center"><?= "no document" ?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
<?php endif; ?>
<?php if($opname['opname_type'] == "CONTAINER"): ?>
<p class="lead mb10"><strong>Container</strong></p>
<table class="table table-bordered table-condensed table-striped no-datatable">
    <thead>
    <tr>
        <th class="text-center" style="width: 20px">No</th>
        <th class="text-center">Owner Name</th>
        <th class="text-center">No Booking</th>
        <th class="text-center">No Container</th>
        <th class="text-center">Position</th>
        <th class="text-center">Seal</th>
        <th class="text-center">Position Check</th>
        <th class="text-center">Quantity Check</th>
        <th class="text-center">Description Check</th>
    </tr>
    </thead>
    <tbody>
    <?php $no = 1; ?>
    <?php foreach ($opnameContainers as $container): ?>
           <tr>
                <td class="text-center"><?= $no++ ?></td>
                <td ><?= $container['name'] ?></td>
                <td class="text-center">
                    <?= $container['no_booking'] ?>
                    <small><?= "(".$container['no_reference'].")" ?></small> 
                </td>
                <td class="text-center"><?= $container['no_container'] ?></td>
                <td>
                    <?= !empty($container['position']) ? $container['position'] : '-' ?>
                    <small class="text-muted" style="display: block">
                       <?= !empty($container['position_block']) ? $container['position_block'] : '' ?>
                    </small>
                </td>
                <td class="text-center"><?= if_empty($container['seal'],'-') ?></td>
                <td class="text-center"><?= $container['position_check'] ?></td>
                <td class="text-center"><?= $container['quantity_check'] ?></td>
                <td class="text-center"><?= $container['description_check'] ?></td>
            </tr>
    <?php endforeach; ?>
    <?php if(is_null($opnameContainers)): ?>
        <tr>
            <td colspan="12" class="text-center"><?= "no document" ?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
<?php endif; ?>

<div class="row" style="margin-top: 20px">
    <hr>
    <div class="col-xs-12">
        Opname Description :
        <p><strong><?= if_empty($opname['description'], ' No Description') ?></strong></p>
    </div>
</div>