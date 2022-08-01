<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Create New Delivery Order</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form action="<?= site_url('delivery_order/save_generate') ?>" role="form" method="post">
        <input type="hidden" name="method" id="method" value="<?= $method ?>">
        <input type="hidden" name="booking" id="booking" value="<?= $booking['id'] ?>">

        <div class="box-body">

            <?php if ($this->session->flashdata('status') != NULL): ?>
                <div class="alert alert-<?= $this->session->flashdata('status') ?>" role="alert">
                    <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                    <p><?= $this->session->flashdata('message'); ?></p>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="booking">Booking</label>
                        <p class="form-control-static"><?= $booking['no_booking'] ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="booking">Customer</label>
                        <p class="form-control-static"><?= $booking['customer_name'] ?></p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="booking">Supplier</label>
                        <p class="form-control-static"><?= $booking['supplier_name'] ?></p>
                    </div>
                </div>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Delivery Order Generate Preview</h3>
                </div>
                <div class="box-body">
                    <table class="table table-bordered table-striped no-datatable">
                        <thead>
                        <tr>
                            <th>No</th>
                            <th>Goods</th>
                            <th>Quantity</th>
                            <th>Unit</th>
                            <th>Tonnage (Kg)</th>
                            <th>Volume (M<sup>3</sup>)</th>
                        </tr>
                        </thead>
                        <tbody>
                        <?php $no = 1 ?>
                        <?php foreach ($bookingGoods as $bookingItem): ?>
                        <tr class="<?= empty($bookingItem['delivery_orders']) ? 'danger' : '' ?>">
                            <td>
                                <input type="hidden" name="booking_goods[]" value="<?= $bookingItem['id'] ?>">
                                <input type="hidden" name="unit_conversions[]" value="<?= $bookingItem['unit_to'] ?>">
                                <?= $no++ ?>
                            </td>
                            <td><?= $bookingItem['goods_name'] ?></td>
                            <td><?= numerical($bookingItem['quantity']) ?></td>
                            <td><?= $bookingItem['unit'] ?></td>
                            <td><?= numerical($bookingItem['tonnage']) ?></td>
                            <td><?= numerical($bookingItem['volume']) ?></td>
                        </tr>
                            <?php if(empty($bookingItem['delivery_orders'])): ?>
                                <tr>
                                    <td></td>
                                    <td colspan="5" class="text-danger">SKIP CONVERT TO DO</td>
                                </tr>
                            <?php else: ?>
                                <tr>
                                    <td></td>
                                    <td colspan="5">
                                        <p><strong class="mb10 text-success">Delivery Order (DO):</strong></p>
                                        <table class="table no-datatable">
                                            <tr>
                                                <th>No</th>
                                                <th>No Delivery Order</th>
                                                <th>Goods</th>
                                                <th>Quantity</th>
                                                <th>Unit</th>
                                                <th>Tonnage (Kg)</th>
                                                <th>Volume (M<sup>3</sup>)</th>
                                            </tr>
                                            <tbody>
                                            <?php $noDo = 1 ?>
                                            <?php foreach ($bookingItem['delivery_orders'] as $deliveryOrder): ?>
                                                <tr>
                                                    <td><?= $noDo++ ?></td>
                                                    <td><?= $deliveryOrder['no_delivery_order'] ?></td>
                                                    <td><?= $deliveryOrder['goods_name'] ?></td>
                                                    <td><?= numerical($deliveryOrder['quantity']) ?></td>
                                                    <td><?= $deliveryOrder['unit'] ?></td>
                                                    <td><?= numerical($deliveryOrder['tonnage']) ?></td>
                                                    <td><?= numerical($deliveryOrder['volume']) ?></td>
                                                </tr>
                                            <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </td>
                                </tr>
                            <?php endif; ?>
                        <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer clearfix">
            <a href="<?= site_url('delivery_order') ?>" class="btn btn-primary pull-left">
                Back to Delivery Order List
            </a>
            <button class="btn btn-primary pull-right" id="btn-save-delivery-order">
                Generate Delivery Order
            </button>
        </div>
    </form>
</div>
<!-- /.box -->