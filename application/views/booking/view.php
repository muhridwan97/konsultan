<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Booking</h3>
        <?php if (AuthorizationModel::isAuthorized(PERMISSION_BOOKING_IN_EDIT) || AuthorizationModel::isAuthorized(PERMISSION_BOOKING_OUT_EDIT)): ?>
            <?php if($booking['status'] == BookingModel::STATUS_BOOKED || $booking['status'] == BookingModel::STATUS_REJECTED): ?>
                <a href="<?= site_url('booking/edit/' . $booking['id']) ?>" class="btn btn-primary pull-right">
                    Edit Booking
                </a>
            <?php endif; ?>
        <?php endif; ?>
    </div>

    <div class="box-body">
        <?php $this->load->view('booking/_view_header') ?>

        <?php $this->load->view('booking/_view_rating') ?>

        <?php $this->load->view('booking/_view_extension') ?>

        <?php $this->load->view('booking/_view_weight') ?>

        <?php $this->load->view('booking/_view_detail') ?>
    </div>

    <div class="box-footer clearfix">
        <a href="javascript:void(0)" onclick="history.back()" class="btn btn-primary pull-left">
            Back
        </a>
        <div class="pull-right">
            <?php if($booking['status'] == BookingModel::STATUS_APPROVED): ?>
                <a href="<?= site_url('safe-conduct/create?category='.$booking['category'].'&booking_id=' . $booking['id']) ?>" class="btn btn-success">
                    Create Safe Conduct
                </a>
            <?php endif; ?>
        </div>
    </div>
</div>