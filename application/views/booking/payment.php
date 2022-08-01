<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Booking Payment <?= $booking['no_booking'] ?></h3>
    </div>

    <div class="box-body">
        <?php $this->load->view('booking/_view_header') ?>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Booking Payment</h3>
            </div>
            <div class="box-body">
                <p class="lead mb10">Billing</p>
                <table class="table table-bordered table-striped no-datatable">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Status Payment</th>
                        <th class="text-right">Amount</th>
                        <th>Status Realization</th>
                        <th class="text-right">Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php $totalPaymentRequest = 0; ?>
                    <?php $totalPayment = 0; ?>
                    <?php foreach ($paymentBillings as $payment): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <a href="<?= site_url('payment/view/' . $payment['id']) ?>">
                                    <?= $payment['payment_type'] ?>
                                </a>
                            </td>
                            <td><?= readable_date($payment['payment_date']) ?></td>
                            <td><?= if_empty($payment['description'], '-') ?></td>
                            <td>
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
                            </td>
                            <td class="text-right">Rp. <?= numerical($payment['amount_request'], 0, true) ?></td>
                            <td>
                                <?php if ($payment['is_submitted'] && !$payment['is_realized']): ?>
                                    <span class="label label-<?= $payment['is_submitted'] && !empty($payment['submitted_at']) ? 'warning' : 'default' ?>">
                                        <?= $payment['is_submitted'] ? 'SUBMITTED' : 'REJECTED' ?>
                                    </span>
                                <?php else: ?>
                                    <span class="label label-<?= $payment['is_realized'] ? 'primary' : 'default' ?>">
                                        <?= $payment['is_realized'] ? 'REALIZED' : 'REQUESTED' ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">Rp. <?= numerical($payment['amount'], 0, true) ?></td>
                        </tr>
                        <?php $totalPaymentRequest += $payment['amount_request']; ?>
                        <?php $totalPayment += $payment['amount']; ?>
                    <?php endforeach; ?>

                    <?php if(empty($paymentBillings)): ?>
                    <tr>
                        <td colspan="8">No any payment available</td>
                    </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="4"><strong>Total Payment</strong></td>
                            <td colspan="2" class="text-right"><strong>Rp. <?= numerical($totalPaymentRequest, 0, true) ?></strong></td>
                            <td colspan="2" class="text-right"><strong>Rp. <?= numerical($totalPayment, 0, true) ?></strong></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>

                <p class="lead mt20 mb10">Non Billing</p>
                <table class="table table-bordered table-striped no-datatable">
                    <thead>
                    <tr>
                        <th style="width: 30px">No</th>
                        <th>Type</th>
                        <th>Date</th>
                        <th>Description</th>
                        <th>Status Payment</th>
                        <th class="text-right">Amount</th>
                        <th>Status Realization</th>
                        <th class="text-right">Amount</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; ?>
                    <?php $totalPaymentRequest = 0; ?>
                    <?php $totalPayment = 0; ?>
                    <?php foreach ($paymentNonBillings as $payment): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td>
                                <a href="<?= site_url('payment/view/' . $payment['id']) ?>">
                                    <?= $payment['payment_type'] ?>
                                </a>
                            </td>
                            <td><?= readable_date($payment['payment_date']) ?></td>
                            <td><?= if_empty($payment['description'], '-') ?></td>
                            <td>
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
                            </td>
                            <td class="text-right">Rp. <?= numerical($payment['amount_request'], 0, true) ?></td>
                            <td>
                                <?php if ($payment['is_submitted'] && !$payment['is_realized']): ?>
                                    <span class="label label-<?= $payment['is_submitted'] && !empty($payment['submitted_at']) ? 'warning' : 'default' ?>">
                                        <?= $payment['is_submitted'] ? 'SUBMITTED' : 'REJECTED' ?>
                                    </span>
                                <?php else: ?>
                                    <span class="label label-<?= $payment['is_realized'] ? 'primary' : 'default' ?>">
                                        <?= $payment['is_realized'] ? 'REALIZED' : 'REQUESTED' ?>
                                    </span>
                                <?php endif; ?>
                            </td>
                            <td class="text-right">Rp. <?= numerical($payment['amount'], 0, true) ?></td>
                        </tr>
                        <?php $totalPaymentRequest += $payment['amount_request']; ?>
                        <?php $totalPayment += $payment['amount']; ?>
                    <?php endforeach; ?>

                    <?php if(empty($paymentNonBillings)): ?>
                        <tr>
                            <td colspan="8">No any payment available</td>
                        </tr>
                    <?php else: ?>
                        <tr>
                            <td colspan="4"><strong>Total Payment</strong></td>
                            <td colspan="2" class="text-right"><strong>Rp. <?= numerical($totalPaymentRequest, 0, true) ?></strong></td>
                            <td colspan="2" class="text-right"><strong>Rp. <?= numerical($totalPayment, 0, true) ?></strong></td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="window.history.back();" class="btn btn-primary pull-left">
            Back
        </a>
        <a href="<?= site_url('payment/create?booking_id=' . $booking['id']) ?>" class="btn btn-success pull-right">
            Add Payment
        </a>
    </div>
</div>