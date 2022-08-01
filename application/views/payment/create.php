<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create New Payment</h3>
    </div>
    <form action="<?= site_url('payment/save') ?>" role="form" class="need-validation" method="post" enctype="multipart/form-data" id="form-payment">
        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('payment_category') == '' ?: 'has-error'; ?>">
                        <label for="payment_category">Payment Category</label>
                        <select class="form-control select2" id="payment_category" name="payment_category" data-placeholder="Select payment category" style="width: 100%">
                            <option value=""></option>
                            <option value="BILLING"<?= set_select('payment_category', 'BILLING') ?>>BILLING</option>
                            <option value="NON BILLING"<?= set_select('payment_category', 'NON BILLING') ?>>NON BILLING</option>
                        </select>
                        <?= form_error('payment_category', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6" id="field-payment-type">
                    <div class="form-group <?= form_error('payment_type') == '' ?: 'has-error'; ?>">
                        <label for="payment_type">Payment Type</label>
                        <select class="form-control select2" id="payment_type" name="payment_type" data-old="<?= set_value('payment_type') ?>" data-placeholder="Select payment type" style="width: 100%">
                            <option value=""></option>
                        </select>
                        <?= form_error('payment_type', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-3" id="field-paid-by-customer" style="display: none">
                    <div class="form-group <?= form_error('paid_by_customer') == '' ?: 'has-error'; ?>">
                        <label for="include_realization">Set Realization</label>
                        <div class="checkbox icheck" style="margin-top: 5px">
                            <label>
                                <input type="checkbox" name="paid_by_customer" id="paid_by_customer" value="1"
                                    <?= set_checkbox('paid_by_customer', 1); ?>>
                                Paid By Customer
                            </label>
                        </div>
                        <?= form_error('paid_by_customer', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

            <div id="field-booking" style="display: none" class="form-group <?= form_error('booking') == '' ?: 'has-error'; ?>">
                <label for="booking">Booking</label>
                <select class="form-control select2" id="booking" name="booking" data-placeholder="Select related booking" style="width: 100%">
                    <option value=""></option>
                    <?php foreach ($bookings as $booking): ?>
                        <option value="<?= $booking['id_booking'] ?>" <?= set_select('booking', $booking['id_booking'], (isset($_GET['booking_id']) ? $_GET['booking_id'] : '') == $booking['id_booking']) ?>>
                            <?= $booking['no_booking'] . ' (' . if_empty($booking['no_reference'], '-') . ')' . ' - ' . if_empty($booking['owner_name'], $booking['customer_name']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="alert alert-warning mt10">
                    <ul style="padding-left: 10px">
                        <li>Only booking with available stock will show up in the list.</li>
                        <li>OB TPS Payment for booking type with DO should submitted by assigned user (from booking assignment).</li>
                        <li>User who has "realized" permissions: allowed taken empty stock from last transaction 21 passed days.</li>
                        <li>Empty Container Repair: any user allowed taken empty stock booking from last transaction 1 passed days.</li>
                        <li>After invoice created, payment data cannot be added anymore.</li>
                    </ul>
                </div>
                <?= form_error('booking', '<span class="help-block">', '</span>'); ?>
            </div>

            <div id="field-upload" style="display: none" class="form-group <?= form_error('upload') == '' ?: 'has-error'; ?>">
                <label for="upload">Upload</label>
                <select class="form-control select2" id="upload" name="upload" data-placeholder="Select related upload" style="width: 100%">
                    <option value=""></option>
                    <?php foreach ($uploads as $upload): ?>
                        <option value="<?= $upload['id'] ?>"<?= set_select('upload', $upload['id']) ?>>
                            <?= $upload['no_upload'] . ' (' . $upload['description'] . ')' . ' - ' . $upload['name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <div class="alert alert-warning mt10">
                    Booking type with DO setup must input payment before the document (DO) uploaded.
                </div>
                <?= form_error('upload', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="panel panel-primary" style="display: none">
                <div class="panel-heading">Last Payment Transaction</div>
                <div class="panel-body">
                    <div id="booking-payment-wrapper"></div>
                </div>
            </div>
            <div id="booking-detail-data"></div>
            <div class="form-group <?= form_error('ask_payment') == '' ?: 'has-error'; ?>">
                <label for="ask_payment">Ask Payment To</label>
                <select class="form-control select2" id="ask_payment" required name="ask_payment" data-placeholder="Select related ask payment">
                    <option value=""></option>
                    <option value="BANK" <?= set_select('ask_payment', 'BANK') ?>>BANK</option>
                    <option value="CASH" <?= set_select('ask_payment', 'CASH') ?>>CASH</option>
                </select>
                <?= form_error('ask_payment', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="row group-bank">
                <div class="col-md-2">
                    <div class="form-group <?= form_error('payment_method') == '' ?: 'has-error'; ?>">
                        <label for="payment_method">Payment Method</label>
                          <select class="form-control select2" id="payment_method" name="payment_method" data-placeholder="Select related payment method">
                            <option value=""></option>
                            <option value="EDC" <?= set_select('payment_method', 'EDC') ?>>EDC</option>
                            <option value="PERANTARA" <?= set_select('payment_method', 'PERANTARA') ?>>PERANTARA</option>
                            <option value="TRANSFER" <?= set_select('payment_method', 'TRANSFER') ?>>TRANSFER</option>
                        </select>
                        <?= form_error('payment_method', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group <?= form_error('bank_name') == '' ?: 'has-error'; ?>">
                        <label for="bank_name">Bank Name</label>
                        <input type="text" class="form-control" id="bank_name" name="bank_name"
                               placeholder="Bank Name" maxlength="300" value="<?= set_value('bank_name') ?>">
                        <?= form_error('bank_name', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group <?= form_error('account_holder_name') == '' ?: 'has-error'; ?>">
                        <label for="account_holder_name">Account Holder's Name</label>
                        <input type="text" class="form-control" id="account_holder_name" name="account_holder_name"
                               placeholder="Account Holder's Name" maxlength="300" value="<?= set_value('account_holder_name') ?>">
                        <?= form_error('account_holder_name', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group <?= form_error('account_number') == '' ?: 'has-error'; ?>">
                        <label for="account_number">Account Number</label>
                        <input type="text" maxlength="100" class="form-control" id="account_number" name="account_number"
                               placeholder="Account Number" value="<?= set_value('account_number') ?>">
                        <?= form_error('account_number', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="form-group <?= form_error('withdrawal_date') == '' ?: 'has-error'; ?>">
                        <label for="withdrawal_date">Withdrawal Date</label>
                        <input type="text" class="form-control datepicker" id="withdrawal_date" name="withdrawal_date"
                               placeholder="Select withdrawal date" autocomplete="off"
                               required maxlength="50" value="<?= set_value('withdrawal_date') ?>">
                        <?= form_error('withdrawal_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-2">
                    <div class="input-group bootstrap-timepicker <?= form_error('withdrawal_time') == '' ?: 'has-error'; ?>">
                        <label for="withdrawal_time">Withdrawal Time</label>
                        <input type="text" class="form-control time-picker" id="withdrawal_time" name="withdrawal_time" placeholder="Withdrawal Time" value="<?= set_value('withdrawal_time') ?>">
                    </div>
                    <?= form_error('withdrawal_time', '<span class="help-block">', '</span>'); ?>
                </div>
            </div>

            <?php $hasValidatePermission = AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_VALIDATE) ?>

            <div class="row">
                <div class="<?= $hasValidatePermission ? 'col-md-8' : 'col-md-12' ?>">
                    <div class="form-group <?= form_error('amount') == '' ?: 'has-error'; ?>">
                        <label for="amount">Amount Request</label>
                        <input type="text" class="form-control currency" id="amount" name="amount"
                               placeholder="Amount of payment"
                               required maxlength="50" value="<?= set_value('amount') ?>">
                        <?= form_error('amount', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <?php if($hasValidatePermission): ?>
                    <div class="col-md-4">
                        <div class="form-group <?= form_error('include_realization') == '' ?: 'has-error'; ?>">
                            <label for="include_realization">Set Realization</label>
                            <div class="checkbox icheck" style="margin-top: 5px">
                                <label>
                                    <input type="checkbox" name="include_realization" class="include_realization" id="include_realization" value="1"
                                        <?php echo set_checkbox('include_realization', 1); ?>>
                                    Include Realization
                                </label>
                            </div>
                            <?= form_error('include_realization', '<span class="help-block">', '</span>'); ?>
                        </div>
                    </div>
                <?php endif; ?>
            </div>

            <?php if($hasValidatePermission): ?>
                <div id="field-payment-date" class="form-group <?= form_error('payment_date') == '' ?: 'has-error'; ?>" style="display: none">
                    <label for="payment_date">Payment Date (Invoice)</label>
                    <input type="text" class="form-control daterangepicker2" id="payment_date" name="payment_date" autocomplete="off"
                           placeholder="Select payment date" maxlength="50" value="<?= set_value('payment_date') ?>">
                    <?= form_error('payment_date', '<span class="help-block">', '</span>'); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-8">
                    <div class="form-group <?= form_error('no_invoice') == '' ?: 'has-error'; ?>">
                        <label for="no_invoice">No Invoice</label>
                        <input type="text" class="form-control" id="no_invoice" name="no_invoice"
                               placeholder="Optional invoice number"
                               maxlength="50" value="<?= set_value('no_invoice') ?>">
                        <?= form_error('no_invoice', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group <?= form_error('settlement_date') == '' ?: 'has-error'; ?>">
                        <label for="settlement_date">Settlement Plan</label>
                        <input type="text" class="form-control datepicker" id="settlement_date" name="settlement_date"
                               placeholder="Pick settlement date" autocomplete="off"
                               required maxlength="50" value="<?= set_value('settlement_date') ?>">
                        <?= form_error('settlement_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('attachment') == '' ?: 'has-error'; ?>">
                <label for="attachment">Attachment</label>
                <input type="file" id="attachment" name="attachment"
                       placeholder="Select payment attachment">
                <?= form_error('attachment', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('pic') == '' ?: 'has-error'; ?>" style="<?= AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_PIC) ? '': 'display: none'?>">
                <label for="pic">PIC</label>
                <select class="form-control select2" id="pic" name="pic" data-placeholder="Select pic" style="width: 100%">
                    <option value=""></option>
                    <?php foreach ($pics as $pic): ?>
                        <option value="<?= $pic['id'] ?>"<?= set_select('pic', $pic['id'], $pic['id'] == UserModel::authenticatedUserData('id')) ?>>
                            <?= $pic['name']?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?= form_error('pic', '<span class="help-block">', '</span>'); ?>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Payment Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Payment description"
                          required maxlength="500"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>
        </div>

        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right" id="btn-save-payment">Save Payment</button>
        </div>
    </form>
</div>

<script src="<?= base_url('assets/app/js/payment.js?v=6') ?>" defer></script>
