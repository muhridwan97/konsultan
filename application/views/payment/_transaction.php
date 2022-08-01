<?php if (!empty($paymentBillings)): ?>
    <p class="lead mb10">Billing Payment</p>
    <table class="table table-bordered table-striped no-datatable">
        <thead>
        <tr>
            <th style="width: 30px">No</th>
            <th>Category</th>
            <th>Type</th>
            <th>Date</th>
            <th>Description</th>
            <th>Status</th>
            <th class="text-right">Request</th>
            <th class="text-right">Realized</th>
        </tr>
        </thead>
        <tbody>
        <?php $totalRequest = 0; $total = 0; ?>
        <?php $no = 1; ?>
        <?php foreach ($paymentBillings as $payment): ?>
            <tr <?= isset($currentPaymentId) && $currentPaymentId == $payment['id'] ? 'class="success"' : '' ?>>
                <td><?= $no++ ?></td>
                <td><?= $payment['payment_category'] ?></td>
                <td><?= $payment['payment_type'] ?></td>
                <td><?= format_date($payment['payment_date'], 'd F Y H:i') ?></td>
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
                <td class="text-right">Rp. <?= numerical($payment['amount_request'], 3, true) ?></td>
                <td class="text-right">Rp. <?= numerical($payment['amount'], 3, true) ?></td>
            </tr>
            <?php $totalRequest += $payment['amount_request']; $total += $payment['amount']; ?>
        <?php endforeach ?>
        <?php if (empty($paymentBillings)): ?>
            <tr class="text-center">
                <td colspan="8">No payment occurred before</td>
            </tr>
        <?php else: ?>
            <tr>
                <td colspan="6"><strong>Total last transaction</strong></td>
                <td class="text-right"><strong>Rp. <?= numerical($totalRequest) ?></strong></td>
                <td class="text-right"><strong>Rp. <?= numerical($total) ?></strong></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if (!empty($paymentNonBillings)): ?>
    <p class="lead mb10">Non Billing Payment</p>
    <table class="table table-bordered table-striped no-datatable">
        <thead>
        <tr>
            <th style="width: 30px">No</th>
            <th>Category</th>
            <th>Type</th>
            <th>Date</th>
            <th>Description</th>
            <th>Status</th>
            <th class="text-right">Amount</th>
        </tr>
        </thead>
        <tbody>
        <?php $totalRequest = 0; $total = 0; ?>
        <?php $no = 1; ?>
        <?php foreach ($paymentNonBillings as $payment): ?>
            <tr <?= isset($currentPaymentId) && $currentPaymentId == $payment['id'] ? 'class="success"' : '' ?>>
                <td><?= $no++ ?></td>
                <td><?= $payment['payment_category'] ?></td>
                <td><?= $payment['payment_type'] ?></td>
                <td><?= (new DateTime($payment['payment_date']))->format('d F Y H:i') ?></td>
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
                <td class="text-right">Rp. <?= numerical($payment['amount_request']) ?></td>
                <td class="text-right">Rp. <?= numerical($payment['amount']) ?></td>
            </tr>
            <?php $totalRequest += $payment['amount_request']; $total += $payment['amount'] ?>
        <?php endforeach ?>
        <?php if (empty($paymentNonBillings)): ?>
            <tr class="text-center">
                <td colspan="8">No payment occurred before</td>
            </tr>
        <?php else: ?>
            <tr>
                <td colspan="6"><strong>Total last transaction</strong></td>
                <td class="text-right"><strong>Rp. <?= numerical($totalRequest) ?></strong></td>
                <td class="text-right"><strong>Rp. <?= numerical($total) ?></strong></td>
            </tr>
        <?php endif; ?>
        </tbody>
    </table>
<?php endif; ?>

<?php if (empty($paymentBillings) && empty($paymentNonBillings)): ?>
    <p>No any category payment occurred before</p>
<?php endif; ?>
