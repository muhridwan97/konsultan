<div class="row">
    <div class="col-sm-7 col-xs-6">
        <p class="lead" style="margin-bottom: 10px;">
            <strong>BOOKING DOCUMENT</strong>
        </p>
        <p class="lead" style="margin-bottom: 0">
            No Booking: <?= $booking['no_booking'] ?>
        </p>
        <p>No Document Upload: <?= $booking['no_upload'] ?></p>
        <p class="mb0"><strong>BRANCH <?= $booking['branch'] ?></strong></p>
    </div>
    <div class="col-sm-5 col-xs-6">
        <div class="pull-right">
            <div class="text-center" style="display: inline-block">
                <img src="data:image/png;base64,<?= $bookingBarcode ?>" alt="<?= $booking['no_booking'] ?>">
                <p>NO BOOKING</p>
            </div>
        </div>
    </div>
</div>

<hr>

<div class="row" style="margin-top: 20px">
    <div class="col-xs-6 col-sm-3">
        Booking Type :
        <p><strong><?= $booking['booking_type'] ?> (<?= $booking['category'] ?>)</strong></p>
    </div>
    <div class="col-xs-6 col-sm-3">
        Booking Date :
        <p><strong><?= readable_date($booking['booking_date']) ?></strong></p>
    </div>
    <div class="col-xs-6 col-sm-3">
        Customer :
        <p><strong><?= $booking['customer_name'] ?></strong></p>
    </div>

    <div class="col-xs-6 col-sm-3">
        Supplier :
        <p><strong><?= $booking['supplier_name'] ?></strong></p>
    </div>
</div>

<hr>

<?php $this->load->view('booking/_view_extension') ?>
<?php $this->load->view('booking/_view_detail') ?>

<div class="row" style="margin-top: 20px">
    <hr>
    <div class="col-xs-12">
        Booking Description :
        <p><strong><?= if_empty($booking['description'], '-') ?></strong></p>
    </div>
</div>