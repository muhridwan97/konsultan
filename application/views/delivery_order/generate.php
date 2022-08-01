<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Generate New Delivery Order</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form action="<?= site_url('delivery_order/preview') ?>" role="form" method="post" id="form-delivery-order">
        <input type="hidden" name="method" id="method" value="GENERATE">

        <div class="box-body">

            <?php if ($this->session->flashdata('status') != NULL): ?>
                <div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <p><?= $this->session->flashdata('message'); ?></p>
                </div>
            <?php endif; ?>

            <div class="form-group <?= form_error('booking') == '' ?: 'has-error'; ?>">
                <label for="booking">Generate From Booking In</label>
                <select class="form-control select2" name="booking" id="booking" data-placeholder="Select booking data">
                    <option value=""></option>
                    <?php foreach ($bookings as $booking): ?>
                        <option value="<?= $booking['id'] ?>" <?= set_select('booking', $booking['id'], (isset($_GET['booking_id']) ? $_GET['booking_id'] : '') == $booking['id']) ?>>
                            <?= $booking['no_booking'] ?> <?= $booking['customer_name'] ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <span class="help-block">Approved booking only can be generated to Delivery Order System (DO)</span>
                <?= form_error('booking', '<span class="help-block">', '</span>'); ?>
                <?= form_error('unit_conversions[]', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Booking data</h3>
                </div>
                <div class="box-body" id="booking-data-wrapper">
                    <p class="text-muted">Container or goods of related booking</p>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer clearfix">
            <a href="<?= site_url('delivery_order') ?>" class="btn btn-primary pull-left">
                Back to Delivery Order List
            </a>
            <button class="btn btn-primary pull-right" id="btn-save-delivery-order">
                Preview Delivery Order
            </button>
        </div>
    </form>
</div>
<!-- /.box -->

<script src="<?= base_url('assets/app/js/delivery_order.js') ?>" defer></script>