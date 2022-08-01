<form class="form-horizontal form-view" role="form">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-sm-4">No Delivery Order</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= $deliveryOrder['no_delivery_order'] ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">DO Owner</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= $deliveryOrder['owner_name'] ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Supplier</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= $deliveryOrder['supplier_name'] ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Customer</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= $deliveryOrder['customer_name'] ?>
                    </p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-sm-4">No Booking</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <a href="<?= site_url('booking/view/' . $deliveryOrder['id_booking']) ?>">
                            <?= $deliveryOrder['no_booking'] ?>
                        </a>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">No Reference</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= if_empty($deliveryOrder['no_reference'], '-') ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Created At</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= readable_date($deliveryOrder['created_at']) ?>
                    </p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-4">Created At</label>
                <div class="col-sm-8">
                    <p class="form-control-static">
                        <?= readable_date($deliveryOrder['updated_at']) ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</form>