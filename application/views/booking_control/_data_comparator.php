<?php if(!empty($containerStocks)): ?>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Stock Comparator Container</h3>
        </div>
        <div class="box-body">
            <?php if(!empty($containerStocks)): ?>
                <table class="table table-condensed table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 10%" class="text-right">No Ref.</th>
                        <th style="width: 10%" class="text-right">Job</th>
                        <th style="width: 10%" class="text-right">Position</th>
                        <th style="width: 10%" class="success text-right">Container In</th>
                        <th style="width: 10%" class="danger">Out Container</th>
                        <th style="width: 10%">Position</th>
                        <th style="width: 10%">Job</th>
                        <th style="width: 10%">No Ref.</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $totalIn = 0; $totalOut = 0; ?>
                    <?php foreach ($containerStocks as $stock): ?>
                        <?php for($i = 0; $i < if_empty(count($stock['outbounds']), 1); $i++): ?>
                            <tr>
                                <?php if($i == 0): ?>
                                    <?php $totalIn++ ?>
                                    <td class="text-right">
                                        <?= substr_replace($stock['no_reference'], '...', 4, strlen($stock['no_reference']) - 8) ?>
                                        <br>
                                        <a href="<?= site_url('booking/view/' . $stock['id_booking']) ?>">
                                            <?= $stock['no_booking'] ?>
                                        </a>
                                    </td>
                                    <td class="text-right">
                                        <a href="<?= site_url('work-order/view/' . $stock['id_work_order']) ?>">
                                            <?= $stock['handling_type'] ?>
                                        </a><br>
                                        <?= $stock['no_work_order'] ?><br>
                                        <small class="text-muted"><?= format_date($stock['completed_at'], 'd M Y') ?></small>
                                    </td>
                                    <td class="text-right"><?= if_empty($stock['position'], 'No position') ?></td>
                                    <td class="success text-right"><?= $stock['no_container'] ?></td>
                                <?php else: ?>
                                    <td colspan="4"></td>
                                <?php endif; ?>

                                <?php if(!empty($stock['outbounds'])): ?>
                                    <?php $totalOut++ ?>
                                    <td class="danger"><?= $stock['outbounds'][$i]['no_container'] ?></td>
                                    <td><?= if_empty($stock['outbounds'][$i]['position'], 'No Position') ?></td>
                                    <td>
                                        <a href="<?= site_url('work-order/view/' . $stock['outbounds'][0]['id_work_order']) ?>">
                                            <?= $stock['outbounds'][$i]['handling_type'] ?>
                                        </a><br>
                                        <?= $stock['outbounds'][$i]['no_work_order'] ?><br>
                                        <small class="text-muted"><?= format_date($stock['outbounds'][$i]['completed_at'], 'd M Y') ?></small>
                                    </td>
                                    <td>
                                        <?= substr_replace($stock['outbounds'][$i]['no_reference'], '...', 4, strlen($stock['outbounds'][$i]['no_reference']) - 8) ?>
                                        <br>
                                        <a href="<?= site_url('booking/view/' . $stock['outbounds'][$i]['id_booking']) ?>">
                                            <?= $stock['outbounds'][$i]['no_booking'] ?>
                                        </a>
                                    </td>
                                <?php endif; ?>
                            </tr>
                        <?php endfor; ?>
                    <?php endforeach; ?>
                    </tbody>
                    <tfoot>
                    <tr class="warning">
                        <th colspan="3">TOTAL STOCK <?= numerical($totalIn - $totalOut) ?></th>
                        <th class="text-right"><?= $totalIn ?> CONTAINER</th>
                        <th class="text-left"><?= $totalOut ?> CONTAINER</th>
                        <th colspan="3" class="text-right">TOTAL OUT</th>
                    </tr>
                    </tfoot>
                </table>
            <?php else: ?>
                No containers available
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>

