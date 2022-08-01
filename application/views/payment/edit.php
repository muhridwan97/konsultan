<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Payment</h3>
    </div>
    <form action="<?= site_url('payment/update/'.$payment['id']) ?>" role="form" method="post" enctype="multipart/form-data" id="form-payment" class="edit">
        <input type="hidden" name="id" id="id" value="<?= $payment['id'] ?>">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>
            <?php $isRealized = !empty($payment['amount']) && $payment['amount'] > 0 ?>

            <?php if (!$isRealized): ?>
            <?php if(!empty($payment['id_booking'])): ?>
                <div class="form-group <?= form_error('booking') == '' ?: 'has-error'; ?>">
                    <label for="booking">Booking</label>
                    <select class="form-control select2" id="booking" name="booking" data-placeholder="Select related booking">
                        <option value=""></option>
                        <option value="<?= $booking['id'] ?>" <?= set_select('booking', $booking['id'], $payment['id_booking'] == $booking['id']) ?>>
                             <?= $booking['no_booking'] . ' (' . if_empty($booking['no_reference'], '-') .')' . ' - ' . if_empty($booking['customer_name'], '-') ?>
                        </option>
                    </select>
                    <?= form_error('booking', '<span class="help-block">', '</span>'); ?>
                </div>
            <?php else: ?>
                <div class="form-group <?= form_error('upload') == '' ?: 'has-error'; ?>">
                    <label for="upload">Upload</label>
                    <select class="form-control select2" id="upload" name="upload" data-placeholder="Select related upload" style="width: 100%">
                        <option value=""></option>
                        <?php foreach ($uploads as $upload): ?>
                            <option value="<?= $upload['id'] ?>"<?= set_select('upload', $upload['id'], $upload['id'] == $payment['id_upload']) ?>>
                                <?= $upload['no_upload'] . ' (' . $upload['description'] . ')' . ' - ' . $upload['name'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('upload', '<span class="help-block">', '</span>'); ?>
                </div>
            <?php endif; ?>
            <?php endif; ?>

            <div class="form-group <?= form_error('ask_payment') == '' ?: 'has-error'; ?>">
                <label for="ask_payment">Ask Payment To</label>
                <select class="form-control select2" id="ask_payment" required name="ask_payment" data-placeholder="Select related ask payment">
                    <option value=""></option>
                    <option value="BANK" <?= $payment['ask_payment'] == 'BANK' ? 'selected' : '' ?>>BANK</option>
                    <option value="CASH" <?= $payment['ask_payment'] == 'CASH' ? 'selected' : '' ?>>CASH</option>
                </select>
                <?= form_error('ask_payment', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('payment_category') == '' ?: 'has-error'; ?>">
                        <label for="payment_category">Payment Category</label>
                        <select class="form-control select2" id="payment_category" name="payment_category" data-placeholder="Select payment category">
                            <option value=""></option>
                            <option value="BILLING" <?= $payment['payment_category'] == 'BILLING' ? 'selected' : '' ?>>BILLING</option>
                            <option value="NON BILLING" <?= $payment['payment_category'] == 'NON BILLING' ? 'selected' : '' ?>>NON BILLING</option>
                        </select>
                        <?= form_error('payment_category', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('payment_type') == '' ?: 'has-error'; ?>">
                        <label for="payment_type">Payment Type</label>
                        <select class="form-control select2" id="payment_type" name="payment_type" data-placeholder="Select payment type">
                            <option value=""></option>
                            <?php if($payment['payment_category'] == 'BILLING'): ?>
                                <option value="OB TPS" <?= $payment['payment_type'] == 'OB TPS' ? 'selected' : '' ?>>OB TPS</option>
                                <option value="OB TPS PERFORMA" <?= $payment['payment_type'] == 'OB TPS PERFORMA' ? 'selected' : '' ?>>OB TPS PERFORMA</option>
                                <option value="DISCOUNT" <?= $payment['payment_type'] == 'DISCOUNT' ? 'selected' : '' ?>>DISCOUNT</option>
                                <option value="DO" <?= $payment['payment_type'] == 'DO' ? 'selected' : '' ?>>DO</option>
                                <option value="EMPTY CONTAINER REPAIR" <?= $payment['payment_type'] == 'EMPTY CONTAINER REPAIR' ? 'selected' : '' ?>>EMPTY CONTAINER REPAIR</option>
                            <?php else: ?>
                                <option value="DRIVER" <?= $payment['payment_type'] == 'DRIVER' ? 'selected' : '' ?>>DRIVER</option>
                                <option value="DISPOSITION AND TPS OPERATIONAL" <?= $payment['payment_type'] == 'DISPOSITION AND TPS OPERATIONAL' ? 'selected' : '' ?>>DISPOSITION AND TPS OPERATIONAL</option>
                            <?php endif; ?>
                            <option value="AS PER BILL" <?= $payment['payment_type'] == 'AS PER BILL' ? 'selected' : '' ?>>AS PER BILL</option>
                            <option value="TOOLS AND EQUIPMENTS" <?= $payment['payment_type'] == 'TOOLS AND EQUIPMENTS' ? 'selected' : '' ?>>TOOLS AND EQUIPMENTS</option>
                            <option value="MISC" <?= $payment['payment_type'] == 'MISC' ? 'selected' : '' ?>>MISC</option>
                        </select>
                        <?= form_error('payment_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <?php if (!$isRealized): ?>
            <div class="row group-bank">
                <div class="col-md-2">
                    <div class="form-group <?= form_error('payment_method') == '' ?: 'has-error'; ?>">
                        <label for="payment_method">Payment Method</label>
                          <select class="form-control select2" id="payment_method" name="payment_method" data-placeholder="Select related payment method">
                            <option value=""></option>
                            <option value="EDC" <?= $payment['payment_method'] == 'EDC' ? 'selected' : '' ?>>EDC</option>
                            <option value="PERANTARA" <?= $payment['payment_method'] == 'PERANTARA' ? 'selected' : '' ?>>PERANTARA</option>
                            <option value="TRANSFER" <?= $payment['payment_method'] == 'TRANSFER' ? 'selected' : '' ?>>TRANSFER</option>
                        </select>
                        <?= form_error('payment_method', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                 <div class="col-md-2">
                    <div class="form-group <?= form_error('bank_name') == '' ?: 'has-error'; ?>">
                        <label for="bank_name">Bank Name</label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name"
                               placeholder="Bank Name" maxlength="300" value="<?= set_value('bank_name', $payment['bank_name']) ?>">
                        <?= form_error('bank_name', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group <?= form_error('account_holder_name') == '' ?: 'has-error'; ?>">
                        <label for="account_holder_name">Account Holder's Name</label>
                        <input type="text" class="form-control" id="account_holder_name" name="account_holder_name"
                               placeholder="Account Holder's Name" maxlength="300" value="<?= set_value('account_number', $payment['holder_name']) ?>">
                        <?= form_error('account_holder_name', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group <?= form_error('account_number') == '' ?: 'has-error'; ?>">
                        <label for="account_number">Account Number</label>
                        <input type="varchar" class="form-control" id="account_number" name="account_number"
                               placeholder="Account Number" maxlength="100" value="<?= set_value('account_number', $payment['bank_account_number']) ?>">
                        <?= form_error('account_number', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <?php 
                    $withdrawal_date = !empty($payment['withdrawal_date']) == true ?  date('d F Y', strtotime($payment['withdrawal_date'])) : null ; 
                    $withdrawal_time = !empty($payment['withdrawal_date']) == true ?  date('H:i', strtotime($payment['withdrawal_date'])) : null ; 
                ?>
                <div class="col-md-2">
                    <div class="form-group <?= form_error('withdrawal_date') == '' ?: 'has-error'; ?>">
                        <label for="withdrawal_date">Withdrawal Date</label>
                        <input type="text" class="form-control datepicker" id="withdrawal_date" name="withdrawal_date"
                               placeholder="Select withdrawal date" autocomplete="off"
                               required maxlength="50" value="<?= set_value('withdrawal_date', $withdrawal_date) ?>">
                        <?= form_error('withdrawal_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group bootstrap-timepicker <?= form_error('withdrawal_time') == '' ?: 'has-error'; ?>">
                        <label for="withdrawal_time">Withdrawal Time</label>
                        <input type="text" class="form-control time-picker" id="withdrawal_time" name="withdrawal_time" placeholder="Withdrawal Time" value="<?= set_value('withdrawal_time', $withdrawal_time) ?>">
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <?php $hasValidatePermission = AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_VALIDATE) ?>

            <?php if ($isRealized): ?>
                <?php if($hasValidatePermission): ?>
                    <div class="form-group <?= form_error('payment_date') == '' ?: 'has-error'; ?>">
                        <label for="payment_date">Payment Date (Invoice)</label>
                        <input type="text" class="form-control daterangepicker2" id="payment_date" name="payment_date"
                               placeholder="Select payment date" autocomplete="off"
                               required maxlength="50" value="<?= set_value('payment_date', format_date($payment['payment_date'], 'd F Y H:i:s')) ?>">
                        <?= form_error('payment_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                <?php endif; ?>
                <div class="row">
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="amount">Amount Request</label>
                            <p class="form-control-static">Rp. <?= numerical($payment['amount_request'], 0) ?></p>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="amount">Requested At</label>
                            <p class="form-control-static"><?= readable_date($payment['created_at']) ?></p>
                        </div>
                    </div>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('amount') == '' ?: 'has-error'; ?>">
                        <label for="amount">Amount <?= $isRealized ? 'Realized' : 'Request' ?></label>
                        <input type="text" class="form-control currency" id="amount" name="amount"
                               placeholder="Amount of payment"
                               required maxlength="50" value="<?= set_value('amount', 'Rp. ' . numerical($isRealized ? $payment['amount'] : $payment['amount_request'], 0)) ?>">
                        <?= form_error('amount', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('settlement_date') == '' ?: 'has-error'; ?>">
                        <label for="settlement_date">Settlement Plan</label>
                        <input type="text" class="form-control datepicker" id="settlement_date" name="settlement_date"
                               placeholder="Pick settlement date" autocomplete="off"
                               required maxlength="50" value="<?= set_value('settlement_date', readable_date($payment['settlement_date'], false)) ?>">
                        <?= form_error('settlement_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div class="form-group <?= form_error('no_invoice') == '' ?: 'has-error'; ?>">
                <label for="no_invoice">No Invoice</label>
                <input type="text" class="form-control" id="no_invoice" name="no_invoice"
                       placeholder="Optional invoice number"
                       maxlength="50" value="<?= set_value('no_invoice', $payment['no_invoice']) ?>">
                <?= form_error('no_invoice', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('attachment') == '' ?: 'has-error'; ?>">
                <label for="attachment">Attachment <?= $isRealized ? 'Realized' : '' ?></label>
                <p class="form-control-static">
                    <?php if (empty($payment[$isRealized ? 'attachment_realization' : 'attachment'])): ?>
                        No uploaded file
                    <?php else: ?>
                        <a href="<?= base_url('uploads/' . ($isRealized ? 'payment_realizations' : 'payments') . '/' . $payment[$isRealized ? 'attachment_realization' : 'attachment']) ?>"
                           target="_blank">
                            <?= $payment[$isRealized ? 'attachment_realization' : 'attachment'] ?>
                        </a>
                    <?php endif; ?>
                </p>
                <input type="file" id="attachment" name="attachment"
                       placeholder="Select payment attachment">
                <?= form_error('attachment', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('pic') == '' ?: 'has-error'; ?>" style="<?= AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_PIC) ? '': 'display: none'?>">
                <label for="pic">PIC</label>
                <select class="form-control select2" id="pic" name="pic" data-placeholder="Select pic" style="width: 100%">
                    <option value=""></option>
                    <?php foreach ($pics as $pic): ?>
                        <option value="<?= $pic['id'] ?>"<?= set_select('pic', $pic['id'], $pic['id'] == $payment['user_pic']) ?>>
                            <?= $pic['name']?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('pic', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Payment Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Payment description"
                          required maxlength="500"><?= set_value('description', $payment['description']) ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-warning pull-right" id="btn-edit-payment">Update Payment</button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/payment.js?v=6') ?>" defer></script>
