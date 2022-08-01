<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Payment Realization</h3>
    </div>
    <form action="<?= site_url('payment/update_realization/' . $payment['id']) ?>" role="form" method="post"
          enctype="multipart/form-data" id="form-payment" class="realization">
        <input type="hidden" name="id" id="id" value="<?= $payment['id'] ?>">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Booking</label>
                        <div class="form-control-static">
                            <?php if(!empty($payment['id_booking'])): ?>
                                <input type="hidden" id="booking" value="<?= $payment['id_booking'] ?>">
                                <?= $payment['no_booking'] ?> (<?= $payment['no_reference'] ?>)
                            <?php else: ?>
                                <input type="hidden" id="upload" value="<?= $payment['id_upload'] ?>">
                                <?= $payment['no_upload'] ?> (<?= $payment['upload_description'] ?>)
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Attachment</label>
                        <div class="form-control-static">
                            <?php if (empty($payment['attachment'])): ?>
                                No uploaded file
                            <?php else: ?>
                                <a href="<?= base_url('uploads/payments/' . $payment['attachment']) ?>" target="_blank">
                                    <?= $payment['attachment'] ?>
                                </a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label for="payment_date">Requested Amount</label>
                        <div class="form-control-static">Rp. <?= numerical($payment['amount_request'], 0, true) ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="payment_date">Request Date</label>
                        <div class="form-control-static"><?= readable_date($payment['created_at']) ?></div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label for="payment_date">Settlement Date</label>
                        <div class="form-control-static"><?= readable_date($payment['settlement_date']) ?></div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('payment_category') == '' ?: 'has-error'; ?>">
                        <label for="payment_category">Payment Category</label>
                        <select class="form-control select2" id="payment_category" name="payment_category"
                                data-placeholder="Select payment category">
                            <option value=""></option>
                            <option value="BILLING" <?= $payment['payment_category'] == 'BILLING' ? 'selected' : '' ?>>
                                BILLING
                            </option>
                            <option value="NON BILLING" <?= $payment['payment_category'] == 'NON BILLING' ? 'selected' : '' ?>>
                                NON BILLING
                            </option>
                        </select>
                        <?= form_error('payment_category', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('payment_type') == '' ?: 'has-error'; ?>">
                        <label for="payment_type">Payment Type</label>
                        <select class="form-control select2" id="payment_type" name="payment_type" data-old="<?= set_value('payment_type', $payment['payment_type']) ?>" data-placeholder="Select payment type">
                            <option value=""></option>
                            <?php if ($payment['payment_category'] == 'BILLING'): ?>
                                <option value="OB TPS" <?= $payment['payment_type'] == 'OB TPS' ? 'selected' : '' ?>>
                                    OB TPS
                                </option>
                                <option value="DISCOUNT" <?= $payment['payment_type'] == 'DISCOUNT' ? 'selected' : '' ?>>
                                    DISCOUNT
                                </option>
                                <option value="DO" <?= $payment['payment_type'] == 'DO' ? 'selected' : '' ?>>
                                    DO
                                </option>
                            <?php else: ?>
                                <option value="DRIVER" <?= $payment['payment_type'] == 'DRIVER' ? 'selected' : '' ?>>
                                    DRIVER
                                </option>
                                <option value="DISPOSITION AND TPS OPERATIONAL" <?= $payment['payment_type'] == 'DISPOSITION AND TPS OPERATIONAL' ? 'selected' : '' ?>>
                                    DISPOSITION AND TPS OPERATIONAL
                                </option>
                            <?php endif; ?>
                            <option value="AS PER BILL" <?= $payment['payment_type'] == 'AS PER BILL' ? 'selected' : '' ?>>
                                AS PER BILL
                            </option>
                            <option value="TOOLS AND EQUIPMENTS" <?= $payment['payment_type'] == 'TOOLS AND EQUIPMENTS' ? 'selected' : '' ?>>
                                TOOLS AND EQUIPMENTS
                            </option>
                            <option value="MISC" <?= $payment['payment_type'] == 'MISC' ? 'selected' : '' ?>>MISC
                            </option>
                        </select>
                        <?= form_error('payment_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div id="booking-detail-data" style="display: none">
                <?php $this->load->view('payment/_booking_detail') ?>
            </div>
            <div class="form-group <?= form_error('amount') == '' ?: 'has-error'; ?>">
                <label for="amount">Amount Realization</label>
                <input type="text" class="form-control currency" id="amount" name="amount"
                       placeholder="Amount of payment"
                       required maxlength="50"
                       value="<?= set_value('amount', if_empty(numerical($payment['amount'], 2, true), '', 'Rp. ')) ?>">
                <?= form_error('amount', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('payment_date') == '' ?: 'has-error'; ?>">
                <label for="payment_date">Payment Date</label>
                <input type="text" class="form-control daterangepicker2" id="payment_date" name="payment_date"
                       placeholder="Select payment date" autocomplete="off"
                       required maxlength="50" value="<?= set_value('payment_date', format_date($payment['payment_date'], 'd F Y H:i')) ?>">
                <?= form_error('payment_date', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('no_invoice') == '' ?: 'has-error'; ?>">
                <label for="no_invoice">No Invoice</label>
                <input type="text" class="form-control" id="no_invoice" name="no_invoice"
                       placeholder="Optional invoice number"
                       maxlength="50" value="<?= set_value('no_invoice', $payment['no_invoice']) ?>">
                <?= form_error('no_invoice', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('attachment') == '' ?: 'has-error'; ?>">
                <label for="attachment">Realization Attachment</label>
                <input type="file" id="attachment"
                       name="attachment" <?= $payment['payment_type'] == 'OB TPS' ? 'required' : '' ?>>
                <p class="form-control-static">
                    <?php if (!empty($payment['attachment_realization'])): ?>
                        <a href="<?= base_url('uploads/payment_realizations/' . $payment['attachment_realization']) ?>"
                           target="_blank">
                            <?= $payment['attachment_realization'] ?>
                        </a>
                    <?php endif; ?>
                </p>
                <?= form_error('attachment', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Payment description"
                          required maxlength="500"><?= set_value('description', if_empty($payment['invoice_description'], $payment['description'])) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" class="btn btn-warning pull-right">Update Payment</button>
        </div>
    </form>
</div>
<!-- /.box -->

<script src="<?= base_url('assets/app/js/payment.js') ?>" defer></script>
