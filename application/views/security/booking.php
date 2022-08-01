<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Security Check Point</h3>
    </div>
    <div class="box-body">
        <?php $this->load->view('security/_scanner') ?>
    </div>
</div>

<div class="box box-primary security-wrapper">
    <div class="box-header with-border">
        <h3 class="box-title">Booking Detail</h3>
    </div>
    <div class="box-body">
        <?php if ($this->session->flashdata('status') != NULL): ?>
            <div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <p><?= $this->session->flashdata('message'); ?></p>
            </div>
        <?php endif; ?>

        <section class="invoice">
            <div class="row">
                <div class="col-md-5 text-center" style="border-right: 1px solid #eee;">
                    <h3>Booking Pass</h3>
                    <p class="text-muted" style="font-size: 16px; letter-spacing: 1px">www.transcon-indonesia.com</p>
                    <hr>
                    <img src="data:image/png;base64,<?= $qrCode ?>" alt="<?= $booking['no_booking'] ?>">
                    <p class="lead" style="margin-top: 10px">No Booking: <?= $booking['no_booking'] ?></p>
                </div>
                <div class="col-md-7">
                    <form class="form-horizontal form-view row-data"
                          data-id="<?= $booking['id'] ?>">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Booking Category</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $booking['category'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">No Booking</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $booking['no_booking'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">No Reference</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $booking['no_reference'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Booking Type</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $booking['booking_type'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Supplier</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $booking['supplier_name'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Customer</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $booking['customer_name'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Status</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $booking['status'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Booking Date</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= readable_date($booking['booking_date'], false) ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Description</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= if_empty($booking['description'], 'No Description') ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Created At</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= readable_date($booking['created_at']) ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Updated At</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= readable_date($booking['updated_at']) ?>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<?php $this->load->view('booking/_view_detail') ?>