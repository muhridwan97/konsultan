<?php $this->load->view('qr/index') ?>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Found Pallet Marking</h3>
    </div>
    <div class="box-body">
        <div style="display: flex; justify-content: space-between; align-items: center">
            <div style="display: flex; align-items: center">
                <img src="data:image/png;base64,<?= $qrCode ?>" alt="<?= $code ?>" class="mr10">
                <div>
                    <p class="lead mb0">
                        No Pallet: <strong><?= $code ?></strong>
                    </p>
                    <p class="text-muted mb0"><?= $goods['goods_name'] ?></p>
                    <p class="text-muted"><?= $goods['owner_name'] ?></p>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Pallet Detail</h3>
    </div>
    <div class="box-body">
        <div class="form-horizontal form-view mb0">
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Booking Type</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($goods['booking_type'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">No Reference</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($goods['no_reference'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Factory</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($goods['owner_name'], 'No factory') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Item No</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($goods['no_goods'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Goods Name</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($goods['goods_name'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Ex No Container</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($goods['ex_no_container'], '-') ?></p>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group">
                        <label class="col-sm-3">Booking No</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($goods['no_booking'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Whey Number</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($goods['whey_number'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Total Weight (Kg)</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= numerical($goods['total_weight'], 2) ?> Kg</p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Invoice No</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($goods['invoice_number'], '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Inbound Date</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty(format_date($goods['completed_at'], 'd F Y H:i'), '-') ?></p>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-3">Last Position</label>
                        <div class="col-sm-9">
                            <p class="form-control-static"><?= if_empty($goods['position'], 'No position') ?></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php if(!empty($workOrders)): ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Work Orders</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped no-datatable no-wrap responsive">
                    <thead>
                    <tr>
                        <th style="width: 25px">No</th>
                        <th>No Work Order</th>
                        <th>Handling Type</th>
                        <th>Status</th>
                        <th>Taken At</th>
                        <th>Completed At</th>
                        <th>Created At</th>
                        <th>Description</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($workOrders as $index => $workOrder): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td>
                                <a href="<?= site_url('work-order/view/' . $workOrder['id']) ?>">
                                    <?= $workOrder['no_work_order'] ?>
                                </a>
                            </td>
                            <td><?= $workOrder['handling_type'] ?></td>
                            <td><?= $workOrder['status'] ?></td>
                            <td><?= $workOrder['taken_at'] ?></td>
                            <td><?= $workOrder['completed_at'] ?></td>
                            <td><?= $workOrder['created_at'] ?></td>
                            <td><?= if_empty($workOrder['description'], '-') ?></td>
                        </tr>
                    <?php endforeach; ?>

                    <?php if (empty($workOrders)): ?>
                        <tr>
                            <td colspan="8" class="text-center">No data available</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php if(!empty($stockGoods)): ?>
    <div class="box box-primary">
        <div class="box-header">
            <h3 class="box-title">Goods Stocks</h3>
        </div>
        <div class="box-body">
            <div class="table-responsive">
                <table class="table table-bordered table-striped no-datatable no-wrap responsive">
                    <thead>
                    <tr>
                        <th>Goods Name</th>
                        <th>Unit</th>
                        <th>Qty</th>
                        <th>Weight</th>
                        <th>Gross Weight</th>
                        <th>Volume</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($stockGoods as $index => $item): ?>
                        <tr>
                            <td>
                                <a href="<?= site_url('report/stock-comparator?booking=' . $item['id_booking'] . '&containers[]=-1&items[]=' . $item['id_goods']) ?>">
                                    <?= $item['goods_name'] ?>
                                </a>
                            </td>
                            <td><?= $item['unit'] ?></td>
                            <td><?= numerical($item['stock_quantity']) ?></td>
                            <td><?= numerical($item['stock_weight']) ?></td>
                            <td><?= numerical($item['stock_gross_weight']) ?></td>
                            <td><?= numerical($item['stock_volume']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
<?php endif; ?>

<?php $this->load->view('booking_control/_data_comparator') ?>
