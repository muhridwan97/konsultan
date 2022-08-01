<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Booking CIF <?= $cif['no_booking'] ?></h3>
    </div>

    <div class="box-body">
        <div class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Category</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($cif['category'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Booking Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($cif['booking_type'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Customer</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($cif['customer_name'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Booking</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php if(empty($cif['no_booking'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="<?= site_url('booking/view/' . $cif['id_booking']) ?>">
                                        <?= $cif['no_booking'] ?>
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Reference</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($cif['no_reference'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Reference <?= $booking['category'] == 'INBOUND' ? 'Out' : 'In' ?></label>
                        <div class="col-sm-9">
                            <?php if ($booking['category'] == 'INBOUND'): ?>
                                <?php foreach ($bookingOut as $bookingOutData): ?>
                                    <p class="form-control-static">
                                        <a href="<?= site_url('booking/cif/' . $bookingOutData['id']) ?>">
                                            <?= $bookingOutData['no_booking'] ?>
                                        </a>
                                    </p>
                                <?php endforeach; ?>
                                <?php if (empty($bookingOut)): ?>
                                    <p class="form-control-static">No outbound references yet</p>
                                <?php endif; ?>
                            <?php else: ?>
                                <p class="form-control-static">
                                    <a href="<?= site_url('booking/cif/' . $booking['id_booking_in']) ?>">
                                        <?= if_empty($booking['no_booking_in'], '-') ?>
                                    </a>
                                </p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Currency From</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($cif['currency_from'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Currency To</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($cif['currency_to'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Exchange Value</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(numerical($cif['exchange_value'], 3, true), '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Exchange Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty(format_date($cif['exchange_date'], 'd F Y'), '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Party</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($cif['party'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= if_empty($cif['description'], '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">CIF Invoice <?= $booking['category'] == 'INBOUND' ? 'In' : 'Out' ?></h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>Goods Name</th>
                        <th>Quantity</th>
                        <th>Weight</th>
                        <th>Gross Weight</th>
                        <th>Volume</th>
                        <th>Price (<?= $cif['currency_from'] ?>)</th>
                        <th>Total (<?= $cif['currency_from'] ?>)</th>
                        <th>Item Value (<?= $cif['currency_from'] ?>)</th>
                        <th>Total Price (<?= $cif['currency_from'] ?>)</th>
                        <th>Total Price (<?= $cif['currency_to'] ?>)</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $totalDistributedValue = 0 ?>
                    <?php foreach ($cifDetails as $index => $detail): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $detail['goods_name'] ?></td>
                            <td><?= numerical($detail['quantity'], 2, true) ?></td>
                            <td><?= numerical($detail['weight'], 2, true) ?></td>
                            <td><?= numerical($detail['gross_weight'], 2, true) ?></td>
                            <td><?= numerical($detail['volume'], 2, true) ?></td>
                            <td><?= numerical($detail['price'], 2, true) ?></td>
                            <td><?= numerical($detail['total_price'], 2, true) ?></td>
                            <td><?= numerical($detail['total_item_value'], 2, true) ?></td>
                            <td><?= numerical($detail['total_price'] + $detail['total_item_value'], 2, true) ?></td>
                            <td><?= numerical(($detail['total_price'] + $detail['total_item_value']) * $cif['exchange_value'], 2, true) ?></td>
                        </tr>
                        <?php $totalDistributedValue += $detail['total_item_value'] ?>
                    <?php endforeach; ?>

                    <?php if(empty($cifDetails)): ?>
                    <tr>
                        <td colspan="9">No data available</td>
                    </tr>
                    <?php else: ?>
                        <tr class="success">
                            <td colspan="7"><strong>Distributed Value</strong></td>
                            <td></td>
                            <td><strong><?= numerical($cif['total_item_value'], 2, true) ?></strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <tr>
                            <td colspan="7"><strong>Total</strong></td>
                            <td><strong><?= numerical($cif['subtotal'], 2, true) ?></strong></td>
                            <td><strong><?= numerical($cif['subtotal'], 2, true) ?></strong></td>
                            <td></td>
                            <td></td>
                        </tr>
                        <?php if($cif['category'] == 'INBOUND'): ?>
                            <tr class="warning">
                                <td colspan="7"><strong>Discount</strong></td>
                                <td><strong><?= numerical($cif['discount'], 2, true) ?></strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="7"><strong>Freight</strong></td>
                                <td><strong><?= numerical($cif['freight'], 2, true) ?></strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="7"><strong>Insurance</strong></td>
                                <td><strong><?= numerical($cif['insurance'], 2, true) ?></strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="7"><strong>Handling</strong></td>
                                <td><strong><?= numerical($cif['handling'], 2, true) ?></strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                            <tr>
                                <td colspan="7"><strong>Other</strong></td>
                                <td><strong><?= numerical($cif['other'], 2, true) ?></strong></td>
                                <td></td>
                                <td></td>
                                <td></td>
                            </tr>
                        <?php endif; ?>
                        <tr class="danger">
                            <td colspan="7"><strong>Total CIF</strong></td>
                            <td><strong><?= numerical($cif['total_price'], 2, true) ?></strong></td>
                            <td><strong><?= numerical($cif['total_price_value'], 2, true) ?></strong></td>
                            <td><strong><?= numerical($cif['total_price_value'], 2, true) ?></strong></td>
                            <td><strong><?= numerical($cif['total_price_value'] * $cif['exchange_value'], 2, true) ?></strong></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

        <?php if($cif['category'] == 'INBOUND'): ?>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">CIF Invoice Outbound</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped no-datatable responsive">
                        <thead>
                        <tr>
                            <th style="width: 50px">No</th>
                            <th>Outbound</th>
                            <th>Total Item</th>
                            <th>Total Price (<?= $cif['currency_from'] ?>)</th>
                            <th>Total Price (<?= $cif['currency_to'] ?>)</th>
                            <th>NDPBM</th>
                            <th>Customs Value</th>
                            <th>VAT</th>
                            <th>Income Tax</th>
                            <th>BM</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $totalCIFOutbound = $totalCustomsValue = $totalVAT = $totalIncomeTax = $totalImportDuty = 0 ?>
                        <?php foreach ($cifOutbounds as $index => $cifOutbound): ?>
                            <tr>
                                <td><?= $index + 1 ?></td>
                                <td>
                                    <a href="<?= site_url('booking/cif/' . $cifOutbound['id_booking']) ?>">
                                        <?= $cifOutbound['no_reference'] ?>
                                    </a>
                                </td>
                                <td><?= numerical($cifOutbound['total_item'], 2, true) ?></td>
                                <td><?= numerical($cifOutbound['total_price_value'], 2, true) ?></td>
                                <td><?= numerical($cifOutbound['total_price_value'] * $cif['exchange_value'], 2, true) ?></td>
                                <td>Rp. <?= numerical($cifOutbound['ndpbm'], 2, true) ?></td>
                                <td>Rp. <?= numerical($cifOutbound['ndpbm'] * $cifOutbound['total_price_value'], 2, true) ?></td>
                                <td>Rp. <?= numerical($cifOutbound['vat'], 2, true) ?></td>
                                <td>Rp. <?= numerical($cifOutbound['income_tax'], 2, true) ?></td>
                                <td>Rp. <?= numerical($cifOutbound['import_duty'], 2, true) ?></td>
                            </tr>
                            <?php
                            $totalCIFOutbound += $cifOutbound['total_price_value'];
                            $totalCustomsValue += $cifOutbound['total_price_value'] * $cifOutbound['ndpbm'];
                            $totalVAT += $cifOutbound['vat'];
                            $totalIncomeTax += $cifOutbound['income_tax'];
                            $totalImportDuty += $cifOutbound['import_duty'];
                            ?>
                        <?php endforeach; ?>
                        <?php if(empty($cifOutbounds)): ?>
                            <tr>
                                <td colspan="9">No invoice outbound data</td>
                            </tr>
                        <?php endif; ?>
                        <tr class="danger">
                            <td colspan="3"><strong>Outbound</strong></td>
                            <td><strong><?= numerical($totalCIFOutbound, 2, true) ?></strong></td>
                            <td><strong><?= numerical($totalCIFOutbound * $cif['exchange_value'], 2, true) ?></strong></td>
                            <td></td>
                            <td><strong>Rp. <?= numerical($totalCustomsValue, 2, true) ?></strong></td>
                            <td><strong>Rp. <?= numerical($totalVAT, 2, true) ?></strong></td>
                            <td><strong>Rp. <?= numerical($totalIncomeTax, 2, true) ?></strong></td>
                            <td><strong>Rp. <?= numerical($totalImportDuty, 2, true) ?></strong></td>
                        </tr>
                        <tr class="success">
                            <td colspan="3"><strong>Inbound</strong></td>
                            <td><strong><?= numerical($cif['total_price_value'], 2, true) ?></strong></td>
                            <td><strong><?= numerical($cif['total_price_value'] * $cif['exchange_value'], 2, true) ?></strong></td>
                            <td colspan="5"></td>
                        </tr>
                        <tr class="warning">
                            <td colspan="3"><strong>Balance</strong></td>
                            <td><strong><?= numerical($cif['total_price_value'] - $totalCIFOutbound, 2, true) ?></strong></td>
                            <td><strong><?= numerical(($cif['total_price_value'] * $cif['exchange_value']) - ($totalCIFOutbound * $cif['exchange_value']), 2, true) ?></strong></td>
                            <td colspan="5"></td>
                        </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        <?php else: ?>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Customs Value Out</h3>
                </div>
                <div class="box-body">
                    <div class="form-horizontal form-view">
                        <div class="row">
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4">CIF In (<?= $cifInbound['currency_from'] ?>)</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= numerical($cifInbound['total_price_value'], 2, true) ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4">CIF In (<?= $cifInbound['currency_to'] ?>)</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= numerical($cifInbound['total_price_value'] * $cifInbound['exchange_value'], 2, true) ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4">CIF Out (<?= $cif['currency_from'] ?>)</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= numerical($cif['total_price_value'], 2, true) ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4">CIF Out (<?= $cif['currency_to'] ?>)</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static"><?= numerical($cif['total_price_value'] * $cif['exchange_value'], 2, true) ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4">NDPBM (<?= $cif['currency_from'] ?>-IDR)</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">Rp. <?= numerical($cif['ndpbm'], 2, true) ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4">Customs Value</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">
                                            <strong>Rp. <?= numerical($cif['ndpbm'] * $cif['total_price_value'], 2, true) ?></strong>
                                        </p>
                                    </div>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group">
                                    <label class="col-sm-4">VAT (PPN)</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">Rp. <?= numerical($cif['vat'], 2, true) ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4">Income Tax (PPH)</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">Rp. <?= numerical($cif['income_tax'], 2, true) ?></p>
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-sm-4">Import Duty (BM)</label>
                                    <div class="col-sm-8">
                                        <p class="form-control-static">Rp. <?= numerical($cif['import_duty'], 2, true) ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>
