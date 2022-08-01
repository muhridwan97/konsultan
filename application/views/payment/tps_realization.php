<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">TPS Payment Realization</h3>
    </div>
    <form action="<?= site_url('payment/update_tps_realization/' . $payment['id']) ?>" role="form" method="post" enctype="multipart/form-data">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="row">
                <div class="col-sm-6">
                    <div class="form-group <?= form_error('tps_invoice_payment_date') == '' ?: 'has-error'; ?>">
                        <label for="tps_invoice_payment_date">Payment Date</label>
                        <input type="text" class="form-control datepicker" id="tps_invoice_payment_date" name="tps_invoice_payment_date"
                               placeholder="Select payment date" autocomplete="off"
                               required maxlength="50" value="<?= set_value('tps_invoice_payment_date', format_date($payment['tps_invoice_payment_date'], 'd F Y')) ?>">
                        <?= form_error('tps_invoice_payment_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group <?= form_error('tps_invoice_bank_name') == '' ?: 'has-error'; ?>">
                        <label for="tps_invoice_bank_name">Bank Name</label>
                        <input type="text" class="form-control" id="tps_invoice_bank_name" name="tps_invoice_bank_name"
                               placeholder="Input bank name"
                               required maxlength="50" value="<?= set_value('tps_invoice_bank_name', $payment['tps_invoice_bank_name']) ?>">
                        <?= form_error('tps_invoice_bank_name', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('tps_invoice_attachment') == '' ?: 'has-error'; ?>">
                <label for="tps_invoice_attachment">Invoice Attachment</label>
                <input type="file" id="tps_invoice_attachment"
                       name="tps_invoice_attachment" <?= $payment['payment_type'] == 'OB TPS' ? 'required' : '' ?>>
                <?php if (!empty($payment['tps_invoice_attachment'])): ?>
                    <p class="form-control-static">
                        Attachment: <a href="<?= asset_url($payment['tps_invoice_attachment']) ?>"
                           target="_blank">
                            <?= basename($payment['tps_invoice_attachment']) ?>
                        </a>
                    </p>
                <?php endif; ?>
                <?= form_error('tps_invoice_attachment', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" class="btn btn-warning pull-right">
                Update Payment
            </button>
        </div>
    </form>
</div>
<!-- /.box -->

<script src="<?= base_url('assets/app/js/payment.js') ?>" defer></script>
