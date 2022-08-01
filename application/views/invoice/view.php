<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Invoice</h3>
    </div>
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">No Invoice</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $invoice['no_invoice'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Reference</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?php
                                $referenceLink = '#';
                                if ($invoice['type'] == 'BOOKING FULL' || $invoice['type'] == 'BOOKING FULL EXTENSION' || $invoice['type'] == 'BOOKING PARTIAL') {
                                    $referenceLink = site_url('booking/view/' . $invoice['id_booking']);
                                } else if ($invoice['type'] == 'HANDLING') {
                                    $referenceLink = site_url('handling/view/' . $invoice['id_handling']);
                                } else if ($invoice['type'] == 'WORK ORDER') {
                                    $referenceLink = site_url('work-order/view/' . $invoice['id_work_order']);
                                }
                                ?>
                                <a href="<?= $referenceLink ?>">
                                    <?= if_empty($invoice['no_reference'], '-') ?>
                                </a>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Customer</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $invoice['customer_name'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Invoice Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($invoice['invoice_date']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $invoice['type'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Status</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= $invoice['status'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Description</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($invoice['description'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Inbound Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($invoice['inbound_date'], false) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Outbound Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($invoice['outbound_date'], false) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Created At</label>
                        <div class="col-sm-9">
                            <p class="form-control-static">
                                <?= readable_date($invoice['created_at']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Payment Information</h3>
            </div>
            <div class="box-body">
                <form role="form" class="form-horizontal form-view">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-sm-3">No Faktur</label>
                                <div class="col-sm-9">
                                    <p class="form-control-static"><?= if_empty($invoice['no_faktur'], '-') ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3">Faktur</label>
                                <div class="col-sm-9">
                                    <p class="form-control-static">
                                        <?php if (empty($invoice['attachment_faktur'])): ?>
                                            No attachment
                                        <?php else: ?>
                                            <a href="<?= base_url('uploads/invoice_faktur/' . $invoice['attachment_faktur']) ?>">
                                                Download Faktur
                                            </a>
                                        <?php endif; ?>
                                    </p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3">Payment Date</label>
                                <div class="col-sm-9">
                                    <p class="form-control-static"><?= readable_date($invoice['payment_date'], false) ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3">Payment Bank</label>
                                <div class="col-sm-9">
                                    <p class="form-control-static"><?= if_empty($invoice['transfer_bank'], '-') ?></p>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-sm-3">Transfer Amount</label>
                                <div class="col-sm-9">
                                    <p class="form-control-static">Rp. <?= numerical($invoice['transfer_amount'], 0, true) ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3">Cash Amount</label>
                                <div class="col-sm-9">
                                    <p class="form-control-static">Rp. <?= numerical($invoice['cash_amount'], 0, true) ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3">Over Payment</label>
                                <div class="col-sm-9">
                                    <p class="form-control-static">Rp. <?= numerical($invoice['over_payment_amount'], 0, true) ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-3">Description</label>
                                <div class="col-sm-9">
                                    <p class="form-control-static"><?= if_empty($invoice['payment_description'], '-') ?></p>
                                </div>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Detail Item</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable">
                    <thead>
                    <tr>
                        <th style="width: 25px">No</th>
                        <th>Item</th>
                        <th>Unit</th>
                        <th>Type</th>
                        <th>Quantity</th>
                        <th class="no-wrap">Unit Price</th>
                        <th>Multiplier</th>
                        <th class="text-right no-wrap">Total</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php $totalPrice = 0 ?>
                    <?php foreach ($invoiceDetails as $invoiceDetail): ?>
                        <?php
                        $total = $invoiceDetail['quantity'] * $invoiceDetail['unit_multiplier'] * $invoiceDetail['unit_price'];
                        $totalPrice += $total;
                        ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td style="word-break: break-word;">
                                <?= $invoiceDetail['item_name'] ?><br>
                                <small class="text-muted"><?= if_empty($invoiceDetail['description'], 'No description') ?></small><br>
                                <small class="text-muted">
                                    <?= str_replace(',', ', ', if_empty(item_summary_modifier($invoiceDetail['item_summary']), 'No item summary')) ?>
                                </small>
                            </td>
                            <td><?= $invoiceDetail['unit'] ?></td>
                            <td><?= $invoiceDetail['type'] ?></td>
                            <td><?= numerical($invoiceDetail['quantity'], 3, true) ?></td>
                            <td class="text-right no-wrap">Rp. <?= numerical($invoiceDetail['unit_price'], 0) ?></td>
                            <td><?= numerical($invoiceDetail['unit_multiplier'], 3, true) ?></td>
                            <td class="text-right no-wrap">Rp. <?= numerical($total, 0) ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <tr>
                        <td colspan="7"><strong>Total Price</strong></td>
                        <td class="text-right no-wrap"><strong>Rp. <?= numerical($totalPrice, 0) ?></strong></td>
                    </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="box-footer">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
    </div>
</div>