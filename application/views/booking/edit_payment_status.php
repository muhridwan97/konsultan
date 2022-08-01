<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Edit Payment Status</h3>
    </div>

    <form action="<?= site_url('booking/update_payment_status/' . $booking['id']) ?>" role="form" method="post">
        <?= _csrf() ?>
        <?= _method('put') ?>

        <div class="box-body">
            <?php $this->load->view('template/_alert') ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">No Booking</label>
                        <p class="form-control-static">
                            <?= $booking['no_booking'] ?> (<?= $booking['no_reference'] ?>)
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Booking Date</label>
                        <p class="form-control-static">
                            <?= format_date($booking['booking_date'], 'd F Y H:i') ?>
                        </p>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Category</label>
                        <p class="form-control-static">
                            <?= $booking['booking_type'] ?> (<?= $booking['category'] ?>)
                        </p>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="control-label">Customer</label>
                        <p class="form-control-static">
                            <?= $booking['customer_name'] ?>
                        </p>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Payment Status</h3>
                </div>
                <div class="box-body">
                    <div class="form-group <?= form_error('payment_status') == '' ?: 'has-error'; ?>">
                        <label for="payment_status">Payment Status</label>
                        <select class="form-control select2" name="payment_status" id="payment_status"
                                data-placeholder="Booking payment status" required>
                            <option value=""></option>
                            <?php foreach (BookingModel::PAYMENT_STATUSES as $paymentStatus): ?>
                                <option value="<?= $paymentStatus ?>"
                                    <?= set_select('payment_status', $paymentStatus, $booking['payment_status'] == $paymentStatus) ?>>
                                    <?= $paymentStatus ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('payment_status', '<span class="help-block">', '</span>'); ?>
                    </div>
                    <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                        <label for="description">Status Description</label>
                        <textarea class="form-control" id="description" name="description"
                                  placeholder="Change status description"
                                  maxlength="500"></textarea>
                        <?= form_error('description', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>

        </div>
        <div class="box-footer clearfix">
            <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
                Back
            </a>
            <button type="submit" class="btn btn-warning pull-right" id="btn-save-booking">
                Update Payment Status
            </button>
        </div>
    </form>
</div>