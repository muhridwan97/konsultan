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
            <th class="text-center">Goods Name</th>
            <th class="text-center">Unit</th>
            <th class="text-center">Pallet Marking Check</th>
            <th class="text-center">Type Check</th>
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
            <td class="text-center"><?= $stock['goods_name'] ?></td>
            <td class="text-center"><?= $stock['unit'] ?></td>
            <td class="text-center" style="<?= $stock['pallet_marking_check'] ? '' : 'background-color:#F08080'?>">
                <?= if_empty($stock['no_pallet'], ' - ') ?>
                <?= $stock['pallet_marking_check'] ? '(Benar)' : '(Salah)' ?></td>
            <td class="text-center" style="<?= $stock['type_goods_check'] ? '' : 'background-color:#F08080'?>">
                                        <?= if_empty($stock['type_goods'],'-') ?>
                <?= $stock['type_goods_check'] ? '(Benar)' : '(Salah)' ?></td>
            <td class="text-center" style="<?= $stock['position_check'] ? '' : 'background-color:#F08080'?>">
                <?= !empty($stock['position']) ? $stock['position'] : '-' ?>
                <small class="text-muted" style="display: block">
                    <?= !empty($stock['position_block']) ? $stock['position_block'] : '' ?>
                </small>
                <?= $stock['position_block'] ? '(Benar)' : '(Salah)' ?></td>
            <td class="text-center" style="<?= $stock['quantity_check'] ? '' : 'background-color:#F08080'?>">
                <?= $stock['quantity'] ?>
                <?= $stock['quantity_check'] ? '(Benar)' : '(Salah)' ?>
            </td>
            <td class="text-center"><?= $stock['description_check'] ?></td>
        </tr>
    <?php endforeach; ?>
    <?php if(empty($putAwayDetails)): ?>
        <tr>
            <td colspan="12" class="text-center"><?= "no document" ?></td>
        </tr>
    <?php endif; ?>
    </tbody>
</table>