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
        <h3 class="box-title">Delivery Order Detail</h3>
    </div>
    <div class="box-body">
        <section class="invoice">
            <div class="row">
                <div class="col-md-5 text-center" style="border-right: 1px solid #eee;">
                    <h3>Delivery Order Pass</h3>
                    <p class="text-muted" style="font-size: 16px; letter-spacing: 1px">www.transcon-indonesia.com</p>
                    <hr>
                    <img src="data:image/png;base64,<?= $qrCode ?>" alt="<?= $deliveryOrder['no_delivery_order'] ?>">
                    <p class="lead" style="margin-top: 10px">No DO: <?= $deliveryOrder['no_delivery_order'] ?></p>
                </div>
                <div class="col-md-7">
                    <form class="form-horizontal form-view row-data mb0"
                          data-id="<?= $deliveryOrder['id'] ?>">
                        <div class="form-group">
                            <label class="col-sm-4 control-label">No Booking</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $deliveryOrder['no_booking'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">No Reference</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $deliveryOrder['no_reference'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Booking Type</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $deliveryOrder['booking_type'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Owner</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $deliveryOrder['owner_name'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Supplier</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $deliveryOrder['supplier_name'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Customer</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $deliveryOrder['customer_name'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Status</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= $deliveryOrder['status'] ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Booking Date</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= readable_date($deliveryOrder['booking_date'], false) ?>
                                </p>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-sm-4 control-label">Description</label>
                            <div class="col-sm-8">
                                <p class="form-control-static">
                                    <?= if_empty($deliveryOrder['description'], 'No Description') ?>
                                </p>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </section>
    </div>
</div>

<?php $this->load->view('delivery_order/_view_detail') ?>
<?php $this->load->view('delivery_order/_view_safe_conduct', [
    'safeConducts' => $safeConducts,
    'gate' => true
]) ?>
<?php $this->load->view('workorder/_modal_confirm_print_job_sheet') ?>

<script src="<?= base_url('assets/app/js/work-order.js') ?>" defer></script>
