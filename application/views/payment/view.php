<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Payment</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_EDIT)): ?>
            <a href="<?= site_url('payment/edit/' . $payment['id']) ?>" class="btn btn-primary pull-right">
                Edit Payment
            </a>
        <?php endif ?>
    </div>
    <div class="box-body">
        <div role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Booking</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php if(!empty($payment['id_booking'])): ?>
                                    <a href="<?= site_url('booking/view/' . $payment['id_booking']) ?>">
                                        <?= $payment['no_booking'] ?> (<?= $payment['no_reference'] ?>)
                                    </a>
                                <?php else: ?>
                                    <a href="<?= site_url('upload/view/' . $payment['id_upload']) ?>">
                                        <?= $payment['no_upload'] ?> (<?= $payment['upload_description'] ?>)
                                    </a>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">No Payment</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $payment['no_payment'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Payment Category</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $payment['payment_category'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Payment Type</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $payment['payment_type'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Ask Payment To</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($payment['ask_payment'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Payment Method</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= (!empty($payment['payment_method']) ? ($payment['payment_method'] == 'CASH' ? 'PERANTARA' : $payment['payment_method'] ) : '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Bank Name</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($payment['bank_name'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Bank Holder's Name</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($payment['holder_name'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Bank Account Number</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($payment['bank_account_number'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Withdrawal Date</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= readable_date($payment['withdrawal_date']) ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Payment Date</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($payment['payment_date']) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Settlement Plan</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($payment['settlement_date'], false) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Realized At (Settlement)</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($payment['realized_at'], false) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Charge Position</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($payment['charge_position'], '-') ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Amount Realization</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                Rp. <?= numerical($payment['amount'], 0, true) ?>
                                <?php if ($payment['payment_type'] == "OB TPS PERFORMA" && $payment['amount'] == 0): ?>
                                    &nbsp;(<strong class="text-success">Paid By Customer</strong>)
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Amount Request</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                Rp. <?= numerical($payment['amount_request'], 0, true) ?>
                                <?php if ($payment['payment_type'] == "OB TPS PERFORMA" && $payment['amount'] == 0): ?>
                                    &nbsp;(<strong class="text-success">Paid By Customer</strong>)
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Day until realized/today</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $payment['elapsed_day_until_realized'] ?> day(s)</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Applicant</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $payment['applicant_name'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">PIC</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $payment['pic_name'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Validator</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($payment['validator_name'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($payment['description'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Invoice Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($payment['invoice_description'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Attachment</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php if (empty($payment['attachment'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="<?= base_url('uploads/payments/' . $payment['attachment']) ?>">
                                        Download Attachment
                                    </a>
                                <?php endif ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Attachment Realization</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php if (empty($payment['attachment_realization'])): ?>
                                    -
                                <?php else: ?>
                                    <a href="<?= base_url('uploads/payment_realizations/' . $payment['attachment_realization']) ?>">
                                        Download Attachment
                                    </a>
                                <?php endif ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Status Payment</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php
                                $statuses = [
                                    'DRAFT' => 'default',
                                    'APPROVED' => 'success',
                                    'REJECTED' => 'danger',
                                ]
                                ?>
                                <span class="label label-<?= $statuses[$payment['status']] ?>">
                                    <?= $payment['status'] ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Status Realization</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php if ($payment['is_submitted'] && !$payment['is_realized']): ?>
                                    <span class="label label-<?= $payment['is_submitted'] && !empty($payment['submitted_at']) ? 'warning' : 'default' ?>">
                                        <?= $payment['is_submitted'] ? 'SUBMITTED' : 'REJECTED' ?>
                                    </span>
                                <?php else: ?>
                                    <span class="label label-<?= $payment['is_realized'] ? 'primary' : 'default' ?>">
                                        <?= $payment['is_realized'] ? 'REALIZED' : 'REQUESTED' ?>
                                    </span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Status Check</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php
                                $statuses = [
                                    PaymentModel::STATUS_PENDING => 'default',
                                    PaymentModel::STATUS_ASK_APPROVAL => 'warning',
                                    PaymentModel::STATUS_APPROVED => 'success',
                                    PaymentModel::STATUS_REJECTED => 'danger',
                                    PaymentModel::STATUS_SUBMITTED => 'warning',
                                    PaymentModel::STATUS_SUBMISSION_REJECTED => 'danger',
                                ]
                                ?>
                                <span class="label label-<?= $statuses[$payment['status_check']] ?>">
                                    <?= $payment['status_check'] ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= format_date($payment['created_at'], 'd F Y H:i') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Updated At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty(format_date($payment['updated_at'], 'd F Y H:i'), '-') ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <?php if($payment['payment_type'] == "OB TPS PERFORMA" && $payment['amount_request'] > 0): ?>
            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">TPS Realization (Performa)</h3>
                </div>
                <div class="box-body form-horizontal form-view">
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label class="col-sm-4">Payment Date</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= if_empty($payment['tps_invoice_payment_date'], '-') ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Bank Name</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static"><?= if_empty($payment['tps_invoice_bank_name'], '-') ?></p>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-sm-4">Payment Attachment</label>
                                <div class="col-sm-8">
                                    <p class="form-control-static">
                                        <?php if (empty($payment['tps_invoice_attachment'])): ?>
                                            -
                                        <?php else: ?>
                                            <a href="<?= asset_url($payment['tps_invoice_attachment']) ?>">
                                                Download Attachment
                                            </a>
                                        <?php endif ?>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        <?php endif; ?>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Status Histories</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable responsive">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>Status</th>
                        <th>Description</th>
                        <th>Data</th>
                        <th>Created At</th>
                        <th>Created By</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php foreach ($statusHistories as $status): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <span class="label label-<?= get_if_exist($statuses, $status['status'], 'default') ?>">
                                    <?= $status['status'] ?>
                                </span>
                            </td>
                            <td><?= if_empty($status['description'], '-') ?></td>
                            <td>
                                <?php if($status['status'] == PaymentModel::STATUS_SUBMITTED): ?>
                                    <?php if(is_array($status['data']) && !empty($status['data']['submission_attachment'])): ?>
                                        <a href="<?= base_url("uploads/{$payment['submission_attachment']}") ?>">
                                            Attachment
                                        </a>
                                    <?php else: ?>
                                        -
                                    <?php endif; ?>
                                <?php else: ?>
                                    -
                                <?php endif; ?>
                            </td>
                            <td><?= format_date($status['created_at'], 'd F Y H:i') ?></td>
                            <td><?= $status['creator_name'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                    <?php if(empty($status)): ?>
                        <tr>
                            <td colspan="5">No statuses available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="box-footer row-payment" data-id="<?= $payment['id'] ?>" data-label="<?= if_empty($payment['no_booking'], $payment['no_upload']) . ' - ' . $payment['payment_type'] ?>">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_VALIDATE) && $payment['status'] != 'APPROVED'): ?>
            <div class="pull-right">
                <a href="<?= site_url('payment/validate/approve/' . $payment['id']) ?>" class="btn btn-success btn-validate" data-id="<?= $payment['id'] ?>" data-validate="approve" data-label="<?= if_empty($payment['no_booking'], $payment['no_upload']) . ' - ' . $payment['payment_type'] ?>">
                    <i class="fa ion-checkmark mr10"></i>Approve
                </a>
                <a href="<?= site_url('payment/validate/reject/' . $payment['id']) ?>" class="btn btn-danger btn-validate" data-id="<?= $payment['id'] ?>" data-validate="reject" data-label="<?= if_empty($payment['no_booking'], $payment['no_upload']) . ' - ' . $payment['payment_type'] ?>">
                    <i class="fa ion-close mr10"></i>Reject
                </a>
            </div>
        <?php endif ?>
    </div>
</div>


<script src="<?= base_url('assets/app/js/payment.js?v=2') ?>" defer></script>
<?php if (AuthorizationModel::isAuthorized(PERMISSION_PAYMENT_VALIDATE)): ?>
    <?php $this->load->view('template/_modal_validate') ?>
    <script src="<?= base_url('assets/app/js/validation.js') ?>" defer></script>
<?php endif ?>
