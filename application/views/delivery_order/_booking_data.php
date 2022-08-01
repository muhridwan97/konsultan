<div class="form-horizontal form-view">
    <div class="row">
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-sm-3">Customer</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $booking['customer_name'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">No Booking</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $booking['no_booking'] ?></p>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="form-group">
                <label class="col-sm-3">Booking Type</label>
                <div class="col-sm-9">
                    <p class="form-control-static"><?= $booking['booking_type'] ?></p>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-3">Booking Date</label>
                <div class="col-sm-9">
                    <p class="form-control-static">
                        <?= (new DateTime($booking['booking_date']))->format('d F Y') ?>
                    </p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(!empty($deliveryOrders) && $method == 'GENERATE'): ?>
    <div class="box box-danger">
        <div class="box-header">
            <h3 class="box-title text-danger">Current Delivery Order</h3>
            <p>Latest delivery order that generated.</p>
        </div>
        <div class="box-body">
            <div class="alert alert-warning" role="alert">
                <button type="button" class="close" data-dismiss="alert" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
                <p>This booking has available delivery order! This action will replace current delivery order.</p>
            </div>
            <table class="table table-bordered table-striped no-datatable">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>No Delivery Order</th>
                    <th>Status</th>
                    <th>Description</th>
                    <th>Created At</th>
                </tr>
                </thead>
                <tbody>
                <?php $no = 1; ?>
                <?php foreach ($deliveryOrders as $deliveryOrder) : ?>
                    <tr>
                        <td><?= $no++ ?></td>
                        <td>
                            <a href="<?= site_url('delivery_order/view/' . $deliveryOrder['id']) ?>" target="_blank">
                                <?= $deliveryOrder['no_delivery_order'] ?>
                            </a>
                        </td>
                        <td><?= $deliveryOrder['status'] ?></td>
                        <td><?= if_empty($deliveryOrder['description'], 'No description') ?></td>
                        <td><?= readable_date($deliveryOrder['created_at']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif; ?>

<?php if (!empty($bookingGoods)): ?>
    <div class="box">
        <div class="box-header">
            <h3 class="box-title">Goods <?= !empty($deliveryOrders) && $method == 'GENERATE' ? '(Retake to replace DO)' : '' ?></h3>
            <p>Delivery order is a <strong>package system</strong> from your booking with specific number ID.</p>
        </div>
        <div class="box-body">
            <table class="table table-bordered no-datatable">
                <thead>
                <tr>
                    <th style="width: 25px">No</th>
                    <th>Goods</th>
                    <th>Quantity</th>
                    <th>Unit</th>
                    <th>Tonnage (Kg)</th>
                    <th>Volume (M<sup>3</sup>)</th>
                    <th>Description</th>
                    <th style="width: 140px">Unit DO Package</th>
                    <?php if($method == 'CREATE'): ?>
                        <th>Action</th>
                    <?php endif; ?>
                </tr>
                </thead>
                <tbody id="source-item-wrapper">
                <tr id="placeholder" style="display: none">
                    <td colspan="11" class="text-center">
                        All items are taken
                    </td>
                </tr>
                <?php $no = 1; ?>
                <?php foreach ($bookingGoods as $item): ?>
                    <tr data-row="<?= $item['id'] ?>">
                        <td><?= $no++ ?></td>
                        <td><?= $item['goods_name'] ?></td>
                        <td>
                            <input type="hidden" step="any" min="0" max="<?= $item['quantity'] ?>" class="form-control" id="quantity" name="quantities[]" value="<?= $item['quantity'] ?>" style="width: 100px" disabled>
                            <span id="quantity-label"><?= numerical($item['quantity']) ?></span>
                        </td>
                        <td><?= $item['unit'] ?></td>
                        <td>
                            <input type="hidden" step="any" min="0" max="<?= $item['tonnage'] ?>" class="form-control" id="tonnage" name="tonnages[]" value="<?= $item['tonnage'] ?>" style="width: 100px" disabled>
                            <span id="tonnage-label"><?= numerical($item['tonnage']) ?></span>
                        </td>
                        <td>
                            <input type="hidden" step="any" min="0" max="<?= $item['volume'] ?>" class="form-control" id="volume" name="volumes[]" value="<?= $item['volume'] ?>" style="width: 100px" disabled>
                            <span id="volume-label"><?= numerical($item['volume']) ?></span>
                        </td>
                        <td><?= if_empty($item['description'], 'No description') ?></td>
                        <?php if($method == 'GENERATE'): ?>
                        <td>
                            <div class="form-group mb0">
                                <input type="hidden" name="booking_goods[]" value="<?= $item['id'] ?>">
                                <select class="form-control select2" name="unit_conversions[]" id="unit_conversions">
                                    <option value="">(EXCLUDE)</option>
                                    <option value="PACKAGE">PACKAGE 1 DO</option>
                                    <?php foreach ($item['unit_conversions'] as $convertibleUnit) : ?>
                                        <option value="<?= $convertibleUnit['id_unit_to'] ?>">
                                            <?= numerical($convertibleUnit['value'] * $item['quantity']) ?> DO - <?= $convertibleUnit['unit_to'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <?php if(empty($item['unit_conversions'])): ?>
                                        <option value="<?= $item['id_unit'] ?>">
                                            <?= numerical($item['quantity']) ?> DO - <?= $item['unit'] ?>
                                        </option>
                                    <?php endif; ?>
                                </select>
                            </div>
                        </td>
                        <?php else: ?>
                            <td class="row-conversion">
                                <select class="form-control select2" name="unit_conversions[]" id="unit_conversions">
                                    <?php foreach ($item['unit_conversions'] as $convertibleUnit) : ?>
                                        <option value="<?= $convertibleUnit['id_unit_to'] ?>">
                                            <?= $convertibleUnit['unit_to'] ?>
                                        </option>
                                    <?php endforeach; ?>
                                    <?php if(empty($item['unit_conversions'])): ?>
                                        <option value="<?= $item['id_unit'] ?>" selected>
                                            <?= $item['unit'] ?>
                                        </option>
                                    <?php endif; ?>
                                </select>
                            </td>
                            <td>
                                <input type="hidden" name="booking_goods[]" value="<?= $item['id'] ?>" disabled>
                                <button type="button" class="btn btn-primary btn-block btn-take"
                                        data-type="item"
                                        data-id="<?= $item['id'] ?>">
                                    Take
                                </button>
                            </td>
                        <?php endif; ?>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
            <p class="text-success mt10">You can specific convert the goods to another system package if you like or necessary.</p>
        </div>
    </div>
<?php endif; ?>