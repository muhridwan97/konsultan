<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Payment Bank</h3>
    </div>
    <form action="<?= site_url('payment/update-bank/' . $payment['id']) ?>" role="form" method="post" id="form-payment">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label>Booking</label>
                        <div class="form-control-static">
                            <input type="hidden" id="booking" value="<?= $payment['id_booking'] ?>">
                            <a href="<?= site_url(empty($payment['id_booking']) ? 'upload/view/' . $payment['id_upload'] : 'booking/view/' . $payment['id_booking']) ?>">
                                <?= if_empty($payment['no_booking'], $payment['no_upload']) ?> (<?= if_empty($payment['no_reference'], $payment['upload_description']) ?>)
                            </a>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Category</label>
                        <div class="form-control-static">
                            <?= $payment['payment_category'] ?>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="form-group">
                        <label>Type</label>
                        <div class="form-control-static">
                            <?= $payment['payment_type'] ?>
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
            <div class="form-group <?= form_error('amount') == '' ?: 'has-error'; ?>">
                <label for="amount">Amount Transfer (Realization)</label>
                <input type="text" class="form-control currency" id="amount" name="amount"
                       placeholder="Amount of payment" required maxlength="50"
                       value="<?= set_value('amount', if_empty(numerical($payment['amount'], 2, true), '', 'Rp. ')) ?>">
                <?= form_error('amount', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Payment Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Payment description"
                          required maxlength="500"><?= set_value('description', $payment['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('bank_account') == '' ?: 'has-error'; ?>">
                <label for="bank_account">Bank Account</label>
                <select class="form-control select2" id="bank_account" name="bank_account"
                        data-placeholder="Select payment bank" required>
                    <option value=""></option>
                    <?php foreach ($bankAccounts as $bankAccount): ?>
                        <option value="<?= $bankAccount['id'] ?>" <?= set_select('bank_account', $bankAccount['id'], $bankAccount['id'] == $payment['id_bank_account']) ?>>
                            <?= $bankAccount['bank'] ?> <?= if_empty($bankAccount['account_number'], '', '(', ')', true) ?> - <?= $bankAccount['bank_type'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="help-block">
                    Type bank regular will notify (email) to
                    <a href="mailto:direktur@transcon-indonesia.com">direktur@transcon-indonesia.com</a>
                </span>
                <?= form_error('bank_account', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group">
                <div class="checkbox icheck">
                    <label for="send_immediately">
                        <input type="checkbox" id="send_immediately" name="send_immediately" value="1">
                        Ask approval immediately
                    </label>
                </div>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" class="btn btn-warning pull-right" data-toggle="one-touch">Update Bank</button>
        </div>
    </form>
</div>
