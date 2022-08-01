<div class="box box-primary">
    <div class="box-header">
        <h3 class="box-title">Booking Payment Report</h3>
        <div class="pull-right">
            <a href="#form-filter" class="btn btn-primary btn-filter-toggle">
                <?= get_url_param('filter_booking_payment', 0) ? 'Hide' : 'Show' ?> Filter
            </a>
            <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=true" class="btn btn-success">
                Export Excel
            </a>
        </div>
    </div>
    <div class="box-body">
        <?php $this->load->view('report/_filter_booking_payments', ['hidden' => isset($_GET['filter_booking_payments']) ? false : true]) ?>
        <div class="table-responsive">
        <table class="table table-bordered table-striped table-ajax responsive" id="table-report-payment">
            <thead>
            <tr>
                <th style="width: 25px">No</th>
                <th class="type-payment">No Payment</th>
                <th class="type-booking">No Booking</th>
                <th class="type-customer">Customer</th>
                <th>Payment Category</th>
                <th>Payment Type</th>
                <th>Ask Payment To</th>
                <th>Payment Method</th>
                <th>Bank Name</th>
                <th>Bank Holder's Name</th>
                <th>Bank Account Number</th>
                <th class="type-withdrawal-date">Withdrawal Date</th>
                <th class="type-payment-date">Payment Date</th>
                <th class="type-settlement-date">Settlement Plan</th>             
                <th>Charge Position</th>
                <th class="type-amount-realization">Amount Realization</th>
                <th class="type-amount-request">Amount Request</th>
                <th class="type-day-realized">Day until realized/today</th>
                <th>Applicant</th>
                <th>Validator</th>
                <th>Description</th>
                <th>Invoice Description</th>
                <th class="type-attachment">Attachment</th>
                <th class="type-attachment-realization">Attachment Realization</th>
                <th class="type-status-payment">Status Payment</th>
                <th class="type-status-realization">Status Realization</th>
                <th class="type-status-check">Status Check</th>
                <th class="type-created-at">Created At</th>
                <th class="type-updated-at">Updated At</th>   
                <th class="type-approved-at">Approved At</th>
                <th class="type-submitted-at">Submitted At</th>
                <th class="type-realized-at">Realized At (Settlement)</th>
                <th class="type-hour-realized">Hour to Realized</th>
            </tr>
            </thead>
            <!-- <tbody>
                <?php $number = 0; ?>
                <?php foreach ($bookingPayments as $bookingPayment): ?>
                <tr>
                    <td><?= $number = $number+1; ?></td>
                    <td><a href="<?= site_url('payment/view/' . $bookingPayment['id']) ?>" target="_blank">
                            <?= $bookingPayment['no_payment'] ?>
                        </a></td>
                    <td>
                    <?php if(!empty($bookingPayment['id_booking'])): ?>
                        <a href="<?= site_url('booking/view/' . $bookingPayment['id_booking']) ?>" target="_blank">
                            <?= $bookingPayment['no_booking'] ?><br>
                            <span class="text-muted"><?= $bookingPayment['no_reference'] ?></span>
                        </a>
                    <?php else: ?>
                        <a href="<?= site_url('upload/view/' . $bookingPayment['id_upload']) ?>" target="_blank">
                            <?= $bookingPayment['no_upload'] ?> <br>
                            <span class="text-muted"><?= $bookingPayment['upload_description'] ?></span>
                        </a>
                    <?php endif; ?>
                    </td>
                    <td><?= $bookingPayment['payment_category'] ?></td>
                    <td><?= $bookingPayment['payment_type'] ?></td>
                    <td><?= if_empty($bookingPayment['ask_payment'], '-') ?></td>
                    <td><?= (!empty($bookingPayment['payment_method']) ? ($bookingPayment['payment_method'] == 'CASH' ? 'PERANTARA' : $bookingPayment['payment_method'] ) : '-') ?></td>
                    <td><?= if_empty($bookingPayment['bank_name'], '-') ?></td>
                    <td><?= if_empty($bookingPayment['holder_name'], '-') ?></td>
                    <td><?= if_empty($bookingPayment['bank_account_number'], '-') ?></td>
                    <td><?= readable_date($bookingPayment['withdrawal_date']) ?></td>
                    <td><?= readable_date($bookingPayment['payment_date']) ?></td>
                    <td><?= readable_date($bookingPayment['settlement_date'], false) ?></td>
                    <td><?= if_empty($bookingPayment['charge_position'], '-') ?></td>
                    <td>Rp. <?= numerical($bookingPayment['amount'], 0, true) ?></td>
                    <td>Rp. <?= numerical($bookingPayment['amount_request'], 0, true) ?></td>
                    <td><?= $bookingPayment['elapsed_day_until_realized'] ?> days</td>
                    <td><?= if_empty($bookingPayment['applicant_name'], '-') ?></td>
                    <td><?= if_empty($bookingPayment['validator_name'], '-') ?></td>
                    <td><?= if_empty($bookingPayment['description'], '-') ?></td>
                    <td><?= if_empty($bookingPayment['invoice_description'], '-') ?></td>
                    <td>
                        <?php if (empty($bookingPayment['attachment'])): ?>
                            -
                        <?php else: ?>
                            <a href="<?= base_url('uploads/payments/' . $bookingPayment['attachment']) ?>" target="_blank">
                                Download Attachment
                            </a>
                        <?php endif ?></td>
                    <td>
                        <?php if (empty($bookingPayment['attachment_realization'])): ?>
                            -
                        <?php else: ?>
                            <a href="<?= base_url('uploads/payments/' . $bookingPayment['attachment_realization']) ?>" target="_blank">
                                Download Attachment
                            </a>
                        <?php endif ?></td>
                    <td><?php
                        $statuses = [
                            'DRAFT' => 'default',
                            'APPROVED' => 'success',
                            'REJECTED' => 'danger',
                        ]
                        ?>
                        <span class="label label-<?= $statuses[$bookingPayment['status']] ?>">
                            <?= $bookingPayment['status'] ?>
                        </span></td>
                    <td><?php if ($bookingPayment['is_submitted'] && !$bookingPayment['is_realized']): ?>
                            <span class="label label-<?= $bookingPayment['is_submitted'] && !empty($bookingPayment['submitted_at']) ? 'warning' : 'default' ?>">
                                <?= $bookingPayment['is_submitted'] ? 'SUBMITTED' : 'REJECTED' ?>
                            </span>
                        <?php else: ?>
                            <span class="label label-<?= $bookingPayment['is_realized'] ? 'primary' : 'default' ?>">
                                <?= $bookingPayment['is_realized'] ? 'REALIZED' : 'REQUESTED' ?>
                            </span>
                        <?php endif; ?></td>
                    <td><?php
                        $statuses = [
                            PaymentModel::STATUS_PENDING => 'default',
                            PaymentModel::STATUS_ASK_APPROVAL => 'warning',
                            PaymentModel::STATUS_APPROVED => 'success',
                            PaymentModel::STATUS_REJECTED => 'danger',
                            PaymentModel::STATUS_SUBMITTED => 'warning',
                            PaymentModel::STATUS_SUBMISSION_REJECTED => 'danger',
                        ]
                        ?>
                        <span class="label label-<?= $statuses[$bookingPayment['status_check']] ?>">
                            <?= $bookingPayment['status_check'] ?>
                        </span></td>
                    <td><?= format_date($bookingPayment['created_at'], 'd F Y H:i') ?></td>
                    <td><?= if_empty(format_date($bookingPayment['updated_at'], 'd F Y H:i'), '-') ?></td>
                    <td><?= if_empty(format_date($bookingPayment['approved_at'], 'd F Y H:i'), '-') ?></td>
                    <td><?= if_empty(format_date($bookingPayment['date_submitted'], 'd F Y H:i'), '-') ?></td>
                    <td><?= if_empty(format_date($bookingPayment['realized_at'], 'd F Y H:i'), '-') ?></td>
                    <td><?= $bookingPayment['elapsed_hour_until_realized'] ?> hours</td>
                </tr>
                <?php endforeach; ?>
            </tbody> -->
        </table>
        </div>
    </div>
</div>

<script src="<?= base_url('assets/app/js/report-booking-payment.js?v=1') ?>" defer></script>