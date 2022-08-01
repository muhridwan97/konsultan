<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">View Warehouse Receipt</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <div class="box-body">
        <form role="form" class="form-horizontal form-view">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">No Warehouse Receipt</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $warehouseReceipt['no_warehouse_receipt'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Customer</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($warehouseReceipt['customer_name'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Issuance Date</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= readable_date($warehouseReceipt['issuance_date'], false) ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Order of</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $warehouseReceipt['order_of'] ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Location</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($warehouseReceipt['location'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Duration</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= $warehouseReceipt['duration'] ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-4">Request Date</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($warehouseReceipt['request_date'], false) ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Status</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?php
                                $statuses = [
                                    'PENDING' => 'default',
                                    'APPROVED' => 'success',
                                    'REJECTED' => 'danger',
                                    'EXPIRED' => 'warning',
                                ]
                                ?>
                                <span class="label label-<?= $statuses[$warehouseReceipt['status']] ?>">
                                    <?= $warehouseReceipt['status'] ?>
                                </span>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Description</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= if_empty($warehouseReceipt['description'], '-') ?>
                            </p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Validated At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= readable_date($warehouseReceipt['validated_at']) ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Validated By</label>
                        <div class="col-sm-8">
                            <p class="form-control-static"><?= if_empty($warehouseReceipt['validator_name'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-4">Created At</label>
                        <div class="col-sm-8">
                            <p class="form-control-static">
                                <?= readable_date($warehouseReceipt['created_at']) ?>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </form>

        <div class="box box-primary">
            <div class="box-header">
                <h3 class="box-title">Warehouse Receipt Details</h3>
            </div>
            <div class="box-body">
                <table class="table table-bordered table-striped no-datatable">
                    <thead>
                    <tr>
                        <th style="width: 25px">No</th>
                        <th>Item</th>
                        <th>Quantity</th>
                        <th>Unit</th>
                        <th>Tonnage (Kg)</th>
                        <th>Tonnage Gross (Kg)</th>
                        <th>Volume (M<sup>3</sup>)</th>
                        <th>Position</th>
                        <th>Pallet</th>
                        <th>Inbound Date</th>
                        <th>Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1; $totalQty = 0; $totalTonnage = 0; ?>
                    <?php foreach ($warehouseReceiptDetails as $item): ?>
                        <tr>
                            <td><?= $no++ ?></td>
                            <td><?= $item['goods_name'] ?></td>
                            <td><?= numerical($item['quantity']) ?></td>
                            <td><?= $item['unit'] ?></td>
                            <td><?= numerical($item['tonnage']) ?></td>
                            <td><?= numerical($item['tonnage_gross']) ?></td>
                            <td><?= numerical($item['volume']) ?></td>
                            <td><?= if_empty($item['position'], '-') ?></td>
                            <td><?= if_empty($item['no_pallet'], '-') ?></td>
                            <td><?= readable_date($item['inbound_date'], false) ?></td>
                            <td><?= if_empty($item['description'], 'No description') ?></td>
                        </tr>
                    <?php $totalQty += $item['quantity']; $totalTonnage += $item['tonnage']  ?>
                    <?php endforeach; ?>

                    <?php if (empty($warehouseReceiptDetails)): ?>
                        <tr>
                            <td colspan="10" class="text-center">No data available</td>
                        </tr>
                    <?php else: ?>
                    <tr>
                        <td></td>
                        <th>Total</th>
                        <th><?= numerical($totalQty, 3) ?></th>
                        <td></td>
                        <th><?= numerical($totalTonnage, 3) ?></th>
                        <td colspan="5"></td>
                    </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
    <!-- /.box-body -->
    <div class="box-footer clearfix">
        <a href="javascript:void();" onclick="window.history.back()" class="btn btn-primary pull-left">
            Back
        </a>
        <a href="<?= site_url('warehouse_receipt/print_warehouse_receipt/' . $warehouseReceipt['id']) ?>" class="btn btn-primary pull-right">
            Print WR
        </a>
    </div>
</div>
<!-- /.box -->