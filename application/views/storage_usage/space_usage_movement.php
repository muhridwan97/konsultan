<div class="row">
    <div class="col-md-12">
        <div class="box">
            <div class="box-header with-border">
                <h3 class="box-title">Dedicated Space Monitoring</h3>
                <div class="pull-right">
                    <a href="#form-filter" class="btn btn-info btn-filter-toggle">
                        <?= get_url_param('filter', 0) ? 'Hide' : 'Show' ?> Filter
                    </a>
                    <a href="<?= base_url(uri_string()) ?>?<?= $_SERVER['QUERY_STRING'] ?>&export=1" class="btn btn-success">
                        Export Excel
                    </a>
                </div>
            </div>
            <div class="box-body">
                <form role="form" method="get" class="form-filter" id="form-filter" <?= isset($_GET['filter']) ? '' : 'style="display:none"'  ?>>
                    <input type="hidden" name="filter" value="1">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Data Filters
                        </div>
                        <div class="panel-body">
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="customer">Customer</label>
                                        <select class="form-control select2 select2-ajax"
                                                data-url="<?= site_url('people/ajax_get_people') ?>"
                                                data-key-id="id" data-key-label="name" data-key-sublabel="outbound_type"
                                                data-params="type=<?= PeopleModel::$TYPE_CUSTOMER ?>"
                                                name="customer" id="customer"
                                                data-placeholder="Select customer" required style="width: 100%">
                                            <option value=""></option>
                                            <?php if (!empty($selectedCustomer)): ?>
                                                <option value="<?= $selectedCustomer['id'] ?>" selected>
                                                    <?= $selectedCustomer['name'] ?>
                                                </option>
                                            <?php endif; ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="warehouse_type">Warehouse Type</label>
                                        <select class="form-control select2" name="warehouse_type" id="warehouse_type"
                                                data-placeholder="Select type" required>
                                            <option value=""></option>
                                            <?php $warehouseTypes = ['WAREHOUSE', 'YARD', 'COVERED YARD'] ?>
                                            <?php foreach($warehouseTypes as $warehouseType): ?>
                                                <option value="<?= $warehouseType ?>" <?= set_select('warehouse_type', $warehouseType, $warehouseType == get_url_param('warehouse_type')) ?>>
                                                    <?= $warehouseType ?>
                                                </option>
                                            <?php endforeach; ?>
                                        </select>
                                    </div>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_from">Date From</label>
                                        <input type="text" class="form-control datepicker" id="date_from" name="date_from"
                                               placeholder="Stock date from" required autocomplete="off"
                                               maxlength="50" value="<?= set_value('date_from', get_url_param('date_from')) ?>">
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="date_to">Date To</label>
                                        <input type="text" class="form-control datepicker" id="date_to" name="date_to"
                                               placeholder="Transaction until" required autocomplete="off"
                                               maxlength="50" value="<?= set_value('date_to', get_url_param('date_to')) ?>">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="panel-footer text-right">
                            <a href="<?= site_url(uri_string(), false) ?>" class="btn btn-default btn-reset-filter">Reset Filter</a>
                            <button type="submit" class="btn btn-primary">Apply Filter</button>
                        </div>
                    </div>
                </form>

                <div class="alert alert-warning">
                    <h4>Storage Calculation</h4>
                    <ul class="list-inline">
                        <li><strong>Containerized Goods:</strong></li>
                        <li>20 Feet = 17 M<sup>2</sup></li>
                        <li>40 Feet STD = 32 M<sup>2</sup></li>
                        <li>40 Feet HC = 34 M<sup>2</sup></li>
                    </ul>
                    <ul class="list-inline">
                        <li><strong>LCL Goods:</strong></li>
                        <li>Inbound job storage usage / quantity</li>
                    </ul>
                </div>

                <table class="table table-bordered table-striped responsive no-datatable" id="table-over-space">
                    <thead>
                    <tr>
                        <th rowspan="2" style="width: 30px">No</th>
                        <th rowspan="2">No Reference</th>
                        <th rowspan="2">Customer Name</th>
                        <th rowspan="2">Date Activity</th>
                        <th rowspan="2">Type</th>
                        <th rowspan="2">Qty</th>
                        <th rowspan="2" style="width: 140px">Unit</th>
                        <th rowspan="2">Container</th>
                        <th colspan="3" class="text-center">Left Storage (M<sup>2</sup>)</th>
                        <th rowspan="2" style="width: 60px">Used (M<sup>2</sup>)</th>
                    </tr>
                    <tr>
                        <th style="width: 60px">Inbound</th>
                        <th style="width: 60px">Outbound</th>
                        <th style="width: 60px">Balance</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php $no = 1 ?>
                    <?php foreach ($goodsStorages as $index => $itemStorage): ?>
                        <?php if($itemStorage['row_type'] == 'beginning-balance'): ?>
                            <tr class="success">
                                <td></td>
                                <td colspan="7">
                                    Beginning balance stock at <strong><?= $itemStorage['stock_date'] ?></strong>
                                    (Capacity <?= $itemStorage['capacity'] ?> M<sup>2</sup>)
                                </td>
                                <td><?= numerical($itemStorage['inbound_storage'], 2, true) ?></td>
                                <td><?= numerical($itemStorage['outbound_storage'], 2, true) ?></td>
                                <td><?= numerical($itemStorage['left_storage'], 2, true) ?></td>
                                <td><?= numerical($itemStorage['used_storage'], 2, true) ?></td>
                            </tr>
                        <?php elseif($itemStorage['row_type'] == 'transaction'): ?>
                            <tr>
                                <td><?= $no++ ?></td>
                                <td title="<?= $itemStorage['no_reference'] ?>">
                                    <?= mid_ellipsis($itemStorage['no_reference'], 6, 6) ?>
                                </td>
                                <td><?= $itemStorage['customer_name'] ?></td>
                                <td><?= $itemStorage['date_activity'] ?></td>
                                <td>
                                    <?= $itemStorage['activity_type'] ?><br>
                                    <a href="<?= site_url('work-order/view/' . $itemStorage['id_work_order']) ?>">
                                        <?= $itemStorage['handling_type'] ?>
                                    </a><br>
                                    <small class="text-muted"><?= $itemStorage['no_work_order'] ?></small>
                                </td>
                                <td><?= numerical($itemStorage['quantity'], 2, true) ?></td>
                                <td>
                                    <?= $itemStorage['unit'] ?><br>
                                    <small class="text-muted"><?= $itemStorage['goods_name'] ?></small>
                                </td>
                                <td>
                                    <?= if_empty($itemStorage['no_container'], 'LCL') ?><br>
                                    <small class="text-muted">
                                        Total Item <?= numerical($itemStorage['total_goods_loaded_quantity'], 2, true) ?>
                                    </small>
                                </td>
                                <td><?= numerical($itemStorage['inbound_storage'], 2, true) ?></td>
                                <td><?= numerical($itemStorage['outbound_storage'], 2, true) ?></td>
                                <td><?= numerical($itemStorage['left_storage'], 2, true) ?></td>
                                <td><strong><?= numerical($itemStorage['used_storage'], 2, true) ?></strong></td>
                            </tr>
                        <?php elseif($itemStorage['row_type'] == 'change-capacity'): ?>
                            <tr class="danger">
                                <td></td>
                                <td colspan="7">
                                    New capacity effective at <strong><?= $itemStorage['effective_date_capacity'] ?></strong>
                                    Capacity: <?= $itemStorage['capacity'] ?> M<sup>2</sup>
                                </td>
                                <td><?= numerical($itemStorage['inbound_storage'], 2, true) ?></td>
                                <td><?= numerical($itemStorage['outbound_storage'], 2, true) ?></td>
                                <td><?= numerical($itemStorage['left_storage'], 2, true) ?></td>
                                <td><?= numerical($itemStorage['used_storage'], 2, true) ?></td>
                            </tr>
                        <?php endif; ?>
                    <?php endforeach; ?>
                    <?php if(empty($goodsStorages)): ?>
                        <tr>
                            <td colspan="12">No dedicated storage</td>
                        </tr>
                    <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
