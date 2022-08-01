<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Gate Check Point</h3>
    </div>
    <div class="box-body">
        <?php $this->load->view('gate/_scanner') ?>
    </div>
</div>

<div class="box box-primary security-wrapper">
    <div class="box-header with-border">
        <h3 class="box-title">Booking Detail</h3>
    </div>
    <div class="box-body">
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
                    <form class="form-horizontal form-view row-data mb0"
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
                                    <?= if_empty($booking['supplier_name'], '-') ?>
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
                                    <?= (new DateTime($booking['booking_date']))->format('d F Y') ?>
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
                        <div class="form-group hidden">
                            <label class="col-sm-4 control-label">Create Job</label>
                            <div class="col-sm-8">
                                <?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_CREATE)): ?>
                                    <a href="<?= site_url('work-order/create/' . $booking['category']) ?>"
                                       class="btn btn-primary"
                                       data-id-booking="<?= $booking['id'] ?>"
                                       data-id-handling=""
                                       data-id-safe-conduct=""
                                       data-type="<?= $booking['category'] ?>"
                                       id="btn-create-job">
                                        Create job
                                    </a>
                                <?php endif; ?>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<?php if (AuthorizationModel::isAuthorized(PERMISSION_WORKORDER_CREATE) && $booking['category'] == 'OUTBOUND'): ?>
    <?php $this->load->view('gate/_booking_data') ?>
<?php endif; ?>

<?php $this->load->view('gate/_job_data') ?>
<?php $this->load->view('gate/_modal_check_in', ['category' => $booking['category']]) ?>
<?php $this->load->view('gate/_modal_check_out', ['category' => $booking['category']]) ?>
<?php $this->load->view('workorder/_modal_confirm_print_job_sheet') ?>

<script src="<?= base_url('assets/app/js/work-order.js') ?>" defer></script>
