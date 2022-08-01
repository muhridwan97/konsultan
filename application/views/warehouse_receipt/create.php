<div class="box box-primary">
    <div class="box-header with-border">
        <h3 class="box-title">Generate Warehouse Receipt Batch</h3>
    </div>
    <!-- /.box-header -->
    <!-- form start -->
    <form action="<?= site_url('warehouse_receipt/save') ?>" role="form" method="post" id="form-warehouse-receipt">
        <input type="hidden" name="divider" id="divider" value="<?= $divider ?>">
        <div class="box-body">

            <?php $this->load->view('template/_alert') ?>

            <?php if($this->config->item('enable_branch_mode')): ?>
                <input type="hidden" name="branch" id="branch" value="<?= get_active_branch('id') ?>">
            <?php else: ?>
                <div class="form-group <?= form_error('branch') == '' ?: 'has-error'; ?>">
                    <label for="branch">Branch</label>
                    <select class="form-control select2" name="branch" id="branch" data-placeholder="Select branch" required>
                        <option value=""></option>
                        <?php foreach (get_customer_branch() as $branch): ?>
                            <option value="<?= $branch['id'] ?>" <?= set_select('branch', $branch['id'], $branch['id'] == get_active_branch('id')) ?>>
                                <?= $branch['branch'] ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                    <?= form_error('branch', '<span class="help-block">', '</span>'); ?>
                </div>
            <?php endif; ?>

            <?php if(UserModel::authenticatedUserData('user_type') == 'EXTERNAL'): ?>
                <input type="hidden" name="customer" id="customer" value="<?= UserModel::authenticatedUserData('id_person') ?>">
            <?php else: ?>
                <div class="form-group <?= form_error('customer') == '' ?: 'has-error'; ?>">
                    <label for="customer">Customer</label>
                    <select class="form-control select2 select2-ajax"
                            data-url="<?= site_url('people/ajax_get_people') ?>"
                            data-key-id="id" data-key-label="name"
                            name="customer" id="customer" data-placeholder="Select customer" required>
                        <option value=""></option>
                    </select>
                    <?= form_error('customer', '<span class="help-block">', '</span>'); ?>
                </div>
            <?php endif; ?>

            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('request_date') == '' ?: 'has-error'; ?>">
                        <label for="request_date">Request Date</label>
                        <input type="text" class="form-control datepicker" name="request_date" id="request_date" required
                               value="<?= set_value('request_date', readable_date('now', false)) ?>" placeholder="Request date">
                        <?= form_error('request_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('order_of') == '' ?: 'has-error'; ?>">
                        <label for="order_of">Order Of</label>
                        <input type="text" class="form-control" name="order_of" id="order_of" required
                               value="<?= set_value('order_of', 'STRAITS (SINGAPORE) PTE. LTD.') ?>" placeholder="Order of">
                        <?= form_error('order_of', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <div class="form-group <?= form_error('issuance_date') == '' ?: 'has-error'; ?>">
                        <label for="issuance_date">Issuance Date</label>
                        <input type="text" class="form-control datepicker" name="issuance_date" id="issuance_date"
                               value="<?= set_value('issuance_date') ?>" placeholder="Request date" required>
                        <?= form_error('issuance_date', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="form-group <?= form_error('duration') == '' ?: 'has-error'; ?>">
                        <label for="duration">Duration</label>
                        <select name="duration" id="duration" class="form-control select2" required>
                            <?php foreach (WarehouseReceiptModel::DURATIONS as $duration): ?>
                                <option value="<?= $duration ?>"><?= $duration ?></option>
                            <?php endforeach; ?>
                        </select>
                        <?= form_error('duration', '<span class="help-block">', '</span>'); ?>
                    </div>
                </div>
            </div>
            <div class="form-group <?= form_error('description') == '' ?: 'has-error'; ?>">
                <label for="description">Description</label>
                <textarea class="form-control" id="description" name="description" placeholder="Receipt description"
                          maxlength="1000"><?= set_value('description') ?></textarea>
                <?= form_error('description', '<span class="help-block">', '</span>'); ?>
            </div>

            <div class="box box-primary">
                <div class="box-header">
                    <h3 class="box-title">Customer stock</h3>
                </div>
                <div class="box-body" id="stock-data-wrapper">
                    <p class="text-muted">Container or goods of related customer</p>
                </div>
            </div>

            <div class="box box-primary" id="stock-loading-wrapper" style="display: none">
                <div class="box-header">
                    <h3 class="box-title">Stock Loading</h3>
                    <p class="text-muted mb0">Current customer stock</p>
                </div>
                <div class="box-body">

                    <div style="display: none" id="stock-container-wrapper">
                        <p class="lead mb0">Stock Containers</p>
                        <table class="table no-datatable mb20">
                            <thead>
                            <tr>
                                <th style="width: 25px">No</th>
                                <th>No Container</th>
                                <th>Type</th>
                                <th>Size</th>
                                <th>Seal</th>
                                <th>Position</th>
                                <th>Is Empty</th>
                                <th>Is Hold</th>
                                <th>Status</th>
                                <th>Danger</th>
                                <th style="width: 100px">Action</th>
                            </tr>
                            </thead>
                            <tbody id="destination-container-wrapper">
                            <tr id="placeholder">
                                <td colspan="11" class="text-center">No loading any container</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div style="display: none" id="stock-item-wrapper">
                        <p class="lead mb0">Stock Goods</p>
                        <table class="table no-datatable">
                            <thead>
                            <tr>
                                <th style="width: 25px">No</th>
                                <th>Goods</th>
                                <th>Quantity</th>
                                <th>Unit</th>
                                <th>Tonnage (Kg)</th>
                                <th>Tonnage Gross (Kg)</th>
                                <th>Volume (M<sup>3</sup>)</th>
                                <th>Position</th>
                                <th>No Pallet</th>
                                <th>Is Hold</th>
                                <th>Status</th>
                                <th>Danger</th>
                                <th style="width: 100px">Action</th>
                            </tr>
                            </thead>
                            <tbody id="destination-item-wrapper">
                            <tr id="placeholder">
                                <td colspan="13" class="text-center">No loading any item</td>
                            </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="row mt20">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="total_items">Total Item</label>
                                <input type="number" required readonly value="0" min="1" class="form-control" id="total_items" name="total_items">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="total_tonnages">Total Tonnage (Ton)</label>
                                <input type="number" required readonly value="0" class="form-control" id="total_tonnages" name="total_tonnages">
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label for="total_wr">Total Generated WR</label>
                                <input type="number" required readonly value="0" min="1" class="form-control" id="total_wr" name="total_wr">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="callout callout-warning">
                <h4><?= $divider ?> Ton Batch Generator / WR</h4>

                <p>
                    Every stock will be divided by each warehouse receipt value as <strong><?= $divider ?> Ton</strong>.
                    For optimal amount of charge you could select total amount by multiples of that value.<br>
                    Eg. Take item with total weight <?= $divider * 2 ?> Ton (as 2 WR), <?= $divider * 3 ?> Ton (as 3 WR), etc
                </p>
            </div>
        </div>
        <!-- /.box-body -->
        <div class="box-footer clearfix">
            <a href="<?= site_url('warehouse_receipt') ?>" class="btn btn-primary pull-left">Back to WR List</a>
            <button type="submit" data-toggle="one-touch" class="btn btn-primary pull-right">Generate Warehouse Receipt</button>
        </div>
    </form>
</div>
<!-- /.box -->

<script src="<?= base_url('assets/app/js/warehouse_receipt.js?v=2') ?>" defer></script>