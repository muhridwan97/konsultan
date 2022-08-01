 <form class="form-horizontal form-view">
    <div class="row">
        <div class="col-xs-6 col-sm-3">
            Branch :
            <p><strong><?=  $putAway['branch'] ?> </strong></p>
        </div>
        <div class="col-xs-6 col-sm-3">
            Description :
            <p><strong> <?= if_empty($putAway['description'], 'No description') ?></strong></p>
        </div>
        <div class="col-xs-6 col-sm-3">
            Put Away Date :
            <p class="form-control-static">
                <strong><?= format_date($putAway['put_away_date'], 'd F Y') ?></strong>
            </p>
        </div>
        <div class="col-xs-6 col-sm-3">
            Status :
            <p class="<?= $putAway['status'] != 'APPROVED' ? 'text-danger' : '' ?>">
                <strong><?= $putAway['status'] ?></strong>
            </p>
        </div>
    </div>
</form>

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
    <?php foreach ($putAwayDetails as $stock): ?>
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
            <td class="text-center"></td>
            <td class="text-center"></td>
            <td class="text-center"></td>
        </tr>
    <?php endforeach; ?>
    <?php if(empty($putAwayDetails)): ?>
        <tr>
            <td colspan="12" class="text-center"><?= "no goods" ?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>
