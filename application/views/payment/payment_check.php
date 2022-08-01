<div class="row">
    <div class="col-md-6 col-md-push-3">
        <h4>Payment Approval Request</h4>

        <?php $this->load->view('template/_alert') ?>

        <table class="table mb10 no-datatable">
            <tbody>
            <tr>
                <th>No Payment</th>
                <td><?= $payment['no_payment'] ?></td>
            </tr>
            <tr>
                <th>No Reference</th>
                <td><?= if_empty($payment['no_reference'], $payment['upload_description']) ?></td>
            </tr>
            <tr>
                <th>Customer Name</th>
                <td><?= $payment['customer_name'] ?></td>
            </tr>
            <tr>
                <th>Type</th>
                <td><?= $payment['payment_type'] ?></td>
            </tr>
            <tr>
                <th>Payment Date</th>
                <td><?= format_date(if_empty($payment['payment_date'], $payment['created_at']), 'd F Y H:i') ?></td>
            </tr>
            <tr>
                <th>Applicant Name</th>
                <td><?= $payment['applicant_name'] ?></td>
            </tr>
            <tr>
                <th>Description</th>
                <td><?= if_empty($payment['invoice_description'], $payment['description']) ?></td>
            </tr>
            <tr>
                <th>Request Bank</th>
                <td><?= if_empty($payment['bank'], '-') ?> (<?= if_empty($payment['account_number'], '-') ?>)</td>
            </tr>
            <tr>
                <th>Status</th>
                <td>
                    <?php
                    $statuses = [
                        PaymentModel::STATUS_PENDING => 'default',
                        PaymentModel::STATUS_ASK_APPROVAL => 'warning',
                        PaymentModel::STATUS_REJECTED => 'danger',
                        PaymentModel::STATUS_APPROVED => 'success',
                    ];
                    ?>
                    <span class="label label-<?= get_if_exist($statuses, if_empty($payment['status_check'], 'PENDING'), 'default') ?>">
                <?= if_empty($payment['status_check'], 'PENDING') ?>
            </span>
                </td>
            </tr>
            <tr>
                <th class="text-nowrap">Total Amount</th>
                <th>Rp. <?= numerical(if_empty($payment['amount'], $payment['amount_request']), 2, true) ?></th>
            </tr>
            </tbody>
        </table>

        <hr>

        <?php if(in_array($payment['status_check'], [PaymentModel::STATUS_ASK_APPROVAL, PaymentModel::STATUS_PENDING])): ?>
            <form action="<?= site_url('payment-check/payment-validate/' . $payment['id'] . '/' . $token) ?>" method="post" id="form-payment-check" class="mb20">
                <?= _csrf() ?>
                <?= _method('put') ?>
                <input type="hidden" name="status" id="status" value="<?= $payment['status_check'] ?>">
                <input type="hidden" name="email" value="<?= get_url_param('email') ?>">
                <div class="form-group">
                    <label for="description">Approval Note</label>
                    <textarea class="form-control" id="description" name="description" maxlength="500" rows="2"
                              placeholder="Validation note about the payment"><?= set_value('description') ?></textarea>
                    <?= form_error('description') ?>
                </div>
                <div class="d-flex">
                    <button type="submit" class="btn btn-danger d-flex flex-grow-1 justify-content-center mr-2"
                            data-value="<?= PaymentModel::STATUS_REJECTED ?>">REJECT
                    </button>
                    <button type="submit" class="btn btn-success d-flex flex-grow-1 justify-content-center ml-2"
                            data-value="<?= PaymentModel::STATUS_APPROVED ?>">APPROVE
                    </button>
                </div>
            </form>
        <?php else: ?>
            <p class="lead text-center">
                Payment already validated as <strong><?= $payment['status_check'] ?></strong><br>
                <?php if(get_url_param('autoclose', false)): ?>
                    <small>This window will close automatically within <span id="close-counter">10</span> second(s)</small>
                <?php endif; ?>
            </p>
        <?php endif; ?>

    </div>
</div>

<script defer>
    var formPayment = $('#form-payment-check');
    var submitButtons = formPayment.find('button[type=submit]');

    /**
     * url: /payment-check/validate-payment/{:id}/{:token}?email={:encoded_email_base64}
     * Add status value by clicked button before submit.
     */
    submitButtons.on('click', function (e) {
        e.preventDefault();

        formPayment.find('#status').val($(this).data('value'));
        submitButtons.attr('disabled', true);

        formPayment.submit();
    });

    var closeCounter = $('#close-counter');
    if (closeCounter.length) {
        setInterval(function () {
            var i = parseInt(closeCounter.text() || 0) - 1;
            if(i >= 0) {
                closeCounter.text(i);
                if (i <= 0) {
                    window.close();
                }
            }
        }, 1000);
    }
</script>