<?php if(!empty($goodsStocks)): ?>
    <div class="box box-primary">
        <div class="box-header with-border">
            <h3 class="box-title">Stock Comparator Goods</h3>
        </div>
        <div class="box-body">
            <?php foreach ($goodsStocks as $goods): ?>
                <?php $totalIn = 0; $totalOut = 0; ?>
                <div class="box box-default">
                    <div class="box-header">
                        <h3 class="box-title">
                            <?= $goods['name'] ?> - <?= $goods['no_goods'] ?>
                        </h3>
                        <span class="pull-right">
                            <?= if_empty(get_if_exist($goods, 'ex_no_container'), '', 'EX CONTAINER: ') ?>
                        </span>
                    </div>
                    <div class="box-body">
                        <?php
                        $rowIn = count($goods['inbounds']);
                        $rowOut = count($goods['outbounds']);
                        $totalRows = $rowIn > $rowOut ? $rowIn : $rowOut
                        ?>
                        <table class="table table-condensed table-striped no-datatable responsive">
                            <thead>
                            <tr>
                                <th class="text-right">No Ref.</th>
                                <th class="text-right">Job</th>
                                <th class="text-right">Volume</th>
                                <th class="text-right">Gross</th>
                                <th class="text-right">Weight</th>
                                <th class="success text-right">Quantity In</th>
                                <th class="danger">Out Quantity</th>
                                <th>Weight</th>
                                <th>Gross</th>
                                <th>Volume</th>
                                <th>Job</th>
                                <th>No Ref.</th>
                            </tr>
                            </thead>
                            <tbody>
                            <?php for($i = 0; $i < $totalRows; $i++): ?>
                                <tr>
                                    <?php if(key_exists($i, $goods['inbounds'])): ?>
                                        <?php $totalIn += $goods['inbounds'][$i]['quantity'] ?>
                                        <td class="text-right">
                                            <?= substr_replace($goods['inbounds'][$i]['no_reference'], '...', 4, strlen($goods['inbounds'][$i]['no_reference']) - 8) ?>
                                            <br>
                                            <a href="<?= site_url('booking/view/' . $goods['inbounds'][$i]['id_booking']) ?>">
                                                <?= $goods['inbounds'][$i]['no_booking'] ?>
                                            </a>
                                        </td>
                                        <td class="text-right">
                                            <a href="<?= site_url('work-order/view/' . $goods['inbounds'][$i]['id_work_order']) ?>">
                                                <?= $goods['inbounds'][$i]['handling_type'] ?>
                                            </a><br>
                                            <?= $goods['inbounds'][$i]['no_work_order'] ?><br>
                                            <small class="text-muted"><?= format_date($goods['inbounds'][$i]['completed_at'], 'd M Y') ?></small>
                                        </td>
                                        <td class="text-right"><?= numerical($goods['inbounds'][$i]['total_volume']) ?></td>
                                        <td class="text-right"><?= numerical($goods['inbounds'][$i]['total_gross_weight'], 3, true) ?></td>
                                        <td class="text-right"><?= numerical($goods['inbounds'][$i]['total_weight'], 3, true) ?></td>
                                        <td class="text-right success">
                                            <?= numerical($goods['inbounds'][$i]['quantity'], 3, true) ?>
                                            <?= $goods['inbounds'][$i]['unit'] ?>
                                        </td>
                                    <?php else: ?>
                                        <td colspan="6"></td>
                                    <?php endif; ?>

                                    <?php if(key_exists($i, $goods['outbounds'])): ?>
                                        <?php $totalOut += $goods['outbounds'][$i]['quantity'] ?>
                                        <td class="danger">
                                            <?= numerical($goods['outbounds'][$i]['quantity'], 3, true) ?>
                                            <?= $goods['outbounds'][$i]['unit'] ?>
                                        </td>
                                        <td><?= numerical($goods['outbounds'][$i]['total_weight'], 3, true) ?></td>
                                        <td><?= numerical($goods['outbounds'][$i]['total_gross_weight'], 3, true) ?></td>
                                        <td><?= numerical($goods['outbounds'][$i]['total_volume']) ?></td>
                                        <td>
                                            <a href="<?= site_url('work-order/view/' . $goods['outbounds'][$i]['id_work_order']) ?>">
                                                <?= $goods['outbounds'][$i]['handling_type'] ?>
                                            </a><br>
                                            <?= $goods['outbounds'][$i]['no_work_order'] ?><br>
                                            <small class="text-muted"><?= format_date($goods['outbounds'][$i]['completed_at'], 'd M Y') ?></small>
                                        </td>
                                        <td>
                                            <?= substr_replace($goods['outbounds'][$i]['no_reference'], '...', 4, strlen($goods['outbounds'][$i]['no_reference']) - 8) ?>
                                            <br>
                                            <a href="<?= site_url('booking/view/' . $goods['outbounds'][$i]['id_booking']) ?>">
                                                <?= $goods['outbounds'][$i]['no_booking'] ?>
                                            </a>
                                        </td>
                                    <?php else: ?>
                                        <td colspan="6"></td>
                                    <?php endif; ?>
                                </tr>
                            <?php endfor; ?>
                            </tbody>
                            <tfoot>
                            <tr class="warning">
                                <th colspan="5">TOTAL STOCK <?= numerical($totalIn - $totalOut, 3, true) ?></th>
                                <th class="text-right"><?= $totalIn ?> UNIT</th>
                                <th class="text-left"><?= $totalOut ?> UNIT</th>
                                <th colspan="5" class="text-right">TOTAL OUT</th>
                            </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            <?php endforeach; ?>
            <?php if(empty($goodsStocks)): ?>
                No goods available
            <?php endif; ?>
        </div>
    </div>
<?php endif; ?>